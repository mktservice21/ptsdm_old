<!--<script src="module/mod_br_entrynon/mytransaksi.js"></script>-->
<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />

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
            
            $edit = mysqli_query($cnit, "SELECT * FROM dbmaster.v_br0_all WHERE brId='$_GET[id]'");
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
                                                    if ($a['COA4']==$coa4)
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value="<?PHP echo $rpjumlah; ?>">
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Realisasi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_realisasi' name='e_realisasi' autocomplete='off' class='form-control col-md-7 col-xs-12' value="<?PHP echo $rprelalisasi; ?>">
                                    </div>
                                </div>


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
