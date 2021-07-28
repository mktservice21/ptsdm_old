<?php
session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
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


if ($module=='brudcccabang')
{
    if ($act=="hapus") {
        
        if (empty($puserid)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        include "../../../config/koneksimysqli.php";
        $pkodenya=$_GET['id'];
        
        //$query = "UPDATE  SET stsnonaktif='Y' WHERE brid='$pkodenya' LIMIT 1";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
        mysqli_close($cnmy);
        //header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=sudahsimpan');
        
    }elseif ($act=="input" OR $act=="update") {
        
        $pidjabatan=$_POST['e_idjbt'];
        $puseridlog=$_POST['e_idinputuser'];
        $pcardidlog=$_POST['e_idcarduser'];
        if (empty($pcardidlog)) $pcardidlog=$pidcard;
        if (empty($puserid)) $puserid=$puseridlog;
        if (empty($pidcard)) $pidcard=$pcardidlog;
        
        if (empty($pcardidlog)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        $now=date("mdYhis");
        $tmp00 =" dbtemp.tmpsimpandcccab00_".$puserid."_$now ";
        
        include "../../../config/koneksimysqli_ms.php";
        
        $pgroupidinput=0;
        $kodenya=$_POST['e_id'];
        $ptglinput=$_POST['e_tglberlaku'];
        $pjenis=$_POST['cb_jenis'];
        $pdivisi=$_POST['cb_divisi'];
        $pcabang=$_POST['cb_cabang'];
        $parea=$_POST['cb_area'];
        $pdoktid=$_POST['cb_dokt'];
        $plokprak=$_POST['cb_outlet'];
        $pjmlusul=$_POST['e_jmlusulan'];
        $pjenisreal=$_POST['rb_jenisreal'];
        $pnmreal=$_POST['e_nmrealasi'];
        $pbankid=$_POST['cb_bankreal'];
        $pbanknama=$_POST['e_nmbankreal'];
        $pnorekening=$_POST['e_norekbankreal'];
        
        $pkodeakun="";
        $pkodecoa="";
        if ($pdivisi=="EAGLE") {
            $pkodeakun="700-02-03";
            $pkodecoa="701-02";
        }elseif ($pdivisi=="PEACO") {
            $pkodeakun="700-04-03";
            $pkodecoa="703-02";
        }elseif ($pdivisi=="PIGEO") {
            $pkodeakun="700-01-03";
            $pkodecoa="702-02";
        }
        
        
        $pttgl = str_replace('/', '-', $ptglinput);
        $ptanggal= date("Y-m-d", strtotime($pttgl));
        
        if (!empty($pnmreal)) $pnmreal = str_replace("'", " ", $pnmreal);
        if (!empty($pbanknama)) $pbanknama = str_replace("'", " ", $pbanknama);
        if (!empty($pnorekening)) $pnorekening = str_replace("'", " ", $pnorekening);
        
        $pccyid="IDR";
        
        $pkdspv=$_POST['e_kdspv'];
        $pkddm=$_POST['e_kddm'];
        $pkdsm=$_POST['e_kdsm'];
        $pkdgsm=$_POST['e_kdgsm'];
        
        $pisitglspv=false;
        $pisitgldm=false;
        $pisitglsm=false;
        $pisitglgsm=false;

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
        
        //echo "$kodenya : $pkodeakun, $pkodecoa, $ptanggal, $pjenis, $pdivisi, $pcabang, $parea, $pdoktid, $plokprak, $pjmlusul, $pjenisreal, $pnmreal, $pbankid, $pbanknama, $pnorekening<br/>";
        
        unset($pinsert_data_detail);//kosongkan array
        $psimpandata=false;
        foreach ($_POST['chk_kodeid'] as $piddata) {
            if (empty($piddata)) {
                //continue;
            }
            
            $pnareaid=$_POST['e_txtareaid'][$piddata];
            $pndoktid=$_POST['e_txtdoktid'][$piddata];
            $pnlokprak=$_POST['e_txtotletid'][$piddata];
            $pnjumlah=$_POST['e_txtrp'][$piddata];
            $pnket=$_POST['e_txtket'][$piddata];
            
            if (empty($pnjumlah)) $pnjumlah=0;
            $pnjumlah=str_replace(",","", $pnjumlah);
            
            if (!empty($pnket)) $pnket = str_replace("'", " ", $pnket);
            
            //echo "$pnareaid, $pndoktid, $pnlokprak, $pnjumlah, $pnket<br/>";
            if ((DOUBLE)$pnjumlah<>0) {
                $pinsert_data_detail[] = "('$ptanggal', '$pjenis', '$pdivisi', '$pcabang', "
                        . " '$pnareaid', '$pndoktid', '$pnlokprak', "
                        . " '$pccyid', '$pnjumlah', '$pnket', "
                        . " '$pjenisreal', '$pnmreal', '$pbankid', '$pbanknama', '$pnorekening', "
                        . " '$pkodeakun', '$pkodecoa')";

                $psimpandata=true;
            }
            
        }
        
        if ($psimpandata==true) {
            
            $query = "CREATE TEMPORARY TABLE $tmp00 ("
                    . " igroup INT(10), tanggal date, jenis_br varchar(10), divprodid varchar(5), icabangid varchar(10), "
                    . " areaid varchar(10), iddokter INT(10), idpraktek INT(10), ccyid varchar(10), jumlah DECIMAL(20,2), "
                    . " keterangan VARCHAR(500), "
                    . " jenis_realisasi INT(4), nama_realisasi Varchar(100), bank varchar(20), nama_bank varchar(100), norek varchar(100), "
                    . " kodeid varchar(100), coa varchar(100), gambar text, "
                    . " atasan1 varchar(10), tgl_atasan1 datetime, atasan2 varchar(10), tgl_atasan2 datetime, "
                    . " atasan3 varchar(10), tgl_atasan3 datetime, atasan4 varchar(10), tgl_atasan4 datetime, "
                    . " atasan5 varchar(10), tgl_atasan5 datetime "
                    . ")";
            mysqli_query($cnms, $query); 
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) {  goto errorsimpan; }
            
            
            $query = "INSERT INTO $tmp00 (tanggal, jenis_br, divprodid, icabangid, areaid, iddokter, idpraktek, "
                    . " ccyid, jumlah, keterangan, jenis_realisasi, nama_realisasi, bank, nama_bank, norek, "
                    . " kodeid, coa) VALUES ".implode(', ', $pinsert_data_detail);
            mysqli_query($cnms, $query);
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) {  goto errorsimpan; }
            
            
            if ($act=="input") {
                $pimgttd=$_POST['txtgambar'];
                $query = "update $tmp00 set gambar='$pimgttd'";
                mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) {  goto errorsimpan; }
            }
            
            
            
            $query = "UPDATE $tmp00 SET atasan1='$pkdspv', atasan2='$pkddm', atasan3='$pkdsm', atasan4='$pkdgsm'";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) {  goto errorsimpan; }

            if ($pisitglspv==true) {
                $query = "UPDATE $tmp00 SET tgl_atasan1=NOW()";
                mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) {  goto errorsimpan; }
            }

            if ($pisitgldm==true) {
                $query = "UPDATE $tmp00 SET tgl_atasan2=NOW()";
                mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto errorsimpan;  }
            }

            if ($pisitglsm==true) {
                $query = "UPDATE $tmp00 SET tgl_atasan3=NOW()";
                mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto errorsimpan;  }
            }

            if ($pisitglgsm==true) {
                $query = "UPDATE $tmp00 SET tgl_atasan4=NOW()";
                mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto errorsimpan;  }
            }
            
            if ($pidjabatan=="05" OR $pidjabatan=="22" OR $pidjabatan=="06") {
                $query = "UPDATE $tmp00 SET tgl_atasan1=NOW(), tgl_atasan2=NOW(), tgl_atasan3=NOW(), tgl_atasan4=NOW(), atasan5='$pidatasan5'";
                mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto errorsimpan;  }
            }
            
            
            if ($act=="input") {
                $query = "select max(igroup) as igroup from ms2.br";
                $tampil= mysqli_query($cnms, $query);
                $nrow= mysqli_fetch_array($tampil);
                $pgroupidinput=$nrow['igroup'];
                if (empty($pgroupidinput)) $pgroupidinput=0;
                $pgroupidinput++;   
            }
            
            $query = "UPDATE $tmp00 SET igroup='$pgroupidinput'";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto errorsimpan;  }
            
            
            $query = "INSERT INTO ms2.br (jenis_br, icabangid, areaid, tanggal, iddokter, idpraktek, divprodid, kode, COA4, "
                    . " keterangan, ccyId, jumlah, jenis_realisasi, nama_realisasi, bank, norek, "
                    . " igroup, "
                    . " approvedby_dm, approveddate_dm, approvedby_sm, approveddate_sm, "
                    . " approvedby_gsm, approveddate_gsm, "
                    . " createdby, createddate) SELECT "
                    . " jenis_br, icabangid, areaid, tanggal, iddokter, idpraktek, divprodid, kodeid, coa, "
                    . " keterangan, ccyid, jumlah, jenis_realisasi, nama_realisasi, bank, norek, "
                    . " igroup, "
                    . " atasan2, tgl_atasan2, atasan3, tgl_atasan3, "
                    . " atasan4, tgl_atasan4, "
                    . " '$pidcard' as createdby, NOW() as createddate FROM $tmp00";
            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { goto errorsimpan;  }
            
        }
        
        
        
        mysqli_query($cnms, "DROP TEMPORARY TABLE IF EXISTS $tmp00");
        mysqli_close($cnms);
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasil');
        exit;
        
        
        errorsimpan:
            echo $erropesan;
            mysqli_query($cnms, "DROP TEMPORARY TABLE IF EXISTS $tmp00");
            mysqli_close($cnms);
            //header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=error&iderror='.$erropesan);
            exit; 
            
        
    }
}

?>