<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI BIAYA LUAR KOTA VS CASH ADVANCE.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>REALISASI BIAYA LUAR KOTA VS CASH ADVANCE</title>
<?PHP if ($_GET['ket']!="excel") { ?>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
    <link href="css/laporanbaru.css" rel="stylesheet">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
<?PHP } ?>
    
<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>

</head>

<body>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?php


    $cnit=$cnmy;
    $date1=$_POST['bulan1'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $bulan= date("Ym", strtotime($date1));
    
    $_SESSION['CLSLKCA']=date("F Y", strtotime($date1));
    
    
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    $per1 = date("F Y", strtotime($tgl01));
    $tgl_utang_pi_= date('M Y', strtotime('-1 month', strtotime($tgl01)));
    
    $stsreport = $_POST['sts_rpt'];
    
    
    $tglini = date("d F Y");
    $pbulan = date("F", strtotime($tgl01));
    $periodeygdipilih = date("Y-m-01", strtotime($tgl01));
    $bulanberikutnya = date('Y-m-d', strtotime("+1 months", strtotime($periodeygdipilih)));
    $pbulanberikutnya = date("F", strtotime($bulanberikutnya));
    
    
    include ("module/mod_br_closing_lkca/seleksi_data_lk_ca.php");
    
    
    $query ="select * from $tmp01";
    
    $query ="select a.*, b.nama nama_des, c.divisi, d.COA4 coa4, e.NAMA4 nama4 from $tmp02 a JOIN dbmaster.t_brid b on a.nobrid=b.nobrid JOIN "
            . "(select distinct IFNULL(idrutin,'') idrutin, divisi from $tmp01 WHERE IFNULL(idrutin,'') <> '') c on a.idrutin=c.idrutin "
            . "LEFT JOIN dbmaster.posting_coa_rutin d on a.nobrid=d.nobrid AND c.divisi=d.divisi "
            . "LEFT JOIN dbmaster.coa_level4 e on d.COA4=e.COA4";
    $query = "create Temporary table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    $query ="select a.jml_adj, a.idca1 idca, a.idrutin, a.keterangan, a.tgltrans, 
        b.coa4, b.nama4 nama_coa4, '' as icabangid, '' as nama_cabang, 
        '' as areaid, '' as nama_area, a.karyawanid, a.nama_karyawan, a.divisi,
        b.nobrid, b.nama_des, b.deskripsi, b.tgl1, b.tgl2, b.notes, 
        a.ca1, IFNULL(a.saldo,0)-IFNULL(a.ca1,0) as selisih, a.ca2, a.saldo jmltrans, '' tgltransreal, a.nobukti, 
        a.saldo jumlah, b.qty, b.rp, b.rptotal
        from $tmp01 a LEFT JOIN $tmp03 b on a.idrutin=b.idrutin";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }

    
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp08");
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="210px"><b>Realisasi Biaya Luar Kota Per </b></td><td><?PHP echo "$per1 "; ?></td></tr>
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
                <th align="center" nowrap>Date Trsfr</th>
                <th align="center" nowrap>Bukti</th>
                <th align="center" colspan="2" nowrap>COA</th>
                <!--<th align="center" nowrap>DAERAH</th>-->
                <th align="center" nowrap>ID CA</th>
                <th align="center" nowrap>No LK</th>
                <th align="center" nowrap>NAMA</th>
                <th align="center" nowrap>DIVISI</th>
                <th align="center" colspan="2" nowrap>Description</th>
                <?PHP
                if ($_GET['ket']=="excel") {
                    echo "<th align='center' nowrap>Jenis</th>";
                    echo "<th align='center' nowrap>Debit</th>";
                }else{
                    echo "<th align='center' nowrap>Keterangan</th>";
                    echo "<th align='center' nowrap>Jenis</th>";
                }
                ?>
                
                <th align="center" nowrap>Credit</th>
                <th align="center" nowrap>Saldo REAL</th>
                <th align="center" nowrap>CA <?PHP echo $per1; ?></th>
                <th align="center" nowrap>Selisih</th>
                <th align="center" >SPV/DM/SM/GSM</th>
                <th align="center" >CA  <?PHP echo $per2; ?></th>
                <th align="center" >AR / AP<br/>  <?PHP echo $tgl_utang_pi_; ?></th>
                <th align="center" >JUML TRSF</th>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $totalrp=0;
                    $totalrpbln_next=0;
                    $totalrpbln_prv=0;
                    
                    $totalrpadj=0;
                    
                    $pselisih=0;
                    $totselisih=0;
                    
                    $totjumlah=0;
                    $totjumlahtrsf=0;
                    
                    $sudahlewat=false;
                    
                    $query = "select * from $tmp04 order by divisi, nama_karyawan, karyawanid, idrutin";
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
                                    
                                    $myquery = "select rp from $tmp02 where idrutin='$pnolk' AND nobrid='$pnobrid'";
                                    $myresult = mysqli_query($cnit, $myquery);
                                    $nr = mysqli_fetch_array($myresult);
                                    $prp=number_format($nr['rp'],0,",",",");
                                    
                                    if ($_GET['ket']=="excel")
                                         $pnmdes=$pnmdes." (".$pqty."x".$prp.")";
                                    else
                                        $pnmdes=$pnmdes."<br/>(".$pqty."x".$prp.")";
                                }
                                
                                if ($pnobrid=="21") {
                                    $ptgl1="";
                                    $ptgl2="";
                                    if ($row['tgl1']!="0000-00-00" AND !empty($row['tgl1']))
                                        $ptgl1 = date('d/m/Y', strtotime($row['tgl1']));
                                    if ($row['tgl2']!="0000-00-00" AND !empty($row['tgl2']))
                                        $ptgl2 = date('d/m/Y', strtotime($row['tgl2']));
                                    
                                    if ($_GET['ket']=="excel")
                                         $pnmdes=$pnmdes." (".$ptgl1." s/d. ".$ptgl2.")";
                                    else
                                        $pnmdes=$pnmdes."<br/>(".$ptgl1." s/d. ".$ptgl2.")";
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
                                echo "<td>$pdeskripsi</td>";
                                if ($_GET['ket']=="excel") {
                                    echo "<td>$pjenis</td>";
                                    echo "<td nowrap></td>";
                                }else{
                                    echo "<td>$pketerangan</td>";
                                    echo "<td nowrap>$pjenis</td>";
                                }
                                echo "<td nowrap align='right'>$prptotal</td>";
                                
                                
                                if ($sudahlewat==false) {
                                    $pjumlah=number_format($row['jumlah'],0,",",",");
                                    $totjumlah=$totjumlah+$row['jumlah'];
                                    $pca1=number_format($row['ca1'],0,",",",");
                                    $pca2=number_format($row['ca2'],0,",",",");
                                    
                                    $pjmladj=number_format($row['jml_adj'],0,",",",");
                                    
                                    $totalrpbln_next =$totalrpbln_next+$row['ca1'];
                                    $pselisih=$row['ca1']-$row['jumlah'];
                                    $totselisih =$totselisih+$pselisih;
                                    
                                    //$pjumlahtrans=$row['ca2']-$pselisih;
                                    //if ($pselisih>0 AND $row['ca2']==0) $pjumlahtrans=0;
                                    //elseif ($pselisih>0 AND $row['ca2']>0) $pjumlahtrans=$row['ca2'];
                                    //elseif ($pselisih==0 AND $row['ca2']>0) $pjumlahtrans=$row['ca2'];
                                    
                                    $pjumlahtrans= ( (double)$row['ca2']-(double)$pselisih ) + (double)$row['jml_adj'];
                                    //if ((double)$pjumlahtrans<0) $pjumlahtrans=0;
                                    if ($pselisih>0 AND $row['ca2']==0) $pjumlahtrans=0;
                                    elseif ($pselisih>0 AND $row['ca2']>0) $pjumlahtrans=$row['ca2'] + (double)$row['jml_adj'];
                                    elseif ($pselisih==0 AND $row['ca2']>0) $pjumlahtrans=$row['ca2'] + (double)$row['jml_adj'];
                                    
                                    if (empty($pjumlahtrans)) $pjumlahtrans=0;
                                    $totjumlahtrsf=$totjumlahtrsf+$pjumlahtrans;
                                    
                                    if ((double)$pjumlahtrans < 0) $pjumlahtrans=0;
                                    $pjumlahtrans=number_format($pjumlahtrans,0,",",",");
                                    
                                    $pselisih=number_format($pselisih,0,",",",");
                                    $totalrpbln_prv =$totalrpbln_prv+$row['ca2'];
                                    
                                    $totalrpadj =$totalrpadj+$row['jml_adj'];
                                    
                                    echo "<td nowrap align='right'>$pjumlah</td>";
                                    echo "<td nowrap align='right'>$pca1</td>";
                                    echo "<td nowrap align='right'>$pselisih</td>";
                                    
                                    echo "<td nowrap>$papv</td>";
                                    echo "<td nowrap align='right'>$pca2</td>";
                                    echo "<td nowrap align='right'>$pjmladj</td>";//utang piutang
                                    echo "<td nowrap align='right'>$pjumlahtrans</td>";
                                    
                                }else{
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    
                                    echo "<td nowrap align='right'></td>";//utang piutang
                                    
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
                            echo "<td></td>";//utang piutang
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            
                            echo "</tr>";
                                
                        }
                        $totalrp=number_format($totalrp,0,",",",");
                        $totjumlah=number_format($totjumlah,0,",",",");
                        $totalrpbln_next=number_format($totalrpbln_next,0,",",",");
                        $totalrpbln_prv=number_format($totalrpbln_prv,0,",",",");
                        
                        $totalrpadj=number_format($totalrpadj,0,",",",");
                        
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
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>$totalrp</b></td>";
                        echo "<td nowrap align='right'><b>$totjumlah</b></td>";
                        echo "<td nowrap align='right'><b>$totalrpbln_next</b></td>";
                        echo "<td nowrap align='right'><b>$totselisih</b></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'><b>$totalrpbln_prv</b></td>";
                        echo "<td nowrap align='right'><b>$totalrpadj</b></td>";
                        echo "<td nowrap align='right'><b>$totjumlahtrsf</b></td>";
                        
                        echo "</tr>";
                    }
                    
                    
                    
                    mysqli_query($cnit, "drop temporary table $tmp01");
                    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
                    mysqli_query($cnit, "drop temporary table $tmp03");
                    mysqli_query($cnit, "drop temporary table $tmp04");
                    mysqli_query($cnit, "drop temporary table $tmp05");
                    mysqli_close($cnit);
                ?>
            </tbody>
        </table>

        <br/>&nbsp;<br/>&nbsp;
        <br/>&nbsp;<br/>&nbsp;
        
    <script>
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};
        function scrollFunction() {
          if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("myBtn").style.display = "block";
          } else {
            document.getElementById("myBtn").style.display = "none";
          }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
          document.body.scrollTop = 0;
          document.documentElement.scrollTop = 0;
        }
    </script>
</body>
</html>