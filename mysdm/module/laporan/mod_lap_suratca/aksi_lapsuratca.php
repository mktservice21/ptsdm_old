<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=LAPORAN SURAT CA.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>LAPORAN SURAT CA</title>
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
    
    //harus ada diseleksi
        $pilih_koneksi="config/koneksimysqli.php";
        $ptgl_pillih = $_POST['bulan1'];
        $stsreport = $_POST['sts_rpt'];
        $pprosid_sts = $_POST['sts_sudahprosesid'];
        $scaperiode1 = "";
        $scaperiode2 = "";
        $iproses_simpandata=false;
        $u_filterkaryawan="";
    //END harus ada diseleksi
    //seleksi data
    include ("module/mod_br_closing_lkca_baru/seleksi_data_lk_ca.php");
    
    $pjenispilih = "1";
    
    $pigroupid="";
    $ptgl_pil01= date("Y-m-01", strtotime($ptgl_pillih));
    $ptgl_pil02= date('Y-m-01', strtotime('+1 month', strtotime($ptgl_pillih)));
    
    if ($scaperiode2=="2") $ptgl_pil02=$ptgl_pil01;
    
    $ptgl_pil_sbl= date('Y-m-01', strtotime('-1 month', strtotime($ptgl_pillih)));
    
    $m_periode1 = date("Y-m", strtotime($ptgl_pil01));
    $m_periode2 = date("Y-m", strtotime($ptgl_pil02));
    $m_periode_sbl = date("Y-m", strtotime($ptgl_pil_sbl));
    
    $perBlnThn1 = date("F Y", strtotime($ptgl_pil01));
    $perBlnThn2 = date("F Y", strtotime($ptgl_pil02));
    
    
    $pidinputpd=""; $pidinputbank="";
    $pdivnomor="";
    
    $query = "select * from $tmp00";
    $tampil= mysqli_query($cnit, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $nr= mysqli_fetch_array($tampil);
        $pidinputpd = $nr['idinput'];
        $pidinputbank = $nr['idinputbank'];
        if ($pidinputpd=="0") $pidinputpd="";
        if ($pidinputbank=="0") $pidinputbank="";
    }
    
    if (!empty($pidinputpd)) {
        $query = "select nodivisi from dbmaster.t_suratdana_br WHERE idinput='$pidinputpd'";
        $tampil= mysqli_query($cnit, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0){
            $sc= mysqli_fetch_array($tampil);
            $pdivnomor=$sc['nodivisi'];
        }

    }
    
    $pnobukti=""; $ptgltrans="";
    if (!empty($pidinputbank)) {
        $query = "select nobukti, tanggal from dbmaster.t_suratdana_bank WHERE idinputbank='$pidinputbank'";
        $tampil= mysqli_query($cnit, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0){
            $sc= mysqli_fetch_array($tampil);
            $pnobukti=$sc['nobukti'];
            $ptgltrans = date('d F Y', strtotime($sc['tanggal']));
        }

    }
                    
    $stsapv = $_POST['sts_apv'];
    $e_stsapv="Semua Data";
    if ($stsapv == "fin") {
        $e_stsapv="Sudah Proses Finance";
    }elseif ($stsapv == "belumfin") {
        $e_stsapv="Belum Proses Finance";
    }
    
    

    $query = "alter table $tmp01 ADD idatasan CHAR(10)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    /*
    $query = "UPDATE $tmp01 SET idatasan=atasan1 WHERE jabatanid IN ('15')";//MR
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 SET idatasan=atasan2 WHERE jabatanid IN ('10', '18')";//AM / SPV
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 SET idatasan=atasan3 WHERE jabatanid IN ('08')";//DM
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //spv
    $query = "UPDATE $tmp01 SET idatasan=karyawanid WHERE jabatanid IN ('10', '18') AND karyawanid=atasan1";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //dm
    $query = "UPDATE $tmp01 SET idatasan=karyawanid WHERE jabatanid IN ('08') AND karyawanid=atasan2";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    */
    
    $query = "UPDATE $tmp01 SET atasan2=atasan1 WHERE atasan2=atasan3 and jabatanid in ('15', '10', '18') ";//SPV KOSONG
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp01 SET idatasan=atasan2 WHERE jabatanid IN ('15', '10', '18', '08')";//DM
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp01 SET idatasan=karyawanid WHERE IFNULL(idatasan,'')=''";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    
    $query = "SELECT DISTINCT a.karyawanid, a.nama_karyawan, a.saldo, a.ca1, a.ca2, jml_adj, a.idatasan, b.nama nama_atasan, a.selisih, a.jmltrans "
            . " from $tmp01 a LEFT JOIN hrd.karyawan b on a.idatasan=b.karyawanid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    //goto hapusdata;
    

    
    
    $pnmatasan="";
    $pidatasan="";
    $gtotca1=0; $gtotjmlsaldo=0; $gtotca2=0; $gtotadj=0; $gtotselisih=0; $gtottrans=0;
    
    $rp_gtotca1=0; $rp_gtotjmlsaldo=0; $rp_gtotca2=0; $rp_gtotadj=0; $rp_gtotselisih=0; $rp_gtottrans=0;
    
    $no=1;
    
?>
    
    <?PHP
    $query = "select distinct idatasan, nama_atasan from $tmp02 order by nama_atasan, idatasan";
    $tampil= mysqli_query($cnit, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pidatasan = $row['idatasan'];
        $pnmatasan = $row['nama_atasan'];
    ?>
        <table style="font-size:13px;">
            <tr><td nowrap colspan='2'><b>Kepada Yth :</b></td></tr>
            <tr><td nowrap colspan='2'><b><?PHP echo $pnmatasan; ?></b></td></tr>
            <tr><td nowrap colspan='2'><b>PT SDM-Jakarta</b></td></tr>
            <tr><td nowrap colspan='2'>&nbsp;</td></tr>
            <tr><td nowrap colspan='2'>Hal : Pengiriman Cash Advance</td></tr>
        </table>
        
        <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center" nowrap>No</th>
                    <th align="center" nowrap>Nama</th>
                    <th align="center" nowrap>CA <?PHP echo $perBlnThn1; ?><br/>Yg Hrs Diprtggjwb kan</th>
                    <th align="center" nowrap>Biaya Luar Kota <?PHP echo $perBlnThn1; ?></th>
                    <th align="center" nowrap>Saldo <?PHP echo $perBlnThn1; ?></th>
                    <th align="center" nowrap>CA <?PHP echo $perBlnThn2; ?><br/>Yg Diminta</th>
                    <th align="center" nowrap>CA Yg Dikirim</th>
                    <th align="center" nowrap>CA <?PHP echo $perBlnThn2; ?><br/>Yg Hrs dipertggjwbkan</th>
                <th align="center" nowrap>Jumlah yg ditransfer ke rek <br/><?PHP echo $pnmatasan; ?></th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $gtotca1=0; $gtotjmlsaldo=0; $gtotca2=0; $gtotadj=0; $gtotselisih=0; $gtottrans=0;
                
                $no=1;
                $query = "select * from $tmp02 WHERE idatasan='$pidatasan' order by nama_karyawan, karyawanid";
                $tampil_= mysqli_query($cnit, $query);
                while ($row1= mysqli_fetch_array($tampil_)) {
                    $pidkaryawan = $row1['karyawanid'];
                    $pnmkaryawan = $row1['nama_karyawan'];
                    
                    $pjmlca1 = $row1['ca1'];
                    $pjmllk = $row1['saldo'];
                    $pjmlca2 = $row1['ca2'];
                    $pjumlahadj = $row1['jml_adj'];
                    $pselisih = $row1['selisih'];
                    $pjmltrans = $row1['jmltrans'];  
                    
                    $gtotca1=(double)$gtotca1+(double)$pjmlca1;
                    $gtotjmlsaldo=(double)$gtotjmlsaldo+(double)$pjmllk;
                    $gtotca2=(double)$gtotca2+(double)$pjmlca2;
                    $gtotadj=(double)$gtotadj+(double)$pjumlahadj;
                    $gtotselisih=(double)$gtotselisih+(double)$pselisih;
                    $gtottrans=(double)$gtottrans+(double)$pjmltrans;
                    
                    $rp_gtotca1=(double)$rp_gtotca1+(double)$pjmlca1;
                    $rp_gtotjmlsaldo=(double)$rp_gtotjmlsaldo+(double)$pjmllk;
                    $rp_gtotca2=(double)$rp_gtotca2+(double)$pjmlca2;
                    $rp_gtotadj=(double)$rp_gtotadj+(double)$pjumlahadj;
                    $rp_gtotselisih=(double)$rp_gtotselisih+(double)$pselisih;
                    $rp_gtottrans=(double)$rp_gtottrans+(double)$pjmltrans;

                            
                    $pjmlca1=number_format($pjmlca1,0,",",",");
                    $pjmllk=number_format($pjmllk,0,",",",");
                    $pjmlca2=number_format($pjmlca2,0,",",",");
                    $pjumlahadj=number_format($pjumlahadj,0,",",",");
                    $pselisih=number_format($pselisih,0,",",",");
                    $pjmltrans=number_format($pjmltrans,0,",",",");
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap align='right'>$pjmlca1</td>";
                    echo "<td nowrap align='right'>$pjmllk</td>";
                    echo "<td nowrap align='right'>$pselisih</td>";
                    echo "<td nowrap align='right'>$pjmlca2</td>";
                    echo "<td nowrap align='right'>$pjmltrans</td>";
                    
                    echo "<td nowrap align='right'>$pjmlca2</td>";
                    echo "<td nowrap align='right'>$pjmltrans</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                
                
                $gtotca1=number_format($gtotca1,0,",",",");
                $gtotjmlsaldo=number_format($gtotjmlsaldo,0,",",",");
                $gtotca2=number_format($gtotca2,0,",",",");
                $gtotadj=number_format($gtotadj,0,",",",");
                $gtotselisih=number_format($gtotselisih,0,",",",");
                $gtottrans=number_format($gtottrans,0,",",",");
                    
                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap align='center'>TOTAL</td>";
                echo "<td nowrap align='right'>$gtotca1</td>";
                echo "<td nowrap align='right'>$gtotjmlsaldo</td>";
                echo "<td nowrap align='right'>$gtotselisih</td>";
                echo "<td nowrap align='right'>$gtotca2</td>";
                echo "<td nowrap align='right'>$gtottrans</td>";
                
                echo "<td nowrap align='right'>$gtotca2</td>";
                echo "<td nowrap align='right'>$gtottrans</td>";
                echo "</tr>";
            ?>
            </tbody>
        </table>
        <br/>&nbsp;
    <?PHP
    }
    
    
    ?>
    <table style="font-size:13px;">
        <tr><td nowrap colspan='2'><b>GRAND TOTAL</b></td></tr>
    </table>
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center" nowrap>CA <?PHP echo $perBlnThn1; ?><br/>Yg Hrs Diprtggjwb kan</th>
                <th align="center" nowrap>Biaya Luar Kota <?PHP echo $perBlnThn1; ?></th>
                <th align="center" nowrap>Saldo <?PHP echo $perBlnThn1; ?></th>
                <th align="center" nowrap>CA <?PHP echo $perBlnThn2; ?><br/>Yg Diminta</th>
                <th align="center" nowrap>CA Yg Dikirim</th>
                <th align="center" nowrap>CA <?PHP echo $perBlnThn2; ?><br/>Yg Hrs dipertggjwbkan</th>
            <th align="center" nowrap>Jumlah yg ditransfer ke rek</th>
            </tr>
        </thead>
        <tbody>
        <?PHP
            $rp_gtotca1=number_format($rp_gtotca1,0,",",",");
            $rp_gtotjmlsaldo=number_format($rp_gtotjmlsaldo,0,",",",");
            $rp_gtotca2=number_format($rp_gtotca2,0,",",",");
            $rp_gtotadj=number_format($rp_gtotadj,0,",",",");
            $rp_gtotselisih=number_format($rp_gtotselisih,0,",",",");
            $rp_gtottrans=number_format($rp_gtottrans,0,",",",");

            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap align='right'>$rp_gtotca1</td>";
            echo "<td nowrap align='right'>$rp_gtotjmlsaldo</td>";
            echo "<td nowrap align='right'>$rp_gtotselisih</td>";
            echo "<td nowrap align='right'>$rp_gtotca2</td>";
            echo "<td nowrap align='right'>$rp_gtottrans</td>";

            echo "<td nowrap align='right'>$rp_gtotca2</td>";
            echo "<td nowrap align='right'>$rp_gtottrans</td>";
            echo "</tr>";
        ?>
        </tbody>
    </table>
        
        
    <?PHP
    
    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
hapusdata:
    mysqli_query($cnit, "drop TEMPORARY table $tmp00");
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
    mysqli_query($cnit, "drop TEMPORARY table $tmp05");
    
    mysqli_close($cnit);
?>
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