<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdatacombocoa") {
    /*
    include "../../config/koneksimysqli.php";
    
    $pcoa4="";
    $pdivprodid = $_POST['udiv'];
    
    echo "<option value='' selected>-- Pilihan --</option>";
    
    $query = "SELECT COA4, NAMA4 FROM dbmaster.v_coa where DIVISI='$pdivprodid' AND "
            . "(divprodid='$pdivprodid' and br <> '') and (divprodid='$pdivprodid' and br<>'N') order by COA4";
    $tampil=mysqli_query($cnmy, $query);
    while($a=mysqli_fetch_array($tampil)){
        $nidcoa4=$a['COA4'];
        $nnmcoa4=$a['NAMA4'];
        
        if ($nidcoa4==$pcoa4)
            echo "<option value='$nidcoa4' selected>$nnmcoa4</option>";
        else
            echo "<option value='$nidcoa4'>$nnmcoa4</option>";
    }
    
    mysqli_close($cnmy);
    */
    include "../../config/koneksimysqli.php";
    
    $upilidcrd=$_SESSION['IDCARD'];
    $pnuseriid=$_SESSION['USERID'];
    
    $pcoa4="";
    $pdivprodid = $_POST['udiv'];
    
    echo "<option value='' selected>-- Pilihan --</option>";
    
    $filternondssdccCOA=" and (bk.br <> '' and bk.br<>'N') ";
    
    $query = "SELECT w.id, w.karyawanId, k.nama, w.COA4, c4.NAMA4,
	bk.br, bk.divprodid FROM dbmaster.coa_wewenang AS w
	LEFT JOIN hrd.karyawan AS k ON w.karyawanId = k.karyawanId
	LEFT JOIN dbmaster.coa_level4 AS c4 ON w.COA4 = c4.COA4
	LEFT JOIN dbmaster.br_kode AS bk ON c4.kodeid = bk.kodeid WHERE 
        bk.divprodid='$pdivprodid' $filternondssdccCOA";
    $tampil=mysqli_query($cnmy, $query);
    while($row=mysqli_fetch_array($tampil)){
        $nidcoa4=$row['COA4'];
        $nnmcoa4=$row['NAMA4'];
        
        if ($nidcoa4==$pcoa4)
            echo "<option value='$nidcoa4' selected>$nidcoa4 - $nnmcoa4</option>";
        else
            echo "<option value='$nidcoa4'>$nidcoa4 - $nnmcoa4</option>";
    }
    
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatacombokodenon") {
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $kodeidcoa="";
    $pdivprodid = $_POST['udiv'];
    $pcoa4 = $_POST['ucoa'];
    
    $kodeidcoa="";
    if (!empty($pcoa4)) {
        $kodeidcoa= getfieldcnmy("select kodeid as lcfields from dbmaster.coa_level4 where COA4='$pcoa4'");
    }
    
    //$query = "select kodeid,nama,divprodid from dbmaster.br_kode where (divprodid='$pdivprodid' and br <> '')  "
    //        . " and (divprodid='$pdivprodid' and br<>'N') order by nama";
			
    $query = "select kodeid,nama,divprodid from dbmaster.br_kode where kodeid='$kodeidcoa' "
            . " AND (divprodid='$pdivprodid' and br <> '') and (divprodid='$pdivprodid' and br<>'N')";
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    echo "<option value=''>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $nkodeid  = $row['kodeid'];
        $nnama = $row['nama'];
        $ndivid = $row['divprodid'];
        
        if ($nkodeid==$kodeidcoa)
            echo "<option value=\"$nkodeid\" selected>$nnama - $nkodeid ($ndivid)</option>";
        else
            echo "<option value=\"$nkodeid\">$nnama  - $nkodeid ($ndivid)</option>";
    }
    
    
    
    
    
    
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatakrybuat") {
    include "../../config/koneksimysqli.php";
    
    //$pnmtablekry = "karyawan";
    $pnmtablekry = "tempkaryawandccdss_inp";
    
    
    $icabangid = $_POST['ucab'];
    
    if (($icabangid=='0000000030') or ($icabangid=='0000000031') or ($icabangid=='0000000032')) {
        $query = "select b.karyawanId, b.nama, b.jabatanid, b.icabangid from hrd.karyawan b where (b.karyawanId='0000000154' or b.karyawanId='0000000159') AND b.aktif = 'Y' "; 
    }else{
        $query = "select b.karyawanId, b.nama, b.jabatanid, b.icabangid from hrd.$pnmtablekry b where b.icabangid='$icabangid' AND b.aktif = 'Y' "; 
		$query .= " AND ( IFNULL(b.tglkeluar,'')='' OR IFNULL(b.tglkeluar,'0000-00-00')='0000-00-00' ) ";
    }
	$query .= " AND b.jabatanId not in ('38') ";
    $query .= " AND b.karyawanid not in ('0000002083') ";
    $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ')  "
            . " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
            . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-') "
            . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ') ";
                                                
    $query .=" order by b.nama";
    
    $tampil=mysqli_query($cnmy, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($row=mysqli_fetch_array($tampil)){
        $cdkryid  = $row['karyawanId'];
        $cdnama = $row['nama'];
        
        echo "<option value='$cdkryid'>$cdnama</option>";
    }
    
    
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatamridkary") {
    include "../../config/koneksimysqli.php";
    
    //$pnmtablekry = "karyawan";
    $pnmtablekry = "tempkaryawandccdss_inp";
    
    
    $karyawanId = $_POST['ukryid']; 
    $icabangid = $_POST['ucab']; 
    $query = "select jabatanId from hrd.karyawan where karyawanId='$karyawanId'"; 	
    $result = mysqli_query($cnmy, $query);
    $records = mysqli_num_rows($result);
    $row = mysqli_fetch_array($result);
    $jabatanid = $row['jabatanId'];
    
    if ($icabangid=="0000000001") { //ho
        $querykry = "select b.karyawanId, b.nama, b.areaId from hrd.$pnmtablekry b WHERE b.aktif = 'Y' "; 
    }else{
        if (($icabangid=="0000000030") or ($icabangid=='0000000031') or ($icabangid=='0000000032')){ // irian, ambon, ntt
            $querykry = "select distinctb.karyawanId, b.nama, b.areaId from hrd.$pnmtablekry b where b.icabangid='$icabangid' AND b.aktif = 'Y' ";
        }else{
            
            if (($jabatanid=="18") or ($jabatanid=="10")) { //spv,am
                $querykry = "select distinct b.karyawanId, b.nama, b.areaId from hrd.$pnmtablekry b where (b.atasanId='$karyawanId' or b.atasanId2='$karyawanId') AND b.icabangid='$icabangid' ";
            }
            
            if ($jabatanid=="08") { //dm
                $querykry = "select distinct b.karyawanId, b.nama, b.areaId from hrd.$pnmtablekry b where b.iCabangId='$icabangid' "; 
            }
            if ($jabatanid=="15") { // mr
                $querykry = "select distinct b.karyawanId, b.nama, b.areaId from hrd.$pnmtablekry b where b.karyawanId='$karyawanId' AND b.icabangid='$icabangid' "; 
            }
            
        }
    }
    
    if (empty($querykry)) {
        $querykry = "select b.karyawanId, b.nama, b.areaId from hrd.$pnmtablekry b WHERE b.icabangid='$icabangid' AND b.aktif = 'Y' AND b.jabatanid IN ('15') "
                . " AND ( IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00' )"; 
        $querykry .= " AND b.karyawanid not in (select distinct IFNULL(karyawanid,'') FROM dbmaster.t_karyawanadmin) ";
    }
    $query .= " AND b.jabatanId not in ('19') ";
    //echo $querykry; exit;
    
    $querykry .= " AND b.karyawanid not in ('0000002083') ";
    //$querykry .= " AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ') ";
    $querykry .=" and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
            . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-') "
            . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ') ";
    
    $querykry .=" order by b.nama";
    $psudah=false;
    $tampil=mysqli_query($cnmy, $querykry);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($row=mysqli_fetch_array($tampil)){
        $cdkryid  = $row['karyawanId'];
        $cdnama = $row['nama'];
        
        echo "<option value='$cdkryid'>$cdnama</option>";
        
        if ($cdkryid==$karyawanId) $psudah=true;
    }
    
    
    if ($psudah==false) {
        $query = "select karyawanid as karyawanid, nama as nama from hrd.karyawan WHERE karyawanid='$karyawanId'";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ((DOUBLE)$ketemu>0) {
            $nrx= mysqli_fetch_array($tampil);
            $pidkry=$nrx['karyawanid'];
            $pnmkry=$nrx['nama'];
            echo "<option value=\"$pidkry\" >$pnmkry</option>";
        }
    }
	
    if ($karyawanId=="0000001031") {
        $query = "select karyawanid as karyawanid, nama as nama from hrd.karyawan WHERE karyawanid='0000002370'";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu=mysqli_num_rows($tampil);
        if ((DOUBLE)$ketemu>0) {
            $nrx= mysqli_fetch_array($tampil);
            $pidkry=$nrx['karyawanid'];
            $pnmkry=$nrx['nama'];
            echo "<option value=\"$pidkry\" >$pnmkry</option>";
        }
    }
	
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdoktermr") {
    include "../../config/koneksimysqli.php";
    
    
    $mr_id = $_POST['umr']; 
    $mr_id2 = $_POST['ucar2']; 
    $mkrybuat = $_POST['ukrybuat']; 
    $icabangid = $_POST['ucab']; 
    
    
    $pfilerkry="";
    if (empty($mr_id2) OR $mr_id2==$mr_id) {
        if (!empty($mkrybuat)) $pfilerkry="'".$mkrybuat."',";
        
        /*
        $query = "select karyawanid from MKT.ispv0 WHERE icabangid='$icabangid'";
        $tampil= mysqli_query($cnmy, $query);
        while ($nr= mysqli_fetch_array($tampil)) {
            $pikry=$nr['karyawanid'];
            $pfilerkry .="'".$pikry."',";
        }
        */
        
        $query = "select karyawanid from MKT.imr0 WHERE icabangid='$icabangid'";
        $tampila= mysqli_query($cnmy, $query);
        while ($nra= mysqli_fetch_array($tampila)) {
            $pikry=$nra['karyawanid'];
            $pfilerkry .="'".$pikry."',";
        }
        
        if (!empty($pfilerkry)) $pfilerkry="(".substr($pfilerkry, 0, -1).")";
    }
	
	
    $filter_kry_dok=" and karyawan.karyawanId='$mr_id' ";
    if (!empty($mkrybuat)) {
        $filter_kry_dok=" AND ( karyawan.karyawanId='$mr_id' OR IFNULL(karyawan.karyawanId,'')='$mkrybuat' OR dokter.dokterid IN ('0000025696', '0000057907') ) ";
    }
	
    if (!empty($pfilerkry)) {
        //$filter_kry_dok = " AND karyawan.karyawanId IN $pfilerkry ";
		//$filter_kry_dok = " AND (karyawan.karyawanId IN $pfilerkry OR IFNULL(karyawan.karyawanId,'')='$mr_id2' OR dokter.dokterid IN ('0000029935') ) ";
    }
	
	
    
    if ($icabangid=="0000000001") {
        $query = "select distinct (mr_dokt.dokterId),CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
                          from hrd.mr_dokt as mr_dokt 
                          join hrd.dokter as dokter on mr_dokt.dokterId=dokter.dokterId
                          where mr_dokt.aktif <> 'N' and dokter.nama<>''
                          order by nama"; 
    } else {
        $query = "select dokter.dokterId, CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
                          FROM hrd.mr_dokt as mr_dokt 
                          join hrd.karyawan as karyawan on mr_dokt.karyawanId=karyawan.karyawanId
                          join hrd.dokter as dokter on mr_dokt.dokterId=dokter.dokterId
                          where mr_dokt.aktif <> 'N' $filter_kry_dok and dokter.nama <> ''
                          order by dokter.nama";
    }
    
    $tampil=mysqli_query($cnmy, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($a=mysqli_fetch_array($tampil)){
        $ciddok=$a['dokterId'];
        $cnmdok=$a['nama'];
        echo "<option value='$ciddok'>$cnmdok</option>";
    }
    
    
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatanamadokter") {
    include "../../config/koneksimysqli.php";
    
    $pdokteridmr = $_POST['uiddok'];
    
    $query = "select nama as nama from hrd.dokter WHERE dokterid='$pdokteridmr'";
    $tampild= mysqli_query($cnmy, $query);
    $nrd= mysqli_fetch_array($tampild);
    $pnamadokter=$nrd['nama'];
    
    mysqli_close($cnmy);
    
    echo $pnamadokter;
    
}elseif ($pmodule=="viewdatanamadokterlist") {
    include "../../config/koneksimysqli.php";
    
    $pdokteridmr = $_POST['uiddok'];
    
    $query = "select distinct realisasi1 as nmrealisasi from hrd.br0 WHERE dokterid='$pdokteridmr' AND IFNULL(dokterid,'') NOT IN ('', '0', '(blank)')";
    $tampild= mysqli_query($cnmy, $query);
    while ($nrd= mysqli_fetch_array($tampild)) {
        $pnamareal=$nrd['nmrealisasi'];
        
        echo "<option value='$pnamareal'>";
    }
    
    mysqli_close($cnmy);
    
    
}elseif ($pmodule=="viewdatadaerahcab") {
    
    include "../../config/koneksimysqli.php";
    
    $pnidcab = $_POST['ucab'];
    $pnidkry = $_POST['ukry'];
    $pnidmr = $_POST['umr'];
    
    $npjbt1="";
    $npjbt2="";
    if (!empty($pnidkry) OR !empty($pnidmr)) {
        $query = "select karyawanid as karyawanid, jabatanid as jabatanid from hrd.karyawan where karyawanId='$pnidkry' OR karyawanId='$pnidmr'";
        $tampilk= mysqli_query($cnmy, $query);
        while ($nrk= mysqli_fetch_array($tampilk)) {
            $nnkryid=$nrk['karyawanid'];
            $nnjbtid=$nrk['jabatanid'];
            
            if ($nnkryid==$pnidkry) $npjbt1=$nnjbtid;
            elseif ($nnkryid==$pnidmr) $npjbt2=$nnjbtid;
        }
    }
    
    $pjabatanplh=$npjbt1;
    $pkryidplh=$pnidkry;
    
    if (!empty($pnidmr) AND !empty($npjbt2)) {
        $pjabatanplh=$npjbt2;
        $pkryidplh=$pnidmr;
    }
    
    
    $query_data="";
    if (!empty($pjabatanplh)) {
        if ($pjabatanplh=="15") {
            $query_data = "select idcbg as idcbg from mkt.cabangareaytd WHERE CONCAT(icabangid,areaid) IN 
                    (select CONCAT(icabangid,areaid) from mkt.imr0 where karyawanid='$pkryidplh') LIMIT 1";
        }elseif ($pjabatanplh=="10" OR $pjabatanplh=="18") {
            $query_data = "select idcbg as idcbg from mkt.cabangareaytd WHERE CONCAT(icabangid,areaid) IN 
                    (select CONCAT(icabangid,areaid) from mkt.ispv0 where karyawanid='$pkryidplh') LIMIT 1";
        }elseif ($pjabatanplh=="08") {
            $query_data = "select idcbg as idcbg from mkt.cabangareaytd WHERE id_dm='$pkryidplh'";
        }elseif ($pjabatanplh=="20") {
            $query_data = "select idcbg as idcbg from mkt.cabangareaytd WHERE id_sm='$pkryidplh'";
        }else{
            $query_data = "select idcabang as idcbg from dbmaster.cabangytd where icabangid='$pnidcab';";
        }  
    }
    
    if (empty($query_data)) {
        $query_data = "select idcabang as idcbg from dbmaster.cabangytd where icabangid='$pnidcab';";
    }
    
    $tampild= mysqli_query($cnmy, $query_data);
    $nrd= mysqli_fetch_array($tampild);
    $piddaerah=$nrd['idcbg'];
    
    echo "<option value=''>-- Pilihan --</option>";
    $query = "select DISTINCT a.idcabang as idcabang, a.nama as nama from MKT.cbgytd as a "
            . " LEFT JOIN dbmaster.cabangytd as b on a.idcabang=b.idcabang "
            . " WHERE "
            . " (a.aktif='Y' OR a.idcabang='$piddaerah') AND b.icabangid='$pnidcab' "
            . " order by a.nama";
    $tampil = mysqli_query($cnmy, $query);
    $ketemu = mysqli_num_rows($tampil);
    
    if ((DOUBLE)$ketemu<=0) {
        $query = "select a.idcabang as idcabang, a.nama as nama from MKT.cbgytd as a WHERE a.aktif='Y' order by a.nama";
        $tampil = mysqli_query($cnmy, $query);
        $ketemu = mysqli_num_rows($tampil);
    }
    
    while($a=mysqli_fetch_array($tampil)){
        $niddaer=$a['idcabang'];
        $nnmdaer=$a['nama'];

        if ($niddaer==$piddaerah)
            echo "<option value='$niddaer' selected>$nnmdaer</option>";
        else{
            if (empty($piddaerah) AND (DOUBLE)$ketemu==1){
                echo "<option value='$niddaer' selected>$nnmdaer</option>";
            }else{
                echo "<option value='$niddaer'>$nnmdaer</option>";
            }
        }
    }
    
    if ($pnidkry=="0000000009") {
        $query = "select a.idcabang as idcabang, a.nama as nama from MKT.cbgytd as a WHERE a.idcabang='045' order by a.nama";
        $tampil = mysqli_query($cnmy, $query);
        while($a=mysqli_fetch_array($tampil)){
            $niddaer=$a['idcabang'];
            $nnmdaer=$a['nama'];
            echo "<option value='$niddaer'>$nnmdaer</option>";
        }
    }
	
    mysqli_close($cnmy);
    
}elseif ($pmodule=="xx") {
}elseif ($pmodule=="xx") {
}
?>