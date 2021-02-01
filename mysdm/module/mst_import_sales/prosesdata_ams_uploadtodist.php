<?php

    ini_set("memory_limit","1G");
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
    
    $distributor=$_POST['uiddist'];
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    $pakhirbulan =  date("Y-m-t", strtotime($ptgl));
    
    
    if ($distributor!="0000000003") {
        echo "tidak ada data yang diproses...";
        exit;
    }
    
    //ubah juga di _uploaddata
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    //$plogit_akses==false;
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    $totalproduk=0;
    // -----------------------------  insert produk
    $qryproduk="
        SELECT DISTINCT a.C_ITENO AS brgid,a.C_ITNAM AS nama,a.C_UNDES AS satuan ,N_SALPRI AS hna
        FROM $dbname.AMS_ITEMSDM a
        INNER JOIN $dbname.AMS_JUALSDM b ON a.C_ITENO = b.C_ITENO
    ";
    $tampil_up= mysqli_query($cnmy, $qryproduk);
    $isave=false;
    while ($data1= mysqli_fetch_array($tampil_up)) {
        $brgid=$data1['brgid'];
        $namaproduk=mysqli_real_escape_string($cnmy, $data1['nama']);
        $satuan=mysqli_real_escape_string($cnmy, $data1['satuan']);
        $hna=$data1['hna'];

        $cekproduk=mysqli_fetch_array(mysqli_query($cnmy, "SELECT COUNT(eprodid) FROM MKT.eproduk WHERE distid='$distributor' AND eprodid='$brgid'"));
        $cekproduk=$cekproduk[0];
        if ($cekproduk<1){

            $pinput_produk[] = "('$distributor','$brgid','$namaproduk','$satuan',$hna,'Y','Y')";

            echo "berhasil input produk baru -> $namaproduk - $brgid - $hna <br>";
            $totalproduk=$totalproduk+1;
            $isave=true;
        }

    }

    if ($isave==true) {
        
        $query_inst_prod = "INSERT INTO MKT.eproduk(distid,eprodid,nama,satuan,hna,aktif,oldflag) VALUES "
                . " ".implode(', ', $pinput_produk);
        
        mysqli_query($cnmy, $query_inst_prod);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT eproduk : $erropesan"; exit; }

        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_inst_prod);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT eproduk IT : $erropesan"; exit; }
        }
        //END IT
    }
    
    
    echo "Total Produk baru yg berhasil diinput: $totalproduk<br><hr><br>";
    mysqli_query($cnmy, "DELETE FROM $dbname.AMS_ITEMSDM");
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.AMS_ITEMSDM");
    }
    //END IT
    
    $totalcust=0;
    // -----------------------------  insert customer
    $qrycust="SELECT DISTINCT C_KDCAB as cabang,C_CUSNO as custid,C_CUNAM as custnm,C_ADRBILL1 as alamat,C_CTYBILL as kota FROM $dbname.AMS_CUSTSDM";
    $tampil_cu= mysqli_query($cnmy, $qrycust);
    $isave=false;
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        $cabang=$data1['cabang'];
        $ecust=$data1['custid'];
        $enama=mysqli_real_escape_string($cnmy, $data1['custnm']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['alamat']);
        $kota=mysqli_real_escape_string($cnmy, $data1['kota']);
        
        $cekcust=mysqli_fetch_array(mysqli_query($cnmy, "select count(distid) from MKT.ecust where distid='$distributor' and cabangid='$cabang' and ecustid='$ecust'"));
        
        $cekcust1=$cekcust[0];
        if ($cekcust1<1){
            
            $pinput_cust[]="('$distributor','$cabang','$ecust','$enama','$alamat','$kota','Y','Y')";
                    
            echo "berhasil input cust baru -> $cabang - $ecust - $enama - $alamat <br>";
            $totalcust=$totalcust+1;
            $isave=true;
            
        }
        
    }
     
    if ($isave==true) {
        
        $query_inst_cust = "insert into MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif)values "
                . " ".implode(', ', $pinput_cust);

        mysqli_query($cnmy, $query_inst_cust);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT ecust : $erropesan"; exit; }

        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_inst_cust);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT ecust IT : $erropesan"; exit; }
        }
        //END IT
        
    }
    
    
    echo "Total Customer baru yg berhasil diinput: $totalcust<br><hr><br>";
    mysqli_query($cnmy, "DELETE FROM $dbname.AMS_CUSTSDM");
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.AMS_CUSTSDM");
    }
    //END IT
    
    // -----------------------------  insert sales
    
        
        
    $totalsalesqty=0;
    $totalsalessum=0;
    mysqli_query($cnmy, "DELETE FROM $dbname.salesams where left(tgljual,7) = '$bulan'");
    
    
    $query = "INSERT INTO $dbname.salesams(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus)"
            . "select C_KDCAB, C_CUSNO, D_INVDATE, C_ITENO, N_SALPRI, N_QTYSAL, C_INVNO, N_QTYBON FROM $dbname.AMS_JUALSDM WHERE LEFT(D_INVDATE,7) = '$bulan'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT salesams : $erropesan"; exit; }
    
    $totalsalessum=mysqli_fetch_array(mysqli_query($cnmy, "select sum(IFNULL(N_SALPRI,0)*IFNULL(N_QTYSAL,0)) as jml from $dbname.AMS_JUALSDM WHERE LEFT(D_INVDATE,7) = '$bulan'"));
    $totalsalesqty=mysqli_fetch_array(mysqli_query($cnmy, "select COUNT(DISTINCT C_INVNO) as jml from $dbname.AMS_JUALSDM WHERE LEFT(D_INVDATE,7) = '$bulan'"));
    
    $totalsalessum=$totalsalessum[0];
    $totalsalesqty=$totalsalesqty[0];
    
    
    
    echo "Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br><hr>";
    mysqli_query($cnmy, "DELETE FROM $dbname.AMS_JUALSDM");
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.salesams where left(tgljual,7) = '$bulan'");
        
        $query = "INSERT INTO $dbname.salesams(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus)"
                . "select C_KDCAB, C_CUSNO, D_INVDATE, C_ITENO, N_SALPRI, N_QTYSAL, C_INVNO, N_QTYBON FROM $dbname.AMS_JUALSDM WHERE LEFT(D_INVDATE,7) = '$bulan'";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT salesams IT : $erropesan"; exit; }
        
        mysqli_query($cnit, "DELETE FROM $dbname.AMS_JUALSDM");
        
    }
    //END IT
    
    
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importfilesdt_ipmsams");
    mysqli_query($cnmy, "create table $dbname.tmp_importfilesdt_ipmsams (select *, CAST(NULL AS DECIMAL(20,2)) as nqty from $dbname.AMS_RETUR)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE salesams RETUR : $erropesan"; exit; }
    
    $query = "UPDATE $dbname.tmp_importfilesdt_ipmsams SET nqty=(IFNULL(N_QTYSALG,'') + IFNULL(N_QTYSALB,''))";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error UPDATE QTY salesams RETUR : $erropesan"; exit; }
    
    $query = "UPDATE $dbname.tmp_importfilesdt_ipmsams SET nqty=0-IFNULL(nqty,0) WHERE IFNULL(nqty,0)>0";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error UPDATE QTY MINUS salesams RETUR : $erropesan"; exit; }
    
    
    
    mysqli_query($cnmy, "delete from $dbname.salesams where left(tgljual,7)='$bulan' AND fakturid LIKE 'R%'");
    
    $pinsert_retur="INSERT INTO $dbname.salesams(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus)"
            . " SELECT C_KDCAB, C_CUSNO, D_INVDATE, C_ITENO, N_SALPRI, "
            . " nqty, C_INVNO, 0 as qbonus FROM $dbname.tmp_importfilesdt_ipmsams WHERE LEFT(D_INVDATE,7) = '$bulan'";
    mysqli_query($cnmy, $pinsert_retur);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT salesams RETUR : $erropesan"; exit; }
    
    
    //IT
    if ($plogit_akses==true) {
        
        mysqli_query($cnit, "DROP TABLE IF EXISTS $dbname.tmp_importfilesdt_ipmsams");
        mysqli_query($cnit, "create table $dbname.tmp_importfilesdt_ipmsams (select *, CAST(NULL AS DECIMAL(20,2)) as nqty from $dbname.AMS_RETUR)");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error CREATE salesams RETUR IT : $erropesan"; exit; }

        $query = "UPDATE $dbname.tmp_importfilesdt_ipmsams SET nqty=(IFNULL(N_QTYSALG,'') + IFNULL(N_QTYSALB,''))";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error UPDATE QTY salesams RETUR IT : $erropesan"; exit; }

        $query = "UPDATE $dbname.tmp_importfilesdt_ipmsams SET nqty=0-IFNULL(nqty,0) WHERE IFNULL(nqty,0)>0";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error UPDATE QTY MINUS salesams RETUR IT : $erropesan"; exit; }
    
        
    
        mysqli_query($cnit, "DELETE FROM $dbname.salesams where left(tgljual,7) = '$bulan' AND fakturid LIKE 'R%'");
        
        mysqli_query($cnit, $pinsert_retur);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT salesams Retur IT : $erropesan"; exit; }
        
        mysqli_query($cnit, "DELETE FROM $dbname.AMS_RETUR");
        
    }
    //END IT
    //
    //
    //end upload ke tabel
    
    $totalsalessum_r=mysqli_fetch_array(mysqli_query($cnmy, "select sum(IFNULL(N_SALPRI,0)*IFNULL(nqty,0)) as jml from $dbname.tmp_importfilesdt_ipmsams WHERE LEFT(D_INVDATE,7) = '$bulan'"));
    $totalsalesqty_r=mysqli_fetch_array(mysqli_query($cnmy, "select COUNT(DISTINCT C_INVNO) as jml from $dbname.tmp_importfilesdt_ipmsams WHERE LEFT(D_INVDATE,7) = '$bulan'"));
    
    $totalsalessum_r=$totalsalessum_r[0];
    $totalsalesqty_r=$totalsalesqty_r[0];
    
    
    
    echo "Total retur yg berhasil diinput: $totalsalessum_r , dengan jumlah no faktur sebanyak $totalsalesqty_r.<br><hr>";
    mysqli_query($cnmy, "DELETE FROM $dbname.AMS_RETUR");
    
    
    $totsales_all=mysqli_fetch_array(mysqli_query($cnmy, "select sum(IFNULL(qbeli,0)*IFNULL(harga,0)) as jml from $dbname.salesams where left(tgljual,7)='$bulan'"));
    $totalsalessum_all=$totsales_all[0];

    echo "Total sales+retur : $totalsalessum_all.<br><hr>";
    
    
    
    
    $query = "SELECT s.tgljual, s.fakturId, s.brgid, s.harga, s.qbeli FROM $dbname.salesams s 
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
    
    echo "Selesai dalam ".$total_time." detik<br/>&nbsp;<br/>&nbsp;";
    
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