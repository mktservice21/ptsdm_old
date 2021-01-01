<?php
session_start();
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
$puserid="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}

$pidcard=$_SESSION['IDCARD'];
$module=$_GET['module'];
$nmodul=$_GET['nmodul'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


if ($module=='tgtaksiuploadspdbpjs' OR $module=='spdbpjs')
{
    include "../../config/koneksimysqli.php";
    
    if ($module=='spdbpjs') {
        $ptglpil=$_GET['hapusnix'];
        $pperiode_ = date("Ym", strtotime($ptglpil));
    }else
        $pperiode_=$_GET['hapusnix'];
    
    $pidinput="";
    if (isset($_GET['nidinput'])) $pidinput=$_GET['nidinput'];
    $pkodeid="2";
    $psubkode="25";
    
    if (!empty($pperiode_)) {
        
        if (!empty($pidinput)) {
            $query = "UPDATE dbmaster.t_suratdana_br SET stsnonaktif='Y' WHERE idinput='$pidinput' and kodeid='$pkodeid' AND subkode='$psubkode' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); mysqli_close($cnmy); exit; }
        }
        
        $query = "UPDATE dbmaster.t_spd_bpjs0 SET stsnonaktif='Y' WHERE periode='$pperiode_' AND "
                . " ( IFNULL(dir1_tgl,'')='' OR IFNULL(dir1_tgl,'0000-00-00')='0000-00-00' OR IFNULL(dir1_tgl,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); mysqli_close($cnmy); exit; }
    }
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$nmodul.'&idmenu='.$idmenu.'&act=sudahsimpan');
}

?>