<?php
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewdatakaryawanapv"){
    include "../../config/koneksimysqli.php";
    $pidcardapv=$_POST['uidcardapv'];    
    $userid=$pidcardapv;
    $namauser="";
    $lvlposisi="";
    $ppilregion="";
    $stsapv="";
    
    $query ="select nama, jabatanId from hrd.karyawan where karyawanId='$pidcardapv'";
    $tampil= mysqli_query($cnmy, $query);
    $nx= mysqli_fetch_array($tampil);
    $namauser=$nx['nama'];
    $pjbtid=$nx['jabatanId'];
    
    $query ="select karyawanId, region, jabatanId from dbmaster.t_karyawan_posisi where karyawanId='$pidcardapv'";
    $tampilk= mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($tampilk);
    if ($ketemu>0) {
        $ns= mysqli_fetch_array($tampilk);
        $ppilregion=$ns['region'];
        if (!empty($ns['jabatanId'])) $pjbtid=$ns['jabatanId'];
        
    }
    
    
    $carijabatanlvl = mysqli_query($cnmy, "select jabatanId, LEVELPOSISI, ID_GROUP from dbmaster.jabatan_level WHERE jabatanId='$pjbtid'");
    $jl = mysqli_fetch_array($carijabatanlvl);
    $lvlposisi=$jl['LEVELPOSISI'];
    
    if (empty($lvlposisi)) $lvlposisi="HO1";
    
    $stsapv="";
    if ($lvlposisi=="FF1")
        $stsapv="MR";
    elseif ($lvlposisi=="FF2")
        $stsapv="SPV";
    elseif ($lvlposisi=="FF3")
        $stsapv="DM";
    elseif ($lvlposisi=="FF4")
        $stsapv="SM";
    elseif ($lvlposisi=="FF5")
        $stsapv="GSM";
    
    
    ?>
        <div class='col-sm-3'>
            <small>Approve Employee :</small>
            <div class="form-group">
                <div class='input-group date'>
                    <input type='text' class='form-control input-sm' id='e_karyawan' name='e_karyawan' value='<?PHP echo $namauser; ?>' Readonly>


                    <input type='text' class='form-control' id='e_lvlposisi' name='e_lvlposisi' value='<?PHP echo $lvlposisi; ?>' Readonly>
                    <input type='text' class='form-control' id='e_regionp' name='e_regionp' value='<?PHP echo $ppilregion; ?>' Readonly>
                    <input type='text' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $userid; ?>' Readonly>
                    <input type='text' class='form-control input-sm' id='e_ketapv' name='e_ketapv' value='<?PHP echo $stsapv; ?>' Readonly>
                </div>
            </div>
        </div>
    <?PHP
    
    mysqli_close($cnmy);
}elseif ($pmodule=="xxxx"){

}

?>
