<?php
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    session_start();
    $puserid="";
    $pinidcrd="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
    if (isset($_SESSION['IDCARD'])) $pinidcrd=$_SESSION['IDCARD'];
    
    $pmodule=$_GET['module'];
    $notlert="Tidak ada data yang disimpan";
    if ($pmodule=="simpandatanya") {
        
        $pidbarang=$_POST['udbr'];
        $pbln=$_POST['ubln'];
        $pdivuntuk=$_POST['udivuntuk'];
        $pjmlop=$_POST['ujmlop'];
        $pjmlkel=$_POST['ujmlkel'];
        $pnotes=$_POST['unotes'];
        $pidcard=$_POST['uuserid'];
        if (empty($pidcard)) $pidcard=$pinidcrd;
        
        if (empty($pidcard)) {
            echo "Anda harus login ulang....!!!"; exit;
        }
        
        $pbulan= date("Ym", strtotime($pbln));
        $pinblnsv= date("Y-m-01", strtotime($pbln));
        
        $pjmlop=str_replace(",","", $pjmlop);
        $pjmlkel=str_replace(",","", $pjmlkel);
        
        if (!empty($pnotes)) $pnotes = str_replace("'", " ", $pnotes);
        
        include "../../config/koneksimysqli.php";
        if (!empty($pidbarang) AND !empty($pbln)) {
            
            $query = "select IDBARANG from dbmaster.t_barang_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulan' AND IDBARANG='$pidbarang' AND PILIHAN='$pdivuntuk'";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ((DOUBLE)$ketemu>0){
            }else{
                $query = "INSERT INTO dbmaster.t_barang_opname_d (PILIHAN, BULAN, IDBARANG, USERID)VALUES"
                        . "('$pdivuntuk', '$pinblnsv', '$pidbarang', '$pidcard')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan.""; exit; }
            }
            
            $query = "UPDATE dbmaster.t_barang_opname_d SET JMLOP='$pjmlop', NOTES='$pnotes' WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulan' AND IDBARANG='$pidbarang' AND PILIHAN='$pdivuntuk' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan.""; exit; }
            
            $notlert="Berhasil";
            //$notlert="$pidbarang, $pbulan ($pinblnsv), $pjmlop, $pnotes ($pidcard) = $ketemu";
        }
        
        mysqli_close($cnmy);
        
    }
    
    echo $notlert;
    
?>

