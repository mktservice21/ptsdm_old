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
    
    
    $cabang="DPS";
    $subdist="MAS";
    $distributor="0000000016";
    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    
    
    if ($distributor!="0000000016") {
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
    
    
     echo "$distributor . $cabang . $bulan . $subdist.<br/>";
    // die();
    $totalcust=0;
    // customer
    
    
    $qrycust="
        SELECT DISTINCT CUSTID,CUSTNAME CUSTNM 
        FROM $dbname.import_mas
    ";
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importprodfile_ipms ($qrycust)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error create tmp_importprodfile_ipms mscust $cabang : $erropesan"; exit; }
    
    $query_del = "DELETE FROM $dbname.tmp_importprodfile_ipms WHERE CONCAT('$distributor', '$cabang', IFNULL(CUSTID,'')) IN "
            . " (SELECT CONCAT(IFNULL(distid,''), IFNULL(cabangid,''), IFNULL(ecustid,'')) FROM MKT.ecust WHERE distid='$distributor' AND cabangid='$cabang')";
    mysqli_query($cnmy, $query_del);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE tmp_importprodfile_ipms mscust $cabang : $erropesan"; exit; }
    
    $pinsertsave=false;
    $qrycust="
        SELECT DISTINCT CUSTID, CUSTNM 
        FROM $dbname.tmp_importprodfile_ipms
    ";
    
    $tampil_cu= mysqli_query($cnmy, $qrycust);
    while ($data1= mysqli_fetch_array($tampil_cu)) {
        
        $ecust=$data1['CUSTID'];
        $enama=mysqli_real_escape_string($cnmy, $data1['CUSTNM']);
        // $alamat=mysqli_real_escape_string($cnmy, $data1['ALAMAT']);
        
        $pinsert_cust_data[] = "('$distributor','$cabang','$ecust','$enama','Y','Y','$subdist')";
        $pinsertsave=true;
        $totalcust=$totalcust+1;
        
        /*
        $cekcust=mysqli_fetch_array(mysqli_query($cnmy, "
        select count(ecustid) from MKT.ecust where distid='$distributor' and cabangid='$cabang' and ecustid='$ecust' and nama='$enama'"));
        $cekcust1=$cekcust[0];
        if ($cekcust1<1){
            mysqli_query($cnmy, "
                insert into MKT.ecust(distid,cabangid,ecustid,nama,oldflag,aktif,subdist) values 
                ('$distributor','$cabang','$ecust','$enama','Y','Y','$subdist')
            ");

            echo "berhasil input cust baru -> $cabang - $ecust - $enama - $alamat <br>";
            $totalcust=$totalcust+1;
        }
        */
    }
    
    if ($pinsertsave==true) {
        $query_pros_cust = "insert into MKT.ecust(distid,cabangid,ecustid,nama,oldflag,aktif,subdist) values "
                . " ".implode(', ', $pinsert_cust_data);
        mysqli_query($cnmy, $query_pros_cust);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT ecust : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_pros_cust);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT ecust : $erropesan"; exit; }
        }
        //END IT
    }
    
    echo "Total Customer baru yg berhasil diinput: $totalcust<br><hr><br>";

    
    $qrysales="SELECT *, CAST(0 as DECIMAL(20,2)) as hna_mkt FROM $dbname.import_mas WHERE left(TGLJUAL,7) = '$bulan'";
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importprodfile_ipms ($qrysales)");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error create tmp_importprodfile_ipms sales $cabang : $erropesan"; exit; }
    
    $query="update $dbname.tmp_importprodfile_ipms a JOIN "
            . " (SELECT e.distid, e.`eProdId`, i.`hna` FROM MKT.eproduk e INNER JOIN MKT.iproduk i ON e.`iProdId` = i.`iProdId` "
            . " WHERE e.distid='$distributor') as b "
            . " on a.BRGID=b.eProdId AND '$distributor'=b.distid SET "
            . " a.hna_mkt=b.hna";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error UPDATE tmp_importprodfile_ipms prod $cabang : $erropesan"; exit; }
    
    //mysqli_close($cnmy); exit;
    
    // sales
    $totalsalesqty=0;
    $totalsalessum=0;
    mysqli_query($cnmy, "delete from $dbname.salesmas where left(tgljual,7)='$bulan' and cabangid='$cabang'");
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, "delete from $dbname.salesmas where left(tgljual,7)='$bulan' and cabangid='$cabang'");
        }
        //END IT
    $qrysales="
        SELECT 
        CUSTID,TGLJUAL,FAKTURID NOJUAL,BRGID,QTY QBELI,HNA harga, hna_mkt  
        FROM $dbname.tmp_importprodfile_ipms
        
    ";//WHERE left(TGLJUAL,7) = '$bulan'
    
    $pinsertsave=false;
    
    $tampil_sl= mysqli_query($cnmy, $qrysales);
    while ($data1= mysqli_fetch_array($tampil_sl)) {
        
        $custid=$data1['CUSTID'];
        $nojual=$data1['NOJUAL'];
        $brgid=$data1['BRGID'];
        $tgljual=$data1['TGLJUAL'];
        
        
        
        $harga=$data1['hna_mkt'];
        $qbeli=$data1['QBELI'];
        $totale=$harga*$qbeli;
        
        $pinsert_sls_data[] = "('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual')";
        $pinsertsave=true;
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
            
        /*
        $harga0=mysqli_fetch_array(mysqli_query($cnmy, "
            SELECT i.`hna` FROM MKT.eproduk e INNER JOIN MKT.iproduk i ON e.`iProdId` = i.`iProdId` WHERE e.`eProdId` = '$brgid'  and  e.distid='$distributor'
        "));
        
        $harga=$harga0[0];
        $qbeli=$data1['QBELI'];
        $totale=$harga*$qbeli;

        // echo $cabang."-".$custid."-".$bulan."-".$tgljual."-".$nojual."-".$brgid."-".$qbeli."-".$harga."<br>";
        $insert=mysqli_query($cnmy, "
            INSERT INTO $dbname.salesmas(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid) VALUES 
         ('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual')
        ");

        if ($insert){
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
        }
         * 
         */
        
        
    }
    
    if ($pinsertsave==true) {
        
        $query_pros_sls = "INSERT INTO $dbname.salesmas(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid) VALUES "
                . " ".implode(', ', $pinsert_sls_data);
        mysqli_query($cnmy, $query_pros_sls);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT salesmas : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_pros_sls);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT salesmas : $erropesan"; exit; }
        }
        //END IT
        
    }
    
    echo "Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>sekian dan terimakasih";
    
    
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