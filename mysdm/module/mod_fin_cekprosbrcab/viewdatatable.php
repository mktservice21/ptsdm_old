<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];

    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $cket = $_POST['eket'];
    
    
    $_SESSION['PSFBRC_TGL1'] = $mytgl1;
    $_SESSION['PSFBRC_TGL2'] = $mytgl2;
    $_SESSION['PSFBRC_KET'] = $cket;
    
    
    $ptgl1= date("Y-m", strtotime($mytgl1));
    $ptgl2= date("Y-m", strtotime($mytgl2));
  
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DBRCABVDS01_".$userid."_$now ";
    $tmp02 =" dbtemp.DBRCABVDS02_".$userid."_$now ";
    $tmp03 =" dbtemp.DBRCABVDS03_".$userid."_$now ";
    $tmp04 =" dbtemp.DBRCABVDS04_".$userid."_$now ";
    
    
    $query = "select a.bridinputcab FROM dbmaster.t_br_cab a JOIN dbmaster.t_br_cab1 b on a.bridinputcab=b.bridinputcab WHERE "
            . " ( (DATE_FORMAT(b.tgl1,'%Y-%m') BETWEEN '$ptgl1' AND '$ptgl2') OR (DATE_FORMAT(b.tgl2,'%Y-%m') BETWEEN '$ptgl1' AND '$ptgl2') ) ";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "SELECT bridinputcab, tgl, karyawanid, karyawanid2, kode, "
            . " dokterid, jumlah, divisi, icabangid, aktivitas, tglex, jamex, jml_expired, validate, ifnull(validate_date,'0000-00-00') validate_date, userid, alasan_batal,"
            . " tglissued, tglbooking, current_date() tglnow, jabatanid, 
            ifnull(tgl_atasan1,'0000-00-00') tgl_atasan1,
            ifnull(tgl_atasan2,'0000-00-00') tgl_atasan2,
            ifnull(tgl_atasan3,'0000-00-00') tgl_atasan3,
            ifnull(tgl_atasan4,'0000-00-00') tgl_atasan4 "
            . " FROM dbmaster.t_br_cab WHERE "
            . " ( (DATE_FORMAT(tgl,'%Y-%m') BETWEEN '$ptgl1' AND '$ptgl2') OR bridinputcab IN (select distinct IFNULL(bridinputcab,'') FROM $tmp04) ) ";
    
    if (strtoupper($cket)!= "REJECT") $query.=" AND IFNULL(stsnonaktif,'') <> 'Y' ";

    if ( (strtoupper($cket)!="SEMUA") ) {
        if (strtoupper($cket)=="REJECT") {
            $query.=" AND IFNULL(stsnonaktif,'') = 'Y' ";
        }elseif (strtoupper($cket)=="BELUMAPVSM") {
            $query.=" AND ifnull(tgl_atasan3,'') = '' and ifnull(validate,'') = '' ";
        }elseif (strtoupper($cket)=="ISIISSUED") {
            $query.=" AND ifnull(tgl_atasan4,'') <> '' and ifnull(tglbooking,'') <> '' ";
        }else{
            if (strtoupper($cket)=="APPROVE") {
                
                $query.=" AND ifnull(tglissued,'') = '' ";
                
                $query.=" AND ( ifnull(tglbooking,'') = '' OR ( ifnull(tglbooking,'') <> '' AND DATE_FORMAT(NOW(),'%Y%m%d%H%i') > DATE_FORMAT(tglex,'%Y%m%d%H%i') ) ) ";
            }elseif (strtoupper($cket)=="UNAPPROVE") {
                $query.=" AND ifnull(tglbooking,'') <> '' ";
            }elseif (strtoupper($cket)=="PENDING") {

            }
        }
    }
                
    $query = "create TEMPORARY table $tmp01 ($query)"; 
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
    
    
    
    
    $nidagency[]="";
    $nnmagency[]="";
    $query ="select id_agency, nama_agency FROM dbmaster.t_agency WHERE IFNULL(aktif,'')<>'N'";
    $tampil= mysqli_query($cnmy, $query);
    while ($ni= mysqli_fetch_array($tampil)) {
        $nidagency[]=$ni['id_agency'];
        $nnmagency[]=$ni['nama_agency'];
    }
    $jmldata_ar=count($nidagency);
    
    $nstyle_txt=" style='text-align:right; background-color: transparent; border: 0px solid;' ";
?>
<script src="js/inputmask.js"></script>
<form method='POST' action='' id='d-form2' name='d-form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        
        <div class="title_left">
            <h4 style="font-size : 12px;">
                <?PHP
                    $noteket = strtoupper($cket);
                    $text="";
                    if ($noteket=="APPROVE") $text="Data Yang Belum DiProses";
                    if ($noteket=="UNAPPROVE") $text="Data Yang Sudah DiProses";
                    if ($noteket=="REJECT") $text="Data Yang DiReject";
                    if ($noteket=="PENDING") $text="Data Yang DiPending";
                    if ($noteket=="SEMUA") $text="Data Yang Belum dan Sudah Proses";
                    if ($noteket=="BELUMAPVSM") $text="Data Yang Belum Approve SM";

                    echo "<b>$text</b>";
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
                    <th width='50px'>Expired/<br/>Jam</th>
                    <th width='50px'>Agency</th>
                    <?PHP
                    //if (strtoupper($cket)=="ISIISSUED") {
                        echo "<th width='50px'>Tgl. Issued<br/>(bln/tgl/thn)</th>";
                    //}else{
                    //    echo "<th width='50px'></th>";
                    //}
                    ?>
                    <th width='30px'></th>
                    <th width='60px'>Tujuan/Kota</th>
                    <th width='60px'>Note</th>
                    <th width='60px'>Tgl. BR</th>
                    <th width='60px'>Yg Membuat</th>
                    <th width='80px'>Cabang</th>
                    <th width='100px'>Dokter</th>
                    <th width='100px'>Aktivitas</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $purutanid=1;
                $query = "select distinct bridinputcab, jml_expired, tglex, jamex, tglakhir1, tglakhir2, tglissued from $tmp04 order by bridinputcab";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $no_brurut=$no;
                    
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
                        
                        if (strtoupper($cket)=="ISIISSUED") {
                            if (!empty($ptglissued) AND $ptglissued<>"0000-00-00") {
                                $psudahed=false;
                                $pcolor1=" color:blue; ";
                            }
                        }
                        
                    }
                    
                    if (!empty($ptgled) AND $ptgled<>"0000-00-00") $ptgled= date("d/m/Y", strtotime($ptgled));
                    if (!empty($ptglissued) AND $ptglissued<>"0000-00-00") {
                        $piltglissued= date("M/d/Y", strtotime($ptglissued));
                        $ptglissued= date("Y-m-d", strtotime($ptglissued));
                    }
                    
                    $pprint="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=printentrybrdcccabang&brid=$pbrid&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$pbrid</a>";
                    
                    
                    
                    $txt_tglissued="<input type='date' value='$ptglissued' id='d_tgliss[$purutanid]' name='d_tgliss[$purutanid]' class='' size='5px'>";
                    
                    
                    
                    $txt_jmled="<input type='text' value='$pjmled' id='txtjamed[$purutanid]' name='txtjamed[$purutanid]' class='inputmaskrp2' autocomplete='off' size='5px' style='text-align:right;'>";
                    
                    $premoveissued="";
                    if (strtoupper($cket)=="ISIISSUED") {
                        $txt_jmled = "<span style='$pcolor1'><b>$ptgled $pjamed</b></span>";
                        
                        $premoveissued="<a href='#' class='btn btn-danger btn-xs' data-toggle='modal' "
                            . "onClick=\"disp_confirm_prosesissued('removeissued', '$pbrid')\"> "
                            . "Remove</a>";
                        
                    }else{
                        if (strtoupper($cket)=="UNAPPROVE") {
                            $txt_jmled = "$ptgled $pjamed";
                        }
                    }
                    
                    $query = "select * from $tmp04 where bridinputcab='$pbrid' order by bridinputcab, noid";
                    $tampil= mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        
                        $pbrid=$row['bridinputcab'];
                        $pjumlah=$row['jumlah'];
                        $ptglbr=$row['tgl'];
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
                        if (!empty($ptglnow2) AND $ptglnow2<>"0000-00-00") $ptglnow=$ptglnow2;
                    


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
                        $pidagency=$row['id_agency'];
                        $pnmagency=$row['nama_agency'];
                        $pstsbayar=$row['stsbayar'];
                        
                        $pstsimages=$row['sdhimage'];
                        
                        


                        $pilihagency_sel="";
                        $data_agn_sel="<option value='' selected>-- Pilih --</option>";
                        if ((double)$jmldata_ar>0) {
                            $xa=0;
                            for($xa=0;$xa<=$jmldata_ar;$xa++) {
                                if (isset($nidagency[$xa]) AND isset($nnmagency[$xa])) {
                                    if (!empty(trim($nidagency[$xa])) AND !empty(trim($nnmagency[$xa]))) {
                                        $n_sel_ar="";
                                        if ($nidagency[$xa]==$pidagency) {
                                            $n_sel_ar="selected";
                                            $pilihagency_sel=$nidagency[$xa];
                                        }
                                        $data_agn_sel .="<option value='$nidagency[$xa]' $n_sel_ar>$nnmagency[$xa]</option>";
                                    }
                                }
                            }
                        }
                        
                        $pcb_agency="<select class='input-sm' id='cbagency[$purutanid]' name='cbagency[$purutanid]' >$data_agn_sel</select>";
                        
                        if ($pstsbayar=="S") $pcb_agency="Byr. Sendiri";
                        
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


                        if (!empty($ptglnow)) $ptglnow= date("Ymd", strtotime($ptglnow));
                        if (!empty($ptglissued)) $ptglisudpilih= date("Ymd", strtotime($ptglissued));

                        if (!empty($ptglissued)) $ptglissued= date("Y-m-d", strtotime($ptglissued));
                        if (!empty($ptglbooking)) $ptglbooking= date("Y-m-d", strtotime($ptglbooking));


                        $apv1="";
                        $apv2="";
                        $apv3="";
                        $apv4="";

                        $pjabat = $row["jabatanid"];
                        if ((int)$pjabat==20 OR (int)$pjabat==5) {
                        }else{
                            if ($row["tgl_atasan1"] <> "0000-00-00") $apv1=date("d F Y, h:i:s", strtotime($row["tgl_atasan1"]));
                            if ($row["tgl_atasan2"] <> "0000-00-00") $apv2=date("d F Y, h:i:s", strtotime($row["tgl_atasan2"]));
                        }
                        if ($row["tgl_atasan3"] <> "0000-00-00") $apv3=date("d F Y, h:i:s", strtotime($row["tgl_atasan3"]));
                        if ($row["tgl_atasan4"] <> "0000-00-00") $apv4=date("d F Y, h:i:s", strtotime($row["tgl_atasan4"]));


                        if ($noteket=="REJECT") {
                            if (!empty($paktivitas)) $paktivitas=$paktivitas.", ".$palasan_batal;
                            else $paktivitas=$palasan_batal;
                        }

                        $txt_brid="<input type='text' value='$pbrid' id='txtbrid[$purutanid]' name='txtbrid[$purutanid]' class='' autocomplete='off' size='8px' Readonly >";
                        $txt_noid="<input type='text' value='$pnoidpilih' id='txtnoid[$purutanid]' name='txtnoid[$purutanid]' class='' autocomplete='off' size='8px' Readonly >";
                        $txt_jumlahrp="<input type='text' value='$prp_pilih' id='txtjmlrp[$purutanid]' name='txtjmlrp[$purutanid]' class='inputmaskrp2' autocomplete='off' size='6px' style='text-align:right;'>";
                        
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
                            $ppilih_jam2="<br/>$txt_jamberangkat2";
                        }else{
                            $ppilih_jam2="<span hidden>$txt_jamberangkat2</span>";
                        }
                        
                        
                        $f_textnya="<span hidden>$txt_brid $txt_noid</span>";


                        if (empty($ptglbooking)) {
                            $simpanbooking= "<input type='button' class='btn btn-dark btn-xs' id='s-submit' value='Save Booking' onclick=\"SimpanDataIssued('input', 'txtbrid[$pbrid]', 'd_tgliss[$pbrid]')\">";
                        }else{
                            $simpanbooking= "<input type='button' class='btn btn-dark btn-xs' id='s-submit' value='Hapus Booking' onclick=\"SimpanDataIssued('hapus', 'txtbrid[$pbrid]', 'd_tgliss[$pbrid]')\">";
                        }

                        $pedit="<button type='button' class='btn btn-warning btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"EditDataBR('$pbrid')\">Edit</button>";
                        
                        $pjumlah=number_format($pjumlah,0,",",",");
                        $ptglbr= date("d/m/Y", strtotime($ptglbr));

                        
                        
                        $ptglisudpilih="";
                        //CekPilihan('chkbox_br[$pbrid]', 'txtbrid[$pbrid]', 'txtjmlrp[$pbrid]', 'd_tglbook[$pbrid]')
                        $cekbox = "<input type=checkbox value='$purutanid' id='chkbox_br[$purutanid]' name='chkbox_br[]' class='chk_$pbrid' onclick=\"toggleCexBox(this)\">";
                        $pvalidate_fin="";
                        if ($row["validate_date"] <> "0000-00-00" AND !empty($row["validate_date"])) $pvalidate_fin=date("d F Y, h:i:s", strtotime($row["validate_date"]));

                        $p_warnaissu="";

                        if (strtoupper($cket)=="ISIISSUED") {
                            $prp_pilih=number_format($prp_pilih,0,",",",");
                            
                            $txt_jumlahrp=$prp_pilih;
                            $txt_jamberangkat1=$pjamtransaski1;
                            
                            $txt_jamberangkat2=$pjamtransaski2;
                            if ($pnoidpilih=="04") {
                                $ppilih_jam2="<p/>$txt_jamberangkat2";
                            }else{
                                $ppilih_jam2="<span hidden>$txt_jamberangkat2</span>";
                            }
                            
                            $pcb_agency=$pnmagency;
                            
                            
                            if ($psudahed==true) {
                                $cekbox="<span style='$pcolor1'>Expired</span>";
                                
                                $txt_tglissued="";
                            }
                            
                            if (!empty($ptglissued)) {
                                $cekbox=$premoveissued;
                            }

                        }else{
                            
                            $txt_tglissued=$piltglissued;
                            
                            if (strtoupper($cket)=="UNAPPROVE") {
                                
                                $prp_pilih=number_format($prp_pilih,0,",",",");

                                $txt_jumlahrp=$prp_pilih;
                                $txt_jamberangkat1=$pjamtransaski1;

                                $txt_jamberangkat2=$pjamtransaski2;
                                if ($pnoidpilih=="04") {
                                    $ppilih_jam2="<p/>$txt_jamberangkat2";
                                }else{
                                    $ppilih_jam2="<span hidden>$txt_jamberangkat2</span>";
                                }

                                $pcb_agency=$pnmagency;
                                
                                if (!empty($pvalidate_fin)) {
                                    $cekbox="val";
                                }
                                
                                if (!empty($ptglissued)) {
                                    $cekbox="";
                                }
                                
                                
                                if ((int)$pjabat==20 OR (int)$pjabat==5) {
                                    if (!empty($apv4)) $cekbox="";
                                }else{
                                    if (!empty($apv3) OR !empty($apv4)) $cekbox="";
                                }
                            }
                            
                        }
                        
                        $nnmbtnupload=" btn btn-success btn-xs ";
                        if ($pstsimages=="Y") $nnmbtnupload=" btn btn-danger btn-xs ";
                        
                        $nbtnlampiran="<button type='button' class='$nnmbtnupload' title='Upload Lampiran' data-toggle='modal' "
                                . " data-target='#myModal' "
                                . " onClick=\"getDataLampiran('$pbrid', '$pnoidpilih', '$puntukpilih')\">Upload</button>";

                        echo "<tr>";
                        echo "<td nowrap>$no_brurut</td>";
                        echo "<td nowrap><span >$cekbox $f_textnya</span></td>";
                        echo "<td nowrap>$pprint</td>";
                        echo "<td nowrap>$nbtnlampiran</td>";
                        echo "<td nowrap align='right'>$txt_jumlahrp</td>";
                        echo "<td nowrap>$pilih_tgl</td>";
                        echo "<td nowrap>$txt_jamberangkat1 $ppilih_jam2</td>";
                        echo "<td nowrap align='right'>$txt_jmled</td>";
                        echo "<td nowrap>$pcb_agency</td>";
                        echo "<td nowrap>$txt_tglissued</td>";
                        echo "<td nowrap $p_warnaissu><b>$puntukpilih</b></td>";
                        //echo "<td nowrap $p_warnaissu>$ptransaskipilih</td>";
                        echo "<td nowrap $p_warnaissu>$ptujuanpilih</td>";
                        echo "<td nowrap $p_warnaissu>$pnotespilih</td>";
                        echo "<td nowrap $p_warnaissu>$ptglbr</td>";
                        echo "<td nowrap $p_warnaissu>$pnmkaryawan</td>";
                        echo "<td nowrap>$pnamacabang</td>";
                        echo "<td nowrap>$pnamadokter</td>";
                        echo "<td nowrap>$paktivitas</td>";
                        echo "</tr>";

                        $purutanid++;
                        $no_brurut="";
                        $pprint="";
                        $txt_jmled="";
                        $txt_tglissued="";
                        $piltglissued="";
                        $premoveissued="";
                        
                    }
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>
        
    </div>
    
    <!-- tanda tangan -->
    <?PHP
    if (strtoupper($cket)=="ISIISSUED") {
        echo "<div class='col-sm-5'>";
        ?>
        <input type='button' class='btn btn-dark btn-sm' id="s-submit" value="Proses Issued" onclick='disp_confirm_prosesissued("Proses Issued...?", "prosesissued")'>
        <?PHP
        echo "</div>";
    }else{
        if ($noteket=="APPROVE") {
            echo "<div class='col-sm-5'>";
            include "ttd_cekprosbrfin.php";
            echo "</div>";
        }elseif ($noteket=="UNAPPROVE") {
            echo "<div class='col-sm-5'>";
            ?>
            <input type='button' class='btn btn-success btn-sm' id="s-submit" value="Un Proses" onclick='disp_confirm("Un Approve...?", "unapprove")'>
            <?PHP
            echo "</div>";
        }
    }
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
                { className: "text-right", "targets": [3] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9,10,11] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            "scrollY": 440,
            "scrollX": true/*,
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true*/
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);
        
        var nmtxt="";
        var ejmlrp ="0";
        var etglisu ="";
        if(button.value == 'select'){
            button.value = 'deselect'
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
                
                /*
                nmtxt="d_tgliss["+checkboxes[i].value+"]";
                var etgliss =document.getElementById(nmtxt).value;
                if (etgliss==""){
                    checkboxes[i].checked = false;
                }
                
               
                nmtxt="txtjmlrp["+checkboxes[i].value+"]";
                ejmlrp =document.getElementById(nmtxt).value;
                
                if (ejmlrp=="" || ejmlrp=="0"){
                    checkboxes[i].checked = false;
                }
                */
               
            }
            //button.value = 'deselect'
        }else{
            button.value = 'select';
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            //button.value = 'select';
        }
    }
    
    function CekPilihan(achkbx, aidbr, ajmlrp, atgliss)  {
        var eidbr =document.getElementById(aidbr).value;
        var ejmlrp =document.getElementById(ajmlrp).value;
        var etgliss =document.getElementById(atgliss).value;
        
        var checkboxes = document.getElementById(achkbx);
        
        if (checkboxes.checked==true) {
            if (eidbr==""){
                alert("id kosong....");
                checkboxes.checked=false;
                return 0;
            }

            if (etgliss==""){
                //alert("tanggal issued kosong....");
                //checkboxes.checked=false;
                //return 0;
            }

            if (ejmlrp=="" || ejmlrp=="0"){
                //alert("jumlah RP kosong....");
                //checkboxes.checked=false;
                //return 0;
            }
        }
        
    }
    //$fsimpan="'txtbrid[$pbrid]', 'txtjmlminta[$pbrid]', 'txtjmlrp[$pbrid]', 'txtjmlexp[$pbrid]'";
    function SimpanData(eact, aidbr, ajmlminta, ajmlrp, atglbok, aed)  {
        
        var eidbr =document.getElementById(aidbr).value;
        var ejmlminta =document.getElementById(ajmlminta).value;
        var ejmlrp =document.getElementById(ajmlrp).value;
        var etglbok =document.getElementById(atglbok).value;
        
    
        if (eidbr==""){
            alert("id kosong....");
            return 0;
        }
    
        if (etglbok==""){
            //alert("tanggal booking kosong....");
            //return 0;
        }
    
        if (ejmlrp=="" || ejmlrp=="0"){
            alert("jumlah RP kosong....");
            return 0;
        }
        
        //alert(eidbr+", "+ejmlminta+", "+ejmlrp+", "+ejmlexp); return 0;
        var pText_="Simpan";
        if (eact=="hapus") var pText_="Hapus";
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/mod_fin_cekprosbrcab/simpan_data.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidbr="+eidbr+"&ujmlminta="+ejmlminta+"&ujmlrp="+ejmlrp+"&utglbok="+etglbok+"&usudahed="+aed,
                    success:function(data){
                        if (data.length > 1) {
                            alert(data);
                        }
                        if (eact=="hapus" && data.length <= 1) {
                            //document.getElementById(ejmlrp).value="";
                        }
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function SimpanDataIssued(eact, aidbr, atglbook){
        var eidbr =document.getElementById(aidbr).value;
        var etglbook =document.getElementById(atglbook).value;
        
    
        if (eidbr==""){
            alert("id kosong....");
            return 0;
        }
    
        if (etglbook=="" && eact=="input"){
            alert("tanggal booking kosong....");
            return 0;
        }
        
        var pText_="Simpan";
        if (eact=="hapus") var pText_="Hapus";
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/mod_fin_cekprosbrcab/simpan_data_issued.php?module="+module+"&act="+eact+"&idmenu="+idmenu,
                    data:"uidbr="+eidbr+"&utglbook="+etglbook,
                    success:function(data){
                        if (data.length > 1) {
                            alert(data);
                            pilihData('isiissued');
                        }
                        if (eact=="hapus" && data.length <= 1) {
                            //document.getElementById(ejmlrp).value="";
                        }
                    }
                });
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
    }
    
    function disp_confirm(pText_,ket)  {
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                
                
                var iketalasan="";
                if (ket=="reject") {
                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                    if (textket == null || textket == "") {
                        iketalasan = textket;
                    } else {
                        iketalasan = textket;
                    }
                }
                    
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/mod_fin_cekprosbrcab/aksi_cekprosbrcab.php?module="+module+"&act="+ket+"&idmenu="+idmenu+"&ukethapus="+iketalasan;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function disp_confirm_prosesissued(pText_,ket)  {
        //Remove Tgl. Issued...???
        var iidremove="";
        if (pText_=="removeissued") {
            iidremove=ket;
            ket=pText_;
            
            pText_="Remove Tgl. Issued...???";
        }
        //alert(pText_+", "+ket+", "+iidremove); return false;
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                
                
                var iketalasan="";
                if (ket=="reject") {
                    var textket = prompt("Masukan alasan "+ket+" : ", "");
                    if (textket == null || textket == "") {
                        iketalasan = textket;
                    } else {
                        iketalasan = textket;
                    }
                }
                    
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/mod_fin_cekprosbrcab/aksi_prosesissued.php?module="+module+"&act="+ket+"&idmenu="+idmenu+"&ukethapus="+iketalasan+"&id="+iidremove;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    
    function getDataLampiran(dbrid, didinput, dnmjenis){
        $.ajax({
            type:"post",
            url:"module/mod_fin_cekprosbrcab/upload_lamp.php?module=uploadlampiran",
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