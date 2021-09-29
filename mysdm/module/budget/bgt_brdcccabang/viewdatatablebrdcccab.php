<?PHP
    session_start();
    
    $fgroupid=$_SESSION['GROUP'];
    $pcabangid=$_POST['ucabid'];
    $pfiltercabang=$_POST['utxtcabid'];
    $_SESSION['DCCCABCAB']=$pcabangid;
    
    echo "<input type='hidden' name='cb_cabang' id='cb_cabang' value='$pcabangid'>";
    echo "<span hidden><textarea id='txt_cabang' name='txt_cabang'>$pfiltercabang></textarea></span>";
    

?>
    
<script>
    $(document).ready(function() {
        var aksi = "module/budget/bgt_brdcccabang/aksi_brdcccabang.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var ecabid=document.getElementById('cb_cabang').value;
        var etxtcabid=document.getElementById('txt_cabang').value;
        
        //alert(aksi); return false;
        var dataTable = $('#dtabelmstdr').DataTable( {
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
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/budget/bgt_brdcccabang/mydata_brdcccab.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&ucabid="+ecabid+"&utxtcabid="+etxtcabid, // json datasource
                type: "post",  // method  , by default get
                //data:"ucabid="+ecabid,
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
    
</script>

<style>
    .divnone {
        display: none;
    }
    #dtabelmstdr th {
        font-size: 13px;
    }
    #dtabelmstdr td { 
        font-size: 11px;
    }
</style>

<form method='POST' action='<?PHP echo "?module='dkdmasterdokt'&act=input&idmenu=476"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <input type='hidden' id='u_module' name='u_module' value='saldosuratdana' Readonly>
    <input type='hidden' id='u_idmenu' name='u_idmenu' value='149' Readonly>
    <input type='hidden' id='u_act' name='u_act' value='hapus' Readonly>
    
    <div class='x_content'>
        <table id='dtabelmstdr' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='5px'>No</th>
                    <th width='50px'></th>
                    <th width='20px'>ID</th>
                    <th width='20px'>Tanggal</th>
                    <th width='30px'>Jenis</th>
                    <th width='30px'>User</th>
                    <th width='50px'>Yg. Membuat</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Keterangan</th>
                </tr>
            </thead>
        </table>

    </div>
    
</form>