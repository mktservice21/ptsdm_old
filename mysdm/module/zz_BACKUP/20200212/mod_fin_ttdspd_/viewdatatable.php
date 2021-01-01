<?php
    session_start();
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    
    $n_filterkaryawan="";
    if ($pses_grpuser=="1" OR $pses_grpuser=="24" OR $pses_grpuser=="25") {
        if ($pses_grpuser=="25") {
            //$n_filterkaryawan=" AND divisi<>'OTC' AND CONCAT(kodeid,subkode) NOT IN ('225', '226', '227', '228', '229', '230', '231', '232', '233', '234', '235', '350') ";
            
            $n_filterkaryawan=" AND CONCAT(kodeid,subkode) NOT IN ('225', '226', '227', '228', '229', '230', '231', '232', '233', '234', '235', '350') "
                    . " AND (divisi<>'OTC' OR (divisi='OTC' AND CONCAT(kodeid, subkode) IN ('103', '221', '236')) ) ";
            
        }
    }else{
        if ($pses_divisi=="OTC") {
            $n_filterkaryawan=" AND divisi='OTC' ";
            if ($pses_grpuser=="23") $n_filterkaryawan=" AND divisi='OTC' AND karyawanid='$pses_idcard' ";
        }else{
            $n_filterkaryawan=" AND divisi<>'OTC' AND karyawanid='$pses_idcard' ";
        }
    }
    
    
    $cket = $_POST['eket'];
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $karyawan = $_POST['ukaryawan'];
    
    $_SESSION['FINSPDAPVKET'] = $cket;
    $_SESSION['FINSPDAPVTGL1'] = $mytgl1;
    $_SESSION['FINSPDAPVTGL2'] = $mytgl2;
    
    $tgl1= date("Y-m", strtotime($mytgl1));
    $tgl2= date("Y-m", strtotime($mytgl2));
    
    
    $sql = "SELECT idinput, DATE_FORMAT(tgl,'%M %Y') bulan, DATE_FORMAT(tgl,'%d/%m/%Y') as tgl, DATE_FORMAT(tglf,'%M %Y') as tglf,
        divisi, kodeid, nama, subkode, subnama, FORMAT(jumlah,0,'de_DE') as jumlah, 
        nomor, nodivisi, pilih, karyawanid, jenis_rpt, userproses, ifnull(tgl_proses,'0000-00-00') tgl_proses, ifnull(tgl_dir,'0000-00-00') tgl_dir
        , ifnull(tgl_dir2,'0000-00-00') tgl_dir2, ifnull(tgl_apv1,'0000-00-00') tgl_apv1, ifnull(tgl_apv2,'0000-00-00') tgl_apv2 ";
    $sql.=" FROM dbmaster.v_suratdana_br ";
    $sql.=" WHERE 1=1  ";
    $sql.=" AND ( (Date_format(tgl, '%Y-%m') between '$tgl1' and '$tgl2') OR (Date_format(tglspd, '%Y-%m') between '$tgl1' and '$tgl2') ) $n_filterkaryawan";
    
    if (strtoupper($cket)!= "REJECT") $sql.=" AND IFNULL(stsnonaktif,'') <> 'Y' ";
    
    //$sql .= " AND ifnull(tgl_proses,'')='' ";// sudah ada spd
    
    
    
    if (strtoupper($cket)=="APVDIRFIN") {
        $sql.=" AND IFNULL(tgl_apv2,'')<>'' AND karyawanid='$pses_idcard' ";//AND IFNULL(tgl_dir,'')='' 
    }else{
        
        if ($pses_divisi=="OTC") {
            //$sql.=" and IFNULL(pilih,'')='Y' ";
        }else{
            //$sql.=" and IFNULL(pilih,'')='Y' ";
        }
    
        if ($pses_grpuser=="3" OR $pses_grpuser=="23" OR $pses_grpuser=="28") {
            if (strtoupper($cket)=="APPROVE") {
                $sql.=" AND IFNULL(tgl_apv1,'')='' ";
            }elseif (strtoupper($cket)=="UNAPPROVE") {
                $sql.=" AND IFNULL(tgl_apv1,'')<>'' ";
            }elseif (strtoupper($cket)=="REJECT") {
                $sql.=" AND IFNULL(stsnonaktif,'') = 'Y' ";
            }elseif (strtoupper($cket)=="PENDING") {

            }
        }else{
            if (strtoupper($cket)=="APPROVE") {
                if ($pses_grpuser=="25") {//anne
                    $sql.=" AND ( IFNULL(tgl_apv1,'')<>'' OR (IFNULL(tgl_apv1,'')='' AND karyawanid='$pses_idcard') ) ";
                }elseif ($pses_grpuser=="26") {//saiful
                    //$sql.=" AND ( IFNULL(tgl_apv1,'')<>'' OR (IFNULL(tgl_apv1,'')='' AND karyawanid='$pses_idcard' AND CONCAT(kodeid, subkode) IN ('103', '221')) ) ";
                    $sql .=" AND ( (IFNULL(tgl_apv1,'')<>'' AND CONCAT(kodeid, subkode) NOT IN ('103', '221', '236')) OR "
                            . " (IFNULL(tgl_apv1,'')='' AND CONCAT(kodeid, subkode) IN ('103', '221', '236')) ) ";
                }else{
                }
                $sql.=" AND IFNULL(tgl_apv2,'')='' ";
            }elseif (strtoupper($cket)=="UNAPPROVE") {
                //$sql.=" AND IFNULL(tgl_apv2,'')<>'' ";
                if ($pses_grpuser=="26") {//saiful
                    $sql.=" AND ( (IFNULL(tgl_apv2,'')<>'' AND CONCAT(kodeid, subkode) NOT IN ('103', '221', '236')) OR "
                            . " (IFNULL(tgl_apv1,'')<>'' AND CONCAT(kodeid, subkode) IN ('103', '221', '236')) )";
                }else{
                    $sql.=" AND IFNULL(tgl_apv2,'')<>'' ";
                }
            }elseif (strtoupper($cket)=="REJECT") {
                $sql.=" AND IFNULL(stsnonaktif,'') = 'Y' ";
            }elseif (strtoupper($cket)=="PENDING") {

            }
        }
    }
    
    if (strtoupper($cket)== "SUDAHFIN") $sql .= " AND ifnull(tgl_proses,'')<>'' "; //sudah fin
    //echo $sql;
?>

<form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    
    <div class='x_content' style="margin-left:-20px; margin-right:-20px;">
        
            <div class="title_left">
                <h4 style="font-size : 12px;">
                    <?PHP
                        $noteket = strtoupper($cket);
                        $text="";
                        if ($noteket=="APPROVE") $text="Data Yang Belum DiApprove";
                        if ($noteket=="UNAPPROVE") $text="Data Yang Sudah DiApprove";
                        if ($noteket=="REJECT") $text="Data Yang DiReject";
                        if ($noteket=="PENDING") $text="Data Yang DiPending";
                        if ($noteket=="SEMUA") $text="Data Yang Belum dan Sudah Approve";
                        
                        echo "<b>$text"
                                . "<p/>&nbsp;*) <span style='color:red;'>klik no divisi/nobr untuk melihat detail pengajuan</span></b>";
                    ?>
                </h4>
            </div>
        <div class="clearfix"></div>
        
        <table id='dtablecadir' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='20px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='100px'>No Divisi/NOBR</th>
                    <th width='50px'>Jumlah</th>
                    <th width='30px'>Divisi</th>
                    <th width='50px'>Tgl Pengajuan</th>
                    <th width='50px'>Bulan</th>
                    <th width='30px'>Kode</th>
                    <th width='250px'>Sub</th>
                    <th width='30px'>Finance</th>
                    <th width='30px'>Checker</th>
                    <th width='30px'>Approved 1</th>
                    <th width='30px'>Approved 2</th>
                </tr>
            </thead>
            <tbody>
            <?PHP
                $sql.=" order by idinput DESC";
                //echo $sql;
                $no=1;
                $tampil = mysqli_query($cnmy, $sql);
                while ($row= mysqli_fetch_array($tampil)) {
                    $idno=$row['idinput'];
                    $tglbuat = $row["tgl"];
                    $pdivisi = $row["divisi"];
                    $pnodivisi = $row["nodivisi"];
                    $pkode = $row["kodeid"];
                    $psubkode = $row["subkode"];
                    $nama = $row["nama"];
                    $subnama = $row["subnama"];
                    $pkaryawanid=$row['karyawanid'];
                    $pjenisrpt=$row["jenis_rpt"];
                    
                    $pmystsyginput="";
                    if ($pkaryawanid=="0000000566") {
                        $pmystsyginput=1;
                    }elseif ($pkaryawanid=="0000001043") {
                        $pmystsyginput=2;
                    }else{
                        if ( ($pkode=="1" AND $psubkode=="01") OR ($pkode=="2" AND $psubkode=="20") ) {//anne
                            $pmystsyginput=5;
                        }else{
                            if ($pkode=="1" AND $psubkode=="03") {//ria
                                $pmystsyginput=3;
                            }elseif ($pkode=="2" AND $psubkode=="21") {//marsis
                                $pmystsyginput=4;
                            }elseif ( ($pkode=="2" AND $psubkode=="22") OR ($pkode=="2" AND $psubkode=="23") ) {//marsis
                                $pmystsyginput=6;
                            }
                        }
                    }
        
                    $periode = $row["bulan"];
                    if ($pkode=="1" AND $psubkode=="04") {
                        $periode = $row["tglf"];
                    }
                    
                    $pbulan = $row["tglf"];
                    $jumlah = $row["jumlah"];
                    $ptgldir = $row["tgl_dir"];
                    $ptgldir2 = $row["tgl_dir2"];
                    $ptglfin = $row["tgl_proses"];
                    $papv1 = $row["tgl_apv1"];
                    $papv2 = $row["tgl_apv2"];
                    
                    $cekbox = "<input type=checkbox value='$idno' name=chkbox_br[]>";
                    
                    $pmymodule="";
                    $print=$pnodivisi;
                    if ($pdivisi=="OTC") {
                        if ( ($pkode=="1" AND $psubkode=="03") ) {
                            $pmymodule="module=rekapbiayarutinotc&act=input&idmenu=171&ket=bukan&ispd=$idno";
                        }elseif ( ($pkode=="2" AND $psubkode=="21") ) {
                            $pmymodule="module=rekapbiayaluarotc&act=input&idmenu=245&ket=bukan&ispd=$idno";
                        }elseif ( ($pkode=="2" AND $psubkode=="36") ) {
                            $pmymodule="module=rekapbiayarutincaotc&act=input&idmenu=245&ket=bukan&ispd=$idno";
                        }else{
                            $pmymodule="module=lapbrotcpermo&act=input&idmenu=134&ket=bukan&ispd=$idno";
                        }
                    }else{
                        if ($pmystsyginput==1) {
                            $pmymodule="module=saldosuratdana&act=rekapbr&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==2) {
                            if ($pjenisrpt=="D") {
                                $pmymodule="module=saldosuratdana&act=viewbrklaim&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                            }else{
                                $pmymodule="module=saldosuratdana&act=viewbr&idmenu=192&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                            }
                        }elseif ($pmystsyginput==3) {
                            $pmymodule="module=rekapbiayarutin&act=input&idmenu=190&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==4) {
                            $pmymodule="module=rekapbiayaluar&act=input&idmenu=187&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==5) {
                            $pmymodule="module=saldosuratdana&act=rekapbr&idmenu=204&ket=bukan&ispd=$idno&iid=$pmystsyginput";
                        }elseif ($pmystsyginput==6) {
                            $pmymodule="module=spdkas&act=viewbrho&idmenu=205&ket=bukan&ispd=$idno&bln=$tglbuat";
                        }
                    }
                    
                    if (!empty($pmymodule)) {
                        
                        $print="<a style='font-size:11px;' title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?$pmymodule',"
                            . "'Ratting','width=800,height=500,left=400,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "$pnodivisi</a>";
                        
                    }
                    
                    $apvdir="";
                    $apvdir2="";
                    $apvfin="";
                    $napv1="";
                    $napv2="";
                    
                    if (!empty($ptgldir) AND $ptgldir <> "0000-00-00") $apvdir=date("d F Y, h:i:s", strtotime($ptgldir));
                    if (!empty($ptgldir2) AND $ptgldir2 <> "0000-00-00") $apvdir2=date("d F Y, h:i:s", strtotime($ptgldir2));
                    if (!empty($ptglfin) AND $ptglfin <> "0000-00-00") $apvfin=date("d F Y, h:i:s", strtotime($ptglfin));
                    if (!empty($papv1) AND $papv1 <> "0000-00-00") $napv1=date("d F Y, h:i:s", strtotime($papv1));
                    if (!empty($papv2) AND $papv2 <> "0000-00-00") $napv2=date("d F Y, h:i:s", strtotime($papv2));
                    
                    
                    if (strtoupper($cket)=="APVDIRFIN") {
                    }else{
                        
                        if ($pses_grpuser=="3" OR $pses_grpuser=="23" OR $pses_grpuser=="28") {
                            if (!empty($napv2)) $cekbox="";
                        }elseif ($pses_grpuser=="25" OR $pses_grpuser=="26") {
                            if ($pkaryawanid!=$pses_idcard){
                                if (empty($napv1)) $cekbox="";
                            }
                            if (!empty($apvdir)) $cekbox="";
                            
                            if ($pses_grpuser=="26" AND ( ($pkode=="1" AND $psubkode=="03") OR ($pkode=="2" AND $psubkode=="21") OR ($pkode=="2" AND $psubkode=="36") ) ) {//saiful untuk rutin dan lk ceker anne
                                if (!empty($napv2)) $cekbox="";
                            }
                            
                        }else{
                            //if (empty($napv1)) $cekbox="";
                        }
                        
                    }
                    
                    if ($noteket=="REJECT") {
                        $cekbox="";
                    }
                    
                    if (strtoupper($cket)=="APVDIRFIN") {
                        if (!empty($apvdir) AND empty($apvdir2)) {
                        $cekbox="<a href='#' class='btn btn-danger btn-xs' data-toggle='modal' "
                            . "onClick=\"ProsesDataUnApproveFIN('unapprove', '$idno')\"> "
                            . "unapprove</a>";
                        }
                    }
                    
                    if (strtoupper($cket)=="APVDIRFIN") {
                        if (!empty($apvdir2)) $cekbox="";
                    }
                    
                    echo "<tr>";
                    echo "<td>$no</td>";
                    echo "<td>$cekbox</td>";
                    echo "<td>$print</td>";
                    echo "<td>$jumlah</td>";
                    echo "<td>$pdivisi</td>";
                    echo "<td>$tglbuat</td>";
                    echo "<td>$periode</td>";
                    echo "<td>$nama</td>";
                    echo "<td>$subnama</td>";
                    echo "<td>$napv1</td>";
                    echo "<td>$napv2</td>";
                    echo "<td>$apvdir</td>";
                    echo "<td>$apvdir2</td>";
                    
                    echo "</tr>";
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
        <div class="well" style="margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;"><!--overflow: auto; -->
            <input class='btn btn-success' type='button' name='buttonapv' value='UnApprove' 
               onClick="ProsesData('unapprove', 'chkbox_br[]')">
        </div>
    <?PHP
    }
    ?>
    <div class='clearfix'></div>
    <!-- tanda tangan -->
    <?PHP
        if (strtoupper($cket)=="APPROVE") {
            echo "<div class='col-sm-5'>";
                include "ttd_ttdspd.php";
            echo "</div>";
        }elseif (strtoupper($cket)=="APVDIRFIN") {
            echo "<div class='col-sm-5'>";
                echo "<span style='color:red;'><b><u>Khusus Tanda Tangan DIREKTUR</u></b><br>&nbsp;</span>";
                include "ttd_appvspddir_fin.php";
            echo "</div>";
        }
    ?>
</form>


<script>
    $(document).ready(function() {
        //alert(etgl1);
        var dataTable = $('#dtablecadir').DataTable( {
            //"stateSave": true,
            //"order": [[ 7, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { className: "text-right", "targets": [3] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            },
            rowReorder: {
                selector: 'td:nth-child(3)'
            },
            responsive: true
        } );
        //$('div.dataTables_filter input', dataTable.table().container()).focus();
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
    
    function ProsesDataUnApproveFIN(ket, cekbr){
        //alert(ket+", "+cekbr);
        var cmt = confirm('Apakah akan melakukan proses unapprove ...?');
        if (cmt == false) {
            return false;
        }
        var allnobr = "";
        
        if (cekbr=="") {
            alert("Tidak ada data yang diproses...!!!");
            return false;
        }
        
        allnobr="('"+cekbr+"')";
        
        var txt;
        var ekaryawan=document.getElementById('e_idkaryawan').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_fin_ttdspd/aksi_apvspddir_fin.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=unapprove"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                pilihData('apvdirfin');
                alert(data);
            }
        });
        
        
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
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_fin_ttdspd/aksi_ttdspd.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=approve"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                if (ket=="reject") ket="approve";
                pilihData(ket);
                alert(data);
            }
        });
        
    }
    
    function BuatLinkApprove(ket, cekbr) {
        var cmt = confirm('Apakah akan membuat link untuk approve direktur...?');
        if (cmt == false) {
            $('#myModal').modal('hide');
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
            $('#myModal').modal('hide');
            //alert("Tidak ada data yang dipilih...!!!");
            //return false;
        }
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_fin_ttdspd/buatlinkspdapprovedir.php?module=buatlinkappdir"+"&idmenu="+idmenu+"&act="+ket,
            data:"unobr="+allnobr,
            success:function(data){
                $("#myModal").html(data);
                //alert(data);
            }
        });
        
    }
</script>



<style>
    .divnone {
        display: none;
    }
    #dtablecadir th {
        font-size: 13px;
    }
    #dtablecadir td { 
        font-size: 11px;
    }
    .imgzoom:hover {
        -ms-transform: scale(3.5); /* IE 9 */
        -webkit-transform: scale(3.5); /* Safari 3-8 */
        transform: scale(3.5);
        
    }
</style>

