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
    
    
    $_SESSION['MAPVKDSTS']=$ppilihsts;
    $_SESSION['MAPVKDBLN1']=$mytgl1;
    $_SESSION['MAPVKDBLN2']=$mytgl2;
    $_SESSION['MAPVKDAPVBY']=$pkaryawanid;
    
    $pbulan1= date("Y-m-01", strtotime($mytgl1));
    $pbulan2= date("Y-m-t", strtotime($mytgl2));
    
    $tampil=mysqli_query($cnmy, "select jabatanId from hrd.karyawan where karyawanid='$pkaryawanid'");
    $pr= mysqli_fetch_array($tampil);
    $pjabatanid=$pr['jabatanId'];
    if (empty($pjabatanid)) {
        $tampil=mysqli_query($cnmy, "select jabatanId from dbmaster.t_karyawan_posisi where karyawanid='$pkaryawanid'");
        $pr= mysqli_fetch_array($tampil);
        $pjabatanid=$pr['jabatanId'];
    }
    
    
    
    $tampil=mysqli_query($cnmy, "select LEVELPOSISI from dbmaster.jabatan_level WHERE jabatanId='$pjabatanid'");
    $pr= mysqli_fetch_array($tampil);
    $plvlposisi=$pr['LEVELPOSISI'];
    
    $papproveby="";
    if ($pjabatanid=="01" OR $pjabatanid=="02") {
        $papproveby="apvdirmkt";
    }elseif ($pjabatanid=="04" OR $pjabatanid=="05" OR $pjabatanid=="36") {
        $papproveby="apvgsm";
    }
    
    if (empty($pjabatanid) OR empty($papproveby) OR empty($pkaryawanid)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    

    
    //echo "$pkaryawanid : $pjabatanid, $plvlposisi $pbulan1 - $pbulan2"; exit;
    
    $puserpilihinput=$_SESSION['IDCARD'];
    $pidgroup=$_SESSION['GROUP'];
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPKDMAPV01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPKDMAPV02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPKDMAPV03_".$userid."_$now ";
    
    
    $pfileterregion="";
    if ($pmodule == "approvedirekturklaimadm") {
        if ($pidgroup=="40" OR $pidgroup=="43") {
            $pfileterregion=" AND (IFNULL(a.user1,'')='$puserpilihinput' OR IFNULL(a.user1,'')='$userid') ";
        }
    }
    
    $query = "SELECT a.klaimId as klaimid, a.pengajuan, a.karyawanid as karyawanid, c.nama as nama_karyawan, "
            . " a.distid, d.nama nama_distributor, a.tgl, a.bulan, a.periode1, a.periode2, a.tgltrans, "
            . " a.user1, a.COA4, "
            . " a.aktivitas1, a.aktivitas2, "
            . " b.atasan4, b.tgl_atasan4, b.atasan5, b.tgl_atasan5, a.jenisklaim, a.jumlah "
            . " FROM hrd.klaim a "
            . " JOIN dbttd.klaim_ttd b on a.klaimId=b.klaimId "
            . " LEFT JOIN hrd.karyawan c on a.karyawanid=c.karyawanid "
            . " LEFT JOIN MKT.distrib0 d ON a.distid = d.Distid "
            . " WHERE ( (a.tgl BETWEEN '$pbulan1' AND '$pbulan2') OR (a.bulan BETWEEN '$pbulan1' AND '$pbulan2') ) $pfileterregion ";
    if ($papproveby=="apvdirmkt") {
        $query .= " AND (IFNULL(b.tgl_atasan4,'')<>'' AND IFNULL(b.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
    }else{
        if ($pjabatanid=="36") {
            $query .= " AND IFNULL(a.pengajuan,'') IN ('OTC', 'CHC', 'OT') AND IFNULL(a.region,'')='' ";
        }else{
            if ($pkaryawanid=="0000000159") {
                $query .= " AND IFNULL(a.region,'')='T' ";
            }elseif ($pkaryawanid=="0000000158") {
                $query .= " AND IFNULL(a.region,'')='B' ";
            }else{
                $query .= " AND IFNULL(a.region,'')='XXXXXXXX' ";
            }
        }
    }
    
    if ($ppilihsts=="REJECT") {
        //$query .=" AND IFNULL(a.stsnonaktif,'')='Y' ";
    }else{
        
        //$query .=" AND IFNULL(stsnonaktif,'')<>'Y' ";
        if ($ppilihsts=="ALLDATA") {

        }else{
            if ($ppilihsts=="APPROVE") {
                if ($papproveby=="apvdirmkt") {
                    $query .= " AND (IFNULL(tgl_atasan5,'')='' OR IFNULL(tgl_atasan5,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                }else{
                    $query .= " AND (IFNULL(tgl_atasan4,'')='' OR IFNULL(tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                }
            }elseif ($ppilihsts=="UNAPPROVE") {
                if ($papproveby=="apvdirmkt") {
                    $query .= " AND (IFNULL(tgl_atasan5,'')<>'' AND IFNULL(tgl_atasan5,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }else{
                    $query .= " AND (IFNULL(tgl_atasan4,'')<>'' AND IFNULL(tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }
            }
        }
        
    }
    
    

    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN SAPV5 VARCHAR(1), ADD COLUMN SAPV4 VARCHAR(1)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    

    $query = "UPDATE $tmp01 a JOIN dbttd.klaim_ttd b on a.klaimId=b.klaimId SET a.SAPV5='Y' WHERE IFNULL(gbr_atasan5,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp01 a JOIN dbttd.klaim_ttd b on a.klaimId=b.klaimId SET a.SAPV4='Y' WHERE IFNULL(gbr_atasan4,'')<>''"; 
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
            url:"module/mod_apv_klaimdiscmkt/aksi_apvklaimdiscmkt.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
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
            url:"module/mod_apv_klaimdiscmkt/aksi_apvklaimdiscmkt.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
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
        <table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='10px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='40px'>ID</th>
                    <th width='50px'>Bulan</th>
                    <th width='50px'>Periode</th>
                    <th width='100px'>Yg. Membuat</th>
                    <th width='50px'>Distributor</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Jenis Klaim</th>
                    <th width='50px'>Keterangan</th>
                    <th width='50px'>Satus Approve</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by klaimid DESC";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidklaim=$row1['klaimid'];
                    $ptgl=$row1['tgl'];
                    $pbln = $row1["bulan"];
                    $pper1 = $row1["periode1"];
                    $pper2 = $row1["periode2"];
                    $ptlgtrans = $row1["tgltrans"];
                    
                    $pnmkaryawan=$row1['nama_karyawan'];
                    $pidpengajuan=$row1['pengajuan'];
                    $pnmdist=$row1['nama_distributor'];
                    $pketerangan=$row1['aktivitas1'];
                    $pketerangan1=$row1['aktivitas1'];
                    $pketerangan2=$row1['aktivitas2'];
                    $pjenisklaim= $row1["jenisklaim"];
                    $prpjumlah=$row1['jumlah'];
                    
                    $ptglatasan5=$row1['tgl_atasan5'];
                    $ptglatasan4=$row1['tgl_atasan4'];
                    
                    $pidatasan5=$row1['atasan5'];
                    $pidatasan4=$row1['atasan4'];
                    
                    $prpjumlah=number_format($prpjumlah,0,",",",");
                    
                    $ptgl= date("d/m/Y", strtotime($ptgl));
                    if ($ptlgtrans=="0000-00-00") $ptlgtrans="";
                    if ($pbln=="0000-00-00") $pbln="";
                    if ($pper1=="0000-00-00") $pper1="";
                    if ($pper2=="0000-00-00") $pper2="";
                    
                    if (!empty($ptlgtrans)) $ptlgtrans =date("d/m/Y", strtotime($ptlgtrans));
                    if (!empty($pbln)) $pbln =date("F Y", strtotime($pbln));
                    if (!empty($pper1)) $pper1 =date("d/m/Y", strtotime($pper1));
                    if (!empty($pper2)) $pper2 =date("d/m/Y", strtotime($pper2));
                    
                    $pperiode="";
                    if (!empty($pper1)) $pperiode="$pper1 s/d.<br/>$pper2";
                    
                    $pnmpengajuan=$pidpengajuan;
                    if ($pidpengajuan=="EAGLE") $pnmpengajuan="EAGLE";
                    elseif ($pidpengajuan=="PIGEO") $pnmpengajuan="PIGEON";
                    elseif ($pidpengajuan=="PEACO") $pnmpengajuan="PEACOCK";
                    elseif ($pidpengajuan=="OTHER") $pnmpengajuan="OTHERS";
                    elseif ($pidpengajuan=="HO") $pnmpengajuan="HO";
                    elseif ($pidpengajuan=="OTC" OR $pidpengajuan=="OT" OR$pidpengajuan=="CHC") $pnmpengajuan="CHC";
                    
                    
                    
                    
                    if ($ptglatasan5=="0000-00-00" OR $ptglatasan5=="0000-00-00 00:00:00") $ptglatasan5="";
                    if ($ptglatasan4=="0000-00-00" OR $ptglatasan4=="0000-00-00 00:00:00") $ptglatasan4="";
                    

                    $print="<a title='Print / Cetak' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=bgtadmentrybrklaim&brid=$pidklaim&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$pidklaim</a>";
                    
                    $ceklisnya = "<input type='checkbox' value='$pidklaim' name='chkbox_br[]' id='chkbox_br[$pidklaim]' class='cekbr'>";
                    
                    if ($ppilihsts=="UNAPPROVE") {
                        
                    }
                    
                    if ($ppilihsts=="APPROVE") {
                        if ($papproveby=="apvgsm") {
                            //if (empty($ptglatasan3)) $ceklisnya="";
                        }else{
                            
                        }
                    }elseif ($ppilihsts=="UNAPPROVE") {
                        if ($papproveby=="apvgsm") {
                            if (!empty($ptglatasan5) AND !empty($pidatasan5)) $ceklisnya="";
                        }else{
                            
                        }
                    }
                    
                    $pstsapvoleh="";
                    
                    
                    if (!empty($ptglatasan4) AND !empty($pidatasan4)) $pstsapvoleh="Sudah Approve GSM";
                    if (!empty($ptglatasan5) AND !empty($pidatasan5)) $pstsapvoleh="Sudah Dir MKT";
                    
                    if ($ppilihsts=="REJECT") {
                        $ceklisnya="";
                        $print="";
                        $pstsapvoleh="";
                    }
                    
                    $pnamajenisklm="";
                    if ($pjenisklaim=="S") $pnamajenisklm="SDM ONLINE";
                    elseif ($pjenisklaim=="D") $pnamajenisklm="SKS ONLINE";

                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$ceklisnya</td>";
                    echo "<td nowrap>$print</td>";
                    echo "<td nowrap>$pbln</td>";
                    echo "<td nowrap>$pperiode</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap>$pnmdist</td>";
                    echo "<td nowrap align='right'>$prpjumlah</td>";
                    echo "<td nowrap>$pnamajenisklm</td>";
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
            include "ttd_apvklaimdiscmkt.php";
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

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>