<?php
session_start();

    if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
        echo "Belum ada data yang disimpan. Anda Harus Login Ulang."; exit;
    }
    
include "../../config/koneksimysqli.php";
$cnmy=$cnmy;
$dbname = "dbmaster";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$pnourt=$_POST['unourut'];
$ptrans=$_POST['utgltrans'];
$psudahpernah=$_POST['usudahpernah'];
$pbukti=$_POST['unobukti'];
if (!empty($pbukti)) $pbukti = str_replace("'", " ", $pbukti);
$ptglnya= date("Y-m-d", strtotime($ptrans));

$berhasil="Tidak ada data yang disimpan";
if ($module=='closingbrlkcaotc' AND $act=='input') {
    if (!empty($pnourt)) {
        $uptgltrans= " tgltrans='$ptglnya' ";
        if (empty($ptrans)) $uptgltrans= " tgltrans=null ";
        
        //if ($psudahpernah!="SUDAH") {
            
            $query = "INSERT INTO dbmaster.t_brrutin_ca_close_otc (tglinput, bulan, karyawanid, divisi, idrutin, idca1, idca2, credit, saldo, ca1, ca2, userid, sts, jml_adj)
                select CURRENT_DATE() tglinput, bulan, karyawanid, divisi, idrutin, idca1, idca2, jumlah, totalrutin, ca1, ca2, '$_SESSION[IDCARD]' userid, sts, jml_adj FROM 
                dbmaster.tmp_lk_closing_otc WHERE idsession='$_SESSION[IDSESI]' and userid='$_SESSION[IDCARD]' AND nourut in $pnourt";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        //}
        /*
        $query = "UPDATE dbmaster.t_brrutin0 SET $uptgltrans, nobukti='$pbukti' WHERE idrutin IN ("
                . "SELECT distinct IFNULL(idrutin,'') FROM dbmaster.tmp_lk_closing_otc WHERE nourut IN $pnourt)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        */
        
        $berhasil="Berhasil disimpan";
    }
}elseif ($module=='closingbrlkcaotc' AND $act=='hapus') {
    if (!empty($pnourt)) {
        
        
        /*
        $query = "UPDATE dbmaster.t_brrutin0 SET tgltrans=null, nobukti='' WHERE idrutin IN ("
                . "SELECT distinct IFNULL(idrutin,'') FROM dbmaster.t_brrutin_ca_close_otc WHERE nourut IN $pnourt)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        */
        
        $piddanabank=$_POST['uiddanabank'];
        $pnodivisi=$_POST['unodivbr'];
        $psubkode="21";
        if (empty($piddanabank)){
            $query = "UPDATE dbmaster.t_suratdana_bank SET stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE divisi='OTC' AND nodivisi='$pnodivisi' AND subkode='$psubkode' AND stsinput='K'";
        }else{
            $query = "UPDATE dbmaster.t_suratdana_bank SET stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE idinputbank='$piddanabank'";
        }
        
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        
        $query = "DELETE FROM dbmaster.t_brrutin_ca_close_otc WHERE nourut IN $pnourt";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        $berhasil="Hapus Berhasil...";
    }
}elseif ($module=='closingbrlkcaotc' AND $act=='simpan') {

    if (!empty($pnourt)) {
        $uptgltrans= " tgltrans='$ptglnya' ";
        if (empty($ptrans)) $uptgltrans= " tgltrans=null ";
        
        $query = "UPDATE dbmaster.t_brrutin_ca_close_otc SET $uptgltrans, nobukti='$pbukti' WHERE nourut IN $pnourt";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        /*
        $query = "UPDATE dbmaster.t_brrutin0 SET $uptgltrans, nobukti='$pbukti' WHERE idrutin IN ("
                . "SELECT distinct IFNULL(idrutin,'') FROM dbmaster.t_brrutin_ca_close_otc WHERE nourut IN $pnourt)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        */
        
        $piddanabank=$_POST['uiddanabank'];
        
        $psaldoreal=$_POST['usaldoreal'];
        $pnodivisi=$_POST['unodivbr'];
        
        $pjumlah=str_replace(",","", $psaldoreal);//tidak jadi, cek dibawah ada jumlah
        
        $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idinputbank,8)) as NOURUT from dbmaster.t_suratdana_bank");
        $ketemu=  mysqli_num_rows($sql);
        $awal=8; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            if (empty($o['NOURUT'])) $o['NOURUT']=0;
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="BN".str_repeat("0", $awal).$urut;
        }
        
        if (!empty($kodenya)) {
            
            $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_br WHERE nodivisi='$pnodivisi' AND divisi='OTC'");
            $r    = mysqli_fetch_array($edit);
            $pjenis=$r['kodeid'];//kodeid
            $psubkode=$r['subkode'];//subkode
            $pidinputspd=$r['idinput'];
            //$pcoa=$r['coa4'];
            //$pcoa="000-0";//intransit jkt
            $pcoa="000";//intransit sby
            $pdivisi=$r['divisi'];//pengajuan
            $pnobukti=$pbukti;
            $pnospd=$r['nomor'];
            $pjumlah=$r['jumlah'];
            $pstatus="1";
            $ptglmasuk=$ptglnya;
            $pket="";
            $pnobrid="";
            $pnoslip="";
            
            $query = "UPDATE dbmaster.t_suratdana_bank SET stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE divisi='OTC' AND idinput='$pidinputspd' AND nodivisi='$pnodivisi' AND subkode='$psubkode' AND stsinput='K'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            if (!empty($ptrans)) {
                
                $query = "INSERT INTO dbmaster.t_suratdana_bank (stsinput, idinputbank, tanggal, coa4, kodeid, subkode, idinput, nomor, nodivisi, "
                        . " nobukti, divisi, sts, jumlah, keterangan, brid, noslip, userid)values"
                        . "('K', '$kodenya', '$ptglmasuk', '$pcoa', '$pjenis', '$psubkode', '$pidinputspd', '$pnospd', '$pnodivisi', "
                        . " '$pnobukti', '$pdivisi', '$pstatus', '$pjumlah', '$pket', '$pnobrid', '$pnoslip', '$_SESSION[IDCARD]')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
            }
            
        }
        
        
        
        $berhasil="Simpan Tgl Transfer dan No Bukti Berhasil...";
    }
}
echo $berhasil;
?>

