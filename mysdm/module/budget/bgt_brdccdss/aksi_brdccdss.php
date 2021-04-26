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

$pmodule=$_GET['module'];
$pact=$_GET['act'];
$pidmenu=$_GET['idmenu'];

include "../../../config/koneksimysqli.php";
include "../../../config/fungsi_sql.php";

if ($pmodule=='bgtinputbrdccdss')
{
    if ($pact=="hapus") {

        $kodenya=$_GET['id'];
        $pnodivisi=$_GET['unodivisi'];
        $pkethapus=$_GET['kethapus'];
        if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);
        
        
        $ncarisudahclosebrid=CariSudahClosingBRID3($kodenya, "A");
        
        if ($ncarisudahclosebrid==true) {
            echo "<span style='color:red;'>BR tersebut sudah closing SURABAYA tidak bisa dihapus....</span>";
            mysqli_close($cnmy);
            exit;
        }
        
        if (!empty($pnodivisi)) {
            mysqli_query($cnmy, "UPDATE hrd.br0 SET batal='Y', alasan_b='$pkethapus' WHERE brId='$kodenya' LIMIT 1");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }else{
            
            $sql = "insert into dbmaster.backup_br0 
                   SELECT * FROM hrd.br0 WHERE brId='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $sql);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            $sql = "UPDATE dbmaster.backup_br0 SET alasan_b='$pkethapus' WHERE brId='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $sql);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            $sql = "insert into hrd.br0_reject(brId, KET, IDREJECT, TGLREJECT)values"
                    . "('$kodenya', '$pkethapus', '$pidcard', NOW())";
            mysqli_query($cnmy, $sql);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            //delete
            mysqli_query($cnmy, "DELETE FROM hrd.br0 WHERE brId='$kodenya' LIMIT 1");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        }

        mysqli_close($cnmy);
        
        //header('location:../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&act='.$pact);
        
        exit;
    }elseif ($pact=="input" OR $pact=="update") {
        
        
        
        
        mysqli_close($cnmy);
        
        exit;
    }
}
mysqli_close($cnmy);
?>