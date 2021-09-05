
<?php
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    include "../../config/fungsi_ubahget_id.php";
    
    $kodeinput = " AND br.kode=1 "; //membedakan biaya luar kota dan rutin
    
    $isitipe = $_POST['ucbtipeisi'];
    $cket = $_POST['eket'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $karyawan = $_POST['ukaryawan'];
    $lvlposisi = $_POST['ulevel'];
    $divisi = $_POST['udiv'];
    $stsapv = $_POST['uketapv'];
    
    
    
    $_SESSION['PROSRUT_TIPE'] = $isitipe;
    $_SESSION['PROSRUT_KET'] = $cket;
    $_SESSION['PROSRUT_TGL1'] = $mytgl1;
    $_SESSION['PROSRUT_TGL2'] = $mytgl2;
    $_SESSION['PROSRUT_KRY'] = $karyawan;
    $_SESSION['PROSRUT_LVL'] = $lvlposisi;
    $_SESSION['PROSRUT_DIV'] = $divisi;
    $_SESSION['PROSRUT_STSAPV'] = $stsapv;
    
    $tgl1= date("Y-m", strtotime($mytgl1));
    $tgl2= date("Y-m", strtotime($mytgl2));
    
?>

<div class='modal fade' id='myModal' role='dialog'></div>
<form method='POST' action='' id='d-form2' name='d-form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    
    
    
    
    
    <div class='x_content'>
        <?PHP if (!empty($isitipe)) { ?>
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                <?PHP 
                if ($isitipe=="A") { 
                    echo "<div class='col-sm-3'>";
                        echo "<b>PPN %</b>";
                        echo "<div class='form-group'>";
                            echo "<div class='input-group date' id=''>";
                            echo "<input type='text' class='form-control inputmaskrp2' id='e_ppn' name='e_ppn' required='required' placeholder='ppn' value=''>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                }elseif ($isitipe=="B") {
                    $hari_ini = date("Y-m-d");
                    $tglberlku = date('d F Y', strtotime($hari_ini));
                    echo "<div class='col-sm-3'>";
                        echo "<b>Tanggal Transfer</b>";
                        echo "<div class='form-group'>";
                            echo "<div class='input-group date' id=''>";
                                echo "<input type='text' class='form-control' id='e_tgltrans' name='e_tgltrans' autocomplete='off' value='$tglberlku' />";
                                echo "<span class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span></span>";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                }
                ?>
                
                <div class='col-sm-3'>
                    <small>&nbsp;</small>
                   <div class="form-group">
                       <input style="font-weight: bold; border:1px solid #000; color:#000;" type='button' class='btn btn-default btn-sm' id="s-submit" value="&nbsp;Save&nbsp;" onclick='disp_confirm("Simpan...?", "<?PHP echo "$isitipe"; ?>")'>
                   </div>
               </div>
                
            </div>
        </div>
        <?PHP } ?>
    </div>
    
    
    
    
    
    
    <div class='x_content'>
        
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
                        if ($noteket=="APPROVE") $text="Data Yang Belum DiProses";
                        if ($noteket=="UNAPPROVE") $text="Data Yang Sudah DiProses";
                        if ($noteket=="REJECT") $text="Data Yang DiReject";
                        if ($noteket=="PENDING") $text="Data Yang DiPending";
                        if ($noteket=="SEMUA") $text="Data Yang Belum dan Sudah Proses";
                        if ($noteket=="BELUMAPVSM") $text="Data Yang Belum Approve SM";
                        
                        echo "<b>$text $apvby</b>";
                    ?>
                </h4>
            </div><div class="clearfix">
        </div>
        <table id='datatableprosrutin' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='20px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='40px'>ID</th>
                    <th width='100px'>Yg Membuat</th>
                    <th width='60px'>Jumlah</th>
                    <th width='40px'>PPN</th>
                    <th width='130px'>Periode</th>
                    <th width='30px'>Bukti</th>
                    <th width='100px'>Keterangan</th>
                    <th width='30px'>Divisi</th>
                    <th width='30px'>Approve SPV/AM</th>
                    <th width='30px'>Approve DM</th>
                    <th width='30px'>Approve SM</th>
                    <th width='30px'>Approve GSM</th>
                    <th width='30px'>Tgl. Transfer</th>
                    <th width='30px'>Tgl. Input</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $sql_x = "SELECT idrutin, DATE_FORMAT(tgl,'%d %M %Y') as tgl, DATE_FORMAT(bulan,'%M %Y') as bulan, DATE_FORMAT(periode1,'%d/%m/%Y') as periode1, "
                        . " DATE_FORMAT(periode2,'%d/%m/%Y') as periode2, "
                        . " divisi, karyawanid, nama, nama_karyawan, areaid, nama_area, FORMAT(jumlah,0,'de_DE') as jumlah, keterangan, "
                        . " COA4, NAMA4, ppn ";
                $sql_x.=" FROM dbmaster.v_brrutin0 ";
                
				
				
				//jika ada masalah di gambar (ttd) aktifkan $sXXql
                $sql = "SELECT
                    br.divisi,
                    br.idrutin,
                    br.karyawanid,
                    k.nama, nama_karyawan,
                    DATE_FORMAT(br.tgltrans,'%d/%m/%Y') as tgltrans,
                    DATE_FORMAT(br.tgl,'%d %M %Y') as tgl,
                    br.bulan,
                    br.kodeperiode,
                    DATE_FORMAT(br.periode1,'%d/%m/%Y') as periode1,
                    DATE_FORMAT(br.periode2,'%d/%m/%Y') as periode2,
                    FORMAT(br.jumlah,0,'de_DE') as jumlah,
                    br.ppn,
                    br.keterangan,
                    i.idrutin bukti,
                    ifnull(br.tgl_atasan1,'0000-00-00') tgl_atasan1,
                    br.gbr_atasan1,
                    ifnull(br.tgl_atasan2,'0000-00-00') tgl_atasan2,
                    br.gbr_atasan2,
                    ifnull(br.tgl_atasan3,'0000-00-00') tgl_atasan3,
                    br.gbr_atasan3,
                    ifnull(tgl_atasan4,'0000-00-00') tgl_atasan4,
                    br.gbr_atasan4,
                    ifnull(br.tgl_fin,'0000-00-00') tgl_fin,
                    br.jabatanid, br.atasan4 
                    FROM dbmaster.t_brrutin0 AS br
                    LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
                    LEFT JOIN (SELECT distinct DISTINCT idrutin from dbimages.img_brrutin1) as i on i.idrutin=br.idrutin 
                    ";
                
					
					//jika ada masalah di gambar (ttd) aktifkan $sXXql
					$sXXql = "SELECT
						br.divisi,
						br.idrutin,
						br.karyawanid,
						k.nama, nama_karyawan,
						DATE_FORMAT(br.tgl,'%d %M %Y') as tgl,
						br.bulan,
						br.kodeperiode,
						DATE_FORMAT(br.periode1,'%d/%m/%Y') as periode1,
						DATE_FORMAT(br.periode2,'%d/%m/%Y') as periode2,
						FORMAT(br.jumlah,0,'de_DE') as jumlah,
						br.keterangan,
						'' as bukti,
						ifnull(br.tgl_atasan1,'0000-00-00') tgl_atasan1,
						'' as gbr_atasan1,
						ifnull(br.tgl_atasan2,'0000-00-00') tgl_atasan2,
						'' as gbr_atasan2,
						ifnull(br.tgl_atasan3,'0000-00-00') tgl_atasan3,
						'' as gbr_atasan3,
						ifnull(tgl_atasan4,'0000-00-00') tgl_atasan4,
						'' as gbr_atasan4,
						ifnull(br.tgl_fin,'0000-00-00') tgl_fin,
						br.jabatanid, br.atasan4 
						FROM dbmaster.v_brrutin0_limit AS br
						LEFT JOIN hrd.karyawan AS k ON br.karyawanid = k.karyawanId
						";
				
				
                $sql.=" WHERE 1=1 $kodeinput ";
                $sql.=" AND Date_format(br.bulan, '%Y-%m') between '$tgl1' and '$tgl2' ";
                
                if (!empty($divisi)) $sql.=" and br.divisi<>'OTC' ";//in $divisi 
                
                if (strtoupper($cket)!= "REJECT") $sql.=" AND br.stsnonaktif <> 'Y' ";
                
                if ( (strtoupper($cket)!="SEMUA") ) {
                    if (strtoupper($cket)=="REJECT") {
                        $sql.=" AND br.stsnonaktif = 'Y' ";
                    }elseif (strtoupper($cket)=="BELUMAPVSM") {
                        $sql.=" AND ifnull(br.tgl_atasan3,'') = '' and ifnull(br.fin,'') = '' ";
                    }else{
                        $sql.=" AND ifnull(br.tgl_atasan3,'') <> '' ";//AND (br.jabatanid <> '20' OR (br.jabatanid = '20' AND ifnull(br.tgl_atasan4,'') <> ''))
                        if (strtoupper($cket)=="APPROVE") {
                            $sql.=" AND ifnull(br.tgl_fin,'') = '' ";
                        }elseif (strtoupper($cket)=="UNAPPROVE") {
                            $sql.=" AND ifnull(br.tgl_fin,'') <> '' ";
                        }elseif (strtoupper($cket)=="PENDING") {

                        }
                    }
                }
                
                
                $sql.=" order by br.kodeperiode, br.idrutin";
                //echo $sql;
                $no=1;
                $tampil = mysqli_query($cnmy, $sql);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idno=$row['idrutin'];
                    $tglbuat = $row["tgl"];
					
                    $pkaryawan=$row['karyawanid'];
                    $nama = $row["nama"];
                    if ($_SESSION['KRYNONE']==$pkaryawan) $nama=$row["nama_karyawan"];
					
                    $pbukti = $row["bukti"];
                    //$nmarea = $row["nama_area"];
                    $bulan = date("F Y", strtotime($row["bulan"]));
                    $periode = $row["periode1"]." - ".$row["periode2"];
                    $tgltransfer="";
                    if (!empty($row["tgltrans"]))
                        $tgltransfer = $row["tgltrans"];
                    $jumlah = $row["jumlah"];
                    $ppn = $row["ppn"];
                    $keterangan = $row["keterangan"];
                    $pdivisi = $row["divisi"];
                    if ($pdivisi=="CAN") $pdivisi = "CANARY";
                    $cekbox = "<input type=checkbox value='$idno' name=chkbox_br[]>";
					
                    $paatasan4 = $row["atasan4"];
                    
                    $pidnoget=encodeString($idno);

                    $print="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=entrybrrutin&brid=$idno&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$idno</a>";
                    
                    $bukti="";
                    if (!empty($pbukti)) {
                        $bukti="<a title='lihat bukti' href='#' class='btn btn-danger btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=entrybrrutin&brid=$idno&iprint=bukti',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Lihat</a>";
                    }
                    
                    $apv1="";
                    $apv2="";
                    $apv3="";
                    $apv4="";
                    $ptglfinapv="";
                    if (!empty($row["gbr_atasan1"]) AND $row["tgl_atasan1"] <> "0000-00-00") $apv1=date("d F Y, h:i:s", strtotime($row["tgl_atasan1"]));
                    if (!empty($row["gbr_atasan2"]) AND $row["tgl_atasan2"] <> "0000-00-00") $apv2=date("d F Y, h:i:s", strtotime($row["tgl_atasan2"]));
                    if (!empty($row["gbr_atasan3"]) AND $row["tgl_atasan3"] <> "0000-00-00") $apv3=date("d F Y, h:i:s", strtotime($row["tgl_atasan3"]));
                    if (!empty($row["gbr_atasan4"]) AND $row["tgl_atasan4"] <> "0000-00-00") $apv4=date("d F Y, h:i:s", strtotime($row["tgl_atasan4"]));
					
                    if (!empty($row["tgl_fin"]) AND $row["tgl_fin"] <> "0000-00-00") $ptglfinapv=date("d F Y, h:i:s", strtotime($row["tgl_fin"]));
                    
                    $edit="";
                    if (strtoupper($cket)=="APPROVE") {
                        $edit="<a title='lihat bukti' href='#' class='btn btn-success btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=$_GET[module]&idmenu=$_GET[idmenu]&brid=$idno&iprint=editrutin',"
                            . "'Ratting','width=1000,height=600,left=200,top=50,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Edit</a>";
                    }
                    
                    $pjabat = $row["jabatanid"];
                    if ((int)$pjabat==20 OR (int)$pjabat==5) {
                        if (empty($row["gbr_atasan4"]) AND $row["tgl_atasan4"] == "0000-00-00") {
                            $cekbox="";
                        }
                    }
                    
                    
                    $cariapvgsm="";
                    if ((int)$pjabat==38) {
                        $cariapvgsm= getfieldcnmy("select karyawanid as lcfields from dbmaster.t_karyawan_app_gsm WHERE karyawanid='$pkaryawan'");
                        if (!empty($cariapvgsm) AND empty($row["gbr_atasan4"]) AND $row["tgl_atasan4"] == "0000-00-00" ){
                            $cekbox="";
                        }
                    }
					
                    $lihatkunjungan="<a title='lihat bukti' href='#' class='btn btn-default btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=entrybrrutin&brid=$idno&iprint=lihatkunjungan',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$nama</a>";
                        
                    
                    $pisipajak="<button type='button' class='btn btn-primary btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataInputPajak('$idno')\">Pajak</button>";
                        
                    $peditketeaja="";
                    if (empty($apvfin)) {
                        $peditketeaja="<a title='lihat bukti' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=editdataketerutincalk&brid=$idno&iprint=nrutin',"
                            . "'Ratting','width=600,height=350,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Edit Ket.</a>";
                    }
					
                    if ($pdivisi=="HO" AND (int)$pjabat<>38) {
                        if (empty($apv4) AND empty($ptglfinapv)) {
                            //$cekbox="";
                        }
                        
                        $print="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=entrybrrutinho&brid=$pidnoget&iprint=print',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "$idno</a>";
                    }
					
                    if (strtoupper($cket)=="APPROVE") {
                        if ($paatasan4=="0000002403") {
                            //if (empty($apv4)) $cekbox="";
                        }
                    }
                    
                    $pviewdataabsen = "";
                    if ($pdivisi=="HO") {
                        $pkaryidcode=encodeString($pkaryawan);
                        $bulan_pilih = date("Ym", strtotime($row["bulan"]));
                        $bulan_pilih=encodeString($bulan_pilih);
                        $pviewdataabsen = "<a class='btn btn-warning btn-xs' href='eksekusi3.php?module=showdataabsensi&i=$pkaryidcode&b=$bulan_pilih' target='_blank'>Absensi</a>";
                    }
					
                    echo "<tr>";
                    echo "<td>$cekbox</td>";
                    echo "<td nowrap>$print $edit $pisipajak $peditketeaja $pviewdataabsen</td>";
                    echo "<td>$lihatkunjungan</td>";
                    echo "<td>$jumlah</td>";
                    echo "<td>$ppn</td>";
                    echo "<td>$periode</td>";
                    echo "<td>$bukti</td>";
                    echo "<td>$keterangan</td>";
                    echo "<td>$pdivisi</td>";
                    echo "<td>$apv1</td>";
                    echo "<td>$apv2</td>";
                    echo "<td>$apv3</td>";
                    echo "<td>$apv4</td>";
                    echo "<td>$tgltransfer</td>";
                    echo "<td>$tglbuat</td>";
                    echo "</tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <div class='clearfix'></div>
    <div class="well" style="overflow: auto; margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">  
<?PHP
if (strtoupper($cket)=="UNAPPROVE") {
?>
        <?PHP
        if (strtoupper($cket)=="APPROVE") {
            ?>
            <!--<input class='btn btn-default' type='Submit' name='buttonapv' value='Approve'>-->
            <!--<input class='btn btn-danger' type='button' name='buttonapv' value='Reject' 
                   onClick="ProsesData('reject', 'chkbox_br[]')"> dipindah ke ttd-->
            <input class='btn btn-default' type='hidden' name='buttonapv' value='Pending' 
                   onClick="ProsesData('pending', 'chkbox_br[]')">
            <?PHP
        }elseif (strtoupper($cket)=="UNAPPROVE") {
            ?>
            <input class='btn btn-success' type='button' name='buttonapv' value='UnApprove' 
                   onClick="ProsesData('unapprove', 'chkbox_br[]')">
            <input class='btn btn-default' type='button' name='buttonapv' value='Rekap Data' onClick="RekapData('rekapdata', 'chkbox_br[]')">
            <input class='btn btn-info' type='button' name='buttonapv' value='Rekap Data Excel' onClick="RekapData('excel', 'chkbox_br[]')">
            <?PHP
        }elseif (strtoupper($cket)=="REJECT") {
        }elseif (strtoupper($cket)=="PENDING") {
        }elseif (strtoupper($cket)=="SEMUA") {
        }
        ?>

<?PHP
}elseif (strtoupper($cket)=="SEMUA") {
    ?><input class='btn btn-success' type='button' name='buttonapv' value='Rekap Data' onClick="RekapData('rekapdata', 'chkbox_br[]')"><?PHP
    ?><input class='btn btn-info' type='button' name='buttonapv' value='Rekap Data Excel' onClick="RekapData('excel', 'chkbox_br[]')"><?PHP
}
?>
    </div>
    
    <div class='clearfix'></div>


    <!-- tanda tangan -->
    <?PHP
        if (strtoupper($cket)=="APPROVE") {
            echo "<div class='col-sm-5'>";
            include "ttd_prosbiayarutin.php";
            echo "</div>";
        }
    ?>
</form>

<script>
    $(document).ready(function() {
        //alert(etgl1);
        var dataTable = $('#datatableprosrutin').DataTable( {
            "stateSave": true,
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [3, 4] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5] }//nowrap

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
        $(".inputmaskrp2").inputmask({ 'alias' : 'decimal', rightAlign: false, 'groupSeparator': '.','autoGroup': true });
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
        
        var ekaryawan=document.getElementById('e_idkaryawan').value;
        var elevel=document.getElementById('e_lvlposisi').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_fin_prosbiayarutin/aksi_prosbiayarutin.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=approve"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ulevel="+elevel+"&ketrejpen="+txt,
            success:function(data){
                pilihData(ket);
                alert(data);
            }
        });
        
    }
    
    function disp_confirm(pText_, act)  {
        var chk_arr =  document.getElementsByName("chkbox_br[]");
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
            
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/mod_fin_prosbiayarutin/aksi_prosbiayarutin.php?module="+module+"&idmenu="+idmenu+"&act="+act;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
    
    function RekapData(ket, cekbr){
        var chk_arr =  document.getElementsByName("chkbox_br[]");
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
        
        ok_ = 1;
        if (ok_) {
            var r=confirm("apaka akan rekap data...?")
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                var act = urlku.searchParams.get("act");
                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "eksekusi3.php?module="+module+"&idmenu="+idmenu+"&act="+ket;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    }
</script>

<script>
                                    
    $(document).ready(function() {

        $('#e_tgltrans').datepicker({
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd MM yy',
            onSelect: function(dateStr) {

            }

        });
    });


    function TambahDataInputPajak(eidbr){
        $.ajax({
            type:"post",
            url:"module/mod_fin_prosbiayarutin/pajakdatarutin.php?module=viewisipajak",
            data:"uidbr="+eidbr,
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
    #datatableprosrutin th {
        font-size: 13px;
    }
    #datatableprosrutin td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>