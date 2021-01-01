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
    
    $cabang="";
    $distributor="0000000030";
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    
    
    if ($distributor!="0000000030") {
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
    
    
    
    $totalproduk=0;
    // -----------------------------  insert produk
    $qryproduk="
      SELECT * FROM (
        SELECT DISTINCT '$distributor' distid,`kode barang` brgid,`nama barang` nama,hna FROM $dbname.cpp_import_jkt1

        UNION

        SELECT DISTINCT '$distributor' distid,`kode barang` brgid,`nama barang` nama,hna FROM $dbname.cpp_import_jkt2

        UNION

        SELECT DISTINCT '$distributor' distid,`kode barang` brgid,`nama barang` nama,hna FROM $dbname.cpp_import_sby
      ) PRODUK
    ";
    
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importprodcpp_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importprodcpp_ipms ($qryproduk)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE tmp_importprodcpp_ipms : $erropesan"; exit; }
    
    mysqli_query($cnmy, "DELETE FROM $dbname.tmp_importprodcpp_ipms WHERE CONCAT('$distributor', IFNULL(brgid,'')) IN "
            . " (SELECT CONCAT(IFNULL(distid,''), IFNULL(eprodid,'')) FROM MKT.eproduk WHERE distid='$distributor')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE tmp_importprodcpp_ipms : $erropesan"; exit; }
    
    
    
    $pinsertsave=false;
    $qryproduk="SELECT DISTINCT '$distributor' distid, brgid, nama, hna FROM $dbname.tmp_importprodcpp_ipms";
    
    $tampil_pr= mysqli_query($cnmy, $qryproduk);
    while ($data1= mysqli_fetch_array($tampil_pr)) {
        
        $brgid=$data1['brgid'];
        $namaproduk=mysqli_real_escape_string($cnmy, $data1['nama']);
        $hna=$data1['hna'];

        $pinst_prod_data[] = "('$distributor','$brgid','$namaproduk',$hna,'Y','Y')";
        $pinsertsave=true;
        
        /*
        $cekproduk=mysqli_fetch_array(mysqli_query($cnmy, "SELECT COUNT(eprodid) FROM MKT.eproduk WHERE distid='$distributor' AND eprodid='$brgid'"));
        $cekproduk=$cekproduk[0];
        if ($cekproduk<1){
            mysqli_query($cnmy, "
                INSERT INTO MKT.eproduk(distid,eprodid,nama,hna,aktif,oldflag) 
                VALUES('$distributor','$brgid','$namaproduk',$hna,'Y','Y')
            ");
            echo "berhasil input produk baru -> $namaproduk ~ $brgid ~ $hna <br>";
            $totalproduk=$totalproduk+1;
            
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, "
                    INSERT INTO MKT.eproduk(distid,eprodid,nama,hna,aktif,oldflag) 
                    VALUES('$distributor','$brgid','$namaproduk',$hna,'Y','Y')
                ");
            }
            //END IT
        }
        */
        
    }
    
    if ($pinsertsave == true) {
        
        $query_prod_ins="INSERT INTO MKT.eproduk(distid,eprodid,nama,hna,aktif,oldflag) VALUES "
                . " ".implode(', ', $pinst_prod_data);
        mysqli_query($cnmy, $query_prod_ins);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER eproduk : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_prod_ins);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER eproduk : $erropesan"; exit; }
        }
        //END IT
        
        
    }
    
    echo "Total Produk baru yg berhasil diinput: $totalproduk<br><hr><br>";
    
    
    
    $totalcust=0;

    $qrycust1="
        SELECT DISTINCT 'JKT' cabangid,'$distributor' distid, REPLACE(`kode customer`,'.0','') custid,`nama customer` nama,alamat alamat1,'JAKARTA' kota
        FROM $dbname.cpp_import_jkt1
    ";
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importprodcpp_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importprodcpp_ipms ($qrycust1)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE tmp_importprodcpp_ipms cust jkt 1: $erropesan"; exit; }
    
    mysqli_query($cnmy, "DELETE FROM $dbname.tmp_importprodcpp_ipms WHERE CONCAT('$distributor', IFNULL(custid,'')) IN "
            . " (SELECT CONCAT(IFNULL(distid,''), IFNULL(ecustid,'')) FROM MKT.ecust WHERE distid='$distributor')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE tmp_importprodcpp_ipms cust jkt 1: $erropesan"; exit; }
    
    $pinsertsave=false;
    $qrycust1x="
        SELECT DISTINCT 'JKT' cabangid,'$distributor' distid, custid,  nama, alamat1, 'JAKARTA' kota
        FROM $dbname.tmp_importprodcpp_ipms
    ";
    
    $tampil_cu= mysqli_query($cnmy, $qrycust1x);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        
        $cabangid=$data1['cabangid'];
        $ecust=$data1['custid'];
        $enama=mysqli_real_escape_string($cnmy, $data1['nama']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['alamat1']);
        $kota=mysqli_real_escape_string($cnmy, $data1['kota']);
        
        $pinst_cust_data[] = "('$distributor','$cabangid','$ecust','$enama','$alamat','$kota','Y','Y')";
        $pinsertsave=true;
        
        /*
        // echo $cabang.'~'.$ecust.'~'.$enama.'~'.$alamat.'~'.$kota.'~'.'<br>';
        $cekcust=mysqli_fetch_array(mysqli_query($cnmy, "select count(distid) from MKT.ecust where distid='$distributor' and ecustid='$ecust'"));

        $cekcust1=$cekcust[0];
        if ($cekcust1<1){
            mysqli_query($cnmy, "
                INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif) 
                VALUES('$distributor','$cabangid','$ecust','$enama','$alamat','$kota','Y','Y')
            ");
            echo "berhasil input cust baru -> $cabangid - $ecust - $enama - $alamat <br>";
            $totalcust=$totalcust+1;
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, "
                    INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif) 
                    VALUES('$distributor','$cabangid','$ecust','$enama','$alamat','$kota','Y','Y')
                ");
            }
            //END IT
            
            
        }
        */
    }

    
    if ($pinsertsave == true) {
        
        $query_cust_ins="INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif) VALUES "
                . " ".implode(', ', $pinst_cust_data);
        mysqli_query($cnmy, $query_cust_ins);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER ecust jkt1 : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_cust_ins);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER ecust jkt1 : $erropesan"; exit; }
        }
        //END IT
        
        
    }
    
    
    echo "Total Customer baru JKT yg berhasil diinput: $totalcust<br><hr><br>";
    
    
    
    
    
    // -----------------------------  insert customer jakarta 2
    
    $totalcust2=0;
    
    $qrycust2="
        SELECT DISTINCT 'JKT2' cabangid,'$distributor' distid,REPLACE(`kode customer`,'.0','') custid,`nama customer` nama,alamat alamat1,'JAKARTA' kota
        FROM $dbname.cpp_import_jkt2
    ";
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importprodcpp_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importprodcpp_ipms ($qrycust2)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE tmp_importprodcpp_ipms cust jkt 2: $erropesan"; exit; }
    
    mysqli_query($cnmy, "DELETE FROM $dbname.tmp_importprodcpp_ipms WHERE CONCAT('$distributor', IFNULL(custid,'')) IN "
            . " (SELECT CONCAT(IFNULL(distid,''), IFNULL(ecustid,'')) FROM MKT.ecust WHERE distid='$distributor')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE tmp_importprodcpp_ipms cust jkt 2: $erropesan"; exit; }
    
    
    
    $pinsertsave=false;
    $qrycust2x="
        SELECT DISTINCT 'JKT2' cabangid,'$distributor' distid, custid,  nama, alamat1, 'JAKARTA' kota
        FROM $dbname.tmp_importprodcpp_ipms
    ";
    
    
    $tampil_cu= mysqli_query($cnmy, $qrycust2x);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        
        $cabangid=$data1['cabangid'];
        $ecust=$data1['custid'];
        $enama=mysqli_real_escape_string($cnmy, $data1['nama']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['alamat1']);
        $kota=mysqli_real_escape_string($cnmy, $data1['kota']);

        $pinst_cust_data2[] = "('$distributor','$cabangid','$ecust','$enama','$alamat','$kota','Y','Y')";
        $pinsertsave=true;
        
        
        /*
        $cekcust=mysqli_fetch_array(mysqli_query($cnmy, "select count(distid) from MKT.ecust where distid='$distributor' and ecustid='$ecust'"));
        $cekcust1=$cekcust[0];
        if ($cekcust1<1){
            mysqli_query($cnmy, "
                INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif) 
                VALUES('$distributor','$cabangid','$ecust','$enama','$alamat','$kota','Y','Y')
            ");
            echo "berhasil input cust baru -> $cabang - $ecust - $enama - $alamat <br>";
            $totalcust2=$totalcust+1;
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, "
                    INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif) 
                    VALUES('$distributor','$cabangid','$ecust','$enama','$alamat','$kota','Y','Y')
                ");
            }
            //END IT
        }
        */
        
    }
    
    
    if ($pinsertsave == true) {
        
        $query_cust_ins2="INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif) VALUES "
                . " ".implode(', ', $pinst_cust_data2);
        mysqli_query($cnmy, $query_cust_ins2);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER ecust jkt2 : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_cust_ins2);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER ecust jkt2 : $erropesan"; exit; }
        }
        //END IT
        
        
    }
    
    
    echo "Total Customer baru JKT2 yg berhasil diinput: $totalcust2<br><hr><br>";
    
    
    
    
    // -----------------------------  insert customer surabaya
    $totalcust3=0;
    
    $qrycust3="
        SELECT DISTINCT 'SBY' cabangid,'$distributor' distid,REPLACE(`kode customer`,'.0','') custid,`nama customer` nama,alamat alamat1,'SURABAYA' kota
        FROM $dbname.cpp_import_sby
    ";
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importprodcpp_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importprodcpp_ipms ($qrycust3)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE tmp_importprodcpp_ipms cust SBY: $erropesan"; exit; }
    
    mysqli_query($cnmy, "DELETE FROM $dbname.tmp_importprodcpp_ipms WHERE CONCAT('$distributor', IFNULL(custid,'')) IN "
            . " (SELECT CONCAT(IFNULL(distid,''), IFNULL(ecustid,'')) FROM MKT.ecust WHERE distid='$distributor')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE tmp_importprodcpp_ipms cust SBY: $erropesan"; exit; }
    
    
    
    $pinsertsave=false;
    $qrycust3x="
        SELECT DISTINCT 'SBY' cabangid,'$distributor' distid, custid,  nama, alamat1, 'JAKARTA' kota
        FROM $dbname.tmp_importprodcpp_ipms
    ";
    
    $tampil_cu= mysqli_query($cnmy, $qrycust3x);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        
        $cabangid=$data1['cabangid'];
        $ecust=$data1['custid'];
        $enama=mysqli_real_escape_string($cnmy, $data1['nama']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['alamat1']);
        $kota=mysqli_real_escape_string($cnmy, $data1['kota']);
        
        $pinst_cust_data3[] = "('$distributor','$cabangid','$ecust','$enama','$alamat','$kota','Y','Y')";
        $pinsertsave=true;
        
        
        /*
        // echo $cabang.'~'.$ecust.'~'.$enama.'~'.$alamat.'~'.$kota.'~'.'<br>';
        $cekcust=mysqli_fetch_array(mysqli_query($cnmy, "select count(distid) from MKT.ecust where distid='$distributor' and ecustid='$ecust'"));

        $cekcust1=$cekcust[0];
        if ($cekcust1<1){
            mysqli_query($cnmy, "
                INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif) 
                VALUES('$distributor','$cabangid','$ecust','$enama','$alamat','$kota','Y','Y')
            ");
            echo "berhasil input cust baru -> $cabang - $ecust - $enama - $alamat <br>";
            $totalcust3=$totalcust+1;
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, "
                    INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif) 
                    VALUES('$distributor','$cabangid','$ecust','$enama','$alamat','$kota','Y','Y')
                ");
            }
            //END IT
        }
        */
        
        
    }
    
    if ($pinsertsave == true) {
        
        $query_cust_ins3="INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif) VALUES "
                . " ".implode(', ', $pinst_cust_data3);
        mysqli_query($cnmy, $query_cust_ins3);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER ecust SBY : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_cust_ins3);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER ecust SBY : $erropesan"; exit; }
        }
        //END IT
        
        
    }
    
    echo "Total Customer baru SBY yg berhasil diinput: $totalcust3<br><hr><br>";

    

    // -----------------------------  insert sales
    $totalsalesqty=0;
    $totalsalessum=0;
    mysqli_query($cnmy, "DELETE FROM $dbname.salescpp WHERE left(tgljual,7)='$bulan'");
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.salescpp WHERE left(tgljual,7)='$bulan'");
    }
    //END IT
        
    $totalsalesqty1=0;$totalsalessum1=0;
    
    // ----------------------------------------------------------- insert jakarta 1
    $pinsertsave=false;
    $qrysales="
        SELECT 'JKT' cabangid,REPLACE(`kode customer`,'.0','') custid,`tgl transaksi` tgljual,REPLACE(`kode barang`,'.0','') brgid,REPLACE(`qty barang`,'.0','') qbeli,REPLACE(hna,'.0','') harga,`no transaksi` fakturid,'0' qbonus
        FROM $dbname.cpp_import_jkt1
    ";
    
    $tampil_sl1= mysqli_query($cnmy, $qrysales);
    while ($data1= mysqli_fetch_array($tampil_sl1)) {

        $cabangid=$data1['cabangid'];
        $custid=$data1['custid'];
        $nojual=$data1['fakturid'];
        $brgid=$data1['brgid'];
        $tgljual=$data1['tgljual'];
        $harga=$data1['harga'];
        $qbeli=$data1['qbeli'];
        $qbonus=$data1['qbonus'];
        $totale=$harga*$qbeli;

        if(strpos($nojual,'RC') !== false) {
            $qbeli = '-'.$qbeli;
        }

        $pinst_sls_data1[] = "('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$qbonus','0','0')";
        $pinsertsave=true;
            $totalsalesqty1=$totalsalesqty1+1;
            $totalsalessum1=$totalsalessum1+$totale;
        
        /*
        // echo $cabangid.'~'.$custid.'~'.$nojual.'~'.$brgid.'~'.$tgljual.'~'.$harga.'~'.$qbeli.'~'.$qbonus.'~'.$totale.'<br>';
        $insert=mysqli_query($cnmy, "
            INSERT INTO $dbname.salescpp(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus,custid2,custid3)
            VALUES('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$qbonus','0','0')
        ");

        if ($insert){
            $totalsalesqty1=$totalsalesqty1+1;
            $totalsalessum1=$totalsalessum1+$totale;
        }
        
        
        //IT
        if ($plogit_akses==true) {
            $insertX=mysqli_query($cnit, "
                INSERT INTO $dbname.salescpp(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus,custid2,custid3)
                VALUES('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$qbonus','0','0')
            ");
        }
        //END IT
        */
    }
    
    
    
    if ($pinsertsave == true) {
        
        $query_sls_ins1="INSERT INTO $dbname.salescpp(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus,custid2,custid3) VALUES "
                . " ".implode(', ', $pinst_sls_data1);
        mysqli_query($cnmy, $query_sls_ins1);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER sales jkt1 : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_sls_ins1);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER sales jkt1 : $erropesan"; exit; }
        }
        //END IT
        
        
    }
    
    
    echo "
      Total penjualan Jakarta 1 yg berhasil diinput : $totalsalessum1 , dengan jumlah no faktur sebanyak $totalsalesqty1.<br><hr>
    ";
    
    
    // -----------------------------------------------------------insert jakarta 2
    $totalsalesqty2=0;$totalsalessum2=0;
    $pinsertsave=false;
    $qrysales2="
        SELECT 'JKT2' cabangid,REPLACE(`kode customer`,'.0','') custid,`tgl transaksi` tgljual,REPLACE(`kode barang`,'.0','') brgid,REPLACE(`qty barang`,'.0','') qbeli,REPLACE(hna,'.0','') harga,`no transaksi` fakturid,'0' qbonus
        FROM $dbname.cpp_import_jkt2
    ";

    $tampil_sl2= mysqli_query($cnmy, $qrysales2);
    while ($data2= mysqli_fetch_array($tampil_sl2)) {
        
        $cabangid=$data2['cabangid'];
        $custid=$data2['custid'];
        $nojual=$data2['fakturid'];
        $brgid=$data2['brgid'];
        $tgljual=$data2['tgljual'];
        $harga=$data2['harga'];
        $qbeli=$data2['qbeli'];
        $qbonus=$data2['qbonus'];
        $totale=$harga*$qbeli;

        if( strpos($nojual,'RC') !== false) {
            $qbeli = '-'.$qbeli;
        }
        
        $pinst_sls_data2[] = "('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$qbonus','0','0')";
        $pinsertsave=true;
            $totalsalesqty2=$totalsalesqty2+1;
            $totalsalessum2=$totalsalessum2+$totale;
        
        /*
        // echo $cabangid.'~'.$custid.'~'.$nojual.'~'.$brgid.'~'.$tgljual.'~'.$harga.'~'.$qbeli.'~'.$qbonus.'~'.$totale.'<br>';
        $insert2=mysqli_query($cnmy, "
            INSERT INTO $dbname.salescpp(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus,custid2,custid3)
            VALUES('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$qbonus','0','0')
        ");

        if ($insert2){
            $totalsalesqty2=$totalsalesqty2+1;
            $totalsalessum2=$totalsalessum2+$totale;
        }
        
        //IT
        if ($plogit_akses==true) {
            $insert2X=mysqli_query($cnit, "
                INSERT INTO $dbname.salescpp(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus,custid2,custid3)
                VALUES('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$qbonus','0','0')
            ");
        }
        //END IT
        */
    
    
    }
    
    
    if ($pinsertsave == true) {
        
        $query_sls_ins2="INSERT INTO $dbname.salescpp(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus,custid2,custid3) VALUES "
                . " ".implode(', ', $pinst_sls_data2);
        mysqli_query($cnmy, $query_sls_ins2);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER sales jkt2 : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_sls_ins2);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER sales jkt2 : $erropesan"; exit; }
        }
        //END IT
        
        
    }
    
    
    echo "
      Total penjualan Jakarta 2 yg berhasil diinput : $totalsalessum2 , dengan jumlah no faktur sebanyak $totalsalesqty2.<br>
      <hr>
    ";
    
    
    
    // -----------------------------------------------------------insert surabaya
    $totalsalesqty3=0;$totalsalessum3=0;
    $pinsertsave=false;
    $qrysales3="
        SELECT 'SBY' cabangid,REPLACE(`kode customer`,'.0','') custid,`tgl transaksi` tgljual,REPLACE(`kode barang`,'.0','') brgid,REPLACE(`qty barang`,'.0','') qbeli,REPLACE(hna,'.0','') harga,`no transaksi` fakturid,'0' qbonus
        FROM $dbname.cpp_import_sby
    ";
    
    $tampil_sl3= mysqli_query($cnmy, $qrysales3);
    while ($data3= mysqli_fetch_array($tampil_sl3)) {
        
        $cabangid=$data3['cabangid'];
        $custid=$data3['custid'];
        $nojual=$data3['fakturid'];
        $brgid=$data3['brgid'];
        $tgljual=$data3['tgljual'];
        $harga=$data3['harga'];
        $qbeli=$data3['qbeli'];
        $qbonus=$data3['qbonus'];
        $totale=$harga*$qbeli;

        if( strpos($nojual,'RC') !== false) {
          $qbeli = '-'.$qbeli;
        }

        
        $pinst_sls_data3[] = "('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$qbonus','0','0')";
        $pinsertsave=true;
            $totalsalesqty3=$totalsalesqty3+1;
            $totalsalessum3=$totalsalessum3+$totale;
        
        /*
        // echo $cabangid.'~'.$custid.'~'.$nojual.'~'.$brgid.'~'.$tgljual.'~'.$harga.'~'.$qbeli.'~'.$qbonus.'~'.$totale.'<br>';
        $insert3=mysqli_query($cnmy, "
            INSERT INTO $dbname.salescpp(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus,custid2,custid3)
            VALUES('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$qbonus','0','0')
        ");
        
        if ($insert3){
            $totalsalesqty3=$totalsalesqty3+1;
            $totalsalessum3=$totalsalessum3+$totale;
        }
        
        //IT
        if ($plogit_akses==true) {
            $insert3X=mysqli_query($cnit, "
                INSERT INTO $dbname.salescpp(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus,custid2,custid3)
                VALUES('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$qbonus','0','0')
            ");
        }
        //END IT
        */
    }

    if ($pinsertsave == true) {
        
        $query_sls_ins3="INSERT INTO $dbname.salescpp(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus,custid2,custid3) VALUES "
                . " ".implode(', ', $pinst_sls_data3);
        mysqli_query($cnmy, $query_sls_ins3);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER sales sby : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_sls_ins3);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER sales sby : $erropesan"; exit; }
        }
        //END IT
        
        
    }
    
    echo "
      Total penjualan Surabaya yg berhasil diinput : $totalsalessum3 , dengan jumlah no faktur sebanyak $totalsalesqty3.<br>
      <hr>
    ";
    
    
    
    $query = "SELECT s.tgljual, s.fakturId, s.brgid, s.harga, s.qbeli FROM $dbname.salescpp s 
        JOIN (SELECT * FROM MKT.eproduk WHERE IFNULL(iprodid,'')='' AND distid='$distributor') ep
        ON s.brgid=ep.eprodid
        WHERE LEFT(tgljual,7)='$bulan'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        echo "<div>";
        echo "<h3>Produk yang belum dimapping...</h3>";
        echo "<table width='100%' border='1px'>";
            
            echo "<tr>";
            echo "<th align='center'>No</th>";
            echo "<th align='center'>Tgl. Jual</th>";
            echo "<th align='center'>No. Faktur</th>";
            echo "<th align='center'>Id Brg</th>";
            echo "<th align='center'>Harga</th>";
            echo "<th align='center'>Qty</th>";
            echo "</tr>";
            
            $no=1;
            while($row= mysqli_fetch_array($tampil)) {
                $nvtgljual=$row['tgljual'];
                $nvfakturid=$row['fakturId'];
                $nvbrgid=$row['brgid'];
                $nvharga=$row['harga'];
                $nvqbeli=$row['qbeli'];
                
                echo "<tr>";
                echo "<td align='left'>$no</td>";
                echo "<td align='left'>$nvtgljual</td>";
                echo "<td align='left'>$nvfakturid</td>";
                echo "<td align='left'>$nvbrgid</td>";
                echo "<td align='right'>$nvharga</td>";
                echo "<td align='right'>$nvqbeli</td>";
                echo "</tr>";
                
                $no++;

            }
        echo "</table>";
        echo "</div>";
    }
    
    
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