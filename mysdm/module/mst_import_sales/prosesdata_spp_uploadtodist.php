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
    
    $subdist="";
    $distributor=$_POST['uiddist'];
    $picabangpilih=$_POST['unmfilecab'];
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    $pakhirbulan =  date("Y-m-t", strtotime($ptgl));
    
    
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
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    

    //echo "$distributor, $cabang, $bulan";
    
    $totalcust=0;
    // customer
    

    
    if($cabang == 'BDG'){
      $qrycust_mer="SELECT * FROM $dbname.spp_mscust_bdg";
    }elseif($cabang == 'YOG'){
      $qrycust_mer="SELECT * FROM $dbname.spp_mscust_diy";
    }elseif($cabang == 'HO2'){
      $qrycust_mer="SELECT * FROM $dbname.spp_mscust_ho";
    }elseif($cabang == 'HO'){
      $qrycust_mer="SELECT * FROM $dbname.spp_mscust_jkt";
    }elseif($cabang == 'MLG'){
      $qrycust_mer="SELECT * FROM $dbname.spp_mscust_mlg";
    }elseif($cabang == 'SBY'){
      $qrycust_mer="SELECT * FROM $dbname.spp_mscust_sby";
    }elseif($cabang == 'SOL'){
      $qrycust_mer="SELECT * FROM $dbname.spp_mscust_slo";
    }elseif($cabang == 'SMG'){
      $qrycust_mer="SELECT * FROM $dbname.spp_mscust_smg";
    }else{
      echo 'Tidak Ada Cabang ';
      exit;
    }
    
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importprodfile_ipms ($qrycust_mer)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER tmp_importprodfile_ipms mscust $cabang : $erropesan"; exit; }
    
    $query_upde = "UPDATE MKT.ecust a JOIN $dbname.tmp_importprodfile_ipms b on a.distid='$distributor' AND a.cabangid='$cabang' AND "
            . " a.ecustid=b.CUSTID SET a.nama=b.CUSTNM, a.alamat1=b.ALAMAT, "
            . " a.kota=b.KOTA, a.ekotaid=b.KOTAID, "
            . " a.esektorid=b.SEKTORID WHERE a.distid='$distributor' AND a.cabangid='$cabang'";
    mysqli_query($cnmy, $query_upde);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error UPDATE tmp_importprodfile_ipms mscust $cabang : $erropesan"; exit; }
    echo "Data ecust yang berhasil diupdate : " . mysqli_affected_rows($cnmy)."<br/>";
    
    $query_del = "DELETE FROM $dbname.tmp_importprodfile_ipms WHERE CONCAT('$distributor', '$cabang', IFNULL(CUSTID,'')) IN "
            . " (SELECT CONCAT(IFNULL(distid,''), IFNULL(cabangid,''), IFNULL(ecustid,'')) FROM MKT.ecust WHERE distid='$distributor' AND cabangid='$cabang')";
    mysqli_query($cnmy, $query_del);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE tmp_importprodfile_ipms mscust $cabang : $erropesan"; exit; }
    
    $query_inst = "INSERT INTO MKT.ecust(distid,cabangid,ecustid,nama,alamat1,oldflag,aktif,kota,ekotaid,esektorid,subdist) "
            . "SELECT DISTINCT '$distributor', '$cabang', CUSTID, CUSTNM, ALAMAT, 'Y', 'Y', KOTA, KOTAID, SEKTORID, '$subdist' "
            . " FROM $dbname.tmp_importprodfile_ipms";
    mysqli_query($cnmy, $query_inst);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT tmp_importprodfile_ipms mscust $cabang : $erropesan"; exit; }
    echo "Data ecust baru yang berhasil diinput : " . mysqli_affected_rows($cnmy)."<br/>";
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
        mysqli_query($cnit, "create table $dbname.tmp_importprodfile_ipms ($qrycust_mer)");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER tmp_importprodfile_ipms mscust $cabang : $erropesan"; exit; }
        
        mysqli_query($cnit, $query_upde);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error UPDATE tmp_importprodfile_ipms mscust $cabang : $erropesan"; exit; }
        echo "IT. Data ecust yang berhasil diupdate : " . mysqli_affected_rows($cnit)."<br/>";
        
        mysqli_query($cnit, $query_del);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE tmp_importprodfile_ipms mscust $cabang : $erropesan"; exit; }
        
        mysqli_query($cnit, $query_inst);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT tmp_importprodfile_ipms mscust $cabang : $erropesan"; exit; }
        echo "IT. Data ecust baru yang berhasil diinput : " . mysqli_affected_rows($cnit)."<br/>";
    }
    //END IT
    
    
    
    
    
    /*
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
    
    */
    
    
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

    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importprodfile_ipms ($qrykota)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER tmp_importprodfile_ipms ekota $cabang : $erropesan"; exit; }
    
    
    $query_kota_del = "DELETE FROM $dbname.tmp_importprodfile_ipms WHERE CONCAT('$distributor', '$cabang', IFNULL(KOTAID,'')) IN "
            . " (SELECT CONCAT(IFNULL(distid,''), IFNULL(cabangid,''), IFNULL(ekotaid,'')) FROM MKT.ekota WHERE distid='$distributor' AND cabangid='$cabang')";
    mysqli_query($cnmy, $query_kota_del);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE tmp_importprodfile_ipms ekota $cabang : $erropesan"; exit; }
    
    /*
    $query_kota_inst = "INSERT INTO MKT.ekota(distid,cabangid,ekotaid,nama,aktif,oldflag) "
            . "SELECT DISTINCT '$distributor', '$cabang', KOTAID, NAMA, 'Y', 'Y' "
            . " FROM $dbname.tmp_importprodfile_ipms";
    mysqli_query($cnmy, $query_kota_inst);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT tmp_importprodfile_ipms ekota $cabang : $erropesan"; exit; }
    echo "Data ekota baru yang berhasil diinput : " . mysqli_affected_rows($cnmy)."<br/>";
    
    //IT
    if ($plogit_akses==true) {
        
        mysqli_query($cnit, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
        mysqli_query($cnit, "create table $dbname.tmp_importprodfile_ipms ($qrykota)");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER tmp_importprodfile_ipms ekota $cabang : $erropesan"; exit; }
        
        mysqli_query($cnit, $query_kota_del);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE tmp_importprodfile_ipms ekota $cabang : $erropesan"; exit; }
        
        mysqli_query($cnit, $query_kota_inst);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT tmp_importprodfile_ipms ekota $cabang : $erropesan"; exit; }
        echo "IT. Data ekota baru yang berhasil diinput : " . mysqli_affected_rows($cnit)."<br/>";
        
    }
    //END IT
    */
    
    
    
    $query_kta_ = "SELECT DISTINCT KOTAID, NAMA FROM $dbname.tmp_importprodfile_ipms";
    $tampil_kt= mysqli_query($cnmy, $query_kta_);
    while ($data1= mysqli_fetch_array($tampil_kt)) {
        $nama=$data1['NAMA'];
        $kotaid=$data1['KOTAID'];
        
        //$pinst_dt_kt[] = "('$distributor', '$cabang', '$kotaid', '$nama', 'Y', 'Y')";
        
        $query_kota_inst = "INSERT INTO MKT.ekota(distid,cabangid,ekotaid,nama,aktif,oldflag) VALUES "
                . " ('$distributor', '$cabang', '$kotaid', '$nama', 'Y', 'Y')";
        
        mysqli_query($cnmy, $query_kota_inst);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT tmp_importprodfile_ipms ekota $cabang : $erropesan"; exit; }
        //echo "Data ekota baru yang berhasil diinput : " . mysqli_affected_rows($cnmy)."<br/>";
        
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_kota_inst);
            //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT tmp_importprodfile_ipms ekota $cabang : $erropesan"; exit; }
            //echo "IT. Data ekota baru yang berhasil diinput : " . mysqli_affected_rows($cnit)."<br/>";
        }
        
    }
    
    
    /*
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
    */
    
    


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

    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importprodfile_ipms ($qrysektor)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER tmp_importprodfile_ipms esektor $cabang : $erropesan"; exit; }
    
    
    $query_skt_del = "DELETE FROM $dbname.tmp_importprodfile_ipms WHERE CONCAT('$distributor', IFNULL(SEKTORID,'')) IN "
            . " (SELECT CONCAT(IFNULL(distid,''), IFNULL(esektorid,'')) FROM MKT.esektor WHERE distid='$distributor')";
    mysqli_query($cnmy, $query_skt_del);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE tmp_importprodfile_ipms esektor $cabang : $erropesan"; exit; }
    
    
    $query_skt_inst = "INSERT INTO MKT.esektor(distid,esektorid,nama,aktif,oldflag) "
            . "SELECT DISTINCT '$distributor', SEKTORID, SEKTORNM, 'Y', 'Y' "
            . " FROM $dbname.tmp_importprodfile_ipms";
    mysqli_query($cnmy, $query_skt_inst);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT tmp_importprodfile_ipms esektor $cabang : $erropesan"; exit; }
    echo "Data esektor baru yang berhasil diinput : " . mysqli_affected_rows($cnmy)."<br/>";
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
        mysqli_query($cnit, "create table $dbname.tmp_importprodfile_ipms ($qrysektor)");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER tmp_importprodfile_ipms esektor $cabang : $erropesan"; exit; }
        
        mysqli_query($cnit, $query_skt_del);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE tmp_importprodfile_ipms esektor $cabang : $erropesan"; exit; }
        
        mysqli_query($cnit, $query_skt_inst);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT tmp_importprodfile_ipms esektor $cabang : $erropesan"; exit; }
        echo "IT. Data esektor baru yang berhasil diinput : " . mysqli_affected_rows($cnit)."<br/>";
        
    }
    //END IT
    
    
    /*
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

    */
    
    

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

    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importprodfile_ipms ($qrybrg)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER tmp_importprodfile_ipms eproduk $cabang : $erropesan"; exit; }
    
    $query_prod_del = "DELETE FROM $dbname.tmp_importprodfile_ipms WHERE CONCAT('$distributor', IFNULL(BRGID,'')) IN "
            . " (SELECT CONCAT(IFNULL(distid,''), IFNULL(eprodid,'')) FROM MKT.eproduk WHERE distid='$distributor')";
    mysqli_query($cnmy, $query_prod_del);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE tmp_importprodfile_ipms eproduk $cabang : $erropesan"; exit; }
    
    
    $query_prod_inst = "INSERT INTO MKT.eproduk(distid,eprodid,nama,satuan,aktif,oldflag) "
            . "SELECT DISTINCT '$distributor', BRGID, BRGNM, UNITPCS, 'Y', 'Y' "
            . " FROM $dbname.tmp_importprodfile_ipms";
    mysqli_query($cnmy, $query_prod_inst);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT tmp_importprodfile_ipms eproduk $cabang : $erropesan"; exit; }
    echo "Data eproduk baru yang berhasil diinput : " . mysqli_affected_rows($cnmy)."<br/>";
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
        mysqli_query($cnit, "create table $dbname.tmp_importprodfile_ipms ($qrybrg)");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER tmp_importprodfile_ipms eproduk $cabang : $erropesan"; exit; }
        
        mysqli_query($cnit, $query_prod_del);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE tmp_importprodfile_ipms eproduk $cabang : $erropesan"; exit; }
    
        mysqli_query($cnit, $query_prod_inst);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT tmp_importprodfile_ipms eproduk $cabang : $erropesan"; exit; }
        echo "IT. Data eproduk baru yang berhasil diinput : " . mysqli_affected_rows($cnit)."<br/>";
    
    }
    //END IT
    
    
    /*
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
    */

    
    
    
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
    
    
    
    $pinsertsave=false;
    //delete asalnya disini
    
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
        
		$pnodpl=$data1['NODPL'];
                
		$pnoidretur=$data1['XNOJUAL'];
		$pketretur=$data1['KET'];
            
        $pinst_sls_data[] = "('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$qbonus','$nojual', '$pnodpl', '$pnoidretur', '$pketretur')";
        $pinsertsave=true;
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
            
            
        /*
        //echo "$cabang, $custid, $tgljual, $brgid, $harga, $qbeli, $qbonus, $nojual<br/>";
        $eksekusi=mysqli_query($cnmy, "
            INSERT INTO $dbname.salesspp(cabangid,custid,tgljual,brgid,harga,qbeli,qbonus,fakturid) 
            VALUES('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$qbonus','$nojual')
        ");
        
        if ($eksekusi){
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
        }
        */
        
        
    }
    
    mysqli_query($cnmy, "DELETE FROM $dbname.salesspp WHERE LEFT(tgljual,7) = '$bulan' AND cabangid = '$cabang' AND subdist = ''");
    if ($pinsertsave==true) {
        $query_sls_ins = "INSERT INTO $dbname.salesspp(cabangid,custid,tgljual,brgid,harga,qbeli,qbonus,fakturid, nodpl, xretur, ket_retur) VALUES "
                . "".implode(', ', $pinst_sls_data);

        mysqli_query($cnmy, $query_sls_ins);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSER salesspp : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, "DELETE FROM $dbname.salesspp WHERE LEFT(tgljual,7) = '$bulan' AND cabangid = '$cabang' AND subdist = ''");
            
            mysqli_query($cnit, $query_sls_ins);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSER salesspp : $erropesan"; exit; }
        }
        //END IT
        
    }
    
    echo "<br>
    <hr><b>Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>sekian dan terimakasih</b>
    <br>";

    
    
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

    
    //IT
    if ($plogit_akses==true) {
        if($cabang == 'BDG'){
          // -------------------------------- BADNUNG
          mysqli_query($cnit, "Delete from $dbname.spp_msbar_bdg");
          mysqli_query($cnit, "Delete from $dbname.spp_mscust_bdg");
          mysqli_query($cnit, "Delete from $dbname.spp_mssales_bdg");
          mysqli_query($cnit, "Delete from $dbname.spp_mskota_bdg");
          mysqli_query($cnit, "Delete from $dbname.spp_mssekt_bdg");
        }elseif($cabang == 'YOG'){
          // -------------------------------- Yogya
          mysqli_query($cnit, "Delete from $dbname.spp_msbar_diy");
          mysqli_query($cnit, "Delete from $dbname.spp_mscust_diy");
          mysqli_query($cnit, "Delete from $dbname.spp_mssales_diy");
          mysqli_query($cnit, "Delete from $dbname.spp_mskota_diy");
          mysqli_query($cnit, "Delete from $dbname.spp_mssekt_diy");
        }elseif($cabang == 'HO2'){
          // -------------------------------- HO2
          mysqli_query($cnit, "Delete from $dbname.spp_msbar_ho");
          mysqli_query($cnit, "Delete from $dbname.spp_mscust_ho");
          mysqli_query($cnit, "Delete from $dbname.spp_mssales_ho");
          mysqli_query($cnit, "Delete from $dbname.spp_mskota_ho");
          mysqli_query($cnit, "Delete from $dbname.spp_mssekt_ho");
        }elseif($cabang == 'HO'){
          // -------------------------------- HO
          mysqli_query($cnit, "Delete from $dbname.spp_msbar_jkt");
          mysqli_query($cnit, "Delete from $dbname.spp_mscust_jkt");
          mysqli_query($cnit, "Delete from $dbname.spp_mssales_jkt");
          mysqli_query($cnit, "Delete from $dbname.spp_mskota_jkt");
          mysqli_query($cnit, "Delete from $dbname.spp_mssekt_jkt");
        }elseif($cabang == 'MLG'){
          // -------------------------------- MALANG
          mysqli_query($cnit, "Delete from $dbname.spp_msbar_mlg");
          mysqli_query($cnit, "Delete from $dbname.spp_mscust_mlg");
          mysqli_query($cnit, "Delete from $dbname.spp_mssales_mlg");
          mysqli_query($cnit, "Delete from $dbname.spp_mskota_mlg");
          mysqli_query($cnit, "Delete from $dbname.spp_mssekt_mlg");
        }elseif($cabang == 'SBY'){
          // -------------------------------- Surabaya
          mysqli_query($cnit, "Delete from $dbname.spp_msbar_sby");
          mysqli_query($cnit, "Delete from $dbname.spp_mscust_sby");
          mysqli_query($cnit, "Delete from $dbname.spp_mssales_sby");
          mysqli_query($cnit, "Delete from $dbname.spp_mskota_sby");
          mysqli_query($cnit, "Delete from $dbname.spp_mssekt_sby");
        }elseif($cabang == 'SOL'){
          // -------------------------------- Solo
          mysqli_query($cnit, "Delete from $dbname.spp_msbar_slo");
          mysqli_query($cnit, "Delete from $dbname.spp_mscust_slo");
          mysqli_query($cnit, "Delete from $dbname.spp_mssales_slo");
          mysqli_query($cnit, "Delete from $dbname.spp_mskota_slo");
          mysqli_query($cnit, "Delete from $dbname.spp_mssekt_slo");
        }elseif($cabang == 'SMG'){
          // -------------------------------- Semarang
          mysqli_query($cnit, "Delete from $dbname.spp_msbar_smg");
          mysqli_query($cnit, "Delete from $dbname.spp_mscust_smg");
          mysqli_query($cnit, "Delete from $dbname.spp_mssales_smg");
          mysqli_query($cnit, "Delete from $dbname.spp_mskota_smg");
          mysqli_query($cnit, "Delete from $dbname.spp_mssekt_smg");
        }else{
          echo 'Tidak Ada Cabang ';
          return;
        }
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