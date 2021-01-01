var xmlHttp

function showKode(str)
{ 
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
var url="breq09.php";
url=url+"?divprodid="+str;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange=statechanged;
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
}

function statechanged() 
{ 
if (xmlHttp.readyState==4)
{ 
document.getElementById("kodeid").innerHTML=xmlHttp.responseText;
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



