<?PHP
    session_start();
    include "config/koneksimysqli_it.php";
    include_once("config/common.php");
    $srid = $_SESSION['USERID'];
    $srnama = $_SESSION['NAMALENGKAP'];
    $sr_id = substr('0000000000'.$srid,-10);
    $userid = $_SESSION['USERID'];
    
    $nnkodeid="";
    $nnperiodeby="";
    $nnf1="";
    $nnf2="";
    $fidinput="";
    
    $nnomorbuktidivisi="";
    $prtpsby="";
    if (isset($_GET['ispd'])) {
        include "config/koneksimysqli.php";
        
        $idinputspd=$_GET['ispd'];
        $_POST['cb_jenis']="";
        $_POST['e_periode01']="2000-01-00";
        $query = "select * from dbmaster.t_suratdana_br WHERE idinput='$idinputspd'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            $_POST['cb_jenis']=$ra['lampiran'];
            if (!empty($ra['tglf'])) $_POST['e_periode01']=$ra['tglf'];
            
            if (!empty($ra['nodivisi']))
                $nnomorbuktidivisi=$ra['nodivisi'];
    
            if ($ra['periodeby']=="S")
                $prtpsby="2";
            
            $nnperiodeby=$ra['periodeby'];
            $nnkodeid=$ra['kodeid'];
            
            $nnf1=$ra['tglf'];
            $nnf2=$ra['tglt'];
            
        }
    }
    
    if (empty($nnf1)) $nnf1=$_POST['e_periode01'];
    if (empty($nnf2)) $nnf2=$_POST['e_periode01'];
    
    $nnperiode1= date("Y-m-d", strtotime($nnf1));
    $nnperiode2= date("Y-m-d", strtotime($nnf2));
    
    
    $jenis = $_POST['cb_jenis'];
    $tgl01=$_POST['e_periode01'];
        
    $periode1= date("Y-m-d", strtotime($tgl01));
    $periode= date("d-M-Y", strtotime($tgl01));

    $now=date("mdYhis");
    $tmpbudgetreq01 =" dbtemp.DTBUDGETBRREKAPSBYOTC01_$_SESSION[IDCARD]$now ";
    $tmpbudgetreq02 =" dbtemp.DTBUDGETBRREKAPSBYOTC02_$_SESSION[IDCARD]$now ";
    //echo "<center><h2><u>REKAP DATA PERMOHONAN DANA BR</u></h2></center>";
    
    $bl= date("m", strtotime($tgl01));
    $byear= date("Y", strtotime($tgl01));
    //$bl=date("m");
    //$byear=date("Y");
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
    
    $tno=1;
    if (!empty($_POST['t_nomor']))
        $tno=(int)$_POST['t_nomor']+1;
    $query = "select tno from dbmaster.t_otc_norekapdanabr_b WHERE tglbr='$periode1'";
    $tampil= mysqli_query($cnit, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $tr= mysqli_fetch_array($tampil);
        $tno=$tr['tno'];
    }
    $tpenomoran="";
    if (!empty($tno)) $tpenomoran=$tno;
    if (strlen($tno)==1) $tno="0".$tno;
    $noslipurut=$tno."/BR-OTC/".$blromawi."/".$byear;
    //$noslipurut="BR-OTC";
    
    if (!empty($nnomorbuktidivisi))
        $noslipurut=$nnomorbuktidivisi;
    
    
    $filedppilih = " jumlah ";
    $filterlampiran = " and case when ifnull(lampiran,'N')='' then 'N' else lampiran end ='$jenis' ";
    
    $ftglnya = " DATE_FORMAT(tglbr,'%Y-%m-%d') = '$periode1' ";
    
    if ( (INT)$prtpsby==2) {
        $filedppilih = " realisasi as jumlah ";
        
        $filterlampiran = "";
        if (!empty($jenis)) $filterlampiran = " and case when ifnull(lampiran,'N')='' then 'N' else lampiran end ='$jenis' ";
        
        $ftglnya = " DATE_FORMAT(tglrpsby,'%Y-%m-%d') = '$periode1' ";
    }else{
        if ($nnkodeid=="2" AND $nnperiodeby=="T") {
            $filedppilih = " realisasi as jumlah ";

            $filterlampiran = "";
            if (!empty($jenis)) $filterlampiran = " and case when ifnull(lampiran,'N')='' then 'N' else lampiran end ='$jenis' ";

            $ftglnya = " DATE_FORMAT(tgltrans,'%Y-%m-%d') BETWEEN '$nnperiode1' AND '$nnperiode2' ";
            
        }
    }
    
    if (isset($_GET['ispd'])) {
        $query = "select DISTINCT IFNULL(bridinput,'') bridinput from dbmaster.t_suratdana_br1 WHERE idinput='$idinputspd'";
        $sql = "create table $tmpbudgetreq02 ($query)";
        mysqli_query($cnit, $sql);
    
        $filterlampiran = "";
        $fidinput = " brOtcId IN (select DISTINCT IFNULL(bridinput,'') bridinput from $tmpbudgetreq02) ";       
        
        $ftglnya=$fidinput;
    }
    
    
    if (empty($jenis)) $filterlampiran= "";
    
    $sql = "select icabangid_o, nama_cabang, keterangan1, idkontak, bankreal1, real1, norekreal1, $filedppilih, CAST(null as char(1)) as GRP1 "
            . " from dbmaster.v_br_otc_all"
            . " where $ftglnya $filterlampiran "
            . " and brOtcId not in (select distinct ifnull(brOtcId,'') from hrd.br_otc_reject)";
    
    //$sql .= " group by brOtcId, icabangid_o, nama_cabang, keterangan1, idkontak, real1, norekreal1";
    //echo $sql;
    $sql = "create table $tmpbudgetreq01 ($sql)";
    mysqli_query($cnit, $sql);

    $sql = "update $tmpbudgetreq01 as b set b.nama_cabang=(select a.initial from dbmaster.cabang_otc as a where b.icabangid_o=a.cabangid_ho)"
            . "where b.icabangid_o in (select distinct c.cabangid_ho from dbmaster.cabang_otc as c)";
    mysqli_query($cnit, $sql);

    $sql = "update $tmpbudgetreq01 as b set b.GRP1=(select a.group1 from dbmaster.cabang_otc as a where b.icabangid_o=a.cabangid_ho)";
    mysqli_query($cnit, $sql);

    $sql = "update $tmpbudgetreq01 set icabangid_o='group1' where ifnull(GRP1,'') <> ''";
    mysqli_query($cnit, $sql);
    
    mysqli_query($cnit, "DROP TABLE IF EXISTS $tmpbudgetreq02");
?>