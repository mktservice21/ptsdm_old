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
    
    $cabang = '01';
    $subdist = 'CPM';
    
    $distributor="0000000006";
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    $pakhirbulan =  date("Y-m-t", strtotime($ptgl));
    
    
    if ($distributor!="0000000006") {
        echo "tidak ada data yang diproses...";
        exit;
    }
    
    
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
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, "
                insert into MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif,subdist) 
                values('$distributor','$cabang','$ecust','$enama','$alamat','$kota','Y','Y','$subdist')
            ");
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error ecust : $erropesan"; exit; }
        }
        //END IT
        
    }
    
    echo "Total Customer baru yg berhasil diinput: $totalcust<br><hr><br>";
    
    
    // // sales
    $totalsalesqty=0;
    $totalsalessum=0;
    
    mysqli_query($cnmy, "delete from $dbname.salescp1 where left(tgljual,7)='$bulan' and cabangid='01'");
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "delete from $dbname.salescp1 where left(tgljual,7)='$bulan' and cabangid='01'");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error delete salescp1 : $erropesan"; exit; }
    }
    //END IT
    
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
        
        //IT
        if ($plogit_akses==true) {
            $insert2=mysqli_query($cnit, "
                insert into $dbname.salescp1(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid) 
                values('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual')
            ");
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error insert salescp1 : $erropesan"; exit; }
        }
        //END IT
        
    }
    

    echo "Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>sekian dan terimakasih<br>
      <hr>";

    mysqli_query($cnmy, "DELETE FROM $dbname.import_cpm WHERE LEFT(STR_TO_DATE(tanggal,'%d-%b-%Y'),7) = '$bulan'");

        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, "DELETE FROM $dbname.import_cpm WHERE LEFT(STR_TO_DATE(tanggal,'%d-%b-%Y'),7) = '$bulan'");
        }
        //END IT


        
    $query = "SELECT s.tgljual, s.fakturId, s.brgid, s.harga, s.qbeli FROM $dbname.salescp1 s 
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

<?php
$data = [
    "api_key" => "kKCrFZZwwgQCiP4KeUis",
    "distid" => "$distributor",
    "date" => "$pakhirbulan",
    "subdist" => ""
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