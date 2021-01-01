<?php
session_start();
if ($_GET['module']=="viewkaryawanjbt") {
    include "../../config/koneksimysqli_it.php";
    $pidjabatan=$_POST['ujbt'];
    $filjbt="";
    if (!empty($pidjabatan)) $filjbt=" AND jabatanId='$pidjabatan' ";
    $no=1;
    $query = "select karyawanId, nama from hrd.karyawan WHERE IFNULL(aktif,'')<>'N' "
            . " AND karyawanId NOT IN (select distinct IFNULL(karyawanId,'') from dbmaster.t_karyawanadmin) $filjbt order by 2,1";
    $tampil= mysqli_query($cnit, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($s= mysqli_fetch_array($tampil)) {
        $pkaryawanid=$s['karyawanId'];
        $pnmkaryawan=$s['nama'];
        echo "<option value='$pkaryawanid'>$pnmkaryawan</option>";
    }
    mysqli_close($cnit);
    
}elseif ($_GET['module']=="simpan") {
    include "../../config/koneksimysqli_ms.php";
    
    $cnmy=$cnms;
    
    $apvid=$_SESSION['IDCARD'];
    
    if (empty($apvid)) {
        echo "Anda harus login ulang....";
        exit;
    }
    
    $tglaju=$_POST['utgl'];
    $ptglpengajuan= date("Y-m-d", strtotime($tglaju));
    $pperiode= date("Y-m", strtotime($tglaju));
    
    $pnoid=$_POST['unoidbr'];
    $pjmlpersen=$_POST['ujmlpersen'];
    
    $berhasil="Tidak ada data yang tersimpan...";
    
    //echo $pjmlpersen; exit;
    $datanya = array($pjmlpersen);
    if (!empty($datanya)){
        $tag = implode(',',$datanya);
        $arr_kata = explode(",",$pjmlpersen);
        $count_kata = count($arr_kata);
        $jumlah_tag = substr_count($tag, ",") + 1;
        $u=0;
        $unsel="";
        for ($x=0; $x<=$jumlah_tag; $x++){
            if (!empty($arr_kata[$u])){
                $uTag=trim($arr_kata[$u]);
                $arr_persen_kry = explode("_",$uTag);
                $arr_karyawan="";
                $arr_jbt="";
                $arr_pers="";
                
                if (isset($arr_persen_kry[0])) $arr_karyawan=$arr_persen_kry[0];
                if (isset($arr_persen_kry[1])) $arr_pers=$arr_persen_kry[1];
                if (isset($arr_persen_kry[2])) $arr_jbt=$arr_persen_kry[2];
                if (empty($arr_pers)) $arr_pers=0;
                
                if (!empty($arr_karyawan)) {
                    
                    $query = "DELETE FROM ms.t_call_incentive WHERE karyawanid='$arr_karyawan' AND DATE_FORMAT(bulan,'%Y-%m')='$pperiode'";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
                        echo "ERROR DELETE....";
                        exit;     
                    }
                    
                    
                    $query = "INSERT INTO ms.t_call_incentive (tgl_proses, bulan, karyawanid, jabatanid, jumlah, userid)values"
                            . "(CURRENT_DATE(), '$ptglpengajuan', '$arr_karyawan', '$arr_jbt', '$arr_pers', '$apvid')";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
                        echo "ERROR SAVE....";
                        exit;     
                    }
                    
                    $berhasil="berhasil";
                }
            }
            $u++;
        }
    }
    
    echo $berhasil;
    
}elseif ($_GET['module']=="hapus") { 
    $berhasil="Tidak ada data yang diunproses...";
    include "../../config/koneksimysqli_ms.php";
    $cnmy=$cnms;
    
    $apvid=$_SESSION['IDCARD'];
    
    if (empty($apvid)) {
        echo "Anda harus login ulang....";
        exit;
    }
    
    $tglaju=$_POST['utgl'];
    $ptglpengajuan= date("Y-m-d", strtotime($tglaju));
    $pperiode= date("Y-m", strtotime($tglaju));
    
    $pnoid=$_POST['unoidbr'];
    
    if (!empty($pnoid) AND !empty($pperiode)) {
        $query = "DELETE FROM ms.t_call_incentive WHERE id IN $pnoid AND DATE_FORMAT(bulan,'%Y-%m')='$pperiode'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
            echo "ERROR DELETE....";
            exit;     
        }

        $berhasil = "data berhasil diunproses";
    }
    echo $berhasil;
    
}elseif ($_GET['module']=="xxxxx") { 
}
?>