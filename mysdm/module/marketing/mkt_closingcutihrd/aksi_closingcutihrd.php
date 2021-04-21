<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }

    $pkaryawanid=$_SESSION['IDCARD'];
    
    $ptahun=$_POST['utahun'];
    $pketerangan=$_POST['uketapv'];
    
    $_SESSION['CLSCUTITHN']=$ptahun;
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    include "../../../config/koneksimysqli.php";
    
    if ($pketerangan=="hapusprosescuti") {
        
        $query = "DELETE FROM hrd.karyawan_cuti_close WHERE tahun='$ptahun'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        echo "data berhasil dihapus"; goto berhasil;
        
    }elseif ($pketerangan=="prosesdatacuti") {
    
        $query = "CALL hrd.proses_cuti_tahunan('$ptahun')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query = "UPDATE hrd.karyawan_cuti_close SET userid='$pkaryawanid' WHERE tahun='$ptahun'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        echo "data berhasil diproses"; goto berhasil;
    }else{
        echo "tidak ada aktivitas..."; goto berhasil;
    }
?>


<?PHP
hapusdata:
    mysqli_close($cnmy);
    
    exit;
berhasil:
    mysqli_close($cnmy);

?>