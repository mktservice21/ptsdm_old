<?php

    session_start();
    if (!isset($_SESSION['IDCARD'])) {
        echo "Anda harus login ulang...";
        exit;
    }
    
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
if ($module=='bgtlimitkaskecilcabchc' AND ($act=='simpanum' OR $act=='hapusum') )
{
    $puserid=$_SESSION['IDCARD'];
    $pjumlah = $_POST['txtrp_um'];
    $pid_input = $_POST['txt_idbr'];
    $pcoa_input = $_POST['txt_coakd'];
    $jumlah_tag=(double)count($pjumlah)-1;
    for ($x=0; $x<=$jumlah_tag; $x++){
        $nobrinput="";
        if (isset($pid_input[$x]) AND isset($pjumlah[$x])) {
            $pn_id=$pid_input[$x];
            $prp_um=$pjumlah[$x];
            if (empty($prp_um)) $prp_um="0";
            $prp_um=str_replace(",","", $prp_um);
            
            $pidcoa="";
            if (isset($pcoa_input[$x])) $pidcoa=$pcoa_input[$x];
            if (!empty($pidcoa)) $pidcoa = str_replace("'", " ", $pidcoa);
            
            
            $query = "delete from dbmaster.t_uangmuka_kascabang WHERE icabangid_o='$pn_id'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            if ($act=='simpanum') {
                $query = "INSERT INTO dbmaster.t_uangmuka_kascabang (icabangid_o, jumlah, userid, coa)VALUES"
                        . "('$pn_id', '$prp_um', '$puserid', '$pidcoa')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
            
            
        }
    }
    
    mysqli_close($cnmy);
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
}else{
    if ($module=='bgtlimitkaskecilcabchc' AND $act=='update' )
    {
        
        $pidcabang=$_POST['e_id'];
        $pidcoa=$_POST['e_coaid'];
        $ptgl=$_POST['e_periode1'];
        $prpsaldoawal=$_POST['e_sldawal'];
        $prptambah=$_POST['e_tambahanrp'];
        $prppcm=$_POST['e_jumlah'];
        $prptotal=$_POST['e_total'];
        $pket=$_POST['e_aktivitas'];
        
        $ptanggal="0000-00-00";
        if (!empty($ptgl)) $ptanggal= date("Y-m-d", strtotime($ptgl));
        
        if (!empty($pket)) $pket = str_replace("'", " ", $pket);
        if (!empty($pidcoa)) $pidcoa = str_replace("'", " ", $pidcoa);
        
        if (empty($prpsaldoawal)) $prpsaldoawal=0;
        if (empty($prptambah)) $prptambah=0;
        if (empty($prppcm)) $prppcm=0;
        if (empty($prptotal)) $prptotal=0;
        
        $prpsaldoawal=str_replace(",","", $prpsaldoawal);
        $prptambah=str_replace(",","", $prptambah);
        $prppcm=str_replace(",","", $prppcm);
        $prptotal=str_replace(",","", $prptotal);
        
        if (!empty($pidcabang)) {
            $query = "delete from dbmaster.t_uangmuka_kascabang WHERE icabangid_o='$pidcabang' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            

            $query = "INSERT INTO dbmaster.t_uangmuka_kascabang (icabangid_o, pcm, saldoawal, jmltambahan, jumlah, userid, tgltambah, ket, coa)VALUES"
                    . "('$pidcabang', '$prppcm', '$prpsaldoawal', '$prptambah', '$prptotal', '$puserid', '$ptanggal', '$pket', '$pidcoa')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            mysqli_close($cnmy);

            header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
        }
        
        
    }
}

?>