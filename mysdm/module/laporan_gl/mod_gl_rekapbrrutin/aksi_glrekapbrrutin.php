<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BIAYA RUTIN.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>Rekap Biaya Rutin</title>
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
        $pstsspd=$_POST['e_stsspd'];
        
        $now=date("mdYhis");
        $tmp01 =" dbtemp.RPTREKOTCF01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTREKOTCF02_".$_SESSION['USERID']."_$now ";
        $tmp03 =" dbtemp.RPTREKOTCF03_".$_SESSION['USERID']."_$now ";
    
        $filternobr="";
        if ($pstsspd=="2") {
            $filternobr=('');
            if (!empty($_POST['chkbox_nodiv'])){
                $filternobr=$_POST['chkbox_nodiv'];
                $filternobr=PilCekBox($filternobr);
            }
            
            $query = "select a.*, b.divisi divisipd, b.nodivisi, b.nomor, b.tgl as tglpd, b.coa4 coa, c.NAMA4 coa_nama,
                b.jumlah jumlahpd, b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.jenis_rpt  
                from dbmaster.t_suratdana_br1 a JOIN  dbmaster.t_suratdana_br b 
                ON a.idinput=b.idinput LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
                LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid and b.subkode=d.subkode 
                WHERE a.idinput IN $filternobr";
            $query = "create TEMPORARY table $tmp01 ($query)"; 
            mysqli_query($cnit, $query);
        }
        
        $query = "SELECT
            b1.nourut,
            br.idrutin,
            br.kode,
            br.karyawanid,
            br.bulan,
            br.kodeperiode,
            br.periode1,
            br.periode2,
            br.jumlah,
            br.keterangan,
            br.divisi,
            br.tgltrans,
            br.jmltrans,
            k.nama,
            a.nama nama_area_o,
            aa.Nama nama_area,
            c.nama nama_cabang,
            co.nama nama_cabang_o,
            b1.nobrid,
            i.nama nama_brid,
            b1.qty,
            b1.rp,
            b1.rptotal,
            b1.notes,
            b1.alasanedit_fin,
            b1.coa COA4,
            c1.NAMA4,
            br.nama_karyawan
            FROM
            dbmaster.t_brrutin1 AS b1
            LEFT JOIN dbmaster.t_brrutin0 AS br ON b1.idrutin = br.idrutin
            LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
            LEFT JOIN MKT.icabang AS c ON c.iCabangId=br.icabangid
            LEFT JOIN MKT.icabang_o AS co ON co.icabangid_o=br.icabangid_o
            LEFT JOIN MKT.iarea_o AS a ON br.areaid_o = a.areaid_o and br.icabangid_o=a.icabangid_o
            LEFT JOIN MKT.iarea AS aa ON br.areaid = aa.areaId and br.icabangid=aa.iCabangId
            LEFT JOIN dbmaster.t_brid AS i ON b1.nobrid = i.nobrid
            LEFT JOIN dbmaster.coa_level4 c1 ON c1.COA4 = b1.coa WHERE 1=1 ";
        
        if ($pstsspd=="2") {
            $query .= " AND br.idrutin IN (select IFNULL(bridinput,'') from $tmp01)";
        }else{
            
            $tgl01=$_POST['bulan1'];
            $periode1= date("Ym", strtotime($tgl01));
            
            $fperiode = " AND DATE_FORMAT(br.bulan, '%Y%m') = '$periode1' ";
            $fstsapv = " AND ifnull(br.tgl_fin,'') <> '' AND ifnull(br.tgl_fin,'0000-00-00') <> '0000-00-00' ";
            
            $edivisi = $_POST['divprodid'];
            $fdivisi = " AND br.divisi NOT IN ('OTC') ";
            if (!empty($edivisi) AND ($edivisi <> "*")) $fdivisi = " AND br.divisi='$edivisi' ";
            
            $query .=" AND br.kode=1 AND br.stsnonaktif <> 'Y' "
                    . " $fperiode $fstsapv $fdivisi";
            
        }
        
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        
        
        $query = "UPDATE $tmp02 a set a.COA4=IFNULL((select b.COA4 from dbmaster.posting_coa_rutin b WHERE 
            a.divisi=b.divisi AND a.nobrid=b.nobrid LIMIT 1),a.COA4)";
        mysqli_query($cnit, $query);

        $query = "UPDATE $tmp02 a set a.NAMA4=IFNULL((select b.NAMA4 from dbmaster.coa_level4 b WHERE 
            a.coa=b.COA4 LIMIT 1),a.NAMA4)";
        mysqli_query($cnit, $query);

        $query = "UPDATE $tmp02 a set a.nama=a.nama_karyawan WHERE karyawanid='$_SESSION[KRYNONE]'";
        mysqli_query($cnit, $query);

        $query = "UPDATE $tmp02 a set a.notes=a.alasanedit_fin WHERE IFNULL(alasanedit_fin,'')<>''";
        mysqli_query($cnit, $query);
    
        
        if ($pstsspd=="2") {
            $query = "SELECT a.*, b.divisipd, b.kodenama, b.tglpd, b.nomor, b.nodivisi, 
                b.nobbm, b.nobbk, b.urutan, b.amount, b.coa, b.coa_nama, b.jumlahpd,
                b.jenis_rpt 
                FROM $tmp02 a JOIN $tmp01 b on a.idrutin=b.bridinput";
        }else{
            $query = "select *, CAST('' as CHAR(1)) as kodenama, CAST('' as CHAR(1)) as divisipd, CAST(NULL as DATE) as tglpd, CAST('' as CHAR(1)) as nomor, CAST('' as CHAR(1)) as nodivisi, 
                    CAST(NULL as CHAR(1)) as nobbm, CAST(NULL as CHAR(1)) as nobbk, CAST(NULL as DECIMAL(20,2)) as urutan, 
                    CAST(NULL as DECIMAL(20,2)) as amount, CAST('' as CHAR(1)) as coa, CAST('' as CHAR(1)) as coa_nama,
                    CAST(NULL as DECIMAL(20,2)) as jumlahpd, CAST('' as CHAR(1)) as jenis_rpt from $tmp02";
        }
        $query = "create  table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        //echo "$query";exit;
        
        $ngtotald=0;
        $ngtotalk=0;
        $ngtotals=0;
        if ($pstsspd=="2") {
            $query = "select distinct kodeperiode, tglpd, divisipd divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt FROM $tmp03 order by tglpd, divisipd, nodivisi";
        }else{
            $query = "select distinct kodeperiode, tglpd, divisipd divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt FROM $tmp03 order by kodeperiode, nodivisi";
        }
        $tampil=mysqli_query($cnit, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($r= mysqli_fetch_array($tampil)) {
                $pkodenm=$r['kodenama'];
                $pnospd=$r['nomor'];
                $pnodivisi=$r['nodivisi'];
                $pcoapd=$r['coa'];
                $pnmcoapd=$r['coa_nama'];
                $pjumlahpd=$r['jumlahpd'];
                $pkdperiode=$r['kodeperiode'];
                
                $pdivisipd=$r['divisi'];

                $ppengajuanpd=$pdivisipd;
                $ppengajuanpd2="RUTIN $pdivisipd";
                
                $tgl01=$_POST['bulan1'];
                $perpilihper = date("15 F y", strtotime($tgl01));
                if ((INT)$pkdperiode==2) $perpilihper = date("t F y", strtotime($tgl01));
    
                $nket="";
                $nket="**Klaim";
                
                $ptglpd = "";
                if (!empty($r['tglpd']) AND $r['tglpd']<>"0000-00-00")
                    $ptglpd =date("d-M-Y", strtotime($r['tglpd']));
                    
                echo "<table class='tjudul' width='100%'>";
                echo "<tr> <td width='300px'>Biaya Rutin Per </td> <td> : </td> <td>$perpilihper</td> </tr>";
                echo "<tr> <td width='200px'>$nket </td> <td> : </td> <td>$ptglpd</td> </tr>";
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
                        <th align='center'>Lain2</th>
                        <th align='center'>&nbsp;</th>
                        <th align="center">Debit</th>
                        <th align="center">Credit</th>
                        <th align="center">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?PHP
                        $gtotalsaldo=0;
                        $totalcredit=0;
                        $fambil=" AND nodivisi='$pnodivisi' ";
                        if ($pstsspd=="1") $fambil=" AND kodeperiode='$pkdperiode' ";
                        
                        $query = "select distinct karyawanid, nama, idrutin, jumlah from $tmp03 WHERE 1=1 $fambil order by nama, idrutin";
                        $result0 = mysqli_query($cnit, $query);
                        while ($row0 = mysqli_fetch_array($result0)) {
                            $pkaryawanid=$row0['karyawanid'];
                            $pidrutin=$row0['idrutin'];
                            $psaldo=$row0['jumlah'];
                            $gtotalsaldo=$gtotalsaldo+$psaldo;


                            $query = "select * from $tmp03 WHERE 1=1 $fambil AND karyawanid='$pkaryawanid' AND idrutin='$pidrutin' order by kodeperiode, nama, idrutin, coa";
                            $result = mysqli_query($cnit, $query);
                            $records = mysqli_num_rows($result);
                            while ($row = mysqli_fetch_array($result)) {
                                $pdate="";
                                $pnobukti=$row['nobbk'];
                                $pcoa=$row['COA4'];
                                $pnmcoa=$row['NAMA4'];
                                $pcabang=$row['nama_cabang'];


                                $pnama=$row['nama'];
                                $pjenis=$row['nama_brid'];
                                $pdesc=$row['notes'];
                                $plain="";

                                $pdebit="";
                                $pcredit=$row['rptotal'];
                                $totalcredit=$totalcredit+$pcredit;

                                if ($_GET['ket']=="excel" AND $_SESSION['IDCARD']=="0000000143")
                                    $pcredit=number_format($pcredit,0,".",".");
                                else
                                    $pcredit=number_format($pcredit,0,",",",");

                                echo "<tr>";
                                echo "<td nowrap>$pdate</td>";
                                echo "<td nowrap>$pnobukti</td>";
                                echo "<td nowrap>$pcoa</td>";
                                echo "<td nowrap>$pnmcoa</td>";
                                echo "<td nowrap>$pcabang</td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap>$pnama</td>";
                                echo "<td nowrap>$pjenis</td>";
                                echo "<td >$pdesc</td>";
                                echo "<td nowrap>$plain</td>";
                                echo "<td nowrap>&nbsp;</td>";
                                echo "<td nowrap>$pdebit</td>";
                                echo "<td nowrap align='right'>$pcredit</td>";
                                echo "<td nowrap></td>";
                                echo "</tr>";
                            }
                            if ($ppilihrpt=="excel" AND $_SESSION['IDCARD']=="0000000143")
                                $psaldo=number_format($psaldo,0,".",".");
                            else
                                $psaldo=number_format($psaldo,0,",",",");
                            echo "<tr>";
                            if ($ppilihrpt=="excel") {
                                echo "<td></td><td></td><td></td><td></td><td></td><td></td>";
                                echo "<td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                            }else{
                                echo "<td colspan=13></td>";
                            }
                            echo "<td nowrap align='right'><b>$psaldo</b></td>";
                            echo "</tr>";
                        }
                        
                        $totaldebit="";
                        if ($pstsspd=="2") {
                            $totaldebit=$pjumlahpd;
                            if ($ppilihrpt=="excel" AND $_SESSION['IDCARD']=="0000000143") {
                                $pjumlahpd=number_format($pjumlahpd,0,".",".");
                            }else{
                                $pjumlahpd=number_format($pjumlahpd,0,",",",");
                            }
                            echo "<tr>";
                            echo "<td nowrap>$pdate</td>";
                            echo "<td nowrap>$pnobukti</td>";
                            echo "<td nowrap>$pcoapd</td>";
                            echo "<td nowrap>$pnmcoapd</td>";
                            echo "<td nowrap>$pkodenm</td>";
                            echo "<td nowrap>$pnospd</td>";
                            echo "<td nowrap>Rutin</td>";
                            echo "<td nowrap>$pnodivisi</td>";
                            echo "<td >Biaya Rutin</td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap align='right'>$pjumlahpd</td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap></td>";
                            echo "</tr>";
                            
                            $ngtotald=$ngtotald+$totaldebit;
                            
                            $gtotalsaldo=$totaldebit-$totalcredit;
                            if ($ppilihrpt=="excel" AND $_SESSION['IDCARD']=="0000000143") {
                                $totaldebit=number_format($totaldebit,0,".",".");
                            }else{
                                $totaldebit=number_format($totaldebit,0,",",",");
                            }
                        }
                        
                        
                        
                        $ngtotalk=$ngtotalk+$totalcredit;
                        
                        if ($ppilihrpt=="excel" AND $_SESSION['IDCARD']=="0000000143") {
                            $gtotalsaldo=number_format($gtotalsaldo,0,".",".");
                            $totalcredit=number_format($totalcredit,0,".",".");
                        }else{
                            $gtotalsaldo=number_format($gtotalsaldo,0,",",",");
                            $totalcredit=number_format($totalcredit,0,",",",");
                        }
                        echo "<tr>";
                        if ($ppilihrpt=="excel") {
                            echo "<td></td><td></td><td></td><td></td><td></td><td></td>";
                                echo "<td></td><td></td><td></td><td></td><td></td><td></td>";
                        }else{
                            echo "<td colspan=11 align='center'>TOTAL</td> <td><b>$totaldebit</b></td>";
                        }
                        echo "<td nowrap align='right'><b>$totalcredit</b></td>";
                        echo "<td nowrap align='right'><b>$gtotalsaldo</b></td>";
                        echo "</tr>";
                        
                        
                    ?>
                    </tbody>
                </table>
                <?PHP

                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
            }
        }
        
        if ($pstsspd=="2") {

            $ngtotals=$ngtotald-$ngtotalk;
            $ngtotald=number_format($ngtotald,0,",",",");
            $ngtotalk=number_format($ngtotalk,0,",",",");
            $ngtotals=number_format($ngtotals,0,",",",");
            
            
            echo "<table id='datatable2' class='table table-striped table-bordered example_2' border='1px solid black'>";
            echo "<tr>";
            echo "<td nowrap colspan=3><b>Grand Total</b></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td><b>Debit</b></td><td><b>Kredit</b></td><td><b>Saldo</b></td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td align='right'><b>$ngtotald</b></td> <td align='right'><b>$ngtotalk</b></td> <td align='right'><b>$ngtotals</b></td>";
            echo "</tr>";

            echo "</table>";
            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
        }
    ?>
    
    
    <?PHP
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        
        mysqli_close($cnit);
    ?>
</body>
</html>