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
include "config/fungsi_ubahget_id.php";


$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.tmprptcutikry01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprptcutikry02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmprptcutikry03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmprptcutikry04_".$puserid."_$now ";
$tmp05 =" dbtemp.tmprptcutikry05_".$puserid."_$now ";
$tmp06 =" dbtemp.tmprptcutikry06_".$puserid."_$now ";
$tmp07 =" dbtemp.tmprptcutikry07_".$puserid."_$now ";


$pkryid = $_POST['cb_karyawan']; 
$nsjenisid = $_POST['cb_jenis']; 
$ptahun = $_POST['e_tahun'];
$ptahunsebelum=(INT)$ptahun-1;
$pnamajabatan="";
    
$query = "select a.nama, a.jabatanId as jabatanid, b.nama as nama_jabatan from hrd.karyawan as a 
    LEFT join hrd.jabatan as b on a.jabatanId=b.jabatanId 
    where a.karyawanid='$pkryid'";
$tampilk=mysqli_query($cnmy, $query);
$rowk=mysqli_fetch_array($tampilk);
$pnamakarywanpl=$rowk['nama'];
$pnamajabatan=$rowk['nama_jabatan'];


$sql = "select * from hrd.karyawan_cuti_close WHERE tahun='$ptahun' AND karyawanid='$pkryid'";
$query = "create TEMPORARY table $tmp01 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "select distinct a.karyawanid, a.jabatanid, b.nama as nama_jabatan from hrd.karyawan_cuti_close as a "
        . " JOIN hrd.jabatan as b on a.jabatanid=b.jabatanId WHERE a.tahun='$ptahunsebelum' AND a.karyawanid='$pkryid' ";
if (!empty($nsjenisid)) $query .=" AND a.id_jenis='$nsjenisid' ";
$tampil= mysqli_query($cnmy, $query);
$ketemu= mysqli_num_rows($tampil);
if ((INT)$ketemu>0) {
    $nrow=mysqli_fetch_array($tampil);
    $pnamajabatan=$nrow['nama_jabatan'];
}


$query = "SELECT distinct a.idcuti, a.tglinput, a.karyawanid, c.nama as nama_karyawan, a.jabatanid, "
        . " a.id_jenis, d.nama_jenis, a.keperluan, a.bulan1, a.bulan2, "
        . " a.atasan1, a.atasan2, a.atasan3, a.atasan4, atasan5, "
        . " a.tgl_atasan1, a.tgl_atasan2, a.tgl_atasan3, a.tgl_atasan4, a.tgl_atasan5, b.tanggal "
        . " FROM hrd.t_cuti0 as a "
        . " LEFT JOIN hrd.t_cuti1 as b on a.idcuti=b.idcuti JOIN hrd.karyawan as c on a.karyawanid=c.karyawanid"
        . " LEFT JOIN hrd.jenis_cuti as d on a.id_jenis=d.id_jenis "
        . " WHERE a.karyawanid='$pkryid' ";
if (!empty($nsjenisid)) $query .=" AND a.id_jenis='$nsjenisid' ";
$query .=" AND ( (YEAR(b.tanggal) = '$ptahun') "
        . " OR (YEAR(a.bulan1) = '$ptahun') OR (YEAR(a.bulan2) = '$ptahun') "
        . " )";
$query = "create TEMPORARY table $tmp02 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>

<HTML>
<HEAD>
  <TITLE>Report Data Cuti</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <style> .str{ mso-number-format:\@; } </style>
    
    
    <!-- Bootstrap -->
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    
    <!-- Custom Theme Style -->
    <link href="build/css/custom.min.css" rel="stylesheet">
</HEAD>
<script>
</script>

<BODY onload="initVar()" style="margin-left:10px; color:#000; background-color:#fff;">
    
    <div class='modal fade' id='myModal' role='dialog'></div>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

    <?PHP

    echo "<b>Report Data Cuti/Izin/Up Country Ethical</b><br/>";
    echo "<b>Nama : $pnamakarywanpl - $pkryid</b><br/>";
    echo "<b>Jabatan : $pnamajabatan</b><br/>";
    echo "<b>Tahun : $ptahun</b><br/>";
    echo "<hr/>";

    $totcall=0;
    $totpoint1=0;
    $totpoint2=0;
    
    
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        
        echo "<tr>";
            
            echo "<th align='left'><small>Jenis</small></th>";
            echo "<th align='left'><small>Keperluan</small></th>";
            echo "<th align='left'><small>Tanggal</small></th>";
            
        echo "</tr>";
        
        $query = "select distinct idcuti, id_jenis, nama_jenis, keperluan, bulan1, bulan2 FROM $tmp02 ORDER BY tanggal, nama_jenis";
        $tampil=mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $pidcuti=$row['idcuti'];
            $pidjenis=$row['id_jenis'];
            $pnmjenis=$row['nama_jenis'];
            $pkeperluan=$row['keperluan'];
            $pbln1=$row['bulan1'];
            $pbln2=$row['bulan2'];
            
            $pbln1= date("d F Y", strtotime($pbln1));
            $pbln2= date("d F Y", strtotime($pbln2));
            
            echo "<tr>";
            echo "<td nowrap>$pnmjenis</td>";
            echo "<td nowrap>$pkeperluan</td>";

            $plewat=false;
            $query = "select tanggal FROM $tmp02 WHERE idcuti='$pidcuti' AND id_jenis<>'02' ORDER BY nama_jenis, tanggal";
            $tampil1=mysqli_query($cnmy, $query);
            $ketemu1= mysqli_num_rows($tampil1);
            if ((INT)$ketemu1==0) {
                echo "<td nowrap>$pbln1 s/d. $pbln2</td>";
                echo "</tr>";
            }else{
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $ntgl=$row1['tanggal'];

                    $ntgl= date("d-m-Y", strtotime($ntgl));
                    if ($plewat==false) {
                        echo "<td nowrap>$ntgl</td>";
                        echo "</tr>";
                    }else{
                        echo "<tr>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>$ntgl</td>";
                        echo "</tr>";
                    }
                    $plewat=true;

                }
            }
            
        }

    echo "</table>";

    

    echo "<br/><br/><br/><br/><br/>";
    
    ?>

</BODY>

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Custom Theme Scripts -->
    <script src="build/js/custom.min.js"></script>

   
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
    
    <script>
        function LiatNotes(ests, enourut, eidkry, etgl, edoktid){
            $.ajax({
                type:"post",
                url:"module/dkd/dkd_reportpalnreal/lihatnotes.php?module=viewnotes",
                data:"usts="+ests+"&unourut="+enourut+"&uidkry="+eidkry+"&utgl="+etgl+"&udoktid="+edoktid,
                success:function(data){
                    $("#myModal").html(data);
                }
            });
        }
        function LiatKomentar(ests, enourut, eidkry, etgl, edoktid){
            $.ajax({
                type:"post",
                url:"module/dkd/dkd_reportpalnreal/lihatkomentar.php?module=viewnotes",
                data:"usts="+ests+"&unourut="+enourut+"&uidkry="+eidkry+"&utgl="+etgl+"&udoktid="+edoktid,
                success:function(data){
                    $("#myModal").html(data);
                }
            });
        }
    </script>

</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp05");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp06");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp07");
    mysqli_close($cnmy);
?>