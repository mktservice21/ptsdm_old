<?php
function seleksi_query_bank($nkodeneksi, $pperiode){
    include($nkodeneksi);
    
    $pbulan= date("Ym", strtotime($pperiode));
    $pbulan_sbl = date('Ym', strtotime('-1 month', strtotime($pperiode)));
    
    $pperiode1 = date("Y-m-d", strtotime($pperiode));
    $pperiode2 = date('Y-m-d', strtotime('-1 month', strtotime($pperiode)));
    
    $pmystatus_berhasil=true;
    $now=date("mdYhis");
    $tmp01 =" dbtemp.RPTREKOTDB01_".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.RPTREKOTDB02_".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.RPTREKOTDB03_".$_SESSION['USERID']."_$now ";
    $tmp04 =" dbtemp.RPTREKOTDB04_".$_SESSION['USERID']."_$now ";
    
    
    $query = "select a.parentidbank, a.stsinput, a.idinputbank, a.tanggal, a.nobukti, a.coa4, 
        a.kodeid, a.subkode, a.idinput, a.nomor, a.divisi, a.keterangan, a.nodivisi, a.jumlah, a.sts, a.userid, 
        a.brid, a.noslip, a.realisasi, a.customer, a.aktivitas1, CAST('' as CHAR(1)) as nket   
        from dbmaster.t_suratdana_bank a WHERE IFNULL(a.stsnonaktif,'') <> 'Y' AND 
        DATE_FORMAT(a.tanggal,'%Y%m')='$pbulan'";
    $query = "create TEMPORARY table $tmp01 ($query)";//a.stsinput NOT IN ('N') AND 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 SET nodivisi=idinput, nket='1' WHERE IFNULL(nodivisi,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    //outstanding LK
    $query = "select idots, tgl_kembali, kembali_rp, jumlah_kembali, divisi, 
        karyawanid, nama_karyawan, bulan, coa, keterangan, CAST('' as CHAR(10)) as idrutin, CAST('' as CHAR(50)) as nobukti  
        from dbmaster.t_brrutin_outstanding
        WHERE ots_status IN ('1') AND DATE_FORMAT(tgl_kembali,'%Y%m')='$pbulan' AND IFNULL(kembali_rp,0)>0";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "UPDATE $tmp02 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid SET a.nama_karyawan=b.nama WHERE "
            . " IFNULL(a.nama_karyawan,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "select distinct bulan, karyawanid, idrutin, nobukti, divisi from dbmaster.t_brrutin_ca_close WHERE "
            . " DATE_FORMAT(bulan,'%Y%m')='$pbulan_sbl' AND karyawanid IN (select distinct IFNULL(karyawanid,'') FROM $tmp02)";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "INSERT INTO $tmp03 (bulan, karyawanid, idrutin, nobukti, divisi) "
            . " select distinct bulan, karyawanid, idrutin, nobukti, 'OTC' as divisi from dbmaster.t_brrutin_ca_close_otc WHERE "
            . " DATE_FORMAT(bulan,'%Y%m')='$pbulan_sbl' AND karyawanid IN (select distinct IFNULL(karyawanid,'') FROM $tmp02)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "UPDATE $tmp02 a JOIN $tmp03 b on a.karyawanid=b.karyawanid AND DATE_FORMAT(a.bulan,'%Y%m')=DATE_FORMAT(b.bulan,'%Y%m')"
            . " SET a.idrutin=b.idrutin, a.nobukti=b.nobukti";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    //END outstanding LK
    
    mysqli_query($cnmy, "DROP TABLE $tmp03");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    //outstanding SERVICE
    
    $query = "select a.idservice, a.tanggal as tgl_kembali, a.jumlah kembali_rp, a.coa, "
            . " b.karyawanid, c.nama nama_karyawan, b.divisi, b.keterangan "
            . " FROM dbmaster.t_brrutin_outstanding_sk a LEFT JOIN dbmaster.t_service_kendaraan b on a.idservice=b.idservice "
            . " LEFT JOIN hrd.karyawan c on b.karyawanid=c.karyawanid WHERE"
            . " b.stsnonaktif<>'Y' AND DATE_FORMAT(a.tanggal,'%Y%m')='$pbulan' ";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //END outstanding SERVICE
    
    
    //insert outstanding LK
    $query = "INSERT INTO $tmp01 (stsinput, idinputbank, tanggal, nobukti, coa4, 
        kodeid, subkode, nomor, divisi, keterangan, nodivisi, jumlah, sts)"
            . " select distinct 'D' stsinput, CONCAT('OT',idots) idots, tgl_kembali, "
            . " nobukti, coa, '' as kodeid, '' as subkode, '' as nomor, "
            . " divisi, CONCAT('outstanding ', nama_karyawan), '' as nodivisi, kembali_rp, '1' sts FROM $tmp02";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    //insert outstanding SERVICE
    $query = "INSERT INTO $tmp01 (stsinput, idinputbank, tanggal, coa4, 
        kodeid, subkode, divisi, keterangan, nodivisi, jumlah, sts)"
            . "select distinct 'D' stsinput, idservice, tgl_kembali, coa, "
            . " '' as kodeid, '' as subkode, divisi, "
            . " CONCAT(idservice,' ', nama_karyawan,' : ', keterangan) as keterangan, "
            . " '' as nodivisi, kembali_rp, '1' sts FROM $tmp03";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.*, b.NAMA4, CAST(0 as DECIMAL(20,2)) as mintadana, "
            . " CAST(0 as DECIMAL(20,2)) as debit, CAST(0 as DECIMAL(20,2)) as kredit, CAST(0 as DECIMAL(20,2)) as saldo, "
            . " CAST(0 as DECIMAL(20,2)) as  saldoawal, CAST('' as CHAR(1)) as sudah_trans "
            . " from $tmp01 a LEFT JOIN dbmaster.coa_level4 b on a.coa4=b.COA4";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "UPDATE $tmp04 SET mintadana=jumlah WHERE stsinput IN ('N')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp04 SET debit=jumlah WHERE stsinput IN ('D', 'M')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp04 SET kredit=jumlah WHERE stsinput IN ('K', 'T')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    //transfer
    $query = "UPDATE $tmp04 a JOIN (select * from $tmp01 WHERE stsinput='K' AND subkode='29') b on a.nodivisi=b.nodivisi AND a.nobukti=b.nobukti AND a.stsinput=b.stsinput "
            . " SET a.sudah_trans='Y' WHERE a.stsinput='K' AND a.subkode='29'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp04 a JOIN (select parentidbank, stsinput from $tmp01 WHERE stsinput='T' AND subkode='29') b on a.idinputbank=b.parentidbank AND a.stsinput=b.stsinput "
            . " SET a.sudah_trans='Y' WHERE a.stsinput='T' and a.subkode<>'29'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    //end transfer
    
    
    //saldo awal dari bulan sebelumnya
    $p_saldo_awal="0";
    $sql = "select jumlah from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$pbulan_sbl'";
    $tampil= mysqli_query($cnmy, $sql);
    $nt= mysqli_fetch_array($tampil);
    $p_saldo_awal=$nt['jumlah'];
    if (empty($p_saldo_awal)) $p_saldo_awal=0;
    

    $query = "INSERT INTO $tmp04 (idinputbank, tanggal, saldoawal)VALUES('SAWAL', '$pperiode1', '$p_saldo_awal')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    
    
    
    
    
    $query = "SELECT a.*, b.subnama, c.nama nama_user FROM $tmp04 a LEFT JOIN dbmaster.t_kode_spd b on a.subkode=b.subkode "
            . " LEFT JOIN hrd.karyawan c on a.userid=c.karyawanId";
    $query = "create table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    
    return $tmp01;
    exit;
    
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    $pmystatus_berhasil=false;
    
    return $pmystatus_berhasil;
}
?>

