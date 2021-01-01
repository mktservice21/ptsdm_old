<?php
session_start();
if ($_GET['module']=="caribuktisudahada"){
    include "../../../config/koneksimysqli.php";
    $dbname="dbmaster";
    
    $tgl01=$_POST['utgl'];

    $tglinput = date("Y-m-01", strtotime($tgl01));
    
    $pkode="2";
    $psubkode="21";
    
    $stsreport = $_POST['sts_rpt'];
    
    $query = "SELECT nodivisi as pnomor "
            . " FROM $dbname.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi<>'OTC' AND kodeid='$pkode' AND subkode='$psubkode' AND "
            . " tgl='$tglinput' AND sts='$stsreport' LIMIT 1";
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
    
    $stsreport = $_POST['sts_rpt'];
    
    $tgl01=$_POST['utgl'];

    $tglinput = date("Y-m-01", strtotime($tgl01));
    
    
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
    
    $pkode="2";
    $psubkode="21";
    
    $nobuktinya="";
    $tno=1;
    $awal=3;

    
    $query = "SELECT nodivisi as pnomor "
            . " FROM $dbname.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi<>'OTC' AND kodeid='$pkode' AND subkode='$psubkode' AND "
            . " tgl='$tglinput' AND sts='$stsreport' LIMIT 1";
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $s= mysqli_fetch_array($showkan);
        if (!empty($s['pnomor'])) { 
            echo $s['pnomor']; exit;
        }
    }
    
    
    
    $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as pnomor "
            . " FROM $dbname.t_suratdana_br WHERE stsnonaktif<>'Y' AND divisi<>'OTC' AND kodeid='$pkode' AND subkode='$psubkode'";
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


    $noslipurut=$tno."/LK/".$blromawi."/".$byear;


    $nobuktinya=$noslipurut;

    echo $nobuktinya;
    
    
}elseif ($_GET['module']=="hitungtotaldata"){
    include "../../../config/koneksimysqli.php";
    $cnit=$cnmy;
    
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    
    $fperiode = " AND ( (DATE_FORMAT(br.periode1, '%Y-%m') = '$periode1') OR (DATE_FORMAT(br.periode2, '%Y-%m') = '$periode1') ) ";
    
    $per1 = date("F Y", strtotime($tgl01));
    $pbulan = date("F", strtotime($tgl01));
    
    $stsapv = $_POST['sts_apv'];

    $fstsapv = "";
    if ($stsapv == "fin") {
        $fstsapv = " AND ifnull(br.tgl_fin,'') <> '' AND ifnull(br.tgl_fin,'0000-00-00') <> '0000-00-00' ";
    }elseif ($stsapv == "belumfin") {
        $fstsapv = " AND (ifnull(br.tgl_fin,'') = '' OR ifnull(br.tgl_fin,'0000-00-00') = '0000-00-00') ";
    }
    
    
    $stsreport = $_POST['sts_rpt'];
    
    $filstatuscls="";
    if (!empty($stsreport)) {
        $finsts=" AND idrutin IN ";
        if ($stsreport=="B") $finsts=" AND idrutin NOT IN ";
        
        $filstatuscls =" $finsts (select DISTINCT IFNULL(idrutin,'') idrutin from dbmaster.t_brrutin_ca_close WHERE "
                . " IFNULL(idrutin,'') <> '' AND DATE_FORMAT(bulan, '%Y-%m') = '$periode1' ";
        if ($stsreport=="C") {
            $filstatuscls=$filstatuscls." AND sts='C' ";
        }elseif ($stsreport=="S") {
            $filstatuscls=$filstatuscls." AND sts='S' ";
        }
        $filstatuscls=$filstatuscls." )";
    }
    
    $ptotcan=0;
    $ptoteagle=0;
    $ptotho=0;
    $ptotpeaco=0;
    $ptotpigeo=0;
    $ptototh=0;
    $pgtotal=0;
    
    
    $no=1;
    $gtotjumlah=0;
    $gtotca1=0;
    $gtotselisih=0;
    $gtotca2=0;
    $gtottrans=0;
    $nourutpilih="";
        
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTBLCS01_".$userid."_$now ";
    if ($stsreport=="C" OR $stsreport=="S"){
        
        $query = "SELECT * FROM dbmaster.t_brrutin_ca_close WHERE DATE_FORMAT(bulan,'%Y-%m')='$periode1' AND IFNULL(sts,'')='$stsreport'";
        $query = "create temporary table $tmp01 ($query)"; 
        mysqli_query($cnit, $query);
        $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $gtottrans=0;
        $query = "select distinct divisi, karyawanid, saldo, ca1, ca2 from $tmp01";
        $result = mysqli_query($cnit, $query);
        $records = mysqli_num_rows($result);
        if ($records) {
            while ($row = mysqli_fetch_array($result)) {
                $pselisih=$row['ca1']-$row['saldo'];
                $pjmltrans=$row['ca2']-$pselisih;
                if ($pselisih>0 AND $row['ca2']==0) $pjmltrans=0;
                elseif ($pselisih>0 AND $row['ca2']>0) $pjmltrans=$row['ca2'];
                elseif ($pselisih==0 AND $row['ca2']>0) $pjmltrans=$row['ca2'];
                $gtottrans=$gtottrans+$pjmltrans;
            }
            $gtottrans=number_format($gtottrans,0,",",",");
        }
        
        
    }
    
    
    
    if ($stsreport=="C" OR $stsreport=="S")
        $query = "SELECT divisi, sum(credit) jumlah FROM $tmp01 WHERE IFNULL(idrutin,'')<>'' AND DATE_FORMAT(bulan,'%Y-%m')='$periode1' AND IFNULL(sts,'')='$stsreport' GROUP BY 1";
    else
        $query = "SELECT br.divisi, sum(br.jumlah) jumlah FROM dbmaster.t_brrutin0 AS br WHERE br.kode=2 AND br.stsnonaktif <> 'Y' AND br.divisi <> 'OTC' $fperiode $fstsapv $filstatuscls GROUP BY 1";
    
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
        
        if ((INT)$gtottrans==0) $gtottrans=$pgtotal;
    }
    
    
    $bolehsimpan=true;
    $piddata = $_POST['uiddata'];
    if (!empty($piddata)) {
        $pkode="2";
        $psubkode="21";
    
        $query = "SELECT tgl_proses FROM dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND kodeid='$pkode' AND subkode='$psubkode' and nodivisi='$piddata'";
        $result = mysqli_query($cnit, $query);
        $records = mysqli_num_rows($result);
        if ($records>0){
            $s= mysqli_fetch_array($result);
            if (!empty($s['tgl_proses'])) {
                if ($s['tgl_proses']<>'0000-00-00') $bolehsimpan=false;
            }
        }
    }
    
    mysqli_query($cnit, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
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

    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah Transfer : <span class='required'></span></label>
        <div class='col-xs-9'>
            <input type='text' id='e_tottrans' name='e_tottrans' class='form-control col-md-7 col-xs-12' value='<?PHP echo $gtottrans; ?>' readonly>
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
    
    if (empty($_SESSION['JMLRECSPD'])) $_SESSION['JMLRECSPD']=30;
    
    $pact=$_GET['act'];
    
    $tglnow = date("d/m/Y");
    $tgl01 = $_POST['bulan1'];
    $periode1 = date("Y-m", strtotime($tgl01));
    
    
    $fperiode = " AND ( (DATE_FORMAT(br.periode1, '%Y-%m') = '$periode1') OR (DATE_FORMAT(br.periode2, '%Y-%m') = '$periode1') ) ";
    
    $per1 = date("F Y", strtotime($tgl01));
    $pbulan = date("F", strtotime($tgl01));
    
    $stsapv = $_POST['sts_apv'];
    
    $pjmltrans=0;
    if (!empty($_POST['utotrans'])) $pjmltrans=str_replace(",","", $_POST['utotrans']);
    
    $tglinput = date("Y-m-01", strtotime($tgl01));
    
    $myinpperiode1= date("Y-m-01", strtotime($tgl01));
    $myinpperiode2= date("Y-m-t", strtotime($tgl01));
    
    $fstsapv = "";
    if ($stsapv == "fin") {
        $fstsapv = " AND ifnull(br.tgl_fin,'') <> '' AND ifnull(br.tgl_fin,'0000-00-00') <> '0000-00-00' ";
    }elseif ($stsapv == "belumfin") {
        $fstsapv = " AND (ifnull(br.tgl_fin,'') = '' OR ifnull(br.tgl_fin,'0000-00-00') = '0000-00-00') ";
    }
    
    $stsreport = $_POST['sts_rpt'];
    
    $filstatuscls="";
    if (!empty($stsreport)) {
        $finsts=" AND br.idrutin IN ";
        if ($stsreport=="B") $finsts=" AND idrutin NOT IN ";
        
        $filstatuscls =" $finsts (select DISTINCT IFNULL(idrutin,'') idrutin from dbmaster.t_brrutin_ca_close WHERE "
                . " IFNULL(idrutin,'') <> '' AND DATE_FORMAT(bulan, '%Y-%m') = '$periode1' ";
        if ($stsreport=="C") {
            $filstatuscls=$filstatuscls." AND sts='C' ";
        }elseif ($stsreport=="S") {
            $filstatuscls=$filstatuscls." AND sts='S' ";
        }
        $filstatuscls=$filstatuscls." )";
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
    $pkode="2";
    $psubkode="21";
    $userid=$_SESSION['IDCARD'];
    if (empty($userid)) $userid="0000000000";
    
    if (!empty($piddata)) {
        
        $query="DELETE FROM $dbname.t_suratdana_br_d WHERE idinput IN ("
                . " select distinct idinput FROM $dbname.t_suratdana_br WHERE "
                . " nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' "
                . " )";
        mysqli_query($cnmy, $query);
        
        $query="DELETE FROM $dbname.t_suratdana_br1_a WHERE idinput IN ("
                . " select distinct idinput FROM $dbname.t_suratdana_br WHERE "
                . " nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' "
                . ")";
        mysqli_query($cnmy, $query);
        
        $query="DELETE FROM $dbname.t_suratdana_br1_b WHERE idinput IN ("
                . " select distinct idinput FROM $dbname.t_suratdana_br WHERE "
                . " nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' "
                . ")";
        mysqli_query($cnmy, $query);
        
        $query="DELETE FROM $dbname.t_suratdana_br1 WHERE idinput IN ("
                . " select distinct idinput FROM $dbname.t_suratdana_br WHERE "
                . " nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' "
                . ")";
        mysqli_query($cnmy, $query);
        
        
        $query="DELETE FROM $dbname.t_suratdana_br WHERE nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
    }
    
    
    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DRSPDDRTN01_".$userid."_$now$milliseconds ";
    $tmp02 =" dbtemp.DRSPDDRTN02_".$userid."_$now$milliseconds ";
    
    /*
    $query = "SELECT idrutin, divisi, idca1, ca1, idca2, ca2, sum(credit) as jumlah 
        FROM dbmaster.t_brrutin_ca_close WHERE DATE_FORMAT(bulan,'%Y-%m')='$periode1' AND IFNULL(sts,'')='$stsreport' 
        GROUP BY 1,2,3,4,5,6";
    $query = "create  table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
    
    mysqli_query($cnit, "UPDATE $tmp01 set idrutin=idca1, jumlah=0 WHERE IFNULL(idrutin,'')='' AND  IFNULL(idca1,'') <> ''");
    mysqli_query($cnit, "UPDATE $tmp01 set idrutin=idca2, jumlah=0 WHERE IFNULL(idrutin,'')='' AND  IFNULL(idca1,'') = '' AND  IFNULL(idca2,'') <> ''");
    
    $query = "select b.divisi, a.idrutin, a.nobrid, c.COA4 coa, sum(a.rptotal) rptotal from dbmaster.t_brrutin1 a 
        JOIN $tmp01 b on a.idrutin=b.idrutin
        LEFT JOIN dbmaster.posting_coa_rutin c on a.nobrid=c.nobrid AND b.divisi=c.divisi
        WHERE a.idrutin IN (select DISTINCT IFNULL(zz.idrutin,'') FROM $tmp01 zz WHERE LEFT(zz.idrutin,2)='BL')
        GROUP BY 1,2,3,4";
    $query = "create  table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
    
    $query = "INSERT INTO $tmp02 select b.divisi, a.idca, a.nobrid, c.COA4 coa, '0' as rptotal from dbmaster.t_ca1 a 
        JOIN $tmp01 b on a.idca=b.idrutin
        LEFT JOIN dbmaster.posting_coa_rutin c on a.nobrid=c.nobrid AND b.divisi=c.divisi
        WHERE a.idca IN (select DISTINCT IFNULL(zz.idrutin,'') FROM $tmp01 zz WHERE LEFT(zz.idrutin,2)='CA')
        GROUP BY 1,2,3,4";
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
    */
    
    
    $query = "SELECT br.idrutin, br.divisi, sum(br.jumlah) jumlah 
        FROM dbmaster.t_brrutin0 AS br WHERE br.kode=2 AND br.stsnonaktif <> 'Y' AND br.divisi <> 'OTC' $fperiode $fstsapv $filstatuscls GROUP BY 1, 2";
    $query = "create Temporary table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
    
    
    $query = "select b.divisi, a.idrutin, a.nobrid, c.COA4 coa, sum(a.rptotal) rptotal from dbmaster.t_brrutin1 a 
        JOIN dbmaster.t_brrutin0 b on a.idrutin=b.idrutin
        LEFT JOIN dbmaster.posting_coa_rutin c on a.nobrid=c.nobrid AND b.divisi=c.divisi
        WHERE a.idrutin IN (select DISTINCT IFNULL(zz.idrutin,'') FROM $tmp01 zz)
        GROUP BY 1,2,3,4";
    $query = "create Temporary table $tmp02 ($query)"; 
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
                //if ((DOUBLE)$pjmltrans > 0) $pjumlah=$pjmltrans;
                $query = "INSERT INTO $dbname.t_suratdana_br (idinput, divisi, kodeid, subkode, tgl, nodivisi, jumlah, userid, coa4, tglf, tglt, sts, karyawanid)values"
                        . "('$kodenya', '$ndivisi', '$pkode', '$psubkode', '$tglinput', '$pdivno', '$pjumlah', '$userid', '$pcoa', '$myinpperiode1', '$myinpperiode2', '$stsreport', '0000000329')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
                
                
                //simpan detail =============
                $purutan=1;
                $pkodeurutan=1;
                $kodeinput="I";//KODE LUAR KOTA
                
                $query="SELECT DISTINCT divisi from $tmp01 order by divisi";
                $tampildiv= mysqli_query($cnmy, $query);
                while ($dv= mysqli_fetch_array($tampildiv)) {
                    $ddivisi=$dv['divisi'];
                    


                    $query="SELECT DISTINCT ifnull(idrutin,'') nobrid, jumlah from $tmp01 WHERE divisi='$ddivisi' order by idrutin";
                    $tampil= mysqli_query($cnmy, $query);
                    $ketemu= mysqli_num_rows($tampil);
                    if ($ketemu>0) {
                        while ($tr= mysqli_fetch_array($tampil)) {
                            $nobrinput=$tr['nobrid'];

                            $pamount=0;
                            if (isset($tr['jumlah'])) $pamount=$tr['jumlah'];

                            //eksekusi input
                            $query = "INSERT INTO $dbname.t_suratdana_br1 (idinput, bridinput, kodeinput, urutan, amount)VALUES"
                                    . "('$kodenya', '$nobrinput', '$kodeinput', '$pkodeurutan', '$pamount')";
                            mysqli_query($cnmy, $query);
                            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                            if ($purutan==(INT)$_SESSION['JMLRECSPD']) {
                                $purutan=0;
                                $pkodeurutan++;
                            }
                            $purutan++;
                        }
                        $purutan=1; $pkodeurutan++;
                        
                    }
                
                    
                }
                
                //END simpan detail ==============================
                
                
                
                //simpan detail 2
                $purutan=1;
                $pkodeurutan=1;
                $kodeinput="I";//KODE BR RUTIN
                
                $query="SELECT DISTINCT divisi from $tmp02 order by divisi";
                $tampildiv_a= mysqli_query($cnmy, $query);
                while ($dva= mysqli_fetch_array($tampildiv_a)) {
                    $ddivisi=$dva['divisi'];
                
                    
                    $query="SELECT idrutin, nobrid, coa, rptotal as jumlah from $tmp02 WHERE divisi='$ddivisi' order by idrutin, nobrid, coa";
                    $tampil_a= mysqli_query($cnmy, $query);
                    $ketemu= mysqli_num_rows($tampil_a);
                    if ($ketemu>0) {
                        while ($tra= mysqli_fetch_array($tampil_a)) {
                            $didrutin=$tra['idrutin'];
                            $dnobrid=$tra['nobrid'];
                            $dcoa=$tra['coa'];

                            $pamount=0;
                            if (isset($tra['jumlah'])) $pamount=$tra['jumlah'];

                            //eksekusi input
                            $query = "INSERT INTO $dbname.t_suratdana_br1_a (idinput, kodeinput, urutan, amount, bridinput, nobrid, coa)VALUES"
                                    . "('$kodenya', '$kodeinput', '$pkodeurutan', '$pamount', '$didrutin', '$dnobrid', '$dcoa')";
                            mysqli_query($cnmy, $query);
                            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                            if ($purutan==(INT)$_SESSION['JMLRECSPD']) {
                                $purutan=0;
                                $pkodeurutan++;
                            }
                            $purutan++;
                        }
                        $purutan=1; $pkodeurutan++;
                        
                    }
                
                }
                
                
                //END simpan detail  2 ===
                
                
                
                //simpan detail 3
                $purutan=1;
                $pkodeurutan=1;
                $kodeinput="I";//KODE BR RUTIN
                
                $query="SELECT DISTINCT divisi from $tmp02 order by divisi";
                $tampildiv_a= mysqli_query($cnmy, $query);
                while ($dva= mysqli_fetch_array($tampildiv_a)) {
                    $ddivisi=$dva['divisi'];
                
                    
                    $query="SELECT idrutin, coa, SUM(rptotal) as jumlah from $tmp02 WHERE divisi='$ddivisi' Group by idrutin, coa order by idrutin, coa";
                    $tampil_a= mysqli_query($cnmy, $query);
                    $ketemu= mysqli_num_rows($tampil_a);
                    if ($ketemu>0) {
                        while ($tra= mysqli_fetch_array($tampil_a)) {
                            $didrutin=$tra['idrutin'];
                            $dcoa=$tra['coa'];

                            $pamount=0;
                            if (isset($tra['jumlah'])) $pamount=$tra['jumlah'];

                            //eksekusi input
                            $query = "INSERT INTO $dbname.t_suratdana_br1_b (idinput, kodeinput, urutan, amount, bridinput, coa)VALUES"
                                    . "('$kodenya', '$kodeinput', '$pkodeurutan', '$pamount', '$didrutin', '$dcoa')";
                            mysqli_query($cnmy, $query);
                            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                            if ($purutan==(INT)$_SESSION['JMLRECSPD']) {
                                $purutan=0;
                                $pkodeurutan++;
                            }
                            $purutan++;
                        }
                        $purutan=1; $pkodeurutan++;
                        
                    }
                
                }
                
                
                //END simpan detail  3 =======
                
                
                //update idinput di t_brrutin_close
                $query = "UPDATE dbmaster.t_brrutin_ca_close SET idinput='$kodenya' WHERE DATE_FORMAT(bulan,'%Y-%m')='$periode1' AND IFNULL(sts,'')='$stsreport'";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
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
    
    mysqli_query($cnit, "drop temporary table IF EXISTS $tmp01");
    mysqli_query($cnit, "drop temporary table IF EXISTS $tmp02");
    mysqli_query($cnit, "drop table IF EXISTS $tmp01");
    mysqli_query($cnit, "drop table IF EXISTS $tmp02");
         
    mysqli_close($cnit);
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
    
    $stsapv = $_POST['sts_apv'];
    
    $stsreport = $_POST['sts_rpt'];
    
    $tglinput = date("Y-m-01", strtotime($tgl01));
    
    $berhasilsimpan="Tidak ada data yang dihapus...";
    
    $pdivno = $_POST['unobukti'];
    $piddata = $_POST['uiddata'];

    $pcoa="101-02-002";
    $pkode="2";
    $psubkode="21";
           
    if (!empty($piddata)) {
        
        $query="UPDATE $dbname.t_brrutin_ca_close SET idinput=NULL WHERE idinput IN ("
                . " select distinct IFNULL(idinput,'') FROM $dbname.t_suratdana_br WHERE "
                . " nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' AND sts='$stsreport' AND IFNULL(tgl_proses,'') = '' "
                . " )";
        mysqli_query($cnmy, $query);
        
        $query="DELETE FROM $dbname.t_suratdana_br_d WHERE idinput IN ("
                . " select distinct idinput FROM $dbname.t_suratdana_br WHERE "
                . " nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' AND sts='$stsreport' AND IFNULL(tgl_proses,'') = '' "
                . " )";
        mysqli_query($cnmy, $query);
        
        $query="DELETE FROM $dbname.t_suratdana_br1_a WHERE idinput IN ("
                . " select distinct idinput FROM $dbname.t_suratdana_br WHERE "
                . " nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' AND sts='$stsreport' AND IFNULL(tgl_proses,'') = '' "
                . ")";
        mysqli_query($cnmy, $query);
        
        $query="DELETE FROM $dbname.t_suratdana_br1_b WHERE idinput IN ("
                . " select distinct idinput FROM $dbname.t_suratdana_br WHERE "
                . " nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' AND sts='$stsreport' AND IFNULL(tgl_proses,'') = '' "
                . ")";
        mysqli_query($cnmy, $query);
        
        $query="DELETE FROM $dbname.t_suratdana_br1 WHERE idinput IN ("
                . " select distinct idinput FROM $dbname.t_suratdana_br WHERE "
                . " nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' AND sts='$stsreport' AND IFNULL(tgl_proses,'') = '' "
                . ")";
        mysqli_query($cnmy, $query);
        
        $query="DELETE FROM $dbname.t_suratdana_br WHERE nodivisi='$piddata' AND kodeid='$pkode' AND subkode='$psubkode' AND sts='$stsreport' AND IFNULL(tgl_proses,'') = ''";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "Error..."; exit; }
        $berhasilsimpan="Data berhasil dihapus...";
    }
    echo $berhasilsimpan;
    
}

?>
