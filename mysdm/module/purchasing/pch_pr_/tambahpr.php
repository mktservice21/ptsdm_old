<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    
    
$pidbr="";
$pidkodeinput="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));
$ptglajukan = date('d/m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));

$pbulanpilih = date('F Y', strtotime($hari_ini));


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

$query = "select karyawanid from dbpurchasing.t_pr_admin WHERE karyawanid='$pidcardpl'";
$tampil= mysqli_query($cnmy, $query);
$ketemu= mysqli_num_rows($tampil);
if ((INT)$ketemu>0) {
    $pkaryawaninpilih=true;
}

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

//ATASAN

    $query ="SELECT a.karyawanid, b.nama nama_karyawan, a.spv, c.nama nama_spv, 
        a.dm, d.nama nama_dm, a.sm, e.nama nama_sm, a.gsm, f.nama nama_gsm, 
        b.icabangid as icabangid, b.areaid as areaid, b.jabatanid as jabatanid, a.icabangid as icabangid_posisi 
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
    $pcabidpilihposisi=$nrs['icabangid_posisi'];
    $pcabidpilihposisi2=$nrs['icabangid'];
    $pareaidpilih=$nrs['areaid'];

// END ATASAN


if ($pdivisilogin=="OTC" OR $pdivisilogin=="CHC") {
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
            $pcabangid=$pcabidpilihposisi2;
        }
        
    }else{
        if ($pidjbtpl=="38") $pdivisiid="HO";//ADMIN CABANG
        else{
            if ($pidjbtpl<>"15") {
                $pdivisiid=$_SESSION['DIVISI'];
                if (empty($pfilcabang) AND empty($pcabangid)) $pcabangid="0000000001";
            }
        }
    }
    //END CARI DIVISI

}//END STATUS LOGIN

//CARI DEPARTEMEN dan PENGAJUAN
$pdepartmen="";
if ($pstatuslogin=="OTC" OR $pstatuslogin=="CHC" OR $pstatuslogin=="ETH") {
    $pdepartmen="MKT";
}else{
    if ($pstatuslogin=="HO") {
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


$pareaid="";

$pidtipe="101";

$untukpil0="";
$untukpil1="";
$untukpil2="";
$untukpil3="";

if ($pstatuslogin=="OTC" OR $pstatuslogin=="CHC") {
    $untukpil2="selected";
    $pareaid=$pareaidpilih;
}else{
    if ($pstatuslogin=="ETH") {
        $untukpil1="selected";
    }else{
        if ($pstatuslogin=="HO") {
            $untukpil3="selected";
        }else{
            $untukpil0="selected";
        }
    }
}

$pketerangan="";
$psudahtampil="";
$ptotjml="";

$pjmlrec=0;

$sudahapv="";

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pmyact=$_GET['act'];
$pact=$_GET['act'];

$act="input";
if ($pact=="editdata"){
    include "config/fungsi_ubahget_id.php";
    
    $act="update";
    $pidbr_ec=$_GET['id'];
    $pidbr = decodeString($pidbr_ec);
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbpurchasing.t_pr_transaksi WHERE idpr='$pidbr'");
    $r    = mysqli_fetch_array($edit);
    $ptglajukan = date('d/m/Y', strtotime($r['tanggal']));
    $pidtipe=$r['idtipe'];
    $idajukan=$r['karyawanid'];
    $pcabangid=$r['icabangid'];
    $pareaid=$r['areaid'];
    $pketerangan=$r['aktivitas'];
    $pdivisiid=$r['divisi'];
    $pidjbtpl=$r['jabatanid'];
    $pdepartmen=$r['iddep'];
    $ppengajuanid=$r['pengajuan'];
    
    
    if ($pstatuslogin=="HO") {
        if ($ppengajuanid=="OTC" OR $ppengajuanid=="CHC") {
            $untukpil0="";
            $untukpil1="";
            $untukpil2="selected";
            $untukpil3="";
        }elseif ($ppengajuanid=="ETH") {
            $untukpil0="";
            $untukpil1="selected";
            $untukpil2="";
            $untukpil3="";
        }elseif ($ppengajuanid=="HO") {
            $untukpil0="";
            $untukpil1="";
            $untukpil2="";
            $untukpil3="selected";
        }
    }
    
    $patasan1=$r['atasan1'];
    $patasan2=$r['atasan2'];
    $patasan3=$r['atasan3'];
    $patasan4=$r['atasan4'];
    
    $ptglatasan1=$r['tgl_atasan1'];
    $ptglatasan2=$r['tgl_atasan2'];
    $ptglatasan3=$r['tgl_atasan3'];
    $ptglatasan4=$r['tgl_atasan4'];
    
    if ($ptglatasan1=="0000-00-00 00:00:00") $ptglatasan1="";
    if ($ptglatasan2=="0000-00-00 00:00:00") $ptglatasan2="";
    if ($ptglatasan3=="0000-00-00 00:00:00") $ptglatasan3="";
    if ($ptglatasan4=="0000-00-00 00:00:00") $ptglatasan4="";
    
    
    $philangkanhapus=true;
    if (empty($patasan1) AND empty($patasan2) AND empty($patasan3) AND empty($patasan4)) {
        $philangkanhapus=false;
    }elseif (empty($patasan1) AND empty($patasan2) AND empty($patasan3) AND !empty($patasan4)) {
        if (empty($ptglatasan4)) $philangkanhapus=false;
    }elseif (empty($patasan1) AND empty($patasan2) AND !empty($patasan3)) {
        if (empty($ptglatasan3)) $philangkanhapus=false;
        if (!empty($patasan4) AND !empty($ptglatasan4)) $philangkanhapus=true;
    }elseif (empty($patasan1) AND !empty($patasan2)) {
        if (empty($ptglatasan2)) $philangkanhapus=false;
        if (!empty($patasan3) AND !empty($ptglatasan3)) $philangkanhapus=true;
    }elseif (!empty($patasan1)) {
        if (empty($ptglatasan1)) $philangkanhapus=false;
        if (!empty($patasan2) AND !empty($ptglatasan2)) $philangkanhapus=true;
    }
    
    if ($ppengajuanid=="HO") {
        $pkdspv="";
        $pnamaspv="";
        $pkddm="";
        $pnamadm="";
        $pkdsm="";
        $pnamasm="";
        
        $pkdgsm=$patasan4;
        $pnamagsm = getfield("select nama as lcfields from hrd.karyawan WHERE karyawanid='$patasan4'");
    }else{
        $query ="SELECT cb.idpr, cb.karyawanid, b.nama nama_karyawan, cb.atasan1 spv, c.nama nama_spv, cb.atasan2 as dm, d.nama nama_dm, 
            cb.atasan3 as sm, e.nama nama_sm, cb.atasan4 as gsm, f.nama nama_gsm 
            FROM dbpurchasing.t_pr_transaksi cb
            LEFT JOIN dbmaster.t_karyawan_posisi a on cb.karyawanid=a.karyawanid
            LEFT JOIN hrd.karyawan b on cb.karyawanId=b.karyawanId 
            LEFT JOIN hrd.karyawan c on cb.atasan1=c.karyawanId 
            LEFT JOIN hrd.karyawan d on cb.atasan2=d.karyawanId 
            LEFT JOIN hrd.karyawan e on cb.atasan3=e.karyawanId 
            LEFT JOIN hrd.karyawan f on cb.atasan4=f.karyawanId WHERE cb.idpr='$pidbr'";
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
    }
    
    //ADMIN BR
    if ($pidgroup=="40" OR $pidgroup=="23" OR $pidgroup=="26") {
        if (empty($ptglfin)) $philangkanhapus=false;
    }
    
    $query ="SELECT idpr_d FROM dbpurchasing.t_pr_transaksi_d WHERE idpr='$pidbr'";
    $itampil= mysqli_query($cnmy, $query);
    $pjmlrec= mysqli_num_rows($itampil);
    $pjmlrec=(DOUBLE)$pjmlrec+1;
    
    $psudahtampil="1";
    $ptotjml="1";
    
}

$pbukaarea="hidden";
$pnamagsmhos="GSM";
if ($pdivisilogin=="OTC" OR $pdivisilogin=="CHC") {
    $pnamagsmhos="HOS";
    $pbukaarea="";
}elseif ($pdivisilogin=="HO") {
    $pnamagsmhos="Atasan";
    if ($pidjbtpl=="10" OR $pidjbtpl=="18" OR $pidjbtpl=="08" OR $pidjbtpl=="20" OR $pidjbtpl=="05") {
        $pnamagsmhos="GSM";
    }
}


//CARI AREA
$pfilarea="";
$query_area="";

if ($pdivisilogin=="OTC" OR $pdivisilogin=="CHC") {
    
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

                if ((INT)$ketemua==1 AND $pact<>"editdata") {
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


if ($ppenecualianatasan==true) {
    $pnamagsmhos="Atasan";
    if ($pdivisilogin=="OTC" OR $pdivisilogin=="CHC") $pareaid="0000000001";
}

?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>


<div class="">

    <!--row-->
    <div class="row">
        
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='demo-form2' name='form1' data-parsley-validate 
              class='form-horizontal form-label-left'>
        
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $pmodule; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $pidmenu; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                
                <div class='x_panel'>
                    
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                        <input type='hidden' id='e_idcardlogin' name='e_idcardlogin' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcardpl; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id='mytgl01_'>
                                            <input type="text" class="form-control" id='e_tglberlaku' name='e_tglberlaku' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglajukan; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tipe <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_tipeaju' name='cb_tipeaju' onchange="ShowDataTipe()" data-live-search="true">
                                            <?PHP
                                                $query = "select idtipe as idtipe, nama_tipe as nama_tipe from dbpurchasing.t_pr_tipe WHERE IFNULL(aktif,'')<>'N'";
                                                $tampil=mysqli_query($cnmy, $query);
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $npidtipe=$row['idtipe'];
                                                    $npnmtipe=$row['nama_tipe'];
                                                    
                                                    if ($npidtipe==$pidtipe)
                                                        echo "<option value='$npidtipe' selected>$npnmtipe</option>";
                                                    else
                                                        echo "<option value='$npidtipe'>$npnmtipe</option>";
                                                        
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div  class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pengajuan <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <select class='form-control input-sm' id='cb_untuk' name='cb_untuk' onchange="ShowPengajuanUntuk()" data-live-search="true">
                                            <?PHP
                                            if ($pstatuslogin=="HO") {
                                                echo "<option value='' $untukpil0>--Pilihan--</option>";
                                                echo "<option value='ETH' $untukpil1>MKT. ETHICAL</option>";
                                                echo "<option value='OTC' $untukpil2>MKT. CHC</option>";
                                                echo "<option value='HO' $untukpil3>HO</option>";
                                            }else{
                                                if ($pstatuslogin=="OTC" OR $pstatuslogin=="CHC") {
                                                    if (empty($untukpil2)) echo "<option value='' selected>--Pilihan--</option>";
                                                    echo "<option value='OTC' $untukpil2>MKT. CHC</option>";
                                                }else{
                                                    if (empty($untukpil1)) echo "<option value='' selected>--Pilihan--</option>";
                                                    echo "<option value='ETH' $untukpil1>MKT. ETHICAL</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pembuat <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' onchange="ShowDataKaryawan();" data-live-search="true">
                                              
                                              <?PHP 
                                                if ($pkaryawaninpilih==true) {
                                                    echo "<option value='' selected>-- Pilihan --</option>";
                                                    
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
                                                            //$query .= " AND divisiId NOT IN ('OTC', 'CHC') ";
                                                        $query .= " ) ";
                                                    $query .= " OR karyawanId='$idajukan' ) ";
                                                    $query .= " ORDER BY nama";
                                                }else{
                                                    $query = "select karyawanId as karyawanid, nama as nama_karyawan From hrd.karyawan WHERE 1=1 ";
                                                    $query .= " AND (karyawanid ='$pidcardpl' OR karyawanid ='$idajukan') ";
                                                }
                                                
                                                $tampil = mysqli_query($cnmy, $query);
                                                $ketemu= mysqli_num_rows($tampil);

                                                if ((DOUBLE)$ketemu<=0 AND $pkaryawaninpilih==false) echo "<option value='' selected>-- Pilihan --</option>";

                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pkaryid=$z['karyawanid'];
                                                    $pkarynm=$z['nama_karyawan'];
                                                    $pkryid=(INT)$pkaryid;
                                                    if ($pkaryid==$idajukan)
                                                        echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                    else
                                                        echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                <div id="div_datakaryawan">
                                    
                                    
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
                                                if ($pdivisilogin=="OTC" OR $pdivisilogin=="CHC") {
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
                                                        if ($pidgroup<>"24" AND $pidgroup<>"1" AND ($pidjbtpl=="10" OR $pidjbtpl=="10" OR $pidjbtpl=="18" OR $pidjbtpl=="08" OR $pidjbtpl=="20" OR $pidjbtpl=="05") ) {
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
                                                if ($pdivisilogin=="OTC" OR $pdivisilogin=="CHC") {
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
                                                if ($pdivisilogin=="OTC" OR $pdivisilogin=="CHC") {
                                                    if (!empty($pcabangid)) {
                                                        $query_ara = "select icabangid_o as icabangid, areaid_o as areaid, nama as nama_area from mkt.iarea_o "
                                                                . " WHERE icabangid_o='$pcabangid' AND IFNULL(aktif,'')<>'N' ";
                                                        $query_ara .=" ORDER BY nama, areaid_o";
                                                    }
                                                }else{
                                                    if ( (!empty($pcabangid) OR !empty($pfilarea)) ) {
                                                        if (empty($pfilarea)) $pfilarea="('$pareaid')";
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
                                            <?PHP //echo $pfilarea; ?>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Departemen <span class='required'></span></label>
                                        <div class='col-xs-5'>
                                            <select class='form-control input-sm' id='cb_dept' name='cb_dept' onchange="">
                                                <?PHP
                                                if ($pstatuslogin=="OTC" OR $pstatuslogin=="CHC" OR $pstatuslogin=="ETH") {
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
                                    
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Notes <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_notes' name='e_notes' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>'>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type="button" class='btn btn-warning btn-xs'  name="btn_refresh" id="btn_refresh" onclick="ShowDataAtasan()" value="Refresh Atasan.."><!--refresh_atasan()-->
                                    </div>
                                </div>
                            
                                
                                <div id="div_atasan">
                                    
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
                                    
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        *) apabila atasan tidak sesuai, mohon untuk disesuaikan terlebih dahulu sebelum tekan tombol Simpan...
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <div id="div_sdh_tmpil">
                                            &nbsp;
                                        </div>
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='hidden' id='e_sdhtmpl' name='e_sdhtmpl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $psudahtampil; ?>' Readonly>
                                        <input type='hidden' id='e_totjml' name='e_totjml' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotjml; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class='col-md-12 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID JML <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_idjmlrec' name='e_idjmlrec' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrec; ?>' Readonly>
                                        <input type='text' class='form-control' id='e_idbrg2' name='e_idbrg2' readonly>
                                        <input type='text' class='form-control' id='e_nmbrg2' name='e_nmbrg2' readonly>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID Barang <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataBarang('e_idbrg', 'e_nmbrg', 'e_spek', 'e_hrgbrg')">Pilih!</button>
                                        </span>
                                        <input type='text' class='form-control' id='e_idbrg' name='e_idbrg' value='<?PHP //echo $pbrnoid; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Barang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_nmbrg' name='e_nmbrg' class='form-control col-md-7 col-xs-12' maxlength="150" onblur='CekBarangKode()' style="text-transform: uppercase">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Spesifikasi / Uraian <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id="e_spek" name='e_spek' maxlength="450"></textarea>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_jmlqty' name='e_jmlqty' class='form-control col-md-7 col-xs-12 inputmaskrp2'  >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Satuan <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_satuanbrg' name='e_satuanbrg' class='form-control col-md-7 col-xs-12' onkeypress="return event.charCode < 48 || event.charCode  >57" style="text-transform: uppercase">
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Harga <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_hrgbrg' name='e_hrgbrg' class='form-control col-md-7 col-xs-12 inputmaskrp2'  >
                                        *) harga estimasi, bisa dikosongkan...
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id="e_ketdetail" name='e_ketdetail' maxlength='300'></textarea>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <button type='button' class='btn btn-dark btn-xs add-row' onclick='TambahDataBarang("")'>&nbsp; &nbsp; &nbsp; Tambah &nbsp; &nbsp; &nbsp;</button>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        *) setelah isi nama barang, spesifikasi, jumlah, satuan, keterangan klik tombol tambah.
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                    </div>
                    
                    
                    
                </div>
                
                
            </div>
            
            
            <div id='loading3'></div>
            <div id="s_div">
                
                <div class='x_content'>
                    <table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>
                        <thead>
                            <tr>
                                <th width='5px' nowrap></th>
                                <th width='10px' align='center' class='divnone'></th><!--class='divnone' -->
                                <th width='5px' align='center'>&nbsp;</th>
                                <th width='20px' align='center'>Kode</th>
                                <th width='200px' align='center'>Nama Barang</th>
                                <th width='200px' align='center'>Spesifikasi / Uraian</th>
                                <th width='40px' align='center'>Jumlah</th>
                                <th width='20px' align='center'>Satuan</th>
                                <th width='40px' align='center'>Harga</th>
                                <th width='40px' align='center'>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class='inputdata'>
                            <?PHP
                            if ($pact=="editdata") {
                                $nnjmlrc=0;
                                $query ="SELECT idpr, idpr_d, idbarang, namabarang, idbarang_d, spesifikasi1, spesifikasi2, uraian, "
                                        . " keterangan, jumlah as jml, harga as rp_pr, satuan "
                                        . " FROM dbpurchasing.t_pr_transaksi_d WHERE idpr='$pidbr'";
                                $tampild=mysqli_query($cnmy, $query);
                                while ($nrd= mysqli_fetch_array($tampild)) {
                                    $pidbrg=$nrd['idbarang'];
                                    $pnmbrg=$nrd['namabarang'];
                                    $pspcbrg=$nrd['spesifikasi1'];
                                    $pketbrg=$nrd['keterangan'];
                                    $pstnbrg=$nrd['satuan'];
                                    $pjmldet=$nrd['jml'];
                                    $phargarp=$nrd['rp_pr'];
                                    
                                    if (empty($pjmldet)) $pjmldet=0;
                                    if (empty($phargarp)) $phargarp=0;
                                    $ijmlbrg=number_format($pjmldet,0,",",",");
                                    $ihargabrg=number_format($phargarp,0,",",",");
                                    
                                    echo "<tr>";
                                    echo "<td nowrap><input type='checkbox' name='record'>"
                                            . "<input type='hidden' id='m_idjmrec[$nnjmlrc]' name='m_idjmrec[]' value='$nnjmlrc' Readonly>"
                                            . "<input type='hidden' id='m_idbrg2[$nnjmlrc]' name='m_idbrg2[$nnjmlrc]' value='$pidbrg'>"
                                            . "<input type='hidden' id='m_nmbrg2[$nnjmlrc]' name='m_nmbrg2[$nnjmlrc]' value='$pnmbrg'>"
                                            . "</td>";
                                    echo "<td nowrap class='divnone'><input type='checkbox' name='chkbox_br[]' id='chkbox_br[$nnjmlrc]' value='$nnjmlrc' checked></td>";
                                    
                                    echo "<td><button type='button' class='btn btn-warning btn-xs' onclick=\"EditDataBarang('chkbox_br[]', '$nnjmlrc')\">Edit</button></td>";
                                    
                                    echo "<td nowrap>$pidbrg<input type='hidden' id='m_idbrg[$nnjmlrc]' name='m_idbrg[$nnjmlrc]' value='$pidbrg'></td>";
                                    echo "<td nowrap>$pnmbrg<input type='hidden' id='m_nmbrg[$nnjmlrc]' name='m_nmbrg[$nnjmlrc]' value='$pnmbrg'></td>";
                                    echo "<td >$pspcbrg<span hidden><textarea class='form-control' id='txt_specbr[$nnjmlrc]' name='txt_specbr[$nnjmlrc]'>$pspcbrg</textarea></span></td>";
                                    echo "<td nowrap align='right'>$ijmlbrg<input type='hidden' class='form-control inputmaskrp2' id='txt_njmlbrg[$nnjmlrc]' name='txt_njmlbrg[$nnjmlrc]' value='$pjmldet'></td>";
                                    echo "<td nowrap>$pstnbrg<input type='hidden' id='m_satuan[$nnjmlrc]' name='m_satuan[$nnjmlrc]' value='$pstnbrg'></td>";
                                    echo "<td nowrap align='right'>$ihargabrg<input type='hidden' class='form-control inputmaskrp2' id='txt_nhrgbrg[$nnjmlrc]' name='txt_nhrgbrg[$nnjmlrc]' value='$phargarp'></td>";
                                    echo "<td >$pketbrg<span hidden><textarea class='form-control' id='txt_ketbrg[$nnjmlrc]' name='txt_ketbrg[$nnjmlrc]'>$pketbrg</textarea></span></td>";
                                    
                                    echo "</tr>";
                                    
                                    $nnjmlrc++;
                                    
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <button type='button' class='btn btn-danger btn-xs delete-row' >&nbsp; &nbsp; Hapus &nbsp; &nbsp;</button>
                </div>
                
            </div>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                <?PHP
                if (empty($sudahapv)) {
                    if ($pmyact=="editdata") {
                        ?>
                        <button type='button' id="btn_simpan" class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Update</button>
                        <?PHP
                    }else{
                        echo "<div class='col-sm-5'>";
                        include "module/purchasing/pch_pr/ttd_pchreq.php";
                        echo "</div>";
                    }
                ?>
                <?PHP
                }elseif ($sudahapv=="reject") {
                    echo "data sudah hapus";
                }else{
                    echo "tidak bisa diedit, sudah approve";
                }
                ?>
                </div>
            </div>
            
            
        
        </form>
        
        
    </div>
</div>


<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
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
    .ui-datepicker-calendar {
        display: none;
    }
</style>

<style>
    .divnone {
        display: none;
    }
    #datatablestockopn th {
        font-size: 13px;
    }
    #datatablestockopn td { 
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


<script>
    $(document).ready(function() {
        var element = document.getElementById("div_atasan");
        //element.classList.remove("disabledDiv");
        element.classList.add("disabledDiv");
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var pact = urlku.searchParams.get("act");
        if (pact=="tambahbaru") {
            var iuntuk = document.getElementById('cb_untuk').value;
            if (iuntuk=="HO") {
                ShowDataAtasan();
            }
        }
                            
    } );


    function ShowDataKaryawan() {
        ShowDataAtasan();
        
        var iuntuk = document.getElementById('cb_untuk').value;
        var ikry = document.getElementById('cb_karyawan').value;
        $.ajax({
            type:"post",
            url:"module/purchasing/viewdatapch.php?module=caridatakaryawan",
            data:"ukry="+ikry+"&uuntuk="+iuntuk,
            beforeSend: function () {
                document.getElementById("btn_simpan").disabled = true;
            },
            success:function(data){
                $("#div_datakaryawan").html(data);
            },
            complete: function () {
                document.getElementById("btn_simpan").disabled = false;
            },
            error: function () {
                alert('Something wrong. Try Again!')                
            }
        });
        
    }

    function ShowPengajuanUntuk() {
        document.getElementById("btn_simpan").disabled = true;
        $("#cb_karyawan").html("");
        $("#cb_divisi").html("");
        $("#cb_cabang").html("");
        $("#cb_area").html("");
        document.getElementById('e_jabatanid').value="";
        
        document.getElementById("btn_simpan").disabled = false;
        
        ShowKaryawan();
    }
    
    function ShowKaryawan(){
        var iuntuk = document.getElementById('cb_untuk').value;
        
        $.ajax({
            type:"post",
            url:"module/purchasing/viewdatapch.php?module=carikaryawanid",
            data:"uuntuk="+iuntuk,
            beforeSend: function () {
                document.getElementById("btn_simpan").disabled = true;
            },
            success:function(data){
                $("#cb_karyawan").html(data);
            },
            complete: function () {
                document.getElementById("btn_simpan").disabled = false;
            },
            error: function () {
                alert('Something wrong. Try Again!')                
            }
        });
        
    }


    function ShowDataAtasan() {
        var iuntuk = document.getElementById('cb_untuk').value;
        var ikry = document.getElementById('cb_karyawan').value;
        var icab = document.getElementById('cb_cabang').value;
        
        $.ajax({
            type:"post",
            url:"module/purchasing/viewdatapch.php?module=caridataatasan",
            data:"ukry="+ikry+"&ucab="+icab+"&uuntuk="+iuntuk,
            beforeSend: function () {
                document.getElementById("btn_simpan").disabled = true;
            },
            success:function(data){
                $("#div_atasan").html(data);
            },
            complete: function () {
                document.getElementById("btn_simpan").disabled = false;
            },
            error: function () {
                alert('Something wrong. Try Again!')                
            }
        });
    }    
    
    function ShowDataArea() {
        var ikry = document.getElementById('cb_karyawan').value;
        var ijbt = document.getElementById('e_jabatanid').value;
        var icab = document.getElementById('cb_cabang').value;
        $.ajax({
            type:"post",
            url:"module/purchasing/viewdatapch.php?module=caridataarea",
            data:"ukry="+ikry+"&ujbt="+ijbt+"&ucab="+icab,
            beforeSend: function () {
                document.getElementById("btn_simpan").disabled = true;
            },
            success:function(data){
                $("#cb_area").html(data);
            },
            complete: function () {
                document.getElementById("btn_simpan").disabled = false;
            },
            error: function () {
                alert('Something wrong. Try Again!')                
            }
        });
    }
    
    function ShowDataTipe() {
        $(".inputdata").html("");
    }
    
    
    function getDataBarang(data1, data2, data3, data4){
        var eidinput =document.getElementById('e_id').value;
        
        $.ajax({
            type:"post",
            url:"module/purchasing/pch_pr/viewdata_barangpr.php?module=viewdatabarang",
            data:"udata1="+data1+"&udata2="+data2+"&udata3="+data3+"&udata4="+data4+"&uidinput="+eidinput,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function getDataModalBarang(fildnya1, fildnya2, fildnya3, fildnya4, d1, d2, d3, d4){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
        document.getElementById(fildnya3).value=d3;
        document.getElementById(fildnya4).value=d4;
        
        document.getElementById('e_idbrg2').value=d1;
        document.getElementById('e_nmbrg2').value=d2;
        document.getElementById("e_jmlqty").focus();
    }
    
    
    function disp_confirm(pText_,ket)  {
        
        ShowDataAtasan();
        
        setTimeout(function () {
            disp_confirm_ext(pText_,ket)
        }, 200);
        
    }
    
    function disp_confirm_ext(pText_,ket)  {

        var iid = document.getElementById('e_id').value;
        var iuntuk = document.getElementById('cb_untuk').value;
        var itipe = document.getElementById('cb_tipeaju').value;
        var ikry = document.getElementById('cb_karyawan').value;
        var idivid = document.getElementById('cb_divisi').value;
        var icabid = document.getElementById('cb_cabang').value;
        var idepid = document.getElementById('cb_dept').value;
        var esudahada=document.getElementById('e_sdhtmpl').value;

        if (itipe=="") {
            alert("tipe harus dipilih...");
            return false;
        }

        if (iuntuk=="") {
            alert("Pengajuan untuk, belum dipilih...");
            return false;
        }

        if (ikry=="") {
            alert("Pembuat masih kosong...");
            return false;
        }
        
        if (idivid=="") {
            alert("divisi masih kosong...");
            return false;
        }
        
        if (icabid=="") {
            alert("Cabang harus diisi...");
            return false;
        }
        
        if (idepid=="") {
            alert("Departemen harus dipilih...");
            return false;
        }

        var espvkd=document.getElementById('e_kdspv').value;
        var edmkd=document.getElementById('e_kddm').value;
        var esmkd=document.getElementById('e_kdsm').value;
        var egsmkd=document.getElementById('e_kdgsm').value;

        if (espvkd=="" && edmkd=="" && esmkd=="" && egsmkd=="") {
            alert("atasan kosong...");
            return false;
        }
            
        if (esudahada=="" || esudahada=="0") {
            alert("barang masih kosong...");
            return false;
        }

        $.ajax({
            type:"post",
            url:"module/purchasing/pch_pr/viewdatapr.php?module=cekdatasudahada",
            data:"uid="+iid+"&ukry="+ikry,
            success:function(data){
                //var tjml = data.length;
                //alert(data);
                //return false;

                if (data=="boleh") {

                    ok_ = 1;
                    if (ok_) {
                        var r=confirm(pText_)
                        if (r==true) {
                            var myurl = window.location;
                            var urlku = new URL(myurl);
                            var module = urlku.searchParams.get("module");
                            var idmenu = urlku.searchParams.get("idmenu");
                            //document.write("You pressed OK!")
                            document.getElementById("demo-form2").action = "module/purchasing/pch_pr/aksi_purchasereq.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }
                    } else {
                        //document.write("You pressed Cancel!")
                        return 0;
                    }

                }else{
                    alert(data);
                }
            }
        });
        
        
        
    }
</script>


<script>
    $(document).ready(function(){
        $("#add_new").click(function(){
            $(".entry-form").fadeIn("fast");
        });

        $("#close").click(function(){
            $(".entry-form").fadeOut("fast");
        });

        $("#cancel").click(function(){
            $(".entry-form").fadeOut("fast");
        });
        
        $(".add-row").click(function(){
            
            var newchar = '';
            var i_idjmlrec = $("#e_idjmlrec").val();
            var i_idbrg2 = $("#e_idbrg2").val();
            var i_nmbrg2 = $("#e_nmbrg2").val();
            var i_idbrg = $("#e_idbrg").val();
            var i_nmbrg = $("#e_nmbrg").val();
            var i_specbrg = $("#e_spek").val();
            var i_ket = $("#e_ketdetail").val();
            var i_satuan = $("#e_satuanbrg").val();
            var i_jml = $("#e_jmlqty").val();
            var i_hrg = $("#e_hrgbrg").val();
            
            if (i_nmbrg=="" && i_specbrg=="" && i_jml=="") {
                alert("masih kosong...."); return false;
            }
            
            if (i_nmbrg=="") {
                alert("nama barang harus diisi...!!!"); return false;
            }
            
            if (i_jml=="") {
                alert("jumlah harus diisi...!!!"); return false;
            }
            
            i_nmbrg = i_nmbrg.toUpperCase();
            i_nmbrg2 = i_nmbrg2.toUpperCase();
            i_satuan = i_satuan.toUpperCase();
            
            var xtxnmbrg = i_nmbrg.replace(/\s/gm,"");
            var ntxtspc = i_specbrg.replace(/\s/gm,"");
            
            var arjmlrec = document.getElementsByName('m_idjmrec[]');
            for (var i = 0; i < arjmlrec.length; i++) {
                var ijmlrec = arjmlrec[i].value;
                
                var ikdbrg = document.getElementById('m_idbrg['+ijmlrec+']').value;
                var inmbrg = document.getElementById('m_nmbrg['+ijmlrec+']').value;
                var ispcbrg = document.getElementById('txt_specbr['+ijmlrec+']').value;
                
                
                var inmbrg = inmbrg.replace(/\s/gm,"");
                var xspcbrg = ispcbrg.replace(/\s/gm,"");
                
                if (ikdbrg==i_idbrg && inmbrg==xtxnmbrg && xspcbrg==ntxtspc) {
                    return false;
                }
            }
            
            
            var markup;
            markup = "<tr>";
            markup += "<td nowrap><input type='checkbox' name='record'>";
            markup += "<input type='hidden' id='m_idjmrec["+i_idjmlrec+"]' name='m_idjmrec[]' value='"+i_idjmlrec+"' Readonly>";
            markup += "<input type='hidden' id='m_idbrg2["+i_idjmlrec+"]' name='m_idbrg2["+i_idjmlrec+"]' value='"+i_idbrg2+"'>";
            markup += "<input type='hidden' id='m_nmbrg2["+i_idjmlrec+"]' name='m_nmbrg2["+i_idjmlrec+"]' value='"+i_nmbrg2+"'>";
            markup += "</td>";
            markup += "<td nowrap class='divnone'><input type='checkbox' name='chkbox_br[]' id='chkbox_br["+i_idjmlrec+"]' value='"+i_idjmlrec+"' checked></td>";
            
            markup += "<td><button type='button' class='btn btn-warning btn-xs' onclick=\"EditDataBarang('chkbox_br[]', '"+i_idjmlrec+"')\">Edit</button></td>";
            
            markup += "<td nowrap>" + i_idbrg + "<input type='hidden' id='m_idbrg["+i_idjmlrec+"]' name='m_idbrg["+i_idjmlrec+"]' value='"+i_idbrg+"'></td>";
            markup += "<td nowrap>" + i_nmbrg + "<input type='hidden' id='m_nmbrg["+i_idjmlrec+"]' name='m_nmbrg["+i_idjmlrec+"]' value='"+i_nmbrg+"'></td>";
            markup += "<td >" + i_specbrg + "<span hidden><textarea class='form-control' id='txt_specbr["+i_idjmlrec+"]' name='txt_specbr["+i_idjmlrec+"]'>"+i_specbrg+"</textarea></span></td>";
            markup += "<td nowrap align='right'>" + i_jml + "<input type='hidden' class='form-control inputmaskrp2' id='txt_njmlbrg["+i_idjmlrec+"]' name='txt_njmlbrg["+i_idjmlrec+"]' value='"+i_jml+"'></td>";
            markup += "<td nowrap>" + i_satuan + "<input type='hidden' id='m_satuan["+i_idjmlrec+"]' name='m_satuan["+i_idjmlrec+"]' value='"+i_satuan+"'></td>";
            markup += "<td nowrap align='right'>" + i_hrg + "<input type='hidden' class='form-control inputmaskrp2' id='txt_nhrgbrg["+i_idjmlrec+"]' name='txt_nhrgbrg["+i_idjmlrec+"]' value='"+i_hrg+"'></td>";
            markup += "<td >" + i_ket + "<span hidden><textarea class='form-control' id='txt_ketbrg["+i_idjmlrec+"]' name='txt_ketbrg["+i_idjmlrec+"]'>"+i_ket+"</textarea></span></td>";
            markup += "</tr>";
            $("table tbody.inputdata").append(markup);
            
            document.getElementById('e_sdhtmpl').value="1";
            
            if (i_idjmlrec=="") i_idjmlrec="0";
            i_idjmlrec = i_idjmlrec.split(',').join(newchar);
            i_idjmlrec=parseFloat(i_idjmlrec)+1;
            document.getElementById('e_idjmlrec').value=i_idjmlrec;
            
        });
        
        $(".delete-row").click(function(){
            
            var ilewat = false;
            $("table tbody.inputdata").find('input[name="record"]').each(function(){
                if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                    ilewat = true;
                }
            });

            if (ilewat == true) {
                
            }
            
        });
        
        
    });
    

    function EditDataBarang(xchk, xidjmlrec) {
        var xkdbrg2 = document.getElementById('m_idbrg2['+xidjmlrec+']').value;
        var xnmbrg2 = document.getElementById('m_nmbrg2['+xidjmlrec+']').value;
        
        var xkdbrg = document.getElementById('m_idbrg['+xidjmlrec+']').value;
        var xnmbrg = document.getElementById('m_nmbrg['+xidjmlrec+']').value;
        var xspec = document.getElementById('txt_specbr['+xidjmlrec+']').value;
        var xjml = document.getElementById('txt_njmlbrg['+xidjmlrec+']').value;
        var xhrg = document.getElementById('txt_nhrgbrg['+xidjmlrec+']').value;
        var xket = document.getElementById('txt_ketbrg['+xidjmlrec+']').value;
        var xstn = document.getElementById('m_satuan['+xidjmlrec+']').value;
        
        
        document.getElementById('e_idbrg2').value=xkdbrg2;
        document.getElementById('e_nmbrg2').value=xnmbrg2;
        document.getElementById('e_idbrg').value=xkdbrg;
        document.getElementById('e_nmbrg').value=xnmbrg;
        document.getElementById('e_spek').value=xspec;
        document.getElementById('e_hrgbrg').value=xhrg;
        document.getElementById('e_jmlqty').value=xjml;
        document.getElementById('e_ketdetail').value=xket;
        document.getElementById('e_satuanbrg').value=xstn;
        
        $("table tbody.inputdata").find('input[id="chkbox_br['+xidjmlrec+']"]').each(function(){
            $(this).parents("tr").remove();
        });
        
    }
    
    function CekBarangKode() {
        var ikdbrg2=document.getElementById('e_idbrg2').value;
        var inmbrg2=document.getElementById('e_nmbrg2').value;
        var ikdbrg1=document.getElementById('e_idbrg').value;
        var inmbrg1=document.getElementById('e_nmbrg').value;
        
        var inmbrg2_ = inmbrg2.replace(/\s/gm,"");
        var inmbrg1_ = inmbrg1.replace(/\s/gm,"");
        
        if (inmbrg2_!=inmbrg1_) {
            document.getElementById('e_idbrg').value="";
        }else{
            document.getElementById('e_idbrg').value=ikdbrg2;
        }
        
    }
</script>