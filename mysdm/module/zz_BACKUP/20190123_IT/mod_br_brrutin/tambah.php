<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<?PHP
include "config/koneksimysqli_it.php";
$nmperiode = "xxx";
if ($_SESSION['GROUP']==1 OR $_SESSION['GROUP']==23 or $_SESSION['GROUP']==26) $nmperiode = "x";
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
if ($_SESSION['JABATANID']==38) $hanyasatukaryawan="Y";
if ($_SESSION['IDCARD']=="0000000825") $hanyasatukaryawan="N"; //DAYANA
if ($_SESSION['IDCARD']=="0000000178") $hanyasatukaryawan="N"; //NURHAYATI
if ($_SESSION['IDCARD']=="0000001587") $hanyasatukaryawan="N"; //MARINA

$idklaim="";

$tglhariini = getfieldit("select DATE_FORMAT(CURRENT_DATE(),'%d') as lcfields ");
if ($tglhariini=="0") $tglhariini="";
if (empty($tglhariini)) $tglhariini = date("d");
if ($_SESSION['GROUP']==1 OR $_SESSION['GROUP']==24 OR $_SESSION['GROUP']==28) $tglhariini = 0;

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
$lvlpengajuan=$_SESSION['LVLPOSISI'];
$totalsemua=0;
$saldoca=0;
$nopol="";
$penambahan = "";
$coatambah = "";
$sudahapv = "";
$idca = "";
$act="input";

if (isset($_GET['ca'])) {
    $hanyasatukaryawan = "";
    $idca = $_GET['ca'];
    $edit = mysqli_query($cnit, "SELECT * FROM dbmaster.v_ca0 WHERE idca='$idca' AND stsnonaktif <> 'Y'");
    $r    = mysqli_fetch_array($edit);
    if (!empty($r['periode']) AND $r['periode'] <> "0000-00-00") {
        $tglberlku = date('F Y', strtotime($r['periode']));
        $day = date('d', strtotime($r['periode']));
        if ($day<16) {
            $selper1="selected";
            $tgl1 = date('01/m/Y', strtotime($r['periode']));
            $tgl2 = date('15/m/Y', strtotime($r['periode']));
        }else{
            $selper2="selected";
            $tgl1 = date('16/m/Y', strtotime($r['periode']));
            $tgl2 = date('t/m/Y', strtotime($r['periode']));
        }
    }
    $idajukan=$r['karyawanid']; 
    $nmajukan=$r['nama']; 
    $divisi=$r['divisi'];
    $idarea=$r['areaid'];
    $nmarea=$r['nama_area'];
    $idcabang=$r['icabangid'];
    
    $pjabatanid = $r['jabatanid'];
    $rank = getfieldit("select rank as lcfields from hrd.jabatan where jabatanId='$pjabatanid'");
    $lvlpengajuan = getfieldit("select LEVELPOSISI as lcfields from dbmaster.v_level_jabatan where jabatanId='$pjabatanid'");
    $lvlpengajuan = trim($lvlpengajuan);
    if ($lvlpengajuan=="0") $lvlpengajuan="";
    if (empty($lvlpengajuan)) $lvlpengajuan = $_SESSION['LVLPOSISI'];
    
    $patasan1=$r['atasan1'];
    $patasan2=$r['atasan2'];
    $patasan3=$r['atasan3'];
    $patasan4=$r['atasan4'];
    
    
    $ca_jumlahrp=$r['jumlah'];
    $rutin_jumlahrp = getfieldit("select sum(jumlah) as lcfields from dbmaster.t_brrutin0 WHERE idca='$idca' AND stsnonaktif <> 'Y'");
    $sewa_jumlahrp = getfieldit("select sum(jumlah) as lcfields from dbmaster.t_sewa WHERE idca='$idca' AND stsnonaktif <> 'Y'");
    if (empty($rutin_jumlahrp)) $rutin_jumlahrp = 0;
    if (empty($sewa_jumlahrp)) $sewa_jumlahrp = 0;
    $saldoca = (double)$ca_jumlahrp-(double)$rutin_jumlahrp-(double)$sewa_jumlahrp;
    
}else{
    if ($_GET['act']=="editdata"){
        $act="update";
        $hanyasatukaryawan = "";

        $filygmengajukan="";
        if ($_SESSION['LVLPOSISI']=="FF1" or $_SESSION['LVLPOSISI']=="FF2" or $_SESSION['LVLPOSISI']=="FF3" or $_SESSION['LVLPOSISI']=="FF4")
            $filygmengajukan=" AND karyawanid='$_SESSION[IDCARD]'";

        $edit = mysqli_query($cnit, "SELECT * FROM dbmaster.v_brrutin0 WHERE kode=1 AND idrutin='$_GET[id]' $filygmengajukan");
        $r    = mysqli_fetch_array($edit);
        $idklaim=$r['idrutin'];
        $tglberlku = date('F Y', strtotime($r['bulan']));
        $tgl1 = date('d/m/Y', strtotime($r['periode1']));
        $tgl2 = date('d/m/Y', strtotime($r['periode2']));
        $idajukan=$r['karyawanid']; 
        $nmajukan=$r['nama']; 
        $divisi=$r['divisi']; 
        $patasanid=$r['atasanid']; 
        $idarea=$r['areaid']; 
        $nmarea=$r['nama_area'];
        $idcabang=$r['icabangid'];
        $keterangan=$r['keterangan'];
        $kdperiode=$r['kodeperiode'];
        if ($kdperiode==1) $selper1="selected";
        if ($kdperiode==2) $selper2="selected";
        $pjabatanid = $r['jabatanid'];
        $rank = getfieldit("select rank as lcfields from hrd.jabatan where jabatanId='$pjabatanid'");
        $lvlpengajuan = getfieldit("select LEVELPOSISI as lcfields from dbmaster.v_level_jabatan where jabatanId='$pjabatanid'");
        $lvlpengajuan = trim($lvlpengajuan);
        if ($lvlpengajuan=="0") $lvlpengajuan="";
        if (empty($lvlpengajuan)) $lvlpengajuan = $_SESSION['LVLPOSISI'];
        
        $totalsemua=$r['jumlah'];
        $nopol=$r['nopol'];

        $patasan1=$r['atasan1'];
        $patasan2=$r['atasan2'];
        $patasan3=$r['atasan3'];
        $patasan4=$r['atasan4'];


        $t_ats1 = $r["tgl_atasan1"];
        $t_ats2 = $r["tgl_atasan2"];
        $t_ats3 = $r["tgl_atasan3"];
        $t_ats4 = $r["tgl_atasan4"];
        $sreject = $r["stsnonaktif"];


        if ($lvlpengajuan=="FF1") {

            $cariapvff1 = getfieldit("select karyawanid as lcfields from dbmaster.t_karyawan_apv where karyawanid='$patasan1' and status='SPV'");
            if ($cariapvff1<>$patasan1) $cariapvff1="";
            if (!empty($cariapvff1)) {
                if (!empty($t_ats2) OR !empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
            }else{
                if (!empty($t_ats1) OR !empty($t_ats2) OR !empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
            }
			
        }elseif ($lvlpengajuan=="FF2") {
			
            $cariapvff2 = getfield("select karyawanid as lcfields from dbmaster.t_karyawan_apv where karyawanid='$patasan2' and status='DM'");
            if ($cariapvff2<>$patasan2) $cariapvff2="";
            if (!empty($cariapvff2)) {
                if (!empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
            }else{
                if (!empty($t_ats2) OR !empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
            }
			
            
        }elseif ($lvlpengajuan=="FF3") {
            if (!empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
        }elseif ($lvlpengajuan=="FF4") {
            if (!empty($t_ats4)) $sudahapv="sudah";
        }

        if ($sreject=='Y') $sudahapv="reject";

        if (!empty($r['idca'])) {
            $idca = $r['idca'];
            $editca = mysqli_query($cnit, "SELECT * FROM dbmaster.v_ca0 WHERE idca='$idca'");
            $ca    = mysqli_fetch_array($editca);
            $ca_jumlahrp=$ca['jumlah'];
            $rutin_jumlahrp = getfieldit("select sum(jumlah) as lcfields from dbmaster.t_brrutin0 WHERE idca='$idca' AND idrutin <> '$idklaim' AND stsnonaktif <> 'Y'");
            $sewa_jumlahrp = getfieldit("select sum(jumlah) as lcfields from dbmaster.t_sewa WHERE idca='$idca' AND stsnonaktif <> 'Y'");
            if (empty($rutin_jumlahrp)) $rutin_jumlahrp = 0;
            if (empty($sewa_jumlahrp)) $sewa_jumlahrp = 0;
            $saldoca = (double)$ca_jumlahrp-(double)$rutin_jumlahrp-(double)$sewa_jumlahrp;
        }
        
        if ($divisi=="OTC") {
            $edittambah = mysqli_query($cnit, "SELECT * FROM dbmaster.t_brrutin2 WHERE idrutin='$idklaim'");
            $tam    = mysqli_fetch_array($edittambah);
            $penambahan = $tam['penambahan'];
            $coatambah = $tam['coa_tambah'];
        }
    }
}
$tutuparea="class='form-group'";
$tutupadmin="class='form-group'";

if ($_SESSION['GROUP']==15 OR $_SESSION['GROUP']==33) {
    $tutuparea="hidden";
    $tutupadmin="hidden";
}

if (isset($_GET['ca'])) {
?>
    <script> window.onload = function() { document.getElementById("e_idca").focus(); } </script>
<?PHP
}else{
?>
    <script> window.onload = function() { document.getElementById("e_idkaryawan").focus(); } </script>
<?PHP
}
?>


<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <input type='hidden' class='form-control' id='e_sudahada' name='e_sudahada' autocomplete='off' value='' />
            
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
                                
                                <div hidden><!--class='form-group'>-->
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <span class="control-label">#jika bukan dari CA Pilih blank_ (kosongkan) !!!</span>
                                    </div>
                                </div>

                                <div hidden><!--class='form-group'>-->
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idca'>Cash Advance <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_idca' name='e_idca' onchange="refresh_ca()">
                                            <?PHP
                                            echo "<option value='' selected>blank_</option>";
                                            /*
                                            if ($act!="update" OR !empty($idca)){
                                                
                                                if ($act != "update") echo "<option value='' selected>blank_</option>";
                                                $karyawan=trim($idajukan);
                                                $query = "select * from dbmaster.t_ca0 WHERE karyawanid='$karyawan' and stsnonaktif<>'Y' ";
                                                $query .=" order by tgl, idca";
                                                $tampil = mysqli_query($cnit, $query);
                                                $ketemu = mysqli_num_rows($tampil);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $pperiode =  date("d F Y", strtotime($a['periode']));
                                                    if ($a['idca']==$idca)
                                                        echo "<option value='$a[idca]' selected>$a[idca] - $pperiode</option>";
                                                    else
                                                        echo "<option value='$a[idca]'>$a[idca] - $pperiode</option>";
                                                }
                                            }else{
                                                echo "<option value='' selected>blank_</option>";
                                            }
                                             * 
                                             */
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div hidden <?PHP //echo $tutuparea; ?>>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idarea'>Area <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_idarea' name='e_idarea'>
                                        <?PHP
                                        if ($_GET['act']=="editdata"){
                                            PilihAreaCabangAll("", "", $idcabang.$idarea, "Y", $idajukan, $lvlpengajuan, $divisi, $idcabang, $idarea);
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>

                                
                                <div hidden <?PHP //echo $tutupadmin; ?>>
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
                                
                                <div <?PHP echo $tutupadmin; ?>>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Polisi Kendaraan <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_nopol' name='e_nopol'>
                                            <?PHP
                                            if ($_GET['act']=="editdata"){
                                                $query = "select * from dbmaster.t_kendaraan ";
                                                $query .=" order by merk, tipe, nopol";
                                                $tampil = mysqli_query($cnit, $query);
                                                $ketemu = mysqli_num_rows($tampil);
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                while($a=mysqli_fetch_array($tampil)){
                                                    if ($a['nopol']==$nopol)
                                                        echo "<option value='$a[nopol]' selected>$a[nopol] - $a[merk] $a[tipe]</option>";
                                                    else
                                                        echo "<option value='$a[nopol]'>$a[nopol] - $a[merk] $a[tipe]</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='thnbln01x'>Bulan </label>
                                    <div class='col-md-3'>
                                        
                                        
                                        <div class='input-group date' id='thnbln01x'>
                                            <input type='text' class='form-control' id='e_bulan' name='e_bulan' autocomplete='off' value='<?PHP echo $tglberlku; ?>' />
                                            <!--<input type="text" class="form-control" id='e_bulan' name='e_bulan' autocomplete='off' required='required' placeholder='MM/yyyy' data-inputmask="'mask': '99/9999'" value='<?PHP echo $tglberlku; ?>'>-->
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode Periode <span class='required'></span></label>
                                    <div class='col-xs-7'>
                                        <select class='form-control input-sm' id='e_periode' name='e_periode' onchange="showPeriode()">
                                            <?PHP
                                            if ($_SESSION['DIVISI']=="OTC"){
                                                echo "<option value='1' selected>Periode 1</option>";
                                            }else{
                                                if ((int)$tglhariini > 20) {
                                                    echo "<option value='' $selper0>-- Pilihan --</option>";
                                                    echo "<option value='2' $selper2>Periode 2</option>";
                                                }else{
                                                    echo "<option value='' $selper0>-- Pilihan --</option>";
                                                    echo "<option value='1' $selper1>Periode 1</option>";
                                                    echo "<option value='2' $selper2>Periode 2</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Periode <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <div class="form-group">
                                            <div class='input-group date' id='mytgl01<?PHP echo $nmperiode; ?>'>
                                                <input type='text' id='e_periode01' name='e_periode01' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $tgl1; ?>' data-inputmask="'mask': '99/99/9999'" Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            <div class='input-group date' id='mytgl02<?PHP echo $nmperiode; ?>'>
                                                <input type='text' id='e_periode02' name='e_periode02' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $tgl2; ?>' data-inputmask="'mask': '99/99/9999'" Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
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
                                
                                
                                <div <?PHP echo $tutupadmin; ?>>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type="button" class='btn btn-warning btn-xs'  name="btn_refresh" id="btn_refresh" onclick='refresh_atasan()' value="Refresh Atasan..">
                                    </div>
                                </div>
                                
                                <!-- Appove SPV / AM -->
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_atasan' name='e_atasan' onchange="ShowAtasanDM('e_atasan')">
                                            <?PHP
                                            if ($_SESSION['GROUP']==33 OR $_SESSION['GROUP']==32 OR $_SESSION['GROUP']==27) {
                                                $query="select a.dm, b.nama from dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.dm=b.karyawanId where a.karyawanId='$karyawan'";
                                                $tampil = mysqli_query($cnit, $query);
                                                $ketemu = mysqli_num_rows($tampil);
                                                echo "<option value='' selected>blank_</option>";
                                                while($a=mysqli_fetch_array($tampil)){
                                                    echo "<option value='$a[dm]'>$a[nama]</option>";
                                                }
                                            }else{
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
                                        
                                        if ($_SESSION['GROUP']==33 OR $_SESSION['GROUP']==32 OR $_SESSION['GROUP']==27) {
                                            $query="select a.dm, b.nama from dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.dm=b.karyawanId where a.karyawanId='$idajukan'";
                                            $tampil = mysqli_query($cnit, $query);
                                            $ketemu = mysqli_num_rows($tampil);
                                            echo "<option value=''>blank_</option>";
                                            while($a=mysqli_fetch_array($tampil)){
                                                echo "<option value='$a[dm]' selected>$a[nama]</option>";
                                            }
                                        }else{
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
                                        
                                        if ($_SESSION['GROUP']==33 OR $_SESSION['GROUP']==32 OR $_SESSION['GROUP']==27) {
                                            $query="select a.sm, b.nama from dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.sm=b.karyawanId where a.karyawanId='$idajukan'";
                                            $tampil = mysqli_query($cnit, $query);
                                            $ketemu = mysqli_num_rows($tampil);
                                            echo "<option value=''>blank_</option>";
                                            while($a=mysqli_fetch_array($tampil)){
                                                echo "<option value='$a[sm]' selected>$a[nama]</option>";
                                            }
                                        }else{
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
                                
                                <div hidden><!--class='form-group'>-->
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saldo CA (Rp.) <br/>#jika data dari CA...<span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_casaldo' name='e_casaldo' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $saldoca; ?>' readonly>
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
                                
                                <?PHP
                                if ($_SESSION['DIVISIKHUSUS']!="Y" AND $_SESSION['LVLPOSISI']!="FF6") {
                                ?>
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type="button" class='btn btn-success btn-xs' name="btn_viewuc" id="btn_viewuc" value="Lihat Data Hari Kerja" 
                                               onclick="window.open('<?PHP echo "eksekusi3.php?module=entrybrluarkota&iprint=kunjungan"; ?>', 'winpopup', 
                                                    'toolbar=no,statusbar=no,menubar=no,resizable=yes,scrollbars=yes,width=800,height=400');" />
                                    </div>
                                </div>
                                <?PHP
                                }
                                ?>
                                
                        
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
                                        include "module/mod_br_brrutin/inputdetailmobile.php";
                                    ?>
                                </div>
                            <?PHP }else{
                                include "module/mod_br_brrutin/inputdetail.php";
                            }
                            ?>
                            
                            <?PHP
                            if ($_SESSION['DIVISI']=="OTC") {
                            ?>
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Penambahan Rp. <span class='required'></span></label>
                                <div class='col-md-4'>
                                    <input type='text' id='e_penambahan' name='e_penambahan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $penambahan; ?>'>
                                </div>
                            </div>
                                
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>COA (Penambahan) <span class='required'></span></label>
                                <div class='col-xs-5'>
                                    <select class='form-control input-sm' id='e_coatambah' name='e_coatambah'>
                                        <?PHP
                                        $query = "select a.COA4, a.NAMA4, c.DIVISI2 from dbmaster.coa_level4 a "
                                                . "JOIN dbmaster.coa_level3 b on a.COA3=b.COA3 JOIN dbmaster.coa_level2 c on b.COA2=c.COA2 WHERE c.DIVISI2='OTC' ";
                                        $query .=" order by a.COA4, a.NAMA4";
                                        $tampil = mysqli_query($cnit, $query);
                                        echo "<option value='' selected>-- Pilihan --</option>";
                                        while($bb=mysqli_fetch_array($tampil)){
                                            if ($bb['COA4']==$coatambah)
                                                echo "<option value='$bb[COA4]' selected>$bb[NAMA4]</option>";
                                            else
                                                echo "<option value='$bb[COA4]'>$bb[NAMA4]</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <?PHP
                            }else{
                                echo "<input type='hidden' id='e_penambahan' name='e_penambahan' value=''>";
                                echo "<input type='hidden' id='e_coatambah' name='e_coatambah' value=''>";
                            }
                            ?>
                                
                        </div>
                    </div>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                            if (empty($sudahapv)) {
                                if ($_GET['act']=="editdata" AND !isset($_GET['ca'])) {
                                    ?><button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button><?PHP
                                }else{
                                echo "<div class='col-sm-5'>";
                                include "module/mod_br_brrutin/ttd_biayarutin.php";
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
        //showCA('e_idkaryawan', 'e_bulan');
        showKendaraan();
        if (pdivisi=="OTC") {
            $("#cb_divisi").html("<option value="+pdivisi+">"+pdivisi+"</option>");
            $("#e_atasan").html("<option value=''>blank_</option");
            $("#e_atasan2").html("<option value=''>blank_</option");
            $("#e_atasan3").html("<option value=''>blank_</option");
        }else{
            $("#cb_divisi").html("<option value=''>blank_</option");
            //showDivisi(pkar, pjabatan, prank, pdivisi);
            
            var elvlpos = plevel;
            if (elvlpos=="AD1") {
            }else if (elvlpos=="OB1") {
                $("#e_atasan").html("<option value=''>blank_</option");
                ShowAtasanDM('e_idkaryawan');
            }else{
                $("#e_atasan").html("<option value=''>blank_</option");
                $("#e_atasan2").html("<option value=''>blank_</option");
                $("#e_atasan3").html("<option value=''>blank_</option");
            }
            
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
    
    
    function refresh_ca() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");

        document.getElementById("demo-form2").action = "module/mod_br_brrutin/refreshca.php?module="+module+"&act=editdata"+"&idmenu="+idmenu;
        document.getElementById("demo-form2").submit();
        
        return 1;
    }
    
    function showCA(ecar, eperiode) {
        var icar = document.getElementById(ecar).value;
        var iperiode = document.getElementById(eperiode).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdata.php?module=viewdatacashadvance",
            data:"umr="+icar+"&uperiode="+iperiode,
            success:function(data){
                $("#e_idca").html(data);
                CekCA();
            }
        });
    }
    
    function CekCA() {
        var eca =document.getElementById('e_idca').value;
        if (eca!="") {
            refresh_ca();
        }
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
    
    function showKendaraan() {
        var icar = document.getElementById('e_idkaryawan').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdata.php?module=viewdatakendaraan",
            data:"umr="+icar,
            success:function(data){
                $("#e_nopol").html(data);
            }
        });
    }
    
    function showPeriode() {
        var ikode = document.getElementById('e_periode').value;
        var ibulan = document.getElementById('e_bulan').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdata.php?module=getperiode",
            data:"ubulan="+ibulan+"&ukode="+ikode,
            success:function(data){
                var arr_date = data.split(",");
                document.getElementById('e_periode01').value=arr_date[0];
                document.getElementById('e_periode02').value=arr_date[1];
            }
        });
    }
    
    function disp_confirm(pText_, ket)  {
        var ekar =document.getElementById('e_idkaryawan').value;
        var eperi =document.getElementById('e_periode').value;
        var ediv =document.getElementById('cb_divisi').value;
        var elvlpos =document.getElementById('e_lvl').value;
        var eatasan1 =document.getElementById('e_atasan').value;
        var eatasan2 =document.getElementById('e_atasan2').value;
        var eatasan3 =document.getElementById('e_atasan3').value;
        var etotsem =document.getElementById('e_totalsemua').value;
        var eidca =document.getElementById('e_idca').value;
        var esaldoca =document.getElementById('e_casaldo').value;
        
        if (etotsem === "") etotsem=0;

        elvlpos=elvlpos.trim();

        var rlevel = elvlpos.substring(0, 2);


        if (ekar==""){
            alert("yang membuat masih kosong....");
            return 0;
        }

        if (eperi==""){
            alert("periode harus diisi....");
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
            
            
        if (ediv == "OTC"){
            var etambah =document.getElementById('e_penambahan').value;
            var itambah = etambah.replace(/\,/g,'');
            if (itambah=="") itambah=0;
            if (parseInt(itambah)>0) {
                var ecoatambah =document.getElementById('e_coatambah').value;
                if (ecoatambah==""){
                    alert("COA Penambahan harus diisi....");
                    return 0;
                }
            }
        }


        if (parseInt(etotsem)==0) {
            alert("Total Rupiah Masih Kosong....");
            return 0;
        }

        if (eidca !=""){
            var isaldo = esaldoca.replace(/\,/g,'');
            var itotal = etotsem.replace(/\,/g,'');
            if (parseFloat(itotal)>parseFloat(isaldo)) {
                alert("Tidak boleh melebihi saldo CA. Saldo CA : Rp. "+esaldoca);
                return 0;
            }
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

                document.getElementById("demo-form2").action = "module/mod_br_brrutin/aksi_brrutin.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
        <?PHP if (empty($idarea)) { ?>
            var selinc = "";
        <?PHP }else{ ?>
            var selinc = <?PHP echo $idarea; ?>;
        <?PHP } ?>
        <?PHP if ($_GET['act']=="tambahbaru"){ ?>
        
            showDataKaryawan('input', '<?PHP echo $idajukan; ?>');
        
        <?PHP } ?>
            
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
        findTotal();
        
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
        var a9 = document.getElementById('e_total9').value;
        var a10 = document.getElementById('e_total10').value;
        var a11 = document.getElementById('e_total11').value;
        var a12 = document.getElementById('e_total12').value;
        var a13 = document.getElementById('e_total13').value;
        var a14 = document.getElementById('e_total14').value;
        
        a1 = a1.split(',').join(newchar);
        a2 = a2.split(',').join(newchar);
        a3 = a3.split(',').join(newchar);
        a4 = a4.split(',').join(newchar);
        a5 = a5.split(',').join(newchar);
        a6 = a6.split(',').join(newchar);
        a7 = a7.split(',').join(newchar);
        a8 = a8.split(',').join(newchar);
        a9 = a9.split(',').join(newchar);
        a10 = a10.split(',').join(newchar);
        a11 = a11.split(',').join(newchar);
        a12 = a12.split(',').join(newchar);
        a13 = a13.split(',').join(newchar);
        a14 = a14.split(',').join(newchar);
        if (a1 === "") a1=0; if (a2 === "") a2=0; if (a3 === "") a3=0; if (a4 === "") a4=0;
        if (a5 === "") a5=0; if (a6 === "") a6=0; if (a7 === "") a7=0; if (a8 === "") a8=0;
        if (a9 === "") a9=0; if (a10 === "") a10=0; if (a11 === "") a11=0; if (a12 === "") a12=0;
        if (a13 === "") a13=0; if (a14 === "") a14=0;
        
        
        tot =parseInt(a1)+parseInt(a2)+parseInt(a3)+parseInt(a4)+parseInt(a5)+parseInt(a6)
            +parseInt(a7)+parseInt(a8)+parseInt(a9)+parseInt(a10)+parseInt(a11)
            +parseInt(a12)+parseInt(a13)+parseInt(a14);
        document.getElementById('e_totalsemua').value = tot;
    }

</script>


<script>
                                    
    $(document).ready(function() {

        $('#e_bulan').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'MM yy',
            <?PHP
            if ($_SESSION['DIVISI']!="OTC") {
                if ($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="28") {
                    ?>
                     minDate: '-5M',
                    <?PHP
                }else{
                    if ($hari_ini=="2019-01-02") {
                    ?>
                        minDate: '-1M',
                    <?PHP
                    }else{
                    ?>
                        minDate: '0M',
                    <?PHP
                    }
                }
            }
            ?>
            onSelect: function(dateStr) {

            },
            onClose: function() {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                showPeriode();
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
    .ui-datepicker-calendar {
        display: none;
    }
    
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