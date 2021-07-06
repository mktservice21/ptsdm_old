<?PHP
    include $pilih_koneksi; $cnit=$cnmy;
    
    
    $ptgl_pil01= date("Y-m-01", strtotime($ptgl_pillih));
    $ptgl_pil02= date('Y-m-01', strtotime('+1 month', strtotime($ptgl_pillih)));
    
    if ($scaperiode2=="1") $ptgl_pil02=$ptgl_pil01;
    
    $ptgl_pil_sbl= date('Y-m-01', strtotime('-1 month', strtotime($ptgl_pillih)));
    
    $m_periode1 = date("Y-m", strtotime($ptgl_pil01));
    $m_periode2 = date("Y-m", strtotime($ptgl_pil02));
    $m_periode_sbl = date("Y-m", strtotime($ptgl_pil_sbl));
    
    $nfilterkaryawan="";
    if ($iproses_simpandata==true AND !empty($u_filterkaryawan)) {
        $nfilterkaryawan=" AND karyawanid IN $u_filterkaryawan ";
    }
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp00 =" dbtemp.DTBRRETRLCLS00_".$puserid."_$now ";
    $tmp01 =" dbtemp.DTBRRETRLCLS01_".$puserid."_$now ";
    $tmp02 =" dbtemp.DTBRRETRLCLS02_".$puserid."_$now ";
    $tmp03 =" dbtemp.DTBRRETRLCLS03_".$puserid."_$now ";
    $tmp04 =" dbtemp.DTBRRETRLCLS04_".$puserid."_$now ";
    $tmp05 =" dbtemp.DTBRRETRLCLS05_".$puserid."_$now ";
    $tmp06 =" dbtemp.DTBRRETRLCLS06_".$puserid."_$now ";
    
    $u_fitergroup="";
    if ($stsreport=="C") {
        if (!empty($pprosid_sts)) $u_fitergroup = " AND igroup='$pprosid_sts' ";
    }
    
    $query = "select a.*, b.nama nama_karyawan from dbmaster.t_brrutin_ca_close a "
            . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " WHERE DATE_FORMAT(a.bulan,'%Y-%m')='$m_periode1' $u_fitergroup";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    if ($stsreport=="C") {
        
        $query = "SELECT * from dbmaster.t_brrutin_ca_close_head WHERE DATE_FORMAT(bulan,'%Y-%m')='$m_periode1' $u_fitergroup";
        $query = "create TEMPORARY table $tmp00 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        goto selesai;
    }else{
        $query = "SELECT * from dbmaster.t_brrutin_ca_close_head WHERE DATE_FORMAT(bulan,'%Y-%m')='DELETE_KOSONG'";
        $query = "create TEMPORARY table $tmp00 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    //RUTIN
    $query = "select idrutin, karyawanid, divisi, icabangid, areaid, keterangan, 
            atasan1, atasan2, atasan3, atasan4, jabatanid, jumlah  
            from dbmaster.t_brrutin0 
            WHERE stsnonaktif <> 'Y' AND kode = 2 and divisi<>'OTC' AND ifnull(tgl_fin,'') <> '' AND ifnull(tgl_fin,'0000-00-00') <> '0000-00-00' AND 
            date_format(bulan,'%Y-%m') ='$m_periode1' "
            . " AND idrutin NOT IN (select distinct IFNULL(idrutin,'') FROM $tmp01) $nfilterkaryawan";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select idrutin, karyawanid, keterangan, 
            CAST('' as CHAR(5)) as divisi, CAST('' as CHAR(10)) as icabangid, CAST('' as CHAR(10)) as areaid, 
            CAST('' as CHAR(10)) as atasan1, CAST('' as CHAR(10)) as atasan2, CAST('' as CHAR(10)) as atasan3, 
            CAST('' as CHAR(10)) as atasan4, CAST('' as CHAR(10)) as jabatanid, 
            sum(jumlah) jumlah FROM $tmp02 GROUP BY 1,2,3"; 
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp04 a JOIN $tmp02 b on a.karyawanid=b.karyawanid "
            . " SET a.divisi=b.divisi, a.icabangid=b.icabangid, a.areaid=b.areaid, "
            . " a.atasan1=b.atasan1, a.atasan2=b.atasan2, a.atasan3=b.atasan3, a.atasan4=b.atasan4, a.jabatanid=b.jabatanid"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    //CA
                
                // cari ca yang sudah closing
                $query = "select distinct idca1 idca FROM $tmp01 WHERE IFNULL(idca1,'')<>''";
                $query = "create TEMPORARY table $tmp05 ($query)"; 
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "INSERT INTO $tmp05 (idca) select distinct idca2 idca FROM $tmp01 WHERE IFNULL(idca2,'')<>''"; 
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                // end cari ca yang sudah closing
                
    $query = "select periode, idca, karyawanid, divisi, icabangid, areaid, keterangan,
            atasan1, atasan2, atasan3, atasan4, jabatanid, jumlah 
            from dbmaster.t_ca0 
            WHERE jenis_ca='lk' AND stsnonaktif <> 'Y' and divisi<>'OTC' AND ifnull(tgl_fin,'') <> '' AND ifnull(tgl_fin,'0000-00-00') <> '0000-00-00' AND 
            DATE_FORMAT(periode,'%Y-%m') between '$m_periode1' AND '$m_periode2' "
            . " AND idca NOT IN (select distinct IFNULL(idca,'') FROM $tmp05) $nfilterkaryawan";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        //$query = "SELECT idca1 idca, SUM(selisih) selisih FROM (select distinct karyawanid, idca1, idca2, selisih from dbmaster.t_brrutin_ca_close WHERE "
        //        . " DATE_FORMAT(bulan,'%Y-%m') = '$m_periode1' AND periode_ca2='2' AND IFNULL(selisih,0) >0 $nfilterkaryawan) as TBL GROUP BY 1";
        
        $query = "select distinct bulan periode, idca1 idca, karyawanid, divisi, icabangid, areaid, keterangan,
            atasan1, atasan2, atasan3, atasan4, jabatanid, selisih jumlah from dbmaster.t_brrutin_ca_close 
            WHERE DATE_FORMAT(bulan,'%Y-%m') = '$m_periode1' AND periode_ca2='1' AND IFNULL(selisih,0) >0 $nfilterkaryawan";
        $query = "create TEMPORARY table $tmp06 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        if ($scaperiode2=="1") {
        }else{
            $query = "INSERT INTO $tmp03 SELECT * FROM $tmp06";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
        
        mysqli_query($cnit, "drop TEMPORARY table $tmp06");
        
        /*
            $query = "select periode, idca, karyawanid, divisi, icabangid, areaid, keterangan,
                    atasan1, atasan2, atasan3, atasan4, jabatanid, jumlah 
                    from dbmaster.t_ca0 
                    WHERE jenis_ca='lk' AND stsnonaktif <> 'Y' and divisi<>'OTC' AND ifnull(tgl_fin,'') <> '' AND ifnull(tgl_fin,'0000-00-00') <> '0000-00-00' AND 
                    DATE_FORMAT(periode,'%Y-%m') = '$m_periode1' "
                    . " AND karyawanid in ('0000001038', '0000001680', '0000001871', '0000001950', '0000001934', '0000001988', '0000002119', '0000002058') $nfilterkaryawan";
            $query = "create TEMPORARY table $tmp06 ($query)"; 
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "INSERT INTO $tmp03 SELECT * FROM $tmp06";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        mysqli_query($cnit, "drop TEMPORARY table $tmp06");
        */
        
        //goto hapusdata;
        
        
        
    // hapus temporari ca yang sudah closing
    $query = "DROP TEMPORARY table $tmp05"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select periode, idca, karyawanid, keterangan, 
            CAST('' as CHAR(5)) as divisi, CAST('' as CHAR(10)) as icabangid, CAST('' as CHAR(10)) as areaid, 
            CAST('' as CHAR(10)) as atasan1, CAST('' as CHAR(10)) as atasan2, CAST('' as CHAR(10)) as atasan3, 
            CAST('' as CHAR(10)) as atasan4, CAST('' as CHAR(10)) as jabatanid, 
            sum(jumlah) jumlah FROM $tmp03 GROUP BY 1,2,3,4"; 
    $query = "create TEMPORARY table $tmp05 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp05 a JOIN $tmp03 b on a.karyawanid=b.karyawanid "
            . " SET a.divisi=b.divisi, a.icabangid=b.icabangid, a.areaid=b.areaid, "
            . " a.atasan1=b.atasan1, a.atasan2=b.atasan2, a.atasan3=b.atasan3, a.atasan4=b.atasan4, a.jabatanid=b.jabatanid"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    
    //RUTIN
    $query = "select *, jumlah credit, CAST(null as DECIMAL(20,2)) as saldo, CAST('' as CHAR(10)) as idca1, CAST(null as DECIMAL(20,2)) as ca1, "
            . " CAST('' as CHAR(10)) as idca2, CAST(null as DECIMAL(20,2)) as ca2, CAST(null as DECIMAL(20,2)) as selisih,"
            . " CAST(null as DECIMAL(20,2)) as jmltrans, CAST(null as DECIMAL(20,2)) as jml_adj, CAST(NULL as date) as bulan from $tmp04";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //CA
    $query = "select * from $tmp05";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    //divisi, atasan, jabatan disamakan (takut ada yang beda)
    $query = "UPDATE $tmp03 a JOIN $tmp02 b on a.karyawanid=b.karyawanid "
            . " SET a.divisi=b.divisi, "
            . " a.atasan1=b.atasan1, a.atasan2=b.atasan2, a.atasan3=b.atasan3, a.atasan4=b.atasan4, a.jabatanid=b.jabatanid"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //END divisi, atasan, jabatan disamakan (takut ada yang beda)
    
    
    //insert yang belum ada di rutin
    $query = "INSERT INTO $tmp02 (idrutin, karyawanid, divisi, icabangid, areaid, atasan1, atasan2, atasan3, atasan4, jabatanid)"
            . " SELECT DISTINCT '' as idrutin, karyawanid, IFNULL(divisi,''), IFNULL(icabangid,''), IFNULL(areaid,''), "
            . " IFNULL(atasan1,''), IFNULL(atasan2,''), IFNULL(atasan3,''), IFNULL(atasan4,''), IFNULL(jabatanid,'') "
            . " FROM $tmp03 WHERE karyawanid NOT IN (select distinct IFNULL(karyawanid,'') FROM $tmp04)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //update saldo awal dari total rutin
    $query="UPDATE $tmp02 a SET a.saldo=(select sum(jumlah) from $tmp04 b WHERE a.karyawanid=b.karyawanid)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
            //update ca1 dari ca periode 1
            $query="UPDATE $tmp02 a JOIN (select idca, karyawanid, keterangan, sum(jumlah) jumlah FROM $tmp03 "
                    . " WHERE DATE_FORMAT(periode,'%Y-%m') = '$m_periode1' GROUP BY 1,2,3) as b on a.karyawanid=b.karyawanid SET "
                    . " a.idca1=b.idca, a.ca1=b.jumlah, a.keterangan=b.keterangan";
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //update ca2 dari ca periode 2
    $query="UPDATE $tmp02 a JOIN (select idca, karyawanid, keterangan, sum(jumlah) jumlah FROM $tmp03 "
            . " WHERE DATE_FORMAT(periode,'%Y-%m') = '$m_periode2' GROUP BY 1,2,3) as b on a.karyawanid=b.karyawanid SET "
            . " a.idca2=b.idca, a.ca2=b.jumlah, a.keterangan=b.keterangan";
    if ($scaperiode2=="1") {
    }else{
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
    mysqli_query($cnit, "drop TEMPORARY table $tmp05");
    

                //test doang
                    //$query="UPDATE $tmp02 a SET a.ca1=ca1-53897 WHERE karyawanid='0000001668'";
                    //mysqli_query($cnit, $query);
                    //$erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                //end test doang
        
                                                    
    
    //cek selisih, jumlah transfer
    $query = "select distinct karyawanid, saldo, ca1, ca2 from $tmp02";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from $tmp03 order by karyawanid";
    $tampil_= mysqli_query($cnit, $query);
    while ($row1= mysqli_fetch_array($tampil_)) {
        $pkaryawanid=$row1['karyawanid'];
        
        $pjmlca1 = $row1['ca1'];
        $pjmllk = $row1['saldo'];
        $pjmlca2 = $row1['ca2'];
        
        $pjumlahadj=0;
        
        if (empty($pjmlca1)) $pjmlca1=0;
        if (empty($pjmllk)) $pjmllk=0;
        if (empty($pjmlca2)) $pjmlca2=0;
        
        $pselisih=(double)$pjmlca1-(double)$pjmllk;

        $pjmltrans= ( (double)$pjmlca2-(double)$pselisih ) + (double)$pjumlahadj;
        //if ((double)$pjmltrans<0) $pjmltrans=0;
        if ($pselisih>0 AND (double)$pjmlca2==0) $pjmltrans=0;
        elseif ((double)$pselisih>0 AND (double)$pjmlca2>0) $pjmltrans=(double)$pjmlca2 + (double)$pjumlahadj;
        elseif ((double)$pselisih==0 AND (double)$pjmlca2>0) $pjmltrans=(double)$pjmlca2 + (double)$pjumlahadj;
        
        if (empty($pselisih)) $pselisih=0;
        if (empty($pjmltrans)) $pjmltrans=0;
        
        $query="UPDATE $tmp02 a SET a.selisih='$pselisih', jmltrans='$pjmltrans' WHERE karyawanid='$pkaryawanid'";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                    
        
    }
    
    //END cek selisih, jumlah transfer
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    
    
    
    //RUTIN DETAIL
    $query = "select idrutin, coa, nobrid, qty, rp, rptotal, notes, deskripsi, tgl1, tgl2, CAST('' as CHAR(5)) as divisi 
            from dbmaster.t_brrutin1  
            WHERE idrutin IN (select distinct IFNULL(idrutin,'') FROM $tmp02 WHERE IFNULL(idrutin,'') <>'')";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp04 a JOIN (select distinct idrutin, divisi from $tmp02 WHERE IFNULL(idrutin,'') <>'') as b "
            . " on a.idrutin=b.idrutin "
            . " SET a.divisi=b.divisi"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp04 a JOIN dbmaster.posting_coa_rutin b "
            . " on a.divisi=b.divisi AND a.nobrid=b.nobrid "
            . " SET a.coa=b.COA4"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.nama nama_brid, c.NAMA4 from $tmp04 a LEFT JOIN dbmaster.t_brid b ON a.nobrid=b.nobrid "
            . " LEFT JOIN dbmaster.coa_level4 c on a.coa=c.COA4";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
    
    //END RUTIN DETAIL
    
    
    //CA DETAIL
    $query = "select idca, coa, nobrid, tgl1, tgl2, qty, rp, rptotal, notes, CAST('' as CHAR(5)) as divisi 
            from dbmaster.t_ca1  
            WHERE idca IN (select distinct IFNULL(idca1,'') FROM $tmp02 WHERE IFNULL(idca1,'')<>'')";
    $query = "create TEMPORARY table $tmp05 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "INSERT $tmp05"
            . " select idca, coa, nobrid, tgl1, tgl2, qty, rp, rptotal, notes, CAST('' as CHAR(5)) as divisi from dbmaster.t_ca1 "
            . " WHERE idca IN (select distinct IFNULL(idca2,'') FROM $tmp02 WHERE IFNULL(idca2,'')<>'')"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp05 a JOIN (select distinct idca1, divisi from $tmp02 WHERE IFNULL(idca1,'') <>'') as b "
            . " on a.idca=b.idca1 "
            . " SET a.divisi=b.divisi"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp05 a JOIN (select distinct idca2, divisi from $tmp02 WHERE IFNULL(idca2,'') <>'') as b "
            . " on a.idca=b.idca2 "
            . " SET a.divisi=b.divisi WHERE IFNULL(a.divisi,'')=''"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.*, b.nama nama_brid, c.NAMA4 from $tmp05 a LEFT JOIN dbmaster.t_brid b ON a.nobrid=b.nobrid "
            . " LEFT JOIN dbmaster.coa_level4 c on a.coa=c.COA4";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp05");
    
    //END CA DETAIL
    
    
    //update per atasan
    $nox_1=3;
    $nox_2=4;
    for ($ix=1;$ix<=3;$ix++) {
        //echo "$nox_1 da $nox_2<br/>";
        
        $inmfield_1="atasan".$nox_1;
        $inmfield_2="atasan".$nox_2;
        
        $query = "UPDATE $tmp02 SET $inmfield_1=$inmfield_2 WHERE IFNULL($inmfield_1,'')=''";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $nox_1=$nox_1-1;
        $nox_2=$nox_2-1;
    }
    //END update per atasan
    
    
    
    
    $query = "ALTER TABLE $tmp02 ADD nama_karyawan VARCHAR(200), ADD nourut VARCHAR(1), ADD kuranglebihsaldo DECIMAL(20,2), ADD kuranglebihca1 DECIMAL(20,2),"
            . " ADD periode_ca1 CHAR(1), ADD periode_ca2 CHAR(1), ADD sts CHAR(1), ADD igroup INT(4)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    //update nama_karyawan
    $query="UPDATE $tmp02 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid SET a.nama_karyawan=b.nama";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnit, "ALTER TABLE $tmp02 CHANGE nourut nourut INT(10) AUTO_INCREMENT PRIMARY KEY;");
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
      
    
    
    $query="UPDATE $tmp02 SET bulan='$ptgl_pil01', periode_ca1='$scaperiode1', periode_ca2='$scaperiode2', sts='C'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    $query = "select * from $tmp02";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    $query = "select * from $tmp03";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    $query = "select * from $tmp04";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    if ($scaperiode2=="1") {
        
    }else{
        //cari adjustment dari closingan bulan lalu
        $query = "SELECT karyawanid, SUM(selisih) selisih FROM (select distinct karyawanid, idca1, idca2, selisih from dbmaster.t_brrutin_ca_close WHERE "
                . " periode_ca2='2' AND DATE_FORMAT(bulan,'%Y-%m')='$m_periode_sbl' AND IFNULL(selisih,0) >0 $nfilterkaryawan) as TBL GROUP BY 1";
        $query = "create TEMPORARY table $tmp05 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        mysqli_query($cnit, "drop TEMPORARY table $tmp04");
        $query ="select distinct karyawanid, kembali_rp from dbmaster.t_brrutin_outstanding WHERE 
            DATE_FORMAT(bulan,'%Y-%m')='$m_periode_sbl' and ots_status='1' and IFNULL(kembali_rp,0)<>0 AND divisi<>'OTC' $nfilterkaryawan group by 1";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "DELETE FROM $tmp05 WHERE karyawanid IN (select distinct IFNULL(karyawanid,'') FROM $tmp04)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }




        $query = "UPDATE $tmp01 a JOIN $tmp05 b on a.karyawanid=b.karyawanid SET "
                . " a.jml_adj=b.selisih, a.jmltrans=IFNULL(a.jmltrans,0)-IFNULL(b.selisih,0)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    //END cari adjustment dari closingan bulan lalu
    
    
                                                /*
                                                    $query = "delete from $tmp03 where karyawanid not in (select distinct karyawanid from dbtemp.t_testtt_ca)"; 
                                                    mysqli_query($cnit, $query);
                                                    $query = "delete from $tmp02 where karyawanid not in (select distinct karyawanid from dbtemp.t_testtt_ca)"; 
                                                    mysqli_query($cnit, $query);
                                                 * 
                                                 */
    /*
    $query = "ALTER table $tmp01 ADD nsudahada CHAR(1)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbtemp.t_testtt_ca b on a.karyawanid=b.karyawanid SET a.nsudahada='Y'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 SET selisih=0, jmltrans=ca2 where nsudahada='Y'";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    */
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp06");
    
    goto selesai;
    
hapusdata:
    mysqli_query($cnit, "drop TEMPORARY table $tmp00");
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
    mysqli_query($cnit, "drop TEMPORARY table $tmp05");
    mysqli_query($cnit, "drop TEMPORARY table $tmp06");
    
selesai:
?>