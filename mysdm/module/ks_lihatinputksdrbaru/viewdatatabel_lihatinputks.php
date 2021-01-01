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
    $tmp01 =" dbtemp.tmpkslhtinptnewdr01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpkslhtinptnewdr02_".$puserid."_$now ";
    
    
    $pnmdoktercari=$_POST['unmdokt'];
    
    $_SESSION['KSLSTDRNEW']=$pnmdoktercari;
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    
    $query = "select a.bulan, a.dokterid, b.nama as nama_dokter, a.srid, c.nama as nama_karyawan, a.notes 
        from hrd.ks1_buka as a JOIN hrd.dokter as b on a.dokterid=b.dokterId 
        left join hrd.karyawan as c on a.srid=c.karyawanId
        WHERE ( b.nama like '%$pnmdoktercari%' OR a.dokterid='$pnmdoktercari')";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select distinct a.bulan, a.dokterid, a.nama_dokter, a.srid, a.nama_karyawan, a.notes, min(b.bulan) as bulanks "
            . " FROM $tmp01 as a LEFT JOIN hrd.ks1 as b on a.dokterid=b.dokterid and a.srid=b.srid";
    $query .= " GROUP BY 1,2,3,4,5,6";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
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
                    <th width='60px'>Bulan Dibuka</th>
                    <th width='40px'>Bulan Input KS</th>
                </tr> 
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp02 order by nama_dokter, nama_karyawan";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $piddokt = $row["dokterid"];
                    $pnmdokt = $row["nama_dokter"];
                    $pidkry = $row["srid"];
                    $pnmkry = $row["nama_karyawan"];
                    $pblnbuka = $row["bulan"];
                    $pblnks = $row["bulanks"];
                    
                    $plihatks="<a class='btn btn-info btn-xs' href='eksekusi3.php?module=lihatdataksusr&ket=bukan&iid=$pidkry&ind=$piddokt' target='_blank'>$pnmdokt</a>";
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$piddokt</td>";
                    echo "<td nowrap>$plihatks</td>";
                    echo "<td nowrap>$pnmkry ($pidkry)</td>";
                    echo "<td nowrap>$pblnbuka</td>";
                    echo "<td nowrap>$pblnks</td>";
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