<?PHP
$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pact=$_GET['act'];


$hari_ini = date("Y-m-d");
$tglinput = date('d/m/Y', strtotime($hari_ini));
$pidbr="";

$filkaryawncabang="";
if (!empty($_SESSION['AKSES_CABANG'])) $filkaryawncabang = $_SESSION['AKSES_CABANG'];
$paksesregion=$_SESSION['AKSES_REGION'];
$pstsadmin=$_SESSION['STSADMIN'];
$plvlposisi=$_SESSION['LVLPOSISI'];
$pidcardlogin=$_SESSION['IDCARD'];
$phn_karaktif_f="Y";
$jabatan_="";
$hanyasatukaryawan = "";

$pidkaryawan=$_SESSION['IDCARD'];
$pjabatanid = getfield("select jabatanId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$pidkaryawan'");
if (empty($pjabatanid))
    $pjabatanid = getfield("select jabatanId as lcfields from hrd.karyawan where karyawanId='$pidkaryawan'");


$pjnspajak = "N";
$pjmldpp=0;
$pjmlppn=10;
$pjmlrpppn=0;
$pjnspph="";
$pjmlpph=5;
$pjmlrppph=0;
$pjmlbulat=0;
$pjmlmaterai=0;
$ptglfakturpajak = date('d/m/Y', strtotime($hari_ini));
$pnoseripajak="";
$pkenapajak="";
$prpjumlahjasa="";
$pchkjasa="";
$pchkatrika="";

$pcabangid="";
$pmrid="";
$pdokterid="";
$pdivisi="";
$pcoa4="";
$paktivitas="";
$pccyid="IDR";
$pjumlah="";


$patasan1="";
$patasan2="";
$patasan3="";
$patasan4="";


$act="input";
if ($pact=="editdata"){
    $act="update";
    $pidbr=$_GET['id'];
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_br_cab WHERE bridinputcab='$pidbr'");
    $r    = mysqli_fetch_array($edit);
    
    $pcabangid=$r['icabangid'];
    $pidkaryawan=$r['karyawanid'];
    $pjabatanid=$r['jabatanid'];
    $pmrid=$r['karyawanid2'];
    $pdokterid=$r['dokterid'];
    $pdivisi=$r['divisi'];
    $pcoa4=$r['coa4'];
    $paktivitas=$r['aktivitas'];
    $pccyid=$r['ccyid'];
    
    $patasan1=$r['atasan1'];
    $patasan2=$r['atasan2'];
    $patasan3=$r['atasan3'];
    $patasan4=$r['atasan4'];
    
    $pjumlah=$r['jumlah'];
    
    
    $ptglfakturpajak = date('d/m/Y', strtotime($hari_ini));
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
    $pjmlmaterai=$r['materai_rp'];
    $pjenisdpp=$r['jenis_dpp'];

    $prpjumlahjasa=$r['jasa_rp'];
    if (empty($prpjumlahjasa)) $prpjumlahjasa=0;


    $pchkjasa="";
    $pchkatrika="";
    if ($pjenisdpp=="A") {
        $pchkjasa="checked";
    }elseif ($pjenisdpp=="B") {
        $pchkatrika="checked";
    }
}

?>


<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>
<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=$act&idmenu=$pidmenu"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
        
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <div class='x_panel'>
                    
                    
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value="<?PHP echo $pidbr; ?>" Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Tgl BR </label>
                                    <div class='col-md-9'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='mytgl01' name='e_tglinput' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tglinput; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='cb_idcabang' name='cb_idcabang' onchange="">
                                          <?PHP
                                            
                                            if ($pact=="editdata"){
                                                $query = "SELECT distinct iCabangId icabangid, nama from MKT.icabang where iCabangId='$pcabangid' order by nama";
                                            }else{
                                                $pcabangid="0000000001";
                                                $query = "SELECT distinct iCabangId as icabangid, nama from MKT.icabang where aktif='Y' order by nama";
                                                if ($pjabatanid=="15") {//mr
                                                    $query = "select distinct a.icabangid, b.nama from mkt.imr0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                                                }elseif ($pjabatanid=="08") {//dm
                                                    $query = "select distinct a.icabangid, b.nama from mkt.idm0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                                                }elseif ($pjabatanid=="18" OR $pjabatanid=="10") {
                                                    $query = "select distinct a.icabangid, b.nama from mkt.ispv0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                                                }elseif ($pjabatanid=="20") {//sm
                                                    $query = "select distinct a.icabangid, b.nama from mkt.ism0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                                                }
                                                
                                                if ($pjabatanid=="15" OR $pjabatanid=="08" OR $pjabatanid=="18" OR $pjabatanid=="10" OR $pjabatanid=="20") {
                                                    $pcabangid="";
                                                }
                                                
                                            }
                                            
                                            $tampil=mysqli_query($cnmy, $query);
                                            while($a=mysqli_fetch_array($tampil)){
                                                $nidcabang=$a['icabangid'];
                                                $nnamacab=$a['nama'];
                                                if ($nidcabang==$pcabangid)
                                                    echo "<option value='$nidcabang' selected>$nnamacab</option>";
                                                else
                                                    echo "<option value='$nidcabang'>$nnamacab</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_idkaryawan' name='e_idkaryawan' onchange="TampilkanDataKaryawan()">
                                            <?PHP
                                            PilihKaryawanAktif("", "-- Pilihan --", $pidkaryawan, "$phn_karaktif_f", $pstsadmin, $fildiv, $plvlposisi, $tampilbawahan, $pidcardlogin, $jabatan_, $paksesregion, $filkaryawncabang, "", $hanyasatukaryawan);
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div id="div_kry">
                                
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_jabatanid' name='e_jabatanid' class='form-control col-md-7 col-xs-12' value="<?PHP echo $pjabatanid; ?>" Readonly>
                                        </div>
                                    </div>
                                    
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>MR <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_idmr' name='cb_idmr' onchange="ShowDataDataMR()">
                                            <?PHP
                                                $query = "select a.karyawanId, a.nama from hrd.karyawan a "
                                                        . " LEFT JOIN dbmaster.t_karyawan_posisi b on a.karyawanId=b.karyawanId "
                                                        . " WHERE a.aktif='Y' ";
                                                $query .=" AND a.karyawanId not in (select distinct karyawanid from dbmaster.t_karyawanadmin) ";
                                                if ($pjabatanid=="15") $query .=" AND a.karyawanId='$pidkaryawan' "; // mr
                                                elseif ($pjabatanid=="08") $query .=" AND b.dm='$pidkaryawan' "; // dm
                                                elseif (($pjabatanid=="18") or ($pjabatanid=="10")) $query .=" AND b.spv='$pidkaryawan' "; // spv am
                                                elseif ($pjabatanid=="20") $query .=" AND b.sm='$pidkaryawan' "; // sm
                                                $query .=" ORDER BY 2,1";
                                                $tampil = mysqli_query($cnmy, $query);
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                while ($rc=mysqli_fetch_array($tampil)){
                                                    $nkaryawanid=$rc['karyawanId'];
                                                    $nnamakry=$rc['nama'];
                                                    if ($nkaryawanid==$pmrid)
                                                        echo "<option value='$nkaryawanid' selected>$nnamakry</option>";
                                                    else
                                                        echo "<option value='$nkaryawanid'>$nnamakry</option>";
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_cabangpil' name='cb_cabangpil' onchange="">
                                              <?PHP

                                                if ($pact=="editdata"){
                                                    $query = "SELECT distinct iCabangId icabangid, nama from MKT.icabang where iCabangId='$pcabangid' order by nama";
                                                }else{

                                                    $query = "SELECT distinct iCabangId as icabangid, nama from MKT.icabang where aktif='Y' order by nama";
                                                    if ($pjabatanid=="15") {//mr
                                                        $query = "select distinct a.icabangid, b.nama from mkt.imr0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                                                    }elseif ($pjabatanid=="08") {//dm
                                                        $query = "select distinct a.icabangid, b.nama from mkt.idm0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                                                    }elseif ($pjabatanid=="18" OR $pjabatanid=="10") {
                                                        $query = "select distinct a.icabangid, b.nama from mkt.ispv0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                                                    }elseif ($pjabatanid=="20") {//sm
                                                        $query = "select distinct a.icabangid, b.nama from mkt.ism0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                                                    }

                                                }

                                                $tampil=mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $nidcabang=$a['icabangid'];
                                                    $nnamacab=$a['nama'];
                                                    if ($nidcabang==$pcabangid)
                                                        echo "<option value='$nidcabang' selected>$nnamacab</option>";
                                                    else
                                                        echo "<option value='$nidcabang'>$nnamacab</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dokter <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_iddokter' name='cb_iddokter' onchange="">
                                                <?PHP
                                                $filter_kry_dok=" and karyawan.karyawanId='$pidkaryawan' ";
                                                if ($pidcabang=="0000000001") {
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
                                                $tampil=mysqli_query($cnmy, $query);
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                while($an=mysqli_fetch_array($tampil)){
                                                    $ndokterid=$an['dokterId'];
                                                    $ndokternm=$an['nama'];
                                                    if ($ndokterid==$pdokterid)
                                                        echo "<option value='$ndokterid' selected>$ndokternm</option>";
                                                    else
                                                        echo "<option value='$ndokterid'>$ndokternm</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowDataCOAKode()">
                                                <?PHP
                                                $query = "select DivProdId as divisi, nama from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTHER', 'CAN') order by 1,2";
                                                if ($pjabatanid=="15") {//mr
                                                    $query = "select distinct a.divisiid as divisi, b.nama from MKT.imr0 a JOIN MKT.divprod b on a.divisiid=b.DivProdId where a.karyawanid='$pidkaryawan' ORDER BY 1,2;";
                                                }elseif ($pjabatanid=="18" OR $pjabatanid=="10") {
                                                    $query = "select distinct a.divisiid as divisi, b.nama from MKT.ispv0 a JOIN MKT.divprod b on a.divisiid=b.DivProdId where a.karyawanid='$pidkaryawan' ORDER BY 1,2";
                                                }

                                                $tampil=mysqli_query($cnmy, $query);
                                                //echo "<option value='' selected>-- Pilihan --</option>";
                                                while($an=mysqli_fetch_array($tampil)){
                                                    $ndivisi=$an['divisi'];
                                                    $nnama=$an['nama'];
                                                    if ($ndivisi==$pdivisi)
                                                        echo "<option value='$ndivisi' selected>$nnama</option>";
                                                    else
                                                        echo "<option value='$ndivisi'>$nnama</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode / COA <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_kode' name='cb_kode' onchange="">
                                                <?PHP
                                                $query="select a.COA4, a.NAMA4, c.DIVISI2 from dbmaster.coa_level4 a 
                                                    JOIN dbmaster.coa_level3 b on a.COA3=b.COA3 JOIN dbmaster.coa_level2 c on b.COA2=c.COA2 WHERE 
                                                    a.COA4 IN ('702-03', '701-03', '704-03', '703-03') AND c.DIVISI2='$pdivisi'";//'702-02', '701-02', '704-02', '703-02', (ini DCC)
                                                $tampil=mysqli_query($cnmy, $query);
                                                //echo "<option value='' selected>-- Pilihan --</option>";
                                                while($an=mysqli_fetch_array($tampil)){
                                                    $ncoa4=$an['COA4'];
                                                    $nnama4=$an['NAMA4'];
                                                    if ($ncoa4==$pcoa4)
                                                        echo "<option value='$ncoa4' selected>$nnama4</option>";
                                                    else
                                                        echo "<option value='$ncoa4'>$nnama4</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                </div>
                                    
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='keterangan'><?PHP echo $paktivitas; ?></textarea>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Mata Uang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='cb_jenis' name='cb_jenis' onchange="">
                                            <?php
                                            $tampil=mysqli_query($cnmy, "SELECT ccyId, nama FROM hrd.ccy");
                                            while($c=mysqli_fetch_array($tampil)){
                                                $nccyid=$c['ccyId'];
                                                $nnamaccy=$c['nama'];
                                                
                                                if ($nccyid==$pccyid)
                                                    echo "<option value='$nccyid' selected>$nccyid - $nnamaccy</option>";
                                                else    
                                                    echo "<option value='$nccyid'>$nccyid - $nnamaccy</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pajak <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='cb_pajak' name='cb_pajak' onchange="ShowPajak()">
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
                                    
                                    <!--- untuk jasa -->
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">&nbsp;<span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type="checkbox" value="jasa" id="chk_jasa" name="chk_jasa" onclick="cekBoxPilihDPP('chk_jasa')" <?PHP echo $pchkjasa; ?>> DPP Dari Jumlah Awal
                                            <br/>
                                            <input type="checkbox" value="atrika" id="chk_atrika" name="chk_atrika" onclick="cekBoxPilihDPP('chk_atrika')" <?PHP echo $pchkatrika; ?>> DPP Khusus (Atrika, dll)
                                        </div>
                                    </div>
                                    
                                    <div id="n_pajakjasa">
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;"><u>Jumlah Awal (Rp.)</u> <span class='required'></span></label>
                                            <div class='col-md-6 col-sm-6 col-xs-12'>
                                                <input type='text' id='e_rpjmljasa' name='e_rpjmljasa' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpjumlahjasa; ?>' onblur="HitungJumlahDPP()">
                                            </div><!--disabled='disabled'-->
                                        </div>
                                        
                                    </div>
                                    <!--- END untuk jasa -->
                                    
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
                                                <select class='form-control input-sm' id='cb_pph' name='cb_pph' onchange="ShowPPH()">
                                                    <?php
                                                    $ketPPH21="PPH21 (DPP*5%*50%) atau (JML AWAL*5%*50%)";
                                                    $ketPPH23="PPH23 (DPP*2%) atau (JML AWAL*2%)";
                                                    
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
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Biaya Materai (Rp.) <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmlmaterai' name='e_jmlmaterai' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlmaterai; ?>' onblur="HitungJumlahUsulan()">
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                </div>
                                
                                
                                
                                <div id="div_atasan">
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:green;">SPV/AM <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_apvspv' name='cb_apvspv' onchange="">
                                                <?PHP
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                $query = "select karyawanId, nama from hrd.karyawan WHERE karyawanId='$patasan1' order by nama";
                                                $tampil=mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $akaryawanid=$a['karyawanId'];
                                                    $anamakaryawan=$a['nama'];
                                                    if ($akaryawanid==$patasan1)
                                                        echo "<option value='$akaryawanid' selected>$anamakaryawan</option>";
                                                    else
                                                        echo "<option value='$akaryawanid'>$anamakaryawan</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:green;">DM <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_apvdm' name='cb_apvdm' onchange="">
                                                <?PHP
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                $query = "select karyawanId, nama from hrd.karyawan WHERE karyawanId='$patasan2' order by nama";
                                                $tampil=mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $akaryawanid=$a['karyawanId'];
                                                    $anamakaryawan=$a['nama'];
                                                    if ($akaryawanid==$patasan2)
                                                        echo "<option value='$akaryawanid' selected>$anamakaryawan</option>";
                                                    else
                                                        echo "<option value='$akaryawanid'>$anamakaryawan</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:green;">SM <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_sm' name='cb_sm' onchange="">
                                                <?PHP
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                $query = "select karyawanId, nama from hrd.karyawan WHERE karyawanId='$patasan3' order by nama";
                                                $tampil=mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $akaryawanid=$a['karyawanId'];
                                                    $anamakaryawan=$a['nama'];
                                                    if ($akaryawanid==$patasan3)
                                                        echo "<option value='$akaryawanid' selected>$anamakaryawan</option>";
                                                    else
                                                        echo "<option value='$akaryawanid'>$anamakaryawan</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:green;">GSM/NSM <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_gsm' name='cb_gsm' onchange="">
                                                <?PHP
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                $query = "select karyawanId, nama from hrd.karyawan WHERE karyawanId='$patasan4' order by nama";
                                                $tampil=mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $akaryawanid=$a['karyawanId'];
                                                    $anamakaryawan=$a['nama'];
                                                    if ($akaryawanid==$patasan4)
                                                        echo "<option value='$akaryawanid' selected>$anamakaryawan</option>";
                                                    else
                                                        echo "<option value='$akaryawanid'>$anamakaryawan</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                    
                                </div>
                                
                                
                                
                            </div>
                        </div>
                    </div>
                    <!-- END KIRI -->
                    
                    
                    
                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
<?PHP
$pjenistiket="";
$ptujuandari="";
$ptujuanke="";

$ptgltiket1="";
$pjamtiket1="";

$ptgltiket2="";
$pjamtiket2="";

$pnginapdi="";
$ptglhotel1="";
$ptglhotel2="";
$ptglsewa1="";
$ptglsewa2="";
$pjamsewa1="";
$pjamsewa2="";

$phiddenharga="hidden";

$prphargapergi="";
$prphargapulang="";
$prphargahotel="";
$prphargasewa="";

$pkotasewa="";
$pketpergi="";
$pketpulang="";
$pkethotel="";
$pketsewa="";

$pchktiket="";
$pchktiketpulang="";
$pchkhotel="";
$pchksewa="";
$pjenisbayartiket="M";
$pjenisbayarhotel="M";
$pjenisbayarsewa="M";

if ($pact=="editdata"){   
    $tampiledit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_br_cab1 WHERE bridinputcab='$pidbr'");
    while($row= mysqli_fetch_array($tampiledit)){
        $pnoid=$row['noid'];
        if ($pnoid=="01") {
            $pchktiket="checked";
            
            $pjenistiket=$row['jenistiket'];
            $ptujuandari=$row['kota1'];
            $ptujuanke=$row['kota2'];
            
            $ptgltiket1=$row['tgl1'];
            $pjamtiket1=$row['jam1'];
            $pketpergi=$row['notes'];
            
            $ptgltiket1 = date('d F Y', strtotime($ptgltiket1));
            
        }
        
        if ($pnoid=="02") {
            $pchktiketpulang="checked";
            
            $ptgltiket2=$row['tgl2'];
            $pjamtiket2=$row['jam2'];
            $pketpulang=$row['notes'];
            
            $ptgltiket2 = date('d F Y', strtotime($ptgltiket2));
            
        }
        
        if ($pnoid=="03") {
            $pchkhotel="checked";
            
            $pnginapdi=$row['kota1'];
            $ptglhotel1=$row['tgl1'];
            $ptglhotel2=$row['tgl2'];
            $pkethotel=$row['notes'];
            $pjenisbayarhotel=$row['stsbayar'];
            
            $ptglhotel1 = date('d F Y', strtotime($ptglhotel1));
            $ptglhotel2 = date('d F Y', strtotime($ptglhotel2));
            
        }
        
        if ($pnoid=="04") {
            $pchksewa="checked";
            
            $pkotasewa=$row['kota1'];
            $ptglsewa1=$row['tgl1'];
            $pjamsewa1=$row['jam1'];
            $ptglsewa2=$row['tgl2'];
            $pjamsewa2=$row['jam2'];
            $pketsewa=$row['notes'];
            $pjenisbayarsewa=$row['stsbayar'];
            
            $ptglsewa1 = date('d F Y', strtotime($ptglsewa1));
            $ptglsewa2 = date('d F Y', strtotime($ptglsewa2));
            
        }
        
        
        
    }
    
}
?>
                                
                                
<script type="text/javascript">
    $(function() {
        $('#e_tglpergi').datepicker({
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            minDate: '0',
            maxDate: '+2Y',
            onSelect: function(dateStr) {
                var min = $(this).datepicker('getDate');
                $('#e_tglpulang').datepicker('option', 'minDate', min || '0');
                $('#e_tglmulai').datepicker('option', 'minDate', min || '0');
                $('#e_tglsampai').datepicker('option', 'minDate', min || '0');
                $('#e_tglsewa1').datepicker('option', 'minDate', min || '0');
                $('#e_tglsewa2').datepicker('option', 'minDate', min || '0');
                datepicked();
            } 
        });
        $('#e_tglpulang').datepicker({
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            minDate: '0',
            maxDate: '+2Y',
            onSelect: function(dateStr) {
                var max = $(this).datepicker('getDate');
                $('#e_tglpergi').datepicker('option', 'maxDate', max || '+2Y');
                $('#e_tglmulai').datepicker('option', 'maxDate', max || '+2Y');
                $('#e_tglsampai').datepicker('option', 'maxDate', max || '+2Y');
                $('#e_tglsewa1').datepicker('option', 'maxDate', max || '+2Y');
                $('#e_tglsewa2').datepicker('option', 'maxDate', max || '+2Y');
                datepicked();
            } 
        });
        
        //hotel
        $('#e_tglmulai').datepicker({
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            minDate: '0',
            maxDate: '+2Y',
            onSelect: function(dateStr) {
                var min = $(this).datepicker('getDate');
                $('#e_tglsampai').datepicker('option', 'minDate', min || '0');
                datepicked2();
            } 
        });
        
        $('#e_tglsampai').datepicker({
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            minDate: '0',
            maxDate: '+2Y',
            onSelect: function(dateStr) {
                var max = $(this).datepicker('getDate');
                $('#e_tglmulai').datepicker('option', 'maxDate', max || '+2Y');
                datepicked2();
            } 
        });
        //end hotel
        
        //sewa
        $('#e_tglsewa1').datepicker({
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            minDate: '0',
            maxDate: '+2Y',
            onSelect: function(dateStr) {
                var min = $(this).datepicker('getDate');
                $('#e_tglsewa2').datepicker('option', 'minDate', min || '0');
                datepicked3();
            } 
        });
        
        $('#e_tglsewa2').datepicker({
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            minDate: '0',
            maxDate: '+2Y',
            onSelect: function(dateStr) {
                var max = $(this).datepicker('getDate');
                $('#e_tglsewa1').datepicker('option', 'maxDate', max || '+2Y');
                datepicked3();
            } 
        });
        
        
        //end sewa
    });
    
    
    
    
    var datepicked = function() {
        var from = $('#e_tglpulang');
        var to = $('#e_tglpergi');
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
    
    var datepicked2 = function() {
        var from2 = $('#e_tglsampai');
        var to2 = $('#e_tglmulai');
        var nights2 = $('#nights');
        var fromDate2 = from2.datepicker('getDate')
        var toDate2 = to2.datepicker('getDate')
        if (toDate2 && fromDate2) {
            var difference2 = 0;
            var oneDay2 = 1000 * 60 * 60 * 24;
            var difference2 = Math.ceil((toDate2.getTime() - fromDate2.getTime()) / oneDay2);
            nights2.val(difference2);
        }
    }
    
    var datepicked3 = function() {
        var from3 = $('#e_tglsewa2');
        var to3 = $('#e_tglsewa1');
        var nights3 = $('#nights');
        var fromDate3 = from3.datepicker('getDate')
        var toDate3 = to3.datepicker('getDate')
        if (toDate3 && fromDate3) {
            var difference3 = 0;
            var oneDay3 = 1000 * 60 * 60 * 24;
            var difference3 = Math.ceil((toDate3.getTime() - fromDate3.getTime()) / oneDay3);
            nights3.val(difference3);
        }
    }
        

    function BukaTutupKonten(schk, sdiv) {
        var nchk = document.getElementById(schk);
        var element = document.getElementById(sdiv);
        if (sdiv=="div_pulang") {
            var melmt = document.getElementById("div_tglpulang");
        }
        if (nchk.checked==true) {
            element.classList.remove("disabledDiv");
            if (sdiv=="div_pulang") {
                melmt.classList.remove("disabledDiv");
            }
        }else{
            element.classList.add("disabledDiv");
            if (sdiv=="div_pulang") {
                melmt.classList.add("disabledDiv");
            }
        }   
    }
    
    function GantiMenginap(nkota1, nkota2, npilih) {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var nact = urlku.searchParams.get("act");
        
        if (nact=="editdata") {
            return false;
        }
        
        var ekota1 = document.getElementById(nkota1).value;
        var ekota2 = document.getElementById(nkota2).value;
        var nkota=ekota1;
        if (ekota2=="") {
        }else{
            nkota=ekota2;
        }
        document.getElementById(npilih).value=nkota;
    }
    
    $(document).ready(function() {
        var nchktiket = document.getElementById("chk_tiket");
        if (nchktiket.checked==true) {
            div_tiket.classList.remove("disabledDiv");
        }else{
            div_tiket.classList.add("disabledDiv");
        }
        
        var nchktglpulang = document.getElementById("chk_pulang");
        if (nchktglpulang.checked==true) {
            div_tglpulang.classList.remove("disabledDiv");
            div_pulang.classList.remove("disabledDiv");
        }else{
            div_tglpulang.classList.add("disabledDiv");
            div_pulang.classList.add("disabledDiv");
        }
        
        var nchkhotel = document.getElementById("chk_hotel");
        if (nchkhotel.checked==true) {
            div_hotel.classList.remove("disabledDiv");
        }else{
            div_hotel.classList.add("disabledDiv");
        }
        
        var nchksewa = document.getElementById("chk_sewa");
        if (nchksewa.checked==true) {
            div_sewa.classList.remove("disabledDiv");
        }else{
            div_sewa.classList.add("disabledDiv");
        }
        
    } );
</script>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><u>Tiket</u> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type="checkbox" id="chk_tiket" name="chk_tiket" onclick="BukaTutupKonten('chk_tiket', 'div_tiket')" value='tiket' <?PHP echo $pchktiket; ?> >
                                    </div>
                                </div>


                                <div id="div_tiket">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dari <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_tjdari' name='e_tjdari' class='form-control col-md-7 col-xs-12' placeholder="kota" onblur="GantiMenginap('e_tjdari', 'e_tjke', 'e_nginapdi')" onkeyup="this.value = this.value.toUpperCase();" value='<?PHP echo $ptujuandari; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Ke <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_tjke' name='e_tjke' class='form-control col-md-7 col-xs-12' placeholder="kota" onblur="GantiMenginap('e_tjdari', 'e_tjke', 'e_nginapdi')" onkeyup="this.value = this.value.toUpperCase();" value='<?PHP echo $ptujuanke; ?>'>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><u>Jenis</u> <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_jenistiket' name='cb_jenistiket' onchange="ShowJenisTiket()">
                                                <?php
                                                if ($pjenistiket=="K") {
                                                        echo "<option value='P'>Pesawat</option>";
                                                        echo "<option value='K' selected>KAI</option>";
                                                    }else{
                                                        echo "<option value='P' selected>Pesawat</option>";
                                                        echo "<option value='K'>KAI</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>



                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Berangkat <span class='required'></span></label>
                                        <div class='col-md-9'>
                                            <div class='input-group date' id=''>
                                                <input type="text" class="form-control" id='e_tglpergi' name='e_tglpergi' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptgltiket1; ?>' Readonly>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jam <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_jampergi' name='e_jampergi' autocomplete='off' class='form-control col-md-7 col-xs-12' data-inputmask="'mask': '99:99'" value='<?PHP echo $pjamtiket1; ?>'>
                                        </div>
                                    </div>

                                    <div <?PHP echo $phiddenharga; ?> class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Harga <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_rphargapergi' name='e_rphargapergi' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prphargapergi; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Ket. Tiket Pergi <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_ketpergi' name='e_ketpergi' class='form-control col-md-7 col-xs-12' placeholder="keterangan" value='<?PHP echo $pketpergi; ?>'>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Pulang 
                                            <input type="checkbox" id="chk_pulang" name="chk_pulang" onclick="BukaTutupKonten('chk_pulang', 'div_pulang')" value='pulang' <?PHP echo $pchktiketpulang; ?>>
                                            <span class='required'></span></label>
                                        <div class='col-md-9'>
                                            <div class='input-group date' id='div_tglpulang'>
                                                <input type="text" class="form-control" id='e_tglpulang' name='e_tglpulang' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptgltiket2; ?>' Readonly>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id='div_pulang'>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jam <span class='required'></span></label>
                                            <div class='col-xs-5'>
                                                <input type='text' id='e_jampulang' name='e_jampulang' autocomplete='off' class='form-control col-md-7 col-xs-12' data-inputmask="'mask': '99:99'" value='<?PHP echo $pjamtiket2; ?>'>
                                            </div>
                                        </div>

                                        <div <?PHP echo $phiddenharga; ?> class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Harga <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <input type='text' id='e_rphargapulang' name='e_rphargapulang' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prphargapulang; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Ket. Tiket Pulang <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <input type='text' id='e_ketpulang' name='e_ketpulang' class='form-control col-md-7 col-xs-12' placeholder="keterangan" value='<?PHP echo $pketpulang; ?>'>
                                            </div>
                                        </div>
                                    
                                        
                                    </div>
                                    
                                    
                                </div>
                                
                                <hr/>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><u>Hotel</u> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type="checkbox" id="chk_hotel" name="chk_hotel" onclick="BukaTutupKonten('chk_hotel', 'div_hotel')" value='hotel' <?PHP echo $pchkhotel; ?>>
                                    </div>
                                </div>
                                
                                <div id="div_hotel">
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Menginap di <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_nginapdi' name='e_nginapdi' class='form-control col-md-7 col-xs-12' placeholder="kota" onkeyup="this.value = this.value.toUpperCase();" value='<?PHP echo $pnginapdi; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Mulai <span class='required'></span></label>
                                        <div class='col-md-9'>
                                            <div class='input-group date' id=''>
                                                <input type="text" class="form-control" id='e_tglmulai' name='e_tglmulai' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptglhotel1; ?>' Readonly>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sampai <span class='required'></span></label>
                                        <div class='col-md-9'>
                                            <div class='input-group date' id=''>
                                                <input type="text" class="form-control" id='e_tglsampai' name='e_tglsampai' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptglhotel2; ?>' Readonly>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div <?PHP echo $phiddenharga; ?> class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Harga <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_rphargahotel' name='e_rphargahotel' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prphargahotel; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Ket. Hotel <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_kethotel' name='e_kethotel' class='form-control col-md-7 col-xs-12' placeholder="keterangan" value='<?PHP echo $pkethotel; ?>'>
                                        </div>
                                    </div>
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status Bayar <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_stsbayarhotel' name='cb_stsbayarhotel' onchange="">
                                                <?php
                                                    if ($pjenisbayarhotel=="S") {
                                                        echo "<option value='M'>SDM</option>";
                                                        echo "<option value='S' selected>Bayar Sendiri</option>";
                                                    }else{
                                                        echo "<option value='M' selected>SDM</option>";
                                                        echo "<option value='S'>Bayar Sendiri</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                    
                                    
                                </div>
                                
                                <hr/>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><u>Sewa Mobil</u> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type="checkbox" id="chk_sewa" name="chk_sewa" onclick="BukaTutupKonten('chk_sewa', 'div_sewa')" value='sewa' <?PHP echo $pchksewa; ?>>
                                    </div>
                                </div>
                                
                                <div id="div_sewa">
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kota <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_kotasewa' name='e_kotasewa' class='form-control col-md-7 col-xs-12' placeholder="kota/daerah" onkeyup="this.value = this.value.toUpperCase();" value='<?PHP echo $pkotasewa; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Mulai <span class='required'></span></label>
                                        <div class='col-md-9'>
                                            <div class='input-group date' id=''>
                                                <input type="text" class="form-control" id='e_tglsewa1' name='e_tglsewa1' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptglsewa1; ?>' Readonly>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jam <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_jamsewa1' name='e_jamsewa1' autocomplete='off' class='form-control col-md-7 col-xs-12' data-inputmask="'mask': '99:99'" value='<?PHP echo $pjamsewa1; ?>'>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Sampai <span class='required'></span></label>
                                        <div class='col-md-9'>
                                            <div class='input-group date' id=''>
                                                <input type="text" class="form-control" id='e_tglsewa2' name='e_tglsewa2' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptglsewa2; ?>' Readonly>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jam <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_jamsewa2' name='e_jamsewa2' autocomplete='off' class='form-control col-md-7 col-xs-12' data-inputmask="'mask': '99:99'" value='<?PHP echo $pjamsewa2; ?>'>
                                        </div>
                                    </div>

                                    <div <?PHP echo $phiddenharga; ?> class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Harga <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_rphargasewa' name='e_rphargasewa' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prphargasewa; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Ket. Sewa <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_ketsewa' name='e_ketsewa' class='form-control col-md-7 col-xs-12' placeholder="keterangan" value='<?PHP echo $pketsewa; ?>'>
                                        </div>
                                    </div>
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status Bayar <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control input-sm' id='cb_stsbayarsewa' name='cb_stsbayarsewa' onchange="">
                                                <?php
                                                    if ($pjenisbayarsewa=="S") {
                                                        echo "<option value='M'>SDM</option>";
                                                        echo "<option value='S' selected>Bayar Sendiri</option>";
                                                    }else{
                                                        echo "<option value='M' selected>SDM</option>";
                                                        echo "<option value='S'>Bayar Sendiri</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <hr/>
                                
                                <div <?PHP echo $phiddenharga; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>' Readonly>
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                
                                
                                <?PHP if ($pact=="editdata") { ?>
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <div class="checkbox">
                                                <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                                <a class='btn btn-default' href="<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                                                <!--<input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>-->
                                            </div>
                                        </div>
                                    </div>
                                
                                <?PHP } ?>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    
                    <!-- END KANAN -->
                    
                    
                </div>
                
                <?PHP
                if ($pact=="tambahbaru") {
                    echo "<div class='col-sm-5'>";
                    include "ttd_entrybrdcccab.php";
                    echo "</div>";
                }
                ?>
                
            </div>
        
        </form>
        
        
        
    </div>
    
    
    
</div>

<script>
    function TampilkanDataKaryawan() {
        var eidkaryawan = document.getElementById('e_idkaryawan').value;
        var eidcabang = document.getElementById('cb_cabangpil').value;

        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrdcccab/viewdata.php?module=viewdatakaryawan",
            data:"uidkaryawan="+eidkaryawan+"&uidcabang="+eidcabang,
            success:function(data){
                $("#div_kry").html(data);
                ShowDataCabang();
                ShowDataDokter('e_idkaryawan', 'cb_idmr', 'cb_cabangpil');
                ShowDataDivisi('e_idkaryawan', 'cb_idmr', 'e_jabatanid', 'cb_cabangpil');
                ShowAtasanKaryawan();
            }
        });
    }
    
    function ShowAtasanKaryawan() {
        var eidkaryawan = document.getElementById('e_idkaryawan').value;
        var ejabatanid = document.getElementById('e_jabatanid').value;
        var ecabangid = document.getElementById('cb_cabangpil').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrdcccab/viewdata.php?module=viewdataatasan",
            data:"uidkaryawan="+eidkaryawan+"&ujabatanid="+ejabatanid+"&ucabangid="+ecabangid,
            success:function(data){
                $("#div_atasan").html(data);
            }
        });
    }
    
    
    function ShowDataCabang() {
        return false;
        var eidkaryawan = document.getElementById('e_idkaryawan').value;
        var ejabatanid = document.getElementById('e_jabatanid').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrdcccab/viewdata.php?module=viewdatacabang",
            data:"uidkaryawan="+eidkaryawan+"&ujabatanid="+ejabatanid,
            success:function(data){
                $("#cb_idcabang").html(data);
            }
        });
    }
    
    function ShowDataDataMR() {
        ShowDataDokter('e_idkaryawan', 'cb_idmr', 'cb_cabangpil');
        ShowDataDivisi('e_idkaryawan', 'cb_idmr', 'e_jabatanid', 'cb_cabangpil');
        ShowDataCOAKode();
    }
    
    function ShowDataDokter(nidkaryawan, nidmr, nidcab) {
        var eidkaryawan = document.getElementById(nidkaryawan).value;
        var eidmr = document.getElementById(nidmr).value;
        var eidcabang = document.getElementById(nidcab).value;
        var ejabatanid = document.getElementById('e_jabatanid').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrdcccab/viewdata.php?module=viewdatadokter",
            data:"uidkaryawan="+eidkaryawan+"&uidmr="+eidmr+"&uidcabang="+eidcabang+"&ujabatanid="+ejabatanid,
            success:function(data){
                $("#cb_iddokter").html(data);
            }
        });
    }
    
    function ShowDataDivisi(nidkaryawan, nidmr, njbt, nidcab) {
        var eidkaryawan = document.getElementById(nidkaryawan).value;
        var eidmr = document.getElementById(nidmr).value;
        var ejabatanid = document.getElementById(njbt).value;
        var eidcabang = document.getElementById(nidcab).value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrdcccab/viewdata.php?module=viewdatadivisi",
            data:"uidkaryawan="+eidkaryawan+"&uidmr="+eidmr+"&ujabatanid="+ejabatanid+"&uidcabang="+eidcabang,
            success:function(data){
                $("#cb_divisi").html(data);
                ShowDataCOAKode();
            }
        });
    }
    
    function ShowDataCOAKode() {
        var edivisi = document.getElementById('cb_divisi').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrdcccab/viewdata.php?module=viewdatacoakode",
            data:"udivisi="+edivisi,
            success:function(data){
                $("#cb_kode").html(data);
            }
        });
    }
    
    
    function disp_confirm(pText_,ket)  {
        var ecab =document.getElementById('cb_cabangpil').value;
        var ebuat =document.getElementById('e_idkaryawan').value;
        var ejbtid =document.getElementById('e_jabatanid').value;
        var edivi =document.getElementById('cb_divisi').value;
        var ekode =document.getElementById('cb_kode').value;
        var edokterid =document.getElementById('cb_iddokter').value;
        var ejumlah =document.getElementById('e_jmlusulan').value;


        if (ecab==""){
            alert("cabang masih kosong....");
            return 0;
        }
        if (ebuat==""){
            alert("yang membuat masih kosong....");
            return 0;
        }
        if (edokterid==""){
            //alert("dokter masih kosong....");
            //return 0;
        }
        if (edivi==""){
            alert("divisi masih kosong....");
            return 0;
        }
        if (ekode==""){
            //alert("kode masih kosong....");
            //return 0;
        }
        if (ejumlah==""){
            //alert("jumlah masih kosong....");
            //document.getElementById('e_jmlusulan').focus();
            //return 0;
        }
        
        if (edivi=="HO"){
            
        }else{
            
            if (ejbtid=="20"){
                var cbatasan4 =document.getElementById('cb_gsm').value;
                
                if (cbatasan4=="") {
                    alert("atasan masih kosong....");
                    return 0;
                }
                
            }else{
                
                var cbatasan1 =document.getElementById('cb_apvspv').value;
                var cbatasan2 =document.getElementById('cb_apvdm').value;
                var cbatasan3 =document.getElementById('cb_sm').value;
                var cbatasan4 =document.getElementById('cb_gsm').value;
                
                if (cbatasan1=="" && cbatasan2=="" && cbatasan3=="" && cbatasan4=="") {
                    alert("atasan masih kosong....");
                    return 0;
                }
                
                if (cbatasan3=="") {
                    alert("SM masih kosong....");
                    return 0;
                }
                
                if (cbatasan4=="") {
                    alert("GSM masih kosong....");
                    return 0;
                }
                
            }
            
        }

            var nchktiket = document.getElementById("chk_tiket");
            var nchktglpulang = document.getElementById("chk_pulang");
            var nchkhotel = document.getElementById("chk_hotel");
            var nchksewa = document.getElementById("chk_sewa");
            if (nchktiket.checked==false && nchkhotel.checked==false && nchksewa.checked==false) {
                alert("Tiket, Hotel atau Sewa belum dipilih");
                return false;
            }
            
            if (nchktiket.checked==true) {
                var etujdari =document.getElementById('e_tjdari').value;
                var etujke =document.getElementById('e_tjke').value;
                var etglpergi=document.getElementById('e_tglpergi').value;
                var ejampergi=document.getElementById('e_jampergi').value;
                
                if (etujdari=="" && etujke=="") {
                    alert("Tujuan belum diisi"); document.getElementById('e_tjdari').focus(); return false;
                }
                
                if (etujdari=="") {
                    alert("Tujuan Dari Kota/Daerah, belum diisi"); document.getElementById('e_tjdari').focus(); return false;
                }
                
                if (etujke=="") {
                    alert("Tujuan Ke Kota/Daerah, belum diisi"); document.getElementById('e_tjke').focus(); return false;
                }
                
                
                if (etglpergi=="") {
                    alert("Tanggal Pergi Masih Kosong"); document.getElementById('e_tglpergi').focus(); return false;
                }

                if (ejampergi=="") {
                    alert("Jam Pergi Masih Kosong"); document.getElementById('e_jampergi').focus(); return false;
                }
                
                
                if (nchktglpulang.checked==true) {
                    var etglpulang=document.getElementById('e_tglpulang').value;
                    var ejampulang=document.getElementById('e_jampulang').value;
                    
                    if (etglpergi=="" && etglpulang=="") {
                        alert("Tanggal Tiket Pulang dan Pergi masih kosong"); document.getElementById('e_tglpergi').focus(); return false;
                    }
                    
                    if (etglpulang=="") {
                        alert("Tanggal Pulang Masih Kosong"); document.getElementById('e_tglpulang').focus(); return false;
                    }
                    
                    if (ejampulang=="") {
                        alert("Jam Pulang Masih Kosong"); document.getElementById('e_jampulang').focus(); return false;
                    }
                    
                    
                }
                
                
                
            }
            
            if (nchkhotel.checked==true) {
                var enginapdi=document.getElementById('e_nginapdi').value;
                var etglmulai=document.getElementById('e_tglmulai').value;
                var etglsampai=document.getElementById('e_tglsampai').value;
                
                if (enginapdi=="" && etglmulai=="" && etglsampai=="") {
                    alert("Data Hotel belum diisi"); document.getElementById('enginapdi').focus(); return false;
                }
                
                if (enginapdi=="") {
                    alert("Menginap di Hotel (Kota/Daerah) belum diisi"); document.getElementById('enginapdi').focus(); return false;
                }
                
                if (etglmulai=="") {
                    alert("Tanggal Mulai Menginap di Hotel belum diisi"); document.getElementById('e_tglmulai').focus(); return false;
                }
                
                if (etglsampai=="") {
                    alert("Tanggal Sampai Menginap di Hotel belum diisi"); document.getElementById('e_tglsampai').focus(); return false;
                }
                
            }
            
            if (nchksewa.checked==true) {
                var etglsewa1=document.getElementById('e_tglsewa1').value;
                var etglsewa2=document.getElementById('e_tglsewa2').value;
                
                if (etglsewa1=="" && etglsewa2=="") {
                    alert("Data Sewa Kendaraan belum diisi"); document.getElementById('etglsewa1').focus(); return false;
                }
                
                if (etglsewa1=="") {
                    alert("Tanggal Mulai Sewa Kendaraan belum diisi"); document.getElementById('etglsewa1').focus(); return false;
                }
                
                if (etglsewa2=="") {
                    alert("Tanggal Sampai Sewa Kendaraan belum diisi"); document.getElementById('etglsewa2').focus(); return false;
                }
                
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
                document.getElementById("demo-form2").action = "module/mod_br_entrybrdcccab/aksi_entrybrdcccab.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
    
    $(document).ready(function() {
        ShowPajak();
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var act = urlku.searchParams.get("act");
        if (act=="tambahbaru") {
            TampilkanDataKaryawan();
        }    
    } );
    
    function ShowPajak(){
        var myurl = window.location;
        var urlku = new URL(myurl);
        var nact = urlku.searchParams.get("act");
        
        var epajak = document.getElementById('cb_pajak').value;

        if (epajak=="" || epajak=="N"){
            n_pajak1.style.display = 'none';
        }else{
            n_pajak1.style.display = 'block';
            if (nact!="editdata") {
                ShowInputJasa();
            }
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
    
    function cekBoxPilihDPP(nmcekbox){
        var nm = document.getElementById(nmcekbox);
        var chkjasa = document.getElementById('chk_jasa');
        var chkatrika = document.getElementById('chk_atrika');
        if (nm.checked) {
            if (nm.value=="jasa") {
                chkatrika.checked='';
            }else if (nm.value=="atrika") {
                chkjasa.checked='';
            }
        }
        ShowInputJasa();
    }
    
    function ShowInputJasa(){
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        if (echkjasa==true || echkatrika==true) {
            n_pajakjasa.style.display = 'block';
        }else{
            n_pajakjasa.style.display = 'none';
        }
        HitungJumlah();
    }
    
    
    function HitungJumlahDPP(){
        var newchar = '';
        var e_totrpdpp = "0";
        erpjmldpp = document.getElementById("e_rpjmljasa").value;
        if (erpjmldpp!="" && erpjmldpp != "0") {
            var nrpjmldpp = erpjmldpp; 
            nrpjmldpp = nrpjmldpp.split(',').join(newchar);
            e_totrpdpp=nrpjmldpp*10/100;
        }
        document.getElementById("e_jmldpp").value=e_totrpdpp;
        HitungJumlah();
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
        document.getElementById("e_jmlpph").value = "0";
        document.getElementById("e_jmlpph").value = "0";
        document.getElementById("e_jmlrppph").value = "0";
        

        
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
        
        
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        var erpjmljasa="0";
        if (echkjasa==true || echkatrika==true) {
            erpjmljasa=document.getElementById("e_rpjmljasa").value;
            var nrpjmljasa = erpjmljasa;
            erpjmljasa = nrpjmljasa.split(',').join(newchar);
            if (erpjmljasa=="") { erpjmljasa="0" }
        }
        
        
        
        var e_totrppph = "0";
        var epph = document.getElementById("cb_pph").value;
        
        if (epph!="") {
            ejmldpp = document.getElementById("e_jmldpp").value;
            if (ejmldpp!="" && ejmldpp != "0") {
                var njmldpp = ejmldpp; 
                njmldpp = njmldpp.split(',').join(newchar);

                
                var idpp_pilih=njmldpp;
                if (echkatrika==true) {
                    idpp_pilih=erpjmljasa;
                }
                
                e_totrppph = idpp_pilih;
                
                if (epph=="pph21") {
                    npph = "5";
                    e_totrppph = (idpp_pilih * npph / 100)*50/100;   
                }else if (epph=="pph23") {
                    npph = "2";
                    e_totrppph = (idpp_pilih * npph / 100);
                }
            }
        }
        document.getElementById("e_jmlrppph").value = e_totrppph;
        HitungJumlahUsulan();
    }


    function HitungJumlahUsulan(){

        var newchar = '';
        
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        var erpjmljasa="0";
        if (echkjasa==true || echkatrika==true) {
            erpjmljasa=document.getElementById("e_rpjmljasa").value;
            var nrpjmljasa = erpjmljasa;
            erpjmljasa = nrpjmljasa.split(',').join(newchar);
            if (erpjmljasa=="") { erpjmljasa="0" }
        }
        
        
        ejmldpp = document.getElementById("e_jmldpp").value;
        var e_totrpusulan = ejmldpp;
        erpppn = document.getElementById("e_jmlrpppn").value;
        erppph = document.getElementById("e_jmlrppph").value;
        erpbulat = document.getElementById("e_jmlbulat").value;
        erpmaterai = document.getElementById("e_jmlmaterai").value;
        if (erpppn=="") erpppn="0";
        if (erppph=="") erppph="0";
        if (erpbulat=="") erpbulat="0";
        if (erpmaterai=="") erpmaterai="0";

        var epph = document.getElementById("cb_pph").value;

        var njmldpp = ejmldpp; 
        njmldpp = njmldpp.split(',').join(newchar);

        var nrpppn = erpppn; 
        nrpppn = nrpppn.split(',').join(newchar);

        var nrppph = erppph; 
        nrppph = nrppph.split(',').join(newchar);

        var nrpbulat = erpbulat; 
        nrpbulat = nrpbulat.split(',').join(newchar);

        var nrpmaterai = erpmaterai; 
        nrpmaterai = nrpmaterai.split(',').join(newchar);
        
        var idpp_pilih=njmldpp;
        /*if (echkjasa==true) {
            idpp_pilih=erpjmljasa;
        }*/
        
        if (epph=="pph21" || epph=="pph23") {
            e_totrpusulan=( ( parseInt(idpp_pilih)+parseInt(nrpppn) - parseInt(nrppph) ) );
        }else{
            e_totrpusulan=( ( parseInt(idpp_pilih)+parseInt(nrpppn)));
        }
        e_totrpusulan=parseInt(e_totrpusulan)+parseInt(nrpbulat)+parseInt(nrpmaterai);
        
        
        
        if (echkjasa==true) {
            e_totrpusulan=parseInt(e_totrpusulan);//-parseInt(njmldpp)
            e_totrpusulan=parseInt(erpjmljasa)+parseInt(e_totrpusulan);
        }else if (echkatrika==true) {
            e_totrpusulan=parseInt(e_totrpusulan)-parseInt(njmldpp);
            e_totrpusulan=parseInt(erpjmljasa)+parseInt(e_totrpusulan);
        }
        
        
        document.getElementById("e_jmlusulan").value = e_totrpusulan;

    }
</script>





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