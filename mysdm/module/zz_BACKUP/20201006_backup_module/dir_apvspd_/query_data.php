<?php

    include "../../config/koneksimysqli.php";

    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    
    
    $cket = $_POST['eket'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $karyawan = $_POST['ukaryawan'];
    
    $_SESSION['DIRSPDAPVKET'] = $cket;
    $_SESSION['DIRSPDAPVTGL1'] = $mytgl1;
    $_SESSION['DIRSPDAPVTGL2'] = $mytgl2;
    
    
    $tgl1= date("Y-m", strtotime($mytgl1));
    $tgl2= date("Y-m", strtotime($mytgl2));
    
    
    $sql = "SELECT idinput, DATE_FORMAT(tgl,'%M %Y') bulan, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, DATE_FORMAT(tglf,'%M %Y') as tglf,
        divisi, kodeid, nama, subkode, subnama, FORMAT(jumlah,0,'de_DE') as jumlah, 
        nomor, nodivisi, pilih, karyawanid, jenis_rpt, userproses, ifnull(tgl_proses,'0000-00-00') tgl_proses, ifnull(tgl_dir,'0000-00-00') tgl_dir,
        ifnull(tgl_dir2,'0000-00-00') tgl_dir2, ifnull(tgl_apv1,'0000-00-00') tgl_apv1, ifnull(tgl_apv2,'0000-00-00') tgl_apv2 ";
    $sql.=" FROM dbmaster.v_suratdana_br ";
    $sql.=" WHERE 1=1 ";// and IFNULL(pilih,'')='Y'
    $sql.=" AND ( (Date_format(tgl, '%Y-%m') between '$tgl1' and '$tgl2') OR (Date_format(tglspd, '%Y-%m') between '$tgl1' and '$tgl2') ) ";
    
	$sql.=" and ( IFNULL(pilih,'')='Y' OR ( IFNULL(pilih,'')='N' AND jenis_rpt IN ('V', 'C') ) )";
	
    //$sql .= " AND ifnull(tgl_proses,'')='' ";
    
    if (strtoupper($cket)!= "REJECT") $sql.=" AND IFNULL(stsnonaktif,'') <> 'Y' ";
    
    
    if ($pses_idcard=="0000001372"){//bu ira
        //$sql.=" and IFNULL(pilih,'')='Y' ";
        $sql.=" and subkode NOT IN ('22', '23', '21', '03', '05') ";
    }
    
    if (strtoupper($cket)=="APPROVE") {
        if ($pses_idcard=="0000001372"){//bu ira
            $sql.=" AND IFNULL(tgl_dir,'')<>'' AND IFNULL(tgl_dir2,'')='' ";
        }else{
            $sql.=" AND IFNULL(tgl_apv2,'')<>'' AND IFNULL(tgl_dir,'')='' ";
        }
    }elseif (strtoupper($cket)=="UNAPPROVE") {
        if ($pses_idcard=="0000001372"){//bu ira
            $sql.=" AND IFNULL(tgl_dir,'')<>'' AND IFNULL(tgl_dir2,'')<>'' ";
        }else{
            $sql.=" AND IFNULL(tgl_dir,'')<>'' ";//AND IFNULL(tgl_dir2,'')=''
        }
    }elseif (strtoupper($cket)=="REJECT") {
        $sql.=" AND IFNULL(stsnonaktif,'') = 'Y' ";
    }elseif (strtoupper($cket)=="PENDING") {
        
    }
    
    if (strtoupper($cket)== "SUDAHFIN") $sql .= " AND ifnull(tgl_proses,'')<>'' "; //sudah fin
    
    $sql.=" order by idinput";
    
?>

