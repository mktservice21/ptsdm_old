<?PHP
    session_start();
    
    
    $pdivprd=$_POST['udivprod'];
    $ppilihanwewenang=$_POST['uwwnpilihan'];
    $pbln=$_POST['ubulan'];
    
    $pbulan= date("Ym", strtotime($pbln));
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    echo "<input type='hidden' name='cb_divprod' id='cb_divprod' value='$pdivprd'>";
    echo "<input type='hidden' id='e_wwnpilihan' name='e_wwnpilihan' value='$ppilihanwewenang' Readonly>";
    echo "<input type='hidden' name='xbulan' id='xbulan' value='$pbln'>";
    
?>

<script>
    $(document).ready(function() {
        var aksi = "module/mod_brg_terimabrg/aksi_terimabrg.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var edivprod=document.getElementById('cb_divprod').value;
        var ewwnpilihan=document.getElementById('e_wwnpilihan').value;
        var ebulan = document.getElementById("xbulan").value;
        
        var dataTable = $('#datatablegmcbrg').DataTable( {
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            "order": [[ 1, "desc" ], [ 2, "desc" ], [ 3, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": true, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": true, "targets": 4 },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/mod_brg_terimabrg/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&udivprod="+edivprod+"&uwwnpilihan="+ewwnpilihan+"&ubulan="+ebulan, // json datasource
                type: "post",  // method  , by default get
                data:"udivprod="+edivprod+"&uwwnpilihan="+ewwnpilihan+"&ubulan="+ebulan,
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
                    <th width='10px'>ID</th>
                    <th width='10px'>TANGGAL</th>
                    <th width='10px'>GRP. PRODUK</th>
                    <th width='10px'>PENERIMA</th>
                    <th width='200px'>SUPPLIER</th>
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


