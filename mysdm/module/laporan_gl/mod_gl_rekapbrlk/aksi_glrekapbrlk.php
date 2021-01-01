<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BIAYA LUAR KOTA.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    include("config/fungsi_combo.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>Rekap Biaya Luar Kota</title>
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
        
        $pstsspd=$_POST['e_stsspd'];
        
        $date1=$_POST['bulan1'];
        $tgl1= date("Y-m-01", strtotime($date1));
        $bulan= date("Ym", strtotime($date1));

        $_SESSION['CLSLKCA']=date("F Y", strtotime($date1));

        $tglnow = date("d/m/Y");
        $tgl01 = $_POST['bulan1'];
        $periode1 = date("Y-m", strtotime($tgl01));
        $per1 = date("F Y", strtotime($tgl01));

        


        $tglini = date("d F Y");
        $pbulan = date("F", strtotime($tgl01));
        $periodeygdipilih = date("Y-m-01", strtotime($tgl01));
        $bulanberikutnya = date('Y-m-d', strtotime("+1 months", strtotime($periodeygdipilih)));
        $pbulanberikutnya = date("F", strtotime($bulanberikutnya));
    
        $stsreport="";
        //$caridatadarigl="('S', 'C')";
        $caridatadarigl="";
        $caridatadivisigl="";
        
        $filternobr="";
        if ($pstsspd=="2") {
            $filternobr=('');
            if (!empty($_POST['chkbox_nodiv'])){
                $filternobr=$_POST['chkbox_nodiv'];
                $filternobr=PilCekBox($filternobr);
            }
        }else{
            $stsreport = $_POST['sts_rpt'];
            $caridatadivisigl=$_POST['divprodid'];
            
        }
        
        include ("module/mod_br_closing_lkca/seleksi_data_lk_ca.php");
        
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp04");
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp05");
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp06");
        
        $query ="select a.*, b.nama nama_des, c.divisi, d.COA4 coa4, e.NAMA4 nama4 from $tmp02 a JOIN dbmaster.t_brid b on a.nobrid=b.nobrid JOIN "
                . "(select distinct IFNULL(idrutin,'') idrutin, divisi from $tmp01 WHERE IFNULL(idrutin,'') <> '') c on a.idrutin=c.idrutin "
                . "LEFT JOIN dbmaster.posting_coa_rutin d on a.nobrid=d.nobrid AND c.divisi=d.divisi "
                . "LEFT JOIN dbmaster.coa_level4 e on d.COA4=e.COA4";
        $query = "create Temporary table $tmp04 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }


        $query ="select a.sts, a.idca1 idca, a.idrutin, a.keterangan, a.tgltrans, 
            b.coa4, b.nama4 nama_coa4, '' as icabangid, '' as nama_cabang, 
            '' as areaid, '' as nama_area, a.karyawanid, a.nama_karyawan, a.divisi,
            b.nobrid, b.nama_des, b.deskripsi, b.tgl1, b.tgl2, b.notes, 
            a.ca1, IFNULL(a.saldo,0)-IFNULL(a.ca1,0) as selisih, a.ca2, a.saldo jmltrans, '' tgltransreal, a.nobukti, a.idinput, 
            a.saldo jumlah, b.qty, b.rp, b.rptotal
            from $tmp01 a LEFT JOIN $tmp04 b on a.idrutin=b.idrutin";
        $query = "create Temporary table $tmp05 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "select idrutin, icabangid, areaid from dbmaster.t_brrutin0 WHERE idrutin IN "
                . "(select distinct IFNULL(idrutin,'') from $tmp05)";
        $query = "create TEMPORARY table $tmp06 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp01");
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp02");
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp03");
        
        
        
        if ($pstsspd=="2") {
            
            $query = "select a.*, b.divisi divisipd, b.nodivisi, b.nomor, b.tgl as tglpd, b.coa4 coa, c.NAMA4 coa_nama,
                b.jumlah jumlahpd, b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.jenis_rpt  
                from dbmaster.t_suratdana_br1 a JOIN  dbmaster.t_suratdana_br b 
                ON a.idinput=b.idinput LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
                LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid and b.subkode=d.subkode 
                WHERE a.idinput IN $filternobr";
            $query = "create TEMPORARY table $tmp01 ($query)"; 
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            $query = "SELECT a.*, b.divisipd, b.kodenama, b.tglpd, b.nomor, b.nodivisi, 
                b.nobbm, b.nobbk, b.urutan, b.amount, b.coa, b.coa_nama, b.jumlahpd,
                b.jenis_rpt 
                FROM $tmp05 a JOIN $tmp01 b on a.idrutin=b.bridinput";
        }else{

        }
        
            $query = "select *, CAST('' as CHAR(200)) as kodenama, CAST('' as CHAR(5)) as divisipd, CAST(NULL as DATE) as tglpd, CAST('' as CHAR(50)) as nomor, CAST('' as CHAR(50)) as nodivisi, 
                    CAST(NULL as CHAR(50)) as nobbm, CAST(NULL as CHAR(50)) as nobbk, CAST(NULL as DECIMAL(20,2)) as urutan, 
                    CAST(NULL as DECIMAL(20,2)) as amount, CAST('' as CHAR(50)) as coa, CAST('' as CHAR(200)) as coa_nama,
                    CAST(NULL as DECIMAL(20,2)) as jumlahpd, CAST('' as CHAR(1)) as jenis_rpt from $tmp05";
            
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        //echo "$query";exit;
        
        if ($pstsspd=="2") {
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.kodenama = (select distinct kodenama from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.divisipd = (select distinct divisipd from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.tglpd = (select distinct tglpd from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.nomor = (select distinct nomor from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.nodivisi = (select distinct nodivisi from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.nobbm = (select distinct nobbm from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.nobbk = (select distinct nobbk from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.urutan = (select distinct urutan from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.amount = (select distinct amount from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.coa = (select distinct coa from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.coa_nama = (select distinct coa_nama from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.jumlahpd = (select distinct jumlahpd from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
            mysqli_query($cnit, "UPDATE $tmp03 a SET a.jenis_rpt = (select distinct jenis_rpt from $tmp01 b WHERE a.idinput=b.idinput LIMIT 1)");
        }
        
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp01");
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp02");
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp04");
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp05");
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp06");
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp07");
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp08");
        

    
        
        if ($pstsspd=="2") {
            $query = "select distinct '' as sts, tglpd, divisipd divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt FROM $tmp03 order by tglpd, divisipd, nodivisi";
        }else{
            $query = "select distinct sts, tglpd, divisipd divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt FROM $tmp03 order by sts, nodivisi";
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
                $pstatuslk=$r['sts'];
                
                $pdivisipd=$r['divisi'];

                $ppengajuanpd=$pdivisipd;
                $ppengajuanpd2="LK $pdivisipd";
    
                $nket="";
                $nket="**Klaim";
                
                $ptglpd = "";
                if (!empty($r['tglpd']) AND $r['tglpd']<>"0000-00-00")
                    $ptglpd =date("d-M-Y", strtotime($r['tglpd']));
                    
                echo "<table class='tjudul' width='100%'>";
                echo "<tr> <td width='300px'>Biaya Luar Kota </td> <td> : </td> <td>$pnodivisi</td> </tr>";
                echo "<tr> <td width='200px'>$nket </td> <td> : </td> <td>$ptglpd</td> </tr>";
                echo "</table>";
                echo "<br/>&nbsp;";
                ?>
                <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
                    <thead>
                        <tr style='background-color:#cccccc; font-size: 13px;'>
                        <th align="center" nowrap>Date Trsfr</th>
                        <th align="center" nowrap>Bukti</th>
                        <th align="center" colspan="2" nowrap>COA</th>
                        <!--<th align="center" nowrap>DAERAH</th>-->
                        <th align="center" nowrap>ID CA</th>
                        <th align="center" nowrap>No LK</th>
                        <th align="center" nowrap>NAMA</th>
                        <th align="center" nowrap>DIVISI</th>
                        <th align="center" colspan="2" nowrap>Description</th>
                        <th align='center' nowrap>Jenis</th>
                        <th align='center' nowrap>Debit</th>
                        <th align="center" nowrap>Credit</th>
                        <th align="center" nowrap>Saldo REAL</th>
                        <th align="center" nowrap>CA <?PHP echo $per1; ?></th>
                        <th align="center" nowrap>Selisih</th>
                        <th align="center" >SPV/DM/SM/GSM</th>
                        <th align="center" >CA  <?PHP echo $per2; ?></th>
                        <th align="center" >JUML TRSF</th>
                        <th align="center" >SALDO</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    <?PHP
                        $no=1;
                        $totalrp=0;
                        $totalrpbln_next=0;
                        $totalrpbln_prv=0;
                        $pselisih=0;
                        $totselisih=0;

                        $totjumlah=0;
                        $totjumlahtrsf=0;

                        $sudahlewat=false;
                        $filtersts=" AND sts='$pstatuslk' ";
                        if ($pstsspd=="2") {
                            $filtersts=" AND nodivisi='$pnodivisi' ";
                        }
                        $query = "select * from $tmp03 where 1=1 $filtersts order by divisi, nama_karyawan, karyawanid, idrutin";
                        $result = mysqli_query($cnit, $query);
                        $records = mysqli_num_rows($result);
                        $row = mysqli_fetch_array($result);

                        if ($records) {
                            $reco = 1;
                            while ($reco <= $records) {
                                $pkaryawanid=$row['karyawanid'];
                                $pnmkaryawan=$row['nama_karyawan'];
                                while (($reco <= $records) AND $row['karyawanid']==$pkaryawanid) {

                                    $ptgltrasn="";
                                    if (!empty($row['tgltrans']) AND $row['tgltrans'] != "0000-00-00")
                                        $ptgltrasn = date('d/m/Y', strtotime($row['tgltrans']));

                                    $prealtgltrasn="";
                                    if (!empty($row['tgltransreal']) AND $row['tgltransreal'] != "0000-00-00")
                                        $prealtgltrasn = date('d/m/Y', strtotime($row['tgltransreal']));

                                    $pnolk=$row['idrutin'];
                                    $pidca=$row['idca'];
                                    $pketerangan=$row['keterangan'];

                                    $pbukti=$row['nobukti'];
                                    $pcoa4=$row['coa4'];
                                    $pnmcoa4=$row['nama_coa4'];
                                    $pkdcabang=$row['icabangid'];
                                    $pnmcabang=$row['nama_cabang'];
                                    $pkdarea=$row['areaid'];
                                    $pnmarea=$row['nama_area'];
                                    $pdivisi=$row['divisi'];
                                    $pnobrid=$row['nobrid'];
                                    $pnmdes=$row['nama_des'];
                                    $pdeskripsi=$row['deskripsi'];
                                    $pnotes=$row['notes'];
                                    if (empty($pdeskripsi)) $pdeskripsi=$pnotes;
                                    elseif (!empty($pdeskripsi)) $pdeskripsi=$pdeskripsi.", ".$pnotes;


                                    $pqty=number_format($row['qty'],0,",",",");
                                    $prp=number_format($row['rp'],0,",",",");
                                    $prptotal=number_format($row['rptotal'],0,",",",");


                                    if ($pnobrid=="04" or $pnobrid=="25") {

                                        $myquery = "select rp from $tmp03 where idrutin='$pnolk' AND nobrid='$pnobrid'";
                                        $myresult = mysqli_query($cnit, $myquery);
                                        $nr = mysqli_fetch_array($myresult);
                                        $prp=number_format($nr['rp'],0,",",",");

                                         $pnmdes=$pnmdes." (".$pqty."x".$prp.")";
                                    }

                                    if ($pnobrid=="21") {
                                        $ptgl1="";
                                        $ptgl2="";
                                        if ($row['tgl1']!="0000-00-00" AND !empty($row['tgl1']))
                                            $ptgl1 = date('d/m/Y', strtotime($row['tgl1']));
                                        if ($row['tgl2']!="0000-00-00" AND !empty($row['tgl2']))
                                            $ptgl2 = date('d/m/Y', strtotime($row['tgl2']));

                                        $pnmdes=$pnmdes." (".$ptgl1." s/d. ".$ptgl2.")";
                                    }

                                    $totalrp =$totalrp+$row['rptotal'];

                                    $pjenis = "UC";
                                    $papv="";

                                    echo "<tr>";
                                    echo "<td nowrap>$ptgltrasn</td>";
                                    echo "<td nowrap>$pbukti</td>";
                                    echo "<td nowrap>$pcoa4</td>";
                                    echo "<td nowrap>$pnmcoa4</td>";
                                    //echo "<td nowrap>$pnmarea</td>";
                                    echo "<td nowrap>$pidca</td>";//IDCA
                                    echo "<td nowrap>$pnolk</td>";//LK
                                    echo "<td nowrap>$pnmkaryawan</td>";
                                    echo "<td>$pdivisi</td>";
                                    echo "<td nowrap>$pnmdes</td>";
                                    echo "<td >$pdeskripsi</td>";
                                    echo "<td>$pjenis</td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap align='right'>$prptotal</td>";


                                    if ($sudahlewat==false) {
                                        $pjumlah=number_format($row['jumlah'],0,",",",");
                                        $totjumlah=$totjumlah+$row['jumlah'];
                                        $pca1=number_format($row['ca1'],0,",",",");
                                        $pca2=number_format($row['ca2'],0,",",",");
                                        $totalrpbln_next =$totalrpbln_next+$row['ca1'];
                                        $pselisih=$row['ca1']-$row['jumlah'];
                                        $totselisih =$totselisih+$pselisih;

                                        $pjumlahtrans=$row['ca2']-$pselisih;

                                        if ($pselisih>0 AND $row['ca2']==0) $pjumlahtrans=0;
                                        elseif ($pselisih>0 AND $row['ca2']>0) $pjumlahtrans=$row['ca2'];
                                        elseif ($pselisih==0 AND $row['ca2']>0) $pjumlahtrans=$row['ca2'];

                                        if (empty($pjumlahtrans)) $pjumlahtrans=0;
                                        $totjumlahtrsf=$totjumlahtrsf+$pjumlahtrans;
                                        $pjumlahtrans=number_format($pjumlahtrans,0,",",",");

                                        $pselisih=number_format($pselisih,0,",",",");
                                        $totalrpbln_prv =$totalrpbln_prv+$row['ca2'];

                                        echo "<td nowrap align='right'>$pjumlah</td>";
                                        echo "<td nowrap align='right'>$pca1</td>";
                                        echo "<td nowrap align='right'>$pselisih</td>";

                                        echo "<td nowrap>$papv</td>";
                                        echo "<td nowrap align='right'>$pca2</td>";
                                        echo "<td nowrap align='right'>$pjumlahtrans</td>";
                                        echo "<td nowrap align='right'></td>";

                                    }else{
                                        echo "<td nowrap align='right'></td>";
                                        echo "<td nowrap align='right'></td>";
                                        echo "<td nowrap align='right'></td>";

                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";
                                        echo "<td></td>";

                                    }


                                    echo "</tr>";

                                    $no++;
                                    $row = mysqli_fetch_array($result);
                                    $reco++;
                                    $sudahlewat=true;
                                }
                                $sudahlewat=false;

                                echo "<tr>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                                //echo "<td nowrap></td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td nowrap></td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td></td>";

                                echo "</tr>";

                            }
                            
                            $totjmlpd="";
                            $totjmlpdsaldo="";
                            if ($pstsspd=="2") {
                                $totjmlpd=$pjumlahpd;
                                $pjumlahpd=number_format($pjumlahpd,0,",",",");
                                
                                echo "<tr>";
                                echo "<td>$ptglpd</td>";
                                echo "<td>$pbukti</td>";
                                echo "<td nowrap>$pcoapd</td>";
                                echo "<td nowrap>$pnmcoapd</td>";
                                echo "<td>Klaim</td>";
                                echo "<td></td>";
                                echo "<td nowrap>LK</td>";
                                echo "<td nowrap>$pnospd</td>";
                                echo "<td>Cash advance luar kota</td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo "<td nowrap align='right'>$pjumlahpd</td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "<td nowrap align='right'></td>";

                                echo "</tr>";
                                $totjmlpdsaldo=$totjmlpd-$totjumlahtrsf;
                                $totjmlpdsaldo=number_format($totjmlpdsaldo,0,",",",");
                                
                                $totjmlpd=number_format($totjmlpd,0,",",",");
                            }
                            
                            
                            $totalrp=number_format($totalrp,0,",",",");
                            $totjumlah=number_format($totjumlah,0,",",",");
                            $totalrpbln_next=number_format($totalrpbln_next,0,",",",");
                            $totalrpbln_prv=number_format($totalrpbln_prv,0,",",",");
                            $totselisih=number_format($totselisih,0,",",",");
                            $totjumlahtrsf=number_format($totjumlahtrsf,0,",",",");
                            echo "<tr>";
                            //echo "<td colspan='11' align='right'> TOTAL : &nbsp;</td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            //echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td nowrap></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td nowrap align='right'><b>$totjmlpd</b></td>";
                            echo "<td nowrap align='right'><b>$totalrp</b></td>";
                            echo "<td nowrap align='right'><b>$totjumlah</b></td>";
                            echo "<td nowrap align='right'><b>$totalrpbln_next</b></td>";
                            echo "<td nowrap align='right'><b>$totselisih</b></td>";
                            echo "<td></td>";
                            echo "<td nowrap align='right'><b>$totalrpbln_prv</b></td>";
                            echo "<td nowrap align='right'><b>$totjumlahtrsf</b></td>";
                            echo "<td nowrap align='right'><b>$totjmlpdsaldo</b></td>";

                            echo "</tr>";
                        }
                        
                    ?>
                        
                    </tbody>
                </table>
                <?PHP

                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
            }
        }
    ?>
    
    
    <?PHP
        mysqli_query($cnit, "drop temporary table IF EXISTS $tmp03");
        
        mysqli_close($cnit);
    ?>
</body>
</html>