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

<script>    
    function showCabangMR(ucar, ecabang) {
        var icar = document.getElementById(ucar).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=viewdatacabangkaryawan",
            data:"umr="+icar,
            success:function(data){
                $("#"+ecabang).html(data);
                ShowDivisi(ucar, 'cb_divisi');
                ShowCOA(ucar, 'cb_divisi', 'cb_coa');
            }
        });
    }

function ShowDivisi(ucar, udiv) {
    var icar = document.getElementById(ucar).value;
    $.ajax({
        type:"post",
        url:"module/mod_br_spd/viewdata.php?module=viewdivisimr",
        data:"umr="+icar,
        success:function(data){
            $("#"+udiv).html(data);
        }
    });
}

function ShowCOA(ucar, udiv, ucoa) {
    var icar = document.getElementById(ucar).value;
    var idiv = document.getElementById(udiv).value;
    $.ajax({
        type:"post",
        url:"module/mod_br_spd/viewdata.php?module=viewcoadivisi",
        data:"umr="+icar+"&udivi="+idiv,
        success:function(data){
            $("#"+ucoa).html(data);
        }
    });
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

</script>

<?PHP
$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));
                
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$idcabang="";
$nmcabang="";
$keterangan="";
$divisi=$_SESSION['DIVISI'];
$jumlah="";
$coa="";
$ca="";
$pkode="";
$psubkode="";
$pnomor="";
$pdivnomor="";
$tahap="2";
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_br WHERE idinput='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idbr=$r['idinput'];
    $tglberlku = date('d/m/Y', strtotime($r['tgl']));
    $tgl1 = date('d/m/Y', strtotime($r['tgl']));
    $idajukan=$_SESSION['IDCARD']; 
    $nmajukan=$_SESSION['NAMALENGKAP']; 
    $pkode=$r['kodeid'];
    $psubkode=$r['subkode'];
    $pnomor=$r['nomor'];
    $pdivnomor=$r['nodivisi'];
    $jumlah=$r['jumlah'];
    $divisi=$r['divisi'];
    
    $editt = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_br2 WHERE idinput='$_GET[id]'");
    $rt    = mysqli_fetch_array($editt);
    
    $tglberlkurtf = $rt['tglf'];
    $tglberlkurtt = $rt['tglt'];
    if (!empty($tglberlkurtf))
        $tgl_pertama = date('d F Y', strtotime($tglberlkurtf));
    
    if (!empty($tglberlkurtt))
        $tgl_terakhir = date('d F Y', strtotime($tglberlkurtt));
    
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

                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_idkaryawan' name='e_idkaryawan' onchange="showCabangMR('e_idkaryawan', 'e_idcabang')">
                                            <?PHP
                                            //comboKaryawanAll("", "pilihan", $idajukan, $_SESSION['STSADMIN'], $_SESSION['LVLPOSISI'], $_SESSION['DIVISI']);
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowCOA('e_idkaryawan', 'cb_divisi', 'cb_coa');">
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
                                

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_coa'>COA / Posting <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_coa' name='cb_coa' data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                if ($_GET['act']=="editdata"){
                                                    $fil = "AND DIVISI = '$divisi'";
                                                    if (empty($divisi)) $fil="";
                                                    $filcoa ="";
                                                    if ($_SESSION['GROUP']<>"26" AND $_SESSION['GROUP'] <> "23") {
                                                        //if ($_SESSION['ADMINKHUSUS']=="Y") $filcoa =" and COA4 in (select distinct COA4 from dbmaster.coa_wewenang where karyawanId='$_SESSION[IDCARD]')";
                                                    }
                                                    $query = "select COA4, NAMA4 from dbmaster.v_coa_all WHERE ( ifnull(kodeid,'') = '' AND "
                                                            . "COA4 not in (select distinct COA4 from dbmaster.posting_coa))"
                                                            . " $fil $filcoa";
                                                    
                                                    $query = "select COA4, NAMA4 from dbmaster.v_coa_all WHERE COA4 in (select distinct ifnull(COA4,'') from dbmaster.posting_coa_rutin) $fil";
                                                    
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        if ($z['COA4']==$coa)
                                                            echo "<option value='$z[COA4]' selected>$z[NAMA4]</option>";
                                                        else
                                                            echo "<option value='$z[COA4]'>$z[NAMA4]</option>";
                                                    }
                                                }
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nomor SPD <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_nomor' name='e_nomor' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnomor; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal Pengajuan Dana</label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
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
                                
                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>'>
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
                                -->
                                
                            </div>
                            
                            
                      
                            
                        </div>
                    </div>
                    

                </div>
            </div>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode BR <span class='required'></span></label>
                    <div class='col-md-3'>
                        <div class="form-group">
                            <div class='input-group date' id=''>
                                <input type='text' id='tglfrom' name='bulan1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>

                            <div class='input-group date' id=''>
                                <input type='text' id='tglto' name='bulan2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_terakhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
                    
                    <button type='button' class='btn btn-info btn-xs' onclick='KlikDataTabel()'>Tampilkan Data Yang Telah Diinput Sesuai Periode BR</button>
                    <div id='loading'></div>
                    <div id='c-data'>

                    </div>
                </div>
            </div>
            
            
            
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <button type='button' class='btn btn-danger btn-xs' onclick='HitungTotalJumlah()'>Hitung Jumlah</button> <span class='required'></span>
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $jumlah; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                            <!--<input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>-->
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


<script>
    var TotalPilih = 0;
    
    $(document).ready(function() {
        //showCabangMR('e_idkaryawan', 'e_idcabang');
        //ShowDivisi('e_idkaryawan', 'cb_divisi');
        <?PHP if ($_GET['act']=="editdata"){ ?>
            KlikDataTabel();
        <?PHP } ?>
    });
    
    function KlikDataTabel() {
        var ket="";
        var etgl1=document.getElementById('e_tglberlaku').value;
        var etglf=document.getElementById('tglfrom').value;
        var etglt=document.getElementById('tglto').value;
        var edivisi=document.getElementById('cb_divisi').value;
        var eid=document.getElementById('e_id').value;
        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;
        
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdatainput.php?module="+ket,
            data:"eket="+ket+"&uperiode1="+etgl1+"&udivisi="+edivisi+"&uidc="+eidc+"&ucabang="+"&utglf="+etglf+"&utglt="+etglt+"&uidinput="+eid,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
                //document.getElementById('e_jmlusulan').value=0;
            }
        });
    }
    
    function CariKataID(echecx) {
        var chk_arr =  document.getElementsByName(echecx);
        var chklength = chk_arr.length;             
        var allnobr="";
        
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                var kata = chk_arr[k].value;
                var fields = kata.split('-');
                allnobr =allnobr + "'"+fields[0]+"',";
                TotalPilih++;
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }
        
        return allnobr;
    }
    function HitungTotalJumlah() {
        TotalPilih=0;
        var nobridA=CariKataID("chkbox_idA[]");
        var nobridB=CariKataID("chkbox_idB[]");
        var nobridC=CariKataID("chkbox_idC[]");
        var nobridD=CariKataID("chkbox_idD[]");
        var nobridE=CariKataID("chkbox_idE[]");
        var nobridF=CariKataID("chkbox_idF[]");
        var nobridG=CariKataID("chkbox_idG[]");
        var nobridH=CariKataID("chkbox_idH[]");
        var nobridI=CariKataID("chkbox_idI[]");
        var nobridJ=CariKataID("chkbox_idJ[]");
        var nobridK=CariKataID("chkbox_idK[]");
        /*
        if (TotalPilih>30) {
            alert("Batas maksimal 30 pilihan... Yang terpilih "+TotalPilih);
        }
        */
        $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_br_spd/viewdata.php?module=hitungtotal",
            data:"ufila="+nobridA+"&ufilb="+nobridB+"&ufilc="+nobridC+
                    "&ufild="+nobridD+"&ufile="+nobridE+"&ufilf="+nobridF+
                    "&ufilg="+nobridG+"&ufilh="+nobridH+"&ufili="+nobridI+"&ufilj="+nobridJ+"&ufilk="+nobridK,
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
        /*
        if (TotalPilih>30) {
            alert("Batas maksimal 30 pilihan... Yang terpilih "+TotalPilih);
            return 0;
        }
        */
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

    <script type="text/javascript">
        $(function() {
            $('#tglfrom').datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                firstDay: 1,
                dateFormat: 'dd MM yy',
                /*
                minDate: '0',
                maxDate: '+2Y',
                */
                onSelect: function(dateStr) {
                    var min = $(this).datepicker('getDate');
                    $('#tglto').datepicker('option', 'minDate', min || '0');
                    datepicked();
                } 
            });
            $('#tglto').datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                firstDay: 1,
                dateFormat: 'dd MM yy',
                minDate: '0',
                /*
                minDate: '0',
                maxDate: '+2Y',
                */
                onSelect: function(dateStr) {
                    var max = $(this).datepicker('getDate');
                    $('#tglfrom').datepicker('option', 'maxDate', max || '+2Y');
                    datepicked();
                } 
            });
        });
        var datepicked = function() {
            var from = $('#from');
            var to = $('#to');
            var nights = $('#nights');
            var fromDate = from.datepicker('getDate')
            var toDate = to.datepicker('getDate')
            if (toDate && fromDate) {
                var difference = 0;
                var oneDay = 1000 * 60 * 60 * 24;
                var difference = Math.ceil((toDate.getTime() - fromDate.getTime()) / oneDay);
                nights.val(difference);
            }
        }
    </script>
    