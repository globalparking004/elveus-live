import imaplib
import ssl
import email
import csv
import os
import re
import subprocess
from datetime import datetime

# Define folder paths and email credentials
FOLDER_PATH = r'/var/www/html/email_files/freeeetomove/bristol/'
IMAGES_PATH = os.path.join(FOLDER_PATH, 'images')
email_user = 'bristol@ukmails.co.uk'
email_pass = 'Hakuyasha123@'

# Setup SSL context and connect to the IMAP server
context = ssl.SSLContext(ssl.PROTOCOL_SSLv23)
mail = imaplib.IMAP4_SSL("imap.hostinger.com")

try:
    mail.login(email_user, email_pass)
except imaplib.IMAP4.error as e:
    print(f"Login failed: {e}")
    exit()

mail.select('INBOX')

# Create or select the "Processed" folder
try:
    mail.create('FreeToMove')  # Create the folder if it doesn't exist
except imaplib.IMAP4.error as e:
    print(f"Error creating 'Processed' folder: {e}")

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
    booking_number_match = re.search(r'Booking number\s*:\s*(\S+)', email_body)
    if booking_number_match:
        details['Booking number'] = booking_number_match.group(1)
    
    # Extract name (fixed to capture "Name" instead of "Client Name")
    name_match = re.search(r'Name\s*:\s*([A-Za-z\s]+?)\s*Phone number', email_body)
    if name_match:
        details['Name'] = name_match.group(1).strip()
    else:
        details['Name'] = 'Unknown'  # Default if name is not found

    # Extract phone number
    phone_number_match = re.search(r'Phone number\s*:\s*(\+[\d]+)', email_body)
    if phone_number_match:
        details['Phone number'] = phone_number_match.group(1)
    else:
        details['Phone number'] = 'N/A'

    # Extract vehicle
    vehicle_match = re.search(r'Vehicle\s*:\s*(.+?)\s*-\s*([A-Z0-9]+)', email_body, re.IGNORECASE)
    if vehicle_match:
        details['Vehicle'] = f"{vehicle_match.group(1).strip()} ({vehicle_match.group(2).strip()})"
    else:
        details['Vehicle'] = 'N/A'

    # Extract total amount
    total_match = re.search(r'Total\s*:\s*(\£[\d\.]+)', email_body)
    if total_match:
        details['Total'] = total_match.group(1)
    
    # Extract drop-off time (first occurrence)
    dropoff_time_match = re.search(r'Date/Time\s*:\s*([A-Za-z]{3}, [A-Za-z]{3} \d{1,2}, \d{4} \d{1,2}:\d{2} [APM]+)', email_body)
    if dropoff_time_match:
        details['Dropoff Time'] = dropoff_time_match.group(1).strip()
        details['Dropoff Time'] = details['Dropoff Time'].replace(',', '')

    else:
        details['Dropoff Time'] = 'N/A'
    
    # Extract pick-up time (second occurrence)
    matches = re.findall(r'Date/Time\s*:\s*([A-Za-z]{3}, [A-Za-z]{3} \d{1,2}, \d{4} \d{1,2}:\d{2} [APM]+)', email_body)
    if len(matches) > 1:
        details['Pickup Time'] = matches[1].strip()  # Get the second match
        details['Pickup Time'] = details['Pickup Time'].replace(',', '')

    else:
        details['Pickup Time'] = 'N/A'

    # Search for "cancelled" in the whole email body
    cancelled_match = re.search(r'cancelled', email_body, re.IGNORECASE)
    if cancelled_match:
        details['status'] = 'cancelled'
    else:
        details['status'] = 'active'  # Default value if "cancelled" is not found

    return details

def remove_html_tags(text):
    """Remove HTML tags from a string."""
    clean = re.compile('<.*?>')
    return re.sub(clean, '', text)

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

    # Only process emails from contact@free2move.com
    if email_from != 'contact@free2move.com':
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
    else:
        # Not multipart - i.e., plain text or HTML
        email_body = safe_decode(email_message.get_payload(decode=True))
    
    # Remove HTML tags from email body
    email_body = remove_html_tags(email_body)

    # Extract details from the cleaned email body
    details = extract_details(email_body)
    print(email_body)

    # Create a unique CSV filename for each email using timestamp and email ID
    email_timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    email_file = os.path.join(FOLDER_PATH, f'email_{email_timestamp}_{num.decode("utf-8")}.csv')

    # Write the extracted details to the unique CSV file
    with open(email_file, 'w', newline='', encoding='utf-8') as csvfile:
        fieldnames = ['Booking number', 'Name', 'Phone number', 'Vehicle', 'Total', 'Dropoff Time', 'Pickup Time', 'status']
        writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerow(details)
    
    print(f"Email {num.decode('utf-8')} details have been saved to {email_file}")

    # Move the email to the "Processed" folder and then delete it from the "Inbox"
    try:
        mail.copy(num, 'INBOX.FreeToMove')  # Attempt to copy to the "Processed" folder
        mail.store(num, '+FLAGS', '\\Deleted')  # Mark for deletion
        mail.expunge()  # Permanently remove emails marked for deletion
        print(f"Email {num.decode('utf-8')} has been moved to 'Processed' and deleted from 'Inbox'")
    except imaplib.IMAP4.error as e:
        print(f"Error processing email {num.decode('utf-8')}: {e}")

    # php_script = '/var/www/html/booking/emailparsing/umairfreetomove/freetomoveinsertion.php'

    # # Run PHP script from Python
    # try:
    #     subprocess.run(['php', php_script], check=True)
    #     print(' PHP script executed successfully.')
    # except subprocess.CalledProcessError as e:
    #     print(f'Error executing PHP script: {e}')
