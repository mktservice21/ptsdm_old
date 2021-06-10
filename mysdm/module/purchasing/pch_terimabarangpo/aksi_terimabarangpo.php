<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
$puserid="";
$pidcard="";
$pidgroup="";
$pnamalengkaplog="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];
if (isset($_SESSION['NAMALENGKAP'])) $pnamalengkaplog=$_SESSION['NAMALENGKAP'];


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


if ($module=='pchterimabarangpo')
{
    if ($act=="hapus") {
        
        if (empty($puserid)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        include "../../../config/koneksimysqli.php";
        $pkodenya=$_GET['id'];
        $pkethapus=$_GET['kethapus'];
        
        if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);
        
        
        //echo "$pkodenya, $pkethapus, $pnamalengkaplog"; exit;
        
        $query = "UPDATE dbpurchasing.t_po_transaksi_terima SET stsnonaktif='Y', keterangan=CONCAT(IFNULL(keterangan,''), ' KET HAPUS : $pkethapus', ', USER HAPUS : $pnamalengkaplog') WHERE idterima='$pkodenya'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        $query = "UPDATE dbmaster.t_barang_terima SET stsnonaktif='Y', NOTES=CONCAT(IFNULL(NOTES,''), ' KET HAPUS : $pkethapus', ', USER HAPUS : $pnamalengkaplog') WHERE idterima='$pkodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        mysqli_close($cnmy);
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
    }elseif ($act=="update") {
        $pcardidlog=$_POST['e_idcardlogin'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        include "../../../config/koneksimysqli.php";
        
        $kodenya=$_POST['e_id'];
        $ptglinput=$_POST['e_tglberlaku'];
        
        $pttgl = str_replace('/', '-', $ptglinput);
        $ptanggal= date("Y-m-d", strtotime($pttgl));
        
        $pidcekdetail="";
        if (isset($_POST['chk_detail'])) $pidcekdetail=$_POST['chk_detail'];
        
        if (empty($pidcekdetail)) {
            echo "data belum dipilih...";
            mysqli_close($cnmy);
            exit;
        }
        
        $pizinsimpanterima=false;
        foreach ($pidcekdetail as $piddata) {
            if (!empty($piddata)) {
                $piddivisi=$_POST['txtiddiv'][$piddata];
                $pidbrg=$_POST['txtidbrg'][$piddata];
                $pjmlterima=$_POST['txtjmltrm'][$piddata];
                $pketterima=$_POST['txtkettrm'][$piddata];
                
                $pjmlterima=str_replace(",","", $pjmlterima);
                if (empty($pjmlterima)) $pjmlterima=0;
                if (!empty($pketterima)) $pketterima = str_replace("'", " ", $pketterima);
                
                //echo "$piddivisi, $pidbrg, $pjmlterima, $pketterima<br/>";
                
                $query = "UPDATE dbpurchasing.t_po_transaksi_terima SET tgl_terima='$ptanggal', jml_terima='$pjmlterima', ket_terima='$pketterima' WHERE idterima='$kodenya' AND idpo_d='$piddata' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan;  mysqli_close($cnmy); exit; }
                
                $query = "UPDATE dbmaster.t_barang_terima SET TANGGAL='$ptanggal' WHERE IDTERIMA='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan;  mysqli_close($cnmy); exit; }
                
                $query = "UPDATE dbmaster.t_barang_terima_d SET TANGGAL='$ptanggal', JUMLAH='$pjmlterima', KET_TERIMA='$pketterima' WHERE IDTERIMA='$kodenya' AND IDBARANG='$pidbrg' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan;  mysqli_close($cnmy); exit; }
                
                $pizinsimpanterima=true;
            }
        }
        
        mysqli_close($cnmy);
        if ($pizinsimpanterima==true) {
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        }else{
            echo "tidak ada data yang diupdate....";
        }
        exit;
    }elseif ($act=="input") {
        
        $pcardidlog=$_POST['e_idcardlogin'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        
        include "../../../config/koneksimysqli.php";
        
        
        $now=date("mdYhis");
        $tmp01 =" dbtemp.tmpsavetrmpo01_".$puserid."_$now ";
        $tmp02 =" dbtemp.tmpsavetrmpo02_".$puserid."_$now ";
        
        $query ="idterima varchar(20), igroup int(10), kdsupp varchar(15), divisi varchar(5), idpo_d INT(10) zerofill unsigned, "
                . " idbarang VARCHAR(20), tgl_terima Date, jml_terima INT(6), jml_bonus INT(6), ket_terima varchar(300), userid varchar(10), "
                . " ibonus varchar(1) DEFAULT 'N'";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan;  mysqli_close($cnmy); exit; }
        
    
        
        $kodenya=$_POST['e_id'];
        $ptglinput=$_POST['e_tglberlaku'];
        $pidpilpo=$_POST['e_idpo'];
        $pidsup=$_POST['e_idvendor'];
        
        $pttgl = str_replace('/', '-', $ptglinput);
        $ptanggal= date("Y-m-d", strtotime($pttgl));
        $pthnbln= date("ym", strtotime($pttgl));
        
        $ptahun = date('Y', strtotime($ptglinput));
        $ptahunbulan = date('Ym', strtotime($ptglinput));
        
        $pidcekdetail="";
        if (isset($_POST['chk_detail'])) $pidcekdetail=$_POST['chk_detail'];
        
        if (empty($pidcekdetail)) {
            echo "data belum dipilih...";
            mysqli_close($cnmy);
            exit;
        }
        
        unset($pinsert_data);//kosongkan array
        $jmlrec=0;
        $isimpan=false;
    
        foreach ($pidcekdetail as $piddata) {
            if (!empty($piddata)) {
                $piddivisi=$_POST['txtiddiv'][$piddata];
                $pidbrg=$_POST['txtidbrg'][$piddata];
                $pjmlterima=$_POST['txtjmltrm'][$piddata];
                $pketterima=$_POST['txtkettrm'][$piddata];
                $pjmlbonus=$_POST['txtjmlbonus'][$piddata];
                
                $pjmlterima=str_replace(",","", $pjmlterima);
                $pjmlbonus=str_replace(",","", $pjmlbonus);
                if (empty($pjmlterima)) $pjmlterima=0;
                if (empty($pjmlbonus)) $pjmlbonus=0;
                if (!empty($pketterima)) $pketterima = str_replace("'", " ", $pketterima);
                
                
                if ((INT)$pjmlterima<>0 OR !empty($pketterima) OR (DOUBLE)$pjmlbonus<>0) {
                    
                    $pinsert_data[] = "('$pidsup', '$piddivisi', '$piddata', '$pidbrg', '$ptanggal', '$pjmlterima', '$pketterima', '$pcardidlog', '$pjmlbonus')";
                    
                    $isimpan=true;
                }
                
            }
        }
        
        $pizinsimpanterima=false;
        
        if ($isimpan==true) {
            $query = "INSERT INTO $tmp01 (kdsupp, divisi, idpo_d, idbarang, tgl_terima, jml_terima, ket_terima, userid, jml_bonus) "
                    . " VALUES ".implode(', ', $pinsert_data);
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {
                echo "ERROR.... $erropesan";
                mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
                exit;
            }
            
            
            // BONUS
            $query = "select kdsupp, divisi, idpo_d, idbarang, tgl_terima, jml_bonus as jml_terima, 'BONUS' as ket_terima, userid, jml_bonus, 'Y' as ibonus "
                    . " FROM $tmp01 WHERE IFNULL(jml_bonus,0)<>0";
            $query = "create TEMPORARY table $tmp02 ($query)"; 
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {
                echo $erropesan;
                mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
                mysqli_close($cnmy);
                exit;
            }
        
            $query = "INSERT INTO $tmp01 (kdsupp, divisi, idpo_d, idbarang, tgl_terima, jml_terima, ket_terima, userid, jml_bonus, ibonus) "
                    . " select kdsupp, divisi, idpo_d, idbarang, tgl_terima, jml_terima, ket_terima, userid, jml_bonus, ibonus "
                    . " FROM $tmp02";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {
                echo $erropesan;
                mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
                mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
                mysqli_close($cnmy);
                exit;
            }
            mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
            
            // END BONUS
            
            
            $query = "select distinct kdsupp, divisi, ibonus FROM $tmp01";
            $query = "create TEMPORARY table $tmp02 ($query)"; 
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan;  mysqli_close($cnmy); exit; }
            
            
            $pidgroup="";
            $nawal=7; $urut=1;
            $purutan=1;
            if ($act=="input") {
                $query = "select IFNULL(MAX(igroup),0) as igroup FROM dbpurchasing.t_po_transaksi_terima";
                $tampilg= mysqli_query($cnmy, $query);
                $rowg= mysqli_fetch_array($tampilg);
                $pidgroup=$rowg['igroup'];
                if (empty($pidgroup)) $pidgroup=0;
                $pidgroup++;
                
                $sql=  mysqli_query($cnmy, "select IDTERIMA as NOURUT from dbmaster.t_setup_barang WHERE TAHUN='$ptahun'");
                $ketemu=  mysqli_num_rows($sql);
                
                if ($ketemu==0){
                    mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_barang (TAHUN, IDTERIMA)VALUES('$ptahun', '$purutan')");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {
                        echo $erropesan;
                        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
                        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
                        mysqli_close($cnmy);
                        exit;
                    }
                }else{
                    $o=  mysqli_fetch_array($sql);
                    if (!empty($o['NOURUT'])) {
                        $urut=$o['NOURUT']+1;
                    }
                }
                
                
                $query = "select distinct kdsupp, divisi, ibonus FROM $tmp02 order by kdsupp, divisi";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pnkdsup=$row['kdsupp'];
                    $pndiv=$row['divisi'];
                    $pnbonuspl=$row['ibonus'];
                    
                    $purutan=$urut;
                    $jml=  strlen($urut);
                    $awal=$nawal-$jml;
                    $pidterima=$ptahunbulan."-STB".str_repeat("0", $awal).$urut;
                    //echo "$purutan, $jml, $awal, $pidterima<br/>";

                    $query = "UPDATE $tmp01 SET idterima='$pidterima', igroup='$pidgroup' WHERE IFNULL(kdsupp,'')='$pnkdsup' AND IFNULL(divisi,'')='$pndiv' AND IFNULL(ibonus,'')='$pnbonuspl'";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan;  mysqli_close($cnmy); exit; }
                    
                    
                    mysqli_query($cnmy, "UPDATE dbmaster.t_setup_barang SET IDTERIMA='$purutan' WHERE TAHUN='$ptahun'");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
                        echo $erropesan;
                        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
                        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
                        mysqli_close($cnmy); 
                        exit; 
                    }
                    
                    $urut++;
                    $pizinsimpanterima=true;
                    
                }
                
                

            }
        
        }
        
        $query = "select * FROM $tmp01 WHERE IFNULL(idterima,'')='' OR IFNULL(igroup,'')='' OR IFNULL(igroup,'0')='0'";
        $tampilk= mysqli_query($cnmy, $query);
        $ketemuk= mysqli_num_rows($tampilk);
        if ((INT)$ketemuk>0) $pizinsimpanterima=false;
        
        if ($pizinsimpanterima==true) {
            
            
            $query = "INSERT INTO dbpurchasing.t_po_transaksi_terima "
                    . " (igroup, idpo_d, tgl_terima, jml_terima, ket_terima, userid, idterima, ibonus)"
                    . " SELECT igroup, idpo_d, tgl_terima, jml_terima, ket_terima, userid, idterima, ibonus FROM $tmp01";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
                echo $erropesan;
                mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
                mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
                mysqli_close($cnmy); 
                exit; 
            }
            
            $query = "INSERT INTO dbmaster.t_barang_terima "
                    . " (IDTERIMA, TANGGAL, KARYAWANID, DIVISIID, KDSUPP, USERID, VALIDATEID, VALIDATEDATE, NOTES, PILIHPO)"
                    . " SELECT DISTINCT idterima, tgl_terima, userid as karyawanid, divisi, kdsupp, userid, "
                    . " userid as validateid, NOW() as validatedate, CONCAT('ID PO - ', '$pidpilpo') as NOTES, 'Y' as PILIHPO "
                    . " FROM $tmp01";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
                echo $erropesan;
                $query = "UPDATE dbpurchasing.t_po_transaksi_terima as a JOIN $tmp01 as b on a.igroup=b.igroup AND "
                        . " a.idpo_d=b.idpo_d AND a.tgl_terima=b.tgl_terima AND a.userid=b.userid AND a.idterima=b.idterima SET "
                        . " a.stsnonaktif='Y'";
                mysqli_query($cnmy, $query);
                
                mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
                mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
                mysqli_close($cnmy); 
                exit; 
            }
            
            $query = "INSERT INTO dbmaster.t_barang_terima_d "
                    . " (IDTERIMA, IDBARANG, JUMLAH, TANGGAL, IDPO_D, KET_TERIMA) "
                    . " SELECT idterima, idbarang, jml_terima, tgl_terima, idpo_d, ket_terima FROM $tmp01";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) {
                
                echo $erropesan;
                
                $query = "UPDATE dbmaster.t_barang_terima as a JOIN $tmp01 as b on a.idterima=b.idterima AND "
                        . " a.TANGGAL=b.tgl_terima AND a.DIVISIID=b.divisi AND a.KARYAWANID=b.userid SET "
                        . " a.STSNONAKTIF='Y'";
                mysqli_query($cnmy, $query);
                
                $query = "UPDATE dbpurchasing.t_po_transaksi_terima as a JOIN $tmp01 as b on a.igroup=b.igroup AND "
                        . " a.idpo_d=b.idpo_d AND a.tgl_terima=b.tgl_terima AND a.userid=b.userid AND a.idterima=b.idterima SET "
                        . " a.stsnonaktif='Y'";
                mysqli_query($cnmy, $query);
                
                mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
                mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
                mysqli_close($cnmy); 
                exit; 
            }
            
        }
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01"); 
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); exit; }//echo $erropesan; 
        
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02"); 
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); exit; }//echo $erropesan; 
        
        mysqli_close($cnmy);
        if ($pizinsimpanterima==true) {
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        }else{
            echo "tidak ada data yang disimpan....";
        }
        
    }
}
?>
