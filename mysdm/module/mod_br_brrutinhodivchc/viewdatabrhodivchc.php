<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="cekdatasudahada") {
    $pid=$_POST['uid'];
    $pidkar=$_POST['ukry'];
    $pbln=$_POST['ubln'];
    $pkdperiode=$_POST['ukdperiode'];
    
    $pbulan= date("Ym", strtotime($pbln));
    
    $bolehinput="boleh";
    
    include "../../config/koneksimysqli.php";
    
    
    $query = "select idrutin from dbmaster.t_brrutin0 WHERE DATE_FORMAT(bulan,'%Y%m')='$pbulan' AND "
            . " karyawanid='$pidkar' AND IFNULL(stsnonaktif,'')<>'Y' AND idrutin<>'$pid'";//AND kodeperiode='$pkdperiode' 
    
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    
    if ($ketemu>0) {
        $row= mysqli_fetch_array($tampil);
        $nidrutin=$row['idrutin'];
        if (!empty($nidrutin)) $bolehinput="Data Sudah Ada, dengan ID : $nidrutin";
    }
    
    mysqli_close($cnmy);
    echo $bolehinput;
    
}elseif ($pmodule=="getkodeperiode"){
    include "../../config/fungsi_sql.php";
    $mytglini="";
    $mytglini = getfield("select CURRENT_DATE as lcfields");
    if ($mytglini==0) $mytglini="";
    if (empty($mytglini)) $mytglini = date("Y-m-d");
    $tglini=date("Y-m", strtotime($mytglini));
    $hariiniserver=date("d", strtotime($mytglini));
    
    $periodeini=trim($_POST['ubulan']);
    $pbulan =  date("Y-m", strtotime($periodeini));
    if (empty($pbulan)) $Pbulan = date("Y-m");
    
    echo "<option value='1' selected>Periode 1</option>";
    /*
    echo "<option value='' selected>-- Pilihan --</option>";
    if ($pbulan<$tglini){
        echo "<option value='2' selected>Periode 2</option>";
    }else{
        if ($pbulan==$tglini){
            if ((int)$hariiniserver > 20) {
                echo "<option value='2' selected>Periode 2</option>";
            }else{
                echo "<option value='1'>Periode 1</option>";
                echo "<option value='2' selected>Periode 2</option>";
            }
        }else{
            echo "<option value='1'>Periode 1</option>";
            echo "<option value='2' selected>Periode 2</option>";
        }
    }
     * 
     */
}elseif ($_GET['module']=="getperiode"){
    $bulan = "01-".str_replace('/', '-', $_POST['ubulan']);
    if ($_POST['ukode']==1) {
        $periode1= date("Y-m-d", strtotime($bulan));
        $periode2= date("Y-m-t", strtotime($bulan));
    }elseif ($_POST['ukode']==2) {
        $periode1= date("Y-m-01", strtotime($bulan));
        $periode2= date("Y-m-t", strtotime($bulan));
    }
    $bln1=""; $bln2="";
    if (!empty($_POST['ukode'])) {
        $bln1= date("d/m/Y", strtotime($periode1));
        $bln2= date("d/m/Y", strtotime($periode2));
    }
    
    if ($_SESSION['DIVISI']=="OTC") {
        $bln1= date("01/m/Y", strtotime($periode1));
        $bln2= date("t/m/Y", strtotime($periode2));
    }
    
    echo "$bln1, $bln2";
}

?>