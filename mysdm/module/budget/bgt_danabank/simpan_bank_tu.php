<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    //$cnmy=$cnit;
    $dbname = "dbmaster";
    $pnobukti="";
    $berhasil = "Tidak ada data yang diinput";
if ($module=='brdanabankbyfin')
{
    $piduser=$_POST['uuserinput'];
    $pidkry=$_POST['ukryinput'];
    
    if (empty($piduser)) {
        echo "Anda harus login ulang"; mysqli_close($cnmy); exit;
    }
    
    $pidbank=$_POST['uid'];
    $pidigroup=$_POST['ugroup'];
    $pidbr=$_POST['ubrid'];
    $pdivisi=$_POST['udivisi'];
    $pbolehsimpanbr=$_POST['ubolehsimpanbr'];
    $pnosliplama=$_POST['unosliplama'];
    $pnoslipbaru=$_POST['unoslipbaru'];
    $ptgl=$_POST['utglkeluar'];
    $pketerangan=$_POST['uketerangan'];
    if (!empty($pketerangan)) $pketerangan = str_replace("'", " ", $pketerangan);
    if (empty($pketerangan)) $pketerangan = "Transfer Ulang";
    
    $ptgl01 = str_replace('/', '-', $ptgl);
    $ptglinput= date("Y-m-d", strtotime($ptgl01));
    
    //echo "igroup : $pidigroup, br ($pdivisi) : $pidbr, $pbolehsimpanbr, noslip baru: $pnoslipbaru noslilama : $pnosliplama, $pidbank, $ptglinput, $pketerangan"; exit;
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpinptubank01_".$piduser."_$now ";

    $query = "select * from dbmaster.t_suratdana_bank WHERE idinputbank='$pidbank' LIMIT 1";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select * from $tmp01";
    $tampil3= mysqli_query($cnmy, $query);
    $ketemu3= mysqli_num_rows($tampil3);
    if ((INT)$ketemu3<=0) {
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
        echo "data tidak ada yang disimpan";
        mysqli_close($cnmy);
        exit;
    }
    
    $pnobukti="";
    $p_no="2";
    $p_buknin="BBK";
    $p_fieldno="nobbk";

    include "../cari_nomorbukti.php";
    include "../../../config/fungsi_combo.php";
    $ppilih_nobukti=caribuktinomor("2", $p_no, $ptglinput);// 1=bbm, 2=bbm

    $pbukti_periode=date('Ym', strtotime($ptglinput));;
    $pblnini = date('m', strtotime($ptglinput));
    $pthnini = date('Y', strtotime($ptglinput));
    $mbulan=CariBulanHuruf($pblnini);
    $ppilih_blnthn="/".$mbulan."/".$pthnini;
    $pnobukti = $p_buknin.$ppilih_nobukti."/".$mbulan."/".$pthnini;

    
    $query = "select nobukti from dbmaster.t_suratdana_bank WHERE nobukti='$pnobukti' AND IFNULL(stsnonaktif,'')<>'Y'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemua=  mysqli_num_rows($tampil);
    if ((INT)$ketemua>0) {
        mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
        echo "nomor bukti tersebut sudah ada";
        mysqli_close($cnmy);
        exit;
    }
        
    //echo "$ppilih_nobukti, nobukti : $pnobukti"; exit;
      
    $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idinputbank,8)) as NOURUT from dbmaster.t_suratdana_bank");
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
    
    if (!empty($kodenya)) {
        //echo $kodenya; exit; 
        $nnoslip=$pnoslipbaru;
        if (empty($pnoslipbaru)) $nnoslip=$pnosliplama;
        
        $query = "UPDATE $tmp01 SET idinputbank='$kodenya', stsinput='T', tanggal='$ptglinput', "
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
		
		
        
        $query = "INSERT INTO dbmaster.t_suratdana_bank SELECT * FROM $tmp01"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query = "UPDATE dbmaster.t_suratdana_bank SET parentidbank='$kodenya' WHERE idinputbank='$pidbank' LIMIT 1"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        if ($pbolehsimpanbr=="Y") {
            
            if ($pdivisi=="OTC"){
                $query = "UPDATE hrd.br_otc SET noslip='$nnoslip', tgltrans='$ptglinput' WHERE brOtcId='$pidbr' LIMIT 1";
            }else{
                $query = "UPDATE hrd.br0 SET noslip='$nnoslip', tgltrans='$ptglinput' WHERE brId='$pidbr' LIMIT 1";
            }
            mysqli_query($cnmy, $query);
            
        }
        
        $berhasil="berhasil";
    }
    
}

mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
mysqli_close($cnmy);
echo $berhasil;
exit;
?>

