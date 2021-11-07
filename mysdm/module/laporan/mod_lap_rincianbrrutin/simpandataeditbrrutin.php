<?php
date_default_timezone_set('Asia/Jakarta');
session_start();
include "../../../config/koneksimysqli.php";
$dbname = "dbmaster";

$puserid=$_SESSION['IDCARD'];
if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...!!!";
    mysqli_close($cnmy);
    exit;
}

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

$berhasil="Tidak ada data yang disimpan";
if (($module=='laprincianbrrutin' OR $module=='laprutinrinciotc') AND $act=='input') {
    
    $pnourut=$_POST['unourut'];
    $pidrtnbr=$_POST['uidbrrutin'];
    $pidno=$_POST['uinoid'];
    $pcoakode=$_POST['ucoa'];
    
    $pkryid=$_POST['ukryid'];
    $pidrtn=$_POST['uidrtn'];
    $pbrid=$_POST['ubrid'];
    $pcoalama=$_POST['ucoalama'];
    
    //$berhasil = "$pkryid, $pidrtn, $pbrid, COA : $pcoakode";
    
    if (!empty($pkryid) AND !empty($pidrtn) AND !empty($pbrid) AND !empty($pcoakode)) {
        
        $query = "select c.DIVISI2, c.COA1, d.NAMA1, b.COA2, c.NAMA2, a.COA3, b.NAMA3, a.COA4, a.NAMA4 from dbmaster.coa_level4 as a JOIN dbmaster.coa_level3 as b on a.COA3=b.COA3 JOIN dbmaster.coa_level2 as c on b.COA2=c.COA2 join dbmaster.coa_level1 as d on c.COA1=d.COA1 WHERE a.COA4='$pcoakode'";
        $tampil=mysqli_query($cnmy, $query);
        $row= mysqli_fetch_array($tampil);
        $pcoalvl1=$row['COA1'];
        $pcoanm1=$row['NAMA1'];
        $pcoalvl2=$row['COA2'];
        $pcoanm2=$row['NAMA2'];
        $pcoalvl3=$row['COA3'];
        $pcoanm3=$row['NAMA3'];
        $pcoanm4=$row['NAMA4'];
        $pcoadivisi=$row['DIVISI2'];
        
        //$berhasil = "$pkryid, $pidrtn, $pbrid, COA : $pcoalvl1 ($pcoanm1), $pcoalvl2 ($pcoanm2), $pcoalvl3 ($pcoanm3), $pcoakode ($pcoanm4) - $pcoadivisi, coa lama : $pcoalama";
        
        $query = "UPDATE dbmaster.t_brrutin1 SET coa='$pcoakode' WHERE nobrid='$pbrid' AND idrutin='$pidrtn' AND coa='$pcoalama' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update"; exit; }
        
        
        $query = "UPDATE dbproses.proses_expenses SET coa4='$pcoakode' WHERE kodeinput='5' AND idkodeinput='$pidrtn' AND kodeid='$pbrid' AND coa4='$pcoalama' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update proses exp"; exit; }
        
        
        $query = "UPDATE dbmaster.t_proses_data_bm SET divisi_coa='$pcoadivisi', "
                . " coa='$pcoakode', nama_coa='$pcoanm4', "
                . " coa_edit='$pcoakode', coa_nama_edit='$pcoanm4', "
                . " coa2='$pcoalvl2', nama_coa2='$pcoanm2', "
                . " coa_edit2='$pcoalvl2', coa_nama_edit2='$pcoanm2',  "
                . " coa3='$pcoalvl3', nama_coa3='$pcoanm3', "
                . " coa_edit3='$pcoalvl3', coa_nama_edit3='$pcoanm3' WHERE kodeinput='F' AND idkodeinput='$pidrtn' AND nobrid_r='$pbrid' AND coa='$pcoalama' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error Update proses exp2"; exit; }
        
        
        
        $berhasil="berhasil";
        
    }
    
    
}
mysqli_close($cnmy);
echo $berhasil;
?>