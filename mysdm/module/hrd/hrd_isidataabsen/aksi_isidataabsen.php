<?php

session_start();

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    $erropesan="error";
    $pketeksekusi="";
    $phapusinduk="";
    
    
// Hapus 
if ($pmodule=='hrdisidataabsen')
{
    if ($pact=='input' OR $pact=='update') {
        
        include "../../../config/koneksimysqli.php";
        include "../../../config/fungsi_sql.php";
        
        $puserid=$_POST['e_idinputuser'];
        $pcardid=$_POST['e_idcarduser'];

        if (empty($puserid)) {
            $puserid="";
            if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
            if (isset($_SESSION['IDCARD'])) $pcardid=$_SESSION['IDCARD'];

            if (empty($puserid)) {
                mysqli_close($cnmy);
                $pketeksekusi="ANDA HARUS LOGIN ULANG...";
                goto errorsimpan;
                exit;
            }
        }
        
        $pkodeid=$_POST['e_id'];
        $pkryid=$_POST['e_idkry'];
        $ptgl=$_POST['e_periode01'];
        $pinitgl=$_POST['e_tglini'];
        $pjam=$_POST['e_jam'];
        $pkdabs=$_POST['e_kdabsen'];
        $pket=$_POST['txt_ket'];
        $pjenisabs=$_POST['e_jenisabse'];
        
        $ptgl= date("Y-m-d", strtotime($ptgl));
        $pinitgl= date("Y-m-d", strtotime($pinitgl));
        if (!empty($pket)) $pket = str_replace("'", " ", $pket);
        
        
        if (empty($ptgl)) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, tanggal kosong...";
            goto errorsimpan;
            exit;
        }
        
        if (empty($pkryid)) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, karyawan kosong...";
            goto errorsimpan;
            exit;
        }
        
        if (empty($pkdabs)) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, kode absen kosong...";
            goto errorsimpan;
            exit;
        }
        
        if (empty($pjam)) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, jam kosong...";
            goto errorsimpan;
            exit;
        }
        
        if (empty($pjenisabs)) {
            mysqli_close($cnmy);
            $pketeksekusi="Gagal Simpan, jenis lokasi masih kosong...";
            goto errorsimpan;
            exit;
        }
        
        
        $plangitut="";
        $plongitut="";
        $pradius_rds=0;
        $pjarakdarilokasi=0;
        
        $pidstatus="HO1";
        if ($pjenisabs=="WFO") {
            $query_lk = "select sdm_latitude, sdm_longitude, sdm_radius from hrd.sdm_lokasi WHERE id_status='$pidstatus'";
        }else{
            $query_lk = "select a_latitude as sdm_latitude, a_longitude as sdm_longitude, a_radius as sdm_radius from hrd.karyawan_absen WHERE id_status='$pidstatus' AND karyawanid='$pkryid'";
        }
        $tampilc= mysqli_query($cnmy, $query_lk);
        $rowc= mysqli_fetch_array($tampilc);
        $plangitut=$rowc['sdm_latitude'];
        $plongitut=$rowc['sdm_longitude'];
        $pradius_rds=$rowc['sdm_radius'];
        
        //echo "$pkryid, $ptgl, $pjam, $pkdabs, $plangitut, $plongitut, $pjenisabs, $pket"; exit;
        
        if ($pact=='input') {
            
            $query = "select * from hrd.t_absen WHERE karyawanid='$pkryid' AND tanggal='$ptgl' AND kode_absen='$pkdabs'";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ((INT)$ketemu>0) {
                mysqli_close($cnmy);
                $pketeksekusi="Gagal Simpan, tanggal dengan kode absen yang dipilih sudah ada...";
                goto errorsimpan;
                exit;
            }
        
            $query = "INSERT INTO hrd.t_absen(kode_absen, karyawanid, tanggal, jam, l_latitude, l_longitude, l_status, l_radius, l_jarak, keterangan)VALUES"
                    . "('$pkdabs', '$pkryid', '$ptgl', '$pjam', '$plangitut', '$plongitut', '$pjenisabs', '$pradius_rds', '$pjarakdarilokasi', '$pket')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error insert ke data absen"; mysqli_close($cnmy); goto errorsimpan; }

            $pkodeid = mysqli_insert_id($cnmy);
            
            if ($pkdabs=="1" OR $pkdabs=="2") {
                $query = "INSERT INTO dbimages2.img_absen(idabsen, kode_absen, tanggal, nama)VALUES"
                        . "('$pkodeid', '$pkdabs', CURRENT_DATE(), '')";
                //mysqli_query($cnmy, $query);
                //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error insert ke data foto"; mysqli_close($cnmy); goto errorsimpan; }
            }
            
        }elseif ($pact=='update') {
            
            $query = "select * from hrd.t_absen WHERE karyawanid='$pkryid' AND tanggal='$ptgl' AND kode_absen='$pkdabs' AND idabsen<>'$pkodeid'";
            echo $query;
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ((INT)$ketemu>0) {
                mysqli_close($cnmy);
                $pketeksekusi="Gagal Simpan, tanggal dengan kode absen yang dipilih sudah ada...";
                goto errorsimpan;
                exit;
            }
            
            $query = "UPDATE hrd.t_absen SET tanggal='$ptgl', jam='$pjam', l_latitude='$plangitut', l_longitude='$plongitut', "
                    . " l_status='$pjenisabs', l_radius='$pradius_rds', l_jarak='$pjarakdarilokasi', keterangan='$pket' WHERE "
                    . " idabsen='$pkodeid' AND karyawanid='$pkryid' AND tanggal='$pinitgl' LIMIT 1";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { $pketeksekusi="error update ke data absen"; mysqli_close($cnmy); goto errorsimpan; }
        }
        
        
        mysqli_close($cnmy);
        
        
        $pketeksekusi="berhasil $pkodeid";
        header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror=berhasil&keteks='.$pketeksekusi);
        exit;
        
        
    }
}


errorsimpan:
    
    if (empty($pketeksekusi)) $pketeksekusi="error";
    //echo $pketeksekusi; exit;
    
    header('location:../../../media.php?module='.$pmodule.'&idmenu='.$pidmenu.'&nmun='.$pidmenu.'&act=complete&iderror=error&keteks='.$pketeksekusi);
    exit;
?>