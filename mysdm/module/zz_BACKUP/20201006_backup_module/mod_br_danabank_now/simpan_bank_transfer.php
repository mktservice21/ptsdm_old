<?php
    date_default_timezone_set('Asia/Jakarta');
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
        
        $pcoa="";
        $query = "select ibank_coa_k from dbmaster.t_kode_spd WHERE subkode='29'";
        $tampil=  mysqli_query($cnmy, $query);
        $nr= mysqli_fetch_array($tampil);
        $pcoa=$nr['ibank_coa_k'];
        if (empty($pcoa)) $pcoa="800-40";
        
        $query ="INSERT INTO dbmaster.t_suratdana_bank (idinputbank, tglinput, tanggal, stsinput, coa4, kodeid, subkode, idinput, nomor, nodivisi,
            nobukti, divisi, sts, jumlah)
            select '$kodenya' as idinputbank, CURRENT_DATE() as tglinput, tanggal, stsinput, '$pcoa' as coa4, '2' as kodeid, '29' as subkode, idinput, nomor, nodivisi,
            nobukti, divisi, sts, '$pjumlah' as jumlah from dbmaster.t_suratdana_bank where idinputbank='$pidinputbank'";
        
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query = "UPDATE dbmaster.t_suratdana_bank SET parentidbank='$pidinputbank' WHERE idinputbank='$kodenya' AND stsinput='T'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query = "UPDATE dbmaster.t_suratdana_bank SET userid='$_SESSION[IDCARD]' WHERE idinputbank='$kodenya'";
        mysqli_query($cnmy, $query);
        
        
        $berhasil="";
    }
    
    echo $berhasil; exit;
?>

