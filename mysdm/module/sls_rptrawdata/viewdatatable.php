<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    session_start();

    $ptgltarikan=$_POST['utgltarik'];
    $pbln1=$_POST['ubln1'];
    $pbln2=$_POST['ubln2'];
    $pidregion=$_POST['uidregi'];
    $pcab=$_POST['uidcab'];
    $puser=$_POST['uuserid'];
    $pidsession=$_POST['uidsesi'];
    
    //$puser=$_SESSION['USERID'];
    //$pidsession=$_SESSION['IDSESI'];
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
?>

<div hidden class='form-group'>
    <label class='control-label col-md-3 col-sm-3 col-xs-8' for=''>Input <span class='required'></span></label>
    <div class='col-md-5'>
        <div class="form-group">
            <input type='text' id='txttgltarik' name='txttgltarik' required='required' class='form-control' value='<?PHP echo $ptgltarikan; ?>' Readonly>
            <input type='text' id='bulan1' name='bulan1' required='required' class='form-control' value='<?PHP echo $pbln1; ?>' Readonly>
            <input type='text' id='bulan2' name='bulan2' required='required' class='form-control' value='<?PHP echo $pbln2; ?>' Readonly>
            <input type='text' id='cbregion' name='cbregion' required='required' class='form-control' value='<?PHP echo $pidregion; ?>' Readonly>
            <input type='text' id='cbcabang' name='cbcabang' required='required' class='form-control' value='<?PHP echo $pcab; ?>' Readonly>
            <input type='text' id='txtuserid' name='txtuserid' required='required' class='form-control' value='<?PHP echo $puser; ?>' Readonly>
            <textarea id="txtidsesi" name="txtidsesi" ><?PHP echo $pidsession; ?></textarea>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        var aksi = "module/sls_rptrawdata/aksi_rptrawdata.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        
        var etgltarik=document.getElementById('txttgltarik').value;
        var ebln1=document.getElementById('bulan1').value;
        var ebln2=document.getElementById('bulan2').value;
        var eidregi=document.getElementById('cbregion').value;
        var eidcab=document.getElementById('cbcabang').value;
        var euserid=document.getElementById('txtuserid').value;
        var eidsesi=document.getElementById('txtidsesi').value;
        
        
        var dataTable = $('#datatablebmsby').DataTable( {
            "processing": true,
            "serverSide": true,
            "fixedHeader": true,
            //"stateSave": true,
            "order": [[ 1, "desc" ], [ 2, "desc" ], [ 3, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { className: "text-right", "targets": [6,7] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6,7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },/*
            "scrollY": 460,
            "scrollX": true,*/

            "ajax":{
                url :"module/sls_rptrawdata/mydatarpt.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&utgltarik="+etgltarik+"&ubln1="+ebln1+"&ubln2="+ebln2+"&uidregi="+eidregi+"&uidcab="+eidcab+"&uuserid="+euserid+"&uidsesi="+eidsesi, // json datasource
                type: "post",  // method  , by default get
                data:"utgltarik="+etgltarik+"&ubln1="+ebln1+"&ubln2="+ebln2+
                        "&uidregi="+eidregi+"&uidcab="+eidcab+"&uuserid="+euserid+"&uidsesi="+eidsesi,
                error: function(){  // error handling
                    $(".data-grid-error").html("");
                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#data-grid_processing").css("display","none");

                }
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
</script>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        <table id='datatablebmsby' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='30px'>Bulan</th>
                    <th width='100px'>Nama Cabang</th>
                    <th width='10px'>Nama Area</th>
                    <th width='100px'>Nama Cust</th>
                    <th width='30px'>Divisi</th>
                    <th width='100px'>Nama Produk</th>
                    <th width='50px'>Qty</th>
                    <th width='50px'>Total</th>
                </tr>
            </thead>
        </table>

    </div>

</form>


<style>
    .divnone {
        display: none;
    }
    #datatablebmsby th {
        font-size: 13px;
    }
    #datatablebmsby td { 
        font-size: 11px;
    }
</style>