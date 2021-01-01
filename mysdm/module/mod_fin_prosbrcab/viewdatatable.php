<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];

    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $cket = $_POST['eket'];
    
    
    $_SESSION['PSFBRCFIN_TGL1'] = $mytgl1;
    $_SESSION['PSFBRCFIN_TGL2'] = $mytgl2;
    $_SESSION['PSFBRCFIN_KET'] = $cket;
    
    
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
    
    
    $query = "SELECT bridinputcab, brid, tgl, karyawanid, karyawanid2, kode, coa4, "
            . " dokterid, jumlah, divisi, icabangid, aktivitas, tglex, jamex, jml_expired, validate, userid, alasan_batal,
            tglissued, tglbooking, 
            ifnull(tgl_atasan1,'0000-00-00') tgl_atasan1,
            ifnull(tgl_atasan2,'0000-00-00') tgl_atasan2,
            ifnull(tgl_atasan3,'0000-00-00') tgl_atasan3,
            ifnull(tgl_atasan4,'0000-00-00') tgl_atasan4,
            ifnull(validate_date,'0000-00-00') validate_date, jabatanid "
            . " FROM dbmaster.t_br_cab WHERE "
            . " ( (DATE_FORMAT(tgl,'%Y-%m') BETWEEN '$ptgl1' AND '$ptgl2') OR bridinputcab IN (select distinct IFNULL(bridinputcab,'') FROM $tmp04) ) ";
    
    if (strtoupper($cket)!= "REJECT") $query.=" AND IFNULL(stsnonaktif,'') <> 'Y' ";

    if ( (strtoupper($cket)!="SEMUA") ) {
        if (strtoupper($cket)=="REJECT") {
            $query.=" AND IFNULL(stsnonaktif,'') = 'Y' ";
        }elseif (strtoupper($cket)=="BELUMAPVSM") {
            $query.=" AND ifnull(tgl_atasan3,'') = '' and ifnull(validate,'') = '' ";
        }else{
            if (strtoupper($cket)=="APPROVE") {
                $query.=" AND ifnull(brid,'') = '' ";
            }elseif (strtoupper($cket)=="UNAPPROVE") {
                $query.=" AND ifnull(brid,'') <> '' ";
            }elseif (strtoupper($cket)=="VALIDATE") {
                $query.=" AND ifnull(brid,'') = '' AND ifnull(tglissued,'') = '' ";
            }
        }
    }
                
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "drop temporary table $tmp04");
    
    
    $query = "SELECT a.*, b.nama nama_cabang, c.nama nama_dokter, d.nama nama_karyawan, e.nama nama_mr, f.nama nama_user, "
            . " g.nama nama_kode, h.NAMA4 "
            . " FROM $tmp01 a LEFT JOIN MKT.icabang b on a.icabangid=b.icabangid "
            . " LEFT JOIN hrd.dokter c on a.dokterid=c.dokterId "
            . " LEFT JOIN hrd.karyawan d on a.karyawanid=d.karyawanId "
            . " LEFT JOIN hrd.karyawan e on a.karyawanid2=e.karyawanId "
            . " LEFT JOIN hrd.karyawan f on a.userid=f.karyawanId "
            . " LEFT JOIN hrd.br_kode g on a.kode=g.kodeid "
            . " LEFT JOIN dbmaster.coa_level4 h on a.coa4=h.COA4";
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
                    if ($noteket=="VALIDATE") $text="VALIDASI DATA";

                    echo "<b>$text</b>";
                ?>
            </h4>
        </div>
        <div class="clearfix"></div>
        
        <table id='datatableprosbrfin' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='20px'>
                        <input type="checkbox" id="chkbtnbr" value="select" onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='30px'>ID</th>
                    <th width='30px'>Lampiran</th>
                    <th width='30px'></th>
                    <th width='50px'>Tgl<br/>Berangkat</th>
                    <th width='50px'>Jam<br/>Berangkat,<br/>Kembali</th>
                    <th width='50px'>Harga Rp.</th>
                    <th width='30px'>Agency</th>
                    <th width='30px'>Tgl. Booking</th>
                    <th width='60px'>Tgl. Issued</th>
                    <th width='60px'>Yg Membuat</th>
                    <th width='80px'>Cabang</th>
                    <th width='100px'>Dokter</th>
                    <th width='100px'>Aktivitas</th>
                    <th width='100px'>Divisi</th>
                    <th width='100px'>ID BR</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $purutanid=1;
                $query = "select distinct bridinputcab, jml_expired, tglex, jamex, tglakhir1, tglakhir2, tglissued, tglbooking, tgl_atasan4, validate_date from $tmp04 order by bridinputcab";
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
                    $ptglbooking=$row1['tglbooking'];
                    $ptglissued=$row1['tglissued'];
                    $ptglvalidate=$row1['validate_date'];
                    $ptglatasan4=$row1['tgl_atasan4'];
                    
                    if ($ptglvalidate=="0000-00-00") $ptglvalidate="";
                    if ($ptglbooking=="0000-00-00") $ptglbooking="";
                    if ($ptglissued=="0000-00-00") $ptglissued="";
                    if ($ptglatasan4=="0000-00-00") $ptglatasan4="";
                    
                    if (!empty($ptglbooking)) $ptglbooking= date("d M Y", strtotime($ptglbooking));
                    if (!empty($ptglissued)) $ptglissued= date("d M Y", strtotime($ptglissued));

                    $pprint="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=printentrybrdcccabang&brid=$pbrid&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$pbrid</a>";
                    
                    $cekbox = "<input type=checkbox value='$pbrid' id='chkbox_br[$pbrid]' name='chkbox_br[]' class='chk_$pbrid' onclick=\"toggleCexBox(this)\">";
                    
                    if (strtoupper($cket)=="VALIDATE") {
                        if (empty($ptglbooking)) $cekbox="Blm Booking";
                        else {
                            
                            if (!empty($ptglvalidate)) {
                                $cekbox="<a href='#' class='btn btn-danger btn-xs' data-toggle='modal' "
                                    . "onClick=\"disp_confirm('removevalidate', '$pbrid')\"> "
                                    . "Remove</a>";
                            }
                            
                        }
                    }else{
                        if (empty($ptglissued)) $cekbox="Blm Issued";
                        if (empty($ptglatasan4)) $cekbox="Blm Disetujui";
                        if (empty($ptglvalidate)) $cekbox="Blm Validate";
                        if (empty($ptglbooking)) $cekbox="Blm Booking";
                    }
                    
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
                        $pdivisi=$row['divisi'];
                        $pnmkode=$row['nama_kode'];
                        $pnmcoa=$row['NAMA4'];
                        $pidbr=$row['brid'];

                        
                        $pjabat = $row["jabatanid"];
                        
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
                        
                        $txt_tglberangkat1= "<b>".date("d M Y", strtotime($ptgltransaski1))."</b>";
                        $txt_tglberangkat2= "<b>".date("d M Y", strtotime($ptgltransaski2))."</b>";
                        
                        $pilih_tgl="$txt_tglberangkat1";
                        if ($pnoidpilih=="03" OR $pnoidpilih=="04") {
                            $pilih_tgl="$txt_tglberangkat1 s/d.<p/>$txt_tglberangkat2";
                        }
                        
                        if ($pnoidpilih=="04") {
                            $ppilih_jam2="<p/>$pjamtransaski2";
                        }else{
                            $ppilih_jam2="<span hidden>$pjamtransaski2</span>";
                        }
                        
                        
                        $prp_pilih=number_format($prp_pilih,0,",",",");


                        $pcb_agency=$pnmagency;
                        if ($pstsbayar=="S") $pcb_agency="Byr. Sendiri";


                        if ($noteket=="REJECT") {
                            if (!empty($paktivitas)) $paktivitas=$paktivitas.", ".$palasan_batal;
                            else $paktivitas=$palasan_batal;
                        }


                        $pjumlah=number_format($pjumlah,0,",",",");
                        $ptgl= date("d/m/Y", strtotime($ptgl));
                        
                        $pjenistiket="";
                        if ($pjnspiltiket=="K") $pjenistiket="KAI";
                        elseif ($pjnspiltiket=="P") $pjenistiket="PESAWAT";

                        $puntukpilih="";
                        if ($pnoidpilih=="01") $puntukpilih="TIKET $pjenistiket PERGI";
                        elseif ($pnoidpilih=="02") $puntukpilih="TIKET $pjenistiket PULANG";
                        elseif ($pnoidpilih=="03") $puntukpilih="HOTEL";
                        elseif ($pnoidpilih=="04") $puntukpilih="SEWA KENDARAAN";

                        
                        $nnmbtnupload=" btn btn-danger btn-xs ";
                        
                        $nbtnlampiran="<button type='button' class='$nnmbtnupload' title='Lihat' data-toggle='modal' "
                                . " data-target='#myModal' "
                                . " onClick=\"getDataLampiran('$pbrid', '$pnoidpilih', '$puntukpilih')\">Lihat</button>";
                        if ($pstsimages!="Y") $nbtnlampiran="";

                        echo "<tr>";
                        echo "<td nowrap>$no_brurut</td>";
                        echo "<td nowrap>$cekbox</td>";
                        echo "<td nowrap>$pprint</td>";
                        echo "<td nowrap>$nbtnlampiran</td>";
                        echo "<td nowrap>$puntukpilih</td>";
                        echo "<td nowrap>$pilih_tgl</td>";
                        echo "<td nowrap>$pjamtransaski1 $ppilih_jam2</td>";
                        echo "<td nowrap align='right'>$prp_pilih</td>";
                        echo "<td nowrap>$pcb_agency</td>";
                        echo "<td nowrap>$ptglbooking</td>";
                        echo "<td nowrap>$ptglissued</td>";
                        echo "<td nowrap>$pnmkaryawan</td>";
                        echo "<td nowrap>$pnamacabang</td>";
                        echo "<td nowrap>$pnamadokter</td>";
                        echo "<td nowrap>$paktivitas</td>";
                        echo "<td nowrap>$pdivisi</td>";
                        echo "<td nowrap></td>";
                        echo "</tr>";

                        
                        $purutanid++;
                        $no_brurut="";
                        $pprint="";
                        $txt_jmled="";
                        $txt_tglissued="";
                        $piltglissued="";
                        $premoveissued="";
                        
                        $ptglbooking="";
                        $ptglissued="";
                        $cekbox="";
                        
                    }
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>
        
    </div>
    
    <?PHP
        if ($noteket=="APPROVE") {
            echo "<div class='col-sm-5'>";
            echo "<input type='button' class='btn btn-warning btn-sm' id='s-submit' value='Proses' onclick=\"disp_confirm('Proses...?', 'input')\">";
            echo "<input type='hidden' class='btn btn-danger btn-sm' id='s-submit' value='Reject' onclick=\"disp_confirm('Hapus / Reject...?', 'reject')\">";
            echo "</div>";
        }elseif ($noteket=="UNAPPROVE") {
            echo "<div class='col-sm-5'>";
            echo "<input type='button' class='btn btn-success btn-sm' id='s-submit' value='Un Proses' onclick=\"disp_confirm('Un Approve...?', 'unapprove')\">";
            echo "</div>";
        }elseif ($noteket=="VALIDATE") {
            echo "<div class='col-sm-5'>";
            echo "<input type='button' class='btn btn-dark btn-sm' id='s-submit' value='Validate Data' onclick=\"disp_confirm('Validate...?', 'validatedata')\">";
            echo "</div>";
        }
    ?>
            
</form>

<script>
    $(document).ready(function() {
        var dataTable = $('#datatableprosbrfin').DataTable( {
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
                { className: "text-right", "targets": [7] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,7,8,9,10,11,12,13,14,15,16] }//nowrap

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
    
    function disp_confirm(pText_,ket)  {
        
        var iidremove="";
        if (pText_=="removevalidate") {
            iidremove=ket;
            ket=pText_;
            
            pText_="Remove Validate...???";
        }
        
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
                document.getElementById("d-form2").action = "module/mod_fin_prosbrcab/aksi_prosbrcab.php?module="+module+"&act="+ket+"&idmenu="+idmenu+"&ukethapus="+iketalasan+"&id="+iidremove;
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
    #datatableprosbrfin th {
        font-size: 13px;
    }
    #datatableprosbrfin td { 
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