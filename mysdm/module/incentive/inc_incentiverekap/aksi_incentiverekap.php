<?php
    //ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
session_start();
$puser="";
if (isset($_SESSION['USERID'])) $puser=$_SESSION['USERID'];

if (empty($puser)) {
    echo "ANDA HARUS LOGIN ULANG....!!!";
    exit;
}

$pmyidcard=$_SESSION['IDCARD'];
$pmyjabatanid=$_SESSION['JABATANID'];
$pmynamlengkap=$_SESSION['NAMALENGKAP'];
$pidgroup=$_SESSION['GROUP'];

$ppilihrpt=$_GET['ket'];

$pmodule=$_GET['module'];
$pact=$_GET['act'];
$pidmenu=$_GET['idmenu'];

$pviewdate=date("d/m/Y H:i:s");

$tgl01=$_POST['bulan'];
$pjabatan=$_POST['cb_jabatan'];
$pincfrom=$_POST['cb_from'];
$ptiperpt=$_POST['cb_rpttipe'];
$preportpl=$_POST['cb_report'];

$pfbln = date("Y-m", strtotime($tgl01));
$pbln1 = date("Y-m-01", strtotime($tgl01));
$pbln2 = date("Y-m-t", strtotime($tgl01));
$pbulan = date("F Y", strtotime($tgl01));

$milliseconds = round(microtime(true) * 1000);
$now=date("mdYhis");
$tmp01 ="dbtemp.tmprptincrkp01_".$puser."_$now$milliseconds";
$tmp02 ="dbtemp.tmprptincrkp02_".$puser."_$now$milliseconds";
$tmp03 ="dbtemp.tmprptincrkp03_".$puser."_$now$milliseconds";

include("config/koneksimysqli_ms.php");
include("config/common.php");

if ($ptiperpt=="D") {
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN INCENTIVE BY DETAIL.xls");
    }

    include("incentiverekap_detail.php");
}else{

    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN INCENTIVE BY SUMMARY.xls");
    }

    include("incentiverekap_sum.php");
}

?>