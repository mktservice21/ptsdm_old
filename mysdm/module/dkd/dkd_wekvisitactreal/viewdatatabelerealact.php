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
    
    $tglnow = date('Y-m-d');
    $ptgl1=$_POST['utgl1'];
    $ptgl2=$_POST['utgl2'];
    $pkryid=$_POST['ukryid'];
    $pcabid=$_POST['ucabid'];
    $pjbtidpl=$_POST['ujbtid'];


    $_SESSION['RLACWEKPLNCAB']=$pcabid;
    $_SESSION['RLACWEKPLNJBT']=$pjbtidpl;
    $_SESSION['RLACWEKPLNKRY']=$pkryid;
    $_SESSION['RLACWEKPLNTGL']=$ptgl1;

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
    $tmp01 =" dbtemp.tmpdkdrletyact01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpdkdrletyact02_".$puserid."_$now ";

    $sql = "select a.*, b.nama as nama_ket FROM hrd.dkd_new_real0 as a LEFT JOIN hrd.ket as b on a.ketid=b.ketId WHERE karyawanid='$pkryid'";
    $sql .=" AND tanggal between '$ptgl1' AND '$ptgl2'";
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


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
                    <th width='50px'>Keperluan</th>
                    <th width='50px'>Compl</th>
                    <th width='50px'>Aktivitas</th>
                    <!--<th width='50px'>&nbsp;</th>-->
                </tr>
            </thead>
            <tbody>
            <?PHP
            $no=1;
            $query = "select * from $tmp01 order by tanggal, nama_ket";
            $tampil0=mysqli_query($cnmy, $query);
            while ($row0=mysqli_fetch_array($tampil0)) {
                $cidinput=$row0['karyawanid'];
                $ntgl=$row0['tanggal'];
                $nnamaket=$row0['nama_ket'];
                $nketid=$row0['ketid'];
                $ncompl=$row0['compl'];
                $naktivitas=$row0['aktivitas'];

                $pidget=encodeString($cidinput);

                
                $ntanggal = date('l d F Y', strtotime($ntgl));
                $ntglnow = date('Y-m-d', strtotime($ntgl));

                $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
                $xtgl= date('d', strtotime($ntgl));
                $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
                $xthn= date('Y', strtotime($ntgl));

                $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidget'>Edit</a>";

                
                $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesDataHapus('hapus', '$cidinput', '$ntglnow')\">";
                
                $pedit="";
                $phapus="";
                
                if ($bukanuser==false) {
                    $pedit="";
                    $phapus="";
                }
                
                if ($tglnow!=$ntglnow) {
                    $pedit="";
                    $phapus="";
                }

                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$xhari, $xtgl $xbulan $xthn</td>";
                echo "<td nowrap>$nnamaket</td>";
                echo "<td >$ncompl</td>";
                echo "<td >$naktivitas</td>";
                //echo "<td nowrap>$pedit &nbsp; &nbsp; $phapus</td>";
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
    mysqli_close($cnmy);
?>