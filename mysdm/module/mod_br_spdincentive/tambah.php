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
    .ui-datepicker-calendar2 {
        display: none;
    }
</style>

<?PHP

$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d F Y', strtotime($hari_ini));
$eperiode1 = date('01 F Y', strtotime($hari_ini));
$eperiode2 = date('t F Y', strtotime($hari_ini));

$eperiodeinc = date('F Y', strtotime('-1 month', strtotime($hari_ini)));

$divisi="";
$keterangan="";
$jumlah="";
$pkode="1";
$psubkode="04";
$pnomor="";
$pdivnomor="";
$pketinc="";

$jmle="";
$jmlh="";
$jmlpea="";
$jmlp="";
$jmlo="";
$jmlc="";

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
    
    $eperiodeinc = date('F Y', strtotime($r['tglf']));

    $pkode=$r['kodeid'];
    $psubkode=$r['subkode'];
    $pnomor=$r['nomor'];
    $pdivnomor=$r['nodivisi'];
    $jumlah=$r['jumlah'];
    $divisi=$r['divisi'];
    
    $jenis = $r['lampiran'];
    $stspilihrpt = $r['sts'];
    $pjnsrpt = $r['jenis_rpt'];

    $pketinc = $r['keterangan'];
    
    if ($r['pilih']=="N") $chkpilih="checked";
    
    
    $pilihperiodetipe=$r['periodeby'];
    if (empty($pilihperiodetipe)) $pilihperiodetipe="I";
    

    $tampil = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_br_d WHERE idinput='$_GET[id]'");
    while ($t    = mysqli_fetch_array($tampil)){
        if ($t['divisi']=="EAGLE") $jmle = $t['jumlah'];
        if ($t['divisi']=="PEACO") $jmlpea = $t['jumlah'];
        if ($t['divisi']=="PIGEO") $jmlp = $t['jumlah'];
        if ($t['divisi']=="HO") $jmlh = $t['jumlah'];
        if ($t['divisi']=="OTC") $jmlo = $t['jumlah'];
        if ($t['divisi']=="CAN") $jmlc = $t['jumlah'];
    }
    
}

$preadonlyincfrom="";
if ($pketinc=="PM") $preadonlyincfrom="Readonly";
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_kode' name='cb_kode' onchange="" data-live-search="true">
                                              <?PHP
                                                $query = "select distinct kodeid, nama from dbmaster.t_kode_spd where kodeid='$pkode' order by kodeid";
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
                                          <select class='form-control input-sm' id='cb_kodesub' name='cb_kodesub' data-live-search="true" onchange="CariInputDiv()">
                                              <?PHP
                                                $query = "select distinct kodeid, subkode, subnama from dbmaster.t_kode_spd where kodeid='$pkode' and subkode='04' order by subkode";

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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Incentive From <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_incfrom' name='cb_incfrom' data-live-search="true" onchange="PilihFromInc()">
                                              <?PHP
                                              if ($pketinc=="PM") {
                                                echo "<option value='GSM'>GSM</option>";
                                                echo "<option value='PM'selected>PM</option>";
                                              }else{
                                                echo "<option value='GSM' selected>GSM</option>";
                                                echo "<option value='PM'>PM</option>";
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan Insentif</label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='cbln01'>
                                            <input type="text" class="form-control" id='e_periodeinc' name='e_periodeinc' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $eperiodeinc; ?>'>
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
                                    </div>
                                </div>
                                
                                
                                
                                <div id="c_input">
                                    <div class='form-group'>
                                        <div id='loading2'></div>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><span style='color:blue;'><b>Rincian Rp.</b></span></label>
                                        <div class='col-md-3'>
                                            &nbsp;
                                        </div>
                                    </div>
                                </div>
                                
                                
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
    <!--end row-->
</div>


<script type="text/javascript">
    $(function() {
        $('#e_tglberlaku').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                <?PHP if ($_GET['act']=="tambahbaru"){ ?>
                        ShowNoBukti();
                <?PHP } ?>
            } 
        });    
    });
    
    
    function ShowNoBukti() {
        
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_spdincentive/viewdata.php?module=viewnomorbukti",
            data:"ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl,
            success:function(data){
                document.getElementById('e_nomordiv').value=data;
            }
        });
    }
    
    function hit_total() {
        
        var nc = document.getElementById('e_jmlc').value;  
        var ne = document.getElementById('e_jmle').value;  
        var npea = document.getElementById('e_jmlpea').value;
        var np = document.getElementById('e_jmlp').value;
        
        if (nc=="") nc="0";
        if (ne=="") ne="0";
        if (npea=="") npea="0";
        if (np=="") np="0";
        
        var newchar = '';
        
        var myc = nc;  
        myc = myc.split(',').join(newchar);
        
        var mye = ne;  
        mye = mye.split(',').join(newchar);
        
        var mypea = npea;  
        mypea = mypea.split(',').join(newchar);
        
        var myp = np;  
        myp = myp.split(',').join(newchar);
        
        total_ = parseInt(myc) + parseInt(mye) + parseInt(mypea) + parseInt(myp);
        document.getElementById('e_jmlusulan').value = total_;
        
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
        
        var ekode =document.getElementById('cb_kode').value;
        var ekodesub =document.getElementById('cb_kodesub').value;
        var etgl1=document.getElementById('e_tglberlaku').value;

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
                document.getElementById("demo-form2").action = "module/mod_br_spdincentive/aksi_spdincentive.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    $(document).ready(function() {
        <?PHP if ($_GET['act']=="tambahbaru"){ ?>
                ShowNoBukti();
        <?PHP } ?>
    } );

    function PilihFromInc() {
        var eincfrm =document.getElementById('cb_incfrom').value;
        if (eincfrm=="PM") {
            document.getElementById("e_jmlc").value = "0";
            document.getElementById("e_jmlc").readOnly = true;
        }else{
            document.getElementById("e_jmlc").readOnly = false;
        }
    }
</script>