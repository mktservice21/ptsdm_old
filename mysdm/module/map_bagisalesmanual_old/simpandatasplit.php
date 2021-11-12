<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    session_start();

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    
    if ($pmodule=="mapbagislsmanual") {
        
        if ($pact=="datasimpansplit") {
            
            
            $pnmtblsales=$_POST['udistnmtblsales'];
            $pecustid=$_POST['aecustid'];
            $pecustnm=$_POST['uecustnm'];
            $piddist=$_POST['udistid'];
            $pidecab=$_POST['uecab'];
            $pfakturid=$_POST['ufakturid'];
            $pidbrg=$_POST['ubrgid'];
            $pidproduk=$_POST['uidprod'];
            $pbln=$_POST['ubln'];
            $ptgljual=$_POST['utgljual'];
            
            $pqtyfaktur=$_POST['uqtyfak'];
            $pqtysisa=$_POST['uqtysisa'];
            $psdhsplitqty=$_POST['uqtysdhsplt'];
            
            $pqtysplitinput=$_POST['uqtysplt'];
            $pidcabang=$_POST['uidcabang'];
            $pidarea=$_POST['uidarea'];
            $pidicust=$_POST['uidcust'];
            
            if (empty($pqtyfaktur)) $pqtyfaktur=0;
            if (empty($pqtysisa)) $pqtysisa=0;
            if (empty($psdhsplitqty)) $psdhsplitqty=0;
            if (empty($pqtysplitinput)) $pqtysplitinput=0;
            
            if (strlen($pbln)==7) $pbln=$pbln."-01";
            $pbulan = date('Y-m', strtotime($pbln));
            
            if ($pnmtblsales=="") { echo "Nama Tabel Sales Distributor Kosong..."; exit; }
            if ($piddist=="") { echo "Distributor Kosong..."; exit; }
            if ($pidecab=="") { echo "Ecabang ID Kosong..."; exit; }
            if ($pfakturid=="") { echo "Faktur ID Kosong..."; exit; }
            if ($pidbrg=="") { echo "Produk ID Kosong..."; exit; }
            if ($pbln=="") { echo "Bulan Kosong..."; exit; }
            if ($ptgljual=="") { echo "Tgl. Jual Kosong..."; exit; }
            
            if ($pidcabang=="") { echo "Cabang SDM Kosong..."; exit; }
            if ($pidarea=="") { echo "Area SDM Kosong..."; exit; }
            
            if ($pidicust=="") { echo "Customer SDM Kosong..."; exit; }
            
            if ($pqtysplitinput=="0") { echo "QTY Splitted Input Kosong..."; exit; }
            
            $pqtyfaktur=str_replace(",","", $pqtyfaktur);
            $pqtysisa=str_replace(",","", $pqtysisa);
            $psdhsplitqty=str_replace(",","", $psdhsplitqty);
            $pqtysplitinput=str_replace(",","", $pqtysplitinput);
            
            
            include "../../config/koneksimysqli_ms.php";
            include "../../config/common.php";
            
            
            //echo "$pecustid, $pecustnm - ($pnmtblsales) - $piddist, $pidecab, $pfakturid, $pidbrg, $pidproduk, $pbln, $ptgljual, $pqtyfaktur, $pqtyfaktur, $psdhsplitqty, $pqtysisa, ";
            //echo "$pqtysplitinput, $pidcabang, $pidarea, $pidicust"; exit;
            
            $query = "SELECT SUM(a.qbeli) qbeli "
                    . " FROM MKT.$pnmtblsales as a WHERE a.cabangid='$pidecab' AND "
                    . " a.tgljual='$ptgljual' AND a.fakturid='$pfakturid' AND a.brgid='$pidbrg'";
            $tampil= mysqli_query($cnms, $query);
            $row=mysqli_fetch_array($tampil);
            $pqtyfaktur=$row['qbeli'];
            
            $query2 = "select sum(b.qty) as qtysudh "
                    . " from MKT.msales0 as a LEFT "
                    . " JOIN MKT.msales1 as b on a.nomsales=b.nomsales WHERE "
                    . " a.distid='$piddist' and a.ecabangid='$pidecab' and a.fakturid='$pfakturid' AND left(a.tgl,7)='$pbulan' AND b.iprodid='$pidproduk'";
            $tampil2= mysqli_query($cnms, $query2);
            $row2=mysqli_fetch_array($tampil2);
            $pqtysdhsplit=$row2['qtysudh'];
            
            
            if (empty($pqtyfaktur)) $pqtyfaktur=0;
            if (empty($pqtysdhsplit)) $pqtysdhsplit=0;
            
            
            $qtysisa=(DOUBLE)$pqtyfaktur-(DOUBLE)$pqtysdhsplit;
            if ((DOUBLE)$qtysisa==0) {
                echo "Sisa 0..., tidak ada yang disimpan..."; mysqli_close($cnms); exit;
            }
            
            if ((DOUBLE)$pqtysplitinput > (DOUBLE)$qtysisa) {
                //$pqtysplitinput=$qtysisa;
            }
            
            //echo "Qty Faktur : $pqtyfaktur, Qty Sudah Split : $pqtysdhsplit, QTY Input : $pqtysplitinput"; exit;
            
            $query = "SELECT nomsales FROM MKT.setup0";
            $result = mysqli_query($cnms, $query);
            $num_results = mysqli_num_rows($result);
            $row = mysqli_fetch_array($result);
            $nomsales = plus1($row['nomsales'],10);
            $query = "UPDATE MKT.setup0 SET nomsales='$nomsales'";//echo"$query";
            $result = mysqli_query($cnms, $query);
            
            //echo "$nomsales"; mysqli_close($cnms); exit;
            if ($result) {
                $query = "INSERT INTO MKT.msales0 (nomsales,icabangid,tgl,distid,ecabangid,fakturid,icustid,ecustid,user1,src) 
                        VALUES ('$nomsales','$pidcabang','$ptgljual','$piddist','$pidecab','$pfakturid','$pidicust','$pecustid','$puserid','U')";
                $result2 = mysqli_query($cnms, $query);
                if ($result2) {
                    $query = "INSERT INTO MKT.msales1 (nomsales,iprodid,areaid,qty,src) 
                            VALUES ('$nomsales','$pidproduk','$pidarea','$pqtysplitinput','U')";
                    $result3 = mysqli_query($cnms, $query);
                }
            }
            
            mysqli_close($cnms);
            echo "berhasil";
            exit;
            
        }elseif ($pact=="hapusdatasplit") {
            include "../../config/koneksimysqli_ms.php";
            
            $pkodeinput=$_POST['ukode'];
            $pidprod=$_POST['uproduk'];
            $pfakturid=$_POST['ufakturid'];
            
            $query = "DELETE FROM MKT.msales1 WHERE nomsales IN (select nomsales FROM MKT.msales0 WHERE "
                    . "nomsales='$pkodeinput' AND fakturid='$pfakturid') AND nomsales='$pkodeinput' AND iprodid='$pidprod' LIMIT 1";
            $result = mysqli_query($cnms, $query);
            if ($result) {
                $query2 = "DELETE FROM MKT.msales0 WHERE nomsales='$pkodeinput' AND fakturid='$pfakturid' LIMIT 1";
                $result2 = mysqli_query($cnms, $query2);
            }
            mysqli_close($cnms);
            echo "berhasil";
            exit;
        }elseif ($pact=="hapusdatasplitprodfaktur") {
            include "../../config/koneksimysqli_ms.php";
            
            $pdistid=$_POST['udistid'];
            $pcabd=$_POST['ucabid'];
            $pbln=$_POST['ubln'];
            $ptgljual=$_POST['utgljual'];
            $pecustid=$_POST['uecustid'];
            $pbrgid=$_POST['ubrg'];
            $pidprod=$_POST['uproduk'];
            $pfakturid=$_POST['ufakturid'];
            
            $pnoidslsinput="";
            $query = "select distinct noMsales FROM MKT.msales0 where distid='$pdistid' and fakturid='$pfakturid' and tgl='$ptgljual' and ecabangid='$pcabd' and ecustid='$pecustid'";
            $tampil= mysqli_query($cnms, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                if (!empty($row['noMsales'])) $pnoidslsinput .="'".$row['noMsales']."',";
            }
            if (!empty($pnoidslsinput)) $pnoidslsinput="(".substr($pnoidslsinput, 0, -1).")";
            
            
            $pnoidslsinput2="";
            $query2 = "select distinct noMsales as noMsales FROM MKT.msales1 where noMsales IN $pnoidslsinput AND iprodid='$pidprod' and nomsales IN 
                (select distinct IFNULL(nomsales,'') FROM MKT.msales0 where distid='$pdistid' and fakturid='$pfakturid' and tgl='$ptgljual' and ecabangid='$pcabd' and ecustid='$pecustid')";
            $tampil2= mysqli_query($cnms, $query2);
            while ($row2= mysqli_fetch_array($tampil2)) {
                if (!empty($row2['noMsales'])) $pnoidslsinput2 .="'".$row2['noMsales']."',";
            }
            
            
            if (!empty($pnoidslsinput2)) {
                $pnoidslsinput2="(".substr($pnoidslsinput2, 0, -1).")";
                
                
                //echo "$pnoidslsinput2"; 
                //echo "$pdistid, $pcabd, $pbln, $ptgljual, brgid : $pbrgid - $pidprod, faktur : $pfakturid, ecustid : $pecustid"; 
                
                $query = "DELETE FROM MKT.msales1 WHERE nomsales IN $pnoidslsinput2 AND iprodid='$pidprod' AND nomsales IN "
                        . "(select distinct IFNULL(nomsales,'') FROM MKT.msales0 where distid='$pdistid' and fakturid='$pfakturid' and tgl='$ptgljual' and ecabangid='$pcabd' and ecustid='$pecustid')";
                //echo $query; exit;
                $result = mysqli_query($cnms, $query);
                if ($result) {
                    $query2 = "DELETE FROM MKT.msales0 WHERE noMsales IN $pnoidslsinput2 AND distid='$pdistid' and fakturid='$pfakturid' and tgl='$ptgljual' and ecabangid='$pcabd' and ecustid='$pecustid'";
                    $result2 = mysqli_query($cnms, $query2);
                }
                
                
                mysqli_close($cnms);
                echo "berhasil"; exit;
            }
            
            
            mysqli_close($cnms);
            echo "tidak ada data yang diproses....";
            exit;
        }elseif ($pact=="hapusdatasplitfaktur") {
            
            include "../../config/koneksimysqli_ms.php";
            
            
            $pdistid=$_POST['udistid'];
            $pcabd=$_POST['ucabid'];
            $pbln=$_POST['ubln'];
            $ptgljual=$_POST['utgljual'];
            $pecustid=$_POST['uecustid'];
            $pfakturid=$_POST['ufakturid'];
            
            //echo "$pdistid, $pcabd, $pbln, $ptgljual, faktur : $pfakturid, ecustid : $pecustid";  exit;
            
            if (!empty($pfakturid)) {
                $query = "DELETE FROM MKT.msales1 WHERE nomsales IN "
                        . " (select DISTINCT IFNULL(nomsales,'') from MKT.msales0 where distid='$pdistid' and "
                        . " fakturid='$pfakturid' and tgl='$ptgljual' and ecabangid='$pcabd' and ecustid='$pecustid')";
                $result = mysqli_query($cnms, $query);
                if ($result) {
                    $query2 = "DELETE FROM MKT.msales0 WHERE distid='$pdistid' and "
                            . " fakturid='$pfakturid' and tgl='$ptgljual' and ecabangid='$pcabd' and ecustid='$pecustid'";
                    $result2 = mysqli_query($cnms, $query2);
                }
                
                mysqli_close($cnms);
                echo "berhasil";
                exit;
            }
            
            mysqli_close($cnms);
            echo "tidak ada data yang diproses....";
            exit;
        }
        
    }
    
    echo "tidak ada data yang diproses...";
    
?>

