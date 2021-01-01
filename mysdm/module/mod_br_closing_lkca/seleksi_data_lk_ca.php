<?php
    
    
    $tgl02= date('Y-m-d', strtotime('+1 month', strtotime($tgl01)));
    $periode2 = date("Y-m", strtotime($tgl02));
    $per2 = date("F Y", strtotime($tgl02));
    
    $periodesebelumnya= date('Y-m', strtotime('-1 month', strtotime($tgl01)));
    
    if (isset($_POST['sts_apv'])) {
        $stsapv = $_POST['sts_apv'];
        $fstsapv = "";
        $e_stsapv="Semua Data";
        if ($stsapv == "fin") {
            $fstsapv = " AND ifnull(tgl_fin,'') <> '' AND ifnull(tgl_fin,'0000-00-00') <> '0000-00-00' ";
            $e_stsapv="Sudah Proses Finance";
        }elseif ($stsapv == "belumfin") {
            $fstsapv = " AND (ifnull(tgl_fin,'') = '' OR ifnull(tgl_fin,'0000-00-00') = '0000-00-00') ";
            $e_stsapv="Belum Proses Finance";
        }
    }else{
        $fstsapv = " AND ifnull(tgl_fin,'') <> '' AND ifnull(tgl_fin,'0000-00-00') <> '0000-00-00' ";
        $e_stsapv="Sudah Proses Finance";
    }
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRETRLCLS01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DTBRRETRLCLS02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DTBRRETRLCLS03_".$_SESSION['IDCARD']."_$now ";
    $tmp04 =" dbtemp.DTBRRETRLCLS04_".$_SESSION['IDCARD']."_$now ";
    $tmp05 =" dbtemp.DTBRRETRLCLS05_".$_SESSION['IDCARD']."_$now ";
    $tmp06 =" dbtemp.DTBRRETRLCLS06_".$_SESSION['IDCARD']."_$now ";
    $tmp07 =" dbtemp.DTBRRETRLCLS07_".$_SESSION['IDCARD']."_$now ";
    $tmp08 =" dbtemp.DTBRRETRLCLS08_".$_SESSION['IDCARD']."_$now ";
    $tmp09 =" dbtemp.DTBRRETRLCLS09_".$_SESSION['IDCARD']."_$now ";
    
    $ptgltrans="";
    $pnobukti="";

    $stspilihan="C";
    $sudahclosing="";
        
    $filterstsclose=" AND sts='B'";
    if ($stsreport=="C") {
        $filterstsclose=" AND sts='C'";
    }elseif ($stsreport=="S") {
        $filterstsclose=" AND sts='S'";
    }elseif ($stsreport=="") {
        $filterstsclose=" AND sts=''";
    }
    
    if (isset($caridatadarigl)) {
        if (!empty($caridatadarigl)) $filterstsclose=" AND sts IN $caridatadarigl";
    }
    
    $filterdivisi="";
    if (isset($caridatadivisigl)) {
        if (!empty($caridatadivisigl)) $filterdivisi=" AND divisi ='$caridatadivisigl'";
    }
    
    $filterdarispd="";
    if (isset($filternobr)) {
        if (!empty($filternobr)) {
            $filterdarispd=$filternobr;
        }
    }
    //cek data yang sudah proses closing
    if (!empty($filterdarispd)) {
        $query = "select * from dbmaster.t_brrutin_ca_close WHERE idinput IN $filterdarispd";
    }else{
        $query = "select * from dbmaster.t_brrutin_ca_close WHERE DATE_FORMAT(bulan,'%Y-%m')='$periode1' $filterstsclose $filterdivisi";
    }
    $query = "create Temporary table $tmp08 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select * from $tmp08";
    $ketemu= mysqli_num_rows(mysqli_query($cnit, $query));
    if ($ketemu==0){
    
        //cari CA bulan sekrang yang sebelumnya sudah closing
        $query = "select idca2 from dbmaster.t_brrutin_ca_close WHERE ifnull(idca2,'') <> '' AND DATE_FORMAT(bulan,'%Y-%m')='$periodesebelumnya' $filterdivisi";
        //echo "$query<br/>";
        $query = "create Temporary table $tmp09 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    
        $filterstsclose="";
        if ($stsreport=="C") {
            $filterstsclose=" AND sts='C'";
        }elseif ($stsreport=="S") {
            $filterstsclose=" AND sts='S'";
        }
    
        $query = "create TEMPORARY table $tmp07 (idrutin VARCHAR(10), ket VARCHAR(3), sts VARCHAR(1))";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        $query = "select DISTINCT idrutin, 'LK1', sts ket from dbmaster.t_brrutin_ca_close WHERE ifnull(idrutin,'') <> '' AND DATE_FORMAT(bulan,'%Y-%m')='$periode1' $filterstsclose $filterdivisi";
        $query .= " UNION select idca1, 'CA1', sts ket from dbmaster.t_brrutin_ca_close WHERE ifnull(idca1,'') <> '' AND DATE_FORMAT(bulan,'%Y-%m')='$periode1' $filterstsclose $filterdivisi";
        $query .= " UNION select idca2, 'CA2', sts ket from dbmaster.t_brrutin_ca_close WHERE ifnull(idca2,'') <> '' AND DATE_FORMAT(bulan,'%Y-%m')='$periode1' $filterstsclose $filterdivisi";
        $query = "INSERT INTO $tmp07 (idrutin, ket, sts) $query"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        //RUTIN
        $query = "select idrutin, karyawanid, divisi, icabangid, areaid, keterangan, tgltrans, nobukti, jumlah from dbmaster.t_brrutin0 
                WHERE stsnonaktif <> 'Y' AND kode = 2 and divisi<>'OTC' AND 
                date_format(bulan,'%Y-%m') ='$periode1' $fstsapv $filterdivisi";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        //RUTIN DETAIL
        $query = "select idrutin, coa, nobrid, qty, rp, rptotal, notes, deskripsi, tgl1, tgl2 from dbmaster.t_brrutin1  
                WHERE idrutin IN (select distinct IFNULL(idrutin,'') FROM $tmp01)";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        //CA 1
        $query = "select idca, karyawanid, divisi, icabangid, areaid, keterangan, tgltrans, jumlah from dbmaster.t_ca0 
                WHERE jenis_ca='lk' AND stsnonaktif <> 'Y' and divisi<>'OTC' AND 
                DATE_FORMAT(periode,'%Y-%m') = '$periode1' $fstsapv $filterdivisi";
        //echo "$query<br/>";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        //hapus ca sekarang yang sebelumya sudah closing
        $query = "DELETE FROM $tmp03 WHERE ifnull(idca,'') NOT IN (select ifnull(idca2,'') FROM $tmp09)";
        mysqli_query($cnit, $query);
        mysqli_query($cnit, "drop TEMPORARY table $tmp09");
        
        
        //CA 2
        $query = "select idca, karyawanid, divisi, icabangid, areaid, keterangan, jumlah from dbmaster.t_ca0 
                WHERE jenis_ca='lk' AND stsnonaktif <> 'Y' and divisi<>'OTC' AND 
                DATE_FORMAT(periode,'%Y-%m') = '$periode2' $fstsapv $filterdivisi";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        
        //hapus data yang sudah pernah closing (transfer)
        if ($stsreport=="B") {
            mysqli_query($cnit, "DELETE FROM $tmp01 WHERE idrutin IN (select distinct ifnull(idrutin,'') FROM $tmp07 WHERE ket='LK1')");
            mysqli_query($cnit, "DELETE FROM $tmp03 WHERE idca IN (select distinct ifnull(idrutin,'') FROM $tmp07 WHERE ket = 'CA1')");
            mysqli_query($cnit, "DELETE FROM $tmp04 WHERE idca IN (select distinct ifnull(idrutin,'') FROM $tmp07 WHERE ket = 'CA2')");
        }elseif ($stsreport=="C" OR $stsreport=="S") {
            mysqli_query($cnit, "DELETE FROM $tmp01 WHERE idrutin NOT IN (select distinct ifnull(idrutin,'') FROM $tmp07 WHERE ket='LK1')");
            mysqli_query($cnit, "DELETE FROM $tmp03 WHERE idca NOT IN (select distinct ifnull(idrutin,'') FROM $tmp07 WHERE ket = 'CA1')");
            mysqli_query($cnit, "DELETE FROM $tmp04 WHERE idca NOT IN (select distinct ifnull(idrutin,'') FROM $tmp07 WHERE ket = 'CA2')");
        }
        

        //CA DETAIL 1
        $query = "select idca, coa, nobrid, tgl1, tgl2, qty, rp, rptotal, notes from dbmaster.t_ca1 WHERE 
                 idca IN (select distinct IFNULL(idca,'') FROM $tmp03)";
        $query = "create TEMPORARY table $tmp05 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        //CA DETAIL 2
        $query = "INSERT INTO $tmp05 (idca, coa, nobrid, tgl1, tgl2, qty, rp, rptotal, notes) 
                select idca, coa, nobrid, tgl1, tgl2, qty, rp, rptotal, notes from dbmaster.t_ca1 WHERE 
                idca IN (select distinct IFNULL(idca,'') FROM $tmp04)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }


        
        //divisi disamakan (takut ada yang beda)
        $tmp01x =" dbtemp.DTBRRETRLCLS01x_".$_SESSION['IDCARD']."_$now ";
        $query = "select distinct karyawanid, CAST('' as CHAR(5)) as divisi from $tmp01";
        $query = "create TEMPORARY table $tmp01x ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_query($cnit, "UPDATE $tmp01x a SET a.divisi=IFNULL((select divisi from $tmp01 b WHERE a.karyawanid=b.karyawanid LIMIT 1),a.divisi)");
        mysqli_query($cnit, "UPDATE $tmp01 a SET a.divisi=IFNULL((select divisi from $tmp01x b WHERE IFNULL(b.divisi,'')<>'' AND a.karyawanid=b.karyawanid LIMIT 1),a.divisi)");
        mysqli_query($cnit, "drop TEMPORARY table $tmp01x");
        
        
        mysqli_query($cnit, "UPDATE $tmp03 a SET a.divisi=IFNULL((select divisi from $tmp01 b WHERE a.karyawanid=b.karyawanid LIMIT 1),a.divisi)");
        mysqli_query($cnit, "UPDATE $tmp04 a SET a.divisi=IFNULL((select divisi from $tmp01 b WHERE a.karyawanid=b.karyawanid LIMIT 1),a.divisi)");
        mysqli_query($cnit, "UPDATE $tmp04 a SET a.divisi=IFNULL((select divisi from $tmp03 b WHERE a.karyawanid=b.karyawanid LIMIT 1),a.divisi)");

        //insert data karyawan dari rutin
        $query = "select distinct divisi, idrutin, karyawanid, keterangan from $tmp01";
        $query = "create TEMPORARY table $tmp06 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        //insert data karyawan dari ca 1
        $query = "INSERT INTO $tmp06 (karyawanid, divisi, keterangan)"
                . " select DISTINCT a.karyawanid, a.divisi, a.keterangan FROM $tmp03 as a WHERE "
                . " a.karyawanid not in (select distinct IFNULL(b.karyawanid,'') from $tmp01 b)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        //insert data karyawan dari ca 1
        $query = "INSERT INTO $tmp06 (karyawanid, divisi, keterangan)"
                . " select DISTINCT a.karyawanid, a.divisi, a.keterangan FROM $tmp04 as a WHERE "
                . " a.karyawanid not in (select distinct IFNULL(b.karyawanid,'') from $tmp01 b) AND " 
                . " a.karyawanid not in (select distinct IFNULL(c.karyawanid,'') from $tmp03 c)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }


        $query = "ALTER TABLE $tmp06 ADD nama_karyawan VARCHAR(200), ADD idca1 VARCHAR(10), ADD idca2 VARCHAR(10), "
                . " ADD credit DECIMAL(20,2), ADD saldo DECIMAL(20,2), "
                . " ADD ca1 DECIMAL(20,2), ADD selisih DECIMAL(20,2), ADD ca2 DECIMAL(20,2), ADD jmltrans DECIMAL(20,2), "
                . " ADD sts VARCHAR(1), ADD nourut VARCHAR(1), ADD tgltrans DATE, ADD nobukti VARCHAR(50)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        mysqli_query($cnit, "UPDATE $tmp06 a SET a.nama_karyawan=(select b.nama from hrd.karyawan b WHERE a.karyawanid=b.karyawanId)");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        mysqli_query($cnit, "UPDATE $tmp06 a SET a.credit=IFNULL((select SUM(b.jumlah) jumlah from $tmp01 b WHERE a.karyawanid=b.karyawanid AND a.idrutin=b.idrutin),0)");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        mysqli_query($cnit, "UPDATE $tmp06 a SET a.saldo=IFNULL((select SUM(b.jumlah) jumlah from $tmp01 b WHERE a.karyawanid=b.karyawanid),0)");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }


        mysqli_query($cnit, "UPDATE $tmp06 a SET a.idca1=IFNULL((select idca from $tmp03 b WHERE a.karyawanid=b.karyawanid LIMIT 1),'')");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        mysqli_query($cnit, "UPDATE $tmp06 a SET a.idca2=IFNULL((select idca from $tmp04 b WHERE a.karyawanid=b.karyawanid),'')");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        

        mysqli_query($cnit, "UPDATE $tmp06 a SET a.ca1=IFNULL((select SUM(b.jumlah) jumlah from $tmp03 b WHERE b.idca=a.idca1),0)");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

        mysqli_query($cnit, "UPDATE $tmp06 a SET a.ca2=IFNULL((select SUM(b.jumlah) jumlah from $tmp04 b WHERE b.idca=a.idca2),0)");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query = "select idrutin from $tmp07 WHERE sts IN ('C', 'S')";
        $ketemucls= mysqli_num_rows(mysqli_query($cnit, $query));
        if ($ketemucls>0){

            //cari salah satu yang sudah closing
            $query = "select idrutin from $tmp07 WHERE idrutin in (select distinct IFNULL(idrutin,'') FROM $tmp06 WHERE IFNULL(idrutin,'') <> '')";
            $ketemuslhsatu= mysqli_num_rows(mysqli_query($cnit, $query));
            if ($ketemuslhsatu==0){
                mysqli_query($cnit, "UPDATE $tmp06 set ca1=0, ca2=0, idca1='', idca2=''");
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
            }

            $sudahclosing="SUDAH";
            $stspilihan="S";//JUKA SUDAH PERNAH CLOSE MAKA STS=SUSULAN

            $query = "select tgltrans from $tmp01 where 
                idrutin in (select distinct IFNULL(idrutin,'') from $tmp06) AND 
                IFNULL(tgltrans,'0000-00-00') <> '0000-00-00'";
            $rx= mysqli_fetch_array(mysqli_query($cnmy, $query));
            if (!empty($rx['tgltrans'])) $ptgltrans= date("d F Y", strtotime($rx['tgltrans']));

            $query = "select nobukti from $tmp01 where 
                idrutin in (select distinct IFNULL(idrutin,'') from $tmp06) AND 
                IFNULL(nobukti,'') <> ''";
            $rx= mysqli_fetch_array(mysqli_query($cnmy, $query));
            if (!empty($rx['nobukti'])) $pnobukti= $rx['nobukti'];
            
            
        }
        
        mysqli_query($cnit, "DELETE FROM $tmp06 WHERE IFNULL(credit,0)=0 AND IFNULL(saldo,0)=0 AND IFNULL(ca1,0)=0 AND IFNULL(ca2,0)=0");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }


        mysqli_query($cnit, "UPDATE $tmp06 set sts='$stspilihan'");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
                
        mysqli_query($cnit, "ALTER TABLE $tmp06 CHANGE nourut nourut INT(10) AUTO_INCREMENT PRIMARY KEY;");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }


        mysqli_query($cnit, "drop TEMPORARY table $tmp01");

        $query = "create TEMPORARY table $tmp01 (select *, CAST(0 as DECIMAL(20,2)) jml_adj from $tmp06)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        
        
        
        //cari outstanding dan ADJ
        $tmp11 =" dbtemp.DTBRRETRLCLS11_".$_SESSION['IDCARD']."_$now ";
        $tmp12 =" dbtemp.DTBRRETRLCLS12_".$_SESSION['IDCARD']."_$now ";
        $tmp13 =" dbtemp.DTBRRETRLCLS13_".$_SESSION['IDCARD']."_$now ";
        
        //cari yang masih hutang dibulan sebelumnya
        $query = "select distinct karyawanid, saldo, ca1, (IFNULL(ca1,0)-IFNULL(saldo,0)) selisih, CAST(0 as DECIMAL(20,2)) as kembali_rp from dbmaster.t_brrutin_ca_close WHERE (IFNULL(ca1,0)-IFNULL(saldo,0)) > 0 AND DATE_FORMAT(bulan,'%Y-%m')='$periodesebelumnya' $filterdivisi";
        $query = "create TEMPORARY table $tmp11 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //cari yang sudah mengembalikan
        $query = "select karyawanid, ots_status, kembali_rp from dbmaster.t_brrutin_outstanding WHERE karyawanid IN "
                . " (select distinct IFNULL(karyawanid,'') from $tmp11) AND DATE_FORMAT(bulan,'%Y-%m')='$periodesebelumnya'";
        $query = "create TEMPORARY table $tmp12 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "UPDATE $tmp11 a SET a.kembali_rp=(select b.kembali_rp FROM $tmp12 b WHERE IFNULL(ots_status,'')='1' AND a.karyawanid=b.karyawanid)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        // pisahkan jenis / status ots. pembulatan / uang muka
        $query = "select a.*, b.ots_status, b.jml_adj from $tmp11 a LEFT JOIN "
                . "(select karyawanid, ots_status, kembali_rp as jml_adj FROM $tmp12 WHERE IFNULL(ots_status,'')<>'1') as b ON a.karyawanid=b.karyawanid";
        $query = "create TEMPORARY table $tmp13 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //hapus yang statusnya pembulatan dan sudah ada OTS
        $query = "DELETE FROM $tmp13 WHERE IFNULL(ots_status,'')='3'";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //update yang sama sekali belum mengembalikan uang
        $query = "UPDATE $tmp13 a SET a.jml_adj=0-a.selisih WHERE IFNULL(a.ots_status,'')='' AND IFNULL(a.kembali_rp,0)=0";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_query($cnit, "drop TEMPORARY table $tmp11");
        mysqli_query($cnit, "CREATE TEMPORARY TABLE $tmp11 select * from $tmp01");
    
        //insert yang karyawannya masih hutang dan belum ada di temporary
        $query = "INSERT INTO $tmp01(karyawanid, nama_karyawan, divisi)"
                . "SELECT distinct a.karyawanid, b.nama, b.divisiid FROM $tmp13 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanId WHERE "
                . " IFNULL(a.jml_adj,0)<>0 AND a.karyawanid NOT IN (select distinct karyawanid FROM $tmp11)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        //update ke temporary 1
        $query = "UPDATE $tmp01 a SET a.jml_adj=(select b.jml_adj FROM $tmp13 b WHERE a.karyawanid=b.karyawanid)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
   
        
        
        
        mysqli_query($cnit, "drop TEMPORARY table $tmp11");
        mysqli_query($cnit, "drop TEMPORARY table $tmp12");
        mysqli_query($cnit, "drop TEMPORARY table $tmp13");
        
        //mysqli_query($cnit, "create  table $tmp13 select * from $tmp01 ");
        //END cari outstanding dan ADJ
        
        
        
        
        
        $hapustmp2=true;
        if (isset($_GET['module'])) {
            if ($_GET['module']=="realisasibl" OR $_GET['module']=="glrekapbrluarkota" OR $_GET['module']=="lapsuratcalk") $hapustmp2=false;
        }
        if ($hapustmp2==true) mysqli_query($cnit, "drop TEMPORARY table $tmp02");
        
        mysqli_query($cnit, "drop TEMPORARY table $tmp03");
        mysqli_query($cnit, "drop TEMPORARY table $tmp04");
        mysqli_query($cnit, "drop TEMPORARY table $tmp05");
        mysqli_query($cnit, "drop TEMPORARY table $tmp06");
        mysqli_query($cnit, "drop TEMPORARY table $tmp07");
        
        $query = "create  table $tmp03 (select * from $tmp01)"; 
        //mysqli_query($cnit, $query);
    
    }else{
        
        $query = "select tgltrans from $tmp08 where IFNULL(tgltrans,'0000-00-00') <> '0000-00-00'";
        $rx= mysqli_fetch_array(mysqli_query($cnmy, $query));
        if (!empty($rx['tgltrans'])) $ptgltrans= date("d F Y", strtotime($rx['tgltrans']));

        $query = "select nobukti from $tmp08 where IFNULL(nobukti,'') <> ''";
        $rx= mysqli_fetch_array(mysqli_query($cnmy, $query));
        if (!empty($rx['nobukti'])) $pnobukti= $rx['nobukti'];
            
        $sudahclosing="SUDAH";
        //$stspilihan="S";//JUKA SUDAH PERNAH CLOSE MAKA STS=SUSULAN
        
        $query = "select a.divisi, a.idrutin, a.karyawanid, b.nama as nama_karyawan, a.credit, 
            a.saldo, a.idca1, a.idca2, a.ca1, a.ca2, a.nourut, '' keterangan, a.sts, a.tgltrans, a.nobukti, a.idinput, a.jml_adj  
            from $tmp08 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanId ";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        if (isset($_GET['module'])) {
            if ($_GET['module']=="realisasibl" OR $_GET['module']=="glrekapbrluarkota" OR $_GET['module']=="lapsuratcalk") {
                
                //RUTIN DETAIL
                $query = "select idrutin, coa, nobrid, qty, rp, rptotal, notes, deskripsi, tgl1, tgl2 from dbmaster.t_brrutin1  
                        WHERE idrutin IN (select distinct IFNULL(idrutin,'') FROM $tmp01 WHERE IFNULL(idrutin,'') <> '')";
                $query = "create TEMPORARY table $tmp02 ($query)"; 
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                $query ="UPDATE $tmp02 SET qty=0, rp=0, rptotal=0 WHERE idrutin IN (SELECT DISTINCT IFNULL(idrutin,'') FROM $tmp01 WHERE IFNULL(saldo,0)=0 AND IFNULL(credit,0)=0)";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
                
            }
        }
        
        
    }

?>
