# Where's the Water Freshet Convert

Takes SSE Spreadsheet of Freshet Releases and converts it for use by Where's the Water

Each year run it once.  Save the Release Frequency sheet on the Freshet schedule ODS file as a CSV then run
`./wtw-freshet-convert.py --in sse-freshet-schedule-2026.csv --out scheduled-sections-2026.json`

Add in the Falls of Lora dates manually (for now) and the Solway Firth Bore dates manually (for now) and copy into the data directory on new year's day.
