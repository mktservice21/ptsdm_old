<?PHP
    session_start();
    
    $module=$_GET['module'];
    $notlert="Tidak ada data yang disimpan";
    if ($module=="simpandatanyaperiode") {
        include "../../config/koneksimysqli.php";
        $cnmy=$cnmy;
        $pidca=$_POST['uidca'];
        $pperiode=$_POST['uperiode'];
        
        if (!empty($pidca) AND !empty($pperiode)) {
            $pperiode =  date("Y-m-01", strtotime($pperiode));
            
            
            //$notlert="$pidca, $pperiode";
            
            $query = "UPDATE dbmaster.t_ca0 SET periode='$pperiode', bulan='$pperiode' WHERE idca='$pidca'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
            $notlert="data berhasil diupdate";
        }
        
        mysqli_close($cnmy);
    }
    echo $notlert;
?>

