<?PHP
    session_start();
    $etipertp = $_POST['e_tipe'];
    $tiperpt="";
    if ($etipertp==2) $tiperpt=" CABANG";
    if ($etipertp==3) $tiperpt=" COA";
    
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP GAJI SPG$tiperpt.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    include "config/fungsi_sql.php";
    $cnit=$cnmy;
?>
<html>
<head>
    <title>REKAP GAJI SPG<?PHP echo $tiperpt; ?></title>
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
    
    $fperiode = " AND DATE_FORMAT(a.periode, '%Y%m') = '$periode1'";
    if (!empty($ecabang) AND ($ecabang <> "*")) {
        if ($ecabang=="JKT_MT") {
            $fcabang = " AND a.icabangid='0000000007' AND a.alokid='001' ";
        }elseif ($ecabang=="JKT_RETAIL") {
            $fcabang = " AND a.icabangid='0000000007' AND a.alokid='002' ";
        }else{
            $fcabang = " AND a.icabangid='$ecabang' ";
        }
        
    }
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DGJSPGOTC01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DGJSPGOTC02_".$_SESSION['IDCARD']."_$now ";
    $tmp04 =" dbtemp.DGJSPGOTC04_".$_SESSION['IDCARD']."_$now ";
    
    $query = "SELECT * FROM dbmaster.t_spg_data WHERE DATE_FORMAT(periode,'%Y%m')='$periode1'";
    $query ="CREATE TABLE $tmp04 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if ($etipertp==3) {
        
        $query = "SELECT
            a.periode tglbr,
            a.icabangid,
            c.nama nama_cabang, a.alokid, 
            br.coa4,
            d.NAMA4 nama4,
            IFNULL(sum(br.rptotal),0) rptotal,
            IFNULL(sum(br.realisasirp),0) realisasirp, sum(br.rptotal) selisih, 
            sum(br.rptotal) lebihkurang, sum(br.rptotal) insentif, sum(br.rptotal) gaji, sum(br.rptotal) makan, sum(br.rptotal) sewa, sum(br.rptotal) pulsa, sum(br.rptotal) parkir, sum(br.rptotal) bbm,
            sum(br.rptotal) rlebihkurang, sum(br.rptotal) rinsentif, sum(br.rptotal) rgaji, sum(br.rptotal) rmakan, sum(br.rptotal) rsewa, sum(br.rptotal) rpulsa, sum(br.rptotal) rparkir, sum(br.rptotal) rbbm,
            sum(br.rptotal) slebihkurang, sum(br.rptotal) sinsentif, sum(br.rptotal) sgaji, sum(br.rptotal) smakan, sum(br.rptotal) ssewa, sum(br.rptotal) spulsa, sum(br.rptotal) sparkir, sum(br.rptotal) sbbm
            FROM
            dbmaster.t_spg_gaji_br1 br
            JOIN dbmaster.t_spg_gaji_br0 a ON br.idbrspg = a.idbrspg
            JOIN $tmp04 b ON a.id_spg = b.id_spg
            LEFT JOIN mkt.icabang_o c ON a.icabangid = c.icabangid_o
            LEFT JOIN dbmaster.coa_level4 d on br.coa4=d.COA4
            JOIN dbmaster.t_spg_kode e on br.kodeid=e.kodeid WHERE a.stsnonaktif<>'Y' $fperiode $fcabang
            GROUP BY 1,2,3,4,5";
        
        $query ="CREATE TABLE $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET lebihkurang=0, selisih=0, insentif=0, gaji=0, hk='', rpmakan=0, makan=0, sewa=0, pulsa=0, parkir=0, bbm=0");
        mysqli_query($cnmy, "UPDATE $tmp01 SET rlebihkurang=0, rinsentif=0, rgaji=0, rmakan=0, rsewa=0, rpulsa=0, rparkir=0, rbbm=0");//ralisasi
        mysqli_query($cnmy, "UPDATE $tmp01 SET slebihkurang=0, sinsentif=0, sgaji=0, smakan=0, ssewa=0, spulsa=0, sparkir=0, sbbm=0");//selisih
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET selisih=rptotal-realisasirp");
        
        /*
        mysqli_query($cnmy, "UPDATE $tmp01 SET insentif=rptotal WHERE kodeid='01'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET gaji=rptotal WHERE kodeid='02'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET makan=rptotal WHERE kodeid='03'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET sewa=rptotal WHERE kodeid='04'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET pulsa=rptotal WHERE kodeid='05'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET parkir=rptotal WHERE kodeid='06'");
        
        //REALISASI
        mysqli_query($cnmy, "UPDATE $tmp01 SET rinsentif=realisasirp WHERE kodeid='01'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET rgaji=realisasirp WHERE kodeid='02'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET rmakan=realisasirp WHERE kodeid='03'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET rsewa=realisasirp WHERE kodeid='04'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET rpulsa=realisasirp WHERE kodeid='05'");
        mysqli_query($cnmy, "UPDATE $tmp01 SET rparkir=realisasirp WHERE kodeid='06'");
        
        //SELISIH
        mysqli_query($cnmy, "UPDATE $tmp01 SET sinsentif=insentif-rinsentif");
        mysqli_query($cnmy, "UPDATE $tmp01 SET sgaji=gaji-rgaji");
        mysqli_query($cnmy, "UPDATE $tmp01 SET smakan=makan-rmakan");
        mysqli_query($cnmy, "UPDATE $tmp01 SET ssewa=sewa-rsewa");
        mysqli_query($cnmy, "UPDATE $tmp01 SET spulsa=pulsa-rpulsa");
        mysqli_query($cnmy, "UPDATE $tmp01 SET sparkir=parkir-rparkir");
        
        $query = "SELECT tglbr,
            icabangid,
            nama_cabang,
            coa4,
            nama4,
            sum(rptotal) rptotal, sum(realisasirp) realisasirp, 
            sum(insentif) insentif, sum(gaji) gaji, sum(makan) makan, sum(sewa) sewa, sum(pulsa) pulsa, sum(parkir) parkir, 
            sum(rinsentif) rinsentif, sum(rgaji) rgaji, sum(rmakan) rmakan, sum(rsewa) rsewa, sum(rpulsa) rpulsa, sum(rparkir) rparkir, 
            sum(sinsentif) sinsentif, sum(sgaji) sgaji, sum(smakan) smakan, sum(ssewa) ssewa, sum(spulsa) spulsa, sum(sparkir) sparkir
            FROM $tmp01";
        $query .= "GROUP BY 1,2,3,4,5";
        $query ="CREATE TABLE $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        */
        
    }else{
        
        $query = "SELECT
            a.idbrspg,
            a.id_spg,
            b.nama,
            a.periode tglbr,
            a.icabangid,
            c.nama nama_cabang, a.alokid,
            a.jml_harikerja harikerja,
            a.total,
            a.realisasi,
            a.keterangan,
            a.total lebihkurang, a.total insentif, a.total gaji, a.keterangan hk, a.total rpmakan, a.total makan, a.total sewa, a.total pulsa, a.total parkir, a.total bbm,
            a.total rlebihkurang, a.total rinsentif, a.total rgaji, a.total rmakan, a.total rsewa, a.total rpulsa, a.total rparkir, a.total rbbm,
            a.total slebihkurang, a.total sinsentif, a.total sgaji, a.total smakan, a.total ssewa, a.total spulsa, a.total sparkir, a.total sbbm
            FROM
            dbmaster.t_spg_gaji_br0 a
            JOIN $tmp04 b ON a.id_spg = b.id_spg
            LEFT JOIN mkt.icabang_o c on a.icabangid=c.icabangid_o 
            WHERE a.stsnonaktif<>'Y' $fperiode $fcabang";

        $query ="CREATE TABLE $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        mysqli_query($cnmy, "UPDATE $tmp01 SET lebihkurang=0, insentif=0, gaji=0, hk='', rpmakan=0, makan=0, sewa=0, pulsa=0, parkir=0, bbm=0");
        mysqli_query($cnmy, "UPDATE $tmp01 SET rlebihkurang=0, rinsentif=0, rgaji=0, rmakan=0, rsewa=0, rpulsa=0, rparkir=0, rbbm=0");//ralisasi
        mysqli_query($cnmy, "UPDATE $tmp01 SET slebihkurang=0, sinsentif=0, sgaji=0, smakan=0, ssewa=0, spulsa=0, sparkir=0, sbbm=0");//selisih

        $query = "SELECT * FROM dbmaster.t_spg_gaji_br1 WHERE idbrspg IN (select distinct idbrspg FROM $tmp01)";
        $query ="CREATE TABLE $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.insentif=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid IN ('01', '07') ),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.gaji=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='02'),0)");
        
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.lebihkurang=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='09'),0)");

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

        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rlebihkurang=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='09'),0)");
        
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
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET slebihkurang=lebihkurang-rlebihkurang");
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET sbbm=bbm-rbbm");
    }
    
    if (!empty($ecabang) OR $ecabang <> "*" OR $ecabang = "0000000007") {
        $query = "UPDATE $tmp01 SET nama_cabang='JAKARTA MT', icabangid='JKT_MT' WHERE icabangid='0000000007' AND alokid='001'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "UPDATE $tmp01 SET nama_cabang='JAKARTA RETAIL', icabangid='JKT_RETAIL' WHERE icabangid='0000000007' AND alokid='002'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
?>
    <center><h2><u></u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <?PHP if ($etipertp=="1") { ?>
                    <tr><td></td><td><b>To</b></td><td>:</td><td> Sdri. Mariane</td></tr>
                    <tr><td></td><td><b>Rekap Budget Request (RBR) Team OTC <?PHP echo ""; ?></b></td><td></td><td></td></tr>
                    <tr><td></td><td><b>Transfer</b></td><td>:</td><td><?PHP echo "$tglnow"; ?></td></tr>
                <?PHP }else{ ?>
                    <tr><td></td><td><b>&nbsp;</b></td><td> </td><td> </td></tr>
                    <tr><td></td><td><b>Rekap Budget Request (RBR) Team OTC <?PHP echo ""; ?></b></td><td></td><td></td></tr>
                    <tr><td></td><td><b>Per</b></td><td>:</td><td><?PHP echo "$per1"; ?></td></tr>
                <?PHP } ?>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    <?PHP if ($etipertp=="1") { ?>
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">NO</th>
                <th align="center">CABANG</th>
                <th align="center">NAMA</th>
                <th align="center">INSENTIF</th>
                <th align="center">SELISIH<br/>(Lebih/Kurang)</th>
                <th align="center">GAJI</th>
                <th align="center" colspan="2">UANG MAKAN</th>
                <th align="center">SEWA KENDARAAN</th>
                <th align="center">PULSA</th>
                <th align="center">BBM</th>
                <th align="center">PARKIR</th>
                <th align="center">JUMLAH</th>
                <th align="center">TOTAL</th>
                <th align="center">BIAYA TRANSFER</th>
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

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmcabang</td>";

                    $ilewat=false;
                    $query2 = "select * from $tmp01 WHERE icabangid='$picabang' order by nama_cabang, nama, id_spg";
                    $tampil2= mysqli_query($cnmy, $query2);
                    $jmlrow=mysqli_num_rows($tampil2);
                    $recno=1;
                    $ptotal=0;
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pidspg=$row2['id_spg'];
                        $pnmspg=$row2['nama'];
                        $phk=$row2['hk'];

                        $pinsentif=number_format($row2['insentif'],0,",",",");
                        $pgaji=number_format($row2['gaji'],0,",",",");
                        $pmakan=number_format($row2['makan'],0,",",",");
                        $psewa=number_format($row2['sewa'],0,",",",");
                        $ppulsa=number_format($row2['pulsa'],0,",",",");
                        $pparkir=number_format($row2['parkir'],0,",",",");
                        $pbbm=number_format($row2['bbm'],0,",",",");
                        $pjumlah=number_format($row2['total'],0,",",",");
                        
                        $plebihkurang=number_format($row2['lebihkurang'],0,",",",");

                        $ptotal=$ptotal+$row2['total'];

                        $gtotaljml=$gtotaljml+$row2['total'];

                        if ($ilewat==true) {
                            echo "<tr>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap></td>";
                        }
                        echo "<td nowrap>$pnmspg</td>";
                        echo "<td nowrap align='right'>$pinsentif</td>";
                        echo "<td nowrap align='right'>$plebihkurang</td>";
                        echo "<td nowrap align='right'>$pgaji</td>";
                        echo "<td nowrap align='center'>$phk</td>";
                        echo "<td nowrap align='right'>$pmakan</td>";
                        echo "<td nowrap align='right'>$psewa</td>";
                        echo "<td nowrap align='right'>$ppulsa</td>";
                        echo "<td nowrap align='right'>$pbbm</td>";
                        echo "<td nowrap align='right'>$pparkir</td>";
                        echo "<td nowrap align='right'><b>$pjumlah</b></td>";

                        $jmltotal="";
                        $jmltransfer="";

                        if ((double)$jmlrow==(double)$recno) {
                            $gtotaltot=$gtotaltot+$ptotal;
                            $jmltotal=number_format($ptotal,0,",",",");
                        }

                        echo "<td nowrap align='right'><b>$jmltotal</b></td>";
                        echo "<td nowrap align='right'><b>$jmltransfer</b></td>";

                        echo "</tr>";
                        $ilewat=true;
                        $recno++;
                        $no++;
                    }
                }
                echo "<tr>";
                echo "<td colspan='15'></td>";
                echo "</tr>";

                $gtotaljml=number_format($gtotaljml,0,",",",");
                $gtotaltot=number_format($gtotaltot,0,",",",");
                $gtotaltrans=number_format($gtotaltrans,0,",",",");

                echo "<tr>";
                echo "<td colspan='12' align='center'><b>GRAND TOTAL</b> </td>";
                echo "<td align='right'><b>$gtotaljml</b></td>";
                echo "<td align='right'><b>$gtotaltot</b></td>";
                echo "<td align='right'><b></b></td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
    
    <?PHP }elseif ($etipertp=="2") { ?>

        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Cabang</th>
                    <th colspan="9">Usulan</th>
                    <th colspan="9">Realisasi</th>
                    <th colspan="9">Selisih</th>
                </tr>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">Insentif</th>
                <th align="center">Selisih (+/0)</th>
                <th align="center">Gaji</th>
                <th align="center">Uang Makan</th>
                <th align="center">Sewa Kendaraan</th>
                <th align="center">Pulsa</th>
                <th align="center">BBM</th>
                <th align="center">Parkir</th>
                <th align="center">Total</th>
                
                <th align="center">Insentif</th>
                <th align="center">Selisih (+/0)</th>
                <th align="center">Gaji</th>
                <th align="center">Uang Makan</th>
                <th align="center">Sewa Kendaraan</th>
                <th align="center">Pulsa</th>
                <th align="center">BBM</th>
                <th align="center">Parkir</th>
                <th align="center">Total</th>
                
                <th align="center">Insentif</th>
                <th align="center">Selisih (+/0)</th>
                <th align="center">Gaji</th>
                <th align="center">Uang Makan</th>
                <th align="center">Sewa Kendaraan</th>
                <th align="center">Pulsa</th>
                <th align="center">BBM</th>
                <th align="center">Parkir</th>
                <th align="center">Total</th>
                
                </tr>
            </thead>
            <tbody>
            <?PHP
                $gtotalinc=0;
                $gtotalgaji=0;
                $gtotalmakan=0;
                $gtotalsewa=0;
                $gtotalpulsa=0;
                $gtotalbbm=0;
                $gtotalparkir=0;
                $gtotaltot=0;
                
                $gtotallebihkurang=0;
                
                $rgtotalinc=0;
                $rgtotalgaji=0;
                $rgtotalmakan=0;
                $rgtotalsewa=0;
                $rgtotalpulsa=0;
                $rgtotalbbm=0;
                $rgtotalparkir=0;
                $rgtotaltot=0;
                
                $rgtotallebihkurang=0;
                
                $sgtotalinc=0;
                $sgtotalgaji=0;
                $sgtotalmakan=0;
                $sgtotalsewa=0;
                $sgtotalpulsa=0;
                $sgtotalbbm=0;
                $sgtotalparkir=0;
                $sgtotaltot=0;
                
                $sgtotallebihkurang=0;
                
                $no=1;
                $query = "select icabangid, nama_cabang, sum(insentif) insentif, sum(gaji) gaji, sum(makan) makan, 
                        sum(sewa) sewa, sum(pulsa) pulsa, sum(bbm) bbm, sum(parkir) parkir, 
                        sum(rinsentif) rinsentif, sum(rgaji) rgaji, sum(rmakan) rmakan, sum(rsewa) rsewa, sum(rpulsa) rpulsa, sum(rbbm) rbbm, sum(rparkir) rparkir,
                        sum(sinsentif) sinsentif, sum(sgaji) sgaji, sum(smakan) smakan, sum(ssewa) ssewa, sum(spulsa) spulsa, sum(sbbm) sbbm, sum(sparkir) sparkir,
                        sum(lebihkurang) lebihkurang, sum(rlebihkurang) rlebihkurang, sum(slebihkurang) slebihkurang 
                        from $tmp01 GROUP BY icabangid, nama_cabang order by nama_cabang";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidcabang=$row['icabangid'];
                    $pnmcabang=$row['nama_cabang'];

                    $pinsentif=number_format($row['insentif'],0,",",",");
                    $pgaji=number_format($row['gaji'],0,",",",");
                    $pmakan=number_format($row['makan'],0,",",",");
                    $psewa=number_format($row['sewa'],0,",",",");
                    $ppulsa=number_format($row['pulsa'],0,",",",");
                    $pbbm=number_format($row['bbm'],0,",",",");
                    $pparkir=number_format($row['parkir'],0,",",",");
                    $plebihkurang=number_format($row['lebihkurang'],0,",",",");
                    
                    $ptotal=(double)$row['insentif']+(double)$row['gaji']+(double)$row['makan']+(double)$row['sewa']+(double)$row['pulsa']+(double)$row['bbm']+(double)$row['parkir']+(double)$row['lebihkurang'];
                    
                    $gtotalinc=(double)$gtotalinc+(double)$row['insentif'];
                    $gtotalgaji=(double)$gtotalgaji+(double)$row['gaji'];
                    $gtotalmakan=(double)$gtotalmakan+(double)$row['makan'];
                    $gtotalsewa=(double)$gtotalsewa+(double)$row['sewa'];
                    $gtotalpulsa=(double)$gtotalpulsa+(double)$row['pulsa'];
                    $gtotalbbm=(double)$gtotalbbm+(double)$row['bbm'];
                    $gtotalparkir=(double)$gtotalparkir+(double)$row['parkir'];
                    $gtotallebihkurang=(double)$gtotallebihkurang+(double)$row['lebihkurang'];
                    $gtotaltot=(double)$gtotaltot+(double)$ptotal;
                    
                    $ptotal=number_format($ptotal,0,",",",");
                    
                    //realisasi
                    $rpinsentif=number_format($row['rinsentif'],0,",",",");
                    $rpgaji=number_format($row['rgaji'],0,",",",");
                    $rpmakan=number_format($row['rmakan'],0,",",",");
                    $rpsewa=number_format($row['rsewa'],0,",",",");
                    $rppulsa=number_format($row['rpulsa'],0,",",",");
                    $rpbbm=number_format($row['rbbm'],0,",",",");
                    $rplebihkurang=number_format($row['rlebihkurang'],0,",",",");
                    $rpparkir=number_format($row['rparkir'],0,",",",");
                    
                    $rptotal=(double)$row['rinsentif']+(double)$row['rgaji']+(double)$row['rmakan']+(double)$row['rsewa']+(double)$row['rpulsa']+(double)$row['rbbm']+(double)$row['rparkir']+(double)$row['rlebihkurang'];
                    
                    $rgtotalinc=$rgtotalinc+$row['rinsentif'];
                    $rgtotalgaji=$rgtotalgaji+$row['rgaji'];
                    $rgtotalmakan=$rgtotalmakan+$row['rmakan'];
                    $rgtotalsewa=$rgtotalsewa+$row['rsewa'];
                    $rgtotalpulsa=$rgtotalpulsa+$row['rpulsa'];
                    $rgtotalbbm=$rgtotalbbm+$row['rbbm'];
                    $rgtotalparkir=$rgtotalparkir+$row['rparkir'];
                    $rgtotallebihkurang=$rgtotallebihkurang+$row['rlebihkurang'];
                    $rgtotaltot=$rgtotaltot+$rptotal;
                    
                    $rptotal=number_format($rptotal,0,",",",");
                    
                    //selisih
                    $spinsentif=number_format($row['sinsentif'],0,",",",");
                    $spgaji=number_format($row['sgaji'],0,",",",");
                    $spmakan=number_format($row['smakan'],0,",",",");
                    $spsewa=number_format($row['ssewa'],0,",",",");
                    $sppulsa=number_format($row['spulsa'],0,",",",");
                    $spbbm=number_format($row['sbbm'],0,",",",");
                    $spparkir=number_format($row['sparkir'],0,",",",");
                    $splebihkurang=number_format($row['slebihkurang'],0,",",",");
                    
                    $sptotal=(double)$row['sinsentif']+(double)$row['sgaji']+(double)$row['smakan']+(double)$row['ssewa']+(double)$row['spulsa']+(double)$row['sbbm']+(double)$row['sparkir']+(double)$row['slebihkurang'];
                    
                    $sgtotalinc=$sgtotalinc+$row['sinsentif'];
                    $sgtotalgaji=$sgtotalgaji+$row['sgaji'];
                    $sgtotalmakan=$sgtotalmakan+$row['smakan'];
                    $sgtotalsewa=$sgtotalsewa+$row['ssewa'];
                    $sgtotalpulsa=$sgtotalpulsa+$row['spulsa'];
                    $sgtotalbbm=$sgtotalbbm+$row['sbbm'];
                    $sgtotalparkir=$sgtotalparkir+$row['sparkir'];
                    $sgtotallebihkurang=$sgtotallebihkurang+$row['slebihkurang'];
                    $sgtotaltot=$sgtotaltot+$sptotal;
                    
                    $sptotal=number_format($sptotal,0,",",",");
                    
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmcabang</td>";
                    
                    echo "<td nowrap align='right'>$pinsentif</td>";
                    echo "<td nowrap align='right'>$plebihkurang</td>";
                    echo "<td nowrap align='right'>$pgaji</td>";
                    echo "<td nowrap align='right'>$pmakan</td>";
                    echo "<td nowrap align='right'>$psewa</td>";
                    echo "<td nowrap align='right'>$ppulsa</td>";
                    echo "<td nowrap align='right'>$pbbm</td>";
                    echo "<td nowrap align='right'>$pparkir</td>";
                    echo "<td nowrap align='right'>$ptotal</td>";
                    
                    echo "<td nowrap align='right'>$rpinsentif</td>";
                    echo "<td nowrap align='right'>$rplebihkurang</td>";
                    echo "<td nowrap align='right'>$rpgaji</td>";
                    echo "<td nowrap align='right'>$rpmakan</td>";
                    echo "<td nowrap align='right'>$rpsewa</td>";
                    echo "<td nowrap align='right'>$rppulsa</td>";
                    echo "<td nowrap align='right'>$rpbbm</td>";
                    echo "<td nowrap align='right'>$rpparkir</td>";
                    echo "<td nowrap align='right'>$rptotal</td>";
                    
                    echo "<td nowrap align='right'>$spinsentif</td>";
                    echo "<td nowrap align='right'>$splebihkurang</td>";
                    echo "<td nowrap align='right'>$spgaji</td>";
                    echo "<td nowrap align='right'>$spmakan</td>";
                    echo "<td nowrap align='right'>$spsewa</td>";
                    echo "<td nowrap align='right'>$sppulsa</td>";
                    echo "<td nowrap align='right'>$spbbm</td>";
                    echo "<td nowrap align='right'>$spparkir</td>";
                    echo "<td nowrap align='right'>$sptotal</td>";
                    
                    
                    echo "</tr>";
                    
                    $no++;
                }
                    
                $gtotalinc=number_format($gtotalinc,0,",",",");
                $gtotalgaji=number_format($gtotalgaji,0,",",",");
                $gtotalmakan=number_format($gtotalmakan,0,",",",");
                $gtotalsewa=number_format($gtotalsewa,0,",",",");
                $gtotalpulsa=number_format($gtotalpulsa,0,",",",");
                $gtotalbbm=number_format($gtotalbbm,0,",",",");
                $gtotalparkir=number_format($gtotalparkir,0,",",",");
                $gtotaltot=number_format($gtotaltot,0,",",",");
                
                $gtotallebihkurang=number_format($gtotallebihkurang,0,",",",");
                
                //realisasi
                $rgtotalinc=number_format($rgtotalinc,0,",",",");
                $rgtotalgaji=number_format($rgtotalgaji,0,",",",");
                $rgtotalmakan=number_format($rgtotalmakan,0,",",",");
                $rgtotalsewa=number_format($rgtotalsewa,0,",",",");
                $rgtotalpulsa=number_format($rgtotalpulsa,0,",",",");
                $rgtotalbbm=number_format($rgtotalbbm,0,",",",");
                $rgtotalparkir=number_format($rgtotalparkir,0,",",",");
                $rgtotaltot=number_format($rgtotaltot,0,",",",");
                
                $rgtotallebihkurang=number_format($rgtotallebihkurang,0,",",",");
                    
                $sgtotalinc=number_format($sgtotalinc,0,",",",");
                $sgtotalgaji=number_format($sgtotalgaji,0,",",",");
                $sgtotalmakan=number_format($sgtotalmakan,0,",",",");
                $sgtotalsewa=number_format($sgtotalsewa,0,",",",");
                $sgtotalpulsa=number_format($sgtotalpulsa,0,",",",");
                $sgtotalbbm=number_format($sgtotalbbm,0,",",",");
                $sgtotalparkir=number_format($sgtotalparkir,0,",",",");
                $sgtotaltot=number_format($sgtotaltot,0,",",",");
                
                $sgtotallebihkurang=number_format($sgtotallebihkurang,0,",",",");
                
                echo "<tr>";
                echo "<td colspan='29'></td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td nowrap colspan='2' align='center'><b>TOTAL</b></td>";
                echo "<td nowrap align='right'><b>$gtotalinc</b></td>";
                echo "<td nowrap align='right'><b>$gtotallebihkurang</b></td>";
                echo "<td nowrap align='right'><b>$gtotalgaji</b></td>";
                echo "<td nowrap align='right'><b>$gtotalmakan</b></td>";
                echo "<td nowrap align='right'><b>$gtotalsewa</b></td>";
                echo "<td nowrap align='right'><b>$gtotalpulsa</b></td>";
                echo "<td nowrap align='right'><b>$gtotalbbm</b></td>";
                echo "<td nowrap align='right'><b>$gtotalparkir</b></td>";
                echo "<td nowrap align='right'><b>$gtotaltot</b></td>";
                
                echo "<td nowrap align='right'><b>$rgtotalinc</b></td>";
                echo "<td nowrap align='right'><b>$rgtotallebihkurang</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalgaji</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalmakan</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalsewa</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalpulsa</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalbbm</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalparkir</b></td>";
                echo "<td nowrap align='right'><b>$rgtotaltot</b></td>";
                
                echo "<td nowrap align='right'><b>$sgtotalinc</b></td>";
                echo "<td nowrap align='right'><b>$sgtotallebihkurang</b></td>";
                echo "<td nowrap align='right'><b>$sgtotalgaji</b></td>";
                echo "<td nowrap align='right'><b>$sgtotalmakan</b></td>";
                echo "<td nowrap align='right'><b>$sgtotalsewa</b></td>";
                echo "<td nowrap align='right'><b>$sgtotalpulsa</b></td>";
                echo "<td nowrap align='right'><b>$sgtotalbbm</b></td>";
                echo "<td nowrap align='right'><b>$sgtotalparkir</b></td>";
                echo "<td nowrap align='right'><b>$sgtotaltot</b></td>";
                
                echo "</tr>";
                    
            ?>
            </tbody>
        </table>
    <?PHP }elseif ($etipertp=="3") { ?>
        
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th rowspan="2">Date</th>
                    <th rowspan="2">Bukti</th>
                    <th rowspan="2">Kode</th>
                    <th rowspan="2">Perkiraan</th>
                    <th rowspan="2">Cabang</th>
                    <th rowspan="2" colspan="2">Keterangan</th>
                    <th colspan="2">Realisasi</th>
                    <th rowspan="2">Debit</th>
                    <th rowspan="2">Kredit</th>
                    <th rowspan="2">Saldo</th>
                    <th colspan="2">Usulan</th>
                    <th colspan="2">Selisih</th>
                </tr>
                
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th>Rp.</th>
                    <th>Total Rp.</th>
                    
                    <th>Rp.</th>
                    <th>Total Rp.</th>
                    
                    <th>Rp.</th>
                    <th>Total Rp.</th>
                    
                </tr>
            </thead>
            <tbody>
            <?PHP
                $gtotalrealisasi=0;
                $gtotalusulan=0;
                $gtotalselisih=0;
                $pjmlrpreal=0;
                $pjmlrpusul=0;
                $pjmlselisih=0;
                
                $no=1;
                $query = "select distinct icabangid, nama_cabang, coa4, nama4 from $tmp01 order by nama_cabang, coa4";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $picabang=$row['icabangid'];
                    $pnmcabang=$row['nama_cabang'];
                    $pcoa4=$row['coa4'];
                    $pnmcoa4=$row['nama4'];
                    
                    $ilewat=true;
                    $query2 = "select * from $tmp01 WHERE icabangid='$picabang' AND coa4='$pcoa4' order by nama_cabang, coa4, tglbr";
                    $tampil2= mysqli_query($cnmy, $query2);
                    $jmlrow=mysqli_num_rows($tampil2);
                    $recno=1;
                    $pjmlrpreal=0;
                    $pjmlrpusul=0;
                    $pjmlselisih=0;
                    
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $ptglbr = date("d-F-Y", strtotime($row2['tglbr']));

                        $pidcabang=$row2['icabangid'];
                        $pnmcabang=$row2['nama_cabang'];
                        $pcoa4=$row2['coa4'];
                        $pnmcoa4=$row2['nama4'];
                        
                        $pjmlrpreal=$pjmlrpreal+$row2['realisasirp'];
                        $pjmlrpusul=$pjmlrpusul+$row2['rptotal'];
                        $pjmlselisih=$pjmlselisih+$row2['selisih'];
                                
                        $pusulan=number_format($row2['rptotal'],0,",",",");
                        $prealisasirp=number_format($row2['realisasirp'],0,",",",");
                        $pselisih=number_format($row2['selisih'],0,",",",");
                        
                        $gtotalrealisasi=$gtotalrealisasi+$row2['realisasirp'];
                        $gtotalusulan=$gtotalusulan+$row2['rptotal'];
                        $gtotalselisih=$gtotalselisih+$row2['selisih'];
                        
                        
                        echo "<tr>";

                        echo "<td nowrap>$ptglbr</td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap>$pcoa4</td>";
                        echo "<td nowrap>$pnmcoa4</td>";
                        echo "<td nowrap>$pnmcabang</td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";

                        if ($jmlrow<>$recno){
                            echo "<td nowrap align='right'>$prealisasirp</td>";//realisasi
                            echo "<td nowrap align='right'></td>";//total realisasi

                            echo "<td nowrap align='right'></td>";//debit
                            echo "<td nowrap align='right'>$prealisasirp</td>";//kredit
                            echo "<td nowrap align='right'></td>";//saldo

                            echo "<td nowrap align='right'>$pusulan</td>";//usulan
                            echo "<td nowrap align='right'></td>";//total usulan

                            echo "<td nowrap align='right'>$pselisih</td>";//selisih
                            echo "<td nowrap align='right'></td>";//total selisih
                            
                            echo "</tr>";
                        }
                        
                        
                        if ($jmlrow==$recno){
                            $ilewat=false;
                        }
                        
                        $recno++;
                        $no++;
                        
                    }
                    
                    if ($ilewat==false) {
                        $pjmlrpreal=number_format($pjmlrpreal,0,",",",");
                        $pjmlrpusul=number_format($pjmlrpusul,0,",",",");
                        $pjmlselisih=number_format($pjmlselisih,0,",",",");
                        
                        echo "<td nowrap align='right'>$prealisasirp</td>";//realisasi
                        echo "<td nowrap align='right'><b>$pjmlrpreal</b></td>";//total realisasi

                        echo "<td nowrap align='right'></td>";//debit
                        echo "<td nowrap align='right'>$prealisasirp</td>";//kredit
                        echo "<td nowrap align='right'></td>";//saldo

                        echo "<td nowrap align='right'>$pusulan</td>";//usulan
                        echo "<td nowrap align='right'><b>$pjmlrpusul</b></td>";//total usulan

                        echo "<td nowrap align='right'>$pselisih</td>";//selisih
                        echo "<td nowrap align='right'><b>$pjmlselisih</b></td>";//total selisih
                            
                        echo "</tr>";
                    }
                    
                }
                //TOTAL
                $gtotalrealisasi=number_format($gtotalrealisasi,0,",",",");
                $gtotalusulan=number_format($gtotalusulan,0,",",",");
                $gtotalselisih=number_format($gtotalselisih,0,",",",");
                
                
                
                echo "<tr>";
                echo "<td colspan=7 alignt='right'><b>TOTAL &nbsp; &nbsp; </b></td>";

                echo "<td nowrap align='right'><b>$gtotalrealisasi</b></td>";//realisasi
                echo "<td nowrap align='right'><b>$gtotalrealisasi</b></td>";//total realisasi

                echo "<td nowrap align='right'></td>";//debit
                echo "<td nowrap align='right'><b>$gtotalrealisasi</b></td>";//kredit
                echo "<td nowrap align='right'></td>";//saldo

                echo "<td nowrap align='right'><b>$gtotalusulan</b></td>";//usulan
                echo "<td nowrap align='right'><b>$gtotalusulan</b></td>";//total usulan

                echo "<td nowrap align='right'><b>$gtotalselisih</b></td>";//selisih
                echo "<td nowrap align='right'><b>$gtotalselisih</b></td>";//total selisih

                echo "</tr>";
                
                
                //SUMMARY CABANG & COA
                echo "<tr><td colspan=16 alignt='center'><b></b></td></tr>";
                echo "<tr><td colspan=16 alignt='left'><b>SUMMARY CABANG & COA</b></td></tr>";
                
                $gtotalrealisasi=0;
                $gtotalusulan=0;
                $gtotalselisih=0;
                
                $query = "select distinct icabangid, nama_cabang from $tmp01 order by nama_cabang";
                $tampilsc= mysqli_query($cnmy, $query);
                while ($rowsc= mysqli_fetch_array($tampilsc)) {
                    $picabang=$rowsc['icabangid'];
                    $pnmcabang=$rowsc['nama_cabang'];
                
                    
                    $query = "SELECT
                        icabangid,
                        nama_cabang,
                        coa4,
                        nama4,
                        IFNULL(sum(rptotal),0) rptotal,
                        IFNULL(sum(realisasirp),0) realisasirp, sum(selisih) selisih 
                        From $tmp01 WHERE icabangid='$picabang' GROUP BY 1,2,3,4 ORDER BY nama_cabang, coa4";
                    $tampilc= mysqli_query($cnmy, $query);
                    $jmlrow=mysqli_num_rows($tampilc);$recno=1;$ilewat=true;
                    $pjmlrpreal=0;$pjmlrpusul=0;$pjmlselisih=0;
                    while ($rowc= mysqli_fetch_array($tampilc)) {
                        $pidcabang=$rowc['icabangid'];
                        $pnmcabang=$rowc['nama_cabang'];
                        $pcoa4=$rowc['coa4'];
                        $pnmcoa4=$rowc['nama4'];

                        $pjmlrpreal=$pjmlrpreal+$rowc['realisasirp'];
                        $pjmlrpusul=$pjmlrpusul+$rowc['rptotal'];
                        $pjmlselisih=$pjmlselisih+$rowc['selisih'];

                        $pusulan=number_format($rowc['rptotal'],0,",",",");
                        $prealisasirp=number_format($rowc['realisasirp'],0,",",",");
                        $pselisih=number_format($rowc['selisih'],0,",",",");

                        $gtotalrealisasi=$gtotalrealisasi+$rowc['realisasirp'];
                        $gtotalusulan=$gtotalusulan+$rowc['rptotal'];
                        $gtotalselisih=$gtotalselisih+$rowc['selisih'];
                        
                        echo "<tr>";
                        echo "<td colspan=2 alignt='center'><b></b></td>";
                        echo "<td nowrap>$pcoa4</td>";
                        echo "<td nowrap>$pnmcoa4</td>";
                        echo "<td colspan=3 nowrap>$pnmcabang</td>";

                        if ($jmlrow<>$recno){
                            echo "<td nowrap align='right'>$prealisasirp</td>";//realisasi
                            echo "<td nowrap align='right'></td>";//realisasi

                            echo "<td nowrap align='right'></td>";//debit
                            echo "<td nowrap align='right'>$prealisasirp</td>";//kredit
                            echo "<td nowrap align='right'></td>";//saldo

                            echo "<td nowrap align='right'>$pusulan</td>";//usulan
                            echo "<td nowrap align='right'></td>";//usulan

                            echo "<td nowrap align='right'>$pselisih</td>";//selisih
                            echo "<td nowrap align='right'></td>";//selisih

                            echo "</tr>";
                        }
                        
                        //if ($jmlrow==$recno){
                        //    $ilewat=false;
                        //}
                        
                        $recno++;
                    }
                    
                    //if ($ilewat==false) {
                        $pjmlrpreal=number_format($pjmlrpreal,0,",",",");
                        $pjmlrpusul=number_format($pjmlrpusul,0,",",",");
                        $pjmlselisih=number_format($pjmlselisih,0,",",",");
                        
                        echo "<td nowrap align='right'>$prealisasirp</td>";//realisasi
                        echo "<td nowrap align='right'><b>$pjmlrpreal</b></td>";//total realisasi

                        echo "<td nowrap align='right'></td>";//debit
                        echo "<td nowrap align='right'>$prealisasirp</td>";//kredit
                        echo "<td nowrap align='right'></td>";//saldo

                        echo "<td nowrap align='right'>$pusulan</td>";//usulan
                        echo "<td nowrap align='right'><b>$pjmlrpusul</b></td>";//total usulan

                        echo "<td nowrap align='right'>$pselisih</td>";//selisih
                        echo "<td nowrap align='right'><b>$pjmlselisih</b></td>";//total selisih
                            
                        echo "</tr>";
                        
                        $pjmlrpreal=0;$pjmlrpusul=0;$pjmlselisih=0;
                    //}
                    
                    
                }
                    
                $gtotalrealisasi=number_format($gtotalrealisasi,0,",",",");
                $gtotalusulan=number_format($gtotalusulan,0,",",",");
                $gtotalselisih=number_format($gtotalselisih,0,",",",");
                
                echo "<tr>";
                echo "<td colspan=7 alignt='right'><b>TOTAL &nbsp; &nbsp; </b></td>";

                echo "<td nowrap align='right'><b>$gtotalrealisasi</b></td>";//realisasi
                echo "<td nowrap align='right'><b>$gtotalrealisasi</b></td>";//total realisasi

                echo "<td nowrap align='right'></td>";//debit
                echo "<td nowrap align='right'><b>$gtotalrealisasi</b></td>";//kredit
                echo "<td nowrap align='right'></td>";//saldo

                echo "<td nowrap align='right'><b>$gtotalusulan</b></td>";//usulan
                echo "<td nowrap align='right'><b>$gtotalusulan</b></td>";//total usulan

                echo "<td nowrap align='right'><b>$gtotalselisih</b></td>";//selisih
                echo "<td nowrap align='right'><b>$gtotalselisih</b></td>";//total selisih

                echo "</tr>";
                
                
                
                //SUMMARY COA
                echo "<tr><td colspan=16 alignt='center'><b></b></td></tr>";
                echo "<tr><td colspan=16 alignt='left'><b>SUMMARY COA</b></td></tr>";
                
                $query = "SELECT
                    coa4,
                    nama4,
                    IFNULL(sum(rptotal),0) rptotal,
                    IFNULL(sum(realisasirp),0) realisasirp, sum(selisih) selisih 
                    From $tmp01 GROUP BY 1,2 ORDER BY coa4";
                $tampilg= mysqli_query($cnmy, $query);
                while ($rowg= mysqli_fetch_array($tampilg)) {
                    $pcoa4=$rowg['coa4'];
                    $pnmcoa4=$rowg['nama4'];
                    
                    $pjmlrpreal=$pjmlrpreal+$rowg['realisasirp'];
                    $pjmlrpusul=$pjmlrpusul+$rowg['rptotal'];
                    $pjmlselisih=$pjmlselisih+$rowg['selisih'];

                    $pusulan=number_format($rowg['rptotal'],0,",",",");
                    $prealisasirp=number_format($rowg['realisasirp'],0,",",",");
                    $pselisih=number_format($rowg['selisih'],0,",",",");
                        
                    echo "<tr>";
                    echo "<td colspan=2 alignt='center'><b></b></td>";
                    echo "<td nowrap>$pcoa4</td>";
                    echo "<td colspan=4 nowrap>$pnmcoa4</td>";
                    

                    echo "<td nowrap align='right' colspan=2>$prealisasirp</td>";//realisasi

                    echo "<td nowrap align='right'></td>";//debit
                    echo "<td nowrap align='right'>$prealisasirp</td>";//kredit
                    echo "<td nowrap align='right'></td>";//saldo

                    echo "<td nowrap align='right' colspan=2>$pusulan</td>";//usulan

                    echo "<td nowrap align='right' colspan=2>$pselisih</td>";//selisih

                    echo "</tr>";
                    
                    
                }
                $pjmlrpreal=number_format($pjmlrpreal,0,",",",");
                $pjmlrpusul=number_format($pjmlrpusul,0,",",",");
                $pjmlselisih=number_format($pjmlselisih,0,",",",");
                
                echo "<tr>";
                echo "<td colspan=7 alignt='right'><b>TOTAL &nbsp; &nbsp; </b></td>";

                echo "<td nowrap align='right' colspan=2><b>$pjmlrpreal</b></td>";//realisasi

                echo "<td nowrap align='right'></td>";//debit
                echo "<td nowrap align='right'><b>$pjmlrpreal</b></td>";//kredit
                echo "<td nowrap align='right'></td>";//saldo

                echo "<td nowrap align='right' colspan=2><b>$pjmlrpusul</b></td>";//usulan

                echo "<td nowrap align='right' colspan=2><b>$pjmlselisih</b></td>";//selisih

                echo "</tr>";
                
                
                
            ?>
            </tbody>
        </table>
        <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    <?PHP
        }
    
        mysqli_query($cnmy, "DROP TABLE $tmp01");
        mysqli_query($cnmy, "DROP TABLE $tmp02");
        mysqli_query($cnmy, "DROP TABLE $tmp04");
        
        mysqli_close($cnmy);
        
        if ($etipertp=="1") {
            
            if (!empty($pttdmgr)) {
                $namapengaju=$_SESSION['USERID'];
                $now=date("mdYhis");
                
                $data="data:".$pttdmgr;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju="img_".$now."MGRSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
                
                if (!empty($namapengaju))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
            }
            
    ?>
        
            <br/>&nbsp;<br/>&nbsp;
            <!--
            Yang Membuat,
            <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
            Desi Humaira
            -->
        <?PHP } ?>
</body>
</html>
