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
    
    
    $_SESSION['APVPCHPRSTS']=$ppilihsts;
    $_SESSION['APVPCHPRBLN1']=$mytgl1;
    $_SESSION['APVPCHPRBLN2']=$mytgl2;
    $_SESSION['APVPCHPRAPVBY']=$pkaryawanid;
    
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
    
    if ($pmodule=="pchapvprbychc") {
        $papproveby="apvmgrchc";
    }elseif ($pmodule=="pchapvprbyho") {
        $papproveby="apvatasanho";
    }elseif ($pmodule=="pchapvprbycoo") {
        $papproveby="apvcoo";
    }
    
    if (empty($pjabatanid) OR empty($papproveby) OR empty($pkaryawanid)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    

    
    //echo "$pkaryawanid : $pjabatanid, $plvlposisi $pbulan1 - $pbulan2"; exit;
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpapvpchet01_".$userid."_$now ";
    $tmp02 =" dbtemp.tmpapvpchet02_".$userid."_$now ";
    $tmp03 =" dbtemp.tmpapvpchet03_".$userid."_$now ";
    
    
    
    $query = "SELECT distinct a.pengajuan, a.idpr, a.idtipe, a.tglinput, a.tanggal, a.karyawanid, b.nama as nama_karyawan, a.jabatanid, "
            . " a.icabangid, c.nama as nama_cabang, a.areaid, a.divisi, a.iddep, d.nama_dep, "
            . " a.aktivitas, a.jumlah, "
            . " a.atasan1, a.atasan2, a.atasan3, a.atasan4, atasan5, "
            . " a.tgl_atasan1, a.tgl_atasan2, a.tgl_atasan3, a.tgl_atasan4, a.tgl_atasan5, "
            . " a.validate1, a.tgl_validate1, a.validate2, a.tgl_validate2 "
            . " FROM dbpurchasing.t_pr_transaksi as a "
            . " LEFT JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN mkt.icabang as c on a.icabangid=c.icabangid "
            . " LEFT JOIN dbmaster.t_department as d on a.iddep=d.iddep "
            . " WHERE 1=1 ";
    $query .=" AND ( (a.tanggal BETWEEN '$pbulan1' AND '$pbulan2') "
            . " OR (a.tglinput BETWEEN '$pbulan1' AND '$pbulan2') "
            . " )";
    
    if ($pmodule=="pchapvprbychc") {
        $query .= " AND a.pengajuan IN ('OTC', 'CHC') ";
    }elseif ($pmodule=="pchapvprbyho") {
        $query .= " AND a.pengajuan IN ('HO') ";
    }elseif ($pmodule=="pchapvprbycoo") {
        
    }else{
        $query .= " AND a.pengajuan NOT IN ('OTC', 'CHC', 'HO') ";
    }
    
    if ($papproveby=="apvdm") {
        $query .= " AND a.atasan2='$pkaryawanid' ";
    }elseif ($papproveby=="apvsm") {
        $query .= " AND a.atasan3='$pkaryawanid' ";
    }elseif ($papproveby=="apvgsm") {
        $query .= " AND a.atasan4='$pkaryawanid' ";//AND a.jabatanid NOT IN ('15', '38')
    }elseif ($papproveby=="apvcoo") {
        $query .= " AND ( a.atasan5='$pkaryawanid' OR a.atasan4='$pkaryawanid' ) ";
    }elseif ($papproveby=="apvmgrchc") {
        $query .= " AND a.atasan4='$pkaryawanid' ";
    }elseif ($papproveby=="apvatasanho") {//, 
        $query .= " AND a.atasan4='$pkaryawanid' ";
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
                    $query .= " AND ( ";
                    $query .= " (IFNULL(a.tgl_atasan5,'')='' OR IFNULL(a.tgl_atasan5,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                    $query .= " OR ";
                    $query .= " (IFNULL(a.tgl_atasan4,'')='' OR IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                    $query .= " ) ";
                    $query .= " AND (IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }elseif ($papproveby=="apvmgrchc") {
                    $query .= " AND (IFNULL(a.tgl_atasan4,'')='' OR IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                    $query .= " AND (IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }elseif ($papproveby=="apvatasanho") {
                    $query .= " AND (IFNULL(a.tgl_atasan4,'')='' OR IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                    $query .= " AND (IFNULL(a.tgl_atasan3,'')<>'' AND IFNULL(a.tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
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
                }elseif ($papproveby=="apvmgrchc") {
                    $query .= " AND (IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }elseif ($papproveby=="apvatasanho") {
                    $query .= " AND (IFNULL(a.tgl_atasan4,'')<>'' AND IFNULL(a.tgl_atasan4,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
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
    
    $query = "UPDATE $tmp01 a JOIN dbttd.t_pr_transaksi_ttd b on a.idpr=b.idpr SET a.SAPV1='Y' WHERE IFNULL(gbr_atasan1,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp01 a JOIN dbttd.t_pr_transaksi_ttd b on a.idpr=b.idpr SET a.SAPV2='Y' WHERE IFNULL(gbr_atasan2,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp01 a JOIN dbttd.t_pr_transaksi_ttd b on a.idpr=b.idpr SET a.SAPV3='Y' WHERE IFNULL(gbr_atasan3,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp01 a JOIN dbttd.t_pr_transaksi_ttd b on a.idpr=b.idpr SET a.SAPV4='Y' WHERE IFNULL(gbr_atasan4,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    $query = "UPDATE $tmp01 a JOIN dbttd.t_pr_transaksi_ttd b on a.idpr=b.idpr SET a.SAPV5='Y' WHERE IFNULL(gbr_atasan5,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $sql = "select a.idpr, a.idbarang, a.namabarang, a.spesifikasi1, a.keterangan, a.jumlah, a.satuan, a.harga "
            . " FROM dbpurchasing.t_pr_transaksi_d as a JOIN $tmp01 as b on a.idpr=b.idpr";
    $query = "create TEMPORARY table $tmp02 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp01 ADD sudahapprove varchar(1), ADD COLUMN sudahisivendor varchar(1)"; 
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
        }elseif ($papproveby=="apvmgrchc") {
            $query = "UPDATE $tmp01 SET sudahapprove='Y' WHERE pengajuan in ('OTC', 'CHC') AND IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }elseif ($papproveby=="apvatasanho") {
            $query = "UPDATE $tmp01 SET sudahapprove='Y' WHERE pengajuan in ('HO') AND IFNULL(tgl_atasan3,'')<>'' AND IFNULL(tgl_atasan3,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
        }
        //
    }
    
    
    $query = "UPDATE $tmp01 as a JOIN dbpurchasing.t_pr_transaksi_po as b on a.idpr=b.idpr SET a.sudahisivendor='Y' WHERE IFNULL(b.aktif,'')='Y'";
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
            url:"module/purchasing/pch_apvpreth/aksi_apvpcheth.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
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
            url:"module/purchasing/pch_apvpreth/aksi_apvpcheth.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
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
                    <th width='50px'>Cabang</th>
                    <th width='50px'>Notes</th>
                    <th width='200px'>Barang</th>
                    <th width='50px'>Satus Approve</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by IFNULL(sudahapprove,'ZZ'), idpr";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidpr=$row1['idpr'];
                    $pnmkaryawan=$row1['nama_karyawan'];
                    $pidcabang=$row1['icabangid'];
                    $pnmcabang=$row1['nama_cabang'];
                    $pkeperluan=$row1['aktivitas'];
                    $ntipe=$row1['idtipe'];
                    $npengajuan=$row1['pengajuan'];
                    $psudahisivendor=$row1['sudahisivendor'];
                    
					
                    
                    $ptglatasan1=$row1['tgl_atasan1'];
                    $ptglatasan2=$row1['tgl_atasan2'];
                    $ptglatasan3=$row1['tgl_atasan3'];
                    $ptglatasan4=$row1['tgl_atasan4'];
                    $ptglatasan5=$row1['tgl_atasan5'];
                    $ptglval1=$row1['tgl_validate1'];
                    $ptglval2=$row1['tgl_validate2'];
                    
                    $pidatasan1=$row1['atasan1'];
                    $pidatasan2=$row1['atasan2'];
                    $pidatasan3=$row1['atasan3'];
                    $pidatasan4=$row1['atasan4'];
                    $pidatasan5=$row1['atasan5'];
                    $puserval1=$row1['validate1'];
                    $puserval2=$row1['validate2'];
                    
                    
                    if ($ptglatasan1=="0000-00-00" OR $ptglatasan1=="0000-00-00 00:00:00") $ptglatasan1="";
                    if ($ptglatasan2=="0000-00-00" OR $ptglatasan2=="0000-00-00 00:00:00") $ptglatasan2="";
                    if ($ptglatasan3=="0000-00-00" OR $ptglatasan3=="0000-00-00 00:00:00") $ptglatasan3="";
                    if ($ptglatasan4=="0000-00-00" OR $ptglatasan4=="0000-00-00 00:00:00") $ptglatasan4="";
                    if ($ptglatasan5=="0000-00-00" OR $ptglatasan5=="0000-00-00 00:00:00") $ptglatasan5="";
                    if ($ptglval1=="0000-00-00" OR $ptglval1=="0000-00-00 00:00:00") $ptglval1="";
                    if ($ptglval2=="0000-00-00" OR $ptglval2=="0000-00-00 00:00:00") $ptglval2="";
                    
                    
                    $pketgsmhos="GSM";
                    if ($papproveby=="apvmgrchc") $pketgsmhos="HOS";
                    elseif ($papproveby=="apvatasanho") $pketgsmhos="Atasan";
                    
                    $npmdl="pchpurchasereq";
                    
                    $pprint="<a title='Detail / Print' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=$npmdl&brid=$pidpr&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$pidpr</a>";
                    
                    $ceklisnya = "<input type='checkbox' value='$pidpr' name='chkbox_br[]' id='chkbox_br[$pidpr]' class='cekbr'>";
                    
                    
                    $pstsapvoleh="";
                    
                    if ($ppilihsts=="UNAPPROVE") {
                        
                    }
                    
                    if ($ppilihsts=="APPROVE") {
                        if ($npengajuan=="HO" OR $npengajuan=="OTC" OR $npengajuan=="CHC") {
                            if (empty($ptglatasan4) AND !empty($pidatasan4)) { $pstsapvoleh=""; }
                        }else{
                        
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
                        
                        }
                        if (!empty($pstsapvoleh)) {
                            $pstsapvoleh="<span style='color:red;'>$pstsapvoleh</span>";
                        }
                        
                    }elseif ($ppilihsts=="UNAPPROVE") {
                        
                        if ($npengajuan=="HO" OR $npengajuan=="OTC" OR $npengajuan=="CHC") {
                            
                        }else{
                            
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
                        
                        }
                        
                        if (!empty($ptglval1)) {
                            if ($ntipe=="102") {
                                $pstsapvoleh="<span style='color:blue;'>Sudah Proses IT</span>";
                                $ceklisnya="";
                            }else{
                                $pstsapvoleh="<span style='color:blue;'>Sudah Proses Purchasing</span>";
                                $ceklisnya="";
                            }
                        }
                        
                        if (!empty($ptglval2)) {
                            $pstsapvoleh="<span style='color:blue;'>Sudah Proses Purchasing</span>";
                            $ceklisnya="";
                        }
                        
                    }
                    
                    
                    if ($ppilihsts=="REJECT") {
                        $ceklisnya="";
                        $pprint="";
                        $pstsapvoleh="";
                    }
                    
                    
                    $plewattgl=false; $cnmbrg=""; $cnmbrg1=""; $cnmbrg2="";
                    $query = "select idpr, idbarang, namabarang, spesifikasi1, keterangan, jumlah, satuan, harga from $tmp02 WHERE idpr='$pidpr' order by namabarang";
                    $tampil0=mysqli_query($cnmy, $query);
                    $ketemu0=mysqli_num_rows($tampil0);
                    if ((INT)$ketemu0>0) {
                        $pawal=false;
                        while ($row0=mysqli_fetch_array($tampil0)) {
                            $pnmbarang=$row0['namabarang'];
                            $pjmlbrg=$row0['jumlah'];
                            $pstauanbrg=$row0['satuan'];
                            
                            $pjmlbrg=number_format($pjmlbrg,0,"","");
                            if (!empty($pnmbarang)) {

                                $cnmbrg .=$pnmbarang." (".$pjmlbrg.") ".$pstauanbrg.", ";

                                if ($pawal==false) {
                                    $cnmbrg1=$pnmbarang;
                                    $pawal=true;
                                }
                                $cnmbrg2=$pnmbarang;

                                $plewattgl=true;
                            }
                        }
                    }

                    if (!empty($cnmbrg)) $cnmbrg=substr($cnmbrg, 0, -2);
                
                    if ($psudahisivendor=="Y") {
                        $ceklisnya="";
                    }
                        
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$ceklisnya</td>";
                    echo "<td nowrap>$pprint</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap>$pnmcabang</td>";
                    echo "<td nowrap>$pkeperluan</td>";
                    echo "<td >$cnmbrg</td>";
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
            include "ttd_aprovepch.php";
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