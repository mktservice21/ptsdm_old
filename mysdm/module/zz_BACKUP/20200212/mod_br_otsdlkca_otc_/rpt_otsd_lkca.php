<?PHP
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 0);
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REPORT LK CA OTC.xls");
    }
    
    include("config/koneksimysqli.php");
    
    
    $tglnow = date("Y-m-d");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    $per1 = date("F Y", strtotime($tgl01));
    $per2 = date('F Y', strtotime('1 month', strtotime($tgl01)));
    $periode2 = date('Y-m', strtotime('1 month', strtotime($tgl01)));
    
    $stsreport = $_POST['sts_rpt'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRETRLCLS01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DTBRRETRLCLS02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DTBRRETRLCLS03_".$_SESSION['IDCARD']."_$now ";
    $tmp04 =" dbtemp.DTBRRETRLCLS04_".$_SESSION['IDCARD']."_$now ";
    $tmp05 =" dbtemp.DTBRRETRLCLS05_".$_SESSION['IDCARD']."_$now ";
    $tmp05 =" dbtemp.DTBRRETRLCLS05_".$_SESSION['IDCARD']."_$now ";
    $tmp06 =" dbtemp.DTBRRETRLCLS06_".$_SESSION['IDCARD']."_$now ";
    
    $query = "select a.idrutin, a.karyawanid, b.nama nama_kry, a.divisi, a.jumlah, a.nama_karyawan, a.atasan1, a.atasan2, a.atasan3, a.atasan4 "
            . " from dbmaster.t_brrutin0 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanId WHERE "
            . " IFNULL(stsnonaktif,'')<>'Y' AND kode=2 AND divisi='OTC' AND DATE_FORMAT(bulan,'%Y-%m') = '$periode1'";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.periode, a.idca, a.karyawanid, b.nama nama_kry, divisi, a.jumlah, a.nama_karyawan, a.atasan1, a.atasan2, a.atasan3, a.atasan4 "
            . " from dbmaster.t_ca0 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanId WHERE "
            . " IFNULL(stsnonaktif,'')<>'Y' AND divisi='OTC' "
            . " AND DATE_FORMAT(periode,'%Y-%m') between '$periode1' AND '$periode2'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "update $tmp01 set nama_kry=nama_karyawan WHERE karyawanid='0000002200'"; 
    mysqli_query($cnmy, $query);
    
    $query = "update $tmp01 a JOIN dbmaster.t_karyawan_kontrak b ON a.nama_karyawan=b.nama AND"
            . " a.atasan1=b.atasan1 AND a.atasan2=b.atasan2 AND a.atasan3=b.atasan3 AND a.atasan4=b.atasan4 "
            . " SET a.karyawanid=b.id WHERE a.karyawanid='0000002200'";
    mysqli_query($cnmy, $query);
    
    
    $query = "update $tmp02 set nama_kry=nama_karyawan WHERE karyawanid='0000002200'"; 
    mysqli_query($cnmy, $query);
    
    $query = "update $tmp02 a JOIN dbmaster.t_karyawan_kontrak b ON a.nama_karyawan=b.nama AND"
            . " a.atasan1=b.atasan1 AND a.atasan2=b.atasan2 AND a.atasan3=b.atasan3 AND a.atasan4=b.atasan4 "
            . " SET a.karyawanid=b.id WHERE a.karyawanid='0000002200'";
    mysqli_query($cnmy, $query);
    
    $query = "select * from $tmp02 WHERE DATE_FORMAT(periode,'%Y-%m') = '$periode2'";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.karyawanid, a.nama_kry nama_karyawan, a.divisi, a.idrutin, nca1.idca idca1, a.jumlah saldo, a.jumlah, nca1.jumlah ca1, "
            . " nca2.idca idca2, nca2.jumlah ca2 "
            . " FROM $tmp01 a LEFT JOIN (select * from $tmp02 WHERE DATE_FORMAT(periode,'%Y-%m') = '$periode1') as nca1 on a.karyawanid=nca1.karyawanid "
            . " LEFT JOIN (select * from $tmp04 WHERE DATE_FORMAT(periode,'%Y-%m') = '$periode2') as nca2 on a.karyawanid=nca2.karyawanid";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //input karyawan yang belum ada BL
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "CREATE TEMPORARY TABLE $tmp04 SELECT * FROM $tmp02");
    mysqli_query($cnmy, "CREATE TEMPORARY TABLE $tmp05 SELECT * FROM $tmp02");
    
    $query = "select distinct a.karyawanid, a.nama_kry, a.divisi, b.idca idca1, b.jumlah ca1, c.idca idca2, c.jumlah ca2 
        from $tmp02 a
        LEFT JOIN (select * from $tmp04 
        WHERE DATE_FORMAT(periode,'%Y-%m')='$periode1') b on a.karyawanid=b.karyawanid
        LEFT JOIN (select * from $tmp05 
        WHERE DATE_FORMAT(periode,'%Y-%m')='$periode2') c on a.karyawanid=c.karyawanid";
    $query = "create TEMPORARY table $tmp06 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "CREATE TEMPORARY TABLE $tmp04 SELECT * FROM $tmp03");
    
    $query="INSERT INTO $tmp03 (karyawanid, nama_karyawan, divisi, idca1, ca1, idca2, ca2)"
            . "select distinct karyawanid, nama_kry, divisi, idca1, ca1, idca2, ca2 FROM $tmp06 WHERE 1=1 AND "
            . " karyawanid NOT IN (select distinct IFNULL(karyawanid,'') from $tmp04)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
    
    
    //END input karyawan yang belum ada BL

    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    
    $query="select * from dbmaster.t_brrutin1 WHERE idrutin IN (select distinct IFNULL(idrutin,'') FROM $tmp03)";
    $query = "create TEMPORARY table $tmp05 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query="select a.idrutin, a.coa, b.NAMA4 nama_coa, a.nobrid, c.nama nama_des, sum(a.rptotal) as rptotal from 
        $tmp05 a 
        LEFT JOIN dbmaster.coa_level4 b on a.coa=b.COA4
        JOIN dbmaster.t_brid c on a.nobrid=c.nobrid 
        GROUP BY 1,2,3,4,5";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select bulan, karyawanid, coa, tgl_kembali, sum(kembali_rp) kembali_rp from dbmaster.t_brrutin_outstanding WHERE 
        DATE_FORMAT(bulan,'%Y-%m')='$periode1' AND divisi = 'OTC' group by 1,2,3,4";
    $query = "create TEMPORARY table $tmp06 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.karyawanid, a.nama_karyawan, a.divisi, a.idrutin, a.idca1 idca, a.idca2, a.jumlah, a.jumlah saldo, 
        a.ca1, CAST(0 as DECIMAL(20,2)) as selisih, a.ca2, CAST(0 as DECIMAL(20,2)) as jmltrans,
        b.coa coa4, b.nama_coa, b.nobrid, b.nama_des, b.rptotal, CAST(0 AS DECIMAL(20,2)) as jml_ots, CAST(0 AS DECIMAL(20,2)) as jml_adj 
        from $tmp03 a 
        LEFT JOIN $tmp02 b ON a.idrutin=b.idrutin";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    

    $pcoa_1="105-02"; //uang muka
    $pcoa_2="905-02"; //pembulatan

    $query = "UPDATE $tmp04 a SET a.jml_ots=(select sum(b.kembali_rp) FROM $tmp06 b WHERE a.karyawanid=b.karyawanid AND coa='$pcoa_1')"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp04 a SET a.jml_adj=(select sum(b.kembali_rp) FROM $tmp06 b WHERE a.karyawanid=b.karyawanid AND coa='$pcoa_2')"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
?>

<html>
<head>
    <title>REPORT LK CA OTC</title>
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
                <tr><td width="210px"><b>Realisasi Biaya Luar Kota Per </b></td><td><?PHP echo "$per1 "; ?></td></tr>
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
                <th align="center" nowrap>Date Trsfr</th>
                <th align="center" nowrap>Bukti</th>
                <th align="center" colspan="2" nowrap>COA</th>
                <!--<th align="center" nowrap>DAERAH</th>-->
                <th align="center" nowrap>ID CA</th>
                <th align="center" nowrap>No LK</th>
                <th align="center" nowrap>NAMA</th>
                <th align="center" nowrap>DIVISI</th>
                <th align="center" colspan="2" nowrap>Description</th>
                <?PHP
                if ($_GET['ket']=="excel") {
                    echo "<th align='center' nowrap>Jenis</th>";
                    echo "<th align='center' nowrap>Debit</th>";
                }else{
                    echo "<th align='center' nowrap>Keterangan</th>";
                    echo "<th align='center' nowrap>Jenis</th>";
                }
                ?>
                
                <th align="center" nowrap>Credit</th>
                <th align="center" nowrap>Saldo REAL</th>
                <th align="center" nowrap>CA <?PHP echo $per1; ?></th>
                
                <th align="center" nowrap>Selisih</th>
                
                <!--<th align="center" nowrap>Transfer</th>-->
                <th align="center" nowrap>Outstanding</th>
                <th align="center" nowrap>Adjustment</th>
                <th align="center" nowrap></th>
                
                <th align="center" >SPV/DM/SM/GSM</th>
                <th align="center" >CA  <?PHP echo $per2; ?></th>
                <th align="center" >JUML TRSF</th>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $totalrp=0;
                    $totalrpbln_next=0;
                    $totalrpbln_prv=0;
                    $pselisih=0;
                    
                    $totselisih=0;
                    
                    $tot_transkurang=0;
                    $tot_ots=0;
                    $tot_adj=0;
                    $tot_otsadj=0;
                    
                    $totjumlah=0;
                    $totjumlahtrsf=0;
                    
                    $sudahlewat=false;
                    
                    $query = "select * from $tmp04 order by divisi, nama_karyawan, karyawanid, idrutin";
                    $result = mysqli_query($cnmy, $query);
                    $records = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result);
                    
                    if ($records) {
                        $reco = 1;
                        while ($reco <= $records) {
                            $pkaryawanid=$row['karyawanid'];
                            $pnmkaryawan=$row['nama_karyawan'];
                            while (($reco <= $records) AND $row['karyawanid']==$pkaryawanid) {
                                
                                $ptgltrasn="";
                                $prealtgltrasn="";
                                /*
                                if (!empty($row['tgltrans']) AND $row['tgltrans'] != "0000-00-00")
                                    $ptgltrasn = date('d/m/Y', strtotime($row['tgltrans']));
                                
                                
                                if (!empty($row['tgltransreal']) AND $row['tgltransreal'] != "0000-00-00")
                                    $prealtgltrasn = date('d/m/Y', strtotime($row['tgltransreal']));
                                */
                                
                                $pnolk=$row['idrutin'];
                                $pidca=$row['idca'];
                                $pketerangan="";//$row['keterangan'];
                                
                                $pbukti="";//$row['nobukti'];
                                $pcoa4=$row['coa4'];
                                $pnmcoa4=$row['nama_coa'];
                                $pkdcabang="";//$row['icabangid'];
                                $pnmcabang="";//$row['nama_cabang'];
                                $pkdarea="";//$row['areaid'];
                                $pnmarea="";//$row['nama_area'];
                                $pdivisi=$row['divisi'];
                                $pnobrid=$row['nobrid'];
                                $pnmdes=$row['nama_des'];
                                $pdeskripsi="";//$row['deskripsi'];
                                $pnotes="";//$row['notes'];
                                $pdeskripsi="";
                                //if (empty($pdeskripsi)) $pdeskripsi=$pnotes;
                                //elseif (!empty($pdeskripsi)) $pdeskripsi=$pdeskripsi.", ".$pnotes;

                                
                                $pqty="0";//number_format($row['qty'],0,",",",");
                                $prp="0";//number_format($row['rp'],0,",",",");
                                $prptotal=number_format($row['rptotal'],0,",",",");
                                
                                $ptgl1="";
                                $ptgl2="";
                                
                                if ($pnobrid=="04" or $pnobrid=="25" or $pnobrid=="21") {
                                    
                                    $myquery = "select qty, rp, tgl1, tgl2 from $tmp05 where idrutin='$pnolk' AND nobrid='$pnobrid'";
                                    $myresult = mysqli_query($cnmy, $myquery);
                                    $nr = mysqli_fetch_array($myresult);
                                    $prp=number_format($nr['rp'],0,",",",");
                                    
                                    $pqty=number_format($nr['qty'],0,",",",");
                                    
                                    if ($_GET['ket']=="excel")
                                         $pnmdes=$pnmdes." (".$pqty."x".$prp.")";
                                    else
                                        $pnmdes=$pnmdes."<br/>(".$pqty."x".$prp.")";
                                    
                                    
                                    if ($pnobrid=="21") {
                                        $ptgl1="";
                                        $ptgl2="";
                                        if ($nr['tgl1']!="0000-00-00" AND !empty($nr['tgl1']))
                                            $ptgl1 = date('d/m/Y', strtotime($nr['tgl1']));
                                        if ($nr['tgl2']!="0000-00-00" AND !empty($nr['tgl2']))
                                            $ptgl2 = date('d/m/Y', strtotime($nr['tgl2']));

                                        if ($_GET['ket']=="excel")
                                             $pnmdes=$pnmdes." (".$ptgl1." s/d. ".$ptgl2.")";
                                        else
                                            $pnmdes=$pnmdes."<br/>(".$ptgl1." s/d. ".$ptgl2.")";
                                    }
                                }
                                
                                $totalrp =$totalrp+$row['rptotal'];
                                
                                $pjenis = "UC";
                                $papv="";
                                
                                echo "<tr>";
                                echo "<td nowrap>$ptgltrasn</td>";
                                echo "<td nowrap>$pbukti</td>";
                                echo "<td nowrap>$pcoa4</td>";
                                echo "<td nowrap>$pnmcoa4</td>";
                                //echo "<td nowrap>$pnmarea</td>";
                                echo "<td nowrap>$pidca</td>";//IDCA
                                echo "<td nowrap>$pnolk</td>";//LK
                                echo "<td nowrap>$pnmkaryawan</td>";
                                echo "<td>$pdivisi</td>";
                                echo "<td nowrap>$pnmdes</td>";
                                echo "<td>$pdeskripsi</td>";
                                if ($_GET['ket']=="excel") {
                                    echo "<td>$pjenis</td>";
                                    echo "<td nowrap></td>";
                                }else{
                                    echo "<td>$pketerangan</td>";
                                    echo "<td nowrap>$pjenis</td>";
                                }
                                echo "<td nowrap align='right'>$prptotal</td>";
                                
                                
                                if ($sudahlewat==false) {
                                    $pjumlah_ots=number_format($row['jml_ots'],0,",",",");
                                    $pjumlah_adj=number_format($row['jml_adj'],0,",",",");
                                    
                                    $tot_ots=$tot_ots+$row['jml_ots'];
                                    $tot_adj=$tot_adj+$row['jml_adj'];
                                    
                                    $pjumlah=number_format($row['jumlah'],0,",",",");
                                    $totjumlah=$totjumlah+$row['jumlah'];
                                    $pca1=number_format($row['ca1'],0,",",",");
                                    $pca2=number_format($row['ca2'],0,",",",");
                                    $totalrpbln_next =$totalrpbln_next+$row['ca1'];
                                    $pselisih=$row['ca1']-$row['jumlah'];
                                    $totselisih =$totselisih+$pselisih;
                                    
                                    $tottranskurang=0;
                                    if ((double)$pselisih<0) {
                                        $tottranskurang=(double)$pselisih*-1;
                                        $tot_transkurang=$tot_transkurang+$tottranskurang;
                                        
                                        $tottranskurang=number_format($tottranskurang,0,",",",");
                                    }
                                    
                                    $pjumlahtrans=$row['ca2']-$pselisih;
                                    
                                    if ($pselisih>0 AND $row['ca2']==0) $pjumlahtrans=0;
                                    elseif ($pselisih>0 AND $row['ca2']>0) $pjumlahtrans=$row['ca2'];
                                    elseif ($pselisih==0 AND $row['ca2']>0) $pjumlahtrans=$row['ca2'];
                                    
                                    if (empty($pjumlahtrans)) $pjumlahtrans=0;
                                    $totjumlahtrsf=$totjumlahtrsf+$pjumlahtrans;
                                    $pjumlahtrans=number_format($pjumlahtrans,0,",",",");
                                    
                                    $pselisih=number_format($pselisih,0,",",",");
                                    $totalrpbln_prv =$totalrpbln_prv+$row['ca2'];
                                    
                                    echo "<td nowrap align='right'>$pjumlah</td>";
                                    echo "<td nowrap align='right'>$pca1</td>";
                                    echo "<td nowrap align='right'>$pselisih</td>";
                                    
                                    
                                    //echo "<td nowrap align='right'>$tottranskurang</td>";//trs kurang
                                    echo "<td nowrap align='right'>$pjumlah_ots</td>";
                                    echo "<td nowrap align='right'>$pjumlah_adj</td>";
                                    echo "<td nowrap align='right'></td>";//tot ots adj
                                    
                                    echo "<td nowrap>$papv</td>";
                                    echo "<td nowrap align='right'>$pca2</td>";
                                    echo "<td nowrap align='right'>$pjumlahtrans</td>";
                                    
                                }else{
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    
                                    //echo "<td nowrap align='right'></td>";//trs kurang
                                    echo "<td nowrap align='right'></td>";//ost
                                    echo "<td nowrap align='right'></td>";//adj
                                    echo "<td nowrap align='right'></td>";//toto ots adj
                                    
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    echo "<td></td>";
                                    
                                }
                                
                                
                                echo "</tr>";

                                $no++;
                                $row = mysqli_fetch_array($result);
                                $reco++;
                                $sudahlewat=true;
                            }
                            $sudahlewat=false;
                            
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            //echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            
                            //echo "<td></td>";//trs kurang
                            echo "<td></td>";//ots
                            echo "<td></td>";//adj
                            echo "<td></td>";//tot ots adj
                            
                            echo "<td nowrap align='right'></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            
                            echo "</tr>";
                                
                        }
                        $totalrp=number_format($totalrp,0,",",",");
                        $totjumlah=number_format($totjumlah,0,",",",");
                        $totalrpbln_next=number_format($totalrpbln_next,0,",",",");
                        $totalrpbln_prv=number_format($totalrpbln_prv,0,",",",");
                        
                        $totjumlahtrsf=number_format($totjumlahtrsf,0,",",",");
                        
                        $tot_otsadj=( (double)$totselisih- (0-((double)$tot_transkurang-(double)$tot_ots)) ) - (double)$tot_adj;
                        $tot_transkurang=number_format($tot_transkurang,0,",",",");
                        $tot_ots=number_format($tot_ots,0,",",",");
                        $tot_adj=number_format($tot_adj,0,",",",");
                        $tot_otsadj=number_format($tot_otsadj,0,",",",");
                        
                        $totselisih=number_format($totselisih,0,",",",");
                        
                        echo "<tr>";
                        //echo "<td colspan='11' align='right'> TOTAL : &nbsp;</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        //echo "<td nowrap></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        
                        echo "<td nowrap></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>Grand Total</b></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>$totalrp</b></td>";
                        echo "<td nowrap align='right'><b>$totjumlah</b></td>";
                        echo "<td nowrap align='right'><b>$totalrpbln_next</b></td>";
                        echo "<td nowrap align='right'><b>$totselisih</b></td>";
                        
                        //echo "<td nowrap align='right'><b>$tot_transkurang</b></td>";//trs kurang
                        echo "<td nowrap align='right'><b>$tot_ots</b></td>";//ots
                        echo "<td nowrap align='right'><b>$tot_adj</b></td>";//adj
                        echo "<td nowrap align='right'><b>$tot_otsadj</b></td>";//tot ots adj
                        
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>$totalrpbln_prv</b></td>";
                        echo "<td nowrap align='right'><b>$totjumlahtrsf</b></td>";
                        
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>

    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp05");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp06");
    
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
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
        
        
    </script>

</html>

