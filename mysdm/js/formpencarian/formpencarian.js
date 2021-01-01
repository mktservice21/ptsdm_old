function cariFormData(str, idnya, myDivForm, cModule, cBank, cCab, cRek, cId){
    $("#"+str).keyup(function(){
        $.ajax({
        type: "POST",
        url: "js/formpencarian/formsearch.php?module="+cModule+"&myidform="+str+"&idnya="+idnya+"&myDivForm="+myDivForm+"&myBank="+cBank+"&myCab="+cCab+"&myRek="+cRek+"&myId="+cId,
        data:'keyword='+$(this).val(),
        beforeSend: function(){
                $("#"+str).css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
        },
        success: function(data){
                $("#"+myDivForm).show();
                $("#"+myDivForm).html(data);
                $("#"+str).css("background","#FFF");
        }
        });
    });
}

function selectDataFormSearch(val) {
    var nmid = val.split("|");
    $("#"+nmid[2]).hide();
    
    $("#e_idbank").val(nmid[3]);
    $("#e_kdrealisasi").val(nmid[4]);
    $("#e_realisasi").val(nmid[5]);
    
    $("#e_bank").val(nmid[6]);
    $("#e_cabbank").val(nmid[7]);
    $("#e_norekrel").val(nmid[8]);
    
    $("#e_realisasi").focus();
}

function selectDataFormSearchxxx(val) {
    var nmid = val.split("|");
    $("#"+nmid[0]).val(nmid[3]);
    $("#"+nmid[1]).val(nmid[4]);
    $("#"+nmid[8]).val(nmid[5]);
    $("#"+nmid[9]).val(nmid[6]);
    $("#"+nmid[10]).val(nmid[7]);
    
    $("#"+nmid[11]).val(nmid[12]);
    
    $("#"+nmid[2]).hide();
    
    $("#"+nmid[8]).focus();
}

function HideDataFormSearch(val) {
    var nmid = val.split("|");
    $("#"+nmid[1]).val(nmid[4]);
    $("#"+nmid[2]).hide();
    /*
    document.getElementById(myDivForm).innerHTML="";
    return;
    */
}