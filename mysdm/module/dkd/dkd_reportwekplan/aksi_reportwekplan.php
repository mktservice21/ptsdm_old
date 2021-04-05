<?PHP

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();
if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}

$pmodule=$_GET['module'];

include("config/koneksimysqli.php");
include("config/common.php");


$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.tmprkpcallincw01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprkpcallincw02_".$puserid."_$now ";


$pkryid = $_POST['cb_karyawan']; 
$ptgl1 = $_POST['e_tanggal'];

$pbulan = date('Y-m', strtotime($ptgl1));
$ptgl1 = date('Y-m-d', strtotime($ptgl1));
$ptgl2 = date('Y-m-d', strtotime('+4 days', strtotime($ptgl1)));

$query = "select a.nama, a.jabatanId as jabatanid, b.nama as nama_jabatan from hrd.karyawan as a 
    LEFT join hrd.jabatan as b on a.jabatanId=b.jabatanId 
    where a.karyawanid='$pkryid'";
$tampilk=mysqli_query($cnmy, $query);
$rowk=mysqli_fetch_array($tampilk);
$pnamakarywanpl=$rowk['nama'];

$sql = "select a.idinput, a.jabatanid, a.tanggal, a.ketid, b.nama as nama_ket, a.real_user1, a.real_date1,
    b.pointMR, b.pointSpv, b.pointDM 
    FROM hrd.dkd_new0 as a JOIN hrd.ket as b on a.ketid=b.ketId 
    WHERE a.karyawanid='$pkryid'";
$sql .=" AND a.tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
$query = "create TEMPORARY table $tmp01 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "select a.idinput, a.dokterid, c.namalengkap, a.jenis,
    c.gelar, c.spesialis FROM 
    hrd.dkd_new1 as a JOIN $tmp01 as b on a.idinput=b.idinput
    LEFT JOIN dr.masterdokter as c on a.dokterid=c.id";
$query = "create TEMPORARY table $tmp02 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select a.jabatanid as jabatanid, b.nama as nama_jabatan from $tmp01 as a 
    LEFT join hrd.jabatan as b on a.jabatanid=b.jabatanId ";
$tampilk=mysqli_query($cnmy, $query);
$rowk=mysqli_fetch_array($tampilk);
$pnamajabatan=$rowk['nama_jabatan'];
$pjabatanid=$rowk['jabatanid'];

$query = "ALTER TABLE $tmp01 ADD COLUMN jpoint DECIMAL(20,2), ADD totakv INT(4), ADD totvisit INT(4), ADD totjv INT(4), ADD totec INT(4), ADD tototh INT(4), ADD totall INT(4), ADD sudahreal VARCHAR(1)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


if ($pjabatanid=="10" OR $pjabatanid=="18") {
    //$query = "UPDATE $tmp01 as a JOIN hrd.ket as b on a.ketid=b.ketId SET a.jpoint=b.pointSpv";
    $query = "UPDATE $tmp01 SET jpoint=pointSpv";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}elseif ($pjabatanid=="08") {
    //$query = "UPDATE $tmp01 as a JOIN hrd.ket as b on a.ketid=b.ketId SET a.jpoint=b.pointDM";
    $query = "UPDATE $tmp01 SET jpoint=pointDM";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}elseif ($pjabatanid=="15") {
    //$query = "UPDATE $tmp01 as a JOIN hrd.ket as b on a.ketid=b.ketId SET a.jpoint=b.pointMR";
    $query = "UPDATE $tmp01 SET jpoint=pointMR";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}


$query = "UPDATE $tmp01 SET totakv=1";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN (select idinput, count(distinct dokterid) as jml FROM 
    $tmp02 WHERE IFNULL(jenis,'')='' GROUP BY 1) as b on a.idinput=b.idinput SET a.totvisit=b.jml";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN (select idinput, count(distinct dokterid) as jml FROM 
    $tmp02 WHERE IFNULL(jenis,'') IN ('EC') GROUP BY 1) as b on a.idinput=b.idinput SET a.totec=b.jml";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 as a JOIN (select idinput, count(distinct dokterid) as jml FROM 
    $tmp02 WHERE IFNULL(jenis,'') IN ('JV') GROUP BY 1) as b on a.idinput=b.idinput SET a.totjv=b.jml";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 as a JOIN (select idinput, count(distinct dokterid) as jml FROM 
    $tmp02 WHERE IFNULL(jenis,'') NOT IN ('', 'EC', 'JV') GROUP BY 1) as b on a.idinput=b.idinput SET a.tototh=b.jml";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN (select idinput, count(distinct dokterid) as jml FROM 
    $tmp02 WHERE IFNULL(jenis,'') NOT IN ('JV') GROUP BY 1) as b on a.idinput=b.idinput SET a.totall=b.jml";
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


    $query = "SELECT jumlah FROM hrd.hrkrj WHERE left(periode1,7)='$pbulan'";
    $tampilk=mysqli_query($cnmy, $query);
    $rowk=mysqli_fetch_array($tampilk);
    $jml_hari_krj=$rowk['jumlah'];

    if ($pjabatanid=='08') {
        $jab = 4;
	} else {
		if (($pjabatanid=='10') or ($pjabatanid=='18')) {
		    $jab = 6;
		} else {
		    if ($pjabatanid=='15') {
			    $jab = 10;
			}
		}
	}
    if (empty($jab)) $jab=0;
    if (empty($jml_hari_krj)) $jml_hari_krj=0;

    $jpoint = (DOUBLE)$jab * (DOUBLE)$jml_hari_krj;


?>

<HTML>
<HEAD>
  <TITLE>Laporan Call Incentive</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>
<script>
</script>

<BODY onload="initVar()">

    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

    <?PHP

    echo "<b>Report Call Incentive (Weekly)</b><br/>";
    echo "<b>Nama : $pnamakarywanpl - $pkryid</b><br/>";
    echo "<b>Jabatan : $pnamajabatan</b><br/>";
    echo "<hr/><br/>";

    $totcall=0;
    $totpoint1=0;
    $totpoint2=0;
    
    echo "<br/><b>Activity</b><br/>";
    
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<tr>";
            $header_ = add_space('Tanggal',40);
            echo "<th align='left'><small>$header_</small></th>";
            $header_ = add_space('Keterangan',60);
            echo "<th align='left'><small>$header_</small></th>";
            $header_ = add_space('Point',40);
            echo "<th align='left'><small>$header_</small></th>";
        echo "</tr>";

        $no=1;
        $query = "select distinct idinput, tanggal, jpoint, totakv, totvisit, totjv, totec, tototh, sudahreal, ketid, nama_ket from $tmp01 order by tanggal";
        $tampil0=mysqli_query($cnmy, $query);
        while ($row0=mysqli_fetch_array($tampil0)) {
            $cidinput=$row0['idinput'];
            $nketid=$row0['ketid'];
            $ntgl=$row0['tanggal'];

            $ntotakv=$row0['totakv'];
            $ntotvisit=$row0['totvisit'];
            $ntotec=$row0['totec'];
            $ntotjv=$row0['totjv'];
            $ntototh=$row0['tototh'];
            $ntotpoint=$row0['jpoint'];

            $nsudahreal=$row0['sudahreal'];
            $nnamaket=$row0['nama_ket'];


            $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
            $xtgl= date('d', strtotime($ntgl));
            $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
            $xthn= date('Y', strtotime($ntgl));

            $ntotpoint=number_format($ntotpoint,0,"","");
            $ntotec=number_format($ntotec,0,"","");
            $ntotjv=number_format($ntotjv,0,"","");
            $ntototh=number_format($ntototh,0,"","");
            
            if ($nketid!="000") {//BLANK
                echo "<tr>";
                echo "<td nowrap>$xhari, $xtgl $xbulan $xthn</td>";
                echo "<td nowrap>$nnamaket</td>";
                echo "<td nowrap align='right'>$ntotpoint</td>";
                echo "</tr>";
            }

            $no++;
        }

    echo "</table>";



    echo "<br/><b>Visist</b><br/>";
    $totcall=0;
    $totpoint1=0;
    $totpoint2=0;

    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<tr>";
            $header_ = add_space('Tanggal',40);
            echo "<th align='left'><small>$header_</small></th>";
            $header_ = add_space('Keterangan',60);
            echo "<th align='left'><small>$header_</small></th>";
            $header_ = add_space('Call',40);
            echo "<th align='left'><small>$header_</small></th>";
        echo "</tr>";

        $no=1;
        $query = "select distinct idinput, tanggal, jpoint, totakv, totvisit, totjv, totec, tototh, totall, sudahreal, nama_ket from $tmp01 order by tanggal";
        $tampil0=mysqli_query($cnmy, $query);
        while ($row0=mysqli_fetch_array($tampil0)) {
            $cidinput=$row0['idinput'];
            $ntgl=$row0['tanggal'];

            $ntotakv=$row0['totakv'];
            $ntotvisit=$row0['totvisit'];
            $ntotec=$row0['totec'];
            $ntotjv=$row0['totjv'];
            $ntototh=$row0['tototh'];
            $ntotpoint=$row0['jpoint'];
            $ntotall=$row0['totall'];

            $nsudahreal=$row0['sudahreal'];
            $nnamaket=$row0['nama_ket'];


            $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
            $xtgl= date('d', strtotime($ntgl));
            $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
            $xthn= date('Y', strtotime($ntgl));

            $ntotpoint=number_format($ntotpoint,0,"","");
            $ntotec=number_format($ntotec,0,"","");
            $ntotjv=number_format($ntotjv,0,"","");
            $ntototh=number_format($ntototh,0,"","");

            $pnamadokt="";
            $pnamadoktjv="";
            $pnamadoktec="";
            $pnamadoktoth="";
            $pnamadoktall="";

            $query = "select distinct jenis, dokterid, namalengkap, gelar, spesialis from $tmp02 WHERE 
                idinput='$cidinput' order by jenis, namalengkap";
            $tampil1=mysqli_query($cnmy, $query);
            while ($row1=mysqli_fetch_array($tampil1)) {
                $njenis=TRIM($row1['jenis']);
                $pnmdokt=$row1['namalengkap'];
                $pgelar=$row1['gelar'];
                $pspesialis=$row1['spesialis'];

                if (!empty($pnmdokt)) $pnmdokt=$pgelar." ".rtrim($pnmdokt, ',')." ".$pspesialis;
                if ($njenis=="JV") $pnmdokt =$pnmdokt." (JV)";
                
                $pnamadoktall .=RTRIM($pnmdokt)." | ";
                
                if (empty($njenis)) {
                    $pnamadokt .=RTRIM($pnmdokt).", ";
                }else{
                    if ($njenis=="JV"){
                        $pnamadoktjv .=RTRIM($pnmdokt).", ";
                    }elseif ($njenis=="EC"){
                        $pnamadoktec .=RTRIM($pnmdokt).", ";
                    }else{
                        $pnamadoktoth .=RTRIM($pnmdokt).", ";
                    }
                }
                
            }

            
            
            if (!empty($pnamadoktall)) {
                $pnamadoktall = substr($pnamadoktall, 0, -2);
            }
                echo "<tr>";
                echo "<td nowrap>$xhari, $xtgl $xbulan $xthn</td>";
                echo "<td >$pnamadoktall</td>";
                echo "<td nowrap align='right'>$ntotall</td>";
                echo "</tr>";
                
            /*
            if (!empty($pnamadokt)) {
                $pnamadokt = substr($pnamadokt, 0, -2);
            }
                echo "<tr>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>Visit</td>";
                echo "<td >$pnamadokt</td>";
                echo "<td nowrap align='right'>$ntotvisit</td>";
                echo "<td nowrap align='right'></td>";
                echo "</tr>";

            

            if (!empty($pnamadoktjv)) {
                $pnamadoktjv = substr($pnamadoktjv, 0, -2);

                echo "<tr>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>Join Visit</td>";
                echo "<td >$pnamadoktjv</td>";
                echo "<td nowrap align='right'>$ntotjv</td>";
                echo "<td nowrap align='right'></td>";
                echo "</tr>";

            }

            if (!empty($pnamadoktec)) {
                $pnamadoktec = substr($pnamadoktec, 0, -2);

                echo "<tr>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>Extra Call</td>";
                echo "<td >$pnamadoktec</td>";
                echo "<td nowrap align='right'>$ntotec</td>";
                echo "<td nowrap align='right'></td>";
                echo "</tr>";

            }

            if (!empty($pnamadoktoth)) {
                $pnamadoktoth = substr($pnamadoktoth, 0, -2);

                echo "<tr>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>Others</td>";
                echo "<td >$pnamadoktoth</td>";
                echo "<td nowrap align='right'>$ntototh</td>";
                echo "<td nowrap align='right'></td>";
                echo "</tr>";

            }
            */
            
            $no++;
        }

    echo "</table>";
    
    
    ?>

</BODY>

<style>
        #myBtn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 30px;
            z-index: 99;
            font-size: 18px;
            border: none;
            outline: none;
            background-color: red;
            color: white;
            cursor: pointer;
            padding: 15px;
            border-radius: 4px;
            opacity: 0.5;
        }

        #myBtn:hover {
            background-color: #555;
        }

    </style>

    <style>
        #tbltable {
            border-collapse: collapse;
        }
        th {
            font-size : 16px;
            padding:5px;
            background-color: #ccccff;
        }
        tr td {
            font-size : 12px;
        }
        tr td {
            padding : 3px;
        }
        tr:hover {background-color:#f5f5f5;}
        thead tr:hover {background-color:#cccccc;}
    </style>

    <script>
        // SCROLL
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};
        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("myBtn").style.display = "block";
            } else {
                document.getElementById("myBtn").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
        // END SCROLL
    </script>


</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_close($cnmy);
?>