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
        header("Content-Disposition: attachment; filename=Laporan_Klaim.xls");
    }
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    include "config/fungsi_sql.php";
    
    $printdate= date("d/m/Y");
?>


<?PHP
    $tgl01 = $_POST['e_periode01'];
    $tgl02 = $_POST['e_periode02'];
    $ptypetgl = $_POST['cb_tgltipe'];
    $prptby = $_POST['cb_reportby'];
    
    $pperiode1 = date("Y-m-d", strtotime($tgl01));
    $pperiode2 = date("Y-m-d", strtotime($tgl02));
    
    $pstsperiode="Transfer";
    if ($ptypetgl=="2") $pstsperiode="Input/Pengajuan";
    
    $myperiode1 = date("d/m/Y", strtotime($tgl01));
    $myperiode2 = date("d/m/Y", strtotime($tgl02));
    
    
    $filterdist=('');
    if (!empty($_POST['chkbox_dist'])){
        $filterdist=$_POST['chkbox_dist'];
        $filterdist=PilCekBox($filterdist);
    }
    $filterdist=" and distid in $filterdist ";
    
    $pgroupid=$_SESSION['GROUP'];
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];

    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmprptbrklaim00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmprptbrklaim01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprptbrklaim02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmprptbrklaim03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmprptbrklaim04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmprptbrklaim05_".$puserid."_$now ";
    $tmp11 =" dbtemp.tmprptbrklaim11_".$puserid."_$now ";
    
    
    $query = "select distinct b.idinput, b.divisi, b.nomor, b.nodivisi, a.kodeinput, a.bridinput, b.pilih, b.kodeid, b.subkode from dbmaster.t_suratdana_br1 a "
            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput WHERE "
            . " IFNULL(b.stsnonaktif,'')<>'Y' AND IFNULL(b.nodivisi,'')<>'' AND a.kodeinput IN ('E') "
            . " AND b.tgl>='$pperiode1'";
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
    
            
    $query = "select DIVISI divprodid, tgl, tgltrans, distid, klaimId, COA4, karyawanid, noslip, "
            . " aktivitas1, realisasi1 nmrealisasi, jumlah, dpp, ppn_rp, pph_rp, tgl_fp, pengajuan divpengajuan,"
            . " region, bulan, periode1, periode2 "
            . " FROM hrd.klaim WHERE 1=1 $filterdist AND"
            . " klaimId not in (SELECT DISTINCT ifnull(klaimId,'') from hrd.klaim_reject) AND ";
    if ($ptypetgl=="2") $query .= " tgl BETWEEN '$pperiode1' AND '$pperiode2' ";
    else $query .= " tgltrans BETWEEN '$pperiode1' AND '$pperiode2' ";
    if ($pgroupid=="40" OR $pgroupid=="43") {
        $query.=" AND (user1='$puserid' OR user1='$picardid' OR karyawanid='$picardid') ";
    }
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp01 ADD COLUMN nobuktibbk VARCHAR(20), ADD COLUMN istsapv VARCHAR(1)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.klaimid as klaimid, a.tgl_atasan4, a.tgl_atasan5 FROM dbttd.klaim_ttd as a JOIN $tmp01 as b on "
            . " a.klaimid=b.klaimid";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN (select klaimid, tgl_atasan4 from $tmp04 WHERE IFNULL(tgl_atasan4,'')<>'' "
            . " AND IFNULL(tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') as b on a.klaimid=b.klaimid SET a.istsapv='A'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN (select klaimid, tgl_atasan5 from $tmp04 WHERE IFNULL(tgl_atasan5,'')<>'' "
            . " AND IFNULL(tgl_atasan5,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') as b on a.klaimid=b.klaimid SET a.istsapv='B'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "select a.*, b.nama nama_karyawan, c.ikotaid, c.nama namadist "
            . " FROM $tmp01 a "
            . " LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanId"
            . " LEFT JOIN MKT.distrib0 c on a.distid=c.distid";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "ALTER table $tmp02 ADD COLUMN idinput BIGINT(20), ADD COLUMN nodivisi VARCHAR(20), ADD COLUMN idinput1 BIGINT(20), ADD COLUMN nodivisi1 VARCHAR(20), ADD COLUMN idinput2 BIGINT(20), ADD COLUMN nodivisi2 VARCHAR(20), ADD COLUMN icabangid VARCHAR(10), ADD COLUMN nama_cabang VARCHAR(100)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "ALTER table $tmp02 ADD COLUMN nomor VARCHAR(20), ADD COLUMN nomor1 VARCHAR(20), ADD COLUMN nomor2 VARCHAR(20)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    $query = "CREATE INDEX `norm1` ON $tmp02 (klaimId, ikotaid, icabangid, idinput)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp02 a JOIN MKT.icabang b ON a.ikotaid=b.ikotaid SET a.icabangid=b.icabangid, a.nama_cabang=b.nama";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            
        
    $query = "UPDATE $tmp02 a JOIN (select distinct pilih, nomor, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp00 WHERE IFNULL(pilih,'')<>'Y') b on a.klaimId=b.bridinput "
            . " SET a.nodivisi1=b.nodivisi, a.idinput1=b.idinput, a.nomor1=b.nomor";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 a JOIN (select distinct pilih, nomor, nodivisi, idinput, bridinput, kodeid, subkode FROM $tmp00 WHERE IFNULL(pilih,'')='Y') b on a.klaimId=b.bridinput "
            . " SET a.nodivisi2=b.nodivisi, a.idinput2=b.idinput, a.nomor2=b.nomor";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET nodivisi=nodivisi2, idinput=idinput2, nomor=nomor2";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp02 SET nomor=nomor1, nodivisi=nodivisi1, idinput=idinput1 WHERE IFNULL(nodivisi,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    //isi tanggal transfer no bukti bbk bobukti
    $query = "UPDATE $tmp02 a JOIN $tmp11 b on a.idinput=b.idinput SET a.nobuktibbk=b.nobukti";//a.nobukti=b.nobukti, a.tgltrans=b.tanggal
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    

    $query ="UPDATE $tmp02 SET divpengajuan='CAN' WHERE IFNULL(divpengajuan,'') NOT IN ('PIGEO', 'PEACO', 'EAGLE', 'OTC', 'OTHER', 'OTHERS')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query ="UPDATE $tmp02 SET divprodid=divpengajuan WHERE IFNULL(divpengajuan,'')<>''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query ="UPDATE $tmp02 SET COA4='751-31' WHERE IFNULL(divprodid,'')='EAGLE'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query ="UPDATE $tmp02 SET COA4='752-31' WHERE IFNULL(divprodid,'')='PIGEO'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query ="UPDATE $tmp02 SET COA4='753-31' WHERE IFNULL(divprodid,'')='PEACO'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query ="UPDATE $tmp02 SET COA4='754-31' WHERE IFNULL(divprodid,'')='OTC'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query ="UPDATE $tmp02 SET COA4='755-31' WHERE IFNULL(divprodid,'')='CAN'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "select a.*, b.NAMA4 from $tmp02 a LEFT JOIN dbmaster.coa_level4 b on a.coa4=b.coa4";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($prptby=="S") {
        $query = "UPDATE $tmp03 SET bulan=tgl WHERE ( IFNULL(bulan,'')='' OR IFNULL(bulan,'0000-00-00')='0000-00-00' ) ";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select divpengajuan as divprodid, distid, namadist, nmrealisasi, bulan, nomor, nodivisi, sum(jumlah) as jumlah from $tmp03"
                . " GROUP BY 1,2,3,4,5,6,7";
        $query = "create TEMPORARY table $tmp05 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
        
?>
<HTML>
<HEAD>
    <title>Laporan Budget Klaim</title>
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

    <center><div class='h1judul'>Laporan Budget Klaim</div></center>
    
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
    
    
    <?PHP if ($prptby=="S") { ?>
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">No.</th>
                <th align="center">Divisi</th>
                <th align="center">Distributor</th>
                <th align="center">Realisasi</th>
                <th align="center">Bulan</th>
                <th align="center">No SPD</th>
                <th align="center">No Divisi</th>
                <th align="center">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $pgrtotal1=0;
                $no=1;
                $query = "select distinct divprodid from $tmp05 ";
                $query .= " ORDER BY divprodid";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pdivisi=$row['divprodid'];
                    $pnmdivisi=$pdivisi;
                    if ($pdivisi=="CAN") $pnmdivisi="ETHICAL";
                    if ($pdivisi=="PIGEO") $pnmdivisi="PIGEON";
                    if ($pdivisi=="PEACO") $pnmdivisi="PEACOCK";
                    if ($pdivisi=="OTC") $pnmdivisi="CHC";
                    
                    $query = "select distinct divprodid, distid, namadist from $tmp05 WHERE divprodid='$pdivisi' ";
                    $query .= " ORDER BY namadist";
                    $tampil2= mysqli_query($cnmy, $query);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pdistid=$row2['distid'];
                        $pdistnm=$row2['namadist'];
                        
                        $query = "select * from $tmp05 WHERE divprodid='$pdivisi' AND distid='$pdistid' ";
                        $query .= " ORDER BY divprodid, namadist";
                        $tampil3= mysqli_query($cnmy, $query);
                        while ($row3= mysqli_fetch_array($tampil3)) {
                            
                            $pbln=$row3['bulan'];
                            $pnomspd=$row3['nomor'];
                            $pnodivisi=$row3['nodivisi'];
                            $pnmrealisasi=$row3['nmrealisasi'];
                            $pjmlrp1=$row3['jumlah'];
                            
                            $pbulan="";
                            if ($pbln=="0000-00-00") $pbln="";
                            if (!empty($pbln)) $pbulan=date("F Y", strtotime($pbln));;
                            
                            
                            $pgrtotal1=(DOUBLE)$pgrtotal1+(DOUBLE)$pjmlrp1;
                            
                            $pjmlrp1=number_format($pjmlrp1,0,",",",");
                            
                            
                            echo "<tr>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pnmdivisi</td>";
                            echo "<td nowrap>$pdistnm</td>";
                            echo "<td nowrap>$pnmrealisasi</td>";
                            echo "<td nowrap>$pbulan</td>";
                            echo "<td nowrap class='str'>$pnomspd</td>";
                            echo "<td nowrap class='str'>$pnodivisi</td>";
                            echo "<td nowrap align='right'>$pjmlrp1</td>";
                            echo "</tr>";
                            
                            $no++;
                        }
                       
                        
                    }
                    
                    
                }
                
                $pgrtotal1=number_format($pgrtotal1,0,",",",");

                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap class='str'></td>";
                echo "<td nowrap class='str'><b>GRAND TOTAL : </b></td>";
                echo "<td nowrap align='right'><b>$pgrtotal1</b></td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
        </table>
    <?PHP }else{ ?>
    
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">No.</th>
                <th align="center">ID</th>
                <th align="center">Tanggal</th>
                <th align="center">Tgl. Trans</th>
                <th align="center">Bulan</th>
                <th align="center">Divisi</th>
                <th align="center">COA</th>
                <th align="center">Perkiraan</th>
                <th align="center">Yg. Membuat</th>
                <th align="center">Distributor</th>
                <th align="center">No. Slip</th>
                <th align="center">Jumlah</th>
                <th align="center">Realisasi</th>
                <th align="center">Keterangan</th>
                <th align="center">No SPD</th>
                <th align="center">No Divisi</th>
                <th align="center">BBK</th>
                <th align="center">Status</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $pgrtotal1=0;
                $no=1;
                $query = "select * from $tmp03 ";
                $query .= " ORDER BY divprodid, NAMA4, namadist";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pbrid=$row['klaimId'];

                    $ptglbr=$row['tgl'];
                    $ptgltrs=$row['tgltrans'];
                    $pbln=$row['bulan'];
                    $pdivisi=$row['divprodid'];
                    $pnmdivisi=$pdivisi;

                    if ($pdivisi=="CAN") $pnmdivisi="ETHICAL";
                    if ($pdivisi=="PIGEO") $pnmdivisi="PIGEON";
                    if ($pdivisi=="PEACO") $pnmdivisi="PEACOCK";
                    if ($pdivisi=="OTC") $pnmdivisi="CHC";
                    
                    $pcoa=$row['COA4'];
                    $pnmcoa=$row['NAMA4'];
                    $ppnmadist=$row['namadist'];
                    $pidkaryawan=$row['karyawanid'];
                    $pnmkaryawan=$row['nama_karyawan'];
                    $pnoslip=$row['noslip'];
                    $pnmrealisasi=$row['nmrealisasi'];
                    $pjmlrp1=$row['jumlah'];
                    $pket1=$row['aktivitas1'];
                    $pnodivisi=$row['nodivisi'];
                    $pnodivisi1=$row['nodivisi1'];
                    $pnodivisi2=$row['nodivisi2'];
                    $pnobuktibbk=$row['nobuktibbk'];
                    $pnomspd=$row['nomor'];
                    $pistatus=$row['istsapv'];

                    $psudahapprove="";
                    if ($pistatus=="A") $psudahapprove="Apv. GSM";
                    elseif ($pistatus=="B") $psudahapprove="Apv. Dir. MKT.";
                    if (empty($psudahapprove)) $psudahapprove="";

                    $pinnodivisi=$pnodivisi;

                    $pbulan="";
                    if ($pbln=="0000-00-00") $pbln="";
                    if (!empty($pbln)) $pbulan=date("F Y", strtotime($pbln));;

                    $pgrtotal1=(double)$pgrtotal1+(double)$pjmlrp1;

                    if ($ptgltrs=="0000-00-00") $ptgltrs="";

                    $ptglbr = date("d/m/Y", strtotime($ptglbr));
                    if (!empty($ptgltrs)) $ptgltrs = date("d/m/Y", strtotime($ptgltrs));

                    $pjmlrp1=number_format($pjmlrp1,0,",",",");


                    $pketerangan=$pket1;

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap class='str'>$pbrid</td>";
                    echo "<td nowrap>$ptglbr</td>";
                    echo "<td nowrap>$ptgltrs</td>";
                    echo "<td nowrap>$pbulan</td>";
                    echo "<td nowrap>$pnmdivisi</td>";
                    echo "<td nowrap>$pcoa</td>";
                    echo "<td nowrap>$pnmcoa</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap class='str'>$ppnmadist</td>";
                    echo "<td nowrap class='str'>$pnoslip</td>";
                    echo "<td nowrap align='right'>$pjmlrp1</td>";
                    echo "<td nowrap>$pnmrealisasi</td>";
                    echo "<td >$pketerangan</td>";
                    echo "<td nowrap>$pnomspd</td>";
                    echo "<td nowrap>$pinnodivisi</td>";
                    echo "<td nowrap>$pnobuktibbk</td>";
                    echo "<td nowrap>$psudahapprove</td>";
                    echo "</tr>";


                    $no++;
                }

                $pgrtotal1=number_format($pgrtotal1,0,",",",");

                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap colspan='11' align='center'>T O T A L : </td>";
                echo "<td nowrap align='right'>$pgrtotal1</td>";
                echo "<td nowrap colspan='6'></td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
    <?PHP } ?>
</div>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    
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