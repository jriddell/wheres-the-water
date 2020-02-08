#!/usr/bin/python3

# Copyright 2018 Jonathan Riddell <jr@jriddell.org>
# May be copied under the terms of the GNU GPL version 3 or later only

# Checks the timestamp written by SCA server and if it is older than an hour print a warning which cron will e-mail to me

import urllib.request
import time
import os

WARNING_TIME = 3600

response = urllib.request.urlopen("https://www.andyjacksonfund.org.uk/wheres-the-water/data/DOWNLOAD-READINGS-TIMESTAMP")
timestamp = int(response.read())

if (time.time() - timestamp) > WARNING_TIME:
    print("Warning Warning, code red, the DOWNLOAD-READINGS-TIMESTAMP file is over an hour old")
    os.system('/home/jr/wheres-the-water/wheres-the-water/telegram-notify.rb Out of date WtW readings')
#else:
#    print("we are cool")
#    os.system('/home/jr/wheres-the-water/wheres-the-water/telegram-notify.rb WtW readings are all good')

#response = urllib.request.urlopen("http://canoescotland.org/wheres-the-water/charts/CHARTS-GENERATED-TIMESTAMP")
#timestamp = int(response.read())

#if (time.time() - timestamp) > WARNING_TIME * 2 :
#    print("Warning Warning, code red, the CHARTS-GENERATED-TIMESTAMP file is over two hours old")
