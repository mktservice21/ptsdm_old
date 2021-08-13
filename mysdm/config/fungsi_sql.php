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

function CariSudahClosingBRID3($nbrid, $nskode) {
    include "../../../config/koneksimysqli.php";
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
        
                    //khusus admin cabang
                    if ($pidjabatan=="38") {
                        $query = "select DISTINCT a.karyawanid as karyawanid, a.nama as nama  
                            from hrd.karyawan as a 
                            left join MKT.iarea as b ON a.areaid=b.areaid and a.icabangid=b.icabangid 
                            where (a.jabatanid='15') and (a.tglkeluar='0000-00-00' OR a.aktif='Y') 
                            and (a.divisiid<>'OTC')
                            and a.icabangid in $pfiltercabpilih ";
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


    }

	
    if (!empty($pfilterkaryawan)) {
        $pfilterkaryawan="(".substr($pfilterkaryawan, 0, -1).")";
        $pfilterkaryawan2=substr($pfilterkaryawan2, 0, -1);

        $filedgabungankry=$pfilterkaryawan." | ".$pfilterkaryawan2;

    }
	
	
    mysqli_close($cnmy);
    return $filedgabungankry;

}

function CariDataKaryawanByCabJbt2($ikryid, $ijbt, $iregion) {
    include("../../config/koneksimysqli.php");

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
        
                    //khusus admin cabang
                    if ($pidjabatan=="38") {
                        $query = "select DISTINCT a.karyawanid as karyawanid, a.nama as nama  
                            from hrd.karyawan as a 
                            left join MKT.iarea as b ON a.areaid=b.areaid and a.icabangid=b.icabangid 
                            where (a.jabatanid='15') and (a.tglkeluar='0000-00-00' OR a.aktif='Y') 
                            and (a.divisiid<>'OTC')
                            and a.icabangid in $pfiltercabpilih ";
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


function BuatFormatNumberRp($prp, $ppilih) {
    if (empty($prp)) $prp=0;

    $numrp=$prp;
    if ($ppilih=="1") $numrp=number_format($prp,0,",",",");
    elseif ($ppilih=="2") $numrp=number_format($prp,0,".",".");
    elseif ($ppilih=="3") $numrp=number_format($prp,0,"","");

    return $numrp;
}
    
function getDistanceBetween($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi') 
{ 
	$theta = $longitude1 - $longitude2; 
	$distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)))  + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
	$distance = acos($distance); 
	$distance = rad2deg($distance); 
	$distance = $distance * 60 * 1.1515; 
	switch($unit) 
	{ 
		case 'Mi': break; 
		case 'Km' : $distance = $distance * 1.609344; 
	} 
	return (round($distance,2)); 
}


function CariSelisihJamMenit($psts, $libur, $ptgl, $pmskawal, $mskpakhir, $lambat) {
    $pwaktuselisih="";
    
    if (empty($ptgl)) {
        return "invalid";
    }
    
    if ($psts=="3") {
        //$pmskawal="08:15";
        //$mskpakhir="08:15";
        if ( (INT)substr($mskpakhir,0,2)<=7 ) {
            return "";
        }elseif ( (INT)substr($mskpakhir,0,2)==8 && (INT)substr($mskpakhir,3,2)==0 ) {
            return "";
        }else{
            if ( (INT)substr($mskpakhir,0,2)==8 ) {
                if ( (INT)substr($mskpakhir,3,2)<=(INT)$lambat ) {
                    return "";
                }
            }
        }
    }elseif ($psts=="1") {
        
        if ( !empty($pmskawal) && (INT)substr($pmskawal,0,2)<8 ) {
            //$pmskawal="08:00";
        }

        if ( !empty($mskpakhir) && (INT)substr($mskpakhir,0,2)>=17 && (INT)substr($pmskawal,0,2)<=17 ) {
            //$mskpakhir="17:00";
        }
        
    }
    
    $pawal=$ptgl." ".$pmskawal;
    $pakhir=$ptgl." ".$mskpakhir;

    if (empty($pmskawal)) $pawal="";
    if (empty($mskpakhir)) $pakhir="";
    
    if (empty($pawal) || empty($pakhir)) {
        if ($libur=="Y") $pwaktuselisih="";
        else $pwaktuselisih="invalid";

        if (empty($pawal) && empty($pakhir)) $pwaktuselisih="";

    }else{
        
        $pwaktu_awal_masuk=strtotime($pawal);
        $pwaktu_awal_pulang=strtotime($pakhir);
        
        //Selisih dg hasil detik
        $pselisih_    =$pwaktu_awal_pulang - $pwaktu_awal_masuk;
        //membagi detik menjadi jam
        $pjam_selisih    =floor($pselisih_ / (60 * 60));
        //membagi sisa detik setelah dikurangi $pjam_selisih menjadi menit
        $pmenit_selisih    =( $pselisih_ - $pjam_selisih * (60 * 60) ) / 60;

        $pjam_selisih = str_pad($pjam_selisih, 2, '0', STR_PAD_LEFT);
        $pmenit_selisih = str_pad($pmenit_selisih, 2, '0', STR_PAD_LEFT);

        $pwaktuselisih=$pjam_selisih.":".$pmenit_selisih;

    }   
    
    return $pwaktuselisih;
    
}

function CariSelisihJamMenit01($psts, $libur, $ptgl, $pmskawal, $mskpakhir, $pistawal, $istpakhir) {
    $pselisihmasuk="";
    $pselisihist="";
    $pkey=1;
    if (empty($ptgl)) {
        return "invalid";
    }
    
    if ($psts=="2") {
        $pawal=$ptgl." ".$pistawal;
        $pakhir=$ptgl." ".$istpakhir;
        
        if (empty($pistawal)) $pawal="";
        if (empty($istpakhir)) $pakhir="";
        
    }else{
        
        if (!empty($pmskawal) || !empty($mskpakhir)) {
            if ( (INT)substr($pmskawal,0,2)<8 ) {
                $pmskawal="08:00";
            }

            if ( (INT)substr($mskpakhir,0,2)>=17 && (INT)substr($pmskawal,0,2)<=17 ) {
                $mskpakhir="17:00";
            }
        }
        
        $pawal=$ptgl." ".$pmskawal;
        $pakhir=$ptgl." ".$mskpakhir;
        
        if (empty($pmskawal)) $pawal="";
        if (empty($mskpakhir)) $pakhir="";
    }
    
    
    $pkey=1;
    ulangselisih:
        if (empty($pawal) || empty($pakhir)) {
            if ($libur=="Y") $pwaktuselisih="";
            else $pwaktuselisih="invalid";
            
            if ($pkey==1) {
                if (empty($pawal) && empty($pakhir)) $pwaktuselisih="";
                return $pwaktuselisih;
            }else{
                if (empty($pawal) && empty($pakhir)) $pwaktuselisih="";
            }

        }else{

            $pwaktu_awal_masuk=strtotime($pawal);
            $pwaktu_awal_pulang=strtotime($pakhir);

            //Selisih dg hasil detik
            $pselisih_    =$pwaktu_awal_pulang - $pwaktu_awal_masuk;
            //membagi detik menjadi jam
            $pjam_selisih    =floor($pselisih_ / (60 * 60));
            //membagi sisa detik setelah dikurangi $pjam_selisih menjadi menit
            $pmenit_selisih    =( $pselisih_ - $pjam_selisih * (60 * 60) ) / 60;

            $pjam_selisih = str_pad($pjam_selisih, 2, '0', STR_PAD_LEFT);
            $pmenit_selisih = str_pad($pmenit_selisih, 2, '0', STR_PAD_LEFT);

            $pwaktuselisih=$pjam_selisih.":".$pmenit_selisih;

            if ($pkey==1) $pselisihmasuk=$pwaktuselisih;
            elseif ($pkey==2) {
                $pselisihist=$pwaktuselisih;
                if ($pselisihist=="invalid") $pselisihist="";
            }elseif ($pkey=3){
                return $pwaktuselisih;
            }

        }
    
        
        if ($psts=="1" || $psts=="2") {
            return $pwaktuselisih;
        }else{

            if ($pkey==1) {
                if (empty($pselisihmasuk) || $pselisihmasuk=="invalid") return "";
            }

            //istirahatnya
            $pawal=$ptgl." ".$pistawal;
            $pakhir=$ptgl." ".$istpakhir;

            if (empty($pistawal)) $pawal="";
            if (empty($istpakhir)) $pakhir="";

            if ($pkey==1 AND ( !empty($pistawal) AND !empty($istpakhir) ) ) {
                $pkey=2;
                goto ulangselisih;

            }
            
            //return "$pselisihmasuk : $pselisihist";
            //return "";
            
            
            
            if (empty($pselisihist) && $pselisihist<>"invalid"){
                return "$pselisihmasuk";
            }else{
                $pmasuk_j=false;
                $pkeluar_j=false;
                
                //$pmskawal="08:00"; $mskpakhir="13:00";
                
                $phasil="";
                
                if ( (INT)substr($pmskawal,0,2)<11 ) {
                    $pmasuk_j=true;
                }elseif ( (INT)substr($pmskawal,0,2)==11 ) {
                    if ( (INT)substr($pmskawal,3,2)<=0 ) {
                        $pmasuk_j=true;
                    }
                }
                
                if ($pmasuk_j==true) {
                    
                    if ( (INT)substr($mskpakhir,0,2)>=13 && (INT)substr($mskpakhir,3,2)>=0 ) {
                        $pkeluar_j=true;
                    }else{
                        $pkeluar_j=false;
                    }
                    
                }
                
                if ($pkeluar_j==false || $pmasuk_j==false) {
                    
                    return "$pselisihmasuk";
                    //$phasil="tidak ada istirahat";
                    
                }else{
                    
                    //$phasil="$pselisihmasuk & $pselisihist";
                    
                    $pawal=$ptgl." ".$pselisihist;
                    $pakhir=$ptgl." ".$pselisihmasuk;
                    
                    $pkey=3;
                    goto ulangselisih;
                    
                    
                }
                
                return $phasil;
                
            }
            
            
        }
}

?>

