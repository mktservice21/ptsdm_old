<?PHP
    session_start();
    
if ($_GET['module']=="cariatasan") {
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $iidkar=$_POST['ukar'];
    $iidjbt=$_POST['ujbt'];
    $istatus=$_POST['ustatus'];
    
    $adaspv="";
    $adadm="";
    $adasm="";
    $adagsm="";
    
    $adanmspv="";
    $adanmdm="";
    $adanmsm="";
    $adanmgsm="";
    
    $query = "select spv, dm, sm, gsm from dbmaster.t_karyawan_posisi WHERE karyawanId='$iidkar'";
    $tampilkan= mysqli_query($cnmy, $query);
    $ketemukan= mysqli_num_rows($tampilkan);
    if ($ketemukan>0) {
        $kr= mysqli_fetch_array($tampilkan);
        $adaspv=$kr['spv'];
        $adadm=$kr['dm'];
        $adasm=$kr['sm'];
        $adagsm=$kr['gsm'];
        
        $adanmspv = getfieldcnmy("select nama as lcfields from hrd.karyawan where karyawanId='$adaspv'");
        $adanmdm = getfieldcnmy("select nama as lcfields from hrd.karyawan where karyawanId='$adadm'");
        $adanmsm = getfieldcnmy("select nama as lcfields from hrd.karyawan where karyawanId='$adasm'");
        $adanmgsm = getfieldcnmy("select nama as lcfields from hrd.karyawan where karyawanId='$adagsm'");
    }
    
    $gsmhiden="hidden";
    if ($iidjbt=="20" OR (INT)$iidjbt==20) $gsmhiden="class='form-group'";
?>

    <!-- Appove SPV / AM -->
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='e_atasan' name='e_atasan'>
                <option value='' selected>blank_</option>
                <?PHP
                if (empty($istatus)) {
                    if (!empty($adaspv)) echo "<option value='$adaspv' selected>$adanmspv</option>";
                }else{
                    $query ="select karyawanid, nama from hrd.karyawan where jabatanid in ('10', '18') AND (aktif='Y') ORDER BY nama";
                    $sql=mysqli_query($cnmy, $query);
                    while ($Xt=mysqli_fetch_array($sql)){
                        $xid=$Xt['karyawanid'];
                        $xnama=$Xt['nama'];

                        if ($xid==$atasanidspv)
                            echo "<option value='$xid' selected>$xnama</option>";
                        else
                            echo "<option value='$xid'>$xnama</option>";
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
            <select class='form-control input-sm' id='e_atasan2' name='e_atasan2'>
                <option value='' selected>blank_</option>
                <?PHP
                if (empty($istatus)) {
                    if (!empty($adadm)) echo "<option value='$adadm' selected>$adanmdm</option>";
                }else{
                    $query ="select karyawanid, nama from hrd.karyawan where jabatanid in ('08') AND (aktif='Y') ORDER BY nama";
                    $sql=mysqli_query($cnmy, $query);
                    while ($Xt=mysqli_fetch_array($sql)){
                        $xid=$Xt['karyawanid'];
                        $xnama=$Xt['nama'];

                        if ($xid==$atasaniddm)
                            echo "<option value='$xid' selected>$xnama</option>";
                        else
                            echo "<option value='$xid'>$xnama</option>";
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
                <option value='' selected>blank_</option>
                <?PHP
                if (empty($istatus)) {
                    if (!empty($adasm)) echo "<option value='$adasm' selected>$adanmsm</option>";
                }else{
                    $query ="select karyawanid, nama from hrd.karyawan where jabatanid in ('20') AND (aktif='Y') ORDER BY nama";
                    $sql=mysqli_query($cnmy, $query);
                    while ($Xt=mysqli_fetch_array($sql)){
                        $xid=$Xt['karyawanid'];
                        $xnama=$Xt['nama'];

                        if ($xid==$atasanidsm)
                            echo "<option value='$xid' selected>$xnama</option>";
                        else
                            echo "<option value='$xid'>$xnama</option>";
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <!-- GSM -->
    <div hidden <?PHP echo $gsmhiden; ?>>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>GSM <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='e_atasan4' name='e_atasan4' onchange="">
                <option value='' selected>blank_</option>
                <?PHP
                if (empty($istatus)) {
                    if (!empty($adagsm)) echo "<option value='$adagsm' selected>$adanmgsm</option>";
                }else{
                    
                }
                ?>
            </select>
        </div>
    </div>

<?PHP
}elseif ($_GET['module']=="caridatadivisicabang") {

    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $pkaryawan=trim($_POST['ukar']);
    
    
    $karyawannone=$_SESSION['KRYNONE'];
    $pnmkrynone="";
    $pstskrynone="";
    $txtkrynone="hidden";
    if ($pkaryawan==$karyawannone) { $txtkrynone="class='form-group'"; $pstskrynone="NONE"; }
    
    
    $divisi = "";
    $pjabatanid = "";
    $rank = "";
    $lvlpos = "";
    $pdivisi = $_SESSION['DIVISI'];
    
    $cari = "select * from dbmaster.t_karyawan_posisi WHERE karyawanId='$pkaryawan'";
    $tampil = mysqli_query($cnmy, $cari);
    $ketemu = mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $j= mysqli_fetch_array($tampil);
        $divisi = $j['divisiId'];
        $pjabatanid = $j['jabatanId'];
        $pcabangnya=$j['iCabangId'];
        $pareanya=$j['areaId'];
    }else{
        
        $cari = "select jabatanId, iCabangId, areaId, divisiId, divisiId2, CONCAT(divisiId,',',divisiId2) as MyDivisi from dbmaster.karyawan WHERE karyawanId='$pkaryawan'";
        $tampilkan = mysqli_query($cnmy, $cari);
        $tp= mysqli_fetch_array($tampilkan);
        if ($_SESSION['DIVISIKHUSUS']!="Y") $pdivisi=$tp['MyDivisi'];
        
        $cdivisi = explode(",", $pdivisi);
        $divisi = $cdivisi[0];
        if (empty($cdivisi[0])) {
            if (isset($cdivisi[1])) $divisi = $cdivisi[1];
        }
        $pjabatanid=$tp['jabatanId'];
        
        $pcabangnya=$tp['iCabangId'];
        $pareanya=$tp['areaId'];
        
    }
    
    if (!empty($pjabatanid)) {
        $rank = getfieldcnmy("select rank as lcfields from hrd.jabatan where jabatanId='$pjabatanid'");
        $lvlpos = getfieldcnmy("select LEVELPOSISI as lcfields from dbmaster.jabatan_level where jabatanId='$pjabatanid'");
        $lvlpos=trim($lvlpos);
    }
    
    if (!empty($pcabangnya))
        $pnmcabangnya = getfieldcnmy("select nama as lcfields from MKT.icabang where iCabangId='$pcabangnya'");
    
    if (!empty($pcabangnya) AND !empty($pareanya))
        $pnmareanya = getfieldcnmy("select nama as lcfields from MKT.iarea where iCabangId='$pcabangnya' and areaId='$pareanya'");
    
    
    
    $txtkendaraannone="class='form-group'";
    if ($pjabatanid=="15" OR $pjabatanid=="38") { $txtkendaraannone="hidden"; }
    
?>
    
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
        <div class='col-xs-9'>
            <input type='hidden' id='e_stskaryawan' name='e_stskaryawan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pstskrynone; ?>' Readonly>
            <input type='hidden' id='e_jabatan' name='e_jabatan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pjabatanid; ?>' Readonly>
            <input type='hidden' id='e_rank' name='e_rank' class='form-control col-md-7 col-xs-12' value='<?PHP echo $rank; ?>' Readonly>
            <input type='hidden' id='e_lvl' name='e_lvl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $lvlpos; ?>' Readonly>
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
                if ($pkaryawan==$karyawannone) {
                    $sql=mysqli_query($cnmy, "SELECT iCabangId, nama FROM MKT.icabang where aktif='Y' order by nama");
                    while ($Xt=mysqli_fetch_array($sql)){
                        $didcab=$Xt['iCabangId'];
                        $dnmcab=$Xt['nama'];
                        echo "<option value='$didcab'>$dnmcab</option>";
                    }
                }else{
                    if (!empty($pcabangnya))
                        echo "<option value='$pcabangnya' selected>$pnmcabangnya</option>";
                }
                ?>
            </select>
        </div>
    </div>
    
    <div <?PHP echo $txtkrynone; ?>>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idarea'>Area <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='e_idarea' name='e_idarea'>
                <option value='' selected>blank_</option>
                <?PHP
                if ($pkaryawan==$karyawannone) {
                    
                }else{
                    if (!empty($pareanya))
                        echo "<option value='$pareanya' selected>$pnmareanya</option>";
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
                if ($pkaryawan==$karyawannone) {
                    $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' order by nama");
                    while ($Xt=mysqli_fetch_array($sql)){
                        $divisi=$Xt['divisiid'];
                        echo "<option value='$divisi'>$divisi</option>";
                    }
                }else{
                    echo "<option value=''>blank_</option>";
                    if (!empty($divisi)) echo "<option value='$divisi' selected>$divisi</option>";
                }
                ?>
            </select>
        </div>
    </div>



    <div <?PHP echo $txtkendaraannone; ?>>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Polisi Kendaraan <span class='required'></span></label>
        <div class='col-xs-5'>
            <select class='form-control input-sm' id='e_nopol' name='e_nopol'>
                <option value='' selected>blank_</option>
                <?PHP
                $filnopol=" AND nopol in (select distinct nopol from dbmaster.t_kendaraan_pemakai where karyawanid='$pkaryawan' and stsnonaktif <> 'Y')";
                $query = "select * from dbmaster.t_kendaraan WHERE 1=1 $filnopol ";
                $query .=" order by merk, tipe, nopol";
                $tampil = mysqli_query($cnmy, $query);
                $ketemu = mysqli_num_rows($tampil);
                while($a=mysqli_fetch_array($tampil)){
                    echo "<option value='$a[nopol]' selected>$a[nopol] - $a[merk] $a[tipe]</option>";
                }
                ?>
            </select>
        </div>
    </div>
    
    
<?PHP
}elseif ($_GET['module']=="cariarecabang") {
    include "../../config/koneksimysqli.php";
    echo "<option value=''>blank_</option>";
    $pidcabang=trim($_POST['ucab']);
    $sql=mysqli_query($cnmy, "SELECT areaid, nama FROM MKT.iarea where aktif='Y' and iCabangId='$pidcabang' order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        $didarea=$Xt['areaid'];
        $dnmarea=$Xt['nama'];
        echo "<option value='$didarea'>$dnmarea</option>";
    }
}
?>