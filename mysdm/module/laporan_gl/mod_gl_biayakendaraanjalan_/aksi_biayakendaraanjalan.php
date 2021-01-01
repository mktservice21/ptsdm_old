<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=BIAYA KENDARAAN DAN PERJALANAN.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>Laporan Biaya Kendaraan & Perjalanan</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Apr 2019 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <?PHP } ?>
</head>

<body>
    <?PHP
        include "config/koneksimysqli.php";
        include "config/fungsi_combo.php";
        $tgl01=$_POST['bulan1'];
        $periode1= date("Ym", strtotime($tgl01));
        
        $now=date("mdYhis");
        $tmp01 =" dbtemp.RPTREKOTCF01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTREKOTCF02_".$_SESSION['USERID']."_$now ";
        $tmp03 =" dbtemp.RPTREKOTCF03_".$_SESSION['USERID']."_$now ";
        $tmp04 =" dbtemp.RPTREKOTCF04_".$_SESSION['USERID']."_$now ";
        
        $query = "select
            b.bulan, 
            b.karyawanid,
            b.icabangid,
            b.nopol,
            a.idrutin, a.nobrid, a.coa, 
            CAST('' as CHAR(100)) as nodivisi, 
            CAST('' as CHAR(100)) as nomor, 
            CAST('' as CHAR(100)) as nobbk, 
            CAST('' as CHAR(100)) as nobbm, 
            CAST(NULL as date) tgltrans, 
            CAST(NULL as decimal(20,2)) debit, 
            sum(a.rptotal) rptotal
             FROM dbmaster.t_brrutin1 a 
            JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin
            WHERE IFNULL(b.stsnonaktif,'')<>'Y' AND a.nobrid IN ('01', '02', '03', '08', '', '09', '41', '21', '22', '23', '24') AND 
            IFNULL(b.tgl_fin,'')<>'' AND 
            DATE_FORMAT(bulan,'%Y%m')='$periode1'";
        $query .=" GROUP BY 1,2,3,4,5,6";
        //echo "$query";exit;
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        mysqli_query($cnmy, "UPDATE $tmp01 a SET a.nopol=IFNULL((SELECT b.nopol FROM dbmaster.t_kendaraan_pemakai b WHERE "
                . " a.karyawanid=b.karyawanid AND IFNULL(b.stsnonaktif,'')<>'Y' AND "
                . " DATE_FORMAT(b.tglawal,'%Y%m')<=DATE_FORMAT(a.bulan,'%Y%m') "
                . " order by b.tglawal DESC LIMIT 1),'') WHERE IFNULL(nopol,'')=''");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        // delete yang no polisi nya kosong
        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE IFNULL(nopol,'')=''");
        // HAPUS SELAIN BENSIN PARKIT TOL SERVICE
        mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE nobrid NOT IN ('01', '02', '03', '08')");
        
        $query ="select a.*, b.nodivisi, b.nomor, b.tgl, b.tglspd FROM dbmaster.t_suratdana_br1 a "
                . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput "
                . " WHERE a.bridinput IN (select distinct idrutin from $tmp01)";
        $query = "create TEMPORARY table $tmp04 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp01 a JOIN $tmp04 b on a.idrutin=b.bridinput SET a.nodivisi=b.nodivisi, a.nomor=b.nomor,"
                . " a.nobbk=b.nobbk, a.nobbm=b.nobbm, a.tgltrans=b.tgl, a.debit=a.rptotal";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        $query = "select a.*, d.nama nama_karyawan, b.nama namaid, c.NAMA4 
               from $tmp01 a
               JOIN dbmaster.t_brid b on a.nobrid=b.nobrid
               LEFT JOIN dbmaster.coa_level4 c on a.coa=c.COA4
               JOIN hrd.karyawan d on a.karyawanid=d.karyawanId";
        $query = "create TEMPORARY table $tmp02 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "SELECT tgltrans, coa, NAMA4, nodivisi, nomor, nobbm, nobbk, karyawanid, nama_karyawan, namaid, "
                . " CAST('' as CHAR(10)) icabangid, CAST('' as CHAR(200)) nama_cabang, sum(rptotal) rptotal, sum(debit) debit "
                . " FROM $tmp02 GROUP BY 1,2,3,4,5,6,7,8,9,10";
        $query = "create TEMPORARY table $tmp03 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        //cari cabang takut ada yang double
        mysqli_query($cnmy, "UPDATE $tmp03 a SET a.icabangid=(SELECT b.icabangid FROM $tmp01 b WHERE a.karyawanid=b.karyawanid LIMIT 1)");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        mysqli_query($cnmy, "UPDATE $tmp03 a JOIN MKT.icabang b ON a.icabangid=b.iCabangId SET a.nama_cabang=b.nama");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        echo "<table class='tjudul' width='100%'>";
        echo "<tr> <td width='300px'><b>BIAYA KENDARAAN & PERJALANAN</b></td> <td> $tgl01 </td> <td>&nbsp;</td> </tr>";
        //echo "<tr> <td width='200px'>&nbsp; </td> <td> &nbsp; </td> <td>&nbsp;</td> </tr>";
        echo "</table>";
        echo "<br/>&nbsp;";
                
    ?>
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
            <th align="center">Date</th>
            <th align="center">Bukti</th>
            <th align="center">Kode</th>
            <th align="center">Perkiraan</th>
            <th align="center">Cabang</th>
            <th align="center">No</th>
            <th align="center">Nama</th>
            <th align="center">Jenis</th>
            <th align="center">Description</th>
            <th align="center">Lain2</th>
            <th align="center">Debit</th>
            <th align="center">Credit</th>
            <th align="center">Saldo</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $ptotdebit=0;
            $ptotcredit=0;
            $ptotsaldo=0;
            $no=1;
            $query = "select * FROM $tmp03 order by tgltrans, namaid, nama_cabang, nama_karyawan";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                
                $ptgltrans="";
                if (!empty($row['tgltrans']) AND $row['tgltrans']<>"0000-00-00")
                    $ptgltrans =date("d-M-Y", strtotime($row['tgltrans']));
                $nobukti="";
                $pnmkaryawan = $row['nama_karyawan'];
                $pnmcabang = $row['nama_cabang'];
                $pcoa = $row['coa'];
                $pnmcoa = $row['NAMA4'];
                $pnodivisi = $row['nodivisi'];
                $pjenis = $row['namaid'];
                $pdesc = "";
                $plain2 = "";
                
                $pdebit=$row['debit'];
                $ptotdebit=$ptotdebit+$pdebit;
                $pdebit="";
                
                $pcredit = $row['rptotal'];
                $ptotcredit=$ptotcredit+$pcredit;
                
                $psaldo="";
                //$psaldo=$pdebit-$pcredit;
                
                if ($_SESSION['IDCARD']=="0000000143") {
                    //$pdebit=number_format($pdebit,0,".",".");
                    $pcredit=number_format($pcredit,0,".",".");
                    //$psaldo=number_format($psaldo,0,".",".");
                }else{
                    //$pdebit=number_format($pdebit,0,",",",");
                    $pcredit=number_format($pcredit,0,",",",");
                    //$psaldo=number_format($psaldo,0,",",",");
                }
                
                
                echo "<tr>";
                echo "<td nowrap>$ptgltrans</td>";
                echo "<td nowrap>$nobukti</td>";
                echo "<td nowrap>$pcoa</td>";
                echo "<td nowrap>$pnmcoa</td>";
                echo "<td nowrap>$pnmcabang</td>";
                echo "<td nowrap>$pnodivisi</td>";
                echo "<td nowrap>$pnmkaryawan</td>";
                echo "<td nowrap>$pjenis</td>";
                echo "<td nowrap>$pdesc</td>";
                echo "<td nowrap>$plain2</td>";
                
                echo "<td nowrap align='right'>$pdebit</td>";
                echo "<td nowrap align='right'>$pcredit</td>";
                echo "<td nowrap align='right'>$psaldo</td>";
                echo "</tr>";


                $no++;
            }
            
            $ptotsaldo=(double)$ptotdebit-(double)$ptotcredit;
            if ((double)$ptotsaldo<0) $ptotsaldo=0;
            
            if ($_SESSION['IDCARD']=="0000000143") {
                $ptotdebit=number_format($ptotdebit,0,".",".");
                $ptotcredit=number_format($ptotcredit,0,".",".");
                $ptotsaldo=number_format($ptotsaldo,0,".",".");
            }else{
                $ptotdebit=number_format($ptotdebit,0,",",",");
                $ptotcredit=number_format($ptotcredit,0,",",",");
                $ptotsaldo=number_format($ptotsaldo,0,",",",");
            }
                
            echo "<tr>";
            echo "<td nowrap colspan=10 align='center'><b>TOTAL</b></td>";
            
            echo "<td nowrap align='right'><b>$ptotdebit</b></td>";
            echo "<td nowrap align='right'><b>$ptotcredit</b></td>";
            echo "<td nowrap align='right'><b>$ptotsaldo</b></td>";
            
            echo "</tr>";
         
        ?>
        </tbody>
    </table>
    
    <?PHP
    hapusdata:
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");
        
        mysqli_close($cnmy);
    ?>
</body>
</html>