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
    
    $query = "select nodivisi from dbmaster.t_kode_spd_nodivisi WHERE karyawanid='$pidcard' AND jenis_rpt='$padvance' AND subkode='$psubkode'";
    $tampilk= mysqli_query($cnmy, $query);
    $nrow= mysqli_fetch_array($tampilk);
    $ppilnodiv=$nrow['nodivisi'];
    if (empty($ppilnodiv)) $ppilnodiv="BR-KD/$pdivsi";
    
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
    
    $noslipurut=$tno."/$ppilnodiv/".$blromawi."/".$tahuninput;
    
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
    
}elseif ($pmodule=="caridatanodivdari") {
    include "../../config/koneksimysqli.php";
    $ppilihan=$_POST['upilihan'];
    
    $nfilter="";
    if ($ppilihan=="N") $nfilter=" and b.pilih='N' ";
    
    echo "<option value='' selected>-- Pilihan --</option>";
    $query = "select DISTINCT a.idinputbank, a.divisi, a.nodivisi from dbmaster.t_suratdana_bank a 
        JOIN dbmaster.t_suratdana_br b on a.nodivisi=b.nodivisi and a.idinput=b.idinput
        where a.stsnonaktif<>'Y' 
        and a.stsinput='K' AND a.subkode IN ('01', '02', '20') $nfilter order by 2,3";
    $tampil = mysqli_query($cnmy, $query);
    while ($z= mysqli_fetch_array($tampil)) {
        $pnnodivbr=$z['nodivisi'];
        $pidinputbankdari=$z['idinputbank'];
        echo "<option value='$pidinputbankdari'>$pnnodivbr</option>";
    }
    mysqli_close($cnmy);
}elseif ($pmodule=="viewdatapcmrp") {
    
    include "../../config/koneksimysqli.php";
    $pdivisi = $_POST['udivisid'];
    $ptgl = $_POST['utgl'];
    $ptahun= date("Y", strtotime($ptgl));
    
    $query="select jumlah from dbmaster.t_uangmuka where divisi='$pdivisi'";
    $tampil=mysqli_query($cnmy, $query);
    $a=mysqli_fetch_array($tampil);
    $pjmlpc=$a['jumlah'];
    
    $query="select jml_ots from dbmaster.t_outstanding_br where divisi='$pdivisi' AND tahun='$ptahun'";
    $tampil=mysqli_query($cnmy, $query);
    $a=mysqli_fetch_array($tampil);
    $pjmlots=$a['jml_ots'];
    
    $psisarp=(DOUBLE)$pjmlpc-(DOUBLE)$pjmlots;
    
    if (empty($pjmlpc)) $pjmlpc=0;
    if (empty($pjmlots)) $pjmlots=0;
    if (empty($psisarp)) $psisarp=0;
    
    mysqli_close($cnmy);
    echo "$pjmlpc|$pjmlots|$psisarp"; exit;
    
}elseif ($pmodule=="viewkodeiddanjenis") {
    include "../../config/koneksimysqli.php";
    $pjenis = $_POST['ujeniskode'];
    $pkodeid="";
    $psubkode="";
    
    $query_data = "dbmaster.t_kode_spd_pengajuan as a "
            . " JOIN dbmaster.t_kode_spd as b on a.subkode=b.subkode "
            . " WHERE a.jenis_rpt='$pjenis' AND IFNULL(a.igroup,'')='1'";
    
?>

    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode <span class='required'></span></label>
        <div class='col-xs-5'>
              <select class='form-control input-sm' id='cb_kode' name='cb_kode' onchange="" data-live-search="true">
                  <!--<option value='' selected>-- Pilihan --</option>-->
                  <?PHP
                    $query_1 = "select distinct a.jenis_rpt, b.kodeid, b.nama from ".$query_data;
                    $query_1 .=" ORDER BY b.nama, b.kodeid";
                    $tampil = mysqli_query($cnmy, $query_1);
                    while ($z= mysqli_fetch_array($tampil)) {
                        $nkodeid=$z['kodeid'];
                        $nkodenm=$z['nama'];

                        if ($nkodeid==$pkodeid)
                            echo "<option value='$nkodeid' selected>$nkodeid - $nkodenm</option>";
                        else
                            echo "<option value='$nkodeid'>$nkodeid - $nkodenm</option>";
                    }
                  ?>
              </select>
        </div>
    </div>


    <div class='form-group'>
        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Sub Kode <span class='required'></span></label>
        <div class='col-xs-5'>
              <select class='form-control input-sm' id='cb_kodesub' name='cb_kodesub' data-live-search="true" onchange="">
                  <!--<option value='' selected>-- Pilihan --</option>-->
                  <?PHP
                    
                    $query_2 = "select distinct a.jenis_rpt, b.kodeid, b.nama, a.subkode, b.subnama from ".$query_data;
                    $query_2 .=" ORDER BY b.subnama, a.subkode, b.nama, b.kodeid";

                    $tampil2 = mysqli_query($cnmy, $query_2);
                    while ($z= mysqli_fetch_array($tampil2)) {
                        $nsubid=$z['subkode'];
                        $nsubnm=$z['subnama'];

                        if ($nsubid==$psubkode)
                            echo "<option value='$nsubid' selected>$nsubid - $nsubnm</option>";
                        else
                            echo "<option value='$nsubid'>$nsubid - $nsubnm</option>";
                    }
                    
                  ?>
              </select>
        </div>
    </div>

<?PHP
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdataareacab") {
    include "../../config/koneksimysqli.php";
    
    $pidcab=$_POST['uidcab'];
    
    echo "<option value='' selected>-- Pilih --</option>";
    $query = "select areaid as areaid, nama as nama from mkt.iarea WHERE iCabangId='$pidcab' AND IFNULL(aktif,'')<>'N' ";
    $query .=" order by nama";
    $tampil= mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $nareaid=$row['areaid'];
        $nareanm=$row['nama'];
        
        echo "<option value='$nareaid' >$nareanm</option>";
    }
    
    mysqli_close($cnmy);
    
}elseif ($pmodule=="viewdatadokter") {
    include "../../config/koneksimysqli_ms.php";
    
    $pidjbt=$_SESSION['JABATANID'];
    $pidcard=$_SESSION['IDCARD'];
    $pidcab=$_POST['uidcab'];
    $pidarea=$_POST['uidarea'];
    $pidoutlet=$_POST['uoutletid'];
    
    echo "<option value='' selected>-- Pilih --</option>";
    //d.iCabangId as icabangid, e.nama as nama_cabang, d.areaId as areaid, f.Nama as nama_area, 
    $query = "SELECT DISTINCT 
        a.iddokter, g.namalengkap as nama_dokter, g.spesialis, h.nama as nama_spesialis  
        FROM ms2.tempatpraktek as a 
        JOIN ms2.outlet_master as b on a.outletId=b.id 
        LEFT JOIN ms2.outlet_type as c on b.type=c.id 
        JOIN ms2.outlet_customer as d on a.outletId=d.outletId 
        LEFT JOIN mkt.icabang as e on d.iCabangId=e.iCabangId 
        LEFT JOIN mkt.iarea as f on d.iCabangId=f.iCabangId and d.areaId=f.areaId 
        JOIN ms2.masterdokter as g on a.iddokter=g.id 
        LEFT JOIN ms2.lookup as h on g.spesialis=h.id 
        WHERE d.icabangid='$pidcab' ";
    if (!empty($pidarea)) $query .=" AND d.areaid='$pidarea' ";
    else{
        if ($pidjbt=="15") {
            $query .=" AND d.areaid IN (select DISTINCT areaId FROM sls.imr0 WHERE karyawanid='$pidcard' AND iCabangId='$pidcab') ";
        }elseif ($pidjbt=="10" OR $pidjbt=="18") {
            $query .=" AND d.areaid IN (select DISTINCT areaId FROM sls.ispv0 WHERE karyawanid='$pidcard' AND iCabangId='$pidcab') ";
        }
    }
    if (!empty($pidoutlet)) $query .=" AND a.outletId='$pidoutlet' ";
    $query .=" ORDER BY g.namalengkap, a.iddokter";
    $tampil= mysqli_query($cnms, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pniddokt=$row['iddokter'];
        $pnnmdokt=$row['nama_dokter'];

        echo "<option value='$pniddokt' >$pnnmdokt - ($pniddokt)</option>";
    }    
    
    mysqli_close($cnms);
    
}elseif ($pmodule=="viewdataoutlet") {
    include "../../config/koneksimysqli_ms.php";
    
    $pidjbt=$_SESSION['JABATANID'];
    $pidcard=$_SESSION['IDCARD'];
    $pidcab=$_POST['uidcab'];
    $pidarea=$_POST['uidarea'];
    $piddokt=$_POST['uiddokt'];
    
    echo "<option value='' selected>-- Pilih --</option>";
    
    //d.iCustId as icustid, a.id as idpraktek, a.approve as approvepraktek, a.iddokter, g.namalengkap as nama_dokter, g.spesialis, h.nama as nama_spesialis
    $query = "SELECT distinct a.outletId as idoutlet, b.nama as nama_outlet, b.alamat,  
        b.jenis, b.type, c.Nama as nama_type, b.dispensing, 
        d.iCabangId as icabangid, e.nama as nama_cabang, d.areaId as areaid, f.Nama as nama_area  
        FROM ms2.tempatpraktek as a 
        JOIN ms2.outlet_master as b on a.outletId=b.id 
        LEFT JOIN ms2.outlet_type as c on b.type=c.id 
        JOIN ms2.outlet_customer as d on a.outletId=d.outletId 
        LEFT JOIN mkt.icabang as e on d.iCabangId=e.iCabangId 
        LEFT JOIN mkt.iarea as f on d.iCabangId=f.iCabangId and d.areaId=f.areaId 
        JOIN ms2.masterdokter as g on a.iddokter=g.id 
        LEFT JOIN ms2.lookup as h on g.spesialis=h.id 
        WHERE d.icabangid='$pidcab' ";
    if (!empty($piddokt)) $query .=" AND a.iddokter='$piddokt' ";
    
    if (!empty($pidarea)) $query .=" AND d.areaid='$pidarea' ";
    else{
        if ($pidjbt=="15") {
            $query .=" AND d.areaid IN (select DISTINCT areaId FROM sls.imr0 WHERE karyawanid='$pidcard' AND iCabangId='$pidcab') ";
        }elseif ($pidjbt=="10" OR $pidjbt=="18") {
            $query .=" AND d.areaid IN (select DISTINCT areaId FROM sls.ispv0 WHERE karyawanid='$pidcard' AND iCabangId='$pidcab') ";
        }
    }
                    
    $query .=" ORDER BY b.nama, a.id";
    $tampil= mysqli_query($cnms, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pnidpraktek=$row['idpraktek'];
        $pnareaid=$row['areaid'];
        $pnareanm=$row['nama_area'];
        $pnotlid=$row['idoutlet'];
        $pnotlnm=$row['nama_outlet'];
        $pntypeotl=$row['nama_type'];
        $pndispensing=$row['dispensing'];
        $pnalamatotl=$row['alamat'];
        $pniddokt=$row['iddokter'];
        $pnnmdokt=$row['nama_dokter'];
        $pnnamatype=$row['nama_type'];
        
        echo "<option value='$pnotlid' >$pnotlnm - $pnotlid ($pnnamatype)</option>";
    }
    
    mysqli_close($cnms);
    
}elseif ($pmodule=="viewlistdokter") {
    
    ?>
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    <script src="js/inputmask.js"></script>
    <?PHP
    
    include "../../config/koneksimysqli_ms.php";
    
    $pact=$_GET['act'];
    
    $pidjbt=$_SESSION['JABATANID']; 
    $pidcard=$_SESSION['IDCARD'];
    $pidinput=$_POST['uidinput'];
    $pidcab=$_POST['uidcab'];
    $pidarea=$_POST['uidarea'];
    $piddokt=$_POST['uiddokt'];
    $pidoutlet=$_POST['uoutletid'];
    $pjmlminta=$_POST['ujumlah'];
    $pketerangan=$_POST['uket'];
    $princitotal="";
    
    $pdivnonearea="";
    $pdivnoneuser="";
    $pdivnonelokasi="";
    $pdivnonejml="";
    $pdivnonereal=" class='divnone' ";
    
    $pdivnonetr="";
    
    if (!empty($pidarea)) $pdivnonearea=" class='divnone' ";
    if (!empty($piddokt)) $pdivnoneuser=" class='divnone' ";
    if (!empty($pidoutlet)) $pdivnonelokasi=" class='divnone' ";
    
    if ($pidinput=="0") $pidinput="";
    

    
    
    ?>
        <table id="dtabel" class="table table-bordered table-striped table-highlight">
            <?PHP
            
                    $query = "SELECT distinct a.approve as approvepraktek, a.id as idpraktek, a.outletId as idoutlet, b.nama as nama_outlet, b.alamat,  
                        b.jenis, b.type, c.Nama as nama_type, b.dispensing, 
                        d.iCabangId as icabangid, e.nama as nama_cabang, d.areaId as areaid, f.Nama as nama_area, 
                        a.iddokter, g.namalengkap as nama_dokter, g.spesialis, h.nama as nama_spesialis  
                        FROM ms2.tempatpraktek as a 
                        JOIN ms2.outlet_master as b on a.outletId=b.id 
                        LEFT JOIN ms2.outlet_type as c on b.type=c.id 
                        JOIN ms2.outlet_customer as d on a.outletId=d.outletId 
                        LEFT JOIN mkt.icabang as e on d.iCabangId=e.iCabangId 
                        LEFT JOIN mkt.iarea as f on d.iCabangId=f.iCabangId and d.areaId=f.areaId 
                        JOIN ms2.masterdokter as g on a.iddokter=g.id 
                        LEFT JOIN ms2.lookup as h on g.spesialis=h.id 
                        WHERE d.icabangid='$pidcab' AND IFNULL(a.deletedby,'')='' ";
                
                $query = "SELECT distinct a.iddokter, g.namalengkap as nama_dokter, g.spesialis, h.nama as nama_spesialis 
                    FROM ms2.tempatpraktek as a 
                    JOIN ms2.outlet_master as b on a.outletId=b.id 
                    LEFT JOIN ms2.outlet_type as c on b.type=c.id 
                    JOIN ms2.outlet_customer as d on a.outletId=d.outletId 
                    LEFT JOIN mkt.icabang as e on d.iCabangId=e.iCabangId 
                    LEFT JOIN mkt.iarea as f on d.iCabangId=f.iCabangId and d.areaId=f.areaId 
                    JOIN ms2.masterdokter as g on a.iddokter=g.id 
                    LEFT JOIN ms2.lookup as h on g.spesialis=h.id 
                    WHERE d.icabangid='$pidcab' AND IFNULL(a.deletedby,'')='' ";
                
                if (!empty($piddokt)) $query .=" AND a.iddokter='$piddokt' ";
                if (!empty($pidoutlet)) $query .=" AND a.outletId='$pidoutlet' ";

                if (!empty($pidarea)) $query .=" AND d.areaid='$pidarea' ";
                else{
                    if ($pidjbt=="15") {
                        $query .=" AND d.areaid IN (select DISTINCT areaId FROM sls.imr0 WHERE karyawanid='$pidcard' AND iCabangId='$pidcab') ";
                    }elseif ($pidjbt=="10" OR $pidjbt=="18") {
                        $query .=" AND d.areaid IN (select DISTINCT areaId FROM sls.ispv0 WHERE karyawanid='$pidcard' AND iCabangId='$pidcab') ";
                    }
                }

                $query .=" ORDER BY f.Nama, g.namalengkap";
                //echo $query;
                $tampil= mysqli_query($cnms, $query);
                $ketemu= mysqli_num_rows($tampil);
                
                $pnamalbljumlah="Jumlah ";
                if ((INT)$ketemu<=1) {
                    //$pdivnonejml=" class='divnone' ";
                    $pdivnonetr=" class='divnone' ";
                    $pnamalbljumlah="Jumlah";
                }
                    
                echo "<thead>";
                    echo "<th class='divnone'>&nbsp;</th>";
                    echo "<th class='divnone' $pdivnonearea>Area</th>";
                    echo "<th $pdivnoneuser>User</th>";
                    echo "<th class='divnone' $pdivnonelokasi>Lokasi Praktek</th>";
                    echo "<th $pdivnonejml>$pnamalbljumlah</th>";
                    echo "<th $pdivnonereal>Realisasi</th>";
                    echo "<th>Keterangan</th>";
                echo "</thead>";

                echo "<tbody>";
                    while ($row= mysqli_fetch_array($tampil)) {
                        $npidpraktek=""; //$row['idpraktek'];
                        $npnmcab="";//$row['nama_cabang'];
                        $npidarea=""; //$row['areaid'];
                        $npnmarea=""; //$row['nama_area'];
                        $npiddokt=$row['iddokter']; $npidpraktek=$npiddokt;
                        $npnmdokt=$row['nama_dokter']; 
                        $npidlokasiprkt=""; //$row['idoutlet'];
                        $npnmlokasiprkt=""; //$row['nama_outlet'];
                        
                        $pjumlahdetail="";
                        $prealdetail="";
                        
                        if (!empty($pidinput) && $pact=="editdata") {
                            $pjumlahdetail=$pjmlminta;
                            $princitotal=$pjmlminta;
                        }else{
                            if ((INT)$ketemu<=1) {
                                $pjumlahdetail=$pjmlminta;
                                $princitotal=$pjmlminta;
                            }
                        }
                        
                        
                        $chkbox = "<input type='checkbox' id='chk_kodeid[$npidpraktek]' name='chk_kodeid[]' value='$npidpraktek' checked>";
                        
                        echo "<tr>";
                        echo "<td class='divnone'>$chkbox</td>";
                        echo "<td class='divnone' nowrap $pdivnonearea>$npnmarea <input type='hidden' value='$npidarea' class='form-control' id='e_txtareaid[$npidpraktek]' name='e_txtareaid[$npidpraktek]' /></td>";
                        echo "<td nowrap $pdivnoneuser>$npnmdokt <input type='hidden' value='$npiddokt' class='form-control' id='e_txtdoktid[$npidpraktek]' name='e_txtdoktid[$npidpraktek]' /></td>";
                        echo "<td class='divnone' nowrap $pdivnonelokasi>$npnmlokasiprkt <input type='hidden' value='$npidlokasiprkt' class='form-control' id='e_txtotletid[$npidpraktek]' name='e_txtotletid[$npidpraktek]' /></td>";
                        echo "<td $pdivnonejml><input type='text' value='$pjumlahdetail' onblur=\"HitungTotalJumlahRp()\" class='form-control inputmaskrp2' id='e_txtrp[$npidpraktek]' name='e_txtrp[$npidpraktek]' /></td>";
                        echo "<td $pdivnonereal><input type='text' value='$prealdetail' class='form-control inputmaskrp2' id='e_txtrealrp[$npidpraktek]' name='e_txtrealrp[$npidpraktek]' value='' /></td>";
                        echo "<td><input type='text' value='$pketerangan' class='form-control' id='e_txtket[$npidpraktek]' name='e_txtket[$npidpraktek]' /></td>";
                        echo "</tr>";
                    }
                    
                    
                    if ((INT)$ketemu<=0) {
                        $pdivnonetr=" class='divnone' ";
                    }
                    echo "<tr $pdivnonetr>";
                    echo "<td class='divnone'></td>";
                    echo "<td class='divnone' nowrap $pdivnonearea></td>";
                    echo "<td nowrap $pdivnoneuser></td>";
                    echo "<td class='divnone' nowrap $pdivnonelokasi></td>";
                    echo "<td $pdivnonejml><input type='text' value='$princitotal' id='e_jmlusulan2' name='e_jmlusulan2' class='form-control col-md-7 col-xs-12 inputmaskrp2' Readonly></td>";
                    echo "<td $pdivnonereal></td>";
                    echo "<td>&nbsp;</td>";
                    echo "</tr>";
                echo "</tbody>";
            ?>
            
        </table>

    <style>
        .divnone {
            display: none;
        }
        #dtabel th {
            font-size: 13px;
        }
        #dtabel td { 
            font-size: 11px;
        }
    </style>
    <?PHP
    
    mysqli_close($cnms); exit;
}elseif ($pmodule=="cekdatasudahada") {
    echo "boleh"; exit;
}elseif ($pmodule=="x") {
    
}

?>