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

//masa kerja
$pthnsistem = date("Y");
$pmasakerja=date("Y-m-d");
if ($ptahun!=$pthnsistem) {
    $pmasakerja=$ptahun."-12-31";
}


$query = "select a.nama, a.jabatanId as jabatanid, b.nama as nama_jabatan from hrd.karyawan as a 
    LEFT join hrd.jabatan as b on a.jabatanId=b.jabatanId 
    where a.karyawanid='$pkryid'";
$tampilk=mysqli_query($cnmy, $query);
$rowk=mysqli_fetch_array($tampilk);
$pnamakarywanpl=$rowk['nama'];
$pnamajabatan=$rowk['nama_jabatan'];


$sql = "select a.*, b.potong_cuti from hrd.karyawan_cuti_close as a LEFT JOIN hrd.jenis_cuti as b "
        . " on a.id_jenis=b.id_jenis WHERE a.tahun='$ptahunsebelum' ";
if (!empty($pkryid)) $sql .=" AND a.karyawanid='$pkryid' ";

$query = "create TEMPORARY table $tmp01 ($sql)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "select distinct a.karyawanid, a.jabatanid, b.nama as nama_jabatan from hrd.karyawan_cuti_close as a "
        . " LEFT JOIN hrd.jabatan as b on a.jabatanid=b.jabatanId WHERE a.tahun='$ptahunsebelum' ";
if (!empty($pkryid)) $sql .=" AND a.karyawanid='$pkryid' ";
if (!empty($nsjenisid)) $query .=" AND a.id_jenis='$nsjenisid' ";
$tampil= mysqli_query($cnmy, $query);
$ketemu= mysqli_num_rows($tampil);
if ((INT)$ketemu>0) {
    $nrow=mysqli_fetch_array($tampil);
    $pnamajabatan=$nrow['nama_jabatan'];
}



$query = "select DISTINCT a.*, c.potong_cuti FROM hrd.t_cuti0 as a "
        . " LEFT JOIN hrd.t_cuti1 as b on a.idcuti=b.idcuti LEFT JOIN hrd.jenis_cuti as c on a.id_jenis=c.id_jenis "
        . " WHERE IFNULL(a.stsnonaktif,'')<>'Y' ";
if (!empty($nsjenisid)) $query .=" AND a.id_jenis='$nsjenisid' ";
$query .=" AND ( (YEAR(b.tanggal) = '$ptahun') "
        . " OR (YEAR(a.bulan1) = '$ptahun') OR (YEAR(a.bulan2) = '$ptahun') "
        . " )";
if (!empty($pkryid)) $query .=" AND a.karyawanid='$pkryid' ";
$query = "create TEMPORARY table $tmp02 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select b.karyawanid, b.id_jenis, b.keperluan, b.potong_cuti, a.* from hrd.t_cuti1 as a JOIN $tmp02 as b on a.idcuti=b.idcuti ";
$query = "create TEMPORARY table $tmp03 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "select distinct a.karyawanid, a.keperluan, a.id_jenis, b.tanggal from $tmp02 as a"
        . " JOIN $tmp03 as b on a.idcuti=b.idcuti WHERE karyawanid='ALLETH'";
$query = "create TEMPORARY table $tmp04 ($query)"; 
mysqli_query($cnmy, $query);

$query = "select a.*, b.nama as nama_karyawan, b.tglmasuk, b.tglkeluar, b.tglkeluar as tglmasakerja, "
        . " c.nama as nama_jabatan, d.nama_jenis "
        . " from $tmp02 as a"
        . " LEFT JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId "
        . " LEFT JOIN hrd.jabatan as c on a.jabatanid=c.jabatanId "
        . " LEFT JOIN hrd.jenis_cuti as d on a.id_jenis=d.id_jenis ";
$query = "create TEMPORARY table $tmp05 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "alter table $tmp05 add column jml_thn INT(4), add column jml_bln INT(4), add column jmlcutithn INT(4), add column jmlcutifree INT (4)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp05 SET tglmasakerja='$pmasakerja' WHERE IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp05 SET jml_thn=IFNULL(TIMESTAMPDIFF(YEAR, tglmasuk, tglmasakerja),0), jml_bln=IFNULL(TIMESTAMPDIFF(MONTH, tglmasuk, tglmasakerja),0)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp05 SET jmlcutithn='12' WHERE IFNULL(jml_thn,0)>=1";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp05 SET jmlcutithn=jml_bln WHERE IFNULL(jml_thn,0)=0 AND IFNULL(jml_bln,0)>1 AND IFNULL(jml_bln,0)<=12";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query ="SELECT id_jenis, dari, sampai, ifnull(free_cuti,0) as free_cuti FROM hrd.jenis_cuti_free_tambahan WHERE id_jenis='01' "
        . " order by id_jenis, dari, sampai";
$tampilk=mysqli_query($cnmy, $query);
while ($rowk= mysqli_fetch_array($tampilk)) {
    $lidjenis=$rowk['id_jenis'];
    $ldari=$rowk['dari'];
    $lsampai=$rowk['sampai'];
    $lfreecuti=$rowk['free_cuti'];
    
    if ($lidjenis=="01") {
        $query = "UPDATE $tmp05 SET jmlcutifree='$lfreecuti' WHERE "
                . " ifnull(jml_thn,0)>='$ldari' AND ifnull(jml_thn,0)<='$lsampai'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
}


/*
goto hapusdata;

$query = "SELECT distinct a.idcuti, a.tglinput, a.karyawanid, c.nama as nama_karyawan, a.jabatanid, "
        . " a.id_jenis, d.nama_jenis, a.keperluan, a.bulan1, a.bulan2, "
        . " a.atasan1, a.atasan2, a.atasan3, a.atasan4, atasan5, "
        . " a.tgl_atasan1, a.tgl_atasan2, a.tgl_atasan3, a.tgl_atasan4, a.tgl_atasan5, b.tanggal "
        . " FROM hrd.t_cuti0 as a "
        . " LEFT JOIN hrd.t_cuti1 as b on a.idcuti=b.idcuti JOIN hrd.karyawan as c on a.karyawanid=c.karyawanid"
        . " LEFT JOIN hrd.jenis_cuti as d on a.id_jenis=d.id_jenis "
        . " WHERE IFNULL(a.stsnonaktif,'')<>'Y' AND a.karyawanid='$pkryid' ";
if (!empty($nsjenisid)) $query .=" AND a.id_jenis='$nsjenisid' ";
$query .=" AND ( (YEAR(b.tanggal) = '$ptahun') "
        . " OR (YEAR(a.bulan1) = '$ptahun') OR (YEAR(a.bulan2) = '$ptahun') "
        . " )";
$query = "create TEMPORARY table $tmp02 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
*/    
    
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
    //echo "<b>Nama : $pnamakarywanpl - $pkryid</b><br/>";
    //echo "<b>Jabatan : $pnamajabatan</b><br/>";
    echo "<b>Tahun : $ptahun</b><br/>";
    echo "<hr/>";

    $totcall=0;
    $totpoint1=0;
    $totpoint2=0;
    
    
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        
        echo "<tr>";
            
            echo "<th align='left'><small>No</small></th>";
            echo "<th align='left'><small>Nama Karyawan</small></th>";
            echo "<th align='left'><small>Jabatan</small></th>";
            echo "<th align='left'><small>Tgl. Masuk</small></th>";
            echo "<th align='left'><small>Masa Kerja</small></th>";
            echo "<th align='left'><small>Jenis Cuti</small></th>";
            echo "<th align='left'><small>Tanggal</small></th>";
            echo "<th align='left'><small>Keperluan</small></th>";
            
        echo "</tr>";
        
        $no=1;
        $query = "select distinct karyawanid, nama_karyawan, jabatanid, nama_jabatan, tglmasuk, jml_thn, jml_bln, jmlcutithn, jml_thn, jmlcutifree FROM $tmp05 ORDER BY nama_karyawan";
        $tampil0=mysqli_query($cnmy, $query);
        while ($row0= mysqli_fetch_array($tampil0)) {
            $pidkaryawan=$row0['karyawanid'];
            $pnmkaryawan=$row0['nama_karyawan'];
            $pnmjabatan=$row0['nama_jabatan'];
            $ptglmasuk=$row0['tglmasuk'];
            
            $pmskrjathn=$row0['jml_thn'];
            $pmskrjabln=$row0['jml_bln'];
            $pjmlcutiskr=$row0['jmlcutithn'];
            $pjmlcutifree=$row0['jmlcutifree'];
            
            if (empty($pjmlcutiskr)) $pjmlcutiskr=0;
            if (empty($pjmlcutifree)) $pjmlcutifree=0;
            
            $pmasakerja="0";
            if ((INT)$pmskrjathn>0) $pmasakerja=$pmskrjathn." tahun";
            else{
                if ((INT)$pmskrjabln>0) $pmasakerja=$pmskrjabln." bulan";
            }
    
            if ($ptglmasuk=="0000-00-00") $ptglmasuk="";
            if (!empty($ptglmasuk)) $ptglmasuk=date("d/m/Y", strtotime($ptglmasuk));
            
            $nidkry=(INT)$pidkaryawan;
            
            echo "<tr class='fbreak'>";
            echo "<td nowrap>$no</td>";
            echo "<td nowrap>$pnmkaryawan ($nidkry)</td>";
            echo "<td nowrap>$pnmjabatan</td>";
            echo "<td nowrap>$ptglmasuk</td>";
            echo "<td nowrap>$pmasakerja</td>";
            
            $plewat0=false;
            $query = "select distinct id_jenis, nama_jenis FROM $tmp05 WHERE karyawanid='$pidkaryawan' ORDER BY nama_jenis";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pidjenis_=$row['id_jenis'];
                $pnmjenis_=$row['nama_jenis'];
                
                if ($plewat0==false) {
                    echo "<td nowrap>$pnmjenis_</td>";
                }else{
                    echo "<tr>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>$pnmjenis_</td>";
                }
                $plewat0=true;
                    
                $plewat1=false;
                $query = "select distinct idcuti, id_jenis, nama_jenis, keperluan, bulan1, bulan2 FROM $tmp05 WHERE karyawanid='$pidkaryawan' AND id_jenis='$pidjenis_' ORDER BY nama_jenis";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidcuti=$row1['idcuti'];
                    $pidjenis=$row1['id_jenis'];
                    $pnmjenis=$row1['nama_jenis'];
                    $pkeperluan=$row1['keperluan'];
                    $pbln1=$row1['bulan1'];
                    $pbln2=$row1['bulan2'];

                    $pbln1= date("d F Y", strtotime($pbln1));
                    $pbln2= date("d F Y", strtotime($pbln2));

                    
                    if ($plewat1==false) {
                        
                    }else{
                        echo "<tr>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                    }
                    $plewat1=true;

                    $plewat2=false;
                    $query = "select * FROM $tmp03 WHERE karyawanid='$pidkaryawan' AND id_jenis='$pidjenis' AND idcuti='$pidcuti' ORDER BY tanggal";
                    $tampil2=mysqli_query($cnmy, $query);
                    $ketemu2= mysqli_num_rows($tampil2);
                    if ((INT)$ketemu2==0) {
                        echo "<td nowrap>$pbln1 s/d. $pbln2</td>";
                        echo "<td >$pkeperluan</td>";
                        echo "</tr>";
                    }else{


                        while ($row2= mysqli_fetch_array($tampil2)) {
                            $ntgl=$row2['tanggal'];
                            $ntgl= date("d-m-Y", strtotime($ntgl));

                            $pkeperluan=$row2['keperluan'];

                            if ($plewat2==false) {
                                echo "<td nowrap>$ntgl</td>";
                                echo "<td >$pkeperluan</td>";
                                echo "</tr>";
                            }else{
                                echo "<tr>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>$ntgl</td>";
                                echo "<td >$pkeperluan</td>";
                                echo "</tr>";
                            }
                            $plewat2=true;
                        }

                    }

                }
            
            }
                
            /*
            
            //Cuti Massal
            
            
            //Cuti Tahun Lalu
            $query = "select karyawanid, sum(sisa_cuti) as sisa_cuti from $tmp01 WHERE karyawanid='$pidkaryawan' AND "
                    . " (id_jenis='01' OR IFNULL(sisa_cuti,0)<0) AND IFNULL(potong_cuti,'')='Y' "
                    . " GROUP BY 1";
            $tampil4=mysqli_query($cnmy, $query);
            $row4= mysqli_fetch_array($tampil4);
            
            $pjmlsisa=$row4['sisa_cuti'];
            if (empty($pjmlsisa)) $pjmlsisa=0;
            
            if ((INT)$pjmlsisa>0) $pjmlsisa=0;
            
            $pketcutithnlalu="Cuti Tahun $ptahunsebelum";

            echo "<tr>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td >$pketcutithnlalu</td>";
            echo "<td nowrap>$pjmlsisa</td>";
            echo "<td >&nbsp;</td>";
            echo "</tr>";
            
            //Jumlah Dapat Cuti Tahun Sekarang
            $pketcutithnskr="Cuti Tahun $ptahun";
            
            echo "<tr>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td >$pketcutithnskr</td>";
            echo "<td nowrap>$pjmlcutiskr</td>";
            echo "<td >&nbsp;</td>";
            echo "</tr>";
            
            //free cuti
            if ((INT)$pjmlcutifree>0) {
                $pketcutithnskr="Free Cuti $ptahun";

                echo "<tr>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td >$pketcutithnskr</td>";
                echo "<td nowrap>$pjmlcutifree</td>";
                echo "<td >&nbsp;</td>";
                echo "</tr>";
            }
            
            */
            
            $no++;
            
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
        .fbreak {
            background-color:#f5f5f5;
        }
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