<?php
session_start();

    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


if ($module=='gimicdatapenerima' AND $act=="hapus")
{
    include "../../config/koneksimysqli.php";
    
    $pidinput=$_GET['id'];
    //$query_eksekusi = "UPDATE dbmaster.t_barang_penerima SET AKTIF='N' WHERE IDPENERIMA='$pidinput'";
    $query_eksekusi = "DELETE FROM dbmaster.t_barang_penerima WHERE IDPENERIMA='$pidinput' LIMIT 1";
    mysqli_query($cnmy, $query_eksekusi);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete'.'&num=$idmenu'.'&id='.$pidinput);
        
}
elseif ($module=='gimicdatapenerima')
{
    

    include "../../config/koneksimysqli.php";
    
    $puserid=$_SESSION['USERID'];
    $pidinput=$_POST['e_id'];
    $pnmpenerima=$_POST['e_nmpenerima'];
    $palamat1=$_POST['e_alamat1'];
    $palamat2=$_POST['e_alamat2'];
    $pkota=$_POST['e_kota'];
    $pprovinsi=$_POST['e_provinsi'];
    $pkodepos=$_POST['e_kodepos'];
    $php=$_POST['e_hp'];
    $pdivuntuk=$_POST['cb_untuk'];
    $nurut=$_POST['e_id'];
    
    if ($act=="input") {
        $query = "select MAX(IGROUP) as NURUT FROM dbmaster.t_barang_penerima";
        $tampil= mysqli_query($cnmy, $query);
        $row= mysqli_fetch_array($tampil);
        $nurut=$row['NURUT'];
        if (empty($nurut)) $nurut=0;
        $nurut++;
    }
        
    if (!empty($pnmpenerima)) $pnmpenerima = str_replace("'", '', $pnmpenerima);
    if (!empty($palamat1)) $palamat1 = str_replace("'", '', $palamat1);
    if (!empty($palamat2)) $palamat2 = str_replace("'", '', $palamat2);
    if (!empty($pkota)) $pkota = str_replace("'", '', $pkota);
    if (!empty($pprovinsi)) $pprovinsi = str_replace("'", '', $pprovinsi);
    if (!empty($pkodepos)) $pkodepos = str_replace("'", '', $pkodepos);
    if (!empty($php)) $php = str_replace("'", '', $php);
    
    $pnamafield=" ICABANGID, AREAID ";
    if ($pdivuntuk=="OTC" OR $pdivuntuk=="CHC" OR $pdivuntuk=="OT") {
        $pnamafield=" ICABANGID_O, AREAID_O ";
    }
        
        
    $pflidcabang="";
    unset($pinsert_data_detail);//kosongkan array
    $psimpandata=false;
    foreach ($_POST['chkbox_br'] as $pidcabangarea) {
        if (empty($pidcabangarea)) {
            continue;
        }
        $pidcabang=$_POST['m_idcab'][$pidcabangarea];
        $pidarea=$_POST['m_idarea'][$pidcabangarea];
        
        $pflidcabang .="'".$pidcabang."',";
        $pinsert_data_detail[] = "('$pnmpenerima', '$palamat1', '$palamat2', '$pkota', '$pprovinsi', '$pkodepos', '$php', '$puserid', '$pdivuntuk', '$nurut', '$pidcabang', '$pidarea')";
        
        $psimpandata=true;
    }
    
    if ($psimpandata==false) {
        echo "Cabang belum diisi...";
        mysqli_close($cnmy);
        exit;
    }
    
    if ($psimpandata == true) {
        
        $query = "DELETE FROM dbmaster.t_barang_penerima WHERE IGROUP='$nurut' AND UNTUK='$pdivuntuk'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
        $query_eksekusi = "INSERT INTO dbmaster.t_barang_penerima(NAMA_PENERIMA, ALAMAT1, ALAMAT2, KOTA, PROVINSI, KODEPOS, HP, USERID, UNTUK, IGROUP, $pnamafield)"
                . " VALUES ".implode(', ', $pinsert_data_detail);
        mysqli_query($cnmy, $query_eksekusi);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    //$pflidcabang="(".substr($pflidcabang, 0, -1).")";
    
    
    //exit;
    /*
    if ($act=="input") {
        $query_eksekusi = "INSERT INTO dbmaster.t_barang_penerima(NAMA_PENERIMA, ALAMAT1, ALAMAT2, KOTA, PROVINSI, KODEPOS, HP)VALUES"
                . "('$pnmpenerima', '$palamat1', '$palamat2', '$pkota', '$pprovinsi', '$pkodepos', '$php')";
    }elseif ($act=="update") {
        $query_eksekusi = "UPDATE dbmaster.t_barang_penerima SET NAMA_PENERIMA='$pnmpenerima', ALAMAT1='$palamat1', ALAMAT2='$palamat2', "
                . " KOTA='$pkota', PROVINSI='$pprovinsi', KODEPOS='$pkodepos', HP='$php' WHERE IDPENERIMA='$pidinput'";
    }
    mysqli_query($cnmy, $query_eksekusi);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    */

        
        
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete'.'&num=$idmenu'.'&id='.$pidinput);
    
    
}
    
?>