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
    
    $distributor="0000000005";
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    
    
    if ($distributor!="0000000005") {
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
    
    
    
    
    $totalproduk=0;
    // -----------------------------  insert produk
    /*
    $qryproduk="SELECT * FROM $dbname.pv_import_produk";
    
    $tampil_pr= mysqli_query($cnmy, $qryproduk);
    while ($data1= mysqli_fetch_array($tampil_pr)) {
        $brgid=$data1['PROD_ID'];
        $namaproduk=mysqli_real_escape_string($cnmy, $data1['PROD_NAME']);
        $satuan=mysqli_real_escape_string($cnmy, $data1['PROD_UOM_PRIN']);
        $hna=$data1['PROD_HNA'];
        
        $cekproduk=mysqli_fetch_array(mysqli_query($cnmy, "SELECT COUNT(eprodid) FROM MKT.eproduk WHERE distid='$distributor' AND eprodid='$brgid'"));
        $cekproduk=$cekproduk[0];
        if ($cekproduk<1){
            
            mysqli_query($cnmy, "
                INSERT INTO MKT.eproduk(distid,eprodid,nama,satuan,hna,aktif,oldflag) 
                VALUES('$distributor','$brgid','$namaproduk','$satuan',$hna,'Y','Y')
                ");
            
            echo "berhasil input produk baru -> $namaproduk ~ $brgid ~ $hna <br>";
            $totalproduk=$totalproduk+1;
            
        }
        
    }
    
    echo "Total Produk baru yg berhasil diinput: $totalproduk<br><hr><br>";
    
      
      
     
    $totalcust=0;
    // -----------------------------  insert customer
    $qrycust="SELECT * FROM $dbname.pv_import_cust";
    $tampil_cu= mysqli_query($cnmy, $qrycust);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        
        $cabang=$data1['BRANCH_ID'];
        $ecust=$data1['CUST_SHIP_ID'];
        $enama=mysqli_real_escape_string($cnmy, $data1['CUST_NAME']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['CUST_ADDR1']);
        $kota=mysqli_real_escape_string($cnmy, $data1['CUST_CITY']);
        
        $cekcust=mysqli_fetch_array(mysqli_query($cnmy, "select count(distid) from MKT.ecust where distid='$distributor' and ecustid='$ecust'"));

        $cekcust1=$cekcust[0];
        if ($cekcust1<1){
            mysqli_query($cnmy, "
                INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif) 
                VALUES('$distributor','$cabang','$ecust','$enama','$alamat','$kota','Y','Y')
            ");

            echo "berhasil input cust baru -> $cabang - $ecust - $enama - $alamat <br>";
            $totalcust=$totalcust+1;
        }
        
        
    }
    
    echo "Total Customer baru yg berhasil diinput: $totalcust<br><hr><br>";
    */
    
    
    
    
        /*
    $qrysales="
        SELECT DISTINCT
          BRANCH_ID,CUSTOMER_ID,INV_NO,PRODUCT_ID,STR_TO_DATE(INV_DATE,'%d-%b-%Y') INV_DATE,REPLACE(REPLACE(SELL_PRICE, ',', ''), '.00', '') SELL_PRICE,
          REPLACE(REPLACE(NETT_QTY_SOLD, ',', ''), '.00', '') NETT_QTY_SOLD, REPLACE(REPLACE(TOT_QTY_BNS, ',', ''), '.00', '') TOT_QTY_BNS
        FROM $dbname.pv_import_sales 
        WHERE LEFT(STR_TO_DATE(INV_DATE,'%d-%b-%Y'),7) = '$bulan'
      ";


    $tampil_sl= mysqli_query($cnmy, $qrysales);
    while ($data1= mysqli_fetch_array($tampil_sl)) {
        
        $cabangid=$data1['BRANCH_ID'];
        $custid=$data1['CUSTOMER_ID'];
        $nojual=$data1['INV_NO'];
        $brgid=$data1['PRODUCT_ID'];
        $tgljual=$data1['INV_DATE'];
        $harga=$data1['SELL_PRICE'];
        $qbeli=$data1['NETT_QTY_SOLD'];
        $qbonus=$data1['TOT_QTY_BNS'];
        $totale=$harga*$qbeli;
        
        
        $insert=mysqli_query($cnmy, "
            INSERT INTO $dbname.salespv(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus)
            VALUES('$cabangid','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$qbonus')
        ");

        if ($insert){
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
        }
    
    }
    */
    
    
    $query1 = "DELETE FROM $dbname.pv_import_produk WHERE CONCAT(IFNULL(PROD_ID,''),'$distributor') in
            (SELECT DISTINCT CONCAT(IFNULL(eprodid,''),'$distributor') FROM MKT.eproduk WHERE distid='$distributor')";
    mysqli_query($cnmy, $query1);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE PROD ADA : $erropesan"; exit; }
    
    $query2="INSERT INTO MKT.eproduk(distid,eprodid,nama,satuan,hna,aktif,oldflag)"
            . " SELECT DISTINCT '$distributor', PROD_ID, PROD_NAME, PROD_UOM_PRIN, REPLACE(REPLACE(PROD_HNA, ',', ''), '.00', '') as PROD_HNA, 'Y', 'Y' FROM $dbname.pv_import_produk";
    mysqli_query($cnmy, $query2);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT PROD ADA : $erropesan"; exit; }
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, $query1);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE PROD ADA : $erropesan"; exit; }
        
        mysqli_query($cnit, $query2);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT PROD ADA : $erropesan"; exit; }
    }
    //END IT
    
    
    $query3="SELECT DISTINCT '$distributor', PROD_ID, PROD_NAME, PROD_UOM_PRIN, PROD_HNA, 'Y', 'Y' FROM $dbname.pv_import_produk";
    $tampil=mysqli_query($cnmy, $query3);
    $totalproduk= mysqli_num_rows($tampil);
    
    echo "Total Produk baru yg berhasil diinput: $totalproduk<br><hr><br>";
    
	
		
		
		
		
		$temp001="dbtemp.tmpisicustfrpv00231x_01";
		$temp002="dbtemp.tmpisicustfrpv00231x_02";
		$temp003="dbtemp.tmpisicustfrpv00231x_03";
		
		mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $temp001");
		$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "DROP TEMPORARY tmp1 : $erropesan"; exit; }
		mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $temp002");
		$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "DROP TEMPORARY tmp2 : $erropesan"; exit; }
		mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $temp003");
		$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "DROP TEMPORARY tmp2 : $erropesan"; exit; }
		
		mysqli_query($cnmy, "CREATE TEMPORARY TABLE $temp001 (select distinct distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif from MKT.ecust WHERE distid='$distributor')");
		$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "INSERT TEMPORARY CUST ADA : $erropesan"; exit; }
		
		mysqli_query($cnmy, "alter table $temp001 add COLUMN noidauto BIGINT(50) NOT NULL AUTO_INCREMENT PRIMARY KEY");
		mysqli_query($cnmy, "CREATE INDEX `norm1` ON $temp001 (noidauto, distid, cabangid,ecustid)");
		
		mysqli_query($cnmy, "CREATE TEMPORARY TABLE $temp002 (select DISTINCT '0000000005' distid, a.BRANCH_ID, a.CUST_SHIP_ID, a.CUST_NAME, a.CUST_ADDR1, a.CUST_CITY, 'Y' oldflag, 'Y' aktif from $dbname.pv_import_cust a)");
		$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "INSERT TEMPORARY TMP PV ADA : $erropesan"; exit; }
		
		mysqli_query($cnmy, "alter table $temp002 add COLUMN noidauto BIGINT(50) NOT NULL AUTO_INCREMENT PRIMARY KEY");
		mysqli_query($cnmy, "CREATE INDEX `norm1` ON $temp002 (noidauto, distid, BRANCH_ID,CUST_SHIP_ID)");
		
		
		$query_pp = "CREATE TEMPORARY TABLE $temp003 (select DISTINCT a.distid, a.BRANCH_ID, a.CUST_SHIP_ID, a.CUST_NAME, a.CUST_ADDR1, a.CUST_CITY, a.oldflag, a.aktif 
			from $temp002 as a LEFT JOIN 
			(select distinct cabangid, ecustid from $temp001) b on IFNULL(a.CUST_SHIP_ID,'')=IFNULL(b.ecustid,'')
			and IFNULL(a.BRANCH_ID,'')=IFNULL(b.cabangid,'') WHERE IFNULL(b.ecustid,'')='')";
		mysqli_query($cnmy, $query_pp);
		$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE TEMP 3 : $erropesan"; exit; }
			
		
		$queryMKT = "insert into MKT.ecust (distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif) 
			select DISTINCT a.distid, a.BRANCH_ID, a.CUST_SHIP_ID, a.CUST_NAME, a.CUST_ADDR1, a.CUST_CITY, a.oldflag, a.aktif 
			from $temp003 as a";
		mysqli_query($cnmy, $queryMKT);
		$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT MKT CUST ADA : $erropesan"; exit; }
		

		
		
		
	/*
    $query4 = "DELETE FROM $dbname.pv_import_cust WHERE CONCAT(IFNULL(CUST_SHIP_ID,''),'$distributor') in 
            (SELECT DISTINCT CONCAT(IFNULL(ecustid,''),'$distributor') FROM MKT.ecust WHERE distid='$distributor');";
    mysqli_query($cnmy, $query4);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE CUST ADA : $erropesan"; exit; }
    
    $query5 = "INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,kota,oldflag,aktif)"
            . "SELECT DISTINCT '$distributor', BRANCH_ID, CUST_SHIP_ID, CUST_NAME, CUST_ADDR1, CUST_CITY, 'Y', 'Y' FROM $dbname.pv_import_cust";
    mysqli_query($cnmy, $query5);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT CUST ADA : $erropesan"; exit; }
    */
	
	
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, $query4);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE CUST ADA : $erropesan"; exit; }
        
        mysqli_query($cnit, $query5);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT CUST ADA : $erropesan"; exit; }
    }
    //END IT
        
    
    //$query6="SELECT DISTINCT '$distributor', BRANCH_ID, CUST_SHIP_ID, CUST_NAME, CUST_ADDR1, CUST_CITY, 'Y', 'Y' FROM $dbname.pv_import_cust";
	$query6="SELECT * FROM $temp003";
			
    $tampil=mysqli_query($cnmy, $query6);
    $totalcust= mysqli_num_rows($tampil);
    
    echo "Total Customer baru yg berhasil diinput: $totalcust<br><hr><br>";
    
		mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $temp001");
		mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $temp002");
		mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $temp003");
    
    
    // -----------------------------  insert sales
    $totalsalesqty=0;
    $totalsalessum=0;
    
    $query_sls1="delete from $dbname.salespv where left(tgljual,7)='$bulan'";
    mysqli_query($cnmy, $query_sls1);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE salespv : $erropesan"; exit; }
    
    $query_sls2="INSERT INTO $dbname.salespv(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,qbonus)"
            . "SELECT BRANCH_ID, CUSTOMER_ID, STR_TO_DATE(INV_DATE,'%d-%b-%Y') as INV_DATE, PRODUCT_ID, "
            . " REPLACE(REPLACE(SELL_PRICE, ',', ''), '.00', '') as SELL_PRICE, "
            . " REPLACE(REPLACE(NETT_QTY_SOLD, ',', ''), '.00', '') as NETT_QTY_SOLD, "
            . " INV_NO, "
            . " REPLACE(REPLACE(TOT_QTY_BNS, ',', ''), '.00', '') as TOT_QTY_BNS "
            . " FROM $dbname.pv_import_sales WHERE LEFT(STR_TO_DATE(INV_DATE,'%d-%b-%Y'),7) = '$bulan'";
    mysqli_query($cnmy, $query_sls2);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT salespv : $erropesan"; exit; }
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, $query_sls1);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE salespv : $erropesan"; exit; }
        
        mysqli_query($cnit, $query_sls2);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT salespv : $erropesan"; exit; }
    }
    //END IT
    
    
    $totalsalessum=mysqli_fetch_array(mysqli_query($cnmy, "select sum(IFNULL(SELL_PRICE,0)*IFNULL(NETT_QTY_SOLD,0)) as jml from $dbname.pv_import_sales WHERE LEFT(STR_TO_DATE(INV_DATE,'%d-%b-%Y'),7) = '$bulan'"));
    $totalsalesqty=mysqli_fetch_array(mysqli_query($cnmy, "select COUNT(DISTINCT INV_NO) as jml from $dbname.pv_import_sales WHERE LEFT(STR_TO_DATE(INV_DATE,'%d-%b-%Y'),7) = '$bulan'"));
    
    $totalsalessum=$totalsalessum[0];
    $totalsalesqty=$totalsalesqty[0];
    
    
    
    echo "
      Total penjualan yg berhasil diinput : $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>
      <hr>";
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.pv_import_produk");
    mysqli_query($cnmy, "DELETE FROM $dbname.pv_import_cust");
    mysqli_query($cnmy, "DELETE FROM $dbname.pv_import_sales");
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.pv_import_produk");
        mysqli_query($cnit, "DELETE FROM $dbname.pv_import_cust");
        mysqli_query($cnit, "DELETE FROM $dbname.pv_import_sales");
    }
    //END IT
    
    
    
    $query = "SELECT s.tgljual, s.fakturId, s.brgid, s.harga, s.qbeli FROM $dbname.salespv s 
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