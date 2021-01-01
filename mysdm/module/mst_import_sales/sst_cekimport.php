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
    
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.importsst");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE importsst : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.importsst");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE importsst : $erropesan"; exit; }
    }
    //END IT
    
    
    include("../../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
    $objPHPExcel = PHPExcel_IOFactory::load($target_dir.$pnmfolder);
    
    $jmlrec=0;
    $isimpan=false;
    
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
        $totalrow = $worksheet->getHighestRow();

        
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
            //$pfile16 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(16, $row)->getValue());
			
			//error_reporting(0);
            $pfile17 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(17, $row)->getValue());
            //error_reporting(-1);
			//if (empty($pfile17)) $pfile17="0000-00-00";
			
			
            if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4) AND empty($pfile5)
                     AND empty($pfile6) AND empty($pfile7) AND empty($pfile8) AND empty($pfile9) 
                     AND empty($pfile10) AND empty($pfile11) AND empty($pfile12)
                     AND empty($pfile13) AND empty($pfile14) AND empty($pfile15) AND empty($pfile16) AND empty($pfile17)) {
                continue;
            }

            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            if (!empty($pfile5)) $pfile5 = str_replace("'", " ", $pfile5);
            
            $data_tlg = $worksheet->getCellByColumnAndRow(8, $row);
            if(!strtotime($data_tlg)) {
                if(PHPExcel_Shared_Date::isDateTime($data_tlg)) {
                    $cellValue = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    $dateValue = PHPExcel_Shared_Date::ExcelToPHP($cellValue);                     
                    $pfile8     = date('Y-m-d',$dateValue);                     
                } else {                        
                    $pfile8     = date('Y-m-d',$pfile8);
                }
            }else{
                $pfile8=date('Y-m-d', strtotime($pfile8));
            }
            
            
            
            /*
            if ($pfile16=="0000/00/00" OR $pfile16=="0000-00-00" OR $pfile16=="00/00/0000" OR $pfile16=="00-00-0000") {
                $pfile16="0000-00-00";
            }else{
            
                $data_tlg2 = $worksheet->getCellByColumnAndRow(16, $row);
                if(!strtotime($data_tlg2)) {
                    if(PHPExcel_Shared_Date::isDateTime($data_tlg2)) {
                        $cellValue = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                        $dateValue = PHPExcel_Shared_Date::ExcelToPHP($cellValue);                     
                        $pfile16     = date('Y-m-d',$dateValue);                     
                    } else {                        
                        $pfile16     = date('Y-m-d',$pfile16);
                    }
                }else{
                    $pfile16=date('Y-m-d', strtotime($pfile16));
                }
                
            }
            
            */

            if (!empty($pfile9)) $pfile9 = str_replace("'", "", $pfile9);
            if (!empty($pfile9)) $pfile9 = str_replace(" ", "", $pfile9);
            if (!empty($pfile9)) $pfile9 = str_replace("*", "", $pfile9);

            if (!empty($pfile10)) $pfile10 = str_replace("'", "", $pfile10);
            if (!empty($pfile10)) $pfile10 = str_replace(" ", "", $pfile10);
            if (!empty($pfile10)) $pfile10 = str_replace("*", "", $pfile10);

            if (!empty($pfile11)) $pfile11 = str_replace("'", "", $pfile11);
            if (!empty($pfile11)) $pfile11 = str_replace(" ", "", $pfile11);
            if (!empty($pfile11)) $pfile11 = str_replace("*", "", $pfile11);

            if (!empty($pfile12)) $pfile12 = str_replace("'", "", $pfile12);
            if (!empty($pfile12)) $pfile12 = str_replace(" ", "", $pfile12);
            if (!empty($pfile12)) $pfile12 = str_replace("*", "", $pfile12);

            if (!empty($pfile13)) $pfile13 = str_replace("'", "", $pfile13);
            if (!empty($pfile13)) $pfile13 = str_replace(" ", "", $pfile13);
            if (!empty($pfile13)) $pfile13 = str_replace("*", "", $pfile13);

            if (!empty($pfile14)) $pfile14 = str_replace("'", "", $pfile14);
            if (!empty($pfile14)) $pfile14 = str_replace(" ", "", $pfile14);
            if (!empty($pfile14)) $pfile14 = str_replace("*", "", $pfile14);



            if (!empty($pfile9)) $pfile9=str_replace(",","", $pfile9);
            if (!empty($pfile10)) $pfile10=str_replace(",","", $pfile10);
            if (!empty($pfile11)) $pfile11=str_replace(",","", $pfile11);
            if (!empty($pfile12)) $pfile12=str_replace(",","", $pfile12);
            if (!empty($pfile13)) $pfile13=str_replace(",","", $pfile13);
            if (!empty($pfile14)) $pfile14=str_replace(",","", $pfile14);
            
            
            $pinsert_sst[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', "
                    . " '$pfile11', '$pfile12', '$pfile13', '$pfile14', '$pfile15', '$pfile17')";//, '$pfile16'
            
            $isimpan=true;
            
            /*
            $query = "INSERT INTO $dbname.importsst (`Prinsipal`, `Cabang`, `Kode Produk`, `Nama Produk`, "
                    . " `Kode Pelanggan`, `Nama Pelanggan`, `Alamat`, `No Faktur`, `Tgl Dok`, `Unit`, `Harga`, "
                    . " `Bonus Faktur`, `Diskon Prinsipal`, `Diskon Cabang`, `Total HNA`, `BatchNo`, `ExpDate`, `asl_data`)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', "
                    . " '$pfile11', '$pfile12', '$pfile13', '$pfile14', '$pfile15', '$pfile16', '$pfile17')";
            
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT importsst : $erropesan"; exit; }
            */
            
            $jmlrec++;
        }
        
        
        
    }
    
    
    if ($isimpan==true) {
        
        $query_sst = "INSERT INTO $dbname.importsst (`Prinsipal`, `Cabang`, `Kode Produk`, `Nama Produk`, "
                . " `Kode Pelanggan`, `Nama Pelanggan`, `Alamat`, `No Faktur`, `Tgl Dok`, `Unit`, `Harga`, "
                . " `Bonus Faktur`, `Diskon Prinsipal`, `Diskon Cabang`, `Total HNA`, `BatchNo`, `asl_data`)values "
                . " ".implode(', ', $pinsert_sst);//, `ExpDate`
        mysqli_query($cnmy, $query_sst);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT importsst : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_sst);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT importsst : $erropesan"; exit; }
        }
        //END IT


        unset($pinsert_sst);//kosongkan array
    }
    
    
    mysqli_query($cnmy, "update $dbname.importsst set `asl_data`= 'HO' where IFNULL(`asl_data`,'')=''");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error UPDATE asl_data : $erropesan"; exit; }
    
    
    $query = "select * from $dbname.importsst WHERE IFNULL(`Tgl Dok`,'')= '' OR IFNULL(`Tgl Dok`,'0000-00-00')= '0000-00-00' OR IFNULL(`Tgl Dok`,'1970-01-01')= '1970-01-01'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        mysqli_query($cnmy, "DELETE FROM $dbname.importsst");
        mysqli_close($cnmy);
        echo "ERROR SIMPAN... ADA Tgl Dok KOSONG.";
        exit;
    }
    
    
    $query = "select DISTINCT `Tgl Dok` as tglfaktur from $dbname.importsst WHERE DATE_FORMAT(`Tgl Dok`,'%Y%m')<>'$pbulan'";
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
<!--
<div class='x_content' style="overflow-x:auto; max-height: 500px;">
    
    <table id='dtablepilprosbks' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='10px'>No</th>
                <th align="center" nowrap>Prinsipal</th>
                <th align="center" nowrap>Cabang</th>
                <th align="center" nowrap>Kode Produk</th>
                <th align="center" nowrap>Nama Produk</th>
                <th align="center" nowrap>Kode Pelanggan</th>
                <th align="center" nowrap>Nama Pelanggan</th>
                <th align="center" nowrap>Alamat</th>
                <th align="center" nowrap>No Faktur</th>
                <th align="center" nowrap>Tgl Dok</th>
                <th align="center" nowrap>Unit</th>
                <th align="center" nowrap>Harga</th>
                <th align="center" nowrap>Bonus Faktur</th>
                <th align="center" nowrap>Diskon Prinsipal</th>
                <th align="center" nowrap>Diskon Cabang</th>
                <th align="center" nowrap>Total HNA</th>
                <th align="center" nowrap>BatchNo</th>
                <th align="center" nowrap>ExpDate</th>
                <th align="center" nowrap>asl_data</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            /*
                $pgrdtotal=0;
                $no=1;
                $query = "select `Prinsipal` as prinsipal, `Cabang` cabang, `Kode Produk` kdprod, `Nama Produk` nmprod, "
                . " `Kode Pelanggan` kdpelanggan, `Nama Pelanggan` nmpelanggan, `Alamat` alamat, `No Faktur` nofaktur, `Tgl Dok` tgldok, `Unit` unit, `Harga` harga, "
                . " `Bonus Faktur` bonusfak, `Diskon Prinsipal` disprins, `Diskon Cabang` discab, `Total HNA` tothna, `BatchNo` batchno, `ExpDate` expdate, `asl_data` asl_data "
                        . " from $dbname.importsst";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                $jmlrec=$ketemu;
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $nfile0=$row['prinsipal'];
                        $nfile1=$row['cabang'];
                        $nfile2=$row['kdprod'];
                        $nfile3=$row['nmprod'];
                        $nfile4=$row['kdpelanggan'];
                        $nfile5=$row['nmpelanggan'];
                        $nfile6=$row['alamat'];
                        $nfile7=$row['nofaktur'];
                        $nfile8=$row['tgldok'];
                        $nfile9=$row['unit'];
                        $nfile10=$row['harga'];
                        $nfile11=$row['bonusfak'];
                        $nfile12=$row['disprins'];
                        $nfile13=$row['discab'];
                        $nfile14=$row['tothna'];
                        $nfile15=$row['batchno'];
                        $nfile16=$row['expdate'];
                        $nfile17=$row['asl_data'];
                        
                        //$ptanggal=date('d M Y', strtotime($ptanggal));
                        $pgrdtotal=(double)$pgrdtotal+(double)$nfile14;
                        
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
                        echo "<td nowrap align='right'>$nfile9</td>";
                        echo "<td nowrap align='right'>$nfile10</td>";
                        echo "<td nowrap align='right'>$nfile11</td>";
                        echo "<td nowrap align='right'>$nfile12</td>";
                        echo "<td nowrap align='right'>$nfile13</td>";
                        echo "<td nowrap align='right'>$nfile14</td>";
                        echo "<td nowrap>$nfile15</td>";
                        echo "<td nowrap>$nfile16</td>";
                        echo "<td nowrap>$nfile17</td>";
                        echo "<tr/>";
                        
                        $no++;
                    }
                    
                    $pgrdtotal=number_format($pgrdtotal,0,",",",");
                    echo "<tr>";
                    echo "<td nowrap colspan='15' align='center'><b>Grand Total : </b></td>";
                    echo "<td nowrap align='right'><b>$pgrdtotal</b></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<tr/>";
                     
                }
             * 
             */
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
-->

<?PHP
    
    $query = "select sum(`Unit`*`Harga`) as tvalue from $dbname.importsst";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $nr=mysqli_fetch_array($tampil);
        $pgrdtotal=number_format($nr['tvalue'],0,",",",");
    }
    
    
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
