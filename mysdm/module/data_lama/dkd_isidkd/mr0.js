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

function go_back(n_) {
   window.history.back();
}   


function get_dokter() {   
   document.getElementById("set_focus").value = "tanggal";
   document.getElementById("mr0").action = "mr0.php";
   document.getElementById("mr0").submit();
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
			document.getElementById("mr0").action = "mr1.php";
			document.getElementById("mr0").submit();
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
	document.getElementById("mr0").action = "ksview2.php";
	document.getElementById("mr0").submit();
	return 1;
}

function click_reset() {
	document.getElementById("mr0").action = "mr0.php";
	document.getElementById("mr0").submit();
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
			document.getElementById("mr0").action = "mr7.php";
			document.getElementById("mr0").submit();
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
