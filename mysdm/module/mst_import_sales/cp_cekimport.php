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
    
    $inputFileName = $target_dir.$filename;
    
    //ubah juga diprosesdata_
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.combieth");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE combieth : $erropesan"; exit; }
    
    
    
    include("../../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
    $objPHPExcel = PHPExcel_IOFactory::load($target_dir.$pnmfolder);
    
    $jmlrec=0;
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
        $totalrow = $worksheet->getHighestRow();
        $pnamasheet = $worksheet->getTitle();//get nama sheet
        $jmlrec=0;
        
        for($row=4; $row<=$totalrow; $row++){
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
            
			/*
			$pfile6="";
            $data_tlg = $worksheet->getCellByColumnAndRow(6, $row);
            if(!strtotime($data_tlg)) {
                if(PHPExcel_Shared_Date::isDateTime($data_tlg)) {
                    $cellValue = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $dateValue = PHPExcel_Shared_Date::ExcelToPHP($cellValue);                     
                    $pfile6     = date('Y-m-d',$dateValue);                     
                } else {                        
                    $pfile6     = date('Y-m-d',$pfile8);
                }
            }else{
                $pfile6=date('Y-m-d', strtotime($pfile6));
            }
			*/
			
            if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4) AND empty($pfile5)
                     AND empty($pfile6) AND empty($pfile7) AND empty($pfile8) AND empty($pfile9) AND empty($pfile10) AND empty($pfile11) AND empty($pfile12)) {
                continue;
            }

            if (!empty($pfile1)) $pfile1 = str_replace("'", "", $pfile1);
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            if (!empty($pfile5)) $pfile5 = str_replace("'", " ", $pfile5);

            if (!empty($pfile6)) $pfile6=date('Y-m-d', strtotime($pfile6));


            if (!empty($pfile10)) $pfile10 = str_replace("*", "", $pfile10);
            if (!empty($pfile12)) $pfile12 = str_replace("*", "", $pfile12);

            if (!empty($pfile10)) $pfile10 = str_replace(" ", "", $pfile10);
            if (!empty($pfile12)) $pfile12 = str_replace(" ", "", $pfile12);

            if (!empty($pfile10)) $pfile10=str_replace(",","", $pfile10);
            if (!empty($pfile12)) $pfile12=str_replace(",","", $pfile12);
        
        
            //echo "$pfile6 <br/>";
			
            $query = "INSERT INTO $dbname.combieth (`kd barang`, `Nama Barang`, `Kd customer`, `Nama`, "
                    . " `Alamat`, `Kota`, `Tanggal Faktur`, `Nomor Faktur`, `ID Cabang`, `Nama Cabang`, `Qty Sales`, `Satuan`, `Harga satuan`)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11', '$pfile12')";

            //echo $query; mysqli_close($cnmy); exit;
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT combieth : $erropesan"; exit; }
            
            //echo "$jmlrec. : $pfile0, $pfile1, $pfile2, $pfile3, $pfile4, $pfile5, $pfile6, $pfile7, $pfile8, $pfile9, $pfile10, $pfile11, $pfile12<br/>";
            
            
            $jmlrec++;
        }
    }

    
    
    $query = "select * from $dbname.combieth WHERE IFNULL(`Tanggal Faktur`,'')= '' OR IFNULL(`Tanggal Faktur`,'0000-00-00')= '0000-00-00' OR IFNULL(`Tanggal Faktur`,'1970-01-01')= '1970-01-01'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        mysqli_close($cnmy);
        echo "ERROR SIMPAN... ADA TANGGAL KOSONG.";
        exit;
    }
    
    
    
    $query = "select DISTINCT `Tanggal Faktur` as tglfaktur from $dbname.combieth WHERE DATE_FORMAT(`Tanggal Faktur`,'%Y%m')<>'$pbulan'";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_= mysqli_num_rows($tampil_);
    if ($ketemu_>0) {
        $pmyperiode =  date("F Y", strtotime($ptgl));
        
        echo "<div style='color:red;'>";
        echo "Periode Pilih : $pmyperiode, ADA TANGGAL FAKTUR YANG BERBEDA DENGAN PERIODE YANG DIPILIH<br/>";
        
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
    
    <table id='dtablepilprosmps' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='10px'>No</th>
                <th align="center" nowrap>Kd BRG</th>
                <th align="center" nowrap>Nama BRG</th>
                <th align="center" nowrap>KD CUST</th>
                <th align="center" nowrap>Nama</th>
                <th align="center" nowrap>Alamat</th>
                <th align="center" nowrap>Kota</th>
                <th align="center" nowrap>Tgl Faktur</th>
                <th align="center" nowrap>No Faktur</th>
                <th align="center" nowrap>ID CAB</th>
                <th align="center" nowrap>NM CAB</th>
                <th align="center" nowrap>QTY</th>
                <th align="center" nowrap>Satuan</th>
                <th align="center" nowrap>Harga</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $pgrdtotal=0;
                $no=1;
                $query = "select `kd barang` as kdbrg, `Nama Barang` as nmbrg, `Kd customer` as kdcust, `Nama` as nama, "
                    . " `Alamat` as alamat, `Kota` as kota, `Tanggal Faktur` as tglfaktur, `Nomor Faktur` as nofaktur, "
                        . " `ID Cabang` as idcab, `Nama Cabang` as nmcab, `Qty Sales` as qty, `Satuan` as satuan, `Harga satuan` as hna "
                        . " from $dbname.combieth";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pkdbrg=$row['kdbrg'];
                        $pnmbrg=$row['nmbrg'];
                        $pkdcust=$row['kdcust'];
                        $pnama=$row['nama'];
                        $palamat=$row['alamat'];
                        $pkota=$row['kota'];
                        $ptglfaktur=$row['tglfaktur'];
                        $pnofaktur=$row['nofaktur'];
                        $pidcab=$row['idcab'];
                        $pnmcab=$row['nmcab'];
                        $pqty=$row['qty'];
                        $psatuan=$row['satuan'];
                        $phna=$row['hna'];
                        
                        $ptotal=(double)$pqty*(double)$phna;
                        $pgrdtotal=(double)$pgrdtotal+(double)$ptotal;
                        
                        //$ptanggal=date('d M Y', strtotime($ptanggal));
                        
                        //$pharga=number_format($pharga,0,",",",");
                        $ptotal=number_format((double)$ptotal,0,",",",");
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pkdbrg</td>";
                        echo "<td nowrap>$pnmbrg</td>";
                        echo "<td nowrap>$pkdcust</td>";
                        echo "<td nowrap>$pnama</td>";
                        echo "<td nowrap>$palamat</td>";
                        echo "<td nowrap>$pkota</td>";
                        echo "<td nowrap>$ptglfaktur</td>";
                        echo "<td nowrap>$pnofaktur</td>";
                        echo "<td nowrap>$pidcab</td>";
                        echo "<td nowrap>$pnmcab</td>";
                        echo "<td nowrap align='right'>$pqty</td>";
                        echo "<td nowrap>$psatuan</td>";
                        echo "<td nowrap align='right'>$phna</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                    
                    $pgrdtotal=number_format($pgrdtotal,0,",",",");
                    echo "<tr>";
                    echo "<td nowrap colspan='13' align='center'><b>Grand Total : </b></td>";
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
    
?>
