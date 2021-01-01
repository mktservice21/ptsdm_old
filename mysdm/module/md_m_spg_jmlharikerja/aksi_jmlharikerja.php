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
$pharikerja_aspr=str_replace(",","", $_POST['uhkaspr']);
if (empty($pharikerja)) $pharikerja=0;
if (empty($pharikerja_aspr)) $pharikerja_aspr=0;

$berhasil="Tidak ada data yang disimpan";

if ($module=='spgjmlharikerja' AND $act=='input') {
    /*
    mysqli_query($cnmy, "DELETE FROM $dbname.t_spg_jmlharikerja WHERE DATE_FORMAT(periode,'%Y-%m')='$ptglnya'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    
    $query="INSERT INTO $dbname.t_spg_jmlharikerja (periode, jumlah, jml_aspr)VALUES"
            . "('$pperiode', '$pharikerja', '$pharikerja_aspr')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    */
    
    $sql = "select periode from $dbname.t_spg_jmlharikerja WHERE DATE_FORMAT(periode,'%Y-%m')='$ptglnya'";
    $tampil= mysqli_query($cnmy, $sql);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu==0) {
        $query="INSERT INTO $dbname.t_spg_jmlharikerja (periode)VALUES('$pperiode')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
    }
    
    $query="UPDATE $dbname.t_spg_jmlharikerja SET jumlah='$pharikerja', jml_aspr='$pharikerja_aspr' WHERE DATE_FORMAT(periode,'%Y-%m')='$ptglnya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }    
    
    
    $berhasil="";
    mysqli_close($cnmy);
}
echo $berhasil;
?>

