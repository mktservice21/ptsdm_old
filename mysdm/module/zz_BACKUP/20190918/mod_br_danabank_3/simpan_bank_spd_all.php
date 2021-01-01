<?php

    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $dbname = "dbmaster";
    
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $psimpanspd=$_POST['usimpanspd'];
    $pnospd=$_POST['unospd'];
    $pnobukti=$_POST['ubukti'];
    $pket=$_POST['uketerangan'];
    $ptgl01 = str_replace('/', '-', $_POST['utglmasuk']);
    $ptglmasuk= date("Y-m-d", strtotime($ptgl01));
    
    $pcoa="000";//intransit sby
    
    $kodestsinput="M";
    
    $berhasil = "Tidak ada data yang disimpan";
    
    
    
    if ($module=="brdanabank" AND $act=="input") {
        //$berhasil="$module, $act, $idmenu : $psimpanspd, $pnospd, $pnobukti, $pket, $ptglmasuk";
        
        //if ($psimpanspd=="N") {
            $query="UPDATE $dbname.t_suratdana_bank SET stsinput='$kodestsinput', tanggal='$ptglmasuk', nobukti='$pnobukti', userid='$_SESSION[IDCARD]' WHERE nomor='$pnospd' AND IFNULL(stsnonaktif,'')<>'Y' AND stsinput='$kodestsinput'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            $berhasil="";
            
            $query="UPDATE $dbname.t_suratdana_br SET tglmasuk='$ptglmasuk', nobbm='$pnobukti' WHERE nomor='$pnospd' AND IFNULL(stsnonaktif,'')<>'Y'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            
            $berhasil="";
        //}
        
        $kodenya="BN00000001";
        if ($psimpanspd=="Y") {
            
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
                            . " nobukti, divisi, sts, jumlah, keterangan, userid)values"
                            . "('$kodestsinput', '$kodenya', '$ptglmasuk', '$pcoa', '$pjenis', '$psubkode', '$pidinputspd', '$pnospd', '$pnodivisi', "
                            . " '$pnobukti', '$pdivisi', '$pstatus', '$pjumlah', '$pket', '$_SESSION[IDCARD]')";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    //$berhasil .="$urut : $kodenya, "; 
                    $urut++;
                }
                $berhasil="";
            }
        }
        
        
    }
    
    
    mysqli_close($cnmy);
    echo $berhasil;
?>
