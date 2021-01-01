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
    
}elseif ($_GET['module']=="viewdatajeniskode"){
    include "../../config/koneksimysqli.php";
    
    $pjeniskode=$_POST['ujeniskode'];
    
    $pkode="1";
    $psubkode="01";
    
    if ($pjeniskode=="K" OR $pjeniskode=="B") {
        $pkode="2";
        $psubkode="20";
    }elseif ($pjeniskode=="S") {
        $pkode="6";
        $psubkode="80";
    }elseif ($pjeniskode=="J") {
        $pkode="3";
        $psubkode="50";
    }
    ?>
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode <span class='required'></span></label>
            <div class='col-xs-5'>
                  <select class='form-control input-sm' id='cb_kode' name='cb_kode' onchange="ShowSubKode();" data-live-search="true">
                      <option value='' selected>-- Pilihan --</option>
                      <?PHP
                        $query = "select distinct kodeid, nama from dbmaster.t_kode_spd order by kodeid";

                        $tampil = mysqli_query($cnmy, $query);
                        while ($z= mysqli_fetch_array($tampil)) {
                            if ($z['kodeid']==$pkode)
                                echo "<option value='$z[kodeid]' selected>$z[nama]</option>";
                            else
                                echo "<option value='$z[kodeid]'>$z[nama]</option>";
                        }
                      ?>
                  </select>
            </div>
        </div>



        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sub Kode <span class='required'></span></label>
            <div class='col-xs-5'>
                  <select class='form-control input-sm' id='cb_kodesub' name='cb_kodesub' data-live-search="true" onchange="">
                      <option value='' selected>-- Pilihan --</option>
                      <?PHP
                      //if ($_GET['act']=="editdata"){
                        $query = "select distinct kodeid, subkode, subnama from dbmaster.t_kode_spd where kodeid='$pkode' order by subkode";

                        $tampil = mysqli_query($cnmy, $query);
                        while ($z= mysqli_fetch_array($tampil)) {
                            if ($z['subkode']==$psubkode)
                                echo "<option value='$z[subkode]' selected>$z[subkode] - $z[subnama]</option>";
                            else
                                echo "<option value='$z[subkode]'>$z[subkode] - $z[subnama]</option>";
                        }
                      //}
                      ?>
                  </select>
            </div>
        </div>
    <?PHP
    mysqli_close($cnmy);
}elseif ($_GET['module']=="viewdatajenislampiran"){
    
    $pjeniskode=$_POST['ujeniskode'];
    $plmp1="";
    $plmp2="selected";
    $plmp3="";
    if ($pjeniskode=="B" OR $pjeniskode=="S") {
        $plmp1="";
        $plmp2="";
        $plmp3="selected";
    }elseif ($pjeniskode=="K" OR $pjeniskode=="D" OR $pjeniskode=="J") {
        $plmp1="selected";
        $plmp2="";
        $plmp3="";
    }
    echo "<option value='' $plmp1>--All--</option>";
    echo "<option value='Y' $plmp2>Ya</option>";
    echo "<option value='N' $plmp3>Tidak</option>";
    
    
}elseif ($_GET['module']=="viewdatanodivisiadjjenis"){
    
    include "../../config/koneksimysqli.php";
    $ajsnobr="";
    
    $userid=$_SESSION['IDCARD'];
    $pidkaryawan=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSETHZE01_".$userid."_$now ";
    $fdivisi_batal="";

    $query = "SELECT distinct brOtcId as brId from (
        select brOtcId from hrd.br_otc where batal='Y'  and year(tglbr)>'2018' $fdivisi_batal 
        UNION
        select DISTINCT brOtcId from dbmaster.backup_br_otc WHERE 1=1 $fdivisi_batal 
        ) as xxx";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);

    echo "<option value='' selected>-- Pilihan --</option>";
    $filbulansod_adj="";
    $query = "select 'OTC' divisi, nodivisi, jumlah from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' "
            . "and IFNULL(pilih,'') = 'Y' AND IFNULL(nodivisi,'')<>'' "
            . " AND karyawanid='$pidkaryawan' "
            . " AND idinput in (select distinct idinput from dbmaster.t_suratdana_br1 WHERE "
            . " bridinput IN (select distinct IFNULL(brOtcId,'') from $tmp01) ) $filbulansod_adj "
            . " ORDER BY 1,2";
    $tampil = mysqli_query($cnmy, $query);
    while ($z= mysqli_fetch_array($tampil)) {
        $pajsjmlbr=$z['jumlah'];
        if (!empty($pajsjmlbr)) $pajsjmlbr=number_format($pajsjmlbr,0);
        $pajsnobr=$z['nodivisi'];
        $pajsdivisi=$z['divisi'];
        if (empty($pajsdivisi)) $pajsdivisi= "ETHICAL";
        $pajsketjml = "$pajsnobr &nbsp;&nbsp (Rp. $pajsjmlbr)";//$pajsdivisi - 
        if (trim($pajsnobr)==trim($ajsnobr))
            echo "<option value='$pajsnobr' selected>$pajsketjml</option>";
        else
            echo "<option value='$pajsnobr'>$pajsketjml</option>";
    }

    mysqli_query($cnmy, "drop temporary table $tmp01");
    mysqli_close($cnmy);
    
    
}elseif ($_GET['module']=="xxx"){
    
}
?>