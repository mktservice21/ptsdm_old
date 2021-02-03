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
    
    //echo $inputFileName; exit;
    
    //ubah juga di prosesdata_
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.import_dum");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE import_dum : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.import_dum");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE import_dum : $erropesan"; exit; }
    }
    //END IT
    

    
    unset($pinsert_data);//kosongkan array
    $jmlrec=0;
    $isimpan=false;
    $pnosht=1;
    
    
    require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
    require('spreadsheet-reader-master/SpreadsheetReader.php');
    
    $Reader = new SpreadsheetReader($target_dir.$pnmfolder);
    
    $jmlrec=0;
    foreach ($Reader as $Key => $Row) {
        // import data excel mulai baris ke-2 (karena ada header pada baris 1)
        if ($Key <= 1) continue;
        
        $pfile0=trim($Row[0]);
        $pfile1=trim($Row[1]);
        $pfile2=trim($Row[2]);
        $pfile3=trim($Row[3]);
        $pfile4=trim($Row[4]);
        $pfile5=trim($Row[5]);
        $pfile6=trim($Row[6]);
        $pfile7=trim($Row[7]);
        $pfile8=trim($Row[8]);
        
        if ( empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4) AND empty($pfile5)
                 AND empty($pfile6) AND empty($pfile7) AND empty($pfile8) ) {
            continue;
        }


        if (!empty($pfile5)) $pfile5 = str_replace("'", "", $pfile5);
        if (!empty($pfile5)) $pfile5 = str_replace(" ", "", $pfile5);
        if (!empty($pfile5)) $pfile5 = str_replace("*", "", $pfile5);
        if (!empty($pfile5)) $pfile5=str_replace(",","", $pfile5);
        
        if (!empty($pfile0)) $pfile0=date('Y-m-d', strtotime($pfile0));
        else $pfile0 = "0000-00-00";
                
        //echo "$pfile0, $pfile1, $pfile2, $pfile3, $pfile4, $pfile5, $pfile6, $pfile7, $pfile8<br/>";
        
        $pinsert_data[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile7', '$pfile6', "
                . " '$pfile5', '$pfile8')";
        $isimpan=true;

        $jmlrec++;
            
    }
    
    if ($isimpan==true) {
        $query_ins_pil = "INSERT INTO $dbname.import_dum (`Tanggal`, `No Faktur`, `Customer`, `Kode Barang`, `Nama Barang`, "
                . " `QTY`, `Kd Cust`) values "
                . " ".implode(', ', $pinsert_data);

        mysqli_query($cnmy, $query_ins_pil);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT import_dum : $erropesan"; exit; }
        
        //IT
        if ($plogit_akses==true) {
            mysqli_query($cnit, $query_ins_pil);
            $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error INSERT import_dum : $erropesan"; exit; }
        }
        //END IT
    }
    
    
    $query = "SELECT * FROM $dbname.import_dum WHERE DATE_FORMAT(Tanggal,'%Y%m')<>'$pbulan'";
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
                <th align="center" nowrap>Tanggal</th>
                <th align="center" nowrap>No Faktur</th>
                <th align="center" nowrap>Cust</th>
                <th align="center" nowrap>Nama Customer</th>
                <th align="center" nowrap>Kode</th>
                <th align="center" nowrap>Nama Barang</th>
                <th align="center" nowrap>Qty</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $pgrdtotal=0;
                $no=1;
                $query = "SELECT `Tanggal` as Tanggal, `No Faktur` as nofaktur, `Customer` as nmcust, `Kode Barang` as Kode, `Nama Barang` as nmbarang, "
                        . " `QTY` as kwantum, `Kd Cust` as cust "
                        . " from $dbname.import_dum";
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
                        $pkwantum=$row['kwantum'];
                        
                        $ptanggal=date('d M Y', strtotime($ptanggal));
                        
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$ptanggal</td>";
                        echo "<td nowrap>$pnofaktur</td>";
                        echo "<td nowrap>$pkdcust</td>";
                        echo "<td nowrap>$pnmcust</td>";
                        echo "<td nowrap>$pkode</td>";
                        echo "<td nowrap>$pnmbrg</td>";
                        echo "<td nowrap align='right'>$pkwantum</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                    
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
    //echo "<b>TOTAL SALES : $pgrdtotal</b><br/>&nbsp;<br/>&nbsp;";
    echo "Selesai dalam ".$total_time." detik";
    
    
    
    mysqli_close($cnmy);
    
    //IT
    if ($plogit_akses==true) {
        mysqli_close($cnit);
    }
    
?>
