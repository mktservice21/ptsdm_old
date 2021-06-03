<?php
session_start();


    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $pidgroup=$_SESSION['GROUP'];
    
    include "../../../config/koneksimysqli.php";
    
    //ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    
    $ppilihsts = strtoupper($_POST['eket']);
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $pkaryawanid = $_POST['ukaryawan'];
    $pstsapv = $_POST['uketapv'];
    
    
    $_SESSION['FINTTDBSSTS']=$ppilihsts;
    $_SESSION['FINTTDBSBLN1']=$mytgl1;
    $_SESSION['FINTTDBSBLN2']=$mytgl2;
    $_SESSION['FINTTDBSAPVBY']=$pkaryawanid;
    
    $pbulan1= date("Y-m-01", strtotime($mytgl1));
    $pbulan2= date("Y-m-t", strtotime($mytgl2));
    
    $tampil=mysqli_query($cnmy, "select jabatanId from hrd.karyawan where karyawanid='$pkaryawanid'");
    $pr= mysqli_fetch_array($tampil);
    $pjabatanid=$pr['jabatanId'];
    if (empty($pjabatanid)) {
        $tampil=mysqli_query($cnmy, "select jabatanId from dbmaster.t_karyawan_posisi where karyawanid='$pkaryawanid'");
        $pr= mysqli_fetch_array($tampil);
        $pjabatanid=$pr['jabatanId'];
    }
    
    
    
    //if (empty($pjabatanid) OR empty($papproveby) OR empty($pkaryawanid)) {
        //echo "Anda tidak berhak proses...";
        //mysqli_close($cnmy); exit;
    //}
    

    
    //echo "$pkaryawanid : $pjabatanid, $plvlposisi $pbulan1 - $pbulan2"; exit;
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpttdbyfinpl01_".$userid."_$now ";
    $tmp02 =" dbtemp.tmpttdbyfinpl02_".$userid."_$now ";
    $tmp03 =" dbtemp.tmpttdbyfinpl03_".$userid."_$now ";
    
    $filterdivisiotc=false;
    $pwewenang1=false;
    $filterwewenang_e1="";
    $filterwewenang_o1="";
    $query = "select karyawanid, subkode, divisi from dbmaster.t_kode_spd_wenang1 WHERE karyawanid='$pkaryawanid'";
    $tampil_w1= mysqli_query($cnmy, $query);
    $ketemu_w1= mysqli_num_rows($tampil_w1);
    if ((INT)$ketemu_w1>0) {
        $pwewenang1=true;
        while ($rw1= mysqli_fetch_array($tampil_w1)) {
            $wdivisi=$rw1['divisi'];
            $wsubkode=$rw1['subkode'];
            if ($wdivisi=="OTC") {
                $filterdivisiotc=true;
                $filterwewenang_o1 .="'".$wsubkode."',";
            }elseif ($wdivisi=="E_O") {
                $filterdivisiotc=true;
                $filterwewenang_o1 .="'".$wsubkode."',";
                $filterwewenang_e1 .="'".$wsubkode."',";
            }else{
                $filterwewenang_e1 .="'".$wsubkode."',";
            }
        }
    }
    
    if (!empty($filterwewenang_e1)) $filterwewenang_e1=" (".substr($filterwewenang_e1, 0, -1).") ";
    else $filterwewenang_e1="('NONE_UNK')";
    if (!empty($filterwewenang_o1)) $filterwewenang_o1=" (".substr($filterwewenang_o1, 0, -1).") ";
    else $filterwewenang_o1="('NONE_UNK')";
    
    $pwewenang2=false;
    $filterwewenang_e2="";
    $filterwewenang_o2="";
    $query = "select karyawanid, subkode, divisi from dbmaster.t_kode_spd_wenang2 WHERE karyawanid='$pkaryawanid'";
    $tampil_w2= mysqli_query($cnmy, $query);
    $ketemu_w2= mysqli_num_rows($tampil_w2);
    if ((INT)$ketemu_w2>0) {
        $pwewenang2=true;
        while ($rw2= mysqli_fetch_array($tampil_w2)) {
            $wdivisi=$rw2['divisi'];
            $wsubkode=$rw2['subkode'];
            if ($wdivisi=="OTC") {
                $filterdivisiotc=true;
                $filterwewenang_o2 .="'".$wsubkode."',";
            }elseif ($wdivisi=="E_O") {
                $filterdivisiotc=true;
                $filterwewenang_o2 .="'".$wsubkode."',";
                $filterwewenang_e2 .="'".$wsubkode."',";
            }else{
                $filterwewenang_e2 .="'".$wsubkode."',";
            }
        }
    }
    
    if (!empty($filterwewenang_e2)) $filterwewenang_e2=" (".substr($filterwewenang_e2, 0, -1).") ";
    else $filterwewenang_e2="('NONE_UNK')";
    if (!empty($filterwewenang_o2)) $filterwewenang_o2=" (".substr($filterwewenang_o2, 0, -1).") ";
    else $filterwewenang_o2="('NONE_UNK')";
    
    $pwewenang3=false;
    $filterwewenang_e3="";
    $filterwewenang_o3="";
    $query = "select karyawanid, subkode, divisi from dbmaster.t_kode_spd_wenang3 WHERE karyawanid='$pkaryawanid'";
    $tampil_w3= mysqli_query($cnmy, $query);
    $ketemu_w3= mysqli_num_rows($tampil_w3);
    if ((INT)$ketemu_w3>0) {
        $pwewenang3=true;
        while ($rw3= mysqli_fetch_array($tampil_w3)) {
            $wdivisi=$rw3['divisi'];
            $wsubkode=$rw3['subkode'];
            if ($wdivisi=="OTC") {
                $filterdivisiotc=true;
                $filterwewenang_o3 .="'".$wsubkode."',";
            }elseif ($wdivisi=="E_O") {
                $filterdivisiotc=true;
                $filterwewenang_o3 .="'".$wsubkode."',";
                $filterwewenang_e3 .="'".$wsubkode."',";
            }else{
                $filterwewenang_e3 .="'".$wsubkode."',";
            }
        }
    }
    
    if (!empty($filterwewenang_e3)) $filterwewenang_e3=" (".substr($filterwewenang_e3, 0, -1).") ";
    else $filterwewenang_e3="('NONE_UNK')";
    if (!empty($filterwewenang_o3)) $filterwewenang_o3=" (".substr($filterwewenang_o3, 0, -1).") ";
    else $filterwewenang_o3="('NONE_UNK')";
    
    
    $query_data = "select a.idinput, a.tglinput, a.tgl, a.divisi, "
            . " a.kodeid, b.nama as namakode, a.subkode, b.subnama, "
            . " a.jumlah, a.jumlah2, a.jumlah3, IFNULL(a.jumlah,0)+IFNULL(a.jumlah2,0) as jumlah_trans, "
            . " a.nomor, a.nodivisi, a.pilih, a.karyawanid, d.nama as nama_karyawan, a.jenis_rpt, "
            . " a.userproses, a.tgl_proses, a.apv1, a.apv2, a.tgl_dir, a.tgl_dir2, a.tgl_apv1, a.tgl_apv2, a.tgl_apv3 "
            . " from dbmaster.t_suratdana_br as a "
            . " LEFT JOIN dbmaster.t_kode_spd as b on "
            . " a.kodeid=b.kodeid AND a.subkode=b.subkode "
            . " LEFT JOIN hrd.karyawan as d on a.karyawanid=d.karyawanId ";
    $query_data .=" WHERE 1=1 ";
    $query_data.=" AND ( (a.tglinput between '$pbulan1' and '$pbulan2') OR (a.tgl between '$pbulan1' and '$pbulan2') ) ";
    $query_data .=" AND IFNULL(a.jenis_rpt,'') NOT IN ('W') ";
    
    
    //$query_data .=" AND a.idinput in (2700, 2696, 2761) ";
    
    
    
    if ($ppilihsts=="APVDIRFIN") {
        
        $query_qd =$query_data." AND a.karyawanid='$pkaryawanid' ";
        $query_qd .=" AND IFNULL(a.stsnonaktif,'')<>'Y' ";
        $query_qd .= " AND (IFNULL(a.tgl_apv2,'')<>'' AND IFNULL(a.tgl_apv2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
        $query = "create TEMPORARY table $tmp01 ($query_qd)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    }else{
        
        $query_q1 =$query_data." AND a.karyawanid='$pkaryawanid' ";
        $query_q1 .=" AND ( (a.subkode IN $filterwewenang_e1 AND a.divisi NOT IN ('OTC', 'CHC')) OR (a.subkode IN $filterwewenang_o1 AND a.divisi IN ('OTC', 'CHC')) ) ";
        if ($ppilihsts=="REJECT") {
            $query_q1 .=" AND IFNULL(a.stsnonaktif,'')='Y' ";
        }else{
            $query_q1 .=" AND IFNULL(a.stsnonaktif,'')<>'Y' ";
            if ($ppilihsts=="ALLDATA") {

            }else{
                if ($ppilihsts=="APPROVE") {
                    $query_q1 .= " AND (IFNULL(a.tgl_apv1,'')='' OR IFNULL(a.tgl_apv1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                }elseif ($ppilihsts=="UNAPPROVE") {
                    $query_q1 .= " AND (IFNULL(a.tgl_apv1,'')<>'' AND IFNULL(a.tgl_apv1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }
            }
        }
        //echo "<br/>$query_q1<br/>";
        $query = "create TEMPORARY table $tmp01 ($query_q1)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


        $query_q2 =$query_data." AND ( (a.subkode IN $filterwewenang_e2 AND a.divisi NOT IN ('OTC', 'CHC')) OR (a.subkode IN $filterwewenang_o2 AND a.divisi IN ('OTC', 'CHC')) ) ";
        $query_q2 .= " AND (IFNULL(a.tgl_apv1,'')<>'' AND IFNULL(a.tgl_apv1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
        if ($ppilihsts=="REJECT") {
            $query_q2 .=" AND IFNULL(a.stsnonaktif,'')='Y' ";
        }else{
            $query_q2 .=" AND IFNULL(a.stsnonaktif,'')<>'Y' ";
            if ($ppilihsts=="ALLDATA") {

            }else{
                if ($ppilihsts=="APPROVE") {
                    $query_q2 .= " AND (IFNULL(a.tgl_apv2,'')='' OR IFNULL(a.tgl_apv2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                }elseif ($ppilihsts=="UNAPPROVE") {
                    $query_q2 .= " AND (IFNULL(a.tgl_apv2,'')<>'' AND IFNULL(a.tgl_apv2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }
            }
        }

        $query = "INSERT INTO $tmp01 $query_q2"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }




        $query_q3 =$query_data." AND ( (a.subkode IN $filterwewenang_e3 AND a.divisi NOT IN ('OTC', 'CHC')) OR (a.subkode IN $filterwewenang_o3 AND a.divisi IN ('OTC', 'CHC')) ) ";
        $query_q3 .= " AND (IFNULL(a.tgl_apv2,'')<>'' AND IFNULL(a.tgl_apv2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
        if ($ppilihsts=="REJECT") {
            $query_q3 .=" AND IFNULL(a.stsnonaktif,'')='Y' ";
        }else{
            $query_q3 .=" AND IFNULL(a.stsnonaktif,'')<>'Y' ";
            if ($ppilihsts=="ALLDATA") {

            }else{
                if ($ppilihsts=="APPROVE") {
                    $query_q3 .= " AND (IFNULL(a.tgl_apv3,'')='' OR IFNULL(a.tgl_apv3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                }elseif ($ppilihsts=="UNAPPROVE") {
                    $query_q3 .= " AND (IFNULL(a.tgl_apv3,'')<>'' AND IFNULL(a.tgl_apv3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }
            }
        }

        $query = "INSERT INTO $tmp01 $query_q3"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        $query_ex =$query_data." AND IFNULL(CONCAT(IFNULL(a.subkode,''), IFNULL(a.jenis_rpt,''), IFNULL(a.karyawanid,'')),'') IN "
                . " (select IFNULL(CONCAT(IFNULL(subkode,''), IFNULL(jenis_rpt,''), IFNULL(karyawaninput,'')),'') "
                . " FROM dbmaster.t_kode_spd_exp WHERE karyawanid='$pkaryawanid' AND IFNULL(nomor_apv,0)=2) ";
        if ($ppilihsts=="REJECT") {
            $query_ex .=" AND IFNULL(a.stsnonaktif,'')='Y' ";
        }else{
            $query_ex .=" AND IFNULL(a.stsnonaktif,'')<>'Y' ";
            if ($ppilihsts=="ALLDATA") {

            }else{
                if ($ppilihsts=="APPROVE") {
                    $query_ex .= " AND (IFNULL(a.tgl_apv2,'')='' OR IFNULL(a.tgl_apv2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                }elseif ($ppilihsts=="UNAPPROVE") {
                    $query_ex .= " AND (IFNULL(a.tgl_apv2,'')<>'' AND IFNULL(a.tgl_apv2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }
            }
        }
        
        $query = "INSERT INTO $tmp01 $query_ex"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
        
        $query_ex2 =$query_data." AND IFNULL(CONCAT(IFNULL(a.subkode,''), IFNULL(a.jenis_rpt,''), IFNULL(a.karyawanid,'')),'') IN "
                . " (select IFNULL(CONCAT(IFNULL(subkode,''), IFNULL(jenis_rpt,''), IFNULL(karyawaninput,'')),'') "
                . " FROM dbmaster.t_kode_spd_exp WHERE karyawanid='$pkaryawanid' AND IFNULL(nomor_apv,0)=1) ";
        if ($ppilihsts=="REJECT") {
            $query_ex2 .=" AND IFNULL(a.stsnonaktif,'')='Y' ";
        }else{
            $query_ex2 .=" AND IFNULL(a.stsnonaktif,'')<>'Y' ";
            if ($ppilihsts=="ALLDATA") {

            }else{
                if ($ppilihsts=="APPROVE") {
                    $query_ex2 .= " AND (IFNULL(a.tgl_apv1,'')='' OR IFNULL(a.tgl_apv1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                }elseif ($ppilihsts=="UNAPPROVE") {
                    $query_ex2 .= " AND (IFNULL(a.tgl_apv1,'')<>'' AND IFNULL(a.tgl_apv1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }
            }
        }
        
        $query = "INSERT INTO $tmp01 $query_ex2"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
    }
    
    $query ="ALTER TABLE $tmp01 ADD COLUMN iurutan VARCHAR(2), ADD COLUMN nama_pengajuan VARCHAR(100), ADD COLUMN nama_report VARCHAR(100), ADD COLUMN nama_ket VARCHAR(100), ADD COLUMN link_eth VARCHAR(100), ADD COLUMN link_otc VARCHAR(100), ADD COLUMN tgl_trans DATE";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="UPDATE $tmp01 SET iurutan='ZZ' WHERE karyawanid<>'$pkaryawanid'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="UPDATE $tmp01 as a JOIN dbmaster.t_kode_spd_pengajuan as b on IFNULL(a.jenis_rpt,'')=IFNULL(b.jenis_rpt,'') AND a.subkode=b.subkode SET "
            . " a.nama_pengajuan=b.nama_pengajuan, a.nama_report=b.nama_report, a.nama_ket=b.nama_ket, a.link_eth=b.link_eth, a.link_otc=b.link_otc";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="UPDATE $tmp01 as a JOIN (select * from dbmaster.t_kode_spd_pengajuan WHERE IFNULL(jenis_rpt,'')='') as b on a.subkode=b.subkode SET "
            . " a.nama_pengajuan=b.nama_pengajuan, a.nama_report=b.nama_report, a.nama_ket=b.nama_ket, a.link_eth=b.link_eth, a.link_otc=b.link_otc WHERE "
            . " ( IFNULL(a.nama_pengajuan,'')='' OR IFNULL(a.link_eth,'')='' OR IFNULL(a.link_otc,'')='' ) ";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN (select idinput, tanggal, nobukti "
            . " from dbmaster.t_suratdana_bank WHERE stsinput='K' "
            . " AND IFNULL(stsnonaktif,'')<>'Y' and subkode NOT IN ('29') LIMIT 1) as b on a.idinput=b.idinput SET "
            . " a.tgl_trans=tanggal"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select DISTINCT * FROM $tmp01";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
echo "<div style='font-weight:bold; color:blue;'>";
if ($ppilihsts=="APPROVE") {
    echo "DATA YANG BELUM DIAPPROVE";
}elseif ($ppilihsts=="UNAPPROVE") {
    echo "DATA YANG SUDAH DIAPPROVE";
}elseif ($ppilihsts=="APVDIRFIN") {
    echo "DATA APPROVE COO";
}elseif ($ppilihsts=="REJECT") {
    echo "DATA REJECT";
}
echo "</div>";

?>



<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content' style="overflow-x:auto; max-height:500px">
        
        <table id='datatable' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='10px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='50px'>No Divisi / No. BR</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Jml. Adjustment</th>
                    <th width='50px'>Jml. Transfer</th>
                    <th width='20px'>Divisi</th>
                    <th width='50px'>Tgl. Input</th>
                    <th width='50px'>Tgl. Pengajuan</th>
                    <th width='50px'>Jenis</th>
                    <th width='50px'>Yg. Mengajukan</th>
                    <th width='50px'>Apv. Fin 1</th>
                    <th width='50px'>Apv. Fin 2</th>
                    <th width='50px'>Approved</th>
                    <th width='50px'>Apv. Dir 1</th>
                    <th width='50px'>Apv. Dir 2</th>
                    <th width='50px'>No. SPD</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $pmystsyginput="";
                $query = "select * from $tmp02 order by IFNULL(iurutan,''), nama_karyawan, idinput DESC";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    
                    $pidno=$row['idinput'];
                    $pkryid=$row['karyawanid'];
                    $pkrynm=$row['nama_karyawan'];
                    $pdivisi=$row['divisi'];
                    $pkodeid=$row['kodeid'];
                    $pkodenm=$row['namakode'];
                    $psubkode=$row['subkode'];
                    $psubnama=$row['subnama'];
                    $pntglinput=$row['tglinput'];
                    $pntgl=$row['tgl'];
                    $pnodivis=$row['nodivisi'];
                    $pnomor=$row['nomor'];
                    $pjumlah1=$row['jumlah'];
                    $pjumlah2=$row['jumlah2'];
                    $pjumlah3=$row['jumlah3'];
                    $pjumlahtrans=$row['jumlah_trans'];
                    $ppilih=$row['pilih'];

                    $puserproses=$row["userproses"];
                    $ptglproses=$row["tgl_proses"];
                    $pjenisrpt=$row["jenis_rpt"];
                    $pnmpengajuan_jenis=$row["nama_pengajuan"];
                    $ptglfin2=$row["tgl_apv2"];
                    $ptgldir1=$row["tgl_dir"];
                    
                    $plinketh=$row["link_eth"];
                    $plinkotc=$row["link_otc"];
                    
                    $papprove1=$row["apv1"];
                    
                    $ptglatasan1=$row["tgl_apv1"];
                    $ptglatasan2=$row["tgl_apv2"];
                    $ptglatasan3=$row["tgl_apv3"];
                    $ptgldir1=$row["tgl_dir"];
                    $ptgldir2=$row["tgl_dir2"];
                    $ptgltrans=$row["tgl_trans"];

                    $pidget=$row['idinput'];

                    $pnamajenis="";

                    if (empty($pnmpengajuan_jenis)) $pnmpengajuan_jenis=$psubnama;//"Advance BR";


                    if ($ptglatasan1=="0000-00-00" OR $ptglatasan1=="0000-00-00 00:00:00") $ptglatasan1="";
                    if ($ptglatasan2=="0000-00-00" OR $ptglatasan2=="0000-00-00 00:00:00") $ptglatasan2="";
                    if ($ptglatasan3=="0000-00-00" OR $ptglatasan3=="0000-00-00 00:00:00") $ptglatasan3="";
                    if ($ptgldir1=="0000-00-00" OR $ptgldir1=="0000-00-00 00:00:00") $ptgldir1="";
                    if ($ptgldir2=="0000-00-00" OR $ptgldir2=="0000-00-00 00:00:00") $ptgldir2="";
                    if ($ptglproses=="0000-00-00" OR $ptglproses=="0000-00-00 00:00:00") $ptglproses="";
                    if ($ptgltrans=="0000-00-00" OR $ptgltrans=="0000-00-00 00:00:00") $ptgltrans="";
                    
                    $pnmdivisi=$pdivisi;
                    if ($pdivisi=="EAGLE") $pnmdivisi="EAGLE";
                    if ($pdivisi=="PEACO") $pnmdivisi="PEACOCK";
                    if ($pdivisi=="PIGEO") $pnmdivisi="PIGEON";
                    if ($pdivisi=="CAN") $pnmdivisi="CANARY";
                    if ($pdivisi=="ETH") $pnmdivisi="ETHICAL/CAN";
                    if (empty($pnmdivisi)) $pnmdivisi="CANARY/ETHICAL";
                    
                    $pntglinput = date('d/m/Y', strtotime($pntglinput));
                    $pntgl = date('d/m/Y', strtotime($pntgl));
                    $pjumlah1=number_format($pjumlah1,0,",",",");
                    $pjumlah2=number_format($pjumlah2,0,",",",");
                    $pjumlah3=number_format($pjumlah3,0,",",",");
                    $pjumlahtrans=number_format($pjumlahtrans,0,",",",");
                    
                    $pjumlah=$pjumlah1;
                    
                    
                    $plink=$plinketh;
                    if ($pdivisi=="OTC" OR $pdivisi=="CHC") $plink=$plinkotc;
                    
                    $pbulanrpt=$pntgl;
                    $plinknodivisi=$pnodivis;
                    if (!empty($plink)) {
                        $plinknodivisi = "<a class='btn btn-info btn-xs' "
                                . " href='eksekusi3.php?$plink&ket=bukan&ispd=$pidget&iid=$pmystsyginput&bln=$pbulanrpt' "
                                . " target='_blank'>$pnodivis</a>";
                    }
                    
                    $ceklisnya = "<input type='checkbox' value='$pidno' name='chkbox_br[]' id='chkbox_br[$pidno]' class='cekbr'>";
                    
                    
                    if ($ppilihsts=="APVDIRFIN") {
                        if (!empty($ptgldir1)) {
                            $ceklisnya="<a href='#' class='btn btn-danger btn-xs' data-toggle='modal' "
                                    . "onClick=\"ProsesUnApproveDirByFin('unapprovedirbyfin', '$pidno')\"> "
                                    . "unapprove</a>";
                        }
                        
                        if (!empty($ptgldir2)) {
                            $ceklisnya="";
                        }
                    }else{
                        
                        if ($pwewenang1 == true AND $pwewenang2 == true) {
                        }else{
                            if ($pwewenang1 == true) {
                                if (!empty($ptglatasan2)) {
                                    $ceklisnya="";
                                }
                            }
                        }

                        if (!empty($ptglatasan3) OR !empty($ptgldir1) OR !empty($ptgldir1)) {
                            $ceklisnya="";
                        }
                        
                    }
                    
                    if (!empty($pnomor)) {
                        $ceklisnya="";
                    }
                    
                    if (!empty($ptgltrans)) {
                        $ceklisnya="";
                    }
                    
                    if (!empty($ptglatasan1) AND $pkryid==$papprove1 AND $ppilihsts=="APPROVE") {
                        //$ceklisnya="";
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$ceklisnya</td>";
                    echo "<td nowrap>$plinknodivisi</td>";
                    echo "<td nowrap>$pjumlah</td>";
                    echo "<td nowrap>$pjumlah2</td>";
                    echo "<td nowrap>$pjumlahtrans</td>";
                    echo "<td nowrap>$pnmdivisi</td>";
                    echo "<td nowrap>$pntglinput</td>";
                    echo "<td nowrap>$pntgl</td>";
                    echo "<td nowrap>$pnmpengajuan_jenis</td>";
                    echo "<td nowrap>$pkrynm</td>";
                    echo "<td nowrap>$ptglatasan1</td>";
                    echo "<td nowrap>$ptglatasan2</td>";
                    echo "<td nowrap>$ptglatasan3</td>";
                    echo "<td nowrap>$ptgldir1</td>";
                    echo "<td nowrap>$ptgldir2</td>";
                    echo "<td nowrap>$pnomor</td>";
                    echo "</tr>";
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>
        
    </div>
    
    
    <br/>&nbsp;<br/>&nbsp;
    <?PHP
        if ($ppilihsts=="UNAPPROVE") {
            echo "<div class='clearfix'></div>";
            echo "<div class='well' style=\"margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;\">";
                echo "<input class='btn btn-success' type='button' name='buttonapv' value='UnApprove' "
                    . " onClick=\"ProsesDataUnApprove('unapprove', 'chkbox_br[]')\">";
            echo "</div>";
        }else{
            if ($ppilihsts=="APPROVE" OR $ppilihsts=="APVDIRFIN") {
                echo "<div class='col-sm-5'>";
                if ($ppilihsts=="APVDIRFIN") {
                    include "ttd_aprovedir.php";
                }else{
                    include "ttd_aprovefin.php";
                }
                echo "</div>";
            }
        }
    ?>
    
    
</form>

<script>
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);
        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
    }
    
    
    function ProsesDataUnApprove(ket, cekbr){
        //alert(ket+", "+cekbr);
        var cmt = confirm('Apakah akan melakukan proses unapprove ...?');
        if (cmt == false) {
            return false;
        }
        var allnobr = "";
        
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        
        
        var txt;
        var ekaryawan=document.getElementById('cb_karyawan').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/budget/mod_fin_ttdbyspd/aksi_ttdbyspd.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=unapprove"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                pilihData('unapprove');
                alert(data);
            }
        });
        
        
    }
    
    function ProsesUnApproveDirByFin(ket, cekbr){
        //alert(ket+", "+cekbr);
        var cmt = confirm('Apakah akan melakukan proses unapprove ...?');
        if (cmt == false) {
            return false;
        }
        var allnobr = "";
        
        if (cekbr=="") {
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        allnobr="('"+cekbr+"')";
        
        var txt;
        var ekaryawan=document.getElementById('cb_karyawan').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/budget/mod_fin_ttdbyspd/aksi_ttdbyspd.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=unapprovedirbyfin"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                pilihData('apvdirfin');
                alert(data);
            }
        });
        
        
    }
    
    function ProsesDataReject(ket, cekbr){
        //alert(ket+", "+cekbr);
        var cmt = confirm('Apakah akan melakukan proses reject data ...?');
        if (cmt == false) {
            return false;
        }
        var allnobr = "";
        
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        
        
        
        var txt;
        if (ket=="reject" || ket=="hapus" || ket=="pending") {
            var textket = prompt("Masukan alasan "+ket+" : ", "");
            if (textket == null || textket == "") {
                txt = textket;
            } else {
                txt = textket;
            }
            if (txt=="") {
                alert("alasan harus diisi...");
                return false;
            }
        }
        
        var ekaryawan=document.getElementById('cb_karyawan').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/budget/mod_fin_ttdbyspd/aksi_ttdbyspd.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=unapprove"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                pilihData('approve');
                alert(data);
            }
        });
        
        
    }
    
    
</script>


<style>
    .divnone {
        display: none;
    }
    #datatable th {
        font-size: 13px;
    }
    #datatable td { 
        font-size: 11px;
    }
</style>

<style>

table {
    text-align: left;
    position: relative;
    border-collapse: collapse;
    background-color:#FFFFFF;
}

th {
    background: white;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index:1;
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp03");
    
    mysqli_close($cnmy);
?>