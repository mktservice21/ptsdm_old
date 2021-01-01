<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<style>
    #nwinbaru .form-group, #nwinbaru .input-group, #nwinbaru .control-label {
        margin-bottom:3px;
    }
    #nwinbaru .control-label {
        font-size:12px;
    }
    #nwinbaru input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    #nwinbaru select.soflow {
        font-size:12px;
        height: 30px;
    }
    #nwinbaru .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    #nwinbaru .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
</style>


<?PHP
session_start();
include "../../config/koneksimysqli.php";
$hari_ini = date("Y-m-d");
$act="simpanbpjskerja";
$aksi="";
$pidspg=$_POST['uidspg'];

$pblnbpjs = date('F Y', strtotime($hari_ini));
$pnobpjskerja="";

$query = "select * from dbmaster.t_spg_bpjs WHERE id_spg='$pidspg'";
$tampil= mysqli_query($cnmy, $query);
$ketemu=mysqli_num_rows($tampil);
if ((INT)$ketemu>0) {
    $row= mysqli_fetch_array($tampil);
    $pbln=$row['bulan'];
    $pnobpjskerja=$row['nobpjs_kerja'];
    
    if ($pbln=="0000-00-00") $pbln="";
    
    if (!empty($pbln)) $pblnbpjs = date('F Y', strtotime($pbln));
    
}
?>

    <!--input mask -->
    <script src="js/inputmask.js"></script>


    
<script> window.onload = function() { document.getElementById("e_jumlah").focus(); } </script>
<div id="nwinbaru">
<div class='modal-dialog modal-lg'>
    
    <!-- Modal content-->
    <div class='modal-content'>
        
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Isi Data BPJS Ketenagakerjaan</h4>
        </div>
        
        <div class="">
            
            <!--row-->
            <div class="row">
                
                <form method='POST' action='<?PHP echo "$aksi?module=importdataspg&act=input&idmenu=282"; ?>' 
                      id='form_data01' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
                
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>
                            
                          
                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID SPG <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_spgid' name='e_spgid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidspg; ?>' Readonly>
                                            </div>
                                        </div>
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' >Bulan </label>
                                            <div class='col-md-6 col-sm-6 col-xs-12'>
                                                <div class='input-group date' id='cbln01'>
                                                    <input type="text" class="form-control" id='e_bulan' name='e_bulan' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $pblnbpjs; ?>'>
                                                    <span class='input-group-addon'>
                                                        <span class='glyphicon glyphicon-calendar'></span>
                                                    </span>
                                                </div>

                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No BPJS <span class='required'></span></label>
                                            <div class='col-md-6 col-sm-6 col-xs-12'>
                                                <input type='text' id='e_nobpjs' name='e_nobpjs' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pnobpjskerja; ?>'>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <div class="checkbox">
                                                    <button type='button' id='nm_btn_save' class='btn btn-success' onclick='disp_confirm_bpjskerja("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
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
        
        
    </div>
    
    
</div>
</div>

<script>
    $('#cbln01, #cbln02').datetimepicker({
        ignoreReadonly: true,
        allowInputToggle: true,
        format: 'MMMM YYYY'
    });
    
    function disp_confirm_bpjskerja(pText_,nid)  {
        var eact="simpanbpjskerja";
        var eidspg = document.getElementById("e_spgid").value;
        var ebulan = document.getElementById("e_bulan").value;
        var enpbpjs = document.getElementById("e_nobpjs").value;
        
        
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
                    url:"module/md_m_spg_importdata/simpan_bpjskerja.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidspg="+eidspg+"&ubulan="+ebulan+"&unpbpjs="+enpbpjs,
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

<?PHP
mysqli_close($cnmy);
?>