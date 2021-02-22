<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
$puserid="";
$pidcard="";
$pidgroup="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];


if ($module=='pchpurchasereq')
{
    if ($act=="hapus") {
        
        if (empty($puserid)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        include "../../config/koneksimysqli.php";
        $pkodenya=$_GET['id'];
        
        $query = "UPDATE dbpurchasing.t_pr_transaksi SET stsnonaktif='Y' WHERE idpr='$pkodenya' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
    }elseif ($act=="input" OR $act=="update") {
        
        $pcardidlog=$_POST['e_idcardlogin'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        
        include "../../config/koneksimysqli.php";
        
        
        $kodenya=$_POST['e_id'];
        $ptglinput=$_POST['e_tglberlaku'];
        $ppengajuan=$_POST['cb_untuk'];
        $ptipeaju=$_POST['cb_tipeaju'];
        $pkaryawanid=$_POST['cb_karyawan'];
        $pidcabang=$_POST['cb_cabang'];
        $pnotes=$_POST['e_notes'];
        
        
        
        $pttgl = str_replace('/', '-', $ptglinput);
        $ptanggal= date("Y-m-d", strtotime($pttgl));
        $pthnbln= date("ym", strtotime($pttgl));
        
        if (!empty($pnotes)) $pnotes = str_replace("'", " ", $pnotes);
        
        
        
        $query = "select jabatanid from hrd.karyawan where karyawanid='$pkaryawanid'";
        $tampil= mysqli_query($cnmy, $query);
        $row= mysqli_fetch_array($tampil);
        $pjabatanid=$row['jabatanid'];
    
        
        $pkdspv=$_POST['e_kdspv'];
        $pkddm=$_POST['e_kddm'];
        $pkdsm=$_POST['e_kdsm'];
        $pkdgsm=$_POST['e_kdgsm'];
    
        
        $pisitglspv=false;
        $pisitgldm=false;
        $pisitglsm=false;
        $pisitglgsm=false;

        //$pkdspv="";$pkddm="";$pkdsm="A";$pkdgsm="A";

        if (empty($pkdspv)) {
            $pisitglspv=true;
            if (empty($pkddm)) {
                $pisitgldm=true;
                if (empty($pkdsm)) {
                    $pisitglsm=true;
                    if (empty($pkdgsm)) {
                        $pisitglgsm=true;
                    }
                }
            }
        }
    
    
        unset($pinsert_data_detail);//kosongkan array
        $psimpandata=false;
        foreach ($_POST['chkbox_br'] as $piddata) {
            if (empty($piddata)) {
                //continue;
            }
            
            $pidbrg=$_POST['m_idbrg'][$piddata];
            $pnmbrg=$_POST['m_nmbrg'][$piddata];
            $pspcbrg=$_POST['txt_specbr'][$piddata];
            $pjmlbrg=$_POST['txt_njmlbrg'][$piddata];
            $phrgbrg=$_POST['txt_nhrgbrg'][$piddata];
            $pketdata=$_POST['txt_ketbrg'][$piddata];
            $psatuan=$_POST['m_satuan'][$piddata];
            
            if (!empty($pnmbrg)) $pnmbrg = str_replace("'", " ", $pnmbrg);
            if (!empty($pspcbrg)) $pspcbrg = str_replace("'", " ", $pspcbrg);
            if (!empty($pketdata)) $pketdata = str_replace("'", " ", $pketdata);
            if (!empty($psatuan)) $psatuan = str_replace("'", " ", $psatuan);
            
            if (empty($pjmlbrg)) $pjmlbrg=1;
            if (empty($phrgbrg)) $phrgbrg=0;
            
            $pjmlbrg=str_replace(",","", $pjmlbrg);
            $phrgbrg=str_replace(",","", $phrgbrg);
            
            //echo "$piddata : $pidbrg, $pnmbrg, $pspcbrg, $phrgbrg, $pjmlbrg, $pketdata<br/>";
            
            $pinsert_data_detail[] = "('$pidbrg', '$pnmbrg', '$pspcbrg', '$phrgbrg', '$pjmlbrg', '$pketdata', '$psatuan')";
            $psimpandata=true;
                
        }
        
        
        if ($psimpandata==true) {
            
            $idusepl=$puserid;
            if (empty($idusepl)) $idusepl=(DOUBLE)$pcardidlog;
            $now=date("mdYhis");
            $tmp01 =" dbtemp.TMPINPTDATTRSPRL01_".$puserid."_$now ";


            $query = "CREATE TEMPORARY TABLE $tmp01 (idbarang varchar(10), namabarang varchar(150), "
                    . " idbarang_d int(10) ZEROFILL, spesifikasi varchar(500), harga double(20,2), jml double(20,2), keterangan varchar(500), satuan varchar(100), "
                    . " idkategori int(4), idsatuan int(4), kdsupp varchar(5), idtipe int(5), ibaru varchar(1) )";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            $query_detail="INSERT INTO $tmp01 (idbarang, namabarang, spesifikasi, harga, jml, keterangan, satuan) VALUES ".implode(', ', $pinsert_data_detail);
            mysqli_query($cnmy, $query_detail);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
                $query = "UPDATE $tmp01 as a JOIN dbmaster.t_barang as b on a.namabarang=b.NAMABARANG SET a.idbarang=b.idbarang WHERE IFNULL(a.idbarang,'')='' ";
                if ($ptipeaju=="102") $query .=" AND b.IDTIPE='30002' ";
                else $query .=" AND b.IDTIPE<>'30002' ";
                
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

                $query = "UPDATE $tmp01 as a JOIN dbpurchasing.t_pr_barang_d as b on "
                        . " TRIM(REPLACE(REPLACE(REPLACE(a.spesifikasi, '\n', ''), '\r', ''), '\t', ''))=TRIM(REPLACE(REPLACE(REPLACE(b.spesifikasi1, '\n', ''), '\r', ''), '\t', '')) AND "
                        . " IFNULL(a.idbarang,'')=IFNULL(b.idbarang,'') "
                        . " SET a.idbarang_d=b.idbarang_d WHERE IFNULL(a.idbarang,'')<>''";
                mysqli_query($cnmy, $query); 
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            //idkategori other=1, idsatuan=0, kdsupp=00001, idtipe=30010(other) atau 30002(IT)
            $query = "UPDATE $tmp01 SET idkategori='1',  idsatuan='0', kdsupp='00001', idtipe='30010' WHERE IFNULL(idbarang,'')=''";
            mysqli_query($cnmy, $query); 
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            $query = "select distinct idbarang, namabarang FROM $tmp01 WHERE IFNULL(idbarang,'')='' AND IFNULL(namabarang,'')<>''";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ((DOUBLE)$ketemu>0) {
                $awal_o=9;
                //$sql_n=  mysqli_query($cnmy, "select MAX(RIGHT(IDBARANG,9)) as NOURUT_O from dbmaster.t_barang");
                //$oo=  mysqli_fetch_array($sql_n);
                //$purut_=$oo['NOURUT_O']+1;
                
                while ($nrow= mysqli_fetch_array($tampil)) {
                    $jml=  strlen($purut_);
                    $nawal=$awal_o-$jml;
                    //$pkdbrgpl="I".str_repeat("0", $nawal).$purut_;
                    $pkdbrgpl="";
                    $puntuknmbrg=$nrow['namabarang'];
                    $query = "UPDATE $tmp01 SET idbarang='$pkdbrgpl', ibaru='Y' WHERE IFNULL(idbarang,'')='' AND IFNULL(namabarang,'')='$puntuknmbrg'";
                    mysqli_query($cnmy, $query); 
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                    
                    //echo "$pkdbrgpl = $puntuknmbrg<br/>";
                    $purut_=(DOUBLE)$purut_+1;
                }
                
                $query= "INSERT INTO dbmaster.t_barang(IDBARANG, NAMABARANG, IDKATEGORI, IDSATUAN, KDSUPP, IDTIPE, HARGA)"
                        . "SELECT DISTINCT idbarang, namabarang, idkategori, idsatuan, kdsupp, idtipe, harga FROM $tmp01 WHERE IFNULL(ibaru,'')='Y' AND "
                        . " IFNULL(idbarang,'')<>''";
                //mysqli_query($cnmy, $query); 
                //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                
                
            }
            
            
            $query = "INSERT INTO dbpurchasing.t_pr_barang_d (idbarang, spesifikasi1, harga)"
                    . "SELECT DISTINCT idbarang, spesifikasi, harga FROM $tmp01 WHERE IFNULL(idbarang_d,'')=''";
            mysqli_query($cnmy, $query); 
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            $query = "UPDATE $tmp01 as a JOIN dbpurchasing.t_pr_barang_d as b on "
                    . " TRIM(REPLACE(REPLACE(REPLACE(a.spesifikasi, '\n', ''), '\r', ''), '\t', ''))=TRIM(REPLACE(REPLACE(REPLACE(b.spesifikasi1, '\n', ''), '\r', ''), '\t', '')) AND "
                    . " IFNULL(a.idbarang,'')=IFNULL(b.idbarang,'') "
                    . " SET a.idbarang_d=b.idbarang_d, b.harga=a.harga WHERE IFNULL(a.idbarang,'')<>''";
            mysqli_query($cnmy, $query); 
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            
        }else{
            echo "Tidak ada data yang disimpan...";
            mysqli_close($cnmy);
            exit;
        }
        
        
        
        if ($act=="input") {
            
            $sql=  mysqli_query($cnmy, "select pr as NOURUT from dbmaster.t_setup_periode where thnbln='$pthnbln'");
            $ketemu=  mysqli_num_rows($sql);
            $awal=4; $nurut=1; $kodenya=""; $padaurut=false;
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                if (empty($o['NOURUT'])) $o['NOURUT']=0;
                $nurut=$o['NOURUT']+1;
                $padaurut=true;
            }else{
                $nurut=1;
            }
            $jml=  strlen($nurut);
            $awal=$awal-$jml;
            $kodenya="PR".$pthnbln."".str_repeat("0", $awal).$nurut;

            if ($padaurut==false) {
                mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_periode (thnbln, pr)VALUES('$pthnbln', '0')");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }

            mysqli_query($cnmy, "UPDATE dbmaster.t_setup_periode SET pr=IFNULL(pr,0)+1 WHERE thnbln='$pthnbln'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

        }else{
            $kodenya=$_POST['e_id'];
        }
        
        if (empty($kodenya)) {
            echo "ID KOSONG";
            mysqli_close($cnmy);
            exit;
        }
        
        
        
        //echo "$pkdspv, $pkddm, $pkdsm, $pkdgsm<br/>";
        //echo "$pcardidlog - $kodenya, $ptanggal, $ppengajuan, $ptipeaju,$pkaryawanid, $pidcabang, $pjabatanid, $pnotes <br/>"; mysqli_close($cnmy); exit;
    
        if ($act=="input") {
            $query = "INSERT INTO dbttd.t_pr_transaksi_ttd(idpr)VALUES('$kodenya')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            $query = "INSERT INTO dbpurchasing.t_pr_transaksi (pilihpo, idpr, tanggal, pengajuan, idtipe, karyawanid, icabangid, jabatanid, aktivitas, userid)values"
                    . "('Y', '$kodenya', '$ptanggal', '$ppengajuan', '$ptipeaju', '$pkaryawanid', '$pidcabang', '$pjabatanid', '$pnotes', '$pcardidlog')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
        }
        
        $query = "UPDATE dbpurchasing.t_pr_transaksi SET tanggal='$ptanggal', idtipe='$ptipeaju', "
                . " karyawanid='$pkaryawanid', icabangid='$pidcabang', jabatanid='$pjabatanid', "
                . " aktivitas='$pnotes', userid='$pcardidlog', pilihpo='Y' WHERE "
                . " idpr='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        if ($act=="input") {
            $pimgttd=$_POST['txtgambar'];
            $query = "update dbttd.t_pr_transaksi_ttd set gambar='$pimgttd' WHERE idpr='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
    
        
        $query = "UPDATE dbpurchasing.t_pr_transaksi SET atasan1='$pkdspv', atasan2='$pkddm', atasan3='$pkdsm', atasan4='$pkdgsm' WHERE idpr='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
        
        if ($pisitglspv==true) {
            $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan1=NOW() WHERE idpr='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }

        if ($pisitgldm==true) {
            $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan2=NOW() WHERE idpr='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }

        if ($pisitglsm==true) {
            $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan3=NOW() WHERE idpr='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }

        if ($pisitglgsm==true) {
            $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan4=NOW() WHERE idpr='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
    
    
        //ADMIN BR dan FINANCE OTC
        if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26") {

            $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan1=NOW() WHERE idpr='$kodenya' AND (IFNULL(tgl_atasan1,'')='' OR IFNULL(tgl_atasan1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') LIMIT 1";
            //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan2=NOW() WHERE idpr='$kodenya' AND (IFNULL(tgl_atasan2,'')='' OR IFNULL(tgl_atasan2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') LIMIT 1";
            //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan3=NOW() WHERE idpr='$kodenya' AND (IFNULL(tgl_atasan3,'')='' OR IFNULL(tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') LIMIT 1";
            //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }


        }
        
        $query = "DELETE from dbpurchasing.t_pr_transaksi_d WHERE idpr='$kodenya'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        
        $query = "INSERT INTO dbpurchasing.t_pr_transaksi_d (idpr, idbarang, namabarang, idbarang_d, spesifikasi1, jumlah, keterangan, harga, satuan)"
                . "SELECT '$kodenya' as idpr, idbarang, namabarang, idbarang_d, spesifikasi, jml, keterangan, harga, satuan FROM $tmp01";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    
        mysqli_close($cnmy);
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
        
    }
}

?>