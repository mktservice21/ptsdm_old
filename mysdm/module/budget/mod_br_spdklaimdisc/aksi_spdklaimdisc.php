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


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

if ($module=='spdklaimdisc')
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

        mysqli_query($cnmy, "UPDATE dbmaster.t_suratdana_br SET stsnonaktif='Y', keterangan=CONCAT(IFNULL(keterangan,''), ' $pkethapus', USER HAPUS : $pnmlengkapuser') WHERE idinput='$kodenya' LIMIT 1");
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
        
        
        
        $kodenya=$_POST['e_id'];
        $ptglinput=$_POST['e_tglberlaku'];
        $pdivisi=$_POST['cb_divisi'];
        $pjenis=$_POST['cb_jenispilih'];
        $pkodeid=$_POST['cb_kode'];
        $psubkode=$_POST['cb_kodesub'];
        $pnodivisi=$_POST['e_nomordiv'];
        $ptgltipe=$_POST['cb_pertipe'];
        $pperiode1=$_POST['e_periode1'];
        $pperiode2=$_POST['e_periode2'];
        $pjumlah=$_POST['e_jmlusulan'];
        $pketerangan=$_POST['e_keterangan'];
        $plampiran="";
        
        
        $ptglinput= date("Y-m-d", strtotime($ptglinput));
        $pperiode1= date("Y-m-d", strtotime($pperiode1));
        $pperiode2= date("Y-m-d", strtotime($pperiode2));
        if (empty($pkodeid)) $pkodeid="1";
        if (empty($psubkode)) $psubkode="01";
        if (!empty($pnodivisi)) $pnodivisi = str_replace("'", " ", $pnodivisi);
        if (!empty($pketerangan)) $pketerangan = str_replace("'", " ", $pketerangan);
        
        $pjumlah=str_replace(",","", $pjumlah);
        if (empty($pjumlah)) $pjumlah=0;
        
        
        $query = "select nodivisi FROM dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' AND idinput<>'$kodenya' AND nodivisi='$pnodivisi' AND karyawanid='$pkaryawanid'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            
            echo "GAGAL... nodivisi tersebut sudah ada, silakan coba lagi..."; mysqli_close($cnmy); exit;
            
        }
        
        $pbolehsimpan=false;
        foreach ($_POST['chk_jml1'] as $no_brid) {
            
            $pjmlinputpilih= $_POST['txt_jml'][$no_brid];
            if (empty($pjmlinputpilih)) $pjmlinputpilih=0;
            $pjmlinputpilih=str_replace(",","", $pjmlinputpilih);
            
            if ((DOUBLE)$pjmlinputpilih<>0) $pbolehsimpan=true;
        }
        
        if ($pbolehsimpan==false) {
            echo "tidak ada data yang akan disimpan..."; mysqli_close($cnmy); exit;
        }
        
        if ($act=="input") {
            $sql=  mysqli_query($cnmy, "select MAX(idinput) as NOURUT from dbmaster.t_suratdana_br");
            $ketemu=  mysqli_num_rows($sql);
            $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
            if ($ketemu>0){
                $o=  mysqli_fetch_array($sql);
                $urut=$o['NOURUT']+1;
                $kodenya=$urut;
            }
        }
        
        if (empty($kodenya)) {
            echo "kode input kosong..."; mysqli_close($cnmy); exit;
        }
        
        if (empty($pnodivisi)) {
            echo "nomor divisi kosong..."; mysqli_close($cnmy); exit;
        }
        
        //echo "$kodenya : $ptglinput, $ptgltipe : $pperiode1 sd. $pperiode2, $pdivisi, jenis : $pjenis, kode $pkodeid - $psubkode, $pnodivisi, $pketerangan";
        
        
        $pcoa="101-02-002";
        if ($act=="input") {

            $query = "INSERT INTO dbmaster.t_suratdana_br (idinput, divisi, kodeid, subkode, tgl, nodivisi, jumlah, "
                    . " userid, coa4, lampiran, tglf, tglt, karyawanid, periodeby, jenis_rpt, keterangan)values"
                    . "('$kodenya', '$pdivisi', '$pkodeid', '$psubkode', '$ptglinput', '$pnodivisi', '$pjumlah', "
                    . " '$pkaryawanid', '$pcoa', '$plampiran', '$pperiode1', '$pperiode2', '$pidcard', '$ptgltipe', '$pjenis', '$pketerangan')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }else{
            $query = "UPDATE dbmaster.t_suratdana_br SET "
                    . " divisi='$pdivisi', kodeid='$pkodeid', subkode='$psubkode', "
                    . " tgl='$ptglinput', nodivisi='$pnodivisi', jumlah='$pjumlah', "
                    . " userid='$pkaryawanid', coa4='$pcoa', lampiran='$plampiran', "
                    . " tglf='$pperiode1', tglt='$pperiode2', karyawanid='$pidcard', "
                    . " periodeby='$ptgltipe', jenis_rpt='$pjenis', keterangan='$pketerangan' "
                    . " WHERE idinput='$kodenya'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        }
        
        
        if ($pjenis=="B" OR $pjenis=="V" OR $pjenis=="C") {
            $query_pilih = "UPDATE dbmaster.t_suratdana_br SET pilih='N' WHERE idinput='$kodenya' LIMIT 1";
        }else{
            $query_pilih = "UPDATE dbmaster.t_suratdana_br SET pilih='Y' WHERE idinput='$kodenya' LIMIT 1";
        }
        mysqli_query($cnmy, $query_pilih);
        $erropesan = mysqli_error($cnmy); 
        if (!empty($erropesan)) { 
            echo $erropesan."<br/>"; 
            if ($act=="input") {
                mysqli_query($cnmy, "UPDATE dbmaster.t_suratdana_br SET stsnonaktif='Y', keterangan=CONCAT(IFNULL(keterangan,''), ' GAGAL') WHERE idinput='$kodenya' LIMIT 1");
            }
            mysqli_close($cnmy);
            exit; 
        }
        
        $kodeinput="E";
        unset($pinst_prod_data);//kosongkan array
        $pbolehsimpan=false;
        foreach ($_POST['chk_jml1'] as $no_brid) {
            $purutan=$_POST['cb_urut'][$no_brid];
            if (empty($purutan)) $purutan="0";
            
            $pjmlinputpilih= $_POST['txt_jml'][$no_brid];
            if (empty($pjmlinputpilih)) $pjmlinputpilih=0;
            $pjmlinputpilih=str_replace(",","", $pjmlinputpilih);
            
            $ptrans_ke="";
            if (isset($_POST['chk_transke'][$no_brid])) $ptrans_ke=$_POST['chk_transke'][$no_brid];
                        
            //echo "$no_brid - $purutan, $pjmlinputpilih ($ptrans_ke)<br/>";
            //idinput, bridinput, kodeinput, urutan, amount, trans_ke, jml_adj, aktivitas1
            
            $pinst_prod_data[] = "('$kodenya','$no_brid','$kodeinput',$purutan,'$pjmlinputpilih','$ptrans_ke')";
            $pbolehsimpan=true;
        }
        
        if ($pbolehsimpan==true) {
            
            $query = "DELETE FROM dbmaster.t_suratdana_br1 WHERE idinput='$kodenya'";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); 
            if (!empty($erropesan)) { 
                echo $erropesan."<br/>"; 
                if ($act=="input") {
                    mysqli_query($cnmy, "UPDATE dbmaster.t_suratdana_br SET stsnonaktif='Y', keterangan=CONCAT(IFNULL(keterangan,''), ' GAGAL') WHERE idinput='$kodenya' LIMIT 1");
                }
                mysqli_close($cnmy);
                exit;
            }
            
            
            $query_prod_ins = "INSERT INTO dbmaster.t_suratdana_br1(idinput, bridinput, kodeinput, urutan, amount, trans_ke) VALUES "
                    . "".implode(', ', $pinst_prod_data);
            mysqli_query($cnmy, $query_prod_ins);
            $erropesan = mysqli_error($cnmy); 
            if (!empty($erropesan)) { 
                echo $erropesan."<br/>"; 
                if ($act=="input") {
                    mysqli_query($cnmy, "UPDATE dbmaster.t_suratdana_br SET stsnonaktif='Y', keterangan=CONCAT(IFNULL(keterangan,''), ' GAGAL') WHERE idinput='$kodenya' LIMIT 1");
                }
                mysqli_close($cnmy);
                exit;
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
?>

