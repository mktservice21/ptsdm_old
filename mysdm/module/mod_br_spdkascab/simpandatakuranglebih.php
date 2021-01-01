<?php


$berhasil="Tidak ada data yang disimpan...";

include "../../config/koneksimysqli.php";

$pidinputspd=$_POST['uidspd'];
$pidbr=$_POST['uidbr'];
$pjmlrp=$_POST['ujml'];
$pket=str_replace("'","", $_POST['uketerangan']);

if (empty($pjmlrp)) $pjmlrp=0;
$pjmlrp=str_replace(",","", $pjmlrp);

if (!empty($pidinputspd) AND !empty($pidbr)) {
    
    $sudhainput=false;
    $query = "select idnoauto from hrd.kas_kuranglebih WHERE idinput='$pidinputspd' AND kasid='$pidbr'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $row= mysqli_fetch_array($tampil);
        $pnoidauto=$row['idnoauto'];
        if (!empty($pnoidauto)) {
            $query = "UPDATE hrd.kas_kuranglebih SET kuranglebihrp='$pjmlrp', ket='$pket' WHERE idinput='$pidinputspd' AND kasid='$pidbr' AND idnoauto='$pnoidauto' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $sudhainput=true;
            $berhasil="berhasil update...";
        }
    }
    
    if ($sudhainput==false) {
        $query = "INSERT INTO hrd.kas_kuranglebih (idinput, kasid, ket, kuranglebihrp)VALUES('$pidinputspd', '$pidbr', '$pket', '$pjmlrp')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $berhasil="berhasil simpan...";
    }
    
    
}

mysqli_close($cnmy);
echo $berhasil;
?>
