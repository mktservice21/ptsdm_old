<?PHP
session_start();

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);


$puserid="";
$pidcard="";
$pidgroup="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

if ($module=='dkdrealisasiplan')
{
    if ($act=="hapusdailydokt") {
        
        $kodenya=$_GET['ukryid'];
        $ntgl=$_GET['utgl'];
        $doktid=$_GET['udokt'];
        
        
        if (!empty($kodenya) AND !empty($ntgl) AND !empty($doktid)) {
            include "../../../config/koneksimysqli.php";
            
            mysqli_query($cnmy, "DELETE FROM hrd.dkd_new_real1 WHERE karyawanid='$kodenya' AND tanggal='$ntgl' AND dokterid='$doktid'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            mysqli_close($cnmy);
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilhapus');
        }
        exit;
    }elseif ($act=="hapusdaily") {
        $kodenya=$_GET['id'];
        $ntgl=$_GET['utgl'];
        
        if (!empty($kodenya) AND !empty($ntgl)) {
            include "../../../config/koneksimysqli.php";
            
            mysqli_query($cnmy, "DELETE FROM hrd.dkd_new_real1 WHERE karyawanid='$kodenya' AND tanggal='$ntgl'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            mysqli_close($cnmy);
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=lihatrlvisit');
        }
        
        exit;
    }elseif ($act=="dailyinput" OR $act=="dailyupdate") {
        
        $pcardidlog=$_POST['e_idcarduser'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }


        include "../../../config/koneksimysqli.php";

        $pkaryawanid=$_POST['e_idcarduser'];
        $pidjabatan=$_POST['e_idjbt'];
        
        
        $ptgl=$_POST['e_periode1'];
        $ppilihttdfoto=$_POST['opt_ttd'];
        //$pjenis=$_POST['cb_jv'];
        $pjenis="";
        if (isset($_POST['chk_jv'])) $pjenis=$_POST['chk_jv'];
        $pcabid=$_POST['cb_cabid'];
        $pdokterid=$_POST['cb_doktid'];
        $pketdokt=$_POST['e_ketdetail'];
        $psaran=$_POST['e_saran'];
        
        $_SESSION['RLWEKPLNCAB']=$pcabid;
                
        $ptanggal= date("Y-m-d", strtotime($ptgl));
        
        if (!empty($pketdokt)) $pketdokt = str_replace("'", " ", $pketdokt);
        if (!empty($psaran)) $psaran = str_replace("'", " ", $psaran);
        
        if (empty($pidjabatan)) {
            if (isset($_SESSION['JABATANID'])) $pidjabatan=$_SESSION['JABATANID'];
        }
        
        if ($pjenis=="JV") {
            
        }else{
            $query = "select dokterid from hrd.dkd_new1 as a WHERE "
                    . " a.dokterid='$pdokterid' AND a.tanggal='$ptanggal'";
            $tampil=mysqli_query($cnmy, $query);
            $ketemu=mysqli_num_rows($tampil);
            if ((INT)$ketemu<=0) {
                $pjenis="EC";
            }
        }
        
        
        $pkdspv=$_POST['e_kdspv'];
        $pkddm=$_POST['e_kddm'];
        $pkdsm=$_POST['e_kdsm'];
        $pkdgsm=$_POST['e_kdgsm'];
    
        
        $pisitglspv=false;
        $pisitgldm=false;
        $pisitglsm=false;
        $pisitglgsm=false;

        //$pkdspv="";$pkddm="";$pkdsm="A";$pkdgsm="A";

        if (empty($pkdspv)) {
            $pisitglspv=true;
            if (empty($pkddm)) {
                $pisitgldm=true;
                if (empty($pkdsm)) {
                    $pisitglsm=true;
                    if (empty($pkdgsm)) {
                        $pisitglgsm=true;
                    }
                }
            }
        }
        
        
        if ($act=="dailyinput") {
            
            
                    
            $query = "INSERT INTO hrd.dkd_new_real1 (tanggal, karyawanid, jenis, dokterid, notes, saran, jabatanid)
                VALUES
                ('$ptanggal', '$pkaryawanid', '$pjenis', '$pdokterid', '$pketdokt', '$psaran', '$pidjabatan')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $kodenya = mysqli_insert_id($cnmy);
            
            
            $query = "UPDATE hrd.dkd_new_real1 SET atasan1='$pkdspv', atasan2='$pkddm', atasan3='$pkdsm', atasan4='$pkdgsm' WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' AND nourut='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            

            if ($pisitglspv==true) {
                $query = "UPDATE hrd.dkd_new_real1 SET tgl_atasan1=NOW() WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' AND nourut='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }

            if ($pisitgldm==true) {
                $query = "UPDATE hrd.dkd_new_real1 SET tgl_atasan2=NOW() WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' AND nourut='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }

            if ($pisitglsm==true) {
                $query = "UPDATE hrd.dkd_new_real1 SET tgl_atasan3=NOW() WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' AND nourut='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }

            if ($pisitglgsm==true) {
                $query = "UPDATE hrd.dkd_new_real1 SET tgl_atasan4=NOW() WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' AND nourut='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }
            
            $pimgttd="";
            $pimgfoto="";
            if ($ppilihttdfoto=="ttd_by" OR $ppilihttdfoto=="foto_by") {
                
                //echo "$ppilihttdfoto"; exit;
                
                //mendefinisikan folder
                
                if ($ppilihttdfoto == "foto_by") {
                    define('UPLOAD_DIR', '../../../images/user_foto/');
                    $pimgttd=$_POST['txt_arss'];
                    $pdata=$pimgttd;
                }else{
                    define('UPLOAD_DIR', '../../../images/user_ttd/');
                    $pimgttd=$_POST['txtgambar'];
                    $pdata="data:".$pimgttd;
                }
                
                $pdata=str_replace(' ','+',$pdata);
                list($type, $pdata) = explode(';', $pdata);
                list(, $pdata)      = explode(',', $pdata);
                $pdata = base64_decode($pdata);
                
                
                if ($ppilihttdfoto == "foto_by") {
                    $pfile       = "ft_".$kodenya."_".uniqid() . '.png';
                }else{
                    $pfile       = "ttd_".$kodenya."_".uniqid() . '.png';
                }
                
                if (file_exists(UPLOAD_DIR.$pfile)) {
                    
                    $query="DELETE FROM hrd.dkd_new_real1 WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' AND nourut='$kodenya' LIMIT 1";
                    mysqli_query($cnmy, $query);
                    
                    mysqli_close($cnmy);
                    echo "nama file foto sudah ada...";
                    exit; 
                }
                
                file_put_contents(UPLOAD_DIR.$pfile, $pdata);
                
                //user_foto
                $puser_ttdfield="";
                if ($ppilihttdfoto == "foto_by") {
                    $puser_ttdfield=" user_foto ";
                }else{
                    $puser_ttdfield=" user_tandatangan ";
                }
                
                $query="UPDATE hrd.dkd_new_real1 SET $puser_ttdfield='$pfile' WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' AND nourut='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                
                    
                    // ======================================
                
                    $pjv_spv="";
                    $pjv_dm="";
                    $pjv_sm="";
                    $pjv_gsm="";
                    $pjv_with="";

                    if ($pjenis=="JV") {
                        if (isset($_POST['chk_jv_spv'])) $pjv_spv=$_POST['chk_jv_spv'];
                        if (isset($_POST['chk_jv_dm'])) $pjv_dm=$_POST['chk_jv_dm'];
                        if (isset($_POST['chk_jv_sm'])) $pjv_sm=$_POST['chk_jv_sm'];
                        if (isset($_POST['chk_jv_gsm'])) $pjv_gsm=$_POST['chk_jv_gsm'];
                    }
                    if ($pidjabatan=="10" OR $pidjabatan=="18") {
                        $pjv_spv="";
                    }elseif ($pidjabatan=="08") {
                        $pjv_spv="";
                        $pjv_dm="";
                    }elseif ($pidjabatan=="20") {
                        $pjv_spv="";
                        $pjv_dm="";
                        $pjv_sm="";
                    }elseif ($pidjabatan=="05") {
                        $pjv_spv="";
                        $pjv_dm="";
                        $pjv_sm="";
                        $pjv_gsm="";
                    }
                    
                    if (!empty($pjv_spv)) $pjv_with .=$pjv_spv.",";
                    if (!empty($pjv_dm))  $pjv_with .=$pjv_dm.",";
                    if (!empty($pjv_sm)) $pjv_with .=$pjv_sm.",";
                    if (!empty($pjv_gsm)) $pjv_with .=$pjv_gsm.",";
                    
                    if (!empty($pjv_with)) $pjv_with=substr($pjv_with, 0, -1);
                    
                    
                    $pbolehsimpan_jvspv=false;
                    $pbolehsimpan_jvdm=false;
                    $pbolehsimpan_jvsm=false;
                    $pbolehsimpan_jvgsm=false;

                    if (!empty($pjv_spv)) {
                        $query = "select tanggal from hrd.dkd_new_real1 WHERE tanggal='$ptanggal' AND karyawanid='$pjv_spv' AND dokterid='$pdokterid'";
                        $tampild=mysqli_query($cnmy, $query);
                        $ketemud=mysqli_fetch_array($tampild);
                        if ($ketemud>0) {
                            $query_ = "UPDATE hrd.dkd_new_real1 SET from_jv='$kodenya', jenis='JV' WHERE tanggal='$ptanggal' AND karyawanid='$pjv_spv' AND dokterid='$pdokterid' AND ifnull(from_jv,'') IN ('', '0') LIMIT 1";
                            mysqli_query($cnmy, $query_); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                        }else{
                            $pbolehsimpan_jvspv=true;
                        }
                    }

                    if (!empty($pjv_dm)) {
                        $query = "select tanggal from hrd.dkd_new_real1 WHERE tanggal='$ptanggal' AND karyawanid='$pjv_dm' AND dokterid='$pdokterid'";
                        $tampild=mysqli_query($cnmy, $query);
                        $ketemud=mysqli_fetch_array($tampild);
                        if ($ketemud>0) {
                            $query_ = "UPDATE hrd.dkd_new_real1 SET from_jv='$kodenya', jenis='JV' WHERE tanggal='$ptanggal' AND karyawanid='$pjv_dm' AND dokterid='$pdokterid' AND ifnull(from_jv,'') IN ('', '0') LIMIT 1";
                            mysqli_query($cnmy, $query_); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                        }else{
                            $pbolehsimpan_jvdm=true;
                        }
                    }

                    if (!empty($pjv_sm)) {
                        $query = "select tanggal from hrd.dkd_new_real1 WHERE tanggal='$ptanggal' AND karyawanid='$pjv_sm' AND dokterid='$pdokterid'";
                        $tampild=mysqli_query($cnmy, $query);
                        $ketemud=mysqli_fetch_array($tampild);
                        if ($ketemud>0) {
                            $query_ = "UPDATE hrd.dkd_new_real1 SET from_jv='$kodenya', jenis='JV' WHERE tanggal='$ptanggal' AND karyawanid='$pjv_sm' AND dokterid='$pdokterid' AND ifnull(from_jv,'') IN ('', '0') LIMIT 1";
                            mysqli_query($cnmy, $query_); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                        }else{
                            $pbolehsimpan_jvsm=true;
                        }
                    }

                    if (!empty($pjv_gsm)) {
                        $query = "select tanggal from hrd.dkd_new_real1 WHERE tanggal='$ptanggal' AND karyawanid='$pjv_gsm' AND dokterid='$pdokterid'";
                        $tampild=mysqli_query($cnmy, $query);
                        $ketemud=mysqli_fetch_array($tampild);
                        if ($ketemud>0) {
                            $query_ = "UPDATE hrd.dkd_new_real1 SET from_jv='$kodenya', jenis='JV' WHERE tanggal='$ptanggal' AND karyawanid='$pjv_gsm' AND dokterid='$pdokterid' AND ifnull(from_jv,'') IN ('', '0') LIMIT 1";
                            mysqli_query($cnmy, $query_); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                        }else{
                            $pbolehsimpan_jvgsm=true;
                        }
                    }
                    
                    // ======================================
                    
            $query = "UPDATE hrd.dkd_new_real1 SET jv_with='$pjv_with' WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' AND nourut='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query);
            
                    // ======================================
                    
                    if ($pbolehsimpan_jvspv==true) {
                        $query_kr = "select jabatanId as jabatanid from hrd.karyawan WHERE karyawanid='$pjv_spv'";
                        $tampilkr=mysqli_query($cnmy, $query_kr);
                        $rowkr= mysqli_fetch_array($tampilkr);
                        $pjbtkry_spv=$rowkr['jabatanid'];
                                
                        
                        $query = "INSERT INTO hrd.dkd_new_real1 (user_tandatangan, user_foto, from_jv, tanggal, karyawanid, jenis, dokterid, notes, saran, jabatanid, tgl_atasan1, atasan2, tgl_atasan2)"
                                . " select user_tandatangan, user_foto, '$kodenya' as from_jv, tanggal, '$pjv_spv' as karyawanid, jenis, dokterid, notes, saran, '$pjbtkry_spv' as jabatanid, NOW() as tgl_atasan1, atasan2, CASE WHEN IFNULL(atasan2,'')='' THEN NOW() ELSE NULL END as tgl_atasan2 "
                                . " FROM hrd.dkd_new_real1 WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' LIMIT 1";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                    }
                    
                    if ($pbolehsimpan_jvdm==true) {
                        $query_kr = "select jabatanId as jabatanid from hrd.karyawan WHERE karyawanid='$pjv_dm'";
                        $tampilkr=mysqli_query($cnmy, $query_kr);
                        $rowkr= mysqli_fetch_array($tampilkr);
                        $pjbtkry_dm=$rowkr['jabatanid'];
                        
                        
                        $query = "INSERT INTO hrd.dkd_new_real1 (user_tandatangan, user_foto, from_jv, tanggal, karyawanid, jenis, dokterid, notes, saran, jabatanid, tgl_atasan1, tgl_atasan2, atasan3)"
                                . " select user_tandatangan, user_foto, '$kodenya' as from_jv, tanggal, '$pjv_dm' as karyawanid, jenis, dokterid, notes, saran, '$pjbtkry_dm' as jabatanid, NOW() as tgl_atasan1, NOW() as tgl_atasan2, atasan3 "
                                . " FROM hrd.dkd_new_real1 WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' LIMIT 1";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                    }
                    
                    if ($pbolehsimpan_jvsm==true) {
                        $query_kr = "select jabatanId as jabatanid from hrd.karyawan WHERE karyawanid='$pjv_sm'";
                        $tampilkr=mysqli_query($cnmy, $query_kr);
                        $rowkr= mysqli_fetch_array($tampilkr);
                        $pjbtkry_sm=$rowkr['jabatanid'];
                        
                        
                        $query = "INSERT INTO hrd.dkd_new_real1 (user_tandatangan, user_foto, from_jv, tanggal, karyawanid, jenis, dokterid, notes, saran, jabatanid, tgl_atasan1, tgl_atasan2, atasan3, tgl_atasan3, atasan4)"
                                . " select user_tandatangan, user_foto, '$kodenya' as from_jv, tanggal, '$pjv_sm' as karyawanid, jenis, dokterid, notes, saran, '$pjbtkry_sm' as jabatanid, NOW() as tgl_atasan1, NOW() as tgl_atasan2, atasan3, NOW() as tgl_atasan3, atasan4 "
                                . " FROM hrd.dkd_new_real1 WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' LIMIT 1";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                    }
                    
                    if ($pbolehsimpan_jvgsm==true) {
                        $query_kr = "select jabatanId as jabatanid from hrd.karyawan WHERE karyawanid='$pjv_gsm'";
                        $tampilkr=mysqli_query($cnmy, $query_kr);
                        $rowkr= mysqli_fetch_array($tampilkr);
                        $pjbtkry_gsm=$rowkr['jabatanid'];
                        
                        
                        $query = "INSERT INTO hrd.dkd_new_real1 (user_tandatangan, user_foto, from_jv, tanggal, karyawanid, jenis, dokterid, notes, saran, jabatanid, tgl_atasan1, tgl_atasan2, atasan3, tgl_atasan3, atasan4, tgl_atasan4)"
                                . " select user_tandatangan, user_foto, '$kodenya' as from_jv, tanggal, '$pjv_gsm' as karyawanid, jenis, dokterid, notes, saran, '$pjbtkry_gsm' as jabatanid, NOW() as tgl_atasan1, NOW() as tgl_atasan2, atasan3, NOW() as tgl_atasan3, atasan4, NOW() as tgl_atasan4 "
                                . " FROM hrd.dkd_new_real1 WHERE tanggal='$ptanggal' AND karyawanid='$pkaryawanid' AND dokterid='$pdokterid' LIMIT 1";
                        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                    }
                    
                    
                    
                    
            }elseif ($ppilihttdfoto=="xxx") {
                
            }
            
            

        }elseif ($act=="dailyupdate") {
            
            
        }
        
        
        
        mysqli_close($cnmy);
        if ($act=="update") {
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
        }else{
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
        }

        exit;
        
        
        exit;
    }elseif ($act=="input" OR $act=="update") {

        $pcardidlog=$_POST['e_idcarduser'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }


        include "../../../config/koneksimysqli.php";

        $pkaryawanid=$_POST['e_idcarduser'];
        $pidjabatan=$_POST['e_idjbt'];
        
        $kodenya=$_POST['e_id'];
        $ptgl=$_POST['e_periode1'];
        $pketid=$_POST['cb_ketid'];//keperluan
        $pcompl=$_POST['e_compl'];
        $paktivitas=$_POST['e_aktivitas'];

        $ptanggal= date("Y-m-d", strtotime($ptgl));
        if (!empty($pcompl)) $pcompl = str_replace("'", " ", $pcompl);
        if (!empty($paktivitas)) $paktivitas = str_replace("'", " ", $paktivitas);

        if (empty($pidjabatan)) {
            if (isset($_SESSION['JABATANID'])) $pidjabatan=$_SESSION['JABATANID'];
        }
        
        $pkdspv=$_POST['e_kdspv'];
        $pkddm=$_POST['e_kddm'];
        $pkdsm=$_POST['e_kdsm'];
        $pkdgsm=$_POST['e_kdgsm'];
    
        
        $pisitglspv=false;
        $pisitgldm=false;
        $pisitglsm=false;
        $pisitglgsm=false;

        //$pkdspv="";$pkddm="";$pkdsm="A";$pkdgsm="A";

        if (empty($pkdspv)) {
            $pisitglspv=true;
            if (empty($pkddm)) {
                $pisitgldm=true;
                if (empty($pkdsm)) {
                    $pisitglsm=true;
                    if (empty($pkdgsm)) {
                        $pisitglgsm=true;
                    }
                }
            }
        }


        //echo "$pkaryawanid, $kodenya, $ptanggal, $pketid, $pcompl, $paktivitas<br/>";

        if ($act=="input") {

            $query = "INSERT INTO hrd.dkd_new_real0 (tanggal, karyawanid, ketid, compl, aktivitas, userid, jabatanid)
                VALUES
                ('$ptanggal', '$pkaryawanid', '$pketid', '$pcompl', '$paktivitas', '$pidcard', '$pidjabatan')";
            mysqli_query($cnmy, $query); 
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $kodenya = mysqli_insert_id($cnmy);

        }elseif ($act=="update") {

            $query = "UPDATE hrd.dkd_new_real0 SET
                tanggal='$ptanggal', karyawanid='$pkaryawanid', 
                ketid='$pketid', compl='$pcompl', aktivitas='$paktivitas', userid='$pidcard', jabatanid='$pidjabatan' WHERE
                idinput='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); 
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

        }

        

        unset($pinsert_data_detail);//kosongkan array
        $psimpandata=false;
        if (isset($_POST['chkbox_br'])) {
            foreach ($_POST['chkbox_br'] as $piddata) {
                if (empty($piddata)) {
                    //continue;
                }
                
                $pjv=$_POST['m_jv'][$piddata];
                $piddokt=$_POST['m_iddokt'][$piddata];
                $pketdokt=$_POST['txt_ketdokt'][$piddata];
                $psaran=$_POST['txt_saran'][$piddata];
                
                if (!empty($pketdokt)) $pketdokt = str_replace("'", " ", $pketdokt);
                if (!empty($psaran)) $psaran = str_replace("'", " ", $psaran);
                
                $pnamajenis="";
                if ($pjv=="Y") $pnamajenis="JV";
                
                //echo "$pjv : $piddokt, $pketdokt<br/>";
                
                $pinsert_data_detail[] = "('$kodenya', '$pnamajenis', '$piddokt', '$pketdokt', '$psaran')";
                $psimpandata=true;
                    
            }
        }

        if ($psimpandata==true) {

            mysqli_query($cnmy, "DELETE FROM hrd.dkd_new_real1 WHERE idinput='$kodenya'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $query_detail="INSERT INTO hrd.dkd_new_real1 (idinput, jenis, dokterid, notes, saran) VALUES ".implode(', ', $pinsert_data_detail);
            mysqli_query($cnmy, $query_detail);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto igagal; exit; }

        }

        
        
        $puserid=$_SESSION['USERID'];
        $now=date("mdYhis");
        $tmp01 =" dbtemp.tmpdkdrlinpt01_".$puserid."_$now ";
        
        $query ="select * from hrd.dkd_new_real1 WHERE idinput='$kodenya'";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto igagal; exit; }
        
        
        $query = "alter table $tmp01 ADD COLUMN dokter_plan varchar(10), ADD COLUMN jenis2 varchar(5)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto igagal; exit; }

        $query = "UPDATE $tmp01 as a JOIN (select distinct a.dokterid From hrd.dkd_new1 as a "
                . " WHERE a.tanggal='$ptanggal' AND a.karyawanid='$pkaryawanid') as b on a.dokterid=b.dokterid SET "
                . " a.dokter_plan=b.dokterid";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto igagal; exit; }
        
        $query = "UPDATE $tmp01 SET jenis='EC' WHERE IFNULL(dokter_plan,'')=''";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto igagal; exit; }

        $query = "UPDATE $tmp01 SET jenis2=jenis";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto igagal; exit; }
        
        $query = "UPDATE hrd.dkd_new_real1 as a JOIN $tmp01 as b on a.idinput=b.idinput AND "
                . " a.dokterid=b.dokterid SET a.jenis=b.jenis2 WHERE a.idinput='$kodenya'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto igagal; exit; }
        
        
        goto iberhasil;
        
        igagal:
            mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
            if ($act=="update") {
            }else{
            
                mysqli_query($cnmy, "DELETE FROM hrd.dkd_new_real1 WHERE idinput='$kodenya'");
                mysqli_query($cnmy, "DELETE FROM hrd.dkd_new_real0 WHERE idinput='$kodenya'");
            
            }
            mysqli_close($cnmy);
            exit;
            
        iberhasil:
            mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
        
        
    
        /*
        
        $query = "UPDATE hrd.dkd_new_real0 SET atasan1='$pkdspv', atasan2='$pkddm', atasan3='$pkdsm', atasan4='$pkdgsm' WHERE idinput='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
        
        if ($pisitglspv==true) {
            $query = "UPDATE hrd.dkd_new_real0 SET tgl_atasan1=NOW() WHERE idinput='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }

        if ($pisitgldm==true) {
            $query = "UPDATE hrd.dkd_new_real0 SET tgl_atasan2=NOW() WHERE idinput='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }

        if ($pisitglsm==true) {
            $query = "UPDATE hrd.dkd_new_real0 SET tgl_atasan3=NOW() WHERE idinput='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }

        if ($pisitglgsm==true) {
            $query = "UPDATE hrd.dkd_new_real0 SET tgl_atasan4=NOW() WHERE idinput='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        */


        mysqli_close($cnmy);
        if ($act=="update") {
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
        }else{
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
        }

        exit;

    }
}
?>