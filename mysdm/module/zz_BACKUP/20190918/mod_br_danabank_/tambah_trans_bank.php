<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    $act="input";
    $aksi="";
    
    $pidinputbank=$_POST['uidbank'];
    $pjumlah="";
    
    $sql = "SELECT * FROM dbmaster.t_suratdana_bank WHERE idinputbank='$pidinputbank'";
    $tampil= mysqli_query($cnmy, $sql);
    $row= mysqli_fetch_array($tampil);
    $pptgl=$row['tanggal'];
    $tgl1 = date('d/m/Y', strtotime($pptgl));
    $pnobukti=$row['nobukti'];
    $pnodivisi=$row['nodivisi'];
?>


    <!-- bootstrap-datetimepicker -->
    <link href="vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">

    <script src="js/hanyaangka.js"></script>
    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!--input mask -->
    <script src="js/inputmask.js"></script>


    
<script> window.onload = function() { document.getElementById("e_jumlah").focus(); } </script>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Input Biaya Transfer</h4>
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
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_idinputbank' name='e_idinputbank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinputbank; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Transaksi </label>
                                            <div class='col-md-3'>
                                                <div class='input-group date' id='mytgl01'>
                                                    <input type="text" class="form-control" id='e_tglmasuk' name='e_tglmasuk' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>' readonly>
                                                    <span class='input-group-addon'>
                                                        <span class='glyphicon glyphicon-calendar'></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No BR/Divisi <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_bukti' name='e_bukti' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnodivisi; ?>' readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Bukti <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_bukti' name='e_bukti' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnobukti; ?>' readonly>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_jumlah' name='e_jumlah' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class="checkbox">
                                                    <button type='button' id='nm_btn_save' class='btn btn-success' onclick='disp_confirm_trans("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
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
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
    </div>
</div>


        <script type='text/javascript' src='datetime/js/jquery-ui.min.js'></script>
       
        <!-- jquery.inputmask -->
        <script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
        
        <!-- bootstrap-daterangepicker -->
        <script src="vendors/moment/min/moment.min.js"></script>
        <script src="vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
        <script src="vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
        <!-- Custom Theme Scripts -->
        
        
<script>
    $('#mytgl01x, #mytgl02x').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
    
    
    
    function ShowSimpanSPDBank(){
        var e_bank = document.getElementById('cb_banksimpan').value;
        if (e_bank=="" || e_bank=="N"){
            d_isibank.style.display = 'none';
        }else{
            d_isibank.style.display = 'block';
        }
    }
    
    
    function disp_confirm_trans(pText_,nid)  {
        // pText_, nid
        var eact="inputtransbank";
        var eidbank = document.getElementById("e_idinputbank").value;
        var etglmasuk = document.getElementById("e_tglmasuk").value;
        var ejumlah = document.getElementById("e_jumlah").value;
        
        
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
                    url:"module/mod_br_danabank/simpan_bank_transfer.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidbank="+eidbank+"&utgl="+etglmasuk+"&ujumlah="+ejumlah,
                    success:function(data){
                        if (data.length > 2) {
                            alert(data);
                        }
                        nm_btn_save.style.display='none';
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

