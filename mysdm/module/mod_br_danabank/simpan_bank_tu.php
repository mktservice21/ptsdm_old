<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    //$cnmy=$cnit;
    $dbname = "dbmaster";
    $pnobukti="";
    $berhasil = "Tidak ada data yang diinput";
if ($module=='brdanabank')
{
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
        
    $pnoslipbaru=$_POST['unoslipbaru'];
    $pidbank=$_POST['uid'];
    $ptgl=$_POST['utglkeluar'];
    $pketerangan=$_POST['uketerangan'];
    if (!empty($pketerangan)) $pketerangan = str_replace("'", " ", $pketerangan);
    if (empty($pketerangan)) $pketerangan = "Transfer Ulang";
    
    $ptgl01 = str_replace('/', '-', $ptgl);
    $ptgl_kembali= date("Y-m-d", strtotime($ptgl01));
    
    //echo "$pnoslipbaru, $pidbank, $ptgl01, $pketerangan"; exit;
    
    if (!empty($kodenya) AND !empty($pidbank)) {
        $userid=$_SESSION['IDCARD'];
        $now=date("mdYhis");
        $tmp01 =" dbtemp.TSDSETHZR01_".$userid."_$now ";
        
        $query = "select * from $dbname.t_suratdana_bank WHERE idinputbank='$pidbank'";
        $query = "create TEMPORARY table $tmp01 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "select divisi from $tmp01";
        $tampil=  mysqli_query($cnmy, $query);
        $nr= mysqli_fetch_array($tampil);
        $ndivisi=$nr['divisi'];
        
        
        //no slip
        $nnoslip="";
        $nnobrid="";
        
        if ($ndivisi=="OTC"){
            $query = "select noslip, brOtcId brId from hrd.br_otc where brOtcId =(select IFNULL(brid,'') from $dbname.t_suratdana_bank WHERE idinputbank='$pidbank')";
        }else{
            $query = "select noslip, brId from hrd.br0 where brId =(select IFNULL(brid,'') from $dbname.t_suratdana_bank WHERE idinputbank='$pidbank')";
        }
        $tampil=  mysqli_query($cnmy, $query);
        $nr= mysqli_fetch_array($tampil);
        $nnobrid=$nr['brId'];
        
        if (empty($pnoslipbaru)) {
            $nnoslip=$nr['noslip'];
        }else{
            $nnoslip=$pnoslipbaru;
            if (!empty($nnoslip)) {
                //include "../../config/koneksimysqli_it.php";
                
                if ($ndivisi=="OTC"){
                    $query = "UPDATE hrd.br_otc SET noslip='$nnoslip', tgltrans='$ptgl_kembali' WHERE brOtcId='$nnobrid'";
                }else{
                    $query = "UPDATE hrd.br0 SET noslip='$nnoslip', tgltrans='$ptgl_kembali' WHERE brId='$nnobrid'";
                }
                mysqli_query($cnmy, $query);
            }
        }
        
        include "../../module/mod_br_danabank/cari_nomorbukti.php";
        include "../../config/fungsi_combo.php";
        $ppilih_nobukti=caribuktinomor('2', $ptgl_kembali);// 1=bbm, 2=bbK

        $pbukti_periode=date('Ym', strtotime($ptgl_kembali));;
        $pblnini = date('m', strtotime($ptgl_kembali));
        $pthnini = date('Y', strtotime($ptgl_kembali));
        $mbulan=CariBulanHuruf($pblnini);
        $ppilih_blnthn="/".$mbulan."/".$pthnini;
        $pnobukti = "BBK".$ppilih_nobukti."/".$mbulan."/".$pthnini;
    
        
        $query = "UPDATE $tmp01 SET idinputbank='$kodenya', stsinput='T', tanggal='$ptgl_kembali', "
                . " parentidbank='$pidbank', nobukti='$pnobukti', noslip='$nnoslip', "
                . " coa4='000-0', keterangan='$pketerangan', sys_now=NOW()"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
		
		
        
        $query = "SELECT nobbk FROM dbmaster.t_setup_bukti WHERE bulantahun='$pbukti_periode'";
        $showkan= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($showkan);
        if ($ketemu==0){
            mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_bukti (bulantahun, nobbk)VALUES('$pbukti_periode', '$ppilih_nobukti')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }else{
            mysqli_query($cnmy, "UPDATE dbmaster.t_setup_bukti SET nobbk='$ppilih_nobukti' WHERE bulantahun='$pbukti_periode'");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
		
		
        
        $query = "INSERT INTO $dbname.t_suratdana_bank "
                . "SELECT * FROM $tmp01"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        
        $query = "UPDATE $dbname.t_suratdana_bank SET parentidbank='$kodenya' WHERE idinputbank='$pidbank'"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        $berhasil="berhasil";
    }
}
    mysqli_close($cnmy);
    echo $berhasil;
?>

