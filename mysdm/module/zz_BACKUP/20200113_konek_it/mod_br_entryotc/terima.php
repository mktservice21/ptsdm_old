<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<script>
function disp_confirm(pText_)  {
    var eid =document.getElementById('e_nobr').value;
    
    if (eid==""){
        alert("Tidak ada data yang disimpan....");
        return 0;
    }
    
    
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            document.getElementById("demo-form2").action = "module/mod_br_entryotc/aksi_entrybrotc.php";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}

</script>

<?PHP
    include "config/koneksimysqli_it.php";
    $edit = mysqli_query($cnit, "SELECT * FROM dbmaster.v_br_otc WHERE brOtcId='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $rpjumlah=$r['jumlah'];
    $rprelalisasi=$r['real1'];
    $rpjumlahreal=$r['realisasi'];
    $tglinput = date('d F Y', strtotime($r['tglbr']));
    $tglinput = date('d/m/Y', strtotime($r['tglbr']));
    if (empty($r['tgltrans']) OR $r['tgltrans']=="0000-00-00"){
        $tgltrans = "";
    }else{
        $tgltrans = date('d F Y', strtotime($r['tgltrans']));
        $tgltrans = date('d/m/Y', strtotime($r['tgltrans']));
    }
    
    if (empty($r['tglreal']) OR $r['tglreal']=="0000-00-00"){
        $tgltrm = "";
    }else{
        $tgltrm = date('d F Y', strtotime($r['tglreal']));
        $tgltrm = date('d/m/Y', strtotime($r['tglreal']));
    }
?>
<script> window.onload = function() { document.getElementById("e_noslip").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=editterima&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            <input type='hidden' id='u_act' name='u_act' value='editterima' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <!--
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <button type='button' class='btn btn-success' onclick='disp_confirm("Edit Data ?")'>Save</button>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    -->
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="font-weight: normal;">ID <span class='required'></span></label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nobr' name='e_nobr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $r['brOtcId']; ?>' Readonly>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="font-weight: normal;">Tanggal BR <span class='required'></span></label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_tglinput' name='e_tglinput' autocomplete='off' 
                                   class='form-control col-md-7 col-xs-12' value='<?PHP echo $tglinput; ?>' data-inputmask="'mask': '99/99/9999'" Readonly>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="font-weight: normal;">Cabang <span class='required'></span></label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_cabang' name='e_cabang' autocomplete='off' 
                                   class='form-control col-md-7 col-xs-12' value='<?PHP echo $r['nama_cabang']; ?>' Readonly>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="font-weight: normal;">Jumlah Transfer <span class='required'></span></label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_jml' name='e_jml' autocomplete='off' 
                                   class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $rpjumlah; ?>' Readonly>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Noslip <span class='required'></span></label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_noslip' name='e_noslip' class='form-control col-md-7 col-xs-12' value='<?PHP echo $r['noslip']; ?>'>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Tanggal Terima </label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <div class='input-group date' id='mytgl01'>
                                <input type="text" class="form-control" id='e_tgltrm' name='e_tgltrm' autocomplete='off' required='required' placeholder='dd/MM/yyyy' 
                                       data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgltrm; ?>'>
                                <span class='input-group-addon'>
                                    <span class='glyphicon glyphicon-calendar'></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    

                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah Realisasi <span class='required'></span></label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_realisasi' name='e_realisasi' autocomplete='off' 
                                   class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $rpjumlahreal; ?>' >
                        </div>
                    </div>
                    
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                        <button type='button' class='btn btn-success' onclick='disp_confirm("Edit Data ?")'>Save</button>
                        <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                    </div>
                    
                    
                </div>
            </div>
        </form>
        
    </div>
</div>