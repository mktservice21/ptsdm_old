<?php
session_start();
include("config/koneksimysqli_it.php");
include("config/koneksimysqli.php");
include("config/common.php");

$pbrid=$_GET['brid'];
$nbrid="";
if (!empty($pbrid)) {
    $pbrid=substr($pbrid, 0, -1);
    $arr_brid = explode (",",$pbrid);
    
    for ( $i = 0; $i < count( $arr_brid ); $i++ ) {
        $nbrid= $nbrid."'".$arr_brid[$i]."',";
    }
    $nbrid="(".substr($nbrid, 0, -1).")";
    $pbrid=$nbrid;
}else{
    $pbrid="('')";
}

?>
<html>
<head>
  <title>Isi Report Surabaya</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="images/icon.ico" />
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    
    <script>
    function disp_confirm(pText_)  {
        ok_ = 1;

            if (ok_) {
                    var r=confirm(pText_)
                    if (r==true) {
                            //document.write("You pressed OK!")
                            document.getElementById("rpbosby1").action = "module/mod_br_spdotc/brosby0.php";
                            document.getElementById("rpbosby1").submit();
                            return 1;
                    }
            } else {
                    //document.write("You pressed Cancel!")
                    return 0;
            }
    }
    </script>

</head>
<body>
<form id="rpbosby1" action="" method=post>
<?PHP
    if (empty($_SESSION['IDCARD'])) {
      echo 'not authorized';
      exit;
    } else {
        
        $disabled_ = "disabled";

        $tahun = date('Y'); 
        $tanggal1 = date('d'); 
        $bln1 = date('m');
        $tahun1 = date('Y');
        $tahun_1 = $tahun - 1;
        $tahun_2 = $tahun + 1;
        $records1 = 0;

        echo "<b>INPUT REPORT SBY</b><br>";
        $query = "select * from hrd.br_otc where brOtcId IN $pbrid";

        $result = mysqli_query($cnmy, $query);
        $records = mysqli_num_rows($result);	//echo"REC=$records";
        $row = mysqli_fetch_array($result);
        if ($records) {
            $i = 1;
            $gtotal = $gtotal_ = 0;
            while ($i <= $records) {
                $bulan_ = $row['tgltrans'];
                echo '<table border="1" cellspacing="0" cellpadding="1">';
                echo "<br>";
                echo "<b>Tanggal Transfer : $bulan_</b>";
                echo '<tr>';
                echo '<th align="left"><small>No</small></th>';
                echo '<th align="center"><small>No Slip</small></th>';
                echo '<th align="center"><small>Nama</small></th>';
                echo '<th align="center"><small>Alokasi Budget</small></th>';
                echo '<th align="center">Keterangan Tempat</th>';
                echo '<th align="center">Keterangan</th>';
                echo '<th align="center"><small>Jumlah IDR</small></th>';
                echo '<th align="center"><small>Realisasi</small></th>';
                echo '<th align="center"><small>Tgl. Report SBY</small></th>';
                echo '<th align="center"><small>Report SBY</small></th>';
                echo '<th align="center"><small>Jenis</small></th>';
                echo '</tr>';
                $total = $total_ = 0;
                $no = 0;
                

                while ( ($i<=$records) and ($bulan_ == $row['tgltrans']) ) {
                    $brid = $row['brOtcId'];	
                    $no = $no + 1;			
                    $tgltrans = $row['tgltrans'];
                    $nobr = $row['brOtcId']; //echo"$noslip";			
                    $kodeid = $row['kodeid']; 
                    //$dokter = $row['dokter'];// echo"$dokter";
                    $keterangan1 = $row['keterangan1'];
                    $keterangan2 = $row['keterangan2'];
                    $jumlah = $row['jumlah']; 
                    $realisasi = $row['realisasi']; 
                    $icabangid_o = $row['icabangid_o'];
                    $tglrpsby = $row['tglrpsby'];
                    $sby = $row['sby'];
                    $jenis = $row['jenis'];
                    $noslip = $row['noslip'];
                    $total = $total + $jumlah;
                    $total_ = $total_ + $realisasi; //echo"$total_";
                    $gtotal = $gtotal + $jumlah;
                    $gtotal_ = $gtotal_ + $realisasi;

                    $j = "0000" . $i;
                    $j = substr($j,-4);
                    $var_ = "br" . $j;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
                    $brsby = $nobr;  //ganti $j dengan custid dari database

                    $nama_kd = '';
                    $query_kd = "select nama from hrd.brkd_otc where kodeid='$kodeid'"; //echo"$query_kd";
                    $result_kd = mysqli_query($cnmy, $query_kd);
                    $num_results_kd = mysqli_num_rows($result_kd);

                    if ($num_results_kd) {
                         $row_kd = mysqli_fetch_array($result_kd);
                         $nama_kd = $row_kd['nama'];
                    }

                    if ($icabangid_o=='MD') {
                        $nama_cab = 'MD'; 
                    } else {
                        if ($icabangid_o=='HO') {
                            $nama_cab = 'HO'; 
                        } else {
                            $nama_cab = '';
                            $query_cab = "select nama from MKT.icabang_o where icabangid_o='$icabangid_o'";
                            $result_cab = mysqli_query($cnmy, $query_cab);
                            $num_results_cab = mysqli_num_rows($result_cab);
                            if ($num_results_cab) {
                                 $row_cab = mysqli_fetch_array($result_cab);
                                 $nama_cab = $row_cab['nama'];
                            }
                            if (empty($nama_cab)) {
                                $query_cab = "select initial nama from dbmaster.cabang_otc where cabangid_ho='$icabangid_o'";
                                $result_cab = mysqli_query($cnmy, $query_cab);
                                $num_results_cab = mysqli_num_rows($result_cab);
                                if ($num_results_cab) {
                                     $row_cab = mysqli_fetch_array($result_cab);
                                     $nama_cab = $row_cab['nama'];
                                }
                            }
                        }
                    }


                    echo "<tr>";
                    echo "<td><small>$no</small></td>";
                    if ($noslip=="") {
                        echo "<td>&nbsp;</td>";
                    } else {
                        echo "<td><small>$noslip</small></td>";
                    }	

                    echo "<td><small>$nama_cab</small></td>";
                    echo "<td><small>$nama_kd</small></td>";
                    if ($keterangan1=="") {
                        echo "<td>&nbsp;</td>";
                    } else {
                        echo "<td><small>$keterangan1</small></td>";	
                    }		
                    if ($keterangan2=="") {
                        echo "<td>&nbsp;</td>";
                    } else {
                        echo "<td><small>$keterangan2</small></td>";	
                    }	
                    echo "<td align=right><small>".number_format($jumlah,0)."</small></td>";
                    echo "<td align=right><small>".number_format($realisasi,0)."</small></td>";
                    
                    $checked_ = "checked";
                    if ($tglrpsby=="0000-00-00"){
                        echo "<td><small>&nbsp;<small></td>";
                        echo "<td><input type='checkbox' name='$var_' value='$brsby' $checked_></td>";
                    } else {
                        echo "<td><small>$tglrpsby</small></td>";
                        if ($sby=='Y') {
                            $checked_ = "checked";
                            echo "<td><input type='checkbox' name='$var_' value='$brsby' $checked_  $disabled_>";
                        } else {
                            echo '<td><input type="checkbox" name="'.$var_.'" value="'.$brsby.'"></td>';
                        }
                    }


                    if ($jenis==""){
                        echo "<td><small>&nbsp;<small></td>";
                    } else {
                        if ($jenis=='A') {
                            $jns = 'Advance';
                        } else {
                            $jns = 'Klaim';
                        }
                        echo "<td><small>$jns</small></td>";
                    }
                    echo "</tr>";

                    $row = mysqli_fetch_array($result);
                    $i++;
                }// break per tanggal transfer
                
                echo "<tr>";
                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                echo "<td align=right><b>Total :</td>";
                echo "<td align=right><b>".number_format($total,0)."</b></td>";
                echo "<td align=right><b>".number_format($total_,0)."</b></td>";
                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; // report SBY

                echo "</tr>";
                
            }
            
            echo "<tr>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
            echo "<td align=right><b>Grand Total :</td>";
            echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
            echo "<td align=right><b>".number_format($gtotal_,0)."</b></td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; // report SBY
            echo "</tr>";
            echo "</table>";
                        
        } else {
            echo "<br /><b>Data tidak ditemukan!!!</b><br/><br/>";		
        }
        
        
        echo "<br>";
        echo "<table>";
        echo '<tr>';
        echo '<td>Tanggal Report SBY</td>';
        echo "<td>:</td>";
        echo "<td><select name='tanggal1' id='tanggal1' >";
        for ($i=1; $i<32; $i++) {
            $i_ = substr('0'.$i,-2);		
            if ($i == $tanggal1) {
                echo "<option selected='selected' value='$i_'>$i_</option>";	
            } else {
                echo "<option value='$i_'>$i_</option>";					
            }
        }		
        echo '</select>';

        echo "&nbsp;&nbsp;<select name='bln1' id='bln1'>";
        for ($i=0; $i<12; $i++) {
            $j = '0'.ltrim(strval($i+1));
            $j = substr($j,-2,2);
            $bln_ = nama_bulan($j);
            if ($j == $bln1) {
                echo "<option selected='selected' value='$j'>$bln_</option>";	
            } else {
                echo "<option value='$j'>$bln_</option>";					
            }
        }		
        echo '</select>';

        $tahun_1 = $tahun1 - 1;
        $tahun_2 = $tahun1 + 1;

        echo "&nbsp;&nbsp;<select name='tahun1' id='tahun1'>";
        echo "<option value='$tahun_1'>$tahun_1</option>";
        echo "<option selected='selected' value='$tahun1'>$tahun1</option>";
        echo "<option value='$tahun_2'>$tahun_2</option>";
        echo '</select>';		
        echo '</td>';		  
        echo '</tr>'; 

        echo "<tr><td>Jenis</td>";
        echo "<td>:</td>";
        echo "<td><select name='jenis' id='jenis'>";
        echo "<option value='A'>Advance</option>";
        echo "<option value='K'>Klaim</option>";
        echo "<option value='S'>Sudah minta uang muka</option>";
        echo "</select></td>";
        echo "</tr>";
        echo "</table>";
        
        
	echo "<br><br><input type=button name=cmdSave id=cmdSave value=Save onclick='disp_confirm(\"Simpan ?\")'>";
	echo "<input type=hidden name=records value=$records />";
	echo "<input type=hidden name=records1 value=$records1 />";
        
    }
?>
</form>
</body>
</html>
