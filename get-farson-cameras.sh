#!/bin/bash

# Needs ./farsoncookie file with cookie in style
# _watercams_session=d3RsOHVXditWY...
curl --cookie `cat farsoncookie` https://www.farsondigitalwatercams.com/scotland/locations > data/farson-camera-locations
curl --cookie `cat farsoncookie` https://www.farsondigitalwatercams.com/scotland/locations?page=2 >> data/farson-camera-locations
curl --cookie `cat farsoncookie` https://www.farsondigitalwatercams.com/scotland/locations?page=3 >> data/farson-camera-locations
curl --cookie `cat farsoncookie` https://www.farsondigitalwatercams.com/scotland/locations?page=4 >> data/farson-camera-locations
