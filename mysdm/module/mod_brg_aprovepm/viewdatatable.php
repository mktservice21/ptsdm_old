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
    
    
    $_SESSION['BRGPMAPVSTS']=$ppilihsts;
    $_SESSION['BRGPMAPVBLN1']=$mytgl1;
    $_SESSION['BRGPMAPVBLN2']=$mytgl2;
    $_SESSION['BRGPMAPVAPVBY']=$pkaryawanid;
    
    $pbulan1= date("Ym", strtotime($mytgl1));
    $pbulan2= date("Ym", strtotime($mytgl2));
    
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
    
    $filterdivgroup="";
    $query = "select DISTINCT DIVISIID FROM dbmaster.t_divisi_gimick WHERE APVBY='$pkaryawanid'";
    $tampil=mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pdivpilih=$row['DIVISIID'];
        
        if (strpos($filterdivgroup, $pdivpilih)==false) $filterdivgroup .="'".$pdivpilih."',";
    }
    
    if (!empty($filterdivgroup)) {
        $filterdivgroup="(".substr($filterdivgroup, 0, -1).")";
    }
    
    if (empty($filterdivgroup)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    
    //echo "$pjabatanid, $plvlposisi $pbulan1 - $pbulan2 : $filterdivgroup";
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPGMCOP01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPGMCOP02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPGMCOP03_".$userid."_$now ";
    
    
    $query = "select 
        b.PILIHAN,a.IDKELUAR,a.TGLINPUT,a.TANGGAL, a.KARYAWANID,e.nama NAMA_KARYAWAN,a.DIVISIID,
        b.DIVISINM,a.ICABANGID,c.nama NAMA_CABANGETH,a.ICABANGID_O,d.nama NAMA_CABANGOTC,
        a.NOTES,a.USERID,a.STSNONAKTIF,a.SYS_NOW,a.PM_APV,a.PM_TGL,a.APV1,a.APV1_TGL,f.PRINT,
        f.NORESI,f.TGLKIRIM,f.TGLTERIMA, f.IGROUP, a.AREAID, g.nama as NAMAAREAETH, a.AREAID_O, h.nama as NAMAAREAOTC 
        from dbmaster.t_barang_keluar a JOIN dbmaster.t_divisi_gimick b on a.DIVISIID=b.DIVISIID LEFT JOIN 
        mkt.icabang c on a.ICABANGID=c.iCabangId
        LEFT JOIN mkt.icabang_o d on a.ICABANGID_O=d.icabangid_o 
        LEFT JOIN hrd.karyawan e on a.KARYAWANID=e.karyawanId 
        LEFT JOIN dbmaster.t_barang_keluar_kirim f on a.IDKELUAR=f.IDKELUAR 
		LEFT JOIN MKT.iarea as g on a.ICABANGID=g.icabangid AND a.AREAID=g.areaid 
		LEFT JOIN MKT.iarea_o as h on a.ICABANGID_O=h.icabangid_o AND a.AREAID_O=h.areaid_o 
		WHERE a.DIVISIID IN $filterdivgroup ";
    
    if ($ppilihsts=="REJECT") {
        $query .=" AND IFNULL(a.STSNONAKTIF,'')='Y' ";
    }else{
        
        $query .=" AND IFNULL(a.STSNONAKTIF,'')<>'Y' ";
        if ($ppilihsts=="ALLDATA") {

        }else{
            if ($ppilihsts=="APPROVE") {
                $query .=" AND ( IFNULL(a.PM_TGL,'')='' OR IFNULL(a.PM_TGL,'0000-00-00')='0000-00-00' OR IFNULL(a.PM_TGL,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ";
            }elseif ($ppilihsts=="UNAPPROVE") {
                $query .=" AND ( IFNULL(a.PM_TGL,'')<>'' AND IFNULL(a.PM_TGL,'0000-00-00')<>'0000-00-00' AND IFNULL(a.PM_TGL,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
            }
        }
        
    }
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.IDKELUAR, b.IDKATEGORI, c.NAMA_KATEGORI, a.IDBARANG, b.NAMABARANG, a.STOCK, a.JUMLAH from dbmaster.t_barang_keluar_d a 
        JOIN dbmaster.t_barang b on a.IDBARANG=b.IDBARANG LEFT JOIN dbmaster.t_barang_kategori c on b.IDKATEGORI=c.IDKATEGORI WHERE 
        a.IDKELUAR IN (select IFNULL(IDKELUAR,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
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
            url:"module/mod_brg_aprovepm/aksi_aprovepm.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
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
            url:"module/mod_brg_aprovepm/aksi_aprovepm.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
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
                    <th width='50px'>Tanggal</th>
                    <th width='50px'>Grp. Produk</th>
                    <th width='200px'>Yg. Mengajukan</th>
                    <th width='50px'>Cabang</th>
					<th width='50px'>Area</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by IDKELUAR";
                $tampil1= mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)) {
                    $pidkeluar=$row1['IDKELUAR'];
                    $ptgl=$row1['TANGGAL'];
                    $ptglkirim=$row1['TGLKIRIM'];
                    $ppilihanid=$row1['PILIHAN'];
                    $pdivisinm=$row1['DIVISINM'];
                    $pnmkaryawan=$row1['NAMA_KARYAWAN'];
                    $pidgroup=$row1['IGROUP'];
                    $pnmcabang=$row1['NAMA_CABANGETH'];
                    $ptglapvpch=$row1['APV1_TGL'];
                    
					
					$pnamaarea=$row1['NAMAAREAETH'];
                    if ($ppilihanid=="OT") {
						$pnmcabang=$row1['NAMA_CABANGOTC'];
						$pnamaarea=$row1['NAMAAREAOTC'];
					}
                    
                    $ptgl= date("d/m/Y", strtotime($ptgl));
                    
                    if ($ptglkirim=="0000-00-00" OR $ptglkirim=="0000-00-00 00:00:00") $ptglkirim="";
                    if ($ptglapvpch=="0000-00-00" OR $ptglapvpch=="0000-00-00 00:00:00") $ptglapvpch="";
                    
                    $print="<a title='Detail Barang / Print' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                        . "onClick=\"window.open('eksekusi3.php?module=gimickeluarbarang&nid=$pidkeluar&iprint=print',"
                        . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                        . "$pidkeluar</a>";
                    
                    $ceklisnya = "<input type='checkbox' value='$pidkeluar' name='chkbox_br[]' id='chkbox_br[$pidkeluar]' class='cekbr'>";
                    
                    if ($ppilihsts=="UNAPPROVE") {
                        if (!empty($ptglapvpch)) $ceklisnya="";
                        if (!empty($pidgroup)) $ceklisnya="";
                        if (!empty($ptglkirim)) $ceklisnya="";
                    }
                    
					$pidcabpl=$row1['ICABANGID_O'];
					if (empty($pnmcabang)) {
						$pnmcabang=$pidcabpl;
						if ($pnmcabang=="JKT_RETAIL") $pnmcabang="JAKARTA RETAIL";
						if ($pnmcabang=="JKT_MT") $pnmcabang="JAKARTA - MODERN TRADE";
					}
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$ceklisnya</td>";
                    echo "<td nowrap>$print</td>";
                    echo "<td nowrap>$ptgl</td>";
                    echo "<td nowrap>$pdivisinm</td>";
                    echo "<td nowrap>$pnmkaryawan</td>";
                    echo "<td nowrap>$pnmcabang</td>";
					echo "<td nowrap>$pnamaarea</td>";
                    echo "</tr>";
                    
                    /*
                    //detail
                    echo "<tr>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td colspan='5'>";
                    
                        echo "<table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>";
                        echo "<tr>";
                        echo "<td><b>KATEGORI</b></td>";
                        echo "<td><b>NAMA BARANG</b></td>";
                        echo "<td><b>JUMLAH</b></td>";
                        echo "</tr>";
                        
                        echo "<tbody>";
                            $query = "select * from $tmp02 WHERE IDKELUAR='$pidkeluar' order by NAMA_KATEGORI, NAMABARANG";
                            $tampil2= mysqli_query($cnmy, $query);
                            while ($row2= mysqli_fetch_array($tampil2)) {
                                
                                $pnmkategori=$row2['NAMA_KATEGORI'];
                                $pnmbarang=$row2['NAMABARANG'];
                                $pjml=$row2['JUMLAH'];
                                
                                $pjml=number_format($pjml,0);
                                
                                echo "<tr>";
                                echo "<td nowrap>$pnmkategori</td>";
                                echo "<td nowrap>$pnmbarang</td>";
                                echo "<td nowrap align='right'>$pjml</td>";
                                echo "</tr>";
                            }
                            
                        echo "</tbody>";
                        echo "</table>";
                    */
                    
                    echo "</td>";
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
            include "ttd_approvepm.php";
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