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
    
    $id_cabang="HO";
    
    $distributor="0000000010";
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    $pakhirbulan =  date("Y-m-t", strtotime($ptgl));
    
    
    if (empty($bulan)){ exit(); }
    
    if ($distributor!="0000000010") {
        echo "tidak ada data yang diproses...";
        exit;
    }
    
    
    //ubah juga di _uploaddata
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    
    echo "Sapta sari ~ $bulan ~ $id_cabang<br>";
    
    $isimpan_cus=false;
    $isimpan_prod=false;
    $isimpan_sls=false;
    
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importsst_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importsst_ipms (select *, CAST('' AS CHAR(5)) as idcab from $dbname.importsst)");
    

    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='ACH' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='ACEH'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='BDG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='BANDUNG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='BJM' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='BANJARMASIN'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='BGR' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='BOGOR'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='CRB' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='CIREBON'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='DPS' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='DENPASAR'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='JK1' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='JAKARTA1'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='JK2' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='JAKARTA2'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='JMB' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='JAMBI'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='JBR' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='JEMBER'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='JOG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='JOGJAKARTA'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='KPG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='KUPANG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='LPG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='LAMPUNG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='MKR' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='MAKASAR'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='MLG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='MALANG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='MND' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='MANADO'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='MTM' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='MATARAM'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='MDN' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='MEDAN'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PDG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PADANG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PLG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PALEMBANG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PLU' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PALU'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PKB' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PEKANBARU'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PTK' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PONTIANAK'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PWK' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PURWOKERTO'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='SMD' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='SAMARINDA'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='SMG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='SEMARANG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='SLO' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='SOLO'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='SBY' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='SURABAYA'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='BKS' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='BEKASI'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='NON' WHERE IFNULL(`Cabang`,'')='' AND IFNULL(idcab,'')=''");
    
    
    
    //CUSTOMER
    
    $totcustinput=0;
    
    $query = "select DISTINCT idcab, `Kode Pelanggan` AS kodepelanggan, `Nama Pelanggan` AS namapelanggan, 
        `Alamat` AS alamat, `Cabang` AS cabang
         from $dbname.tmp_importsst_ipms WHERE CONCAT('0000000010', IFNULL(`idcab`,''), IFNULL(`Kode Pelanggan`,'')) NOT IN 
        (SELECT CONCAT(IFNULL(distid,''), IFNULL(cabangid,''), IFNULL(ecustid,'')) FROM MKT.ecust WHERE distid = '0000000010')";
    
    $tampil_cu= mysqli_query($cnmy, $query);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        $kodedist="0000000010";
        $kodecabang=$data1['idcab'];
        $cabang=mysqli_real_escape_string($cnmy, $data1['cabang']);
        $kodepelanggan=mysqli_real_escape_string($cnmy, $data1['kodepelanggan']);
        $namapelanggan=mysqli_real_escape_string($cnmy, $data1['namapelanggan']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['alamat']);
        
        $totcustinput++;
        $isimpan_cus=true;
        
        $pinsert_cust[] = "('$kodedist','$kodecabang','$kodepelanggan','$namapelanggan','$alamat','Y','Y')";
        
    }
    
    
    //PRODUK
    
    $totprodukinput=0;
    
    $query = "select DISTINCT `Kode Produk` AS kodeproduk, `Nama Produk` AS namaproduk, `Harga` AS harga 
         from $dbname.tmp_importsst_ipms WHERE CONCAT('0000000010', IFNULL(`Kode Produk`,'')) NOT IN 
        (SELECT CONCAT(IFNULL(distid,''), IFNULL(eprodid,'')) FROM MKT.eproduk WHERE distid = '0000000010')";
    $tampil_pr= mysqli_query($cnmy, $query);
    while ($data1= mysqli_fetch_array($tampil_pr)) {
        $kodedist="0000000010";
        $kodeproduk=mysqli_real_escape_string($cnmy, $data1['kodeproduk']);
        $namaproduk=mysqli_real_escape_string($cnmy, $data1['namaproduk']);
        $harga=$data1['harga'];
        
        $totprodukinput++;
        $isimpan_prod=true;
        
        $pinsert_prod[] = "('$kodedist','$kodeproduk','$namaproduk','$harga','Y','Y')";
        
    }
    
    echo "<br/>Total Customer BARU : $totcustinput<br/>Total Produk BARU : $totprodukinput<br/>";  
    
    
    
    $qry="
        SELECT
          i.`Cabang` AS cabang,
          i.`Kode Produk` AS kodeproduk,
          i.`Kode Pelanggan` AS kodepelanggan,
          i.`Nama Pelanggan` AS namapelanggan,
          i.`Nama Produk` AS namaproduk,
          i.`Alamat` AS alamat,
          CASE WHEN `No Faktur` LIKE '19%' THEN LEFT(i.`No Faktur`, 13) ELSE LEFT(i.`No Faktur`, 15) END nofaktur,
          LEFT(i.`Tgl Dok`,10) AS tglfaktur,
          i.`Unit` AS qty,
          i.`Bonus faktur` AS bonus,
          i.`Harga` AS harga,
          i.`asl_data`,
          i.`Total HNA` total_hna,
          i.`ExpDate`, 
          i.`acu`,
          i.`tgl_acu` 
        FROM
          $dbname.importsst i
        WHERE LEFT(i.`Tgl Dok`, 7) = '$bulan' AND asl_data = '$id_cabang'
        ";
    
    $tampil_sl= mysqli_query($cnmy, $qry);
    while ($data1= mysqli_fetch_array($tampil_sl)) {
        
        
        $kodedist="0000000010";
        $cabang=mysqli_real_escape_string($cnmy, $data1['cabang']);

        $kodeproduk=mysqli_real_escape_string($cnmy, $data1['kodeproduk']);
        $namaproduk=mysqli_real_escape_string($cnmy, $data1['namaproduk']);
        $kodepelanggan=mysqli_real_escape_string($cnmy, $data1['kodepelanggan']);
        $namapelanggan=mysqli_real_escape_string($cnmy, $data1['namapelanggan']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['alamat']);
        $nofaktur=$data1['nofaktur'];
        $kodecabang=substr($nofaktur,4,3);
            
        if($cabang=='Aceh'){
          $kodecabang = 'ACH';
        }elseif($cabang=='Bandung'){
          $kodecabang = 'BDG';
        }elseif($cabang=='Banjarmasin'){
          $kodecabang = 'BJM';
        }elseif($cabang=='Bogor'){
          $kodecabang = 'BGR';
        }elseif($cabang=='Cirebon'){
          $kodecabang = 'CRB';
        }elseif($cabang=='Denpasar'){
          $kodecabang = 'DPS';
        }elseif($cabang=='Jakarta1'){
          $kodecabang = 'JK1';
        }elseif($cabang=='Jakarta2'){
          $kodecabang = 'JK2';
        }elseif($cabang=='Jambi'){
          $kodecabang = 'JMB';
        }elseif($cabang=='Jember'){
          $kodecabang = 'JBR';
        }elseif($cabang=='Jogjakarta'){
          $kodecabang = 'JOG';
        }elseif($cabang=='Kupang'){
          $kodecabang = 'KPG';
        }elseif($cabang=='Lampung'){
          $kodecabang = 'LPG';
        }elseif($cabang=='Makasar'){
          $kodecabang = 'MKR';
        }elseif($cabang=='Malang'){
          $kodecabang = 'MLG';
        }elseif($cabang=='Manado'){
          $kodecabang = 'MND';
        }elseif($cabang=='Mataram'){
          $kodecabang = 'MTM';
        }elseif($cabang=='Medan'){
          $kodecabang = 'MDN';
        }elseif($cabang=='Padang'){
          $kodecabang = 'PDG';
        }elseif($cabang=='Palembang'){
          $kodecabang = 'PLG';
        }elseif($cabang=='Palu'){
          $kodecabang = 'PLU';
        }elseif($cabang=='Pekanbaru'){
          $kodecabang = 'PKB';
        }elseif($cabang=='Pontianak'){
          $kodecabang = 'PTK';
        }elseif($cabang=='Purwokerto'){
          $kodecabang = 'PWK';
        }elseif($cabang=='Samarinda'){
          $kodecabang = 'SMD';
        }elseif($cabang=='Semarang'){
          $kodecabang = 'SMG';
        }elseif($cabang=='Solo'){
          $kodecabang = 'SLO';
        }elseif($cabang=='Surabaya'){
          $kodecabang = 'SBY';
        }elseif($cabang=='Bekasi'){
          $kodecabang = 'BKS';
        }else{
          $kodecabang = 'NON';
        }
    
        
	      $noacu = $data1['acu'];
	      $ntglacu = $data1['tgl_acu'];
	      $nexpdate = $data1['ExpDate'];
        

        $tglfaktur=$data1['tglfaktur'];
        $qty=$data1['qty'];
        $bonus=$data1['bonus'];
        $harga=$data1['harga'];
        $total_hna=$data1['total_hna'];
        if($bonus <> 0){
            $qty = (double)$qty - (double)$bonus;
        }

        if($harga == '' || $harga == null){
            $harga = 0;
        }

        if($bonus == '' || $bonus == null){
            $bonus = 0;
        }
        
        $isimpan_sls=true;
        
        $pinsert_sls[] = "('$id_cabang','$kodecabang','$kodepelanggan','$tglfaktur','$kodeproduk',$harga,$qty,$bonus,'$nofaktur','$kodepelanggan','$noacu', '$ntglacu', '$nexpdate')";
        
        
    }
    
    
    
    if ($isimpan_cus==true) {
        $query_cust = "INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,oldflag,aktif) VALUES "
                . " ".implode(', ', $pinsert_cust);
        mysqli_query($cnmy, $query_cust);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT CUST : $erropesan"; exit; }
        
        
    }
    
    
    if ($isimpan_prod==true) {
        $query_prod = "INSERT INTO MKT.eproduk(distid,eprodid,nama,hna,oldflag,aktif) VALUES "
                . " ".implode(', ', $pinsert_prod);
        mysqli_query($cnmy, $query_prod);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT PROD : $erropesan"; exit; }
        
    }
    
    
    
    //hapus data sales perbulan, dari atas
    mysqli_query($cnmy, "DELETE FROM $dbname.salessaptabaru WHERE left(tgljual,7)='$bulan' AND asl_data = '$id_cabang'");
    
    if ($isimpan_sls==true) {
        $query_sales = "INSERT INTO $dbname.salessaptabaru(asl_data,cabangid,custid,tgljual,brgid,harga,qbeli,qbonus,fakturid,custid3,NoAcu, tgl_acu, expdate) VALUES "
                . " ".implode(', ', $pinsert_sls);
        mysqli_query($cnmy, $query_sales);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT SALES : $erropesan"; exit; }
        
        
    }
    

    
    
    $totalsalessum=0;
    $totalsalesqty=0;
    
    $totalsalessum=mysqli_fetch_array(mysqli_query($cnmy, "select sum(IFNULL(Harga,0)*IFNULL(Unit,0)) as jml from $dbname.importsst WHERE LEFT(`Tgl Dok`, 7) = '$bulan' AND asl_data = '$id_cabang'"));
    $totalsalesqty=mysqli_fetch_array(mysqli_query($cnmy, "select COUNT(DISTINCT `No Faktur`) as jml from $dbname.importsst WHERE LEFT(`Tgl Dok`, 7) = '$bulan' AND asl_data = '$id_cabang'"));
    
    $totalsalessum=$totalsalessum[0];
    $totalsalesqty=$totalsalesqty[0];
    
    echo "Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>sekian dan terimakasih";

    mysqli_query($cnmy, "DELETE FROM $dbname.tmp_importsst_ipms");
    
    mysqli_query($cnmy, "DELETE FROM $dbname.importsst WHERE LEFT(`Tgl Dok`, 7) = '$bulan' AND asl_data = '$id_cabang'");

    
    
    
    $query = "SELECT s.tgljual, s.fakturId, s.brgid, s.harga, s.qbeli FROM $dbname.salessaptabaru s 
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
    
    

    mysqli_close($cnmy);
    
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


<?PHP

echo "<br/><br/>Upload IT...<br/><br/>";


    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    $dbname = "MKT";
    
    unset($pinsert_sls);//kosongkan array
    unset($pinsert_prod);//kosongkan array
    unset($pinsert_cust);//kosongkan array
    
    echo "Sapta sari ~ $bulan ~ $id_cabang<br>";
    
    $isimpan_cus=false;
    $isimpan_prod=false;
    $isimpan_sls=false;
    
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importsst_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importsst_ipms (select *, CAST('' AS CHAR(5)) as idcab from $dbname.importsst)");
    

    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='ACH' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='ACEH'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='BDG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='BANDUNG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='BJM' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='BANJARMASIN'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='BGR' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='BOGOR'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='CRB' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='CIREBON'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='DPS' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='DENPASAR'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='JK1' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='JAKARTA1'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='JK2' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='JAKARTA2'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='JMB' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='JAMBI'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='JBR' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='JEMBER'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='JOG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='JOGJAKARTA'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='KPG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='KUPANG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='LPG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='LAMPUNG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='MKR' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='MAKASAR'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='MLG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='MALANG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='MND' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='MANADO'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='MTM' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='MATARAM'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='MDN' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='MEDAN'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PDG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PADANG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PLG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PALEMBANG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PLU' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PALU'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PKB' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PEKANBARU'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PTK' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PONTIANAK'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='PWK' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='PURWOKERTO'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='SMD' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='SAMARINDA'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='SMG' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='SEMARANG'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='SLO' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='SOLO'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='SBY' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='SURABAYA'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='BKS' WHERE LTRIM(RTRIM(UCASE(`Cabang`)))='BEKASI'");
    mysqli_query($cnmy, "UPDATE $dbname.tmp_importsst_ipms set idcab='NON' WHERE IFNULL(`Cabang`,'')='' AND IFNULL(idcab,'')=''");
    
    
    
    //CUSTOMER
    
    $totcustinput=0;
    
    $query = "select DISTINCT idcab, `Kode Pelanggan` AS kodepelanggan, `Nama Pelanggan` AS namapelanggan, 
        `Alamat` AS alamat, `Cabang` AS cabang
         from $dbname.tmp_importsst_ipms WHERE CONCAT('0000000010', IFNULL(`idcab`,''), IFNULL(`Kode Pelanggan`,'')) NOT IN 
        (SELECT CONCAT(IFNULL(distid,''), IFNULL(cabangid,''), IFNULL(ecustid,'')) FROM MKT.ecust WHERE distid = '0000000010')";
    
    $tampil_cu= mysqli_query($cnmy, $query);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        $kodedist="0000000010";
        $kodecabang=$data1['idcab'];
        $cabang=mysqli_real_escape_string($cnmy, $data1['cabang']);
        $kodepelanggan=mysqli_real_escape_string($cnmy, $data1['kodepelanggan']);
        $namapelanggan=mysqli_real_escape_string($cnmy, $data1['namapelanggan']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['alamat']);
        
        $totcustinput++;
        $isimpan_cus=true;
        
        $pinsert_cust[] = "('$kodedist','$kodecabang','$kodepelanggan','$namapelanggan','$alamat','Y','Y')";
        
    }
    
    
    //PRODUK
    
    $totprodukinput=0;
    
    $query = "select DISTINCT `Kode Produk` AS kodeproduk, `Nama Produk` AS namaproduk, `Harga` AS harga 
         from $dbname.tmp_importsst_ipms WHERE CONCAT('0000000010', IFNULL(`Kode Produk`,'')) NOT IN 
        (SELECT CONCAT(IFNULL(distid,''), IFNULL(eprodid,'')) FROM MKT.eproduk WHERE distid = '0000000010')";
    $tampil_pr= mysqli_query($cnmy, $query);
    while ($data1= mysqli_fetch_array($tampil_pr)) {
        $kodedist="0000000010";
        $kodeproduk=mysqli_real_escape_string($cnmy, $data1['kodeproduk']);
        $namaproduk=mysqli_real_escape_string($cnmy, $data1['namaproduk']);
        $harga=$data1['harga'];
        
        $totprodukinput++;
        $isimpan_prod=true;
        
        $pinsert_prod[] = "('$kodedist','$kodeproduk','$namaproduk','$harga','Y','Y')";
        
    }
    
    echo "<br/>Total Customer BARU : $totcustinput<br/>Total Produk BARU : $totprodukinput<br/>";  
    
    
    
    $qry="
        SELECT
          i.`Cabang` AS cabang,
          i.`Kode Produk` AS kodeproduk,
          i.`Kode Pelanggan` AS kodepelanggan,
          i.`Nama Pelanggan` AS namapelanggan,
          i.`Nama Produk` AS namaproduk,
          i.`Alamat` AS alamat,
          CASE WHEN `No Faktur` LIKE '19%' THEN LEFT(i.`No Faktur`, 13) ELSE LEFT(i.`No Faktur`, 15) END nofaktur,
          LEFT(i.`Tgl Dok`,10) AS tglfaktur,
          i.`Unit` AS qty,
          i.`Bonus faktur` AS bonus,
          i.`Harga` AS harga,
          i.`asl_data`,
          i.`Total HNA` total_hna
        FROM
          $dbname.importsst i
        WHERE LEFT(i.`Tgl Dok`, 7) = '$bulan' AND asl_data = '$id_cabang'
        ";
    
    $tampil_sl= mysqli_query($cnmy, $qry);
    while ($data1= mysqli_fetch_array($tampil_sl)) {
        
        
        $kodedist="0000000010";
        $cabang=mysqli_real_escape_string($cnmy, $data1['cabang']);

        $kodeproduk=mysqli_real_escape_string($cnmy, $data1['kodeproduk']);
        $namaproduk=mysqli_real_escape_string($cnmy, $data1['namaproduk']);
        $kodepelanggan=mysqli_real_escape_string($cnmy, $data1['kodepelanggan']);
        $namapelanggan=mysqli_real_escape_string($cnmy, $data1['namapelanggan']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['alamat']);
        $nofaktur=$data1['nofaktur'];
        $kodecabang=substr($nofaktur,4,3);
            
        if($cabang=='Aceh'){
          $kodecabang = 'ACH';
        }elseif($cabang=='Bandung'){
          $kodecabang = 'BDG';
        }elseif($cabang=='Banjarmasin'){
          $kodecabang = 'BJM';
        }elseif($cabang=='Bogor'){
          $kodecabang = 'BGR';
        }elseif($cabang=='Cirebon'){
          $kodecabang = 'CRB';
        }elseif($cabang=='Denpasar'){
          $kodecabang = 'DPS';
        }elseif($cabang=='Jakarta1'){
          $kodecabang = 'JK1';
        }elseif($cabang=='Jakarta2'){
          $kodecabang = 'JK2';
        }elseif($cabang=='Jambi'){
          $kodecabang = 'JMB';
        }elseif($cabang=='Jember'){
          $kodecabang = 'JBR';
        }elseif($cabang=='Jogjakarta'){
          $kodecabang = 'JOG';
        }elseif($cabang=='Kupang'){
          $kodecabang = 'KPG';
        }elseif($cabang=='Lampung'){
          $kodecabang = 'LPG';
        }elseif($cabang=='Makasar'){
          $kodecabang = 'MKR';
        }elseif($cabang=='Malang'){
          $kodecabang = 'MLG';
        }elseif($cabang=='Manado'){
          $kodecabang = 'MND';
        }elseif($cabang=='Mataram'){
          $kodecabang = 'MTM';
        }elseif($cabang=='Medan'){
          $kodecabang = 'MDN';
        }elseif($cabang=='Padang'){
          $kodecabang = 'PDG';
        }elseif($cabang=='Palembang'){
          $kodecabang = 'PLG';
        }elseif($cabang=='Palu'){
          $kodecabang = 'PLU';
        }elseif($cabang=='Pekanbaru'){
          $kodecabang = 'PKB';
        }elseif($cabang=='Pontianak'){
          $kodecabang = 'PTK';
        }elseif($cabang=='Purwokerto'){
          $kodecabang = 'PWK';
        }elseif($cabang=='Samarinda'){
          $kodecabang = 'SMD';
        }elseif($cabang=='Semarang'){
          $kodecabang = 'SMG';
        }elseif($cabang=='Solo'){
          $kodecabang = 'SLO';
        }elseif($cabang=='Surabaya'){
          $kodecabang = 'SBY';
        }elseif($cabang=='Bekasi'){
          $kodecabang = 'BKS';
        }else{
          $kodecabang = 'NON';
        }
    
        $noacu="";
	  // $noacu = $data1['NoAcu'];
        $tglfaktur=$data1['tglfaktur'];
        $qty=$data1['qty'];
        $bonus=$data1['bonus'];
        $harga=$data1['harga'];
        $total_hna=$data1['total_hna'];
        if($bonus <> 0){
            $qty = (double)$qty - (double)$bonus;
        }

        if($harga == '' || $harga == null){
            $harga = 0;
        }

        if($bonus == '' || $bonus == null){
            $bonus = 0;
        }
        
        $isimpan_sls=true;
        
        $pinsert_sls[] = "('$id_cabang','$kodecabang','$kodepelanggan','$tglfaktur','$kodeproduk',$harga,$qty,$bonus,'$nofaktur','$kodepelanggan','$noacu')";
        
        
    }
    
    

    if ($isimpan_cus==true) {
        $query_cust = "INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,oldflag,aktif) VALUES "
                . " ".implode(', ', $pinsert_cust);
        mysqli_query($cnmy, $query_cust);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT CUST : $erropesan"; exit; }
        
        
    }
    


    if ($isimpan_prod==true) {
        $query_prod = "INSERT INTO MKT.eproduk(distid,eprodid,nama,hna,oldflag,aktif) VALUES "
                . " ".implode(', ', $pinsert_prod);
        mysqli_query($cnmy, $query_prod);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT PROD : $erropesan"; exit; }
        
    }
    
    
    
    //hapus data sales perbulan, dari atas
    mysqli_query($cnmy, "DELETE FROM $dbname.salessaptabaru WHERE left(tgljual,7)='$bulan' AND asl_data = '$id_cabang'");
    
    if ($isimpan_sls==true) {
        $query_sales = "INSERT INTO $dbname.salessaptabaru(asl_data,cabangid,custid,tgljual,brgid,harga,qbeli,qbonus,fakturid,custid3,NoAcu) VALUES "
                . " ".implode(', ', $pinsert_sls);
        mysqli_query($cnmy, $query_sales);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT SALES : $erropesan"; exit; }
        
        
    }
    

    
    
    $totalsalessum=0;
    $totalsalesqty=0;
    
    $totalsalessum=mysqli_fetch_array(mysqli_query($cnmy, "select sum(IFNULL(Harga,0)*IFNULL(Unit,0)) as jml from $dbname.importsst WHERE LEFT(`Tgl Dok`, 7) = '$bulan' AND asl_data = '$id_cabang'"));
    $totalsalesqty=mysqli_fetch_array(mysqli_query($cnmy, "select COUNT(DISTINCT `No Faktur`) as jml from $dbname.importsst WHERE LEFT(`Tgl Dok`, 7) = '$bulan' AND asl_data = '$id_cabang'"));
    
    $totalsalessum=$totalsalessum[0];
    $totalsalesqty=$totalsalesqty[0];
    
    echo "Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>sekian dan terimakasih";

    mysqli_query($cnmy, "DELETE FROM $dbname.tmp_importsst_ipms");
    
    mysqli_query($cnmy, "DELETE FROM $dbname.importsst WHERE LEFT(`Tgl Dok`, 7) = '$bulan' AND asl_data = '$id_cabang'");

    
    
    
    $query = "SELECT s.tgljual, s.fakturId, s.brgid, s.harga, s.qbeli FROM $dbname.salessaptabaru s 
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

    mysqli_close($cnmy);

echo "<br/>";

?>


<?PHP
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 4);
    
    echo "<br/>Selesai dalam ".$total_time." detik<br/>&nbsp;<br/>&nbsp;";
?>