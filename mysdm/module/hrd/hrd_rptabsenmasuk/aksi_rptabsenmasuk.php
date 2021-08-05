<?php
    session_start();

    //ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $pidgroup=$_SESSION['GROUP'];
    
    include "../../../config/koneksimysqli.php";
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmprptabsmasukimg01_".$userid."_$now ";
    $tmp02 =" dbtemp.tmprptabsmasukimg02_".$userid."_$now ";
    $tmp03 =" dbtemp.tmprptabsmasukimg03_".$userid."_$now ";
    
    
    $ppilihsts = strtoupper($_POST['eket']);
    $mytgl1 = $_POST['uperiode1'];
    $pkaryawanid = $_POST['ukaryawan'];
    $pstsapv = $_POST['uketapv'];
    
    $pbulan1= date("Y-m-01", strtotime($mytgl1));
    $pbulan2= date("Y-m-t", strtotime($mytgl1));
    
    
    $query = "select a.idabsen, a.karyawanid, b.nama as nama_karyawan, a.kode_absen, a.tanggal, a.jam, a.l_latitude, a.l_longitude "
            . " FROM hrd.t_absen as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId "
            . " WHERE a.kode_absen='$pstsapv' AND "
            . " a.tanggal BETWEEN '$pbulan1' AND '$pbulan2' ";
    if (!empty($pkaryawanid)) {
        $query .=" AND a.karyawanid='$pkaryawanid' ";
    }
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select distinct a.idabsen, a.kode_absen, a.nama FROM dbimages2.img_absen as a "
            . " JOIN $tmp01 as b on a.idabsen=b.idabsen AND a.kode_absen=b.kode_absen "
            . " WHERE a.kode_absen='$pstsapv'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp01 ADD COLUMN nama_images VARCHAR(200)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN $tmp02 as b on a.idabsen=b.idabsen AND a.kode_absen=b.kode_absen "
            . " SET a.nama_images=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    echo "<div class='row'>";
    
        echo "<div class='col-md-12 col-sm-12 col-xs-12'>";
            echo "<div class='x_panel'>";
    
                echo "<div class='title_left' style='color:blue;'><h3>";
                if ($pstsapv=="1") {
                    echo "ABSEN MASUK";
                }elseif ($pstsapv=="2") {
                    echo "ABSEN PULANG";
                }
                echo "</h3></div>";
        
            echo "</div>";
        echo "</div>";
        
    echo "</div>";    
    
    echo "<div class=''>";
        
        $query = "select * from $tmp01 ORDER BY tanggal, nama_karyawan, karyawanid";
        $tampil=mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $nnmkaryawan=$row['nama_karyawan'];
            $ntgl=$row['tanggal'];
            $njam=$row['jam'];
            $nnamaimg=$row['nama_images'];
            $nlat=$row['l_latitude'];
            $nlong=$row['l_longitude'];
            
            $ntanggal= date("d/m/Y", strtotime($ntgl));
            
            $folderfotofileabs="images/foto_absen/".$nnamaimg;
            
            $pnamafiles_img="kosong";
            if (!file_exists($folderfotofileabs)) {
                $pnamafiles_img="<img src='$folderfotofileabs' width='70px' height='75px' />";
            }
            
            echo "<div class='animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12'>";
                echo "<div class='tile-stats'>";
                    echo "<div class='icon'>";
                        //echo "<i class='glyphicon glyphicon-check'></i>";
                        echo "$pnamafiles_img";
                    echo "</div>";

                    echo "<div class='count'>$njam</div>";

                    echo "<h3>";
                        echo "<a href='#' class='btn btn-default' onclick=\"initMap('$nlat', '$nlong', '$nnmkaryawan');\">$ntanggal</a>";
                    echo "<h3>";
                    echo "<p>$nnmkaryawan</p>";
                echo "</div>";
            echo "</div>";
        
        }
    
    echo "</div>";
?>


<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp03");
    
    mysqli_close($cnmy);
?>