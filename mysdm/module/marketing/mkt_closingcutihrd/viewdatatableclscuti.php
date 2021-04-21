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

    $pkaryawanid=$_SESSION['IDCARD'];
    
    $ptahun=$_POST['utahun'];
    $pnket=$_POST['eket'];
    
    $_SESSION['CLSCUTITHN']=$ptahun;
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    echo "<input type='hidden' name='e_tahun' id='e_tahun' value='$ptahun'>";
    echo "<input type='hidden' name='e_apvpilih' id='e_apvpilih' value='$pnket'>";
    
    
    include "../../../config/koneksimysqli.php";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpcuticlsthn01_".$puserid."_$now ";
    
    $sql = "select a.tahun, a.karyawanid, b.nama, a.id_jenis, a.jabatanid, a.skar, "
            . " a.tglmasuk, a.tglkeluar, a.jml_thn, a.jumlah, a.jml_cuti, a.sisa_cuti FROM "
            . " hrd.karyawan_cuti_close as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid WHERE a.tahun='$ptahun'";
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
    mysqli_close($cnmy);
?>

<script>
    $(document).ready(function() {
        var aksi = "module/marketing/mkt_closingcutihrd/aksi_closingcutihrd.php";
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var nmun = urlku.searchParams.get("nmun");
        var etahun=document.getElementById('e_tahun').value;
        var ket=document.getElementById('e_apvpilih').value;
        
        //alert(eidkry); return false;
        var dataTable = $('#dtablecuticls').DataTable( {
            "processing": true,
            "serverSide": true,
            //"stateSave": true,
            "order": [[ 2, "asc" ], [ 6, "asc" ]],
            "lengthMenu": [[11, 55, 110, 99999999999], [11, 55, 110, "All"]],
            "displayLength": 11,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                //{ className: "text-right", "targets": [7] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true,

            "ajax":{
                url :"module/marketing/mkt_closingcutihrd/mydata_clscuti.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi+"&utahun="+etahun+"&eket="+ket, // json datasource
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
    #dtablecuticls th {
        font-size: 13px;
    }
    #dtablecuticls td { 
        font-size: 11px;
    }
</style>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='dtablecuticls' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='5px'>No</th>
                    <th width='20px'>Karyawan Id</th>
                    <th width='30px'>Nama Karyawan</th>
                    <th width='30px'>Jabatan</th>
                    <th width='50px'>Tgl. Masuk</th>
                    <th width='50px'>Masa Kerja</th>
                    <th width='50px'>Jenis Cuti</th>
                    <th width='50px'>Jumlah</th>
                    <th width='10px'>Cuti</th>
                    <th width='30px'>Sisa Cuti</th>
                </tr>
            </thead>
        </table>

    </div>
    
</form>

