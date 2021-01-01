<?php
    session_start();
if ($_GET['module']=="gantitombol") {
    $ptipe=$_POST['utipe'];
    ?>
        <div id="c_tombol">
            <div class='col-sm-6'>
                <small>&nbsp;</small>
               <div class="form-group">
                   <?PHP
                   if ($ptipe=="A") {
                       echo "<input type='button' class='btn btn-success btn-xs' id='s-submit1' value='Belum Proses' onclick=\"TampilData('1')\">&nbsp;";
                       echo "<input type='button' class='btn btn-info btn-xs' id='s-submit2' value='Sudah Proses' onclick=\"TampilData('2')\">&nbsp;";
                       echo "<input type='button' class='btn btn-primary btn-xs' id='s-print' value='Preview SPD' onclick=\"disp_confirm_print('bukan')\">";
                       echo "<input type='button' class='btn btn-warning btn-xs' id='s-print' value='Excel SPD' onclick=\"disp_confirm_print('excel')\">";
                   }elseif ($ptipe=="B") {
                       echo "<input type='button' class='btn btn-success' id='s-submit' value='View Data' onclick=\"TampilDataPD()\">&nbsp;";
                       ?><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=suratpd&idmenu=204&act=tambahbaru"; ?>';"><?PHP
                       echo "<input type='button' class='btn btn-primary' id='s-print' value='Preview SPD' onclick=\"disp_confirm_print('bukan')\">";
                       echo "<input type='button' class='btn btn-warning' id='s-print' value='Excel SPD' onclick=\"disp_confirm_print('excel')\">";
                   }elseif ($ptipe=="C") {
                       echo "<input type='button' class='btn btn-success btn-xs' id='s-submit1' value='Lihat Data SPD' onclick=\"TampilDataNOSPD('1')\">&nbsp;";
                       echo "<input type='button' class='btn btn-info btn-xs' id='s-submit2' value='Sudah Ada No BBM' onclick=\"TampilDataNOSPD('2')\">&nbsp;";
                   }elseif ($ptipe=="D") {
                       echo "<input type='button' class='btn btn-success btn-xs' id='s-submit1' value='Lihat Data SPD' onclick=\"TampilDataNOSPD('3')\">&nbsp;";
                       echo "<input type='button' class='btn btn-info btn-xs' id='s-submit2' value='Sudah Ada No BBK' onclick=\"TampilDataNOSPD('4')\">&nbsp;";
                   }
                   ?>
               </div>
           </div>
       </div>
    <?PHP
}elseif ($_GET['module']=="viewnomorspd") {

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
}elseif ($_GET['module']=="viewnomorbukti") {
    $pkode = trim($_POST['ukode']);
    $psubkode = trim($_POST['ukodesub']);
    
    if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="2" AND $psubkode=="20") OR ($pkode=="3") ) {
        
    }else{
        echo ""; exit;
    }
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
    $padvance = trim($_POST['uadvance']);
    
    $userid=$_SESSION['IDCARD'];
    $nobuktinya="";
    $tno=1;
    $awal=3;
    
    $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as pnomor FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND YEAR(tgl)='$tahuninput' AND "
            . " karyawanid='$userid'";
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

    $ndiv="PEA";
    if ($pdivsi=="HO") $ndiv="HO";
    if ($pdivsi=="PIGEO") $ndiv="P";
    if ($pdivsi=="EAGLE") $ndiv="E";
    if ($pdivsi=="EAGLE"){ 
        $byear= date("y", strtotime($tgl01));
        $noslipurut=$tno."/BR $ndiv/".$blromawi."/".$byear;
    }else
        $noslipurut=$tno."/BR-$ndiv/".$blromawi."/".$byear;

    $nobuktinya=$noslipurut;

    echo $nobuktinya;
                    
                    
}elseif ($_GET['module']=="hitungtotalcekboxspd") {
    include "../../config/koneksimysqli.php";
    $pnoid=$_POST['unoidbr'];
    $totalinput=0;
    
    if (!empty($pnoid)) {
        $query="SELECT SUM(jumlah) as jumlah from dbmaster.t_suratdana_br WHERE idinput IN $pnoid";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $totalinput=$tr['jumlah'];
        }
    }
    
    echo $totalinput;
}elseif ($_GET['module']=="cariinputdiv") {
    include "../../config/koneksimysqli.php";
    $hari_ini = date("Y-m-d");
    $tgl1 = date('d F Y', strtotime($hari_ini));

    $pidinput=$_POST['uid'];
    $pkode=$_POST['ukode'];
    $psubkode=$_POST['usubkode'];
    $ptgl=$_POST['utgl'];
    
    $eperiode1=$ptgl;
    $eperiode2=$ptgl;
    
    $pdivnomor="";
    
    $jumlah="";
    
    $pjnsrpt="";
    if ($pkode=="2") $pjnsrpt="K";
    $pjens1="";
    $pjens2="";
    $pjens3="";
    $pjens4="";
    
    $jenis="";
    
    $ajsnospd="";
    $ajsnobr="";
    
    $pilihperiodetipe="T";
    
    if ( $pkode=="3") $tgl1 = date('F Y', strtotime($hari_ini));
    if (!empty($pidinput)) {
        
        
        $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_br WHERE idinput='$pidinput'");
        $r    = mysqli_fetch_array($edit);
        $jumlah=$r['jumlah'];
        $pdivnomor=$r['nodivisi'];
        
        $pjnsrpt = $r['jenis_rpt'];
        
        $jenis = $r['lampiran'];
        
        $tgl1 = date('d F Y', strtotime($r['tgl']));
        
        $pilihperiodetipe=$r['periodeby'];
        if (empty($pilihperiodetipe)) $pilihperiodetipe="I";
        
        $ajsnospd=$r['nomor2'];
        $ajsnobr=$r['nodivisi2'];
        
        if (!empty($r['bulan2'] AND $r['bulan2']<>"0000-00-00")) {
            $tgl1 = date("F Y", strtotime($r['bulan2']));
            if ( $pkode=="3") $ptgl=$r['bulan2'];
        }
        
    }
    
    $pjens1="";
    $pjens2="";
    $pjens3="";
    $pjens4="";
    if ($pjnsrpt=="A") $pjens1="selected";
    if ($pjnsrpt=="K") $pjens2="selected";
    if ($pjnsrpt=="B") $pjens3="selected";
    if ($pjnsrpt=="D") $pjens4="selected";
    
    $plmp1="selected";
    $plmp2="";
    $plmp3="";
    if ($jenis=="Y") {
        $plmp1="";
        $plmp2="selected";
        $plmp3="";
    }elseif ($jenis=="N") {
        $plmp1="";
        $plmp2="";
        $plmp3="selected";
    }

    $ptupeper1="";
    $ptupeper2="";
    $ptupeper3="selected";
    $ptupeper4="";
    if ($pilihperiodetipe=="T") $ptupeper2="selected";
    if ($pilihperiodetipe=="I") $ptupeper3="selected";
    if ($pilihperiodetipe=="S") $ptupeper4="selected";

    ?>
        <script src="js/inputmask.js"></script>
        <script type="text/javascript">
            $(function() {
                $('#e_periode1').datepicker({
                    changeMonth: true,
                    changeYear: true,
                    numberOfMonths: 1,
                    firstDay: 1,
                    dateFormat: 'dd MM yy',
                    onSelect: function(dateStr) {
                        document.getElementById('e_periode2').value=document.getElementById('e_periode1').value;
                    }
                });

                $('#e_periode2').datepicker({
                    changeMonth: true,
                    changeYear: true,
                    numberOfMonths: 1,
                    firstDay: 1,
                    dateFormat: 'dd MM yy',
                    onSelect: function(dateStr) {
                        
                    }
                });

            });
        </script>
    <?PHP
    
    if ( ($pkode=="3") ) {
        $ajsbulan= date("Y-m", strtotime($ptgl));
    ?>
        <!--<input type="hidden" id="e_nomordiv" name="e_nomordiv" value='<?PHP echo $pdivnomor; ?>'>-->
        <input type="hidden" id="cb_jenispilih" name="cb_jenispilih">
        <input type="hidden" id="cb_jenis" name="cb_jenis">
        <input type="hidden" id="cb_pertipe" name="cb_pertipe">
        <input type="hidden" id="e_periode1" name="e_periode1" value='<?PHP echo $eperiode1; ?>'>
        <input type="hidden" id="e_periode2" name="e_periode2" value='<?PHP echo $eperiode2; ?>'>
        

        <script>
            $(function() {
                $('#e_tglberlaku').datepicker({
                    showButtonPanel: true,
                    changeMonth: true,
                    changeYear: true,
                    numberOfMonths: 1,
                    firstDay: 1,
                    dateFormat: 'MM yy',
                    onSelect: function(dateStr) {

                    },
                    onClose: function() {
                        var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                        var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                        $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                        ShowDataAjsNoSPD();
                        ShowDataNoDivisiBR();
                    },

                    beforeShow: function() {
                        if ((selDate = $(this).val()).length > 0) 
                        {
                            iYear = selDate.substring(selDate.length - 4, selDate.length);
                            iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                            $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                            $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                        }
                    }
                });
            });
        </script>
        <style>
            .ui-datepicker-calendar {
                display: none;
            }
        </style>
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan Pengajuan Dana</label>
            <div class='col-md-3'>
                <div class='input-group date' id=''>
                    <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl1; ?>'>
                    <span class='input-group-addon'>
                        <span class='glyphicon glyphicon-calendar'></span>
                    </span>
                </div>
            </div>
        </div>
        
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih No.SPD <span class='required'></span></label>
            <div class='col-xs-5'>
                <select class='form-control input-sm' id='cb_ajsnospd' name='cb_ajsnospd' onchange="ShowDataNoDivisiBR();">
                    <option value='' selected>-- Pilihan --</option>
                    <?PHP
                            
        
                    $query = "select nomor, SUM(jumlah) jumlah from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' "
                            . "and IFNULL(pilih,'') = 'Y' AND IFNULL(nomor,'')<>'' AND "
                            . "( DATE_FORMAT(tgl,'%Y-%m')='$ajsbulan' OR DATE_FORMAT(tglspd,'%Y-%m')='$ajsbulan' )"
                            . "GROUP BY 1 ORDER BY 1";
                    $tampil = mysqli_query($cnmy, $query);
                    while ($z= mysqli_fetch_array($tampil)) {
                        $pajsjml=$z['jumlah'];
                        if (!empty($pajsjml)) $pajsjml=number_format($pajsjml,0);
                        $pajsnospd=$z['nomor'];
                        
                        $pajsketjml = "$pajsnospd (Rp. $pajsjml)";
                        if ($pajsnospd==$ajsnospd)
                            echo "<option value='$pajsnospd' selected>$pajsketjml</option>";
                        else
                            echo "<option value='$pajsnospd'>$pajsketjml</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        
        
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih No.BR/Divisi <span class='required'></span></label>
            <div class='col-xs-5'>
                <select class='form-control input-sm' id='cb_ajsnobr' name='cb_ajsnobr'>
                    <option value='' selected>-- Pilihan --</option>
                    <?PHP
                    $query = "select divisi, nodivisi, SUM(jumlah) jumlah from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' "
                            . "and IFNULL(pilih,'') = 'Y' AND IFNULL(nodivisi,'')<>'' AND "
                            . "( DATE_FORMAT(tgl,'%Y-%m')='$ajsbulan' OR DATE_FORMAT(tglspd,'%Y-%m')='$ajsbulan' )"
                            . "GROUP BY 1,2 ORDER BY 1,2";
                    $tampil = mysqli_query($cnmy, $query);
                    while ($z= mysqli_fetch_array($tampil)) {
                        $pajsjmlbr=$z['jumlah'];
                        if (!empty($pajsjmlbr)) $pajsjmlbr=number_format($pajsjmlbr,0);
                        $pajsnobr=$z['nodivisi'];
                        $pajsdivisi=$z['divisi'];
                        if (empty($pajsdivisi)) $pajsdivisi= "ETHICAL";
                        $pajsketjml = "$pajsdivisi - $pajsnobr   &nbsp;&nbsp;&nbsp;&nbsp;    (Rp. $pajsjmlbr)";
                        if (trim($pajsnobr)==trim($ajsnobr))
                            echo "<option value='$pajsnobr' selected>$pajsketjml</option>";
                        else
                            echo "<option value='$pajsnobr'>$pajsketjml</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Divisi / No. BR <span class='required'></span></label>
            <div class='col-xs-3'>
                <input type='text' id='e_nomordiv' name='e_nomordiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivnomor; ?>'>
            </div>
        </div>
        
        <div class='form-group'>
            <div id='loading2'></div>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                Jumlah
            </label>
            <div class='col-md-3'>
                <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>' >
            </div>
        </div>
            
    <?PHP
    }else{
        ?>
        
        <input type="hidden" id="cb_ajsnospd" name="cb_ajsnospd">
        <input type="hidden" id="cb_ajsnobr" name="cb_ajsnobr">
        
        <script>
            $(function() {
                $('#e_tglberlaku').datepicker({
                    changeMonth: true,
                    changeYear: true,
                    numberOfMonths: 1,
                    firstDay: 1,
                    dateFormat: 'dd MM yy',
                    onSelect: function(dateStr) {
                        //ShowNoSPD();
                        ShowNoBukti();
                        document.getElementById('e_periode1').value=document.getElementById('e_tglberlaku').value;
                        document.getElementById('e_periode2').value=document.getElementById('e_tglberlaku').value;
                    } 
                });    
            });
        </script>
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal Pengajuan Dana</label>
            <div class='col-md-3'>
                <div class='input-group date' id=''>
                    <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl1; ?>'>
                    <span class='input-group-addon'>
                        <span class='glyphicon glyphicon-calendar'></span>
                    </span>
                </div>
            </div>
        </div>
        <?PHP
        if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="2" AND $psubkode=="20") ) {
    ?>
        
            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Divisi / No. BR <span class='required'></span></label>
                <div class='col-xs-3'>
                    <input type='text' id='e_nomordiv' name='e_nomordiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivnomor; ?>'>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Jenis <span class='required'></span></label>
                <div class='col-md-3'>
                    <div class="form-group">

                            <select class='form-control input-sm' id="cb_jenispilih" name="cb_jenispilih" onchange="" data-live-search="true">
                                <?PHP
                                if ($pkode=="1"){
                                    echo "<option value='A' $pjens1>Advance</option>";
                                    echo "<option value='B' $pjens3>Belum Ada Kuitansi</option>";
                                }else{
                                    echo "<option value='K' $pjens2>Klaim</option>";
                                    echo "<option value='B' $pjens3>Belum Ada Kuitansi</option>";
                                }
                                ?>
                            </select>

                    </div>
                </div>
            </div>


            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Lampiran <span class='required'></span></label>
                <div class='col-md-3'>
                    <div class="form-group">

                        <select class='form-control input-sm' id="cb_jenis" name="cb_jenis" onchange="" data-live-search="true">
                            <option value="" <?PHP echo $plmp1; ?>>--All--</option>
                            <option value="Y" <?PHP echo $plmp2; ?>>Ya</option>
                            <option value="N" <?PHP echo $plmp3; ?>>Tidak</option>
                        </select>

                    </div>
                </div>
            </div>


            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Periode By <span class='required'></span></label>
                <div class='col-md-3'>
                    <div class="form-group">

                        <select class='form-control input-sm' id="cb_pertipe" name="cb_pertipe" onchange="" data-live-search="true">
                            <option value="T" <?PHP echo $ptupeper2; ?>>Transfer</option>
                            <option value="I" <?PHP echo $ptupeper3; ?>>Input</option>
                            <option value="S" <?PHP echo $ptupeper4; ?>>Rpt SBY</option>
                        </select>

                    </div>
                </div>
            </div>

            <div class='form-group'>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;"> <span class='required'></span></label>
                <div class='col-md-5'>
                    <div class='input-group date' id=''>
                        <input type="text" class="form-control" id='e_periode1' name='e_periode1' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $eperiode1; ?>'>
                        <span class='input-group-addon'>
                            <span class='glyphicon glyphicon-calendar'></span>
                        </span>

                        <input type="text" class="form-control" id='e_periode2' name='e_periode2' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $eperiode2; ?>'>
                        <span class='input-group-addon'>
                            <span class='glyphicon glyphicon-calendar'></span>
                        </span>
                    </div>
                </div>
            </div>


            <div class='form-group'>
                <div id='loading2'></div>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                    <button type='button' class='btn btn-info btn-xs' onclick='HitungData()'>Tampilkan Data</button> <span class='required'></span>
                </label>
                <div class='col-md-3'>
                    <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>' Readonly>
                </div>
            </div>


        <?PHP
        }else{
            $pdivnomor="";
        ?>

            <input type="hidden" id="e_nomordiv" name="e_nomordiv" value='<?PHP echo $pdivnomor; ?>'>
            <input type="hidden" id="cb_jenispilih" name="cb_jenispilih">
            <input type="hidden" id="cb_jenis" name="cb_jenis">
            <input type="hidden" id="cb_pertipe" name="cb_pertipe">
            <input type="hidden" id="e_periode1" name="e_periode1" value='<?PHP echo $eperiode1; ?>'>
            <input type="hidden" id="e_periode2" name="e_periode2" value='<?PHP echo $eperiode2; ?>'>

            <div class='form-group'>
                <div id='loading2'></div>
                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                    Jumlah
                </label>
                <div class='col-md-3'>
                    <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>' >
                </div>
            </div>

        <?PHP
        }
    }
    
}elseif ($_GET['module']=="hitungtotalcekboxbr") {
    include "../../config/koneksimysqli.php";
    $pnoid=$_POST['unoidbr'];
    $padvance=$_POST['uadvance'];
    $userid=$_SESSION['IDCARD'];
    $totalinput=0;
    if (!empty($pnoid)) {
        $now=date("mdYhis");
        $tmp01 =" dbtemp.DSETHK01_".$userid."_$now ";
        
        $query = "select brId, jumlah, jumlah1 from hrd.br0 where brId in $pnoid";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        if ($padvance=="A"){
        }else{
            mysqli_query($cnmy, "UPDATE $tmp01 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)>0");
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
}elseif ($_GET['module']=="viewajsnomorspd") {
    include "../../config/koneksimysqli.php";
    $pajsnospd=$_POST['uajsspd'];
    $tgl01=$_POST['utgl'];
    $ajsbulan= date("Y-m", strtotime($tgl01));
    
    $filajsnospd="";
    if (!empty($pajsnospd)) $filajsnospd=" AND IFNULL(nomor,'')='$pajsnospd' ";
    $query = "select nomor, SUM(jumlah) jumlah from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' "
            . "and IFNULL(pilih,'') = 'Y' AND IFNULL(nomor,'')<>'' $filajsnospd AND "
            . "( DATE_FORMAT(tgl,'%Y-%m')='$ajsbulan' OR DATE_FORMAT(tglspd,'%Y-%m')='$ajsbulan' )"
            . "GROUP BY 1 ORDER BY 1";
    $tampil = mysqli_query($cnmy, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    
    while ($zs= mysqli_fetch_array($tampil)) {
        $pajsjmlbr=$zs['jumlah'];
        if (!empty($pajsjmlbr)) $pajsjmlbr=number_format($pajsjmlbr,0);
        $pajsnobr=$zs['nomor'];
        //$pajsdivisi=$zs['divisi'];
        //if (empty($pajsdivisi)) $pajsdivisi= "ETHICAL";
        //$pajsketjml = "$pajsdivisi - $pajsnobr   &nbsp;&nbsp;&nbsp;&nbsp;    (Rp. $pajsjmlbr)";
        $pajsketjml = "$pajsnobr   &nbsp;&nbsp;&nbsp;&nbsp;    (Rp. $pajsjmlbr)";
        
        echo "<option value='$pajsnobr'>$pajsketjml</option>";
    }
}elseif ($_GET['module']=="viewajsnomorbrdivisi") {
    include "../../config/koneksimysqli.php";
    $pajsnospd=$_POST['uajsspd'];
    $tgl01=$_POST['utgl'];
    $ajsbulan= date("Y-m", strtotime($tgl01));
    
    $filajsnospd="";
    if (!empty($pajsnospd)) $filajsnospd=" AND IFNULL(nomor,'')='$pajsnospd' ";
    $query = "select divisi, nodivisi, SUM(jumlah) jumlah from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' "
            . "and IFNULL(pilih,'') = 'Y' AND IFNULL(nodivisi,'')<>'' $filajsnospd AND "
            . "( DATE_FORMAT(tgl,'%Y-%m')='$ajsbulan' OR DATE_FORMAT(tglspd,'%Y-%m')='$ajsbulan' )"
            . "GROUP BY 1,2 ORDER BY 1,2";
    $tampil = mysqli_query($cnmy, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    
    while ($zs= mysqli_fetch_array($tampil)) {
        $pajsjmlbr=$zs['jumlah'];
        if (!empty($pajsjmlbr)) $pajsjmlbr=number_format($pajsjmlbr,0);
        $pajsnobr=$zs['nodivisi'];
        $pajsdivisi=$zs['divisi'];
        if (empty($pajsdivisi)) $pajsdivisi= "ETHICAL";
        $pajsketjml = "$pajsdivisi - $pajsnobr   &nbsp;&nbsp;&nbsp;&nbsp;    (Rp. $pajsjmlbr)";
        
        echo "<option value='$pajsnobr'>$pajsketjml</option>";
    }
}elseif ($_GET['module']=="showdatanospdnodiv") {
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    include "../../config/fungsi_sql.php";
    
    $ppilihan=$_GET['pilih'];
    
    $pchksama=$_POST['uchksama'];
    $pnoakhir=$_POST['unoakhir'];
    
    $ptgl=$_POST['utgl'];
    $pblnini = date('m', strtotime($ptgl));
    $pthnini = date('Y', strtotime($ptgl));
    
    if (empty($pnoakhir)) {
        $tno="1500";
        if ($ppilihan=="nobbm"){
            $query = "SELECT LTRIM(REPLACE(MAX(SUBSTRING_INDEX(nobbm, '/', 1)),'BBM','')) as pnomor FROM dbmaster.t_suratdana_br1 
                WHERE idinput IN (SELECT DISTINCT idinput from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'') <> 'Y' 
                AND YEAR(tgl)='$pthnini')";
        }else{
            $query = "SELECT LTRIM(REPLACE(MAX(SUBSTRING_INDEX(nobbk, '/', 1)),'BBK','')) as pnomor FROM dbmaster.t_suratdana_br1 
                WHERE idinput IN (SELECT DISTINCT idinput from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'') <> 'Y' 
                AND YEAR(tgl)='$pthnini')";
        }
        $showkan= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($showkan);
        if ($ketemu>0){
            $sh= mysqli_fetch_array($showkan);
            if (!empty($sh['pnomor'])) { $tno=(INT)$sh['pnomor']; }
        }
        
    }else{
        $tno=(INT)$pnoakhir;
    }
    if ($pchksama=="Y") $tno++;
    
    $mbulan=CariBulanHuruf($pblnini);
    
    
    $mychk=$_POST['ucekbox'];
    $filnodiv=" AND idinput='' ";
    if (!empty($mychk)) {
        //$mychk=substr($mychk, 0, -1);
        $filnodiv=" AND idinput IN $mychk ";
    }
    
?>
    <div class='x_content'>

        <table id='datatablespdbbk' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='2px'>No</th>
                    <th width='2px'></th>
                    <th width='50px'>Divisi</th>
                    <th width='100px'>No. Divisi/BR</th>
                    <th width='100px'>Jumlah</th>
                    <th width='50px'>Kode</th>
                    <?PHP
                    if ($ppilihan=="nobbm"){
                        echo "<th width='50px'>NO. BBM</th>";
                    }else{
                        echo "<th width='50px'>NO. BBK</th>";
                    }
                    ?>
                    <th width='50px'></th>
                </tr>
            </thead>
            <tbody>

                <?PHP
                $no=1;
                $query = "select *, FORMAT(jumlah,0,'de_DE') rpjumlah from dbtemp.t_sp4 WHERE 1=1 $filnodiv order by idinput, divisi, nodivisi, jmlrec DESC, urutan";
                $tampil=mysqli_query($cnmy, $query) or die("error");
                while( $row=mysqli_fetch_array($tampil) ) {
                    $pjmlrec=$row['jmlrec'];
                    $pdivisi=$row['divisi'];
                    if (empty($pdivisi)) $pdivisi = "ETHICAL";
                    //elseif (empty($pdivisi) AND (INT)$pjmlrec==0) $pdivisi = "HO";
                    $pnodivisi=$row['nodivisi'];
                    $pnmkode=$row['nama'];
                    $pnmsub=$row['subnama'];
                    $pjumlah=$row['rpjumlah'];
                    $pmyno=$row['mynourut'];
                    
                    $pidinout=$row['idinput'];
                    $purutan=$row['urutan'];
                    
                    $onsimpan="SimpanHapusData";
                    if ($ppilihan=="nobbm"){
                        $onsimpan="SimpanHapusDataNOBBM";
                        $nosudahinput= getfieldcnmy("SELECT nobbm as lcfields FROM dbmaster.t_suratdana_br1 WHERE idinput=$pidinout AND urutan=$purutan AND IFNULL(nobbm,'')<>'' LIMIT 1");
                    }else{
                        $nosudahinput= getfieldcnmy("SELECT nobbk as lcfields FROM dbmaster.t_suratdana_br1 WHERE idinput=$pidinout AND urutan=$purutan AND IFNULL(nobbk,'')<>'' LIMIT 1");
                    }
                    
                    
                    $pedit="";
                    
                    $cnminput="e_noinput$pmyno";
                    $cnmurut="e_nourut$pmyno";
                    $cnmnobbk="txtbbk$pmyno";
                    
                    
                    echo "<tr>";
                    echo "<td>$no<t/d>";
                    echo "<td>"
                        . "<input type='hidden' name='$cnminput' id='$cnminput' size='20px' value='$pidinout'>"
                        . "<input type='hidden' name='$cnmurut' id='$cnmurut' size='20px' value='$purutan'>"
                        . "</td>";
                    echo "<td nowrap>$pdivisi</td>";// ($pjmlrec)
                    echo "<td nowrap>$pnodivisi</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap>$pnmkode ($pnmsub)</td>";
                    

                    
                    $fsimpan="'$cnminput', '$cnmurut', '$cnmnobbk'";
                    

                    $csimpan= "<input type='button' class='btn btn-info btn-xs' id='s-submit' value='Save' onclick=\"$onsimpan('input', $fsimpan)\">";
                    $chapus= "<input type='button' class='btn btn-danger btn-xs' id='s-hapus' value='Hapus' onclick=\"$onsimpan('hapus', $fsimpan)\">";
                    $ctombol="";
                    
                    if (!empty($nosudahinput)){
                        echo "<td nowrap>$nosudahinput</td>";
                        $ctombol="$chapus";
                    }else{
                        
                        if ($pchksama=="N") $tno=(INT)$tno+1;
                        
                        if ($ppilihan=="nobbm"){
                            $noterakhir = "BBM".$tno."/".$mbulan."/".$pthnini;
                        }else{
                            $noterakhir = "BBK".$tno."/".$mbulan."/".$pthnini;
                        }
                    
                        echo "<td nowrap><input type='text' name='$cnmnobbk' id='$cnmnobbk' size='20px' value='$noterakhir'></td>";
                        $ctombol="$csimpan";
                    }
                    echo "<td nowrap>$ctombol</td>";
                    echo "</tr>";
                    $no++;
                }
                ?>

            </tbody>
        </table>
        <?PHP
        $query ="select mynourut from dbtemp.t_sp4";
        $jmlrec= mysqli_num_rows(mysqli_query($cnmy, $query));
        echo "<input type='hidden' name='e_jmldata' id='e_jmldata' size='20px' value='$jmlrec'>";
        ?>
    </div>       
            
            
    <style>
        .divnone {
            display: none;
        }
        #datatablespdbbk th {
            font-size: 13px;
        }
        #datatablespdbbk td { 
            font-size: 11px;
        }
        .imgzoom:hover {
            -ms-transform: scale(3.5); /* IE 9 */
            -webkit-transform: scale(3.5); /* Safari 3-8 */
            transform: scale(3.5);

        }
    </style>
    <script>
        $(document).ready(function() {

            //alert(etgl1);
            var dataTable = $('#datatablespdbbk').DataTable( {
                "bPaginate": false,
                "bLengthChange": false,
                "bFilter": true,
                "bInfo": false,
                "ordering": false,
                "searching": false,
                "order": [[ 0, "desc" ]],
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "columnDefs": [
                    { "visible": false },
                    { "orderable": false, "targets": 0 },
                    { "orderable": false, "targets": 1 },
                    { className: "text-right", "targets": [4] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7] }//nowrap

                ],
                "language": {
                    "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
                },
                "scrollY": 280,
                "scrollX": true/*,
                rowReorder: {
                    selector: 'td:nth-child(3)'
                },
                responsive: true*/
            } );
        } );
    </script>
<?PHP
}elseif ($_GET['module']=="viewnourutbbk") {
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    
    $ptgl=$_POST['utgl'];
    $pblnini = date('m', strtotime($ptgl));
    $pthnini = date('Y', strtotime($ptgl));

    $tno="1500";
    $query = "SELECT LTRIM(REPLACE(MAX(SUBSTRING_INDEX(nobbk, '/', 1)),'BBK','')) as pnomor FROM dbmaster.t_suratdana_br1 
        WHERE idinput IN (SELECT DISTINCT idinput from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'') <> 'Y' 
        AND YEAR(tgl)='$pthnini')";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $sh= mysqli_fetch_array($showkan);
        if (!empty($sh['pnomor'])) { $tno=(INT)$sh['pnomor']; }
    }

    $mbulan=CariBulanHuruf($pblnini);
    $noterakhir = "BBK".$tno."/".$mbulan."/".$pthnini;
    echo $tno;
}elseif ($_GET['module']=="viewnourutbbm") {
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    
    $ptgl=$_POST['utgl'];
    $pblnini = date('m', strtotime($ptgl));
    $pthnini = date('Y', strtotime($ptgl));

    $tno="1500";
    $query = "SELECT LTRIM(REPLACE(MAX(SUBSTRING_INDEX(nobbm, '/', 1)),'BBM','')) as pnomor FROM dbmaster.t_suratdana_br1 
        WHERE idinput IN (SELECT DISTINCT idinput from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'') <> 'Y' 
        AND YEAR(tgl)='$pthnini')";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $sh= mysqli_fetch_array($showkan);
        if (!empty($sh['pnomor'])) { $tno=(INT)$sh['pnomor']; }
    }

    $mbulan=CariBulanHuruf($pblnini);
    $noterakhir = "BBM".$tno."/".$mbulan."/".$pthnini;
    echo $tno;
    
}elseif ($_GET['module']=="simpandatanobbk" OR $_GET['module']=="simpandatanobbm") {
    $berhasil = "Tidak ada data yang disimpan...";
    include "../../config/koneksimysqli.php";
    
    $pidinput=$_POST['uidinput'];
    $purutan=$_POST['uurutan'];
    $pnobbk=$_POST['unobbk'];
    
    if (!empty($pidinput) AND !empty($purutan)){
        $isimpan = " nobbk='$pnobbk' ";
        if ($_GET['module']=="simpandatanobbm") $isimpan = " nobbm='$pnobbk' ";
        
        $query = "UPDATE dbmaster.t_suratdana_br1 SET $isimpan WHERE idinput=$pidinput AND urutan=$purutan";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $berhasil = "data berhasi disimpan...";
    }
    
    echo $berhasil;
}elseif ($_GET['module']=="hapusdatanobbk" OR $_GET['module']=="hapusdatanobbm") {
    $berhasil = "Tidak ada data yang dihapus...";
    include "../../config/koneksimysqli.php";
    
    $pidinput=$_POST['uidinput'];
    $purutan=$_POST['uurutan'];
    $pnobbk=$_POST['unobbk'];
    
    if (!empty($pidinput) AND !empty($purutan)){
        
        $isimpan = " nobbk=NULL ";
        if ($_GET['module']=="hapusdatanobbm") $isimpan = " nobbm=NULL ";
        
        $query = "UPDATE dbmaster.t_suratdana_br1 SET $isimpan WHERE idinput=$pidinput AND urutan=$purutan";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $berhasil = "data berhasi dihapus...";
    }
    
    echo $berhasil;
}elseif ($_GET['module']=="xxx") {
    
}
?>
