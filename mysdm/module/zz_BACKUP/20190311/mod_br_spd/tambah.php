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

<script type="text/javascript">
    function ShowSubKode() {
        var ikode = document.getElementById('cb_kode').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewsubkode",
            data:"ukode="+ikode,
            success:function(data){
                $("#cb_kodesub").html(data);
                ShowDataKode();
            }
        });
    }

    function ShowNoSPD() {
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewnomorspd",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl,
            success:function(data){
                document.getElementById('e_nomor').value=data;
            }
        });
    }

    function ShowNoBukti() {
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewnomorbukti",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl,
            success:function(data){
                document.getElementById('e_nomordiv').value=data;
            }
        });
    }

    function ShowDataKode() {
        ShowNoBukti();
        ShowNoSPD();
    }
    
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
                    HitungTotalJumlahData();
                }
            } 
        });
    });
    
    function HitungTotalJumlahData() {
        var eidinput =document.getElementById('e_id').value;
        var edivsi =document.getElementById('cb_divisi').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        var ejenis=document.getElementById('cb_jenis').value;
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=hitungtotaldata",
            data:"udivisi="+edivsi+"&utgl="+etgl1+"&ujenis="+ejenis+"&uact="+iact+"&eidinput="+eidinput,
            success:function(data){
                $("#loading2").html("");
                document.getElementById('e_jmlusulan').value=data;
            }
        });
        
    }
    
    function disp_confirm(pText_,ket)  {

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
</script>
    
    
<?PHP

$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d F Y', strtotime($hari_ini));
                
$keterangan="";
$divisi="";
if ($_SESSION['DIVISI']=="OTC") $divisi=$_SESSION['DIVISI'];
$jumlah="";
$pkode="";
$psubkode="";
$pnomor="";
$pdivnomor="";
$jenis="";
$chkpilih="";
$act="input";

if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_br WHERE idinput='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idbr=$r['idinput'];
    $tglberlku = date('d/m/Y', strtotime($r['tgl']));
    $tgl1 = date('d F Y', strtotime($r['tgl']));
    
    $pkode=$r['kodeid'];
    $psubkode=$r['subkode'];
    $pnomor=$r['nomor'];
    $pdivnomor=$r['nodivisi'];
    $jumlah=$r['jumlah'];
    $divisi=$r['divisi'];
    
    $jenis = $r['lampiran'];
    
    if ($r['pilih']=="N") $chkpilih="checked";
    
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


$tutupnospd="";
$tutuplampiran="";
if ($_SESSION['GROUP']!=1){
    $tutuplampiran="hidden";
    if ($_SESSION['DIVISI']=="OTC") {
        $tutupnospd="hidden";
        $tutuplampiran="";
    }
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
                                    </div>
                                </div>

                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowDataKode();">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' ";
                                            if ($_SESSION['DIVISI']=="OTC") {
                                                $query .=" AND DivProdId = 'OTC' ";
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
                                          <select class='form-control input-sm' id='cb_kodesub' name='cb_kodesub' data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                              if ($_GET['act']=="editdata"){
                                                $query = "select distinct kodeid, subkode, subnama from dbmaster.t_kode_spd where kodeid='$pkode' order by subkode";

                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    if ($z['subkode']==$psubkode)
                                                        echo "<option value='$z[subkode]' selected>$z[subkode] - $z[subnama]</option>";
                                                    else
                                                        echo "<option value='$z[subkode]'>$z[subkode] - $z[subnama]</option>";
                                                }
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
                                
                                <div <?PHP echo $tutupnospd; ?> class='form-group'>
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
                                
                                
                                <div <?PHP echo $tutuplampiran; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Lampiran <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            <div class='input-group date' id=''>
                                                <select class='form-control input-sm' id="cb_jenis" name="cb_jenis" onchange="HitungTotalJumlahData()" data-live-search="true">
                                                    <option value="" <?PHP echo $plmp1; ?>>--All--</option>
                                                    <option value="Y" <?PHP echo $plmp2; ?>>Ya</option>
                                                    <option value="N" <?PHP echo $plmp3; ?>>Tidak</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <?PHP if (empty($tutuplampiran)) { ?>
                                            <button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalJumlahData()'>Hitung Jumlah</button> <span class='required'></span>
                                        <?PHP }else{ ?>
                                            Jumlah
                                        <?PHP } ?>
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>' >
                                    </div>
                                </div>
                                
                                <div <?PHP echo $tutuplampiran; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='checkbox' id='e_chkpilih' name='e_chkpilih' value='N' <?PHP echo $chkpilih; ?>>
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
            
        
        
        </form>
        
    </div>
    <!--end row-->
</div>