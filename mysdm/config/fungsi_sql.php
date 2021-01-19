<?php

function getfield($ssql){
    include "config/koneksimysqli.php";
    $sql=mysqli_query($cnmy, $ssql);
    $ketemu=mysqli_num_rows($sql);
    $z=mysqli_fetch_array($sql);
    if ($ketemu > 0){
        return $z['lcfields'];
    }
    else {
        return '';
    }
}

function getfieldcnmy($ssql){
    include "../../config/koneksimysqli.php";
    $sql=mysqli_query($cnmy, $ssql);
    $ketemu=mysqli_num_rows($sql);
    $z=mysqli_fetch_array($sql);
    if ($ketemu > 0){
        return $z['lcfields'];
    }
    else {
        return '';
    }
}

function getfieldit($ssql){
    include "config/koneksimysqli_it.php";
    $sql=mysqli_query($cnit, $ssql);
    $ketemu=mysqli_num_rows($sql);
    $z=mysqli_fetch_array($sql);
    if ($ketemu > 0){
        return $z['lcfields'];
    }
    else {
        return '';
    }
}

function getfieldcnit($ssql){
    include "../../config/koneksimysqli_it.php";
    $sql=mysqli_query($cnit, $ssql);
    $ketemu=mysqli_num_rows($sql);
    $z=mysqli_fetch_array($sql);
    if ($ketemu > 0){
        return $z['lcfields'];
    }
    else {
        return '';
    }
}


function getfieldnew($ssql){
    include "config/koneksimysqli_ms.php";
    $sql=mysqli_query($cnms, $ssql);
    $ketemu=mysqli_num_rows($sql);
    $z=mysqli_fetch_array($sql);
    if ($ketemu > 0){
        return $z['lcfields'];
    }
    else {
        return '';
    }
}

function getfieldcnnew($ssql){
    include "../../config/koneksimysqli_ms.php";
    $sql=mysqli_query($cnms, $ssql);
    $ketemu=mysqli_num_rows($sql);
    $z=mysqli_fetch_array($sql);
    if ($ketemu > 0){
        return $z['lcfields'];
    }
    else {
        return '';
    }
}

function CariSudahClosingBRID1($nbrid, $nskode) {
    include "config/koneksimysqli.php";
    $query = "select a.bridinput from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput 
        JOIN dbmaster.t_suratdana_br_close c on a.idinput=c.idinput and b.idinput=c.idinput WHERE 
        IFNULL(b.stsnonaktif,'')<>'Y' and a.bridinput='$nbrid' ";
    if ($nskode=="A") $query .=" AND IFNULL(a.kodeinput,'') IN ('A', 'B', 'C') AND IFNULL(b.divisi,'')<>'OTC' ";
    elseif ($nskode=="D") $query .=" AND IFNULL(a.kodeinput,'') IN ('D') AND IFNULL(b.divisi,'')='OTC' ";
    elseif ($nskode=="E") $query .=" AND IFNULL(a.kodeinput,'') IN ('E') ";
		
		
    $sql=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($sql);
    $nnobridada="";
    if ($ketemu > 0){
        $z=mysqli_fetch_array($sql);
        $nnobridada= $z['bridinput'];
    }
    mysqli_close($cnmy);
    
    if (!empty($nnobridada) AND $nnobridada==$nbrid) return true;
    else return false;
}


function CariSudahClosingBRID2($nbrid, $nskode) {
    include "../../config/koneksimysqli.php";
    $query = "select a.bridinput from dbmaster.t_suratdana_br1 a JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput 
        JOIN dbmaster.t_suratdana_br_close c on a.idinput=c.idinput and b.idinput=c.idinput WHERE 
        IFNULL(b.stsnonaktif,'')<>'Y' and a.bridinput='$nbrid' ";
    if ($nskode=="A") $query .=" AND IFNULL(a.kodeinput,'') IN ('A', 'B', 'C') AND IFNULL(b.divisi,'')<>'OTC' ";
    elseif ($nskode=="D") $query .=" AND IFNULL(a.kodeinput,'') IN ('D') AND IFNULL(b.divisi,'')='OTC' ";
    elseif ($nskode=="E") $query .=" AND IFNULL(a.kodeinput,'') IN ('E') ";
		
		
    $sql=mysqli_query($cnmy, $query);
    $ketemu=mysqli_num_rows($sql);
    $nnobridada="";
    if ($ketemu > 0){
        $z=mysqli_fetch_array($sql);
        $nnobridada= $z['bridinput'];
    }
    mysqli_close($cnmy);
    
    if (!empty($nnobridada) AND $nnobridada==$nbrid) return true;
    else return false;
}

function CariDataKaryawanByRsmAuthCNIT($ikryid, $ijbt, $iregion) {
    include("config/koneksimysqli.php");
    $cnit=$cnmy;
    $pidkaryawan=$ikryid;
    $pidjabatan=$ijbt;
    $pidregion=$iregion;

    $filedgabungankry="";
    $pfilterkaryawan="'".$ikryid."',";
    $pfilterkaryawan2=$ikryid.",";
    $pfiltercabpilih="";
    
    if ($pidjabatan=="38") {
        $query = "select DISTINCT a.karyawanid as karyawanid, a.nama as nama  
            from hrd.karyawan as a 
            left join MKT.iarea as b ON a.areaid=b.areaid and a.icabangid=b.icabangid 
            where (a.jabatanid='15') and (a.tglkeluar='0000-00-00' OR a.aktif='Y') 
            and (a.divisiid<>'OTC')
            and a.icabangid in (select IFNULL(icabangid,'') from hrd.rsm_auth where karyawanid='$pidkaryawan') ";
        $tampil= mysqli_query($cnit, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($rs= mysqli_fetch_array($tampil)) {
                $vbkryid=$rs['karyawanid'];

                if (!empty($vbkryid)) {
                    if (strpos($pfilterkaryawan, $vbkryid)==false) {
                        $pfilterkaryawan .="'".$vbkryid."',";
                        $pfilterkaryawan2 .=$vbkryid.",";
                    }
                }

            }
        }
    }
    
    
    if (!empty($pfilterkaryawan)) {
        $pfilterkaryawan="(".substr($pfilterkaryawan, 0, -1).")";
        $pfilterkaryawan2=substr($pfilterkaryawan2, 0, -1);

        $filedgabungankry=$pfilterkaryawan." | ".$pfilterkaryawan2;

    }
    
    
    mysqli_close($cnit);
    return $filedgabungankry;
}

function CariDataKaryawanByRsmAuthCNMY($ikryid, $ijbt, $iregion) {
    include("config/koneksimysqli.php");
    
    $pidkaryawan=$ikryid;
    $pidjabatan=$ijbt;
    $pidregion=$iregion;

    $filedgabungankry="";
    $pfilterkaryawan="'".$ikryid."',";
    $pfilterkaryawan2=$ikryid.",";
    $pfiltercabpilih="";
    
    if ($pidjabatan=="38") {
        $query = "select DISTINCT a.karyawanid as karyawanid, a.nama as nama  
            from hrd.karyawan as a 
            join MKT.iarea as b ON a.areaid=b.areaid and a.icabangid=b.icabangid 
            where (a.jabatanid='15') and (a.tglkeluar='0000-00-00' OR a.aktif='Y') 
            and (a.divisiid<>'OTC')
            and a.icabangid in (select IFNULL(icabangid,'') from hrd.rsm_auth where karyawanid='$pidkaryawan') ";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($rs= mysqli_fetch_array($tampil)) {
                $vbkryid=$rs['karyawanid'];

                if (!empty($vbkryid)) {
                    if (strpos($pfilterkaryawan, $vbkryid)==false) {
                        $pfilterkaryawan .="'".$vbkryid."',";
                        $pfilterkaryawan2 .=$vbkryid.",";
                    }
                }

            }
        }
    }
    
    
    if (!empty($pfilterkaryawan)) {
        $pfilterkaryawan="(".substr($pfilterkaryawan, 0, -1).")";
        $pfilterkaryawan2=substr($pfilterkaryawan2, 0, -1);

        $filedgabungankry=$pfilterkaryawan." | ".$pfilterkaryawan2;

    }
    
    
    mysqli_close($cnmy);
    return $filedgabungankry;
}



function CariDataKaryawanByCabJbt($ikryid, $ijbt, $iregion) {
    include("config/koneksimysqli.php");

    $pidkaryawan=$ikryid;
    $pidjabatan=$ijbt;
    $pidregion=$iregion;

    $filedgabungankry="";
    $pfilterkaryawan="'".$ikryid."',";
    $pfilterkaryawan2=$ikryid.",";
    $pfiltercabpilih="";

    $query_cab="";
    if ($pidjabatan=="38") {
        $query_cab ="SELECT icabangid as icabangid FROM hrd.rsm_auth WHERE karyawanid='$pidkaryawan'";
    }elseif ($pidjabatan=="20") {
        $query_cab ="SELECT distinct icabangid as icabangid FROM MKT.ism0 WHERE karyawanid = '$pidkaryawan'";
    }elseif ($pidjabatan=="08") {
        $query_cab ="SELECT distinct icabangid as icabangid FROM MKT.idm0 WHERE karyawanid = '$pidkaryawan'";
    }elseif ($pidjabatan=="10" OR $pidjabatan=="18") {
        $query_cab ="SELECT distinct CONCAT(IFNULL(icabangid,''), IFNULL(areaid,''), IFNULL(divisiid,'')) as icabangid FROM MKT.ispv0 WHERE karyawanid = '$pidkaryawan'";
    }elseif ($pidjabatan=="05") {
        $query_cab ="SELECT distinct icabangid as icabangid FROM MKT.icabang WHERE region = '$pidregion'";
    }

    if (!empty($query_cab)) {
        $tampil= mysqli_query($cnmy, $query_cab);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($rs= mysqli_fetch_array($tampil)) {
                $vbicabangid=$rs['icabangid'];

                if (strpos($pfiltercabpilih, $vbicabangid)==false) {
                    $pfiltercabpilih .="'".$vbicabangid."',";
                }

            }
        }
    }



    if (!empty($pfiltercabpilih)) {
        $pfiltercabpilih="(".substr($pfiltercabpilih, 0, -1).")";


        //SM
        if ($pidjabatan<>"20" AND $pidjabatan<>"08" AND $pidjabatan<>"10" AND $pidjabatan<>"18" AND $pidjabatan<>"15") {

            $query ="SELECT distinct karyawanid as karyawanid FROM MKT.ism0 WHERE icabangid IN $pfiltercabpilih";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                while ($rs= mysqli_fetch_array($tampil)) {
                    $vbkryid=$rs['karyawanid'];

                    if (!empty($vbkryid)) {
                        if (strpos($pfilterkaryawan, $vbkryid)==false) {
                            $pfilterkaryawan .="'".$vbkryid."',";
                            $pfilterkaryawan2 .=$vbkryid.",";
                        }
                    }

                }
            }

        }


        //DM
        if ($pidjabatan<>"08" AND $pidjabatan<>"10" AND $pidjabatan<>"18" AND $pidjabatan<>"15") {

            $query ="SELECT distinct karyawanid as karyawanid FROM MKT.idm0 WHERE icabangid IN $pfiltercabpilih";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                while ($rs= mysqli_fetch_array($tampil)) {
                    $vbkryid=$rs['karyawanid'];

                    if (strpos($pfilterkaryawan, $vbkryid)==false) {
                        $pfilterkaryawan .="'".$vbkryid."',";
                        $pfilterkaryawan2 .=$vbkryid.",";
                    }

                }
            }

        }

        //SPV
        if ($pidjabatan<>"10" AND $pidjabatan<>"18" AND $pidjabatan<>"15") {

            $query ="SELECT distinct karyawanid as karyawanid FROM MKT.ispv0 WHERE icabangid IN $pfiltercabpilih";
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                while ($rs= mysqli_fetch_array($tampil)) {
                    $vbkryid=$rs['karyawanid'];

                    if (!empty($vbkryid)) {
                        if (strpos($pfilterkaryawan, $vbkryid)==false) {
                            $pfilterkaryawan .="'".$vbkryid."',";
                            $pfilterkaryawan2 .=$vbkryid.",";
                        }
                    }

                }
            }

        }

        //MR
        if ($pidjabatan<>"15") {
            if ($pidjabatan=="10" OR $pidjabatan=="18") {
                $query ="SELECT distinct karyawanid as karyawanid FROM MKT.imr0 WHERE CONCAT(IFNULL(icabangid,''), IFNULL(areaid,''), IFNULL(divisiid,'')) IN $pfiltercabpilih";
            }else{
                $query ="SELECT distinct karyawanid as karyawanid FROM MKT.imr0 WHERE icabangid IN $pfiltercabpilih";
            }
            $tampil= mysqli_query($cnmy, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu>0) {
                while ($rs= mysqli_fetch_array($tampil)) {
                    $vbkryid=$rs['karyawanid'];

                    if (!empty($vbkryid)) {
                        if (!empty($vbkryid)) {
                            if (strpos($pfilterkaryawan, $vbkryid)==false) {
                                $pfilterkaryawan .="'".$vbkryid."',";
                                $pfilterkaryawan2 .=$vbkryid.",";
                            }
                        }
                    }

                }
            }

        }


    }

	
    if (!empty($pfilterkaryawan)) {
        $pfilterkaryawan="(".substr($pfilterkaryawan, 0, -1).")";
        $pfilterkaryawan2=substr($pfilterkaryawan2, 0, -1);

        $filedgabungankry=$pfilterkaryawan." | ".$pfilterkaryawan2;

    }
	
	
    mysqli_close($cnmy);
    return $filedgabungankry;

}


function CariSelisihPeriodeDua($pperiode1, $pperiode2){
    $pselisihnya=1;
    
    $ptgl_mulai_sl  = $pperiode1;
    $ptgl_selesai_sl=$pperiode2;
    //convert
    $ptimeStart = strtotime($ptgl_mulai_sl);
    $ptimeEnd = strtotime($ptgl_selesai_sl);
    // Menambah bulan ini + semua bulan pada tahun sebelumnya
    $pblnselish = (date("Y",$ptimeEnd)-date("Y",$ptimeStart))*12;
    // hitung selisih bulan
    $pblnselish += date("m",$ptimeEnd)-date("m",$ptimeStart);
    
    
    $pselisihnya=$pblnselish;
    
    return $pselisihnya;
    
}

?>

