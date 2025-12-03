#!/bin/env python3

import argparse
import csv
import json
from pathlib import Path
from typing import Any, Dict, Iterable

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


def detect_group_key(fieldnames: Iterable[str]) -> str | None:
  # common grouping keys in schedules
  candidates = ("section", "section_id", "section name", "section_name", "group", "schedule", "name")
  lowered = {fn.lower(): fn for fn in fieldnames}
  for c in candidates:
    if c in lowered:
      return lowered[c]
  return None


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

  print("Reading from:", in_path)
  with in_path.open("r", encoding="utf-8-sig", newline="") as csvFile:
    reader = csv.DictReader(csvFile)
    fieldnames = reader.fieldnames
    print("Field names:", fieldnames)
    for row in reader:
      print(row['Location Description'] + " " + row['Release Start Date'] + " " + row['Release End Date'])


if __name__ == "__main__":
  main()
