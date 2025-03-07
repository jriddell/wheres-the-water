#!/bin/bash

# Copyright 2025 Jonathan Riddell <jr@jriddell.org>

# One line to use the base 64 SEPA key to get the daily access token, run it every hour or so in cron

curl -s -XPOST -H "Authorization: Basic `cat ~/.config/sepa-key.text`" -d "grant_type=client_credentials" https://timeseries.sepa.org.uk/KiWebPortal/rest/auth/oidcServer/token | jq -r .access_token > ~/.config/daily-sepa-token.text
