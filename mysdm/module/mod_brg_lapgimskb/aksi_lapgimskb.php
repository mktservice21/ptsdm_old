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
        header("Content-Disposition: attachment; filename=Laporan Keluar Barang Gimmick.xls");
    }
    
    include("config/koneksimysqli.php");
    
    $printdate= date("d/m/Y");
    
    $karyawanid=$_SESSION['IDCARD'];
    $ptgl=$_POST['e_periode01'];
    $ptgl2=$_POST['e_periode02'];
    $pbulan = date("Ym", strtotime($ptgl));
    $pbulan2 = date("Ym", strtotime($ptgl2));
    
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
    $tmp01 =" dbtemp.tmplapgmcskb01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmplapgmcskb02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmplapgmcskb03_".$puserid."_$now ";
    
    $query ="select b.*, a.NAMA_KATEGORI, c.NAMA_SUP from dbmaster.t_barang b LEFT JOIN dbmaster.t_barang_kategori a "
            . " on a.IDKATEGORI=b.IDKATEGORI LEFT JOIN dbmaster.t_supplier c on b.KDSUPP=c.KDSUPP "
            . " WHERE 1=1 $fkategori $fbarangid";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query="SELECT b.STSNONAKTIF, a.IDKELUAR, b.TANGGAL, b.DIVISIID, b.ICABANGID, b.ICABANGID_O, a.IDBARANG, "
            . " a.JUMLAH, b.NOTES, b.PM_TGL, c.NORESI, c.TGLKIRIM, c.IDPENERIMA, c.TGLTERIMA FROM dbmaster.t_barang_keluar_d a "
            . " JOIN dbmaster.t_barang_keluar b on a.IDKELUAR=b.IDKELUAR "
            . " LEFT JOIN dbmaster.t_barang_keluar_kirim c on a.IDKELUAR=c.IDKELUAR "
            . " WHERE DATE_FORMAT(b.TANGGAL,'%Y%m') BETWEEN '$pbulan' AND '$pbulan2' "
            . " AND b.$fcabangid $fdivgrp ";
    $query .= " AND a.IDBARANG IN (select distinct IFNULL(IDBARANG,'') FROM $tmp01)";
    if (!empty($phanyaadatrans)) {
        $query .= " AND IFNULL(b.STSNONAKTIF,'')='Y'";
    }else{
        $query .= " AND IFNULL(b.STSNONAKTIF,'')<>'Y'";
    }
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="SELECT d.PILIHAN, a.*, b.NAMABARANG, b.IDKATEGORI, b.NAMA_KATEGORI, b.NAMA_SUP, c.NAMA_PENERIMA, "
            . " e.nama NAMA_CABANG, f.nama NAMACABOTC "
            . " FROM $tmp02 a LEFT JOIN $tmp01 b on a.IDBARANG=b.IDBARANG "
            . " LEFT JOIN dbmaster.t_barang_penerima c on a.IDPENERIMA=c.IDPENERIMA "
            . " LEFT JOIN dbmaster.t_divisi_gimick d on a.DIVISIID=d.DIVISIID "
            . " LEFT JOIN MKT.icabang e on a.ICABANGID=e.icabangid "
            . " LEFT JOIN MKT.icabang_o f on a.ICABANGID_O=f.icabangid_o";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp03 SET ICABANGID=ICABANGID_O, NAMA_CABANG=NAMACABOTC WHERE PILIHAN='OT'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
?>


<HTML>
<HEAD>
    <title>Laporan Keluar Barang Gimmick</title>
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
    
    <center><div class='h1judul'>Laporan Keluar Barang Gimmick</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Divisi</td><td>:</td><td><?PHP echo "$pdivpilihanuntuk"; ?></td></tr>
            <tr><td>Bulan</td><td>:</td><td><?PHP echo "$ptgl s/d. $ptgl2"; ?></td></tr>
            <?PHP
            if (!empty($phanyaadatrans)) {
                echo "<tr><td>Status</td><td>:</td><td>Reject/Batal</td></tr>";
            }
            ?>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th width='10px' align='center'>No</th>
                <th width='10px' align='center'>ID</th>
                <th width='10px' align='center'>Tanggal</th>
                <th width='10px' align='center'>Cabang</th>
                <th width='100px' align='center'>Grp.Prod</th>
                <th width='100px' align='center'>Kategori</th>
                <th width='200px' align='center'>Nama Barang</th>
                <th width='100px' align='center'>Qty</th>
                <th width='5px' align='center'></th>

            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $query = "select DISTINCT IDKELUAR, TANGGAL, ICABANGID, NAMA_CABANG, DIVISIID, PM_TGL, NORESI, TGLTERIMA, STSNONAKTIF from $tmp03 ORDER BY  IDKELUAR, TANGGAL, NAMA_CABANG, DIVISIID";
            $tampil1= mysqli_query($cnmy, $query);
            while ($row1= mysqli_fetch_array($tampil1)) {
                $pidkeluar=$row1['IDKELUAR'];
                $ptgl=$row1['TANGGAL'];
                $pidcab=$row1['ICABANGID'];
                $pnmcab=$row1['NAMA_CABANG'];
                $piddiv=$row1['DIVISIID'];
                
                $pstsbatal=$row1['STSNONAKTIF'];
                $ptglpm=$row1['PM_TGL'];
                $ptglterima=$row1['TGLTERIMA'];
                $pnoresi=$row1['NORESI'];
                
                if ($ptglpm=="0000-00-00 00:00:00" OR $ptglpm=="0000-00-00") $ptglpm="";
                if ($ptglterima=="0000-00-00 00:00:00" OR $ptglterima=="0000-00-00") $ptglterima="";
                
                $pstsappv="";
                if (!empty($ptglpm)) {
                    $pstsappv="PM";
                    if (!empty($pnoresi)) $pstsappv="KR";
                    if (!empty($ptglterima)) $pstsappv="TR";
                }
                
                $pstylests=" style='background-color:#F0FFFF;' ";
                if ($pstsappv=="PM") $pstylests=" style='background-color:yellow;' ";
                elseif ($pstsappv=="KR") $pstylests=" style='background-color:green;' ";
                elseif ($pstsappv=="TR") $pstylests=" style='background-color:blue;' ";
                
                if ($pstsbatal=="Y") {
                    $pstsappv="BT";
                    $pstylests=" style='background-color:red;' ";
                }
                
                
                $ptgl = date("d/m/Y", strtotime($ptgl));
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pidkeluar</td>";
                echo "<td nowrap>$ptgl</td>";
                echo "<td nowrap>$pnmcab</td>";
                echo "<td nowrap>$piddiv</td>";
                /*
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "</tr>";
                */
                $ilewat=false;
                $no++;
                
                $query = "select * from $tmp03 WHERE IDKELUAR='$pidkeluar' ORDER BY  NAMA_KATEGORI, NAMABARANG";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidbrg=$row['IDBARANG'];
                    $pnmbrg=$row['NAMABARANG'];
                    $pidkat=$row['IDKATEGORI'];
                    $pnmkat=$row['NAMA_KATEGORI'];
                    $pqty=$row['JUMLAH'];
                    
                    $pqty=number_format($pqty,0,",",",");
                    
                    if ($ilewat==true) {
                        echo "<tr>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                    }
                    echo "<td nowrap>$pnmkat</td>";
                    echo "<td nowrap>$pnmbrg</td>";
                    echo "<td nowrap align='right'>$pqty</td>";
                    
                    
                    if ($ilewat==false) {
                        echo "<td nowrap $pstylests>&nbsp;</td>";//staus
                    }else{
                        echo "<td nowrap></td>";//staus
                    }
                    
                    echo "</tr>";
                    
                    $ilewat=true;
                    

                }

            }
            ?>
        </tbody>
    </table>
    
    
    
    <table id='mydatatable1' class='table-striped table-bordered'>
        <tr><td style='background-color:#F0FFFF;'>&nbsp; &nbsp; &nbsp; &nbsp;</td><td>&nbsp;Belum Proses</td></tr>
        <tr><td style='background-color:yellow;'>&nbsp; &nbsp; &nbsp; &nbsp;</td><td>&nbsp;Approve PM</td></tr>
        <tr><td style='background-color:green;'>&nbsp; &nbsp; &nbsp; &nbsp;</td><td>&nbsp;Sudah dikirim</td></tr>
        <tr><td style='background-color:blue;'>&nbsp; &nbsp; &nbsp; &nbsp;</td><td>&nbsp;Sudah diterima Cabang</td></tr>
        <tr><td style='background-color:red;'>&nbsp; &nbsp; &nbsp; &nbsp;</td><td>&nbsp;Reject/Batal</td></tr>
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
                    { className: "text-right", "targets": [7] },//right
                    { className: "text-nowrap", "targets": [0,1,2,3,4,5,6,7] }//nowrap

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
    mysqli_close($cnmy);
?>