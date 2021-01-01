<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BANK.xls");
    }
    
    $nmodule=$_GET['module'];
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;

    
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];

    $n_filterkaryawan="";

    include "config/koneksimysqli.php";
    include "config/fungsi_combo.php";
    if ($nmodule=="brdanabank") {
        $tgl01=$_POST['e_periode01'];
        $tgl02=$_POST['e_periode02'];
        $periode1= date("Ym", strtotime($tgl01));
        $periode2= date("Ym", strtotime($tgl02));

        $f_tglmasuk = " AND DATE_FORMAT(tanggal,'%Y%m') BETWEEN '$periode1' AND '$periode2' ";
        $f_kembali = " AND DATE_FORMAT(a.tgl_kembali,'%Y%m') BETWEEN '$periode1' AND '$periode2' ";

        if ($pses_grpuser=="1" OR $pses_grpuser=="24" OR $pses_grpuser=="25") {
        }else{
            $n_filterkaryawan = " AND CONCAT(a.nomor,IFNULL(a.nodivisi,'')) IN (SELECT CONCAT(nomor,IFNULL(nodivisi,'')) FROM dbmaster.t_suratdana_br WHERE "
                    . " karyawanid='$pses_idcard')";
        }

    }else{
        $tgl01=$_POST['bulan1'];
        $periode1= date("Ym", strtotime($tgl01));

        $f_tglmasuk = " AND DATE_FORMAT(tanggal,'%Y%m')='$periode1' ";
        $f_kembali = " AND DATE_FORMAT(a.tgl_kembali,'%Y%m')='$periode1' ";
    }

    
    $pilih_bulan_=date("F Y", strtotime($tgl01));
    
    
    $tgl_sbl = date('Ym', strtotime('-1 month', strtotime($tgl01)));
    //saldo awal dari bulan sebelumnya
    $p_saldo_awal="0";
    $sql = "select jumlah from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$tgl_sbl'";
    $tampil= mysqli_query($cnmy, $sql);
    $nt= mysqli_fetch_array($tampil);
    $p_saldo_awal=$nt['jumlah'];
    if (empty($p_saldo_awal)) $p_saldo_awal=0;



    $now=date("mdYhis");
    $tmp01 =" dbtemp.RPTREKOTDB01_".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.RPTREKOTDB02_".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.RPTREKOTDB03_".$_SESSION['USERID']."_$now ";
    $tmp04 =" dbtemp.RPTREKOTDB04_".$_SESSION['USERID']."_$now ";
    $tmp05 =" dbtemp.RPTREKOTDB05_".$_SESSION['USERID']."_$now ";
    $tmp06 =" dbtemp.RPTREKOTDB06_".$_SESSION['USERID']."_$now ";
    $tmp07 =" dbtemp.RPTREKOTDB07_".$_SESSION['USERID']."_$now ";
    $tmp08 =" dbtemp.RPTREKOTDB08_".$_SESSION['USERID']."_$now ";
    $tmp09 =" dbtemp.RPTREKOTDB09_".$_SESSION['USERID']."_$now ";
    $tmp10 =" dbtemp.RPTREKOTDB10_".$_SESSION['USERID']."_$now ";

    $query = "select a.parentidbank, a.stsinput, a.idinputbank, a.tanggal, a.nobukti, a.coa4, c.NAMA4, 
        a.kodeid, a.subkode, a.idinput, a.nomor, a.divisi, a.keterangan, a.nodivisi, a.jumlah, a.sts, a.userid, a.brid, a.noslip, a.realisasi, a.customer, a.aktivitas1, CAST('' as CHAR(1)) as nket   
        from dbmaster.t_suratdana_bank a 
        JOIN dbmaster.coa_level4 c on a.coa4=c.COA4 WHERE IFNULL(a.stsnonaktif,'') <> 'Y' $f_tglmasuk $n_filterkaryawan";
    //echo "$query";goto hapusdata;
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp01 SET nodivisi=idinput, nket='1' WHERE IFNULL(nodivisi,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


    // cari data dari LK (outstanding)

    $query = "SELECT  b.idrutin, a.idots, a.tgl_kembali, a.kembali_rp, a.divisi, a.karyawanid, a.bulan, a.coa, c.NAMA4, b.nobukti, a.keterangan, cast('' as char(150)) as nmkaryawan 
        FROM dbmaster.t_brrutin_outstanding a 
        LEFT JOIN 
        (
        select * from (
        select distinct idrutin, bulan, divisi, karyawanid, nobukti from dbmaster.t_brrutin_ca_close WHERE IFNULL(sts,'')='C'
        UNION
        select distinct idrutin, bulan, divisi, karyawanid, nobukti from dbmaster.t_brrutin_ca_close_otc WHERE IFNULL(sts,'')='C'
        ) as tblclose
        ) as b on DATE_FORMAT(a.bulan,'%Y%m')=DATE_FORMAT(b.bulan,'%Y%m') 
        AND a.karyawanid=b.karyawanid and a.divisi=b.divisi
        JOIN dbmaster.coa_level4 c on a.coa=c.COA4
        WHERE a.ots_status IN ('1') $f_kembali";
    
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "DELETE FROM $tmp02 WHERE IFNULL(kembali_rp,0)<=0";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    $query="update $tmp02 a SET a.nmkaryawan=(select nama from hrd.karyawan b where a.karyawanid=b.karyawanid)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query="update $tmp02 a SET a.nmkaryawan=(select nama from dbmaster.t_karyawan_kontrak b where a.karyawanid=b.id) WHERE IFNULL(a.nmkaryawan,'')='' AND a.divisi='OTC'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query="update $tmp02 SET keterangan=CASE WHEN IFNULL(keterangan,'')<>'' THEN CONCAT('outstanding ',nmkaryawan, '<br/>', keterangan) ELSE CONCAT('outstanding ',nmkaryawan) END";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    $query = "select a.*, c.kodeid, c.subkode, c.nomor, c.nodivisi, c.tglspd, c.tgl from $tmp02 a  JOIN dbmaster.t_suratdana_br1 b on a.idrutin=b.bridinput "
            . " JOIN dbmaster.t_suratdana_br c on b.idinput=c.idinput";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //baru
    $query="select * from $tmp02 WHERE idots NOT IN (select distinct IFNULL(idots,'') FROM $tmp03) AND IFNULL(idrutin,'')<>''";
    $query = "create TEMPORARY table $tmp10 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    $query = "DELETE FROM $tmp02 WHERE idots IN (select distinct IFNULL(idots,'') FROM $tmp10)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    /*
    
    $query="INSERT INTO $tmp03(idrutin, idots, tgl_kembali, kembali_rp, divisi, karyawanid, bulan, coa, NAMA4, nobukti, keterangan, nmkaryawan)"
            . "SELECT idrutin, idots, tgl_kembali, kembali_rp, divisi, karyawanid, bulan, coa, NAMA4, nobukti, keterangan, nmkaryawan FROM $tmp10";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
   */
    //baru
    
    
    $query = "DELETE FROM $tmp02 WHERE IFNULL(idrutin,'')<>''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET idrutin=karyawanid WHERE IFNULL(idrutin,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select divisi, kodeid, subkode, nomor, nodivisi from $tmp03 WHERE divisi='OTC' LIMIT 1";
    $query = "create TEMPORARY table $tmp09 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT INTO $tmp09 (divisi, kodeid, subkode, nomor, nodivisi)"
            . " select distinct divisi, kodeid, subkode, nomor, nodivisi from $tmp03 WHERE divisi<>'OTC'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp03 (idrutin, idots, tgl_kembali, kembali_rp, divisi, karyawanid, bulan, "
            . " coa, NAMA4, nobukti, keterangan, nmkaryawan, kodeid, subkode, nomor, nodivisi)"
            . " select a.idrutin, a.idots, a.tgl_kembali, a.kembali_rp, a.divisi, a.karyawanid, a.bulan, "
            . " a.coa, a.NAMA4, a.nobukti, a.keterangan, a.nmkaryawan, b.kodeid, b.subkode, b.nomor, b.nodivisi FROM "
            . " $tmp02 a LEFT JOIN $tmp09 b on a.divisi=b.divisi";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    

    $query = "INSERT INTO $tmp01 (stsinput, idinputbank, tanggal, nobukti, coa4, NAMA4, 
        kodeid, subkode, nomor, divisi, keterangan, nodivisi, jumlah, sts)"
            . "select distinct 'D' stsinput, CONCAT('OT',idots) idots, tgl_kembali, nobukti, coa, NAMA4, kodeid, subkode, nomor, divisi, keterangan, nodivisi, kembali_rp, '1' sts FROM $tmp03";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    
    $query = "select a.idservice, a.tanggal as tgl_kembali, a.jumlah kembali_rp, a.coa, d.NAMA4, "
            . " b.karyawanid, c.nama nama_karyawan, b.divisi, b.keterangan "
            . " FROM dbmaster.t_brrutin_outstanding_sk a JOIN dbmaster.t_service_kendaraan b on a.idservice=b.idservice "
            . " JOIN hrd.karyawan c on b.karyawanid=c.karyawanid "
            . " LEFT JOIN dbmaster.coa_level4 d on a.coa=d.COA4 WHERE"
            . " b.stsnonaktif<>'Y' AND DATE_FORMAT(a.tanggal,'%Y%m')='$periode1' ";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "SELECT a.*, b.nodivisi, b.kodeid, b.subkode FROM $tmp02 a LEFT JOIN "
            . "(SELECT distinct x.bridinput, y.nodivisi, y.kodeid, y.subkode FROM dbmaster.t_suratdana_br1 x JOIN dbmaster.t_suratdana_br y on "
            . " x.idinput=y.idinput WHERE x.kodeinput='V' and y.stsnonaktif<>'Y') as b on a.idservice=b.bridinput "
            . " ";//LEFT JOIN dbmaster.t_kode_spd c on b.kodeid=c.kodeid AND b.subkode=c.subkode
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "INSERT INTO $tmp01 (stsinput, idinputbank, tanggal, coa4, NAMA4, 
        kodeid, subkode, divisi, keterangan, nodivisi, jumlah, sts)"
            . "select distinct 'D' stsinput, CONCAT('OTSK',idservice) idservice, tgl_kembali, coa, NAMA4, "
            . " kodeid, subkode, divisi, CONCAT(idservice,' ', nama_karyawan,' : ', keterangan) as keterangan, nodivisi, kembali_rp, '1' sts FROM $tmp03";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    
    
    //ketinggal
    $query = "INSERT INTO $tmp01 (stsinput, idinputbank, tanggal, nobukti, coa4, NAMA4, 
        kodeid, subkode, nomor, divisi, keterangan, nodivisi, jumlah, sts)"
            . "select distinct 'D' stsinput, CONCAT('OT',idots) idots, tgl_kembali, nobukti, coa, NAMA4, '' as kodeid, '' as subkode, '' as nomor, "
            . " divisi, keterangan, '' as nodivisi, kembali_rp, '1' sts FROM $tmp10";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //end ketinggal
    
    
    
    
/*
 
    //OTC
    $query = "SELECT a.brOtcId brid, a.noslip, a.tgltrans, a.jumlah, a.realisasi, a.keterangan1, a.real1, a.icabangid_o, b.nama nama_cabang 
        from hrd.br_otc a LEFT JOIN mkt.icabang_o b on a.icabangid_o=b.icabangid_o WHERE 
        a.brOtcId IN (select distinct brid From $tmp01 WHERE IFNULl(brid,'')<>'' AND divisi='OTC')";
    $query = "create TEMPORARY table $tmp05 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query="UPDATE $tmp05 SET nama_cabang=icabangid_o WHERE IFNULL(nama_cabang,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    //END OTC

    //BR0
    $query = "SELECT a.brid, a.noslip, a.tgltrans, a.jumlah, a.jumlah1, a.aktivitas1, a.realisasi1, a.icabangid, b.nama nama_cabang, a.dokterid 
        from hrd.br0 a LEFT JOIN mkt.icabang b on a.icabangid=b.icabangid WHERE 
        a.brid IN (select distinct brid From $tmp01 WHERE IFNULl(brid,'')<>'' AND divisi<>'OTC')";
    $query = "create TEMPORARY table $tmp06 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "select dokterid, nama nama_dokter from hrd.dokter WHERE dokterid IN (select distinct dokterid from $tmp03)";
    $query = "create TEMPORARY table $tmp07 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


    //END BR0
    
    //$query = "select *, jumlah debit, CAST(0 as DECIMAL(20,2)) as kredit, CAST(0 as DECIMAL(20,2)) as saldo from $tmp01 WHERE stsinput='D'";
    //$query = "create TEMPORARY table $tmp04 ($query)";
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    $query = "select *, CAST(0 as DECIMAL(20,2)) mintadana, CAST(0 as DECIMAL(20,2)) debit, CAST(0 as DECIMAL(20,2)) as kredit, CAST(0 as DECIMAL(20,2)) as saldo from $tmp01";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp04 a SET a.debit=(select sum(b.jumlah) FROM $tmp01 b WHERE b.stsinput IN ('D', 'M', 'N') AND a.nomor=b.nomor AND a.nodivisi=b.nodivisi AND a.idinput=b.idinput)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp04 a SET a.kredit=(select sum(b.jumlah) FROM $tmp01 b WHERE b.stsinput='K' AND a.nomor=b.nomor AND a.nodivisi=b.nodivisi AND a.idinput=b.idinput)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    //$query = "UPDATE $tmp04 a SET a.mintadana=(select sum(b.jumlah) FROM $tmp01 b WHERE b.stsinput='N' AND a.nomor=b.nomor AND a.nodivisi=b.nodivisi AND a.idinput=b.idinput)";
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    


    $query = "select a.*, b.tgltrans, b.aktivitas1, b.realisasi1 nmrealisasi, b.nama_cabang, b.dokterid, c.nama_dokter, d.nama nama_user "
            . " from $tmp04 a LEFT JOIN $tmp06 b on a.brid=b.brid LEFT JOIN $tmp07 c on b.dokterid=c.dokterid "
            . " LEFT JOIN hrd.karyawan d on a.userid=d.karyawanId";
    $query = "create TEMPORARY table $tmp08 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


    //OTC
    $query = "UPDATE $tmp08 a SET a.tgltrans=(select b.tgltrans FROM $tmp05 b WHERE a.brid=b.brid) WHERE IFNULL(a.brid,'')<>'' AND a.divisi='OTC'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "UPDATE $tmp08 a SET a.aktivitas1=(select b.keterangan1 FROM $tmp05 b WHERE a.brid=b.brid) WHERE IFNULL(a.brid,'')<>'' AND a.divisi='OTC'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "UPDATE $tmp08 a SET a.nmrealisasi=(select b.real1 FROM $tmp05 b WHERE a.brid=b.brid) WHERE IFNULL(a.brid,'')<>'' AND a.divisi='OTC'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "UPDATE $tmp08 a SET a.nama_cabang=(select b.nama_cabang FROM $tmp05 b WHERE a.brid=b.brid) WHERE IFNULL(a.brid,'')<>'' AND a.divisi='OTC'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    //END OTC
    
    
*/ //end lama
    
    
    
/* lama juga
 
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");

    $query = "select * from $tmp08"; 
    $query = "create TEMPORARY table $tmp06 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


    $query = "select idinput, tgl, divisi, kodeid, subkode, nomor, nodivisi, jumlah from 
        dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND nomor IN 
        (select distinct nomor from $tmp08 WHERE IFNULL(stsinput,'')='M')";
    $query = "create TEMPORARY table $tmp07 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }



    $query = "select c.stsinput, c.idinputbank, c.tanggal, c.nobukti, c.coa4, c.nama4, b.kodeid, b.subkode, b.idinput,
        b.nomor, b.divisi, c.keterangan, b.nodivisi, b.jumlah, c.sts, c.userid, c.nama_user
         from 
        $tmp07 b JOIN (
        SELECT
        a.stsinput, a.idinputbank, a.tanggal, a.nobukti, a.coa4, a.nama4, a.kodeid, a.subkode, a.idinput, 
        a.nomor, a.divisi, a.keterangan, a.nodivisi, a.jumlah, a.sts, a.userid, a.nama_user FROM
        $tmp06 a WHERE stsinput='M') as c on b.nomor=c.nomor";

    $query = "INSERT INTO $tmp08 (stsinput, idinputbank, tanggal, nobukti, coa4, nama4, kodeid, subkode, idinput, "
            . " nomor, divisi, keterangan, nodivisi, jumlah, sts, userid, nama_user)"
            . " $query"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
*/ //end lama juga

    
//jika baru di nonaktifkan maka lama di aktifkan dan lama juga non aktif
    
//baru
    $query = "select *, CAST(0 as DECIMAL(20,2)) mintadana, CAST(0 as DECIMAL(20,2)) debit, CAST(0 as DECIMAL(20,2)) as kredit, CAST(0 as DECIMAL(20,2)) as saldo from $tmp01";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp04 a SET a.debit=(select sum(b.jumlah) FROM $tmp01 b WHERE b.stsinput IN ('D', 'M', 'N') AND a.nomor=b.nomor AND a.nodivisi=b.nodivisi AND a.idinput=b.idinput)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp04 a SET a.kredit=(select sum(b.jumlah) FROM $tmp01 b WHERE b.stsinput='K' AND a.nomor=b.nomor AND a.nodivisi=b.nodivisi AND a.idinput=b.idinput)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    $query = "select a.*, a.tanggal as tgltrans, a.realisasi nmrealisasi, a.customer as nama_dokter, d.nama nama_user "
            . " from $tmp04 a LEFT JOIN hrd.karyawan d on a.userid=d.karyawanId";
    $query = "create TEMPORARY table $tmp08 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
//end baru
    
    
    $query = "ALTER TABLE $tmp08 ADD COLUMN subnama CHAR(100), ADD COLUMN sudah_trans CHAR(1)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    $query = "UPDATE $tmp08 a SET a.subnama=(select b.subnama from dbmaster.t_kode_spd b WHERE a.subkode=b.subkode)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");
    $query = "select * from $tmp08 WHERE stsinput='K' AND subkode='29'"; 
    $query = "create TEMPORARY table $tmp07 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "UPDATE $tmp08 a JOIN $tmp07 b on a.nodivisi=b.nodivisi AND a.nobukti=b.nobukti AND a.stsinput=b.stsinput SET a.sudah_trans='Y' "; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");
    $query = "select parentidbank, stsinput from $tmp08 WHERE stsinput='T' AND subkode='29'"; 
    $query = "create TEMPORARY table $tmp07 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp08 a JOIN $tmp07 b on a.idinputbank=b.parentidbank AND a.stsinput=b.stsinput SET a.sudah_trans='Y' WHERE a.stsinput='T' and a.subkode<>'29'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    
?>
    


<html>
<head>
    <title>Laporan Saldo BCA  PT SDM – Jakarta</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

        <!-- Datatables -->
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    <?PHP } ?>
    
</head>



<body class="nav-md">

<div class='modal fade' id='myModal' role='dialog'></div>

    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
<div id='n_content'>

    
    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <?PHP if ($ppilihrpt=="excel") {
                    echo "<tr><td colspan=5 width='150px'><b>Laporan Saldo BCA  PT SDM – Jakarta $pilih_bulan_</b></td></tr>";
                }else{
                    echo "<tr><td width='150px'><b>Laporan Saldo BCA  PT SDM – Jakarta $pilih_bulan_</b></td></tr>";
                }
                ?>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
    
    <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
            <th align="center">Date</th>
            <th align="center">Bukti</th>
            <th align="center">KODE</th>
            <th align="center">PERKIRAAN</th>
            <th align="center">Jenis</th>
            <th align="center">Surat Dana</th>
            <th align="center">Pengajuan</th>
            <th align="center">Keterangan</th>
            <th align="center">No. Divisi</th>
            <th align="center">Selisih</th>
            <th align="center">Minta Dana</th>
            <th align="center">Debit</th>
            <th align="center">Credit</th>
            <th align="center">Saldo</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
        
            $p_saldo=number_format($p_saldo_awal,0,",",",");

            echo "<tr>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap>Saldo</td>";
            echo "<td nowrap></td>";
            echo "<td nowrap align='right'><b></b></td>";
            echo "<td nowrap align='right'><b></b></td>";
            echo "<td nowrap align='right'><b></b></td>";
            echo "<td nowrap align='right'><b></b></td>";
            echo "<td nowrap align='right'><b>$p_saldo</b></td>";
            echo "</tr>";
            
            
            $no=1;
            $ptotal=0;
            $ptotal_k=0;
            $c_sudah=false;
            //$query = "select distinct nomor, nodivisi FROM $tmp01 order by nomor, nodivisi";
            //$tampil1=mysqli_query($cnmy, $query);
            //while ($row1= mysqli_fetch_array($tampil1)) {
                //$pnospd1 = $row1['nomor'];
                //$pnodivisi1 = $row1['nodivisi'];
                //$c_sudah=false;
                //WHERE nomor='$pnospd1' AND nodivisi='$pnodivisi1' 
                $query = "select * FROM $tmp08 order by tanggal, nobukti, nomor, divisi, nodivisi, stsinput, kodeid, idinputbank";
                $tampil=mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    
                    $ptgltrans =date("d-M-Y", strtotime($row['tanggal']));
                    $pidinputbank = $row['idinputbank'];
                    $pbukti = $row['nobukti'];
                    
                    

                    $pcoa = $row['coa4'];
                    $pnmcoa = $row['NAMA4'];
                    $pdivisi = $row['divisi'];
                    
                    $pstsinput = $row['stsinput'];
                    $pkodeid = $row['kodeid'];
                    $psubkode = $row["subkode"];
                    $psubnamakode = $row["subnama"];
                    
                    $pnamakode = "Bank";
                    if ($psubkode=="29") {
                        
                    }else{
                        if ($pkodeid=="1") $pnamakode = "Advance";
                        if ($pkodeid=="2") $pnamakode = "Klaim";
                    }
                    
                    if ($pkodeid!="5") {
                        //$pnamakode=$psubnamakode;
                    }
                    
                    if (empty($pdivisi) AND $pstsinput!="M") $pdivisi = "ETHICAL";
                    
                    
                    $pstatus = $row['sts'];
                    $pnospd = $row['nomor'];
                    
                    $pnket = $row['nket'];
                    $pnodivisi = $row['nodivisi'];
                    if ($pnket=="1") $pnodivisi="";
                    
                    $pketerangan = $row['keterangan'];
                    
                    $pjml_md = $row['jumlah'];
                    $pjumlah = $row['jumlah'];
                    $pjmlkredit = $row['jumlah'];
                    if ($pstsinput=="K" OR $pstsinput=="T") {
                        $pjml_md=0;
                        $pjumlah=0;
                        $ptotal_k=(double)$ptotal_k+(double)$pjmlkredit;
                    }elseif ($pstsinput=="N") {
                        $pjumlah=0;
                        $pjmlkredit=0;
                    }else{
                        $pjml_md=0;
                        $pjmlkredit=0;
                        $ptotal=(double)$ptotal+(double)$pjumlah;
                    }
                    $p_saldo_awal=(double)$p_saldo_awal+(double)$pjumlah-(double)$pjmlkredit;
                    
                    
                    $nk_rtr="retur";
                    if ($pstsinput=="T") {
                        $nk_rtr="transfer";
                    }

                    if (empty($pnospd) AND $pstatus=="1") {
                        $pnospd= "non surat";
                    }else{
                        if ($pstatus=="2") {
                            if (!empty($pketerangan))
                                $pketerangan="$nk_rtr, ".$pketerangan;
                            else
                                $pketerangan="$nk_rtr";
                        }
                    }
                    
                    $pnobridinput = $row['brid'];
                    $pnoslip = $row["noslip"];
                    $paktivitasbr = $row["aktivitas1"];
                    $pnmuser = $row["nama_user"];
                    $pnmrealisasi = $row["nmrealisasi"];
                    $pnmdokter = $row["nama_dokter"];
                    
                    
                    
                    $nket_brinput="";
                    if (!empty($pnobridinput)) {
                        if (!empty($pnoslip)) $pnoslip="No Slip : $pnoslip";
                        if (empty($pnoslip)) $pnoslip="IDBR : $pnobridinput";
                        
                        if (!empty($pnmdokter)) $pnmdokter=", Dok/Cust : $pnmdokter";
                        if (!empty($pnmrealisasi)) {
                            $pnmrealisasi="<br/>Realisasi : $pnmrealisasi";
                            if (!empty($paktivitasbr)) $pnmrealisasi .=", Ket : $paktivitasbr";
                        }else{
                            if (!empty($paktivitasbr)) $pnmrealisasi .="<br/>Ket : $paktivitasbr";
                        }
                        
                        $nket_brinput="$pnoslip $pnmdokter $pnmrealisasi";
                    }
                    
                    if (!empty($pketerangan) AND !empty($nket_brinput)) {
                        $pketerangan .="<br/>".$nket_brinput;
                    }else{
                        if (!empty($nket_brinput)) $pketerangan =$nket_brinput;
                    }
                    
                    if ($psubkode=="29") {
                        $pketerangan=$psubnamakode;
                    }else{
                        if (empty($pketerangan)) $pketerangan=$psubnamakode;
                    }
                    
                    /*
                    $pjmlkredit = $row['kredit'];
                    if ($c_sudah==true){
                        $pjmlkredit="";
                    }else{
                        $ptotal_k=(double)$ptotal_k+(double)$pjmlkredit;
                        $pjmlkredit=number_format($pjmlkredit,0,",",",");
                    }
                    if ($pjmlkredit=="0") $pjmlkredit="";
                    */
                    
                    $pjml_md=number_format($pjml_md,0,",",",");
                    $pjumlah=number_format($pjumlah,0,",",",");
                    $pjmlkredit=number_format($pjmlkredit,0,",",",");
                    $p_saldo=number_format($p_saldo_awal,0,",",",");
                    
                    if ($pstsinput=="N") {
                        $p_saldo="";
                    }
                    
                    if ($pjml_md=="0") $pjml_md="";
                    if ($pjumlah=="0") $pjumlah="";
                    if ($pjmlkredit=="0") $pjmlkredit="";
                    
                    $psudahtransfer = $row["sudah_trans"];
                    $nadd_trans=$pnodivisi;
                    if ($ppilihrpt!="excel" AND ($pstsinput=="K" OR $pstsinput=="T") AND $psubkode!="29" AND $psudahtransfer!="Y") {
                        if (!empty($pnodivisi)) {
                            $nadd_trans="<button type='button' class='btn btn-default btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataInput('$pidinputbank')\">$pnodivisi</button>";
                        }
                    }else{
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$pbukti</td>";
                    echo "<td nowrap>$pcoa</td>";
                    echo "<td nowrap>$pnmcoa</td>";
                    echo "<td nowrap>$pnamakode</td>";
                    echo "<td nowrap>$pnospd</td>";
                    echo "<td nowrap>$pdivisi</td>";
                    echo "<td>$pketerangan</td>";
                    echo "<td nowrap>$nadd_trans</td>";
                    echo "<td nowrap align='right'></td>";
                    echo "<td nowrap align='right'>$pjml_md</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap align='right'>$pjmlkredit</td>";
                    echo "<td nowrap align='right'>$p_saldo</td>";
                    echo "</tr>";

                    $c_sudah=true;
                    $no++;
                }            
            //}
            
                $ptotal=number_format($ptotal,0,",",",");
                $ptotal_k=number_format($ptotal_k,0,",",",");
                $p_saldo_awal=number_format($p_saldo_awal,0,",",",");
                
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'><b></b></td>";
                echo "<td nowrap align='right'><b></b></td>";
                echo "<td nowrap align='right'><b>$ptotal</b></td>";
                echo "<td nowrap align='right'><b>$ptotal_k</b></td>";
                echo "<td nowrap align='right'><b>$p_saldo_awal</b></td>";
                echo "</tr>";
        ?>
        </tbody>
    </table>
    <br/>&nbsp;<br/>&nbsp;
    
    
    
    
    <?PHP
    hapusdata:
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp06");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp08");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp09");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp10");
        
        mysqli_close($cnmy);
    ?>

</div>
            


    <?PHP if ($ppilihrpt!="excel") { ?>

        
        <!-- Bootstrap -->
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    
        <!-- Datatables -->
        <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
        <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
        <script src="vendors/jszip/dist/jszip.min.js"></script>
        <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
        <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

        
        
        <style>
            #myBtn {
                display: none;
                position: fixed;
                bottom: 20px;
                right: 30px;
                z-index: 99;
                font-size: 18px;
                border: none;
                outline: none;
                background-color: red;
                color: white;
                cursor: pointer;
                padding: 15px;
                border-radius: 4px;
                opacity: 0.5;
            }

            #myBtn:hover {
                background-color: #555;
            }

            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 20px;
                /*overflow-x:auto;*/
            }
        </style>

        <style>
            .divnone {
                display: none;
            }
            #datatable2, #datatable3 {
                color:#000;
                font-family: "Arial";
            }
            #datatable2 th, #datatable3 th {
                font-size: 12px;
            }
            #datatable2 td, #datatable3 td { 
                font-size: 11px;
            }
        </style>
        
    <?PHP }else{ ?>
        <style>
            .tjudul {
                font-family: Georgia, serif;
                font-size: 15px;
                margin-left:10px;
                margin-right:10px;
            }
            .tjudul td {
                padding: 4px;
            }
            #datatable2, #datatable3 {
                font-family: Georgia, serif;
                margin-left:10px;
                margin-right:10px;
            }
            #datatable2 th, #datatable2 td, #datatable3 th, #datatable3 td {
                padding: 4px;
            }
            #datatable2 thead, #datatable3 thead{
                background-color:#cccccc; 
                font-size: 12px;
            }
            #datatable2 tbody, #datatable3 tbody{
                font-size: 11px;
            }
        </style>
    <?PHP } ?>
    
</body>

    <script>
        // SCROLL
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};
        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("myBtn").style.display = "block";
            } else {
                document.getElementById("myBtn").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
        // END SCROLL
    
    
        $(document).ready(function() {
            var table = $('#datatable2, #datatable3').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [9,10,11,12,13] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,8,9,10,11,12,13] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
        
        
        function TambahDataInput(eidbank){
            $.ajax({
                type:"post",
                url:"module/mod_br_danabank/tambah_trans_bank.php?module=viewisibankspdall",
                data:"uidbank="+eidbank,
                success:function(data){
                    $("#myModal").html(data);
                }
            });
        }
    
    
    
    </script>

</html>