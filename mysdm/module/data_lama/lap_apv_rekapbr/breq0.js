var xmlHttp

function showDokter(str)
{ 
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
var url="breq05.php";
var distid = document.getElementById("icabangid").value;  
url=url+"?mr_id=" +str+ "&icabangid="+distid;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange=StateChanged;
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
}

function StateChanged() 
{ 
if (xmlHttp.readyState==4)
{ 
document.getElementById("dokterid").innerHTML=xmlHttp.responseText;
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
