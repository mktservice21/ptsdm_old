<?php
if ($_GET['module']=="viewdatamr0") {
    include "../../config/koneksimysqli.php";
    $idkarawanspv=$_POST['uspv'];
    $no=1;
    $query = "select distinct karyawanid, nama from dbmaster.v_penempatanmr where ifnull(karyawanid,'')<>'' "
            . " and CONCAT(icabangid,areaid) in (select distinct CONCAT(icabangid,areaid) from dbmaster.v_penempatanspv where karyawanid='$idkarawanspv')"
            . " order by nama";
    $tampil=mysqli_query($cnmy, $query);

    while ($r=  mysqli_fetch_array($tampil)) {
        echo "<option value='$r[karyawanid]'>$r[nama]</option>";
        if ($no==1) $idkarawalmr="$r[karyawanid]";
        $no++;
    }
}elseif ($_GET['module']=="viewdataspv0") {
    include "../../config/koneksimysqli.php";
    $idkarawandm=$_POST['udm'];
    $no=1;
    $fil="";
    if (!empty($idkarawandm)) $fil=" and icabangid in (select distinct icabangid from dbmaster.v_penempatandm where karyawanid='$idkarawandm') ";
    $query = "select distinct karyawanid, nama from dbmaster.v_penempatanspv where ifnull(karyawanid,'')<>'' "
            . " $fil "
            . " order by nama";
    $tampil=mysqli_query($cnmy, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while ($r=  mysqli_fetch_array($tampil)) {
        echo "<option value='$r[karyawanid]'>$r[nama]</option>";
        if ($no==1) $idkarawalspv="$r[karyawanid]";

        $no++;
    }   
}else{

}
?>