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
        url:"module/md_m_spg/viewdata.php?module=viewcoadivisi",
        data:"umr="+icar+"&udivi="+idiv,
        success:function(data){
            $("#"+ucoa).html(data);
        }
    });
}

function showArea(ocabang, oarea) {
    var ecab = document.getElementById(ocabang).value;
    $.ajax({
        type:"post",
        url:"module/md_m_spg/viewdata.php?module=viewdataareacab&data1="+ocabang+"&data2="+oarea,
        data:"ucab="+ecab+"&uarea="+oarea,
        success:function(data){
            $("#"+oarea).html(data);
            showToko(ocabang, '', 'cb_custsdm');
        }
    });
}

function showToko(ocabang, oarea, otoko) {
    var ecab = document.getElementById(ocabang).value;
    var earea="";
    if (oarea == "") {
    }else{
        var earea = document.getElementById(oarea).value;
    }
    
    $.ajax({
        type:"post",
        url:"module/md_m_spg/viewdata.php?module=viewdatatokocab&data1="+ocabang+"&data2="+earea,
        data:"ucab="+ecab+"&uarea="+earea,
        success:function(data){
            $("#"+otoko).html(data);
        }
    });
}

function showAreaByToko(ocabang, oarea, otoko) {
    var ecab = document.getElementById(ocabang).value;
    var earea = document.getElementById(oarea).value;
    var etoko = document.getElementById(otoko).value;
    //if (earea == "") {

        $.ajax({
            type:"post",
            url:"module/md_m_spg/viewdata.php?module=viewdataareacabbytoko&data1="+ocabang+"&data2="+oarea,
            data:"ucab="+ecab+"&uarea="+oarea+"&utoko="+etoko,
            success:function(data){
                $("#"+oarea).html(data);
            }
        });

    //}
}

function disp_confirm(pText_,ket)  {
    
    var enama =document.getElementById('e_nama').value;
    
    if (enama==""){
        alert("nama lengkap masih kosong....");
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
            document.getElementById("demo-form2").action = "module/md_m_spg/aksi_spg.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
$idkaryawan="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = "";
$ptgllahir = "";
$ptlahir = "";

$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP'];

$namalengkap="";
$pdivisi="OTC";
$pidcabang="";
$pidarea="";
$pidtoko="";
$ptoko="";
$pidagama="";
$pjekel="";
$pidjabatan="";
$palamat="";
$pkota="";
$php="";

$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_spg WHERE karyawanId='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idkaryawan=$r['karyawanId'];
    $tgl1 = date('d/m/Y', strtotime($r['tglmasuk']));
    if (!empty($r['tglkeluar']) AND $r['tglkeluar']<>"0000-00-00")
        $tgl2 = date('d/m/Y', strtotime($r['tglkeluar']));
    
    if (!empty($r['tgllahir']) AND $r['tgllahir']<>"0000-00-00")
        $ptgllahir = date('d/m/Y', strtotime($r['tgllahir']));
    
    $namalengkap=$r['nama'];
    $ptlahir=$r['tempat'];
    
    if (!empty($r['DIVISI']))
        $pdivisi=$r['DIVISI'];
    $pidcabang=$r['iCabangId'];
    $pjekel=$r['jkel'];
    $pidagama=$r['agamaId'];
    $pidjabatan=$r['jabatanId'];
    $palamat=$r['alamat'];
    $pkota=$r['kota'];
    $php=$r['hp'];
    
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
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idkaryawan; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal Masuk </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tglmasuk' name='e_tglmasuk' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Lengkap <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_nama' name='e_nama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $namalengkap; ?>'>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal Lahir </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tgllahir' name='e_tgllahir' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptgllahir; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tempat Lahir <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_tlahir' name='e_tlahir' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptlahir; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis Kelamin <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_jekel' name='cb_jekel'>
                                            <?PHP
                                            if ($pjekel=="L") {
                                                echo "<option value='L' selected>Laki-Laki</option>";
                                                echo "<option value='P'>Perempuan</option>";
                                            }else{
                                                echo "<option value='L'>Laki-Laki</option>";
                                                echo "<option value='P' selected>Perempuan</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Alamat <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_alamat' name='e_alamat' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kota <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_kota' name='e_kota' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkota; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Hp <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_hp' name='e_hp' class='form-control col-md-7 col-xs-12' value='<?PHP echo $php; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jabatan <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_jabatan' name='cb_jabatan'>
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select jabatanId, nama from hrd.jabatan ";
                                            $query .=" order by jabatanId";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $iidjabatan=$z['jabatanId'];
                                                $inmjabatan=$z['nama'];
                                                
                                                if ($iidjabatan==$pidjabatan)
                                                    echo "<option value='$iidjabatan' selected>$iidjabatan - $inmjabatan</option>";
                                                else
                                                    echo "<option value='$iidjabatan'>$iidjabatan - $inmjabatan</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Agama <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_agama' name='cb_agama'>
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select agamaid, nama from hrd.agama ";
                                            $query .=" order by agamaid";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $iidagama=$z['agamaid'];
                                                $inmagama=$z['nama'];
                                                
                                                if ($iidagama==$pidagama)
                                                    echo "<option value='$iidagama' selected>$iidagama - $inmagama</option>";
                                                else
                                                    echo "<option value='$iidagama'>$iidagama - $inmagama</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi'>
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' ";
                                            if ($_SESSION['DIVISI']=="OTC") {
                                                $query .=" AND DivProdId = 'OTC' ";
                                            }
                                            $query .=" order by DivProdId";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $idivisi=$z['DivProdId'];
                                                
                                                if ($idivisi==$pdivisi)
                                                    echo "<option value='$idivisi' selected>$idivisi</option>";
                                                else
                                                    echo "<option value='$idivisi'>$idivisi</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_cabang' name='cb_cabang' onchange="showArea('cb_cabang', 'cb_areasdm')">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select icabangid_o, nama from dbmaster.v_icabang_o WHERE (aktif='Y' OR icabangid_o='$pidcabang') ";
                                            $query .=" order by nama";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $iidcabang=$z['icabangid_o'];
                                                $inmcabang=$z['nama'];
                                                
                                                if ($iidcabang==$pidcabang)
                                                    echo "<option value='$iidcabang' selected>$inmcabang</option>";
                                                else
                                                    echo "<option value='$iidcabang'>$inmcabang</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_areasdm'>Area <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' name='cb_areasdm' id='cb_areasdm' onchange="showToko('cb_cabang', 'cb_areasdm', 'cb_custsdm')"><!-- onchange="ClearModalToko('cb_custsdm', 'cb_nmcustsdm')" -->
                                            <option value="">-- Pilihan --</option>
                                            <?PHP
                                            $cabang = $pidcabang;

                                            $query = "select areaid_o, nama, aktif from MKT.iarea_o where icabangid_o='$cabang' order by nama";

                                            $tampil=mysqli_query($cnit, $query);
                                            while($a=mysqli_fetch_array($tampil)){
                                                if ($a['areaid_o']==$pareao)
                                                    echo "<option value='$a[areaid_o]' selected>$a[nama]</option>";
                                                else
                                                    echo "<option value='$a[areaid_o]'>$a[nama]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_custsdm'>Customer / Toko <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' name='cb_custsdm' id='cb_custsdm' onchange="showAreaByToko('cb_cabang', 'cb_areasdm', 'cb_custsdm')">
                                            <option value="">-- Pilihan --</option>
                                            <?PHP
                                            $cabang = $pidcabang;
                                            $area=" and areaid_o='$pidarea' ";

                                            $query = "select icustid_o, nama from MKT.icust_o where icabangid_o='$cabang' $area order by nama";

                                            $tampil=mysqli_query($cnit, $query);
                                            while($a=mysqli_fetch_array($tampil)){
                                                if ($a['icustid_o']==$pidtoko)
                                                    echo "<option value='$a[icustid_o]' selected>$a[nama]</option>";
                                                else
                                                    echo "<option value='$a[icustid_o]'>$a[nama]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_toko' name='e_toko' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptoko; ?>'>
                                    </div>
                                </div>
                                
                                <?PHP
                                if ($_GET['act']!="editdata") {
                                    ?>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal Keluar </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl02'>
                                            <input type="text" class="form-control" id='e_tglkeluar' name='e_tglkeluar' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl2; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                        <div style="color:red;"> *) <b>jika diisi maka status jadi non aktif</b></div>
                                    </div>
                                </div>
                                    <?PHP
                                }
                                ?>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
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
    