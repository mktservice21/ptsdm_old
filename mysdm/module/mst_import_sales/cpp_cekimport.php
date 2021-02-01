<?php
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
session_start();
$puser=$_SESSION['IDCARD'];
if (empty($puser)) {
    echo "ANDA HARUS LOGIN ULANG....!!!";
    exit;
}
/*
include 'PHPExcel/IOFactory.php';

$objReader = PHPExcel_IOFactory::createReader('csv');

// If the files uses a delimiter other than a comma (e.g. a tab), then tell the reader
$objReader->setDelimiter("\t");
// If the files uses an encoding other than UTF-8 or ASCII, then tell the reader
$objReader->setInputEncoding('UTF-16LE');

$target_dir ="../../fileupload/201912/CPM/laporantransaksifakturpenjualandanretur/";
$objPHPExcel = $objReader->load($target_dir.'data_jkt2.csv');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save($target_dir.'MyExcelFile.xls');


exit;
*/




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
    $target_dir .=$pname_foder_dist."/".$pnmfolder."/";
    
    //ubah juga di prosesdata_
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    $dbname = "MKT";
    
    
    $plogit_akses=$_SESSION['PROSESLOGKONEK_IT'];//true or false || status awal true
    $plogit_akses=true;
    if ($plogit_akses==true) {
        include "../../config/koneksimysqli_it.php";
    }
    
    
    
    /* memanggil file DBF untuk kita Buka */
    
    $pnamatext_datajkt1="";
    $pnamatext_datajkt2="";
    $pnamatext_datasby="";
    if (is_dir($target_dir)){
        
        if ($dh = opendir($target_dir)){
            
            while (($pfilerar = readdir($dh)) !== false){
                if (!empty($pfilerar) && $pfilerar!="." && $pfilerar!="..") {
                    $path = pathinfo($target_dir.$pfilerar);
                    $ext = $path['extension'];
                    if ($ext=="csv") {
                        if (!empty($pfilerar)) {
                            $pnamatxt= TRIM(strtoupper(substr(TRIM($pfilerar),0,9)));
                            
                            if (strtoupper($pnamatxt)=="DATA_JKT1") $pnamatext_datajkt1=$pfilerar;
                            if (strtoupper($pnamatxt)=="DATA_JKT2") $pnamatext_datajkt2=$pfilerar;
                            if (strtoupper($pnamatxt)=="DATA_SBY." OR strtoupper($pnamatxt)=="DATA_SBY") $pnamatext_datasby=$pfilerar;
                            
                        }
                    }
                }
            }
            
            closedir($dh);
            
        }
        
    }
    
    $pjmljkt1=0;
    $pjmljkt2=0;
    $pjmlsby=0;
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.cpp_import_jkt1");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE cpp_import_jkt1 : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.cpp_import_jkt1");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE cpp_import_jkt1 : $erropesan"; exit; }
    }
    //END IT
    
    if (!empty($pnamatext_datajkt1)) {    
        $no=0;
		
        $fileopen = fopen($target_dir.$pnamatext_datajkt1, "r");
        while (($column = fgetcsv($fileopen, 10000, ",")) !== FALSE) {
            
                
            if ((double)$no==0) { $no++; continue; }

            $pfile0=TRIM($column[0]);//A format jadi text
            $pfile1=TRIM($column[1]);
            //$pfile2=TRIM($column[2]);//kolom C dihapus
            $pfile2=TRIM($column[3]);//D format jadi text
            $pfile3=TRIM($column[4]);//E format jadi text
            $pfile4=TRIM($column[5]);
            
            
            $mcarialamat_kode=explode('\r\",', $column[5]);
            $pfile4=$mcarialamat_kode[0];
            if (isset($mcarialamat_kode[1]) AND !isset($column[10])) {
                $pfile5=$mcarialamat_kode[1];
                
                if (!empty($pfile5)) $pfile5 = TRIM(str_replace('"', "", $pfile5));
                
                $pfile6=TRIM($column[6]);//H format jadi text
                
                $pfile7=""; //KODE SEKTOR
                $pfile8=TRIM($column[7]);
                
                $pfile9=TRIM($column[8]);
                $pfile10=TRIM($column[9]);
                
            }else{
                
                $pfile5=TRIM($column[6]);
                $mcarialamat_kode=explode('\",', $column[5]);
                $pfile4=$mcarialamat_kode[0];
                
                if (isset($mcarialamat_kode[1]) AND !isset($column[10])) {
                    
                    $pfile5=$mcarialamat_kode[1];

                    if (!empty($pfile5)) $pfile5 = TRIM(str_replace('"', "", $pfile5));

                    $pfile6=TRIM($column[6]);//H format jadi text

                    $pfile7=""; //KODE SEKTOR
                    $pfile8=TRIM($column[7]);

                    $pfile9=TRIM($column[8]);
                    $pfile10=TRIM($column[9]);
                    
                }else{
                    
                    $pfile5=TRIM($column[6]);
                    $pfile6=TRIM($column[7]);//H format jadi text

                    $pfile7=""; //KODE SEKTOR
                    $pfile8=TRIM($column[8]);

                    $pfile9=TRIM($column[9]);
                    $pfile10=TRIM($column[10]);
                    
                }
                
                
            }

            

            
            if (strtoupper($pfile8)=="END USER" OR strtoupper($pfile8)=="KARYAWAN") $pfile7="01";
            if (strtoupper($pfile8)=="KLINIK") $pfile7="02";
            if (strtoupper($pfile8)=="DOKTER") $pfile7="03";
            if (strtoupper($pfile8)=="BIDAN") $pfile7="04";
            if (strtoupper($pfile8)=="END USER-KS") $pfile7="05";
            if (strtoupper($pfile8)=="CORPORATE") $pfile7="06";
            
            if (!empty($pfile2)) { $pfile2 = str_replace("'", "", $pfile2); $pfile2=ltrim($pfile2, '0'); }//$pfile2=(DOUBLE)$pfile2;
            
            
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            if (!empty($pfile4)) $pfile4 = TRIM(str_replace('\r\n', " ", $pfile4));

            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            if (!empty($pfile5)) { $pfile5 = str_replace("'", "", $pfile5); $pfile5=ltrim($pfile5, '0'); }//$pfile5=(DOUBLE)$pfile5;
            
            
            if (!empty($pfile6)) $pfile6 = str_replace("'", " ", $pfile6);
            
            
            if (!empty($pfile1)) $pfile1 = str_replace("/", "-", $pfile1);
            if (!empty($pfile1)) $pfile1=date('Y-m-d', strtotime($pfile1));
            
            

            if (!empty($pfile9)) $pfile9 = str_replace("'", "", $pfile9);
            if (!empty($pfile9)) $pfile9 = str_replace(" ", "", $pfile9);
            if (!empty($pfile9)) $pfile9 = str_replace("*", "", $pfile9);
            
            if (!empty($pfile10)) $pfile10 = str_replace("'", "", $pfile10);
            if (!empty($pfile10)) $pfile10 = str_replace(" ", "", $pfile10);
            if (!empty($pfile10)) $pfile10 = str_replace("*", "", $pfile10);
            
            if (!empty($pfile9)) $pfile9=str_replace(",","", $pfile9);
            if (!empty($pfile10)) $pfile10=str_replace(",","", $pfile10);
            
            

            
            $query = "INSERT INTO $dbname.cpp_import_jkt1 (`NO TRANSAKSI`, `TGL TRANSAKSI`, `KODE CUSTOMER`, `NAMA CUSTOMER`, "
                    . " `ALAMAT`, `KODE BARANG`, `NAMA BARANG`, `KODE_SEKTOR`, `SEKTOR`, `QTY BARANG`, `HNA`)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10')";
					
            //echo "$query<br/>"; exit;
			
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { fclose($open); mysqli_close($cnmy); echo "$query <br/> Error INSERT DATA JKT 1 : $erropesan"; exit; }
            
            
            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { fclose($open); mysqli_close($cnit); echo "$query <br/> IT... Error INSERT DATA JKT 1 : $erropesan"; exit; }
            }
            //END IT
            
            
            $no++;
        }
        fclose($fileopen);
    }
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.cpp_import_jkt2");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE cpp_import_jkt2 : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.cpp_import_jkt2");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE cpp_import_jkt2 : $erropesan"; exit; }
    }
    //END IT
    
    if (!empty($pnamatext_datajkt2)) {    
        $no=0;
        $fileopen = fopen($target_dir.$pnamatext_datajkt2, "r");
        while (($column = fgetcsv($fileopen, 10000, ",")) !== FALSE) {
            
            //$column=addslashes($column);   
            if ((double)$no==0) { $no++; continue; }

            $pfile0=TRIM($column[0]);//A format jadi text
            $pfile1=TRIM($column[1]);
            //$pfile2=TRIM($column[2]);//kolom C dihapus
            $pfile2=TRIM($column[3]);//D format jadi text
            $pfile3=TRIM($column[4]);//E format jadi text
            $pfile4=TRIM($column[5]);
            
            $mcarialamat_kode=explode('\r\",', $column[5]);
            $pfile4=$mcarialamat_kode[0];
            if (isset($mcarialamat_kode[1]) AND !isset($column[10])) {
                $pfile5=$mcarialamat_kode[1];
                
                if (!empty($pfile5)) $pfile5 = TRIM(str_replace('"', "", $pfile5));
                
                $pfile6=TRIM($column[6]);//H format jadi text
                
                $pfile7=""; //KODE SEKTOR
                $pfile8=TRIM($column[7]);
                
                $pfile9=TRIM($column[8]);
                $pfile10=TRIM($column[9]);
                
            }else{
                
                $pfile5=TRIM($column[6]);
                $mcarialamat_kode=explode('\",', $column[5]);
                $pfile4=$mcarialamat_kode[0];
                
                if (isset($mcarialamat_kode[1]) AND !isset($column[10])) {
                    
                    $pfile5=$mcarialamat_kode[1];

                    if (!empty($pfile5)) $pfile5 = TRIM(str_replace('"', "", $pfile5));

                    $pfile6=TRIM($column[6]);//H format jadi text

                    $pfile7=""; //KODE SEKTOR
                    $pfile8=TRIM($column[7]);

                    $pfile9=TRIM($column[8]);
                    $pfile10=TRIM($column[9]);
                    
                }else{
                    
                    $pfile5=TRIM($column[6]);
                    $pfile6=TRIM($column[7]);//H format jadi text

                    $pfile7=""; //KODE SEKTOR
                    $pfile8=TRIM($column[8]);

                    $pfile9=TRIM($column[9]);
                    $pfile10=TRIM($column[10]);
                    
                }
                
            }
            
            if (strtoupper($pfile8)=="END USER" OR strtoupper($pfile8)=="KARYAWAN") $pfile7="01";
            if (strtoupper($pfile8)=="KLINIK") $pfile7="02";
            if (strtoupper($pfile8)=="DOKTER") $pfile7="03";
            if (strtoupper($pfile8)=="BIDAN") $pfile7="04";
            if (strtoupper($pfile8)=="END USER-KS") $pfile7="05";
            if (strtoupper($pfile8)=="CORPORATE") $pfile7="06";
            
            
            if (!empty($pfile2)) { $pfile2 = str_replace("'", "", $pfile2); $pfile2=ltrim($pfile2, '0'); }//$pfile2=(DOUBLE)$pfile2;
            
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            if (!empty($pfile4)) $pfile4 = TRIM(str_replace('\r\n', " ", $pfile4));
            
            
            if (!empty($pfile5)) { $pfile5 = str_replace("'", "", $pfile5); $pfile5=ltrim($pfile5, '0'); }//$pfile5=(DOUBLE)$pfile5;
            if (!empty($pfile6)) $pfile6 = str_replace("'", " ", $pfile6);
            
            
            if (!empty($pfile1)) $pfile1 = str_replace("/", "-", $pfile1);
            if (!empty($pfile1)) $pfile1=date('Y-m-d', strtotime($pfile1));
            
            

            if (!empty($pfile9)) $pfile9 = str_replace("'", "", $pfile9);
            if (!empty($pfile9)) $pfile9 = str_replace(" ", "", $pfile9);
            if (!empty($pfile9)) $pfile9 = str_replace("*", "", $pfile9);
            
            if (!empty($pfile10)) $pfile10 = str_replace("'", "", $pfile10);
            if (!empty($pfile10)) $pfile10 = str_replace(" ", "", $pfile10);
            if (!empty($pfile10)) $pfile10 = str_replace("*", "", $pfile10);
            
            if (!empty($pfile9)) $pfile9=str_replace(",","", $pfile9);
            if (!empty($pfile10)) $pfile10=str_replace(",","", $pfile10);
            
            
            
            
            
            $query = "INSERT INTO $dbname.cpp_import_jkt2 (`NO TRANSAKSI`, `TGL TRANSAKSI`, `KODE CUSTOMER`, `NAMA CUSTOMER`, "
                    . " `ALAMAT`, `KODE BARANG`, `NAMA BARANG`, `KODE_SEKTOR`, `SEKTOR`, `QTY BARANG`, `HNA`)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10')";
            
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { fclose($open); mysqli_close($cnmy); echo "Error INSERT DATA JKT 2 : $erropesan"; exit; }
            

            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { fclose($open); mysqli_close($cnit); echo "IT... Error INSERT DATA JKT 2 : $erropesan"; exit; }
            }
            //END IT
            
            $no++;
        }
        fclose($fileopen);
    }
    
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.cpp_import_sby");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE cpp_import_sby : $erropesan"; exit; }
    
    //IT
    if ($plogit_akses==true) {
        mysqli_query($cnit, "DELETE FROM $dbname.cpp_import_sby");
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { mysqli_close($cnit); echo "IT... Error DELETE cpp_import_sby : $erropesan"; exit; }
    }
    //END IT
    
    if (!empty($pnamatext_datasby)) { 
        $no=0;
        $fileopen = fopen($target_dir.$pnamatext_datasby, "r");
        while (($column = fgetcsv($fileopen, 10000, ",")) !== FALSE) {
            
                
            if ((double)$no==0) { $no++; continue; }

            $pfile0=TRIM($column[0]);//A format jadi text
            $pfile1=TRIM($column[1]);
            //$pfile2=TRIM($column[2]);//kolom C dihapus
            $pfile2=TRIM($column[3]);//D format jadi text
            $pfile3=TRIM($column[4]);//E format jadi text
            $pfile4=TRIM($column[5]);
            
            $mcarialamat_kode=explode('\r\",', $column[5]);
            $pfile4=$mcarialamat_kode[0];
            if (isset($mcarialamat_kode[1]) AND !isset($column[10])) {
                $pfile5=$mcarialamat_kode[1];
                
                if (!empty($pfile5)) $pfile5 = TRIM(str_replace('"', "", $pfile5));
                
                $pfile6=TRIM($column[6]);//H format jadi text
                
                $pfile7=""; //KODE SEKTOR
                $pfile8=TRIM($column[7]);
                
                $pfile9=TRIM($column[8]);
                $pfile10=TRIM($column[9]);
                
            }else{
                
                $pfile5=TRIM($column[6]);
                $mcarialamat_kode=explode('\",', $column[5]);
                $pfile4=$mcarialamat_kode[0];
                
                if (isset($mcarialamat_kode[1]) AND !isset($column[10])) {
                    
                    $pfile5=$mcarialamat_kode[1];

                    if (!empty($pfile5)) $pfile5 = TRIM(str_replace('"', "", $pfile5));

                    $pfile6=TRIM($column[6]);//H format jadi text

                    $pfile7=""; //KODE SEKTOR
                    $pfile8=TRIM($column[7]);

                    $pfile9=TRIM($column[8]);
                    $pfile10=TRIM($column[9]);
                    
                }else{
                    
                    $pfile5=TRIM($column[6]);
                    $pfile6=TRIM($column[7]);//H format jadi text

                    $pfile7=""; //KODE SEKTOR
                    $pfile8=TRIM($column[8]);

                    $pfile9=TRIM($column[9]);
                    $pfile10=TRIM($column[10]);
                    
                }
            }
            
            if (strtoupper($pfile8)=="END USER" OR strtoupper($pfile8)=="KARYAWAN") $pfile7="01";
            if (strtoupper($pfile8)=="KLINIK") $pfile7="02";
            if (strtoupper($pfile8)=="DOKTER") $pfile7="03";
            if (strtoupper($pfile8)=="BIDAN") $pfile7="04";
            if (strtoupper($pfile8)=="END USER-KS" OR strtoupper($pfile8)=="END USER - KS") $pfile7="05";
            if (strtoupper($pfile8)=="CORPORATE") $pfile7="06";
            
            if (!empty($pfile2)) { $pfile2 = str_replace("'", "", $pfile2); $pfile2=ltrim($pfile2, '0'); }//$pfile2=(DOUBLE)$pfile2;
            
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            if (!empty($pfile4)) $pfile4 = TRIM(str_replace('\r\n', " ", $pfile4));
            
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            if (!empty($pfile5)) { $pfile5 = str_replace("'", "", $pfile5); $pfile5=ltrim($pfile5, '0'); }//$pfile5=(DOUBLE)$pfile5;
            if (!empty($pfile6)) $pfile6 = str_replace("'", " ", $pfile6);
            
            
            if (!empty($pfile1)) $pfile1 = str_replace("/", "-", $pfile1);
            if (!empty($pfile1)) $pfile1=date('Y-m-d', strtotime($pfile1));
            
            

            if (!empty($pfile9)) $pfile9 = str_replace("'", "", $pfile9);
            if (!empty($pfile9)) $pfile9 = str_replace(" ", "", $pfile9);
            if (!empty($pfile9)) $pfile9 = str_replace("*", "", $pfile9);
            
            if (!empty($pfile10)) $pfile10 = str_replace("'", "", $pfile10);
            if (!empty($pfile10)) $pfile10 = str_replace(" ", "", $pfile10);
            if (!empty($pfile10)) $pfile10 = str_replace("*", "", $pfile10);
            
            if (!empty($pfile9)) $pfile9=str_replace(",","", $pfile9);
            if (!empty($pfile10)) $pfile10=str_replace(",","", $pfile10);
            
            

            
            $query = "INSERT INTO $dbname.cpp_import_sby (`NO TRANSAKSI`, `TGL TRANSAKSI`, `KODE CUSTOMER`, `NAMA CUSTOMER`, "
                    . " `ALAMAT`, `KODE BARANG`, `NAMA BARANG`, `KODE_SEKTOR`, `SEKTOR`, `QTY BARANG`, `HNA`)values"
                    . " ('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile9', '$pfile10')";
            
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { fclose($open); mysqli_close($cnmy); echo "Error INSERT DATA SBY : $erropesan"; exit; }

            //IT
            if ($plogit_akses==true) {
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { fclose($open); mysqli_close($cnit); echo "IT... Error INSERT DATA SBY : $erropesan"; exit; }
            }
            //END IT
            
            $no++;
        }
        fclose($fileopen);
    }
    
    
    
    
    $query = "select distinct HNA from $dbname.cpp_import_jkt1 WHERE IFNULL(`HNA`,'0')= 0";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        mysqli_query($cnmy, "DELETE FROM $dbname.cpp_import_jkt1");
        echo "ERROR SIMPAN... MASIH ADA HNA KOSONG DI cpp_import_jkt1.<br/>";
        mysqli_close($cnmy); exit;
    }
    
    
    $query = "select * from $dbname.cpp_import_jkt1 WHERE IFNULL(`TGL TRANSAKSI`,'')= '' OR IFNULL(`TGL TRANSAKSI`,'0000-00-00')= '0000-00-00' OR IFNULL(`TGL TRANSAKSI`,'1970-01-01')= '1970-01-01' OR IFNULL(`TGL TRANSAKSI`,'01-Jan-70')= '01-Jan-70'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        mysqli_query($cnmy, "DELETE FROM $dbname.cpp_import_jkt1");
        mysqli_close($cnmy);
        echo "ERROR SIMPAN... ADA Tanggal KOSONG. pada cpp_import_jkt1";
        exit;
    }
    
    
    
    $query = "select DISTINCT `TGL TRANSAKSI` as tglfaktur from $dbname.cpp_import_jkt1 WHERE DATE_FORMAT(`TGL TRANSAKSI`,'%Y%m')<>'$pbulan'";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_= mysqli_num_rows($tampil_);
    if ($ketemu_>0) {
        $pmyperiode =  date("F Y", strtotime($ptgl));
        
        echo "<div style='color:red;'>";
        echo "Periode Pilih : $pmyperiode, ADA TANGGAL YANG BERBEDA DENGAN PERIODE YANG DIPILIH. pada cpp_import_jkt1<br/>";
        
        $myno=1;
        while ($nr= mysqli_fetch_array($tampil_)) {
            $ntgl=$nr['tglfaktur'];
            echo "$myno. $ntgl<br/>";
            $myno++;
        }
        
        echo "</div>";
    }
    
    
    $query = "select distinct HNA from $dbname.cpp_import_jkt2 WHERE IFNULL(`HNA`,'0')= 0";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        mysqli_query($cnmy, "DELETE FROM $dbname.cpp_import_jkt2");
        echo "ERROR SIMPAN... MASIH ADA HNA KOSONG DI cpp_import_jkt2.<br/>";
        mysqli_close($cnmy); exit;
    }
    
    
    $query = "select * from $dbname.cpp_import_jkt2 WHERE IFNULL(`TGL TRANSAKSI`,'')= '' OR IFNULL(`TGL TRANSAKSI`,'0000-00-00')= '0000-00-00' OR IFNULL(`TGL TRANSAKSI`,'1970-01-01')= '1970-01-01' OR IFNULL(`TGL TRANSAKSI`,'01-Jan-70')= '01-Jan-70'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        mysqli_query($cnmy, "DELETE FROM $dbname.cpp_import_jkt2");
        mysqli_close($cnmy);
        echo "ERROR SIMPAN... ADA Tanggal KOSONG. pada cpp_import_jkt2";
        exit;
    }
    
    
    
    $query = "select DISTINCT `TGL TRANSAKSI` as tglfaktur from $dbname.cpp_import_jkt2 WHERE DATE_FORMAT(`TGL TRANSAKSI`,'%Y%m')<>'$pbulan'";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_= mysqli_num_rows($tampil_);
    if ($ketemu_>0) {
        $pmyperiode =  date("F Y", strtotime($ptgl));
        
        echo "<div style='color:red;'>";
        echo "Periode Pilih : $pmyperiode, ADA TANGGAL YANG BERBEDA DENGAN PERIODE YANG DIPILIH. pada cpp_import_jkt2<br/>";
        
        $myno=1;
        while ($nr= mysqli_fetch_array($tampil_)) {
            $ntgl=$nr['tglfaktur'];
            echo "$myno. $ntgl<br/>";
            $myno++;
        }
        
        echo "</div>";
    }
    
    
    
    
    $query = "select distinct HNA from $dbname.cpp_import_sby WHERE IFNULL(`HNA`,'0')= 0";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        mysqli_query($cnmy, "DELETE FROM $dbname.cpp_import_sby");
        echo "ERROR SIMPAN... MASIH ADA HNA KOSONG DI cpp_import_sby.<br/>";
        mysqli_close($cnmy); exit;
    }
    
    
    $query = "select * from $dbname.cpp_import_sby WHERE IFNULL(`TGL TRANSAKSI`,'')= '' OR IFNULL(`TGL TRANSAKSI`,'0000-00-00')= '0000-00-00' OR IFNULL(`TGL TRANSAKSI`,'1970-01-01')= '1970-01-01' OR IFNULL(`TGL TRANSAKSI`,'01-Jan-70')= '01-Jan-70'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        mysqli_query($cnmy, "DELETE FROM $dbname.cpp_import_sby");
        mysqli_close($cnmy);
        echo "ERROR SIMPAN... ADA Tanggal KOSONG. pada cpp_import_sby";
        exit;
    }
    
    
    
    $query = "select DISTINCT `TGL TRANSAKSI` as tglfaktur from $dbname.cpp_import_sby WHERE DATE_FORMAT(`TGL TRANSAKSI`,'%Y%m')<>'$pbulan'";
    $tampil_= mysqli_query($cnmy, $query);
    $ketemu_= mysqli_num_rows($tampil_);
    if ($ketemu_>0) {
        $pmyperiode =  date("F Y", strtotime($ptgl));
        
        echo "<div style='color:red;'>";
        echo "Periode Pilih : $pmyperiode, ADA TANGGAL YANG BERBEDA DENGAN PERIODE YANG DIPILIH. pada cpp_import_sby<br/>";
        
        $myno=1;
        while ($nr= mysqli_fetch_array($tampil_)) {
            $ntgl=$nr['tglfaktur'];
            echo "$myno. $ntgl<br/>";
            $myno++;
        }
        
        echo "</div>";
    }
    
    
    
    
    
    $query = "select * from $dbname.cpp_import_jkt1";
    $tmapilprod= mysqli_query($cnmy, $query);
    $pjmljkt1 = mysqli_num_rows($tmapilprod);
    
    $query = "select * from $dbname.cpp_import_jkt2";
    $tmapilcust= mysqli_query($cnmy, $query);
    $pjmljkt2 = mysqli_num_rows($tmapilcust);
    
    $query = "select * from $dbname.cpp_import_sby";
    $tmapiljual= mysqli_query($cnmy, $query);
    $pjmlsby = mysqli_num_rows($tmapiljual);
    
?>

<div class='x_content' style="overflow-x:auto; max-height: 400px;">
    
    <table id='dtablepilproscpm1' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='10px'>No</th>
                <th align="center" nowrap>No Transaksi</th>
                <th align="center" nowrap>Tgl Transaksi</th>
                <th align="center" nowrap>Kode Customer</th>
                <th align="center" nowrap>Nama Customer</th>
                <th align="center" nowrap>Alamat</th>
                <th align="center" nowrap>Kode Barang</th>
                <th align="center" nowrap>Nama Barang</th>
                <th align="center" nowrap>Kode Sektor</th>
                <th align="center" nowrap>Sektor</th>
                <th align="center" nowrap>Qty Barang</th>
                <th align="center" nowrap>HNA</th>
                <th align="center" nowrap>Total</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $pgrdtotal=0;
                $no=1;
                $query = "select `NO TRANSAKSI` as notransaksi, `TGL TRANSAKSI` as tgltransaksi, "
                        . " `KODE CUSTOMER` as kdcustomer, `NAMA CUSTOMER` as nmcustomer, "
                        . " `ALAMAT` as alamat, `KODE BARANG` as kdbrg, `NAMA BARANG` as nmbrg, `KODE_SEKTOR` as kdsektor, "
                        . " `SEKTOR` as sektor, `QTY BARANG` as qtybrg, `HNA` as hna "
                        . " from $dbname.cpp_import_jkt1";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pnotransaksi=$row['notransaksi'];
                        $ptgltransaksi=$row['tgltransaksi'];
                        $pkdcust=$row['kdcustomer'];
                        $pnmcust=$row['nmcustomer'];
                        $palamat=$row['alamat'];
                        $pkdbrg=$row['kdbrg'];
                        $pnmbrg=$row['nmbrg'];
                        $pkdsektor=$row['kdsektor'];
                        $psektor=$row['sektor'];
                        $pqty=$row['qtybrg'];
                        $phna=$row['hna'];
                        
                        $ntotal=(double)$pqty*(double)$phna;
                        
                        $pgrdtotal=(double)$pgrdtotal+(double)$ntotal;
                        
                        /*
                        $ptgltransaksi=date('d M Y', strtotime($ptgltransaksi));
                        
                        $pqty=number_format((double)$pqty,0,",",",");
                        $phna=number_format((double)$phna,0,",",",");
                        */
                        
                        $ntotal=number_format((double)$ntotal,0,",",",");
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pnotransaksi</td>";
                        echo "<td nowrap>$ptgltransaksi</td>";
                        echo "<td nowrap>$pkdcust</td>";
                        echo "<td nowrap>$pnmcust</td>";
                        echo "<td nowrap>$palamat</td>";
                        echo "<td nowrap>$pkdbrg</td>";
                        echo "<td nowrap>$pnmbrg</td>";
                        echo "<td nowrap>$pkdsektor</td>";
                        echo "<td nowrap>$psektor</td>";
                        echo "<td nowrap align='right'>$pqty</td>";
                        echo "<td nowrap align='right'>$phna</td>";
                        echo "<td nowrap align='right'>$ntotal</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                    
                    $pgrdtotal=number_format($pgrdtotal,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap colspan='12' align='center'><b>Grand Total : </b></td>";
                    echo "<td nowrap align='right'><b>$pgrdtotal</b></td>";
                    echo "</tr>";
                     
                }
            ?>
        </tbody>

    </table>

</div>
<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;

<div class='x_content' style="overflow-x:auto; max-height: 400px;">
    
    <table id='dtablepilproscpm2' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='10px'>No</th>
                <th align="center" nowrap>No Transaksi</th>
                <th align="center" nowrap>Tgl Transaksi</th>
                <th align="center" nowrap>Kode Customer</th>
                <th align="center" nowrap>Nama Customer</th>
                <th align="center" nowrap>Alamat</th>
                <th align="center" nowrap>Kode Barang</th>
                <th align="center" nowrap>Nama Barang</th>
                <th align="center" nowrap>Kode Sektor</th>
                <th align="center" nowrap>Sektor</th>
                <th align="center" nowrap>Qty Barang</th>
                <th align="center" nowrap>HNA</th>
                <th align="center" nowrap>Total</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $pgrdtotal=0;
                $no=1;
                $query = "select `NO TRANSAKSI` as notransaksi, `TGL TRANSAKSI` as tgltransaksi, "
                        . " `KODE CUSTOMER` as kdcustomer, `NAMA CUSTOMER` as nmcustomer, "
                        . " `ALAMAT` as alamat, `KODE BARANG` as kdbrg, `NAMA BARANG` as nmbrg, `KODE_SEKTOR` as kdsektor, "
                        . " `SEKTOR` as sektor, `QTY BARANG` as qtybrg, `HNA` as hna "
                        . " from $dbname.cpp_import_jkt2";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pnotransaksi=$row['notransaksi'];
                        $ptgltransaksi=$row['tgltransaksi'];
                        $pkdcust=$row['kdcustomer'];
                        $pnmcust=$row['nmcustomer'];
                        $palamat=$row['alamat'];
                        $pkdbrg=$row['kdbrg'];
                        $pnmbrg=$row['nmbrg'];
                        $pkdsektor=$row['kdsektor'];
                        $psektor=$row['sektor'];
                        $pqty=$row['qtybrg'];
                        $phna=$row['hna'];
                        
                        $ntotal=(double)$pqty*(double)$phna;
                        
                        $pgrdtotal=(double)$pgrdtotal+(double)$ntotal;
                        
                        /*
                        $ptgltransaksi=date('d M Y', strtotime($ptgltransaksi));
                        
                        $pqty=number_format((double)$pqty,0,",",",");
                        $phna=number_format((double)$phna,0,",",",");
                        */
                        
                        $ntotal=number_format((double)$ntotal,0,",",",");
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pnotransaksi</td>";
                        echo "<td nowrap>$ptgltransaksi</td>";
                        echo "<td nowrap>$pkdcust</td>";
                        echo "<td nowrap>$pnmcust</td>";
                        echo "<td nowrap>$palamat</td>";
                        echo "<td nowrap>$pkdbrg</td>";
                        echo "<td nowrap>$pnmbrg</td>";
                        echo "<td nowrap>$pkdsektor</td>";
                        echo "<td nowrap>$psektor</td>";
                        echo "<td nowrap align='right'>$pqty</td>";
                        echo "<td nowrap align='right'>$phna</td>";
                        echo "<td nowrap align='right'>$ntotal</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                    
                    $pgrdtotal=number_format($pgrdtotal,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap colspan='12' align='center'><b>Grand Total : </b></td>";
                    echo "<td nowrap align='right'><b>$pgrdtotal</b></td>";
                    echo "</tr>";
                     
                }
            ?>
        </tbody>

    </table>

</div>
<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;

<div class='x_content' style="overflow-x:auto; max-height: 400px;">
    
    <table id='dtablepilproscpm3' class='table table-striped table-bordered' width='100%'>
        <thead>
            <tr>
                <th width='10px'>No</th>
                <th align="center" nowrap>No Transaksi</th>
                <th align="center" nowrap>Tgl Transaksi</th>
                <th align="center" nowrap>Kode Customer</th>
                <th align="center" nowrap>Nama Customer</th>
                <th align="center" nowrap>Alamat</th>
                <th align="center" nowrap>Kode Barang</th>
                <th align="center" nowrap>Nama Barang</th>
                <th align="center" nowrap>Kode Sektor</th>
                <th align="center" nowrap>Sektor</th>
                <th align="center" nowrap>Qty Barang</th>
                <th align="center" nowrap>HNA</th>
                <th align="center" nowrap>Total</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
                $pgrdtotal=0;
                $no=1;
                $query = "select `NO TRANSAKSI` as notransaksi, `TGL TRANSAKSI` as tgltransaksi, "
                        . " `KODE CUSTOMER` as kdcustomer, `NAMA CUSTOMER` as nmcustomer, "
                        . " `ALAMAT` as alamat, `KODE BARANG` as kdbrg, `NAMA BARANG` as nmbrg, `KODE_SEKTOR` as kdsektor, "
                        . " `SEKTOR` as sektor, `QTY BARANG` as qtybrg, `HNA` as hna "
                        . " from $dbname.cpp_import_sby";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu>0) {
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pnotransaksi=$row['notransaksi'];
                        $ptgltransaksi=$row['tgltransaksi'];
                        $pkdcust=$row['kdcustomer'];
                        $pnmcust=$row['nmcustomer'];
                        $palamat=$row['alamat'];
                        $pkdbrg=$row['kdbrg'];
                        $pnmbrg=$row['nmbrg'];
                        $pkdsektor=$row['kdsektor'];
                        $psektor=$row['sektor'];
                        $pqty=$row['qtybrg'];
                        $phna=$row['hna'];
                        
                        $ntotal=(double)$pqty*(double)$phna;
                        
                        $pgrdtotal=(double)$pgrdtotal+(double)$ntotal;
                        
                        /*
                        $ptgltransaksi=date('d M Y', strtotime($ptgltransaksi));
                        
                        $pqty=number_format((double)$pqty,0,",",",");
                        $phna=number_format((double)$phna,0,",",",");
                        */
                        
                        $ntotal=number_format((double)$ntotal,0,",",",");
                        
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pnotransaksi</td>";
                        echo "<td nowrap>$ptgltransaksi</td>";
                        echo "<td nowrap>$pkdcust</td>";
                        echo "<td nowrap>$pnmcust</td>";
                        echo "<td nowrap>$palamat</td>";
                        echo "<td nowrap>$pkdbrg</td>";
                        echo "<td nowrap>$pnmbrg</td>";
                        echo "<td nowrap>$pkdsektor</td>";
                        echo "<td nowrap>$psektor</td>";
                        echo "<td nowrap align='right'>$pqty</td>";
                        echo "<td nowrap align='right'>$phna</td>";
                        echo "<td nowrap align='right'>$ntotal</td>";
                        echo "</tr>";
                        
                        $no++;
                    }
                    
                    $pgrdtotal=number_format($pgrdtotal,0,",",",");
                    
                    echo "<tr>";
                    echo "<td nowrap colspan='12' align='center'><b>Grand Total : </b></td>";
                    echo "<td nowrap align='right'><b>$pgrdtotal</b></td>";
                    echo "</tr>";
                     
                }
            ?>
        </tbody>

    </table>

</div>
<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;



<style>
    .divnone {
        display: none;
    }
    #dtablepilproscpm1 th, #dtablepilproscpm2 th, #dtablepilproscpm3 th {
        font-size: 13px;
    }
    #dtablepilproscpm1 td, #dtablepilproscpm2 td, #dtablepilproscpm3 td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);

    }
</style>


<?PHP
    
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    $finish = $time;
    $total_time = round(($finish - $start), 4);
    

    
    
    
    echo "<b>Jumlah Proses Import : </b><br/>";
    echo "1. Jml Data JKT 1 : $pjmljkt1<br/>";
    echo "2. Jml Data JKT 2 : $pjmljkt2<br/>";
    echo "3. Jml Data SBY : $pjmlsby<br/>";
    echo "Selesai dalam ".$total_time." detik";
    
    
    
    
    mysqli_close($cnmy);
    
    //IT
    if ($plogit_akses==true) {
        mysqli_close($cnit);
    }
    
    
    
?>