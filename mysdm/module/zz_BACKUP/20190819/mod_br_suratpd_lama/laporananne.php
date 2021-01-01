<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REPORT BR.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>REPORT BR</title>
<?PHP if ($_GET['ket']!="excel") { ?>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2019 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <link href="css/laporanbaru.css" rel="stylesheet">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
<?PHP } ?>
</head>

<body>
<?php
    $gmrheight = "100px";
    $ngbr_idinput="";
    $gbrttd_fin1="";
    $gbrttd_fin2="";
    $gbrttd_dir1="";
    $gbrttd_dir2="";
    
    
    $tglnow = date("d/m/Y");
    $periode1 = date("d F Y");
    $nodivisi="";
    $pdivisi="";
    $pjnsrpt="";
    $padvance="A";
    if (isset($_GET['ispd'])) {
        $idinputspd=$_GET['ispd'];
        
        $query = "select * from dbmaster.t_suratdana_br WHERE idinput='$idinputspd'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            
            $nodivisi=$ra['nodivisi'];
            $padvance=$ra['jenis_rpt'];
            $pjnsrpt=$ra['kodeid'];
            $pdivisi=$ra['divisi'];
            $tgl=$ra['tgl'];
            if ($_GET['act']=="rekapbr") {
                $periode1 = date("d-M-y", strtotime($tgl));
            }else{
                $periode1 = date("d F Y", strtotime($tgl));
            }
            
            
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
            }
            
            if (!empty($gbrttd_fin2)) {
                $data="data:".$gbrttd_fin2;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd_fin2="imgfin2_".$ngbr_idinput."TTDSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin2, $data);
            }
            
            if (!empty($gbrttd_dir1)) {
                $data="data:".$gbrttd_dir1;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd1="imgdr1_".$ngbr_idinput."TTDSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd1, $data);
            }
            
            if (!empty($gbrttd_dir2)) {
                $data="data:".$gbrttd_dir2;
                $data=str_replace(' ','+',$data);
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $namapengaju_ttd2="imgdr2_".$ngbr_idinput."TTDSPD_.png";
                file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd2, $data);
            }
            
            
        }
    }
    
    $nmadvance="* Advance";
    if ($padvance=="K") $nmadvance="* Klaim";
    if ($padvance=="B") $nmadvance="* BELUM ADA KUITANSI";
    
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSETHZ01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETHZ02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETHZ03_".$userid."_$now ";
    
    //cari yang sudah ada
    $query = "select distinct IFNULL(bridinput,'') bridinput FROM dbmaster.t_suratdana_br1 WHERE idinput='$idinputspd'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    //END 
    
    $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
        a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2, a.iCabangId, d.nama nama_cabang from hrd.br0 a 
        LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId
        LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId 
        LEFT JOIN MKT.icabang d on a.iCabangId=d.iCabangId 
        WHERE a.brId IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp02)";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if ($pjnsrpt=="2") {//khusus klaim bukan advance
        mysqli_query($cnmy, "UPDATE $tmp01 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)>0");
    }
   
?>

    <div id="kotakjudul" style="margin-bottom: -30px;">
        <div id="isikiri">
        <?php
        if ($_GET['act']=="rekapbr") {
        ?>
            <table class='tjudul' width='100%'>                
                <tr><td width="200px">To : </td><td>Sdr. Lina (Accounting)</td></tr>
                <tr><td width="150px">PT. SDM - Surabaya</td><td></td></tr>
                <tr><td width="150px"><b>&nbsp;</b></td><td></td></tr>
                <tr><td width="150px"><b>Rekap Budget Team <?PHP echo $pdivisi; ?></b></td><td></td></tr>
                <tr><td width="150px"><b>No. <?PHP echo $nodivisi; ?></b></td><td></td></tr>
                <tr><td width="150px"><b><?PHP echo $periode1; ?></b></td><td></td></tr>
            </table>
        <?PHP
        }else{
        ?>
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>&nbsp;</b></td><td></td></tr>
                <tr><td width="200px"><b>To : </b></td><td>Sdr. Lina (Accounting)</td></tr>
            </table>
        <?php
        }
        ?>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    <center><h2>
        <?php
        if ($_GET['act']=="rekapbr") {
            
        }else{
            echo "REPORT SURABAYA BR $pdivisi TGL : $periode1 ($nodivisi)";
        }
        ?>
    </h2></center>
    
    <div class="clearfix"></div>
    <?PHP echo "<div><table style='color:red; border:0px' width='90%'><tr><td align='right'>$nmadvance</td></tr></table></div>"; ?>
        <?php
        if ($_GET['act']=="rekapbr") {
        ?>
            <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
                <thead>
                    <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center">DOKTER</th>
                    <th align="center">NO. SLIP</th>
                    <th align="center">PENGAJUAN</th>
                    <th align="center">DAERAH</th>
                    <th align="center">KETERANGAN</th>
                    <th align="center">REALISASI</th>
                    <th align="center">CREDIT</th>
                    <th align="center">NO</th>
                </thead>
                <tbody>
                    <?PHP
                        $gtotal=0;
                        $no=1;
                        
                        $query = "select distinct noslip from $tmp01 order by noslip";
                        $tampil1=mysqli_query($cnmy, $query);
                        while ($row1= mysqli_fetch_array($tampil1)) {
                            $pnoslip_ = $row1['noslip'];
                            $njml=0;
                            $tot_perslip_c=0;
                            
                            $nmyno=$no;
                            $query = "select * from $tmp01 WHERE noslip='$pnoslip_' order by noslip, nama_karyawan, brId";
                            $tampil=mysqli_query($cnmy, $query);
                            while ($row= mysqli_fetch_array($tampil)) {
                                $pbrid = $row['brId'];
                                $pnoslip = $row['noslip'];
                                $ptgltrans = "";
                                if (!empty($row['tgltrans']))
                                    $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                                $pnamakaryawan = $row['nama_karyawan'];
                                $piddokter = $row['dokterId'];
                                $pnmdokter = $row['nama_dokter'];
                                if (empty($pnmdokter)) $pnmdokter = $row['dokter'];
                                $paktivitas1 = $row['aktivitas1'];
                                $paktivitas2 = $row['aktivitas2'];
                                $prealisasi1 = $row['realisasi1'];
                                $pdivisi = $row['divprodid'];

                                $pjumlah = $row['jumlah'];
                                $pjmlreal = $row['realisasi2'];

                                $tot_perslip_c=(double)$tot_perslip_c+(double)$row['jumlah'];
                                $gtotal=(double)$gtotal+(double)$row['jumlah'];

                                $pjumlah=number_format($row['jumlah'],0,",",",");
                                if (!empty($row['realisasi2']))
                                    $pjmlreal=number_format($row['realisasi2'],0,",",",");

                                if (empty($pnmdokter)) $pnmdokter=$prealisasi1;

                                $pdaerah = $row['nama_cabang'];
                                if ($pdaerah=="ETH - HO") $pdaerah= "HO";

                                echo "<tr>";
                                echo "<td>$pnmdokter</td>";
                                echo "<td nowrap>$pnoslip</td>";
                                echo "<td>$pnamakaryawan</td>";
                                echo "<td nowrap>$pdaerah</td>";
                                echo "<td>$paktivitas1</td>";
                                echo "<td>$prealisasi1</td>";
                                echo "<td nowrap align='right'>$pjumlah</td>";
                                echo "<td nowrap align='center'>$nmyno</td>";
                                echo "</tr>";
                                
                                $nmyno="";
                                //$no++;
                                $njml++;
                            }
                            $no++;
                            
                            
                            if ((double)$njml>1) {
                                echo "<tr>";
                                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                                $tot_perslip_c=number_format($tot_perslip_c,0,",",",");
                                echo "<td align='right'><b>$tot_perslip_c</b></td>";
                                echo "<td>&nbsp</td>";
                                echo "</tr>";
                                echo "<tr>";
                                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp</td><td>&nbsp</td>";
                                echo "</tr>";
                            }else{
                                
                            }
                        
                        }
                        
                        
                        echo "<tr>";
                        echo "<td colspan='8'></td>";
                        echo "</tr>";
                        $gtotal=number_format($gtotal,0,",",",");
                        echo "<tr>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td><b>TOTAL</b></td>";
                        echo "<td nowrap align='right'><b>$gtotal</b></td>";
                        echo "<td></td>";
                        echo "</tr>";

                        mysqli_query($cnmy, "drop temporary table $tmp01");
                        mysqli_query($cnmy, "drop temporary table $tmp02");
                    ?>
                </tbody>
            </table>
        <?PHP
        }else{
        ?>
            <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
                <thead>
                    <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center">No</th>
                    <th align="center">NO. SLIP</th>
                    <th align="center">TGL TRANSFER</th>
                    <th align="center">NAMA PEMBUAT</th>
                    <th align="center">NAMA DOKTER</th>
                    <th align="center">KETERANGAN</th>
                    <th align="center">NAMA REALISASI</th>
                    <th align="center">JUMLAH</th>
                </thead>
                <tbody>
                    <?PHP
                        $gtotal=0;
                        $no=1;
                        $query = "select * from $tmp01 order by noslip, brId";
                        $tampil=mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $pbrid = $row['brId'];
                            $pnoslip = $row['noslip'];
                            $ptgltrans = "";
                            if (!empty($row['tgltrans']))
                                $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));

                            $pnamakaryawan = $row['nama_karyawan'];
                            $piddokter = $row['dokterId'];
                            $pnmdokter = $row['nama_dokter'];
                            if (empty($pnmdokter)) $pnmdokter = $row['dokter'];
                            $paktivitas1 = $row['aktivitas1'];
                            $paktivitas2 = $row['aktivitas2'];
                            $prealisasi1 = $row['realisasi1'];
                            $pdivisi = $row['divprodid'];

                            $pjumlah = $row['jumlah'];
                            $pjmlreal = $row['realisasi2'];

                            $gtotal=$gtotal+$row['jumlah'];

                            $pjumlah=number_format($row['jumlah'],0,",",",");
                            if (!empty($row['realisasi2']))
                                $pjmlreal=number_format($row['realisasi2'],0,",",",");


                            //if (empty($pnmdokter)) $pnmdokter=$prealisasi1;


                            echo "<tr>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td>$pnamakaryawan</td>";
                            echo "<td>$pnmdokter</td>";
                            echo "<td>$paktivitas1</td>";
                            echo "<td>$prealisasi1</td>";
                            echo "<td nowrap align='right'>$pjumlah</td>";
                            echo "</tr>";

                            $no++;
                        }
                        $gtotal=number_format($gtotal,0,",",",");
                        echo "<tr>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td><b>TOTAL</b></td>";
                        echo "<td nowrap align='right'><b>$gtotal</b></td>";
                        echo "</tr>";

                        mysqli_query($cnmy, "drop temporary table $tmp01");
                        mysqli_query($cnmy, "drop temporary table $tmp02");
                    ?>
                </tbody>
            </table>
        <?php   
        }
        ?>
    
    
    
        <br/>&nbsp;<br/>&nbsp;
        <?PHP
        
            echo "<table class='tjudul' width='100%'>";
                echo "<tr>";

                    echo "<td align='center'>";
                    echo "Yang Membuat,";
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
                    
                    
        /*
        echo "<table width='100%' style='border:0px;' >";
        echo "<tr align='center'>";
        //echo "<td>Yang membuat,</td> <td></td> <td>Checker</td> <td></td> <td>Menyetujui,</td>";
        echo "<td>Yang membuat,</td> <td></td> <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td></td> <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
        echo "</tr>";

        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";

        echo "<tr align='center'>";
        //echo "<td>Ernilya</td> <td></td> <td>(Marianne Prasanti)</td> <td></td> <td>(dr. Farida Soewanto)</td>";
        echo "<td>Ernilya</td> <td></td> <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td> <td></td> <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
        echo "</tr>";
        
        echo "</table>";
         * 
         */
        ?>
        <br/>&nbsp;<br/>&nbsp;
</body>
</html>
