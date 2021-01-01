<?php
    session_start();
    include "../../../config/koneksimysqli.php";
    /*
    $servername = "localhost";
    $username = "root";
    $password = "";
    $cnmy = mysqli_connect($servername, $username, $password) or die("Connection failed: " . mysqli_connect_error());
    */
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    $nmdb=$_GET['nmdb'];
    if ($nmdb=="MKT") $nmdb="mkt";
    $namatabel=$_GET['namatabel'];
    
    if ($nmdb=="callprosesdata") {
        if ($namatabel=="1") {
            mysqli_query($cnmy, "CALL dbmaster.proses_backup_new_br_all_1()");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $namatabel= $erropesan;}
        }elseif ($namatabel=="2") {
            mysqli_query($cnmy, "CALL dbmaster.proses_backup_new_br_all_2()");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $namatabel= $erropesan;}
        }elseif ($namatabel=="3") {
            
            mysqli_query($cnmy, "CALL dbmaster.proses_data_karyawan_hrd()");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $namatabel= $erropesan;}
            
            mysqli_query($cnmy, "CALL dbmaster.proses_data_karyawan_hrd_dokter()");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $namatabel= $erropesan;}
            
            mysqli_query($cnmy, "CALL dbmaster.proses_data_cabang_area_prod_rsmaut_dll()");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $namatabel= $erropesan;}
            
            
        }elseif ($namatabel=="4") {
            mysqli_query($cnmy, "CALL dbmaster.proses_backup_new_br_all_3()");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $namatabel= $erropesan;}
        }
        
        
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=none&nmtbl='.$namatabel);
    }
    
    exit;
    if ($nmdb=="dbbackup") {
        if ($namatabel=="brhrdall"){
            
            date_default_timezone_set('Asia/Jakarta');
            $milliseconds = round(microtime(true) * 1000);
            $now=date("ymd_His");
            $tmp01 =" dbbackup_it.h_".$now."_br_otc_".$milliseconds;
            $tmp02 =" dbbackup_it.h_".$now."_br_otc_bank_".$milliseconds;
            $tmp03 =" dbbackup_it.h_".$now."_br_otc_ext_".$milliseconds;
            $tmp04 =" dbbackup_it.h_".$now."_br_otc_reject_".$milliseconds;
            $tmp05 =" dbbackup_it.h_".$now."_br_otc_ttd_".$milliseconds;
            
            mysqli_query($cnmy, "DROP TABLE IF EXISTS $tmp01");
            $query = "CREATE TABLE $tmp01 (select * from hrd.br_otc)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }
            
            mysqli_query($cnmy, "DROP TABLE IF EXISTS $tmp02");
            $query = "CREATE TABLE $tmp02 (select * from hrd.br_otc_bank)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }
            
            mysqli_query($cnmy, "DROP TABLE IF EXISTS $tmp03");
            $query = "CREATE TABLE $tmp03 (select * from hrd.br_otc_ext)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }
            
            mysqli_query($cnmy, "DROP TABLE IF EXISTS $tmp04");
            $query = "CREATE TABLE $tmp04 (select * from hrd.br_otc_reject)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }
            
            mysqli_query($cnmy, "DROP TABLE IF EXISTS $tmp05");
            $query = "CREATE TABLE $tmp05 (select * from hrd.br_otc_ttd)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }
            
            
            $tmp06 =" dbbackup_it.h_".$now."_br0_".$milliseconds;
            $tmp07 =" dbbackup_it.h_".$now."_br0_reject_".$milliseconds;
            $tmp08 =" dbbackup_it.h_".$now."_br0_ttd_".$milliseconds;
            
            mysqli_query($cnmy, "DROP TABLE IF EXISTS $tmp06");
            $query = "CREATE TABLE $tmp06 (select * from hrd.br0)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }
            
            mysqli_query($cnmy, "DROP TABLE IF EXISTS $tmp07");
            $query = "CREATE TABLE $tmp07 (select * from hrd.br0_reject)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }
            
            mysqli_query($cnmy, "DROP TABLE IF EXISTS $tmp08");
            $query = "CREATE TABLE $tmp08 (select * from hrd.br0_ttd)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }
            
            
            
            $tmp09 =" dbbackup_it.h_".$now."_kas_".$milliseconds;
            
            mysqli_query($cnmy, "DROP TABLE IF EXISTS $tmp09");
            $query = "CREATE TABLE $tmp09 (select * from hrd.kas)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }
            
            
            $tmp10 =" dbbackup_it.h_".$now."_klaim_".$milliseconds;
            $tmp11 =" dbbackup_it.h_".$now."_klaim_reject_".$milliseconds;
            
            mysqli_query($cnmy, "DROP TABLE IF EXISTS $tmp10");
            $query = "CREATE TABLE $tmp10 (select * from hrd.klaim)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }
            
            mysqli_query($cnmy, "DROP TABLE IF EXISTS $tmp11");
            $query = "CREATE TABLE $tmp11 (select * from hrd.klaim_reject)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }
            
            
            mysqli_query($cnmy, "CALL dbmaster.dbbackup_it_backupdata_br()");
    
            /*
            //OTC
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM hrd.br_otc LIMIT 1"));
            if ($ketemu>0) {
                mysqli_query($cnmy, "DELETE FROM dbbackup_it.hrd_br_otc");
                mysqli_query($cnmy, "INSERT INTO dbbackup_it.hrd_br_otc SELECT * FROM hrd.br_otc");
            }
            
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM hrd.br_otc_bank LIMIT 1"));
            if ($ketemu>0) {
                mysqli_query($cnmy, "DELETE FROM dbbackup_it.hrd_br_otc_bank");
                mysqli_query($cnmy, "INSERT INTO dbbackup_it.hrd_br_otc_bank SELECT * FROM hrd.br_otc_bank");
            }
            
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM hrd.br_otc_ext LIMIT 1"));
            if ($ketemu>0) {
                mysqli_query($cnmy, "DELETE FROM dbbackup_it.hrd_br_otc_ext");
                mysqli_query($cnmy, "INSERT INTO dbbackup_it.hrd_br_otc_ext SELECT * FROM hrd.br_otc_ext");
            }
            
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM hrd.br_otc_reject LIMIT 1"));
            if ($ketemu>0) {
                mysqli_query($cnmy, "DELETE FROM dbbackup_it.hrd_br_otc_reject");
                mysqli_query($cnmy, "INSERT INTO dbbackup_it.hrd_br_otc_reject SELECT * FROM hrd.br_otc_reject");
            }
            
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM hrd.br_otc_ttd LIMIT 1"));
            if ($ketemu>0) {
                mysqli_query($cnmy, "DELETE FROM dbbackup_it.hrd_br_otc_ttd");
                mysqli_query($cnmy, "INSERT INTO dbbackup_it.hrd_br_otc_ttd SELECT * FROM hrd.br_otc_ttd");
            }
            
            //END OTC
            
            //BR ETC
            
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM hrd.br0 LIMIT 1"));
            if ($ketemu>0) {
                mysqli_query($cnmy, "DELETE FROM dbbackup_it.hrd_br0");
                mysqli_query($cnmy, "INSERT INTO dbbackup_it.hrd_br0 SELECT * FROM hrd.br0");
            }
            
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM hrd.br0_reject LIMIT 1"));
            if ($ketemu>0) {
                mysqli_query($cnmy, "DELETE FROM dbbackup_it.hrd_br0_reject");
                mysqli_query($cnmy, "INSERT INTO dbbackup_it.hrd_br0_reject SELECT * FROM hrd.br0_reject");
            }
            
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM hrd.br0_ttd LIMIT 1"));
            if ($ketemu>0) {
                mysqli_query($cnmy, "DELETE FROM dbbackup_it.hrd_br0_ttd");
                mysqli_query($cnmy, "INSERT INTO dbbackup_it.hrd_br0_ttd SELECT * FROM hrd.br0_ttd");
            }
            
            //KAS
            
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM hrd.kas LIMIT 1"));
            if ($ketemu>0) {
                mysqli_query($cnmy, "DELETE FROM dbbackup_it.hrd_kas");
                mysqli_query($cnmy, "INSERT INTO dbbackup_it.hrd_kas SELECT * FROM hrd.kas");
            }
            
            //KLAIM
            
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM hrd.klaim LIMIT 1"));
            if ($ketemu>0) {
                mysqli_query($cnmy, "DELETE FROM dbbackup_it.hrd_klaim");
                mysqli_query($cnmy, "INSERT INTO dbbackup_it.hrd_klaim SELECT * FROM hrd.klaim");
            }
            
            $ketemu= mysqli_num_rows(mysqli_query($cnmy, "SELECT * FROM hrd.klaim_reject LIMIT 1"));
            if ($ketemu>0) {
                mysqli_query($cnmy, "DELETE FROM dbbackup_it.hrd_klaim_reject");
                mysqli_query($cnmy, "INSERT INTO dbbackup_it.hrd_klaim_reject SELECT * FROM hrd.klaim_reject");
            }
            
            */
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=none&nmtbl='.$namatabel);
        }
    }else{
        if (!empty($namatabel)) {
            $query = "CREATE TABLE dbtemp.$namatabel (select * from it_$nmdb.$namatabel)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>table ".$namatabel; exit; }


            $query = "CREATE TABLE IF NOT EXISTS $nmdb.$namatabel (select * from it_$nmdb.$namatabel limit 1)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>create table ".$namatabel; exit; }

            $query = "delete from $nmdb.$namatabel";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>delete record ".$namatabel; exit; }

            $query = "INSERT INTO $nmdb.$namatabel select * from dbtemp.$namatabel";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>insert record ".$namatabel; exit; }

            $query = "DROP TABLE dbtemp.$namatabel";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>drop table dbtemp ".$namatabel; exit; }

            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=none&nmtbl='.$namatabel);
        }
    }
?>

