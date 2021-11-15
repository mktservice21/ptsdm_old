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

$query = "select dokterid, nama, spid, bagian, alamat1, alamat2, kota, telp, telp2, hp, nowa, tgllahir from hrd.dokter where dokterid='$pdokterid'";
$tampil= mysqli_query($cnmy, $query);
$row=mysqli_fetch_array($tampil);

$pnamadokt=$row['nama'];
$pspdokt=$row['spid'];
$palamat1=$row['alamat1'];
$palamat2=$row['alamat2'];
$pkota=$row['kota'];
$ptelp=$row['telp'];
$pnohp=$row['hp'];
$pnowa=$row['nowa'];
$ptgllahir=$row['tgllahir'];



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
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Alamat <span class='required'></span></label>
                                        <div class='col-md-8 col-sm-8 col-xs-12'>
                                            <input type='text' id='e_alamat' name='e_alamat' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat1; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No HP <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_nohp' name='e_nohp' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnohp; ?>'  Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No WA <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_nowa' name='e_nowa' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnowa; ?>' Readonly>
                                            <span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <?PHP
                                            echo "<input type='checkbox' id='chk_userveri' name='chk_userveri' value='OKE'> User sudah diverifikasi<br/>";
                                            echo "<input type='checkbox' id='chk_norekveri' name='chk_norekveri' value='OKE'> No Rekening sudah diverifikasi<br/>";
                                            echo "<input type='checkbox' id='chk_tanggung' name='chk_tanggung' value='OKE'> Saya Bertanggungjawab<br/>";
                                            ?>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-md-7 col-sm-7 col-xs-12'>
                                            <?PHP
                                                include "ttd_apvbrrealbymkt.php";
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
    
    function disp_confirm_approve(pText_, ket, data_img) {
        //chk_userveri, chk_norekveri, chk_tanggung
        var iiduser =document.getElementById('e_iduser').value;
        var ichk_usrveri =document.getElementById('chk_userveri');
        var iveriuser="N";
        if (ichk_usrveri.checked==true) {
            iveriuser="Y";
        }
        
        var ichk_norek =document.getElementById('chk_norekveri');
        var inorekuser="N";
        if (ichk_norek.checked==true) {
            inorekuser="Y";
        }
        
        var ichk_tanggung =document.getElementById('chk_tanggung');
        var itanggungjwb="N";
        if (ichk_tanggung.checked==true) {
            itanggungjwb="Y";
        }
        
        //alert(iveriuser+" | "+inorekuser+" | "+itanggungjwb); return false;
        
        
        if (iiduser=="") {
            alert("ID KOSONG...");
            return false;
        }
        
        var pText_="";
        
        pText_="Apakah akan melakukan approve data...";
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                
                alert(data_img);
                
                
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


