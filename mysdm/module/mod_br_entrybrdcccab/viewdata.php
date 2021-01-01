<?php
date_default_timezone_set('Asia/Jakarta');
session_start();

$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewdatakaryawan") {
    include "../../config/koneksimysqli.php";
    $pidkaryawan=$_POST['uidkaryawan'];
    $pidcabang=$_POST['uidcabang'];
    
    $query = "select jabatanId from hrd.karyawan where karyawanId='$pidkaryawan'"; 	
    $result = mysqli_query($cnmy, $query);
    $row = mysqli_fetch_array($result);
    $pjabatanid = $row['jabatanId'];
    
    ?>

    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
        <div class='col-xs-9'>
            <input type='text' id='e_jabatanid' name='e_jabatanid' class='form-control col-md-7 col-xs-12' value="<?PHP echo $pjabatanid; ?>" Readonly>
        </div>
    </div>

    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>MR <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='cb_idmr' name='cb_idmr' onchange="ShowDataDataMR()">
                <?PHP
                    $query = "select a.karyawanId, a.nama from hrd.karyawan a "
                            . " LEFT JOIN dbmaster.t_karyawan_posisi b on a.karyawanId=b.karyawanId "
                            . " WHERE a.aktif='Y' ";
                    $query .=" AND a.karyawanId not in (select distinct karyawanid from dbmaster.t_karyawanadmin) ";
                    if ($pjabatanid=="15") $query .=" AND a.karyawanId='$pidkaryawan' "; // mr
                    elseif ($pjabatanid=="08") $query .=" AND b.dm='$pidkaryawan' "; // dm
                    elseif (($pjabatanid=="18") or ($pjabatanid=="10")) $query .=" AND b.spv='$pidkaryawan' "; // spv am
                    elseif ($pjabatanid=="20") $query .=" AND b.sm='$pidkaryawan' "; // sm
                    $query .=" ORDER BY 2,1";
                    $tampil = mysqli_query($cnmy, $query);
                    echo "<option value='' selected>-- Pilihan --</option>";
                    while ($rc=mysqli_fetch_array($tampil)){
                        $nkaryawanid=$rc['karyawanId'];
                        $nnamakry=$rc['nama'];
                        
                        echo "<option value='$nkaryawanid'>$nnamakry</option>";
                    }
                ?>
            </select>
            <?PHP //echo  $query; ?>
        </div>
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='cb_cabangpil' name='cb_cabangpil' onchange="">
              <?PHP
                $pcabangid="0000000001";
                $query = "SELECT distinct iCabangId as icabangid, nama from MKT.icabang where aktif='Y' order by nama";
                if ($pjabatanid=="15") {//mr
                    $query = "select distinct a.icabangid, b.nama from mkt.imr0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                }elseif ($pjabatanid=="08") {//dm
                    $query = "select distinct a.icabangid, b.nama from mkt.idm0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                }elseif ($pjabatanid=="18" OR $pjabatanid=="10") {
                    $query = "select distinct a.icabangid, b.nama from mkt.ispv0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                }elseif ($pjabatanid=="20") {//sm
                    $query = "select distinct a.icabangid, b.nama from mkt.ism0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
                }
                
                
                if ($pjabatanid=="15" OR $pjabatanid=="08" OR $pjabatanid=="18" OR $pjabatanid=="10" OR $pjabatanid=="20") {
                    $pcabangid="";
                }
                
                $tampil=mysqli_query($cnmy, $query);
                
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu==0) {
                    $query = "SELECT distinct iCabangId as icabangid, nama from MKT.icabang where aktif='Y' order by nama";
                    $tampil=mysqli_query($cnmy, $query);
                }
                
                while($a=mysqli_fetch_array($tampil)){
                    $nidcabang=$a['icabangid'];
                    $nnamacab=$a['nama'];
                    if ($nidcabang==$pcabangid)
                        echo "<option value='$nidcabang' selected>$nnamacab</option>";
                    else
                        echo "<option value='$nidcabang'>$nnamacab</option>";
                }
                ?>
            </select>
        </div>
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dokter <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='cb_iddokter' name='cb_iddokter' onchange="">
                <?PHP
                echo "<option value='' selected>-- Pilihan --</option>";
                ?>
            </select>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="ShowDataCOAKode()">
                <?PHP

                ?>
            </select>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode / COA <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='cb_kode' name='cb_kode' onchange="">
                <?PHP

                ?>
            </select>
        </div>
    </div>

    <?PHP
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatacabang") {
    include "../../config/koneksimysqli.php";
    $pidkaryawan=$_POST['uidkaryawan'];
    $pjabatanid = $_POST['ujabatanid'];
    
    $pcabangid="0000000001";
    $query = "SELECT distinct iCabangId as icabangid, nama from MKT.icabang where aktif='Y' order by nama";
    if ($pjabatanid=="15") {//mr
        $query = "select distinct a.icabangid, b.nama from mkt.imr0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
    }elseif ($pjabatanid=="08") {//dm
        $query = "select distinct a.icabangid, b.nama from mkt.idm0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
    }elseif ($pjabatanid=="18" OR $pjabatanid=="10") {
        $query = "select distinct a.icabangid, b.nama from mkt.ispv0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
    }elseif ($pjabatanid=="20") {//sm
        $query = "select distinct a.icabangid, b.nama from mkt.ism0 a JOIN mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$pidkaryawan' and b.aktif='Y'";
    }
    
    if ($pjabatanid=="15" OR $pjabatanid=="08" OR $pjabatanid=="18" OR $pjabatanid=="10" OR $pjabatanid=="20") {
        $pcabangid="";
    }
                
    $tampil=mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu==0) echo "<option value='' selected>-- Pilihan --</option>";
    while ($rc=mysqli_fetch_array($tampil)){
        $nidcabang=$rc['icabangid'];
        $nnamacab=$rc['nama'];
        if ($nidcabang==$pcabangid)
            echo "<option value='$nidcabang' selected>$nnamacab</option>";
        else
            echo "<option value='$nidcabang'>$nnamacab</option>";
    }
    mysqli_close($cnmy);    
}elseif ($pmodule=="viewdatadokter") {
    include "../../config/koneksimysqli.php";
    $pidkaryawan=$_POST['uidkaryawan'];
    $pmrid = $_POST['uidmr'];
    $pidcabang=$_POST['uidcabang'];
    $pidjbt=$_POST['ujabatanid'];
    if ($pidjbt=="20" OR $pidjbt=="05") {
        $filter_kry_dok=" and karyawan.karyawanId='$pmrid' ";
    }else{
        $filter_kry_dok=" and karyawan.karyawanId='$pidkaryawan' ";
    }
    
    
    //if ($pidcabang=="0000000001") {
        $query = "select distinct (mr_dokt.dokterId),CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
              from hrd.mr_dokt as mr_dokt 
              join hrd.dokter as dokter on mr_dokt.dokterId=dokter.dokterId
              where mr_dokt.aktif <> 'N' and dokter.nama<>''
              order by nama"; 
    //} else {
        $query = "select dokter.dokterId, CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
              FROM hrd.mr_dokt as mr_dokt 
              join hrd.karyawan as karyawan on mr_dokt.karyawanId=karyawan.karyawanId
              join hrd.dokter as dokter on mr_dokt.dokterId=dokter.dokterId
              where mr_dokt.aktif <> 'N' $filter_kry_dok and dokter.nama <> ''
              order by dokter.nama";
    //}
    $tampil=mysqli_query($cnmy, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($an=mysqli_fetch_array($tampil)){
        $ndokterid=$an['dokterId'];
        $ndokternm=$an['nama'];
        echo "<option value='$ndokterid'>$ndokternm</option>";
    }
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatadivisi") {
    include "../../config/koneksimysqli.php";
    $pidkaryawan=$_POST['uidkaryawan'];
    $pmrid = $_POST['uidmr'];
    $pjabatanid = $_POST['ujabatanid'];
    $pidcabang=$_POST['uidcabang'];
    
    $pdivisi="";
    if ($pidcabang=="0000000001") $pdivisi="HO";
    
    $query = "select DivProdId as divisi, nama from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTHER', 'CAN') order by 1,2";
    if ($pjabatanid=="15") {//mr
        $query = "select distinct a.divisiid as divisi, b.nama from MKT.imr0 a JOIN MKT.divprod b on a.divisiid=b.DivProdId where a.karyawanid='$pidkaryawan' ORDER BY 1,2;";
    }elseif ($pjabatanid=="18" OR $pjabatanid=="10") {
        $query = "select distinct a.divisiid as divisi, b.nama from MKT.ispv0 a JOIN MKT.divprod b on a.divisiid=b.DivProdId where a.karyawanid='$pidkaryawan' ORDER BY 1,2";
    }
    
    $tampil=mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu==0) {
        $query = "select DivProdId as divisi, nama from MKT.divprod WHERE br='Y' AND DivProdId NOT IN ('OTHER', 'CAN') order by 1,2";
        $tampil=mysqli_query($cnmy, $query);
    }
    
    //echo "<option value='' selected>-- Pilihan --</option>";
    while($an=mysqli_fetch_array($tampil)){
        $ndivisi=$an['divisi'];
        $nnama=$an['nama'];
        if ($ndivisi==$pdivisi)
            echo "<option value='$ndivisi' selected>$nnama</option>";
        else
            echo "<option value='$ndivisi'>$nnama</option>";
    }
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatacoakode") {
    include "../../config/koneksimysqli.php";
    $pdivisi=$_POST['udivisi'];
    
    
    $query="select a.COA4, a.NAMA4, c.DIVISI2 from dbmaster.coa_level4 a 
        JOIN dbmaster.coa_level3 b on a.COA3=b.COA3 JOIN dbmaster.coa_level2 c on b.COA2=c.COA2 WHERE 
        a.COA4 IN ('702-03', '701-03', '704-03', '703-03', '750-05') AND c.DIVISI2='$pdivisi'";//'702-02', '701-02', '704-02', '703-02', (ini DCC)
    $tampil=mysqli_query($cnmy, $query);
    //echo "<option value='' selected>-- Pilihan --</option>";
    while($an=mysqli_fetch_array($tampil)){
        $ncoa4=$an['COA4'];
        $nnama4=$an['NAMA4'];
        echo "<option value='$ncoa4'>$nnama4</option>";
    }
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdataatasan") {
    include "../../config/koneksimysqli.php";
    $pidkaryawan=$_POST['uidkaryawan'];
    $pjabatanid = $_POST['ujabatanid'];
    $ppilihsm=$pidkaryawan;
    
    ?>
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:green;">SPV/AM <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='cb_apvspv' name='cb_apvspv' onchange="">
                <?PHP
                $query = "select a.karyawanId karyawanid, b.nama from dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.karyawanId=b.karyawanId order by 2";
                if ($pjabatanid=="15") {
                    $query = "select a.spv karyawanid, b.nama from dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.spv=b.karyawanId WHERE a.karyawanid='$pidkaryawan' order by 2";
                }elseif ($pjabatanid=="08" OR $pjabatanid=="18" OR $pjabatanid=="10" OR $pjabatanid=="20") {
                    $query = "select karyawanId karyawanid, nama from hrd.karyawan WHERE karyawanId='$pidkaryawan'";
                }
                $tampil=mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($pjabatanid=="15") {
                    if ($ketemu==0) echo "<option value='' selected>-- Pilihan --</option>";
                }else{
                     echo "<option value='' selected>-- Pilihan --</option>";
                }
                while($an=mysqli_fetch_array($tampil)){
                    $nkaryawanid=$an['karyawanid'];
                    $nnama=$an['nama'];
                    if ($pjabatanid=="15")
                        echo "<option value='$nkaryawanid' selected>$nnama</option>";
                    else
                        echo "<option value='$nkaryawanid'>$nnama</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:green;">DM <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='cb_apvdm' name='cb_apvdm' onchange="">
                <?PHP
                $query = "select a.karyawanId karyawanid, b.nama from dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.karyawanId=b.karyawanId order by 2";
                if ($pjabatanid=="15" OR $pjabatanid=="18" OR $pjabatanid=="10") {
                    $query = "select a.dm karyawanid, b.nama from dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.dm=b.karyawanId WHERE a.karyawanid='$pidkaryawan' order by 2";
                }elseif ($pjabatanid=="08" OR $pjabatanid=="20") {
                    $query = "select karyawanId karyawanid, nama from hrd.karyawan WHERE karyawanId='$pidkaryawan'";
                }
                $tampil=mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($pjabatanid=="15" OR $pjabatanid=="18" OR $pjabatanid=="10") {
                    if ($ketemu==0) echo "<option value='' selected>-- Pilihan --</option>";
                }else{
                     echo "<option value='' selected>-- Pilihan --</option>";
                }
                while($an=mysqli_fetch_array($tampil)){
                    $nkaryawanid=$an['karyawanid'];
                    $nnama=$an['nama'];
                    if ($pjabatanid=="15" OR $pjabatanid=="18" OR $pjabatanid=="10")
                        echo "<option value='$nkaryawanid' selected>$nnama</option>";
                    else
                        echo "<option value='$nkaryawanid'>$nnama</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:green;">SM <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='cb_sm' name='cb_sm' onchange="">
                <?PHP
                $query = "select a.karyawanId karyawanid, b.nama from dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.karyawanId=b.karyawanId order by 2";
                if ($pjabatanid=="15" OR $pjabatanid=="18" OR $pjabatanid=="10" OR $pjabatanid=="08") {
                    $query = "select a.sm karyawanid, b.nama from dbmaster.t_karyawan_posisi a JOIN hrd.karyawan b on a.sm=b.karyawanId WHERE a.karyawanid='$pidkaryawan' order by 2";
                }elseif ($pjabatanid=="20") {
                    $query = "select karyawanId karyawanid, nama from hrd.karyawan WHERE karyawanId='$pidkaryawan'";
                }
                $tampil=mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($pjabatanid=="15" OR $pjabatanid=="18" OR $pjabatanid=="10" OR $pjabatanid=="08") {
                    if ($ketemu==0) echo "<option value='' selected>-- Pilihan --</option>";
                }else{
                     echo "<option value='' selected>-- Pilihan --</option>";
                }
                while($an=mysqli_fetch_array($tampil)){
                    $nkaryawanid=$an['karyawanid'];
                    $nnama=$an['nama'];
                    if ($pjabatanid=="15" OR $pjabatanid=="18" OR $pjabatanid=="10" OR $pjabatanid=="08") {
                        echo "<option value='$nkaryawanid' selected>$nnama</option>";
                        $ppilihsm=$nkaryawanid;
                    }else
                        echo "<option value='$nkaryawanid'>$nnama</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <?PHP
    $pcabangid = $_POST['ucabangid'];
    
    $pregion="";
    if ($pidkaryawan=="0000000158") $pregion="B";
    elseif ($pidkaryawan=="0000000159") $pregion="T";
    else{
        $query = "select distinct b.region from mkt.ism0 a join mkt.icabang b on a.icabangid=b.iCabangId WHERE a.karyawanid='$ppilihsm'";
        $tampil= mysqli_query($cnmy, $query);
        $xc= mysqli_fetch_array($tampil);
        $pregion=$xc['region'];
    }
    
    $icardidgsm="";
    if ($pregion=="B") $icardidgsm="0000000158";
    elseif ($pregion=="T") $icardidgsm="0000000159";
    
    ?>
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:green;">GSM/NSM <span class='required'></span></label>
        <div class='col-xs-9'>
            <select class='form-control input-sm' id='cb_gsm' name='cb_gsm' onchange="">
                <?PHP
                $query = "select karyawanId karyawanid, nama from hrd.karyawan WHERE karyawanid='$icardidgsm' order by 2";// jabatanId IN ('05')  '04', '05', '06', '22'
                $tampil=mysqli_query($cnmy, $query);
                $ketemugsm= mysqli_num_rows($tampil);
                if ($ketemugsm==0) echo "<option value='' selected>-- Pilihan --</option>";
                while($an=mysqli_fetch_array($tampil)){
                    $nkaryawanid=$an['karyawanid'];
                    $nnama=$an['nama'];
                    if ($nkaryawanid==$icardidgsm)
                        echo "<option value='$nkaryawanid' selected>$nnama</option>";
                    else
                        echo "<option value='$nkaryawanid'>$nnama</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <?PHP
    
    mysqli_close($cnmy);
}elseif ($pmodule=="xxxxx") {
}elseif ($pmodule=="xxxxx") {
    
}


?>

