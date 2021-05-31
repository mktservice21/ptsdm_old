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
    
    
    $_SESSION['APVPCHPOSTS']=$ppilihsts;
    $_SESSION['APVPCHPOBLN1']=$mytgl1;
    $_SESSION['APVPCHPOBLN2']=$mytgl2;
    $_SESSION['APVPCHPOAPVBY']=$pkaryawanid;
    
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
    
    if ($pmodule=="pchapvpobychc") {
        $papproveby="apvmgrchc";
    }elseif ($pmodule=="pchapvpobyho") {
        $papproveby="apvatasanho";
    }elseif ($pmodule=="pchapvpobycoo") {
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
    
    
    
    $query = "SELECT distinct a.idpo, a.tglinput, a.tanggal, a.kdsupp, d.NAMA_SUP as nama_sup, a.karyawanid, b.nama as nama_karyawan, "
            . " a.notes, a.idbayar, c.nama_bayar, a.tglkirim, a.note_kirim, a.status_bayar, "
            . " a.ppn, a.ppnrp, a.disc, a.discrp, a.jnspph, a.pph, a.pphrp, a.pembulatan, a.totalrp as jumlah, "
            . " a.dir1, a.tgl_dir1, a.dir2, a.tgl_dir2, a.userid "
            . " FROM dbpurchasing.t_po_transaksi as a "
            . " LEFT JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN dbpurchasing.t_jenis_bayar as c on a.idbayar=c.idbayar "
            . " JOIN dbmaster.t_supplier as d on a.kdsupp=d.KDSUPP "
            . " WHERE 1=1 ";
    $query .=" AND ( (a.tanggal BETWEEN '$pbulan1' AND '$pbulan2') "
            . " OR (a.tglinput BETWEEN '$pbulan1' AND '$pbulan2') "
            . " )";
    
    if ($papproveby=="apvdm") {
        
    }elseif ($papproveby=="apvsm") {
        
    }elseif ($papproveby=="apvgsm") {
        
    }elseif ($papproveby=="apvcoo") {
        //$query .= " AND a.dir1='$pkaryawanid' AND a.jabatanid IN ('05', '36') ";
    }elseif ($papproveby=="apvmgrchc") {
        
    }elseif ($papproveby=="apvatasanho") {//, 
        
    }else{
        
    }
    
    if ($ppilihsts=="REJECT") {
        $query .=" AND IFNULL(a.stsnonaktif,'')='Y' ";
    }else{
        
        $query .=" AND IFNULL(a.stsnonaktif,'')<>'Y' ";
        if ($ppilihsts=="ALLDATA") {

        }else{
            if ($ppilihsts=="APPROVE") {
                if ($papproveby=="apvdm") {
                    
                }elseif ($papproveby=="apvsm") {
                    
                }elseif ($papproveby=="apvgsm") {
                    
                }elseif ($papproveby=="apvcoo") {
                    $query .= " AND (IFNULL(a.tgl_dir1,'')='' OR IFNULL(a.tgl_dir1,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
                }elseif ($papproveby=="apvmgrchc") {
                    
                }elseif ($papproveby=="apvatasanho") {
                    
                }else{
                    
                }
            }elseif ($ppilihsts=="UNAPPROVE") {
                if ($papproveby=="apvdm") {
                    
                }elseif ($papproveby=="apvsm") {
                    
                }elseif ($papproveby=="apvgsm") {
                    
                }elseif ($papproveby=="apvcoo") {
                    $query .= " AND (IFNULL(a.tgl_dir1,'')<>'' AND IFNULL(a.tgl_dir1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
                }elseif ($papproveby=="apvmgrchc") {
                    
                }elseif ($papproveby=="apvatasanho") {
                    
                }else{
                    
                }
            }
        }
        
    }
    
    
    //echo $query."<br/>";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN SAPV1 VARCHAR(1), ADD COLUMN SAPV2 VARCHAR(1), ADD COLUMN SAPV3 VARCHAR(1), ADD COLUMN SAPV4 VARCHAR(1), ADD COLUMN SAPVDIR1 VARCHAR(1)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN dbttd.t_po_transaksi_ttd b on a.idpo=b.idpo SET a.SAPVDIR1='Y' WHERE IFNULL(gbr_dir1,'')<>''"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $sql = "select a.idpo, a.idpo_d, a.idpr_po, c.idpr, c.idbarang, c.namabarang, c.spesifikasi1, c.jumlah, c.harga, c.satuan, c.keterangan 
        from dbpurchasing.t_po_transaksi_d as a 
        JOIN $tmp01 as b on a.idpo=b.idpo 
        JOIN dbpurchasing.t_pr_transaksi_po as c on a.idpr_po=c.idpr_po AND b.kdsupp=c.kdsupp";
    $query = "create TEMPORARY table $tmp02 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "ALTER table $tmp01 ADD sudahapprove varchar(1), ADD COLUMN sudahisikirim varchar(1), ADD karyawanid_pr varchar(10), ADD COLUMN nama_karyawan_pr varchar(200)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER table $tmp02 ADD karyawanid_pr varchar(10), ADD COLUMN nama_karyawan_pr varchar(200)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN dbpurchasing.t_pr_transaksi as b on a.idpr=b.idpr SET a.karyawanid_pr=b.karyawanid";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 as a JOIN hrd.karyawan as b on a.karyawanid_pr=b.karyawanid SET a.nama_karyawan_pr=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN $tmp02 as b on a.idpo=b.idpo SET a.karyawanid_pr=b.karyawanid_pr, a.nama_karyawan_pr=b.nama_karyawan_pr";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    if ($ppilihsts=="APPROVE") {
        if ($papproveby=="apvspv") {
            
        }elseif ($papproveby=="apvdm") {
            
        }elseif ($papproveby=="apvsm") {
            
        }elseif ($papproveby=="apvgsm") {
            
        }elseif ($papproveby=="apvcoo") {
            $query = "UPDATE $tmp01 SET sudahapprove='Y' WHERE IFNULL(tgl_dir1,'')<>'' AND IFNULL(tgl_dir1,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00'";
            mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        }elseif ($papproveby=="apvmgrchc") {
            
        }elseif ($papproveby=="apvatasanho") {
            
            
        }
        //
    }
    
    
    $query = "UPDATE $tmp01 as a JOIN "
            . " (select DISTINCT b.idpo from dbpurchasing.t_po_transaksi_terima as a "
            . " JOIN dbpurchasing.t_po_transaksi_d as b on a.idpo_d=b.idpo_d WHERE IFNULL(a.stsnonaktif,'')<>'Y') as b "
            . " on a.idpo=b.idpo SET a.sudahisikirim='Y'";
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
            url:"module/purchasing/pch_apvpoeth/aksi_apvpchpoeth.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
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
            url:"module/purchasing/pch_apvpoeth/aksi_apvpchpoeth.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
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
                    <th width='50px'>ID PO</th>
                    <th width='50px'>Pembuat</th>
                    <th width='50px'>Supplier</th>
                    <th width='50px'>Notes</th>
                    <th width='200px'>Barang</th>
                    <th width='200px'>Yg. Membuat PR</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by IFNULL(sudahapprove,'ZZ'), idpo";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidpo=$row1['idpo'];
                    $pnmkaryawan=$row1['nama_karyawan'];
                    $pnmpembuatpr=$row1['nama_karyawan_pr'];
                    $pidsupp=$row1['kdsupp'];
                    $pnmsupp=$row1['nama_sup'];
                    $pkeperluan=$row1['notes'];
                    $nidbayar=$row1['idbayar'];
                    $nnmbayar=$row1['nama_bayar'];
                    $psudahisivendor=$row1['sudahisikirim'];
                    
					
                    $ptgldir1=$row1['tgl_dir1'];
                    $ptgldir2=$row1['tgl_dir2'];
                    
                    $piddir1=$row1['dir1'];
                    $piddir2=$row1['dir2'];
                    
                    
                    if ($ptgldir1=="0000-00-00" OR $ptgldir1=="0000-00-00 00:00:00") $ptgldir1="";
                    if ($ptgldir2=="0000-00-00" OR $ptgldir2=="0000-00-00 00:00:00") $ptgldir2="";
                    
                    
                    $pketgsmhos="GSM";
                    if ($papproveby=="apvmgrchc") $pketgsmhos="HOS";
                    elseif ($papproveby=="apvatasanho") $pketgsmhos="Atasan";
                    
                    $npmdl="pchpotransaksi";
                    
                    $pprint="<a title='Detail / Print' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=$npmdl&brid=$pidpo&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$pidpo</a>";
                    
                    $ceklisnya = "<input type='checkbox' value='$pidpo' name='chkbox_br[]' id='chkbox_br[$pidpo]' class='cekbr'>";
                    
                    
                    $pstsapvoleh="";
                    
                    if ($ppilihsts=="UNAPPROVE") {
                        
                    }
                    
                    if ($ppilihsts=="APPROVE") {
                        if ($nnmbayar=="HO" OR $nnmbayar=="OTC" OR $nnmbayar=="CHC") {
                            
                        }else{
                        
                        }
                        if (!empty($pstsapvoleh)) {
                            $pstsapvoleh="<span style='color:red;'>$pstsapvoleh</span>";
                        }
                        
                    }elseif ($ppilihsts=="UNAPPROVE") {
                        
                        if ($nnmbayar=="HO" OR $nnmbayar=="OTC" OR $nnmbayar=="CHC") {
                            
                        }else{
                        
                        }
                        
                    }
                    
                    
                    if ($ppilihsts=="REJECT") {
                        $ceklisnya="";
                        $pprint="";
                        $pstsapvoleh="";
                    }
                    
                    
                    $cnmbrg=""; $cnmbrg1=""; $cnmbrg2="";
                    $query = "select idpo, idpo_d, idbarang, namabarang, spesifikasi1, keterangan, jumlah, satuan, harga from $tmp02 WHERE idpo='$pidpo' order by namabarang";
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
                    echo "<td nowrap>$pnmsupp</td>";
                    echo "<td nowrap>$pkeperluan</td>";
                    echo "<td >$cnmbrg</td>";
                    echo "<td nowrap>$pnmpembuatpr</td>";
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
            include "ttd_aprovepchpo.php";
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