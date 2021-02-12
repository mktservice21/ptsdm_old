<?PHP
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $pidcard=$_SESSION['IDCARD'];
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    
    
if ($module=='lihatkseditapt' AND $act=='simpanapt')
{
    include "../../config/koneksimysqli.php";
    
    $pidsr=$_POST['cb_karyawan'];
    $piddokter=$_POST['cb_dokter'];
    $pidtype=$_POST['txt_apttype'];
    $pidapt=$_POST['txt_aptid'];
    $pbln=$_POST['txt_bulan'];
    $pidapotik=$_POST['e_apotikid'];
    
    $myArray = explode(',', $pidtype);
    $pidtype="";
    foreach($myArray as $myArray){
        $pidtype_="";
        if ($myArray=="1") {
            $pidtype_="'1',";
        }else{
            $pidtype_="'0','',";
        }
        $pidtype .=$pidtype_;
    }
    if (!empty($pidtype)) $pidtype="(".substr($pidtype, 0, -1).")";
    
    $myArray = explode(',', $pidapt);
    $pidapt="";
    foreach($myArray as $myArray){
        $pidapt .="'".$myArray."',";
    }
    if (!empty($pidapt)) $pidapt="(".substr($pidapt, 0, -1).")";
    
    $myArray = explode(',', $pbln);
    $pbln="";
    foreach($myArray as $myArray){
        $pbln .="'".$myArray."',";
    }
    if (!empty($pbln)) $pbln="(".substr($pbln, 0, -1).")";
    
    //echo "aptid : $pidapt - apttype : $pidtype - bulan : $pbln";
    
    $query = "UPDATE hrd.ks1 as a SET a.idapotik='$pidapotik', a.userid='$pidcard' WHERE a.srid='$pidsr' AND a.dokterid='$piddokter' AND "
            . " IFNULL(a.idapotik,'') IN ('', '0', '0000000000') AND "
            . " bulan IN $pbln AND aptid IN $pidapt AND IFNULL(apttype,'') IN $pidtype";
    //echo $query;
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    
    mysqli_close($cnmy);
    
    
    header('location:../../eksekusi3.php?module=simpaneditapotikks&idmenu='.$idmenu.'&act=complete');
}

?>

