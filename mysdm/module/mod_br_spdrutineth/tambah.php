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
         
$pkaryawanid=$_SESSION['IDCARD'];

$divisi="";
$pnomor="";
$pdivnomor="";
$pkode="";
if ($pkaryawanid=="0000000143") $pkode="1";
if ($pkaryawanid=="0000000329") $pkode="2";

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
    
    
}
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_kode' name='cb_kode' onchange="ShowSubKode();" data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $nfilter_kode=" AND kodeid IN ('1', '2')";
                                                if ($pkaryawanid=="0000000143") {
                                                    $nfilter_kode=" AND kodeid IN ('1', '2')";
                                                    if (empty($pkode)) $pkode=1;
                                                }elseif ($pkaryawanid=="0000000329") {
                                                    $nfilter_kode=" AND kodeid IN ('2')";
                                                    if (empty($pkode)) $pkode=2;
                                                }
                                                if (empty($pkode)) $pkode=1;
                                                
                                                $query = "select distinct kodeid, nama from dbmaster.t_kode_spd WHERE 1=1 $nfilter_kode order by kodeid";
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
                                                $query = "select distinct kodeid, subkode, subnama from dbmaster.t_kode_spd where kodeid='$pkode' AND CONCAT(kodeid,subkode) IN ('103', '105', '221') order by subkode";

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
                                <?PHP
                                    $nperiode_nm="Periode Rutin/LK";
                                    if ($pkaryawanid=="0000000143") $nperiode_nm="Periode Rutin";
                                    if ($pkaryawanid=="0000000329") $nperiode_nm="Periode LK";
                                ?>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><?PHP echo $nperiode_nm; ?></label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='cbln01'>
                                            <input type="text" class="form-control" id='e_periode' name='e_periode' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $eperiode1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <button type='button' class='btn btn-info btn-xs' onclick='CariDataTransaksi()'>Tampilkan Data</button> <span class='required'></span>
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>' Readonly>
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
            //HitungTotalJumlahData();
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
            } 
        });
        
    });
    
    
    
    function ShowKode() {
        var ipilihca = document.getElementById('e_chkpilih').checked;
        var nca="";
        if (ipilihca==true) nca="ca";
        $.ajax({
            type:"post",
            url:"module/mod_br_spdrutineth/viewdata.php?module=viewkode",
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
            url:"module/mod_br_spdrutineth/viewdata.php?module=viewsubkode",
            data:"ukode="+ikode,
            success:function(data){
                $("#cb_kodesub").html(data);
                ShowDataKode();
            }
        });
    }
    
    function ShowDataKode() {
        $("#s_div").html("");
        ShowNoBukti();
    }
    
    function ShowNoBukti() { 
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        var iadvance = "";
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spdrutineth/viewdata.php?module=viewnomorbukti",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl+"&uadvance="+iadvance,
            success:function(data){
                document.getElementById('e_nomordiv').value=data;
            }
        });
    }
    
    function CariDataTransaksi() {
        var eidinput =document.getElementById('e_id').value;
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_periode').value;
        var iadvance = "";
        var ikodeperiode = "";
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        //alert(ikodesub); return false;
        if (ikodesub=="05") {
            $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
            $.ajax({
                type:"post",
                url:"module/mod_br_spdrutineth/databrinputrutinlk.php?module=viewdatabrinput",
                data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl+"&uadvance="+iadvance+"&ukodeperiode="+ikodeperiode+
                    "&uact="+iact+"&eidinput="+eidinput,
                success:function(data){
                    $("#s_div").html(data);
                    $("#loading3").html("");
                }
            });
        }
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
                document.getElementById("demo-form2").action = "module/mod_br_spdrutineth/aksi_spdrutineth.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
</script>

<script>
    $(document).ready(function() {
        <?PHP if ($_GET['act']=="editdata") { ?>
            CariDataTransaksi();
        <?PHP } ?>
    } );
</script>