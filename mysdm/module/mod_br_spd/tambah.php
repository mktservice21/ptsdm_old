<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
</style>

<?PHP

$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d F Y', strtotime($hari_ini));
$eperiode1 = date('01 F Y', strtotime($hari_ini));
$eperiode2 = date('t F Y', strtotime($hari_ini));
       
$pidkaryawan=$_SESSION['IDCARD'];
$divisi="";
if ($pidkaryawan=="0000001043") $divisi="EAGLE";

$pkode="1";
$psubkode="01";
$pnomor="";
$pdivnomor="";  
$jumlah="";
$pjmladj="";
$pjm_total="";

$keterangan="";

$nreadonjml="";

$nlabelperiode="";
$pjnsrpt="A";
$jenis="Y";
$pilihperiodetipe="I";

$pots_rppcm=0;
$pots_jml=0;
$pots_sisarp=0;

$act="input";

if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_br WHERE idinput='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idbr=$r['idinput'];
    $tglberlku = date('d/m/Y', strtotime($r['tgl']));
    $tgl1 = date('d F Y', strtotime($r['tgl']));
    
    $eperiode1 = date('d F Y', strtotime($r['tglf']));
    $eperiode2 = date('d F Y', strtotime($r['tglt']));

    $pkode=$r['kodeid'];
    $psubkode=$r['subkode'];
    $pnomor=$r['nomor'];
    $pdivnomor=$r['nodivisi'];
    
    $jumlah=$r['jumlah'];
    $pjmladj=$r['jumlah2'];
    $pjm_total=(double)$jumlah+(double)$pjmladj;
    
    $divisi=$r['divisi'];
    
    $ajsnobr=$r['nodivisi2'];
    
    $keterangan=$r['keterangan'];
    
    $jenis = $r['lampiran'];
    $pjnsrpt = $r['jenis_rpt'];
    
    if ($r['periodeby']=="S") $chkpilihsby="checked";
    
    $pilihperiodetipe=$r['periodeby'];
    if (empty($pilihperiodetipe)) $pilihperiodetipe="I";
    
}


$pjens1="selected";
$pjens2="";
$pjens3="";
$pjens4="";
$pjens5="";
$pjens6="";
$pjens7="";
$pjens8="";
$pjens9="";

if ($pjnsrpt=="A") $pjens1="selected";
if ($pjnsrpt=="K") $pjens2="selected";
if ($pjnsrpt=="B") $pjens3="selected";
if ($pjnsrpt=="D") $pjens4="selected";
if ($pjnsrpt=="S") $pjens5="selected";
if ($pjnsrpt=="J") $pjens6="selected";
if ($pjnsrpt=="V") $pjens7="selected";
if ($pjnsrpt=="C") $pjens8="selected";
if ($pjnsrpt=="W") $pjens9="selected";

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

if (!empty($pilihperiodetipe)) $ptupeper3="";

if ($pilihperiodetipe=="T") $ptupeper2="selected";
if ($pilihperiodetipe=="I") $ptupeper3="selected";
if ($pilihperiodetipe=="S") $ptupeper4="selected";

?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
        <form method='POST' action='' id='demo-form10' name='form10' data-parsley-validate target="_blank"></form>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                  
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                          
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowDataDivisiAwal()">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' ";
                                            if ($pidkaryawan=="0000000566") {
                                                $query .=" AND DivProdId IN ('EAGLE', 'PEACO', 'PIGEO', 'HO') ";
                                            }elseif ($pidkaryawan=="0000001043") {
                                                $query .=" AND DivProdId IN ('EAGLE', 'HO') ";
                                            }
                                            $query .=" order by DivProdId";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                if ($z['DivProdId']==$divisi)
                                                    echo "<option value='$z[DivProdId]' selected>$z[DivProdId]</option>";
                                                else
                                                    echo "<option value='$z[DivProdId]'>$z[DivProdId]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                            <div hidden id="jenis_kode">
                                
                                <div hidden class='form-group'>
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
                                
                                
                                
                                <div hidden class='form-group'>
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
                                
                            </div>
                                
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
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            
                                                <select class='form-control input-sm' id="cb_jenispilih" name="cb_jenispilih" onchange="ShowDataKodeJenis()" data-live-search="true">
                                                    <?PHP
                                                    if ($_SESSION['IDCARD']=="0000000148") {
                                                        echo "<option value='' selected></option>";
                                                    }
                                                    ?>
                                                    <option value="A" <?PHP echo $pjens1; ?>>Advance</option>
                                                    <option value="K" <?PHP echo $pjens2; ?>>Klaim</option>
                                                    <option value="B" <?PHP echo $pjens3; ?>>Belum Ada Kuitansi (CA)</option>
                                                    <option value="V" <?PHP echo $pjens7; ?>>Via Surabaya (BR)</option>
                                                    <?PHP
                                                    if ($_SESSION['IDCARD']=="0000001043") {
                                                        echo "<option value='D' $pjens4>Klaim Discount</option>";
                                                        echo "<option value='C' $pjens8>Via Surabaya (Klaim Discount)</option>";
                                                    }
                                                    ?>
                                                    <option value="S" <?PHP echo $pjens5; ?>>Kasbon Surabaya</option>
                                                    <!--<option value="W" <?PHP //echo $pjens9; ?>>Transfer Ulang</option>-->
                                                    <option value="J" <?PHP echo $pjens6; ?>>Adjustment</option>
                                                </select>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <div id='loading'></div>
                                <div id="div_datajenis1">
                                    <?PHP
                                    
                                    ?>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih No.BR/Divisi <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_ajsnobr' name='cb_ajsnobr'>
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                if ($_GET['act']=="editdata" AND $pjnsrpt=="J"){
                                                    $now=date("mdYhis");
                                                    $tmp01 =" dbtemp.DSETHZE01_".$pidkaryawan."_$now ";
                                                    $fdivisi_batal="";
                                                    if ($pidkaryawan=="0000001043") $fdivisi_batal=" AND divprodid ='EAGLE' ";
                                                    if ($pidkaryawan=="0000000566") $fdivisi_batal=" AND divprodid NOT IN ('EAGLE', 'HO') ";;

                                                    $query = "SELECT distinct brId from (
                                                        select brId from hrd.br0 where batal='Y'  and year(tgl)>'2018' $fdivisi_batal 
                                                        UNION
                                                        select DISTINCT brId from dbmaster.backup_br0 WHERE 1=1 $fdivisi_batal 
                                                        ) as xxx";
                                                    $query = "create TEMPORARY table $tmp01 ($query)"; 
                                                    mysqli_query($cnmy, $query);

                                                    $filbulansod_adj="";
                                                    $query = "select divisi, nodivisi, jumlah from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' "
                                                            . "and IFNULL(pilih,'') = 'Y' AND IFNULL(nodivisi,'')<>'' "
                                                            . " AND karyawanid='$pidkaryawan' "
                                                            . " AND idinput in (select distinct idinput from dbmaster.t_suratdana_br1 WHERE "
                                                            . " bridinput IN (select distinct brId from $tmp01) ) $filbulansod_adj "
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
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                                
                                
                                <div id="div_datajenis2">

                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nomor SPD <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_nomor' name='e_nomor' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnomor; ?>'>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Divisi / No. BR <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_nomordiv' name='e_nomordiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivnomor; ?>'>
                                        </div>
                                    </div>
                                    
                                    
                                    
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:red;">Adjustment <span class='required'></span></label>
                                            <div class='col-xs-5'>
                                                <select class='form-control input-sm' id='cb_ajsnobr2' name='cb_ajsnobr2'>
                                                    <option value='' selected>-- Pilihan --</option>
                                                    <?PHP
													/*
                                                    $query = "select divisi, nodivisi, SUM(jumlah) jumlah from dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' "
                                                            . " AND IFNULL(nodivisi,'')<>'' $filbulansod_adj "//AND
                                                            . " AND (userid='$_SESSION[IDCARD]' OR nodivisi='$ajsnobr') "//( DATE_FORMAT(tgl,'%Y-%m')='$ajsbulan' OR DATE_FORMAT(tglspd,'%Y-%m')='$ajsbulan' )
                                                            . "GROUP BY 1,2 ORDER BY 1,2";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pajsjmlbr=$z['jumlah'];
                                                        if (!empty($pajsjmlbr)) $pajsjmlbr=number_format($pajsjmlbr,0);
                                                        $pajsnobr=$z['nodivisi'];
                                                        $pajsdivisi=$z['divisi'];
                                                        if (empty($pajsdivisi)) $pajsdivisi= "ETHICAL";
                                                        $pajsketjml = "$pajsnobr";//$pajsdivisi -  &nbsp;&nbsp (Rp. $pajsjmlbr)
                                                        if (trim($pajsnobr)==trim($ajsnobr))
                                                            echo "<option value='$pajsnobr' selected>$pajsketjml</option>";
                                                        else
                                                            echo "<option value='$pajsnobr'>$pajsketjml</option>";
                                                    }
													*/
                                                    ?>
                                                </select>
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
                                                        <!--<option value="" <?PHP echo $ptupeper1; ?>>--All--</option>-->
                                                        <option value="T" <?PHP echo $ptupeper2; ?>>Transfer</option>
                                                        <option value="I" <?PHP echo $ptupeper3; ?>>Input</option>
                                                        <option value="S" <?PHP echo $ptupeper4; ?>>Rpt SBY</option>
                                                    </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;"><?PHP echo $nlabelperiode; ?> <span class='required'></span></label>
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

                                </div>
                                
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        
                                        <div id="div_datajenis_jml1">
                                            Jumlah
                                        </div>
                                        
                                        <div id="div_datajenis_jml2">
                                            <button type='button' class='btn btn-info btn-xs' onclick='TampilkanDataBRInput()'>Tampilkan Data</button> <span class='required'></span>
                                        </div>
                                        
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$jumlah"; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Adjustment <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_jmladj' name='e_jmladj' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pjmladj"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_jmltotal' name='e_jmltotal' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pjm_total"; ?>' Readonly>
                                    </div>
                                </div>
								
                                <div hidden id="div_ots">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PCM Rp. <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_otspcmrp' name='e_otspcmrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pots_rppcm"; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                            <button type='button' class='btn btn-dark btn-xs' onclick='RptOutstandingShow()'>Outstanding Rp.</button> <span class='required'></span>
                                        </label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_otsjmlrp' name='e_otsjmlrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pots_jml"; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sisa PCM Rp. <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_otssisarp' name='e_otssisarp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pots_sisarp"; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_keterangan' name='e_keterangan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $keterangan; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    
                    <div id='loading3'></div>
                    <div id="s_div">


                    </div>
                    
                    
                </div>
            </div>
            
            
            
            
        </form>
        
    </div>
    
    
</div>




<script type="text/javascript">
    function ShowDataDivisiAwal() {
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        if (ijeniskode=="J") { 
            document.getElementById('e_nomordiv').value="";
            ShowNoDivAdj();
        }else { 
            ShowNoBukti(); 
        }
        ShowDivJenisBukaTutup();
		ShowDivRpPCM('1');
    }
    
    function ShowDataKode() {
        ShowSubKode();
    }
    
    function ShowDataKodeJenis() {
        ShowDivJenisKode();
        ShowDivJenisLampiran();
        
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        
        if (ijeniskode=="J") { 
            document.getElementById('e_nomordiv').value="";
            ShowNoDivAdj();
        }else { 
            <?PHP if ($_GET['act']!="editdata") { ?>
                ShowNoBukti(); 
            <?PHP } ?>
        }
        ShowDivJenisBukaTutup();
		
		ShowDivRpPCM('1');
    }
    
    function ShowDivJenisBukaTutup(){
        var myurl = window.location;
        var urlku = new URL(myurl);
        var nact = urlku.searchParams.get("act");
        
        var ijeniskode = document.getElementById('cb_jenispilih').value;

        if (ijeniskode=="J"){
            div_datajenis1.style.display = 'block';
            div_datajenis2.style.display = 'none';
            s_div.style.display = 'none';
            
            div_datajenis_jml1.style.display = 'block';
            div_datajenis_jml2.style.display = 'none';
            
            document.getElementById("e_jmlusulan").disabled = false;
        }else{
            div_datajenis1.style.display = 'none';
            div_datajenis2.style.display = 'block';
            s_div.style.display = 'block';
            
            div_datajenis_jml1.style.display = 'none';
            div_datajenis_jml2.style.display = 'block';
            
            document.getElementById("e_jmlusulan").disabled = false;//true
            
            if (nact!="editdata") {
                
            }
        }
    }
    
    
    function ShowSubKode() {
        var ikode = document.getElementById('cb_kode').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewsubkode",
            data:"ukode="+ikode,
            success:function(data){
                $("#cb_kodesub").html(data);
            }
        });
    }
    
    function ShowDivJenisKode() {
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewdatajeniskode",
            data:"ujeniskode="+ijeniskode,
            success:function(data){
                $("#jenis_kode").html(data);
            }
        });
    }
    
    function ShowDivJenisLampiran() {
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewdatajenislampiran",
            data:"ujeniskode="+ijeniskode,
            success:function(data){
                $("#cb_jenis").html(data);
            }
        });
    }
    
    
    
    function ShowNoBukti() {
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        var iadvance = document.getElementById('cb_jenispilih').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewnomorbukti",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl+"&uadvance="+iadvance,
            success:function(data){
                document.getElementById('e_nomordiv').value=data;
            }
        });
    }
    
    function ShowNoDivAdj() {
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewdatanodivisiadjjenis",
            data:"ukode=kode",
            success:function(data){
                $("#loading").html("");
                $("#cb_ajsnobr").html(data);
            }
        });
    }
    
    function TampilkanDataBRInput() {
        var edivsi =document.getElementById('cb_divisi').value;
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        
        if (ijeniskode=="J") {
            
        }else{
            CariDataBRInput();
        }
        
    }
    
    function CariDataBRInput() {
        var eidinput =document.getElementById('e_id').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        
        var edivsi =document.getElementById('cb_divisi').value;
        var eadvance = document.getElementById('cb_jenispilih').value;
        var epertipe=document.getElementById('cb_pertipe').value;
        var eper1=document.getElementById('e_periode1').value;
        var eper2=document.getElementById('e_periode2').value;
        
        var ejenis=document.getElementById('cb_jenis').value;
        
        var estsrpt="";
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
        if (eadvance=="D" || eadvance=="C") {
            
            $.ajax({
                type:"post",
                url:"module/mod_br_spd/dataklaimdiskon.php?module=viewdatakd&ket=detail",
                data:"udivisi="+edivsi+"&uper1="+eper1+"&uper2="+eper2+"&eidinput="+eidinput+"&uact="+iact+
                        "&ujenis="+ejenis+"&upertipe="+epertipe+"&uadvance="+eadvance+"&sts_rpt="+estsrpt+"&utgl="+etgl1,
                success:function(data){
                    $("#s_div").html(data);
                    $("#loading3").html("");
                }
            });
            
        }else{
            
            
            $.ajax({
                type:"post",
                url:"module/mod_br_spd/databrinput.php?module=viewdatabrinput&ket=detail",
                data:"udivisi="+edivsi+"&uper1="+eper1+"&uper2="+eper2+"&eidinput="+eidinput+"&uact="+iact+
                        "&ujenis="+ejenis+"&upertipe="+epertipe+"&uadvance="+eadvance+"&sts_rpt="+estsrpt+"&utgl="+etgl1,
                success:function(data){
                    $("#s_div").html(data);
                    $("#loading3").html("");
                }
            });
        
        }
        
        
    }
    
    
    function disp_confirm(pText_,ket)  {
        
        var ijenispl =document.getElementById('cb_jenispilih').value;
        if (ijenispl=="D" || ijenispl=="C") {
            HitungTotalDariCekBoxKD();
        }else{
            HitungTotalDariCekBox();
        }
        
        setTimeout(function () {
            disp_confirm_ext(pText_,ket)
        }, 500);
        
    }
    
    function disp_confirm_ext(pText_,ket)  {

        var ijml =document.getElementById('e_jmlusulan').value;
        if(ijml==""){
            ijml="0";
        }
        if (ijml=="0") {
            alert("jumlah masih kosong...");
            return false;
        }
        
        /*
        var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        if (cmt == false) {
            return false;
        }
        */
        var edivsi =document.getElementById('cb_divisi').value;
        var ekode =document.getElementById('cb_kode').value;
        var ekodesub =document.getElementById('cb_kodesub').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        
        if (edivsi==""){
            alert("divisi masih kosong....");
            return 0;
        }

        if (ekode==""){
            alert("kode masih kosong....");
            return 0;
        }

        if (ekodesub==""){
            alert("sub kode masih kosong....");
            return 0;
        }
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_spd/aksi_spd.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
	
    function ShowDivRpPCM(sno) {
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        var idivisid = document.getElementById('cb_divisi').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        document.getElementById('e_otspcmrp').value=0;
        document.getElementById('e_otsjmlrp').value=0;
        document.getElementById('e_otssisarp').value=0;
        if (ijeniskode=="B") {
            div_ots.style.display = 'block';
            if (sno=="2") {
            }else{
                if (idivisid!="") {
                    
                    $.ajax({
                        type:"post",
                        url:"module/mod_br_spd/viewdata.php?module=viewdatapcmrp",
                        data:"udivisid="+idivisid+"&utgl="+itgl,
                        success:function(data){
                            var idata=data.split("|");
                            document.getElementById('e_otspcmrp').value=idata[0];
                            document.getElementById('e_otsjmlrp').value=idata[1];
                            document.getElementById('e_otssisarp').value=idata[2];
                        }
                    });
                    
                }
            }
            
        }else{
            div_ots.style.display = 'none';
        }
        
    }
    
    function RptOutstandingShow() {
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        var idivisid = document.getElementById('cb_divisi').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        
        var ndate = new Date(itgl);
        var ntahun = ndate.getFullYear();
        
        if (ijeniskode=="B") {
            document.getElementById("demo-form10").action = "eksekusi3.php?module=rekapotsbr&act=input&idmenu=273&ket=dariinputanspd&udivisi="+idivisid+"&utahun="+ntahun;
            document.getElementById("demo-form10").submit();
            return 1;
        }
    }
</script>



<style>
    .ui-datepicker-calendar2 {
        display: none;
    }
</style>
<script type="text/javascript">
    $(function() {
        $('#e_tglberlaku').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                //////ShowDataKode();
                ShowNoBukti();
                //CariDataPeriode();
				ShowDivRpPCM('1');
            } 
        });
        
        $('#e_periode1').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                //CariDataPeriode2();
            }
        });
        
        $('#e_periode2').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                //CariDataPeriode3();
            }
        });
        
    });
    
    function CariDataPeriode(){
        document.getElementById('e_jmlusulan').value="0";
        var itgl = document.getElementById('e_tglberlaku').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=cariperiode1",
            data:"utgl="+itgl+"&ukode="+ikode+"&ukodesub="+ikodesub,
            success:function(data){
                document.getElementById('e_periode1').value=data;
            }
        });
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=cariperiode2",
            data:"utgl="+itgl+"&ukode="+ikode+"&ukodesub="+ikodesub,
            success:function(data){
                document.getElementById('e_periode2').value=data;
            }
        });
        
    }
    
    function CariDataPeriode2(){
        document.getElementById('e_jmlusulan').value="0";
        var itgl = document.getElementById('e_periode1').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=cariperiode2",
            data:"utgl="+itgl+"&ukode="+ikode+"&ukodesub="+ikodesub,
            success:function(data){
                document.getElementById('e_periode2').value=data;
            }
        });
    }
    
    function CariDataPeriode3(){
        //document.getElementById('e_jmlusulan').value="0";
        var itglasal = document.getElementById('e_periode1').value;
        var itgl = document.getElementById('e_periode2').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=cariperiode3",
            data:"utgl="+itgl+"&ukode="+ikode+"&ukodesub="+ikodesub+"&uasal="+itglasal,
            success:function(data){
                document.getElementById('e_periode1').value=data;
            }
        });
    }
</script>

<script>
    $(document).ready(function() {
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        
        <?PHP if ($_GET['act']!="editdata") { ?>
                
                div_datajenis1.style.display = 'none';
                div_datajenis2.style.display = 'block';
                s_div.style.display = 'block';

                div_datajenis_jml1.style.display = 'none';
                div_datajenis_jml2.style.display = 'block';
                
                document.getElementById("e_jmlusulan").disabled = false;//true
                
                ShowNoBukti();
        <?PHP }elseif ($_GET['act']=="editdata") { ?>
            ShowDivJenisBukaTutup();
            if (ijeniskode!="J") {
                TampilkanDataBRInput();
            }
        <?PHP } ?>
    } );
</script>


            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <style>
                .custom-combobox {
                    position: relative;
                    display: inline-block;
                }
                .custom-combobox-toggle {
                    position: absolute;
                    top: 0;
                    bottom: 0;
                    margin-left: -1px;
                    padding: 0;
                }
                .custom-combobox-input {
                    margin: 0;
                    padding: 5px 10px;
                    width:300px;
                }
            </style>
            <script src="js/select_combo.js"></script>
            <script>
                $( function() {
                    $( "#cb_ajsnobr2" ).combobox();
                } );
            </script>