<?php
session_start();
if ($_GET['module']=="viewkode"){
    include "../../config/koneksimysqli.php";
    $ppilihca = trim($_POST['upilihca']);
    if ($ppilihca=="ca") $ppilihca="2";
    $query = "select distinct kodeid, nama from dbmaster.t_kode_spd";
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    echo "<option value='' selected>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $pkode  = $row['kodeid'];
        $pnama = $row['nama'];
        if ($ppilihca==$pkode)
            echo "<option value=\"$pkode\" selected>$pnama</option>";
        else
            echo "<option value=\"$pkode\">$pnama</option>";
    }
}elseif ($_GET['module']=="viewsubkode"){
    include "../../config/koneksimysqli.php";
    $pkode = trim($_POST['ukode']);
    $query = "select kodeid, subkode, subnama from dbmaster.t_kode_spd WHERE kodeid='$pkode' AND CONCAT(kodeid,subkode) IN ('103', '221', '236')";
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $subkode  = $row['subkode'];
        $namasub = $row['subnama'];
        echo "<option value=\"$subkode\">$subkode - $namasub</option>";
    }
}elseif ($_GET['module']=="viewnomorbukti"){
    include "../../config/koneksimysqli.php";
    $tgl01=$_POST['utgl'];
    $tahuninput= date("Y", strtotime($tgl01));
    
    $bl= date("m", strtotime($tgl01));
    $byear= date("y", strtotime($tgl01));
    $bl=(int)$bl;
    $blromawi="I";
    if ($bl==1) $blromawi="I";
    if ($bl==2) $blromawi="II";
    if ($bl==3) $blromawi="III";
    if ($bl==4) $blromawi="IV";
    if ($bl==5) $blromawi="V";
    if ($bl==6) $blromawi="VI";
    if ($bl==7) $blromawi="VII";
    if ($bl==8) $blromawi="VIII";
    if ($bl==9) $blromawi="IX";
    if ($bl==10) $blromawi="X";
    if ($bl==11) $blromawi="XI";
    if ($bl==12) $blromawi="XII";

    $pdivsi = trim($_POST['udivisi']);
    $pkode = trim($_POST['ukode']);
    $psubkode = trim($_POST['ukodesub']);
    $padvance = trim($_POST['uadvance']);
    
    $nobuktinya="";
    $tno=1;
    $awal=3;
    
    $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as pnomor FROM dbmaster.t_suratdana_br WHERE "
            . " stsnonaktif<>'Y' AND YEAR(tgl)='$tahuninput' AND divisi='$pdivsi' "
            . " AND kodeid='$pkode' AND subkode='$psubkode'";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $sh= mysqli_fetch_array($showkan);
        if (!empty($sh['pnomor'])) { $tno=(INT)$sh['pnomor']; $tno++; }
    }

    $jml=  strlen($tno);
    $awal=$awal-$jml;

    if ($awal>=0)
        $tno=str_repeat("0", $awal).$tno;
    else
        $tno=$tno;

    if ($psubkode=="21")
        $noslipurut=$tno."/LK-OTC/".$blromawi."/".$byear;
    elseif ($psubkode=="36")
        $noslipurut=$tno."/CA-RTN-OTC/".$blromawi."/".$byear;
    else
        $noslipurut=$tno."/RUTIN-OTC/".$blromawi."/".$byear;


    $nobuktinya=$noslipurut;
    
    echo $nobuktinya;
    
}elseif ($_GET['module']=="hitungtotalcekboxotc"){
    include "../../config/koneksimysqli.php";
    $pnoid=$_POST['unoidbr'];
    $padvance=$_POST['uadvance'];
    $userid=$_SESSION['IDCARD'];
    $totalinput=0;
    if (!empty($pnoid)) {
        $now=date("mdYhis");
        $tmp01 =" dbtemp.DSETHK01_".$userid."_$now ";
        
        $query = "select brOtcId, jumlah, realisasi from hrd.br_otc where brOtcId in $pnoid";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        if ($padvance=="A"){
        }else{
            mysqli_query($cnmy, "UPDATE $tmp01 SET jumlah=realisasi WHERE IFNULL(realisasi,0)>0");
        }
        
        $query="SELECT SUM(jumlah) as jumlah from $tmp01";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinput=$tr['jumlah'];
        }
        
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    }
    
    echo $totalinput;
    
}elseif ($_GET['module']=="hideungjumlahrp"){
    include "../../config/koneksimysqli.php";
    
    $pdivsi = trim($_POST['udivisi']);
    $pkode = trim($_POST['ukode']);
    $psubkode = trim($_POST['ukodesub']);
    $padvance = trim($_POST['uadvance']);
    $pdaripilihan=$_POST['upilihdari'];
    
    $tgl01=$_POST['utgl'];
    $thnblninput= date("Ym", strtotime($tgl01));
    $totalinput=0;
    
    $pkodepilih="";
    if ((double)$psubkode==3) $pkodepilih=" AND kode=1";
    if ((double)$psubkode==21) $pkodepilih=" AND kode=2";
    
    if ($pdaripilihan=="CA" AND (double)$psubkode==21) {
        $query ="select sum(jumlah) jumlah from dbmaster.t_ca0 where divisi='$pdivsi' 
            AND IFNULL(stsnonaktif,'')<>'Y' and "
            . " DATE_FORMAT(bulan,'%Y%m')='$thnblninput' "
            . " AND idca NOT IN (select distinct IFNULL(a.bridinput,'') FROM dbmaster.t_suratdana_br1 a JOIN "
            . " dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND b.divisi='$pdivsi')";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinput=$tr['jumlah'];
        }
    }else{
        if (!empty($pkodepilih)) {
        
            $query ="select sum(jumlah) jumlah from dbmaster.t_brrutin0 where divisi='$pdivsi' AND IFNULL(stsnonaktif,'')<>'Y' $pkodepilih and "
                    . " DATE_FORMAT(bulan,'%Y%m')='$thnblninput' "
                    . " AND idrutin NOT IN (select distinct IFNULL(a.bridinput,'') FROM dbmaster.t_suratdana_br1 a JOIN "
                    . " dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND b.divisi='$pdivsi')";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                $tr= mysqli_fetch_array($tampil);
                if (!empty($tr['jumlah'])) $totalinput=$tr['jumlah'];
            }
        }
    }


    echo $totalinput;
}elseif ($_GET['module']=="viewdaripilihan"){
    $pkode = trim($_POST['ukode']);
    if ($pkode=="1") {
        echo "<option value='RT'>Rutin</option>";
    }else{
        echo "<option value='LK' selected>Luar Kota</option>";
        echo "<option value='CA'>CA</option>";
    }
}elseif ($_GET['module']=="xxx"){
    
}
?>