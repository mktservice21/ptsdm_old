<?php
function getUserIPAddrAbs()
{
	// Get real visitor IP behind CloudFlare network
	if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
			  $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}
	$client  = @$_SERVER['HTTP_CLIENT_IP'];
	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	$remote  = $_SERVER['REMOTE_ADDR'];

	if(filter_var($client, FILTER_VALIDATE_IP))
	{
		$ip = $client;
	}
	elseif(filter_var($forward, FILTER_VALIDATE_IP))
	{
		$ip = $forward;
	}
	else
	{
		$ip = $remote;
	}

	return $ip;
}


date_default_timezone_set('Asia/Jakarta');
ini_set("memory_limit","512M");
ini_set('max_execution_time', 0);
	
	
session_start();


$puser_ipaddr = getUserIPAddrAbs();
if ($puser_ipaddr=="::1") $puser_ipaddr="";
$_SESSION['IDADDRESS_SYS']=$puser_ipaddr;
	
	
$pcardidabsen="";
if (isset($_SESSION['IDCARD'])) $pcardidabsen=$_SESSION['IDCARD'];

if (empty($pcardidabsen)) {
    echo "Anda Harus Login Ulang... (GAGAL)"; exit;
}

$puser_ipaddr="";
if (isset($_SESSION['IDADDRESS_SYS'])) $puser_ipaddr=$_SESSION['IDADDRESS_SYS'];

$pmodule="";
$pact="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
if (isset($_GET['act'])) $pact=$_GET['act'];

$pberhasil="GAGAL";

if ($pmodule=="hrdabsenmasuk" AND ($pact=="absenmasuk" || $pact=="absenpulang")) {
    
    //mendefinisikan folder
    define('UPLOAD_DIR', '../../../images/foto_absen/');

    $pkey        = $_POST['ukey'];
    $plangitut   = $_POST['ulatitude'];
    $plongitut   = $_POST['ulongitude'];
    $pimg        = $_POST['image'];

    $pnamaabse="";
    if ($pkey=="1") $pnamaabse="Absen Masuk";
    elseif ($pkey=="2") $pnamaabse="Absen Pulang";
    elseif ($pkey=="3") $pnamaabse="Absen Istirahat";
    elseif ($pkey=="4") $pnamaabse="Masuk Dari Istirahat";
    
    $pketabsen="";
    if (isset($_POST['uketerangan'])) $pketabsen   = $_POST['uketerangan'];
    if (!empty($pketabsen)) $pketabsen = str_replace("'", " ", $pketabsen);
    
    
    if (empty($plangitut) OR empty($plongitut)) {
        $pberhasil= "GAGAL... Lokasi Kosong";
        echo "$pberhasil"; exit;
    }
    
    if (empty($pkey)) {
        echo "$pberhasil"; exit;
    }
    
    
    $ptangga=date("d F Y");
    $pjam=date("H i s");
    
    $ptglabsen_g=date("Y-m-d");
    $pjamabsen_g=date("H:i");
    
    
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_sql.php";
    
    
    $ptglabsen="";
    $pjamabsen="";
    $query = "select CURRENT_DATE() as tglsekarang, DATE_FORMAT(CURRENT_TIME(),'%H:%i') as jamsekarang";
    $tampil_j= mysqli_query($cnmy, $query);
    $ketemu_j= mysqli_num_rows($tampil_j);
    if ((INT)$ketemu_j>0) {
        $jrow= mysqli_fetch_array($tampil_j);
        $ptglabsen=$jrow['tglsekarang'];
        $pjamabsen=$jrow['jamsekarang'];
    }
    
    if (empty($pjamabsen)) $pjamabsen=$pjamabsen_g;
    if (empty($ptglabsen)) $ptglabsen=$ptglabsen_g;
    
    if (empty($ptglabsen) OR empty($pjamabsen)) {
        //echo "GAGAL...\n"."Tanggal atau Jam Absen Kosong...";
        //exit;
    }
    
    //echo "GAGAL... JAM : $pjamabsen, TGL : $ptglabsen"; exit;
    
    
    
    
    $pabsensiwfh=false;    
    $a_lat="";
    $a_long="";
    $a_radius="";
    $a_idstaus="HO1";
    
    $query = "select * from hrd.karyawan_absen WHERE karyawanid='$pcardidabsen' AND IFNULL(aktif,'')='Y'";
    $tampilwfh= mysqli_query($cnmy, $query);
    $ketemuwfh= mysqli_num_rows($tampilwfh);
    if ((INT)$ketemuwfh>0) {
        $row= mysqli_fetch_array($tampilwfh);
        $a_lat=$row['a_latitude'];
        $a_long=$row['a_longitude'];
        $a_radius=$row['a_radius'];
        $a_idstaus=$row['id_status'];
        $pabsensiwfh=true;
    }
    if (empty($a_lat)) $a_lat=0;
    if (empty($a_long)) $a_long=0;
    if (empty($a_radius)) $a_radius=0;
    
    
    $query = "select * from hrd.sdm_lokasi WHERE id_status='$a_idstaus'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $sdmlat=$row['sdm_latitude'];
    $sdmlong=$row['sdm_longitude'];
    $sdmradius=$row['sdm_radius'];
    
    //KHUSUS
    $queryR = "select sdm_radius from hrd.sdm_lokasi_radius_ex WHERE karyawanid='$pcardidabsen'";
    $tampilR= mysqli_query($cnmy, $queryR);
    $ketemuR= mysqli_num_rows($tampilR);
    if ((INT)$ketemuR>0) {
        $nrow= mysqli_fetch_array($tampilR);
        $nex_radius=$nrow['sdm_radius'];
        if (empty($nex_radius)) $nex_radius=0;
        
        if ($nex_radius<>"0") {
            $sdmradius=$nex_radius;
        }
        
    }
    //echo $sdmradius; exit;
    
    if (empty($sdmlat)) $sdmlat=0;
    if (empty($sdmlong)) $sdmlong=0;
    if (empty($sdmradius)) $sdmradius=0;
    
    $plangitut_rds="";
    $plongitut_rds="";
    $pradius_rds="";
    
    if ($pabsensiwfh==true) {
        $plangitut_rds=$a_lat;
        $plongitut_rds=$a_long;
        $pradius_rds=$a_radius;
    }else{
        $plangitut_rds=$sdmlat;
        $plongitut_rds=$sdmlong;
        $pradius_rds=$sdmradius;
    }
    
    if ( ((INT)$a_lat==0 || (INT)$a_long==0) AND ((INT)$sdmlat==0 || (INT)$sdmlong==0) ) {
        mysqli_close($cnmy);
        echo "GAGAL...\n"."Tidak ada pengaturan absen...";
        exit;
    }
    
    
    $pjarak_absen=getDistanceBetween($plangitut, $plongitut, $plangitut_rds, $plongitut_rds, $unit = 'Mi');
    $pjarak_absen_wfo=getDistanceBetween($plangitut, $plongitut, $sdmlat, $sdmlong, $unit = 'Mi');
    $pjarak_absen_wfh=getDistanceBetween($plangitut, $plongitut, $a_lat, $a_long, $unit = 'Mi');
    
    
    
    
    
    if ( ((DOUBLE)$pjarak_absen_wfo>(DOUBLE)$sdmradius) AND ((DOUBLE)$pjarak_absen_wfh>(DOUBLE)$a_radius) ) {
        mysqli_close($cnmy);
        echo "GAGAL...\n"."Lokasi ABSEN Tidak Sesuai...\n"."Jarak dari Kantor : ".$pjarak_absen_wfo." \n"."Jarak dari Rumah : ".$pjarak_absen_wfh." ";
        exit;
    }
    
    $pjarakdarilokasi=$pjarak_absen_wfh;
    $plokasiabs="WFH";
    if ( (DOUBLE)$pjarak_absen_wfo<=(DOUBLE)$sdmradius ) {
        $plokasiabs="WFO";
        $pjarakdarilokasi=$pjarak_absen_wfo;
    }
    
    //mysqli_close($cnmy); echo "jarak : $pjarak_absen, radius sdm : $pjarak_absen_wfo, radius rumah : $pjarak_absen_wfh,  status : $plokasiabs"; exit;

    
    
    
    if ($pkey=="2" OR $pkey=="3" OR $pkey=="4") {
        $query = "select * from hrd.t_absen WHERE tanggal=CURRENT_DATE() AND kode_absen='1' AND karyawanid='$pcardidabsen'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu==0) {
            mysqli_close($cnmy);
            echo "Tidak ada proses absen...\n"."Karena anda belum absen masuk...";
            exit;
        }
    }
    
    
    if ($pkey=="3" OR $pkey=="4") {
        $query = "select * from hrd.t_absen WHERE tanggal=CURRENT_DATE() AND kode_absen='2' AND karyawanid='$pcardidabsen'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            mysqli_close($cnmy);
            echo "Tidak ada proses absen...\n"."Karena sudah absen pulang...";
            exit;
        }
    }
    
    if ($pkey=="2") {
        $query = "select * from hrd.t_absen WHERE tanggal=CURRENT_DATE() AND kode_absen='3' AND karyawanid='$pcardidabsen'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu>0) {
            
            $query = "select * from hrd.t_absen WHERE tanggal=CURRENT_DATE() AND kode_absen='4' AND karyawanid='$pcardidabsen'";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            
            if ((INT)$ketemu==0) {
                //mysqli_close($cnmy);
                //echo "Tidak ada proses absen...\n"."Karena anda belum absen masuk dari istirahat...";
                //exit;
            }
            
        }else{
            mysqli_close($cnmy);
            echo "Tidak ada proses absen...\n"."Karena anda belum absen istirahat...";
            exit;
        }
    }
    
    if ($pkey=="4") {
        $query = "select * from hrd.t_absen WHERE tanggal=CURRENT_DATE() AND kode_absen='3' AND karyawanid='$pcardidabsen'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ((INT)$ketemu==0) {
            mysqli_close($cnmy);
            echo "Tidak ada proses absen...\n"."Karena anda belum absen istirahat...";
            exit;
        }
    }
    
    $psudahpernahabsen_masuk=false;
    $pkodesudahinput="";
    $query = "select * from hrd.t_absen WHERE tanggal=CURRENT_DATE() AND kode_absen='$pkey' AND karyawanid='$pcardidabsen'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        if ($pkey=="1") {
            
            $row= mysqli_fetch_array($tampil);
            $pkodesudahinput=$row['idabsen'];
            $psudahpernahabsen_masuk=true;
            if ($pkodesudahinput=="0") $pkodesudahinput="";
            
            $query_ = "select * from hrd.t_absen WHERE tanggal=CURRENT_DATE() AND kode_absen IN ('2', '3', '4') AND karyawanid='$pcardidabsen'";
            $tampil_= mysqli_query($cnmy, $query_);
            $ketemu_= mysqli_num_rows($tampil_);
            if ((INT)$ketemu_>0) {
                mysqli_close($cnmy);
                echo "Absen Masuk tidak bisa diulang...\n"."Karena sudah absen Istirahat/Pulang...";
                exit;
            }
            
        }elseif ($pkey=="2") {
            
            $row= mysqli_fetch_array($tampil);
            $pkodesudahinput=$row['idabsen'];
            $psudahpernahabsen_masuk=true;
            if ($pkodesudahinput=="0") $pkodesudahinput="";
			
        }else{
            
            mysqli_close($cnmy);
            echo "Tidak ada proses absen...\n"."Karena anda sudah $pnamaabse..., tidak bisa diulang";
            exit;
            
        }
        
    }
    
    //cek Lokasi MASUK WFO atau WFH
    $psudahabsenmasuk_status="";
    if ($pkey<>"1") {
		
		if ($pcardidabsen=="0000002262") {
		}else{
			
			$query = "select * from hrd.t_absen WHERE tanggal=CURRENT_DATE() AND kode_absen='1' AND karyawanid='$pcardidabsen'";
			$tampil_= mysqli_query($cnmy, $query);
			$nrow= mysqli_fetch_array($tampil_);
			$psudahabsenmasuk_status=$nrow['l_status'];

			if (!empty($psudahabsenmasuk_status)) {
				if ($plokasiabs<>$psudahabsenmasuk_status) {
					mysqli_close($cnmy);
					echo "Tidak bisa absen pulang...\n"."Absen masuk anda $psudahabsenmasuk_status...";
					exit;
				}
			}
			
		}
        
    }
        
	//NIKA (khusus)
	if ($pcardidabsen=="0000002262") {
		$pmyip_khusus=substr($puser_ipaddr,0,11);
		if ($pmyip_khusus=="103.130.192") {
			$plokasiabs="WFO";
		}else{
			$plokasiabs="WFH";
		}
		
		//echo "GAGAL... TEST, $plokasiabs ($pmyip_khusus)"; exit;
	}
		
    
    if ($psudahpernahabsen_masuk==true AND $pkey=="1" AND !empty($pkodesudahinput)) {
        
        
        $query = "UPDATE hrd.t_absen SET jam=DATE_FORMAT(CURRENT_TIME(),'%H:%i'), l_latitude='$plangitut', l_longitude='$plongitut', l_status='$plokasiabs', l_radius='$pradius_rds', l_jarak='$pjarakdarilokasi', keterangan='$pketabsen' WHERE idabsen='$pkodesudahinput' AND 
				kode_absen='1' AND tanggal=CURRENT_DATE() AND karyawanid='$pcardidabsen' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        $pkodeid=$pkodesudahinput;
        
    }elseif ($psudahpernahabsen_masuk==true AND $pkey=="2" AND !empty($pkodesudahinput)) {
        
        $query = "UPDATE hrd.t_absen SET jam=DATE_FORMAT(CURRENT_TIME(),'%H:%i'), l_latitude='$plangitut', l_longitude='$plongitut', l_status='$plokasiabs', l_radius='$pradius_rds', l_jarak='$pjarakdarilokasi', keterangan='$pketabsen' WHERE idabsen='$pkodesudahinput' AND "
                . " kode_absen='2' AND tanggal=CURRENT_DATE() AND karyawanid='$pcardidabsen' LIMIT 1";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        $pkodeid=$pkodesudahinput;
		
    }else{
    
        $query = "INSERT INTO hrd.t_absen(kode_absen, karyawanid, tanggal, jam, l_latitude, l_longitude, l_status, l_radius, l_jarak, keterangan)VALUES"
                . "('$pkey', '$pcardidabsen', CURRENT_DATE(), DATE_FORMAT(CURRENT_TIME(),'%H:%i'), '$plangitut', '$plongitut', '$plokasiabs', '$pradius_rds', '$pjarakdarilokasi', '$pketabsen')";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; mysqli_close($cnmy); exit; }
        
        $pkodeid = mysqli_insert_id($cnmy);
    
    }
    
    
    if (empty($pkodeid)) {
        mysqli_query($cnmy, "DELETE FROM hrd.t_absen WHERE karyawanid='$pcardidabsen' AND tanggal=CURRENT_DATE() AND kode_absen='$pkey' LIMIT 1");
        mysqli_close($cnmy);
        echo "GAGAL";
        exit; 
    }
    
    $pimgges        = str_replace('data:image/jpeg;base64,', '', $pimg);
    $pimgges        = str_replace(' ', '+', $pimgges);

    //resource gambar diubah dari encode
    $pdata       = base64_decode($pimgges);

    //menamai file, file dinamai secara random dengan unik
    $pfile       = "a".$pkodeid."_".uniqid() . '.png';
    
    
    if (file_exists(UPLOAD_DIR.$pfile)) {
        mysqli_close($cnmy);
        echo "nama file foto sudah ada...";
        exit; 
    }
    
    //memindahkan file ke folder upload
    file_put_contents(UPLOAD_DIR.$pfile, $pdata);
    
    if ($psudahpernahabsen_masuk==true AND $pkey=="1" AND !empty($pkodesudahinput)) {
        $query = "UPDATE dbimages2.img_absen SET nama='$pfile' WHERE idabsen='$pkodeid' AND kode_absen='$pkey' AND tanggal=CURRENT_DATE()  LIMIT 1";
	}elseif ($psudahpernahabsen_masuk==true AND $pkey=="2" AND !empty($pkodesudahinput)) {
		$query = "UPDATE dbimages2.img_absen SET nama='$pfile' WHERE idabsen='$pkodeid' AND kode_absen='2' AND tanggal=CURRENT_DATE()  LIMIT 1";
    }else{
        $query = "INSERT INTO dbimages2.img_absen(idabsen, kode_absen, tanggal, nama)VALUES"
                . "('$pkodeid', '$pkey', CURRENT_DATE(), '$pfile')";//, gambar , '$pimg'
    }
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy);
    if (!empty($erropesan)) {
        mysqli_query($cnmy, "DELETE FROM hrd.t_absen WHERE karyawanid='$pcardidabsen' AND tanggal=CURRENT_DATE() AND kode_absen='$pkey' LIMIT 1");
        mysqli_close($cnmy);
        echo $erropesan; 
        exit; 
    }
    
    
    
    $pberhasil="berhasil\n"."Status : $plokasiabs\n"."Anda berhasil $pnamaabse, Tgl : $ptglabsen, Jam : $pjamabsen";
    
	
	
    $njadwalwfo="N";
    $pdapatuangmakan=false;
    $pjamkerja=0;
    if ($pkey=="2") {
        $pjamkerja_wfo=0;
        $pjamkerja_wfh=0;

        $query ="select jam_kerja_wfo_y, jam_kerja_wfo_n from hrd.t_absen_jam_kerja WHERE IFNULL(id_status,'')='HO1'";
        $tampilw=mysqli_query($cnmy, $query);
        $roww=mysqli_fetch_array($tampilw);
        $pjamkerja_wfo=$roww['jam_kerja_wfo_y'];
        $pjamkerja_wfh=$roww['jam_kerja_wfo_n'];

        if (empty($pjamkerja_wfo)) $pjamkerja_wfo=0;
        if (empty($pjamkerja_wfh)) $pjamkerja_wfh=0;
        
        $query ="select distinct jam_kerja_wfo FROM hrd.t_absen_jam_kerja_ex WHERE id_status='HO1' "
                . " AND karyawanid IN ('', 'ALL', 'all', 'All', '$pcardidabsen') AND tanggal=CURRENT_DATE()";
        $tampilw2=mysqli_query($cnmy, $query);
        $ketemu2= mysqli_num_rows($tampilw2);
        if ((INT)$ketemu2>0) {
            $row2= mysqli_fetch_array($tampilw2);
            $pjamkerja_wfo=$row2['jam_kerja_wfo'];
            
            if (empty($pjamkerja_wfo)) $pjamkerja_wfo=0;
        }
        
        
        
        $query = "select a.karyawanId as karyawanid, a.lantai FROM dbmaster.t_karyawan_posisi as a "
                . " JOIN (select tanggal, lantai from hrd.t_absen_jadwal_wfo WHERE tanggal=CURRENT_DATE()) as b "
                . " on a.lantai=b.lantai WHERE a.karyawanid='$pcardidabsen'";
        $tampil3=mysqli_query($cnmy, $query);
        $ketemu3= mysqli_num_rows($tampil3);
        if ((INT)$ketemu3>0) {
            $row3=mysqli_fetch_array($tampil3);
            $nn_kryid=$row3['karyawanid'];
            if (!empty($nn_kryid)) {
                $njadwalwfo="Y";
            }
        }
        
        
        $query ="select jam, DATE_FORMAT(CURRENT_TIME(),'%H:%i') as jampulang, CURRENT_DATE() as tgl from hrd.t_absen WHERE kode_absen='1' AND tanggal=CURRENT_DATE() AND karyawanid='$pcardidabsen'";
        $tampil=mysqli_query($cnmy, $query);
        $row=mysqli_fetch_array($tampil);
        $pjammasukkerja=$row['jam'];
        $pjampulangkerja=$row['jampulang'];
        $ntgl_cr=$row['tgl'];
        $pliburannone="";
        
        $pselisih_jam=CariSelisihJamMenit("1", $pliburannone, $ntgl_cr, $pjammasukkerja, $pjampulangkerja, "");
        if ($pselisih_jam=="invalid") $pselisih_jam="";
        
        if ($plokasiabs=="WFO" && !empty($pselisih_jam)) {
            $pjamkerja=$pjamkerja_wfo;
            if ($njadwalwfo == "N") $pjamkerja=$pjamkerja_wfh;
            
            if ((INT)substr($pselisih_jam,0,2)>=(INT)$pjamkerja) {
                $pdapatuangmakan=true;
            }
        }
        
        //echo "Jadwal WFO";
        if ($pdapatuangmakan==true) {
            $pberhasil="berhasil\n"."Status : $plokasiabs\n"."Anda berhasil $pnamaabse, Tgl : $ptglabsen, Jam : $pjamabsen\n"."Jam Kerja (Durasi) : $pselisih_jam, Dapat Uang Makan";
        }else{
            $pberhasil="berhasil\n"."Status : $plokasiabs\n"."Anda berhasil $pnamaabse, Tgl : $ptglabsen, Jam : $pjamabsen\n"."Jam Kerja (Durasi) : $pselisih_jam, Tidak Dapat Uang Makan\n"."Minimal $pjamkerja Jam Kerja";
        }
        
        //echo $pberhasil;
    }
    //exit;//hilangkan
	
    

    mysqli_close($cnmy);

    //$pberhasil="$pmodule | $pact = $pkey : $plangitut, $plongitut";
}
echo $pberhasil; exit;
?>

