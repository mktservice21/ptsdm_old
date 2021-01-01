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
        header("Content-Disposition: attachment; filename=Laporan_Service_kendaraan_CHC.xls");
    }
    
    include("config/koneksimysqli.php");
    
    $printdate= date("d/m/Y");
?>


<?PHP
    $tgl01 = $_POST['e_periode01'];
    $tgl02 = $_POST['e_periode02'];
    
    $pperiode1 = date("Ym", strtotime($tgl01));
    $pperiode2 = date("Ym", strtotime($tgl02));
    
    
    $myperiode1 = date("F Y", strtotime($tgl01));
    $myperiode2 = date("F Y", strtotime($tgl02));
    
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];

    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmprptbrsvckenetc00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmprptbrsvckenetc01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprptbrsvckenetc02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmprptbrsvckenetc03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmprptbrsvckenetc04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmprptbrsvckenetc05_".$puserid."_$now ";
    $tmp07 =" dbtemp.tmprptbrsvckenetc07_".$puserid."_$now ";
    $tmp11 =" dbtemp.tmprptbrsvckenetc11_".$puserid."_$now ";
    
    
    
    
    $query = "select distinct b.idinput, b.divisi, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
            . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' AND a.kodeinput IN ('S', 'O') "
            . " AND DATE_FORMAT(b.tgl,'%Y%m')>='$pperiode1'";
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
            
            
    $query = "select a.karyawanid, a.tglawal, a.nopol, b.merk, b.jenis, c.nama_jenis from dbmaster.t_kendaraan_pemakai a "
            . " LEFT JOIN dbmaster.t_kendaraan b on a.nopol=b.nopol "
            . " LEFT JOIN dbmaster.t_kendaraan_jenis c on b.jenis=c.jenis";
    $query = "create TEMPORARY table $tmp07 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

        $query = "ALTER table $tmp07 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        $query = "CREATE UNIQUE INDEX `unx1` ON $tmp07 (noidauto)";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
    $query = "select idservice, kode, nobrid, tgl, divisi, "
            . " karyawanid, icabangid, areaid, icabangid_o, areaid_o, "
            . " nopol, tglservice, km, jumlah, keterangan, COA4 "
            . " from dbmaster.t_service_kendaraan WHERE IFNULL(stsnonaktif,'') <> 'Y' AND "
            . " DATE_FORMAT(tglservice,'%Y%m') BETWEEN '$pperiode1' AND '$pperiode2'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp01 ADD COLUMN nobuktibbk VARCHAR(20), ADD COLUMN jenis VARCHAR(50), ADD COLUMN nama_merk VARCHAR(100)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
            
    $query = "select distinct karyawanid, DATE_FORMAT(tglawal,'%Y%m') bulan, nopol, nama_jenis, merk FROM $tmp07 order by 1,2";
    $tampil=mysqli_query($cnmy, $query);
    while ($nr= mysqli_fetch_array($tampil)) {
        $pikryid=$nr['karyawanid'];
        $pibln=$nr['bulan'];
        $pinopol=$nr['nopol'];
        $pidjenis=$nr['nama_jenis'];
        $pnmmerk=$nr['merk'];
        if (!empty($pinopol)) {

            $query = "UPDATE $tmp01 SET nopol='$pinopol', jenis='$pidjenis', nama_merk='$pnmmerk' WHERE DATE_FORMAT(tglservice,'%Y%m')>='$pibln' AND karyawanid='$pikryid'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
    }
        
        
    $query = "select a.*, b.jabatanid, h.nama nama_jabatan, IFNULL(g.nama,'') nama_brid, b.nama nama_karyawan, c.nama nama_cabang, d.nama nama_area, "
            . " e.nama nmcabotc, f.nama nmareaotc, i.NAMA4 from $tmp01 a "
            . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang c on a.icabangid=c.iCabangId "
            . " LEFT JOIN MKT.iarea d on a.areaid=d.areaId AND a.icabangid=d.iCabangId "
            . " LEFT JOIN MKT.icabang_o e on a.icabangid_o=e.icabangid_o "
            . " LEFT JOIN MKT.iarea_o f on a.areaid_o=f.areaid_o AND a.icabangid_o=f.icabangid_o "
            . " LEFT JOIN dbmaster.t_brid g ON a.nobrid=g.nobrid "
            . " LEFT JOIN hrd.jabatan h on b.jabatanid=h.jabatanid "
            . " LEFT JOIN dbmaster.coa_level4 i on a.COA4=i.COA4";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "UPDATE $tmp02 SET icabangid=icabangid_o, areaid=areaid_o, nama_cabang=nmcabotc, nama_area=nmareaotc WHERE divisi='OTC'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "ALTER table $tmp02 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(20), ADD COLUMN tgltrans DATE";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp02 a JOIN (select distinct pilih, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp00) b on a.idservice=b.bridinput "
            . " SET a.nodivisi=b.nodivisi, a.idinput=b.idinput";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //isi tanggal transfer no bukti bbk bobukti
    $query = "UPDATE $tmp02 a JOIN $tmp11 b on a.idinput=b.idinput SET a.nobuktibbk=b.nobukti, a.tgltrans=b.tanggal";//a.nobukti=b.nobukti, a.tgltrans=b.tanggal
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
        
?>
<HTML>
<HEAD>
    <title>Laporan Service Kendaraan CHC</title>
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

    <center><div class='h1judul'>Laporan Service Kendaraan CHC</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <?PHP
            echo "<tr> <td>Periode</td> <td>:</td> <td><b>$myperiode1 s/d. $myperiode2</b></td> </tr>";
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
            <th align="center">Tgl. Service</th>
            <th align="center">Karyawan</th>
            <th align="center">Cabang</th>
            <th align="center">Nopol</th>
            <th align="center">Jenis/Merk</th>
            <th align="center">Perkiraan</th>
            <th align="center">Jumlah</th>
            <th align="center">Keterangan</th>
            <th align="center">Nodivisi</th>
            <th align="center">NOBBK</th>
            <th align="center">Tgl. Trans</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $pgrtotal1=0;
            $pgrtotal2=0;
            $no=1;
            $query = "select * from $tmp02 ";
            $query .= " ORDER BY nama_karyawan, nama_cabang, NAMA4";
            $tampil= mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pbrid=$row['idservice'];
                $pnmkaryawan=$row['nama_karyawan'];
                $ppnmcabang=$row['nama_cabang'];
                
                $pnopol = $row['nopol'];
                $pnmjenis = $row['jenis'];
                $pnmmerk = $row['nama_merk'];
                
                $pcoa=$row['COA4'];
                $pnmcoa=$row['NAMA4'];
                
                $ptglbr=$row['tglservice'];
                $pjmlrp1=$row['jumlah'];
                $pket1=$row['keterangan'];
                $pnodivisi=$row['nodivisi'];
                $pnobuktibbk=$row['nobuktibbk'];
                $ptgltrans=$row['tgltrans'];
                
                
                
                $pgrtotal1=(double)$pgrtotal1+(double)$pjmlrp1;
                
                if ($ptgltrans=="0000-00-00") $ptgltrans="";
                
                $ptglbr = date("d/m/Y", strtotime($ptglbr));
                if (!empty($ptgltrans)) $ptgltrans = date("d/m/Y", strtotime($ptgltrans));
                
                $pjmlrp1=number_format($pjmlrp1,0,",",",");
                
                
                $pketerangan=$pket1;
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap class='str'>$pbrid</td>";
                echo "<td nowrap>$ptglbr</td>";
                echo "<td nowrap>$pnmkaryawan</td>";
                echo "<td nowrap>$ppnmcabang</td>";
                echo "<td nowrap>$pnopol</td>";
                echo "<td nowrap>$pnopol $pnmmerk</td>";
                echo "<td nowrap>$pcoa $pnmcoa</td>";
                echo "<td nowrap align='right'>$pjmlrp1</td>";
                echo "<td >$pketerangan</td>";
                echo "<td nowrap>$pnodivisi</td>";
                echo "<td nowrap>$pnobuktibbk</td>";
                echo "<td nowrap>$ptgltrans</td>";
                echo "</tr>";
                
                
                $no++;
            };
            
            $pgrtotal1=number_format($pgrtotal1,0,",",",");
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap colspan='8' align='center'>T O T A L : </td>";
            echo "<td nowrap align='right'>$pgrtotal1</td>";
            echo "<td nowrap colspan='4'></td>";
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
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp07");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp11");
    mysqli_close($cnmy);
?>