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
    
    if ($ppilihrpt=="dariinputanspd") {
        $_POST['tahun']=$_GET['utahun'];
        $_POST['ca_darispd']="N";
        $_POST['lampiran']="T";
    }
    
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
        
        $query = "select b.kodeid, b.subkode, a.bridinput, b.nodivisi, a.amount, a.jml_adj, 
            CAST(NULL as CHAR(50)) nodivisi2, CAST(NULL as DECIMAL(20,2)) as amount2, CAST(NULL as DECIMAL(20,2)) as jml_adj2 
            from dbmaster.t_suratdana_br1 a
            JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput
            where b.stsnonaktif<>'Y' and ( (b.pilih='N' and b.jenis_rpt='B') OR CONCAT(b.kodeid,b.subkode) IN ('680') ) and a.kodeinput IN ('D') 
            and year(b.tgl)='$ptahun' AND b.divisi='OTC' and date_format(b.tgl,'%Y%m')>='201909'";
        $query = "create TEMPORARY table $tmp00 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select a.bridinput, b.nodivisi, a.amount, CAST(NULL as DECIMAL(20,2)) as jml_adj from dbmaster.t_suratdana_br1 a
            JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput
             where b.stsnonaktif<>'Y' and b.jenis_rpt<>'B' and a.kodeinput IN ('D') 
             and a.bridinput in (select IFNULL(bridinput,'') from $tmp00 WHERE CONCAT(kodeid,subkode) NOT IN ('680'))";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp00 a JOIN $tmp01 b on a.bridinput=b.bridinput "
                . " SET a.nodivisi2=b.nodivisi, a.amount2=b.amount, a.jml_adj2=b.jml_adj WHERE CONCAT(a.kodeid,a.subkode) NOT IN ('680')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        
        $query = "select brOtcId, noslip, icabangid_o, tglbr, tgltrans, kodeid, subpost, real1, "
                . " jumlah, realisasi, jumlah jumlah_asli, realisasi as realisasi_asli, "
                . " keterangan1, keterangan2, lampiran, ca "
                . " from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' AND brOtcId IN (select IFNULL(bridinput,'') from $tmp00)";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 SET jumlah=NULL, realisasi=NULL"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select a.*, b.nama nama_cabang, c.nama nama_kode, "
                . " CAST('' as CHAR(50)) as nodivisi, CAST('' as CHAR(50)) as nodivisi2,"
                . " CAST('' as CHAR(10)) as subkode, CAST('Y' as CHAR(1)) as BT "
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
        
        
        $query = "UPDATE $tmp03 a JOIN dbmaster.t_suratdana_bank b on a.nodivisi=b.nodivisi SET "
                . " a.tgltrans=b.tanggal WHERE IFNULL(b.stsnonaktif,'')<>'Y' and b.stsinput='K' and b.subkode not in ('29') AND "
                . " (IFNULL(a.tgltrans,'0000-00-00')='0000-00-00' OR a.tgltrans='')"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 SET tgltrans=tglbr, BT='N' WHERE IFNULL(tgltrans,'0000-00-00')='0000-00-00' OR tgltrans=''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 a JOIN $tmp00 b on a.brOtcId=b.bridinput AND a.nodivisi=b.nodivisi "
                . " SET a.kodeid=b.kodeid, a.subkode=b.subkode WHERE IFNULL(a.nodivisi,'')<>''"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 SET kodeid='0', subkode='0' WHERE IFNULL(kodeid,'')<>'6'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 SET realisasi=realisasi_asli, nodivisi2='OP' WHERE "
                . " IFNULL(nodivisi2,'')='' AND IFNULL(lampiran,'')='Y' AND IFNULL(ca,'')='N' "
                . " AND CONCAT(kodeid,subkode) NOT IN ('680')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp03 SET realisasi=realisasi_asli, nodivisi2='SR' WHERE "
                . " IFNULL(nodivisi2,'')='' AND IFNULL(lampiran,'')='Y' AND IFNULL(ca,'')='N' "
                . " AND CONCAT(kodeid,subkode) IN ('680')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        goto querydarisps;
    }
    
    
    
    $query = "select brOtcId, noslip, icabangid_o, tglbr, tgltrans, kodeid, subpost, real1, "
            . " jumlah, realisasi, jumlah jumlah_asli, realisasi as realisasi_asli, "
            . " keterangan1, keterangan2, lampiran, ca "
            . " from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' AND  YEAR(tgltrans)='$ptahun' $fillamp";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select distinct a.bridinput, b.nodivisi, b.pilih, a.amount, a.jml_adj, b.kodeid, b.subkode "
            . " from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput "
            . " WHERE b.stsnonaktif<>'Y' AND a.kodeinput IN ('D') AND b.divisi='OTC' AND a.bridinput IN (select distinct IFNULL(brOtcId,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.*, b.nama nama_cabang, c.nama nama_kode, CAST('' as CHAR(50)) as nodivisi, CAST('' as CHAR(50)) as nodivisi2, "
            . " CAST('' as CHAR(10)) as subkode, CAST('Y' as CHAR(1)) as BT "//d.nodivisi nodivisi2, 
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
    
    
    $query = "UPDATE $tmp03 a JOIN $tmp02 b on a.brOtcId=b.bridinput AND a.nodivisi=b.nodivisi "
            . " SET a.kodeid=b.kodeid, a.subkode=b.subkode WHERE IFNULL(a.nodivisi,'')<>''"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($plampiran=='T') {
        $query = "UPDATE $tmp03 SET kodeid='0', subkode='0' WHERE IFNULL(kodeid,'')<>'6'";
    }else{
        $query = "UPDATE $tmp03 SET kodeid='0', subkode='0'";
    }
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
    <style> .str{ mso-number-format:\@; padding-left:5px; } </style>
</head>

<body>
    
    <center><h2><u></u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td colspan="5" nowrap><h2>REKAP OUTSTANDING DANA KLAIM OTC</h2></td></tr>
                <tr><td colspan="5" nowrap></td></tr>
                <tr><td colspan="5" nowrap>View Date : <i><?PHP echo "$ptglview"; ?></i></td></tr>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
    
<?PHP
$npkodeidpil="";
$queryhed = "select distinct kodeid from $tmp03 order by kodeid";
$tampilhed=mysqli_query($cnmy,$queryhed);
while ($rh= mysqli_fetch_array($tampilhed)) {
    $npkodeidpil=$rh['kodeid'];
    
    $pnmkodeid="PC-M";
    if ($npkodeidpil=="6") $pnmkodeid="KASBON SURABAYA";
    
    $ppilihjudul=true;
    if ($pcadari=="T") {
        $ppilihjudul=false;
        if ($plampiran=='T') {
            $ppilihjudul=true;
        }
    }
    
    if ($ppilihjudul==false) {
        $pnmkodeid="";
    }else{
        echo "<center><h2 style='font-size:14px; color:red;'>$pnmkodeid</h2></center>";
    }
?>
    
    <?PHP
    $pgrdjumlah=0;
    $pgrdreal=0;
    
    $pgrtotalblmreal_rp=0;
    $pgrtotalblmreal_rpop=0;
    $pgrtotalblmreal_rpbt=0;
    
    
    $query = "select distinct DATE_FORMAT(tgltrans,'%Y-%m') tgltrans from $tmp03 WHERE kodeid='$npkodeidpil' order by DATE_FORMAT(tgltrans,'%Y%m')";
    $tampil=mysqli_query($cnmy,$query);
    while ($row= mysqli_fetch_array($tampil)) {
        $ptgl=$row['tgltrans'];
        
        $pbulann=$row['tgltrans']."-01";
        $pbulann = date("F Y", strtotime($pbulann));
        echo "<h2 style='font-size:16px;'>$pbulann</h2>";
     
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
                
                $ptotalblmreal_rp=0;
                $ptotalblmreal_rpop=0;
                $ptotalblmreal_rpbt=0;
                
                $query = "select * from $tmp03 WHERE kodeid='$npkodeidpil' AND DATE_FORMAT(tgltrans,'%Y-%m')='$ptgl' ORDER BY tgltrans, noslip";//WHERE icabangid_o='$pidcabang' 
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
                    $pblmtrf=$row1['BT'];
                    
                    
                    $pwarnafield="";
                    if ($pblmtrf=="N") {
                        $ptgltrans="";
                        $pgrtotalblmreal_rpbt=(double)$pgrtotalblmreal_rpbt+(double)$pjumlah;
                        $ptotalblmreal_rpbt=(double)$ptotalblmreal_rpbt+(double)$pjumlah;
                    }else{
                        
                        if (empty($pnodivisi_2)) {
                            $pgrtotalblmreal_rp=(double)$pgrtotalblmreal_rp+(double)$pjumlah;
                            $ptotalblmreal_rp=(double)$ptotalblmreal_rp+(double)$pjumlah;
                        }elseif ($pnodivisi_2=="OP") {
                            $pwarnafield=" style='color:red;' ";
                            $pnodivisi_2="on process";
                            $pgrtotalblmreal_rpop=(double)$pgrtotalblmreal_rpop+(double)$pjumlah;
                            $ptotalblmreal_rpop=(double)$ptotalblmreal_rpop+(double)$pjumlah;
                        }
                        
                    }
                        
                        
                    $ptotjumlah=(double)$ptotjumlah+(double)$pjumlah;
                    $ptotreal=(double)$ptotreal+(double)$prealisasi;
                    

                    $pgrdjumlah=(double)$pgrdjumlah+(double)$pjumlah;
                    $pgrdreal=(double)$pgrdreal+(double)$prealisasi;
                    
                    $psisa=(double)$pjumlah-(double)$prealisasi;
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    $prealisasi=number_format($prealisasi,0,",",",");
                    $psisa=number_format($psisa,0,",",",");
                    
                    $pnwrap="";
                    if ($ppilihrpt=="excel") $pnwrap="nowrap";
                    if ($pnodivisi_2=="SR") $pnodivisi_2="";
                    
                    echo "<tr>";
                    echo "<td class='str' nowrap>$pidbr</td>";
                    echo "<td nowrap>$pnodivisi</td>";
                    echo "<td nowrap $pwarnafield>$pnodivisi_2</td>";
                    echo "<td class='str' nowrap>$pnoslip</td>";
                    echo "<td nowrap>$pnmcabang</td>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$pnmposting</td>";
                    echo "<td $pnwrap>$pketerangan</td>";
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
                
                
                if ($pcadari=="Y") {
                    
                    //TOTAL ON PROCESS
                    $ptotalblmreal_rpop=number_format($ptotalblmreal_rpop,0,",",",");
                    
                    echo "<tr style='font-weight:bold;'>";
                    echo "<td colspan='9' align='right'>TOTAL ON PROCESS : </td>";
                    echo "<td nowrap align='right'>$ptotalblmreal_rpop</td>";
                    echo "<td nowrap align='right'></td>";
                    echo "<td nowrap align='right'></td>";
                    echo "</tr>";
                    
                    
                    //TOTAL BELUM REALISASI SAMA SEKALI
                    $ptotalblmreal_rp=number_format($ptotalblmreal_rp,0,",",",");
                    
                    echo "<tr style='font-weight:bold;'>";
                    echo "<td colspan='9' align='right'>TOTAL BLM REALISASI : </td>";
                    echo "<td nowrap align='right'>$ptotalblmreal_rp</td>";
                    echo "<td nowrap align='right'></td>";
                    echo "<td nowrap align='right'></td>";
                    echo "</tr>";
                    
                    
                    //TOTAL BELUM REALISASI SAMA SEKALI
                    $ptotalblmreal_rpbt=number_format($ptotalblmreal_rpbt,0,",",",");
                    
                    echo "<tr style='font-weight:bold;'>";
                    echo "<td colspan='9' align='right'>TOTAL BLM TRANSFER : </td>";
                    echo "<td nowrap align='right'>$ptotalblmreal_rpbt</td>";
                    echo "<td nowrap align='right'></td>";
                    echo "<td nowrap align='right'></td>";
                    echo "</tr>";
                    
                    
                }
                
                ?>
            </tbody>
        </table>
        
    <?PHP
    }
    
    echo "<br/>&nbsp;";
    echo "<h2 style='font-size:16px;'>GRAND TOTAL $pnmkodeid : </h2>";
    ?>
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
            <th align="center"></th>
            <th align="center">Usulan Rp.</th>
            <th align="center">Realisasi Rp.</th>
            <th align="center">Sisa Rp.</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            
            $pgrdsisa=(double)$pgrdjumlah-(double)$pgrdreal;

            $pgrdjumlah=number_format($pgrdjumlah,0,",",",");
            $pgrdreal=number_format($pgrdreal,0,",",",");
            $pgrdsisa=number_format($pgrdsisa,0,",",",");
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap align='right'>TOTAL : </td>";
            echo "<td nowrap align='right'>$pgrdjumlah</td>";
            echo "<td nowrap align='right'>$pgrdreal</td>";
            echo "<td nowrap align='right'>$pgrdsisa</td>";
            echo "</tr>";
            
            
            
            if ($pcadari=="Y") {
                
                $pgrtotalblmreal_rpop=number_format($pgrtotalblmreal_rpop,0,",",",");
                
                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap align='right'>TOTAL ON PROCESS : </td>";
                echo "<td nowrap align='right'>$pgrtotalblmreal_rpop</td>";
                echo "<td nowrap align='right'></td>";
                echo "<td nowrap align='right'></td>";
                echo "</tr>";
                
                $pgrtotalblmreal_rp=number_format($pgrtotalblmreal_rp,0,",",",");
                
                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap align='right'>TOTAL BLM REALISASI : </td>";
                echo "<td nowrap align='right'>$pgrtotalblmreal_rp</td>";
                echo "<td nowrap align='right'></td>";
                echo "<td nowrap align='right'></td>";
                echo "</tr>";
                
                $pgrtotalblmreal_rpbt=number_format($pgrtotalblmreal_rpbt,0,",",",");
                
                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap align='right'>TOTAL BLM TRANSFER : </td>";
                echo "<td nowrap align='right'>$pgrtotalblmreal_rpbt</td>";
                echo "<td nowrap align='right'></td>";
                echo "<td nowrap align='right'></td>";
                echo "</tr>";
                
            }
            ?>
        </tbody>
    </table>
   
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    
<?PHP
}
?>
    
<?PHP
if ($pcadari!="Y" AND $ppilihrpt=="excel") {
    
    ?>
    <table>
        <tr>
            <td colspan="3" nworap><h1>Summary</h1></td>
        </tr>
    </table>
    <?PHP
    
    $npkodeidpil="";
    $queryhed = "select distinct kodeid from $tmp03 order by kodeid";
    $tampilhed=mysqli_query($cnmy,$queryhed);
    while ($rh= mysqli_fetch_array($tampilhed)) {
        $npkodeidpil=$rh['kodeid'];

        $pnmkodeid="PC-M";
        if ($npkodeidpil=="6") $pnmkodeid="KASBON SURABAYA";

        $ppilihjudul=false;
        if ($pcadari=="T") {
            if ($plampiran=='T') {
                $ppilihjudul=true;
            }
        }

        if ($ppilihjudul==false) {
            $pnmkodeid="";
        }else{
            echo "<span style='font-size:14px; color:red;'>$pnmkodeid</span>";
        }
    
    
        $pgrdjumlah=0;
        $pgrdreal=0;

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

                $query = "select * from $tmp03 WHERE kodeid='$npkodeidpil' ORDER BY tgltrans, noslip";//WHERE icabangid_o='$pidcabang' 
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
                    echo "<td class='str' nowrap>$pidbr</td>";
                    echo "<td nowrap>$pnodivisi</td>";
                    echo "<td nowrap>$pnodivisi_2</td>";
                    echo "<td class='str' nowrap>$pnoslip</td>";
                    echo "<td nowrap>$pnmcabang</td>";
                    echo "<td nowrap>$ptgltrans</td>";
                    echo "<td nowrap>$pnmposting</td>";
                    echo "<td nowrap>$pketerangan</td>";
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
    }
    
}
?>
    
    
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