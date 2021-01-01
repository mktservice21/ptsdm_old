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
$pmodule=$_GET['module'];
$pact=$_GET['act'];
$pidmenu=$_GET['idmenu'];

include "../../config/koneksimysqli.php";
include "../../config/fungsi_sql.php";

$berhasil="Tidak ada data yang disimpan";

if ($pmodule=='entrybrdcc')
{
    if ($pact=="realsimpansatu") {
        
        $pidbr=$_POST['uidbr'];
        $pjmlreal=$_POST['ujmlreal'];
        if (empty($pjmlreal)) $pjmlreal=0;
        $pjmlreal=str_replace(",","", $pjmlreal);

        $plain=$_POST['ulain'];
        $nbatal=$_POST['ubatal'];
        $ptglterima=$_POST['utglterima'];

        $tglrealterima="0000-00-00";
        if (!empty($ptglterima)) {
            $tglrealterima= date("Y-m-d", strtotime($ptglterima));
        }

        $pbatal="";
        $pketntl="";
        if ($nbatal=="true") {
            $pbatal="Y";
            $pketntl=$_POST['utxtbatal'];
            
            if (!empty($pketntl)) $pketntl = str_replace("'", " ", $pketntl);
        }

        //echo "$pmodule, $pact, $pidmenu, $pidbr, $pjmlreal, $plain, $tglrealterima, $pbatal, $pketntl";exit;
        
        mysqli_query($cnmy, "UPDATE hrd.br0 SET jumlah1='$pjmlreal', tgltrm='$tglrealterima', "
                . " lampiran='Y', batal='$pbatal', lain2='$plain', alasan_b='$pketntl' WHERE brid='$pidbr' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "GAGAL.... Error Simpan"; mysqli_close($cnmy); exit; }
        
        $berhasil="";
        
        mysqli_close($cnmy);
        echo $berhasil;
        exit;
    }
}

mysqli_close($cnmy);
echo $berhasil;
?>