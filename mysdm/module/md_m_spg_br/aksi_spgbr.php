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
if ($module=='spgbr' AND $act=='hapus')
{
    mysqli_query($cnmy, "update $dbname.t_spg_br0 set stsnonaktif='Y' WHERE idbrspg='$_GET[id]'");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='spgbr')
{
    $kodenya=$_POST['e_id'];
    $datebr = str_replace('/', '-', $_POST['e_tglbr']);
    $ptglbr= date("Y-m-d", strtotime($datebr));
    $pbulanbr =  date("Ym", strtotime($ptglbr));
    
    //$ptglbr =  date("Y-m-d", strtotime($_POST['e_tglbr']));
    //$pbulanbr =  date("Ym", strtotime($_POST['e_tglbr']));
    
    
    $pidcabang=$_POST['cb_cabang'];
    $pidspg=$_POST['cb_spg'];
    $pketerangan=$_POST['e_keterangan'];
    if (!empty($pketerangan)) $pketerangan = str_replace("'", " ", $pketerangan);
    
    $pinsentif=str_replace(",","", $_POST['e_insentif']);
    $pgaji=str_replace(",","", $_POST['e_gaji']);
    $phk=str_replace(",","", $_POST['e_hk']);
    $pumakan=str_replace(",","", $_POST['e_makan']);
    $ptotmakan=str_replace(",","", $_POST['e_totmakan']);
    $psewa=str_replace(",","", $_POST['e_sewa']);
    $ppulsa=str_replace(",","", $_POST['e_pulsa']);
    $pparkir=str_replace(",","", $_POST['e_parkir']);
    $ptotal=str_replace(",","", $_POST['e_total']);
    
    
    if ($act=='input') {
        $sqlinput=  mysqli_query($cnmy, "select * from $dbname.t_spg_br0 WHERE stsnonaktif<>'Y' AND id_spg='$pidspg' AND DATE_FORMAT(tglbr,'%Y%m')='$pbulanbr'");
        $sudhinput=  mysqli_num_rows($sqlinput);
        if ($sudhinput>0){
            $a=  mysqli_fetch_array($sqlinput);
            $iidbrnya=$a['idbrspg'];
            $itglinput= date("d F Y", strtotime($a['tglinput']));
            echo "Bulan Tersebut Sudah pernah input, dengan ID : $iidbrnya, Tgl. Input : $itglinput";
            exit;
        }
        
        $sql=  mysqli_query($cnmy, "select IFNULL(MAX(right(idbrspg,9)),0) as NOURUT from $dbname.t_spg_br0");
        $ketemu=  mysqli_num_rows($sql);
        $awal=9; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $jml=  strlen($urut);
            $awal=$awal-$jml;
            $kodenya="S".str_repeat("0", $awal).$urut;
        }else{
            $kodenya=$_POST['e_id'];
        }
    }
    
    //echo "$kodenya, $pinsentif, $pgaji, $phk, $pumakan, $psewa, $ppulsa, $pparkir, $ptotal"; exit;
    
    if ($act=="input") {
        $query = "INSERT INTO $dbname.t_spg_br0 (tglinput, idbrspg, id_spg, tglbr, icabangid, "
                . "harikerja, total, keterangan, userid)values"
                . "(CURRENT_DATE(), '$kodenya', '$pidspg', '$ptglbr', '$pidcabang', "
                . "'$phk', '$ptotal', '$pketerangan', '$_SESSION[IDCARD]')";
    }else{
        $query = "UPDATE $dbname.t_spg_br0 SET id_spg='$pidspg', tglbr='$ptglbr', "
                . " icabangid='$pidcabang', harikerja='$phk', total='$ptotal', "
                . " keterangan='$pketerangan', userid='$_SESSION[IDCARD]' WHERE "
                . " idbrspg='$kodenya'";
    }
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    mysqli_query($cnmy, "DELETE FROM $dbname.t_spg_br1 WHERE idbrspg='$kodenya'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    //INSENTIF 01
    $query="INSERT INTO $dbname.t_spg_br1 (idbrspg, kodeid, qty, rp, rptotal)VALUES('$kodenya', '01', 1, '$pinsentif', '$pinsentif')";
    mysqli_query($cnmy, $query);
    //GAJI 02
    $query="INSERT INTO $dbname.t_spg_br1 (idbrspg, kodeid, qty, rp, rptotal)VALUES('$kodenya', '02', 1, '$pgaji', '$pgaji')";
    mysqli_query($cnmy, $query);
    //MAKAN 03
    $query="INSERT INTO $dbname.t_spg_br1 (idbrspg, kodeid, qty, rp, rptotal)VALUES('$kodenya', '03', '$phk', '$pumakan', '$ptotmakan')";
    mysqli_query($cnmy, $query);
    //SEWA 04
    $query="INSERT INTO $dbname.t_spg_br1 (idbrspg, kodeid, qty, rp, rptotal)VALUES('$kodenya', '04', 1, '$psewa', '$psewa')";
    mysqli_query($cnmy, $query);
    //PULSA 05
    $query="INSERT INTO $dbname.t_spg_br1 (idbrspg, kodeid, qty, rp, rptotal)VALUES('$kodenya', '05', 1, '$ppulsa', '$ppulsa')";
    mysqli_query($cnmy, $query);
    //PARKIR 06
    $query="INSERT INTO $dbname.t_spg_br1 (idbrspg, kodeid, qty, rp, rptotal)VALUES('$kodenya', '06', 1, '$pparkir', '$pparkir')";
    mysqli_query($cnmy, $query);
    
    //UPDATE COA
    $query="UPDATE $dbname.t_spg_br1 a SET a.coa4=(select b.coa4 from $dbname.t_spg_kode b WHERE a.kodeid=b.kodeid) WHERE idbrspg='$kodenya'";
    mysqli_query($cnmy, $query);
    
    
    // HITUNG LAGI TOTAL YANG TERINPUT
    $query="select sum(rptotal) as rptotal FROM $dbname.t_spg_br1 WHERE idbrspg='$kodenya'";
    $tampil= mysqli_query($cnmy, $query);
    $tot= mysqli_fetch_array($tampil);
    $ptotal=$tot['rptotal'];
    if (empty($ptotal)) $ptotal=0;
    $query = "UPDATE $dbname.t_spg_br0 SET total='$ptotal'  WHERE idbrspg='$kodenya'";
    mysqli_query($cnmy, $query);
    
    
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
  
    
?>
