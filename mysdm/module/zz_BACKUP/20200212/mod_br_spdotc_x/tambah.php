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

<?php

$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d F Y', strtotime($hari_ini));
$eperiode1 = date('01 F Y', strtotime($hari_ini));
$eperiode2 = date('t F Y', strtotime($hari_ini));
                
$divisi="OTC";
$pnomor="";
$pdivnomor="";
$pkode="";
$psubkode="";
$jenis="Y";
$pilihperiodetipe="I";
$jumlah=0;
$chkpilih="";
$chkpilihsby="";


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
    
    $jenis = $r['lampiran'];
    $stspilihrpt = $r['sts'];
    $pjnsrpt = $r['jenis_rpt'];
    
    if ($r['pilih']=="N") $chkpilih="checked";
    if ($r['periodeby']=="S") $chkpilihsby="checked";
    
    
    $pilihperiodetipe=$r['periodeby'];
    if (empty($pilihperiodetipe)) $pilihperiodetipe="I";
    
    
}



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
$ptupeper3="";
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
                                        <input type='hidden' id='cb_divisi' name='cb_divisi' class='form-control col-md-7 col-xs-12' value='<?PHP echo $divisi; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='checkbox' id='e_chkpilih' name='e_chkpilih' value='N' <?PHP echo $chkpilih; ?> onclick="ShowKode()"> <b>Cash Advance</b>
                                    </div>
                                </div>
                                
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
                                          <select class='form-control input-sm' id='cb_kodesub' name='cb_kodesub' data-live-search="true" onchange="ShowDataKode();">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $query = "select distinct kodeid, subkode, subnama from dbmaster.t_kode_spd where kodeid='$pkode' order by subkode";

                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    if ($z['subkode']==$psubkode)
                                                        echo "<option value='$z[subkode]' selected>$z[subkode] - $z[subnama]</option>";
                                                    else
                                                        echo "<option value='$z[subkode]'>$z[subkode] - $z[subnama]</option>";
                                                }
                                              ?>
                                          </select>
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Divisi / No. BR <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_nomordiv' name='e_nomordiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivnomor; ?>'>
                                        <input type='hidden' id='e_nomor' name='e_nomor' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnomor; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Lampiran <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            
                                                <select class='form-control input-sm' id="cb_jenis" name="cb_jenis" onchange="ShowDataKode()" data-live-search="true">
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
                                            
                                                <select class='form-control input-sm' id="cb_pertipe" name="cb_pertipe" onchange="ShowDataKode()" data-live-search="true">
                                                    <!--<option value="" <?PHP echo $ptupeper1; ?>>--All--</option>-->
                                                    <option value="T" <?PHP echo $ptupeper2; ?>>Transfer</option>
                                                    <option value="I" <?PHP echo $ptupeper3; ?>>Input</option>
                                                    <option value="S" <?PHP echo $ptupeper4; ?>>Rpt SBY</option>
                                                </select>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">&nbsp; <span class='required'></span></label>
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
                                        <button type='button' class='btn btn-info btn-xs' onclick='HitungTotalJumlahData()'>Hitung Jumlah</button> <span class='required'></span>
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>' >
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <b>Tgl. Surabaya </b><input type='checkbox' id='chk_tglsby' name='chk_tglsby' onclick="nolkanangka()" value='N' <?PHP echo $chkpilihsby; ?>>
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
    
    
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var iact = urlku.searchParams.get("act");
        if (iact=="editdata") {
            HitungTotalJumlahData();
        }
    } );
    
    $(function() {
        $('#e_tglberlaku').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                ShowDataKode();
                var edivsi =document.getElementById('cb_divisi').value;
                if (edivsi=="OTC") {
                    //HitungTotalJumlahData();
                }
                CariDataPeriode();
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
    
    
    
    function ShowKode() {
        var ipilihca = document.getElementById('e_chkpilih').checked;
        var nca="";
        if (ipilihca==true) nca="ca";
        $.ajax({
            type:"post",
            url:"module/mod_br_spdotc/viewdata.php?module=viewkode",
            data:"upilihca="+nca,
            success:function(data){
                $("#cb_kode").html(data);
                ShowSubKode();
            }
        });
    }
    
    function ShowSubKode() {
        var ikode = document.getElementById('cb_kode').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spdotc/viewdata.php?module=viewsubkode",
            data:"ukode="+ikode,
            success:function(data){
                $("#cb_kodesub").html(data);
                ShowDataKode();
            }
        });
    }
    
    function ShowDataKode() {
        var iperiodeby = document.getElementById('cb_pertipe').value;
        if (iperiodeby=="S"){
            document.getElementById('chk_tglsby').checked=true;
        }else{
            document.getElementById('chk_tglsby').checked=false;
        }
        $("#s_div").html("");
        ShowNoBukti();
        CariDataPeriode();
    }
    
    function ShowNoBukti() { 
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        var iadvance = "";
        $.ajax({
            type:"post",
            url:"module/mod_br_spdotc/viewdata.php?module=viewnomorbukti",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl+"&uadvance="+iadvance,
            success:function(data){
                document.getElementById('e_nomordiv').value=data;
            }
        });
    }
    
    
    function CariDataPeriode(){
        document.getElementById('e_jmlusulan').value="0";
        var itgl = document.getElementById('e_tglberlaku').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spdotc/viewdata.php?module=cariperiode1",
            data:"utgl="+itgl+"&ukode="+ikode+"&ukodesub="+ikodesub,
            success:function(data){
                document.getElementById('e_periode1').value=data;
            }
        });
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spdotc/viewdata.php?module=cariperiode2",
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
            url:"module/mod_br_spdotc/viewdata.php?module=cariperiode2",
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
            url:"module/mod_br_spdotc/viewdata.php?module=cariperiode3",
            data:"utgl="+itgl+"&ukode="+ikode+"&ukodesub="+ikodesub+"&uasal="+itglasal,
            success:function(data){
                document.getElementById('e_periode1').value=data;
            }
        });
    }
    
    
    function HitungTotalJumlahData() {
        CariDataOTC();
    }
    
    
    function CariDataOTC() {
        
        var eidinput =document.getElementById('e_id').value;
        
        var eper1=document.getElementById('e_periode1').value;
        var eper2=document.getElementById('e_periode2').value;
        var estsrpt="";
        
        var edivsi =document.getElementById('cb_divisi').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        
        var ejenis=document.getElementById('cb_jenis').value;
        var epertipe=document.getElementById('cb_pertipe').value;
        var eadvance="";
        
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");

        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spdotc/dataotc.php?module=viewdataotc&ket=detail",
            data:"udivisi="+edivsi+"&utgl="+etgl1+"&uper1="+eper1+"&uper2="+eper2+"&eidinput="+eidinput+"&uact="+iact+"&sts_rpt="+estsrpt+
                    "&ujenis="+ejenis+"&upertipe="+epertipe+"&uadvance="+eadvance,
            success:function(data){
                $("#s_div").html(data);
                $("#loading3").html("");
                HitungTotalDariCekBox();
            }
        });
    }
    
    function disp_confirm(pText_,ket)  {
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
            //alert("divisi masih kosong....");
            //return 0;
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
                document.getElementById("demo-form2").action = "module/mod_br_spdotc/aksi_spdotc.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
</script>