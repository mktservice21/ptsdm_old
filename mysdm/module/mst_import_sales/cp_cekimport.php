<?php
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","1G");
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
    
    //ubah juga yang di prosesdata_
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.combieth");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE combieth : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.combieth");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE combieth : $erropesan"; exit; }
    }
    //END IT
    
    
    include("../../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
    $objPHPExcel = PHPExcel_IOFactory::load($target_dir.$pnmfolder);
    unset($pinsert_data);//kosongkan array
    $padasheetkosong=false;
    $jmlrec=0;
    
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
        $totalrow = $worksheet->getHighestRow();
        $jmlrec=0;
        
        for($row=2; $row<=$totalrow; $row++){
            
            $pidcab="CP";
            $pnmcab="CP";
            
            $pfile0 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(0, $row)->getValue());
            $pfile1 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(1, $row)->getValue());
            $pfile2 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(2, $row)->getValue());
            $pfile3 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(3, $row)->getValue());
            $pfile4 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(4, $row)->getValue());
            $pfile5 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(5, $row)->getValue());
            $pfile6 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(6, $row)->getValue());
            $pfile7 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(7, $row)->getValue());
            $pfile8 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(8, $row)->getValue());
            $pfile9 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(9, $row)->getValue());
            $pfile10 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(10, $row)->getValue());
            $pfile11 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(11, $row)->getValue());
            $pfile12 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(12, $row)->getValue());
            $pfile13 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(13, $row)->getValue());
            $pfile14 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(14, $row)->getValue());
            $pfile15 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(15, $row)->getValue());
            $pfile16 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(16, $row)->getValue());
            $pfile17 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(17, $row)->getValue());
            $pfile18 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(18, $row)->getValue());
            $pfile19 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(19, $row)->getValue());
            $pfile20 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(20, $row)->getValue());
            $pfile21 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(21, $row)->getValue());
            $pfile22 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(22, $row)->getValue());
            $pfile23 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(23, $row)->getValue());
            $pfile24 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(24, $row)->getValue());
            
            if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4) AND empty($pfile5)
                    AND empty($pfile6) AND empty($pfile7) AND empty($pfile8) AND empty($pfile9) AND empty($pfile10) AND empty($pfile11) 
                    AND empty($pfile12) AND empty($pfile13) AND empty($pfile14) AND empty($pfile15) AND empty($pfile16) AND empty($pfile17) 
                    AND empty($pfile18) AND empty($pfile19) AND empty($pfile20) AND empty($pfile21) AND empty($pfile22) AND empty($pfile23)
                    AND empty($pfile24)) {
                continue;
            }
            
            
            if (!empty($pfile19)) {			
                $excel_date = $pfile19; //here is that value 41621 or 41631
                $unix_date = ($excel_date - 25569) * 86400;
                $excel_date = 25569 + ($unix_date / 86400);
                $unix_date = ($excel_date - 25569) * 86400;
                $pfile19 = gmdate("Y-m-d", $unix_date);
            }
			
            /*
            $pfile19="";
            $data_tlg = $worksheet->getCellByColumnAndRow(19, $row);
            if(!strtotime($data_tlg)) {
                if(PHPExcel_Shared_Date::isDateTime($data_tlg)) {
                    $cellValue = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
                    $dateValue = PHPExcel_Shared_Date::ExcelToPHP($cellValue);                     
                    $pfile19     = date('Y-m-d',$dateValue);
                } else {                        
                    $pfile19     = date('Y-m-d',$pfile19);
                }
            }else{
                $pfile19 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(19, $row)->getValue());
                $pfile19=date('Y-m-d', strtotime($pfile19));
				
            }
            */


            //echo "$pfile19<br/>";
		
            
            
            //echo "KD BRG : $pfile2, NM BRG : $pfile3, KD CUST : $pfile17, NAMA : $pfile18, ALAMAT : $pfile23, KOTA : $pfile24, TGL. FAKTUR : $pfile19, NOFAKTUR : $pfile1, ID CAB : $pidcab, NAMA CAB : $pnmcab, QTY : $pfile5, SATUAN : $pfile4, HARGA : $pfile7,<br/>";
            
            $query_jualcp = "INSERT INTO $dbname.combieth (`kd barang`, `Nama Barang`, `Kd customer`, `Nama`, "
                    . " `Alamat`, `Kota`, `Tanggal Faktur`, `Nomor Faktur`, `ID Cabang`, `Nama Cabang`, `Qty Sales`, `Satuan`, `Harga satuan`)values"
                    . " ('$pfile2', '$pfile3', '$pfile17', '$pfile18', "
                    . " '$pfile23', '$pfile24', '$pfile19', '$pfile1', '$pidcab', '$pnmcab', '$pfile5', '$pfile4', '$pfile7')";
            
                      
            $pinsert_data[] = "('$pfile2', '$pfile3', '$pfile17', '$pfile18', "
                    . " '$pfile23', '$pfile24', '$pfile19', '$pfile1', '$pidcab', '$pnmcab', '$pfile5', '$pfile4', '$pfile7')";
            $pbisasimpan=true;
                        
                        
        }

    }
    
    
    if ($pbisasimpan==true) {
        
        $query_ins_pil = "INSERT INTO $dbname.combieth (`kd barang`, `Nama Barang`, `Kd customer`, `Nama`, "
                    . " `Alamat`, `Kota`, `Tanggal Faktur`, `Nomor Faktur`, `ID Cabang`, `Nama Cabang`, `Qty Sales`, `Satuan`, `Harga satuan`) values "
                . " ".implode(', ', $pinsert_data);

        mysqli_query($cnmy, $query_ins_pil);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT combieth : $erropesan"; exit; }
        
        $query_dlt = "delete from mkt.combieth WHERE IFNULL(`kd barang`,'')='' AND IFNULL(`Nama Barang`,'')='' AND IFNULL(`kd customer`,'')='' AND IFNULL(`Nama`,'')='' AND IFNULL(`Alamat`,'')='' AND IFNULL(`Tanggal Faktur`,'')='' AND IFNULL(`Nomor Faktur`,'')='' AND IFNULL(`Harga satuan`,'')=''";
        mysqli_query($cnmy, $query_d); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE combieth : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_ins_pil);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT combieth : $erropesan"; exit; }
            
            mysqli_query($cnmy, $query_dlt); $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE combieth : $erropesan"; exit; }
        }
        //END IT
        
            
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
    
    //IT
    if ($plogit_akses==true) {
        mysqli_close($cnit);
    }
    //END IT
    
?>
