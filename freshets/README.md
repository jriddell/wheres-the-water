# Where's the Water Freshet Convert

Takes SSE Spreadsheet of Freshet Releases and converts it for use by Where's the Water

Each year run it once.  Save the Release Frequency sheet on the Freshet schedule ODS file as a CSV called sse-freshet-schedule-2026.csv
Sync the template with cp ../data/scheduled-sections.json scheduled-sections-template.json
Download Falls of Lora from https://www.fallsoflora.info/times-and-height-predictions/tide-tables/
`./wtw-freshet-convert.py -y 2026`

Add in the Falls of Lora dates manually (for now) and the Solway Firth Bore dates manually (for now) and copy into the data directory on new year's day.
