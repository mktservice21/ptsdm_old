<?php
session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    
    $pnamalogin=$_SESSION['NAMALENGKAP'];
    
    
if ($module=="mktapvkaskecilcab") {
        
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    $stsspv=$_POST['ket'];
    $karyawanapv=$_POST['ukaryawan'];
    
    if (empty($karyawanapv)) $karyawanapv=$_SESSION['IDCARD'];
    
    
    $tampil=mysqli_query($cnmy, "select jabatanId from hrd.karyawan where karyawanid='$karyawanapv'");
    $pr= mysqli_fetch_array($tampil);
    $pjabatanid=$pr['jabatanId'];
    if (empty($pjabatanid)) {
        $tampil=mysqli_query($cnmy, "select jabatanId from dbmaster.t_karyawan_posisi where karyawanid='$karyawanapv'");
        $pr= mysqli_fetch_array($tampil);
        $pjabatanid=$pr['jabatanId'];
    }
    
    
    
    $tampil=mysqli_query($cnmy, "select LEVELPOSISI from dbmaster.jabatan_level WHERE jabatanId='$pjabatanid'");
    $pr= mysqli_fetch_array($tampil);
    $plvlposisi=$pr['LEVELPOSISI'];
    
    $papproveby="";
    if ($pjabatanid=="18" OR $pjabatanid=="10") {
        $papproveby="apvspv";
    }elseif ($pjabatanid=="08") {
        $papproveby="apvdm";
    }elseif ($pjabatanid=="20") {
        $papproveby="apvsm";
    }elseif ($pjabatanid=="04" OR $pjabatanid=="05" OR $pjabatanid=="36") {
        $papproveby="apvgsm";
    }
    
    if (empty($pjabatanid) OR empty($papproveby) OR empty($karyawanapv)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    
    
    
    
    $noteapv = "tidak ada data yang diapprove";
    $noteapv="$noidbr, $karyawanapv";
    if (!empty($noidbr) AND !empty($karyawanapv)) {
        
        if ($act=="simpan_ttdallam") {
            $gbrapv=$_POST['uttd'];
            
            $fielduntukttd="";
            $fieldtglapprovenya="";
            if ($papproveby=="apvdm") {
                $fielduntukttd=" a.tgl_atasan2=NOW(), b.gbr_atasan2='$gbrapv' ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_atasan2,'')='' OR IFNULL(a.tgl_atasan2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan1,'')<>'' AND IFNULL(a.tgl_atasan1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvsm") {
                $fielduntukttd=" a.tgl_atasan3=NOW(), b.gbr_atasan3='$gbrapv' ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_atasan3,'')='' OR IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan2,'')<>'' AND IFNULL(a.tgl_atasan2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvgsm") {
                $fielduntukttd=" a.tgl_atasan4=NOW(), b.gbr_atasan4='$gbrapv' ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_atasan4,'')='' OR IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvspv") {
                $fielduntukttd=" a.tgl_atasan1=NOW(), b.gbr_atasan1='$gbrapv' ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_atasan1,'')='' OR IFNULL(a.tgl_atasan1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
            }
            
            //echo "$noidbr - APPROVE BY : $papproveby";
            
            
            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {
                
                mysqli_query($cnmy, "update dbmaster.t_kaskecilcabang a JOIN dbttd.t_kaskecilcabang_ttd b on a.idkascab=b.idkascab SET $fielduntukttd WHERE "
                        . " a.idkascab IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                
                if ($papproveby=="apvdm") {
                    $query = "UPDATE dbmaster.t_kaskecilcabang SET tgl_atasan3=NOW(), atasan3='' WHERE idkascab IN $noidbr AND IFNULL(atasan3,'')='' AND (IFNULL(tgl_atasan3,'')='' OR IFNULL(tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(tgl_atasan1,'')<>'' AND IFNULL(tgl_atasan1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                }elseif ($papproveby=="apvsm") {
                    
                }elseif ($papproveby=="apvgsm") {
                    
                }elseif ($papproveby=="apvspv") {
                    $query = "UPDATE dbmaster.t_kaskecilcabang SET tgl_atasan2=NOW(), atasan2='' WHERE idkascab IN $noidbr AND IFNULL(atasan2,'')='' AND (IFNULL(tgl_atasan2,'')='' OR IFNULL(tgl_atasan2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(tgl_atasan1,'')<>'' AND IFNULL(tgl_atasan1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $query = "UPDATE dbmaster.t_kaskecilcabang SET tgl_atasan3=NOW(), atasan3='' WHERE idkascab IN $noidbr AND IFNULL(atasan3,'')='' AND (IFNULL(tgl_atasan3,'')='' OR IFNULL(tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(tgl_atasan2,'')<>'' AND IFNULL(tgl_atasan2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                }
                
                $noteapv = "data berhasil diapprove...";
            }
        }elseif ($act=="unapprove") {
            
            
            $fielduntukttd="";
            $fieldtglapprovenya="";
            if ($papproveby=="apvdm") {
                $fielduntukttd=" a.tgl_atasan2=NULL, b.gbr_atasan2=NULL ";
                $fieldtglapprovenya= " ( (IFNULL(a.tgl_atasan3,'')='' OR IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') OR IFNULL(atasan3,'')='' ) AND (IFNULL(a.tgl_atasan2,'')<>'' AND IFNULL(a.tgl_atasan2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvsm") {
                $fielduntukttd=" a.tgl_atasan3=NULL, b.gbr_atasan3=NULL ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_atasan4,'')='' OR IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvgsm") {
                $fielduntukttd=" a.tgl_atasan4=NULL, b.gbr_atasan4=NULL ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_fin,'')='' OR IFNULL(a.tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvspv") {
                $fielduntukttd=" a.tgl_atasan1=NULL, b.gbr_atasan1=NULL ";
                $fieldtglapprovenya= " ( (IFNULL(a.tgl_atasan2,'')='' OR IFNULL(a.tgl_atasan2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') OR IFNULL(atasan2,'')='' ) AND "
                        . " ( (IFNULL(a.tgl_atasan3,'')='' OR IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') OR IFNULL(atasan3,'')='' ) AND "
                        . " (IFNULL(a.tgl_atasan1,'')<>'' AND IFNULL(a.tgl_atasan1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }
            
            
            
            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {
                
                mysqli_query($cnmy, "update dbmaster.t_kaskecilcabang a JOIN dbttd.t_kaskecilcabang_ttd b on a.idkascab=b.idkascab SET $fielduntukttd WHERE "
                        . " ( IFNULL(a.tgl_fin,'')='' OR IFNULL(a.tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                        . " a.idkascab IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                if ($papproveby=="apvdm") {
                    $query = "UPDATE dbmaster.t_kaskecilcabang SET tgl_atasan3=NULL, atasan3=NULL WHERE ( IFNULL(tgl_fin,'')='' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                            . " idkascab IN $noidbr AND ( IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND IFNULL(atasan3,'')='' )";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                }elseif ($papproveby=="apvsm") {
                    
                }elseif ($papproveby=="apvgsm") {
                    
                }elseif ($papproveby=="apvspv") {
                    
                    $query = "UPDATE dbmaster.t_kaskecilcabang SET tgl_atasan3=NULL, atasan3=NULL WHERE ( IFNULL(tgl_fin,'')='' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                            . " idkascab IN $noidbr AND ( IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND IFNULL(atasan3,'')='' ) AND "
                            . " ( IFNULL(tgl_atasan2,'')<>'' AND IFNULL(tgl_atasan2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND IFNULL(atasan2,'')='' ) ";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $query = "UPDATE dbmaster.t_kaskecilcabang SET tgl_atasan2=NULL, atasan2=NULL WHERE ( IFNULL(tgl_fin,'')='' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                            . " idkascab IN $noidbr AND ( IFNULL(tgl_atasan2,'')<>'' AND IFNULL(tgl_atasan2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND IFNULL(atasan2,'')='' ) AND "
                            . " ( IFNULL(tgl_atasan3,'')='' OR IFNULL(tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR IFNULL(atasan3,'')='' )";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                }
                
                $noteapv = "data berhasil diunapprove...";
            }
            

        }elseif ($act=="reject") {
            $pkethapus=$_POST['ketrejpen'];
            
            if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);
            
            
            if (!empty($pnamalogin) AND !empty($karyawanapv)) {
                mysqli_query($cnmy, "update dbmaster.t_kaskecilcabang set stsnonaktif='Y', userid='$karyawanapv', "
                        . " keterangan=CONCAT(keterangan,' Ket Reject : $pkethapus', ' user : $pnamalogin') WHERE "
                        . " idkascab in $noidbr AND "
                        . " ( IFNULL(tgl_fin,'')='' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $noteapv = "data berhasil direject...";
            }
        }
        
    }
    
    
}
    
    
    mysqli_close($cnmy);
    echo $noteapv;


?>
