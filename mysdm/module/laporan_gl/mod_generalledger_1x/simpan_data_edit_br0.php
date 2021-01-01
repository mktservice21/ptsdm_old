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

include "../../../config/koneksimysqli.php";

$pidcard=$_SESSION['IDCARD'];
$pmodule=$_GET['module'];


    $pkdakunedit=$_POST['uakuneditid'];
    $pdokternmedit=$_POST['uiddokternmedit'];

    $query = "select nama from hrd.br_kode where kodeid='$pkdakunedit'";
    $tampilk= mysqli_query($cnmy, $query);
    $nr= mysqli_fetch_array($tampilk);
    $pnamaakunedit=$nr['nama'];

    
    $piddokterasli=$_POST['uasliiddokter'];
    $pnmdokterasli=$_POST['uaslinmdokter'];
    
    $pidakunasli=$_POST['ukdakunasli'];
    $pnmakunasli=$_POST['unmakunasli'];
    
    

$papppil=strtoupper(TRIM($_POST['uapppil']));
$pketpros=$_POST['ustspros'];
$pidbr=$_POST['uidbr'];
$pnobridr=$_POST['unobridr'];
$pnoidauto=$_POST['uidauto'];
$pcoapilih=$_POST['ucoapil'];



$pcoapilih_asli=$_POST['uaslicoapil'];
    
$psudahpost=false;
if ($pketpros=="1" OR (INT)$pketpros==1) $psudahpost=true;

$query = "select DISTINCT d.DIVISI2, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, b.COA4, b.NAMA4
   from dbmaster.coa_level4 b 
   LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
   LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
   LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1 WHERE b.COA4='$pcoapilih' ";
$tampil = mysqli_query($cnmy, $query);
$z=mysqli_fetch_array($tampil);
$ncoa2=$z['COA2'];
$nnmcoa2=$z['NAMA2'];
$ncoa3=$z['COA3'];
$nnmcoa3=$z['NAMA3'];
$pcoanamapilih=$z['NAMA4'];
$pcoadivisi=$z['DIVISI2'];
if ($pcoadivisi=="" OR $pcoadivisi=="OTHER" OR $pcoadivisi=="OTHERS") $pcoadivisi="AA";
         

        //ASLI
        $query = "select DISTINCT d.DIVISI2, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, b.COA4, b.NAMA4
           from dbmaster.coa_level4 b 
           LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
           LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
           LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1 WHERE b.COA4='$pcoapilih_asli' ";
        $tampil = mysqli_query($cnmy, $query);
        $z=mysqli_fetch_array($tampil);
        $ncoa2_asli=$z['COA2'];
        $nnmcoa2_asli=$z['NAMA2'];
        $ncoa3_asli=$z['COA3'];
        $nnmcoa3_asli=$z['NAMA3'];
        $pcoanamapilih_asli=$z['NAMA4'];
        $pcoadivisi_asli=$z['DIVISI2'];
        if ($pcoadivisi_asli=="" OR $pcoadivisi_asli=="OTHER" OR $pcoadivisi_asli=="OTHERS") $pcoadivisi_asli="AA";
        //END ASLI

$berhasil="Tidak ada data yang tersimpan...";

//$berhasil="$papppil, $pketpros - $psudahpost, $pidbr, $pnoidauto, $pcoapilih $pcoanamapilih, ($ncoa2 $nnmcoa2), ($ncoa3 $nnmcoa3) - $pcoadivisi";
//$berhasil="$pcoapilih_asli $pcoanamapilih_asli, ($ncoa2_asli $nnmcoa2_asli), ($ncoa3_asli $nnmcoa3_asli) - $pcoadivisi_asli";

if ($pmodule=="lapgeneralledger") {
    
    if ($psudahpost==true) {
        $query ="UPDATE dbmaster.t_proses_bm_act SET coa_edit='$pcoapilih', "
                . " coa_nama_edit='$pcoanamapilih', coa_edit2='$ncoa2', coa_nama_edit2='$nnmcoa2', "
                . " coa_edit3='$ncoa3', coa_nama_edit3='$nnmcoa3', divisi_edit='$pcoadivisi', userid='$pidcard', "
                . " nkodeid='$pkdakunedit', nkodeid_nama='$pnamaakunedit' WHERE "
                . " noidauto='$pnoidauto' AND idkodeinput='$pidbr' AND kodeinput='$papppil' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    }
    
    if ($papppil=="A") {
        $query_br_edit ="UPDATE hrd.br0 SET COA4='$pcoapilih', kode='$pkdakunedit' WHERE brid='$pidbr' LIMIT 1";
    }
    
    
    if (!empty($query_br_edit)) {
        
        mysqli_query($cnmy, $query_br_edit);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
            
            if ($psudahpost==true) {
                $query ="UPDATE dbmaster.t_proses_bm_act SET coa_edit='$pcoapilih_asli', "
                        . " coa_nama_edit='$pcoanamapilih_asli', coa_edit2='$ncoa2_asli', coa_nama_edit2='$nnmcoa2_asli', "
                        . " coa_edit3='$ncoa3_asli', coa_nama_edit3='$nnmcoa3_asli', divisi_edit='$pcoadivisi_asli', "
                        . " dokterid='$piddokterasli', dokter_nama='$pnmdokterasli', "
                        . " nkodeid='$pidakunasli', nkodeid_nama='$pnmakunasli' WHERE "
                        . " noidauto='$pnoidauto' AND idkodeinput='$pidbr' AND kodeinput='$papppil' LIMIT 1";
                mysqli_query($cnmy, $query);
            }
            
            echo $erropesan;
            mysqli_close($cnmy); 
            exit; 
        }
        
    }
    
    if ($psudahpost==true) {
        if (empty(trim($pdokternmedit))) {
            mysqli_query($cnmy, "UPDATE dbmaster.t_proses_bm_act SET dokterid='', dokter_nama='' WHERE noidauto='$pnoidauto' AND idkodeinput='$pidbr' AND kodeinput='$papppil' LIMIT 1");
        }
    }
        
    if ($papppil=="A") {
        if (empty(trim($pdokternmedit))) {
            mysqli_query($cnmy, "UPDATE hrd.br0 SET dokterid='' WHERE brid='$pidbr' LIMIT 1");
        }
    }
    
    
    
    $berhasil="berhasil...";
}

mysqli_close($cnmy);
echo $berhasil;
?>