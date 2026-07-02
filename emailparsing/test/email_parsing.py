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

# Search for all emails in the selected mailbox
type, email_ids = mail.search(None, 'ALL')
# Loop through the list of email IDs
print(f'emails: {email_ids}')

# mail.expunge()
