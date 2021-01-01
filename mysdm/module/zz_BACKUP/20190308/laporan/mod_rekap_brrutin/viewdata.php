<?php
session_start();
if ($_GET['module']=="caribuktisudahada"){
    include "../../../config/koneksimysqli.php";
    $dbname="dbmaster";
    
    $pperiode=$_POST['uperiode'];
    $tgl01=$_POST['utgl'];

    $tglinput = date("Y-m-01", strtotime($tgl01));
    if ($pperiode==2) $tglinput = date("Y-m-16", strtotime($tgl01));
    
    $pkode="1";
    $psubkode="03";
    
    
    $query = "SELECT nodivisi as pnomor "
            . " FROM $dbname.t_suratdana_br WHERE kodeid='$pkode' AND subkode='$psubkode' AND "
            . " tgl='$tglinput' LIMIT 1";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $s= mysqli_fetch_array($showkan);
        if (!empty($s['pnomor'])) { 
            echo $s['pnomor']; exit;
        }
    }
    
}elseif ($_GET['module']=="viewnomorbukti"){
    include "../../../config/koneksimysqli.php";
    $dbname="dbmaster";
    
    $pperiode=$_POST['uperiode'];
    $tgl01=$_POST['utgl'];

    $tglinput = date("Y-m-01", strtotime($tgl01));
    if ($pperiode==2) $tglinput = date("Y-m-16", strtotime($tgl01));
    
    
    $bl= date("m", strtotime($tgl01));
    $byear= date("y", strtotime($tgl01));
    $bl=(int)$bl;
    $blromawi="I";
    if ($bl==1) $blromawi="I";
    if ($bl==2) $blromawi="II";
    if ($bl==3) $blromawi="III";
    if ($bl==4) $blromawi="IV";
    if ($bl==5) $blromawi="V";
    if ($bl==6) $blromawi="VI";
    if ($bl==7) $blromawi="VII";
    if ($bl==8) $blromawi="VIII";
    if ($bl==9) $blromawi="IX";
    if ($bl==10) $blromawi="X";
    if ($bl==11) $blromawi="XI";
    if ($bl==12) $blromawi="XII";
    
    $pkode="1";
    $psubkode="03";
    
    $nobuktinya="";
    $tno=1;
    $awal=3;

    
    $query = "SELECT nodivisi as pnomor "
            . " FROM $dbname.t_suratdana_br WHERE kodeid='$pkode' AND subkode='$psubkode' AND "
            . " tgl='$tglinput' LIMIT 1";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $s= mysqli_fetch_array($showkan);
        if (!empty($s['pnomor'])) { 
            echo $s['pnomor']; exit;
        }
    }
    
    
    
    $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as pnomor "
            . " FROM $dbname.t_suratdana_br WHERE stsnonaktif<>'Y' AND kodeid='$pkode' AND subkode='$psubkode'";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $sh= mysqli_fetch_array($showkan);
        if (!empty($sh['pnomor'])) { $tno=(INT)$sh['pnomor']; $tno++; }
    }

    $jml=  strlen($tno);
    $awal=$awal-$jml;

    if ($awal>=0)
        $tno=str_repeat("0", $awal).$tno;
    else
        $tno=$tno;


    $noslipurut=$tno."/RUTIN/".$blromawi."/".$byear;


    $nobuktinya=$noslipurut;

    echo $nobuktinya;
    
    
}elseif ($_GET['module']=="hitungtotaldata"){
    include "../../../config/koneksimysqli.php";
    $cnit=$cnmy;
    
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    
    $fperiode = " AND DATE_FORMAT(br.bulan, '%Y-%m') = '$periode1' ";
    
    $per1 = date("F Y", strtotime($tgl01));
    $pbulan = date("F", strtotime($tgl01));
    
    $kdperiode = $_POST['e_periode'];
    $stsapv = $_POST['sts_apv'];
    
    $fstsapv = "";
    if ($stsapv == "fin") {
        $fstsapv = " AND ifnull(br.tgl_fin,'') <> '' AND ifnull(br.tgl_fin,'0000-00-00') <> '0000-00-00' ";
    }elseif ($stsapv == "belumfin") {
        $fstsapv = " AND (ifnull(br.tgl_fin,'') = '' OR ifnull(br.tgl_fin,'0000-00-00') = '0000-00-00') ";
    }
    
    $ptotcan=0;
    $ptoteagle=0;
    $ptotho=0;
    $ptotpeaco=0;
    $ptotpigeo=0;
    $ptototh=0;
    $pgtotal=0;
    
    $query = "SELECT br.divisi, sum(br.jumlah) jumlah 
        FROM dbmaster.t_brrutin0 AS br WHERE br.kode=1 AND br.stsnonaktif <> 'Y' AND br.divisi <> 'OTC' 
        AND br.kodeperiode='$kdperiode' $fperiode $fstsapv GROUP BY 1";
    $result = mysqli_query($cnit, $query);
    $records = mysqli_num_rows($result);
    if ($records>0){
        while ($sh= mysqli_fetch_array($result)) {
            $ndivisi=$sh['divisi'];
            $njml=$sh['jumlah'];
            if ($ndivisi=="CAN") $ptotcan=$sh['jumlah'];
            elseif ($ndivisi=="EAGLE") $ptoteagle=$sh['jumlah'];
            elseif ($ndivisi=="HO") $ptotho=$sh['jumlah'];
            elseif ($ndivisi=="PEACO") $ptotpeaco=$sh['jumlah'];
            elseif ($ndivisi=="PIGEO") $ptotpigeo=$sh['jumlah'];
            else $ptototh=$ptototh+$sh['jumlah'];
        }
        $pgtotal=$ptotcan+$ptoteagle+$ptotho+$ptotpeaco+$ptotpigeo+$ptototh;
        
        $ptotcan=number_format($ptotcan,0,",",",");
        $ptoteagle=number_format($ptoteagle,0,",",",");
        $ptotho=number_format($ptotho,0,",",",");
        $ptotpeaco=number_format($ptotpeaco,0,",",",");
        $ptotpigeo=number_format($ptotpigeo,0,",",",");
        $ptototh=number_format($ptototh,0,",",",");

        $pgtotal=number_format($pgtotal,0,",",",");
    }
    
    $piddata = $_POST['uiddata'];
    
    $bolehsimpan=true;
    $piddata = $_POST['uiddata'];
    if (!empty($piddata)) {
        $pkode="1";
        $psubkode="03";

        $tglinput = date("Y-m-01", strtotime($tgl01));
        if ($kdperiode==2) $tglinput = date("Y-m-16", strtotime($tgl01));
    
        $query = "SELECT tgl_proses FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND kodeid='$pkode' AND "
                . " subkode='$psubkode' and nodivisi='$piddata' AND tgl='$tglinput'";
        $result = mysqli_query($cnit, $query);
        $records = mysqli_num_rows($result);
        if ($records>0){
            $s= mysqli_fetch_array($result);
            if (!empty($s['tgl_proses'])) {
                if ($s['tgl_proses']<>'0000-00-00') $bolehsimpan=false;
            }
        }
    }
    ?>
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total CAN : <span class='required'></span></label>
        <div class='col-xs-9'>
            <input type='text' id='e_totcan' name='e_totcan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptotcan; ?>' readonly>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total EAGLE : <span class='required'></span></label>
        <div class='col-xs-9'>
            <input type='text' id='e_totegl' name='e_totegl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptoteagle; ?>' readonly>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total HO : <span class='required'></span></label>
        <div class='col-xs-9'>
            <input type='text' id='e_totho' name='e_totho' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptotho; ?>' readonly>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total PEACO : <span class='required'></span></label>
        <div class='col-xs-9'>
            <input type='text' id='e_totpeac' name='e_totpeac' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptotpeaco; ?>' readonly>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total PIGEO : <span class='required'></span></label>
        <div class='col-xs-9'>
            <input type='text' id='e_totpeog' name='e_totpeog' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptotpigeo; ?>' readonly>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>OTHER : <span class='required'></span></label>
        <div class='col-xs-9'>
            <input type='text' id='e_tototh' name='e_tototh' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptototh; ?>' readonly>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Grand Total : <span class='required'></span></label>
        <div class='col-xs-9'>
            <input type='text' id='e_tot' name='e_tot' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pgtotal; ?>' readonly>
        </div>
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
        <div class='col-xs-9'>
            <?php if ($bolehsimpan==true) { ?>
                <button type='button' class='btn btn-success' onclick='simpan_data("Simpan ?", "")'>Simpan Pengajuan Dana</button>
                <?php if (!empty($piddata)) { ?>
                    <button type='button' class='btn btn-danger' onclick='hapus_data("Hapus ?", "")'>Hapus</button>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <?PHP
        
}elseif ($_GET['module']=="simpandata"){
    include "../../../config/koneksimysqli.php";
    $cnit=$cnmy;
    $dbname="dbmaster";
    
    $pact=$_GET['act'];
    
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    
    
    $fperiode = " AND DATE_FORMAT(br.bulan, '%Y-%m') = '$periode1' ";
    
    $per1 = date("F Y", strtotime($tgl01));
    $pbulan = date("F", strtotime($tgl01));
    
    $kdperiode = $_POST['e_periode'];
    $stsapv = $_POST['sts_apv'];
    
    $tglinput = date("Y-m-01", strtotime($tgl01));
    
    $myinpperiode1= date("Y-m-01", strtotime($tgl01));
    $myinpperiode2= date("Y-m-15", strtotime($tgl01));
        
    if ($kdperiode==2) {
        $tglinput = date("Y-m-16", strtotime($tgl01));
        
        $myinpperiode1= date("Y-m-16", strtotime($tgl01));
        $myinpperiode2= date("Y-m-t", strtotime($tgl01));
    }
    
    $fstsapv = "";
    if ($stsapv == "fin") {
        $fstsapv = " AND ifnull(br.tgl_fin,'') <> '' AND ifnull(br.tgl_fin,'0000-00-00') <> '0000-00-00' ";
    }elseif ($stsapv == "belumfin") {
        $fstsapv = " AND (ifnull(br.tgl_fin,'') = '' OR ifnull(br.tgl_fin,'0000-00-00') = '0000-00-00') ";
    }
    
    $ptotcan=0;
    $ptoteagle=0;
    $ptotho=0;
    $ptotpeaco=0;
    $ptotpigeo=0;
    $ptototh=0;
    $pgtotal=0;
    $pjumlah=0;
    
    $berhasilsimpan="Tidak ada data yang tersimpan...";
    
    $pdivno = $_POST['unobukti'];
    $piddata = $_POST['uiddata'];

    $pcoa="101-02-002";
    $pkode="1";
    $psubkode="03";
    $userid=$_SESSION['IDCARD'];
    if (empty($userid)) $userid="0000000000";
    
    if (!empty($piddata)) {
        $query="DELETE FROM $dbname.t_suratdana_br WHERE nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' AND kodeperiode='$kdperiode'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
    }
    
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DRSPDDRTN01_".$userid."_$now$milliseconds ";
    
    $query = "SELECT br.idrutin, br.divisi, sum(br.jumlah) jumlah 
        FROM dbmaster.t_brrutin0 AS br WHERE br.kode=1 AND br.stsnonaktif <> 'Y' AND br.divisi <> 'OTC' 
        AND br.kodeperiode='$kdperiode' $fperiode $fstsapv GROUP BY 1, 2";
    $query = "create temporary table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
    
    
    $query = "SELECT '' divisi, sum(jumlah) jumlah FROM $tmp01";// GROUP BY 1 ORDER BY divisi
    $result = mysqli_query($cnit, $query);
    $records = mysqli_num_rows($result);
    if ($records>0){
        while ($sh= mysqli_fetch_array($result)) {
            $ndivisi=$sh['divisi'];
            $njml=$sh['jumlah'];
            if ($ndivisi=="CAN") $ptotcan=$sh['jumlah'];
            elseif ($ndivisi=="EAGLE") $ptoteagle=$sh['jumlah'];
            elseif ($ndivisi=="HO") $ptotho=$sh['jumlah'];
            elseif ($ndivisi=="PEACO") $ptotpeaco=$sh['jumlah'];
            elseif ($ndivisi=="PIGEO") $ptotpigeo=$sh['jumlah'];
            else $ptototh=$sh['jumlah'];
            
            $pjumlah=$sh['jumlah'];
            
            $ncari="select MAX(idinput) as NOURUT from $dbname.t_suratdana_br";
            $sql=  mysqli_query($cnmy, $ncari);
            $ketemu=  mysqli_num_rows($sql);
            $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $urut=$o['NOURUT']+1;
                $kodenya=$urut;
            }
            
            if (!empty($kodenya)){
                $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, tgl, nodivisi, jumlah, userid, coa4, kodeperiode, tglf, tglt)values"
                        . "('$kodenya', '$ndivisi', '$pkode', '$psubkode', '$tglinput', '$pdivno', '$pjumlah', '$userid', '$pcoa', '$kdperiode', '$myinpperiode1', '$myinpperiode2')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
                
                
                //simpan detail
                $purutan=1;
                $pkodeurutan=1;
                $kodeinput="F";//KODE BR RUTIN
                
                $query="SELECT DISTINCT ifnull(idrutin,'') nobrid from $tmp01 order by idrutin";
                $tampil= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ($ketemu>0) {
                    while ($tr= mysqli_fetch_array($tampil)) {
                        $nobrinput=$tr['nobrid'];
                        //eksekusi input
                        $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, urutan)VALUES"
                                . "('$kodenya', '$nobrinput', '$kodeinput', '$pkodeurutan')";
                        mysqli_query($cnmy, $query);
                        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                        if ($purutan==30) {
                            $purutan=0;
                            $pkodeurutan++;
                        }
                        $purutan++;
                    }

                }
                
                //END simpan detail
                
                
                $berhasilsimpan="Data berhasil disimpan.";
            }
            $pjumlah=0;
        }
        
        
        if (!empty($kodenya)){
            
            $query = "SELECT divisi, sum(jumlah) jumlah FROM $tmp01 GROUP BY 1 ORDER BY divisi";
            $result2 = mysqli_query($cnit, $query);
            $records2 = mysqli_num_rows($result2);
            if ($records2>0){
                while ($sh= mysqli_fetch_array($result2)) {
                    $ndivisi=$sh['divisi'];
                    $pjumlah=$sh['jumlah'];
                    
                    $query = "INSERT INTO $dbname.t_suratdana_br_d (idinput, divisi, jumlah)values"
                            . "('$kodenya', '$ndivisi', '$pjumlah')";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
                    
                    
                }
            }
            
        }
        
        
        
    }
    
    mysqli_query($cnit, "drop temporary table $tmp01");
                    
    echo $berhasilsimpan; exit;
    
}elseif ($_GET['module']=="hapusdata"){
    include "../../../config/koneksimysqli.php";
    $cnit=$cnmy;
    $dbname="dbmaster";
    
    $pact=$_GET['act'];
    
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    
    $per1 = date("F Y", strtotime($tgl01));
    $pbulan = date("F", strtotime($tgl01));
    
    $kdperiode = $_POST['e_periode'];
    $stsapv = $_POST['sts_apv'];
    
    $tglinput = date("Y-m-01", strtotime($tgl01));
    if ($kdperiode==2) $tglinput = date("Y-m-16", strtotime($tgl01));
    
    $berhasilsimpan="Tidak ada data yang dihapus...";
    
    $pdivno = $_POST['unobukti'];
    $piddata = $_POST['uiddata'];

    $pcoa="101-02-002";
    $pkode="1";
    $psubkode="03";
           
    if (!empty($piddata)) {
        $query="DELETE FROM $dbname.t_suratdana_br_d WHERE idinput IN ("
                . " select distinct idinput FROM $dbname.t_suratdana_br WHERE "
                . " nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' AND kodeperiode='$kdperiode' AND IFNULL(tgl_proses,'') = '' "
                . ")";
        mysqli_query($cnmy, $query);
        
        $query="DELETE FROM $dbname.t_suratdana_br1 WHERE idinput IN ("
                . " select distinct idinput FROM $dbname.t_suratdana_br WHERE "
                . " nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' AND kodeperiode='$kdperiode' AND IFNULL(tgl_proses,'') = '' "
                . ")";
        mysqli_query($cnmy, $query);
        
        $query="DELETE FROM $dbname.t_suratdana_br WHERE nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' AND kodeperiode='$kdperiode' AND IFNULL(tgl_proses,'') = ''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
        $berhasilsimpan="Data berhasil dihapus...";
    }
    echo $berhasilsimpan;
    
}

?>
