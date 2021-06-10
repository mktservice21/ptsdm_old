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
    
if ($module=="pchapvprbymkt" OR $module=="pchapvpobychc" OR $module=="pchapvpobyho" OR $module=="pchapvpobycoo" OR $module=="pchapvpobymgr") {
        
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
    
    if ($module=="pchapvpobymgr") {
        $papproveby="apvatasanmgrpurch";
    }elseif ($module=="pchapvpobycoo") {
        //$papproveby="apvcoo";
    }
    
    
    if ($papproveby=="apvatasanmgrpurch") {
        $tampil=mysqli_query($cnmy, "select karyawanid FROM dbpurchasing.t_po_apvby WHERE karyawanid='$karyawanapv'");
        $pr= mysqli_fetch_array($tampil);
        $pkryadaid=$pr['karyawanid'];
        if (empty($pkryadaid)) $papproveby="";
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
                
            }elseif ($papproveby=="apvsm") {
                
            }elseif ($papproveby=="apvgsm") {
                
            }elseif ($papproveby=="apvspv") {
                
            }elseif ($papproveby=="apvatasanmgrpurch") {
                $fielduntukttd=" a.apv_mgr='$karyawanapv', a.tgl_mgr=NOW(), b.gbr_mgr='$gbrapv' ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_mgr,'')='' OR IFNULL(a.tgl_mgr,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvcoo") {
                $fielduntukttd=" a.dir1='$karyawanapv', a.tgl_dir1=NOW(), b.gbr_dir1='$gbrapv' ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_dir1,'')='' OR IFNULL(a.tgl_dir1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_mgr,'')<>'' AND IFNULL(a.tgl_mgr,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvmgrchc") {
                
            }elseif ($papproveby=="apvatasanho") {
                
            }
            
            //echo "$noidbr - APPROVE BY : $papproveby";
            
            
            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {
                
                mysqli_query($cnmy, "update dbpurchasing.t_po_transaksi a JOIN dbttd.t_po_transaksi_ttd b on a.idpo=b.idpo SET $fielduntukttd WHERE "
                        . " a.idpo IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

                
                if ($papproveby=="apvdm") {
                    
                }elseif ($papproveby=="apvsm") {
                    
                }elseif ($papproveby=="apvgsm") {
                }elseif ($papproveby=="apvmgrchc") {
                }elseif ($papproveby=="apvatasanho") {
                    
                }elseif ($papproveby=="apvspv") {
                    
                }elseif ($papproveby=="apvatasanmgrpurch") {
                    
                    
                }
                
                $noteapv = "data berhasil diapprove...";
            }
        }elseif ($act=="unapprove") {
            
            $query = "select DISTINCT b.idpo from dbpurchasing.t_po_transaksi_terima as a "
                    . " JOIN dbpurchasing.t_po_transaksi_d as b on a.idpo_d=b.idpo_d WHERE "
                    . " IFNULL(a.stsnonaktif,'')<>'Y' AND b.idpo IN $noidbr";
            $tampilc= mysqli_query($cnmy, $query);
            $ketemuc= mysqli_num_rows($tampilc);
            if ((INT)$ketemuc>0) {
                mysqli_close($cnmy); echo "Salah satu ID PR sudah ada tanggal terima..., tidak bisa diproses"; exit;
            }
            
            $fielduntukttd="";
            $fieldtglapprovenya="";
            if ($papproveby=="apvdm") {
                
            }elseif ($papproveby=="apvsm") {
                
            }elseif ($papproveby=="apvgsm") {
                
            }elseif ($papproveby=="apvspv") {
            }elseif ($papproveby=="apvatasanmgrpurch") {
                $fielduntukttd=" a.tgl_mgr=NULL, b.gbr_mgr=NULL ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_dir1,'')='' OR IFNULL(a.tgl_dir1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_mgr,'')<>'' AND IFNULL(a.tgl_mgr,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvcoo") {
                $fielduntukttd=" a.tgl_dir1=NULL, b.gbr_dir1=NULL ";
                $fieldtglapprovenya= " (IFNULL(a.tgl_dir2,'')='' OR IFNULL(a.tgl_dir2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') AND (IFNULL(a.tgl_dir1,'')<>'' AND IFNULL(a.tgl_dir1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }elseif ($papproveby=="apvmgrchc") {
                
            }elseif ($papproveby=="apvatasanho") {
                
            }
            
            
            
            if (!empty($fielduntukttd) AND !empty($fieldtglapprovenya)) {
                
                mysqli_query($cnmy, "update dbpurchasing.t_po_transaksi a JOIN dbttd.t_po_transaksi_ttd b on a.idpo=b.idpo SET $fielduntukttd WHERE "
                        . " ( IFNULL(a.tgl_dir2,'')='' OR IFNULL(a.tgl_dir2,'0000-00-00 00:00:00')='0000-00-00 00:00:00' ) AND "
                        . " a.idpo IN $noidbr AND $fieldtglapprovenya");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
                
                if ($papproveby=="apvdm") {
                    
                }elseif ($papproveby=="apvsm") {
                    
                }elseif ($papproveby=="apvgsm") {
                }elseif ($papproveby=="apvmgrchc") {
                }elseif ($papproveby=="apvatasanho") {
                    
                }elseif ($papproveby=="apvspv") {
                }elseif ($papproveby=="apvatasanmgrpurch") {
                    
                    
                    
                }
                
                $noteapv = "data berhasil diunapprove...";
            }
            

        }elseif ($act=="reject") {
            $pkethapus=$_POST['ketrejpen'];
            
            if (!empty($pkethapus)) $pkethapus = str_replace("'", " ", $pkethapus);
            
            
            if (!empty($pnamalogin) AND !empty($karyawanapv)) {
                mysqli_query($cnmy, "update dbpurchasing.t_po_transaksi set stsnonaktif='Y', "
                        . " notes=CONCAT(IFNULL(notes,''),'Ket Reject : $pkethapus', ', user reject : $pnamalogin') WHERE "
                        . " idpo in $noidbr AND "
                        . " ( IFNULL(tgl_dir2,'')='' OR IFNULL(tgl_dir2,'0000-00-00 00:00:00')='0000-00-00 00:00:00' )");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                $noteapv = "data berhasil direject...";
            }
        }
        
    }
    
    
}
    
    
    mysqli_close($cnmy);
    echo $noteapv;


?>
