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

$nidkaryawan="";
$nnmkaryawan="";
$nnmarea="";

$pdivisiid="";
$piddivisi="";
$piddivisi2="";
$jabatanid="";
$pidcabang="";
$pidarea="";

$act="updatejabatandivisarea";
$idnya=$_GET['id'];
//$idnya=$_SESSION['IDCARD'];
$pstsact=$_GET['act'];


$query = "select a.karyawanId, a.nama, a.jabatanId, a.iCabangId, c.nama nmcabang, a.areaId, d.nama nmarea, a.divisiId, a.divisiId2, 
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

    
    $pdivisiid=$row['divisiId'];
    $jabatanid=$row['jabatanId'];
    
    $piddivisi=$row['divisiId'];
    $piddivisi2=$row['divisiId2'];
    $pidcabang=$row['iCabangId'];
    $pidarea=$row['areaId'];
    
}



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
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nama'>NAMA <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_nama' name='e_nama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nnmkaryawan; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jabatan'>JABATAN <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_jabatan' name='cb_jabatan' onchange="ShowDataCabangDariJBT()">
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DIVISI 1 <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='soflow' name='cb_divisi' id='cb_divisi' onchange="ShowDataUntukDivisi1()">
                                            <?php
                                            if ($pstsact=="editdivisijabatan") {
                                                echo "<option value='' selected>--Pilih--</option>";
                                            }
                                            $query = "select divprodid, nama from MKT.divprod where DivProdid In ('EAGLE', 'PEACO', 'PIGEO', 'OTC', 'HO') order by 1";
                                            $tampiledu= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampiledu)) {
                                                $nniddiv=$du['divprodid'];
                                                $nnmdiv=$du['nama'];

                                                if ($nniddiv==$piddivisi) 
                                                    echo "<option value='$nniddiv' selected>$nnmdiv</option>";
                                                else
                                                    echo "<option value='$nniddiv'>$nnmdiv</option>";

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DIVISI 2 <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='soflow' name='cb_divisi2' id='cb_divisi2' onchange="">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            if ($pstsact=="editdivisijabatan" AND !empty($piddivisi)) {

                                                $query = "select divprodid, nama from MKT.divprod where DivProdid In ('EAGLE', 'PEACO', 'PIGEO', 'OTC', 'HO') order by 1";
                                                $tampiledu= mysqli_query($cnmy, $query);
                                                while ($du= mysqli_fetch_array($tampiledu)) {
                                                    $nniddiv=$du['divprodid'];
                                                    $nnmdiv=$du['nama'];

                                                    if ($nniddiv==$piddivisi2) 
                                                        echo "<option value='$nniddiv' selected>$nnmdiv</option>";
                                                    else
                                                        echo "<option value='$nniddiv'>$nnmdiv</option>";

                                                }

                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='soflow' name='cb_cabang' id='cb_cabang' onchange="ShowDataArea()">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            if ($piddivisi=="OTC"){
                                                if ($jabatanid=="06" OR $jabatanid=="07" OR $jabatanid=="09" OR $jabatanid=="11" OR $jabatanid=="12" OR $jabatanid=="13" OR $jabatanid=="14" OR $jabatanid=="16" OR $jabatanid=="17" OR $jabatanid=="37") {
                                                    $query = "select icabangid, nama from MKT.icabang where aktif='Y' order by nama";
                                                }else{
                                                    $query = "select icabangid_o icabangid, nama from MKT.icabang_o where aktif='Y' order by nama";
                                                }
                                            }else{
                                                $query = "select icabangid, nama from MKT.icabang where aktif='Y' order by nama";
                                            }
                                            $tampiledu= mysqli_query($cnmy, $query);
                                            while ($du= mysqli_fetch_array($tampiledu)) {
                                                $nnidcab=$du['icabangid'];
                                                $nnmcab=$du['nama'];

                                                if ($nnidcab==$pidcabang) 
                                                    echo "<option value='$nnidcab' selected>$nnmcab</option>";
                                                else
                                                    echo "<option value='$nnidcab'>$nnmcab</option>";

                                            }
                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='soflow' name='cb_area' id='cb_area' onchange="">
                                            <?php
                                            echo "<option value='' selected>--Pilih--</option>";
                                            if ($pstsact=="editdivisijabatan") {
                                                
                                                if ($piddivisi=="OTC"){
                                                    if ($jabatanid=="06" OR $jabatanid=="07" OR $jabatanid=="09" OR $jabatanid=="11" OR $jabatanid=="12" OR $jabatanid=="13" OR $jabatanid=="14" OR $jabatanid=="16" OR $jabatanid=="17" OR $jabatanid=="37") {
                                                        $query = "select areaid, nama from MKT.iarea where aktif='Y' AND icabangid='$pidcabang' order by nama";
                                                    }else{
                                                        $query = "select areaid_o areaid, nama from MKT.iarea_o where aktif='Y' AND icabangid_o='$pidcabang' order by nama";
                                                    }
                                                }else{
                                                    $query = "select areaid, nama from MKT.iarea where aktif='Y' AND icabangid='$pidcabang' order by nama";
                                                }
                                                $tampiledu= mysqli_query($cnmy, $query);
                                                while ($du= mysqli_fetch_array($tampiledu)) {
                                                    $nnidarea=$du['areaid'];
                                                    $nnmarea=$du['nama'];

                                                    if ($nnidarea==$pidarea) 
                                                        echo "<option value='$nnidarea' selected>$nnmarea</option>";
                                                    else
                                                        echo "<option value='$nnidarea'>$nnmarea</option>";

                                                }
                                            }
                                            ?>
                                        </select>
                                        
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


<script>
    
    function ShowDataUntukDivisi1() {
        ShowDataCabang();
        ShowDataDivisi2();
    }
    
    function ShowDataCabangDariJBT() {
        ShowDataCabang();
        ShowDataArea();
    }
    
    function ShowDataCabang() {
       var edivid =document.getElementById('cb_divisi').value;
       var ecabangid =document.getElementById('cb_cabang').value;
       var ejbtid =document.getElementById('cb_jabatan').value;
       
       $.ajax({
           type:"post",
           url:"module/mst_isidatakaryawan/viewdata.php?module=viewdatacabang",
           data:"ucabangid="+ecabangid+"&udivid="+edivid+"&ujbtid="+ejbtid,
           success:function(data){
               $("#cb_cabang").html(data);
               ShowDataArea();
               //$("#cb_area").html("<option value=''>--Pilihan--</option>");
           }
       });
    }
    
    function ShowDataArea() {
       var ecabangid =document.getElementById('cb_cabang').value;
       var edivid =document.getElementById('cb_divisi').value;
       var eareaids =document.getElementById('cb_area').value;
       var ejbtid =document.getElementById('cb_jabatan').value;
       $.ajax({
           type:"post",
           url:"module/mst_isidatakaryawan/viewdata.php?module=viewdataarea",
           data:"ucabangid="+ecabangid+"&udivid="+edivid+"&uareaids="+eareaids+"&ujbtid="+ejbtid,
           success:function(data){
               $("#cb_area").html(data);
           }
       });
    }
    
    
    function disp_confirm(pText_, ket)  {
        var ejabatan =document.getElementById('cb_jabatan').value;
        var edivid =document.getElementById('cb_divisi').value;
        var ecabang =document.getElementById('cb_cabang').value;

        if (ejabatan=="") {
            alert("jabatan masih kosong");
            return false;
        }

        if (edivid=="") {
            alert("divisi masih kosong");
            return false;
        }

        if (ecabang=="") {
            alert("cabang masih kosong");
            return false;
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