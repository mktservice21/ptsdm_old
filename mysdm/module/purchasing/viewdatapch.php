<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridatakaryawan") {
    
    $idajukan=$_POST['ukry'];
    
    include "../../config/koneksimysqli.php";
    
    

    $query = "SELECT karyawanId as karyawanid, nama nama_karyawan, jabatanId as jabatanid, divisiId as divisiid FROM hrd.karyawan WHERE karyawanid='$idajukan'";
    $ptampil= mysqli_query($cnmy, $query);
    $nrs= mysqli_fetch_array($ptampil);    
    $pidjbtpl=$nrs['jabatanid'];
    $ndivisikry=$nrs['divisiid'];


//CABANG    
$pcabangid="";
$pfilcabang="";
$query_cabang="";
if ($pidjbtpl=="15") {
    $query_cabang = "select distinct icabangid as icabangid, aktif as aktif FROM mkt.imr0 WHERE karyawanid='$idajukan' AND IFNULL(icabangid,'')<>''";
}elseif ($pidjbtpl=="10" OR $pidjbtpl=="18") {
    $query_cabang = "select distinct icabangid as icabangid, aktif as aktif FROM mkt.ispv0 WHERE karyawanid='$idajukan' AND IFNULL(icabangid,'')<>''";
}elseif ($pidjbtpl=="08") {
    $query_cabang = "select distinct icabangid as icabangid, 'Y' as aktif FROM mkt.idm0 WHERE karyawanid='$idajukan' AND IFNULL(icabangid,'')<>''";
}elseif ($pidjbtpl=="20") {
    $query_cabang = "select distinct icabangid as icabangid, 'Y' as aktif FROM mkt.ism0 WHERE karyawanid='$idajukan' AND IFNULL(icabangid,'')<>''";
}elseif ($pidjbtpl=="05") {
    if ($idajukan=="0000000158") {
        $pcabangid="0000000001";
        $query_cabang = "select distinct icabangid as icabangid, 'Y' as aktif FROM mkt.icabang WHERE region='B' AND IFNULL(icabangid,'')<>'' AND IFNULL(aktif,'')<>'N'";
    }elseif ($idajukan=="0000000159") {
        $pcabangid="0000000114";
        $query_cabang = "select distinct icabangid as icabangid, 'Y' as aktif FROM mkt.icabang WHERE region='T' AND IFNULL(icabangid,'')<>'' AND IFNULL(aktif,'')<>'N'";
    }
}elseif ($pidjbtpl=="38") {
    $query_cabang = "select distinct icabangid as icabangid, 'Y' as aktif from hrd.rsm_auth where karyawanid='$idajukan'";
}

if (!empty($query_cabang)) {
    $ptampilc= mysqli_query($cnmy, $query_cabang);
    $ketemuc= mysqli_num_rows($ptampilc);
    while ($nrc= mysqli_fetch_array($ptampilc)) {
        $cidcab=$nrc['icabangid'];
        $caktif=$nrc['aktif'];
        
        if ((INT)$ketemuc==1) {
            $pcabangid=$cidcab;
            $pfilcabang .="'".$cidcab."',";
        }else{
            if ($caktif=="N") {
            }else{
                $pfilcabang .="'".$cidcab."',";
            }
        }
    }
    if (!empty($pfilcabang)) $pfilcabang="(".substr($pfilcabang, 0, -1).")";
    
}
//END CABANG



//CARI DIVISI
$pdivisiid="";
$pfildivisi="";
$query_divisi="";
if ($pidjbtpl=="15") {
    $query_divisi = "select distinct divisiid as divisiid FROM mkt.imr0 WHERE karyawanid='$idajukan' AND IFNULL(divisiid,'')<>'' AND IFNULL(aktif,'')<>'N'";
}elseif ($pidjbtpl=="10" OR $pidjbtpl=="18") {
    $query_divisi = "select distinct divisiid as divisiid FROM mkt.ispv0 WHERE karyawanid='$idajukan' AND IFNULL(divisiid,'')<>'' AND IFNULL(aktif,'')<>'N'";
}

if (!empty($query_divisi)) {
    $ptampild= mysqli_query($cnmy, $query_divisi);
    $ketemud= mysqli_num_rows($ptampild);
    while ($nrd= mysqli_fetch_array($ptampild)) {
        $diddiv=$nrd['divisiid'];
        
        $pfildivisi .="'".$diddiv."',";
        
        if ((INT)$ketemud==1) {
            $pdivisiid=$diddiv;
        }else{
            if ($pidjbtpl=="15") {
                $pdivisiid="CAN";
                $pfildivisi .="'CAN',";
            }
        }
    }
    if (!empty($pfildivisi)) $pfildivisi="(".substr($pfildivisi, 0, -1).")";
    
}

if ($pidjbtpl=="10" OR $pidjbtpl=="18" OR $pidjbtpl=="08" OR $pidjbtpl=="20" OR $pidjbtpl=="05") {
    $pdivisiid="CAN";
}else{
    if ($pidjbtpl=="38") $pdivisiid="HO";//ADMIN CABANG
    else{
        if ($pidjbtpl<>"15") {
            $pdivisiid=$ndivisikry;
            if (empty($pfilcabang) AND empty($pcabangid)) $pcabangid="0000000001";
        }
    }
}
//END CARI DIVISI

//CARI DEPARTEMEN
$pdepartmen="";
if ( ($pidjbtpl=="15" OR $pidjbtpl=="10" OR $pidjbtpl=="18" OR $pidjbtpl=="08" OR $pidjbtpl=="20" OR $pidjbtpl=="05" OR $pidjbtpl=="38") ) {
    $pdepartmen="MKT";
}else{
    
}

//END CARI DEPARTEMEN


//CARI AREA
$pareaid="";
$pfilarea="";
$query_area="";
if (!empty($pcabangid)) {
    if ($pidjbtpl=="15") {
        $query_area = "select distinct areaid as areaid, aktif FROM mkt.imr0 WHERE karyawanid='$idajukan' and icabangid='$pcabangid' AND IFNULL(areaid,'')<>'' AND IFNULL(aktif,'')<>'N'";
    }elseif ($pidjbtpl=="10" OR $pidjbtpl=="18") {
        $query_area = "select distinct areaid as areaid, aktif FROM mkt.ispv0 WHERE karyawanid='$idajukan' and icabangid='$pcabangid' AND IFNULL(areaid,'')<>'' AND IFNULL(aktif,'')<>'N'";
    }else{
        $query_area = "select distinct areaid as areaid, aktif FROM mkt.iarea WHERE icabangid='$pcabangid' AND IFNULL(areaid,'')<>'' AND IFNULL(aktif,'')<>'N'";
    }
    
    if (!empty($query_area)) {
        $ptampila= mysqli_query($cnmy, $query_area);
        $ketemua= mysqli_num_rows($ptampila);
        while ($nra= mysqli_fetch_array($ptampila)) {
            $aidarea=$nra['areaid'];
            $aaktif=$nra['aktif'];

            if ((INT)$ketemua==1) {
                $pareaid=$aidarea;
                $pfilarea .="'".$aidarea."',";
            }else{
                if ($aaktif=="N") {
                }else{
                    $pfilarea .="'".$aidarea."',";
                }
            }
        }
        if (!empty($pfilarea)) $pfilarea="(".substr($pfilarea, 0, -1).")";

    }
    
}

//END CARI AREA
    
?>
    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
        <div class='col-md-4'>
            <input type='text' id='e_jabatanid' name='e_jabatanid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidjbtpl; ?>' Readonly>
        </div>
    </div>

    <div  class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
        <div class='col-xs-5'>
            <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="">
                <?PHP

                if (!empty($pfildivisi) OR !empty($pdivisiid) AND ($pidjbtpl=="15")) {
                    if (empty($pfildivisi)) $pfildivisi="('')";

                    $query = "select DivProdId as divprodid from mkt.divprod where ( IFNULL(aktif,'')='Y' AND IFNULL(br,'')='Y' "
                            . " AND DivProdId In $pfildivisi ) OR ( DivProdId='$pdivisiid' AND IFNULL(DivProdId,'')<>'' ) ";
                    $query .=" Order by DivProdId";
                }else{
                    $query = "select DivProdId as divprodid from mkt.divprod WHERE 1=1 ";
                    if ( ($pidjbtpl=="10" OR $pidjbtpl=="10" OR $pidjbtpl=="18" OR $pidjbtpl=="08" OR $pidjbtpl=="20" OR $pidjbtpl=="05") ) {
                        $query .=" AND DivProdId IN ('CAN', 'EAGLE', 'PEACO', 'PIGEO')";
                    }else{
                        $query .=" AND ( IFNULL(aktif,'')='Y' AND IFNULL(br,'')='Y' ) OR ( DivProdId='$pdivisiid' AND IFNULL(DivProdId,'')<>'' )";
                        $query .=" AND DivProdId IN ('OTHER', 'OTHERS')";
                    }
                    $query .=" Order by DivProdId";
                }
                $tampil = mysqli_query($cnmy, $query);
                while ($z= mysqli_fetch_array($tampil)) {
                    $piddiv=$z['divprodid'];
                    $pnmdiv=$piddiv;
                    if ($piddiv=="CAN") $pnmdiv="CANARY";
                    elseif ($piddiv=="PEACO") $pnmdiv="PEACOCK";
                    elseif ($piddiv=="PIGEO") $pnmdiv="PIGEON";

                    if ($piddiv==$pdivisiid)
                        echo "<option value='$piddiv' selected>$pnmdiv</option>";
                    else
                        echo "<option value='$piddiv'>$pnmdiv</option>";
                }
                ?>
            </select>

        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
        <div class='col-xs-5'>
            <select class='form-control input-sm' id='cb_cabang' name='cb_cabang' onchange="">
                <option value='' selected>-- Pilihan --</option>
                <?PHP
                if (!empty($pfilcabang) OR !empty($pcabangid) AND ($pidjbtpl=="10" OR $pidjbtpl=="18" OR $pidjbtpl=="08" OR $pidjbtpl=="20" OR $pidjbtpl=="05")) {
                    if (empty($pfilcabang)) $pfilcabang="('')";

                    $query = "select iCabangId as icabangid, nama as nama_cabang from mkt.icabang WHERE 1=1 "
                            . " AND ( iCabangId In $pfilcabang AND IFNULL(aktif,'')<>'N' ) OR iCabangId='$pcabangid' ";
                    $query .=" Order by nama";
                }else{
                    $query = "select iCabangId as icabangid, nama as nama_cabang from mkt.icabang WHERE 1=1 "
                            . " AND IFNULL(aktif,'')<>'N' OR iCabangId='$pcabangid' ";
                    $query .=" Order by nama";
                }
                $tampil = mysqli_query($cnmy, $query);
                while ($z= mysqli_fetch_array($tampil)) {
                    $pidcab=$z['icabangid'];
                    $pnmcab=$z['nama_cabang'];
                    if ($pidcab==$pcabangid)
                        echo "<option value='$pidcab' selected>$pnmcab</option>";
                    else
                        echo "<option value='$pidcab'>$pnmcab</option>";
                }
                ?>
            </select>

        </div>
    </div>

    <div hidden class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
        <div class='col-xs-5'>
            <select class='form-control input-sm' id='cb_area' name='cb_area' onchange="">
                <option value='' selected>-- Pilihan --</option>
                <?PHP
                if (!empty($pcabangid) AND !empty($pfilarea)) {//$pareaid
                    $query = "select areaid as areaid, nama as nama_area FROM mkt.iarea WHERE icabangid='$pcabangid' "
                            . " AND IFNULL(areaid,'') IN $pfilarea AND IFNULL(aktif,'')<>'N' ";
                    $query .=" ORDER BY nama, areaid";
                    $tampil = mysqli_query($cnmy, $query);
                    while ($z= mysqli_fetch_array($tampil)) {
                        $pidarea=$z['areaid'];
                        $pnmarea=$z['nama_area'];
                        if ($pidarea==$pareaid)
                            echo "<option value='$pidarea' selected>$pnmarea</option>";
                        else
                            echo "<option value='$pidarea'>$pnmarea</option>";
                    }
                }
                ?>
            </select>

        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Departemen <span class='required'></span></label>
        <div class='col-xs-5'>
            <select class='form-control input-sm' id='cb_dept' name='cb_dept' onchange="">
                <?PHP
                if (!empty($pdepartmen)) {
                    $query = "select iddep as iddep, nama_dep as nama_dep from dbmaster.t_department WHERE 1=1 "
                            . " AND ( iddep='$pdepartmen' AND IFNULL(aktif,'')<>'N' ) OR iddep='$pdepartmen' ";
                    $query .=" Order by nama_dep";
                }else{
                    echo "<option value='' selected>-- Pilihan --</option>";
                    $query = "select iddep as iddep, nama_dep as nama_dep from dbmaster.t_department WHERE 1=1 "
                            . " AND IFNULL(aktif,'')<>'N' OR iddep='$pdepartmen' ";
                    $query .=" Order by nama_dep";
                }
                $tampil = mysqli_query($cnmy, $query);
                while ($z= mysqli_fetch_array($tampil)) {
                    $piddep=$z['iddep'];
                    $pnmdep=$z['nama_dep'];
                    if ($piddep==$pdepartmen)
                        echo "<option value='$piddep' selected>$pnmdep</option>";
                    else
                        echo "<option value='$piddep'>$pnmdep</option>";
                }
                ?>
            </select>

        </div>
    </div>
<?PHP
    mysqli_close($cnmy); exit;
}elseif ($pmodule=="caridataatasan") {
    
    $idajukan=$_POST['ukry'];
    
    include "../../config/koneksimysqli.php";
    
    
//ATASAN

    $query ="SELECT a.karyawanid, b.nama nama_karyawan, a.spv, c.nama nama_spv, 
        a.dm, d.nama nama_dm, a.sm, e.nama nama_sm, a.gsm, f.nama nama_gsm, 
        g.icabangid as icabangid, g.areaid as areaid, g.jabatanid as jabatanid, g.divisiId as divisiid 
        FROM dbmaster.t_karyawan_posisi a 
        LEFT JOIN hrd.karyawan as g on a.karyawanId=g.karyawanId 
        LEFT JOIN hrd.karyawan b on a.karyawanId=b.karyawanId 
        LEFT JOIN hrd.karyawan c on a.spv=c.karyawanId 
        LEFT JOIN hrd.karyawan d on a.dm=d.karyawanId 
        LEFT JOIN hrd.karyawan e on a.sm=e.karyawanId 
        LEFT JOIN hrd.karyawan f on a.gsm=f.karyawanId WHERE a.karyawanid='$idajukan'";
    $ptampil= mysqli_query($cnmy, $query);
    $nrs= mysqli_fetch_array($ptampil);
    $pkdspv=$nrs['spv'];
    $pnamaspv=$nrs['nama_spv'];
    $pkddm=$nrs['dm'];
    $pnamadm=$nrs['nama_dm'];
    $pkdsm=$nrs['sm'];
    $pnamasm=$nrs['nama_sm'];
    $pkdgsm=$nrs['gsm'];
    $pnamagsm=$nrs['nama_gsm'];
    
    $pidjbtpl=$nrs['jabatanid'];
    $ndivisikry=$nrs['divisiid'];

// END ATASAN
    
?>
    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
        <div class='col-xs-3'>
            <input type='hidden' id='e_kdspv' name='e_kdspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdspv; ?>'>
            <input type='text' id='e_namaspv' name='e_namaspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamaspv; ?>'>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM <span class='required'></span></label>
        <div class='col-xs-3'>
            <input type='hidden' id='e_kddm' name='e_kddm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkddm; ?>'>
            <input type='text' id='e_namadm' name='e_namadm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadm; ?>'>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
        <div class='col-xs-3'>
            <input type='hidden' id='e_kdsm' name='e_kdsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdsm; ?>'>
            <input type='text' id='e_namasm' name='e_namasm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamasm; ?>'>
        </div>
    </div>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>GSM <span class='required'></span></label>
        <div class='col-xs-3'>
            <input type='hidden' id='e_kdgsm' name='e_kdgsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdgsm; ?>'>
            <input type='text' id='e_namagsm' name='e_namagsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamagsm; ?>'>
        </div>
    </div>
<?PHP
    mysqli_close($cnmy); exit;
}elseif ($pmodule=="caridataarea") {
    $idajukan=$_POST['ukry'];
    $pidjbt=$_POST['ujbt'];
    $pcabangid=$_POST['ucab'];
    $pareaid="";
    
    include "../../config/koneksimysqli.php";
    $query_area="";
    if ($pidjbtpl=="15") {
        $query_area = "select distinct a.areaid as areaid, b.nama as nama_area, a.aktif FROM mkt.imr0 as a "
                . " JOIN mkt.iarea as b on a.icabangid=b.icabangid "
                . " AND a.areaid=b.areaid WHERE "
                . " a.karyawanid='$idajukan' and a.icabangid='$pcabangid' AND IFNULL(a.areaid,'')<>'' AND IFNULL(a.aktif,'')<>'N'";
        $query_area .=" ORDER BY b.nama, a.areaid";
    }elseif ($pidjbtpl=="10" OR $pidjbtpl=="18") {
        $query_area = "select distinct a.areaid as areaid, b.nama as nama_area, a.aktif FROM mkt.ispv0 as a "
                . " JOIN mkt.iarea as b on a.icabangid=b.icabangid "
                . " AND a.areaid=b.areaid WHERE "
                . " a.karyawanid='$idajukan' and a.icabangid='$pcabangid' AND IFNULL(a.areaid,'')<>'' AND IFNULL(a.aktif,'')<>'N'";
        $query_area .=" ORDER BY b.nama, a.areaid";
    }else{
        $query_area = "select areaid as areaid, nama as nama_area FROM mkt.iarea WHERE icabangid='$pcabangid' "
                . " AND IFNULL(aktif,'')<>'N' ";
        $query_area .=" ORDER BY nama, areaid";
    }
    
    echo "<option value='' selected>-- Pilihan --</option>";
    if (!empty($query_area)) {
        
        $tampil = mysqli_query($cnmy, $query_area);
        while ($z= mysqli_fetch_array($tampil)) {
            $pidarea=$z['areaid'];
            $pnmarea=$z['nama_area'];
            if ($pidarea==$pareaid)
                echo "<option value='$pidarea' selected>$pnmarea</option>";
            else
                echo "<option value='$pidarea'>$pnmarea</option>";
        }
                    
    }
    
    mysqli_close($cnmy); exit;
    
}elseif ($pmodule=="XXX") {
}elseif ($pmodule=="XXX") {
}elseif ($pmodule=="XXX") {
    
}