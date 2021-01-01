<?php

if ($_GET['module']=="viewdataposting"){
    include "../../config/koneksimysqli_it.php";
    
    $subposting = $_POST['usubpost'];
    
    $query = "select distinct kodeid, nama from hrd.brkd_otc where subpost='$subposting' order by nama ";
    
    $tampil=mysqli_query($cnit, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[kodeid]'>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdatacoa"){
    include "../../config/koneksimysqli_it.php";
    
    $subposting = $_POST['usubpost'];
    $query = "select distinct COA4 from dbmaster.posting_coa where 1=1 AND subpost = $subposting";
    $tampil=mysqli_query($cnit, $query);
    $x=mysqli_fetch_array($tampil);
    $coa4=$x['COA4'];
        
    
    
    $posting = $_POST['upost'];
    if (!empty($posting)) {
        $query = "select distinct COA4 from dbmaster.posting_coa where 1=1 AND (kodeid=$posting AND subpost = $subposting)";
        $tampil=mysqli_query($cnit, $query);
        $ketemu=  mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $x=mysqli_fetch_array($tampil);
            $coa4=$x['COA4'];
        }
    }
    
    
    $query = "select distinct COA4, NAMA4 from dbmaster.v_coa_all where (DIVISI='OTC' or ifnull(DIVISI,'')='') order by NAMA4";
    
    $tampil=mysqli_query($cnit, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        if ($a['COA4']==$coa4)
            echo "<option value='$a[COA4]' selected>$a[NAMA4]</option>";
        else
            echo "<option value='$a[COA4]'>$a[NAMA4]</option>";
    }
}elseif ($_GET['module']=="viewdataareacab"){
    include "../../config/koneksimysqli_it.php";
    
    $cabang = $_POST['ucab'];
    
    $query = "select areaid_o, nama, aktif from MKT.iarea_o where icabangid_o='$cabang' order by nama";
    
    $tampil=mysqli_query($cnit, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[areaid_o]'>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdataareacabbytoko"){
    include "../../config/koneksimysqli_it.php";
    include "../../config/fungsi_sql.php";
    
    $cabang = $_POST['ucab'];
    $tokoo = $_POST['utoko'];
    
    $areatoko= getfieldcnit("select areaid_o as lcfields from MKT.icust_o where icustid_o='$tokoo' and icabangid_o='$cabang'");
    
    $query = "select areaid_o, nama, aktif from MKT.iarea_o where icabangid_o='$cabang' order by nama";
    $tampil=mysqli_query($cnit, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        if ($a['areaid_o']==$areatoko)
            echo "<option value='$a[areaid_o]' selected>$a[nama]</option>";
        else
            echo "<option value='$a[areaid_o]'>$a[nama]</option>";
    }
    
}elseif ($_GET['module']=="viewdatatokocab"){
    include "../../config/koneksimysqli_it.php";
    
    $cabang = $_POST['ucab'];
    $area="";
    if (isset($_POST['uarea'])) {
        if (!empty($_POST['uarea']))
            $area=" and areaid_o='$_POST[uarea]' ";
    }
    
    $query = "select icustid_o, nama from MKT.icust_o where icabangid_o='$cabang' $area order by nama";
    
    $tampil=mysqli_query($cnit, $query);
    
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[icustid_o]'>$a[nama]</option>";
    }
}elseif ($_GET['module']=="viewdatatokoinput"){
    include "../../config/koneksimysqli_it.php";
    $cabang = $_POST['ucab'];
    $area = $_POST['uarea'];
    $tokoo = $_POST['utoko'];
    $tgl = $_POST['utgl'];
    $idnya = $_POST['uid'];
    $filid = "";
    if (!empty($idnya)) $filid = " AND brotcid <> '$idnya' ";
    $ptgl="";
    if (!empty($tgl)) {
        $datetrm = str_replace('/', '-', $tgl);
        $ptgl= date("Ym", strtotime($datetrm));
    }
        
    $query = "select * from hrd.br_otc_ext 
        where icabangid_o='$cabang' and areaid_o='$area' and icustid_o='$tokoo'
        and '$ptgl' BETWEEN DATE_FORMAT(tglmulaisewa,'%Y%m') and DATE_FORMAT(DATE_ADD(tglmulaisewa, INTERVAL periode-1 MONTH),'%Y%m') $filid";
    $tampil=mysqli_query($cnit, $query);
    $ketemu=mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $a=mysqli_fetch_array($tampil);
        $noid = $a['brotcid'];
        $periode = $a['periode'];
        $mulai = date("d F Y", strtotime($a['tglmulaisewa']));
        $query = "select * from hrd.br_otc where brOtcId='$noid'";
        $tampil=mysqli_query($cnit, $query);
        $z=mysqli_fetch_array($tampil);
        $jumlah=(double)$z['jumlah']/(double)$periode;
        $jumlah=number_format($jumlah,0,",",",");
        $total=number_format($z['jumlah'],0,",",",");
        echo "<table>";
        echo "<tr style='background-color:red; color:#ffffff;'><td colspan=3><b>Masih Dalam Periode Pengajuan</b></td></tr>";
        echo "<tr><td>ID</td><td> &nbsp; : &nbsp; </td><td>$noid</td></tr>";
        echo "<tr><td>Periode/bulan</td><td> &nbsp; : &nbsp; </td><td>$periode</td></tr>";
        echo "<tr><td>Mulai</td><td> &nbsp; : &nbsp; </td><td>$mulai</td></tr>";
        echo "<tr><td>biaya/bulan</td><td> &nbsp; : &nbsp; </td><td>$jumlah</td></tr>";
        echo "<tr><td>Total Rp.</td><td> &nbsp; : &nbsp; </td><td>$total</td></tr>";
        echo "</table>";
    }else{
        $query = "select * from hrd.br_otc_ext where CONCAT(icabangid_o, areaid_o, DATE_FORMAT(tglmulaisewa,'%Y%m')) in (
                select CONCAT(icabangid_o, areaid_o, DATE_FORMAT(max(tglmulaisewa),'%Y%m')) terakhir from hrd.br_otc_ext 
                where icabangid_o='$cabang' and areaid_o='$area' and icustid_o='$tokoo' $filid 
                )";
        $tampil=mysqli_query($cnit, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $a=mysqli_fetch_array($tampil);
            $noid = $a['brotcid'];
            $periode = $a['periode'];
            $mulai = date("d F Y", strtotime($a['tglmulaisewa']));
            $query = "select * from hrd.br_otc where brOtcId='$noid'";
            $tampil=mysqli_query($cnit, $query);
            $z=mysqli_fetch_array($tampil);
            $jumlah=(double)$z['jumlah']/(double)$periode;
            $jumlah=number_format($jumlah,0,",",",");
            $total=number_format($z['jumlah'],0,",",",");
            echo "<table>";
            echo "<tr><td colspan=3><b><u>Terakhir Pengajuan</u></b></td></tr>";
            echo "<tr><td>ID</td><td> &nbsp; : &nbsp; </td><td>$noid</td></tr>";
            echo "<tr><td>Periode/bulan</td><td> &nbsp; : &nbsp; </td><td>$periode</td></tr>";
            echo "<tr><td>Mulai</td><td> &nbsp; : &nbsp; </td><td>$mulai</td></tr>";
            echo "<tr><td>Rp./bulan</td><td> &nbsp; : &nbsp; </td><td>$jumlah</td></tr>";
            echo "<tr><td>Total Rp.</td><td> &nbsp; : &nbsp; </td><td>$total</td></tr>";
            echo "</table>";
        }else{
            echo "";
        }
    }
}elseif ($_GET['module']=="xxx"){
}

?>
