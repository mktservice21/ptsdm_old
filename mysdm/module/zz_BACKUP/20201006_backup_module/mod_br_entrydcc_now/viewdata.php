<?php
if ($_GET['module']=="viewdatacomboposting"){
    include "../../config/koneksimysqli_it.php";
    $tampil = mysqli_query($cnit, "SELECT distinct kodeid, nama from hrd.brkd_otc where ifnull(subpost,'') = '$_POST[ukodesub]'");
    echo "<option value='' selected>-- Pilihan --</option>";
    while ($r=mysqli_fetch_array($tampil)){
        echo "<option value='$r[kodeid]'>$r[nama]</option>";
    }
}elseif ($_GET['module']=="viewmarcabang"){
    include "../../config/koneksimysqli_it.php";
    
    $karyawanId = $_POST['ukaryawan']; 
    $icabangid = $_POST['ucabang']; 
    $query = "select jabatanId from hrd.karyawan where karyawanId='$karyawanId'"; 	
    $result = mysqli_query($cnit, $query);
    $records = mysqli_num_rows($result);
    $row = mysqli_fetch_array($result);
    $jabatanid = $row['jabatanId'];
    
    if ($icabangid=="0000000001") { //ho
        $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan order by nama"; 
    } else {
        if (($icabangid=="0000000030") or ($icabangid=='0000000031') or ($icabangid=='0000000032')){ // irian, ambon, ntt
            $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where iCabangId='$icabangid' order by nama";
        } else {
            if (($jabatanid=="18") or ($jabatanid=="10")) { //spv,am
                    $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where (atasanId='$karyawanId' or atasanId2='$karyawanId') order by nama";
            }
            if ($jabatanid=="08") { //dm
                    $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where iCabangId='$icabangid' order by nama"; 
            }
            if ($jabatanid=="15") { // mr
                    $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where karyawanId='$karyawanId'"; 
            }
        }
    }
    
    if ($query=="") {
        echo "<option value='' selected>-- Pilihan --</option>";
    }else{
        $tampil = mysqli_query($cnit, $query);
        echo "<option value='' selected>-- Pilihan --</option>";
        while ($r=mysqli_fetch_array($tampil)){
            echo "<option value='$r[mr_id]'>$r[nama]</option>";
        }
    }
}elseif ($_GET['module']=="viewdatacombokode"){
    include "../../config/koneksimysqli_it.php";

    $divprodid = $_POST['udiv']; 
    $query = "select kodeid,nama,divprodid from hrd.br_kode where "
    . " (divprodid='$divprodid' and br <> '') and (divprodid='$divprodid' and br<>'N') order by nama"; 
    $result = mysqli_query($cnit, $query); 
    $record = mysqli_num_rows($result);
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $kodeid  = $row['kodeid'];
        $nama = $row['nama'];
        echo "<option value=\"$kodeid\">$nama</option>";
    }
}elseif ($_GET['module']=="viewdatalevel2"){
    include "../../config/koneksimysqli_it.php";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnit, "SELECT COA2, NAMA2 FROM dbmaster.coa_level2 where COA1='$_POST[ulevel1]' order by COA2");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[COA2]'>$a[NAMA2]</option>";
    }
}elseif ($_GET['module']=="viewdatalevel3"){
    include "../../config/koneksimysqli_it.php";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnit, "SELECT COA3, NAMA3 FROM dbmaster.coa_level3 where COA2='$_POST[ulevel2]' order by COA3");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[COA3]'>$a[NAMA3]</option>";
    }
}elseif ($_GET['module']=="viewdatalevel4"){
    include "../../config/koneksimysqli_it.php";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnit, "SELECT COA4, NAMA4 FROM dbmaster.coa_level4 where COA3='$_POST[ulevel3]' order by COA4");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
    }
}elseif ($_GET['module']=="viewdatalevel5"){
    include "../../config/koneksimysqli_it.php";
    echo "<option value='' selected>-- Pilihan --</option>";
    $tampil=mysqli_query($cnit, "SELECT COA_KODE, COA_NAMA FROM dbmaster.coa where COA4='$_POST[ulevel4]' order by COA_KODE");
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[COA_KODE]'>$a[COA_NAMA]</option>";
    }
}elseif ($_GET['module']=="viewdatakaryawancabang"){
    include "../../config/koneksimysqli_it.php";
    $icabangid = $_POST['uicabang']; 
    if (($icabangid=='30') or ($icabangid=='31') or ($icabangid=='0000000032')) {
            $query = "select karyawanId,nama,jabatanid,icabangid from hrd.karyawan where (karyawanId='0000000154' or karyawanId='0000000159') AND aktif = 'Y' order by nama"; 
    } else {
            $query = "select karyawanId,nama,jabatanid,icabangid from hrd.karyawan where icabangid='$icabangid' AND aktif = 'Y' order by nama"; 
    }
    $tampil=mysqli_query($cnit, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[karyawanId]'>$a[nama]</option>";
    }
    
    
}elseif ($_GET['module']=="viewdoktermr"){
    include "../../config/koneksimysqli_it.php";
    
    $mr_id = $_POST['umr']; 
    $icabangid = $_POST['ucab']; 

	
    $filter_kry_dok=" and karyawan.karyawanId='$mr_id' ";
    if (isset($_POST['umr_car'])) {
        $nkaryawan_pil=$_POST['umr_car'];
        $filter_kry_dok=" AND ( karyawan.karyawanId='$mr_id' OR karyawan.karyawanId='$nkaryawan_pil' ) ";
    }
	
	
    $query = "select iCabangId from hrd.karyawan where iCabangId='$icabangid'"; 
    $result = mysqli_query($cnit, $query); 
    $record = mysqli_num_rows($result); 
    if ($icabangid=="0000000001") {
        $query = "select distinct (mr_dokt.dokterId),CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
                          from hrd.mr_dokt as mr_dokt 
                          join hrd.dokter as dokter on mr_dokt.dokterId=dokter.dokterId
                          where mr_dokt.aktif <> 'N' and dokter.nama<>''
                          order by nama"; 
    } else {
        $query = "select dokter.dokterId, CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
                          FROM hrd.mr_dokt as mr_dokt 
                          join hrd.karyawan as karyawan on mr_dokt.karyawanId=karyawan.karyawanId
                          join hrd.dokter as dokter on mr_dokt.dokterId=dokter.dokterId
                          where mr_dokt.aktif <> 'N' $filter_kry_dok and dokter.nama <> ''
                          order by dokter.nama";
    }
    $tampil=mysqli_query($cnit, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
    echo "<option value='$a[dokterId]'>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdatacombocoa"){
    include "../../config/koneksimysqli.php";
    $cnit=$cnmy;
    $divprodid = $_POST['udiv'];
    $queryx = " AND ifnull(kodeid,'') (select distinct ifnull(kodeid,'') as kodeid from hrd.br_kode where "
        . " (divprodid='$divprodid' and br <> '') and (divprodid='$divprodid' and br<>'N'))";
    
    $query = "SELECT COA4, NAMA4 FROM dbmaster.v_coa where DIVISI='$divprodid' AND "
            . "(divprodid='$divprodid' and br <> '') and (divprodid='$divprodid' and br<>'N') order by COA4";
    $tampil=mysqli_query($cnit, $query);
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

}elseif ($_GET['module']=="viewdatalevel2xxx"){
}

