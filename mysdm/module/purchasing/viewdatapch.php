<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="caridatakaryawan") {
    
    $idajukan=$_POST['ukry'];
    $ppengajuanid=$_POST['uuntuk'];
    
    include "../../config/koneksimysqli.php";
    
    

    $query = "SELECT karyawanId as karyawanid, nama nama_karyawan, jabatanId as jabatanid, divisiId as divisiid FROM hrd.karyawan WHERE karyawanid='$idajukan'";
    $ptampil= mysqli_query($cnmy, $query);
    $nrs= mysqli_fetch_array($ptampil);    
    $pidjbtpl=$nrs['jabatanid'];
    $ndivisikry=$nrs['divisiid'];


$pkdspv="";
$pnamaspv="";
$pkddm="";
$pnamadm="";
$pkdsm="";
$pnamasm="";
$pkdgsm="";
$pnamagsm="";

$pcabangid="";
$pfilcabang="";
$query_cabang="";

$pdivisiid="";
$pfildivisi="";
$query_divisi="";

if ($ppengajuanid=="OTC" OR $ppengajuanid=="CHC") {
    
    $query ="SELECT a.karyawanid, b.nama nama_karyawan,
        b.icabangid as icabangid, b.areaid as areaid, b.jabatanid as jabatanid, a.icabangid as icabangid_posisi 
        FROM dbmaster.t_karyawan_posisi a 
        LEFT JOIN hrd.karyawan b on a.karyawanId=b.karyawanId WHERE a.karyawanid='$idajukan'";
    $ptampil= mysqli_query($cnmy, $query);
    $nrs= mysqli_fetch_array($ptampil);
    $pcabidpilihposisi=$nrs['icabangid_posisi'];
    $pcabidpilihposisi2=$nrs['icabangid'];
    $pareaidpilih=$nrs['areaid'];
    
    $pcabangid=$pcabidpilihposisi;
    if (empty($pcabangid)) {
        $pcabangid=$pcabidpilihposisi2;
    }
    $pdivisiid="OTC";
    
}else{
    
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
        
        if (empty($pfilcabang) AND empty($pcabangid)) {
            $query ="SELECT iCabangId as icabangid FROM hrd.karyawan WHERE karyawanId='$idajukan'";
            $ptampil= mysqli_query($cnmy, $query);
            $nrs= mysqli_fetch_array($ptampil);
            $pcabangid=$nrs['icabangid'];
        }
        
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

}
    
//CARI DEPARTEMEN
$pdepartmen="";
if ($ppengajuanid=="OTC" OR $ppengajuanid=="CHC" OR $ppengajuanid=="ETH") {
    //$pdepartmen="MKT";
    if ($pidjbtpl=="36") $pdepartmen="SLS03";
    else $pdepartmen="SLS01";
}else{
    if ($ppengajuanid=="HO") {
        $query = "select iddep FROM dbmaster.t_karyawan_dep WHERE karyawanid='$idajukan'";
        $ptampildv= mysqli_query($cnmy, $query);
        $ketemudv= mysqli_num_rows($ptampildv);
        $nrdv= mysqli_fetch_array($ptampildv);
        $pdepartmen=$nrdv['iddep'];
    }
}

//END CARI DEPARTEMEN

//CARI PENGECUALIAN
$ppenecualianatasan=false;
$query = "select * from dbpurchasing.t_karyawan_input_exc WHERE karyawanid='$idajukan'";
$ptampilexc= mysqli_query($cnmy, $query);
$ketemuexc= mysqli_num_rows($ptampilexc);
if ((DOUBLE)$ketemuexc>0) {
    $nexp= mysqli_fetch_array($ptampilexc);
    $ppengajuanexp=$nexp['pengajuan'];
    $pdepartmenexp=$nexp['iddep'];
    $pdivisiidexp=$nexp['divisi'];
    $patasanexp=$nexp['atasanid'];
    
    
    if (!empty($patasanexp)) {
        $query = "select nama from hrd.karyawan WHERE karyawanid='$patasanexp'";
        $tmpkn= mysqli_query($cnmy, $query);
        $tkn=mysqli_fetch_array($tmpkn);
        $pnamaatasanexp=$tkn['nama'];
    
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
        $pkdsm="";
        $pnamasm="";
        $pkdgsm=$patasanexp;
        $pnamagsm=$pnamaatasanexp;
        
    }
    
    
    if (!empty($pdivisiidexp)) {
        $pdivisilogin=$pdivisiidexp;
        $pdivisiid=$pdivisiidexp;
        
        $pfildivisi="('$pdivisiidexp')";
    }
    
    if (!empty($ppengajuanexp)) $pstatuslogin=$ppengajuanexp;
    if (!empty($pdepartmenexp)) $pdepartmen=$pdepartmenexp;
    
    if ($pdivisiidexp=="OTC") {
        $pcabangid="0000000007";
    }else{
        $pcabangid="0000000001";
    }
    
    $ppenecualianatasan=true;
}
        
//END CARI PENGECUALIAN

//CARI AREA
$pareaid="";
$pfilarea="";
$query_area="";

if ($ppengajuanid=="OTC" OR $ppengajuanid=="CHC") {
    $pareaid=$pareaidpilih;
}else{
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

}

//END CARI AREA

$pbukaarea="hidden";
if ($ppengajuanid=="OTC" OR $ppengajuanid=="CHC") {
    $pbukaarea="";
}

if ($ppengajuanid=="HO") {
    $pdivisiid="HO";
}
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
                if ($ppengajuanid=="OTC" OR $ppengajuanid=="CHC") {
                    $query = "select DivProdId as divprodid from mkt.divprod where DivProdId='OTC' ";
                    $query .=" Order by DivProdId";
                }else{
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
                }
                $tampil = mysqli_query($cnmy, $query);
                while ($z= mysqli_fetch_array($tampil)) {
                    $piddiv=$z['divprodid'];
                    $pnmdiv=$piddiv;
                    if ($piddiv=="CAN") $pnmdiv="CANARY/ETHICAL";
                    elseif ($piddiv=="PEACO") $pnmdiv="PEACOCK";
                    elseif ($piddiv=="PIGEO") $pnmdiv="PIGEON";
                    elseif ($piddiv=="OTC") $pnmdiv="CHC";

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
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang / Area <span class='required'></span></label>
        <div class='col-xs-5'>
            <select class='form-control input-sm' id='cb_cabang' name='cb_cabang' onchange="ShowDataArea()">
                <option value='' selected>-- Pilihan --</option>
                <?PHP
				if ($idajukan=="0000001556") {
					$query = "SELECT distinct icabangid_o as icabangid, nama as nama_cabang from dbmaster.v_icabang_o where aktif='Y' AND icabangid_o NOT IN ('JKT_MT', 'JKT_RETAIL') ";
                    $query .=" Order by nama";
                }elseif ($ppengajuanid=="OTC" OR $ppengajuanid=="CHC") {
                    $query = "select icabangid_o as icabangid, nama as nama_cabang from mkt.icabang_o WHERE 1=1 ";
                    if (!empty($pcabangid)) {
                        $query .= " AND icabangid_o='$pcabangid' ";
                    }
                    $query .=" Order by nama";
                }else{
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

    <div <?PHP echo $pbukaarea; ?> class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
        <div class='col-xs-5'>
            <select class='form-control input-sm' id='cb_area' name='cb_area' onchange="">
                <option value='' selected>-- Pilihan --</option>
                <?PHP
                $query_ara="";
                if ($ppengajuanid=="OTC" OR $ppengajuanid=="CHC") {
                    if (!empty($pcabangid)) {
                        $query_ara = "select icabangid_o as icabangid, areaid_o as areaid, nama as nama_area from mkt.iarea_o "
                                . " WHERE icabangid_o='$pcabangid' AND IFNULL(aktif,'')<>'N' ";
                        $query_ara .=" ORDER BY nama, areaid_o";
                    }
                }else{
                    if (!empty($pcabangid) AND !empty($pfilarea)) {//$pareaid
                        $query_ara = "select areaid as areaid, nama as nama_area FROM mkt.iarea WHERE icabangid='$pcabangid' "
                                . " AND IFNULL(areaid,'') IN $pfilarea AND IFNULL(aktif,'')<>'N' ";
                        $query_ara .=" ORDER BY nama, areaid";
                    }
                }
                
                if (!empty($query_ara)) {
                    $tampil = mysqli_query($cnmy, $query_ara);
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
    
    $ppengajuanid=$_POST['uuntuk'];
    $idajukan=$_POST['ukry'];
    
    include "../../config/koneksimysqli.php";
    
    
$pkdspv="";
$pnamaspv="";
$pkddm="";
$pnamadm="";
$pkdsm="";
$pnamasm="";
$pkdgsm="";
$pnamagsm="";
$pidjbtpl="";
$ndivisikry="";

    
//ATASAN

if ($ppengajuanid=="HO") {
    include "../../config/fungsi_sql.php";
    
    if ($idajukan=="0000001342"  OR $idajukan=="0000002074") {
        $pidatasan = getfieldcnmy("select atasanid2 as lcfields from hrd.karyawan WHERE karyawanid='$idajukan'");
    }else{
        $pidatasan = getfieldcnmy("select atasanid as lcfields from hrd.karyawan WHERE karyawanid='$idajukan'");
    }
    if (empty($pidatasan)) {
        $pidatasan = getfieldcnmy("select atasanid as lcfields from dbmaster.t_karyawan_posisi WHERE karyawanid='$idajukan'");
    }
    $pnmatasan = getfieldcnmy("select nama as lcfields from hrd.karyawan WHERE karyawanid='$pidatasan'");
    
    $pkdgsm=$pidatasan;
    $pnamagsm=$pnmatasan;

}else{
    $query ="SELECT a.karyawanid, b.nama nama_karyawan, a.spv, c.nama nama_spv, 
        a.dm, d.nama nama_dm, a.sm, e.nama nama_sm, a.gsm, f.nama nama_gsm, 
        b.icabangid as icabangid, b.areaid as areaid, b.jabatanid as jabatanid, b.divisiId as divisiid 
        FROM dbmaster.t_karyawan_posisi a 
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
    
}

// END ATASAN


//CARI PENGECUALIAN
$ppenecualianatasan=false;
$query = "select * from dbpurchasing.t_karyawan_input_exc WHERE karyawanid='$idajukan'";
$ptampilexc= mysqli_query($cnmy, $query);
$ketemuexc= mysqli_num_rows($ptampilexc);
if ((DOUBLE)$ketemuexc>0) {
    $nexp= mysqli_fetch_array($ptampilexc);
    $ppengajuanexp=$nexp['pengajuan'];
    $pdepartmenexp=$nexp['iddep'];
    $pdivisiidexp=$nexp['divisi'];
    $patasanexp=$nexp['atasanid'];
    
    
    if (!empty($patasanexp)) {
        $query = "select nama from hrd.karyawan WHERE karyawanid='$patasanexp'";
        $tmpkn= mysqli_query($cnmy, $query);
        $tkn=mysqli_fetch_array($tmpkn);
        $pnamaatasanexp=$tkn['nama'];
    
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
        $pkdsm="";
        $pnamasm="";
        $pkdgsm=$patasanexp;
        $pnamagsm=$pnamaatasanexp;
        
    }
    
    
    if (!empty($pdivisiidexp)) {
        $pdivisilogin=$pdivisiidexp;
        $pdivisiid=$pdivisiidexp;
        
        $pfildivisi="('$pdivisiidexp')";
    }
    
    if (!empty($ppengajuanexp)) $pstatuslogin=$ppengajuanexp;
    if (!empty($pdepartmenexp)) $pdepartmen=$pdepartmenexp;
    
    if ($pdivisiidexp=="OTC") {
        $pcabangid="0000000007";
    }else{
        $pcabangid="0000000001";
    }
    
    $ppenecualianatasan=true;
}
        
//END CARI PENGECUALIAN

$pnamagsmhos="GSM";
if ($ppengajuanid=="OTC" OR $ppengajuanid=="CHC") {
    $pnamagsmhos="HOS";
}elseif ($ppengajuanid=="HO") {
    $pnamagsmhos="Atasan";
}
if ($pidjbtpl=="05") {
	$pnamagsmhos="Atasan";
}


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
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><?PHP echo $pnamagsmhos; ?> <span class='required'></span></label>
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
	$pdivisi=$_POST['udivisi'];
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
	
	if ($pdivisi=="OTC" OR $pdivisi=="CHC" OR $pdivisi=="OT") {
        $query_area = "select areaid_o as areaid, nama as nama_area FROM mkt.iarea_o WHERE icabangid_o='$pcabangid' "
                . " AND IFNULL(aktif,'')<>'N' ";
        $query_area .=" ORDER BY nama, areaid_o";
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
    
}elseif ($pmodule=="carikaryawanid") {
    
    $ppengajuanid=$_POST['uuntuk'];
    
    $pidgroup=$_SESSION['GROUP'];
    $pidjbtpl=$_SESSION['JABATANID'];
    $pidcardpl=$_SESSION['IDCARD'];
    $idajukan=$_SESSION['IDCARD'];
    $nmajukan=$_SESSION['NAMALENGKAP']; 
    $pdivisilogin=$_SESSION['DIVISI']; 

    $pkaryawaninpilih=false;
    if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26" OR $pidgroup=="1" OR $pidgroup=="24") {
        $pkaryawaninpilih=true;
    }

    $pstatuslogin="HO";
    if ($pdivisilogin=="OTC" OR $pdivisilogin=="CHC") {
        $pstatuslogin="OTC";
        $pkaryawaninpilih=false;
    }else{
        if ($pidgroup<>"24" AND $pidgroup<>"1" AND ($pidjbtpl=="15" OR $pidjbtpl=="10" OR $pidjbtpl=="18" OR $pidjbtpl=="08" OR $pidjbtpl=="20" OR $pidjbtpl=="05" OR $pidjbtpl=="38") ) {
            $pstatuslogin="ETH";
            $pkaryawaninpilih=false;
        }
    }


    include "../../config/koneksimysqli.php";
    
    $query = "select karyawanid from dbpurchasing.t_pr_admin WHERE karyawanid='$pidcardpl'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $pkaryawaninpilih=true;
    }
    
    if ($pkaryawaninpilih==true) {

        $query = "select karyawanId as karyawanid, nama as nama_karyawan From hrd.karyawan WHERE 1=1 ";
        $query .= " AND ( ";
            $query .= " ( ";
                $query .= " (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                        . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                        . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                        . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                        . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                $query .= " AND nama NOT IN ('ACCOUNTING')";
                $query .= " AND karyawanid NOT IN ('0000002200', '0000002083') ";
                if ($ppengajuanid=="OTC" OR $ppengajuanid=="CHC") {
                     $query .= " AND divisiId IN ('OTC', 'CHC') ";
               }elseif ($ppengajuanid=="ETH") {
                    $query .= " AND jabatanId IN ('15', '10', '18', '08', '20', '05', '38') ";
                }else{
                    $query .= " AND divisiId NOT IN ('OTC', 'CHC') ";
                    $query .= " AND jabatanId NOT IN ('15', '10', '18', '08', '20', '05', '38') ";
                }
            $query .= " ) ";
        //$query .= " OR karyawanId='$idajukan' ) ";
        $query .= "  ) ";
        $query .= " ORDER BY nama";
    }else{
        $query = "select karyawanId as karyawanid, nama as nama_karyawan From hrd.karyawan WHERE 1=1 ";
        
		if ($pidcardpl=="0000002329" AND $ppengajuanid=="ETH") {
			if ($ppengajuanid=="ETH") {
				$query .= " AND karyawanid IN ('$pidcardpl', '$idajukan', '0000000158') AND karyawanid<>'0000002329' ";
			}else{
				$query .= " AND karyawanid IN ('$pidcardpl', '$idajukan') ";
			}
		}else{
			$query .= " AND (karyawanid ='$pidcardpl' OR karyawanid ='$idajukan') "; 
		}
    }

    $tampil = mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);

    echo "<option value='' selected>-- Pilihan --</option>";

    while ($z= mysqli_fetch_array($tampil)) {
        $pkaryid=$z['karyawanid'];
        $pkarynm=$z['nama_karyawan'];
        $pkryid=(INT)$pkaryid;
        echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
    }
    
    mysqli_close($cnmy); exit;
}elseif ($pmodule=="XXX") {
}elseif ($pmodule=="XXX") {
    
}