var _mouseX, _mouseY;

function _hide_help()
{
	if(! document.getElementById('helpid')) return;
	var _alertobj = document.getElementById('helpid');

	_alertobj.style.display = 'none';
}

function annulerConfirm(gid, uid)
{

	var width = 300;
	var height = 150;
	var text = '';
	if(! document.getElementById('overlayid'))
     return;
	
    var _alertobj = document.getElementById('overlayid');

	text = '<table width="300" align="center" cellpadding="0" cellspacing="0">';
	text += '<tr><td colspan="2" height="100" valign="middle" align="center"><b>Est-ce que vous acceptez une nulle ?</b></td></tr>';
	text += '<tr><td width="50%" align="center" height="50" valign="middle"><a href="?action=traiter abandon&gid='+gid+'">Oui</a></td>';
	text += '<td width="50%" align="center" height="50" valign="middle"><a href="?action=montrer partie&gid='+gid+'&nulle=0">Non</a></td></tr>';
	text += '</table>';

	_alertobj.innerHTML = text;
	_alertobj.style.width = width;
	_alertobj.style.height = height;
	_alertobj.style.left = 250;
	_alertobj.style.top = 250;
	_alertobj.style.display = 'block';
    
}

function essai($piece)
{
    var w = screen.width;
    var h = screen.height;

    
    top.document.location = "index.php?action=montrer partie&piece="+piece+"&w="+w+"&h="+h; 
    
}
function impressioncoups()
{
    coupsWindow = window.open('coups.php','coupsWindows','height=200, width=500, top=100, left=300, toolbar=no, menubar=no, location=no, resizable=yes, scrollbars=yes, status=no');
}

function _toggle_help(title, text)
{
	if(! document.getElementById('helpid')) return;
	var _alertobj = document.getElementById('helpid');

	if(_alertobj.style.display == 'block') {_alertobj.style.display = 'none'; return;}

	var output = '<table width="305" cellpadding="0" cellspacing="0">';
	output += '<tr style="background-image: url(\'images/bg/bulle_tl_l1.gif\');"><td align="center" height="45" valign="middle">&nbsp;</td></tr>';
	output += '<tr style="background-image: url(\'images/bg/bulle_tl_l2.gif\');"><td align="center" height="30" valign="middle"><b>'+title+'</b></td></tr>';
	output += '<tr style="background-image: url(\'images/bg/bulle_tl_l3.gif\'); background-repeat:repeat-y;"><td align="left" valign="top" style="padding-left: 15px; padding-right: 15px;"><font class="small">'+text+'</font></td></tr>';
	output += '<tr style="background-image: url(\'images/bg/bulle_tl_l4.gif\');"><td align="center" height="40" valign="middle"><a href="javascript:" onclick="_hide_help(); return(false);">Fermer</a></td></tr>';
	output += '</table>';

	var left = _mouseX;
	var top = _mouseY;

	_alertobj.innerHTML = output;
	_alertobj.style.left = left;
	_alertobj.style.top = top;
	_alertobj.style.display = 'block';

}

function getMouse(e)
{
    var x,y;
    var elt = document.documentElement;
    
    if ( document.captureEvents ) {
        x = e.pageX;
        y = e.pageY;
    } else if ( window.event.clientX ) {
        x = window.event.clientX+elt.scrollLeft;
        y = window.event.clientY+elt.scrollTop;
    }
    _mouseX = x;
    _mouseY = y;
}

