<?php
session_start();
if ($_GET['module']=="viewdatacomboposting"){
    include "../../config/koneksimysqli.php";
    $tampil = mysqli_query($cnmy, "SELECT distinct kodeid, nama from dbmaster.brkd_otc where ifnull(subpost,'') = '$_POST[ukodesub]'");
    echo "<option value='' selected>-- Pilihan --</option>";
    while ($r=mysqli_fetch_array($tampil)){
        echo "<option value='$r[kodeid]'>$r[nama]</option>";
    }
}elseif ($_GET['module']=="viewmarcabang"){
    include "../../config/koneksimysqli.php";
    
    $karyawanId = $_POST['ukaryawan']; 
    $icabangid = $_POST['ucabang']; 
    $query = "select jabatanId from dbmaster.karyawan where karyawanId='$karyawanId'"; 	
    $result = mysqli_query($cnmy, $query);
    $records = mysqli_num_rows($result);
    $row = mysqli_fetch_array($result);
    $jabatanid = $row['jabatanId'];
    
    if ($icabangid=="0000000001") { //ho
        $query = "select karyawanId as mr_id, nama, areaId from dbmaster.karyawan order by nama"; 
    } else {
        if (($icabangid=="0000000030") or ($icabangid=='0000000031') or ($icabangid=='0000000032')){ // irian, ambon, ntt
            $query = "select karyawanId as mr_id, nama, areaId from dbmaster.karyawan where iCabangId='$icabangid' order by nama"; 
        } else {
            if (($jabatanid=="18") or ($jabatanid=="10")) { //spv,am
                    $query = "select karyawanId as mr_id, nama, areaId from dbmaster.karyawan where (atasanId='$karyawanId' or atasanId2='$karyawanId') order by nama";
            }
            if ($jabatanid=="08") { //dm
                    $query = "select karyawanId as mr_id, nama, areaId from dbmaster.karyawan where iCabangId='$icabangid' order by nama"; 
            }
            if ($jabatanid=="15") { // mr
                    $query = "select karyawanId as mr_id, nama, areaId from dbmaster.karyawan where karyawanId='$karyawanId'"; 
            }else{
                $query="";
            }
        }
    }
    
    if ($query=="") {
        echo "<option value='' selected>-- Pilihan --</option>";
    }else{
        $tampil = mysqli_query($cnmy, $query);
        echo "<option value='' selected>-- Pilihan --</option>";
        while ($r=mysqli_fetch_array($tampil)){
            echo "<option value='$r[mr_id]'>$r[nama]</option>";
        }
    }
}elseif ($_GET['module']=="viewdatacombokode"){
    include "../../config/koneksimysqli.php";

    $divprodid = $_POST['udiv']; 
    $query = "select kodeid,nama,divprodid from dbmaster.br_kode where "
    . " (divprodid='$divprodid' and br <> '') and (divprodid='$divprodid' and br<>'N') order by nama"; 
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $kodeid  = $row['kodeid'];
        $nama = $row['nama'];
        echo "<option value=\"$kodeid\">$nama</option>";
    }
}elseif ($_GET['module']=="viewdatacombokodenon"){
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $kodeidcoa="";
    if (!empty($_POST['ucoa']))
        $kodeidcoa= getfieldcnmy("select kodeid as lcfields from dbmaster.v_coa where COA4='$_POST[ucoa]'");
    
    $divprodid = $_POST['udiv'];
    $query = "select kodeid,nama,divprodid from dbmaster.br_kode where (divprodid='$divprodid' and br = '')  "
            . " and (divprodid='$divprodid' and br<>'N') order by nama";
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    echo "<option value='' selected>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $kodeid  = $row['kodeid'];
        $nama = $row['nama'];
        if ($kodeid==$kodeidcoa)
            echo "<option value=\"$kodeid\" selected>$nama</option>";
        else
            echo "<option value=\"$kodeid\">$nama</option>";
    }
}elseif ($_GET['module']=="viewdatacabangkaryawan"){
    include "../../config/koneksimysqli.php";
    
    $karyawanId = $_POST['umr']; 
    $query = "select karyawan.iCabangId, cabang.nama from dbmaster.karyawan as karyawan join dbmaster.icabang as cabang on "
            . " karyawan.icabangid=cabang.icabangid where karyawanId='$karyawanId'"; 
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    echo "<option value='' selected>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $kodeid  = $row['iCabangId'];
        $nama = $row['nama'];
        echo "<option value=\"$kodeid\" selected>$nama</option>";
    }
}elseif ($_GET['module']=="viewdatacombocoa"){
    include "../../config/koneksimysqli.php";
    
    $cnmy=$cnmy;
    
    $sql="select distinct COA4 from dbmaster.v_coa_wewenang where karyawanId='$_SESSION[IDCARD]'";//DCC & DSS
    $tampil=mysqli_query($cnmy, $sql);
    $ketemu=mysqli_num_rows($tampil);
    $filcoa="";
    if ($ketemu>0) {
        while ($ar=  mysqli_fetch_array($tampil)) {
            $filcoa .= "'".$ar['COA4']."',";
        }
        if (!empty($filcoa)) {
            $filcoa=" AND COA4 in (".substr($filcoa, 0, -1).")";
        }
    }
    
    
    
    $divprodid = $_POST['udiv'];
    
    $query = "SELECT COA4, NAMA4 FROM dbmaster.v_coa where (DIVISI='$divprodid' or ifnull(DIVISI,'')='') AND "
            . "( ((divprodid='$divprodid' and br = '') and (divprodid='$divprodid' and br<>'N'))   or ifnull(kodeid,'')='') $filcoa order by COA4";
    
    $tampil=mysqli_query($cnmy, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
    }
    
}elseif ($_GET['module']=="caridatapajak"){
    
    
    include "../../config/koneksimysqli.php";
    $act="editdata";
    $hari_ini = date("Y-m-d");
    
    $pidinput=$_POST['uid'];
    
    $sql = "SELECT * FROM dbmaster.t_br0_pajak WHERE idinput='$pidinput'";
    $tampil= mysqli_query($cnmy, $sql);
    $row= mysqli_fetch_array($tampil);
    
    $pjmldpp=$row['dpp'];
    $pjmlppn=$row['ppn'];
    $pjmlrpppn=$row['ppn_rp'];
    $pjnspph=$row['pph_jns'];
    $pjmlpph=$row['pph'];
    $pjmlrppph=$row['pph_rp'];
    $pjmlbulat=$row['pembulatan'];
    $pjmlmaterai=$row['materai_rp'];
    $ptglfakturpajak="";
    if (!empty($row['tgl_fp']) AND $row['tgl_fp']<>"0000-00-00") $ptglfakturpajak = date('d/m/Y', strtotime($row['tgl_fp']));
    $pnoseripajak=$row['noseri'];
    $pkenapajak=$row['nama_pengusaha'];
    $prpjumlahjasa=$row['jasa_rp'];
    $pjumlahrpusul=$row['jumlah'];
    
    
    $pchkjasa="";
    $pchkatrika="checked";
?>

    <script src="js/inputmask.js"></script>
    
    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>IDINPUT <span class='required'></span></label>
        <div class='col-md-4'>
            <input type='text' id='e_idinput' name='e_idinput' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pajak <span class='required'></span></label>
        <div class='col-md-4'>
            <select class='form-control input-sm' id='cb_pajak' name='cb_pajak' onchange="">
                <option value='Y' selected>Y</option>
                <option value='N'>N</option>
            </select>
        </div>
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Pengusaha Kena Pajak <span class='required'></span></label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <input type='text' id='e_kenapajak' name='e_kenapajak' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pkenapajak; ?>'>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">No Seri Faktur Pajak <span class='required'></span></label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <input type='text' id='e_noserifp' name='e_noserifp' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pnoseripajak; ?>'>
        </div>
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Tgl Faktur Pajak </label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <div class='input-group date' id='mytgl01'>
                <input type="text" class="form-control" id='mytgl05' name='e_tglpajak' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglfakturpajak; ?>'>
                <span class='input-group-addon'>
                    <span class='glyphicon glyphicon-calendar'></span>
                </span>
            </div>

        </div>
    </div>



    <!--- untuk jasa -->
    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">&nbsp;<span class='required'></span></label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <div hidden>
                <input type="checkbox" value="jasa" id="chk_jasa" name="chk_jasa" onclick="cekBoxPilihDPP('chk_jasa')" <?PHP echo $pchkjasa; ?>> DPP Dari Jumlah Awal 
                <br/>
            </div>
            <input type="checkbox" value="atrika" id="chk_atrika" name="chk_atrika" onclick="cekBoxPilihDPP('chk_atrika')" <?PHP echo $pchkatrika; ?>> DPP Khusus
        </div>
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Jumlah Awal (Rp.) <span clasJumlah Awal (Rp.)s='required'></span></label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <input type='text' id='e_rpjmljasa' name='e_rpjmljasa' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpjumlahjasa; ?>' onblur="HitungJumlahDPP()">
        </div><!--disabled='disabled'-->
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">DPP (Rp.) <span class='required'></span></label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <input type='text' id='e_jmldpp' name='e_jmldpp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmldpp; ?>' onblur="HitungJumlah()">
        </div><!--disabled='disabled'-->
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">PPN (%) <span class='required'></span></label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <input type='text' id='e_jmlppn' name='e_jmlppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlppn; ?>' onblur="HitungPPN()">
            <input type='hidden' id='e_jmlrpppn' name='e_jmlrpppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrpppn; ?>' Readonly>
        </div><!--disabled='disabled'-->
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">PPH <span class='required'></span></label>
        <div class='col-xs-9'>
            <div style="margin-bottom:2px;">
                <select class='soflow' name='cb_pph' id='cb_pph' onchange="ShowPPH()">
                    <?php
                    if ($pjnspph=="pph21") {
                        echo "<option value=''></option>";
                        echo "<option value='pph21' selected>PPH21</option>";
                        echo "<option value='pph23'>PPH23</option>";
                    }elseif ($pjnspph=="pph23") {
                        echo "<option value=''></option>";
                        echo "<option value='pph21'>PPH21</option>";
                        echo "<option value='pph23' selected>PPH23</option>";
                    }else{
                        echo "<option value='' selected></option>";
                        echo "<option value='pph21'>PPH21</option>";
                        echo "<option value='pph23'>PPH23</option>";
                    }
                    ?>
                </select>
                <input type='hidden' id='e_jmlpph' name='e_jmlpph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlpph; ?>' readonly>
                <input type='hidden' id='e_jmlrppph' name='e_jmlrppph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrppph; ?>' Readonly>
            </div>
        </div>
    </div>



    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Pembulatan <span class='required'></span></label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <input type='text' id='e_jmlbulat' name='e_jmlbulat' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlbulat; ?>' onblur="HitungJumlahUsulan()">
        </div><!--disabled='disabled'-->
    </div>


    <div hidden>
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Biaya Materai (Rp.) <span class='required'></span></label>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <input type='text' id='e_jmlmaterai' name='e_jmlmaterai' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlmaterai; ?>' onblur="HitungJumlahUsulan()">
            </div><!--disabled='disabled'-->
        </div>
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlahrpusul; ?>'>
        </div><!--disabled='disabled'-->
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
        <div class='col-xs-9'>
            <div class="checkbox">
                <button type='button' id='nm_btn_save' class='btn btn-success' onclick='disp_confirm_pajak("Simpan ?", "<?PHP echo $act; ?>")'>Update</button>
            </div>
        </div>
    </div>
    
    <script>
        $('#mytgl01, #mytgl02').datetimepicker({
            ignoreReadonly: true,
            allowInputToggle: true,
            format: 'DD/MM/YYYY'
        });
    </script>
<?PHP

}elseif ($_GET['module']=="x"){
}elseif ($_GET['module']=="xx"){
}

