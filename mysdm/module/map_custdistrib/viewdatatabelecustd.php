<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $pidcabang=$_POST['ucabang'];
    $pidarea=$_POST['uarea'];
    $pnmfilter=$_POST['unamafilter'];
    
    
    $_SESSION['MAPCUSTDISIDCAB']=$pidcabang;
    $_SESSION['MAPCUSTDISIDARE']=$pidarea;
    $_SESSION['MAPCUSTDISFILTE']=$pnmfilter;
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    echo "<input type='hidden' name='cb_dist' id='cb_dist' value='$pidcabang'>";
    echo "<input type='hidden' name='cb_ecabang' id='cb_ecabang' value='$pidarea'>";
    echo "<input type='hidden' name='e_namafilter' id='e_namafilter' value='$pnmfilter'>";
    

?>

<script>
    $(document).ready(function() {
        var aksi = "module/map_custdistrib/aksi_custdistrib.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var edist=document.getElementById('cb_dist').value;
        var eecab=document.getElementById('cb_ecabang').value;
        var enamafilter=document.getElementById('e_namafilter').value;
        
        var idisply="10";
        if (eecab!="") {
            idisply="10";
        }
        var dataTable = $('#datatablecustd').DataTable( {
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            //"order": [[ 2, "asc" ], [ 3, "asc" ], [ 4, "asc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": idisply,
            "columnDefs": [
                { "visible": false },
                { "orderable": true, "targets": 0 },
                { "orderable": true, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": true, "targets": 3 },
                { "orderable": true, "targets": 4 },
                //{ className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4,5] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            //"scrollY": 490,
            "scrollX": true,

            "ajax":{
                url :"module/map_custdistrib/mydatacustd.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&udist="+edist+"&uecab="+eecab+"&unamafilter="+enamafilter, // json datasource
                type: "post",  // method  , by default get
                data:"udist="+edist+"&uecab="+eecab,
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
        <table id='datatablecustd' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='50px'>No</th>
                    <th width='100px'>Nama Customer</th>
                    <th width='50px'>Cust.</th>
                    <th width='100px'>Alamat 1</th>
                    <th width='100px'>Alamat 2</th>
                    <th width='50px'>Kota</th>
                </tr>
            </thead>
        </table>

    </div>
    
</form>

<style>
    .divnone {
        display: none;
    }
    #datatablecustd th {
        font-size: 13px;
    }
    #datatablecustd td { 
        font-size: 11px;
    }
</style>

<script>

</script>
