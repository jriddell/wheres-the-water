#!/usr/bin/python3

# Copyright 2018 Jonathan Riddell <jr@jriddell.org>
# May be copied under the terms of the GNU GPL version 3 or later only

# Checks the timestamp written by SCA server and if it is older than an hour print a warning which cron will e-mail to me

import urllib.request
import time

WARNING_TIME = 3600

response = urllib.request.urlopen("http://canoescotland.org/wheres-the-water/data/DOWNLOAD-READINGS-TIMESTAMP")
timestamp = int(response.read())

if (time.time() - timestamp) > WARNING_TIME:
    print("Warning Warning, code red, the DOWNLOAD-READINGS-TIMESTAMP file is over an hour old")
