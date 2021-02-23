<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $cnit=$cnmy;
    
    
// Hapus 
if ($module=='isikartustatus' AND $act=='hapus')
{
    $kodenya=$_GET['id'];
    
    $query =  "DELETE from hrd.ks1 WHERE concat(IFNULL(srid,''), IFNULL(dokterid,''), IFNULL(bulan,''), IFNULL(idapotik,''), IFNULL(iprodid,''), nourut_ks)='$kodenya' LIMIT 1";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    mysqli_close($cnit);

    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');

    exit;   
}
elseif ($module=='isikartustatus' AND ($act=='input' OR $act=='update') )
{
    
    $puserid=$_POST['e_idinputuser'];
    $pcardid=$_POST['e_idcarduser'];
    $pidgrpuser=$_POST['e_idgrpuser'];
    
    if (empty($puserid)) {
        $puserid="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
        if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

        if (empty($puserid)) {
            mysqli_close($cnit);
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
    }
    
    if (empty($pidgrpuser)) $pidgrpuser=$_SESSION['GROUP'];
    
    
    $kodenya=$_POST['e_id'];
    $pbln = $_POST['e_bulan'];
    $pidkry = $_POST['cb_karyawan'];
    $piddokt = $_POST['e_iddokt'];
    $papotikid = $_POST['cb_idapotik'];
    $pidapt = $_POST['cb_apotik'];
    $pidapttipe = $_POST['txt_apttyp'];
    $pcn = $_POST['e_cn'];
    $ptotal = $_POST['e_total'];
    //$pidapttipe="";
    
    $pbln= date("Y-m", strtotime($pbln));
    if (empty($pcn)) $pcn=0;
    if (empty($ptotal)) $ptotal=0;
    
    $pcn=str_replace(",","", $pcn);
    $ptotal=str_replace(",","", $ptotal);
    
    
    //$query = "select aptid as aptid, nama as nama, apttype as apttype from hrd.mr_apt where srid='$pidkry' and aptid='$pidapt' order by nama";
    //$query = "select aptid as aptid, nama as nama, apttype as apttype from hrd.mr_apt where idapotik='$papotikid' order by nama";
    //$result = mysqli_query($cnit, $query); 
    //$row = mysqli_fetch_array($result);
    //$pidapttipe  = $row['apttype'];
        
    
    if ($pidgrpuser=="1" OR $pidgrpuser=="24") {
        
    }else{
        //$query  = "select distinct dokterid FROM hrd.ks1 WHERE bulan='$pbln' AND srid='$pidkry' AND dokterid='$piddokt' AND aptid='$pidapt'";
        $query  = "select distinct dokterid FROM hrd.ks1 WHERE bulan='$pbln' AND srid='$pidkry' AND dokterid='$piddokt' AND idapotik='$papotikid'";
        $result = mysqli_query($cnit, $query);
        $record = mysqli_num_rows($result);
        if ((DOUBLE)$record>0) {
            $bolehinput="Sudah ada data... Tidak bisa diubah / hapus";
            echo $bolehinput; mysqli_close($cnit); exit;
        }
    }
    //echo "GRP : $pidgrpuser, $kodenya, bln : $pbln, kry : $pidkry, dokt : $piddokt, apt : $papotikid - $pidapt (tipe : $pidapttipe), cn : $pcn, ttl : $ptotal<br/>"; exit;
    $ptotalrecord=0;
    $pbolehsimpan=false;
    unset($pinsert_data_detail);//kosongkan array
    foreach ($_POST['chk_kodeid'] as $no_brid) {
        $pdet_qty= $_POST['e_txtqty'][$no_brid];
        $pdet_hna= $_POST['e_txthna'][$no_brid];
        $pdet_jml= $_POST['e_txtjml'][$no_brid];
        
        if (empty($pdet_qty)) $pdet_qty=0;
        if (empty($pdet_hna)) $pdet_hna=0;
        if (empty($pdet_jml)) $pdet_jml=0;
        
        
        ////$query =  "DELETE from hrd.ks1 WHERE srid='$pidkry' and bulan='$pbln' and dokterid='$piddokt' and aptid='$pidapt' AND iprodid='$no_brid' LIMIT 1";
        //$query =  "DELETE from hrd.ks1 WHERE srid='$pidkry' and bulan='$pbln' and dokterid='$piddokt' and idapotik='$papotikid' AND iprodid='$no_brid' LIMIT 1";
        //mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
        if ((DOUBLE)$pdet_qty==0 AND (DOUBLE)$pdet_jml==0) {
        }else{
            $ptotalrecord++;
            $pbolehsimpan=true;
            $pdet_qty=str_replace(",","", $pdet_qty);
            $pdet_hna=str_replace(",","", $pdet_hna);
            $pdet_jml=str_replace(",","", $pdet_jml);
            
            //echo "qty = $pdet_qty, hna = $pdet_hna, jml = $pdet_jml<br/>";
            
            $pinsert_data_detail[] = "('$pidkry', '$pbln', '$piddokt', '$pidapt', '$pidapttipe', '$no_brid', '$pdet_qty', '$pdet_hna', '', '$pcn', '$papotikid', '$pcardid')";
            
            $pinsert_data_detail_pl = "('$pidkry', '$pbln', '$piddokt', '$pidapt', '$pidapttipe', '$no_brid', '$pdet_qty', '$pdet_hna', '', '$pcn', '$papotikid', '$pcardid')";
            
            //$query_detail="INSERT INTO hrd.ks1 (srid,bulan,dokterid,aptid,apttype,iprodid,qty,hna,approved,cn_ks1,idapotik, userid) VALUES ".$pinsert_data_detail_pl;
            //mysqli_query($cnit, $query_detail); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan." INSERT "; mysqli_close($cnit); exit; }
            
        
        }
        
    }
    
    
    //$query =  "DELETE from hrd.ks1 WHERE srid='$pidkry' and bulan='$pbln' and dokterid='$piddokt' and aptid='$pidapt' LIMIT $ptotalrecord";
    $query =  "DELETE from hrd.ks1 WHERE srid='$pidkry' and bulan='$pbln' and dokterid='$piddokt' and idapotik='$papotikid' LIMIT $ptotalrecord";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
    if ($pbolehsimpan == true) {
        $query_detail="INSERT INTO hrd.ks1 (srid,bulan,dokterid,aptid,apttype,iprodid,qty,hna,approved,cn_ks1,idapotik, userid) VALUES ".implode(', ', $pinsert_data_detail);
        mysqli_query($cnit, $query_detail); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan." INSERT "; mysqli_close($cnit); exit; }
    }


    $query =  "DELETE from hrd.ks1_diskonbaru WHERE srid='$pidkry' and bulan='$pbln' and dokterid='$piddokt' LIMIT 1";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
    if ($pbolehsimpan == true) {
        $query =  "INSERT INTO hrd.ks1_diskonbaru (karyawanid, dokterid, bulan, cn, aktif)VALUES
            ('$pidkry', '$piddokt', '$pbln', '0', 'N')";
        mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    }
    
    
    
    mysqli_close($cnit);

    header('location:../../eksekusi3.php?module=isikartustatusberhasil&idmenu='.$idmenu.'&act=complete');

    exit;
}

mysqli_close($cnit);
?>