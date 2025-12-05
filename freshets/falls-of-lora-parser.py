#!/usr/bin/env python3

import json
from enum import Enum

# Find daylight Ebb flows above 3.2m range
class FallsOfLoraParser:

    class ParsingState(Enum):
        PARSING_DATE = "parsing_date"
        PARSING_SUNUPTIME = "parsing_sunuptime"
        PARSING_SUNDOWNTIME = "parsing_sundowntime"
        PARSING_TIDE1 = "parsing_tide1"
        PARSING_TIDE2 = "parsing_tide2"
        PARSING_TIDE3 = "parsing_tide3"
        PARSING_TIDE4 = "parsing_tide4"
        PARSING_TIDE5 = "parsing_tide5"
        PARSING_TIDE6 = "parsing_tide6"
        PARSING_TIDE7 = "parsing_tide7"


    def __init__(self, path):
        self.path = path
        self.flowDates = []

    def getFallsofLoraDates(self):
        state = self.ParsingState.PARSING_DATE

        with open(self.path, 'r', encoding='utf-8') as file:
            for line in file:
                line = line.strip()
                #print(f"Line: {line}, State: {state}")
                if '<td class="date">' in line:
                    state = self.ParsingState.PARSING_DATE
                    date = line.split('<td class="date">')[1].split('</td>')[0]
                    print(f"XXX Date {date} {state}")
                elif '<td class="suntime">' in line and state == self.ParsingState.PARSING_DATE:
                    state = self.ParsingState.PARSING_SUNUPTIME
                    sunuptime = line.split('<td class="suntime">')[1].split('</td>')[0]
                    print(f"XXX Sunup {sunuptime} {state}")
                elif '<td class="suntime">' in line and state == self.ParsingState.PARSING_SUNUPTIME:
                    state = self.ParsingState.PARSING_SUNDOWNTIME
                    sundowntime = line.split('<td class="suntime">')[1].split('</td>')[0]
                    print(f"XXX Sundown {sundowntime} {state}")
                elif '<td class="data">' in line and (state == self.ParsingState.PARSING_SUNDOWNTIME or state == self.ParsingState.PARSING_TIDE7):
                    state = self.ParsingState.PARSING_TIDE1
                    tideTime = line.split('<td class="data">')[1].split('</td>')[0]
                    print(f"XXX tideTime {tideTime} {state}")
                elif state == self.ParsingState.PARSING_TIDE1:
                    state = self.ParsingState.PARSING_TIDE2
                    tideHeight = line.split('<td class="data">')[1].split('</td>')[0]
                    print(f"XXX tideHeight {tideHeight} {state}")
                elif state == self.ParsingState.PARSING_TIDE2:
                    state = self.ParsingState.PARSING_TIDE3
                    tideRange = line.split('<td class="data">')[1].split('</td>')[0]
                    print(f"XXX tideRange {tideRange} {state}")
                elif state == self.ParsingState.PARSING_TIDE3:
                    state = self.ParsingState.PARSING_TIDE4
                    tideType = line.split('">')[1].split('</td>')[0]
                    print(f"XXX tideType {tideType} {state}")
                elif state == self.ParsingState.PARSING_TIDE4:
                    state = self.ParsingState.PARSING_TIDE5
                    tideFlowStarts = line.split('">')[1].split('</td>')[0]
                    print(f"XXX tideFlowStarts {tideFlowStarts} {state}")
                elif state == self.ParsingState.PARSING_TIDE5:
                    state = self.ParsingState.PARSING_TIDE6
                    tideGoodFrom = line.split('">')[1].split('</td>')[0]
                    print(f"XXX tideGoodFrom {tideGoodFrom} {state}")
                elif state == self.ParsingState.PARSING_TIDE6:
                    state = self.ParsingState.PARSING_TIDE7
                    tideGoodTo = line.split('">')[1].split('</td>')[0]
                    print(f"XXX tideGoodTo{tideGoodTo} {state}")
                    self.isTideRunnable(date, sunuptime, sundowntime, tideTime, tideHeight, tideRange, tideType, tideFlowStarts, tideGoodFrom, tideGoodTo)
                else:
                    print(f"Ignoring line in state {state}: {line}")

        return self.flowDates

    def isTideRunnable(self, date, sunuptime, sundowntime, tideTime, tideHeight, tideRange, tideType, tideFlowStarts, tideGoodFrom, tideGoodTo):
        print(f"isTideRunnable() Date: {date}, Sunup: {sunuptime}, Sundown: {sundowntime}, Tide Time: {tideTime}, Height: {tideHeight}, Range: {tideRange}, Type: {tideType}, Flow Starts: {tideFlowStarts}, Good From: {tideGoodFrom}, Good To: {tideGoodTo}")
        self.flowDates.append(date)

if __name__ == "__main__":
  parser = FallsOfLoraParser("Tide Tables – The Falls Of Lora Information Site.html")
  dates = parser.getFallsofLoraDates()
  print(dates)
  json.dumps(dates, indent=2)
