<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    $dbname = "dbmaster";

    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $berhasil="Tidak ada data yang disimpan";
    
    if ($module=="brdanabank") {
        if ($act=="input") {
            
            $pidinput=$_POST['uid'];
            $pidinputspd=$_POST['uidinputspd'];
            $pnodivisi=$_POST['unodiv'];
            $pket=$_POST['uketerangan'];
            $pnodivisidari=$_POST['unodivdari'];//idinput bank
            $pjml=$_POST['ujml'];
            $pjumlah=str_replace(",","", $pjml);
            $pket=str_replace("'","", $pket);
            
            $kodenya="BN00000001";
            $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idinputbank,8)) as NOURUT from $dbname.t_suratdana_bank");
            $ketemu=  mysqli_num_rows($sql);
            $awal=8; $urut=1; $kodenya=""; $periode=date('Ymd');
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                if (empty($o['NOURUT'])) $o['NOURUT']=0;
                $urut=$o['NOURUT']+1;
                $jml=  strlen($urut);
                $awal=$awal-$jml;
                $kodenya="BN".str_repeat("0", $awal).$urut;
            }else{
                $kodenya="BN00000001";
            }

            $now=date("mdYhis");
            $tmp01 =" dbtemp.RINPBANK01_".$_SESSION['USERID']."_$now ";
            
            $query = "select * from dbmaster.t_suratdana_bank WHERE idinputbank='$pnodivisidari'";
            $query = "create TEMPORARY table $tmp01 ($query)";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $query="UPDATE $tmp01 SET parentidbank=idinputbank, idinputbank='$kodenya', "
                    . " nodivisi='$pnodivisi', idinput='$pidinputspd', tglinput=NOW(), "
                    . " stsinput='N', jumlah='$pjumlah', brid='', noslip='', "
                    . " realisasi='', customer='', aktivitas1='', keterangan='$pket', sys_now=NOW(), userid='$_SESSION[IDCARD]'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }            
            
            $query="UPDATE $tmp01 a JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput SET "
                    . " a.kodeid=b.kodeid, a.subkode=b.subkode, a.divisi=b.divisi, a.nomor=b.nomor";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }            
            
            $query = "INSERT INTO dbmaster.t_suratdana_bank SELECT * FROM $tmp01";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }            
            
            hapusdata:
                mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
            
            $berhasil="$kodenya, $pidinput, $pidinputspd, $pnodivisi, $pket";
            $berhasil="berhasil...";
            
        }
    }
    
    mysqli_close($cnmy);
    echo $berhasil;
?>