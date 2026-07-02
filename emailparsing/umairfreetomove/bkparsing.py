import imaplib
import ssl
import email
import csv
import os
from pathlib import Path
from datetime import datetime

FOLDER_PATH = r'/var/www/html/email_files/umairtest/'
IMAGES_PATH = os.path.join(FOLDER_PATH, 'images')

email_user = 'umair@ukmails.co.uk'  # Email
email_pass = 'Hakuyasha123@'      # Password

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

# Prepare CSV file with timestamp
timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
csv_file = os.path.join(FOLDER_PATH, f'emails_{timestamp}.csv')
os.makedirs(FOLDER_PATH, exist_ok=True)
os.makedirs(IMAGES_PATH, exist_ok=True)

def safe_decode(payload, encoding='utf-8'):
    try:
        return payload.decode(encoding)
    except UnicodeDecodeError:
        return payload.decode('ISO-8859-1')

with open(csv_file, 'w', newline='', encoding='utf-8') as csvfile:
    #fieldnames = ['From', 'Subject', 'Body']
    fieldnames = ['Body']

    writer = csv.DictWriter(csvfile, fieldnames=fieldnames)

    writer.writeheader()

    for num in id_list:
        typ, data = mail.fetch(num, '(RFC822)')
        if typ != 'OK':
            print(f"Fetch failed for email id {num}: {typ}")
            continue

        raw_email = data[0][1]
        raw_email_string = raw_email.decode('utf-8')
        email_message = email.message_from_string(raw_email_string)

        # Initialize email details
        email_subject = email_message['subject']
        email_from = email_message['from']
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

        # Write email details to CSV
        writer.writerow({'Body': email_body})
        #writer.writerow({'From': email_from, 'Subject': email_subject, 'Body': email_body})

        print(f'From: {email_from}')
        print(f'Subject: {email_subject}')
        print(f'Body:\n{email_body}')

print(f'Emails have been saved to {csv_file}')
