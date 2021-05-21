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

$pidbr="";
$pdivisi="";
$pjnslampiran="Y";
$pjenis="A";
$pkodeid="1";
$psubkode="01";
$pnodivisi="";
$pperiodeby="";
$pjumlah="";
$pjmladj="";
$pjm_total="";
$pketerangan="";
        
$pots_rppcm=0;
$pots_jml=0;
$pots_sisarp=0;

$act="input";
if ($pact=="editdata") {
    $act="update";
    $pidbr=$_GET['id'];
    
    $query = "select * from dbmaster.t_suratdana_br where idinput='$pidbr'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $pdivisi=$row['divisi'];
    $pjnslampiran = $r['lampiran'];
    $pjenis=$row['jenis_rpt'];
    $pkodeid=$row['kodeid'];
    $psubkode=$row['subkode'];
    $pnodivisi=$row['nodivisi'];
    $pperiodeby=$row['periodeby'];
    $pjumlah=$row['jumlah'];
    $pjmladj=$r['jumlah2'];
    $pjm_total=(double)$pjumlah+(double)$pjmladj;
    $pketerangan=$row['keterangan'];
    $ntgl1=$row['tgl'];
    $ntgl2=$row['tglf'];
    $ntgl3=$row['tglt'];
    
    $ptgl_pengajuan = date('d F Y', strtotime($ntgl1));
    $eperiode1 = date('01 F Y', strtotime($ntgl2));
    $eperiode2 = date('t F Y', strtotime($ntgl3));

}

$plmp1="selected";
$plmp2="";
$plmp3="";

if ($pjnslampiran=="Y") {
    $plmp1="";
    $plmp2="selected";
    $plmp3="";
}elseif ($pjnslampiran=="N") {
    $plmp1="";
    $plmp2="";
    $plmp3="selected";
}

$pjenis1="";
$pjenis2="";

if ($pjenis=="C") {
    $pjenis1="";
    $pjenis2="selected";
}else{
    $pjenis1="selected";
    $pjenis2="";
}

$ptupeper1="";
$ptupeper2="";
$ptupeper3="selected";
$ptupeper4="";
$ptupeper5="";

if ($pperiodeby=="K") {
    $ptupeper1="";
    $ptupeper2="";
    $ptupeper3="";
    $ptupeper4="";
    $ptupeper5="selected";
}


?>


<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

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
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal Pengajuan Dana</label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $ptgl_pengajuan; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowDariDivisi()">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            if ($pidgroup=="1" OR $pidgroup=="24") {
                                                $query = "select DivProdId as divisi from MKT.divprod WHERE br='Y' ";
                                                $query .= " AND DivProdId IN (select distinct divisi from dbmaster.t_kode_spd_divisi WHERE inama='BRETHICAL') ";
                                                $query .=" order by DivProdId";
                                            }else{
                                                $query = "select distinct a.DivProdId as divisi from mkt.divprod as a "
                                                        . " JOIN dbmaster.t_kode_spd_divisi as b on a.DivProdId=b.divisi WHERE "
                                                        . " b.karyawanid='$pidcard' AND b.inama='BRETHICAL'";
                                                $query .=" order by a.DivProdId";
                                            }
                                            $tampil = mysqli_query($cnmy, $query);
                                            $ketemudiv= mysqli_num_rows($tampil);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $ndivisi=$z['divisi'];
                                                
                                                $nnmdivisi=$ndivisi;
                                                if ($ndivisi=="CAN") $nnmdivisi="CANARY/ETHICAL";
                                                
                                                if (empty($ndivisi)) $nnmdivisi="ETHICAL";
                                                
                                                if ((INT)$ketemudiv==1) {
                                                    echo "<option value='$ndivisi' selected>$nnmdivisi</option>";
                                                }else{
                                                    if ($ndivisi==$pdivisi)
                                                        echo "<option value='$ndivisi' selected>$nnmdivisi</option>";
                                                    else
                                                        echo "<option value='$ndivisi'>$nnmdivisi</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            <select class='form-control input-sm' id="cb_jenispilih" name="cb_jenispilih" onchange="ShowDariJenis()" data-live-search="true">
                                            <?PHP
                                            $query ="select distinct jenis_rpt, nama_pengajuan FROM dbmaster.t_kode_spd_pengajuan WHERE IFNULL(igroup,'')='1' ";
                                            $query .=" AND jenis_rpt NOT IN ('J') ";
                                            $query .=" ORDER BY nama_pengajuan";
                                            $tampil = mysqli_query($cnmy, $query);
                                            $ketemujns= mysqli_num_rows($tampil);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $nidjenisrpt=$z['jenis_rpt'];
                                                $nnmjenisrpt=$z['nama_pengajuan'];
                                                
                                                if ((INT)$ketemujns==1) {
                                                    echo "<option value='$nidjenisrpt' selected>$nnmjenisrpt</option>";
                                                }else{
                                                    if ($nidjenisrpt==$pjenis)
                                                        echo "<option value='$nidjenisrpt' selected>$nnmjenisrpt</option>";
                                                    else
                                                        echo "<option value='$nidjenisrpt'>$nnmjenisrpt</option>";
                                                }
                                            }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div id="jenis_kode">

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                              <select class='form-control input-sm' id='cb_kode' name='cb_kode' onchange="" data-live-search="true">
                                                  <!--<option value='' selected>-- Pilihan --</option>-->
                                                  <?PHP
                                                    $query = "select distinct kodeid, nama from dbmaster.t_kode_spd WHERE kodeid='1' order by kodeid";

                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $nkodeid=$z['kodeid'];
                                                        $nkodenm=$z['nama'];
                                                        
                                                        if ($nkodeid==$pkodeid)
                                                            echo "<option value='$nkodeid' selected>$nkodeid - $nkodenm</option>";
                                                        else
                                                            echo "<option value='$nkodeid'>$nkodeid - $nkodenm</option>";
                                                    }
                                                  ?>
                                              </select>
                                        </div>
                                    </div>



                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sub Kode <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                              <select class='form-control input-sm' id='cb_kodesub' name='cb_kodesub' data-live-search="true" onchange="">
                                                  <!--<option value='' selected>-- Pilihan --</option>-->
                                                  <?PHP
                                                  //if ($_GET['act']=="editdata"){
                                                    $query = "select distinct kodeid, subkode, subnama from dbmaster.t_kode_spd where subkode='01' order by subkode";

                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $nsubid=$z['subkode'];
                                                        $nsubnm=$z['subnama'];
                                                        
                                                        if ($nsubid==$psubkode)
                                                            echo "<option value='$nsubid' selected>$nsubid - $nsubnm</option>";
                                                        else
                                                            echo "<option value='$nsubid'>$nsubid - $nsubnm</option>";
                                                    }
                                                  //}
                                                  ?>
                                              </select>
                                        </div>
                                    </div>

                                </div>
                                
                                <!-- untuk adjustment -->
                                <div id='loading'></div>
                                <div id="div_datajenis1">
                                    
                                </div>
                                
                                
                                <div id="div_datajenis2">
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Divisi / No. BR <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_nomordiv' name='e_nomordiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnodivisi; ?>'>
                                        </div>
                                    </div>

                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Lampiran <span class='required'></span></label>
                                        <div class='col-md-3'>
                                            <div class="form-group">

                                                    <select class='form-control input-sm' id="cb_jenis" name="cb_jenis" onchange="" data-live-search="true">
                                                        <option value="" <?PHP echo $plmp1; ?>>--All--</option>
                                                        <option value="Y" <?PHP echo $plmp2; ?>>Ya</option>
                                                        <option value="N" <?PHP echo $plmp3; ?>>Tidak</option>
                                                    </select>

                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Periode By <span class='required'></span></label>
                                        <div class='col-md-3'>
                                            <div class="form-group">

                                                <select class='form-control input-sm' id="cb_pertipe" name="cb_pertipe" onchange="" data-live-search="true">
                                                    <!--<option value="" <?PHP echo $ptupeper1; ?>>--All--</option>-->
                                                    <option value="T" <?PHP echo $ptupeper2; ?>>Transfer</option>
                                                    <option value="I" <?PHP echo $ptupeper3; ?>>Input</option>
                                                    <option value="S" <?PHP echo $ptupeper4; ?>>Rpt SBY</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>


                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">&nbsp; <span class='required'></span></label>
                                        <div class='col-md-5'>
                                            <div class='input-group date' id=''>
                                                <input type="text" class="form-control" id='e_periode1' name='e_periode1' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $eperiode1; ?>' Readonly>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>

                                                <input type="text" class="form-control" id='e_periode2' name='e_periode2' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $eperiode2; ?>' Readonly>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                </div><!-- end div_datajenis2 -->
                            
                                
                                
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        
                                        <div id="div_datajenis_jml1">
                                            Jumlah
                                        </div>
                                        
                                        <div id="div_datajenis_jml2">
                                            <button type='button' class='btn btn-info btn-xs' onclick='TampilkanDataBRInput()'>Tampilkan Data</button> <span class='required'></span>
                                        </div>
                                        
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pjumlah"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Adjustment <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_jmladj' name='e_jmladj' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pjmladj"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_jmltotal' name='e_jmltotal' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pjm_total"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div hidden id="div_ots">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PCM Rp. <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_otspcmrp' name='e_otspcmrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pots_rppcm"; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                            <button type='button' class='btn btn-dark btn-xs' onclick='RptOutstandingShow()'>Outstanding Rp.</button> <span class='required'></span>
                                        </label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_otsjmlrp' name='e_otsjmlrp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pots_jml"; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sisa PCM Rp. <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_otssisarp' name='e_otssisarp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pots_sisarp"; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_keterangan' name='e_keterangan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' id="btn_simpan" onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                        </div>
                                    </div>
                                </div>
                                
                            
                            
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    
                    <div id='loading3'></div>
                    <div id="s_div">


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
        $('#e_tglberlaku_').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                ShowNoDivisiKD();
            } 
        });
        
        $('#e_periode1').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                
            }
        });
        
        $('#e_periode2').datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {
                
            }
        });
        
    });
    
    
</script>


<script>
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var nact = urlku.searchParams.get("act");
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        
        if (nact=="tambahbaru") {
            
            if (ijeniskode!="J") {
                ShowDivJenisBukaTutup();
            }
        }
                
    } );
    
    function ShowDivJenisBukaTutup(){
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var nact = urlku.searchParams.get("act");
        
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        
        if (ijeniskode=="J"){
            div_datajenis1.style.display = 'block';
            div_datajenis2.style.display = 'none';
            s_div.style.display = 'none';
            
            div_datajenis_jml1.style.display = 'block';
            div_datajenis_jml2.style.display = 'none';
            
            document.getElementById("e_jmlusulan").disabled = false;
        }else{
            
            
            div_datajenis1.style.display = 'none';
            div_datajenis2.style.display = 'block';
            
            s_div.style.display = 'block';
            
            div_datajenis_jml1.style.display = 'none';
            div_datajenis_jml2.style.display = 'block';
            
            document.getElementById("e_jmlusulan").disabled = false;
        }
    }
    
    function ShowDariJenis() {
        document.getElementById("e_jmlusulan").disabled = true;
        
        ShowDivJenisBukaTutup();
        ShowDivRpPCM('1');
        ShowKodeidDanSubkode();
        //ShowNoDivisiKD();
        
        document.getElementById("e_jmlusulan").disabled = false;
    }
    
    function ShowDariDivisi() {
        document.getElementById("e_jmlusulan").disabled = true;
        
        ShowDivJenisBukaTutup();
        ShowDivRpPCM('1');
        //ShowNoDivisiKD();
        
        document.getElementById("e_jmlusulan").disabled = false;
    }
    
    function ShowDivRpPCM(sno) {
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        var idivisid = document.getElementById('cb_divisi').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        document.getElementById('e_otspcmrp').value=0;
        document.getElementById('e_otsjmlrp').value=0;
        document.getElementById('e_otssisarp').value=0;
        if (ijeniskode=="B") {
            div_ots.style.display = 'block';
            if (sno=="2") {
            }else{
                if (idivisid!="") {
                    
                    $.ajax({
                        type:"post",
                        url:"module/budget/viewdatabgt.php?module=viewdatapcmrp",
                        data:"udivisid="+idivisid+"&utgl="+itgl,
                        beforeSend: function () {
                            document.getElementById("btn_simpan").disabled = true;
                            document.getElementById('e_otspcmrp').value="0";
                            document.getElementById('e_otsjmlrp').value="0";
                            document.getElementById('e_otssisarp').value="0";
                        },
                        success:function(data){
                            var idata=data.split("|");
                            document.getElementById('e_otspcmrp').value=idata[0];
                            document.getElementById('e_otsjmlrp').value=idata[1];
                            document.getElementById('e_otssisarp').value=idata[2];
                        },
                        complete: function () {
                            document.getElementById("btn_simpan").disabled = false;
                        },
                        error: function () {
                            alert('Something wrong. Try Again!')                
                        }
                    });
                    
                }
            }
            
        }else{
            div_ots.style.display = 'none';
        }
        
    }
    
    function ShowKodeidDanSubkode() {
        var ijeniskode = document.getElementById('cb_jenispilih').value;
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewkodeiddanjenis",
            data:"ujeniskode="+ijeniskode,
            beforeSend: function () {
                document.getElementById("btn_simpan").disabled = true;
            },
            success:function(data){
                $("#jenis_kode").html(data);
            },
            complete: function () {
                document.getElementById("btn_simpan").disabled = false;
            },
            error: function () {
                alert('Something wrong. Try Again!')                
            }
        });
        
    }
    
</script>



