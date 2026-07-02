import email
import imaplib, ssl
import re
from bs4 import BeautifulSoup
import requests
import json

data = []
mylist = []
# IMAP server connection details
IMAP_SERVER = 'imap.hostinger.com'
USERNAME = 'bhx@ukmails.co.uk'
PASSWORD = 'Hakuyasha123@'

context = ssl.SSLContext(ssl.PROTOCOL_SSLv23)
# PROTOCOL_TLS_SERVER or PROTOCOL_TLS_CLIENT 
# context = ssl.SSLContext(ssl.PROTOCOL_TLS_CLIENT)

# Connect to the IMAP server
mail = imaplib.IMAP4_SSL(IMAP_SERVER)
mail.login(USERNAME, PASSWORD)

# Select the mailbox you want to parse emails from
mail.select('inbox')

def find_second_occurrence(pattern, text):
    # Compile the regex pattern
    compiled_pattern = re.compile(pattern)
    
    # Find all matches using finditer, which returns an iterator yielding match objects
    matches = list(compiled_pattern.finditer(text))
    
    # Check if there are at least two matches
    if len(matches) >= 2:
        second_match = matches[1]
        return second_match.group(), second_match.start(), second_match.end()
    else:
        return None
    
# Search for all emails in the selected mailbox
type, email_ids = mail.search(None, 'ALL')
# Loop through the list of email IDs
for email_id in email_ids[0].split():
    # Fetch the email using its ID
    type, email_data = mail.fetch(email_id, '(RFC822)')
    raw_email = email_data[0][1]

    # Parse the raw email data
    msg = email.message_from_bytes(raw_email)

    # Extract email headers
    subject = msg['subject']
    sender = msg['from']
    date = msg['date']
    supplier = msg.get('Subject').split(" ")[0]
    status = msg.get('Subject').split(" ")[2]


    ref_pattern = r'Booking number:\s*(.*)'
    name_pattern = r'Name :\s*(.*)'
    phone_pattern = r'Phone number :\s*(.*)'
    vehicle_pattern = r'Vehicle :\s*(.*)'
    total_pattern = r'Total:\s*(.*)'

    agency_pattern = r'Agency :\s*(.*)'
    dropoff_pattern = r'Date/Time :\s*(.*)'
    pickup_pattern = r'Date/Time :\s*(.*)'
    # Extract email body
    if msg.is_multipart():
        for part in msg.walk():
            content_type = part.get_content_type()
            content_disposition = str(part.get("Content-Disposition"))

            # Extract text/plain parts
            if "text/plain" in content_type and "attachment" not in content_disposition:
                body = part.get_payload(decode=True).decode()
                soup = BeautifulSoup(body, 'html.parser')
                text = soup.get_text(separator='\n', strip=True)

                # print("supplier:", supplier)
                # print("Subject:", subject)
                # print("From:", sender)
                # print("Date:", date)
                # print("if--------------------------------------")
    else:
        body = msg.get_payload(decode=True).decode()
        soup = BeautifulSoup(body, 'html.parser')
        text = soup.get_text(separator='\n', strip=True)
        text1 = soup.get_text()

        ref_match = re.search(ref_pattern, text)
        ref = ref_match.group(1) if ref_match else None
        
        name_match = re.search(name_pattern, text)
        name = name_match.group(1) if name_match else None

        phone_match = re.search(phone_pattern, text)
        phone = phone_match.group(1) if phone_match else None

        vehicle_pattern = re.search(vehicle_pattern, text)
        vehicle = vehicle_pattern.group(1) if vehicle_pattern else None

        total_match = re.search(total_pattern, text)
        total = total_match.group(1) if total_match else None

        agency_pattern = re.search(agency_pattern, text)
        agency = agency_pattern.group(1) if agency_pattern else None

        dropoff_pattern = re.search(dropoff_pattern, text)
        drop_off = dropoff_pattern.group(1) if dropoff_pattern else None
        
        pickup_pattern = re.search(pickup_pattern, text)
        pickup = pickup_pattern.group(1) if pickup_pattern else None

        pattern = r'Date/Time :\s*(.*)'
        compiled_pattern = re.compile(pattern)

        # Find all matches using finditer, which returns an iterator yielding match objects
        matches = list(compiled_pattern.finditer(text))
        pattern2 = r'\s*\w{3},\s*\w{3}\s*\d{1,2},\s*\d{4}\s*\d{1,2}:\d{2}\s*[AP]M'
        result = find_second_occurrence(pattern2,text)
        match_text = ''
        # print(f"r: {result}")
        # print(f"name: {name}")
        if result:
            match_text, start, end = result
            # print(f"Second occurrence: '{match_text}' found at position {start} to {end}")
        # else:
        #     print("Pattern not found at least twice.")
        price = 0
        if total:
            price = re.findall(r'[\d.]+', total)[0]

        data2={}
        if name:
            mylist.append(name)
            data2["ref"]= ref
            data2["source"]= supplier
            data2["name"]= name
            data2["phone"]= phone
            data2["vehicle"]= vehicle
            data2["total"]= price
            data2["drop_off"]= drop_off
            data2["agency"]= agency
            data2["pickup"]= match_text
            data2["order_date"]= date
            data2["status"]= status

            data.append(data2)
        # print(f"Data: {data}")
        # print("ref:", ref)
        # print("supplier:", supplier)
        print("Subject:", subject)
        print("From:", sender)
        # print("Date:", date)
        # print("\nName:", name)
        # print("\nPhone:", phone)
        # print("\nvehicle:", vehicle)
        # print("\nTotal:", total)
        # print("\nPrice:", price)
        # # print("\nmatches:", matches)
        # print("\ndrop_off:", drop_off)
        # print("\npickup:", match_text)
        print("--------------------------------------")
        mail.copy('INBOX.Processed')
        mail.store('+FLAGS', '\\Deleted')
# Logout from the IMAP server
# for i in mylist:
#     mail.copy(i, 'INBOX.Processed')
#     mail.store(i, '+FLAGS', '\\Deleted')
mail.expunge()

# php_script = '/var/www/html/booking/emailparsing/free2move/insert_email_files_data.php'

php_script="/var/www/html/booking/emailparsing/free2move/insert_email_files_data.php"
# php_script="http://localhost/fiver/gp/pythondata2.php"

# try:
#     with open(php_script, 'r') as file:
#         # Read or perform operations on the file
#         print("File found")
#         pass
# except FileNotFoundError:
#     print("File not found")
# except Exception as e:
#     print("An error occurred:", e)
# Send the data as a JSON payload

# print(f"data: ",data)

# # Convert the data to JSON format
json_data = json.dumps(data)

# # Set the headers to indicate that we are sending JSON data
headers = {
    'Content-Type': 'application/json'
}

# # Send the POST request with the data
response = requests.post(php_script, data=json_data, headers=headers)

# print('Response content:', response)

try:
    # Check the response status code
    if response.status_code == 200:
        # Attempt to parse the response as JSON
        response_data = response.json()
        # Log the response
        # logging.info('Response received: %s', response.text)
        print('Response from server:', response_data)
    else:
        print('Failed to get a valid response. Status code', response.status_code)
        # logging.error('Failed to get a valid response. Status code: %d', response.status_code)
except Exception as e:
    # Log any exceptions
    # logging.error('An error occurred: %s', str(e))
    print('Failed to decode JSON from response. Raw response:', response.text)