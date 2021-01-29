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
    
    $subdist="";
    $cabang='SBY';
    $distributor="0000000025";
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    $pakhirbulan =  date("Y-m-t", strtotime($ptgl));
    
    
    if ($distributor!="0000000025") {
        echo "tidak ada data yang diproses...";
        exit;
    }
    
    //ubah juga di _uploaddata
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    

    echo "$distributor ~ $cabang ~ $bulan<br><br>";

    $totalproduk=0;
    // customer
    $qryproduk="
        SELECT DISTINCT 
        kode AS BRGID,`Nama Barang` AS NAMA,Satuan AS satuan,harga as hna
        FROM $dbname.import_mulyaraya 
        WHERE left(tanggal,7) = '$bulan'
    ";
    
    $tampil_pr= mysqli_query($cnmy, $qryproduk);
    while ($data1= mysqli_fetch_array($tampil_pr)) {
        
        $brgid=$data1['BRGID'];
        $namaproduk=mysqli_real_escape_string($cnmy, $data1['NAMA']);
        $satuan=mysqli_real_escape_string($cnmy, $data1['satuan']);
        $hna=$data1['hna'];
        $cekproduk=mysqli_fetch_array(mysqli_query($cnmy, "SELECT COUNT(eprodid) FROM MKT.eproduk WHERE distid='$distributor' AND eprodid='$brgid'"));
        $cekproduk=$cekproduk[0];
        
        if ($cekproduk<1){
            mysqli_query($cnmy, "
                INSERT INTO MKT.eproduk(distid,eprodid,nama,satuan,hna,aktif,oldflag) 
                    VALUES('$distributor','$brgid','$namaproduk','$satuan',$hna,'Y','Y')
            ");
            echo "berhasil input produk baru -> $namaproduk - $brgid - $hna <br>";
            $totalproduk=$totalproduk+1;
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, "
                    INSERT INTO MKT.eproduk(distid,eprodid,nama,satuan,hna,aktif,oldflag) 
                        VALUES('$distributor','$brgid','$namaproduk','$satuan',$hna,'Y','Y')
                ");
            }
            //END IT
            
        }
        
    }    


    echo "Total produk baru yg berhasil diinput: $totalproduk<br><hr><br>";
    
    
    $totalcust=0;
    // customer
    $qrycust="
        SELECT DISTINCT CASE WHEN cust LIKE '%.0' THEN LEFT(cust,4) ELSE cust END CUSTID,`nama customer` CUSTNM 
        FROM $dbname.import_mulyaraya
        WHERE left(tanggal,7) = '$bulan'
        ";
    
    $tampil_cu= mysqli_query($cnmy, $qrycust);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        
        $ecust=$data1['CUSTID'];
        $enama=mysqli_real_escape_string($cnmy, $data1['CUSTNM']);
        $cekcust=mysqli_fetch_array(mysqli_query($cnmy, "
        select count(distid) from MKT.ecust where distid='$distributor' and cabangid='$cabang' and ecustid='$ecust' and ecustid NOT LIKE '%.0' and nama='$enama'"));
        $cekcust1=$cekcust[0];
        if ($cekcust1<1){
            mysqli_query($cnmy, "
                insert into MKT.ecust(distid,cabangid,ecustid,nama,oldflag,aktif,subdist) 
                values('$distributor','$cabang','$ecust','$enama','Y','Y','$subdist')
                ");
            echo "berhasil input cust baru -> $cabang - $ecust - $enama<br>";
            $totalcust=$totalcust+1;
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, "
                    insert into MKT.ecust(distid,cabangid,ecustid,nama,oldflag,aktif,subdist) 
                    values('$distributor','$cabang','$ecust','$enama','Y','Y','$subdist')
                    ");
            }
            //END IT
            
        }
          
    }

    echo "Total Customer baru yg berhasil diinput: $totalcust<br><hr><br>";

    
    // sales
    $totalsalesqty=0;
    $totalsalessum=0;
    mysqli_query($cnmy, "DELETE FROM $dbname.salesmps WHERE left(tgljual,7)='$bulan' AND cabangid='$cabang'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error delete salesmps : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.salesmps WHERE left(tgljual,7)='$bulan' AND cabangid='$cabang'");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error delete salesmps : $erropesan"; exit; }
    }
    //END IT
    
    $qrysales="
        SELECT RIGHT(cust,4) CUSTID,tanggal TGLJUAL,`no.faktur` NOJUAL,kode BRGID,kwantum QBELI,harga HARGA0
        FROM $dbname.import_mulyaraya 
        WHERE left(tanggal,7) = '$bulan'
    ";
    $tampil_sl= mysqli_query($cnmy, $qrysales);
    while ($data1= mysqli_fetch_array($tampil_sl)) {
        
        $custid=$data1['CUSTID'];
        $nojual=$data1['NOJUAL'];
        $brgid=$data1['BRGID'];
        $tgljual=$data1['TGLJUAL'];
        $harga=$data1['HARGA0'];
        $qbeli=$data1['QBELI'];
        $totale=$harga*$qbeli;
        $tabel="$dbname.salesmps";

        //$tahun=substr($nojual,0,4);
        //$bulan=substr($nojual,4,2);
        //$tgl=substr($nojual,6,2);

        //$tanggaljual=$tahun."-".$bulan."-".$tgl;
        $insert=mysqli_query($cnmy, "
            insert into $tabel(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid) 
            values('$cabang',LEFT('$custid',4),'$tgljual','$brgid','$harga','$qbeli','$nojual')
        ");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER $tabel : $erropesan"; exit; }

        if ($insert){
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
        }
        
        //IT
        if ($plogit_akses==true) {
            $insert=mysqli_query($cnit, "
                insert into $tabel(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid) 
                values('$cabang',LEFT('$custid',4),'$tgljual','$brgid','$harga','$qbeli','$nojual')
            ");
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER $tabel : $erropesan"; exit; }
        }
        //END IT
        
        
    }
    
    
    
    echo "Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>Sekian dan terimakasih<br>
    <hr>";
    // mysqli_query($cnmy, "drop table combieth");

    
    mysqli_query($cnmy, "DELETE FROM $dbname.import_mulyaraya");
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.import_mulyaraya");
    }
    //END IT
    
    
    
    
    $query = "SELECT s.tgljual, s.fakturId, s.brgid, s.harga, s.qbeli FROM $dbname.salesmps s 
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