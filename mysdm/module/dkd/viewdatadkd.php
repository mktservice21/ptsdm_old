<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];



if ($pmodule=="cekdatasudahada") {
    include "../../config/koneksimysqli.php";

    $ptgl=$_POST['utgl'];
    $pkaryawanid=$_POST['ukaryawan'];

    $ptanggal= date("Y-m-d", strtotime($ptgl));

    $boleh="boleh";

    $query = "select tanggal from hrd.dkd_new0 where tanggal='$ptanggal' And karyawanid='$pkaryawanid'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $boleh="Tanggal tersebut sudah ada..., silakan piliha tanggal yang lain";
    }

    mysqli_close($cnmy);

    echo $boleh;

}
?>