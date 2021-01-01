<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    $puserid=$_SESSION['IDCARD'];
    
    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN...!!!";
        exit;
    }
    
if ($module=="fincekprosesbrcab" AND $act=="input") {
    
    $pgambar=$_POST['txtgambar'];
    $f_nobrinput="";
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        $pnobrinput=TRIM($_POST['txtbrid'][$nobrinput]);
        $pnoid=TRIM($_POST['txtnoid'][$nobrinput]);
        $pjmlrp=TRIM($_POST['txtjmlrp'][$nobrinput]);
        $pjamberangkat1=TRIM($_POST['txtjamberangkat1'][$nobrinput]);
        $pjamberangkat2=TRIM($_POST['txtjamberangkat2'][$nobrinput]);
        
        $pidagency=TRIM($_POST['cbagency'][$nobrinput]);
        
        $p_updatejamed="";
        if (isset($_POST['txtjamed'][$nobrinput])) {
            $pjamed=$_POST['txtjamed'][$nobrinput];
            if ($pjamed=="0" OR $pjamed=="") $pjamed="24";
            
            $p_updatejamed= " tglex=NOW(), jamex=NULL, jml_expired=NULL ";
            
            if (!empty($pjamed)) {
                $pmenited=(double)$pjamed*60;
                $p_updatejamed= " tglex=NOW()+INTERVAL $pmenited MINUTE, jamex=TIME_FORMAT(NOW()+INTERVAL $pmenited MINUTE,'%H:%i'), jml_expired='$pjamed' ";
            }
            
        }
        
        $pjmlrp=str_replace(",","", $pjmlrp);
        if (empty($pjmlrp)) $pjmlrp=0;
        
        if (strpos($f_nobrinput,$pnobrinput)==0) $f_nobrinput .="'".$pnobrinput."',";
        //echo "$nobrinput : $pnobrinput - $pnoid Rp. $pjmlrp<br/>";
        
        if (!empty($f_nobrinput)) {
            
            if (!empty($p_updatejamed)) {
                $query = "UPDATE dbmaster.t_br_cab SET $p_updatejamed WHERE bridinputcab='$pnobrinput'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update Jml ED ID : $pnobrinput"; exit; }
            }
            
            $query = "UPDATE dbmaster.t_br_cab1 SET id_agency='$pidagency', rp='$pjmlrp' WHERE bridinputcab='$pnobrinput' AND noid='$pnoid'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update ID : $nobrinput"; exit; }
            
            if ($pnoid=="01" OR $pnoid=="02") {
                $query = "UPDATE dbmaster.t_br_cab1 SET jam1='$pjamberangkat1', jam2='$pjamberangkat1' WHERE bridinputcab='$pnobrinput' AND noid='$pnoid'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update ID : $nobrinput"; exit; }
            }elseif ($pnoid=="04") {
                $query = "UPDATE dbmaster.t_br_cab1 SET jam1='$pjamberangkat1', jam2='$pjamberangkat2' WHERE bridinputcab='$pnobrinput' AND noid='$pnoid'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update ID : $nobrinput"; exit; }
            }
            
        }
        
    }
    
    if (!empty($f_nobrinput)) {
        $f_nobrinput="(".substr($f_nobrinput, 0, -1).")";
        
        //expired
        
        $query = "UPDATE dbmaster.t_br_cab SET "
                . " gbr_atasan4=NULL, tgl_atasan4=NULL "
                . " WHERE bridinputcab IN $f_nobrinput AND IFNULL(tglbooking,'')<>'' AND IFNULL(gbr_atasan4,'')<>'' AND IFNULL(tglissued,'')='' ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update atasan4 ID : $f_nobrinput"; exit; }
        
        $query = "UPDATE dbmaster.t_br_cab SET "
                . " gbr_atasan3=NULL, tgl_atasan3=NULL "
                . " WHERE bridinputcab IN $f_nobrinput AND IFNULL(tglbooking,'')<>'' AND IFNULL(gbr_atasan3,'')<>'' AND IFNULL(tglissued,'')='' AND "
                . " jabatanId IN ('15', '10', '18', '08') ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update atasan3 ID : $f_nobrinput"; exit; }
        
        $query = "UPDATE dbmaster.t_br_cab SET "
                . " gbr_atasan2=NULL, tgl_atasan2=NULL "
                . " WHERE bridinputcab IN $f_nobrinput AND IFNULL(tglbooking,'')<>'' AND IFNULL(gbr_atasan2,'')<>'' AND IFNULL(tglissued,'')='' AND "
                . " jabatanId IN ('15', '10', '18') ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update atasan2 ID : $f_nobrinput"; exit; }
        
        $query = "UPDATE dbmaster.t_br_cab SET "
                . " gbr_atasan1=NULL, tgl_atasan1=NULL "
                . " WHERE bridinputcab IN $f_nobrinput AND IFNULL(tglbooking,'')<>'' AND IFNULL(gbr_atasan1,'')<>'' AND IFNULL(tglissued,'')='' AND "
                . " jabatanId IN ('15') ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update atasan1 ID : $f_nobrinput"; exit; }
        
        $query = "UPDATE dbmaster.t_br_cab SET "
                . " validate_gbr=NULL, validate_date=NULL, validate=NULL "
                . " WHERE bridinputcab IN $f_nobrinput AND IFNULL(tglbooking,'')<>'' AND IFNULL(tglissued,'')='' AND IFNULL(validate_date,'')<>'' ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update Validate ID : $f_nobrinput"; exit; }
        
        
        //end expired
        
        
        $query = "UPDATE dbmaster.t_br_cab a JOIN (SELECT bridinputcab, IFNULL(sum(rp),0) as rp FROM dbmaster.t_br_cab1 WHERE "
                . " bridinputcab IN $f_nobrinput GROUP BY 1) b ON a.bridinputcab=b.bridinputcab SET "
                . " a.jumlah=b.rp, a.userbooking='$puserid', a.tglbooking=NOW(), a.gbrbooking='$pgambar', "
                . " a.jambooking=TIME_FORMAT(CURRENT_TIME,'%H:%i') WHERE "
                . " a.bridinputcab IN $f_nobrinput";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update BOOKING ID : $f_nobrinput"; exit; }
        
        
    }
    
    //echo "$f_nobrinput";
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
    
    /*
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $pjmlminta=$_POST['txtjmlminta'][$nobrinput];
            $pjmlrp=$_POST['txtjmlrp'][$nobrinput];
            $ptglissu=$_POST['d_tgliss'][$nobrinput];
            
            //$ptgl=$_POST['txttglril'][$nobrinput];
            //$ntglril="";
            //if (!empty($ptgl)) $ntglril= date("Y-m-d", strtotime($ptgl));
            
            $pjmlminta=str_replace(",","", $pjmlminta);
            $pjmlrp=str_replace(",","", $pjmlrp);
    
            if (empty($pjmlminta)) $pjmlminta=0;
            if (empty($pjmlrp)) $pjmlrp=0;

            if ((double)$pjmlrp==0) $pjmlrp=$pjmlminta;
    
            $psaveissue=" tglissued='$ptglissu', ";
            if (empty($ptglissu)) $psaveissue=" tglissued=null, ";
    
            $query = "UPDATE dbmaster.t_br_cab SET jumlah='$pjmlrp', $psaveissue validate='$puserid', validate_date=NOW(), validate_gbr='$pgambar' WHERE bridinputcab='$nobrinput'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update ID : $nobrinput"; exit; }
    
            //echo "$nobrinput, $pjmlminta, $pjmlrp, $ptglissu<br/>";
        }
    }
    
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
     * 
     */
}elseif ($module=="fincekprosesbrcab" AND $act=="unapprove") {
    
    $f_nobr="";
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $pnobrinput_p=TRIM($_POST['txtbrid'][$nobrinput]);
            
            if (strpos($f_nobr,$pnobrinput_p)==0) $f_nobr .="'".$pnobrinput_p."',";
            //echo "$pnobrinput_p<br/>";
        }
    }
    //echo "$f_nobr"; exit;
    
    if (!empty($f_nobr)) {
        $f_nobr="(".substr($f_nobr, 0, -1).")";
        
        $query = "UPDATE dbmaster.t_br_cab SET tglex=NULL, jamex=NULL, jml_expired=NULL, userbooking=NULL, tglbooking=NULL, gbrbooking=NULL WHERE bridinputcab IN $f_nobr";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update ID : $nobrinput"; exit; }
        
    }
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
    
}elseif ($module=="fincekprosesbrcab" AND $act=="reject") {
    $f_nobr="";
    $palasanket=$_GET['ukethapus'];
    
    $pnmreject=$_SESSION['NAMALENGKAP'];
    
    $hari_ini = date("d F Y h:i:s");
    $palasanket="User : ".$pnmreject."  ".$hari_ini.", ".$palasanket;
    
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $f_nobr .="'".$nobrinput."',";
            //echo "$nobrinput<br/>";
        }
    }
    
    if (!empty($f_nobr)) {
        $f_nobr="(".substr($f_nobr, 0, -1).")";
        
        $query = "UPDATE dbmaster.t_br_cab SET stsnonaktif='Y', alasan_batal='$palasanket' WHERE bridinputcab IN $f_nobr";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update ID : $nobrinput"; exit; }
        
    }
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
}
?>

