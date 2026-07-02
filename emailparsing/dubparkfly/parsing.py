import imaplib
import ssl
import email
import csv
import os
import re
import subprocess
from datetime import datetime
from email.header import decode_header

# Configuration
FOLDER_PATH = r'/var/www/html/email_files/dubparkfly/'
IMAGES_PATH = os.path.join(FOLDER_PATH, 'images')
email_user = 'dub@ukmails.co.uk'
email_pass = 'Hakuyasha123@'
imap_server = "imap.hostinger.com"

# Ensure required directories exist
os.makedirs(FOLDER_PATH, exist_ok=True)
os.makedirs(IMAGES_PATH, exist_ok=True)

# Setup IMAP connection
context = ssl.create_default_context()
mail = imaplib.IMAP4_SSL(imap_server, ssl_context=context)

try:
    mail.login(email_user, email_pass)
except imaplib.IMAP4.error as e:
    print(f"Login failed: {e}")
    exit()

mail.select('INBOX')

# Create or confirm "ParkAndFly" folder exists
try:
    mail.create('ParkAndFly')
except imaplib.IMAP4.error:
    pass  # Folder likely already exists


# Search for emails
status, data = mail.search(None, 'FROM', '"booking@parkandfly.ie"')
if status != 'OK' or not data[0]:
    print("No emails found from booking@parkandfly.ie.")
    exit()

id_list = data[0].split()

def safe_decode(payload, encoding='utf-8'):
    try:
        return payload.decode(encoding)
    except:
        try:
            return payload.decode('ISO-8859-1')
        except:
            return payload.decode(errors='ignore')

def remove_html_tags(text):
    return re.sub(r'<.*?>', '', text)

def extract_details(email_body):
    details = {}
    
    patterns = {
        'Booking number': r'Booking ID:\s*(\d+)',
        'Name': r'Dear\s+([A-Za-z\s]+),',
        'Phone number': r'Contact Number:\s*([\d\s]+)',
        'Vehicle': r'Vehicle Details:\s*([\w\s]+)C',
        'Total': r'Price:\s*(\d+)',
        'Dropoff Time': r'From Date & Time:\s*([\d-]+\s[\d:]+)h',
        'Pickup Time': r'To Date & Time:\s*([\d-]+\s[\d:]+)h',
        'Departure Terminal': r'Departure Terminal:\s*([A-Za-z0-9]+)R',
        'Return Terminal': r'Return Terminal:\s*([A-Za-z0-9]+)R',
        'Return Flight': r'Return Flight:\s*([\w\s\.]+)T',
    }

    for key, pattern in patterns.items():
        match = re.search(pattern, email_body)
        details[key] = match.group(1).strip() if match else 'N/A'

    # Status check
    details['Status'] = 'cancelled' if re.search(r'cancelled', email_body, re.IGNORECASE) else 'active'
    
    return details

for num in id_list:
    typ, msg_data = mail.fetch(num, '(RFC822)')
    if typ != 'OK':
        print(f"Failed to fetch email {num.decode()}")
        continue

    raw_email = msg_data[0][1]
    email_message = email.message_from_bytes(raw_email)
    email_body = ""

    if email_message.is_multipart():
        for part in email_message.walk():
            content_type = part.get_content_type()
            if content_type in ['text/plain', 'text/html']:
                try:
                    payload = part.get_payload(decode=True)
                    email_body += safe_decode(payload)
                except Exception as e:
                    print(f"Error decoding part: {e}")
    else:
        email_body = safe_decode(email_message.get_payload(decode=True))

    email_body = remove_html_tags(email_body)
    details = extract_details(email_body)

    # Save to CSV
    timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    csv_file = os.path.join(FOLDER_PATH, f'email_{timestamp}_{num.decode()}.csv')

    with open(csv_file, 'w', newline='', encoding='utf-8') as f:
        writer = csv.DictWriter(f, fieldnames=[
            'Booking number', 'Name', 'Phone number', 'Vehicle', 'Total',
            'Dropoff Time', 'Pickup Time', 'Departure Terminal', 'Return Terminal',
            'Return Flight', 'Status'
        ])
        writer.writeheader()
        writer.writerow(details)

    print(f"Saved email {num.decode()} details to {csv_file}")

    # Move and delete
    try:
        mail.copy(num, 'INBOX.ParkAndFly')
        mail.store(num, '+FLAGS', '\\Deleted')
        mail.expunge()
    except imaplib.IMAP4.error as e:
        print(f"Error moving/deleting email {num.decode()}: {e}")

# Run PHP script
php_script = '/var/www/html/booking/emailparsing/dubparkfly/parkflyinsertion.php'
try:
    result = subprocess.run(['php', php_script], check=True)#capture_output=True, text=True, 
    print("PHP script executed successfully.")
    print("STDOUT:", result.stdout)
except subprocess.CalledProcessError as e:
    print("Error running PHP script:")
    print(e.stderr or str(e))
