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



    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    //$cnmy=$cnit;
    $dbname = "dbmaster";
  
// Hapus 
if ($module=='bgtpdkaskecilcabang' AND $act=='hapus')
{
    $puserid=$_SESSION['IDCARD'];
    $kodenya=$_GET['id'];
    
    mysqli_query($cnmy, "update $dbname.t_suratdana_br set stsnonaktif='Y', userid='$puserid' WHERE idinput='$kodenya' LIMIT 1");
    //mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br1 WHERE idinput='$kodenya'");
    //mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br_d WHERE idinput='$kodenya'");
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='bgtpdkaskecilcabang')
{
    
    if (empty($_SESSION['JMLRECSPD'])) $_SESSION['JMLRECSPD']=30;
    
    $pidgroup=$_SESSION['GROUP'];
    $userid=$_SESSION['IDCARD'];
    
    $pkode=$_POST['cb_kode'];
    $psubkode=$_POST['cb_kodesub'];
    
    $pdivisi="CAN";
    if ($pidgroup=="23" OR $pidgroup=="26") $pdivisi="OTC";
        
    $pnomor="";
    
    $ptgl = $_POST['e_tglberlaku'];
    $periode1= date("Y-m-d", strtotime($ptgl));
    
    $pdivno=$_POST['e_nomordiv'];
    $pjumlah=str_replace(",","", $_POST['e_jmlusulan']);
    
    
    
    $pidpembuat = $_POST['cb_karyawan'];
    
    $mytgl1 = $_POST['e_periode1'];
    $mytgl2 = $_POST['e_periode2'];
    
    $mper1= date("Y-m-d", strtotime($mytgl1));
    $mper2= date("Y-m-d", strtotime($mytgl2));
    
    $periodef=$mper1;
    $periodet=$mper2;

    
    $pjenis="";//lampiran
    $padvance="";//advance/klaim/belum ada kuitansi
    
    $ppertipe="";
    
    
    $pkodeperiode="";
    
    $kodenya="";
    
    
    
    if ($act=="input") {
        $sql=  mysqli_query($cnmy, "select MAX(idinput) as NOURUT from $dbname.t_suratdana_br");
        $ketemu=  mysqli_num_rows($sql);
        $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $kodenya=$urut;
        }else{
            $kodenya=$_POST['e_id'];
        }
    }else{
        $kodenya=$_POST['e_id'];
    }
    
    
    //echo "$periodef, $periodet <br/>$pkode, $psubkode <br/>$kodenya<br/>$pjenis<br/>$ppertipe<br/>$padvance<br/>$pjumlah<br/>"; exit;
    
    
    $pcoa="101-02-002";
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, nomor, tgl, nodivisi, jumlah, "
                . " userid, coa4, lampiran, tglf, tglt, kodeperiode, karyawanid, periodeby, jenis_rpt, jumlah2)values"
                . "('$kodenya', '$pdivisi', '$pkode', '$psubkode', '$pnomor', '$periode1', '$pdivno', '$pjumlah', "
                . " '$_SESSION[IDCARD]', '$pcoa', '$pjenis', '$periodef', '$periodet', '$pkodeperiode', '$pidpembuat', '$ppertipe', '$padvance', '$pjumlah_kb')";
    }else{
        $query = "UPDATE $dbname.t_suratdana_br SET lampiran='$pjenis', pilih='Y', coa4='$pcoa', divisi='$pdivisi', kodeid='$pkode', "
                . " subkode='$psubkode', karyawanid='$pidpembuat', nomor='$pnomor', tgl='$periode1', nodivisi='$pdivno', jumlah='$pjumlah', userid='$_SESSION[IDCARD]', "
                . " tglf='$periodef', tglt='$periodet', kodeperiode='$pkodeperiode', periodeby='$ppertipe', jenis_rpt='$padvance', jumlah2='$pjumlah_kb' WHERE "
                . " idinput='$kodenya'";
    }
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "DELETE FROM $dbname.t_suratdana_br_d WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    
    $query = "DELETE FROM $dbname.t_suratdana_br1 WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    
    
    $query = "INSERT INTO $dbname.t_suratdana_br_d (idinput,divisi,jumlah)values('$kodenya', '$pdivisi', '$pjumlah')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $kodeinput = "X";
    
    if (!empty($kodenya)){
        //save input ke table
        foreach ($_POST['chk_jml1'] as $no_brid) {
            if (!empty($no_brid)) {
                $pjmlinputpilih= $_POST['txt_jml'][$no_brid];
                if (empty($pjmlinputpilih)) $pjmlinputpilih=0;
                $pjmlinputpilih=str_replace(",","", $pjmlinputpilih);
                        
                //eksekusi input
                $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, amount)VALUES"
                        . " ('$kodenya', '$no_brid', '$kodeinput', '$pjmlinputpilih')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                        
            }
        }
    }
    
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
}
?>
