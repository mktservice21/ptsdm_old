<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
if ($_GET['module']=="cari_cardid") {
    $ssql="SELECT MAX(Right(CARDID,4))+1 AS kodenya From t_employee";
    $result=  mysqli_query($cnmy, $ssql);
    $found=mysqli_num_rows($result);
    $kde_nya="00001";
    if ($found>0){
        $ci=  mysqli_fetch_array($result);
        $kodenya=$ci['kodenya'];
        $jml=  strlen($kodenya);
        $awal=4-$jml;
        $kde_nya="0".str_repeat("0", $awal).$kodenya;
    }else{
        $kde_nya= $_POST['udata']."0001";
    }
    echo "$kde_nya";
}
?>