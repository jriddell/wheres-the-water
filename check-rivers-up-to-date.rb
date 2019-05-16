#!/usr/bin/ruby

# Copyright 2018 Jonathan Riddell <jr@jriddell.org>
# May be copied under the terms of the GNU GPL version 3 or later only

# Checks the timestamp written by SCA server and if it is older than an hour print a warning which cron will e-mail to me

# abandoned as seems ruby on embra is too old to have httparty, go to check-rivers-up-to-date.py

require 'httparty'

WARNING_TIME = 3600

response = HTTParty.get('http://canoescotland.org/wheres-the-water/data/DOWNLOAD-READINGS-TIMESTAMP')

if Time.now - Time.at(response.body.to_i) > WARNING_TIME
    puts "Warning Warning, code red, the DOWNLOAD-READINGS-TIMESTAMP file is over an hour old"
end
