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
$pbrid_uc=$_POST['ubrid'];

$pdokterid = decodeString($pdokterid_uc);
$pbrid = decodeString($pbrid_uc);

$query = "select brid, id_rekening, aktivitas1, aktivitas2 from hrd.br0 where brid='$pbrid'";
$tampilb= mysqli_query($cnmy, $query);
$brow=mysqli_fetch_array($tampilb);
$pidrek_br=$brow['id_rekening'];
$pketerangan1=$brow['aktivitas1'];
$pketerangan2=$brow['aktivitas2'];

$query = "select a.id_rekening, a.dokterid, a.idbank, b.NAMA as nama_bank, a.kcp, "
        . " a.norekening, a.atasnama, a.norek_sesuai, a.relasi_norek "
        . " from hrd.dokter_norekening as a "
        . " LEFT JOIN dbmaster.bank as b on a.idbank=b.KDBANK WHERE a.id_rekening='$pidrek_br'";
$tampil=mysqli_query($cnmy, $query);
$nr= mysqli_fetch_array($tampil);

$pbank=$nr['idbank'];
$pnmbank=$nr['nama_bank'];
$pkcpbank=$nr['kcp'];
$pnorekatasnama=$nr['atasnama'];
$pnorekuser=$nr['norekening'];
$pnoreksesuai=$nr['norek_sesuai'];
$pnmrelasi=$nr['relasi_norek'];


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


    $psesuai_="Y";
    if (!empty($pnmrelasi)) {
        $psesuai_="N";
    }
    
    if ($pnoreksesuai=="Y") $psesuai_="Y";
    elseif ($pnoreksesuai=="N") $psesuai_="N";
    
    
?>


    
    
    
<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Isi Rekening (Budget Request) - Data User</h4>
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
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID BR <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='hidden' id='e_iduser' name='e_iduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdokterid; ?>' Readonly>
                                            <input type='text' id='e_idbr' name='e_idbr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbrid; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama User <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_nmdokt' name='e_nmdokt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadokt; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Alamat <span class='required'></span></label>
                                        <div class='col-md-8 col-sm-8 col-xs-12'>
                                            <input type='text' id='e_alamat' name='e_alamat' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat1; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No HP <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_nohp' name='e_nohp' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnohp; ?>' readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No WA <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_nowa' name='e_nowa' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnowa; ?>'  Readonly>
                                            <span class="fa fa-user form-control-feedback right" aria-hidden="true"></span>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Aktivitas <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <textarea class='form-group' id="txt_ket1" name="txt_ket1" rows="3px" readonly><?PHP echo $pketerangan1; ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih Rekening <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <?PHP
                                            echo "<select class='form-control input-sm' id='e_idrek' name='e_idrek' onchange=\"ShowDataRekeningByUser()\">";
                                                echo "<option value='' selected></option>";
                                                
                                                $query = "select a.id_rekening, a.dokterid, a.idbank, b.NAMA as nama_bank, a.kcp, "
                                                        . " a.norekening, a.atasnama, a.relasi_norek "
                                                        . " from hrd.dokter_norekening as a "
                                                        . " LEFT JOIN dbmaster.bank as b on a.idbank=b.KDBANK WHERE a.dokterid='$pdokterid' ORDER BY b.NAMA";
                                                $tampil=mysqli_query($cnmy, $query);
                                                while ($nr= mysqli_fetch_array($tampil)) {
                                                    $r_idrek=$nr['id_rekening'];
                                                    $r_idbank=$nr['idbank'];
                                                    $r_nmbank=$nr['nama_bank'];
                                                    $r_an=$nr['atasnama'];
                                                    $r_norek=$nr['norekening'];
                                                    
                                                    $pnama_rek="$r_idrek - $r_an ($r_norek) - $r_nmbank";
                                                    if ($r_idrek==$pidrek_br)
                                                        echo "<option value='$r_idrek' selected>$pnama_rek</option>";
                                                    else
                                                        echo "<option value='$r_idrek'>$pnama_rek</option>";
                                                }
                                                
                                            echo "</select>";
                                            ?>
                                        </div>
                                    </div>
                                    
                                    
                                    <div id="div_rek">
                                    
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bank <span class='required'></span></label>
                                            <div class='col-md-4 col-sm-4 col-xs-12'>
                                                <input type='hidden' id='e_idbank' name='e_idbank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbank; ?>'  Readonly>
                                                <input type='text' id='e_nmbank' name='e_nmbank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmbank; ?>'  Readonly>
                                            </div>
                                        </div>


                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>KCP <span class='required'></span></label>
                                            <div class='col-md-4 col-sm-4 col-xs-12'>
                                                <input type='text' id='e_kcpbank' name='e_kcpbank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkcpbank; ?>'  Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Rekening <span class='required'></span></label>
                                            <div class='col-md-4 col-sm-4 col-xs-12'>
                                                <input type='text' id='e_norek' name='e_norek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorekuser; ?>'  Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Rekening Atas Nama <span class='required'></span></label>
                                            <div class='col-md-4 col-sm-4 col-xs-12'>
                                                <input type='text' id='e_atsnmrek' name='e_atsnmrek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorekatasnama; ?>'  Readonly>
                                            </div>
                                        </div>

                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                            <div class='col-md-4 col-sm-4 col-xs-12'>
                                                <?PHP
                                                if ($psesuai_=="Y"){
                                                    echo "<label><input type='checkbox' class='js-switch' id='chk_sesuai' name='chk_sesuai' value='Y' checked> Rekening Sesuai User</label>";
                                                }else{
                                                    echo "<label><input type='checkbox' class='js-switch' id='chk_sesuai' name='chk_sesuai' value='' > Rekening Sesuai User</label>";
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Relasi (istri /suami /anak /dsb.) <span class='required'></span></label>
                                            <div class='col-md-5 col-sm-5 col-xs-12'>
                                                <input type='text' id='e_relasinorek' name='e_relasinorek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmrelasi; ?>' placeholder="diisi jika atas nama no rekening tidak sesuai user" Readonly>
                                            </div>
                                        </div>
                                    
                                    
                                    </div>
                                    
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-md-8 col-sm-8 col-xs-12'>
                                            <?PHP
                                                echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm_simpanbrrekening()\">Simpan</button>";
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
    
    function ShowDataRekeningByUser() {
        $("#div_rek").html("");
        var iiduser =document.getElementById('e_iduser').value;
        var irekid =document.getElementById('e_idrek').value;
        
        $.ajax({
            type:"post",
            url:"module/manaj_user/mod_apvbrbymkt/viewdataapvbrmkt.php?module=viewnorekeningdata",
            data:"uiduser="+iiduser+"&urekid="+irekid,
            success:function(data){
                $("#div_rek").html(data);
            }
        });
    }
    
    function disp_confirm_simpanbrrekening() {
        var iiduser =document.getElementById('e_iduser').value;
        var ibrid =document.getElementById('e_idbr').value;
        
        var iidrekening =document.getElementById('e_idrek').value;
        var iidbank =document.getElementById('e_idbank').value;
        var ikcp =document.getElementById('e_kcpbank').value;
        var inorek =document.getElementById('e_norek').value;
        var iatasnama =document.getElementById('e_atsnmrek').value;
        var inmrelasi =document.getElementById('e_relasinorek').value;
        var ichksesuai =document.getElementById('chk_sesuai');
        
        if (iiduser=="") {
            alert("ID KOSONG...");
            return false;
        }
        
        if (iidrekening=="") {
            alert("Rekening belum dipilih...");
            return false;
        }
        
        if (iidbank=="") {
            alert("Bank masih kosong...");
            return false;
        }
        
        if (inorek=="") {
            alert("no rekening harus diisi...");
            return false;
        }
        
        if (iatasnama=="") {
            alert("atas nama rekening harus diisi...");
            return false;
        }
        
        if (ichksesuai.checked==false) {
            if (inmrelasi=="") {
                alert("relasi (istri/anak/dll.) harus diisi...");
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
                    url:"module/manaj_user/mod_apvbrbymkt/simpanbrquest.php?module="+module+"&act=simpanbrequestnorek&idmenu="+idmenu,
                    data:"uiduser="+iiduser+"&ubrid="+ibrid+"&uidrekening="+iidrekening+"&uidbank="+iidbank+"&ukcp="+ikcp+"&unorek="+inorek+"&uatasnama="+iatasnama+"&unmrelasi="+inmrelasi,
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



