<?PHP
session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];

if ($pmodule=="viewdatakrybuat") {
    include "../../config/koneksimysqli.php";
    
    $picabangid = $_POST['ucab'];

    $query = "select DISTINCT karyawanid as karyawanid, nama as nama from hrd.tempkaryawandccdss_inp WHERE 
        icabangid='$picabangid' ";

    $query .=" AND jabatanid NOT IN ('15') ";

    $query .=" and LEFT(nama,7) NOT IN ('NN DM - ')  "
            . " and LEFT(nama,3) NOT IN ('TO.', 'TO-') "
            . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'DR - ', 'NN - ') ";


    if ($picabangid=="0000000030") {
        $query = "select karyawanId as karyawanid, nama as nama from hrd.karyawan WHERE karyawanId=159 ";
    }elseif ($picabangid=="0000000031") {
        $query = "select karyawanId as karyawanid, nama as nama from hrd.karyawan WHERE karyawanId=158 ";
    }
    $query .=" order by nama";

    $tampil=mysqli_query($cnmy, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($row=mysqli_fetch_array($tampil)){
        $cdkryid  = $row['karyawanid'];
        $cdnama = $row['nama'];

        echo "<option value='$cdkryid'>$cdnama</option>";
    }
    
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdatamridkary") {

    include "../../config/koneksimysqli.php";

    $karyawanid = $_POST['ukryid'];
    $icabangid = $_POST['ucab'];

    $query = "select DISTINCT karyawanid as karyawanid, nama as nama from hrd.tempkaryawandccdss_inp WHERE 
        icabangid='$icabangid' AND (karyawanid='$karyawanid' OR atasanid='$karyawanid' OR
        atasanid2='$karyawanid' OR atasanid3='$karyawanid') ";

    $query .=" AND (jabatanid = ('15') OR karyawanid='$karyawanid') ";

    $query .=" order by nama";

    if ($icabangid=='0000000030' OR $icabangid=='0000000031') {
        $query = "select distinct a.karyawanid as karyawanid, b.nama as nama 
            FROM MKT.imr0 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId 
            WHERE a.icabangid='$icabangid'";
            $query .=" order by b.nama";
    }

    $tampil=mysqli_query($cnmy, $query);
    echo "<option value='' selected>-- Pilihan --</option>";
    while($row=mysqli_fetch_array($tampil)){
        $cdkryid  = $row['karyawanid'];
        $cdnama = $row['nama'];

        echo "<option value='$cdkryid'>$cdnama</option>";
    }

    mysqli_close($cnmy);
    exit;
            
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
        $query = "select distinct (mrdoktbaru.dokterId),CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
                from hrd.mrdoktbaru as mrdoktbaru 
                join hrd.dokter as dokter on mrdoktbaru.dokterId=dokter.dokterId
                where dokter.nama<>''
                order by nama"; 
    } else {
        $query = "select distinct dokter.dokterId, CONCAT(dokter.nama,' - ',dokter.dokterId) AS nama 
                FROM hrd.mrdoktbaru as mrdoktbaru 
                join hrd.karyawan as karyawan on mrdoktbaru.karyawanId=karyawan.karyawanId
                join hrd.dokter as dokter on mrdoktbaru.dokterId=dokter.dokterId
                where dokter.nama <> '' $filter_kry_dok 
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
    
}elseif ($pmodule=="viewdatacombocoa") {
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
    
}elseif ($pmodule=="viewdatadaerahcab") {
    include "../../config/koneksimysqli.php";
    $pnidcab = $_POST['ucab'];
    $pnidkry = $_POST['ukry'];
    $pnidmr = $_POST['umr'];
    $piddaerah="";
    
    $npjbt1="";
    $npjbt2="";
    
    $query = "select karyawanid as karyawanid, jabatanid as jabatanid from hrd.karyawan where karyawanId='$pnidkry' OR karyawanId='$pnidmr'";
    $tampilk= mysqli_query($cnmy, $query);
    while ($nrk= mysqli_fetch_array($tampilk)) {
        $nnkryid=$nrk['karyawanid'];
        $nnjbtid=$nrk['jabatanid'];

        if ($nnkryid==$pnidkry) $npjbt1=$nnjbtid;
        elseif ($nnkryid==$pnidmr) $npjbt2=$nnjbtid;
    }
    
    
    $query_pilih="";
    if (empty($pnidmr)) {
        if ($npjbt1=="08") {
            $query_pilih="select distinct a.idcabang, a.nama from ms.cbgytd as a WHERE a.id_dm='$pnidkry' AND IFNULL(a.aktif,'')<>'N'";
        }elseif ($npjbt1=="20") {
            $query_pilih="select distinct a.idcabang, a.nama from ms.cbgytd as a WHERE a.id_sm='$pnidkry' AND IFNULL(a.aktif,'')<>'N'";
        }elseif ($npjbt1=="10" OR $npjbt1=="18") {
            $query_pilih = "select distinct a.idcabang, a.nama from ms.cbgytd as a "
                    . " JOIN ms.cabangareaytd as b on a.idcabang=b.idcbg "
                    . " JOIN sls.ispv0 as c on b.icabangid=c.icabangid AND b.areaid=c.areaid WHERE c.karyawanid='$pnidkry' AND IFNULL(a.aktif,'')<>'N'";
        }
    }else{
        if (!empty($npjbt2)) {
            if ($npjbt2=="08") {
                $query_pilih="select distinct a.idcabang, a.nama from ms.cbgytd as a WHERE a.id_dm='$pnidmr' AND IFNULL(a.aktif,'')<>'N'";
            }elseif ($npjbt2=="20") {
                $query_pilih="select distinct a.idcabang, a.nama from ms.cbgytd as a WHERE a.id_sm='$pnidmr' AND IFNULL(a.aktif,'')<>'N'";
            }elseif ($npjbt2=="10" OR $npjbt2=="18") {
                $query_pilih = "select distinct a.idcabang, a.nama from ms.cbgytd as a "
                        . " JOIN ms.cabangareaytd as b on a.idcabang=b.idcbg "
                        . " JOIN sls.ispv0 as c on b.icabangid=c.icabangid AND b.areaid=c.areaid WHERE c.karyawanid='$pnidmr' AND IFNULL(a.aktif,'')<>'N'";
            }elseif ($npjbt2=="15") {
                $query_pilih = "select distinct a.idcabang, a.nama from ms.cbgytd as a "
                        . " JOIN ms.cabangareaytd as b on a.idcabang=b.idcbg "
                        . " JOIN sls.imr0 as c on b.icabangid=c.icabangid AND b.areaid=c.areaid WHERE c.karyawanid='$pnidmr' AND IFNULL(a.aktif,'')<>'N'";
            }
        }
        
    }
    
    if (empty($query_pilih)) {
        $query_pilih="select distinct a.idcabang, a.nama from ms.cbgytd as a WHERE IFNULL(a.aktif,'')<>'N'";
    }
        
    $tampil = mysqli_query($cnmy, $query_pilih);
    $ketemu = mysqli_num_rows($tampil);
    
    echo "<option value='' >-- Pilihan --</option>";
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
    
    mysqli_close($cnmy);
    
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
}elseif ($pmodule=="viewnomordivisikd") {
    
    $pidcard="";
    if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];

    include "../../config/koneksimysqli.php";
    $tgl01=$_POST['utgl'];
    $tahuninput= date("Y", strtotime($tgl01));
    
    $bl= date("m", strtotime($tgl01));
    $byear= date("y", strtotime($tgl01));
    $bl=(int)$bl;
    $blromawi="I";
    if ($bl==1) $blromawi="I";
    if ($bl==2) $blromawi="II";
    if ($bl==3) $blromawi="III";
    if ($bl==4) $blromawi="IV";
    if ($bl==5) $blromawi="V";
    if ($bl==6) $blromawi="VI";
    if ($bl==7) $blromawi="VII";
    if ($bl==8) $blromawi="VIII";
    if ($bl==9) $blromawi="IX";
    if ($bl==10) $blromawi="X";
    if ($bl==11) $blromawi="XI";
    if ($bl==12) $blromawi="XII";
    
    $pdivsi = trim($_POST['udivisi']);
    $pkode = trim($_POST['ukode']);
    $psubkode = trim($_POST['ukodesub']);
    $padvance = trim($_POST['uadvance']);
    
    
    $nobuktinya="";
    $tno=0;
    $awal=3;
    
    $query = "SELECT MAX(SUBSTRING_INDEX(nodivisi, '/', 1)) as nodivisi FROM dbmaster.t_suratdana_br "
            . " WHERE stsnonaktif<>'Y' AND YEAR(tgl)='$tahuninput' AND "
            . " karyawanid='$pidcard'";
    //echo $query;
    $showkan= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($showkan);
    if ($ketemu>0){
        $sh= mysqli_fetch_array($showkan);
        $tno=(INT)$sh['nodivisi'];
        if (empty($tno)) $tno="0";
        
        $tno++;
    }
    
    $jml=  strlen($tno);
    $awal=$awal-$jml;
    if ($awal>=0)
        $tno=str_repeat("0", $awal).$tno;
    else
        $tno=$tno;

    
    mysqli_close($cnmy);
    
    $noslipurut=$tno."/BR $pdivsi/".$blromawi."/".$tahuninput;
    
    echo $noslipurut; exit;
    
}elseif ($pmodule=="cekdatasudahadakdisc") {
    
    $pkaryawanid="";
    if (isset($_SESSION['IDCARD'])) $pkaryawanid=$_SESSION['IDCARD'];
    
    include "../../config/koneksimysqli.php";
    
    $pidinput=$_POST['uid'];
    $pact=$_POST['uact'];
    $pnodivisi=$_POST['unodivisi'];
    
    $boleh="boleh";
    
    $query = "select nodivisi FROM dbmaster.t_suratdana_br WHERE IFNULL(stsnonaktif,'')<>'Y' AND idinput<>'$pidinput' AND nodivisi='$pnodivisi' AND karyawanid='$pkaryawanid'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {

        $boleh= "nodivisi tersebut sudah ada, silakan coba lagi...";

    }
        
    mysqli_close($cnmy);
    
    echo $boleh; exit;
    
}elseif ($pmodule=="x") {
}elseif ($pmodule=="x") {
}elseif ($pmodule=="x") {
}elseif ($pmodule=="x") {
    
}

?>