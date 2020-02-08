#!/usr/bin/ruby

# Send a notification to a Telegram chat, used for check-rivers-up-to-date.py when it's not up to date

require 'telegram/bot'
require 'inifile'

class TelegramNotify

  def initialize
    @myini = IniFile.load('config.ini')
  end
  
  def telegram_token
    @myini['Cron']['TelegramToken']
  end

  def telegram_chat_id
    @myini['Cron']['TelegramChatId']
  end

  def send_message(message)
    Telegram::Bot::Client.run(telegram_token) do |bot|
      bot.api.send_message(chat_id: telegram_chat_id, text: message)
    end
  end
end

notify = TelegramNotify.new
puts notify.send_message(ARGV.join(' '))
