<?php
    session_start();
if ($_GET['module']=="viewnomorbukti") {
    
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
    
    $pidgroup=$_SESSION['GROUP'];
    $userid=$_SESSION['IDCARD'];
    $psubkode=$_POST['ukodesub'];
    $nobuktinya="";
    $tno=1;
    $awal=3;
    
    $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as pnomor FROM dbmaster.t_suratdana_br WHERE "
            . " stsnonaktif<>'Y' AND YEAR(tgl)='$tahuninput' AND "
            . " kodeid='2' AND subkode IN ('39') ";
    if ($pidgroup=="23" OR $pidgroup=="26") {
        $query .=" AND IFNULL(divisi,'') IN ('OTC', 'CHC', 'OT') ";
    }else{
        $query .=" AND IFNULL(divisi,'') NOT IN ('OTC', 'CHC', 'OT') ";
    }
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

    $ndiv="CAB";
    if ($pidgroup=="23" OR $pidgroup=="26") {
        $noslipurut=$tno."/KEU-CHC/$ndiv/".$blromawi."/".$byear;
    }else{
        $noslipurut=$tno."/KEU/$ndiv/".$blromawi."/".$byear;
    }

    $nobuktinya=$noslipurut;
    
    mysqli_close($cnmy);
    
    echo $nobuktinya;
}elseif ($_GET['module']=="hitungtotalcekboxkas") {
    include "../../config/koneksimysqli.php";
    $pnoid=$_POST['unoidbr'];
    $totalinput=0;
    if (!empty($pnoid)) {
        
        $query="SELECT SUM(jumlah) as jumlah from dbmaster.t_kaskecilcabang where idkascab IN $pnoid";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinput=$tr['jumlah'];
        }
    }
    mysqli_close($cnmy);
    echo $totalinput;
}elseif ($_GET['module']=="cxxxx") {
   
}elseif ($_GET['module']=="xxx") {
    
}

?>
