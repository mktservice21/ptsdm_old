<?php

    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
session_start();
$puser=$_SESSION['IDCARD'];
if (empty($puser)) {
    echo "ANDA HARUS LOGIN ULANG....!!!";
    exit;
}

    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $start = $time;
    
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $id_cabang="DPS";
    $distributor="0000000002";
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    
    
    //echo "$cabang - $bulan<br>";
    
    if ($distributor!="0000000002" AND $id_cabang!="DPS") {
        echo "tidak ada data yang diproses...";
        exit;
    }
    
    
    //ubah juga di _uploaddata
    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    $dbname = "MKT";
    
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    
    
    if (empty($bulan)){ exit(); }
    echo "Budhi Kurniawan Sejati ~ $bulan ~ $id_cabang<br>";
    
    

    mysqli_query($cnmy, "DELETE FROM $dbname.salesspp WHERE left(tgljual,7)='$bulan' AND subdist = 'BKS'");
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.salesspp WHERE left(tgljual,7)='$bulan' AND subdist = 'BKS'");
    }
    //END IT
    
    
    // customer
    $qry="
      SELECT 'DPS' cabangid,plgkode custid,tanggal tgljual,brkode brgid,harsat harga,jumlah qbeli,0 qbonus,jlfkt2 fakturid,0 dpl,
        brnama namaprod,nama custname,alamat,kota
      FROM $dbname.import_bks
      WHERE LEFT(tanggal,7) = '$bulan'
    ";

    $tampil_cu= mysqli_query($cnmy, $qry);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        
        $kodedist="0000000002";
        $kodecabang=mysqli_real_escape_string($cnmy, $data1['cabangid']);
        $kodeproduk=mysqli_real_escape_string($cnmy, $data1['brgid']);
        $namaproduk=mysqli_real_escape_string($cnmy, $data1['namaprod']);
        $kodepelanggan=mysqli_real_escape_string($cnmy, $data1['custid']);
        $namapelanggan=mysqli_real_escape_string($cnmy, $data1['custname']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['alamat']);
        $nofaktur=$data1['fakturid'];
        $tglfaktur=$data1['tgljual'];
        $qty=$data1['qbeli'];
        $bonus=$data1['qbonus'];
        $harga=$data1['harga'];

        if($harga == '' || $harga == null){
          $harga = 0;
        }

        if($bonus == '' || $bonus == null){
          $bonus = 0;
        }
    
        
        $cekcust0=mysqli_fetch_array(mysqli_query($cnmy, "SELECT COUNT(ecustid) FROM MKT.ecust WHERE distid = '$kodedist' AND cabangid = '$kodecabang' AND ecustid = '$kodepelanggan' AND subdist = 'BKS'"));
        
        $cekcust=$cekcust0[0];
        if ($cekcust==0){

            mysqli_query($cnmy, "
                INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,oldflag,aktif,subdist)
                VALUES('$kodedist','$kodecabang','$kodepelanggan','$namapelanggan','$alamat','Y','Y','BKS')
            ");
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, "
                    INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,oldflag,aktif,subdist)
                    VALUES('$kodedist','$kodecabang','$kodepelanggan','$namapelanggan','$alamat','Y','Y','BKS')
                ");
            }
            //END IT
            
            
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan : Error insert ecust"; exit; }
        }
    
    
        $cekprod0=mysqli_fetch_array(mysqli_query($cnmy, "SELECT COUNT(eprodid) AS total FROM MKT.eproduk WHERE distid = '$kodedist' AND eprodid = '$kodeproduk'"));
        $cekprod=$cekprod0[0];
        if ($cekprod==0){
            mysqli_query($cnmy, "
                INSERT INTO MKT.eproduk(distid,eprodid,nama,hna,oldflag,aktif)
                VALUES('$kodedist','$kodeproduk','$namaproduk','$harga','Y','Y')
            ");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error insert eproduk"; exit; }
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, "
                    INSERT INTO MKT.eproduk(distid,eprodid,nama,hna,oldflag,aktif)
                    VALUES('$kodedist','$kodeproduk','$namaproduk','$harga','Y','Y')
                ");
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "IT... Error insert eproduk"; exit; }
            }
            //END IT
          
        }

        mysqli_query($cnmy, "
            INSERT INTO $dbname.salesspp(cabangid,custid,tgljual,brgid,harga,qbeli,qbonus,fakturid,subdist)
            VALUES('$kodecabang','$kodepelanggan','$tglfaktur','$kodeproduk',$harga,$qty,$bonus,'$nofaktur','BKS')
        ");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error insert salesspp"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, "
                INSERT INTO $dbname.salesspp(cabangid,custid,tgljual,brgid,harga,qbeli,qbonus,fakturid,subdist)
                VALUES('$kodecabang','$kodepelanggan','$tglfaktur','$kodeproduk',$harga,$qty,$bonus,'$nofaktur','BKS')
            ");
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "IT... Error insert salesspp"; exit; }
        }
        //END IT
        
        
    }
    
    
    echo "Selesai Sis/Bro...<br/>";
    
    
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 4);
    
    echo "<br/>Selesai dalam ".$total_time." detik<br/>&nbsp;<br/>&nbsp;";
    
    mysqli_close($cnmy);
    
    //IT
    if ($plogit_akses==true) {
        mysqli_close($cnit);
    }
    
?>