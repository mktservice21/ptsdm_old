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
    
    
// Hapus 
if ($module=='discdplchc' AND $act=='hapus')
{
    $kodenya=$_GET['id'];
    
    $query =  "DELETE from dbmaster.t_dpl WHERE nourut='$kodenya' LIMIT 1";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    mysqli_close($cnmy);

    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');

    exit;   
}
elseif ($module=='discdplchc' AND $act=='update' )
{
    $puserid=(INT)$_POST['e_userinput'];
    $pcardid=$_POST['e_userinput'];
    
    if (empty($puserid)) {
        $puserid="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

        if (empty($puserid)) {
            mysqli_close($cnmy);
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
    }
    
    
    $kodenya=$_POST['e_id'];
    $pgrpinputid = $_POST['e_grpid'];
    
    $pdivisi="OTC";
    $pperiode=$_POST['e_tahun'];
    $pidsem=$_POST['cb_semester'];
    $pnodpl=$_POST['e_nodpl'];
    $pket=$_POST['e_notes'];
    $pgroupid=$_POST['e_grpid'];
    
    if (!empty($pnodpl)) $pnodpl = str_replace("'", ' ', $pnodpl);
    if (!empty($pket)) $pket = str_replace("'", ' ', $pket);
    if (!empty($pket)) $pket = str_replace('"', ' ', $pket);
    
    if ($pgroupid=="0") $pgroupid="";
    
    
    $pbelimin=$_POST['e_belimin'];
    $pbelimax=$_POST['e_belimax'];
    $pdisc=$_POST['e_discount'];

    if (empty($pbelimin)) $pbelimin=0;
    if (empty($pbelimax)) $pbelimax=0;
    if (empty($pdisc)) $pdisc=0;

    $pbelimin=str_replace(",","", $pbelimin);
    $pbelimax=str_replace(",","", $pbelimax);
    $pdisc=str_replace(",","", $pdisc);
    
    
    if (empty($pgroupid)) {

        $query = "select MAX(igroup) as urutan FROM dbdiscount.t_dpl";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            $nrow= mysqli_fetch_array($tampil);
            if (isset($nrow['urutan'])) $pgroupid=$nrow['urutan'];
            $pgroupid++;
        }else{
            $pgroupid=1;
        }

    }
    
    if (!empty($kodenya)) {
       $query = "UPDATE dbdiscount.t_dpl SET nodpl='$pnodpl', beli_min='$pbelimin', beli_max='$pbelimax', discount='$pdisc', "
               . " keterangan='$pket', igroup='$pgroupid', userid='$pcardid' WHERE nourut='$kodenya' LIMIT 1";
       mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan." UPDATE "; mysqli_close($cnmy); exit; }
       
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
    }
    
    
    
}
elseif ($module=='discdplchc' AND $act=='input' )
{
    
    $puserid=(INT)$_POST['e_userinput'];
    $pcardid=$_POST['e_userinput'];
    
    if (empty($puserid)) {
        $puserid="";
        if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

        if (empty($puserid)) {
            mysqli_close($cnmy);
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
    }
    
    
    $kodenya=$_POST['e_id'];
    $pgrpinputid = $_POST['e_grpid'];
    
    $pdivisi="OTC";
    $pperiode=$_POST['e_tahun'];
    $pidsem=$_POST['cb_semester'];
    $pnodpl=$_POST['e_nodpl'];
    $pket=$_POST['e_notes'];
    $pgroupid=$_POST['e_grpid'];
    
    if (!empty($pnodpl)) $pnodpl = str_replace("'", ' ', $pnodpl);
    if (!empty($pket)) $pket = str_replace("'", ' ', $pket);
    if (!empty($pket)) $pket = str_replace('"', ' ', $pket);
    
    if ($pgroupid=="0") $pgroupid="";
    
    
    if ($act=="input" OR empty($pgroupid)) {

        $query = "select MAX(igroup) as urutan FROM dbdiscount.t_dpl";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            $nrow= mysqli_fetch_array($tampil);
            if (isset($nrow['urutan'])) $pgroupid=$nrow['urutan'];
            $pgroupid++;
        }else{
            $pgroupid=1;
        }

    }
    $pbolehsimpan=false;
    unset($pinsert_data_detail);//kosongkan array
    foreach ($_POST['chk_kodeid'] as $no_brid) {
        $pbelimin=$_POST['e_txtbelimin'][$no_brid];
        $pbelimax=$_POST['e_txtbelimax'][$no_brid];
        $pdisc=$_POST['e_txtdisc'][$no_brid];
        
        if (empty($pbelimin)) $pbelimin=0;
        if (empty($pbelimax)) $pbelimax=0;
        if (empty($pdisc)) $pdisc=0;
        
        $pbelimin=str_replace(",","", $pbelimin);
        $pbelimax=str_replace(",","", $pbelimax);
        $pdisc=str_replace(",","", $pdisc);
        
        if ((DOUBLE)$pbelimin==0 AND (DOUBLE)$pbelimax==0 AND (DOUBLE)$pdisc==0) {
        }else{
            $pinsert_data_detail[] = "('$pdivisi', '$pperiode', '$pidsem', '$pnodpl', '$no_brid', '$pbelimin', '$pbelimax', '$pdisc', '$pket', '$pgroupid', '$pcardid')";
            $pbolehsimpan=true;
            //echo "$no_brid, min : $pbelimin, max : $pbelimax, disc : $pdisc<br/>";
        
        }
    }
    
    if ($act=="input") {
    }else{
        $query =  "DELETE from dbdiscount.t_dpl WHERE IFNULL(igroup,'')='$pgroupid' AND IFNULL(igroup,'') NOT IN ('0', '')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    if ($pbolehsimpan == true) {
        $query_detail="INSERT INTO dbdiscount.t_dpl (divisi,tahun,semester,nodpl,iprodid,beli_min,beli_max,discount,keterangan,igroup,userid) VALUES ".implode(', ', $pinsert_data_detail);
        mysqli_query($cnmy, $query_detail); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan." INSERT "; mysqli_close($cnmy); exit; }
    }
    
    
    //echo "$puserid, $pcardid";
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
    
    
    
    
}
?>