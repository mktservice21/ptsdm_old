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

if ($module=='dkdrealplanbysign')
{
    if ($act=="hapus") {

        
        exit;
    }elseif ($act=="simpandatattdvstpln" OR $act=="simpandatattdvstplnx") {
        
        $pcardidlog=$_POST['e_idcarduser'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        include "../../../config/koneksimysqli.php";
        
        $kodenya=$_POST['e_id'];
        $ptgl=$_POST['e_tanggal'];
        $pkaryawanid=$_POST['e_kryid'];
        $pdoktid=$_POST['e_doktid'];
        $platitude=$_POST['e_latitude'];
        $plongitude=$_POST['e_longitude'];
        
        $pidjabatan=$_POST['e_idjbt'];
        
        if (empty($pidjabatan)) {
            if (isset($_SESSION['JABATANID'])) $pidjabatan=$_SESSION['JABATANID'];
        }
        
        $ptanggal= date("Y-m-d", strtotime($ptgl));
        $pjenis="";
        
        $query = "select dokterid from hrd.dkd_new1 as a WHERE "
                . " a.dokterid='$pdoktid' AND a.tanggal='$ptanggal'";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ((INT)$ketemu<=0) {
            $pjenis="EC";
        }
            
        //echo "$kodenya, $ptanggal : $pkaryawanid, $pdoktid, $platitude, $plongitude, $pidjabatan, $pjenis"; exit;
        
        if ($act=="simpandatattdvstpln") {
            
            $query = "INSERT INTO hrd.dkd_new_real1 (ttd, tanggal, karyawanid, dokterid, l_latitude, l_longitude, jabatanid, jenis) values "
                    . "('Y', '$ptanggal', '$pkaryawanid', '$pdoktid', '$platitude', '$plongitude', '$pidjabatan', '$pjenis')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        }
            
        mysqli_close($cnmy);
        
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
        
    }
}