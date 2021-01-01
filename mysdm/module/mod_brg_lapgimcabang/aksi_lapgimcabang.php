<?php
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
	
	
	
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Laporan Stock Gimmick Cabang.xls");
    }
    
    include("config/koneksimysqli.php");
    
    $printdate= date("d/m/Y");
    
    $karyawanid=$_SESSION['IDCARD'];
    $ptgl=$_POST['e_periode01'];
    $pbulan = date("Ym", strtotime($ptgl));
    $pbulanlalu = date('Ym', strtotime('-1 month', strtotime($ptgl)));
    
    $pdivpilih=$_POST["cb_udiv"];
    
    $pdivpilihanuntuk="ETHICAL";
    if ($pdivpilih=="OT") $pdivpilihanuntuk="OTC";
    
    $phanyaadatrans="";
    if (isset($_POST['chkhanya'])) {
        $phanyaadatrans=$_POST['chkhanya'];
    }
    
    $fdivgrp="";
    foreach ($_POST['chkbox_divisiprodgrp'] as $pgrpdiv) {
        if (!empty($pgrpdiv)) {
            $fdivgrp .="'".$pgrpdiv."',";
        }
    }
    
    if (!empty($fdivgrp)) $fdivgrp=" AND b.DIVISIID IN (".substr($fdivgrp, 0, -1).")";
    
    $fkategori="";
    foreach ($_POST['chkbox_kategori'] as $pkategoriid) {
        if (!empty($pgrpdiv)) {
            $fkategori .="'".$pkategoriid."',";
        }
    }
    
    if (!empty($fkategori)) $fkategori=" AND b.IDKATEGORI IN (".substr($fkategori, 0, -1).")";
    
    $fcabangid="";
    foreach ($_POST['chkbox_cabangid'] as $pcabangid) {
        if (!empty($pcabangid)) {
            $fcabangid .="'".$pcabangid."',";
        }
    }
    
    if (!empty($fcabangid)) {
        $fcabangid="(".substr($fcabangid, 0, -1).")";
    
        if ($pdivpilih=="OT") $fcabangid="ICABANGID_O IN $fcabangid ";
        else $fcabangid="ICABANGID IN $fcabangid ";
        
    }
    
    
    
    $fbarangid="";
    if (isset($_POST['chkbox_produkid'])) {
        foreach ($_POST['chkbox_produkid'] as $pprodukid) {
            if (!empty($pprodukid)) {
                $fbarangid .="'".$pprodukid."',";
            }
        }
    }
    
    if (!empty($fbarangid)) $fbarangid=" AND b.IDBARANG IN (".substr($fbarangid, 0, -1).")";
    
    
    
    
    //echo "$pbulan - $pdivpilih, $fdivgrp<br/>$fkategori<br/>$fcabangid<br/>$fbarangid<br/>";
    
    
    
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp01 =" dbtemp.tmplapgmccab01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmplapgmccab02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmplapgmccab03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmplapgmccab04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmplapgmccab05_".$puserid."_$now ";
    $tmp06 =" dbtemp.tmplapgmccab06_".$puserid."_$now ";
    $tmp07 =" dbtemp.tmplapgmccab07_".$puserid."_$now ";
    
    $query ="SELECT
	d.PILIHAN,
	b.IDBARANG,
	b.DIVISIID,
	d.DIVISINM,
	b.IDKATEGORI,
	k.NAMA_KATEGORI,
	b.NAMABARANG,
	b.STSNONAKTIF,
	k.STSAKTIF,
        CAST(0 as DECIMAL(20,2)) as jmlop, CAST(0 as DECIMAL(20,2)) as jmlakhir, CAST(0 as DECIMAL(20,2)) as jmllalu,
        CAST(0 as DECIMAL(20,2)) as jmlterima, CAST(0 as DECIMAL(20,2)) as jmlkeluar, 
        CAST(0 as DECIMAL(20,2)) as jmlinput, CAST(0 as DECIMAL(20,2)) as jmlblmproces 
        FROM
	dbmaster.t_barang AS b
        LEFT JOIN dbmaster.t_barang_kategori AS k ON b.IDKATEGORI = k.IDKATEGORI
        LEFT JOIN dbmaster.t_divisi_gimick d on b.DIVISIID=d.DIVISIID WHERE d.PILIHAN='$pdivpilih' $fdivgrp $fkategori $fbarangid";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select * from dbmaster.t_barang_cab_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulan' "
            . " AND PILIHAN='$pdivpilih' AND IDBARANG IN (SELECT IFNULL(IDBARANG,'') FROM $tmp01) ";
    if (!empty($fcabangid)) {
        $query .=" AND $fcabangid";
    }
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    //data yang belum closing
    $query="SELECT b.ICABANGID, b.ICABANGID_O, a.IDKELUAR, a.IDBARANG, a.JUMLAH, b.PM_TGL, c.TGLKIRIM, c.TGLTERIMA FROM dbmaster.t_barang_keluar_d a "
            . " JOIN dbmaster.t_barang_keluar b on a.IDKELUAR=b.IDKELUAR "
            . " LEFT JOIN dbmaster.t_barang_keluar_kirim c on a.IDKELUAR=c.IDKELUAR "
            . " WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(b.TANGGAL,'%Y%m')='$pbulan' "
            . " AND CONCAT(a.IDBARANG,b.DIVISIID) IN (SELECT CONCAT(IFNULL(IDBARANG,''),IFNULL(DIVISIID,'')) FROM $tmp01) ";
    if (!empty($fcabangid)) {
        $query .=" AND b.".$fcabangid." ";
    }
    if ($pdivpilih=="OT") {
        $query .=" AND b.ICABANGID_O NOT IN (SELECT IFNULL(ICABANGID_O,'') FROM $tmp02)";
    }else{
        $query .=" AND b.ICABANGID NOT IN (SELECT IFNULL(ICABANGID,'') FROM $tmp02)";
    }
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        //STOCK AWAL ATAU BULAN LALU
        $query = "select * from dbmaster.t_barang_cab_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulanlalu' "
                . " AND PILIHAN='$pdivpilih' AND IDBARANG IN (SELECT IFNULL(IDBARANG,'') FROM $tmp01) ";
        if (!empty($fcabangid)) {
            $query .=" AND $fcabangid";
        }
        if ($pdivpilih=="OT") {
            $query .=" AND ICABANGID_O NOT IN (SELECT IFNULL(ICABANGID_O,'') FROM $tmp02)";
        }else{
            $query .=" AND ICABANGID NOT IN (SELECT IFNULL(ICABANGID,'') FROM $tmp02)";
        }
        $query = "create TEMPORARY table $tmp07 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
    if ($pdivpilih=="OT") {
        $query="SELECT DISTINCT ICABANGID_O as ICABANGID FROM $tmp02 UNION SELECT DISTINCT ICABANGID_O as ICABANGID FROM $tmp03 UNION SELECT DISTINCT ICABANGID_O as ICABANGID FROM $tmp07";
    }else{
        $query="SELECT DISTINCT ICABANGID FROM $tmp02 UNION SELECT DISTINCT ICABANGID FROM $tmp03 UNION SELECT DISTINCT ICABANGID FROM $tmp07";
    }
    $query ="select * from ($query) as XXX";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query ="select DISTINCT * from $tmp04, $tmp01";
    $query = "create TEMPORARY table $tmp05 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "UPDATE $tmp05 a JOIN $tmp02 b ON a.IDBARANG=b.IDBARANG AND a.ICABANGID=b.ICABANGID SET "
            . " a.jmlop=b.JMLOP, a.jmlakhir=b.JMLAKHIR, a.jmllalu=b.JMLLALU, a.jmlblmproces=b.JMLBLMPROS, a.jmlterima=b.JMLTERIMA";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
    
    //yang belum closing
    $query = "UPDATE $tmp05 a JOIN (select ICABANGID, IDBARANG, SUM(JUMLAH) JUMLAH FROM $tmp03 WHERE IFNULL(TGLTERIMA,'')<>'' AND "
            . " IFNULL(TGLTERIMA,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND "
            . " IFNULL(TGLTERIMA,'0000-00-00')<>'0000-00-00'"
            . " GROUP BY 1,2) b ON a.IDBARANG=b.IDBARANG AND a.ICABANGID=b.ICABANGID SET a.jmlterima=b.JUMLAH";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp05 a JOIN (select ICABANGID, IDBARANG, SUM(JUMLAH) JUMLAHBP FROM $tmp03 WHERE IFNULL(TGLTERIMA,'')='' OR "
            . " IFNULL(TGLTERIMA,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR "
            . " IFNULL(TGLTERIMA,'0000-00-00')='0000-00-00'"
            . " GROUP BY 1,2) b ON a.IDBARANG=b.IDBARANG AND a.ICABANGID=b.ICABANGID SET a.jmlblmproces=b.JUMLAHBP";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    
    
        //STOCK AWAL ATAU BULAN LALU
        $query = "UPDATE $tmp05 a JOIN $tmp07 b ON a.IDBARANG=b.IDBARANG AND a.ICABANGID=b.ICABANGID SET a.jmllalu=b.jmlop";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
    //stock akhir dan opname
    $query = "UPDATE $tmp05 SET jmlakhir=IFNULL(jmlterima,0)+IFNULL(jmllalu,0) WHERE ICABANGID IN (SELECT DISTINCT IFNULL(ICABANGID,'') FROM $tmp03)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    $query = "UPDATE $tmp05 SET jmlop=jmlakhir WHERE ICABANGID IN (SELECT DISTINCT IFNULL(ICABANGID,'') FROM $tmp03)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "UPDATE $tmp05 SET jmlakhir=IFNULL(jmlterima,0)+IFNULL(jmllalu,0) WHERE ICABANGID IN (SELECT DISTINCT IFNULL(ICABANGID,'') FROM $tmp07)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        $query = "UPDATE $tmp05 SET jmlop=jmlakhir WHERE ICABANGID IN (SELECT DISTINCT IFNULL(ICABANGID,'') FROM $tmp07)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    
    if ($pdivpilih=="OT") {
        $query = "select a.*, b.nama NAMA_CABANG, CAST('' as CHAR(1)) as stspost from $tmp05 a JOIN MKT.icabang_o b on a.ICABANGID=b.icabangid_o";
    }else{
        $query = "select a.*, b.nama NAMA_CABANG, CAST('' as CHAR(1)) as stspost from $tmp05 a JOIN MKT.icabang b on a.ICABANGID=b.icabangid";
    }
    $query = "create TEMPORARY table $tmp06 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($pdivpilih=="OT") {
        $query = "UPDATE $tmp06 a JOIN $tmp02 b on a.ICABANGID=b.ICABANGID_O SET a.stspost='Y'";
    }else{
        $query = "UPDATE $tmp06 a JOIN $tmp02 b on a.ICABANGID=b.ICABANGID SET a.stspost='Y'";
    }
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($phanyaadatrans=="Y") {
        $query = "DELETE FROM $tmp06 WHERE IFNULL(jmlop,0)=0 AND IFNULL(jmlakhir,0)=0 AND "
                . " IFNULL(jmllalu,0)=0 AND IFNULL(jmlterima,0)=0 AND IFNULL(jmlkeluar,0)=0 AND IFNULL(jmlinput,0)=0 AND IFNULL(jmlblmproces,0)=0";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
?>


<HTML>
<HEAD>
    <title>Laporan Stock Gimmick Cabang</title>
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
    
</HEAD>
<BODY class="nav-md">
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>

<div id='n_content'>
    
    <center><div class='h1judul'>Laporan Stock Gimmick Cabang</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Divisi</td><td>:</td><td><?PHP echo "$pdivpilihanuntuk"; ?></td></tr>
            <tr><td>Bulan</td><td>:</td><td><?PHP echo "$ptgl"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th width='10px' align='center'>No</th>
                <th width='50px' align='center'>Cabang /<br/>Grp.Prod</th>
                <th width='100px' align='center'>Kategori</th>
                <th width='10px' align='center'>Kode</th>
                <th width='200px' align='center'>Nama Barang</th>
                <th width='50px' align='center'>Stock Awal</th>
                <th width='70px' align='center'>blm proses / blm terima</th>
                <th width='60px' align='center'>Jml Masuk</th>
                <th width='60px' align='center'>Stock Akhir</th>
                <th width='70px' align='center'>Stock Opname</th>

            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $query = "select DISTINCT ICABANGID, NAMA_CABANG from $tmp06 order by NAMA_CABANG, ICABANGID";
            $tampil1= mysqli_query($cnmy, $query);
            while ($row1= mysqli_fetch_array($tampil1)) {
                $pidcab=$row1['ICABANGID'];
                $pnmcabang=$row1['NAMA_CABANG'];
                
                echo "<tr>";
                echo "<td nowrap><b></b></td>";
                echo "<td nowrap colspan='9'><b>$pnmcabang</b></td>";
                if ($ppilihrpt!="excel") {
                    echo "<td class='divnone'><b></b></td>";
                    echo "<td class='divnone'><b></b></td>";
                    echo "<td class='divnone'><b></b></td>";
                    echo "<td class='divnone'><b></b></td>";
                    echo "<td class='divnone'><b></b></td>";
                    echo "<td class='divnone'><b></b></td>";
                    echo "<td class='divnone'><b></b></td>";
                    echo "<td class='divnone'><b></b></td>";
                }
                echo "</tr>";
                $no=1;

                $query = "select * from $tmp06 WHERE ICABANGID='$pidcab' order by DIVISINM, NAMABARANG, NAMA_KATEGORI";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {

                    $piddivisi=$row['DIVISIID'];
                    $pnmdivisi=$row['DIVISINM'];
                    $pidkategori=$row['IDKATEGORI'];
                    $pkategori=$row['NAMA_KATEGORI'];
                
                    $pidbarang=$row['IDBARANG'];
                    $pnmbarang=$row['NAMABARANG'];
                    
                    $pjmlop=$row['jmlop'];
                    $pjmlakhir=$row['jmlakhir'];
                    $pjmllalu=$row['jmllalu'];
                    $pjmlterima=$row['jmlterima'];
                    $pjmlkeluar=$row['jmlkeluar'];
                    $pjmlinput=$row['jmlinput'];
                    $pjmlblmpross=$row['jmlblmproces'];


                    $pjmllalu=number_format($pjmllalu,0,",",",");
                    $pjmlblmpross=number_format($pjmlblmpross,0,",",",");
                    $pjmlterima=number_format($pjmlterima,0,",",",");
                    $pjmlakhir=number_format($pjmlakhir,0,",",",");
                    $pjmlop=number_format($pjmlop,0,",",",");

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmdivisi</td>";
                    echo "<td nowrap>$pkategori</td>";
                    echo "<td nowrap>$pidbarang</td>";
                    echo "<td nowrap>$pnmbarang</td>";
                    echo "<td nowrap align='right'>$pjmllalu</td>";
                    echo "<td nowrap align='right'>$pjmlblmpross</td>";
                    echo "<td nowrap align='right'>$pjmlterima</td>";
                    echo "<td nowrap align='right'>$pjmlakhir</td>";
                    echo "<td nowrap align='right'>$pjmlop</td>";
                    echo "</tr>";


                    $no++;
                }


            }
            ?>
        </tbody>
    </table>
    
    
    <p/>&nbsp;<p/>&nbsp;<p/>&nbsp;
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
    
    
        $(document).ready(function() {
            
            
            var table1 = $('#mydatatable1').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [5,6,7,8,9] },//right
                    { className: "text-nowrap", "targets": [0,1,2,3,4,5,6,7,8,9] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );
            

        } );
        
    
    </script>
    
    
    
</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");
    mysqli_close($cnmy);
?>