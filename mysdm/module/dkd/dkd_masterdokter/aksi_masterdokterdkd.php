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

if ($module=='dkdmasterdokt')
{
    if ($act=="hapus") {


        exit;
    }elseif ($act=="input" OR $act=="update") {

        $pcardidlog=$_POST['e_idcarduser'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }


        include "../../../config/koneksimysqli_ms.php";

        $pkaryawanid=$_POST['e_idcarduser'];
        
        $kodenya=$_POST['e_id'];
        $pcabid=$_POST['cb_cabang'];
        $pgelar=$_POST['cb_gelar'];
        $pprofesi=$_POST['cb_profesi'];
        $pnamadokt=$_POST['e_namadokt'];
        $pspesialis=$_POST['cb_spesial'];
        $pnohp=$_POST['e_nohp'];

        if (!empty($pnamadokt)) $pnamadokt = str_replace("'", " ", $pnamadokt);
        if (!empty($pnohp)) $pnohp = str_replace("'", " ", $pnohp);


        //echo "$kodenya, $pcabid, $pgelar, $pnamadokt, $pspesialis, $pnohp<br/>"; exit;

        if ($act=="input") {

            $query = "INSERT INTO dr.masterdokter (icabangid, profesi, namalengkap, spesialis, nohp)
                VALUES
                ('$pcabid', '$pprofesi', '$pnamadokt', '$pspesialis', '$pnohp')";
            mysqli_query($cnms, $query); 
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnms); exit; }

        }elseif ($act=="update") {
            if (!empty($kodenya)) {
                $query = "UPDATE dr.masterdokter SET
                    icabangid='$pcabid', 
                    namalengkap='$pnamadokt', spesialis='$pspesialis', nohp='$pnohp', profesi='$pprofesi' WHERE
                    id='$kodenya' LIMIT 1";
                mysqli_query($cnms, $query); 
                $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnms); exit; }
            }
        }

        

        mysqli_close($cnms);

        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');

        exit;

    }
}
?>