<?PHP
    session_start();
    
    
    
    $pkryid=$_POST['ukryid'];
    $ppilihkryid=$_POST['uidpilihkry'];
    $ppilihdrid=$_POST['uiddokt'];
    $ppilihblnid=$_POST['ubln'];
    $ppilihblnid2=$_POST['ubln2'];
    
    $_SESSION['KSDTKSKRY']=$pkryid;
    $_SESSION['KSDTKSDOK']=$ppilihdrid;
    $_SESSION['KSDTKSBLN01']=$ppilihblnid;
    $_SESSION['KSDTKSBLN02']=$ppilihblnid2;
    
    echo "<input type='hidden' name='cb_kryid' id='cb_kryid' value='$pkryid'>";
    echo "<input type='hidden' name='e_idkaryawanpilih' id='e_idkaryawanpilih' value='$ppilihkryid'>";
    echo "<input type='hidden' name='cb_dokerid' id='cb_dokerid' value='$ppilihdrid'>";
    echo "<input type='hidden' name='bulan1' id='bulan1' value='$ppilihblnid'>";
    echo "<input type='hidden' name='bulan2' id='bulan2' value='$ppilihblnid2'>";
    

?>
    
<script>
    $(document).ready(function() {
        var aksi = "module/ks_isiks/aksi_isiks.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var eidkry=document.getElementById('cb_kryid').value;
        var eidpilihkry=document.getElementById('e_idkaryawanpilih').value;
        var eiddokt=document.getElementById('cb_dokerid').value;
        var ebln=document.getElementById('bulan1').value;
        var ebln2=document.getElementById('bulan2').value;
        
        //alert(eidpilihkry);
        var dataTable = $('#datatableksdr').DataTable( {
            "processing": true,
            "serverSide": true,
            "stateSave": true,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                //{ "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [3,4,5,6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/ks_isiks/mydata_ksisi.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&uidkry="+eidkry+"&uidpilihkry="+eidpilihkry+"&uiddokt="+eiddokt+"&ubln="+ebln+"&ubln2="+ebln2, // json datasource
                type: "post",  // method  , by default get
                data:"uidkry="+eidkry+"&uidpilihkry="+eidpilihkry+"&uiddokt="+eiddokt+"&ubln="+ebln+"&ubln2="+ebln2,
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

<script>
    function ProsesData(ket, noid){

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            if (r==true) {

                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/ks_isiks/aksi_isiks.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+"&ket="+ket+"&id="+noid;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }



    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatableksdr th {
        font-size: 13px;
    }
    #datatableksdr td { 
        font-size: 11px;
    }
</style>

<form method='POST' action='<?PHP echo "?module='saldosuratdana'&act=input&idmenu=149"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <input type='hidden' id='u_module' name='u_module' value='saldosuratdana' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='149' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='hapus' Readonly>
    
    <div class='x_content'>
        <table id='datatableksdr' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='5px'>No</th>
                    <th width='20px'>Bulan</th>
                    <th width='30px'>Produk</th>
                    <th width='30px'>Qty</th>
                    <th width='50px'>Hna</th>
                    <th width='50px'>Value</th>
                    <th width='20px'>CN</th>
                    <th width='20px'>Apotik</th>
                    <th width='20px'></th>
                </tr>
            </thead>
        </table>

    </div>
    
</form>