<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<?PHP
include "config/koneksimysqli_it.php";

$jabatan_="";
$fildiv="";
$tampilbawahan = "N";
$filkaryawncabang = "";
$hanyasatukaryawan = "";
if ($_SESSION['ADMINKHUSUS']=="Y"){
    $fildiv = $_SESSION['KHUSUSSEL'];
}else{
    if (!empty($_SESSION['DIVISI']) AND $_SESSION['DIVISI'] <> "HO") {
        $fildiv = "('".$_SESSION['DIVISI']."')";
    }
}
if (!empty($_SESSION['AKSES_JABATAN'])) {
    $jabatan_ = $_SESSION['AKSES_JABATAN'];
}

if (!empty($_SESSION['AKSES_CABANG'])) {
    $filkaryawncabang = $_SESSION['AKSES_CABANG'];
}

$idklaim="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d F Y', strtotime($hari_ini));
$tgl1 = date('01/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('F Y', strtotime($hari_ini));
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$pjabatanid = getfieldit("select jabatanId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$idajukan'");
if (empty($pjabatanid))
    $pjabatanid = getfieldit("select jabatanId as lcfields from hrd.karyawan where karyawanId='$idajukan'");
$rank = getfieldit("select rank as lcfields from hrd.jabatan where jabatanId='$pjabatanid'");
$divisi="";
$patasanid="";
$idarea="";
$idcabang="";
$nmarea="";
$keterangan="";
$ca="";
$kdperiode="";
$selper0="";
$selper1="";
$selper2="";
$gambar="";
$patasan1="";
$patasan2="";
$patasan3="";
$patasan4="";
$jenisca="";
$lvlpengajuan=trim($_SESSION['LVLPOSISI']);
$totalsemua=0;
$sudahapv = "";
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    $hanyasatukaryawan = "";
    
    $edit = mysqli_query($cnit, "SELECT * FROM dbmaster.v_ca0 WHERE idca='$_GET[id]'");
    $r    = mysqli_fetch_array($edit);
    $idklaim=$r['idca'];
    $tglberlku = date('F Y', strtotime($r['periode']));
    $tgl1 = date('d/m/Y', strtotime($r['periode']));
    $tgl2 = date('d/m/Y', strtotime($r['periode']));
    $idajukan=$r['karyawanid']; 
    $nmajukan=$r['nama']; 
    $divisi=$r['divisi']; 
    $patasanid=$r['atasan1']; 
    $idarea=$r['areaid'];
    $nmarea=$r['nama_area'];
    $idcabang=$r['icabangid'];
    $keterangan=$r['keterangan'];
    $pjabatanid = $r['jabatanid'];
    $rank = getfieldit("select rank as lcfields from hrd.jabatan where jabatanId='$pjabatanid'");
    $lvlpengajuan = getfieldit("select LEVELPOSISI as lcfields from dbmaster.v_level_jabatan where jabatanId='$pjabatanid'");
    $lvlpengajuan = trim($lvlpengajuan);
    if ($lvlpengajuan=="0") $lvlpengajuan="";
    if (empty($lvlpengajuan)) $lvlpengajuan = $_SESSION['LVLPOSISI'];
    //if ($r['lunas']=="Y") $ca="checked";
    //if ($r['lampiran']=="Y") $ca="checked";
    $totalsemua=$r['jumlah'];
    
    
    $patasan1=$r['atasan1'];
    $patasan2=$r['atasan2'];
    $patasan3=$r['atasan3'];
    $patasan4=$r['atasan4'];
    
    
    $t_ats1 = $r["tgl_atasan1"];
    $t_ats2 = $r["tgl_atasan2"];
    $t_ats3 = $r["tgl_atasan3"];
    $t_ats4 = $r["tgl_atasan4"];
    $sreject = $r["stsnonaktif"];
    $jenisca = $r["jenis_ca"];
    
    
    if ($lvlpengajuan=="FF1") {
		
            $cariapvff1 = getfieldit("select karyawanid as lcfields from dbmaster.t_karyawan_apv where karyawanid='$patasan1' and status='SPV'");
            if ($cariapvff1<>$patasan1) $cariapvff1="";
            if (!empty($cariapvff1)) {
                if (!empty($t_ats2) OR !empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
            }else{
                if (!empty($t_ats1) OR !empty($t_ats2) OR !empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
            }
		
        //if (!empty($t_ats1) OR !empty($t_ats2) OR !empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
    }elseif ($lvlpengajuan=="FF2") {
		
            $cariapvff2 = getfield("select karyawanid as lcfields from dbmaster.t_karyawan_apv where karyawanid='$patasan2' and status='DM'");
            if ($cariapvff2<>$patasan2) $cariapvff2="";
            if (!empty($cariapvff2)) {
                if (!empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
            }else{
                if (!empty($t_ats2) OR !empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
            }
			
        //if (!empty($t_ats2) OR !empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
    }elseif ($lvlpengajuan=="FF3") {
        if (!empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
    }elseif ($lvlpengajuan=="FF4") {
        if (!empty($t_ats4)) $sudahapv="sudah";
    }
    
    if ($sreject=='Y') $sudahapv="reject";
    
}
$hidenff1="class='form-group'";
$hidenff2="class='form-group'";
$hidenff3="class='form-group'";

$tutuparea="class='form-group'";
if ($_SESSION['GROUP']==4 OR $_SESSION['GROUP']==5 OR $_SESSION['GROUP']==6 OR $_SESSION['GROUP']==11 OR $_SESSION['GROUP']==33) {
    $tutuparea="hidden";
}

$tutupadmin="class='form-group'";
if ($_SESSION['GROUP']==33) {
    $tutupadmin="hidden";
}
?>

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
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                            if (empty($sudahapv)) {
                            ?>
                            <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                            <!--<input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                            <small>tambah data</small>-->
                            <?PHP
                            }elseif ($sudahapv=="reject") {
                                echo "data sudah hapus";
                            }else{
                                echo "tidak bisa diedit, sudah approve";
                            }
                            ?>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idklaim; ?>' Readonly>
                                        <input type='hidden' id='e_jabatan' name='e_jabatan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pjabatanid; ?>' Readonly>
                                        <input type='hidden' id='e_rank' name='e_rank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $rank; ?>' Readonly>
                                        <input type='hidden' id='e_lvl' name='e_lvl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $lvlpengajuan; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_idkaryawan' name='e_idkaryawan' onchange="showDataKaryawan('tambahbaru', 'e_idkaryawan')">
                                            <?PHP
                                            //comboKaryawanAktifAll("", "pilihan", $idajukan, $_SESSION['STSADMIN'], $_SESSION['LVLPOSISI'], $fildiv, $_SESSION['IDCARD'], $jabatan_);
                                            //PilihKaryawanAktif($konek, $adapilihan, $ygdipilih, $hanyaygaktif, $stsadim, $divisi, $lvlposisi, $tampilkanlevelbawahan, $karyawan, $jabatan, $region, $cabang, $area, $hanyasatu)
                                            PilihKaryawanAktif("", "-- Pilihan --", $idajukan, "Y", $_SESSION['STSADMIN'], $fildiv, $_SESSION['LVLPOSISI'], $tampilbawahan, $_SESSION['IDCARD'], $jabatan_, $_SESSION['AKSES_REGION'], $filkaryawncabang, "", $hanyasatukaryawan);
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Status CA Untuk <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_jenisca' name='e_jenisca'>
                                            <?PHP
                                            if ($jenisca=="br") {
                                                echo "<option value='lk'>Biaya Luar Kota</option>";
                                                echo "<option value='br' selected>Biaya Rutin</option>";
                                            }else{
                                                echo "<option value='lk' selected>Biaya Luar Kota</option>";
                                                echo "<option value='br'>Biaya Rutin</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idarea'>Area <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_idarea' name='e_idarea'>
                                        <?PHP
                                        if ($_GET['act']=="editdata"){
                                            PilihAreaCabangAll("", "", $idcabang.$idarea, "Y", $idajukan, $lvlpengajuan, $divisi, $idcabang, $idarea);
                                            /*
                                            $karyawan=$idajukan;
                                            $query="SELECT DISTINCT areaid areaId, nama_area FROM dbmaster.v_area_cabang_all_divisi WHERE nama_area <> '' AND karyawanId='$karyawan' ";
                                            $query .=" order by nama_area, areaId";
                                            $ketemu=  mysqli_num_rows(mysqli_query($cnit, $query));
                                            if ($ketemu==0) {
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                            }else{
                                                if ($ketemu>=2) echo "<option value='' selected>-- Pilihan --</option>";
                                                $tampil = mysqli_query($cnit, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    if ($a['areaId']==$idarea)
                                                        echo "<option value='$a[areaId]' selected>$a[nama_area]</option>";
                                                    else
                                                        echo "<option value='$a[areaId]'>$a[nama_area]</option>";
                                                }
                                            }
                                             * 
                                             */
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>

                                
                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="">
                                        <?PHP
                                        if ($_GET['act']=="editdata"){
                                            $kry=$idajukan;
                                            if ($rank=="05" OR $rank==5)
                                                $sql= mysqli_query($cnit, "select distinct divisiid from MKT.imr0 where karyawanid='$kry'");
                                            elseif ($rank=="04" OR $rank==4)
                                                $sql= mysqli_query($cnit, "select distinct divisiid from MKT.ispv0 where karyawanid='$kry'");
                                            else
                                                $sql=mysqli_query($cnit, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' order by nama");

                                            $ketemu= mysqli_num_rows($sql);
                                            if ($ketemu==0) {
                                                $sql=mysqli_query($cnit, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' order by nama");
                                                $ketemu= mysqli_num_rows($sql);
                                            }

                                            $cana="";
                                            echo "<option value=''>-- Pilihan --</option>";
                                            while ($Xt=mysqli_fetch_array($sql)){
                                                if ($Xt['divisiid']=="CAN") $cana="CAN";
                                                if (($ketemu==1) OR ($Xt['divisiid']==$divisi))
                                                    echo "<option value='$Xt[divisiid]' selected>$Xt[divisiid]</option>";
                                                else
                                                    echo "<option value='$Xt[divisiid]'>$Xt[divisiid]</option>";
                                            }
                                            
                                            if ((int)$rank!=5 OR $ketemu>=2){
                                                if ($cana==""){
                                                    if ($divisi=="CAN")
                                                        echo "<option value='CAN' selected>CANARY</option>";
                                                    else
                                                        echo "<option value='CAN'>CANARY</option>";
                                                }
                                            }
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cbln01'>Periode </label>
                                    <div class='col-md-6'>
                                        <div class='input-group date' id='cbln01'>
                                            <input type="text" class="form-control" id='e_tgl' name='e_tgl' autocomplete='off' required='required' placeholder='F Y' value='<?PHP echo $tglberlku; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <textarea class='form-control' id='e_ket' name='e_ket' rows='3' placeholder='Aktivitas'><?PHP echo $keterangan; ?></textarea>
                                    </div><!--disabled='disabled'-->
                                </div>


                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Upload Dokumen <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class="checkbox">
                                            <input type='file' name='image' id='image' accept='image/jpeg,image/JPG,,image/JPEG;capture=camera'/>
                                        </div>
                                        <?PHP
                                        if (!empty($gambar)) {
                                            echo '<br/><img class="imgzoomx" src="data:image/jpeg;base64,'.base64_encode( $gambar ).'" height="100" class="img-thumnail"/>';
                                            echo "<p/><input type='checkbox' name='del_img' id='del_img' value='delete'> <b>Hapus Gambar</b>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type="button" class='btn btn-warning btn-xs'  name="btn_refresh" id="btn_refresh" onclick='refresh_atasan()' value="Refresh Atasan..">
                                    </div>
                                </div>
                                
                                <!-- Appove SPV / AM -->
                                <div <?PHP echo $hidenff1; ?> >
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_atasan' name='e_atasan' onchange="ShowAtasanDM('e_atasan')">
                                            <?PHP
                                            if ($_GET['act']=="editdata"){
                                                $karyawan=$idajukan;
                                                $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatanspv as a WHERE CONCAT(a.divisiid, a.icabangid) in 
                                                    (select CONCAT(b.divisiid, b.icabangid) 
                                                    from MKT.imr0 b where b.karyawanid='$karyawan')";
                                                $query .=" order by nama, karyawanId";
                                                $tampil = mysqli_query($cnit, $query);
                                                $ketemu = mysqli_num_rows($tampil);
                                                echo "<option value='' selected>blank_</option>";
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    if ($a['karyawanId']==$patasan1)
                                                        echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[karyawanId]'>$a[nama]</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- DM -->
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_atasan2' name='e_atasan2' onchange="ShowAtasanSM('e_atasan2')">
                                        <?PHP
                                        if ($_GET['act']=="editdata"){
                                            if ($lvlpengajuan=="FF2")
                                                $karyawan=$idajukan;
                                            else
                                                $karyawan=$patasan1;
                                            
                                            $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatandm as a WHERE CONCAT(a.icabangid) in 
                                                (select CONCAT(b.icabangid) 
                                                from MKT.ispv0 b where b.karyawanid='$karyawan')";
                                            $query .=" order by nama, karyawanId";
                                            $tampil = mysqli_query($cnit, $query);
                                            $ketemu = mysqli_num_rows($tampil);
                                            echo "<option value='' selected>blank_</option>";
                                            while($a=mysqli_fetch_array($tampil)){
                                                if ($a['karyawanId']==$patasan2) 
                                                    echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
                                                else
                                                    echo "<option value='$a[karyawanId]'>$a[nama]</option>";
                                            }
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- SM -->
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_atasan3' name='e_atasan3' onchange="">
                                        <?PHP
                                        if ($_GET['act']=="editdata"){
                                            if ($lvlpengajuan=="FF3")
                                                $karyawan=$idajukan;
                                            else
                                                $karyawan=$patasan2;
                                            
                                            $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatansm as a WHERE CONCAT(a.icabangid) in 
                                                (select CONCAT(b.icabangid) 
                                                from MKT.idm0 b where b.karyawanid='$karyawan')";
                                            $query .=" order by nama, karyawanId";
                                            $tampil = mysqli_query($cnit, $query);
                                            $ketemu = mysqli_num_rows($tampil);
                                            echo "<option value='' selected>blank_</option>";
                                            while($a=mysqli_fetch_array($tampil)){
                                                if ($a['karyawanId']==$patasan3) 
                                                    echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
                                                else
                                                    echo "<option value='$a[karyawanId]'>$a[nama]</option>";
                                            }
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- NSM -->
                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>NSM <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_atasan4' name='e_atasan4' onchange="">
                                            <?PHP
                                            $query = "select DISTINCT karyawanId, nama from dbmaster.v_karyawan_all where rank=2 and IFNULL(tglkeluar,'0000-00-00')='0000-00-00' ";
                                            $query .=" order by nama, karyawanId";
                                            $tampil = mysqli_query($cnit, $query);
                                            $ketemu = mysqli_num_rows($tampil);
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                if ($a['karyawanId']==$patasan4)
                                                    echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
                                                else
                                                    echo "<option value='$a[karyawanId]'>$a[nama]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total Rp. <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_totalsemua' name='e_totalsemua' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $totalsemua; ?>' readonly>
                                    </div>
                                </div>
                                
                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class="checkbox">
                                            <label><input type="checkbox" value="ca" name="cx_ca" <?PHP echo $ca; ?>> CA Sebelum Lunas </label> &nbsp;&nbsp;&nbsp;
                                        </div>
                                    </div>
                                </div>
                                
                                
                        
                            </div>
                            
                            
                            
                            <style>
                                .form-group, .input-group, .control-label {
                                    margin-bottom:2px;
                                }
                                .control-label {
                                    font-size:11px;
                                }
                                #datatable input[type=text], #tabelnobr input[type=text] {
                                    box-sizing: border-box;
                                    color:#000;
                                    font-size:11px;
                                    height: 25px;
                                }
                                select.soflow {
                                    font-size:12px;
                                    height: 30px;
                                }
                                .disabledDiv {
                                    pointer-events: none;
                                    opacity: 0.4;
                                }

                                table.datatable, table.tabelnobr {
                                    color: #000;
                                    font-family: Helvetica, Arial, sans-serif;
                                    width: 100%;
                                    border-collapse:
                                    collapse; border-spacing: 0;
                                    font-size: 11px;
                                    border: 0px solid #000;
                                }

                                table.datatable td, table.tabelnobr td {
                                    border: 1px solid #000; /* No more visible border */
                                    height: 10px;
                                    transition: all 0.1s;  /* Simple transition for hover effect */
                                }

                                table.datatable th, table.tabelnobr th {
                                    background: #DFDFDF;  /* Darken header a bit */
                                    font-weight: bold;
                                }

                                table.datatable td, table.tabelnobr td {
                                    background: #FAFAFA;
                                }

                                /* Cells in even rows (2,4,6...) are one color */
                                tr:nth-child(even) td { background: #F1F1F1; }

                                /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
                                tr:nth-child(odd) td { background: #FEFEFE; }

                                tr td:hover.biasa { background: #666; color: #FFF; }
                                tr td:hover.left { background: #ccccff; color: #000; }

                                tr td.center1, td.center2 { text-align: center; }

                                tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
                                tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
                                /* Hover cell effect! */
                                tr td {
                                    padding: -10px;
                                }

                            </style>
                            
                            
                            <?PHP if ($_SESSION['MOBILE']=="Y") { ?>
                                <br/>&nbsp;<div style="overflow-x:auto;">
                                    <?PHP
                                        include "module/mod_br_entrybrcash/inputdetailmobile.php";
                                    ?>
                                </div>
                            <?PHP }else{
                                include "module/mod_br_entrybrcash/inputdetail.php";
                            }
                            ?>
        
                            
                        </div>
                    </div>
                    
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                            if (empty($sudahapv)) {
                                if ($_GET['act']=="editdata") {
                                    ?><button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button><?PHP
                                }else{
                                echo "<div class='col-sm-5'>";
                                include "module/mod_br_entrybrcash/ttd_ca.php";
                                echo "</div>";
                                }
                            ?>
                            <!--<button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>-->
                            <?PHP
                            }elseif ($sudahapv=="reject") {
                                echo "data sudah hapus";
                            }else{
                                echo "tidak bisa diedit, sudah approve";
                            }
                            ?>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    

                </div>
            </div>

        </form>
        
        
    </div>
    <!--end row-->
</div>


<script>
    function Kosongkan() {
        $("#e_idarea").html("<option value=''>blank_</option>");
        $("#cb_divisi").html("<option value=''>blank_</option>");
        $("#e_atasan").html("<option value=''>blank_</option");
        $("#e_atasan2").html("<option value=''>blank_</option");
        $("#e_atasan3").html("<option value=''>blank_</option");
        document.getElementById('e_jabatan').value="";
        document.getElementById('e_rank').value="";
        document.getElementById('e_lvl').value="";
    }
    
    function showDataKaryawan(act, karyawan) {
        if (act =='input') {
            var icar = karyawan;
        }else{
            var icar = document.getElementById(karyawan).value;
        }
        if (icar != "") {
            $.ajax({
                type:"post",
                url:"module/mod_br_entrybrcash/viewdata.php?module=caridivisi",
                data:"umr="+icar,
                success:function(data){
                    var arr_date = data.split(",");
                    document.getElementById('e_jabatan').value=arr_date[1];
                    document.getElementById('e_rank').value=arr_date[2];
                    document.getElementById('e_lvl').value=arr_date[3];
                    showDataByAll(arr_date[0], arr_date[1], arr_date[2], arr_date[3], arr_date[4]);
                }
            });
            
        }else{
            Kosongkan();
        }
    }
    
    function showDataByAll(pkar, pjabatan, prank, plevel, pdivisi) {
        $("#e_idarea").html("<option value=''>blank_</option");
        //showAreaEmp('', 'e_idkaryawan', 'e_idarea', pdivisi, plevel);
        if (pdivisi=="OTC") {
            $("#cb_divisi").html("<option value="+pdivisi+">"+pdivisi+"</option>");
            $("#e_atasan").html("<option value=''>blank_</option");
            $("#e_atasan2").html("<option value=''>blank_</option");
            $("#e_atasan3").html("<option value=''>blank_</option");
        }else{
            $("#cb_divisi").html("<option value=''>blank_</option");
            //showDivisi(pkar, pjabatan, prank, pdivisi);
            
            $("#e_atasan").html("<option value=''>blank_</option");
            $("#e_atasan2").html("<option value=''>blank_</option");
            $("#e_atasan3").html("<option value=''>blank_</option");
            
            var elvlpos = plevel;
            if (elvlpos=="FF1") {
                ShowAtasanSPV('e_idkaryawan');
            }

            if (elvlpos=="FF2") {
                $("#e_atasan").html("<option value=''>blank_</option");
                ShowAtasanDM('e_idkaryawan');
            }

            if (elvlpos=="FF3") {
                $("#e_atasan").html("<option value=''>blank_</option");
                $("#e_atasan2").html("<option value=''>blank_</option");
                ShowAtasanSM('e_idkaryawan');
            }
            
        }
    }
    
    function showAreaEmp(selinc, ucar, earea, edivisi, elevel) {
        var icar = document.getElementById(ucar).value;
        $.ajax({
            type:"post",
            url:"config/viewdata2.php?module=viewdataareakaryawanbylevel",
            data:"uselinc="+selinc+"&umr="+icar+"&udivisi="+edivisi+"&ulevel="+elevel,
            success:function(data){
                $("#"+earea).html(data);
            }
        });
    }
    
    function showDivisi(icar, ijbt, irank, idivisi) {
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdata.php?module=viewdatadivisi",
            data:"umr="+icar+"&ujbt="+ijbt+"&urank="+irank+"&udivisi="+idivisi,
            success:function(data){
                $("#cb_divisi").html(data);
            }
        });
    }
    
    function refresh_atasan() {
        var elvlpos =document.getElementById('e_lvl').value;
        elvlpos=elvlpos.trim();
        var rlevel = elvlpos.substring(0, 2);

        $("#e_atasan").html("<option value=''>blank_</option");
        $("#e_atasan2").html("<option value=''>blank_</option");
        $("#e_atasan3").html("<option value=''>blank_</option");
        
        if (rlevel=="FF") {
            if (elvlpos=="FF1") {
                ShowAtasanSPV('e_idkaryawan');
            }

            if (elvlpos=="FF2") {
                $("#e_atasan").html("<option value=''>blank_</option");
                ShowAtasanDM('e_idkaryawan');
            }

            if (elvlpos=="FF3") {
                $("#e_atasan").html("<option value=''>blank_</option");
                $("#e_atasan2").html("<option value=''>blank_</option");
                ShowAtasanSM('e_idkaryawan');
            }

        }
    }
    
    function ShowAtasanSPV(idkar) {
        var icar = document.getElementById(idkar).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdata.php?module=viewdataatasanspv",
            data:"umr="+icar,
            success:function(data){
                $("#e_atasan").html(data);
                ShowAtasanDM('e_atasan');
            }
        });
    }
    
    function ShowAtasanDM(idkar) {
        var icar = document.getElementById(idkar).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdata.php?module=viewdataatasandm",
            data:"umr="+icar,
            success:function(data){
                $("#e_atasan2").html(data);
                ShowAtasanSM('e_atasan2');
            }
        });
    }
    
    function ShowAtasanSM(idkar) {
        var icar = document.getElementById(idkar).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdata.php?module=viewdataatasansm",
            data:"umr="+icar,
            success:function(data){
                $("#e_atasan3").html(data);
            }
        });
    }
    
    
    function disp_confirm(pText_, ket)  {

        var ekar =document.getElementById('e_idkaryawan').value;
        var ediv =document.getElementById('cb_divisi').value;
        var elvlpos =document.getElementById('e_lvl').value;
        var eatasan1 =document.getElementById('e_atasan').value;
        var eatasan2 =document.getElementById('e_atasan2').value;
        var eatasan3 =document.getElementById('e_atasan3').value;
        var etotsem =document.getElementById('e_totalsemua').value;
        if (etotsem === "") etotsem=0;
        
        
        elvlpos=elvlpos.trim();

        var rlevel = elvlpos.substring(0, 2);

        if (ekar==""){
            alert("yang membuat masih kosong....");
            return 0;
        }

        if (ediv==""){
            alert("divisi masih kosong....");
            return 0;
        }
        
        if (ediv != "OTC"){
            
            if (rlevel=="FF") {
                if (elvlpos=="FF1") {
                    if (eatasan1==""){
                        alert("SPV / AM masih kosong....");
                        return 0;
                    }
                }

                if (elvlpos=="FF1" || elvlpos=="FF2") {
                    if (eatasan2==""){
                        alert("DM masih kosong....");
                        return 0;
                    }
                }

                if (elvlpos=="FF1" || elvlpos=="FF2" || elvlpos=="FF3") {
                    if (eatasan3==""){
                        alert("SM masih kosong....");
                        return 0;
                    }
                }

            }
        }
        
        if (parseInt(etotsem)==0) {
            alert("Total Rupiah Masih Kosong....");
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

                document.getElementById("demo-form2").action = "module/mod_br_entrybrcash/aksi_entrybrcash.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>

<script>
    $(document).ready(function() {
        <?PHP
            if ($_GET['act']=="tambahbaru"){
        ?>
                showDataKaryawan('input', '<?PHP echo $idajukan; ?>');
        <?PHP
            }
        ?>
            
        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
    });
    
    
    function hit_total(pNilai_,pQty_,pTotal_) {
        
        nilai = document.getElementById(pNilai_).value;  
        qty = document.getElementById(pQty_).value;

        var newchar = '';
        var mynilai = nilai;  
        mynilai = mynilai.split(',').join(newchar);
        var myqty = qty;  
        myqty = myqty.split(',').join(newchar);
        
        total_ = mynilai * myqty;
        document.getElementById(pTotal_).value = total_;
        findTotal()
        
    }
    
    function findTotal(){
        var newchar = '';
        var a1 = document.getElementById('e_total1').value;
        var a2 = document.getElementById('e_total2').value;
        var a3 = document.getElementById('e_total3').value;
        var a4 = document.getElementById('e_total4').value;
        var a5 = document.getElementById('e_total5').value;
        var a6 = document.getElementById('e_total6').value;
        var a7 = document.getElementById('e_total7').value;
        var a8 = document.getElementById('e_total8').value;
        
        a1 = a1.split(',').join(newchar);
        a2 = a2.split(',').join(newchar);
        a3 = a3.split(',').join(newchar);
        a4 = a4.split(',').join(newchar);
        a5 = a5.split(',').join(newchar);
        a6 = a6.split(',').join(newchar);
        a7 = a7.split(',').join(newchar);
        a8 = a8.split(',').join(newchar);
        if (a1 === "") a1=0; if (a2 === "") a2=0; if (a3 === "") a3=0; if (a4 === "") a4=0;
        if (a5 === "") a5=0; if (a6 === "") a6=0; if (a7 === "") a7=0; if (a8 === "") a8=0;
        
        tot =parseInt(a1)+parseInt(a2)+parseInt(a3)+parseInt(a4)+parseInt(a5)+parseInt(a6)
            +parseInt(a7)+parseInt(a8);
        document.getElementById('e_totalsemua').value = tot;
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatableuc th {
        font-size: 12px;
    }
    #datatableuc td { 
        font-size: 12px;
        padding: 3px;
        margin: 1px;
    }
</style>