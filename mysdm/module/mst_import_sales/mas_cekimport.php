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
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.import_mas");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE import_mas : $erropesan"; exit; }
    
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.import_mas");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE import_mas : $erropesan"; exit; }
    }
    //END IT
    
    
    include("../../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
    
    chmod($target_dir.$pnmfolder,0777);
    
    $objPHPExcel = PHPExcel_IOFactory::load($target_dir.$pnmfolder);
    
    $pisavedata=false;

    $jmlrec=0;
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
        $totalrow = $worksheet->getHighestRow();
        $jmlrec=0;
        
        for($row=2; $row<=$totalrow; $row++){
            
            $pfile7="DPS";
            $pfile8="";
            
            $pfilepabrik = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(14, $row)->getValue());//kode pabrik
            
            if (TRIM($pfilepabrik)!="SDM") continue;
            
            $pfile0 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(12, $row)->getValue());
            $pfile1 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(1, $row)->getValue());
            $pfile2 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(11, $row)->getValue());
            $pfile3 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(4, $row)->getValue());//nama pelanggan
            $pfile4 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(16, $row)->getValue());
            $pfile5 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(17, $row)->getValue());
            $pfile6 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(18, $row)->getValue());//nilai hna
            //$pfile7 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(18, $row)->getValue());//cabang
            //$pfile8 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(18, $row)->getValue());//brgid
            $pfile9 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(3, $row)->getValue());//kode pelanggan
            
            
            
            if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4) AND empty($pfile5)
                     AND empty($pfile6) AND empty($pfile9) ) {
                continue;
            }

            if (!empty($pfile0)) $pfile0 = str_replace("'", " ", $pfile0);
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            
            
            
            if (!empty($pfile5)) $pfile5 = str_replace("'", "", $pfile5);
            if (!empty($pfile5)) $pfile5 = str_replace(" ", "", $pfile5);
            if (!empty($pfile5)) $pfile5 = str_replace("*", "", $pfile5);

            if (!empty($pfile6)) $pfile6 = str_replace("'", "", $pfile6);
            if (!empty($pfile6)) $pfile6 = str_replace(" ", "", $pfile6);
            if (!empty($pfile6)) $pfile6 = str_replace("*", "", $pfile6);


            if (!empty($pfile5)) $pfile5=str_replace(",","", $pfile5);
            if (!empty($pfile6)) $pfile6=str_replace(",","", $pfile6);
            
            //untuk tanggal
            
            $data_tlg = $worksheet->getCellByColumnAndRow(1, $row);
            if(!strtotime($data_tlg)) {
                if(PHPExcel_Shared_Date::isDateTime($data_tlg)) {
                    $cellValue = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $dateValue = PHPExcel_Shared_Date::ExcelToPHP($cellValue);                     
                    $pfile1     = date('Y-m-d',$dateValue);                     
                } else {                        
                    $pfile1     = date('Y-m-d',$pfile1);
                }
            }else{
                $pfile1     = date('Y-m-d',$pfile1);
            }
            
            $pinst_data[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9')";
            $pisavedata=true;
            
            
            /*
            $query = "INSERT INTO $dbname.import_mas (`NAMA_BRG`, `TGLJUAL`, `FAKTURID`, `CUSTNAME`, "
                    . " `QTY`, `HNA`, `NILAI`, `CABANGID`, `BRGID`, `CUSTID`)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT import_mas : $erropesan"; exit; }
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT import_mas : $erropesan"; exit; }
            }
            //END IT
            */
            
            $jmlrec++;
        }
    }

    
    if ($pisavedata == true) {
        
        $query_ins_pil = "INSERT INTO $dbname.import_mas (`NAMA_BRG`, `TGLJUAL`, `FAKTURID`, `CUSTNAME`, "
                    . " `QTY`, `HNA`, `NILAI`, `CABANGID`, `BRGID`, `CUSTID`)values "
                . " ".implode(', ', $pinst_data);
        
        mysqli_query($cnmy, $query_ins_pil);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT import_mas : $erropesan"; exit; }

        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_ins_pil);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT import_mas : $erropesan"; exit; }
        }
        //END IT
        
    }
    
    
    mysqli_query($cnmy, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
    mysqli_query($cnmy, "create table $dbname.tmp_importprodfile_ipms (select DISTINCT DistId, eProdId, nama from $dbname.eproduk WHERE DistId='0000000016')");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE tmp_importprodfile_ipms eproduk $cabang : $erropesan"; exit; }
    
    
    $query_impmas ="UPDATE $dbname.import_mas a JOIN 
            (select DISTINCT DistId, eProdId, nama from $dbname.tmp_importprodfile_ipms WHERE DistId='0000000016') as b on RTRIM(a.NAMA_BRG)=RTRIM(b.nama) SET 
            a.BRGID=b.eProdId";
    mysqli_query($cnmy, $query_impmas);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error UPDATE BRGID : $erropesan"; exit; }
            
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DROP TABLE IF EXISTS $dbname.tmp_importprodfile_ipms");
        mysqli_query($cnit, "create table $dbname.tmp_importprodfile_ipms (select DISTINCT DistId, eProdId, nama from $dbname.eproduk WHERE DistId='0000000016')");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error CREATE tmp_importprodfile_ipms eproduk $cabang : $erropesan"; exit; }
    
        mysqli_query($cnit, $query_impmas);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error UPDATE BRGID : $erropesan"; exit; }
    }
    //END IT
    
    
    $query = "select distinct BRGID from $dbname.import_mas WHERE IFNULL(`BRGID`,'')= ''";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        echo "<span style='color:red;'>ERROR SIMPAN... MASIH ADA BRGID KOSONG.</span><br/>";
    }
    
    
    
    $query = "select * from $dbname.import_mas WHERE IFNULL(`TGLJUAL`,'')= '' OR IFNULL(`TGLJUAL`,'0000-00-00')= '0000-00-00' OR IFNULL(`TGLJUAL`,'1970-01-01')= '1970-01-01' OR IFNULL(`TGLJUAL`,'01-Jan-70')= '01-Jan-70'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        mysqli_close($cnmy);
        echo "<span style='color:red;'>ERROR SIMPAN... ADA Tanggal KOSONG.</span>";
        exit;
    }
    
    
    
    $query = "select DISTINCT `TGLJUAL` as tglfaktur from $dbname.import_mas WHERE DATE_FORMAT(`TGLJUAL`,'%Y%m')<>'$pbulan'";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_= mysqli_num_rows($tampil_);
    if ($ketemu_>0) {
        $pmyperiode =  date("F Y", strtotime($ptgl));
        
        echo "<div style='color:red;'>";
        echo "Periode Pilih : $pmyperiode, ADA TANGGAL YANG BERBEDA DENGAN PERIODE YANG DIPILIH<br/>";
        
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
                <th align="center" nowrap>NAMA BRG</th>
                <th align="center" nowrap>TGL JUAL</th>
                <th align="center" nowrap>FAKTURID</th>
                <th align="center" nowrap>CUSTOMER NAME</th>
                <th align="center" nowrap>QTY</th>
                <th align="center" nowrap>HNA</th>
                <th align="center" nowrap>NILAI</th>
                <th align="center" nowrap>CABANGID</th>
                <th align="center" nowrap>BRGIDT</th>
                <th align="center" nowrap>CUSTID</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $pgrdtotal=0;
                $no=1;
                $query = "select `NAMA_BRG`, `TGLJUAL`, `FAKTURID`, `CUSTNAME`, "
                    . " `QTY`, `HNA`, `NILAI`, `CABANGID`, `BRGID`, `CUSTID` "
                        . " from $dbname.import_mas";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                $jmlrec=$ketemu;
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $nfile0=$row['NAMA_BRG'];
                        $nfile1=$row['TGLJUAL'];
                        $nfile2=$row['FAKTURID'];
                        $nfile3=$row['CUSTNAME'];
                        $nfile4=$row['QTY'];
                        $nfile5=$row['HNA'];
                        $nfile6=$row['NILAI'];
                        $nfile7=$row['CABANGID'];
                        $nfile8=$row['BRGID'];
                        $nfile9=$row['CUSTID'];
                        
                        //$ptanggal=date('d M Y', strtotime($ptanggal));
                        $pgrdtotal=(double)$pgrdtotal+(double)$nfile6;
                        
                        //$pharga=number_format($pharga,0,",",",");
                        //$ptotal=number_format((double)$ptotal,0,",",",");
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$nfile0</td>";
                        echo "<td nowrap>$nfile1</td>";
                        echo "<td nowrap>$nfile2</td>";
                        echo "<td nowrap>$nfile3</td>";
                        echo "<td nowrap align='right'>$nfile4</td>";
                        echo "<td nowrap align='right'>$nfile5</td>";
                        echo "<td nowrap align='right'>$nfile6</td>";
                        echo "<td nowrap>$nfile7</td>";
                        echo "<td nowrap>$nfile8</td>";
                        echo "<td nowrap>$nfile9</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                    
                    $pgrdtotal=number_format($pgrdtotal,0,",",",");
                    echo "<tr>";
                    echo "<td nowrap colspan='7' align='center'><b>Grand Total : </b></td>";
                    echo "<td nowrap align='right'><b>$pgrdtotal</b></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
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
