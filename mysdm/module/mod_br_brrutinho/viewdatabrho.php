<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="cekdatasudahada") {
    $pid=$_POST['uid'];
    $pidkar=$_POST['ukry'];
    $pbln=$_POST['ubln'];
    $ptgl_pl1=$_POST['up01'];
    $ptgl_pl2=$_POST['up02'];
    $pkdperiode=$_POST['ukdperiode'];
    
    $pbulan= date("Ym", strtotime($pbln));
    
    
    $ptgl_pl1 = str_replace('/', '-', $ptgl_pl1);
    $ptgl_pl2 = str_replace('/', '-', $ptgl_pl2);
    $ptgl1= date("Y-m-d", strtotime($ptgl_pl1));
    $ptgl2= date("Y-m-d", strtotime($ptgl_pl2));
    
    $pbln_pl1=date("Ym", strtotime($ptgl_pl1));
    $pbln_pl2=date("Ym", strtotime($ptgl_pl2));
    
    
    $bolehinput="boleh";
    
    
    if ( ($pbulan<>$pbln_pl1) OR ($pbulan<>$pbln_pl2) ) {
        echo "Bulan dan Periode tidak sesuai...."; exit;
    }
    
    
    include "../../config/koneksimysqli.php";
    
    
    $query = "select idrutin from dbmaster.t_brrutin0 WHERE ( (periode1 between '$ptgl1' AND '$ptgl2') OR (periode2 between '$ptgl1' AND '$ptgl2') ) "
            . " AND "
            . " karyawanid='$pidkar' AND IFNULL(stsnonaktif,'')<>'Y' AND idrutin<>'$pid'";//AND kodeperiode='$pkdperiode' 
    
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    
    if ($ketemu>0) {
        $row= mysqli_fetch_array($tampil);
        $nidrutin=$row['idrutin'];
        if (!empty($nidrutin)) $bolehinput="GAGAL.... Periode yang dipilih Tidak bisa tersimpan. karena sudah ada inputan, dengan ID : $nidrutin";
    }
    
    if (empty($pid)) {
        $query_spd = "select * from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' AND kodeperiode='$pkdperiode' AND DATE_FORMAT(tglf,'%Y%m')='$pbulan' AND jenis_rpt='RTNETH'";
        $tampil2= mysqli_query($cnmy, $query_spd);
        $ketemu2= mysqli_num_rows($tampil2);
        if ($ketemu2>0) {
            $pbulan_ym= date("F Y", strtotime($pbln));
            $bolehinput="GAGAL.... Periode ($pkdperiode) $pbulan_ym yang dipilih Sudah closing";
        }
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
}elseif ($_GET['module']=="getperiode"){
    $bulan = "01-".str_replace('/', '-', $_POST['ubulan']);
    if ($_POST['ukode']==1) {
        $periode1= date("Y-m-d", strtotime($bulan));
        $periode2= date("Y-m-15", strtotime($bulan));
    }elseif ($_POST['ukode']==2) {
        include "../../config/koneksimysqli.php";
        $pkry=$_POST['ukry'];
        $pnbln= date("Ym", strtotime($bulan));
        $query = "select idrutin from dbmaster.t_brrutin0 WHERE karyawanid='$pkry' AND DATE_FORMAT(bulan,'%Y%m')='$pnbln' AND kodeperiode='1'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        mysqli_close($cnmy);
        if ((INT)$ketemu>0) {
            $periode1= date("Y-m-16", strtotime($bulan));
            $periode2= date("Y-m-t", strtotime($bulan));
        }else{
            $periode1= date("Y-m-01", strtotime($bulan));
            $periode2= date("Y-m-t", strtotime($bulan));
        }
    }
    $bln1=""; $bln2="";
    if (!empty($_POST['ukode'])) {
        $bln1= date("d/m/Y", strtotime($periode1));
        $bln2= date("d/m/Y", strtotime($periode2));
    }
    
    //if ($_SESSION['DIVISI']=="OTC") {
        //$bln1= date("01/m/Y", strtotime($periode1));
        //$bln2= date("t/m/Y", strtotime($periode2));
    //}
    
    echo "$bln1, $bln2";
}

?>