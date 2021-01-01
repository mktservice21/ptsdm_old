<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    
    $pgroupuser = trim($_SESSION['GROUP']);
    
    $cket = $_POST['eket'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $karyawan = $_POST['ukaryawan'];
    $lvlposisi = $_POST['ulevel'];
    $stsapv = $_POST['uketapv'];
    
    
    $pidcardapv = $_SESSION['IDCARD'];
    $ppilregion = $_SESSION['REGION'];
    if ($pgroupuser=="1") {
        $pidcardapv = $_POST['uidapvcard'];
        $ppilregion = $_POST['uregionpil'];
    }
    
    
    
    $_SESSION['APVRUT_KET'] = $cket;
    $_SESSION['APVRUT_TGL1'] = $mytgl1;
    $_SESSION['APVRUT_TGL2'] = $mytgl2;
    $_SESSION['APVRUT_KRY'] = $karyawan;
    $_SESSION['APVRUT_LVL'] = $lvlposisi;
    $_SESSION['APVRUT_STSAPV'] = $stsapv;
    
    $ptgl1= date("Y-m", strtotime($mytgl1));
    $ptgl2= date("Y-m", strtotime($mytgl2));
    
    
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DBRCABVDSB01_".$userid."_$now ";
    $tmp02 =" dbtemp.DBRCABVDSB02_".$userid."_$now ";
    $tmp03 =" dbtemp.DBRCABVDSB03_".$userid."_$now ";
    $tmp04 =" dbtemp.DBRCABVDSB04_".$userid."_$now ";
    
    
    $query = "select a.bridinputcab FROM dbmaster.t_br_cab a JOIN dbmaster.t_br_cab1 b on a.bridinputcab=b.bridinputcab WHERE "
            . " ( (DATE_FORMAT(b.tgl1,'%Y-%m') BETWEEN '$ptgl1' AND '$ptgl2') OR (DATE_FORMAT(b.tgl2,'%Y-%m') BETWEEN '$ptgl1' AND '$ptgl2') ) ";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $sql = "SELECT bridinputcab, tgl, karyawanid, karyawanid2, kode, "
        . " dokterid, jumlah, divisi, icabangid, aktivitas, tglex, jamex, jml_expired, validate, userid, alasan_batal,
            tglissued, tglbooking, 
            ifnull(tgl_atasan1,'0000-00-00') tgl_atasan1,
            ifnull(tgl_atasan2,'0000-00-00') tgl_atasan2,
            ifnull(tgl_atasan3,'0000-00-00') tgl_atasan3,
            ifnull(tgl_atasan4,'0000-00-00') tgl_atasan4,
            ifnull(validate_date,'0000-00-00') validate_date, jabatanid, current_date() tglnow "
        . " "
        . " FROM dbmaster.t_br_cab WHERE "
        . " ( (DATE_FORMAT(tgl,'%Y-%m') BETWEEN '$ptgl1' AND '$ptgl2') OR bridinputcab IN (select distinct IFNULL(bridinputcab,'') FROM $tmp04) ) ";
    
    if (strtoupper($cket)!= "REJECT") $sql.=" AND stsnonaktif <> 'Y' ";
    
    $filterbawahan = "";
    $atasannya = "";
    $tglatasannya = "";
    $tglatasannya_atas = "";
    $tglatasannya_bawah = "";
    $tglatasannya1 = "";
    $filterapv = "";
    $filterregion = "";
    $filterjabatangsm = "";

    if ($lvlposisi=="FF2") {
        $atasannya = "atasan1";
        $tglatasannya = "tgl_atasan1";
            $tglatasannya_atas = "tgl_atasan2";
    }elseif ($lvlposisi=="FF3") {
        $atasannya = "atasan2";
        $tglatasannya = "tgl_atasan2";
            $tglatasannya_bawah = "tgl_atasan1";
            $tglatasannya_atas = "tgl_atasan3";
    }elseif ($lvlposisi=="FF4") {
        $atasannya = "atasan3";
        $tglatasannya = "tgl_atasan3";
            $tglatasannya_bawah = "tgl_atasan2";
            $tglatasannya_atas = "tgl_atasan4";
    }elseif ($lvlposisi=="FF5" OR $lvlposisi=="FF7") {
        $tglatasannya = "tgl_atasan4";
            $tglatasannya_bawah = "tgl_atasan3";

            //khusus
            if ($pidcardapv=="0000000159"){
                $apvnyaats4="";
                if (strtoupper($cket)=="APPROVE") {
                    $apvnyaats4=" AND ifnull(tgl_atasan4,'')='' ";
                }elseif (strtoupper($cket)=="UNAPPROVE") {
                    $apvnyaats4=" AND ifnull(tgl_atasan4,'')<>'' AND ifnull(brid,'')='' ";
                }elseif (strtoupper($cket)=="SUDAHFIN") {
                    $apvnyaats4=" AND ifnull(brid,'')<>'' ";
                }
                $filterjabatangsm = " AND jabatanid in ('20', '05') OR (kode=1 AND jabatanid='38' $apvnyaats4 AND stsnonaktif <> 'Y' AND karyawanid in (select distinct karyawanid from dbmaster.t_karyawan_app_gsm where gsm='$pidcardapv')) ";
            }else{
                $filterjabatangsm = " AND jabatanid in ('20', '05') ";
            }
        if (!empty($ppilregion))
            $filterregion = " AND (icabangid in (select icabangid from dbmaster.v_penempatan_des WHERE region='$ppilregion') OR karyawanid='$pidcardapv')";
    }else{

    }

    if (!empty($atasannya)) $filterbawahan = " $atasannya='$karyawan'"; //bawahan
    if (!empty($tglatasannya)) $tglatasannya = "ifnull($tglatasannya,'')"; //tanggal approve
    if (!empty($tglatasannya_bawah)) $tglatasannya_bawah = " AND ifnull($tglatasannya_bawah,'')<>'' "; //tanggal approve palingatas
    if (!empty($tglatasannya_atas)) $tglatasannya_atas = " AND ifnull($tglatasannya_atas,'')='' "; //tanggal approve palingatas


    if (strtoupper($cket)=="APPROVE") {
        if (!empty($tglatasannya)) $filterapv = " $tglatasannya=''"; 
    }elseif (strtoupper($cket)=="UNAPPROVE") {
        if (!empty($tglatasannya)) $filterapv = " $tglatasannya<>''";
    }elseif (strtoupper($cket)=="REJECT") {
        $sql.=" AND stsnonaktif = 'Y' ";
        if ($lvlposisi=="FF5" OR $lvlposisi=="FF7") {
            $sql.=" AND karyawanid = '$pidcardapv' ";
        }
    }elseif (strtoupper($cket)=="PENDING") {

    }


    if ( (strtoupper($cket)!="SEMUA") AND strtoupper($cket)!= "REJECT" AND strtoupper($cket)!= "SUDAHFIN") {
        $sql .= " AND ifnull(brid,'')='' AND IFNULL(tglissued,'')='' "; //sudah validate

        if (!empty($filterapv)) $sql .= " AND $filterapv "; //filter tanggal approve
        if (!empty($tglatasannya_bawah)) $sql .= " $tglatasannya_bawah "; //filter tanggal approve paling atas
        if (!empty($tglatasannya_atas)) $sql .= " $tglatasannya_atas "; //filter tanggal approve paling atas
    }
    if (!empty($filterbawahan)) $sql .= " AND $filterbawahan "; //filter bawahan

    if (strtoupper($cket)== "SUDAHFIN") $sql .= " AND ifnull(brid,'')<>'' "; //sudah fin
    
    if ($lvlposisi=="FF5") {
        $sql .= " AND ifnull(atasan4,'')='$pidcardapv' ";
    }
    //echo $sql;
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "drop temporary table $tmp04");
    
    $query = "SELECT a.*, b.nama nama_cabang, c.nama nama_dokter, d.nama nama_karyawan, e.nama nama_mr, f.nama nama_user FROM $tmp01 a LEFT JOIN MKT.icabang b on a.icabangid=b.icabangid "
            . " LEFT JOIN hrd.dokter c on a.dokterid=c.dokterId "
            . " LEFT JOIN hrd.karyawan d on a.karyawanid=d.karyawanId"
            . " LEFT JOIN hrd.karyawan e on a.karyawanid2=e.karyawanId"
            . " LEFT JOIN hrd.karyawan f on a.userid=f.karyawanId";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select * from dbmaster.t_br_cab1 WHERE bridinputcab IN (select distinct IFNULL(bridinputcab,'') FROM $tmp02)";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.noid, b.jenistiket, b.kota1, b.kota2, b.tgl1, b.tgl2, b.jam1, b.jam2, b.rp, b.notes, b.id_agency, c.nama_agency, IFNULL(b.stsbayar,'') as stsbayar, DATE_FORMAT(tglex,'%Y%m%d%H%i') tglakhir1,  DATE_FORMAT(NOW(),'%Y%m%d%H%i') tglakhir2, CAST('' as CHAR(1)) as sdhimage "
            . " from $tmp02 a JOIN $tmp03 b on a.bridinputcab=b.bridinputcab LEFT JOIN dbmaster.t_agency c on b.id_agency=c.id_agency";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "drop temporary table $tmp02");
    $query = "SELECT distinct bridinputcab, noid From dbimages.img_br_cab1 WHERE CONCAT(bridinputcab, noid) IN "
            . " (select DISTINCT IFNULL(CONCAT(bridinputcab, noid),'') FROM $tmp03)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp04 a JOIN $tmp02 b on a.bridinputcab=b.bridinputcab AND a.noid=b.noid SET a.sdhimage='Y'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>

<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <div hidden>
        <input type='text' class='form-control' id='e_lvlposisi_p' name='e_lvlposisi_p' value='<?PHP echo $lvlposisi; ?>' Readonly>
        <input type='text' class='form-control' id='e_regionp_p' name='e_regionp_p' value='<?PHP echo $ppilregion; ?>' Readonly>
        <input type='text' class='form-control' id='e_idkaryawan_p' name='e_idkaryawan_p' value='<?PHP echo $pidcardapv; ?>' Readonly>
        <input type='text' class='form-control input-sm' id='e_ketapv_p' name='e_ketapv_p' value='<?PHP echo $stsapv; ?>' Readonly>
    </div>                     
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
        <div class="title_left">
            <h4 style="font-size : 12px;">
                <?PHP
                    $noteket = strtoupper($cket);
                    $apvby = "";
                    if ($lvlposisi=="FF2") $apvby = "SPV / AM";
                    if ($lvlposisi=="FF3") $apvby = "DM";
                    if ($lvlposisi=="FF4") $apvby = "SM";
                    if (!empty($apvby)) $apvby = ".&nbsp; &nbsp; Status Karyawan : $apvby";
                    $text="";
                    if ($noteket=="APPROVE") $text="Data Yang Belum DiApprove";
                    if ($noteket=="UNAPPROVE") $text="Data Yang Sudah DiApprove";
                    if ($noteket=="REJECT") $text="Data Yang DiReject";
                    if ($noteket=="PENDING") $text="Data Yang DiPending";
                    if ($noteket=="SEMUA") $text="Data Yang Belum dan Sudah Approve";

                    echo "<b>$text $apvby"
                            . "<p/>&nbsp;</b>";
                ?>
            </h4>
        </div>
        <div class="clearfix"></div>
        
            
        <table id='datatableprosbrcabfin' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='20px'>
                        <input type="checkbox" id="chkbtnbr" value="select" onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='30px'>ID</th>
                    <th width='30px'>Lampiran</th>
                    <th width='50px'>Harga Rp.</th>
                    <th width='50px'>Tgl<br/>Berangkat</th>
                    <th width='50px'>Jam<br/>Berangkat,<br/>Kembali</th>
                    <th width='50px'>Expired / Jam</th>
                    <th width='50px'>Agency</th>
                    <th width='50px'>Tgl. Issued</th>
                    <th width='30px'></th>
                    <th width='60px'>Tujuan/Kota</th>
                    <th width='60px'>Note</th>
                    <th width='60px'>Tgl. BR</th>
                    <th width='60px'>Yg Membuat</th>
                    <th width='80px'>Cabang</th>
                    <th width='100px'>Dokter</th>
                    <th width='100px'>Aktivitas</th>
                    <th width='30px'>Approve SPV/AM</th>
                    <th width='30px'>Approve DM</th>
                    <th width='30px'>Approve SM</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $purutanid=1;
                $query = "select distinct bridinputcab, jml_expired, tglex, jamex, tglakhir1, tglakhir2, tglissued from $tmp04 order by bridinputcab";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pbrid=$row1['bridinputcab'];
                    $pjmled=$row1['jml_expired'];
                    $ptgled=$row1['tglex'];
                    $pjamed=$row1['jamex'];
                    
                    $ptglakhir1=$row1['tglakhir1'];
                    $ptglakhir2=$row1['tglakhir2'];
                    
                    $piltglissued="";
                    $ptglissued=$row1['tglissued'];
                    
                    $psudahed=false;
                    $pcolor1=" color:blue; ";
                    if ((double)$ptglakhir2>(double)$ptglakhir1) {
                        $pcolor1=" color:red; ";
                        
                        $psudahed=true;
                        
                        if (!empty($ptglissued) AND $ptglissued<>"0000-00-00") {
                            $psudahed=false;
                            $pcolor1=" color:blue; ";
                        }
                        
                    }
                    
                    
                    if (!empty($ptglissued) AND $ptglissued<>"0000-00-00") {
                        $piltglissued= date("d/M/Y", strtotime($ptglissued));
                        $ptglissued= date("Y-m-d", strtotime($ptglissued));
                    }
                    
                    if (!empty($ptgled) AND $ptgled<>"0000-00-00") $ptgled= date("d/m/Y", strtotime($ptgled));
                    
                    $txt_jmled = "<span style='$pcolor1'><b>$ptgled $pjamed</b></span>";
                    
                    $pprint="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=printentrybrdcccabang&brid=$pbrid&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$pbrid</a>";
                        
                    $no_brurut=$no;
                
                    $query = "select * from $tmp04 where bridinputcab='$pbrid' order by bridinputcab, noid";
                    $tampil= mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        $pbrid=$row['bridinputcab'];
                        $pjumlah=$row['jumlah'];
                        $ptgl=$row['tgl'];
                        $pkaryawanid=$row['karyawanid'];
                        $pnmkaryawan=$row['nama_karyawan'];
                        $pmrid=$row['karyawanid2'];
                        $pnamamr=$row['nama_mr'];
                        $pnamacabang=$row['nama_cabang'];
                        $pnamadokter=$row['nama_dokter'];
                        $pjmlex=$row['jml_expired'];
                        $paktivitas=$row['aktivitas'];
                        $palasan_batal=$row['alasan_batal'];

                        $ptglissued=$row['tglissued'];
                        $ptglbooking=$row['tglbooking'];
                        $ptglnow=$row['tglnow'];
                        $ptglnow2=$row['tgl_atasan4'];
                        
                        
                        $pnoidpilih=$row['noid'];
                        $pjnspiltiket=$row['jenistiket'];
                        $ptgltransaski1=$row['tgl1'];
                        $ptgltransaski2=$row['tgl2'];
                        $pjamtransaski1=$row['jam1'];
                        $pjamtransaski2=$row['jam2'];
                        $pkotatransaski1=$row['kota1'];
                        $pkotatransaski2=$row['kota2'];
                        $pnotespilih=$row['notes'];
                        $prp_pilih=$row['rp'];
                        $pnmagency=$row['nama_agency'];
                        $pstsbayar=$row['stsbayar'];
                        
                        $pstsimages=$row['sdhimage'];
                        
                        if ($pstsbayar=="S") $pnmagency="Byr. Sendiri";

                        //$ptgltransaski1= date("d/m/Y", strtotime($ptgltransaski1));
                        //$ptgltransaski2= date("d/m/Y", strtotime($ptgltransaski2));

                        $pjenistiket="";
                        if ($pjnspiltiket=="K") $pjenistiket="KAI";
                        elseif ($pjnspiltiket=="P") $pjenistiket="PESAWAT";

                        $puntukpilih="";
                        if ($pnoidpilih=="01") $puntukpilih="TIKET $pjenistiket PERGI";
                        elseif ($pnoidpilih=="02") $puntukpilih="TIKET $pjenistiket PULANG";
                        elseif ($pnoidpilih=="03") $puntukpilih="HOTEL";
                        elseif ($pnoidpilih=="04") $puntukpilih="SEWA KENDARAAN";

                        if (!empty($pjamtransaski1)) $ptransaskipilih="$ptgltransaski1 | $pjamtransaski1";
                        else $ptransaskipilih="$ptgltransaski1";

                        if (!empty($pkotatransaski2)) $ptujuanpilih="$pkotatransaski1 - $pkotatransaski2";
                        else $ptujuanpilih="$pkotatransaski1";
                        
                        
                        
                        if (!empty($ptglnow2) AND $ptglnow2<>"0000-00-00") $ptglnow=$ptglnow2;
                        
                        $ptglisudpilih="";

                        if (!empty($ptglnow)) $ptglnow= date("Ymd", strtotime($ptglnow));
                        if (!empty($ptglissued)) $ptglisudpilih= date("Ymd", strtotime($ptglissued));

                        if (!empty($ptglissued)) $ptglissued= date("d/m/Y", strtotime($ptglissued));
                        if (!empty($ptglbooking)) $ptglbooking= date("d/m/Y", strtotime($ptglbooking));



                        
                        $txt_tglberangkat1="<input type='date' value='$ptgltransaski1' id='txttglberangkat1[$purutanid]' name='txttglberangkat1[$purutanid]' class='' size='8px' >";
                        $txt_tglberangkat1= "<b>".date("d/m/Y", strtotime($ptgltransaski1))."</b>";
                        
                        $txt_tglberangkat2="<input type='date' value='$ptgltransaski2' id='txttglberangkat2[$purutanid]' name='txttglberangkat2[$purutanid]' class='' size='8px' >";
                        $txt_tglberangkat2= "<b>".date("d/m/Y", strtotime($ptgltransaski2))."</b>";
                        
                        $txt_jamberangkat1="<input type='text' value='$pjamtransaski1' id='txtjamberangkat1[$purutanid]' name='txtjamberangkat1[$purutanid]' class='maskpersen2' data-inputmask=\"'mask': '99:99'\" size='5px' >";
                        //$txt_jamberangkat=$pjamtransaski1;
                        
                        $pilih_tgl="$txt_tglberangkat1";
                        if ($pnoidpilih=="03" OR $pnoidpilih=="04") {
                            $pilih_tgl="$txt_tglberangkat1 s/d.<p/>$txt_tglberangkat2";
                        }
                        
                        $txt_jamberangkat2="<input type='text' value='$pjamtransaski2' id='txtjamberangkat2[$purutanid]' name='txtjamberangkat2[$purutanid]' class='maskpersen2' data-inputmask=\"'mask': '99:99'\" size='5px' >";
                        if ($pnoidpilih=="04") {
                            $ppilih_jam2="<p/>$pjamtransaski2";
                        }else{
                            $ppilih_jam2="<span hidden>$pjamtransaski2</span>";
                        }
                        
                        
                        $cekbox = "<input type=checkbox value='$pbrid' id='chkbox_br[$pbrid]' name='chkbox_br[]' class='chk_$pbrid' onclick=\"toggleCexBox(this)\">";
                        
                        
                        if ($noteket=="REJECT") {
                            if (!empty($paktivitas)) $paktivitas=$paktivitas.", ".$palasan_batal;
                            else $paktivitas=$palasan_batal;
                        }

                        if ($noteket!="APPROVE") {
                            $simpandata="";
                        }

                        $pjumlah=number_format($pjumlah,0,",",",");
                        $prp_pilih=number_format($prp_pilih,0,",",",");
                        $ptgl= date("d/m/Y", strtotime($ptgl));


                        $apv1="";
                        $apv2="";
                        $apv3="";
                        $apv4="";
                        $pvalidate_fin="";

                        $pjabat = $row["jabatanid"];
                        if ((int)$pjabat==20 OR (int)$pjabat==5) {
                        }else{
                            if ($row["tgl_atasan1"] <> "0000-00-00") $apv1=date("d F Y, h:i:s", strtotime($row["tgl_atasan1"]));
                            if ($row["tgl_atasan2"] <> "0000-00-00") $apv2=date("d F Y, h:i:s", strtotime($row["tgl_atasan2"]));
                        }
                        if ($row["tgl_atasan3"] <> "0000-00-00") $apv3=date("d F Y, h:i:s", strtotime($row["tgl_atasan3"]));
                        if ($row["tgl_atasan4"] <> "0000-00-00") $apv4=date("d F Y, h:i:s", strtotime($row["tgl_atasan4"]));

                        if ($row["validate_date"] <> "0000-00-00" AND !empty($row["validate_date"])) $pvalidate_fin=date("d F Y, h:i:s", strtotime($row["validate_date"]));

                        if (empty($ptglbooking)) {
                            $cekbox="";
                        }

                        $p_warnaissu="";
                        if (empty($ptglisudpilih)) {

                        }else{
                            if ( ((double)$ptglisudpilih<(double)$ptglnow) ) {
                                if (strtoupper($cket)=="APPROVE") {
                                    $cekbox="";
                                }
                                $p_warnaissu=" style='color:red;' ";
                                $print=$pbrid;
                            }
                        }
                        
                        if ($psudahed==true AND strtoupper($cket)!="UNAPPROVE" AND !empty($ptgled)) {
                            $cekbox="<span style='$pcolor1'>Expired</span>";
                        }

                        $nnmbtnupload=" btn btn-danger btn-xs ";
                        
                        $nbtnlampiran="<button type='button' class='$nnmbtnupload' title='Lihat' data-toggle='modal' "
                                . " data-target='#myModal' "
                                . " onClick=\"getDataLampiran('$pbrid', '$pnoidpilih', '$puntukpilih')\">Lihat</button>";
                        if ($pstsimages!="Y") $nbtnlampiran="";
                        
                        
                        echo "<tr>";
                        echo "<td nowrap>$no_brurut</td>";
                        echo "<td nowrap>$cekbox</td>";
                        echo "<td nowrap $p_warnaissu>$pprint</td>";
                        echo "<td nowrap>$nbtnlampiran</td>";
                        echo "<td nowrap align='right'>$prp_pilih</td>";
                        echo "<td nowrap $p_warnaissu>$pilih_tgl</td>";
                        echo "<td nowrap $p_warnaissu>$pjamtransaski1 $ppilih_jam2</td>";
                        echo "<td nowrap>$txt_jmled</td>";
                        echo "<td nowrap>$pnmagency</td>";
                        echo "<td nowrap>$piltglissued</td>";
                        echo "<td nowrap>$puntukpilih</td>";
                        echo "<td nowrap>$ptujuanpilih</td>";
                        echo "<td nowrap>$pnotespilih</td>";
                        echo "<td nowrap>$ptgl</td>";
                        echo "<td nowrap>$pnmkaryawan</td>";
                        echo "<td nowrap>$pnamacabang</td>";
                        echo "<td nowrap>$pnamadokter</td>";
                        echo "<td nowrap>$paktivitas</td>";
                        echo "<td>$apv1</td>";
                        echo "<td>$apv2</td>";
                        echo "<td>$apv3</td>";
                        echo "</tr>";

                        $purutanid++;
                        $no_brurut="";
                        $pprint="";
                        $piltglissued="";
                        
                    }
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>
        
            
    </div>
    
    <?PHP
    if (strtoupper($cket)=="UNAPPROVE") {
    ?>
        <div class='clearfix'></div>
        <div class="well" style="margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">
            <?PHP
            if (strtoupper($cket)=="APPROVE") {
                ?>
                <input class='btn btn-default' type='hidden' name='buttonapv' value='Pending' 
                       onClick="ProsesData('pending', 'chkbox_br[]')">
                <?PHP
            }elseif (strtoupper($cket)=="UNAPPROVE") {
                ?>
                <input class='btn btn-success' type='button' name='buttonapv' value='UnApprove' 
                       onClick="ProsesData('unapprove', 'chkbox_br[]')">
                <?PHP
            }elseif (strtoupper($cket)=="REJECT") {
            }elseif (strtoupper($cket)=="PENDING") {
            }elseif (strtoupper($cket)=="SEMUA") {
            }
            ?>
        </div>
    <?PHP
    }
    ?>
    
    <div class='clearfix'></div>

    <!-- tanda tangan -->
    <?PHP
        //if ($pgroupuser=="1") {            
        //}else{
            if (strtoupper($cket)=="APPROVE") {
                echo "<div class='col-sm-5'>";
                include "ttd_apvbrcab.php";
                echo "</div>";
            }
        //}
    ?>
</form>

<script type="text/javascript">
    function toggleCexBox(source) {
        var aInputs = document.getElementsByTagName('input');
        for (var i=0;i<aInputs.length;i++) {
            if (aInputs[i] != source && aInputs[i].className == source.className) {
                aInputs[i].checked = source.checked;
            }
        }
    }
</script>


<script>
    $(document).ready(function() {
        var dataTable = $('#datatableprosbrcabfin').DataTable( {
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "ordering": false,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [4] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9,10,11,12,13] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
    }
    
    
    function ProsesData(ket, cekbr){
        var cmt = confirm('Apakah akan melakukan proses '+ket+' ...?');
        if (cmt == false) {
            return false;
        }
        var chk_arr =  document.getElementsByName(cekbr);
        var chklength = chk_arr.length;             
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        var txt;
        if (ket=="reject" || ket=="pending") {
            var textket = prompt("Masukan alasan "+ket+" : ", "");
            if (textket == null || textket == "") {
                txt = textket;
            } else {
                txt = textket;
            }
        }
        
        var ekaryawan=document.getElementById('e_idkaryawan_p').value;
        var elevel=document.getElementById('e_lvlposisi_p').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_apv_brcab/aksi_apvbrcab.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=approve"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ulevel="+elevel+"&ketrejpen="+txt,
            success:function(data){
                pilihData(ket);
                alert(data);
            }
        });
        
    }
    
    function getDataLampiran(dbrid, didinput, dnmjenis){
        $.ajax({
            type:"post",
            url:"module/mod_fin_prosbrcab/upload_lamp.php?module=uploadlampiran",
            data:"ubrid="+dbrid+"&uidinput="+didinput+"&unmjenis="+dnmjenis,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #datatableprosbrcabfin th {
        font-size: 13px;
    }
    #datatableprosbrcabfin td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop temporary table $tmp01");
    mysqli_query($cnmy, "drop temporary table $tmp02");
    mysqli_query($cnmy, "drop temporary table $tmp03");
    mysqli_query($cnmy, "drop temporary table $tmp04");
    
?>