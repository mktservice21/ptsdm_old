<?php
session_start();

    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    
    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_ubahget_id.php";
    
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
    
    
    $_SESSION['BRRTNAPVSTS']=$ppilihsts;
    $_SESSION['BRRTNAPVBLN1']=$mytgl1;
    $_SESSION['BRRTNAPVBLN2']=$mytgl2;
    $_SESSION['BRRTNAPVBY']=$pkaryawanid;
    
    $pbulan1= date("Ym", strtotime($mytgl1));
    $pbulan2= date("Ym", strtotime($mytgl2));
    
    
    
    $pidgroup=$_SESSION['GROUP'];
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpbrtnapvho01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpbrtnapvho02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmpbrtnapvho03_".$puserid."_$now ";
    
    
    $query = "select a.idrutin, a.karyawanid, b.nama as nama_kry, a.jumlah, 
        a.tgl, a.bulan, a.kodeperiode, a.periode1, a.periode2, 
        a.keterangan, a.atasan4, a.tgl_atasan4, a.fin, a.tgl_fin 
        from dbmaster.t_brrutin0 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId 
        WHERE 1=1 ";

    $query .=" AND ( (DATE_FORMAT(a.bulan,'%Y%m') BETWEEN '$pbulan1' AND '$pbulan2') OR ((DATE_FORMAT(a.tgl,'%Y%m') BETWEEN '$pbulan1' AND '$pbulan2')) ) ";

    if ($pidgroup=="46") {
        $query .=" AND ( atasan4='$pkaryawanid' OR a.karyawanid='$pkaryawanid' ) ";
    }elseif ($pidgroup=="38") {//asykur
        $query .=" AND IFNULL(a.divisi,'')='HO' AND atasan4='$pkaryawanid' ";
    }else{
        $query .=" AND atasan4='$pkaryawanid' ";
    }
    
    if ($ppilihsts=="REJECT") {
        $query .=" AND IFNULL(a.stsnonaktif,'')='Y' ";
    }else{
        $query .=" AND IFNULL(a.stsnonaktif,'')<>'Y' ";
        
        if ($ppilihsts=="ALLDATA") {

        }else{
            if ($ppilihsts=="APPROVE") {
                $query .=" AND ( IFNULL(a.tgl_atasan4,'')='' OR IFNULL(a.tgl_atasan4,'0000-00-00')='0000-00-00' OR IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
            }elseif ($ppilihsts=="UNAPPROVE") {
                $query .=" AND ( IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00')<>'0000-00-00' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }
        }
        
    }
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "Alter table $tmp01 ADD COLUMN pbukti VARCHAR(1), ADD COLUMN absen_rutin VARCHAR(1) DEFAULT 'N'"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 as a JOIN dbimages.img_brrutin1 as b on a.idrutin=b.idrutin SET a.pbukti='Y' WHERE IFNULL(b.idrutin,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 as a JOIN dbmaster.t_karyawan_posisi as b on a.karyawanid=b.karyawanId SET a.absen_rutin=b.absen_rutin"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
?>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        
        <div class="title_left">
            <h4 style="font-size : 12px;">
                <?PHP
                    
                    $ptext="";
                    if ($ppilihsts=="APPROVE") $ptext="Data Yang Belum DiApprove";
                    if ($ppilihsts=="UNAPPROVE") $ptext="Data Yang Sudah DiApprove";
                    if ($ppilihsts=="REJECT") $ptext="Data Yang DiReject";
                    if ($ppilihsts=="PENDING") $ptext="Data Yang DiPending";
                    if ($ppilihsts=="SEMUA" OR $ppilihsts=="ALLDATA") $ptext="Data Yang Belum dan Sudah Approve";

                    echo "<b>$ptext"
                            . "<p/>&nbsp;*) <span style='color:red;'>klik nama untuk melihat detail pengajuan</span></b>";
                ?>
            </h4>
        </div>
        <div class="clearfix"></div>
        
        <?PHP
        $pchkall = "<input type='checkbox' id='chkbtnbr' name='chkbtnbr' value='deselect' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" checked/>";
        ?>
        
        <table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='10px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='40px'>Yang Membuat</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Tgl. Input</th>
                    <th width='200px'>Periode</th>
                    <th width='50px'>Bukti</th>
                    <th width='50px'></th>
                    <th width='50px'>Keterangan</th>
                    <th width='50px'>ID</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 ";
                if ($ppilihsts=="UNAPPROVE") {
                    $query .= " order by idrutin DESC";
                }else{
                    $query .= " order by idrutin";
                }
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidrutin=$row1['idrutin'];
                    $pidkry=$row1['karyawanid'];
                    $pnmkry=$row1['nama_kry'];
                    $ptgl=$row1['tgl'];
                    $pbln=$row1['bulan'];
                    $pkdperiode=$row1['kodeperiode'];
                    $ptgl01=$row1['periode1'];
                    $ptgl02=$row1['periode2'];
                    $pketerangan=$row1['keterangan'];
                    $ppsudhbukti=$row1['pbukti'];
                    $pabsrutin = $row1["absen_rutin"];
                    
                    $pjumlah=$row1['jumlah'];
                    
                    $pidnoget=encodeString($pidrutin);

                    $apv1="";
                    $apv2="";
                    $apv3="";
                    $apv4="";
                    $apvfin="";
                    
                    $ptglatasan4=$row1["tgl_atasan4"];
                    $ptglfin=$row1["tgl_fin"];
                    
                    $ptgl= date("d/m/Y", strtotime($ptgl));
                    $ptgl01= date("d/m/Y", strtotime($ptgl01));
                    $ptgl02= date("d/m/Y", strtotime($ptgl02));
                    
                    
                    $pperiode=$ptgl01." s/d. ".$ptgl02;
                    
                    $pjumlah=number_format($pjumlah,0,",",",");
                    
                    if ($ptglatasan4=="0000-00-00" OR $ptglatasan4=="0000-00-00 00:00:00") $ptglatasan4="";
                    if ($ptglfin=="0000-00-00" OR $ptglfin=="0000-00-00 00:00:00") $ptglfin="";
                        
                    if (!empty($ptglatasan4)) $apv4=date("d F Y, h:i:s", strtotime($ptglatasan4));
                    if (!empty($ptglfin)) $apvfin=date("d F Y, h:i:s", strtotime($ptglfin));
                    
                    
                    $pprint="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=entrybrrutinho&brid=$pidnoget&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$pnmkry</a>";
                    
                    $pprintabs_inv="<a title='Print / Cetak' href='#' class='btn btn-warning btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=entrybrrutinho&brid=$pidnoget&iprint=absinvprint',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "Absensi Invalid</a>";
                    
                    
                    $ceklisnya = "<input type='checkbox' value='$pidrutin' name='chkbox_br[]' id='chkbox_br[$pidrutin]' class='cekbr'>";
                    
                    
                    $pbukti="";
                    if (!empty($ppsudhbukti)) {
                        $pbukti="<a title='lihat bukti' href='#' class='btn btn-danger btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=entrybrrutin&brid=$pidrutin&iprint=bukti',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Lihat</a>";
                    }
                    
                    $peditketeaja="";
                    if (empty($apvfin)) {
                        $peditketeaja="<a title='lihat bukti' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=editdataketerutincalk&brid=$pidrutin&iprint=nrutin',"
                            . "'Ratting','width=600,height=350,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Edit Ket.</a>";
                    }
                    
                    if (!empty($ptglfin)) {
                        $ceklisnya="";
                    }
                    
                    if ($pabsrutin<>"Y") {
                        $pprintabs_inv="";
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$ceklisnya</td>";
                    echo "<td nowrap>$pprint $pprintabs_inv</td>";
                    echo "<td nowrap align='right'>$pjumlah</td>";
                    echo "<td nowrap>$ptgl</td>";
                    echo "<td nowrap>$pperiode</td>";
                    echo "<td nowrap>$pbukti</td>";
                    echo "<td nowrap>$peditketeaja</td>";
                    echo "<td >$pketerangan</td>";
                    echo "<td nowrap>$pidrutin</td>";
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
            <input class='btn btn-success' type='button' name='buttonapv' value='UnApprove' 
               onClick="ProsesDataUnApprove('unapprove', 'chkbox_br[]')">
        </div>
    <?PHP
    }
    ?>
    
    <!-- tanda tangan -->
    <?PHP
        if ($ppilihsts=="APPROVE") {
            echo "<div class='col-sm-5'>";
            include "ttd_approvebrho.php";
            echo "</div>";
        }
    ?>
    
    
</form>



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
    
    
    function ProsesDataUnApprove(ket, cekbr){
        //alert(ket+", "+cekbr);
        var cmt = confirm('Apakah akan melakukan proses unapprove ...?');
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
            url:"module/mod_apv_biayarutinho/aksi_apvbiayarutinho.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
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
        var ekaryawan=document.getElementById('cb_karyawan').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/mod_apv_biayarutinho/aksi_apvbiayarutinho.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=unapprove"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                pilihData('approve');
                alert(data);
            }
        });
        
        
    }
    
    
</script>
<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>

