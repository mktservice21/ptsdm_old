<?php

if ($_GET['module']=="viewdataareakaryawanbylevel"){
    include "koneksimysqli.php";
    include "fungsi_sql.php";
    $karyawan = trim($_POST['umr']);
    $pdivisi = trim($_POST['udivisi']);
    $psel = trim($_POST['uselinc']);
    $pelevel = trim($_POST['ulevel']);
    $fildivi = " and divi='ETH' ";
    if (trim($pdivisi)=="OTC") {
        $fildivi = " and divi='OTC' ";
    }
    $query = "";
    $ketemu = 0;
    if (trim($pdivisi)=="OTC") {
        $query="SELECT DISTINCT icabangid, areaid, nama_area FROM dbmaster.v_area_cabang_all_divisi WHERE nama_area <> '' AND karyawanid='$karyawan' $fildivi ";
        $query .=" order by nama_area, areaid";
        $tampil = mysqli_query($cnmy, $query);
        $ketemu=  mysqli_num_rows($tampil);
        if ($ketemu==0) $query = "";
        else {
            if (empty($psel)) {
                $tmpl = mysqli_query($cnmy, "select CONCAT(iCabangId,areaId) as lcfields from hrd.karyawan WHERE karyawanId='$karyawan'");
                $rt=mysqli_fetch_array($tmpl);
                $cabareaid = $rt['lcfields'];
                $psel = $cabareaid;
            }
        }
        
    }else{
        if (trim(substr($pelevel, 0, 2)=="FF")) {
            if ($pelevel=="FF1" or $pelevel=="FF2") {
                $query="SELECT DISTINCT icabangid, areaid, nama_area FROM dbmaster.v_penempatan_des WHERE aktif='Y' AND nama_area <> '' AND karyawanid='$karyawan' $fildivi ";
                $query .=" order by nama_area, areaid";
            }elseif ($pelevel=="FF3" or $pelevel=="FF4") {
                $query="SELECT DISTINCT iCabangId icabangid, areaId areaid, nama nama_area FROM MKT.iarea WHERE aktif='Y' ";
                $query .=" AND iCabangId in (SELECT DISTINCT icabangid FROM dbmaster.v_penempatan_all WHERE karyawanid='$karyawan')";
                $query .=" order by nama_area, areaId";
                if (empty($psel)) {
                    $tmpl = mysqli_query($cnmy, "select CONCAT(iCabangId,areaId) as lcfields from hrd.karyawan WHERE karyawanId='$karyawan'");
                    $rt=mysqli_fetch_array($tmpl);
                    $cabareaid = $rt['lcfields'];
                    $psel = $cabareaid;
                }
            }
            if (!empty($query)) {
                $tampil = mysqli_query($cnmy, $query);
                $ketemu=  mysqli_num_rows($tampil);
                if ($ketemu==0) $query = "";
            }
        }
        
        if (empty($psel)) {
            $tmpl = mysqli_query($cnmy, "select CONCAT(iCabangId,areaId) as lcfields from dbmaster.t_karyawan_posisi WHERE karyawanId='$karyawan'");
            $rt=mysqli_fetch_array($tmpl);
            $cabareaid = $rt['lcfields'];
            $psel = $cabareaid;
        }
    
    }
    
    
    if (empty($query)) {
        if (trim($pdivisi)=="OTC") {
            $query="SELECT DISTINCT icabangid_o icabangid, areaid_o areaid, nama nama_area FROM MKT.iarea_o WHERE aktif='Y' ";
            $query .=" order by nama, areaid_o";
            if (empty($psel)) $psel = "0000000007"."0000000001";
        }else{
            $query="SELECT DISTINCT iCabangId icabangid, areaId areaid, nama nama_area FROM MKT.iarea WHERE aktif='Y' ";
            $query .=" order by nama_area, areaId";
            if (empty($psel)) $psel = "0000000001"."0000000001";
        }
    }
    
    
    $tampil = mysqli_query($cnmy, $query);
    $ketemu=  mysqli_num_rows($tampil);
    
    $xsudah="";
    if ($ketemu>0) {
        if ($ketemu>=2) echo "<option value='' selected>blank_</option>";
        while($a=mysqli_fetch_array($tampil)){
            if (!empty($a['nama_area'])) {
                $cabangarea = $a['icabangid'].",".$a['areaid'];
                $namacabangarea = $a['nama_area'];
                if ($a['icabangid'].$a['areaid']==$psel){
                    echo "<option value='$cabangarea' selected>$namacabangarea</option>";
                    $xsudah="sudah";
                }elseif (trim(substr($pelevel, 0, 2)=="FF") AND empty($psel)){
                    echo "<option value='$cabangarea' selected>$namacabangarea</option>";
                    $xsudah="sudah";
                }else{
                    if ($xsudah==""){
                        echo "<option value='$cabangarea' selected>$a[nama_area]</option>";
                        $xsudah="sudah";
                    }else{
                        echo "<option value='$cabangarea'>$a[nama_area]</option>";
                    }
                }
            }
        }
    }else{
        echo "<option value='' selected>blank_</option>";
    }
    
}elseif ($_GET['module']=="viewdataareakaryawanalldiv"){
    include "koneksimysqli.php";
    include "fungsi_sql.php";
    $karyawan=$_POST['umr'];
    $pdivisi=$_POST['udivisi'];
    $psel = $_POST['uselinc'];
    if (empty($psel))
        $psel = getfieldit("select areaid as lcfields from hrd.karyawan where karyawanId='$karyawan'");
    
    $fildivi = " and divi='ETH' ";
    if (trim($pdivisi)=="OTC") {
        $fildivi = " and divi='OTC' ";
    }
    $query="SELECT DISTINCT areaid areaId, nama_area FROM dbmaster.v_area_cabang_all_divisi WHERE nama_area <> '' AND karyawanid='$karyawan' $fildivi ";
    $query .=" order by nama_area, areaid";
    $ketemu=  mysqli_num_rows(mysqli_query($cnmy, $query));
    if ($ketemu==0) {
        $query="SELECT DISTINCT areaId, nama nama_area FROM dbmaster.v_area_all_divisi WHERE aktif='Y' $fildivi ";
        $query .=" order by nama, areaId";
        $ketemu=  mysqli_num_rows(mysqli_query($cnmy, $query));
    }
    
    if ($ketemu>=2) echo "<option value='' selected>blank_</option>";
    $tampil = mysqli_query($cnmy, $query);
    while($a=mysqli_fetch_array($tampil)){
        if ($a['areaId']==$psel)
            echo "<option value='$a[areaId]' selected>$a[nama_area]</option>";
        else
            echo "<option value='$a[areaId]'>$a[nama_area]</option>";
    }
    
}elseif ($_GET['module']=="viewdataareakaryawan"){
    include "koneksimysqli.php";
    $karyawan=$_POST['umr'];
    $query="SELECT DISTINCT areaId, nama_area FROM dbmaster.v_areakaryawan WHERE karyawanId='$karyawan' ";
    $query .=" order by nama_area, areaId";
    $ketemu=  mysqli_num_rows(mysqli_query($cnmy, $query));
    if ($ketemu==0) {
        echo "<option value='' selected>-- Pilihan --</option>";
    }else{
        if ($ketemu>=2) echo "<option value='' selected>-- Pilihan --</option>";
        $tampil = mysqli_query($cnmy, $query);
        while($a=mysqli_fetch_array($tampil)){
            if ($a['areaId']==$_POST['uselinc'])
                echo "<option value='$a[areaId]' selected>$a[nama_area]</option>";
            else
                echo "<option value='$a[areaId]'>$a[nama_area]</option>";
        }
    }
}elseif ($_GET['module']=="viewkodedivisi"){
    include "koneksimysqli.php";
    $myfil=$_POST['udata1'];
    if (!empty($myfil)){
        $myfil="(".substr($myfil, 0, -1).")";
    }
    $filtipe = "";
    if (isset($_POST['upilihtipe'])) {
        if ($_POST['upilihtipe']=="Y") {
            $filtipe = " and kodeid in (select kodeid from dbmaster.br_kode where (br <> '') and (br<>'N')) ";
        }elseif ($_POST['upilihtipe']=="N") {
            $filtipe = " and kodeid not in (select kodeid from dbmaster.br_kode where (br <> '') and (br<>'N')) ";
        }
    }
    $sql=mysqli_query($cnmy, "select kodeid,nama,divprodid from dbmaster.br_kode where divprodid in $myfil $filtipe order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[kodeid]' name=chkbox_kode[] checked> $Xt[kodeid] - $Xt[nama]<br/>";
    }
}elseif ($_GET['module']=="viewregioncab"){
    include "koneksimysqli.php";
    if ($_POST['udata1']=="true" and $_POST['udata2']=="true") {
        $filter =" ('B', 'T') ";
        echo '<input type="checkbox" name="chkbox_cabang[]" id="chkbox_cabang[]" value="tanpa_cabang" checked>_blank <br/>';
    }elseif ($_POST['udata1']=="true" and $_POST['udata2']=="false") {
        $filter =" ('B') ";
    }elseif ($_POST['udata1']=="false" and $_POST['udata2']=="true") {
        $filter =" ('T') ";
    }else{
        $filter =" ('') ";
    }
    $filter= " where region in ".$filter;
    
    $sql=mysqli_query($cnmy, "SELECT distinct iCabangId, nama from dbmaster.icabang $filter order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[iCabangId]' name=chkbox_cabang[] checked> $Xt[nama]<br/>";
    }
}elseif ($_GET['module']=="viewkodepostingotc"){
    include "koneksimysqli.php";
    $filt=" where subpost='$_POST[upost]' ";
    if ($_POST['upost']=="none") $filt="";
    $sql=mysqli_query($cnmy, "select distinct kodeid, nama, subpost from dbmaster.brkd_otc $filt order by kodeid");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[kodeid]' name=chkbox_kodeotc[] checked> $Xt[kodeid] - $Xt[nama]<br/>";
    }
    
}elseif ($_GET['module']=="xxxx"){
}elseif ($_GET['module']=="xxxxx"){
}elseif ($_GET['module']=="xxxxxx"){
}elseif ($_GET['module']=="xxxxxxx"){
}elseif ($_GET['module']=="xxxxxxxx"){
    
}


?>
