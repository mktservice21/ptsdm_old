<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $_SESSION['SPDKDKARYA']=$_POST['ukryid'];
    $_SESSION['SPDKDTGL01']=$_POST['uperiode1'];
    $_SESSION['SPDKDTGL02']=$_POST['uperiode2'];
    
    $pkryid=$_POST['ukryid'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-d", strtotime($date1));
    $tgl2= date("Y-m-d", strtotime($date2));
    
    $paksi=$_POST['uaksi'];
    
    echo "<input type='hidden' name='cb_karyawan' id='cb_karyawan' value='$pkryid'>";
    echo "<input type='hidden' name='e_tgl1' id='e_tgl1' value='$tgl1'>";
    echo "<input type='hidden' name='e_tgl2' id='e_tgl2' value='$tgl2'>";
    echo "<input type='hidden' name='e_aksi' id='e_aksi' value='$paksi'>";
    
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pact="input";
    
    
?>


<script>
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var ekryid=document.getElementById('cb_karyawan').value;
        var etgl1 = document.getElementById("e_tgl1").value;
        var etgl2 = document.getElementById("e_tgl2").value;
        var eaksi = document.getElementById("e_aksi").value;
        
        //alert(etgl1);
        var dataTable = $('#datatablespd').DataTable( {
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
                { className: "text-right", "targets": [7,8,9] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/budget/mod_br_spdklaimdisc/mydatapdkd.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+eaksi+"&ukryid="+ekryid+"&uperiode1="+etgl1+"&uperiode2="+etgl2, // json datasource
                type: "post",  // method  , by default get
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

<style>
    .divnone {
        display: none;
    }
    #datatablespd th {
        font-size: 13px;
    }
    #datatablespd td { 
        font-size: 11px;
    }
</style>


<form method='POST' action='<?PHP echo "?module=$pmodule&act=$pact&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content'>
        <table id='datatablespd' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='50px'></th>
                    <th width='40px'>Divisi</th>
                    <th width='50px'>Jenis</th>
                    <th width='80px'>No. SPD</th>
                    <th width='40px'>Tanggal</th>
                    <th width='50px'>No. BR</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Adjustment</th>
                    <th width='50px'>Jml.Transfer</th>
                </tr>
            </thead>
        </table>

    </div>
</form>

