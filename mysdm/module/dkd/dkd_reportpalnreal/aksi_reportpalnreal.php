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
$tmp01 =" dbtemp.tmprptplanreal01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprptplanreal02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmprptplanreal03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmprptplanreal04_".$puserid."_$now ";


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

$sql = "select a.idinput, a.karyawanid, a.jabatanid, a.tanggal, a.ketid, b.nama as nama_ket, a.compl, a.aktivitas
    FROM hrd.dkd_new0 as a LEFT JOIN hrd.ket as b on a.ketid=b.ketId 
    WHERE a.karyawanid='$pkryid' ";
$sql .=" AND a.tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
$query = "create TEMPORARY table $tmp01 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$sql = "select a.idinput, a.karyawanid, a.jabatanid, a.tanggal, 
    c.dokterid, d.namalengkap, d.gelar, d.spesialis, c.jenis, c.notes, c.saran 
    FROM $tmp01 as a LEFT JOIN hrd.dkd_new1 as c on a.idinput=c.idinput JOIN dr.masterdokter as d on c.dokterid=d.id";
$query = "create TEMPORARY table $tmp02 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$sql = "select a.idinput, a.karyawanid, a.tanggal, a.ketid, b.nama as nama_ket, a.compl, a.aktivitas
    FROM hrd.dkd_new_real0 as a LEFT JOIN hrd.ket as b on a.ketid=b.ketId 
    WHERE a.karyawanid='$pkryid' ";
$sql .=" AND a.tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
$query = "create TEMPORARY table $tmp03 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$sql = "select a.karyawanid, a.tanggal, 
    a.dokterid, d.namalengkap, d.gelar, d.spesialis, a.jenis, a.notes, a.saran 
    FROM hrd.dkd_new_real1 as a JOIN dr.masterdokter as d on a.dokterid=d.id 
    WHERE a.karyawanid='$pkryid' ";
$sql .=" AND a.tanggal BETWEEN '$ptgl1' AND '$ptgl2' ";
$query = "create TEMPORARY table $tmp04 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


goto hapusdata;

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

<HTML>
<HEAD>
  <TITLE>Report Daily Plan & Realisasi</TITLE>
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

    echo "<b>Report Daily Plan & Realisasi</b><br/>";
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
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp04");
    mysqli_close($cnmy);
?>