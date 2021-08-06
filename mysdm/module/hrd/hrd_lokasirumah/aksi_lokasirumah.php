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

$erropesan="ERROR";
if ($module=='hrdlokasirumah')
{
    if ($act=="simpanlokasi") {
        
        include "../../../config/koneksimysqli.php";
        
        $pkaryawanid=$_POST['e_idkaryawan'];
        $platitude=$_POST['e_lat'];
        $plongitude=$_POST['e_long'];
        $pradius=$_POST['e_radius'];
        $pidstatus=$_POST['cb_lokasists'];
        
        if (empty($pradius)) $pradius="0.10";
        if (empty($pidstatus)) $pidstatus="HO1";
        
        if (empty($pkaryawanid) OR empty($platitude) OR empty($plongitude) OR empty($pradius)) {
            $erropesan="ada yang kosong";
            goto errorsimpan;
        }
       
        $query = "INSERT INTO hrd.karyawan_absen (karyawanid, a_latitude, a_longitude, a_radius, userid, id_status, aktif) VALUES "
                . " ('$pkaryawanid', '$platitude', '$plongitude', '$pradius', '$pidcard', '$pidstatus', 'Y')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { goto errorsimpan;  }
        
        
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
    
            $queryabs = "select id_status, a_latitude, a_longitude, a_radius from hrd.karyawan_absen WHERE karyawanid='$pkaryawanid'";
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
        
        
        mysqli_close($cnmy);
        //header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasil');
        header('location:../../../media.php?module=home&act=berhasil');
        exit;
        
        errorsimpan:
            //echo $erropesan;
            mysqli_close($cnmy);
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=error&iderror='.$erropesan);
            exit;
    }
}

?>