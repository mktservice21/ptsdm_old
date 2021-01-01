<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BIAYA RUTIN REK. BANK.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>REKAP BIAYA RUTIN REK. BANK</title>
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
    $tglnow = date("d/m/Y");
    
    
    $gmrheight = "100px";
    $ngbr_idinput="";
    $gbrttd_fin1="";
    $gbrttd_fin2="";
    $gbrttd_dir1="";
    $gbrttd_dir2="";
    
    $pnamadirapp1="FARIDA SOEWANTO";
    $pnamadirapp2="EVI KOSINA SANTOSO";
    
    $pnamadirapp=$pnamadirapp2;
    
    $namapengaju_ttd_fin1="";
    $namapengaju_ttd_fin2="";
    
    $namapengaju_ttd1="";
    $namapengaju_ttd2="";
    
    $ntgl_apv1="";
    $ntgl_apv2="";
    $ntgl_apv_dir1="";
    $ntgl_apv_dir2="";
    
    
    $pnomorbr="";
    
    if (isset($_GET['ispd'])) {
        $idinputspd=$_GET['ispd'];
        $_POST['bulan1']="2000-01-00";
        $_POST['e_periode']="";
        $_POST['sts_apv']="fin";
        
        $query = "select * from dbmaster.t_suratdana_br WHERE idinput='$idinputspd'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            if (!empty($ra['tglf']))
                $_POST['bulan1']=$ra['tglf'];
            
            $_POST['e_periode']=$ra['kodeperiode'];
            
            $pnomorbr= $ra['nodivisi'];
            
            $ngbr_idinput=$ra['idinput'];
            
            $gbrttd_fin1=$ra['gbr_apv1'];
            $gbrttd_fin2=$ra['gbr_apv2'];
            
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
    }
    
    $tgl01 = $_POST['bulan1'];
    $kdperiode = $_POST['e_periode'];
    $stsapv = $_POST['sts_apv'];
    
    
    $periode1 = date("Y-m", strtotime($tgl01));
    
    $fperiode = " AND DATE_FORMAT(br.bulan, '%Y-%m') = '$periode1' ";
    
    $per1 = date("F Y", strtotime($tgl01));
    $pbulan = date("F", strtotime($tgl01));
    
    $fstsapv = "";
    $e_stsapv="Semua Data";
    if ($stsapv == "fin") {
        $fstsapv = " AND ifnull(br.tgl_fin,'') <> '' AND ifnull(br.tgl_fin,'0000-00-00') <> '0000-00-00' ";
        $e_stsapv="Sudah Proses Finance";
    }elseif ($stsapv == "belumfin") {
        $fstsapv = " AND (ifnull(br.tgl_fin,'') = '' OR ifnull(br.tgl_fin,'0000-00-00') = '0000-00-00') ";
        $e_stsapv="Belum Proses Finance";
    }
    
    $kdper1 = date("01/m/Y", strtotime($tgl01));
    $kdper2 = date("15/m/Y", strtotime($tgl01));
    $tglinput = date("Y-m-01", strtotime($tgl01));
    if ($kdperiode==2) {
        $kdper1 = date("16/m/Y", strtotime($tgl01));
        $kdper2 = date("t/m/Y", strtotime($tgl01));
        $tglinput = date("Y-m-16", strtotime($tgl01));
    }
    
    //cari no br / no divisi yang sudah di save
    if (empty($pnomorbr)) {
        $nnnodiv="";
        if (isset($_POST['e_nomordiv'])) $nnnodiv=$_POST['e_nomordiv'];
        
        $pkode="1";
        $psubkode="03";
        $query = "SELECT nodivisi as pnomor FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi<>'OTC' AND nodivisi='$nnnodiv'";
        /*
        $query = "SELECT nodivisi as pnomor "
                . " FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi<>'OTC' AND kodeid='$pkode' AND subkode='$psubkode' AND "
                . " tgl='$tglinput' LIMIT 1";
         * 
         */
        $showkan= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($showkan);
        if ($ketemu>0){
            $s= mysqli_fetch_array($showkan);
            if (!empty($s['pnomor'])) { 
                $pnomorbr= $s['pnomor'];
            }
        }
    }
    //end cari no br / no divisi yang sudah di save
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DBRROTCPBLL01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DBRROTCPBLL02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DBRROTCPBLL03_".$_SESSION['IDCARD']."_$now ";
    
    $query = "SELECT
	br.idrutin,
	br.tgl,
	br.karyawanid,
	br.icabangid,
	br.areaid,
	br.jumlah,
	br.keterangan,
	k.nama,
	a.nama nama_area,
        br.divisi, br.nama_karyawan
        FROM
                dbmaster.t_brrutin0 AS br
        LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
        LEFT JOIN MKT.iarea AS a ON br.areaid = a.areaId and br.icabangid=a.iCabangId WHERE br.kode=1 AND br.stsnonaktif <> 'Y' AND br.divisi <> 'OTC' AND br.kodeperiode='$kdperiode' $fperiode $fstsapv";
    
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    
    $query = "select karyawanid, sum(jumlah) as AMOUNT, CAST(''  AS char(10)) icabangid, CAST(''  AS char(10)) areaid, CAST(''  AS char(10)) divisi from $tmp02 Group by karyawanid";
    $query = "create temporary table $tmp03 ($query)";
    mysqli_query($cnit, $query);
    
    $query = "UPDATE $tmp03 set icabangid=ifnull((select icabangid from $tmp02 where $tmp02.karyawanid=$tmp03.karyawanid LIMIT 1),0)";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp03 set areaid=ifnull((select areaid from $tmp02 where $tmp02.karyawanid=$tmp03.karyawanid LIMIT 1),0)";
    mysqli_query($cnit, $query);
    $query = "UPDATE $tmp03 set divisi=ifnull((select divisi from $tmp02 where $tmp02.karyawanid=$tmp03.karyawanid LIMIT 1),0)";
    mysqli_query($cnit, $query);
    
    
    $query = "select distinct br.karyawanid, k.nama, br.areaid, a.nama nama_area, br.icabangid, c.nama nama_cabang, br.divisi 
        , AMOUNT, bn.atasnama_b, bn.norek_b from $tmp03 br JOIN hrd.karyawan k ON br.karyawanid=k.karyawanId 
        LEFT JOIN MKT.icabang c on br.icabangid=c.iCabangId LEFT JOIN MKT.iarea a on 
        br.areaid=a.areaId And br.icabangid=a.iCabangId 
        LEFT JOIN dbmaster.t_karyawan_bank_rutin bn on br.karyawanid=bn.karyawanid";
    $query = "create temporary table $tmp01 ($query)";
    mysqli_query($cnit, $query);
    
    mysqli_query($cnit, "DELETE FROM $tmp01 WHERE karyawanid='$_SESSION[KRYNONE]'");
    $query = "INSERT INTO $tmp01 (karyawanid, nama, divisi, AMOUNT, areaid, nama_area, icabangid)"
            . "SELECT karyawanid, nama_karyawan, divisi, jumlah, areaid, nama_area, icabangid FROM $tmp02 WHERE "
            . " karyawanid='$_SESSION[KRYNONE]'";
    mysqli_query($cnit, $query);
    
    
    
    if (!isset($_GET['ispd'])) {
        
        $query = "select * from dbmaster.t_suratdana_br WHERE nodivisi='$pnomorbr' AND stsnonaktif<>'Y'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            if (!empty($ra['tglf']))
                $_POST['bulan1']=$ra['tglf'];
            
            $_POST['e_periode']=$ra['kodeperiode'];
            
            $pnomorbr= $ra['nodivisi'];
            
            $ngbr_idinput=$ra['idinput'];
            
            $gbrttd_fin1=$ra['gbr_apv1'];
            $gbrttd_fin2=$ra['gbr_apv2'];
            
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
    }
    
?>

    <style> .str{ mso-number-format:\@; padding-left:5px; } </style>
    
    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="200px"><b>Rekap Biaya Rutin Per </b></td><td><?PHP echo "$per1 "; ?></td></tr>
                <tr><td width="150px"><b>Periode </b></td><td><?PHP echo "$kdper1 - $kdper2"; ?></td></tr>
                <tr><td width="150px"><b>No.BR </b></td><td><?PHP echo "$pnomorbr"; ?></td></tr>
                <tr><td><b>Status Approve </b></td><td><?PHP echo "$e_stsapv"; ?></td></tr>
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
                <th align="center">No</th>
                <th align="center">KaryawanId</th>
                <th align="center">Nama</th>
                <th align="center">Divisi</th>
                <th align="center">Amount</th>
                <th align="center">Atas Nama Bank</th>
                <th align="center">No. Rekening</th>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $total=0;
                    $totalpot=0;
                    $totalpen=0;
                    $totalbay=0;
                    
                    $query = "select distinct divisi from $tmp01 order by CASE WHEN divisi='HO' then 'ZHO' ELSE divisi END";
                    $tampil = mysqli_query($cnit, $query);
                    while ($row1=mysqli_fetch_array($tampil)) {
                        $ndivisi=$row1['divisi'];
                        $no=1;
                        
                        $query = "select * from $tmp01 WHERE divisi='$ndivisi' order by divisi, nama, karyawanid";
                        $result = mysqli_query($cnit, $query);
                    
                        while ($row=mysqli_fetch_array($result)) {
                            $noid=$row['karyawanid'];
                            $nama=$row['nama'];
                            $area=$row['nama_area'];
                            $pdivisi=$row['divisi'];
                            
                            if ($pdivisi=="CAN") $pdivisi="CANARY";
                            $jumlah=number_format($row['AMOUNT'],0,",",",");
                            
                            if ($_GET['ket']=="excel" AND $_SESSION['IDCARD']=="0000000143") {
                                $jumlah=number_format($row['AMOUNT'],0,".",".");
                            }
                            $total = $total + $row['AMOUNT'];
                            
                            $patas_nama=$row['atasnama_b'];
                            $pnorekbank=$row['norek_b'];
                            
                            if ($_GET['ket']=="excel") {
                                //if (!empty($pnorekbank)) $pnorekbank="'".$pnorekbank;
                            }
                            
                            echo "<tr>";
                            echo "<td align='center'>$no</td>";
                            echo "<td class='str' nowrap>$noid</td>";
                            echo "<td nowrap style='padding-left:5px;'>$nama</td>";
                            echo "<td align='center' nowrap>$pdivisi</td>";
                            echo "<td align='right' style='padding-right:5px;'>$jumlah</td>";
                            
                            echo "<td nowrap style='padding-left:5px;'>$patas_nama</td>";
                            echo "<td class='str' nowrap>$pnorekbank</td>";
                            echo "</tr>";
                            
                            $no++;
                        }
                        
                        echo "<tr>";
                        echo "<td align='center'></td>";
                        echo "<td nowrap style='padding-left:5px;'></td>";
                        echo "<td align='center' nowrap></td>";
                        echo "<td align='right' style='padding-right:5px;'></td>";
                        echo "<td align='left'></td>";
                        echo "<td align='left'></td>";
                        echo "<td align='left'></td>";
                        echo "</tr>";
                        
                    }
                    
                        if ($_GET['ket']=="excel" AND $_SESSION['IDCARD']=="0000000143") {
                            $total=number_format($total,0,".",".");
                        }else{
                            $total=number_format($total,0,",",",");
                        }
                        
                        //TOTAL
                        echo "<tr>";
                        echo "<td colspan=3 align='right'><b>Total : </b></td>";
                        echo "<td align='right' style='padding-right:5px;'><b>$total</b></td>";
                        echo "<td align='left'></td>";
                        echo "<td align='left'></td>";
                        echo "<td align='left'></td>";
                        echo "</tr>";
                    
                    
                    
                    mysqli_query($cnit, "drop temporary table $tmp01");
                    mysqli_query($cnit, "drop temporary table $tmp02");
                    mysqli_close($cnit);
                ?>
            </tbody>
        </table>
        <br/>&nbsp;<br/>&nbsp;
        <?PHP
        
        if ($_GET['ket']=="excel") {
    
            echo "<table class='tjudul' width='100%'>";
                echo "<tr>";

                    echo "<td align='center'>";
                    echo "Yang Membuat,";
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>CHAERIAH</b></td>";


                    echo "<td align='center'>";
                    echo "Checker,";
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv2<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>MARIANNE PRASANTI</b></td>";


                    echo "<td align='center'>";
                    echo "Mengetahui,";
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv_dir1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>$pnamadirapp</b></td>";
                echo "</tr>";

            echo "</table>";
            
        }else{
            
            echo "<table class='tjudul' width='100%'>";
                echo "<tr>";

                    echo "<td align='center'>";
                    echo "Yang Membuat,";
                    if (!empty($namapengaju_ttd_fin1))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>CHAERIAH</b></td>";


                    echo "<td align='center'>";
                    echo "Checker,";
                    if (!empty($namapengaju_ttd_fin2))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>MARIANNE PRASANTI</b></td>";


                    echo "<td align='center'>";
                    echo "Mengetahui,";
                    if (!empty($namapengaju_ttd1))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>$pnamadirapp</b></td>";

                    /*
                    echo "<td align='center'>";
                    echo "Disetujui,";
                    if (!empty($namapengaju_ttd2))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>IRA BUDISUSETYO</b></td>";
                    */
                echo "</tr>";

            echo "</table>";
            
        }
        
        
        /*
        echo "<table width='100%' border='0px' >";
        echo "<tr align='center'>";
        echo "<td>Yang membuat,</td> <td></td> <td>Checker</td> <td></td> <td>Menyetujui,</td>";
        echo "</tr>";

        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";

        echo "<tr align='center'>";
        echo "<td>(..................)</td> <td></td> <td>(Marianne Prasanti)</td> <td></td> <td>($pnamadirapp)</td>";
        echo "</tr>";
        
        echo "</table>";
         * 
         */
        ?>
        <br/>&nbsp;<br/>&nbsp;
</body>
</html>
