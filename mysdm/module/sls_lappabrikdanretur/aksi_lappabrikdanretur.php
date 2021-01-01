<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
	
    $prptpilih=$_POST['e_pilrpt'];
    if ($prptpilih=="R") {
        $ppilihreport="Data Pabrik Retur";
    }else{
        $ppilihreport="Data Pabrik Sales";
    }
    
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=$ppilihreport.xls");
    }
    
    include("config/koneksimysqli_ms.php");
    
    
    
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp01 =" dbtemp.tmplapslsrtrpbk01_".$puserid."_$now ";
    
    if ($prptpilih=="R") {
        $query = "select * from sls.pabrik_retur ";
    }else{
        $query = "select * from sls.pabrik_sales ";
    }
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; exit; }
    

    
?>

<HTML>
<HEAD>
    <title>Pabrik Sales dan Retur</title>
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
    
    <center><div class='h1judul'><?PHP echo $ppilihreport; ?></div></center>
    
    
    <div class="clearfix"></div>
    <hr/>
    <?PHP
    if ($prptpilih=="R") {
        ?>
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th align="center" nowrap>Bukti Retur</th>
                    <th align="center" nowrap>Tgl Faktur</th>
                    <th align="center" nowrap>Kode Customer</th>
                    <th align="center" nowrap>Nama Customer</th>
                    <th align="center" nowrap>Alamat Customer</th>
                    <th align="center" nowrap>Kota</th>
                    <th align="center" nowrap>Kode Barang</th>
                    <th align="center" nowrap>Nama Barang</th>
                    <th align="center" nowrap>No. Batch</th>
                    <th align="center" nowrap>Kuantitas</th>
                    <th align="center" nowrap>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?PHP 
                $no=1;
                $query = "select * from $tmp01";
                $tampil= mysqli_query($cnms, $query);
                while ($row= mysqli_fetch_array($tampil)) {

                    $pnofaktur=$row['bukti_retur'];
                    $ptglfaktur=$row['tgl_retur'];
                    $pkdcust=$row['kdcustomer'];
                    $pnmcust=$row['nama_customer'];
                    $palamat=$row['alamat_customer'];
                    $pkota=$row['kota'];
                    $pkdbarang=$row['kdbarang'];
                    $pnmbarang=$row['nama_barang'];
                    $pnobatch=$row['nobatch'];
                    $pkuantitas=$row['kuantitas_r'];
                    $pket=$row['keterangan'];



                    if (empty($pkuantitas)) $pkuantitas=0;

                    $pkuantitas=number_format($pkuantitas,0,",",",");

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnofaktur</td>";
                    echo "<td nowrap>$ptglfaktur</td>";
                    echo "<td nowrap>$pkdcust</td>";
                    echo "<td nowrap>$pnmcust</td>";
                    echo "<td nowrap>$palamat</td>";
                    echo "<td nowrap>$pkota</td>";
                    echo "<td nowrap>$pkdbarang</td>";
                    echo "<td nowrap>$pnmbarang</td>";
                    echo "<td nowrap>$pnobatch</td>";

                    echo "<td nowrap align='right'>$pkuantitas</td>";
                    echo "<td nowrap>$pket</td>";
                    echo "</tr>";


                    $no++;
                }



                ?>
            </tbody>
        </table>
        <?PHP
    }else{
        ?>
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th align="center" nowrap>Bukti / No. Faktur</th>
                    <th align="center" nowrap>Tgl Faktur</th>
                    <th align="center" nowrap>Kode Customer</th>
                    <th align="center" nowrap>Nama Customer</th>
                    <th align="center" nowrap>Alamat Customer</th>
                    <th align="center" nowrap>Kota</th>
                    <th align="center" nowrap>Kode Barang</th>
                    <th align="center" nowrap>Nama Barang</th>
                    <th align="center" nowrap>No. Batch</th>
                    <th align="center" nowrap>Kuantitas</th>
                    <th align="center" nowrap>Kuantitas Bonus</th>
                    <th align="center" nowrap>Nilai Bonus</th>
                    <th align="center" nowrap>Harga</th>
                    <th align="center" nowrap>Disc %</th>
                    <th align="center" nowrap>Disc Rp.</th>
                    <th align="center" nowrap>Jumlah Rp.</th>
                    <th align="center" nowrap>Disc Tambah %</th>
                    <th align="center" nowrap>Disc Tambah Rp.</th>
                    <th align="center" nowrap>Jumlah Netto</th>

                </tr>
            </thead>
            <tbody>
                <?PHP 
                $no=1;
                $query = "select * from $tmp01";
                $tampil= mysqli_query($cnms, $query);
                while ($row= mysqli_fetch_array($tampil)) {

                    $pnofaktur=$row['nofaktur'];
                    $ptglfaktur=$row['tglfaktur'];
                    $pkdcust=$row['kdcustomer'];
                    $pnmcust=$row['nama_customer'];
                    $palamat=$row['alamat_customer'];
                    $pkota=$row['kota'];
                    $pkdbarang=$row['kdbarang'];
                    $pnmbarang=$row['nama_barang'];
                    $pnobatch=$row['nobatch'];
                    $pkuantitas=$row['kuantitas'];
                    $pkuantitasbonus=$row['kuantitas_b'];
                    $pbonus=$row['bonus'];
                    $pharga=$row['harga'];
                    $pdiscp=$row['disc_p'];
                    $pdiscrp=$row['disc_rp'];
                    $pjumlahrp=$row['jumlahrp'];
                    $pdisct=$row['disc_t'];
                    $pdistctrp=$row['disc_tr'];
                    $pjumlahnet=$row['jumlah_net'];



                    if (empty($pkuantitas)) $pkuantitas=0;

                    $pkuantitas=number_format($pkuantitas,0,",",",");
                    $pkuantitasbonus=number_format($pkuantitasbonus,0,",",",");
                    $pbonus=number_format($pbonus,0,",",",");
                    $pharga=number_format($pharga,0,",",",");
                    $pdiscp=number_format($pdiscp,2,".",",");
                    $pdiscrp=number_format($pdiscrp,0,",",",");
                    $pjumlahrp=number_format($pjumlahrp,0,",",",");
                    $pdisct=number_format($pdisct,2,".",",");
                    $pdistctrp=number_format($pdistctrp,0,",",",");
                    $pjumlahnet=number_format($pjumlahnet,0,",",",");

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnofaktur</td>";
                    echo "<td nowrap>$ptglfaktur</td>";
                    echo "<td nowrap>$pkdcust</td>";
                    echo "<td nowrap>$pnmcust</td>";
                    echo "<td nowrap>$palamat</td>";
                    echo "<td nowrap>$pkota</td>";
                    echo "<td nowrap>$pkdbarang</td>";
                    echo "<td nowrap>$pnmbarang</td>";
                    echo "<td nowrap>$pnobatch</td>";

                    echo "<td nowrap align='right'>$pkuantitas</td>";
                    echo "<td nowrap align='right'>$pkuantitasbonus</td>";
                    echo "<td nowrap align='right'>$pbonus</td>";
                    echo "<td nowrap align='right'>$pharga</td>";
                    echo "<td nowrap align='right'>$pdiscp</td>";
                    echo "<td nowrap align='right'>$pdiscrp</td>";
                    echo "<td nowrap align='right'>$pjumlahrp</td>";
                    echo "<td nowrap align='right'>$pdisct</td>";
                    echo "<td nowrap align='right'>$pdistctrp</td>";
                    echo "<td nowrap align='right'>$pjumlahnet</td>";
                    echo "</tr>";


                    $no++;
                }



                ?>
            </tbody>
        </table>
        <?PHP
    }
    ?>

    
    
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
</HTML>

<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");
    mysqli_close($cnms);
?>