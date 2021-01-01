<?php
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/koneksimysqli_it.php";
    $dbname = "dbmaster";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    $puserid=$_SESSION['IDCARD'];
    $pkaryawanid=$puserid;
    
    
    $berhasil="Tidak ada data yang disimpan";
    
    if ($module=="entrybrotc" AND $act=="inputdatapajak" OR $act=="editdata") {
        
        
        $pbrid=$_POST['uidbr'];
        $pidinput=$_POST['uidinput'];
        if ($pidinput=="0") $pidinput="";
        
        $pcbpajak=$_POST['cbpajak'];
        
        $pjenis_dpp=" NULL ";
    
        
        $fieldjasa = ", jasa_rp=NULL, jenis_dpp=NULL ";

        $pchkjasa="";
        if (isset($_POST['chk_jasa'])) $pchkjasa=$_POST['chk_jasa'];

        $pchkatrika="";
        if (isset($_POST['chk_atrika'])) $pchkatrika=$_POST['chk_atrika'];
    
        $prpjmljasa=0;
        if (!empty($_POST['erpjmljasa'])) $prpjmljasa=str_replace(",","", $_POST['erpjmljasa']);

        if (!empty($pchkjasa)) {
            $pjenis_dpp=" 'A' ";
            
            $fieldjasa = ", jasa_rp='$prpjmljasa', jenis_dpp='A' ";
        }elseif (!empty($pchkatrika)) {
            $pjenis_dpp=" 'B' ";
            
            $fieldjasa = ", jasa_rp='$prpjmljasa', jenis_dpp='B' ";
        }
    
        
        $pekenapajak=$_POST['ekenapajak'];
        $penoserifp=$_POST['enoserifp'];
        $petglfp=$_POST['etglfp'];
        $perpjmljasa=$_POST['erpjmljasa'];
        $pejmldpp=$_POST['ejmldpp'];
        $pejmlppn=$_POST['ejmlppn'];
        $pejmlrpppn=$_POST['ejmlrpppn'];
        $pcbpph=$_POST['cbpph'];
        $pejmlpph=$_POST['ejmlpph'];
        $pejmlrppph=$_POST['ejmlrppph'];
        $pejmlbulat=$_POST['ejmlbulat'];
        $pejmlmaterai=$_POST['ejmlmaterai'];
        $pejmlusulan=$_POST['ejmlusulan'];
        
        $ptotjmlminta=$_POST['ujmltptotminta'];
        
        if (!empty($pekenapajak)) $pekenapajak = str_replace("'", " ", $pekenapajak);
        if (!empty($penoserifp)) $penoserifp = str_replace("'", " ", $penoserifp);
        
        if (empty($perpjmljasa)) $perpjmljasa=0;
        if (empty($pejmldpp)) $pejmldpp=0;
        if (empty($pejmlppn)) $pejmlppn=0;
        if (empty($pejmlrpppn)) $pejmlrpppn=0;
        if (empty($pejmlpph)) $pejmlpph=0;
        if (empty($pejmlrppph)) $pejmlrppph=0;
        if (empty($pejmlbulat)) $pejmlbulat=0;
        if (empty($pejmlmaterai)) $pejmlmaterai=0;
        if (empty($pejmlusulan)) $pejmlusulan=0;
        
        if (empty($ptotjmlminta)) $ptotjmlminta=0;
        
        $petglfp = str_replace('/', '-', $petglfp);
        $petglfp = date("Y-m-d", strtotime($petglfp));
        
        $perpjmljasa=str_replace(",","", $perpjmljasa);
        $pejmldpp=str_replace(",","", $pejmldpp);
        $pejmlppn=str_replace(",","", $pejmlppn);
        $pejmlrpppn=str_replace(",","", $pejmlrpppn);
        $pejmlpph=str_replace(",","", $pejmlpph);
        $pejmlrppph=str_replace(",","", $pejmlrppph);
        $pejmlbulat=str_replace(",","", $pejmlbulat);
        $pejmlmaterai=str_replace(",","", $pejmlmaterai);
        $pejmlusulan=str_replace(",","", $pejmlusulan);
        
        $ptotjmlminta=str_replace(",","", $ptotjmlminta);
        
        if ((double)$ptotjmlminta==0) {
            echo "tidak ada data yang disimpan...";
            exit;
        }
        
        if (empty($pidinput) AND $act=="inputdatapajak") {
            $query = "SELECT * from hrd.br_otc WHERE brOtcId='$pbrid' AND pajak='Y'";
            $tampil=mysqli_query($cnit,$query);
            $ketemu=mysqli_num_rows($tampil);
            if ($ketemu==0 AND $pcbpajak=="Y") {

                $query = "UPDATE hrd.br_otc SET pajak='Y', nama_pengusaha='$pekenapajak', noseri='$penoserifp', tgl_fp=$petglfp'', "
                        . "dpp='$pejmldpp', ppn='$pejmlppn', ppn_rp='$pejmlrpppn', pph_jns='$pcbpph', pph='$pejmlpph', "
                        . "pph_rp='$pejmlrppph', pembulatan='$pejmlbulat', materai_rp='$pejmlmaterai' $fieldjasa from hrd.br_otc WHERE "
                        . "brOtcId='$pbrid' AND pajak<>'Y'";

                $query = "UPDATE hrd.br_otc SET pajak='Y' WHERE brOtcId='$pbrid' AND pajak<>'Y'";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan : br otc"; exit; }
            }
        }
        
        if (empty($pidinput) AND $act=="inputdatapajak") {
            $query = "INSERT INTO dbmaster.t_br_otc_pajak (brOtcId,"
                    . "nama_pengusaha, noseri, tgl_fp, dpp, ppn, ppn_rp, "
                    . "pph_jns, pph, pph_rp, jasa_rp, jenis_dpp, jumlah, pembulatan, materai_rp)VALUES"
                    . "('$pbrid', "
                    . "'$pekenapajak', '$penoserifp', '$petglfp', '$pejmldpp', '$pejmlppn', '$pejmlrpppn',"
                    . "'$pcbpph', '$pejmlpph', '$pejmlrppph', '$perpjmljasa', $pjenis_dpp, '$pejmlusulan', '$pejmlbulat', '$pejmlmaterai')";
        }else{
            $query = "UPDATE dbmaster.t_br_otc_pajak SET nama_pengusaha='$pekenapajak', noseri='$penoserifp', tgl_fp='$petglfp', "
                    . " dpp='$pejmldpp', ppn='$pejmlppn', ppn_rp='$pejmlrpppn', "
                    . " pph_jns='$pcbpph', pph='$pejmlpph', pph_rp='$pejmlrppph', jumlah='$pejmlusulan', pembulatan='$pejmlbulat', materai_rp='$pejmlmaterai' $fieldjasa WHERE idinput='$pidinput' AND brOtcId='$pbrid'";
        }
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan : br otc pajak"; exit; }
            
        
        $query = "UPDATE hrd.br_otc SET jumlah='$ptotjmlminta' WHERE brOtcId='$pbrid'";
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan : br otc"; exit; }
        
        //$berhasil="$pbrid, $pcbpajak, $pekenapajak, $penoserifp, $petglfp, $perpjmljasa, $pejmldpp, $pejmlppn, $pejmlrpppn, $pcbpph, $pejmlpph, $pejmlrppph, $pejmlbulat, $pejmlmaterai, $pejmlusulan";
        
        $berhasil="";
    }elseif ($module=="entrybrotc" AND $act=="hapus") {
        $pidinput=$_POST['uid'];
        
        if ($pidinput=="0" OR (double)$pidinput==0) {
            
        }else{
            $query = "select brotcid, jumlah FROM dbmaster.t_br_otc_pajak WHERE idinput='$pidinput'";
            $tampil= mysqli_query($cnmy, $query);
            $nx= mysqli_fetch_array($tampil);
            $nbrid=$nx['brotcid'];
            $njmldel=$nx['jumlah'];
            if (empty($njmldel)) $njmldel=0;
            
            $query = "select sum(jumlah) as jumlahrp FROM dbmaster.t_br_otc_pajak WHERE idinput<>'$pidinput' AND brotcid='$nbrid'";
            $tampil1= mysqli_query($cnmy, $query);
            $ns= mysqli_fetch_array($tampil1);
            $njmlrp1=$ns['jumlahrp'];
            if (empty($njmlrp1)) $njmlrp1=0;
            
            $ntotalpajak_1=(double)$njmlrp1-(double)$njmldel;
            
            $query = "select jumlah as jumlahusul FROM hrd.br_otc WHERE brotcid='$nbrid'";
            $tampil2= mysqli_query($cnmy, $query);
            $nl= mysqli_fetch_array($tampil2);
            $njmlrp2=$nl['jumlahusul'];
            if (empty($njmlrp2)) $njmlrp2=0;
            
            $ntotalusul=(double)$njmlrp2+(double)$ntotalpajak_1;
            
            //echo "$nbrid, $njmldel, $njmlrp1, $njmlrp2 --> $ntotalpajak_1 tot usul : $ntotalusul"; exit;
            
            
            $query = "DELETE FROM dbmaster.t_br_otc_pajak WHERE idinput='$pidinput'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan : hapus br otc"; exit; }
            
            
            if (!empty($nbrid)) {
                $query = "UPDATE hrd.br_otc SET jumlah='$ntotalusul' WHERE brOtcId='$nbrid'";
                mysqli_query($cnit, $query);
                $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo "$erropesan : br otc"; exit; }
            }
            
        
        }
        $berhasil="data berhasil dihapus...";
    }
    
    mysqli_close($cnmy);
    mysqli_close($cnit);
    echo $berhasil;
?>

