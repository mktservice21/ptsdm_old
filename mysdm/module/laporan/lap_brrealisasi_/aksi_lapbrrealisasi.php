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
        header("Content-Disposition: attachment; filename=Laporan_Realisasi_Budget_Request_Ethical.xls");
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
        
        
?>
<HTML>
<HEAD>
    <title>Laporan Realisasi Budget Request Ethical</title>
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

    <center><div class='h1judul'>Laporan Realisasi Budget Request Ethical</div></center>
    
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
            <th align="center">ID</th>
            <th align="center">Tanggal</th>
            <th align="center">Tgl. Trans</th>
            <th align="center">Divisi</th>
            <th align="center">Cabang</th>
            <th align="center">Akun</th>
            <th align="center">Perkiraan</th>
            <th align="center">Yg. Membuat</th>
            <th align="center">Dokter</th>
            <th align="center">No. Slip</th>
            <th align="center">Jumlah</th>
            <th align="center">Realisasi</th>
            <th align="center">Tgl. Trm</th>
            <th align="center">Keterangan</th>
            <th align="center">Tgl. Sby</th>
            <th align="center">No Divisi</th>
            <th align="center">No Divisi PCM</th>
            <th align="center">BBK</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $ptotgrtotal1=0;
            $ptotgrtotal2=0;
            
            $query = "select distinct DATE_FORMAT(tgltrans,'%Y%m') as tgltrans, DATE_FORMAT(tgltrans,'%M %Y') as tgltrans2 from $tmp02 ";
            $query .= " ORDER BY DATE_FORMAT(tgltrans,'%Y%m')";
            $tampil1= mysqli_query($cnmy, $query);
            while ($row1= mysqli_fetch_array($tampil1)) {
                $pmytrans1=$row1['tgltrans'];
                $pmytrans2=$row1['tgltrans2'];
                
                echo "<tr>";
                echo "<td nowrap>&nbsp;</td>";
                echo "<td nowrap colspan='18' align='left'><b>$pmytrans2</b></td>";
                echo "</tr>";
                
                $pgrtotal1=0;
                $pgrtotal2=0;
                $no=1;
                $query = "select * from $tmp02 WHERE DATE_FORMAT(tgltrans,'%Y%m')='$pmytrans1' ";
                $query .= " ORDER BY tgltrans, divprodid, nama_kode, NAMA4";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pbrid=$row['brid'];

                    $ptglbr=$row['tgl'];
                    $ptgltrs=$row['tgltrans'];
                    $pdivisi=$row['divprodid'];
                    $pnmdivisi=$pdivisi;
                    if ($pdivisi=="PEACO") $pnmdivisi="PEACOK";
                    if ($pdivisi=="PIGEO") $pnmdivisi="PIGEON";

                    $pnmakun=$row['nama_kode'];
                    $pcoa=$row['COA4'];
                    $pnmcoa=$row['NAMA4'];
                    $ppnmcabang=$row['nama_cabang'];
                    $pidkaryawan=$row['karyawanid'];
                    $pnmkaryawan=$row['nama_karyawan'];
                    $piddokter=$row['dokterid'];
                    $pnmdokter=$row['nama_dokter'];
                    $pnoslip=$row['noslip'];
                    $pnmrealisasi=$row['realisasi1'];
                    $pjmlrp1=$row['jumlah'];
                    $pjmlrp2=$row['jumlah1'];
                    $ptgltrm=$row['tgltrm'];
                    $pket1=$row['aktivitas1'];
                    $pket2=$row['aktivitas2'];
                    $ptglrpsby=$row['tglrpsby'];
                    $pnodivisi=$row['nodivisi'];
                    $pnodivisi1=$row['nodivisi1'];
                    $pnodivisi2=$row['nodivisi2'];
                    $pnobuktibbk=$row['nobuktibbk'];



                    $pinnodivisi=$pnodivisi;

                    $pselisih=(double)$pjmlrp1-(double)$pjmlrp2;

                    $pgrtotal1=(double)$pgrtotal1+(double)$pjmlrp1;
                    $pgrtotal2=(double)$pgrtotal2+(double)$pjmlrp2;
                    
                    $ptotgrtotal1=(double)$ptotgrtotal1+(double)$pjmlrp1;
                    $ptotgrtotal2=(double)$ptotgrtotal2+(double)$pjmlrp2;

                    $pdokternm="";
                    if (!empty($pnmdokter)) $pdokternm=$piddokter." - ".$pnmdokter;


                    if ($ptgltrs=="0000-00-00") $ptgltrs="";
                    if ($ptgltrm=="0000-00-00") $ptgltrm="";
                    if ($ptglrpsby=="0000-00-00") $ptglrpsby="";

                    $ptglbr = date("d/m/Y", strtotime($ptglbr));
                    if (!empty($ptgltrs)) $ptgltrs = date("d/m/Y", strtotime($ptgltrs));
                    if (!empty($ptgltrm)) $ptgltrm = date("d/m/Y", strtotime($ptgltrm));
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
                    echo "<td nowrap>$pnmdivisi</td>";
                    echo "<td nowrap>$ppnmcabang</td>";
                    echo "<td nowrap>$pnmakun</td>";
                    echo "<td nowrap>$pcoa $pnmcoa</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap class='str'>$pdokternm</td>";
                    echo "<td nowrap class='str'>$pnoslip</td>";
                    echo "<td nowrap align='right'>$pjmlrp1</td>";
                    echo "<td nowrap>$pnmrealisasi</td>";
                    echo "<td nowrap>$ptgltrm</td>";
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
                echo "<td nowrap colspan='11' align='right'>T O T A L &nbsp; &nbsp; $pmytrans2 : </td>";
                echo "<td nowrap align='right'>$pgrtotal1</td>";
                echo "<td nowrap colspan='7'></td>";
                echo "</tr>";
            }
            
            $pselisih=(double)$ptotgrtotal1-(double)$ptotgrtotal2;

            $ptotgrtotal1=number_format($ptotgrtotal1,0,",",",");
            $ptotgrtotal2=number_format($ptotgrtotal2,0,",",",");
            $pselisih=number_format($pselisih,0,",",",");
                
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap colspan='11' align='center'>G R A N D &nbsp; &nbsp; &nbsp; &nbsp; T O T A L : </td>";
            echo "<td nowrap align='right'>$ptotgrtotal1</td>";
            echo "<td nowrap colspan='7'></td>";
            echo "</tr>";
                
                
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