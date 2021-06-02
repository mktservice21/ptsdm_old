
<?php

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();
if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}

$ppilformat=1;

$ppilihrpt="";

if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];
if ($ppilihrpt=="excel") {
    $ppilformat=3;
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=Laporan Rincian Kas Kecil Cabang.xls");
}
    
    
$pmodule=$_GET['module'];

include("config/koneksimysqli.php");
include("config/fungsi_sql.php");
include("config/common.php");

$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.tmplapkascabsum01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmplapkascabsum02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmplapkascabsum03_".$puserid."_$now ";


$pbln1 = $_POST['e_tgl1'];
$pbln2 = $_POST['e_tgl2'];
$pdivpilih = $_POST['cb_divisiid'];
$pstsprosesapv= $_POST['cb_status'];
$prptby= $_POST['cb_rptby'];


$pbulan01 = date('Y-m-01', strtotime($pbln1));
$pbulan02 = date('Y-m-t', strtotime($pbln2));

$pperiode01 = date('F Y', strtotime($pbln1));
$pperiode02 = date('F Y', strtotime($pbln2));

$pnamadivisi="ETHICAL";
if ($pdivpilih=="OTC" OR $pdivpilih=="CHC") $pnamadivisi="CHC";

$query = "select idkascab, bulan, pengajuan, karyawanid, icabangid, areaid, "
        . " icabangid_o, areaid_o, coa4, "
        . " keterangan, nmrealisasi, norekening, jumlah "
        . " from dbmaster.t_kaskecilcabang WHERE "
        . " IFNULL(stsnonaktif,'')<>'Y' ";
$query .=" AND bulan BETWEEN '$pbulan01' AND '$pbulan02' ";
if ($pdivpilih=="OTC" OR $pdivpilih=="CHC") {
    $query .=" AND IFNULL(pengajuan,'') IN ('OTC', 'CHC') ";
}else{
    $query .=" AND IFNULL(pengajuan,'') NOT IN ('OTC', 'CHC') ";
}
if ($pstsprosesapv=="apvfin") {
    $query .=" AND IFNULL(tgl_fin,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00' AND IFNULL(tgl_fin,'') <> '' ";
}
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

if ($pdivpilih=="OTC" OR $pdivpilih=="CHC") {
    $query = "select a.*, b.nama as nama_cabang, c.nama as nama_area FROM $tmp01 as a JOIN mkt.icabang_o as b on a.icabangid_o=b.icabangid_o "
            . " LEFT JOIN mkt.iarea_o as c on a.icabangid_o=c.icabangid_o AND "
            . " a.areaid_o=c.areaid_o";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET icabangid=icabangid_o, areaid=areaid_o";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}else{
    $query = "select a.*, b.nama as nama_cabang, c.nama as nama_area FROM $tmp01 as a JOIN mkt.icabang as b on a.icabangid=b.icabangid "
            . " LEFT JOIN mkt.iarea as c on a.icabangid=c.icabangid AND "
            . " a.areaid=c.areaid";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET areaid='', nama_area=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}

$query = "select a.idkascab, a.bulan, a.pengajuan, a.karyawanid, b.nama as nama_karyawan, "
        . " a.icabangid, a.nama_cabang, a.areaid, a.nama_area, "
        . " a.coa4, c.NAMA4 as nama_coa4, "
        . " a.keterangan, a.nmrealisasi, a.norekening, "
        . " a.jumlah, d.saldoawal, d.pc_bln_lalu, d.pcm, d.jmltambahan, d.jumlah as jmlpcm, d.oustanding, d.iket "
        . " from $tmp02 as a LEFT JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId "
        . " LEFT JOIN dbmaster.coa_level4 as c on a.coa4=c.COA4 "
        . " LEFT JOIN dbmaster.t_kaskecilcabang_rpdetail as d on a.idkascab=d.idkascab";
$query = "create  table $tmp03 ($query)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER table $tmp03 ADD COLUMN kdbulan varchar(20), ADD COLUMN namabulan varchar(200)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "ALTER TABLE $tmp03 MODIFY icabangid varchar(200), MODIFY areaid varchar(200)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

if ($prptby=="rptbybln") {
    
    $query = "UPDATE $tmp03 SET kdbulan=LEFT(bulan,7), namabulan=DATE_FORMAT(bulan, '%M %Y'), icabangid=nama_cabang";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
}else{

    $query = "UPDATE $tmp03 SET kdbulan=icabangid, namabulan=nama_cabang";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp03 SET icabangid=LEFT(bulan,7), nama_cabang=DATE_FORMAT(bulan, '%M %Y')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

}

?>

<HTML>
<HEAD>
  <TITLE>Laporan Kas Kecil Cabang</TITLE>
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
    echo "<b>Laporan Kas Kecil Cabang</b><br/>";
    echo "<b>Periode : $pperiode01 s/d. $pperiode02</b><br/>";
    echo "<b>Divisi : $pnamadivisi</b><br/>";
    echo "<hr/><br/>";
    
    echo "<table id='tbltable' border='1' cellspacing='0' cellpadding='1'>";
        echo "<thead>";
            echo "<tr>";
                echo "<th align='center'><small>No</small></th>";
                echo "<th align='center'><small>Cabang</small></th>";
                if ($pdivpilih=="OTC" OR $pdivpilih=="CHC") echo "<th align='center'><small>Area</small></th>";
                echo "<th align='center'><small>ID</small></th>";
                echo "<th align='center'><small>COA</small></th>";
                echo "<th align='center'><small>Nama Perkiraan</small></th>";
                echo "<th align='center'><small>Nama Realisasi</small></th>";
                echo "<th align='center'><small>No. Rekening</small></th>";
                echo "<th align='center'><small>Keterangan</small></th>";
                echo "<th align='center'><small>Saldo Awal</small></th>";
                echo "<th align='center'><small>PC Bln Lalu</small></th>";
                echo "<th align='center'><small>Jumlah Biaya</small></th>";
                echo "<th align='center'><small>PCM</small></th>";
                echo "<th align='center'><small>Saldo Akhir</small></th>";
                echo "<th align='center'><small>Jml Tambahan</small></th>";
                echo "<th align='center'><small>PCM Plus Tambahan</small></th>";
                echo "<th align='center'><small>Saldo Akhir Plus Tambahan</small></th>";
                echo "<th align='center'><small>Ket Tambahan</small></th>";
            echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
            $no=1;
            $query = "select distinct kdbulan, namabulan from $tmp03 order by kdbulan";
            $tampil0=mysqli_query($cnmy, $query);
            while ($row0=mysqli_fetch_array($tampil0)) {
                //$nbulan=$row0['bulan'];
                //$nbln=$row0['ibulan'];
                //$nbln = date('F Y', strtotime($nbln));
                
                $nkdbln=$row0['kdbulan'];
                $nnmbln=$row0['namabulan'];
                
                $pjmlcolspan=17;
                if ($pdivpilih=="OTC" OR $pdivpilih=="CHC") $pjmlcolspan=18;
                
                echo "<tr>";
                echo "<td nowrap colspan='$pjmlcolspan' align='left'><b>$nnmbln</b></td>";
                echo "</tr>";
                
                $no=1;
                //$query = "select * from $tmp03 WHERE LEFT(bulan,7)='$nbulan' order by bulan, nama_cabang";
                $query = "select * from $tmp03 WHERE kdbulan='$nkdbln' order by icabangid, nama_cabang";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1=mysqli_fetch_array($tampil1)) {
                    
                    $nidkascab=$row1['idkascab'];
                    $nnmcab=$row1['nama_cabang'];
                    $nnmarea=$row1['nama_area'];
                    $ncoa=$row1['coa4'];
                    $nnmcoa=$row1['nama_coa4'];
                    $nnmreal=$row1['nmrealisasi'];
                    $nnorek=$row1['norekening'];
                    $nketerangan=$row1['keterangan'];
                    
                    $niket_tambah=$row1['iket'];
                    $nsldawal=$row1['saldoawal'];
                    $npcblnlalu=$row1['pc_bln_lalu'];
                    $njmlbiaya=$row1['jumlah'];
                    $npcm=$row1['pcm'];
                    $njmltambahan=$row1['jmltambahan'];
                    $noutstanding=$row1['oustanding'];
                    
                    $psaldoakhir=(DOUBLE)$npcm-(DOUBLE)$njmlbiaya;
                    $ppcmtambahan=(DOUBLE)$npcm+(DOUBLE)$njmltambahan;
                    $psaldoakhirtambah=((DOUBLE)$npcm+(DOUBLE)$njmltambahan)-(DOUBLE)$njmlbiaya;
                    
                    
                    $nsldawal=BuatFormatNumberRp($nsldawal, $ppilformat);//1 OR 2 OR 3
                    $npcblnlalu=BuatFormatNumberRp($npcblnlalu, $ppilformat);//1 OR 2 OR 3
                    $njmlbiaya=BuatFormatNumberRp($njmlbiaya, $ppilformat);//1 OR 2 OR 3
                    $npcm=BuatFormatNumberRp($npcm, $ppilformat);//1 OR 2 OR 3
                    $njmltambahan=BuatFormatNumberRp($njmltambahan, $ppilformat);//1 OR 2 OR 3
                    
                    $psaldoakhir=BuatFormatNumberRp($psaldoakhir, $ppilformat);//1 OR 2 OR 3
                    $ppcmtambahan=BuatFormatNumberRp($ppcmtambahan, $ppilformat);//1 OR 2 OR 3
                    $psaldoakhirtambah=BuatFormatNumberRp($psaldoakhirtambah, $ppilformat);//1 OR 2 OR 3
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$nnmcab</td>";
                    if ($pdivpilih=="OTC" OR $pdivpilih=="CHC") echo "<td nowrap>$nnmarea</td>";
                    echo "<td nowrap>$nidkascab</td>";
                    echo "<td nowrap>$ncoa</td>";
                    echo "<td nowrap>$nnmcoa</td>";
                    echo "<td nowrap>$nnmreal</td>";
                    echo "<td nowrap>$nnorek</td>";
                    echo "<td >$nketerangan</td>";
                    echo "<td nowrap align='right'>$nsldawal</td>";
                    echo "<td nowrap align='right'>$npcblnlalu</td>";
                    echo "<td nowrap align='right'><b>$njmlbiaya</b></td>";
                    echo "<td nowrap align='right'>$npcm</td>";
                    echo "<td nowrap align='right'><b>$psaldoakhir</b></td>";
                    echo "<td nowrap align='right'>$njmltambahan</td>";
                    echo "<td nowrap align='right'>$ppcmtambahan</td>";
                    echo "<td nowrap align='right'><b>$psaldoakhirtambah</b></td>";
                    echo "<td >$niket_tambah</td>";
                    echo "</tr>";
                    
                    $no++;
                }
            }
            
        echo "</tbody>";
    echo "</table>";
    
    echo "<br/><br/><br/><br/><br/>";
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
    
</HTML>


<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
    mysqli_close($cnmy);
?>