<?PHP
    session_start();
    $etipertp = $_POST['e_tipe'];
    $tiperpt="";
    
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN GAJI SPG$tiperpt.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    include "config/fungsi_sql.php";
    $cnit=$cnmy;
?>
<html>
<head>
    <title>LAPORAN GAJI SPG<?PHP echo $tiperpt; ?></title>
<?PHP if ($_GET['ket']!="excel") { ?>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <link href="css/laporanbaru.css" rel="stylesheet">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
<?PHP } ?>
</head>

<body>
<?php
    $tglnow = date("d F Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Ym", strtotime($tgl01));
    
    $per1 = date("F Y", strtotime($tgl01));
    
    $ecabang = $_POST['icabangid_o'];
    
    
    $pttdmgr="";
    //$pttdmgr = getfield("select apvgbr3 as lcfields from dbimages.img_spg_gaji_br0 where DATE_FORMAT(periode,'%Y%m')='$periode1' AND IFNULL(apvgbr3,'')<>'' LIMIT 1");
    $gmrheight = "80px";
    
    
    $fcabang = "";
    
    $fperiode = " AND DATE_FORMAT(periode, '%Y%m') = '$periode1'";
    if (!empty($ecabang) AND ($ecabang <> "*")) $fcabang = " AND icabangid='$ecabang' ";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DGJSPGOTCL01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DGJSPGOTCL02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DGJSPGOTCL03_".$_SESSION['IDCARD']."_$now ";
    
    
    if ($etipertp=="1") {
        if (empty($ecabang)) {
            echo "cabang belum diisi...."; exit;
        }
        
        // file ini harus sama dengan md_m_spg_proses caridata.php
        include "module/laporan/mod_spg_lapgaji/caridataquery.php";
        
        $bulan = date("Ym", strtotime($tgl01));
        
        $tmp_tabel = CariDataSPGGajiTJ($periode1, $ecabang, "", "", $periode1);
        
        
        $query = "SELECT
            a.idbrspg,
            a.id_spg,
            b.nama,
            a.periode tglbr,
            a.icabangid,
            c.nama nama_cabang,
            b.areaid, a.id_zona, a.nama_zona, 
            d.nama nama_area,
            a.jml_harikerja harikerja,
            IFNULL(a.total,0)-IFNULL(a.insentif,0) total,
            CAST(null AS DECIMAL(20,2)) realisasi,
            a.keterangan,
            CAST(null AS DECIMAL(20,2)) insentif, a.gaji, a.keterangan hk, a.umakan rpmakan, a.tmakan makan, a.sewakendaraan sewa, a.pulsa, a.parkir, a.bbm,
            CAST(null AS DECIMAL(20,2)) rinsentif, CAST(null AS DECIMAL(20,2)) rgaji, CAST(null AS DECIMAL(20,2)) rmakan, CAST(null AS DECIMAL(20,2)) rsewa, CAST(null AS DECIMAL(20,2)) rpulsa, CAST(null AS DECIMAL(20,2)) rparkir, CAST(null AS DECIMAL(20,2)) rbbm,
            CAST(null AS DECIMAL(20,2)) sinsentif, CAST(null AS DECIMAL(20,2)) sgaji, CAST(null AS DECIMAL(20,2)) smakan, CAST(null AS DECIMAL(20,2)) ssewa, CAST(null AS DECIMAL(20,2)) spulsa, CAST(null AS DECIMAL(20,2)) sparkir, CAST(null AS DECIMAL(20,2)) sbbm 
            FROM
            $tmp_tabel a
            JOIN mkt.spg b ON a.id_spg = b.id_spg
            LEFT JOIN mkt.icabang_o c on a.icabangid=c.icabangid_o 
            LEFT JOIN mkt.iarea_o d on d.icabangid_o=c.icabangid_o AND d.areaid_o=b.areaid";

        $query ="CREATE TEMPORARY TABLE $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        mysqli_query($cnmy, "drop table $tmp_tabel");
        
    }else{
    
        $filapvpros=" AND IFNULL(apvtgl1,'')='' ";
        if ($etipertp=="2") $filapvpros=" AND IFNULL(apvtgl1,'')<>'' ";
        if ($etipertp=="3") $filapvpros=" AND (IFNULL(apvtgl2,'')<>'' OR IFNULL(apvtgl3,'')<>'') ";
        if ($etipertp=="4") $filapvpros=" AND IFNULL(apvtgl4,'')<>'' ";
        $query = "select * from dbmaster.t_spg_gaji_br0 WHERE IFNULL(stsnonaktif,'')<>'Y' $fperiode $fcabang $filapvpros";
        $query ="CREATE TEMPORARY TABLE $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    
        $query = "SELECT
            a.idbrspg,
            a.id_spg,
            b.nama,
            a.periode tglbr,
            a.icabangid,
            c.nama nama_cabang,
            b.areaid,
            a.id_zona, zn.nama_zona, 
            d.nama nama_area,
            a.jml_harikerja harikerja,
            a.total,
            a.realisasi,
            a.keterangan,
            a.total insentif, a.total gaji, a.keterangan hk, a.total rpmakan, a.total makan, a.total sewa, a.total pulsa, a.total parkir, a.total bbm,
            a.total rinsentif, a.total rgaji, a.total rmakan, a.total rsewa, a.total rpulsa, a.total rparkir, a.total rbbm,
            a.total sinsentif, a.total sgaji, a.total smakan, a.total ssewa, a.total spulsa, a.total sparkir, a.total sbbm
            FROM
            $tmp03 a
            JOIN mkt.spg b ON a.id_spg = b.id_spg
            LEFT JOIN mkt.icabang_o c on a.icabangid=c.icabangid_o 
            LEFT JOIN mkt.iarea_o d on d.icabangid_o=c.icabangid_o AND d.areaid_o=b.areaid LEFT JOIN dbmaster.t_zona zn ON a.id_zona=zn.id_zona";

        $query ="CREATE TEMPORARY TABLE $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        mysqli_query($cnmy, "UPDATE $tmp01 SET insentif=0, gaji=0, hk='', rpmakan=0, makan=0, sewa=0, pulsa=0, parkir=0, bbm=0");
        mysqli_query($cnmy, "UPDATE $tmp01 SET rinsentif=0, rgaji=0, rmakan=0, rsewa=0, rpulsa=0, rparkir=0, rbbm=0");//ralisasi
        mysqli_query($cnmy, "UPDATE $tmp01 SET sinsentif=0, sgaji=0, smakan=0, ssewa=0, spulsa=0, sparkir=0, sbbm=0");//selisih

        $query = "SELECT * FROM dbmaster.t_spg_gaji_br1 WHERE idbrspg IN (select distinct idbrspg FROM $tmp01)";
        $query ="CREATE TEMPORARY TABLE $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.insentif=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid IN ('01', '07') ),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.gaji=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='02'),0)");

        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rpmakan=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.makan=IFNULL((SELECT sum(rptotal) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 SET hk=CONCAT(harikerja,' x ', FORMAT(rpmakan,0,'ta_in'))");

        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.sewa=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='04'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.pulsa=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='05'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.parkir=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='06'),0)");
        
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.bbm=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='08'),0)");

        //realisasi
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rinsentif=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='01'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rgaji=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='02'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rmakan=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");

        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rsewa=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='04'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rpulsa=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='05'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rparkir=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='06'),0)");
        
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rbbm=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='08'),0)");

        mysqli_query($cnmy, "UPDATE $tmp01 SET sinsentif=insentif-rinsentif");
        mysqli_query($cnmy, "UPDATE $tmp01 SET sgaji=gaji-rgaji");
        mysqli_query($cnmy, "UPDATE $tmp01 SET smakan=makan-rmakan");
        mysqli_query($cnmy, "UPDATE $tmp01 SET ssewa=sewa-rsewa");
        mysqli_query($cnmy, "UPDATE $tmp01 SET spulsa=pulsa-rpulsa");
        mysqli_query($cnmy, "UPDATE $tmp01 SET sparkir=parkir-rparkir");
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET sbbm=bbm-rbbm");
    }
    
?>
    <center><h2><u></u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td></td><td><b>Laporan Gaji SPG Team OTC <?PHP echo ""; ?></b></td><td></td><td></td></tr>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center" rowspan="2">NO</th>
                    <th align="center" rowspan="2">NAMA</th>
                    <th align="center" rowspan="2">AREA</th>
                    <th align="center" rowspan="2">GAJI <br/>POKOK</th>
                    <th align="center" rowspan="2">JABATAN</th>
                    <th align="center" rowspan="2">SEWA <br/>KENDARAAN</th>
                    <th align="center" rowspan="2">PULSA</th>
                    <th align="center" rowspan="2">BBM</th>
                    <th align="center" rowspan="2">PARKIR</th>
                    <th align="center" rowspan="2">TOTAL GP & <br/>TUNJANGAN</th>
                    <th align="center" colspan="4">UANG MAKAN</th>
                    
                    <th align="center" rowspan="2">INSENTIF</th>
                    
                    <th align="center" rowspan="2"></th>
                    <th align="center" rowspan="2">GRAND TOTAL</th>
                    <th align="center" colspan="3">DATA REKENING</th>
                </tr>
                
                <tr>
                    <th align="center">ZONA</th>
                    <th align="center">HARI KERJA</th>
                    <th align="center">UANG<br/>MAKAN /<br/>HARI</th>
                    <th align="center">TOTAL UM</th>
                    
                    <th align="center">NAMA</th>
                    <th align="center">BANK</th>
                    <th align="center">NO. REKENING</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $gtotaljml=0;
                $gtotaltot=0;
                $gtotaltrans=0;

                $no=1;
                $query = "select distinct icabangid, nama_cabang from $tmp01 order by nama_cabang";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $picabang=$row['icabangid'];
                    $pnmcabang=$row['nama_cabang'];

                    
                    $query2 = "select * from $tmp01 WHERE icabangid='$picabang' order by nama_cabang, nama, id_spg";
                    $tampil2= mysqli_query($cnmy, $query2);
                    $jmlrow=mysqli_num_rows($tampil2);
                    $recno=1;
                    $ptotal=0;
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pidspg=$row2['id_spg'];
                        $pnmspg=$row2['nama'];
                        $phk=$row2['hk'];
                        
                        $pharikerja=$row2['harikerja'];
                        $prpmakan=$row2['rpmakan'];
                        $prpmakan=number_format($prpmakan,0,",",",");
                        
                        $pidarea=$row2['areaid'];
                        $pnmarea=$row2['nama_area'];
                        
                        $pnmzona=$row2['nama_zona'];
                        
                        $tottunjangan=(double)$row2['sewa']+(double)$row2['pulsa']+(double)$row2['bbm']+(double)$row2['parkir'];
                        $totgp_tunjangan=(double)$tottunjangan+(double)$row2['gaji'];
                        
                        $pinsentif=number_format($row2['insentif'],0,",",",");
                        $pgaji=number_format($row2['gaji'],0,",",",");
                        $pmakan=number_format($row2['makan'],0,",",",");
                        $psewa=number_format($row2['sewa'],0,",",",");
                        $ppulsa=number_format($row2['pulsa'],0,",",",");
                        $pparkir=number_format($row2['parkir'],0,",",",");
                        $pbbm=number_format($row2['bbm'],0,",",",");
                        $pjumlah=number_format($row2['total'],0,",",",");
                        
                        $totgp_tunjangan=number_format($totgp_tunjangan,0,",",",");
                        
                        $ptotal=$row2['total'];

                        $gtotaljml=$gtotaljml+$row2['total'];
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pnmspg</td>";
                        echo "<td nowrap>$pnmarea</td>";
                        
                        echo "<td nowrap align='right'>$pgaji</td>";
                        echo "<td nowrap align='right'></td>";
                        echo "<td nowrap align='right'>$psewa</td>";
                        echo "<td nowrap align='right'>$ppulsa</td>";
                        echo "<td nowrap align='right'>$pbbm</td>";
                        echo "<td nowrap align='right'>$pparkir</td>";
                        echo "<td nowrap align='right'><b>$totgp_tunjangan</b></td>";
                        
                        echo "<td nowrap align='center'>$pnmzona</td>";
                        echo "<td nowrap align='center'>$pharikerja</td>";
                        echo "<td nowrap align='center'>$prpmakan</td>";
                        echo "<td nowrap align='right'>$pmakan</td>";
                        
                        echo "<td nowrap align='right'>$pinsentif</td>";
                        
                        $jmltotal="";
                        $jmltransfer="";

                        
                        $gtotaltot=$gtotaltot+$ptotal;
                        $jmltotal=number_format($ptotal,0,",",",");
                        

                        echo "<td nowrap align='right'><b></b></td>";
                        echo "<td nowrap align='right'><b>$jmltotal</b></td>";
                        
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        
                        echo "</tr>";
                        
                        $recno++;
                        $no++;
                    }
                }
                echo "<tr>";
                echo "<td colspan='20'></td>";
                echo "</tr>";

                $gtotaljml=number_format($gtotaljml,0,",",",");
                $gtotaltot=number_format($gtotaltot,0,",",",");
                $gtotaltrans=number_format($gtotaltrans,0,",",",");

                echo "<tr>";
                echo "<td colspan='14' align='center'><b>GRAND TOTAL</b> </td>";
                echo "<td align='right'><b></b></td>";
                echo "<td align='right'><b>&nbsp;</b></td>";
                echo "<td align='right'><b>$gtotaltot</b></td>";
                
                echo "<td nowrap colspan='3'></td>";
                
                echo "</tr>";
                ?>
            </tbody>
        </table>
    <?PHP
    hapusdata:
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
        
        mysqli_close($cnmy);
        

            
    ?>
        
</body>
</html>
