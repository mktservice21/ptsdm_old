var xmlHttp

function showMR(str)
{ 
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
var url="breq07.php";
var distid = document.getElementById("icabangid").value;  
url=url+"?karyawanid=" +str+ "&icabangid="+distid; 
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange=Statechanged;
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
}

function Statechanged() 
{ 
if (xmlHttp.readyState==4)
{ 
document.getElementById("mr_id").innerHTML=xmlHttp.responseText;
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
