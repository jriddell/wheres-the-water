#!/usr/bin/ruby

# Update SEPA gauge readings and backup river sections, usually run every minute by cron

# This gets run on our host server to trigger the downloading of river readings from SEPA
# It also downloads the river-sections.json (which may have been edited through the admin webpage),
# tidies it up and adds it into Git for backup and changelog

require 'pp'
require 'net/http'
require 'net/https'
require 'json'
require 'logger'
LOCK_FILE = Dir.pwd + '/' + 'CRON-UPDATE-SEPA-GAUGES-AND-BACKUP-LOCK'
RIVER_SECTIONS_FILE = '/home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json'
RIVER_SECTIONS_FILE_COPY = '/home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections-sca-copy.json'
RIVER_SECTIONS_DEV_FILE = '/home/jr/www/dev.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json'

class UpdateAndBackup
  def run_locked
    # Write lock file or quit if it exists
    if File.exist?(LOCK_FILE)
        #pp "Instance of cron-update-sepa-gauges-and-backup.rb already running"
        exit
    end
    lock_file = File.new(LOCK_FILE, 'w')
    lock_file.close
    #pp "lock written"

    yield

    # Remove lock file
    File.delete(LOCK_FILE)
    #pp "lock deleted"
  end
  
  def admin_login
    f = File.open('/home/jr/bin/wtw-admin-ajfund', 'r')
    login = f.read
    login.chomp!
  end

  def trigger_sepa_gauges_update
    %x{wget https://`cat ~/bin/wtw-admin-ajfund`@www.andyjacksonfund.org.uk/wheres-the-water/admin/download-river-readings.php?download=1  -o /dev/null -O /dev/null }
    %x{wget http://`cat ~/bin/wtw-admin-ajfund`@www.andyjacksonfund.org.uk/wheres-the-water/admin/download-river-readings.php?download=1  -o /dev/null -O /dev/null }
  end

  def run
    run_locked do
      #pp "running locked"
      trigger_sepa_gauges_update()

      # get JSON and tidy and backup
      river_sections_json = JSON.parse(File.open(RIVER_SECTIONS_FILE).read)
      File.write(RIVER_SECTIONS_FILE, JSON.pretty_generate(river_sections_json))
      File.write(RIVER_SECTIONS_FILE_COPY, JSON.pretty_generate(river_sections_json))
      File.write(RIVER_SECTIONS_DEV_FILE, JSON.pretty_generate(river_sections_json))

      # commit to git
      diff = `git diff`
      puts diff if diff.length > 3
      `git add data/river-sections-sca-copy.json`
      `git commit -m 'update river-sections-sca-copy.json from embra server' && git push`
    end
    #pp "run done"
  end
end

update_and_backup = UpdateAndBackup.new
begin
  update_and_backup.run
rescue StandardError => e
  puts "exception, quitting " + e.message
  # Really remove lock file
  pp "really deleting lock"
  File.delete(LOCK_FILE)
end
