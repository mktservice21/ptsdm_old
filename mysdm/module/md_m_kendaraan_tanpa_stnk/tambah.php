<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<?PHP
include "config/koneksimysqli.php";
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tglawal = date('d/m/Y', strtotime($hari_ini));
$tglakhir = date('d/m/Y', strtotime($hari_ini));

$platnomor="";
$jenis="02";
$merk="";
$tipe="";

$idpakai="";
$idajukan=$_SESSION['IDCARD'];

$stskendaraan="";
$pwarna="";

$chktgl ="";
$tglakhhidden="hidden";

$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    $platnomor=$_GET['id'];
    $query = "select * from dbmaster.t_kendaraan where nopol='$platnomor'";
    $tampil= mysqli_query($cnmy, $query);
    $r= mysqli_fetch_array($tampil);
    $jenis=$r['jenis'];
    $merk=$r['merk'];
    $tipe=$r['tipe'];
    $tgl1 = date('d/m/Y', strtotime($r['tglbeli']));
    
    $stskendaraan=$r['statuskendaraan'];
    
    $pwarna=$r['warna'];
    
    $pemakai= mysqli_query($cnmy, "select * from dbmaster.t_kendaraan_pemakai where nopol='$platnomor' and stsnonaktif<>'Y'");
    $p= mysqli_fetch_array($pemakai);
    $idpakai=$p['nourut'];
    $idajukan=$p['karyawanid'];
    
    
    
    
    
    if (!empty($p['tglawal']) AND $p['tglawal'] <> "0000-00-00")
        $tglawal = date('d/m/Y', strtotime($p['tglawal']));
    
    if (!empty($p['tglakhir']) AND $p['tglakhir'] <> "0000-00-00") {
        $tglakhir = date('d/m/Y', strtotime($p['tglakhir']));
        $chktgl="checked";
        $tglakhhidden="";
    }
    
    
}
?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' 
              enctype='multipart/form-data'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <div class='x_panel'>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <!-- ISI INPUT -->
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PLAT NOMOR <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='hidden' id='e_idlama' name='e_idlama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $platnomor; ?>'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $platnomor; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>JENIS <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <select class='form-control input-sm' id='e_jenis' name='e_jenis'>
                                        <?PHP
                                            $query="SELECT DISTINCT jenis, nama_jenis FROM dbmaster.t_kendaraan_jenis";
                                            $query .=" order by nama_jenis, jenis";
                                            $ketemu=  mysqli_num_rows(mysqli_query($cnmy, $query));
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while($a=mysqli_fetch_array($tampil)){
                                                if ($a['jenis']==$jenis)
                                                    echo "<option value='$a[jenis]' selected>$a[nama_jenis]</option>";
                                                else
                                                    echo "<option value='$a[jenis]'>$a[nama_jenis]</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>MERK <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_merk' name='e_merk' class='form-control col-md-7 col-xs-12' value='<?PHP echo $merk; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>TIPE <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_tipe' name='e_tipe' class='form-control col-md-7 col-xs-12' value='<?PHP echo $tipe; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>WARNA <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_warna' name='e_warna' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pwarna; ?>'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>TGL. BELI <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='e_tgl' name='e_tgl' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgl1; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>STATUS KENDARAAN <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <select class='form-control input-sm' id='e_ststkendaraan' name='e_ststkendaraan'>
                                        <?PHP
                                            $sselect1="";
                                            $sselect2="";
                                            $sselect3="";
                                            if (empty($stskendaraan) OR $stskendaraan=="AKTIF") $sselect1="selected";
                                            if ($stskendaraan=="JUAL") $sselect2="selected";
                                            if ($stskendaraan=="TIDAKTERPAKAI") $sselect3="selected";
                                            
                                            echo "<option value='AKTIF' $sselect1>Aktif</option>";
                                            echo "<option value='JUAL' $sselect2>Di Jual</option>";
                                            echo "<option value='TIDAKTERPAKAI' $sselect3>Tidak Terpakai</option>";
                                            
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <br/>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>PEMAKAI SEKARANG : <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='hidden' id='e_idpakai' name='e_idpakai' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idpakai; ?>'>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>KARYAWAN <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_pemakai' name='e_pemakai' onchange="showAreaEmp('', 'e_idkaryawan', 'e_idarea')">
                                            <?PHP
                                            PilihKaryawanAktif("", "-- Pilihan --", $idajukan, "Y", $_SESSION['STSADMIN'], "", $_SESSION['LVLPOSISI'], "", $_SESSION['IDCARD'], "", $_SESSION['AKSES_REGION'], "", "", "");
                                            //comboKaryawanAktifAll("", "pilihan", $idajukan, $_SESSION['STSADMIN'], $_SESSION['LVLPOSISI'], $_SESSION['DIVISI'], $_SESSION['IDCARD'], $jabatan_);
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>TANGGAL <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <div class='input-group date' id='mytgl02'>
                                            <input type="text" class="form-control" id='e_tglawal' name='e_tglawal' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tglawal; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>TANGGAL AKHIR 
                                        <span class='required'><input type="checkbox" id="chktgl" name="chktgl" <?PHP echo $chktgl; ?> onclick="myShowHide()"></span></label>
                                    <div class='col-md-4'
                                         <div id="divtglakhir">
                                            <div class='input-group date' id='mytgl02'>
                                                <input type="text" class="form-control" id='e_tglakhir' name='e_tglakhir' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tglakhir; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <br/>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                        <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                    </div>
                                </div>
                                
                                
                                
                                <!-- END ISI INPUT -->
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            
            
        </form>
    </div>
    
</div>

<script>
function disp_confirm(pText_, ket)  {
    var eid =document.getElementById('e_id').value;
    
    if (eid==""){
        alert("PLAT NOMOR TIDAK BOLEH KOSONG....");
        document.getElementById('e_id').focus();
        return 0;
    }
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");
            
            document.getElementById("demo-form2").action = "module/md_m_kendaraan/aksi_kendaraan.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}

function myShowHide() {
    var xchec=$("#chktgl").is(":checked");
    var x = document.getElementById("divtglakhir");
    if (xchec==false) {
        x.style.display = "none";
    }else{
        x.style.display = "block";
    }
    

}

$(document).ready(function() {
    var xchec=$("#chktgl").is(":checked");
    if (xchec==false) {
        var x = document.getElementById("divtglakhir");
        x.style.display = "none";
    }
} );
</script>
