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

function hit_total() {

    var newchar = '';
    
    nmakan = document.getElementById('re_makan').value;
    ngaji = document.getElementById('re_gaji').value;
    ninsentif = document.getElementById('re_insentif').value;
    nsewa = document.getElementById('re_sewa').value;
    npulsa = document.getElementById('re_pulsa').value;
    nparkir = document.getElementById('re_parkir').value;
    
    if (nmakan=="") nmakan="0";
    if (ngaji=="") ngaji="0"; 
    if (ninsentif=="") ninsentif="0";
    if (nsewa=="") nsewa="0";
    if (npulsa=="") npulsa="0";
    if (nparkir=="") nparkir="0";
    
    nmakan = nmakan.split(',').join(newchar);
    ngaji = ngaji.split(',').join(newchar);
    ninsentif = ninsentif.split(',').join(newchar);
    nsewa = nsewa.split(',').join(newchar);
    npulsa = npulsa.split(',').join(newchar);
    nparkir = nparkir.split(',').join(newchar);
    
    
    total_ = parseInt(nmakan)+parseInt(ngaji)+parseInt(ninsentif)+parseInt(nsewa)+parseInt(npulsa)+parseInt(nparkir);
    
    document.getElementById('re_total').value = total_;
}
   
$(function() {
    $('#e_tgltrans, #e_tglreal').datepicker({
        showButtonPanel: false,
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
            
        } 
    });
});

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
            document.getElementById("demo-form2").action = "module/md_m_spg_br/aksi_spgbrreal.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
$hari_ini = date("Y-m-d");
$trans_ini = date("d/m/Y");
$tglreal = date("d/m/Y");
$tgltrans="";
    $act="realisasi";
    
    $penempatan="";
    $tgl2="";
            
    $pgaji="";
    $pmakan="";
    $ptotmakan="";
    $psewakendaraan="";
    $ppulsa="";
    $pparkir="";
    $pinsentif=0;
    
    $rpgaji="";
    $rpmakan="";
    $rptotmakan="";
    $rpsewakendaraan="";
    $rppulsa="";
    $rpparkir="";
    $rpinsentif=0;
    $rtotalrp="";
    
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_spg_br0 WHERE idbrspg='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $pidbrspg=$r['idbrspg'];
    $tgl1 = date('d F Y', strtotime($r['tglbr']));
    
    if (!empty($r['tgltrans']) AND $r['tgltrans']<>"0000-00-00")
        $tgltrans = date('d/m/Y', strtotime($r['tgltrans']));
    
    if (!empty($r['tglreal']) AND $r['tglreal']<>"0000-00-00")
        $tglreal = date('d/m/Y', strtotime($r['tglreal']));
    
    $pidcabang=$r['icabangid'];
    $pidspg=$r['id_spg'];
    $pharikerja=$r['harikerja'];
    $totalrp=$r['total'];
    $rtotalrp=$r['realisasi'];
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
        
        
        if ($pkodeid=="01") $rpinsentif=$r1['realisasirp'];
        if ($pkodeid=="02") $rpgaji=$r1['realisasirp'];
        if ($pkodeid=="03") $rpmakan=$r1['realisasirp'];
        if ($pkodeid=="04") $rpsewakendaraan=$r1['realisasirp'];
        if ($pkodeid=="05") $rppulsa=$r1['realisasirp'];
        if ($pkodeid=="06") $rpparkir=$r1['realisasirp'];
        
    }
    
    
?>

<script> window.onload = function() { document.getElementById("e_tglreal").focus(); } </script>


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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl02_xx'>
                                            <input type="text" class="form-control" id='e_tglbr' name='e_tglbr' autocomplete="off" required='required' placeholder='F Y' value='<?PHP echo $tgl1; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_cabang' name='cb_cabang'>
                                            <?PHP
                                            $query = "select icabangid_o, nama from dbmaster.v_icabang_o WHERE icabangid_o='$pidcabang' ";
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
                                        <select class='soflow' name='cb_spg' id='cb_spg'>
                                            <?PHP
                                                $cabang = $pidcabang;

                                                $query = "select * from MKT.spg where id_spg='$pidspg'";

                                                $tampil=mysqli_query($cnit, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $eidspg=(int)$a['id_spg'];
                                                    $enmspg=$a['nama'];
                                                    
                                                        $penempatan=$a['penempatan'];
                                                        $tgl1="";
                                                        $tgl2="";
                                                        echo "<option value='$eidspg' selected>$enmspg</option>";
                                                }
                                             
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Penempatan <span class='required'></span></label>
                                    <div class='col-xs-3'>
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_keterangan' name='e_keterangan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                            </div>
                            
                        </div>
                    </div>
                    

                </div>
            </div>
            
            
            <!--Kiri-->
            <div class='col-md-6 col-xs-12'>
                <div class='x_panel'>
                    <div class='x_content form-horizontal form-label-left'><br />
                        <b><u>Realisasi</u></b>
                        <div id="data_spg">

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Realisasi </label>
                                <div class='col-md-9'>
                                    <div class='input-group date' id='mytgl02_xx'>
                                        <input type="text" class="form-control" id='e_tglreal' name='e_tglreal' autocomplete="off" required='required' placeholder='<?PHP echo $trans_ini; ?>' value='<?PHP echo $tglreal; ?>'>
                                        <span class='input-group-addon'>
                                            <span class='glyphicon glyphicon-calendar'></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Insentif <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='re_insentif' name='re_insentif' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $rpinsentif; ?>' onblur='hit_total()'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Gaji Pokok <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='re_gaji' name='re_gaji' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $rpgaji; ?>' onblur='hit_total()'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Uang Makan <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='re_makan' name='re_makan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $rpmakan; ?>' onblur='hit_total()'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sewa Kendaraan <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='re_sewa' name='re_sewa' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $rpsewakendaraan; ?>' onblur='hit_total()'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pulsa <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='re_pulsa' name='re_pulsa' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $rppulsa; ?>' onblur='hit_total()'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Parkir <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='re_parkir' name='re_parkir' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $rpparkir; ?>' onblur='hit_total()'>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='re_total' name='re_total' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $rtotalrp; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl. Transfer </label>
                                <div class='col-md-9'>
                                    <div class='input-group date' id='mytgl02_xx'>
                                        <input type="text" class="form-control" id='e_tgltrans' name='e_tgltrans' autocomplete="off" required='required' placeholder='<?PHP echo $trans_ini; ?>' value='<?PHP echo $tgltrans; ?>'>
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
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                        <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>           
            </div>
            
            
            
            <!--Kanan-->
            <div class='col-md-6 col-xs-12'>
                <div class='x_panel'>
                    <div class='x_content form-horizontal form-label-left'><br />

                        <div id="data_spg">

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Insentif <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_insentif' name='e_insentif' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pinsentif; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Gaji Pokok <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_gaji' name='e_gaji' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pgaji; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jml. Hari Kerja <span class='required'></span></label>
                                <div class='col-xs-6'>
                                    <input type='text' id='e_hk' name='e_hk' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pharikerja; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Uang Makan <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_makan' name='e_makan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pmakan; ?>' Readonly>
                                    <input type='text' id='e_totmakan' name='e_totmakan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotmakan; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sewa Kendaraan <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_sewa' name='e_sewa' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $psewakendaraan; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pulsa <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_pulsa' name='e_pulsa' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ppulsa; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Parkir <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_parkir' name='e_parkir' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pparkir; ?>' Readonly>
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total <span class='required'></span></label>
                                <div class='col-xs-9'>
                                    <input type='text' id='e_total' name='e_total' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $totalrp; ?>' Readonly>
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
   
