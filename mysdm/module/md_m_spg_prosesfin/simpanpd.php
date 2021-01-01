<?php

    session_start();
if ($_GET['module']=="simpan") {
    
    $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "ada harus login ulang...."; exit;
    }
    
    include "../../config/koneksimysqli.php";
    
    
    //$cnmy=$cnit;
    $dbname = "dbmaster";
    $berhasil = "Tidak ada data yang disimpan....";
    
    $sql=  mysqli_query($cnmy, "select MAX(idinput) as NOURUT from $dbname.t_suratdana_br");
    $ketemu=  mysqli_num_rows($sql);
    $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
    if ($ketemu>0){
        $o=  mysqli_fetch_array($sql);
        $urut=$o['NOURUT']+1;
        $kodenya=$urut;
    }
    
    $ptgl = $_POST['uperiode'];
    $pperiod=$_POST['utglpengajuan'];
    $pnoid=$_POST['unoidbr'];
    $pdivno=$_POST['unobrdiv'];
    
    if (!empty($kodenya) AND !empty($ptgl) AND !empty($pperiod) AND !empty($pnoid)) {
        
        
        $periode1= date("Y-m-d", strtotime($pperiod));
        
        
        $periodef= date("Y-m-01", strtotime($ptgl));
        $periodet= date("Y-m-t", strtotime($ptgl));
        
        $pdivisi="OTC";
        $pcoa="101-02-002";
        $pkode="1";
        $psubkode="02";
        $pnomor="";
        
        $pjenis="Y";
        $pkodeperiode="";
        $pstspilihan="";
        $ppertipe="I";
        $padvance="A";
        
        $pjumlah=0;
        
        $pkaryawanid=$_SESSION['IDCARD'];
        
        $icarapv1="";
        $papvfin1="";
        $papvfin_gbr1="";
        
        $apvid=$_SESSION['IDCARD'];
        $gbrapv=$_POST['uttd'];
        
        $query = "UPDATE dbmaster.t_spg_gaji_br0 set apv3='$apvid', apvtgl3=NOW(), apvgbr3='$gbrapv' WHERE "
                . " idbrspg IN $pnoid";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "ERROR...."; exit; }
    
    
        $query="SELECT apv3, apvtgl3, apvgbr3 from dbmaster.t_spg_gaji_br0 WHERE idbrspg IN $pnoid AND IFNULL(apvgbr2,'')<>'' AND IFNULL(apvgbr3,'')<>''";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            //dari apv3 masuk ke surat dana jadi apv1
            $icarapv1=$tr['apv3'];
            $papvfin1=$tr['apvtgl3'];
            $papvfin_gbr1=$tr['apvgbr3'];
            

        }
        
        
        
        $query="SELECT SUM(total) as jumlah from dbmaster.t_spg_gaji_br0 WHERE idbrspg IN $pnoid";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            if (!empty($tr['jumlah'])) $pjumlah=$tr['jumlah'];
        }
        
        
        if ((DOUBLE)$pjumlah>0) {
            $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, nomor, tgl, nodivisi, jumlah, "
                    . " userid, coa4, lampiran, tglf, tglt, kodeperiode, sts, karyawanid, periodeby, jenis_rpt, "
                    . " apv1, tgl_apv1, gbr_apv1)values"
                    . "('$kodenya', '$pdivisi', '$pkode', '$psubkode', '$pnomor', '$periode1', '$pdivno', '$pjumlah', "
                    . " '$pkaryawanid', '$pcoa', '$pjenis', '$periodef', '$periodet', '$pkodeperiode', '$pstspilihan', '$pkaryawanid', '$ppertipe', '$padvance',"
                    . " '$icarapv1', '$papvfin1', '$papvfin_gbr1')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $query = "DELETE FROM $dbname.t_suratdana_br_d WHERE idinput='$kodenya'";
            mysqli_query($cnmy, $query);
    
            $query = "INSERT INTO $dbname.t_suratdana_br_d (idinput,divisi,jumlah)values('$kodenya', '$pdivisi', '$pjumlah')";
            mysqli_query($cnmy, $query);
            
            $query = "DELETE FROM $dbname.t_suratdana_br1 WHERE idinput='$kodenya'";
            mysqli_query($cnmy, $query);
            
            $purutan=1;
            $pkodeurutan=1;
            
            
            $query = "UPDATE dbmaster.t_spg_gaji_br0 SET nodivisi='$pdivno', idinput='$kodenya' WHERE idbrspg IN $pnoid";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            $query="SELECT idbrspg, total as jumlah from dbmaster.t_spg_gaji_br0 WHERE "
                    . " idbrspg IN $pnoid AND "
                    . " idbrspg NOT IN (SELECT DISTINCT ifnull(bridinput,'') from dbmaster.t_suratdana_br1 WHERE kodeinput='L') ORDER BY 1";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                while ($tr= mysqli_fetch_array($tampil)) {
                    
                    $kodeinput="L";
                    
                    $nobrinput=$tr['idbrspg'];
                    $namount=$tr['jumlah'];
                    if (empty($namount)) $namount = "0";

                    //eksekusi input
                    $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, urutan, amount)VALUES"
                            . "('$kodenya', '$nobrinput', '$kodeinput', '$pkodeurutan', '$namount')";
                    mysqli_query($cnmy, $query);
                    //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                    if ($purutan==30) {
                        $purutan=0;
                        $pkodeurutan++;
                    }
                    $purutan++;
                }

            }
        
            
            $berhasil = "Data berhasil disimpan....";
        }
        
    }
    mysqli_close($cnmy);
    echo $berhasil;
}elseif ($_GET['module']=="hapus") {
    $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "ada harus login ulang...."; exit;
    }
    
    
    include "../../config/koneksimysqli.php";
    
    $berhasil= "Tidak ada data yang dihapus....";
    
    $pkode="1";
    $psubkode="02";
        
    $pnodivisi=$_POST['unodivbr'];
    $pidinputspd=$_POST['uidinput'];
    $pperiode=$_POST['uperiode'];
    $periode1= date("Y-m", strtotime($pperiode));
    
    if (!empty($pidinputspd)) {
        $query = "UPDATE dbmaster.t_suratdana_br SET stsnonaktif='Y', userid='$puserid' WHERE nodivisi='$pnodivisi' AND idinput='$pidinputspd' AND kodeid='$pkode' AND subkode='$psubkode'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "UPDATE dbmaster.t_spg_gaji_br0 SET nodivisi=NULL, idinput=NULL, apv3=NULL, apvtgl3=NULL, apvgbr3=NULL WHERE nodivisi ='$pnodivisi' AND DATE_FORMAT(periode,'%Y-%m')='$periode1'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        $berhasil="Data berhasil dihapus";
    }
    
    mysqli_close($cnmy);
    echo $berhasil;
     
}elseif ($_GET['module']=="xxx") {
    
}
    
    
?>
