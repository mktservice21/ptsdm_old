<?php

    function BuatFormatNum($prp, $ppilih) {
        if (empty($prp)) $prp=0;
        
        $numrp=$prp;
        if ($ppilih=="1") $numrp=number_format($prp,0,",",",");
        elseif ($ppilih=="2") $numrp=number_format($prp,0,".",".");
            
        return $numrp;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    $ppilihrpt="";
    
    if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Laporan KS Monitoring User.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/koneksimysqli_it.php");
    include "config/fungsi_combo.php";
    include "config/fungsi_sql.php";
    include("config/common.php");
    
    $printdate= date("d/m/Y");
    
    $pidgrouppil=$_SESSION['GROUP'];
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $ppilformat="1";
    
?>

<?PHP

$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp00 =" dbtemp.tmplapmonitksusr00_".$puserid."_$now ";
$tmp01 =" dbtemp.tmplapmonitksusr01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmplapmonitksusr02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmplapmonitksusr03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmplapmonitksusr04_".$puserid."_$now ";


$filtercabang=('');
if (!empty($_POST['chkbox_idcab'])){
    $filtercabang=$_POST['chkbox_idcab'];
    $filtercabang=PilCekBoxAndEmpty($filtercabang);
}
$fpost_blank = strpos($filtercabang, 'pilih_kosong');

$filterdokter=('');
if (!empty($_POST['chkbox_iddok'])){
    $filterdokter=$_POST['chkbox_iddok'];
    $filterdokter=PilCekBoxAndEmpty($filterdokter);
}
$fpost_blank = strpos($filterdokter, 'pilih_kosong');


$nmkaryawan="";
$pjbtid="";
$filterkaryawan="";

$pidkaryawan=$_POST['cb_karyawan'];
if (!empty($pidkaryawan)) {
    $filterkaryawan="'".$pidkaryawan."',";
    
    
    $query = "select karyawanid as karyawanid, nama as nama, jabatanid as jabatanid from hrd.karyawan where karyawanId='$pidkaryawan'";
    $tampilk=mysqli_query($cnmy, $query);
    $krow= mysqli_fetch_array($tampilk);
    
    $nmkaryawan=$krow['nama'];
    $pjbtid=$krow['jabatanid'];
}

$piddokt=$_POST['e_iddokt'];
$nmdokter= getfield("select nama as lcfields from hrd.dokter where dokterid='$piddokt'");
$ppilihdokt="";
if (!empty($piddokt)) {
    $ppilihdokt="($nmdokter)";
}


$tgl01 = $_POST['e_tgl1'];
$tgl02 = $_POST['e_tgl2'];

$pperiode1 = date("Y-m", strtotime($tgl01));
$pperiode2 = date("Y-m", strtotime($tgl02));

$myperiode1 = date("F Y", strtotime($tgl01));
$myperiode2 = date("F Y", strtotime($tgl02));

$filtercabang="";
if ($pjbtid=="08") {
    
    
    $query = "select distinct icabangid as icabangid from MKT.idm0 WHERE karyawanid='$pidkaryawan'";
    $tampil=mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $nicabid=$row['icabangid'];
        
        $filtercabang .="'".$nicabid."',";
    }
    
    if (!empty($filtercabang)) {
        $filtercabang="(".substr($filtercabang, 0, -1).")";

        $query = "select distinct karyawanid as karyawanid from MKT.ispv0 WHERE icabangid IN $filtercabang";
        $tampil=mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $nikryid=$row['karyawanid'];
            
            if (!empty($nikryid)) $filterkaryawan .="'".$nikryid."',";
        }

        $query = "select distinct karyawanid as karyawanid from MKT.imr0 WHERE icabangid IN $filtercabang";
        $tampil=mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $nikryid=$row['karyawanid'];
            
            if (!empty($nikryid)) $filterkaryawan .="'".$nikryid."',";
        }
        
    
    }
    
}elseif ($pjbtid=="10" OR $pjbtid=="18") {
    
    $query = "select distinct icabangid as icabangid, areaid as areaid, divisiid as divisiid from MKT.ispv0 WHERE karyawanid='$pidkaryawan'";
    $tampil=mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $nicabid=$row['icabangid'];
        $niareaid=$row['areaid'];
        $nidivisiid=$row['divisiid'];
        
        $filtercabang .="'".$nicabid."".$niareaid."".$nidivisiid."',";
    }
    
    
    if (!empty($filtercabang)) {
        $filtercabang="(".substr($filtercabang, 0, -1).")";
        
        
        $query = "select distinct karyawanid as karyawanid from MKT.imr0 WHERE CONCAT(icabangid,areaid,divisiid) IN $filtercabang";
        $tampil=mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $nikryid=$row['karyawanid'];
            
            if (!empty($nikryid)) $filterkaryawan .="'".$nikryid."',";
        }
        
    }
    
}


if (!empty($filterkaryawan)) {
    $filterkaryawan="(".substr($filterkaryawan, 0, -1).")";
}

//echo "$pidkaryawan = $nmkaryawan ($pjbtid)<br/>cabang : $filtercabang, KRY : $filterkaryawan, DOKT : $filterdokter"; goto hapusdata;

$query = "select * from hrd.ks1 WHERE 1=1";

if (!empty($filterdokter)) {
    $query .=" AND dokterid IN $filterdokter ";
}

if (!empty($filterkaryawan)) {
    $query .=" AND srid IN $filterkaryawan ";
}

if (!empty($piddokt)) {
    $query .=" AND dokterid='$piddokt' ";
}

if (!empty($filtercabang)) {
    if ((DOUBLE)$fpost_blank>0) {//jika ada pilihan kosong cabang (blank)
        
    }else{
        
    }
}
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select a.tgl, a.karyawanid, a.dokterid, a.awal, a.cn "
        . " from hrd.mr_dokt_a as a JOIN (select distinct srid, dokterid from $tmp01) as b on a.karyawanid=b.srid AND a.dokterid=b.dokterid";
$query = "create TEMPORARY table $tmp02 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select a.bulan, a.srid, c.nama as nama_karyawan, a.dokterid, b.nama as nama_dokter, a.aptid, a.apttype, a.iprodid, sum(IFNULL(a.qty,0)*IFNULL(a.hna,0)) as rp "
        . " from $tmp01 as a "
        . " JOIN hrd.dokter as b on a.dokterid=b.dokterid "
        . " JOIN hrd.karyawan as c on a.srid=c.karyawanid";
$query .=" GROUP BY 1,2,3,4,5,6,7,8";
$query = "create TEMPORARY table $tmp03 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
/*
$query = "DELETE FROM $tmp03 WHERE IFNULL(rp,0)=0";
mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "Alter table $tmp03 ADD COLUMN cn DECIMAL(20,2), ADD COLUMN cnrp DECIMAL(20,2)";
mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "SELECT distinct DATE_FORMAT(tgl, '%Y-%m') as tgl, 
        ifnull(karyawanid,'') as karyawanid, ifnull(dokterid,'') as dokterid, ifnull(cn,0) as cn 
	FROM $tmp02 order by tgl asc";
$tampil= mysqli_query($cnit, $query);
$ketemu= mysqli_num_rows($tampil);
if ((DOUBLE)$ketemu>0) {
    
    while ($row= mysqli_fetch_array($tampil)) {
        
        $nptgl=$row['tgl'];
        $npkryid=$row['karyawanid'];
        $npdoktid=$row['dokterid'];
        $npcn=$row['cn'];
        
        if (empty($npcn)) $npcn=0;
        
        $query = "UPDATE $tmp03 SET cn='$npcn' WHERE left(bulan,7)>='$nptgl' AND srid='$npkryid' AND dokterid='$npdoktid'";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    $query = "UPDATE $tmp03 SET cnrp=case when IFNULL(cn,0)=0 then 0 else IFNULL(rp,0)*(IFNULL(cn,0)/100) end";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp03 SET cnrp=case when IFNULL(cn,0)=0 then 0 else (IFNULL(rp,0)*0.8) * (IFNULL(cn,0)/100) end WHERE IFNULL(apttype,'')<>'1'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
}
*/
$query = "Alter table $tmp03 ADD column dokterid_new INT(10) UNSIGNED ZEROFILL, ADD column dokterid_new_nama VARCHAR(150), ADD column spesialis VARCHAR(50)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 as a JOIN dbmaster.dokter_mapping as b on a.dokterid=b.dokterid SET a.dokterid_new=b.dokterid_new";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp03 as a JOIN dbmaster.masterdokter as b on a.dokterid_new=b.id SET a.dokterid_new_nama=b.namalengkap, a.spesialis=b.spesialis";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


/*
$query = "select dokterid, nama_dokter, srid, nama_karyawan, bulan, dokterid_new, dokterid_new_nama, spesialis, sum(rp) as rp, sum(cnrp) as cnrp "
        . " FROM $tmp03 ";
$query .=" GROUP BY 1,2,3,4,5,6,7,8";
*/
$query = "select dokterid, nama_dokter, srid, nama_karyawan, bulan, dokterid_new, dokterid_new_nama, spesialis "
        . " FROM $tmp03 ";
$query = "create TEMPORARY table $tmp04 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$ppilihsrid="";
$query = "select distinct srid from $tmp04";
$tampil=mysqli_query($cnit, $query);
while ($row= mysqli_fetch_array($tampil)) {
    $pilsrid=$row['srid'];

    $ppilihsrid .=$pilsrid.",";
}

?>


<HTML>
<HEAD>
    <title>Laporan KS Monitoring User</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        
		<!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        

        
    <?PHP } ?>
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>


<BODY>
    
<div class='modal fade' id='myModal' role='dialog'></div>
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>

<div id='n_content'>

    <center><div class='h1judul'>Monitoring User</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    <?PHP
    $plihatview = "<a class='btn btn-info btn-xs' href='eksekusi3.php?module=lihatdatausernew&act=viewdata&idmenu=$_GET[idmenu]&ket=bukan&ikar=$pidkaryawan&iusr=$piddokt&isr=$ppilihsrid' target='_blank'>Lihat Data User Baru</a>";
    echo $plihatview;
    ?>
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th align="center" nowrap>No</th>
                <th align="center" nowrap>ID</th>
                <th align="center" nowrap>Nama User</th>
                <th align="center" nowrap>User Mapping</th>
                <th align="center" nowrap>Mapping ID</th>
                <th align="center" nowrap>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $query = "select DISTINCT dokterid, nama_dokter, dokterid_new, dokterid_new_nama, spesialis from $tmp04 ";
            $query .= " order by nama_dokter, dokterid";
            $tampil=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $piddokt=$row['dokterid'];
                $pnmdokter=$row['nama_dokter'];
                
                $pidnewdokt=$row['dokterid_new'];
                $pnmnewdokter=$row['dokterid_new_nama'];
                $pnmnewspesialis=$row['spesialis'];
                
                $pnamadoktermaping="";
                if (!empty($pidnewdokt)) $pnamadoktermaping=$pnmnewdokter.", ".$pnmnewspesialis." (".$pidnewdokt.")";
                
                $filedinputnama_dokt="<input type='text' size='40px' value='$pnamadoktermaping' name='txt_nmdoktmap[$piddokt]' id='txt_nmdoktmap[$piddokt]' Readonly>";
                
                $filedinput="<input type='text' value='$pidnewdokt' name='txt_doktmap[$piddokt]' id='txt_doktmap[$piddokt]' maxlength='10' size='10px'>";
                
                $psimpan="<input type='button' id='btnsave[]' name='btnsave[]' value='Save' "
                        . " onclick=\"SimpanData('$piddokt', 'txt_doktmap[$piddokt]')\">";
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap class='str'>$piddokt</td>";
                echo "<td nowrap>$pnmdokter</td>";
                echo "<td nowrap>$filedinputnama_dokt</td>";
                echo "<td nowrap>$filedinput</td>";
                echo "<td nowrap>$psimpan</td>";
                echo "</tr>";

                $no++;
            }
            ?>
        </tbody>
    </table>
    
    <!--
    <br/>
    
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th align="center" nowrap>No</th>
                <th align="center" nowrap>ID</th>
                <th align="center" nowrap>Nama User</th>
                <th align="center" nowrap>karyawan ID</th>
                <th align="center" nowrap>Nama Karyawan</th>
                <th align="center" nowrap>Bulan</th>
                <th align="center" nowrap>Value</th>
                <th align="center" nowrap>CN</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            /*
            $ptotal=0;
            $no=1;
            $query = "select * from $tmp04 ";
            $query .= " order by nama_dokter, dokterid, nama_karyawan, srid, bulan";
            $tampil=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                
                $piddokt=$row['dokterid'];
                $pnmdokter=$row['nama_dokter'];
                $pidkary=$row['srid'];
                $pnmkary=$row['nama_karyawan'];
                $pblnp=$row['bulan'];
                $prp=$row['rp'];
                $pcn=$row['cnrp'];
                
                $pblnp=$pblnp."-01";
                if ($ppilihrpt=="excel") {
                    $pblnp = date("d F Y", strtotime($pblnp));
                }else{
                    $pblnp = date("F Y", strtotime($pblnp));
                }

                $prp=BuatFormatNum($prp, $ppilformat);
                $pcn=BuatFormatNum($pcn, $ppilformat);
                
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap class='str'>$piddokt</td>";
                echo "<td nowrap>$pnmdokter</td>";
                echo "<td nowrap class='str'>$pidkary</td>";
                echo "<td nowrap>$pnmkary</td>";
                echo "<td nowrap>$pblnp</td>";
                echo "<td nowrap align='right'>$prp</td>";
                echo "<td nowrap align='right'>$pcn</td>";
                echo "</tr>";

                $no++;
                
                
            }
            
            */
            ?>
        </tbody>
    </table>
    
    <br/>&nbsp;
    <u><b>SUMMARY</b></u><br/>
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th align="center" nowrap>ID</th>
                <th align="center" nowrap>Nama User</th>
                <th align="center" nowrap>karyawan ID</th>
                <th align="center" nowrap>Nama Karyawan</th>
                <th align="center" nowrap>Value</th>
                <th align="center" nowrap>CN</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            /*
            $ptotal=0;
            $no=1;
            $query = "select dokterid, nama_dokter, srid, nama_karyawan, sum(rp) as rp, sum(cnrp) as cnrp from $tmp04 "
                    . " GROUP BY 1,2,3,4";
            $query .= " order by nama_dokter, dokterid, nama_karyawan, srid";
            $tampil=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                
                $piddokt=$row['dokterid'];
                $pnmdokter=$row['nama_dokter'];
                $pidkary=$row['srid'];
                $pnmkary=$row['nama_karyawan'];
                
                $prp=$row['rp'];
                $pcn=$row['cnrp'];

                $prp=BuatFormatNum($prp, $ppilformat);
                $pcn=BuatFormatNum($pcn, $ppilformat);
                
                
                echo "<tr>";
                echo "<td nowrap class='str'>$piddokt</td>";
                echo "<td nowrap>$pnmdokter</td>";
                echo "<td nowrap class='str'>$pidkary</td>";
                echo "<td nowrap>$pnmkary</td>";
                echo "<td nowrap align='right'>$prp</td>";
                echo "<td nowrap align='right'>$pcn</td>";
                echo "</tr>";

                $no++;
                
                
            }
            */
            
            ?>
        </tbody>
    </table>
    
    -->
    
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    
</div>    
    
    <?PHP if ($ppilihrpt!="excel") { ?>

        
        <!-- Bootstrap -->
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    
        <!-- Datatables -->
		
        <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
        <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
        <script src="vendors/jszip/dist/jszip.min.js"></script>
        <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
        <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

        
        
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
            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 5px 20px 20px 20px;
                /*overflow-x:auto;*/
            }
            
            .h1judul {
              color: blue;
              font-family: verdana;
              font-size: 140%;
              font-weight: bold;
            }
            table.tbljudul {
                font-size : 15px;
            }
            table.tbljudul tr td {
                padding: 1px;
                font-family : "Arial, Verdana, sans-serif";
            }
            .tebal {
                 font-weight: bold;
            }
            .miring {
                 font-style: italic;
            }
            table.tbljudul tr.text2 {
                font-size : 13px;
            }
            
            table {
                text-align: left;
                position: relative;
                border-collapse: collapse;
                background-color:#FFFFFF;
            }

            th {
                background: white;
                position: sticky;
                top: 0;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                z-index:1;
            }

            .th2 {
                background: white;
                position: sticky;
                top: 23;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                border-top: 1px solid #000;
            }
        </style>
    
        <style>
            
            .divnone {
                display: none;
            }
            #mydatatable1, #mydatatable2 {
                color:#000;
                font-family: "Arial";
            }
            #mydatatable1 th, #mydatatable2 th {
                font-size: 12px;
            }
            #mydatatable1 td, #mydatatable2 td { 
                font-size: 11px;
            }
        </style>
        
    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 140%;
              font-weight: bold;
            }
        </style>
    <?PHP } ?>
        
        
        
        
</BODY>

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
        function SimpanData(uiddokt, uidmaping) {
        
            var idokmaping = document.getElementById(uidmaping).value;


            if (uiddokt=="") {
                alert("UserMasih Kosong..."); return; false;
            }
        
            var r=confirm("Apakah akan menyimpan data...???")
            if (r==true) {
                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/laporan/lap_ks_user/simpandataeditmaping.php?module="+module+"&act=input&idmenu="+idmenu,
                    data:"uiddokt="+uiddokt+"&uidokmaping="+idokmaping,
                    success:function(data){
                        //alert(data);
                        document.getElementById('txt_nmdoktmap['+uiddokt+']').value=data;
                    }
                });

                return 1;
            }
            
        
        }
        
    </script>
    
    
</HTML>



<?PHP
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");

    mysqli_close($cnmy);
    mysqli_close($cnit);
?>