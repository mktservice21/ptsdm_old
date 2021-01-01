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

<script>
function getDataKaryawan(data1, data2, icabang){
    var cabang =document.getElementById(icabang).value;
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewkaryawancabang&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2+"&uicabang="+cabang+"&fldcab="+icabang,
        success:function(data){
            $("#myModal").html(data);
        }
    });
}

function getDataModalKaryawan(fildnya1, fildnya2, d1, d2, icabang){
    document.getElementById(fildnya1).value=d1;
    document.getElementById(fildnya2).value=d2;
    var ucar=document.getElementById(fildnya1).value;
    
    $.ajax({
        type:"post",
        url:"module/mod_br_entrydcc/viewdata.php?module=viewmarcabang&data1="+ucar,
        data:"ukaryawan="+ucar+"&ucabang="+icabang,
        success:function(data){
            $("#cb_mr").html(data);
        }
    });
}

function getDataCabang(data1, data2){
    $.ajax({
        type:"post",
        url:"config/viewdata.php?module=viewdatacabang&data1="+data1+"&data2="+data2,
        data:"udata1="+data1+"&udata2="+data2,
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
        url:"module/mod_br_entrydcc/viewdata.php?module=viewdatacomboposting&data1="+onklik+"&data2="+kodesub,
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

function showKodeNya(divisi, kodeid){
    var ediv = document.getElementById(divisi).value;
    
    $.ajax({
        type:"post",
        url:"module/mod_br_entrydcc/viewdata.php?module=viewdatacombokode&data1="+ediv+"&data2="+kodeid,
        data:"udiv="+ediv+"&ukodeid="+kodeid,
        success:function(data){
            $("#"+kodeid).html(data);
        }
    });
}

function showCOANya(divisi, coa){
    var ediv = document.getElementById(divisi).value;
    
    $.ajax({
        type:"post",
        url:"module/mod_br_entrydcc/viewdata.php?module=viewdatacombocoa&data1="+ediv+"&data2="+coa,
        data:"udiv="+ediv+"&ucoa="+coa,
        success:function(data){
            $("#"+coa).html(data);
        }
    });
}
function lihat_ks(pText_)  {
    document.getElementById("demo-form2").action = "module/mod_br_entrydcc/liht_ks.php";
    document.getElementById("demo-form2").submit();
    //window.open("module/mod_br_entrydcc/liht_ks.php", "_blank");
    //document.getElementById("demo-form2").submit();
    //return false;
    
    //newDialog = window.open('about:blank', "_form");
    //document.forms["demo-form2"].target='_form';
    //document.forms["demo-form2"].submit();
    //return false;
}


function disp_confirm(pText_)  {
    var ecab =document.getElementById('e_idcabang').value;
    var ebuat =document.getElementById('e_idkaryawan').value;
    var edivi =document.getElementById('cb_divisi').value;
    var ekode =document.getElementById('cb_coa').value;
    var ejumlah =document.getElementById('e_jmlusulan').value;
    /*
    var elvel1 =document.getElementById('cb_level1').value;
    var elvel2 =document.getElementById('cb_level2').value;
    var elvel3 =document.getElementById('cb_level3').value;
    */
    
    
    if (ecab==""){
        alert("cabang masih kosong....");
        return 0;
    }
    if (ebuat==""){
        alert("yang membuat masih kosong....");
        return 0;
    }
    if (edivi==""){
        alert("divisi masih kosong....");
        return 0;
    }
    if (ekode==""){
        alert("kode masih kosong....");
        return 0;
    }
    if (ejumlah==""){
        alert("jumlah masih kosong....");
        document.getElementById('e_jmlusulan').focus();
        return 0;
    }
    
    /*
    if (elvel1==""){
        alert("Level 1 masih kosong....");
        document.getElementById('e_jmlusulan').focus();
        return 0;
    }
    if (elvel2==""){
        alert("Level 2 masih kosong....");
        document.getElementById('e_jmlusulan').focus();
        return 0;
    }
    */
    
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            document.getElementById("demo-form2").action = "module/mod_br_entrydcc/aksi_entrybrdcc.php";
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
function showBuat(ecabang, ucar) {
    var icabang = document.getElementById(ecabang).value;
    $.ajax({
        type:"post",
        url:"module/mod_br_entrydcc/viewdata.php?module=viewdatakaryawancabang",
        data:"uicabang="+icabang+"&ukaryawan="+ucar,
        success:function(data){
            $("#"+ucar).html(data);
            showMR(ecabang,ucar);
            showDokterMR(ecabang,ucar);
        }
    });
}

function showMR(ecabang, ucar) {
    var icabang = document.getElementById(ecabang).value;
    var icar = document.getElementById(ucar).value;
    
    $.ajax({
        type:"post",
        url:"module/mod_br_entrydcc/viewdata.php?module=viewmarcabang&data1="+icar,
        data:"ukaryawan="+icar+"&ucabang="+icabang,
        success:function(data){
            $("#cb_mr").html(data);
            showDokterMR(ecabang,ucar);
        }
    });
}

function showDokterMR(ecabang, ucar) {
    var icabang = document.getElementById(ecabang).value;
    var icar = document.getElementById(ucar).value;
    
    $.ajax({
        type:"post",
        url:"module/mod_br_entrydcc/viewdata.php?module=viewdoktermr&data1="+icar,
        data:"umr="+icar+"&ucab="+icabang,
        success:function(data){
            $("#e_iddokter").html(data);
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
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            <input type='hidden' id='u_act' name='u_act' value='input' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <!--
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                            <!--<input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?")'>Save</button>
                            <small>tambah data</small>
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
                                        <input type='text' id='e_nobr' name='e_nobr' class='form-control col-md-7 col-xs-12' value='' Readonly>
                                    </div>
                                </div>
                                
                                <?PHP
                                    $hari_ini = date("Y-m-d");
                                    $tglinput = date('d F Y', strtotime($hari_ini));
                                    $tglinput = date('d/m/Y', strtotime($hari_ini));
                                ?>
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
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_cabang'>Cabang SDM <span class='required'></span></label>
                                    <div class='col-sm-9'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataCabang('e_idcabang', 'e_cabang')">Go!</button>
                                        </span>
                                        <input type='hidden' class='form-control' id='e_idcabang' name='e_idcabang' value='' Readonly>
                                        <input type='text' class='form-control' id='e_cabang' name='e_cabang' value='' Readonly>
                                        </div>

                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class='input-group '>
                                            <span class='input-group-btn'>
                                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataKaryawan('e_idkaryawan', 'e_karyawan', 'e_idcabang')">Go!</button>
                                            </span>
                                            <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='' Readonly>
                                            <input type='text' class='form-control' id='e_karyawan' name='e_karyawan' value='' Readonly>
                                        </div>
                                    </div>
                                </div>
                                -->
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idcabang'>Cabang <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_idcabang' name='e_idcabang' onchange="showBuat('e_idcabang', 'e_idkaryawan')">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $tampil=mysqli_query($cnmy, "SELECT distinct iCabangId, nama from MKT.icabang where aktif='Y' order by nama");
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    echo "<option value='$a[iCabangId]'>$a[nama]</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_idkaryawan' name='e_idkaryawan' onchange="showMR('e_idcabang', 'e_idkaryawan')">
                                              <option value="">-- Pilih --</option>
                                          </select>
                                      </div>
                                </div>
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_mr'>MR <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='cb_mr' name='cb_mr' onchange="showDokterMR('e_idcabang', 'cb_mr')">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $tampil=mysqli_query($cnmy, "select karyawanid as mr_id, nama, areaId from hrd.karyawan where karyawanid='zzz' order by nama");
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    echo "<option value='$a[mr_id]'>$a[nama]</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>
                                
                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_dokter'>Dokter <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class='input-group '>
                                            <span class='input-group-btn'>
                                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataDokterMRCabang('e_iddokter', 'e_dokter', 'e_idcabang', 'cb_mr')">Go!</button>
                                            </span>
                                            <input type='hidden' class='form-control' id='e_iddokter' name='e_iddokter' value='' Readonly>
                                            <input type='text' class='form-control' id='e_dokter' name='e_dokter' value='' Readonly>
                                        </div>
                                        <button type='button' class='btn btn-success btn-xs' target="_blank" onclick='lihat_ks("")'>Lihat KS</button>
                                    </div>
                                </div>
                                -->
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_iddokter'>Dokter <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_iddokter' name='e_iddokter'>
                                              <option value="">-- Pilih --</option>
                                          </select>
                                          <!--<button type='button' class='btn btn-success btn-xs' target="_blank" onclick='lihat_ks("")'>Lihat KS</button>-->
                                      </div>
                                </div>
                                
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='soflow' id='cb_divisi' name='cb_divisi' onchange="showCOANya('cb_divisi', 'cb_coa')"><!--showKodeNya('cb_divisi', 'cb_kode')-->
                                                <?PHP
                                                $tampil=mysqli_query($cnmy, "SELECT DivProdId, nama FROM MKT.divprod where br='Y' order by nama");
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    echo "<option value='$a[DivProdId]'>$a[nama]</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_coa'>Kode / COA <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='soflow' id='cb_coa' name='cb_coa'>
                                                <option value='' selected>-- Pilihan --</option>
                                            </select>
                                        </div>
                                    </div>
                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_kode'>Kode <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_kode' name='cb_kode'>
                                            <option value="">-- Pilih --</option>
                                            <?PHP
                                                $tampil=mysqli_query($cnmy, "select kodeid,nama,divprodid from hrd.br_kode where divprodid='zzz'");
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    echo "<option value='$a[DivProdId]'>$a[nama]</option>";
                                                }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                                -->
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Aktivitas <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Aktivitas'></textarea>
                                    </div>
                                </div>
                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas2'> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas2' name='e_aktivitas2' rows='3' placeholder='Aktivitas'></textarea>
                                    </div>
                                </div>
                                
                        
                            </div>
                        </div>
                    </div>

                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
<!--                  
<div class="form-group">
    <label class="col-sm-3 control-label">Dokter</label>

    <div class="col-sm-9">
        <div class="input-group">
            
            <div class="input-group-btn">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" 
                        aria-expanded="false">Pilih <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a data-target='#myModal' onClick="getDataKaryawan('e_iddokter', 'e_dokter')">Pilih</a></li>
                    <li class="divider"></li>
                    <li><a href="#">Lihat KS</a></li>
                </ul>
            </div>
            <input type="text" class="form-control" aria-label="Text input with dropdown button" id='e_dokter' name='e_dokter'>
            <input type='hidden' class='form-control' id='e_iddokter' name='e_iddokter' value='' Readonly>
            
        </div>
    </div>
</div>
-->                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Mata Uang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' name='cb_jenis'>
                                            <?php
                                            $tampil=mysqli_query($cnmy, "SELECT ccyId, nama FROM hrd.ccy");
                                            while($c=mysqli_fetch_array($tampil)){
                                                if ($c['ccyId']=="IDR")
                                                    echo "<option value='$c[ccyId]' selected>$c[ccyId] - $c[nama]</option>";
                                                else    
                                                    echo "<option value='$c[ccyId]'>$c[ccyId] - $c[nama]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

<?PHP

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

?>


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
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value=''>
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Realisasi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class='input-group '>
                                            <input type='hidden' class='form-control' id='e_realisasi2' name='e_realisasi2' autocomplete="off" value=''>
                                            <input type='hidden' id='e_kdrealisasi' name='e_kdrealisasi' autocomplete='off' class='form-control col-md-7 col-xs-12' value=''>
                                        
                                            <input type="text" class='form-control' id="e_realisasi" name="e_realisasi" size="50px" placeholder="cari data..."
                                                   onkeyup="cariFormData(this.id, 'e_kdrealisasi', 'myDivSearching2', 'carirealisasi')" 
                                                   onkeydown="checkkey()" 
                                                   autocomplete="off" value="" />
                                        </div>
                                        <div id="myDivSearching2"></div>
                                    </div>
                                </div>

                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Realisasi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_realisasi' name='e_realisasi' autocomplete='off' class='form-control col-md-7 col-xs-12' value=''>
                                    </div>
                                </div>
                                -->
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CN <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_cn' name='e_cn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='' readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Slip <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_noslip' name='e_noslip' autocomplete='off' class='form-control col-md-7 col-xs-12' value=''>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl02'>Tanggal Transfer </label>
                                    <div class='col-md-9'>
                                        <div class='input-group date' id='mytgl02'>
                                            <input type="text" class="form-control" id='e_tgltrans' name='e_tgltrans' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP //echo $tglinput; ?>'>
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
                                            <input type='text' id='e_tgltrans' name='e_tgltrans' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl lahir' value='<?PHP echo $tglinput; ?>' placeholder='dd mmm yyyy' Readonly>
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
                                            <label><input type="checkbox" value="lapiran" name="cx_lapir"> Lampiran </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="ca" name="cx_ca"> CA </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="via" name="cx_via"> Via Surabaya </label>
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
                                            $tampil=mysqli_query($cnmy, "SELECT COA1, NAMA1 FROM dbmaster.coa_level1 order by COA1");
                                            while($a=mysqli_fetch_array($tampil)){
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
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_level3'>COA Level 3<span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_level3' name='cb_level3' onchange="showLevel4('cb_level3', 'cb_level4')">
                                            <option value='' selected>-- Pilihan --</option>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_level4'>COA Level 4<span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_level4' name='cb_level4' ><!--onchange="showLevel5('cb_level4', 'cb_level5')"
                                            <option value='' selected>-- Pilihan --</option>
                                        </select>
                                    </div>
                                </div>

                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_level5'>COA (Level 5)<span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_level5' name='cb_level5'>
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP /*
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            $tampil=mysqli_query($cnmy, "SELECT COA_KODE, COA_NAMA FROM dbmaster.coa order by COA_KODE");
                                            while($a=mysqli_fetch_array($tampil)){
                                            echo "<option value='$a[COA_KODE]'>$a[COA_KODE] - $a[COA_NAMA]</option>";
                                            } */
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                -->
<!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_karyawan'>Akun<br/>(COA Level 5) <span class='required'>*</span></label>
                                    <div class='col-md-9 col-sm-9 col-xs-12'>
                                        <div class='input-group '>
                                            <span class='input-group-btn'>
                                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' 
                                                        onClick="getDataAkunLevel5('e_akun', 'e_namaakun', 'cb_level1',
                                                        'cb_level2', 'cb_level3', 'cb_level4')">Go!</button>
                                            </span>
                                            <input type='text' class='form-control' id='e_akun' name='e_akun' value='' Readonly>
                                        </div>
                                        <input type='text' class='form-control' id='e_namaakun' name='e_namaakun' value='' Readonly>
                                    </div>
                                </div>
                                
-->

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?")'>Save</button>
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
                        { className: "text-right", "targets": [7] },//right
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
                        document.getElementById("demo-form2").action = "module/mod_br_entrydcc/aksi_entrybrdcc.php?kethapus="+txt+"&ket="+ket+"&id="+noid;
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
                    <table id='datatable' class='table table-striped table-bordered' width='100%'>
                        <thead>
                            <tr>
                                <th>Aksi</th><th width='60px'>No ID</th>
                                <th width='60px'>Tanggal</th><th width='60px'>Tgl. Transfer</th><th>Keterangan</th>
                                <th width='80px'>Yg Membuat</th><th width='100px'>Dokter</th><th width='50px'>Jumlah</th>
                                <th width='50px'>Realisasi</th><th>Kode</th>

                            </tr>
                        </thead>
                        <body>
                            <?PHP
                            include "config/koneksimysqli_it.php";
                            $sql = "SELECT brId, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(tgltrans,'%d %M %Y') as tgltrans, DATE_FORMAT(tgltrm,'%d %M %Y') as tgltrm, "
                                    . "nama, nama_kode, nama_cabang, FORMAT(jumlah,2,'de_DE') as jumlah, FORMAT(jumlah1,2,'de_DE') as jumlah1, realisasi1, "
                                    . "dokterId,nama_dokter, "
                                    . "FORMAT(cn,2,'de_DE') as cn, "
                                    . "noslip, aktivitas1 ";
                            $sql.=" FROM dbmaster.v_br0 ";
                            $sql.=" WHERE 1=1 and user1=$_SESSION[USERID] ";
                            $sql.=" and (br <> '' and br<>'N') ";
                            $sql.=" and brId not in (select distinct ifnull(brId,'') from hrd.br0_reject) ";
                            $sql.=" order by brId desc limit 5 ";
                            $tampil=mysqli_query($cnit, $sql);
                            while ($xc=  mysqli_fetch_array($tampil)) {
                                $fnoid=$xc["brId"];
                                $dok="";
                                if (!empty($xc['dokterId'])) $dok=$xc["nama_dokter"]." <small>(".(int)$xc['dokterId'].")</small>";
                                $faksi = "<a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&nmun=$_GET[idmenu]&id=$xc[brId]'>Edit</a>"
                                        . "<button class='btn btn-danger btn-xs'"
                                        . "onClick=\"ProsesData('hapus', '$xc[brId]')\">Hapus</button>";
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
                                echo "<td>$dok</td>";
                                echo "<td>$fjuml</td>";
                                echo "<td>$freal</td>";
                                echo "<td>$fnamakode</td>";
                                echo "</tr>";
                            }
                            ?>
                        </body>
                    </table>

                </div>
            </div>
        </div>
        
        
        
        
        
        
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