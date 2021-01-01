<?PHP
session_start();
include "../../config/koneksimysqli.php";
$cnmy=$cnmy;
$dbname = "dbmaster";


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$pbulan=$_POST['uperiode'];
$ptglnya= date("Y-m", strtotime($pbulan));
$pperiode= date("Y-m-d", strtotime($pbulan));

$pharikerja=str_replace(",","", $_POST['uharikerja']);

$berhasil="Tidak ada data yang disimpan";

if ($module=='spgjmlharikerja' AND $act=='input') {
    
    mysqli_query($cnmy, "DELETE FROM $dbname.t_spg_jmlharikerja WHERE DATE_FORMAT(periode,'%Y-%m')='$ptglnya'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    
    $query="INSERT INTO $dbname.t_spg_jmlharikerja (periode, jumlah)VALUES"
            . "('$pperiode', '$pharikerja')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    $berhasil="";
    mysqli_close($cnmy);
}
echo $berhasil;
?>

