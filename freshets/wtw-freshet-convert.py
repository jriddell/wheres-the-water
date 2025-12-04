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


NUMERIC_TRY_ORDER = (int, float)


def parse_value(s: str) -> Any:
  if s is None:
          return None
  s = s.strip()
  if s == "":
          return None
  # Try to convert to int/float if it looks numeric
  for fn in NUMERIC_TRY_ORDER:
          try:
                  # reject conversions that change meaning (e.g. "01" -> 1 is OK)
                  val = fn(s)
                  return val
          except Exception:
                  continue
  return s

def row_convert(row: Dict[str, str]) -> Dict[str, Any]:
  return {k: parse_value(v) for k, v in row.items()}

def aweConvert(row):
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
  parser.add_argument("--in", "-i", dest="infile", default="sse-freshet-schedule-2025.csv",
                                          help="Input CSV file (default: sse-freshet-schedule-2025.csv)")
  parser.add_argument("--out", "-o", dest="outfile", default="schedules-sections-2025.json",
                                          help="Output JSON file (default: schedules-sections-2025.json)")
  args = parser.parse_args()

  in_path = Path(args.infile)
  out_path = Path(args.outfile)

  if not in_path.exists():
    raise SystemExit(f"Input file not found: {in_path}")

  # make lists for each river
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

  print(json.dumps(dams, indent=4))
  # Read the json template
  # Put the rivers into the template
  # Write the output json file    


if __name__ == "__main__":
  main()
