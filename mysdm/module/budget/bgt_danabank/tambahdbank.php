<?php
$pmodule="";
$pidmenu="";
$pact="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
if (isset($_GET['idmenu'])) $pidmenu=$_GET['idmenu'];
if (isset($_GET['act'])) $pact=$_GET['act'];
    
$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];
    
$hari_ini = date("Y-m-d");
$ptgl_pengajuan = date('d F Y', strtotime($hari_ini));
$eperiode1 = date('01 F Y', strtotime($hari_ini));
$eperiode2 = date('t F Y', strtotime($hari_ini));

$ntglclose="";
$pidbr="";
$pdivisi="";
$psubkode="";
//$pcoa="105-02";
$pcoa="000-0";
$pstatus="";
$pd_spd_debker="";
$p_bnkjumlah="";
$pketerangan="";
$pnobukti="";
$pnodivisi="";
$pidinput="";
$pkodeinput="";

$pbrnoid="";
$pnoslipbr="";
$prealisasibr="";
$pketeranganbr="";
$pcustbr="";
$pketeranganbr="";
        
$act="input";
if ($pact=="editdata") {
    $act="update";
    $pidbr=$_GET['id'];
    
    $query = "select * from dbmaster.t_suratdana_bank where idinputbank='$pidbr'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $ntgl1=$row['tanggal'];
    $pdivisi=$row['divisi'];
    $psubkode=$row['subkode'];
    $pcoa=$row['coa4'];
    $pstatus=$row['sts'];
    $pd_spd_debker=$row['stsinput'];
    $p_bnkjumlah=$row['jumlah'];
    $pketerangan=$row['keterangan'];
    $pnobukti=$row['nobukti'];
    $pnodivisi=$row['nodivisi'];
    $pidinput=$row['idinput'];
    
    $pbrnoid=$row['brid'];
    $pnoslipbr=$row['noslip'];
    $prealisasibr=$row['realisasi'];
    $pcustbr=$row['customer'];
    $pketeranganbr=$row['aktivitas1'];
    
    $ptgl_pengajuan = date('d F Y', strtotime($ntgl1));
    if ($pidinput=="0") $pidinput="";
    
    if (!empty($pidinput)) {
        $querya = "select distinct kodeinput from dbmaster.t_suratdana_br1 WHERE idinput='$pidinput'";
        $tampila= mysqli_query($cnmy, $querya);
        $rowa= mysqli_fetch_array($tampila);
        $pkodeinput=$rowa['kodeinput'];
        
    }
    
}


?>


<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">
    
    <!--row-->
    <div class="row">
        
        <form method='POST' action='' id='d-form1' name='form1' data-parsley-validate target="_blank"></form>
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=$act&idmenu=$pidmenu"; ?>' 
              id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
            
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
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='text' id='e_iduser' name='e_iduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type="text" class="form-control" id='e_nobukti' name='e_nobukti' autocomplete='off' value='<?PHP echo $pnobukti; ?>'>
                                        <input type="text" class="form-control" id='e_tgl_cls' name='e_tgl_cls' autocomplete='off' value='<?PHP echo $ntglclose; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Transaksi</label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id=''>
                                            <input type="hidden" class="form-control" id='e_asltglberlaku' name='e_asltglberlaku' value='<?PHP echo $ptgl_pengajuan; ?>' Readonly>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptgl_pengajuan; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <button type='button' class='btn btn-danger btn-xs' onclick='hapus_nodivisi()'>hapus nodivisi</button>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih No. BR/Divisi<br/><span style="color:red;"><u><i>(klik pilih)</i></u></span> <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataNoDivisi('e_idinput', 'e_nodivisi', 'e_jmlnodiv', 'e_kodeinput', '<?PHP echo "$pidcard"; ?>', '<?PHP echo "$pidgroup"; ?>')">Pilih</button>
                                        </span>
                                        <input type='text' class='form-control' id='e_nodivisi' name='e_nodivisi' value='<?PHP echo $pnodivisi; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='e_idinput' name='e_idinput' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='e_jmlnodiv' name='e_jmlnodiv' value='<?PHP echo $pnodivisi; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='e_kodeinput' name='e_kodeinput' value='<?PHP echo $pkodeinput; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <button type='button' class='btn btn-danger btn-xs' onclick='hapus_brinput()'>hapus br input</button>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID INPUT BR <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataBRInput('e_idnobr', 'e_noslipbr', 'e_realisasibr', 'e_customerbr', 'e_ketbr')">Pilih!</button>
                                        </span>
                                        <input type='text' class='form-control' id='e_idnobr' name='e_idnobr' value='<?PHP echo $pbrnoid; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='e_noslipbr' name='e_noslipbr' value='<?PHP echo $pnoslipbr; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='e_realisasibr' name='e_realisasibr' value='<?PHP echo $prealisasibr; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='e_customerbr' name='e_customerbr' value='<?PHP echo $pcustbr; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='e_ketbr' name='e_ketbr' value='<?PHP echo $pketeranganbr; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_kodesub' name='cb_kodesub' onchange="ShowDebitKreditJenis()">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                                $query = "select distinct kodeid, subkode, subnama from dbmaster.t_kode_spd where ibank='Y' order by subkode";

                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $nkodeid=$z['kodeid'];
                                                    $nsubkode=$z['subkode'];
                                                    $nsubnama=$z['subnama'];
                                                    
                                                    if ($nsubkode==$psubkode)
                                                        echo "<option value='$nsubkode' selected>$nsubkode - $nsubnama</option>";
                                                    else
                                                        echo "<option value='$nsubkode'>$nsubkode - $nsubnama</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div id="div_nonspd">
                                    
                                    
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>COA <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_coa' name='cb_coa' onchange="">
                                                <!--<option value='' selected>-- Pilihan --</option>-->
                                                <?PHP
                                                $query = "select a.coa, b.NAMA4 FROM dbmaster.coa_dana_bank a JOIN "
                                                        . " dbmaster.coa_level4 b on a.coa=b.COA4 order by a.coa";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $ncoa4=$z['coa'];
                                                    $nnama4=$z['NAMA4'];
                                                    
                                                    if ($ncoa4==$pcoa)
                                                        echo "<option value='$ncoa4' selected>$ncoa4 - $nnama4</option>";
                                                    else
                                                        echo "<option value='$ncoa4'>$ncoa4 - $nnama4</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Pengajuan <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="">
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                $query = "select DivProdId from MKT.divprod WHERE br='Y' ";
                                                if ($_SESSION['DIVISI']=="OTC") {
                                                    $query .=" AND DivProdId = 'OTC' ";
                                                }
                                                $query .=" order by DivProdId";
                                                $tampil = mysqli_query($cnmy, $query);
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $nkddivisi=$z['DivProdId'];
                                                    $nnmdisivi=$nkddivisi;
                                                    
                                                    if ($nkddivisi=="CAN") $nnmdisivi="CANARY/ETHICAL";
                                                    elseif ($nkddivisi=="PIGEO") $nnmdisivi="PIGEON";
                                                    elseif ($nkddivisi=="PEACO") $nnmdisivi="PEACOCK";
                                                    
                                                    if ($nkddivisi==$pdivisi)
                                                        echo "<option value='$nkddivisi' selected>$nnmdisivi</option>";
                                                    else
                                                        echo "<option value='$nkddivisi'>$nnmdisivi</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                    
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_sts' name='cb_sts' onchange="">
                                            <?PHP
                                            if ($pstatus=="1") {
                                                echo "<option value='1' selected>Setoran (Tunai)</option>";
                                                echo "<option value='2'>Retur Bank</option>";
                                            }elseif ($pstatus=="2") {
                                                echo "<option value='1'>Setoran (Tunai)</option>";
                                                echo "<option value='2' selected>Retur Bank</option>";
                                            }else{
                                                echo "<option value='1'>Setoran (Tunai)</option>";
                                                echo "<option value='2'>Retur Bank</option>";
                                                echo "<option value='3' selected></option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Debit/Kredit <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type="hidden" class="form-control" id='cb_asldebitkredit' name='cb_asldebitkredit' value='<?PHP echo $pd_spd_debker; ?>' Readonly>
                                        <select class='form-control input-sm' id='cb_debitkredit' name='cb_debitkredit' onchange="ShowCoaPilihJenis()">
                                            <?PHP
                                            $pdebker_sel1="selected";
                                            $pdebker_sel2="";
                                            if ($pd_spd_debker=="K") $pdebker_sel2="selected";

                                            echo "<option value='D' $pdebker_sel1>Debit</option>";
                                            echo "<option value='K' $pdebker_sel2>Kredit</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Jumlah
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jml' name='e_jml' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $p_bnkjumlah; ?>'>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_ket' name='e_ket' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                        </div>
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
        $('#e_tglberlaku').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                //ShowNoDivisiKD();
            } 
        });
    });
    
    
</script>


<script>
    
    function getDataNoDivisi(data1, data2, data3, data4, idkry, ibuka){
        var etgl=document.getElementById('e_tglberlaku').value;
        var epnospd="";
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt_ms.php?module=viewdataspddivisiperuserid2",
            data:"udata1="+data1+"&udata2="+data2+"&udata3="+data3+"&udata4="+data4+"&uidkry="+idkry+"&ubuka="+ibuka+"&utgl="+etgl+"&upnospd="+epnospd,
            success:function(data){
                $("#myModal").html(data);
                document.getElementById('ex_idnobrxx').value="";
                document.getElementById('e_idnobr').value="";
                document.getElementById('e_noslip').value="";
            }
        });
    }
    
    function getDataModalNoDivisi(fildnya1, fildnya2, fildnya3, fildnya4, d1, d2, d3, d4){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
        document.getElementById(fildnya3).value=d3;
        document.getElementById(fildnya4).value=d4;
        hapus_brinput();
    }
    
    function hapus_nodivisi() {
        document.getElementById('e_idinput').value="";
        document.getElementById('e_jmlnodiv').value="";
        document.getElementById('e_kodeinput').value="";
        document.getElementById('e_nodivisi').value="";
        hapus_brinput();
    }
    
    function getDataBRInput(data1, data2, data3, data4, data5) {
        var eidinput=document.getElementById('e_idinput').value;
        var ekodeinput=document.getElementById('e_kodeinput').value;
        
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt_ms.php?module=viewdatabrinput",
            data:"udata1="+data1+"&udata2="+data2+"&udata3="+data3+"&udata4="+data4+"&udata5="+data5+"&uidinput="+eidinput+"&ukodeinput="+ekodeinput,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    
    function getDataModalBrInput(fildnya1, fildnya2, fildnya3, fildnya4, fildnya5, d1, d2, d3, d4, d5){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
        document.getElementById(fildnya3).value=d3;
        document.getElementById(fildnya4).value=d4;
        document.getElementById(fildnya5).value=d5;
    }
    
    function hapus_brinput() {
        document.getElementById('e_noslipbr').value="";
        document.getElementById('e_realisasibr').value="";
        document.getElementById('e_customerbr').value="";
        document.getElementById('e_ketbr').value="";
        document.getElementById('e_idnobr').value="";
    }
    
    
    function disp_confirm(pText_,ket)  {
        
        //HitungTotalDariCekBoxKD();
        
        setTimeout(function () {
            disp_confirm_ext(pText_,ket)
        }, 200);
        
    }
    
    
    function disp_confirm_ext(pText_,ket)  {
        
        var etgl_cls = document.getElementById('e_tgl_cls').value;
        if (etgl_cls!=""){
            alert("Periode (Tgl. Transaksi) yang diisi sudah proses closing...\n\
Tidak bisa tambah dan edit data.\n\
Silakan isi Tgl. Transaksi lain...!!!");
            return false;
        }
        
        var itgl =document.getElementById('e_tglberlaku').value;
        var x_ = document.getElementById("cb_kodesub").selectedIndex;
        var y_ = document.getElementById("cb_kodesub").options;
        var ikdsub=y_[x_].index;
        var inmsub=y_[x_].text;
        
        var x_ = document.getElementById("cb_coa").selectedIndex;
        var y_ = document.getElementById("cb_coa").options;
        var ikdcoa=y_[x_].index;
        var inmcoa=y_[x_].text;
        
        var x_ = document.getElementById("cb_divisi").selectedIndex;
        var y_ = document.getElementById("cb_divisi").options;
        var ikddivisi=y_[x_].index;
        var inmdivisi=y_[x_].text;
        
        var x_ = document.getElementById("cb_sts").selectedIndex;
        var y_ = document.getElementById("cb_sts").options;
        var ikdsts=y_[x_].index;
        var inmsts=y_[x_].text;
        
        var x_ = document.getElementById("cb_debitkredit").selectedIndex;
        var y_ = document.getElementById("cb_debitkredit").options;
        var ikddebker=y_[x_].index;
        var inmdebker=y_[x_].text;
        
        var ijml =document.getElementById('e_jml').value;
        var iket =document.getElementById('e_ket').value;
        
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var iact = urlku.searchParams.get("act");
        
        if (ikddivisi=="") {
            alert("divisi belum dipilih...");
            return false;
        }
        
        if (iact=="tambahbaru") {
            if(ijml==""){
                ijml="0";
            }
            if (ijml=="0") {
                alert("jumlah masih kosong...");
                return false;
            }
        }
        
        
        if (iket=="") {
            alert("keterangan harus diisi...");
            return false;
        }
        
        
        pText_="Divisi : "+inmdivisi+", \n\
Jenis : "+inmsub+", \n\
Status : "+inmsts+", \n\
Total Jumlah "+inmdebker+" : Rp. "+ijml+" \n\
________________________________________  \n\
Apakah akan simpan data...?";
        
        //alert(iact);
        
        
                    ok_ = 1;
                    if (ok_) {
                        var r=confirm(pText_)
                        if (r==true) {
                            //document.write("You pressed OK!")
                            document.getElementById("d-form2").action = "module/budget/bgt_danabank/aksi_danabank.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                            document.getElementById("d-form2").submit();
                            return 1;
                        }
                    } else {
                        //document.write("You pressed Cancel!")
                        return 0;
                    }
        
        
        
    }
    
    
</script>