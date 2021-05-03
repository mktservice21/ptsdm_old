<?PHP
session_start();

date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);


$puserid="";
$pidcard="";
$pidgroup="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
if (isset($_SESSION['GROUP'])) $pidgroup=$_SESSION['GROUP'];


$module=$_GET['module'];
$act=$_GET['act'];
$idmenu=$_GET['idmenu'];

if ($module=='mktformcutiho')
{
    if ($act=="hapus") {
        include "../../../config/koneksimysqli.php";
        
        $kodenya=$_GET['id'];
        $pkethapus=$_GET['kethapus'];
        if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);

        mysqli_query($cnmy, "UPDATE hrd.t_cuti0 SET stsnonaktif='Y' WHERE idcuti='$kodenya' LIMIT 1");
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
        
        $ppengajuan="HO";
        $pidjabatan=$_POST['e_idjbt'];
        $kodenya=$_POST['e_id'];
        $pidjeniscuti=$_POST['cb_jeniscuti'];
        $pkeperluan=$_POST['e_keperluan'];
        $pbln01=$_POST['e_bulan01'];
        $pbln02=$_POST['e_bulan02'];
        
        $pbulan1= date("Y-m-d", strtotime($pbln01));
        $pbulan2= date("Y-m-d", strtotime($pbln02));
        
        
        if (empty($pidjabatan)) {
            if (isset($_SESSION['JABATANID'])) $pidjabatan=$_SESSION['JABATANID'];
        }
        
        if (!empty($pkeperluan)) $pkeperluan = str_replace("'", " ", $pkeperluan);
        
        
        $pidatasan4=$_POST['e_atasan'];
        $pidatasan5=$_POST['e_kdcoo'];
        
        $ptglpiliha="";
        $pbolehutksimapan=false;
        if (isset($_POST['chktgl'])) {
            foreach ($_POST['chktgl'] as $pidtgl) {
                if (empty($pidtgl)) {
                    //continue;
                }
                if (strpos($ptglpiliha, $pidtgl)==false) {
                    $ptglpiliha .="'".$pidtgl."',";
                    //echo "$pidtgl<br/>";
                    $pbolehutksimapan=true;
                }
                    
            }
        }
        
        
        if ($pidjeniscuti=="01" AND $pbolehutksimapan==false) {
            echo "tanggal belum dipilih";
            mysqli_close($cnmy);
            exit;
        }else{
            $pbolehutksimapan=true;
        }
        
        //echo "$pkaryawanid - $pidjabatan - $pidjeniscuti keperluan $pkeperluan, $ptglpiliha - $pbulan1 s/d. $pbulan2"; mysqli_close($cnmy); exit;
        
        
        if ($pbolehutksimapan == true) {
            
            
            if ($act=="input") {
                
                
                $pilihantgl=$ptglpiliha;
                if (!empty($pilihantgl)) $pilihantgl="(".substr($pilihantgl, 0, -1).")";
                else $pilihantgl="('')";
                
                if ($pidjeniscuti=="02") {
                    $pbln1= date("Ym", strtotime($pbln01));
                    $pbln2= date("Ym", strtotime($pbln02));
                    if ($pbln1>$pbln2) {
                        mysqli_close($cnmy); echo "Bulan tidak sesuai..."; exit;
                    }

                    $query = "select distinct a.bulan1 from hrd.t_cuti0 as a LEFT JOIN hrd.t_cuti1 as b "
                            . " on a.idcuti=b.idcuti WHERE a.idcuti<>'$kodenya' AND "
                            . " (b.tanggal in $pilihantgl OR (DATE_FORMAT(a.bulan1,'%Y%m') BETWEEN '$pbln1' AND '$pbln2') OR (DATE_FORMAT(a.bulan2,'%Y%m') BETWEEN '$pbln1' AND '$pbln2') ) "
                            . " AND a.karyawanid='$pkaryawanid' AND IFNULL(a.stsnonaktif,'')<>'Y' ";//AND a.id_jenis='$pidjeniscuti'
                    //echo "$query<br/>";
                }else{
                    $query = "select distinct b.tanggal from hrd.t_cuti0 as a JOIN hrd.t_cuti1 as b "
                            . " on a.idcuti=b.idcuti WHERE a.idcuti<>'$kodenya' AND b.tanggal in $pilihantgl AND a.karyawanid='$pkaryawanid' AND IFNULL(a.stsnonaktif,'')<>'Y'";
                }

                $tampilp=mysqli_query($cnmy, $query);
                $ketemup=mysqli_num_rows($tampilp);
                if ((INT)$ketemup>0) {
                    echo "Salah satu tanggal yang dipilih sudah ada..., silakan pilih tanggal yang lain"; mysqli_close($cnmy); exit;
                }
                
                //exit;
                
                $query = "INSERT INTO hrd.t_cuti0 (karyawanid, jabatanid, id_jenis, keperluan, bulan1, bulan2, userid, pengajuan)
                    VALUES
                    ('$pkaryawanid', '$pidjabatan', '$pidjeniscuti', '$pkeperluan', '$pbulan1', '$pbulan2', '$pidcard', '$ppengajuan')";
                mysqli_query($cnmy, $query); 
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

                $kodenya = mysqli_insert_id($cnmy);
                
                $query = "INSERT INTO dbttd.t_cuti_ttd (idcuti) VALUES ('$kodenya')";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                
                if ($act=="input") {
                    $pimgttd=$_POST['txtgambar'];
                    $query = "update dbttd.t_cuti_ttd set gambar='$pimgttd' WHERE idcuti='$kodenya' LIMIT 1";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                }
            
            }elseif ($act=="update") {
                
                $query = "UPDATE hrd.t_cuti0 SET karyawanid='$pkaryawanid', jabatanid='$pidjabatan', id_jenis='$pidjeniscuti', "
                        . " keperluan='$pkeperluan', bulan1='$pbulan1', bulan2='$pbulan2', userid='$pidcard', pengajuan='$ppengajuan' WHERE idcuti='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); 
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
                
            }
            
            
            $ptglpiliha="";
            unset($pinsert_data_detail);//kosongkan array
            $psimpandata=false;
            if (isset($_POST['chktgl'])) {
                foreach ($_POST['chktgl'] as $pidtgl) {
                    if (empty($pidtgl)) {
                        //continue;
                    }
                    if (strpos($ptglpiliha, $pidtgl)==false) {
                        $ptglpiliha .="'".$pidtgl."',";
                        //echo "$pidtgl<br/>";
                        $pinsert_data_detail[] = "('$kodenya', '$pidtgl')";
                        $psimpandata=true;
                    }

                }
            }

            
            if ($psimpandata==true) {

                mysqli_query($cnmy, "DELETE FROM hrd.t_cuti1 WHERE idcuti='$kodenya'");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

                $query_detail="INSERT INTO hrd.t_cuti1 (idcuti, tanggal) VALUES ".implode(', ', $pinsert_data_detail);
                mysqli_query($cnmy, $query_detail);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            }
        
        

        
            $query = "UPDATE hrd.t_cuti0 SET atasan1='', tgl_atasan1=NOW(), atasan2='', tgl_atasan2=NOW(), atasan3='', tgl_atasan3=NOW() WHERE idcuti='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }

            
            if ($pidatasan4==$pidatasan5) {//SPV MGR DLL
                $query = "UPDATE hrd.t_cuti0 SET atasan4='', tgl_atasan4=NOW(), atasan5='$pidatasan5' WHERE idcuti='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }else{
                $query = "UPDATE hrd.t_cuti0 SET atasan4='$pidatasan4' WHERE idcuti='$kodenya' LIMIT 1";
                mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            }
            
            
            
            mysqli_close($cnmy);
            if ($act=="update") {
                header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
            }else{
                header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
            }
        
        }
        
        
    }
    
}