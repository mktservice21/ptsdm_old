<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
$puserid="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}


$pidcard=$_SESSION['IDCARD'];
$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


if ($module=='gimickeluarbarang')
{

    
    if ($act=="hapus") {
        include "../../config/koneksimysqli.php";
        $pkodenya=$_GET['id'];
        
        $query = "UPDATE dbmaster.t_barang_keluar SET STSNONAKTIF='Y' WHERE IDKELUAR='$pkodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
    }elseif ($act=="isinoresi") {
        $pkodenya=$_POST['e_id'];
        $pnoresi=$_POST['e_noresi'];
        $ptgl=$_POST['e_tglberlaku'];
        
        if (!empty($pnoresi)) $pnoresi = str_replace("'", '', $pnoresi);
        $ptglresi = date('Y-m-d', strtotime($ptgl));
        //echo "$pkodenya, $pnoresi, $ptglresi"; exit;
        include "../../config/koneksimysqli.php";
        
        $query ="select IDKELUAR from dbmaster.t_barang_keluar_kirim WHERE IDKELUAR='$pkodenya'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu==0) {
            mysqli_query($cnmy, "INSERT INTO dbmaster.t_barang_keluar_kirim (IDKELUAR)VALUES('$pkodenya')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        if (empty($pnoresi)) {
            $query = "UPDATE dbmaster.t_barang_keluar_kirim SET TGLKIRIM=NULL, NORESI=NULL WHERE IDKELUAR='$pkodenya'";
        }else{
            $query = "UPDATE dbmaster.t_barang_keluar_kirim SET TGLKIRIM='$ptglresi', NORESI='$pnoresi' WHERE IDKELUAR='$pkodenya'";
        }
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
    }elseif ($act=="input" OR $act=="update") {
        
        include "../../config/koneksimysqli.php";
        
        $pkodeid=$_POST['e_id'];
        
        $ptgl=$_POST['e_tglberlaku'];
        $pgrpprod=$_POST['cb_divisi'];
        $pcabangid=$_POST['cb_cabang'];
        $pareaid=$_POST['cb_area'];
        $pnotes=$_POST['e_notes'];
        if (!empty($pnotes)) $pnotes = str_replace("'", '', $pnotes);
        
        $pkaryawanid=$_POST['cb_karyawan'];
        $puserinput=$_SESSION['IDCARD'];
        
        $ptglminta = date('Y-m-d', strtotime($ptgl));
        $ptahun = date('Y', strtotime($ptgl));
        $ptahunbulan = date('Ym', strtotime($ptgl));
        
        
        if ($act=="input") {
            $sql=  mysqli_query($cnmy, "select IDKELUAR as NOURUT from dbmaster.t_setup_barang WHERE TAHUN='$ptahun'");
            $ketemu=  mysqli_num_rows($sql);
            $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');

            $psavedataurut=false;
            $purutan=1;
            if ($ketemu==0){
                mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_barang (TAHUN, IDKELUAR)VALUES('$ptahun', '$purutan')");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }else{
                $o=  mysqli_fetch_array($sql);
                if (!empty($o['NOURUT'])) {
                    $urut=$o['NOURUT']+1;
                    $purutan=$urut;
                }
            }
			
            mysqli_query($cnmy, "UPDATE dbmaster.t_setup_barang SET IDKELUAR='$purutan' WHERE TAHUN='$ptahun'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $pkodeid=$ptahunbulan."-SKB".str_repeat("0", $awal).$urut;

        }
         
        if (empty($pkodeid)) {
            mysqli_close($cnmy);
            exit;
        }
            
        //echo "$pkodeid : $ptglminta, $pgrpprod, $pcabangid, $pnotes<br/>"; exit;
        
        unset($pinsert_data_detail);//kosongkan array
        $psimpandata=false;
        foreach ($_POST['chkbox_br'] as $pbarangid) {

            if (empty($pbarangid)) {
                continue;
            }
            
            $pjmlstock=$_POST['txt_njmstock'][$pbarangid];
            $pjml=$_POST['txt_njml'][$pbarangid];
            if (empty($pjmlstock)) $pjmlstock=0;
            if (empty($pjml)) $pjml=0;
            $pjmlstock=str_replace(",","", $pjmlstock);
            $pjml=str_replace(",","", $pjml);
            
            if ((DOUBLE)$pjml>(DOUBLE)$pjmlstock) {
                $pjml=$pjmlstock;
            }
            
            if ((DOUBLE)$pjml>0) {
                $pinsert_data_detail[] = "('$pkodeid', '$pbarangid', '$pjmlstock', '$pjml')";
                $psimpandata=true;
            }
            //echo "$pbarangid, $pjml, $pjmlstock<br/>";
            //echo "('$pkodeid', '$pbarangid', '$pjmlstock', '$pjml')<br/>";
        }
        
        
        if ($psimpandata == true) {
            
            mysqli_query($cnmy, "START TRANSACTION");
            
            if ($act=="input") {
                
                $query_mst="INSERT INTO dbmaster.t_barang_keluar (IDKELUAR, TANGGAL, KARYAWANID, DIVISIID, ICABANGID, ICABANGID_O, NOTES, USERID, AREAID, AREAID_O)VALUES"
                        . "('$pkodeid', '$ptglminta', '$pkaryawanid', '$pgrpprod', '$pcabangid', '$pcabangid', '$pnotes', '$puserinput', '$pareaid', '$pareaid')";
                
            }elseif ($act=="update") {
                $query_mst="UPDATE dbmaster.t_barang_keluar SET TANGGAL='$ptglminta', KARYAWANID='$pkaryawanid', "
                        . " DIVISIID='$pgrpprod', ICABANGID='$pcabangid', ICABANGID_O='$pcabangid', NOTES='$pnotes', "
                        . " USERID='$puserinput', AREAID='$pareaid', AREAID_O='$pareaid' WHERE IDKELUAR='$pkodeid'";
            }
            $pinsertmaster = mysqli_query($cnmy, $query_mst);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            if ($act=="input") {
                mysqli_query($cnmy, "DELETE FROM dbttd.t_barang_keluar_ttd WHERE IDKELUAR='$pkodeid' LIMIT 1");
                $query_kirim="INSERT INTO dbttd.t_barang_keluar_ttd (IDKELUAR)VALUES('$pkodeid')";
                $pinsertkirim = mysqli_query($cnmy, $query_kirim);
                $erropesan = mysqli_error($cnmy); 
                if (!empty($erropesan)) { 
                    mysqli_query($cnmy, "UPDATE dbmaster.t_barang_keluar SET STSNONAKTIF='Y', NOTES=CONCAT(NOTES,'_ ERROR SAVE') WHERE IDKELUAR='$pkodeid'");
                    echo $erropesan; 
                    mysqli_close($cnmy); exit;     
                }
                
                $query_kirim="INSERT INTO dbmaster.t_barang_keluar_kirim (IDKELUAR)VALUES('$pkodeid')";
                $pinsertkirim = mysqli_query($cnmy, $query_kirim);
                $erropesan = mysqli_error($cnmy); 
                if (!empty($erropesan)) { 
                    mysqli_query($cnmy, "UPDATE dbmaster.t_barang_keluar SET STSNONAKTIF='Y', NOTES=CONCAT(NOTES,'_ ERROR SAVE') WHERE IDKELUAR='$pkodeid'");
                    echo $erropesan; 
                    mysqli_close($cnmy); exit;     
                }
            }else{
                $pinsertkirim="1";
            }
            
            
            $query_delete_detail="DELETE FROM dbmaster.t_barang_keluar_d WHERE IDKELUAR='$pkodeid'";
            $pdeletedetail = mysqli_query($cnmy, $query_delete_detail);
            $erropesan = mysqli_error($cnmy); 
            if (!empty($erropesan)) { 
                mysqli_query($cnmy, "UPDATE dbmaster.t_barang_keluar SET STSNONAKTIF='Y', NOTES=CONCAT(NOTES,'_ ERROR SAVE') WHERE IDKELUAR='$pkodeid'");
                mysqli_query($cnmy, "DELETE FROM dbmaster.t_barang_keluar_kirim WHERE IDKELUAR='$pkodeid'");
                echo $erropesan; 
                mysqli_close($cnmy); exit;     
            }
             
            $query_detail="INSERT INTO dbmaster.t_barang_keluar_d (IDKELUAR, IDBARANG, STOCK, JUMLAH) VALUES ".implode(', ', $pinsert_data_detail);
            $pinsertdetail = mysqli_query($cnmy, $query_detail);
            $erropesan = mysqli_error($cnmy); 
            if (!empty($erropesan)) { 
                mysqli_query($cnmy, "UPDATE dbmaster.t_barang_keluar SET STSNONAKTIF='Y', NOTES=CONCAT(NOTES,'_ ERROR SAVE') WHERE IDKELUAR='$pkodeid'");
                //mysqli_query($cnmy, "DELETE FROM dbmaster.t_barang_keluar_kirim WHERE IDKELUAR='$pkodeid'");
                echo $erropesan; 
                mysqli_close($cnmy); exit;     
            }
            
            
            
            if ($query_detail AND $pinsertkirim AND $query_delete_detail AND $query_detail) {
                mysqli_query($cnmy, "COMMIT");
            } else {        
                mysqli_query($cnmy, "ROLLBACK");
            }
            
            
        }
        
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
    }
    
}
?>