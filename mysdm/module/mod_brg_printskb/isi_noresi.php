<?PHP
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_combo.php";
    $act="input";
    $aksi="";
    
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('d/m/Y', strtotime($hari_ini));
    $pnoresi="";
    $pidgroup=$_POST['uidgroup'];
    $pidgrpprint=$_POST['uidgrpprint'];
    $pidkeluar=$_POST['uidkeluar'];
    $pjumlah="";
    $pketkirim="";
    
    $sql = "SELECT * FROM dbmaster.t_barang_keluar_kirim WHERE IGROUP='$pidgroup' AND GRPPRINT='$pidgrpprint'";
    $tampil= mysqli_query($cnmy, $sql);
    $row= mysqli_fetch_array($tampil);
    $pptgl=$row['TGLKIRIM'];
    if (!empty($pptgl))
        $tgl_pertama = date('d/m/Y', strtotime($pptgl));
    $pnoresi=$row['NORESI'];
    $pketkirim=$row['KETKIRIM'];
    
    $sql = "SELECT TGLTERIMA FROM dbmaster.t_barang_keluar_kirim WHERE IGROUP='$pidgroup' AND GRPPRINT='$pidgrpprint' AND IFNULL(TGLTERIMA,'')<>'' AND IFNULL(TGLTERIMA,'0000-00-00')<>'0000-00-00'";
    $tampil= mysqli_query($cnmy, $sql);
    $row= mysqli_fetch_array($tampil);
    $pptglterima=$row['TGLTERIMA'];
    if ($pptglterima=="0000-00-00") $pptglterima="";
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
            <h4 class='modal-title'>Input No Resi</h4>
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


                                        <div hidden class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_idgroup' name='e_idgroup' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidgroup; ?>' Readonly>
                                                <input type='text' id='e_idprintgroup' name='e_idprintgroup' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidgrpprint; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Kirim </label>
                                            <div class='col-md-3'>
                                                <div class='input-group date' id='mytgl01'>
                                                    <input type="text" class="form-control" id='e_tglkirim' name='e_tglkirim' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl_pertama; ?>' readonly>
                                                    <span class='input-group-addon'>
                                                        <span class='glyphicon glyphicon-calendar'></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Resi <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_noresi' name='e_noresi' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnoresi; ?>' >
                                                *)kosongkan noresi untuk menghapus/batal input, lalu klik save
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_ketkirim' name='e_ketkirim' class='form-control col-md-7 col-xs-12' placeholder="keterangan / ekspedisi" value='<?PHP echo $pketkirim; ?>' >
                                            </div>
                                        </div>
                                        
                                        <?PHP
                                        if (empty($pptglterima)) {
                                        ?>
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class="checkbox">
                                                    <button type='button' id='nm_btn_save' class='btn btn-success' onclick='disp_confirm_trans("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                                </div>
                                            </div>
                                        </div>
                                        <?PHP
                                        }
                                        ?>
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
    $('#mytgl01, #mytgl02x').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'DD/MM/YYYY'
    });
    
    function disp_confirm_trans(pText_,nid)  {
        // pText_, nid
        var eact="inputnoresi";
        var eidgroup = document.getElementById("e_idgroup").value;
        var eidprintgroup = document.getElementById("e_idprintgroup").value;
        var etglkirim = document.getElementById("e_tglkirim").value;
        var enoresi = document.getElementById("e_noresi").value;
        var eketkirim = document.getElementById("e_ketkirim").value;
        
        if (eidgroup=="") {
            alert("tidak ada data yang disimpan..."); return false;
        }
        
        if (eidprintgroup=="") {
            alert("tidak ada data yang disimpan..."); return false;
        }
        
        
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
                    url:"module/mod_brg_printskb/simpan_noresi.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidgroup="+eidgroup+"&uidprintgroup="+eidprintgroup+"&utgl="+etglkirim+"&unoresi="+enoresi+"&uketkirim="+eketkirim,
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

