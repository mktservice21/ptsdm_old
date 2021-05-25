<?php

    session_start();
    include "../../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
    if (empty($puserid)) {
        echo "Maaf harus login ulang...";
        exit;
    }
    $fkaryawan=$_SESSION['IDCARD'];
    //$cnmy=$cnit;
    $dbname = "dbmaster";
    
// Hapus 
if ($module=='brdanabankbyfin' AND $act=='hapus')
{
    $pnodivisi=$_GET['id'];
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
    if (empty($puserid)) {
        echo "Maaf harus login ulang...";
        exit;
    }
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpbktulang01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpbktulang02_".$puserid."_$now ";
    
    $query = "select idinputbank, parentidbank, nodivisi, idinput, kodeid, subkode "
            . " from dbmaster.t_suratdana_bank where nodivisi='$pnodivisi' AND stsinput='T'";
    $query = "create  table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata_; }
    
    $query = "select idinputbank, nodivisi, idinput, kodeid, subkode "
            . " from dbmaster.t_suratdana_bank where stsinput='D' AND idinputbank IN "
            . " (select distinct IFNULL(parentidbank,'') from $tmp01)";
    $query = "create temporary table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata_; }
    
    $query = "DELETE FROM $tmp01 WHERE parentidbank NOT IN (select distinct IFNULL(idinputbank,'') from $tmp02)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata_; }
    
    $query = "UPDATE $tmp01 a JOIN $tmp02 b on a.parentidbank=b.idinputbank SET a.nodivisi=b.nodivisi, "
            . " a.idinput=b.idinput, a.kodeid=b.kodeid, a.subkode=b.subkode"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata_; }
    
    
    
            $query = "UPDATE dbmaster.t_suratdana_bank a JOIN $tmp01 b on a.idinputbank=b.idinputbank SET "
                    . " a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE a.nodivisi = '$pnodivisi' AND a.stsinput='T'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE dbmaster.t_suratdana_br SET stsnonaktif='Y' WHERE nodivisi = '$pnodivisi' AND jenis_rpt='W'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
    
    
    //echo $pnodivisi;
    hapusdata_:
        mysqli_query($cnmy, "drop temporary table $tmp01");
        mysqli_query($cnmy, "drop temporary table $tmp02");
        
    mysqli_close($cnmy);
    header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    exit;
}
elseif ($module=='brdanabankbyfin')
{
    
    $date1=$_POST['e_periode_save'];
    $periode1= date("Y-m-d", strtotime($date1));
    $npidbank="";
    foreach ($_POST['chk_jmltu'] as $no_brid) {
        if (!empty($no_brid)) {
            $npidbank .="'".$no_brid."',";
        }
        
    }
    
    if (!empty($npidbank)) {
        $npidbank="(".substr($npidbank, 0, -1).")";
        
        $now=date("mdYhis");
        $tmp01 =" dbtemp.tmpbktulang01_".$puserid."_$now ";
        $tmp02 =" dbtemp.tmpbktulang02_".$puserid."_$now ";
        $tmp03 =" dbtemp.tmpbktulang03_".$puserid."_$now ";
        
        $query = "select idinputbank, tanggal, idinput, nodivisi, jumlah, divisi, brid, noslip, realisasi, customer, aktivitas1 from dbmaster.t_suratdana_bank where idinputbank IN $npidbank";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select * from dbmaster.t_suratdana_br where CONCAT(IFNULL(nodivisi,''), IFNULL(idinput,'')) IN "
                . "(select distinct CONCAT(IFNULL(nodivisi,''), IFNULL(idinput,'')) FROM  $tmp01)";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "DELETE FROM $tmp01 WHERE CONCAT(IFNULL(nodivisi,''), IFNULL(idinput,'')) NOT IN "
                . "(select distinct CONCAT(IFNULL(nodivisi,''), IFNULL(idinput,'')) FROM  $tmp02)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp01 SET nodivisi=CONCAT(nodivisi,'/TU')"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "UPDATE $tmp02 a JOIN $tmp01 b on a.idinput=b.idinput SET "
                . " a.nodivisi=b.nodivisi, a.jumlah=b.jumlah, a.tgl=b.tanggal, "
                . " keterangan='Transfer Ulang', pilih='N', a.tglf=b.tanggal, a.tglt=b.tanggal, "
                . " a.tgl_dir=NULL, a.tgl_dir2=NULL, a.jenis_rpt='T', a.nomor=''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "select distinct idinput from $tmp02";
        $query = "create TEMPORARY table $tmp03 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $kodenya="";
        $sql=  mysqli_query($cnmy, "select MAX(idinput) as NOURUT from $dbname.t_suratdana_br");
        $ketemu=  mysqli_num_rows($sql);
        $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $kodenya=$urut;
        }

        if (!empty($kodenya)) {
            $query = "select distinct idinput from $tmp03 order by idinput";
            $tampil= mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pidinputasli=$row['idinput'];
                
                $query = "UPDATE $tmp01 SET idinput='$kodenya' WHERE idinput='$pidinputasli'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "UPDATE $tmp02 SET idinput='$kodenya' WHERE idinput='$pidinputasli'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                $kodenya++;
            }
            
            
            $query = "INSERT INTO dbmaster.t_suratdana_br (idinput, divisi, kodeid, subkode, tgl, nodivisi, jumlah, 
                        keterangan, userid, tglinput, coa4, pilih, 
                        lampiran, kodeperiode, tglf, tglt, 
                        karyawanid, jenis_rpt)"
                    . "SELECT 
                        idinput, divisi, kodeid, subkode, tgl, nodivisi, jumlah, 
                        keterangan, '$fkaryawan' as userid, NOW() as tglinput, coa4, 'N' as pilih, 
                        lampiran, kodeperiode, tglf, tglt, 
                        '$fkaryawan' as karyawanid, 'W' as jenis_rpt
                        FROM $tmp02";//, apv1, tgl_apv1, apv2, tgl_apv2
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "INSERT INTO dbmaster.t_suratdana_br1 (idinput, bridinput, kodeinput, amount)"
                    . " SELECT distinct idinput, brid, 'W' as kodeinput, jumlah from $tmp01";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
            $query = "UPDATE dbmaster.t_suratdana_bank a JOIN $tmp01 b on a.idinputbank=b.idinputbank SET "
                    . " a.nodivisi=b.nodivisi, a.idinput=b.idinput WHERE a.idinputbank IN $npidbank AND a.stsinput='T'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        }
        
        
        hapusdata:
            mysqli_query($cnmy, "drop temporary table $tmp01");
            mysqli_query($cnmy, "drop temporary table $tmp02");
            mysqli_query($cnmy, "drop temporary table $tmp03");
    }
    
}

mysqli_close($cnmy);

header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');

?>

