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
$divisi="HO";

$pkode="1";
$psubkode="01";
$pnomor="";
$pdivnomor="";  
$jumlah="";

$keterangan="";

$nreadonjml="";

$nlabelperiode="";
$pjnsrpt="A";
$jenis="Y";
$pilihperiodetipe="I";

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

if ($pjnsrpt=="A") $pjens1="selected";
if ($pjnsrpt=="K") $pjens2="selected";
if ($pjnsrpt=="B") $pjens3="selected";
if ($pjnsrpt=="D") $pjens4="selected";
if ($pjnsrpt=="S") $pjens5="selected";
if ($pjnsrpt=="J") $pjens6="selected";

$plmp1="";
$plmp2="selected";
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

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
        
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
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' ";
                                            $query .=" AND DivProdId IN ('HO') ";
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
                                
                            <div id="jenis_kode">
                                
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
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Jenis <span class='required'></span></label>
                                        <div class='col-md-3'>
                                            <div class="form-group">

                                                    <select class='form-control input-sm' id="cb_jenispilih" name="cb_jenispilih" onchange="" data-live-search="true">
                                                        <option value="A" <?PHP echo $pjens1; ?>>Advance</option>
                                                        <option value="K" <?PHP echo $pjens2; ?>>Klaim</option>
                                                        <option value="B" <?PHP echo $pjens3; ?>>Belum Ada Kuitansi (CA)</option>
                                                        <option value="S" <?PHP echo $pjens5; ?>>Kasbon Surabaya</option>
                                                        <option value="J" <?PHP echo $pjens6; ?>>Adjusment</option>
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
            
            
            
            
            <div id='loading3'></div>
            <div id="s_div">
                
                
            </div>
            
            
            
            
        </form>
        
    </div>
    
    
</div>




<script type="text/javascript">

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
            } 
        });
        
        $('#e_periode1').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                CariDataPeriode2();
            }
        });
        
        $('#e_periode2').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                CariDataPeriode3();
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
        document.getElementById('e_jmlusulan').value="0";
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