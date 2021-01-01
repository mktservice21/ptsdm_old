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
        header("Content-Disposition: attachment; filename=Laporan Monitoring User KI dan KS Per Cabang.xls");
    }
    
    include("config/koneksimysqli.php");
    //include("config/koneksimysqli_it.php");
    include "config/fungsi_combo.php";
    include "config/fungsi_sql.php";
    include("config/common.php");
    
    $cnit=$cnmy;
    
    $printdate= date("d/m/Y");
    $pthnsekrang= date("Y");
    $pthnlalu=(DOUBLE)$pthnsekrang-1;
    
    $pidgrouppil=$_SESSION['GROUP'];
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $ppilformat="1";
    
?>

<?PHP

$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp00 =" dbtemp.tmplapmonitkiksusrcb00_".$puserid."_$now ";
$tmp01 =" dbtemp.tmplapmonitkiksusrcb01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmplapmonitkiksusrcb02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmplapmonitkiksusrcb03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmplapmonitkiksusrcb04_".$puserid."_$now ";
$tmp05 =" dbtemp.tmplapmonitkiksusrcb05_".$puserid."_$now ";
$tmp06 =" dbtemp.tmplapmonitkiksusrcb06_".$puserid."_$now ";


$pidcabang=$_POST['cb_cabang'];
$pidrpttype=$_POST['cb_rpttype'];
$piddokter="";
$pidkry="";
$pbln="2019-12-01";

$pblnpilih = date('Y-m', strtotime($pbln));
$pbulan = date('F Y', strtotime($pbln));


$pnama_cabang= getfield("select nama as lcfields from MKT.icabang WHERE icabangid='$pidcabang'");


    $query = "select distinct dokterid as dokterid, mrid as mrid "
            . " from hrd.br0 WHERE icabangid='$pidcabang' AND IFNULL(stsbr,'')='KI' and 
        ifnull(batal,'')<>'Y' and ifnull(retur,'')<>'Y' and brid not in 
        (select distinct IFNULL(brid,'') from hrd.br0_reject) ";
    
    //$query .= " AND dokterid='0000022361' and mrid='0000001941' ";
    
    $tampil = mysqli_query($cnmy, $query);
    while ($z= mysqli_fetch_array($tampil)) {
        $pniddokt=$z['dokterid'];
        $pmrid=$z['mrid'];
        
        $piddokter .="'".$pniddokt."".$pmrid."',";
    }
    
    if (!empty($piddokter)) {
        $piddokter="(".substr($piddokter, 0, -1).")";
    }

//echo $piddokter; goto hapusdata;


$query = "select bulan, srid, dokterid, aptid, apttype, iprodid, qty, hna from hrd.ks1 WHERE 1=1 ";
if (!empty($piddokter)) {
    $query .= " AND CONCAT(IFNULL(dokterid,''), IFNULL(srid,'')) IN $piddokter ";
}

//$query .= " AND dokterid='0000022361' and srid='0000001941' ";

$query = "create TEMPORARY table $tmp00 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER TABLE $tmp00 ADD COLUMN rp DECIMAL(20,2), ADD COLUMN cn DECIMAL(20,2), ADD COLUMN cnrp DECIMAL(20,2), ADD COLUMN jmlki DECIMAL(20,2), ADD COLUMN jmlsa DECIMAL(20,2), ADD COLUMN ists VARCHAR(5)";
mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query = "select distinct bulan, srid, dokterid from $tmp00 ";
        $query = "create TEMPORARY table $tmp05 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query = "select DISTINCT karyawanid as srid, dokterid as dokterid, tgl as tgl, LEFT(tgl,7) as bulan, awal as awal from hrd.mr_dokt WHERE 1=1 ";
        if (!empty($piddokter)) {
            $query .= " AND CONCAT(IFNULL(dokterid,''), IFNULL(karyawanid,'')) IN $piddokter ";
        }
        $query = "create TEMPORARY table $tmp06 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "UPDATE $tmp06 SET tgl=CONCAT('$pthnlalu', '-01-01'), bulan=CONCAT('$pthnlalu', '-01') WHERE IFNULL(tgl,'')='' OR IFNULL(tgl,'0000-00-00')='0000-00-00'";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select tgl, karyawanid, dokterid, awal, cn from hrd.mr_dokt_a WHERE 1=1 ";
if (!empty($piddokter)) {
    $query .= " AND CONCAT(IFNULL(dokterid,''), IFNULL(karyawanid,'')) IN $piddokter ";
}
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
    
    $query = "UPDATE $tmp00 SET cn='$pcn' WHERE left(bulan,7)>='$ptgl' AND dokterid='$pdoktid' AND srid='$pkryid'";
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
        from hrd.br0 WHERE CONCAT(IFNULL(dokterid,''), IFNULL(mrid,'')) IN $piddokter AND IFNULL(stsbr,'')='KI' and 
        ifnull(batal,'')<>'Y' and ifnull(retur,'')<>'Y' and brid not in 
        (select distinct IFNULL(brid,'') from hrd.br0_reject)
        GROUP BY 1,2,3";

$query = "create TEMPORARY table $tmp02 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



            $pfiltsrdandr="";
            $query = "select distinct bulan, srid, dokterid from $tmp06 order by srid, dokterid, bulan";
            $tampilc=mysqli_query($cnit, $query);
            while ($crow= mysqli_fetch_array($tampilc)) {
                $csrid=$crow['srid'];
                $cdoktid=$crow['dokterid'];
                $cblnpl=$crow['bulan'];

                $psrdandokt=$csrid."".$cdoktid;

                if (!empty($psrdandokt)) {
                    if (strpos($pfiltsrdandr, $psrdandokt)==false) {
                        $pfiltsrdandr .="'".$psrdandokt."',";
                        //echo "$csrid, $cdoktid, $cblnpl<br/>";

                        $query = "DELETE FROM $tmp00 WHERE srid='$csrid' AND dokterid='$cdoktid' AND bulan<'$cblnpl'";
                        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                        $query = "DELETE FROM $tmp02 WHERE mrid='$csrid' AND dokterid='$cdoktid' AND bulan<'$cblnpl'";
                        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                    }
                }


            }


$query = "UPDATE $tmp02 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select mrid as mrid, dokterid as dokterid, bulan, SUM(jumlah) as jumlah 
        from $tmp02
        GROUP BY 1,2,3";
$query = "create TEMPORARY table $tmp03 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


//insert KI
$query = "insert into $tmp00 (srid, dokterid, bulan, ists, jmlki) 
        select mrid as mrid, dokterid as dokterid, bulan, 'KI' as ists, SUM(jumlah) as jumlah 
        from $tmp03
        GROUP BY 1,2,3,4";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



        $query = "DROP TEMPORARY TABLE $tmp02";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        //SALDO AWAL
        $query = "select karyawanid, dokterid, left(tgl,7) as bulan, sum(awal) as awal from hrd.mr_dokt WHERE "
                . "CONCAT(IFNULL(dokterid,''), IFNULL(karyawanid,'')) IN $piddokter AND IFNULL(awal,0)<>0 GROUP by 1,2,3";
        $query = "create TEMPORARY table $tmp02 ($query)";
        //mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    //insert SALDO AWAL
    $query = "insert into $tmp00 (srid, dokterid, bulan, ists, jmlsa) 
            select srid as karyawanid, dokterid as dokterid, bulan, 'SA' as ists, SUM(awal) as awal 
            from $tmp06
            GROUP BY 1,2,3,4";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select srid, dokterid, SUM(jmlsa) as jmlsa, SUM(jmlki) as jmlki, sum(cnrp) as cnrp, SUM(IFNULL(jmlsa,0)+IFNULL(jmlki,0)+IFNULL(cnrp,0)) as saldo_akhir   
    from $tmp00 GROUP BY 1,2";//WHERE bulan<='$pblnpilih'
$query = "create TEMPORARY table $tmp04 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



$query = "DROP TEMPORARY TABLE $tmp01";
mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "select a.srid, b.nama as nama_karyawan, a.dokterid, c.nama as nama_dokter, a.jmlsa, a.jmlki, a.cnrp, a.saldo_akhir "
        . " FROM $tmp04 as a JOIN hrd.karyawan as b on a.srid=b.karyawanid "
        . " JOIN hrd.dokter as c on a.dokterid=c.dokterid";
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnit, $query);
$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

if (empty($pidrpttype)) {
    
}else{
    if ($pidrpttype=="M") {//MINUS
        $query = "DELETE FROM $tmp01 WHERE IFNULL(saldo_akhir,0)>=0";
    }elseif ($pidrpttype=="N") {//NOL
        $query = "DELETE FROM $tmp01 WHERE IFNULL(saldo_akhir,0)<>0";
    }else{//PLUS
        $query = "DELETE FROM $tmp01 WHERE IFNULL(saldo_akhir,0)<=0";
    }
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
}


?>



<HTML>
<HEAD>
    <title>Laporan Monitoring User KI dan KS Per Cabang</title>
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
    
    
    <center><div class='h1judul'>Laporan Monitoring User KI dan KS Per Cabang</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr class='text2'><td>Cabang</td><td>:</td><td><?PHP echo "$pnama_cabang"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>

    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th align="center" nowrap>No</th>
                <th align="center" nowrap>Karyawan</th>
                <th align="center" nowrap>Dokter</th>
                <th align="center" nowrap>Saldo Awal</th>
                <th align="center" nowrap>KI</th>
                <th align="center" nowrap>KS</th>
                <th align="center" nowrap>Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $ptotsa=0;
            $ptotki=0;
            $ptotks=0;
            
            $no=1;
            $query = "select * from $tmp01 order by nama_karyawan, nama_dokter";
            $tampil=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $ipkryid=$row['srid'];
                $ipkrynm=$row['nama_karyawan'];
                $ipdoktid=$row['dokterid'];
                $ipdoktnm=$row['nama_dokter'];
                
                $ipjmlsa=$row['jmlsa'];
                $ipjmlki=$row['jmlki'];
                $ipcnrp=$row['cnrp'];
                
                $psaldoakhir=(DOUBLE)$ipjmlsa+(DOUBLE)$ipcnrp+(DOUBLE)$ipjmlki;
                $ptotsa=(DOUBLE)$ptotsa+(DOUBLE)$ipjmlsa;
                $ptotki=(DOUBLE)$ptotki+(DOUBLE)$ipjmlki;
                $ptotks=(DOUBLE)$ptotks+(DOUBLE)$ipcnrp;
                
                $ipjmlsa=BuatFormatNum($ipjmlsa, $ppilformat);
                $ipjmlki=BuatFormatNum($ipjmlki, $ppilformat);
                $ipcnrp=BuatFormatNum($ipcnrp, $ppilformat);
                $psaldoakhir=BuatFormatNum($psaldoakhir, $ppilformat);
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$ipkrynm ($ipkryid)</td>";
                echo "<td nowrap>$ipdoktnm ($ipdoktid)</td>";
                echo "<td nowrap align='right'>$ipjmlsa</td>";
                echo "<td nowrap align='right'>$ipjmlki</td>";
                echo "<td nowrap align='right'>$ipcnrp</td>";
                echo "<td nowrap align='right'>$psaldoakhir</td>";
                echo "</tr>";
                
                $no++;
            }
            /*
            $psaldoakhir=(DOUBLE)$ptotsa+(DOUBLE)$ptotks+(DOUBLE)$ptotki;
            
            $ptotsa=BuatFormatNum($ptotsa, $ppilformat);
            $ptotks=BuatFormatNum($ptotks, $ppilformat);
            $ptotki=BuatFormatNum($ptotki, $ppilformat);
            $psaldoakhir=BuatFormatNum($psaldoakhir, $ppilformat);
                
            echo "<tr style='font-weight:bold'>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap>Total : </td>";
            echo "<td nowrap align='right'>$ptotsa</td>";
            echo "<td nowrap align='right'>$ptotki</td>";
            echo "<td nowrap align='right'>$ptotks</td>";
            echo "<td nowrap align='right'>$psaldoakhir</td>";
            echo "</tr>";
               */ 
                
        ?>
        </tbody>
    </table>
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
    
    
</HTML>



<?PHP
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp06");

    mysqli_close($cnmy);
    //mysqli_close($cnit);
?>




