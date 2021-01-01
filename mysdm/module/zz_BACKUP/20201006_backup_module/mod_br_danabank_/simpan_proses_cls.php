<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    $dbname = "dbmaster";
    
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    $ptgl01=$_POST['utgl'];
    $ptglproses= date("Y-m-01", strtotime($ptgl01));
    $periode1= date("Ym", strtotime($ptgl01));
        
    $berhasil="Tidak ada data yang disimpan";
    
    
    if ($module=="uploadgambarsave" AND $act=="uploadgambar") {
        $nkodeneksi="../../config/koneksimysqli.php";
        $periode1= date("Ym", strtotime($ptgl01));
        
        include "../../config/fungsi_image.php";
        $gambarnya=$_POST['uimgconver'];
        if (!empty($gambarnya)) {
            mysqli_query($cnmy, "UPDATE dbmaster.t_bank_saldo SET gambar='$gambarnya' WHERE DATE_FORMAT(bulan,'%Y%m')='$periode1'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "data berhasil disimpan... upload error"; goto hapusdata; }
        }
    
        $berhasil="gambar berhasil diupload";
        
        mysqli_close($cnmy);
        echo $berhasil;
        exit;
    }
    
    
    if ($module=="brdanabank" AND $act=="hapus") {
        $query = "DELETE from dbmaster.t_bank_saldo_d WHERE DATE_FORMAT(bulan,'%Y%m')='$periode1'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "DELETE from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$periode1'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $berhasil="data berhasil dihapus...";
        mysqli_close($cnmy);
        echo $berhasil; exit;
    }elseif ($module=="brdanabank" AND $act=="simpan") {
        
        $psaldoakhir=$_POST['usaldoakhir'];
        $psaldoakhir=str_replace(",","", $psaldoakhir);
        
        $query = "DELETE from dbmaster.t_bank_saldo WHERE DATE_FORMAT(bulan,'%Y%m')='$periode1'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        $query = "DELETE from dbmaster.t_bank_saldo_d WHERE DATE_FORMAT(bulan,'%Y%m')='$periode1'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        
        $nkodeneksi="../../config/koneksimysqli.php";
        $periode1= date("Ym", strtotime($ptgl01));
        $periode_sbl = date('F Y', strtotime('-1 month', strtotime($ptgl01)));
        $periode_pros= date("F Y", strtotime($ptgl01));
        $tgl_sbl = date('Ym', strtotime('-1 month', strtotime($ptgl01)));
        include("query_saldobank.php");
        $tmp01=seleksi_query_bank($nkodeneksi, $ptgl01);
        if ($tmp01==false) {
            $berhasil="error"; goto hapusdata;
        }
        $query ="INSERT INTO dbmaster.t_bank_saldo_d (bulan, idinputbank, parentidbank, stsinput, tanggal, "
                . " nobukti, coa4, kodeid, subkode, idinput, nomor, divisi, keterangan, "
                . " nodivisi, jumlah, sts, userid, brid, noslip, realisasi, "
                . " customer, aktivitas1, nket, NAMA4, mintadana, debit, kredit, saldo, saldoawal, sudah_trans, subnama, nama_user, igroup, inama)"
                . "SELECT '$ptglproses' as bulan, idinputbank, parentidbank, stsinput, tanggal, "
                . " nobukti, coa4, kodeid, subkode, idinput, nomor, divisi, keterangan, "
                . " nodivisi, jumlah, sts, userid, brid, noslip, realisasi, "
                . " customer, aktivitas1, nket, NAMA4, mintadana, debit, kredit, saldo, saldoawal, sudah_trans, subnama, nama_user, igroup, inama "
                . " FROM $tmp01 order by tanggal, nobukti, nomor, divisi, nodivisi, stsinput, kodeid, idinputbank";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "INSERT INTO dbmaster.t_bank_saldo (bulan, jumlah, userid)VALUES"
                . "('$ptglproses', '$psaldoakhir', '$_SESSION[IDCARD]')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        include "../../config/fungsi_image.php";
        $gambarnya=$_POST['uimgconver'];
        if (!empty($gambarnya)) {
            mysqli_query($cnmy, "UPDATE dbmaster.t_bank_saldo SET gambar='$gambarnya' WHERE DATE_FORMAT(bulan,'%Y%m')='$periode1'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "data berhasil disimpan... upload error"; goto hapusdata; }
        }
    
        //$berhasil="$act, $periode1 : $ptglproses, $psaldoakhir, data berhasil disimpan";
        $berhasil="data berhasil disimpan";
    }
    
hapusdata:
    mysqli_query($cnmy, "DROP TABLE $tmp01");

    mysqli_close($cnmy);
    echo $berhasil;

?>