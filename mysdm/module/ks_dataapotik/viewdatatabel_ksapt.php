<?PHP
    session_start();
    
    
    $_SESSION['KSDTAPTKRY']=$_POST['ukryid'];
    
    
    $pkryid=$_POST['ukryid'];
    $ppilihkryid=$_POST['uidpilihkry'];
    
    echo "<input type='hidden' name='cb_kryid' id='cb_kryid' value='$pkryid'>";
    echo "<input type='hidden' name='e_idkaryawanpilih' id='e_idkaryawanpilih' value='$ppilihkryid'>";
    

?>
    
<script>
    $(document).ready(function() {
        var aksi = "module/ks_dataapotik/aksi_dataapotik.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var eidkry=document.getElementById('cb_kryid').value;
        var eidpilihkry=document.getElementById('e_idkaryawanpilih').value;
        
        //alert(eidpilihkry);
        var dataTable = $('#datatablekasbon').DataTable( {
            "processing": true,
            "serverSide": true,
            "stateSave": true,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                //{ className: "text-right", "targets": [7] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/ks_dataapotik/mydata_ksapt.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&uidkry="+eidkry+"&uidpilihkry="+eidpilihkry, // json datasource
                type: "post",  // method  , by default get
                data:"uidkry="+eidkry+"&uidpilihkry="+eidpilihkry,
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
                document.getElementById("d-form2").action = "module/ks_dataapotik/aksi_dataapotik.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+"&ket="+ket+"&id="+noid;
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
    #datatablekasbon th {
        font-size: 13px;
    }
    #datatablekasbon td { 
        font-size: 11px;
    }
</style>

<form method='POST' action='<?PHP echo "?module='saldosuratdana'&act=input&idmenu=149"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <input type='hidden' id='u_module' name='u_module' value='saldosuratdana' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='149' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='hapus' Readonly>
    
    <div class='x_content'>
        <table id='datatablekasbon' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='5px'>No</th>
                    <th width='50px'></th>
                    <th width='20px'>ID</th>
                    <th width='30px'>Nama Apotik</th>
                    <th width='30px'>Alamat 1</th>
                    <th width='50px'>Alamat 2</th>
                    <th width='50px'>Kota</th>
                    <th width='10px'>Aktif</th>
                    <th width='30px'>Karyawan</th>
                </tr>
            </thead>
        </table>

    </div>
    
</form>