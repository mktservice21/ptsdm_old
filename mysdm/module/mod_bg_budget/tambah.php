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


<?PHP
$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));


                
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$keterangan="";
$pdivisi="";
$pjumlah="";
$pratio="";
$kodeid="";
    

$pjmljan="";
$pjmlfeb="";
$pjmlmar="";
$pjmlapr="";
$pjmlmei="";
$pjmljun="";
$pjmljul="";
$pjmlagus="";
$pjmlsep="";
$pjmlokt="";
$pjmlnov="";
$pjmldes="";
        
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_budget WHERE idbudget='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idbr=$r['idbudget'];
    $tglberlku = date('d/m/Y', strtotime($r['tglinput']));
    $tgl1 = $r['tahun'];
    $idajukan=$_SESSION['IDCARD']; 
    $nmajukan=$_SESSION['NAMALENGKAP']; 
    $pjumlah=$r['jumlah'];
    $pdivisi=$r['g_divisi'];
    $kodeid=$r['kodeid'];
    
    $pjmljan=$r['jan'];
    $pjmlfeb=$r['feb'];
    $pjmlmar=$r['mar'];
    $pjmlapr=$r['apr'];
    $pjmlmei=$r['mei'];
    $pjmljun=$r['jun'];
    $pjmljul=$r['jul'];
    $pjmlagus=$r['agu'];
    $pjmlsep=$r['sep'];
    $pjmlokt=$r['okt'];
    $pjmlnov=$r['nov'];
    $pjmldes=$r['des'];
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tahun </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='thn01'>
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
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowDataKode()">
                                            <option value=''>-- Pilihan --</option>
                                            <?PHP
                                            if ($pdivisi=="OTC") {
                                                echo "<option value='ETH'>ETHICAL</option>";
                                                echo "<option value='OTC' selected>OTC</option>";
                                            }else{
                                                echo "<option value='ETH' selected>ETHICAL</option>";
                                                echo "<option value='OTC'>OTC</option>";
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

                                
                            </div>
                            
                            <div hidden class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Januari
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmljan; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Februari
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmlfeb; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Maret
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmlmar; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                April
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmlapr; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Mei
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmlmei; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Juni
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmljun; ?>'>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div hidden class='col-md-6 col-xs-12'>
                                <div class='x_panel'>
                                    <div class='x_content form-horizontal form-label-left'>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Juli
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmljul; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Agustus
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmlagus; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                September
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmlsep; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Oktober
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmlokt; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                November
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmlnov; ?>'>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                                Desember
                                            </label>
                                            <div class='col-md-6'>
                                                <input type='text' id='e_jmlbln[]' name='e_jmlbln[]' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur="updateTotal()" value='<?PHP echo $pjmldes; ?>'>
                                            </div>
                                        </div>

                                    </div>
                                </div>
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
    <!--end row-->
</div>


<script type="text/javascript">
    
    function ShowDataKode(){
        var edivisi = document.getElementById('cb_divisi').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_bg_budget/viewdata.php?module=viewdatakode",
            data:"udivisi="+edivisi,
            success:function(data){
                $("#cb_kodeid").html(data);
            }
        });
    }   

    function updateTotal() {
        var newchar = '';
        var total = 0;//
        var list = document.getElementsByName('e_jmlbln[]');
        var values = [];
        for(var i = 0; i < list.length; ++i) {
            a1 = list[i].value;
            if (a1!="") {
                a1 = a1.split(',').join(newchar);
                total=parseInt(total)+parseInt(a1);
            }
            //values.push(parseFloat(list[i].value));
        }
        //total = values.reduce(function(previousValue, currentValue, index, array){
        //    return previousValue + currentValue;
        //});

        document.getElementById("e_jumlah").value = total;    
    }

    function disp_confirm(pText_,ket)  {
        var edivisi = document.getElementById('cb_divisi').value;
        var ekodeid = document.getElementById('cb_kodeid').value;

        if (edivisi==""){
            alert("divisi masih kosong....");
            return 0;
        }

        if (ekodeid==""){
            alert("kodeid masih kosong....");
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
                document.getElementById("demo-form2").action = "module/mod_bg_budget/aksi_budgetteam.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
</script>