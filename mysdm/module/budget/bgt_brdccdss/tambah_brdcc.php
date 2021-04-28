<?php
//$pnmtabelkry="karyawan";
$pnmtabelkry="tempkaryawandccdss_inp";
$pthn_edit="2020";
$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];


$hari_ini = date("Y-m-d");
$tglinput = date('d/m/Y', strtotime($hari_ini));

$ptahunbrinput = date('Y', strtotime($hari_ini));
$pbulanmulai = date('F Y', strtotime($hari_ini));

$pidbrid="";
$pidcabang="";
$pkaryawanid="";
$pmrid="";
$pdokteridmr="";
$pdivisiid="EAGLE";
$pcoa4="";
$pkodeid="";
$piddaerah="";

$pki_pilih="";
$pjangkawaktu="";
$pjangkawaktu_fin="";

$paktivitas1="";
$paktivitas2="";

$pccyid="IDR";
$pjnspajak="N";

$pkenapajak="";
$pnoseripajak="";
$ptglfakturpajak="";
$pjmldpp="";
$pjmldpp="";

$pjmlppn="";
$pjmlrpppn="";

$pjnspph="";
$pjmlpph="";
$pjmlrppph="";
$pjmlbulat="";
$pjmlmaterai="";
$pjenisdpp="";
$prpjumlahjasa=0;

$pchkjasa="";
$pchkatrika="";

$rprelalisasi="";
$pchkjenisreal1="";
$pchkjenisreal2="checked";
$pnmreal_readonly="";
$prelasijenis="";

$rpjumlah="";
$pjmlreadonly="";
$rpcn="";

$pnoslip="";
$plampiran="";
$pcapil="";
$pviapil="";

$act="input";
if ($pidact=="editdata"){
    $act="update";
    
    include "config/fungsi_ubahget_id.php";
    
    $pidinput_ec=$_GET['id'];
    $pidbrid = decodeString($pidinput_ec);
    
    $ncarisudahclosebrid=CariSudahClosingBRID1($pidbrid, "A");
    $query ="SELECT tgl, tgltrans, brId as brid, icabangid, karyawanId as karyawanid, mrid, "
            . " dokterId as dokterid, divprodid, COA4 as coa4, kode, "
            . " idcabang, bulan_mulai, stsbr, jangka_waktu, jangka_waktu_fin, "
            . " aktivitas1, aktivitas2, ccyId as ccyid, "
            . " pajak, nama_pengusaha, noseri, tgl_fp, "
            . " dpp, ppn, pph_jns, ppn_rp, pph, pph_rp, pembulatan, materai_rp, jenis_dpp, jasa_rp, "
            . " jumlah, realisasi1, realisasi2, cn, noslip, lampiran, ca, via "
            . " FROM hrd.br0 WHERE brId='$pidbrid'";
    //echo $query;
    $pedit = mysqli_query($cnmy, $query);
    $row    = mysqli_fetch_array($pedit);
    
    $tglinput = date('d F Y', strtotime($row['tgl']));
    $tglinput = date('d/m/Y', strtotime($row['tgl']));
    $ptahunbrinput = date('Y', strtotime($row['tgl']));
    
    if (empty($row['tgltrans']) OR $row['tgltrans']=="0000-00-00"){
        $tgltrans = "";
    }else{
        $tgltrans = date('d F Y', strtotime($row['tgltrans']));
        $tgltrans = date('d/m/Y', strtotime($row['tgltrans']));
    }
    
    
    $pidcabang=$row['icabangid'];
    $pkaryawanid=$row['karyawanid'];
    $pmrid=$row['mrid'];
    $pdokteridmr=$row['dokterid'];
    $pdivisiid=$row['divprodid'];
    $pcoa4=$row['coa4'];
    $pkodeid=$row['kode'];
    $piddaerah=$row['idcabang'];
    $rpjumlah=$row['jumlah'];
    $rprelalisasi=$row['realisasi1'];
    $rpcn=$row['cn'];
    
    $pnoslip=$row['noslip'];
    $plampiran=$row['lampiran'];
    $pcapil=$row['ca'];
    $pviapil=$row['via'];
    
    $pbulanmulai=$row['bulan_mulai'];
    $pki_pilih=$row['stsbr'];
    $pjangkawaktu=$row['jangka_waktu'];
    $pjangkawaktu_fin=$row['jangka_waktu_fin'];
    
    if ($pbulanmulai=="0000-00-00") $pbulanmulai="";
    if (!empty($pbulanmulai)) $pbulanmulai = date('F Y', strtotime($pbulanmulai));
    
    
    $paktivitas1=$row['aktivitas1'];
    $paktivitas2=$row['aktivitas2'];
    
    $pccyid=$row['ccyid'];
    $pjnspajak=$row['pajak'];
    
    $pkenapajak=$row['nama_pengusaha'];
    $pnoseripajak=$row['noseri'];
    if (!empty($row['tgl_fp']) AND $row['tgl_fp']<>"0000-00-00")
        $ptglfakturpajak = date('d/m/Y', strtotime($row['tgl_fp']));

    if ($row['tgl_fp']=="0000-00-00") $ptglfakturpajak = "";

    $pjmldpp=$row['dpp'];
    $pjmlppn=$row['ppn'];
    $pjmlrpppn=$row['ppn_rp'];

    $pjnspph=$row['pph_jns'];
    $pjmlpph=$row['pph'];
    $pjmlrppph=$row['pph_rp'];
    $pjmlbulat=$row['pembulatan'];
    $pjmlmaterai=$row['materai_rp'];
    $pjenisdpp=$row['jenis_dpp'];

    $prpjumlahjasa=$row['jasa_rp'];
    if (empty($prpjumlahjasa)) $prpjumlahjasa=0;


    $pchkjasa="";
    $pchkatrika="";
    if ($pjenisdpp=="A") {
        $pchkjasa="checked";
    }elseif ($pjenisdpp=="B") {
        $pchkatrika="checked";
    }
    
    
    
    
    $prelasijenis=TRIM($row['realisasi2']);
    
    if (!empty($pdokteridmr)) {
        $prel2_tojenis=$row['idkontak'];
        if (empty($prel2_tojenis)) $prel2_tojenis="0";
            
        $query = "select nama as nama from hrd.dokter WHERE dokterid='$pdokteridmr'";
        $tampild= mysqli_query($cnmy, $query);
        $nrd= mysqli_fetch_array($tampild);
        $pnamadokter=$nrd['nama'];
        
        if ($prel2_tojenis!="0") {
            $pchkjenisreal1="checked";
            $pchkjenisreal2="";
            
            $pnmreal_readonly=" Readonly ";
        }
    }
    
    
    
    
    
    if ((INT)$ptahunbrinput<=(INT)$pthn_edit) {
        $pjmlreadonly="Readonly";
    }
    
}

$query = "select jabatanId from hrd.karyawan where karyawanId='$pkaryawanid'"; 	
$result = mysqli_query($cnmy, $query);
$records = mysqli_num_rows($result);
$row = mysqli_fetch_array($result);
$pjabatanid = $row['jabatanId'];

?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>
<div class='modal fade' id='myModal2' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_nobr").focus(); } </script>

<div class="">
    
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=$act&idmenu=$pidmenu"; ?>' 
              id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_nobr' name='e_nobr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbrid; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Tanggal </label>
                                    <div class='col-md-9'>
                                        <div class='input-group date' id='mytgl01'>
                                            <input type="text" class="form-control" id='mytgl01' name='e_tglinput' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tglinput; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Cabang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='e_idcabang' name='e_idcabang' onchange="showYangMembuat()">
                                            <?PHP
                                              //
                                              if ($pact=="editdata" AND (INT)$ptahunbrinput<=(INT)$pthn_edit) {
                                                  if (empty($pidcabang)) echo "<option value='' selected>-- Pilihan --</option>";
                                                  $query = "select iCabangId, nama from MKT.icabang WHERE iCabangId='$pidcabang' order by nama";
                                              }else{
                                                  echo "<option value='' selected>-- Pilihan --</option>";
                                                  $query = "select iCabangId, nama from MKT.icabang WHERE (aktif='Y' OR iCabangId='$pidcabang') order by nama";
                                              }
                                              $tampil = mysqli_query($cnmy, $query);
                                              while($a=mysqli_fetch_array($tampil)){
                                                  $nidcab=$a['iCabangId'];
                                                  $nnmcab=$a['nama'];

                                                  if ($nidcab==$pidcabang)
                                                      echo "<option value='$nidcab' selected>$nnmcab</option>";
                                                  else
                                                      echo "<option value='$nidcab'>$nnmcab</option>";
                                              }
                                              ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='e_idkaryawan' name='e_idkaryawan' onchange="pilihShowDariYgMembuat()">

                                            <?PHP
                                            if ($pact=="editdata") {
                                                if (empty($pkaryawanid)) echo "<option value='' selected>-- Pilihan --</option>";
                                                if ((INT)$ptahunbrinput<=(INT)$pthn_edit) {
                                                    $query = "select distinct b.karyawanId, b.nama, b.jabatanid, b.icabangid from hrd.karyawan as b where b.karyawanid='$pkaryawanid' "; 
                                                }else{
                                                    if (($pidcabang=='0000000030') or ($pidcabang=='0000000031') or ($pidcabang=='0000000032')) {
                                                        $query = "select distinct b.karyawanId, b.nama, b.jabatanid, b.icabangid from hrd.karyawan b where (b.karyawanId='0000000154' or b.karyawanId='0000000159') AND b.aktif = 'Y' "; 
                                                    }else{
                                                        $query = "select distinct b.karyawanId, b.nama, b.jabatanid from hrd.$pnmtabelkry b where b.icabangid='$pidcabang' AND b.aktif = 'Y' "; 
                                                    }
                                                }
                                            }else{
                                                echo "<option value='' selected>-- Pilihan --</option>";

                                                $query ="SELECT DISTINCT b.karyawanId, b.nama FROM hrd.$pnmtabelkry b WHERE 1=1 ";
                                                $query .= " AND ( IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00' ) ";
                                            }
                                            $query .= " AND b.karyawanid not in ('0000002083') ";
                                            $query .=" AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ')  "
                                            . " and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
                                            . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-') "
                                            . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ') ";

                                            $query .= " OR b.karyawanid='$pkaryawanid' ";
                                            $query .= " order by b.nama, b.karyawanId";

                                            $tampil = mysqli_query($cnmy, $query);
                                            while($a=mysqli_fetch_array($tampil)){
                                            $nidkry=$a['karyawanId'];
                                            $nnmkry=$a['nama'];

                                            if ($nidkry==$pkaryawanid)
                                                echo "<option value='$nidkry' selected>$nnmkry</option>";
                                            else
                                                echo "<option value='$nidkry'>$nnmkry</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>MR <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_mr' name='cb_mr' onchange="pilihShowDariMr('e_idcabang', 'cb_mr')">
                                              
                                            <?PHP
                                                if ((INT)$ptahunbrinput<=(INT)$pthn_edit) {
                                                    if (empty($pmrid)) echo "<option value='' selected>-- Pilihan --</option>";
                                                }else{
                                                    echo "<option value='' selected>-- Pilihan --</option>";
                                                }
                                                if ($pact=="editdata") {
                                                  
                                                    if ($pidcabang=="0000000001") { //ho
                                                        $querykry = "select distinct b.karyawanId, b.nama, b.areaId from hrd.$pnmtabelkry b WHERE b.aktif = 'Y' "; 
                                                    }else{
                                                        if (($pidcabang=="0000000030") or ($pidcabang=='0000000031') or ($pidcabang=='0000000032')){ // irian, ambon, ntt
                                                            $querykry = "select distinct b.karyawanId, b.nama, b.areaId from hrd.$pnmtabelkry b where b.icabangid='$pidcabang' AND b.aktif = 'Y' ";
                                                        }else{

                                                            if (($pjabatanid=="18") or ($pjabatanid=="10")) { //spv,am
                                                                $querykry = "select distinct b.karyawanId, b.nama, b.areaId from hrd.$pnmtabelkry b where (b.atasanId='$pkaryawanid' or b.atasanId2='$pkaryawanid') ";
                                                            }

                                                            if ($pjabatanid=="08") { //dm
                                                                $querykry = "select distinct b.karyawanId, b.nama, b.areaId from hrd.$pnmtabelkry b where b.iCabangId='$pidcabang' "; 
                                                            }
                                                            if ($pjabatanid=="15") { // mr
                                                                $querykry = "select distinct b.karyawanId, b.nama, b.areaId from hrd.$pnmtabelkry b where b.karyawanId='$pkaryawanid' "; 
                                                            }

                                                        }
                                                    }

                                                    if (empty($querykry)) {
                                                        $querykry = "select distinct b.karyawanId, b.nama, b.areaId from hrd.$pnmtabelkry b WHERE b.icabangid='$pidcabang' AND b.aktif = 'Y' AND b.jabatanid IN ('15') "
                                                                . " AND ( IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00' )"; 
                                                        $querykry .= " AND b.karyawanid not in (select distinct IFNULL(karyawanid,'') FROM dbmaster.t_karyawanadmin) ";
                                                    }
                                                    $query .= " AND b.jabatanId not in ('19') ";

                                                    $querykry .= " AND b.karyawanid not in ('0000002083') ";
                                                    //$querykry .= " AND LEFT(b.nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ') ";
                                                    //$querykry .= " and LEFT(b.nama,3) NOT IN ('DR ', 'DR-', 'JKT', 'NN-') ";
                                                    $querykry .=" and LEFT(b.nama,7) NOT IN ('NN DM - ')  "
                                                            . " and LEFT(b.nama,3) NOT IN ('TO.', 'TO-') "
                                                            . " AND LEFT(b.nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ') ";
                                                    $querykry .= " OR b.karyawanid='$pmrid' ";
                                                    $querykry .=" order by b.nama";
                                                    
                                                    if ((INT)$ptahunbrinput<=(INT)$pthn_edit) {
                                                        $querykry = "select distinct b.karyawanId, b.nama, b.areaId from hrd.karyawan as b WHERE b.karyawanid='$pmrid' ";
                                                    }
                                                    
                                                    $tampil = mysqli_query($cnmy, $querykry);
                                                    while($a=mysqli_fetch_array($tampil)){
                                                        $nidkrymr=$a['karyawanId'];
                                                        $nnmkrymr=$a['nama'];

                                                        if ($nidkrymr==$pmrid)
                                                            echo "<option value='$nidkrymr' selected>$nnmkrymr</option>";
                                                        else
                                                            echo "<option value='$nidkrymr'>$nnmkrymr</option>";
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dokter <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='hidden' id='e_namadokter' name='e_namadokter' class='form-control col-md-7 col-xs-12' value="<?PHP echo $pnamadokter; ?>" Readonly>
                                        <select class='soflow' id='e_iddokter' name='e_iddokter' onchange="CariNamaDokter()">
                                                
                                            <?PHP
                                            if ($pact=="editdata") {
                                                $psudahpilih=false;
                                                if ((INT)$ptahunbrinput<=(INT)$pthn_edit) {
                                                    if (empty($pdokteridmr)) echo "<option value='' selected>-- Pilihan --</option>";
                                                    $query="select distinct dokterId, nama from hrd.dokter WHERE dokterid='$pdokteridmr' ";
                                                }else{

                                                    echo "<option value='' selected>-- Pilihan --</option>";

                                                    $pfilerkry="";
                                                    if (empty($pmrid) OR $pmrid==$pkaryawanid) {
                                                        if (!empty($mkrybuat)) $pfilerkry="'".$mkrybuat."',";
                                                        $query = "select karyawanid from MKT.imr0 WHERE icabangid='$icabangid'";
                                                        $tampila= mysqli_query($cnmy, $query);
                                                        while ($nra= mysqli_fetch_array($tampila)) {
                                                            $pikry=$nra['karyawanid'];
                                                            $pfilerkry .="'".$pikry."',";
                                                        }

                                                        if (!empty($pfilerkry)) $pfilerkry="(".substr($pfilerkry, 0, -1).")";
                                                    }



                                                    $filter_kry_dok=" and karyawan.karyawanId='$pmrid' ";
                                                    if (!empty($pkaryawanid)) {
                                                        $filter_kry_dok=" AND ( karyawan.karyawanId='$pmrid' OR karyawan.karyawanId='$pkaryawanid' ) ";
                                                    }


                                                    if (!empty($pfilerkry)) {
                                                        $filter_kry_dok = " AND karyawan.karyawanId IN $pfilerkry ";
                                                    }



                                                    if ($pidcabang=="0000000001") {
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
                                                                          where (dokter.nama <> '' $filter_kry_dok ) OR dokter.dokterId='$pdokteridmr' 
                                                                          order by dokter.nama";
                                                    }

                                                }
                                                $tampil=mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $ciddok=$a['dokterId'];
                                                    $cnmdok=$a['nama'];

                                                    $piddopl=$ciddok;
                                                    if(!empty($ciddok)) $piddopl=(INT)$ciddok;

                                                    if ($ciddok==$pdokteridmr) {
                                                        echo "<option value='$ciddok' selected>$cnmdok ($piddopl)</option>";
                                                        $psudahpilih=true;
                                                    }else
                                                        echo "<option value='$ciddok'>$cnmdok ($piddopl)</option>";
                                                }

                                                if ($psudahpilih==false) echo "<option value='' selected>-- Pilihan --</option>";

                                            }else{
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Divisi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_divisi' name='cb_divisi' onchange="showCOANya()"><!--showKodeNyaNon('cb_divisi', 'cb_kode')-->
                                            <?PHP
                                            if ($pact=="editdata" AND (INT)$ptahunbrinput<=(INT)$pthn_edit) {
                                                if (empty($pdivisiid)) echo "<option value='' selected>-- Pilihan --</option>";
                                                $query = "SELECT DivProdId as divprodid, nama as nama FROM mkt.divprod where DivProdId='$pdivisiid'";
                                            }else{
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                $query = "SELECT DivProdId as divprodid, nama as nama FROM mkt.divprod where br='Y' AND DivProdId NOT IN ('CAN', 'OTHER', 'OTC') OR DivProdId='$pdivisiid' order by nama";
                                            }
                                            $tampil=mysqli_query($cnmy, $query);
                                            
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                $ndivid=$a['divprodid'];
                                                $ndivnm=$a['nama'];
                                                
                                                if ($ndivid==$pdivisiid)
                                                    echo "<option value='$ndivid' selected>$ndivnm</option>";
                                                else
                                                    echo "<option value='$ndivid'>$ndivnm</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_coa'>Kode / COA <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_coa' name='cb_coa' onchange="showKodeNyaNon()">
                                            
                                        <?PHP
                                        if ($pact=="editdata") {
                                            if (empty($pcoa4)) echo "<option value='' selected>-- Pilihan --</option>";
                                            if ((INT)$ptahunbrinput<=(INT)$pthn_edit) {
                                                $query = "select COA4, NAMA4 from dbmaster.coa_level4 WHERE COA4='$pcoa4'";
                                            }else{
                                                $filternondssdccCOA=" and (bk.br <> '' and bk.br<>'N') ";

                                                $query = "SELECT w.id, w.karyawanId, k.nama, w.COA4, c4.NAMA4,
                                                    bk.br, bk.divprodid FROM dbmaster.coa_wewenang AS w
                                                    LEFT JOIN hrd.karyawan AS k ON w.karyawanId = k.karyawanId
                                                    LEFT JOIN dbmaster.coa_level4 AS c4 ON w.COA4 = c4.COA4
                                                    LEFT JOIN dbmaster.br_kode AS bk ON c4.kodeid = bk.kodeid WHERE 
                                                    bk.divprodid='$pdivisiid' $filternondssdccCOA";
                                            }
                                            $tampil=mysqli_query($cnmy, $query);
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                $nidcoa4=$a['COA4'];
                                                $nnmcoa4=$a['NAMA4'];
                                                
                                                if ($nidcoa4==$pcoa4)
                                                    echo "<option value='$nidcoa4' selected>$nidcoa4 - $nnmcoa4</option>";
                                                else
                                                    echo "<option value='$nidcoa4'>$nidcoa4 - $nnmcoa4</option>";
                                            }
                                        }else{
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_kode'>Kode <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='cb_kode' name='cb_kode'>
                                            
                                            <?PHP
                                            if ($pact=="editdata") {
                                                if ((INT)$ptahunbrinput<=(INT)$pthn_edit) {
                                                    if (empty($pkodeid)) echo "<option value='' selected>-- Pilihan --</option>";
                                                    $query = "select kodeid,nama,divprodid from dbmaster.br_kode where kodeid='$pkodeid'";
                                                }else{
                                                    $query = "select kodeid,nama,divprodid from dbmaster.br_kode where (divprodid='$pdivisiid' and br <> '')  "
                                                        . " and (divprodid='$pdivisiid' and br<>'N') order by nama";
                                                }
                                                $tampil = mysqli_query($cnmy, $query);
                                                while($a=mysqli_fetch_array($tampil)){ 
                                                    $nidkode=$a['kodeid'];
                                                    $nnmkode=$a['nama'];
                                                    $nnmdivid=$a['divprodid'];
                                                
                                                    if ($nidkode==$pkodeid)
                                                        echo "<option value='$nidkode' selected>$nnmkode - $nidkode ($nnmdivid)</option>";
                                                    else
                                                        echo "<option value='$nidkode'>$nnmkode - $nidkode ($nnmdivid)</option>";
                                                }
                                            }else{
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Daerah <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='e_iddaerah' name='e_iddaerah' onchange="">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                                $query = "select DISTINCT a.idcabang as idcabang, a.nama as nama from MKT.cbgytd as a "
                                                        . " LEFT JOIN dbmaster.cabangytd as b on a.idcabang=b.idcabang "
                                                        . " WHERE "
                                                        . " (a.aktif='Y' OR a.idcabang='$piddaerah') AND b.icabangid='$pidcabang' "
                                                        . " order by a.nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                $ketemu = mysqli_num_rows($tampil);
                                                
                                                if ((DOUBLE)$ketemu<=0) {
                                                    $query = "select a.idcabang as idcabang, a.nama as nama from ms.cbgytd as a WHERE a.aktif='Y' order by a.nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    $ketemu = mysqli_num_rows($tampil);
                                                }
                                                
                                                while($a=mysqli_fetch_array($tampil)){
                                                    $niddaer=$a['idcabang'];
                                                    $nnmdaer=$a['nama'];
                                                    
                                                    if ($niddaer==$piddaerah)
                                                        echo "<option value='$niddaer' selected>$nnmdaer</option>";
                                                    else
                                                        echo "<option value='$niddaer'>$nnmdaer</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <?PHP $chkpilihki=""; if ($pki_pilih=="KI") $chkpilihki="checked"; ?>
                                        <label><input type="checkbox" value="KI" name="chk_ki" id="chk_ki" <?PHP echo $chkpilihki; ?> onclick="CariDataKI()"> KI </label> &nbsp;&nbsp;&nbsp;
                                        <span hidden><a class='btn btn-default btn-xs' href='#' onclick="HilangkanDataKI()">Hilangkan</a></span>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jangka Waktu / Bulan <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='number' id='e_jangkawaktu' name='e_jangkawaktu' class='form-control col-md-7 col-xs-12' value="<?PHP echo $pjangkawaktu; ?>" >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><span style="color:blue;">Jangka Waktu / Bulan (FINANCE)</span> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='number' id='e_fin_jangkawaktu' name='e_fin_jangkawaktu' class='form-control col-md-7 col-xs-12' value="<?PHP echo $pjangkawaktu_fin; ?>" >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Bulan Mulai KI </label>
                                    <div class='col-md-9'>
                                        <div class='input-group date' id='cbln01'>
                                            <input type="text" class="form-control" id='e_blnmulai' name='e_blnmulai' autocomplete='off' required='required' placeholder='MMMM yyyy' value='<?PHP echo $pbulanmulai; ?>' >
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas'>Aktivitas <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas' name='e_aktivitas' rows='3' placeholder='Aktivitas'><?PHP echo $paktivitas1; ?></textarea>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_aktivitas2'> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id='e_aktivitas2' name='e_aktivitas2' rows='3' placeholder='Keterangan Detail'><?PHP echo $paktivitas2; ?></textarea>
                                    </div>
                                </div>
                                
                                
                                
                                
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    <!-- end kiri -->
                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Mata Uang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' name='cb_jenis'>
                                            <?php
                                            $tampil=mysqli_query($cnmy, "SELECT ccyId as ccyid, nama FROM dbmaster.ccy");
                                            while($c=mysqli_fetch_array($tampil)){
                                                $nidccy=$c['ccyid'];
                                                $nnmccy=$c['nama'];
                                                    
                                                if ($nidccy==$pccyid)
                                                    echo "<option value='$nidccy' selected>$nidccy - $nnmccy</option>";
                                                else {
                                                    if ($nidccy=="IDR")
                                                        echo "<option value='$nidccy' selected>$nidccy - $nnmccy</option>";
                                                    else    
                                                        echo "<option value='$nidccy'>$nidccy - $nnmccy</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jenis'>Pajak <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div style="margin-bottom:2px;">
                                            <select class='soflow' name='cb_pajak' id='cb_pajak' onchange="ShowPajak()">
                                                <?php
                                                if ($pjnspajak=="Y") {
                                                    echo "<option value='N'>N</option>";
                                                    echo "<option value='Y' selected>Y</option>";
                                                }else{
                                                    echo "<option value='N' selected>N</option>";
                                                    echo "<option value='Y'>Y</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                

                                <div id="n_pajak1">
                                    
                                    
                                    <div  class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Pengusaha Kena Pajak <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_kenapajak' name='e_kenapajak' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pkenapajak; ?>'>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">No Seri Faktur Pajak <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_noserifp' name='e_noserifp' class='form-control col-md-7 col-xs-12' autocomplete="off" value='<?PHP echo $pnoseripajak; ?>'>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Tgl Faktur Pajak </label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <div class='input-group date' id='mytgl01'>
                                                <input type="text" class="form-control" id='mytgl05' name='e_tglpajak' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglfakturpajak; ?>'>
                                                <span class='input-group-addon'>
                                                    <span class='glyphicon glyphicon-calendar'></span>
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                    
                                    <!--- untuk jasa -->
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">&nbsp;<span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type="checkbox" value="jasa" id="chk_jasa" name="chk_jasa" onclick="cekBoxPilihDPP('chk_jasa')" <?PHP echo $pchkjasa; ?>> DPP Dari Jumlah Awal
                                            <br/>
                                            <input type="checkbox" value="atrika" id="chk_atrika" name="chk_atrika" onclick="cekBoxPilihDPP('chk_atrika')" <?PHP echo $pchkatrika; ?>> DPP Khusus (Atrika, dll)
                                        </div>
                                    </div>
                                    
                                    <div id="n_pajakjasa">
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;"><u>Jumlah Awal (Rp.)</u> <span class='required'></span></label>
                                            <div class='col-md-6 col-sm-6 col-xs-12'>
                                                <input type='text' id='e_rpjmljasa' name='e_rpjmljasa' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $prpjumlahjasa; ?>' onblur="HitungJumlahDPP()">
                                            </div><!--disabled='disabled'-->
                                        </div>
                                        
                                    </div>
                                    <!--- END untuk jasa -->
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">DPP (Rp.) <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmldpp' name='e_jmldpp' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmldpp; ?>' onblur="HitungJumlah()">
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">PPN (%) <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmlppn' name='e_jmlppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlppn; ?>' onblur="HitungPPN()">
                                            <input type='hidden' id='e_jmlrpppn' name='e_jmlrpppn' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrpppn; ?>' Readonly>
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">PPH <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <div style="margin-bottom:2px;">
                                                <select class='soflow' name='cb_pph' id='cb_pph' onchange="ShowPPH()">
                                                    <?php
                                                    $ketPPH21="PPH21 (DPP*5%*50%) atau (JML AWAL*5%*50%)";
                                                    $ketPPH23="PPH23 (DPP*2%) atau (JML AWAL*2%)";
                                                    
                                                    $ketPPH22="PPH21 (DPP*6%*50%) atau (JML AWAL*6%*50%)";
                                                    
                                                    if ($pjnspph=="pph21") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21' selected>$ketPPH21</option>";
                                                        echo "<option value='pph23'>$ketPPH23</option>";
                                                        echo "<option value='pph22'>$ketPPH22</option>";
                                                    }elseif ($pjnspph=="pph23") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21'>$ketPPH21</option>";
                                                        echo "<option value='pph23' selected>$ketPPH23</option>";
                                                        echo "<option value='pph22'>$ketPPH22</option>";
                                                    }elseif ($pjnspph=="pph22") {
                                                        echo "<option value=''></option>";
                                                        echo "<option value='pph21'>$ketPPH21</option>";
                                                        echo "<option value='pph23'>$ketPPH23</option>";
                                                        echo "<option value='pph22' selected>$ketPPH22</option>";
                                                    }else{
                                                        echo "<option value='' selected></option>";
                                                        echo "<option value='pph21'>$ketPPH21</option>";
                                                        echo "<option value='pph23'>$ketPPH23</option>";
                                                        echo "<option value='pph22'>$ketPPH22</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <input type='hidden' id='e_jmlpph' name='e_jmlpph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlpph; ?>' readonly>
                                                <input type='hidden' id='e_jmlrppph' name='e_jmlrppph' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrppph; ?>' Readonly>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Pembulatan <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmlbulat' name='e_jmlbulat' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlbulat; ?>' onblur="HitungJumlahUsulan()">
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:blue;">Biaya Materai (Rp.) <span class='required'></span></label>
                                        <div class='col-md-6 col-sm-6 col-xs-12'>
                                            <input type='text' id='e_jmlmaterai' name='e_jmlmaterai' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlmaterai; ?>' onblur="HitungJumlahUsulan()">
                                        </div><!--disabled='disabled'-->
                                    </div>
                                    
                                </div>


                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_jmlusulan' name='e_jmlusulan' class='form-control col-md-7 col-xs-12 inputmaskrp2' value="<?PHP echo $rpjumlah; ?>" <?PHP echo $pjmlreadonly; ?> >
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jenis Realisasi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div style="margin-bottom:2px;">
                                            <input type="radio" id="chksesuai" name="rb_jenisreal" value="1" <?PHP echo $pchkjenisreal1; ?> onclick="CekDataRealisasi()"> Sesuai Nama Dokter &nbsp;
                                            <input type="radio" id="chkrelasi" name="rb_jenisreal" value="0" <?PHP echo $pchkjenisreal2; ?> onclick="CekDataRealisasi()"> Relasi Dokter &nbsp;
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <button type='button' class='btn btn-default btn-xs' onclick='CariNamaDokter()'>Realisasi</button> 
                                        <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='hidden' id='e_realisasix' name='e_realisasix' class='form-control col-md-7 col-xs-12' value="<?PHP echo $rprelalisasi; ?>" <?PHP echo $pnmreal_readonly; ?> >
                                        
                                        <input list="namarealisasi" id="e_realisasi" name="e_realisasi" autocomplete='off' class='form-control col-md-7 col-xs-12' value="<?PHP echo $rprelalisasi; ?>">
                                            <datalist id="namarealisasi">
                                                <?PHP
                                                if ($pact=="editdata") {
                                                    $query = "select distinct realisasi1 as nmrealisasi from hrd.br0 WHERE dokterid='$pdokteridmr' AND IFNULL(dokterid,'') NOT IN ('', '0', '(blank)')";
                                                    $tampild= mysqli_query($cnmy, $query);
                                                    while ($nrd= mysqli_fetch_array($tampild)) {
                                                        $pnamareal=$nrd['nmrealisasi'];

                                                        echo "<option value='$pnamareal'>";
                                                    }
                                                }
                                                ?>
                                        </datalist>
                                    </div>
                                </div>
                                
                                <div id="n_jnsrelasi">
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                            Relasi (istri /suami /anak /dsb.)
                                            <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='text' id='e_nmrealasi' name='e_nmrealasi' class='form-control col-md-7 col-xs-12' value="<?PHP echo $prelasijenis; ?>" >
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div hidden>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CN <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_cn' name='e_cn' class='form-control col-md-7 col-xs-12 inputmaskrp2' value="<?PHP echo $rpcn; ?>" readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Slip <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_noslip' name='e_noslip' class='form-control col-md-7 col-xs-12' value="<?PHP echo $pnoslip; ?>">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl02'>Tanggal Transfer </label>
                                    <div class='col-md-9'>
                                        <div class='input-group date' id='mytgl02'>
                                            <input type="text" class="form-control" id='e_tgltrans' name='e_tgltrans' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $tgltrans; ?>'>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <?PHP $chklam=""; if ($plampiran=="Y") $chklam="checked"; ?>
                                            <?PHP $chkca=""; if ($pcapil=="Y") $chkca="checked"; ?>
                                            <?PHP $chkvia=""; if ($pviapil=="Y") $chkvia="checked"; ?>
                                            <label><input type="checkbox" value="lapiran" name="cx_lapir" <?PHP echo $chklam; ?>> Lampiran </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="ca" name="cx_ca" <?PHP echo $chkca; ?>> PC-M </label> &nbsp;&nbsp;&nbsp;
                                            <label><input type="checkbox" value="via" name="cx_via" <?PHP echo $chkvia; ?>> Via Surabaya </label>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
										
                                            <?PHP
                                            if ($pact=="editdata") {
                                                if ($ncarisudahclosebrid==true) {
                                                    echo "<span style='color:red;'>BR tersebut sudah closing SURABAYA tidak bisa diubah....</span>";
                                                    echo "<a class='btn btn-default' href='?module=$pmodule&idmenu=$pidmenu&act=$pidmenu'>Back</a>";
                                                }else{
                                                    echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm('edit', 'Edit Data... ?')\">Update</button>";
                                                    echo "<a class='btn btn-default' href='?module=$pmodule&idmenu=$pidmenu&act=$pidmenu'>Back</a>";
                                                }
                                            }else{
                                                echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm('simpan', 'Simpan ?')\">Save</button>";
                                                echo "<a class='btn btn-default' href='?module=$pmodule&idmenu=$pidmenu&act=$pidmenu'>Back</a>";
                                            }
                                            ?>
											
											
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    <!-- end kanan -->
                    
                    
                    
                    
                    
                </div>
            </div>
            
            
        </form>
        
        
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_content'>
                <div class='x_panel'>
                    <b>Data yang terakhir diinput (max 5 data)</b>
                    <table id='datatable' class='table table-striped table-bordered' width='100%'>
                        <thead>
                            <tr>
                                <th></th><th width='60px'>No ID</th>
                                <th width='60px'>Tanggal</th><th width='60px'>Tgl. Transfer</th><th>Keterangan</th>
                                <th width='80px'>Yg Membuat</th><th width='50px'>Jumlah</th><th width='50px'>Realisasi</th>
                                <th width='50px'>Nm Realisasi</th><th>Kode</th><th>No Slip</th>

                            </tr>
                        </thead>
                        <body>
                            <?PHP
                            
                            $sql = "SELECT a.brId, DATE_FORMAT(a.tgl,'%d %M %Y') as tgl, DATE_FORMAT(a.tgltrans,'%d %M %Y') as tgltrans, "
                                    . " DATE_FORMAT(a.tgltrm,'%d %M %Y') as tgltrm, "
                                    . " b.nama as nama_kode, FORMAT(a.jumlah,2,'de_DE') as jumlah, "
                                    . " FORMAT(a.jumlah1,2,'de_DE') as jumlah1, a.realisasi1, "
                                    . " a.dokterId, c.nama as nama_dokter, "
                                    . " FORMAT(a.cn,2,'de_DE') as cn, "
                                    . " a.noslip, a.aktivitas1 ";
                            $sql.=" FROM hrd.br0 as a ";
                            $sql.="  LEFT JOIN hrd.br_kode as b on a.kode=b.kodeid ";
                            $sql.="  LEFT JOIN hrd.dokter as c on a.dokterid=c.dokterId ";
                            $sql.=" WHERE 1=1 and (a.user1=$piduser or a.user1='$pidcard') ";
                            $sql.=" and (b.br <> '' and b.br<>'N') ";
                            $sql.=" and a.brId not in (select distinct ifnull(brId,'') from hrd.br0_reject) ";
                            $sql.=" order by a.brId desc limit 5 ";
                            //echo $sql;
                            $tampil=mysqli_query($cnmy, $sql);
                            while ($xc=  mysqli_fetch_array($tampil)) {
                                $fnoid=$xc["brId"];
                                $faksi = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$fnoid'>Edit</a>"
                                        . "<button class='btn btn-danger btn-xs'"
                                        . "onClick=\"ProsesDataHapusSatu('hapus', '$fnoid', '')\">Hapus</button>";
                                $ftgl = $xc["tgl"];
                                $ftgltrans = $xc["tgltrans"];
                                $ftgltrm = $xc["tgltrm"];
                                $fket1 = $xc["aktivitas1"];
                                $fnamakry = "<a href='#' title=".$xc['nama_cabang'].">".$xc["nama"]."</a>";
                                $fjuml = $xc["jumlah"];
                                $fjuml1 = $xc["jumlah1"];
                                $freal = $xc["realisasi1"];
                                $fnoslip = $xc["noslip"];
                                $fnamakode = $xc["nama_kode"];
                                echo "<tr>";
                                echo "<td>$faksi</td>";
                                echo "<td>$fnoid</td>";
                                echo "<td>$ftgl</td>";
                                echo "<td>$ftgltrans</td>";
                                echo "<td>$fket1</td>";
                                echo "<td>$fnamakry</td>";
                                echo "<td>$fjuml</td>";
                                echo "<td>$fjuml1</td>";
                                echo "<td>$freal</td>";
                                echo "<td>$fnamakode</td>";
                                echo "<td>$fnoslip</td>";
                                echo "</tr>";
                            }
                            ?>
                        </body>
                    </table>

                </div>
            </div>
        </div>
        
        
        
    </div>
    
</div>


<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
</style>

<script>
    
    $(document).ready(function() {
        ShowPajak();
        CekDataRealisasi();
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var idact = urlku.searchParams.get("act");
        
        //baru kalau salah delete aja
        if (idact=="tambahbaru") {
            showCOANya();
        }
        //END baru kalau salah delete aja
        
        //baru kalau salah delete aja
        if (idact=="editdata") {
            var epajak = document.getElementById('cb_pajak').value;
            if (epajak=="" || epajak=="N"){
            }else{
                cekBoxPilihDPP('chk_jasa');
                cekBoxPilihDPP('chk_atrika');
            }
        }
        //END baru kalau salah delete aja
        
        
        var table = $('#datatable').DataTable({
            fixedHeader: true,
            "ordering": false,
            "columnDefs": [
                { "visible": false },
                { className: "text-right", "targets": [6,7] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 6, 7, 8,9,10] }//nowrap

            ],
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
        
        
        
    } );
    
</script>


<script>
    
    function showYangMembuat() {
        var icab = document.getElementById('e_idcabang').value;
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewdatakrybuat",
            data:"ucab="+icab,
            beforeSend: function () {
                //$('select[name=e_idkaryawan]').attr('disabled',true)
                $('select[name=e_idkaryawan]').empty();
                $('select[name=e_idkaryawan]').append('<option value="loading" disabled selected>Loading...</option>');
            },
            success:function(data){
                $("#e_idkaryawan").html(data);
            },
            complete: function () {
                //$('select[name=e_idkaryawan]').attr('disabled',false)
                $('select[name=e_idkaryawan] option[value="loading"]').text('');
            },
            error: function () {
                //$('select[name=e_idkaryawan]').attr('disabled',false)
                $('select[name=e_idkaryawan] option[value="loading"]').text('');
                alert('Something wrong. Try Again!')                
            }
        });
    }
    
    function pilihShowDariYgMembuat() {
        showMRKaryawan();
    }
    
    function pilihShowDariMr(ecabang, ucar) {
        showDokterMR(ecabang, ucar);
    }
    
    function showMRKaryawan() {
        
        var ikryid = document.getElementById('e_idkaryawan').value;
        var icab = document.getElementById('e_idcabang').value;
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewdatamridkary",
            data:"ukryid="+ikryid+"&ucab="+icab,
            beforeSend: function () {
                //$('select[name=cb_mr]').attr('disabled',true)
                $('select[name=cb_mr]').empty();
                $('select[name=cb_mr]').append('<option value="loading" disabled selected>Loading...</option>');
            },
            success:function(data){
                $("#cb_mr").html(data);
                showDataDaerah();
            },
            complete: function () {
                //$('select[name=cb_mr]').attr('disabled',false)
                $('select[name=cb_mr] option[value="loading"]').text('');
            },
            error: function () {
                //$('select[name=cb_mr]').attr('disabled',false)
                $('select[name=cb_mr] option[value="loading"]').text('');
                alert('Something wrong. Try Again!')                
            }
        });
    }
    
    function showDokterMR(ecabang, ucar) {
        var icabang = document.getElementById(ecabang).value;
        var icar = document.getElementById(ucar).value;
        var icar2 = document.getElementById('cb_mr').value;
        var ekrybuat = document.getElementById('e_idkaryawan').value;
        
        if (icar=="") {
            icar = ekrybuat;
        }

        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewdoktermr&data1="+icar,
            data:"umr="+icar+"&ucab="+icabang+"&ukrybuat="+ekrybuat+"&ucar2="+icar2,
            beforeSend: function () {
                //$('select[name=e_iddokter]').attr('disabled',true)
                $('select[name=e_iddokter]').empty();
                $('select[name=e_iddokter]').append('<option value="loading" disabled selected>Loading...</option>');
            },
            success:function(data){
                $("#e_iddokter").html(data);
                showDataDaerah();
            },
            complete: function () {
                //$('select[name=e_iddokter]').attr('disabled',false)
                $('select[name=e_iddokter] option[value="loading"]').text('');
            },
            error: function () {
                //$('select[name=e_iddokter]').attr('disabled',false)
                $('select[name=e_iddokter] option[value="loading"]').text('');
                alert('Something wrong. Try Again!')                
            }
        });
    }
    
    
    function showCOANya(){
        var ediv = document.getElementById('cb_divisi').value;
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewdatacombocoa",
            data:"udiv="+ediv,
            beforeSend: function () {
                //$('select[name=e_iddokter]').attr('disabled',true)
                $('select[name=cb_coa]').empty();
                $('select[name=cb_coa]').append('<option value="loading" disabled selected>Loading...</option>');
            },
            success:function(data){
                $("#cb_coa").html(data);
                $("#cb_kode").html("<option value=''>--Pilihan--</option>");
            },
            complete: function () {
                //$('select[name=e_iddokter]').attr('disabled',false)
                $('select[name=cb_coa] option[value="loading"]').text('');
            },
            error: function () {
                //$('select[name=e_iddokter]').attr('disabled',false)
                $('select[name=cb_coa] option[value="loading"]').text('');
                alert('Something wrong. Try Again!')                
            }
        });
    }
    
    function showKodeNyaNon(){
        var ediv = document.getElementById('cb_divisi').value;
        var ecoa = document.getElementById('cb_coa').value;

        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewdatacombokodenon",
            data:"udiv="+ediv+"&ucoa="+ecoa,
            beforeSend: function () {
                //$('select[name=e_iddokter]').attr('disabled',true)
                $('select[name=cb_kode]').empty();
                $('select[name=cb_kode]').append('<option value="loading" disabled selected>Loading...</option>');
            },
            success:function(data){
                $("#cb_kode").html(data);
            },
            complete: function () {
                //$('select[name=e_iddokter]').attr('disabled',false)
                $('select[name=cb_kode] option[value="loading"]').text('');
            },
            error: function () {
                //$('select[name=e_iddokter]').attr('disabled',false)
                $('select[name=cb_kode] option[value="loading"]').text('');
                alert('Something wrong. Try Again!')                
            }
        });
    }
    
    
    function showDataDaerah() {
        var icab = document.getElementById('e_idcabang').value;
        var ikry = document.getElementById('e_idkaryawan').value;
        var imr = document.getElementById('cb_mr').value;
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewdatadaerahcab",
            data:"ucab="+icab+"&ukry="+ikry+"&umr="+imr,
            beforeSend: function () {
                //$('select[name=e_iddokter]').attr('disabled',true)
                $('select[name=e_iddaerah]').empty();
                $('select[name=e_iddaerah]').append('<option value="loading" disabled selected>Loading...</option>');
            },
            success:function(data){
                $("#e_iddaerah").html(data);
            },
            complete: function () {
                //$('select[name=e_iddokter]').attr('disabled',false)
                $('select[name=e_iddaerah] option[value="loading"]').text('');
            },
            error: function () {
                //$('select[name=e_iddokter]').attr('disabled',false)
                $('select[name=e_iddaerah] option[value="loading"]').text('');
                alert('Something wrong. Try Again!')                
            }
        });
    }
     
</script>


<script>
    
    function ShowPajak(){
        var myurl = window.location;
        var urlku = new URL(myurl);
        var nact = urlku.searchParams.get("act");
        
        var epajak = document.getElementById('cb_pajak').value;

        if (epajak=="" || epajak=="N"){
            n_pajak1.style.display = 'none';
        }else{
            n_pajak1.style.display = 'block';
            if (nact!="editdata") {
                ShowInputJasa();
            }else{//baru kalau salah delete aja
                cekBoxPilihDPP('chk_jasa');
                cekBoxPilihDPP('chk_atrika');
            }
        }
    }
    
    function cekBoxPilihDPP(nmcekbox){
        var nm = document.getElementById(nmcekbox);
        var chkjasa = document.getElementById('chk_jasa');
        var chkatrika = document.getElementById('chk_atrika');
        if (nm.checked) {
            if (nm.value=="jasa") {
                chkatrika.checked='';
            }else if (nm.value=="atrika") {
                chkjasa.checked='';
            }
        }
        ShowInputJasa();
    }
    
    function ShowInputJasa(){
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        if (echkjasa==true || echkatrika==true) {
            n_pajakjasa.style.display = 'block';
        }else{
            n_pajakjasa.style.display = 'none';
        }
        HitungJumlah();
    }
    
    
    function HitungJumlahDPP(){
        var newchar = '';
        var e_totrpdpp = "0";
        erpjmldpp = document.getElementById("e_rpjmljasa").value;
        if (erpjmldpp!="" && erpjmldpp != "0") {
            var nrpjmldpp = erpjmldpp; 
            nrpjmldpp = nrpjmldpp.split(',').join(newchar);
            e_totrpdpp=nrpjmldpp*10/100;
        }
        document.getElementById("e_jmldpp").value=e_totrpdpp;
        HitungJumlah();
    }
    
    
    function HitungJumlah(){
        HitungPPN();
        HitungPPH();
        HitungJumlahUsulan();
    }

    function HitungPPN(){
        var newchar = '';
        var e_totrpppn = "0";

        ejmldpp = document.getElementById("e_jmldpp").value;
        if (ejmldpp!="" && ejmldpp != "0") {
            var njmldpp = ejmldpp; 
            njmldpp = njmldpp.split(',').join(newchar);

            eppn = document.getElementById("e_jmlppn").value;
            if (eppn!="" && eppn != "0") {
                var nppn = eppn; 
                nppn = nppn.split(',').join(newchar);

                e_totrpppn = njmldpp * nppn / 100;
            }

        }

        document.getElementById("e_jmlrpppn").value = e_totrpppn;
        HitungPPH();
    }

    function ShowPPH(){
        document.getElementById("e_jmlpph").value = "0";
        document.getElementById("e_jmlpph").value = "0";
        document.getElementById("e_jmlrppph").value = "0";
        

        
        var epph = document.getElementById("cb_pph").value;
        if (epph=="pph21") {
            document.getElementById("e_jmlpph").value = "5";
            HitungPPH();
        }else if (epph=="pph23") {
            document.getElementById("e_jmlpph").value = "2";
            HitungPPH();
        }else if (epph=="pph22") {
            document.getElementById("e_jmlpph").value = "6";
            HitungPPH();
        }else{
            document.getElementById("e_jmlpph").value = "0";
            document.getElementById("e_jmlrppph").value = "0";
            HitungJumlahUsulan();
        }
    }
    
    
    function HitungPPH(){
        var newchar = '';
        
        
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        var erpjmljasa="0";
        if (echkjasa==true || echkatrika==true) {
            erpjmljasa=document.getElementById("e_rpjmljasa").value;
            var nrpjmljasa = erpjmljasa;
            erpjmljasa = nrpjmljasa.split(',').join(newchar);
            if (erpjmljasa=="") { erpjmljasa="0" }
        }
        
        
        
        var e_totrppph = "0";
        var epph = document.getElementById("cb_pph").value;
        
        if (epph!="") {
            ejmldpp = document.getElementById("e_jmldpp").value;
            if (ejmldpp!="" && ejmldpp != "0") {
                var njmldpp = ejmldpp; 
                njmldpp = njmldpp.split(',').join(newchar);

                
                var idpp_pilih=njmldpp;
                if (echkatrika==true) {
                    idpp_pilih=erpjmljasa;
                }
                
                e_totrppph = idpp_pilih;
                
                if (epph=="pph21") {
                    npph = "5";
                    e_totrppph = (idpp_pilih * npph / 100)*50/100;   
                }else if (epph=="pph23") {
                    npph = "2";
                    e_totrppph = (idpp_pilih * npph / 100);
                }else if (epph=="pph22") {
                    npph = "6";
                    e_totrppph = (idpp_pilih * npph / 100)*50/100;
                }
            }
        }
        document.getElementById("e_jmlrppph").value = e_totrppph;
        HitungJumlahUsulan();
    }


    function HitungJumlahUsulan(){

        var newchar = '';
        
        var echkjasa = document.getElementById("chk_jasa").checked;
        var echkatrika = document.getElementById("chk_atrika").checked;
        var erpjmljasa="0";
        if (echkjasa==true || echkatrika==true) {
            erpjmljasa=document.getElementById("e_rpjmljasa").value;
            var nrpjmljasa = erpjmljasa;
            erpjmljasa = nrpjmljasa.split(',').join(newchar);
            if (erpjmljasa=="") { erpjmljasa="0" }
        }
        
        
        ejmldpp = document.getElementById("e_jmldpp").value;
        var e_totrpusulan = ejmldpp;
        erpppn = document.getElementById("e_jmlrpppn").value;
        erppph = document.getElementById("e_jmlrppph").value;
        erpbulat = document.getElementById("e_jmlbulat").value;
        erpmaterai = document.getElementById("e_jmlmaterai").value;
        if (erpppn=="") erpppn="0";
        if (erppph=="") erppph="0";
        if (erpbulat=="") erpbulat="0";
        if (erpmaterai=="") erpmaterai="0";

        var epph = document.getElementById("cb_pph").value;

        var njmldpp = ejmldpp; 
        njmldpp = njmldpp.split(',').join(newchar);

        var nrpppn = erpppn; 
        nrpppn = nrpppn.split(',').join(newchar);

        var nrppph = erppph; 
        nrppph = nrppph.split(',').join(newchar);

        var nrpbulat = erpbulat; 
        nrpbulat = nrpbulat.split(',').join(newchar);

        var nrpmaterai = erpmaterai; 
        nrpmaterai = nrpmaterai.split(',').join(newchar);
        
        var idpp_pilih=njmldpp;
        /*if (echkjasa==true) {
            idpp_pilih=erpjmljasa;
        }*/
        
        if (epph=="pph21" || epph=="pph23" || epph=="pph22") {
            e_totrpusulan=( ( parseFloat(idpp_pilih)+parseFloat(nrpppn) - parseFloat(nrppph) ) );
        }else{
            e_totrpusulan=( ( parseFloat(idpp_pilih)+parseFloat(nrpppn)));
        }
        e_totrpusulan=parseFloat(e_totrpusulan)+parseFloat(nrpbulat)+parseFloat(nrpmaterai);
        
        
        
        if (echkjasa==true) {
            e_totrpusulan=parseFloat(e_totrpusulan);//-parseFloat(njmldpp)
            e_totrpusulan=parseFloat(erpjmljasa)+parseFloat(e_totrpusulan);
        }else if (echkatrika==true) {
            e_totrpusulan=parseFloat(e_totrpusulan)-parseFloat(njmldpp);
            e_totrpusulan=parseFloat(erpjmljasa)+parseFloat(e_totrpusulan);
        }
        
        
        //document.getElementById("e_jmlusulan").value = e_totrpusulan;
        document.getElementById("e_jmlusulan").value = parseFloat(e_totrpusulan).toFixed(2);
		
    }
</script>


<script>
    
    function CariNamaDokter() {
        var iiddok = document.getElementById('e_iddokter').value;
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewdatanamadokterlist",
            data:"uiddok="+iiddok,
            success:function(data){
                $("#namarealisasi").html(data);
                //$("#e_realisasi").html(data);
            }
        });
    }
    
    function CekDataRealisasi() {
        var chkjns1=document.getElementById("chksesuai").checked;
        if (chkjns1==true) {
            n_jnsrelasi.style.display = 'none';
        }else{
            n_jnsrelasi.style.display = 'block';
        }
    }
    
    
    function CariDataKI() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var idact = urlku.searchParams.get("act");
        var iakv1=document.getElementById("e_aktivitas").value;
        
        var chkki=document.getElementById("chk_ki").checked;
        if (chkki==true) {
            var iket1=document.getElementById("e_aktivitas").value;
            
            if (idact=="editdata" && iket1 != "") {
            }else{
                if (iakv1=="") {
                    document.getElementById("e_aktivitas").value="Kerjasama Ilmiah";
                }
            }
        }
    }
    
    
    function HilangkanDataKI() {
        document.getElementById("e_jangkawaktu").value="";
        document.getElementById("e_blnmulai").value="";
        document.getElementById("e_aktivitas").focus();
    }
    
    
</script>