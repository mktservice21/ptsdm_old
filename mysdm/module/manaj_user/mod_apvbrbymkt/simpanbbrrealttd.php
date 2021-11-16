<?php
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    session_start();

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $pidcard=$_SESSION['IDCARD'];
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    
    $berhasil="tidak ada data yang disimpan...";
    
    //$berhasil = "$pmodule, $pact, $pidmenu";
    if ($pmodule=="approvebrquestbymkt" AND $pact=="simpanbrrealapvbymkt") {
        
        $piddokt=$_POST['uiduser'];
        $pkaryawanapproveid=$_POST['uidkryapv'];
        $pkaryawanapprovejbt=$_POST['uidjbtapv'];
        $pdataimage=$_POST['udataimage'];
        
        if (!empty($piddokt) AND !empty($pkaryawanapproveid) AND !empty($pkaryawanapprovejbt) AND !empty($pdataimage)) {
            include "../../../config/koneksimysqli.php";
            
            $pnobrid=$_POST['ubrid'];
            $pveriuser=$_POST['uveriuser'];
            $pverinorek=$_POST['unorekuser'];
            $ptanggungjawab=$_POST['utanggungjwb'];
            
            unset($pinsert_data);//kosongkan array
            unset($pinsert_data_img);//kosongkan array
            $pinsertdata=false;
            $nidbr_=explode(',', $pnobrid);
            $filter_br="";
            foreach($nidbr_ as $rowbrid) {
                if (!empty($rowbrid)) {
                    $query = "select brid from hrd.br0_apvreal WHERE brid='$rowbrid'";
                    $tampil=mysqli_query($cnmy, $query);
                    $ketemu= mysqli_num_rows($tampil);
                    if ((INT)$ketemu<=0) {
                        $pinsert_data[] = "('$rowbrid', '$pkaryawanapproveid', NOW())";
                        $pinsert_data_img[] = "('$rowbrid', '$pdataimage')";
                        $pinsertdata=true;
                    }else{
                        $filter_br .="'".$rowbrid."',";
                    }
                }   
            }
            if (!empty($filter_br)) $filter_br="(".substr($filter_br, 0, -1).")";
            
            if ($pinsertdata == true) {
                if ($pkaryawanapprovejbt=="08") {
                    $query_ins = "INSERT INTO hrd.br0_apvreal (brid, atasan2, tgl_atasan2) VALUES ".implode(', ', $pinsert_data);
                    $query_img = "INSERT INTO dbttd.t_br0_ttd_apvreal (brid, gbr_atasan2) VALUES ".implode(', ', $pinsert_data_img);
                }elseif ($pkaryawanapprovejbt=="10" OR $pkaryawanapprovejbt=="18") {
                    $query_ins = "INSERT INTO hrd.br0_apvreal (brid, atasan1, tgl_atasan1) VALUES ".implode(', ', $pinsert_data);
                    $query_img = "INSERT INTO dbttd.t_br0_ttd_apvreal (brid, gbr_atasan1) VALUES ".implode(', ', $pinsert_data_img);
                }elseif ($pkaryawanapprovejbt=="20") {
                    $query_ins = "INSERT INTO hrd.br0_apvreal (brid, atasan3, tgl_atasan3) VALUES ".implode(', ', $pinsert_data);
                    $query_img = "INSERT INTO dbttd.t_br0_ttd_apvreal (brid, gbr_atasan3) VALUES ".implode(', ', $pinsert_data_img);
                }elseif ($pkaryawanapprovejbt=="05") {
                    $query_ins = "INSERT INTO hrd.br0_apvreal (brid, atasan4, tgl_atasan4) VALUES ".implode(', ', $pinsert_data);
                    $query_img = "INSERT INTO dbttd.t_br0_ttd_apvreal (brid, gbr_atasan4) VALUES ".implode(', ', $pinsert_data_img);
                }
                if (!empty($query_ins)) {
                    mysqli_query($cnmy, $query_img); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "error insert ttd"; mysqli_close($cnmy); exit; }
                    mysqli_query($cnmy, $query_ins); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "error insert data"; mysqli_close($cnmy); exit; }
                }
            }
            
            if (!empty($filter_br)) {
                
                $query_updt="";
                if ($pkaryawanapprovejbt=="08") {
                    $query_updt = "UPDATE hrd.br0_apvreal as a JOIN dbttd.t_br0_ttd_apvreal as b on a.brid=b.brid SET "
                            . " a.atasan2='$pkaryawanapproveid', a.tgl_atasan2=NOW(), b.gbr_atasan2='$pdataimage' WHERE a.brid IN $filter_br AND "
                            . " IFNULL(a.tgl_atasan2,'') IN ('', '0000-00-00 00:00:00') AND "
                            . " IFNULL(a.tgl_atasan3,'') IN ('', '0000-00-00 00:00:00')";
                }elseif ($pkaryawanapprovejbt=="10" OR $pkaryawanapprovejbt=="18") {
                    $query_updt = "UPDATE hrd.br0_apvreal as a JOIN dbttd.t_br0_ttd_apvreal as b on a.brid=b.brid SET "
                            . " a.atasan1='$pkaryawanapproveid', a.tgl_atasan1=NOW(), b.gbr_atasan1='$pdataimage' WHERE a.brid IN $filter_br AND "
                            . " IFNULL(a.tgl_atasan1,'') IN ('', '0000-00-00 00:00:00') AND "
                            . " IFNULL(a.tgl_atasan2,'') IN ('', '0000-00-00 00:00:00') AND "
                            . " IFNULL(a.tgl_atasan3,'') IN ('', '0000-00-00 00:00:00')";
                }elseif ($pkaryawanapprovejbt=="20") {
                    $query_updt = "UPDATE hrd.br0_apvreal as a JOIN dbttd.t_br0_ttd_apvreal as b on a.brid=b.brid SET "
                            . " a.atasan3='$pkaryawanapproveid', a.tgl_atasan3=NOW(), b.gbr_atasan3='$pdataimage' WHERE a.brid IN $filter_br AND "
                            . " IFNULL(a.tgl_atasan3,'') IN ('', '0000-00-00 00:00:00') AND "
                            . " IFNULL(a.tgl_atasan4,'') IN ('', '0000-00-00 00:00:00')";
                }elseif ($pkaryawanapprovejbt=="05") {
                    $query_updt = "UPDATE hrd.br0_apvreal as a JOIN dbttd.t_br0_ttd_apvreal as b on a.brid=b.brid SET "
                            . " a.atasan4='$pkaryawanapproveid', a.tgl_atasan4=NOW(), b.gbr_atasan4='$pdataimage' WHERE a.brid IN $filter_br AND "
                            . " IFNULL(a.tgl_atasan3,'') NOT IN ('', '0000-00-00 00:00:00') AND "
                            . " IFNULL(a.tgl_atasan4,'') IN ('', '0000-00-00 00:00:00')";
                }

                if (!empty($query_updt)) {
                    mysqli_query($cnmy, $query_updt); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "error update data"; mysqli_close($cnmy); exit; }
                }
            
            }
            
            
            
            mysqli_close($cnmy);
            
            //$berhasil="$piddokt, $pnobrid<br/>$pdataimage";
            $berhasil="berhasil";
            
        }else{
            $berhasil="ID KOSONG...";
        }
            
    }
    
    echo $berhasil;
    
?>