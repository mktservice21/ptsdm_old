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
        header("Content-Disposition: attachment; filename=Laporan_Budget_Request_By_Leader_HO.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/fungsi_sql.php");
    
    $printdate= date("d/m/Y");
    
    $pmygroupid=$_SESSION['GROUP'];
?>


<?PHP
    $idkaryawan = $_POST['cb_karyawanid'];
    $tgl01 = $_POST['e_periode01'];
    $tgl02 = $_POST['e_periode02'];
    $pdivisipil = $_POST['cb_divisi'];
    $ptipepil = $_POST['e_pilihtipe'];
    
    $pincbawahan="";
    if (isset($_POST['chk_incbawahan'])) $pincbawahan = $_POST['chk_incbawahan'];
    if ($pincbawahan!="Y") $pincbawahan="";
    
    $filterakun ="('A', 'F', 'G')";
    
    $pperiode1 = date("Y-m-d", strtotime($tgl01));
    $pperiode2 = date("Y-m-d", strtotime($tgl02));
    $periode_thn = date("Y", strtotime($tgl01));
    
    $myperiode1 = date("F Y", strtotime($tgl01));
    $myperiode2 = date("F Y", strtotime($tgl02));
    
    $pchkumakan="";
    $pchkkesehatan="";
    
    if (isset($_POST['chk_umakank'])) $pchkumakan = $_POST['chk_umakank'];
    if (isset($_POST['chk_kesehatan'])) $pchkkesehatan = $_POST['chk_kesehatan'];
    
    
    $pnamakaryawan= getfield("select nama as lcfields from hrd.karyawan WHERE karyawanid='$idkaryawan'");
    
    $filterkaryawan="'".$idkaryawan."',";
    if ($pincbawahan=="Y") {
        $query = "select karyawanId as karyawanid from hrd.karyawan WHERE (atasanId='$idkaryawan' OR atasanId2='$idkaryawan')";
        $tampilk= mysqli_query($cnmy, $query);
        while ($rowk= mysqli_fetch_array($tampilk)) {
            $pkrypilih=$rowk['karyawanid'];

            $filterkaryawan .="'".$pkrypilih."',";
        }
    }
    
    if (!empty($filterkaryawan)) $filterkaryawan="(".substr($filterkaryawan, 0, -1).")";
    
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];

    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmprptbretc00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmprptbretc01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprptbretc02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmprptbretc03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmprptbretc04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmprptbretc05_".$puserid."_$now ";
    $tmp11 =" dbtemp.tmprptbretc11_".$puserid."_$now ";
    
    
    
    $query = "select kodeinput, idkodeinput as brid, karyawanid, nama_karyawan, tglinput as tgl, tgltrans, divisi as divprodid, icabangid, nama_cabang, 
        nkodeid as kodeid, nkodeid_nama as nama_kode, nobrid_r, nobrid_n, noslip, nmrealisasi, keterangan as aktivitas1, nmrealisasi as aktivitas2, 
        coa as COA4, nama_coa as NAMA4, 
        idinput_pd, nodivisi, nodivisi1, nodivisi2, nobukti as nobuktibbk, dokterid, dokter_nama as nama_dokter, kredit as jumlah 
        from dbmaster.t_proses_data_bm WHERE 
        tgltarikan BETWEEN '$pperiode1' AND '$pperiode2' AND IFNULL(kredit,0)<>0 ";
    if ($pmygroupid=="47") {
        if ($pchkumakan=="Y" OR $pchkkesehatan=="Y") {
            $query .=" AND ( karyawanid IN $filterkaryawan ";
            if ($pchkumakan=="Y" AND $pchkkesehatan=="Y") {
                $query .=" OR ( IFNULL(nobrid_r,'') IN ('04', '10', '11', '16', '17', '18', '19') AND kodeinput IN ('F', 'G') AND IFNULL(divisi,'')='HO' ) ";
            }elseif ($pchkumakan=="Y" AND $pchkkesehatan<>"Y") {
                $query .=" OR ( IFNULL(nobrid_r,'') IN ('04') AND kodeinput IN ('F', 'G') AND IFNULL(divisi,'')='HO' ) ";
            }elseif ($pchkumakan<>"Y" AND $pchkkesehatan=="Y") {
                $query .=" OR ( IFNULL(nobrid_r,'') IN ('10', '11', '16', '17', '18', '19') AND kodeinput IN ('F', 'G') AND IFNULL(divisi,'')='HO' ) ";
            }
                
            $query .=" ) ";
        }else{
            $query .=" AND karyawanid IN $filterkaryawan ";
        }
    }else{
        $query .=" AND karyawanid IN $filterkaryawan ";
    }
    $query .=" AND kodeinput IN $filterakun ";
    if (!empty($ptipepil)) {
        $query .=" AND kodeinput = '$ptipepil'";
    }
    if (!empty($pdivisipil)) {
        $query .=" AND divisi = '$pdivisipil'";
    }
    $query .=" AND IFNULL(ishare,'')<>'Y' ";
    //echo $query;
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    if ($pchkumakan=="Y") {
    }else{
        $query = "DELETE FROM $tmp02 WHERE IFNULL(nobrid_r,'')='04' AND kodeinput IN ('F', 'G')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
    }
    
    if ($pchkkesehatan=="Y") {
    }else{
        $query = "DELETE FROM $tmp02 WHERE IFNULL(nobrid_r,'') IN ('10', '11', '16', '17', '18', '19') AND kodeinput IN ('F', 'G')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }        
    }
    
    $query = "UPDATE $tmp02 SET kodeid=nobrid_r, nama_kode=nobrid_n, nmrealisasi='' WHERE kodeinput IN ('F', 'G')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid SET a.nama_karyawan=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp02 as a JOIN MKT.icabang as b on a.icabangid=b.icabangid SET a.nama_cabang=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp02 as a JOIN dbmaster.coa_level4 as b on a.COA4=b.COA4 SET a.NAMA4=b.NAMA4";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "UPDATE $tmp02 as a JOIN dbmaster.t_suratdana_bank as b on a.nodivisi=b.nodivisi AND a.idinput_pd=b.idinput "
            . " SET a.nobuktibbk=b.nobukti WHERE a.kodeinput IN ('F', 'G') AND b.subkode NOT IN ('29') AND b.stsinput='K'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN dbmaster.t_suratdana_bank as b on a.nobuktibbk=b.nobukti AND a.nodivisi=b.nodivisi "
            . " SET a.tgltrans=b.tanggal WHERE a.kodeinput IN ('F', 'G') AND b.subkode NOT IN ('29') AND b.stsinput='K'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $ptanggalprosesnya="";
    $query = "select tanggal_proses from dbmaster.t_proses_data_bm_date WHERE tahun='$periode_thn'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((DOUBLE)$ketemu>0) {
        $nt= mysqli_fetch_array($tampil);
        $ptanggalprosesnya=$nt['tanggal_proses'];
    }
    
?>
<HTML>
<HEAD>
    <title>Laporan Budget Request By Leader HO</title>
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

    <center><div class='h1judul'>Laporan Budget Request By Leader HO</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <?PHP
            echo "<tr> <td>Karyawan</td> <td>:</td> <td><b>$pnamakaryawan</b></td> </tr>";
            echo "<tr> <td>Periode </td> <td>:</td> <td><b>$myperiode1 s/d. $myperiode2</b></td> </tr>";
            echo "<tr class='miring text2'> <td>view date</td> <td>:</td> <td>$printdate</td> </tr>";
            ?>
            <tr class='miring text2'><td>Proses Terakhir</td><td>:</td><td><?PHP echo "$ptanggalprosesnya"; ?></td></tr>
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
            <th align="center">COA</th>
            <th align="center">Perkiraan</th>
            <th align="center">Yg. Membuat</th>
            <th align="center">No. Slip</th>
            <th align="center">Jumlah</th>
            <th align="center">Realisasi</th>
            <th align="center">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $pgrtotal1=0;
            $pgrtotal2=0;
            $no=1;
            $query = "select * from $tmp02 ";
            $query .= " ORDER BY divprodid, nama_kode, NAMA4";
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
                $pnmrealisasi=$row['nmrealisasi'];
                $pjmlrp1=$row['jumlah'];
                $pket1=$row['aktivitas1'];
                $pket2=$row['aktivitas2'];
                $pnodivisi=$row['nodivisi'];
                $pnodivisi1=$row['nodivisi1'];
                $pnodivisi2=$row['nodivisi2'];
                $pnobuktibbk=$row['nobuktibbk'];
                
                $pinnodivisi=$pnodivisi;
                
                $pgrtotal1=(double)$pgrtotal1+(double)$pjmlrp1;
                
                $pdokternm="";
                if (!empty($pnmdokter)) $pdokternm=$piddokter." - ".$pnmdokter;
                
                
                if ($ptgltrs=="0000-00-00") $ptgltrs="";
                
                $ptglbr = date("d/m/Y", strtotime($ptglbr));
                if (!empty($ptgltrs)) $ptgltrs = date("d/m/Y", strtotime($ptgltrs));
                
                $pjmlrp1=number_format($pjmlrp1,0,",",",");
                
                
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
                echo "<td nowrap>$pcoa</td>";
                echo "<td nowrap>$pnmcoa</td>";
                echo "<td nowrap>$pnmkaryawan</td>";
                echo "<td nowrap class='str'>$pnoslip</td>";
                echo "<td nowrap align='right'>$pjmlrp1</td>";
                echo "<td nowrap>$pnmrealisasi</td>";
                echo "<td >$pketerangan</td>";
                echo "</tr>";
                
                
                $no++;
            }
            
            $pgrtotal1=number_format($pgrtotal1,0,",",",");
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap colspan='10' align='center'>T O T A L : </td>";
            echo "<td nowrap align='right'>$pgrtotal1</td>";
            echo "<td nowrap colspan='2'></td>";
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