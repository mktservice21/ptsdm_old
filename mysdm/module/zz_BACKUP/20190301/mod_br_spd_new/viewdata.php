<?php
session_start();
if ($_GET['module']=="viewsubkode"){
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
}elseif ($_GET['module']=="viewnomorspd"){

    if ($_SESSION['IDCARD']!="0000000148" AND $_SESSION['IDCARD']!="0000001854") { 
        echo "";
        exit;
    }

    include "../../config/koneksimysqli.php";
    $tgl01=$_POST['utgl'];

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
    $nomorspd="";
    $tno=1;
    $awal=3;

        $query = "SELECT MAX(SUBSTRING_INDEX(nomor, '/', 1)) as pnomor FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y'";
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

        $noslipurut=$tno."/UM-JKT/".$blromawi."/".$byear;


        $nomorspd=$noslipurut;

    echo $nomorspd;

}elseif ($_GET['module']=="viewnomorbukti"){

    if ($_SESSION['IDCARD']=="0000000148") { 
        echo "";
        exit;
    }

    include "../../config/koneksimysqli.php";
    $tgl01=$_POST['utgl'];

    $bl= date("m", strtotime($tgl01));
    $byear= date("Y", strtotime($tgl01));
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
    $nobuktinya="";
    $tno=1;
    $awal=3;

    if ($pdivsi=="OTC"){
        $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as pnomor FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi='$pdivsi'";// AND kodeid='$pkode'
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


        $noslipurut=$tno."/BR-OTC/".$blromawi."/".$byear;


        $nobuktinya=$noslipurut;
    }
    echo $nobuktinya;


}elseif ($_GET['module']=="hitungtotaldata"){

    include "../../config/koneksimysqli.php";
    $totalinput=0;
    $pdivisi=$_POST['udivisi'];
    $jenis=$_POST['ujenis'];
    $tgl01=$_POST['utgl'];
    $periode1= date("Y-m-d", strtotime($tgl01));
    $filterlampiran="";
    if (!empty($jenis)) $filterlampiran = " and case when ifnull(lampiran,'N')='' then 'N' else lampiran end ='$jenis' ";

    $query="SELECT SUM(jumlah) as jumlah from hrd.br_otc where "
            . " brOtcId not in (SELECT DISTINCT ifnull(brOtcId,'') from hrd.br_otc_reject) AND "
            . " DATE_FORMAT(tglbr,'%Y-%m-%d') = '$periode1' $filterlampiran"
            . " AND brOtcId NOT IN (SELECT DISTINCT ifnull(bridinput,'') from dbmaster.t_suratdana_br1 WHERE kodeinput='D')";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $tr= mysqli_fetch_array($tampil);
        if (!empty($tr['jumlah'])) $totalinput=$tr['jumlah'];
    }
    echo $totalinput;

}elseif ($_GET['module']=="xxxx"){


}
?>

