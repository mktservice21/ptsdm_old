<?php
    session_start();
    $userid=$_SESSION['IDCARD'];
    
    if (empty($userid)) {
        echo "ANDA HARUS LOGIN ULANG...!!!";
        exit;
    }
    
    $bolehsave=false;
    if (isset($_POST['chkbox_br'])) {
        
        foreach ($_POST['chkbox_br'] as $nobrinput) {
            if (!empty($nobrinput)) {
                $bolehsave=true;
            }
        }
        
    }
    
    if ($bolehsave==false) {
        echo "Tidak ada data yang dipilih...!!!";
        exit;
    }
    
    include "../../config/koneksimysqli.php";
    //include "../../config/koneksimysqli_it.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSPGHRA01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSPGHRA02_".$userid."_$now ";
    
    $ptglpilih=$_POST['e_periodepilih'];
    $pcabidpilih=$_POST['e_cabangpilih'];
    
    $ptglpilih= date("Y-m-d", strtotime($ptglpilih));
    $pblnpilih= date("Ym", strtotime($ptglpilih));
    
    $fcabang="";
    if (!empty($pcabidpilih)) {
        if ($pcabidpilih=="JKT_MT") {
            $fcabang = " AND a.icabangid='0000000007' AND a.alokid='001' ";
        }elseif ($pcabidpilih=="JKT_RETAIL") {
            $fcabang = " AND a.icabangid='0000000007' AND a.alokid='002' ";
        }else{
            $fcabang = " AND a.icabangid='$pcabidpilih' ";
        }
        
    }
    
    $query = "DELETE FROM dbmaster.tmp_spg_trans_to_brotc";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $sql=  mysqli_query($cnmy, "select max(brOtcId) as NOURUT from dbmaster.t_setup");
    $ketemu=  mysqli_num_rows($sql);
    $purut="";
    if ($ketemu>0){
        $o=  mysqli_fetch_array($sql);
        $purut=$o['NOURUT'];
    }
        
    if (empty($purut)) {
        echo "Tidak ada data yang ditransfer...";
        exit;
    }
        
    
    $query = "select brOtcId, icabangid_o, subpost, kodeid, tglbr, keterangan1, jumlah, COA4, user1, lampiran, ca, via, lampiran2, ca2, via2,
        ccyId, bralid, MODIFDATE, icabangid_o as icabangid, CAST('' as CHAR(3)) as alokid, CAST('' as CHAR(50)) as nodivisi 
         from hrd.br_otc WHERE brOtcId='XXXXXXXXX'";
    $query ="CREATE TEMPORARY TABLE $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query ="ALTER TABLE $tmp01 ADD idinput INTEGER(11)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DELETE FROM $tmp01");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $lsave=false;
    $awal=10; $urut=1; $kodenya="";
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            
            $urut=(double)$purut+1;
            $jml=  strlen($urut);
            $pawal=$awal-$jml;
            $kodenya=str_repeat("0", $pawal).$urut;
            
            $purut++;
            
            
            $pbridotcinput=$_POST['txtbrotcid'][$nobrinput];
            $pidinputspd=$_POST['txtidinputspd'][$nobrinput];
            $pnodivisi=$_POST['txtnodivisi'][$nobrinput];
            $pidcabang=$_POST['txtidcabang'][$nobrinput];
            $palokid="";//$_POST['txtalokid'][$nobrinput];
            $psubkodepost=$_POST['txtsubkode'][$nobrinput];
            $pkodeidpost=$_POST['txtkodeid'][$nobrinput];
            $ptglbr=$_POST['txttglbr'][$nobrinput];
            $pketerangan=$_POST['txtket'][$nobrinput];
            $pjumlahrp=$_POST['txtjmlrp'][$nobrinput];
            $pcoa=$_POST['txtcoa'][$nobrinput];
            
            $ptglbr= date("Y-m-d", strtotime($ptglbr));
            
            if (!empty($kodenya)) {
                $query = "INSERT INTO $tmp01 (brOtcId, icabangid_o, subpost, kodeid, tglbr, 
                    keterangan1, jumlah, COA4, user1, lampiran, ca, via, lampiran2, ca2, via2,
                    ccyId, bralid, MODIFDATE, alokid, icabangid, nodivisi, idinput)VALUES"
                    . " ('$kodenya', '$pidcabang', '$psubkodepost', '$pkodeidpost', '$ptglbr', "
                    . " '$pketerangan', '$pjumlahrp', '$pcoa', '$userid', 'Y', 'N', 'N', 'Y', 'N', 'N', "
                    . " 'IDR', 'bl', NOW(), '$palokid', '$pidcabang', '$pnodivisi', '$pidinputspd')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

                $lsave=true;
            }
                //echo "$nobrinput : $pbridotcinput, $pidinputspd, $pnodivisi, $pidcabang, $palokid, $psubkodepost, $pkodeidpost, $ptglbr, $pketerangan, $pjumlahrp, $pcoa<br/>";
        }
    }
    
    if ($lsave==true) {
        
                //khusus jakarta
        
        $query = "UPDATE $tmp01 SET icabangid='0000000007', alokid='001' WHERE icabangid_o='JKT_MT'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 SET icabangid='0000000007', alokid='002' WHERE icabangid_o='JKT_RETAIL'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                //end khusus jakarta
        
        
        
                // insert temporary dulu
        $query = "INSERT INTO dbmaster.tmp_spg_trans_to_brotc (brOtcId, icabangid_o, subpost, kodeid, tglbr, 
            keterangan1, jumlah, COA4, user1, lampiran, ca, via, lampiran2, ca2, via2,
            ccyId, bralid, MODIFDATE, alokid, icabangid)"
            . " SELECT brOtcId, icabangid_o, subpost, kodeid, tglbr, 
            keterangan1, jumlah, COA4, user1, lampiran, ca, via, lampiran2, ca2, via2,
            ccyId, bralid, MODIFDATE, alokid, icabangid FROM $tmp01";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        mysqli_query($cnmy, "UPDATE dbmaster.t_setup SET brOtcId='$kodenya'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $pidcard="";
        if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
        
                //insert ke br_otc
        $query = "INSERT INTO hrd.br_otc (brOtcId, icabangid_o, subpost, kodeid, tglbr, 
            keterangan1, jumlah, COA4, user1, lampiran, ca, via, lampiran2, ca2, via2,
            ccyId, bralid, MODIFDATE, karyawanid, p_spg)"
            . " SELECT brOtcId, icabangid_o, subpost, kodeid, tglbr, 
            keterangan1, jumlah, COA4, user1, lampiran, ca, via, lampiran2, ca2, via2,
            ccyId, bralid, MODIFDATE, '$pidcard' as karyawanid, 'Y' as p_spg FROM dbmaster.tmp_spg_trans_to_brotc";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
                //non jakarta
        $query = "UPDATE dbmaster.t_spg_gaji_br0 a JOIN (select * from $tmp01 WHERE CONCAT(subpost,kodeid)='0512') b on a.icabangid=b.icabangid AND "
                . " a.nodivisi=b.nodivisi AND a.idinput=b.idinput SET "
                . " a.brotcid=b.brOtcId WHERE DATE_FORMAT(a.periode,'%Y%m')='$pblnpilih' $fcabang AND a.icabangid NOT IN ('0000000007')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                //khusus jakarta
        $query = "UPDATE dbmaster.t_spg_gaji_br0 a JOIN (select * from $tmp01 WHERE CONCAT(subpost,kodeid)='0512') b on a.icabangid=b.icabangid AND "
                . " a.alokid=b.alokid AND a.nodivisi=b.nodivisi AND a.idinput=b.idinput SET "
                . " a.brotcid=b.brOtcId WHERE DATE_FORMAT(a.periode,'%Y%m')='$pblnpilih' $fcabang AND a.icabangid IN ('0000000007')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
            //insentif
                //non jakarta
        $query = "UPDATE dbmaster.t_spg_gaji_br0 a JOIN (select * from $tmp01 WHERE CONCAT(subpost,kodeid)='0308') b on a.icabangid=b.icabangid AND "
                . " a.nodivisi=b.nodivisi AND a.idinput=b.idinput SET "
                . " a.brotcid2=b.brOtcId WHERE DATE_FORMAT(a.periode,'%Y%m')='$pblnpilih' $fcabang AND a.icabangid NOT IN ('0000000007') AND IFNULL(insentif,0)+IFNULL(insentif_tambahan,0)<>0 ";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                //khusus jakarta
        $query = "UPDATE dbmaster.t_spg_gaji_br0 a JOIN (select * from $tmp01 WHERE CONCAT(subpost,kodeid)='0308') b on a.icabangid=b.icabangid AND "
                . " a.alokid=b.alokid AND a.nodivisi=b.nodivisi AND a.idinput=b.idinput SET "
                . " a.brotcid2=b.brOtcId WHERE DATE_FORMAT(a.periode,'%Y%m')='$pblnpilih' $fcabang AND a.icabangid IN ('0000000007') AND IFNULL(insentif,0)+IFNULL(insentif_tambahan,0)<>0";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            //end insentif
        
        
        
                //non jakarta
        $query = "UPDATE dbmaster.t_spg_gaji_br0 a JOIN (select * from $tmp01 WHERE CONCAT(subpost,kodeid)='1093') b on a.icabangid=b.icabangid AND "
                . " a.nodivisi=b.nodivisi AND a.idinput=b.idinput SET "
                . " a.brotcid3=b.brOtcId WHERE DATE_FORMAT(a.periode,'%Y%m')='$pblnpilih' $fcabang AND a.icabangid NOT IN ('0000000007')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
                //khusus jakarta
        $query = "UPDATE dbmaster.t_spg_gaji_br0 a JOIN (select * from $tmp01 WHERE CONCAT(subpost,kodeid)='1093') b on a.icabangid=b.icabangid AND "
                . " a.alokid=b.alokid AND a.nodivisi=b.nodivisi AND a.idinput=b.idinput SET "
                . " a.brotcid3=b.brOtcId WHERE DATE_FORMAT(a.periode,'%Y%m')='$pblnpilih' $fcabang AND a.icabangid IN ('0000000007')";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query = "DELETE FROM dbmaster.t_suratdana_br1 WHERE kodeinput='D' AND nourut > 32417 AND CONCAT(idinput,bridinput) IN "
                . " (SELECT DISTINCT IFNULL(CONCAT(IFNULL(idinput,''),IFNULL(brOtcId,'')),'') FROM $tmp01)";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "INSERT INTO dbmaster.t_suratdana_br1 (idinput, bridinput, kodeinput, amount)"
                . "select idinput, brOtcId as bridinput, 'D' as kodeinput, jumlah amount from $tmp01";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }
    
    hapusdata:
        mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
        mysqli_query($cnmy, "drop TEMPORARY table $tmp02");

        mysqli_close($cnmy);
        //mysqli_close($cnit);
        
        
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
?>