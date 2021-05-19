<?php
session_start();
if ($_GET['module']=="caridivisi"){
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $pkaryawan=trim($_POST['umr']);
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
    }else{
        if ($_SESSION['DIVISIKHUSUS']!="Y")
            $pdivisi = getfieldcnit("select CONCAT(divisiId,',',divisiId2) as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");

        $cdivisi = explode(",", $pdivisi);
        $divisi = $cdivisi[0];
        if (empty($cdivisi[0])) {
            $divisi = $cdivisi[1];
        }
        $pjabatanid = getfieldcnit("select jabatanId as lcfields from hrd.karyawan where karyawanId='$pkaryawan'");
    }
    
    if (!empty($pjabatanid)) {
        $rank = getfieldcnit("select rank as lcfields from hrd.jabatan where jabatanId='$pjabatanid'");
        $lvlpos = getfieldcnit("select LEVELPOSISI as lcfields from dbmaster.jabatan_level where jabatanId='$pjabatanid'");
        $lvlpos=trim($lvlpos);
    }
    echo "$pkaryawan,$pjabatanid,$rank,$lvlpos,$divisi";
}elseif ($_GET['module']=="viewdatadivisi"){
    include "../../config/koneksimysqli.php";
    $jbt=(int)trim($_POST['ujbt']);
    $rank=trim($_POST['urank']);
    $kry=trim($_POST['umr']);
    $udivisi=trim($_POST['udivisi']);
    
    if (($jbt==5 OR $jbt==8 OR $jbt==10 OR $jbt==18 OR $jbt==20) AND $udivisi!="OTC") {
        echo "<option value='CAN' selected>CANARY</option>";
        exit;
    }
    if (empty($udivisi)) $udivisi = "HO";
    
    if ($rank=="05" OR $rank==5)
        $sql= mysqli_query($cnmy, "select distinct divisiid from MKT.imr0 where karyawanid='$kry'");
    elseif ($rank=="04" OR $rank==4)
        $sql= mysqli_query($cnmy, "select distinct divisiid from MKT.ispv0 where karyawanid='$kry'");
    else
        $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' order by nama");
    
    $ketemu= mysqli_num_rows($sql);
    if ($ketemu==0) {
        $sql=mysqli_query($cnmy, "SELECT DivProdId divisiid FROM MKT.divprod where br='Y' order by nama");
        $ketemu= mysqli_num_rows($sql);
    }
    
    if ($ketemu>=2 AND (int)$jbt==15) {
        echo "<option value='CAN' selected>CANARY</option>";
    }else{
        $cana="";
        echo "<option value=''>-- Pilihan --</option>";
        while ($Xt=mysqli_fetch_array($sql)){
            if ($Xt['divisiid']=="CAN") $cana="CAN";
            if ($ketemu==1)
                echo "<option value='$Xt[divisiid]' selected>$Xt[divisiid]</option>";
            else {
                if ($Xt['divisiid']==$udivisi)
                    echo "<option value='$Xt[divisiid]' selected>$Xt[divisiid]</option>";
                else
                    echo "<option value='$Xt[divisiid]'>$Xt[divisiid]</option>";
            }
        }
        if ((int)$rank!=5 OR $ketemu>=2){
            if ($cana=="")
                echo "<option value='CAN' selected>CANARY</option>";
        }
    }
}elseif ($_GET['module']=="viewdataatasanspv"){
    include "../../config/koneksimysqli.php";
    $karyawan=trim($_POST['umr']);
    $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatanspv as a WHERE CONCAT(a.divisiid, a.icabangid, a.areaid) in 
        (select CONCAT(b.divisiid, b.icabangid, b.areaid) 
        from MKT.imr0 b where b.karyawanid='$karyawan')";
    $query .=" order by nama, karyawanId";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value='' selected>blank_</option>";
    while($a=mysqli_fetch_array($tampil)){ 
        $sel="";
        if ($ketemu==1) $sel="selected";
        echo "<option value='$a[karyawanId]' $sel>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdataatasandm"){
    include "../../config/koneksimysqli.php";
    $karyawan=trim($_POST['umr']);
    $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatandm as a WHERE CONCAT(a.icabangid) in 
        (select CONCAT(b.icabangid) 
        from MKT.ispv0 b where b.karyawanid='$karyawan')";
    $query .=" order by nama, karyawanId";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    if ($ketemu==0) {
        $query = "select a.dm karyawanId, b.nama from dbmaster.t_karyawan_posisi as a JOIN hrd.karyawan b on a.dm=b.karyawanId WHERE a.karyawanId='$karyawan'";
        $tampil = mysqli_query($cnmy, $query);
        $ketemu = mysqli_num_rows($tampil);
    }
    echo "<option value='' selected>blank_</option>";
    while($a=mysqli_fetch_array($tampil)){
        $sel="";
        if ($ketemu==1) $sel="selected";
        echo "<option value='$a[karyawanId]' $sel>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdataatasansm"){
    include "../../config/koneksimysqli.php";
    $karyawan=trim($_POST['umr']);
    $query = "select distinct a.karyawanid karyawanId, nama from dbmaster.v_penempatansm as a WHERE CONCAT(a.icabangid) in 
        (select CONCAT(b.icabangid) 
        from MKT.idm0 b where b.karyawanid='$karyawan')";
    $query .=" order by nama, karyawanId";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value='' selected>blank_</option>";
    while($a=mysqli_fetch_array($tampil)){
        $sel="";
        if ($ketemu==1) $sel="selected";
        echo "<option value='$a[karyawanId]' $sel>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdatakendaraan"){
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $karyawan=trim($_POST['umr']);
    $filnopol="";
    $adakendaraan = getfieldcnit("select nopol as lcfields from dbmaster.t_kendaraan_pemakai where karyawanid='$karyawan' and stsnonaktif <> 'Y'");
    //if (!empty($adakendaraan))
        $filnopol=" AND nopol in (select distinct nopol from dbmaster.t_kendaraan_pemakai where karyawanid='$karyawan' and stsnonaktif <> 'Y')";
    
    $query = "select * from dbmaster.t_kendaraan WHERE 1=1 $filnopol ";
    $query .=" order by merk, tipe, nopol";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        if ($ketemu<=1)
            echo "<option value='$a[nopol]' selected>$a[nopol] - $a[merk] $a[tipe]</option>";
        else
            echo "<option value='$a[nopol]'>$a[nopol] - $a[merk] $a[tipe]</option>";
    }
}elseif ($_GET['module']=="getperiode"){
    $bulan = "01-".str_replace('/', '-', $_POST['ubulan']);
    if ($_POST['ukode']==1) {
        $periode1= date("Y-m-d", strtotime($bulan));
        $periode2= date("Y-m-15", strtotime($bulan));
    }elseif ($_POST['ukode']==2) {
        $periode1= date("Y-m-16", strtotime($bulan));
        $periode2= date("Y-m-t", strtotime($bulan));
    }
    $bln1=""; $bln2="";
    if (!empty($_POST['ukode'])) {
        $bln1= date("d/m/Y", strtotime($periode1));
        $bln2= date("d/m/Y", strtotime($periode2));
    }
    
    if ($_SESSION['DIVISI']=="OTC") {
        $bln1= date("01/m/Y", strtotime($periode1));
        $bln2= date("t/m/Y", strtotime($periode2));
    }
    
    echo "$bln1, $bln2";
}elseif ($_GET['module']=="viewdatacashadvance"){
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $karyawan=trim($_POST['umr']);
    $periode=trim($_POST['uperiode']);
    //$date1 = "01-".str_replace('/', '-', $periode);
    $pbulan =  date("Y-m", strtotime($periode));
    $query = "select * from dbmaster.t_ca0 WHERE karyawanid='$karyawan' AND stsnonaktif<>'Y' and DATE_FORMAT(periode, '%Y-%m')<='$pbulan' ";
    $query .=" order by tgl, idca";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    echo "<option value='' selected>blank_</option>";
    if ($ketemu>0) {
        while($a=mysqli_fetch_array($tampil)){
            $pperiode =  date("d F Y", strtotime($a['periode']));
            $idca=$a['idca'];
            $ca_jumlahrp=$a['jumlah'];
            $rutin_jumlahrp = getfieldcnit("select sum(jumlah) as lcfields from dbmaster.t_brrutin0 WHERE idca='$idca' AND stsnonaktif <> 'Y'");
            $sewa_jumlahrp = getfieldcnit("select sum(jumlah) as lcfields from dbmaster.t_sewa WHERE idca='$idca' AND stsnonaktif <> 'Y'");
            if (empty($rutin_jumlahrp)) $rutin_jumlahrp = 0;
            if (empty($sewa_jumlahrp)) $sewa_jumlahrp = 0;
            $saldoca = (double)$ca_jumlahrp-(double)$rutin_jumlahrp-(double)$sewa_jumlahrp;
            if ((double)$saldoca>0)
                echo "<option value='$idca' selected>$idca - $pperiode</option>";
        }
    }
}elseif ($_GET['module']=="getkodeperiode"){
    include "../../config/fungsi_sql.php";
    $mytglini="";
    $mytglini = getfield("select CURRENT_DATE as lcfields");
    if ($mytglini==0) $mytglini="";
    if (empty($mytglini)) $mytglini = date("Y-m-d");
    $tglini=date("Y-m", strtotime($mytglini));
    $hariiniserver=date("d", strtotime($mytglini));
    
    $periodeini=trim($_POST['ubulan']);
    $pbulan =  date("Y-m", strtotime($periodeini));
    if (empty($pbulan)) $Pbulan = date("Y-m");
    
    echo "<option value='' selected>-- Pilihan --</option>";
    if ($pbulan<$tglini){
        echo "<option value='2'>Periode 2</option>";
    }else{
        if ($pbulan==$tglini){
            if ((int)$hariiniserver > 20) {
                echo "<option value='2'>Periode 2</option>";
            }else{
                echo "<option value='1'>Periode 1</option>";
                echo "<option value='2'>Periode 2</option>";
            }
        }else{
            echo "<option value='1'>Periode 1</option>";
            echo "<option value='2'>Periode 2</option>";
        }
    }
}elseif ($_GET['module']=="viewdatajumlahuc"){
    
    include "../../config/koneksimysqli.php";
    $idklaim=$_POST['uid'];
    $idajukan=$_POST['ukar'];
    $pntgl01=$_POST['uperi01'];
    $pntgl02=$_POST['uperi02'];
    
    $pntgl01 = str_replace('/', '-', $pntgl01);
    $pntgl02 = str_replace('/', '-', $pntgl02);
    
    $blnpilihuc = date('Y-m', strtotime($pntgl01));
    $blnpilihuc02 = date('Y-m', strtotime($pntgl02));
    
    $ntgl01 = date('Y-m-d', strtotime($pntgl01));
    $ntgl02 = date('Y-m-d', strtotime($pntgl02));
    
    $query = "select jabatanId as jabatanid FROM hrd.karyawan WHERE karyawanId='$idajukan'";
    $tampilj= mysqli_query($cnmy, $query);
    $jrow= mysqli_fetch_array($tampilj);
    $pjabatanid=$jrow['jabatanid'];
    
    $fdata_uc="";
    if (!empty($idklaim)) $fdata_uc=" AND a.idrutin <>'$idklaim'";
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


    mysqli_close($cnmy);

    if ($blnpilihuc==$blnpilihuc02) {
    }else{
        $pjumlahhr_ucinput=0;
    }
    
if ($_SESSION['IDCARD']=="0000000329" OR $_SESSION['IDCARD']=="0000000962") {
    $pjumlahhr_ucinput=100;
    $pjumlahhr_blinput=0;
}

?>
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
<?PHP
}elseif ($_GET['module']=="caribulanyangbeda"){
    $pntgl01=$_POST['uperi01'];
    $pntgl02=$_POST['uperi02'];
    
    $pntgl01 = str_replace('/', '-', $pntgl01);
    $pntgl02 = str_replace('/', '-', $pntgl02);
    
    $blnpilihuc = date('Y-m', strtotime($pntgl01));
    $blnpilihuc02 = date('Y-m', strtotime($pntgl02));
    
    $ntgl01 = date('Ymd', strtotime($pntgl01));
    $ntgl02 = date('Ymd', strtotime($pntgl02));
    
    if ($blnpilihuc==$blnpilihuc02) {
        if ($ntgl01>$ntgl02) { echo "bedaperiode"; exit; }
        else exit;
    }else{
        echo "bedaperiode"; exit;
    }
    
}elseif ($_GET['module']=="xxx"){
    
}

?>
