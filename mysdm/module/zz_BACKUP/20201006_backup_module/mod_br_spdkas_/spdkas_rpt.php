<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP BUDGET REQUEST KAS KECIL.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>

<html>
<head>
    <title>Rekap Budget Request Kas Kecil</title>
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
        $pstsspd="2";
        $pnodiv=$_GET['ispd'];
        
        $ptglpd=date("d F Y");
        
        
        
        $p_rp_pettycash_ho="30000000";
        $p_rp_pettycash_cor="5000000";
        
        $p_rp_pettycash=$p_rp_pettycash_ho;
        
        $now=date("mdYhis");
        
        $tmp01 =" dbtemp.RPTREKOTCF01_".$_SESSION['USERID']."_$now ";
        $tmp02 =" dbtemp.RPTREKOTCF02_".$_SESSION['USERID']."_$now ";
        $tmp03 =" dbtemp.RPTREKOTCF03_".$_SESSION['USERID']."_$now ";
        $tmp04 =" dbtemp.RPTREKOTCF04_".$_SESSION['USERID']."_$now ";
    

        
        
        $gmrheight = "100px";
        $ngbr_idinput="";
        $gbrttd_fin1="";
        $gbrttd_fin2="";
        $gbrttd_dir1="";
        $gbrttd_dir2="";
    
        $ntgl_apv1="";
        $ntgl_apv2="";
        $ntgl_apv_dir1="";
        $ntgl_apv_dir2="";

        $namapengaju_ttd_fin1="";
        $namapengaju_ttd_fin2="";

        $namapengaju_ttd1="";
        $namapengaju_ttd2="";
        
        
        $query = "select * from dbmaster.t_suratdana_br WHERE idinput='$pnodiv'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            
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
        
        
        
        $query = "select a.*, b.tglf, b.tglt, b.divisi divisipd, b.nodivisi, b.nomor, b.tgl as tglpd, b.coa4 coa, c.NAMA4 coa_nama,
            b.jumlah jumlahpd, b.kodeid, d.nama kodenama, b.subkode, d.subnama, b.jenis_rpt, b.jumlah2 jml_kasbon  
            from dbmaster.t_suratdana_br1 a JOIN  dbmaster.t_suratdana_br b 
            ON a.idinput=b.idinput LEFT JOIN dbmaster.coa_level4 c on b.coa4=c.COA4 
            LEFT JOIN dbmaster.t_kode_spd d on b.kodeid=d.kodeid and b.subkode=d.subkode 
            WHERE a.idinput = '$pnodiv'";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnit, $query);
        
        $query = "select a.kasId, a.periode1, a.kode, b.COA4, c.NAMA4, a.periode2, a.karyawanid, a.nama, d.nama nama_karyawan,
            a.aktivitas1, a.aktivitas2, a.jumlah 
            from hrd.kas a LEFT JOIN dbmaster.posting_coa_kas b on a.kode=b.kodeid 
            LEFT JOIN dbmaster.coa_level4 c on b.COA4=c.COA4
            LEFT JOIN hrd.karyawan d on a.karyawanid=d.karyawanId 
            WHERE 1=1 ";
            $query .= " AND a.kasId IN (select IFNULL(bridinput,'') from $tmp01)";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnit, $query);
        
        
            $query = "SELECT a.*, b.tglf, b.tglt, b.divisipd, b.kodenama, b.tglpd, b.nomor, b.nodivisi, 
                b.nobbm, b.nobbk, b.urutan, b.amount, b.coa, b.coa_nama, b.jumlahpd,
                b.jenis_rpt, b.kodeid, b.subkode, b.jml_kasbon   
                FROM $tmp02 a JOIN $tmp01 b on a.kasId=b.bridinput";
            
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnit, $query);
        //echo "$query";exit;
        
        $ftgl=$_GET['bln'];
        
        $datetrm = str_replace('/', '-', $ftgl);
        $ptgl_bln= date("Y-m-d", strtotime($datetrm));
            
        $mbln= date("F Y", strtotime($ptgl_bln));
        $mthan= date("Y", strtotime($ptgl_bln));
        
        $query = "select * from dbmaster.t_kasbon where IFNULL(stsnonaktif,'')<>'Y' AND YEAR(tgl)='$mthan'";
        $query = "create TEMPORARY table $tmp04 ($query)"; 
        mysqli_query($cnit, $query);
        
        $pjmlkasbon=0;
        /*
        $query = "select sum(jumlah) as jmlkasbon from $tmp04";
        $tampil=mysqli_query($cnit, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ks= mysqli_fetch_array($tampil);
            $pjmlkasbon=$ks['jmlkasbon'];
            if (empty($pjmlkasbon)) $pjmlkasbon=0;
        }
        */
        
        $query = "select distinct kodeid, subkode, tglf, tglt, tglpd, divisipd divisi, kodenama, nomor, nodivisi, coa, coa_nama, jumlahpd, jenis_rpt, jml_kasbon FROM $tmp03 order by tglpd, divisipd, nodivisi";
      
        $tampil=mysqli_query($cnit, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($r= mysqli_fetch_array($tampil)) {
                $pkode_id=$r['kodeid'];
                $psubkode_id=$r['subkode'];
                
                $pjmlkasbon=$r['jml_kasbon'];
                
                $p_rp_pettycash=$p_rp_pettycash_ho;
                if ($pkode_id=="2" AND $psubkode_id=="23") {
                    $p_rp_pettycash=$p_rp_pettycash_cor;
                    $pjmlkasbon=0;
                }
                
                $pkodenm=$r['kodenama'];
                $pnospd=$r['nomor'];
                $pnodivisi=$r['nodivisi'];
                $pcoapd=$r['coa'];
                $pnmcoapd=$r['coa_nama'];
                $pjumlahpd=$r['jumlahpd'];
                
                $pdivisipd=$r['divisi'];

                $ppengajuanpd=$pdivisipd;
                $ppengajuanpd2="BR $pdivisipd";
                
                $pjenisrpt=$r["jenis_rpt"];
                $nket="Laporan Kas Kecil periode $mbln";
                
                
                if (!empty($r['tglpd']) AND $r['tglpd']<>"0000-00-00")
                    $ptglpd =date("d F Y", strtotime($r['tglpd']));
                
                $ptglpd_f = "";
                if (!empty($r['tglf']) AND $r['tglf']<>"0000-00-00")
                    $ptglpd_f =date("d M Y", strtotime($r['tglf']));
                
                $ptglpd_t = "";
                if (!empty($r['tglt']) AND $r['tglt']<>"0000-00-00")
                    $ptglpd_t =date("d M Y", strtotime($r['tglt']));
                    
                echo "<table class='tjudul' width='100%'>";
                echo "<tr> <td width='300px' colspan='3'>Kepada : </td></tr>";
                echo "<tr> <td width='300px' colspan='3'>Yth. Ibu Natalia S. / Ibu Vanda</td></tr>";
                echo "<tr> <td width='300px' colspan='3'>PT. SDM- Surabaya</td></tr>";
                
                if ($ppilihrpt=="excel") {
                    echo "<tr> <td>No. </td> <td width='300px' colspan='2'>$pnodivisi</td> </tr>";
                    echo "<tr> <td>Hal. </td> <td width='300px' colspan='2'>Laporan Kas Kecil periode $ptglpd_f-$ptglpd_t</td> </tr>";
                }else{
                    echo "<tr> <td width='300px' colspan='3'>No. $pnodivisi</td></tr>";
                    echo "<tr> <td width='300px' colspan='3'>Hal. Laporan Kas Kecil periode $ptglpd_f-$ptglpd_t</td></tr>";
                }
                
                echo "</table>";
                echo "<br/>&nbsp;";
                
                
                //sementara
                if ($pnodivisi=="027/KEU/HO/VIII/19" OR $pnodivisi=="") {
                    //$pjmlkasbon=24330082;
                }
                //end sementara
                                
                ?>
                <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
                    <thead>
                        <tr style='background-color:#cccccc; font-size: 13px;'>
                        <th align="center">No.</th>
                        <th align="center">DATE</th>
                        <th align="center">DESCRIPTION</th>
                        <th align="center">AMOUNT ( Rp. )</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?PHP
                        $no=1;
                        $pjmldebit=0;
                        $pjmlkredit=0;
                        $pjmlsaldo=0;
                        $query = "select * FROM $tmp03 WHERE nodivisi='$pnodivisi' order by periode2, kasId, nodivisi, COA4";
                        $tampil2=mysqli_query($cnit, $query);
                        while ($row= mysqli_fetch_array($tampil2)) {
                            $ptgltrans = "";
                            if (!empty($row['periode1']) AND $row['periode1']<>"0000-00-00")
                                $ptgltrans =date("d-M-Y", strtotime($row['periode1']));
                            
                            $ptgltrc = "";
                            if (!empty($row['periode2']) AND $row['periode2']<>"0000-00-00")
                                $ptgltrc =date("d-M-Y", strtotime($row['periode2']));

                            $pbbk = $row['nobbk'];
                            $pcoa = $row['COA4'];
                            $pnmcoa = $row['NAMA4'];

                            $pnama = $row['nama'];
                            $ppengajuan = $row['nama'];
                            
                            $paktivitas1 = $row['aktivitas1'];
                            $paktivitas2 = $row['aktivitas2'];
                            
                            $pkredit=$row['jumlah'];
                            
                            $pjmlkredit=$pjmlkredit+$pkredit;
                            $pkredit=number_format($pkredit,0,",",",");
                            


                            echo "<tr>";
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$ptgltrc</td>";
                            echo "<td nowrap>$ppengajuan - $paktivitas1 $paktivitas2</td>";
                            echo "<td nowrap align='right'>$pkredit</td>";
                            echo "</tr>";


                            $no++;
                        }
                        $pjmldebit="";
                        if ($pstsspd=="2") {
                            $pjmldebit=$pjumlahpd;
                            $pjumlahpd=number_format($pjumlahpd,0,",",",");
                                           
                            
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td nowrap align='right'></td>";
                            echo "<td nowrap align='right'><b> </b></td>";
                            echo "</tr>";
                            
                            $pjmldebit=number_format($pjmldebit,0,",",",");
                        }
                        
                        $psldakhir=(double)$p_rp_pettycash-(double)$pjmlkredit-(double)$pjmlkasbon;
                        $pjmlkredit=number_format($pjmlkredit,0,",",",");
                        $p_rp_pettycash=number_format($p_rp_pettycash,0,",",",");
                        $pjmlkasbon=number_format($pjmlkasbon,0,",",",");
                        $psldakhir=number_format($psldakhir,0,",",",");
                        
                        
                            
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td nowrap align='right'></td>";
                        echo "<td nowrap align='right'><b> </b></td>";
                        echo "</tr>";
                            
                        
                        for ($x = 1; $x <= 6; $x++) {
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<td></td>";
                            
                            if ($x==1) {
                                echo "<td nowrap><b>TOTAL</b></td>";
                                echo "<td nowrap align='right'><b>$pjmlkredit</b></td>";
                            }elseif ($x==2) {
                                echo "<td nowrap><b>Petty Cash</b></td>";
                                echo "<td nowrap align='right'><b>$p_rp_pettycash</b></td>";
                            }elseif ($x==3) {
                                echo "<td nowrap><b>Kas Bon terlampir</b></td>";
                                echo "<td nowrap align='right'><b>$pjmlkasbon</b></td>";
                            }elseif ($x==4) {
                                echo "<td nowrap><b>Petty Cash- belum tarik dari Bank Niaga No.</b></td>";
                                echo "<td nowrap align='right'><b></b></td>";
                            }elseif ($x==5) {
                                echo "<td nowrap><b>Petty Cash- belum tarik dari Bank Niaga No.</b></td>";
                                echo "<td nowrap align='right'><b></b></td>";
                            }elseif ($x==6) {
                                echo "<td nowrap><b>Saldo Akhir</b></td>";
                                echo "<td nowrap align='right'><b>$psldakhir</b></td>";
                            }else{
                                echo "<td nowrap><b></b></td>";
                                echo "<td nowrap align='right'><b></b></td>";
                            }
                            
                            echo "</tr>";
                            $pjmlkredit="";
                            $pjmldebit="";
                        }

                        ?>
                    </tbody>
                </table>
                <?PHP

                echo "<br/>&nbsp;";
                
                echo "<table width='100%' border='0px' style='border : 0px solid #fff; font-size: 11px;'>";
                echo "<tr>";
                    echo "<td colspan='3'>Jakarta, $ptglpd</td>";
                echo "</tr>";
                echo "</table>";
                
                echo "<br/>&nbsp;";
                
                
        
        if ($_GET['ket']=="excel") {
    
                echo "<table class='tjudul' width='100%'>";
                    echo "<tr>";

                        echo "<td align='center'>";
                        echo "Yang Membuat,";
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>MARSISTO SUSIWATI</b></td>";


                        echo "<td align='center'>";
                        echo "Checker,";
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv2<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>MARIANNE PRASANTI</b></td>";


                        echo "<td align='center'>";
                        echo "Mengetahui,";
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv_dir1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>FARIDA SOEWANTO</b></td>";
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
                        echo "<b>MARSISTO SUSIWATI</b></td>";


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
                if ($ppilihrpt=="excel") {
                    echo "<table>";

                    echo "<tr>";
                        echo "<td colspan='4'>Jakarta, $ptglpd</td>";
                    echo "</tr>";

                    echo "<tr>";
                        echo "<td align='left' colspan='2'>Dilaporkan oleh,</td>";
                        echo "<td align='center'>Mengetahui,</td>";
                        echo "<td align='center'>Menyetujui,</td>";
                    echo "</tr>";

                    echo "<tr><td colspan='4'>&nbsp;</td></tr>";
                    echo "<tr><td colspan='4'>&nbsp;</td></tr>";
                    echo "<tr><td colspan='4'>&nbsp;</td></tr>";
                    echo "<tr><td colspan='4'>&nbsp;</td></tr>";


                    echo "<tr>";
                        echo "<td align='left' colspan='2'>Marsis</td>";
                        echo "<td align='center'>Marianne</td>";
                        echo "<td align='center'>dr. Farida Soewanto</td>";
                    echo "</tr>";

                    echo "</table>";
                    
                }else{
                        echo "<table width='100%' border='0px' style='border : 0px solid #fff; font-size: 11px;'>";
                        echo "<tr>";
                            echo "<td colspan='3'>Jakarta, $ptglpd</td>";
                        echo "</tr>";
                        
                        echo "<tr align='center'>";
                        echo "<td>Dilaporkan oleh,</td><td>Mengetahui,</td><td>Menyetujui,</td>";
                        echo "</tr>";

                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

                        echo "<tr align='center'>";
                        echo "<td>Marsis</td><td>Marianne</td><td>dr. Farida Soewanto</td>";
                        echo "</tr>";
                        echo "</table>";
                }
                
                */
                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                
                
            }
        }
    ?>
    
    
    <?PHP
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp02");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp03");
        mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp04");
        
        mysqli_close($cnit);
    ?>
</body>
</html>