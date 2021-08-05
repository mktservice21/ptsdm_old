<?php
date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

session_start();

$pkaryawanabsmsk="";
if (isset($_SESSION['IDCARD'])) $pkaryawanabsmsk=$_SESSION['IDCARD'];

if (empty($pkaryawanabsmsk)) {
    echo "Anda Harus Login Ulang... (GAGAL)"; exit;
}

$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="carisudahabsen") {
    
    $pkryjamist="00:00";
    $pkryjammskist="00:00";

    if (isset($_SESSION['J_ISTIRAHAT']))    $pkryjamist=$_SESSION['J_ISTIRAHAT'];
    if (isset($_SESSION['J_MSKISTIRAHAT'])) $pkryjammskist=$_SESSION['J_MSKISTIRAHAT'];
    
    $pkodeabsen=$_POST['ukey'];
    $ptglnwoabsmsk=date("Y-m-d");
    
    include "../../../config/koneksimysqli.php";
    
    
    if ($pkodeabsen=="3") {
        
        $ponclickabsist="";
        
        $query = "select jam FROM hrd.t_absen WHERE karyawanid='$pkaryawanabsmsk' AND tanggal='$ptglnwoabsmsk' AND kode_absen='3'";
        $tampilabsist=mysqli_query($cnmy, $query);
        $irow= mysqli_fetch_array($tampilabsist);
        $pjmabsen_i=$irow['jam'];
        $pjamistabs="<div class='count'>".$pjmabsen_i."</div>";
        if (empty($pjmabsen_i)) {
            $pjamistabs="<div class='count' style='color:#C0C0C0'>$pkryjamist</div>";
            $ponclickabsist=" onclick=\"SimpanAbsensiHome('3'')\" ";
        }
        
        echo "<div class='tile-stats'>";
            echo "<div class='icon'><i class='glyphicon glyphicon-upload'></i></div>";
            echo $pjamistabs;
            echo "<h3><button type='button' class='btn btn-default' id='ibuttonsave' $ponclickabsist >Absen Istirahat</button></h3>";
            echo "<p>&nbsp;</p>";
        echo "</div>";
        
    }else{
        
        $ponclickabsistmsk="";
        
        $query = "select jam FROM hrd.t_absen WHERE karyawanid='$pkaryawanabsmsk' AND tanggal='$ptglnwoabsmsk' AND kode_absen='4'";
        $tampilabsist_msk=mysqli_query($cnmy, $query);
        $irow_m= mysqli_fetch_array($tampilabsist_msk);
        $pjmabsen_im=$irow_m['jam'];
        $pjamistabs_msk="<div class='count'>".$pjmabsen_im."</div>";
        if (empty($pjmabsen_im)) {
            $pjamistabs_msk="<div class='count' style='color:#C0C0C0'>$pkryjammskist</div>";
            $ponclickabsistmsk=" onclick=\"SimpanAbsensiHome('3'')\" ";
        }
        
        echo "<div class='tile-stats'>";
            echo "<div class='icon'><i class='glyphicon glyphicon-download'></i></div>";
            echo $pjamistabs_msk;
            echo "<h3><button type='button' class='btn btn-default' id='ibuttonsave' $ponclickabsistmsk >Selesai Istirahat</button></h3>";
            echo "<p>&nbsp;</p>";
        echo "</div>";
        
    }
    mysqli_close($cnmy);
    
}elseif ($pmodule=="xx") {
    
}
?>