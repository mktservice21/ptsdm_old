<?PHP

$iservername = "203.142.71.82";
$iservername = "192.168.88.25";
$iusername = "root";
$ipassword = "sdmmysqlserver2017";
//$icnit = mysqli_connect($iservername, $iusername, $ipassword) or die("Connection failed: " . mysqli_connect_error());

//include "config/koneksimysqli_it.php";
//$icnit=$cnit;

include "config/koneksimysqli.php";

$icnit=$cnmy;

$pilogin=false;
if (isset($_GET['nlog'])) {
    if ($_GET['nlog']=="ilog") $pilogin=true;
}

$nidkaryawan="";
$nnmkaryawan="";
$nnmarea="";
$nidatasan="";
$nnmatasan="";

$pdivisiid="";
$jabatanid="";

$ptglkeluar="";
$nonaktif="N";
$hanyadmin="";

$act="updateaktifnon";
$idnya=$_GET['id'];
//$idnya=$_SESSION['IDCARD'];

$query = "select a.tglkeluar, a.AKTIF aktif, a.karyawanId, a.nama, a.jabatanId, a.iCabangId, c.nama nmcabang, a.areaId, d.nama nmarea, a.divisiId, a.divisiId2, 
    b.divisi1, b.divisi2, b.divisi3, b.atasanId, i.nama nmatasan, b.spv, e.nama nmspv, b.dm, f.nama nmdm, b.sm, g.nama nmsm, b.gsm, h.nama nmgsm 
    from hrd.karyawan a LEFT JOIN dbmaster.t_karyawan_posisi b on 
    a.karyawanId=b.karyawanId 
    LEFT JOIN MKT.icabang c on a.iCabangId=c.iCabangId
    LEFT JOIN MKT.iarea d on a.areaId=d.areaId and a.iCabangId=d.iCabangId 
    LEFT JOIN hrd.karyawan e on b.spv=e.karyawanId 
    LEFT JOIN hrd.karyawan f on b.dm=f.karyawanId 
    LEFT JOIN hrd.karyawan g on b.sm=g.karyawanId 
    LEFT JOIN hrd.karyawan h on b.gsm=h.karyawanId 
    LEFT JOIN hrd.karyawan i on b.atasanId=i.karyawanId 
    WHERE a.karyawanid='$idnya'";

$tampil= mysqli_query($icnit, $query);
$ketemu = mysqli_num_rows($tampil);
if ($ketemu>0) {
    $row= mysqli_fetch_array($tampil);

    $nidkaryawan=$row['karyawanId'];
    $nnmkaryawan=$row['nama'];

    $nidatasan=$row['atasanId'];
    $nnmatasan=$row['nmatasan'];
    
    
    $pdivisiid=$row['divisiId'];
    $jabatanid=$row['jabatanId'];
    
    $pntglkel=$row['tglkeluar'];
    if ($pntglkel=="0000-00-00") $pntglkel="";
    if (!empty($pntglkel)) $ptglkeluar = date('d/m/Y', strtotime($pntglkel));
    else $ptglkeluar="";
    
    $nonaktif="N";
    if ($row['aktif']=="Y") $nonaktif="";
    
    $hanyadmin = trim(getfield("select karyawanId as lcfields from dbmaster.t_karyawanadmin where karyawanId='$idnya'"));
    
}

$khusushidden="hidden";
if ($_SESSION['GROUP']=="1") {$khusushidden=""; }

?>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
        
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <div class='x_panel'>
                
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>KARYAWAN ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idnya; ?>' Readonly>
                                        <input type='hidden' id='e_divisiid' name='e_divisiid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivisiid; ?>' Readonly>
                                        <input type='hidden' id='e_jabatanid' name='e_jabatanid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $jabatanid; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nama'>NAMA <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_nama' name='e_nama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nnmkaryawan; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_jabatan'>JABATAN <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_jabatan' name='e_jabatan' onchange="showDataKaryawan('tambahbaru', 'e_idkaryawan')" disabled="">
                                            <?PHP
                                            $sql=mysqli_query($icnit, "SELECT jabatanId, nama FROM hrd.jabatan order by jabatanId");
                                            $ketemu= mysqli_num_rows($sql);
                                            echo "<option value=''>-- Pilihan --</option>";
                                            while ($Xt=mysqli_fetch_array($sql)){
                                                if ($Xt['jabatanId']==$jabatanid)
                                                    echo "<option value='$Xt[jabatanId]' selected>$Xt[jabatanId] - $Xt[nama]</option>";
                                                else
                                                    echo "<option value='$Xt[jabatanId]'>$Xt[jabatanId] - $Xt[nama]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>TGL KELUAR <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <div class='input-group date' id='mytgl03'>
                                            <input type="text" class="form-control" id='e_tglkeluar' name='e_tglkeluar' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglkeluar; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idcab'>NON AKTIF <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <?PHP
                                        if (!empty($nonaktif))
                                            echo "<input type='checkbox' name='chk_nonaktif' id='chk_nonaktif' checked>";
                                        else
                                            echo "<input type='checkbox' name='chk_nonaktif' id='chk_nonaktif' >";
                                        ?>
                                    </div>
                                </div>
                                
                                
                                <div <?PHP echo $khusushidden; ?>>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idcab'>KARYAWAN BAYANGAN <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <?PHP
                                        
                                        if (!empty($hanyadmin))
                                            echo "<input type='checkbox' name='chk_admin' id='chk_admin' checked>";
                                        else
                                            echo "<input type='checkbox' name='chk_admin' id='chk_admin' >";
                                        ?>
                                    </div>
                                </div>
                                
                                
                                
                                <br/>&nbsp;
                                <!-- Save -->
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                        <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
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


<script type="text/javascript">
    $(function() {
        $('#mytgl03, #mytgl04').datetimepicker({
            ignoreReadonly: true,
            allowInputToggle: true,
            format: 'DD/MM/YYYY'
        });
    });
</script>

<script>
    
    function disp_confirm(pText_, ket)  {
        var eid =document.getElementById('e_id').value;
        var etglkeluar =document.getElementById('e_tglkeluar').value;
        
        
        if (eid=="") {
            alert("ID kosong");
            return false;
        }
        
        
        if (etglkeluar=="") {
        }else{
            //alert("Tanggal Keluar diisi, maka karyawan akan menjadi NON AKTIF...");
            pText_ = "Tanggal Keluar diisi, maka karyawan akan menjadi NON AKTIF...\n\
Apakah akan melanjutkan Simpan...?";
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

                document.getElementById("demo-form2").action = "module/mst_isidatakaryawan/aksi_isidatakaryawan.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
mysqli_close($icnit);
?>