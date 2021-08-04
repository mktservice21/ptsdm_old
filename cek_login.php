<?php
session_start();

$_SESSION['FOLDERGL']="mysdm";
include "$_SESSION[FOLDERGL]/config/koneksimysqli.php";
//include "$_SESSION[FOLDERGL]/config/koneksimysqli_it.php";
include "$_SESSION[FOLDERGL]/config/fungsi_sql.php";

$username   = $_POST['t_email'];
$pass       = $_POST['t_pass'];



$masukpass=0;
$ketemuuser=0;
$ketemumysql=0;
$fromuser=mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_users WHERE USERNAME='$username'");
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
$ketemuuser=mysqli_num_rows($fromuser);
if ($ketemuuser>0) {
    $ketemuuser=0;
    $rx=mysqli_fetch_array($fromuser);
    $idkaryawan=$rx['karyawanId'];
    if (!empty($rx['PASSWORD'])){
        include "$_SESSION[FOLDERGL]/config/encriptpassword.php";
        $tglnya=$rx['CREATEDPW'];

        $thn=substr($tglnya,0,4);
        $bln=substr($tglnya,5,2);
        $tgl=substr($tglnya,8,2);
        $tanggal=$thn.$bln.$tgl;

        $password   = encriptpasswordSSQl($pass, $tanggal);
        $masukpass=1;
    }
    
    if ($masukpass==1){
        $masukpass=0;
        $fromuser=mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_users WHERE USERNAME='$username' and PASSWORD='$password'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        $ketemuuser=mysqli_num_rows($fromuser);
        if ($ketemuuser>0) {
            $zz=mysqli_fetch_array($fromuser);
            $masukpass=1;
        }
    }
    $username=$idkaryawan;
}

$pfilteraktif=" AND IFNULL(aktif,'')<>'N' ";
if ($username=="175" OR $username=="0000000175") {
	$pfilteraktif="";
}

$karyawankhusus=false;
if ($masukpass==1){
    $sql=mysqli_query($cnmy, "SELECT * FROM dbmaster.t_karyawan_khusus WHERE karyawanId='$username' AND aktif<>'N'");
    $ketemukhusus=mysqli_num_rows($sql);
    if ($ketemukhusus==1) $karyawankhusus=true;
}

if ($karyawankhusus==false) {
	if ($masukpass==1){
		$sql=mysqli_query($cnmy, "SELECT * FROM hrd.karyawan WHERE karyawanId=$username $pfilteraktif");//$cnit
	}else{
		$sql=mysqli_query($cnmy, "SELECT * FROM hrd.karyawan WHERE karyawanid=$username and pin='$pass' AND aktif<>'N'");//$cnit
	}
	$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
}

if (!empty($sql))
    $ketemumysql=mysqli_num_rows($sql);
else
    $ketemumysql=0;


if ($ketemumysql > 0){
    
    $r=mysqli_fetch_array($sql);
    include "timeout.php";
    
    $_SESSION['NAMAPT'] = "PT. Surya Dermato Medica";
    $_SESSION['EMAIL']=$username;
    $_SESSION['IDCARD']=$r['karyawanId'];
    $_SESSION['USERID']=(int)$r['karyawanId'];
    $_SESSION['USERNAME']=(int)$r['karyawanId'];
    $_SESSION['NAMALENGKAP']=$r['nama'];
    
    $periodemasuk="2018-07-02";
    if (!empty($r['tglmasuk']) OR $r['tglmasuk']<>"0000-00-00")
        $periodemasuk= date("d-m-Y", strtotime($r['tglmasuk']));
    $_SESSION['MEMBERSEJAK']=$periodemasuk;
    $_SESSION['DIVISI']=$r['divisiId'];
    if (empty($_SESSION['DIVISI'])) $_SESSION['DIVISI']="HO";
    $_SESSION['JABATANID'] = $r['jabatanId'];
    
    $_SESSION['ATASANID']=$r['atasanId'];
    $t = mysqli_fetch_array(mysqli_query($cnmy, "select nama from hrd.karyawan where karyawanId='$_SESSION[ATASANID]'"));
    $_SESSION['NAMAATASAN']=$t['nama'];
    
    $carijabatan = mysqli_query($cnmy, "select jabatanId, nama nama_jabatan, rank from hrd.jabatan WHERE jabatanId='$_SESSION[JABATANID]'");
    $jb = mysqli_fetch_array($carijabatan);
    $_SESSION['JABATANNM']=$jb['nama_jabatan'];
    $_SESSION['JABATANRANK']=(int)$jb['rank'];
    
    $carijabatanlvl = mysqli_query($cnmy, "select jabatanId, LEVELPOSISI, ID_GROUP from dbmaster.jabatan_level WHERE jabatanId='$_SESSION[JABATANID]'");
    $jl = mysqli_fetch_array($carijabatanlvl);
    
    $_SESSION['GROUP']=$jl['ID_GROUP'];
    
    $_SESSION['LVLPOSISI']=trim($jl['LEVELPOSISI']);
    if (empty($_SESSION['LVLPOSISI'])) $_SESSION['LVLPOSISI']="HO1";
    
    
    $_SESSION['IDCABANG']="";
    $_SESSION['NMCABANG']="";
    $_SESSION['IDAREA']="";
    $_SESSION['NMAREA']="";
    $_SESSION['REGION']="";
    $_SESSION['CABID']="";
    
    
    $_SESSION['PIDSPV']="";
    $_SESSION['PIDDM']="";
    $_SESSION['PIDSM']="";
    $_SESSION['PIDGSM']="";
    
    $cari = "select * from dbmaster.t_karyawan_posisi WHERE karyawanId='$_SESSION[IDCARD]'";
    $tampil = mysqli_query($cnmy, $cari);//$cnit
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }//$cnit
    $ketemu = mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $j= mysqli_fetch_array($tampil);
        
        $_SESSION['PIDSPV']=$j['spv'];
        $_SESSION['PIDDM']=$j['dm'];
        $_SESSION['PIDSM']=$j['sm'];
        $_SESSION['PIDGSM']=$j['gsm'];
        
        $_SESSION['JABATANID'] = $j['jabatanId'];
        
        $carijabatan = mysqli_query($cnmy, "select jabatanId, nama nama_jabatan, rank from hrd.jabatan WHERE jabatanId='$_SESSION[JABATANID]'");
        $jb = mysqli_fetch_array($carijabatan);
        $_SESSION['JABATANNM']=$jb['nama_jabatan'];
        $_SESSION['JABATANRANK']=(int)$jb['rank'];
        
        $carijabatanlvl = mysqli_query($cnmy, "select jabatanId, LEVELPOSISI, ID_GROUP from dbmaster.jabatan_level WHERE jabatanId='$_SESSION[JABATANID]'");
        $jl = mysqli_fetch_array($carijabatanlvl);
        $_SESSION['GROUP']=$jl['ID_GROUP'];
        
        
        $_SESSION['LVLPOSISI']=trim($jl['LEVELPOSISI']);
        if (empty($_SESSION['LVLPOSISI'])) $_SESSION['LVLPOSISI']="HO1";
        
        $_SESSION['DIVISI']=$j['divisiId'];
        if (empty($_SESSION['DIVISI'])) $_SESSION['DIVISI']="HO";
        
        $_SESSION['IDCABANG']=$j['iCabangId'];
        $_SESSION['IDAREA']=$j['areaId'];
        
        if ($_SESSION['DIVISI']=="OTC") {
            $t = mysqli_fetch_array(mysqli_query($cnmy, "select nama from MKT.iarea_o where icabangid_o='$j[iCabangId]' and areaid_o='$j[areaId]'"));
            $_SESSION['NMAREA'] = $t['nama'];
            $t = mysqli_fetch_array(mysqli_query($cnmy, "select nama from MKT.icabang_o where icabangid_o='$j[iCabangId]'"));
            $_SESSION['NMCABANG'] = $t['nama'];
        }else{
            $t = mysqli_fetch_array(mysqli_query($cnmy, "select Nama as nama from MKT.iarea where iCabangId='$j[iCabangId]' and areaId='$j[areaId]'"));
            $_SESSION['NMAREA'] = $t['nama'];
            $t = mysqli_fetch_array(mysqli_query($cnmy, "select nama from MKT.icabang where iCabangId='$j[iCabangId]'"));
            $_SESSION['NMCABANG'] = $t['nama'];
        }
        
        $_SESSION['REGION']=$j['region'];
        $_SESSION['CABID']=$j['IDCAB'];
        if ($_SESSION['CABID']==0) $_SESSION['CABID'] = "";
        $_SESSION['ATASANID']=$j['atasanId'];
        $t = mysqli_fetch_array(mysqli_query($cnmy, "select nama from hrd.karyawan where karyawanId='$_SESSION[ATASANID]'"));
        $_SESSION['NAMAATASAN']=$t['nama'];
        
    }
    
    //OTC SPV DM DLL
    if ($_SESSION['DIVISI']=="OTC") {
        if ($_SESSION['JABATANID']=="18" OR $_SESSION['JABATANID']=="20" OR $_SESSION['JABATANID']=="23") {
            $_SESSION['GROUP']=391;
        }
    }
    //END OTC SPV DM DLL
    
    $_SESSION['UKHUSUS']="N";
    $_SESSION['LEVELUSER'] = "guest";
    $_SESSION['STSADMIN']="guest";
    
    $_SESSION['ADMINKHUSUS']="N";
    $_SESSION['KHUSUSSEL']="";
    
    
    $_SESSION['FOTOKU']="";

    //$_SESSION['S_TAMBAH']="";
    //$_SESSION['S_EDIT']="";
    //$_SESSION['S_HAPUS']="";
    
    
    $query=mysqli_query($cnmy, "SELECT * FROM dbmaster.sdm_users WHERE karyawanId=$username");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    $ketemu=mysqli_num_rows($query);
    if ($ketemu>0) {
        $rx=mysqli_fetch_array($query);
        
        if (!empty($rx['ID_GROUP'])) $_SESSION['GROUP']=$rx['ID_GROUP'];
        $_SESSION['USERNAME']=$rx['USERNAME'];
        $_SESSION['STSADMIN']=$rx['LEVEL'];
        $_SESSION['LEVELUSER'] = $rx['LEVEL'];
        if (!empty($rx['AKHUSUS']))
            $_SESSION['ADMINKHUSUS']=$rx['AKHUSUS'];
        
        
        if ($_SESSION['ADMINKHUSUS']=="Y"){
            $query="select DivProdId from dbmaster.sdm_users_khusus where karyawanId='$_SESSION[IDCARD]'";
            $sql=  mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $ketemu=  mysqli_num_rows($sql);
            if ($ketemu>0) {
                $divnya="";
                while ($k=  mysqli_fetch_array($sql)) {
                    $divnya=$divnya."'".$k['DivProdId']."',";
                }
                if (!empty($divnya)) {
                    $divnya="(".substr($divnya, 0, -1).")";
                    $_SESSION['KHUSUSSEL']=$divnya;
                }
            }
        }
        
        //$_SESSION['FOTOKU']=$r['GAMBAR'];

        
    }
    
    include "$_SESSION[FOLDERGL]/config/mobile.php";
    if(mobile_device_detect(true,true,true,true,false,false)){
        $_SESSION['MOBILE']="Y";
    }else{
        $_SESSION['MOBILE']="N";
    }
    
    $_SESSION['DIVISIKHUSUS']="N";
    if ($_SESSION['IDCARD']=="0000000432") {
        $_SESSION['DIVISIKHUSUS']="Y";
        $_SESSION['DIVISI'] = "HO";
    }
    
    $_SESSION['AKSES_JABATAN']="";
    $_SESSION['AKSES_REGION']="";
    
    if ($_SESSION['GROUP']!="") {
        $cari = "select * from dbmaster.sdm_aksesbygroupuser WHERE ID_GROUP=$_SESSION[GROUP]";
        $tampil = mysqli_query($cnmy, $cari);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        $ketemu = mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $j= mysqli_fetch_array($tampil);
            if (!empty($j['JABATANID'])) {
                $_SESSION['AKSES_JABATAN']=$j['JABATANID'];
            }

            if (!empty($j['REGION']))
                $_SESSION['AKSES_REGION']=$j['REGION'];
        }
    }
    
    $_SESSION['AKSES_CABANG'] = "";
    $cari = "select c.IDCAB, o.ICABANGID from dbmaster.sdm_aksesbycabang as c 
            JOIN dbmaster.sdm_admincabang_on as o on c.IDCAB=o.IDCAB WHERE KARYAWANID='$_SESSION[IDCARD]'";
    $tampil = mysqli_query($cnmy, $cari);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    $ketemu = mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $aksescab = "";
        while ($j= mysqli_fetch_array($tampil)) {
            if (!empty($j['ICABANGID']))
                $aksescab=$aksescab."'".$j['ICABANGID']."',";
        }
        if (!empty($aksescab)) {
            $aksescab = substr($aksescab, 0, -1);
            $_SESSION['AKSES_CABANG']=$aksescab;
        }
    }
	
    $_SESSION['JMLRECSPD']=30;
    $cari = "select IFNULL(jmlrec_spd,0) jmlrec_spd from dbmaster.t_setup WHERE IFNULL(jmlrec_spd,0) <> 0 LIMIT 1";
    $tampilz = mysqli_query($cnmy, $cari);
    $ketemu = mysqli_num_rows($tampilz);
    if ($ketemu>0) {
        $jz= mysqli_fetch_array($tampilz);
        if (!empty($jz['jmlrec_spd'])) {
            $_SESSION['JMLRECSPD']=$jz['jmlrec_spd'];
        }
    }
	
    if ($_SESSION['DIVISI']=="OTC")
        $_SESSION['KRYNONE']="0000002200";
    else
        $_SESSION['KRYNONE']="0000002083";
    
	
    $_SESSION['ALOKASIID']="";
    if ($_SESSION['DIVISI']=="OTC" AND $_SESSION['GROUP']=="36") {
            if ($_SESSION['IDCARD']=="0000000515") {
                $_SESSION['ALOKASIID']="JKT_RETAIL";
            }elseif ($_SESSION['IDCARD']=="0000000424") {
                $_SESSION['ALOKASIID']="JKT_MT";
            }
    }
	
    $_SESSION['PROSESLOGKONEK_IT']=false; //dipake di import data sales
	
	
    //CEK ABSEN
    $_SESSION['K_LATITUDE']="";
    $_SESSION['K_LONGITUDE']="";
    $_SESSION['K_RADIUS']="";
    
    $_SESSION['R_STATUSABS']="";
    $_SESSION['R_LATITUDE']="";
    $_SESSION['R_LONGITUDE']="";
    $_SESSION['R_RADIUS']="";
    
    $_SESSION['J_MASUK']="";
    $_SESSION['J_PULANG']="";
    $_SESSION['J_ISTIRAHAT']="";
    $_SESSION['J_MSKISTIRAHAT']="";
    $_SESSION['J_MENIT_LAMBAT_MASUK']="";
    
    $queryabs = "select id_status, a_latitude, a_longitude, a_radius from hrd.karyawan_absen WHERE karyawanid='$_SESSION[IDCARD]'";
    $tampilabs=mysqli_query($cnmy, $queryabs);
    $ketemutabs=mysqli_num_rows($tampilabs);
    if ((INT)$ketemutabs>0) {
        $abs= mysqli_fetch_array($tampilabs);
        $_SESSION['R_STATUSABS']=$abs['id_status'];
        $_SESSION['R_LATITUDE']=$abs['a_latitude'];
        $_SESSION['R_LONGITUDE']=$abs['a_longitude'];
        $_SESSION['R_RADIUS']=$abs['a_radius'];
        
        if (empty($_SESSION['R_STATUSABS'])) $_SESSION['R_STATUSABS']="HO1";
        
        $queryabs_a = "select id_status, kode_absen, jam, menit_terlambat from hrd.t_absen_status WHERE id_status='".$_SESSION['R_STATUSABS']."'";
        $tampilabs_a=mysqli_query($cnmy, $queryabs_a);
        $ketemutabs_a=mysqli_num_rows($tampilabs_a);
        if ((INT)$ketemutabs_a>0) {
            while ($nabs= mysqli_fetch_array($tampilabs_a)) {
                $pkodeabs=$nabs['kode_absen'];
                $pjamabs=$nabs['jam'];
                $pmenitabs=$nabs['menit_terlambat'];
                
                if ($pkodeabs=="1") {
                    $_SESSION['J_MASUK']=$pjamabs;
                    $_SESSION['J_MENIT_LAMBAT_MASUK']=$pmenitabs;
                }elseif ($pkodeabs=="2") {
                    $_SESSION['J_PULANG']=$pjamabs;
                }elseif ($pkodeabs=="3") {
                    $_SESSION['J_ISTIRAHAT']=$pjamabs;
                }elseif ($pkodeabs=="4") {
                    $_SESSION['J_MSKISTIRAHAT']=$pjamabs;
                }
            }
        }
        
        $queryabs_k = "select id_status, sdm_latitude, sdm_longitude, sdm_radius from hrd.sdm_lokasi WHERE id_status='".$_SESSION['R_STATUSABS']."'";
        $tampilabs_k=mysqli_query($cnmy, $queryabs_k);
        $ketemutabs_k=mysqli_num_rows($tampilabs_k);
        if ((INT)$ketemutabs_k>0) {
            $kbs= mysqli_fetch_array($tampilabs_k);
            $_SESSION['K_LATITUDE']=$kbs['sdm_latitude'];
            $_SESSION['K_LONGITUDE']=$kbs['sdm_longitude'];
            $_SESSION['K_RADIUS']=$kbs['sdm_radius'];
        }
    }
    //END CEK ABSEN
	
	
    //cek menu tambahan
    $_SESSION['MENUTAMBAHGRP']="";
    $_SESSION['MENUTAMBAHID']="";
    $query = "select karyawanid as karyawanid, igroup as igroup from dbmaster.t_karyawan_menu WHERE karyawanid='$_SESSION[IDCARD]'";
    $tampiltm=mysqli_query($cnmy, $query);
    $ketemutm=mysqli_num_rows($tampiltm);
    if ((INT)$ketemutm>0) {
        $rtm= mysqli_fetch_array($tampiltm);
        $pigroupmenutm=$rtm['igroup'];
        $fidmenutambah="";
        $query = "select igroup as igroup, `id` as idmenu from dbmaster.t_karyawan_menu_d WHERE igroup='$pigroupmenutm'";
        $tampiltmg=mysqli_query($cnmy, $query);
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
	
	
    //CEK FOTO IMG
    $query = "select itipe from dbimages.img_foto_karyawan WHERE karyawanid='$_SESSION[IDCARD]'";
    $tampiltf=mysqli_query($cnmy, $query);
    $ketemutf=mysqli_num_rows($tampiltf);
    if ((INT)$ketemutf>0) {
        $rf= mysqli_fetch_array($tampiltf);
        if (!empty($rf['itipe'])) {
            $_SESSION['FOTOKU']=$rf['itipe'];
        }
    }
    //END CEK FOTO IMG
    
    timer();

    $sid_lama = session_id();
    session_regenerate_id();
    $sid_baru = session_id();
    $_SESSION['IDSESI']=$sid_baru;
    $_SESSION['IDSESI_VPS']="";
	
	
	
	
    // now try it
    $ua=getBrowser();
    //$yourbrowser= "Your browser: " . $ua['singkatan'] . " " . $ua['name'] . " " . $ua['version'] . " on " .$ua['platform'] . " reports: <br >" . $ua['userAgent'];
    $pjnsbrospilih=$ua['singkatan'];
    //print_r($pjnsbrospilih); mysqli_close($cnmy); exit;
	
    $puser_ipaddr = getUserIP();
    if ($puser_ipaddr=="::1") $puser_ipaddr="";
    //echo $puser_ipaddr; mysqli_close($cnmy); exit;

    //mysqli_query($cnmy, "insert into dbmaster.sdm_users_log (KARYAWANID, SESSION_ID, AKTIF)values('$_SESSION[IDCARD]', '$sid_baru', 'Y')");
    $query ="insert into dbmaster.sdm_users_log (KARYAWANID, SESSION_ID, AKTIF, MOBILE, JBROWSE, IPADD)values('$_SESSION[IDCARD]', '$sid_baru', 'Y', '$_SESSION[MOBILE]', '$pjnsbrospilih', '$puser_ipaddr')";
    //echo $query;
    mysqli_query($cnmy, "insert into dbmaster.sdm_users_log (KARYAWANID, SESSION_ID, AKTIF, MOBILE, JBROWSE, IPADD)values('$_SESSION[IDCARD]', '$sid_baru', 'Y', '$_SESSION[MOBILE]', '$pjnsbrospilih', '$puser_ipaddr')");
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }exit;
    mysqli_query($cnmy, "UPDATE dbmaster.sdm_users SET ID_SESSION='$sid_baru', ONLINE='Y' WHERE (karyawanId='$username' or USERNAME='$username')");
    
    mysqli_close($cnmy);
    //mysqli_close($cnit);
	
	
	
	
	
    if ($_SESSION['IDCARD']=="0000000367") {
        //$_SESSION['FOLDERGL']="ms";
    }
	
	
    $home_pilspv=$_SESSION['PIDSPV'];
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
	//(divisi<>'OTC' AND (jbtid='15' OR jbtid='10' OR jbtid='18' OR jbtid='08'))
	
    if ($ipilihmenu_atasan==true) {
        header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=mstsesuaidatakry&idmenu=299&nmun=nmun&act=editdata&nlog=ilog&id='.$_SESSION['IDCARD']);
    }else{
        header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=home&users='.$sid_baru);
    }
	
	
    /////header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=home&users='.$sid_baru);
	
	
}else{
    echo "<script>alert('user atau password anda tidak terdaftar'); window.location = 'index.php'</script>";
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
?>

<?PHP

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


<form id="myForm" action="http://ms2.marvis.id/login" method="post">
    <input type="hidden" name="userid" value="<?PHP echo $username; ?>">
    <input type="hidden" name="password" value="<?PHP echo $pass; ?>">
<input type="hidden" name="kriteria" value="N">
</form>
<script type="text/javascript">
    //document.getElementById('myForm').submit();
</script>

<?PHP
/*
ini_set('session.cookie_domain',  'http://ms.marvis.id');
session_set_cookie_params(0, '/', 'http://ms.marvis.id');

header('Access-Control-Allow-Origin: http://ms.marvis.id');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
*/
?>
<!--
<script src="mysdm/vendors/jquery/dist/jquery.min.js"></script>
<script type="text/javascript">
    
    $(document).ready(function() {
        //alert("dasdsad");
        CekData();
    } );
                    
    function CekData() {
        var euser="972";
        var epass="1105";
        jQuery.support.cors = true;
        $.ajax({
            type:"post",
            timeout: 30000,
            url:"http://ms.marvis.id/zz_acoba.php?module=ceklogin",
            data:"t_email="+euser+"&t_pass="+epass,
            success:function(data){
                alert(data);
            },
            error: function(jqXHR, textStatus, ex) {
                alert(textStatus + "," + ex + "," + jqXHR.responseText);
            }
        });
        

    }
                    
</script>
-->