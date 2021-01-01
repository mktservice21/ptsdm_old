<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="carisudahclosing") {
    
    $pmyidcard=$_SESSION['IDCARD'];
    $ptglpil=$_POST['uperiode1'];
    $pperiode_ = date("Ym", strtotime($ptglpil));
    
    include "../../config/koneksimysqli_ms.php";

    $query = "select * from sls.closing_target WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_'";
    $tampil= mysqli_query($cnms, $query);
    $rs= mysqli_fetch_array($tampil);
    $nketclosing=STRTOUPPER($rs['status']);

    $pbukatarget=true;
    if ($nketclosing=="CLOSING") $pbukatarget=false;

    $query = "select * from sls.closing_target_exception WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_' AND karyawanid='$pmyidcard'";
    $tampil1= mysqli_query($cnms, $query);
    $ketemu= mysqli_num_rows($tampil1);
    if ($ketemu>0) {
        $pbukatarget=true;
    }
    
    if ($pbukatarget==true) {
    ?>
        <div class='col-sm-2'>
            Load File (<b>File XLSX</b>)
            <div class="form-group">
                <input type="file" name="fileToUpload" id="fileToUpload" accept=".xlsx"><!--.xlsx,.xls-->
            </div>
        </div>

        <div class='col-sm-4'>
            <small>&nbsp;</small>
           <div class="form-group">
               <button type='button' class='btn btn-dark btn-xs' onclick='UploadDataKeServer()'>Upload</button>
               <button type='button' class='btn btn-info btn-xs' onclick='LihatDataSudahUpload()'>Lihat Data</button>
               <button type='button' class='btn btn-danger btn-xs' onclick='ResetUploadData()'>Reset Target Area (0)</button>
           </div>
       </div>

    <?PHP
    }else{
    ?>
        <div class='col-sm-4'>
            <small>&nbsp;</small>
           <div class="form-group">
               <button type='button' class='btn btn-info btn-xs' onclick='LihatDataSudahUpload()'>Lihat Data</button>
           </div>
       </div>
    <?PHP  
    }
    
}elseif ($pmodule=="cariareacabang") {
    $ptglpil=$_POST['uperiode1'];
    $pidcabang=$_POST['ucabid'];
    
    $pperiode_ = date("Ym", strtotime($ptglpil));
    
    $pidareapil="";
    if (!empty($_SESSION['TGTUPDAREAPIL'])) $pidareapil=$_SESSION['TGTUPDAREAPIL'];
    
    include "../../config/koneksimysqli_ms.php";
    
    
    $query ="select DISTINCT icabangid, areaid from tgt.targetarea WHERE icabangid='$pidcabang' AND DATE_FORMAT(bulan,'%Y%m')='$pperiode_'";
    $tampil_= mysqli_query($cnms, $query);
    $ketemu= mysqli_num_rows($tampil_);
    if ($ketemu==0) {
        echo "<option value=''>-- Pilih --</option>";
    }else{
        
        $piarean="";
        while ($nr= mysqli_fetch_array($tampil_)) {
            $mmpidarea=$nr['areaid'];
            $piarean .="'".$mmpidarea."',";
        }
        if (!empty($piarean)) {
            $piarean .="'xxxcxxx'";
            $piarean=" AND areaId IN (".$piarean.") ";
        }
    
        //include "../../config/koneksimysqli.php";

        echo "<option value=''>-- Pilih --</option>";
        $query = "select iCabangId, areaId, Nama from sls.iarea where aktif='Y' AND iCabangId='$pidcabang' $piarean order by Nama";
        $tampil = mysqli_query($cnms, $query);
        while ($rx= mysqli_fetch_array($tampil)) {
            $nidarea=$rx['areaId'];
            $nnmarea=$rx['Nama'];
            if ($pidareapil==$nidarea)
                echo "<option value='$nidarea' selected>$nnmarea</option>";
            else
                echo "<option value='$nidarea'>$nnmarea</option>";
        }
    
    }
    
    mysqli_close($cnms);
    //mysqli_close($cnmy);
}

?>