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

function ShowCOA(udiv, ucoa) {
    var icar = "";
    var idiv = document.getElementById(udiv).value;
    $.ajax({
        type:"post",
        url:"module/mod_br_isikasbon/viewdata.php?module=viewcoadivisi",
        data:"umr="+icar+"&udivi="+idiv,
        success:function(data){
            $("#"+ucoa).html(data);
        }
    });
}

function ShowCOAKode(udiv, ukode, ucoa) {
    var icar = "";
    var ikode = document.getElementById(ukode).value;
    var idiv = document.getElementById(udiv).value;
    $.ajax({
        type:"post",
        url:"module/mod_br_isikasbon/viewdata.php?module=caricoaperkode",
        data:"umr="+icar+"&ukode="+ikode+"&udivi="+idiv,
        success:function(data){
            $("#"+ucoa).html(data);
        }
    });
}


function disp_confirm(pText_,ket)  {
    var ukode = document.getElementById('cb_kdoepil').value;
    var ucoap = document.getElementById('cb_coa').value;
    if (ukode=="") {
        alert("kode masih kosong...");
        return false;
    }
    if (ucoap=="") {
        alert("COA harus dipilih...");
        return false;
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
            document.getElementById("demo-form2").action = "module/mod_br_isikasbon/aksi_isikasbon.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));


                
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$keterangan="";
$divisi="HO";
if ($_SESSION['DIVISI']=="OTC") $divisi="OTC";
$pjumlah="";
$jumlahk="";
$coa="";
$pnamauntuk="";
$pketerangan="";
$idkdoepilih="";

$ppilcoa="";

$pmyact=$_GET['act'];

$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_kasbon WHERE idkasbon='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idbr=$r['idkasbon'];
    $tglberlku = date('d/m/Y', strtotime($r['tgl']));
    $tgl1 = date('d/m/Y', strtotime($r['tgl']));
    $idajukan=$r['karyawanid']; 
    $nmajukan=$_SESSION['NAMALENGKAP']; 
    $keterangan=$r['keterangan'];
    $pjumlah=$r['jumlah'];
    $divisi=$r['divisi'];
    $pnamauntuk=$r['nama'];
    $idkdoepilih=$r['kode'];
    $ppilcoa=$r['coa4'];
    
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowCOAKode('cb_divisi', 'cb_kdoepil', 'cb_coa');">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTHER') ";
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pembuat <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP 
                                                    $query = "select karyawanId, nama From hrd.karyawan
                                                        WHERE IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='' ";
                                                    $query .= " ORDER BY nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        if ($z['karyawanId']==$idajukan)
                                                            echo "<option value='$z[karyawanId]' selected>$z[nama]</option>";
                                                        else
                                                            echo "<option value='$z[karyawanId]'>$z[nama]</option>";
                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_nama' name='e_nama' class='form-control col-md-7 col-xs-12' placeholder="" value='<?PHP echo $pnamauntuk; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_kdoepil' name='cb_kdoepil' data-live-search="true" onchange="ShowCOAKode('cb_divisi', 'cb_kdoepil', 'cb_coa');">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP 
                                                    $query = "select kodeid,nama from hrd.bp_kode where per='' or per='D' ";
                                                    $query .= " order by kodeid";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pidkdkode=$z['kodeid'];
                                                        $pnmkdkode=$z['nama'];
                                                        
                                                        if ($pidkdkode==$idkdoepilih)
                                                            echo "<option value='$pidkdkode' selected>$pnmkdkode</option>";
                                                        else
                                                            echo "<option value='$pidkdkode'>$pnmkdkode</option>";
                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>COA <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_coa' name='cb_coa' data-live-search="true">
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                if ($pmyact=="editdata") {
                                                    $fil = " AND ( c.DIVISI2 = '$divisi' OR RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', '') )";
                                                    if (empty($divisi)) $fil = " AND RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', '')";
                                                    
                                                    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
                                                        LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
                                                        LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
                                                        WHERE 1=1 $fil OR a.COA4='$ppilcoa'";
                                                    $query .= " ORDER BY a.COA4";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pcoa=$z['COA4'];
                                                        $pnmcoa=$z['NAMA4'];
                                                        if ($pcoa==$ppilcoa)
                                                            echo "<option value='$pcoa' selected>$pcoa - $pnmcoa</option>";
                                                        else
                                                            echo "<option value='$pcoa'>$pcoa - $pnmcoa</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Jumlah
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jml' name='e_jml' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>'>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_ket' name='e_ket' class='form-control col-md-7 col-xs-12' value='<?PHP echo $keterangan; ?>'>
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
    