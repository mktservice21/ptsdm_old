<?PHP
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0);
    session_start();
    
    include("config/koneksimysqli.php");
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRETRLCLS01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DTBRRETRLCLS02_".$_SESSION['IDCARD']."_$now ";
    
    $pidspg=$_GET['idspg'];
    $pbulan=$_GET['bulan'];
    $pidcab=$_GET['idcab'];
    $pidarea=$_GET['idarea'];
    
    
    $nbln=substr($pbulan,4,2);
    $nthn=substr($pbulan,0,4);
    $ptgl=$nthn."-".$nbln."-01";
    $nperiode= date("F Y", strtotime($ptgl));
    
    $nperiode_inc="";
    $tot_inc=0;
    $pjml_inctambah=0;
    $pjml_inc=0;
    
    $pjml_gaji=0;
    $pjml_um=0;
    $pjml_sewa=0;
    $pjml_pulsa=0;
    $pjml_bbm=0;
    $pjml_parkir=0;
    $pjharikerjasistem=0;
    $pketerangan="";
    
    $pzonaid="";
    
    
    $query = "select a.id_spg, a.nama, a.jabatid, b.penempatan, a.icabangid, a.alokid, b.areaid from MKT.spg a LEFT JOIN"
            . " dbmaster.t_spg_tempat b on a.id_spg=b.id_spg "
            . " where a.id_spg = '$pidspg'";
    $tampil = mysqli_query($cnmy, $query);
    $sp= mysqli_fetch_array($tampil);
    
    $pnmspg=$sp['nama'];
    $pjabatanid=$sp['jabatid'];
    $palokid=$sp['alokid'];
    $ppenempatan=$sp['penempatan'];
    
    //rincian jumlah kerja
    $query = "select * from dbmaster.t_spg_gaji_br0 WHERE id_spg='$pidspg' AND DATE_FORMAT(periode,'%Y%m')='$pbulan' ";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    if ($ketemu==0) {
        echo "Belum ada data....";
        mysqli_close($cnmy);
        exit;
    }
    $sp= mysqli_fetch_array($tampil);
    
    $pjharikerjasistem=$sp['jharikerjasistem'];
    $pjml_hk=$sp['jml_harikerja'];
    $pjml_sakit=$sp['jml_sakit'];
    $pjml_izin=$sp['jml_izin'];
    $pjml_alpa=$sp['jml_alpa'];
    $pjml_uc=$sp['jml_uc'];
    $pjml_inctambah=$sp['insentif_tambahan'];
    
    $nperiodeinc=$sp['periode_insentif'];
    $pketerangan=$sp['keterangan'];
    $p_apv1=$sp['apv1'];
    
    if (!empty($nperiode_inc)) {
        $nperiode_inc= date("F Y", strtotime($nperiodeinc));
    }
    
    $nket_rinci="Estimasi Rincian Gaji SPG";
    if (!empty($p_apv1)) {
        $nket_rinci="Rincian Gaji SPG";
        
        $query = "SELECT DATE_FORMAT(periode,'%Y%m') periode, id_spg,
            icabangid, alokid, areaid, jabatid, id_zona,
            kodeid, rp, sum(rptotal) as rptotal 
            FROM dbmaster.t_spg_gaji_br1 WHERE id_spg='$pidspg' AND DATE_FORMAT(periode,'%Y%m')='$pbulan' 
            GROUP BY 1,2,3,4,5,6,7,8,9";
        $tampil = mysqli_query($cnmy, $query);
        while ($sp= mysqli_fetch_array($tampil)) {
            $pzonaid=$sp['id_zona'];
            $pkodeid=$sp['kodeid'];
            
            if ($pkodeid=="01") $pjml_inc=$sp['rptotal'];
            if ($pkodeid=="07") $pjml_inctambah=$sp['rptotal'];
            if ($pkodeid=="02") $pjml_gaji=$sp['rptotal'];
            if ($pkodeid=="03") $pjml_um=(double)$sp['rptotal']/(double)$pjml_hk;
            if ($pkodeid=="04") $pjml_sewa=$sp['rptotal'];
            if ($pkodeid=="05") $pjml_pulsa=$sp['rptotal'];
            if ($pkodeid=="08") $pjml_bbm=$sp['rptotal'];
            if ($pkodeid=="06") $pjml_parkir=$sp['rptotal'];
        }
        
    }else{
    
        //gaji
        $query = "select a.* from dbmaster.t_spg_gaji_area_zona a WHERE a.icabangid='$pidcab' AND a.areaid='$pidarea' AND "
                . " DATE_FORMAT(a.bulan,'%Y-%m') = (select MAX(DATE_FORMAT(b.bulan,'%Y-%m')) FROM dbmaster.t_spg_gaji_area_zona b WHERE 
                    a.icabangid=b.icabangid AND a.areaid=b.areaid AND a.id_zona=b.id_zona AND b.icabangid='$pidcab' AND b.areaid='$pidarea')";
        $tampil = mysqli_query($cnmy, $query);
        $sp= mysqli_fetch_array($tampil);

        $pzonaid=$sp['id_zona'];
        $pjml_gaji=$sp['gaji'];

        //uang makan
        $query = "select a.* from dbmaster.t_spg_gaji_zona_jabatan a WHERE a.id_zona='$pzonaid' AND a.jabatid='$pjabatanid' AND "
                . " DATE_FORMAT(a.bulan,'%Y-%m') = (select MAX(DATE_FORMAT(b.bulan,'%Y-%m')) FROM dbmaster.t_spg_gaji_zona_jabatan b WHERE 
                    a.id_zona=b.id_zona AND a.jabatid=b.jabatid AND b.id_zona='$pzonaid' AND b.jabatid='$pjabatanid')";
        $tampil = mysqli_query($cnmy, $query);
        $sp= mysqli_fetch_array($tampil);

        $pjml_um=$sp['umakan'];

        //tunjangan
        $query = "select a.* from dbmaster.t_spg_gaji_jabatan a WHERE a.jabatid='$pjabatanid' AND "
                . " DATE_FORMAT(a.bulan,'%Y-%m') = (select MAX(DATE_FORMAT(b.bulan,'%Y-%m')) FROM dbmaster.t_spg_gaji_jabatan b WHERE 
                    a.jabatid=b.jabatid AND b.jabatid='$pjabatanid')";
        $tampil = mysqli_query($cnmy, $query);
        $sp= mysqli_fetch_array($tampil);

        $pjml_sewa=$sp['sewakendaraan'];
        $pjml_pulsa=$sp['pulsa'];
        $pjml_bbm=$sp['bbm'];
        $pjml_parkir=$sp['parkir'];
    
    }
    
    $query = "select nama_jabatan from dbmaster.t_spg_jabatan WHERE jabatid='$pjabatanid'";
    $sp= mysqli_fetch_array(mysqli_query($cnmy, $query));
    $pnmjabatan=$sp['nama_jabatan'];
    
    $query = "select nama from mkt.icabang_o WHERE icabangid_o='$pidcab'";
    $sp= mysqli_fetch_array(mysqli_query($cnmy, $query));
    $pnmcab=$sp['nama'];
    
    $query = "select nama from mkt.iarea_o WHERE areaid_o='$pidarea' and icabangid_o='$pidcab'";
    $sp= mysqli_fetch_array(mysqli_query($cnmy, $query));
    $pnmarea=$sp['nama'];
    
    $query = "select nama_zona from dbmaster.t_zona WHERE id_zona='$pzonaid'";
    $sp= mysqli_fetch_array(mysqli_query($cnmy, $query));
    $pnmzona=$sp['nama_zona'];
    
    $tot_inc=(double)$pjml_inctambah+(double)$pjml_inc;
    //echo "Jsistem : $pjharikerjasistem, Gaji : $pjml_gaji, Umakan : $pjml_um, TJ : $pjml_sewa, $pjml_pulsa, $pjml_bbm, $pjml_parkir";
?>

<html>
<head>
    <title>RINCIAN GAJI SPG <?PHP echo $pnmspg; ?></title>
    
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
        
        
    
    
</head>

<body class="nav-md">
   
    
<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

    
<div id='n_content'>

    
    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td> <td>&nbsp;</td></tr>
                <tr><td width="210px"><?PHP echo "<b>$nket_rinci</b>"; ?></td> <td>&nbsp;</td></tr>
                <tr><td width="210px"><b>Nama : </b></td> <td><?PHP echo "$pnmspg"; ?></td></tr>
                <tr><td width="210px"><b>Jabatan : </b></td> <td><?PHP echo "$pnmjabatan"; ?></td></tr>
                <tr><td width="210px"><b>Cabang : </b></td> <td><?PHP echo "$pnmcab"; ?></td></tr>
                <tr><td width="210px"><b>Area : </b></td> <td><?PHP echo "$pnmarea"; ?></td></tr>
                <tr><td width="210px"><b>Zona : </b></td> <td><?PHP echo "$pnmzona"; ?></td></tr>
                <tr><td width="210px"><b>Penempatan : </b></td> <td><?PHP echo "$ppenempatan"; ?></td></tr>
                <?PHP
                if (empty($p_apv1)) {
                    ?><tr><td width="210px"><b><u>Belum termasuk insentif</u></b></td> <td>&nbsp;</td></tr><?PHP
                }else{
                    ?><tr><td width="210px"><b><u>Sudah termasuk insentif</u></b></td> <td>&nbsp;</td></tr><?PHP
                }
                ?>
                <tr><td width="210px" colspan="2"><b>Jumlah Hari Kerja Bulan January 2019 : Hari <?PHP echo "$pjharikerjasistem"; ?></td></tr>
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
                <th align="center" nowrap>Hari Kerja</th>
                <th align="center" nowrap>S</th>
                <th align="center" nowrap>I</th>
                <th align="center" nowrap>A</th>
                <th align="center" nowrap>UC</th>
                <th align="center" nowrap>Insentif <?PHP echo $nperiode_inc; ?></th>
                <th align="center" nowrap>Gaji Pokok</th>
                <th align="center" nowrap>Sewa Kendaraan</th>
                <th align="center" nowrap>Pulsa</th>
                <th align="center" nowrap>BBM</th>
                <th align="center" nowrap>Parkir</th>
                <th align="center" nowrap>GP & Tunjangan</th>
                <th align="center" nowrap>U. Makan</th>
                <!--<th align="center" nowrap>T. Makan</th>-->
                <th align="center" nowrap>Total</th>
                <th align="center" nowrap>Keterangan</th>
            </thead>
            <tbody>
            <?PHP
                
                $j_hari_gp=(double)$pjml_hk+(double)$pjml_sakit+(double)$pjml_uc;
                
                if ((double)$j_hari_gp<(double)$pjharikerjasistem) {
                    $tot_gp=(double)$j_hari_gp/(double)$pjharikerjasistem*(double)$pjml_gaji;
                    
                    if ((double)$pjml_sewa >0) $pjml_sewa=(double)$j_hari_gp/(double)$pjharikerjasistem*(double)$pjml_sewa;
                    if ((double)$pjml_pulsa >0) $pjml_pulsa=(double)$j_hari_gp/(double)$pjharikerjasistem*(double)$pjml_pulsa;
                    if ((double)$pjml_bbm >0) $pjml_bbm=(double)$j_hari_gp/(double)$pjharikerjasistem*(double)$pjml_bbm;
                    if ((double)$pjml_parkir >0) $pjml_parkir=(double)$j_hari_gp/(double)$pjharikerjasistem*(double)$pjml_parkir;
                    
                }else{
                    $tot_gp=$pjml_gaji;
                }
                
                
                $tot_gp_tj=(double)$tot_gp+(double)$pjml_sewa+(double)$pjml_pulsa+(double)$pjml_bbm+(double)$pjml_parkir;
                
                $totmakan=(double)$pjml_hk*(double)$pjml_um;
                
                $grand_tot=(double)$tot_gp_tj+(double)$totmakan+(double)$tot_inc;
                
                
                $pjml_um=number_format($pjml_um,0,",",",");
                $totmakan=number_format($totmakan,0,",",",");
                $pjml_sewa=number_format($pjml_sewa,0,",",",");
                $pjml_pulsa=number_format($pjml_pulsa,0,",",",");
                $pjml_bbm=number_format($pjml_bbm,0,",",",");
                $pjml_parkir=number_format($pjml_parkir,0,",",",");
                $tot_gp=number_format($tot_gp,0,",",",");
                $tot_gp_tj=number_format($tot_gp_tj,0,",",",");
                $tot_inc=number_format($tot_inc,0,",",",");
                $grand_tot=number_format($grand_tot,0,",",",");
                
                
                echo "<tr>";
                echo "<td nowrap>$pjml_hk</td>";
                echo "<td nowrap>$pjml_sakit</td>";
                echo "<td nowrap>$pjml_izin</td>";
                echo "<td nowrap>$pjml_alpa</td>";
                echo "<td nowrap>$pjml_uc</td>";
                echo "<td nowrap>$tot_inc</td>";
                echo "<td nowrap>$tot_gp</td>";
                echo "<td nowrap>$pjml_sewa</td>";
                echo "<td nowrap>$pjml_pulsa</td>";
                echo "<td nowrap>$pjml_bbm</td>";
                echo "<td nowrap>$pjml_parkir</td>";
                echo "<td nowrap><b>$tot_gp_tj</b></td>";
                echo "<td nowrap>$pjml_um x $pjml_hk = $totmakan</td>";
                //echo "<td nowrap>$totmakan</td>";
                echo "<td nowrap align='right'><b>$grand_tot</b></td>";
                echo "<td nowrap>$pketerangan</td>";
                echo "</tr>";
            ?>
            </tbody>
        </table>

    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    mysqli_close($cnmy);
?>
    
</div>
            


   

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
            #datatable2 {
                color:#000;
                font-family: "Arial";
            }
            #datatable2 th {
                font-size: 12px;
            }
            #datatable2 td { 
                font-size: 11px;
            }
        </style>
    
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
            var table = $('#datatable2').DataTable({
                fixedHeader: false,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                "bPaginate": false
            } );

        } );
        
        
    </script>

</html>

