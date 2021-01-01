<?PHP
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0);
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN BIAYA RUTIN OTC PER TAHUN.xls");
    }
    include("config/koneksimysqli.php");
    
    $figroupuser=$_SESSION['GROUP'];
    
    $ptahun=$_POST['tahun'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DBRROTCPBLLO01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DBRROTCPBLLO02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DBRROTCPBLLO03_".$_SESSION['IDCARD']."_$now ";
    $tmp04 =" dbtemp.DBRROTCPBLLO04_".$_SESSION['IDCARD']."_$now ";
    
    
    $query = "select idrutin, bulan, karyawanid, nama_karyawan, divisi, icabangid, jumlah from dbmaster.t_brrutin0 WHERE IFNULL(stsnonaktif,'')<>'Y' "
            . " AND kode='1' AND YEAR(bulan)='$ptahun' AND divisi='OTC'";//AND ifnull(tgl_fin,'') <> '' AND ifnull(tgl_fin,'0000-00-00') <> '0000-00-00' 
    $query = "create temporary table $tmp01 ($query)";    
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.nama from $tmp01 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid";
    $query = "create temporary table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a set a.nama=a.nama_karyawan, karyawanid=idrutin WHERE karyawanid='$_SESSION[KRYNONE]'";
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select distinct divisi, karyawanid, nama from $tmp02";
    $query = "create temporary table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $n_filed_add="";
    for($xi=1;$xi<=12;$xi++) {
        $n_filed_add .=" ADD COLUMN bln_".$xi." DECIMAL(20,2),";
    }
    $n_filed_add .=" ADD COLUMN vtotal DECIMAL(20,2)";
    
    $query = "ALTER TABLE $tmp03 $n_filed_add";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    for($xi=1;$xi<=12;$xi++) {
        $fbulan=$ptahun."0".$xi;
        if ((double)$xi >=10) $fbulan=$ptahun."".$xi;
        $n_filed_add = "bln_".$xi;
        
        $query = "UPDATE $tmp03 a SET a.$n_filed_add=IFNULL((select sum(jumlah) jumlah FROM $tmp02 b WHERE a.divisi=b.divisi AND a.karyawanid=b.karyawanid AND DATE_FORMAT(bulan,'%Y%m')='$fbulan'),0)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query="DELETE FROM $tmp02 WHERE DATE_FORMAT(bulan,'%Y%m')='$fbulan'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
    $query = "UPDATE $tmp03 SET vtotal=bln_1+bln_2+bln_3+bln_4+bln_5+bln_6+bln_7+bln_8+bln_9+bln_10+bln_11+bln_12";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
     
        
    $query = "ALTER TABLE $tmp03 ADD COLUMN icabangid CHAR(10)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "UPDATE $tmp03 a SET a.icabangid=(select icabangid from $tmp01 b WHERE a.karyawanid=b.karyawanid AND IFNULL(b.icabangid,'')<>'' LIMIT 1)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //update cabang other
    $query = "UPDATE $tmp03 a SET a.icabangid=(select icabangid from $tmp01 b WHERE a.karyawanid=b.idrutin AND IFNULL(b.icabangid,'')<>'' LIMIT 1) WHERE IFNULL(a.icabangid,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.*, b.nama nama_cabang, b.region from $tmp03 a LEFT JOIN MKT.icabang_o b on a.icabangid=b.icabangid_o";
    $query = "create temporary table $tmp04 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    function format_num_khusus($n_grp, $jumlah_rp) {
        $pjumlah_=$jumlah_rp;
        if ($n_grp=="28") $pjumlah_=number_format($jumlah_rp,0,".",".");
        else $pjumlah_=number_format($jumlah_rp,0,",",",");

        return $pjumlah_;
    }
?>

<html>
<head>
    <title>LAPORAN BIAYA RUTIN OTC PER TAHUN</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

        <!-- Datatables -->
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <!-- jQuery -->
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/jquery/dist/jquery.min.js"></script>
        
        
    <?PHP } ?>
    
</head>

<body class="nav-md">
   
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
<div id='n_content'>

    
    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>LAPORAN BIAYA RUTIN OTC PER TAHUN JANUARI - DESEMBER <?php echo $ptahun; ?></b></td></tr>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
    
    <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                    <th width='60px'>NAMA</th>
                    <th width='60px'>CABANG</th>
                    <th width='30px'>Jan</th>
                    <th width='30px'>Feb</th>
                    <th width='30px'>Mar</th>
                    <th width='30px'>Apr</th>
                    <th width='30px'>Mei</th>
                    <th width='30px'>Jun</th>
                    <th width='30px'>Jul</th>
                    <th width='30px'>Agust</th>
                    <th width='30px'>Sept</th>
                    <th width='30px'>Okt</th>
                    <th width='30px'>Nov</th>
                    <th width='30px'>Des</th>
                    <th width='30px'>Total</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $n_tot1=0; $n_tot2=0; $n_tot3=0; $n_tot4=0; $n_tot5=0; $n_tot6=0; 
                $n_tot7=0; $n_tot8=0; $n_tot9=0; $n_tot10=0; $n_tot11=0; $n_tot12=0; 
                
                $d_tot1=0; $d_tot2=0; $d_tot3=0; $d_tot4=0; $d_tot5=0; $d_tot6=0; 
                $d_tot7=0; $d_tot8=0; $d_tot9=0; $d_tot10=0; $d_tot11=0; $d_tot12=0; 
            
                $no=1;
                
                $query = "select distinct region FROM $tmp04 order by region";
                $tampil_r= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil_r)) {
                    $pregion=$row1['region'];
                    $pnmregion="BARAT";
                    if ($pregion=="T") $pnmregion="TIMUR";
                    
                    echo "<tr style='background:yellow;'>";
                    echo "<td nowrap><b>$pnmregion</b></td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "</tr>";
                    
                    $no=1;
                    
                    
                    $query = "select karyawanid, nama, nama_cabang, sum(bln_1) bln_1,"
                            . " sum(bln_2) bln_2, sum(bln_3) bln_3, sum(bln_4) bln_4, sum(bln_5) bln_5,"
                            . " sum(bln_6) bln_6, sum(bln_7) bln_7, sum(bln_8) bln_8, sum(bln_9) bln_9,"
                            . " sum(bln_10) bln_10, sum(bln_11) bln_11, sum(bln_12) bln_12, sum(vtotal) vtotal from $tmp04 WHERE region='$pregion' GROUP BY 1,2,3 order by region, nama, karyawanid";
                    $tampil= mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        $pkaryawanid=$row['karyawanid'];
                        $pnama=$row['nama'];
                        $pnmcabang=$row['nama_cabang'];
                        $pjan=$row['bln_1'];
                        $pfeb=$row['bln_2'];
                        $pmar=$row['bln_3'];
                        $papr=$row['bln_4'];
                        $pmei=$row['bln_5'];
                        $pjun=$row['bln_6'];
                        $pjul=$row['bln_7'];
                        $pagu=$row['bln_8'];
                        $psep=$row['bln_9'];
                        $pokt=$row['bln_10'];
                        $pnov=$row['bln_11'];
                        $pdes=$row['bln_12'];

                        $ptotal=$row['vtotal'];

                        $n_tot1=(double)$n_tot1+(double)$pjan;
                        $n_tot2=(double)$n_tot2+(double)$pfeb;
                        $n_tot3=(double)$n_tot3+(double)$pmar;
                        $n_tot4=(double)$n_tot4+(double)$papr;
                        $n_tot5=(double)$n_tot5+(double)$pmei;
                        $n_tot6=(double)$n_tot6+(double)$pjun;
                        $n_tot7=(double)$n_tot7+(double)$pjul;
                        $n_tot8=(double)$n_tot8+(double)$pagu;
                        $n_tot9=(double)$n_tot9+(double)$psep;
                        $n_tot10=(double)$n_tot10+(double)$pokt;
                        $n_tot11=(double)$n_tot11+(double)$pnov;
                        $n_tot12=(double)$n_tot12+(double)$pdes;

                        $pjan=format_num_khusus($figroupuser, $pjan);
                        $pfeb=format_num_khusus($figroupuser, $pfeb);
                        $pmar=format_num_khusus($figroupuser, $pmar);
                        $papr=format_num_khusus($figroupuser, $papr);
                        $pmei=format_num_khusus($figroupuser, $pmei);
                        $pjun=format_num_khusus($figroupuser, $pjun);
                        $pjul=format_num_khusus($figroupuser, $pjul);
                        $pagu=format_num_khusus($figroupuser, $pagu);
                        $psep=format_num_khusus($figroupuser, $psep);
                        $pokt=format_num_khusus($figroupuser, $pokt);
                        $pnov=format_num_khusus($figroupuser, $pnov);
                        $pdes=format_num_khusus($figroupuser, $pdes);



                        $ptotal=format_num_khusus($figroupuser, $ptotal);

                        echo "<tr>";
                        echo "<td nowrap>$pnama</td>";
                        echo "<td nowrap>$pnmcabang</td>";
                        echo "<td nowrap>$pjan</td>";
                        echo "<td nowrap>$pfeb</td>";
                        echo "<td nowrap>$pmar</td>";
                        echo "<td nowrap>$papr</td>";
                        echo "<td nowrap>$pmei</td>";
                        echo "<td nowrap>$pjun</td>";
                        echo "<td nowrap>$pjul</td>";
                        echo "<td nowrap>$pagu</td>";
                        echo "<td nowrap>$psep</td>";
                        echo "<td nowrap>$pokt</td>";
                        echo "<td nowrap>$pnov</td>";
                        echo "<td nowrap>$pdes</td>";
                        echo "<td nowrap>$ptotal</td>";
                        echo "</tr>";

                        $no++;
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "</tr>";
                    
                }
                
                
                $n_gtotal=(double)$n_tot1+(double)$n_tot2+(double)$n_tot3+(double)$n_tot4+
                        (double)$n_tot5+(double)$n_tot6+(double)$n_tot7+(double)$n_tot8+
                        (double)$n_tot9+(double)$n_tot10+(double)$n_tot11+(double)$n_tot12;
                
                $n_tot1=format_num_khusus($figroupuser, $n_tot1);
                $n_tot2=format_num_khusus($figroupuser, $n_tot2);
                $n_tot3=format_num_khusus($figroupuser, $n_tot3);
                $n_tot4=format_num_khusus($figroupuser, $n_tot4);
                $n_tot5=format_num_khusus($figroupuser, $n_tot5);
                $n_tot6=format_num_khusus($figroupuser, $n_tot6);
                $n_tot7=format_num_khusus($figroupuser, $n_tot7);
                $n_tot8=format_num_khusus($figroupuser, $n_tot8);
                $n_tot9=format_num_khusus($figroupuser, $n_tot9);
                $n_tot10=format_num_khusus($figroupuser, $n_tot10);
                $n_tot11=format_num_khusus($figroupuser, $n_tot11);
                $n_tot12=format_num_khusus($figroupuser, $n_tot12);
                
                $n_gtotal=format_num_khusus($figroupuser, $n_gtotal);
                /*
                echo "<tr>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td><td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "</tr>";
                */
                echo "<tr>";
                echo "<td nowrap>TOTAL</td>";
                echo "<td nowrap></td>";
                echo "<td nowrap>$n_tot1</td>";
                echo "<td nowrap>$n_tot2</td>";
                echo "<td nowrap>$n_tot3</td>";
                echo "<td nowrap>$n_tot4</td>";
                echo "<td nowrap>$n_tot5</td>";
                echo "<td nowrap>$n_tot6</td>";
                echo "<td nowrap>$n_tot7</td>";
                echo "<td nowrap>$n_tot8</td>";
                echo "<td nowrap>$n_tot9</td>";
                echo "<td nowrap>$n_tot10</td>";
                echo "<td nowrap>$n_tot11</td>";
                echo "<td nowrap>$n_tot12</td>";
                echo "<td nowrap>$n_gtotal</td>";
                echo "</tr>";
                
                
            ?>
            </tbody>
        </table>
    
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    
    mysqli_close($cnmy);
?>
    
</div>
            


    <?PHP if ($ppilihrpt!="excel") { ?>

        <!-- Datatables -->
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/jszip/dist/jszip.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/pdfmake/build/pdfmake.min.js"></script>
        <script src="https://s3-ap-southeast-1.amazonaws.com/bucketdatasdm/ms/vendors/pdfmake/build/vfs_fonts.js"></script>

        
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

            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 20px;
                /*overflow-x:auto;*/
            }
        </style>

        <style>
            .divnone {
                display: none;
            }
            #datatable2, #datatable3 {
                color:#000;
                font-family: "Arial";
            }
            #datatable2 th, #datatable3 th {
                font-size: 12px;
            }
            #datatable2 td, #datatable3 td { 
                font-size: 11px;
            }
        </style>
        
    <?PHP }else{ ?>
        <style>
            .tjudul {
                font-family: Georgia, serif;
                font-size: 15px;
                margin-left:10px;
                margin-right:10px;
            }
            .tjudul td {
                padding: 4px;
            }
            #datatable2, #datatable3 {
                font-family: Georgia, serif;
                margin-left:10px;
                margin-right:10px;
            }
            #datatable2 th, #datatable2 td, #datatable3 th, #datatable3 td {
                padding: 4px;
            }
            #datatable2 thead, #datatable3 thead{
                background-color:#cccccc; 
                font-size: 12px;
            }
            #datatable2 tbody, #datatable3 tbody{
                font-size: 11px;
            }
        </style>
    <?PHP } ?>
    
</body>

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
            var table = $('#datatable2, #datatable3').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [2, 3, 4, 5,6,7,8,9,10,11,12,13,14] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9,10,11,12,13,14] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
        
        
    </script>

</html>

