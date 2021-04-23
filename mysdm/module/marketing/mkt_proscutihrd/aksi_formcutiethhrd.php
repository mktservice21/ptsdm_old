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

if ($module=='mktproscutihrd')
{
    if ($act=="hapus") {


        exit;
    }elseif ($act=="input" OR $act=="update") {
        $pkaryawanid_=$_POST['e_idcarduser'];
        if (empty($pkaryawanid_)) $pkaryawanid_=$pidcard;
        
        if (empty($pkaryawanid_)) {
            echo "ANDA HARUS LOGIN ULANG...";
            exit;
        }


        include "../../../config/koneksimysqli.php";
        
        
        $pkaryawanid=$_POST['cb_karyawanid'];
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
        
        
        if ($pidjeniscuti=="00" AND $pbolehutksimapan==false) {
            echo "tanggal belum dipilih";
            mysqli_close($cnmy);
            exit;
        }else{
            $pbolehutksimapan=true;
        }
        
        $pjbt_k="";
        $patasanid1_k="";
        $patasanid2_k="";
        $pdivisiid_k="";
        
        //echo "$pkaryawanid - $pidjabatan - $pidjeniscuti keperluan $pkeperluan, $ptglpiliha - $pbulan1 s/d. $pbulan2"; mysqli_close($cnmy); exit;
        
        
        if ($pbolehutksimapan == true) {
            
            $query = "select karyawanId as karyawanid, nama, jabatanId as jabatanid, "
                    . " atasanId as atasanid, atasanId2 as atasanid2, divisiId as divisiid "
                    . " FROM hrd.karyawan WHERE karyawanid='$pkaryawanid'";
            $tampilk=mysqli_query($cnmy, $query);
            $ketemuk= mysqli_num_rows($tampilk);
            if ((INT)$ketemuk>0) {
                $rowk= mysqli_fetch_array($tampilk);
                $pjbt_k=$rowk['jabatanid'];
                $patasanid1_k=$rowk['atasanid'];
                $patasanid2_k=$rowk['atasanid2'];
                $pdivisiid_k=$rowk['divisiid'];
            }
            
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

                    $query = "select distinct b.tanggal from hrd.t_cuti0 as a JOIN hrd.t_cuti1 as b "
                            . " on a.idcuti=b.idcuti WHERE a.idcuti<>'$kodenya' AND "
                            . " (b.tanggal in $pilihantgl OR (DATE_FORMAT(a.bulan1,'%Y%m') BETWEEN '$pbln1' AND '$pbln2') OR (DATE_FORMAT(a.bulan2,'%Y%m') BETWEEN '$pbln1' AND '$pbln2') ) "
                            . " AND a.karyawanid='$pkaryawanid'";
                }else{
                    $query = "select distinct b.tanggal from hrd.t_cuti0 as a JOIN hrd.t_cuti1 as b "
                            . " on a.idcuti=b.idcuti WHERE a.idcuti<>'$kodenya' AND b.tanggal in $pilihantgl AND a.karyawanid='$pkaryawanid'";
                }

                $tampilp=mysqli_query($cnmy, $query);
                $ketemup=mysqli_num_rows($tampilp);
                if ((INT)$ketemup>0) {
                    echo "Salah satu tanggal yang dipilih sudah ada..., silakan pilih tanggal yang lain"; mysqli_close($cnmy); exit;
                }
                
            }
    
            //echo "$pjbt_k, $patasanid1_k, $patasanid2_k, $pdivisiid_k"; exit;
            
            if ($act=="input") {
                
                $query = "INSERT INTO hrd.t_cuti0 (karyawanid, jabatanid, id_jenis, keperluan, bulan1, bulan2, userid)
                    VALUES
                    ('$pkaryawanid', '$pjbt_k', '$pidjeniscuti', '$pkeperluan', '$pbulan1', '$pbulan2', '$pidcard')";
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
                        . " keperluan='$pkeperluan', bulan1='$pbulan1', bulan2='$pbulan2', userid='$pidcard' WHERE idcuti='$kodenya' LIMIT 1";
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
        
            
            
            $query = "UPDATE hrd.t_cuti0 SET tgl_atasan1=NOW(), tgl_atasan2=NOW(), tgl_atasan3=NOW(), tgl_atasan4=NOW(), tgl_atasan5=NOW() WHERE idcuti='$kodenya' LIMIT 1";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
            
            
            
            mysqli_close($cnmy);
            if ($act=="update") {
                header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
            }else{
                header('location:../../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=berhasilupdate');
            }
        
        }
        
        
    }
    
}