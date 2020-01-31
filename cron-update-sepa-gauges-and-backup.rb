#!/usr/bin/ruby

# Update SEPA gauge readings and backup river sections, usually run every minute by cron

# This gets run on our host server to trigger the downloading of river readings from SEPA
# It also downloads the river-sections.json (which may have been edited through the admin webpage),
# tidies it up and adds it into Git for backup and changelog

require 'pp'

class UpdateAndBackup
  LOCK_FILE = Dir.pwd + '/' + 'WTW-EXTERNAL-UPDATE-CRON-LOCK'

  def run_locked
    # Write lock file or quit if it exists
    if File.exist?(LOCK_FILE)
        pp "Instance of cron-update-sepa-gauges-and-backup.rb already running"
        exit
    end
    lock_file = File.new(LOCK_FILE, 'w')
    lock_file.close
    pp "lock written"

    yield

    # Remove lock file
    File.delete(LOCK_FILE)
    pp "lock deleted"
  end
  
  def run
    run_locked do
      pp "running locked"
      # GET the download-river-readsings

      # get JSON and tidy

      # copy tidy json to river-sections.json and dev and -sca-copy.json
      # git diff and add and push
    end
    pp "run done"
  end
end

update_and_backup = UpdateAndBackup.new
update_and_backup.run

=begin



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
=end
