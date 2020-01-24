#!/bin/bash

# This gets run on an external server (my embra one in 2018) to trigger the downloading of river readings
# It can't be run from canoescotland.org because of firewall rules
# It also downloads the river-sections.json, tidies it up and adds it into Git for backup and changelog

#tell both servers to update river readings from SEPA
wget https://`cat ~/bin/wtw-admin-ajfund`@www.andyjacksonfund.org.uk/wheres-the-water/admin/download-river-readings.php?download=1 -o /dev/null -O /dev/null
wget https://`cat ~/bin/wtw-admin-ajfund`@dev.andyjacksonfund.org.uk/wheres-the-water/admin/download-river-readings.php?download=1 -o /dev/null -O /dev/null

# backup section data from my server into Git
python -mjson.tool /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json > /home/jr/tmp/river-sections-tidy.json
HEAD=`head -n1 /home/jr/tmp/river-sections-tidy.json`
if [ $HEAD = '[' ]; then
    cp /home/jr/tmp/river-sections-tidy.json /home/jr/www/dev.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json
    cp /home/jr/tmp/river-sections-tidy.json /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json
    cp /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections-sca-copy.json 
    cd /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/
    git diff
    git add river-sections-sca-copy.json > /dev/null
    git commit -m 'update river-sections-sca-copy.json from embra server' > /dev/null && git push 2>&1 > /dev/null
else
    echo "embra cron: downloaded river-sections.json has zero size, not updating git"
    cp /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections-sca-copy.json /home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json
fi
