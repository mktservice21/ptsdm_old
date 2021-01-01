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
    $query = "select kodeid, subkode, subnama from dbmaster.t_kode_spd WHERE kodeid='$pkode' AND CONCAT(kodeid,subkode) IN ('103', '205', '221')";
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
    
    if ($psubkode=="05") $awal=2;
    
    $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as pnomor FROM dbmaster.t_suratdana_br WHERE "
            . " stsnonaktif<>'Y' AND YEAR(tgl)='$tahuninput' "
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
        $noslipurut=$tno."/LK/".$blromawi."/".$byear;
    elseif ($psubkode=="05")
        $noslipurut=$tno."/CARUTIN/".$blromawi."/".$byear;
    else
        $noslipurut=$tno."/RUTIN/".$blromawi."/".$byear;


    $nobuktinya=$noslipurut;
    
    echo $nobuktinya;
    
    mysqli_close($cnmy);
    
}elseif ($_GET['module']=="hitungtotalcekboxbr"){
    include "../../config/koneksimysqli.php";
    $pnoid=$_POST['unoidbr'];
    $totalinput=0;
    if (!empty($pnoid)) {
        $query ="select sum(jumlah) jumlah from dbmaster.t_sewa WHERE 1=1 AND idsewa in $pnoid";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinput=$tr['jumlah'];
        }
    }
    mysqli_close($cnmy);
    
    echo $totalinput;
    
}elseif ($_GET['module']=="xxx"){
    
}
?>