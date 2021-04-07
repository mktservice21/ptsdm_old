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
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.importsks");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE importsks : $erropesan"; exit; }
    
    
    include("../../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
    $objPHPExcel = PHPExcel_IOFactory::load($target_dir.$pnmfolder);
    unset($pinsert_data);//kosongkan array
    $padasheetkosong=false;
    $jmlrec=0;
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
        $totalrow = $worksheet->getHighestRow();
        $pnamasheet = $worksheet->getTitle();//get nama sheet
        //$jmlrec=0;
        
        
        $mynamesheet="";
        if (strtoupper(TRIM($pnamasheet))=="SBY") $mynamesheet="SBY";
        elseif (strtoupper(TRIM($pnamasheet))=="SMG") $mynamesheet="SMG";
        elseif (strtoupper(TRIM($pnamasheet))=="PWKT") $mynamesheet="PWK";
        elseif (strtoupper(TRIM($pnamasheet))=="MKSR") $mynamesheet="MKS";
        elseif (strtoupper(TRIM($pnamasheet))=="BALI") $mynamesheet="DPS";
        elseif (strtoupper(TRIM($pnamasheet))=="TEGAL") $mynamesheet="TGL";
		elseif (strtoupper(TRIM($pnamasheet))=="PALU") $mynamesheet="PLU";
        
        if (!empty($mynamesheet)) {
            $pbisasimpan=false;
            unset($pinsert_data);//kosongkan array
            for($row=2; $row<=$totalrow; $row++){
                $pfile0 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(0, $row)->getValue());
                $pfile1 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(1, $row)->getValue());//nopelanggan
                $pfile2 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(3, $row)->getValue());//nama faktur pelanggan
                $pfile3 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(8, $row)->getValue());//no faktur
                $pfile4 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(9, $row)->getValue());//tgl fakrur
                $pfile5 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(5, $row)->getValue());//TELP
                $pfile6 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(10, $row)->getValue());//kode barang / no barang
                $pfile7 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(11, $row)->getValue());//keterangan barang
                $pfile8 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(12, $row)->getValue());// qty unit kwantitas kuantitas
                $pfile9 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(13, $row)->getValue());//harga satuan
				
				$pfile12 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(6, $row)->getValue());//Nama Group Barang
				
				$pfile11 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(4, $row)->getValue());//alamat pelanggan
                
                if (!empty($pfile8)) {
                    $pfile8=str_replace("=","", $pfile8);
                    $pcek_data8= explode("*", $pfile8);
                    
                    if (isset($pcek_data8)) {
                        if (isset($pcek_data8[0]) AND isset($pcek_data8[1])) {
                            $palhasil8=(DOUBLE)$pcek_data8[0]*(DOUBLE)$pcek_data8[1];
                            if (isset($pcek_data8[2]) OR isset($pcek_data8[3])) {
                            }else{
                                $pfile8=$palhasil8;
                            }
                        }
                    }
                }
                
                
                if (!empty($pfile9)) {
                    $pfile9=str_replace("=","", $pfile9);
                    $pfile9=str_replace("(","", $pfile9);
                    $pfile9=str_replace(")","", $pfile9);
                    
                    $pcek_data9= explode("/", $pfile9);
                    
                    if (isset($pcek_data9)) {
                        if (isset($pcek_data9[0]) AND isset($pcek_data9[1])) {
                            $palhasil9=(DOUBLE)$pcek_data9[0]/(DOUBLE)$pcek_data9[1];
                            if (isset($pcek_data9[2]) OR isset($pcek_data9[3])) {
                            }else{
                                $pfile9=$palhasil9;
                            }
                        }
                    }
                }
                
                
                
                $pfile10=$mynamesheet;//cabang

                if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4) AND empty($pfile5)
                         AND empty($pfile6) AND empty($pfile7) AND empty($pfile8) AND empty($pfile9)) {
                    continue;
                }

                if (!empty($pfile2)) $pfile2 = str_replace("'", " ", $pfile2);
                if (!empty($pfile7)) $pfile7 = str_replace("'", " ", $pfile7);
                
                if (!empty($pfile8)) $pfile8=str_replace(".","", $pfile8);
                if (!empty($pfile8)) $pfile8=str_replace(",","", $pfile8);
                
                if (!empty($pfile9)) $pfile9=str_replace(".","", $pfile9);
                if (!empty($pfile9)) $pfile9=str_replace(",","", $pfile9);
                
                if (!empty($pfile4)) $pfile4 = str_replace("MEI", "May", strtoupper($pfile4));
                if (!empty($pfile4)) $pfile4 = str_replace("AGU", "Aug", strtoupper($pfile4));
                if (!empty($pfile4)) $pfile4 = str_replace("AGS", "Aug", strtoupper($pfile4));
                if (!empty($pfile4)) $pfile4 = str_replace("OKT", "Oct", strtoupper($pfile4));
                if (!empty($pfile4)) $pfile4 = str_replace("DES", "Dec", strtoupper($pfile4));
                
                if (!empty($pfile4)) $pfile4=date('Y-m-d', strtotime($pfile4));
                
				if (!empty($pfile12)) $pfile12 = str_replace("'", " ", $pfile12);
				
                $pinsert_data[] = "('$pfile3', '$pfile4', '$pfile1', '$pfile2', "
                        . " '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11', '$pfile12')";
                $pbisasimpan=true;
                /*
                $query = "INSERT INTO $dbname.importsks (`No. Faktur Faktur`, `Tgl Faktur Faktur`, `No. Pelanggan`, `Nama Pelanggan`, "
                        . " `KODE BARANG`, `Keterangan Barang`, `kuantitas`, `HNA`, `CABANG`, `Alamat 1 Pelanggan`)values"
                        . " ('$pfile3', '$pfile4', '$pfile1', '$pfile2', "
                        . " '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11')";

                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT importsks : $erropesan"; exit; }
                */
                
                $jmlrec++;
            }
            
        }else{
            $padasheetkosong=true;
        }
        
        
        if ($pbisasimpan==true) {
            $query_ins_pil = "INSERT INTO $dbname.importsks (`No. Faktur Faktur`, `Tgl Faktur Faktur`, `No. Pelanggan`, `Nama Pelanggan`, "
                    . " `KODE BARANG`, `Keterangan Barang`, `kuantitas`, `HNA`, `CABANG`, `Alamat 1 Pelanggan`, `HEADER`) values "
                    . " ".implode(', ', $pinsert_data);

            mysqli_query($cnmy, $query_ins_pil);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT importsks : $erropesan"; exit; }
        }
        
    }

    
    
    $query = "select * from $dbname.importsks WHERE IFNULL(`Tgl Faktur Faktur`,'')= '' OR IFNULL(`Tgl Faktur Faktur`,'0000-00-00')= '0000-00-00' OR IFNULL(`Tgl Faktur Faktur`,'1970-01-01')= '1970-01-01'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        //mysqli_query($cnmy, "DELETE FROM $dbname.importsks");
        //mysqli_close($cnmy);
        echo "ERROR SIMPAN... ADA TANGGAL KOSONG atau FORMAT TANGGAL SALAH DI EXCEL NYA....";
        //exit;
    }
    
    
    $query = "select DISTINCT `Tgl Faktur Faktur` as tglfaktur from $dbname.importsks WHERE DATE_FORMAT(`Tgl Faktur Faktur`,'%Y%m')<>'$pbulan'";
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
                <th align="center" nowrap>No Faktur</th>
                <th align="center" nowrap>Tgl Faktur</th>
                <th align="center" nowrap>No Pelanggan</th>
                <th align="center" nowrap>Nama Pelanggan</th>
                <th align="center" nowrap>KD BRG</th>
                <th align="center" nowrap>Ket BRG</th>
                <th align="center" nowrap>QTY</th>
                <th align="center" nowrap>HNA</th>
                <th align="center" nowrap>CAB</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $pgrdtotal=0;
                $no=1;
                $query = "select `No. Faktur Faktur` as nofaktur, `Tgl Faktur Faktur` as tglfaktur, `No. Pelanggan` as nopelanggan, "
                        . " `Nama Pelanggan` as nmpelanggan, "
                        . " `KODE BARANG` as kdbrg, `Keterangan Barang` as ketbrg, `kuantitas` as qty, `HNA` as hna, `CABANG` as cabang "
                        . " from $dbname.importsks";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pnofaktur=$row['nofaktur'];
                        $ptglfaktur=$row['tglfaktur'];
                        $pnopelanggan=$row['nopelanggan'];
                        $pnmpelanggan=$row['nmpelanggan'];
                        $pkdbrg=$row['kdbrg'];
                        $pketbrg=$row['ketbrg'];
                        $pqty=$row['qty'];
                        $phna=$row['hna'];
                        $pcab=$row['cabang'];
                        
                        $ptotal=(double)$pqty*(double)$phna;
                        $pgrdtotal=(double)$pgrdtotal+(double)$ptotal;
                        
                        //$ptanggal=date('d M Y', strtotime($ptanggal));
                        
                        //$pharga=number_format($pharga,0,",",",");
                        $ptotal=number_format((double)$ptotal,0,",",",");
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pnofaktur</td>";
                        echo "<td nowrap>$ptglfaktur</td>";
                        echo "<td nowrap>$pnopelanggan</td>";
                        echo "<td nowrap>$pnmpelanggan</td>";
                        echo "<td nowrap>$pkdbrg</td>";
                        echo "<td nowrap>$pketbrg</td>";
                        echo "<td nowrap align='right'>$pqty</td>";
                        echo "<td nowrap align='right'>$phna</td>";
                        echo "<td nowrap>$pcab</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                    
                    $pgrdtotal=number_format($pgrdtotal,0,",",",");
                    echo "<tr>";
                    echo "<td nowrap colspan='8' align='center'><b>Grand Total : </b></td>";
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
    
    if ($padasheetkosong==true) {
        echo "<span style='color:red;'>ADA SHEET KOSONG...!!!</span><br/>&nbsp;<br/>&nbsp;";
    }
    
    //echo "<b>Jumlah Proses Import : $jmlrec</b><br/>&nbsp;<br/>&nbsp;";
    echo "<b>TOTAL SALES : $pgrdtotal</b><br/>&nbsp;<br/>&nbsp;";
    
    $query = "select CABANG, SUM(IFNULL(`kuantitas`,0)*IFNULL(`HNA`,0)) as ttotal from $dbname.importsks Group BY 1";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        echo "<span style='color:blue;'>";
        while ($row= mysqli_fetch_array($tampil)) {
            $npcab=$row['CABANG'];
            $ntotal=$row['ttotal'];
            
            $ntotal=number_format($ntotal,0,",",",");
            echo "<b>TOTAL SALES $npcab : $ntotal</b><br/>&nbsp;<br/>&nbsp;";
        }
        echo "</span>";
    }
    
    
    
    
    mysqli_close($cnmy);
    
?>


<?PHP
echo "<br/><br/><br/>Import IT....<br/><br/><br/>";



    include "../../config/koneksimysqli_it.php";
    $cnmy=$cnit;
    $dbname = "MKT";
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.importsks");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE importsks : $erropesan"; exit; }
    
    
    $objPHPExcel = PHPExcel_IOFactory::load($target_dir.$pnmfolder);
    unset($pinsert_data);//kosongkan array
    $padasheetkosong=false;
    $jmlrec=0;
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
        $totalrow = $worksheet->getHighestRow();
        $pnamasheet = $worksheet->getTitle();//get nama sheet
        //$jmlrec=0;
        
        
        $mynamesheet="";
        if (strtoupper(TRIM($pnamasheet))=="SBY") $mynamesheet="SBY";
        elseif (strtoupper(TRIM($pnamasheet))=="SMG") $mynamesheet="SMG";
        elseif (strtoupper(TRIM($pnamasheet))=="PWKT") $mynamesheet="PWK";
        elseif (strtoupper(TRIM($pnamasheet))=="MKSR") $mynamesheet="MKS";
        elseif (strtoupper(TRIM($pnamasheet))=="BALI") $mynamesheet="DPS";
        elseif (strtoupper(TRIM($pnamasheet))=="TEGAL") $mynamesheet="TGL";
		elseif (strtoupper(TRIM($pnamasheet))=="PALU") $mynamesheet="PLU";
        
        if (!empty($mynamesheet)) {
            $pbisasimpan=false;
            unset($pinsert_data);//kosongkan array
            for($row=2; $row<=$totalrow; $row++){
                $pfile0 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(0, $row)->getValue());
                $pfile1 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(1, $row)->getValue());//nopelanggan
                $pfile2 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(3, $row)->getValue());//nama faktur pelanggan
                $pfile3 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(8, $row)->getValue());//no faktur
                $pfile4 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(9, $row)->getValue());//tgl fakrur
                $pfile5 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(5, $row)->getValue());//TELP
                $pfile6 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(10, $row)->getValue());//kode barang / no barang
                $pfile7 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(11, $row)->getValue());//keterangan barang
                $pfile8 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(12, $row)->getValue());// qty unit kwantitas kuantitas
                $pfile9 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(13, $row)->getValue());//harga satuan
				
				$pfile12 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(6, $row)->getValue());//Nama Group Barang
				
				$pfile11 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(4, $row)->getValue());//alamat pelanggan
                
                if (!empty($pfile8)) {
                    $pfile8=str_replace("=","", $pfile8);
                    $pcek_data8= explode("*", $pfile8);
                    
                    if (isset($pcek_data8)) {
                        if (isset($pcek_data8[0]) AND isset($pcek_data8[1])) {
                            $palhasil8=(DOUBLE)$pcek_data8[0]*(DOUBLE)$pcek_data8[1];
                            if (isset($pcek_data8[2]) OR isset($pcek_data8[3])) {
                            }else{
                                $pfile8=$palhasil8;
                            }
                        }
                    }
                }
                
                
                if (!empty($pfile9)) {
                    $pfile9=str_replace("=","", $pfile9);
                    $pfile9=str_replace("(","", $pfile9);
                    $pfile9=str_replace(")","", $pfile9);
                    
                    $pcek_data9= explode("/", $pfile9);
                    
                    if (isset($pcek_data9)) {
                        if (isset($pcek_data9[0]) AND isset($pcek_data9[1])) {
                            $palhasil9=(DOUBLE)$pcek_data9[0]/(DOUBLE)$pcek_data9[1];
                            if (isset($pcek_data9[2]) OR isset($pcek_data9[3])) {
                            }else{
                                $pfile9=$palhasil9;
                            }
                        }
                    }
                }
                
                
                
                $pfile10=$mynamesheet;//cabang

                if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4) AND empty($pfile5)
                         AND empty($pfile6) AND empty($pfile7) AND empty($pfile8) AND empty($pfile9)) {
                    continue;
                }

                if (!empty($pfile2)) $pfile2 = str_replace("'", " ", $pfile2);
                if (!empty($pfile7)) $pfile7 = str_replace("'", " ", $pfile7);
                
                if (!empty($pfile8)) $pfile8=str_replace(".","", $pfile8);
                if (!empty($pfile8)) $pfile8=str_replace(",","", $pfile8);
                
                if (!empty($pfile9)) $pfile9=str_replace(".","", $pfile9);
                if (!empty($pfile9)) $pfile9=str_replace(",","", $pfile9);
                
                if (!empty($pfile4)) $pfile4 = str_replace("MEI", "May", strtoupper($pfile4));
                if (!empty($pfile4)) $pfile4 = str_replace("AGU", "Aug", strtoupper($pfile4));
                if (!empty($pfile4)) $pfile4 = str_replace("AGS", "Aug", strtoupper($pfile4));
                if (!empty($pfile4)) $pfile4 = str_replace("OKT", "Oct", strtoupper($pfile4));
                if (!empty($pfile4)) $pfile4 = str_replace("DES", "Dec", strtoupper($pfile4));
                
                if (!empty($pfile4)) $pfile4=date('Y-m-d', strtotime($pfile4));
                
				if (!empty($pfile12)) $pfile12 = str_replace("'", " ", $pfile12);
				
                $pinsert_data[] = "('$pfile3', '$pfile4', '$pfile1', '$pfile2', "
                        . " '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11', '$pfile12')";
                $pbisasimpan=true;
                
                /*
                $query = "INSERT INTO $dbname.importsks (`No. Faktur Faktur`, `Tgl Faktur Faktur`, `No. Pelanggan`, `Nama Pelanggan`, "
                        . " `KODE BARANG`, `Keterangan Barang`, `kuantitas`, `HNA`, `CABANG`, `Alamat 1 Pelanggan`)values"
                        . " ('$pfile3', '$pfile4', '$pfile1', '$pfile2', "
                        . " '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10', '$pfile11')";

                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT importsks : $erropesan"; exit; }
                */
                
                $jmlrec++;
            }
            
        }else{
            $padasheetkosong=true;
        }
        
        
        if ($pbisasimpan == true) {
            $query_ins_pil = "INSERT INTO $dbname.importsks (`No. Faktur Faktur`, `Tgl Faktur Faktur`, `No. Pelanggan`, `Nama Pelanggan`, "
                    . " `KODE BARANG`, `Keterangan Barang`, `kuantitas`, `HNA`, `CABANG`, `Alamat 1 Pelanggan`, `HEADER`) values "
                    . " ".implode(', ', $pinsert_data);

            mysqli_query($cnmy, $query_ins_pil);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT importsks : $erropesan"; exit; }
        }
        
        
    }

    
    
    $query = "select * from $dbname.importsks WHERE IFNULL(`Tgl Faktur Faktur`,'')= '' OR IFNULL(`Tgl Faktur Faktur`,'0000-00-00')= '0000-00-00' OR IFNULL(`Tgl Faktur Faktur`,'1970-01-01')= '1970-01-01'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        //mysqli_query($cnmy, "DELETE FROM $dbname.importsks");
        //mysqli_close($cnmy);
        echo "ERROR SIMPAN... ADA TANGGAL KOSONG atau FORMAT TANGGAL SALAH DI EXCEL NYA....";
        //exit;
    }


    $query = "select CABANG, SUM(IFNULL(`kuantitas`,0)*IFNULL(`HNA`,0)) as ttotal from $dbname.importsks Group BY 1";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        echo "<span style='color:blue;'>";
        while ($row= mysqli_fetch_array($tampil)) {
            $npcab=$row['CABANG'];
            $ntotal=$row['ttotal'];
            
            $ntotal=number_format($ntotal,0,",",",");
            echo "<b>TOTAL SALES $npcab : $ntotal</b><br/>&nbsp;<br/>&nbsp;";
        }
        echo "</span>";
    }
    
    echo "Selesai dalam ".$total_time." detik";
    
?>