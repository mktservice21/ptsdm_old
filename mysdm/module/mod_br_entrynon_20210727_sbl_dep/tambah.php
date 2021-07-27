<!--<script src="module/mod_br_entrynon/mytransaksi.js"></script>-->
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

<script>
                    
    function ProsesDataHapusSatu(ket, noid, snodivi){

        ok_ = 1;
        if (ok_) {
            if (snodivi=="") {
                var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            }else{
                var r = confirm('Sudah Ada Nodivisi /no BR ('+snodivi+')...!!!\n\
Apakah akan melakukan proses '+ket+' ...?\n\
Status pada SPD akan berubah menjadi BATAL (merah)...');
            }
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

                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_entrynon/aksi_entrybrnon.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
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
//$pnmtabelkry="karyawan";
$pnmtabelkry="tempkaryawandccdss_inp";
                                                
$pusernid=$_SESSION['USERID'];

$hari_ini = date("Y-m-d");
$tglinput = date('d F Y', strtotime($hari_ini));
$tglinput = date('d/m/Y', strtotime($hari_ini));
                                    
$pidbrid="";

$pdivisiid="";

if ($pusernid==1043) {
    $pdivisiid="EAGLE";
}elseif ($pusernid==148) {
    $pdivisiid="HO";
}

$pcoa4="";
$pkodeid="";
$pkaryawanid=$_SESSION['IDCARD'];
$pnmkaryawan=$_SESSION['NAMALENGKAP'];
$pidcabang="0000000001";
$pnmcabang="ETH - HO";


$paktivitas1="";
$paktivitas2="";
$pccyid="";
$pnoslip="";
$plampiran="";
$pcapil="";
$pviapil="";
        
$rpjumlah="";
$rprelalisasi="";
$rpcn="";

$tgltrans="";
$pjnspajak="N";
$pkenapajak="";
$pnoseripajak="";
$ptglfakturpajak="";
$pjmldpp="";
$pjmldpp="";

$pjmlppn="";
$pjmlrpppn="";

$pjnspph="";
$pjmlpph="";
$pjmlrppph="";
$pjmlbulat="";
$pjmlmaterai="";
$pjenisdpp="";



$prpjumlahjasa=0;


$pchkjasa="";
$pchkatrika="";

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pact=$_GET['act'];

$act="input";                
if ($pact=="editdata") {
    $act="update";
    $pidbrid=$_GET['id'];
    
    $ncarisudahclosebrid=CariSudahClosingBRID1($pidbrid, "A");
    
    
    
    $edit = mysqli_query($cnmy, "SELECT * FROM hrd.br0 WHERE brId='$pidbrid'");
    $r    = mysqli_fetch_array($edit);
    $rpjumlah=$r['jumlah'];
    $rprelalisasi=$r['realisasi1'];
    $rpcn=$r['cn'];
    $tglinput = date('d F Y', strtotime($r['tgl']));
    $tglinput = date('d/m/Y', strtotime($r['tgl']));
    if (empty($r['tgltrans']) OR $r['tgltrans']=="0000-00-00"){
        $tgltrans = "";
    }else{
        $tgltrans = date('d F Y', strtotime($r['tgltrans']));
        $tgltrans = date('d/m/Y', strtotime($r['tgltrans']));
    }

    
    $pdivisiid=$r['divprodid'];
    $pcoa4=$r['COA4'];
    $pkodeid=$r['kode'];
    $pkaryawanid=$r['karyawanId'];
    $pidcabang=$r['icabangid'];
    $paktivitas1=$r['aktivitas1'];
    $paktivitas2=$r['aktivitas2'];
    $pccyid=$r['ccyId'];
    $pnoslip=$r['noslip'];
    $plampiran=$r['lampiran'];
    $pcapil=$r['ca'];
    $pviapil=$r['via'];


    

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

    $pcoa4=$r['COA4'];

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

$pnmkaryawan=getfield("select nama as lcfields from hrd.karyawan WHERE karyawanid='$pkaryawanid'");
$pnmcabang=getfield("select nama as lcfields from MKT.icabang WHERE icabangid='$pidcabang'");

?>


<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>
<div class='modal fade' id='myModal2' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_nobr").focus(); } </script>

<div class="">
    
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=$act&idmenu=$pidmenu"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
        
        
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $pmodule; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $pidmenu; ?>' Readonly>
            <input type='hidden' id='u_act' name='u_act' value='update' Readonly>
        
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                  
                    
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_nobr' name='e_nobr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbrid; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Tanggal </label>
                                    <div class='col-md-9'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='mytgl01' name='e_tglinput' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tglinput; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_divisi' name='cb_divisi' onchange="showCOANya()"><!--showKodeNyaNon('cb_divisi', 'cb_kode')-->
                                            <?PHP
                                            $query = "SELECT DivProdId, nama FROM MKT.divprod where br='Y' AND DivProdId NOT IN ('CAN', 'OTHER', 'OTC') OR DivProdId='$pdivisiid' order by nama";
                                            $tampil=mysqli_query($cnmy, $query);
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                $ndivid=$a['DivProdId'];
                                                $ndivnm=$a['nama'];
                                                
                                                if ($ndivid==$pdivisiid)
                                                    echo "<option value='$ndivid' selected>$ndivnm</option>";
                                                else
                                                    echo "<option value='$ndivid'>$ndivnm</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_coa'>Kode / COA <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_coa' name='cb_coa' onchange="showKodeNyaNon()">
                                            <option value='' selected>-- Pilihan --</option>
                                        <?PHP
										/*
                                            $query = "SELECT DISTINCT COA4, NAMA4 FROM dbmaster.v_coa where (DIVISI='$pdivisiid' or ifnull(DIVISI,'')='') AND "
                                                    . "( ((divprodid='$pdivisiid' and br = '') and (divprodid='$pdivisiid' and br<>'N'))   or ifnull(kodeid,'')='') order by COA4";
                                        */
                                            //untuk yang non
                                            $filternondssdccCOA=" AND (bk.br = '') and (bk.br<>'N') ";

                                            $query = "SELECT w.id, w.karyawanId, k.nama, w.COA4, c4.NAMA4,
                                                bk.br, bk.divprodid FROM dbmaster.coa_wewenang AS w
                                                LEFT JOIN hrd.karyawan AS k ON w.karyawanId = k.karyawanId
                                                LEFT JOIN dbmaster.coa_level4 AS c4 ON w.COA4 = c4.COA4
                                                LEFT JOIN dbmaster.br_kode AS bk ON c4.kodeid = bk.kodeid WHERE 
                                                bk.divprodid='$pdivisiid' $filternondssdccCOA";
												
												
											$query = "select DISTINCT d.DIVISI2, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, b.COA4, b.NAMA4, b.kodeid 
											   from dbmaster.coa_level4 b 
											   LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
											   LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
											   LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1 WHERE d.DIVISI2='$pdivisiid' AND IFNULL(b.kodeid,'')<>'' AND 
											   IFNULL(b.kodeid,'') NOT IN (select IFNULL(kodeid,'') from dbmaster.br_kode WHERE (br <> '' and br<>'N')) ";
											$query .=" ORDER BY b.COA4";
											
                                            $tampil=mysqli_query($cnmy, $query);
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                $nidcoa4=$a['COA4'];
                                                $nnmcoa4=$a['NAMA4'];
                                                
                                                if ($nidcoa4==$pcoa4)
                                                    echo "<option value='$nidcoa4' selected>$nnmcoa4</option>";
                                                else
                                                    echo "<option value='$nidcoa4'>$nnmcoa4</option>";
                                            }
											
											//OTHER
											$query = "select DISTINCT d.DIVISI2, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, b.COA4, b.NAMA4, b.kodeid 
											   from dbmaster.coa_level4 b 
											   LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
											   LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
											   LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1 WHERE d.DIVISI2 IN ('', 'OTHER', 'OTHERS') ";
											$query .=" ORDER BY b.COA4";
											
                                            $tampil=mysqli_query($cnmy, $query);
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                $nidcoa4=$a['COA4'];
                                                $nnmcoa4=$a['NAMA4'];
                                                
                                                if ($nidcoa4==$pcoa4)
                                                    echo "<option value='$nidcoa4' selected>$nnmcoa4</option>";
                                                else
                                                    echo "<option value='$nidcoa4'>$nnmcoa4</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_kode'>Kode <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_kode' name='cb_kode'>
                                            <option value="">-- Pilih --</option>
                                            <?PHP
                                                $query = "select kodeid,nama,divprodid from dbmaster.br_kode where (divprodid='$pdivisiid' and br = '')  "
                                                    . " and (divprodid='$pdivisiid' and br<>'N') order by nama";

                                                $tampil = mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    $nidkode=$a['kodeid'];
                                                    $nnmkode=$a['nama'];
													$nnmdivid=$a['divprodid'];
                                                
                                                    if ($a['kodeid']==$pkodeid)
                                                        echo "<option value='$nidkode' selected>$nnmkode - $nidkode ($nnmdivid)</option>";
                                                    else
                                                        echo "<option value='$nidkode'>$nnmkode - $nidkode ($nnmdivid)</option>";
                                                }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_idkaryawan' name='e_idkaryawan' onchange="showCabangMR()">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                
                                                $query ="SELECT DISTINCT b.karyawanId, b.nama FROM hrd.$pnmtabelkry b WHERE 1=1 ";
                                                $query .= " AND ( IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00' ) ";
                                                $query .= " AND b.karyawanid not in ('0000002083') ";
                                                $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ')  "
                                                        . " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
                                                        . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') "
                                                        . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ') ";
                                                $query .= " OR b.karyawanid='$pkaryawanid' ";
                                                $query .= " order by b.nama, b.karyawanId";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $nidkry=$a['karyawanId'];
                                                    $nnmkry=$a['nama'];
                                                    
                                                    if ($nidkry==$pkaryawanid)
                                                        echo "<option value='$nidkry' selected>$nnmkry</option>";
                                                    else
                                                        echo "<option value='$nidkry'>$nnmkry</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Cabang <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_idcabang' name='e_idcabang' onchange="showNamaCabang()">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $query = "select karyawan.iCabangId, cabang.nama from hrd.$pnmtabelkry as karyawan join dbmaster.icabang as cabang on "
                                                        . " karyawan.icabangid=cabang.icabangid where karyawanId='$pkaryawanid'";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $nidcab=$a['iCabangId'];
                                                    $nnmcab=$a['nama'];
                                                    
                                                    if ($nidcab==$pidcabang)
                                                        echo "<option value='$nidcab' selected>$nnmcab</option>";
                                                    else
                                                        echo "<option value='$nidcab'>$nnmcab</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Nama <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_nmkaryawan' name='e_nmkaryawan' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pnmkaryawan; ?>' Readonly>
                                        <input type='text' id='e_nmcabang' name='e_nmcabang' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pnmcabang; ?>' Readonly>
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
                    <!--END kiri-->
                    
                    
                    
                    
                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Mata Uang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' name='cb_jenis'>
                                            <?php
                                            $tampil=mysqli_query($cnmy, "SELECT ccyId, nama FROM dbmaster.ccy");
                                            while($c=mysqli_fetch_array($tampil)){
                                                $nidccy=$c['ccyId'];
                                                $nnmccy=$c['nama'];
                                                    
                                                if ($nidccy==$pccyid)
                                                    echo "<option value='$nidccy' selected>$nidccy - $nnmccy</option>";
                                                else {
                                                    if ($nidccy=="IDR")
                                                        echo "<option value='$nidccy' selected>$nidccy - $nnmccy</option>";
                                                    else    
                                                        echo "<option value='$nidccy'>$nidccy - $nnmccy</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
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
                                                <select class='soflow' name='cb_pph' id='cb_pph' onchange="ShowPPH()">
                                                    <?php
                                                    $ketPPH21="PPH21 (DPP*5%*50%) atau (JML AWAL*5%*50%)";
                                                    $ketPPH23="PPH23 (DPP*2%) atau (JML AWAL*2%)";
                                                    
                                                    $ketPPH22="PPH21 (DPP*6%*50%) atau (JML AWAL*6%*50%)";
                                                    
                                                    if ($pjnspph=="pph21") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21' selected>$ketPPH21</option>";
                                                        echo "<option value='pph23'>$ketPPH23</option>";
                                                        echo "<option value='pph22'>$ketPPH22</option>";
                                                    }elseif ($pjnspph=="pph23") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21'>$ketPPH21</option>";
                                                        echo "<option value='pph23' selected>$ketPPH23</option>";
                                                        echo "<option value='pph22'>$ketPPH22</option>";
                                                    }elseif ($pjnspph=="pph22") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21'>$ketPPH21</option>";
                                                        echo "<option value='pph23'>$ketPPH23</option>";
                                                        echo "<option value='pph22' selected>$ketPPH22</option>";
                                                    }else{
                                                        echo "<option value='' selected></option>";
                                                        echo "<option value='pph21'>$ketPPH21</option>";
                                                        echo "<option value='pph23'>$ketPPH23</option>";
                                                        echo "<option value='pph22'>$ketPPH22</option>";
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


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value="<?PHP echo $rpjumlah; ?>">
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Realisasi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_realisasi' name='e_realisasi' class='form-control col-md-7 col-xs-12' value="<?PHP echo $rprelalisasi; ?>">
                                    </div>
                                </div>
                                
                                
                                
                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CN <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_cn' name='e_cn' class='form-control col-md-7 col-xs-12 inputmaskrp2' value="<?PHP echo $rpcn; ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Slip <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_noslip' name='e_noslip' class='form-control col-md-7 col-xs-12' value="<?PHP echo $pnoslip; ?>">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl02'>Tanggal Transfer </label>
                                    <div class='col-md-9'>
                                        <div class='input-group date' id='mytgl02'>
                                            <input type="text" class="form-control" id='e_tgltrans' name='e_tgltrans' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgltrans; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <?PHP $chklam=""; if ($plampiran=="Y") $chklam="checked"; ?>
                                            <?PHP $chkca=""; if ($pcapil=="Y") $chkca="checked"; ?>
                                            <?PHP $chkvia=""; if ($pviapil=="Y") $chkvia="checked"; ?>
                                            <label><input type="checkbox" value="lapiran" name="cx_lapir" <?PHP echo $chklam; ?>> Lampiran </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="ca" name="cx_ca" <?PHP echo $chkca; ?>> PC-M </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="via" name="cx_via" <?PHP echo $chkvia; ?>> Via Surabaya </label>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
										
                                            <?PHP
                                            if ($pact=="editdata") {
                                                if ($ncarisudahclosebrid==true) {
                                                    echo "<span style='color:red;'>BR tersebut sudah closing SURABAYA tidak bisa diubah....</span>";
                                                    echo "<a class='btn btn-default' href='?module=$pmodule&idmenu=$pidmenu&act=$pidmenu'>Back</a>";
                                                }else{
                                                    echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm('edit', 'Edit Data... ?')\">Update</button>";
                                                    echo "<a class='btn btn-default' href='?module=$pmodule&idmenu=$pidmenu&act=$pidmenu'>Back</a>";
                                                }
                                            }else{
                                                echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm('simpan', 'Simpan ?')\">Save</button>";
                                                echo "<a class='btn btn-default' href='?module=$pmodule&idmenu=$pidmenu&act=$pidmenu'>Back</a>";
                                            }
                                            ?>
											
											
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                            </div>
                        </div>
                    </div>
                    <!--END kanan-->
                    
                    
                </div>
            </div>
            
            
            
        
        </form>
        
        
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_content'>
                <div class='x_panel'>
                    <b>Data yang terakhir diinput (max 5 data)</b>
                    <table id='datatable' class='table table-striped table-bordered' width='100%'>
                        <thead>
                            <tr>
                                <th></th><th width='60px'>No ID</th>
                                <th width='60px'>Tanggal</th><th width='60px'>Tgl. Transfer</th><th>Keterangan</th>
                                <th width='80px'>Yg Membuat</th><th width='50px'>Jumlah</th><th width='50px'>Realisasi</th>
                                <th width='50px'>Nm Realisasi</th><th>Kode</th><th>No Slip</th>

                            </tr>
                        </thead>
                        <body>
                            <?PHP
                            
                            $sql = "SELECT brId, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(tgltrans,'%d %M %Y') as tgltrans, DATE_FORMAT(tgltrm,'%d %M %Y') as tgltrm, "
                                    . "nama, nama_kode, nama_cabang, FORMAT(jumlah,2,'de_DE') as jumlah, FORMAT(jumlah1,2,'de_DE') as jumlah1, realisasi1, "
                                    . "dokterId,nama_dokter, "
                                    . "FORMAT(cn,2,'de_DE') as cn, "
                                    . "noslip, aktivitas1 ";
                            $sql.=" FROM dbmaster.v_br0 ";
                            $sql.=" WHERE 1=1 and user1=$pusernid ";
                            $sql.=" and (br = '' and br<>'N') ";
                            $sql.=" and brId not in (select distinct ifnull(brId,'') from hrd.br0_reject) ";
                            $sql.=" order by brId desc limit 5 ";
                            $tampil=mysqli_query($cnmy, $sql);
                            while ($xc=  mysqli_fetch_array($tampil)) {
                                $fnoid=$xc["brId"];
                                $faksi = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$fnoid'>Edit</a>"
                                        . "<button class='btn btn-danger btn-xs'"
                                        . "onClick=\"ProsesDataHapusSatu('hapus', '$fnoid', '')\">Hapus</button>";
                                $ftgl = $xc["tgl"];
                                $ftgltrans = $xc["tgltrans"];
                                $ftgltrm = $xc["tgltrm"];
                                $fket1 = $xc["aktivitas1"];
                                $fnamakry = "<a href='#' title=".$xc['nama_cabang'].">".$xc["nama"]."</a>";
                                $fjuml = $xc["jumlah"];
                                $fjuml1 = $xc["jumlah1"];
                                $freal = $xc["realisasi1"];
                                $fnoslip = $xc["noslip"];
                                $fnamakode = $xc["nama_kode"];
                                echo "<tr>";
                                echo "<td>$faksi</td>";
                                echo "<td>$fnoid</td>";
                                echo "<td>$ftgl</td>";
                                echo "<td>$ftgltrans</td>";
                                echo "<td>$fket1</td>";
                                echo "<td>$fnamakry</td>";
                                echo "<td>$fjuml</td>";
                                echo "<td>$fjuml1</td>";
                                echo "<td>$freal</td>";
                                echo "<td>$fnamakode</td>";
                                echo "<td>$fnoslip</td>";
                                echo "</tr>";
                            }
                            ?>
                        </body>
                    </table>

                </div>
            </div>
        </div>
        
        
        
        
    </div>
    
    
</div>


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
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var idact = urlku.searchParams.get("act");
        
        //baru kalau salah delete aja
        if (idact=="tambahbaru") {
            showCOANya();
        }
        //END baru kalau salah delete aja
        
        
        var table = $('#datatable').DataTable({
            fixedHeader: true,
            "ordering": false,
            "columnDefs": [
                { "visible": false },
                { className: "text-right", "targets": [6,7] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10] }//nowrap

            ],
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
    } );
            
            
    function showCOANya(){
        var ediv = document.getElementById('cb_divisi').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrynon/viewdata.php?module=viewdatacombocoa",
            data:"udiv="+ediv,
            success:function(data){
                $("#cb_coa").html(data);
                $("#cb_kode").html("<option value=''>--Pilihan--</option>");
                
                showCabangMR();
            }
        });
    }
    
    function showKodeNyaNon(){
        var ediv = document.getElementById('cb_divisi').value;
        var ecoa = document.getElementById('cb_coa').value;

        $.ajax({
            type:"post",
            url:"module/mod_br_entrynon/viewdata.php?module=viewdatacombokodenon",
            data:"udiv="+ediv+"&ucoa="+ecoa,
            success:function(data){
            $("#cb_kode").html(data);
            }
        });
    }
    
    function showCabangMR() {
        var icar = document.getElementById('e_idkaryawan').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrynon/viewdata.php?module=viewdatacabangkaryawan",
            data:"umr="+icar,
            success:function(data){
                $("#e_idcabang").html(data);
                showNamaKaryawan();
                showNamaCabang();
            }
        });
    }
    
    function showNamaKaryawan() {
        var icar = document.getElementById('e_idkaryawan').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrynon/viewdata.php?module=viewdatanamakaryawan",
            data:"ucar="+icar,
            success:function(data){
                document.getElementById('e_nmkaryawan').value=data;
            }
        });
    }
    
    function showNamaCabang() {
        var iidcab = document.getElementById('e_idcabang').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrynon/viewdata.php?module=viewdatanamacabang",
            data:"uidcab="+iidcab,
            success:function(data){
                document.getElementById('e_nmcabang').value=data;
            }
        });
    }
    
    

    function disp_confirm(ket, pText_)  {
        var ecab =document.getElementById('e_idcabang').value;
        var ebuat =document.getElementById('e_idkaryawan').value;
        var edivi =document.getElementById('cb_divisi').value;
        var ekode =document.getElementById('cb_kode').value;
        var ecoa =document.getElementById('cb_coa').value;
        var ejumlah =document.getElementById('e_jmlusulan').value;
        
        var iidnmkaryawan = document.getElementById('e_nmkaryawan').value;
        var iidnmcabang = document.getElementById('e_nmcabang').value;

        if (edivi==""){
            alert("divisi masih kosong....");
            return 0;
        }
        if (ekode==""){
            alert("kode masih kosong....");
            return 0;
        }
        if (ecoa==""){
            alert("coa masih kosong....");
            return 0;
        }
        if (ebuat==""){
            alert("yang membuat masih kosong....");
            return 0;
        }
        if (ecab==""){
            alert("cabang masih kosong....");
            //return 0;
        }
        if (ejumlah==""){
            alert("jumlah masih kosong....");
            document.getElementById('e_jmlusulan').focus();
            return 0;
        }
        
        pText_ = "Divisi : "+edivi+"\n\
Karyawan : "+iidnmkaryawan+"\n\
Cabang : "+iidnmcabang+"\n\
Jumlah Rp. : "+ejumlah+"\n\
-------------------------------------------------\n\
Apakah Akan melakukan simpan...???";

        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                            
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_br_entrynon/aksi_entrybrnon.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var idact = urlku.searchParams.get("act");
        
        //baru kalau salah delete aja
        if (idact=="editdata") {
            var epajak = document.getElementById('cb_pajak').value;
            if (epajak=="" || epajak=="N"){
            }else{
                cekBoxPilihDPP('chk_jasa');
                cekBoxPilihDPP('chk_atrika');
            }
        }
        //END baru kalau salah delete aja
        
        
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
            }else{//baru kalau salah delete aja
                cekBoxPilihDPP('chk_jasa');
                cekBoxPilihDPP('chk_atrika');
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
        }else if (epph=="pph22") {
            document.getElementById("e_jmlpph").value = "6";
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
                }else if (epph=="pph22") {
                    npph = "6";
                    e_totrppph = (idpp_pilih * npph / 100)*50/100;
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
        
        if (epph=="pph21" || epph=="pph23" || epph=="pph22") {
            e_totrpusulan=( ( parseFloat(idpp_pilih)+parseFloat(nrpppn) - parseFloat(nrppph) ) );
        }else{
            e_totrpusulan=( ( parseFloat(idpp_pilih)+parseFloat(nrpppn)));
        }
        e_totrpusulan=parseFloat(e_totrpusulan)+parseFloat(nrpbulat)+parseFloat(nrpmaterai);
        
        
        
        if (echkjasa==true) {
            e_totrpusulan=parseFloat(e_totrpusulan);//-parseFloat(njmldpp)
            e_totrpusulan=parseFloat(erpjmljasa)+parseFloat(e_totrpusulan);
        }else if (echkatrika==true) {
            e_totrpusulan=parseFloat(e_totrpusulan)-parseFloat(njmldpp);
            e_totrpusulan=parseFloat(erpjmljasa)+parseFloat(e_totrpusulan);
        }
        
        
        //document.getElementById("e_jmlusulan").value = e_totrpusulan;
		document.getElementById("e_jmlusulan").value = parseFloat(e_totrpusulan).toFixed(2);

    }
</script>