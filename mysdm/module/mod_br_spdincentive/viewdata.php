<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewnomorbukti") {
    
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
}elseif ($pmodule=="viewdatajumlahincentiv") {
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    include "../../config/koneksimysqli_ms.php";
    include "../../config/koneksimysqli.php";
    $tgl01=$_POST['ublninc'];
    $pblninc= date("Y-m", strtotime($tgl01));
    
    $pfrom=$_POST['ufrom'];
    $psubkode=$_POST['usubkod'];
    
    $pfilterjnsrpt="INCALL";
    $preadonlyincfrom="";
    if ($pfrom=="PM") {
        $preadonlyincfrom="Readonly";
        $pfilterjnsrpt="INCPM";
    }elseif ($pfrom=="GSM") {
        $pfilterjnsrpt="INCGSM";
    }

    $jmlc=0;
    $jmle=0;
    $jmlpea=0;
    $jmlp=0;
    $jumlah=0;
    
    
    
    $query = "select idinput, jenis_rpt, tglf, keterangan "
            . " from dbmaster.t_suratdana_br where subkode='$psubkode' AND "
            . " IFNULL(stsnonaktif,'')<>'Y' AND LEFT(tglf,7)='$pblninc' AND "
            . " IFNULL(jenis_rpt,'')='$pfilterjnsrpt'";
    $tampilinc= mysqli_query($cnmy, $query);
    $ketemuinc= mysqli_num_rows($tampilinc);
    
    if ((INT)$ketemuinc<=0) {
    
        $query = "select divisi, sum(jumlah) as jumlah from ms.incentiveperdivisi "
                . " WHERE LEFT(bulan,7)='$pblninc' AND IFNULL(jenis2,'')='$pfrom' ";
        $query .=" GROUP BY 1";
        $tampil= mysqli_query($cnms, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $ndivisi=$row['divisi'];
            $njumlah=$row['jumlah'];

            $jumlah=(DOUBLE)$jumlah+(DOUBLE)$njumlah;

            if ($ndivisi=="CAN") $jmlc=$njumlah;
            elseif ($ndivisi=="EAGLE") $jmle=$njumlah;
            elseif ($ndivisi=="PEACO") $jmlpea=$njumlah;
            elseif ($ndivisi=="PIGEO") $jmlp=$njumlah;

        }
    
    }
    ?>
    
    <div id="c_input">
        <div class='form-group'>
            <div id='loading2'></div>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><b>CANARY</b></label>
            <div class='col-md-3'>
                <input type='text' id='e_jmlc' name='e_jmlc' onblur="hit_total()" autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jmlc; ?>' <?PHP echo $preadonlyincfrom; ?>>
            </div>
        </div>
    </div>

    <div id="c_input">
        <div class='form-group'>
            <div id='loading2'></div>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><b>EAGLE</b></label>
            <div class='col-md-3'>
                <input type='text' id='e_jmle' name='e_jmle' onblur="hit_total()" autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jmle; ?>' >
            </div>
        </div>
    </div>

    <div id="c_input">
        <div class='form-group'>
            <div id='loading2'></div>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><b>PEACOCK</b></label>
            <div class='col-md-3'>
                <input type='text' id='e_jmlpea' name='e_jmlpea' onblur="hit_total()" autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jmlpea; ?>' >
            </div>
        </div>
    </div>

    <div id="c_input">
        <div class='form-group'>
            <div id='loading2'></div>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><b>PIGEON</b></label>
            <div class='col-md-3'>
                <input type='text' id='e_jmlp' name='e_jmlp' onblur="hit_total()" autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jmlp; ?>' >
            </div>
        </div>
    </div>

    <div id="c_input">
        <div class='form-group'>
            <div id='loading2'></div>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah</label>
            <div class='col-md-3'>
                <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>' Readonly>
            </div>
        </div>
    </div>
    
    <?PHP
    
    mysqli_close($cnms);
    mysqli_close($cnmy);
}

?>
