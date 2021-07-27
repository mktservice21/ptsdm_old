<?php
session_start();


    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $pidgroup=$_SESSION['GROUP'];
    
    include "../../../config/koneksimysqli.php";
    
    //ini_set('display_errors', '0');
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    
    $ppilihsts = strtoupper($_POST['eket']);
    $mytgl1 = $_POST['uperiode1'];
    $mytgl2 = $_POST['uperiode2'];
    $pkaryawanid = $_POST['ukaryawan'];
    $pstsapv = $_POST['uketapv'];
    
    
    $_SESSION['APVCUTISTS']=$ppilihsts;
    $_SESSION['APVCUTIBLN1']=$mytgl1;
    $_SESSION['APVCUTIBLN2']=$mytgl2;
    $_SESSION['APVCUTIAPVBY']=$pkaryawanid;
    
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
    if ($pjabatanid=="18" OR $pjabatanid=="10") {
        $papproveby="apvspv";
    }elseif ($pjabatanid=="08") {
        $papproveby="apvdm";
    }elseif ($pjabatanid=="20") {
        $papproveby="apvsm";
    }elseif ($pjabatanid=="04" OR $pjabatanid=="05" OR $pjabatanid=="36") {
        $papproveby="apvgsm";
    }elseif ($pjabatanid=="01") {
        $papproveby="apvcoo";
    }else{
        if ($pidgroup=="46") {
            $papproveby="apvcoo";
        }elseif ($pidgroup=="8") {
            $papproveby="apvgsm";
        }
    }
    
    if (empty($pjabatanid) OR empty($papproveby) OR empty($pkaryawanid)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    

    
    //echo "$pkaryawanid : $pjabatanid, $plvlposisi $pbulan1 - $pbulan2"; exit;
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpapvcutiet01_".$userid."_$now ";
    $tmp02 =" dbtemp.tmpapvcutiet02_".$userid."_$now ";
    $tmp03 =" dbtemp.tmpapvcutiet03_".$userid."_$now ";
    
    
    
    $query = "SELECT distinct a.idcuti, a.tglinput, a.karyawanid, c.nama as nama_karyawan, a.jabatanid, "
            . " a.id_jenis, d.nama_jenis, a.keperluan, a.bulan1, a.bulan2, "
            . " a.atasan1, a.atasan2, a.atasan3, a.atasan4, atasan5, "
            . " a.tgl_atasan1, a.tgl_atasan2, a.tgl_atasan3, a.tgl_atasan4, a.tgl_atasan5, a.hrd_user, a.hrd_date FROM hrd.t_cuti0 as a "
            . " LEFT JOIN hrd.t_cuti1 as b on a.idcuti=b.idcuti JOIN hrd.karyawan as c on a.karyawanid=c.karyawanid"
            . " LEFT JOIN hrd.jenis_cuti as d on a.id_jenis=d.id_jenis "
            . " WHERE 1=1 ";
    $query .=" AND ( (b.tanggal BETWEEN '$pbulan1' AND '$pbulan2') "
            . " OR ( (a.bulan1 BETWEEN '$pbulan1' AND '$pbulan2') OR (a.bulan2 BETWEEN '$pbulan1' AND '$pbulan2') ) "
            . " OR (a.tglinput BETWEEN '$pbulan1' AND '$pbulan2') "
            . " )";
    
    if ($papproveby=="apvdm") {
        $query .= " AND a.atasan2='$pkaryawanid' ";
    }elseif ($papproveby=="apvsm") {
        $query .= " AND a.atasan3='$pkaryawanid' ";
    }elseif ($papproveby=="apvgsm") {
        
        if ($pkaryawanid=="0000000159") {
            $query .= " AND a.atasan4='$pkaryawanid' AND ( a.jabatanid NOT IN ('15', '38') OR (a.jabatanid='38' AND a.karyawanid='0000002073') ) ";
        }else{
            $query .= " AND a.atasan4='$pkaryawanid' AND a.jabatanid NOT IN ('15', '38') ";
        }
        
    }elseif ($papproveby=="apvcoo") {
        $query .= " AND a.atasan5='$pkaryawanid' AND a.jabatanid IN ('05') ";
    }else{
        $query .= " AND a.atasan1='$pkaryawanid' ";
    }
    
    if ($ppilihsts=="REJECT") {
        $query .=" AND IFNULL(a.stsnonaktif,'')='Y' ";
    }else{
        
        $query .=" AND IFNULL(a.stsnonaktif,'')<>'Y' ";
        if ($ppilihsts=="ALLDATA") {

        }else{
            if ($ppilihsts=="APPROVE") {
                if ($papproveby=="apvdm") {
                    $query .= " AND (IFNULL(a.tgl_atasan2,'')='' OR IFNULL(a.tgl_atasan2,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                }elseif ($papproveby=="apvsm") {
                    $query .= " AND (IFNULL(a.tgl_atasan3,'')='' OR IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                }elseif ($papproveby=="apvgsm") {
                    $query .= " AND (IFNULL(a.tgl_atasan4,'')='' OR IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                    $query .= " AND (IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }elseif ($papproveby=="apvcoo") {
                    $query .= " AND (IFNULL(a.tgl_atasan5,'')='' OR IFNULL(a.tgl_atasan5,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                    $query .= " AND (IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }else{
                    $query .= " AND (IFNULL(a.tgl_atasan1,'')='' OR IFNULL(a.tgl_atasan1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                }
            }elseif ($ppilihsts=="UNAPPROVE") {
                if ($papproveby=="apvdm") {
                    $query .= " AND (IFNULL(a.tgl_atasan2,'')<>'' AND IFNULL(a.tgl_atasan2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }elseif ($papproveby=="apvsm") {
                    $query .= " AND (IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }elseif ($papproveby=="apvgsm") {
                    $query .= " AND (IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }elseif ($papproveby=="apvcoo") {
                    $query .= " AND (IFNULL(a.tgl_atasan5,'')<>'' AND IFNULL(a.tgl_atasan5,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }else{
                    $query .= " AND (IFNULL(a.tgl_atasan1,'')<>'' AND IFNULL(a.tgl_atasan1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }
            }
        }
        
    }
    
    
    //echo $query."<br/>";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN SAPV1 VARCHAR(1), ADD COLUMN SAPV2 VARCHAR(1), ADD COLUMN SAPV3 VARCHAR(1), ADD COLUMN SAPV4 VARCHAR(1), ADD COLUMN SAPV5 VARCHAR(1)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbttd.t_cuti_ttd b on a.idcuti=b.idcuti SET a.SAPV1='Y' WHERE IFNULL(gbr_atasan1,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp01 a JOIN dbttd.t_cuti_ttd b on a.idcuti=b.idcuti SET a.SAPV2='Y' WHERE IFNULL(gbr_atasan2,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp01 a JOIN dbttd.t_cuti_ttd b on a.idcuti=b.idcuti SET a.SAPV3='Y' WHERE IFNULL(gbr_atasan3,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp01 a JOIN dbttd.t_cuti_ttd b on a.idcuti=b.idcuti SET a.SAPV4='Y' WHERE IFNULL(gbr_atasan4,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp01 a JOIN dbttd.t_cuti_ttd b on a.idcuti=b.idcuti SET a.SAPV5='Y' WHERE IFNULL(gbr_atasan5,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $sql = "select a.idcuti, a.tanggal FROM hrd.t_cuti1 as a JOIN $tmp01 as b on a.idcuti=b.idcuti";
    $query = "create TEMPORARY table $tmp02 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp01 ADD sudahapprove varchar(1)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    if ($ppilihsts=="APPROVE") {
        if ($papproveby=="apvspv") {
            $query = "UPDATE $tmp01 SET sudahapprove='Y' WHERE jabatanid in ('15', '38') AND IFNULL(tgl_atasan1,'')<>'' AND IFNULL(tgl_atasan1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }elseif ($papproveby=="apvdm") {
            $query = "UPDATE $tmp01 SET sudahapprove='Y' WHERE jabatanid in ('15', '10', '18', '38') AND IFNULL(tgl_atasan1,'')<>'' AND IFNULL(tgl_atasan1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }elseif ($papproveby=="apvsm") {
            $query = "UPDATE $tmp01 SET sudahapprove='Y' WHERE jabatanid in ('15', '10', '18', '08', '38') AND IFNULL(tgl_atasan2,'')<>'' AND IFNULL(tgl_atasan2,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }elseif ($papproveby=="apvgsm") {
            $query = "UPDATE $tmp01 SET sudahapprove='Y' WHERE jabatanid in ('10', '18', '08', '20') AND IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }elseif ($papproveby=="apvcoo") {
            $query = "UPDATE $tmp01 SET sudahapprove='Y' WHERE jabatanid in ('05') AND IFNULL(tgl_atasan5,'')<>'' AND IFNULL(tgl_atasan5,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }
    }
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
            url:"module/marketing/mkt_apvcutieth/aksi_apvcutieth.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
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
            if (txt=="") {
                alert("alasan harus diisi...");
                return false;
            }
        }
        
        var ekaryawan=document.getElementById('cb_karyawan').value;
            
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $.ajax({
            type:"post",
            url:"module/marketing/mkt_apvcutieth/aksi_apvcutieth.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
            data:"ket=unapprove"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt,
            success:function(data){
                pilihData('approve');
                alert(data);
            }
        });
        
        
    }
    
    
</script>

<?PHP
echo "<div style='font-weight:bold; color:blue;'>";
if ($ppilihsts=="APPROVE") {
    echo "DATA YANG BELUM DIAPPROVE";
}elseif ($ppilihsts=="UNAPPROVE") {
    echo "DATA YANG SUDAH DIAPPROVE";
}elseif ($ppilihsts=="REJECT") {
    echo "DATA REJECT";
}
echo "</div>";
?>
<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content' style="overflow-x:auto; max-height:500px">
        
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
                    <th width='50px'>&nbsp;</th>
                    <th width='50px'>Karyawan</th>
                    <th width='50px'>Jenis</th>
                    <th width='50px'>Keperluan</th>
                    <th width='200px'>Periode</th>
                    <th width='50px'>Satus Approve</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by IFNULL(sudahapprove,'ZZ'), idcuti";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidcuti=$row1['idcuti'];
                    $pnmkaryawan=$row1['nama_karyawan'];
                    $pidjenis=$row1['id_jenis'];
                    $pnmjenis=$row1['nama_jenis'];
                    $pkeperluan=$row1['keperluan'];
                    
                    $nbln1=$row1['bulan1'];
                    $nbln2=$row1['bulan2'];
					
                    
                    $ptglatasan1=$row1['tgl_atasan1'];
                    $ptglatasan2=$row1['tgl_atasan2'];
                    $ptglatasan3=$row1['tgl_atasan3'];
                    $ptglatasan4=$row1['tgl_atasan4'];
                    $ptglatasan5=$row1['tgl_atasan5'];
                    $ptglhrd=$row1['hrd_date'];
                    
                    $pidatasan1=$row1['atasan1'];
                    $pidatasan2=$row1['atasan2'];
                    $pidatasan3=$row1['atasan3'];
                    $pidatasan4=$row1['atasan4'];
                    $pidatasan5=$row1['atasan5'];
                    $puserhrd=$row1['hrd_user'];
                    
                    
                    if ($ptglatasan1=="0000-00-00" OR $ptglatasan1=="0000-00-00 00:00:00") $ptglatasan1="";
                    if ($ptglatasan2=="0000-00-00" OR $ptglatasan2=="0000-00-00 00:00:00") $ptglatasan2="";
                    if ($ptglatasan3=="0000-00-00" OR $ptglatasan3=="0000-00-00 00:00:00") $ptglatasan3="";
                    if ($ptglatasan4=="0000-00-00" OR $ptglatasan4=="0000-00-00 00:00:00") $ptglatasan4="";
                    if ($ptglatasan5=="0000-00-00" OR $ptglatasan5=="0000-00-00 00:00:00") $ptglatasan5="";
                    if ($ptglhrd=="0000-00-00" OR $ptglhrd=="0000-00-00 00:00:00") $ptglhrd="";
                    
                    $pketgsmhos="GSM";
                    $npmdl="mktformcutieth";
                    
                    $print="<a title='Detail / Print' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=$npmdl&brid=$pidcuti&iprint=detail',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "View</a>";
                    
                    $ceklisnya = "<input type='checkbox' value='$pidcuti' name='chkbox_br[]' id='chkbox_br[$pidcuti]' class='cekbr'>";
                    
                    
                    $pstsapvoleh="";
                    
                    if ($ppilihsts=="UNAPPROVE") {
                        
                    }
                    
                    if ($ppilihsts=="APPROVE") {
                        if ($papproveby=="apvdm") {
                            if (empty($ptglatasan1)) $ceklisnya="";
                        }elseif ($papproveby=="apvsm") {
                            if (empty($ptglatasan2)) $ceklisnya="";
                        }elseif ($papproveby=="apvgsm") {
                            if (empty($ptglatasan3)) $ceklisnya="";
                        }else{
                            
                        }
                        
                        if (empty($ptglatasan4) AND !empty($pidatasan4)) { $pstsapvoleh="Belum Approve $pketgsmhos"; }
                        if (empty($ptglatasan3) AND !empty($pidatasan3)) { $pstsapvoleh="Belum Approve SM"; }
                        if (empty($ptglatasan2) AND !empty($pidatasan2)) { $pstsapvoleh="Belum Approve DM"; }
                        if (empty($ptglatasan1) AND !empty($pidatasan1)) { $pstsapvoleh="Belum Approve SPV/AM"; }
                        
                        if (!empty($pstsapvoleh)) {
                            $pstsapvoleh="<span style='color:red;'>$pstsapvoleh</span>";
                        }
                        
                    }elseif ($ppilihsts=="UNAPPROVE") {
                        if ($papproveby=="apvdm") {
                            if (!empty($ptglatasan4) AND !empty($pidatasan4)) $ceklisnya="";
                            if (!empty($ptglatasan3) AND !empty($pidatasan3)) $ceklisnya="";
                        }elseif ($papproveby=="apvsm") {
                            if (!empty($ptglatasan4) AND !empty($pidatasan4)) $ceklisnya="";
                        }elseif ($papproveby=="apvgsm") {
                            
                        }else{
                            if (!empty($ptglatasan4) AND !empty($pidatasan4)) $ceklisnya="";
                            if (!empty($ptglatasan3) AND !empty($pidatasan3)) $ceklisnya="";
                            if (!empty($ptglatasan2) AND !empty($pidatasan2)) $ceklisnya="";
                        }
                        
                        if (!empty($ptglatasan1) AND !empty($pidatasan1)) $pstsapvoleh="Sudah Approve SPV/AM";
                        if (!empty($ptglatasan2) AND !empty($pidatasan2)) $pstsapvoleh="Sudah Approve DM";
                        if (!empty($ptglatasan3) AND !empty($pidatasan3)) $pstsapvoleh="Sudah Approve SM";
                        if (!empty($ptglatasan4) AND !empty($pidatasan4)) $pstsapvoleh="Sudah Approve $pketgsmhos";
                        
                        if (!empty($ptglhrd)) {
                            $pstsapvoleh="<span style='color:blue;'>Sudah Proses HRD</span>";
                            $ceklisnya="";
                        }
                        
                    }
                    
                    
                    if ($ppilihsts=="REJECT") {
                        $ceklisnya="";
                        $print="";
                        $pstsapvoleh="";
                    }
                    
                    if ($pidjenis=="02") {
                        $nbln1 = date('d F Y', strtotime($nbln1));
                        $nbln2 = date('d F Y', strtotime($nbln2));
                    }else{
                        $nbln1 = date('F Y', strtotime($nbln1));
                        $nbln2 = date('F Y', strtotime($nbln2));
                    }
                    
                    $plewattgl=false; $ctglpl=""; $ctglpl1=""; $ctglpl2="";
                    $query = "select distinct tanggal from $tmp02 WHERE idcuti='$pidcuti' order by tanggal";
                    $tampil0=mysqli_query($cnmy, $query);
                    $ketemu0=mysqli_num_rows($tampil0);
                    if ((INT)$ketemu0>0) {
                        $pawal=false;
                        while ($row0=mysqli_fetch_array($tampil0)) {
                            $tgl_p=$row0['tanggal'];
                            if (!empty($tgl_p)) {
                                $tgl_p = date('d F Y', strtotime($tgl_p));

                                $ctglpl .=$tgl_p.", ";

                                if ($pawal==false) {
                                    $ctglpl1=$tgl_p;
                                    $pawal=true;
                                }
                                $ctglpl2=$tgl_p;

                                $plewattgl=true;
                            }
                        }
                    }

                    if (!empty($ctglpl)) $ctglpl=substr($ctglpl, 0, -2);
                    
                    $ntglpilih="";
                    if ($pidjenis=="02") {
                        if ($plewattgl==true)
                            $ntglpilih=$nbln1." s/d. ".$nbln2." (".$ctglpl1." - ".$ctglpl2.")";
                        else
                            $ntglpilih=$nbln1." s/d. ".$nbln2;
                    }else{
                        $ntglpilih=$ctglpl;
                    }
                
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$ceklisnya</td>";
                    echo "<td nowrap>$print</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap>$pnmjenis</td>";
                    echo "<td nowrap>$pkeperluan</td>";
                    echo "<td >$ntglpilih</td>";
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
            include "ttd_aprovecuti.php";
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
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table IF EXISTS $tmp03");
    
    mysqli_close($cnmy);
?>