<?php
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
    $pnmfile=$_POST['upilfile'];
    
    
    $_SESSION['MSTIMPPERTPIL']=$ptgl;
    $_SESSION['MSTIMPDISTPIL']=$pdist;
    $_SESSION['MSTIMPFOLDPIL']=$pnmfolder;
    $_SESSION['MSTIMPFILEPIL']=$pnmfile;
    
    
    $pname_foder_dist=CekNamaDist($pdist);
    $pbulan =  date("Ym", strtotime($ptgl));
    
    $target_dir .=$pbulan."/";
    $target_dir .=$pname_foder_dist."/".$pnmfolder."/";
    
    

    
    $ppath=$target_dir;
    $pfilerar=$pnmfile;
    $filename_rar = basename($ppath.'/'.$pfilerar);
    $filenameWX_rar = preg_replace("/\.[^.]+$/", "", $filename_rar);

    
    if (is_dir($target_dir.$filenameWX_rar)){
    }else{
        $archive = RarArchive::open($ppath.$pfilerar);
        $entries = $archive->getEntries();
        foreach ($entries as $entry) {
            $entry->extract($ppath.$filenameWX_rar);
        }
        $archive->close();
    }
    $target_dir .=$filenameWX_rar."/";
    
    
    $picabangpilih= trim(substr($filenameWX_rar, 0, 3));
    if (!empty($picabangpilih)) $picabangpilih= ucwords ($picabangpilih);
    
    
    $pnmtab1="";
    $pnmtab2="";
    $pnmtab3="";
    $pnmtab4="";
    $pnmtab5="";
    
    if ($picabangpilih=="BDG") {
        $pnmtab1="spp_msbar_bdg";
        $pnmtab2="spp_mscust_bdg";
        $pnmtab3="spp_mskota_bdg";
        $pnmtab4="spp_mssales_bdg";
        $pnmtab5="spp_mssekt_bdg";
    }elseif ($picabangpilih=="DIY") {
        $pnmtab1="spp_msbar_diy";
        $pnmtab2="spp_mscust_diy";
        $pnmtab3="spp_mskota_diy";
        $pnmtab4="spp_mssales_diy";
        $pnmtab5="spp_mssekt_diy";
    }elseif ($picabangpilih=="H_O") {
        $pnmtab1="spp_msbar_ho";
        $pnmtab2="spp_mscust_ho";
        $pnmtab3="spp_mskota_ho";
        $pnmtab4="spp_mssales_ho";
        $pnmtab5="spp_mssekt_ho";
    }elseif ($picabangpilih=="JKT") {
        $pnmtab1="spp_msbar_jkt";
        $pnmtab2="spp_mscust_jkt";
        $pnmtab3="spp_mskota_jkt";
        $pnmtab4="spp_mssales_jkt";
        $pnmtab5="spp_mssekt_jkt";
    }elseif ($picabangpilih=="MLG") {
        $pnmtab1="spp_msbar_mlg";
        $pnmtab2="spp_mscust_mlg";
        $pnmtab3="spp_mskota_mlg";
        $pnmtab4="spp_mssales_mlg";
        $pnmtab5="spp_mssekt_mlg";
    }elseif ($picabangpilih=="SBY") {
        $pnmtab1="spp_msbar_sby";
        $pnmtab2="spp_mscust_sby";
        $pnmtab3="spp_mskota_sby";
        $pnmtab4="spp_mssales_sby";
        $pnmtab5="spp_mssekt_sby";
    }elseif ($picabangpilih=="SLO") {
        $pnmtab1="spp_msbar_slo";
        $pnmtab2="spp_mscust_slo";
        $pnmtab3="spp_mskota_slo";
        $pnmtab4="spp_mssales_slo";
        $pnmtab5="spp_mssekt_slo";
    }elseif ($picabangpilih=="SMG") {
        $pnmtab1="spp_msbar_smg";
        $pnmtab2="spp_mscust_smg";
        $pnmtab3="spp_mskota_smg";
        $pnmtab4="spp_mssales_smg";
        $pnmtab5="spp_mssekt_smg";
    }
    
    
    if (empty($pnmtab1)) {
        echo "KOSONG...!!!";
        exit;
    }
    
    //ubah juga di prosesdata_
    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    $dbname = "MKT";
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    /* memanggil file DBF untuk kita Buka */
    
    $pinsertsave=false;
    mysqli_query($cnmy, "DELETE FROM $dbname.$pnmtab1");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE MSBAR : $erropesan"; exit; }
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'msbar.dbf',0) or die("Error! Could not open dbase database file");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        $no=1;
        for ($ind=1;$ind<=$jum_record;$ind++){
            
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=TRIM($record[0]);
            $pfile1=TRIM($record[1]);
            $pfile2=TRIM($record[2]);

            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            if (!empty($pfile2)) $pfile2 = str_replace("'", " ", $pfile2);
            
            $pinst_data_1[] = "('$pfile0', '$pfile1', '$pfile2')";
            $pinsertsave=true;
            
            /*
            $query = "INSERT INTO $dbname.$pnmtab1 (BRGID, BRGNM, UNITPCS)values "
                    . " ('$pfile0', '$pfile1', '$pfile2')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT MSBAR : $erropesan"; exit; }
            */
            
            $no++;
        }
        
    }
    dbase_close($pinsert);
    
    $pjmlmsbar=$jum_record;
    
    if ($pinsertsave==true) {
        $query_pros1 = "INSERT INTO $dbname.$pnmtab1 (BRGID, BRGNM, UNITPCS)values "
                . " ".implode(', ', $pinst_data_1);
        mysqli_query($cnmy, $query_pros1);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT MSBAR : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_pros1);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT MSBAR : $erropesan"; exit; }
        }
        //END IT
    }
    
    
    
    $pinsertsave=false;
    mysqli_query($cnmy, "DELETE FROM $dbname.$pnmtab2");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE MSCUST : $erropesan"; exit; }
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'mscust.dbf',0) or die("Error! Could not open dbase database file");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        $no=1;
        for ($ind=1;$ind<=$jum_record;$ind++){
            
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile3="";
            $pfile4="";
            $pfile5="";
            $pfile6="";
            
            $pfile0=TRIM($record[0]);
            $pfile1=TRIM($record[1]);
            $pfile2=TRIM($record[2]);
            if (isset($record[3])) $pfile3=TRIM($record[3]);
            if (isset($record[4])) $pfile4=TRIM($record[4]);
            if (isset($record[5])) $pfile5=TRIM($record[5]);
            if (isset($record[6])) $pfile6=TRIM($record[6]);

            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            if (!empty($pfile2)) $pfile2 = str_replace("'", " ", $pfile2);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            
            
            $pinst_data_2[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', '$pfile4', '$pfile5', '$pfile6')";
            $pinsertsave=true;
            
            /*
            $query = "INSERT INTO $dbname.$pnmtab2 (CUSTID, CUSTNM, ALAMAT, KOTAID, KOTA, SEKTORID, KODEPOS)values "
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', '$pfile4', '$pfile5', '$pfile6')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT MSCUST : $erropesan"; exit; }
            */
            
            $no++;
        }
        
    }
    dbase_close($pinsert);
    
    $pjmlcustomer=$jum_record;
    
    
    if ($pinsertsave==true) {
        $query_pros2 = "INSERT INTO $dbname.$pnmtab2 (CUSTID, CUSTNM, ALAMAT, KOTAID, KOTA, SEKTORID, KODEPOS)values "
                . " ".implode(', ', $pinst_data_2);
				//echo $query_pros2; exit;
        mysqli_query($cnmy, $query_pros2);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT MSCUST : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_pros2);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT MSCUST : $erropesan"; exit; }
        }
        //END IT
        
    }
    
    
    
    $pinsertsave=false;
    mysqli_query($cnmy, "DELETE FROM $dbname.$pnmtab3");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE MSKOTA : $erropesan"; exit; }
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'mskota.dbf',0) or die("Error! Could not open dbase database file");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        $no=1;
        for ($ind=1;$ind<=$jum_record;$ind++){
            
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=TRIM($record[0]);
            $pfile1=TRIM($record[1]);

            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            
            $pinst_data_3[] = "('$pfile0', '$pfile1')";
            $pinsertsave=true;
            
            /*
            $query = "INSERT INTO $dbname.$pnmtab3 (KOTAID, NAMA)values "
                    . " ('$pfile0', '$pfile1')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT MSKOTA : $erropesan"; exit; }
            */
            
            $no++;
        }
        
    }
    dbase_close($pinsert);
    
    $pjmlkode=$jum_record;
    
    if ($pinsertsave==true) {
        $query_pros3 = "INSERT INTO $dbname.$pnmtab3 (KOTAID, NAMA)values "
                . " ".implode(', ', $pinst_data_3);
        mysqli_query($cnmy, $query_pros3);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT MSKOTA : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_pros3);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT MSKOTA : $erropesan"; exit; }
        }
        //END IT
        
    }
    
    
    $pinsertsave=false;
    mysqli_query($cnmy, "DELETE FROM $dbname.$pnmtab4");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE SALES : $erropesan"; exit; }
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'sales.dbf',0) or die("Error! Could not open dbase database file");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        $no=1;
        for ($ind=1;$ind<=$jum_record;$ind++){
            
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=TRIM($record[0]);
            $pfile1=TRIM($record[1]);
            $pfile2=TRIM($record[2]);
            $pfile3=TRIM($record[3]);
            $pfile4=TRIM($record[4]);
            $pfile5=TRIM($record[5]);
            $pfile6=TRIM($record[6]);
            $pfile7=TRIM($record[7]);
            $pfile8=TRIM($record[8]);
            $pfile9=TRIM($record[9]);
			
			$pfile10="";
			if (isset($record[10])) $pfile10=TRIM($record[10]);

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
			
			
            $pinst_data_4[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10')";
            $pinsertsave=true;
            
            /*
            $query = "INSERT INTO $dbname.$pnmtab4 (CUSTID, TGLJUAL, NOJUAL, BRGID, HARGA, QBELI, QBONUS, DPL, XNOJUAL, KET)values "
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT SALES : $erropesan"; exit; }
            */
            
            $no++;
        }
        
    }
    dbase_close($pinsert);
    
    $pjmljual=$jum_record;
    
    if ($pinsertsave==true) {
        $query_pros4 = "INSERT INTO $dbname.$pnmtab4 (CUSTID, TGLJUAL, NOJUAL, BRGID, HARGA, QBELI, QBONUS, DPL, XNOJUAL, KET, NODPL)values "
                . " ".implode(', ', $pinst_data_4);
        mysqli_query($cnmy, $query_pros4);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT SALES : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_pros4);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT SALES : $erropesan"; exit; }
        }
        //END IT
        
    }
    
    
    $pinsertsave=false;
    mysqli_query($cnmy, "DELETE FROM $dbname.$pnmtab5");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE SEKTOR : $erropesan"; exit; }
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'mssekt.dbf',0) or die("Error! Could not open dbase database file");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        $no=1;
        for ($ind=1;$ind<=$jum_record;$ind++){
            
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=TRIM($record[0]);
            $pfile1=TRIM($record[1]);

            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            
            $pinst_data_5[] = "('$pfile0', '$pfile1')";
            $pinsertsave=true;
            
            /*
            $query = "INSERT INTO $dbname.$pnmtab5 (SEKTORID, SEKTORNM)values "
                    . " ('$pfile0', '$pfile1')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT SEKTOR : $erropesan"; exit; }
            */
            
            $no++;
        }
        
    }
    dbase_close($pinsert);
    
    $pjmlsektor=$jum_record;
    
    
    if ($pinsertsave==true) {
        $query_pros5 = "INSERT INTO $dbname.$pnmtab5 (SEKTORID, SEKTORNM)values "
                . " ".implode(', ', $pinst_data_5);
        mysqli_query($cnmy, $query_pros5);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT SEKTOR : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_pros5);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT SEKTOR : $erropesan"; exit; }
        }
        //END IT
        
    }
    
    
    mysqli_close($cnmy);
    
    //IT
    if ($plogit_akses==true) {
        mysqli_close($cnit);
    }
    
    
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 4);
    
    
    
    echo "<b>Jumlah Proses Import <span style='color:red;'>$filenameWX_rar</span> : </b><br/>";
    echo "1. Jml MSBAR : $pjmlmsbar<br/>";
    echo "2. Jml Customer : $pjmlcustomer<br/>";
    echo "3. Jml Kota : $pjmlkode<br/>";
    echo "4. Jml Sales : $pjmljual<br/>";
    echo "5. Jml Sektor : $pjmlsektor<br/>";
    echo "Selesai dalam ".$total_time." detik";
    
    
    
    //header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=spptampilkandatacekimport');
?>

