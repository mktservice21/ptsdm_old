<?PHP
    session_start();
    
    
    $pdivprd=$_POST['udivprod'];
    $ppilihanwewenang=$_POST['uwwnpilihan'];
    
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    echo "<input type='hidden' name='cb_divprod' id='cb_divprod' value='$pdivprd'>";
    echo "<input type='hidden' id='e_wwnpilihan' name='e_wwnpilihan' value='$ppilihanwewenang' Readonly>";
    
?>

<script>
    $(document).ready(function() {
        var aksi = "module/pch_barang/aksi_pchbarang.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var edivprod=document.getElementById('cb_divprod').value;
        var ewwnpilihan=document.getElementById('e_wwnpilihan').value;
        
        var dataTable = $('#datatablegmcbrg').DataTable( {
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            "order": [[ 2, "desc" ], [ 3, "desc" ], [ 2, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": true, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": true, "targets": 4 },
                { className: "text-right", "targets": [5] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4,5,6,7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/pch_barang/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&udivprod="+edivprod+"&uwwnpilihan="+ewwnpilihan, // json datasource
                type: "post",  // method  , by default get
                data:"udivprod="+edivprod+"&uwwnpilihan="+ewwnpilihan,
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
        <table id='datatablegmcbrg' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='50px'></th>
                    <th width='10px'>Kategori</th>
                    <th width='10px'>ID BRG.</th>
                    <th width='200px'>Nama</th>
                    <th width='100px'>Vendor</th>
                    <th width='40px'>Harga</th>
                    <th width='40px'>Status</th>
                    <th width='40px'>Tipe</th>
                </tr>
            </thead>
        </table>

    </div>
    
</form>

<style>
    .divnone {
        display: none;
    }
    #datatablegmcbrg th {
        font-size: 13px;
    }
    #datatablegmcbrg td { 
        font-size: 11px;
    }
</style>