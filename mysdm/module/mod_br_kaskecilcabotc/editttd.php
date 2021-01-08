<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
?>
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
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
</style>

<script>    


function disp_confirm(pText_,ket)  {
    var iid = document.getElementById('e_id').value;
    
    if (ikry=="") {
        alert("Pembuat masih kosong...");
        return false;
    }
        
}


</script>

<?PHP
$idbr="";
$pidkodeinput="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));

$pbulanpilih = date('F Y', strtotime($hari_ini));

$pidgroup=$_SESSION['GROUP'];
                
$pidcardpl=$_SESSION['IDCARD'];
$idajukan=$_SESSION['IDCARD'];
$nmajukan=$_SESSION['NAMALENGKAP']; 
$keterangan="";
$pdivisi="OTC";
if ($_SESSION['DIVISI']=="OTC") $pdivisi="OTC";


$ptxthidden="hidden";
//if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26" OR $pidgroup=="1") $ptxthidden="";
if ($pidgroup=="1") $ptxthidden="";

$ptxthidden2="hidden";
if ($pidgroup=="1") $ptxthidden2="";



$untukpil1="";
$untukpil2="selected";
        
$pjumlah="";
$jumlahk="";
$coa="";
$pnamauntuk="";
$pketerangan="";
$idkdoepilih="";

$pcabangid="";
$pcabangid_o="";
$pareaid="";
$ppilcoa="";

$pjabatanid="";


$pkdspv="";
$pnamaspv="";
$pkddm="";
$pnamadm="";
$pkdsm="";
$pnamasm="";
$pkdgsm="";
$pnamagsm="";


$act="updatettd";

$edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_kaskecilcabang WHERE idkascab='$_GET[id]'");
$r    = mysqli_fetch_array($edit);
$idbr=$r['idkascab'];
$pidkodeinput=$r['idkascab'];
$tglberlku = date('d/m/Y', strtotime($r['tanggal']));
$tgl1 = date('d/m/Y', strtotime($r['tanggal']));
$pbulanpilih = date('F Y', strtotime($r['bulan']));
$idajukan=$r['karyawanid']; 
$keterangan=$r['keterangan'];
$pjumlah=$r['jumlah'];
$pdivisi=$r['divisi'];
$pcabangid=$r['icabangid'];
$pcabangid_o=$r['icabangid_o'];
$pareaid=$r['areaid_o'];
$ppilcoa=$r['coa4'];
$pjabatanid=$r['jabatanid'];
$pnmreal=$r['nmrealisasi'];
$pnorekening=$r['norekening'];
    
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
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan (Periode PC / Kas Kecil) </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id=''>
                                            <input type='text' class='form-control' id='e_bulan' name='e_bulan' autocomplete='off' value='<?PHP echo $pbulanpilih; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pengajuan <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_untuk' name='cb_untuk' onchange="ShowDataPengajuan();" data-live-search="true">
                                            <?PHP
                                                //echo "<option value='ETH' $untukpil1>Ethical</option>";
                                                echo "<option value='OTC' $untukpil2>CHC</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pembuat <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' onchange="ShowDataKaryawan();" data-live-search="true">
                                              <option value='' selected>-- Pilihan --</option>
                                              <?PHP 
                                                    $query = "select karyawanId, nama From hrd.karyawan
                                                        WHERE 1=1 ";
                                                    $query .= " AND karyawanid ='$idajukan' ";
                                                    $query .= " ORDER BY nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pkaryid=$z['karyawanId'];
                                                        $pkarynm=$z['nama'];
                                                        $pkryid=(INT)$pkaryid;
                                                        if ($z['karyawanId']==$idajukan)
                                                            echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                        else
                                                            echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Total Biaya Rp.
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_jml' name='e_jml' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlah; ?>' Readonly>
                                    </div>
                                </div>
                            
                            
                            </div>
                            
                        </div>
                    </div>
                    

                </div>
            </div>
            

            <div class='col-md-12 col-sm-12 col-xs-12'>
                <h2>
                    <?PHP
                        echo "<div class='col-sm-5'>";
                        include "module/mod_br_kaskecilcabotc/ttd_kkcabotc_edit.php";
                        echo "</div>";

                    ?>
                </h2>
                <div class='clearfix'></div>
            </div>
            
            
        </form>
        
    </div>
    <!--end row-->
</div>


    
    
    
<script>
            
</script>

<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>