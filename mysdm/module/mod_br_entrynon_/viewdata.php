<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="viewdatacombocoa") {
    
	/*
    $psescardidid=$_SESSION['IDCARD'];
    $pnuseriid=(INT)$psescardidid;
    if (empty($pnuseriid)) $pnuseriid=$_SESSION['USERID'];
    
    
    include "../../config/koneksimysqli.php";
    
    //untuk yang non
    $filternondssdccCOA=" AND (bk.br = '') and (bk.br<>'N') ";
    $filternondssdcc=" AND ( (br = '' and br<>'N') OR user1=$pnuseriid ) ";
    
    $sql = "SELECT w.id, w.karyawanId, k.nama, w.COA4, c4.NAMA4,
	bk.br, bk.divprodid FROM dbmaster.coa_wewenang AS w
	LEFT JOIN hrd.karyawan AS k ON w.karyawanId = k.karyawanId
	LEFT JOIN dbmaster.coa_level4 AS c4 ON w.COA4 = c4.COA4
	LEFT JOIN dbmaster.br_kode AS bk ON c4.kodeid = bk.kodeid WHERE 
        w.karyawanId='$psescardidid' $filternondssdccCOA";
    
    $tampil=mysqli_query($cnmy, $sql);
    $ketemu=mysqli_num_rows($tampil);
    $filcoapilih="";
    if ($ketemu>0) {
        while ($r=  mysqli_fetch_array($tampil)) {
            $xccoaid=$r['COA4'];
            $filcoapilih .= "'".$xccoaid."',";
        }
        if (!empty($filcoapilih)) {
            $filcoapilih="(".substr($filcoapilih, 0, -1).")";
        }
    }
    
    $filteruntukcoa="";
    if (!empty($filcoapilih)) {
        $filteruntukcoa = " AND COA4 IN $filcoapilih ";
    }
    
    
    $pcoa4="";
    $pdivprodid = $_POST['udiv'];
    
    echo "<option value='' selected>-- Pilihan --</option>";
    
    $query = "SELECT DISTINCT COA4, NAMA4 FROM dbmaster.v_coa where (DIVISI='$pdivprodid' or ifnull(DIVISI,'')='') AND "
            . "( ((divprodid='$pdivprodid' and br = '') and (divprodid='$pdivprodid' and br<>'N'))   or ifnull(kodeid,'')='') $filteruntukcoa order by COA4";
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
    
    
    //untuk yang non
    $filternondssdccCOA=" AND (bk.br = '') and (bk.br<>'N') ";
    
		$query = "SELECT w.id, w.karyawanId, k.nama, w.COA4, c4.NAMA4,
		bk.br, bk.divprodid FROM dbmaster.coa_wewenang AS w
		LEFT JOIN hrd.karyawan AS k ON w.karyawanId = k.karyawanId
		LEFT JOIN dbmaster.coa_level4 AS c4 ON w.COA4 = c4.COA4
		LEFT JOIN dbmaster.br_kode AS bk ON c4.kodeid = bk.kodeid WHERE 
			bk.divprodid='$pdivprodid' $filternondssdccCOA";
			
    $query = "select DISTINCT d.DIVISI2, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, b.COA4, b.NAMA4, b.kodeid 
       from dbmaster.coa_level4 b 
       LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
       LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
       LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1 WHERE d.DIVISI2='$pdivprodid' AND IFNULL(b.kodeid,'')<>'' AND 
       IFNULL(b.kodeid,'') NOT IN (select IFNULL(kodeid,'') from dbmaster.br_kode WHERE (br <> '' and br<>'N')) ";
    $query .=" ORDER BY b.COA4";
		
    $tampil=mysqli_query($cnmy, $query);
    while($row=mysqli_fetch_array($tampil)){
        $nidcoa4=$row['COA4'];
        $nnmcoa4=$row['NAMA4'];
        
        if ($nidcoa4==$pcoa4)
            echo "<option value='$nidcoa4' selected>$nidcoa4 - $nnmcoa4</option>";
        else
            echo "<option value='$nidcoa4'>$nidcoa4 - $nnmcoa4</option>";
    }
    
	if ($pdivprodid=="HO") {
		echo "<option value=''>-- OTHERS --</option>";
		$query = "select DISTINCT d.DIVISI2, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, b.COA4, b.NAMA4, b.kodeid 
		   from dbmaster.coa_level4 b 
		   LEFT JOIN dbmaster.coa_level3 c ON c.COA3=b.COA3
		   LEFT JOIN dbmaster.coa_level2 d ON c.COA2=d.COA2
		   LEFT JOIN dbmaster.coa_level1 e ON e.COA1=d.COA1 WHERE d.DIVISI2 IN ('', 'OTHER', 'OTHERS') ";
		$query .=" ORDER BY b.COA4";
			
		$tampil=mysqli_query($cnmy, $query);
		while($row=mysqli_fetch_array($tampil)){
			$nidcoa4=$row['COA4'];
			$nnmcoa4=$row['NAMA4'];
			
			if ($nidcoa4==$pcoa4)
				echo "<option value='$nidcoa4' selected>$nidcoa4 - $nnmcoa4</option>";
			else
				echo "<option value='$nidcoa4'>$nidcoa4 - $nnmcoa4</option>";
		}

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
    
    $query = "select kodeid,nama,divprodid from dbmaster.br_kode where (divprodid='$pdivprodid' and br = '')  "
            . " and (divprodid='$pdivprodid' and br<>'N') order by nama";
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    echo "<option value='' >-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result); 
        $nkodeid  = $row['kodeid'];
        $nnama = $row['nama'];
		$ndivid = $row['divprodid'];
        
        if ($nkodeid==$kodeidcoa)
            echo "<option value=\"$nkodeid\" selected>$nnama - $nkodeid ($ndivid)</option>";
        else
            echo "<option value=\"$nkodeid\">$nnama - $nkodeid ($ndivid)</option>";
    }
    
    
    
    
    
    
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatacabangkaryawan") {
    include "../../config/koneksimysqli.php";
    
    //$pnmtablekry = "karyawan";
    $pnmtablekry = "tempkaryawandccdss_inp";
    
    $belumklik=false;
    $karyawanId = $_POST['umr'];
    $query = "select DISTINCT karyawan.iCabangId, cabang.nama, karyawan.icabangkaryawan from hrd.$pnmtablekry as karyawan join dbmaster.icabang as cabang on "
            . " karyawan.icabangid=cabang.icabangid where karyawanId='$karyawanId'  order by cabang.nama"; 
    $result = mysqli_query($cnmy, $query); 
    $record = mysqli_num_rows($result);
    if ($record==0) {
        $query = "select iCabangId, nama, '' as icabangkaryawan FROM MKT.icabang WHERE AKTIF='Y' order by nama";
        $result = mysqli_query($cnmy, $query); 
        $record = mysqli_num_rows($result);
        $belumklik=true;
    }
    
    
    echo "<option value=''>-- Pilihan --</option>";
    for ($i=0;$i < $record;$i++) {
        $row = mysqli_fetch_array($result);
        $cdicabkry  = $row['icabangkaryawan'];
        $cdkodeid  = $row['iCabangId'];
        $cdnama = $row['nama'];
        if ($cdkodeid==$cdicabkry) {
            echo "<option value=\"$cdkodeid\" selected>$cdnama</option>";
            $belumklik=true;
        }else{
            if ($belumklik==true) 
                echo "<option value=\"$cdkodeid\" >$cdnama</option>";
            else
                echo "<option value=\"$cdkodeid\" selected>$cdnama</option>";
        }
    }
    
    
    mysqli_close($cnmy);
}elseif ($pmodule=="xx") {
}elseif ($pmodule=="xx") {
}elseif ($pmodule=="xx") {
}
?>