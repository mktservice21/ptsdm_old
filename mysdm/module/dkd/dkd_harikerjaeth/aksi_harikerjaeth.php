<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];



if ($pmodule=="simpandatahketh") {
    include "../../../config/koneksimysqli.php";
    $berhasil="Tidak ada data yang diproses...!!!";
    
    $pcardidlog="";
    if (isset($_SESSION['IDCARD'])) $pcardidlog=$_SESSION['IDCARD'];
    
    if (empty($pcardidlog)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }

    $ptahun=$_POST['utahun'];
    $pbulan=$_POST['ubulan'];
    $pjml=$_POST['ujml'];

    $pjml=str_replace(",","", $pjml);
    if (empty($pjml)) $pjml=0;
    
    $kodenya="";

    $query = "SELECT * FROM hrd.hrkrj WHERE left(periode1,7)='$ptahun-$pbulan'";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ((INT)$ketemu<=0) {
        $query = "SELECT max(hrkrjid) as nourut FROM hrd.hrkrj";
        $tampil2=mysqli_query($cnmy, $query);
        $ketemu2=mysqli_num_rows($tampil2);
        if ((INT)$ketemu2>0) {
            $nrow=mysqli_fetch_array($tampil2);
            $pnourut=$nrow['nourut'];
            if (empty($pnourut)) $pnourut=1;
        }else{
            $pnourut=1;
        }
        if ((INT)$pnourut==0) $pnourut=1;
        $pnourut++;

        $awal=10;
        $jml=  strlen($pnourut);
        $awal=$awal-$jml;
        $kodenya=str_repeat("0", $awal).$pnourut;

        $pperiode=$ptahun."-".$pbulan."-01";

        $query = "INSERT INTO hrd.hrkrj (hrkrjid, periode1, jumlah) VALUES 
            ('$kodenya', '$pperiode', '$pjml')";
    }else{
        $query = "UPDATE hrd.hrkrj SET jumlah='$pjml' WHERE left(periode1,7)='$ptahun-$pbulan' LIMIT 1";
    }

    mysqli_query($cnmy, $query); 
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

    $berhasil="berhasil...";

    mysqli_close($cnmy);

    echo $berhasil;

}


?>