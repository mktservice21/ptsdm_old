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

<?php

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pact=$_GET['act'];
$hari_ini = date("Y-m-d");
$iduser=$_SESSION['USERID']; 
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP'];
$padmkhusus=$_SESSION['ADMINKHUSUS'];


$act="editnonuser";
$pidklaim=$_GET['id'];
$edit = mysqli_query($cnmy, "SELECT * FROM hrd.klaim WHERE klaimId='$pidklaim'");
$r    = mysqli_fetch_array($edit);

$ptglinp=$r['tgl'];
$ptgltrs=$r['tgltrans'];

$idajukan=$r['karyawanid'];
$piddistrb=$r['distid']; 
$pjumlah=$r['jumlah'];
$prealisasi=$r['realisasi1'];
$pnoslip=$r['noslip'];
$plamp=$r['lampiran'];
$paktivitas1=$r['aktivitas1'];
$paktivitas2=$r['aktivitas2'];
$pdivpengajuan=$r['pengajuan'];
$pcoa=$r['COA4'];

$pjnspajak=$r['pajak'];
$pkenapajak=$r['nama_pengusaha'];
$pnoseripajak=$r['noseri'];
$ptglfp=$r['tgl_fp'];
$pjmldpp=$r['dpp'];
$pjmlppn=$r['ppn'];
$pjmlrpppn=$r['ppn_rp'];

$pjnspph=$r['pph_jns'];
$pjmlpph=$r['pph'];
$pjmlrppph=$r['pph_rp'];
$pjmlbulat=$r['pembulatan'];


$ptglfakturpajak="";
if ($ptglfp=="0000-00-00") $ptglfp = "";
if (!empty($ptglfp)) $ptglfakturpajak = date('d/m/Y', strtotime($ptglfp));
    

$tglinput = date('d/m/Y', strtotime($ptglinp));
$tgltrans="";
if ($ptgltrs=="0000-00-00") $ptgltrs="";
if (!empty($ptgltrs)) $tgltrans=date('d/m/Y', strtotime($ptgltrs));

$nmajukan=getfield("select nama as lcfields from hrd.karyawan WHERE karyawanid='$idajukan'");
$namadist=getfield("select nama as lcfields from MKT.distrib0 WHERE distid='$piddistrb'");

$plampiran="";
if ($plamp=="Y") $plampiran="checked";

if ($pdivpengajuan=="ETH") {   
    $pdivpengajuan="CAN";
}



?>

<script> window.onload = function() { document.getElementById("e_klaimid").focus(); } </script>


<div class="">
    
    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=editnonuser&idmenu=$pidmenu"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    
                    <!-- kiri -->
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class='input-group date' id='mytgl01x'>
                                            <input type="text" class="form-control" id='e_tglinput' name='e_tglinput' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tglinput; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Yang Membuat <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-9'>
                                        <input type='hidden' id='e_idkaryawan' name='e_idkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idajukan; ?>' Readonly>
                                        <input type='text' id='e_nmkaryawan' name='e_nmkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nmajukan; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Distributor <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-9'>
                                        <input type='hidden' id='e_iddist' name='e_iddist' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piddistrb; ?>' Readonly>
                                        <input type='text' id='e_nmdist' name='e_nmdist' class='form-control col-md-7 col-xs-12' value='<?PHP echo $namadist; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Aktivitas <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea readonly class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Aktivitas'><?PHP echo $paktivitas1; ?></textarea>
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
                    <!-- end kiri -->
                    
                    <!-- kanan -->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Pajak <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div style="margin-bottom:2px;">
                                            <select class='soflow' name='cb_pajak' id='cb_pajak' onchange="ShowPajak()">
                                                <?php
                                                echo "<option value='Y' selected>Y</option>";
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
                                            <input type='text' id='e_jmldpp' name='e_jmldpp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmldpp; ?>' onblur="HitungJumlah()" Readonly>
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">PPN (%) <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmlppn' name='e_jmlppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlppn; ?>' onblur="HitungPPN()"  Readonly>
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
                                            <input type='text' id='e_jmlbulat' name='e_jmlbulat' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlbulat; ?>' onblur="HitungJumlahUsulan()" Readonly>
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>' Readonly>
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <?PHP if ($pact=="tambahbaru") { ?>
                                                <a class='btn btn-default' href="<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
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
                    <!-- end kanan -->
                    
                    
                    
                </div>
            </div>
            
            
            
        </form>
    </div>
    <!--end row-->
    
</div>



<script>
    
    
    function disp_confirm(pText_, ket)  {
        var eid =document.getElementById('e_klaimid').value;

        if (eid==""){
            alert("ID kosong....");
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