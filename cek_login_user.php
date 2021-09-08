<?php
session_start();

$_SESSION['FOLDERGL']="mysdm";
include "$_SESSION[FOLDERGL]/config/koneksimysqli.php";
include "$_SESSION[FOLDERGL]/config/fungsi_sql.php";


$_SESSION['UKHUSUS']="N";
$_SESSION['LEVELUSER'] = "guest";
$_SESSION['STSADMIN']="guest";
$_SESSION['ADMINKHUSUS']="N";
$_SESSION['KHUSUSSEL']="";
$_SESSION['FOTOKU']="";
$_SESSION['DIVISIKHUSUS']="N";
$_SESSION['AKSES_JABATAN']="";
$_SESSION['AKSES_REGION']="";
$_SESSION['AKSES_CABANG'] = "";
$_SESSION['JMLRECSPD']=30;
$_SESSION['ALOKASIID']="";
$_SESSION['PROSESLOGKONEK_IT']=false; //dipake di import data sales
$_SESSION['MENUTAMBAHGRP']="";
$_SESSION['MENUTAMBAHID']="";
$_SESSION['IDSESI']="";
$_SESSION['IDSESI_VPS']="";
$_SESSION['SUDAHUPDATEPASS']="N";
$_SESSION['IDADDRESS_SYS']="";


$ipilihmenu_atasan=false;
$pberhasillogin=false;
$pberhasilloginkhusus=false;
$psudahupdatepass=false;

$pusername  = $_POST['t_email'];
$ppassword  = $_POST['t_pass'];
$logpassword=$ppassword;

$pceknum  = is_numeric($pusername);

$sid_baru="";
$ploginposisi="";
$pidkaryawan="";
$logpassword="";
$pmessagelog="";


$query="SELECT karyawanId as karyawanid FROM dbmaster.t_karyawan_posisi WHERE slogin='Y' AND IFNULL(pin_pass,'')<>'' AND "
        . " IFNULL(tgl_pass,'') NOT IN ('', '0000-00-00 00:00:00') AND ";
if ($pceknum==true) $query .=" karyawanId=$pusername ";
else $query .=" username='$pusername' ";
$tampil_p=mysqli_query($cnmy, $query);
$ketemu_p=mysqli_num_rows($tampil_p);
if ((INT)$ketemu_p>0) {
    $psudahupdatepass=true;
    
    $query="SELECT karyawanId as karyawanid, username, slogin FROM dbmaster.t_karyawan_posisi WHERE "
            . " slogin='Y' AND pin_pass='$ppassword' AND ";
    if ($pceknum==true) $query .=" karyawanId=$pusername ";
    else $query .=" username='$pusername' ";

    $tampil_1=mysqli_query($cnmy, $query);
    $ketemu_1=mysqli_num_rows($tampil_1);
    if ((INT)$ketemu_1>0) {
        $lrow= mysqli_fetch_array($tampil_1);
        $ploginposisi=$lrow['slogin'];
        $pidkaryawan=$lrow['karyawanid'];

        if ($ploginposisi=="Y") $psudahupdatepass=true;
        $_SESSION['SUDAHUPDATEPASS']=$ploginposisi;

        $pberhasilloginkhusus=true;
        $pberhasillogin=true;
    }else{
        //user dan password salah
        $pmessagelog="USER DAN PASSWORD <b style='color:red;'>SALAH</b>";
        mysqli_close($cnmy);
        echo "<script>alert('user atau password anda tidak terdaftar'); window.location = 'index.php'</script>";
        exit;
    }
    
}



if ($psudahupdatepass==false){

    $query="SELECT karyawanId as karyawanid, username, slogin, pass, createdpw, exp_pass FROM dbmaster.t_karyawan_posisi WHERE ";
    if ($pceknum==true) $query .=" karyawanId=$pusername ";
    else $query .=" username='$pusername' ";

    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $row= mysqli_fetch_array($tampil);
        $ploginposisi=$row['slogin'];
        $pidkaryawan=$row['karyawanid'];
        $ppasslog=$row['pass'];
        $ptglcrt=$row['createdpw'];
        $ptglexp=$row['exp_pass'];

        if ($ploginposisi=="Y") $psudahupdatepass=true;
        $_SESSION['SUDAHUPDATEPASS']=$ploginposisi;
        
        $ptglcreate="";
        if (!empty($pidkaryawan) AND !empty($ppasslog) AND !empty($ptglcrt)) {
            include "$_SESSION[FOLDERGL]/config/encriptpassword.php";
            $ptglcreate = date("Ymd", strtotime($ptglcrt));

            $logpassword = encriptpasswordSSQl($ppassword, $ptglcreate);

            $query_log = "select karyawanId from dbmaster.t_karyawan_posisi WHERE pass='$logpassword'";
            if ($pceknum==true) $query_log .=" AND karyawanId=$pusername ";
            else $query_log .=" AND username='$pusername' ";

            $tampil_l=mysqli_query($cnmy, $query_log);
            $ketemu_l=mysqli_num_rows($tampil_l);
            if ((INT)$ketemu_l>0) {
                $pberhasilloginkhusus=true;
                $pberhasillogin=true;
                $pmessagelog="USER DAN PASSWORD <b style='color:red;'>BENER</b>";
            }else{
                $pmessagelog="USER DAN PASSWORD <b style='color:red;'>SALAH</b>";
            }
        }
    }else{
        $pmessagelog="USER TIDAK DITEMUKAN";
        $pberhasillogin=false;
    }

    if ($pceknum==true AND $pberhasillogin==false) {

        $query = "select karyawanId as karyawanid from hrd.karyawan WHERE karyawanId=$pusername";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {

            $row= mysqli_fetch_array($tampil);
            $pidkaryawan=$row['karyawanid'];

            $query_log = "select karyawanId from hrd.karyawan WHERE karyawanId=$pusername AND pin='$ppassword'";
            $tampil_l=mysqli_query($cnmy, $query_log);
            $ketemu_l=mysqli_num_rows($tampil_l);
            if ((INT)$ketemu_l>0) {
                $pberhasillogin=true;
                $pmessagelog="HRD : USER DAN PASSWORD <b style='color:red;'>BENER</b>";
            }else{
                $pmessagelog="HRD : USER DAN PASSWORD <b style='color:red;'>SALAH</b>";
            }


            if (!empty($pidkaryawan)) {
                $query_ins="select karyawanId from dbmaster.t_karyawan_posisi WHERE karyawanId='$pidkaryawan'";
                $tampil_ins=mysqli_query($cnmy, $query_ins);
                $ketemu_ins=mysqli_num_rows($tampil_ins);
                if ((INT)$ketemu_ins<=0) {
                    $query = "insert into dbmaster.t_karyawan_posisi (karyawanId, jabatanId, divisiId, "
                            . " iCabangId, areaId, atasanId, aktif, divisi1, divisi2)"
                            . " select karyawanId, jabatanId, divisiId, iCabangId, areaId, atasanId, "
                            . " AKTIF, divisiId as divisi1, divisiId2 as divisi2 from hrd.karyawan WHERE karyawanId='$pidkaryawan'";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                    $pmessagelog="berhasil tambah user...";
                }
            }


        }

    }
    
    
}

//echo "<br/>$pmessagelog<br/>";exit;

    
if ($pberhasillogin==true) {
    
    $query_karyawan = "select karyawanId as karyawanid, nama as nama_karyawan, tglmasuk, divisiId as divisiid,"
            . " jabatanId as jabatanid, atasanId as atasanid, "
            . " iCabangId as icabangid, areaId as areaid ";
    
    $query_1= $query_karyawan." from hrd.karyawan WHERE karyawanId='$pidkaryawan' AND IFNULL(aktif,'')<>'N' ";
    $tampil=mysqli_query($cnmy, $query_1);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu<=0) {
        $logincekkhs=false;
        if ($pberhasilloginkhusus==true) {
            
            $query_2= $query_karyawan." from dbmaster.t_karyawan_khusus WHERE karyawanId='$pidkaryawan' AND IFNULL(aktif,'')<>'N' ";
            $tampil=mysqli_query($cnmy, $query_2);
            $ketemu=mysqli_num_rows($tampil);
            
            if ((INT)$ketemu>0) $logincekkhs=true;
        }
        
        if ($logincekkhs == false) {
            mysqli_close($cnmy);
            echo "<script>alert('user atau password anda tidak terdaftar'); window.location = 'index.php'</script>";
            exit;
        }
        
    }
    $row= mysqli_fetch_array($tampil);
    
    $lkaryawanid=$row['karyawanid'];
    $lkryid=(INT)$row['karyawanid'];
    $lkaryawannm=$row['nama_karyawan'];
    $ltglmasuk=$row['tglmasuk'];
    $ldivisiid=$row['divisiid'];
    $ljabatanid=$row['jabatanid'];
    $latasanid=$row['atasanid'];
    $lcabangid=$row['icabangid'];
    $lareaid=$row['areaid'];
    
    
    $ljabatannm="";
    $ljabatanrank="";
    $latasannm="";
    $lcabangnm="";
    $lareanm="";
    $lnmgroupuser="";
    
    
    if ($ltglmasuk=="0000-00-00" OR $ltglmasuk=="0000-00-00 00:00:00") $ltglmasuk="";
    if (!empty($ltglmasuk)) $ltglmasuk= date("d-m-Y", strtotime($ltglmasuk));
    if (empty($ldivisiid)) $ldivisiid="HO";
    
    $query_j = "select nama as nama_jabatan, rank from hrd.jabatan WHERE jabatanId='$ljabatanid'";
    $tampil_j=mysqli_query($cnmy, $query_j);
    $jrow= mysqli_fetch_array($tampil_j);
    $ljabatannm=$jrow['nama_jabatan'];
    $ljabatanrank=(INT)$jrow['rank'];
    
    
    $query_l = "select LEVELPOSISI as lvlposisi, ID_GROUP as id_group from dbmaster.jabatan_level WHERE jabatanId='$ljabatanid'";
    $tampil_l=mysqli_query($cnmy, $query_l);
    $lrow= mysqli_fetch_array($tampil_l);
    $lidgroupuser=$lrow['id_group'];
    $lposisilvl=TRIM($lrow['lvlposisi']);
    
    if (empty($lposisilvl)) $lposisilvl="HO1";
    
    
    $query_a = "select nama as nama_atasan from hrd.karyawan WHERE karyawanId='$latasanid'";
    $tampil_a=mysqli_query($cnmy, $query_a);
    $arow= mysqli_fetch_array($tampil_a);
    $latasannm=$arow['nama_atasan'];
    
    
    $queryc="";
    $querycra="";
    if ($ldivisiid=="OTC" OR $ldivisiid=="CHC") {
        $queryc = "select nama as nama_cabang from mkt.icabang_o WHERE icabangid_o='$lcabangid'";
        $querycra = "select nama as nama_area from mkt.iarea_o WHERE icabangid_o='$lcabangid' AND areaid_o='$lareaid'";
    }else{
        $queryc = "select nama as nama_cabang from mkt.icabang WHERE iCabangId='$lcabangid'";
        $querycra = "select nama as nama_area from mkt.iarea WHERE iCabangId='$lcabangid' AND areaId='$lareaid'";
    }
    $tampilc=mysqli_query($cnmy, $queryc);
    $crow= mysqli_fetch_array($tampilc);
    $lcabangnm=$crow['nama_cabang'];
    
    $tampilar=mysqli_query($cnmy, $querycra);
    $arw= mysqli_fetch_array($tampilar);
    $lareanm=$arw['nama_area'];
    
    
    
    $query_psi = "select karyawanId as karyawanid, spv, dm, sm, gsm, "
            . " region, IDCAB as idcab, slogin "
            . " from dbmaster.t_karyawan_posisi WHERE karyawanId='$pidkaryawan'";
    $tampil_psi=mysqli_query($cnmy, $query_psi);
    $psrw= mysqli_fetch_array($tampil_psi);
    $psi_spv=$psrw['spv'];
    $psi_dm=$psrw['dm'];
    $psi_sm=$psrw['sm'];
    $psi_gsm=$psrw['gsm'];
    $psi_region=$psrw['region'];
    $psi_icab=$psrw['idcab'];
    $ploginposisi=$psrw['slogin'];
    
    
    $query_u = "select karyawanId as karyawanid, ID_GROUP as id_group, `LEVEL` as `nlevel`, `ONLINE` as `conline`, AKHUSUS as akhusus "
            . " from dbmaster.t_karyawan_group WHERE karyawanId='$pidkaryawan'";
    $tampil_u=mysqli_query($cnmy, $query_u);
    $ketemu_u= mysqli_num_rows($tampil_u);
    if ((INT)$ketemu_u>0) {
        $usr= mysqli_fetch_array($tampil_u);
        $usr_idgroup=$usr['id_group'];
        if ($usr_idgroup=="0") $usr_idgroup="";
        
        if (!empty($usr_idgroup)) $lidgroupuser=$usr_idgroup;
    }
    
    
    $query_ng = "select NAMA_GROUP as nama_group from dbmaster.sdm_groupuser WHERE ID_GROUP='$lidgroupuser'";
    $tampil_ng=mysqli_query($cnmy, $query_ng);
    $ngrw= mysqli_fetch_array($tampil_ng);
    $lnmgroupuser=$ngrw['nama_group'];
    
    
    
    $_SESSION['NAMAPT'] = "PT. Surya Dermato Medica";
    $_SESSION['EMAIL']=$pusername;
    $_SESSION['IDCARD']=$lkaryawanid;
    $_SESSION['USERID']=$lkryid;
    $_SESSION['USERNAME']=$lkryid;
    $_SESSION['NAMALENGKAP']=$lkaryawannm;
    $_SESSION['MEMBERSEJAK']=$ltglmasuk;
    $_SESSION['DIVISI']=$ldivisiid;
    $_SESSION['JABATANID'] = $ljabatanid;
    $_SESSION['JABATANNM']=$ljabatannm;
    $_SESSION['JABATANRANK']=$ljabatanrank;
    $_SESSION['ATASANID']=$latasanid;
    $_SESSION['NAMAATASAN']=$latasannm;
    
    $_SESSION['IDCABANG']=$lcabangid;
    $_SESSION['NMCABANG']=$lcabangnm;
    $_SESSION['IDAREA']=$lareaid;
    $_SESSION['NMAREA']=$lareanm;
    
    $_SESSION['GROUP']=$lidgroupuser;
    $_SESSION['NAMAGROUPID']=$lnmgroupuser;
    $_SESSION['LVLPOSISI']=$lposisilvl;
    
    
    
    $_SESSION['REGION']=$psi_region;
    $_SESSION['CABID']=$psi_icab;
    
    
    $_SESSION['PIDSPV']=$psi_spv;
    $_SESSION['PIDDM']=$psi_dm;
    $_SESSION['PIDSM']=$psi_sm;
    $_SESSION['PIDGSM']=$psi_gsm;
    
    $_SESSION['SUDAHUPDATEPASS']=$ploginposisi;
    
    
    //OTC CHC
    if ($_SESSION['DIVISI']=="OTC" OR $_SESSION['DIVISI']=="CHC") {
        $_SESSION['KRYNONE']="0000002200";
        
        if ($_SESSION['JABATANID']=="18" OR $_SESSION['JABATANID']=="20" OR $_SESSION['JABATANID']=="23") {
            //$_SESSION['GROUP']=391;
            $_SESSION['NAMAGROUPID']="NOONE";
        }
        
        if ($_SESSION['GROUP']=="36") {
            if ($_SESSION['IDCARD']=="0000000515") {
                $_SESSION['ALOKASIID']="JKT_RETAIL";
            }elseif ($_SESSION['IDCARD']=="0000000424") {
                $_SESSION['ALOKASIID']="JKT_MT";
            }
        }
        
    }else{
        $_SESSION['KRYNONE']="0000002083";
    }
    //END OTC SPV DM DLL
    
    include "$_SESSION[FOLDERGL]/config/mobile.php";
    if(mobile_device_detect(true,true,true,true,false,false)){
        $_SESSION['MOBILE']="Y";
    }else{
        $_SESSION['MOBILE']="N";
    }
    
    
    //cek menu tambahan
    $_SESSION['MENUTAMBAHGRP']="";
    $_SESSION['MENUTAMBAHID']="";
    $query = "select karyawanid as karyawanid, igroup as igroup from dbmaster.t_karyawan_menu WHERE karyawanid='$pidkaryawan'";
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
    
    $queryabs = "select id_status, a_latitude, a_longitude, a_radius from hrd.karyawan_absen WHERE karyawanid='$pidkaryawan' AND IFNULL(aktif,'')='Y'";
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
    
    //CEK FOTO IMG
    $querytf = "select itipe from dbimages.img_foto_karyawan WHERE karyawanid='$pidkaryawan'";
    $tampiltf=mysqli_query($cnmy, $querytf);
    $ketemutf=mysqli_num_rows($tampiltf);
    if ((INT)$ketemutf>0) {
        $rf= mysqli_fetch_array($tampiltf);
        if (!empty($rf['itipe'])) {
            $_SESSION['FOTOKU']=$rf['itipe'];
        }
    }
    //END CEK FOTO IMG
    
    
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
    
    
    if ($_SESSION['DIVISI']=="OTC" OR $_SESSION['DIVISI']=="CHC") {
        $ipilihmenu_atasan=false;
    }
    
    
    
    
    if ($_SESSION['SUDAHUPDATEPASS']=="N") {
        mysqli_close($cnmy);
        echo "<script>alert('user atau password anda tidak terdaftar'); window.location = 'index.php'</script>";
        exit;
    }
    
    
    
    
    // now try it
    $ua=getBrowser();
    //$yourbrowser= "Your browser: " . $ua['singkatan'] . " " . $ua['name'] . " " . $ua['version'] . " on " .$ua['platform'] . " reports: <br >" . $ua['userAgent'];
    $pjnsbrospilih=$ua['singkatan'];
    //print_r($pjnsbrospilih); mysqli_close($cnmy); exit;
	
    $puser_ipaddr = getUserIP();
    if ($puser_ipaddr=="::1") $puser_ipaddr="";
    //echo $puser_ipaddr; mysqli_close($cnmy); exit;
    $_SESSION['IDADDRESS_SYS']=$puser_ipaddr;
    
    
    include "timeout.php";
    timer();

    $sid_lama = session_id();
    session_regenerate_id();
    $sid_baru = session_id();
    $_SESSION['IDSESI']=$sid_baru;
    $_SESSION['IDSESI_VPS']="";

    
    
    /*
    echo "nama pt : ".$_SESSION['NAMAPT']."<br/>"; echo "email : ".$_SESSION['EMAIL']."<br/>"; echo "idcard : ".$_SESSION['IDCARD']."<br/>"; echo "userid : ".$_SESSION['USERID']."<br/>"; echo "username : ".$_SESSION['USERNAME']."<br/><hr/>";
    echo "nama lengkap : ".$_SESSION['NAMALENGKAP']."<br/>"; echo "member sejak : ".$_SESSION['MEMBERSEJAK']."<br/>"; echo "divisi : ".$_SESSION['DIVISI']."<br/><hr/>"; echo "id jabatan : ".$_SESSION['JABATANID']."<br/>";
    echo "nama jabatan : ".$_SESSION['JABATANNM']."<br/>"; echo "jabatan rank : ".$_SESSION['JABATANRANK']."<br/>"; echo "id atasan : ".$_SESSION['ATASANID']."<br/>"; echo "nama atasan : ".$_SESSION['NAMAATASAN']."<br/><hr/>";
    echo "group user : ".$_SESSION['GROUP']."<br/>"; echo "NAMA GROUP USER : ".$_SESSION['NAMAGROUPID']."<br/>"; echo "level posisi : ".$_SESSION['LVLPOSISI']."<br/><hr/>";
    echo "id cabang : ".$_SESSION['IDCABANG']."<br/>"; echo "nama cabang : ".$_SESSION['NMCABANG']."<br/>"; echo "id area : ".$_SESSION['IDAREA']."<br/>"; echo "nama area : ".$_SESSION['NMAREA']."<br/><hr/>";
    echo "region : ".$_SESSION['REGION']."<br/>"; echo "id cab : ".$_SESSION['CABID']."<br/><hr/>";
    echo "tambah menu group : ".$_SESSION['MENUTAMBAHGRP']."<br/>"; echo "tambah menu group id : ".$_SESSION['MENUTAMBAHID']."<br/><hr/>";
    echo "foto : ".$_SESSION['FOTOKU']."<br/><hr/>";
    echo "spv : ".$_SESSION['PIDSPV']."<br/>"; echo "dm : ".$_SESSION['PIDDM']."<br/>"; echo "sm : ".$_SESSION['PIDSM']."<br/>"; echo "gsm : ".$_SESSION['PIDGSM']."<br/><hr/>";
    echo "id session : ".$_SESSION['IDSESI']."<br/>"; echo "sudah update pass : ".$_SESSION['SUDAHUPDATEPASS']."<br/><hr/>";
    //echo "LEVEL : ".$_SESSION['LVLPOSISI']."<br/>";
    */
    
    
    mysqli_query($cnmy, "insert into dbmaster.sdm_users_log (KARYAWANID, SESSION_ID, AKTIF, MOBILE, JBROWSE, IPADD)values('$_SESSION[IDCARD]', '$sid_baru', 'Y', '$_SESSION[MOBILE]', '$pjnsbrospilih', '$puser_ipaddr')");
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    mysqli_query($cnmy, "UPDATE dbmaster.t_karyawan_group SET ID_SESSION='$sid_baru', ONLINE='Y' WHERE (karyawanId='$pusername' or USERNAME='$pusername')");
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_close($cnmy);
    
    
    
    //cek sudah pernah ubah pin atau password
    if ($psudahupdatepass==false) {
        //include "$_SESSION[FOLDERGL]/config/fungsi_ubahget_id.php";
        //$pidnoget=encodeString($pidkaryawan);
        //header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=tolsresetpass&idmenu=530&nmun=530&act=editdata&sloginawal=awal&id='.$pidnoget);
        //exit;
    }
    
    
    if ($ipilihmenu_atasan==true) {
        header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=mstsesuaidatakry&idmenu=299&nmun=nmun&act=editdata&nlog=ilog&id='.$pidkaryawan);
    }else{
        header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=home&users='.$sid_baru);
    }
    
    ///////header('location:'.$_SESSION['FOLDERGL'].'/media.php?module=home&users='.$sid_baru);
    
    //exit;
    
}else{
    mysqli_close($cnmy);
    echo "<script>alert('user atau password anda tidak terdaftar'); window.location = 'index.php'</script>";
    exit;
}
    

exit;


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

<?PHP
$pcurul=$_SERVER["HTTP_HOST"];
?>

<form id="myForm" action="http://ms2.marvis.id/login" method="post">
    <input type="hidden" name="userid" value="<?PHP echo $pusername; ?>">
    <input type="hidden" name="password" value="<?PHP echo $ppassword; ?>">
    <input type="hidden" name="purl" value="<?PHP echo $pcurul; ?>">
    <input type="hidden" name="kriteria" value="N">
</form>
<script type="text/javascript">
    ////var inama="<?PHP //echo "$_SESSION[NAMALENGKAP]"; ?>";
    ////alert(inama);
    document.getElementById('myForm').submit();
</script>