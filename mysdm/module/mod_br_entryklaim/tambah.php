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


<?PHP
$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pact=$_GET['act'];


$hari_ini = date("Y-m-d");
$tglinput = date('d F Y', strtotime($hari_ini));
$tglinput = date('d/m/Y', strtotime($hari_ini));
$tgltrans="";
$iduser=$_SESSION['USERID']; 
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP'];
$padmkhusus=$_SESSION['ADMINKHUSUS'];

$pidklaim="";
$piddistrb="";
$paktivitas1="";
$paktivitas2="";


$pjumlah=0;
$prealisasi="";
$pnoslip="";
$plampiran="";

$pkodeid="";
$pcoa="755-31";//canary
//$pcoa="751-31";//EAGLE
//$pcoa="752-31";//PIGEO
//$pcoa="753-31";//PEACO
//$pcoa="754-31";//OTC
$pdivpengajuan="CAN";



$pjnspajak = "N";
$pjmldpp=0;
$pjmlppn=10;
$pjmlrpppn=0;
$pjnspph="";
$pjmlpph=5;
$pjmlrppph=0;
$pjmlbulat=0;
$ptglfakturpajak = date('d/m/Y', strtotime($hari_ini));
$pnoseripajak="";
$pkenapajak="";


$act="input";
if ($pact=="editdata"){
    $act="update";
    $pidklaim=$_GET['id'];
    
    $edit = mysqli_query($cnmy, "SELECT * FROM hrd.klaim WHERE klaimId='$pidklaim'");
    $r    = mysqli_fetch_array($edit);
    
    $tglinput = date('d F Y', strtotime($r['tgl']));
    $tglinput = date('d/m/Y', strtotime($r['tgl']));
    if (empty($r['tgltrans']) OR $r['tgltrans']=="0000-00-00"){
        
    }else{
        $tgltrans = date('d F Y', strtotime($r['tgltrans']));
        $tgltrans = date('d/m/Y', strtotime($r['tgltrans']));
    }
    
    $idajukan=$r['karyawanid'];
    $piddistrb=$r['distid']; 
    $pjumlah=$r['jumlah'];
    $prealisasi=$r['realisasi1'];
    $pnoslip=$r['noslip'];
    $paktivitas1=$r['aktivitas1'];
    $paktivitas2=$r['aktivitas2'];
    if ($r['lampiran']=="Y") $plampiran="checked";
    
    
    $pdivpengajuan=$r['pengajuan'];
    $pcoa=$r['COA4'];
    
    if ($pdivpengajuan=="ETH") {   
        $pdivpengajuan="CAN";
    }
    
    
    $pjnspajak=$r['pajak'];
    $pkenapajak=$r['nama_pengusaha'];
    $pnoseripajak=$r['noseri'];
    if (!empty($r['tgl_fp']) AND $r['tgl_fp']<>"0000-00-00")
        $ptglfakturpajak = date('d/m/Y', strtotime($r['tgl_fp']));

    if ($r['tgl_fp']=="0000-00-00") $ptglfakturpajak = "";

    $pjmldpp=$r['dpp'];
    $pjmlppn=$r['ppn'];
    $pjmlrpppn=$r['ppn_rp'];

    $pjnspph=$r['pph_jns'];
    $pjmlpph=$r['pph'];
    $pjmlrppph=$r['pph_rp'];
    $pjmlbulat=$r['pembulatan'];
    
    
    
}
?>


<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>
<div class='modal fade' id='myModal2' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_klaimid").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
        
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                  
                    
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-9'>
                                        <input type='text' id='e_klaimid' name='e_klaimid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidklaim; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Tanggal </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='mytgl01' name='e_tglinput' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tglinput; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_idkaryawan' name='e_idkaryawan'>
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $query = "SELECT DISTINCT karyawanId as karyawanid, nama as nama FROM dbmaster.karyawan WHERE "
                                                        . " IFNULL(tglkeluar,'0000-00-00') = '0000-00-00' ";
                                                $query .= " AND IFNULL(aktif,'')='Y' ";
                                                $query .=" AND (IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00') ";
                                                $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S', 'BKS-')  "
                                                        . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                        . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                        . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                        . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY', 'LOGIN ', 'SMGTO-') ";
                                                $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                                $query .=" order by nama, karyawanId";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    $pkryid=$a['karyawanid'];
                                                    $pkrynm=$a['nama'];
                                                    if ($pkryid==$idajukan)
                                                        echo "<option value='$pkryid' selected>$pkrynm ($pkryid)</option>";
                                                    else
                                                        echo "<option value='$pkryid'>$pkrynm ($pkryid)</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_iddist'>Distributor <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_iddist' name='e_iddist'>
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $tampil = mysqli_query($cnmy, "SELECT distinct Distid as distid, nama as nama, alamat1 from dbmaster.distrib0 order by nama");
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    $ndistid=$a['distid'];
                                                    $ndistnm=$a['nama'];
                                                    if ($ndistid==$piddistrb)
                                                        echo "<option value='$ndistid' selected>$ndistnm ($ndistid)</option>";
                                                    else
                                                        echo "<option value='$ndistid'>$ndistnm ($ndistid)</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Aktivitas <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Aktivitas'><?PHP echo $paktivitas1; ?></textarea>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas2'> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas2' name='e_aktivitas2' rows='3' placeholder='Keterangan Detail'><?PHP echo $paktivitas2; ?></textarea>
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Pajak <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div style="margin-bottom:2px;">
                                            <select class='soflow' name='cb_pajak' id='cb_pajak' onchange="ShowPajak()">
                                                <?php
                                                if ($pjnspajak=="Y") {
                                                    echo "<option value='N'>N</option>";
                                                    echo "<option value='Y' selected>Y</option>";
                                                }else{
                                                    echo "<option value='N' selected>N</option>";
                                                    echo "<option value='Y'>Y</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="n_pajak1">
                                    
                                    
                                    <div  class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Pengusaha Kena Pajak <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_kenapajak' name='e_kenapajak' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pkenapajak; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">No Seri Faktur Pajak <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_noserifp' name='e_noserifp' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pnoseripajak; ?>'>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Tgl Faktur Pajak </label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <div class='input-group date' id='mytgl01'>
                                                <input type="text" class="form-control" id='mytgl05' name='e_tglpajak' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglfakturpajak; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">DPP (Rp.) <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmldpp' name='e_jmldpp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmldpp; ?>' onblur="HitungJumlah()">
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">PPN (%) <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmlppn' name='e_jmlppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlppn; ?>' onblur="HitungPPN()">
                                            <input type='hidden' id='e_jmlrpppn' name='e_jmlrpppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrpppn; ?>' Readonly>
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">PPH <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <div style="margin-bottom:2px;">
                                                <select class='soflow' name='cb_pph' id='cb_pph' onchange="ShowPPH()">
                                                    <?php
                                                    $ketPPH21="PPH21 (DPP*5%*50%)";
                                                    $ketPPH23="PPH23 (DPP*2%)";
                                                    
                                                    if ($pjnspph=="pph21") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21' selected>$ketPPH21</option>";
                                                        echo "<option value='pph23'>$ketPPH23</option>";
                                                    }elseif ($pjnspph=="pph23") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21'>$ketPPH21</option>";
                                                        echo "<option value='pph23' selected>$ketPPH23</option>";
                                                    }else{
                                                        echo "<option value='' selected></option>";
                                                        echo "<option value='pph21'>$ketPPH21</option>";
                                                        echo "<option value='pph23'>$ketPPH23</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <input type='hidden' id='e_jmlpph' name='e_jmlpph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlpph; ?>' readonly>
                                                <input type='hidden' id='e_jmlrppph' name='e_jmlrppph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrppph; ?>' Readonly>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Pembulatan <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmlbulat' name='e_jmlbulat' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlbulat; ?>' onblur="HitungJumlahUsulan()">
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>'>
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Realisasi <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_realisasi' name='e_realisasi' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $prealisasi; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Slip <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_noslip' name='e_noslip' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnoslip; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl02'>Tanggal Transfer </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='mytgl02'>
                                            <input type="text" class="form-control" id='e_tgltrans' name='e_tgltrans' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgltrans; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class="checkbox">
                                            <label><input type="checkbox" value="lapiran" name="cx_lapir" <?PHP echo $plampiran; ?>> Lampiran </label> &nbsp;&nbsp;&nbsp;
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pengajuan <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_divpengajuan' name='cb_divpengajuan' onchange="showCOANya('cb_divpengajuan', 'cb_coa')">
                                            <?PHP //$pdivpengajuan
                                            $query = "SELECT DivProdId as divprodid, nama as nama FROM dbmaster.divprod where br='Y' AND DivProdId NOT IN ('HO') ";//AND DivProdId NOT IN ('OTHER', 'OTHERS')
                                            $query .=" order by nama";
                                            $tampil=mysqli_query($cnmy, $query);
                                            if ($pdivpengajuan=="ETH") {
                                                //echo "<option value='ETH' selected>ETHICAL</option>";
                                            }
                                            while($et=mysqli_fetch_array($tampil)){
                                                $netdivprod=$et['divprodid'];
                                                $netdivnm=$et['nama'];
                                                if ($netdivprod=="CAN") $netdivnm="CANARY / ETHICAL";
                                                if ($netdivprod==$pdivpengajuan)
                                                    echo "<option value='$netdivprod' selected>$netdivnm</option>";
                                                else
                                                    echo "<option value='$netdivprod'>$netdivnm</option>";
                                                
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_coa'>Kode / COA <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_coa' name='cb_coa' onchange="">
                                            <option value='' selected>-- Pilihan --</option>
                                        <?PHP
                                            if (empty($pcoa)) $pcoa="755-31";
                                            //$pcoa4=  getfield("select COA4 as lcfields from dbmaster.v_coa where kodeid='$pkodeid'");
                                            $query = "SELECT COA4, NAMA4 FROM dbmaster.coa_level4 where COA4='$pcoa'";
                                            $query .= " order by COA4";
                                            $tampil=mysqli_query($cnmy, $query);
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                $ncoa4=$a['COA4'];
                                                $nnmcoa4=$a['NAMA4'];
                                                if ($ncoa4==$pcoa)
                                                    echo "<option value='$ncoa4' selected>$ncoa4 - $nnmcoa4</option>";
                                                else
                                                    echo "<option value='$ncoa4'>$ncoa4 - $nnmcoa4</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <?PHP if ($_GET['act']=="tambahbaru") { ?>
                                                <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                            <?PHP }else{ 
                                                echo "<a class='btn btn-default' href='?module=$pmodule&idmenu=$pidmenu&act=$pidmenu'>Back</a>";
                                            }
                                            ?>
                                                
                                            <!--<small>tambah data</small>-->
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    
                    
                </div>
            </div>
            
        </form>
        
        <?PHP if ($pact=="tambahbaru") { ?>
        
        <style>
            .divnone {
                display: none;
            }
            #datatable th {
                font-size: 12px;
            }
            #datatable td { 
                font-size: 11px;
            }
        </style>

        <script>
            $(document).ready(function() {
                var table = $('#datatable').DataTable({
                    fixedHeader: true,
                    "ordering": false,
                    "columnDefs": [
                        { "visible": false },
                        { className: "text-right", "targets": [6] },//right
                        { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] }//nowrap

                    ],
                    bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                    "bPaginate": false
                } );
            } );
            
            function ProsesData(ket, noid){
                
                ok_ = 1;
                if (ok_) {
                    var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
                    if (r==true) {
                        
                        var txt;
                        if (ket=="reject" || ket=="hapus" || ket=="pending") {
                            var textket = prompt("Masukan alasan "+ket+" : ", "");
                            if (textket == null || textket == "") {
                                txt = textket;
                            } else {
                                txt = textket;
                            }
                        }
                        
                        
                        //document.write("You pressed OK!")
                        document.getElementById("demo-form2").action = "module/mod_br_entryklaim/aksi_entryklaim.php?kethapus="+txt+"&ket="+ket+"&id="+noid;
                        document.getElementById("demo-form2").submit();
                        return 1;
                    }
                } else {
                    //document.write("You pressed Cancel!")
                    return 0;
                }
                
                

            }
        </script>
        
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_content'>
                <div class='x_panel'>
                    <b>Data yang terakhir diinput (max 5 data)</b>
                    <table id='datatable' class='table table-striped nowrap table-bordered' width='100%'>
                        <thead>
                            <tr>
                                <th>Aksi</th><th>NoID</th>
                                <th width='60px'>Tanggal</th><th width='60px'>Tgl. Transfer</th>
                                <th>Yg Membuat</th>
                                <th width='80px'>Distributor</th><th width='50px'>Jumlah</th>
                                <th width='50px'>Realisasi</th><th width='50px'>No Slip</th>
                                <th>Lampiran</th><th width='100px'>Aktivitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?PHP
                            $no=1;
                            //include "config/koneksimysqli_it.php";
                            $sql = "SELECT a.klaimId, DATE_FORMAT(a.tgl,'%d %M %Y') as tgl, DATE_FORMAT(a.tgltrans,'%d %M %Y') as tgltrans, "
                                    . " a.karyawanId, c.nama, a.distid, b.nama as nama_distributor, FORMAT(a.jumlah,2,'de_DE') as jumlah, a.realisasi1, "
                                    . " a.noslip, a.lampiran, a.aktivitas1, a.aktivitas2 ";
                            $sql.=" FROM hrd.klaim a LEFT JOIN MKT.distrib0 b on a.distid=b.distid "
                                    . " LEFT JOIN hrd.karyawan c on a.karyawanid=c.karyawanid";
                            $sql.=" WHERE 1=1 and a.user1='$iduser' ";
                            $sql.=" order by a.klaimId desc limit 5 ";

                            $tampil=mysqli_query($cnmy, $sql);
                            while ($xc=  mysqli_fetch_array($tampil)) {
                                $faksi = ""
                                        . "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$xc[klaimId]'>Edit</a> "
                                        . "<button class='btn btn-danger btn-xs'"
                                        . "onClick=\"ProsesData('hapus', '$xc[klaimId]')\">Hapus</button>
                                            
                                ";
                                $fnoid = $xc['klaimId'];
                                //$ftgl = "<a href='#' data-toggle=\"tooltip\" data-placement=\"top\" title=".$xc['klaimId'].">".$xc["tgl"]."</a>";
                                $ftgl = $xc["tgl"];
                                $ftgltrans = $xc["tgltrans"];
                                $fnamakry = $xc["nama"];
                                $nmdist = $xc["nama_distributor"];
                                $fjuml = $xc["jumlah"];
                                $freal = $xc["realisasi1"];
                                $noslip = $xc["noslip"];
                                $lamp = $xc["lampiran"];
                                $aktivitas = $xc["aktivitas1"];
                                echo "<tr>";
                                echo "<td>$faksi</td>";
                                echo "<td>$fnoid</td>";
                                echo "<td>$ftgl</td>";
                                echo "<td>$ftgltrans</td>";
                                echo "<td>$fnamakry</td>";
                                echo "<td>$nmdist</td>";
                                echo "<td>$fjuml</td>";
                                echo "<td>$freal</td>";
                                echo "<td>$noslip</td>";
                                echo "<td>$lamp</td>";
                                echo "<td>$aktivitas</td>";
                                echo "</tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        
        
        <?PHP } ?>
    </div>
    
    
</div>



<script>
    $(document).ready(function() {
        ShowPajak();
    } );
    
    function showCOANya(divisi, coa){
        var ediv = document.getElementById(divisi).value;
        if (ediv=="") {
            ediv="CAN";
        }else if (ediv=="OTH" || ediv=="OTHER" || ediv=="OTHERS") {
            ediv="CAN";
        }
        $.ajax({
            type:"post",
            url:"module/mod_br_entryklaim/viewdata.php?module=viewdatacombocoa&data1="+ediv+"&data2="+coa,
            data:"udiv="+ediv+"&ucoa="+coa,
            success:function(data){
                $("#"+coa).html(data);
            }
        });
    }

    
    function disp_confirm(pText_, ket)  {
        var edist =document.getElementById('e_iddist').value;
        var ebuat =document.getElementById('e_idkaryawan').value;
        var ejumlah =document.getElementById('e_jmlusulan').value;
        var ecoa =document.getElementById('cb_coa').value;

        if (ebuat==""){
            alert("yang membuat masih kosong....");
            return 0;
        }
        if (edist==""){
            alert("distributor masih kosong....");
            return 0;
        }
        if (ejumlah==""){
            alert("jumlah masih kosong....");
            document.getElementById('e_jmlusulan').focus();
            return 0;
        }

        if (ecoa==""){
            alert("coa masih kosong....");
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
                document.getElementById("demo-form2").action = "module/mod_br_entryklaim/aksi_entryklaim.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }

</script>

<script>
    
    function ShowPajak(){
        var epajak = document.getElementById('cb_pajak').value;

        if (epajak=="" || epajak=="N"){
            n_pajak1.style.display = 'none';
        }else{
            n_pajak1.style.display = 'block';
        }
        
        //document.getElementById('e_kenapajak').focus();
        /*
        if (epajak==""){
            n_pajak.classList.add("disabledDiv");
        }else{
            n_pajak.classList.remove("disabledDiv");
        }
        */
    }

    function HitungJumlah(){
        HitungPPN();
        HitungPPH();
        HitungJumlahUsulan();
    }

    function HitungPPN(){
        var newchar = '';
        var e_totrpppn = "0";

        ejmldpp = document.getElementById("e_jmldpp").value;
        if (ejmldpp!="" && ejmldpp != "0") {
            var njmldpp = ejmldpp; 
            njmldpp = njmldpp.split(',').join(newchar);

            eppn = document.getElementById("e_jmlppn").value;
            if (eppn!="" && eppn != "0") {
                var nppn = eppn; 
                nppn = nppn.split(',').join(newchar);

                e_totrpppn = njmldpp * nppn / 100;
            }

        }

        document.getElementById("e_jmlrpppn").value = e_totrpppn;
        HitungPPH();
    }

    function ShowPPH(){
        var epph = document.getElementById("cb_pph").value;
        if (epph=="pph21") {
            document.getElementById("e_jmlpph").value = "5";
            HitungPPH();
        }else if (epph=="pph23") {
            document.getElementById("e_jmlpph").value = "2";
            HitungPPH();
        }else{
            document.getElementById("e_jmlpph").value = "0";
            document.getElementById("e_jmlrppph").value = "0";
            HitungJumlahUsulan();
        }
    }

    function HitungPPH(){
        var newchar = '';
        var e_totrppph = "0";
        var epph = document.getElementById("cb_pph").value;

        if (epph!="") {
            ejmldpp = document.getElementById("e_jmldpp").value;
            if (ejmldpp!="" && ejmldpp != "0") {
                var njmldpp = ejmldpp; 
                njmldpp = njmldpp.split(',').join(newchar);

                e_totrppph = njmldpp;

                if (epph=="pph21") {
                    npph = "5";
                    e_totrppph = (njmldpp * npph / 100)*50/100;   
                }else if (epph=="pph23") {
                    npph = "2";
                    e_totrppph = (njmldpp * npph / 100);
                }
            }
        }
        document.getElementById("e_jmlrppph").value = e_totrppph;
        HitungJumlahUsulan();
    }


    function HitungJumlahUsulan(){

        var newchar = '';

        ejmldpp = document.getElementById("e_jmldpp").value;
        var e_totrpusulan = ejmldpp;
        erpppn = document.getElementById("e_jmlrpppn").value;
        erppph = document.getElementById("e_jmlrppph").value;
        erpbulat = document.getElementById("e_jmlbulat").value;
        if (erpppn=="") erpppn="0";
        if (erppph=="") erppph="0";
        if (erpbulat=="") erpbulat="0";



        var njmldpp = ejmldpp; 
        njmldpp = njmldpp.split(',').join(newchar);

        var nrpppn = erpppn; 
        nrpppn = nrpppn.split(',').join(newchar);

        var nrppph = erppph; 
        nrppph = nrppph.split(',').join(newchar);

        var nrpbulat = erpbulat; 
        nrpbulat = nrpbulat.split(',').join(newchar);

        e_totrpusulan=( ( parseFloat(njmldpp)+parseFloat(nrpppn) - parseFloat(nrppph) ) + parseFloat(nrpbulat) );
        //e_totrpusulan=( njmldpp+nrpppn - nrppph ) + nrpbulat);
        //parseFloat(value).toFixed(2);
        //document.getElementById("e_jmlusulan").value = parseInt(e_totrpusulan);
        document.getElementById("e_jmlusulan").value = parseFloat(e_totrpusulan).toFixed(2);

    }
</script>