<?php

// viewdata.php hitungtotalcekbox, simpan
//viewdatatable
function CariDataSPGGajiTJ($bulan, $pidcabang, $pnoid, $status, $periodeins) {
    include "../../config/koneksimysqli.php";
    $fcabang = " AND a.icabangid = '$pidcabang' ";
    if (empty($pidcabang)) $fcabang = " AND IFNULL(a.icabangid,'') = '' ";
    
    if ($pidcabang=="JKT_MT") {
        $fcabang = " AND IFNULL(a.icabangid,'') = '0000000007' AND a.alokid='001' ";
    }elseif ($pidcabang=="JKT_RETAIL") {
        $fcabang = " AND IFNULL(a.icabangid,'') = '0000000007' AND a.alokid='002' ";
    }
    
    
    $filspgnya = "";
    if (!empty($pnoid)) {
        if ($pidcabang=="JKT_MT" OR $pidcabang=="JKT_RETAIL") {
            $filspgnya = " AND CONCAT(IFNULL(a.id_spg,''),IFNULL(a.icabangid,''),IFNULL(a.alokid,'')) IN $pnoid ";
        }else{
            $filspgnya = " AND CONCAT(IFNULL(a.id_spg,''),IFNULL(a.icabangid,'')) IN $pnoid ";
        }
    }
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSPGCR01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSPGCR02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSPGCR03_".$userid."_$now ";
    $tmp04 =" dbtemp.DSPGCR04_".$userid."_$now ";
    
    //apv 2
    $approve2_sel = " AND (IFNULL(a.apvtgl2,'') = '' OR IFNULL(a.apvtgl2,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
    $filststusapv = "";
    if ((INT)$status==1) {
        $filststusapv = " $approve2_sel AND (IFNULL(a.apvtgl1,'') = '' OR IFNULL(a.apvtgl1,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
    }elseif ((INT)$status==2) {
        $filststusapv = " $approve2_sel AND (IFNULL(a.apvtgl1,'') <> '' AND IFNULL(a.apvtgl1,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') AND IFNULL(sts,'')<>'P' ";
    }elseif ((INT)$status==3) {        
        $filststusapv = " AND (IFNULL(a.apvtgl1,'') <> '' AND IFNULL(a.apvtgl1,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') AND IFNULL(sts,'')='P' ";
    }elseif ((INT)$status==4) {
        $fmgr = " AND  (IFNULL(a.apvtgl4,'') = '' OR IFNULL(a.apvtgl4,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
        $fsvp = " OR ( (IFNULL(a.apvtgl3,'') <> '' AND IFNULL(a.apvtgl3,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') )";
        $filststusapv = " $fmgr AND ( (IFNULL(a.apvtgl2,'') <> '' AND IFNULL(a.apvtgl2,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') $fsvp) ";
    }elseif ((INT)$status==5) {
        $filststusapv = " AND (IFNULL(a.apvtgl4,'') <> '' AND IFNULL(a.apvtgl4,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') ";
    }
    //SPG
    $query = "select a.jharikerjasistem, a.idbrspg, a.periode, a.periode_insentif, a.id_spg, b.nama, b.penempatan, a.icabangid, a.alokid, a.areaid, a.jabatid, a.id_zona, a.jml_harikerja, a.jml_sakit, a.jml_izin, a.jml_alpa, a.jml_uc,  
        CAST(0 AS DECIMAL(20,2)) gaji, 
        CAST(0 AS DECIMAL(20,2)) umakan, CAST(0 AS DECIMAL(20,2)) sewakendaraan, CAST(0 AS DECIMAL(20,2)) pulsa, CAST(0 AS DECIMAL(20,2)) bbm, 
        CAST(0 AS DECIMAL(20,2)) parkir, CAST(0 AS DECIMAL(20,2)) lain, 
        insentif, insentif_tambahan, CAST(0 AS DECIMAL(20,2)) tmakan, CAST(0 AS DECIMAL(20,2)) total,
        a.keterangan, a.sts, a.apvtgl1, a.apvtgl2, a.apvtgl3, CAST(0 AS DECIMAL(20,2)) ngaji, CAST(0 AS DECIMAL(20,2)) ntunjangan,
        CAST(0 AS DECIMAL(20,2)) tot_tujangan, 
        CAST(0 AS DECIMAL(20,2)) nhk, CAST(0 AS DECIMAL(20,2)) njmlharisistem
        FROM dbmaster.t_spg_gaji_br0 a 
        JOIN MKT.spg b on a.id_spg=b.id_spg where DATE_FORMAT(a.periode,'%Y%m')='$bulan' $fcabang $filspgnya $filststusapv ";
    
    $query = "create  table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN nama_jabatan char(150), ADD COLUMN nama_area CHAR(200), ADD COLUMN nama_zona CHAR(100)";
    mysqli_query($cnmy, $query);
    
    $query = "UPDATE $tmp01 a SET a.nama_jabatan=(select b.nama_jabatan from dbmaster.t_spg_jabatan b WHERE a.jabatid=b.jabatid)";
    mysqli_query($cnmy, $query);
    
    $query = "UPDATE $tmp01 a SET a.nama_area=(select b.nama from MKT.iarea_o b WHERE a.areaid=b.areaid_o AND a.icabangid=b.icabangid_o)";
    mysqli_query($cnmy, $query);
    
    $query = "UPDATE $tmp01 a SET a.nama_zona=(select b.nama_zona from dbmaster.t_zona b WHERE a.id_zona=b.id_zona)";
    mysqli_query($cnmy, $query);
    
    
    //exit;
    $ipilihcab1=" a.icabangid ";
    $ipilihcab2=" IFNULL(icabangid,'') ";
    
    $ipilihcab3=" CONCAT(a.id_spg,a.icabangid) ";
    $ipilihcab4=" IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')),'') ";
    
    $ipilihupdate = "";
    
    if ($pidcabang=="JKT_MT" OR $pidcabang=="JKT_RETAIL") {
        $ipilihcab1=" CONCAT(a.icabangid,a.alokid) ";
        $ipilihcab2=" CONCAT(IFNULL(icabangid,''),IFNULL(alokid,'')) ";
        
        $ipilihcab3=" CONCAT(a.id_spg,a.icabangid,a.alokid) ";
        $ipilihcab4=" IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,''),IFNULL(alokid,'')),'') ";
        
        $ipilihupdate = " AND a.alokid=b.alokid ";
    }
    
    
    //gaji pokok
    $query = "select a.* from dbmaster.t_spg_gaji_area_zona a WHERE CONCAT(a.icabangid,a.areaid) IN "
            . " (select CONCAT(icabangid,areaid) from $tmp01) AND "
            . " DATE_FORMAT(a.bulan,'%Y-%m') = (select MAX(DATE_FORMAT(b.bulan,'%Y-%m')) FROM dbmaster.t_spg_gaji_area_zona b WHERE 
                a.icabangid=b.icabangid AND a.areaid=b.areaid AND a.id_zona=b.id_zona)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    //UPDATE GAJI CABANG
    $query = "UPDATE $tmp01 a SET a.gaji=IFNULL((SELECT b.gaji FROM $tmp02 b WHERE a.icabangid=b.icabangid AND a.areaid=b.areaid),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    //uang makan
    $query = "select a.* from dbmaster.t_spg_gaji_zona_jabatan a WHERE CONCAT(a.id_zona,a.jabatid) IN "
            . " (select CONCAT(id_zona,jabatid) from $tmp01) AND "
            . " DATE_FORMAT(a.bulan,'%Y-%m') = (select MAX(DATE_FORMAT(b.bulan,'%Y-%m')) FROM dbmaster.t_spg_gaji_zona_jabatan b WHERE 
                a.id_zona=b.id_zona AND a.jabatid=b.jabatid)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.umakan=IFNULL((SELECT b.umakan FROM $tmp02 b WHERE a.id_zona=b.id_zona AND a.jabatid=b.jabatid),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    //tunjangan
    $query = "select a.* from dbmaster.t_spg_gaji_jabatan a WHERE "
            . " DATE_FORMAT(a.bulan,'%Y-%m') = (select MAX(DATE_FORMAT(b.bulan,'%Y-%m')) FROM dbmaster.t_spg_gaji_jabatan b WHERE 
                a.jabatid=b.jabatid)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "UPDATE $tmp01 a SET a.sewakendaraan=IFNULL((SELECT b.sewakendaraan FROM $tmp02 b WHERE a.jabatid=b.jabatid),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.pulsa=IFNULL((SELECT b.pulsa FROM $tmp02 b WHERE a.jabatid=b.jabatid),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.bbm=IFNULL((SELECT b.bbm FROM $tmp02 b WHERE a.jabatid=b.jabatid),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.parkir=IFNULL((SELECT b.parkir FROM $tmp02 b WHERE a.jabatid=b.jabatid),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    //insentif
    $query ="select * from fe_it.t_spg_incentive WHERE REPLACE(inct_bulan,'-','')='$periodeins' AND "
            . " id_spg IN (SELECT distinct id_spg FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 SET tmakan=IFNULL(jml_harikerja,0)*IFNULL(umakan,0)";
    mysqli_query($cnmy, $query);
    
    if ($status=="1") {
        $query = "UPDATE $tmp01 a SET a.insentif=(select sum(b.inct_total) FROM $tmp02 b WHERE a.id_spg=b.id_spg)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    //insentif_tambahan tambahn insentif
    //$query = "UPDATE $tmp01 a SET a.insentif_tambahan='0'";
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    //cek jumlah hari kerja
    $pjmlkerja = 0;
    $pjmlkerja_aspr = 0;
    $query = "select * from dbmaster.t_spg_jmlharikerja WHERE DATE_FORMAT(periode,'%Y%m')='$bulan'";
    $tampilnp = mysqli_query($cnmy, $query);
    while ($np= mysqli_fetch_array($tampilnp)) {
        if (!empty($np['jumlah']))$pjmlkerja=$np['jumlah'];
        if (!empty($np['jml_aspr']))$pjmlkerja_aspr=$np['jml_aspr'];
    }
    //if ((double)$pjmlkerja>0) {
        mysqli_query($cnmy, "UPDATE $tmp01 SET nhk=IFNULL(jml_harikerja,0)+IFNULL(jml_sakit,0)");
        //mysqli_query($cnmy, "UPDATE $tmp01 SET njmlharisistem='$pjmlkerja'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET njmlharisistem=jharikerjasistem");
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET tot_tujangan=IFNULL(sewakendaraan,0)+IFNULL(pulsa,0)+IFNULL(bbm,0)+IFNULL(parkir,0)");
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET ngaji=gaji, ntunjangan=tot_tujangan");
        
        
        // perhitungan proporsional
        $query= "UPDATE $tmp01 SET ngaji=( (IFNULL(nhk,0) + IFNULL(jml_uc,0) )/IFNULL(njmlharisistem,0))*IFNULL(gaji,0), "
                . " ntunjangan=IFNULL(tot_tujangan,0), "
                . " umakan=IFNULL(umakan,0), "
                . " tmakan=IFNULL(tmakan,0), "
                . " sewakendaraan=( (IFNULL(nhk,0) + IFNULL(jml_uc,0) )/IFNULL(njmlharisistem,0))*IFNULL(sewakendaraan,0), "
                . " pulsa=( (IFNULL(nhk,0) + IFNULL(jml_uc,0) )/IFNULL(njmlharisistem,0))*IFNULL(pulsa,0), "
                . " bbm=( (IFNULL(nhk,0) + IFNULL(jml_uc,0) )/IFNULL(njmlharisistem,0))*IFNULL(bbm,0), "
                . " parkir=( (IFNULL(nhk,0) + IFNULL(jml_uc,0) )/IFNULL(njmlharisistem,0))*IFNULL(parkir,0) "
                . " WHERE ( ( IFNULL(nhk,0) + IFNULL(jml_uc,0) ) < IFNULL(njmlharisistem,0) )";
        
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET ntunjangan=IFNULL(sewakendaraan,0)+IFNULL(pulsa,0)+IFNULL(bbm,0)+IFNULL(parkir,0)");
        
        
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET gaji=IFNULL(ngaji,0)");
        
    //}
    
    
    //$query = "UPDATE $tmp01 SET total=IFNULL(insentif_tambahan,0)+IFNULL(insentif,0)+IFNULL(tmakan,0)+IFNULL(gaji,0)+IFNULL(sewakendaraan,0)+IFNULL(pulsa,0)+IFNULL(bbm,0)+IFNULL(parkir,0)+IFNULL(lain,0)";
    $query = "UPDATE $tmp01 SET total=IFNULL(insentif,0)+IFNULL(insentif_tambahan,0)+IFNULL(insentif,0)+IFNULL(gaji,0)+IFNULL(ntunjangan,0)+IFNULL(lain,0)+IFNULL(tmakan,0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    //$tmp10 =" dbtemp.DSPGCR10_".$userid."_$now ";
    //mysqli_query($cnmy, "Create table $tmp10 select * from $tmp01");
    
    return $tmp01;
}

// viewdata.php hitungtotalcekbox, simpan
//viewdatatable
function CariDataSPG($bulan, $pidcabang, $pnoid, $status, $periodeins) {
    include "../../config/koneksimysqli.php";
    $fcabang = " AND a.icabangid = '$pidcabang' ";
    if (empty($pidcabang)) $fcabang = " AND IFNULL(a.icabangid,'') = '' ";
    
    if ($pidcabang=="JKT_MT") {
        $fcabang = " AND IFNULL(a.icabangid,'') = '0000000007' AND a.alokid='001' ";
    }elseif ($pidcabang=="JKT_RETAIL") {
        $fcabang = " AND IFNULL(a.icabangid,'') = '0000000007' AND a.alokid='002' ";
    }
    
    
    $filspgnya = "";
    if (!empty($pnoid)) {
        if ($pidcabang=="JKT_MT" OR $pidcabang=="JKT_RETAIL") {
            $filspgnya = " AND CONCAT(IFNULL(a.id_spg,''),IFNULL(a.icabangid,''),IFNULL(a.alokid,'')) IN $pnoid ";
        }else{
            $filspgnya = " AND CONCAT(IFNULL(a.id_spg,''),IFNULL(a.icabangid,'')) IN $pnoid ";
        }
    }
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSPGCR01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSPGCR02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSPGCR03_".$userid."_$now ";
    $tmp04 =" dbtemp.DSPGCR04_".$userid."_$now ";
    
    //apv 2
    $approve2_sel = " AND (IFNULL(a.apvtgl2,'') = '' OR IFNULL(a.apvtgl2,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
    $filststusapv = "";
    if ((INT)$status==1) {
        $filststusapv = " $approve2_sel AND (IFNULL(a.apvtgl1,'') = '' OR IFNULL(a.apvtgl1,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
    }elseif ((INT)$status==2) {
        $filststusapv = " $approve2_sel AND (IFNULL(a.apvtgl1,'') <> '' AND IFNULL(a.apvtgl1,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') AND IFNULL(sts,'')<>'P' ";
    }elseif ((INT)$status==3) {        
        $filststusapv = " AND (IFNULL(a.apvtgl1,'') <> '' AND IFNULL(a.apvtgl1,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') AND IFNULL(sts,'')='P' ";
    }elseif ((INT)$status==4) {
        $fmgr = " AND  (IFNULL(a.apvtgl4,'') = '' OR IFNULL(a.apvtgl4,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
        $fsvp = " OR ( (IFNULL(a.apvtgl3,'') <> '' AND IFNULL(a.apvtgl3,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') )";
        $filststusapv = " $fmgr AND ( (IFNULL(a.apvtgl2,'') <> '' AND IFNULL(a.apvtgl2,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') $fsvp) ";
    }elseif ((INT)$status==5) {
        $filststusapv = " AND (IFNULL(a.apvtgl4,'') <> '' AND IFNULL(a.apvtgl4,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') ";
    }
    //SPG
    $query = "select a.idbrspg, a.periode, a.periode_insentif, a.id_spg, b.nama, b.penempatan, a.icabangid, a.alokid, a.jml_harikerja, a.jml_sakit, a.jml_izin, a.jml_alpa, a.jml_uc,  
        CAST(0 AS DECIMAL(20,2)) gaji, 
        CAST(0 AS DECIMAL(20,2)) umakan, CAST(0 AS DECIMAL(20,2)) sewakendaraan, CAST(0 AS DECIMAL(20,2)) pulsa, CAST(0 AS DECIMAL(20,2)) bbm, 
        CAST(0 AS DECIMAL(20,2)) parkir, CAST(0 AS DECIMAL(20,2)) lain, 
        insentif, insentif_tambahan, CAST(0 AS DECIMAL(20,2)) tmakan, CAST(0 AS DECIMAL(20,2)) total,
        a.keterangan, a.sts, a.apvtgl1, a.apvtgl2, a.apvtgl3, CAST(0 AS DECIMAL(20,2)) ngaji, CAST(0 AS DECIMAL(20,2)) ntunjangan,
        CAST(0 AS DECIMAL(20,2)) tot_tujangan, 
        CAST(0 AS DECIMAL(20,2)) nhk, CAST(0 AS DECIMAL(20,2)) njmlharisistem
        FROM dbmaster.t_spg_gaji_br0 a 
        JOIN MKT.spg b on a.id_spg=b.id_spg where DATE_FORMAT(a.periode,'%Y%m')='$bulan' $fcabang $filspgnya $filststusapv ";
    
    $query = "create  table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    //exit;
    $ipilihcab1=" a.icabangid ";
    $ipilihcab2=" IFNULL(icabangid,'') ";
    
    $ipilihcab3=" CONCAT(a.id_spg,a.icabangid) ";
    $ipilihcab4=" IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')),'') ";
    
    $ipilihupdate = "";
    
    if ($pidcabang=="JKT_MT" OR $pidcabang=="JKT_RETAIL") {
        $ipilihcab1=" CONCAT(a.icabangid,a.alokid) ";
        $ipilihcab2=" CONCAT(IFNULL(icabangid,''),IFNULL(alokid,'')) ";
        
        $ipilihcab3=" CONCAT(a.id_spg,a.icabangid,a.alokid) ";
        $ipilihcab4=" IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,''),IFNULL(alokid,'')),'') ";
        
        $ipilihupdate = " AND a.alokid=b.alokid ";
    }
    
    
    //gaji per cabang
    $query = "select a.* from dbmaster.t_spg_gaji_cabang a 
        WHERE a.periode IN (SELECT MAX(b.periode) FROM dbmaster.t_spg_gaji_cabang b WHERE a.icabangid=b.icabangid) 
        AND $ipilihcab1 IN (SELECT DISTINCT $ipilihcab2 FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    //gaji per spg
    $query = "select a.* from dbmaster.t_spg_gaji a 
        WHERE a.periode IN (SELECT MAX(b.periode) FROM dbmaster.t_spg_gaji b WHERE DATE_FORMAT(b.periode,'%Y%m')>='$bulan' AND a.icabangid=b.icabangid AND a.id_spg=b.id_spg $ipilihupdate)
        AND $ipilihcab3 IN (SELECT DISTINCT $ipilihcab4 FROM $tmp01)";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select * from $tmp03";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    //UPDATE GAJI CABANG
    $query = "UPDATE $tmp01 a SET a.gaji=IFNULL((SELECT b.gaji FROM $tmp02 b WHERE a.icabangid=b.icabangid $ipilihupdate),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.umakan=IFNULL((SELECT b.umakan FROM $tmp02 b WHERE a.icabangid=b.icabangid $ipilihupdate),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.sewakendaraan=IFNULL((SELECT b.sewakendaraan FROM $tmp02 b WHERE a.icabangid=b.icabangid $ipilihupdate),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.pulsa=IFNULL((SELECT b.pulsa FROM $tmp02 b WHERE a.icabangid=b.icabangid $ipilihupdate),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.bbm=IFNULL((SELECT b.bbm FROM $tmp02 b WHERE a.icabangid=b.icabangid $ipilihupdate),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.parkir=IFNULL((SELECT b.parkir FROM $tmp02 b WHERE a.icabangid=b.icabangid $ipilihupdate),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.lain=IFNULL((SELECT b.lain FROM $tmp02 b WHERE a.icabangid=b.icabangid $ipilihupdate),0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    
    
    //UPDATE GAJI PER SPG
    $query = "UPDATE $tmp01 a SET a.gaji=IFNULL((SELECT b.gaji FROM $tmp03 b WHERE a.icabangid=b.icabangid AND a.id_spg=b.id_spg $ipilihupdate) ,0)"
            . " WHERE CONCAT(a.id_spg,a.icabangid) IN (SELECT DISTINCT IFNULL(CONCAT(IFNULL(c.id_spg,''),IFNULL(c.icabangid,'')),'') FROM $tmp04 c)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $query = "UPDATE $tmp01 a SET a.umakan=IFNULL((SELECT b.umakan FROM $tmp03 b WHERE a.icabangid=b.icabangid AND a.id_spg=b.id_spg $ipilihupdate),0)"
            . " WHERE CONCAT(a.id_spg,a.icabangid) IN (SELECT DISTINCT IFNULL(CONCAT(IFNULL(c.id_spg,''),IFNULL(c.icabangid,'')),'') FROM $tmp04 c)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.sewakendaraan=IFNULL((SELECT b.sewakendaraan FROM $tmp03 b WHERE a.icabangid=b.icabangid AND a.id_spg=b.id_spg $ipilihupdate),0)"
            . " WHERE CONCAT(a.id_spg,a.icabangid) IN (SELECT DISTINCT IFNULL(CONCAT(IFNULL(c.id_spg,''),IFNULL(c.icabangid,'')),'') FROM $tmp04 c)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.pulsa=IFNULL((SELECT b.pulsa FROM $tmp03 b WHERE a.icabangid=b.icabangid AND a.id_spg=b.id_spg $ipilihupdate),0)"
            . " WHERE CONCAT(a.id_spg,a.icabangid) IN (SELECT DISTINCT IFNULL(CONCAT(IFNULL(c.id_spg,''),IFNULL(c.icabangid,'')),'') FROM $tmp04 c)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.bbm=IFNULL((SELECT b.bbm FROM $tmp03 b WHERE a.icabangid=b.icabangid AND a.id_spg=b.id_spg $ipilihupdate),0)"
            . " WHERE CONCAT(a.id_spg,a.icabangid) IN (SELECT DISTINCT IFNULL(CONCAT(IFNULL(c.id_spg,''),IFNULL(c.icabangid,'')),'') FROM $tmp04 c)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.parkir=IFNULL((SELECT b.parkir FROM $tmp03 b WHERE a.icabangid=b.icabangid AND a.id_spg=b.id_spg $ipilihupdate),0)"
            . " WHERE CONCAT(a.id_spg,a.icabangid) IN (SELECT DISTINCT IFNULL(CONCAT(IFNULL(c.id_spg,''),IFNULL(c.icabangid,'')),'') FROM $tmp04 c)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a SET a.lain=IFNULL((SELECT b.lain FROM $tmp03 b WHERE a.icabangid=b.icabangid AND a.id_spg=b.id_spg $ipilihupdate),0)"
            . " WHERE CONCAT(a.id_spg,a.icabangid) IN (SELECT DISTINCT IFNULL(CONCAT(IFNULL(c.id_spg,''),IFNULL(c.icabangid,'')),'') FROM $tmp04 c)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
    
    //insentif
    $query ="select * from fe_it.t_spg_incentive WHERE REPLACE(inct_bulan,'-','')='$periodeins' AND "
            . " id_spg IN (SELECT distinct id_spg FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 SET tmakan=IFNULL(jml_harikerja,0)*IFNULL(umakan,0)";
    mysqli_query($cnmy, $query);
    
    if ($status=="1") {
        $query = "UPDATE $tmp01 a SET a.insentif=(select sum(b.inct_total) FROM $tmp02 b WHERE a.id_spg=b.id_spg)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    //insentif_tambahan tambahn insentif
    //$query = "UPDATE $tmp01 a SET a.insentif_tambahan='0'";
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    //cek jumlah hari kerja
    $pjmlkerja = 0;
    $query = "select * from dbmaster.t_spg_jmlharikerja WHERE DATE_FORMAT(periode,'%Y%m')='$bulan'";
    $tampilnp = mysqli_query($cnmy, $query);
    while ($np= mysqli_fetch_array($tampilnp)) {
        if (!empty($np['jumlah']))$pjmlkerja=$np['jumlah'];
    }
    if ((double)$pjmlkerja>0) {
        mysqli_query($cnmy, "UPDATE $tmp01 SET nhk=IFNULL(jml_harikerja,0)+IFNULL(jml_sakit,0)");
        mysqli_query($cnmy, "UPDATE $tmp01 SET njmlharisistem='$pjmlkerja'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET tot_tujangan=IFNULL(sewakendaraan,0)+IFNULL(pulsa,0)+IFNULL(bbm,0)+IFNULL(parkir,0)");
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET ngaji=gaji, ntunjangan=tot_tujangan");
        
        
        
        $query= "UPDATE $tmp01 SET ngaji=(IFNULL(nhk,0)/IFNULL(njmlharisistem,0))*IFNULL(gaji,0), "
                . " ntunjangan=(IFNULL(nhk,0)/IFNULL(njmlharisistem,0))*IFNULL(tot_tujangan,0), "
                . " umakan=(IFNULL(nhk,0)/IFNULL(njmlharisistem,0))*IFNULL(umakan,0), "
                . " tmakan=(IFNULL(nhk,0)/IFNULL(njmlharisistem,0))*IFNULL(tmakan,0), "
                . " sewakendaraan=(IFNULL(nhk,0)/IFNULL(njmlharisistem,0))*IFNULL(sewakendaraan,0), "
                . " pulsa=(IFNULL(nhk,0)/IFNULL(njmlharisistem,0))*IFNULL(pulsa,0), "
                . " bbm=(IFNULL(nhk,0)/IFNULL(njmlharisistem,0))*IFNULL(bbm,0), "
                . " parkir=(IFNULL(nhk,0)/IFNULL(njmlharisistem,0))*IFNULL(parkir,0) "
                . " WHERE (IFNULL(nhk,0) < IFNULL(njmlharisistem,0))";
        
        $query= "UPDATE $tmp01 SET ngaji=( (IFNULL(nhk,0) + IFNULL(jml_uc,0) )/IFNULL(njmlharisistem,0))*IFNULL(gaji,0), "
                . " ntunjangan=IFNULL(tot_tujangan,0), "
                . " umakan=IFNULL(umakan,0), "
                . " tmakan=IFNULL(tmakan,0), "
                . " sewakendaraan=IFNULL(sewakendaraan,0), "
                . " pulsa=IFNULL(pulsa,0), "
                . " bbm=IFNULL(bbm,0), "
                . " parkir=IFNULL(parkir,0) "
                . " WHERE ( ( IFNULL(nhk,0) + IFNULL(jml_uc,0) ) < IFNULL(njmlharisistem,0) )";
        
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET gaji=IFNULL(ngaji,0)");
        
    }
    
    
    //$query = "UPDATE $tmp01 SET total=IFNULL(insentif_tambahan,0)+IFNULL(insentif,0)+IFNULL(tmakan,0)+IFNULL(gaji,0)+IFNULL(sewakendaraan,0)+IFNULL(pulsa,0)+IFNULL(bbm,0)+IFNULL(parkir,0)+IFNULL(lain,0)";
    $query = "UPDATE $tmp01 SET total=IFNULL(insentif,0)+IFNULL(insentif_tambahan,0)+IFNULL(insentif,0)+IFNULL(gaji,0)+IFNULL(ntunjangan,0)+IFNULL(lain,0)+IFNULL(tmakan,0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    //$tmp10 =" dbtemp.DSPGCR10_".$userid."_$now ";
    //mysqli_query($cnmy, "Create table $tmp10 select * from $tmp01");
    
    return $tmp01;
}

// $dari == 1= proses fin (proses) ||  2= proses fin (pd)
//  $dari == 3 = proses mng
//md_m_spg_prosesfin (proses & pd)
//md_m_spg_prosesfin SPV
//md_m_spg_prosesmgr
function CariDataSPGBR($dari, $bulan, $pidcabang, $pnoid, $status) {
    include "../../config/koneksimysqli.php";
    $cket=$status;
            
    $fperiode = " AND DATE_FORMAT(a.periode, '%Y%m') = '$bulan'";
    $fcabang = "";
    if (!empty($pidcabang) AND ($pidcabang <> "*")) $fcabang = " AND a.icabangid='$pidcabang' ";
    
    if ($pidcabang=="JKT_MT") {
        $fcabang = " AND IFNULL(a.icabangid,'') = '0000000007' AND a.alokid='001' ";
    }elseif ($pidcabang=="JKT_RETAIL") {
        $fcabang = " AND IFNULL(a.icabangid,'') = '0000000007' AND a.alokid='002' ";
    }
    
    $filterapprovedari = "";
    if ((INT)$dari==1) {
        
        $filststusapv = " AND (IFNULL(a.apvtgl1,'') <> '' AND IFNULL(a.apvtgl1,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00')  ";//AND IFNULL(sts,'')<>'P'
        //apv 3
        $approve3_sel = " AND (IFNULL(a.apvtgl3,'') = '' OR IFNULL(a.apvtgl3,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
        $filststusapv2 = "";
        if ((INT)$cket==1) {
            $filststusapv2 = " $approve3_sel AND (IFNULL(a.apvtgl2,'') = '' OR IFNULL(a.apvtgl2,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
        }elseif ((INT)$cket==2) {//$approve3_sel
            $filststusapv2 = "  AND (IFNULL(a.apvtgl2,'') <> '' AND IFNULL(a.apvtgl2,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00')  ";//AND IFNULL(sts,'')<>'P'
        }elseif ((INT)$cket==3) {
            $filststusapv = " AND (IFNULL(a.apvtgl1,'') <> '' AND IFNULL(a.apvtgl1,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') ";
            $filststusapv2 = " AND IFNULL(sts,'')='P' ";//AND (IFNULL(a.apvtgl2,'') <> '' AND IFNULL(a.apvtgl2,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') 
        }elseif ((INT)$cket==4) {
            $filststusapv2 = " AND (IFNULL(a.apvtgl4,'') <> '' AND IFNULL(a.apvtgl4,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') ";
        }
        
        $filterapprovedari = " $filststusapv $filststusapv2 ";
        
    }elseif ((INT)$dari==2) {
        $filststusapv3 = " AND (IFNULL(a.apvtgl4,'') <> '' AND IFNULL(a.apvtgl4,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') ";
        if ((INT)$cket==1) {
            $filststusapvpd = " $filststusapv3 AND IFNULL(a.nodivisi,'')='' ";
        }elseif ((INT)$cket==2) {
            $filststusapvpd = " $filststusapv3 AND IFNULL(a.nodivisi,'')<>'' ";
        }
        
        $filterapprovedari = $filststusapvpd;
    }elseif ((INT)$dari==3) {
        
        $filststusapv = " AND (IFNULL(a.apvtgl2,'') <> '' AND IFNULL(a.apvtgl2,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00')  ";//AND IFNULL(sts,'')<>'P'
        //apv 3
        $approve4_sel = " AND (IFNULL(a.apvtgl4,'') = '' OR IFNULL(a.apvtgl4,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
        
        $filststusapv3 = "";
        if ((INT)$cket==1) {
            $filststusapv3 = " $approve4_sel AND (IFNULL(a.apvtgl3,'') = '' OR IFNULL(a.apvtgl3,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
        }elseif ((INT)$cket==2) {
            $filststusapv3 = " $approve4_sel AND (IFNULL(a.apvtgl3,'') <> '' AND IFNULL(a.apvtgl3,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00')  ";//AND IFNULL(sts,'')<>'P'
        }elseif ((INT)$cket==3) {
            $filststusapv = " AND (IFNULL(a.apvtgl2,'') <> '' AND IFNULL(a.apvtgl2,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') ";
            $filststusapv3 = " AND (IFNULL(a.apvtgl3,'') <> '' AND IFNULL(a.apvtgl3,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00')  ";//AND IFNULL(sts,'')='P'
        }elseif ((INT)$cket==4) {
            $filststusapv3 = " AND (IFNULL(a.apvtgl4,'') <> '' AND IFNULL(a.apvtgl4,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') ";
        }
        
        $filterapprovedari = " $filststusapv $filststusapv3 ";
    }elseif ((INT)$dari==4) {
        
        $filststusapv = " AND (IFNULL(a.apvtgl3,'') <> '' AND IFNULL(a.apvtgl3,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00')  ";//AND IFNULL(sts,'')<>'P'
        //apv 4
        //$approve5_sel = " AND (IFNULL(a.apvtgl5,'') = '' OR IFNULL(a.apvtgl5,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
        $approve5_sel = " ";
        $filststusapv4 = "";
        if ((INT)$cket==1) {
            $filststusapv4 = " $approve5_sel AND (IFNULL(a.apvtgl4,'') = '' OR IFNULL(a.apvtgl4,'0000-00-00 00:00:00') = '0000-00-00 00:00:00') ";
        }elseif ((INT)$cket==2) {
            $filststusapv4 = " $approve5_sel AND (IFNULL(a.apvtgl4,'') <> '' AND IFNULL(a.apvtgl4,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00')  ";//AND IFNULL(sts,'')<>'P'
        }elseif ((INT)$cket==3) {
            $filststusapv = " AND (IFNULL(a.apvtgl3,'') <> '' AND IFNULL(a.apvtgl3,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') ";
            $filststusapv4 = " AND (IFNULL(a.apvtgl4,'') <> '' AND IFNULL(a.apvtgl4,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00')  ";//AND IFNULL(sts,'')='P'
        }elseif ((INT)$cket==4) {
            $filststusapv4 = " AND (IFNULL(a.apvtgl4,'') <> '' AND IFNULL(a.apvtgl4,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') ";
        }
        
        $filterapprovedari = " $filststusapv $filststusapv4 ";
        
        
    }else{//sementara takut ada yang kelewat
        $filststusapv3 = " AND (IFNULL(a.apvtgl3,'') <> '' AND IFNULL(a.apvtgl3,'0000-00-00 00:00:00') <> '0000-00-00 00:00:00') ";
        
        $filterapprovedari = $filststusapv3;
    }
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DGJSPGPOTC01_".$userid."_$now ";
    $tmp02 =" dbtemp.DGJSPGPOTC02_".$userid."_$now ";
    
    $query = "SELECT
        a.nodivisi, 
        a.nomor, 
        a.idbrspg,
        a.id_spg,
        b.nama,
        b.penempatan,
        a.periode tglbr,
        a.tglpengajuan,
        a.icabangid,
        a.alokid,
        a.areaid, a.jabatid, a.id_zona,
        c.nama nama_cabang,
        a.jml_harikerja harikerja, a.jml_sakit, a.jml_izin, a.jml_alpa, a.jml_uc, 
        a.total,
        a.realisasi,
        a.keterangan,
        a.sts, a.apvtgl3,
        a.total insentif, a.total insentif_tambahan, a.total gaji, a.keterangan hk, a.total rpmakan, a.total makan, a.total sewa, a.total pulsa, a.total bbm, a.total parkir,
        a.total rinsentif, a.total rinsentif_tambahan, a.total rgaji, a.total rmakan, a.total rsewa, a.total rpulsa, a.total rbbm, a.total rparkir,
        a.total sinsentif, a.total sinsentif_tambahan, a.total sgaji, a.total smakan, a.total ssewa, a.total spulsa, a.total sbbm, a.total sparkir
        FROM
        dbmaster.t_spg_gaji_br0 a
        JOIN mkt.spg b ON a.id_spg = b.id_spg
        LEFT JOIN mkt.icabang_o c on a.icabangid=c.icabangid_o 
        WHERE a.stsnonaktif<>'Y' $fperiode $fcabang $filterapprovedari";

    $query ="CREATE TABLE $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    
    $query = "ALTER TABLE $tmp01 ADD COLUMN nama_jabatan char(150), ADD COLUMN nama_area CHAR(200), ADD COLUMN nama_zona CHAR(100)";
    mysqli_query($cnmy, $query);
    
    $query = "UPDATE $tmp01 a SET a.nama_jabatan=(select b.nama_jabatan from dbmaster.t_spg_jabatan b WHERE a.jabatid=b.jabatid)";
    mysqli_query($cnmy, $query);
    
    $query = "UPDATE $tmp01 a SET a.nama_area=(select b.nama from MKT.iarea_o b WHERE a.areaid=b.areaid_o AND a.icabangid=b.icabangid_o)";
    mysqli_query($cnmy, $query);
    
    $query = "UPDATE $tmp01 a SET a.nama_zona=(select b.nama_zona from dbmaster.t_zona b WHERE a.id_zona=b.id_zona)";
    mysqli_query($cnmy, $query);
    
    
    
    
    mysqli_query($cnmy, "UPDATE $tmp01 SET icabangid='JKT_MT', nama_cabang='JAKARTA - MODERN TRADE'  WHERE icabangid='0000000007' AND alokid='001'");
    mysqli_query($cnmy, "UPDATE $tmp01 SET icabangid='JKT_RETAIL', nama_cabang='JAKARTA RETAIL' WHERE icabangid='0000000007' AND alokid='002'");
    
    mysqli_query($cnmy, "UPDATE $tmp01 SET insentif=0, insentif_tambahan=0, gaji=0, hk='', rpmakan=0, makan=0, sewa=0, pulsa=0, bbm=0, parkir=0");
    mysqli_query($cnmy, "UPDATE $tmp01 SET rinsentif=0, rinsentif_tambahan=0, rgaji=0, rmakan=0, rsewa=0, rpulsa=0, rbbm=0, rparkir=0");//ralisasi
    mysqli_query($cnmy, "UPDATE $tmp01 SET sinsentif=0, sinsentif_tambahan=0, sgaji=0, smakan=0, ssewa=0, spulsa=0, sbbm=0, sparkir=0");//selisih

    $query = "SELECT * FROM dbmaster.t_spg_gaji_br1 WHERE idbrspg IN (select distinct idbrspg FROM $tmp01)";
    $query ="CREATE TABLE $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.insentif=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='01'),0)");
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.insentif_tambahan=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='07'),0)");
    
    
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.gaji=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='02'),0)");

    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rpmakan=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.makan=IFNULL((SELECT sum(rptotal) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");
    mysqli_query($cnmy, "UPDATE $tmp01 SET hk=CONCAT(harikerja,' x ', FORMAT(rpmakan,0,'ta_in'))");

    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.sewa=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='04'),0)");
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.pulsa=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='05'),0)");
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.bbm=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='08'),0)");
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.parkir=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='06'),0)");

    //realisasi
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rinsentif=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='01'),0)");
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rinsentif_tambahan=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='07'),0)");
    
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rgaji=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='02'),0)");
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rmakan=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");

    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rsewa=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='04'),0)");
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rpulsa=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='05'),0)");
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rbbm=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='08'),0)");
    mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rparkir=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='06'),0)");

    mysqli_query($cnmy, "UPDATE $tmp01 SET sinsentif=insentif-rinsentif");
    mysqli_query($cnmy, "UPDATE $tmp01 SET sinsentif_tambahan=insentif_tambahan-rinsentif_tambahan");
    mysqli_query($cnmy, "UPDATE $tmp01 SET sgaji=gaji-rgaji");
    mysqli_query($cnmy, "UPDATE $tmp01 SET smakan=makan-rmakan");
    mysqli_query($cnmy, "UPDATE $tmp01 SET ssewa=sewa-rsewa");
    mysqli_query($cnmy, "UPDATE $tmp01 SET spulsa=pulsa-rpulsa");
    mysqli_query($cnmy, "UPDATE $tmp01 SET sbbm=bbm-rbbm");
    mysqli_query($cnmy, "UPDATE $tmp01 SET sparkir=parkir-rparkir");
    
    mysqli_query($cnmy, "drop table $tmp02");
    
    return $tmp01;
    
}
?>