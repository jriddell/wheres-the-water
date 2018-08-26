	function switchOff( cellName )
	{
		var cell = document.getElementById( cellName );
		cell.style.background="#CCCCCC";
	}
	
	function switchOn( cellName )
	{
		var cell = document.getElementById( cellName );
		cell.style.background="#FFFFFF";
	}
	
	function changeText(elemId, newText)
	{
		var newTxtElem=document.createTextNode(newText);
		var elemToChange=document.getElementById(elemId);
		
		elemToChange.replaceChild(newTxtElem, elemToChange.firstChild);
	}
	
	function showHeadlineSummary(resortName, summary)
	{			
		changeText('headlineSummary',resortName + ' - ' + summary);		
	}
	
	function showSectionInfo(name, level, lastReadingDate, reading, trend)
	{
		changeText('sectionname',name);
		
		changeText('level', level);
		
		changeText('lastUpdated', lastReadingDate);
				
		if (level == "NO_GUAGE_DATA")
		{ 
			changeText('level', '-');
			changeText('lastUpdated','No recent data');
		}
		
		if (level == "OLD_DATA")
		{ 
			changeText('level', 'Data out of date!');
		}
		
		if (level == "CONVERSION_UNKNOWN")
		{ 
			changeText('level', 'Uncallibrated');
		}
				
		if ( reading == -1 )
		{
			changeText('currentReading', ' - ');
		}
		else
		{
			changeText('currentReading', reading);
		}
		
		if (trend == "TREND_UNKNOWN") { changeText('trend', ' - '); }
		if (trend == "RISING") { changeText('trend', 'Goin\' Up!'); }
		if (trend == "FALLING") { changeText('trend', 'Goin\' Down'); }
		if (trend == "STABLE") { changeText('trend', 'Steady'); }
		
	}
	
	function showConversionInfo(level, scrape, low, medium, high, veryhigh, flood)
	{
		if ( low == 0 )
		{
			changeText('empty', 'never');
			changeText('justRunnable', 'never');
		}
		else if ( scrape == 0 )
		{
			changeText('empty', 'never');
			changeText('justRunnable', scrape + ' - ' + low);
		}
		else
		{
			changeText('empty', '< ' + scrape);
			changeText('justRunnable', scrape + ' - ' + low);
		}
		
		changeText('low',low + ' - ' + medium);
		
		
		changeText('medium',medium + ' - ' + high);
		changeText('high',high + ' - ' + veryhigh);
		changeText('veryHigh',veryhigh + ' - ' + flood);
		changeText('huge','> ' + flood);
		
		if (level == "EMPTY") { switchOn('empty'); } else { switchOff('empty'); }
		if (level == "SCRAPE") { switchOn('justRunnable'); } else { switchOff('justRunnable'); }
		if (level == "LOW") { switchOn('low'); } else { switchOff('low'); }
		if (level == "MEDIUM") { switchOn('medium'); } else { switchOff('medium'); }
		if (level == "HIGH") { switchOn('high'); } else { switchOff('high'); }
		if (level == "VERY_HIGH") { switchOn('veryHigh'); } else { switchOff('veryHigh'); }
		if (level == "FLOOD") { switchOn('huge'); } else { switchOff('huge'); }
	}