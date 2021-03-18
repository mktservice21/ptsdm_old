<?PHP
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0);
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REPORT INCENTIVE.xls");
    }
    
    $figroupuser=$_SESSION['GROUP'];
    
    include("config/koneksimysqli.php");
    
    //$cnmy=$cnms;
    
    $pincfrom="";
    $pnmidspd="";
    $pdivprod="";
    $date1 = date("Y-m-d");
    if (isset($_GET['ispd'])) {
        $pnmidspd=$_GET['ispd'];
        
        $query = "select tglf from dbmaster.t_suratdana_br WHERE idinput='$pnmidspd'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $nr= mysqli_fetch_array($tampil);
            if (!empty($nr['tglf']) AND $nr['tglf']<>"0000-00-00") {
                $date1=$nr['tglf'];
            }
        }
        
    }else{
    
        $pdivprod=$_POST['e_divisi'];
        $pincfrom=$_POST['e_incfrom'];
        $date1=$_POST['e_periode01'];
    }
    $ptgl1= date("Y-m-01", strtotime($date1));
    $ptahun= date("Y", strtotime($date1));
    $per1= date("F Y", strtotime($date1));
    
    $pfilterincfrom=" AND IFNULL(jenis2,'')='$pincfrom' ";
    $pfilterincfrom2=" AND IFNULL(a.jenis2,'')='$pincfrom' ";
    if ($pincfrom=="PM") {
        $pfilterincfrom=" AND IFNULL(jenis2,'') NOT IN ('GSM', '') ";
        $pfilterincfrom2=" AND IFNULL(a.jenis2,'') NOT IN ('GSM', '') ";
    }
    
    if ((INT)$ptahun<=2020) {
        $pfilterincfrom="";
        $pfilterincfrom2="";
    }


    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRETRLCLS01_".$_SESSION['IDCARD']."_$now ";
    
    $sudahproses=false;
    /*
    $query = "SELECT * FROM ms.incentiveperdivisi WHERE bulan='$ptgl1'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $sudahproses=true;
    }
    */
    $now=date("mdYhis");
    $tmp01 =" dbtemp.RTMPPROSINC01_".$_SESSION['USERID']."_$now ";
    
    $sudahproses=true;//ngambil yang sudah diproses saja
    
    if (!empty($pnmidspd)) $sudahproses=true;
    
    if ($sudahproses==false) {
        
        include("config/koneksimysqli_ms.php");
        $cnmy=$cnms;
        
        include "prosesdatainc.php";

        $tmp01 =caridatainsentif_query($cnmy, "", $ptgl1, "", $pdivprod, $pincfrom);

        $query = "SELECT table_name FROM information_schema.tables WHERE table_name='$tmp01'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu==0) {
            goto hapusdata;
        }
        $tmp01="dbtemp.".$tmp01;
        
    }else{
        
        $fildivisi="";
        if (!empty($pdivprod)) $fildivisi=" AND IFNULL(a.divisi,'')='$pdivprod'";
        if ($pdivprod=="blank") $fildivisi=" AND IFNULL(a.divisi,'')=''";
        
        $query = "SELECT CAST(null as DECIMAL(10,0)) as urutan, a.bulan, a.divisi, a.cabang icabangid, b.nama cabang, "
                . " a.jabatan, a.karyawanid, a.nama, a.region, a.jumlah FROM dbmaster.incentiveperdivisi a "
                . " LEFT JOIN mkt.icabang b on a.cabang=b.iCabangId WHERE a.bulan='$ptgl1' $fildivisi $pfilterincfrom2";
        //echo $query; goto hapusdata;
        $query = "create  table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query="UPDATE $tmp01 SET urutan=1 WHERE jabatan='MR'";
        mysqli_query($cnmy, $query);
        $query="UPDATE $tmp01 SET urutan=2 WHERE jabatan='AM'";
        mysqli_query($cnmy, $query);
        $query="UPDATE $tmp01 SET urutan=3 WHERE jabatan='DM'";
        mysqli_query($cnmy, $query);
    }
    
    if ($figroupuser=="28" OR $figroupuser=="3" OR $figroupuser=="25" OR $figroupuser=="23" OR $figroupuser=="26") {
        $query="DELETE FROM $tmp01 WHERE IFNULL(jumlah,0)=0";
        mysqli_query($cnmy, $query);
    }
    
    
    $query="Alter table $tmp01 ADD COLUMN coa CHAR(50), ADD COLUMN nama_coa CHAR(100)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="UPDATE $tmp01 SET coa='705-05' WHERE divisi='CAN'";//, nama_coa='P1-DIN-INSENTIVE CANARY'
    mysqli_query($cnmy, $query);
    
    $query ="UPDATE $tmp01 SET coa='701-05' WHERE divisi='EAGLE'";//, nama_coa='P1-DIN-INSENTIVE EAGLE'
    mysqli_query($cnmy, $query);
    
    $query ="UPDATE $tmp01 SET coa='702-05' WHERE divisi='PIGEO'";//, nama_coa='P2-DIN-INSENTIVE PIGEON'
    mysqli_query($cnmy, $query);
    
    $query ="UPDATE $tmp01 SET coa='703-05' WHERE divisi='PEACO'";//, nama_coa='P3-DIN-INSENTIF PEACOCK'
    mysqli_query($cnmy, $query);
    
    $query ="UPDATE $tmp01 a SET a.nama_coa=(select NAMA4 FROM dbmaster.coa_level4 b WHERE a.coa=b.COA4) WHERE IFNULL(divisi,'')<>''";
    mysqli_query($cnmy, $query);
    
?>

<html>
<head>
    <title>REPORT INCENTIVE</title>
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
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="210px"><b>Report Incentive Per </b></td><td><?PHP echo "$per1 "; ?></td></tr>
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
                    <th width='5px'>NO</th>
                    <th width='50px'>Kode</th>
                    <th width='20px'>Perkiraan</th>
                    <th width='10px'>Region</th>
                    <th width='20px'>Cabang</th>
                    <th width='30px'>Jabatan</th>
                    <th width='50px'>Karyawan</th>
                    <th width='200px'>Jumlah</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $gtotalmr=0;
                $gtotalam=0;
                $gtotaldm=0;
                
                $grand_total=0;
                
                $no=1;
                $query = "select distinct urutan from $tmp01 order by urutan";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $nurutan=$row1['urutan'];
                
                    $query = "select * from $tmp01 WHERE urutan='$nurutan' order by urutan, divisi, region, cabang, nama";
                    $tampil= mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {

                        $pdivisi=$row['divisi'];
                        $ndivisi=$pdivisi;
                        if ($pdivisi=="CAN") $ndivisi="CANARY";
                        
                        $pregion=$row['region'];
                        $pcoa=$row['coa'];
                        $pnmcoa=$row['nama_coa'];
                        $pidcabang=$row['icabangid'];
                        $pnmcabang=$row['cabang'];
                        $pjabatan=$row['jabatan'];
                        $pidkaryawan=$row['karyawanid'];
                        $pnmkaryawan=$row['nama'];
                        $pjumlah=$row['jumlah'];
                        
                        $grand_total=(double)$grand_total+(double)$pjumlah;
                        
                        if ($nurutan=="1") $gtotalmr=(double)$gtotalmr+(double)$pjumlah;
                        if ($nurutan=="2") $gtotalam=(double)$gtotalam+(double)$pjumlah;
                        if ($nurutan=="3") $gtotaldm=(double)$gtotaldm+(double)$pjumlah;
                        
                        if ($figroupuser=="28")
                            $pjumlah=number_format($pjumlah,0,".",".");
                        else
                            $pjumlah=number_format($pjumlah,0,",",",");
                        

                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pcoa</td>";
                        echo "<td nowrap>$pnmcoa</td>";
                        echo "<td nowrap>$pregion</td>";
                        echo "<td nowrap>$pnmcabang</td>";
                        echo "<td nowrap>$pjabatan</td>";
                        echo "<td nowrap>$pnmkaryawan</td>";
                        echo "<td nowrap>$pjumlah</td>";
                        echo "</tr>";

                        $no++;
                    }
                    
                }
                $grand_total=number_format($grand_total,0,",",",");
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>Grand Total</b></td>";
                echo "<td nowrap><b>$grand_total</b></td>";
                echo "</tr>";
            ?>
            </tbody>
        </table>

    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TABLE $tmp01");
    
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
            #datatable2 {
                font-family: Georgia, serif;
                margin-left:10px;
                margin-right:10px;
            }
            #datatable2 th, #datatable2 td {
                padding: 4px;
            }
            #datatable2 thead{
                background-color:#cccccc; 
                font-size: 12px;
            }
            #datatable2 tbody{
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
            var table = $('#datatable2').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [7] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
        
        
    </script>

</html>

