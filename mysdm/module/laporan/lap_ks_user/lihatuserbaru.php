<?php

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
        header("Content-Disposition: attachment; filename=Rekap Data User Baru.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/koneksimysqli_ms.php");
    include "config/fungsi_combo.php";
    include "config/fungsi_sql.php";
    include("config/common.php");
    
    
    $printdate= date("d/m/Y");
    
    
?>

<?PHP
$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp00 =" dbtemp.tmplapmnksurnwd00_".$puserid."_$now ";
$tmp01 =" dbtemp.tmplapmnksurnwd01_".$puserid."_$now ";

$pidkaryawan=$_GET['ikar'];
$piddokt=$_GET['iusr'];
$pidsr=$_GET['isr'];

$filtersrid="";

$ppilsrid = explode(",", $pidsr);
$pjml= count($ppilsrid);
for($ix=0;$ix<7;$ix++) {
    if (isset($ppilsrid[$ix])) $filtersrid .="'".$ppilsrid[$ix]."',";
    
    
}

$nmkaryawan="";
$pjbtid="";
$filterkaryawan="";


if (!empty($pidkaryawan)) {
    $filterkaryawan="'".$pidkaryawan."',";
    
    
    $query = "select karyawanid as karyawanid, nama as nama, jabatanid as jabatanid from hrd.karyawan where karyawanId='$pidkaryawan'";
    $tampilk=mysqli_query($cnmy, $query);
    $krow= mysqli_fetch_array($tampilk);
    
    $nmkaryawan=$krow['nama'];
    $pjbtid=$krow['jabatanid'];
}

$nmdokter= getfield("select nama as lcfields from hrd.dokter where dokterid='$piddokt'");
$ppilihdokt="";
if (!empty($piddokt)) {
    $ppilihdokt="($nmdokter)";
}


$filtercabang="";
if ($pjbtid=="08") {
    
    $query = "select distinct icabangid as icabangid from MKT.idm0 WHERE karyawanid='$pidkaryawan'";
    $tampil=mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $nicabid=$row['icabangid'];
        
        $filtercabang .="'".$nicabid."',";
    }
    
}elseif ($pjbtid=="10" OR $pjbtid=="18") {
    
    $query = "select distinct icabangid as icabangid, areaid as areaid, divisiid as divisiid from MKT.ispv0 WHERE karyawanid='$pidkaryawan'";
    $tampil=mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $nicabid=$row['icabangid'];
        $niareaid=$row['areaid'];
        $nidivisiid=$row['divisiid'];
        
        //$filtercabang .="'".$nicabid."".$niareaid."".$nidivisiid."',";
        $filtercabang .="'".$nicabid."',";
    }
    
}else{
    
    if (!empty($pidkaryawan)) {
        $query = "select distinct icabangid as icabangid, areaid as areaid, divisiid as divisiid from MKT.imr0 WHERE karyawanid='$pidkaryawan'";
        $tampil=mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $nicabid=$row['icabangid'];
            $niareaid=$row['areaid'];
            $nidivisiid=$row['divisiid'];

            //$filtercabang .="'".$nicabid."".$niareaid."".$nidivisiid."',";
            $filtercabang .="'".$nicabid."',";
        }
    }
    
}

if (empty($pidkaryawan)) {
    
    $filtercabang="";
    
    if (!empty($filtersrid)) {
        $filtersrid="(".substr($filtersrid, 0, -1).")";
    }else{
        $filtersrid="('')";
    }

    $psudah=false;
    $query = "select distinct icabangid as icabangid from MKT.idm0 WHERE karyawanid IN $filtersrid";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        while ($row= mysqli_fetch_array($tampil)) {
            $nicabid=$row['icabangid'];

            $filtercabang .="'".$nicabid."',";
            $psudah=true;
        }
    }
    
    //if ($psudah==false) {
        $query = "select distinct icabangid as icabangid from MKT.ispv0 WHERE karyawanid IN $filtersrid";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            while ($row= mysqli_fetch_array($tampil)) {
                $nicabid=$row['icabangid'];

                $filtercabang .="'".$nicabid."',";
                $psudah=true;
            }
        }
    //}
    
    //if ($psudah==false) {
        $query = "select distinct icabangid as icabangid from MKT.imr0 WHERE karyawanid IN $filtersrid";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            while ($row= mysqli_fetch_array($tampil)) {
                $nicabid=$row['icabangid'];

                $filtercabang .="'".$nicabid."',";
                $psudah=true;
            }
        }
    //}
    
    
}

if (!empty($filtercabang)) {
    $filtercabang="(".substr($filtercabang, 0, -1).")";
}

//echo "CAB : $filtercabang<br/>SRID : $filtersrid<br/>KRY : $pidkaryawan"; goto hapusdata;

$query = "SELECT * FROM dr.masterdokter WHERE 1=1";
if (!empty($filtercabang)) {
    $query .=" AND icabangid IN $filtercabang";
}
$query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
mysqli_query($cnms, $query);
$erropesan = mysqli_error($cnms); if (!empty($erropesan)) {  echo "Error CREATE TABLE : $erropesan"; goto hapusdata; }
    

?>

<HTML>
<HEAD>
    <title>Rekap Data User Baru</title>
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

    <center><div class='h1judul'>Rekap Data User Baru</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th width='10px'>No</th>
                <th align="center" nowrap>ID</th>
                <th align="center" nowrap>Gelar</th>
                <th align="center" nowrap>Nama Lengkap</th>
                <th align="center" nowrap>Spesialis</th>
                <th align="center" nowrap>No. Hp</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $query = "select * from $tmp01 ORDER BY namalengkap";
            $tampil1= mysqli_query($cnms, $query);
            $ketemu1= mysqli_num_rows($tampil1);
            $jmlrec1=$ketemu1;
            if ($ketemu1>0) {
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $nfile0=$row1['id'];
                    $nfile1=$row1['gelar'];
                    $nfile2=$row1['namalengkap'];
                    $nfile3=$row1['spesialis'];
                    $nfile4=$row1['nohp'];

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$nfile0</td>";
                    echo "<td nowrap>$nfile1</td>";
                    echo "<td nowrap>$nfile2</td>";
                    echo "<td nowrap>$nfile3</td>";
                    echo "<td nowrap>$nfile4</td>";
                    echo "</tr>";

                    $no++;

                }
            }
            
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
    
    <script>

        $(document).ready(function() {
            var dataTable = $('#mydatatable1').DataTable( {
                //"bPaginate": false,
                "bLengthChange": false,
                "bFilter": true,
                //"bInfo": false,
                //"ordering": false,
                "order": [[ 0, "desc" ]],
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": 10,
                "columnDefs": [
                    { "visible": false },
                    //{ "orderable": false, "targets": 0 },
                    //{ "orderable": false, "targets": 1 },
                    //{ className: "text-right", "targets": [3, 6] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4,5] }//nowrap

                ],
                "language": {
                    "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
                },
                //"scrollY": 460,
                "scrollX": true
            } );
            $('div.dataTables_filter input', dataTable.table().container()).focus();
        } );

    </script>
    
</HTML>


<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");

    mysqli_close($cnmy);
    mysqli_close($cnms);
?>