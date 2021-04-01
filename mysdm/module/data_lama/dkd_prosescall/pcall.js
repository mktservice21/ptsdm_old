function go_back(n_) {
   window.history.go(n_);
}   

function set_focus(var_) {
   //alert(var_);
   document.getElementById(var_).focus();
}

function upload(pText_)  {
    ok_ = 1;
	if (ok_) {
		var r=confirm(pText_)
		if (r==true) {
			//document.write("You pressed OK!")
			document.getElementById("pcall00").action = "pcall01.php";
			document.getElementById("pcall00").submit();
			return 1;
		}
	} else {
		//document.write("You pressed Cancel!")
		return 0;
	}
}

