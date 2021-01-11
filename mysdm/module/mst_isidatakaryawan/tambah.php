<!--<script src="module/mod_br_entrydcc/mytransaksi.js"></script>-->
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
    $(function() {
        $('#mytgl03, #mytgl04').datetimepicker({
            ignoreReadonly: true,
            allowInputToggle: true,
            format: 'DD/MM/YYYY'
        });
    });
</script>


<script>
    function ShowDataUntukDivisi1() {
        ShowDataCabang();
        ShowDataDivisi2();
    }
    
    function ShowDataCabangDariJBT() {
        ShowDataCabang();
        ShowDataArea();
    }
    
    function ShowDataCabang() {
       var edivid =document.getElementById('cb_divisi').value;
       var ecabangid =document.getElementById('cb_cabang').value;
       var ejbtid =document.getElementById('cb_jabatan').value;
       
       $.ajax({
           type:"post",
           url:"module/mst_isidatakaryawan/viewdata.php?module=viewdatacabang",
           data:"ucabangid="+ecabangid+"&udivid="+edivid+"&ujbtid="+ejbtid,
           success:function(data){
               $("#cb_cabang").html(data);
               ShowDataArea();
               //$("#cb_area").html("<option value=''>--Pilihan--</option>");
           }
       });
    }
    
    function ShowDataArea() {
       var ecabangid =document.getElementById('cb_cabang').value;
       var edivid =document.getElementById('cb_divisi').value;
       var eareaids =document.getElementById('cb_area').value;
       var ejbtid =document.getElementById('cb_jabatan').value;
       
       $.ajax({
           type:"post",
           url:"module/mst_isidatakaryawan/viewdata.php?module=viewdataarea",
           data:"ucabangid="+ecabangid+"&udivid="+edivid+"&uareaids="+eareaids+"&ujbtid="+ejbtid,
           success:function(data){
               $("#cb_area").html(data);
           }
       });
    }
    
    function disp_confirm(pText_,ket)  {
        var enmkaryawan =document.getElementById('e_nmkaryawan').value;
        var ejabatan =document.getElementById('cb_jabatan').value;
        var estskry =document.getElementById('cb_stskry').value;
        var edivid =document.getElementById('cb_divisi').value;
        var ecabangid =document.getElementById('cb_cabang').value;
        var etglkeluar =document.getElementById('e_tglkeluar').value;
       
        var ispv = document.getElementById('e_spv').value;
        var idm = document.getElementById('e_dm').value;
        var ism = document.getElementById('e_sm').value;
        var igsm = document.getElementById('e_gsm').value;
        
        
        if (enmkaryawan=="") {
            alert("nama karyawan tidak boleh kosong...!!!");
            return false;
        }
       
        if (ejabatan=="") {
            alert("jabatan tidak boleh kosong...!!!");
            return false;
        }
       
        if (estskry=="") {
            alert("status karyawan tidak boleh kosong...!!!");
            return false;
        }
       
        if (edivid=="") {
            alert("divisi masih kosong...!!!");
            return false;
        }
       
        if (ecabangid=="") {
            alert("cabang masih kosong...!!!");
            return false;
        }
        
        
        if (edivid=="HO" || edivid=="OTC"){
            
        }else{
        
            if (ejabatan=="15") {
                if (ispv=="" && idm=="" && ism=="") {
                    alert("SPV atau DM atau SM masih kosong");
                    return false;
                }
            }

            if (ejabatan=="10" || ejabatan=="18") {
                if (idm=="" && ism=="") {
                    alert("DM atau SM masih kosong");
                    return false;
                }
            }

            if (ejabatan=="08") {
                if (ism=="") {
                    alert("SM masih kosong");
                    return false;
                }
            }

            if (ejabatan=="20" || ejabatan=="39") {
                if (igsm=="") {
                    alert("GSM masih kosong");
                    return false;
                }
            }
            
        }
        
        if (ket=="input") {
            pText_ = "Nama : "+enmkaryawan+"\n\
Pastikan Nama Karyawan Sudah Sesuai, karena Nama tidak bisa diubah...!!!\n\
Jika ingin mengubah Nama hubungin MS.\n\
Apakah akan melanjutkan SIMPAN...???";
        }
        
        if (etglkeluar=="") {
        }else{
            //alert("Tanggal Keluar diisi, maka karyawan akan menjadi NON AKTIF...");
            pText_ = "Tanggal Keluar diisi, maka karyawan akan menjadi NON AKTIF...\n\
Apakah akan melanjutkan Simpan...?";
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
                document.getElementById("demo-form2").action = "module/mst_isidatakaryawan/aksi_isidatakaryawan.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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

$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));




$pidkaryawan="";
$pnamakry="";
$ptmplahir="";
$ptgllahir = date('d/m/Y', strtotime($hari_ini));
$palamat1="";
$palamat2="";
$pkotaalamat="";
$ptlprumah="";
$ptlphp="";
$pjekel="L";
$pagama="";
$ptglmasuk = date('d/m/Y', strtotime($hari_ini));
$ptglkeluar="";

$pidedu="";
$pidjabatan="";
$pstskry="";
$piddivisi="";
$piddivisi2="";
$pidcabang="";
$pidarea="";


$ppin="";
$pnmbank="";
$pcabangbank="";
$pkotabnk="";
$prpovinbnk="";
$pnmrekbank="";
$pnorekbank="";


$ppasnama="";
$ppastempatlahir="";
$ppastgllahir = date('d/m/Y', strtotime($hari_ini));
$ppaskerja="";

$panaknm1="";
$panakjekel1="";
$panaktempat1="";
$panaktgllhir1="";

$panaknm2="";
$panakjekel2="";
$panaktempat2="";
$panaktgllhir2="";

$panaknm3="";
$panakjekel3="";
$panaktempat3="";
$panaktgllhir3="";

$panaknm4="";
$panakjekel4="";
$panaktempat4="";
$panaktgllhir4="";

$panaknm5="";
$panakjekel5="";
$panaktempat5="";
$panaktgllhir5="";

$panaknm6="";
$panakjekel6="";
$panaktempat6="";
$panaktgllhir6="";



$atasanidspv="";
$nnmspv="";
$atasaniddm="";
$nnmdm="";
$atasanidsm="";
$nnmsm="";
$atasanidgsm="";
$nnmgsm="";

$lblnmdm="DM";
$lblnmgsm="GSM/HOS";


$pnamareadonly="";
$pketnama="";

$pstsact=$_GET['act'];
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $pnamareadonly="Readonly";
    $pketnama="<br/><span style='color:red;'>nama tidak bisa diubah</span>";
    $pnamareadonly=""; $pketnama="";
    
    $pidkaryawan=$_GET['id'];
    
    $query = "select * from hrd.karyawan where karyawanid='$pidkaryawan'";
    $tampil= mysqli_query($cnmy, $query);
    $rs= mysqli_fetch_array($tampil);
    
    $pnamakry=$rs['nama'];
    
    if (empty($pnamakry)) $pnamareadonly="";
    
    $ptmplahir=$rs['tempat'];
    
    $pntgllah=$rs['tgllahir'];
    if ($pntgllah=="0000-00-00") $pntgllah="";
    if (!empty($pntgllah)) $ptgllahir = date('d/m/Y', strtotime($pntgllah));
    else $ptgllahir="";
    
    $palamat1=$rs['alamat1'];
    $palamat2=$rs['alamat2'];
    $pkotaalamat=$rs['kota'];

    $ptlprumah=$rs['telp'];
    $ptlphp=$rs['hp'];
    $pjekel=$rs['jkel'];
    $pagama=$rs['agamaId'];
    
    $pntglmsk=$rs['tglmasuk'];
    if ($pntglmsk=="0000-00-00") $pntglmsk="";
    if (!empty($pntglmsk)) $ptglmasuk = date('d/m/Y', strtotime($pntglmsk));
    else $ptglmasuk="";
    
    $pntglkel=$rs['tglkeluar'];
    if ($pntglkel=="0000-00-00") $pntglkel="";
    if (!empty($pntglkel)) $ptglkeluar = date('d/m/Y', strtotime($pntglkel));
    else $ptglkeluar="";
    
    
    
    $pidedu=$rs['eduId'];
    $pidjabatan=$rs['jabatanId'];
    $pstskry=$rs['skar'];
    $piddivisi=$rs['divisiId'];
    $piddivisi2=$rs['divisiId2'];
    $pidcabang=$rs['iCabangId'];
    $pidarea=$rs['areaId'];
    
    $ppin=$rs['pin'];
    $pnmbank=$rs['b_bank'];
    $pcabangbank=$rs['b_cabang'];
    $pkotabnk=$rs['b_kota'];
    $prpovinbnk=$rs['b_prov'];
    $pnmrekbank=$rs['b_nama'];
    $pnorekbank=$rs['b_norek'];

    $ppasnama=$rs['pasangan'];
    $ppastempatlahir=$rs['tempat2'];
    
    $pntgllahir2=$rs['tgllahir2'];
    if ($pntgllahir2=="0000-00-00") $pntgllahir2="";
    if (!empty($pntgllahir2)) $ppastgllahir = date('d/m/Y', strtotime($pntgllahir2));
    else $ppastgllahir="";
    
    $ppaskerja=$rs['pekerjaan'];
    
    mysqli_query($cnmy, "set @no=0");
    $query ="select @no:=@no+1 as nurut, karyawanid, nama, jkel, tempat, tgllahir from hrd.anak where karyawanid='$pidkaryawan'";
    $tampilkan=mysqli_query($cnmy, $query);
    while ($nr= mysqli_fetch_array($tampilkan)) {
        $purut=$nr['nurut'];
        
        if ($purut=="1") {
            $panaknm1=$nr['nama'];
            $panakjekel1=$nr['jkel'];
            $panaktempat1=$nr['tempat'];

            $plhrtgl1=$nr['tgllahir'];
            if ($plhrtgl1=="0000-00-00") $plhrtgl1="";
            if (!empty($plhrtgl1)) $panaktgllhir1 = date('Y-m-d', strtotime($plhrtgl1));
        }
        
        if ($purut=="2") {
            $panaknm2=$nr['nama'];
            $panakjekel2=$nr['jkel'];
            $panaktempat2=$nr['tempat'];

            $plhrtgl1=$nr['tgllahir'];
            if ($plhrtgl1=="0000-00-00") $plhrtgl1="";
            if (!empty($plhrtgl1)) $panaktgllhir2 = date('Y-m-d', strtotime($plhrtgl1));
        }
        
        if ($purut=="3") {
            $panaknm3=$nr['nama'];
            $panakjekel3=$nr['jkel'];
            $panaktempat3=$nr['tempat'];

            $plhrtgl1=$nr['tgllahir'];
            if ($plhrtgl1=="0000-00-00") $plhrtgl1="";
            if (!empty($plhrtgl1)) $panaktgllhir3 = date('Y-m-d', strtotime($plhrtgl1));
        }
        
        if ($purut=="4") {
            $panaknm4=$nr['nama'];
            $panakjekel4=$nr['jkel'];
            $panaktempat4=$nr['tempat'];

            $plhrtgl1=$nr['tgllahir'];
            if ($plhrtgl1=="0000-00-00") $plhrtgl1="";
            if (!empty($plhrtgl1)) $panaktgllhir4 = date('Y-m-d', strtotime($plhrtgl1));
        }
        
        if ($purut=="5") {
            $panaknm5=$nr['nama'];
            $panakjekel6=$nr['jkel'];
            $panaktempat5=$nr['tempat'];

            $plhrtgl1=$nr['tgllahir'];
            if ($plhrtgl1=="0000-00-00") $plhrtgl1="";
            if (!empty($plhrtgl1)) $panaktgllhir5 = date('Y-m-d', strtotime($plhrtgl1));
        }
        
        
    }
    
    
    
    $query = "SELECT * FROM dbmaster.t_karyawan_posisi WHERE karyawanid='$pidkaryawan'";
    $tampilk=mysqli_query($cnmy, $query);
    $ns= mysqli_fetch_array($tampilk);
    
    $atasanidspv=$ns['spv'];
    $atasaniddm=$ns['dm'];
    $atasanidsm=$ns['sm'];
    $atasanidgsm=$ns['gsm'];
    
}



?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
        
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                
                <!--kiri-->
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidkaryawan; ?>' Readonly>
                                </div>
                            </div>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PIN <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_pin' name='e_pin' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp3' value='<?PHP echo $ppin; ?>' maxlength="4">
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>*) Nama <?PHP echo $pketnama; ?><span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_nmkaryawan' name='e_nmkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamakry; ?>' onkeyup="this.value = this.value.toUpperCase()" <?PHP echo $pnamareadonly; ?> >
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tempat Lahir <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_tlahir' name='e_tlahir' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptmplahir; ?>'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Lahir </label>
                                <div class='col-md-6'>
                                    <div class='input-group date' id='mytgl01'>
                                        <input type="text" class="form-control" id='e_tgllahir' name='e_tgllahir' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptgllahir; ?>' Readonly>
                                        <span class='input-group-addon'>
                                            <span class='glyphicon glyphicon-calendar'></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Alamat <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_alamat1' name='e_alamat1' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat1; ?>'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_alamat2' name='e_alamat2' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $palamat2; ?>'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kota <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_kotaalamat' name='e_kotaalamat' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkotaalamat; ?>'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Telp. Rumah <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_tlprumah' name='e_tlprumah' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptlprumah; ?>'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Hp <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_tlphp' name='e_tlphp' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptlphp; ?>'>
                                </div>
                            </div>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis Kelamin <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div style="margin-bottom:2px;">
                                        <select class='soflow' name='cb_jekel' id='cb_jekel' onchange="">
                                            <?php
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
                            </div>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Agama <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div style="margin-bottom:2px;">
                                        <select class='soflow' name='cb_agama' id='cb_agama' onchange="">
                                            <?php
                                            //echo "<option value='' selected>--Pilih--</option>";
                                            $query = "select agamaid, nama from hrd.agama order by 1";
                                            $tampiledu= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampiledu)) {
                                                $nnidagama=$du['agamaid'];
                                                $nnmagama=$du['nama'];

                                                if ($nnidagama==$pagama) 
                                                    echo "<option value='$nnidagama' selected>$nnmagama</option>";
                                                else
                                                    echo "<option value='$nnidagama'>$nnmagama</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Masuk </label>
                                <div class='col-md-6'>
                                    <div class='input-group date' id='mytgl02'>
                                        <input type="text" class="form-control" id='e_tglmasuk' name='e_tglmasuk' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglmasuk; ?>' Readonly>
                                        <span class='input-group-addon'>
                                            <span class='glyphicon glyphicon-calendar'></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Keluar </label>
                                <div class='col-md-6'>
                                    <div class='input-group date' id='mytgl03'>
                                        <input type="text" class="form-control" id='e_tglkeluar' name='e_tglkeluar' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglkeluar; ?>'>
                                        <span class='input-group-addon'>
                                            <span class='glyphicon glyphicon-calendar'></span>
                                        </span>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>


                <!--kanan-->
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pendidikan Terakhir <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div style="margin-bottom:2px;">
                                        <select class='soflow' name='cb_pendidikan' id='cb_pendidikan' onchange="">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            $query = "select eduId, nama from hrd.edu order by nama";
                                            $tampiledu= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampiledu)) {
                                                $nidedu=$du['eduId'];
                                                $nnmedu=$du['nama'];

                                                if ($nidedu==$pidedu) 
                                                    echo "<option value='$nidedu' selected>$nnmedu</option>";
                                                else
                                                    echo "<option value='$nidedu'>$nnmedu</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>



                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>*) Jabatan <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div style="margin-bottom:2px;">
                                        <select class='soflow' name='cb_jabatan' id='cb_jabatan' onchange="ShowDataCabangDariJBT()">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            $query = "select jabatanId, nama from hrd.jabatan order by 1";
                                            $tampiledu= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampiledu)) {
                                                $nnidjbt=$du['jabatanId'];
                                                $nnmjbt=$du['nama'];

                                                if ($nnidjbt==$pidjabatan) 
                                                    echo "<option value='$nnidjbt' selected>$nnidjbt - $nnmjbt</option>";
                                                else
                                                    echo "<option value='$nnidjbt'>$nnidjbt - $nnmjbt</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>



                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>*) Status <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div style="margin-bottom:2px;">
                                        <select class='soflow' name='cb_stskry' id='cb_stskry' onchange="">
                                            <?php
                                            echo "<option value='' selected>--Pilihan--</option>";
                                            if ($pstskry=="T") {
                                                echo "<option value='T' selected>Tetap</option>";
                                                echo "<option value='K'>Kontrak</option>";
                                            }elseif ($pstskry=="K") {
                                                echo "<option value='T'>Tetap</option>";
                                                echo "<option value='K' selected>Kontrak</option>";
                                            }else{
                                                echo "<option value='T'>Tetap</option>";
                                                echo "<option value='K'>Kontrak</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>*) Divisi 1 <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div style="margin-bottom:2px;">
                                        <select class='soflow' name='cb_divisi' id='cb_divisi' onchange="ShowDataUntukDivisi1()">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            if ($pstsact=="editdata") {
                                                
                                            }
                                            $query = "select divprodid, nama from MKT.divprod where DivProdid In ('EAGLE', 'PEACO', 'PIGEO', 'OTC', 'HO') order by 1";
                                            $tampiledu= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampiledu)) {
                                                $nniddiv=$du['divprodid'];
                                                $nnmdiv=$du['nama'];

                                                if ($nniddiv==$piddivisi) 
                                                    echo "<option value='$nniddiv' selected>$nnmdiv</option>";
                                                else
                                                    echo "<option value='$nniddiv'>$nnmdiv</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi 2 <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div style="margin-bottom:2px;">
                                        <select class='soflow' name='cb_divisi2' id='cb_divisi2' onchange="">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            if ($pstsact=="editdata" AND !empty($piddivisi)) {

                                                $query = "select divprodid, nama from MKT.divprod where DivProdid In ('EAGLE', 'PEACO', 'PIGEO', 'OTC', 'HO') order by 1";
                                                $tampiledu= mysqli_query($cnmy, $query);
                                                while ($du= mysqli_fetch_array($tampiledu)) {
                                                    $nniddiv=$du['divprodid'];
                                                    $nnmdiv=$du['nama'];

                                                    if ($nniddiv==$piddivisi2) 
                                                        echo "<option value='$nniddiv' selected>$nnmdiv</option>";
                                                    else
                                                        echo "<option value='$nniddiv'>$nnmdiv</option>";

                                                }

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>*) Cabang <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div style="margin-bottom:2px;">
                                        <select class='soflow' name='cb_cabang' id='cb_cabang' onchange="ShowDataArea()">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            if ($piddivisi=="OTC"){
                                                if ($jabatanid=="06" OR $jabatanid=="07" OR $jabatanid=="09" OR $jabatanid=="11" OR $jabatanid=="12" OR $jabatanid=="13" OR $jabatanid=="14" OR $jabatanid=="16" OR $jabatanid=="17" OR $jabatanid=="37") {
                                                    $query = "select icabangid, nama from MKT.icabang where aktif='Y' order by nama";
                                                }else{
                                                    $query = "select icabangid_o icabangid, nama from MKT.icabang_o where aktif='Y' order by nama";
                                                }
                                            }else{
                                                $query = "select icabangid, nama from MKT.icabang where aktif='Y' order by nama";
                                            }
                                            $tampiledu= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampiledu)) {
                                                $nnidcab=$du['icabangid'];
                                                $nnmcab=$du['nama'];

                                                if ($nnidcab==$pidcabang) 
                                                    echo "<option value='$nnidcab' selected>$nnmcab</option>";
                                                else
                                                    echo "<option value='$nnidcab'>$nnmcab</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div style="margin-bottom:2px;">
                                        <select class='soflow' name='cb_area' id='cb_area' onchange="">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            if ($pstsact=="editdata") {
                                                if ($piddivisi=="OTC"){
                                                    if ($jabatanid=="06" OR $jabatanid=="07" OR $jabatanid=="09" OR $jabatanid=="11" OR $jabatanid=="12" OR $jabatanid=="13" OR $jabatanid=="14" OR $jabatanid=="16" OR $jabatanid=="17" OR $jabatanid=="37") {
                                                        $query = "select areaid, nama from MKT.iarea where aktif='Y' AND icabangid='$pidcabang' order by nama";
                                                    }else{
                                                        $query = "select areaid_o areaid, nama from MKT.iarea_o where aktif='Y' AND icabangid_o='$pidcabang' order by nama";
                                                    }
                                                }else{
                                                    $query = "select areaid, nama from MKT.iarea where aktif='Y' AND icabangid='$pidcabang' order by nama";
                                                }
                                                $tampiledu= mysqli_query($cnmy, $query);
                                                while ($du= mysqli_fetch_array($tampiledu)) {
                                                    $nnidarea=$du['areaid'];
                                                    $nnmarea=$du['nama'];

                                                    if ($nnidarea==$pidarea) 
                                                        echo "<option value='$nnidarea' selected>$nnmarea</option>";
                                                    else
                                                        echo "<option value='$nnidarea'>$nnmarea</option>";

                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Bank <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_nmbank' name='e_nmbank' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmbank; ?>'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang Bank <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_cabbank' name='e_cabbank' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pcabangbank; ?>'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kota <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_nmkotabnk' name='e_nmkotabnk' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkotabnk; ?>'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Provinsi <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_provinsibnk' name='e_provinsibnk' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $prpovinbnk; ?>'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Pemilik Rekening <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_nmrekbank' name='e_nmrekbank' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmrekbank; ?>'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Rekening <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_norekbank' name='e_norekbank' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnorekbank; ?>'>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            
                
            </div>
            

            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                
                <!--kiri-->
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>
                            <b>Data Pasangan</b><hr/>
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Suami/Istri <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_pasnama' name='e_pasnama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ppasnama; ?>' >
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tempat Lahir <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_pastempat' name='e_pastempat' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ppastempatlahir; ?>' >
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Lahir </label>
                                <div class='col-md-9'>
                                    <div class='input-group date' id='mytgl04'>
                                        <input type="text" class="form-control" id='e_pastgllahir' name='e_pastgllahir' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ppastgllahir; ?>'>
                                        <span class='input-group-addon'>
                                            <span class='glyphicon glyphicon-calendar'></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pekerjaan <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_paskerja' name='e_paskerja' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ppastempatlahir; ?>' >
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!--kanan-->
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>
                            <b>Data Anak</b><hr/>
                            <table width="100%" border="1px">
                                <tr>
                                    <td>No.</td>
                                    <td>Nama</td>
                                    <td>Jenis Kelamin</td>
                                    <td>Tempat Lahir</td>
                                    <td>Tgl. Lahir</td>
                                </tr>

                                <tr>
                                    <td>1</td>
                                    <td><input type='text' id='e_anaknm1' name='e_anaknm1' class='' value='<?PHP echo $panaknm1; ?>' ></td>
                                    <td>
                                        <select name="cb_ankjekel1" id="cb_ankjekel1">
                                            <?PHP
                                            if ($panakjekel1=="L") {
                                                echo "<option value='L' selected>Laki-Laki</option>";
                                                echo "<option value='P'>Perempuan</option>";
                                            }elseif ($panakjekel1=="P") {
                                                echo "<option value='L'>Laki-Laki</option>";
                                                echo "<option value='P' selected>Perempuan</option>";
                                            }else{
                                                echo "<option value='L'>Laki-Laki</option>";
                                                echo "<option value='P'>Perempuan</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><input type='text' id='e_anaktempat1' name='e_anaktempat1' class='' value='<?PHP echo $panaktempat1; ?>' ></td>
                                    <td><input type='date' id='e_anaktgllahir1' name='e_anaktgllahir1' class='' value='<?PHP echo $panaktgllhir1; ?>' ></td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td><input type='text' id='e_anaknm2' name='e_anaknm2' class='' value='<?PHP echo $panaknm2; ?>' ></td>
                                    <td>
                                        <select name="cb_ankjekel2" id="cb_ankjekel2">
                                            <?PHP
                                            if ($panakjekel2=="L") {
                                                echo "<option value='L' selected>Laki-Laki</option>";
                                                echo "<option value='P'>Perempuan</option>";
                                            }elseif ($panakjekel2=="P") {
                                                echo "<option value='L'>Laki-Laki</option>";
                                                echo "<option value='P' selected>Perempuan</option>";
                                            }else{
                                                echo "<option value='L'>Laki-Laki</option>";
                                                echo "<option value='P'>Perempuan</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><input type='text' id='e_anaktempat2' name='e_anaktempat2' class='' value='<?PHP echo $panaktempat2; ?>' ></td>
                                    <td><input type='date' id='e_anaktgllahir2' name='e_anaktgllahir2' class='' value='<?PHP echo $panaktgllhir2; ?>' ></td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td><input type='text' id='e_anaknm3' name='e_anaknm3' class='' value='<?PHP echo $panaknm3; ?>' ></td>
                                    <td>
                                        <select name="cb_ankjekel3" id="cb_ankjekel3">
                                            <?PHP
                                            if ($panakjekel3=="L") {
                                                echo "<option value='L' selected>Laki-Laki</option>";
                                                echo "<option value='P'>Perempuan</option>";
                                            }elseif ($panakjekel3=="P") {
                                                echo "<option value='L'>Laki-Laki</option>";
                                                echo "<option value='P' selected>Perempuan</option>";
                                            }else{
                                                echo "<option value='L'>Laki-Laki</option>";
                                                echo "<option value='P'>Perempuan</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><input type='text' id='e_anaktempat3' name='e_anaktempat3' class='' value='<?PHP echo $panaktempat3; ?>' ></td>
                                    <td><input type='date' id='e_anaktgllahir3' name='e_anaktgllahir3' class='' value='<?PHP echo $panaktgllhir3; ?>' ></td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td><input type='text' id='e_anaknm4' name='e_anaknm4' class='' value='<?PHP echo $panaknm4; ?>' ></td>
                                    <td>
                                        <select name="cb_ankjekel4" id="cb_ankjekel4">
                                            <?PHP
                                            if ($panakjekel4=="L") {
                                                echo "<option value='L' selected>Laki-Laki</option>";
                                                echo "<option value='P'>Perempuan</option>";
                                            }elseif ($panakjekel4=="P") {
                                                echo "<option value='L'>Laki-Laki</option>";
                                                echo "<option value='P' selected>Perempuan</option>";
                                            }else{
                                                echo "<option value='L'>Laki-Laki</option>";
                                                echo "<option value='P'>Perempuan</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><input type='text' id='e_anaktempat4' name='e_anaktempat4' class='' value='<?PHP echo $panaktempat4; ?>' ></td>
                                    <td><input type='date' id='e_anaktgllahir4' name='e_anaktgllahir4' class='' value='<?PHP echo $panaktgllhir4; ?>' ></td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td><input type='text' id='e_anaknm5' name='e_anaknm5' class='' value='<?PHP echo $panaknm5; ?>' ></td>
                                    <td>
                                        <select name="cb_ankjekel5" id="cb_ankjekel5">
                                            <?PHP
                                            if ($panakjekel5=="L") {
                                                echo "<option value='L' selected>Laki-Laki</option>";
                                                echo "<option value='P'>Perempuan</option>";
                                            }elseif ($panakjekel5=="P") {
                                                echo "<option value='L'>Laki-Laki</option>";
                                                echo "<option value='P' selected>Perempuan</option>";
                                            }else{
                                                echo "<option value='L'>Laki-Laki</option>";
                                                echo "<option value='P'>Perempuan</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><input type='text' id='e_anaktempat5' name='e_anaktempat5' class='' value='<?PHP echo $panaktempat5; ?>' ></td>
                                    <td><input type='date' id='e_anaktgllahir5' name='e_anaktgllahir5' class='' value='<?PHP echo $panaktgllhir5; ?>' ></td>
                                </tr>



                            </table>
                        </div>
                    </div>
                </div>
                
                
            </div>



            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                
                <!--kiri-->
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>
                            
                            *) <u><b>Atasan</b></u>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_spv'>SPV <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <select class='form-control input-sm' id='e_spv' name='e_spv' onchange="ShowDataDM()">
                                        <?PHP
                                        $query ="select karyawanid, nama from hrd.karyawan where 1=1 "
                                                . " AND (aktif='Y' OR karyawanid='$atasanidspv') ";
                                        $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                        $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
                                        $query .=" ORDER BY nama";
                                        $sql=mysqli_query($cnmy, $query);
                                        echo "<option value=''>-- Pilihan --</option>";
                                        while ($Xt=mysqli_fetch_array($sql)){
                                            $xid=$Xt['karyawanid'];
                                            $xnama=$Xt['nama'];

                                            if ($xid==$atasanidspv)
                                                echo "<option value='$xid' selected>$xnama</option>";
                                            else
                                                echo "<option value='$xid'>$xnama</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><?PHP echo $lblnmdm; ?> <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <select class='form-control input-sm' id='e_dm' name='e_dm' onchange="ShowDataSM()">
                                        <?PHP
                                        //PilihKaryawanAktif("", "-- Pilihan --", $atasaniddm, "Y", $_SESSION['STSADMIN'], "", "", "Y", $_SESSION['IDCARD'], "", "", "", "", "");
                                        ?>
                                        <?PHP
                                        $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$atasaniddm') ";                                            
                                        $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                        $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
                                        $query .=" ORDER BY nama";

                                        $sql=mysqli_query($cnmy, $query);
                                        echo "<option value=''>-- Pilihan --</option>";
                                        while ($Xt=mysqli_fetch_array($sql)){
                                            $xid=$Xt['karyawanid'];
                                            $xnama=$Xt['nama'];

                                            if ($xid==$atasaniddm)
                                                echo "<option value='$xid' selected>$xnama</option>";
                                            else
                                                echo "<option value='$xid'>$xnama</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <select class='form-control input-sm' id='e_sm' name='e_sm' onchange="ShowDataGSM()">
                                        <?PHP
                                        //PilihKaryawanAktif("", "-- Pilihan --", $atasanidsm, "Y", $_SESSION['STSADMIN'], "", "", "Y", $_SESSION['IDCARD'], "", "", "", "", "");
                                        ?>
                                        <?PHP
                                        $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$atasanidsm')";
                                        $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                        $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
                                        $query .=" ORDER BY nama";

                                        $sql=mysqli_query($cnmy, $query);
                                        echo "<option value=''>-- Pilihan --</option>";
                                        while ($Xt=mysqli_fetch_array($sql)){
                                            $xid=$Xt['karyawanid'];
                                            $xnama=$Xt['nama'];

                                            if ($xid==$atasanidsm)
                                                echo "<option value='$xid' selected>$xnama</option>";
                                            else
                                                echo "<option value='$xid'>$xnama</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><?PHP echo $lblnmgsm; ?> <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <select class='form-control input-sm' id='e_gsm' name='e_gsm' onchange="">
                                        <?PHP
                                        //PilihKaryawanAktif("", "-- Pilihan --", $atasanidgsm, "Y", $_SESSION['STSADMIN'], "", "", "Y", $_SESSION['IDCARD'], "", "", "", "", "");
                                        ?>
                                        <?PHP
                                        $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$atasanidgsm')";
                                        $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                        $query .=" ORDER BY nama";

                                        $sql=mysqli_query($cnmy, $query);
                                        echo "<option value=''>-- Pilihan --</option>";
                                        while ($Xt=mysqli_fetch_array($sql)){
                                            $xid=$Xt['karyawanid'];
                                            $xnama=$Xt['nama'];

                                            if ($xid==$atasanidgsm)
                                                echo "<option value='$xid' selected>$xnama</option>";
                                            else
                                                echo "<option value='$xid'>$xnama</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>

                <!--kanan-->
                <div class='col-md-6 col-xs-12'>
                    <div class='x_panel'>
                        <div class='x_content form-horizontal form-label-left'>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> &nbsp;<span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div class="checkbox">
                                        *) Jika jabatan staf atau divisinya HO, minimal isi SPV nya...!!!
                                    </div>
                                </div>
                            </div>


                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> &nbsp;<span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div class="checkbox">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> &nbsp;<span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <div class="checkbox">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>

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
                    

            
            
        </form>
        
    </div>
    
</div>



<script>
    
    function ShowDataDM() {
    
        var ispv = document.getElementById('e_spv').value;
        if (ispv=="") {
        }else{
            $.ajax({
                type:"post",
                url:"module/mst_isidatakaryawan/viewdata.php?module=viewdatadmnya",
                data:"uspv="+ispv,
                success:function(data){
                    $("#e_dm").html(data);
                    ShowDataSM();
                }
            });
        }
    }
    
    function ShowDataSM() {
        var ispv = document.getElementById('e_spv').value;
        var idm = document.getElementById('e_dm').value;
        if (idm=="" && ispv=="") {
        }else{
            $.ajax({
                type:"post",
                url:"module/mst_isidatakaryawan/viewdata.php?module=viewdatasmnya",
                data:"udm="+idm+"&uspv="+ispv,
                success:function(data){
                    $("#e_sm").html(data);
                    ShowDataGSM();
                }
            });
        }
    }
    
    function ShowDataGSM() {
        var ispv = document.getElementById('e_spv').value;
        var idm = document.getElementById('e_dm').value;
        var ism = document.getElementById('e_sm').value;
        if (ism=="" && idm=="" && ispv=="") {
        }else{
            $.ajax({
                type:"post",
                url:"module/mst_isidatakaryawan/viewdata.php?module=viewdatagsmnya",
                data:"usm="+ism+"&udm="+idm+"&uspv="+ispv,
                success:function(data){
                    $("#e_gsm").html(data);
                }
            });
        }
    }
    
    
    function ShowDataDivisi2() {
    
        var idivisi1 = document.getElementById('cb_divisi').value;
        $.ajax({
            type:"post",
            url:"module/mst_isidatakaryawan/viewdata.php?module=viewdata2divisi",
            data:"udivisi1="+idivisi1,
            success:function(data){
                $("#cb_divisi2").html(data);
                //ShowDataDivisi3();
            }
        });
    }
                                    
    function ShowDataDivisi3() {
    
        var idivisi1 = document.getElementById('cb_divisi1').value;
        var idivisi2 = document.getElementById('cb_divisi2').value;
        $.ajax({
            type:"post",
            url:"module/mst_isidatakaryawan/viewdata.php?module=viewdata3divisi",
            data:"udivisi1="+idivisi1+"&udivisi2="+idivisi2,
            success:function(data){
                $("#cb_divisi3").html(data);
            }
        });
    }
    
</script>