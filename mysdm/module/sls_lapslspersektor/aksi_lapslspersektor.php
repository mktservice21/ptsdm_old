<?php
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
        header("Content-Disposition: attachment; filename=Laporan Sales Per Sektor.xls");
    }
    
    include("config/koneksimysqli_ms.php");
    $cnmy=$cnms;
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $printdate= date("d/m/Y");
    
    $karyawanid=$_SESSION['IDCARD'];
    $ptgl1=$_POST['e_periode01'];
    $ptgl2=$_POST['e_periode02'];
    
    $pbulan1 = date("Y-m-01", strtotime($ptgl1));
    $pbulan2 = date("Y-m-t", strtotime($ptgl2));
    $date1=date_create($pbulan1);
    $date2=date_create($pbulan2);
    $pidcabang=$_POST["cb_cabang"];
    $pidamkry=$_POST["cb_am"];
    $pdivisiid=$_POST["cb_divisi"];
    $pjenissektor=$_POST["rd_rptny"];
    $ppilhqtyval=$_POST["rd_rptjns"];
    $piddist=$_POST['cbdistributor'];
    
    
    $pprodoth = "";
    $pplhothpea = "";
    if (isset($_POST['chkboth'])) $pprodoth=$_POST['chkboth'];
    
    $pplhothpea=$pprodoth;
    if ($pprodoth!="Y") $pplhothpea = "N";
    
    $query = "select nama from sls.icabang where icabangid='$pidcabang'";
    $tampil= mysqli_query($cnmy, $query);
    $nr= mysqli_fetch_array($tampil);
    $pnamacabang_p=$nr['nama'];
    
    $query = "select nama from ms.karyawan where karyawanid='$pidamkry'";
    $tampil= mysqli_query($cnmy, $query);
    $nr= mysqli_fetch_array($tampil);
    $pnamaam_p=$nr['nama'];
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp01 =" dbtemp.tmplapslssektor01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmplapslssektor02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmplapslssektor03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmplapslssektor04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmplapslssektor05_".$puserid."_$now ";
    $tmp06 =" dbtemp.tmplapslssektor06_".$puserid."_$now ";
    $tmp07 =" dbtemp.tmplapslssektor07_".$puserid."_$now ";
    $tmp08 =" dbtemp.tmplapslssektor08_".$puserid."_$now ";
    $tmp09 =" dbtemp.tmplapslssektor09_".$puserid."_$now ";
    $tmp10 =" dbtemp.tmplapslssektor10_".$puserid."_$now ";
   
    
    $query = "select * from sls.ispv0 WHERE 1=1 ";
    if (!empty($pidamkry)) $query .=" AND karyawanid='$pidamkry' ";
    if (!empty($pdivisiid)) $query .=" AND divisiid='$pdivisiid' ";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $pnmdistirbutor="";
    $query = "select nama from MKT.distrib0 where distid='$piddist'";
    $tampil= mysqli_query($cnms, $query);
    $rd= mysqli_fetch_array($tampil);
    $pnmdistirbutor=$rd['nama'];
    if (empty($piddist)) $pnmdistirbutor="All";
    
    
    $filtercabangarea="";
    $filterdivisicabarea="";
    $query ="select * from $tmp02";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu==0) {
        echo "data area tidak ada....";
        goto hapusdata;
    }
    while ($row= mysqli_fetch_array($tampil)) {
        $picabangid=$row['icabangid'];
        $pareaid=$row['areaid'];
        $pdividare=$row['divisiid'];
        
        if (strpos($filtercabangarea, $picabangid.$pareaid)==false) $filtercabangarea .="'".$picabangid.$pareaid."',";
        if (strpos($filterdivisicabarea, $picabangid.$pareaid.$pdividare)==false) $filterdivisicabarea .="'".$picabangid.$pareaid.$pdividare."',";
        
        
    }
    
    if (!empty($filtercabangarea)) $filtercabangarea="(".substr($filtercabangarea, 0, -1).")";
    if (!empty($filterdivisicabarea)) $filterdivisicabarea="(".substr($filterdivisicabarea, 0, -1).")";
    
    $query ="select *, CAST('' as CHAR(10)) as karyawanid from sls.mr_sales2 where "
            . " tgljual BETWEEN '$pbulan1' AND '$pbulan2' and icabangid='$pidcabang' ";
    $query .=" AND CONCAT(icabangid,areaid, divprodid) IN $filterdivisicabarea ";
    if ($pprodoth=="Y") {
    }else{
        $query .= " AND iprodid NOT IN (select IFNULL(iprodid,'') iprodid from sls.othproduk WHERE divprodid='PEACO')";
    }
    if (!empty($piddist)) $query .= " AND distid='$piddist' ";
    
    /*
    if (!empty($pdivisiid)) {
        //$query .=" AND CONCAT(icabangid,areaid) IN (select distinct CONCAT(IFNULL(icabangid,''),IFNULL(areaid,'')) FROM sls.ispv0 WHERE karyawanid='$pidamkry')";
        $query .=" AND CONCAT(icabangid,areaid) IN $filtercabangarea ";
        $query .=" AND divprodid='$pdivisiid' ";
    }else{
        //$query .=" AND CONCAT(icabangid,areaid, divprodid) IN (select distinct CONCAT(IFNULL(icabangid,''), IFNULL(areaid,''), IFNULL(divisiid,'')) FROM sls.ispv0 WHERE karyawanid='$pidamkry')";
        $query .=" AND CONCAT(icabangid,areaid, divprodid) IN $filterdivisicabarea ";
    }
     * 
     */
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp01 a JOIN $tmp02 b on a.icabangid=b.icabangid AND a.areaid=b.areaid AND a.divprodid=b.divisiid SET "
            . " a.karyawanid=b.karyawanid"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    

    
    $query = "select * from sls.icust WHERE CONCAT(icabangid,areaid,IFNULL(icustid,'')) "
            . " IN (select distinct CONCAT(IFNULL(icabangid,''),IFNULL(areaid,''),IFNULL(icustid,'')) FROM $tmp01)";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from MKT.isektor WHERE iSektorId IN (select distinct IFNULL(iSektorId,'') FROM $tmp03)";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from sls.iproduk";
    $query = "create TEMPORARY table $tmp05 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
        //$query = "UPDATE $tmp03 SET iSektorId='Z1' WHERE IFNULL(iSektorId,'')=''"; 
        $query = "UPDATE $tmp03 SET iSektorId='99' WHERE IFNULL(iSektorId,'')=''"; 
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        //$query = "insert into $tmp04 (iSektorId, nama, aktif, grp_pvt, nama_pvt)values('Z1', 'ZNONE1', 'Y', 'Z1', 'ZNONE1'), ('Z2', 'ZNONE2', 'Y', 'Z2', 'ZNONE2'), ('Z3', 'ZNONECUST', 'Y', 'Z3', 'ZNONECUST')"; 
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "CREATE INDEX `norm1` ON $tmp01 (icabangid, areaid, icustid,iprodid)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp03 (icabangid, areaid, icustid, iSektorId)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp04 (iSektorId)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp05 (iprodid)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "select s.*, prd.nama nama_produk, ic.isektorid, ise.nama nama_sektor, ise.grp_pvt, ise.nama_pvt "
            . " from $tmp01 s LEFT JOIN $tmp03 ic on s.icabangid = ic.iCabangId and s.areaid = ic.areaId and s.icustid = ic.iCustId "
            . " left join $tmp04 ise on ic.iSektorId = ise.iSektorId "
            . " JOIN $tmp05 prd on s.iprodid=prd.iprodid ";
    $query = "create TEMPORARY table $tmp06 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        $query ="select DISTINCT iSektorId FROM $tmp06 WHERE IFNULL(iSektorId,'')=''";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $query ="select DISTINCT iSektorId FROM $tmp04 WHERE IFNULL(iSektorId,'')='99'";
            $tampils= mysqli_query($cnmy, $query);
            $ketemus= mysqli_num_rows($tampils);
            if ($ketemus==0) {
                $query ="insert into $tmp04 select * from MKT.isektor WHERE IFNULL(iSektorId,'')='99'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            }
        }
        
    
        //$query = "UPDATE $tmp06 SET icustid='ZCNONE99', iSektorId='Z3', nama_sektor='ZNONECUST', grp_pvt='Z3', nama_pvt='ZNONECUST' WHERE IFNULL(icustid,'')=''"; 
        $query = "UPDATE $tmp06 a JOIN (select '99' as isektorid, ise.nama, ise.grp_pvt, ise.nama_pvt from $tmp04 ise WHERE ise.iSektorId='99' LIMIT 1) b ON IFNULL(a.iSektorId,'99')=b.iSektorId SET a.icustid='LAIN-LAIN', a.iSektorId=b.iSektorId, a.nama_sektor=b.nama, a.grp_pvt=b.grp_pvt, a.nama_pvt=b.nama_pvt WHERE IFNULL(a.icustid,'')=''"; 
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        //$query = "INSERT INTO $tmp03(icabangid, areaid, icustid, nama, iSektorId) select distinct icabangid, areaid, 'ZCNONE99' icustid, 'ZCNONECUST' nama, iSektorId from $tmp06 where IFNULL(icustid,'')='ZCNONE99'"; 
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //$query = "UPDATE $tmp06 SET iSektorId='Z2', nama_sektor='ZNONE2', grp_pvt='Z2', nama_pvt='ZNONE2' WHERE IFNULL(iSektorId,'')=''";
        $query = "UPDATE $tmp06 a JOIN (select '99' as isektorid, ise.nama, ise.grp_pvt, ise.nama_pvt from $tmp04 ise WHERE ise.iSektorId='99' LIMIT 1) b ON IFNULL(a.iSektorId,'99')=b.iSektorId SET a.iSektorId=b.iSektorId, a.nama_sektor=b.nama, a.grp_pvt=b.grp_pvt, a.nama_pvt=b.nama_pvt WHERE IFNULL(a.iSektorId,'')=''";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    
    // J / G
    if ($pjenissektor=="J") {//isektorid, 
        $query = "select iprodid, nama_produk, nama_sektor, sum(qty) as qty, round(sum(hna * qty), 0) total from $tmp06 "
                . " GROUP BY 1,2,3";//,4
    }else{//, grp_pvt as isektorid
        $query = "select iprodid, nama_produk, nama_pvt as nama_sektor, sum(qty) as qty, round(sum(hna * qty), 0) total from $tmp06 "
                . " GROUP BY 1,2,3";//,4
    }
    $query = "create TEMPORARY table $tmp07 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    // Q / V
    if ($ppilhqtyval=="A") {
    }else{
        if ($ppilhqtyval=="Q") {

        }else{

        }
    }

    
    

        /*
        //jangan lupa temp6 dan  temp7 dibuka di hapusdata
        if ($pjenissektor=="J")
            $tmp07="dbtemp.tmplapslssektor07_1854_04222020112324";
        else
            $tmp07="dbtemp.tmplapslssektor07_1854_04222020112352";

        */
    
    
    
    $query = "select distinct iprodid, nama_produk FROM $tmp07";
    $query = "create TEMPORARY table $tmp08 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    // J / G
    if ($pjenissektor=="J") {//isektorid, 
        $query = "select DISTINCT isektorid, nama_sektor from $tmp06";
    }else{//, grp_pvt as isektorid
        $query = "select DISTINCT grp_pvt isektorid, nama_pvt as nama_sektor from $tmp06";
    }
    $query = "create TEMPORARY table $tmp09 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "create TEMPORARY table $tmp10 (isektorid VARCHAR(100), nama_sektor VARCHAR(100))"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $ifalsesektor=false;
    $query = "select DISTINCT nama_sektor from $tmp09";
    $tampilk1= mysqli_query($cnmy, $query);
    while ($rk1= mysqli_fetch_array($tampilk1)) {
        $pnmsktr=$rk1['nama_sektor'];
        
        
        $query = "select DISTINCT isektorid from $tmp09 WHERE nama_sektor='$pnmsktr'";
        $tampilk2= mysqli_query($cnmy, $query);
        
        $pidsektorp="";
        while ($rk2= mysqli_fetch_array($tampilk2)) {
            $pidsektor=$rk2['isektorid'];
            $pidsektorp .=$pidsektor.",";
        }
        
        if (!empty($pidsektorp)) {
            $pidsektorp=substr($pidsektorp, 0, -1);
            $query = "INSERT INTO $tmp10 (isektorid, nama_sektor)VALUES('$pidsektorp', '$pnmsktr')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $ifalsesektor=true;
        }
    }
    
    
    $query ="select DISTINCT nama_sektor from $tmp07 order by nama_sektor";
    unset($mysql_fields_hidp);//kosongkan array
    unset($mysql_fields_h);//kosongkan array
    unset($mysql_fields_h_nm);//kosongkan array
    $pjmlarray=0;
    if ($ifalsesektor=true) {
        $query ="select DISTINCT isektorid, nama_sektor from $tmp10 order by nama_sektor";
    }else{
        $query ="select DISTINCT '' as isektorid, nama_sektor from $tmp07 order by nama_sektor";
    }
    $tampil= mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pnmsektor=$row['nama_sektor'];
        $pidsektor=$row['isektorid'];
        
        
        $pnmfieldsektor= str_replace("-", "", $pnmsektor);
        $pnmfieldsektor= str_replace("/", "", $pnmfieldsektor);
        $pnmfieldsektor= str_replace(".", "", $pnmfieldsektor);
        $pnmfieldsektor=trim($pnmfieldsektor);
        
        $mysql_fields_h[]=$pnmfieldsektor;
        $mysql_fields_h_nm[]=$pnmsektor;
        $mysql_fields_hidp[]=$pidsektor;
        $pjmlarray++;
        
        $pfieldqty="Q".$pnmfieldsektor;
        $pfieldval="V".$pnmfieldsektor;
        
        $query ="ALTER TABLE $tmp08 ADD COLUMN `$pfieldqty` DECIMAL(20,2), ADD COLUMN `$pfieldval` DECIMAL(20,2)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query ="UPDATE $tmp08 SET `$pfieldqty`=(SELECT SUM(qty) FROM $tmp07 WHERE $tmp07.nama_sektor='$pnmsektor' AND $tmp08.iprodid=$tmp07.iprodid)";
        mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
        
        $query ="UPDATE $tmp08 SET `$pfieldval`=(SELECT SUM(total) FROM $tmp07 WHERE $tmp07.nama_sektor='$pnmsektor' AND $tmp08.iprodid=$tmp07.iprodid)";
        mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
        
        
        
    }
    
    /*
    for($ix=0;$ix<count($mysql_fields_h);$ix++) {
        $pnama=$mysql_fields_h[$ix];
        
        $pnmfieldsektor=$mysql_fields_h[$ix];
        $pnmsektor=$mysql_fields_h_nm[$ix];
        $pidsektor=$mysql_fields_hidp[$ix];
        
        echo "$pnama, $pnmsektor, $pidsektor<br/>";
    }
    */
    
    $query ="ALTER TABLE $tmp08 ADD COLUMN totalqty DECIMAL(20,2), ADD COLUMN totalvalue DECIMAL(20,2)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="UPDATE $tmp08 SET `totalqty`=(SELECT SUM(qty) FROM $tmp07 WHERE $tmp08.iprodid=$tmp07.iprodid)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
    $query ="UPDATE $tmp08 SET `totalvalue`=(SELECT SUM(total) FROM $tmp07 WHERE $tmp08.iprodid=$tmp07.iprodid)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto hapusdata; }
    
?>


<HTML>
<HEAD>
    <title>Laporan Sales Per Sektor</title>
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
    
    <center><div class='h1judul'>Laporan Sales Per Sektor</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Cabang</td><td>:</td><td><?PHP echo "$pnamacabang_p"; ?></td></tr>
            <tr><td>Distributor</td><td>:</td><td><?PHP echo "$pnmdistirbutor"; ?></td></tr>
            <tr><td>AM</td><td>:</td><td><?PHP echo "$pnamaam_p"; ?></td></tr>
            <tr><td>Periode</td><td>:</td><td><?PHP echo "$ptgl1 s/d. $ptgl2"; ?></td></tr>
            <?PHP
            if ($pprodoth=="Y") {
                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>Include Produk Other Peacock</td></tr>";
            }else{
                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>Tanpa Produk Other Peacock</td></tr>";
            }
            ?>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
    <?PHP
    if ($ppilhqtyval=="A") {
    ?>
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
        <tr>
            <th rowspan='2' align='center'>No</th>
            <th rowspan='2' align='center'>Nama Produk</th>
            <?PHP
            if ($pjmlarray>0) {
                foreach($mysql_fields_h_nm as $nmfield_h){
                    echo "<th colspan='2' align='center'>$nmfield_h</th>";
                }
            }
            ?>
            <th colspan='2' align='center'>Total</th>
        </tr>
        
        <tr>
            <?PHP
            if ($pjmlarray>0) {
                foreach($mysql_fields_h_nm as $nmfield_h){
                    echo "<th class='th2' align='center'>Unit</th>";
                    echo "<th class='th2' align='center'>Value</th>";
                }
            }
            ?>
            <th class='th2' align='center'>Unit</th>
            <th class='th2' align='center'>Value</th>
        </tr>
        </thead>
        <tbody>
            <?PHP
            if ($pjmlarray>0) {
                
            
                $no=1;
                $pgtotalqty=0;
                $pgtotalval=0;
                
                unset($parytotaqty);//kosongkan array
                unset($parytotaval);//kosongkan array
                
                $pnomarry=1;
                foreach($mysql_fields_h as $idfield_h){
                    $parytotaqty[$pnomarry]=0;
                    $parytotaval[$pnomarry]=0;
                    $pnomarry++;
                }
                
                $query="select * from $tmp08 order by nama_produk, iprodid";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    
                    //$pdivpdo=$row['divprodid'];
                    $pidprod=$row['iprodid'];
                    $pnmprod=$row['nama_produk'];
                    
                    
                    
                    $ptqty=$row['totalqty'];
                    $ptval=$row['totalvalue'];
                    
                    $pgtotalqty=(double)$pgtotalqty+(double)$ptqty;
                    $pgtotalval=(double)$pgtotalval+(double)$ptval;
                    
                    $ptqty=number_format($ptqty,0,",",",");
                    $ptval=number_format($ptval,0,",",",");
                    

                    
                    $pnomarry=1;
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmprod</td>";
                    foreach($mysql_fields_h as $idfield_h){
                        $nunit=$row['Q'.$idfield_h];
                        $nvalue=$row['V'.$idfield_h];
                        
                        $parytotaqty[$pnomarry]=(DOUBLE)$parytotaqty[$pnomarry]+(DOUBLE)$nunit;
                        $parytotaval[$pnomarry]=(DOUBLE)$parytotaval[$pnomarry]+(DOUBLE)$nvalue;
                        $pnomarry++;
                        
                        $nunit=number_format($nunit,0,",",",");
                        $nvalue=number_format($nvalue,0,",",",");
                        
                        echo "<td nowrap align='right'>$nunit</td>";
                        echo "<td nowrap align='right'>$nvalue</td>";
                    }
                    echo "<td nowrap align='right'>$ptqty</td>";
                    echo "<td nowrap align='right'>$ptval</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                
                $pgtotalqty=number_format($pgtotalqty,0,",",",");
                $pgtotalval=number_format($pgtotalval,0,",",",");
                
                echo "<tr class='tebal'>";
                echo "<td nowrap></td>";
                echo "<td nowrap>Total : </td>";
                $pnomarry=1;
                foreach($mysql_fields_h as $idfield_h){
                    
                    $nunit=$parytotaqty[$pnomarry];
                    $nvalue=$parytotaval[$pnomarry];
                    $pnomarry++;
                    
                    $nunit=number_format($nunit,0,",",",");
                    $nvalue=number_format($nvalue,0,",",",");

                    echo "<td nowrap align='right'>$nunit</td>";
                    echo "<td nowrap align='right'>$nvalue</td>";
                }
                echo "<td nowrap align='right'>$pgtotalqty</td>";
                echo "<td nowrap align='right'>$pgtotalval</td>";
                echo "</tr>";
                
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
            <th align='center'>No</th>
            <th align='center'>Nama Produk</th>
            <?PHP
            if ($pjmlarray>0) {
                foreach($mysql_fields_h_nm as $nmfield_h){
                    echo "<th align='center'>$nmfield_h</th>";
                }
            }
            ?>
            <th align='center'>Total</th>
        </tr>
        </thead>
        <tbody>
            <?PHP
            if ($pjmlarray>0) {
                $pidsektor="";
            
                $no=1;
                $pgtotalqty=0;
                $pgtotalval=0;
                
                unset($parytotaqty);//kosongkan array
                unset($parytotaval);//kosongkan array
                
                $pnomarry=1;
                foreach($mysql_fields_h as $idfield_h){
                    $parytotaqty[$pnomarry]=0;
                    $parytotaval[$pnomarry]=0;
                    $pnomarry++;
                }
                
                $query="select * from $tmp08 order by nama_produk, iprodid";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    
                    //$pdivpdo=$row['divprodid'];
                    $pidprod=$row['iprodid'];
                    $pnmprod=$row['nama_produk'];
                    
                    
                    
                    $ptqty=$row['totalqty'];
                    $ptval=$row['totalvalue'];
                    
                    $pgtotalqty=(double)$pgtotalqty+(double)$ptqty;
                    $pgtotalval=(double)$pgtotalval+(double)$ptval;
                    
                    if ($ppilhqtyval=="Q") {
                        $prtjumlah=$ptqty;
                    }else{
                        $prtjumlah=$ptval;
                    }
                    
                    $pkosong="";
                    $plinkrpttot_valunit=$prtjumlah;
                    if ((double)$prtjumlah<>0) {
                        $prtjumlah=number_format($prtjumlah,0,",",",");
                        $plinkrpttot_valunit="<a href='eksekusi3.php?module=detailsaleslappersektor&act=input&idmenu=$pidmenu&ket=bukan"
                                . "&ipilih=$pkosong&iprd=$pidprod&pper1=$pbulan1&pper2=$pbulan2"
                                . "&pcb=$pidcabang&pkry=$pidamkry&idiv=$pdivisiid&qval=$ppilhqtyval&jns=$pjenissektor&incpoth=$pplhothpea&niddist=$piddist' "
                                . " target='_blank'>$prtjumlah</a>";
                    }else{
                        $plinkrpttot_valunit=number_format($prtjumlah,0,",",",");
                    }
                        
                        
                    $ptqty=number_format($ptqty,0,",",",");
                    $ptval=number_format($ptval,0,",",",");
                    
                    
                    $pnomarry=1;
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmprod</td>";
                    //foreach($mysql_fields_h as $idfield_h){
                    for($ix=0;$ix<count($mysql_fields_h);$ix++) {
                        $pnama=$mysql_fields_h[$ix];

                        $idfield_h=$mysql_fields_h[$ix];
                        $pnmsektor=$mysql_fields_h_nm[$ix];
                        $pidsektor=$mysql_fields_hidp[$ix];
                        
                        if ($pjenissektor=="G") $pidsektor=$pnmsektor;
            
            
                        $nunit=$row['Q'.$idfield_h];
                        $nvalue=$row['V'.$idfield_h];
                        
                        $parytotaqty[$pnomarry]=(DOUBLE)$parytotaqty[$pnomarry]+(DOUBLE)$nunit;
                        $parytotaval[$pnomarry]=(DOUBLE)$parytotaval[$pnomarry]+(DOUBLE)$nvalue;
                        $pnomarry++;
                        
                        
                        if ($ppilhqtyval=="Q") {
                            $prjumlah=$nunit;
                        }else{
                            $prjumlah=$nvalue;
                        }
                        
                        $plinkrpt_valunit=$prjumlah;
                        if ((double)$prjumlah<>0) {
                            $prjumlah=number_format($prjumlah,0,",",",");
                            $plinkrpt_valunit="<a href='eksekusi3.php?module=detailsaleslappersektor&act=input&idmenu=$pidmenu&ket=bukan"
                                    . "&ipilih=$pidsektor&iprd=$pidprod&pper1=$pbulan1&pper2=$pbulan2"
                                    . "&pcb=$pidcabang&pkry=$pidamkry&idiv=$pdivisiid&qval=$ppilhqtyval&jns=$pjenissektor&incpoth=$pplhothpea&niddist=$piddist' "
                                    . " target='_blank'>$prjumlah</a>";
                        }else{
                            $plinkrpt_valunit=number_format($prjumlah,0,",",",");
                        }
                        
                        $nunit=number_format($nunit,0,",",",");
                        $nvalue=number_format($nvalue,0,",",",");
                        
                        
                        
                        echo "<td nowrap align='right'>$plinkrpt_valunit</td>";
                        
                        
                    }
                    
                    /*
                    if ($ppilhqtyval=="Q") {
                        echo "<td nowrap align='right'>$ptqty</td>";
                    }else{
                        echo "<td nowrap align='right'>$ptval</td>";
                    }
                    */
                    echo "<td nowrap align='right'>$plinkrpttot_valunit</td>";
                    
                    
                    echo "</tr>";
                    
                    $no++;
                }
                
                $pgtotalqty=number_format($pgtotalqty,0,",",",");
                $pgtotalval=number_format($pgtotalval,0,",",",");
                
                echo "<tr class='tebal'>";
                echo "<td nowrap></td>";
                echo "<td nowrap>Total : </td>";
                $pnomarry=1;
                $ix=0;
                foreach($mysql_fields_h as $idfield_h){
                    
                    
                    $nunit=$parytotaqty[$pnomarry];
                    $nvalue=$parytotaval[$pnomarry];
                    $pnomarry++;
                    
                    $pnmsektor=$mysql_fields_h_nm[$ix];
                    $pidsektor=$mysql_fields_hidp[$ix]; $ix++;
                    
                    if ($pjenissektor=="G") $pidsektor=$pnmsektor;
                    
                    $pkosong="";
                    $plinkrptgrp_valunit=$nvalue;
                    if ((double)$nvalue<>0) {
                        $nvalue=number_format($nvalue,0,",",",");
                        $plinkrptgrp_valunit="<a href='eksekusi3.php?module=detailsaleslappersektor&act=input&idmenu=$pidmenu&ket=bukan"
                                . "&ipilih=$pidsektor&iprd=$pkosong&pper1=$pbulan1&pper2=$pbulan2"
                                . "&pcb=$pidcabang&pkry=$pidamkry&idiv=$pdivisiid&qval=$ppilhqtyval&jns=$pjenissektor&incpoth=$pplhothpea&niddist=$piddist' "
                                . " target='_blank'>$nvalue</a>";
                    }else{
                        $plinkrptgrp_valunit=number_format($nvalue,0,",",",");
                    }
                    
                    $nunit=number_format($nunit,0,",",",");
                    //$nvalue=number_format($nvalue,0,",",",");
                    
                    echo "<td nowrap align='right'>$plinkrptgrp_valunit</td>";
                }
                
                $pkosong="";
                $plinkrptgrp_valunit="<a href='eksekusi3.php?module=detailsaleslappersektor&act=input&idmenu=$pidmenu&ket=bukan"
                        . "&ipilih=$pkosong&iprd=$pkosong&pper1=$pbulan1&pper2=$pbulan2"
                        . "&pcb=$pidcabang&pkry=$pidamkry&idiv=$pdivisiid&qval=$ppilhqtyval&jns=$pjenissektor&incpoth=$pplhothpea&niddist=$piddist' "
                        . " target='_blank'>$pgtotalval</a>";
                    
                    
                echo "<td nowrap align='right'>$plinkrptgrp_valunit</td>";
                echo "</tr>";
                
                
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
            top: 35;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            border-top: 1px solid #000;
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
    
    
        $(document).ready(function() {
            
            
            var table1 = $('#mydatatable1x').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [2,4,5] },//right
                    { className: "text-nowrap", "targets": [0,1,2,3,4,5] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );
            
            var table = $('#mydatatable2x').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [1,2] },//right
                    { className: "text-nowrap", "targets": [0,1,2] }//nowrap

                ],
                bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                "bPaginate": false
            } );
            

        } );
        
        
        function TambahDataInput(eidbank){
            $.ajax({
                type:"post",
                url:"module/mod_br_danabank/tambah_trans_bank.php?module=viewisibankspdall",
                data:"uidbank="+eidbank,
                success:function(data){
                    $("#myModal").html(data);
                }
            });
        }
    
    
    
    </script>
    
    
    
</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp08");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp09");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp10");
    mysqli_close($cnmy);
?>