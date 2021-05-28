<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
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


if ($module=='pchprosesprit' AND $act=="update")
{
    $pcardidlog=$_POST['e_idcardlogin'];
    if (empty($pcardidlog)) $pcardidlog=$pidcard;
    
    if (empty($pcardidlog)) {

        $pmessage = "<script>";
        $pmessage .= "alert('Anda Harus login Ulang...');";
        $pmessage .= "window.location = '";
        $pmessage .= "../../../../index.php";
        $pmessage .= "'";
        $pmessage .= "</script>";
        echo "$pmessage"; exit;
    }


    $kodenya=$_POST['e_id'];
    $detailkode=$_POST['e_did'];

    if (empty($detailkode) OR empty($kodenya)) {
        $pmessage = "<script>";
        $pmessage .= "alert('Tidak ada data yang akan diproses...');";
        $pmessage .= "window.location = '";
        $pmessage .= "../../../media.php?module=$module&idmenu=$idmenu&act=gagalsimpan";
        $pmessage .= "'";
        $pmessage .= "</script>";

        echo "$pmessage"; exit;
    }

    //echo "$module : $kodenya, $detailkode<br/>"; exit;

    unset($pinsert_data_detail);//kosongkan array
    $psimpandata=false;
    foreach ($_POST['chkbox_br'] as $piddata) {
        if (empty($piddata)) {
            //continue;
        }
        
        $pidbrg=$_POST['m_idbrg'][$piddata];
        $pnmbrg=$_POST['m_nmbrg'][$piddata];
        $pspcbrg=$_POST['txt_specbr'][$piddata];
        $pjmlbrg=$_POST['txt_njmlbrg'][$piddata];
        $phrgbrg=$_POST['txt_nhrgbrg'][$piddata];
        $pketdata=$_POST['txt_ketbrg'][$piddata];
        $psatuan=$_POST['m_satuan'][$piddata];
        
        if (!empty($pnmbrg)) $pnmbrg = str_replace("'", " ", $pnmbrg);
        if (!empty($pspcbrg)) $pspcbrg = str_replace("'", " ", $pspcbrg);
        if (!empty($pketdata)) $pketdata = str_replace("'", " ", $pketdata);
        if (!empty($psatuan)) $psatuan = str_replace("'", " ", $psatuan);
        
        if (empty($pjmlbrg)) $pjmlbrg=1;
        if (empty($phrgbrg)) $phrgbrg=0;
        
        $pjmlbrg=str_replace(",","", $pjmlbrg);
        $phrgbrg=str_replace(",","", $phrgbrg);
        
        //echo "$piddata : $pidbrg, $pnmbrg, $pspcbrg, $phrgbrg, $pjmlbrg, $pketdata<br/>";
        
        $pinsert_data_detail[] = "('$kodenya', '$pidbrg', '$pnmbrg', '$pspcbrg', '$pjmlbrg', '$phrgbrg', '$pketdata', '$psatuan', '$pcardidlog')";
        $psimpandata=true;

    }

    if ($psimpandata==false) {
        $pmessage = "<script>";
        $pmessage .= "alert('Tidak ada data yang akan diproses...');";
        $pmessage .= "window.location = '";
        $pmessage .= "../../../media.php?module=$module&idmenu=$idmenu&act=gagalsimpan";
        $pmessage .= "'";
        $pmessage .= "</script>";

        echo "$pmessage"; exit;
    }

    include "../../../config/koneksimysqli.php";
    
    $query = "select idpr_d FROM dbpurchasing.t_pr_transaksi_d2 WHERE idpr_d='$detailkode' AND idpr='$kodenya'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu<=0) {
        $query = "insert into dbpurchasing.t_pr_transaksi_d2 
            (idpr_d, idpr, idbarang, namabarang, idbarang_d, spesifikasi1, spesifikasi2, uraian, 
            keterangan, jumlah, satuan, harga, userid) select 
            idpr_d, idpr, idbarang, namabarang, idbarang_d, spesifikasi1, spesifikasi2, uraian, 
            keterangan, jumlah, satuan, harga, '$pcardidlog' as userid FROM dbpurchasing.t_pr_transaksi_d WHERE 
            idpr_d='$detailkode' AND idpr='$kodenya'";

        mysqli_query($cnmy, $query);  $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

    }

    $query_detail="INSERT INTO dbpurchasing.t_pr_transaksi_d (idpr, idbarang, namabarang, spesifikasi1, jumlah, harga, keterangan, satuan, userid) 
        VALUES ".implode(', ', $pinsert_data_detail);
    mysqli_query($cnmy, $query_detail);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    
    $query = "DELETE FROM dbpurchasing.t_pr_transaksi_d WHERE idpr_d='$detailkode' AND idpr='$kodenya' LIMIT 1";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }


    mysqli_close($cnmy);
    
    header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
    exit;

}


function begin($conn){
    mysqli_query($conn, "BEGIN");
}
    
function commit($conn){
    mysqli_query($conn, "COMMIT");
}
    
function rollback($conn){
    mysqli_query($conn, "ROLLBACK");
}

?>