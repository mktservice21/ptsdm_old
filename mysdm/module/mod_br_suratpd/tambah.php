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

$divisi=$_SESSION['DIVISI'];
$keterangan="";
$jumlah="";
$pkode="";
$psubkode="";
$pnomor="";
$pdivnomor="";

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
    $divisi=$r['divisi'];
    
    if ($pkode=="3") $divisi="HO";
    
    $keterangan=$r['keterangan'];
    
    $jenis = $r['lampiran'];
    $stspilihrpt = $r['sts'];
    $pjnsrpt = $r['jenis_rpt'];
    
    if ($r['pilih']=="N") $chkpilih="checked";
    
    
    $pilihperiodetipe=$r['periodeby'];
    if (empty($pilihperiodetipe)) $pilihperiodetipe="I";
    
}


?>

<div class='modal fade' id='myModal' role='dialog'></div>

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
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowDataKode();">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE DivProdId='HO' ";
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
                                          <select class='form-control input-sm' id='cb_kode' name='cb_kode' onchange="ShowDataKode();" data-live-search="true">
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
                                          <select class='form-control input-sm' id='cb_kodesub' name='cb_kodesub' data-live-search="true" onchange="CariInputDiv()">
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
                                

                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nomor SPD <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_nomor' name='e_nomor' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnomor; ?>'>
                                    </div>
                                </div>
                                
                                <div id="c_input">
                                    
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
                                        <div id='loading2'></div>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                            Jumlah
                                        </label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>' >
                                        </div>
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
                //ShowNoSPD();
                ShowNoBukti();
                document.getElementById('e_periode1').value=document.getElementById('e_tglberlaku').value;
                document.getElementById('e_periode2').value=document.getElementById('e_tglberlaku').value;
            } 
        });    
    });
    
    function ShowDataKode() {
        $("#s_div").html("");
        ShowSubKode();
        //ShowNoSPD();
    }
    
    function ShowSubKode() {
        var ikode = document.getElementById('cb_kode').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewsubkode",
            data:"ukode="+ikode,
            success:function(data){
                $("#cb_kodesub").html(data);
                CariInputDiv();
                if (ikode=="3") {
                }else{
                    ShowNoBukti();
                }
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
            url:"module/mod_br_suratpd/viewdata.php?module=viewnomorspd",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl,
            success:function(data){
                document.getElementById('e_nomor').value=data;
            }
        });
    }
    
    function ShowDataAjsNoSPD() {
        var iajsspd = document.getElementById('cb_ajsnospd').value;
        var chkinp=document.getElementById('chk_pilihbln');
        if (chkinp.checked == true){
            var itgl = document.getElementById('e_tglberlaku').value;
        }else{
            var itgl = "";
        }
        
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/viewdata.php?module=viewajsnomorspd",
            data:"uajsspd="+iajsspd+"&utgl="+itgl,
            success:function(data){
                $("#cb_ajsnospd").html(data);
            }
        });
    }
    
    function ShowDataNoDivisiBR() {
        var iajsspd = document.getElementById('cb_ajsnospd').value;
        var chkinp=document.getElementById('chk_pilihbln');
        if (chkinp.checked == true){
            var itgl = document.getElementById('e_tglberlaku').value;
        }else{
            var itgl = "";
        }
        
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/viewdata.php?module=viewajsnomorbrdivisi",
            data:"uajsspd="+iajsspd+"&utgl="+itgl,
            success:function(data){
                $("#cb_ajsnobr").html(data);
            }
        });
    }
    
    
    function ShowNoBukti() {
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        var iadvance = "";
        
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/viewdata.php?module=viewnomorbukti",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl+"&uadvance="+iadvance,
            success:function(data){
                document.getElementById('e_nomordiv').value=data;
            }
        });
    }
    
    function CariInputDiv() {
        var iid = document.getElementById('e_id').value;
        var ikode = document.getElementById('cb_kode').value;
        var isubkode = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/viewdata.php?module=cariinputdiv",
            data:"ukode="+ikode+"&usubkode="+isubkode+"&uid="+iid+"&utgl="+itgl,
            success:function(data){
                $("#c_input").html(data);
            }
        });
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        if (iact!="editdata") {
            if (ikode=="3") {}else{
                ShowNoBukti();
            }
        }
    }
    
    
    
    
    function HitungData()  {
        CariDataBR();
    }
    
    
    function CariDataBR() {
        var eidinput =document.getElementById('e_id').value;
        
        var eper1=document.getElementById('e_periode1').value;
        var eper2=document.getElementById('e_periode2').value;
        var estsrpt="";
        
        var edivsi =document.getElementById('cb_divisi').value;
        var etgl1=document.getElementById('e_tglberlaku').value;
        
        var ejenis=document.getElementById('cb_jenis').value;
        var epertipe=document.getElementById('cb_pertipe').value;
        var eadvance=document.getElementById('cb_jenispilih').value;
        
        var ekode =document.getElementById('cb_kode').value;
        if (ekode=="7") {
            var enodivadj=document.getElementById('cb_ajsnobr').value;
            if (enodivadj=="") {
                alert("No BR/Divisi harus diisi...!!!"); return false;
            }
        }else{
            var enodivadj="";
        }
        
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");

        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_suratpd/databr.php?module=viewdataanne&ket=detail",
            data:"udivisi="+edivsi+"&utgl="+etgl1+"&uper1="+eper1+"&uper2="+eper2+"&eidinput="+eidinput+"&uact="+iact+"&sts_rpt="+estsrpt+
                    "&ujenis="+ejenis+"&upertipe="+epertipe+"&uadvance="+eadvance+"&ukodeid="+ekode+"&unodivadj="+enodivadj,
            success:function(data){
                $("#s_div").html(data);
                $("#loading3").html("");
                if (eidinput=="") document.getElementById('e_jmlusulan').value="0";
                if (eidinput!="") {
                    HitungTotalDariCekBox();
                }
            }
        });
    }
    
    
    //HitungTotalDariCekBox
    
    function disp_confirm(pText_,ket)  {
        
        HitungTotalDariCekBox();
        
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
        
        if (ekode=="3") {
            var enobradj=document.getElementById('cb_ajsnobr').value;
            var enobradj2=document.getElementById('e_ajsnobr2').value;
            
            if (enobradj=="" && enobradj2=="") {
                alert("no divisi / br masih kosong...\n\
                hapus terlebih dahulu no divisi / br kemudian isi kembali!!!");
                return 0;
            }
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
                document.getElementById("demo-form2").action = "module/mod_br_suratpd/aksi_simpanspd.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
        var idivisid = "HO";
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
        var idivisid = "HO";
        var itgl = document.getElementById('e_tglberlaku').value;
        
        var ndate = new Date(itgl);
        var ntahun = ndate.getFullYear();
        
        if (ijeniskode=="B") {
            document.getElementById("demo-form10").action = "eksekusi3.php?module=rekapotsbr&act=input&idmenu=273&ket=dariinputanspd&udivisi="+idivisid+"&utahun="+ntahun;
            document.getElementById("demo-form10").submit();
            return 1;
        }
    }
	
	
    $(document).ready(function() {
        <?PHP if ($_GET['act']=="editdata"){ ?>
                CariInputDiv();
        <?PHP } ?>
    } );
</script>