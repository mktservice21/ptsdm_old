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
function getDataKaryawan(data1, data2, icabang){
    var cabang =document.getElementById(icabang).value;
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewkaryawan&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&uicabang="+cabang+"&fldcab="+icabang,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalKaryawan(fildnya1, fildnya2, d1, d2, icabang){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
    document.getElementById('e_idcabang').value="";
    document.getElementById('e_cabang').value="";
}

function getDataCabangFmr(data1, data2, imr){
    var emr=document.getElementById(imr).value;
    if (emr=="") return 0;
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewdatacabangfmr&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&umr="+emr,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalCabang(fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
}

function getDataSubPosting(onklik, data1, data2){
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewdatasubposting&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&uonklik="+onklik,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalSubPosting(onklik, fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
    if (onklik!=""){
        var kodesub = document.getElementById(fildnya1).value;
        getDataComboPosting(onklik, kodesub);
    }
}
function getDataComboPosting(onklik, kodesub){
    //alert(kodesub); return false;
    $.ajax({
        type:"post",
        url:"module/mod_br_entrynon/viewdata.php?module=viewdatacomboposting&data1="+onklik+"&data2="+kodesub,
        data:"uonklik="+onklik+"&ukodesub="+kodesub,
        success:function(data){
            $("#"+onklik).html(data);
        }
    });
}
function getDataDokterMRCabang(data1, data2, icab, imr){
    var ecab = document.getElementById(icab).value;
    var emr = document.getElementById(imr).value;
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewdatadoktermrcabang&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&ucab="+ecab+"&umr="+emr,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalDokter(fildnya1, fildnya2, d1, d2){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
}

function showKodeNyaNon(divisi, kodeid, coa){
    var ediv = document.getElementById(divisi).value;
    var ecoa = document.getElementById(coa).value;
    
    $.ajax({
        type:"post",
        url:"module/mod_br_entrynon/viewdata.php?module=viewdatacombokodenon&data1="+ediv+"&data2="+kodeid,
        data:"udiv="+ediv+"&ukodeid="+kodeid+"&ucoa="+ecoa,
        success:function(data){
            $("#"+kodeid).html(data);
        }
    });
}

function showCOANya(divisi, coa){
    var ediv = document.getElementById(divisi).value;
    $.ajax({
        type:"post",
        url:"module/mod_br_entrynon/viewdata.php?module=viewdatacombocoa&data1="+ediv+"&data2="+coa,
        data:"udiv="+ediv+"&ucoa="+coa,
        success:function(data){
            $("#"+coa).html(data);
            var apilih = "<option value=''>-- Pilihan --</option>";
            $("#cb_kode").html(apilih);
        }
    });
}

function disp_confirm(pText_)  {
    var ecab =document.getElementById('e_idcabang').value;
    var ebuat =document.getElementById('e_idkaryawan').value;
    var edivi =document.getElementById('cb_divisi').value;
    var ekode =document.getElementById('cb_kode').value;
    var ecoa =document.getElementById('cb_coa').value;
    var ejumlah =document.getElementById('e_jmlusulan').value;
    
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
        //alert("cabang masih kosong....");
        //return 0;
    }
    if (ejumlah==""){
        alert("jumlah masih kosong....");
        document.getElementById('e_jmlusulan').focus();
        return 0;
    }
    
    
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            document.getElementById("demo-form2").action = "module/mod_br_entrynon/aksi_entrybrnon.php";
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
    function showCabangMR(ucar, ecabang) {
        var icar = document.getElementById(ucar).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrynon/viewdata.php?module=viewdatacabangkaryawan",
            data:"umr="+icar,
            success:function(data){
                $("#"+ecabang).html(data);
            }
        });
    }
</script>


<script>
    function showLevel2(level1, level2){
        var elvel1 = document.getElementById(level1).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrydcc/viewdata.php?module=viewdatalevel2",
            data:"ulevel1="+elvel1+"&ulevel2="+level2,
            success:function(data){
                $("#"+level2).html(data);
            }
        });
    }

    function showLevel3(level2, level3){
        var elvel2 = document.getElementById(level2).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrydcc/viewdata.php?module=viewdatalevel3",
            data:"ulevel2="+elvel2+"&ulevel3="+level3,
            success:function(data){
                $("#"+level3).html(data);
            }
        });
    }

    function showLevel4(level3, level4){
        var elvel3 = document.getElementById(level3).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrydcc/viewdata.php?module=viewdatalevel4",
            data:"ulevel3="+elvel3+"&ulevel4="+level4,
            success:function(data){
                $("#"+level4).html(data);
            }
        });
    }

    function showLevel5(level4, level5){
        var elvel4 = document.getElementById(level4).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrydcc/viewdata.php?module=viewdatalevel5",
            data:"ulevel4="+elvel4+"&ulevel5="+level5,
            success:function(data){
                $("#"+level5).html(data);
            }
        });
    }

    function getDataAkunLevel5(data1, data2, level1, level2, level3, level4){
        var elevel1=document.getElementById(level1).value;
        var elevel2=document.getElementById(level2).value;
        var elevel3=document.getElementById(level3).value;
        var elevel4=document.getElementById(level4).value;
        $.ajax({
            type:"post",
            url:"config/viewdata.php?module=viewakunlevel5",
            data:"udata1="+data1+"&udata2="+data2+"&ulevel1="+elevel1+"&ulevel2="+elevel2+"&ulevel3="+elevel3+"&ulevel4="+elevel4,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }

    function getDataModalBgAkun(fildnya1, fildnya2, d1, d2){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
    }
</script>


<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>
<div class='modal fade' id='myModal2' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_nobr").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        <?PHP
            include "config/koneksimysqli_it.php";
            
            $edit = mysqli_query($cnit, "SELECT * FROM hrd.br0 WHERE brId='$_GET[id]'");
            $r    = mysqli_fetch_array($edit);
            $rpjumlah=$r['jumlah'];
            $rprelalisasi=$r['realisasi1'];
            $realisasikode=$r['idkontak'];
            $rpcn=$r['cn'];
            $tglinput = date('d F Y', strtotime($r['tgl']));
            $tglinput = date('d/m/Y', strtotime($r['tgl']));
            if (empty($r['tgltrans']) OR $r['tgltrans']=="0000-00-00"){
                $tgltrans = "";
            }else{
                $tgltrans = date('d F Y', strtotime($r['tgltrans']));
                $tgltrans = date('d/m/Y', strtotime($r['tgltrans']));
            }
            
            
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
            
        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            <input type='hidden' id='u_act' name='u_act' value='update' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    <!--
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                            <!--<input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <button type='button' class='btn btn-success' onclick='disp_confirm("Edit Data ?")'>Save</button>
                            <small>edit data</small>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    -->
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_nobr' name='e_nobr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $r['brId']; ?>' Readonly>
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
                                
                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='tgl01'>Tanggal </label>
                                    <div class='col-md-9'>
                                        <div class='input-group date' id='tgl01'>
                                            <input type='text' id='tgl01' name='e_tglinput' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='<?PHP echo $tglinput; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                -->
                                
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='soflow' id='cb_divisi' name='cb_divisi' onchange="showCOANya('cb_divisi', 'cb_coa')"><!--showKodeNyaNon('cb_divisi', 'cb_kode')-->
                                                <?PHP
                                                $tampil=mysqli_query($cnit, "SELECT DivProdId, nama FROM dbmaster.divprod where br='Y' order by nama");
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    if ($a['DivProdId']==$r['divprodid'])
                                                        echo "<option value='$a[DivProdId]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[DivProdId]'>$a[nama]</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_coa'>Kode / COA <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='soflow' id='cb_coa' name='cb_coa' onchange="showKodeNyaNon('cb_divisi', 'cb_kode', 'cb_coa')">
                                                <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                                $divprodid = $r['divprodid'];
                                                $coa4=  getfield("select COA4 as lcfields from dbmaster.v_coa where kodeid='$r[kode]'");
                                                
                                                $query = "SELECT COA4, NAMA4 FROM dbmaster.v_coa where (DIVISI='$divprodid' or ifnull(DIVISI,'')='') AND "
                                                        . "( ((divprodid='$divprodid' and br = '') and (divprodid='$divprodid' and br<>'N'))   or ifnull(kodeid,'')='') order by COA4";
                                                $tampil=mysqli_query($cnit, $query);
                                                
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    $pnmcoa=$a['COA4'];
                                                    if ($a['COA4']==$pcoa4)
                                                        echo "<option value='$a[COA4]' selected>$a[NAMA4]</option>";
                                                    else
                                                        echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
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
                                                $query = "select kodeid,nama,divprodid from dbmaster.br_kode where (divprodid='$r[divprodid]' and br = '')  "
                                                    . " and (divprodid='$r[divprodid]' and br<>'N') order by nama";
                                                
                                                $tampil = mysqli_query($cnit, $query);
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    if ($a['kodeid']==$r['kode'])
                                                        echo "<option value='$a[kodeid]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[kodeid]'>$a[nama]</option>";
                                                }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                
                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class='input-group '>
                                            <span class='input-group-btn'>
                                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataKaryawan('e_idkaryawan', 'e_karyawan', 'e_idcabang')">Go!</button>
                                            </span>
                                            <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $r['karyawanId']; ?>' Readonly>
                                            <input type='text' class='form-control' id='e_karyawan' name='e_karyawan' value='<?PHP echo $r['nama']; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_cabang'>Cabang SDM <span class='required'></span></label>
                                    <div class='col-sm-9'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataCabangFmr('e_idcabang', 'e_cabang', 'e_idkaryawan')">Go!</button>
                                        </span>
                                        <input type='hidden' class='form-control' id='e_idcabang' name='e_idcabang' value="<?PHP echo $r['icabangid']; ?>" Readonly>
                                        <input type='text' class='form-control' id='e_cabang' name='e_cabang' value="<?PHP echo $r['nama_cabang']; ?>" Readonly>
                                        </div>

                                    </div>
                                </div>
                                -->
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_idkaryawan' name='e_idkaryawan' onchange="showCabangMR('e_idkaryawan', 'e_idcabang')">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $tampil = mysqli_query($cnit, "SELECT DISTINCT karyawanId, nama FROM dbmaster.karyawan order by nama, karyawanId");
                                                while($a=mysqli_fetch_array($tampil)){
                                                    if ($a['karyawanId']==$r['karyawanId'])
                                                        echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[karyawanId]'>$a[nama]</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Cabang <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_idcabang' name='e_idcabang'>
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $karyawanId = $r['karyawanId']; 
                                                $query = "select karyawan.iCabangId, cabang.nama from dbmaster.karyawan as karyawan join dbmaster.icabang as cabang on "
                                                        . " karyawan.icabangid=cabang.icabangid where karyawanId='$karyawanId'";
                                                $tampil = mysqli_query($cnit, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    if ($a['iCabangId']==$r['icabangid'])
                                                        echo "<option value='$a[iCabangId]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[iCabangId]'>$a[nama]</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Aktivitas <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Aktivitas'><?PHP echo $r['aktivitas1']; ?></textarea>
                                    </div>
                                </div>
                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas2'> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas2' name='e_aktivitas2' rows='3' placeholder='Aktivitas'><?PHP echo $r['aktivitas2']; ?></textarea>
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Mata Uang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' name='cb_jenis'>
                                            <?php
                                            $tampil=mysqli_query($cnit, "SELECT ccyId, nama FROM dbmaster.ccy");
                                            while($c=mysqli_fetch_array($tampil)){
                                                if ($c['ccyId']==$r['ccyid'])
                                                    echo "<option value='$c[ccyId]' selected>$c[ccyId] - $c[nama]</option>";
                                                else {
                                                    if ($c['ccyId']=="IDR")
                                                        echo "<option value='$c[ccyId]' selected>$c[ccyId] - $c[nama]</option>";
                                                    else    
                                                        echo "<option value='$c[ccyId]'>$c[ccyId] - $c[nama]</option>";
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
                                    
                                    
                                    <div hidden class='form-group'>
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
                                            <input type="checkbox" value="atrika" id="chk_atrika" name="chk_atrika" onclick="cekBoxPilihDPP('chk_atrika')" <?PHP echo $pchkatrika; ?>> DPP Khusus
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


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value="<?PHP echo $rpjumlah; ?>">
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Realisasi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class='input-group '>
                                            <input type='hidden' class='form-control' id='e_realisasi2' name='e_realisasi2' autocomplete="off" value="<?PHP echo $rprelalisasi; ?>">
                                            <input type='hidden' id='e_kdrealisasi' name='e_kdrealisasi' autocomplete='off' class='form-control col-md-7 col-xs-12'  value="<?PHP echo $realisasikode; ?>">
                                        
                                            <input type="text" class='form-control' id="e_realisasi" name="e_realisasi" size="50px" placeholder="cari data..."
                                                   onkeyup="cariFormData(this.id, 'e_kdrealisasi', 'myDivSearching2', 'carirealisasi')" 
                                                   onkeydown="checkkey()" 
                                                   autocomplete="off"  value="<?PHP echo $rprelalisasi; ?>" />
                                        </div>
                                        <div id="myDivSearching2"></div>
                                    </div>
                                </div>
                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Realisasi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_realisasi' name='e_realisasi' autocomplete='off' class='form-control col-md-7 col-xs-12' value="<?PHP echo $rprelalisasi; ?>">
                                    </div>
                                </div>
                                -->

                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CN <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_cn' name='e_cn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value="<?PHP echo $rpcn; ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Slip <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_noslip' name='e_noslip' autocomplete='off' class='form-control col-md-7 col-xs-12' value="<?PHP echo $r['noslip']; ?>">
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
                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for="e_tgltrans">Tanggal Transfer </label>
                                    <div class='col-md-9'>
                                        <div class='input-group date' id='tgl02'>
                                            <input type='text' id='e_tgltrans' name='e_tgltrans' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='<?PHP echo $tgltrans; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                -->
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <?PHP $chklam=""; if ($r['lampiran']=="Y") $chklam="checked"; ?>
                                            <?PHP $chkca=""; if ($r['ca']=="Y") $chkca="checked"; ?>
                                            <?PHP $chkvia=""; if ($r['via']=="Y") $chkvia="checked"; ?>
                                            <label><input type="checkbox" value="lapiran" name="cx_lapir" <?PHP echo $chklam; ?>> Lampiran </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="ca" name="cx_ca" <?PHP echo $chkca; ?>> CA </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="via" name="cx_via" <?PHP echo $chkvia; ?>> Via Surabaya </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_level1'>COA Level 1 <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_level1' name='cb_level1' onchange="showLevel2('cb_level1', 'cb_level2')">
                                            <?PHP
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            $tampil=mysqli_query($cnit, "SELECT COA1, NAMA1 FROM dbmaster.coa_level1 order by COA1");
                                            while($a=mysqli_fetch_array($tampil)){
                                                if ($a['COA1']==$r['COA1']) 
                                                    echo "<option value='$a[COA1]' selected>$a[NAMA1]</option>";
                                                else
                                                    echo "<option value='$a[COA1]'>$a[NAMA1]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_level2'>COA Level 2 <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_level2' name='cb_level2' onchange="showLevel3('cb_level2', 'cb_level3')">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $tampil=mysqli_query($cnit, "SELECT COA2, NAMA2 FROM dbmaster.coa_level2 where COA1='$r[COA1]' order by COA2");
                                            while($a=mysqli_fetch_array($tampil)){
                                                if ($a['COA2']==$r['COA2']) 
                                                    echo "<option value='$a[COA2]' selected>$a[NAMA2]</option>";
                                                else
                                                    echo "<option value='$a[COA2]'>$a[NAMA2]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_level3'>COA Level 3<span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_level3' name='cb_level3' onchange="showLevel4('cb_level3', 'cb_level4')">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $tampil=mysqli_query($cnit, "SELECT COA3, NAMA3 FROM dbmaster.coa_level3 where COA2='$r[COA2]' order by COA3");
                                            while($a=mysqli_fetch_array($tampil)){
                                                if ($a['COA3']==$r['COA3']) 
                                                    echo "<option value='$a[COA3]' selected>$a[NAMA3]</option>";
                                                else
                                                    echo "<option value='$a[COA3]'>$a[NAMA3]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_level4'>COA Level 4<span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_level4' name='cb_level4'><!-- onchange="showLevel5('cb_level4', 'cb_level5')"
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $tampil=mysqli_query($cnit, "SELECT COA4, NAMA4 FROM dbmaster.coa_level4 where COA3='$r[COA3]' order by COA4");
                                            while($a=mysqli_fetch_array($tampil)){
                                                if ($a['COA4']==$r['COA4']) 
                                                    echo "<option value='$a[COA4]' selected>$a[NAMA4]</option>";
                                                else
                                                    echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                -->
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Edit Data... ?")'>Save</button>
                                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                            <!--<input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>-->
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
    <!--end row-->
</div>


<script>
    
    $(document).ready(function() {
        ShowPajak();
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



<style>
    .infoCari{padding:5px;margin-bottom: 5px; cursor: pointer;}
    .infoCari b{color:#555555;}

    #myDivSearching, #myDivSearching1, #myDivSearching2, #myDivSearching3, #myDivSearching4, #myDivSearching5, #myDivSearching6, #myDivSearching7, #myDivSearching8, #myDivSearching9, #myDivSearching10,
    #myDivSearching11, #myDivSearching12, #myDivSearching13, #myDivSearching14, #myDivSearching15,
        #myDivSearchingObt1, #myDivSearchingObt2, #myDivSearchingObt3, #myDivSearchingObt4, #myDivSearchingObt5,
        #myDivSearchingObt6, #myDivSearchingObt7, #myDivSearchingObt8, #myDivSearchingObt9, #myDivSearchingObt10 {
        position: absolute;background: #fff;box-shadow: 0px 3px 5px #555555; z-index:100; color:#000;
        width: 350px; padding-left: 0px;
    }

    #search-form{list-style:none;margin-left:-30px;}
    #search-form li{padding: 5px 10px 5px 0px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid; padding-left: 5px;}

    #search-form li:hover{background:#ece3d2;cursor: pointer;}
</style>

<script>
    function cariFormData(str, idnya, myDivForm, cModule){
        $("#"+str).keyup(function(){
            $.ajax({
            type: "POST",
            url: "js/formpencarian/formsearch_eth.php?module="+cModule+"&myidform="+str+"&idnya="+idnya+"&myDivForm="+myDivForm,
            data:'keyword='+$(this).val(),
            beforeSend: function(){
                    $("#"+str).css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
            },
            success: function(data){
                    $("#"+myDivForm).show();
                    $("#"+myDivForm).html(data);
                    $("#"+str).css("background","#FFF");
            }
            });
        });
    }
    function selectDataFormSearch(val) {
        var nmid = val.split("|");
        $("#"+nmid[2]).hide();
        $("#e_kdrealisasi").val(nmid[3]);
        $("#e_realisasi").val(nmid[4]);
        $("#e_realisasi2").val(nmid[4]);
        $("#e_noslip").focus();
    }

    function HideDataFormSearch(val) {
        var nmid = val.split("|");
        $("#"+nmid[1]).val(nmid[4]);
        $("#"+nmid[2]).hide();
    }

    function checkkey(){
        if(event.keyCode==27){
            //put what you want here...
            $("#myDivSearching2").hide();
            $("#e_noslip").focus();
            //window.alert("Escape key pressed!");
        }
    }
</script>