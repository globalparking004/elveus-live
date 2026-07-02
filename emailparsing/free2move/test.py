import datetime
import email
import imaplib, ssl
import re
from bs4 import BeautifulSoup
import requests
import json

# Get the current date and time
now = datetime.datetime.now()

# data["ref"]= 'ACP-BHX-324234'
# data["source"]= 'supplier'
# data["name"]= 'name'
# data["phone"]= 'phone'
# data["vehicle"]= 'vehicle'
# data["total"]= 'price'
# data["drop_off"]= 'drop_off'
# data["agency"]= 'agency'
# data["pickup"]= 'match_text'
# data["order_date"]= 'date'
# data["status"]= 'status'

# php_script="/var/www/html/booking/emailparsing/free2move/test.php"
# # # Convert the data to JSON format
# json_data = json.dumps(data)

# # # Set the headers to indicate that we are sending JSON data
# headers = {
#     'Content-Type': 'application/json'
# }

# # # Send the POST request with the data
# response = requests.post(php_script, data=json_data, headers=headers)

# # print('Response content:', response)

# try:
#     # Check the response status code
#     if response.status_code == 200:
#         # Attempt to parse the response as JSON
#         response_data = response.json()
#         # Log the response
#         # logging.info('Response received: %s', response.text)
#         print('Response from server:', response_data)
#     else:
#         print('Failed to get a valid response. Status code', response.status_code)
#         # logging.error('Failed to get a valid response. Status code: %d', response.status_code)
# except Exception as e:
#     # Log any exceptions
#     # logging.error('An error occurred: %s', str(e))
#     print('Failed to decode JSON from response. Raw response:', response.text)

# # Define the file where the date and time will be logged
log_file = "/var/www/html/booking/emailparsing/test/log.txt"

# Write the date and time to the file
with open(log_file, "a") as file:
    file.write(f"Cron job ran at: {now}\n")
