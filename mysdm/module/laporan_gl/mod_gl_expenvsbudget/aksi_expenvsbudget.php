<?php

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();
if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}

$ppilformat=1;
$ppilihrpt="";
if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];

if ($ppilihrpt=="excel") {
    $ppilformat=3;
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");
    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=Report Expense VS Budget.xls");
}


$pmodule=$_GET['module'];

include("config/koneksimysqli.php");
include("config/fungsi_sql.php");
include("config/common.php");

$pkaryawanid_user=$_SESSION['IDCARD'];
$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.tmpprosbgtexp01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmpprosbgtexp02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmpprosbgtexp03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmpprosbgtexp04_".$puserid."_$now ";
$tmp05 =" dbtemp.tmpprosbgtexp05_".$puserid."_$now ";
$tmp06 =" dbtemp.tmpprosbgtexp06_".$puserid."_$now ";


    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    $pmobile=$_SESSION['MOBILE'];
    
    
    $psemuadep=false;
    $pbolehpilihdep=false;
    $ppilihlini_produk="";
    $query = "select * from dbproses.maping_karyawan_dep WHERE karyawanid='$fkaryawan' AND iddep='ALL'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $psemuadep=true;
        $pbolehpilihdep=true;
    }
    
    $pilihregion="";
    if ($fjbtid=="05") {
        $query = "select region FROM dbmaster.t_karyawan_posisi WHERE karyawanid='$fkaryawan'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        $row= mysqli_fetch_array($tampil);
        $pilihregion=$row['region'];
    }
    
    
$ptahun = $_POST['e_tahun'];
$piddep = $_POST['cb_dept'];
$pidpengajuan = $_POST['cb_pengajuan'];
$pregion = $_POST['cb_region'];
$pidkrysm="";
if (isset($_POST['cb_karyawansm'])) $pidkrysm = $_POST['cb_karyawansm'];
$pliniproduk = $_POST['cb_liniproduk'];

$pnamaregion="";
if ($pregion=="B") $pnamaregion="Barat";
elseif ($pregion=="T") $pnamaregion="Timur";

$pnamadep="";
if (!empty($piddep)) {
    $query = "select nama_dep FROM dbmaster.t_department WHERE iddep='$piddep'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pnamadep=$row['nama_dep'];
}

$pnamapengajuan=$pidpengajuan;
if ($pidpengajuan=="ETH") $pnamapengajuan="ETHICAL";
elseif ($pidpengajuan=="OTC" OR $pidpengajuan=="OT" OR $pidpengajuan=="CHC") $pnamapengajuan="CHC";
elseif ($pidpengajuan=="OTH" OR $pidpengajuan=="OTHER" OR $pidpengajuan=="OTHERS") $pnamapengajuan="OTHERS";

$pchkallexp="";
if (isset($_POST['c_allexp'])) $pchkallexp = $_POST['c_allexp'];

$pchksumary="";
if (isset($_POST['chk_sum'])) $pchksumary = $_POST['chk_sum'];

$pidcabangdivisi="";
$pidcoa="";

if (isset($_POST['chkbox_cab'])) $pidcabangdivisi = $_POST['chkbox_cab'];
if (isset($_POST['chkbox_coa'])) $pidcoa = $_POST['chkbox_coa'];


$filter_coa="";
$filter_coa2="";
if (!empty($pidcoa)){
    $tag = implode(',',$pidcoa);
    $arr_kata = explode(",",$tag);
    $count_kata = count($arr_kata);
    $jumlah_tag = substr_count($tag, ",") + 1;
    $u=0;
    $unsel="";
    $unsel2="";
    for ($x=0; $x<=$jumlah_tag; $x++){
        if (isset($arr_kata[$u])){
            $uTag=trim($arr_kata[$u]);
            
            $unsel=$unsel."'".$uTag."',";
            
            $unsel2=$unsel2."".$uTag.",";
        }
        $u++;
    }
    
    if (!empty($unsel)) {
        $filter_coa="(".substr($unsel,0,strlen($unsel)-1).")";
        $filter_coa2=substr($unsel2,0,strlen($unsel2)-1);
    }

}

$pilih_allexp="";
if ($pchkallexp=="allexpen") {
    $filter_coa="";
    $filter_coa2="";
    $pilih_allexp="All Expense";
}



$ppilihsales=false;
$ppilihsales_gsm=false;
$ppilihsales_sm=false;
$ppilihmarketing=false;

if ($piddep=="SLS" OR $piddep=="SLS01") {
    $ppilihsales=true;
}

if ($piddep=="SLS02") {
    $ppilihsales_gsm=true;
}

if ($piddep=="SLS03") {
    $ppilihsales_sm=true;
}

if ($piddep=="MKT") {
    $ppilihmarketing=true;
}


$pcabangdivisi="";
$filtercabang="";
$filterdivisi="";
$filternamacabang="";

$pcabangdivisi2="";
$filtercabang2="";
$filterdivisi2="";

if ($ppilihsales == true OR $ppilihmarketing == true) {

    if (!empty($pidcabangdivisi)){
        $tag = implode(',',$pidcabangdivisi);
        $arr_kata = explode(",",$tag);
        $count_kata = count($arr_kata);
        $jumlah_tag = substr_count($tag, ",") + 1;
        $u=0;
        $unsel="";
        for ($x=0; $x<=$jumlah_tag; $x++){
            if (isset($arr_kata[$u])){
                $uTag=trim($arr_kata[$u]);
                if (!empty($uTag)) {
                    
                    $idcabdiv_ = explode("|", $uTag);
                    
                    $pidcabang=$idcabdiv_[0];
                    $piddivisi=$idcabdiv_[1];
                    
                    if (strpos($filtercabang, $pidcabang)==false) {
                        $filtercabang .="'".$pidcabang."',";
                        $filtercabang2 .="".$pidcabang.",";
                    }
                    if (strpos($filterdivisi, $piddivisi)==false) {
                        $filterdivisi .="'".$piddivisi."',";
                        $filterdivisi2 .="".$piddivisi.",";
                    }
                    
                    if (strpos($filterdivisi, $uTag)==false) {
                        $pcabangdivisi .="'".$uTag."',";
                        $pcabangdivisi2 .="".$uTag.",";
                    }
                    
                    //$unsel=$unsel."'".$uTag."',";
                    //echo "$pidcabang<br/>";
                }
            }
            $u++;
        }

        if (!empty($pcabangdivisi)) $pcabangdivisi="(".substr($pcabangdivisi, 0, -1).")";
        if (!empty($pcabangdivisi2)) $pcabangdivisi2=substr($pcabangdivisi2, 0, -1);
        
        if (!empty($filtercabang)) $filtercabang="(".substr($filtercabang, 0, -1).")";
        if (!empty($filterdivisi)) $filterdivisi="(".substr($filterdivisi, 0, -1).")";

        if (!empty($filtercabang2)) $filtercabang2=substr($filtercabang2, 0, -1);
        if (!empty($filterdivisi2)) $filterdivisi2=substr($filterdivisi2, 0, -1);
        
        
        $query_cabang = "select icabangid, nama, 'ETH' as iket, 'SLSPM' as ists from mkt.icabang "
                . " UNION select icabangid_o, nama, 'OTC' as iket, 'SLS' as ists from mkt.icabang_o "
                . " UNION select cabangid_ho, nama, 'OTC' as iket, 'PM' as ists from dbmaster.cabang_otc";
        $query_cabang="select icabangid, nama as nama_cabang, iket FROM ( ".$query_cabang." ) as tblcabang WHERE 1=1 ";
        
        if (!empty($pcabangdivisi)) {
            
            if ($ppilihsales == true) {
                $query_cabang .=" AND IFNULL(ists,'') NOT IN ('PM')";
            }elseif ($ppilihmarketing == true) {
                $query_cabang .=" AND IFNULL(ists,'') IN ('PM', 'SLSPM')";
            }
            $query_cabang .=" AND CONCAT(icabangid, '|', iket) IN $pcabangdivisi";
            
            if (!empty($filterdivisi)) $query_cabang .=" AND iket IN $filterdivisi";
            
            $query_cabang .=" ORDER BY 3, 2,1";
            
            $tampilcab= mysqli_query($cnmy, $query_cabang);
            
            $ndiv_ket="";
            while ($rowc= mysqli_fetch_array($tampilcab)) {
                $pnamacabang=$rowc['nama_cabang'];
                $pnket=$rowc['iket'];
                
                if (strpos($ndiv_ket, $pnket)==false) {
                    if (empty($ndiv_ket)){
                        if ($pnket=="ETH") $filternamacabang.="<b>Cabang ETHICAL :</b> ";
                        elseif ($pnket=="OTC") $filternamacabang.="<b>Cabang CHC :</b> ";
                    }else{
                        if ($pnket=="ETH") $filternamacabang.="<br/><b>Cabang ETHICAL :</b> ";
                        elseif ($pnket=="OTC") $filternamacabang.="<br/><b>Cabang CHC :</b> ";
                    }
                    
                    
                    $ndiv_ket .="'".$pnket."',";
                }
                
                $filternamacabang .="".$pnamacabang.", ";
            }
            if (!empty($filternamacabang)) $filternamacabang=substr($filternamacabang, 0, -2);
        }
        
        
    }
    

}

//echo "$filtercabang <br/> $filterdivisi<br/>";
       

$query = "SELECT tipe, bulan, coa4, SUM(jumlah) as jumlah FROM dbproses.proses_budget_expenses WHERE "
        . " tahun='$ptahun' ";
if (!empty($piddep)) $query .=" AND iddep='$piddep' ";
else{
    
    if ($psemuadep==true) {
        
    }else{
    
        $query .=" AND iddep IN (select DISTINCT IFNULL(iddep,'') from dbproses.maping_karyawan_dep WHERE karyawanid='$pkaryawanid_user') ";

        if ($fjbtid=="36") {
            $query .=" AND divisi_pengajuan IN ('OTC', 'OT', 'CHC') ";
        }elseif ($fjbtid=="20") {
            $query .=" AND divisi_pengajuan IN ('ETH') ";
            
            if (empty($pcabangdivisi)) {
                $query .=" AND icabangid IN (select distinct IFNULL(icabangid,'') from sls.ism0 where karyawanid='$pkaryawanid_user') ";
            }
        }elseif ($fjbtid=="05") {
            if ($pkaryawanid_user=="0000000158") $query .=" AND region='B' ";
            elseif ($pkaryawanid_user=="0000000159") $query .=" AND region='T' ";
        }
    
    }
    
    
}
if (!empty($filter_coa)) $query .=" AND coa4 IN $filter_coa ";

if ($ppilihsales == true) {
    
    if (empty($pidpengajuan)) {
        if (!empty($filterdivisi)) $query .=" AND divisi_pengajuan IN $filterdivisi ";
    }else{
        $query .=" AND divisi_pengajuan='$pidpengajuan' ";
    }
    //if (!empty($filtercabang)) $query .=" AND icabangid IN $filtercabang ";
    if (!empty($pcabangdivisi)) $query .=" AND CONCAT(icabangid, '|', divisi_pengajuan) IN $pcabangdivisi ";
}elseif ($ppilihsales_gsm==true) {
    if (!empty($pregion)) {
        $query .=" AND region='$pregion' ";
    }
}elseif ($ppilihmarketing == true) {
    
    /*
    if (!empty($pidpengajuan)) {
        $query .=" AND divisi_pengajuan='$pidpengajuan' ";
    }
    
    if ($pidpengajuan=="ETH") {
        //if (!empty($filtercabang)) $query .=" AND icabangid IN $filtercabang ";
    }elseif ($pidpengajuan=="OTC" OR $pidpengajuan=="OT" OR $pidpengajuan=="CHC") {
        if (!empty($filtercabang)) $query .=" AND icabangid IN $filtercabang ";
    }
    */
    
    //if ($pliniproduk=="EAGLE") $query .=" AND karyawanid='0000000257' ";
    //elseif ($pliniproduk=="PEACO") $query .=" AND karyawanid='0000000910' ";
    //elseif ($pliniproduk=="PIGEO") $query .=" AND karyawanid='0000000157' ";
    //elseif ($pliniproduk=="OT" OR $pliniproduk=="OTC" OR $pliniproduk=="CHC") $query .=" AND karyawanid='0000001556' ";
    
    
    if ($pliniproduk=="EAGLE") $query .=" AND ( karyawanid='0000000257' OR (divisi_pengajuan='ETH' AND iddep='MKT') ) ";
    elseif ($pliniproduk=="PEACO") $query .=" AND ( karyawanid='0000000910' OR (divisi_pengajuan='ETH' AND iddep='MKT') ) ";
    elseif ($pliniproduk=="PIGEO") $query .=" AND ( karyawanid='0000000157' OR (divisi_pengajuan='ETH' AND iddep='MKT') ) ";
    elseif ($pliniproduk=="OT" OR $pliniproduk=="OTC" OR $pliniproduk=="CHC") $query .=" AND (karyawanid='0000001556' OR (divisi_pengajuan='OTC' AND iddep='MKT') ) ";
    
}elseif ($ppilihsales_sm == true) {
    if (!empty($pidkrysm)) {
        $query .=" AND karyawanid='$pidkrysm' ";
    }else{
        if ($fjbtid=="05" AND !empty($pilihregion)) {
            $query .= " AND karyawanid IN (select distinct IFNULL(karyawanid,'') from mkt.ism0 as a "
                    . " JOIN mkt.icabang as b on a.icabangid=b.iCabangId WHERE region='$pilihregion') ";
        }
    }
}

$query .="GROUP BY 1,2,3";
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "SELECT DISTINCT coa4 FROM $tmp01";
$query = "create TEMPORARY table $tmp02 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$nadd_filed="";

for ($ix=1;$ix<=12;$ix++) {
    $nadd_filed .=" ADD COLUMN b".$ix." DECIMAL(20,2), ADD COLUMN e".$ix." DECIMAL(20,2), ";
}
$nadd_filed .=" ADD COLUMN b_total DECIMAL(20,2), ADD COLUMN e_total DECIMAL(20,2)";
$query = "ALTER table $tmp02 $nadd_filed ";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

for ($ix=1;$ix<=12;$ix++) {
    $b_field="b".$ix;
    $e_field="e".$ix;
    
    $n_bln = str_pad($ix, 2, '0', STR_PAD_LEFT);
    
    $nbulan=$ptahun."-".$n_bln;
    
    $query = "UPDATE $tmp02 as a JOIN (SELECT coa4, LEFT(bulan,7) as bulan, SUM(jumlah) as jumlah FROM $tmp01 WHERE LEFT(bulan,7)='$nbulan' AND tipe='BUDGET' GROUP BY 1,2) as b "
            . " on a.coa4=b.coa4 SET a.".$b_field."=b.jumlah";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN (SELECT coa4, LEFT(bulan,7) as bulan, SUM(jumlah) as jumlah FROM $tmp01 WHERE LEFT(bulan,7)='$nbulan' AND tipe='EXPENSES' GROUP BY 1,2) as b "
            . " on a.coa4=b.coa4 SET ".$e_field."=b.jumlah";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
}

$query = "ALTER table $tmp02 ADD COLUMN nama_coa VARCHAR(200)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp02 as a JOIN dbmaster.coa_level4 as b on a.coa4=b.COA4 SET a.nama_coa=b.NAMA4";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

?>

<HTML>
<HEAD>
  <TITLE>Report Expense VS Budget</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <?PHP
    if ($ppilihrpt=="excel") {
    }else{
        echo "<script src=\"vendors/jquery/dist/jquery.min.js\"></script>";
        echo "<link href=\"vendors/font-awesome/css/font-awesome.min.css\" rel=\"stylesheet\">";
    }
    ?>
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>
<script>
</script>

<BODY onload="initVar()">
    
    <?PHP
    if ($ppilihrpt=="excel") {
    }else{
        echo "<button onclick=\"topFunction()\" id=\"myBtn\" title=\"Go to top\">Top</button>";
    }
    ?>
    
    
    <?PHP
    
    echo "<div id='div_konten'>";
        
        if ($ppilihrpt=="excel") {
        }else{
            
            echo "<div hidden id='n_form'>";

                echo "<form method='POST' action='aksi_cari_databudgetexp.php' id='d-form3' name='form3' data-parsley-validate class='form-horizontal form-label-left'>";
                    
                    echo "<input type='text' id='e_tahun' name='e_tahun' value='$ptahun' Readonly>";
                    echo "<input type='text' id='cb_dept' name='cb_dept' value='$piddep' Readonly>";
                    echo "<input type='text' id='cb_nmdept' name='cb_nmdept' value='$pnamadep' Readonly>";
                    echo "<input type='text' id='cb_karyawansm' name='cb_karyawansm' value='$pidkrysm' Readonly>";
                    echo "<input type='text' id='cb_liniproduk' name='cb_liniproduk' value='$pliniproduk' Readonly>";
                    echo "<input type='text' id='cb_pengajuan' name='cb_pengajuan' value='$pidpengajuan' Readonly>";// ETH, OTC, HO
                    echo "<input type='text' id='cb_region' name='cb_region' value='$pregion' Readonly>";
                    echo "<textarea id='txt_namacabang' name='txt_namacabang'>$filternamacabang</textarea>";// ETH, OTC
                    echo "<textarea id='txt_cabangiddivisi' name='txt_cabangiddivisi'>$pcabangdivisi2</textarea>";// ETH, OTC
                    echo "<textarea id='txt_cabangid' name='txt_cabangid'>$filtercabang2</textarea>";// ETH, OTC
                    echo "<textarea id='txt_divisicb' name='txt_divisicb'>$filterdivisi2</textarea>";// ETH, OTC
                    echo "<textarea id='txt_coa' name='txt_coa'>$filter_coa2</textarea>";// ETH, OTC
                    echo "<input type='text' id='txt_pilsales' name='txt_pilsales' value='$ppilihsales' Readonly>";
                    echo "<input type='text' id='txt_pilsalesgsm' name='txt_pilsalesgsm' value='$ppilihsales_gsm' Readonly>";
                    echo "<input type='text' id='txt_pilsalessm' name='txt_pilsalessm' value='$ppilihsales_sm' Readonly>";
                    echo "<input type='text' id='txt_pilmkt' name='txt_pilmkt' value='$ppilihmarketing' Readonly>";

                echo "</form>";

            echo "</div>";
            
        }
    
        
        echo "<b>Report Expense VS Budget</b><br/>";
        echo "<b>Tahun : $ptahun</b><br/>";
        
        if (!empty($pnamadep)) {
            echo "<b>Departemen : $pnamadep</b><br/>";
        }else{
            echo "<b>Departemen : All</b><br/>";
        }
        
        if ($ppilihsales == true OR $ppilihmarketing == true) {
            
            if (!empty($filternamacabang)) {
                echo "<small>$filternamacabang</small><br/>";
            }else{
                if (!empty($pnamapengajuan)) {
                    echo "<b>Divisi : $pnamapengajuan</b><br/>";
                }
            }
        }elseif ($ppilihsales_gsm == true) {
            if (!empty($pregion)) {
                echo "<b>Region : $pnamaregion</b><br/>";
            }
        }
        
        if (!empty($pilih_allexp)) {
            echo "<br/><small>$pilih_allexp</small>";
        }
        
        $printdate= date("d/m/Y H:i");
        echo "<br/><i><small>view date : $printdate</small></i><br/>";

        echo "<hr/><br/>";


        echo "<table id='tbltable' border='1' class='table customerTable' cellspacing='0' cellpadding='1'>";
            echo "<thead>";
                echo "<tr>";
                
                    $pchkalljml="";
                    if ($ppilihrpt=="excel") {
                    }else{
                        $pchkalljml="<input type='checkbox' id='chkbtnjml' value='deselect' onClick=\"SelAllCheckBox('chkbtnjml', 'chkbox_jumlah[]')\" checked/>";
                    }
                    
                    echo "<th align='center' rowspan='2'><small>No</small></th>";
                    echo "<th align='center' rowspan='2'><small>$pchkalljml</small></th>";
                    echo "<th align='center' rowspan='2'><small>&nbsp;</small></th>";
                    echo "<th align='center' rowspan='2'><small>COA</small></th>";
                    
                    echo "<th align='center' colspan='3'><small>Total</small></th>";
                    
                    echo "<th align='center' colspan='3'><small>Januari</small></th>";
                    echo "<th align='center' colspan='3'><small>Februari</small></th>";
                    echo "<th align='center' colspan='3'><small>Maret</small></th>";
                    echo "<th align='center' colspan='3'><small>April</small></th>";
                    echo "<th align='center' colspan='3'><small>Mei</small></th>";
                    echo "<th align='center' colspan='3'><small>Juni</small></th>";
                    echo "<th align='center' colspan='3'><small>Juli</small></th>";
                    echo "<th align='center' colspan='3'><small>Agustus</small></th>";
                    echo "<th align='center' colspan='3'><small>September</small></th>";
                    echo "<th align='center' colspan='3'><small>Oktober</small></th>";
                    echo "<th align='center' colspan='3'><small>November</small></th>";
                    echo "<th align='center' colspan='3'><small>Desember</small></th>";

                echo "</tr>";

                echo "<tr>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                    echo "<th align='center' class='th2'><small>Budget</small></th>";
                    echo "<th align='center' class='th2'><small>Expenses</small></th>";
                    echo "<th align='center' class='th2'><small>%</small></th>";

                echo "</tr>";

            echo "</thead>";

            echo "<tbody>";


                for ($ix=1;$ix<=12;$ix++) {
                    $nb_subtotal[$ix]=0;
                    $ne_subtotal[$ix]=0;

                    $nb_grndtotal[$ix]=0;
                    $ne_grndtotal[$ix]=0;
                }


                $no=1;
                $query = "select * from $tmp02 ORDER BY coa4, nama_coa";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $ncoa=$row['coa4'];
                    $nnamacoa=$row['nama_coa'];

                    $ptomboljenis="";
                    $pchkjumlah="";
                    if ($ppilihrpt=="excel") {
                    }else{
                        $ptomboljenis = "<button type='button' id='btn_jenis' name='btn_jenis' class='btn btn-dark btn-xs' onclick=\"LihatDataJenis('$ncoa')\"><i class=\"fa fa-archive\"></i> Jenis</button>";
                        $pchkjumlah= "<input onClick=\"HitungJumlahDataTabel()\" type='checkbox' value='$ncoa' name='chkbox_jumlah[]' id='chkbox_jumlah[]' checked>";
                    }
                    

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pchkjumlah</td>";
                    echo "<td nowrap>$ptomboljenis</td>";
                    echo "<td nowrap>$ncoa - $nnamacoa</td>";

                    $pb_totalsub=0;
                    $pe_totalsub=0;

                    for ($ix=1;$ix<=12;$ix++) {
                        $b_field="b".$ix;
                        $e_field="e".$ix;

                        $nbudgetrp=$row[$b_field];
                        $nexpensesrp=$row[$e_field];

                        if (empty($nbudgetrp)) $nbudgetrp=0;
                        if (empty($nexpensesrp)) $nexpensesrp=0;


                        $nb_subtotal[$ix]=(DOUBLE)$nb_subtotal[$ix]+(DOUBLE)$nbudgetrp;
                        $ne_subtotal[$ix]=(DOUBLE)$ne_subtotal[$ix]+(DOUBLE)$nexpensesrp;

                        $nb_grndtotal[$ix]=(DOUBLE)$nb_grndtotal[$ix]+(DOUBLE)$nbudgetrp;
                        $ne_grndtotal[$ix]=(DOUBLE)$ne_grndtotal[$ix]+(DOUBLE)$nexpensesrp;

                        $pb_totalsub=(DOUBLE)$pb_totalsub+(DOUBLE)$nbudgetrp;
                        $pe_totalsub=(DOUBLE)$pe_totalsub+(DOUBLE)$nexpensesrp;

                    }

                    if (empty($pb_totalsub)) $pb_totalsub=0;
                    if (empty($pe_totalsub)) $pe_totalsub=0;

                    $nach=0;
                    if ((DOUBLE)$pb_totalsub<>0) {
                        $nach=(DOUBLE)$pe_totalsub/(DOUBLE)$pb_totalsub*100;
                    }

                    if (empty($nach)) $nach=0;

                    $pb_totalsub=BuatFormatNumberRp($pb_totalsub, $ppilformat);//1 OR 2 OR 3
                    $pe_totalsub=BuatFormatNumberRp($pe_totalsub, $ppilformat);//1 OR 2 OR 3

                    $nach=ROUND($nach,2);

                    echo "<td nowrap align='right' style='font-weight:bold;'>$pb_totalsub</td>";
                    echo "<td nowrap align='right' style='font-weight:bold;'>$pe_totalsub</td>";
                    echo "<td nowrap align='right' style='font-weight:bold;'>$nach</td>";

                    
                    
                    for ($ix=1;$ix<=12;$ix++) {
                        $b_field="b".$ix;
                        $e_field="e".$ix;

                        $nbudgetrp=$row[$b_field];
                        $nexpensesrp=$row[$e_field];

                        if (empty($nbudgetrp)) $nbudgetrp=0;
                        if (empty($nexpensesrp)) $nexpensesrp=0;

                        $nach=0;
                        if ((DOUBLE)$nbudgetrp<>0) {
                            $nach=(DOUBLE)$nexpensesrp/(DOUBLE)$nbudgetrp*100;
                        }

                        if (empty($nach)) $nach=0;

                        $nbudgetrp=BuatFormatNumberRp($nbudgetrp, $ppilformat);//1 OR 2 OR 3
                        $nexpensesrp=BuatFormatNumberRp($nexpensesrp, $ppilformat);//1 OR 2 OR 3

                        $nach=ROUND($nach,2);

                        echo "<td nowrap align='right'>$nbudgetrp</td>";
                        echo "<td nowrap align='right'>$nexpensesrp</td>";
                        echo "<td nowrap align='right' style='font-weight:bold;'>$nach</td>";

                    }
                    
                    echo "</tr>";

                    $no++;

                }


                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap></td>";
                echo "<td nowrap>Grand Total </td>";

                $pb_totalsub=0;
                $pe_totalsub=0;

                for ($ix=1;$ix<=12;$ix++) {
                    $nbudgetrp=$nb_subtotal[$ix];
                    $nexpensesrp=$ne_subtotal[$ix];

                    if (empty($nbudgetrp)) $nbudgetrp=0;
                    if (empty($nexpensesrp)) $nexpensesrp=0;

                    $pb_totalsub=(DOUBLE)$pb_totalsub+(DOUBLE)$nbudgetrp;
                    $pe_totalsub=(DOUBLE)$pe_totalsub+(DOUBLE)$nexpensesrp;


                }


                if (empty($pb_totalsub)) $pb_totalsub=0;
                if (empty($pe_totalsub)) $pe_totalsub=0;

                $nach=0;
                if ((DOUBLE)$pb_totalsub<>0) {
                    $nach=(DOUBLE)$pe_totalsub/(DOUBLE)$pb_totalsub*100;
                }

                if (empty($nach)) $nach=0;

                $pb_totalsub=BuatFormatNumberRp($pb_totalsub, $ppilformat);//1 OR 2 OR 3
                $pe_totalsub=BuatFormatNumberRp($pe_totalsub, $ppilformat);//1 OR 2 OR 3

                $nach=ROUND($nach,2);

                echo "<td nowrap align='right' style='font-weight:bold;'>$pb_totalsub</td>";
                echo "<td nowrap align='right' style='font-weight:bold;'>$pe_totalsub</td>";
                echo "<td nowrap align='right' style='font-weight:bold;'>$nach</td>";

                $pb_totalsub=0;
                $pe_totalsub=0;
                for ($ix=1;$ix<=12;$ix++) {
                    $nbudgetrp=$nb_subtotal[$ix];
                    $nexpensesrp=$ne_subtotal[$ix];

                    if (empty($nbudgetrp)) $nbudgetrp=0;
                    if (empty($nexpensesrp)) $nexpensesrp=0;

                    $nach=0;
                    if ((DOUBLE)$nbudgetrp<>0) {
                        $nach=(DOUBLE)$nexpensesrp/(DOUBLE)$nbudgetrp*100;
                    }

                    if (empty($nach)) $nach=0;

                    $nbudgetrp=BuatFormatNumberRp($nbudgetrp, $ppilformat);//1 OR 2 OR 3
                    $nexpensesrp=BuatFormatNumberRp($nexpensesrp, $ppilformat);//1 OR 2 OR 3

                    $nach=ROUND($nach,2);

                    echo "<td nowrap align='right'>$nbudgetrp</td>";
                    echo "<td nowrap align='right'>$nexpensesrp</td>";
                    echo "<td nowrap align='right'>$nach</td>";

                }
                
                
                echo "</tr>";



            echo "</tbody>";


        echo "</table>";


        echo "<br/>&nbsp;<br/>&nbsp;";
        
        // DETAIL JENIS
        echo "<div id='div-jenis'>";
        
            
        
        echo "</div>";
        // END DETAIL JENIS
        
        echo "<br/>&nbsp;<br/>&nbsp;";
        
        // DETAIL
        echo "<div id='div-detail'>";
        
            
        
        echo "</div>";
        // END DETAIL
        
        
        
        echo "<br/>&nbsp;<br/>&nbsp;";
        echo "<br/>&nbsp;<br/>&nbsp;";
    
    
    echo "</div>";
    ?>
    
    
</BODY>


<?PHP
if ($ppilihrpt=="excel") {
}else{
?>
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
    
    <style>
        #btn_jenis {
            border: 1px solid #4CAF50;
            border-radius: 6px;
            background-color: white;
        }
        #btn_jenis:hover {
            cursor:pointer;
            background-color: #cccccc;
        }
        #btn_jenis:focus {
            border: 1px solid #cc0000;
            background-color: #fff;
        }
        #div_konten{
            
        }
    </style>
    
    <script>
        function LihatDataJenis(icoa) {
            $("#div-jenis").html("");
            $("#div-detail").html("");
            
            var idep = document.getElementById('cb_dept').value;
            var itahun = document.getElementById('e_tahun').value;
            var ipengajuan = document.getElementById('cb_pengajuan').value;//divisi
            var iregion = document.getElementById('cb_region').value;
            var ikrysm = document.getElementById('cb_karyawansm').value;
            var ilproduk = document.getElementById('cb_liniproduk').value;
            var icabangiddivid = document.getElementById('txt_cabangiddivisi').value;
            var icabangid = document.getElementById('txt_cabangid').value;
            var idivcb = document.getElementById('txt_divisicb').value;
            var iall_coa = document.getElementById('txt_coa').value;
            var ipilsls = document.getElementById('txt_pilsales').value;
            var ipilslsgsm = document.getElementById('txt_pilsalesgsm').value;
            var ipilslssm = document.getElementById('txt_pilsalessm').value;
            var ipilmkt = document.getElementById('txt_pilmkt').value;
            var inmdep = document.getElementById('cb_nmdept').value;
            var inmcabang = document.getElementById('txt_namacabang').value;
            
            
            $.ajax({
                type:"post",
                url:"module/laporan_gl/mod_gl_expenvsbudget/aksi_viewdataexpbgt.php?module=viewdatajenis",
                data:"ucoa="+icoa+"&udep="+idep+"&utahun="+itahun+"&upengajuan="+ipengajuan+"&uregion="+iregion+"&ucabdivisi="+iall_coa+"&ukrysm="+ikrysm+
                        "&ucabangiddivid="+icabangiddivid+"&ucabangid="+icabangid+"&udivcb="+idivcb+"&uall_coa="+iall_coa+
                        "&upilsls="+ipilsls+"&upilslsgsm="+ipilslsgsm+"&upilslssm="+ipilslssm+"&upilmkt="+ipilmkt+
                        "&unmdep="+inmdep+"&unmcabang="+inmcabang+"&ulproduk="+ilproduk,
                success:function(data){
                    $("#div-jenis").html(data);
                    
                    window.scrollTo(0,document.body.scrollHeight);
                    //window.scrollTo(0,document.querySelector("#div-jenis").scrollHeight);
                }
            });
        
        }
        
        
        function SelAllCheckBox(nmbuton, data){
            var checkboxes = document.getElementsByName(data);
            var button = document.getElementById(nmbuton);

            if(button.value == 'select'){
                for (var i in checkboxes){
                    checkboxes[i].checked = 'FALSE';
                }
                button.value = 'deselect'
            }else{
                for (var i in checkboxes){
                    checkboxes[i].checked = '';
                }
                button.value = 'select';
            }

            HitungJumlahDataTabel();

        }
        
        function HitungJumlahDataTabel() {
            
            var chk_arr =  document.getElementsByName('chkbox_jumlah[]');
            var newchar = '';
            var itotal1="0", itotal2="0";
            var table = document.getElementById("tbltable");
            var trows=table.rows.length;
            var tawal="2";
            var ichk="0";
            var icol_pl1="4", icol_pl2="5", icol_pl3="6";
            var icol_1="0", icol_2="0";
            
            var icol1="4";
            for (var i=0; i<=13; i++) {
                icol_1=parseInt(icol1)-i-2;
                icol_2=parseInt(icol1)-i-1;
                
                icol_pl1=parseInt(icol1)+i;
                icol_pl2=parseInt(icol1)+i+1;
                icol_pl3=parseInt(icol1)+i+2;
                itotal1="0"; itotal2="0";
                for(var r = tawal; r < trows - 1; r++)
                {
                    var ijml1=table.rows[r].cells[icol_pl1].innerHTML;
                    if (ijml1=="") ijml1="0";
                    ijml1 = ijml1.split(',').join(newchar);
                    
                    var ijml2=table.rows[r].cells[icol_pl2].innerHTML;
                    if (ijml2=="") ijml2="0";
                    ijml2 = ijml2.split(',').join(newchar);
                    
                    ichk=parseInt(r)-parseInt(tawal);
                    if (chk_arr[ichk].checked == true) {
                        itotal1=parseFloat(itotal1)+parseFloat(ijml1);
                        itotal2=parseFloat(itotal2)+parseFloat(ijml2);
                        
                        table.rows[r].cells[icol_1].bgColor = "#FFF";
                        table.rows[r].cells[icol_2].bgColor = "#FFF";
                        
                        table.rows[r].cells[icol_pl1].bgColor = "#FFF";
                        table.rows[r].cells[icol_pl2].bgColor = "#FFF";
                        table.rows[r].cells[icol_pl3].bgColor = "#FFF";
                    }else{
                        table.rows[r].cells[icol_1].bgColor = "#808080";
                        table.rows[r].cells[icol_2].bgColor = "#808080";
                        
                        table.rows[r].cells[icol_pl1].bgColor = "#808080";
                        table.rows[r].cells[icol_pl2].bgColor = "#808080";
                        table.rows[r].cells[icol_pl3].bgColor = "#808080";
                    }
                }
                
                var iach="0";
                if (parseFloat(itotal1)>0) {
                    iach=(parseFloat(itotal2)/parseFloat(itotal1)*100).toFixed(2);
                }
                
                table.rows[trows-1].cells[icol_pl1].innerHTML=itotal1.toLocaleString();
                table.rows[trows-1].cells[icol_pl2].innerHTML=itotal2.toLocaleString();
                table.rows[trows-1].cells[icol_pl3].innerHTML=iach;
                
                
                icol1=parseInt(icol1)+2;
            }
            
            
        }

    </script>
    
<?PHP
}
?>
    
    <style>
        #tbltable {
            border-collapse: collapse;
        }
        
        th {
            background-color: #ccccff;
            font-size : 16px;
            padding:5px;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }
        .th2 {
            background-color: #ccccff;
            position: sticky;
            top: 23;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            border-top: 0px solid #000;
        }
        tr td {
            font-size : 12px;
        }
        tr td {
            padding : 3px;
        }
        tr:hover {background-color:#f5f5f5;}
        thead tr:hover {background-color:#cccccc;}
    </style>
    
</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp04");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp05");
    mysqli_query($cnmy, "drop TEMPORARY table if EXISTS $tmp06");
    mysqli_close($cnmy);
?>