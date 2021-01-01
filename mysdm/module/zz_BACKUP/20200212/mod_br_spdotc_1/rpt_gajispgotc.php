<?PHP
    session_start();
    
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=BR OTC GAJI SPG.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    include "config/fungsi_sql.php";
    $cnit=$cnmy;
    
    $tglnow = date("d F Y");
    $periodeajukan = date("d/m/Y");
    $periodegaji = date("F Y");
    
    
    $gmrheight = "100px";
    $ngbr_idinput="";
    $gbrttd_fin1="";
    $gbrttd_fin2="";
    $gbrttd_dir1="";
    $gbrttd_dir2="";
    
    $namapengaju_ttd_fin1="";
    $namapengaju_ttd_fin2="";
    $namapengaju_ttd_fin3="";
    
    $namapengaju_ttd1="";
    $namapengaju_ttd2="";
    $namapengaju_ttd3="";
    
    $ntgl_apv1="";
    $ntgl_apv2="";
    $ntgl_apv3="";
    $ntgl_apv_dir1="";
    $ntgl_apv_dir2="";
    
    
    $pidinput=$_GET['ispd'];
    $pnobrdivisi="";
    $query = "select * from dbmaster.t_suratdana_br WHERE idinput='$pidinput'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $ra= mysqli_fetch_array($tampil);
        $pnobrdivisi=$ra['nodivisi'];
        $periodeajukan=$ra['tgl'];
        $periodegaji=$ra['tglf'];
        
        $periodeajukan= date("d/m/Y", strtotime($periodeajukan));
        $periodegaji= date("F Y", strtotime($periodegaji));
        

        $ngbr_idinput=$ra['idinput'];

        $gbrttd_fin1=$ra['gbr_apv1'];
        $gbrttd_fin2=$ra['gbr_apv2'];
        $gbrttd_fin3=$ra['gbr_apv3'];

        $gbrttd_dir1=$ra['gbr_dir'];
        $gbrttd_dir2=$ra['gbr_dir2'];

        if (!empty($gbrttd_fin1)) {
            $data="data:".$gbrttd_fin1;
            $data=str_replace(' ','+',$data);
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $namapengaju_ttd_fin1="imgfin1_".$ngbr_idinput."TTDSPD_.png";
            file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin1, $data);

            if (!empty($ra['tgl_apv1']) AND $ra['tgl_apv1']<>"0000-00-00") $ntgl_apv1="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_apv1']));

        }

        if (!empty($gbrttd_fin2)) {
            $data="data:".$gbrttd_fin2;
            $data=str_replace(' ','+',$data);
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $namapengaju_ttd_fin2="imgfin2_".$ngbr_idinput."TTDSPD_.png";
            file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin2, $data);

            if (!empty($ra['tgl_apv2']) AND $ra['tgl_apv2']<>"0000-00-00") $ntgl_apv2="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_apv2']));

        }

        if (!empty($gbrttd_fin3)) {
            $data="data:".$gbrttd_fin3;
            $data=str_replace(' ','+',$data);
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $namapengaju_ttd_fin3="imgfin3_".$ngbr_idinput."TTDSPD_.png";
            file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin3, $data);

            if (!empty($ra['tgl_apv2']) AND $ra['tgl_apv2']<>"0000-00-00") $ntgl_apv2="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_apv2']));

        }

        if (!empty($gbrttd_dir1)) {
            $data="data:".$gbrttd_dir1;
            $data=str_replace(' ','+',$data);
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $namapengaju_ttd1="imgdr1_".$ngbr_idinput."TTDSPD_.png";
            file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd1, $data);

            if (!empty($ra['tgl_dir']) AND $ra['tgl_dir']<>"0000-00-00") $ntgl_apv_dir1="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_dir']));

        }

        if (!empty($gbrttd_dir2)) {
            $data="data:".$gbrttd_dir2;
            $data=str_replace(' ','+',$data);
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $namapengaju_ttd2="imgdr2_".$ngbr_idinput."TTDSPD_.png";
            file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd2, $data);

            if (!empty($ra['tgl_dir2']) AND $ra['tgl_dir2']<>"0000-00-00") $ntgl_apv_dir2="Approved<br/>".date("d-m-Y", strtotime($ra['tgl_dir2']));

        }
    }
        
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DGJSPGOTC01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DGJSPGOTC02_".$_SESSION['IDCARD']."_$now ";
    
    $query = "SELECT
        a.idbrspg,
        a.id_spg,
        b.nama,
        a.periode tglbr,
        a.icabangid,
        c.nama nama_cabang,
        a.jml_harikerja harikerja,
        a.total,
        a.realisasi,
        a.keterangan,
        a.total insentif, a.total gaji, a.keterangan hk, a.total rpmakan, a.total makan, a.total sewa, a.total pulsa, a.total parkir, a.total bbm,
        a.total rinsentif, a.total rgaji, a.total rmakan, a.total rsewa, a.total rpulsa, a.total rparkir, a.total rbbm,
        a.total sinsentif, a.total sgaji, a.total smakan, a.total ssewa, a.total spulsa, a.total sparkir, a.total sbbm
        FROM
        dbmaster.t_spg_gaji_br0 a
        JOIN mkt.spg b ON a.id_spg = b.id_spg
        LEFT JOIN mkt.icabang_o c on a.icabangid=c.icabangid_o 
        WHERE a.stsnonaktif<>'Y' and nodivisi='$pnobrdivisi'";

    $query ="CREATE temporary TABLE $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "UPDATE $tmp01 SET insentif=0, gaji=0, hk='', rpmakan=0, makan=0, sewa=0, pulsa=0, parkir=0, bbm=0");
    mysqli_query($cnmy, "UPDATE $tmp01 SET rinsentif=0, rgaji=0, rmakan=0, rsewa=0, rpulsa=0, rparkir=0, rbbm=0");//ralisasi
    mysqli_query($cnmy, "UPDATE $tmp01 SET sinsentif=0, sgaji=0, smakan=0, ssewa=0, spulsa=0, sparkir=0, sbbm=0");//selisih

    $query = "SELECT * FROM dbmaster.t_spg_gaji_br1 WHERE idbrspg IN (select distinct idbrspg FROM $tmp01)";
    $query ="CREATE temporary TABLE $tmp02 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


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
?>

<html>
<head>
    <?PHP 
        echo "<title>BR OTC GAJI SPG</title>";
     
        if ($_GET['ket']!="excel") {
            echo "<meta http-equiv='Expires' content='Mon, 01 Jan 2019 1:00:00 GMT'>";
            echo "<meta http-equiv='Pragma' content='no-cache'>";
            echo "<link rel='shortcut icon' href='images/icon.ico' />";
            echo "<link href='css/laporanbaru.css' rel='stylesheet'>";
            
            header("Cache-Control: no-cache, must-revalidate");
        }
        
    ?>
</head>

<body>
    
    <center><h2><u></u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td><h2>REKAP BR GAJI, UM : SDM, BC & SPG</h2></td><td><h2><?PHP echo "$periodegaji"; ?></h2></td></tr>
                <tr><td>NO</td><td><?PHP echo "$pnobrdivisi"; ?></td></tr>
                <tr><td>TANGGAL</td><td><?PHP echo "$periodeajukan"; ?></td></tr>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
    <br/>&nbsp;
    
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Cabang</th>
                    <th colspan="8">Usulan</th>
                    <th colspan="8">Realisasi</th>
                </tr>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">Insentif</th>
                <th align="center">Gaji</th>
                <th align="center">Uang Makan</th>
                <th align="center">Sewa Kendaraan</th>
                <th align="center">Pulsa</th>
                <th align="center">BBM</th>
                <th align="center">Parkir</th>
                <th align="center">Total</th>
                
                <th align="center">Insentif</th>
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
                
                $rgtotalinc=0;
                $rgtotalgaji=0;
                $rgtotalmakan=0;
                $rgtotalsewa=0;
                $rgtotalpulsa=0;
                $rgtotalbbm=0;
                $rgtotalparkir=0;
                $rgtotaltot=0;
                
                $sgtotalinc=0;
                $sgtotalgaji=0;
                $sgtotalmakan=0;
                $sgtotalsewa=0;
                $sgtotalpulsa=0;
                $sgtotalbbm=0;
                $sgtotalparkir=0;
                $sgtotaltot=0;
                
                $no=1;
                $query = "select icabangid, nama_cabang, sum(insentif) insentif, sum(gaji) gaji, sum(makan) makan, 
                        sum(sewa) sewa, sum(pulsa) pulsa, sum(bbm) bbm, sum(parkir) parkir, 
                        sum(rinsentif) rinsentif, sum(rgaji) rgaji, sum(rmakan) rmakan, sum(rsewa) rsewa, sum(rpulsa) rpulsa, sum(rbbm) rbbm, sum(rparkir) rparkir,
                        sum(sinsentif) sinsentif, sum(sgaji) sgaji, sum(smakan) smakan, sum(ssewa) ssewa, sum(spulsa) spulsa, sum(sbbm) sbbm, sum(sparkir) sparkir
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
                    
                    $ptotal=(double)$row['insentif']+(double)$row['gaji']+(double)$row['makan']+(double)$row['sewa']+(double)$row['pulsa']+(double)$row['bbm']+(double)$row['parkir'];
                    
                    $gtotalinc=(double)$gtotalinc+(double)$row['insentif'];
                    $gtotalgaji=(double)$gtotalgaji+(double)$row['gaji'];
                    $gtotalmakan=(double)$gtotalmakan+(double)$row['makan'];
                    $gtotalsewa=(double)$gtotalsewa+(double)$row['sewa'];
                    $gtotalpulsa=(double)$gtotalpulsa+(double)$row['pulsa'];
                    $gtotalbbm=(double)$gtotalbbm+(double)$row['bbm'];
                    $gtotalparkir=(double)$gtotalparkir+(double)$row['parkir'];
                    $gtotaltot=(double)$gtotaltot+(double)$ptotal;
                    
                    $ptotal=number_format($ptotal,0,",",",");
                    
                    //realisasi
                    $rpinsentif=number_format($row['rinsentif'],0,",",",");
                    $rpgaji=number_format($row['rgaji'],0,",",",");
                    $rpmakan=number_format($row['rmakan'],0,",",",");
                    $rpsewa=number_format($row['rsewa'],0,",",",");
                    $rppulsa=number_format($row['rpulsa'],0,",",",");
                    $rpbbm=number_format($row['rbbm'],0,",",",");
                    $rpparkir=number_format($row['rparkir'],0,",",",");
                    
                    $rptotal=(double)$row['rinsentif']+(double)$row['rgaji']+(double)$row['rmakan']+(double)$row['rsewa']+(double)$row['rpulsa']+(double)$row['rbbm']+(double)$row['rparkir'];
                    
                    $rgtotalinc=$rgtotalinc+$row['rinsentif'];
                    $rgtotalgaji=$rgtotalgaji+$row['rgaji'];
                    $rgtotalmakan=$rgtotalmakan+$row['rmakan'];
                    $rgtotalsewa=$rgtotalsewa+$row['rsewa'];
                    $rgtotalpulsa=$rgtotalpulsa+$row['rpulsa'];
                    $rgtotalbbm=$rgtotalbbm+$row['rbbm'];
                    $rgtotalparkir=$rgtotalparkir+$row['rparkir'];
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
                    
                    $sptotal=(double)$row['sinsentif']+(double)$row['sgaji']+(double)$row['smakan']+(double)$row['ssewa']+(double)$row['spulsa']+(double)$row['sbbm']+(double)$row['sparkir'];
                    
                    $sgtotalinc=$sgtotalinc+$row['sinsentif'];
                    $sgtotalgaji=$sgtotalgaji+$row['sgaji'];
                    $sgtotalmakan=$sgtotalmakan+$row['smakan'];
                    $sgtotalsewa=$sgtotalsewa+$row['ssewa'];
                    $sgtotalpulsa=$sgtotalpulsa+$row['spulsa'];
                    $sgtotalbbm=$sgtotalbbm+$row['sbbm'];
                    $sgtotalparkir=$sgtotalparkir+$row['sparkir'];
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
                    echo "<td nowrap align='right'>$pbbm</td>";
                    echo "<td nowrap align='right'>$pparkir</td>";
                    echo "<td nowrap align='right'>$ptotal</td>";
                    
                    $rpinsentif=""; $rpgaji=""; $rpmakan=""; $rpsewa="";
                    $rppulsa=""; $rpbbm=""; $rpparkir=""; $rptotal="";
                    
                    echo "<td nowrap align='right'>$rpinsentif</td>";
                    echo "<td nowrap align='right'>$rpgaji</td>";
                    echo "<td nowrap align='right'>$rpmakan</td>";
                    echo "<td nowrap align='right'>$rpsewa</td>";
                    echo "<td nowrap align='right'>$rppulsa</td>";
                    echo "<td nowrap align='right'>$rpbbm</td>";
                    echo "<td nowrap align='right'>$rpparkir</td>";
                    echo "<td nowrap align='right'>$rptotal</td>";
                    
                    
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
                
                //realisasi
                $rgtotalinc=number_format($rgtotalinc,0,",",",");
                $rgtotalgaji=number_format($rgtotalgaji,0,",",",");
                $rgtotalmakan=number_format($rgtotalmakan,0,",",",");
                $rgtotalsewa=number_format($rgtotalsewa,0,",",",");
                $rgtotalpulsa=number_format($rgtotalpulsa,0,",",",");
                $rgtotalbbm=number_format($rgtotalbbm,0,",",",");
                $rgtotalparkir=number_format($rgtotalparkir,0,",",",");
                $rgtotaltot=number_format($rgtotaltot,0,",",",");
                    
                $sgtotalinc=number_format($sgtotalinc,0,",",",");
                $sgtotalgaji=number_format($sgtotalgaji,0,",",",");
                $sgtotalmakan=number_format($sgtotalmakan,0,",",",");
                $sgtotalsewa=number_format($sgtotalsewa,0,",",",");
                $sgtotalpulsa=number_format($sgtotalpulsa,0,",",",");
                $sgtotalbbm=number_format($sgtotalbbm,0,",",",");
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
                echo "<td nowrap align='right'><b>$gtotalbbm</b></td>";
                echo "<td nowrap align='right'><b>$gtotalparkir</b></td>";
                echo "<td nowrap align='right'><b>$gtotaltot</b></td>";
                
                $rgtotalinc=""; $rgtotalgaji=""; $rgtotalmakan=""; $rgtotalsewa="";
                $rgtotalpulsa=""; $rgtotalbbm=""; $rgtotalparkir=""; $rgtotaltot="";
                
                echo "<td nowrap align='right'><b>$rgtotalinc</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalgaji</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalmakan</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalsewa</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalpulsa</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalbbm</b></td>";
                echo "<td nowrap align='right'><b>$rgtotalparkir</b></td>";
                echo "<td nowrap align='right'><b>$rgtotaltot</b></td>";
                
                echo "</tr>";
                    
            ?>
            </tbody>
        </table>
    
    <br/>&nbsp;
    <br/>&nbsp;
    
    <?PHP
        echo "<table class='tjudul' width='100%'>";
            echo "<tr>";

                echo "<td align='center'>";
                echo "Dibuat oleh,";
                if (!empty($namapengaju_ttd_fin1))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>DESI RATNA DEWI</b></td>";

                echo "<td align='center'>";
                echo "";
                if (!empty($namapengaju_ttd_fin2))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>SAIFUL RAHMAT</b></td>";

                echo "<td align='center'>";
                echo "Mengetahui,";
                if (!empty($namapengaju_ttd_fin3))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin3' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>M. ASYKUR</b></td>";


                echo "<td align='center'>";
                echo "";
                if (!empty($namapengaju_ttd1))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>FARIDA SOEWANTO</b></td>";

                echo "<td align='center'>";
                echo "Disetujui,";
                if (!empty($namapengaju_ttd2))
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                else
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                echo "<b>IRA BUDISUSETYO</b></td>";

            echo "</tr>";

        echo "</table>";

        echo "<br/>&nbsp;<br/>&nbsp;";
    ?>
    
</body>
</html>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP temporary TABLE $tmp01");
    mysqli_query($cnmy, "DROP temporary TABLE $tmp02");
    mysqli_close($cnmy);
?>