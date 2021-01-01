<?php

    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
if ($module=='spgdatamastergaji' AND ($act=='simpanum' OR $act=='hapusum') )
{
    $ptgl = $_POST['u_tgl1'];
    $pbulan= date("Y-m", strtotime($ptgl));
    $pperiode= date("Y-m-d", strtotime($ptgl));
    $pjumlah = $_POST['txtrp_um'];
    $pid_input = $_POST['txt_idbr'];
    $jumlah_tag=(double)count($pjumlah)-1;
    for ($x=0; $x<=$jumlah_tag; $x++){
        $nobrinput="";
        if (isset($pid_input[$x]) AND isset($pjumlah[$x])) {
            $pn_id=$pid_input[$x];
            $pn_id = explode("_",$pn_id);
            $prp_um=$pjumlah[$x];
            if (empty($prp_um)) $prp_um="0";
            $prp_um=str_replace(",","", $prp_um);
            
            
            $pidzona="";
            $pidjbt="";
            if(isset($pn_id[0])) $pidzona=$pn_id[0];
            if(isset($pn_id[1])) $pidjbt=$pn_id[1];
            
            $query = "delete from dbmaster.t_spg_gaji_zona_jabatan WHERE DATE_FORMAT(bulan,'%Y-%m')='$pbulan' AND jabatid='$pidjbt' AND id_zona='$pidzona'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            if ($act=='simpanum') {
                $query = "INSERT INTO dbmaster.t_spg_gaji_zona_jabatan (bulan, id_zona, jabatid, umakan, userid)VALUES"
                        . "('$pperiode', '$pidzona', '$pidjbt', '$prp_um', '$_SESSION[IDCARD]')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
    
        }
    }
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
}
elseif ($module=='spgdatamastergaji' AND ($act=='simpantunjangan' OR $act=='hapustunjangan') )
{
 
    $ptgl = $_POST['u_tgl1'];
    $pbulan= date("Y-m", strtotime($ptgl));
    $pperiode= date("Y-m-d", strtotime($ptgl));
    $pjmlsewa = $_POST['txtrp_sw'];
    $pjmlpulsa = $_POST['txtrp_pulsa'];
    $pjmlbbm = $_POST['txtrp_bbm'];
    $pjmlparkir = $_POST['txtrp_parkir'];
    $pid_input = $_POST['txt_idbr'];
    $jumlah_tag=(double)count($pjmlsewa)-1;
    for ($x=0; $x<=$jumlah_tag; $x++){
        $nobrinput="";
        if (isset($pid_input[$x]) AND isset($pjmlsewa[$x])) {
            $pidjbt=$pid_input[$x];
            $prp_sw=$pjmlsewa[$x];
            $prp_pulsa=$pjmlpulsa[$x];
            $prp_bbm=$pjmlbbm[$x];
            $prp_parkir=$pjmlparkir[$x];
            
            
            if (empty($prp_sw)) $prp_sw="0";
            if (empty($prp_pulsa)) $prp_pulsa="0";
            if (empty($prp_bbm)) $prp_bbm="0";
            if (empty($prp_parkir)) $prp_parkir="0";
            
            $prp_sw=str_replace(",","", $prp_sw);
            $prp_pulsa=str_replace(",","", $prp_pulsa);
            $prp_bbm=str_replace(",","", $prp_bbm);
            $prp_parkir=str_replace(",","", $prp_parkir);
            
            $query = "delete from dbmaster.t_spg_gaji_jabatan WHERE DATE_FORMAT(bulan,'%Y-%m')='$pbulan' AND jabatid='$pidjbt'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            if ($act=='simpantunjangan') {
                $query = "INSERT INTO dbmaster.t_spg_gaji_jabatan (bulan, jabatid, sewakendaraan, pulsa, bbm, parkir, userid)VALUES"
                        . "('$pperiode', '$pidjbt', '$prp_sw', '$prp_pulsa', '$prp_bbm', '$prp_parkir', '$_SESSION[IDCARD]')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
    
        }
    }
    
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='spgdatamastergaji' AND ($act=='simpangp' OR $act=='hapusgp') )
{
    
    $ptgl = $_POST['u_tgl1'];
    $pbulan= date("Y-m", strtotime($ptgl));
    $pperiode= date("Y-m-d", strtotime($ptgl));
    $pjumlah = $_POST['txtrp_gp'];
    $pid_cab = $_POST['txt_idcab'];
    $pid_area = $_POST['txt_idarea'];
    $pid_zona = $_POST['cb_zona'];
    
    $jumlah_tag=(double)count($pjumlah)-1;
    for ($x=0; $x<=$jumlah_tag; $x++){
        $nobrinput="";
        if (isset($pid_cab[$x]) AND isset($pid_area[$x]) AND isset($pid_zona[$x]) AND isset($pjumlah[$x])) {
            $pn_idcab=$pid_cab[$x];
            $pn_idarea=$pid_area[$x];
            $pidzona=$pid_zona[$x];
            
            $prp_gp=$pjumlah[$x];
            if (empty($prp_gp)) $prp_gp="0";
            $prp_gp=str_replace(",","", $prp_gp);
            
            
            $query = "delete from dbmaster.t_spg_gaji_area_zona WHERE DATE_FORMAT(bulan,'%Y-%m')='$pbulan' AND icabangid='$pn_idcab' AND areaid='$pn_idarea'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            if ($act=='simpangp') {
                $query = "INSERT INTO dbmaster.t_spg_gaji_area_zona (bulan, icabangid, areaid, id_zona, gaji, userid)VALUES"
                        . "('$pperiode', '$pn_idcab', '$pn_idarea', '$pidzona', '$prp_gp', '$_SESSION[IDCARD]')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
    
        }
    }
    
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
    
    
?>

