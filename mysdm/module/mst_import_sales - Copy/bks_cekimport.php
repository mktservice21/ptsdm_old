<?php
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
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
    
    
    //ubah juga di prosesdata_
    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    $dbname = "MKT";
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    
    include"PHPExcel.php";
    $objReader = PHPExcel_IOFactory::createReader('Excel2007');
    $inputFileType = 'Excel2007';
    $sheetIndex = 0;
    $inputFileName = $target_dir.$filename;
    
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $sheetnames = $objReader->listWorksheetNames($inputFileName);
    $objReader->setLoadSheetsOnly($sheetnames[$sheetIndex]);
    
    try {
        $objPHPExcel = $objReader->load($inputFileName);
    } catch(Exception $e) {
        die('Error loading file :' . $e->getMessage());
    }
        
    
    $worksheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
    $numRows = count($worksheet);
    
    mysqli_query($cnmy, "DELETE FROM $dbname.import_bks");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE import_bks : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.import_bks");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE import_bks : $erropesan"; exit; }
    }
    //END IT
    
    $jmlrec=0;
    //baca untuk setiap baris excel
    for ($i=2; $i <= $numRows ; $i++) {

        $pfile0=trim($worksheet[$i]['A']);
        $pfile1=trim($worksheet[$i]['B']);
        $pfile2=trim($worksheet[$i]['C']);
        $pfile3=trim($worksheet[$i]['D']);
        $pfile4=trim($worksheet[$i]['E']);
        $pfile5=trim($worksheet[$i]['F']);
        $pfile6=trim($worksheet[$i]['G']);
        $pfile7=trim($worksheet[$i]['H']);
        $pfile8=trim($worksheet[$i]['I']);
        $pfile9=trim($worksheet[$i]['J']);
        $pfile10=trim($worksheet[$i]['K']);
        $pfile11=trim($worksheet[$i]['L']);
        $pfile12=trim($worksheet[$i]['M']);
        $pfile13=trim($worksheet[$i]['N']);
        $pfile14=trim($worksheet[$i]['O']);
        
        
        if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
        if (!empty($pfile8)) $pfile8 = str_replace("'", " ", $pfile8);
        if (!empty($pfile9)) $pfile9 = str_replace("'", " ", $pfile9);
        if (!empty($pfile10)) $pfile10 = str_replace("'", " ", $pfile10);
        if (!empty($pfile11)) $pfile11 = str_replace("'", " ", $pfile11);
            
        $pfile5=date('Y-m-d', strtotime($pfile5));
        
        $query = "INSERT INTO $dbname.import_bks (jeniskode, slkode, brkode, brnama, "
                . " batchitem, tanggal, jlfkt2, plgkode, nama, alamat, kota, jumlah, satuan, harsat, ttlhna)values"
                . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11', '$pfile12', '$pfile13', '$pfile14')";

        //echo $query; dbase_close($pinsert); mysqli_close($cnmy); exit;
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT import_bks : $erropesan"; exit; }
        //echo "$pfile5<br/>";
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT import_bks : $erropesan"; exit; }
        }
        //END IT
        
        $jmlrec++;
    }
    
    
    $query = "select * from $dbname.import_bks WHERE IFNULL(tanggal,'')= '' OR IFNULL(tanggal,'0000-00-00')= '0000-00-00' OR IFNULL(tanggal,'1970-01-01')= '1970-01-01'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        mysqli_close($cnmy);
        echo "ERROR SIMPAN... ADA TANGGAL KOSONG.";
        exit;
    }
    
    //hapus data
    //unlink($target_dir.$filename);
    
    
    $query = "SELECT * FROM $dbname.import_bks WHERE DATE_FORMAT(tanggal,'%Y%m')<>'$pbulan'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);    
    
    if ($ketemu>0) {
        echo "<div style='color:red;'><h1>Import tidak sesuai periode</h1></div>";
    }

?>

<div class='x_content' style="overflow-x:auto; max-height: 500px;">
    
    <table id='dtablepilprosbks' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='10px'>No</th>
                <th align="center" nowrap>Jenis Kode</th>
                <th align="center" nowrap>Sl Kode</th>
                <th align="center" nowrap>BRKODE</th>
                <th align="center" nowrap>BRNAMA</th>
                <th align="center" nowrap>Batch Item</th>
                <th align="center" nowrap>Tanggal</th>
                <th align="center" nowrap>JLFKT2</th>
                <th align="center" nowrap>PLGKODE</th>
                <th align="center" nowrap>Nama</th>
                <th align="center" nowrap>Alamat</th>
                <th align="center" nowrap>Kota</th>
                <th align="center" nowrap>Jumlah</th>
                <th align="center" nowrap>Satuan</th>
                <th align="center" nowrap>Harga Sat</th>
                <th align="center" nowrap>Total HNA</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $pgrdtotal=0;
                $no=1;
                $query = "select jeniskode, slkode, brkode, brnama, batchitem, "
                        . " tanggal, jlfkt2, plgkode, nama, alamat, kota, jumlah, satuan, harsat, ttlhna "
                        . " from $dbname.import_bks";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pjeniskode=$row['jeniskode'];
                        $pslkode=$row['slkode'];
                        $pbrkode=$row['brkode'];
                        $pbrnama=$row['brnama'];
                        $pbatchk=$row['batchitem'];
                        $ptanggal=$row['tanggal'];
                        $pjlfkt=$row['jlfkt2'];
                        $pplgkode=$row['plgkode'];
                        $pnama=$row['nama'];
                        $palamat=$row['alamat'];
                        $pkota=$row['kota'];
                        $pjumlah=$row['jumlah'];
                        $psatuan=$row['satuan'];
                        $pharga=$row['harsat'];
                        $ptotal=$row['ttlhna'];
                        
                        $ptanggal=date('d M Y', strtotime($ptanggal));
                        $pgrdtotal=(double)$pgrdtotal+(double)$ptotal;
                        
                        $pharga=number_format($pharga,0,",",",");
                        $ptotal=number_format((double)$ptotal,0,",",",");
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pjeniskode</td>";
                        echo "<td nowrap>$pslkode</td>";
                        echo "<td nowrap>$pbrkode</td>";
                        echo "<td nowrap>$pbrnama</td>";
                        echo "<td nowrap>$pbatchk</td>";
                        echo "<td nowrap>$ptanggal</td>";
                        echo "<td nowrap>$pjlfkt</td>";
                        echo "<td nowrap>$pplgkode</td>";
                        echo "<td nowrap>$pnama</td>";
                        echo "<td nowrap>$palamat</td>";
                        echo "<td nowrap>$pkota</td>";
                        echo "<td nowrap align='right'>$pjumlah</td>";
                        echo "<td nowrap>$psatuan</td>";
                        echo "<td nowrap align='right'>$pharga</td>";
                        echo "<td nowrap align='right'>$ptotal</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                    
                    $pgrdtotal=number_format($pgrdtotal,0,",",",");
                    echo "<tr>";
                    echo "<td nowrap colspan='15' align='center'><b>Grand Total : </b></td>";
                    echo "<td nowrap align='right'><b>$pgrdtotal</b></td>";
                    echo "</tr>";
                }
            ?>
        </tbody>

    </table>
    
    
    <style>
        .divnone {
            display: none;
        }
        #dtablepilprosbks th {
            font-size: 13px;
        }
        #dtablepilprosbks td { 
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
