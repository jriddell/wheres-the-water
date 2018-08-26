var picWindow;
var linkWindow;

function showPicWin(url)
{
	picWindow=window.open(url,'picwin','height=800,width=1000,resizable=yes,scrollbars=yes');
	if(window.focus)
	{
		picWindow.focus();
	}
}

function showLinkWin(url)
{
	linkWindow=window.open(url,'linkwin');
	if(window.focus)
	{
		linkWindow.focus();
	}
}
