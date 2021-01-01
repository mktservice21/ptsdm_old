<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<?PHP
include "config/koneksimysqli.php";
$idajukan = $_SESSION['IDCARD'];
$lvlpengajuan = $_SESSION['LVLPOSISI'];
$idnya="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d F Y', strtotime($hari_ini));
$namakaryawan="";
$jabatanid="";
$divisi="";
$idcabang="";
$idarea="";
$atasanid="";
$atasanidspv="";
$atasaniddm="";
$atasanidsm="";
$atasanidgsm="";
$region="";
$idcab="";
$region="";
$darikaryawan = "";
$bank = "";
$norekening = "";

$pdivisi1 = "";
$pdivisi2 = "";
$pdivisi3 = "";

$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    $hanyasatukaryawan = "Y";
    
    $edit = mysqli_query($cnmy, "SELECT a.*, b.nama FROM dbmaster.t_karyawan_posisi a LEFT JOIN hrd.karyawan b on a.karyawanId=b.karyawanId WHERE a.karyawanId='$_GET[id]'");
    $ketemu = mysqli_num_rows($edit);
    if ($ketemu==0) {
        $darikaryawan = "Y";
        $edit = mysqli_query($cnmy, "SELECT * FROM hrd.karyawan WHERE karyawanId='$_GET[id]'");
    }
    $r    = mysqli_fetch_array($edit);
    $idnya = $r['karyawanId'];
    $namakaryawan =$r['nama'];//getfield("select nama as lcfields from hrd.karyawan where karyawanId='$idnya'");
    $divisi = $r['divisiId'];
    $idcabang = $r['iCabangId'];
    $idarea = $r['areaId'];
    $jabatanid = $r['jabatanId'];
    $atasanid = $r['atasanId'];
    
    if (isset($r['spv'])) $atasanidspv = $r['spv'];
    if (isset($r['dm'])) $atasaniddm = $r['dm'];
    if (isset($r['sm'])) $atasanidsm = $r['sm'];
    if (isset($r['gsm'])) $atasanidgsm = $r['gsm'];
    
    if (isset($r['b_bank'])) $bank = $r['b_bank'];
    if (isset($r['b_norek'])) $norekening = $r['b_norek'];
    
    if (isset($r['divisi1'])) $pdivisi1 = $r['divisi1'];
    if (isset($r['divisi2'])) $pdivisi2 = $r['divisi2'];
    if (isset($r['divisi3'])) $pdivisi3 = $r['divisi3'];
    
    
    if ($darikaryawan != "Y") {
        $region = $r['region'];
        $idcab = $r['IDCAB'];
    }
    
    $nonaktif="N";
    if ($darikaryawan=="Y"){
        if ($r['AKTIF']=="Y") $nonaktif="";
    }else{
        if ($r['aktif']=="Y") $nonaktif="";
    }
        
    $hanyadmin = trim(getfield("select karyawanId as lcfields from dbmaster.t_karyawanadmin where karyawanId='$idnya'"));
    
    if ($jabatanid=="15" AND empty($atasanidspv)){
        //$idcabang = trim(getfieldit("select icabangid as lcfields from MKT.imr0 where karyawanid='$idnya' LIMIT 1"));
        //$idarea = trim(getfieldit("select areaid as lcfields from MKT.imr0 where karyawanid='$idnya' AND icabangid='$idcabang' LIMIT 1"));
        //$atasanidspv = trim(getfieldit("select karyawanid as lcfields from MKT.ispv0 where areaid='$idarea' AND icabangid='$idcabang' LIMIT 1")); 
        //$atasaniddm = trim(getfieldit("select karyawanid as lcfields from MKT.idm0 where icabangid='$idcabang' LIMIT 1"));
        //$atasanidsm = trim(getfieldit("select karyawanid as lcfields from MKT.ism0 where icabangid='$idcabang' LIMIT 1"));
        
    }
}

$khusushidden="hidden";
$khusushiddenadmotc="hidden";
if ($_SESSION['GROUP']=="1") {$khusushidden=""; $khusushiddenadmotc="";}
if ($_SESSION['DIVISI']=="OTC") { $khusushiddenadmotc=""; }


$lblnmdm="DM";
$lblnmgsm="GSM";
if ($_SESSION['DIVISI']=="OTC") {
    $lblnmdm="AM";
    $lblnmgsm="HOS";    
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
                                        <input type='text' id='e_nama' name='e_nama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $namakaryawan; ?>' Readonly>
                                    </div>
                                </div>
                                

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_jabatan'>JABATAN <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_jabatan' name='e_jabatan' onchange="showDataKaryawan('tambahbaru', 'e_idkaryawan')">
                                            <?PHP
                                            $sql=mysqli_query($cnmy, "SELECT jabatanId, nama FROM hrd.jabatan order by jabatanId");
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_spv'>SPV <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_spv' name='e_spv' onchange="">
                                            <?PHP
                                            $query ="select karyawanid, nama from hrd.karyawan where 1=1 "
                                                    . " AND (aktif='Y' OR karyawanid='$atasanidspv') ";
                                            if ($_SESSION['DIVISI']=="OTC"){
                                                $query .=" AND divisiid ='OTC' ";
                                                $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                            }else{
                                                $query .=" AND jabatanid in ('10', '18')";
                                            }
                                            $query .=" ORDER BY nama";
                                            $sql=mysqli_query($cnmy, $query);
                                            echo "<option value=''>-- Pilihan --</option>";
                                            while ($Xt=mysqli_fetch_array($sql)){
                                                $xid=$Xt['karyawanid'];
                                                $xnama=$Xt['nama'];
                                                
                                                if ($xid==$atasanidspv)
                                                    echo "<option value='$xid' selected>$xnama</option>";
                                                else
                                                    echo "<option value='$xid'>$xnama</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><?PHP echo $lblnmdm; ?> <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_dm' name='e_dm' onchange="">
                                            <?PHP
                                            //PilihKaryawanAktif("", "-- Pilihan --", $atasaniddm, "Y", $_SESSION['STSADMIN'], "", "", "Y", $_SESSION['IDCARD'], "", "", "", "", "");
                                            ?>
                                            <?PHP
                                            $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$atasaniddm') ";                                            
                                            if ($_SESSION['DIVISI']=="OTC"){
                                                $query .=" AND divisiid ='OTC' ";
                                                $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                            }else{
                                                $query .=" AND jabatanid in ('08')";
                                            }
                                            $query .=" ORDER BY nama";
                                            
                                            $sql=mysqli_query($cnmy, $query);
                                            echo "<option value=''>-- Pilihan --</option>";
                                            while ($Xt=mysqli_fetch_array($sql)){
                                                $xid=$Xt['karyawanid'];
                                                $xnama=$Xt['nama'];
                                                
                                                if ($xid==$atasaniddm)
                                                    echo "<option value='$xid' selected>$xnama</option>";
                                                else
                                                    echo "<option value='$xid'>$xnama</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_sm' name='e_sm' onchange="">
                                            <?PHP
                                            //PilihKaryawanAktif("", "-- Pilihan --", $atasanidsm, "Y", $_SESSION['STSADMIN'], "", "", "Y", $_SESSION['IDCARD'], "", "", "", "", "");
                                            ?>
                                            <?PHP
                                            $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$atasanidsm')";
                                            if ($_SESSION['DIVISI']=="OTC"){
                                                $query .=" AND divisiid ='OTC' ";
                                                $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                                //$query .=" And jabatanId in (select distinct jabatanId from hrd.jabatan WHERE rank='03')";
                                            }else{
                                                $query .=" AND jabatanid in ('20')";
                                            }
                                            $sql=mysqli_query($cnmy, $query);
                                            echo "<option value=''>-- Pilihan --</option>";
                                            while ($Xt=mysqli_fetch_array($sql)){
                                                $xid=$Xt['karyawanid'];
                                                $xnama=$Xt['nama'];
                                                
                                                if ($xid==$atasanidsm)
                                                    echo "<option value='$xid' selected>$xnama</option>";
                                                else
                                                    echo "<option value='$xid'>$xnama</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><?PHP echo $lblnmgsm; ?> <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_gsm' name='e_gsm' onchange="">
                                            <?PHP
                                            //PilihKaryawanAktif("", "-- Pilihan --", $atasanidgsm, "Y", $_SESSION['STSADMIN'], "", "", "Y", $_SESSION['IDCARD'], "", "", "", "", "");
                                            ?>
                                            <?PHP
                                            $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$atasanidgsm')";
                                            if ($_SESSION['DIVISI']=="OTC"){
                                                $query .=" AND divisiid ='OTC' ";
                                                $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                                                $query .=" And jabatanId in (select distinct jabatanId from hrd.jabatan WHERE rank='02')";
                                            }else{
                                                $query .=" AND jabatanid in ('05')";
                                            }
                                            $sql=mysqli_query($cnmy, $query);
                                            echo "<option value=''>-- Pilihan --</option>";
                                            while ($Xt=mysqli_fetch_array($sql)){
                                                $xid=$Xt['karyawanid'];
                                                $xnama=$Xt['nama'];
                                                
                                                if ($xid==$atasanidgsm)
                                                    echo "<option value='$xid' selected>$xnama</option>";
                                                else
                                                    echo "<option value='$xid'>$xnama</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                
                                <div <?PHP echo $khusushiddenadmotc; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_atasan'>ATASAN <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_atasan' name='e_atasan' onchange="">
                                            <?PHP
                                            //PilihKaryawanAktif($konek, $adapilihan, $ygdipilih, $hanyaygaktif, $stsadim, $divisi, $lvlposisi, $tampilkanlevelbawahan, $karyawan, $jabatan, $region, $cabang, $area, $hanyasatu)
                                            PilihKaryawanAktif("", "-- Pilihan --", $atasanid, "Y", $_SESSION['STSADMIN'], "", "", "Y", $_SESSION['IDCARD'], "", "", "", "", "");
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div <?PHP echo $khusushidden; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idarea'>CABANG & AREA <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_idarea' name='e_idarea'>
                                        <?PHP
                                        if ($divisi=="OTC") {
                                            $sql=mysqli_query($cnmy, "select a.icabangid_o cabangid, a.areaid_o areaid, a.nama nama_area, c.nama nama_cabang from MKT.iarea_o a 
                                                JOIN MKT.icabang_o c on a.icabangid_o=c.icabangid_o
                                                WHERE a.aktif='Y' and c.aktif='Y' order by c.nama, a.nama");
                                        }else{
                                            $sql=mysqli_query($cnmy, "SELECT a.iCabangId cabangid, a.areaId areaid, a.Nama nama_area, 
                                                c.nama nama_cabang FROM MKT.iarea a JOIN MKT.icabang c on a.iCabangId=c.iCabangId
                                                WHERE a.aktif='Y' and c.aktif='Y' order by c.nama, a.Nama");
                                        }
                                        $ketemu= mysqli_num_rows($sql);
                                        echo "<option value=''>-- Pilihan --</option>";
                                        while ($Xt=mysqli_fetch_array($sql)){
                                            if ($Xt['cabangid'].$Xt['areaid']==$idcabang.$idarea)
                                                echo "<option value='$Xt[cabangid],$Xt[areaid]' selected>$Xt[nama_cabang] - $Xt[nama_area]</option>";
                                            else
                                                echo "<option value='$Xt[cabangid],$Xt[areaid]'>$Xt[nama_cabang] - $Xt[nama_area]</option>";
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $khusushidden; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>REGION <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_region' name='e_region'>
                                            <?PHP
                                            $regsel = "selected";$regselB = "";$regselT = "";
                                            if ($region=="B") { $regsel = "";$regselB = "selected";$regselT = ""; }
                                            if ($region=="T") { $regsel = "";$regselB = "";$regselT = "selected"; }
                                            echo "<option value='' $regsel>blank_</option>";
                                            echo "<option value='B' $regselB>BARAT</option>";
                                            echo "<option value='T' $regselT>TIMUR</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $khusushidden; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DIVISI <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="showDataArea('cb_divisi')">
                                        <?PHP
                                        
                                        $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' order by nama");
                                        $ketemu= mysqli_num_rows($sql);
                                        echo "<option value=''>-- Pilihan --</option>";
                                        while ($Xt=mysqli_fetch_array($sql)){
                                            if (($ketemu==1) OR ($Xt['divisiid']==$divisi))
                                                echo "<option value='$Xt[divisiid]' selected>$Xt[divisiid]</option>";
                                            else
                                                echo "<option value='$Xt[divisiid]'>$Xt[divisiid]</option>";
                                        }
                                        
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $khusushidden; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DIVISI 1 <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi1' name='cb_divisi1'>
                                            <?PHP
                                                $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' order by nama");
                                                $ketemu= mysqli_num_rows($sql);
                                                echo "<option value=''>-- Pilihan --</option>";
                                                while ($Xt=mysqli_fetch_array($sql)){
                                                    if (($Xt['divisiid']==$pdivisi1))
                                                        echo "<option value='$Xt[divisiid]' selected>$Xt[divisiid]</option>";
                                                    else
                                                        echo "<option value='$Xt[divisiid]'>$Xt[divisiid]</option>";
                                                }

                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $khusushidden; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DIVISI 2 <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi2' name='cb_divisi2'>
                                            <?PHP
                                                $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' order by nama");
                                                $ketemu= mysqli_num_rows($sql);
                                                echo "<option value=''>-- Pilihan --</option>";
                                                while ($Xt=mysqli_fetch_array($sql)){
                                                    if (($Xt['divisiid']==$pdivisi2))
                                                        echo "<option value='$Xt[divisiid]' selected>$Xt[divisiid]</option>";
                                                    else
                                                        echo "<option value='$Xt[divisiid]'>$Xt[divisiid]</option>";
                                                }

                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $khusushidden; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DIVISI 3 <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_divisi3' name='cb_divisi3'>
                                            <?PHP
                                                $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' order by nama");
                                                $ketemu= mysqli_num_rows($sql);
                                                echo "<option value=''>-- Pilihan --</option>";
                                                while ($Xt=mysqli_fetch_array($sql)){
                                                    if (($Xt['divisiid']==$pdivisi3))
                                                        echo "<option value='$Xt[divisiid]' selected>$Xt[divisiid]</option>";
                                                    else
                                                        echo "<option value='$Xt[divisiid]'>$Xt[divisiid]</option>";
                                                }

                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div <?PHP echo $khusushidden; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idcab'>ADMIN CABANG <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_idcab' name='e_idcab' onchange="">
                                            <?PHP
                                            $sql=mysqli_query($cnmy, "SELECT IDCAB, NAMA_CAB FROM dbmaster.sdm_admincabang order by IDCAB, NAMA_CAB");
                                            $ketemu= mysqli_num_rows($sql);
                                            echo "<option value=''>-- Pilihan --</option>";
                                            while ($Xt=mysqli_fetch_array($sql)){
                                                if ($Xt['IDCAB']==$idcab)
                                                    echo "<option value='$Xt[IDCAB]' selected>$Xt[IDCAB] - $Xt[NAMA_CAB]</option>";
                                                else
                                                    echo "<option value='$Xt[IDCAB]'>$Xt[IDCAB] - $Xt[NAMA_CAB]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div <?PHP echo $khusushiddenadmotc; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>BANK <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='e_bank' name='e_bank'>
                                            <?PHP
                                            $sql=mysqli_query($cnmy, "SELECT bankid, nama FROM hrd.br_bank order by bankid");
                                            $ketemu= mysqli_num_rows($sql);
                                            echo "<option value=''>-- Pilihan --</option>";
                                            while ($Xt=mysqli_fetch_array($sql)){
                                                if ($Xt['bankid']==$bank)
                                                    echo "<option value='$Xt[bankid]' selected>$Xt[nama]</option>";
                                                else{
                                                    if ($Xt['bankid']=="002" AND $_SESSION['DIVISI']=="OTC")
                                                        echo "<option value='$Xt[bankid]' selected>$Xt[nama]</option>";
                                                    else
                                                        echo "<option value='$Xt[bankid]'>$Xt[nama]</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div <?PHP echo $khusushiddenadmotc; ?> class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ACCOUNT NO <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_norek' name='e_norek' class='form-control col-md-7 col-xs-12' value='<?PHP echo $norekening; ?>'>
                                    </div>
                                </div>

                                <div <?PHP echo $khusushiddenadmotc; ?> class='form-group'>
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
    <!--end row-->
</div>

<script>
    function showDataArea(idivisi) {
        var idiv = document.getElementById(idivisi).value;
        $.ajax({
            type:"post",
            url:"module/lap_m_karyawan/viewdata.php?module=viewdataareadivisi",
            data:"udivisi="+idiv,
            success:function(data){
                $("#e_idarea").html(data);
            }
        });
    }
    
    function disp_confirm(pText_, ket)  {


        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                document.getElementById("demo-form2").action = "module/lap_m_karyawan/aksi_karyawan.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
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