function getDataKaryawan(data1, data2, icabang){
    var cabang =document.getElementById(icabang).value;
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewkaryawan&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&uicabang="+cabang+"&fldcab="+icabang,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalKaryawan(fildnya1, fildnya2, d1, d2, icabang){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
    document.getElementById('e_idcabang').value="";
    document.getElementById('e_cabang').value="";
}

function getDataCabangFmr(data1, data2, imr){
    var emr=document.getElementById(imr).value;
    if (emr=="") return 0;
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewdatacabangfmr&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&umr="+emr,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalCabang(fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
}

function getDataSubPosting(onklik, data1, data2){
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewdatasubposting&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&uonklik="+onklik,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalSubPosting(onklik, fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
    if (onklik!=""){
        var kodesub = document.getElementById(fildnya1).value;
        getDataComboPosting(onklik, kodesub);
    }
}
function getDataComboPosting(onklik, kodesub){
    //alert(kodesub); return false;
    $.ajax({
        type:"post",
        url:"module/mod_br_entrynon/viewdata.php?module=viewdatacomboposting&data1="+onklik+"&data2="+kodesub,
        data:"uonklik="+onklik+"&ukodesub="+kodesub,
        success:function(data){
            $("#"+onklik).html(data);
        }
    });
}
function getDataDokterMRCabang(data1, data2, icab, imr){
    var ecab = document.getElementById(icab).value;
    var emr = document.getElementById(imr).value;
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewdatadoktermrcabang&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&ucab="+ecab+"&umr="+emr,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalDokter(fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
}

function showKodeNyaNon(divisi, kodeid, coa){
    var ediv = document.getElementById(divisi).value;
    var ecoa = document.getElementById(coa).value;
    
    $.ajax({
        type:"post",
        url:"module/mod_br_entrynon/viewdata.php?module=viewdatacombokodenon&data1="+ediv+"&data2="+kodeid,
        data:"udiv="+ediv+"&ukodeid="+kodeid+"&ucoa="+ecoa,
        success:function(data){
            $("#"+kodeid).html(data);
        }
    });
}

function showCOANya(divisi, coa){
    var ediv = document.getElementById(divisi).value;
    $.ajax({
        type:"post",
        url:"module/mod_br_entrynon/viewdata.php?module=viewdatacombocoa&data1="+ediv+"&data2="+coa,
        data:"udiv="+ediv+"&ucoa="+coa,
        success:function(data){
            $("#"+coa).html(data);
            var apilih = "<option value=''>-- Pilihan --</option>";
            $("#cb_kode").html(apilih);
        }
    });
}