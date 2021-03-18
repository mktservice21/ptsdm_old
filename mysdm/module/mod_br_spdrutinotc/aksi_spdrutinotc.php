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
if ($module=='spdrutinotc' AND $act=='hapus')
{   
    mysqli_query($cnmy, "update $dbname.t_suratdana_br set stsnonaktif='Y', userid='$_SESSION[IDCARD]' WHERE idinput='$_GET[id]'");
    //mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br1 WHERE idinput='$_GET[id]'");
    //mysqli_query($cnmy, "DELETE FROm $dbname.t_suratdana_br_d WHERE idinput='$_GET[id]'");
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='spdrutinotc')
{
    if (empty($_SESSION['JMLRECSPD'])) $_SESSION['JMLRECSPD']=30;
    
    $pkode=$_POST['cb_kode'];
    $psubkode=$_POST['cb_kodesub'];
    
    $pmystsyginput="";
    
    
    $ppertipe="";
    $pdivisi=$_POST['cb_divisi'];
    $ppilihdari=$_POST['cb_daripilihan'];
    $pjenis="";
    
    $padvance = "A";
    
    $pnomor=$_POST['e_nomor'];
    //$ptgl = str_replace('/', '-', $_POST['e_tglberlaku']);
    $ptgl = $_POST['e_tglberlaku'];
    $periode1= date("Y-m-d", strtotime($ptgl));
    
    
    $ptgl_pilihca1 = $_POST['e_periode1'];
    $periode_ca1= date("Y-m-d", strtotime($ptgl_pilihca1));
    
    $ptgl_pilihca2 = $_POST['e_periode2'];
    $periode_ca2= date("Y-m-d", strtotime($ptgl_pilihca2));
    
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
    

    
    $pmytgl = $_POST['e_periode'];
    $pmytg2 = $_POST['e_periode'];
    
    $periodef = date("Y-m-01", strtotime($pmytgl));
    $periodet = date("Y-m-01", strtotime($pmytg2));
    
    $thnblninput= date("Ym", strtotime($pmytgl));
    
    $prtpsby="";
    $pkodeperiode=1;
    
    if ($psubkode=="36") {
        $periodef=$periode_ca1;
        $periodet=$periode_ca2;
    }
    //echo "$periodef, $periodet <br/>$pkode, $psubkode <br/>$kodenya<br/>$pjenis<br/>$ppertipe<br/>jns : $padvance<br/>sby : $prtpsby<br/>ID BR : <br/>PER CA : $periode_ca1 - $periode_ca2"; exit;
    

    
    $pcoa="101-02-002";
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, nomor, tgl, nodivisi, jumlah, "
                . " userid, coa4, lampiran, tglf, tglt, kodeperiode, sts, karyawanid, periodeby, jenis_rpt, keterangan)values"
                . "('$kodenya', '$pdivisi', '$pkode', '$psubkode', '$pnomor', '$periode1', '$pdivno', '$pjumlah', "
                . " '$_SESSION[IDCARD]', '$pcoa', '$pjenis', '$periodef', '$periodet', '$pkodeperiode', '$pstspilihan', '$_SESSION[IDCARD]', '$ppertipe', '$padvance', '$ppilihdari')";
    }else{
        $query = "UPDATE $dbname.t_suratdana_br SET lampiran='$pjenis', pilih='Y', coa4='$pcoa', divisi='$pdivisi', kodeid='$pkode', "
                . " subkode='$psubkode', nomor='$pnomor', tgl='$periode1', nodivisi='$pdivno', jumlah='$pjumlah', userid='$_SESSION[IDCARD]', "
                . " tglf='$periodef', tglt='$periodet', kodeperiode='$pkodeperiode', sts='$pstspilihan', periodeby='$ppertipe', jenis_rpt='$padvance', keterangan='$ppilihdari' WHERE "
                . " idinput='$kodenya' LIMIT 1";
    }
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
   
    
    $query = "DELETE FROM $dbname.t_suratdana_br_d WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    
    $query = "INSERT INTO $dbname.t_suratdana_br_d (idinput,divisi,jumlah)values('$kodenya', '$pdivisi', '$pjumlah')";
    mysqli_query($cnmy, $query);
    
    
    $query = "DELETE FROM $dbname.t_suratdana_br1 WHERE idinput='$kodenya'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    /*
    $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput,urutan,amount)values('$kodenya', 1, '$pjumlah')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    */
    
    

    
    
    if ($psubkode=="36") {
        
        
        $query = "UPDATE $dbname.t_suratdana_br SET pilih='N' WHERE idinput='$kodenya'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $purutan=1;
        $pkodeurutan=1;
        $kodeinput="O";//CA RUTIN
        foreach ($_POST['chk_idbr'] as $nobrinput) {
            if (!empty($nobrinput)) {
                $namount=0;
                if (isset($_POST['txt_jml'][$nobrinput])) {
                    $namount=$_POST['txt_jml'][$nobrinput];
                    $namount=str_replace(",","", $namount);
                }
                //echo "$nobrinput = $namount<br/>";

                //eksekusi input
                $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, urutan, amount)VALUES"
                        . "('$kodenya', '$nobrinput', '$kodeinput', '$pkodeurutan', '$namount')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
        }
        
        
    }else{
        
        $purutan=1;
        $pkodeurutan=1;

        $kodeinput="M";//RUTIN/LK BR OTC
        if ((double)$psubkode==3) $kodeinput="N";//RUTIN/LK BR OTC

        $pkodepilih=" AND kode=2";
        if ((double)$psubkode==3) $pkodepilih=" AND kode=1";
        
        if ($ppilihdari=="CA" AND (double)$psubkode==21) {
            
            $query = "UPDATE $dbname.t_suratdana_br SET pilih='N', jenis_rpt='C', jumlah3=jumlah WHERE idinput='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

            $query="SELECT DISTINCT idca nobrid, sum(jumlah) jumlah from dbmaster.t_ca0 where "
                    . " divisi='$pdivisi' AND IFNULL(stsnonaktif,'')<>'Y' and DATE_FORMAT(bulan,'%Y%m')='$thnblninput' "
                    . " GROUP BY 1 ORDER BY 1";
        }else{
            $query="SELECT DISTINCT idrutin nobrid, sum(jumlah) jumlah from dbmaster.t_brrutin0 where "
                    . " divisi='$pdivisi' AND IFNULL(stsnonaktif,'')<>'Y' $pkodepilih and DATE_FORMAT(bulan,'%Y%m')='$thnblninput' "
                    . " GROUP BY 1 ORDER BY 1";
        }
        //echo $query;
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($tr= mysqli_fetch_array($tampil)) {
                $nobrinput=$tr['nobrid'];
                $namount=$tr['jumlah'];
                if (empty($namount)) $namount = "0";

                //eksekusi input
                $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, urutan, amount)VALUES"
                        . "('$kodenya', '$nobrinput', '$kodeinput', '$pkodeurutan', '$namount')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                if ($purutan==(INT)$_SESSION['JMLRECSPD']) {
                    $purutan=0;
                    $pkodeurutan++;
                }
                $purutan++;
            }

        }
        
    }
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
?>
