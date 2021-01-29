<?php
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
session_start();
$puser=$_SESSION['IDCARD'];
if (empty($puser)) {
    echo "ANDA HARUS LOGIN ULANG....!!!";
    exit;
}

    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $start = $time;

    
    
    include "ceknamadist.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];

    
    
    
    $target_dir = "../../fileupload/";
    $pdist=$_POST['uiddist'];
    $ptgl=$_POST['ubln'];
    $pnmfolder=$_POST['upilfolder'];
    
    $_SESSION['MSTIMPPERTPIL']=$ptgl;
    $_SESSION['MSTIMPDISTPIL']=$pdist;
    $_SESSION['MSTIMPFOLDPIL']=$pnmfolder;
            
    $pname_foder_dist=CekNamaDist($pdist);
    $pbulan =  date("Ym", strtotime($ptgl));
    
    $target_dir .=$pbulan."/";
    $target_dir .=$pname_foder_dist."/";
    
    $filename = basename($target_dir.'/'.$pnmfolder);
    $filenameWX = preg_replace("/\.[^.]+$/", "", $filename);
    
    $inputFileName = $target_dir.$filename;
    
    //ubah juga di prosesdata_
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.import_mulyaraya");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE import_mulyaraya : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.import_mulyaraya");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE import_mulyaraya : $erropesan"; exit; }
    }
    //END IT
    
    require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
    require('spreadsheet-reader-master/SpreadsheetReader.php');
    
    $Reader = new SpreadsheetReader($target_dir.$pnmfolder);
    
    $jmlrec=0;
    foreach ($Reader as $Key => $Row) {
        // import data excel mulai baris ke-2 (karena ada header pada baris 1)
        if ($Key <= 3) continue;
        
        $pfile0="";
        if (isset($Row[0])) $pfile0=trim($Row[0]);
        $pfile1=trim($Row[1]);
        $pfile2=trim($Row[2]);
        $pfile3=trim($Row[3]);
        $pfile4=trim($Row[4]);
        $pfile5=trim($Row[5]);
        $pfile6=trim($Row[6]);
        $pfile7=trim($Row[7]);
        $pfile8=trim($Row[8]);
        $pfile9=trim($Row[9]);
        $pfile10=trim($Row[10]);
        $pfile11=trim($Row[11]);
        
        if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4) AND empty($pfile5)
                 AND empty($pfile6) AND empty($pfile7) AND empty($pfile8) AND empty($pfile9) AND empty($pfile10) AND empty($pfile11)) {
            continue;
        }
        
        if (!empty($pfile2)) $pfile2 = str_replace("'", "", $pfile2);
        if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
        if (!empty($pfile7)) $pfile7 = str_replace("'", " ", $pfile7);
        if (!empty($pfile8)) $pfile8 = str_replace("'", " ", $pfile8);
        if (!empty($pfile9)) $pfile9 = str_replace("*", "", $pfile9);
        if (!empty($pfile10)) $pfile10 = str_replace("*", "", $pfile10);
        if (!empty($pfile11)) $pfile11 = str_replace("*", "", $pfile11);
        
        if (!empty($pfile9)) $pfile9 = str_replace(" ", "", $pfile9);
        if (!empty($pfile10)) $pfile10 = str_replace(" ", "", $pfile10);
        if (!empty($pfile11)) $pfile11 = str_replace(" ", "", $pfile11);
            
        if (!empty($pfile4)) $pfile4=date('Y-m-d', strtotime($pfile4));
        
        if (!empty($pfile9)) $pfile9=str_replace(",","", $pfile9);
        if (!empty($pfile10)) $pfile10=str_replace(",","", $pfile10);
        if (!empty($pfile11)) $pfile11=str_replace(",","", $pfile11);
        
        
        
        
        $pfile1=trim($pfile1);
        $pfile2=trim($pfile2);
        $pfile3=trim($pfile3);
        $pfile5=trim($pfile5);
        $pfile6=trim($pfile6);
        $pfile7=trim($pfile7);
        $pfile8=trim($pfile8);
        $pfile9=trim($pfile9);
        $pfile10=trim($pfile10);
        $pfile11=trim($pfile11);
        
        
        //type
        $mkode=trim(substr($pfile6,0,3));
        if ($mkode=="SDE" OR $mkode=="sde" OR ucwords($mkode)=="SDE") {
            $pfile12="Ethical";
        }else{
            $pfile12="OTC";
        }
        
        
			$pfile20="";//NODPL
			
			//untuk promo
			if ($pfile6=="01524") {
				if (empty($pfile10)) $pfile10==0;
				if (empty($pfile9)) $pfile9==0;
				if ((DOUBLE)$pfile10<>0) {
					$pfile10=(DOUBLE)$pfile10/3;
					$pfile9=(DOUBLE)$pfile9*3;
					$pfile20="promo 3in1 glikoderm lumineux";
				}
			}
			
			
        $query = "INSERT INTO $dbname.import_mulyaraya (cust, `nama customer`, Tanggal, `No.Faktur`, "
                . " Kode, `nama barang`, satuan, kwantum, harga, total, type, nodpl)values"
                . " ('$pfile2', '$pfile3', '$pfile4', '$pfile5', "
                . " '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11', '$pfile12', '$pfile20')";

        //echo $query; mysqli_close($cnmy); exit;
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT import_mulyaraya : $erropesan"; exit; }
         
        //echo "$jmlrec. : $pfile0, $pfile1, $pfile2, $pfile3, $pfile4, $pfile5, $pfile6, $pfile7, $pfile8, $pfile9, $pfile10, $pfile11<br/>";
        
        $jmlrec++;
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT import_mulyaraya : $erropesan"; exit; }
        }
        //END IT
        
    }
    
    mysqli_query($cnmy, "DELETE FROM $dbname.import_mulyaraya WHERE IFNULL(cust,'')='' AND IFNULL(`nama customer`,'')='' AND IFNULL(Tanggal,'')='' AND IFNULL(kode,'')=''");
	
    $query = "select * from $dbname.import_mulyaraya WHERE IFNULL(Tanggal,'')= '' OR IFNULL(tanggal,'0000-00-00')= '0000-00-00' OR IFNULL(tanggal,'1970-01-01')= '1970-01-01'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        //mysqli_query($cnmy, "DELETE FROM $dbname.import_mulyaraya");
        //mysqli_close($cnmy);
        echo "ERROR SIMPAN... ADA TANGGAL KOSONG.";
        //exit;
    }
    
    //hapus data
    //unlink($target_dir.$filename);
    
    $query = "SELECT * FROM $dbname.import_mulyaraya WHERE DATE_FORMAT(Tanggal,'%Y%m')<>'$pbulan'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);    
    
    if ($ketemu>0) {
        echo "<div style='color:red;'><h1>Import tidak sesuai periode</h1></div>";
    }
    
?>

<div class='x_content' style="overflow-x:auto; max-height: 500px;">
    
    <table id='dtablepilprosmps' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='10px'>No</th>
                <th align="center" nowrap>Cust</th>
                <th align="center" nowrap>Nama Customer</th>
                <th align="center" nowrap>Tanggal</th>
                <th align="center" nowrap>No Faktur</th>
                <th align="center" nowrap>Kode</th>
                <th align="center" nowrap>Nama Barang</th>
                <th align="center" nowrap>Satuan</th>
                <th align="center" nowrap>Kwantum</th>
                <th align="center" nowrap>Harga</th>
                <th align="center" nowrap>Total</th>
                <th align="center" nowrap>Type</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $pgrdtotal=0;
                $no=1;
                $query = "select cust, `nama customer` as nmcust, Tanggal, `No.Faktur` as nofaktur, Kode, "
                        . " `nama barang` as nmbarang, satuan, kwantum, harga, total, type as ttype "
                        . " from $dbname.import_mulyaraya";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pkdcust=$row['cust'];
                        $pnmcust=$row['nmcust'];
                        $ptanggal=$row['Tanggal'];
                        $pnofaktur=$row['nofaktur'];
                        $pkode=$row['Kode'];
                        $pnmbrg=$row['nmbarang'];
                        $psatuan=$row['satuan'];
                        $pkwantum=$row['kwantum'];
                        $pharga=$row['harga'];
                        $ptotal=$row['total'];
                        $ptype=$row['ttype'];
                        
                        $pgrdtotal=(double)$pgrdtotal+(double)$ptotal;
                        
                        $ptanggal=date('d M Y', strtotime($ptanggal));
                        
                        $pharga=number_format($pharga,0,",",",");
                        $ptotal=number_format((double)$ptotal,0,",",",");
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pkdcust</td>";
                        echo "<td nowrap>$pnmcust</td>";
                        echo "<td nowrap>$ptanggal</td>";
                        echo "<td nowrap>$pnofaktur</td>";
                        echo "<td nowrap>$pkode</td>";
                        echo "<td nowrap>$pnmbrg</td>";
                        echo "<td nowrap>$psatuan</td>";
                        echo "<td nowrap align='right'>$pkwantum</td>";
                        echo "<td nowrap align='right'>$pharga</td>";
                        echo "<td nowrap align='right'>$ptotal</td>";
                        echo "<td nowrap>$ptype</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                    
                    $pgrdtotal=number_format($pgrdtotal,0,",",",");
                    echo "<tr>";
                    echo "<td nowrap colspan='10' align='center'><b>Grand Total : </b></td>";
                    echo "<td nowrap align='right'><b>$pgrdtotal</b></td>";
                    echo "<td nowrap></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>

    </table>
    
    
    <style>
        .divnone {
            display: none;
        }
        #dtablepilprosmps th {
            font-size: 13px;
        }
        #dtablepilprosmps td { 
            font-size: 11px;
        }
        .imgzoom:hover {
            -ms-transform: scale(3.5); /* IE 9 */
            -webkit-transform: scale(3.5); /* Safari 3-8 */
            transform: scale(3.5);

        }
    </style>
    
</div>


<?PHP

    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 4);
    
    echo "<br/>&nbsp;";
    echo "<br/>&nbsp;";
    
    echo "<b>Jumlah Proses Import : $jmlrec</b><br/>&nbsp;<br/>&nbsp;";
    echo "<b>TOTAL SALES : $pgrdtotal</b><br/>&nbsp;<br/>&nbsp;";
    echo "Selesai dalam ".$total_time." detik";
    
    
    
    mysqli_close($cnmy);
    
    //IT
    if ($plogit_akses==true) {
        mysqli_close($cnit);
    }
    
?>
