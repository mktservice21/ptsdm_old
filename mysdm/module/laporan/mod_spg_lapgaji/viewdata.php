<?php
session_start();
include "../../../config/koneksimysqli.php";
$ntgl=$_POST['utgl'];
$bulan = date('Ym', strtotime($ntgl));

    // cek validate / submit dari cabang (semua cabang input harus validate)
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp02 =" dbtemp.DSPGSUBMITLP02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSPGSUBMITLP03_".$userid."_$now ";
    $tmp04 =" dbtemp.DSPGSUBMITLP04_".$userid."_$now ";
    
    $query = "select DISTINCT DATE_FORMAT(periode,'%Y%m') bulan, icabangid, alokid  
            from dbmaster.t_spg_gaji_br0 where IFNULL(stsnonaktif,'')<>'Y' and DATE_FORMAT(periode,'%Y%m')='$bulan'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select DISTINCT DATE_FORMAT(bulan,'%Y%m') bulan, icabangid 
        from dbmaster.t_spg_validate where DATE_FORMAT(bulan,'%Y%m')='$bulan'";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET icabangid='JKT_MT' WHERE icabangid='0000000007' AND alokid='001'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET icabangid='JKT_RETAIL' WHERE icabangid='0000000007' AND alokid='002'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "DELETE FROM $tmp02 WHERE CONCAT(bulan,icabangid) IN (select distinct IFNULL(CONCAT(IFNULL(bulan,''),IFNULL(icabangid,'')),'') FROM $tmp03)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.nama nama_cabang from $tmp02 a LEFT JOIN MKT.icabang_o b on a.icabangid=b.icabangid_o";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    
    $adacabangblmvalidate=false;
    $cb_blmsubmit="";
    $query = "select * from $tmp04";
    $tampilcb = mysqli_query($cnmy, $query);
    $ketemucb = mysqli_num_rows($tampilcb);
    if ($ketemucb>0) {
        while ($cb= mysqli_fetch_array($tampilcb)) {
            $nidcabang=$cb['icabangid'];
            $nnmcabang=$cb['nama_cabang'];
            $nalokid=$cb['alokid'];
            
            
            if ($nidcabang=="JKT_MT") $nnmcabang="JAKARTA MT";
            elseif ($nidcabang=="JKT_RETAIL") $nnmcabang="JAKARTA RETAIL";
            
            $cb_blmsubmit=$cb_blmsubmit."".$nnmcabang.", ";
            $adacabangblmvalidate=true; //dimatikan dulu
        }

        if (!empty($cb_blmsubmit)) {
            $cb_blmsubmit=substr($cb_blmsubmit, 0, -2);
            //echo $cb_blmsubmit;
        }
    }
    // cek validate / submit dari cabang (semua cabang input harus validate)
    

    if ($adacabangblmvalidate==true) {

    ?>
    <br/>&nbsp;<br/>&nbsp;
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><span class='required'></span></label>
        <div class='col-xs-9'>
            <?PHP
            echo "<h1 style='font-size : 17px; color:red;'>"
                . "Finance belum bisa Proses data, karena ada Cabang Belum Klik SUBMIT  :$cb_blmsubmit"
                . "</h1>silakan pilih status <span style='color:red;'>Belum Proses</span>";
            ?>
        </div>
    </div>

    <?PHP

    }
                                    
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
    
    
?>

