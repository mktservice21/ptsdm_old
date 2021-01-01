<?php
session_start();
include "../../config/koneksimysqli_it.php";
include "../../config/fungsi_sql.php";
$cnmy=$cnit;
$dbname = "hrd";
$dbname2 = "dbmaster";

$module=$_POST['u_module'];
$act=$_POST['u_act'];
$idmenu=$_POST['u_idmenu'];




//HAPUS DATA
if (isset($_GET['ket'])) {
    $kodenya= $_GET['id'];
    $kethapus= $_GET['kethapus'];
    if ($kethapus=="null") $kethapus="";
    
    if (!empty($kodenya)) {
        
        
        $sql = "insert into $dbname2.backup_br0 
               SELECT * FROM $dbname.br0 WHERE brId='$kodenya'";
        mysqli_query($cnit, $sql);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $sql = "insert into $dbname.br0_reject(brId, KET, IDREJECT, TGLREJECT)values"
                . "('$kodenya', '$kethapus', '$_SESSION[IDCARD]', NOW())";
        
        mysqli_query($cnit, $sql);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        //delete
        mysqli_query($cnit, "DELETE FROM $dbname.br0 WHERE brId='$kodenya'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act='.$act);
}


//=================================




// Hapus entry
if ($module=='entrybrnon' AND $act=='hapus')
{
    //mysqli_query($cnmy, "update $dbname.br0 set NONAKTIF='Y' WHERE brId='$_GET[id]'");
    //header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
}
elseif ($module=='entrybrnon' AND ($act=='editterima' OR $act=='edittransfer' OR $act=='input' OR $act=='update'))
{

    
    //terima
    if ($act=='editterima'){
    
        $kodenya=$_POST['e_nobr'];
        $prprealisasi=str_replace(",","", $_POST['e_realisasi']);
        $datetrm="";
        $ptgl="null";
        if (!empty($_POST['e_tgltrm'])) {
            $datetrm = str_replace('/', '-', $_POST['e_tgltrm']);
            $ptgl= date("Y-m-d", strtotime($datetrm));
        }
        
        
        $query = "update $dbname.br0 set "
                . "  tgltrm='$ptgl', "
                . "  jumlah1='$prprealisasi' where brId='$kodenya'";
        mysqli_query($cnmy, $query);
        
        
        //update modif terima
        $query = "update $dbname.br0_ttd SET MODIFTERIMAID='$_SESSION[IDCARD]', "
                . " MODIFTERIMADATE=NOW() WHERE brId='$kodenya'";
        mysqli_query($cnmy, $query);
        
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
        exit;
    }
    
    
    
    // transfer
    if ($act=='edittransfer'){
    
        $kodenya=$_POST['e_nobr'];
        $prprealisasi=str_replace(",","", $_POST['e_realisasi']);
        $datetrm="";
        $ptgl="null";
        if (!empty($_POST['e_tgltrans'])) {
            $datetrm = str_replace('/', '-', $_POST['e_tgltrans']);
            $ptgl= date("Y-m-d", strtotime($datetrm));
        }
        
        $query = "update $dbname.br0 set "
                . "  tgltrans='$ptgl', "
                . "  jumlah1='$prprealisasi' where brId='$kodenya'";
        mysqli_query($cnmy, $query);
        
        
        //update modif transfer
        $query = "update $dbname.br0_ttd SET MODIFTRANSID='$_SESSION[IDCARD]', "
                . " MODIFTRANSDATE=NOW() WHERE brId='$kodenya'";
        mysqli_query($cnmy, $query);
        
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
        exit;
    }
    
    
    
    /*
    if ($act=='input'){
        
        $sql=  mysqli_query($cnmy, "select DATE_FORMAT(CURRENT_DATE(),'%Y%m%d') AS TGLNYA, CONCAT(TAHUN) as PERIODE, brId as NOURUT from dbmaster.sdm_counter where CONCAT(TAHUN)=DATE_FORMAT(CURRENT_DATE(),'%Y')");
        $ketemu=  mysqli_num_rows($sql);
        $awal=9; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            if (!empty($o['NOURUT'])) {
                $periode=$o['TGLNYA'];
                $urut=$o['NOURUT']+1;
                $jml=  strlen($urut);
                $awal=$awal-$jml;
                $kodenya=str_repeat("0", $awal).$urut;
            }else{
                mysqli_query($cnmy, "insert into dbmaster.sdm_counter(TAHUN, BULAN)values(DATE_FORMAT(CURRENT_DATE(),'%Y'), DATE_FORMAT(CURRENT_DATE(),'%m'))");
                $kodenya=str_repeat("0", (int)$awal-1)."1";
            }

            mysqli_query($cnmy, "update dbmaster.sdm_counter set brId=ifnull(brId,0)+1 where CONCAT(TAHUN,BULAN)=DATE_FORMAT(CURRENT_DATE(),'%Y%m')");
            $kodenya=$periode."-".$kodenya;
        
        }else{
            $kodenya=$_POST['e_nobr'];
        }
    }else{
        $kodenya=$_POST['e_nobr'];
    }
     * 
     */
    
    if ($act=='input') {
        $sql=  mysqli_query($cnmy, "select max(brId) as NOURUT from $dbname.br0");
        $ketemu=  mysqli_num_rows($sql);
        $awal=10; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya=str_repeat("0", $awal).$urut;
        }else{
            $kodenya=$_POST['e_nobr'];
        }
    }else{
        $kodenya=$_POST['e_nobr'];
    }
    
    if (empty($kodenya)){
        echo "ID kosong, ulang lagi....";
        exit;
    }
    
    $date1 = str_replace('/', '-', $_POST['e_tglinput']);
    $date2="";
    if (!empty($_POST['e_tgltrans']))
        $date2 = str_replace('/', '-', $_POST['e_tgltrans']);
    
    $ptglinput= date("Y-m-d", strtotime($date1));
    $pidcabang=$_POST['e_idcabang'];
    $pkaryawan=$_POST['e_idkaryawan'];
    $pdivprodid=$_POST['cb_divisi'];
    
    $pcoa=$_POST['cb_coa'];
    //$pkode=  getfieldcnmy("select kodeid as lcfields from dbmaster.v_coa where COA4='$pcoa'");
    $pkode=$_POST['cb_kode'];
    
    
    $paktivitas1=$_POST['e_aktivitas'];
    $paktivitas2=$_POST['e_aktivitas2'];
    $pjenisuang=$_POST['cb_jenis'];
    $prpnya=str_replace(",","", $_POST['e_jmlusulan']);
    $prprealisasi=str_replace(",","", $_POST['e_realisasi']);
    $prpcn=str_replace(",","", $_POST['e_cn']);
    $pnoslip=$_POST['e_noslip'];
    
    $ptgltras="null";
    if (!empty($date2))
        $ptgltras= date("Y-m-d", strtotime($date2));
    
    
    
    if ($pcoa=="") {
        echo "coa kosong...";
        exit;
    }
    
    if ($pkode=="") {
        echo "kode posting kosong...";
        exit;
    }
    
    
    //selain OTC
    $pwilayah="01";
    $pcabwil=  substr($pidcabang, 7,3);
    if ($pidcabang=="0000000001")
        $pwilayah="01";
    else{
        $reg=  getfieldcnit("select distinct region as lcfields from dbmaster.icabang where iCabangId='$pidcabang'");
        if ($reg=="B")
            $pwilayah="02";
        else
            $pwilayah="03";
    }
    
    $pwilgabungan=$pwilayah."-".$pcabwil;
    
    
    
    
    if ($act=='input') {
        
        $sql=  mysqli_query($cnmy, "select brId from $dbname.br0 where brId='$kodenya'");
        $ketemu=  mysqli_num_rows($sql);
        if ($ketemu>0){
            echo "Kode : $kodenya, sudah ada";
            exit;
        }
        
        $query="insert into $dbname.br0 (brId, tgl, karyawanid, jumlah, aktivitas1, aktivitas2, icabangid, divprodid)values"
                . "('$kodenya', '$ptglinput', '$pkaryawan', '$prpnya', '$paktivitas1', '$paktivitas2', '$pidcabang', '$pdivprodid')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $sql=  mysqli_query($cnmy, "select brId from $dbname.br0 where brId='$kodenya'");
        $ketemu=  mysqli_num_rows($sql);
        if ($ketemu==0) {
            echo "Penyimpanan gagal...";
            exit;
        }
        
        
        // jika orang HO / Fianance Input
        $query = "insert into $dbname.br0_ttd (brId, TTDPROS_ID, TTDPROS_DATE)values('$kodenya', '$_SESSION[IDCARD]', NOW())";
        mysqli_query($cnmy, $query);
        
        
        //update modif transfer
        if (!empty($_POST['e_tgltrans'])) {
            $query = "update $dbname.br0_ttd SET MODIFTRANSID='$_SESSION[IDCARD]', "
                    . " MODIFTRANSDATE=NOW() WHERE brId='$kodenya'";
            mysqli_query($cnmy, $query);
        }
        

    }

    //MODIFUN='$_SESSION[IDCARD]',  
    $query = "update $dbname.br0 set "
            . "  tgl='$ptglinput', "
            . "  kode='$pkode', "
            . "  aktivitas1='$paktivitas1', "
            . "  aktivitas2='$paktivitas2', "
            . "  ccyid='$pjenisuang', "
            . "  jumlah='$prpnya', "
            . "  cn='$prpcn', "
            . "  realisasi1='$prprealisasi', "
            . "  karyawanid='$pkaryawan', "
            . "  noslip='$pnoslip', "
            . "  lampiran='N', "
            . "  tgltrans='$ptgltras', "
            . "  user1='$_SESSION[USERID]', "
            . "  icabangid='$pidcabang', "
            . "  divprodid='$pdivprodid', "
            . "  via='N', "
            . "  ca='N'  WHERE brId='$kodenya'";
    mysqli_query($cnmy, $query);
    
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
        echo $erropesan; exit;
    }
    
    
    $query = "update $dbname.br0 set "
            . "  COA4='$pcoa', KODEWILAYAH='$pwilgabungan' WHERE brId='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
    //$query = "update $dbname.br0 set trmskid='$trmskid', tglacc='$periode3' where brId='$kodenya' ";
    //mysqli_query($cnmy, $query);
    
                        

    
    if (!empty($_POST['cx_lapir'])) mysqli_query($cnmy, "update $dbname.br0 set lampiran='Y' where brId='$kodenya'");
    if (!empty($_POST['cx_ca'])) mysqli_query($cnmy, "update $dbname.br0 set ca='Y' where brId='$kodenya'");
    if (!empty($_POST['cx_via'])) mysqli_query($cnmy, "update $dbname.br0 set via='Y' where brId='$kodenya'");
    
    /*
    
    $coa1=$_POST['cb_level1'];
    $coa2=$_POST['cb_level2'];
    $coa3=$_POST['cb_level3'];
    $coa4=$_POST['cb_level4'];
    
    $query = "update $dbname.br0 set "
            . "  COA1='$coa1', "
            . "  COA2='$coa2', "
            . "  COA3='$coa3', "
            . "  COA4='$coa4' where "
            . "  brId='$kodenya'";
    mysqli_query($cnmy, $query);
    */
    
    
    $query = "update $dbname.br0 set MODIFDATE=NOW() where brId='$kodenya' ";
    mysqli_query($cnmy, $query);
    
    
    if ($act=='input')
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complete');
        

}


?>

