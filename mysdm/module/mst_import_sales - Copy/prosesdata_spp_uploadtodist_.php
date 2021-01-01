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
    $distributor=$_POST['uiddist'];
    $picabangpilih=$_POST['unmfilecab'];
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    
    
    $n_cabang= trim(substr($picabangpilih, 0, 3));
    if (!empty($n_cabang)) $n_cabang= ucwords ($n_cabang);
    
    
    if ($n_cabang=="BDG") {
        $cabang="BDG";
    }elseif ($n_cabang=="DIY") {
        $cabang="YOG";
    }elseif ($n_cabang=="H_O") {
        $cabang="HO2";
    }elseif ($n_cabang=="JKT") {
        $cabang="HO";
    }elseif ($n_cabang=="MLG") {
        $cabang="MLG";
    }elseif ($n_cabang=="SBY") {
        $cabang="SBY";
    }elseif ($n_cabang=="SLO") {
        $cabang="SOL";
    }elseif ($n_cabang=="SMG") {
        $cabang="SMG";
    }else{
        echo 'Tidak Ada Cabang ';
        exit;
    }
    
    
    
    if ($distributor!="0000000002") {
        echo "tidak ada data yang diproses...";
        exit;
    }
    
    //ubah juga di _uploaddata
    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    $dbname = "MKT";
    

    //echo "$distributor, $cabang, $bulan";
    
    $totalcust=0;
    // customer
    

    
    if($cabang == 'BDG'){
      $qrycust="SELECT * FROM $dbname.spp_mscust_bdg";
    }elseif($cabang == 'YOG'){
      $qrycust="SELECT * FROM $dbname.spp_mscust_diy";
    }elseif($cabang == 'HO2'){
      $qrycust="SELECT * FROM $dbname.spp_mscust_ho";
    }elseif($cabang == 'HO'){
      $qrycust="SELECT * FROM $dbname.spp_mscust_jkt";
    }elseif($cabang == 'MLG'){
      $qrycust="SELECT * FROM $dbname.spp_mscust_mlg";
    }elseif($cabang == 'SBY'){
      $qrycust="SELECT * FROM $dbname.spp_mscust_sby";
    }elseif($cabang == 'SOL'){
      $qrycust="SELECT * FROM $dbname.spp_mscust_slo";
    }elseif($cabang == 'SMG'){
      $qrycust="SELECT * FROM $dbname.spp_mscust_smg";
    }else{
      echo 'Tidak Ada Cabang ';
      exit;
    }
    
    $tampil_cu= mysqli_query($cnmy, $qrycust);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        $ecust=$data1['CUSTID'];
        $enama=$data1['CUSTNM'];
        $enama=mysqli_real_escape_string($cnmy, $enama);
        $alamat=$data1['ALAMAT'];
        $alamat=mysqli_real_escape_string($cnmy, $alamat);
        $kotaid=$data1['KOTAID'];
        $kota=$data1['KOTA'];
        $sektorid=$data1['SEKTORID'];
        
        echo "$ecust, $enama, $alamat, $kotaid, $kota, $sektorid<br/>";
        
        
        $cekcust2=mysqli_num_rows(mysqli_query($cnmy, "
            SELECT * FROM MKT.ecust 
            WHERE distid='$distributor' 
            AND cabangid='$cabang' 
            AND ecustid='$ecust'
        "));
        
        if ($cekcust2<1){

            $eksekusi=mysqli_query($cnmy, "
                INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,oldflag,aktif,kota,ekotaid,esektorid,subdist) 
                VALUES('$distributor','$cabang','$ecust','$enama','$alamat','Y','Y','$kota','$kotaid','$sektorid','$subdist')
            ");

            if ($eksekusi) { 
                echo "berhasil input cust baru -> $cabang - $ecust - $enama - $alamat - $kotaid - $kota - $sektorid <br>"; 
                $totalcust=$totalcust+1; 
            }

        }else{

            $eksekusi=mysqli_query($cnmy, "
                UPDATE MKT.ecust 
                SET nama='$enama', alamat1='$alamat', kota='$kota', ekotaid='$kotaid', esektorid='$sektorid' 
                WHERE distid='$distributor' 
                AND cabangid='$cabang' 
                AND ecustid='$ecust'
            ");

            if ($eksekusi) { 
                echo "berhasil EDIT nama Customer -> $cabang - $ecust - $enama <br>"; 
                $totalcust=$totalcust+1; 
            }
            
        }

        
        
    }
    
    echo "Total Customer baru yg berhasil diinput: $totalcust<br><hr><br>";
    
    
    
    
    $totalkota=0;
    // kota
    if($cabang == 'BDG'){
      $qrykota="SELECT * FROM $dbname.spp_mskota_bdg";
    }elseif($cabang == 'YOG'){
      $qrykota="SELECT * FROM $dbname.spp_mskota_diy";
    }elseif($cabang == 'HO2'){
      $qrykota="SELECT * FROM $dbname.spp_mskota_ho";
    }elseif($cabang == 'HO'){
      $qrykota="SELECT * FROM $dbname.spp_mskota_jkt";
    }elseif($cabang == 'MLG'){
      $qrykota="SELECT * FROM $dbname.spp_mskota_mlg";
    }elseif($cabang == 'SBY'){
      $qrykota="SELECT * FROM $dbname.spp_mskota_sby";
    }elseif($cabang == 'SOL'){
      $qrykota="SELECT * FROM $dbname.spp_mskota_slo";
    }elseif($cabang == 'SMG'){
      $qrykota="SELECT * FROM $dbname.spp_mskota_smg";
    }else{
      echo 'Tidak Ada Cabang ';
      exit;
    }
    
    $tampil_kt= mysqli_query($cnmy, $qrykota);
    while ($data1= mysqli_fetch_array($tampil_kt)) {
        
        $nama=$data1['NAMA'];
        $kotaid=$data1['KOTAID'];
        $cekkota=mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM MKT.ekota WHERE distid='$distributor' AND cabangid='$cabang' AND ekotaid='$kotaid'"));
        
        //echo "$nama, $kotaid, $cekkota<br/>";
        
        if ($cekkota<1){
            $eksekusi=mysqli_query($cnmy, "
                INSERT INTO MKT.ekota(distid,cabangid,ekotaid,nama,aktif,oldflag) 
                VALUES('$distributor','$cabang','$kotaid','$nama','Y','Y')
            ");
            
            if ($eksekusi) { 
                echo "berhasil input kota baru -> $distributor - $cabang - $kotaid - $nama  <br>"; 
                $totalkota=$totalkota+1; 
            }
        }
        
    }
    
    echo "Total kota baru yg berhasil diinput: $totalkota<br><hr><br>";
    
    
    


    $totalsektor=0;
    // sektor
    if($cabang == 'BDG'){
      $qrysektor="SELECT * FROM $dbname.spp_mssekt_bdg";
    }elseif($cabang == 'YOG'){
      $qrysektor="SELECT * FROM $dbname.spp_mssekt_diy";
    }elseif($cabang == 'HO2'){
      $qrysektor="SELECT * FROM $dbname.spp_mssekt_ho";
    }elseif($cabang == 'HO'){
      $qrysektor="SELECT * FROM $dbname.spp_mssekt_jkt";
    }elseif($cabang == 'MLG'){
      $qrysektor="SELECT * FROM $dbname.spp_mssekt_mlg";
    }elseif($cabang == 'SBY'){
      $qrysektor="SELECT * FROM $dbname.spp_mssekt_sby";
    }elseif($cabang == 'SOL'){
      $qrysektor="SELECT * FROM $dbname.spp_mssekt_slo";
    }elseif($cabang == 'SMG'){
      $qrysektor="SELECT * FROM $dbname.spp_mssekt_smg";
    }else{
      echo 'Tidak Ada Cabang';
      return;
    }

    $tampil_se= mysqli_query($cnmy, $qrysektor);
    while ($data1= mysqli_fetch_array($tampil_se)) {
        
        $sektorid=$data1['SEKTORID'];
        $sektornm=$data1['SEKTORNM'];
        $ceksektor=mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM MKT.esektor WHERE distid='$distributor' AND esektorid='$sektorid'"));
        
        //echo "$sektorid, $sektornm, $ceksektor<br/>";
        
        //echo "SELECT * FROM esektor WHERE distid='$distributor' AND esektorid='$sektorid' AND nama='$sektornm' <br>";
        if ($ceksektor<1){
            $eksekusi=mysqli_query($cnmy, "
                INSERT INTO MKT.esektor(distid,esektorid,nama,aktif,oldflag) 
                VALUES('$distributor','$sektorid','$sektornm','Y','Y')
            ");

            if ($eksekusi) { 
                echo "berhasil input sektor baru -> $distributor - $sektorid - $sektornm<br>"; 
                $totalsektor=$totalsektor+1; 
            }
        }
        
    }

    
    echo "Total sektor baru yg berhasil diinput: $totalsektor<br><hr><br>";


    
    

    $totalbrg=0;
    // barang
    if($cabang == 'BDG'){
      $qrybrg="SELECT * FROM $dbname.spp_msbar_bdg";
    }elseif($cabang == 'YOG'){
      $qrybrg="SELECT * FROM $dbname.spp_msbar_diy";
    }elseif($cabang == 'HO2'){
      $qrybrg="SELECT * FROM $dbname.spp_msbar_ho";
    }elseif($cabang == 'HO'){
      $qrybrg="SELECT * FROM $dbname.spp_msbar_jkt";
    }elseif($cabang == 'MLG'){
      $qrybrg="SELECT * FROM $dbname.spp_msbar_mlg";
    }elseif($cabang == 'SBY'){
      $qrybrg="SELECT * FROM $dbname.spp_msbar_sby";
    }elseif($cabang == 'SOL'){
      $qrybrg="SELECT * FROM $dbname.spp_msbar_slo";
    }elseif($cabang == 'SMG'){
      $qrybrg="SELECT * FROM $dbname.spp_msbar_smg";
    }else{
      echo 'Tidak Ada Cabang ';
      return;
    }

    
    $tampil_ip= mysqli_query($cnmy, $qrybrg);
    while ($data1= mysqli_fetch_array($tampil_ip)) {
        
        
        $brgid=$data1['BRGID'];
        $brgnm=$data1['BRGNM'];
        $unitpcs=$data1['UNITPCS'];
        $cekbrg=mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM MKT.eproduk WHERE distid='$distributor' AND eprodid='$brgid'"));
        
        //echo "$brgid, $brgnm, $unitpcs, $cekbrg <br/>";
        
        if ($cekbrg<1){
            $eksekusi=mysqli_query($cnmy, "
                INSERT INTO MKT.eproduk(distid,eprodid,nama,satuan,aktif,oldflag) 
                VALUES('$distributor','$brgid','$brgnm','$unitpcs','Y','Y')
            ");
            if ($eksekusi) { 
                echo "berhasil input sektor baru -> $distributor - $brgid - $brgnm - $unitpcs<br>"; 
                $totalbrg=$totalbrg+1; 
            }
        }
    
    }
    
    echo "Total barang baru yg berhasil diinput: $totalbrg<br><hr><br>";
    

    
    
    
    // sales
    $totalsalesqty=0;
    $totalsalessum=0;
    if($cabang == 'BDG'){
      $qrysales="SELECT * FROM $dbname.spp_mssales_bdg WHERE LEFT (tgljual, 7) = '$bulan'";
    }elseif($cabang == 'YOG'){
      $qrysales="SELECT * FROM $dbname.spp_mssales_diy WHERE LEFT (tgljual, 7) = '$bulan'";
    }elseif($cabang == 'HO2'){
      $qrysales="SELECT * FROM $dbname.spp_mssales_ho WHERE LEFT (tgljual, 7) = '$bulan'";
    }elseif($cabang == 'HO'){
      $qrysales="SELECT * FROM $dbname.spp_mssales_jkt WHERE LEFT (tgljual, 7) = '$bulan'";
    }elseif($cabang == 'MLG'){
      $qrysales="SELECT * FROM $dbname.spp_mssales_mlg WHERE LEFT (tgljual, 7) = '$bulan'";
    }elseif($cabang == 'SBY'){
      $qrysales="SELECT * FROM $dbname.spp_mssales_sby WHERE LEFT (tgljual, 7) = '$bulan'";
    }elseif($cabang == 'SOL'){
      $qrysales="SELECT * FROM $dbname.spp_mssales_slo WHERE LEFT (tgljual, 7) = '$bulan'";
    }elseif($cabang == 'SMG'){
      $qrysales="SELECT * FROM $dbname.spp_mssales_smg WHERE LEFT (tgljual, 7) = '$bulan'";
    }else{
      echo 'Tidak Ada Cabang ';
      return;
    }
    
    mysqli_query($cnmy, "DELETE FROM $dbname.salesspp WHERE LEFT(tgljual,7) = '$bulan' AND cabangid = '$cabang' AND subdist = ''");
    
    $tampil_sl= mysqli_query($cnmy, $qrysales);
    while ($data1= mysqli_fetch_array($tampil_sl)) {
        
	$custid=$data1['CUSTID'];
        $nojual=$data1['NOJUAL'];
        $brgid=$data1['BRGID'];
        $harga=$data1['HARGA'];
        if (substr($nojual, -1)=="R") {
            $qbeli=$data1['QBELI']*-1;
            $qbonus=$data1['QBONUS']*-1; 
        }else{
            $qbeli=$data1['QBELI'];
            $qbonus=$data1['QBONUS'];
        }

        $tgljual=$data1['TGLJUAL'];
        $totale=$harga*$qbeli;
        
        //echo "$cabang, $custid, $tgljual, $brgid, $harga, $qbeli, $qbonus, $nojual<br/>";
        
        $eksekusi=mysqli_query($cnmy, "
            INSERT INTO $dbname.salesspp(cabangid,custid,tgljual,brgid,harga,qbeli,qbonus,fakturid) 
            VALUES('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$qbonus','$nojual')
        ");
        
        if ($eksekusi){
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
        }
        
        
    }
    
    
    echo "<br>
    <hr><b>Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>sekian dan terimakasih</b>
    <br>";

    
    
    if($cabang == 'BDG'){
      // -------------------------------- BADNUNG
      mysqli_query($cnmy, "Delete from $dbname.spp_msbar_bdg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mscust_bdg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssales_bdg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mskota_bdg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssekt_bdg");
    }elseif($cabang == 'YOG'){
      // -------------------------------- Yogya
      mysqli_query($cnmy, "Delete from $dbname.spp_msbar_diy");
      mysqli_query($cnmy, "Delete from $dbname.spp_mscust_diy");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssales_diy");
      mysqli_query($cnmy, "Delete from $dbname.spp_mskota_diy");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssekt_diy");
    }elseif($cabang == 'HO2'){
      // -------------------------------- HO2
      mysqli_query($cnmy, "Delete from $dbname.spp_msbar_ho");
      mysqli_query($cnmy, "Delete from $dbname.spp_mscust_ho");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssales_ho");
      mysqli_query($cnmy, "Delete from $dbname.spp_mskota_ho");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssekt_ho");
    }elseif($cabang == 'HO'){
      // -------------------------------- HO
      mysqli_query($cnmy, "Delete from $dbname.spp_msbar_jkt");
      mysqli_query($cnmy, "Delete from $dbname.spp_mscust_jkt");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssales_jkt");
      mysqli_query($cnmy, "Delete from $dbname.spp_mskota_jkt");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssekt_jkt");
    }elseif($cabang == 'MLG'){
      // -------------------------------- MALANG
      mysqli_query($cnmy, "Delete from $dbname.spp_msbar_mlg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mscust_mlg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssales_mlg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mskota_mlg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssekt_mlg");
    }elseif($cabang == 'SBY'){
      // -------------------------------- Surabaya
      mysqli_query($cnmy, "Delete from $dbname.spp_msbar_sby");
      mysqli_query($cnmy, "Delete from $dbname.spp_mscust_sby");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssales_sby");
      mysqli_query($cnmy, "Delete from $dbname.spp_mskota_sby");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssekt_sby");
    }elseif($cabang == 'SOL'){
      // -------------------------------- Solo
      mysqli_query($cnmy, "Delete from $dbname.spp_msbar_slo");
      mysqli_query($cnmy, "Delete from $dbname.spp_mscust_slo");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssales_slo");
      mysqli_query($cnmy, "Delete from $dbname.spp_mskota_slo");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssekt_slo");
    }elseif($cabang == 'SMG'){
      // -------------------------------- Semarang
      mysqli_query($cnmy, "Delete from $dbname.spp_msbar_smg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mscust_smg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssales_smg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mskota_smg");
      mysqli_query($cnmy, "Delete from $dbname.spp_mssekt_smg");
    }else{
      echo 'Tidak Ada Cabang ';
      return;
    }


    mysqli_close($cnmy);
    
?>