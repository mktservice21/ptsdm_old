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
    
    
    $cabang="PKB";
    $subdist="GMP";
    $distributor="0000000002";
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    $pakhirbulan =  date("Y-m-t", strtotime($ptgl));
    
    
    if ($distributor!="0000000002" AND $subdist!="GMP") {
        echo "tidak ada data yang diproses...";
        exit;
    }
    
    
    echo "$distributor . $cabang . $bulan . $subdist<br/>";
    
    //ubah juga di _uploaddata
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importfilesdt_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importfilesdt_ipms (select *, CAST(NULL AS DECIMAL(20,2)) as hna from $dbname.mssales)");
    
    $query_up_prod = "UPDATE $dbname.tmp_importfilesdt_ipms a JOIN "
            . " (SELECT DISTINCT e.distid, e.`iProdId`, e.`eProdId`, i.`hna` FROM MKT.eproduk e INNER JOIN MKT.iproduk i ON "
            . " e.`iProdId` = i.`iProdId` WHERE e.distid='0000000002') b ON a.BRGID=b.eProdId AND '0000000002'=b.distid SET "
            . " a.hna=b.hna";
    mysqli_query($cnmy, $query_up_prod);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error UPDATE HNA : $erropesan"; exit; }
    
    
    $query ="select * from $dbname.tmp_importfilesdt_ipms where IFNULL(hna,0)=0";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu>0) {
        echo "<div style='color:red;'><h1>ADA HNA 0....</h1></div>";
    }
    
    
    
    $totalcust=0;
    
    //call procedur
        $eksekusi=mysqli_query($cnmy, "CALL MKT.cursor_ecust()");    
    
    //IT
    if ($plogit_akses==true) {
        $eksekusi2=mysqli_query($cnit, "CALL $dbname.cursor_ecust()");
    }
    
    
    // sales
    $totalsalesqty=0;
    $totalsalessum=0;
    
    mysqli_query($cnmy, "DELETE FROM $dbname.salesspp WHERE left(tgljual,7)='$bulan' AND cabangid='$cabang' AND subdist='$subdist' ");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error DELETE salesspp : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.salesspp WHERE left(tgljual,7)='$bulan' AND cabangid='$cabang' AND subdist='$subdist' ");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error DELETE salesspp : $erropesan"; exit; }
    }
    //END IT
    
    
    $pinsertsave=false;
    //$qrysales="SELECT * FROM $dbname.mssales";
    $qrysales="SELECT * FROM $dbname.tmp_importfilesdt_ipms";
    
        if ($distributor=="0000000002"){
            $tabel="$dbname.salesspp";
        }
        
    $tampil_sl= mysqli_query($cnmy, $qrysales);
    while ($data1= mysqli_fetch_array($tampil_sl)) {
        
        
        $custid=$data1['CUSTID'];
        $nojual=$data1['NOJUAL'];
        $brgid=$data1['BRGID'];
        $tgljual=$data1['TGLJUAL'];
        
        $harga=$data1['hna'];
        $qbeli=$data1['QBELI'];
        $totale=$harga*$qbeli;
        
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
        
        $pinsert_data_sls[] = "('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$subdist')";
        $pinsertsave=true; 
        
        /*
        $harga0=mysqli_fetch_array(mysqli_query($cnmy, "
            SELECT i.`hna` FROM MKT.eproduk e INNER JOIN MKT.iproduk i ON e.`iProdId` = i.`iProdId` WHERE e.`eProdId` = '$brgid' AND  e.distid='0000000002'
        "));
        $harga=$harga0[0];
        
        
        $qbeli=$data1['QBELI'];
        $totale=$harga*$qbeli;
        if ($distributor=="0000000002"){
            $tabel="$dbname.salesspp";
        }

        //$tanggaljual=$tahun."-".$bulan."-".$tgl;
        // echo $distributor.','.$custid.','.$brgid.','.$tgljual.','.$harga.','.$qbeli.','.$totale.','.$subdist.'<br>';

        $insert=mysqli_query($cnmy, "
            INSERT INTO $tabel(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,subdist) 
            VALUES('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$subdist')
        ");

        if ($insert){
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
        }
        
        //IT
        if ($plogit_akses==true) {
            $insert2=mysqli_query($cnit, "
                INSERT INTO $tabel(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,subdist) 
                VALUES('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$subdist')
            ");
        }
        */
        
    }
    
    if ($pinsertsave = true) {
        
        $query_sls_ins = "INSERT INTO $tabel (cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,subdist) VALUES "
                . "".implode(', ', $pinsert_data_sls);

        mysqli_query($cnmy, $query_sls_ins);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER $tabel : $erropesan"; exit; }

        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_sls_ins);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "Error INSER $tabel : $erropesan"; exit; }
        }
        //END IT
        
    }
    
    
    echo "Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>sekian dan terimakasih.<br>
    <hr>";
    mysqli_query($cnmy, "drop table $dbname.mssales");
    mysqli_query($cnmy, "drop table $dbname.msbar");
    mysqli_query($cnmy, "truncate table $dbname.subdist_mscust_bcm");



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
    