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

    
    
    include "ceknamadist.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];

    
    
    
    $target_dir = "../../fileupload/";
    $pdist=$_POST['uiddist'];
    $ptgl=$_POST['ubln'];
    $pnmfolder=$_POST['upilfolder'];
    
    $_SESSION['MSTIMPPERTPIL']=$ptgl;
    $_SESSION['MSTIMPDISTPIL']=$pdist;
    $_SESSION['MSTIMPFOLDPIL']=$pnmfolder;
            
    $pname_foder_dist=CekNamaDist($pdist);
    $pbulan =  date("Ym", strtotime($ptgl));
    $bulan =  date("Y-m", strtotime($ptgl));
    
    $target_dir .=$pbulan."/";
    $target_dir .=$pname_foder_dist."/".$pnmfolder."/";
    
    //ubah juga di prosesdata_
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    
    /* memanggil file DBF untuk kita Buka */
    
    $pnamatext_prod="";
    $pnamatext_cus="";
    $pnamatext_sls="";
    if (is_dir($target_dir)){
        
        if ($dh = opendir($target_dir)){
            
            while (($pfilerar = readdir($dh)) !== false){
                if (!empty($pfilerar) && $pfilerar!="." && $pfilerar!="..") {
                    $path = pathinfo($target_dir.$pfilerar);
                    $ext = $path['extension'];
                    if ($ext=="txt") {
                        if (!empty($pfilerar)) {
                            $pnamatxt= TRIM(strtoupper(substr(TRIM($pfilerar),0,9)));
                            
                            if ($pnamatxt=="PENTA_PRO") $pnamatext_prod=$pfilerar;
                            if ($pnamatxt=="PENTA_CUS") $pnamatext_cus=$pfilerar;
                            if ($pnamatxt=="PENTA_SLS") $pnamatext_sls=$pfilerar;
                            
                        }
                    }
                }
            }
            
            closedir($dh);
            
        }
        
    }
    
    $pjmlmsbar=0;
    $pjmlcustomer=0;
    $pjmljual=0;
    
    
    //mysqli_query($cnmy, "ALTER TABLE $dbname.pv_import_produk DISABLE KEYS");
    //mysqli_query($cnmy, "ALTER TABLE $dbname.pv_import_cust DISABLE KEYS");
    //mysqli_query($cnmy, "ALTER TABLE $dbname.pv_import_sales DISABLE KEYS");
    
        //mysqli_query($cnmy, "LOCK TABLES $dbname.pv_import_sales WRITE");
        //mysqli_query($cnmy, "UNLOCK TABLES");
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.pv_import_produk");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE pv_import_produk : $erropesan"; exit; }
    mysqli_query($cnmy, "DELETE FROM $dbname.pv_import_cust");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE pv_import_cust : $erropesan"; exit; }
    mysqli_query($cnmy, "DELETE FROM $dbname.pv_import_sales");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE pv_import_sales : $erropesan"; exit; }
    
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.pv_import_produk");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE pv_import_produk : $erropesan"; exit; }
        mysqli_query($cnit, "DELETE FROM $dbname.pv_import_cust");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE pv_import_cust : $erropesan"; exit; }
        mysqli_query($cnit, "DELETE FROM $dbname.pv_import_sales");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE pv_import_sales : $erropesan"; exit; }
    }
    //END IT
    
    
    
    
    if (!empty($pnamatext_prod)) {    
        $no=0;
        $open = fopen($target_dir.$pnamatext_prod,'r');
        while (!feof($open)) {
            
            $getTextLine = fgets($open);
            if (empty($getTextLine)) { $no++; continue; }
            $explodeLine = explode("|",$getTextLine);
                
            if ((double)$no==0) { $no++; continue; }

            list($pfile0,$pfile1,$pfile2,$pfile3,$pfile4,$pfile5) = $explodeLine;

            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            if (!empty($pfile2)) $pfile2 = str_replace("'", " ", $pfile2);
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            
            $query_parts_prod[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5')";
            
            $query = "INSERT INTO $dbname.pv_import_produk (PROD_ID, PROD_NAME, PROD_UOM_PRIN, PROD_GRP_PRIN, "
                    . " PROD_HNA_PRIN, PROD_HNA)values "
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5')";
            //mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { fclose($open); mysqli_close($cnmy); echo "Error INSERT PRODUK : $erropesan"; exit; }
            
            $no++;
        }
        
        
        $queryprod = "INSERT INTO $dbname.pv_import_produk (PROD_ID, PROD_NAME, PROD_UOM_PRIN, PROD_GRP_PRIN, "
                . " PROD_HNA_PRIN, PROD_HNA)values "
                . " ".implode(', ', $query_parts_prod);
        mysqli_query($cnmy, $queryprod);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { fclose($open); mysqli_close($cnmy); echo "Error INSERT PRODUK : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $queryprod);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { fclose($open); mysqli_close($cnit); echo "IT... Error INSERT PRODUK : $erropesan"; exit; }
        }
        //END IT
        
        
        
        fclose($open);
    }
    
    
    
    if (!empty($pnamatext_cus)) {    
        $no=0;
        $open = fopen($target_dir.$pnamatext_cus,'r');
        while (!feof($open)) {
            
            $getTextLine = fgets($open);
            if (empty($getTextLine)) { $no++; continue; }
            $explodeLine = explode("|",$getTextLine);
                
            if ((double)$no==0) { $no++; continue; }

            list($pfile0,$pfile1,$pfile2,$pfile3,$pfile4,$pfile5,$pfile6,$pfile7,$pfile8) = $explodeLine;

            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            if (!empty($pfile2)) $pfile2 = str_replace("'", " ", $pfile2);
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            if (!empty($pfile5)) $pfile5 = str_replace("'", " ", $pfile5);
            if (!empty($pfile6)) $pfile6 = str_replace("'", " ", $pfile6);
            if (!empty($pfile7)) $pfile7 = str_replace("'", " ", $pfile7);
			
			if (!empty($pfile8)) $pfile8 = str_replace("'", " ", $pfile8);
			
			if (empty($pfile1)) $pfile1=$pfile8;

            $query_parts_cust[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7')";
            
            $query = "INSERT INTO $dbname.pv_import_cust (BRANCH_ID, CUST_SHIP_ID, CUST_NAME, CUST_ADDR1, "
                    . " CUST_ADDR2, CUST_CITY, CUST_POSTAL_CODE, CUST_TYPE_ID)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7')";
            //mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { fclose($open); mysqli_close($cnmy); echo "Error INSERT CUSTOMER : $erropesan"; exit; }
            
            $no++;
        }
        
        $querycust = "INSERT INTO $dbname.pv_import_cust (BRANCH_ID, CUST_SHIP_ID, CUST_NAME, CUST_ADDR1, "
                . " CUST_ADDR2, CUST_CITY, CUST_POSTAL_CODE, CUST_TYPE_ID)values "
                . " ".implode(', ', $query_parts_cust);
        
        mysqli_query($cnmy, $querycust);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { fclose($open); mysqli_close($cnmy); echo "Error INSERT CUSTOMER : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $querycust);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { fclose($open); mysqli_close($cnit); echo "IT... Error INSERT CUSTOMER : $erropesan"; exit; }
        }
        //END IT
        
        
        
        fclose($open);
    }
    
    
    
    if (!empty($pnamatext_sls)) {    
        $no=0;
        $open = fopen($target_dir.$pnamatext_sls,'r');

        while (!feof($open)) {
            
            $getTextLine = fgets($open);
            if (empty($getTextLine)) { $no++; continue; }
            $explodeLine = explode("|",$getTextLine);
                
            if ((double)$no==0) { $no++; continue; }

            list($pfile0,$pfile1,$pfile2,$pfile3,$pfile4,$pfile5,$pfile6,$pfile7,$pfile8,$pfile9,$pfile10,$pfile11,$pfile12,$pfile13,$pfile14,$pfile15) = $explodeLine;

            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            if (!empty($pfile2)) $pfile2 = str_replace("'", " ", $pfile2);
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            
            if (!empty($pfile8)) $pfile8 = str_replace("'", " ", $pfile8);
            if (!empty($pfile9)) $pfile9 = str_replace("'", " ", $pfile9);
            
            if (!empty($pfile14)) $pfile14 = str_replace("'", " ", $pfile14);
			
			if (!empty($pfile15)) $pfile15 = str_replace("'", " ", $pfile15);
			
			
			if (empty($pfile1)) $pfile1=$pfile15;

    
            echo "$pfile5, $pfile6, $pfile7, $pfile8, $pfile9, $pfile10<br/>";
                        
            $query_parts_sls[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', "
                    . " '$pfile8', '$pfile9', '$pfile10', '$pfile11', '$pfile12', '$pfile13', '$pfile14')";
            
            $query = "INSERT INTO $dbname.pv_import_sales (BRANCH_ID, CUSTOMER_ID, PANEL_ID, INV_DATE, "
                    . " PRODUCT_ID, SELL_PRICE, QTY_SOLD, QTY_BONUS, "
                    . " INV_NO, SECTOR_ID, QTY_BNS_SDM, QTY_BNS_CONV, TOT_QTY_BNS, NETT_QTY_SOLD, f15)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', "
                    . " '$pfile8', '$pfile9', '$pfile10', '$pfile11', '$pfile12', '$pfile13', '$pfile14')";
            //mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { fclose($open); mysqli_close($cnmy); echo "Error INSERT SLS : $erropesan"; exit; }
            

            
            $no++;
        }
        
        
        $querysls = "INSERT INTO $dbname.pv_import_sales (BRANCH_ID, CUSTOMER_ID, PANEL_ID, INV_DATE, "
                . " PRODUCT_ID, SELL_PRICE, QTY_SOLD, QTY_BONUS, "
                . " INV_NO, SECTOR_ID, QTY_BNS_SDM, QTY_BNS_CONV, TOT_QTY_BNS, NETT_QTY_SOLD, f15)"
                . " VALUES ".implode(', ', $query_parts_sls);
        mysqli_query($cnmy, $querysls);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { fclose($open); mysqli_close($cnmy); echo "Error INSERT SLS : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $querysls);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { fclose($open); mysqli_close($cnit); echo "IT... Error INSERT SLS : $erropesan"; exit; }
        }
        //END IT
        
        
        fclose($open);
    }
    
    
    
    $query = "select * from $dbname.pv_import_produk";
    $tmapilprod= mysqli_query($cnmy, $query);
    $pjmlmsbar = mysqli_num_rows($tmapilprod);
    
    $query = "select * from $dbname.pv_import_cust";
    $tmapilcust= mysqli_query($cnmy, $query);
    $pjmlcustomer = mysqli_num_rows($tmapilcust);
    
    $query = "select * from $dbname.pv_import_sales";
    $tmapiljual= mysqli_query($cnmy, $query);
    $pjmljual = mysqli_num_rows($tmapiljual);
    
    
    
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 4);
      

    
    //mysqli_query($cnmy, "ALTER TABLE $dbname.pv_import_produk ENABLE KEYS;");
    //mysqli_query($cnmy, "ALTER TABLE $dbname.pv_import_cust ENABLE KEYS;");
    //mysqli_query($cnmy, "ALTER TABLE $dbname.pv_import_sales ENABLE KEYS;");
    
    
    $query = "SELECT * FROM $dbname.pv_import_sales WHERE LEFT(STR_TO_DATE(INV_DATE,'%d-%b-%Y'),7)<>'$bulan'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);    
    
    if ($ketemu>0) {
        echo "<div style='color:red;'><h1>Import tidak sesuai periode</h1></div>";
    }
    
    
    echo "<b>Jumlah Proses Import : </b><br/>";
    echo "1. Jml Produk : $pjmlmsbar<br/>";
    echo "2. Jml Customer : $pjmlcustomer<br/>";
    echo "3. Jml Sales : $pjmljual<br/>";
    echo "Selesai dalam ".$total_time." detik";
    
    
    
    
    mysqli_close($cnmy);
    
    //IT
    if ($plogit_akses==true) {
        mysqli_close($cnit);
    }
    
    
    
?>