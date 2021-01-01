<?php

    session_start();
if ($_GET['module']=="simpan") {
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
        
        $icarapv2="";
        $papvfin2="";
        $papvfin_gbr2="";
        $icarapv3="";
        $papvfin3="";
        $papvfin_gbr3="";
        $icarapv4="";
        $papvfin4="";
        $papvfin_gbr4="";
        
        $query="SELECT apv2, apvtgl2, apvgbr2, apv3, apvtgl3, apvgbr3, apv4, apvtgl4, apvgbr4 from dbmaster.t_spg_gaji_br0 WHERE idbrspg IN $pnoid AND IFNULL(apvgbr2,'')<>'' AND IFNULL(apvgbr3,'')<>''";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $tr= mysqli_fetch_array($tampil);
            $icarapv2=$tr['apv2'];
            $papvfin2=$tr['apvtgl2'];
            $papvfin_gbr2=$tr['apvgbr2'];
            
            $icarapv3=$tr['apv3'];
            $papvfin3=$tr['apvtgl3'];
            $papvfin_gbr3=$tr['apvgbr3'];
            
            $icarapv4=$tr['apv4'];
            $papvfin4=$tr['apvtgl4'];
            $papvfin_gbr4=$tr['apvgbr4'];
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
                    . " apv1, tgl_apv1, gbr_apv1, "
                    . " apv2, tgl_apv2, gbr_apv2, "
                    . " apv3, tgl_apv3, gbr_apv3)values"
                    . "('$kodenya', '$pdivisi', '$pkode', '$psubkode', '$pnomor', '$periode1', '$pdivno', '$pjumlah', "
                    . " '$pkaryawanid', '$pcoa', '$pjenis', '$periodef', '$periodet', '$pkodeperiode', '$pstspilihan', '$pkaryawanid', '$ppertipe', '$padvance',"
                    . " '$icarapv2', '$papvfin2', '$papvfin_gbr2', "
                    . " '$icarapv3', '$papvfin3', '$papvfin_gbr3', "
                    . " '$icarapv4', '$papvfin4', '$papvfin_gbr4')";
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
            
            
            $query = "UPDATE dbmaster.t_spg_gaji_br0 SET nodivisi='$pdivno' WHERE idbrspg IN $pnoid";
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
    echo $berhasil;
}elseif ($_GET['module']=="hapus") {
    /*
    include "../../config/koneksimysqli.php";
    
    $berhasil= "Tidak ada data yang dihapus....";
    
    $pnoid=$_POST['unoidbr'];
    
    if (!empty($pnoid)) {
        $query = "UPDATE dbmaster.t_spg_gaji_br0 SET nodivisi=NULL WHERE idbrspg IN $pnoid";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        $berhasil="Data berhasil dihapus";
    }
    echo $berhasil;
     * 
     */
}elseif ($_GET['module']=="xxx") {
    
}
    
    
?>
