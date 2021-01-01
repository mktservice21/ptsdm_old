<?php

    session_start();
    include "../../config/koneksimysqli.php";
    $dbname = "dbmaster";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    
    $berhasil="Tidak ada data yang disimpan";
    
    if ($module=="brdanabank" AND $act=="inputtransbank") {
        $pidinputbank=$_POST['uidbank'];
        $ptgl01 = str_replace('/', '-', $_POST['utgl']);
        $pjml=$_POST['ujumlah'];
        $pjumlah=str_replace(",","", $pjml);
        
        $kodenya="BN00000001";
        
        $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idinputbank,8)) as NOURUT from $dbname.t_suratdana_bank");
        $ketemu=  mysqli_num_rows($sql);
        $urut=1; $awal=8; $kodenya="";
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            if (empty($o['NOURUT'])) $o['NOURUT']=0;
            $urut=$o['NOURUT']+1;
        }
        $jml=  strlen($urut);
        $nawal=$awal-$jml;
        $kodenya="BN".str_repeat("0", $nawal).$urut;
        
        
        $query ="INSERT INTO dbmaster.t_suratdana_bank (idinputbank, tglinput, tanggal, stsinput, coa4, kodeid, subkode, idinput, nomor, nodivisi,
            nobukti, divisi, sts, jumlah)
            select '$kodenya' as idinputbank, CURRENT_DATE() as tglinput, tanggal, stsinput, coa4, '2' as kodeid, '29' as subkode, idinput, nomor, nodivisi,
            nobukti, divisi, sts, '$pjumlah' as jumlah from dbmaster.t_suratdana_bank where idinputbank='$pidinputbank'";
        
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $berhasil="";
    }
    
    echo $berhasil; exit;
?>

