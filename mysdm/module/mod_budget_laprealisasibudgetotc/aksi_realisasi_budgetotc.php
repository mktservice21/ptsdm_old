<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI BIAYA MARKETING VS BUDGET CHC.xls");
    }
    
    include("config/koneksimysqli.php");
    
    $printdate= date("d/m/Y");
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmprlvsbgchcmkt00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmprlvsbgchcmkt01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprlvsbgchcmkt02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmprlvsbgchcmkt03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmprlvsbgchcmkt04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmprlvsbgchcmkt05_".$puserid."_$now ";
    
    
    $tgl1=$_POST['tahun'];
    $pbulan=date("Y-m", strtotime($tgl1));
    $ptahun=date("Y", strtotime($tgl1));
    $pblnthn=date("F Y", strtotime($tgl1));
    $pbln=date("m", strtotime($tgl1));
    
    $tgl01 = $ptahun."-01";
    $tgl02 = $ptahun."-".$pbln;
    
    
    $query = "create TEMPORARY table $tmp00 (SELECT * FROM dbmaster.t_budget_realisasi_lap_otc)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    mysqli_query($cnmy, "update $tmp00 set keterangan=keterangan2");
        
        
    
    //CA H //BM biaya marketing surabaya I & J //sewa kontrakan rumah U //service kendaraan V 
    //BR chc A //klaimdiscount B //KAS KASBON C & D //BROTC E //RUTIN LUAR KOTA F rutin G lk //insentif incentive K
    //X Kas Kecil Cabang
    
    $pfilterselpil = "('A','B','C','E','F','G','K','X','U','V','I','J')";//,'D'
    
    //echo "$tgl01 - $tgl02";
    
    $query ="SELECT noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idkodeinput as idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
        . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
        . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
        . " divisi_edit as divisi_coa, coa_edit as coa, coa_nama_edit as nama_coa, coa_edit2 as coa2, "
        . " coa_nama_edit2 as nama_coa2, coa_edit3 as coa3, coa_nama_edit3 as nama_coa3, "
        . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
        . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
        . " tgltarikan, nkodeid, nkodeid_nama, nsubkode, nsubkode_nama "
        . " FROM dbmaster.t_proses_data_bm WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$tgl01' AND '$tgl02' AND "
        . " kodeinput IN $pfilterselpil ";
    $query .=" AND IFNULL(ishare,'')<>'Y' "; //pilih salah satu divisi <> 'HO' atau IFNULL(ishare,'')<>'Y'
    $query .=" AND IFNULL(divisi,'') IN ('OTC', 'CHC', 'OT') ";

    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    
    //BANK
    $query ="INSERT INTO $tmp01"
            . "(noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
        . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
        . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
        . " divisi_coa, coa, nama_coa, coa2, "
        . " nama_coa2, coa3, nama_coa3, "
        . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
        . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
        . " tgltarikan, nkodeid, nkodeid_nama)"
            . "SELECT noidauto, tahun, periode, hapus_nodiv_kosong, kodeinput, idkodeinput as idinput, nobrid_r, nobrid_n, idkodeinput, divisi, tglinput, tgltrans, "
        . " karyawanid, nama_karyawan, dokterid, dokter_nama, noslip, nmrealisasi, keterangan, dpp, ppn, pph, tglfp, "
        . " idinput_pd, nodivisi, debit, kredit, saldo, jumlah1, jumlah2, "
        . " divisi_edit as divisi_coa, coa_edit as coa, coa_nama_edit as nama_coa, coa_edit2 as coa2, "
        . " coa_nama_edit2 as nama_coa2, coa_edit3 as coa3, coa_nama_edit3 as nama_coa3, "
        . " tgl_trans_bank, nobukti, idinput_pd1, idinput_pd2, nodivisi1, nodivisi2, pengajuan, "
        . " divisi2, icabangid, nama_cabang, areaid, nama_area, kodeid_pd, subkode_pd, pcm, kasbonsby, coa_pcm, nama_coa_pcm, "
        . " tgltarikan, nkodeid, nkodeid_nama "
        . " FROM dbmaster.t_proses_data_bm WHERE IFNULL(hapus_nodiv_kosong,'') <>'Y' AND DATE_FORMAT(tgltarikan,'%Y-%m') BETWEEN '$tgl01' AND '$tgl02' AND "
        . " kodeinput IN ('M') "
        . " AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN (select CONCAT(IFNULL(kodeid,''),IFNULL(subkode,'')) from dbmaster.t_kode_spd where IFNULL(igroup,'')='3') "
        . " and IFNULL(nkodeid_nama,'')='K'";
    $query .=" AND IFNULL(ishare,'')<>'Y' "; //pilih salah satu divisi <> 'HO' atau IFNULL(ishare,'')<>'Y'
    $query .=" AND IFNULL(divisi,'') IN ('OTC', 'CHC', 'OT') ";
    
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select *, kredit as jumlah from $tmp01";
    $query = "create  table dbtemp.tmptaba_ ($query)";
    //mysqli_query($cnmy, $query);
    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "CREATE INDEX `norm1` ON $tmp01 (kodeinput,idinput,divisi,tgltrans,coa)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    $query = "UPDATE $tmp01 SET coa=coa_pcm WHERE IFNULL(coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.coa_level4 b on a.coa=b.COA4 SET a.nama_coa=b.NAMA4, a.coa3=b.COA3 WHERE IFNULL(a.coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.coa_level3 b on a.coa3=b.COA3 SET a.nama_coa3=b.NAMA3, a.coa2=b.COA2 WHERE IFNULL(a.coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbmaster.coa_level2 b on a.coa2=b.COA2 SET a.nama_coa2=b.NAMA2 WHERE IFNULL(a.coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp01 SET divisi='OTHER' WHERE IFNULL(divisi,'') IN ('', 'AA', 'OTHERS')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //hapus yang PCM
    $query = "DELETE FROM $tmp01 WHERE IFNULL(coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    $query = "SELECT
        a.tahun,
        a.g_divisi,
        a.kodeid,
        a.jumlah
        FROM
        dbmaster.t_budget_otc AS a
        WHERE tahun = '$ptahun' AND g_divisi='OTC'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        
    
    $filtgajispg=""; $filtinsentif=""; $filtsponsor=""; $filtsewadisp=""; $filtentertain=""; $filtpromat=""; $filtiklan=""; $filtevent="";
    $filtrafaksi=""; $filtklaimdisc=""; $filtpromotcost=""; $filtlistingfe=""; $filtfrontline=""; $filthoteltiket=""; $filtinventaris="";
    $filtrpetykes="";
    $query = "select distinct kodeid, kode_akun, kode_akun_sub from dbmaster.t_budget_kode_otc_d order by kodeid";
    $tampil=mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pkodeidp=$row['kodeid'];
        $pakunkode=$row['kode_akun'];
        $pakunkodesub=$row['kode_akun_sub'];
        
        $pkodeandsub=$pakunkode."".$pakunkodesub;
        
        if ($pkodeidp=="03") {
            $filtgajispg .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="04") {
            $filtinsentif .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="05") {
            $filtsponsor .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="06") {
            $filtsewadisp .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="07") {
            $filtentertain .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="08") {
            $filtpromat .="'".$pakunkode."',";
        }elseif ($pkodeidp=="09") {
            $filtiklan .="'".$pakunkode."',";
        }elseif ($pkodeidp=="10") {
            $filtevent .="'".$pakunkode."',";
        }elseif ($pkodeidp=="11") {
            $filtrafaksi .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="17") {
            $filtklaimdisc .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="18") {
            $filtpromotcost .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="13") {
            $filtlistingfe .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="14") {
            $filtfrontline .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="15") {
            $filthoteltiket .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="16") {
            $filtinventaris .="'".$pkodeandsub."',";
        }elseif ($pkodeidp=="19") {
            $filtrpetykes .="'".$pkodeandsub."',";
        }
        
    }
    
    if (!empty($filtgajispg)) $filtgajispg="(".substr($filtgajispg, 0, -1).")";
    else $filtgajispg="('')";
    
    if (!empty($filtinsentif)) $filtinsentif="(".substr($filtinsentif, 0, -1).")";
    else $filtinsentif="('')";
    
    if (!empty($filtsponsor)) $filtsponsor="(".substr($filtsponsor, 0, -1).")";
    else $filtsponsor="('')";
    
    if (!empty($filtsewadisp)) $filtsewadisp="(".substr($filtsewadisp, 0, -1).")";
    else $filtsewadisp="('')";
    
    if (!empty($filtentertain)) $filtentertain="(".substr($filtentertain, 0, -1).")";
    else $filtentertain="('')";
    
    if (!empty($filtpromat)) $filtpromat="(".substr($filtpromat, 0, -1).")";
    else $filtpromat="('')";
    
    if (!empty($filtiklan)) $filtiklan="(".substr($filtiklan, 0, -1).")";
    else $filtiklan="('')";
    
    if (!empty($filtevent)) $filtevent="(".substr($filtevent, 0, -1).")";
    else $filtevent="('')";
    
    if (!empty($filtrafaksi)) $filtrafaksi="(".substr($filtrafaksi, 0, -1).")";
    else $filtrafaksi="('')";
    
    if (!empty($filtklaimdisc)) $filtklaimdisc="(".substr($filtklaimdisc, 0, -1).")";
    else $filtklaimdisc="('')";
    
    if (!empty($filtpromotcost)) $filtpromotcost="(".substr($filtpromotcost, 0, -1).")";
    else $filtpromotcost="('')";
    
    if (!empty($filtlistingfe)) $filtlistingfe="(".substr($filtlistingfe, 0, -1).")";
    else $filtlistingfe="('')";
    
    if (!empty($filtfrontline)) $filtfrontline="(".substr($filtfrontline, 0, -1).")";
    else $filtfrontline="('')";
    
    if (!empty($filthoteltiket)) $filthoteltiket="(".substr($filthoteltiket, 0, -1).")";
    else $filtfrontline="('')";
    
    if (!empty($filtinventaris)) $filtinventaris="(".substr($filtinventaris, 0, -1).")";
    else $filtinventaris="('')";
    
    if (!empty($filtrpetykes)) $filtrpetykes="(".substr($filtrpetykes, 0, -1).")";
    else $filtrpetykes="('')";
   
    
    //echo "$filtgajispg<br/>$filtinsentif</br>$filtsponsor<br/>$filtsewadisp<br/>$filtentertain<br/>$filtpromat<br/>$filtiklan<br/>$filtevent<br/>$filtrafaksi<br/>$filtklaimdisc</br/>$filtpromotcost<br/>$filtlistingfe<br/>$filtfrontline<br/>$filthoteltiket<br/>$filtinventaris<br/>";
    
    
    //GAJI SPG 03
    $query = "SELECT kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, CAST('03' as CHAR(5)) as kodeid, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtgajispg";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtgajispg");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //insentif 04
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '04' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtinsentif";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtinsentif");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //sponsor 05
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '05' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtsponsor";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtsponsor");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //sewa display 06
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '06' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtsewadisp";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtsewadisp");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //entertain 07
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '07' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtentertain";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtentertain");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //promat 08
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '08' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " IFNULL(nkodeid,'') IN $filtpromat";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND IFNULL(nkodeid,'') IN $filtpromat");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //iklan 09
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '09' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " IFNULL(nkodeid,'') IN $filtiklan";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND IFNULL(nkodeid,'') IN $filtiklan");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //event 10
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '10' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " IFNULL(nkodeid,'') IN $filtevent";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND IFNULL(nkodeid,'') IN $filtevent");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    //rafaksi 11
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '11' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtrafaksi";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtrafaksi");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //klaim discount 17
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '17' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtklaimdisc";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtklaimdisc");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        //klaim discount 17 KHUSUS
        $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                . "SELECT '17' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
                . " IFNULL(nkodeid,'')='12' AND IFNULL(nsubkode,'')='' ";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND IFNULL(nkodeid,'')='12' AND IFNULL(nsubkode,'')=''");
        if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //PROMOTION COST 18 
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '18' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtpromotcost";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtpromotcost");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //Listing fee 13
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '13' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtlistingfe";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtlistingfe");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //front liner 14
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '14' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtfrontline";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtfrontline");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //tiket dan hotel 15
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '15' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filthoteltiket";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filthoteltiket");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //inventaris 16
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '16' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtinventaris";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtinventaris");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //biaya rutin 01
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '01' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('F','U','V')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //luar kota 02
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '02' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('G')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //DARI BM SBY INSENTIF masuk INSENTIF 04
        $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                . "SELECT '04' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('I','J') AND "
                . " coa IN ('704-05')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        
    //DARI BM SBY GAJI masuk .... HO dulu 20
        $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                . "SELECT '20' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('I','J') AND "
                . " coa NOT IN ('700-05', '701-05', '702-05', '703-05', '704-05')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    //DARI BANK masuk .... HO dulu 20
        $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                . "SELECT '20' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('M')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    //INVENTARIS
        $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                . "SELECT '16' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') "
                . " AND nkodeid in ('07') AND IFNULL(nsubkode,'')=''";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND nkodeid in ('07') AND IFNULL(nsubkode,'')=''");
        
        
    //pety cash 19 kas kecil
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '19' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E') AND "
            . " CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtrpetykes";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E') AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN $filtrpetykes");
    if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //BR OTC yang belum masuk .... HO dulu 20
        $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
                . "SELECT '20' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('E')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE kodeinput IN ('E')");
        if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        
    // 12 BONUS DPL/DPF DARI KLAIM DISCOUNT PRITA AHMAD AHMED--- PENGAJUAN = OTC
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '12' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('B')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    
    // KAS KECIL CABANG MASUK KE .....? 19 KAS KECIL
    $query = "INSERT INTO $tmp03 (kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit)"
            . "SELECT '19' as kodeid, kodeinput, divisi, idkodeinput, nkodeid, coa, nama_coa, kredit FROM $tmp01 WHERE kodeinput IN ('X')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT kodeid, SUM(kredit) as kredit FROM $tmp03 GROUP BY 1) as b on IFNULL(a.kodeid,'')=IFNULL(b.kodeid,'') SET a.jumlah1=b.kredit"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }    
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT kodeid, SUM(jumlah) as jumlah FROM $tmp02 GROUP BY 1) as b on IFNULL(a.kodeid,'')=IFNULL(b.kodeid,'') SET a.jumlah2=b.jumlah"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");

    $query = "SELECT * FROM $tmp00";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=1) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=7";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=2) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=14";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=3) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=19";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=5) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=28";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE IFNULL(grp,0)=6) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=36";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");

    $query = "SELECT * FROM $tmp00";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE grp in (2,3,4,5,6)) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=37";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");

    $query = "SELECT * FROM $tmp00";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 as a JOIN (SELECT SUM(jumlah1) as jumlah1, SUM(jumlah2) as jumlah2 FROM $tmp04 WHERE nourut in (7,37)) as b SET a.jumlah1=b.jumlah1, a.jumlah2=b.jumlah2 WHERE nourut=39";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    
    
    
    //SALES
    $query = "select 'OTC' as divprodid, sum(`value`) as value_sales, 0 as value_target from fe_it.otc_etl WHERE "
            . " YEAR(tgljual)='$ptahun' AND DATE_FORMAT(tgljual,'%Y-%m') <= '$pbulan' AND divprodid <>'OTHER' and icabangid <> 22 GROUP BY 1";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    mysqli_query($cnmy, "UPDATE $tmp00 a SET a.jumlah1=(select sum(b.value_sales) FROM $tmp04 b) WHERE nourut=41");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    mysqli_query($cnmy, "UPDATE $tmp00 a SET a.jumlah2=(select sum(b.value_target) FROM $tmp04 b) WHERE nourut=41");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "UPDATE $tmp00 SET jumlah3=IFNULL(jumlah2,0)-IFNULL(jumlah1,0)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $jsales1=0;
    $jsales2=0;
    $jsales3=0;
    $query = "select * FROM $tmp00 WHERE nourut=41";
    $tampil=mysqli_query($cnmy, $query);
    while ($ro= mysqli_fetch_array($tampil)) {
        $jsales1=$ro['jumlah1'];
        $jsales2=$ro['jumlah2'];
        $jsales3=$ro['jumlah3'];
        if (empty($jsales1)) $jsales1=0;
        if (empty($jsales2)) $jsales2=0;
        if (empty($jsales3)) $jsales3=0;
    }
    if ((DOUBLE)$jsales1>0){
        mysqli_query($cnmy, "UPDATE $tmp00 a SET a.ratio1=ifnull(a.jumlah1,0)/$jsales1*100");
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    if ((DOUBLE)$jsales2>0){
        mysqli_query($cnmy, "UPDATE $tmp00 a SET a.ratio2=ifnull(a.jumlah2,0)/$jsales2*100");
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    if ((DOUBLE)$jsales2>0){
        mysqli_query($cnmy, "UPDATE $tmp00 a SET a.ratio3=ifnull(a.jumlah3,0)/$jsales2*100");
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
        
        
    
    $query = "SELECT * FROM $tmp00";
    //$query = "create table $tmp05 ($query)"; 
    //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>


<HTML>
<HEAD>
    <title>REALISASI BIAYA MARKETING VS BUDGET CHC</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
		
		<!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    
        <style>
            .tjudul {
                font-family: "times new roman", Arial, Georgia, serif;
                margin-left:10px;
                margin-right:10px;
            }
            .tjudul td {
                padding: 4px;
                font-size: 15px;
            }
            #datatable2 {
                font-family: "times new roman", Arial, Georgia, serif;
                margin-left:10px;
                margin-right:10px;
                border-collapse: collapse;
            }
            #datatable2 th, #datatable2 td {
                padding: 10px;
            }
            #datatable2 thead{
                background-color:#cccccc; 
                font-size: 18px;
            }
            #datatable2 tbody{
                font-size: 16px;
            }
        </style>
        
    <?PHP } ?>
    
</HEAD>
<BODY class="nav-md">
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>
    
    <?PHP
        $tglbulanbesar=strtoupper($pblnthn);
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='400px' colspan='2'>REALISASI BIAYA MARKETING VS BUDGET CHC s/d. $tglbulanbesar</td> </tr>";
        echo "<tr> <td width='200px' colspan='2'>PT. SURYA DERMATO MEDICA LABORATORIES </td></tr>";
        echo "<tr> <td width='200px' colspan='2'>DIVISI CHC</td></tr>";
        echo "</table>";
        echo "<br/>&nbsp;";
        
    ?>
   
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr>
            <th align="center" rowspan="2">NO</th>
            <th align="center" rowspan="2">KETERANGAN</th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            <th align="center" colspan="2"><?PHP echo "$ptahun"; ?></th>
            </tr>
            
            <tr>
            <th align="center">REALISASI BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">USULAN BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
            <th align="center">SISA BUDGET</th>
            <th align="center">COST <br/>RATIO</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $query = "select * FROM $tmp00 order by nourut";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pnourut=$row['nourut'];
                $pno=$row['no'];
                $pjudul=$row['keterangan'];
                
                $pjumlah1=$row['jumlah1'];
                $pjumlah2=$row['jumlah2'];
                $pjumlah3=$row['jumlah3'];

                $pratio1=ROUND($row['ratio1'],2);
                $pratio2=ROUND($row['ratio2'],2);
                $pratio3=ROUND($row['ratio3'],2);

                


                $pjumlah1=number_format($pjumlah1,0,",",",");
                $pjumlah2=number_format($pjumlah2,0,",",",");
                $pjumlah3=number_format($pjumlah3,0,",",",");
                
                if ($pjumlah1==0) $pjumlah1="";
                if ($pjumlah2==0) $pjumlah2="";
                if ($pjumlah3==0) $pjumlah3="";
                
                if ($pratio1==0) $pratio1="";
                if ($pratio2==0) $pratio2="";
                if ($pratio3==0) $pratio3="";
                
                echo "<tr>";
                echo "<td nowrap>$pno</td>";
                
                if ((int)$pnourut==7 OR (int)$pnourut==14 OR (int)$pnourut==19 OR (int)$pnourut==28 OR (int)$pnourut==36 OR (int)$pnourut==37 OR (int)$pnourut==39 OR (int)$pnourut==43) {
                    echo "<td nowrap><b>$pjudul</b></td>";
                    echo "<td nowrap align='right'><b>$pjumlah1</b></td>";
                    echo "<td nowrap align='right'><b>$pratio1</b></td>";
                    echo "<td nowrap align='right'><b>$pjumlah2</b></td>";
                    echo "<td nowrap align='right'><b>$pratio2</b></td>";
                    
                    echo "<td nowrap align='right'><b>$pjumlah3</b></td>";
                    echo "<td nowrap align='right'><b>$pratio3</b></td>";
                }else{
                    echo "<td nowrap>$pjudul</td>";
                    echo "<td nowrap align='right'>$pjumlah1</td>";
                    echo "<td nowrap align='right'>$pratio1</td>";
                    echo "<td nowrap align='right'>$pjumlah2</td>";
                    echo "<td nowrap align='right'>$pratio2</td>";
                    
                    echo "<td nowrap align='right'>$pjumlah3</td>";
                    echo "<td nowrap align='right'>$pratio3</td>";
                }
                echo "</tr>";
                
                
            }
            
            ?>
        </tbody>
    </table>
    
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;    
</div>
    
    
    <?PHP if ($ppilihrpt!="excel") { ?>

        
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

        </style>
    
    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 140%;
              font-weight: bold;
            }
        </style>
    <?PHP } ?>
        
        
</BODY>


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
    </script>
    
    
    
</HTML>




<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_close($cnmy);
?>