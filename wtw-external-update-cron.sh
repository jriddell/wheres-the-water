#!/bin/bash

# This gets run on an external server (my embra one in 2018) to trigger the downloading of river readings
# It can't be run from canoescotland.org because of firewall rules
# It also downloads the river-sections.json, tidies it up and adds it into Git for backup and changelog

#tell both servers to update river readings from SEPA
wget http://`cat ~/bin/wtw-admin-ajfund`@www.andyjacksonfund.org.uk/wheres-the-water/admin/download-river-readings.php?download=1 -o /dev/null -O /dev/null
wget http://`cat ~/bin/wtw-admin-canoescotland`@canoescotland.org/wheres-the-water/admin/download-river-readings.php?download=1  -o /home/jr/tmp/riverlog -O /home/jr/tmp/river

cd /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water; ./get-farson-cameras.sh
wget http://`cat ~/bin/wtw-admin-canoescotland`@canoescotland.org/wheres-the-water/admin/update-thumbs.php?download=1  -o /home/jr/tmp/riverlog-thumbs -O /home/jr/tmp/river-thumbs

# backup section data from SCA server into Git
wget http://canoescotland.org/wheres-the-water/data/river-sections.json -o /dev/null -O /home/jr/tmp/river-sections.json
python -mjson.tool /home/jr/tmp/river-sections.json > /home/jr/tmp/river-sections-tidy.json
if [ -s /home/jr/tmp/river-sections-tidy.json ]; then
    cp /home/jr/tmp/river-sections-tidy.json /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json
    cp /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections-sca-copy.json 
    cd /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/
    git diff
    git add river-sections-sca-copy.json > /dev/null
    git commit -m 'update river-sections-sca-copy.json from sca server' > /dev/null && git push 2>&1 > /dev/null
else
    echo "embra cron: downloaded river-sections.json has zero size, not updating git"
fi
