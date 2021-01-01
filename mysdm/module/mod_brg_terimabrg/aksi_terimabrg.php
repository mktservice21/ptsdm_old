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


if ($module=='gimicterimabarang')
{

    
    if ($act=="hapus") {
        include "../../config/koneksimysqli.php";
        $pkodenya=$_GET['id'];
        
        $query = "UPDATE dbmaster.t_barang_terima SET STSNONAKTIF='Y' WHERE IDTERIMA='$pkodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
    }elseif ($act=="validate") {
        
        include "../../config/koneksimysqli.php";
        $pkodenya=$_GET['id'];
        $puserinput=$_SESSION['IDCARD'];
        
        $query = "UPDATE dbmaster.t_barang_terima SET VALIDATEID='$puserinput', VALIDATEDATE=NOW() WHERE IDTERIMA='$pkodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
    }elseif ($act=="input" OR $act=="update") {
        
        include "../../config/koneksimysqli.php";
        
        $pkodeid=$_POST['e_id'];
        
        $ptgl=$_POST['e_tglberlaku'];
        $pgrpprod=$_POST['cb_divisi'];
        $psupplierid=$_POST['cb_supplier'];
        $pnotes=$_POST['e_notes'];
        if (!empty($pnotes)) $pnotes = str_replace("'", '', $pnotes);
        
        $pkaryawanid=$_POST['cb_karyawan'];
        $puserinput=$_SESSION['IDCARD'];
        
        $ptglminta = date('Y-m-d', strtotime($ptgl));
        $ptahun = date('Y', strtotime($ptgl));
        $ptahunbulan = date('Ym', strtotime($ptgl));
        
        
        if ($act=="input") {
            $sql=  mysqli_query($cnmy, "select IDTERIMA as NOURUT from dbmaster.t_setup_barang WHERE TAHUN='$ptahun'");
            $ketemu=  mysqli_num_rows($sql);
            $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');

            $psavedataurut=false;
            $purutan=1;
            if ($ketemu==0){
                mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_barang (TAHUN, IDTERIMA)VALUES('$ptahun', '$purutan')");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }else{
                $o=  mysqli_fetch_array($sql);
                if (!empty($o['NOURUT'])) {
                    $urut=$o['NOURUT']+1;
                    $purutan=$urut;
                }
            }
            
            mysqli_query($cnmy, "UPDATE dbmaster.t_setup_barang SET IDTERIMA='$purutan' WHERE TAHUN='$ptahun'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $pkodeid=$ptahunbulan."-STB".str_repeat("0", $awal).$urut;

        }
         
        if (empty($pkodeid)) {
            mysqli_close($cnmy);
            exit;
        }
            
        //echo "$pkodeid : $ptglminta, $pgrpprod, $psupplierid, $pnotes<br/>"; exit;
        
        unset($pinsert_data_detail);//kosongkan array
        $psimpandata=false;
        foreach ($_POST['chkbox_br'] as $pbarangid) {

            if (empty($pbarangid)) {
                continue;
            }
            
            $ptglmm=$_POST['txt_ntgl'][$pbarangid];
            $pnoukti=$_POST['txt_nnobukti'][$pbarangid];
            
            if (!empty($ptglmm))
                $ptglmm = date('Y-m-d', strtotime($ptglmm));
            else
                $ptglmm = "0000-00-00";
            
            if (!empty($pnoukti)) $pnoukti = str_replace("'", '', $pnoukti);
            
            
            $pjml=$_POST['txt_njml'][$pbarangid];
            if (empty($pjml)) $pjml=0;
            $pjml=str_replace(",","", $pjml);
            
            
            if ((DOUBLE)$pjml>0) {
                $pinsert_data_detail[] = "('$pkodeid', '$pbarangid', '$pjml', '$ptglmm', '$pnoukti')";
                $psimpandata=true;
            }
            //echo "$pbarangid, $pjml<br/>";
            //echo "('$pkodeid', '$pbarangid', '$pjml')<br/>";
        }
        
        
        if ($psimpandata == true) {
            
            mysqli_query($cnmy, "START TRANSACTION");
            
            if ($act=="input") {
                
                $query_mst="INSERT INTO dbmaster.t_barang_terima (IDTERIMA, TANGGAL, KARYAWANID, DIVISIID, KDSUPP, NOTES, USERID)VALUES"
                        . "('$pkodeid', '$ptglminta', '$pkaryawanid', '$pgrpprod', '$psupplierid', '$pnotes', '$puserinput')";
                
            }elseif ($act=="update") {
                $query_mst="UPDATE dbmaster.t_barang_terima SET TANGGAL='$ptglminta', KARYAWANID='$pkaryawanid', "
                        . " DIVISIID='$pgrpprod', KDSUPP='$psupplierid', NOTES='$pnotes', "
                        . " USERID='$puserinput' WHERE IDTERIMA='$pkodeid'";
            }
            $pinsertmaster = mysqli_query($cnmy, $query_mst);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            $query_delete_detail="DELETE FROM dbmaster.t_barang_terima_d WHERE IDTERIMA='$pkodeid'";
            $pdeletedetail = mysqli_query($cnmy, $query_delete_detail);
            $erropesan = mysqli_error($cnmy); 
            if (!empty($erropesan)) { 
                mysqli_query($cnmy, "UPDATE dbmaster.t_barang_terima SET STSNONAKTIF='Y', NOTES=CONCAT(NOTES,'_ ERROR SAVE') WHERE IDTERIMA='$pkodeid'");
                echo $erropesan; 
                mysqli_close($cnmy); exit;     
            }
             
            $query_detail="INSERT INTO dbmaster.t_barang_terima_d (IDTERIMA, IDBARANG, JUMLAH, TANGGAL, NOBUKTI) VALUES ".implode(', ', $pinsert_data_detail);
            $pinsertdetail = mysqli_query($cnmy, $query_detail);
            $erropesan = mysqli_error($cnmy); 
            if (!empty($erropesan)) { 
                mysqli_query($cnmy, "UPDATE dbmaster.t_barang_terima SET STSNONAKTIF='Y', NOTES=CONCAT(NOTES,'_ ERROR SAVE') WHERE IDTERIMA='$pkodeid'");
                echo $erropesan; 
                mysqli_close($cnmy); exit;     
            }
            
            
            
            if ($query_detail AND $query_delete_detail AND $query_detail) {
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