<?PHP
    session_start();
    $date1 = str_replace('/', '-', $_POST['uperiode1']);
    $periode1= date("Y-m", strtotime($date1));
    $edivisi=$_POST['udivisi'];
    $eidinput=$_POST['uidinput'];
    
    $tgl01 = $_POST['utglf'];
    $tgl02 = $_POST['utglt'];
    
    $periode1 = date("Y-m-d", strtotime($tgl01));
    $periode2 = date("Y-m-d", strtotime($tgl02));
    
    $pbln1 = date("Y-m", strtotime($tgl01));
    $pbln2 = date("Y-m", strtotime($tgl02));
    
    $ptglprint1 = date("d F Y", strtotime($tgl01));
    $ptglprint2 = date("d F Y", strtotime($tgl02));
    
?>
    

<script>
</script>

<style>
    .divnone {
        display: none;
    }
    #datatableinputb th {
        font-size: 13px;
    }
    #datatableinputb td { 
        font-size: 11px;
    }
</style>

<?PHP
    include("../../config/koneksimysqli.php");
    $cnit=$cnmy;
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DRSPDD01_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp02 =" dbtemp.DRSPDD02_".$_SESSION['USERID']."_$now$milliseconds ";
    $tmp03 =" dbtemp.DRSPDD03_".$_SESSION['USERID']."_$now$milliseconds ";
    
    $droptable01 = "DROP TABLE $tmp01";
    $droptable02 = "DROP TABLE $tmp02";
    $droptable03 = "DROP TABLE $tmp03";
    
    $query = "CREATE TABLE $tmp01 (kodeinput VARCHAR(2), pengajuan varchar(100), divisi varchar(5), tgl date, icabangid varchar(10), bukti varchar(50), 
        idinput varchar(20), noid varchar(50), 
        noidnm varchar(150), noidsub varchar(50), noidsubnm varchar(150), 
        coa4 varchar(20), karyawanid varchar(10), iddokter varchar(10), nmdokter varchar(200), noslip varchar(50),
        keterangan varchar(500), nmrealisasi varchar(200), icabangid_o varchar(10), debit DECIMAL(30,2), kredit DECIMAL(30,2), saldo DECIMAL(30,2),
        jumlah DECIMAL(30,2), realisasi DECIMAL(30,2), cn DECIMAL(30,2)
        )";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $filtereditid="";
    if (!empty($eidinput)) $filtereditid=" AND idinput<>'$eidinput' ";
        
    if ($edivisi != "OTC") {
        //DCC, DSS & NON
        $filtertgl = " AND DATE_FORMAT(a.tgl,'%Y-%m-%d') between '$periode1' AND '$periode2' ";
        $filteridsudah = " AND a.brId NOT IN (SELECT DISTINCT ifnull(bridinput,'') FROM dbmaster.t_suratdana_br1 WHERE kodeinput='A' $filtereditid)";
        //if (!empty($eidinput)) $filteridsudah="";
        if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND a.divprodid='$edivisi' ";
        $sql = "select 'A' kodeinput, 'NON DCC DSS' pengajuan, a.divprodid, a.tgl, a.icabangid, a.brId, a.kode, a.COA4, a.karyawanId, 
            a.dokterId, b.nama namadokter, a.noslip,
            a.aktivitas1, a.realisasi1 nmrealisasi, a.jumlah, a.jumlah1, a.cn 
            FROM hrd.br0 a LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId  
            WHERE a.brId not in (SELECT DISTINCT ifnull(brId,'') from hrd.br0_reject) $filtertgl $fdivisi $filteridsudah";
        
        $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, coa4, karyawanid, iddokter, nmdokter, noslip,
                keterangan, nmrealisasi, jumlah, realisasi, cn) $sql";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
            mysqli_query($cnit, $droptable01);
            echo $erropesan; exit;
        }
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.coa4=(select b.COA4 from dbmaster.posting_coa_br b WHERE b.kodeid=a.noid AND b.divisi=a.divisi limit 1) WHERE a.kodeinput='A' AND ifnull(a.coa4,'')=''");
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.divisi=(select b.divisi from dbmaster.posting_coa_br b WHERE b.kodeid=a.noid AND b.divisi=a.divisi limit 1) WHERE a.kodeinput='A' AND ifnull(a.divisi,'')=''");
        
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.pengajuan=ifnull((select b.nama from hrd.br_kode b WHERE b.kodeid=a.noid and b.divprodid=a.divisi limit 1),'DCC DSS NON') WHERE a.kodeinput='A'");
    }
    
    if ($edivisi != "OTC") {
        //KLAIM ==> belum pakai divisi
        $filtertgl = " AND DATE_FORMAT(tgl,'%Y-%m-%d') between '$periode1' AND '$periode2' ";
        $fdivisi = "";
        $filteridsudah = " AND klaimId NOT IN (SELECT DISTINCT ifnull(bridinput,'') FROM dbmaster.t_suratdana_br1 WHERE kodeinput='B' $filtereditid)";
        //if (!empty($eidinput)) $filteridsudah="";
        $sql = "select 'B' kodeinput, 'KLAIM' pengajuan, DIVISI, tgl, distid, klaimId, COA4, karyawanid, noslip,
            aktivitas1, realisasi1 nmrealisasi, jumlah 
            FROM hrd.klaim WHERE klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) $filtertgl $fdivisi $filteridsudah ";
        
        $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, coa4, karyawanid, noslip, 
                keterangan, nmrealisasi, jumlah) $sql";
        
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
            mysqli_query($cnit, $droptable01);
            echo $erropesan; exit;
        }
        mysqli_query($cnit, "UPDATE $tmp01 set coa4=(select COA4 from dbmaster.posting_coa_br WHERE kodeid='700-02-07' limit 1) WHERE kodeinput='B' AND ifnull(coa4,'')=''");
        mysqli_query($cnit, "UPDATE $tmp01 set divisi=(select divisi from dbmaster.posting_coa_br WHERE kodeid='700-02-07' limit 1) WHERE kodeinput='B' AND ifnull(divisi,'')=''");
    }
    
    
    if ($edivisi != "OTC") {
        //KAS
        $filtertgl = " AND DATE_FORMAT(a.periode1,'%Y-%m-%d') between '$periode1' AND '$periode2' ";
        $filteridsudah = " AND a.kasId NOT IN (SELECT DISTINCT ifnull(bridinput,'') FROM dbmaster.t_suratdana_br1 WHERE kodeinput='C' $filtereditid)";
        //if (!empty($eidinput)) $filteridsudah="";
        if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND e.DIVISI2='$edivisi' ";
        $sql = "select 	'C' kodeinput, 'KAS' pengajuan, e.DIVISI2, a.periode1, a.kasId, a.kode, b.COA4, a.karyawanid, a.nobukti,
                a.aktivitas1, a.jumlah
                FROM hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid
                LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4 LEFT JOIN dbmaster.coa_level3 d on c.COA3=d.COA3
                LEFT JOIN dbmaster.coa_level2 e on e.COA2=d.COA2 
                WHERE 1=1 $filtertgl $fdivisi $filteridsudah ";
        
        $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, idinput, noid, coa4, karyawanid, bukti,
                keterangan, jumlah) $sql";
        
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
            mysqli_query($cnit, $droptable01);
            echo $erropesan; exit;
        }
        mysqli_query($cnit, "UPDATE $tmp01 set coa4=null WHERE kodeinput='C' AND coa4=''");
    }
    
    if (empty($edivisi) OR ($edivisi == "OTC")) {
        //BR OTC
        $filtertgl = " AND DATE_FORMAT(tglbr,'%Y-%m-%d') between '$periode1' AND '$periode2' ";
        $fdivisi = "";
        $filteridsudah = " AND brOtcId NOT IN (SELECT DISTINCT ifnull(bridinput,'') FROM dbmaster.t_suratdana_br1 WHERE kodeinput='D' $filtereditid)";
        //if (!empty($eidinput)) $filteridsudah="";
        $sql = "select 'D' kodeinput, 'BROTC' pengajuan, 'OTC' divisi, tglbr, icabangid_o, brOtcId, subpost, kodeid, COA4, noslip,
            keterangan1, jumlah, realisasi 
            FROM hrd.br_otc WHERE brOtcId not in (SELECT DISTINCT ifnull(brOtcId,'') from hrd.br_otc_reject) $filtertgl $filteridsudah ";
        
        $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, noidsub, coa4, noslip,
                keterangan, jumlah, realisasi) $sql";
        
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
            mysqli_query($cnit, $droptable01);
            echo $erropesan; exit;
        }
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.coa4=(select b.COA4 from dbmaster.posting_coa b WHERE CONCAT(b.subpost, b.kodeid)=CONCAT(a.noid, a.noidsub) limit 1) WHERE a.kodeinput='D' AND ifnull(a.coa4,'')=''");
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.divisi='OTC' WHERE a.kodeinput='D' AND ifnull(a.divisi,'')=''");
        
        mysqli_query($cnit, "UPDATE $tmp01 as a set a.pengajuan=ifnull((select b.nama from hrd.brkd_otc b WHERE b.kodeid=a.noidsub AND b.subpost=a.noid limit 1),'BR OTC') WHERE a.kodeinput='D'");
        
    }
    
    //Budget Biaya Rutin Gelondongan COA
    $filtertgl = " AND DATE_FORMAT(periode,'%Y-%m') between '$pbln1' AND '$pbln2' ";
    $filteridsudah = " AND idbr NOT IN (SELECT DISTINCT ifnull(bridinput,'') FROM dbmaster.t_suratdana_br1 WHERE kodeinput='E' $filtereditid)";
    //if (!empty($eidinput)) $filteridsudah="";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND divisi='$edivisi' ";
    $sql = "select 'E' kodeinput, 'BRRUTINALL' pengajuan, divisi, periode, idbr, COA4, karyawanid, icabangid, keterangan, jumlah
        FROM dbmaster.t_br_bulan WHERE stsnonaktif <> 'Y' $filtertgl $fdivisi $filteridsudah ";
    
    $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, idinput, coa4, karyawanid, icabangid, keterangan, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    mysqli_query($cnit, "UPDATE $tmp01 as a set a.pengajuan='RUTIN' WHERE kodeinput='E' AND karyawanid='0000000143'");
    mysqli_query($cnit, "UPDATE $tmp01 as a set a.pengajuan='LUAR KOTA' WHERE kodeinput='E' AND karyawanid='0000000329'");
    
    //RUTIN
    $filtertgl = " AND ((DATE_FORMAT(b.periode1,'%Y-%m-%d') between '$periode1' AND '$periode2') OR (DATE_FORMAT(b.periode2,'%Y-%m-%d') between '$periode1' AND '$periode2')) ";
    $filteridsudah = " AND a.idrutin NOT IN (SELECT DISTINCT ifnull(bridinput,'') FROM dbmaster.t_suratdana_br1 WHERE kodeinput='F' $filtereditid)";
    //if (!empty($eidinput)) $filteridsudah="";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND b.divisi='$edivisi' ";
    $sql = "select 'F' kodeinput,  'RUTIN' pengajuan, b.divisi, b.bulan, b.icabangid, 
        a.idrutin, a.nobrid, a.coa, b.karyawanid, b.keterangan, sum(a.rptotal) rptotal 
        FROM dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin 
        WHERE b.stsnonaktif <> 'Y' AND kode=1 AND ifnull(b.fin,'') <> '' $filtertgl $fdivisi $filteridsudah  
        GROUP BY 1,2,3,4,5,6,7,8,9,10";
    
    $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, coa4, karyawanid, keterangan, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    //LUAR KOTA
    $filtertgl = " AND ((DATE_FORMAT(b.periode1,'%Y-%m-%d') between '$periode1' AND '$periode2') OR (DATE_FORMAT(b.periode2,'%Y-%m-%d') between '$periode1' AND '$periode2')) ";
    $filteridsudah = " AND a.idrutin NOT IN (SELECT DISTINCT ifnull(bridinput,'') FROM dbmaster.t_suratdana_br1 WHERE kodeinput='G' $filtereditid)";
    //if (!empty($eidinput)) $filteridsudah="";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND b.divisi='$edivisi' ";
    $sql = "select 'G' kodeinput,  'LUAR KOTA' pengajuan, b.divisi, b.bulan, b.icabangid, 
        a.idrutin, a.nobrid, a.coa, b.karyawanid, b.keterangan, sum(a.rptotal) rptotal 
        FROM dbmaster.t_brrutin1 a JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin 
        WHERE b.stsnonaktif <> 'Y' AND kode=2 AND ifnull(b.fin,'') <> '' $filtertgl $fdivisi $filteridsudah   
        GROUP BY 1,2,3,4,5,6,7,8,9,10";
    
    $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, coa4, karyawanid, keterangan, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    //CA RUTIN
    $filtertgl = " AND DATE_FORMAT(b.periode,'%Y-%m') between '$pbln1' AND '$pbln2' ";
    $filteridsudah = " AND a.idca NOT IN (SELECT DISTINCT ifnull(bridinput,'') FROM dbmaster.t_suratdana_br1 WHERE kodeinput='H' $filtereditid)";
    //if (!empty($eidinput)) $filteridsudah="";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND b.divisi='$edivisi' ";
    $sql = "select 'H' kodeinput,  'CA RUTIN' pengajuan, b.divisi, b.periode, b.icabangid, 
        a.idca, a.nobrid, a.coa, b.karyawanid, b.keterangan, sum(a.rptotal) rptotal 
        FROM dbmaster.t_ca1 a JOIN dbmaster.t_ca0 b on a.idca=b.idca 
        WHERE b.stsnonaktif <> 'Y' AND jenis_ca='br' AND ifnull(b.fin,'') <> '' $filtertgl $fdivisi $filteridsudah 
        GROUP BY 1,2,3,4,5,6,7,8,9,10";
    
    $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, coa4, karyawanid, keterangan, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    //CA LUAR KOTA
    $filtertgl = " AND DATE_FORMAT(b.periode,'%Y-%m') between '$pbln1' AND '$pbln2' ";
    $filteridsudah = " AND a.idca NOT IN (SELECT DISTINCT ifnull(bridinput,'') FROM dbmaster.t_suratdana_br1 WHERE kodeinput='I' $filtereditid)";
    //if (!empty($eidinput)) $filteridsudah="";
    if (!empty($edivisi) AND ($edivisi <> "*") AND ($edivisi <> "0")) $fdivisi = " AND b.divisi='$edivisi' ";
    $sql = "select 'I' kodeinput,  'CA LUAR KOTA' pengajuan, b.divisi, b.periode, b.icabangid, 
        a.idca, a.nobrid, a.coa, b.karyawanid, b.keterangan, sum(a.rptotal) rptotal 
        FROM dbmaster.t_ca1 a JOIN dbmaster.t_ca0 b on a.idca=b.idca 
        WHERE b.stsnonaktif <> 'Y' AND jenis_ca='lk' AND ifnull(b.fin,'') <> '' $filtertgl $fdivisi $filteridsudah  
        GROUP BY 1,2,3,4,5,6,7,8,9,10";
    
    $query = "INSERT INTO $tmp01 (kodeinput, pengajuan, divisi, tgl, icabangid, idinput, noid, coa4, karyawanid, keterangan, jumlah) $sql";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    //service
    
    
    //sewa
    
    
    $query = "SELECT a.*, b.NAMA4, b.COA3, c.NAMA3, c.COA2, d.NAMA2, d.COA1, e.NAMA1, d.DIVISI2 DIVISICOA, idinput idinput2  
        from $tmp01 a 
        LEFT JOIN dbmaster.coa_level4 b on a.coa4=b.COA4
        LEFT JOIN dbmaster.coa_level3 c on b.coa3=c.COA3
        LEFT JOIN dbmaster.coa_level2 d on c.coa2=d.COA2
        LEFT JOIN dbmaster.coa_level1 e on d.coa1=e.COA1";
    
    $query = "CREATE  TABLE $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { 
        mysqli_query($cnit, $droptable01);
        echo $erropesan; exit;
    }
    
    $query = "UPDATE $tmp02 set divisi='zzzz', DIVISICOA='zzzz', coa4='zzzz', coa3='zzzz', coa2='zzzz', coa1='zzzz' WHERE IFNULL(coa4,'')=''";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp02 set idinput2=''";
    mysqli_query($cnit, $query);
    
    if (!empty($eidinput)) {
        $sql = "select * FROM dbmaster.t_suratdana_br1 WHERE idinput='$eidinput'";
        $query = "CREATE  TABLE $tmp03 ($sql)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query="UPDATE $tmp02 a SET a.idinput2=(select ifnull(b.bridinput,'') from $tmp03 b WHERE a.idinput=b.bridinput and a.kodeinput=b.kodeinput) "
                . " ";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
?>

<div class='x_content'>
    <table id='datatableinputb' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='3px'>No</th>
                <th width='20px'></th>
                <th width='40px'>ID</th>
                <th width='40px'>Tanggal</th>
                <th width='30px'>Kode</th>
                <th width='80px'>Perkiraan</th>
                <th width='30px'>Jumlah</th>
                <th width='50px'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $pnojml=1;
            $query = "select distinct kodeinput from $tmp02 order by kodeinput";
            $tampilp=mysqli_query($cnit, $query);
            while ($rowp= mysqli_fetch_array($tampilp)) {
                $pkodeinp=$rowp['kodeinput'];
                $namapengajuannya="";
                if ($pkodeinp=="A") $namapengajuannya="DSS / DCC / NON";
                if ($pkodeinp=="B") $namapengajuannya="KLAIM";
                if ($pkodeinp=="C") $namapengajuannya="KAS";
                if ($pkodeinp=="D") $namapengajuannya="BR OTC";
                if ($pkodeinp=="E") $namapengajuannya="RUTIN & LUAR KOTA";
                if ($pkodeinp=="F") $namapengajuannya="RUTIN";
                if ($pkodeinp=="G") $namapengajuannya="LUAR KOTA";
                if ($pkodeinp=="H") $namapengajuannya="CA RUTIN";
                if ($pkodeinp=="I") $namapengajuannya="CA LUAR KOTA";
                if ($pkodeinp=="J") $namapengajuannya="SERVICE";
                if ($pkodeinp=="K") $namapengajuannya="SEWA";
                
                $nmchknyaall = "chkbtnall".$pkodeinp;
                $nmchknya = "chkbox_id".$pkodeinp."[]";
                $nmjmlnya = "e_jml[]";
                $kettipeisi = "<input type='checkbox' id='$nmchknyaall' name='$nmchknyaall' value='select' onClick=\"SelAllCheckBox('$nmchknyaall', '$nmchknya', '$nmjmlnya')\"/>";
                
                echo "<tr>";
                echo "<td nowrap>$kettipeisi</td>";
                echo "<td nowrap></td>";
                echo "<td nowrap colspan=6><b><u>$namapengajuannya</u></b></td>";
                echo "<td class='divnone'></td><td class='divnone'></td><td class='divnone'></td><td class='divnone'></td><td class='divnone'></td>";
                //echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                echo "</tr>";
                
                $no=1;
                $query = "select * from $tmp02 WHERE kodeinput='$pkodeinp' order by kodeinput, idinput";
                $tampil=mysqli_query($cnit, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $ppengajuan=$row['pengajuan'];
                    if ($row['kodeinput']=="E" OR $row['kodeinput']=="F" OR $row['kodeinput']=="G" OR $row['kodeinput']=="H" OR $row['kodeinput']=="I")
                        $ptgl = date("F Y", strtotime($row['tgl']));
                    else
                        $ptgl = date("d F Y", strtotime($row['tgl']));
                    $pbukti=$row['bukti'];
                    $pcoa4=$row['coa4'];
                    $pnama4=$row['NAMA4'];
                    if ($pcoa4=="zzzz") {
                        $pcoa4="";
                        $pnama4="";
                    }
                    $pidinput=$row['idinput'];
                    $pidinput2=$row['idinput2'];
                    $pdokterid=$row['iddokter'];
                    $pdokternm=$row['nmdokter'];
                    $pnoslip=$row['noslip'];
                    $ppengajuan=$row['pengajuan'];
                    $pketerangan=$row['keterangan'];
                    $pnmrealisasi=$row['nmrealisasi'];

                    $pjumlahnya=$row['jumlah'];
                    $pjumlah=number_format($row['jumlah'],0,",",",");
                    
                    
                    $nmjmlnya2 = "e_jml".$pnojml;
                    $jumlahket2 = "<input type='hidden' name='$nmjmlnya2' id='$nmjmlnya2' value='$pjumlahnya'>";
                    $pnojml++;
                    
                    $chkck="";
                    if ($pidinput2==$pidinput) $chkck="checked";
                    
                    $kettipeisi = "<input type='checkbox' value='$pidinput-$pkodeinp' name='$nmchknya' id='$nmchknya' class='cekbr' $chkck>";//onClick=\"HitungJumlah('$nmchknya', '$nmjmlnya2')\"
                    $jumlahket = "<input type='hidden' name='$nmjmlnya' id='$nmjmlnya' value='$pjumlahnya'>";
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$kettipeisi $jumlahket $jumlahket2</td>";
                    echo "<td nowrap>$pidinput</td>";
                    echo "<td nowrap>$ptgl</td>";
                    echo "<td nowrap>$pcoa4</td>";
                    echo "<td nowrap>$pnama4</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td >$pketerangan</td>";
                    echo "</tr>";

                    $no++;
                }
                
            }
        ?>
        </tbody>
    </table>

</div>
<?PHP
    mysqli_query($cnit, $droptable01);
    mysqli_query($cnit, "DROP TABLE $tmp02");
    mysqli_query($cnit, "DROP TABLE $tmp03");
    
    echo "<div hidden>";
    echo "<input type='checkbox' value='0-A' name='chkbox_idA[]' id='chkbox_idA[]' class='cekbr'>";
    echo "<input type='checkbox' value='0-B' name='chkbox_idB[]' id='chkbox_idB[]' class='cekbr'>";
    echo "<input type='checkbox' value='0-C' name='chkbox_idC[]' id='chkbox_idC[]' class='cekbr'>";
    echo "<input type='checkbox' value='0-D' name='chkbox_idD[]' id='chkbox_idD[]' class='cekbr'>";
    echo "<input type='checkbox' value='0-E' name='chkbox_idE[]' id='chkbox_idE[]' class='cekbr'>";
    echo "<input type='checkbox' value='0-F' name='chkbox_idF[]' id='chkbox_idF[]' class='cekbr'>";
    echo "<input type='checkbox' value='0-G' name='chkbox_idG[]' id='chkbox_idG[]' class='cekbr'>";
    echo "<input type='checkbox' value='0-H' name='chkbox_idH[]' id='chkbox_idH[]' class='cekbr'>";
    echo "<input type='checkbox' value='0-I' name='chkbox_idI[]' id='chkbox_idI[]' class='cekbr'>";
    echo "<input type='checkbox' value='0-J' name='chkbox_idJ[]' id='chkbox_idJ[]' class='cekbr'>";
    echo "<input type='checkbox' value='0-K' name='chkbox_idK[]' id='chkbox_idK[]' class='cekbr'>";
    echo "</div>";
?>

<script>
    $(document).ready(function() {
        var dataTable = $('#datatableinputb').DataTable( {
            fixedHeader: true,
            "ordering": false,
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10000000,
            "columnDefs": [
                { "visible": false },
                { className: "text-right", "targets": [6] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 460,
            "scrollX": true
        } );
    } );
    
</script>

<script>
    function SelAllCheckBox(nmbuton, data, ijml){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);
        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
        /*
        var ejum=null;
        var itotal=document.getElementById('e_jmlusulan').value;
        if (itotal=="") itotal="0";
        var etotal = itotal.replace(/\,/g,'');
        
        var chk_arr =  document.getElementsByName(data);
        var jmlnya =  document.getElementsByName(ijml);
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            ejum=jmlnya[k].value;
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
                etotal=parseFloat(etotal)+parseFloat(ejum);
            }else{
                etotal=parseFloat(etotal)-parseFloat(ejum);
            }
        }
        if (etotal=="") etotal="0";
        
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }
        document.getElementById('e_jmlusulan').value=etotal;
        */
    }
    
    function HitungJumlah(chknya, jumlahnya) {
        var button = document.getElementById(chknya);
        var ejum = document.getElementById(jumlahnya).value;
        
        var itotal=document.getElementById('e_jmlusulan').value;
        if (itotal=="") itotal="0";
        var etotal = itotal.replace(/\,/g,'');
        
        if (button.checked == true) {
            etotal=parseFloat(etotal)+parseFloat(ejum);
        }else{
            etotal=parseFloat(etotal)-parseFloat(ejum);
        }
        document.getElementById('e_jmlusulan').value=etotal;
    }
    
</script>