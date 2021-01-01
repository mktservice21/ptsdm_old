<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
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
        header("Content-Disposition: attachment; filename=Laporan Realisasi Budget By PM.xls");
    }
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    include("config/common.php");
    
    
    $printdate= date("d/m/Y");
    
    
?>


<?PHP
    
    
    $tgl01 = $_POST['e_tgl1'];
    $tgl02 = $_POST['e_tgl2'];
    
    $pperiode1 = date("Y-m", strtotime($tgl01));
    $pperiode2 = date("Y-m", strtotime($tgl02));
    
    $myperiode1 = date("F Y", strtotime($tgl01));
    $myperiode2 = date("F Y", strtotime($tgl02));

    $filterkode=('');
    if (!empty($_POST['chkbox_kode'])){
        $filterkode=$_POST['chkbox_kode'];
        $filterkode=PilCekBoxAndEmpty($filterkode);
    }
    
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];

    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmprptrealbudgetpm01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprptrealbudgetpm02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmprptrealbudgetpm03_".$puserid."_$now ";
    
    $query = "select brId, noslip, icabangid, tgl, tgltrans, divprodid, COA4, kode, realisasi1, "
            . " jumlah, jumlah1, jumlah jumlah_asli, jumlah1 as jumlah1_asli, "
            . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
            . " dpp, ppn_rp, pph_rp, tgl_fp, CAST('' as CHAR(20)) as nobukti "
            . " from hrd.br0 WHERE IFNULL(batal,'')<>'Y' AND "
            . " brId NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
            . " DATE_FORMAT(tgltrans,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
    $query .=" AND IFNULL(kode,'') IN $filterkode ";
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "CREATE INDEX `norm1` ON $tmp01 (brId,dokterId)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
        
            //via SBY
            $query = "select a.bridinput brId, b.noslip, b.icabangid, b.tgl, a.tgltransfersby tgltrans, b.divprodid, "
                    . " b.COA4, b.kode, b.realisasi1, a.jumlah jumlah, a.jumlah jumlah1, a.jumlah jumlah_asli, a.jumlah as jumlah1_asli, "
                    . " b.aktivitas1, b.aktivitas2, b.dokterId, b.dokter, b.karyawanId, b.ccyId, b.tgltrm, b.lampiran, b.ca, "
                    . " b.dpp, b.ppn_rp, b.pph_rp, b.tgl_fp, "
                    . " a.nobukti "
                    . " from dbmaster.t_br0_via_sby a JOIN hrd.br0 b on a.bridinput=b.brId "
                    . " WHERE IFNULL(b.batal,'')<>'Y' AND "
                    . " a.bridinput NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
                    . " DATE_FORMAT(a.tgltransfersby,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' ";
            $query .=" AND IFNULL(kode,'') IN $filterkode ";
            $query = "create TEMPORARY table $tmp02 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "CREATE INDEX `norm1` ON $tmp02 (brId,dokterId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            $query = "DELETE FROM $tmp01 WHERE brId IN (select distinct IFNULL(brId,'') FROM $tmp02)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "INSERT INTO $tmp01 (brId, noslip, icabangid, tgl, tgltrans, divprodid, "
                    . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
                    . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                    . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti) "
                    . " select brId, noslip, icabangid, tgl, tgltrans, divprodid, "
                    . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
                    . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                    . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti "
                    . " from $tmp02 ";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //END via SBY
            
            
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
        
        $query = "select dokterId, nama from hrd.dokter WHERE dokterId IN (select distinct IFNULL(dokterId,'') from $tmp01)";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "CREATE INDEX `norm1` ON $tmp02 (dokterId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            
        
        
        $query = "select a.*, d.nama nama_dokter, e.nama nama_karyawan, b.nama nama_cabang, c.nama nama_kode, f.NAMA4, "
                . " CAST('' as CHAR(50)) as nodivisi, CAST('' as CHAR(50)) as nodivisi1, CAST('' as CHAR(50)) as nodivisi2 "
                . " from $tmp01 a LEFT JOIN mkt.icabang b on a.icabangid=b.icabangid "
                . " LEFT JOIN hrd.br_kode c on a.kode=c.kodeid "
                . " LEFT JOIN $tmp02 d on a.dokterId=d.dokterId"
                . " LEFT JOIN hrd.karyawan e on a.karyawanId=e.karyawanId "
                . " LEFT JOIN dbmaster.coa_level4 f on a.COA4=f.COA4";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
?>



<HTML>
<HEAD>
    <title>Laporan Realiasi Budget By PM</title>
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
    
    <div class='modal fade' id='myModal' role='dialog'></div>
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>
    
    <center><div class='h1judul'>Laporan Realiasi Budget By PM</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Periode</td><td>:</td><td><?PHP echo "<b>$myperiode1 s/d. $myperiode2</b>"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                <th align="center" nowrap>No</th>
                <th align="center" nowrap>Nama Pembuat</th>
                <th align="center" nowrap>Tgl. Transfer</th>
                <th align="center" nowrap>Akun</th>
                <th align="center" nowrap>Keterangan</th>
                <th align="center" nowrap>Nama Dokter</th>
                <th align="center" nowrap>No Slip</th>
                <th align="center" nowrap>Tgl. Terima</th>
                <th align="center" nowrap>Nama Realiasi</th>
                <th align="center" nowrap>Jumlah</th>
                <th align="center" nowrap>Realiasi</th>
                <th align="center" nowrap>Selisih</th>

            </thead>
            <tbody>
                <?PHP
                $pgrandtotal=0;
                $pgrandtotalreal=0;
                $no=1;
                $query = "select distinct DATE_FORMAT(tgltrans,'%Y%m') as tgltrans1, DATE_FORMAT(tgltrans,'%M %Y') as tgltrans2 from $tmp03 order by DATE_FORMAT(tgltrans,'%Y%m')";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $ptbbln=$row1['tgltrans1'];
                    $ntbulan=$row1['tgltrans2'];
                    
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='11'><b>$ntbulan</b></td>";
                    if ($ppilihrpt!="excel") {
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                    }
                    echo "</tr>";
                    
                    $ptotalrp=0;
                    $ptotalrealrp=0;
                    $no=1;
                    
                    $query = "select * from $tmp03 WHERE DATE_FORMAT(tgltrans,'%Y%m')='$ptbbln' order by tgltrans, nama_karyawan, nama_kode";
                    $tampil2=mysqli_query($cnmy, $query);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pnmkaryawan=$row2['nama_karyawan'];
                        $ptgltrans=$row2['tgltrans'];
                        $pnmakun=$row2['nama_kode'];
                        $pketerangan=$row2['aktivitas1'];
                        $pnmdokter=$row2['nama_dokter'];
                        $pnoslip=$row2['noslip'];
                        $ptglterima=$row2['tgltrm'];
                        $pnmrealiasi=$row2['realisasi1'];
                        
                        $pjumlah=$row2['jumlah'];
                        $pjmlreal=$row2['jumlah1'];
                        
                        $ptgltrans=date("d/m/Y", strtotime($ptgltrans));
                        
                        if ($ptglterima=="0000-00-00") $ptglterima="";
                        if (!empty($ptglterima)) $ptglterima=date("d/m/Y", strtotime($ptglterima));
                        
                        if (empty($pjumlah)) $pjumlah=0;
                        if (empty($pjmlreal)) $pjmlreal=0;
                        
                        $ptotalrp=(double)$ptotalrp+(double)$pjumlah;
                        $ptotalrealrp=(double)$ptotalrealrp+(double)$pjmlreal;
                        
                        $pgrandtotal=(double)$pgrandtotal+(double)$pjumlah;
                        $pgrandtotalreal=(double)$pgrandtotalreal+(double)$pjmlreal;
                        
                        $pselisihrp=(double)$pjumlah-(double)$pjmlreal;
                        
                        $pjumlah=number_format($pjumlah,0,",",",");
                        $pjmlreal=number_format($pjmlreal,0,",",",");
                        $pselisihrp=number_format($pselisihrp,0,",",",");
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pnmkaryawan</td>";
                        echo "<td nowrap>$ptgltrans</td>";
                        echo "<td nowrap>$pnmakun</td>";
                        echo "<td>$pketerangan</td>";
                        echo "<td nowrap>$pnmdokter</td>";
                        echo "<td nowrap>$pnoslip</td>";
                        echo "<td nowrap>$ptglterima</td>";
                        echo "<td nowrap>$pnmrealiasi</td>";
                        echo "<td nowrap align='right'>$pjumlah</td>";
                        echo "<td nowrap align='right'>$pjmlreal</td>";
                        echo "<td nowrap align='right'>$pselisihrp</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                    
                    $psubselisihrp=(double)$ptotalrp-(double)$ptotalrealrp;
                    $ptotalrp=number_format($ptotalrp,0,",",",");
                    $ptotalrealrp=number_format($ptotalrealrp,0,",",",");
                    $psubselisihrp=number_format($psubselisihrp,0,",",",");
                    
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='8'><b>Total $ntbulan</b></td>";
                    if ($ppilihrpt!="excel") {
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                        echo "<td class='divnone'></td>";
                    }
                    echo "<td align='right'><b>$ptotalrp</b></td>";
                    echo "<td align='right'><b>$ptotalrealrp</b></td>";
                    echo "<td align='right'><b>$psubselisihrp</b></td>";
                    echo "</tr>";
                    
                }
                
                $pgrandselisihrp=(double)$pgrandtotal-(double)$pgrandtotalreal;
                $pgrandtotal=number_format($pgrandtotal,0,",",",");
                $pgrandtotalreal=number_format($pgrandtotalreal,0,",",",");
                $pgrandselisihrp=number_format($pgrandselisihrp,0,",",",");
                
                echo "<tr>";
                echo "<td></td>";
                echo "<td colspan='8'><b>Grand Total</b></td>";
                if ($ppilihrpt!="excel") {
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone'></td>";
                }
                echo "<td align='right'><b>$pgrandtotal</b></td>";
                echo "<td align='right'><b>$pgrandtotalreal</b></td>";
                echo "<td align='right'><b>$pgrandselisihrp</b></td>";
                echo "</tr>";
                    
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
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_close($cnmy);
?>