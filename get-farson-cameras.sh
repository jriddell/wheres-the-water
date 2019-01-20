#!/bin/bash

# Needs ./farsoncookie file with cookie in style
# _watercams_session=d3RsOHVXditWY...
curl --cookie `cat farsoncookie` https://www.farsondigitalwatercams.com/scotland/locations > data/farson-camera-locations
