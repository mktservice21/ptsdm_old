<?PHP
    session_start();
    include "config/koneksimysqli_it.php";
    include_once("config/common.php");
    $srid = $_SESSION['USERID'];
    $srnama = $_SESSION['NAMALENGKAP'];
    $sr_id = substr('0000000000'.$srid,-10);
    $userid = $_SESSION['USERID'];
    $jenis = $_POST['cb_jenis'];

    $tgl01=$_POST['e_periode01'];

    $periode1= date("Y-m-d", strtotime($tgl01));
    $periode= date("d-M-Y", strtotime($tgl01));

    $now=date("mdYhis");
    $tmpbudgetreq01 =" dbtemp.DTBUDGETBRREKAPSBYOTC01_$_SESSION[IDCARD]$now ";
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
        
    $sql = "select icabangid_o, nama_cabang, keterangan1, idkontak, bankreal1, real1, norekreal1, jumlah, CAST(null as char(1)) as GRP1 "
            . " from dbmaster.v_br_otc_all"
            . " where tglbr = '$periode1' and case when ifnull(lampiran,'N')='' then 'N' else lampiran end ='$jenis' "
            . " and brOtcId not in (select distinct ifnull(brOtcId,'') from hrd.br_otc_reject)";
    
    //$sql .= " group by brOtcId, icabangid_o, nama_cabang, keterangan1, idkontak, real1, norekreal1";
    
    $sql = "create table $tmpbudgetreq01 ($sql)";
    mysqli_query($cnit, $sql);

    $sql = "update $tmpbudgetreq01 as b set b.nama_cabang=(select a.initial from dbmaster.cabang_otc as a where b.icabangid_o=a.cabangid_ho)"
            . "where b.icabangid_o in (select distinct c.cabangid_ho from dbmaster.cabang_otc as c)";
    mysqli_query($cnit, $sql);

    $sql = "update $tmpbudgetreq01 as b set b.GRP1=(select a.group1 from dbmaster.cabang_otc as a where b.icabangid_o=a.cabangid_ho)";
    mysqli_query($cnit, $sql);

    $sql = "update $tmpbudgetreq01 set icabangid_o='group1' where ifnull(GRP1,'') <> ''";
    mysqli_query($cnit, $sql);
?>