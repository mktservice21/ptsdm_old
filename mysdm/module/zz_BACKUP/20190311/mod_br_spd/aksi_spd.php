<?php

    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    //$cnmy=$cnit;
    $dbname = "dbmaster";
    
// Hapus 
if ($module=='saldosuratdana' AND $act=='hapus')
{
    mysqli_query($cnmy, "update $dbname.t_suratdana_br set stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE idinput='$_GET[id]'");
    mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br1 WHERE idinput='$_GET[id]'");
    mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br_d WHERE idinput='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='saldosuratdana')
{
    
    $pdivisi=$_POST['cb_divisi'];
    $pjenis=$_POST['cb_jenis'];
    $pkode=$_POST['cb_kode'];
    $psubkode=$_POST['cb_kodesub'];
    $pnomor=$_POST['e_nomor'];
    //$ptgl = str_replace('/', '-', $_POST['e_tglberlaku']);
    $ptgl = $_POST['e_tglberlaku'];
    $periode1= date("Y-m-d", strtotime($ptgl));
    
    $pdivno=$_POST['e_nomordiv'];
    $pjumlah=str_replace(",","", $_POST['e_jmlusulan']);
    
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
    
    $periodef = date("Y-m-d", strtotime($ptgl));
    $periodet = date("Y-m-d", strtotime($ptgl));
	
    $pcoa="101-02-002";
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, nomor, tgl, nodivisi, jumlah, userid, coa4, lampiran, tglf, tglt, karyawanid)values"
                . "('$kodenya', '$pdivisi', '$pkode', '$psubkode', '$pnomor', '$periode1', '$pdivno', '$pjumlah', '$_SESSION[IDCARD]', '$pcoa', '$pjenis', '$periodef', '$periodet', '$_SESSION[IDCARD]')";
    }else{
        $query = "UPDATE $dbname.t_suratdana_br SET lampiran='$pjenis', pilih='Y', coa4='$pcoa', divisi='$pdivisi', kodeid='$pkode', "
                . " subkode='$psubkode', nomor='$pnomor', tgl='$periode1', nodivisi='$pdivno', jumlah='$pjumlah', userid='$_SESSION[IDCARD]', "
                . " tglf='$periodef', tglt='$periodet' WHERE "
                . " idinput='$kodenya'";
    }
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if (isset($_POST['e_chkpilih'])) {
        if ($_POST['e_chkpilih']=="N") {
            $query = "UPDATE $dbname.t_suratdana_br SET pilih='N' WHERE idinput='$kodenya'";
            mysqli_query($cnmy, $query);
        }
    }
    
    
    
    $query = "DELETE FROM $dbname.t_suratdana_br1 WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    $query = "DELETE FROM $dbname.t_suratdana_br_d WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    $query = "INSERT INTO $dbname.t_suratdana_br_d (idinput,divisi,jumlah)values('$kodenya', '$pdivisi', '$pjumlah')";
    mysqli_query($cnmy, $query);
    
    
    
    $purutan=1;
    $pkodeurutan=1;
    
    if ($pdivisi=="OTC") {
        
        $kodeinput="D";//KODE BR OTC
        
        $filterlampiran="";
        if (!empty($pjenis)) $filterlampiran = " and case when ifnull(lampiran,'N')='' then 'N' else lampiran end ='$pjenis' ";
        
        $query="SELECT DISTINCT ifnull(brOtcId,'') nobrid from hrd.br_otc where "
                . " brOtcId not in (SELECT DISTINCT ifnull(brOtcId,'') from hrd.br_otc_reject) AND "
                . " DATE_FORMAT(tglbr,'%Y-%m-%d') = '$periode1' $filterlampiran"
                . " AND brOtcId NOT IN (SELECT DISTINCT ifnull(bridinput,'') from dbmaster.t_suratdana_br1 WHERE kodeinput='D')";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($tr= mysqli_fetch_array($tampil)) {
                $nobrinput=$tr['nobrid'];
                //eksekusi input
                $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, urutan)VALUES"
                        . "('$kodenya', '$nobrinput', '$kodeinput', '$pkodeurutan')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                if ($purutan==30) {
                    $purutan=0;
                    $pkodeurutan++;
                }
                $purutan++;
            }
            
        }
        
    }
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
