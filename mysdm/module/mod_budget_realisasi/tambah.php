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

<script>    


function disp_confirm(pText_,ket)  {
    
    var ecoa =document.getElementById('cb_kodeid').value;
    
    if (ecoa==""){
        alert("coa masih kosong....");
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
            //document.write("You pressed OK!")
            document.getElementById("demo-form2").action = "module/mod_budget_realisasi/aksi_budg_realisasi.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}

    $(function() {
        $('#e_tglberlaku').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            firstDay: 1,
            dateFormat: 'MM yy',
            onSelect: function(dateStr) {
                
            },
            onClose: function() {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                
            },

            beforeShow: function() {
                if ((selDate = $(this).val()).length > 0) 
                {
                    iYear = selDate.substring(selDate.length - 4, selDate.length);
                    iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                    $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                }
            }
        });
    });
    
</script>

<?PHP
$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('F Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));


                
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$keterangan="";
$divisi="";
$pjumlah="";
$pratio="";
$kodeid="";
    
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_budget_realisasi WHERE idno='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idbr=$r['idno'];
    $tglberlku = date('d/m/Y', strtotime($r['tglinput']));
    $tgl1 = date('F Y', strtotime($r['bulan']));
    $idajukan=$_SESSION['IDCARD']; 
    $nmajukan=$_SESSION['NAMALENGKAP']; 
    $pjumlah=$r['jumlah'];
    $pratio=$r['ratio'];
    $divisi=$r['divisi'];
    $kodeid=$r['kodeid'];
    
}
    
?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>


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

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl01x'>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='MM yyyy'  value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTHER') ";
                                            if ($_SESSION['DIVISI']=="OTC") {
                                                $query .=" AND DivProdId = 'OTC' ";
                                            }
                                            $query .=" order by DivProdId";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                if ($z['DivProdId']==$divisi)
                                                    echo "<option value='$z[DivProdId]' selected>$z[DivProdId]</option>";
                                                else
                                                    echo "<option value='$z[DivProdId]'>$z[DivProdId]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_kodeid'>Kode <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_kodeid' name='cb_kodeid' data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP 
                                                    $query = "select kodeid, nama, urutan from dbmaster.t_budget_kode order by urutan";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        if ($z['kodeid']==$kodeid)
                                                            echo "<option value='$z[kodeid]' selected>$z[nama] - $z[nama]</option>";
                                                        else
                                                            echo "<option value='$z[kodeid]'>$z[kodeid] - $z[nama]</option>";
                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Jumlah
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jumlah' name='e_jumlah' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Cost Ratio
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_ratio' name='e_ratio' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pratio; ?>'>
                                    </div>
                                </div>


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
    <!--end row-->
</div>


    <script type="text/javascript">
        $(function() {
            $('#tglfrom').datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                firstDay: 1,
                dateFormat: 'dd MM yy',
                /*
                minDate: '0',
                maxDate: '+2Y',
                */
                onSelect: function(dateStr) {
                    var min = $(this).datepicker('getDate');
                    $('#tglto').datepicker('option', 'minDate', min || '0');
                    datepicked();
                } 
            });
            $('#tglto').datepicker({
                changeMonth: true,
                changeYear: true,
                numberOfMonths: 1,
                firstDay: 1,
                dateFormat: 'dd MM yy',
                minDate: '0',
                /*
                minDate: '0',
                maxDate: '+2Y',
                */
                onSelect: function(dateStr) {
                    var max = $(this).datepicker('getDate');
                    $('#tglfrom').datepicker('option', 'maxDate', max || '+2Y');
                    datepicked();
                } 
            });
        });
        var datepicked = function() {
            var from = $('#from');
            var to = $('#to');
            var nights = $('#nights');
            var fromDate = from.datepicker('getDate')
            var toDate = to.datepicker('getDate')
            if (toDate && fromDate) {
                var difference = 0;
                var oneDay = 1000 * 60 * 60 * 24;
                var difference = Math.ceil((toDate.getTime() - fromDate.getTime()) / oneDay);
                nights.val(difference);
            }
        }
    </script>
    