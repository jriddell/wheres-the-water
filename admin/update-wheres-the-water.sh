#!/bin/bash
cd /var/www/vhosts/canoescotland.org/httpdocs
rm -f master.zip
wget https://github.com/jriddell/wheres-the-water/archive/master.zip
unzip -o master.zip
rm -f master.zip

