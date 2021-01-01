<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $dbname = "dbmaster";
    
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $psimpanspd=$_POST['usimpanspd'];
    $pnospd=$_POST['unospd'];
    $pket=$_POST['uketerangan'];
    $kodestsinput="M";
    
    $berhasil = "Tidak ada data yang disimpan";
    
    $query = "SELECT nomor FROM dbmaster.t_suratdana_bank WHERE stsnonaktif<>'Y' AND nomor='$pnospd' AND stsinput='$kodestsinput'";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        echo $berhasil; exit;
    }
    
    
    
    $pnobukti=$_POST['ubukti'];
    $pnobukti2=$_POST['ubukti2'];
    $pbuktiperiode=$_POST['ubuktiperiode'];
    $pbuktithnbln=$_POST['ubuktithnbln'];
    
    
    if (empty($pnobukti)) $pnobukti=0;
    if (empty($pnobukti2)) $pnobukti2=0;

    
        $query = "SELECT nobbm FROM dbmaster.t_setup_bukti WHERE bulantahun='$pbuktiperiode'";
        $showkan= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($showkan);
        if ($ketemu==0){
            mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_bukti (bulantahun, nobbm)VALUES('$pbuktiperiode', '$pnobukti')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }else{

            $nox= mysqli_fetch_array($showkan);
            $pno_asli_bukti=$nox['nobbm'];
            if (empty($pno_asli_bukti)) $pno_asli_bukti="1500";
            $pno_asli_bukti=(double)$pno_asli_bukti+1;


            $isimpan_bukti=true;
            if ((double)$pnobukti==(double)$pnobukti2){
                $pnobukti=$pno_asli_bukti;//dibuat sama karena tidak ada perubahan
            }elseif ((double)$pnobukti<(double)$pnobukti2){
                $isimpan_bukti=false;
            }else{
            }


            if ($isimpan_bukti==true) {
                mysqli_query($cnmy, "UPDATE dbmaster.t_setup_bukti SET nobbm='$pnobukti' WHERE bulantahun='$pbuktiperiode'");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }

        }
    $pnobukti="BBM".$pnobukti.$pbuktithnbln;
    //echo $pnobukti; exit;
    
    $pjumlah=$_POST['umintadana'];
    $pjumlah=str_replace(",","", $pjumlah);
    
    $ptgl01 = str_replace('/', '-', $_POST['utglmasuk']);
    $ptglmasuk= date("Y-m-d", strtotime($ptgl01));
    
    $pcoa="000";//intransit sby    
    
    
    if ($module=="brdanabank" AND $act=="input") {
        //$berhasil="$module, $act, $idmenu : $psimpanspd, $pnospd, $pnobukti, $pket, $ptglmasuk";
        
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
                
        $pjenis="5";
        $psubkode="";
        $pidinputspd="";
        $pnodivisi="";
        $pdivisi="";
        $pstatus="1";
        
        $kodeawalbank=$kodenya;
        
        $query = "INSERT INTO $dbname.t_suratdana_bank (stsinput, idinputbank, tanggal, coa4, kodeid, subkode, idinput, nomor, nodivisi, "
                . " nobukti, divisi, sts, jumlah, keterangan, userid, parentidbank)values"
                . "('$kodestsinput', '$kodenya', '$ptglmasuk', '$pcoa', '$pjenis', '$psubkode', '$pidinputspd', '$pnospd', '$pnodivisi', "
                . " '$pnobukti', '$pdivisi', '$pstatus', '$pjumlah', '$pket', '$_SESSION[IDCARD]', '$kodenya')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
        //echo $query; exit;
        
        //if ($psimpanspd=="N") {
        /*
            $query="UPDATE $dbname.t_suratdana_bank SET stsinput='$kodestsinput', tanggal='$ptglmasuk', nobukti='$pnobukti', userid='$_SESSION[IDCARD]' WHERE nomor='$pnospd' AND IFNULL(stsnonaktif,'')<>'Y' AND stsinput='$kodestsinput'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $berhasil="";
        */  
            $query="UPDATE $dbname.t_suratdana_br SET tglmasuk='$ptglmasuk', nobbm='$pnobukti' WHERE nomor='$pnospd' AND IFNULL(stsnonaktif,'')<>'Y'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            $berhasil="";
        //}
        
        $kodestsinput="N";
        //if ($psimpanspd=="Y") {
            
            $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idinputbank,8)) as NOURUT from $dbname.t_suratdana_bank");
            $ketemu=  mysqli_num_rows($sql);
            $urut=1;
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                if (empty($o['NOURUT'])) $o['NOURUT']=0;
                $urut=$o['NOURUT']+1;
            }
            
            $nkode="";
            $query ="select distinct idinput, nomor, nodivisi, kodeid, subkode, divisi, jumlah FROM $dbname.t_suratdana_br WHERE "
                    . " IFNULL(stsnonaktif,'')<>'Y' AND nomor='$pnospd' AND CONCAT(idinput,nomor,nodivisi) NOT IN "
                    . " (select distinct CONCAT(idinput,nomor,nodivisi) FROM $dbname.t_suratdana_bank WHERE "
                    . " nomor='$pnospd' AND IFNULL(stsnonaktif,'')<>'Y' AND stsinput='$kodestsinput')";
            $showkan= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($showkan);
            if ($ketemu>0){
                $awal=8; $kodenya="";
                while ($row= mysqli_fetch_array($showkan)) {
                    
                    $jml=  strlen($urut);
                    $nawal=$awal-$jml;
                    $kodenya="BN".str_repeat("0", $nawal).$urut;
                    
                    $pjenis=$row['kodeid'];
                    $psubkode=$row['subkode'];
                    $pidinputspd=$row['idinput'];
                    $pnodivisi=$row['nodivisi'];
                    $pdivisi=$row['divisi'];
                    $pjumlah=$row['jumlah'];
                    $pstatus="1"; //setor / retur
                    
                    $query = "INSERT INTO $dbname.t_suratdana_bank (stsinput, idinputbank, tanggal, coa4, kodeid, subkode, idinput, nomor, nodivisi, "
                            . " nobukti, divisi, sts, jumlah, keterangan, userid, parentidbank)values"
                            . "('$kodestsinput', '$kodenya', '$ptglmasuk', '$pcoa', '$pjenis', '$psubkode', '$pidinputspd', '$pnospd', '$pnodivisi', "
                            . " '$pnobukti', '$pdivisi', '$pstatus', '$pjumlah', '$pket', '$_SESSION[IDCARD]', '$kodeawalbank')";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    //$berhasil .="$urut : $kodenya, "; 
                    $urut++;
                }
                $berhasil="";
            }
        //}
        
            
            
        //include "../../config/koneksimysqli_it.php";
        $now=date("mdYhis");
        $tmp01 =" dbtemp.RINPBANK01_".$_SESSION['USERID']."_$now ";
    
        $query = "select a.divisi, a.nomor, a.nodivisi, b.bridinput, b.kodeinput from dbmaster.t_suratdana_br a JOIN dbmaster.t_suratdana_br1 b 
            on a.idinput=b.idinput
            WHERE a.nomor='$pnospd' AND a.stsnonaktif<>'Y' and IFNULL(b.bridinput,'') <> ''
            and a.subkode NOT IN ('21', '22', '23', '03', '05', '')
            AND a.divisi IN ('PIGEO', 'PEACO', 'HO', 'EAGLE', 'OTC')";
        $query = "create TEMPORARY table $tmp01 ($query)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        
        //ETHICAL br0
        $query= "UPDATE hrd.br0 set sby='Y', tglrpsby='$ptglmasuk' WHERE brId in "
                . " (select DISTINCT IFNULL(bridinput,'') bridinput from $tmp01 WHERE divisi<>'OTC' AND kodeinput<>'E') AND "
                . " IFNULL(sby,'')<>'Y' AND IFNULL(tglrpsby,'0000-00-00')='0000-00-00'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        //OTC br_otc
        $query= "UPDATE hrd.br_otc set sby='Y', tglrpsby='$ptglmasuk' WHERE brOtcId in "
                . " (select DISTINCT IFNULL(bridinput,'') bridinput from $tmp01 WHERE divisi='OTC') AND "
                . " IFNULL(sby,'')<>'Y' AND IFNULL(tglrpsby,'0000-00-00')='0000-00-00'";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        //EAGLE KLAIM
        $query= "UPDATE hrd.klaim set sby='Y', tglrpsby='$ptglmasuk' WHERE klaimId in "
                . " (select DISTINCT IFNULL(bridinput,'') bridinput from $tmp01 WHERE divisi='EAGLE' AND kodeinput='E') AND "
                . " IFNULL(sby,'')<>'Y' AND IFNULL(tglrpsby,'0000-00-00')='0000-00-00'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapudata; }
        
        hapudata:
            mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
        
    }
    
    
    mysqli_close($cnmy);
    echo $berhasil;
?>
