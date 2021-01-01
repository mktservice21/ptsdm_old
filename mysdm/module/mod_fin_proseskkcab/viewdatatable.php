<?php
session_start();


    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    include "../../config/koneksimysqli.php";
    
    //ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    
    $ppilihsts = strtoupper($_POST['eket']);
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $pkaryawanid = $_POST['ukaryawan'];
    $pstsapv = $_POST['uketapv'];
    
    
    $_SESSION['FPROSKKCSTS']=$ppilihsts;
    $_SESSION['FPROSKKCBLN1']=$mytgl1;
    $_SESSION['FPROSKKCBLN2']=$mytgl2;
    $_SESSION['FPROSKKCAPVBY']=$pkaryawanid;
    
    $pbulan1= date("Y-m-01", strtotime($mytgl1));
    $pbulan2= date("Y-m-t", strtotime($mytgl2));
    
    
    if (empty($pkaryawanid)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    

    
    //echo "$pkaryawanid : $pjabatanid, $plvlposisi $pbulan1 - $pbulan2"; exit;
    
    $pidgroup=$_SESSION['GROUP'];
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPKKCPRSF01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPKKCPRSF02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPKKCPRSF03_".$userid."_$now ";
    
    
    
    $query = "SELECT idkascab, tanggal, bulan, pengajuan, karyawanid, jabatanid, "
            . " icabangid, icabangid_o, divisi, jumlah, keterangan,"
            . " atasan1, atasan2, atasan3, atasan4,"
            . " tgl_atasan1, tgl_atasan2, tgl_atasan3, tgl_atasan4, tgl_fin FROM dbmaster.t_kaskecilcabang "
            . " WHERE ( (bulan BETWEEN '$pbulan1' AND '$pbulan2') OR (tglinput BETWEEN '$pbulan1' AND '$pbulan2') ) ";
    if ($pidgroup=="23" OR $pidgroup=="26") {
        $query .=" AND IFNULL(pengajuan,'') IN ('OTC', 'CHC', 'OT') ";
    }elseif ($pidgroup=="40") {
        $query .=" AND IFNULL(pengajuan,'') NOT IN ('OTC', 'CHC', 'OT') ";
    }
    if ($ppilihsts=="REJECT") {
        $query .=" AND IFNULL(stsnonaktif,'')='Y' ";
    }else{
        
        $query .=" AND IFNULL(stsnonaktif,'')<>'Y' ";
        if ($ppilihsts=="ALLDATA") {

        }else{
            if ($ppilihsts=="APPROVE") {
                $query .= " AND (IFNULL(tgl_fin,'')='' OR IFNULL(tgl_fin,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
            }elseif ($ppilihsts=="UNAPPROVE") {
                $query .= " AND (IFNULL(tgl_fin,'')<>'' AND IFNULL(tgl_fin,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }
        }
        
    }
    
    

    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "SELECT a.*, b.nama as nama_karyawan, c.nama as nama_cabang, d.nama as nama_cabangotc, "
            . " e.saldoawal, e.pcm, e.jmltambahan, e.jumlah as jmlttl, e.oustanding as otsrp FROM $tmp01 a LEFT JOIN "
            . " hrd.karyawan b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang c on a.icabangid=c.icabangid "
            . " LEFT JOIN MKT. icabang_o d on a.icabangid_o=d.icabangid_o "
            . " LEFT JOIN dbmaster.t_kaskecilcabang_rpdetail e on a.idkascab=e.idkascab";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "UPDATE $tmp02 SET icabangid=icabangid_o, nama_cabang=nama_cabangotc WHERE IFNULL(pengajuan,'') IN ('OTC', 'OT', 'CHC')"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.bridinput, b.nodivisi from dbmaster.t_suratdana_br1 a "
            . " JOIN dbmaster.t_suratdana_br b on a.idinput=b.idinput "
            . " JOIN $tmp02 c on a.bridinput=c.idkascab where "
            . " a.kodeinput='X' and IFNULL(stsnonaktif,'')<>'Y'";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp02 ADD COLUMN nodivisi VARCHAR(50)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp02 a JOIN $tmp03 b on b.bridinput=a.idkascab SET a.nodivisi=b.nodivisi";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>


<script>
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
    
    function ProsesDataApprove(ket, cekbr){
        //alert(ket+", "+cekbr);
        var cmt = confirm('Apakah akan melakukan proses data ...?');
        if (cmt == false) {
            return false;
        }
        var allnobr = "";
        
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
        var ekaryawan=document.getElementById('cb_karyawan').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_fin_proseskkcab/aksi_finproseskkcab.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=unapprove"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                pilihData('approve');
                alert(data);
            }
        });
        
        
    }
    
    function ProsesDataUnApprove(ket, cekbr){
        //alert(ket+", "+cekbr);
        var cmt = confirm('Apakah akan melakukan batal proses ...?');
        if (cmt == false) {
            return false;
        }
        var allnobr = "";
        
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
        var ekaryawan=document.getElementById('cb_karyawan').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_fin_proseskkcab/aksi_finproseskkcab.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=unapprove"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                pilihData('unapprove');
                alert(data);
            }
        });
        
        
    }
    
    function ProsesDataReject(ket, cekbr){
        //alert(ket+", "+cekbr);
        var cmt = confirm('Apakah akan melakukan proses reject data ...?');
        if (cmt == false) {
            return false;
        }
        var allnobr = "";
        
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
        if (ket=="reject" || ket=="hapus" || ket=="pending") {
            var textket = prompt("Masukan alasan "+ket+" : ", "");
            if (textket == null || textket == "") {
                txt = textket;
            } else {
                txt = textket;
            }
        }
        
        var ekaryawan=document.getElementById('cb_karyawan').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_fin_proseskkcab/aksi_finproseskkcab.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=unapprove"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                pilihData('approve');
                alert(data);
            }
        });
        
        
    }
    
    
</script>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        
        <?PHP
        $pchkall = "<input type='checkbox' id='chkbtnbr' name='chkbtnbr' value='deselect' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" checked/>";
        ?>
        <table id='dttblproskkcbfin' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='10px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='40px'>ID</th>
                    <th width='50px'>Bulan (Periode PC / Kas Kecil)</th>
                    <th width='50px'>Pengajuan</th>
                    <th width='200px'>Yg. Mengajukan</th>
                    <th width='50px'>Cabang</th>
                    <th align="center" nowrap>Saldo Awal</th>
                    <th align="center" nowrap>Jml. Biaya</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Keterangan</th>
                    <th width='50px'>Satus Approve</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp02 order by IFNULL(nodivisi,''), idkascab";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidkkcab=$row1['idkascab'];
                    $ptgl=$row1['tanggal'];
					$pbln=$row1['bulan'];
                    $pnmkaryawan=$row1['nama_karyawan'];
                    $pidpengajuan=$row1['pengajuan'];
                    $pnmcabang=$row1['nama_cabang'];
                    $pketerangan=$row1['keterangan'];
                    $pnodivisi=$row1['nodivisi'];
                    $prpjumlah=$row1['jumlah'];
                    $psldawal = $row1['saldoawal'];
                    
					$pbln = date("F Y", strtotime($pbln));
					
                    $pnmodulp="bgtkaskecilcabang";
                    $nama_pengajuan="ETHICAL";
                    if ($pidpengajuan=="OTC" OR $pidpengajuan=="OT" OR$pidpengajuan=="CHC") {
                        $nama_pengajuan="CHC";
                        $pnmodulp="bgtkaskecilcabangotc";
                    }
                    
                    $ptglfinance=$row1['tgl_fin'];
                    $ptglatasan1=$row1['tgl_atasan1'];
                    $ptglatasan2=$row1['tgl_atasan2'];
                    $ptglatasan3=$row1['tgl_atasan3'];
                    $ptglatasan4=$row1['tgl_atasan4'];
                    
                    $pidatasan1=$row1['atasan1'];
                    $pidatasan2=$row1['atasan2'];
                    $pidatasan3=$row1['atasan3'];
                    $pidatasan4=$row1['atasan4'];
                    
                    $pjmlmintarp=(DOUBLE)$prpjumlah-(DOUBLE)$psldawal;
                    
                    $psldawal=number_format($psldawal,0,",",",");
                    $prpjumlah=number_format($prpjumlah,0,",",",");
                    $pjmlmintarp=number_format($pjmlmintarp,0,",",",");
                    
                    
                    $ptgl= date("d/m/Y", strtotime($ptgl));
                    
                    if ($ptglfinance=="0000-00-00" OR $ptglfinance=="0000-00-00 00:00:00") $ptglfinance="";
                    if ($ptglatasan1=="0000-00-00" OR $ptglatasan1=="0000-00-00 00:00:00") $ptglatasan1="";
                    if ($ptglatasan2=="0000-00-00" OR $ptglatasan2=="0000-00-00 00:00:00") $ptglatasan2="";
                    if ($ptglatasan3=="0000-00-00" OR $ptglatasan3=="0000-00-00 00:00:00") $ptglatasan3="";
                    if ($ptglatasan4=="0000-00-00" OR $ptglatasan4=="0000-00-00 00:00:00") $ptglatasan4="";
                    
                    $print="<a title='Detail / Print' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=$pnmodulp&brid=$pidkkcab&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$pidkkcab</a>";
                    
                    $pedit="<a title='Edit data' href='eksekusi3.php?module=bgtkaskecilcabang&brid=$pidkkcab&iprint=editdatafin' "
                            . " class='btn btn-success btn-xs' data-toggle='modal' target='_blank'> "
                        . "Edit</a>";
                    
                    $ceklisnya = "<input type='checkbox' value='$pidkkcab' name='chkbox_br[]' id='chkbox_br[$pidkkcab]' class='cekbr'>";
                    
                    
                    $pstsapvoleh="";
                    if ($ppilihsts=="APPROVE") {
                        if (empty($ptglatasan4) AND !empty($pidatasan4)) { $ceklisnya=""; $pstsapvoleh="Belum Approve GSM"; }
                        if (empty($ptglatasan3) AND !empty($pidatasan3)) { $ceklisnya=""; $pstsapvoleh="Belum Approve SM"; }
                        if (empty($ptglatasan2) AND !empty($pidatasan2)) { $ceklisnya=""; $pstsapvoleh="Belum Approve DM"; }
                        if (empty($ptglatasan1) AND !empty($pidatasan1)) { $ceklisnya=""; $pstsapvoleh="Belum Approve SPV/AM"; }
                    }elseif ($ppilihsts=="UNAPPROVE") {
                        if (!empty($pnodivisi)) { $ceklisnya=""; $pstsapvoleh="<span style='color:blue;'>$pnodivisi</span>"; }
                        
                        $pedit="";
                    }
                    
                    if ($ppilihsts=="REJECT") {
                        $ceklisnya="";
                        $print="";
                        $pstsapvoleh="";
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$ceklisnya</td>";
                    echo "<td nowrap>$print &nbsp; &nbsp; $pedit</td>";
                    echo "<td nowrap>$pbln</td>";
                    echo "<td nowrap>$nama_pengajuan</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap>$pnmcabang</td>";
                    echo "<td nowrap align='right'>$psldawal</td>";
                    echo "<td nowrap align='right'>$prpjumlah</td>";
                    echo "<td nowrap align='right'>$pjmlmintarp</td>";
                    echo "<td >$pketerangan</td>";
                    echo "<td nowrap>$pstsapvoleh</td>";
                    echo "</tr>";
                    
                    
                    $no++;
                }
                ?>
            </tbody>
                
        </table>
        
    </div>
    
    
    <?PHP
    if ($ppilihsts=="UNAPPROVE") {
    ?>
        <div class='clearfix'></div>
        <div class="well" style="margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;"><!--overflow: auto; -->
            <input class='btn btn-success' type='button' name='buttonapv' value='Batal Proses' 
               onClick="ProsesDataUnApprove('unapprove', 'chkbox_br[]')">
        </div>
    <?PHP
    }
    ?>
    
    <?PHP
    if ($ppilihsts=="APPROVE") {
    ?>
        <div class='clearfix'></div>
        <div class="well" style="margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;"><!--overflow: auto; -->
            <input class='btn btn-warning' type='button' name='buttonapv' value='Proses' 
               onClick="ProsesDataApprove('simpan_ttdallam', 'chkbox_br[]')">
            &nbsp; &nbsp; &nbsp; &nbsp; 
            <input class='btn btn-danger' type='button' name='buttonapvrjk' value='Reject' 
               onClick="ProsesDataReject('reject', 'chkbox_br[]')">
        </div>
    <?PHP
    }
    ?>
    
    
</form>


<style>
    .divnone {
        display: none;
    }
    #dttblproskkcbfin th {
        font-size: 13px;
    }
    #dttblproskkcbfin td { 
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
    z-index:1;
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>