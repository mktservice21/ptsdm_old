<?php

    ini_set("memory_limit","512M");
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
    $pakhirbulan =  date("Y-m-t", strtotime($ptgl));
    
    
    $distributor="0000000002";
    $cabang="UPD";
    $subdist="AKF";

    
    if ($distributor!="0000000002" AND $subdist!="AKF") {
        echo "tidak ada data yang diproses...";
        exit;
    }
    
    echo "$distributor . $cabang . $ptgl<br/>";
    
    
    //ubah juga di _uploaddata
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    $plogit_akses=true;
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

    
    
    $query = "SELECT s.tgljual, s.fakturId, s.brgid, s.harga, s.qbeli FROM $dbname.salesspp s 
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


<?php
$data = [
    "api_key" => "kKCrFZZwwgQCiP4KeUis",
    "distid" => "$distributor",
    "date" => "$pakhirbulan",
    "subdist" => "$subdist"
  ];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://ms2.marvis.id/api/sales");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
  ));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  $response = curl_exec($ch);
  $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  
  if (empty($httpcode)) $httpcode=0;
  if ((INT)$httpcode==201) {
      echo "<br/>Berhasil insert elastic...";
  }else{
      echo "<br/>Gagal insert elastic...";
  }
?>