var xmlHttp

function showCN(str)
{ 
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
var url="breq16.php";
var distid = document.getElementById("mr_id").value;  
url=url+"?dokterid=" +str+ "&mr_id="+distid;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange=STATECHANGED;
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
}

function STATECHANGED() 
{ 
if (xmlHttp.readyState==4)
{ 
document.getElementById("cn").innerHTML=xmlHttp.responseText;
}
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}
