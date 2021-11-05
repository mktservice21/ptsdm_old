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
    $tmp01 =" dbtemp.tmpdkdentry01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpdkdentry02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpdkdentry03_".$puserid."_$now ";
    
    $sql = "select idinput, karyawanid, tanggal, ketid FROM hrd.dkd_new0 WHERE karyawanid='$pkryid'";
    $sql .=" AND tanggal between '$ptgl1' AND '$ptgl2'";
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $sql = "select karyawanid, tanggal, dokterid, jenis FROM hrd.dkd_new1 WHERE karyawanid='$pkryid'";
    $sql .=" AND tanggal between '$ptgl1' AND '$ptgl2'";
    $query = "create TEMPORARY table $tmp02 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    $query = "create TEMPORARY table $tmp03 (select distinct karyawanid, tanggal from $tmp01)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO $tmp03 (karyawanid, tanggal) SELECT distinct karyawanid, tanggal FROM $tmp02 WHERE CONCAT(karyawanid, tanggal) NOT IN "
            . " (select distinct IFNULL(CONCAT(karyawanid, tanggal),'') FROM $tmp01)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    


    $query = "Alter table $tmp03 ADD totakv INT(4), ADD totvisit INT(4), ADD totec INT(4), ADD totjv INT(4), ADD sudahreal VARCHAR(1)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp03 as a JOIN (select karyawanid, tanggal, count(distinct ketid) as jml FROM 
        $tmp01 GROUP BY 1,2) as b on a.tanggal=b.tanggal AND a.karyawanid=b.karyawanid SET a.totakv=b.jml";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    $query = "UPDATE $tmp03 as a JOIN (select karyawanid, tanggal, count(distinct dokterid) as jml FROM 
        $tmp02 WHERE IFNULL(jenis,'') NOT IN ('EC', 'JV') GROUP BY 1,2) as b on a.tanggal=b.tanggal AND a.karyawanid=b.karyawanid SET a.totvisit=b.jml";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp03 as a JOIN (select karyawanid, tanggal, count(distinct dokterid) as jml FROM 
       $tmp02 WHERE IFNULL(jenis,'') IN ('EC') GROUP BY 1,2) as b on a.tanggal=b.tanggal AND a.karyawanid=b.karyawanid SET a.totec=b.jml";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    $query = "UPDATE $tmp03 as a JOIN (select karyawanid, tanggal, count(distinct dokterid) as jml FROM 
        $tmp02 WHERE IFNULL(jenis,'') IN ('JV') GROUP BY 1,2) as b on a.tanggal=b.tanggal AND a.karyawanid=b.karyawanid SET a.totjv=b.jml";
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
    


    $hari_ini = date('Y-m-d');
    $query = "select CURRENT_DATE() as tglnow";
    $tampilt=mysqli_query($cnmy, $query);
    $pketemut= mysqli_num_rows($tampilt);
    if ((INT)$pketemut>0) {
        $trow=mysqli_fetch_array($tampilt);
        $t_tglnow=$trow['tglnow'];
        if ($t_tglnow=="0000-00-00") $t_tglnow="";

        if (!empty($t_tglnow)) {
            $hari_ini = $t_tglnow;
        }
    }
    //$hari_ini="2021-11-06";
    $pweekDay = date('w', strtotime($hari_ini));
    //$hari_ini=date_create($hari_ini);

    // 1=senin, 2=selasa, 3=rabu, 4= kamis, 5= jumat, 6= sabtu, 0= minggu
    $pminggulalu=8;
    if ((INT)$pweekDay==0) $pminggulalu=1;
    $pminggulalu=(INT)$pminggulalu-(INT)$pweekDay;
    $ptgllau = date('Ymd', strtotime('+'.(INT)$pminggulalu.' days', strtotime($hari_ini)));



?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
    id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        <table id='datatable' class='datatable table nowrap table-striped table-bordered' width="100%">
            <thead>
                <tr>
                    <th width='50px'>No</th>
                    <th width='100px'>Tanggal</th>
                    <th width='50px'>&nbsp;</th>
                    <th width='50px'>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
            $no=1;
            $query = "select distinct karyawanid, tanggal, totakv, totvisit, totec, totjv, sudahreal from $tmp03 order by tanggal";
            $tampil0=mysqli_query($cnmy, $query);
            while ($row0=mysqli_fetch_array($tampil0)) {
                $cidinput=$row0['karyawanid'];
                $ntgl=$row0['tanggal'];
                $ntotakv=$row0['totakv'];
                $ntotvisit=$row0['totvisit'];
                $ntotec=$row0['totec'];
                $ntotjv=$row0['totjv'];
                $nsudahreal=$row0['sudahreal'];

                $pidget=encodeString($cidinput);

                if (empty($ntotakv)) $ntotakv=0;
                if (empty($ntotvisit)) $ntotvisit=0;
                if (empty($ntotec)) $ntotec=0;
                if (empty($ntotjv)) $ntotjv=0;

                $ntglinputdata = date('Ymd', strtotime($ntgl));
                $ntanggal = date('l d F Y', strtotime($ntgl));

                $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
                $xtgl= date('d', strtotime($ntgl));
                $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
                $xthn= date('Y', strtotime($ntgl));
                
                $pharinya_detail="$xhari, $xtgl $xbulan $xthn";
                
                $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidget&nid=$ntgl'>Edit</a>";
                $print="<a title='detail' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                    . "onClick=\"window.open('eksekusi3.php?module=$pmodule&brid=$cidinput&nid=$ntgl&iprint=detail&intgl=$pharinya_detail',"
                    . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                    . "Detail</a>";

                $phapus="";

                if ($bukanuser==false) {
                    $pedit="";
                }

                $pkettotal="$ntotakv Activity, $ntotvisit Visit";
                if ((INT)$ntotec>0) {
                    $pkettotal="$ntotakv Activity, $ntotvisit Visit, $ntotjv Join Visit, $ntotec Extra Call";
                }else{
                    if ((INT)$ntotec>0 OR (INT)$ntotjv>0) {
                        $pkettotal="$ntotakv Activity, $ntotvisit Visit, $ntotjv Join Visit";
                    }
                }

                if ($nsudahreal=="Y") {
                    $pedit="";
                    $phapus="";
                }
                
                if ($ntglinputdata<$ptgllau) {
                    $pedit="";
                    $phapus="";
                }

                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$xhari, $xtgl $xbulan $xthn</td>";
                echo "<td nowrap>$pkettotal</td>";
                echo "<td nowrap>$pedit &nbsp; &nbsp; $print</td>";
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