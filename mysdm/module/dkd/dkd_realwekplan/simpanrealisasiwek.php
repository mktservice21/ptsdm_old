<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];



if ($pmodule=="simpanrealisasiwekly") {
    include "../../../config/koneksimysqli.php";
    $berhasil="Tidak ada data yang diproses...!!!";
    
    $pcardidlog="";
    if (isset($_SESSION['IDCARD'])) $pcardidlog=$_SESSION['IDCARD'];
    
    if (empty($pcardidlog)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }

    $pskey=$_POST['ukey'];
    $pidinput=$_POST['uidinput'];
    $ptgl=$_POST['utgl'];
    $pdoktid=$_POST['udoktid'];
    $psaran=$_POST['usaran'];

    if (!empty($psaran)) $psaran = str_replace("'", " ", $psaran);

    if ((INT)$pskey==0) {
        $query = "UPDATE hrd.dkd_new0 SET real_user1='$pcardidlog', real_date1=NOW() WHERE idinput='$pidinput' AND tanggal='$ptgl' LIMIT 1";
    }else{
        $query = "UPDATE hrd.dkd_new1 as a JOIN hrd.dkd_new0 as b on a.idinput=b.idinput 
            SET a.real_user='$pcardidlog', a.real_date=NOW(), 
            a.saran='$psaran' WHERE b.idinput='$pidinput' AND b.tanggal='$ptgl' AND 
            a.dokterid='$pdoktid'";
    }

    mysqli_query($cnmy, $query); 
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

    $berhasil="berhasil...";

    mysqli_close($cnmy);

    echo $berhasil;
}

?>