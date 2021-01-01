<?php

    session_start();
    include "../../../config/koneksimysqli.php";
    $dbname = "dbmaster";
    
    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];
    $puserid=$_SESSION['IDCARD'];
    $pkaryawanid=$puserid;
    
    $berhasil="Tidak ada data yang disimpan";
    
    if ($module=="glreportspd" AND $act=="inputdataadj") {
        $pnomorspd=$_POST['uidnospd'];
        $ptglspd=$_POST['utglspd'];
        $ptgl01 = str_replace('/', '-', $_POST['utgl']);
        $ptgl01 = date("Y-m-d", strtotime($ptgl01));
        $pjml=$_POST['ujumlah'];
        $pjumlah=str_replace(",","", $pjml);
        
        $pketerangan=$_POST['uketerangan'];
        $pketerangan=str_replace("'","", $pketerangan);
        
        $ppilihnobrdiv=$_POST['upilih'];
        $pjenis=$_POST['ujenis'];
        $pdivisi=$_POST['udivisi'];
        $pnobrdiv=$_POST['unobrdivisi'];
        
        
        $pkodeid2="1"; $psubkode2="01";
        $pbulan2=$ptgl01;
        if ($ppilihnobrdiv=="Y") {
            $sql = "SELECT tgl, kodeid, subkode, divisi, karyawanid FROM dbmaster.t_suratdana_br WHERE nodivisi='$pnobrdiv' AND stsnonaktif<>'Y'";
            $tampil= mysqli_query($cnmy, $sql);
            $row= mysqli_fetch_array($tampil);
            $pbulan2=$row['tgl'];
            $pkodeid2=$row['kodeid'];
            $psubkode2=$row['subkode'];
            $pdivisi=$row['divisi'];
            if (!empty($row['karyawanid'])) $pkaryawanid=$row['karyawanid'];
        }else{
            if (!empty($pjenis)) {
                $pkodeid2=$pjenis;
                if ($pjenis=="1") $psubkode2="01";
                if ($pjenis=="2") $psubkode2="20";
            }
        }
        
        $kodenya="";
    
        $sql=  mysqli_query($cnmy, "select MAX(idinput) as NOURUT from dbmaster.t_suratdana_br");
        $ketemu=  mysqli_num_rows($sql);
        $awal=7; $urut=1; $kodenya=""; $periode=date('Ymd');
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            $urut=$o['NOURUT']+1;
            $kodenya=$urut;
        }else{
            $kodenya=$_POST['e_id'];
        }

        $pcoa="101-02-002";
        
        //$berhasil="$puserid, $kodenya : $pnomorspd, $ptgl01, $pjumlah, ket : $pketerangan, $ppilihnobrdiv, $pjenis, $pdivisi, bln2 : $pbulan2, $pkodeid2, $psubkode2"; echo $berhasil; exit;
        
        if (!empty($kodenya)) {
            
            $query="INSERT INTO dbmaster.t_suratdana_br "
                    . "(idinput, coa4, divisi, kodeid, subkode, tgl, tglspd, "
                    . "jumlah, nomor, bulan2, nomor2, nodivisi2, divisi2, kodeid2, subkode2, "
                    . "userid, userproses, karyawanid, tgl_proses, keterangan)VALUES"
                    . "('$kodenya', '$pcoa', 'HO', '3', '50', '$ptgl01', '$ptglspd', "
                    . "'$pjumlah', '$pnomorspd', '$pbulan2', '$pnomorspd', '$pnobrdiv', '$pdivisi', '$pkodeid2', '$psubkode2', "
                    . "'$puserid', '$puserid', '$pkaryawanid', NOW(), '$pketerangan')";
            mysqli_query($cnmy, $query);
            $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            
            mysqli_close($cnmy);
            $berhasil="";
            
        }
        
    }elseif ($module=="glreportspd" AND $act=="hapus") {
        $pidinput=$_POST['uid'];
        
        $query = "UPDATE dbmaster.t_suratdana_br SET stsnonaktif='Y' WHERE idinput='$pidinput'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

        mysqli_close($cnmy);
        $berhasil="data berhasil dihapus...";
    }

    echo $berhasil;
    
?>