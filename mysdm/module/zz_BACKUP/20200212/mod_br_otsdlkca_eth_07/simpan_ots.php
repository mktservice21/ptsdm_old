<?PHP
    session_start();
    include "../../config/koneksimysqli.php";
    $cnmy=$cnmy;
    $dbname = "dbmaster";

    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];

if ($module=="outlkcaethical") {
    
    $pbulan=$_POST['e_pilperiodeots'];//bulan ar ap atau closing lk (input ini ada di viewdatatable_ost)
    $pidkry_master=$_POST['cb_karyawan'];
    $pnama_master=$_POST['e_namamaster'];
    if (empty($pnama_master)) {
        $query = "SELECT nama FROM hrd.karyawan where karyawanId='$pidkry_master'";
        $tampil= mysqli_query($cnmy, $query);
        $osz= mysqli_fetch_array($tampil);
        $pnama_master=$osz['nama'];
    }
    
    $ptgl=$_POST['e_tglberlaku'];//tgl kembalikan uang
    $ptotal_rpmst=$_POST['e_jmlusulan'];//total uang kembali
    $ptotal_rpmst=str_replace(",","", $ptotal_rpmst);
    
    $ptotal_rpmst2=$_POST['e_jmlusulan2'];//total uang kembali (asli)
    $ptotal_rpmst2=str_replace(",","", $ptotal_rpmst2);
    
    $pblnnya= date("Y-m-01", strtotime($pbulan));//bulan ar ap atau closing lk
    $pblnthn= date("Ym", strtotime($pbulan));//bulan ar ap atau closing lk

    $ptgl_kembali="0000-00-00";
    if (!empty($ptgl)) $ptgl_kembali= date("Y-m-d", strtotime($ptgl));//tgl kembalikan uang

    $pcoa_1="105-02"; //uang muka
    $pcoa_2="905-02"; //pembulatan

    $no_idgroup=$_POST['e_id'];
    
    
    if ($act=='input') {

        $no_idgroup=1;
        $query = "SELECT MAX(igroup) igroup FROM dbmaster.t_brrutin_outstanding";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ox= mysqli_fetch_array($tampil);
            if (!empty($ox['igroup'])) {
                $no_idgroup=(double)$ox['igroup']+1;
            }
        }

    }
    
    
    $query = "DELETE FROM $dbname.t_brrutin_outstanding WHERE DATE_FORMAT(bulan,'%Y%m')='$pblnthn' AND ikaryawanid='$pidkry_master'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Delete"; exit; }
        
    foreach ($_POST['txtchksama'] as $no_brid) {
        $pdivisi=$_POST['txtiddiv'][$no_brid];
        $pstatus=$_POST['cbsts'][$no_brid];
        $pidkry=$_POST['txtidkry'][$no_brid];
        $pnamakaryawan=$_POST['txtnmkry'][$no_brid];
        
        
        $psaldo=$_POST['txtjmlsaldo'][$no_brid];
        $psaldo=str_replace(",","", $psaldo);
        
        $pca=$_POST['txtjmlca'][$no_brid];
        $pca=str_replace(",","", $pca);
        
        $pselisih=$_POST['txtjmlselisih'][$no_brid];
        $pselisih=str_replace(",","", $pselisih);
        
        $pkembali_rp=$_POST['txtjmlkembali'][$no_brid];
        $pkembali_rp=str_replace(",","", $pkembali_rp);
        
        if (empty($psaldo)) $psaldo=0;
        if (empty($pca)) $pca=0;
        if (empty($pselisih)) $pselisih=0;
        if (empty($pkembali_rp)) $pkembali_rp=0;

        $nstsjenis="1";//dimasukan ke ots dulu statusnya $pstatus ada hubungannya
        
        $plebih_rp=(double)$pkembali_rp-(double)$pselisih;
        if (empty($plebih_rp)) $plebih_rp=0;

        $prp_kembali=$pkembali_rp;
        if ((double)$plebih_rp>0){
            //$prp_kembali=$pselisih; //simpan disesuaikan dengan selisih, sisa di entry kembali ke coa berbeda
        }
        
        
        $pket="";

        
        $query="INSERT INTO $dbname.t_brrutin_outstanding (tglinput, bulan, karyawanid, divisi, saldo, ca, selisih, jumlah_kembali, kembali_rp, tgl_kembali, ots_status, keterangan, coa, userid, nama_karyawan, "
                . "igroup, ikaryawanid, rp_total, rp_total2, inama_karyawan)VALUES"
                . "(CURRENT_DATE(), '$pblnnya' ,'$pidkry', '$pdivisi', '$psaldo', '$pca', '$pselisih', '$pkembali_rp', '$prp_kembali', '$ptgl_kembali', '$nstsjenis', '$pket', '$pcoa_1', '$_SESSION[IDCARD]', '$pnamakaryawan', "
                . "'$no_idgroup', '$pidkry_master', '$ptotal_rpmst', '$ptotal_rpmst2', '$pnama_master')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan"; exit; }
        
        if ((double)$plebih_rp<>0){
            $nstsjenis=$pstatus; // di ubah jadi adjusment, 1 = outstanding
            if ($pstatus=="4") $pcoa_2=$pcoa_1;

            $query="INSERT INTO $dbname.t_brrutin_outstanding (tglinput, bulan, karyawanid, divisi, saldo, ca, selisih, jumlah_kembali, kembali_rp, tgl_kembali, ots_status, keterangan, coa, userid, nama_karyawan, "
                    . "igroup, ikaryawanid, rp_total, rp_total2, inama_karyawan)VALUES"
                    . "(CURRENT_DATE(), '$pblnnya' ,'$pidkry', '$pdivisi', '$psaldo', '$pca', '$pselisih', '$pkembali_rp', '$plebih_rp', '$ptgl_kembali', '$nstsjenis', '$pket', '$pcoa_2', '$_SESSION[IDCARD]', '$pnamakaryawan', "
                    . "'$no_idgroup', '$pidkry_master', '$ptotal_rpmst', '$ptotal_rpmst2', '$pnama_master')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Simpan Lebih / Kurang"; exit; }
        }
    
    
        
        //echo "$no_idgroup . $ptotal_rpmst . bln $pbulan, mas : $pidkry_master, tgl : $ptgl, $pidkry - $pdivisi : sts : $pstatus, $psaldo, $pca, $pselisih,  k : $pkembali_rp <br/>";
    }
    
    
}
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
?>
