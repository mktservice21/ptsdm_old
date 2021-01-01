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
    mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET stsnonaktif='Y', userid='$userid' WHERE nopol='$_GET[id]'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}

elseif ($module=='datakendaraan')
{
    $idlama=$_POST['e_idlama'];
    $kodenya=$_POST['e_id'];
    $pjenis=$_POST['e_jenis'];
    $pmerk=$_POST['e_merk'];
    $ptipe=$_POST['e_tipe'];
    $pstskendaraan=$_POST['e_ststkendaraan'];

    $date1 = str_replace('/', '-', $_POST['e_tgl']);
    $pp01 =  date("Y-m-d", strtotime($date1));
    
    //cari plat yang sudah ada
    $query = "select nopol from $dbname.t_kendaraan WHERE nopol='$kodenya' AND nopol<>'$idlama'";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    if ($ketemu>0) {
        echo "PLAT NOMOR $kodenya SUDAH ADA...!!!"; exit;
    }
    
    if ($act=='input')
    {
        mysqli_query($cnmy, "INSERT INTO $dbname.t_kendaraan (nopol, jenis, merk, tipe, tglbeli, userid, statuskendaraan)"
                . " VALUES ('$kodenya', '$pjenis', '$pmerk', '$ptipe', '$pp01', '$userid', '$pstskendaraan')");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    elseif ($act=='update')
    {
        mysqli_query($cnmy, "UPDATE $dbname.t_kendaraan SET nopol='$kodenya', jenis='$pjenis', "
                . " merk='$pmerk', tipe='$ptipe', tglbeli='$pp01', userid='$userid', statuskendaraan='$pstskendaraan' WHERE nopol='$idlama'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
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
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
}
    
?>

