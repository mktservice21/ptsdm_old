<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    session_status();

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        //echo "ANDA HARUS LOGIN ULANG...";
        //exit;
    }
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    
    if ($pmodule=="mapbagislsmanual") {
        
        if ($pact=="datasimpansplit") {
            
            
            $pnmtblsales=$_POST['udistnmtblsales'];
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
            
            //if ($pidicust=="") { echo "Customer SDM Kosong..."; exit; }
            
            if ($pqtysplitinput=="0") { echo "QTY Splitted Input Kosong..."; exit; }
            
            $pqtyfaktur=str_replace(",","", $pqtyfaktur);
            $pqtysisa=str_replace(",","", $pqtysisa);
            $psdhsplitqty=str_replace(",","", $psdhsplitqty);
            $pqtysplitinput=str_replace(",","", $pqtysplitinput);
            
            
            include "../../config/koneksimysqli_ms.php";
            
            
            //echo "($pnmtblsales) - $piddist, $pidecab, $pfakturid, $pidbrg, $pidproduk, $pbln, $ptgljual, $pqtyfaktur, $pqtyfaktur, $psdhsplitqty, $pqtysisa, ";
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
            if ((DOUBLE)$qtysisa<=0) {
                echo "Sisa 0..., tidak ada yang disimpan..."; mysqli_close($cnms); exit;
            }
            
            if ((DOUBLE)$pqtysplitinput > (DOUBLE)$qtysisa) {
                $pqtysplitinput=$qtysisa;
            }
            
            //echo "Qty Faktur : $pqtyfaktur, Qty Sudah Split : $pqtysdhsplit, QTY Input : $pqtysplitinput"; exit;
            
            
            
            
            mysqli_close($cnms);
            echo "berhasil";
            exit;
            
        }
        
    }
    
    echo "tidak ada data yang disimpan...";
    
?>

