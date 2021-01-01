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

<?PHP
    include "config/koneksimysqli_it.php";
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.v_br0_all WHERE brId='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $rpjumlah=$r['jumlah'];
    $rprelalisasi=$r['realisasi1'];
    $rpcn=$r['cn'];
    $rpjumlahreal=$r['jumlah1'];
    $tglinput = date('d F Y', strtotime($r['tgl']));
    $tglinput = date('d/m/Y', strtotime($r['tgl']));
    if (empty($r['tgltrans']) OR $r['tgltrans']=="0000-00-00"){
        $tgltrans = "";
    }else{
        $tgltrans = date('d F Y', strtotime($r['tgltrans']));
        $tgltrans = date('d/m/Y', strtotime($r['tgltrans']));
    }
    if (empty($r['tgltrm']) OR $r['tgltrm']=="0000-00-00"){
        $tgltrm = "";
    }else{
        $tgltrm = date('d F Y', strtotime($r['tgltrm']));
        $tgltrm = date('d/m/Y', strtotime($r['tgltrm']));
    }
?>
<script> window.onload = function() { document.getElementById("e_nobr").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=edittransfer&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            <input type='hidden' id='u_act' name='u_act' value='edittransfer' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <button type='button' class='btn btn-success' onclick='disp_confirm("Edit Data ?")'>Save</button>
                            <small>edit data transfer / realisasi</small>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="font-weight: normal;">ID <span class='required'></span></label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_nobr' name='e_nobr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $r['brId']; ?>' Readonly>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="font-weight: normal;">Tanggal <span class='required'></span></label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_tglinput' name='e_tglinput' autocomplete='off' 
                                   class='form-control col-md-7 col-xs-12' value='<?PHP echo $tglinput; ?>' data-inputmask="'mask': '99/99/9999'" Readonly>
                        </div>
                    </div>
                    
                    <div class='form-group'>
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="font-weight: normal;">Nama Dokter <span class='required'></span></label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <input type='text' id='e_dokter' name='e_dokter' autocomplete='off' 
                                   class='form-control col-md-7 col-xs-12' value='<?PHP echo $r['nama_dokter']; ?>' Readonly>
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
                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Tanggal Transfer </label>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <div class='input-group date' id='mytgl01'>
                                <input type="text" class="form-control" id='e_tgltrans' name='e_tgltrans' autocomplete='off' required='required' placeholder='dd/MM/yyyy' 
                                       data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgltrans; ?>'>
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
                    
                    
                </div>
            </div>
        </form>
        
    </div>
</div>