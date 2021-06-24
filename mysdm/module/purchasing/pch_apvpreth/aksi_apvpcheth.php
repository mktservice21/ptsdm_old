<?php
session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    
    include "../../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    
    $pnamalogin=$_SESSION['NAMALENGKAP'];
    $pidgroup=$_SESSION['GROUP'];
    
if ($module=="pchapvprbymkt" OR $module=="pchapvprbychc" OR $module=="pchapvprbyho" OR $module=="pchapvprbycoo") {
        
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
    }elseif ($pjabatanid=="01") {
        $papproveby="apvcoo";
    }else{
        if ($pidgroup=="46") {
            $papproveby="apvcoo";
        }elseif ($pidgroup=="8") {
            $papproveby="apvgsm";
        }
    }
    
    if ($module=="pchapvprbychc") {
        $papproveby="apvmgrchc";
    }elseif ($module=="pchapvprbyho") {
        $papproveby="apvatasanho";
    }elseif ($module=="pchapvprbycoo") {
        //$papproveby="apvcoo";
    }
    
    if (empty($pjabatanid) OR empty($papproveby) OR empty($karyawanapv)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    
    
    
    
    $noteapv = "tidak ada data yang diapprove";
    //$noteapv="$noidbr, $karyawanapv";
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
            }elseif ($papproveby=="apvcoo") {
                $fielduntukttd=" a.tgl_atasan4=NOW(), b.gbr_atasan4='$gbrapv', a.tgl_atasan5=NOW(), b.gbr_atasan5='$gbrapv' ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_atasan5,'')='' OR IFNULL(a.tgl_atasan5,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvmgrchc") {
                $fielduntukttd=" a.tgl_atasan4=NOW(), b.gbr_atasan4='$gbrapv' ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_atasan4,'')='' OR IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvatasanho") {
                $fielduntukttd=" a.tgl_atasan4=NOW(), b.gbr_atasan4='$gbrapv' ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_atasan4,'')='' OR IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }
            
            //echo "$noidbr - APPROVE BY : $papproveby";
            
            
            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {
                
                mysqli_query($cnmy, "update dbpurchasing.t_pr_transaksi a JOIN dbttd.t_pr_transaksi_ttd b on a.idpr=b.idpr SET $fielduntukttd WHERE "
                        . " a.idpr IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                
                if ($papproveby=="apvdm") {
                    $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan3=NOW(), atasan3='' WHERE idpr IN $noidbr AND IFNULL(atasan3,'')='' AND (IFNULL(tgl_atasan3,'')='' OR IFNULL(tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(tgl_atasan1,'')<>'' AND IFNULL(tgl_atasan1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                }elseif ($papproveby=="apvsm") {
                    
                }elseif ($papproveby=="apvgsm") {
                }elseif ($papproveby=="apvmgrchc") {
                }elseif ($papproveby=="apvatasanho") {
                    
                }elseif ($papproveby=="apvspv") {
                    $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan2=NOW(), atasan2='' WHERE idpr IN $noidbr AND IFNULL(atasan2,'')='' AND (IFNULL(tgl_atasan2,'')='' OR IFNULL(tgl_atasan2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(tgl_atasan1,'')<>'' AND IFNULL(tgl_atasan1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan3=NOW(), atasan3='' WHERE idpr IN $noidbr AND IFNULL(atasan3,'')='' AND (IFNULL(tgl_atasan3,'')='' OR IFNULL(tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(tgl_atasan2,'')<>'' AND IFNULL(tgl_atasan2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00')";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                }
                
                $noteapv = "data berhasil diapprove...";
            }
        }elseif ($act=="unapprove") {
            
            $query = "select idpr from dbpurchasing.t_pr_transaksi_po WHERE idpr IN $noidbr AND IFNULL(aktif,'')='Y'";
            $tampilc= mysqli_query($cnmy, $query);
            $ketemuc= mysqli_num_rows($tampilc);
            if ((INT)$ketemuc>0) {
                mysqli_close($cnmy); echo "Salah satu ID PR sudah diisi vendor..., tidak bisa diproses"; exit;
            }
            
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
                $fieldtglapprovenya= " (IFNULL(a.tgl_validate1,'')='' OR IFNULL(a.tgl_validate1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvspv") {
                $fielduntukttd=" a.tgl_atasan1=NULL, b.gbr_atasan1=NULL ";
                $fieldtglapprovenya= " ( (IFNULL(a.tgl_atasan2,'')='' OR IFNULL(a.tgl_atasan2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') OR IFNULL(atasan2,'')='' ) AND "
                        . " ( (IFNULL(a.tgl_atasan3,'')='' OR IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') OR IFNULL(atasan3,'')='' ) AND "
                        . " (IFNULL(a.tgl_atasan1,'')<>'' AND IFNULL(a.tgl_atasan1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvcoo") {
                $fielduntukttd=" a.tgl_atasan4=NULL, b.gbr_atasan4=NULL, a.tgl_atasan5=NULL, b.gbr_atasan5=NULL ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_validate1,'')='' OR IFNULL(a.tgl_validate1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan5,'')<>'' AND IFNULL(a.tgl_atasan5,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvmgrchc") {
                $fielduntukttd=" a.tgl_atasan4=NULL, b.gbr_atasan4=NULL ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_validate1,'')='' OR IFNULL(a.tgl_validate1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvatasanho") {
                $fielduntukttd=" a.tgl_atasan4=NULL, b.gbr_atasan4=NULL ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_validate1,'')='' OR IFNULL(a.tgl_validate1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }
            
            
            
            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {
                
                mysqli_query($cnmy, "update dbpurchasing.t_pr_transaksi a JOIN dbttd.t_pr_transaksi_ttd b on a.idpr=b.idpr SET $fielduntukttd WHERE "
                        . " ( IFNULL(a.tgl_validate1,'')='' OR IFNULL(a.tgl_validate1,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                        . " a.idpr IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                if ($papproveby=="apvdm") {
                    $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan3=NULL, atasan3=NULL WHERE ( IFNULL(tgl_validate1,'')='' OR IFNULL(tgl_validate1,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                            . " idpr IN $noidbr AND ( IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND IFNULL(atasan3,'')='' )";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                }elseif ($papproveby=="apvsm") {
                    
                }elseif ($papproveby=="apvgsm") {
                }elseif ($papproveby=="apvmgrchc") {
                }elseif ($papproveby=="apvatasanho") {
                    
                }elseif ($papproveby=="apvspv") {
                    
                    $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan3=NULL, atasan3=NULL WHERE ( IFNULL(tgl_validate1,'')='' OR IFNULL(tgl_validate1,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                            . " idpr IN $noidbr AND ( IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND IFNULL(atasan3,'')='' ) AND "
                            . " ( IFNULL(tgl_atasan2,'')<>'' AND IFNULL(tgl_atasan2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND IFNULL(atasan2,'')='' ) ";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                    $query = "UPDATE dbpurchasing.t_pr_transaksi SET tgl_atasan2=NULL, atasan2=NULL WHERE ( IFNULL(tgl_validate1,'')='' OR IFNULL(tgl_validate1,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                            . " idpr IN $noidbr AND ( IFNULL(tgl_atasan2,'')<>'' AND IFNULL(tgl_atasan2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND IFNULL(atasan2,'')='' ) AND "
                            . " ( IFNULL(tgl_atasan3,'')='' OR IFNULL(tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00' OR IFNULL(atasan3,'')='' )";
                    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                    
                }
                
                $noteapv = "data berhasil diunapprove...";
            }
            

        }elseif ($act=="reject") {
            $pkethapus=$_POST['ketrejpen'];
            
            if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);
            
            
            if (!empty($pnamalogin) AND !empty($karyawanapv)) {
                mysqli_query($cnmy, "update dbpurchasing.t_pr_transaksi set stsnonaktif='Y', "
                        . " aktivitas=CONCAT(IFNULL(aktivitas,''),'Ket Reject : $pkethapus', ', user reject : $pnamalogin') WHERE "
                        . " idpr in $noidbr AND "
                        . " ( IFNULL(tgl_validate1,'')='' OR IFNULL(tgl_validate1,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $noteapv = "data berhasil direject...";
            }
        }
        
    }
    
    
}
    
    
    mysqli_close($cnmy);
    echo $noteapv;


?>
