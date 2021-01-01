var EventUtil = new Object;
EventUtil.formatEvent = function (oEvent) {
	return oEvent;
}


function goto2(pForm_,pPage_) {
   document.getElementById(pForm_).action = pPage_;
   document.getElementById(pForm_).submit();
  
}
