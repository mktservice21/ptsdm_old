<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caricabang") {
    $pidkar=$_POST['ukry'];
    
    include "../../config/koneksimysqli.php";
    
    
    
    $query = "select icabangid, areaid, spv atasan1, dm atasan2, sm atasan3, gsm atasan4, jabatanid from dbmaster.t_karyawan_posisi where karyawanid='$pidkar'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pidcabang=$row['icabangid'];
    $pidarea=$row['areaid'];
    $pidjabatan=$row['jabatanid'];
    
    $patasan1=$row['atasan1'];
    $patasan2=$row['atasan2'];
    $patasan3=$row['atasan3'];
    $patasan4=$row['atasan4'];

    $query = "select icabangid, areaid, jabatanid from hrd.karyawan where karyawanid='$pidkar'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    if (empty($pidcabang)) $pidcabang=$row['icabangid'];
    if (empty($pidarea)) $pidarea=$row['areaid'];
    if (empty($pidjabatan)) $pidjabatan=$row['jabatanid'];
    
    $query = "select icabangid_o as icabangid_o, nama as nama FROM MKT.icabang_o WHERE icabangid_o='$pidcabang' order by nama";
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    
    
    
    if ((DOUBLE)$record<=0) echo "<option value='' selected>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result);
        $cdkodeid  = $row['icabangid_o'];
        $cdnama = $row['nama'];
        
        if ($cdkodeid==$pidcabang) {
            echo "<option value=\"$cdkodeid\" selected>$cdnama</option>";
        }else{
            echo "<option value=\"$cdkodeid\" >$cdnama</option>";
        }
    }
    
    $cdkodeid="JKT_RETAIL";
    $cdnama="JAKARTA RETAIL";
    if ($pidkar=="0000000515") {
        echo "<option value=\"$cdkodeid\" selected>$cdnama</option>";
    }
    
    mysqli_close($cnmy);
            

}elseif ($pmodule=="caridataarea") {
    $pidkar=$_POST['ukry'];
    $pidcabang=$_POST['ucab'];
    $pidcabasli=$_POST['ucab'];
    
    include "../../config/koneksimysqli.php";
    
    if ($pidcabasli=="JKT_RETAIL") $pidcabang="0000000007";
    
    $query = "select distinct icabangid_o as iCabangId, areaid_o as areaid_o, nama as nama from MKT.iarea_o WHERE icabangid_o='$pidcabang' order by nama";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((DOUBLE)$ketemu<=0) echo "<option value='' selected>-- Pilihan --</option>";
    while ($z= mysqli_fetch_array($tampil)) {
        $pidarea=$z['areaid_o'];
        $pnmarea=$z['nama'];
        if ($pidcabasli=="JKT_RETAIL") {
            if ($pidarea=="0000000001") {
                echo "<option value='$pidarea' selected>$pnmarea</option>";
            }else{
                echo "<option value='$pidarea'>$pnmarea</option>";
            }
        }else{
            echo "<option value='$pidarea'>$pnmarea</option>";
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
            . " IFNULL(icabangid_o,'')='$pidcab' AND IFNULL(stsnonaktif,'')<>'Y' AND idkascab<>'$pid'";
    
    
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
