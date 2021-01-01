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
    
    
    $fcabang = "";
    
    $fperiode = " AND DATE_FORMAT(a.tglbr, '%Y%m') = '$periode1'";
    if (!empty($ecabang) AND ($ecabang <> "*")) $fcabang = " AND a.icabangid='$ecabang' ";
        
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DGJSPGOTC01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DGJSPGOTC02_".$_SESSION['IDCARD']."_$now ";
    
    if ($etipertp==3) {
        
        $query = "SELECT
            a.tglbr,
            a.icabangid,
            c.nama nama_cabang,
            br.coa4,
            d.NAMA4 nama4,
            IFNULL(sum(br.rptotal),0) rptotal,
            IFNULL(sum(br.realisasirp),0) realisasirp, sum(br.rptotal) selisih, 
            sum(br.rptotal) insentif, sum(br.rptotal) gaji, sum(br.rptotal) makan, sum(br.rptotal) sewa, sum(br.rptotal) pulsa, sum(br.rptotal) parkir,
            sum(br.rptotal) rinsentif, sum(br.rptotal) rgaji, sum(br.rptotal) rmakan, sum(br.rptotal) rsewa, sum(br.rptotal) rpulsa, sum(br.rptotal) rparkir,
            sum(br.rptotal) sinsentif, sum(br.rptotal) sgaji, sum(br.rptotal) smakan, sum(br.rptotal) ssewa, sum(br.rptotal) spulsa, sum(br.rptotal) sparkir
            FROM
            dbmaster.t_spg_br1 br
            JOIN dbmaster.t_spg_br0 a ON br.idbrspg = a.idbrspg
            JOIN mkt.spg b ON a.id_spg = b.id_spg
            LEFT JOIN mkt.icabang_o c ON a.icabangid = c.icabangid_o
            LEFT JOIN dbmaster.coa_level4 d on br.coa4=d.COA4
            JOIN dbmaster.t_spg_kode e on br.kodeid=e.kodeid WHERE a.stsnonaktif<>'Y' $fperiode $fcabang
            GROUP BY 1,2,3,4,5";
        
        $query ="CREATE TABLE $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        mysqli_query($cnmy, "UPDATE $tmp01 SET selisih=0, insentif=0, gaji=0, hk='', rpmakan=0, makan=0, sewa=0, pulsa=0, parkir=0");
        mysqli_query($cnmy, "UPDATE $tmp01 SET rinsentif=0, rgaji=0, rmakan=0, rsewa=0, rpulsa=0, rparkir=0");//ralisasi
        mysqli_query($cnmy, "UPDATE $tmp01 SET sinsentif=0, sgaji=0, smakan=0, ssewa=0, spulsa=0, sparkir=0");//selisih
        
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
            a.tglbr,
            a.icabangid,
            c.nama nama_cabang,
            a.harikerja,
            a.total,
            a.realisasi,
            a.keterangan,
            a.total insentif, a.total gaji, a.keterangan hk, a.total rpmakan, a.total makan, a.total sewa, a.total pulsa, a.total parkir,
            a.total rinsentif, a.total rgaji, a.total rmakan, a.total rsewa, a.total rpulsa, a.total rparkir,
            a.total sinsentif, a.total sgaji, a.total smakan, a.total ssewa, a.total spulsa, a.total sparkir
            FROM
            dbmaster.t_spg_br0 a
            JOIN mkt.spg b ON a.id_spg = b.id_spg
            LEFT JOIN mkt.icabang_o c on a.icabangid=c.icabangid_o 
            WHERE a.stsnonaktif<>'Y' $fperiode $fcabang";

        $query ="CREATE TABLE $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        mysqli_query($cnmy, "UPDATE $tmp01 SET insentif=0, gaji=0, hk='', rpmakan=0, makan=0, sewa=0, pulsa=0, parkir=0");
        mysqli_query($cnmy, "UPDATE $tmp01 SET rinsentif=0, rgaji=0, rmakan=0, rsewa=0, rpulsa=0, rparkir=0");//ralisasi
        mysqli_query($cnmy, "UPDATE $tmp01 SET sinsentif=0, sgaji=0, smakan=0, ssewa=0, spulsa=0, sparkir=0");//selisih

        $query = "SELECT * FROM dbmaster.t_spg_br1 WHERE idbrspg IN (select distinct idbrspg FROM $tmp01)";
        $query ="CREATE TABLE $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.insentif=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='01'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.gaji=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='02'),0)");

        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rpmakan=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.makan=IFNULL((SELECT sum(rptotal) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 SET hk=CONCAT(harikerja,' x ', FORMAT(rpmakan,0,'ta_in'))");

        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.sewa=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='04'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.pulsa=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='05'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.parkir=IFNULL((SELECT sum(rp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='06'),0)");

        //realisasi
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rinsentif=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='01'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rgaji=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='02'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rmakan=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='03'),0)");

        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rsewa=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='04'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rpulsa=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='05'),0)");
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.rparkir=IFNULL((SELECT sum(realisasirp) FROM $tmp02 b WHERE a.idbrspg=b.idbrspg AND b.kodeid='06'),0)");

        mysqli_query($cnmy, "UPDATE $tmp01 SET sinsentif=insentif-rinsentif");
        mysqli_query($cnmy, "UPDATE $tmp01 SET sgaji=gaji-rgaji");
        mysqli_query($cnmy, "UPDATE $tmp01 SET smakan=makan-rmakan");
        mysqli_query($cnmy, "UPDATE $tmp01 SET ssewa=sewa-rsewa");
        mysqli_query($cnmy, "UPDATE $tmp01 SET spulsa=pulsa-rpulsa");
        mysqli_query($cnmy, "UPDATE $tmp01 SET sparkir=parkir-rparkir");
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
                <th align="center">GAJI</th>
                <th align="center" colspan="2">UANG MAKAN</th>
                <th align="center">SEWA KENDARAAN</th>
                <th align="center">PULSA</th>
                <th align="center">PARKIR</th>
                <th align="center">JUMLAH</th>
                <th align="center">TOTAL</th>
                <th align="center">BIAYA TRANSFER</th>
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
                        $pjumlah=number_format($row2['total'],0,",",",");

                        $ptotal=$ptotal+$row2['total'];

                        $gtotaljml=$gtotaljml+$row2['total'];

                        if ($ilewat==true) {
                            echo "<tr>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap></td>";
                        }
                        echo "<td nowrap>$pnmspg</td>";
                        echo "<td nowrap align='right'>$pinsentif</td>";
                        echo "<td nowrap align='right'>$pgaji</td>";
                        echo "<td nowrap align='center'>$phk</td>";
                        echo "<td nowrap align='right'>$pmakan</td>";
                        echo "<td nowrap align='right'>$psewa</td>";
                        echo "<td nowrap align='right'>$ppulsa</td>";
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
                echo "<td colspan='13'></td>";
                echo "<tr>";

                $gtotaljml=number_format($gtotaljml,0,",",",");
                $gtotaltot=number_format($gtotaltot,0,",",",");
                $gtotaltrans=number_format($gtotaltrans,0,",",",");

                echo "<tr>";
                echo "<td colspan='10' align='center'><b>GRAND TOTAL</b> </td>";
                echo "<td align='right'><b>$gtotaljml</b></td>";
                echo "<td align='right'><b>$gtotaltot</b></td>";
                echo "<td align='right'><b></b></td>";
                echo "<tr>";
                ?>
            </tbody>
        </table>
    
    <?PHP }elseif ($etipertp=="2") { ?>

        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Cabang</th>
                    <th colspan="7">Usulan</th>
                    <th colspan="7">Realisasi</th>
                    <th colspan="7">Selisih</th>
                </tr>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">Insentif</th>
                <th align="center">Gaji</th>
                <th align="center">Uang Makan</th>
                <th align="center">Sewa Kendaraan</th>
                <th align="center">Pulsa</th>
                <th align="center">Parkir</th>
                <th align="center">Total</th>
                
                <th align="center">Insentif</th>
                <th align="center">Gaji</th>
                <th align="center">Uang Makan</th>
                <th align="center">Sewa Kendaraan</th>
                <th align="center">Pulsa</th>
                <th align="center">Parkir</th>
                <th align="center">Total</th>
                
                <th align="center">Insentif</th>
                <th align="center">Gaji</th>
                <th align="center">Uang Makan</th>
                <th align="center">Sewa Kendaraan</th>
                <th align="center">Pulsa</th>
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
                $gtotalparkir=0;
                $gtotaltot=0;
                
                $rgtotalinc=0;
                $rgtotalgaji=0;
                $rgtotalmakan=0;
                $rgtotalsewa=0;
                $rgtotalpulsa=0;
                $rgtotalparkir=0;
                $rgtotaltot=0;
                
                $sgtotalinc=0;
                $sgtotalgaji=0;
                $sgtotalmakan=0;
                $sgtotalsewa=0;
                $sgtotalpulsa=0;
                $sgtotalparkir=0;
                $sgtotaltot=0;
                
                $no=1;
                $query = "select icabangid, nama_cabang, sum(insentif) insentif, sum(gaji) gaji, sum(makan) makan, 
                        sum(sewa) sewa, sum(pulsa) pulsa, sum(parkir) parkir, 
                        sum(rinsentif) rinsentif, sum(rgaji) rgaji, sum(rmakan) rmakan, sum(rsewa) rsewa, sum(rpulsa) rpulsa, sum(rparkir) rparkir,
                        sum(sinsentif) sinsentif, sum(sgaji) sgaji, sum(smakan) smakan, sum(ssewa) ssewa, sum(spulsa) spulsa, sum(sparkir) sparkir
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
                    $pparkir=number_format($row['parkir'],0,",",",");
                    
                    $ptotal=(double)$row['insentif']+(double)$row['gaji']+(double)$row['makan']+(double)$row['sewa']+(double)$row['pulsa']+(double)$row['parkir'];
                    
                    $gtotalinc=$gtotalinc+$row['insentif'];
                    $gtotalgaji=$gtotalgaji+$row['gaji'];
                    $gtotalmakan=$gtotalmakan+$row['makan'];
                    $gtotalsewa=$gtotalsewa+$row['sewa'];
                    $gtotalpulsa=$gtotalpulsa+$row['pulsa'];
                    $gtotalparkir=$gtotalparkir+$row['parkir'];
                    $gtotaltot=$gtotaltot+$ptotal;
                    
                    $ptotal=number_format($ptotal,0,",",",");
                    
                    //realisasi
                    $rpinsentif=number_format($row['rinsentif'],0,",",",");
                    $rpgaji=number_format($row['rgaji'],0,",",",");
                    $rpmakan=number_format($row['rmakan'],0,",",",");
                    $rpsewa=number_format($row['rsewa'],0,",",",");
                    $rppulsa=number_format($row['rpulsa'],0,",",",");
                    $rpparkir=number_format($row['rparkir'],0,",",",");
                    
                    $rptotal=(double)$row['rinsentif']+(double)$row['rgaji']+(double)$row['rmakan']+(double)$row['rsewa']+(double)$row['rpulsa']+(double)$row['rparkir'];
                    
                    $rgtotalinc=$rgtotalinc+$row['rinsentif'];
                    $rgtotalgaji=$rgtotalgaji+$row['rgaji'];
                    $rgtotalmakan=$rgtotalmakan+$row['rmakan'];
                    $rgtotalsewa=$rgtotalsewa+$row['rsewa'];
                    $rgtotalpulsa=$rgtotalpulsa+$row['rpulsa'];
                    $rgtotalparkir=$rgtotalparkir+$row['rparkir'];
                    $rgtotaltot=$rgtotaltot+$rptotal;
                    
                    $rptotal=number_format($rptotal,0,",",",");
                    
                    //selisih
                    $spinsentif=number_format($row['sinsentif'],0,",",",");
                    $spgaji=number_format($row['sgaji'],0,",",",");
                    $spmakan=number_format($row['smakan'],0,",",",");
                    $spsewa=number_format($row['ssewa'],0,",",",");
                    $sppulsa=number_format($row['spulsa'],0,",",",");
                    $spparkir=number_format($row['sparkir'],0,",",",");
                    
                    $sptotal=(double)$row['sinsentif']+(double)$row['sgaji']+(double)$row['smakan']+(double)$row['ssewa']+(double)$row['spulsa']+(double)$row['sparkir'];
                    
                    $sgtotalinc=$gtotalinc+$row['sinsentif'];
                    $sgtotalgaji=$gtotalgaji+$row['sgaji'];
                    $sgtotalmakan=$gtotalmakan+$row['smakan'];
                    $sgtotalsewa=$gtotalsewa+$row['ssewa'];
                    $sgtotalpulsa=$gtotalpulsa+$row['spulsa'];
                    $sgtotalparkir=$gtotalparkir+$row['sparkir'];
                    $sgtotaltot=$sgtotaltot+$sptotal;
                    
                    $sptotal=number_format($sptotal,0,",",",");
                    
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmcabang</td>";
                    
                    echo "<td nowrap align='right'>$pinsentif</td>";
                    echo "<td nowrap align='right'>$pgaji</td>";
                    echo "<td nowrap align='right'>$pmakan</td>";
                    echo "<td nowrap align='right'>$psewa</td>";
                    echo "<td nowrap align='right'>$ppulsa</td>";
                    echo "<td nowrap align='right'>$pparkir</td>";
                    echo "<td nowrap align='right'>$ptotal</td>";
                    
                    echo "<td nowrap align='right'>$rpinsentif</td>";
                    echo "<td nowrap align='right'>$rpgaji</td>";
                    echo "<td nowrap align='right'>$rpmakan</td>";
                    echo "<td nowrap align='right'>$rpsewa</td>";
                    echo "<td nowrap align='right'>$rppulsa</td>";
                    echo "<td nowrap align='right'>$rpparkir</td>";
                    echo "<td nowrap align='right'>$rptotal</td>";
                    
                    echo "<td nowrap align='right'>$spinsentif</td>";
                    echo "<td nowrap align='right'>$spgaji</td>";
                    echo "<td nowrap align='right'>$spmakan</td>";
                    echo "<td nowrap align='right'>$spsewa</td>";
                    echo "<td nowrap align='right'>$sppulsa</td>";
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
                $gtotalparkir=number_format($gtotalparkir,0,",",",");
                $gtotaltot=number_format($gtotaltot,0,",",",");
                
                //realisasi
                $rgtotalinc=number_format($rgtotalinc,0,",",",");
                $rgtotalgaji=number_format($rgtotalgaji,0,",",",");
                $rgtotalmakan=number_format($rgtotalmakan,0,",",",");
                $rgtotalsewa=number_format($rgtotalsewa,0,",",",");
                $rgtotalpulsa=number_format($rgtotalpulsa,0,",",",");
                $rgtotalparkir=number_format($rgtotalparkir,0,",",",");
                $rgtotaltot=number_format($rgtotaltot,0,",",",");
                    
                $sgtotalinc=number_format($sgtotalinc,0,",",",");
                $sgtotalgaji=number_format($sgtotalgaji,0,",",",");
                $sgtotalmakan=number_format($sgtotalmakan,0,",",",");
                $sgtotalsewa=number_format($sgtotalsewa,0,",",",");
                $sgtotalpulsa=number_format($sgtotalpulsa,0,",",",");
                $sgtotalparkir=number_format($sgtotalparkir,0,",",",");
                $sgtotaltot=number_format($sgtotaltot,0,",",",");
                
                echo "<tr>";
                echo "<td colspan='23'></td>";
                echo "<tr>";
                echo "<td nowrap colspan='2' align='center'><b>TOTAL</b></td>";
                echo "<td nowrap align='right'><b>$gtotalinc</b></td>";
                echo "<td nowrap align='right'><b>$gtotalgaji</b></td>";
                echo "<td nowrap align='right'><b>$gtotalmakan</b></td>";
                echo "<td nowrap align='right'><b>$gtotalsewa</b></td>";
                echo "<td nowrap align='right'><b>$gtotalpulsa</b></td>";
                echo "<td nowrap align='right'><b>$gtotalparkir</b></td>";
                echo "<td nowrap align='right'><b>$gtotaltot</b></td>";
                
                echo "<td nowrap align='right'><b>$rgtotalinc</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalgaji</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalmakan</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalsewa</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalpulsa</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalparkir</b></td>";
                echo "<td nowrap align='right'><b>$rgtotaltot</b></td>";
                
                echo "<td nowrap align='right'><b>$sgtotalinc</b></td>";
                echo "<td nowrap align='right'><b>$sgtotalgaji</b></td>";
                echo "<td nowrap align='right'><b>$sgtotalmakan</b></td>";
                echo "<td nowrap align='right'><b>$sgtotalsewa</b></td>";
                echo "<td nowrap align='right'><b>$sgtotalpulsa</b></td>";
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
        if ($etipertp=="1") {
    ?>
            <br/>&nbsp;<br/>&nbsp;
            Yang Membuat,
            <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
            Desi Humaira
            
        <?PHP } ?>
</body>
</html>
