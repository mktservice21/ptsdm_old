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

    $ptgl1=$_POST['utgl1'];
    $ptgl2=$_POST['utgl2'];
    $pkryid=$_POST['ukryid'];
    $pcabid=$_POST['ucabid'];
    $pjbtidpl=$_POST['ujbtid'];


    $_SESSION['WEKPLNCAB']=$pcabid;
    $_SESSION['WEKPLNJBT']=$pjbtidpl;
    $_SESSION['WEKPLNKRY']=$pkryid;
    $_SESSION['WEKPLNTGL']=$ptgl1;

    $bukanuser=false;
    if ($pkaryawanid==$pkryid) $bukanuser=true;

    $ptgl1 = date('Y-m-d', strtotime($ptgl1));
    //$ptgl2 = date('Y-m-d', strtotime($ptgl2));
    $ptgl2 = date('Y-m-d', strtotime('+4 days', strtotime($ptgl1)));

    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_ubahget_id.php";

    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpttdvstpln01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpttdvstpln02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpttdvstpln03_".$puserid."_$now ";
    
    
    $sql = "select a.nourut, a.karyawanid, a.tanggal, a.dokterid, b.namalengkap as nama_dokter, a.jenis FROM hrd.dkd_new1 as a "
            . " JOIN dr.masterdokter as b on a.dokterid=b.id "
            . " WHERE a.karyawanid='$pkryid'";
    $sql .=" AND a.tanggal = '$ptgl1'";
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "alter table $tmp01 add column sdhreal varchar(1), add column ttd varchar(1) DEFAULT 'N', add l_latitude varchar(200), add l_longitude varchar(200)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $sql = "select a.nourut, a.karyawanid, a.tanggal, a.dokterid, b.namalengkap as nama_dokter, a.jenis, a.ttd, a.l_latitude, a.l_longitude FROM hrd.dkd_new_real1 as a "
            . " JOIN dr.masterdokter as b on a.dokterid=b.id "
            . " WHERE a.karyawanid='$pkryid'";
    $sql .=" AND a.tanggal = '$ptgl1'";
    $query = "create TEMPORARY table $tmp02 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $sql = "select * from $tmp01";
    $query = "create TEMPORARY table $tmp03 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp01 (nourut, karyawanid, tanggal, dokterid, nama_dokter, jenis, ttd, l_latitude, l_longitude) "
            . " SELECT nourut, karyawanid, tanggal, dokterid, nama_dokter, jenis, ttd, l_latitude, l_longitude FROM $tmp02 WHERE dokterid NOT IN "
            . " (select distinct IFNULL(dokterid,'') FROM $tmp03)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp01 as a JOIN $tmp02 as b on a.karyawanid=b.karyawanid AND "
            . " a.dokterid=b.dokterid AND a.tanggal=b.tanggal SET a.sdhreal='Y', a.ttd=b.ttd, a.l_latitude=b.l_latitude, a.l_longitude=b.l_longitude";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $bulan_array=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
        "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember");

    $hari_array = array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );
    
?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
    id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='50px'>No</th>
                    <th width='100px'>Tanggal</th>
                    <th width='50px'>User</th>
                    <th width='50px'>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
            $no=1;
            $query = "select * from $tmp01 order by tanggal, nama_dokter";
            $tampil0=mysqli_query($cnmy, $query);
            while ($row0=mysqli_fetch_array($tampil0)) {
                $cidinput=$row0['nourut'];
                $nkaryawanid=$row0['karyawanid'];
                $ntgl=$row0['tanggal'];
                $ndoktid=$row0['dokterid'];
                $ndoktnm=$row0['nama_dokter'];
                $nfromttd=$row0['ttd'];
                $nsudahreal=$row0['sdhreal'];
                
                $nlat=$row0['l_latitude'];
                $nlong=$row0['l_longitude'];
                
                $pidget=encodeString($cidinput);

                $ntanggal = date('l d F Y', strtotime($ntgl));

                $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
                $xtgl= date('d', strtotime($ntgl));
                $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
                $xthn= date('Y', strtotime($ntgl));
                
                $pttd="<a class='btn btn-info btn-xs' href='?module=$pmodule&act=ttdvisit&idmenu=$pidmenu&nmun=$pidmenu&id=$pidget&nid=$ntgl'>Tanda Tangan</a>";
                $plihatview = "<a class='btn btn-success btn-xs' href='eksekusi3.php?module=viewmapttddkd&ket=bukan&ilat=$nlat&ilong=$nlong' target='_blank'>View Map</a>";
                
                if ($nsudahreal=="Y") {
                    $pttd="sudah realisasi";
                    if ($nfromttd=="Y") {
                        $pttd = $plihatview;
                    }
                }
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$xhari, $xtgl $xbulan $xthn</td>";
                echo "<td nowrap>$ndoktnm</td>";
                echo "<td nowrap>$pttd</td>";
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
        
        .form-group, .input-group, .control-label {
            margin-bottom:2px;
        }
        .control-label {
            font-size:11px;
        }
        #datatable input[type=text], #tabelnobr input[type=text] {
            box-sizing: border-box;
            color:#000;
            font-size:11px;
            height: 25px;
        }
        select.soflow {
            font-size:12px;
            height: 30px;
        }
        .disabledDiv {
            pointer-events: none;
            opacity: 0.4;
        }

        table.datatable, table.tabelnobr {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            width: 100%;
            border-collapse:
            collapse; border-spacing: 0;
            font-size: 11px;
            border: 0px solid #000;
        }

        table.datatable td, table.tabelnobr td {
            border: 1px solid #000; /* No more visible border */
            height: 10px;
            transition: all 0.1s;  /* Simple transition for hover effect */
        }

        table.datatable th, table.tabelnobr th {
            background: #DFDFDF;  /* Darken header a bit */
            font-weight: bold;
        }

        table.datatable td, table.tabelnobr td {
            background: #FAFAFA;
        }

        /* Cells in even rows (2,4,6...) are one color */
        tr:nth-child(even) td { background: #F1F1F1; }

        /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
        tr:nth-child(odd) td { background: #FEFEFE; }

        tr td:hover.biasa { background: #666; color: #FFF; }
        tr td:hover.left { background: #ccccff; color: #000; }

        tr td.center1, td.center2 { text-align: center; }

        tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
        tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
        /* Hover cell effect! */
        tr td {
            padding: -10px;
        }

        th {
            background: white;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            z-index:1;
        }
		
    </style>



<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp03");
    mysqli_close($cnmy);
?>