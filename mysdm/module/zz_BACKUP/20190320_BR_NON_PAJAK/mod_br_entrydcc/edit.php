<script src="module/mod_br_entrydcc/mytransaksi.js"></script>
<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<script>
function disp_confirm(pText_)  {
    var ecab =document.getElementById('e_idcabang').value;
    var ebuat =document.getElementById('e_idkaryawan').value;
    var edivi =document.getElementById('cb_divisi').value;
    var ekode =document.getElementById('cb_coa').value;
    var ejumlah =document.getElementById('e_jmlusulan').value;
    
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
                                            <input type='text' id='tgl01' name='e_tglinput' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl input' value='<?PHP echo $tglinput; ?>' placeholder='dd mmm yyyy' Readonly>
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
                                        <input type='hidden' class='form-control' id='e_idcabang' name='e_idcabang' value="<?PHP echo $r['icabangid']; ?>" Readonly>
                                        <input type='text' class='form-control' id='e_cabang' name='e_cabang' value="<?PHP echo $r['nama_cabang']; ?>" Readonly>
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
                                            <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $r['karyawanId']; ?>' Readonly>
                                            <input type='text' class='form-control' id='e_karyawan' name='e_karyawan' value='<?PHP echo $r['nama']; ?>' Readonly>
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
                                                $tampil=mysqli_query($cnit, "SELECT distinct iCabangId, nama from MKT.icabang where aktif='Y' order by nama");
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
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_idkaryawan' name='e_idkaryawan' onchange="showMR('e_idcabang', 'e_idkaryawan')">
                                                <?PHP
                                                $icabangid = $r['icabangid']; 
                                                if (($icabangid=='30') or ($icabangid=='31') or ($icabangid=='0000000032')) {
                                                    $query = "select karyawanId,nama,jabatanid,icabangid from hrd.karyawan where (karyawanId='0000000154' or karyawanId='0000000159') AND aktif = 'Y' order by nama"; 
                                                } else {
                                                    $query = "select karyawanId,nama,jabatanid,icabangid from hrd.karyawan where icabangid='$icabangid' AND aktif = 'Y' order by nama"; 
                                                }
                                                $tampil=mysqli_query($cnit, $query);
                                                echo "<option value='' selected>-- Pilihan --</option>";
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
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_mr'>MR <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='cb_mr' name='cb_mr'>
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP
                                                $karyawanId = $r['karyawanId']; 
                                                $icabangid = $r['icabangid']; 
                                                $query = "select jabatanId from hrd.karyawan where karyawanId='$karyawanId'"; 	
                                                $result = mysqli_query($cnit, $query);
                                                $records = mysqli_num_rows($result);
                                                $row = mysqli_fetch_array($result);
                                                $jabatanid = $row['jabatanId'];
                                              
                                                if ($icabangid=="0000000001") { //ho
                                                    $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan order by nama"; 
                                                } else {
                                                    if (($icabangid=="0000000030") or ($icabangid=='0000000031') or ($icabangid=='0000000032')){ // irian, ambon, ntt
                                                        $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where iCabangId='$icabangid' order by nama"; 
                                                    } else {
                                                        if (($jabatanid=="18") or ($jabatanid=="10")) { //spv,am
                                                                $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where (atasanId='$karyawanId' or atasanId2='$karyawanId') order by nama";
                                                        }
                                                        if ($jabatanid=="08") { //dm
                                                                $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where iCabangId='$icabangid' order by nama"; 
                                                        }
                                                        if ($jabatanid=="15") { // mr
                                                                $query = "select karyawanId as mr_id, nama, areaId from hrd.karyawan where karyawanId='$karyawanId'"; 
                                                        }
                                                    }
                                                }
                                                
                                                if ($query=="") {
                                                }else{
                                                    $tampil = mysqli_query($cnit, $query);
                                                    while($a=mysqli_fetch_array($tampil)){
                                                        if ($a['mr_id']==$r['mrid'])
                                                            echo "<option value='$a[mr_id]' selected>$a[nama]</option>";
                                                        else
                                                            echo "<option value='$a[mr_id]'>$a[nama]</option>";
                                                    }
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
                                            <input type='hidden' class='form-control' id='e_iddokter' name='e_iddokter' value='<?PHP echo $r['dokterId']; ?>' Readonly>
                                            <input type='text' class='form-control' id='e_dokter' name='e_dokter' value='<?PHP echo $r['nama_dokter']; ?>' Readonly>
                                        </div>
                                        <button type='button' class='btn btn-success btn-xs'>Lihat KS</button>
                                    </div>
                                </div>
                                -->
                                
                                
                                <div class='form-group'>
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_iddokter'>Dokter <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='e_iddokter' name='e_iddokter'>
                                          <?PHP
                                                $mr_id = $r['mrid']; 
                                                $icabangid = $r['icabangid']; 

                                                $query = "select iCabangId from hrd.karyawan where iCabangId='$icabangid'"; 
                                                $result = mysqli_query($cnit, $query); 
                                                $record = mysqli_num_rows($result); 
                                                if ($icabangid=="0000000001") {
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
                                                                      where mr_dokt.aktif <> 'N' and karyawan.karyawanId='$mr_id' and dokter.nama <> ''
                                                                      order by dokter.nama";
                                                }
                                                
                                                $tampil=mysqli_query($cnit, $query);
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                while($a=mysqli_fetch_array($tampil)){
                                                    if ($a['dokterId']==$r['dokterId'])
                                                        echo "<option value='$a[dokterId]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[dokterId]'>$a[nama]</option>";
                                                }
                                          ?>
                                          </select>
                                          <!--<button type='button' class='btn btn-success btn-xs' target="_blank" onclick='lihat_ks("")'>Lihat KS</button>-->
                                      </div>
                                </div>
                                
                                
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='soflow' id='cb_divisi' name='cb_divisi' onchange="showCOANya('cb_divisi', 'cb_coa')"><!--"showKodeNya('cb_divisi', 'cb_kode')"-->
                                                <?PHP
                                                $tampil=mysqli_query($cnit, "SELECT DivProdId, nama FROM MKT.divprod where br='Y' order by nama");
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
                                            <select class='soflow' id='cb_coa' name='cb_coa'>
                                            <?PHP
                                                $divprodid = $r['divprodid'];
                                                $queryx = " AND ifnull(kodeid,'') (select distinct ifnull(kodeid,'') as kodeid from hrd.br_kode where "
                                                    . " (divprodid='$divprodid' and br <> '') and (divprodid='$divprodid' and br<>'N'))";

                                                $query = "SELECT COA4, NAMA4, kodeid FROM dbmaster.v_coa where DIVISI='$divprodid' AND "
                                                        . "(divprodid='$divprodid' and br <> '') and (divprodid='$divprodid' and br<>'N') order by COA4";
                                                $tampil=mysqli_query($cnit, $query);
                                                
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    if ($a['kodeid']==$r['kode'])
                                                        echo "<option value='$a[COA4]' selected>$a[NAMA4]</option>";
                                                    else
                                                        echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
                                                }
                                            ?>
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
                                                $tampil = mysqli_query($cnit, "select kodeid,nama,divprodid from hrd.br_kode where "
                                                    . " (divprodid='$r[divprodid]' and br <> '') and (divprodid='$r[divprodid]' and br<>'N') order by nama");
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
                                -->
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Aktivitas <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Aktivitas'><?PHP echo $r['aktivitas1']; ?></textarea>
                                    </div>
                                </div>
                                <div class='form-group'>
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
                                            $tampil=mysqli_query($cnit, "SELECT ccyId, nama FROM hrd.ccy");
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


                                <div class='form-group'>
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
                                            <input type='text' id='e_tgltrans' name='e_tgltrans' required='required' class='form-control col-md-7 col-xs-12' placeholder='tgl transfer' value='<?PHP echo $tgltrans; ?>' placeholder='dd mmm yyyy' Readonly>
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

                                <!--
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_level5'>COA (Level 5)<span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_level5' name='cb_level5'>
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP /*
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            $tampil=mysqli_query($cnit, "SELECT COA_KODE, COA_NAMA FROM dbmaster.coa order by COA_KODE");
                                            while($a=mysqli_fetch_array($tampil)){
                                            echo "<option value='$a[COA_KODE]'>$a[COA_KODE] - $a[COA_NAMA]</option>";
                                            } */
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
