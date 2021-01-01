<?PHP
    session_start();
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI LUAR KOTA.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/common.php");
    $cnit=$cnmy;
?>
<html>
<head>
    <title>REALISASI LUAR KOTA</title>
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
    
    $pbulan = date("Y-m-d");
    $pnoidinputspd="";
    $psudahprosesfin="";
    $stsreport="";
    $pprosid_sts="";
    if (isset($_GET['ispd'])) {
        $pnoidinputspd=$_GET['ispd'];
        if (!empty($pnoidinputspd)) {
            $query ="select igroup, bulan from dbmaster.t_brrutin_ca_close_head WHERE idinput='$pnoidinputspd'";
            $tampil= mysqli_query($cnmy, $query);
            $nr= mysqli_fetch_array($tampil);
            $pbulan=$nr['bulan'];
            $pprosid_sts=$nr['igroup'];
            $stsreport="C";
        }
        $psudahprosesfin="fin";
    }else{
        $pbulan=$_POST['bulan1'];
        $pperiode= date("Ym", strtotime($pbulan));
        $psudahprosesfin=$_POST['sts_apv'];
        $stsreport=$_POST['sts_rpt'];
        
        if (isset($_POST['sts_sudahprosesid'])) {
            $pprosid_sts=$_POST['sts_sudahprosesid'];
        }
        
        if (!empty($pprosid_sts)) {
            $query ="select idinput from dbmaster.t_brrutin_ca_close_head WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode' AND igroup='$pprosid_sts'";
            $tampil= mysqli_query($cnmy, $query);
            $nr= mysqli_fetch_array($tampil);
            $pnoidinputspd=$nr['idinput'];
            $stsreport="C";
            $psudahprosesfin="fin";
        }
        
    }
    
    $bulan= date("Ym", strtotime($pbulan));
    $perBlnThn1= date("F Y", strtotime($pbulan));
    
    //harus ada diseleksi
        $pilih_koneksi="config/koneksimysqli.php";
        $ptgl_pillih = $pbulan;
            //$stsreport = $_POST['sts_rpt']; //sudah ada diatas, status sudah closing/belum/all
            //$pprosid_sts = ""; //sudah ada diatas, status sudah proses closing 1/2
        $scaperiode1 = "";
        $scaperiode2 = "";
        $iproses_simpandata=false;
        $u_filterkaryawan="";
    //END harus ada diseleksi
    //seleksi data
    include ("module/mod_br_closing_lkca_baru/seleksi_data_lk_ca.php");
    mysqli_query($cnit, "drop TEMPORARY table $tmp00");
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
    mysqli_query($cnit, "drop TEMPORARY table $tmp05");
        
    
    $query ="SELECT divisi, karyawanid, nama, nama_area, sum(AMOUNT) as AMOUNT FROM ("
            . "select distinct igroup, divisi, karyawanid, nama_karyawan nama, '' as nama_area, saldo AMOUNT from $tmp01"
            . ") as TBL GROUP BY 1,2,3,4";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
        
    mysqli_query($cnit, "DELETE FROM $tmp02 WHERE IFNULL(AMOUNT,0)=0"); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    
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
    
    $ntgl_apv1="";
    $ntgl_apv2="";
    $ntgl_apv_dir1="";
    $ntgl_apv_dir2="";
    
    $pdivnomor="";
    
    $nnama_ss_mktdir1="FARIDA SOEWANTO";
    $nnama_ss_mktdir2="EVI KOSINA SANTOSO";
    
    $nnama_ss_mktdir=$nnama_ss_mktdir1;
	
	$ptgltransbank="";
	
    if (!empty($pnoidinputspd)) {

        $query = "select * from dbmaster.t_suratdana_br WHERE idinput='$pnoidinputspd'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            
            $pdivnomor= $ra['nodivisi'];
            
            $ngbr_idinput=$ra['idinput'];
            
            $gbrttd_fin1=$ra['gbr_apv1'];
            $gbrttd_fin2=$ra['gbr_apv2'];
            
            $gbrttd_dir1=$ra['gbr_dir'];
            $gbrttd_dir2=$ra['gbr_dir2'];
            
			$tgljakukannya=$ra['tgl'];
			if ($tgljakukannya=="0000-00-00") $tgljakukannya="";
			if (!empty($tgljakukannya)) $tgljakukannya = date("Ymd", strtotime($tgljakukannya));
			
				if (!empty($tgljakukannya)) {
					if ((double)$tgljakukannya>='20200701') {
						$nnama_ss_mktdir=$nnama_ss_mktdir2;
					}
				}
				
			
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
		
        $query = "select tanggal from dbmaster.t_suratdana_bank where idinput='$pnoidinputspd' and stsinput='K' order by tanggal";
		$tampil2= mysqli_query($cnmy, $query);
		$ketemu2= mysqli_num_rows($tampil2);
		if ($ketemu2>0) {
			$rs= mysqli_fetch_array($tampil2);
			$ptgltransbank=$rs['tanggal'];
			if ($ptgltransbank=="0000-00-00") $ptgltransbank="";
			if (!empty($ptgltransbank)) {
				$ptgltransbank = date("d/m/Y", strtotime($ptgltransbank));
			}
			
		}
		
		
    }
    
    
    
    $e_stsapv="Semua Data";
    if ($psudahprosesfin == "fin") {
        $e_stsapv="Sudah Proses Finance";
    }elseif ($psudahprosesfin == "belumfin") {
        $e_stsapv="Belum Proses Finance";
    }
    
        
?>

    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                <tr><td width="200px"><b>Realisasi Luar Kota Per </b></td><td><?PHP echo "$perBlnThn1 "; ?></td></tr>
                <tr><td width="150px"><b>No.BR </b></td><td><?PHP echo "$pdivnomor"; ?></td></tr>
                <tr><td><b>Status Approve </b></td><td><?PHP echo "$e_stsapv"; ?></td></tr>
				<?PHP
				if (!empty($ptgltransbank)) {
					echo "<tr><td width='200px'><b>Tanggal Transfer </b></td><td>$ptgltransbank</td></tr>";
				}
				?>
				
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
                <th align="center">Divisi</th>
                <th align="center">Biaya Luar Kota <?PHP echo "$perBlnThn1 "; ?></th>
            </thead>
            <tbody>
                <?PHP
                    $no=1;
                    $total=0;
                    $totalpot=0;
                    $totalpen=0;
                    $totalbay=0;
                    $query = "select * from $tmp02 order by divisi, nama, karyawanid";
                    $result = mysqli_query($cnit, $query);
                    $records = mysqli_num_rows($result);
                    $row = mysqli_fetch_array($result);
                    
                    if ($records) {
                        $reco = 1;
                        while ($reco <= $records) {
                            $noid=$row['karyawanid'];
                            $nama=$row['nama'];
                            $area=$row['nama_area'];
                            $pdivisi=$row['divisi'];
                            if ($pdivisi=="CAN") $pdivisi="CANARY";
                            
                            $jumlah=$row['AMOUNT'];
                            if ($bulan=="201908" AND $noid=="0000000257"){//AILISIA WONGSO
                                //$jumlah=(double)$jumlah-17000;
                            }elseif ($bulan=="201908" AND $noid=="0000001668"){//EVI FIRDAUS
                                //$jumlah=(double)$jumlah-53897;
                            }
                            
                            $total = (double)$total + (double)$jumlah;
                            
                            $jumlah=number_format($jumlah,0,",",",");
                            
                            
                            
                            echo "<tr>";
                            echo "<td align='center'>$no</td>";
                            echo "<td nowrap style='padding-left:5px;'>$nama</td>";
                            echo "<td nowrap align='center'>$pdivisi</td>";
                            echo "<td align='right' style='padding-right:5px;'>$jumlah</td>";
                            echo "</tr>";
                            
                            $no++;
                            $row = mysqli_fetch_array($result);
                            $reco++;  
                            
                        }
                        $total=number_format($total,0,",",",");
                        //TOTAL
                        echo "<tr>";
                        echo "<td colspan=3 align='right'><b>Total : </b></td>";
                        echo "<td align='right' style='padding-right:5px;'><b>$total</b></td>";
                        echo "</tr>";
                    }
                    
                    
                hapusdata:
                    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
                    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
                    mysqli_close($cnit);
                ?>
            </tbody>
        </table>
    
        <?PHP
            if ($bulan=="201908"){
                echo "<table class='' width=''>";
                    echo "<tr>";
                    echo "<td colspan=3><b>Kelebihan LK : </b></td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>AILISIA WONGSO</td> <td>(No. 012/LK/VIII/19)</td> <td> : </td><td>Rp. 17,000</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>EVI FIRDAUS</td> <td>(No. 013/LK/VIII/19)</td> <td> : </td><td>Rp. 53,897</td>";
                    echo "</tr>";
                echo "</table>";
                
                echo "<br/>&nbsp;";
            }else{
                echo "<br/>&nbsp;<br/>&nbsp;";
            }
        
        
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
                    echo "Menyetujui,";
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv_dir1<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>$nnama_ss_mktdir</b></td>";
					
                    echo "<td align='center'>";
                    echo "Mengetahui,";
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>$ntgl_apv_dir2<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>IRA BUDISUSETYO</b></td>";
					
					
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
                    echo "Menyetujui,";
                    if (!empty($namapengaju_ttd1))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>$nnama_ss_mktdir</b></td>";

                    
                    echo "<td align='center'>";
                    echo "Mengetahui,";
                    if (!empty($namapengaju_ttd2))
                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                    else
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "<b>IRA BUDISUSETYO</b></td>";
                    
                echo "</tr>";

            echo "</table>";
        }
        ?>
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
