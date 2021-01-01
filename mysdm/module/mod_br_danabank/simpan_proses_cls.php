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
		
		
		
            $puserid=$_SESSION['USERID'];
            $now=date("mdYhis");
            $tmp21 =" dbtemp.tmptarikbank21_".$puserid."_$now ";
            $tmp22 =" dbtemp.tmptarikbank22_".$puserid."_$now ";
            $tmp23 =" dbtemp.tmptarikbank23_".$puserid."_$now ";

            $query = "ALTER TABLE $tmp01 ADD `idinputdiv` BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "CREATE INDEX `norm1` ON $tmp01 (idinputdiv,idinputbank)";
            mysqli_query($cnmy, $query); 
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query = "ALTER TABLE $tmp01 ADD `jmlothselisih` DECIMAL(20,2)";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $query = "select *, REPLACE(idinputbank,'OT','') as iidasli from $tmp01 WHERE left(idinputbank,2)='OT' AND IFNULL(divisi,'')<>'OTC'";
            $query = "create TEMPORARY table $tmp21 ($query)";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "ALTER TABLE $tmp21 MODIFY COLUMN iidasli BIGINT(18)";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "CREATE INDEX `norm1` ON $tmp21 (idinputdiv,idinputbank, iidasli)";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "select DISTINCT a.idinputbank, a.iidasli, c.periode_ca2, e.nodivisi, d.idinput, a.tanggal bulanots, c.bulan 
                from $tmp21 a 
                LEFT JOIN dbmaster.t_brrutin_outstanding b ON a.iidasli=b.idots 
                LEFT JOIN dbmaster.t_brrutin_ca_close c on 
                b.karyawanid=c.karyawanid AND DATE_FORMAT(b.bulan,'%Y%m')=DATE_FORMAT(c.bulan,'%Y%m') 
                LEFT JOIN dbmaster.t_brrutin_ca_close_head d on c.igroup=d.igroup 
                LEFT JOIN dbmaster.t_suratdana_br e on d.idinput=e.idinput";
            $query = "create TEMPORARY table $tmp22 ($query)";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $query = "UPDATE $tmp21 a JOIN (select distinct idinputbank, idinput, nodivisi FROM $tmp22 WHERE IFNULL(nodivisi,'')<>'' and periode_ca2<>'1') b on "
                    . " a.idinputbank=b.idinputbank SET a.idinput=b.idinput WHERE IFNULL(a.stsinput,'')<>'M'";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "UPDATE $tmp21 a JOIN (select distinct bulan, idinput, nodivisi FROM $tmp22 WHERE IFNULL(nodivisi,'')<>'' and periode_ca2<>'1') b on "
                    . " DATE_FORMAT(a.tanggal - INTERVAL '1' MONTH,'%Y%m')=DATE_FORMAT(b.bulan,'%Y%m') SET a.idinput=b.idinput WHERE IFNULL(a.stsinput,'')='M'";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "UPDATE $tmp21 a JOIN (select distinct idinput, nodivisi FROM $tmp22 WHERE IFNULL(nodivisi,'')<>'') b on "
                    . " a.idinput=b.idinput SET a.nodivisi=b.nodivisi WHERE IFNULL(a.idinput,'')<>''";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp22");

            $query = "select nobukti, sum(mintadana) as mintadana FROM $tmp21 WHERE IFNULL(stsinput,'')='N' GROUP BY 1";
            $query = "create TEMPORARY table $tmp22 ($query)";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $query = "UPDATE $tmp21 a JOIN $tmp22 b on a.nobukti=b.nobukti SET a.jmlothselisih=b.mintadana WHERE IFNULL(stsinput,'')='M'";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



            $query = "UPDATE $tmp01 a JOIN $tmp21 b on a.idinputdiv=b.idinputdiv AND a.idinputbank=b.idinputbank SET "
                    . " a.jmlothselisih=b.jmlothselisih, a.idinput=b.idinput, a.nodivisi=b.nodivisi WHERE LEFT(IFNULL(a.idinputbank,''),2)='OT' AND IFNULL(a.divisi,'')<>'OTC'";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



            mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp21");
            mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp22");
        
            
            
            
            $query = "select tgl, nodivisi, idinput from dbmaster.t_suratdana_br where IFNULL(stsnonaktif,'')<>'Y' AND CONCAT(kodeid, subkode) = '221' and divisi='OTC'";
            $query = "create TEMPORARY table $tmp23 ($query)";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "CREATE INDEX `norm1` ON $tmp23 (nodivisi,tgl, idinput)";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $query = "select *, REPLACE(idinputbank,'OT','') as iidasli from $tmp01 WHERE left(idinputbank,2)='OT' AND IFNULL(divisi,'')='OTC'";
            $query = "create TEMPORARY table $tmp21 ($query)";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "select DISTINCT a.idinputbank, a.iidasli, '2' as periode_ca2, d.nodivisi, d.idinput, a.tanggal bulanots, c.bulan 
                from $tmp21 a 
                LEFT JOIN dbmaster.t_brrutin_outstanding b ON a.iidasli=b.idots 
                LEFT JOIN dbmaster.t_brrutin_ca_close_otc c on 
                b.karyawanid=c.karyawanid AND DATE_FORMAT(b.bulan,'%Y%m')=DATE_FORMAT(c.bulan,'%Y%m') 
                LEFT JOIN $tmp23 d on DATE_FORMAT(d.tgl,'%Y%m')=DATE_FORMAT(c.bulan,'%Y%m')";
            $query = "create TEMPORARY table $tmp22 ($query)";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
            $query = "UPDATE $tmp21 a JOIN (select distinct idinputbank, idinput, nodivisi FROM $tmp22 WHERE IFNULL(nodivisi,'')<>'' and periode_ca2<>'1') b on "
                    . " a.idinputbank=b.idinputbank SET a.idinput=b.idinput WHERE IFNULL(a.stsinput,'')<>'M'";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "UPDATE $tmp21 a JOIN (select distinct bulan, idinput, nodivisi FROM $tmp22 WHERE IFNULL(nodivisi,'')<>'' and periode_ca2<>'1') b on "
                    . " DATE_FORMAT(a.tanggal - INTERVAL '1' MONTH,'%Y%m')=DATE_FORMAT(b.bulan,'%Y%m') SET a.idinput=b.idinput WHERE IFNULL(a.stsinput,'')='M'";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            $query = "UPDATE $tmp21 a JOIN (select distinct idinput, nodivisi FROM $tmp22 WHERE IFNULL(nodivisi,'')<>'') b on "
                    . " a.idinput=b.idinput SET a.nodivisi=b.nodivisi WHERE IFNULL(a.idinput,'')<>''";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

            mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp22");

            $query = "select nobukti, sum(mintadana) as mintadana FROM $tmp21 WHERE IFNULL(stsinput,'')='N' GROUP BY 1";
            $query = "create TEMPORARY table $tmp22 ($query)";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


            $query = "UPDATE $tmp21 a JOIN $tmp22 b on a.nobukti=b.nobukti SET a.jmlothselisih=b.mintadana WHERE IFNULL(stsinput,'')='M'";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



            $query = "UPDATE $tmp01 a JOIN $tmp21 b on a.idinputdiv=b.idinputdiv AND a.idinputbank=b.idinputbank SET "
                    . " a.jmlothselisih=b.jmlothselisih, a.idinput=b.idinput, a.nodivisi=b.nodivisi WHERE LEFT(IFNULL(a.idinputbank,''),2)='OT' AND IFNULL(a.divisi,'')='OTC'";
            mysqli_query($cnmy, $query);
            //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
		
		
        $query ="INSERT INTO dbmaster.t_bank_saldo_d (bulan, idinputbank, parentidbank, stsinput, tanggal, "
                . " nobukti, coa4, kodeid, subkode, idinput, nomor, divisi, keterangan, "
                . " nodivisi, jumlah, sts, userid, brid, noslip, realisasi, "
                . " customer, aktivitas1, nket, NAMA4, mintadana, debit, kredit, saldo, saldoawal, sudah_trans, subnama, nama_user, igroup, inama, jmlothselisih)"
                . "SELECT '$ptglproses' as bulan, idinputbank, parentidbank, stsinput, tanggal, "
                . " nobukti, coa4, kodeid, subkode, idinput, nomor, divisi, keterangan, "
                . " nodivisi, jumlah, sts, userid, brid, noslip, realisasi, "
                . " customer, aktivitas1, nket, NAMA4, mintadana, debit, kredit, saldo, saldoawal, sudah_trans, subnama, nama_user, igroup, inama, jmlothselisih "
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