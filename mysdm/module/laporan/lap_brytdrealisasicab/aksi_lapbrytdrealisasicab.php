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
        header("Content-Disposition: attachment; filename=Laporan_YTD_Realisasi_Budget_Request_Ethical.xls");
    }
    
    include("config/koneksimysqli.php");
    
    $printdate= date("d/m/Y");
?>


<?PHP
    $tgl01 = $_POST['e_periode01'];
    $pdivisipil = $_POST['cb_divisi'];
    $ptipepil = $_POST['e_pilihtipe'];
    $pcabidpil = $_POST['cb_cabangid'];
    
    $pperiode1 = $tgl01;
    
    $myperiode1 = $tgl01;
    

    
    $filterdss=" ('700-01-04', '700-02-04', '700-04-04') ";
    $filterdcc=" ('700-01-03', '700-02-03', '700-04-03') ";
    $filterdssdcc=" ('700-01-04', '700-02-04', '700-04-04', '700-01-03', '700-02-03', '700-04-03') ";
    
    $filpilihdssdcc="";
    if ($ptipepil=="D") $filpilihdssdcc=$filterdss;
    elseif ($ptipepil=="C") $filpilihdssdcc=$filterdcc;
    elseif ($ptipepil=="DC") $filpilihdssdcc=$filterdssdcc;
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];

    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpbrrealisasi00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpbrrealisasi01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpbrrealisasi02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpbrrealisasi03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmpbrrealisasi04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmpbrrealisasi05_".$puserid."_$now ";
    $tmp11 =" dbtemp.tmpbrrealisasi11_".$puserid."_$now ";
    
    
    $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
            . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' AND a.kodeinput IN ('A', 'B', 'C') "
            . " AND b.divisi<>'OTC'";//AND b.tgl>='$pperiode1' 
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            $query = "ALTER table $tmp00 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query = "CREATE UNIQUE INDEX `unx1` ON $tmp00 (noidauto)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
    $query = "select distinct tanggal, nobukti, idinput, nodivisi from dbmaster.t_suratdana_bank "
            . " WHERE IFNULL(stsnonaktif,'')<>'Y' and stsinput='K' and subkode not in ('29') "
            . " AND idinput IN (select distinct IFNULL(idinput,'') from $tmp00)";
    $query = "create TEMPORARY table $tmp11 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
            $query = "ALTER table $tmp11 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            $query = "CREATE UNIQUE INDEX `unx1` ON $tmp11 (noidauto)";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
    
    
        //via SBY
        $query = "select bridinput, tgltransfersby, jumlah jmlsby, nobukti from dbmaster.t_br0_via_sby WHERE YEAR(tgltransfersby) = '$pperiode1' ";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select brid, noslip, icabangid, tgl, tgltrans, tglrpsby, tgltrm, divprodid, COA4, kode, realisasi1, "
            . " jumlah, jumlah1, "
            . " aktivitas1, aktivitas2, dokterid, dokter, karyawanid, ccyid, lampiran, ca, "
            . " dpp, ppn_rp, pph_rp, tgl_fp, batal, retur "
            . " from hrd.br0 WHERE IFNULL(batal,'')<>'Y' AND IFNULL(retur,'')<>'Y' AND "
            . " brId NOT IN (select DISTINCT IFNULL(brId,'') FROM hrd.br0_reject) AND ";
    
    $query .= " ( (YEAR(tgltrans) = '$pperiode1') OR brid IN (select distinct IFNULL(bridinput,'') FROM $tmp02) ) ";
    
    if (!empty($pdivisipil)) $query .=" AND divprodid='$pdivisipil' ";
    if (!empty($pcabidpil)) $query .=" AND icabangid='$pcabidpil' ";
    
    if (!empty($ptipepil)) {
        if ($ptipepil=="N") $query .=" AND kode NOT IN $filterdssdcc ";
        else $query .=" AND kode IN $filpilihdssdcc ";
    }
    
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp01 ADD COLUMN nobuktibbk VARCHAR(20)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //via SBY
    
        $query = "UPDATE $tmp01 a JOIN (select bridinput, sum(jmlsby) as jmlsby from $tmp02 group by 1) b on "
                . " a.brid=b.bridinput SET a.jumlah1=b.jmlsby WHERE IFNULL(a.jumlah1,0)=0";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 a JOIN (select bridinput, tgltransfersby, nobukti from $tmp02 WHERE IFNULL(tgltransfersby,'')<>'' AND IFNULL(tgltransfersby,'0000-00-00')<>'0000-00-00' ) b on "
                . " a.brid=b.bridinput SET a.tgltrans=b.tgltransfersby, a.nobuktibbk=b.nobukti WHERE IFNULL(a.tgltrans,'')='' OR IFNULL(a.tgltrans,'0000-00-00')='0000-00-00'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp01 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)<>0";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    

        
        
    $query = "select a.*, d.nama nama_dokter, e.nama nama_karyawan, b.nama nama_cabang, c.nama nama_kode, f.NAMA4 "
            . " from $tmp01 a LEFT JOIN mkt.icabang b on a.icabangid=b.icabangid "
            . " LEFT JOIN hrd.br_kode c on a.kode=c.kodeid "
            . " LEFT JOIN hrd.dokter d on a.dokterId=d.dokterId"
            . " LEFT JOIN hrd.karyawan e on a.karyawanId=e.karyawanId "
            . " LEFT JOIN dbmaster.coa_level4 f on a.COA4=f.COA4";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "ALTER table $tmp02 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(20), ADD COLUMN idinput1 BIGINT(20), ADD COLUMN nodivisi1 VARCHAR(20), ADD COLUMN idinput2 BIGINT(20), ADD COLUMN nodivisi2 VARCHAR(20)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    $query = "UPDATE $tmp02 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp00 WHERE IFNULL(pilih,'')<>'Y') b on a.brId=b.bridinput "
            . " SET a.nodivisi1=b.nodivisi, a.idinput1=b.idinput";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp00 WHERE IFNULL(pilih,'')='Y') b on a.brId=b.bridinput "
            . " SET a.nodivisi2=b.nodivisi, a.idinput2=b.idinput";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET nodivisi=nodivisi2, idinput=idinput2";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp02 SET nodivisi=nodivisi1, idinput=idinput1 WHERE IFNULL(nodivisi,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    //isi tanggal transfer no bukti bbk bobukti
    $query = "UPDATE $tmp02 a JOIN $tmp11 b on a.idinput=b.idinput SET a.nobuktibbk=b.nobukti";//a.nobukti=b.nobukti, a.tgltrans=b.tanggal
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        $query ="DELETE FROM $tmp02 WHERE IFNULL(nodivisi,'')='' AND DATE_FORMAT(tgl,'%Y-%m')>='2020-01'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    $query = "select distinct divprodid, icabangid, nama_cabang, cast(null as decimal(20,2)) as bulan1, cast(null as decimal(20,2)) as bulan2,"
            . " cast(null as decimal(20,2)) as bulan3, cast(null as decimal(20,2)) as bulan4, cast(null as decimal(20,2)) as bulan5,"
            . " cast(null as decimal(20,2)) as bulan6, cast(null as decimal(20,2)) as bulan7, cast(null as decimal(20,2)) as bulan8,"
            . " cast(null as decimal(20,2)) as bulan9, cast(null as decimal(20,2)) as bulan10, cast(null as decimal(20,2)) as bulan11,"
            . " cast(null as decimal(20,2)) as bulan12"
            . " from $tmp02";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    for ($i=1; $i <= 12; $i++) {
        $fldbulan = $myperiode1.$kodenya=str_repeat("0", 1).$i;
        
        $query = "update $tmp04 as a JOIN (select divprodid, icabangid, sum(jumlah) as jumlah "
                . " from $tmp02 WHERE DATE_FORMAT(tgltrans,'%Y%m')='$fldbulan' GROUP BY 1,2) b "
                . " on a.icabangid=b.icabangid and a.divprodid=b.divprodid "
                . " SET a.bulan$i=b.jumlah";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
    
        
?>
<HTML>
<HEAD>
    <title>Laporan YTD Realisasi Budget Request Ethical</title>
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

    <center><div class='h1judul'>Laporan YTD Realisasi Budget Request Ethical</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <?PHP
            echo "<tr> <td>Tahun </td> <td>:</td> <td><b>$myperiode1</b></td> </tr>";
            echo "<tr class='miring text2'> <td>view date</td> <td>:</td> <td>$printdate</td> </tr>";
            ?>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
            <th align="center">No.</th>
            <th>Nama Cabang</th>
            <th>Januari</th>
            <th>Februari</th>
            <th>Maret</th>
            <th>April</th>
            <th>Mei</th>
            <th>Juni</th>
            <th>Juli</th>
            <th>Agustus</th>
            <th>September</th>
            <th>Oktober</th>
            <th>November</th>
            <th>Desember</th>
            <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $totalreal=0;
            $subtotreal=0;
            $no=1;
            $query = "select distinct divprodid from $tmp04 ";
            $query .= " ORDER BY divprodid";
            $tampil1= mysqli_query($cnmy, $query);
            while ($row1= mysqli_fetch_array($tampil1)) {
                $pdivisi=$row1['divprodid'];
                $mdivisinm=$pdivisi;
                if ($pdivisi=="PIGEO") $mdivisinm="PIGEON";
                if ($pdivisi=="PEACO") $mdivisinm="PEACOK";
                
                echo "<tr>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap colspan='14' align='left'><b>$mdivisinm</b></td>";
                echo "</tr>";
                
                $subtotreal=0;
                $no=1;
                $query = "select * from $tmp04 WHERE divprodid='$pdivisi' ";
                $query .= " ORDER BY divprodid, nama_cabang";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $ppnmcabang=$row['nama_cabang'];

                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td>$ppnmcabang</td>";
                    for ($i=1; $i <= 12; $i++) {
                        $b="bulan".$i;
                        $subtotreal =floatval($subtotreal)+floatval($row[$b]);
                        $fb=number_format($row[$b],0,",",",");
                        echo "<td align='right'>".$fb."</td>";
                    }
                    $subtotreal=number_format($subtotreal,0,",",",");
                    echo "<td align='right'><b>$subtotreal</b></td>";
                    echo "</tr>";
                    $subtotreal=0;
                    $no++;
                }
                
                
                $subtotreal=0;
                //sub total
                $sub0 = mysqli_query($cnmy, "select sum(bulan1) as bulan1, sum(bulan2) as bulan2, sum(bulan3) as bulan3, "
                        . " sum(bulan4) as bulan4, sum(bulan5) as bulan5, sum(bulan6) as bulan6, sum(bulan7) as bulan7, "
                        . " sum(bulan8) as bulan8, sum(bulan9) as bulan9, sum(bulan10) as bulan10, sum(bulan11) as bulan11, "
                        . " sum(bulan12) as bulan12 "
                        . " from $tmp04 where "
                        . " divprodid='$pdivisi'");
                while ($s0=mysqli_fetch_array($sub0)){

                    $namasub="Total $mdivisinm : ";

                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td><b>$namasub</b></td>";
                    for ($i=1; $i <= 12; $i++) {
                        $b="bulan".$i;
                        $subtotreal =floatval($subtotreal)+floatval($s0[$b]);
                        $fb=number_format($s0[$b],0,",",",");
                        echo "<td align='right'><b>".$fb."</b></td>";
                    }
                    $subtotreal=number_format($subtotreal,0,",",",");
                    echo "<td align='right'><b>$subtotreal</b></td>";
                    echo "</tr>";
                    $subtotreal=0;
                }

                $subtotreal=0;
                
                
            }
            
            
            // total
            $sub1 = mysqli_query($cnmy, "select sum(bulan1) as bulan1, sum(bulan2) as bulan2, sum(bulan3) as bulan3, "
                        . " sum(bulan4) as bulan4, sum(bulan5) as bulan5, sum(bulan6) as bulan6, sum(bulan7) as bulan7, "
                        . " sum(bulan8) as bulan8, sum(bulan9) as bulan9, sum(bulan10) as bulan10, sum(bulan11) as bulan11, "
                        . " sum(bulan12) as bulan12 "
                        . " from $tmp04");

            while ($s1=mysqli_fetch_array($sub1)){

                $namatot="Grand Total : ";

                echo "<tr>";
                echo "<td></td>";
                echo "<td><b>$namatot</b></td>";
                for ($i=1; $i <= 12; $i++) {
                    $b="bulan".$i;
                    $subtotreal =floatval($subtotreal)+floatval($s1[$b]);
                    $fb=number_format($s1[$b],0,",",",");
                    echo "<td align='right'><b>".$fb."</b></td>";
                }
                $subtotreal=number_format($subtotreal,0,",",",");
                echo "<td align='right'><b>$subtotreal</b></td>";
                echo "</tr>";
                $subtotreal=0;
            }
            
            $subtotreal=0;
                
                
            ?>
        </tbody>
    </table>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
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
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp11");
    mysqli_close($cnmy);
?>