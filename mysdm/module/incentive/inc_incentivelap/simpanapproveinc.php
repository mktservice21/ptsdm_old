<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

$berhasil="tidak ada data yang diproses...";
if ($pmodule=="simpandataapprove") {
    $pbulan=$_POST['ubulan'];
    $pkryid=$_POST['ukaryawan'];
    $pregion=$_POST['uregion'];

    if (!empty($pbulan) AND !empty($pkryid)) {
        include "../../../config/koneksimysqli_ms.php";

        $query = "INSERT INTO ms.approve_insentif (bulan, karyawanid, `status`)VALUES
            ('$pbulan', '$pkryid', 'approve')";
        mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Tidak ada yang diapprove.... $erropesan"; exit; }

        mysqli_close($cnms);

        $berhasil="berhasil";
    }
}

echo $berhasil;

?>