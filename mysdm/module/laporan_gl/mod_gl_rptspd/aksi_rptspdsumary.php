<?php

function BuatFormatNum($prp, $ppilih) {
    if (empty($prp)) $prp=0;

    $numrp=$prp;
    if ($ppilih=="1") $numrp=number_format($prp,0,",",",");
    elseif ($ppilih=="2") $numrp=number_format($prp,0,".",".");

    return $numrp;
}
    
    
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
    header("Content-Disposition: attachment; filename=REKAP SUMMARY SPD.xls");
}

$nmodule=$_GET['module'];

include("config/koneksimysqli.php");
include("config/common.php");
    
$printdate= date("d/m/Y");
$fgroupid=$_SESSION['GROUP'];
$pses_divisi=$_SESSION['DIVISI'];
$pses_idcard=$_SESSION['IDCARD'];
$puserid=$_SESSION['USERID'];
    
$tgl01=$_POST['bulan1'];
$tgl02=$_POST['bulan2'];


$periode1= date("Y-m-01", strtotime($tgl01));
$periode2= date("Y-m-t", strtotime($tgl02));


$ppilihbay="";
if (isset($_POST['cb_rptby'])) $ppilihbay=$_POST['cb_rptby'];

$pperiodepilihby="Tanggal No. SPD";
$pperiodepilih="";
if (isset($_POST['cb_periodepil'])) $pperiodepilih=$_POST['cb_periodepil'];
if ($pperiodepilih=="ND") {
    $pperiodepilihby="Tanggal No. Divisi";
}


$ppilihanall=false;//OR $fgroupid=="25" //anne
if ($fgroupid=="1" OR $fgroupid=="24" OR $fgroupid=="2" OR $fgroupid=="22" OR $fgroupid=="46" OR $fgroupid=="50" OR $fgroupid=="34") {
    $ppilihanall=true;
}

$ppilformat="1";
if (($pses_idcard=="0000000143" OR $pses_idcard=="0000000329") AND $ppilihrpt=="excel") {
    $ppilformat="2";
}

$now=date("mdYhis");
$tmp00 =" dbtemp.tmplapspdsum00_".$puserid."_$now ";
$tmp01 =" dbtemp.tmplapspdsum01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmplapspdsum02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmplapspdsum03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmplapspdsum04_".$puserid."_$now ";




//echo "$periode1, $periode1, $ppilihbay, $pperiodepilih";

$query = "select divisi, idinput, tgl, tglspd, karyawanid, kodeid, subkode, nomor, nodivisi, 
    nomor2, nodivisi2, 
    pilih, jenis_rpt, kodeperiode, tglf, tglt, idinputbank, userid, 
    jumlah, jumlah2, jumlah3 
    from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' ";
if ($pperiodepilih=="NS")
    $query .=" AND tglspd BETWEEN '$periode1' AND '$periode2' ";
else
    $query .=" AND tgl BETWEEN '$periode1' AND '$periode2' ";

if ($ppilihanall == true){
}else{
    if ($fgroupid=="26") {//saiful OTC
        $query .=" AND divisi IN ('OTC', 'CHC') AND (subkode NOT IN ('29') OR karyawanid='$pses_idcard' OR IFNULL(userid,'')='$pses_idcard' ) ";
    }else{
        $query .=" AND ( karyawanid='$pses_idcard' OR IFNULL(userid,'')='$pses_idcard' ) ";
    }
}

//transfer ulang
$query .=" AND ifnull(jenis_rpt,'') NOT IN ('W') ";
//adjustment
$query .=" AND ifnull(kodeid,'') NOT IN ('3') ";
    
$query = "create TEMPORARY table $tmp00 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//ADJUSTMENU
    $query = "select divisi, idinput, tgl, tglspd, karyawanid, kodeid, subkode, nomor, nodivisi, 
        nomor2, nodivisi2, 
        pilih, jenis_rpt, kodeperiode, tglf, tglt, idinputbank, userid, 
        jumlah, jumlah2, jumlah3 
        from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' ";
    //adjustment
    $query .=" AND ifnull(kodeid,'') IN ('3') ";
    
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp04 SET nodivisi2=RTRIM(LEFT(nodivisi2, LENGTH(nodivisi2) -1)) WHERE RIGHT(nodivisi2,1)='_'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
//END ADJUSTMENU


$query = "select a.*, b.nama as nama_karyawan, c.nama as nama_user, 
    d.nama as namaid, d.subnama, d.igroup, d.inama as inamagroup 
    from $tmp00 as a 
    LEFT JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId 
    LEFT JOIN hrd.karyawan as c on a.userid=c.karyawanId 
    LEFT JOIN dbmaster.t_kode_spd as d on a.kodeid=d.kodeid and a.subkode=d.subkode";
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET igroup='3' WHERE IFNULL(jenis_rpt,'')='B'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN dbmaster.t_kode_spd as b on a.igroup=b.igroup SET a.inamagroup=b.inama";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "select distinct a.idinput, a.bridinput, a.kodeinput, a.amount, a.jml_adj 
    FROM dbmaster.t_suratdana_br1 as a JOIN 
    (SELECT idinput FROM $tmp01 WHERE ( igroup in (1,2,5) OR kodeid IN ('1','2') ) ) as b on a.idinput=b.idinput";
$query = "create TEMPORARY table $tmp02 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "ALTER TABLE $tmp02 ADD COLUMN stsbatal VARCHAR(1)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//MENCARI YANG SUDAH SPD TAPI BATAL

    //BR0 BR ETHICAL HO, EAGLE, PIGEON, PEACOCK ===== A
    $query ="select a.brid, a.batal from hrd.br0 as a JOIN
        (select bridinput FROM $tmp02 WHERE kodeinput IN ('A')) as b on a.brid=b.bridinput WHERE IFNULL(batal,'')='Y'";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    $query = "INSERT INTO $tmp03 (brid, batal)"
            . "select a.brid, 'Y' as batal from hrd.br0_reject as a JOIN
            (select bridinput FROM $tmp02 WHERE kodeinput IN ('A')) as b on a.brid=b.bridinput";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 as a JOIN $tmp03 as b on a.bridinput=b.brid SET a.stsbatal='Y' WHERE a.kodeinput IN ('A') AND IFNULL(b.batal,'')='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");

    //KLAIM DISCOUNT ===== B
    $query = "select a.klaimid as brid, 'Y' as batal from hrd.klaim_reject as a JOIN
            (select bridinput FROM $tmp02 WHERE kodeinput IN ('B')) as b on a.klaimid=b.bridinput";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 as a JOIN $tmp03 as b on a.bridinput=b.brid SET a.stsbatal='Y' WHERE a.kodeinput IN ('B') AND IFNULL(b.batal,'')='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");


    //KAS KECIL MARSIS ===== C
    $query = "select a.kasid as brid, 'Y' as batal from hrd.kas_reject as a JOIN
            (select bridinput FROM $tmp02 WHERE kodeinput IN ('C')) as b on a.kasid=b.bridinput";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 as a JOIN $tmp03 as b on a.bridinput=b.brid SET a.stsbatal='Y' WHERE a.kodeinput IN ('C') AND IFNULL(b.batal,'')='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");


    //KLAIM DISCOUNT ===== D
    $query = "select a.klaimid as brid, 'Y' as batal from hrd.klaim_reject as a JOIN
            (select bridinput FROM $tmp02 WHERE kodeinput IN ('D')) as b on a.klaimid=b.bridinput";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 as a JOIN $tmp03 as b on a.bridinput=b.brid SET a.stsbatal='Y' WHERE a.kodeinput IN ('D') AND IFNULL(b.batal,'')='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");

    //OTC ===== E
    $query ="select a.brotcid as brid, a.batal from hrd.br_otc as a JOIN
        (select bridinput FROM $tmp02 WHERE kodeinput IN ('E')) as b on a.brotcid=b.bridinput WHERE IFNULL(batal,'')='Y'";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "INSERT INTO $tmp03 (brid, batal)"
            . "select a.brotcid, 'Y' as batal from hrd.br_otc_reject as a JOIN
            (select bridinput FROM $tmp02 WHERE kodeinput IN ('E')) as b on a.brotcid=b.bridinput";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 as a JOIN $tmp03 as b on a.bridinput=b.brid SET a.stsbatal='Y' WHERE a.kodeinput IN ('E') AND IFNULL(b.batal,'')='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");



    //RUTIN DAN LK ===== RUTIN F LK G 
    $query = "select a.idrutin as brid, stsnonaktif as batal from dbmaster.t_brrutin0 as a JOIN
            (select bridinput FROM $tmp02 WHERE kodeinput IN ('F', 'G')) as b on a.idrutin=b.bridinput WHERE "
            . " IFNULL(stsnonaktif,'')='Y'";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 as a JOIN $tmp03 as b on a.bridinput=b.brid SET a.stsbatal='Y' WHERE a.kodeinput IN ('F', 'G') AND IFNULL(b.batal,'')='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");


    //SEWA ===== U
    $query = "select a.idsewa as brid, stsnonaktif as batal from dbmaster.t_sewa as a JOIN
            (select bridinput FROM $tmp02 WHERE kodeinput IN ('U')) as b on a.idsewa=b.bridinput WHERE "
            . " IFNULL(stsnonaktif,'')='Y'";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 as a JOIN $tmp03 as b on a.bridinput=b.brid SET a.stsbatal='Y' WHERE a.kodeinput IN ('U') AND IFNULL(b.batal,'')='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");



    //SERVICE KENDARAAN ===== V
    $query = "select a.idservice as brid, stsnonaktif as batal from dbmaster.t_service_kendaraan as a JOIN
            (select bridinput FROM $tmp02 WHERE kodeinput IN ('V')) as b on a.idservice=b.bridinput WHERE "
            . " IFNULL(stsnonaktif,'')='Y'";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 as a JOIN $tmp03 as b on a.bridinput=b.brid SET a.stsbatal='Y' WHERE a.kodeinput IN ('V') AND IFNULL(b.batal,'')='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");



    //KAS KECIL CABANG ===== X
    $query = "select a.idkascab as brid, stsnonaktif as batal from dbmaster.t_kaskecilcabang as a JOIN
            (select bridinput FROM $tmp02 WHERE kodeinput IN ('X')) as b on a.idkascab=b.bridinput WHERE "
            . " IFNULL(stsnonaktif,'')='Y'";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 as a JOIN $tmp03 as b on a.bridinput=b.brid SET a.stsbatal='Y' WHERE a.kodeinput IN ('X') AND IFNULL(b.batal,'')='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");



//END MENCARI YANG SUDAH SPD TAPI BATAL

$query = "ALTER TABLE $tmp01 ADD COLUMN jumlah4 DECIMAL(20,2), ADD COLUMN jumlah5 DECIMAL(20,2), ADD COLUMN jmlkeluar DECIMAL(20,2)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 as a JOIN (select idinput, sum(IFNULL(amount,0)+IFNULL(jml_adj,0)) as amount from $tmp02 WHERE IFNULL(stsbatal,'')='Y' GROUP BY 1) as b "
        . " on a.idinput=b.idinput SET a.jumlah4=b.amount";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

/*
//memisahkan yang adjustment
$query="select * from $tmp01 WHERE kodeid IN ('3')";
$query = "create TEMPORARY table $tmp03 ($query)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

//hapus yang adjustment
$query = "DELETE FROM $tmp01 WHERE kodeid IN ('3')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
*/
//update adjustment
$query = "UPDATE $tmp01 as a JOIN $tmp04 as b "
        . " on a.nodivisi=b.nodivisi2 SET a.jumlah5=b.jumlah WHERE a.subkode "
        . " IN ('01', '02', '03', '04', '05', '20', '21', '22', '23', '24', '36', '39')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET igroup='0' WHERE IFNULL(igroup,'')='' OR IFNULL(igroup,'0')='0'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 SET nomor='' WHERE IFNULL(igroup,'')='3'";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET igroup='7', inamagroup='Via Surabaya' WHERE IFNULL(jenis_rpt,'') IN ('V', 'C')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");

//KLAIM DISCOUNT ===== B
$query = "select a.idinput, a.nodivisi, sum(a.jumlah) as jumlah from dbmaster.t_suratdana_bank as a JOIN
        $tmp01 as b on a.idinput=b.idinput WHERE IFNULL(stsinput,'')='K' AND IFNULL(stsnonaktif,'')<>'Y' AND a.subkode NOT IN ('29') "
        . " GROUP BY 1,2";
$query = "create TEMPORARY table $tmp04 ($query)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "UPDATE $tmp01 as a JOIN $tmp04 as b on a.idinput=b.idinput and a.nodivisi=b.nodivisi SET a.jmlkeluar=b.jumlah";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }




?>

<HTML>
<HEAD>
    <TITLE>Rekap Summary Surat Permintaan Dana</TITLE>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Apr 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
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

<div class='modal fade' id='myModal' role='dialog'></div>

<BODY class="nav-md">
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>

    <center><div class='h1judul'>Summary Permintaan Dana</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Periode By</td><td>:</td><td><?PHP echo "<b>$pperiodepilihby</b>"; ?></td></tr>
            <tr><td>Periode</td><td>:</td><td><?PHP echo "<b>$tgl01 s/d. $tgl02</b>"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
            <th align="center" nowrap>No</th>
            <th align="center" nowrap></th>
            <th align="center" nowrap>Jenis Dana</th>
            <th align="center" nowrap>No. SPD</th>
            <th align="center" nowrap>No. Divisi</th>
            <th align="center" nowrap>Jumlah</th>
            <th align="center" nowrap>Kurang /Lebih</th>
            <th align="center" nowrap>Batal</th>
            <th align="center" nowrap>Total</th>
            <th align="center" nowrap>Jumlah Transfer</th>
            <th align="center" nowrap>Sudah Adjusment</th>
            
            <th align="center" nowrap>Isi Adjusment</th>
            <th align="center" nowrap>Pilih SPD</th>
            <th align="center" nowrap></th>
            
            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $query = "select distinct igroup, inamagroup from $tmp01 order by igroup";
            $tampil=mysqli_query($cnmy, $query);
            while ($row=mysqli_fetch_array($tampil)) {
                $pidgroup=$row['igroup'];
                $pnmgroup=$row['inamagroup'];
                
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>$pnmgroup</b></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                
                echo "</tr>";
                
                $no=1;
                $ptotaligroup=0;
                $ptotalbtligroup=0;
                $ptotaladjigroup=0;
                $ptotaljmlkeluar=0;
                
                $query = "select distinct igroup, inamagroup, kodeid, namaid, subkode, subnama from $tmp01 WHERE igroup='$pidgroup' order by igroup, kodeid, namaid, subkode, subnama";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1=mysqli_fetch_array($tampil1)) {
                    $pkode=$row1['kodeid'];
                    $pnmkode=$row1['namaid'];
                    
                    $psubkode=$row1['subkode'];
                    $psubnama=$row1['subnama'];
                    
                    
                    
                    $query = "select * from $tmp01 WHERE igroup='$pidgroup' AND kodeid='$pkode' and subkode='$psubkode' order by igroup, divisi, kodeid, namaid, subkode, subnama, nodivisi, nomor";
                    $tampil2=mysqli_query($cnmy, $query);
                    while ($row2=mysqli_fetch_array($tampil2)) {
                        
                        $pjenisrpt=$row2["jenis_rpt"];
                        $pdivisi=$row2['divisi'];
                        $pkaryawanid=$row2['karyawanid'];
                        $idno=$row2['idinput'];
                        $tglbuat = $row2["tgl"];
                        
                        $pnomor=$row2['nomor'];
                        $pnodivisi=$row2['nodivisi'];
                        
                        
                        $pjumlah=$row2['jumlah'];
                        $pjmlkuranglebih=$row2['jumlah2'];
                        
                        $pjumlahbatal=$row2['jumlah4'];
                        $pjumlahadj=$row2['jumlah5'];
                        
                        $pjmlkeluartrs=$row2['jmlkeluar'];
                        
                        
                        
                        $pnmpengajuan_jenis=$psubnama;
                        if ($pdivisi!="OTC" AND ($psubkode=="01" OR $psubkode=="02" OR $psubkode=="20")) {
                            $pnmpengajuan_jenis="Advance BR";
                            if ($pjenisrpt=="K") $pnmpengajuan_jenis="Klaim BR";
                            if ($pjenisrpt=="B") $pnmpengajuan_jenis="PC-M";
                            if ($pjenisrpt=="S") $pnmpengajuan_jenis="Kasbon SBY";
                            if ($pjenisrpt=="D") $pnmpengajuan_jenis="Klaim Disc.";
                            if ($pjenisrpt=="C") $pnmpengajuan_jenis="Klaim Disc. (Via SBY)";
                            if ($pjenisrpt=="V") $pnmpengajuan_jenis="Advance BR (Via SBY)";
                            if ($pjenisrpt=="J") $pnmpengajuan_jenis="Adjustment";
                            if ($pdivisi=="HO" AND empty($pjenisrpt)) $pnmpengajuan_jenis="Adjustment";
                        }
                        
                        if ($pdivisi=="OTC" AND ($psubkode=="01" OR $psubkode=="02" OR $psubkode=="20")) {
                            if ($pkode=="1") $pnmpengajuan_jenis="Advance BR";
                            elseif ($pkode=="2") $pnmpengajuan_jenis="Klaim BR";
                            if ($pjenisrpt=="B") $pnmpengajuan_jenis="PC-M";
                        }
					
                        if ($pjenisrpt=="W") $pnmpengajuan_jenis="Transfer Ulang";
                        if ($psubkode=="25" OR $psubkode=="26" OR $psubkode=="27" 
                                OR $psubkode=="28" OR $psubkode=="29" OR $psubkode=="30" 
                                OR $psubkode=="31" OR $psubkode=="32" OR $psubkode=="33" 
                                OR $psubkode=="34") $pnmpengajuan_jenis=$psubnama;
                        
                        
                        $pmystsyginput="";
                        if ($pkaryawanid=="0000000566") {
                            $pmystsyginput=1;
                        }elseif ($pkaryawanid=="0000001043") {
                            $pmystsyginput=2;
                        }else{
                            if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="2" AND $psubkode=="20") ) {//anne
                                $pmystsyginput=5;
                            }else{
                                if ($pkode=="1" AND $psubkode=="03") {//ria
                                    $pmystsyginput=3;
                                }elseif ($pkode=="2" AND $psubkode=="05") {//ria CA SEWA
                                    $pmystsyginput=7;
                                }elseif ($pkode=="1" AND $psubkode=="04") {//ria Insentif
                                    $pmystsyginput=8;
                                }elseif ($pkode=="2" AND $psubkode=="21") {//marsis
                                    $pmystsyginput=4;
                                }elseif ( ($pkode=="2" AND $psubkode=="22") OR ($pkode=="2" AND $psubkode=="23") ) {//marsis
                                    $pmystsyginput=6;
                                }elseif ($pkode=="2" AND $psubkode=="39") {//kas kecil cabang
                                    $pmystsyginput=9;

                                }
                            }
                        }
                        
                        $pmybatal="";
                        $pmymodule="";
                        $pmymodule2="";
                        if ($pdivisi=="OTC") {
                            if ( ($pkode=="1" AND $psubkode=="03") ) {
                                $pmymodule="module=rekapbiayarutinotc&act=input&idmenu=171&ket=bukan&ispd=$idno";
                                $pmymodule2="module=rekapbiayarutinotc&act=input&idmenu=171&ket=excel&ispd=$idno";
                                $pmybatal="F";
                            }elseif ( ($pkode=="2" AND $psubkode=="21") ) {
                                $pmymodule="module=rekapbiayaluarotc&act=input&idmenu=245&ket=bukan&ispd=$idno";
                                $pmymodule2="module=rekapbiayaluarotc&act=input&idmenu=245&ket=excel&ispd=$idno";
                                $pmybatal="G";
                            }elseif ( ($pkode=="1" AND $psubkode=="02") ) {
                                $pmymodule="module=laporangajispgotc&act=input&idmenu=134&ket=bukan&ispd=$idno";
                                $pmymodule2="module=laporangajispgotc&act=input&idmenu=134&ket=excel&ispd=$idno";
                            }elseif ($pkode=="2" AND $psubkode=="36") {
                                $pmymodule="module=rekapbiayarutincaotc&act=input&idmenu=134&ket=bukan&ispd=$idno";
                                $pmymodule2="module=rekapbiayarutincaotc&act=input&idmenu=134&ket=excel&ispd=$idno";
                            }else{
                                $pmymodule="module=lapbrotcpermorpt&act=input&idmenu=134&ket=bukan&ispd=$idno";
                                $pmymodule2="module=lapbrotcpermorpt&act=input&idmenu=134&ket=excel&ispd=$idno";
                            }
                        }else{
                            if ($pmystsyginput==1) {
                                $pmymodule="module=saldosuratdana&act=rekapbr&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                                $pmymodule2="module=saldosuratdana&act=rekapbr&idmenu=192&ket=excel&ispd=$idno&iid=$pmystsyginput";
                                $pmybatal="A";
                            }elseif ($pmystsyginput==2) {
                                if ($pjenisrpt=="D" OR $pjenisrpt=="C") {
                                    $pmymodule="module=saldosuratdana&act=viewbrklaim&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                                    $pmymodule2="module=saldosuratdana&act=viewbrklaim&idmenu=192&ket=excel&ispd=$idno&iid=$pmystsyginput";
                                }else{
                                    $pmymodule="module=saldosuratdana&act=viewbr&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                                    $pmymodule2="module=saldosuratdana&act=viewbr&idmenu=192&ket=excel&ispd=$idno&iid=$pmystsyginput";
                                    $pmybatal="A";
                                }
                            }elseif ($pmystsyginput==3) {
                                $pmymodule="module=rekapbiayarutin&act=input&idmenu=190&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                                $pmymodule2="module=rekapbiayarutin&act=input&idmenu=190&ket=excel&ispd=$idno&iid=$pmystsyginput";
                                $pmybatal="F";
                            }elseif ($pmystsyginput==4) {
                                $pmymodule="module=rekapbiayaluar&act=input&idmenu=187&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                                $pmymodule2="module=rekapbiayaluar&act=input&idmenu=187&ket=excel&ispd=$idno&iid=$pmystsyginput";
                                $pmybatal="G";
                            }elseif ($pmystsyginput==5) {
                                $pmymodule="module=saldosuratdana&act=rekapbr&idmenu=204&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                                $pmymodule2="module=saldosuratdana&act=rekapbr&idmenu=204&ket=excel&ispd=$idno&iid=$pmystsyginput";
                                $pmybatal="A";
                            }elseif ($pmystsyginput==6) {
                                $pmymodule="module=spdkas&act=viewbrho&idmenu=205&ket=bukan&ispd=$idno&bln=$tglbuat";
                                $pmymodule2="module=spdkas&act=viewbrho&idmenu=205&ket=excel&ispd=$idno&bln=$tglbuat";
                            }elseif ($pmystsyginput==7) {
                                $pmymodule="module=reportcasewa&act=rpt&idmenu=264&ket=bukan&ispd=$idno&bln=$tglbuat";
                                $pmymodule2="module=reportcasewa&act=rpt&idmenu=264&ket=excel&ispd=$idno&bln=$tglbuat";
                            }elseif ($pmystsyginput==8) {
                                $pmymodule="module=mstprosesinsentif&act=input&idmenu=262&ket=bukan&ispd=$idno&bln=$tglbuat";
                                $pmymodule2="module=mstprosesinsentif&act=input&idmenu=262&ket=excel&ispd=$idno&bln=$tglbuat";
                            }
                        }
                    
                        if ($pmystsyginput==9) {
                            $pmymodule="module=bgtpdkaskecilcabang&act=input&idmenu=305&ket=bukan&ispd=$idno&bln=$tglbuat";
                            $pmymodule2="module=bgtpdkaskecilcabang&act=input&idmenu=305&ket=excel&ispd=$idno&bln=$tglbuat";
                        }
                        
                        if ($psubkode=="29") {
                            $pmymodule=""; $pmymodule2="";
                        }
                        
                        $padadjustment=false;
                        if ((DOUBLE)$pjumlahadj<>0) {
                            $padadjustment=true;
                        }
                        
                        $padabatal=false;
                        if ((DOUBLE)$pjumlahbatal<>0) {
                            $padabatal=true;
                        }
                        
                        if ($pmybatal=="A") {
                            
                        }else{
                            $pjmlkuranglebih=0;
                        }
                        
                        $ptotalkurangbatal=(DOUBLE)$pjumlah-(DOUBLE)$pjumlahbatal+(DOUBLE)$pjmlkuranglebih;
                        
                        $ptotaligroup=(DOUBLE)$ptotaligroup+(DOUBLE)$pjumlah;
                        $ptotalbtligroup=(DOUBLE)$ptotalbtligroup+(DOUBLE)$pjumlahbatal;
                        $ptotaladjigroup=(DOUBLE)$ptotaladjigroup+(DOUBLE)$pjumlahadj;
                        
                        $ptotaljmlkeluar=(DOUBLE)$ptotaljmlkeluar+(DOUBLE)$pjmlkeluartrs;
                        
                        $pjumlah=BuatFormatNum($pjumlah, $ppilformat);
                        $pjmlkuranglebih=BuatFormatNum($pjmlkuranglebih, $ppilformat);
                        $pjumlahbatal=BuatFormatNum($pjumlahbatal, $ppilformat);
                        $ptotalkurangbatal=BuatFormatNum($ptotalkurangbatal, $ppilformat);
                        $pjumlahadj=BuatFormatNum($pjumlahadj, $ppilformat);
                    
                        
                        $pjmlkeluartrs=BuatFormatNum($pjmlkeluartrs, $ppilformat);
                        
                        $prpjmlbtl_btn=$pjumlahbatal;
                        $prpjmladj_btn=$pjumlahadj;
                        if ($ppilihrpt!="excel") {
                            if ($padadjustment==true) {
                                $prpjmladj_btn="<button type='button' class='btn btn-default btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"TampilkanDataAdjustment('$pnodivisi')\">$pjumlahadj</button>";
                            }
                            
                            if ($padabatal==true) {
                                if (!empty($pmybatal)) {
                                    $prpjmlbtl_btn="<button type='button' class='btn btn-default btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"TampilkanDataBatal('$idno', '$pnodivisi', '$pmybatal')\">$pjumlahbatal</button>";
                                }
                            }
                            
                        }
                        
                        $plampiran="";
                        if (!empty($pmymodule)) {
                            $plampiran="<a style='font-size:11px;' title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                                . "onClick=\"window.open('eksekusi3.php?$pmymodule',"
                                . "'Ratting','width=800,height=500,left=400,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                                . "Lampiran</a>";
                            $plampiran = "<a class='btn btn-info btn-xs' href='eksekusi3.php?$pmymodule' target='_blank'>Lampiran</a>";
                        }
                        

                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$plampiran</td>";
                        echo "<td nowrap>$pnmpengajuan_jenis</td>";
                        echo "<td nowrap>$pnomor</td>";
                        echo "<td nowrap>$pnodivisi</td>";
                        echo "<td nowrap align='right'>$pjumlah</td>";
                        echo "<td nowrap align='right'>$pjmlkuranglebih</td>";
                        echo "<td nowrap align='right'>$prpjmlbtl_btn</td>";
                        echo "<td nowrap align='right'>$ptotalkurangbatal</td>";
                        echo "<td nowrap align='right'>$pjmlkeluartrs</td>";
                        echo "<td nowrap align='right'>$prpjmladj_btn</td>";
                        
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        
                        echo "</tr>";
                        
                        $no++;
                        
                    }
                    
                    
                }
                
                $ptotalkurangbatal=(DOUBLE)$ptotaligroup-(DOUBLE)$ptotalbtligroup;
                
                $ptotaligroup=BuatFormatNum($ptotaligroup, $ppilformat);
                $ptotalbtligroup=BuatFormatNum($ptotalbtligroup, $ppilformat);
                $ptotaladjigroup=BuatFormatNum($ptotaladjigroup, $ppilformat);
                
                $ptotalkurangbatal=BuatFormatNum($ptotalkurangbatal, $ppilformat);
                
                $ptotaljmlkeluar=BuatFormatNum($ptotaljmlkeluar, $ppilformat);
                
                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap>Total $pnmgroup</td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'>$ptotaligroup</td>";
                echo "<td nowrap align='right'></td>";
                echo "<td nowrap align='right'>$ptotalbtligroup</td>";
                echo "<td nowrap align='right'>$ptotalkurangbatal</td>";
                echo "<td nowrap align='right'>$ptotaljmlkeluar</td>";
                echo "<td nowrap align='right'>$ptotaladjigroup</td>";
                
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                
                echo "</tr>";
                
                
            }
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
    
    
    <script>
        function TampilkanDataAdjustment(enodivisi){
            $.ajax({
                type:"post",
                url:"module/laporan_gl/mod_gl_rptspd/tampil_adjustment_nodivisi.php?module=viewdatanodivisiadj",
                data:"unodivisi="+enodivisi,
                success:function(data){
                    $("#myModal").html(data);
                }
            });
        }
        
        function TampilkanDataBatal(eidinput, enodivisi, eidkode){
            $.ajax({
                type:"post",
                url:"module/laporan_gl/mod_gl_rptspd/tampil_batal.php?module=viewdatabatal",
                data:"uidinput="+eidinput+"&unodivisi="+enodivisi+"&uidkode="+eidkode,
                success:function(data){
                    $("#myModal").html(data);
                }
            });
        }
    </script>
    
    
    
    
</HTML>
<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_close($cnmy);
?>