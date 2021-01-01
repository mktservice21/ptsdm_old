<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    
// Hapus 
if ($module=='isikartustatus' AND $act=='hapus')
{
    $kodenya=$_GET['id'];
    
    $query =  "DELETE from hrd.ks1 WHERE concat(IFNULL(srid,''), IFNULL(dokterid,''), IFNULL(bulan,''), IFNULL(aptid,''), IFNULL(iprodid,''))='$kodenya' LIMIT 1";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
    mysqli_close($cnit);

    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');

    exit;   
}
elseif ($module=='isikartustatus' AND ($act=='input' OR $act=='update') )
{
    
    $puserid=$_POST['e_idinputuser'];
    $pcardid=$_POST['e_idcarduser'];
    
    if (empty($puserid)) {
        $puserid="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

        if (empty($puserid)) {
            mysqli_close($cnit);
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
    }
    
    
    $kodenya=$_POST['e_id'];
    $pbln = $_POST['e_bulan'];
    $pidkry = $_POST['cb_karyawan'];
    $piddokt = $_POST['e_iddokt'];
    $pidapt = $_POST['cb_apotik'];
    $pcn = $_POST['e_cn'];
    $ptotal = $_POST['e_total'];
    $pidapttipe="";
    
    $pbln= date("Y-m", strtotime($pbln));
    if (empty($pcn)) $pcn=0;
    if (empty($ptotal)) $ptotal=0;
    
    $pcn=str_replace(",","", $pcn);
    $ptotal=str_replace(",","", $ptotal);
    
    
    $query = "select aptid as aptid, nama as nama, apttype as apttype from hrd.mr_apt where srid='$pidkry' and aptid='$pidapt' order by nama";
    $result = mysqli_query($cnit, $query); 
    $row = mysqli_fetch_array($result);
    $pidapttipe  = $row['apttype'];
        
    //echo "$kodenya, bln : $pbln, kry : $pidkry, dokt : $piddokt, apt : $pidapt ($pidapttipe), cn : $pcn, ttl : $ptotal<br/>";
    
    $pbolehsimpan=false;
    unset($pinsert_data_detail);//kosongkan array
    foreach ($_POST['chk_kodeid'] as $no_brid) {
        $pdet_qty= $_POST['e_txtqty'][$no_brid];
        $pdet_hna= $_POST['e_txthna'][$no_brid];
        $pdet_jml= $_POST['e_txtjml'][$no_brid];
        
        if (empty($pdet_qty)) $pdet_qty=0;
        if (empty($pdet_hna)) $pdet_hna=0;
        if (empty($pdet_jml)) $pdet_jml=0;
        
        if ((DOUBLE)$pdet_qty==0 AND (DOUBLE)$pdet_jml==0) {
        }else{
            $pbolehsimpan=true;
            $pdet_qty=str_replace(",","", $pdet_qty);
            $pdet_hna=str_replace(",","", $pdet_hna);
            $pdet_jml=str_replace(",","", $pdet_jml);
            
            //echo "qty = $pdet_qty, hna = $pdet_hna, jml = $pdet_jml<br/>";
            
            $pinsert_data_detail[] = "('$pidkry', '$pbln', '$piddokt', '$pidapt', '$pidapttipe', '$no_brid', '$pdet_qty', '$pdet_hna', '', '$pcn')";
        }
        
    }
    
    
    $query =  "DELETE from hrd.ks1 WHERE srid='$pidkry' and bulan='$pbln' and dokterid='$piddokt' and aptid='$pidapt'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
    if ($pbolehsimpan == true) {
        $query_detail="INSERT INTO hrd.ks1 (srid,bulan,dokterid,aptid,apttype,iprodid,qty,hna,approved,cn_ks1) VALUES ".implode(', ', $pinsert_data_detail);
        mysqli_query($cnit, $query_detail); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan." INSERT "; mysqli_close($cnit); exit; }
    }
    
    
    
    
    mysqli_close($cnit);

    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');

    exit;
}

mysqli_close($cnit);
?>