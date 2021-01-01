<?PHP
    session_start();
    $mact=$_GET['act'];
    if ($_GET['ket']=="excel") {
        $now_=date("mdYhis");
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        if ($mact=="viewbrklaim")
            header("Content-Disposition: attachment; filename=KLAIM_DISCOUNT_BR_$now_.xls");
        else
            header("Content-Disposition: attachment; filename=CASH ADVANCE_BR_$now_.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
     <?PHP if ($mact=="viewbrklaim") { 
         echo "<title>KLAIM DISCOUNT - BR</title>";
     }else{
         echo "<title>CASH ADVANCE - BR</title>";
     }
    ?>  
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
    
    $nmadvance="**Cash Advance";
    if ($padvance=="K") $nmadvance="* Klaim";
    if ($padvance=="B") $nmadvance="* BELUM ADA KUITANSI";
    if ($padvance=="D") $nmadvance="* Klaim Discount";
    
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSETHZ01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETHZ02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETHZ03_".$userid."_$now ";
    
    //cari yang sudah ada
    $query = "select distinct IFNULL(bridinput,'') bridinput, CAST(urutan AS CHAR(150)) as urutan, amount FROM dbmaster.t_suratdana_br1 WHERE idinput='$idinputspd'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //END 
    if ($mact=="viewbrklaim") {
        $query = "select a.DIVISI divprodid, a.klaimId brId, a.karyawanid karyawanId, c.nama nama_karyawan,
            a.distid dokterId, b.nama nama_dokter, a.aktivitas1, '' aktivitas2, a.jumlah, a.tgl, a.tgltrans, 
            a.realisasi1, a.noslip, a.COA4, d.NAMA4, 0 jumlah1, 0 realisasi2, '' iCabangId, '' nama_cabang  
            from hrd.klaim a LEFT JOIN MKT.distrib0 b on a.distid=b.distid 
            LEFT JOIN hrd.karyawan c on a.karyawanid=c.karyawanId 
            LEFT JOIN dbmaster.coa_level4 d on a.COA4=d.COA4
            WHERE a.klaimId IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp02) ";
    }else{
        $query = "select a.divprodid, a.brId, a.tgl, a.noslip, a.tgltrans, a.karyawanId, c.nama nama_karyawan, a.dokterId, a.dokter, b.nama nama_dokter,
            a.aktivitas1, a.aktivitas2, a.realisasi1, a.jumlah, jumlah1, a.realisasi2, a.iCabangId, d.nama nama_cabang, a.COA4, e.NAMA4 from hrd.br0 a 
            LEFT JOIN hrd.dokter b on a.dokterId=b.dokterId
            LEFT JOIN hrd.karyawan c on a.karyawanId=c.karyawanId 
            LEFT JOIN MKT.icabang d on a.iCabangId=d.iCabangId
            LEFT JOIN dbmaster.coa_level4 e on a.COA4=e.COA4
            WHERE a.brId IN (select DISTINCT IFNULL(bridinput,'') bridinput FROM $tmp02)";
    }
    
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    /*
    if ($mact=="viewbrklaim") {        
    }else{
        $query = "UPDATE $tmp01 a SET a.jumlah=(select b.amount from $tmp02 b WHERE a.brId=b.bridinput LIMIT 1)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    */
    
    
    $query = "select a.*, b.amount, b.urutan from $tmp01 a JOIN $tmp02 b on a.brId=b.bridinput";
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
            <table class='tjudul' width='100%'>                
                <tr><td width="200px">To : </td><td>Sdr. Lina (Finance)</td></tr>
                <tr><td width="150px"><b>&nbsp;</b></td><td></td></tr>
                <tr><td width="150px"><b>Budget Request Team <?PHP echo $pdivisi; ?> : </b></td><td><?PHP echo $nodivisi; ?></td></tr>
                <?PHP if ($mact=="viewbrklaim") { ?>
                    <tr><td width="150px">**Mau Minta Uang : </td><td><?PHP echo $periode1; ?></td></tr>
                <?PHP }else{ ?>
                    <tr><td width="150px"><b>**Cash Advance : </b></td><td><b><?PHP echo $periode1; ?></b></td></tr>
                <?PHP } ?>
                <tr><td width="150px">&nbsp;</td><td></td></tr>
            </table>
        </div>
        <div id="isikanan">
            
        </div>
        <div class="clearfix"></div>
    </div>
    
    <div class="clearfix"></div>
    <?PHP echo "<div><table style='color:blue; border:0px' width='90%'><tr><td align='right'>$nmadvance</td></tr></table></div>"; ?>

    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
            <?PHP if ($mact=="viewbrklaim") { ?>
                <th align="center">Date</th>
                <th align="center">Bukti</th>
                <th align="center">KODE</th>
                <th align="center">PERKIRAAN</th>
                <th align="center">DOKTER/SUPPLIER/CUSTOMER</th>
                <th align="center">NO. SLIP</th>
                <th align="center">PENGAJUAN</th>
                <th align="center">KETERANGAN</th>
                <th align="center">Credit</th>
                <th align="center">No.</th>
            <?PHP }else{ ?>
                <?PHP if ($padvance=="Ka") { ?>
                    <th align="center">Date</th>
                    <th align="center">DOKTER/SUPPLIER/CUSTOMER</th>
                    <th align="center">NO. SLIP</th>
                    <th align="center">PENGAJUAN</th>
                    <th align="center">KETERANGAN</th>
                    <th align="center">REALISASI</th>
                    <?PHP if ($_GET['ket']=="excel") {
                        echo "<th align='center'></th>";
                    } ?>
                    <th align="center">Debit</th>
                    <th align="center">Credit</th>
                    <th align="center">Saldo</th>
                    <th align="center">No.</th>
                <?PHP }elseif ($padvance=="B") { ?>
                    <th align="center">Date</th>
                    <th align="center">DOKTER/SUPPLIER/CUSTOMER</th>
                    <th align="center">NO. SLIP</th>
                    <th align="center">PENGAJUAN</th>
                    <th align="center">DAERAH</th>
                    <th align="center">KETERANGAN</th>
                    <th align="center">REALISASI</th>
                    <?PHP if ($_GET['ket']=="excel") {
                        echo "<th align='center'></th>";
                    } ?>
                    <th align="center">Credit</th>
                    <th align="center">No.</th>
                <?PHP }else{ ?>
                    <th align="center">Date</th>
                    <th align="center">Bukti</th>
                    <th align="center">KODE</th>
                    <th align="center">PERKIRAAN</th>
                    <th align="center">DOKTER/SUPPLIER/CUSTOMER</th>
                    <th align="center">NO. SLIP</th>
                    <th align="center">PENGAJUAN</th>
                    <th align="center">DAERAH</th>
                    <th align="center">KETERANGAN</th>
                    <th align="center">REALISASI</th>
                    <?PHP if ($_GET['ket']=="excel") {
                        echo "<th align='center'></th>";
                    } ?>
                    <th align="center">Credit</th>
                    <th align="center">No.</th>
                <?PHP } ?>
            <?PHP } ?>
        </thead>
        <tbody>
            <?PHP
                $noklm=1;
                $pnoslip="";
                $gtotal=0;
                $gtotal1=0;
                $gsaldo=0;
                $no=1;
                $nmyno=1;
                
                $noklaim=1;
                        
                $query = "select distinct urutan from $tmp03 order by urutan";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pnourut_ = $row1['urutan'];
                    $njml=0;
                    $tot_perslip_c=0;
                    $tot_perslip_c1=0;

                    $nmyno=$no;
                            
                    $query = "select * from $tmp03 WHERE urutan='$pnourut_' order by urutan, noslip, realisasi1, nama_karyawan, brId";
                    $tampil=mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pnoslip = $row['noslip'];

                        $pbrid = $row['brId'];

                        $ptgltrans = "";
                        if (!empty($row['tgltrans']) AND $row['tgltrans']<> "0000-00-00")
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
                        $pjumlah1 = $row['jumlah1'];

                        $psld=$pjumlah-$pjumlah1;
                        $gsaldo=$gsaldo+$psld;

                        $pjmlreal = $row['realisasi2'];

                        
                        $tot_perslip_c=(double)$tot_perslip_c+(double)$row['jumlah'];
                        $tot_perslip_c1=(double)$tot_perslip_c1+(double)$row['jumlah1'];
                        
                        
                        $gtotal=$gtotal+$row['jumlah'];
                        $gtotal1=$gtotal1+$row['jumlah1'];

                        $pjumlah=number_format($row['jumlah'],0,",",",");
                        $pjumlah1=number_format($row['jumlah1'],0,",",",");
                        $psld=number_format($psld,0,",",",");
                        $psaldo=0;

                        if (!empty($row['realisasi2']))
                            $pjmlreal=number_format($row['realisasi2'],0,",",",");

                        //if (empty($pnmdokter)) $pnmdokter=$prealisasi1;

                        $pdaerah = $row['nama_cabang'];
                        if ($pdaerah=="ETH - HO") $pdaerah= "HO";

                        
                        $pcoa = $row['COA4'];
                        $pnmcoa = $row['NAMA4'];

                        echo "<tr>";
                        if ($padvance=="Ka") {
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td>$pnmdokter</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td>$pnamakaryawan</td>";
                            echo "<td>$paktivitas1</td>";
                            echo "<td>$prealisasi1</td>";
                            if ($_GET['ket']=="excel") echo "<td></td>";
                            echo "<td nowrap align='right'>$pjumlah1</td>";
                            echo "<td nowrap align='right'>$pjumlah</td>";
                            echo "<td nowrap align='right'>$psld</td>";
                            echo "<td nowrap align='center'>$nmyno</td>";
                        }elseif ($padvance=="B") {
                            echo "<td nowrap>$ptgltrans</td>";
                            echo "<td>$pnmdokter</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td>$pnamakaryawan</td>";
                            echo "<td nowrap>$pdaerah</td>";
                            echo "<td>$paktivitas1</td>";
                            echo "<td>$prealisasi1</td>";
                            if ($_GET['ket']=="excel") echo "<td></td>";
                            echo "<td nowrap align='right'>$pjumlah</td>";
                            echo "<td nowrap align='center'>$nmyno</td>";
                        }else{

                            echo "<td nowrap>$ptglkeluar</td>";//$ptgltrans
                            echo "<td nowrap>$pnobukti</td>";

                            echo "<td nowrap>$pcoa</td>";
                            echo "<td>$pnmcoa</td>";

                            echo "<td>$pnmdokter</td>";
                            echo "<td nowrap>$pnoslip</td>";
                            echo "<td>$pnamakaryawan</td>";

                            if ($mact=="viewbr") echo "<td nowrap>$pdaerah</td>";
                            echo "<td>$paktivitas1</td>";
                            if ($mact=="viewbr") {
                                echo "<td>$prealisasi1</td>";
                                if ($_GET['ket']=="excel") echo "<td></td>";
                            }

                            echo "<td nowrap align='right'>$pjumlah</td>";
                            
                            if ($padvance=="K") {
                                echo "<td nowrap align='center'>$noklaim</td>";
                            }else{
                                echo "<td nowrap align='center'>$nmyno</td>";
                            }
                        }
                        echo "</tr>";
                        
                        $nmyno="";
                        
                        
                        $noklm++;
                        $njml++;
                        
                        $noklaim++;
                    }
                    $no++;
                    
                    if ((double)$njml>1 AND $padvance!="K") {
                        $tot_perslip_s=(double)$tot_perslip_c-(double)$tot_perslip_c1;
                        
                        $tot_perslip_c=number_format($tot_perslip_c,0,",",",");
                        $tot_perslip_c1=number_format($tot_perslip_c1,0,",",",");
                        $tot_perslip_s=number_format($tot_perslip_s,0,",",",");
                        
                        echo "<tr>";
                        if ($padvance=="Ka") {
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            if ($_GET['ket']=="excel") echo "<td></td>";
                            echo "<td nowrap align='right'><b>$tot_perslip_c1</b></td>";
                            echo "<td nowrap align='right'><b>$tot_perslip_c</b></td>";
                            echo "<td nowrap align='right'><b>$tot_perslip_s</b></td>";
                            echo "<td nowrap align='center'></td>";
                        }elseif ($padvance=="B") {
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            if ($_GET['ket']=="excel") echo "<td></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='center'></td>";
                        }else{

                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";

                            echo "<td nowrap></td>";
                            echo "<td></td>";

                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";

                            if ($mact=="viewbr") echo "<td nowrap></td>";
                            echo "<td></td>";
                            if ($mact=="viewbr") {
                                echo "<td></td>";
                                if ($_GET['ket']=="excel") echo "<td></td>";
                            }

                            echo "<td nowrap align='right'><b>$tot_perslip_c</b></td>";
                            echo "<td nowrap align='center'></td>";
                        }
                        echo "</tr>";
                        
                        //
                        echo "<tr>";
                        if ($padvance=="Ka") {
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            if ($_GET['ket']=="excel") echo "<td></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='center'></td>";
                        }elseif ($padvance=="B") {
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            if ($_GET['ket']=="excel") echo "<td></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='center'></td>";
                        }else{

                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";

                            echo "<td nowrap></td>";
                            echo "<td></td>";

                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";

                            if ($mact=="viewbr") echo "<td nowrap></td>";
                            echo "<td></td>";
                            if ($mact=="viewbr") {
                                echo "<td></td>";
                                if ($_GET['ket']=="excel") echo "<td></td>";
                            }

                            echo "<td nowrap align='right'><b></b></td>";
                            echo "<td nowrap align='center'></td>";
                        }
                        echo "</tr>";
                    }
                        
                }
                
                echo "<tr>";
                if ($mact=="viewbr"){
                    if ($padvance=="Ka"){
                        if ($_GET['ket']=="excel") 
                            echo "<td colspan='11'></td>";
                        else
                            echo "<td colspan='10'></td>";
                    }elseif ($padvance=="B"){
                        if ($_GET['ket']=="excel") 
                            echo "<td colspan='10'></td>";
                        else
                            echo "<td colspan='9'></td>";
                    }else{
                        if ($_GET['ket']=="excel") 
                            echo "<td colspan='13'></td>";
                        else
                            echo "<td colspan='12'></td>";
                    }
                }else
                    echo "<td colspan='10'></td>";
                
                echo "</tr>";
                
                //$gsaldo=$gtotal-$gtotal1;
                
                $gtotal=number_format($gtotal,0,",",",");
                $gtotal1=number_format($gtotal1,0,",",",");
                $gsaldo=number_format($gsaldo,0,",",",");
                
                echo "<tr>";
                if ($padvance=="Ka"){
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td></td>";
                    echo "<td><b>TOTAL</b></td>";
                    echo "<td></td>";
                    if ($_GET['ket']=="excel") echo "<td></td>";
                    echo "<td nowrap align='right'><b>$gtotal1</b></td>";
                    echo "<td nowrap align='right'><b>$gtotal</b></td>";
                    echo "<td nowrap align='right'><b>$gsaldo</b></td>";
                    echo "<td></td>";
                    echo "</tr>";
                }elseif ($padvance=="B"){
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    if ($_GET['ket']=="excel") echo "<td></td>";
                    echo "<td><b>TOTAL</b></td>";
                    echo "<td></td>";
                    echo "<td nowrap align='right'><b>$gtotal</b></td>";
                    echo "<td></td>";
                    echo "</tr>";
                }else{
                    
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    if ($mact=="viewbr"){
                        echo "<td></td>";
                        echo "<td></td>";
                        if ($_GET['ket']=="excel") echo "<td></td>";
                    }
                    echo "<td><b>TOTAL</b></td>";
                    echo "<td></td>";
                    echo "<td nowrap align='right'><b>$gtotal</b></td>";
                    echo "<td></td>";
                    echo "</tr>";
                    
                }
            ?>
        </tbody>
    </table>
    
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
                    echo "<b>PRITA M SINA</b></td>";

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
        echo "<td>YANG MEMBUAT,</td> <td></td> <td>CHECKER,</td> <td></td> <td>MENYETUJUI,</td>";
        echo "</tr>";

        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td> <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td></tr>";

        echo "<tr align='center'>";
        //echo "<td>Ernilya</td> <td></td> <td>(Marianne Prasanti)</td> <td></td> <td>(dr. Farida Soewanto)</td>";
        echo "<td>PRITA M SINA</td> <td></td> <td>MARIANNE PRASANTI</td> <td></td> <td>IRA BUDI SUSETYO</td>";
        echo "</tr>";
        
        echo "</table>";
         * 
         */
        ?>
        <br/>&nbsp;<br/>&nbsp;
</body>
</html>
