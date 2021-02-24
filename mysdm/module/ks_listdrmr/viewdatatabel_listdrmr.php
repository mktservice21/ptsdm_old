<?PHP
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    include "../../config/koneksimysqli.php";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpkslstdoktmr01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpkslstdoktmr02_".$puserid."_$now ";
    
    
    $pnmdoktercari=$_POST['unmdokt'];
    
    $_SESSION['KSLSTDRMR']=$pnmdoktercari;
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    
    $query = "select DISTINCT a.dokterid as dokterid, a.nama as nama_dokter, 
        b.karyawanid as karyawanid, c.nama as nama_karyawan, 
        c.iCabangId as icabangid, d.nama as nama_cabang, c.areaId as areaid, e.nama as nama_area 
        from hrd.dokter as a LEFT JOIN hrd.mrdoktbaru as b on a.dokterid=b.dokterid 
        left join hrd.karyawan as c on b.karyawanId=c.karyawanId 
        left join MKT.icabang as d on c.iCabangId=d.iCabangId 
        LEFT JOIN MKT.iarea as e on c.iCabangId=e.iCabangId and c.areaId=e.areaId
        WHERE ( a.nama like '%$pnmdoktercari%' OR a.dokterid='$pnmdoktercari')";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' 
      id='demo_data2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content'>
        <table id='datatabledrlstmr' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'>DokterId</th>
                    <th width='20px'>Nama Dokter</th>
                    <th width='50px'>Karyawan</th>
                    <th width='60px'>Cabang</th>
                    <th width='40px'>Area</th>
                </tr> 
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by nama_dokter";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $piddokt = $row["dokterid"];
                    $pnmdokt = $row["nama_dokter"];
                    $pidkry = $row["karyawanid"];
                    $pnmkry = $row["nama_karyawan"];
                    $pnmcab = $row["nama_cabang"];
                    $pnmarea = $row["nama_area"];

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$piddokt</td>";
                    echo "<td nowrap>$pnmdokt</td>";
                    echo "<td nowrap>$pnmkry ($pidkry)</td>";
                    echo "<td nowrap>$pnmcab</td>";
                    echo "<td nowrap>$pnmarea</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
</form>

<style>
    .divnone {
        display: none;
    }
    #datatabledrlstmr th {
        font-size: 12px;
    }
    #datatabledrlstmr td { 
        font-size: 11px;
    }
</style>

<script>
    $(document).ready(function() {
        var dataTable = $('#datatabledrlstmr').DataTable( {
            //"stateSave": true,
            fixedHeader: true,
            "ordering": false,
            "processing": true,
            //"order": [[ 0, "asc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": true, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": true, "targets": 4 },
                //{ className: "text-right", "targets": [8, 9] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
</script>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    mysqli_close($cnmy);
?>