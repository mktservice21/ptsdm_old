<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
    
$puserid="";
$pidcard="";
$pidgroup="";
$pidsesion="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];
if (isset($_SESSION['IDSESI'])) $pidsesion=$_SESSION['IDSESI'];


if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

if ($module=='pindacabareacust' AND $act=="prosespindah")
{
    include "../../config/koneksimysqli_ms.php";
    include "../../config/fungsi_sql.php";
    $cnit=$cnms;
    
    $pidcab=$_POST['txt_idcab_view'];
    $pidarea=$_POST['txt_idarea_view'];
    
    $pidoldcab=$_POST['txt_idcab_view_old'];
    $pidoldarea=$_POST['txt_idarea_view_old'];
    
    
    $_SESSION['PNDCSTNWIDCAB']=$pidcab;
    $_SESSION['PNDCSTNWIDARA']=$pidarea;
    $_SESSION['PNDCSTOLIDCAB']=$pidoldcab;
    $_SESSION['PNDCSTOLIDARA']=$pidoldarea;
    
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmppndcustcbrprs00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmppndcustcbrprs01_".$puserid."_$now ";
    $tmp02 =" dbmaster.bcpecust_".$now;
    
    $query = "select * from MKT.ecust";
    $query = "create  table $tmp02 ($query)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
    
    //echo "icab new : $pidcab, area new : $pidarea<br/>";
    //echo "icab old : $pidoldcab, old new : $pidoldarea<br/>";
    
    $query = "select nourut, icabangid_new, icustid_new, areaid_new, "
            . " nama, alamat1, alamat2, kodepos, contact, "
            . " telp, fax, ikotaid, kota, isektorid, aktif, dispen, user1,oldflag, scode, grp, grp_spp, "
            . " o_icabangid, o_areaid, o_icustid, pertgl, batch_id, icabangid_hist, iareaid_hist, icustid_hist, "
            . " istatus, idisc, sys_now from dbmaster.tmp_pindah_cust WHERE icabangid='$pidoldcab' and areaid='$pidoldarea' "
            . " AND icabangid_new='$pidcab' and areaid_new='$pidarea' AND IFNULL(selesai,'')<>'Y' AND idsesi='$pidsesion' AND userid='$pidcard'";
    $query = "create  table $tmp00 ($query)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
    
    //e cabang e custid
    $query = "select a.nourut, a.distid as distid, a.cabangid as cabangid, a.ecustid as ecustid, "
            . " a.icabangid as icabangid, a.areaid as areaid, a.icustid as icustid, "
            . " a.icabangid_new, a.areaid_new, a.icustid_new, "
            . " a.nama as nama "
            . " from dbmaster.tmp_pindah_ecust as a "
            . " WHERE a.icabangid='$pidoldcab' and a.areaid='$pidoldarea' "
            . " AND a.icabangid_new='$pidcab' and a.areaid_new='$pidarea' AND IFNULL(a.selesai,'')<>'Y' AND idsesi='$pidsesion' AND userid='$pidcard'";
    $query = "create table $tmp01 ($query)";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
    
    $query = "insert into MKT.icust(icabangid, icustid, areaid, nama, alamat1, alamat2, kodepos, contact, telp, fax, ikotaid,
        kota, isektorid, aktif, dispen, user1, oldflag, sys_now, scode, grp, grp_spp,
	o_icabangid, o_areaid, o_icustid, pertgl, batch_id, icabangid_hist, iareaid_hist, icustid_hist)
	select icabangid_new, icustid_new, areaid_new, nama, alamat1, alamat2, 
        kodepos, contact, telp, fax, ikotaid, kota, isektorid, aktif, dispen, '$puserid' as user1, oldflag, now(), scode, grp, grp_spp,
	o_icabangid, o_areaid, o_icustid, pertgl, batch_id, icabangid_hist, iareaid_hist, icustid_hist
	from $tmp00";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
    $query = "UPDATE dbmaster.tmp_pindah_cust SET selesai='Y' WHERE icabangid='$pidoldcab' and areaid='$pidoldarea' "
            . " AND icabangid_new='$pidcab' and areaid_new='$pidarea' AND IFNULL(selesai,'')<>'Y' AND idsesi='$pidsesion' AND userid='$pidcard'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
    
    
    $query = "UPDATE MKT.ecust as a JOIN $tmp01 as b on a.icabangid=b.icabangid AND a.areaid=b.areaid AND a.icustid=b.icustid "
            . " and a.ecustid=b.ecustid and a.CabangId=b.cabangid and a.distid=b.distid JOIN "
            . " dbmaster.tmp_pindah_ecust as c on b.nourut=c.nourut and b.icabangid_new=c.icabangid_new and "
            . " b.areaid_new=c.areaid_new and b.icustid_new=c.icustid_new and "
            . " b.eCustId=c.ecustid and b.CabangId=c.cabangid and b.distid=c.distid SET "
            . " a.icabangid=b.icabangid_new, a.areaid=b.areaid_new, a.icustid=LPAD(ifnull(b.icustid_new,0), 10, '0'), c.selesai='Y', "
            . " a.user1='$puserid', a.daricab='$pidoldcab', a.kecab='$pidcab' WHERE "
            . " a.icabangid='$pidoldcab' AND a.areaid='$pidoldarea'";
    mysqli_query($cnit, $query); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnit); exit; }
    
    mysqli_query($cnit, "DROP TABLE $tmp00");
    mysqli_query($cnit, "DROP TABLE $tmp01");
    mysqli_close($cnit);
    
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=duplikaticust');
    
}

?>