<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];



include "../../config/koneksimysqli.php";

$ppilihan=$_POST['upilih'];
$pidkaryawan=$_POST['ucar'];
$pstskry=$_POST['ustskry'];
$pidkontrak=$_POST['uidkontrak'];
$pnmlama=$_POST['unmlama'];
$pnmbaru=$_POST['unmbaru'];


$pidjabatan="";
$pidcabang="";
$pidarea="";

$patasan1="";
$patasan2="";
$patasan3="";
$patasan4="";

$pstatusreadonly=" class='disabledDiv' ";
if ($ppilihan=="2" OR (INT)$ppilihan==2) $pstatusreadonly="";

if ($pstskry=="inone") {
    if ( (strtoupper(trim($pnmlama))<>strtoupper(trim($pnmbaru))) OR (empty($pnmlama) AND empty($pnmbaru) AND empty($pidkontrak)) ) {
        $pidkontrak="";
        $pstatusreadonly="";
    }
    if (empty($pidkontrak)) $pstatusreadonly="";
    
    $query = "select icabangid_o icabangid, areaid_o areaid, atasan1, atasan2, atasan3, atasan4, jabatanid from dbmaster.t_karyawan_kontrak where id='$pidkontrak'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pidcabang=$row['icabangid'];
    $pidarea=$row['areaid'];
    $pidjabatan=$row['jabatanid'];
    
    $patasan1=$row['atasan1'];
    $patasan2=$row['atasan2'];
    $patasan3=$row['atasan3'];
    $patasan4=$row['atasan4'];
}else{
    
    $query = "select icabangid, areaid, spv atasan1, dm atasan2, sm atasan3, gsm atasan4, jabatanid from dbmaster.t_karyawan_posisi where karyawanid='$pidkaryawan'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pidcabang=$row['icabangid'];
    $pidarea=$row['areaid'];
    $pidjabatan=$row['jabatanid'];
    
    $patasan1=$row['atasan1'];
    $patasan2=$row['atasan2'];
    $patasan3=$row['atasan3'];
    $patasan4=$row['atasan4'];

    $query = "select icabangid, areaid, jabatanid from hrd.karyawan where karyawanid='$pidkaryawan'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    if (empty($pidcabang)) $pidcabang=$row['icabangid'];
    if (empty($pidarea)) $pidarea=$row['areaid'];
    if (empty($pidjabatan)) $pidjabatan=$row['jabatanid'];
}


?>
<style>
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
</style>

<div id="div_disable_non" <?PHP echo $pstatusreadonly; ?> >
    
    <div id="div_cabang">

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jabatan <span class='required'></span></label>
            <div class='col-xs-5'>
                <select class='form-control input-sm' id='cb_idjabatan' name='cb_idjabatan'>
                    <option value='' selected>_blank</option>
                    <?PHP
                    $query = "select jabatanid, nama from hrd.jabatan ";
                    $query .= " order by jabatanid";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($rt= mysqli_fetch_array($tampil)) {
                        $pjabid=$rt['jabatanid'];
                        $pnmjab=$rt['nama'];

                        if ($pjabid==$pidjabatan)
                            echo "<option value='$pjabid' selected>$pjabid - $pnmjab</option>";
                        else
                            echo "<option value='$pjabid'>$pjabid - $pnmjab</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
            <div class='col-xs-5'>
                <select class='form-control input-sm' id='cb_idcabang' name='cb_idcabang' onclick="CariDataArea()">
                    <option value='' selected>_blank</option>
                    <?PHP
                    $query = "select icabangid_o, nama from MKT.icabang_o where IFNULL(aktif,'')='Y' ";
                    $query .= " order by nama";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($rt= mysqli_fetch_array($tampil)) {
                        $pcabid=$rt['icabangid_o'];
                        $pnmcab=$rt['nama'];

                        if ($pcabid==$pidcabang)
                            echo "<option value='$pcabid' selected>$pnmcab</option>";
                        else
                            echo "<option value='$pcabid'>$pnmcab</option>";
                    }
                    ?>
                </select>
            </div>
        </div>


        <div class='form-group'>
            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
            <div class='col-xs-5'>
                <select class='form-control input-sm' id='cb_idarea' name='cb_idarea'>
                    <option value='' selected>_blank</option>
                    <?PHP
                    $query = "select areaid_o, nama from MKT.iarea_o where icabangid_o='$pidcabang' AND IFNULL(aktif,'')='Y' ";
                    $query .= " order by nama";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($rt= mysqli_fetch_array($tampil)) {
                        $pareaid=$rt['areaid_o'];
                        $pnmarea=$rt['nama'];

                        if ($pareaid==$pidarea)
                            echo "<option value='$pareaid' selected>$pnmarea</option>";
                        else
                            echo "<option value='$pareaid'>$pnmarea</option>";
                    }
                    ?>
                </select>
            </div>
        </div>


    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV <span class='required'></span></label>
        <div class='col-xs-5'>
            <select class='form-control input-sm' id='cb_idspv' name='cb_idspv' onchange="ShowDataDMotc()">
                <option value='' selected>_blank</option>
                <?PHP
                $query ="select karyawanid, nama from hrd.karyawan where 1=1 "
                        . " AND (aktif='Y' OR karyawanid='$patasan1' ) ";
                    $query .=" AND divisiid ='OTC' ";
                    $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
                $query .=" ORDER BY nama";
                $sql=mysqli_query($cnmy, $query);
                while ($Xt=mysqli_fetch_array($sql)){
                    $xid=$Xt['karyawanid'];
                    $xnama=$Xt['nama'];

                    if ($xid==$patasan1)
                        echo "<option value='$xid' selected>$xnama</option>";
                    else
                        echo "<option value='$xid'>$xnama</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>AM <span class='required'></span></label>
        <div class='col-xs-5'>
            <select class='form-control input-sm' id='cb_idam' name='cb_idam' onchange="ShowDataGSMotc()">
                <option value='' selected>_blank</option>
                <?PHP
                $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$patasan2') ";                                            
                    $query .=" AND divisiid ='OTC' ";
                    $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.')  and LEFT(nama,7) NOT IN ('NN DM - ')  and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-') AND LEFT(nama,5) NOT IN ('NN AM', 'NN DR') ";
                $query .=" ORDER BY nama";

                $sql=mysqli_query($cnmy, $query);
                while ($Xt=mysqli_fetch_array($sql)){
                    $xid=$Xt['karyawanid'];
                    $xnama=$Xt['nama'];

                    if ($xid==$patasan2)
                        echo "<option value='$xid' selected>$xnama</option>";
                    else
                        echo "<option value='$xid'>$xnama</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>HOS <span class='required'></span></label>
        <div class='col-xs-5'>
            <select class='form-control input-sm' id='cb_idhos' name='cb_idhos'>
                <option value='' selected>_blank</option>
                <?PHP
                $query ="select karyawanid, nama from hrd.karyawan where (aktif='Y' OR karyawanid='$patasan4')";
                    $query .=" AND divisiid ='OTC' ";
                    $query .=" AND karyawanId Not In (select distinct karyawanId from dbmaster.t_karyawanadmin) ";
                    $query .=" And jabatanId in (select distinct jabatanId from hrd.jabatan WHERE rank='02')";
                $query .=" ORDER BY nama";

                $sql=mysqli_query($cnmy, $query);
                while ($Xt=mysqli_fetch_array($sql)){
                    $xid=$Xt['karyawanid'];
                    $xnama=$Xt['nama'];

                    if ($xid==$patasan4)
                        echo "<option value='$xid' selected>$xnama</option>";
                    else
                        echo "<option value='$xid'>$xnama</option>";
                }
                ?>
            </select>
        </div>
    </div>
    
</div>

<?PHP
mysqli_close($cnmy);
?>