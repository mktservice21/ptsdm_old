<?php

    session_start();
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
$puserid="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG...";
    exit;
}

    

    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    
    $pidgroup=$_SESSION['GROUP'];
    
    //$cnmy=$cnit;
    $dbname = "dbmaster";
// Hapus 
if ($module=='bgtkaskecilcabang' AND $act=='hapus')
{
    $puserid=$_SESSION['IDCARD'];
    $kodenya=$_GET['id'];
    
    mysqli_query($cnmy, "update $dbname.t_kaskecilcabang set stsnonaktif='Y', userid='$puserid' WHERE idkascab='$kodenya' LIMIT 1");
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
}
elseif ($module=='bgtkaskecilcabang')
{
    $puserid=$_SESSION['IDCARD'];
    $kodenya=$_POST['e_id'];
    $pdivisi=$_POST['cb_divisi'];
    $pkaryawanid=$_POST['cb_karyawan'];
    $pidcabang=$_POST['cb_cabang'];
    $pcoapilih=$_POST['cb_coa'];
    $putkpengajuan=$_POST['cb_untuk'];
    $pnmreal=$_POST['e_nmreal'];
    $pnorek=$_POST['e_norek'];
    
    $pblnpil=$_POST['e_bulan'];
    
    $pkdspv=$_POST['e_kdspv'];
    $pkddm=$_POST['e_kddm'];
    $pkdsm=$_POST['e_kdsm'];
    $pkdgsm=$_POST['e_kdgsm'];
    
    $pket=$_POST['e_ket'];
    if (!empty($pket)) $pket = str_replace("'", " ", $pket);
    
    if (!empty($pnmreal)) $pnmreal = str_replace("'", " ", $pnmreal);
    if (!empty($pnorek)) $pnorek = str_replace("'", " ", $pnorek);
    
    $ptanggal = str_replace('/', '-', $_POST['e_tglberlaku']);
    $periode1= date("Y-m-d", strtotime($ptanggal));
    $pthnbln= date("ym", strtotime($ptanggal));
    $pbulanpilih= date("Y-m-01", strtotime($pblnpil));
    
    $pjumlah=str_replace(",","", $_POST['e_jml']);
    if(empty($pjumlah)) $pjumlah=0;
    
    
    $query = "select jabatanid from hrd.karyawan where karyawanid='$pkaryawanid'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pjabatanid=$row['jabatanid'];
    
    
    
    $pisitglspv=false;
    $pisitgldm=false;
    $pisitglsm=false;
    $pisitglgsm=false;
    
    //$pkdspv="";$pkddm="";$pkdsm="A";$pkdgsm="A";
    
    if (empty($pkdspv)) {
        $pisitglspv=true;
        if (empty($pkddm)) {
            $pisitgldm=true;
            if (empty($pkdsm)) {
                $pisitglsm=true;
                if (empty($pkdgsm)) {
                    $pisitglgsm=true;
                }
            }
        }
    }
    
    
    //echo "$pkdspv, $pkddm, $pkdsm, $pkdgsm<br/>spv : $pisitglspv<br/>dm : $pisitgldm<br/>sm : $pisitglsm<br/>gsm : $pisitglgsm<br/>"; exit;
    
    
    if ($act=="input") {
        $sql=  mysqli_query($cnmy, "select kasboncab as NOURUT from dbmaster.t_setup_periode where thnbln='$pthnbln'");
        $ketemu=  mysqli_num_rows($sql);
        $awal=5; $nurut=1; $kodenya=""; $padaurut=false;
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            if (empty($o['NOURUT'])) $o['NOURUT']=0;
            $nurut=$o['NOURUT']+1;
            $padaurut=true;
        }else{
            $nurut=1;
        }
        $jml=  strlen($nurut);
        $awal=$awal-$jml;
        $kodenya="C".$pthnbln."".str_repeat("0", $awal).$nurut;
        
        if ($padaurut==false) {
            mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_periode (thnbln, kasboncab)VALUES('$pthnbln', '0')");
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        }
        
        mysqli_query($cnmy, "UPDATE dbmaster.t_setup_periode SET kasboncab=IFNULL(kasboncab,0)+1 WHERE thnbln='$pthnbln'");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
    }else{
        $kodenya=$_POST['e_id'];
    }
    
    
    
    if ($act=="input") {
        $query = "INSERT INTO dbttd.t_kaskecilcabang_ttd(idkascab)VALUES('$kodenya')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
		
		
        $query = "INSERT INTO $dbname.t_kaskecilcabang_rpdetail(idkascab)VALUES('$kodenya')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
		
    
        $query = "INSERT INTO $dbname.t_kaskecilcabang (idkascab, bulan, tanggal, pengajuan, karyawanid, icabangid, divisi, jabatanid, jumlah, keterangan, coa4, userid, nmrealisasi, norekening)values"
                . "('$kodenya', '$pbulanpilih', '$periode1', '$putkpengajuan', '$pkaryawanid', '$pidcabang', '$pdivisi', '$pjabatanid', '$pjumlah', '$pket', '$pcoapilih', '$puserid', '$pnmreal', '$pnorek')";
    }else{
        $query = "UPDATE $dbname.t_kaskecilcabang SET divisi='$pdivisi', tanggal='$periode1', bulan='$pbulanpilih', "
                . " karyawanid='$pkaryawanid', icabangid='$pidcabang', jabatanid='$pjabatanid', "
                . " keterangan='$pket', jumlah='$pjumlah', coa4='$pcoapilih', userid='$puserid', nmrealisasi='$pnmreal', norekening='$pnorek' WHERE "
                . " idkascab='$kodenya' LIMIT 1";
    }
    
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    
    if ($act=="input") {
        $pimgttd=$_POST['txtgambar'];
        $query = "update dbttd.t_kaskecilcabang_ttd set gambar='$pimgttd' WHERE idkascab='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
    
    
    $query = "UPDATE $dbname.t_kaskecilcabang SET pengajuan='$putkpengajuan', atasan1='$pkdspv', atasan2='$pkddm', atasan3='$pkdsm', atasan4='$pkdgsm' WHERE idkascab='$kodenya' LIMIT 1";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    
    if ($pisitglspv==true) {
        $query = "UPDATE $dbname.t_kaskecilcabang SET tgl_atasan1=NOW() WHERE idkascab='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    if ($pisitgldm==true) {
        $query = "UPDATE $dbname.t_kaskecilcabang SET tgl_atasan2=NOW() WHERE idkascab='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    if ($pisitglsm==true) {
        $query = "UPDATE $dbname.t_kaskecilcabang SET tgl_atasan3=NOW() WHERE idkascab='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    if ($pisitglgsm==true) {
        $query = "UPDATE $dbname.t_kaskecilcabang SET tgl_atasan4=NOW() WHERE idkascab='$kodenya' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    }
    
    
    //ADMIN BR dan FINANCE OTC
    if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26") {
        
        $query = "UPDATE $dbname.t_kaskecilcabang SET tgl_atasan1=NOW() WHERE idkascab='$kodenya' AND (IFNULL(tgl_atasan1,'')='' OR IFNULL(tgl_atasan1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') LIMIT 1";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        $query = "UPDATE $dbname.t_kaskecilcabang SET tgl_atasan2=NOW() WHERE idkascab='$kodenya' AND (IFNULL(tgl_atasan2,'')='' OR IFNULL(tgl_atasan2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') LIMIT 1";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        $query = "UPDATE $dbname.t_kaskecilcabang SET tgl_atasan3=NOW() WHERE idkascab='$kodenya' AND (IFNULL(tgl_atasan3,'')='' OR IFNULL(tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') LIMIT 1";
        //mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        
    }
    
    
    
    $query = "DELETE from dbmaster.t_kaskecilcabang_d WHERE idkascab='$kodenya'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
    
    foreach ($_POST['chk_kodeid'] as $no_brid) {
        
        $pjmlinputpilih= $_POST['e_txtrp'][$no_brid];
        if (empty($pjmlinputpilih)) $pjmlinputpilih=0;
        $pjmlinputpilih=str_replace(",","", $pjmlinputpilih);
        
        $ptglpilih= $_POST['e_tglpilih'][$no_brid];
        if (!empty($ptglpilih)) $ptglpilih=date("Y-m-d", strtotime($ptglpilih));
        else $ptglpilih=$periode1;
        
        $pnotes= $_POST['e_txtnotes'][$no_brid];
        if (!empty($pnotes)) $pnotes = str_replace("'", " ", $pnotes);
        
        $pcoa4pilih= $_POST['e_txtcoa4'][$no_brid];
        
        if ($pjmlinputpilih<>"0") {
            $query = "INSERT INTO dbmaster.t_kaskecilcabang_d (idkascab, kode, jumlahrp, notes, tglpilih, coa4)VALUES"
                    . "('$kodenya', '$no_brid', '$pjmlinputpilih', '$pnotes', '$ptglpilih', '$pcoa4pilih')";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); 
            if (!empty($erropesan)) { 
                echo "Silakan edit No Inputan : <b>$kodenya</b><br/>";
                echo $erropesan; mysqli_close($cnmy);exit; 
            }
        }
        
        
    }
    
    
    
    
    
    $ppcrptot=str_replace(",","", $_POST['e_pcrp']);
    $ppcmrp=str_replace(",","", $_POST['e_rppc']);
    $pjmltambah=str_replace(",","", $_POST['e_tambahanrp']);
    $psldawal=str_replace(",","", $_POST['e_sldawal']);
    $prpots=str_replace(",","", $_POST['e_otsrp']);
    $prppclalu=str_replace(",","", $_POST['e_pcblnlalu']);
    
    if(empty($ppcrptot)) $ppcrptot=0;
    if(empty($ppcmrp)) $ppcmrp=0;
    if(empty($pjmltambah)) $pjmltambah=0;
    if(empty($psldawal)) $psldawal=0;
    if(empty($prpots)) $prpots=0;
    if(empty($prppclalu)) $prppclalu=0;
    
    $query = "UPDATE $dbname.t_kaskecilcabang_rpdetail SET pc_bln_lalu='$prppclalu', oustanding='$prpots', saldoawal='$psldawal', pcm='$ppcmrp', jmltambahan='$pjmltambah', jumlah='$ppcrptot' WHERE idkascab='$kodenya' LIMIT 1";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan." ID : $kodenya"; exit; }
    
    
    
    $query = "select coa from $dbname.t_uangmuka_kascabang WHERE icabangid='$pidcabang'";
    $tampilp= mysqli_query($cnmy, $query);
    $pr= mysqli_fetch_array($tampilp);
    $icoa=$pr['coa'];
    
    $query = "UPDATE $dbname.t_kaskecilcabang SET coa4='$icoa' WHERE idkascab='$kodenya' LIMIT 1";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan." ID : $kodenya"; exit; }
    
    
    
    mysqli_close($cnmy);
    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
    /*
    if ($act=="input")
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=tambahbaru');
    else
        header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
     * 
     */
    
}
  
    
?>
