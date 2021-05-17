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
$pdivisi="CAN";
$pjenis="";
$pkodeid="1";
$psubkode="01";
$pnodivisi="";
$pperiodeby="";
$pjumlah="";
$pketerangan="";
        
$act="input";
if ($pact=="editdata") {
    $act="update";
    $pidbr=$_GET['id'];
    
    $query = "select * from dbmaster.t_suratdana_br where idinput='$pidbr'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $pdivisi=$row['divisi'];
    $pjenis=$row['jenis_rpt'];
    $pkodeid=$row['kodeid'];
    $psubkode=$row['subkode'];
    $pnodivisi=$row['nodivisi'];
    $pperiodeby=$row['periodeby'];
    $pjumlah=$row['jumlah'];;
    $pketerangan=$row['keterangan'];
    $ntgl1=$row['tgl'];
    $ntgl2=$row['tglf'];
    $ntgl3=$row['tglt'];
    
    $ptgl_pengajuan = date('d F Y', strtotime($ntgl1));
    $eperiode1 = date('01 F Y', strtotime($ntgl2));
    $eperiode2 = date('t F Y', strtotime($ntgl3));

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
$ptupeper3="";
$ptupeper4="";
$ptupeper5="selected";

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
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' ";
                                            $query .=" AND DivProdId IN ('CAN') ";
                                            $query .=" order by DivProdId";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $ndivisi=$z['DivProdId'];
                                                
                                                $nnmdivisi="";
                                                if ($ndivisi=="CAN") $nnmdivisi="CANARY";
                                                
                                                if (empty($nnmdivisi)) $nnmdivisi="ETHICAL";
                                                
                                                if ($ndivisi==$pdivisi)
                                                    echo "<option value='$ndivisi' selected>$nnmdivisi</option>";
                                                else
                                                    echo "<option value='$ndivisi'>$nnmdivisi</option>";
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
                                                    echo "<option value='D' $pjenis1>Klaim Discount</option>";
                                                    echo "<option value='C' $pjenis2>Via Surabaya (Klaim Discount)</option>";
                                                ?>
                                            </select>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div hidden id="jenis_kode">

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
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No. Divisi / No. BR <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_nomordiv' name='e_nomordiv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnodivisi; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Periode By <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">

                                            <select class='form-control input-sm' id="cb_pertipe" name="cb_pertipe" onchange="" data-live-search="true">
                                                <!--
                                                <option value="T" <?PHP //echo $ptupeper2; ?>>Transfer</option>
                                                <option value="I" <?PHP //echo $ptupeper3; ?>>Input</option>
                                                <option value="S" <?PHP //echo $ptupeper4; ?>>Rpt SBY</option>
                                                -->
                                                <option value="K" <?PHP echo $ptupeper5; ?>>Klaim Dist.</option>
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
                            
                            
                                <div class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        
                                        <div id="div_datajenis_jml2">
                                            <button type='button' class='btn btn-info btn-xs' onclick='TampilkanDataKalimDisc()'>Tampilkan Data</button> <span class='required'></span>
                                        </div>
                                        
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo "$pjumlah"; ?>' Readonly>
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
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
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
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        if (iact=="tambahbaru") {
            document.getElementById('e_jmlusulan').value="0";
            ShowNoDivisiKD();
        }else if (iact=="editdata") {
            TampilkanDataKalimDisc();
        }
        
    } );
    
    
    function ShowDariDivisi() {
        ShowNoDivisiKD();
    }
    
    function ShowDariJenis() {
        ShowNoDivisiKD();
    }
    
    function ShowNoDivisiKD() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        var eidinput =document.getElementById('e_id').value;
        var idiv = document.getElementById('cb_divisi').value;
        var ikode = document.getElementById('cb_kode').value;
        var ikodesub = document.getElementById('cb_kodesub').value;
        var itgl = document.getElementById('e_tglberlaku').value;
        var iadvance = document.getElementById('cb_jenispilih').value;
        
        if (iact=="editdata" || eidinput!="") {
            return false;
        }
        
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewnomordivisikd",
            data:"udivisi="+idiv+"&ukode="+ikode+"&ukodesub="+ikodesub+"&utgl="+itgl+"&uadvance="+iadvance,
            success:function(data){
                document.getElementById('e_nomordiv').value=data;
            }
        });
    }
    
    
    function TampilkanDataKalimDisc() {
        var eidinput =document.getElementById('e_id').value;
        var edivisi =document.getElementById('cb_divisi').value;
        var ejenis = document.getElementById('cb_jenispilih').value;
        var etgl=document.getElementById('e_tglberlaku').value;
        var epertipe=document.getElementById('cb_pertipe').value;
        var eper1=document.getElementById('e_periode1').value;
        var eper2=document.getElementById('e_periode2').value;
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var iact = urlku.searchParams.get("act");
        
        $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
            
        $.ajax({
            type:"post",
            url:"module/budget/mod_br_spdklaimdisc/datakdiskon.php?module="+module+"&ket=dataklaimdisc",
            data:"uact="+iact+"&uidinput="+eidinput+"&udivisi="+edivisi+"&ujenis="+ejenis+"&utgl="+etgl+
                    "&upertipe="+epertipe+"&uper1="+eper1+"&uper2="+eper2,
            success:function(data){
                $("#s_div").html(data);
                $("#loading3").html("");
            }
        });
            
        
        
    }
    
    
    function disp_confirm(pText_,ket)  {
        
        HitungTotalDariCekBoxKD();
        
        setTimeout(function () {
            disp_confirm_ext(pText_,ket)
        }, 200);
        
    }
    
    
    function disp_confirm_ext(pText_,ket)  {
        
        var ijml =document.getElementById('e_jmlusulan').value;
        if(ijml==""){
            ijml="0";
        }
        if (ijml=="0") {
            alert("jumlah masih kosong...");
            return false;
        }
        
        var iid =document.getElementById('e_id').value;
        var edivsi =document.getElementById('cb_divisi').value;
        var ejenis =document.getElementById('cb_jenispilih').value;
        var ekode =document.getElementById('cb_kode').value;
        var ekodesub =document.getElementById('cb_kodesub').value;
        var enodivisi =document.getElementById('e_nomordiv').value;
        
        if (edivsi==""){
            alert("divisi masih kosong....");
            return 0;
        }
        
        if (ejenis==""){
            alert("jenis masih kosong....");
            return 0;
        }

        if (ekode==""){
            alert("kode masih kosong....");
            return 0;
        }

        if (ekodesub==""){
            alert("sub kode masih kosong....");
            return 0;
        }

        if (enodivisi==""){
            alert("nodivisi masih kosong....");
            return 0;
        }
        
        var x_ = document.getElementById("cb_divisi").selectedIndex;
        var y_ = document.getElementById("cb_divisi").options;
        var ikddivisi=y_[x_].index;
        var inmdivisi=y_[x_].text;
        
        var x_ = document.getElementById("cb_jenispilih").selectedIndex;
        var y_ = document.getElementById("cb_jenispilih").options;
        var inmjenis=y_[x_].text;

        pText_="Divisi : "+inmdivisi+", \n\
Jenis : "+inmjenis+", \n\
Nomor Divisi : "+enodivisi+", \n\
Total Pengajuan : Rp. "+ijml+" \n\
________________________________________  \n\
Apakah akan simpan data...?";
        
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var iact = urlku.searchParams.get("act");
        //alert(iact);
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=cekdatasudahadakdisc",
            data:"uact="+iact+"&uid="+iid+"&unodivisi="+enodivisi,
            success:function(data){
                //var tjml = data.length;
                //alert(data);
                //return false;

                if (data=="boleh") {
            
                    ok_ = 1;
                    if (ok_) {
                        var r=confirm(pText_)
                        if (r==true) {
                            //document.write("You pressed OK!")
                            document.getElementById("d-form2").action = "module/budget/mod_br_spdklaimdisc/aksi_spdklaimdisc.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                            document.getElementById("d-form2").submit();
                            return 1;
                        }
                    } else {
                        //document.write("You pressed Cancel!")
                        return 0;
                    }
                    

                }else{
                    alert(data);
                }
            }
        });
        
        
        
    }
    
    
    
</script>

<!--

        $.ajax({
            type:"post",
            url:"module/budget/viewdatadkd.php?module=cekdatasudahadakdisc",
            data:"uact="+iact+"&uid="+iid+"&unodivisi="+enodivisi,
            success:function(data){
                //var tjml = data.length;
                //alert(data);
                //return false;
                if (data=="boleh") {
        
                    pText_="Divisi : "+inmdivisi+", \n\
Jenis : "+inmjenis+", \n\
Nomor Divisi : "+enodivisi+", \n\
Total Pengajuan : Rp. "+ijml+" \n\
________________________________________  \n\
Apakah akan simpan data...?";

                    ok_ = 1;
                    if (ok_) {
                        var r=confirm(pText_)
                        if (r==true) {
                            //document.write("You pressed OK!")
                            document.getElementById("d-form2").action = "module/budget/mod_br_spdklaimdisc/aksi_spdklaimdisc.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                            document.getElementById("d-form2").submit();
                            return 1;
                        }
                    } else {
                        //document.write("You pressed Cancel!")
                        return 0;
                    }
        
                }else{
                    alert(data);
                }
            }
        });
-->