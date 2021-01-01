<?php

    function BuatFormatNum($prp, $ppilih) {
        if (empty($prp)) $prp=0;
        
        $numrp=$prp;
        if ($ppilih=="1") $numrp=number_format($prp,0,",",",");
        elseif ($ppilih=="2") $numrp=number_format($prp,0,".",".");
            
        return $numrp;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    //ini_set("memory_limit","1G");
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
        header("Content-Disposition: attachment; filename=Laporan Monitoring User KI dan KS.xls");
    }
    
    include("config/koneksimysqli.php");
    //include("config/koneksimysqli_it.php");
    include "config/fungsi_combo.php";
    include "config/fungsi_sql.php";
    include("config/common.php");
    
    $cnit=$cnmy;
    
    $printdate= date("d/m/Y");
    
    $pidgrouppil=$_SESSION['GROUP'];
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $ppilformat="1";
    
?>

<?PHP

$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp00 =" dbtemp.tmplapmonitkiksusr00_".$puserid."_$now ";
$tmp01 =" dbtemp.tmplapmonitkiksusr01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmplapmonitkiksusr02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmplapmonitkiksusr03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmplapmonitkiksusr04_".$puserid."_$now ";


$pidcabang=$_GET['ic'];
$piddokter=$_GET['did'];
$pidkry=$_GET['nid'];
$pbln="2019-12-01";

$pblnpilih = date('Y-m', strtotime($pbln));
$pbulan = date('F Y', strtotime($pbln));


$pnama_cabang= getfield("select nama as lcfields from MKT.icabang WHERE icabangid='$pidcabang'");
$pnama_karyawan= getfield("select nama as lcfields from hrd.karyawan WHERE karyawanid='$pidkry'");
$pnama_dokter= getfield("select nama as lcfields from hrd.dokter WHERE dokterid='$piddokter'");


$query = "select bulan, srid, dokterid, aptid, apttype, iprodid, qty, hna from hrd.ks1 WHERE srid='$pidkry' and dokterid='$piddokter'";
$query = "create TEMPORARY table $tmp00 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER TABLE $tmp00 ADD COLUMN rp DECIMAL(20,2), ADD COLUMN cn DECIMAL(20,2), ADD COLUMN cnrp DECIMAL(20,2), ADD COLUMN jmlki DECIMAL(20,2), ADD COLUMN ists VARCHAR(5)";
mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "select tgl, karyawanid, dokterid, awal, cn from hrd.mr_dokt_a WHERE karyawanid='$pidkry' and dokterid='$piddokter'";
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "SELECT distinct DATE_FORMAT(tgl, '%Y-%m') as tgl, 
        ifnull(karyawanid,'') as karyawanid, ifnull(dokterid,'') as dokterid, ifnull(cn,0) as cn 
        FROM $tmp01 order by tgl asc";
$tampil=mysqli_query($cnit, $query);
while ($row= mysqli_fetch_array($tampil)) {
    $ptgl=$row['tgl'];
    $pkryid=$row['karyawanid'];
    $pdoktid=$row['dokterid'];
    $pcn=$row['cn'];
    
    if (empty($pcn)) $pcn=0;
    
    $query = "UPDATE $tmp00 SET cn='$pcn' WHERE left(bulan,7)>='$ptgl'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
}


$query = "UPDATE $tmp00 SET rp=IFNULL(qty,0)*IFNULL(hna,0)";
mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp00 SET cnrp=case when IFNULL(cn,0)=0 then 0 else IFNULL(rp,0)*(IFNULL(cn,0)/100) end";
mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp00 SET cnrp=case when IFNULL(cn,0)=0 then 0 else (IFNULL(rp,0)*0.8) * (IFNULL(cn,0)/100) end WHERE apttype<>'1'";
mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp00 SET cnrp=0-IFNULL(cnrp,0)";
mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

$query = "select brid, mrid as mrid, dokterid as dokterid, left(tgl,7) as bulan, jumlah, jumlah1 
        from hrd.br0 WHERE mrid='$pidkry' and dokterid='$piddokter' and 
        ifnull(batal,'')<>'Y' and ifnull(retur,'')<>'Y' and brid not in 
        (select distinct IFNULL(brid,'') from hrd.br0_reject)
        GROUP BY 1,2,3";
$query = "create TEMPORARY table $tmp02 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp02 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select mrid as mrid, dokterid as dokterid, bulan, SUM(jumlah) as jumlah 
        from $tmp02
        GROUP BY 1,2,3";
$query = "create TEMPORARY table $tmp03 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "insert into $tmp00 (srid, dokterid, bulan, ists, jmlki) 
        select mrid as mrid, dokterid as dokterid, bulan, 'KI' as ists, SUM(jumlah) as jumlah 
        from $tmp03
        GROUP BY 1,2,3,4";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



$query = "select srid, dokterid, SUM(jmlki) as jmlki, sum(cnrp) as cnrp, SUM(IFNULL(jmlki,0)+IFNULL(cnrp,0)) as saldo_awal   
    from $tmp00 WHERE bulan<='$pblnpilih'";
$query = "create TEMPORARY table $tmp04 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "DELETE FROM $tmp00 WHERE bulan<='$pblnpilih'";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



?>



<HTML>
<HEAD>
    <title>Laporan Monitoring User KI dan KS</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2030 1:00:00 GMT">
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
    
    
    <center><div class='h1judul'>Laporan Monitoring User KI dan KS</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr class='text2'><td>Cabang</td><td>:</td><td><?PHP echo "$pnama_cabang"; ?></td></tr>
            <tr class='text2'><td>Karyawan</td><td>:</td><td><?PHP echo "$pnama_karyawan ($pidkry)"; ?></td></tr>
            <tr class='text2'><td>User</td><td>:</td><td><?PHP echo "$pnama_dokter ($piddokter)"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    <?PHP
    $query = "select tgl_trans as tgl_trans, tgl as tgl, awal as awal, cn as cn from hrd.mr_dokt WHERE "
            . " karyawanid='$pidkry' and dokterid='$piddokter' order by tgl";
    $tampils=mysqli_query($cnit, $query);
    $srow= mysqli_fetch_array($tampils);
    $psaldopertama=$srow['awal'];
    
    $query = "select * from $tmp04";
    $tampila=mysqli_query($cnit, $query);
    $nrow= mysqli_fetch_array($tampila);
    $psaldoawal=$nrow['saldo_awal']+(DOUBLE)$psaldopertama;
    ?>
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th align="center" nowrap>No</th>
                <th align="center" nowrap>Bulan</th>
                <th align="center" nowrap>KS</th>
                <th align="center" nowrap>KI</th>
                <th align="center" nowrap>Saldo</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $no=1;
            $psaldo=BuatFormatNum($psaldoawal, $ppilformat);
            echo "<tr>";
            echo "<td nowrap>$no</td>";
            echo "<td nowrap>Saldo Awal</td>";
            echo "<td nowrap align='right'></td>";
            echo "<td nowrap align='right'></td>";
            echo "<td nowrap align='right'>$psaldo</td>";
            echo "</tr>";
                
            $no++;
            $query = "select bulan, sum(cnrp) as cnrp, sum(jmlki) as jmlki from $tmp00 GROUP BY 1 order by bulan";
            $tampil=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $ipbulan=$row['bulan'];
                $ipcnrp=$row['cnrp'];
                $ipjmlki=$row['jmlki'];
                
                $psaldoawal=(DOUBLE)$psaldoawal+(DOUBLE)$ipcnrp+(DOUBLE)$ipjmlki;
                
                $ipcnrp=BuatFormatNum($ipcnrp, $ppilformat);
                $ipjmlki=BuatFormatNum($ipjmlki, $ppilformat);
                $psaldo=BuatFormatNum($psaldoawal, $ppilformat);
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$ipbulan</td>";
                echo "<td nowrap align='right'>$ipcnrp</td>";
                echo "<td nowrap align='right'>$ipjmlki</td>";
                echo "<td nowrap align='right'>$psaldo</td>";
                echo "</tr>";
                
                $no++;
            }
        ?>
        </tbody>
    </table>
    
    
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
    
    
</HTML>



<?PHP
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");

    mysqli_close($cnmy);
    //mysqli_close($cnit);
?>




