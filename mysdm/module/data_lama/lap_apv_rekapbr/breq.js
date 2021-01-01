var EventUtil = new Object;
EventUtil.formatEvent = function (oEvent) {
	return oEvent;
}
function gotoPage(pPage_)
{
  window.location.assign("http:" + pPage_ ) 
  /*document.getElementById(pForm_).action = pPage_
  document.getElementById(pForm_).submit()*/

}

function goto2(pForm_,pPage_) {
   document.getElementById(pForm_).action = pPage_;
   document.getElementById(pForm_).submit();
  
}

function go_back(n_) {
   window.history.back();
}   


function get_dokter() {   
   document.getElementById("set_focus").value = "tanggal";
   document.getElementById("breq00").action = "breq00.php";
   document.getElementById("breq00").submit();
}

function set_focus(var_) {
   //alert(var_);
   document.getElementById(var_).focus();
}

function disp_confirm(pText_)  {
    ok_ = 1;

	if (ok_) {
		var r=confirm(pText_)
		if (r==true) {
			//document.write("You pressed OK!")
			document.getElementById("breq00").action = "breq01.php";
			document.getElementById("breq00").submit();
			return 1;
		}
	} else {
		//document.write("You pressed Cancel!")
		return 0;
	}
}

function disp_confirm1(pText_)  {
    ok_ = 1;

	if (ok_) {
		var r=confirm(pText_)
		if (r==true) {
			//document.write("You pressed OK!")
			document.getElementById("breq40").action = "breq41.php";
			document.getElementById("breq40").submit();
			return 1;
		}
	} else {
		//document.write("You pressed Cancel!")
		return 0;
	}
}

function disp_ks(pText_)  {
	document.getElementById("breq00").action = "ksview2.php";
	document.getElementById("breq00").submit();
	return 1;
}

function click_reset() {
	document.getElementById("breq00").action = "breq00.php";
	document.getElementById("breq00").submit();
}

function simpan_cancel()  {
	//document.getElementById("ikary00").action = "ikary21.php";
	//document.getElementById("ikary00").submit();
	window.history.back();

}
function disp_hapus(pText_)  {
    ok_ = 1;
	if (ok_) {
		var r=confirm(pText_)
		if (r==true) {
			//document.write("You pressed OK!")
			document.getElementById("breq00").action = "breq12.php";
			document.getElementById("breq00").submit();
			return 1;
		}
	} else {
		//document.write("You pressed Cancel!")
		return 0;
	}
}
function hapus_cancel()  {
	//document.getElementById("ikary00").action = "ikary21.php";
	//document.getElementById("ikary00").submit();
	window.history.back();

}


function say_it(num,pDestination) {
	num = num.toString().replace(/\$|\,/g,'');
	if(isNaN(num))
		num = "0";
	document.getElementById(pDestination).value = num;
	document.getElementById("jumlah_").value = num;
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
	if(cents<10)
		cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+','+
		num.substring(num.length-(4*i+3));
	ret_ =  (((sign)?'':'-') + num + '.' + cents);
	return ret_;
}
function allowChars(oTextbox, oEvent) {
    return true;
	if(window.event) { // IE
		keynum = oEvent.keyCode;
	} else if(oEvent.which) { // Netscape/Firefox/Opera
		keynum = oEvent.which;
	}
    //alert(keynum); 
	keynum = oEvent.keyCode;
	oEvent = EventUtil.formatEvent(oEvent);
	var sValidChars = oTextbox.getAttribute("validchars");
	var sChar = String.fromCharCode(oEvent.charCode); 
	var bIsValidChar = sValidChars.indexOf(sChar) > -1;
    var mystr = oTextbox.value;
    var len_ = mystr.length;
	if (keynum==8 || keynum==37) {  //8=backspace 37=left arrow
	   mystr = mystr.substr(0,len_-1);
	   oTextbox.value = mystr;
	}
	if (keynum==36) {  //36=home
		oTextbox.value = "";
	}
	return bIsValidChar || oEvent.ctrlKey || keynum==9  || keynum==40;   //9=tab, 40=downarrow
	
	
}

