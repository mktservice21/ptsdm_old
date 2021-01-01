<?php

    session_start();
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_combo.php";
    $act="input";
    $aksi="";
    
    $pdivisipilih=$_POST['udivisi'];
    $pidbr=$_POST['uidbr'];
    
    $hari_ini = date("Y-m-d");
    $tgl1 = date('d/m/Y', strtotime($hari_ini));
    $tgl1="";
    $pnoseri="";
    
    if ($pdivisipilih=="OTC")
        $query = "select noseri_pph, tgl_fp_pph from hrd.br_otc WHERE brOtcId='$pidbr'";
    elseif ($pdivisipilih=="KD")
        $query = "select noseri_pph, tgl_fp_pph from hrd.klaim WHERE klaimId='$pidbr'";
    else
        $query = "select noseri_pph, tgl_fp_pph from hrd.br0 WHERE brId='$pidbr'";
    
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pnoseri=$row['noseri_pph'];
    if (!empty($row['tgl_fp_pph']) AND $row['tgl_fp_pph']<>"0000-00-00") {
        $tgl1 = date('d/m/Y', strtotime($row['tgl_fp_pph']));
    }
    
?>

<!--input mask -->
<script src="js/inputmask.js"></script>
<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Isi Info Data Pajak BR</h4>
        </div>

        
        
        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=brdanabank&act=input&idmenu=258"; ?>' id='demo-form4' name='form4' data-parsley-validate class='form-horizontal form-label-left'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>

                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID BR <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                                <input type='hidden' id='e_divisi_p' name='e_divisi_p' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivisipilih; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                                                                
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Seri PPH <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_noseri' name='e_noseri' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnoseri; ?>'>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Faktur Pajak PPH </label>
                                            <div class='col-md-3'>
                                                <div class='input-group date' id='mytgl01'>
                                                    <input type="text" class="form-control" id='e_tgl' name='e_tgl' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
                                                    <span class='input-group-addon'>
                                                        <span class='glyphicon glyphicon-calendar'></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class="checkbox">
                                                    <button type='button' class='btn btn-success' onclick='disp_confirm_pajak("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                                </div>
                                            </div>
                                        </div>
                                        *)<b>untuk menghapus noseri dan tgl. faktur pajak, kosongkan dan klik tombol save</b>
                                    </div>

                                </div>
                            </div>


                        </div>
                    </div>


                </form>

            </div>
            <!--end row-->
        </div>
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
    </div>
</div>



<script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>


<script>
    $('#mytgl01, #mytgl02').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
    
    
    $('#mytgl01, #mytgl02').on('change dp.change', function(e){
        
    });
    
    
    function disp_confirm_pajak(pText_,ket)  {
        var eact=ket;
        var eidbr = document.getElementById("e_id").value;
        var edivisi_p = document.getElementById("e_divisi_p").value;
        
        var enoseri = document.getElementById("e_noseri").value;
        var etglfp = document.getElementById("e_tgl").value;
        
        //alert(eact+" : "+eidbr+", "+edivisi+" : "+enoseri+", "+etglfp); return false;
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                
                $.ajax({
                    type:"post",
                    url:"module/surabaya/mod_sby_dbr/simpan_data_pajak.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidbr="+eidbr+"&udivisi_p="+edivisi_p+"&unoseri="+enoseri+"&utglfp="+etglfp,
                    success:function(data){
                        if (data.length > 2) {
                            alert(data);
                        }
                        $('#myModal').modal('hide');
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
</script>