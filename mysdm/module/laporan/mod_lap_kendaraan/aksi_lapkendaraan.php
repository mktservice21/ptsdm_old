<?PHP
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0);
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=DAFTAR KENDARAAN DINAS PT.SDM.xls");
    }
    include("config/koneksimysqli.php");
    
    $figroupuser=$_SESSION['GROUP'];
    
    $ptahun=$_POST['tahun'];
    $pstskendaraan=$_POST['e_ststkendaraan'];
    
    $pchktgl="";
    if (isset($_POST['chktgl'])) $pchktgl=$_POST['chktgl'];
    $ptglstn=$_POST['e_blnstnk'];
    
    $pftglstn="";
    if ($pchktgl=="Y" AND !empty($ptglstn)) {
        $pftglstn=date("Ym", strtotime($ptglstn));
    }
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DBRROTCPBLL01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DBRROTCPBLL02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DBRROTCPBLL03_".$_SESSION['IDCARD']."_$now ";
    $tmp04 =" dbtemp.DBRROTCPBLL04_".$_SESSION['IDCARD']."_$now ";
    
    $query = "select * from dbmaster.t_kendaraan WHERE IFNULL(stsnonaktif,'')<>'Y' ";
    if (!empty($pstskendaraan)) {
        $query .=" AND IFNULL(statuskendaraan,'')='$pstskendaraan'";
    }
    if (!empty($pftglstn)) {
        $query .=" AND DATE_FORMAT(tgltempostnk,'%Y%m')='$pftglstn'";
    }
    $query = "create temporary table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from dbmaster.t_kendaraan_pemakai ";//WHERE IFNULL(stsnonaktif,'')<>'Y'
    $query = "create temporary table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET icabangid='0000000001' WHERE IFNULL(icabangid,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.karyawanid, b.icabangid from $tmp01 a LEFT JOIN "
            . "(select nopol, karyawanid, icabangid from $tmp02 WHERE IFNULL(stsnonaktif,'')<>'Y' AND (tglakhir='0000-00-00' OR IFNULL(tglakhir,'')='') ) as b "
            . "on a.nopol=b.nopol";
    
    $query = "select a.*, b.karyawanid, b.icabangid, b.tglawal, b.tglakhir, b.stsnonaktif stspemakai from $tmp01 a LEFT JOIN $tmp02 b on a.nopol=b.nopol";
    $query = "create temporary table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.nama, c.nama nama_cabang, b.divisiid FROM $tmp03 a "
            . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang c on a.icabangid=c.icabangid ";
    $query = "create temporary table $tmp04 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp04 a JOIN MKT.icabang_o b on a.icabangid=b.icabangid_o SET a.nama_cabang=b.nama WHERE IFNULL(a.divisiid,'')='OTC'";
    mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    /*
    goto hapusdata;
    
    $query = "select a.nopol, d.merk, d.tipe, year(d.tglbeli) tahun, d.statuskendaraan, a.karyawanid, b.nama, a.icabangid, c.nama nama_cabang "
            . " FROM dbmaster.t_kendaraan_pemakai a LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang c on a.icabangid=c.icabangid"
            . " LEFT JOIN dbmaster.t_kendaraan d on a.nopol=d.nopol "
            . " WHERE IFNULL(a.stsnonaktif,'')<>'Y' ";
    $query = "create temporary table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    */
?>

<html>
<head>
    <title>DAFTAR KENDARAAN DINAS PT.SDM</title>
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

<div class='modal fade' id='myModal' role='dialog'></div>

<body class="nav-md">
   
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
<div id='n_content'>

    
    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="10px">&nbsp;</td><td nowrap><b>DAFTAR KENDARAAN DINAS PT.SDM <?php echo $ptahun; ?></b></td></tr>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
    <table>
        <tr style="color:red; font-size: 15px;">
            <td style="background-color:red; padding-right:15px; padding-bottom:5px;">&nbsp;</td><td>&nbsp; &nbsp; <b>Pemakai Non Aktif</b></td>
        </tr>
    </table><br/>
    
    <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr>
                    <th width='6px'>No.</th>
                    <th width='30px'>Area</th>
                    <th width='30px'>Nama Pemakai</th>
                    <th width='30px'>Kendaraan</th>
                    <th width='30px'>THN.</th>
                    <th width='30px'>No.Polisi</th>
                    <th width='30px'>Warna</th>
                    <th width='30px'>Tgl Jatuh Tempo STNK</th>
                    <th width='30px'>Tgl. Awal</th>
                    <th width='30px'>Tgl. Akhir</th>
                    <th width='30px'>Status Kendaraan</th>
                    <th width='70px'>No Rangka</th>
                    <th width='70px'>No Mesin</th>
                    <th width='70px'>Jenis Asuransi</th>
                    <th width='70px'>Nama Asuransi</th>
                    <th width='70px'>No. Polis</th>
                    <th width='70px'>Periode</th>
                    <th width='70px'>s/d.</th>
                    
                </tr>
            </thead>
            <tbody>
            <?PHP
                $no=1;
                $query = "select * from $tmp04 order by merk, nopol, tglawal, nama, karyawanid";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pkaryawanid=$row['karyawanid'];
                    $pnama=$row['nama'];
                    $pidcabang=$row['icabangid'];
                    $pcabang=$row['nama_cabang'];
                    if ($pidcabang=="0000000001") $pcabang="Jakarta";
                    if ($pkaryawanid=="0000000159") $pcabang="Surabaya";
                    $pmerk=$row['merk'];
                    $ptahun=$row['tahun'];
                    $pnopol=$row['nopol'];
                    $pwarna=$row['warna'];
                    $pstskendaraan=$row['statuskendaraan'];
                    
                    $ptglst=$row['tgltempostnk'];
                    
                    $ptglawal=$row['tglawal'];
                    $ptglakhir=$row['tglakhir'];
                    $pstspemakai=$row['stspemakai'];
                    

                    $pnorangka=$row['norangka'];
                    $pnomesin=$row['nomesin'];
                    $pjnsasuransi=$row['jenis_asuransi'];
                    $pnmasuransi=$row['nama_asuransi'];
                    $nnopolis=$row['nopolis_asuransi'];
                    $nperiode1=$row['polis_periode1'];
                    $nperiode2=$row['polis_periode2'];
                    
                    if ($ptglst=="0000-00-00") $ptglst="";
                    if ($ptglawal=="0000-00-00") $ptglawal="";
                    if ($ptglakhir=="0000-00-00") $ptglakhir="";
                    
                    if (RTRIM(trim($pstskendaraan))=="TIDAKTERPAKAI") $pstskendaraan="TIDAK TERPAKAI";
                    
                    
                    
                    $ptglstnk="";
                    
                    
                    if ($ptglst=="0000-00-00") $ptglst="";
                    if ($nperiode1=="0000-00-00") $nperiode1="";
                    if ($nperiode2=="0000-00-00") $nperiode2="";

                    if (!empty($ptglst)) $ptglstnk=date("d/m/Y", strtotime($ptglst));
                    if (!empty($nperiode1)) $nperiode1= date('d/m/Y', strtotime($nperiode1));
                    if (!empty($nperiode2)) $nperiode2= date('d/m/Y', strtotime($nperiode2));
                                                
                                                
                    
                    $nadd_nopol="<button type='button' class='btn btn-default btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"HistoryPemakai('$pnopol')\">$pnopol</button>";
                    if ($ppilihrpt=="excel") {
                        
                    }
                    $nadd_nopol=$pnopol;
                    
                    if (!empty($ptglakhir)) $pstspemakai="Y";
                    $pwarnatr="";
                    if ($pstspemakai=="Y") {
                        $pwarnatr=" style='color:red; font-weight:bold;'";
                    }
                    
                    echo "<tr $pwarnatr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pcabang</td>";
                    echo "<td nowrap>$pnama</td>";
                    echo "<td nowrap>$pmerk</td>";
                    echo "<td nowrap>$ptahun</td>";
                    echo "<td nowrap>$nadd_nopol</td>";
                    echo "<td nowrap>$pwarna</td>";
                    echo "<td nowrap>$ptglstnk</td>";
                    echo "<td nowrap>$ptglawal</td>";
                    echo "<td nowrap>$ptglakhir</td>";
                    echo "<td nowrap>$pstskendaraan</td>";
                    echo "<td>$pnorangka</td>";
                    echo "<td>$pnomesin</td>";
                    echo "<td>$pjnsasuransi</td>";
                    echo "<td>$pnmasuransi</td>";
                    echo "<td nowrap>$nnopolis</td>";
                    echo "<td>$nperiode1</td>";
                    echo "<td>$nperiode2</td>";
                    echo "</tr>";
                        
                    $no++;
                }
                
                
            ?>
            </tbody>
        </table>
        <?PHP if ($ppilihrpt!="excel") { ?>
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
        
        <script>
            function HistoryPemakai(enopol){
                $.ajax({
                    type:"post",
                    url:"module/laporan/mod_lap_kendaraan/datapemakain.php?module=viewdatapemakai",
                    data:"unopol="+enopol,
                    success:function(data){
                        $("#myModal").html(data);
                    }
                });
            }
        </script>
        <?PHP } ?>
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
                    //{ className: "text-right", "targets": [1, 2, 3, 4, 5,6] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
        
        
    </script>

</html>

