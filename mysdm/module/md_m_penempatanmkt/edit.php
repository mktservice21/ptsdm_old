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
    .ui-datepicker-calendar {
        display: none;
    }
</style>


<?php
include "config/koneksimysqli_ms.php";

$idbr="";
$hari_ini = date("Y-m-d");
$pperiode = date('d F Y', strtotime($hari_ini));

$idbr="";
$pnmsm="";

$pidsm="";
$piddm="";
$pidam="";
$pidmr="";

$nkaryawan=$_GET['idkry'];

$pnact=$_GET['act'];

$act="input";
if ($pnact=="editdatamr" OR $pnact=="editdataam" OR $pnact=="editdatadm" OR $pnact=="editdatasm"){
    $act="update";
    $idbr=$_GET['id'];
    
    $query ="SELECT a.id, a.bulan, a.region, a.icabangid, b.nama nama_cabang, a.areaid, c.nama nama_area, a.divprodid, 
        a.gsm, d.nama nama_gsm, a.sm, e.nama nama_sm, a.dm, h.nama nama_dm, a.am, f.nama nama_am, a.mr, g.nama nama_mr 
        from ms.penempatan_marketing a 
        LEFT JOIN sls.icabang b on a.icabangid=b.iCabangId 
        LEFT JOIN sls.iarea c on a.icabangid=c.iCabangId AND a.areaid=c.areaId 
        LEFT JOIN ms.karyawan d on a.gsm=d.karyawanId 
        LEFT JOIN ms.karyawan e on a.sm=e.karyawanId 
        LEFT JOIN ms.karyawan f on a.am=f.karyawanId 
        LEFT JOIN ms.karyawan g on a.mr=g.karyawanId 
        LEFT JOIN ms.karyawan h on a.dm=h.karyawanId 
        WHERE a.id='$idbr'";
    $edit = mysqli_query($cnms, $query);
    $r    = mysqli_fetch_array($edit);
    
    $tgl=$r['bulan'];
    $pperiode = date('F Y', strtotime($tgl));
    
    $pidsm=$r['sm'];
    $pnmsm=$r['nama_sm'];
    if ($pidsm=="000") $pnmsm="VACANT";
    $piddm=$r['dm'];
    $pnmdm=$r['nama_dm'];
    if ($piddm=="000") $pnmdm="VACANT";
    $pidam=$r['am'];
    $pnmam=$r['nama_am'];
    if ($pidam=="000") $pnmam="VACANT";
    $pidmr=$r['mr'];
    $pnmmr=$r['nama_mr'];
    
    $pidcabang=$r['icabangid'];
    $pnmcabang=$r['nama_cabang'];
    $pidarea=$r['areaid'];
    $pnmarea=$r['nama_area'];
    $pdivisi=$r['divprodid'];
    
}
?>

<script> window.onload = function() { document.getElementById("e_sm").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
        
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                                        <input type='text' id='e_nkaryawan' name='e_nkaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nkaryawan; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_periode' name='e_periode' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pperiode; ?>' Readonly>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_sm' name='e_sm' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmsm; ?>' Readonly>
                                    </div>
                                </div>

                                <?PHP
                                if ($pnact!="editdatadm") {
                                ?>
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_dm' name='e_dm' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmdm; ?>' Readonly>
                                        </div>
                                    </div>
                                <?PHP
                                }
                                ?>
                                
                                <?PHP
                                if ($pnact=="editdatamr") {
                                ?>
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>AM <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_am' name='e_am' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmam; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_cabang' name='e_cabang' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmcabang; ?>' Readonly>
                                            <input type='hidden' id='e_idcabang' name='e_idcabang' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcabang; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_area' name='e_area' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmarea; ?>' Readonly>
                                            <input type='hidden' id='e_idarea' name='e_idarea' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidarea; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_divisi' name='e_divisi' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivisi; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type="radio" id="e_pilih1" name="e_pilih" value="Y" onclick="PilihKaryawanJbt('MR', 'A')" checked> MR Aktif &nbsp; &nbsp; 
                                            <input type="radio" id="e_pilih2" name="e_pilih" value="T" onclick="PilihKaryawanJbt('MR', 'T')"> MR Tidak Aktif / MR Promosi
                                            <input type="radio" id="e_pilih3" name="e_pilih" value="V" onclick="PilihKaryawanJbt('MR', 'V')"> Pilih Vacant &nbsp; &nbsp; 
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>MR <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_mr' name='cb_mr' data-live-search="true">
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                $query="select karyawanId, nama from ms.karyawan WHERE "
                                                        . " (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR tglkeluar='') "
                                                        . " AND (jabatanId='15' OR karyawanId='$pidmr') order by nama, karyawanId";
                                                $tampil= mysqli_query($cnms, $query);
                                                while ($nr= mysqli_fetch_array($tampil)){
                                                    $npkaryawanid=$nr['karyawanId'];
                                                    $npnmkaryawan=$nr['nama'];
                                                    if ($npkaryawanid==$pidmr)
                                                        echo "<option value='$npkaryawanid' selected>$npkaryawanid - $npnmkaryawan</option>";
                                                    else
                                                        echo "<option value='$npkaryawanid'>$npkaryawanid - $npnmkaryawan</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                <?PHP
                                }elseif ($pnact=="editdataam") {
                                ?>
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_cabang' name='e_cabang' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmcabang; ?>' Readonly>
                                            <input type='hidden' id='e_idcabang' name='e_idcabang' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcabang; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_area' name='e_area' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmarea; ?>' Readonly>
                                            <input type='hidden' id='e_idarea' name='e_idarea' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidarea; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type="radio" id="e_pilih1" name="e_pilih" value="Y" onclick="PilihKaryawanJbt('AM', 'A')" checked> AM Aktif &nbsp; &nbsp; 
                                            <input type="radio" id="e_pilih2" name="e_pilih" value="T" onclick="PilihKaryawanJbt('AM', 'T')"> AM Tidak Aktif / AM Promosi
                                            <input type="radio" id="e_pilih3" name="e_pilih" value="V" onclick="PilihKaryawanJbt('AM', 'V')"> Pilih Vacant &nbsp; &nbsp; 
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>AM <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_am' name='cb_am' data-live-search="true">
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                $query="select karyawanId, nama from ms.karyawan WHERE "
                                                        . " (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR tglkeluar='') "
                                                        . " AND (jabatanId IN ('10', '18') OR karyawanId='$pidam') order by nama, karyawanId";
                                                $tampil= mysqli_query($cnms, $query);
                                                while ($nr= mysqli_fetch_array($tampil)){
                                                    $npkaryawanid=$nr['karyawanId'];
                                                    $npnmkaryawan=$nr['nama'];
                                                    if ($npkaryawanid==$pidam)
                                                        echo "<option value='$npkaryawanid' selected>$npkaryawanid - $npnmkaryawan</option>";
                                                    else
                                                        echo "<option value='$npkaryawanid'>$npkaryawanid - $npnmkaryawan</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                
                                <?PHP
                                }elseif ($pnact=="editdatadm") {
                                ?>
                                
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_cabang' name='e_cabang' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmcabang; ?>' Readonly>
                                            <input type='hidden' id='e_idcabang' name='e_idcabang' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcabang; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type='text' id='e_area' name='e_area' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmarea; ?>' Readonly>
                                            <input type='hidden' id='e_idarea' name='e_idarea' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidarea; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <input type="radio" id="e_pilih1" name="e_pilih" value="Y" onclick="PilihKaryawanJbt('DM', 'A')" checked> DM Aktif &nbsp; &nbsp; 
                                            <input type="radio" id="e_pilih2" name="e_pilih" value="T" onclick="PilihKaryawanJbt('DM', 'T')"> DM Tidak Aktif / DM Promosi
                                            <input type="radio" id="e_pilih3" name="e_pilih" value="V" onclick="PilihKaryawanJbt('DM', 'V')"> Pilih Vacant &nbsp; &nbsp; 
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM<span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_dm' name='cb_dm' data-live-search="true">
                                                <option value='' selected>-- Pilihan --</option>
                                                <?PHP
                                                $query="select karyawanId, nama from ms.karyawan WHERE "
                                                        . " (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR tglkeluar='') "
                                                        . " AND (jabatanId IN ('08') OR karyawanId='$piddm') order by nama, karyawanId";
                                                $tampil= mysqli_query($cnms, $query);
                                                while ($nr= mysqli_fetch_array($tampil)){
                                                    $npkaryawanid=$nr['karyawanId'];
                                                    $npnmkaryawan=$nr['nama'];
                                                    if ($npkaryawanid==$piddm)
                                                        echo "<option value='$npkaryawanid' selected>$npkaryawanid - $npnmkaryawan</option>";
                                                    else
                                                        echo "<option value='$npkaryawanid'>$npkaryawanid - $npnmkaryawan</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                <?PHP    
                                }
                                ?>
                                
                            </div>
                            

                       
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
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

<script>
    function disp_confirm(pText_,ket)  {
        var eid = document.getElementById('e_id').value;

        if (eid==""){
            alert("Tidak ada data yang diedit....");
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
                var iact = urlku.searchParams.get("act");
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/md_m_penempatanmkt/aksi_penempatanmkt.php?module="+module+"&act="+ket+"&idmenu="+idmenu+"&nact="+iact;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function PilihKaryawanJbt(ikry, ists) {
        var nkry="cb_mr";
        if (ikry=="AM") {
            nkry="cb_am";
        }else if (ikry=="DM") {
            nkry="cb_dm";
        }
        
        if (ists=="V") {
            $("#"+nkry).html("<option value=''></option");
            return false;
        }
        $.ajax({
            type:"post",
            url:"module/md_m_penempatanmkt/viewdata.php?module=viewdatakaryawan",
            data:"ukry="+ikry+"&usts="+ists,
            success:function(data){
                $("#"+nkry).html(data);
                //$("#"+nkry).focus();
            }
        });
    }
</script>




<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    .custom-combobox {
        position: relative;
        display: inline-block;
    }
    .custom-combobox-toggle {
        position: absolute;
        top: 0;
        bottom: 0;
        margin-left: -1px;
        padding: 0;
    }
    .custom-combobox-input {
        margin: 0;
        padding: 5px 10px;
        width:300px;
    }
</style>
<script src="js/select_combo.js"></script>
<script>
    $( function() {
        $( "#cb_mr" ).combobox();
        $( "#cb_am" ).combobox();
        $( "#cb_dm" ).combobox();
    } );
</script>
  