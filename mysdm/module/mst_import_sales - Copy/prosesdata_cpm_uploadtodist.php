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
    
    $cabang = '01';
    $subdist = 'CPM';
    
    $distributor="0000000006";
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    
    
    if ($distributor!="0000000006") {
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
    
    
    
    
    $totalcust=0;
    // // customer
    $qrycust="
        SELECT DISTINCT plgkode custid,nama custnm,alamat,kota
        FROM $dbname.import_cpm WHERE plgkode NOT IN (SELECT DISTINCT ecustid FROM MKT.ecust WHERE distid = '$distributor')";
    
    $tampil_cu= mysqli_query($cnmy, $qrycust);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        
        $ecust=$data1['custid'];
        $enama=mysqli_real_escape_string($cnmy, $data1['custnm']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['alamat']);
        $kota=mysqli_real_escape_string($cnmy, $data1['kota']);
        
        mysqli_query($cnmy, "
            insert into MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif,subdist) 
            values('$distributor','$cabang','$ecust','$enama','$alamat','$kota','Y','Y','$subdist')
        ");
        
        echo "berhasil input cust baru -> $cabang - $ecust - $enama - $alamat <br>";
        $totalcust=$totalcust+1;
        
    }
    
    echo "Total Customer baru yg berhasil diinput: $totalcust<br><hr><br>";
    
    
    // // sales
    $totalsalesqty=0;
    $totalsalessum=0;
    
    mysqli_query($cnmy, "delete from $dbname.salescp1 where left(tgljual,7)='$bulan' and cabangid='01'");
    
    
    $qrysales="
        SELECT '01' cabangid,plgkode custid,STR_TO_DATE(tanggal,'%d-%b-%Y') tgljual,jlfkt2 fakturid,brkode brgid,jumlah qbeli,harsat harga
        FROM $dbname.import_cpm
        WHERE LEFT(STR_TO_DATE(tanggal,'%d-%b-%Y'),7) = '$bulan'
        ";
    
    $tampil_sl= mysqli_query($cnmy, $qrysales);
    while ($data1= mysqli_fetch_array($tampil_sl)) {
        
        $cabangid=$data1['cabangid'];
        $custid=$data1['custid'];
        $nojual=$data1['fakturid'];
        $brgid=$data1['brgid'];
        $tgljual=$data1['tgljual'];
        $harga0=mysqli_fetch_array(mysqli_query($cnmy, "
                SELECT i.`hna` 
                FROM MKT.eproduk e 
                  INNER JOIN MKT.iproduk i ON e.`iProdId` = i.`iProdId` 
                WHERE e.`eProdId` = '$brgid' and  e.distid='0000000006'
            "));
      
        $harga=$harga0[0];
        $qbeli=$data1['qbeli'];
        $totale=$harga*$qbeli;
      
        $insert=mysqli_query($cnmy, "
            insert into $dbname.salescp1(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid) 
            values('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual')
        ");

        if ($insert){
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
        }
        
    }
    

    echo "Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>sekian dan terimakasih<br>
      <hr>";

    mysqli_query($cnmy, "DELETE FROM $dbname.import_cpm WHERE LEFT(STR_TO_DATE(tanggal,'%d-%b-%Y'),7) = '$bulan'");



    
    
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