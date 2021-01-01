<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
if ($_GET['module']=="viewdataposting"){
    include "../../config/koneksimysqli.php";
    
    $subposting = $_POST['usubpost'];
    $pgrpidkode=$_POST['ugrpid'];
    
    if (empty($pgrpidkode)) {
        $query = "select distinct kodeid as kodeid, nama as nama from hrd.brkd_otc "
                . " where subpost='$subposting' "
                . " and ifnull(aktif,'')<>'N' order by nama ";
    }else{
        $query = "SELECT distinct a.kodeid as kodeid, a.nama as nama FROM hrd.brkd_otc as a "
                . " JOIN hrd.brkd_otc_d as b on a.kodeid=b.kodeid "
                . " WHERE b.gkode='$pgrpidkode' AND ifnull(a.subpost,'') = '$subposting' ";
        $query .=" order by a.nama";
    }
    
    $tampil=mysqli_query($cnmy, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[kodeid]'>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdatacoa"){
    include "../../config/koneksimysqli.php";
    
    $subposting = $_POST['usubpost'];
	
    if ($subposting=="01")
        $query = "select distinct COA4 from dbmaster.posting_coa where 1=1 AND subpost = '$subposting'";
    else
        $query = "select distinct COA4 from dbmaster.posting_coa where 1=1 AND subpost = $subposting";
	
    $tampil=mysqli_query($cnmy, $query);
    $x=mysqli_fetch_array($tampil);
    $coa4=$x['COA4'];
        
    
    
    $posting = $_POST['upost'];
    if (!empty($posting)) {
        $query = "select distinct COA4 from dbmaster.posting_coa where 1=1 AND (kodeid=$posting AND subpost = $subposting)";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu=  mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $x=mysqli_fetch_array($tampil);
            $coa4=$x['COA4'];
        }
    }
    
    
    $query = "select distinct COA4, NAMA4 from dbmaster.v_coa_all where (DIVISI='OTC' or DIVISI='OTHER' or DIVISI='OTHERS' or ifnull(DIVISI,'')='' OR COA4='$coa4') order by NAMA4";
    
    $tampil=mysqli_query($cnmy, $query);
    
    echo "<option value='' >-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        if ($a['COA4']==$coa4)
            echo "<option value='$a[COA4]' selected>$a[COA4] - $a[NAMA4]</option>";
        else
            echo "<option value='$a[COA4]'>$a[COA4] - $a[NAMA4]</option>";
    }
}elseif ($_GET['module']=="viewdataareacab"){
    include "../../config/koneksimysqli.php";
    
    $cabang = $_POST['ucab'];
	if ($cabang=="JKT_RETAIL") {
		$cabang="0000000007";
	}
	
    $query = "select areaid_o, nama, aktif from MKT.iarea_o where icabangid_o='$cabang' order by nama";
    
    $tampil=mysqli_query($cnmy, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[areaid_o]'>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdataareacabbytoko"){
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $cabang = $_POST['ucab'];
	if ($cabang=="JKT_RETAIL") {
		$cabang="0000000007";
	}
	
    $tokoo = $_POST['utoko'];
    
    $areatoko= getfieldcnmy("select areaid_o as lcfields from MKT.icust_o where icustid_o='$tokoo' and icabangid_o='$cabang'");
    
    $query = "select areaid_o, nama, aktif from MKT.iarea_o where icabangid_o='$cabang' order by nama";
    $tampil=mysqli_query($cnmy, $query);
    echo "<option value='' >-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        if ($a['areaid_o']==$areatoko)
            echo "<option value='$a[areaid_o]' selected>$a[nama]</option>";
        else
            echo "<option value='$a[areaid_o]'>$a[nama]</option>";
    }
    
}elseif ($_GET['module']=="viewdatatokocab"){
    include "../../config/koneksimysqli.php";
    
    $cabang = $_POST['ucab'];
	if ($cabang=="JKT_RETAIL") {
		$cabang="0000000007";
		//$_POST['uarea']="0000000012";
	}
	
    $area="";
    if (isset($_POST['uarea'])) {
        if (!empty($_POST['uarea']))
            $area=" and areaid_o='$_POST[uarea]' ";
    }
    
    $query = "select icustid_o, nama from MKT.icust_o where icabangid_o='$cabang' $area order by nama";
    
    $tampil=mysqli_query($cnmy, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[icustid_o]'>$a[nama] ($a[icustid_o])</option>";
    }
}elseif ($_GET['module']=="viewdatatokoinput"){
    include "../../config/koneksimysqli.php";
    $cabang = $_POST['ucab'];
    $area = $_POST['uarea'];
    $tokoo = $_POST['utoko'];
    $tgl = $_POST['utgl'];
    $idnya = $_POST['uid'];
    $filid = "";
    if (!empty($idnya)) $filid = " AND brotcid <> '$idnya' ";
    $ptgl="";
    if (!empty($tgl)) {
        $datetrm = str_replace('/', '-', $tgl);
        $ptgl= date("Ym", strtotime($datetrm));
    }
        
    $query = "select * from hrd.br_otc_ext 
        where icabangid_o='$cabang' and areaid_o='$area' and icustid_o='$tokoo'
        and '$ptgl' BETWEEN DATE_FORMAT(tglmulaisewa,'%Y%m') and DATE_FORMAT(DATE_ADD(tglmulaisewa, INTERVAL periode-1 MONTH),'%Y%m') $filid";
    $tampil=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $a=mysqli_fetch_array($tampil);
        $noid = $a['brotcid'];
        $periode = $a['periode'];
        $mulai = date("d F Y", strtotime($a['tglmulaisewa']));
        $query = "select * from hrd.br_otc where brOtcId='$noid'";
        $tampil=mysqli_query($cnmy, $query);
        $z=mysqli_fetch_array($tampil);
        $jumlah=(double)$z['jumlah']/(double)$periode;
        $jumlah=number_format($jumlah,0,",",",");
        $total=number_format($z['jumlah'],0,",",",");
        echo "<table>";
        echo "<tr style='background-color:red; color:#ffffff;'><td colspan=3><b>Masih Dalam Periode Pengajuan</b></td></tr>";
        echo "<tr><td>ID</td><td> &nbsp; : &nbsp; </td><td>$noid</td></tr>";
        echo "<tr><td>Periode/bulan</td><td> &nbsp; : &nbsp; </td><td>$periode</td></tr>";
        echo "<tr><td>Mulai</td><td> &nbsp; : &nbsp; </td><td>$mulai</td></tr>";
        echo "<tr><td>biaya/bulan</td><td> &nbsp; : &nbsp; </td><td>$jumlah</td></tr>";
        echo "<tr><td>Total Rp.</td><td> &nbsp; : &nbsp; </td><td>$total</td></tr>";
        echo "</table>";
    }else{
        $query = "select * from hrd.br_otc_ext where CONCAT(icabangid_o, areaid_o, DATE_FORMAT(tglmulaisewa,'%Y%m')) in (
                select CONCAT(icabangid_o, areaid_o, DATE_FORMAT(max(tglmulaisewa),'%Y%m')) terakhir from hrd.br_otc_ext 
                where icabangid_o='$cabang' and areaid_o='$area' and icustid_o='$tokoo' $filid 
                )";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $a=mysqli_fetch_array($tampil);
            $noid = $a['brotcid'];
            $periode = $a['periode'];
            $mulai = date("d F Y", strtotime($a['tglmulaisewa']));
            $query = "select * from hrd.br_otc where brOtcId='$noid'";
            $tampil=mysqli_query($cnmy, $query);
            $z=mysqli_fetch_array($tampil);
            $jumlah=(double)$z['jumlah']/(double)$periode;
            $jumlah=number_format($jumlah,0,",",",");
            $total=number_format($z['jumlah'],0,",",",");
            echo "<table>";
            echo "<tr><td colspan=3><b><u>Terakhir Pengajuan</u></b></td></tr>";
            echo "<tr><td>ID</td><td> &nbsp; : &nbsp; </td><td>$noid</td></tr>";
            echo "<tr><td>Periode/bulan</td><td> &nbsp; : &nbsp; </td><td>$periode</td></tr>";
            echo "<tr><td>Mulai</td><td> &nbsp; : &nbsp; </td><td>$mulai</td></tr>";
            echo "<tr><td>Rp./bulan</td><td> &nbsp; : &nbsp; </td><td>$jumlah</td></tr>";
            echo "<tr><td>Total Rp.</td><td> &nbsp; : &nbsp; </td><td>$total</td></tr>";
            echo "</table>";
        }else{
            echo "";
        }
    }
}elseif ($_GET['module']=="caridatapajak"){
    
    
    include "../../config/koneksimysqli.php";
    $act="editdata";
    $hari_ini = date("Y-m-d");
    
    $pidinput=$_POST['uid'];
    
    $sql = "SELECT * FROM dbmaster.t_br_otc_pajak WHERE idinput='$pidinput'";
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
    
    $pjenisdpp=$row['jenis_dpp'];
    
    $pbuka_js="hidden";
    
    $pchkjasa="";
    $pchkatrika="";
    
    $pchkjasa="";
    $pchkatrika="";
    if ($pjenisdpp=="A") {
        $pchkjasa="checked";
    }elseif ($pjenisdpp=="B") {
        $pchkatrika="checked";
        $pbuka_js="";
    }
?>

    <script src="js/inputmask.js"></script>
    
    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>IDINPUT <span class='required'></span></label>
        <div class='col-md-4'>
            <input type='text' id='e_idinput' name='e_idinput' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
            <input type='text' id='e_jumlahminta3' name='e_jumlahminta3' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlahrpusul; ?>' Readonly>
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
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">&nbsp;<span class='required'></span></label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <div hidden>
                <input type="checkbox" value="jasa" id="chk_jasa" name="chk_jasa" onclick="cekBoxPilihDPP('chk_jasa')" <?PHP echo $pchkjasa; ?>> DPP Dari Jumlah Awal 
                <br/>
            </div>
            <input type="checkbox" value="atrika" id="chk_atrika" name="chk_atrika" onclick="cekBoxPilihDPP('chk_atrika')" <?PHP echo $pchkatrika; ?>> DPP Khusus
        </div>
    </div>

    <div <?PHP echo $pbuka_js; ?> id="n_pajakjasa">
        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Jumlah Awal (Rp.) <span clasJumlah Awal (Rp.)s='required'></span></label>
            <div class='col-md-6 col-sm-6 col-xs-12'>
                <input type='text' id='e_rpjmljasa' name='e_rpjmljasa' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpjumlahjasa; ?>' onblur="HitungJumlahDPP()">
            </div><!--disabled='disabled'-->
        </div>
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



    <div  class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:000;">Pembulatan <span class='required'></span></label>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <input type='text' id='e_jmlbulat' name='e_jmlbulat' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlbulat; ?>' onblur="HitungJumlahUsulan()">
        </div><!--disabled='disabled'-->
    </div>


    <div >
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
    
}elseif ($_GET['module']=="viewdatapostingsubgrp"){
    include "../../config/koneksimysqli.php";
    $pgrpidkode=$_POST['ugrpid'];
    if (empty($pgrpidkode)) {
        $query = "select distinct subpost as subpost, nmsubpost as nmsubpost from hrd.brkd_otc where ifnull(subpost,'') <> '' order by nmsubpost";
    }else{
        $query = "SELECT distinct a.subpost as subpost, a.nmsubpost as nmsubpost FROM hrd.brkd_otc as a "
                . " JOIN hrd.brkd_otc_d as b on a.kodeid=b.kodeid "
                . " WHERE b.gkode='$pgrpidkode' AND ifnull(a.subpost,'') <> '' ";
        $query .=" order by a.nmsubpost";
    }
    $tampil=mysqli_query($cnmy, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){ 
        echo "<option value='$a[subpost]'>$a[nmsubpost]</option>";
    }                   
    mysqli_close($cnmy);
    
    
}elseif ($_GET['module']=="xxx"){
}

?>
