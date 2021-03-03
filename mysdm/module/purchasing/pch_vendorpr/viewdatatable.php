<?php
session_start();


    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    include "../../../config/koneksimysqli.php";
    
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
    
    
    $_SESSION['PCHSSIVSTS']=$ppilihsts;
    $_SESSION['PCHSSIVTGL1']=$mytgl1;
    $_SESSION['PCHSSIVTGL2']=$mytgl2;
    $_SESSION['PCHSSIVPVBY']=$pkaryawanid;
    
    $pbulan1= date("Y-m-01", strtotime($mytgl1));
    $pbulan2= date("Y-m-t", strtotime($mytgl2));
    
    
    if (empty($pkaryawanid)) {
        echo "Anda tidak berhak proses...";
        mysqli_close($cnmy); exit;
    }
    
    $filidtipe="";
    $query = "select distinct idtipe from dbpurchasing.t_pr_wewenang WHERE karyawanid='$pkaryawanid'";
    $tampil= mysqli_query($cnmy, $query);
    while ($row= mysqli_fetch_array($tampil)) {
        $pidntip=$row['idtipe'];
        if (empty($pidntip)) $pidntip=0;
        if ((DOUBLE)$pidntip==0) $pidntip="";
        
        if (!empty($pidntip)) $filidtipe .="'".$pidntip."',";
    }
    
    if (!empty($filidtipe)) {
        $filidtipe="(".substr($filidtipe, 0, -1).")";
    }
    
    //echo "$pkaryawanid : $pjabatanid, $plvlposisi $pbulan1 - $pbulan2"; exit;
    
    $pidgroup=$_SESSION['GROUP'];
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPISIVENPR01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPISIVENPR02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPISIVENPR03_".$userid."_$now ";
    
    
    
    $query = "select b.pengajuan, b.idtipe, g.nama_tipe, a.idpr, 
        b.tglinput, b.tanggal, b.karyawanid, c.nama as nama_karyawan, 
        b.jabatanid, b.divisi, b.icabangid, d.nama as nama_cabang, b.areaid, e.nama nama_area, 
        b.aktivitas, b.userid, f.nama as nama_user,  
        a.idpr_d, a.idbarang, a.namabarang, a.idbarang_d, a.spesifikasi1, 
        a.spesifikasi2, a.uraian, a.keterangan, a.jumlah as jml, a.harga as rp_pr,
        b.atasan1, b.tgl_atasan1, 
        b.atasan2, b.tgl_atasan2, 
        b.atasan3, b.tgl_atasan3,
        b.atasan4, b.tgl_atasan4,
        b.atasan5, b.tgl_atasan5 
        from dbpurchasing.t_pr_transaksi_d as a JOIN dbpurchasing.t_pr_transaksi as b on a.idpr=b.idpr 
        JOIN hrd.karyawan c on b.karyawanid=c.karyawanid
        LEFT JOIN MKT.icabang as d on b.icabangid=d.iCabangId
        LEFT JOIN MKT.iarea as e on b.icabangid=e.iCabangId and b.areaid=e.areaid 
        LEFT JOIN hrd.karyawan as f on b.userid=f.karyawanId 
        LEFT JOIN dbpurchasing.t_pr_tipe as g on b.idtipe=g.idtipe WHERE 1=1 AND IFNULL(pilihpo,'') IN ('Y') ";
    $query .=" AND IFNULL(stsnonaktif,'')<>'Y' AND b.tanggal BETWEEN '$pbulan1' AND '$pbulan2' ";
    if ($ppilihsts=="UNAPPROVE") {
        $query .=" AND a.idpr_d IN (select distinct IFNULL(idpr_d,'') from dbpurchasing.t_pr_transaksi_po WHERE IFNULL(aktif,'')='Y') ";
    }
    if (!empty($filidtipe)) {
        $query .=" AND b.idtipe IN $filidtipe ";
    }
    $query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN ssudah VARCHAR(1), ADD COLUMN ssudahpo VARCHAR(1)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    if ($ppilihsts=="UNAPPROVE") {
        $query = "UPDATE $tmp01 SET ssudah='Y'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    }else{
        $query = "UPDATE $tmp01 as a JOIN (select distinct idpr_d from dbpurchasing.t_pr_transaksi_po WHERE IFNULL(aktif,'')='Y') as b "
                . " on IFNULL(a.idpr_d,'')=IFNULL(b.idpr_d,'') SET a.ssudah='Y'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    }
    
    $query = "UPDATE $tmp01 as a JOIN "
            . " (select cc.idpr, cc.idbarang, cc.idbarang_d, cc.idpr_d from dbpurchasing.t_po_transaksi_d as aa "
            . " JOIN dbpurchasing.t_po_transaksi as bb on aa.idpo=bb.idpo "
            . " JOIN dbpurchasing.t_pr_transaksi_po as cc on aa.idpr_po=cc.idpr_po WHERE IFNULL(bb.stsnonaktif,'')<>'Y') as b "
            . " on IFNULL(a.idpr,'')=IFNULL(b.idpr,'') AND IFNULL(a.idpr_d,'')=IFNULL(b.idpr_d,'') SET a.ssudahpo='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' 
      id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content' style="overflow-x:auto; max-height: 400px;">
        
        <?PHP
        $pchkall = "<input type='checkbox' id='chkbtnbr' name='chkbtnbr' value='deselect' onClick=\"SelAllCheckBox('chkbtnbr', 'chkbox_br[]')\" checked/>";
        ?>
        
        <table id='dttblisivendor' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='20px'>
                        <input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                    </th>
                    <th width='20px'>ID</th>
                    <th width='20px'>Tipe</th>
                    <th width='30px'>Tanggal</th>
                    <th width='30px'>Yg Mengajukan</th>
                    <th width='30px'>Nama Barang</th>
                    <th width='50px'>Spesifikasi</th>
                    <th width='50px'>Keterangan</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Harga</th>
                    <th width='50px'>Status</th>
                    <th width='50px'>User Input</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select distinct idpr from $tmp01 order by idpr asc";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidpr=$row['idpr'];
                    $pbelumlewat=false;
                    
                    $query = "select * from $tmp01 WHERE idpr='$pidpr' order by idpr asc";
                    $tampil1= mysqli_query($cnmy, $query);
                    while ($row1= mysqli_fetch_array($tampil1)) {
                        $pidpr_d=$row1['idpr_d'];
                        $ptgl=$row1['tanggal'];
                        $pnmtipe=$row1['nama_tipe'];
                        $pkryid=$row1['karyawanid'];
                        $pkrynm=$row1['nama_karyawan'];
                        $pnmbarang=$row1['namabarang'];
                        $pspesifikasi=$row1['spesifikasi1'];
                        $pketerangan=$row1['keterangan'];
                        $pnotes=$row1['aktivitas'];
                        $puserinput=$row1['nama_user'];
                        $psudahisi=$row1['ssudah'];
                        $pposudah=$row1['ssudahpo'];
                        
                        $pjml=$row1['jml'];
                        $pharga=$row1['rp_pr'];
                        
                        $ptgl= date("d/m/Y", strtotime($ptgl));
                        $pjml=number_format($pjml,0,",",",");
                        $pharga=number_format($pharga,0,",",",");
                        
                        
                        $pwarnafld1="btn btn-default btn-xs";
                        $pwarnafld2="btn btn-default btn-xs";
                        if ($psudahisi=="Y") {
                            $pwarnafld1="btn btn-warning btn-xs";
                            $pwarnafld2="btn btn-dark btn-xs";
                        }
                        
                        $pisivendor="<a class='$pwarnafld1' href='?module=$pmodule&act=isivendor&idmenu=$pidmenu&nmun=$pidmenu&id=$pidpr&xid=$pidpr_d'>Isi Vendor</a>";
                        $plihatvendor="<input type='button' value='$pidpr' class='$pwarnafld2' onClick=\"TampilkanDataVendor('$pposudah', '$pidpr', '$pidpr_d', '$pnmbarang')\">";
                        
                        
                        $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$pidpr')\">";
                        
                        $print="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=$pmodule&brid=$pidpr&iprint=print',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Print</a>";
                        
                        $ppilihan="$pisivendor";
                        
                        if ($pposudah=="Y") {
                            $ppilihan="SUDAH PO";
                        }
                        
                        echo "<tr>";
                        
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap class='divnone'>$pidpr $pnmtipe $pkrynm $puserinput $ptgl </td>";
                            echo "<td nowrap>$ppilihan</td>";
                            echo "<td nowrap>$plihatvendor</td>";
                            echo "<td nowrap>$pnmtipe</td>";
                            echo "<td nowrap>$ptgl</td>";
                            echo "<td nowrap>$pkrynm</td>";
                        
                        echo "<td nowrap>$pnmbarang</td>";
                        echo "<td >$pspesifikasi</td>";
                        echo "<td >$pketerangan</td>";
                        echo "<td nowrap align='right'>$pjml</td>";
                        echo "<td nowrap align='right'>$pharga</td>";
                        
                        
                            echo "<td >&nbsp;</td>";
                            echo "<td >$puserinput</td>";
                        
                        echo "</tr>";
                        
                        $pbelumlewat=true;
                        $no++;
                    }
                    
                    
                }
                ?>
            </tbody>
        </table>
        
    </div>
    
    <?PHP
    $pidbr="";
    $pidbr_d="";
    ?>
    <div class="clearfix"></div>
    <div class="row">
        <div class='x_content'>
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <div class='x_panel'>
                    
                    <div id="div_isivendor">
                        
                        <div class="page-title">
                            <h3>
                                <?PHP echo "<u>Data Purchase Request</u>"; ?>
                            </h3>
                        </div>
                        <div class="clearfix"></div>

                        <div hidden class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                            <div class='col-md-4'>
                                <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                <input type='text' id='e_id_d' name='e_id_d' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr_d; ?>' Readonly>
                            </div>
                        </div>


                        <table id='dttblisivendor' class='table table-striped table-bordered' width='100%'>
                            <thead>
                                <tr>
                                    <th width='7px'>No</th>
                                    <th width='20px'>
                                        <input type="checkbox" id="chkbtnbr" value="select" 
                                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                                    </th>
                                    <th width='30px'>Vendor</th>
                                    <th width='30px'>Nama Barang</th>
                                    <th width='50px'>Spesifikasi</th>
                                    <th width='50px'>Jumlah</th>
                                    <th width='50px'>Harga</th>
                                    <th width='50px'>Pilih</th>
                                    <th width='50px'>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>     
                        
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    
</form>

<script>
    $(document).ready(function() {
        var iid="<?PHP echo $_SESSION['PCHSSIVIDPR']; ?>";
        var iidd="<?PHP echo $_SESSION['PCHSSIVIDPD']; ?>";
        var inmbr="<?PHP echo $_SESSION['PCHSSIVNMBG']; ?>";
        
        if (iid!="" && iidd!="" && inmbr!="") {
            TampilkanDataVendor(iid, iidd, inmbr);
        }
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
    function TampilkanDataVendor(isudahpo, iid, iidd, inmbr) {
        $.ajax({
            type:"post",
            url:"module/purchasing/pch_vendorpr/tampildataisivendor.php?module=tampildataisivendor",
            data:"usudahpo="+isudahpo+"&uid="+iid+"&uidd="+iidd+"&unmbr="+inmbr,
            success:function(data){
                $("#div_isivendor").html(data);
                window.scrollTo(0,document.querySelector("#div_isivendor").scrollHeight);
            }
        });
    }
</script>

<style>
    .divnone {
        display: none;
    }
    #dttblisivendor th {
        font-size: 13px;
    }
    #dttblisivendor td { 
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