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
        
        if (empty($pradius)) $pradius="0.10";
        
        if (empty($pkaryawanid) OR empty($platitude) OR empty($plongitude) OR empty($pradius)) {
            $erropesan="ada yang kosong";
            goto errorsimpan;
        }
       
        $query = "INSERT INTO hrd.karyawan_absen (karyawanid, a_latitude, a_longitude, a_radius, userid) VALUES "
                . " ('$pkaryawanid', '$platitude', '$plongitude', '$pradius', '$pidcard')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { goto errorsimpan;  }
        
        
        mysqli_close($cnmy);
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasil');
        exit;
        
        errorsimpan:
            //echo $erropesan;
            mysqli_close($cnmy);
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=error&iderror='.$erropesan);
            exit;
    }
}

?>