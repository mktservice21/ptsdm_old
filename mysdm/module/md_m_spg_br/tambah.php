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
</style>

<script>
                                    
    $(document).ready(function() {

        $('#e_tglbrxx').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'MM yy',
            onSelect: function(dateStr) {
                
            },
            onClose: function() {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                showDataGaji();
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

<style>
    .ui-datepicker-calendarX {
        display: none;
    }
</style>

<script>    

$(function() {
    $('#e_tglbr').datepicker({
        showButtonPanel: true,
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        firstDay: 1,
        dateFormat: 'dd/mm/yy',
        /*
        minDate: '0',
        maxDate: '+2Y',
        */
        onSelect: function(dateStr) {
            showDataGaji();
        } 
    });
});
    
function showSPG(ocabang, ospg) {
    var ecab = document.getElementById(ocabang).value;
    $.ajax({
        type:"post",
        url:"module/md_m_spg_br/viewdata.php?module=viewspg&data1="+ocabang+"&data2="+ospg,
        data:"ucab="+ecab+"&uarea="+ospg,
        success:function(data){
            $("#"+ospg).html(data);
            showDataGaji();
        }
    });
}

function showDataGaji() {
    showDataSPG();
}

function showDataSPG() {
    var espg = document.getElementById('cb_spg').value;
    var ecabang = document.getElementById('cb_cabang').value;
    var etgl = document.getElementById('e_tglbr').value;
    
    $.ajax({
        type:"post",
        url:"module/md_m_spg_br/viewdata.php?module=viewdataspg",
        data:"uspg="+espg+"&ucabang="+ecabang+"&utgl="+etgl,
        success:function(data){
            $("#data_spg").html(data);
        }
    });
}

function hit_total() {

    nhk = document.getElementById('e_hk').value;  
    nmakan = document.getElementById('e_makan').value;

    var newchar = '';
    
    var myhk = nhk;  
    myhk = myhk.split(',').join(newchar);
    var mymakan = nmakan;  
    mymakan = mymakan.split(',').join(newchar);

    totalmakan = myhk*mymakan;
    document.getElementById('e_totmakan').value = totalmakan;
    
    ngaji = document.getElementById('e_gaji').value;
    ninsentif = document.getElementById('e_insentif').value;
    nsewa = document.getElementById('e_sewa').value;
    npulsa = document.getElementById('e_pulsa').value;
    nparkir = document.getElementById('e_parkir').value;
    
    
    ngaji = ngaji.split(',').join(newchar);
    ninsentif = ninsentif.split(',').join(newchar);
    nsewa = nsewa.split(',').join(newchar);
    npulsa = npulsa.split(',').join(newchar);
    nparkir = nparkir.split(',').join(newchar);
    
    
    total_ = parseInt(totalmakan)+parseInt(ngaji)+parseInt(ninsentif)+parseInt(nsewa)+parseInt(npulsa)+parseInt(nparkir);
    
    document.getElementById('e_total').value = total_;
}
    
function disp_confirm(pText_,ket)  {
    
    var enama =document.getElementById('cb_spg').value;
    var ehk =document.getElementById('e_hk').value;
    
    if (enama==""){
        alert("SPG masih kosong....");
        return 0;
    }
    
    if (ehk==""){
        alert("Hari kerja masih kosong....");
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
            document.getElementById("demo-form2").action = "module/md_m_spg_br/aksi_spgbr.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
$pidbrspg="";
$hari_ini = date("Y-m-d");
$tgl1 = date('F Y', strtotime($hari_ini));
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = "";
$ptgllahir = "";
$ptlahir = "";

$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP'];

$pidcabang="";
$pidspg="";
$pketerangan="";

$penempatan="";
$pharikerja="";
$pgaji="";
$pmakan="";
$ptotmakan="";
$psewakendaraan="";
$ppulsa="";
$pparkir="";
$pinsentif=0;
$totalrp="";

$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_spg_br0 WHERE idbrspg='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $pidbrspg=$r['idbrspg'];
    $tgl1 = date('d/m/Y', strtotime($r['tglbr']));
    $pidcabang=$r['icabangid'];
    $pidspg=$r['id_spg'];
    $pharikerja=$r['harikerja'];
    $totalrp=$r['total'];
    $pketerangan=$r['keterangan'];
    
    $edit1 = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_spg_br1 WHERE idbrspg='$_GET[id]' order by kodeid");
    while ($r1=mysqli_fetch_array($edit1)) {
        $pkodeid=$r1['kodeid'];
        
        if ($pkodeid=="01") $pinsentif=$r1['rp'];
        if ($pkodeid=="02") $pgaji=$r1['rp'];
        if ($pkodeid=="03") {
            $pmakan=$r1['rp'];
            $ptotmakan=$r1['rptotal'];
        }
        if ($pkodeid=="04") $psewakendaraan=$r1['rp'];
        if ($pkodeid=="05") $ppulsa=$r1['rp'];
        if ($pkodeid=="06") $pparkir=$r1['rp'];
        
    }
    

    
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
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbrspg; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl02_xx'>
                                            <input type="text" class="form-control" id='e_tglbr' name='e_tglbr' autocomplete="off" required='required' placeholder='F Y' value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_cabang' name='cb_cabang' onchange="showSPG('cb_cabang', 'cb_spg')">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query = "select icabangid_o, nama from dbmaster.v_icabang_o WHERE (aktif='Y' OR icabangid_o='$pidcabang') ";
                                            $query .=" order by nama";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $iidcabang=$z['icabangid_o'];
                                                $inmcabang=$z['nama'];
                                                
                                                if ($iidcabang==$pidcabang)
                                                    echo "<option value='$iidcabang' selected>$inmcabang</option>";
                                                else
                                                    echo "<option value='$iidcabang'>$inmcabang</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_spg'>SPG <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' name='cb_spg' id='cb_spg' onchange="showDataGaji()">
                                            <option value="">-- Pilihan --</option>
                                            <?PHP
                                            if ($_GET['act']=="editdata") {
                                                $cabang = $pidcabang;

                                                $query = "select * from MKT.spg where icabangid='$cabang' order by nama";

                                                $tampil=mysqli_query($cnit, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $eidspg=(int)$a['id_spg'];
                                                    $enmspg=$a['nama'];
                                                    if ((int)$a['id_spg']==$pidspg) {
                                                        $penempatan=$a['penempatan'];
                                                        $tgl1="";
                                                        $tgl2="";
                                                        echo "<option value='$eidspg' selected>$enmspg</option>";
                                                    }else
                                                        echo "<option value='$eidspg'>$enmspg</option>";
                                                }
                                            }
                                             
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div id="data_spg">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Penempatan <span class='required'></span></label>
                                        <div class='col-xs-6'>
                                            <input type='text' id='e_penempatan' name='e_penempatan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $penempatan; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Mulai <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_mulai' name='e_mulai' class='form-control col-md-7 col-xs-12' value='<?PHP echo $tgl1; ?>' Readonly>
                                            s/d.
                                            <input type='text' id='e_sampai' name='e_sampai' class='form-control col-md-7 col-xs-12' value='<?PHP echo $tgl2; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Insentif <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_insentif' name='e_insentif' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pinsentif; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Gaji Pokok <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_gaji' name='e_gaji' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pgaji; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jml. Hari Kerja <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_hk' name='e_hk' class='form-control col-md-7 col-xs-12 inputmaskrp2' onblur='hit_total()' value='<?PHP echo $pharikerja; ?>'>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Uang Makan <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_makan' name='e_makan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pmakan; ?>' Readonly>
                                            <input type='text' id='e_totmakan' name='e_totmakan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotmakan; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sewa Kendaraan <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_sewa' name='e_sewa' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $psewakendaraan; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pulsa <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_pulsa' name='e_pulsa' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ppulsa; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Parkir <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_parkir' name='e_parkir' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pparkir; ?>' Readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total <span class='required'></span></label>
                                        <div class='col-xs-3'>
                                            <input type='text' id='e_total' name='e_total' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $totalrp; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_keterangan' name='e_keterangan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>'>
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
   
