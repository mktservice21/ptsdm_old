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
    
    $pnobukti="";
    $ptglkeluar="";
    
    
    if (isset($_GET['ispd'])) {
        $idinputspd=$_GET['ispd'];
        
        //$query = "select * from dbmaster.t_suratdana_br WHERE idinput='$idinputspd'";
        $query = "select a.*, b.tanggal as tglkeluar, b.nobukti from dbmaster.t_suratdana_br a LEFT JOIN "
                . " (select idinput, tanggal, nobukti from dbmaster.t_suratdana_bank WHERE idinput='$idinputspd' AND stsinput='K') "
                . " as b on a.idinput=b.idinput"
                . " WHERE a.idinput='$idinputspd'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            
            $pnobukti=$ra['nobukti'];
            
            if (!empty($ra['tglkeluar']) AND $ra['tglkeluar']<>"0000-00-00") $ptglkeluar = date("d-M-y", strtotime($ra['tglkeluar']));
            
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
    $query = "select distinct IFNULL(bridinput,'') bridinput, CAST(urutan AS CHAR(150)) as urutan, amount, trans_ke FROM dbmaster.t_suratdana_br1 WHERE idinput='$idinputspd'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //END 
    
    $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
        a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2, a.iCabangId, d.nama nama_cabang from hrd.br0 a 
        LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId
        LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId 
        LEFT JOIN MKT.icabang d on a.iCabangId=d.iCabangId 
        WHERE a.brId IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp02)";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.*, b.amount, b.urutan, b.trans_ke from $tmp01 a JOIN $tmp02 b on a.brId=b.bridinput";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "UPDATE $tmp03 SET urutan=noslip where IFNULL(urutan,'0')='0'");
    mysqli_query($cnmy, "UPDATE $tmp03 SET urutan=realisasi1 where IFNULL(urutan,'')=''");
    
    if ($pjnsrpt=="2") {//khusus klaim bukan advance
        mysqli_query($cnmy, "UPDATE $tmp03 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)>0"); 
    }else{
        mysqli_query($cnmy, "UPDATE $tmp03 SET jumlah=amount");
    }
    
    
    /*
    if ($pjnsrpt=="2") {//khusus klaim bukan advance
        mysqli_query($cnmy, "UPDATE $tmp01 SET jumlah=jumlah1 WHERE IFNULL(jumlah1,0)>0");
    }
   */
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
                        
                        $query = "select distinct urutan from $tmp03 order by urutan";
                        $tampil1=mysqli_query($cnmy, $query);
                        while ($row1= mysqli_fetch_array($tampil1)) {
                            $pnourut_ = $row1['urutan'];
                            $njml=0;
                            $tot_perslip_c=0;
                            
                            $nmyno=$no;
                            $query = "select * from $tmp03 WHERE urutan='$pnourut_' order by urutan, noslip, realisasi1, nama_karyawan, brId";
                            $tampil=mysqli_query($cnmy, $query);
                            while ($row= mysqli_fetch_array($tampil)) {
                                $pnoslip = $row['noslip'];
                                $pbrid = $row['brId'];
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

                                $ptranske = $row['trans_ke'];
                                
                                echo "<tr>";
                                echo "<td>$pnmdokter</td>";
                                echo "<td nowrap>$pnoslip</td>";
                                echo "<td>$pnamakaryawan</td>";
                                echo "<td nowrap>$pdaerah</td>";
                                echo "<td>$paktivitas1</td>";
                                echo "<td>$prealisasi1</td>";
                                echo "<td nowrap align='right'>$pjumlah</td>";
                                if ($ptranske=="NB")
                                    echo "<td nowrap align='center'><b>$nmyno</b></td>";
                                else
                                    echo "<td nowrap align='center'>$nmyno</td>";
                                echo "</tr>";

                                $nmyno="";
                                //$no++;
                                $njml++;
                            }
                            $no++;
                            
                            if ((double)$njml>1) {
                                $tot_perslip_c=number_format($tot_perslip_c,0,",",",");
                                echo "<tr>";
                                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
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
                        echo "<tr style='font-size:16px;'>";
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
                        
                        $query = "select distinct urutan from $tmp03 order by urutan";
                        $tampil1=mysqli_query($cnmy, $query);
                        while ($row1= mysqli_fetch_array($tampil1)) {
                            $pnourut_ = $row1['urutan'];
                            $njml=0;
                            $tot_perslip_c=0;
                            
                            $nmyno=$no;
                            $query = "select * from $tmp03 WHERE urutan='$pnourut_' order by urutan, noslip, realisasi1, nama_karyawan, brId";
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


                                //if (empty($pnmdokter)) $pnmdokter=$prealisasi1;

                                $ptranske = $row['trans_ke'];
                                
                                echo "<tr>";
                                if ($ptranske=="NB")
                                    echo "<td nowrap><b>$nmyno</b></td>";
                                else
                                    echo "<td nowrap>$nmyno</td>";
                                
                                echo "<td nowrap>$pnoslip</td>";
                                echo "<td nowrap>$ptgltrans</td>";
                                echo "<td>$pnamakaryawan</td>";
                                echo "<td>$pnmdokter</td>";
                                echo "<td>$paktivitas1</td>";
                                echo "<td>$prealisasi1</td>";
                                echo "<td nowrap align='right'>$pjumlah</td>";
                                echo "</tr>";
                                
                                $nmyno="";
                                //$no++;
                                $njml++;
                                
                            }
                            $no++;
                            
                            if ((double)$njml>1) {
                                $tot_perslip_c=number_format($tot_perslip_c,0,",",",");
                                echo "<tr>";
                                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                                echo "<td>&nbsp</td>";
                                echo "<td align='right'><b>$tot_perslip_c</b></td>";
                                echo "</tr>";
                                
                                echo "<tr>";
                                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                                echo "<td>&nbsp</td>";
                                echo "<td align='right'><b></b></td>";
                                echo "</tr>";
                            }else{
                            }
                        
                        }
                        
                        echo "<tr>";
                        echo "<td colspan='8'></td>";
                        echo "</tr>";
                        
                        $gtotal=number_format($gtotal,0,",",",");
                        echo "<tr style='font-size:16px;'>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td><b>TOTAL</b></td>";
                        echo "<td nowrap align='right'><b>$gtotal</b></td>";
                        echo "</tr>";


                    ?>
                </tbody>
            </table>
        <?php   
        }
        ?>
    
<?PHP
hapusdata:
    mysqli_query($cnmy, "drop temporary table $tmp01");
    mysqli_query($cnmy, "drop temporary table $tmp02");
    mysqli_query($cnmy, "drop temporary table $tmp03");
?>
    
        <br/>&nbsp;<br/>&nbsp;
        <?PHP
        
            $nposisi="left";
            if ($padvance!="B") $nposisi="center";
            
            echo "<table class='tjudul' width='100%'>";
                echo "<tr>";

                    echo "<td align='$nposisi'>";
                    echo "Yang Membuat,";
                    if (!empty($namapengaju_ttd_fin1))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>ERNILYA</b></td>";

                if ($padvance!="B") {
                    
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
                    echo "<b>FARIDA SOEWANTO</b></td>";


                    echo "<td align='center'>";
                    echo "Disetujui,";
                    if (!empty($namapengaju_ttd2))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>IRA BUDISUSETYO</b></td>";
                    
                }
                
                
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
