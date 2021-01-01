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
    
    
    $userid=$_SESSION['IDCARD'];
    $nobuktinya="";
    $tno=1;
    $awal=3;
    
    $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as pnomor FROM dbmaster.t_suratdana_br WHERE "
            . " stsnonaktif<>'Y' AND YEAR(tgl)='$tahuninput' AND "
            . " kodeid='1' AND subkode='04'";
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

    $ndiv="INC";
    $noslipurut=$tno."/$ndiv/".$blromawi."/".$byear;

    $nobuktinya=$noslipurut;

    echo $nobuktinya;
}

?>
