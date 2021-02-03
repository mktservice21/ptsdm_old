<?php
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
session_start();
$puser="";
if (isset($_SESSION['IDCARD'])) $puser=$_SESSION['IDCARD'];

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
    
    $subdist="DUM";
    $cabang='01';
    $distributor="0000000023";
    $ptgl=$_POST['ubln'];

    $ptgl=$_POST['ubln'];
    
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    $pakhirbulan =  date("Y-m-t", strtotime($ptgl));
    
    
    if ($distributor!="0000000023") {
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
    
    
    
    
$totalcust=0;
$qrycust="
SELECT `kd cust` custid,customer custnm,'' alamat 
FROM $dbname.import_dum
WHERE LEFT(tanggal,7) = '$bulan'
";
$tampil=mysqli_query($cnmy, $qrycust);
$ketemu= mysqli_num_rows($tampil);
if ((INT)$ketemu>0){
    while ($data1=mysqli_fetch_array($tampil)){
        $ecust=$data1['custid'];
        $enama=mysqli_real_escape_string($cnmy, $data1['custnm']);
        $alamat=mysqli_real_escape_string($cnmy, $data1['alamat']);
        $cekcust=mysqli_fetch_array(mysqli_query($cnmy, "
            SELECT COUNT(ecustid) FROM $dbname.ecust WHERE distid='$distributor' AND cabangid='$cabang' AND ecustid='$ecust'
        "));


        $cekcust1=$cekcust[0];
        if ($cekcust1<1){
            mysqli_query($cnmy, "
                INSERT INTO $dbname.ecust(distid,cabangid,ecustid,nama,alamat1,oldflag,aktif,subdist) 
                VALUES('$distributor','$cabang','$ecust','$enama','$alamat','Y','Y','$subdist')
            ");
            echo "berhasil input cust baru -> $cabang - $ecust - $enama - $alamat <br>";
            $totalcust=$totalcust+1;
        }
    }
}
echo "Total Customer baru yg berhasil diinput: $totalcust<br><hr><br>";


    
// sales
$dpl="";
$totalsalesqty=0;
$totalsalessum=0;
unset($pinsert_data_sls);//kosongkan array
$isimpan=false;

mysqli_query($cnmy, "delete from $dbname.salesdum where left(tgljual,7)='$bulan'");
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE salesdum : $erropesan"; exit; }

$qrysales="
    SELECT '01' cabangid,`kd cust` custid,tanggal tgljual,`kode barang` brgid,harga,qty qbeli,'0' qbonus,`no faktur` fakturid,'0' dpl 
    FROM $dbname.import_dum
    WHERE LEFT(tanggal,7) = '$bulan'
    ";
// echo $qrysales.'<br>';


$tampilsls=mysqli_query($cnmy, $qrysales);
$ketemusls= mysqli_num_rows($tampilsls);
if ((INT)$ketemusls>0){
    while ($data1=mysqli_fetch_array($tampilsls)){
        $custid=$data1['custid'];
        $nojual=$data1['fakturid'];
        $brgid=$data1['brgid'];
        $tgljual=$data1['tgljual'];
        $harga0=mysqli_fetch_array(mysqli_query($cnmy, "
            SELECT i.`hna` 
            FROM $dbname.eproduk e            
            INNER JOIN $dbname.iproduk i ON e.`iProdId` = i.`iProdId` 
            WHERE e.`eProdId` = '$brgid' and  e.distid='0000000023'
        "));
        $harga=$harga0[0];
        $qbeli=$data1['qbeli'];
        $totale=$harga*$qbeli;
        
        
        // echo $cabang.'~'.$custid.'~'.$nojual.'~'.$custid.'~'.$brgid.'~'.$tgljual.'~'.$harga.'~'.$qbeli.'<br>';
        /*
        $insert=mysqli_query($cnmy, "
            INSERT INTO $dbname.salesdum(cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,dpl) 
            VALUES('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$dpl')");
        if ($insert)
        {
            $totalsalesqty=$totalsalesqty+1;
            $totalsalessum=$totalsalessum+$totale;
        }
        */
        $totalsalesqty=$totalsalesqty+1;
        $totalsalessum=$totalsalessum+$totale;
            
        $pinsert_data_sls[] = "('$cabang','$custid','$tgljual','$brgid','$harga','$qbeli','$nojual','$dpl')";
        $isimpan=true;
        
    }
}

if ($isimpan==true) {
    $query_ins_pil_sls = "INSERT INTO $dbname.salesdum (cabangid,custid,tgljual,brgid,harga,qbeli,fakturid,dpl) values "
            . " ".implode(', ', $pinsert_data_sls);

    mysqli_query($cnmy, $query_ins_pil_sls);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT salesdum : $erropesan"; exit; }
    

    echo "Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>sekian dan terimakasih<br>
    <hr>
    ";
}else{
    echo "<hr/>TIDAK ADA DATA SALES DUM YANG DI PROSES<hr/>";
}

//IT
if ($plogit_akses==true) {

    
    $totalcust=0;
    $qrycust="
    SELECT `kd cust` custid,customer custnm,'' alamat 
    FROM $dbname.import_dum
    WHERE LEFT(tanggal,7) = '$bulan'
    ";
    $tampil=mysqli_query($cnit, $qrycust);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0){
        while ($data1=mysqli_fetch_array($tampil)){
            $ecust=$data1['custid'];
            $enama=mysqli_real_escape_string($cnit, $data1['custnm']);
            $alamat=mysqli_real_escape_string($cnit, $data1['alamat']);
            $cekcust=mysqli_fetch_array(mysqli_query($cnit, "
                SELECT COUNT(ecustid) FROM $dbname.ecust WHERE distid='$distributor' AND cabangid='$cabang' AND ecustid='$ecust'
            "));


            $cekcust1=$cekcust[0];
            if ($cekcust1<1){
                mysqli_query($cnit, "
                    INSERT INTO $dbname.ecust(distid,cabangid,ecustid,nama,alamat1,oldflag,aktif,subdist) 
                    VALUES('$distributor','$cabang','$ecust','$enama','$alamat','Y','Y','$subdist')
                ");
                echo "IT... berhasil input cust baru -> $cabang - $ecust - $enama - $alamat <br>";
                $totalcust=$totalcust+1;
            }
        }
    }

    echo "IT... Total Customer baru yg berhasil diinput: $totalcust<br><hr><br>";
    
    
    
    //sales
    mysqli_query($cnit, "delete from $dbname.salesdum where left(tgljual,7)='$bulan'");
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT salesdum : $erropesan"; exit; }
    if ($isimpan==true) {
        mysqli_query($cnit, $query_ins_pil_sls);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT salesdum : $erropesan"; exit; }
        
        echo "IT... Total penjualan yg berhasil diinput: $totalsalessum , dengan jumlah no faktur sebanyak $totalsalesqty.<br>sekian dan terimakasih<br>
        <hr>
        ";
    }
    
}
//END IT
    
    
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
