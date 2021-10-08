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
    
    include("../../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
    $objPHPExcel = PHPExcel_IOFactory::load($target_dir.$pnmfolder);
    
    $jmlrec=0;
    $isimpan=false;
    
    $pjumlah_sheet=1;
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
        $totalrow = $worksheet->getHighestRow();
        if ((INT)$pjumlah_sheet>1) {
            break;
        }else{
            $pjumlah_sheet++;
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


            if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4) AND empty($pfile5)
                     AND empty($pfile6) AND empty($pfile7) AND empty($pfile8) AND empty($pfile9) AND empty($pfile10) AND empty($pfile11) AND empty($pfile12) AND empty($pfile13) AND empty($pfile14)) {
                continue;
            }
                
                /*
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
                    $pfile1=date('Y-m-d', strtotime($pfile1));
                }
                */


                //type
                $mkode=trim(substr($pfile9,0,3));
                if ($mkode=="SDE" OR $mkode=="sde" OR ucwords($mkode)=="SDE") {
                    $pfile_tipe="Ethical";
                }else{
                    $pfile_tipe="OTC";
                }

                $pfile_nodpl="";//NODPL

                //untuk promo
                if ($pfile9=="01524") {
                    if (empty($pfile13)) $pfile13==0;
                    if (empty($pfile11)) $pfile11==0;
                    if ((DOUBLE)$pfile13<>0) {
                        $pfile13=(DOUBLE)$pfile13/3;
                        $pfile11=(DOUBLE)$pfile11*3;
                        $pfile_nodpl="promo 3in1 glikoderm lumineux";
                    }
                }

                $ptotal_=(DOUBLE)$pfile11*(DOUBLE)$pfile13;



                //echo "tgl : $pfile1, total : $ptotal_, kdbrg : $pfile3, qty : $pfile11, hrg : $pfile7, divisi : $pfile12<br/>";

                $query = "INSERT INTO $dbname.import_mulyaraya (cust, `nama customer`, Tanggal, `No.Faktur`, "
                        . " Kode, `nama barang`, satuan, kwantum, harga, total, type, nodpl)values"
                        . " ('$pfile4', '$pfile5', '$pfile1', '$pfile0', "
                        . " '$pfile9', '$pfile10', '$pfile12', '$pfile11', '$pfile13', '$ptotal_', '$pfile_tipe', '$pfile_nodpl')";

                //echo $query; mysqli_close($cnmy); exit;
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT import_mulyaraya : $erropesan"; exit; }

                $jmlrec++;

                //IT
                if ($plogit_akses==true) {
                    mysqli_query($cnit, $query);
                    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT import_mulyaraya : $erropesan"; exit; }
                }
                //END IT


            }
        
        }
        
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
