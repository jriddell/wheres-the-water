#!/usr/bin/ruby

# Update SEPA gauge readings and backup river sections, usually run every minute by cron

# This gets run on our host server to trigger the downloading of river readings from SEPA
# It also downloads the river-sections.json (which may have been edited through the admin webpage),
# tidies it up and adds it into Git for backup and changelog

require 'pp'
require 'net/http'
require 'net/https'
require 'json'
require 'git'
LOCK_FILE = Dir.pwd + '/' + 'CRON-UPDATE-SEPA-GAUGES-AND-BACKUP-LOCK'
RIVER_SECTIONS_FILE = '/home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json'
RIVER_SECTIONS_FILE_COPY = '/home/jr/www/www.andyjacksonfund.org.uk/wheres-the-water/data/river-sections-sca-copy.json'
RIVER_SECTIONS_DEV_FILE = '/home/jr/www/dev.andyjacksonfund.org.uk/wheres-the-water/data/river-sections.json'

class UpdateAndBackup
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
  
  def admin_login
    f = File.open('/home/jr/bin/wtw-admin-ajfund', 'r')
    login = f.read
    login.chomp!
  end

  def trigger_sepa_gauges_update
    uri = URI("https://" + admin_login + "@www.andyjacksonfund.org.uk/wheres-the-water/admin/download-river-readings.php?download=1")

    req = Net::HTTP::Get.new(uri)
    req.basic_auth uri.user, uri.password

    res = Net::HTTP.start(uri.hostname, uri.port, :use_ssl => true) {|http|
      http.request(req)
    }
    #puts res.body
    uri2 = URI("http://" + admin_login + "@dev.andyjacksonfund.org.uk/wheres-the-water/admin/download-river-readings.php?download=1")
    response = Net::HTTP.get(uri2)
    #puts response
  end
  
  def run
    run_locked do
      pp "running locked"
      trigger_sepa_gauges_update()

      # get JSON and tidy and backup
      river_sections_json = JSON.parse(File.open(RIVER_SECTIONS_FILE).read)
      File.write(RIVER_SECTIONS_FILE, JSON.pretty_generate(river_sections_json))
      File.write(RIVER_SECTIONS_FILE_COPY, JSON.pretty_generate(river_sections_json))
      File.write(RIVER_SECTIONS_DEV_FILE, JSON.pretty_generate(river_sections_json))

      # git diff and add and push
      git = Git.open(Dir.pwd)
      git.add('data/river-sections-sca-copy.json')
      begin
        git.commit('Update river-sections-sca-copy.json')
        git.push
      rescue StandardError => e
        puts e.message
      end
    end
    pp "run done"
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
