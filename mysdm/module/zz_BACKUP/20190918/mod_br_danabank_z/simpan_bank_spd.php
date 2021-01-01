<?php

    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $dbname = "dbmaster";
    
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    
    $berhasil="";
    if ($module=="brdanabank" AND $act=="hapus") {
        $berhasil="Tidak ada data yang dihapus";
        $pidinput=$_POST['uid'];
        
        if (!empty($pidinput)) {
            /*
            $query="UPDATE $dbname.t_suratdana_br SET nobbm='' WHERE CONCAT(idinput, nomor, nodivisi) IN ( "
                    . "select CONCAT(idinput, nomor, nodivisi) FROM $dbname.t_suratdana_bank WHERE idinputbank='$pidinput')";
            */
            $query = "UPDATE $dbname.t_suratdana_br SET nobbm='' WHERE nomor='$pidinput' AND stsnonaktif<>'Y'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $query="UPDATE $dbname.t_suratdana_bank SET stsnonaktif='Y' WHERE nomor='$pidinput' AND stsinput IN ('M', 'N')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $berhasil="";
        }
        
        echo $berhasil;
        exit;
    }
    
    $kodestsinput="N";
    
    $pidinput=$_POST['uid'];
    $pidinputspd=$_POST['uidinputspd'];
    $pnospd=$_POST['unospd'];
    $pnodivisi=$_POST['unodiv'];
    $pjml=$_POST['ujml'];
    $pket=$_POST['uketerangan'];
    $pnobukti=$_POST['unobukti'];
    
    $ptgl01 = str_replace('/', '-', $_POST['utglmasuk']);
    $ptglmasuk= date("Y-m-d", strtotime($ptgl01));
    $pjumlah=str_replace(",","", $pjml);
    
    
    $pjenis="";
    $psubkode="";
    //$pcoa="000-0";//intransit jkt 
    $pcoa="000";//intransit sby
    $pdivisi="HO";
    $pstatus="1";
    
    $pnobrid="";
    $pnoslip="";
    
    
    $berhasil="Tidak ada data yang disimpan";
    
    if ($module=="brdanabank") {
        
        $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_suratdana_br WHERE idinput='$pidinputspd'");
        $r    = mysqli_fetch_array($edit);
        $pjenis=$r['kodeid'];//kodeid
        $psubkode=$r['subkode'];//subkode
        //$pcoa=$r['coa4'];
        //$pcoa="000-0";//intransit jkt
        $pcoa="000";//intransit sby
        $pdivisi=$r['divisi'];//pengajuan
        
        if (empty($pnospd)) {//jika kosong maka cari nomor spd sesuai  no br / divisi
            $pnospd=$r['nomor'];
        }
        
        
        
        if ($act=="input") {
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
        }else{
            $kodenya=$pidinput;
        }
        
        //echo "$act : $kodenya, $ptglmasuk, $pcoa, $pjenis, $psubkode, $pidinputspd, $pnospd, $pnodivisi, $pnobukti, $pdivisi, $pstatus, $pjumlah, $pket, $pnobrid, $pnoslip, $_SESSION[IDCARD]"; exit;
        
        if ($act=="input") {
            $query = "INSERT INTO $dbname.t_suratdana_bank (stsinput, idinputbank, tanggal, coa4, kodeid, subkode, idinput, nomor, nodivisi, "
                    . " nobukti, divisi, sts, jumlah, keterangan, brid, noslip, userid)values"
                    . "('$kodestsinput', '$kodenya', '$ptglmasuk', '$pcoa', '$pjenis', '$psubkode', '$pidinputspd', '$pnospd', '$pnodivisi', "
                    . " '$pnobukti', '$pdivisi', '$pstatus', '$pjumlah', '$pket', '$pnobrid', '$pnoslip', '$_SESSION[IDCARD]')";
        }else{
            $query = "UPDATE $dbname.t_suratdana_bank SET stsinput='$kodestsinput', tanggal='$ptglmasuk', "
                    . " coa4='$pcoa', kodeid='$pjenis', subkode='$psubkode', idinput='$pidinputspd', nomor='$pnospd', nodivisi='$pnodivisi', "
                    . " nobukti='$pnobukti', divisi='$pdivisi', sts='$pstatus', jumlah='$pjumlah', "
                    . " keterangan='$pket', brid='$pnobrid', noslip='$pnoslip', userid='$_SESSION[IDCARD]' WHERE "
                    . " idinputbank='$kodenya'";
        }

        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $query="UPDATE $dbname.t_suratdana_br SET tglmasuk='$ptglmasuk', nobbm='$pnobukti' WHERE nomor='$pnospd' AND nodivisi='$pnodivisi' AND IFNULL(stsnonaktif,'')<>'Y'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        
        $berhasil="";
    }
    
    
    mysqli_close($cnmy);
    echo $berhasil;
    
?>
