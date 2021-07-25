<?php
echo "tetap";
exit;
session_start();

$_SESSION['FOLDERGL']="mysdm";
include "$_SESSION[FOLDERGL]/config/koneksimysqli.php";
//include "$_SESSION[FOLDERGL]/config/koneksimysqli_it.php";
include "$_SESSION[FOLDERGL]/config/fungsi_sql.php";

$purlkembali="location:../ptsdm/";

$username="";
$pass       = "";
$pmedia_sessi="";
$pmodule="";
$pidmenu="";

if (isset($_GET['iuser'])) $username   = $_GET['iuser'];
if (isset($_GET['isesi'])) $pmedia_sessi = $_GET['isesi'];
if (isset($_GET['module'])) $pmodule = $_GET['module'];
if (isset($_GET['idmenu'])) $pidmenu = $_GET['idmenu'];

if (empty($username) OR empty($pmedia_sessi)) {
    $pcurul=$_SERVER["HTTP_HOST"];
    //echo "KOSONG.....";
    //echo "A"; exit;
    echo "<script>alert('Silakan login ulang...'); window.location = 'index.php'</script>";
    //header($purlkembali);
    exit;
}

$query = "select * from dbmaster.sdm_users_log WHERE karyawanid=$username AND session_id='$pmedia_sessi' AND IFNULL(AKTIF,'')<>'N'";
$tampil= mysqli_query($cnmy, $query);
$ketemu=mysqli_num_rows($tampil);
if ((INT)$ketemu<=0) {
    $pcurul=$_SERVER["HTTP_HOST"];
    //echo "KOSONG.....";
    //echo "B"; exit;
    echo "<script>alert('Silakan login ulang...'); window.location = 'index.php'</script>";
    //header($purlkembali);
    exit;
}

$psdhlogidcard="";
$psdhlogidgroup="";
if (isset($_SESSION['IDCARD'])) $psdhlogidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['GROUP'])) $psdhlogidgroup=$_SESSION['GROUP'];

$_SESSION['IDSESI_VPS']=$pmedia_sessi;

if (!empty($psdhlogidcard) AND !empty($psdhlogidgroup)){
    
    //echo "sudah login";
    header('location:'.$_SESSION['FOLDERGL'].'/media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act='.$pidmenu.'&kriteria=Y');
    exit;
    
}else{

    
    //cek karyawan khusus (seperti login admin surabaya dan bu kristina)
    //$query = "select a.karyawanid as karyawanid, a.username as username, a.id_group as id_group, b.nama, b.jabatanid as jabatanid from dbmaster.sdm_users as a join dbmaster.t_karyawan_khusus as b on a.karyawanid=b.karyawanid";



    $query = "select karyawanid as karyawanid, jabatanid as jabatanid, nama as nama_karyawan, "
            . " tglmasuk, divisiId as divisiid, atasanId as atasanid, "
            . " icabangid as icabangid, areaid as areaid "
            . " from hrd.karyawan WHERE karyawanid=$username AND IFNULL(aktif,'')<>'N'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu<=0) {
        //echo "Tidak berhak login...";
        //echo "C"; exit;
        echo "<script>alert('Silakan login ulang...'); window.location = 'index.php'</script>";
        //header($purlkembali);
        exit;
    }

    $row=mysqli_fetch_array($tampil);
    $pidkaryawan=$row['karyawanid'];
    $pnmkaryawan=$row['nama_karyawan'];
    $ptglmasuk=$row['tglmasuk'];
    $pdivisiid=$row['divisiid'];
    $patasanid=$row['atasanid'];
    $pjabtanid=$row['jabatanid'];
    $picabangid=$row['icabangid'];
    $pareaid=$row['areaid'];
    $pidgroup="";

    if ($ptglmasuk=="0000-00-00") $ptglmasuk="";
    if (!empty($ptglmasuk)) $ptglmasuk = date("d F Y", strtotime($ptglmasuk));


    $pidspv="";
    $piddm="";
    $pidsm="";
    $pidgsm="";
    $pregion="";
    $pcabid="";

    $querykryposisi = "select karyawanid as karyawanid, spv as spv, dm as dm, sm as sm, gsm as gsm, "
            . " region as region, IDCAB as idcab "
            . " from dbmaster.t_karyawan_posisi WHERE karyawanId=$username";
    $tampilkryposisi = mysqli_query($cnmy, $querykryposisi);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    $ketemukryposisi = mysqli_num_rows($tampilkryposisi);
    if ($ketemukryposisi>0) {
        $rsk= mysqli_fetch_array($tampilkryposisi);

        $pidspv=$rsk['spv'];
        $piddm=$rsk['dm'];
        $pidsm=$rsk['sm'];
        $pidgsm=$rsk['gsm'];

        $pregion=$rsk['region'];
        $pcabid=$rsk['idcab'];

    }

    $rsat = mysqli_fetch_array(mysqli_query($cnmy, "select nama as nama_atasan from hrd.karyawan where karyawanId='$patasanid'"));
    $pnamaatasan=$rsat['nama_atasan'];

    $rsjb = mysqli_fetch_array(mysqli_query($cnmy, "select jabatanId, nama as nama_jabatan, rank as rank from hrd.jabatan WHERE jabatanId='$pjabtanid'"));
    $pnmjabatan=$rsjb['nama_jabatan'];
    $praknk=$rsjb['rank'];

    //mencari id group
    $query = "select id_group as id_group, LEVELPOSISI as levelposisi from dbmaster.jabatan_level WHERE jabatanid='$pjabtanid'";
    $tampil2= mysqli_query($cnmy, $query);
    $row2=mysqli_fetch_array($tampil2);
    $pidgroup1=$row2['id_group'];
    $plvlposisi=trim($row2['levelposisi']);




    //mencari id group dari sdm_user 
    $query = "select karyawanid as karyawanid, id_group as id_group, username as username, "
            . " level as level, akhusus as akhusus from dbmaster.sdm_users WHERE karyawanid=$username";
    $tampil3= mysqli_query($cnmy, $query);
    $row3=mysqli_fetch_array($tampil3);
    $pidgroup2=$row3['id_group'];
    $puserlogin=$row3['username'];
    $plevel=$row3['level'];
    $pakhusus=$row3['akhusus'];

    if ($pidgroup2=="0") $pidgroup2="";
    if (!empty($pidgroup2)) $pidgroup=$pidgroup2;

    if (empty($pakhusus)) $pakhusus="N";

    $rscb = mysqli_fetch_array(mysqli_query($cnmy, "select nama as nama_cabang from MKT.icabang where icabangid='$picabangid'"));
    $pnamacabang=$rscb['nama_cabang'];

    $rsar = mysqli_fetch_array(mysqli_query($cnmy, "select nama as nama_area from MKT.iarea where icabangid='$picabangid' AND areaid='$pareaid'"));
    $pnamaarea=$rsar['nama_area'];


    $_SESSION['NAMAPT'] = "PT. Surya Dermato Medica";
    $_SESSION['EMAIL']=$username;
    $_SESSION['IDCARD']=$pidkaryawan;
    $_SESSION['USERID']=(int)$pidkaryawan;
    $_SESSION['USERNAME']=(int)$pidkaryawan;
    $_SESSION['NAMALENGKAP']=$pnmkaryawan;

    $_SESSION['MEMBERSEJAK']=$ptglmasuk;
    $_SESSION['DIVISI']=$pdivisiid;
    $_SESSION['ATASANID']=$patasanid;
    $_SESSION['NAMAATASAN']=$pnamaatasan;

    $_SESSION['IDCABANG']=$picabangid;
    $_SESSION['NMCABANG']=$pnamacabang;
    $_SESSION['IDAREA']=$pareaid;
    $_SESSION['NMAREA']=$pnamaarea;

    $_SESSION['JABATANID'] = $pjabtanid;
    $_SESSION['JABATANNM']=$pnmjabatan;
    $_SESSION['JABATANRANK']=(int)$praknk;
    $_SESSION['LVLPOSISI']=$plvlposisi;
    $_SESSION['GROUP']=$pidgroup;

    $_SESSION['PIDSPV']=$pidspv;
    $_SESSION['PIDDM']=$piddm;
    $_SESSION['PIDSM']=$pidsm;
    $_SESSION['PIDGSM']=$pidgsm;

    $_SESSION['REGION']=$pregion;
    $_SESSION['CABID']=$pcabid;


    if (!empty($puserlogin)) $_SESSION['USERNAME']=$puserlogin;
    $_SESSION['STSADMIN']=$plevel;
    $_SESSION['LEVELUSER'] = $plevel;
    $_SESSION['ADMINKHUSUS']=$pakhusus;

    $pdivisikhusus="";
    if ($pakhusus=="Y"){
        $query="select DivProdId as divprodid from dbmaster.sdm_users_khusus where karyawanId='$pidkaryawan'";
        $tampilkh=  mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        $ketemukh=  mysqli_num_rows($tampilkh);
        if ($ketemukh>0) {
            while ($k=  mysqli_fetch_array($tampilkh)) {
                $pdivisikhusus=$pdivisikhusus."'".$k['divprodid']."',";
            }
            if (!empty($pdivisikhusus)) {
                $pdivisikhusus="(".substr($pdivisikhusus, 0, -1).")";

            }
        }
    }
    $_SESSION['KHUSUSSEL']=$pdivisikhusus;

    $pnregion="";
    $pnaksesjbt="";

    $cari = "select id_group, jabatanid as jabatanid, region as region from dbmaster.sdm_aksesbygroupuser WHERE ID_GROUP='$pidgroup'";
    $tampilag = mysqli_query($cnmy, $cari);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    $ketemuag = mysqli_num_rows($tampilag);
    if ($ketemuag>0) {
        $ag= mysqli_fetch_array($tampilag);

        if (!empty($ag['jabatanid'])) $pnaksesjbt=$ag['jabatanid'];
        if (!empty($ag['region'])) $pnregion=$ag['region'];
    }

    $_SESSION['AKSES_JABATAN']=$pnaksesjbt;
    $_SESSION['AKSES_REGION']=$pnregion;


    $paksescab = "";
    $queryakc = "select karyawanid as karyawanid, c.IDCAB as idcab, o.ICABANGID as icabangid from dbmaster.sdm_aksesbycabang as c 
            JOIN dbmaster.sdm_admincabang_on as o on c.IDCAB=o.IDCAB WHERE karyawanid='$pidkaryawan'";
    $tampilakc = mysqli_query($cnmy, $queryakc);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    $ketemuakc = mysqli_num_rows($tampilakc);
    if ($ketemuakc>0) {
        while ($akc= mysqli_fetch_array($tampilakc)) {
            if (!empty($akc['icabangid']))
                $paksescab=$paksescab."'".$akc['icabangid']."',";
        }
        if (!empty($paksescab)) {
            $paksescab = substr($paksescab, 0, -1);
        }
    }

    $_SESSION['AKSES_CABANG'] = $paksescab;


    //cek menu tambahan
    $_SESSION['MENUTAMBAHGRP']="";
    $_SESSION['MENUTAMBAHID']="";
    $querytm = "select karyawanid as karyawanid, igroup as igroup from dbmaster.t_karyawan_menu WHERE karyawanid='$_SESSION[IDCARD]'";
    $tampiltm=mysqli_query($cnmy, $querytm);
    $ketemutm=mysqli_num_rows($tampiltm);
    if ((INT)$ketemutm>0) {
        $rtm= mysqli_fetch_array($tampiltm);
        $pigroupmenutm=$rtm['igroup'];
        $fidmenutambah="";
        $querytmg = "select igroup as igroup, `id` as idmenu from dbmaster.t_karyawan_menu_d WHERE igroup='$pigroupmenutm'";
        $tampiltmg=mysqli_query($cnmy, $querytmg);
        $ketemutmg=mysqli_num_rows($tampiltmg);
        if ((INT)$ketemutmg>0) {
            $_SESSION['MENUTAMBAHGRP']=$pigroupmenutm;
            while ($itm= mysqli_fetch_array($tampiltmg)) {
                $nidmenutm=$itm['idmenu'];

                $fidmenutambah .="'".$nidmenutm."',";
            }
            if (!empty($fidmenutambah)) $fidmenutambah="(".substr($fidmenutambah, 0, -1).")";
            $_SESSION['MENUTAMBAHID']=$fidmenutambah;
        }

    }
    //END cek menu tambahan


    $_SESSION['JMLRECSPD']=30;
    $queryjmlrec = "select IFNULL(jmlrec_spd,0) jmlrec_spd from dbmaster.t_setup WHERE IFNULL(jmlrec_spd,0) <> 0 LIMIT 1";
    $tampiljr = mysqli_query($cnmy, $queryjmlrec);
    $ketemujr = mysqli_num_rows($tampiljr);
    if ($ketemujr>0) {
        $jr= mysqli_fetch_array($tampiljr);
        if (!empty($jr['jmlrec_spd'])) {
            $_SESSION['JMLRECSPD']=$jr['jmlrec_spd'];
        }
    }



    include "$_SESSION[FOLDERGL]/config/mobile.php";
    if(mobile_device_detect(true,true,true,true,false,false)){
        $_SESSION['MOBILE']="Y";
    }else{
        $_SESSION['MOBILE']="N";
    }





        //OTC SPV DM DLL
        if ($_SESSION['DIVISI']=="OTC") {
            if ($_SESSION['JABATANID']=="18" OR $_SESSION['JABATANID']=="20" OR $_SESSION['JABATANID']=="23") {
                $_SESSION['GROUP']=391;
            }
        }
        //END OTC SPV DM DLL


        //id karyawan kontrak untuk input rutin.
        if ($_SESSION['DIVISI']=="OTC")
            $_SESSION['KRYNONE']="0000002200";
        else
            $_SESSION['KRYNONE']="0000002083";


        $_SESSION['UKHUSUS']="N";
        $_SESSION['LEVELUSER'] = "guest";
        $_SESSION['STSADMIN']="guest";
        $_SESSION['ADMINKHUSUS']="N";
        $_SESSION['KHUSUSSEL']="";
        $_SESSION['FOTOKU']="";


        $_SESSION['ALOKASIID']="";
        if ($_SESSION['DIVISI']=="OTC" AND $_SESSION['GROUP']=="36") {
            if ($_SESSION['IDCARD']=="0000000515") {
                $_SESSION['ALOKASIID']="JKT_RETAIL";
            }elseif ($_SESSION['IDCARD']=="0000000424") {
                $_SESSION['ALOKASIID']="JKT_MT";
            }
        }
        $_SESSION['PROSESLOGKONEK_IT']=false; //dipake di import data sales







    $home_pildm=$_SESSION['PIDDM'];
    $home_pilsm=$_SESSION['PIDSM'];
    $home_pilgsm=$_SESSION['PIDGSM'];
    $home_pilidjabatan=$_SESSION['JABATANID'];

    $ipilihmenu_atasan=false;
    if ($home_pilidjabatan=="15") {
        if (empty($home_pilspv) AND empty($home_pildm) AND empty($home_pilsm)) {
            $ipilihmenu_atasan=true;
        }
    }elseif ($home_pilidjabatan=="10" OR $home_pilidjabatan=="18") {
        if (empty($home_pildm) AND empty($home_pilsm)) {
            $ipilihmenu_atasan=true;
        }
    }elseif ($home_pilidjabatan=="08") {
        if (empty($home_pilsm)) {
            $ipilihmenu_atasan=true;
        }
    }

    //untuk 1 minggu
    if ($home_pilidjabatan=="15" OR $home_pilidjabatan=="10" OR $home_pilidjabatan=="18" OR $home_pilidjabatan=="08") {
        $ipilihmenu_atasan=true;
    }


    if ($_SESSION['DIVISI']=="OTC") {
        $ipilihmenu_atasan=false;
    }



    include "timeout.php";

    timer();

    $sid_lama = session_id();
    session_regenerate_id();
    $sid_baru = session_id();
    $_SESSION['IDSESI']=$sid_baru;


    // now try it
    $ua=getBrowser();
    //$yourbrowser= "Your browser: " . $ua['singkatan'] . " " . $ua['name'] . " " . $ua['version'] . " on " .$ua['platform'] . " reports: <br >" . $ua['userAgent'];
    $pjnsbrospilih=$ua['singkatan'];
    //print_r($pjnsbrospilih); mysqli_close($cnmy); exit;
    $puser_ipaddr = getUserIP();
    if ($puser_ipaddr=="::1") $puser_ipaddr="";


    mysqli_query($cnmy, "insert into dbmaster.sdm_users_log (KARYAWANID, SESSION_ID, AKTIF, MOBILE, JBROWSE, IPADD)values('$_SESSION[IDCARD]', '$sid_baru', 'Y', '$_SESSION[MOBILE]', '$pjnsbrospilih', '$puser_ipaddr')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    mysqli_query($cnmy, "UPDATE dbmaster.sdm_users SET ID_SESSION='$sid_baru', ONLINE='Y' WHERE (karyawanId='$username' or USERNAME='$username') LIMIT 1");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    mysqli_close($cnmy);

    if ($ipilihmenu_atasan==true) {
        //header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=mstsesuaidatakry&idmenu=299&nmun=nmun&act=editdata&nlog=ilog&id='.$_SESSION['IDCARD']);
    }else{
        //header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=home&users='.$sid_baru);
    }


    //header('location:'.$_SESSION['FOLDERGL'].'/media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act='.$pidmenu.'&kriteria=Y');
    exit;
    
    
}
?>




<?PHP
    function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";
        $bsingkat = 'Unknown';

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Internet Explorer';
            $bsingkat = 'IE';
            $ub = "MSIE";
        }
        elseif(preg_match('/Trident/i',$u_agent))
        { // this condition is for IE11
            $bname = 'Internet Explorer';
            $bsingkat = 'IE';
            $ub = "rv";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $bsingkat = 'MF';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $bsingkat = 'GC';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $bsingkat = 'AS';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Opera';
            $bsingkat = 'OP';
            $ub = "Opera";
        }
        elseif(preg_match('/OPR/i',$u_agent))
        {
            $bname = 'Opera';
            $bsingkat = 'OP';
            $ub = "OPR";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $bsingkat = 'NC';
            $ub = "Netscape";
        }

        // finally get the correct version number
        // Added "|:"
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
         ')[/|: ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern,
            'singkatan'  =>$bsingkat,
        );
    }


    function getUserIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                  $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }
?>