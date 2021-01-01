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
        header("Content-Disposition: attachment; filename=Laporan_Budget_Request_CHC.xls");
    }
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    
    $printdate= date("d/m/Y");
?>


<?PHP
    $tgl01 = $_POST['e_periode01'];
    $tgl02 = $_POST['e_periode02'];
    $ptypetgl = $_POST['cb_tgltipe'];
    $pcabidpil = $_POST['cb_cabangid'];
    
    $pperiode1 = date("Y-m-d", strtotime($tgl01));
    $pperiode2 = date("Y-m-d", strtotime($tgl02));
    
    $pstsperiode="Transfer";
    if ($ptypetgl=="2") $pstsperiode="Input/Pengajuan";
    
    $myperiode1 = date("d/m/Y", strtotime($tgl01));
    $myperiode2 = date("d/m/Y", strtotime($tgl02));
    
    
    $filterkode=('');
    if (!empty($_POST['chkbox_kodeotc'])){
        $filterkode=$_POST['chkbox_kodeotc'];
        $filterkode=PilCekBox($filterkode);
    }
    $filterkode=" and kodeid in $filterkode ";

    
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];

    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmprptbrchc00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmprptbrchc01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprptbrchc02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmprptbrchc03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmprptbrchc04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmprptbrchc05_".$puserid."_$now ";
    $tmp11 =" dbtemp.tmprptbrchc11_".$puserid."_$now ";
    
    
    $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
            . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' AND a.kodeinput IN ('D') "
            . " AND b.tgl>='$pperiode1' AND b.divisi='OTC'";
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
            
            
    $query = "select brOtcId, noslip, icabangid_o, tglbr, tgltrans, tglrpsby, tglreal, COA4, kodeid, subpost, real1, "
            . " jumlah, realisasi, jumlah jumlah_asli, realisasi as realisasi_asli, "
            . " keterangan1, keterangan2, lampiran, ca, jenis, dpp, ppn_rp, pph_rp, tgl_fp, batal "
            . " from hrd.br_otc WHERE IFNULL(batal,'')<>'Y' AND "
            . " brOtcId NOT IN (select DISTINCT IFNULL(brOtcId,'') FROM hrd.br_otc_reject) $filterkode AND ";
    if ($ptypetgl=="1") {
        $query .= " tgltrans BETWEEN '$pperiode1' AND '$pperiode2' ";
    }else{
        $query .= " tglbr BETWEEN '$pperiode1' AND '$pperiode2' ";
    }
    if (!empty($pcabidpil)) $query .=" AND icabangid_o='$pcabidpil' ";
    
    
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp01 ADD COLUMN nobuktibbk VARCHAR(20), ADD COLUMN nmkodeid VARCHAR(100), ADD COLUMN nmsubpost VARCHAR(100)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN hrd.brkd_otc b on a.subpost=b.subpost SET a.nmkodeid=b.nmsubpost";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN hrd.brkd_otc b on a.kodeid=b.kodeid AND a.subpost=b.subpost SET a.nmsubpost=b.nama";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "select a.*, b.nama nama_cabang, c.NAMA4 "
            . " from $tmp01 a LEFT JOIN mkt.icabang_o b on a.icabangid_o=b.icabangid_o "
            . " LEFT JOIN dbmaster.coa_level4 c on a.COA4=c.COA4";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "ALTER table $tmp02 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(20), ADD COLUMN idinput1 BIGINT(20), ADD COLUMN nodivisi1 VARCHAR(20), ADD COLUMN idinput2 BIGINT(20), ADD COLUMN nodivisi2 VARCHAR(20)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "UPDATE $tmp02 SET nama_cabang=icabangid_o where IFNULL(nama_cabang,'')=''";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    $query = "UPDATE $tmp02 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp00 WHERE IFNULL(pilih,'')<>'Y') b on a.brOtcId=b.bridinput "
            . " SET a.nodivisi1=b.nodivisi, a.idinput1=b.idinput";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp00 WHERE IFNULL(pilih,'')='Y') b on a.brOtcId=b.bridinput "
            . " SET a.nodivisi2=b.nodivisi, a.idinput2=b.idinput";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET nodivisi=nodivisi2, idinput=idinput2";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp02 SET nodivisi=nodivisi1, idinput=idinput1 WHERE IFNULL(nodivisi,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    //isi tanggal transfer no bukti bbk bobukti
    $query = "UPDATE $tmp02 a JOIN $tmp11 b on a.idinput=b.idinput SET a.nobuktibbk=b.nobukti";//a.nobukti=b.nobukti, a.tgltrans=b.tanggal
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
?>
<HTML>
<HEAD>
    <title>Laporan Budget Request CHC</title>
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

    <center><div class='h1judul'>Laporan Budget Request CHC</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <?PHP
            echo "<tr> <td>Periode $pstsperiode</td> <td>:</td> <td><b>$myperiode1 s/d. $myperiode2</b></td> </tr>";
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
            <th align="center">ID</th>
            <th align="center">Tanggal</th>
            <th align="center">Tgl. Trans</th>
            <th align="center">Cabang</th>
            <th align="center">Alokasi Budget</th>
            <th align="center">Alokasi Budget</th>
            <th align="center">Perkiraan</th>
            <th align="center">No. Slip</th>
            <th align="center">Jumlah</th>
            <th align="center">Jml. Realisasi</th>
            <th align="center">Selisih Rp.</th>
            <th align="center">Realisasi</th>
            <th align="center">Tgl. Real</th>
            <th align="center">Keterangan</th>
            <th align="center">Tgl. Sby</th>
            <th align="center">No Divisi</th>
            <th align="center">No Divisi PCM</th>
            <th align="center">BBK</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $pgrtotal1=0;
            $pgrtotal2=0;
            $no=1;
            $query = "select * from $tmp02 ";
            $query .= " ORDER BY nmkodeid, nmsubpost, NAMA4";
            $tampil= mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pbrid=$row['brOtcId'];
                
                $ptglbr=$row['tglbr'];
                $ptgltrs=$row['tgltrans'];
                
                $pnmakun=$row['nmkodeid'];
                $pnmsubakun=$row['nmsubpost'];
                $pcoa=$row['COA4'];
                $pnmcoa=$row['NAMA4'];
                $ppnmcabang=$row['nama_cabang'];
                $pnoslip=$row['noslip'];
                $pnmrealisasi=$row['real1'];
                $pjmlrp1=$row['jumlah'];
                $pjmlrp2=$row['realisasi'];
                $tgreali=$row['tglreal'];
                $pket1=$row['keterangan1'];
                $pket2=$row['keterangan2'];
                $ptglrpsby=$row['tglrpsby'];
                $pnodivisi=$row['nodivisi'];
                $pnodivisi1=$row['nodivisi1'];
                $pnodivisi2=$row['nodivisi2'];
                $pnobuktibbk=$row['nobuktibbk'];
                
                
                
                $pselisih=(double)$pjmlrp1-(double)$pjmlrp2;
                
                $pgrtotal1=(double)$pgrtotal1+(double)$pjmlrp1;
                $pgrtotal2=(double)$pgrtotal2+(double)$pjmlrp2;
                
                $pdokternm="";
                if (!empty($pnmdokter)) $pdokternm=$piddokter." - ".$pnmdokter;
                
                
                if ($ptgltrs=="0000-00-00") $ptgltrs="";
                if ($tgreali=="0000-00-00") $tgreali="";
                if ($ptglrpsby=="0000-00-00") $ptglrpsby="";
                
                $ptglbr = date("d/m/Y", strtotime($ptglbr));
                if (!empty($ptgltrs)) $ptgltrs = date("d/m/Y", strtotime($ptgltrs));
                if (!empty($tgreali)) $tgreali = date("d/m/Y", strtotime($tgreali));
                if (!empty($ptglrpsby)) $ptglrpsby = date("d/m/Y", strtotime($ptglrpsby));
                
                $pjmlrp1=number_format($pjmlrp1,0,",",",");
                $pjmlrp2=number_format($pjmlrp2,0,",",",");
                $pselisih=number_format($pselisih,0,",",",");
                
                
                $pketerangan=$pket1;
                if (!empty($pket2)) {
                    if (!empty($pketerangan)) $pketerangan .=" ".$pket2;
                    else $pketerangan=$pket2;
                }
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap class='str'>$pbrid</td>";
                echo "<td nowrap>$ptglbr</td>";
                echo "<td nowrap>$ptgltrs</td>";
                echo "<td nowrap>$ppnmcabang</td>";
                echo "<td nowrap>$pnmakun</td>";
                echo "<td nowrap>$pnmsubakun</td>";
                echo "<td nowrap>$pcoa $pnmcoa</td>";
                echo "<td nowrap class='str'>$pnoslip</td>";
                echo "<td nowrap align='right'>$pjmlrp1</td>";
                echo "<td nowrap align='right'>$pjmlrp2</td>";
                echo "<td nowrap align='right'>$pselisih</td>";
                echo "<td nowrap>$pnmrealisasi</td>";
                echo "<td nowrap>$tgreali</td>";
                echo "<td >$pketerangan</td>";
                echo "<td nowrap>$ptglrpsby</td>";
                echo "<td nowrap>$pnodivisi2</td>";
                echo "<td nowrap>$pnodivisi1</td>";
                echo "<td nowrap>$pnobuktibbk</td>";
                echo "</tr>";
                
                
                $no++;
            }
            $pselisih=(double)$pgrtotal1-(double)$pgrtotal2;
            
            $pgrtotal1=number_format($pgrtotal1,0,",",",");
            $pgrtotal2=number_format($pgrtotal2,0,",",",");
            $pselisih=number_format($pselisih,0,",",",");
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap colspan='9' align='center'>T O T A L : </td>";
            echo "<td nowrap align='right'>$pgrtotal1</td>";
            echo "<td nowrap align='right'>$pgrtotal2</td>";
            echo "<td nowrap align='right'>$pselisih</td>";
            echo "<td nowrap colspan='7'></td>";
            echo "</tr>";
            ?>
        </tbody>
    </table>
    
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