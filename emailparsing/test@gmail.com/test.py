import datetime

# Get the current date and time
now = datetime.datetime.now()

# Define the file where the date and time will be logged
log_file = "/var/www/html/booking/emailparsing/test/log.txt"

# Write the date and time to the file
with open(log_file, "a") as file:
    file.write(f"Cron job ran at: {now}\n")
