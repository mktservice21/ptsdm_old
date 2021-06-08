<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />
                                
<?PHP
include "config/koneksimysqli.php";
$nmperiode = "";
//if ($_SESSION['GROUP']==23 or $_SESSION['GROUP']==26) $nmperiode = "x";
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
if ($_SESSION['IDCARD']=="0000001587") $hanyasatukaryawan="N"; //MARINA
if ($_SESSION['IDCARD']=="0000002329") $hanyasatukaryawan="N"; //ELSA

$idklaim="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d F Y', strtotime($hari_ini));
$tgl1 = date('01/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('F Y', strtotime($hari_ini));
$blnpilihuc = date('Y-m', strtotime($hari_ini));
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP'];
$pjabatanid = getfield("select jabatanId as lcfields from dbmaster.t_karyawan_posisi where karyawanId='$idajukan'");
if (empty($pjabatanid))
    $pjabatanid = getfield("select jabatanId as lcfields from hrd.karyawan where karyawanId='$idajukan'");

if (empty($pjabatanid)) $pjabatanid=$_SESSION['JABATANID'];

$rank = getfield("select rank as lcfields from hrd.jabatan where jabatanId='$pjabatanid'");
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
$sudahapv = "";

$idca = "";
$totalca = 0;
$ketca = "";
$darica = "";
$stykry1 = "style='display:block;'";
$stykry2 = "style='display:none;'";
if (isset($_GET['ca'])) {
    $darica = "ca";
    $stykry1 = "style='display:none;'";
    $stykry2 = "style='display:block;'";
}

$pnmkrynone="";

$pkd_krynone_="";
$pnm_krynonelama_="";
$pnm_krynonebaru_="";


$act="input";

if (isset($_GET['ca'])) {
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.v_ca0 WHERE idca='$_GET[ca]'");
    $r    = mysqli_fetch_array($edit);
    $idklaim="";
    $idajukan=$r['karyawanid']; 
    $nmajukan=$r['nama'];
    
    
    $divisi=$r['divisi'];
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
    $idarea=$r['areaid'];
    $idcabang=$r['icabangid'];
    $nmarea=$r['nama_area'];
    $ketca=$r['keterangan'];
    //$totalca=$r['jumlah'];
    
    $ca_jumlahrp=$r['jumlah'];
    $rutin_jumlahrp = getfield("select sum(jumlah) as lcfields from dbmaster.t_brrutin0 WHERE idca='$_GET[ca]' AND stsnonaktif <> 'Y'");
    $sewa_jumlahrp = getfield("select sum(jumlah) as lcfields from dbmaster.t_sewa WHERE idca='$_GET[ca]' AND stsnonaktif <> 'Y'");
    if (empty($rutin_jumlahrp)) $rutin_jumlahrp = 0;
    if (empty($sewa_jumlahrp)) $sewa_jumlahrp = 0;
    $saldoca = (double)$ca_jumlahrp-(double)$rutin_jumlahrp-(double)$sewa_jumlahrp;
    $totalca=$saldoca;
    
    $nopol = getfield("select nopol as lcfields from dbmaster.t_kendaraan_pemakai where karyawanid='$idajukan' and stsnonaktif <> 'Y'");
    
    $pjabatanid = $r['jabatanid'];
    $rank = getfield("select rank as lcfields from hrd.jabatan where jabatanId='$pjabatanid'");
    $lvlpengajuan = getfield("select LEVELPOSISI as lcfields from dbmaster.v_level_jabatan where jabatanId='$pjabatanid'");
    $lvlpengajuan = trim($lvlpengajuan);
    if ($lvlpengajuan=="0") $lvlpengajuan="";
    if (empty($lvlpengajuan)) $lvlpengajuan = $_SESSION['LVLPOSISI'];
        
    $patasan1=$r['atasan1'];
    $patasan2=$r['atasan2'];
    $patasan3=$r['atasan3'];
    $patasan4=$r['atasan4'];
        
}else{

    if ($_GET['act']=="editdata"){
        
        $act="update";
        $hanyasatukaryawan = "";
        
        $filygmengajukan="";
        if ($_SESSION['LVLPOSISI']=="FF1" or $_SESSION['LVLPOSISI']=="FF2" or $_SESSION['LVLPOSISI']=="FF3" or $_SESSION['LVLPOSISI']=="FF4")
            $filygmengajukan=" AND karyawanid='$_SESSION[IDCARD]'";


        $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.v_brrutin0 WHERE kode=2 AND idrutin='$_GET[id]' $filygmengajukan");
        $r    = mysqli_fetch_array($edit);
        $idklaim=$r['idrutin'];
        $tglberlku = date('F Y', strtotime($r['bulan']));
        $tgl1 = date('d/m/Y', strtotime($r['periode1']));
        $tgl2 = date('d/m/Y', strtotime($r['periode2']));
        $idajukan=$r['karyawanid']; 
        $nmajukan=$r['nama'];
        
        $pnmkrynone=$r['nama_karyawan'];
        
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
        $rank = getfield("select rank as lcfields from hrd.jabatan where jabatanId='$pjabatanid'");
        $lvlpengajuan = getfield("select LEVELPOSISI as lcfields from dbmaster.v_level_jabatan where jabatanId='$pjabatanid'");
        $lvlpengajuan = trim($lvlpengajuan);
        if ($lvlpengajuan=="0") $lvlpengajuan="";
        if (empty($lvlpengajuan)) $lvlpengajuan = $_SESSION['LVLPOSISI'];
        $totalsemua=$r['jumlah'];
        $nopol=$r['nopol'];
        $idca=$r['idca'];
        if (!empty($idca)) {
            $stykry1 = "style='display:none;'";
            $stykry2 = "style='display:block;'";
        }
        
        $patasan1=$r['atasan1'];
        $patasan2=$r['atasan2'];
        $patasan3=$r['atasan3'];
        $patasan4=$r['atasan4'];


        if ($idajukan=="0000002200") {
            $pnm_krynonelama_=$r['nama_karyawan'];
            $pnm_krynonebaru_=$r['nama_karyawan'];
            $pkd_krynone_="";
        }


        $t_ats1 = $r["tgl_atasan1"];
        $t_ats2 = $r["tgl_atasan2"];
        $t_ats3 = $r["tgl_atasan3"];
        $t_ats4 = $r["tgl_atasan4"];
        $sreject = $r["stsnonaktif"];

        $g_ats1 = $r["gbr_atasan1"];
        $g_ats2 = $r["gbr_atasan2"];

        if ($lvlpengajuan=="FF1") {
			
			if (empty($g_ats1) AND empty($g_ats2)) {
			}else{
				if (!empty($t_ats1) OR !empty($t_ats2) OR !empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
			}
			
        }elseif ($lvlpengajuan=="FF2") {
            if (!empty($t_ats2) OR !empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
        }elseif ($lvlpengajuan=="FF3") {
			
			if ($_SESSION['GROUP']=="42") {
				if (!empty($t_ats4)) $sudahapv="sudah";
			}else{
			
				if (!empty($t_ats3) OR !empty($t_ats4)) $sudahapv="sudah";
				
			}
        }elseif ($lvlpengajuan=="FF4") {
            if (!empty($t_ats4)) $sudahapv="sudah";
        }

        if ($sreject=='Y') { 
            $sudahapv="reject";
        }else{
            if ($_SESSION['DIVISI']=="OTC") {
                $sudahapv="";
            }
        }
        
        
        if (!empty($idca)) {
            $editca = mysqli_query($cnmy, "SELECT * FROM dbmaster.v_ca0 WHERE idca='$idca'");
            $ca    = mysqli_fetch_array($editca);
            $ketca=$ca['keterangan'];
            //$totalca=$ca['jumlah'];
            $ca_jumlahrp=$ca['jumlah'];
            $rutin_jumlahrp = getfield("select sum(jumlah) as lcfields from dbmaster.t_brrutin0 WHERE idrutin <>'$idklaim' AND idca='$idca' AND stsnonaktif <> 'Y'");
            $sewa_jumlahrp = getfield("select sum(jumlah) as lcfields from dbmaster.t_sewa WHERE idca='$idca' AND stsnonaktif <> 'Y'");
            if (empty($rutin_jumlahrp)) $rutin_jumlahrp = 0;
            if (empty($sewa_jumlahrp)) $sewa_jumlahrp = 0;
            $saldoca = (double)$ca_jumlahrp-(double)$rutin_jumlahrp-(double)$sewa_jumlahrp;
            $totalca=$saldoca;
            $darica = "ca";
        }
        
    }
    
    
}

$tutuparea="class='form-group'";
$tutupadmin="class='form-group'";

if ($_SESSION['GROUP']==15 OR $_SESSION['GROUP']==33) {
    $tutuparea="hidden";
    $tutupadmin="hidden";
}




    $karyawannone=$_SESSION['KRYNONE'];
    
    $pstskrynone="";
    $txtkrynone="hidden";
    if ($idajukan==$karyawannone) { $txtkrynone="class='form-group'"; $pstskrynone="NONE"; }
    
    $txtkendaraannone="class='form-group'";
    if ($pjabatanid=="15" OR $pjabatanid=="38") { $txtkendaraannone="hidden"; }
    
    
$fdata_uc="";
if ($act=="update") $fdata_uc=" AND a.idrutin <>'$idklaim'";
//JUMLAH UC diambil dari akun 21 HOTEL
$query = "select SUM(a.qty) as qty FROM dbmaster.t_brrutin1 as a "
        . " JOIN dbmaster.t_brrutin0 as b on a.idrutin=b.idrutin "
        . " WHERE b.kode='2' AND b.karyawanid='$idajukan' AND LEFT(b.bulan,7)='$blnpilihuc' AND "
        . " a.nobrid IN ('21') AND IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(a.rp,0)<>0 $fdata_uc";
$tampil_s= mysqli_query($cnmy, $query);
$rows= mysqli_fetch_array($tampil_s);
$pjumlahhr_blinput=$rows['qty'];


$query = "select SUM(a.qty) as qtyi FROM dbmaster.t_brrutin1 as a "
        . " JOIN dbmaster.t_brrutin0 as b on a.idrutin=b.idrutin "
        . " WHERE b.kode='2' AND b.karyawanid='$idajukan' AND LEFT(b.bulan,7)='$blnpilihuc' AND "
        . " a.nobrid IN ('21') AND IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(a.rp,0)<>0 AND a.idrutin ='$idklaim'";
$tampil_i= mysqli_query($cnmy, $query);
$rowi= mysqli_fetch_array($tampil_i);
$pjumlahhr_bledit=$rowi['qtyi'];

//cari data UC dari tabel cuti
$sql = "select COUNT(distinct b.tanggal) as jmlhariuc FROM hrd.t_cuti0 as a "
        . " LEFT JOIN hrd.t_cuti1 as b on a.idcuti=b.idcuti WHERE a.id_jenis IN ('05') AND "
        . " a.karyawanid='$idajukan' AND LEFT(b.tanggal,7)='$blnpilihuc' AND IFNULL(a.stsnonaktif,'')<>'Y' ";
if ($pjabatanid=="15" OR $pjabatanid=="38") {
    $sql .=" AND IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' ";
}elseif ($pjabatanid=="05") {
    $sql .=" AND IFNULL(a.tgl_atasan5,'')<>'' AND IFNULL(a.tgl_atasan5,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' ";
}else{
    $sql .=" AND IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' ";
}
$tampil_u= mysqli_query($cnmy, $sql);
$rowu= mysqli_fetch_array($tampil_u);
$pjumlahhr_ucinput=$rowu['jmlhariuc'];

if (empty($pjumlahhr_blinput)) $pjumlahhr_blinput=0;
if (empty($pjumlahhr_bledit)) $pjumlahhr_bledit=0;
if (empty($pjumlahhr_ucinput)) $pjumlahhr_ucinput=0;

//echo "$pjumlahhr_blinput dan $pjumlahhr_ucinput<br/>";

if ($_SESSION['IDCARD']=="0000000329" OR $_SESSION['IDCARD']=="0000000962") {
    $pjumlahhr_ucinput=100;
    $pjumlahhr_blinput=0;
}

if (isset($_GET['ca'])) {
?>
    <script> window.onload = function() { document.getElementById("e_ket").focus(); } </script>
<?PHP
}else{
?>
    <script> window.onload = function() { document.getElementById("e_id").focus(); } </script>
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
                                    </div>
                                </div>
                                
                                <?PHP
                                if (substr($_SESSION['LVLPOSISI'],0,2)!="FF" AND $_SESSION['JABATANID']!=38) {
                                    if ($_GET['act']=="tambahbaru" OR !empty($darica)) {
                                ?>
                                        <div hidden><!--class='form-group'-->
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>&nbsp; <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <?PHP
                                                if (!empty($darica)) {
                                                    echo "<input type='checkbox' name='chk_dari' id='chk_dari' onclick=\"myShowHide()\" checked>Dari Cash Advance<br/>";
                                                }else{
                                                    echo "<input type='checkbox' name='chk_dari' id='chk_dari' onclick=\"myShowHide()\">Dari Cash Advance<br/>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                <?PHP
                                    }
                                }
                                ?>
                                
                                
                                <div class='form-group' <?PHP echo $stykry1; ?> id='divkry1'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_idkaryawan' name='e_idkaryawan' onchange="showDataKaryawan('tambahbaru', 'e_idkaryawan')">
                                            <?PHP
                                            //comboKaryawanAktifAll("", "pilihan", $idajukan, $_SESSION['STSADMIN'], $_SESSION['LVLPOSISI'], $fildiv, $_SESSION['IDCARD'], $jabatan_);
                                            //PilihKaryawanAktif($konek, $adapilihan, $ygdipilih, $hanyaygaktif, $stsadim, $divisi, $lvlposisi, $tampilkanlevelbawahan, $karyawan, $jabatan, $region, $cabang, $area, $hanyasatu)
											if ($_SESSION['DIVISI']=="OTC") {
												//PilihKaryawanAktif("", "-- Pilihan --", $idajukan, "Y", $_SESSION['STSADMIN'], $fildiv, $_SESSION['LVLPOSISI'], $tampilbawahan, $_SESSION['IDCARD'], $jabatan_, $_SESSION['AKSES_REGION'], $filkaryawncabang, "", $hanyasatukaryawan);
												
												echo "<option value='' selected>--Pilihan--</option>";
												$query = "select b.karyawanid, b.nama from hrd.karyawan b WHERE b.aktif='Y' and (IFNULL(b.tglkeluar,'')='' OR IFNULL(b.tglkeluar,'0000-00-00')='0000-00-00') ";
												$query .= " AND IFNULL(b.jabatanid,'') NOT IN ('35') ";
												$query .= " AND b.divisiId='OTC' ";
												$query .= " AND b.karyawanid NOT IN ('0000001272', '0000000432', '0000000992') ";
												$query .=" AND b.karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
												$query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ')  "
														. " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
														. " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-') "
														. " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ') ";
												
												$query .= " OR b.karyawanid='$idajukan' ";
												
                                                                                                
                                                                                                $query = "select DISTINCT b.karyawanId as karyawanid, b.nama FROM hrd.karyawan as b JOIN dbmaster.t_karyawan_posisi as a "
                                                                                                        . " on a.karyawanId=b.karyawanId WHERE ( (IFNULL(a.rutin_chc,'')='Y' AND IFNULL(a.aktif,'')<>'N') OR "
                                                                                                        . " b.karyawanId IN ('$idajukan', '0000002200') ) ";
												$query .= " order by b.nama";
												$tampil=mysqli_query($cnmy, $query);
												while ($rt= mysqli_fetch_array($tampil)) {
													$pkryid=$rt['karyawanid'];
													$pnmkry=$rt['nama'];
													
													if ($pkryid==$idajukan)
														echo "<option value='$pkryid' selected>$pnmkry</option>";
													else
														echo "<option value='$pkryid'>$pnmkry</option>";
												}
											}else{
												PilihKaryawanAktif("", "-- Pilihan --", $idajukan, "Y", $_SESSION['STSADMIN'], $fildiv, $_SESSION['LVLPOSISI'], $tampilbawahan, $_SESSION['IDCARD'], $jabatan_, $_SESSION['AKSES_REGION'], $filkaryawncabang, "", $hanyasatukaryawan);
											}
                                            ?>
                                        </select>
                                        
                                        <div id="kry_none_" style="display:none;">
                                        
                                            <input type='hidden' class='form-control' id='e_nmkrynone2' name='e_nmkrynone2' autocomplete="off" value='<?PHP echo $pnm_krynonelama_; ?>'>
                                            <input type='hidden' id='e_kdkrynone' name='e_kdkrynone' autocomplete='off' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkd_krynone_; ?>'>
                                            
                                            <input type="text" class='form-control' id="e_nmkrynone" name="e_nmkrynone" size="50px" placeholder="cari data..."
                                                   onkeyup="cariFormData(this.id, 'e_kdkrynone', 'myDivSearching2', 'carikaryawankontrak')" 
                                                   onkeydown="checkkey()" 
                                                   autocomplete="off" value="<?PHP echo $pnm_krynonebaru_; ?>" />
                                            
                                            <div id="myDivSearching2"></div>
                                            
                                        </div>
										
										
										
										
                                    </div>
                                </div>
                                

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idca'>ID Cash Advance <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_idca' name='e_idca' onchange="">
                                            <?PHP
                                                $query = "select * from dbmaster.t_ca0 WHERE jenis_ca='lkxxx' AND karyawanid='$idajukan' and stsnonaktif<>'Y' ";
                                                $query .=" order by periode, idca";
                                                $tampil = mysqli_query($cnmy, $query);
                                                $ketemu = mysqli_num_rows($tampil);
                                                if ($ketemu>0) {
                                                    $sudahselect="";
                                                    while($a=mysqli_fetch_array($tampil)){
                                                        $pperiode =  date("F Y", strtotime($a['periode']));
                                                        if (empty($sudahselect)){
                                                            echo "<option value='$a[idca]' selected>$a[idca] - $pperiode</option>";
                                                            $sudahselect="sudah";
                                                            $idca = $a['idca'];
                                                        }else
                                                            echo "<option value='$a[idca]'>$a[idca] - $pperiode</option>";
                                                    }
                                                }else{
                                                    echo "<option value='' selected>blank_</option>";
                                                }
                                                
                                                if (!empty($idca)) {
                                                    $ca_jumlahrp = getfield("select sum(jumlah) as lcfields from dbmaster.t_ca0 WHERE idca='$idca' AND stsnonaktif <> 'Y'");
                                                    $rutin_jumlahrp = getfield("select sum(jumlah) as lcfields from dbmaster.t_brrutin0 WHERE idca='$idca' AND stsnonaktif <> 'Y' AND kode=1 AND idrutin <> '$idklaim'");
                                                    if (empty($ca_jumlahrp)) $ca_jumlahrp=0;
                                                    if (empty($rutin_jumlahrp)) $rutin_jumlahrp=0;
                                                    $saldoca=(double)$ca_jumlahrp-(double)$rutin_jumlahrp;
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div hidden class='form-group' <?PHP echo $stykry2; ?> id='divkry2'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>YangMembuatCA <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        
                                        <select class='form-control input-sm' id='e_idkaryawan_ca' name='e_idkaryawan_ca' onchange="refresh_ca()">
                                            <?PHP
                                            $filterdivisica = "";
                                            if (!empty($fildiv)) $filterdivisica = " AND divisi in ".$fildiv;
                                            
                                            if (!empty($idca)) {
                                                $query = "select divisi, tgl, karyawanid karyawanId, nama, idca, jumlah, periode, areaid 
                                                    from dbmaster.v_ca0 where jenis_ca='lkxxx' AND idca='$idca' $filterdivisica ";
                                            }else{
                                                $query = "select divisi, tgl, karyawanid karyawanId, nama, idca, jumlah, periode, areaid 
                                                    from dbmaster.v_ca0 where jenis_ca='lkxxx' AND stsnonaktif <> 'Y' 
                                                    $filterdivisica ";
                                            }//and idca not in (select distinct ifnull(idca,'') from dbmaster.t_brrutin0) 
                                            $query .=" order by divisi, tgl, nama, karyawanid";       
                                            $tampil = mysqli_query($cnmy, $query);
                                            $ketemu = mysqli_num_rows($tampil);
                                            //if ($ketemu<>1) echo "<option value='' selected>-- Pilihan --</option>";
                                            while($a=mysqli_fetch_array($tampil)){
                                                $gid = $a['idca'];
                                                $gdiv = $a['divisi'];
                                                $gtgl = $a['tgl'];
                                                $gperiode =  date("d/m/Y", strtotime($gtgl));
                                                $ca_jumlahrp=$a['jumlah'];
                                                $filrt = "";
                                                if (!empty($idklaim)) $filrt = " AND idrutin <> '$idklaim' ";
                                                $rutin_jumlahrp = getfield("select sum(jumlah) as lcfields from dbmaster.t_brrutin0 WHERE idca='$gid' AND stsnonaktif <> 'Y' $filrt");
                                                $sewa_jumlahrp = getfield("select sum(jumlah) as lcfields from dbmaster.t_sewa WHERE idca='$gid' AND stsnonaktif <> 'Y'");
                                                if (empty($rutin_jumlahrp)) $rutin_jumlahrp = 0;
                                                if (empty($sewa_jumlahrp)) $sewa_jumlahrp = 0;
                                                $saldoca = (double)$ca_jumlahrp-(double)$rutin_jumlahrp-(double)$sewa_jumlahrp;
                                                if ((double)$saldoca>0) {
                                                    echo "<optgroup label='$gdiv - $gperiode - $gid'>";
                                                    if ($a['karyawanId']==$idajukan)
                                                        echo "<option value='$a[idca]' selected>$a[nama]</option>";
                                                    else
                                                        echo "<option value='$a[idca]'>$a[nama]</option>";
                                                }else{
                                                    //echo "<option value=''>blank_</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        
                                    </div>
                                </div>

                                

                        <div id='div_karyawan'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='hidden' id='e_stskaryawan' name='e_stskaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pstskrynone; ?>' Readonly>
                                        <input type='hidden' id='e_jabatan' name='e_jabatan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pjabatanid; ?>' Readonly>
                                        <input type='hidden' id='e_rank' name='e_rank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $rank; ?>' Readonly>
                                        <input type='hidden' id='e_lvl' name='e_lvl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $lvlpengajuan; ?>' Readonly>
                                    </div>
                                </div>

                            
                                <div <?PHP echo $txtkrynone; ?>>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Karyawan <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_namakrynone' name='e_namakrynone' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmkrynone; ?>'>
                                    </div>
                                </div>
                            
                                <div <?PHP echo $txtkrynone; ?>>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_idcabang' name='e_idcabang' onchange="TampilkanArea()">
                                            <option value='' selected>blank_</option>
                                            <?PHP
                                            if ($_GET['act']=="editdata"){
                                                if ($_SESSION['DIVISI']=="OTC"){
                                                    $sql=mysqli_query($cnmy, "SELECT icabangid_o iCabangId, nama FROM MKT.icabang_o where icabangid_o='$idcabang' order by nama");
                                                }else{
                                                    $sql=mysqli_query($cnmy, "SELECT iCabangId, nama FROM MKT.icabang where iCabangId='$idcabang' order by nama");
                                                }
                                                while ($Xt=mysqli_fetch_array($sql)){
                                                    $didcab=$Xt['iCabangId'];
                                                    $dnmcab=$Xt['nama'];
                                                    if ($didcab==$idcabang)
                                                        echo "<option value='$didcab' selected>$dnmcab</option>";
                                                    else
                                                        echo "<option value='$didcab'>$dnmcab</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                            
                                <div <?PHP echo $txtkrynone; ?>>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idarea'>Area <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_idarea' name='e_idarea'>
                                        <?PHP
                                        if ($_GET['act']=="editdata"){
                                            if ($_SESSION['DIVISI']=="OTC"){
                                                $sql=mysqli_query($cnmy, "SELECT areaid_o areaid, nama FROM MKT.iarea_o where areaid_o='$idarea' AND icabangid_o='$idcabang' order by nama");
                                                while ($Xt=mysqli_fetch_array($sql)){
                                                    $didcab=$Xt['areaid'];
                                                    $dnmcab=$Xt['nama'];
                                                    if ($didcab==$idcabang)
                                                        echo "<option value='$didcab' selected>$dnmcab</option>";
                                                    else
                                                        echo "<option value='$didcab'>$dnmcab</option>";
                                                }
                                            }else{
                                                PilihAreaCabangAll("", "", $idcabang.$idarea, "Y", $idajukan, $lvlpengajuan, $divisi, $idcabang, $idarea);
                                            }
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>

                                
                                
                                <div <?PHP echo $txtkrynone; ?>>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="">
                                        <?PHP
                                        if ($_GET['act']=="editdata"){
                                            $kry=$idajukan;
                                            if ($_SESSION['DIVISI']=="OTC"){
                                                $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where DivProdId='OTC' order by nama");
                                            }else{
                                                if ($rank=="05" OR $rank==5)
                                                    $sql= mysqli_query($cnmy, "select distinct divisiid from MKT.imr0 where karyawanid='$kry'");
                                                elseif ($rank=="04" OR $rank==4)
                                                    $sql= mysqli_query($cnmy, "select distinct divisiid from MKT.ispv0 where karyawanid='$kry'");
                                                else{
                                                    $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' order by nama");
                                                }
                                            }
                                            $ketemu= mysqli_num_rows($sql);
                                            if ($ketemu==0) {
                                                $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' order by nama");
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
                                
                                
                                
                                <div <?PHP echo $txtkendaraannone; ?>>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Polisi Kendaraan <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_nopol' name='e_nopol'>
                                            <?PHP
                                            if ($_GET['act']=="editdata"){
                                                $query = "select * from dbmaster.t_kendaraan WHERE nopol='$nopol' ";
                                                $query .=" order by merk, tipe, nopol";
                                                $tampil = mysqli_query($cnmy, $query);
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
                                
                        </div>
                                
                                
                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
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

                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode Periode <span class='required'></span></label>
                                    <div class='col-xs-7'>
                                        <select class='form-control input-sm' id='e_periode' name='e_periode' onchange="showPeriode()">
                                            <?PHP
                                            if ($_SESSION['DIVISI']=="OTC"){
                                                ?><option value="1" selected>Periode 1</option><?PHP
                                            }else{
                                            ?>
                                            <option value="" <?PHP echo $selper0; ?>>-- Pilihan --</option>
                                            <option value="1" <?PHP echo $selper1; ?> selected>Periode 1</option>
                                            <option value="2" <?PHP echo $selper2; ?>>Periode 2</option>
                                            <?PHP
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
                                                <input type='text' id='e_periode01' name='e_periode01' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $tgl1; ?>' data-inputmask="'mask': '99/99/9999'">
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            <div class='input-group date' id='mytgl02<?PHP echo $nmperiode; ?>'>
                                                <input type='text' id='e_periode02' name='e_periode02' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $tgl2; ?>' data-inputmask="'mask': '99/99/9999'">
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kunjungan Ke Kota <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <textarea class='form-control' id='e_ket' name='e_ket' rows='3' placeholder='Kunjungan Ke Kota'><?PHP echo $keterangan; ?></textarea>
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
                                        <input type="button" class='btn btn-warning btn-xs'  name="btn_refresh" id="btn_refresh" onclick="CariAtasan('spv')" value="Refresh Atasan.."><!--refresh_atasan()-->
                                    </div>
                                </div>
                                
                        <div id="div_atasan">
                                
                                <!-- Appove SPV / AM -->
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        SPV / AM <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_atasan' name='e_atasan' onchange="ShowAtasanDM('e_atasan')">
                                            <?PHP
                                            if ($_GET['act']=="editdata"){
                                                if ($_SESSION['DIVISI']=="OTC"){
                                                    $query ="select karyawanid karyawanId, nama from hrd.karyawan where divisiId='OTC' AND (aktif='Y') "
                                                            . " AND karyawanId NOT IN (select IFNULL(karyawanId,'') FROM dbmaster.t_karyawanadmin) ";
                                                }else{
                                                    $karyawan=$idajukan;
                                                    $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatanspv as a WHERE CONCAT(a.divisiid, a.icabangid) in 
                                                        (select CONCAT(b.divisiid, b.icabangid) 
                                                        from MKT.imr0 b where b.karyawanid='$karyawan')";
                                                }
                                                $query .=" order by nama, karyawanId";
                                                
                                                $tampil = mysqli_query($cnmy, $query);
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
                                            if ($_SESSION['DIVISI']=="OTC"){
                                                $query ="select karyawanid karyawanId, nama from hrd.karyawan where divisiId='OTC' AND (aktif='Y') "
                                                        . " AND karyawanId NOT IN (select IFNULL(karyawanId,'') FROM dbmaster.t_karyawanadmin) ";
                                            }else{

                                                if ($lvlpengajuan=="FF2")
                                                    $karyawan=$idajukan;
                                                else
                                                    $karyawan=$patasan1;

                                                $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatandm as a WHERE CONCAT(a.icabangid) in 
                                                    (select CONCAT(b.icabangid) 
                                                    from MKT.ispv0 b where b.karyawanid='$karyawan')";
                                            }
                                            $query .=" order by nama, karyawanId";
                                            $tampil = mysqli_query($cnmy, $query);
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
                                            
                                            if ($_SESSION['DIVISI']=="OTC"){
                                                $query ="select karyawanid karyawanId, nama from hrd.karyawan where divisiId='OTC' AND (aktif='Y') "
                                                        . " AND karyawanId NOT IN (select IFNULL(karyawanId,'') FROM dbmaster.t_karyawanadmin) ";
                                            }else{
                                            
                                                if ($lvlpengajuan=="FF3")
                                                    $karyawan=$idajukan;
                                                else
                                                    $karyawan=$patasan2;

                                                $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatansm as a WHERE CONCAT(a.icabangid) in 
                                                    (select CONCAT(b.icabangid) 
                                                    from MKT.idm0 b where b.karyawanid='$karyawan')";
                                            }
                                            
                                            $query .=" order by nama, karyawanId";
                                            $tampil = mysqli_query($cnmy, $query);
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
                                
                                <!-- GSM -->
                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>NSM <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='e_atasan4' name='e_atasan4' onchange="">
                                            <?PHP
                                            $query = "select DISTINCT karyawanId, nama from dbmaster.v_karyawan_all where rank=2 and IFNULL(tglkeluar,'0000-00-00')='0000-00-00' ";
                                            $query .=" order by nama, karyawanId";
                                            $tampil = mysqli_query($cnmy, $query);
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
                                
                        </div>
                                
                                <div hidden class='form-group' <?PHP echo $stykry2; ?> id='divketca'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Saldo CA (Rp.) <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_casaldo' name='e_casaldo' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $totalca; ?>' readonly><br/>
                                        <input type='hidden' id='e_ketca' name='e_ketca' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ketca; ?>' readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
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
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type="button" class='btn btn-success btn-xs' name="btn_viewuc" id="btn_viewuc" value="Lihat Data Kunjungan" 
                                               onclick="window.open('<?PHP echo "eksekusi3.php?module=$_GET[module]&iprint=kunjungan"; ?>', 'winpopup', 
                                                    'toolbar=no,statusbar=no,menubar=no,resizable=yes,scrollbars=yes,width=800,height=400');" />
                                        <!--
                                        <button class='btn btn-success btn-xs' type="submit" formtarget="_blank" onclick="ShowDataKunjungan()">Lihat Data Kunjungan</button>
                                        -->
                                    </div>
                                </div>
                                
                                <div id="divdata_uc">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah UC <span class='required'></span></label>
                                        <div class='col-md-4'>
                                            <input type='text' id='e_uctotal' name='e_uctotal' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlahhr_ucinput; ?>' readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah Hotel yg sudah input<span class='required'></span></label>
                                        <div class='col-md-4'>
                                            <input type='hidden' id='e_ucinput_e' name='e_ucinput_e' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlahhr_blinput; ?>' readonly>
                                            <input type='hidden' id='e_ucinput_i' name='e_ucinput_i' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlahhr_bledit; ?>' readonly>
                                            <input type='text' id='e_ucinput' name='e_ucinput' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjumlahhr_blinput; ?>' readonly>
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
                                        include "module/mod_br_entrybrluarkota/inputdetailmobile.php";
                                    ?>
                                </div>
                            <?PHP }else{
                                include "module/mod_br_entrybrluarkota/inputdetail.php";
                            }
                            ?>
                            
                        </div>
                    </div>
                    
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                            if (empty($sudahapv)) {
                                if (($_GET['act']=="editdata") AND (!isset($_GET['ca']))) {
                                    ?><button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button><?PHP
                                }else{
                                echo "<div class='col-sm-5'>";
                                include "module/mod_br_entrybrluarkota/ttd_biayaluarkota.php";
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
        
        
        <?PHP
        if ($_GET['act']=="tambahbaru" AND $_SESSION['LVLPOSISI'] !="FF1" AND $_SESSION['LVLPOSISI'] !="FF2" AND $_SESSION['LVLPOSISI'] !="FF3" ){
            //include "module/mod_br_entrybrluarkota/displaydata.php";
        }
        ?>
        
        
    </div>
    <!--end row-->
</div>

    
    
<script>
    function TampilkanDataKaryawan(){
        var eidkar =document.getElementById('e_idkaryawan').value;
		
        if (eidkar=="0000002200") {
            $.ajax({
                type:"post",
                url:"module/mod_br_entrybrcash/cariatasan.php?module=caridatadivisicabangkrynoneotc",
                data:"ukar="+eidkar,
                success:function(data){
                    $("#div_karyawan").html(data);
                }
            });
            
            kry_none_.style.display = 'block';
        }else{
            kry_none_.style.display = 'none';
		
			$.ajax({
				type:"post",
				url:"module/mod_br_entrybrcash/cariatasan.php?module=caridatadivisicabang",
				data:"ukar="+eidkar,
				success:function(data){
					$("#div_karyawan").html(data);
					CariAtasan('');
				}
			});
			
		}
		
    }
    
    function TampilkanArea() {
        var eidcab =document.getElementById('e_idcabang').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/cariatasan.php?module=cariarecabang",
            data:"ucab="+eidcab,
            success:function(data){
                $("#e_idarea").html(data);
				$("#cb_idarea_").html(data);
            }
        });
    }
    
    function CariAtasan(status) {
        var eidkar =document.getElementById('e_idkaryawan').value;
        var eidjbt =document.getElementById('e_jabatan').value;
        var ekrynone =document.getElementById('e_stskaryawan').value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/cariatasan.php?module=cariatasan",
            data:"ustatus="+ekrynone+"&ukar="+eidkar+"&ujbt="+eidjbt,
            success:function(data){
                $("#div_atasan").html(data);
            }
        });
    }
    
</script>


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
        //BARU
        Kosongkan();
        TampilkanDataKaryawan();
        return false;
        //END BARU
        
        if (act =='input') {
            var icar = karyawan;
        }else{
            var icar = document.getElementById(karyawan).value;
        }
        if (icar != "") {
            $.ajax({
                type:"post",
                url:"module/mod_br_entrybrcash/viewdatams.php?module=caridivisi",
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
        showKendaraan();
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
            url:"config/viewdata2ms.php?module=viewdataareakaryawanbylevel",
            data:"uselinc="+selinc+"&umr="+icar+"&udivisi="+edivisi+"&ulevel="+elevel,
            success:function(data){
                $("#"+earea).html(data);
            }
        });
    }
    
    function showDivisi(icar, ijbt, irank, idivisi) {
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdatams.php?module=viewdatadivisi",
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
            url:"module/mod_br_entrybrcash/viewdatams.php?module=viewdataatasanspv",
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
            url:"module/mod_br_entrybrcash/viewdatams.php?module=viewdataatasandm",
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
            url:"module/mod_br_entrybrcash/viewdatams.php?module=viewdataatasansm",
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
            url:"module/mod_br_entrybrcash/viewdatams.php?module=viewdatakendaraan",
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
            url:"module/mod_br_entrybrcash/viewdatams.php?module=getperiode",
            data:"ubulan="+ibulan+"&ukode="+ikode,
            success:function(data){
                var arr_date = data.split(",");
                document.getElementById('e_periode01').value=arr_date[0];
                document.getElementById('e_periode02').value=arr_date[1];
            }
        });
    }

    function disp_confirm(pText_,ket)  {
        findHitungHariUC();
        
        setTimeout(function () {
            disp_confirm_ext(pText_,ket)
        }, 200);
        
    }
    
    function disp_confirm_ext(pText_, ket)  {
        
        var newchar = '';
        var aq_sdh_input = document.getElementById('e_ucinput').value;
        var adainputuc = document.getElementById('e_ucinput_i').value;
        var aq_totuc = document.getElementById('e_uctotal').value;

        aq_sdh_input = aq_sdh_input.split(',').join(newchar);
        adainputuc = adainputuc.split(',').join(newchar);
        aq_totuc = aq_totuc.split(',').join(newchar);

        if (aq_sdh_input=="") aq_sdh_input="0";
        if (adainputuc=="") adainputuc="0";
        if (aq_totuc=="") aq_totuc="0";

        if (adainputuc=="0") {
        }else{
            if (aq_totuc=="0") {
                alert("Anda belum mengisi Form UC...\n\
Untuk mengisi HOTEL form cuti harus diisi terlebih dahulu."); return false;
            }else{
                if (parseInt(aq_sdh_input)>parseInt(aq_totuc)) {
                    alert("Jumlah hari (HOTEL), melebihi jumlah UC..."); return false;
                }
            }
        }
        
        
            
        var ekar =document.getElementById('e_idkaryawan').value;
        var eperi =document.getElementById('e_periode').value;
        var ediv =document.getElementById('cb_divisi').value;
        var elvlpos =document.getElementById('e_lvl').value;
        var eatasan1 =document.getElementById('e_atasan').value;
        var eatasan2 =document.getElementById('e_atasan2').value;
        var eatasan3 =document.getElementById('e_atasan3').value;
        var etotsem =document.getElementById('e_totalsemua').value;
        var eidca =document.getElementById('e_idkaryawan_ca').value;
        var esaldoca =document.getElementById('e_casaldo').value;
        var chkdari=$("#chk_dari").is(":checked");
        
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
                        //alert("SPV / AM masih kosong....");
                        //return 0;
                    }
                }

                if (elvlpos=="FF1" || elvlpos=="FF2") {
                    if (eatasan2==""){
                        //alert("DM masih kosong....");
                        //return 0;
                    }
                }

                if (elvlpos=="FF1" || elvlpos=="FF2" || elvlpos=="FF3") {
                    if (eatasan3==""){
						
						
                        if (ekar=="0000002297") {

                        }else{
							
							alert("SM masih kosong....");
							return 0;
							
						}
						
						
                    }
                }

            }
        
        }

        if (parseInt(etotsem)==0) {
            alert("Total Rupiah Masih Kosong....");
            return 0;
        }

        if (chkdari==true){
            var isaldo = esaldoca.replace(/\,/g,'');
            var itotal = etotsem.replace(/\,/g,'');

            isaldo=parseInt(isaldo,10);
            itotal=parseInt(itotal,10);


            if (itotal>isaldo) {
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

                document.getElementById("demo-form2").action = "module/mod_br_entrybrluarkota/aksi_entrybrluarkota.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
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
    function myShowHide() {
        var xchec=$("#chk_dari").is(":checked");
        var e_kry = document.getElementById("divkry1");
        var e_kryca = document.getElementById("divkry2");
        var e_ketca = document.getElementById("divketca");
        if (xchec==false) {
            e_kry.style.display = "block";
            e_kryca.style.display = "none";
            e_ketca.style.display = "none";
            refresh_ca();
        }else{
            e_kry.style.display = "none";
            e_kryca.style.display = "block";
            e_ketca.style.display = "block";
            refresh_ca();
        }


    }
    
    function refresh_ca() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");

        document.getElementById("demo-form2").action = "module/mod_br_entrybrluarkota/refreshca.php?module="+module+"&act=editdata"+"&idmenu="+idmenu;
        document.getElementById("demo-form2").submit();
        
        return 1;
    }
</script>

<script>
    $(document).ready(function() {
        <?PHP if (empty($idarea)) { ?>
            var selinc = "";
        <?PHP }else{ ?>
            var selinc = <?PHP echo $idarea; ?>;
        <?PHP }
        
		
        if ($_GET['act']=="tambahbaru"){ ?>
        
			showDataKaryawan('input', '<?PHP echo $idajukan; ?>');
        
        <?PHP
            }else{
                if ($_SESSION['DIVISI']=="OTC") {
        ?>
                    var ncar = document.getElementById('e_idkaryawan').value;
                    var inonekry = "2200";
                    var iidcar = parseFloat(ncar);
                    if (iidcar==inonekry) {
                        kry_none_.style.display = 'block';
                        
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_entrybrcash/cariatasan.php?module=caridatadivisicabangkrynoneotc",
                            data:"ukar=0000002200",
                            success:function(data){
                                $("#div_karyawan").html(data);
                            }
                        });
                        CariAtasanKaryawanKontrak();
                    }else{
                        kry_none_.style.display = 'none';
                    }
                    
        <?PHP        
                }
            }
        ?>
		
		
		
            
        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
    });
    
    function hit_total(pNilai_,pQty_,pTotal_, pNid) {
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
        
        if (pNid=="21") {
            findHitungHariUC();
        }
        
    }
    
    function findHitungHariUC() {
        //e_uctotal, e_ucinput_e, e_ucinput
        //alert(pNilai_); return false;
        var newchar = '';
        var aq1 = document.getElementById('e_qty1').value;
        var aq2 = document.getElementById('e_qty2').value;
        var aq3 = document.getElementById('e_qty3').value;
        var aq4 = document.getElementById('e_qty4').value;
        
        var aq_sdh_input = document.getElementById('e_ucinput_e').value;
        var aq_totuc = document.getElementById('e_uctotal').value;
        
        aq1 = aq1.split(',').join(newchar);
        aq2 = aq2.split(',').join(newchar);
        aq3 = aq3.split(',').join(newchar);
        aq4 = aq4.split(',').join(newchar);
        aq_sdh_input = aq_sdh_input.split(',').join(newchar);
        aq_totuc = aq_totuc.split(',').join(newchar);
        
        if (aq1 === "") aq1=0; if (aq2 === "") aq2=0;
        if (aq3 === "") aq3=0; if (aq4 === "") aq4=0;
        if (aq_sdh_input === "") aq_sdh_input=0;
        if (aq_totuc === "") aq_totuc=0;
        
        if (parseInt(aq_sdh_input)>parseInt(aq_totuc)) {
            //alert("Jumlah hari, melebihi jumlah UC..."); return false;
        }
        
        var itotuc="0";
        var itotuc_i="0";
        
        itotuc =parseInt(aq1)+parseInt(aq2)+parseInt(aq3)+parseInt(aq4)+parseInt(aq_sdh_input);
        itotuc_i =parseInt(aq1)+parseInt(aq2)+parseInt(aq3)+parseInt(aq4);
        document.getElementById('e_ucinput').value=itotuc;
        document.getElementById('e_ucinput_i').value=itotuc_i;
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
        
        a1 = a1.split(',').join(newchar);
        a2 = a2.split(',').join(newchar);
        a3 = a3.split(',').join(newchar);
        a4 = a4.split(',').join(newchar);
        a5 = a5.split(',').join(newchar);
        a6 = a6.split(',').join(newchar);
        a7 = a7.split(',').join(newchar);
        a8 = a8.split(',').join(newchar);
        a9 = a9.split(',').join(newchar);
        if (a1 === "") a1=0; if (a2 === "") a2=0; if (a3 === "") a3=0; if (a4 === "") a4=0;
        if (a5 === "") a5=0; if (a6 === "") a6=0; if (a7 === "") a7=0; if (a8 === "") a8=0;
        if (a9 === "") a9=0;
        
        tot =parseInt(a1)+parseInt(a2)+parseInt(a3)+parseInt(a4)+parseInt(a5)+parseInt(a6)
            +parseInt(a7)+parseInt(a8)+parseInt(a9);
        document.getElementById('e_totalsemua').value = tot;
    }

    function ShowDataTotalUC() {
        var eid =document.getElementById('e_id').value;
        var ekar =document.getElementById('e_idkaryawan').value;
        var eperi01 =document.getElementById('e_periode01').value;
        var eperi02 =document.getElementById('e_periode02').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdatams.php?module=viewdatajumlahuc",
            data:"uid="+eid+"&ukar="+ekar+"&uperi01="+eperi01+"&uperi02="+eperi02,
            success:function(data){
                $("#divdata_uc").html(data);
            }
        });
        
    }
    
    function ShowDataKunjungan() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");

        document.getElementById("demo-form2").action = "eksekusi3.php?module="+module+"&iprint=kunjungan";
        document.getElementById("demo-form2").submit();
        return 1;
    }
</script>

<script>
    function show_caritanggal(skey)  {
        var eperi01 =document.getElementById('e_periode01').value;
        var eperi02 =document.getElementById('e_periode02').value;
        
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/viewdatams.php?module=caribulanyangbeda",
            data:"skey="+skey+"&uperi01="+eperi01+"&uperi02="+eperi02,
            success:function(data){
                if (data=="bedaperiode") {
                    if (skey=="1") {
                        document.getElementById('e_periode02').value=document.getElementById('e_periode01').value;
                    }else{
                        document.getElementById('e_periode01').value=document.getElementById('e_periode02').value;
                    }
                }
            }
        });
        
        setTimeout(function () {
            ShowDataTotalUC();
        }, 200);
        
    }
    
    $(document).ready(function() {
        $('#mytgl01').on('change dp.change', function(e){
            show_caritanggal('1');
        });
        $('#mytgl02').on('change dp.change', function(e){
            show_caritanggal('2');
        });
        
        
        $('#e_bulan').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'MM yy',
            <?PHP
            if ($_SESSION['DIVISI']!="OTC") {
                if ($_GET['act']=="editdata") {
                ?>
                    minDate: '0M',
                <?PHP
                }else{
                ?>
                    minDate: '0M',
                <?PHP
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



<style>
    .infoCari{padding:5px;margin-bottom: 5px; cursor: pointer;}
    .infoCari b{color:#555555;}

    #myDivSearching, #myDivSearching1, #myDivSearching2, #myDivSearching3, #myDivSearching4, #myDivSearching5, #myDivSearching6, #myDivSearching7, #myDivSearching8, #myDivSearching9, #myDivSearching10,
    #myDivSearching11, #myDivSearching12, #myDivSearching13, #myDivSearching14, #myDivSearching15,
        #myDivSearchingObt1, #myDivSearchingObt2, #myDivSearchingObt3, #myDivSearchingObt4, #myDivSearchingObt5,
        #myDivSearchingObt6, #myDivSearchingObt7, #myDivSearchingObt8, #myDivSearchingObt9, #myDivSearchingObt10 {
        position: absolute;background: #fff;box-shadow: 0px 3px 5px #555555; z-index:100; color:#000;
        width: 350px; padding-left: 0px;
    }

    #search-form{list-style:none;margin-left:-30px;}
    #search-form li{padding: 5px 10px 5px 0px; background: #f0f0f0; border-bottom: #bbb9b9 1px solid; padding-left: 5px;}

    #search-form li:hover{background:#ece3d2;cursor: pointer;}
</style>

<script>
    function cariFormData(str, idnya, myDivForm, cModule){
        $("#"+str).keyup(function(){
            $.ajax({
            type: "POST",
            url: "js/formpencarian/formsearch_eth.php?module="+cModule+"&myidform="+str+"&idnya="+idnya+"&myDivForm="+myDivForm,
            data:'keyword='+$(this).val(),
            beforeSend: function(){
                    $("#"+str).css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
            },
            success: function(data){
                    $("#"+myDivForm).show();
                    $("#"+myDivForm).html(data);
                    $("#"+str).css("background","#FFF");
            }
            });
        });
    }
    function selectDataFormSearch(val) {
        var nmid = val.split("|");
        $("#"+nmid[2]).hide();
        $("#e_kdkrynone").val(nmid[3]);
        $("#e_nmkrynone").val(nmid[4]);
        $("#e_nmkrynone2").val(nmid[4]);
        //alert(nmid[3]);
        CariAtasanKaryawanKontrak();
    }

    function HideDataFormSearch(val) {
        var nmid = val.split("|");
        $("#"+nmid[1]).val(nmid[4]);
        $("#"+nmid[2]).hide();
        CariAtasanKaryawanKontrak();
    }

    function checkkey(){
        if(event.keyCode==27){
            //put what you want here...
            $("#myDivSearching2").hide();
            //window.alert("Escape key pressed!");
        }
    }
    
    function CariAtasanKaryawanKontrak() {
        var sid = document.getElementById('e_kdkrynone').value;
        var snmlama = document.getElementById('e_nmkrynone2').value;
        var snmbaru = document.getElementById('e_nmkrynone').value;
        //alert(sid+", "+snmlama+", "+snmbaru); return false;
        $.ajax({
            type:"post",
            url:"module/mod_br_entrybrcash/cariatasan.php?module=cariatasankaryawankontrak",
            data:"uidkontrak="+sid+"&unmlama="+snmlama+"&unmbaru="+snmbaru,
            success:function(data){
                $("#div_atasan").html(data);
            }
        });
    }
</script>