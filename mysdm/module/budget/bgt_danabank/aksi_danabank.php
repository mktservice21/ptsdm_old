<?php
session_start();

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);

$puserid="";
$pidcard="";
$pnmlengkapuser="";
$pidgroup="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['NAMALENGKAP'])) $pnmlengkapuser=$_SESSION['NAMALENGKAP'];
if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];


$_SESSION['BNKDANATIPE']="viewdatabank";

$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

if ($module=='brdanabankbyfin')
{
    if ($act=="hapus") {
        if (empty($pidcard)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }
        
        include "../../../config/koneksimysqli.php";
        
        $kodenya=$_GET['id'];
        $pkethapus=$_GET['kethapus'];
        if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);

        mysqli_query($cnmy, "UPDATE dbmaster.t_suratdana_bank SET stsnonaktif='Y', keterangan=CONCAT(IFNULL(keterangan,''), ' $pkethapus', ', USER HAPUS : $pnmlengkapuser') WHERE idinputbank='$kodenya' LIMIT 1");
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        mysqli_close($cnmy);
        
        header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilhapus');
        exit;
        
    }elseif ($act=="input" OR $act=="update") {
        
        $pkaryawanid=$_POST['e_idcarduser'];
        if (empty($pkaryawanid)) $pkaryawanid=$pidcard;
        
        if (empty($pkaryawanid)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }


        include "../../../config/koneksimysqli.php";
        
        $pnobukti=$_POST['e_nobukti'];
        $pnobrid=$_POST['e_idnobr'];
        $pnoslipbr=$_POST['e_noslipbr'];
        $prealisasibr=$_POST['e_realisasibr'];
        $pcustomerbr=$_POST['e_customerbr'];
        $paktivitasbr=$_POST['e_ketbr'];
        
        $pidinputspd=$_POST['e_idinput'];
        $pnodivisi=$_POST['e_nodivisi'];
        $pnospd="";
        
        $kodenya=$_POST['e_id'];
        $ptglaslinput=$_POST['e_asltglberlaku'];
        $ptglinputpl=$_POST['e_tglberlaku'];
        $pkodeid="";
        $psubkode=$_POST['cb_kodesub'];
        $pcoa=$_POST['cb_coa'];
        $pdivisi=$_POST['cb_divisi'];
        $pstatus=$_POST['cb_sts'];
        $pstsaslinput=$_POST['cb_asldebitkredit'];
        $pstsinput=$_POST['cb_debitkredit'];
        $pjumlah=$_POST['e_jml'];
        $pketerangan=$_POST['e_ket'];
        
        $ptglinput= date("Y-m-d", strtotime($ptglinputpl));
        if (!empty($pketerangan)) $pketerangan = str_replace("'", " ", $pketerangan);
        $pjumlah=str_replace(",","", $pjumlah);
        if (empty($pjumlah)) $pjumlah=0;
        
        
        $query = "select kodeid from dbmaster.t_kode_spd WHERE subkode='$psubkode'";
        $tampil= mysqli_query($cnmy, $query);
        $nrow= mysqli_fetch_array($tampil);
        $pkodeid=$nrow['kodeid'];
        
        $query = "select nomor, subkode, kodeid, divisi from dbmaster.t_suratdana_br WHERE idinput='$pidinputspd'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemus=  mysqli_num_rows($tampil);
        if ((INT)$ketemus>0) {
            $srow= mysqli_fetch_array($tampil);
            $pnospd=$srow['nomor'];
            if (!empty($pidinputspd) AND $pidinputspd<>"0") {
                $pkodeid=$srow['kodeid'];
                $psubkode=$srow['subkode'];
                $pdivisi=$srow['divisi'];
                $pcoa="000-0";//intransit jkt
            }
            
        }
        
        
        
        
        if (empty($pkodeid)) $pkodeid="5"; //bank
        if (empty($pidinputspd)) $pidinputspd="0"; //bank
        
        $bolehpilihnobukti=false;
        
        if ($act=="input") {
            
            $bolehpilihnobukti=true;
            
            $sql=  mysqli_query($cnmy, "select MAX(RIGHT(idinputbank,8)) as NOURUT from dbmaster.t_suratdana_bank");
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
            
            $pblninput= date("Ym", strtotime($ptglinputpl));
            if (!empty($ptglaslinput)) $ptglaslinput= date("Ym", strtotime($ptglaslinput));

            //echo "$pblninput - $ptglaslinput, $pstsinput - $pstsaslinput";

            if ($pblninput<>$ptglaslinput) $bolehpilihnobukti=true;
            if ($pstsinput<>$pstsaslinput) $bolehpilihnobukti=true;
            
        }
        
        //echo $bolehpilihnobukti; exit;
        
        if ($bolehpilihnobukti==true) {
            
            if ($pstsinput=="D" OR $pstsinput=="K") {
                
                $pnobukti="";
                $p_no="1";
                $p_buknin="BBM";
                $p_fieldno="nobbm";
                if ($pstsinput=="K") {
                    $p_no="2";
                    $p_buknin="BBK";
                    $p_fieldno="nobbk";
                }
                
                include "../cari_nomorbukti.php";
                include "../../../config/fungsi_combo.php";
                $ppilih_nobukti=caribuktinomor("2", $p_no, $ptglinput);// 1=bbm, 2=bbm
                
                $pbukti_periode=date('Ym', strtotime($ptglinput));;
                $pblnini = date('m', strtotime($ptglinput));
                $pthnini = date('Y', strtotime($ptglinput));
                $mbulan=CariBulanHuruf($pblnini);
                $ppilih_blnthn="/".$mbulan."/".$pthnini;
                $pnobukti = $p_buknin.$ppilih_nobukti."/".$mbulan."/".$pthnini;
        
                //echo "$ppilih_nobukti, nobukti : $pnobukti<br/>";
                
                $query = "select nobukti from dbmaster.t_suratdana_bank WHERE nobukti='$pnobukti' AND IFNULL(stsnonaktif,'')<>'Y'";
                $tampil= mysqli_query($cnmy, $query);
                $ketemua=  mysqli_num_rows($tampil);
                if ((INT)$ketemua>0) {
                    echo "nomor bukti tersebut sudah ada";
                    mysqli_close($cnmy);
                    exit;
                }
                
        
                $query = "SELECT * FROM dbmaster.t_setup_bukti WHERE bulantahun='$pbukti_periode'";
                $showkan= mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($showkan);
                if ((INT)$ketemu<=0){
                    mysqli_query($cnmy, "INSERT INTO dbmaster.t_setup_bukti (bulantahun, $p_fieldno)VALUES('$pbukti_periode', '$ppilih_nobukti')");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                }else{
                    mysqli_query($cnmy, "UPDATE dbmaster.t_setup_bukti SET $p_fieldno='$ppilih_nobukti' WHERE bulantahun='$pbukti_periode'");
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                }
        
            }
            
        }
        
        
        //echo "<br/>ID : $kodenya, nobukti : $pnobukti, TGL. transaksi : $ptglinput, Jenis/subkode : $pkodeid - $psubkode, coa : $pcoa, divisi : $pdivisi, sts : $pstatus, stsinput : $pstsinput<br/>";
        //echo "jumlah : $pjumlah, keternagan : $pketerangan<br/>";
        //echo "idinput : $pidinputspd, nodivisi : $pnodivisi, nomor : $pnospd<br/>";
        //exit;
    
        
        if ($act=="input") {
            
            $query = "INSERT INTO dbmaster.t_suratdana_bank (idinputbank, tanggal, coa4, kodeid, subkode, idinput, nomor, nodivisi, "
                    . " nobukti, divisi, sts, jumlah, keterangan, userid, stsinput, brid, noslip, realisasi, customer, aktivitas1)values"
                    . "('$kodenya', '$ptglinput', '$pcoa', '$pkodeid', '$psubkode', '$pidinputspd', '$pnospd', '$pnodivisi', "
                    . " '$pnobukti', '$pdivisi', '$pstatus', '$pjumlah', '$pketerangan', '$pkaryawanid', '$pstsinput', '$pnobrid', '$pnoslipbr', '$prealisasibr', '$pcustomerbr', '$paktivitasbr')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
        }else{
            
            $query = "UPDATE dbmaster.t_suratdana_bank SET tanggal='$ptglinput', "
                    . " coa4='$pcoa', kodeid='$pkodeid', subkode='$psubkode', idinput='$pidinputspd', nomor='$pnospd', nodivisi='$pnodivisi', "
                    . " divisi='$pdivisi', sts='$pstatus', jumlah='$pjumlah', "
                    . " keterangan='$pketerangan', userid='$pkaryawanid', stsinput='$pstsinput', "
                    . " brid='$pnobrid', noslip='$pnoslipbr', realisasi='$prealisasibr', customer='$pcustomerbr', aktivitas1='$paktivitasbr' WHERE "
                    . " idinputbank='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            if ($bolehpilihnobukti==true) {
                $query = "UPDATE dbmaster.t_suratdana_bank SET nobukti='$pnobukti' WHERE idinputbank='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
            
            
        }
        
        
        
        mysqli_close($cnmy);
        if ($act=="update") {
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
        }else{
            header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilsimpan');
        }
        
        
        
    }
    
}