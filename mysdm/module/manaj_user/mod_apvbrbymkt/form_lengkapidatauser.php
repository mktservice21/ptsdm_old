<?PHP
session_start();
$aksi="";
$fkaryawan=$_SESSION['IDCARD'];
$fjbtid=$_SESSION['JABATANID'];
$fgroupid=$_SESSION['GROUP'];
$fstsadmin=$_SESSION['STSADMIN'];
$flvlposisi=$_SESSION['LVLPOSISI'];
$fdivisi=$_SESSION['DIVISI'];
        
include "../../../config/koneksimysqli.php";
include "../../../config/fungsi_ubahget_id.php";



$psudahmaping=false;
$pdokterid_uc=$_POST['udoktid'];

$pdokterid = decodeString($pdokterid_uc);

$query = "select dokterid, nama, jekel, spid, bagian, alamat1, alamat2, kota, telp, telp2, hp, nowa, tgllahir from hrd.dokter where dokterid='$pdokterid'";
$tampil= mysqli_query($cnmy, $query);
$row=mysqli_fetch_array($tampil);

$pnamadokt=$row['nama'];
$pjekel=$row['jekel'];
$pspdokt=$row['spid'];
$palamat1=$row['alamat1'];
$palamat2=$row['alamat2'];
$pkota=$row['kota'];
$ptelp=$row['telp'];
$pnohp=$row['hp'];
$pnowa=$row['nowa'];
$ptgllahir=$row['tgllahir'];
$pbank="";//$row['norek_bank'];
$pnorekuser="";//$row['norek_user'];
$pnorekatasnama="";//$row['norek_atas'];

if (empty($pnowa)) $pnowa="+62";


if ($ptgllahir=="0000-00-00") $ptgllahir="";

if (!empty($ptgllahir)) $ptgllahir = date('d/mm/Y', strtotime($ptgllahir));



?>


    
    
    
<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Lengkapi Data User</h4>
        </div>
        <br/>
        <div class="">
            
            <?PHP //echo $query; ?>
            
            <div class="row">

                <form method='POST' action='' id='d-form3' name='form3' data-parsley-validate class='form-horizontal form-label-left' enctype='multipart/form-data'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>
                            
                            
                            
                            <div class='x_content'>
                                <div class='col-md-12 col-sm-12 col-xs-12'>



                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_iduser' name='e_iduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdokterid; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama User <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_nmdokt' name='e_nmdokt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadokt; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis Kelamin <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <?PHP
                                            echo "<select class='form-control input-sm' id='e_jekel' name='e_jekel'>";
                                                if ($pjekel=="L") {
                                                    echo "<option value=''>--Pilih--</option>";
                                                    echo "<option value='L' selected>Laki-Laki</option>";
                                                    echo "<option value='P'>Perempuan</option>";
                                                }elseif ($pjekel=="P") {
                                                    echo "<option value=''>--Pilih--</option>";
                                                    echo "<option value='L'>Laki-Laki</option>";
                                                    echo "<option value='P' selected>Perempuan</option>";
                                                }else{
                                                    echo "<option value='' selected>--Pilih--</option>";
                                                    echo "<option value='L'>Laki-Laki</option>";
                                                    echo "<option value='P'>Perempuan</option>";
                                                }
                                            echo "</select>";
                                            ?>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Spesialis <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <?PHP
                                            echo "<select class='form-control input-sm' id='e_idspesial' name='e_idspesial'>";
                                            
                                                $query = "select spId as spid, nama, aktif, initial from hrd.spesial WHERE ( IFNULL(aktif,'')<>'N' OR spId='$pspdokt') ORDER BY nama";
                                                $tampil=mysqli_query($cnmy, $query);
                                                while ($nr= mysqli_fetch_array($tampil)) {
                                                    $r_idpsesial=$nr['spid'];
                                                    $r_nmpsesial=$nr['nama'];
                                                    
                                                    if ($r_idpsesial==$pspdokt)
                                                        echo "<option value='$r_idpsesial' selected>$r_nmpsesial</option>";
                                                    else
                                                        echo "<option value='$r_idpsesial'>$r_nmpsesial</option>";
                                                }
                                            
                                            echo "</select>";
                                            ?>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Tgl. Lahir </label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <div class='input-group date' id='mytgl01'>
                                                <input type="text" class="form-control" id='e_tgllahir' name='e_tgllahir' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptgllahir; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Alamat <span class='required'></span></label>
                                        <div class='col-md-8 col-sm-8 col-xs-12'>
                                            <input type='text' id='e_alamat' name='e_alamat' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat1; ?>' >
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kota <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_kota' name='e_kota' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkota; ?>' >
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No HP <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_nohp' name='e_nohp' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnohp; ?>'  data-mask="____________">
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No WA <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <!--<input type='text' id='e_nowa' name='e_nowa' class='form-control col-md-7 col-xs-12' value='<?PHP //echo $pnowa; ?>' data-mask="____________">-->
                                            <input type='text' id='e_nowa' name='e_nowa' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnowa; ?>' placeholder="+6285115622134">
                                            <span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
                                        </div>
                                    </div>
                                    
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bank <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <?PHP
                                            echo "<select class='form-control input-sm' id='e_idbank' name='e_idbank'>";
                                                echo "<option value='' selected></option>";
                                                /*
                                                $query = "select KDBANK, NAMA from dbmaster.bank ORDER BY NAMA";
                                                $tampil=mysqli_query($cnmy, $query);
                                                while ($nr= mysqli_fetch_array($tampil)) {
                                                    $r_idbank=$nr['KDBANK'];
                                                    $r_nmbank=$nr['NAMA'];
                                                    
                                                    if ($r_idbank==$pbank)
                                                        echo "<option value='$r_idbank' selected>$r_nmbank</option>";
                                                    else
                                                        echo "<option value='$r_idbank'>$r_nmbank</option>";
                                                }
                                                */
                                            echo "</select>";
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Rekening <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_norek' name='e_norek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorekuser; ?>' >
                                        </div>
                                    </div>
                                    
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Rekening Atas Nama <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_atsnmrek' name='e_atsnmrek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorekatasnama; ?>' >
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-md-8 col-sm-8 col-xs-12'>
                                            <?PHP
                                                echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm_simpandatauser()\">Simpan</button>";
                                            ?>
                                        </div>
                                    </div>
                                    
                                    

                                </div>
                            </div>
                            
                            
                            
                            
                        </div>
                    </div>
                </form>
                
                
            </div>
        
        </div>
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
        
    </div>
</div>


<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />


<style>
    .divnone {
        display: none;
    }
    #datatable th {
        font-size: 13px;
    }
    #datatable td { 
        font-size: 11px;
    }
</style>

<script>
    
    function disp_confirm_simpandatauser() {
        var iiduser =document.getElementById('e_iduser').value;
        var ijkel =document.getElementById('e_jekel').value;
        var ispesial =document.getElementById('e_idspesial').value;
        var itgllahir =document.getElementById('e_tgllahir').value;
        var ialamat =document.getElementById('e_alamat').value;
        var ikota =document.getElementById('e_kota').value;
        var inohp =document.getElementById('e_nohp').value;
        var inowa =document.getElementById('e_nowa').value;
        
        var iidbank =document.getElementById('e_idbank').value;
        var inorek =document.getElementById('e_norek').value;
        var iatasnama =document.getElementById('e_atsnmrek').value;
        
        var new_wa = inowa.replace(/_/g, '');
        
        if (iiduser=="") {
            alert("ID KOSONG...");
            return false;
        }
        
        if (ijkel=="") {
            alert("Jenis Kelamin harus diisi...");
            return false;
        }
        
        if (ispesial=="") {
            alert("Spesialis harus diisi...");
            return false;
        }
        
        if (ialamat=="") {
            alert("alamat harus diisi...");
            return false;
        }
        
        if (new_wa=="" || new_wa=="+62") {
            alert("no wa harus diisi...");
            return false;
        }else{
            var iforma_wa=new_wa.substring(0, 3);
            if (iforma_wa=="+62") {
            }else{
                alert("format no wa tidak sesuai...\n\
harus +62");
                return false;
            }
        }
        
        var pText_="";
        
        pText_="Apakah akan melakukan simpan data...?";
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                
                $.ajax({
                    type:"post",
                    url:"module/manaj_user/mod_apvbrbymkt/simpanlengkpdataapvreal.php?module="+module+"&act=simpanbrrealbymkt&idmenu="+idmenu,
                    data:"uiduser="+iiduser+"&uspesial="+ispesial+"&utgllahir="+itgllahir+"&ualamat="+ialamat+
                            "&ukota="+ikota+"&unohp="+inohp+"&unowa="+inowa+
                            "&uidbank="+iidbank+"&unorek="+inorek+"&uatasnama="+iatasnama+"&ujkel="+ijkel,
                    success:function(data){
                        alert(data);
                    }
                });
                
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
        
    }
    
    function myTrim(x) {
        return x.replace(/^\s+|\s+$/gm,'');
    }
</script>
    
<?PHP
mysqli_close($cnmy);
?>




<!-- jquery.inputmask -->
<script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
<!-- bootstrap-daterangepicker -->
<script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- bootstrap-datetimepicker -->
<script src="vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

        
<!--
<script src="vendors/jquery/dist/jquery.min.js"></script>
<link href="module/ks_lihatks/select2.min.css" rel="stylesheet" type="text/css" />
<script src="module/ks_lihatks/select2.min.js"></script>
-->

<script>
$(document).ready(function() {
    //$('.s2').select2();
    //$('.s3').select2();
    
    $('#mytgl01, #mytgl02').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
    
});
</script>
<script>
    document.getElementById('e_nowa').addEventListener('input', function (e) {
        var x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,3})(\d{0,3})(\d{0,3})(\d{0,3})/);
        e.target.value = '+' + x[1] + '' + x[2] + '' + x[3] + '' + x[4] + '' + x[5];
    });
    
    Array.prototype.forEach.call(document.body.querySelectorAll("*[data-mask]"), applyDataMask);
    function applyDataMask(field) {
        var mask = field.dataset.mask.split('');

        // For now, this just strips everything that's not a number
        function stripMask(maskedData) {
            function isDigit(char) {
                return /\d/.test(char);
            }
            return maskedData.split('').filter(isDigit);
        }

        // Replace `_` characters with characters from `data`
        function applyMask(data) {
            return mask.map(function(char) {
                if (char != '_') return char;
                if (data.length == 0) return char;
                return data.shift();
            }).join('')
        }

        function reapplyMask(data) {
            return applyMask(stripMask(data));
        }

        function changed() {   
            var oldStart = field.selectionStart;
            var oldEnd = field.selectionEnd;

            field.value = reapplyMask(field.value);

            field.selectionStart = oldStart;
            field.selectionEnd = oldEnd;
        }

        field.addEventListener('click', changed)
        field.addEventListener('keyup', changed)
    }
</script>

