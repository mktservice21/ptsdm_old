<?php

    session_start();
if ($_GET['module']=="hitungtotalcekbox") {
    include "../../module/md_m_spg_proses/caridata.php";
    include "../../config/koneksimysqli.php";
    
    $date1=$_POST['utgl'];
    $bulan= date("Ym", strtotime($date1));
    
    $date2=$_POST['utglinsentif'];
    $bulaninsentif= date("Ym", strtotime($date2));
    
    
    $pidcabang=$_POST['ucabang'];
    $pnoid=$_POST['unoidbr'];
    
    //$tmp01 = CariDataSPG($bulan, $pidcabang, $pnoid, "", $bulaninsentif);
    $tmp01 = CariDataSPGGajiTJ($bulan, $pidcabang, $pnoid, "", $bulaninsentif);
    
    $totalinput=0;
    
    $query="SELECT SUM(total) as jumlah from $tmp01";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $tr= mysqli_fetch_array($tampil);
        if (!empty($tr['jumlah'])) $totalinput=$tr['jumlah'];
    }
    mysqli_query($cnmy, "drop table $tmp01");
    echo $totalinput;
}elseif ($_GET['module']=="simpan") {
    
    $apvid=$_SESSION['IDCARD'];
    
    if (empty($apvid)) {
        echo "Anda harus login ulang....";
        exit;
    }
    
    
    include "../../module/md_m_spg_proses/caridata.php";
    include "../../config/koneksimysqli.php";
    
    $tglaju=$_POST['utglpengajuan'];
    $ptglpengajuan= date("Y-m-d", strtotime($tglaju));
    
    $date1=$_POST['utgl'];
    $bulan= date("Ym", strtotime($date1));
    $pidcabang=$_POST['ucabang'];
    $pnoid=$_POST['unoidbr'];
    $pjmlinc_bot=$_POST['umlincbot'];
    $ptipests=$_POST['utipests'];
    
    $date2=$_POST['utglinsentif'];
    $bulaninsentif= date("Ym", strtotime($date2));
    $pperiodeinct= date("Y-m-d", strtotime($date2));
    
    //echo $pjmlinc_bot; exit;
    $datanya = array($pjmlinc_bot);
    if (!empty($datanya)){
        $tag = implode(',',$datanya);
        $arr_kata = explode(",",$pjmlinc_bot);
        $count_kata = count($arr_kata);
        $jumlah_tag = substr_count($tag, ",") + 1;
        $u=0;
        $unsel="";
        for ($x=0; $x<=$jumlah_tag; $x++){
            if (!empty($arr_kata[$u])){
                $uTag=trim($arr_kata[$u]);
                $arr_inc_spg = explode("_",$uTag);
                $arr_spg="";
                $arr_inc="";
                
                if (isset($arr_inc_spg[0])) $arr_spg=$arr_inc_spg[0];
                if (isset($arr_inc_spg[1])) $arr_inc=$arr_inc_spg[1];
                if (empty($arr_inc)) $arr_inc=0;
                
                if (!empty($arr_spg)) {
                    $query = "UPDATE dbmaster.t_spg_gaji_br0 SET insentif_tambahan='$arr_inc' WHERE CONCAT(id_spg,icabangid)='$arr_spg' AND DATE_FORMAT(periode,'%Y%m')='$bulan'";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
                        echo "ERROR....";
                        exit;     
                    }
                }
            }
            $u++;
        }
    }
    
    
    //$tmp01 = CariDataSPG($bulan, $pidcabang, $pnoid, "", $bulaninsentif);
    $tmp01 = CariDataSPGGajiTJ($bulan, $pidcabang, $pnoid, "", $bulaninsentif);
    
    if ($pidcabang=="JKT_MT") {
        $pidcabang="0000000007";
    }elseif ($pidcabang=="JKT_RETAIL") {
        $pidcabang="0000000007";
    }
    
    $berhasil="Tidak ada data yang disimpan";
    
    $query="SELECT SUM(total) as jumlah from $tmp01";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $tr= mysqli_fetch_array($tampil);
        if (!empty($tr['jumlah'])) $totalinput=$tr['jumlah'];
    }
    
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp02 =" dbtemp.DSPGCR02_".$userid."_$now ";
    
    mysqli_query($cnmy, "CREATE TABLE $tmp02 (SELECT * FROM dbmaster.t_spg_gaji_br1 WHERE periode='1000-00-01' and id_spg='zyz')");
    
    mysqli_query($cnmy, "INSERT INTO $tmp02 (idbrspg, id_spg, icabangid, alokid, periode, kodeid, coa4, areaid, id_zona, jabatid) select DISTINCT a.idbrspg, a.id_spg, a.icabangid, alokid, a.periode, b.kodeid, b.coa4, a.areaid, a.id_zona, a.jabatid from $tmp01 a, dbmaster.t_spg_kode b");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
        mysqli_query($cnmy, "drop table $tmp01");
        mysqli_query($cnmy, "drop table $tmp02");
        echo "ERROR....";
        exit;     
    }
    
    
    //INSENTIF 01
    $query="UPDATE $tmp02 a SET a.rptotal=(SELECT insentif FROM $tmp01 b WHERE CONCAT(a.id_spg,a.icabangid)=CONCAT(b.id_spg,b.icabangid)) WHERE kodeid='01'";
    mysqli_query($cnmy, $query);
    
    
    //INSENTIF 07
    $query="UPDATE $tmp02 a SET a.rptotal=(SELECT insentif_tambahan FROM $tmp01 b WHERE CONCAT(a.id_spg,a.icabangid)=CONCAT(b.id_spg,b.icabangid)) WHERE kodeid='07'";
    mysqli_query($cnmy, $query);
    
    //GAJI 01
    $query="UPDATE $tmp02 a SET a.rptotal=(SELECT gaji FROM $tmp01 b WHERE CONCAT(a.id_spg,a.icabangid)=CONCAT(b.id_spg,b.icabangid)) WHERE kodeid='02'";
    mysqli_query($cnmy, $query);
    //SEWA 04
    $query="UPDATE $tmp02 a SET a.rptotal=(SELECT sewakendaraan FROM $tmp01 b WHERE CONCAT(a.id_spg,a.icabangid)=CONCAT(b.id_spg,b.icabangid)) WHERE kodeid='04'";
    mysqli_query($cnmy, $query);
    //PULSA 05
    $query="UPDATE $tmp02 a SET a.rptotal=(SELECT pulsa FROM $tmp01 b WHERE CONCAT(a.id_spg,a.icabangid)=CONCAT(b.id_spg,b.icabangid)) WHERE kodeid='05'";
    mysqli_query($cnmy, $query);
    
    //BBM 08
    $query="UPDATE $tmp02 a SET a.rptotal=(SELECT bbm FROM $tmp01 b WHERE CONCAT(a.id_spg,a.icabangid)=CONCAT(b.id_spg,b.icabangid)) WHERE kodeid='08'";
    mysqli_query($cnmy, $query);
    
    //PARKIR 06
    $query="UPDATE $tmp02 a SET a.rptotal=(SELECT parkir FROM $tmp01 b WHERE CONCAT(a.id_spg,a.icabangid)=CONCAT(b.id_spg,b.icabangid)) WHERE kodeid='06'";
    mysqli_query($cnmy, $query);
    
    //MAKAN 03
    $query="UPDATE $tmp02 a SET a.rp=(SELECT umakan FROM $tmp01 b WHERE CONCAT(a.id_spg,a.icabangid)=CONCAT(b.id_spg,b.icabangid)) WHERE kodeid='03'";
    mysqli_query($cnmy, $query);
    $query="UPDATE $tmp02 a SET a.qty=(SELECT jml_harikerja FROM $tmp01 b WHERE CONCAT(a.id_spg,a.icabangid)=CONCAT(b.id_spg,b.icabangid)) WHERE kodeid='03'";
    mysqli_query($cnmy, $query);
    $query="UPDATE $tmp02 a SET a.rptotal=rp*qty WHERE kodeid IN ('03')";
    mysqli_query($cnmy, $query);
    //
    
    $query="UPDATE $tmp02 a SET a.qty=1, rp=rptotal WHERE kodeid NOT IN ('03')";
    mysqli_query($cnmy, $query);
    
    
    $query = "DELETE FROM dbmaster.t_spg_gaji_br1 WHERE "
            . " CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')) IN "
            . " (SELECT IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')),'') FROM $tmp01) AND "
            . " icabangid='$pidcabang' AND DATE_FORMAT(periode,'%Y%m')='$bulan'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
        mysqli_query($cnmy, "drop table $tmp01");
        mysqli_query($cnmy, "drop table $tmp02");
        echo "ERROR....";
        exit;     
    }
    
    $query = "INSERT INTO dbmaster.t_spg_gaji_br1 (idbrspg, id_spg, icabangid, alokid, periode, kodeid, coa4, qty, rp, rptotal, areaid, id_zona, jabatid) "
            . "SELECT idbrspg, id_spg, icabangid, alokid, periode, kodeid, coa4, qty, rp, rptotal, areaid, id_zona, jabatid FROM $tmp02 ORDER BY periode, icabangid, id_spg, kodeid";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
        mysqli_query($cnmy, "drop table $tmp01");
        mysqli_query($cnmy, "drop table $tmp02");
        echo "ERROR....";
        exit;     
    }
    
    
    $query = "UPDATE dbmaster.t_spg_gaji_br0 set periode_insentif='$pperiodeinct', sts='$ptipests', apv1='$apvid', apvtgl1=NOW(), tglpengajuan='$ptglpengajuan' WHERE "
            . " CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')) IN "
            . " (SELECT IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')),'') FROM $tmp01) AND "
            . " icabangid='$pidcabang' AND DATE_FORMAT(periode,'%Y%m')='$bulan'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
        mysqli_query($cnmy, "drop table $tmp01");
        mysqli_query($cnmy, "drop table $tmp02");
        echo "ERROR....";
        exit;     
    }
    
    $query = "UPDATE dbmaster.t_spg_gaji_br0 a SET a.total=IFNULL((SELECT SUM(b.total) FROM $tmp01 b WHERE "
        . " CONCAT(a.id_spg,a.icabangid)=CONCAT(b.id_spg,b.icabangid) ),0) WHERE "
        . " CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')) IN "
        . " (SELECT IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')),'') FROM $tmp01) AND "
        . " icabangid='$pidcabang' AND DATE_FORMAT(periode,'%Y%m')='$bulan'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
        mysqli_query($cnmy, "drop table $tmp01");
        mysqli_query($cnmy, "drop table $tmp02");
        echo "ERROR....";
        exit;     
    }
    
    $berhasil="Data berhasil diproses...";
    mysqli_query($cnmy, "drop table $tmp01");
    mysqli_query($cnmy, "drop table $tmp02");
    echo $berhasil;
    
}elseif ($_GET['module']=="hapus") {
    $apvid=$_SESSION['IDCARD'];
    
    if (empty($apvid)) {
        echo "Anda harus login ulang....";
        exit;
    }
    
    
    $berhasil="Tidak ada data yang diunproses...";
    include "../../config/koneksimysqli.php";
    
    $date1=$_POST['utgl'];
    $bulan= date("Ym", strtotime($date1));
    $pidcabang=$_POST['ucabang'];
    $pnoid=$_POST['unoidbr'];
    
    $npilihcabalok=" CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')) ";
    if ($pidcabang=="JKT_MT") {
        $pidcabang="0000000007";
        $npilihcabalok=" CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,''),IFNULL(alokid,'')) ";
    }elseif ($pidcabang=="JKT_RETAIL") {
        $pidcabang="0000000007";
        $npilihcabalok=" CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,''),IFNULL(alokid,'')) ";
    }
    
    
    $query = "DELETE FROM dbmaster.t_spg_gaji_br1_unapprove WHERE "
            . " $npilihcabalok IN $pnoid AND "
            . " icabangid='$pidcabang' AND DATE_FORMAT(periode,'%Y%m')='$bulan'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "ERROR...."; exit; }
    
    
    $query = "INSERT INTO dbmaster.t_spg_gaji_br1_unapprove (periode, id_spg, icabangid, alokid, areaid, kodeid, qty, rp, rptotal)"
            . "SELECT periode, id_spg, icabangid, alokid, areaid, kodeid, qty, rp, rptotal FROM dbmaster.t_spg_gaji_br1 WHERE "
            . " $npilihcabalok IN $pnoid AND "
            . " icabangid='$pidcabang' AND DATE_FORMAT(periode,'%Y%m')='$bulan' AND kodeid IN ('07', '09')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "ERROR...."; exit; }
    
    
    
    $query = "UPDATE dbmaster.t_spg_gaji_br0 set insentif_tambahan=0, lebihkurang=0, periode_insentif=NULL, apv1=NULL, apvtgl1=NULL, tglpengajuan=NULL, total=NULL, sts='' WHERE "
            . " $npilihcabalok IN $pnoid AND "
            . " icabangid='$pidcabang' AND DATE_FORMAT(periode,'%Y%m')='$bulan'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "ERROR...."; exit; }
    
    $query = "DELETE FROM dbmaster.t_spg_gaji_br1 WHERE "
            . " $npilihcabalok IN $pnoid AND "
            . " icabangid='$pidcabang' AND DATE_FORMAT(periode,'%Y%m')='$bulan'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "ERROR...."; exit; }
    
    $berhasil = "data berhasil diunproses";
    
    echo $berhasil;
}elseif ($_GET['module']=="simpanpending") {
    
    $apvid=$_SESSION['IDCARD'];
    
    if (empty($apvid)) {
        echo "Anda harus login ulang....";
        exit;
    }
    
    
    $berhasil="Tidak ada data yang diproses...";
    include "../../config/koneksimysqli.php";
    
    $tglaju=$_POST['utglpengajuan'];
    $ptglpengajuan= date("Y-m-d", strtotime($tglaju));
    
    $date1=$_POST['utgl'];
    $bulan= date("Ym", strtotime($date1));
    $pidcabang=$_POST['ucabang'];
    $pnoid=$_POST['unoidbr'];
    $ptipests=$_POST['utipests'];
    
    $date2=$_POST['utglinsentif'];
    $pperiodeinct= date("Y-m-d", strtotime($date2));
    //periode_insentif='$pperiodeinct', 
    $query = "UPDATE dbmaster.t_spg_gaji_br0 set sts='$ptipests', tglpengajuan='$ptglpengajuan' WHERE "
            . " CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')) IN $pnoid AND "
            . " icabangid='$pidcabang' AND DATE_FORMAT(periode,'%Y%m')='$bulan'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "ERROR...."; exit; }
    
    $berhasil = "data berhasil diunproses";
    
    echo $berhasil;
    
}elseif ($_GET['module']=="xxx") {
    
}

?>
