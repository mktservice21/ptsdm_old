<?PHP
include "config/koneksimysqli_ms.php";

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];

$piduser=$_SESSION['USERID']; 
$pidcard=$_SESSION['IDCARD'];
$pidjbt=$_SESSION['JABATANID']; 
$pidgroup=$_SESSION['GROUP']; 
$pnamalengkap=$_SESSION['NAMALENGKAP'];
    /*
    $pidcard="0000000823"; //0000001364
    $pnamalengkap="ANDELA KURNIAWAN"; //LURI DERMAWAN GAHO
    $pidjbt="08"; //10
    $pidgroup="6"; //6 = DM | 11 = AM SPV

    $pidcard="0000001364"; //0000000823
    $pnamalengkap="LURI DERMAWAN GAHO"; //ANDELA KURNIAWAN
    $pidjbt="10"; //08
    $pidgroup="11"; //6 = DM | 11 = AM SPV

    $pidcard="0000001941";
    $pnamalengkap="DWIKI RAMADHAN";
    $pidjbt="15";
    $pidgroup="7";
    */
$pidcabang="";        
$pareaid="";
$pregionid="";

//CARI ATASAN
    $pkdcoo="0000002403";
    $pnamacoo="EVI KOSINA SANTOSO";
    
    
    $query ="SELECT a.karyawanid, b.nama nama_karyawan, a.spv, c.nama nama_spv, 
        a.dm, d.nama nama_dm, a.sm, e.nama nama_sm, a.gsm, f.nama nama_gsm, 
        b.iCabangId as icabangid, b.areaId as areaid, b.jabatanId as jabatanid, a.region as region 
        FROM dbmaster.t_karyawan_posisi a 
        LEFT JOIN hrd.karyawan b on a.karyawanId=b.karyawanId 
        LEFT JOIN hrd.karyawan c on a.spv=c.karyawanId 
        LEFT JOIN hrd.karyawan d on a.dm=d.karyawanId 
        LEFT JOIN hrd.karyawan e on a.sm=e.karyawanId 
        LEFT JOIN hrd.karyawan f on a.gsm=f.karyawanId WHERE a.karyawanid='$pidcard'";
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
    
    $pcabangid_hrd=$nrs['icabangid'];
    $pareaid_hrd=$nrs['areaid'];
    $pjabatanid_hrd=$nrs['jabatanid'];
    $pregionid_psi=$nrs['region'];

    if ($pidjbt=="08" || (DOUBLE)$pidjbt==8) {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
    }

    if ($pidjbt=="20" || (DOUBLE)$pidjbt==20) {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
        $pkdsm="";
        $pnamasm="";
    }

    if ($pidjbt=="05" || (DOUBLE)$pidjbt==5 || $pidjbt=="22" || (DOUBLE)$pidjbt==22 || $pidjbt=="06" || (DOUBLE)$pidjbt==6) {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
        $pkdsm="";
        $pnamasm="";
        
        $pkdgsm=$pkdcoo;
        $pnamagsm=$pnamacoo;
    }

    if ($pidgroup=="8" || (DOUBLE)$pidgroup==8) {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
        $pkdsm="";
        $pnamasm="";
        
        $pkdgsm=$pkdcoo;
        $pnamagsm=$pnamacoo;
    }

// END CARI ATASAN
    
if (empty($pidjbt)) $pidjbt=$pjabatanid;



//CARI CABANG
$pidcabangho_eth="0000000001";
$filtercabang="";

    $query_cabang="";
    if ($pidgroup=="1" OR $pidgroup=="24") {
        $query_cabang = "select iCabangId as icabangid from mkt.icabang WHERE IFNULL(aktif,'')<>'N' ";
        $query_cabang .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
    }else{
        if ($pidjbt=="10" OR $pidjbt=="18") {
            $query_cabang = "select distinct icabangid as icabangid FROM mkt.ispv0 WHERE karyawanid='$pidcard'";
        }elseif ($pidjbt=="08") {
            $query_cabang = "select distinct icabangid as icabangid FROM mkt.idm0 WHERE karyawanid='$pidcard'";
        }elseif ($pidjbt=="20") {
            $query_cabang = "select distinct icabangid as icabangid FROM mkt.ism0 WHERE karyawanid='$pidcard'";
        }elseif ($pidjbt=="38") {
            $query_cabang = "select distinct icabangid as icabangid from hrd.rsm_auth where karyawanid='$pidcard'";
        }elseif ($pidjbt=="05" AND !empty($pregionid)) {
            $query_cabang = "select iCabangId as icabangid from mkt.icabang WHERE IFNULL(aktif,'')<>'N' ";
            $query_cabang .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
            $query_cabang .=" AND region='$pregionid'";
        }else{
            $query_cabang = "select distinct icabangid as icabangid FROM mkt.imr0 WHERE karyawanid='$pidcard'";
        }
    }
        
    if (!empty($query_cabang)) {
        
        $tampilcab= mysqli_query($cnmy, $query);
        $ketemucab=mysqli_num_rows($tampilcab);
        while ($rcab= mysqli_fetch_array($tampilcab)) {
            $nidcab=$rcab['icabangid'];
            
            $filtercabang .="'".$nidcab."',";
            
            if ((DOUBLE)$ketemucab==1) {
                $pidcabang=$nidcab;
            }else{
                if ($pidjbt=="10" || (DOUBLE)$pidjbt==10 || $pidjbt=="18" || (DOUBLE)$pidjbt==18 || $pidjbt=="08" || (DOUBLE)$pidjbt==8 || $pidjbt=="20" || (DOUBLE)$pidjbt==20 || $pidjbt=="38" || (DOUBLE)$pidjbt==30) {
                    if (empty($pidcabang) AND $pcabangid_hrd==$nidcab) {
                        $pidcabang=$nidcab;
                    }
                }else{
                    if ($pidjbt=="05" || (DOUBLE)$pidjbt==5) {
                        $pidcabang=$pidcabangho_eth;
                    }
                }
            }
            
        }
        if (!empty($filtercabang)) $filtercabang="(".substr($filtercabang, 0, -1).")";
    }
                                                
//END CARI CABANG



$pidinput="";
$piddivisi="";
$gsdsudoktit="";

$hari_ini = date("Y-m-d");
$tgl_pertama = date('F Y', strtotime($hari_ini));
$ptglajukan = date('d/m/Y', strtotime($hari_ini));

$pketerangan="";

$act="input";
if ($pidact=="editdata"){
    $act="update";

    include "config/fungsi_ubahget_id.php";
    
    $pidinput_ec=$_GET['id'];
    $pidinput = decodeString($pidinput_ec);
    
    $edit = mysqli_query($cnmy, "SELECT * FROM  WHERE ='$pidinput'");
    $jmlrw0=mysqli_num_rows($edit);
    
}


$phiddenatasan1="";
$phiddenatasan2="hidden";
if ($pidjbt=="05" OR $pidjbt=="22" OR $pidjbt=="06") {
    $phiddenatasan1="hidden";
    $phiddenatasan2="";
}else{
    if ($pidgroup=="8" OR (DOUBLE)$pidgroup==8) {
        $phiddenatasan1="hidden";
        $phiddenatasan2="";
    }
}
?>
<div class="">

    
    <!--row-->
    <div class="row">


        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>

                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                        id='form_data1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                        
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>



                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                

                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='text' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='text' id='e_idjbt' name='e_idjbt' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidjbt; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                    <div class='col-md-6'>
                                        <div class='input-group date' id='mytgl01_'>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglajukan; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type='text' id='e_namauser' name='e_namauser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamalengkap; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                          <select class='form-control input-sm' id='cb_divisi' name='cb_divisi' onchange="" data-live-search="true">
                                            <?PHP 
                                                $query = "select DivProdId as divisiid, nama as nama_divisi from mkt.divprod WHERE "
                                                        . " DivProdId IN ('EAGLE', 'PIGEO', 'PEACO')";
                                                $tampilket= mysqli_query($cnmy, $query);
                                                $ketemu=mysqli_num_rows($tampilket);
                                                if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";
                                                while ($du= mysqli_fetch_array($tampilket)) {
                                                    $niddiv=$du['divisiid'];
                                                    $nnmdiv=$du['nama_divisi'];
    
                                                    if ($niddiv==$piddivisi)
                                                        echo "<option value='$niddiv' selected>$nnmdiv</option>";
                                                    else
                                                    echo "<option value='$niddiv'>$nnmdiv</option>";
    
    
                                                    $cno++;
                                                }
                                            ?>
                                          </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                          <select class='form-control input-sm' id='cb_cabang' name='cb_cabang' onchange="ShowDataCabang()" data-live-search="true">
                                            <?PHP 
                                                if ($pidgroup=="1" OR $pidgroup=="24") {
                                                    $query = "select iCabangId as icabangid, nama as nama_cabang from mkt.icabang WHERE IFNULL(aktif,'')<>'N' ";
                                                    $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -', 'ETH -')";
                                                    $query .=" order by nama, iCabangId";
                                                }else{
                                                    if ($pidjbt=="10" OR $pidjbt=="18") {
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.ispv0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }elseif ($pidjbt=="08") {
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.idm0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }elseif ($pidjbt=="20") {
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.ism0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }else{
                                                        $query = "select distinct a.icabangid as icabangid, b.nama as nama_cabang 
                                                            FROM mkt.imr0 as a JOIN mkt.icabang as b on a.icabangid=b.iCabangId 
                                                            WHERE a.karyawanid='$pidcard'";
                                                            $query .=" order by b.nama, a.icabangid";
                                                    }
                                                }
                                                $tampilket= mysqli_query($cnmy, $query);
                                                $ketemu=mysqli_num_rows($tampilket);
                                                if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";
                                                while ($du= mysqli_fetch_array($tampilket)) {
                                                    $nidcab=$du['icabangid'];
                                                    $nnmcab=$du['nama_cabang'];
                                                    $nidcab_=(INT)$nidcab;
    
                                                    if ($nidcab==$pidcabang)
                                                        echo "<option value='$nidcab' selected>$nnmcab ($nidcab_)</option>";
                                                    else
                                                    echo "<option value='$nidcab'>$nnmcab ($nidcab_)</option>";
    
    
                                                    $cno++;
                                                }
                                            ?>
                                          </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='cb_area' name='cb_area' onchange="ShowDataDokter()">
                                            <option value='' selected>-- Pilihan --</option>
                                            <?PHP
                                            $query_ara="";
                                            if ( !empty($pidcabang) ) {
                                                if ($pidjbtpl=="15") {
                                                    $query_ara = "select distinct a.areaid as areaid, b.nama as nama_area FROM mkt.imr0 as a "
                                                            . " JOIN mkt.iarea as b on a.areaid=b.areaid and a.icabangid=b.icabangid "
                                                            . " WHERE a.karyawanid='$idajukan' "
                                                            . " and a.icabangid='$pcabangid' "
                                                            . " AND IFNULL(a.areaid,'')<>'' AND ( IFNULL(a.aktif,'')<>'N' OR a.areaid='$pareaid' ) "
                                                            . " AND ( IFNULL(b.aktif,'')<>'N' OR b.areaid='$pareaid' ) ";
                                                    $query_ara .=" ORDER BY b.nama";
                                                }elseif ($pidjbtpl=="10" OR $pidjbtpl=="18") {
                                                    $query_ara = "select distinct a.areaid as areaid, b.nama as nama_area FROM mkt.ispv0 as a "
                                                            . " JOIN mkt.iarea as b on a.areaid=b.areaid and a.icabangid=b.icabangid "
                                                            . " WHERE a.karyawanid='$idajukan' "
                                                            . " and a.icabangid='$pcabangid' "
                                                            . " AND IFNULL(a.areaid,'')<>'' AND ( IFNULL(a.aktif,'')<>'N' OR a.areaid='$pareaid' ) "
                                                            . " AND ( IFNULL(b.aktif,'')<>'N' OR b.areaid='$pareaid' ) ";
                                                    $query_ara .=" ORDER BY b.nama";
                                                }else{
                                                    $query_ara = "select areaid as areaid, nama as nama_area FROM mkt.iarea WHERE icabangid='$pidcabang' "
                                                            . " AND ( IFNULL(aktif,'')<>'N' OR areaid='$pareaid' )  ";
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
                                        <?PHP //echo $pfilarea; ?>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>User <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='cb_dokt' name='cb_dokt' onchange="ShowDataOutelt()">
                                            <?PHP
                                                echo "<option value='' selected>-- Pilih --</option>";
                                                if (!empty($pidcabang)) {
                                                    
                                                    $query = "SELECT DISTINCT d.iCabangId as icabangid, e.nama as nama_cabang, d.areaId as areaid, f.Nama as nama_area, 
                                                        a.iddokter, g.namalengkap as nama_dokter, g.spesialis, h.nama as nama_spesialis  
                                                        FROM ms2.tempatpraktek as a 
                                                        JOIN ms2.outlet_master as b on a.outletId=b.id 
                                                        LEFT JOIN ms2.outlet_type as c on b.type=c.id 
                                                        JOIN ms2.outlet_customer as d on a.outletId=d.outletId 
                                                        LEFT JOIN mkt.icabang as e on d.iCabangId=e.iCabangId 
                                                        LEFT JOIN mkt.iarea as f on d.iCabangId=f.iCabangId and d.areaId=f.areaId 
                                                        JOIN ms2.masterdokter as g on a.iddokter=g.id 
                                                        LEFT JOIN ms2.lookup as h on g.spesialis=h.id 
                                                        WHERE d.icabangid='$pidcabang' ";
                                                    if (!empty($pareaid)) {
                                                        $query .=" AND d.areaid='$pareaid' ";
                                                    }
                                                    $query .=" ORDER BY g.namalengkap, a.iddokter";
                                                    $tampil= mysqli_query($cnms, $query);
                                                    while ($row= mysqli_fetch_array($tampil)) {
                                                        $pniddokt=$row['iddokter'];
                                                        $pnnmdokt=$row['nama_dokter'];
                                                        
                                                        if ($pniddokt==$gsdsudoktit)
                                                            echo "<option value='$pniddokt' selected>$pnnmdokt - ($pniddokt)</option>";
                                                        else
                                                            echo "<option value='$pniddokt' >$pnnmdokt - ($pniddokt)</option>";
                                                    }
    
                                                }
                                            ?>
                                        </select>
                                        <?PHP //echo $pfilarea; ?>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Lokasi Praktek <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control input-sm' id='cb_outlet' name='cb_outlet' onchange="">
                                            <?PHP
                                                echo "<option value='' selected>-- Pilih --</option>";
                                                if (!empty($pidcabang)) {
                                                    
                                                    $query = "SELECT DISTINCT a.approve as approvepraktek, a.id as idpraktek, a.outletId as idoutlet, b.nama as nama_outlet, b.alamat,  
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
                                                        WHERE d.icabangid='$pidcabang' ";
                                                    if (!empty($pareaid)) {
                                                        $query .=" AND d.areaid='$pareaid' ";
                                                    }
                                                    $query .=" AND a.iddokter='$gsdsudoktit' ";
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
                                                        
                                                        if ($pnotlid==$gsouteltid)
                                                            echo "<option value='$pnotlid' selected>$pnotlnm - $pnotlid ($pnnamatype)</option>";
                                                        else
                                                            echo "<option value='$pnotlid' >$pnotlnm - $pnotlid ($pnnamatype)</option>";
                                                    }
    
                                                }
                                            ?>
                                        </select>
                                        <?PHP //echo $pfilarea; ?>
                                    </div>
                                </div>


                                <div id='div_akv'>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                        <div class='col-md-9'>
                                        <textarea class='form-control' id="e_keterangan" name='e_keterangan' maxlength="300"><?PHP echo $pketerangan; ?></textarea>
                                        </div>
                                    </div>

                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><u>Atasan</u> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        &nbsp;
                                    </div>
                                </div>
                                
                                <div id="div_atasan" <?PHP echo $phiddenatasan1; ?>>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SPV / AM <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='hidden' id='e_kdspv' name='e_kdspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdspv; ?>' Readonly>
                                            <input type='text' id='e_namaspv' name='e_namaspv' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamaspv; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>DM <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='hidden' id='e_kddm' name='e_kddm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkddm; ?>' Readonly>
                                            <input type='text' id='e_namadm' name='e_namadm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamadm; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='hidden' id='e_kdsm' name='e_kdsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdsm; ?>' Readonly>
                                            <input type='text' id='e_namasm' name='e_namasm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamasm; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>GSM <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='hidden' id='e_kdgsm' name='e_kdgsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdgsm; ?>' Readonly>
                                            <input type='text' id='e_namagsm' name='e_namagsm' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamagsm; ?>' Readonly>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <div id="div_atasan2" <?PHP echo $phiddenatasan2; ?>>
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <input type='hidden' id='e_kdcoo' name='e_kdcoo' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkdcoo; ?>' Readonly>
                                            <input type='text' id='e_namacoo' name='e_namacoo' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamacoo; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        *) Apabila atasan tidak sesuai, mohon untuk disesuaikan terlebih dahulu sebelum disimpan.
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <!--kanan-->
                    <div class='col-md-6 col-xs-12'>
                        
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                
                            </div>
                        </div>
                    </div>

                    
                    <div class='clearfix'></div>


                </form>

            </div>

        </div>


    </div>

</div>




<script>
    $(document).ready(function() {
        
        $('#cbln01').on('change dp.change', function(e){
            //ShowTanggalPilih();
        });
        
    });
    
    function ShowDataCabang() {
        ShowDataArea();
    }
    
    function ShowDataArea() {
        var eidcab =document.getElementById('cb_cabang').value;

        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewdataareacab",
            data:"uidcab="+eidcab,
            success:function(data){
                $("#cb_area").html(data);
                ShowDataDokter();
            }
        });
    }
    
    
    function ShowDataDokter() {
        var eidcab =document.getElementById('cb_cabang').value;
        var eidarea =document.getElementById('cb_area').value;
        
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewdatadokter",
            data:"uidcab="+eidcab+"&uidarea="+eidarea,
            success:function(data){
                $("#cb_dokt").html(data);
                ShowDataOutelt();
            }
        });
    }
    
    
    function ShowDataOutelt() {
        var eidcab =document.getElementById('cb_cabang').value;
        var eidarea =document.getElementById('cb_area').value;
        var eiddokt =document.getElementById('cb_dokt').value;
        
        $.ajax({
            type:"post",
            url:"module/budget/viewdatabgt.php?module=viewdataoutlet",
            data:"uidcab="+eidcab+"&uidarea="+eidarea+"&uiddokt="+eiddokt,
            success:function(data){
                $("#cb_outlet").html(data);
            }
        });
    }
    
</script>

<script type="text/javascript">
    
    $(document).ready(function() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var iact = urlku.searchParams.get("act");
        
       if (iact=="editdata") {
            setTimeout(function () {
                //ShowPeriode()
            }, 200);
       }
       
    } );
    
    
    

</script>

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

<style>

table {
    text-align: left;
    position: relative;
    border-collapse: collapse;
    background-color:#FFFFFF;
}

th {
    background: white;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>