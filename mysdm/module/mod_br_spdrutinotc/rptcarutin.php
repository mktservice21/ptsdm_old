<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP CA SERVICE KENDARAAN.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>REKAP CA SERVICE KENDARAAN</title>
<?PHP if ($_GET['ket']!="excel") { ?>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2050 1:00:00 GMT">
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
    
    $namapengaju_ttd_fin1="";
    $namapengaju_ttd_fin2="";
    
    $namapengaju_ttd1="";
    $namapengaju_ttd2="";
    $per1="";
    $ntgl_apv1="";
    $ntgl_apv2="";
    $ntgl_apv_dir1="";
    $ntgl_apv_dir2="";
    
    $pnomorbr="";
    if (isset($_GET['ispd'])) {
        $idinputspd=$_GET['ispd'];
        $_POST['bulan1']="2000-01-00";
        $_POST['sts_rpt']="";
        
        $query = "select * from dbmaster.t_suratdana_br WHERE idinput='$idinputspd'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            
            $per1= $ra['tgl'];
            $per1 = date("d F Y", strtotime($per1));
                
            if (!empty($ra['tglf']))
                $_POST['bulan1']=$ra['tglf'];
            
            $_POST['sts_rpt']=$ra['sts'];
            
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
    
    
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    
    $fperiode = " AND ( (DATE_FORMAT(br.periode1, '%Y-%m') = '$periode1') OR (DATE_FORMAT(br.periode2, '%Y-%m') = '$periode1') ) ";
    
    $pbulan = date("F", strtotime($tgl01));
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DBRROTCPBLL01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DBRROTCPBLL02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DBRROTCPBLL03_".$_SESSION['IDCARD']."_$now ";
    
    
    $query = "select * from dbmaster.t_suratdana_br1 WHERE idinput='$idinputspd'";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select idservice, tglservice, divisi, karyawanid, icabangid, areaid, icabangid_o, areaid_o, nopol, 
        km, jumlah, keterangan from dbmaster.t_service_kendaraan WHERE stsnonaktif<>'Y' and divisi='OTC' and 
        idservice IN (select distinct IFNULL(bridinput,'') from $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, a.jumlah as AMOUNT, b.nama nama_karyawan, c.nama nama_cabang, CAST('' as CHAR(10)) as bridinput from $tmp02 a JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang_o c on a.icabangid_o=c.icabangid_o";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
        
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="200px"><b>CA Service Kendaraan OTC Per </b></td><td align="left"><?PHP echo "$per1 "; ?></td></tr>
                <tr><td width="150px"><b>No.BR </b></td><td><?PHP echo "$pnomorbr"; ?></td></tr>
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
                <th align="center">Nama</th>
                <th align="center">Daerah</th>
                <th align="center">Tanggal</th>
                <th align="center">Jumlah Rp.</th>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $total=0;
                    $totalpot=0;
                    $totalpen=0;
                    $totalbay=0;
                    $query = "select * from $tmp03 order by nama_karyawan, karyawanid";
                    $result = mysqli_query($cnit, $query);
                    $records = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result);
                    
                    if ($records) {
                        $reco = 1;
                        while ($reco <= $records) {
                            $noid=$row['karyawanid'];
                            $nama=$row['nama_karyawan'];
                            $area=$row['nama_cabang'];
                            $ptglinput =date("d-M-Y", strtotime($row['tglservice']));
                            $jumlah=number_format($row['AMOUNT'],0,",",",");
                            
                            $total = $total + $row['AMOUNT'];
                            
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td>$nama</td>";
                            echo "<td>$area</td>";
                            echo "<td>$ptglinput</td>";
                            echo "<td align='right'>$jumlah</td>";
                            echo "</tr>";
                            
                            $no++;
                            $row = mysqli_fetch_array($result);
                            $reco++;  
                            
                        }
                        $total=number_format($total,0,",",",");
                        //TOTAL
                        echo "<tr>";
                        echo "<td colspan=4 align='right'><b>Total : </b></td>";
                        echo "<td align='right'><b>$total</b></td>";
                        echo "</tr>";
                    }
                    
                    
                ?>
            </tbody>
        </table>
        <br/>&nbsp;<br/>&nbsp;
        <?PHP
        
        
        if ($_GET['ket']=="excel") {
    
                    echo "<table class='tjudul' width='100%'>";
                        echo "<tr>";
                        
                            echo "<td align='center'>";
                            echo "Yang membuat,";
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>(Saiful Rahmat)</b></td>";
                             
							
                            echo "<td align='center'>";
                            echo "Checker,";
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv2<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>(Marianne Prasanti)</b></td>";
                            
                            /*
                            echo "<td align='center'>";
                            echo "Menyetujui,";
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv_dir1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>(dr. Farida Soewanto)</b></td>";
                            */
                        echo "</tr>";
                        
                    echo "</table>";
            
        }else{
                    echo "<table class='tjudul' width='100%'>";
                        echo "<tr>";
                        
                            echo "<td align='center'>";
                            echo "Yang membuat,";
                            if (!empty($namapengaju_ttd_fin1))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>(Saiful Rahmat)</b></td>";
                             
							
                            echo "<td align='center'>";
                            echo "Checker,";
                            if (!empty($namapengaju_ttd_fin2))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>(Marianne Prasanti)</b></td>";
                            
                            /*
                            echo "<td align='center'>";
                            echo "Menyetujui,";
                            if (!empty($namapengaju_ttd1))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>(dr. Farida Soewanto)</b></td>";
                            */
                        echo "</tr>";
                        
                    echo "</table>";
                    
        }
        
        /*
        echo "<table width='100%' border='0px' style='border : 0px solid #fff; font-size: 11px; font-weight: bold;'>";
        echo "<tr align='center'>";
        echo "<td>Yang membuat,</td><td colspan=2></td><td>Menyetujui,</td>";
        echo "</tr>";

        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

        echo "<tr align='center'>";
        echo "<td>(Saiful Rahmat)</td><td></td><td></td><td>(dr. Farida Soewanto)</td>";
        echo "</tr>";
        echo "</table>";
         * 
         */
        ?>
        <br/>&nbsp;<br/>&nbsp;
</body>
</html>

<?PHP
hapusdata:
    mysqli_query($cnit, "drop temporary table $tmp01");
    mysqli_query($cnit, "drop temporary table $tmp02");
    mysqli_query($cnit, "drop temporary table $tmp03");
    mysqli_close($cnit);
?>
