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
    
    if ($module=="entrybrnon" AND $act=="inputdatapajak" OR $act=="editdata") {
        
        
        $pbrid=$_POST['uidbr'];
        $pidinput=$_POST['uidinput'];
        if ($pidinput=="0") $pidinput="";
        
        $pcbpajak=$_POST['cbpajak'];
        
        $pjenis_dpp="B";
    
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
        
        
        if (empty($pidinput) AND $act=="inputdatapajak") {
            $query = "SELECT * from hrd.br0 WHERE brId='$pbrid' AND pajak='Y'";
            $tampil=mysqli_query($cnmy,$query);
            $ketemu=mysqli_num_rows($tampil);
            if ($ketemu==0 AND $pcbpajak=="Y") {

                $query = "UPDATE hrd.br0 SET pajak='Y', nama_pengusaha='$pekenapajak', noseri='$penoserifp', tgl_fp=$petglfp'', "
                        . "dpp='$pejmldpp', ppn='$pejmlppn', ppn_rp='$pejmlrpppn', pph_jns='$pcbpph', pph='$pejmlpph', "
                        . "pph_rp='$pejmlrppph', jasa_rp='$perpjmljasa', jenis_dpp='$pjenis_dpp' from hrd.br0 WHERE "
                        . "brId='$pbrid' AND pajak<>'Y'";

                $query = "UPDATE hrd.br0 SET pajak='Y' WHERE brId='$pbrid' AND pajak<>'Y'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan : br otc"; exit; }
            }
        }
        
        if (empty($pidinput) AND $act=="inputdatapajak") {
            $query = "INSERT INTO dbmaster.t_br0_pajak (brId,"
                    . "nama_pengusaha, noseri, tgl_fp, dpp, ppn, ppn_rp, "
                    . "pph_jns, pph, pph_rp, jasa_rp, jenis_dpp, jumlah)VALUES"
                    . "('$pbrid', "
                    . "'$pekenapajak', '$penoserifp', '$petglfp', '$pejmldpp', '$pejmlppn', '$pejmlrpppn',"
                    . "'$pcbpph', '$pejmlpph', '$pejmlrppph', '$perpjmljasa', '$pjenis_dpp', '$pejmlusulan')";
        }else{
            $query = "UPDATE dbmaster.t_br0_pajak SET nama_pengusaha='$pekenapajak', noseri='$penoserifp', tgl_fp='$petglfp', "
                    . " dpp='$pejmldpp', ppn='$pejmlppn', ppn_rp='$pejmlrpppn', "
                    . " pph_jns='$pcbpph', pph='$pejmlpph', pph_rp='$pejmlrppph', jasa_rp='$perpjmljasa', "
                    . " jenis_dpp='$pjenis_dpp', jumlah='$pejmlusulan' WHERE idinput='$pidinput' AND brId='$pbrid'";
        }
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan : br otc pajak"; exit; }
            
        
        //$berhasil="$pbrid, $pcbpajak, $pekenapajak, $penoserifp, $petglfp, $perpjmljasa, $pejmldpp, $pejmlppn, $pejmlrpppn, $pcbpph, $pejmlpph, $pejmlrppph, $pejmlbulat, $pejmlmaterai, $pejmlusulan";
        
        $berhasil="";
    }elseif ($module=="entrybrnon" AND $act=="hapus") {
        $pidinput=$_POST['uid'];
        
        if ($pidinput=="0" OR (double)$pidinput==0) {
            
        }else{
            $query = "DELETE FROM dbmaster.t_br0_pajak WHERE idinput='$pidinput'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan : hapus br otc"; exit; }
        }
        $berhasil="data berhasil dihapus...";
    }
    
    mysqli_close($cnmy);
    mysqli_close($cnit);
    echo $berhasil;
?>

