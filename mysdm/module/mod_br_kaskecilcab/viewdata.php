<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caricabang") {
    $pidkar=$_POST['ukry'];
    
    include "../../config/koneksimysqli.php";
    
    $query = "select jabatanid from hrd.karyawan where karyawanid='$pidkar'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pjabatanid=$row['jabatanid'];
    if ($pjabatanid=="38") {
        $query = "SELECT distinct a.karyawanid, a.iCabangId, b.nama, '' as icabangkaryawan "
                . " FROM hrd.rsm_auth a JOIN MKT.icabang b on a.icabangid=b.icabangid WHERE a.karyawanid='$pidkar' order by b.nama";//b.aktif='Y'
        $result = mysqli_query($cnmy, $query); 
        $record = mysqli_num_rows($result);
    }else{
    
        //$pnmtablekry = "karyawan";
        $pnmtablekry = "tempkaryawandccdss_inp";

        $belumklik=false;
        $karyawanId = $_POST['umr'];
        $query = "select DISTINCT karyawan.iCabangId, cabang.nama, karyawan.icabangkaryawan from hrd.$pnmtablekry as karyawan join dbmaster.icabang as cabang on "
                . " karyawan.icabangid=cabang.icabangid where karyawanId='$pidkar'  order by cabang.nama"; 
        $result = mysqli_query($cnmy, $query); 
        $record = mysqli_num_rows($result);
        
    }
    
    if ($record==0) {
        $query = "select iCabangId, nama, '' as icabangkaryawan FROM MKT.icabang WHERE AKTIF='Y' order by nama";
        $result = mysqli_query($cnmy, $query); 
        $record = mysqli_num_rows($result);
        $belumklik=true;
    }
    
    
    echo "<option value='' selected>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result);
        $cdicabkry  = $row['icabangkaryawan'];
        $cdkodeid  = $row['iCabangId'];
        $cdnama = $row['nama'];
        if ($cdkodeid==$cdicabkry) {
            echo "<option value=\"$cdkodeid\" selected>$cdnama</option>";
            $belumklik=true;
        }else{
            if ($belumklik==true) 
                echo "<option value=\"$cdkodeid\" >$cdnama</option>";
            else
                echo "<option value=\"$cdkodeid\" selected>$cdnama</option>";
        }
    }
    
    
    mysqli_close($cnmy);
            

}elseif ($pmodule=="cekdatasudahada") {
    $pid=$_POST['uid'];
    $ptgl=$_POST['utgl'];
    $pbln=$_POST['ubulan'];
    $pidkar=$_POST['ukry'];
    $pidcab=$_POST['ucabid'];
    $pcoa=$_POST['ucoap'];
    
    $ptanggal = str_replace('/', '-', $ptgl);
    $periode1= date("Y-m-d", strtotime($ptanggal));
    $pbulan= date("Ym", strtotime($pbln));
    
    $bolehinput="boleh";
    
    include "../../config/koneksimysqli.php";
    
    //$pidcab="xxxxxx";//hilangkan kalau dibuka
    $query = "select idkascab from dbmaster.t_kaskecilcabang WHERE DATE_FORMAT(bulan,'%Y%m')='$pbulan' AND "
            . " IFNULL(icabangid,'')='$pidcab' AND IFNULL(stsnonaktif,'')<>'Y' AND idkascab<>'$pid'";
    
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    
    if ($ketemu>0) {
        $row= mysqli_fetch_array($tampil);
        $nidkascab=$row['idkascab'];
        if (!empty($nidkascab)) $bolehinput="Data Sudah Ada, dengan ID : $nidkascab";
    }
    
    mysqli_close($cnmy);
    echo $bolehinput;
    
}elseif ($pmodule=="caridatapettycashcab") {
    include "../../config/koneksimysqli.php";
    $pidpengajuan=$_POST['uuntuk'];
    $pidcabang=$_POST['ucab'];
    
    $pnmfieldcab=" icabangid ";
    if ($pidpengajuan=="OTC" OR $pidpengajuan=="CHC") {
        $pnmfieldcab=" icabangid_o ";
    }
    
    $query = "select * from dbmaster.t_uangmuka_kascabang WHERE $pnmfieldcab='$pidcabang'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $prppettycash=$pr['jumlah'];
    if (empty($prppettycash)) $prppettycash=0;
    
    mysqli_close($cnmy);
    
    echo $prppettycash;
}elseif ($pmodule=="caridatapettycashpcmcab") {
    include "../../config/koneksimysqli.php";
    $pidpengajuan=$_POST['uuntuk'];
    $pidcabang=$_POST['ucab'];
    
    $pnmfieldcab=" icabangid ";
    if ($pidpengajuan=="OTC" OR $pidpengajuan=="CHC") {
        $pnmfieldcab=" icabangid_o ";
    }
    
    $query = "select * from dbmaster.t_uangmuka_kascabang WHERE $pnmfieldcab='$pidcabang'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $prppcm=$pr['pcm'];
    if (empty($prppcm)) $prppcm=0;
    
    mysqli_close($cnmy);
    
    echo $prppcm;
}elseif ($pmodule=="caridatapettycashtambahcab") {
    include "../../config/koneksimysqli.php";
    $pidpengajuan=$_POST['uuntuk'];
    $pidcabang=$_POST['ucab'];
    
    $pnmfieldcab=" icabangid ";
    if ($pidpengajuan=="OTC" OR $pidpengajuan=="CHC") {
        $pnmfieldcab=" icabangid_o ";
    }
    
    $query = "select * from dbmaster.t_uangmuka_kascabang WHERE $pnmfieldcab='$pidcabang'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $prptambahan=$pr['jmltambahan'];
    if (empty($prptambahan)) $prptambahan=0;
    
    mysqli_close($cnmy);
    
    echo $prptambahan;
}elseif ($pmodule=="caridatapettycashsldawalcab") {
    include "../../config/koneksimysqli.php";
    $pidpengajuan=$_POST['uuntuk'];
    $pidcabang=$_POST['ucab'];
    
    $pnmfieldcab=" icabangid ";
    if ($pidpengajuan=="OTC" OR $pidpengajuan=="CHC") {
        $pnmfieldcab=" icabangid_o ";
    }
    
    $query = "select * from dbmaster.t_uangmuka_kascabang WHERE $pnmfieldcab='$pidcabang'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $prpsldawal=$pr['saldoawal'];
    if (empty($prpsldawal)) $prpsldawal=0;
    
    mysqli_close($cnmy);
    
    echo $prpsldawal;
    
}elseif ($pmodule=="caridataoustanding") {
    include "../../config/koneksimysqli.php";
    $pidpengajuan=$_POST['uuntuk'];
    $pidcabang=$_POST['ucab'];
    $pnmfieldcab=" icabangid ";
    
    $query = "select * from dbmaster.t_outstanding_kaskecilcab WHERE icabangid='$pidcabang' AND pengajuan='$pidpengajuan'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $prpputsanding=$pr['jmlsisa'];
    if (empty($prpputsanding)) $prpputsanding=0;
    
    mysqli_close($cnmy);
    
    echo $prpputsanding;
}elseif ($pmodule=="carinamarealisasi") {
    include "../../config/koneksimysqli.php";
    $pidkry=$_POST['ukary'];
    
    $query = "select nama as nama from hrd.karyawan WHERE karyawanid='$pidkry'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $pnamakry=$pr['nama'];
    
    mysqli_close($cnmy);
    
    echo $pnamakry;
}

?>
