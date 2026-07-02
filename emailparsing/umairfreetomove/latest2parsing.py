import imaplib
import ssl
import email
import csv
import os
import re
from datetime import datetime

# Define folder paths and email credentials
FOLDER_PATH = r'/var/www/html/email_files/umairtest/'
IMAGES_PATH = os.path.join(FOLDER_PATH, 'images')
email_user = 'umair@ukmails.co.uk'
email_pass = 'Hakuyasha123@'

# Setup SSL context and connect to the IMAP server
context = ssl.SSLContext(ssl.PROTOCOL_SSLv23)
mail = imaplib.IMAP4_SSL("imap.hostinger.com")

try:
    mail.login(email_user, email_pass)
except imaplib.IMAP4.error as e:
    print(f"Login failed: {e}")
    exit()

mail.select('Inbox')

# Search for all emails
typ, data = mail.search(None, 'ALL')
if typ != 'OK':
    print(f"Search failed: {typ}")
    exit()

mail_ids = data[0]
id_list = mail_ids.split()

if not id_list:
    print("No emails found.")
    exit()

os.makedirs(FOLDER_PATH, exist_ok=True)
os.makedirs(IMAGES_PATH, exist_ok=True)

def safe_decode(payload, encoding='utf-8'):
    try:
        return payload.decode(encoding)
    except UnicodeDecodeError:
        return payload.decode('ISO-8859-1')

def extract_details(email_body):
    details = {}
    
    # Extract booking number
    booking_number_match = re.search(r'Booking number: (\S+)', email_body)
    if booking_number_match:
        details['Booking number'] = booking_number_match.group(1)
    
    # Extract name
    name_match = re.search(r'\* Name (.+)', email_body)
    if name_match:
        details['Name'] = name_match.group(1)
    else:
        # If no match, try matching with "* Name:"
        name_match = re.search(r'\* Name: (.+)', email_body)
        if name_match:
            details['Name'] = name_match.group(1)

    # Extract phone number
    phone_number_match = re.search(r'\* Phone number : (\+[\d]+)', email_body)
    if phone_number_match:
        details['Phone number'] = phone_number_match.group(1)
    else:
        # If no match, try matching with "Phone number:"
        phone_number_match = re.search(r'Phone number: (\+[\d]+)', email_body)
        if phone_number_match:
            details['Phone number'] = phone_number_match.group(1)
    
    # Extract vehicle
    vehicle_match = re.search(r'\* Vehicle (.+)', email_body)
    if vehicle_match:
        details['Vehicle'] = vehicle_match.group(1)
    else:
        vehicle_match = re.search(r'\* Vehicle: (.+)', email_body)
        if vehicle_match:
            details['Vehicle'] = vehicle_match.group(1)

    # Extract total amount
    total_match = re.search(r'\* Total: (\£[\d\.]+)', email_body)
    if total_match:
        details['Total'] = total_match.group(1)
    
    # Extract drop-off time (first occurrence)
    dropoff_time_match = re.search(r'\* Date/Time (.+)', email_body)
    if dropoff_time_match:
        details['Dropoff Time'] = dropoff_time_match.group(1).strip()
    else:
        details['Dropoff Time'] = "N/A"
    
    # Extract pick-up time (second occurrence)
    matches = re.findall(r'\* Date/Time\s*:\s*(.+)', email_body)
    if len(matches) > 1:
        details['Pickup Time'] = matches[1].strip()  # Get the second match
    else:
        details['Pickup Time'] = "N/A"

    # Search for "cancelled" in the whole email body
    cancelled_match = re.search(r'cancelled', email_body, re.IGNORECASE)
    if cancelled_match:
        details['status'] = 'cancell'
    else:
        details['status'] = 'active'  # Default value if "cancelled" is not found

    return details

# Iterate through each email and create a separate CSV for each one
for num in id_list:
    typ, data = mail.fetch(num, '(RFC822)')
    if typ != 'OK':
        print(f"Fetch failed for email id {num}: {typ}")
        continue

    raw_email = data[0][1]
    raw_email_string = raw_email.decode('utf-8')
    email_message = email.message_from_string(raw_email_string)

    # Get the sender's email address
    email_from = email.utils.parseaddr(email_message['from'])[1]

    # Only process emails from umairrasheed57@gmail.com
    if email_from != 'bhx@ukmails.co.uk':
        print(f"Skipping email from: {email_from}")
        continue

    # Initialize email details
    email_subject = email_message['subject']
    email_body = ""
    attachments = []

    if email_message.is_multipart():
        for part in email_message.walk():
            content_type = part.get_content_type()
            content_disposition = str(part.get('Content-Disposition'))

            if content_type == 'text/plain':
                email_body += safe_decode(part.get_payload(decode=True))
            elif content_type == 'text/html':
                html_body = safe_decode(part.get_payload(decode=True))
                email_body += f"\n\n--- HTML Content ---\n{html_body}"
            elif 'attachment' in content_disposition:
                filename = part.get_filename()
                if filename:
                    attachments.append((filename, part.get_payload(decode=True)))
    else:
        # Not multipart - i.e., plain text or HTML
        email_body = safe_decode(email_message.get_payload(decode=True))
    
    # Extract details from the email body
    details = extract_details(email_body)

    # Create a unique CSV filename for each email using timestamp and email ID
    email_timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    email_file = os.path.join(FOLDER_PATH, f'email_{email_timestamp}_{num.decode("utf-8")}.csv')

    # Write the extracted details to the unique CSV file
    with open(email_file, 'w', newline='', encoding='utf-8') as csvfile:
        fieldnames = ['Booking number', 'Name', 'Phone number', 'Vehicle', 'Total', 'Dropoff Time', 'Pickup Time','status']
        writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerow(details)
    
    print(f"Email {num.decode('utf-8')} details have been saved to {email_file}")
