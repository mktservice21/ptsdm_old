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
        header("Content-Disposition: attachment; filename=Laporan_Data_Stock.xls");
    }
    
    include("config/koneksimysqli_ms.php");
    
    $printdate= date("d/m/Y");
?>


<?PHP
    $pdivisipil = $_POST['cb_divisi'];
    $pprodpil = $_POST['cb_produk'];
    $tgl01 = $_POST['e_periode01'];
    
    $pchked="";
    if (isset($_POST['chk_ed'])) $pchked = $_POST['chk_ed'];
    
    $pblnexpdate = date("Ym", strtotime($tgl01));
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];

    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpstclapdt00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpstclapdt01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpstclapdt02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpstclapdt03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpstclapdt04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmpstclapdt05_".$puserid."_$now ";
    
    $filterdivproduk=false;
    $query_prod = "";
    if (!empty($pdivisipil)) {
        $query_prod = "select a.id, a.kdproduk, a.nmproduk, b.divprodid, b.iprodid from sls.imaping_produk a JOIN sls.iproduk b "
                . " on a.iprodid=b.iprodid "
                . " WHERE b.divprodid='$pdivisipil' ";
        if (!empty($pprodpil)) $query_prod .= " AND a.iprodid='$pprodpil'";
    }else{
        if (!empty($pprodpil)) {
            $query_prod = "select a.id, a.kdproduk, a.nmproduk, b.divprodid, b.iprodid from sls.imaping_produk a JOIN sls.iproduk b "
                    . " on a.iprodid=b.iprodid "
                    . " WHERE a.iprodid='$pprodpil' ";
        }
    }
    if (!empty($query_prod)) {
        $query = "CREATE TEMPORARY TABLE $tmp00($query_prod)";
        mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
        
        $query = "ALTER table $tmp00 MODIFY COLUMN id BIGINT(50) NOT NULL AUTO_INCREMENT PRIMARY KEY";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        $query = "CREATE UNIQUE INDEX `unx1` ON $tmp00 (id)";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $filterdivproduk=true;
    }
        
    
    
    $query ="select * from sls.istock WHERE 1=1 ";// WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_'
    if ($filterdivproduk==true) {
        $query .= " AND kdproduk in (select distinct IFNULL(kdproduk,'') FROM $tmp00)";
    }
    if ($pchked=="Y") $query .= " AND DATE_FORMAT(expdate,'%Y%m')='$pblnexpdate' ";
    
    $query = "CREATE TEMPORARY TABLE $tmp01($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $query = "select a.*, b.nmproduk, b.iprodid, c.nama nama_produk, c.divprodid from $tmp01 a LEFT JOIN sls.imaping_produk b on a.kdproduk=b.kdproduk "
            . " LEFT JOIN sls.iproduk c on b.iprodid=c.iprodid";
    $query = "CREATE TEMPORARY TABLE $tmp02($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $query = "select distinct sysnow from $tmp01 WHERE IFNULL(sysnow,'')<>''";
    $tampilk=mysqli_query($cnms, $query);
    $nx= mysqli_fetch_array($tampilk);
    
    $plastupdate=$nx['sysnow'];
    if ($plastupdate=="0000-00-00 00:00:00") $plastupdate="";
    if (!empty($plastupdate)) $plastupdate=date("d/m/Y H:i:s", strtotime($plastupdate));;
    
    if (!empty($plastupdate)) $plastupdate=" Update Data Stock : $plastupdate";
?>
<HTML>
<HEAD>
    <title>Laporan Data Stock</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Apr 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
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
        
        
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
    <?PHP } ?>
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>
<BODY class="nav-md">
    
    <div class='modal fade' id='myModal' role='dialog'></div>
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>

    <center><div class='h1judul2'>Laporan Data Stock</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <?PHP
            //echo "<tr> <td>Periode $pstsperiode</td> <td>:</td> <td><b>$myperiode1 s/d. $myperiode2</b></td> </tr>";
            echo "<tr class='miring text2'> <td>view date</td> <td>:</td> <td>$printdate</td> </tr>";
            if ($ppilihrpt!="excel") echo "<tr > <td colspan='3'></td></tr>";
            echo "<tr > <td colspan='3'> <div class='h1judul'>$plastupdate</div> </td></tr>";
            ?>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
    
    <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
            <?PHP if ($ppilihrpt=="excel") { echo "<th>No</th>"; } ?>
            <th align="center" nowrap>Divisi</th>
            <th align="center" nowrap>Kode</th>
            <th align="center" nowrap>Nama</th>
            <th align="center" nowrap>Qty</th>
            <th align="center" nowrap>Batch</th>
            <th align="center" nowrap>Expired Date</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $totalqty=0;
                $no=1;
                $query = "select * from $tmp02 order by divprodid, nmproduk, kdproduk";
                $tampil1= mysqli_query($cnms, $query);
                $ketemu1= mysqli_num_rows($tampil1);
                $jmlrec1=$ketemu1;
                if ($ketemu1>0) {
                    while ($row1= mysqli_fetch_array($tampil1)) {
                        $nfile0=$row1['divprodid'];
                        $pnmdivisi=$nfile0;
                        if ($nfile0=="PEACO") $pnmdivisi="PEACOK";
                        if ($nfile0=="PIGEO") $pnmdivisi="PIGEON";
                        if ($nfile0=="CAN") $pnmdivisi="CANARY";
                        
                        $nfile1=$row1['kdproduk'];
                        $nfile2="";
                        $nfile3=$row1['nmproduk'];
                        $nfile4=$row1['qty'];
                        $nfile5="";
                        $nfile6=$row1['nobatch'];
                        $nfile7=$row1['expdate'];
                        $nfile8=$row1['iprodid'];
                        $nfile9=$row1['nama_produk'];


                        if ($nfile7=="0000-00-00") $nfile7="";

                        if (!empty($nfile7)) $nfile7_b = date("F Y", strtotime($nfile7));
                        if (!empty($nfile7)) $nfile7_ = date("Y-m", strtotime($nfile7));

                        if (empty($nfile4)) $nfile4=0;

                        $totalqty=(double)$totalqty+(double)$nfile4;
                        $nfile4=number_format($nfile4,0,",",",");


                        echo "<tr >";
                        if ($ppilihrpt=="excel") { echo "<td nowrap>$no</td>"; }
                        echo "<td nowrap>$pnmdivisi</td>";
                        echo "<td nowrap class='str'>$nfile1</td>";
                        echo "<td nowrap>$nfile3</td>";
                        echo "<td nowrap align='right'>$nfile4</td>";
                        echo "<td nowrap class='str'>$nfile6</td>";
                        if ($ppilihrpt=="excel") echo "<td nowrap align='left'>$nfile7_b</td>";
                        else echo "<td nowrap>$nfile7_ ($nfile7_b)</td>";
                        echo "</tr>";

                        $no++;

                    }
                }
            ?>
        </tbody>
    </table>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
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
        
        
        <script>
        $(document).ready(function() {
            var dataTable = $('#datatable2').DataTable( {
                "bPaginate": false,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                fixedHeader: true,
                /*"ordering": false,*/
                "order": [[ 0, "asc" ]],
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "columnDefs": [
                    { "visible": false },
                    /*{ "orderable": false, "targets": 0 },
                    { "orderable": false, "targets": 1 },*/
                    { className: "text-right", "targets": [3] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2,3] }//nowrap

                ],
                "language": {
                    "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
                }/*,
                "scrollY": 460,
                "scrollX": true*/
            } );
            $('div.dataTables_filter input', dataTable.table().container()).focus();
        } );
        </script>
        
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
              font-size: 120%;
              font-weight: bold;
            }
            .h1judul2 {
              color: blue;
              font-family: verdana;
              font-size: 170%;
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
            th, td {
                padding: 3px;
            }
        </style>
        
        
        <style>
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
            }

            .th2 {
                background: white;
                position: sticky;
                top: 23;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                border-top: 1px solid #000;
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
    
    
    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 130%;
              font-weight: bold;
            }
            .h1judul2 {
              font-size: 150%;
              font-weight: bold;
            }
        </style>
    <?PHP } ?>
</BODY>

</HTML>

<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp05");
    mysqli_close($cnms);
?>