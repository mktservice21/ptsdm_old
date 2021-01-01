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
    
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    
    
    $distributor="0000000002";
    $cabang="UPD";
    $subdist="AKF";

    
    if ($distributor!="0000000002" AND $subdist!="AKF") {
        echo "tidak ada data yang diproses...";
        exit;
    }
    
    echo "$distributor . $cabang . $ptgl<br/>";
    
    
    //ubah juga di _uploaddata
    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    $dbname = "MKT";
    
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    $totalcust=0;
    
    //call procedur
        $eksekusi=mysqli_query($cnmy, "CALL $dbname.cursor_ecust()");
        
        
    //IT
    if ($plogit_akses==true) {
        $eksekusi2=mysqli_query($cnit, "CALL $dbname.cursor_ecust()");
    }
    
    
    

    // sales
    $totalsalesqty=0;
    $totalsalessum=0;
    
    mysqli_query($cnmy, "delete from $dbname.salesspp where left(tgljual,7)='$bulan' and cabangid='$cabang' and subdist='$subdist' ");
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "delete from $dbname.salesspp where left(tgljual,7)='$bulan' and cabangid='$cabang' and subdist='$subdist' ");
    }
    //END IT
    
    
    $qrysales="SELECT * FROM $dbname.mssales";
    $tampil_sl= mysqli_query($cnmy, $qrysales);
    while ($data1= mysqli_fetch_array($tampil_sl)) {
        $custid=$data1['CUSTID'];
        $nojual=$data1['NOJUAL'];
        $brgid=$data1['BRGID'];
        $tgljual=$data1['TGLJUAL'];
        $harga0=mysqli_fetch_array(mysqli_query($cnmy, "SELECT i.`hna` FROM MKT.eproduk e INNER JOIN MKT.iproduk i ON e.`iProdId` = i.`iProdId` WHERE e.`eProdId` = '$brgid'  and  e.distid='0000000002'"));
        $harga=$harga0[0];
        $qbeli=$data1['QBELI'];
        $totale=$harga*$qbeli;
        if ($distributor=="0000000002"){
            $tabel="$dbname.salesspp";
        }
        
        //echo "$custid, $nojual<br/>";
        //$tahun=substr($nojual,0,4);
        //$bulan=substr($nojual,4,2);
        //$tgl=substr($nojual,6,2);

        //$tanggaljual=$tahun."-".$bulan."-".$tgl;
        
        
        $insert=mysqli_query($cnmy, "insert into $tabel(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,subdist) values('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$subdist')");
        if ($insert) {
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
        }
        
        //IT
        if ($plogit_akses==true) {
            $insert2=mysqli_query($cnit, "insert into $tabel(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,subdist) values('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$subdist')");
        }
        
    }


    echo "Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>sekian dan terimakasih";
    
    mysqli_query($cnmy, "drop table $dbname.mssales");
    mysqli_query($cnmy, "drop table $dbname.msbar");
    mysqli_query($cnmy, "truncate table $dbname.subdist_mscust_akf");

    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "drop table $dbname.mssales");
        mysqli_query($cnit, "drop table $dbname.msbar");
        mysqli_query($cnit, "truncate table $dbname.subdist_mscust_akf");
    }

    
    
    
    mysqli_close($cnmy);
    
    //IT
    if ($plogit_akses==true) {
        mysqli_close($cnit);
    }
    
    
    
?>
