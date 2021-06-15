<?php

$pidbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d F Y', strtotime($hari_ini));
$eperiode1 = date('01 F Y', strtotime('-2 month', strtotime($hari_ini)));
//$eperiode1 = date('01 F Y', strtotime($hari_ini));
$eperiode2 = date('t F Y', strtotime($hari_ini));

$ptglpo = date('d F Y', strtotime($hari_ini));
$ptglkirim = date('d F Y', strtotime($hari_ini));


$pidcardpl=$_SESSION['IDCARD'];
$idajukan=$_SESSION['IDCARD'];


$pidbayar="";
$pidsup="";
$pnotes_kirim="";
$pnotes="";

$pjmldpp=0;
$pppn_h=10;
$pppn_hrp=0;
$pdisc_h=0;
$pdisc_hrp=0;
$ppembulatan_h="";
$pjumlah_h=0;
$pjmlbayar_h=0;

$pjnspph="";
$pjmlpph="";
$pjmlrppph="";

$pselppn1="selected";
$pselppn2="";

$sudahapv="";

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pact=$_GET['act'];
$act="input";

if ($pact=="editdata"){
    include "config/fungsi_ubahget_id.php";
    
    $act="update";
    $pidbr_ec=$_GET['id'];
    $pidbr = decodeString($pidbr_ec);
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbpurchasing.t_po_transaksi WHERE idpo='$pidbr'");
    $r    = mysqli_fetch_array($edit);
    
    
    $ptglpo=$r['tanggal'];
    $ptglkirim=$r['tglkirim'];
    
    $ptglpo = date('d F Y', strtotime($ptglpo));
    $ptglkirim = date('d F Y', strtotime($ptglkirim));


    $pidbayar=$r['idbayar'];
    $pidsup=$r['kdsupp'];
    $pnotes_kirim=$r['note_kirim'];
    $pnotes=$r['notes'];
    
    $pjmldpp=$r['dpp'];
    //$pppn_h=$r['ppn'];
    $pppn_hrp=$r['ppnrp'];
    $pdisc_h=$r['disc'];
    $pdisc_hrp=$r['discrp'];
    $ppembulatan_h=$r['pembulatan'];
    
    $pjnspph=$r['pph_jns'];
    $pjmlpph=$r['pph'];
    $pjmlrppph=$r['pph_rp'];
    
    $pjmlbayar_h=$r['totalrp'];
    
    
    if ((DOUBLE)$pppn_h<>0) {
        //$pppn_h=ROUND($pppn_h,2);
        $pselppn1="";
        $pselppn2="selected";
    }
    
    $query = "select SUM(IFNULL(cc.jumlah,0)*IFNULL(cc.harga,0)) as ttotal from dbpurchasing.t_po_transaksi_d as aa "
            . " JOIN dbpurchasing.t_po_transaksi as bb on aa.idpo=bb.idpo "
            . " JOIN dbpurchasing.t_pr_transaksi_po as cc on aa.idpr_po=cc.idpr_po WHERE IFNULL(bb.stsnonaktif,'')<>'Y' AND aa.idpo='$pidbr'";
    $ptampil=mysqli_query($cnmy, $query);
    $rx    = mysqli_fetch_array($ptampil);
    
    $pjumlah_h=$rx['ttotal'];
    
    
}

?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>


<div class="">
    

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
        
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID PO <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                        <input type='hidden' id='e_idcardlogin' name='e_idcardlogin' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcardpl; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal PO</label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptglpo; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Vendor <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                          <select class='form-control input-sm' id='cb_supplier' name='cb_supplier' onchange="ShowDataVendor()" data-live-search="true">
                                              <?PHP
                                              if ($pact=="tambahbaru") {
                                                $query = "select DISTINCT a.kdsupp, a.kdsupp as kdsupp, c.NAMA_SUP as nama_supp 
                                                    from dbpurchasing.t_pr_transaksi_po as a JOIN dbpurchasing.t_pr_transaksi as b on a.idpr=b.idpr JOIN dbmaster.t_supplier as c on a.kdsupp=c.KDSUPP WHERE 1=1 
                                                    AND IFNULL(b.stsnonaktif,'')<>'Y' 
                                                    AND IFNULL(c.AKTIF,'')<>'N' 
                                                    AND IFNULL(a.aktif,'')='Y' 
                                                    AND CONCAT(a.idpr, a.idbarang, a.idpr_d) NOT IN 
                                                    (select CONCAT(IFNULL(c.idpr,''), IFNULL(c.idbarang, ''), IFNULL(c.idpr_d,'')) as id 
                                                    from dbpurchasing.t_po_transaksi_d as a JOIN dbpurchasing.t_po_transaksi as b on a.idpo=b.idpo 
                                                    JOIN dbpurchasing.t_pr_transaksi_po as c on a.idpr_po=c.idpr_po WHERE IFNULL(b.stsnonaktif,'')<>'Y')
                                                    order by c.NAMA_SUP";
                                                }else{
                                                    $query = "select distinct KDSUPP as kdsupp, NAMA_SUP as nama_supp from dbmaster.t_supplier WHERE KDSUPP='$pidsup' order by NAMA_SUP";
                                                }
                                                $tampil = mysqli_query($cnmy, $query);
                                                $ketemu=mysqli_num_rows($tampil);
                                                if ((DOUBLE)$ketemu==0) echo "<option value='' selected>-- Pilih --</option>";
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pnidsup=$z['kdsupp'];
                                                    $pnnmsup=$z['nama_supp'];
                                                    
                                                    if ($pnidsup==$pidsup)
                                                        echo "<option value='$pnidsup' selected>$pnnmsup ($pnidsup)</option>";
                                                    else
                                                        echo "<option value='$pnidsup'>$pnnmsup ($pnidsup)</option>";
                                                }
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pembayaran <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                          <select class='form-control input-sm' id='cb_bayar' name='cb_bayar' onchange="" data-live-search="true">
                                              <?PHP
                                                $query = "select distinct idbayar, nama_bayar from dbpurchasing.t_jenis_bayar order by nama_bayar";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pnidbayar=$z['idbayar'];
                                                    $pnnmbayar=$z['nama_bayar'];
                                                    
                                                    if ($pnidbayar==$pidbayar)
                                                        echo "<option value='$pnidbayar' selected>$pnnmbayar</option>";
                                                    else
                                                        echo "<option value='$pnidbayar'>$pnnmbayar</option>";
                                                }
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal Kirim</label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_tglkirim' name='e_tglkirim' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptglkirim; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Notes Kirim <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_noteskirim' name='e_noteskirim' class='form-control col-md-7 col-xs-12' maxlength="200" value="<?PHP echo $pnotes_kirim; ?>">
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Remarks <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <textarea class='form-control' id="e_notes" name='e_notes' rows="5" maxlength="500"><?PHP echo $pnotes; ?></textarea>
                                    </div>
                                </div>
                                
                                
                                <div id="c_input">
                                    <div class='form-group'>
                                        <div id='loading2'></div>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <button type='button' class='btn btn-info btn-xs' onclick='CariData()'>Tampilkan Data</button> <span class='required'></span>
                                        </label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah_h; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DPP <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmldpp' name='e_jmldpp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmldpp; ?>' onblur="HitungDPP()">
                                    </div>
                                </div>
                                
                                
                                <div id="c_input">
                                    <div class='form-group'>
                                        <div id='loading2'></div>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Discount (%) <span class='required'></span></label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_jmldisc' name='e_jmldisc' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pdisc_h; ?>' onblur="HitungDiscount()">
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="c_input">
                                    <div class='form-group'>
                                        <div id='loading2'></div>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sub Total <span class='required'></span></label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_jmldiscrp' name='e_jmldiscrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pdisc_hrp; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PPN <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                          <select class='form-control input-sm' id='cb_ppn' name='cb_ppn' onchange="HitungPPN()" data-live-search="true">
                                              <?PHP
                                                echo "<option value='0' $pselppn1>None</option>";
                                                echo "<option value='$pppn_h' $pselppn2>Include ($pppn_h%)</option>";
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                <div hidden id="c_input">
                                    <div class='form-group'>
                                        <div id='loading2'></div>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PPN (Rp.) <span class='required'></span></label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_jmlppnrp' name='e_jmlppnrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pppn_hrp; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div hidden id="c_input">
                                    <div class='form-group'>
                                        <div id='loading2'></div>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pembulatan <span class='required'></span></label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_jmlbulat' name='e_jmlbulat' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ppembulatan_h; ?>' onblur="HitungPembulatan()">
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PPH <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div style="margin-bottom:2px;">
                                            <select class='soflow' name='cb_pph' id='cb_pph' onchange="ShowPPH()">
                                                <?php
                                                //$ketPPH21="PPH21 (DPP*5%*50%) atau (JML AWAL*5%*50%)";
                                                //$ketPPH23="PPH23 (DPP*2%) atau (JML AWAL*2%)";

                                                //$ketPPH22="PPH21 (DPP*6%*50%) atau (JML AWAL*6%*50%)";
                                                
                                                $ketPPHbd="PPH Badan (DPP*2%/100)";
                                                $ketPPHpr="PPH Perorangan (DPP*4%/100)";
                                                
                                                if ($pjnspph=="pph21") {
                                                    echo "<option value=''></option>";
                                                    echo "<option value='pph21' selected>$ketPPH21</option>";
                                                    echo "<option value='pph23'>$ketPPH23</option>";
                                                    echo "<option value='pph22'>$ketPPH22</option>";
                                                    echo "<option value='pphbd'>$ketPPHbd</option>";
                                                    echo "<option value='pphpr'>$ketPPHpr</option>";
                                                }elseif ($pjnspph=="pph23") {
                                                    echo "<option value=''></option>";
                                                    echo "<option value='pph21'>$ketPPH21</option>";
                                                    echo "<option value='pph23' selected>$ketPPH23</option>";
                                                    echo "<option value='pph22'>$ketPPH22</option>";
                                                    echo "<option value='pphbd'>$ketPPHbd</option>";
                                                    echo "<option value='pphpr'>$ketPPHpr</option>";
                                                }elseif ($pjnspph=="pph22") {
                                                    echo "<option value=''></option>";
                                                    echo "<option value='pph21'>$ketPPH21</option>";
                                                    echo "<option value='pph23'>$ketPPH23</option>";
                                                    echo "<option value='pph22' selected>$ketPPH22</option>";
                                                    echo "<option value='pphbd'>$ketPPHbd</option>";
                                                    echo "<option value='pphpr'>$ketPPHpr</option>";
                                                }elseif ($pjnspph=="pphbd") {
                                                    echo "<option value=''></option>";
                                                    //echo "<option value='pph21'>$ketPPH21</option>";
                                                    //echo "<option value='pph23'>$ketPPH23</option>";
                                                    //echo "<option value='pph22'>$ketPPH22</option>";
                                                    echo "<option value='pphbd' selected>$ketPPHbd</option>";
                                                    echo "<option value='pphpr'>$ketPPHpr</option>";
                                                    
                                                }elseif ($pjnspph=="pphpr") {
                                                    echo "<option value=''></option>";
                                                    //echo "<option value='pph21'>$ketPPH21</option>";
                                                    //echo "<option value='pph23'>$ketPPH23</option>";
                                                    //echo "<option value='pph22'>$ketPPH22</option>";
                                                    echo "<option value='pphbd'>$ketPPHbd</option>";
                                                    echo "<option value='pphpr' selected>$ketPPHpr</option>";
                                                }else{
                                                    echo "<option value='' selected></option>";
                                                    //echo "<option value='pph21'>$ketPPH21</option>";
                                                    //echo "<option value='pph23'>$ketPPH23</option>";
                                                    //echo "<option value='pph22'>$ketPPH22</option>";
                                                    echo "<option value='pphbd'>$ketPPHbd</option>";
                                                    echo "<option value='pphpr'>$ketPPHpr</option>";
                                                }
                                                ?>
                                            </select>
                                            <input type='hidden' id='e_jmlpph' name='e_jmlpph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlpph; ?>' readonly>
                                            <input type='hidden' id='e_jmlrppph' name='e_jmlrppph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrppph; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div id="c_input">
                                    <div class='form-group'>
                                        <div id='loading2'></div>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total Bayar (Rp.) <span class='required'></span></label>
                                        <div class='col-md-3'>
                                            <input type='text' id='e_jmlbayarrp' name='e_jmlbayarrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlbayar_h; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        
        
            <div id='loading3'></div>
            <div id="s_div">
                
                
            </div>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                <?PHP
                if (empty($sudahapv)) {
                    if ($pact=="editdata") {
                        ?>
                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Update</button>
                        <?PHP
                    }else{
                        echo "<div class='col-sm-5'>";
                        include "module/purchasing/pch_purchaseorder/ttd_po.php";
                        echo "</div>";
                    }
                ?>
                <?PHP
                }elseif ($sudahapv=="reject") {
                    echo "data sudah hapus";
                }else{
                    echo "tidak bisa diedit, sudah approve";
                }
                ?>
                </div>
            </div>
            
        </form>
        
    </div>    
    
    
</div>


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
    .ui-datepicker-calendar2 {
        display: none;
    }
</style>

<script type="text/javascript">
    $(function() {
        $('#e_tglberlaku_, #e_tglkirim').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                //ShowNoBukti();
            } 
        });
    });
</script>


<script>
    
    $(document).ready(function() {
            
        <?PHP if ($pact=="editdata"){ ?>
                CariData();
        <?PHP } ?>
            
    } );
    
    function CariData()  {
        //document.getElementById('e_jmlusulan_kb').value=0;
        var eidinput =document.getElementById('e_id').value;
        var esupp=document.getElementById('cb_supplier').value;
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        if (esupp=="") {
            alert("Vendor harus diisi...!!!");
            return false;
        }
        
        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/purchasing/pch_purchaseorder/dataprsupp.php?module=viewdataprpo&ket=detail",
            data:"uact="+iact+"&uidinput="+eidinput+"&usupp="+esupp,
            success:function(data){
                $("#s_div").html(data);
                $("#loading3").html("");
                //HitungTotalDariCekBox();
            }
        });
        
    }
    
    function HitungDPP() {
        HitungJumlahUsulan();
        
    }
    
    function HitungPPN() {
        HitungJumlahUsulan();
        
    }
    
    function HitungDiscount() {
        HitungJumlahUsulan();
        
    }
    
    function HitungPembulatan() {
        HitungJumlahUsulan();
    }
    
    
    function ShowPPH(){
        HitungJumlahUsulan();
    }
    
    
    function HitungJumlahUsulan(){
        var newchar = '';
        var ejmlusul = document.getElementById("e_jmlusulan").value;
        if (ejmlusul=="") ejmlusul="0";
        ejmlusul = ejmlusul.split(',').join(newchar);
        
        
        var ejmldpp = document.getElementById("e_jmldpp").value;
        if (ejmldpp=="") ejmldpp="0";
        ejmldpp = ejmldpp.split(',').join(newchar);
        
        var epph = document.getElementById("cb_pph").value;
        var idpp_pilih=ejmlusul;//e_jmldpp dari jml ususl, biasanya dari dpp
        var ipajak="N";
        if (epph=="pph21" || epph=="pph23" || epph=="pph22" || epph=="pphbd" || epph=="pphpr") {
            ipajak="Y";
            idpp_pilih=ejmldpp;//e_jmldpp dari jml ususl, biasanya dari dpp
        }
        
        //DISCOUNT
        
        var idisc = document.getElementById('e_jmldisc').value;
        if (idisc=="") idisc="0";
        idisc = idisc.split(',').join(newchar);
        
        var itotaldisc="0";
        itotaldisc=parseFloat(idisc)*parseFloat(ejmlusul)/100;
        document.getElementById('e_jmldiscrp').value=itotaldisc;
        
        //END DISCOUNT
        
        //PPN
        var ippn = document.getElementById('cb_ppn').value;
        if (ippn=="") ippn="0";
        ippn = ippn.split(',').join(newchar);
        
        var itotalppn="0";
        itotalppn=parseFloat(ippn)*parseFloat(idpp_pilih)/100;
        document.getElementById('e_jmlppnrp').value=itotalppn;
        //END PPN
        
        //PPH
        
        document.getElementById("e_jmlpph").value = "0";
        document.getElementById("e_jmlpph").value = "0";
        document.getElementById("e_jmlrppph").value = "0";
        
        
        var npph="0";
        var itotalpph = "0";
        
        if (epph!="") {
            
            if (epph=="pph21") {
                npph="5";
            }else if (epph=="pph23") {
                npph="2";
            }else if (epph=="pph22") {
                npph="6";
            }else if (epph=="pphbd") {
                npph="2";
            }else if (epph=="pphpr") {
                npph="4";
            }else{
            }
            
            
            
            if (ipajak=="Y" && idpp_pilih!="" && idpp_pilih != "0") {
                
                if (epph=="pph21") {
                    itotalpph = (parseFloat(idpp_pilih) * parseFloat(npph) / 100)*50/100;
                }else if (epph=="pph23") {
                    itotalpph = (parseFloat(idpp_pilih) * parseFloat(npph) / 100);
                }else if (epph=="pph22") {
                    itotalpph = (parseFloat(idpp_pilih) * parseFloat(npph) / 100)*50/100;
                }else if (epph=="pphbd") {
                    itotalpph = (parseFloat(idpp_pilih) * parseFloat(npph) / 100);
                }else if (epph=="pphpr") {
                    itotalpph = (parseFloat(idpp_pilih) * parseFloat(npph) / 100);
                }
                
            }
            
            
        }
        
        document.getElementById("e_jmlpph").value = npph;
        document.getElementById("e_jmlrppph").value = itotalpph;
        
        //END PPH
        
        var ejmlbulat = document.getElementById("e_jmlbulat").value;
        if (ejmlbulat=="") ejmlbulat="0";
        ejmlbulat = ejmlbulat.split(',').join(newchar);
        
        var itotalbayar="0";
        //TOTAL BAYAR
        if (ipajak=="Y") {
            itotalbayar=( ( parseFloat(idpp_pilih)- parseFloat(itotalpph) ) );
        }else{
            itotalbayar=parseFloat(idpp_pilih);
        }
        itotalbayar=parseFloat(itotalbayar)+parseFloat(itotalppn)+parseFloat(ejmlbulat)-parseFloat(itotaldisc);
        
        document.getElementById("e_jmlbayarrp").value=itotalbayar;
        
    }
    
    
    
    function disp_confirm(pText_,ket)  {
        var iid = document.getElementById('e_id').value;
        var isupp = document.getElementById('cb_supplier').value;
        var ijml =document.getElementById('e_jmlusulan').value;

        if (isupp=="") {
            alert("Vendor masih kosong...");
            return false;
        }

        if(ijml==""){
            ijml="0";
        }
        if (ijml=="0") {
            alert("jumlah masih kosong...");
            return false;
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
                document.getElementById("demo-form2").action = "module/purchasing/pch_purchaseorder/aksi_purchaseorder.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function ShowDataVendor() {
        $("#s_div").html("");
        $("#loading3").html("");
    }
</script>