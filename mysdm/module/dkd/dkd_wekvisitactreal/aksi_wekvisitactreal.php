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

if ($module=='dkdrealisasiact')
{
    if ($act=="hapusdailyact") {
        
        $kodenya=$_GET['ukryid'];
        $ntgl=$_GET['utgl'];
        $doktid=$_GET['udokt'];
        
        
        if (!empty($kodenya) AND !empty($ntgl) AND !empty($doktid)) {
            include "../../../config/koneksimysqli.php";
            
            mysqli_query($cnmy, "DELETE FROM hrd.dkd_new_real0 WHERE karyawanid='$kodenya' AND tanggal='$ntgl' AND ketid='$doktid'");
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
            
            mysqli_query($cnmy, "DELETE FROM hrd.dkd_new_real0 WHERE karyawanid='$kodenya' AND tanggal='$ntgl'");
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
        
        
        

        if ($act=="dailyinput") {

            $query = "INSERT INTO hrd.dkd_new_real0 (tanggal, karyawanid, ketid, compl, aktivitas, userid, jabatanid)
                VALUES
                ('$ptanggal', '$pkaryawanid', '$pketid', '$pcompl', '$paktivitas', '$pidcard', '$pidjabatan')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }


        }elseif ($act=="dailyupdate") {
            
            
        }
        
        
        
        mysqli_close($cnmy);
        if ($act=="update") {
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
        }else{
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
        }

        exit;
        
    }elseif ($act=="input" OR $act=="update") {

    }
}
?>