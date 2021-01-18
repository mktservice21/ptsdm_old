<?php

function CariBulanRomawi($bulan){
    $bl=(int)$bulan;
    $blromawi="I";
    if ($bl==1) $blromawi="I";
    if ($bl==2) $blromawi="II";
    if ($bl==3) $blromawi="III";
    if ($bl==4) $blromawi="IV";
    if ($bl==5) $blromawi="V";
    if ($bl==6) $blromawi="VI";
    if ($bl==7) $blromawi="VII";
    if ($bl==8) $blromawi="VIII";
    if ($bl==9) $blromawi="IX";
    if ($bl==10) $blromawi="X";
    if ($bl==11) $blromawi="XI";
    if ($bl==12) $blromawi="XII";
    
    return $blromawi;
}

function CariBulanHuruf($bulan){
    $bl=(int)$bulan;
    $blromawi="A";
    if ($bl==1) $blromawi="A";
    if ($bl==2) $blromawi="B";
    if ($bl==3) $blromawi="C";
    if ($bl==4) $blromawi="D";
    if ($bl==5) $blromawi="E";
    if ($bl==6) $blromawi="F";
    if ($bl==7) $blromawi="G";
    if ($bl==8) $blromawi="H";
    if ($bl==9) $blromawi="I";
    if ($bl==10) $blromawi="J";
    if ($bl==11) $blromawi="K";
    if ($bl==12) $blromawi="L";
    
    return $blromawi;
}

function PilCekBox($datanya){
    if (!empty($datanya)){
        $tag = implode(',',$datanya);
        $arr_kata = explode(",",$tag);
        $count_kata = count($arr_kata);
        $jumlah_tag = substr_count($tag, ",") + 1;
        $u=0;
        $unsel="";
        for ($x=0; $x<=$jumlah_tag; $x++){
            if (!empty($arr_kata[$u])){
                $uTag=trim($arr_kata[$u]);
                $unsel=$unsel."'".$uTag."',";
            }
            $u++;
        }
        $mydata="(".substr($unsel,0,strlen($unsel)-1).")";

    }else{
        $mydata="('')";
    }
    return $mydata;
}

function PilCekBoxAndEmpty($datanya){
    if (!empty($datanya)){
        $tag = implode(',',$datanya);
        $arr_kata = explode(",",$tag);
        $count_kata = count($arr_kata);
        $jumlah_tag = substr_count($tag, ",") + 1;
        $u=0;
        $unsel="";
        for ($x=0; $x<=$jumlah_tag; $x++){
            if (isset($arr_kata[$u])){
                $uTag=trim($arr_kata[$u]);
                $unsel=$unsel."'".$uTag."',";
            }
            $u++;
        }
        $mydata="(".substr($unsel,0,strlen($unsel)-1).")";

    }else{
        $mydata="('')";
    }
    return $mydata;
}

function cbLevel1($onchange) {
    $oncha="";
    if (!empty($onchange)) $oncha=" onchange=".$onchange;
    include "koneksimysqli_it.php";
    echo "<select class='form-control' id='cb_level1' name='cb_level1' $oncha>";
    echo "<option value=''>-- Pilih --</option>";
    $tampil=mysqli_query($cnit, "SELECT COA1, NAMA1 FROM dbmaster.coa_level1 order by COA1");
    while($a=mysqli_fetch_array($tampil)){
        echo "<option value='$a[COA1]'>$a[COA1] - $a[NAMA1]</option>";
    }
    echo "</select>";
}

function comboTahap($konek, $pilihan, $includ) {
    $t1=""; $t2="";
    if ($includ=="1") $t1="selected";
    if ($includ=="2") $t2="selected";
    echo "<select class='form-control input-sm' id='e_tahap' name='e_tahap'>";
    if (!empty($pilihan)) echo "<option value='' selected>-- Pilihan --</option>";
    echo "<option value='1' $t1>Tahap 1</option>";
    echo "<option value='2' $t2>Tahap 2</option>";
    echo "</select>";
}
function comboKaryawanAll($konek, $pilihan, $includ, $stsadim, $lvlposisi, $divisi) {
    include $konek."koneksimysqli_it.php";
    $fildiv="";
    if (empty($divisi)){

    }else{
        if ($divisi=="HO")
            $fildiv=" and ifnull(divisiId,'HO') in ('', 'HO') ";
        else
            $fildiv=" and divisiId='$divisi' ";
    }
    
    if ($stsadim=="admin") $fildiv="";
    
    $query="SELECT DISTINCT karyawanId, nama FROM dbmaster.karyawan WHERE 1=1 $fildiv ";
    $query .=" order by nama, karyawanId";       
    $tampil = mysqli_query($cnit, $query);
    if (!empty($pilihan)) echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){ 
        if ($a['karyawanId']==$includ)
            echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
        else
            echo "<option value='$a[karyawanId]'>$a[nama]</option>";
    }
}

function comboKaryawanAktif($konek, $pilihan, $includ, $stsadim, $lvlposisi, $divisi, $karyawannya) {
    include $konek."koneksimysqli_it.php";
    $fildiv="";
    if (empty($divisi)){

    }else{
        if ($divisi=="HO")
            $fildiv=" and ifnull(divisiId,'HO') in ('', 'HO') ";
        else
            $fildiv=" and divisiId='$divisi' ";
    }
    
    if ($stsadim=="admin") $fildiv="";
    $fildiv="";
    
    
    $query="SELECT DISTINCT karyawanId, nama FROM dbmaster.karyawan WHERE 1=1 "
            . " AND jabatanId in (4, 5, 6, 8, 10, 15, 18, 20, 22, 27, 35, 36)  and nama NOT LIKE '%DM - %' and nama NOT LIKE '%NN - %' and nama NOT LIKE '%DR - %' "
            . " AND (ifnull(tglkeluar,'0000-00-00') = '0000-00-00' OR tglkeluar = '' OR karyawanId='$includ') $fildiv ";
    
    if ($lvlposisi=="FF1") $query="SELECT DISTINCT karyawanId, nama FROM dbmaster.karyawan WHERE karyawanId='$karyawannya'";
    if ($lvlposisi=="FF2") {
        $query="select distinct karyawanid karyawanId, nama from dbmaster.v_penempatanmr 
            where icabangid in (select iCabangId from MKT.ispv0 where karyawanId='$karyawannya' and nama not LIKE '%DR - %'
             and nama not LIKE '%DM - %' and nama not LIKE '%NN - %')
            UNION select distinct karyawanId, nama from hrd.karyawan where karyawanId='$karyawannya'";
    }
    
    $query .=" order by nama, karyawanId";       
    $tampil = mysqli_query($cnit, $query);
    if (!empty($pilihan)) echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){ 
        if ($a['karyawanId']==$includ)
            echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
        else
            echo "<option value='$a[karyawanId]'>$a[nama]</option>";
    }
}

function comboKaryawanAktifAll($konek, $pilihan, $includ, $stsadim, $lvlposisi, $divisi, $karyawannya, $filjbt) {
    include $konek."koneksimysqli_it.php";
    $fildiv="";
    if (empty($divisi)){

    }else{
        $fildiv = " AND divisiId in $divisi ";
        /*
        if ($divisi=="HO")
            $fildiv=" and ifnull(divisiId,'HO') in ('', 'HO') ";
        else
            $fildiv=" and divisiId='$divisi' ";
         * 
         */
    }
    
    if ($stsadim=="admin") $fildiv="";
    //$fildiv="";
    
    $efiljbt="";
    if (!empty($filjbt)) $efiljbt=" AND jabatanId in ($filjbt)";
        
    $query="SELECT DISTINCT karyawanId, nama FROM dbmaster.karyawan WHERE 1=1 "
            . " $efiljbt AND nama NOT LIKE '%DM - %' and nama NOT LIKE '%NN - %' and nama NOT LIKE '%DR - %' "
            . " AND (ifnull(tglkeluar,'0000-00-00') = '0000-00-00' OR tglkeluar = '' OR karyawanId='$includ') $fildiv ";
    
    if ($lvlposisi=="FF1") $query="SELECT DISTINCT karyawanId, nama FROM dbmaster.karyawan WHERE karyawanId='$karyawannya'";
    if ($lvlposisi=="FF2") {
        $query="select distinct karyawanid karyawanId, nama from dbmaster.v_penempatanmr 
            where icabangid in (select iCabangId from MKT.ispv0 where karyawanId='$karyawannya' and nama not LIKE '%DR - %'
             and nama not LIKE '%DM - %' and nama not LIKE '%NN - %')
            UNION select distinct karyawanId, nama from hrd.karyawan where karyawanId='$karyawannya'";
    }
    
    $query .=" order by nama, karyawanId";       
    $tampil = mysqli_query($cnit, $query);
    if (!empty($pilihan)) echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){ 
        if ($a['karyawanId']==$includ)
            echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
        else
            echo "<option value='$a[karyawanId]'>$a[nama]</option>";
    }
}

function PilihKaryawanAktif($konek, $adapilihan, $ygdipilih, $hanyaygaktif, $stsadim, $divisi, $lvlposisi, $tampilkanlevelbawahan, $karyawan, $jabatan, $region, $cabang, $area, $hanyasatu) {
    include $konek."koneksimysqli.php";
	$cnit=$cnmy;
    $filterharusada = "";
    if (!empty($ygdipilih)) $filterharusada = " OR karyawanId='$ygdipilih' ";
    
    $filternotlike = "";
    $filterhanyaaktif = "";
    if ($hanyaygaktif=="Y") {
        $filternotlike = " AND karyawanId NOT in (select distinct ifnull(karyawanId,'') from dbmaster.t_karyawanadmin) ";
        if (!empty($filterharusada)) $filterhanyaaktif = " AND (ifnull(tglkeluar,'0000-00-00') = '0000-00-00' OR tglkeluar = '' $filterharusada) $filternotlike ";
        else $filterhanyaaktif = " AND (ifnull(tglkeluar,'0000-00-00') = '0000-00-00' OR tglkeluar = '') $filternotlike ";
    }
    
    $filterdivisi="";
    if (!empty($divisi)) $filterdivisi= " AND divisiId in $divisi ";
    
    $filterelevel = "";
    if ($stsadim != "admin" and $hanyasatu !="Y") {
        if (!empty($cabang)) $tampilkanlevelbawahan = "Y";
        if (!empty($area)) $tampilkanlevelbawahan = "Y";
        
        if ($lvlposisi=="FF1" OR $lvlposisi=="FF2" OR $lvlposisi=="FF3" OR $lvlposisi=="FF4" OR $lvlposisi=="FF5" OR $lvlposisi=="FF6")
            $filterelevel = " AND karyawanId='$karyawan' ";
        
        if ($tampilkanlevelbawahan=="Y") {
            if ($lvlposisi=="FF2") {
                $filterbawahan = "";
                $tampillvl = "select distinct a.karyawanid from MKT.imr0 as a WHERE CONCAT(a.divisiid, a.icabangid, a.areaid) in 
                        (select CONCAT(b.divisiid, b.icabangid, b.areaid) 
                        from MKT.ispv0 b where b.karyawanid='$karyawan')";
            }elseif ($lvlposisi=="FF3" or $lvlposisi=="FF4") {
                $posisilvl = "('FF1', 'FF2')";
                if ($lvlposisi=="FF4") $posisilvl = "('FF1', 'FF2', 'FF3')";
                if ($lvlposisi=="FF5") $posisilvl = "('FF1', 'FF2', 'FF3', 'FF4')";

                $tampillvl = "select distinct a.karyawanid from dbmaster.v_area_cabang_all_divisi as a where CONCAT(a.icabangid)
                    in (select distinct CONCAT(b.icabangid)from dbmaster.v_area_cabang_all_divisi as b 
                    where b.karyawanid='$karyawan') and a.LVLPOSISI in $posisilvl";
            }else{
                if (!empty($cabang)) {
                    $filterarea = "";
                    if (!empty($area)) $filterarea = " AND CONCAT(b.icabangid, b.areaid) in ($area) ";
                    $tampillvl = "select distinct a.karyawanid from dbmaster.v_area_cabang_all_divisi as a where CONCAT(a.icabangid)
                        in (select distinct CONCAT(b.icabangid) from dbmaster.v_area_cabang_all_divisi as b 
                        where b.icabangid in ($cabang) $filterarea) and a.LVLPOSISI in ('FF1', 'FF2', 'FF3', 'FF4', 'FF5', 'FF6')";
                }
            }

            if (!empty($tampillvl)) {
                $tampillvl = mysqli_query($cnit, $tampillvl);
                while($lvl=mysqli_fetch_array($tampillvl)){
                    $filterbawahan = $filterbawahan."'".$lvl['karyawanid']."',";
                }
                if (!empty($filterbawahan)) $filterbawahan = " AND karyawanId in (".$filterbawahan."'".$karyawan."')";
                else $filterbawahan = " AND karyawanId in '".$karyawan."')";

                if (!empty($filterbawahan)) $filterelevel = $filterbawahan;
            }
        }
    }
    
    $filterjabatan="";
    if (!empty($jabatan)) {
        $filterjabatan = " AND karyawanId in (select distinct karyawanId from dbmaster.t_karyawan_posisi where jabatanId in ($jabatan) ";
        if (!empty($region)) $filterjabatan = $filterjabatan." AND region in ($region)";
        $filterjabatan = $filterjabatan." )";
    }
    
    if ($stsadim=="admin") {
        $filterdivisi = "";
        $filterjabatan="";
    }
    
    if ($hanyasatu =="Y") {
        $filterhanyaaktif = "";
        $filterdivisi = "";
        $filterelevel = "";
        $filterjabatan="";
        $adapilihan="";
        if (empty($ygdipilih)) $ygdipilih = $karyawan;
        $filterharusada = " AND (karyawanId='$ygdipilih')";
    }
    
    $query="SELECT DISTINCT karyawanId, nama FROM hrd.karyawan WHERE (1=1 "
            . " $filterhanyaaktif $filterdivisi $filterelevel $filterjabatan) $filterharusada ";
    
    $query .=" order by nama, karyawanId";
    $tampil = mysqli_query($cnit, $query);
    if (!empty($adapilihan)) echo "<option value='' selected>$adapilihan</option>";
    while($a=mysqli_fetch_array($tampil)){ 
        if ($a['karyawanId']==$ygdipilih)
            echo "<option value='$a[karyawanId]' selected>$a[nama]</option>";
        else
            echo "<option value='$a[karyawanId]'>$a[nama]</option>";
    }
    
}

function PilihAreaCabangAll($konek, $adapilihan, $ygdipilih, $hanyaygaktif, $karyawan, $level, $divisi, $cabang, $area) {
    include $konek."koneksimysqli_it.php";
    $karyawan = trim($karyawan);
    $pdivisi = trim($divisi);
    $pelevel = trim($level);
    $fildivi = " and divi='ETH' ";
    if (trim($pdivisi)=="OTC") {
        $fildivi = " and divi='OTC' ";
    }
    $psel = $ygdipilih;
    
    $filterharusada = "";
    if (!empty($ygdipilih)) $filterharusada = " OR CONCAT(icabangid,areaid)='$ygdipilih' ";
    
    $filterhanyaaktif = "";
    if ($hanyaygaktif=="Y") {
        $filterhanyaaktif = " AND aktif='Y' ";
    }
    
    $query = "";
    $ketemu = 0;
    
    if (trim($pdivisi)=="OTC") {
        $query="SELECT DISTINCT icabangid, areaid, nama_area FROM dbmaster.v_area_cabang_all_divisi WHERE (nama_area <> '' AND karyawanid='$karyawan' $fildivi) $filterharusada ";
        $query .=" order by nama_area, areaid";
        $tampil = mysqli_query($cnit, $query);
        $ketemu=  mysqli_num_rows($tampil);
        if ($ketemu==0) $query = "";
        else {
            if (empty($psel)) {
                $tmpl = mysqli_query($cnit, "select CONCAT(iCabangId,areaId) as lcfields from hrd.karyawan WHERE karyawanId='$karyawan'");
                $rt=mysqli_fetch_array($tmpl);
                $cabareaid = $rt['lcfields'];
                $psel = $cabareaid;
            }
        }
        
    }else{
        if (trim(substr($pelevel, 0, 2)=="FF")) {
            if ($pelevel=="FF1" or $pelevel=="FF2") {
                $query="SELECT DISTINCT icabangid, areaid, nama_area FROM dbmaster.v_penempatan_des WHERE (aktif='Y' AND nama_area <> '' AND karyawanid='$karyawan' $fildivi) $filterharusada ";
                $query .=" order by nama_area, areaid";
            }elseif ($pelevel=="FF3" or $pelevel=="FF4") {
                $query="SELECT DISTINCT iCabangId icabangid, areaId areaid, nama nama_area FROM MKT.iarea WHERE (aktif='Y' ";
                $query .=" AND iCabangId in (SELECT DISTINCT icabangid FROM dbmaster.v_penempatan_all WHERE karyawanid='$karyawan')) $filterharusada";
                $query .=" order by nama_area, areaId";
                if (empty($psel)) {
                    $tmpl = mysqli_query($cnit, "select CONCAT(iCabangId,areaId) as lcfields from hrd.karyawan WHERE karyawanId='$karyawan'");
                    $rt=mysqli_fetch_array($tmpl);
                    $cabareaid = $rt['lcfields'];
                    $psel = $cabareaid;
                }
            }
            if (!empty($query)) {
                $tampil = mysqli_query($cnit, $query);
                $ketemu=  mysqli_num_rows($tampil);
                if ($ketemu==0) $query = "";
            }
        }
    }
    
    if (empty($query)) {
        if (trim($pdivisi)=="OTC") {
            $query="SELECT DISTINCT icabangid_o icabangid, areaid_o areaid, nama nama_area FROM MKT.iarea_o WHERE (aktif='Y') $filterharusada ";
            $query .=" order by nama, areaid_o";
            if (empty($psel)) $psel = "0000000007"."0000000001";
        }else{
            $query="SELECT DISTINCT iCabangId icabangid, areaId areaid, nama nama_area FROM MKT.iarea WHERE (1=1 $filterhanyaaktif "
                    . " ) $filterharusada ";
            $query .=" order by nama_area, areaId";
        }
    }
    
    $tampil = mysqli_query($cnit, $query);
    $ketemu=  mysqli_num_rows($tampil);
    
    if ($ketemu>0) {
        if (!empty($adapilihan)) echo "<option value='' selected>$adapilihan</option>";
        while($a=mysqli_fetch_array($tampil)){
            if (!empty($a['nama_area'])) {
                $cabangarea = $a['icabangid'].",".$a['areaid'];
                $namacabangarea = $a['nama_area'];
                if ($a['icabangid'].$a['areaid']==$psel)
                    echo "<option value='$cabangarea' selected>$namacabangarea</option>";
                elseif (trim(substr($pelevel, 0, 2)=="FF") AND empty($psel))
                    echo "<option value='$cabangarea' selected>$namacabangarea</option>";
                else
                    echo "<option value='$cabangarea'>$a[nama_area]</option>";
            }
        }
    }else
        echo "<option value='' selected>blank_</option>";
}

function cBoxIsiKodePosting($konek){
    include $konek."koneksimysqli_it.php";
    $sql=mysqli_query($cnit, "select kodeid,nama,divprodid from dbmaster.br_kode order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[kodeid]' name=chkbox_kode[] checked> $Xt[kodeid] - $Xt[nama]<br/>";
    }
}

function cBoxIsiKodePostingOTC($konek){
    include $konek."koneksimysqli.php";
    $sql=mysqli_query($cnmy, "select distinct kodeid, nama, subpost from dbmaster.brkd_otc order by kodeid");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[kodeid]' name=chkbox_kodeotc[] checked> $Xt[kodeid] - $Xt[nama]<br/>";
    }
}

function cBoxIsiDivisiProdFilter($konek, $onclick, $karyawan, $stsadim, $lvlposisi, $divisi, $tipe){
    $onc="";
    if (!empty($onclick)) $onc=" onclick=".$onclick."";
    include $konek."koneksimysqli_it.php";
    $sql=mysqli_query($cnit, "SELECT DivProdId, nama FROM dbmaster.divprod where br='Y' order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[DivProdId]' name='chkbox_divisiprod[]' $onc checked> $Xt[DivProdId]<br/>";
    }
}

function ComboSelectIsiDivisiProdFilter($konek, $onclick, $karyawan, $stsadim, $lvlposisi, $divisi, $pilih){
    $onc="";
    if (!empty($onclick)) $onc=" onchange=".$onclick."";
    include $konek."koneksimysqli.php";
	$cnit=$cnmy;
    $sql=mysqli_query($cnit, "SELECT DivProdId, nama FROM dbmaster.divprod where br='Y' order by nama");
    echo "<select class='form-control input-sm' id='cb_divisi' name='cb_divisi' $onc>";
    echo "<option value=''>-- Pilihan --</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        if ($Xt['DivProdId']==$pilih)
            echo "<option value='$Xt[DivProdId]' selected>$Xt[DivProdId]</option>";
        else
            echo "<option value='$Xt[DivProdId]'>$Xt[DivProdId]</option>";
    }
    echo "</select>";
}


function cBoxIsiCabangFilter($konek, $onclick, $karyawan, $stsadim, $lvlposisi, $divisi, $tipe){
    $onc="";
    if (!empty($onclick)) $onc=" onclick=".$onclick."";
    include $konek."koneksimysqli_it.php";
    $sql=mysqli_query($cnit, "SELECT distinct iCabangId, nama from dbmaster.icabang order by nama");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[iCabangId]' name=chkbox_cabang[] $onc checked> $Xt[nama]<br/>";
    }
}

function cBoxIsiCabangOFilter($konek, $onclick, $karyawan, $stsadim, $lvlposisi, $divisi, $tipe){
    $onc="";
    if (!empty($onclick)) $onc=" onclick=".$onclick."";
    include $konek."koneksimysqli_it.php";
    $sql=mysqli_query($cnit, "select icabangid_o, nama from dbmaster.v_icabang_o");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[icabangid_o]' name=chkbox_cabango[] $onc checked> $Xt[nama]<br/>";
    }
}

function cBoxLampiranAll() {
    echo "<select class='form-control' name='e_lampiran' id='e_lampiran' style='width: 100%;'>";
    echo "<option value='' selected>All</option>";
    echo "<option value='Y'>Y</option>";
    echo "<option value='N'>N</option>";
    echo "</select>";
}

function cBoxCAAll() {
    echo "<select class='form-control' name='e_ca' id='e_ca' style='width: 100%;'>";
    echo "<option value='' selected>All</option>";
    echo "<option value='Y'>Y</option>";
    echo "<option value='N'>N</option>";
    echo "</select>";
}

function cBoxVIAAll() {
    echo "<select class='form-control' name='e_via' id='e_via' style='width: 100%;'>";
    echo "<option value='' selected>All</option>";
    echo "<option value='Y'>Y</option>";
    echo "<option value='N'>N</option>";
    echo "</select>";
}

function cBoxIsiDistributor($konek){
    include $konek."koneksimysqli_it.php";
    $sql=mysqli_query($cnit, "SELECT distinct Distid, nama, alamat1 from dbmaster.distrib0 order by nama, Distid");
    while ($Xt=mysqli_fetch_array($sql)){
        echo "<input type=checkbox value='$Xt[Distid]' name=chkbox_dist[] checked> $Xt[nama]<br/>";
    }
}

function cComboDistibutor($konek, $selec){
    include $konek."koneksimysqli_it.php";
    $fsel="";
    if (isset($selec)) {
        $fsel=$selec;
    }
    $sql=mysqli_query($cnit, "SELECT distinct Distid, nama, alamat1 from dbmaster.distrib0 order by nama, Distid");
    echo "<option value=''>--Pilih--</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        if ((int)$Xt['Distid']==(int)$fsel)
            echo "<option value='$Xt[Distid]' selected>$Xt[nama]</option>";
        else
            echo "<option value='$Xt[Distid]'>$Xt[nama]</option>";
    }
}

function cComboDistibutorHanya($konek, $selec, $pinsel){
    include $konek."koneksimysqli_it.php";
    $fsel="";
    if (isset($selec)) {
        $fsel=$selec;
    }
    $sql=mysqli_query($cnit, "SELECT distinct Distid, nama, alamat1 from MKT.distrib0 WHERE "
            . " Distid IN $pinsel order by nama, Distid");
    echo "<option value=''>--Pilih--</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        if ((int)$Xt['Distid']==(int)$fsel)
            echo "<option value='$Xt[Distid]' selected>$Xt[nama]</option>";
        else
            echo "<option value='$Xt[Distid]'>$Xt[nama]</option>";
    }
}

function cComboDistibutorHanyaCnNew($konek, $selec, $pinsel){
    include $konek."koneksimysqli_ms.php";
    $fsel="";
    if (isset($selec)) {
        $fsel=$selec;
    }
    $sql=mysqli_query($cnms, "SELECT distinct Distid as Distid, nama as nama, alamat1 from sls.distrib0 WHERE "
            . " Distid IN $pinsel order by nama, Distid");
    echo "<option value=''>--Pilih--</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        if ((int)$Xt['Distid']==(int)$fsel)
            echo "<option value='$Xt[Distid]' selected>$Xt[nama]</option>";
        else
            echo "<option value='$Xt[Distid]'>$Xt[nama]</option>";
    }
    mysqli_close($cnms);
}

function cComboDistibutorHanyaCnMs($konek, $selec, $pinsel){
    include $konek."koneksimysqli.php";
    $fsel="";
    if (isset($selec)) {
        $fsel=$selec;
    }
    $sql=mysqli_query($cnmy, "SELECT distinct Distid as Distid, nama as nama, alamat1 from MKT.distrib0 WHERE "
            . " Distid IN $pinsel order by nama, Distid");
    echo "<option value=''>--Pilih--</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        if ((int)$Xt['Distid']==(int)$fsel)
            echo "<option value='$Xt[Distid]' selected>$Xt[nama]</option>";
        else
            echo "<option value='$Xt[Distid]'>$Xt[nama]</option>";
    }
    mysqli_close($cnmy);
}

function cComboKodePosting($konek, $selec){
    include $konek."koneksimysqli_it.php";
    $fsel="";
    if (isset($selec)) {
        $fsel=$selec;
    }
    $sql=mysqli_query($cnit, "SELECT distinct kodeid, nama, divprodid from dbmaster.br_kode where br<>'N' "
            . " and divprodid not in ('OTC') order by divprodid, nama, kodeid");
    echo "<option value=''>--Pilih--</option>";
    while ($Xt=mysqli_fetch_array($sql)){
        if ((int)$Xt['kodeid']==(int)$fsel)
            echo "<option value='$Xt[kodeid]' selected>$Xt[divprodid] - $Xt[nama]</option>";
        else
            echo "<option value='$Xt[kodeid]'>$Xt[divprodid] - $Xt[nama]</option>";
    }
}

function SaveDataMS($nmdb, $namatabel){
    include "../../config/koneksimysqli.php";
    $berhasil="";
    
    $query = "CREATE TABLE dbtemp.$namatabel (select * from it_$nmdb.$namatabel)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $berhasil="gagal"; }


    $query = "CREATE TABLE IF NOT EXISTS $nmdb.$namatabel (select * from it_$nmdb.$namatabel limit 1)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $berhasil="gagal"; }

    $query = "delete from $nmdb.$namatabel";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $berhasil="gagal"; }

    $query = "INSERT INTO $nmdb.$namatabel select * from dbtemp.$namatabel";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $berhasil="gagal"; }

    $query = "DROP TABLE dbtemp.$namatabel";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan."<br/>drop table dbtemp ".$namatabel; $berhasil="gagal"; }
    
    return $berhasil;
}
?>
