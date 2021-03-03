<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
$puserid="";
$pidcard="";
$pidgroup="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

if ($module=='pchisivendorpr')
{
    if ($act=="hapus") {
        
        if (empty($puserid)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        include "../../../config/koneksimysqli.php";
        $pkodenya=$_GET['id'];
        
        //$query = "UPDATE dbmaster.t_pr_transaksi_po SET stsnonaktif='Y' WHERE idpr_po='$pkodenya'  LIMIT 1";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        mysqli_close($cnmy);
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
    }elseif ($act=="inputvendor" OR $act=="updatevendor") {
        
        $pcardidlog=$_POST['e_idcardlogin'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        
        include "../../../config/koneksimysqli.php";
        
        $kodenya=$_POST['e_id'];
        $pdidpr=$_POST['e_idpr'];
        $pdidpr_d=$_POST['e_idpr_d'];
        $pidvendor_d=$_POST['e_idvendor'];
        $pidbrg=$_POST['e_idbrg'];
        $pidbrg_d=$_POST['e_idbrg2'];
        $pnmbrg=$_POST['e_nmbrg'];
        
        $pidbrg2=$_POST['e_brg2'];
        $pspesbrg_asli=$_POST['e_spek_asli'];
        
        $pspesbrg=$_POST['e_spek'];
        $psatuan=$_POST['e_satuan'];
        $ppilihsts=$_POST['cb_pilih'];
        
        if (empty($ppilihsts)) $ppilihsts="Y";
        
        $pharga=$_POST['e_hrgbrg'];
        $pjml=$_POST['e_jmlqty'];
        
        
        $pketdetail=$_POST['e_ketdetail'];
        
        
        if (!empty($pspesbrg)) $pspesbrg = str_replace("'", " ", $pspesbrg);
        if (!empty($pketdetail)) $pketdetail = str_replace("'", " ", $pketdetail);
        if (!empty($psatuan)) $psatuan = str_replace("'", " ", $psatuan);
        
        $pharga=str_replace(",","", $pharga);
        $pjml=str_replace(",","", $pjml);
        
        if (empty($pharga)) $pharga=0;
        if (empty($pjml)) $pjml=0;
        $ptotalhrga=(DOUBLE)$pharga*(DOUBLE)$pjml;
        
        $pspesbrg_asli_bd="";
        $pspesbrg_bd="";
        if (!empty($pspesbrg_asli)) $pspesbrg_asli_bd = preg_replace("/[\\n\\r]+/", "", $pspesbrg_asli);;
        if (!empty($pspesbrg)) $pspesbrg_bd = preg_replace("/[\\n\\r]+/", "", $pspesbrg);;
        
        if (TRIM($pspesbrg_asli_bd)==TRIM($pspesbrg_bd)){
        }else{
            $pidbrg2="";
            
            $query = "select idbarang_d from dbpurchasing.t_pr_barang_d WHERE IFNULL(idbarang,'')='' AND "
                    . " TRIM(REPLACE(REPLACE(REPLACE(spesifikasi1, '\n', ''), '\r', ''), '\t', ''))='$pspesbrg_bd'";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ((DOUBLE)$ketemu>0) {
                $pidbrg_d="";
            }else{
            
                $query = "INSERT into dbpurchasing.t_pr_barang_d (idbarang, spesifikasi1, harga) VALUES"
                        . " ('$pidbrg_d', '$pspesbrg', '$pharga')";
                mysqli_query($cnmy, $query); 
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

                $pidbrg2 = mysqli_insert_id($cnmy);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                
            }
            
        }
        
        
        if (empty($pidbrg_d)) {
            //echo "kesini"; exit;
            $pidbrg_d=$pidbrg;
            
            $query = "UPDATE dbpurchasing.t_pr_barang_d SET idbarang='$pidbrg_d', harga='$pharga' WHERE IFNULL(idbarang,'')='' AND "
                    . " TRIM(REPLACE(REPLACE(REPLACE(spesifikasi1, '\n', ''), '\r', ''), '\t', ''))='$pspesbrg_bd'";
            mysqli_query($cnmy, $query); 
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            $query = "select idbarang_d from dbpurchasing.t_pr_barang_d WHERE IFNULL(idbarang,'')='$pidbrg_d' AND "
                    . " TRIM(REPLACE(REPLACE(REPLACE(spesifikasi1, '\n', ''), '\r', ''), '\t', ''))='$pspesbrg_bd'";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ((DOUBLE)$ketemu>0) {
                $nrow= mysqli_fetch_array($tampil);
                $pidbrg2=$nrow['idbarang_d'];
            }
        }
        
        //echo "$pidbrg<br/>"; exit;
        
        
        if ($pidbrg_d!=$pidbrg) {
            echo "ada"; exit;
            $query = "UPDATE dbpurchasing.t_pr_transaksi_d SET idbarang='$pidbrg', namabarang='$pnmbrg' WHERE idbarang='$pidbrg_d' AND idpr='$pdidpr' AND idpr_d='$pdidpr_d' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            $query = "UPDATE dbpurchasing.t_pr_transaksi_po SET idbarang='$pidbrg', namabarang='$pnmbrg' WHERE idbarang='$pidbrg_d' AND idpr='$pdidpr' AND idpr_d='$pdidpr_d' AND idpr_po<>'$kodenya'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        //echo "$pidbrg2<br/>"; exit;
        
        if ($act=="inputvendor") {
            
            $query = "INSERT INTO dbpurchasing.t_pr_transaksi_po (idpr, idpr_d, kdsupp, idbarang, namabarang, idbarang_d, spesifikasi1, jumlah, harga, satuan, keterangan, totalrp, aktif, userid)"
                    . "VALUES('$pdidpr', '$pdidpr_d', '$pidvendor_d', '$pidbrg', '$pnmbrg', '$pidbrg2', '$pspesbrg', '$pjml', '$pharga', '$psatuan', '$pketdetail', '$ptotalhrga', '$ppilihsts', '$pcardidlog')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        }elseif ($act=="updatevendor") {
            if (!empty($kodenya)) {
                $query = "UPDATE dbpurchasing.t_pr_transaksi_po SET "
                        . " idpr='$pdidpr', idpr_d='$pdidpr_d', kdsupp='$pidvendor_d', idbarang='$pidbrg', namabarang='$pnmbrg', "
                        . " idbarang_d='$pidbrg2', spesifikasi1='$pspesbrg', jumlah='$pjml', harga='$pharga', keterangan='$pketdetail', "
                        . " totalrp='$ptotalhrga', userid='$pcardidlog', satuan='$psatuan', aktif='$ppilihsts' WHERE idpr_po='$kodenya' LIMIT 1"; 
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }
        }
        
        $query = "UPDATE dbpurchasing.t_pr_transaksi_po SET idbarang_d=NULL WHERE idpr_po='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

        
        mysqli_close($cnmy);
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=isivendor&nmun='.$idmenu.'&id='.$pdidpr.'&xid='.$pdidpr_d);
        
        
    }
}