<?php
    session_start();
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    $tglaju=$_POST['e_tglpengajuan'];
    $ptglpengajuan= date("Y-m-d", strtotime($tglaju));
    
    $date1=$_POST['e_periodepilih'];
    $bulan= date("Ym", strtotime($date1));
    $bulan_input= date("Y-m-d", strtotime($date1));
    
    $pidcabang=$_POST['e_cabangpilih'];
    $ptipests=$_POST['cb_tipests'];
    
    
    $date2=$_POST['e_periodepilih2'];
    $bulaninsentif= date("Ym", strtotime($date2));
    $pperiodeinct= date("Y-m-d", strtotime($date2));

    
    $userid=$_SESSION['IDCARD'];    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSPGHR01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSPGHR02_".$userid."_$now ";
    
    
    $query = "select idbrspg, periode, id_spg, icabangid, alokid, "
            . " areaid, id_zona, jabatid, kodeid, qty, rp, rptotal, "
            . " rptotal as insentif, periode periode_insentif, CAST('' as CHAR(1)) as sts, rptotal total, periode tglpengajuan, rptotal as insentif_tambahan from "
            . " dbmaster.t_spg_gaji_br1 WHERE idbrspg='XYZASASSDD' LIMIT 1";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { goto hapusdata; }//echo $erropesan; 
    
    mysqli_query($cnmy, "DELETE FROM $tmp01");
    
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $pidbrspg=$_POST['txtbridspg'][$nobrinput];
            $pidcabang=$_POST['txtidcabang'][$nobrinput];
            $pidalok=$_POST['txtalokid'][$nobrinput];
            $pidarea=$_POST['txtareaid'][$nobrinput];
            $pidzona=$_POST['txtzonaid'][$nobrinput];
            $pidjbt=$_POST['txtjabatid'][$nobrinput];
            
            $ptothr_jml=$_POST['txthrjml'][$nobrinput];
            
            $ptotal_inc=$_POST['txtinsentif'][$nobrinput];//01
            $ptotal_gp=$_POST['txtgp'][$nobrinput];//02
            $ptotal_makan=$_POST['txttmakan'][$nobrinput];//03
            $ptotal_sewa=$_POST['txtsewa'][$nobrinput];//04
            $ptotal_pulsa=$_POST['txtpulsa'][$nobrinput];//05
            $ptotal_parkir=$_POST['txtparkir'][$nobrinput];//06
            $ptotal_incbot=$_POST['txtincbot'][$nobrinput];//07
            $ptotal_bbm=$_POST['txtbbm'][$nobrinput];//08
            
            $pjml_makan=$_POST['txtmakan'][$nobrinput];
            $ptotal_spg=$_POST['txttotal'][$nobrinput];
            
            $ptotal_inc=str_replace(",","", $ptotal_inc);
            $ptotal_gp=str_replace(",","", $ptotal_gp);
            $ptotal_makan=str_replace(",","", $ptotal_makan);
            $ptotal_sewa=str_replace(",","", $ptotal_sewa);
            $ptotal_pulsa=str_replace(",","", $ptotal_pulsa);
            $ptotal_parkir=str_replace(",","", $ptotal_parkir);
            $ptotal_incbot=str_replace(",","", $ptotal_incbot);
            $ptotal_bbm=str_replace(",","", $ptotal_bbm);
            
            $pjml_makan=str_replace(",","", $pjml_makan);
            $ptotal_spg=str_replace(",","", $ptotal_spg);
            
            
            if ((double)$ptothr_jml==0) {
                $ptotal_gp=0;
                $ptotal_makan=0;
                $ptotal_sewa=0;
                $ptotal_pulsa=0;
                $ptotal_parkir=0;
                $ptotal_bbm=0;
            }
            
            //echo "idbr : $pidbrspg, $pidalok - $pidarea  - $pidzona - $pidjbt, $bulan _ $nobrinput, $pidcabang, INC BOT : $ptotal_incbot, TOT : $ptotal_spg<br/>";
            
            //$nobrinput = idspg
            for($nx=1;$nx<=8;$nx++) {
                $nx_kodeid=""; $nx_qty=""; $nx_rp=""; $nx_rptot="";
                
                if ((double)$nx==1) {
                    $nx_kodeid="01"; $nx_qty="1"; $nx_rp=$ptotal_inc; $nx_rptot=$ptotal_inc;
                }elseif ((double)$nx==2) {
                    $nx_kodeid="02"; $nx_qty="1"; $nx_rp=$ptotal_gp; $nx_rptot=$ptotal_gp;
                }elseif ((double)$nx==3) {
                    $nx_kodeid="03"; $nx_qty=$ptothr_jml; $nx_rp=$pjml_makan; $nx_rptot=$ptotal_makan;
                }elseif ((double)$nx==4) {
                    $nx_kodeid="04"; $nx_qty="1"; $nx_rp=$ptotal_sewa; $nx_rptot=$ptotal_sewa;
                }elseif ((double)$nx==5) {
                    $nx_kodeid="05"; $nx_qty="1"; $nx_rp=$ptotal_pulsa; $nx_rptot=$ptotal_pulsa;
                }elseif ((double)$nx==6) {
                    $nx_kodeid="06"; $nx_qty="1"; $nx_rp=$ptotal_parkir; $nx_rptot=$ptotal_parkir;
                }elseif ((double)$nx==7) {
                    $nx_kodeid="07"; $nx_qty="1"; $nx_rp=$ptotal_incbot; $nx_rptot=$ptotal_incbot;
                }elseif ((double)$nx==8) {
                    $nx_kodeid="08"; $nx_qty="1"; $nx_rp=$ptotal_bbm; $nx_rptot=$ptotal_bbm;
                    
                }
                
                $query = "INSERT INTO $tmp01 (idbrspg, periode, id_spg, icabangid, alokid, areaid, id_zona, jabatid, "
                        . "kodeid, qty, rp, rptotal, "
                        . "insentif, periode_insentif, sts, total, tglpengajuan, insentif_tambahan)VALUES "
                        . "('$pidbrspg', '$bulan_input', '$nobrinput', '$pidcabang', '$pidalok', '$pidarea', '$pidzona', '$pidjbt', "
                        . "'$nx_kodeid', '$nx_qty', '$nx_rp', '$nx_rptot', "
                        . "'$ptotal_inc', '$pperiodeinct', '$ptipests', '$ptotal_spg', '$ptglpengajuan', '$ptotal_incbot')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
                    //echo "ERROR....";
                    goto hapusdata;
                }
                
            }
        }
    }
    
    $query = "SELECT a.*, b.coa4 FROM $tmp01 a LEFT JOIN dbmaster.t_spg_kode b on a.kodeid=b.kodeid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
        //echo "ERROR....";
        goto hapusdata;
    }
    
    $query = "DELETE FROM dbmaster.t_spg_gaji_br1 WHERE DATE_FORMAT(periode,'%Y%m')='$bulan' AND "
            . " CONCAT(id_spg,icabangid) IN (SELECT IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')),'') FROM $tmp02)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
        //echo "ERROR....";
        goto hapusdata;
    }
    
    $query = "INSERT INTO dbmaster.t_spg_gaji_br1 (idbrspg, periode, id_spg, icabangid, alokid, areaid, id_zona, jabatid, kodeid, qty, rp, rptotal, coa4)"
            . "SELECT idbrspg, periode, id_spg, icabangid, alokid, areaid, id_zona, jabatid, kodeid, qty, rp, rptotal, coa4 FROM $tmp02";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
        goto hapusdata;
        //echo "ERROR....";
        exit;     
    }
    
    
    $query = "UPDATE dbmaster.t_spg_gaji_br0 a JOIN "
            . " (select distinct idbrspg, periode, id_spg, periode_insentif, sts, total, tglpengajuan, insentif_tambahan, insentif FROM $tmp01) b "
            . " ON a.idbrspg=b.idbrspg AND a.id_spg=b.id_spg AND DATE_FORMAT(a.periode,'%Y%m')=DATE_FORMAT(b.periode,'%Y%m') SET "
            . " a.insentif=b.insentif, a.insentif_tambahan=b.insentif_tambahan, a.periode_insentif=b.periode_insentif, "
            . " a.sts=b.sts, a.tglpengajuan=b.tglpengajuan, a.total=b.total, "
            . " a.apv1='$userid', a.apvtgl1=NOW() WHERE "
            . " CONCAT(a.id_spg,a.icabangid) IN (SELECT IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')),'') FROM $tmp02) AND "
            . " DATE_FORMAT(a.periode,'%Y%m')='$bulan'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
        
        $query = "DELETE FROM dbmaster.t_spg_gaji_br1 WHERE DATE_FORMAT(periode,'%Y%m')='$bulan' AND "
                . " CONCAT(id_spg,icabangid) IN (SELECT IFNULL(CONCAT(IFNULL(id_spg,''),IFNULL(icabangid,'')),'') FROM $tmp02)";
        mysqli_query($cnmy, $query);
        
        goto hapusdata;
        //echo "ERROR....";
        exit;     
    }
    
    
    hapusdata:
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp02");

        mysqli_close($cnmy);
        
        
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    
?>