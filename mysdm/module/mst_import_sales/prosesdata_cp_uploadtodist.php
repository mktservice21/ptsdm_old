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
    
    $subdist="";
    $cabang="CP";
    $distributor="0000000011";
    $ptgl=$_POST['ubln'];
    $ptgl_per=$_POST['unmfilecab'];//tanggal disimpan di unmfilecab cek di importsales.php
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    $periode =  date("Y-m-d", strtotime($ptgl_per));
    
    
    if ($distributor!="0000000011") {
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
    
    
    echo "$distributor ~ $cabang ~ $periode<br><br>";
    
    
    $totalproduk=0;
    // customer
    $qryproduk="
      SELECT DISTINCT `kd barang` AS BRGID,`Nama Barang` AS NAMA,Satuan AS satuan,`Harga satuan` as hna
      FROM $dbname.combieth 
    ";
    
    $tampil_pr= mysqli_query($cnmy, $qryproduk);
    while ($data1= mysqli_fetch_array($tampil_pr)) {
        
        $brgid=$data1['BRGID'];
        $namaproduk=mysqli_real_escape_string($cnmy, $data1['NAMA']);
        $satuan=mysqli_real_escape_string($cnmy, $data1['satuan']);
        $hna=$data1['hna'];
        $cekproduk=mysqli_fetch_array(mysqli_query($cnmy, "SELECT COUNT(eprodid) FROM MKT.eproduk WHERE distid='0000000011' AND eprodid='$brgid'"));
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
    $qrycust="SELECT DISTINCT `Kd customer` as CUSTID,nama as CUSTNM,alamat as ALAMAT,kota FROM $dbname.combieth";
    $tampil_cu= mysqli_query($cnmy, $qrycust);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        
        $ecust=$data1['CUSTID'];
        $enama=mysqli_real_escape_string($cnmy, $data1['CUSTNM']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['ALAMAT']);
        $kota=mysqli_real_escape_string($cnmy, $data1['kota']);
        $cekcust=mysqli_fetch_array(mysqli_query($cnmy, "
            SELECT COUNT(distid) FROM MKT.ecust WHERE distid='$distributor' AND cabangid='$cabang' AND ecustid='$ecust'
            "));
        $cekcust1=$cekcust[0];
        if ($cekcust1<1){
            mysqli_query($cnmy, "
                INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif,subdist) 
                VALUES('$distributor','$cabang','$ecust','$enama','$alamat','$kota','Y','Y','$subdist')");
            
            echo "berhasil input cust baru -> $cabang - $ecust - $enama - $alamat <br>";
            $totalcust=$totalcust+1;
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, "
                    INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif,subdist) 
                    VALUES('$distributor','$cabang','$ecust','$enama','$alamat','$kota','Y','Y','$subdist')");
            }
            //END IT
            
        }
        
    }
    
    
    echo "Total Customer baru yg berhasil diinput: $totalcust<br><hr><br>";


    // sales
    $totalsalesqty=0;
    $totalsalessum=0;
    mysqli_query($cnmy, "DELETE FROM $dbname.salescombibaru WHERE tgljual = '$periode' AND cabangid='$cabang'");

    $qrysales="
        SELECT `Kd customer` AS CUSTID,`Nomor Faktur` AS NOJUAL,`kd barang` AS BRGID,`Tanggal Faktur` AS TGLJUAL,`Harga satuan` AS HARGA0,`Qty Sales` AS QBELI 
        FROM $dbname.combieth 
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
        if ($distributor=="0000000011"){
            $tabel="$dbname.salescombibaru";
        }

        
        $insert=mysqli_query($cnmy, "
            INSERT INTO $tabel(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid) 
            VALUES('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual')
        ");
        if ($insert)
        {
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
        }
        
        //IT
        if ($plogit_akses==true) {
            $insert2=mysqli_query($cnit, "
                INSERT INTO $tabel(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid) 
                VALUES('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual')
            ");
        }
        //END IT
        
        
        
    }
    
    
    echo "Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>Sekian dan terimakasih<br>
    <hr>";
    mysqli_query($cnmy, "DELETE FROM $dbname.combieth");
    
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, "DELETE FROM $dbname.combieth");
        }
        //END IT
    
    
        
        
    $query = "SELECT s.tgljual, s.fakturId, s.brgid, s.harga, s.qbeli FROM $dbname.salescombibaru s 
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