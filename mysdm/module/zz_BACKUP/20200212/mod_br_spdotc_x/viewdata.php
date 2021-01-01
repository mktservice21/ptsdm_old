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
    $query = "select kodeid, subkode, subnama from dbmaster.t_kode_spd WHERE kodeid='$pkode'";
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
    
    $nfilsubkode=" ";
    if ($psubkode=="02") $nfilsubkode=" AND subkode='$psubkode' ";
    
    $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as pnomor FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' $nfilsubkode AND YEAR(tgl)='$tahuninput' AND divisi='$pdivsi'";// AND kodeid='$pkode'
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

    if ($psubkode=="02")
        $noslipurut=$tno."/BROTC-GAJI/".$blromawi."/".$byear;
    else
        $noslipurut=$tno."/BR-OTC/".$blromawi."/".$byear;


    $nobuktinya=$noslipurut;
    
    echo $nobuktinya;
    
}elseif ($_GET['module']=="cariperiode1"){
    $pkode = trim($_POST['ukode']);
    $psubkode = trim($_POST['ukodesub']);
    
    $tgl01=$_POST['utgl'];
    $periode1= date("d F Y", strtotime($tgl01));
    
    $pd= date("d", strtotime($tgl01));
    $pm= date("m", strtotime($tgl01));
    $py= date("Y", strtotime($tgl01));
    
    //if ($_SESSION['IDCARD']=="0000000143") {
    if ($pkode=="1" AND $psubkode=="03") {
        $periode1= date("01 F Y", strtotime($tgl01));
        if ((int)$pd>=16) $periode1= date("16 F Y", strtotime($tgl01));
        
    //}elseif ($_SESSION['IDCARD']=="0000000329") {
    }elseif ($pkode=="2" AND $psubkode=="21") {
        $periode1= date("01 F Y", strtotime($tgl01));
    }
    echo $periode1;
    
}elseif ($_GET['module']=="cariperiode2"){
    $pkode = trim($_POST['ukode']);
    $psubkode = trim($_POST['ukodesub']);
    
    $tgl01=$_POST['utgl'];
    $periode1= date("d F Y", strtotime($tgl01));
    
    $pd= date("d", strtotime($tgl01));
    $pm= date("m", strtotime($tgl01));
    $py= date("Y", strtotime($tgl01));
    
    //if ($_SESSION['IDCARD']=="0000000143") {
    if ($pkode=="1" AND $psubkode=="03") {
        $periode1= date("01 F Y", strtotime($tgl01));
        if ((int)$pd>=16) $periode1= date("16 F Y", strtotime($tgl01));
        $periode1= date("15 F Y", strtotime($tgl01));
        if ((int)$pd>=16) $periode1= date("t F Y", strtotime($tgl01));
        
    //}elseif ($_SESSION['IDCARD']=="0000000329") {
    }elseif ($pkode=="2" AND $psubkode=="21") {
        $periode1= date("t F Y", strtotime($tgl01));
    }
    echo $periode1;
    
}elseif ($_GET['module']=="cariperiode3"){
    
    $pkode = trim($_POST['ukode']);
    $psubkode = trim($_POST['ukodesub']);
    
    $tglaslal=$_POST['uasal'];
    
    $tgl01=$_POST['utgl'];
    $periode1= date("d F Y", strtotime($tgl01));
    
    $pd= date("d", strtotime($tgl01));
    $pm= date("m", strtotime($tgl01));
    $py= date("Y", strtotime($tgl01));
    
    //if ($_SESSION['IDCARD']=="0000000143") {
    if ($pkode=="1" AND $psubkode=="03") {
        $periode1= date("01 F Y", strtotime($tgl01));
        if ((int)$pd>=16) $periode1= date("16 F Y", strtotime($tgl01));
        
    //}elseif ($_SESSION['IDCARD']=="0000000329") {
    }elseif ($pkode=="2" AND $psubkode=="21") {
        $periode1= date("01 F Y", strtotime($tgl01));
    }else{
        $periode1= $tglaslal;
    }
    echo $periode1;
    
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
    
}elseif ($_GET['module']=="xxx"){
    
}
?>