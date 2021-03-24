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
    $pbulan_upload =  date("Y-m", strtotime($ptgl));
    
    $target_dir .=$pbulan."/";
    $target_dir .=$pname_foder_dist."/".$pnmfolder."/";
    
    //ubah juga di prosesdata_
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    //$plogit_akses==false;
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
   
    mysqli_query($cnmy, "DELETE FROM $dbname.AMS_CUSTSDM");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE CUSTOMER SDM : $erropesan"; exit; }
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.AMS_CUSTSDM");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE CUSTOMER SDM : $erropesan"; exit; }
    }
    //END IT
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'CUSTSDM.DBF',0) or die("Error! Could not open dbase database file");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        
        $pnom=1; $isimpan=true;
        
        for ($ind=1;$ind<=$jum_record;$ind++){
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=$record[0];
            $pfile1=$record[1];
            $pfile2=$record[2];
            $pfile3=$record[3];
            $pfile4=$record[4];
            $pfile5=$record[5];
            $pfile6=$record[6];
            $pfile7=$record[7];
            $pfile8=$record[8];
            
            
            if (!empty($pfile2)) $pfile2 = str_replace("'", " ", $pfile2);
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            if (!empty($pfile5)) $pfile5 = str_replace("'", " ", $pfile5);
            if (!empty($pfile6)) $pfile6 = str_replace("'", " ", $pfile6);
            
            
            $query_parts_cus[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8')";
            
            
            /*
            $isimpan=true;
            if ((double)$pnom >= 20000) {
                
                $query_imp_cust = "INSERT INTO $dbname.AMS_CUSTSDM (C_KDCAB, C_CUSNO, C_CUNAM, C_ADRBILL1, "
                        . " C_ADRBILL2, C_CTYBILL, C_ZIPBILL, C_SECTORCD, L_DISPEN)values "
                        . " ".implode(', ', $query_parts_cus);
                mysqli_query($cnmy, $query_imp_cust);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT CUST : $erropesan"; exit; }

                //IT
                if ($plogit_akses==true) {
                    mysqli_query($cnit, $query_imp_cust);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT CUST : $erropesan"; exit; }
                }
                //END IT
                
                unset($query_parts_cus);//kosongkan array
                $pnom=0;
                
                $isimpan=false;
                
            }
            $pnom++;
            */
            
            
            /*
            $query = "INSERT INTO $dbname.AMS_CUSTSDM (C_KDCAB, C_CUSNO, C_CUNAM, C_ADRBILL1, "
                    . " C_ADRBILL2, C_CTYBILL, C_ZIPBILL, C_SECTORCD, L_DISPEN)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8')";

            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error CUST SDM : $erropesan"; exit; }
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error CUST SDM : $erropesan"; exit; }
            }
            //END IT
            */
        }
        
        
        // jika ada data yang sama
        $query_parts_cus2 = array_unique($query_parts_cus); 
        
        //if ($isimpan == true) {
            $query_imp_cust = "INSERT INTO $dbname.AMS_CUSTSDM (C_KDCAB, C_CUSNO, C_CUNAM, C_ADRBILL1, "
                    . " C_ADRBILL2, C_CTYBILL, C_ZIPBILL, C_SECTORCD, L_DISPEN)values "
                    . " ".implode(', ', $query_parts_cus2);
            mysqli_query($cnmy, $query_imp_cust);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT CUST : $erropesan"; exit; }

            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query_imp_cust);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT CUST : $erropesan"; exit; }
            }
            //END IT
        //}
        
        
        
    }
    dbase_close($pinsert);
    
    $pjmlcustomer=$jum_record;
    

    mysqli_query($cnmy, "DELETE FROM $dbname.AMS_ITEMSDM");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE ITEM PROD SDM : $erropesan"; exit; }
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.AMS_ITEMSDM");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE ITEM PROD SDM : $erropesan"; exit; }
    }
    //END IT
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'ITEMSDM.DBF',0) or die("Error! Could not open dbase database file");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        
        
        
        for ($ind=1;$ind<=$jum_record;$ind++){
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=$record[0];
            $pfile1=$record[1];
            $pfile2=$record[2];
            $pfile3=$record[3];
            
            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            if (!empty($pfile2)) $pfile2 = str_replace("'", " ", $pfile2);
            
            $query_parts_item[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3')";
            
            
            /*
            $query = "INSERT INTO $dbname.AMS_ITEMSDM (C_ITENO, C_ITNAM, C_UNDES, C_KDDIVPRI)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3')";
            
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error ITEM PRODUK : $erropesan"; exit; }
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error ITEM PRODUK : $erropesan"; exit; }
            }
            //END IT
            */
            
        }
        
        $query_imp_item = "INSERT INTO $dbname.AMS_ITEMSDM (C_ITENO, C_ITNAM, C_UNDES, C_KDDIVPRI)values "
                . " ".implode(', ', $query_parts_item);
        mysqli_query($cnmy, $query_imp_item);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT ITEM : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_imp_item);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT ITEM IT : $erropesan"; exit; }
        }
        //END IT
        
        
        
    }
    dbase_close($pinsert);

    $pjmlproduk=$jum_record;
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.AMS_JUALSDM");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE JUAL/SALES SDM : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.AMS_JUALSDM");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE JUAL/SALES SDM : $erropesan"; exit; }
    }
    //END IT
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'JUALSDM.DBF',0) or die("Error! Could not open dbase database file");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        
        
        
        for ($ind=1;$ind<=$jum_record;$ind++){
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=$record[0];
            $pfile1=$record[1];
            $pfile2=$record[2];
            $pfile3=$record[3];
            $pfile4=$record[4];
            $pfile5=$record[5];
            $pfile6=$record[6];
            $pfile7=$record[7];
            $pfile8=$record[8];
            $pfile9=$record[9];
            $pfile10=$record[10];
            $pfile11=$record[11];
            
            
            $query_parts_jual[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11')";
            
            /*
            $query = "INSERT INTO $dbname.AMS_JUALSDM (C_KDCAB, C_CUSNO, C_CUSNO2, C_INVNO, "
                    . " D_INVDATE, C_ITENO, N_QTYSAL, N_QTYBON, N_SALPRI, DPL, N_DISC2PRI, N_DISC3PRI)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11')";
            
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error JUAL SDM : $erropesan"; exit; }
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error JUAL SDM : $erropesan"; exit; }
            }
            //END IT
            */
            
        }
        
        $query_imp_jual = "INSERT INTO $dbname.AMS_JUALSDM (C_KDCAB, C_CUSNO, C_CUSNO2, C_INVNO, "
                    . " D_INVDATE, C_ITENO, N_QTYSAL, N_QTYBON, N_SALPRI, DPL, N_DISC2PRI, N_DISC3PRI)values "
                . " ".implode(', ', $query_parts_jual);
        mysqli_query($cnmy, $query_imp_jual);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT JUAL : $erropesan"; exit; }
        
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_imp_jual);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT JUAL IT : $erropesan"; exit; }
        }
        //END IT
        
        
    }
    dbase_close($pinsert);

    $pjmljual=$jum_record;
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.AMS_RETUR");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE RETUR SDM : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.AMS_RETUR");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE RETUR SDM : $erropesan"; exit; }
    }
    //END IT
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'RETUR.DBF',0) or die("Error! Could not open dbase database file");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        
        
        
        for ($ind=1;$ind<=$jum_record;$ind++){
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=$record[0];
            $pfile1=$record[1];
            $pfile2=$record[2];
            $pfile3=$record[3];
            $pfile4=$record[4];
            $pfile5=$record[5];
            $pfile6=$record[6];
            $pfile7=$record[7];
            $pfile8=$record[8];
            $pfile9=$record[9];
            $pfile10=$record[10];
            $pfile11=$record[11];
            $pfile12=$record[12];
            $pfile13=$record[13];
            $pfile14=$record[14];
            
            
            $query_parts_retur[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11', '$pfile12', '$pfile13', '$pfile14')";
            
            
            /*
            $query = "INSERT INTO $dbname.AMS_RETUR (C_KDCAB, C_CUSNO, C_CUSNO2, C_INVNO, "
                    . " D_INVDATE, C_ITENO, N_QTYSALG, N_QTYSALB, N_QTYBONG, N_QTYBONB, N_SALPRI, N_DISC2PRI, N_DISC3PRI)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11', '$pfile12')";
            
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error RETUR SDM : $erropesan"; exit; }
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error RETUR SDM : $erropesan"; exit; }
            }
            //END IT
            */
            
        }
        
        
        $query_imp_retur = "INSERT INTO $dbname.AMS_RETUR (C_KDCAB, C_CUSNO, C_CUSNO2, C_INVNO, "
                    . " D_INVDATE,C_EXFAK,D_EXTGL, C_ITENO, N_QTYSALG, N_QTYSALB, N_QTYBONG, N_QTYBONB, N_SALPRI, N_DISC2PRI, N_DISC3PRI)values "
                . " ".implode(', ', $query_parts_retur);
        mysqli_query($cnmy, $query_imp_retur);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT RETUR : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_imp_retur);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT RETUR IT : $erropesan"; exit; }
        }
        //END IT
        
        
    }
    dbase_close($pinsert);
    
    $pjmlretur=$jum_record;
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.AMS_STOCK");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE STOCK SDM : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.AMS_STOCK");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE STOCK SDM : $erropesan"; exit; }
    }
    //END IT
    
    $jum_record=0;
    $pinsert=dbase_open($target_dir.'STOCK.DBF',0) or die("Error! Could not open dbase database file");
    if ($pinsert){
        $jum_record=dbase_numrecords($pinsert);
        
        
        
        for ($ind=1;$ind<=$jum_record;$ind++){
            $record=dbase_get_record($pinsert,$ind);
            
            $pfile0=$record[0];
            $pfile1=$record[1];
            $pfile2=$record[2];
            $pfile3=$record[3];
            $pfile4=$record[4];
            
            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            
            
            $query_parts_stock[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4')";
            
            
            /*
            $query = "INSERT INTO $dbname.AMS_STOCK (C_KDCAB, C_NMCAB, C_ITENO, C_ITNAM, "
                    . " N_QOH)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4')";
            
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error STOCK SDM : $erropesan"; exit; }
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error STOCK SDM : $erropesan"; exit; }
            }
            //END IT
            */
            
        }
        
        $query_imp_stock = "INSERT INTO $dbname.AMS_STOCK (C_KDCAB, C_NMCAB, C_ITENO, C_ITNAM, "
                    . " N_QOH)values "
                . " ".implode(', ', $query_parts_stock);
        mysqli_query($cnmy, $query_imp_stock);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnmy); echo "Error INSERT STOCK : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_imp_stock);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { dbase_close($pinsert); mysqli_close($cnit); echo "IT... Error INSERT STOCK IT : $erropesan"; exit; }
        }
        //END IT
        
    }
    dbase_close($pinsert);
    
    $pjmlstock=$jum_record;
    
    
    
    
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 4);
    

    $query = "SELECT * FROM $dbname.AMS_JUALSDM WHERE DATE_FORMAT(D_INVDATE,'%Y%m')<>'$pbulan'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);    
    
    if ($ketemu>0) {
        echo "<div style='color:red;'><h1>Import tidak sesuai periode</h1></div>";
    }
        
    
    echo "<b>Jumlah Proses Import : </b><br/>";
    echo "1. Jml Customer : $pjmlcustomer<br/>";
    echo "2. Jml Item Produk : $pjmlproduk<br/>";
    echo "3. Jml Sales : $pjmljual<br/>";
    echo "4. Jml Retur : $pjmlretur<br/>";
    echo "5. Jml Stock : $pjmlstock<br/>";
    echo "Selesai dalam ".$total_time." detik<br/>&nbsp;<br/>&nbsp;";
    
    
    
    
    mysqli_close($cnmy);
    
    //IT
    if ($plogit_akses==true) {
        mysqli_close($cnit);
    }
    

?>

<script>
    $(document).ready(function() {
        var table = $('#dtabelpros1, #dtabelpros2, #dtabelpros3, #dtabelpros4, #dtabelpros5').DataTable({
            fixedHeader: true,
            "ordering": true,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "order": [[ 0, "asc" ]],
            bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
            "bPaginate": false,
            "scrollY": 440,
            "scrollX": true
        } );
    } );
</script>

<style>
    .divnone {
        display: none;
    }
    #dtabelpros1 th, #dtabelpros2 th, #dtabelpros3 th, #dtabelpros4 th, #dtabelpros5 th {
        font-size: 13px;
    }
    #dtabelpros1 td, #dtabelpros2 td, #dtabelpros3 td, #dtabelpros4 td, #dtabelpros5 td { 
        font-size: 11px;
    }
</style>

