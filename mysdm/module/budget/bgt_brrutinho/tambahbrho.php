<?php

include "config/fungsi_ubahget_id.php";

$pidmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pidact=$_GET['act'];
$pstsmobile=$_SESSION['MOBILE'];


$piduser=$_SESSION['USERID'];
$pidcard=$_SESSION['IDCARD'];
$pidgroup=$_SESSION['GROUP'];


$hari_ini = date("Y-m-d");
$mytglini="";
$query = "select CURRENT_DATE as lcfields";
$tampilt= mysqli_query($cnmy, $query);
$rowt= mysqli_fetch_array($tampilt);
$mytglini=$rowt['lcfields'];

if ($mytglini==0) $mytglini="";
if (!empty($mytglini)) $hari_ini = date('Y-m-d', strtotime($mytglini));
$tglhariini=date('d', strtotime($hari_ini));


$ppilih_hari=date('Y-m-01', strtotime($hari_ini));//dibuat 01 agar akhir bulan tidak loncat
if ((INT)$tglhariini<=5) {
    $pbln = date('F Y', strtotime('-1 month', strtotime($ppilih_hari)));
    //$pbln = date('F Y', strtotime($hari_ini));
}else{
    $pbln = date('F Y', strtotime($ppilih_hari));
}

$pnbln= date("Ym", strtotime($pbln));
$ptgl1 = date('01/m/Y', strtotime($pbln));
$ptgl2 = date('t/m/Y', strtotime($pbln));

$pidrutin="";
$pkaryawanid=$_SESSION['IDCARD'];
$pkaryawannm=$_SESSION['NAMALENGKAP'];
$pjabatanid=$_SESSION['JABATANID'];


$pkdperiode="2";
$pselper0="";
$pselper1="";
$pselper2="selected";

$query = "select idrutin from dbmaster.t_brrutin0 WHERE kode='1' AND karyawanid='$pkaryawanid' AND DATE_FORMAT(bulan,'%Y%m')='$pnbln' AND kodeperiode='1'";
$tampilj= mysqli_query($cnmy, $query);
$ketemuj= mysqli_num_rows($tampilj);
if ((INT)$ketemuj>0) {
    $ptgl1= date("16/m/Y", strtotime($pbln));
}


$puseradmin=false;
$pfilterkrypilih="";
$query ="select distinct karyawanid FROM dbmaster.t_karyawan_posisi WHERE IFNULL(id_admin,'')='$pkaryawanid'";
$tampiln= mysqli_query($cnmy, $query);
$ketemun=mysqli_num_rows($tampiln);
if ((INT)$ketemun>0) {
    $puseradmin=true;

    $query ="select distinct karyawanid FROM dbmaster.t_karyawan_posisi WHERE (karyawanid='$pkaryawanid' OR IFNULL(id_admin,'')='$pkaryawanid')";
    $tampiln= mysqli_query($cnmy, $query);
    while ($nrow= mysqli_fetch_array($tampiln)) {
        $pkryplid=$nrow['karyawanid'];

        $pfilterkrypilih .="'".$pkryplid."',";
    }
    if (!empty($pfilterkrypilih)) $pfilterkrypilih="(".substr($pfilterkrypilih, 0, -1).")";
    
}

if (empty($pfilterkrypilih)) $pfilterkrypilih="('$pkaryawanid')";

$pketerangan="";
$ptotalsemua=0;
$pidcabang="0000000001";//ETH HO
$pidarea="0000000001";//ETH HO

if ($pkaryawanid=="0000000962" OR $pkaryawanid=="0000001342" OR $pkaryawanid=="0000002074" OR $pkaryawanid=="0000002739") {
    $query_ats = "select atasanid2 as atasanid from hrd.karyawan WHERE karyawanid='$pkaryawanid'";
}else{
    $query_ats = "select atasanid as atasanid from hrd.karyawan WHERE karyawanid='$pkaryawanid'";
}

$tampila= mysqli_query($cnmy, $query_ats);
$rowa= mysqli_fetch_array($tampila);
$pidatasan=$rowa['atasanid'];


$pdivisi="HO";
if ($pidmodule=="entrybrrutinhodivchc") {
    $pdivisi="OTC";
    
    $query = "select karyawanId, atasanId as atasanid, atasanId2 as atasanid2, iCabangId as icabangid, areaId as areaid FROM hrd.karyawan where karyawanId='$pkaryawanid'";
    $tampila=mysqli_query($cnmy, $query);
    $nrow= mysqli_fetch_array(($tampila));
    $pidcabang=$nrow['icabangid'];
    $pidarea=$nrow['areaid'];
    $pidatasan=$nrow['atasanid'];
    $pidatasan2=$nrow['atasanid2'];

    $query = "select karyawanId, atasanId as atasanid, iCabangId as icabangid, areaId as areaid FROM dbmaster.t_karyawan_posisi where karyawanId='$pkaryawanid'";
    $tampila=mysqli_query($cnmy, $query);
    $nrow= mysqli_fetch_array(($tampila));
    $pidcabang_=$nrow['icabangid'];
    $pidarea_=$nrow['areaid'];
    $pidatasan_=$nrow['atasanid'];

    if (empty($pidcabang)) $pidcabang=$pidcabang_;
    if (empty($pidarea)) $pidarea=$pidarea_;
    if (empty($pidatasan)) $pidatasan=$pidatasan_;
    
    
    $pkdperiode="1";
    $pselper0="";
    $pselper1="selected";
    $pselper2="";


}

$query = "select nopol as lcfields from dbmaster.t_kendaraan_pemakai WHERE karyawanid='$pkaryawanid' AND IFNULL(stsnonaktif,'')<>'Y' order by tglawal desc LIMIT 1";
$tampiln= mysqli_query($cnmy, $query);
$rown= mysqli_fetch_array($tampiln);
$pidnopol=$rown['lcfields'];


$pjmlwfh=0;
$pjmlwfo=0;
$pjmlwfo_val=0;
$pjmlwfo_inv=0;

$sudahapv="";

$act="input";
if ($pidact=="editdata"){
    $act="update";
    
    $pidinput_ec=$_GET['id'];
    $pidrutin = decodeString($pidinput_ec);
    
    if ($puseradmin==true) {
        $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_brrutin0 WHERE kode=1 AND idrutin='$pidrutin' AND ( karyawanid='$pidcard' OR karyawanid IN $pfilterkrypilih )");
    }else{
        $edit = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_brrutin0 WHERE kode=1 AND idrutin='$pidrutin' AND karyawanid='$pidcard'");
    }
    $pketemu    = mysqli_num_rows($edit);
    if ((DOUBLE)$pketemu<=0) { exit; }
    $r    = mysqli_fetch_array($edit);
    
    $pbln = date('F Y', strtotime($r['bulan']));
    $ptgl1 = date('d/m/Y', strtotime($r['periode1']));
    $ptgl2 = date('d/m/Y', strtotime($r['periode2']));
    
    
    $pdivisi=$r['divisi'];
    $pjabatanid=$r['jabatanid'];
    $pidcabang=$r['icabangid'];
    $pidarea=$r['areaid'];
    $pidnopol=$r['nopol'];
    
    $pkaryawanid=$r['karyawanid'];
    $query_nm = "select nama from hrd.karyawan WHERE karyawanid='$pkaryawanid'";
    $tampil_nm= mysqli_query($cnmy, $query_nm);
    $kr= mysqli_fetch_array($tampil_nm);
    $pkaryawannm=$kr['nama'];
    
    
    $pkdperiode=$r['kodeperiode'];
    if ($pkdperiode==1) {
        $pselper1="selected";
        $pselper2="";
    }elseif ($pkdperiode==2) {
        $pselper1="";
        $pselper2="selected";
    }
    
    $pketerangan=$r['keterangan'];
    $pidatasan=$r['atasan4'];
    
    $ptotalsemua=$r['jumlah'];
    
    
    
}else{
    
    //cari absensi
    
    include "cari_absen_karyawan.php";
    $pjumlahabs = CariAbsensiByKaryawan("", $pkaryawanid, $pbln);

    $pjmlwfh=$pjumlahabs[0];
    $pjmlwfo=$pjumlahabs[1];
    $pjmlwfo_val=$pjumlahabs[2];
    $pjmlwfo_inv=$pjumlahabs[3];
    
    //echo "WFH : $pjmlwfh, WFO : $pjmlwfo, WFO val : $pjmlwfo_val, WFO inval: $pjmlwfo_inv<br/>";

    //END cari absensi
}



?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">
    
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <h2>
                        <a class='btn btn-default' href="<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                    </h2>
                    <div class='clearfix'></div>
                </div>
                
                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='d-form1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-12'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidrutin; ?>' Readonly>
                                        <input type='hidden' id='e_idinputuser' name='e_idinputuser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piduser; ?>' Readonly>
                                        <input type='hidden' id='e_idcarduser' name='e_idcarduser' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcard; ?>' Readonly>
                                        <input type='hidden' id='e_act' name='e_act' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidact; ?>' Readonly>
                                        <input type='hidden' id='e_tanggal' name='e_tanggal' class='form-control col-md-7 col-xs-12' value='<?PHP echo $hari_ini; ?>' Readonly>
                                        <input type='hidden' id='e_stsadmin' name='e_stsadmin' class='form-control col-md-7 col-xs-12' value='<?PHP echo $puseradmin; ?>' Readonly>
                                        <input type='hidden' id='e_divisiid' name='e_divisiid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pdivisi; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Yang Membuat <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-12'>
                                        <?PHP
                                        if ($puseradmin==true) {
                                            echo "<select class='form-control input-sm' id='e_idkaryawan' name='e_idkaryawan' onchange='ShowDariKaryawan()'>";
                                            $query = "select karyawanid as karyawanid, nama as nama FROM hrd.karyawan as a WHERE 1=1 "
                                                    . " AND (karyawanid IN $pfilterkrypilih OR karyawanid='$pkaryawanid' ) ";
                                            $tampilk=mysqli_query($cnmy, $query);
                                            while ($krow= mysqli_fetch_array($tampilk)) {
                                                $npkryid=$krow['karyawanid'];
                                                $npkrynm=$krow['nama'];
                                                
                                                if ($npkryid==$pkaryawanid)
                                                    echo "<option value='$npkryid' selected>$npkrynm</option>";
                                                else
                                                    echo "<option value='$npkryid'>$npkrynm</option>";
                                            }
                                            echo "</select>";
                                        }else{
                                            echo "<input type='text' id='e_nmkaryawan' name='e_nmkaryawan' class='form-control col-md-7 col-xs-12' value='$pkaryawannm' Readonly>";
                                            echo "<input type='hidden' id='e_idkaryawan' name='e_idkaryawan' class='form-control col-md-7 col-xs-12' value='$pkaryawanid' Readonly>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                
                                <div id="div_datakry">
                                    
                                    <div hidden class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            
                                            <input type='text' id='e_jabatanid' name='e_jabatanid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pjabatanid; ?>' Readonly>
                                            <input type='text' id='e_cabangid' name='e_cabangid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcabang; ?>' Readonly>
                                            <input type='text' id='e_areaid' name='e_areaid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidarea; ?>' Readonly>
                                            <?PHP
                                            if ($puseradmin==true) {
                                                echo "<input type='text' id='e_nmkaryawan' name='e_nmkaryawan' class='form-control col-md-7 col-xs-12' value='$pkaryawannm' Readonly>";
                                            }
                                            ?>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Polisi Kendaraan <span class='required'></span></label>
                                        <div class='col-md-4 col-sm-4 col-xs-12'>
                                            <input type='hidden' id='e_nopolidX' name='e_nopolidX' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidnopol; ?>' Readonly>
                                            
                                            <?PHP
                                                $psudhpilihnopol=false;
                                                echo "<select class='form-control input-sm' id='e_nopolid' name='e_nopolid' onchange=''>";
                                                    echo "<option value='' selected>--Tidak Ada--</option>";
                                                    $query = "select nopol from dbmaster.t_kendaraan_pemakai WHERE karyawanid='$pkaryawanid' "
                                                            . " AND IFNULL(stsnonaktif,'')<>'Y' order by tglawal desc";
                                                    $tampilk=mysqli_query($cnmy, $query);
                                                    while ($krow= mysqli_fetch_array($tampilk)) {
                                                        $pidnopolis=$krow['nopol'];

                                                        if ($psudhpilihnopol==false) {
                                                            echo "<option value='$pidnopolis' selected>$pidnopolis</option>";
                                                            $psudhpilihnopol=true;
                                                        }else{
                                                            echo "<option value='$pidnopolis'>$pidnopolis</option>";
                                                        }
                                                    }
                                                echo "</select>";
                                            ?>
                                            
                                        </div>
                                    </div>
                                    
                                    
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan </label>
                                    <div class='col-md-3 col-sm-4 col-xs-12'>
                                        <div class='input-group date' id='thnbln01x'>
                                            <input type='text' class='form-control' id='e_bulan' name='e_bulan' autocomplete='off' value='<?PHP echo $pbln; ?>' Readonly />
                                            <span class='input-group-addon'>
                                                <i class="fa fa-long-arrow-left"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode Periode <span class='required'></span></label>
                                    <div class='col-md-3 col-sm-4 col-xs-12'>
                                        <select class='form-control input-sm' id='e_periode' name='e_periode' onchange="showDariKodePeriode()">
                                            <?PHP
                                                if ((int)$tglhariini > 20) {
                                                    echo "<option value='' $pselper0>-- Pilihan --</option>";
                                                    echo "<option value='2' $pselper2>Periode 2</option>";
                                                }else{
                                                    echo "<option value='' $pselper0>-- Pilihan --</option>";
                                                    echo "<option value='1' $pselper1>Periode 1</option>";
                                                    echo "<option value='2' $pselper2>Periode 2</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='mytgl01'>Periode <span class='required'></span></label>
                                    <div class='col-md-3 col-sm-4 col-xs-12'>
                                        <div class="form-group">
                                            <div class='input-group date' id='mytgl01'>
                                                <input type='text' id='e_periode01' name='e_periode01' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $ptgl1; ?>' data-inputmask="'mask': '99/99/9999'" Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            <div class='input-group date' id='mytgl02'>
                                                <input type='text' id='e_periode02' name='e_periode02' autocomplete='off' required='required' class='form-control' placeholder='dd/MM/yyyy' value='<?PHP echo $ptgl2; ?>' data-inputmask="'mask': '99/99/9999'" Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <textarea class='form-control' id='e_ket' name='e_ket' rows='3' placeholder='Aktivitas'><?PHP echo $pketerangan; ?></textarea>
                                    </div><!--disabled='disabled'-->
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Atasan <span class='required'></span></label>
                                    <div class='col-md-4 col-sm-4 col-xs-12'>
                                        <select class='form-control input-sm' id='e_atasan' name='e_atasan' onchange="">
                                            <?PHP
                                                echo "<option value='' selected>-- Pilihan --</option>";
                                                $query = "select karyawanId as karyawanid, nama as nama From hrd.karyawan WHERE karyawanid='$pidatasan'";
                                                $query .= " ORDER BY nama";
                                                $tampil = mysqli_query($cnmy, $query);
                                                $ketemu= mysqli_num_rows($tampil);
                                                
                                                if ((DOUBLE)$ketemu==0) {
                                                    $query = "select karyawanId as karyawanid, nama as nama From hrd.karyawan WHERE 1=1 ";
                                                    $query .= " AND (IFNULL(tglkeluar,'0000-00-00')='0000-00-00' OR IFNULL(tglkeluar,'')='') ";
                                                    $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S')  "
                                                            . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                                                            . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                                                            . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                                                            . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY') ";
                                                    $query .= " AND nama NOT IN ('ACCOUNTING')";
                                                    $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
                                                    $query .= " ORDER BY nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                }
                                                
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pkaryid=$z['karyawanid'];
                                                    $pkarynm=$z['nama'];
                                                    $pkryid=(INT)$pkaryid;
                                                    
                                                    if ($pkaryid==$pidatasan)
                                                        echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                    else
                                                        echo "<option value='$pkaryid'>$pkarynm ($pkryid)</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div id="div_jmlabs">
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                        <div class='col-md-3 col-sm-3 col-xs-6'>
                                            <?PHP
                                            $pkaryidcode=encodeString($pkaryawanid);
                                            $bulan_pilih=encodeString($pnbln);
                                            $pviewdataabsen = "<a class='btn btn-warning btn-xs' href='eksekusi3.php?module=showdataabsensi&i=$pkaryidcode&b=$bulan_pilih' target='_blank'>List Absensi $pkaryawannm</a>";
                                            echo $pviewdataabsen;
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah WFH <span class='required'></span></label>
                                        <div class='col-md-3 col-sm-3 col-xs-5'>
                                            <input type='text' id='e_jmlwfh' name='e_jmlwfh' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlwfh; ?>' readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah WFO (Valid) <span class='required'></span></label>
                                        <div class='col-md-3 col-sm-3 col-xs-5'>
                                            <input type='hidden' id='e_jmlwfo' name='e_jmlwfo' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlwfo; ?>' readonly>
                                            <input type='text' id='e_jmlwfoval' name='e_jmlwfoval' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlwfo_val; ?>' readonly>
                                        </div>
                                    </div>

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for='' style="color:red;">Jumlah WFO (Invalid) <span class='required'></span></label>
                                        <div class='col-md-3 col-sm-3 col-xs-5'>
                                            <input type='text' id='e_jmlwfoinv' name='e_jmlwfoinv' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlwfo_inv; ?>' readonly>
                                        </div>
                                    </div>
                                    
                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Total Rp. <span class='required'></span></label>
                                        <div class='col-md-3 col-sm-3 col-xs-5'>
                                            <input type='text' id='e_totalsemua' name='e_totalsemua' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotalsemua; ?>' readonly>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                
                                
                            </div>
                            
                            
                            <?PHP
                            echo "<br/>*) <b>sebelum mengisi detail, pastikan bulan dan kode periode sudah sesuai.</b><br/>";
                            $ptomboldetail = "<input type='button' class='btn btn-info btn-xs' "
                                    . " onclick=\"ShowDariTombolRefAbs()\" value='Klik disini jika detail atau absensinya tidak muncul.' >";
                            echo "$ptomboldetail";
                            
                            echo "<div id='div_detail'>";
                                if ($pstsmobile=="Y") {
                                    echo "<br/>&nbsp;";
                                    echo "<div style='overflow-x:auto;'>";
                                        include "module/budget/bgt_brrutinho/inputdetailmobileho.php";
                                    echo "</div>";
                                }else{
                                    include "module/budget/bgt_brrutinho/inputdetailbrho.php";
                                }
                            echo "</div>";
                            
                            ?>
                            
                            
                            
                        </div>
                    </div>
                    
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <?PHP
                            if (empty($sudahapv)) {
                                if ($pidact=="editdata" ) {
                                    echo "<button type='button' class='btn btn-success' onclick=\"disp_confirm('Simpan ?', '$act', '')\">Save</button>";
                                }else{
                                echo "<div class='col-sm-5'>";
                                    include "module/budget/bgt_brrutinho/ttd_brrutinho.php";
                                echo "</div>";
                                }
                            }elseif ($sudahapv=="reject") {
                                echo "data sudah hapus";
                            }else{
                                echo "tidak bisa diedit, sudah approve";
                            }
                            ?>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                </form>
                
            </div>
            
        </div>
        
    </div>
    
</div>


<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<style>
    .ui-datepicker-calendar {
        display: none;
    }
    
    .divnone {
        display: none;
    }
    #datatableuc th {
        font-size: 12px;
    }
    #datatableuc td { 
        font-size: 12px;
        padding: 3px;
        margin: 1px;
    }
</style>

<script>
    
    $(document).ready(function() {
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var nact = urlku.searchParams.get("act");
        if (nact=="tambahbaru") {
            showKodePeriode();
            showPeriode();
        }
        
        HitungTotalJumlahRp();
        
        var dataTable = $('#datatable').DataTable( {
            "ordering": false,
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
        
        
        $('#e_bulan').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'MM yy',
            <?PHP
            if ($tglhariini=="01" OR $tglhariini=="1") {
            ?>
                minDate: '-1M',
            <?PHP
            }else{
            ?>
                minDate: '-1M',
            <?PHP
            }
            ?>
            onSelect: function(dateStr) {
                
            },
            onClose: function() {
                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                showDariBulan();
            },

            beforeShow: function() {
                if ((selDate = $(this).val()).length > 0) 
                {
                    iYear = selDate.substring(selDate.length - 4, selDate.length);
                    iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                    $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                }
            }

        });
        
        
        
        
        
    });
    
    function ShowDariKaryawan() {
        $("#div_datakry").html("");
        setTimeout(function () {
            ShowDataKaryawan();
        }, 100);
        
        showKodePeriode();
        showPeriode();
        
        $("#div_jmlabs").html("");
        $("#div_detail").html("");
        setTimeout(function () {
            showDataAbsensi();
        }, 200);
        
    }
    
    
    
    function showDariBulan() {
        
        var istsadmin = document.getElementById('e_stsadmin').value;
        if (istsadmin=="1" || istsadmin==true) {
            $("#div_datakry").html("");
            setTimeout(function () {
                ShowDataKaryawan();
            }, 100);
        }
        
        
        showKodePeriode();
        showPeriode();
        
        $("#div_jmlabs").html("");
        $("#div_detail").html("");
        setTimeout(function () {
            showDataAbsensi();
        }, 200);
        
        
    }
    
    
    function showDariKodePeriode() {
        showPeriode();
        
        $("#div_jmlabs").html("");
        $("#div_detail").html("");
        setTimeout(function () {
            showDataAbsensi();
        }, 200);
        
    }
    
    
    function ShowDariTombolRefAbs() {
        showDataAbsensi();
    }
    
    function ShowDataKaryawan() {
        var ikry = document.getElementById('e_idkaryawan').value;
        var idivisi = document.getElementById('e_divisiid').value;
        $.ajax({
            type:"post",
            url:"module/budget/bgt_brrutinho/viewdatabrho.php?module=getdatakaryawan",
            data:"ukry="+ikry+"&udivisi="+idivisi,
            success:function(data){
                $("#div_datakry").html(data);
            }
        });
    }
    
    function showKodePeriode() {
        var idivid = document.getElementById('e_divisiid').value;
        var itanggal = document.getElementById('e_tanggal').value;
        var ibulan = document.getElementById('e_bulan').value;
        $.ajax({
            type:"post",
            url:"module/budget/bgt_brrutinho/viewdatabrho.php?module=getkodeperiode",
            data:"ubulan="+ibulan+"&utanggal="+itanggal+"&udivid="+idivid,
            success:function(data){
                $("#e_periode").html(data);
            }
        });
    }
    
    
    function showPeriode() {
        var ikode = document.getElementById('e_periode').value;
        var ibulan = document.getElementById('e_bulan').value;
        var ikry = document.getElementById('e_idkaryawan').value;
        var idivid = document.getElementById('e_divisiid').value;
        var itanggal = document.getElementById('e_tanggal').value;
        
        $.ajax({
            type:"post",
            url:"module/budget/bgt_brrutinho/viewdatabrho.php?module=getperiode",
            data:"ubulan="+ibulan+"&utanggal="+itanggal+"&ukode="+ikode+"&ukry="+ikry+"&udivid="+idivid,
            success:function(data){
                var arr_date = data.split(",");
                document.getElementById('e_periode01').value=arr_date[0];
                document.getElementById('e_periode02').value=arr_date[1];
            }
        });
    }
    
    
    function showDataAbsensi() {
        $("#div_detail").html("");
        $("#div_jmlabs").html("");
        var ibulan = document.getElementById('e_bulan').value;
        var ikry = document.getElementById('e_idkaryawan').value;
        var ikode = document.getElementById('e_periode').value;
        var idivid = document.getElementById('e_divisiid').value;
        
        $.ajax({
            type:"post",
            url:"module/budget/bgt_brrutinho/viewdatabrho.php?module=caridataabsentotal",
            data:"ubulan="+ibulan+"&ukry="+ikry+"&ukode="+ikode+"&udivid="+idivid,
            success:function(data){
                $("#div_jmlabs").html(data);
                setTimeout(function () {
                    ShowDetailInputan();
                }, 80);
            }
        });
    }
    
    function ShowDetailInputan() {
        $("#div_detail").html("");
        var iid = document.getElementById('e_id').value;
        var ikry = document.getElementById('e_idkaryawan').value;
        var ijbt = document.getElementById('e_jabatanid').value;
        var idivisi = document.getElementById('e_divisiid').value;
        var iact = document.getElementById('e_act').value;
        var ijmlwfh = document.getElementById('e_jmlwfh').value;
        var ijmlwfo = document.getElementById('e_jmlwfo').value;
        var ijmlwfo_val = document.getElementById('e_jmlwfoval').value;
        var ijmlwfo_inv = document.getElementById('e_jmlwfoinv').value;
        var itotal = document.getElementById('e_totalsemua').value;
        
        var ibulan = document.getElementById('e_bulan').value;
        var ikode = document.getElementById('e_periode').value;
        
        $.ajax({
            type:"post",
            url:"module/budget/bgt_brrutinho/viewdatabrho.php?module=cariinputandetail",
            data:"ubulan="+ibulan+"&ukode="+ikode+"&ukry="+ikry+
                    "&uid="+iid+"&ujbt="+ijbt+"&udivisi="+idivisi+"&uact="+iact+
                    "&ujmlwfh="+ijmlwfh+"&ujmlwfo="+ijmlwfo+"&ujmlwfo_val="+ijmlwfo_val+"&ujmlwfo_inv="+ijmlwfo_inv+
                    "&utotal="+itotal,
            success:function(data){
                $("#div_detail").html(data);
                HitungTotalJumlahRp();
            }
        });
    }
    
    
    function myTrim(x) {
        return x.replace(/^\s+|\s+$/gm,'');
    }
    
    function disp_confirm(pText_, ket, data_img) {
        
        var iid = document.getElementById('e_id').value;
        var ikry = document.getElementById('e_idkaryawan').value;
        var ibln = document.getElementById('e_bulan').value;
        var ikdperiode = document.getElementById('e_periode').value;
        var iperiode01 = document.getElementById('e_periode01').value;
        var iatasan = document.getElementById('e_atasan').value;
        var etotsem =document.getElementById('e_totalsemua').value;
        var ep01 =document.getElementById('e_periode01').value;
        var ep02 =document.getElementById('e_periode02').value;
        var idivisi =document.getElementById('e_divisiid').value;
        
        if (idivisi=="") {
            alert("Divisi kosong...");
            return false;
        }
        
        if (ikry=="") {
            alert("Pembuat masih kosong...");
            return false;
        }

        if (ibln=="") {
            alert("Bulan masih kosong...");
            return false;
        }

        if (ikdperiode=="") {
            alert("Kode periode masih kosong...");
            return false;
        }

        if (iperiode01=="") {
            alert("periode masih kosong...");
            return false;
        }

        if (iatasan=="") {
            alert("Atasan masih kosong...");
            return false;
        }

        if (parseFloat(etotsem)==0) {
            alert("Total Rupiah Masih Kosong....");
            return 0;
        }
        
        
        //alert(pText_+" dan "+ket);//input
        
        $.ajax({
            type:"post",
            url:"module/budget/bgt_brrutinho/viewdatabrho.php?module=cekdatasudahada",
            data:"uid="+iid+"&ukry="+ikry+"&ubln="+ibln+"&ukdperiode="+ikdperiode+"&up01="+ep01+"&up02="+ep02+"&udivisi="+idivisi,
            success:function(data){
                var tconfrm_d = myTrim(data);
                //var tjml = data.length;
                //alert(tconfrm_d);
                
                if (tconfrm_d=="boleh") {
                    
                    //simpan data ke DB
                    var iket_save="";
                    if (ket=="input") {
                        iket_save="pastikan tanda tangan terisi....!!! jika sudah terisi klik OK";
                    }else if (ket=="update") {
                        iket_save="Apakah akan update data...?";
                    }
                    
                    
                    if (iket_save=="") {
                        alert("tidak ada data yang disimpan...");
                        return false;
                    }
                    
                    
                    var cmt = confirm(iket_save);
                    if (cmt == false) {
                        return false;
                    }  
                        
                    //alert(tconfrm_d);
                    var uttd = data_img;//gambarnya

                    var myurl = window.location;
                    var urlku = new URL(myurl);
                    var module = urlku.searchParams.get("module");
                    var idmenu = urlku.searchParams.get("idmenu");
                    var act = urlku.searchParams.get("act");
                    
                    
                    document.getElementById("d-form1").action = "module/budget/bgt_brrutinho/aksi_brrutinho.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                    document.getElementById("d-form1").submit();
                    
                    return false;
                    
                }else{
                    alert(data);
                }
                
            }
        });
        
        
    }
    
    
</script>