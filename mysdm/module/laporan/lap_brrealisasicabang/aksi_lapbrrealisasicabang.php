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
        header("Content-Disposition: attachment; filename=Laporan_Realisasi_Budget_Request_Ethical_Per_Cabang.xls");
    }
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    
    $printdate= date("d/m/Y");
?>


<?PHP
    $tgl01 = $_POST['e_periode01'];
    $tgl02 = $_POST['e_periode02'];
    $pdivisipil = $_POST['cb_divisi'];
    
    $pperiode1 = date("Y-m-d", strtotime($tgl01));
    $pperiode2 = date("Y-m-d", strtotime($tgl02));
    
    $myperiode1 = date("d/m/Y", strtotime($tgl01));
    $myperiode2 = date("d/m/Y", strtotime($tgl02));

    $filtercab=('');
    if (!empty($_POST['chkbox_cabang'])){
        $filtercab=$_POST['chkbox_cabang'];
        $filtercab=PilCekBox($filtercab);
    }
    $pilcabkososng = (int)strpos($filtercab, "tanpa_cabang");
    if ( (int)$pilcabkososng>0 )
        $filtercab=" and (icabangid in $filtercab OR ifnull(icabangid,'')='')";
    else
        $filtercab=" and icabangid in $filtercab ";
    
    $filterkode=" ('700-01-04', '700-02-04', '700-04-04', '700-01-03', '700-02-03', '700-04-03') ";
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];

    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpbrrealcabang00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpbrrealcabang01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpbrrealcabang02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpbrrealcabang03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpbrrealcabang04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmpbrrealcabang05_".$puserid."_$now ";
    $tmp10 =" dbtemp.tmpbrrealcabang10_".$puserid."_$now ";
    $tmp11 =" dbtemp.tmpbrrealcabang11_".$puserid."_$now ";
    $tmp12 =" dbtemp.tmpbrrealcabang12_".$puserid."_$now ";
    
    
    $query = "select brId, noslip, icabangid, idcabang, tgl, tgltrans, divprodid, COA4, kode, realisasi1, "
            . " jumlah, jumlah1, jumlah jumlah_asli, jumlah1 as jumlah1_asli, "
            . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
            . " dpp, ppn_rp, pph_rp, tgl_fp, CAST('' as CHAR(20)) as nobukti "
            . " from hrd.br0 WHERE IFNULL(batal,'')<>'Y' AND "
            . " brId NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
            . " tgltrans BETWEEN '$pperiode1' AND '$pperiode2' ";
    $query .=" AND IFNULL(kode,'') IN $filterkode $filtercab ";
if (!empty($pdivisipil)) $query .=" AND divprodid='$pdivisipil' ";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
        $query = "CREATE INDEX `norm1` ON $tmp01 (brId,dokterId)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
        
            //via SBY
            $query = "select a.bridinput brId, b.noslip, b.icabangid, b.idcabang, b.tgl, a.tgltransfersby tgltrans, b.divprodid, "
                    . " b.COA4, b.kode, b.realisasi1, a.jumlah jumlah, a.jumlah jumlah1, a.jumlah jumlah_asli, a.jumlah as jumlah1_asli, "
                    . " b.aktivitas1, b.aktivitas2, b.dokterId, b.dokter, b.karyawanId, b.ccyId, b.tgltrm, b.lampiran, b.ca, "
                    . " b.dpp, b.ppn_rp, b.pph_rp, b.tgl_fp, "
                    . " a.nobukti "
                    . " from dbmaster.t_br0_via_sby a JOIN hrd.br0 b on a.bridinput=b.brId "
                    . " WHERE IFNULL(b.batal,'')<>'Y' AND "
                    . " a.bridinput NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND "
                    . " a.tgltransfersby BETWEEN '$pperiode1' AND '$pperiode2' ";
            $query .=" AND IFNULL(kode,'') IN $filterkode ";
            if (!empty($pdivisipil)) $query .=" AND b.divprodid='$pdivisipil' ";
            $query = "create TEMPORARY table $tmp02 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "CREATE INDEX `norm1` ON $tmp02 (brId,dokterId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
            
            $query = "DELETE FROM $tmp01 WHERE brId IN (select distinct IFNULL(brId,'') FROM $tmp02)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "INSERT INTO $tmp01 (brId, noslip, icabangid, idcabang, tgl, tgltrans, divprodid, "
                    . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
                    . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                    . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti) "
                    . " select brId, noslip, icabangid, idcabang, tgl, tgltrans, divprodid, "
                    . " COA4, kode, realisasi1, jumlah, jumlah1, jumlah_asli, jumlah1_asli, "
                    . " aktivitas1, aktivitas2, dokterId, dokter, karyawanId, ccyId, tgltrm, lampiran, ca, "
                    . " dpp, ppn_rp, pph_rp, tgl_fp, nobukti "
                    . " from $tmp02 ";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //END via SBY
            
        $query = "UPDATE $tmp01 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
        

    
        $query = "select a.*, e.nama nama_karyawan, b.nama nama_cabang, c.nama nama_kode, f.NAMA4, g.region, g.nama nama_daerah, "
                . " CAST('' as CHAR(50)) as nodivisi, CAST('' as CHAR(50)) as nodivisi1, CAST('' as CHAR(50)) as nodivisi2 "
                . " from $tmp01 a LEFT JOIN mkt.icabang b on a.icabangid=b.icabangid "
                . " LEFT JOIN hrd.br_kode c on a.kode=c.kodeid "
                . " LEFT JOIN hrd.karyawan e on a.karyawanId=e.karyawanId "
                . " LEFT JOIN dbmaster.coa_level4 f on a.COA4=f.COA4 "
                . " LEFT JOIN MKT.cbgytd g on a.idcabang=g.idcabang";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "ALTER table $tmp03 ADD COLUMN idinput BIGINT(20), ADD COLUMN idinput1 BIGINT(20), ADD COLUMN idinput2 BIGINT(20), ADD COLUMN kodeid_pd INT(4), ADD COLUMN subkode_pd VARCHAR(5), ADD COLUMN pcm VARCHAR(1), ADD COLUMN kasbonsby VARCHAR(1), ADD COLUMN coa_pcm VARCHAR(50), ADD COLUMN nama_coa_pcm VARCHAR(100)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query = "CREATE INDEX `norm1` ON $tmp03 (brId)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; } 
            
            
        $query ="select distinct idinput, bridinput, kodeinput from dbmaster.t_suratdana_br1 WHERE kodeinput IN ('A', 'B', 'C')";
        $query = "create TEMPORARY table $tmp11 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp11 (bridinput,kodeinput)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "select idinput, divisi, nodivisi, pilih, kodeid, subkode "
                . " from dbmaster.t_suratdana_br WHERE idinput in (select distinct IFNULL(idinput,'') from $tmp11) "
                . " AND IFNULL(stsnonaktif,'')<>'Y'";
        $query = "create TEMPORARY table $tmp12 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "CREATE INDEX `norm1` ON $tmp12 (idinput,divisi,nodivisi, pilih)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from $tmp11 a "
                . " JOIN $tmp12 b on a.idinput=b.idinput";
        $query = "create TEMPORARY table $tmp10 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "CREATE INDEX `norm1` ON $tmp10 (idinput,divisi,nodivisi,kodeinput,bridinput, pilih)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi2=b.nodivisi, a.idinput2=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='Y'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput, a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $query = "UPDATE $tmp03 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp10 WHERE kodeinput IN ('A', 'B', 'C') AND divisi<>'OTC') b on a.brId=b.bridinput "
                    . " SET a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.kodeid_pd=b.kodeid, a.subkode_pd=b.subkode WHERE b.pilih='N'"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            
            $query = "UPDATE $tmp03 SET pcm='Y' WHERE IFNULL(nodivisi1,'')<>'' AND IFNULL(nodivisi2,'')=''"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE $tmp03 SET kasbonsby='Y' WHERE CONCAT(kodeid_pd,subkode_pd) IN ('680')"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            $query = "UPDATE $tmp03 SET pcm='' WHERE "
                    . " IFNULL(nodivisi2,'')='' AND ( (IFNULL(tgltrm,'0000-00-00')<>'0000-00-00' AND IFNULL(tgltrm,'')<>'') OR ( IFNULL(lampiran,'')='Y' AND IFNULL(ca,'')='N' AND IFNULL(tgltrm,'0000-00-00')='0000-00-00') )"
                    . " AND CONCAT(kodeid_pd,subkode_pd) NOT IN ('680')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
            //UPDATE PCM JADI U.M BIAYA UANG MUKA
            $query = "UPDATE $tmp03 SET coa_pcm='105-02', nama_coa_pcm='U.M. BIAYA' WHERE IFNULL(pcm,'')='Y'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        $query ="DELETE FROM $tmp03 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        //$query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(nodivisi,'')='' AND ( IFNULL(tgltrans,'')='' OR IFNULL(tgltrans,'0000-00-00')='0000-00-00' ) AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        //$query ="UPDATE $tmp03 SET hapus_nodiv_kosong='Y' WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        
    mysqli_query($cnmy, "update $tmp03 set region='N' WHERE IFNULL(region,'')=''");
    mysqli_query($cnmy, "update $tmp03 set icabangid='N', nama_cabang='NONE' WHERE IFNULL(icabangid,'')=''");
    mysqli_query($cnmy, "update $tmp03 set idcabang='N', nama_daerah='NONE' WHERE IFNULL(idcabang,'')=''");
    
    
    $query = "select distinct region, divprodid, icabangid, nama_cabang, cast(NULL as DECIMAL(20,2)) as DCC, "
            . " cast(NULL as DECIMAL(20,2)) as DSS, cast(NULL as DECIMAL(20,2)) as VTOTAL from $tmp03 ";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "UPDATE $tmp04 a SET a.DCC=(select sum(b.jumlah) jumlah from $tmp03 b WHERE a.region=b.region AND a.divprodid=b.divprodid AND "
            . " a.icabangid=b.icabangid AND b.nama_kode like '%DCC%' )"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp04 a SET a.DSS=(select sum(b.jumlah) jumlah from $tmp03 b WHERE a.region=b.region AND a.divprodid=b.divprodid AND "
            . " a.icabangid=b.icabangid AND b.nama_kode like '%DSS%' )"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp04 SET VTOTAL=IFNULL(DCC,0)+IFNULL(DSS,0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    
    
?>
<HTML>
<HEAD>
    <title>Laporan Realisasi Budget Request Ethical Per Cabang</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Apr 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <?PHP } ?>
        
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>
<BODY class="nav-md">
    
    <div class='modal fade' id='myModal' role='dialog'></div>
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>

    <center><div class='h1judul'>Laporan Realisasi Budget Request Ethical Per Cabang</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <?PHP
            echo "<tr> <td>Periode </td> <td>:</td> <td><b>$myperiode1 s/d. $myperiode2</b></td> </tr>";
            echo "<tr class='miring text2'> <td>view date</td> <td>:</td> <td>$printdate</td> </tr>";
            ?>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr>
                <th width='7px'>No</th>
                <th nowrap>Nama Daerah</th>
                <th nowrap>DCC</th>
                <th nowrap>DSS</th>
                <th nowrap>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $pgrdtotdcc=0;
                $pgrdtotdss=0;
                $pgrdtotrp=0;
                
                $psubtotdivdcc=0;
                $psubtotdivdss=0;
                $psubtotdivrp=0;
                
                $psubtotdcc=0;
                $psubtotdss=0;
                $psubtotrp=0;
                
                $no=1;
                
                $group1 = mysqli_query($cnmy, "select distinct divprodid from $tmp04 order by divprodid");
                while ($row1= mysqli_fetch_array($group1)) {
                    $pdivisi=$row1['divprodid'];
                    $mdivisinm=$pdivisi;
                    if ($pdivisi=="PIGEO") $mdivisinm="PIGEON";
                    if ($pdivisi=="PEACO") $mdivisinm="PEACOK";
                    
                    echo "<tr style='font-weight: bold;'>";
                    echo "<td></td>";
                    echo "<td nowrap>$mdivisinm</td>";
                    echo "<td nowrap align='right'></td>";
                    echo "<td nowrap align='right'></td>";
                    echo "<td nowrap align='right'></td>";
                    echo "</tr>";
                    
                    $psubtotdivdcc=0;
                    $psubtotdivdss=0;
                    $psubtotdivrp=0;
                
                    $group2 = mysqli_query($cnmy, "select distinct divprodid, region from $tmp04 WHERE divprodid='$pdivisi' order by divprodid, region");
                    while ($row2= mysqli_fetch_array($group2)) {
                        
                        $region="Barat";
                        $pnreg=$row2['region'];
                        if ($pnreg=="T") $region="Timur";
                        if ($pnreg=="N") $region="Other";
                        
                        echo "<tr style='font-weight: bold;'>";
                        echo "<td></td>";
                        echo "<td nowrap>$region</td>";
                        echo "<td nowrap align='right'></td>";
                        echo "<td nowrap align='right'></td>";
                        echo "<td nowrap align='right'></td>";
                        echo "</tr>";
                        
                        $no=1;
                        $psubtotdcc=0;
                        $psubtotdss=0;
                        $psubtotrp=0;
                        
                        $query = "select * from $tmp04 WHERE divprodid='$pdivisi' AND region='$pnreg' order by divprodid, region, nama_cabang";
                        $tampil= mysqli_query($cnmy, $query);
                        while ($row3= mysqli_fetch_array($tampil)) {
                            
                            $pidcabang=$row3['icabangid'];
                            $pnmdaerah=$row3['nama_cabang'];
                            if ($pidcabang=="N") $pnmdaerah=".........";
                            $pjmldcc=$row3['DCC'];
                            $pjmldss=$row3['DSS'];
                            $pjmltot=$row3['VTOTAL'];

                            if (empty($pjmldcc)) $pjmldcc=0;
                            if (empty($pjmldss)) $pjmldss=0;
                            if (empty($pjmltot)) $pjmltot=0;
                            
                            $psubtotdcc=(double)$psubtotdcc+(double)$pjmldcc;
                            $psubtotdss=(double)$psubtotdss+(double)$pjmldss;
                            $psubtotrp=(double)$psubtotrp+(double)$pjmltot;
                            
                            $pgrdtotdcc=(double)$pgrdtotdcc+(double)$pjmldcc;
                            $pgrdtotdss=(double)$pgrdtotdss+(double)$pjmldss;
                            $pgrdtotrp=(double)$pgrdtotrp+(double)$pjmltot;
                            

                            $psubtotdivdcc=(double)$psubtotdivdcc+(double)$pjmldcc;
                            $psubtotdivdss=(double)$psubtotdivdss+(double)$pjmldss;
                            $psubtotdivrp=(double)$psubtotdivrp+(double)$pjmltot;

                            $pjmldcc=number_format($pjmldcc,0,",",",");
                            $pjmldss=number_format($pjmldss,0,",",",");
                            $pjmltot=number_format($pjmltot,0,",",",");
                            
                            

                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td nowrap>$pnmdaerah</td>";
                            echo "<td nowrap align='right'>$pjmldcc</td>";
                            echo "<td nowrap align='right'>$pjmldss</td>";
                            echo "<td nowrap align='right'>$pjmltot</td>";
                            echo "</tr>";
                            
                            $no++;
                            
                        }
                        
                        $psubtotdcc=number_format($psubtotdcc,0,",",",");
                        $psubtotdss=number_format($psubtotdss,0,",",",");
                        $psubtotrp=number_format($psubtotrp,0,",",",");
                        
                        echo "<tr style='font-weight: bold;'>";
                        echo "<td></td>";
                        echo "<td nowrap>Total $mdivisinm $region</td>";
                        echo "<td nowrap align='right'>$psubtotdcc</td>";
                        echo "<td nowrap align='right'>$psubtotdss</td>";
                        echo "<td nowrap align='right'>$psubtotrp</td>";
                        echo "</tr>";
                        
                        
                    }
                    
                    
                    $psubtotdivdcc=number_format($psubtotdivdcc,0,",",",");
                    $psubtotdivdss=number_format($psubtotdivdss,0,",",",");
                    $psubtotdivrp=number_format($psubtotdivrp,0,",",",");
                            
                    echo "<tr style='font-weight: bold;'>";
                    echo "<td></td>";
                    echo "<td nowrap>Total $mdivisinm</td>";
                    echo "<td nowrap align='right'>$psubtotdivdcc</td>";
                    echo "<td nowrap align='right'>$psubtotdivdss</td>";
                    echo "<td nowrap align='right'>$psubtotdivrp</td>";
                    echo "</tr>";
                    
                    echo "<tr style='font-weight: bold;'>";
                    echo "<td></td>";
                    echo "<td nowrap>&nbsp</td>";
                    echo "<td nowrap align='right'>&nbsp</td>";
                    echo "<td nowrap align='right'>&nbsp</td>";
                    echo "<td nowrap align='right'>&nbsp</td>";
                    echo "</tr>";
                            
                    
                }

                $pgrdtotdcc=number_format($pgrdtotdcc,0,",",",");
                $pgrdtotdss=number_format($pgrdtotdss,0,",",",");
                $pgrdtotrp=number_format($pgrdtotrp,0,",",",");

                echo "<tr style='font-weight: bold;'>";
                echo "<td></td>";
                echo "<td nowrap>Grand Total </td>";
                echo "<td nowrap align='right'>$pgrdtotdcc</td>";
                echo "<td nowrap align='right'>$pgrdtotdss</td>";
                echo "<td nowrap align='right'>$pgrdtotrp</td>";
                echo "</tr>";

            ?>
        </tbody>
    </table>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
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

        <style>
            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 5px 20px 20px 20px;
                /*overflow-x:auto;*/
            }
            
            .h1judul {
              color: blue;
              font-family: verdana;
              font-size: 140%;
              font-weight: bold;
            }
            table.tbljudul {
                font-size : 15px;
            }
            table.tbljudul tr td {
                padding: 1px;
                font-family : "Arial, Verdana, sans-serif";
            }
            .tebal {
                 font-weight: bold;
            }
            .miring {
                 font-style: italic;
            }
            table.tbljudul tr.text2 {
                font-size : 13px;
            }
            
            table {
                text-align: left;
                position: relative;
                border-collapse: collapse;
                background-color:#FFFFFF;
            }

            th {
                background: white;
                position: sticky;
                top: 0;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                z-index:1;
            }

            .th2 {
                background: white;
                position: sticky;
                top: 23;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                border-top: 1px solid #000;
            }
        </style>
    
        <style>
            
            .divnone {
                display: none;
            }
            #mydatatable1, #mydatatable2 {
                color:#000;
                font-family: "Arial";
            }
            #mydatatable1 th, #mydatatable2 th {
                font-size: 12px;
            }
            #mydatatable1 td, #mydatatable2 td { 
                font-size: 11px;
            }
        </style>
        
        
        <style>
            table {
                text-align: left;
                position: relative;
                border-collapse: collapse;
                background-color:#FFFFFF;
            }

            th {
                background: white;
                position: sticky;
                top: 0;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            }

            .th2 {
                background: white;
                position: sticky;
                top: 23;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                border-top: 1px solid #000;
            }
        </style>
    
    
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
    
    
    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 140%;
              font-weight: bold;
            }
        </style>
    <?PHP } ?>
</BODY>

</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp10");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp11");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp12");
    mysqli_close($cnmy);
?>