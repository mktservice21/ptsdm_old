<?php
    session_start();
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    $userid=$_SESSION['IDCARD'];
    
    
//HAPUS DATA
if ($module=='datakendaraan' AND $act=='hapus')
{
    mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET stsnonaktif='Y', userid='$userid' WHERE noid='$_GET[id]' LIMIT 1");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

elseif ($module=='datakendaraan')
{
    
    
    $kodenya=$_POST['e_id'];
    $plamanopol=$_POST['e_nopollama'];
    $pnopolisi=$_POST['e_nopolid'];
    $pjenis=$_POST['e_jenis'];
    $pmerk=$_POST['e_merk'];
    $ptipe=$_POST['e_tipe'];
    $pwarna=$_POST['e_warna'];
    $pstskendaraan=$_POST['e_ststkendaraan'];
    
    $plstjnsasuransi=$_POST['lst_jenisasuransi'];
    $plstnmasuransi=$_POST['lst_nmasuransi'];
    $pasuransinopolis=$_POST['lst_nopolisasuransi'];
    
    $pasuransitgl01=$_POST['e_tglper01'];
    $pasuransitgl02=$_POST['e_tglper02'];
    
    $pperiodepolis01="0000-00-00";
    $pperiodepolis02="0000-00-00";
    
    if (!empty($pasuransitgl01)) {
        $pasuransitgl01_ = str_replace('/', '-', $pasuransitgl01);
        $pperiodepolis01 =  date("Y-m-d", strtotime($pasuransitgl01_));
    }
    
    if (!empty($pasuransitgl02)) {
        $pasuransitgl02_ = str_replace('/', '-', $pasuransitgl02);
        $pperiodepolis02 =  date("Y-m-d", strtotime($pasuransitgl02_));
    }
    
    
    $pnorangka=$_POST['e_norangka'];
    $pnomesin=$_POST['e_nomesin'];
    $ptglst=$_POST['e_tglstnk'];
    
    $ptgltempostnk="";
    if (!empty($ptglst)) {
        $ptglst_ = str_replace('/', '-', $ptglst);
        $ptgltempostnk =  date("Y-m-d", strtotime($ptglst_));
    }
    
    //tgl beli
    $date1 = str_replace('/', '-', $_POST['e_tgl']);
    $pp01 =  date("Y-m-d", strtotime($date1));
    $pthnbeli =  date("Y", strtotime($date1));
    
    
    //pemakai
    $idpemakai=$_POST['e_idpakai'];
    $ppemakai=$_POST['e_pemakai'];
    $pdivpakai=$_POST['e_divisiid'];
    $pcabpakai=$_POST['e_cabid'];
    $dateawal = str_replace('/', '-', $_POST['e_tglawal']);
    $ppawal01 =  date("Y-m-d", strtotime($dateawal));
    $ppakhir="0000-00-00";
    if (isset($_POST['chktgl'])) {
        if (!empty($_POST['chktgl'])) {
            $dateakhir = str_replace('/', '-', $_POST['e_tglakhir']);
            $ppakhir =  date("Y-m-d", strtotime($dateakhir));
        }
    }
    
    
    if (!empty($pnopolisi)) $pnopolisi = str_replace("'", " ", $pnopolisi);
    if (!empty($pnorangka)) $pnorangka = str_replace("'", " ", $pnorangka);
    if (!empty($pnomesin)) $pnomesin = str_replace("'", " ", $pnomesin);
    if (!empty($pjenis)) $pjenis = str_replace("'", " ", $pjenis);
    if (!empty($pmerk)) $pmerk = str_replace("'", " ", $pmerk);
    if (!empty($ptipe)) $ptipe = str_replace("'", " ", $ptipe);
    if (!empty($pwarna)) $pwarna = str_replace("'", " ", $pwarna);
    if (!empty($plstjnsasuransi)) $plstjnsasuransi = str_replace("'", " ", $plstjnsasuransi);
    if (!empty($plstnmasuransi)) $plstnmasuransi = str_replace("'", " ", $plstnmasuransi);
    if (!empty($pasuransinopolis)) $pasuransinopolis = str_replace("'", " ", $pasuransinopolis);
    
    
    
    /*
    echo "ID : $kodenya, nopol : $pnopolisi, nopol lama : $plamanopol, jenis : $pjenis, merk : $pmerk, tipe : $ptipe, warna : $pwarna<br/>";
    echo "tgl beli : $pp01, norangka : $pnorangka, nomesin : $pnomesin <br/>";
    echo "tgl stnk : $ptgltempostnk, sts kendaraan : $pstskendaraan<br/>";
    echo "id pakai : $idpemakai, pemakain / kry : $ppemakai, divisi : $pdivpakai, cabang : $pcabpakai <br/>";
    echo "tgl Mulai : $ppawal01, tgl akhir : $ppakhir<br/> ";
    echo "jnis asuransi : $plstjnsasuransi, nama asuransi : $plstnmasuransi, nopolis : $pasuransinopolis<br/> ";
    echo "Periode asuransi : $pperiodepolis01 s/d. $pperiodepolis02<br/> ";
    
    mysqli_close($cnmy); exit;
    */
    
    //cari plat yang sudah ada
    $query = "select nopol from $dbname.t_kendaraan WHERE nopol='$pnopolisi' AND nopol<>'$plamanopol'";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    if ($ketemu>0) {
        echo "PLAT NOMOR $pnopolisi SUDAH ADA...!!!"; mysqli_close($cnmy); exit;
    }
    
    
    if ($act=='input')
    {
        mysqli_query($cnmy, "INSERT INTO $dbname.t_kendaraan (nopol, jenis, merk, tipe, tglbeli, userid, statuskendaraan, warna, tahun)"
                . " VALUES ('$pnopolisi', '$pjenis', '$pmerk', '$ptipe', '$pp01', '$userid', '$pstskendaraan', '$pwarna', '$pthnbeli')");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $kodenya = mysqli_insert_id($cnmy);
        
    }
    elseif ($act=='update')
    {
        
    }
    
    if (!empty($kodenya)) {
        
        mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET tgltempostnk=NULL, norangka='$pnorangka', nomesin='$pnomesin', nopol='$pnopolisi', jenis='$pjenis', "
                . " merk='$pmerk', tipe='$ptipe', tglbeli='$pp01', userid='$userid', "
                . " statuskendaraan='$pstskendaraan', warna='$pwarna', "
                . " tahun='$pthnbeli', jenis_asuransi='$plstjnsasuransi', "
                . " nama_asuransi='$plstnmasuransi', "
                . " nopolis_asuransi='$pasuransinopolis' WHERE noid='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        if (!empty($ptgltempostnk)) {
            mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET tgltempostnk='$ptgltempostnk' WHERE noid='$kodenya' LIMIT 1");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
    
        mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET polis_periode1='$pperiodepolis01', polis_periode2='$pperiodepolis02' WHERE noid='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
    }
    
    
    
    if (!empty($ppemakai)) {
        
        if ($act=='update' AND ($pnopolisi<>$plamanopol)) {
            mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan_pemakai SET stsnonaktif='Y', userid='$userid' WHERE nourut='$idpemakai'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        
        $bolehinput="";
        $query = "select * from $dbname.t_kendaraan_pemakai WHERE nourut = '$idpemakai' ";
        $tampil = mysqli_query($cnmy, $query);
        $ketemu = mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $zz= mysqli_fetch_array($tampil);
            if ($zz['karyawanid'] !=$ppemakai) {
                mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan_pemakai SET stsnonaktif='Y', userid='$userid' WHERE nourut = '$idpemakai'");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $bolehinput="boleh";
            }
        }
        
        
        if ($act=='input' OR empty($idpemakai) OR !empty($bolehinput))
        {
            mysqli_query($cnmy, "INSERT INTO $dbname.t_kendaraan_pemakai (noid, nopol, tgl, karyawanid, tglawal, userid, tglakhir, icabangid, divisi)"
                    . " VALUES ('$kodenya', '$pnopolisi', CURRENT_DATE(), '$ppemakai', '$ppawal01', '$userid', '$ppakhir', '$pcabpakai', '$pdivpakai')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        elseif ($act=='update')
        {
            mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan_pemakai SET nopol='$pnopolisi', karyawanid='$ppemakai', "
                    . " tglawal='$ppawal01', tglakhir='$ppakhir', userid='$userid', icabangid='$pcabpakai', divisi='$pdivpakai' WHERE nourut='$idpemakai'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        
    }
    
    
    
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
    exit;
    
    
    //=========================================================================================
    
    
    
    /*
    //cari plat yang sudah ada
    $query = "select nopol from $dbname.t_kendaraan WHERE nopol='$kodenya' AND nopol<>'$idlama'";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    if ($ketemu>0) {
        echo "PLAT NOMOR $kodenya SUDAH ADA...!!!"; exit;
    }
    */
    if ($act=='input')
    {
        /*
        mysqli_query($cnmy, "INSERT INTO $dbname.t_kendaraan (nopol, jenis, merk, tipe, tglbeli, userid, statuskendaraan, warna)"
                . " VALUES ('$kodenya', '$pjenis', '$pmerk', '$ptipe', '$pp01', '$userid', '$pstskendaraan', '$pwarna')");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET tgltempostnk=NULL, norangka='$pnorangka', nomesin='$pnomesin' WHERE nopol='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        if (!empty($ptgltempostnk)) {
            mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET tgltempostnk='$ptgltempostnk' WHERE nopol='$kodenya' LIMIT 1");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        */
        
    }
    elseif ($act=='update')
    {
        /*
        mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET tgltempostnk=NULL, norangka='$pnorangka', nomesin='$pnomesin', nopol='$kodenya', jenis='$pjenis', "
                . " merk='$pmerk', tipe='$ptipe', tglbeli='$pp01', userid='$userid', statuskendaraan='$pstskendaraan', warna='$pwarna' WHERE nopol='$idlama' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        if (!empty($ptgltempostnk)) {
            mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET tgltempostnk='$ptgltempostnk' WHERE nopol='$idlama' LIMIT 1");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        */
    }
    
    
    /*
    $idpemakai=$_POST['e_idpakai'];
    $ppemakai=$_POST['e_pemakai'];
    $dateawal = str_replace('/', '-', $_POST['e_tglawal']);
    $ppawal01 =  date("Y-m-d", strtotime($dateawal));
    $ppakhir="0000-00-00";
    if (isset($_POST['chktgl'])) {
        if (!empty($_POST['chktgl'])) {
            $dateakhir = str_replace('/', '-', $_POST['e_tglakhir']);
            $ppakhir =  date("Y-m-d", strtotime($dateakhir));
        }
    }
    
    $cabangpakai="";
    if (!empty($ppemakai)) {
        $cabangpakai= getfieldcnit("select distinct icabangid as lcfields from dbmaster.v_penempatan_all where karyawanid='$ppemakai' and ifnull(icabangid,'') <> ''");
        
        if ($act=='update' AND ($kodenya<>$idlama)) {
            mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan_pemakai SET stsnonaktif='Y', userid='$userid' WHERE nourut='$idpemakai'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        
        $bolehinput="";
        $query = "select * from $dbname.t_kendaraan_pemakai WHERE nourut = '$idpemakai' ";
        $tampil = mysqli_query($cnmy, $query);
        $ketemu = mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $zz= mysqli_fetch_array($tampil);
            if ($zz['karyawanid'] !=$ppemakai) {
                mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan_pemakai SET stsnonaktif='Y', userid='$userid' WHERE nourut = '$idpemakai'");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $bolehinput="boleh";
            }
        }
        
        
        if ($act=='input' OR empty($idpemakai) OR !empty($bolehinput))
        {
            mysqli_query($cnmy, "INSERT INTO $dbname.t_kendaraan_pemakai (nopol, tgl, karyawanid, tglawal, userid, tglakhir, icabangid)"
                    . " VALUES ('$kodenya', CURRENT_DATE(), '$ppemakai', '$ppawal01', '$userid', '$ppakhir', '$cabangpakai')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        elseif ($act=='update')
        {
            mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan_pemakai SET nopol='$kodenya', karyawanid='$ppemakai', "
                    . " tglawal='$ppawal01', tglakhir='$ppakhir', userid='$userid', icabangid='$cabangpakai' WHERE nourut='$idpemakai'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        
    }
    */
    
    //header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
}
    
?>

