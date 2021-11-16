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
$pbank="";//$row['norek_bank'];
$pkcpbank="";//$row['kcp'];
$pnorekuser="";//$row['norek_user'];
$pnorekatasnama="";//$row['norek_atas'];
$pnmrelasi="";//$row['relasi_norek'];



if ($ptgllahir=="0000-00-00") $ptgllahir="";

if (!empty($ptgllahir)) $ptgllahir = date('d/mm/Y', strtotime($ptgllahir));



?>


    
    
    
<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Isi Rekening - Data User</h4>
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
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bank <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <?PHP
                                            echo "<select class='form-control input-sm' id='e_idbank' name='e_idbank'>";
                                                echo "<option value='' selected></option>";
                                                
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
                                                
                                            echo "</select>";
                                            ?>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>KCP <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_kcpbank' name='e_kcpbank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkcpbank; ?>' >
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Rekening <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_norek' name='e_norek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorekuser; ?>' >
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Rekening Atas Nama <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='text' id='e_atsnmrek' name='e_atsnmrek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorekatasnama; ?>' >
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <?PHP
                                            echo "<label><input type='checkbox' class='js-switch' id='chk_sesuai' name='chk_sesuai' value='Y' checked> Rekening Sesuai User</label>";
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Relasi (istri/anak/dll.) <span class='required'></span></label>
                                        <div class='col-md-5 col-sm-5 col-xs-12'>
                                            <input type='text' id='e_relasinorek' name='e_relasinorek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmrelasi; ?>' placeholder="diisi jika atas nama no rekening tidak sesuai user">
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-md-8 col-sm-8 col-xs-12'>
                                            <?PHP
                                                echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm_simpanrekdatauser()\">Simpan</button>";
                                            ?>
                                        </div>
                                    </div>
                                    
                                    

                                </div>
                            </div>
                            
                            
                            
                            
                        </div>
                        
                        
                        <div id='loading_rek'></div>
                        <div id='c-data_rek'>
                            <div class='x_content'>

                                <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                    <thead>
                                        <tr>
                                            <th width='7px'>No</th>
                                            <th width='10px'>Bank</th>
                                            <th width='10px'>KCP</th>
                                            <th width='10px'>No Rekening</th>
                                            <th width='10px'>Atas Nama</th>
                                            <th width='10px'>Relasi</th>
                                            <th width='10px'>Input User</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?PHP
                                        $no=1;
                                        $query = "select a.idbank, b.NAMA as nama_bank, a.kcp, a.norekening, a.atasnama, a.relasi_norek, a.tglinput, "
                                                . " a.inputby, c.nama as nama_input "
                                                . " from hrd.dokter_norekening as a LEFT JOIN dbmaster.bank as b on a.idbank=b.KDBANK "
                                                . " LEFT JOIN hrd.karyawan as c on a.inputby=c.karyawanId "
                                                . " WHERE a.dokterid='$pdokterid'";
                                        $tampil_u= mysqli_query($cnmy, $query);
                                        while ($urow= mysqli_fetch_array($tampil_u)) {
                                            $uidbank=$urow['idbank'];
                                            $unmbank=$urow['nama_bank'];
                                            $ukcp=$urow['kcp'];
                                            $unorek=$urow['norekening'];
                                            $uatasnm=$urow['atasnama'];
                                            $urelasi=$urow['relasi_norek'];
                                            $uuserinputnm=$urow['nama_input'];
                                            
                                            echo "<tr>";
                                            echo "<td nowrap>$no</td>";
                                            echo "<td nowrap>$unmbank</td>";
                                            echo "<td nowrap>$ukcp</td>";
                                            echo "<td nowrap>$unorek</td>";
                                            echo "<td nowrap>$uatasnm</td>";
                                            echo "<td nowrap>$urelasi</td>";
                                            echo "<td nowrap>$uuserinputnm</td>";
                                            echo "</tr>";
                                            
                                            $no++;
                                        }
                                        ?>
                                    </tbody>
                                </table>

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
    
    function disp_confirm_simpanrekdatauser() {
        var iiduser =document.getElementById('e_iduser').value;
        
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
        
        pText_="Apakah akan melakukan simpan data...";
        
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
                    url:"module/manaj_user/mod_apvbrbymkt/simpanisinorek.php?module="+module+"&act=simpanbrrealbymkt&idmenu="+idmenu,
                    data:"uiduser="+iiduser+"&uidbank="+iidbank+"&ukcp="+ikcp+"&unorek="+inorek+"&uatasnama="+iatasnama+"&unmrelasi="+inmrelasi,
                    success:function(data){
                        alert(data);
                        
                        $.ajax({
                            type:"post",
                            url:"module/manaj_user/mod_apvbrbymkt/show_datarek.php?module=showdatarekening",
                            data:"udoktid="+iiduser,
                            success:function(data){
                                $("#c-data_rek").html(data);
                            }
                        });
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



