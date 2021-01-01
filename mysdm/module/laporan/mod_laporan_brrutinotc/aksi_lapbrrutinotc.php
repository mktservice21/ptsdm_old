<?PHP
    session_start();
    $erptby = $_POST['cb_rptby'];
    $nmtipe="";
    if ($erptby=="C") $nmtipe="COA";
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN $nmtipe BIAYA RUTIN OTC.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>LAPORAN <?PHP echo $nmtipe; ?> BIAYA RUTIN OTC</title>
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
    $tgl01 = $_POST['bulan1'];
    $tgl02 = $_POST['bulan2'];
    $periode1 = date("Ym", strtotime($tgl01));
    $periode2 = date("Ym", strtotime($tgl02));
    
    $per1 = date("F Y", strtotime($tgl01));
    $per2 = date("F Y", strtotime($tgl02));
    
    $ekaryawan = $_POST['e_karyawan'];
    $ecabang = $_POST['icabangid_o'];
    
    $fkaryawan = "";
    $fcabang = "";
    
    $fperiode = " AND DATE_FORMAT(bulan, '%Y%m') between '$periode1' AND '$periode2' ";
    if (!empty($ekaryawan) AND ($ekaryawan <> "*")) $fkaryawan = " AND karyawanid='$ekaryawan' ";
    if (!empty($ecabang) AND ($ecabang <> "*")) $fcabang = " AND icabangid_o='$ecabang' ";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBRRTNLKOTC01_".$_SESSION['IDCARD']."_$now ";
    $tmp02 =" dbtemp.DTBRRTNLKOTC02_".$_SESSION['IDCARD']."_$now ";
    $tmp03 =" dbtemp.DTBRRTNLKOTC03_".$_SESSION['IDCARD']."_$now ";
    $tmp04 =" dbtemp.DTBRRTNLKOTC04_".$_SESSION['IDCARD']."_$now ";
    
    $query = "select idrutin, kode, tgl, bulan, kodeperiode, periode1, periode2,
        karyawanid, nama_karyawan, icabangid_o icabangid, areaid_o areaid, keterangan, jumlah 
        from dbmaster.t_brrutin0 WHERE IFNULL(stsnonaktif,'')<>'Y' AND divisi='OTC' 
        AND kode='1' $fperiode $fkaryawan $fcabang";
    $query = "create temporary table $tmp01 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.nama, c.nama nama_area from $tmp01 a LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.iarea_o AS c ON a.areaid = c.areaid_o AND a.icabangid=c.icabangid_o";
    $query = "create temporary table $tmp02 ($query)";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //0000002200, 0000002083, $_SESSION['KRYNONE']
    $query="UPDATE $tmp02 SET nama=nama_karyawan, karyawanid=idrutin WHERE karyawanid IN ('0000002200', '0000002083')";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    if ($erptby=="C") {
        $query = "select idrutin, nobrid, coa, deskripsi, notes, qty, rp, rptotal from dbmaster.t_brrutin1 WHERE "
                . " idrutin IN (select distinct IFNULL(idrutin,'') idrutin FROM $tmp01)";
        $query = "create temporary table $tmp03 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "select a.*, b.nama nama_brid, c.NAMA4 nama_coa from $tmp03 a LEFT JOIN dbmaster.t_brid AS b ON a.nobrid = b.nobrid
            LEFT JOIN dbmaster.coa_level4 c ON c.COA4 = a.coa";
        $query = "create temporary table $tmp04 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        mysqli_query($cnit, "drop temporary table $tmp01");
        
        $query = "SELECT b.kodeperiode, a.idrutin, b.karyawanid, b.nama, b.areaid, b.nama_area, b.bulan, 
                a.nobrid, a.nama_brid, a.coa, a.nama_coa, sum(b.jumlah) jumlah, sum(a.rptotal) rptotal
                 FROM $tmp04 a JOIN $tmp02 b on a.idrutin=b.idrutin 
                GROUP BY 1,2,3,4,5,6,7,8,9,10,11";
        
        $query = "create  table $tmp01 ($query)";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }
    
if ($erptby=="S") {
?>
    <center><h2><u>LAPORAN BIAYA RUTIN OTC</u></h2></center>
    
    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td><b>Periode </b></td><td>:</td><td><?PHP echo "$per1 s/d. $per2"; ?></td></tr>
                <tr><td><b>View Date </b></td><td>:</td><td><?PHP echo "$tglnow"; ?></td></tr>
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
            <th align="center">Yang Mengajukan</th>
            <th align="center">Daerah</th>
            <th align="center">ID</th>
            
            <th align="center">Keterangan</th>
            <th align="center">Jumlah (Rp.)</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $ptotjumlah=0;
            $pjmlbln=0;
            $no=1;
            $query = "select Distinct DATE_FORMAT(bulan,'%Y-%m-01') bulan from $tmp02 order by 1";
            $tampil=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $nbulan=$row['bulan'];
                $pbulan = strtoupper(date("F Y", strtotime($nbulan)));
                $fbulan = date("Ym", strtotime($nbulan));
                    
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>$pbulan</b></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";

                echo "<td nowrap></td>";
                echo "<td nowrap align='right'></td>";
                echo "</tr>";
            
                $pjmlbln=0;
                $no=1;
                
                $query2 ="select * from $tmp02 WHERE DATE_FORMAT(bulan,'%Y%m')='$fbulan' order by nama, karyawanid, nama_area, idrutin";
                $tampil2=mysqli_query($cnit, $query2);
                while ($row2= mysqli_fetch_array($tampil2)) {
                    $pkaryawanid=$row2['karyawanid'];
                    $pkaryawannm=$row2['nama'];
                    $pnmarea=$row2['nama_area'];
                    $pidruin=$row2['idrutin'];
                    $pket=$row2['keterangan'];
                    $pjml=$row2['jumlah'];
                    
                    $pjmlbln=(double)$pjmlbln+(double)$pjml;
                    $ptotjumlah=(double)$ptotjumlah+(double)$pjml;
                    
                    $pjml=number_format($pjml,0,",",",");
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pkaryawannm</td>";
                    echo "<td nowrap>$pnmarea</td>";
                    echo "<td nowrap>$pidruin</td>";
                    
                    echo "<td>$pket</td>";
                    echo "<td nowrap align='right'>$pjml</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                
                $pjmlbln=number_format($pjmlbln,0,",",",");

                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";

                echo "<td nowrap align='right'><b>TOTAL $pbulan</b></td>";
                echo "<td nowrap align='right'><b>$pjmlbln</b></td>";
                echo "</tr>";
                
            }
            
            $ptotjumlah=number_format($ptotjumlah,0,",",",");
            
            echo "<tr>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            
            echo "<td nowrap align='right'><b>GRAND TOTAL</b></td>";
            echo "<td nowrap align='right'><b>$ptotjumlah</b></td>";
            echo "</tr>";
            
            // SUMARRY ALL BULAN
            echo "<tr>";
            echo "<td colspan='6'></td>";
            echo "</tr>";
            
            echo "<tr>";
            echo "<td colspan='6'><b>SUMMARY : </b></td>";
            echo "</tr>";
            
            $no=1;
            $ptotjumlah=0;
            
            $query2 ="select karyawanid, nama, areaid, nama_area, sum(jumlah) jumlah "
                    . " from $tmp02 GROUP BY 1,2,3,4 ORDER BY nama, karyawanid, nama_area";
            $tampil2=mysqli_query($cnit, $query2);
            while ($row2= mysqli_fetch_array($tampil2)) {
                $pkaryawanid=$row2['karyawanid'];
                $pkaryawannm=$row2['nama'];
                $pnmarea=$row2['nama_area'];
                $pjml=$row2['jumlah'];

                $ptotjumlah=(double)$ptotjumlah+(double)$pjml;

                $pjml=number_format($pjml,0,",",",");


                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pkaryawannm</td>";
                echo "<td nowrap>$pnmarea</td>";
                echo "<td nowrap></td>";

                echo "<td></td>";
                echo "<td nowrap align='right'>$pjml</td>";
                echo "</tr>";

                $no++;
            }
            $ptotjumlah=number_format($ptotjumlah,0,",",",");
            
            echo "<tr>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            
            echo "<td nowrap align='right'><b>GRAND TOTAL</b></td>";
            echo "<td nowrap align='right'><b>$ptotjumlah</b></td>";
            echo "</tr>";
        ?>
        </tbody>
    </table>
    
<?PHP

}else{
        
?>
    <center><h2><u>LAPORAN DETAIL COA BIAYA RUTIN OTC</u></h2></center>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td><b>Periode </b></td><td>:</td><td><?PHP echo "$per1 s/d. $per2"; ?></td></tr>
                <tr><td><b>View Date </b></td><td>:</td><td><?PHP echo "$tglnow"; ?></td></tr>
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
            <th align="center">Yang Mengajukan</th>
            <th align="center">Daerah</th>
            <th align="center">ID</th>
            <th align="center">Akun</th>
            <th align="center">COA</th>
            <th align="center">Nama COA</th>
            <th align="center">Jumlah (Rp.)</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $ptotjumlah=0;
            $pjmlbln=0;
            $pjmlemp=0;
            $no=1;
            $query = "select Distinct DATE_FORMAT(bulan,'%Y-%m-01') bulan from $tmp01 order by 1";
            $tampil=mysqli_query($cnit, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $nbulan=$row['bulan'];
                $pbulan = strtoupper(date("F Y", strtotime($nbulan)));
                $fbulan = date("Ym", strtotime($nbulan));
                    
                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap><b>$pbulan</b></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'></td>";
                echo "</tr>";
            
                $no=1;
                
                $query1 = "select Distinct DATE_FORMAT(bulan,'%Y-%m-01') bulan, karyawanid, nama from $tmp01 WHERE DATE_FORMAT(bulan,'%Y%m')='$fbulan' order by 3,2,1";
                $tampil1=mysqli_query($cnit, $query1);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    
                    $pkaryawanid=$row1['karyawanid'];
                    $pkaryawannm=$row1['nama'];
                    
                    $sudahlewat=false;
                    $pjmlemp=0;
                    
                    $query2 ="select * from $tmp01 WHERE DATE_FORMAT(bulan,'%Y%m')='$fbulan' and karyawanid='$pkaryawanid' order by nama, karyawanid, nama_area, idrutin, coa, nama_coa";
                    $tampil2=mysqli_query($cnit, $query2);
                    while ($row2= mysqli_fetch_array($tampil2)) {
                        $pkaryawanid=$row2['karyawanid'];
                        $pkaryawannm=$row2['nama'];
                        $pnmarea=$row2['nama_area'];
                        $pidruin=$row2['idrutin'];
                        $pbridnm=$row2['nama_brid'];
                        $pcoa=$row2['coa'];
                        $pcoanm=$row2['nama_coa'];
                        
                        $pjumlah=$row2['jumlah'];
                        $pjml=$row2['rptotal'];

                        $pjmlemp=(double)$pjmlemp+(double)$pjml;
                        $pjmlbln=(double)$pjmlbln+(double)$pjml;
                        $ptotjumlah=(double)$ptotjumlah+(double)$pjml;

                        $pjumlah=number_format($pjumlah,0,",",",");
                        $pjml=number_format($pjml,0,",",",");
                        


                        echo "<tr>";
                        if ($sudahlewat==false) {
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pkaryawannm</td>";
                            echo "<td nowrap>$pnmarea</td>";
                            echo "<td nowrap>$pidruin</td>";
                        }else{
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                        }
                        $sudahlewat=true;
                        
                        echo "<td>$pbridnm</td>";
                        echo "<td>$pcoa</td>";
                        echo "<td>$pcoanm</td>";
                        echo "<td nowrap align='right'>$pjml</td>";
                        echo "</tr>";

                    }
                    $pjmlemp=number_format($pjmlemp,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'><b>Sub Total</b></td>";
                    echo "<td nowrap align='right'><b>$pjmlemp</b></td>";
                    echo "</tr>";
                    
                    $no++;
                }
                
                $pjmlbln=number_format($pjmlbln,0,",",",");

                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'><b>TOTAL $pbulan</b></td>";
                echo "<td nowrap align='right'><b>$pjmlbln</b></td>";
                echo "</tr>";
                
                
            }
            
            $ptotjumlah=number_format($ptotjumlah,0,",",",");

            echo "<tr>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap align='right'><b>GRAND TOTAL</b></td>";
            echo "<td nowrap align='right'><b>$ptotjumlah</b></td>";
            echo "</tr>";
            

            
            // SUMARRY ALL BULAN
            echo "<tr>";
            echo "<td colspan='8'></td>";
            echo "</tr>";
            
            echo "<tr>";
            echo "<td colspan='8'><b>SUMMARY : </b></td>";
            echo "</tr>";
            
            $no=1;
            $ptotjumlah=0;
            
            $query1 = "select Distinct karyawanid, nama from $tmp01 order by 2,1";
            $tampil1=mysqli_query($cnit, $query1);
            while ($row1= mysqli_fetch_array($tampil1)) {
                $pkaryawanid=$row1['karyawanid'];
                $pkaryawannm=$row1['nama'];
                
                $sudahlewat=false;
                $pjmlemp=0;
                
                $query2 ="select karyawanid, nama, areaid, nama_area, nobrid, nama_brid, coa, nama_coa, sum(rptotal) rptotal "
                        . " from $tmp01 WHERE karyawanid='$pkaryawanid' GROUP BY 1,2,3,4,5,6,7,8 ORDER BY nama, karyawanid, nama_area, nama_brid, coa";
                $tampil2=mysqli_query($cnit, $query2);
                while ($row2= mysqli_fetch_array($tampil2)) {
                    $pkaryawanid=$row2['karyawanid'];
                    $pkaryawannm=$row2['nama'];
                    $pnmarea=$row2['nama_area'];
                    $pbridnm=$row2['nama_brid'];
                    $pcoa=$row2['coa'];
                    $pcoanm=$row2['nama_coa'];
                    $pjml=$row2['rptotal'];

                    $pjmlemp=(double)$pjmlemp+(double)$pjml;
                    $ptotjumlah=(double)$ptotjumlah+(double)$pjml;

                    $pjml=number_format($pjml,0,",",",");


                    echo "<tr>";
                    if ($sudahlewat==false) {
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pkaryawannm</td>";
                        echo "<td nowrap>$pnmarea</td>";
                    }else{
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                        echo "<td nowrap></td>";
                    }
                    $sudahlewat=true;
                    
                    echo "<td nowrap></td>";

                    echo "<td>$pbridnm</td>";
                    echo "<td>$pcoa</td>";
                    echo "<td>$pcoanm</td>";
                    echo "<td nowrap align='right'>$pjml</td>";
                    echo "</tr>";

                    
                }
            
                $pjmlemp=number_format($pjmlemp,0,",",",");

                echo "<tr>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'><b>Sub Total</b></td>";
                echo "<td nowrap align='right'><b>$pjmlemp</b></td>";
                echo "</tr>";

                $no++;
                
            }
            
            $ptotjumlah=number_format($ptotjumlah,0,",",",");
            
            echo "<tr>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            echo "<td nowrap></td>";
            
            echo "<td nowrap align='right'><b>GRAND TOTAL</b></td>";
            echo "<td nowrap align='right'><b>$ptotjumlah</b></td>";
            echo "</tr>";
        ?>
        </tbody>
    </table>
    
<?PHP
}
echo "<br/>&nbsp;<br/>&nbsp";

hapusdata:
    mysqli_query($cnit, "drop temporary table $tmp01");
    mysqli_query($cnit, "drop temporary table $tmp02");
    mysqli_query($cnit, "drop temporary table $tmp03");
    mysqli_query($cnit, "drop temporary table $tmp04");
?>
</body>
</html>
