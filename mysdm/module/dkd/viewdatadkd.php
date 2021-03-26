<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];



if ($pmodule=="cekdatasudahada") {
    include "../../config/koneksimysqli.php";

    $pidinput=$_POST['uid'];
    $ptgl=$_POST['utgl'];
    $pkaryawanid=$_POST['ukaryawan'];

    $ptanggal= date("Y-m-d", strtotime($ptgl));

    $boleh="boleh";

    $query = "select tanggal from hrd.dkd_new0 where idinput<>'$pidinput' AND tanggal='$ptanggal' And karyawanid='$pkaryawanid'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $boleh="Tanggal tersebut sudah ada..., silakan piliha tanggal yang lain";
    }

    mysqli_close($cnmy);

    echo $boleh;

}elseif ($pmodule=="viewdatadoktercabang") {
    include "../../config/koneksimysqli.php";
    $pidcab=$_POST['uidcab'];
    $pkodeid=$_POST['skode'];
    $pcabpilih=$_POST['ukdcab'];
    $pdoktpilih=$_POST['ukddokt'];

    $pkodecabang=$pidcab;
    if ((INT)$pkodeid==2) $pkodecabang=$pcabpilih;

    $query = "select `id` as iddokter, namalengkap, gelar, spesialis from dr.masterdokter WHERE 1=1 ";
    $query .=" AND icabangid='$pkodecabang' ";
    $query .=" order by namalengkap, `id`";
    $tampilket= mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampilket);
    if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";
    while ($du= mysqli_fetch_array($tampilket)) {
        $niddokt=$du['iddokter'];
        $nnmdokt=$du['namalengkap'];
        $ngelar=$du['gelar'];
        $nspesial=$du['spesialis'];
        if ($niddokt==$pdoktpilih)
            echo "<option value='$niddokt' selected>$nnmdokt ($ngelar), $nspesial</option>";
        else
            echo "<option value='$niddokt'>$nnmdokt ($ngelar), $nspesial</option>";

    }
    mysqli_close($cnmy);
}
?>