#!/bin/env python3

import argparse
import csv
import json
from pathlib import Path
from typing import Any, Dict, Iterable
from datetime import datetime
from datetime import date, timedelta

#!/usr/bin/env python3
"""
Convert sse-freshet-schedule-2025.csv -> schedules-sections-2025.json

Usage:
    python wtw-freshet-convert.py \
            --in sse-freshet-schedule-2025.csv \
            --out schedules-sections-2025.json
"""


# Releases are either one day or Friday midday to Monday midday
def aweConvert(row):
  sepaDate = row.get('Release Start Date')
  if not sepaDate:
    return None
  sepaDate = sepaDate.strip()
  dateFormat = "%A, %B %d, %Y"
  try:
    date = datetime.strptime(sepaDate, dateFormat)
    if sepaDate.split()[0].strip(',') == 'Friday':
      date2 = date + timedelta(days=1)
      date3 = date + timedelta(days=2)
      date4 = date + timedelta(days=3)
      return [date.strftime("%Y-%m-%d"), date2.strftime("%Y-%m-%d"), 
              date3.strftime("%Y-%m-%d"), date4.strftime("%Y-%m-%d")]
    else:
      return date.strftime("%Y-%m-%d")
  except ValueError:
    return None

# Dundreggan (River Moriston) - two day releases
# But times vary so point to calendar
def moristonConvert(row):
  sepaDate = row.get('Release Start Date')
  if not sepaDate:
    return None
  sepaDate = sepaDate.strip()
  dateFormat = "%A, %B %d, %Y"
  try:
    date = datetime.strptime(sepaDate, dateFormat)
    date2 = date + timedelta(days=1)
    return [date.strftime("%Y-%m-%d"), date2.strftime("%Y-%m-%d")]
  except ValueError:
    return None

# Garry (Invergarry)
# Releases usually 8am to evening or early next day
def garryConvert(row):
  sepaDate = row.get('Release Start Date')
  if not sepaDate:
    return None
  sepaDate = sepaDate.strip()
  dateFormat = "%A, %B %d, %Y"
  try:
    date = datetime.strptime(sepaDate, dateFormat)
    return date.strftime("%Y-%m-%d")
  except ValueError:
    return None

# Dunalastair - three day releases start 14:00 until 13:00 day after next
def upperTummelConvert(row):
  sepaDate = row.get('Release Start Date')
  if not sepaDate:
    return None
  sepaDate = sepaDate.strip()
  dateFormat = "%A, %B %d, %Y"
  try:
    date = datetime.strptime(sepaDate, dateFormat)
    date2 = date + timedelta(days=1)
    date3 = date + timedelta(days=2)
    return [date.strftime("%Y-%m-%d"), date2.strftime("%Y-%m-%d"), date3.strftime("%Y-%m-%d")]
  except ValueError:
    return None

# Clunie - 16:00 Friday to 08:00 Monday
def lowerTummelConvert(row):
  sepaDate = row.get('Release Start Date')
  if not sepaDate:
    return None
  sepaDate = sepaDate.strip()
  dateFormat = "%A, %B %d, %Y"
  try:
    date = datetime.strptime(sepaDate, dateFormat)
    date2 = date + timedelta(days=1)
    date3 = date + timedelta(days=2)
    return [date2.strftime("%Y-%m-%d"), date3.strftime("%Y-%m-%d")]
  except ValueError:
    return None

# Stronuich – River Lyon
# List next day
def lyonConvert(row):
  sepaDate = row.get('Release Start Date')
  if not sepaDate:
    return None
  sepaDate = sepaDate.strip()
  dateFormat = "%A, %B %d, %Y"
  try:
    date = datetime.strptime(sepaDate, dateFormat)
    date = date + timedelta(days=1)
    return date.strftime("%Y-%m-%d")
  except ValueError:
    return None

# Confusingly there is a Cluanie on the Moriston too
conversationFunctions = {
  'Loch Awe Barrage': aweConvert,
  'Dundreggan (River Moriston)': moristonConvert,
  'Garry (Invergarry)': garryConvert,
  'Dunalastair': upperTummelConvert,
  'Clunie - River Tummel': lowerTummelConvert,
  'Stronuich – River Lyon': lyonConvert
}

sepaNamesToWtwNames = {
  'Loch Awe Barrage': 'Awe',
  'Dundreggan (River Moriston)': 'Moriston',
  'Garry (Invergarry)': 'Garry (Great Glen)',
  'Dunalastair': 'Tummel (Upper)',
  'Clunie - River Tummel': 'Tummel (Lower)',
  'Stronuich – River Lyon': 'Lyon'
}

def main():
  parser = argparse.ArgumentParser(description="Convert CSV schedule to JSON (sections-aware).")
  parser.add_argument("--year", "-y", dest="year", default="2025",
                                          help="Year to do (default: 2025)")
  args = parser.parse_args()

  in_path = Path("sse-freshet-schedule-" + args.year + ".csv")
  out_path = Path("scheduled-sections-" + args.year + ".json")

  if not in_path.exists():
    raise SystemExit(f"Input file not found: {in_path}")

  dams = {'Awe': [], 
          'Garry (Great Glen)': [],
          'Lyon': [],
          'Moriston': [],
          'Tummel (Upper)': [],
          'Tummel (Lower)': []
         }

  print("Reading from:", in_path)
  with in_path.open("r", encoding="utf-8-sig", newline="") as csvFile:
    reader = csv.DictReader(csvFile)
    fieldnames = reader.fieldnames
    print("Field names:", fieldnames)
    for row in reader:
      if row['Location Description'] in conversationFunctions.keys():
        releaseDate = conversationFunctions[row['Location Description']](row)
        key = sepaNamesToWtwNames[row['Location Description']]
        target = dams[key]
        if isinstance(releaseDate, str):
            target.append(releaseDate)
        elif isinstance(releaseDate, list):
            target.extend(releaseDate)
        elif releaseDate is not None:
            target.append(releaseDate)

  # Read the json template
  templateFile = Path("scheduled-sections-template.json")

  with templateFile.open("r", encoding="utf-8") as jsonFile:
    template = json.load(jsonFile)

  # Put the rivers into the template
  template[2]['dates'] = dams['Awe']
  template[3]['dates'] = dams['Garry (Great Glen)']
  template[4]['dates'] = dams['Lyon']
  template[5]['dates'] = dams['Moriston']
  template[6]['dates'] = dams['Tummel (Upper)']
  template[7]['dates'] = dams['Tummel (Lower)']

  # Get Falls of Lora
  template[0]['dates'] = getFallsofLora()

  print(json.dumps(template, indent=2))
  print("Writing to:", out_path)
  with out_path.open("w", encoding="utf-8") as outFile:
    json.dump(template, outFile, indent=2)

if __name__ == "__main__":
  main()
