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
    
    $target_dir .=$pbulan."/";
    $target_dir .=$pname_foder_dist."/".$pnmfolder."/";
    
    //ubah juga yang di proesdata_
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    
    /* memanggil file DBF untuk kita Buka */
    
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.msbar");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DROP TABLE MSBAR : $erropesan"; exit; }
    
    
    mysqli_query($cnmy, "CREATE TABLE $dbname.msbar (BRGID VARCHAR(10), BRGNM VARCHAR(40), BRGID_SUB VARCHAR(15), BRGNM_SUB VARCHAR(40), UNITPCS VARCHAR(10))");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE TABLE MSBAR : $erropesan"; exit; }
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.msbar");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE MSBAR : $erropesan"; exit; }
    
    
    //IT
    if ($plogit_akses==true) {
        
        mysqli_query($cnit, "DROP TABLE IF EXISTS $dbname.msbar");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DROP TABLE MSBAR : $erropesan"; exit; }
        
        mysqli_query($cnit, "CREATE TABLE $dbname.msbar (BRGID VARCHAR(10), BRGNM VARCHAR(40), BRGID_SUB VARCHAR(15), BRGNM_SUB VARCHAR(40), UNITPCS VARCHAR(10))");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error CREATE TABLE MSBAR : $erropesan"; exit; }
        
        mysqli_query($cnit, "DELETE FROM $dbname.msbar");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE MSBAR : $erropesan"; exit; }
        
    }   
    //END IT
    
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'msbar.dbf',0) or die("Error! Could not open dbase database file MSBAR");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        
        
        
        for ($ind=1;$ind<=$jum_record;$ind++){
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=TRIM($record[0]);
            $pfile1=TRIM($record[1]);
            $pfile2=TRIM($record[2]);
            $pfile3=TRIM($record[3]);
            $pfile4=TRIM($record[4]);
            
            
            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            
            
            $query = "INSERT INTO $dbname.msbar (BRGID, BRGNM, BRGID_SUB, BRGNM_SUB, "
                    . " UNITPCS)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4')";
            
            //echo $query; dbase_close($pinsert); mysqli_close($cnmy); exit;
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT MSBAR : $erropesan"; exit; }
            //echo "$pfile0, $pfile1, $pfile2<br/>";
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT MSBAR : $erropesan"; exit; }
            }
            //END IT
            
        }
        
        
    }
    dbase_close($pinsert);
    
    $pjmlmsbar=$jum_record;
    
    
    
    
    
    /*
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.ms_x_cust");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DROP TABLE MSBAR : $erropesan"; exit; }
    
    
    mysqli_query($cnmy, "CREATE TABLE $dbname.ms_x_cust (CUSTID VARCHAR(20), CUSTNM VARCHAR(60), ALAMAT VARCHAR(100), KOTAID VARCHAR(10), KOTA VARCHAR(25), SEKTORID VARCHAR(3))");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE TABLE MSBAR : $erropesan"; exit; }
    */
    
    mysqli_query($cnmy, "DELETE FROM $dbname.subdist_mscust_bcm");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE CUSTOMER SDM : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.subdist_mscust_bcm");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE CUSTOMER SDM : $erropesan"; exit; }
    }
    //END IT
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'mscust.DBF',0) or die("Error! Could not open dbase database file CUSTOMER");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        
        
        
        for ($ind=1;$ind<=$jum_record;$ind++){
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=TRIM($record[0]);
            $pfile1=TRIM($record[1]);
            $pfile2=TRIM($record[2]);
            $pfile3=TRIM($record[3]);
            $pfile4=TRIM($record[4]);
            $pfile5=TRIM($record[5]);
            
            
            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            if (!empty($pfile2)) $pfile2 = str_replace("'", " ", $pfile2);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            
            
            $query = "INSERT INTO $dbname.subdist_mscust_bcm (CUSTID, CUSTNM, ALAMAT, KOTAID, "
                    . " KOTA, SEKTORID)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5')";
            
            //echo $query; dbase_close($pinsert); mysqli_close($cnmy); exit;
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT CUST SDM : $erropesan"; exit; }
            //echo "$pfile0, $pfile1, $pfile2<br/>";
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT CUST SDM : $erropesan"; exit; }
            }
            //END IT
            
            
        }
        
        
    }
    dbase_close($pinsert);
    
    $pjmlcustomer=$jum_record;
    
    
    
    
    
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.mssales");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DROP TABLE SALES : $erropesan"; exit; }
    
    
    mysqli_query($cnmy, "CREATE TABLE $dbname.mssales (CUSTID VARCHAR(15), TGLJUAL date, NOJUAL VARCHAR(20), BRGID VARCHAR(5), HARGA DOUBLE, QBELI DOUBLE, QBONUS DOUBLE, DPL DOUBLE, _NullFlags longtext, NODPL VARCHAR(100))");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE TABLE MSBAR : $erropesan"; exit; }
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.mssales");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE SALES SDM : $erropesan"; exit; }
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DROP TABLE IF EXISTS $dbname.mssales");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DROP TABLE SALES : $erropesan"; exit; }


        mysqli_query($cnit, "CREATE TABLE $dbname.mssales (CUSTID VARCHAR(15), TGLJUAL date, NOJUAL VARCHAR(20), BRGID VARCHAR(5), HARGA DOUBLE, QBELI DOUBLE, QBONUS DOUBLE, DPL DOUBLE, _NullFlags longtext, NODPL VARCHAR(100))");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error CREATE TABLE MSBAR : $erropesan"; exit; }


        mysqli_query($cnit, "DELETE FROM $dbname.mssales");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE SALES SDM : $erropesan"; exit; }
    }
    //END IT
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'sales.dbf',0) or die("Error! Could not open dbase database file SALES");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        
        
        
        for ($ind=1;$ind<=$jum_record;$ind++){
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=TRIM($record[0]);
            $pfile1=TRIM($record[1]);
            $pfile2=TRIM($record[2]);
            $pfile3=TRIM($record[3]);
            $pfile4=$record[4];
            $pfile5=$record[5];
            $pfile6=$record[6];
            $pfile7=$record[7];
            //$pfile8=$record[8];
            
            //if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            
			$pfile10="";//NODPL
			
			//untuk promo
			if ($pfile3=="01524") {
				if (empty($pfile4)) $pfile4==0;
				if (empty($pfile5)) $pfile5==0;
				if ((DOUBLE)$pfile4<>0) {
					$pfile4=(DOUBLE)$pfile4/3;
					$pfile5=(DOUBLE)$pfile5*3;
					$pfile10="promo 3in1 glikoderm lumineux";
				}
			}
            
            $query = "INSERT INTO $dbname.mssales (CUSTID, TGLJUAL, NOJUAL, BRGID, "
                    . " HARGA, QBELI, QBONUS, DPL, NODPL)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile10')";
            
            //echo $query; dbase_close($pinsert); mysqli_close($cnmy); exit;
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT SALES SDM : $erropesan"; exit; }
            //echo "$pfile0, $pfile1, $pfile2<br/>";
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT SALES SDM : $erropesan"; exit; }
            }
            //END IT
            
            
        }
        
        
    }
    dbase_close($pinsert);
    
    $pjmljual=$jum_record;
    
    
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 4);
    
    
    $query = "SELECT * FROM $dbname.mssales WHERE DATE_FORMAT(TGLJUAL,'%Y%m')<>'$pbulan'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);    
    
    if ($ketemu>0) {
        echo "<div style='color:red;'><h1>Import tidak sesuai periode</h1></div>";
    }
    
    
    echo "<b>Jumlah Proses Import : </b><br/>";
    echo "1. Jml MSBAR : $pjmlmsbar<br/>";
    echo "2. Jml Customer : $pjmlcustomer<br/>";
    echo "3. Jml Sales : $pjmljual<br/>";
    echo "Selesai dalam ".$total_time." detik";
    
    
    
    
    mysqli_close($cnmy);
    
    //IT
    if ($plogit_akses==true) {
        mysqli_close($cnit);
    }
    
    
?>