<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP OUTSTANDING DANA KLAIM OTC.xls");
    }
    
    $hariini=date("Y-m-d");
    $ptglview = date("d F Y", strtotime($hariini));
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp00 =" dbtemp.RPTREKOTOTSOTC00_".$puserid."_$now ";
    $tmp01 =" dbtemp.RPTREKOTOTSOTC01_".$puserid."_$now ";
    $tmp02 =" dbtemp.RPTREKOTOTSOTC02_".$puserid."_$now ";
    $tmp03 =" dbtemp.RPTREKOTOTSOTC03_".$puserid."_$now ";
    
    
    $ptahun=$_POST['tahun'];
    $pcadari=$_POST['ca_darispd'];
    $plampiran = $_POST['lampiran'];
    $fillamp="";
    
    if ($plampiran=='L') {
        $fillamp=" AND lampiran='Y' ";
    }elseif ($plampiran=='T') {
        $fillamp=" AND (IFNULL(lampiran,'')='N' OR IFNULL(lampiran,'')='') ";
    }
    
    
    
    if ($pcadari=="Y") {
        
        $query = "select a.bridinput, b.nodivisi, a.amount, a.jml_adj, 
            CAST(NULL as CHAR(50)) nodivisi2, CAST(NULL as DECIMAL(20,2)) as amount2, CAST(NULL as DECIMAL(20,2)) as jml_adj2 
            from dbmaster.t_suratdana_br1 a
            JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput
            where b.stsnonaktif<>'Y' and b.pilih='N' and b.jenis_rpt='B' and a.kodeinput IN ('D') 
            and year(b.tgl)='$ptahun' AND b.divisi='OTC' and date_format(b.tgl,'%Y%m')>='201909'";
        $query = "create TEMPORARY table $tmp00 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select a.bridinput, b.nodivisi, a.amount, a.jml_adj from dbmaster.t_suratdana_br1 a
            JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput
             where b.stsnonaktif<>'Y' and b.jenis_rpt<>'B' and a.kodeinput IN ('D') 
             and a.bridinput in (select IFNULL(bridinput,'') from $tmp00)";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp00 a JOIN $tmp01 b on a.bridinput=b.bridinput "
                . " SET a.nodivisi2=b.nodivisi, a.amount2=b.amount, a.jml_adj2=b.jml_adj";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        
        $query = "select brOtcId, noslip, icabangid_o, tglbr, tgltrans, kodeid, subpost, real1, "
                . " jumlah, realisasi, jumlah jumlah_asli, realisasi as realisasi_asli, "
                . " keterangan1, keterangan2 "
                . " from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' AND brOtcId IN (select IFNULL(bridinput,'') from $tmp00)";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 SET jumlah=NULL, realisasi=NULL"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select a.*, b.nama nama_cabang, c.nama nama_kode, "
                . " CAST('' as CHAR(50)) as nodivisi, CAST('' as CHAR(50)) as nodivisi2  "
                . " from $tmp01 a LEFT JOIN mkt.icabang_o b on a.icabangid_o=b.icabangid_o "
                . " LEFT JOIN hrd.brkd_otc c on a.kodeid=c.kodeid and a.subpost=c.subpost "
                . " ";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        $query = "UPDATE $tmp03 a JOIN $tmp00 b on a.brOtcId=b.bridinput "
                . "SET a.nodivisi=b.nodivisi, a.nodivisi2=b.nodivisi2, a.jumlah=IFNULL(b.amount,0)+IFNULL(b.jml_adj,0), a.realisasi=IFNULL(b.amount2,0)+IFNULL(b.jml_adj2,0)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp03 SET tgltrans=tglbr WHERE IFNULL(tgltrans,'0000-00-00')='0000-00-00' OR tgltrans=''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        goto querydarisps;
    }
    
    
    
    $query = "select brOtcId, noslip, icabangid_o, tglbr, tgltrans, kodeid, subpost, real1, "
            . " jumlah, realisasi, jumlah jumlah_asli, realisasi as realisasi_asli, "
            . " keterangan1, keterangan2 "
            . " from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' AND  YEAR(tgltrans)='$ptahun' $fillamp";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select distinct a.bridinput, b.nodivisi, b.pilih, a.amount, a.jml_adj "
            . " from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput "
            . " WHERE b.stsnonaktif<>'Y' AND a.kodeinput IN ('D') AND b.divisi='OTC' AND a.bridinput IN (select distinct IFNULL(brOtcId,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.*, b.nama nama_cabang, c.nama nama_kode, CAST('' as CHAR(50)) as nodivisi, CAST('' as CHAR(50)) as nodivisi2 "//d.nodivisi nodivisi2, 
            . " from $tmp01 a LEFT JOIN mkt.icabang_o b on a.icabangid_o=b.icabangid_o "
            . " LEFT JOIN hrd.brkd_otc c on a.kodeid=c.kodeid and a.subpost=c.subpost "
            . " ";//LEFT JOIN (select distinct bridinput, nodivisi FROM $tmp02 WHERE IFNULL(pilih,'')='Y') as d on a.brOtcId=d.bridinput
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp03 a JOIN $tmp02 b on a.brOtcId=b.bridinput "
            . "SET a.nodivisi=b.nodivisi WHERE b.pilih='Y'"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp03 a JOIN $tmp02 b on a.brOtcId=b.bridinput "
            . "SET a.nodivisi=b.nodivisi WHERE b.pilih='N' AND IFNULL(a.nodivisi,'')=''"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
querydarisps:
    
    $query = "UPDATE $tmp03 SET nama_cabang=icabangid_o WHERE IFNULL(nama_cabang,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
?>
<html>
<head>
    <?PHP 
        echo "<title>REKAP OUTSTANDING DANA KLAIM OTC</title>";
     
        if ($_GET['ket']!="excel") {
            echo "<meta http-equiv='Expires' content='Mon, 01 Jan 2019 1:00:00 GMT'>";
            echo "<meta http-equiv='Pragma' content='no-cache'>";
            echo "<link rel='shortcut icon' href='images/icon.ico' />";
            echo "<link href='css/laporanbaru.css' rel='stylesheet'>";
            
            header("Cache-Control: no-cache, must-revalidate");
        }
        
    ?>
</head>

<body>
    
    <center><h2><u></u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td><h2>REKAP OUTSTANDING DANA KLAIM OTC</h2></td><td><h2><?PHP echo ""; ?></h2></td></tr>
                <tr><td></td><td><?PHP echo ""; ?></td></tr>
                <tr><td>View Date : <i><?PHP echo "$ptglview"; ?></i></td><td><?PHP echo ""; ?></td></tr>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
    
    <?PHP
    $pgrdjumlah=0;
    $pgrdreal=0;
    /*
    $query = "select distinct nama_cabang, icabangid_o from $tmp03 order by nama_cabang, icabangid_o";
    $tampil=mysqli_query($cnmy,$query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pidcabang=$row['icabangid_o'];
        $pnmcabang=$row['nama_cabang'];
        
        echo "<h2 style='font-size:16px;'>$pnmcabang</h2>";
     * 
     */
    ?>
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">ID</th>
                <th align="center">No BR/Divisi</th>
                <th align="center">No BR/Divisi<br/>Realisasi</th>
                <th align="center">Noslip</th>
                <th align="center">Cabang</th>
                <th align="center">Tgl. Transfer</th>
                <th align="center">Sub-Posting</th>
                <th align="center">Keterangan</th>
                <th align="center">Nama Realisasi</th>
                <th align="center">Usulan Rp.</th>
                <th align="center">Realisasi Rp.</th>
                <th align="center">Sisa Rp.</th>
                
                </tr>
            </thead>
            <tbody>
                <?PHP
                $ptotjumlah=0;
                $ptotreal=0;
                
                $query = "select * from $tmp03 ORDER BY tgltrans, noslip";//WHERE icabangid_o='$pidcabang' 
                $tampil1=mysqli_query($cnmy,$query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidbr=$row1['brOtcId'];
                    $pnodivisi=$row1['nodivisi'];
                    $pnodivisi_2=$row1['nodivisi2'];
                    $pnoslip=$row1['noslip'];
                    
                    $pidcabang=$row1['icabangid_o'];
                    $pnmcabang=$row1['nama_cabang'];
        
                    $ptgltrans=$row1['tgltrans'];
                    if ($ptgltrans=="0000-00-00") $ptgltrans="";
                    if (!empty($ptgltrans) AND $ptgltrans<>"0000-00-00") $ptgltrans = date("d/m/Y", strtotime($ptgltrans));
                    
                    $pnmposting=$row1['nama_kode'];
                    $pketerangan=$row1['keterangan1'];
                    $pnmreal=$row1['real1'];
                    $pjumlah=$row1['jumlah'];
                    $prealisasi=$row1['realisasi'];
                    
                    $ptotjumlah=(double)$ptotjumlah+(double)$pjumlah;
                    $ptotreal=(double)$ptotreal+(double)$prealisasi;
                    

                    $pgrdjumlah=(double)$pgrdjumlah+(double)$pjumlah;
                    $pgrdreal=(double)$pgrdreal+(double)$prealisasi;
                    
                    $psisa=(double)$pjumlah-(double)$prealisasi;
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    $prealisasi=number_format($prealisasi,0,",",",");
                    $psisa=number_format($psisa,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap>$pidbr</td>";
                    echo "<td nowrap>$pnodivisi</td>";
                    echo "<td nowrap>$pnodivisi_2</td>";
                    echo "<td nowrap>$pnoslip</td>";
                    echo "<td nowrap>$pnmcabang</td>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$pnmposting</td>";
                    echo "<td>$pketerangan</td>";
                    echo "<td nowrap>$pnmreal</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap align='right'>$prealisasi</td>";
                    echo "<td nowrap align='right'>$psisa</td>";
                    echo "</tr>";
                    
                }
                
                $ptotsisa=(double)$ptotjumlah-(double)$ptotreal;

                $ptotjumlah=number_format($ptotjumlah,0,",",",");
                $ptotreal=number_format($ptotreal,0,",",",");
                $ptotsisa=number_format($ptotsisa,0,",",",");
                    
                echo "<tr style='font-weight:bold;'>";
                echo "<td colspan='9' align='right'>TOTAL : </td>";
                echo "<td nowrap align='right'>$ptotjumlah</td>";
                echo "<td nowrap align='right'>$ptotreal</td>";
                echo "<td nowrap align='right'>$ptotsisa</td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
        
    <?PHP
    //}
    
    //echo "<br/>&nbsp;";
    //echo "<h2 style='font-size:16px;'>GRAND TOTAL : </h2>";
    ?>
    <!--
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
            <th align="center">Usulan Rp.</th>
            <th align="center">Realisasi Rp.</th>
            <th align="center">Sisa Rp.</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            /*
            $pgrdsisa=(double)$pgrdjumlah-(double)$pgrdreal;

            $pgrdjumlah=number_format($pgrdjumlah,0,",",",");
            $pgrdreal=number_format($pgrdreal,0,",",",");
            $pgrdsisa=number_format($pgrdsisa,0,",",",");
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap align='right'>$pgrdjumlah</td>";
            echo "<td nowrap align='right'>$pgrdreal</td>";
            echo "<td nowrap align='right'>$pgrdsisa</td>";
            echo "</tr>";
             * 
             */
            ?>
        </tbody>
    </table>
    -->
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
</body>

</html>

<?PHP
hapusdata:
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");

    mysqli_close($cnmy);
?>