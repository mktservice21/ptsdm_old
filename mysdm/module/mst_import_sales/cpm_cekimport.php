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
    
    
    //ubah juga di prosesdata_
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.import_cpm");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE import_cpm : $erropesan"; exit; }
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.import_cpm");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE import_cpm : $erropesan"; exit; }
    }
    //END IT
    
    
    include("../../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
    $objPHPExcel = PHPExcel_IOFactory::load($target_dir.$pnmfolder);
    
    $jmlrec=0;
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
        $totalrow = $worksheet->getHighestRow();
        $jmlrec=0;
        
        for($row=2; $row<=$totalrow; $row++){
            $pfile0 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(0, $row)->getValue());
            $pfile1 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(1, $row)->getValue());
            $pfile2 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(2, $row)->getValue());
            $pfile3 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(3, $row)->getValue());
            $pfile4 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(4, $row)->getValue());
            $pfile5 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(5, $row)->getValue());
            $pfile6 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(6, $row)->getValue());
            $pfile7 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(7, $row)->getValue());
            $pfile8 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(8, $row)->getValue());
            $pfile9 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(9, $row)->getValue());
            $pfile10 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(10, $row)->getValue());
            $pfile11 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(11, $row)->getValue());
            $pfile12 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(12, $row)->getValue());
            $pfile13 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(13, $row)->getValue());
            $pfile14 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(14, $row)->getValue());
            $pfile15 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(15, $row)->getValue());
            $pfile16 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(16, $row)->getValue());
            $pfile17 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(17, $row)->getValue());
            $pfile18 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(18, $row)->getValue());
            $pfile19 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(19, $row)->getValue());
            
            if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4) AND empty($pfile5)
                     AND empty($pfile6) AND empty($pfile7) AND empty($pfile8) AND empty($pfile9) 
                     AND empty($pfile10) AND empty($pfile11) AND empty($pfile12)
                     AND empty($pfile13) AND empty($pfile14) AND empty($pfile15) AND empty($pfile16) AND empty($pfile17) AND empty($pfile18)) {
                continue;
            }

            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            if (!empty($pfile2)) $pfile2 = str_replace("'", "", $pfile2);//tanpa spashi
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            
            if (!empty($pfile8)) $pfile8 = str_replace("'", "", $pfile8);//tanpa spashi
            if (!empty($pfile9)) $pfile9 = str_replace("'", " ", $pfile9);
            if (!empty($pfile10)) $pfile10 = str_replace("'", " ", $pfile10);
            if (!empty($pfile11)) $pfile11 = str_replace("'", " ", $pfile11);

            //$pfile6=date('d-M-yy', strtotime($pfile6));
            $dateValue1 = PHPExcel_Shared_Date::ExcelToPHP($pfile6);
            $pfile6     = date('d-M-y',$dateValue1);
            
            
            if (!empty($pfile12)) $pfile12 = str_replace("'", "", $pfile12);
            if (!empty($pfile12)) $pfile12 = str_replace(" ", "", $pfile12);
            if (!empty($pfile12)) $pfile12 = str_replace("*", "", $pfile12);

            if (!empty($pfile14)) $pfile14 = str_replace("'", "", $pfile14);
            if (!empty($pfile14)) $pfile14 = str_replace(" ", "", $pfile14);
            if (!empty($pfile14)) $pfile14 = str_replace("*", "", $pfile14);
            
            if (!empty($pfile15)) $pfile15 = str_replace("'", "", $pfile15);
            if (!empty($pfile15)) $pfile15 = str_replace(" ", "", $pfile15);
            if (!empty($pfile15)) $pfile15 = str_replace("*", "", $pfile15);

            if (!empty($pfile16)) $pfile16 = str_replace("'", "", $pfile16);
            if (!empty($pfile16)) $pfile16 = str_replace(" ", "", $pfile16);
            if (!empty($pfile16)) $pfile16 = str_replace("*", "", $pfile16);

            if (!empty($pfile17)) $pfile17 = str_replace("'", "", $pfile17);
            if (!empty($pfile17)) $pfile17 = str_replace(" ", "", $pfile17);
            if (!empty($pfile17)) $pfile17 = str_replace("*", "", $pfile17);

            if (!empty($pfile18)) $pfile18 = str_replace("'", "", $pfile18);
            if (!empty($pfile18)) $pfile18 = str_replace(" ", "", $pfile18);
            if (!empty($pfile18)) $pfile18 = str_replace("*", "", $pfile18);




            if (!empty($pfile12)) $pfile12=str_replace(",","", $pfile12);
            if (!empty($pfile14)) $pfile14=str_replace(",","", $pfile14);
            if (!empty($pfile15)) $pfile15=str_replace(",","", $pfile15);
            if (!empty($pfile16)) $pfile16=str_replace(",","", $pfile16);
            if (!empty($pfile17)) $pfile17=str_replace(",","", $pfile17);
            if (!empty($pfile18)) $pfile18=str_replace(",","", $pfile18);
            
            //untuk tanggal
            /*
            $data = $worksheet->getCellByColumnAndRow(6, $row);
            $dateh  = "";
            if(!strtotime($data)) {
                if(PHPExcel_Shared_Date::isDateTime($data)) {
                    $cellValue = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $dateValue = PHPExcel_Shared_Date::ExcelToPHP($cellValue);                     
                    $dateh     = date('Y-m-d',$dateValue);                     
                } else {                        
                    $dateh  = "";                                                   
                }                
            }
            */

            
            $query = "INSERT INTO $dbname.import_cpm (`jeniskode`, `brkode`, `brnama`, `batchitem`, "
                    . " `tanggal`, `jlfkt2`, `plgkode`, `nama`, `alamat`, `kota`, `jumlah`, "
                    . " `satuan`, `harsat`, `prodisc1`, `prodisc2`, `ttlitem`, `ttlhna`)values"
                    . " ('$pfile0', '$pfile3', '$pfile4', '$pfile5', "
                    . " '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11', '$pfile12', "
                    . " '$pfile13', '$pfile14', '$pfile15', '$pfile16', '$pfile17', '$pfile18')";

            //echo $query; dbase_close($pinsert); mysqli_close($cnmy); exit;
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT import_cpm : $erropesan"; exit; }
            
            //echo "$pfile5 dan $pfile6 dan $pfile12 dan $pfile14 dan $pfile15 dan $pfile16 dan $pfile17 dan $pfile18<br/>";
            
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT import_cpm : $erropesan"; exit; }
            }
            //END IT
            
            
            $jmlrec++;
        }
    }

    
    
    $query = "select * from $dbname.import_cpm WHERE IFNULL(`tanggal`,'')= '' OR IFNULL(`tanggal`,'0000-00-00')= '0000-00-00' OR IFNULL(`tanggal`,'1970-01-01')= '1970-01-01' OR IFNULL(`tanggal`,'01-Jan-70')= '01-Jan-70'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        mysqli_close($cnmy);
        echo "ERROR SIMPAN... ADA Tanggal KOSONG.";
        exit;
    }
    
    
    
    $query = "select DISTINCT `tanggal` as tglfaktur from $dbname.import_cpm WHERE DATE_FORMAT(`tanggal`,'%Y%m')<>'$pbulan'";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_= mysqli_num_rows($tampil_);
    if ($ketemu_>0) {
        $pmyperiode =  date("F Y", strtotime($ptgl));
        
        echo "<div style='color:red;'>";
        echo "Periode Pilih : $pmyperiode, ADA TANGGAL DOK YANG BERBEDA DENGAN PERIODE YANG DIPILIH<br/>";
        
        $myno=1;
        while ($nr= mysqli_fetch_array($tampil_)) {
            $ntgl=$nr['tglfaktur'];
            echo "$myno. $ntgl<br/>";
            $myno++;
        }
        
        echo "</div>";
    }
    
    
    //hapus data
    //unlink($target_dir.$filename);

?>

<div class='x_content' style="overflow-x:auto; max-height: 500px;">
    
    <table id='dtablepilprosbks' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='10px'>No</th>
                <th align="center" nowrap>JENIS KODE</th>
                <th align="center" nowrap>BRKODE</th>
                <th align="center" nowrap>BRNAMA</th>
                <th align="center" nowrap>BATCHITEM</th>
                <th align="center" nowrap>TANGGAL</th>
                <th align="center" nowrap>JLFKT2</th>
                <th align="center" nowrap>PLGKODE</th>
                <th align="center" nowrap>NAMA</th>
                <th align="center" nowrap>ALAMAT</th>
                <th align="center" nowrap>KOTA</th>
                <th align="center" nowrap>JUMLAH</th>
                <th align="center" nowrap>SATUAN</th>
                <th align="center" nowrap>HARSAT</th>
                <th align="center" nowrap>PRODISC1</th>
                <th align="center" nowrap>PRODISC2</th>
                <th align="center" nowrap>TTLITEM</th>
                <th align="center" nowrap>TTLHNA</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $pgrdtotal=0;
                $no=1;
                $query = "select `jeniskode`, `brkode`, `brnama`, `batchitem`, "
                    . " `tanggal`, `jlfkt2`, `plgkode`, `nama`, `alamat`, `kota`, `jumlah`, "
                    . " `satuan`, `harsat`, `prodisc1`, `prodisc2`, `ttlitem`, `ttlhna` "
                        . " from $dbname.import_cpm";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                $jmlrec=$ketemu;
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $nfile0=$row['jeniskode'];
                        $nfile1=$row['brkode'];
                        $nfile2=$row['brnama'];
                        $nfile3=$row['batchitem'];
                        $nfile4=$row['tanggal'];
                        $nfile5=$row['jlfkt2'];
                        $nfile6=$row['plgkode'];
                        $nfile7=$row['nama'];
                        $nfile8=$row['alamat'];
                        $nfile9=$row['kota'];
                        $nfile10=$row['jumlah'];
                        $nfile11=$row['satuan'];
                        $nfile12=$row['harsat'];
                        $nfile13=$row['prodisc1'];
                        $nfile14=$row['prodisc2'];
                        $nfile15=$row['ttlitem'];
                        $nfile16=$row['ttlhna'];
                        
                        //$ptanggal=date('d M Y', strtotime($ptanggal));
                        $pgrdtotal=(double)$pgrdtotal+(double)$nfile15;
                        
                        //$pharga=number_format($pharga,0,",",",");
                        //$ptotal=number_format((double)$ptotal,0,",",",");
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$nfile0</td>";
                        echo "<td nowrap>$nfile1</td>";
                        echo "<td nowrap>$nfile2</td>";
                        echo "<td nowrap>$nfile3</td>";
                        echo "<td nowrap>$nfile4</td>";
                        echo "<td nowrap>$nfile5</td>";
                        echo "<td nowrap>$nfile6</td>";
                        echo "<td nowrap>$nfile7</td>";
                        echo "<td nowrap>$nfile8</td>";
                        echo "<td nowrap>$nfile9</td>";
                        echo "<td nowrap align='right'>$nfile10</td>";
                        echo "<td nowrap>$nfile11</td>";
                        echo "<td nowrap align='right'>$nfile12</td>";
                        echo "<td nowrap align='right'>$nfile13</td>";
                        echo "<td nowrap align='right'>$nfile14</td>";
                        echo "<td nowrap align='right'>$nfile15</td>";
                        echo "<td nowrap align='right'>$nfile16</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                    
                    $pgrdtotal=number_format($pgrdtotal,0,",",",");
                    echo "<tr>";
                    echo "<td nowrap colspan='16' align='center'><b>Grand Total : </b></td>";
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
