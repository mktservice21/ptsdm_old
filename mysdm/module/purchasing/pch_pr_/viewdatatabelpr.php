<?PHP
    date_default_timezone_set('Asia/Jakarta'); 
    session_start(); 
    
    //ini_set('display_errors', '0');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $_SESSION['PCHSESITGL01']=$_POST['uperiode1'];
    $_SESSION['PCHSESITGL02']=$_POST['uperiode2'];
    
    
    $psescardidid=$_SESSION['IDCARD'];
    $pgroupid=$_SESSION['GROUP'];
    
    
    include "../../../config/koneksimysqli.php";
    include "../../../config/fungsi_ubahget_id.php";
    
    
    $pkaryawanid=$_POST['ukryid'];
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $tgl2= date("Y-m-t", strtotime($date2));
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPPCHPRTMPL01_".$puserid."_$now ";
    $tmp02 =" dbtemp.TMPPCHPRTMPL02_".$puserid."_$now ";
    
    $query = "select b.pengajuan, b.idtipe, g.nama_tipe, a.idpr, 
        b.tglinput, b.tanggal, b.karyawanid, c.nama as nama_karyawan, 
        b.jabatanid, b.divisi, b.icabangid, d.nama as nama_cabang, b.areaid, e.nama nama_area, 
        b.aktivitas, b.userid, f.nama as nama_user,  
        a.idpr_d, a.idbarang, a.namabarang, a.idbarang_d, a.spesifikasi1, 
        a.spesifikasi2, a.uraian, a.keterangan, a.jumlah as jml, a.harga as rp_pr, a.satuan,
        b.iddep, h.nama_dep, 
        b.atasan1, b.tgl_atasan1, 
        b.atasan2, b.tgl_atasan2, 
        b.atasan3, b.tgl_atasan3,
        b.atasan4, b.tgl_atasan4,
        b.atasan5, b.tgl_atasan5, 
        b.validate1, b.tgl_validate1,
        b.validate2, b.tgl_validate2 
        from dbpurchasing.t_pr_transaksi_d as a JOIN dbpurchasing.t_pr_transaksi as b on a.idpr=b.idpr 
        JOIN hrd.karyawan c on b.karyawanid=c.karyawanid
        LEFT JOIN MKT.icabang as d on b.icabangid=d.iCabangId
        LEFT JOIN MKT.iarea as e on b.icabangid=e.iCabangId and b.areaid=e.areaid 
        LEFT JOIN hrd.karyawan as f on b.userid=f.karyawanId 
        LEFT JOIN dbpurchasing.t_pr_tipe as g on b.idtipe=g.idtipe 
        LEFT JOIN dbmaster.t_department as h on b.iddep=h.iddep WHERE 1=1 AND IFNULL(pilihpo,'') IN ('Y') ";
    $query .=" AND IFNULL(b.stsnonaktif,'')<>'Y' AND b.tanggal BETWEEN '$tgl1' AND '$tgl2' ";
    if (!empty($pkaryawanid)) {
        $query .=" AND b.karyawanid='$pkaryawanid' ";
    }
    $query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

    $query = "ALTER TABLE $tmp01 ADD COLUMN ssudah VARCHAR(1)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "UPDATE $tmp01 as a JOIN (select distinct idpr from dbpurchasing.t_pr_transaksi_po WHERE IFNULL(aktif,'')='Y') as b "
            . " on IFNULL(a.idpr,'')=IFNULL(b.idpr,'') SET a.ssudah='Y'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    
    $query = "select distinct a.idpr, a.idpr_d from dbimages.img_pr as a "
            . " JOIN (select distinct idpr from $tmp01) as b on a.idpr=b.idpr";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp01 ADD COLUMN adabukti VARCHAR(1)"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 a JOIN $tmp02 b on a.idpr=b.idpr AND a.idpr_d=b.idpr_d SET a.adabukti='Y'"; 
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatablepchreq' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='5px'>No</th>
                    <th width='50px' class='divnone'></th>
                    <th width='50px'></th>
                    <th width='20px'>Tipe</th>
                    <th width='20px'>ID</th>
                    <th width='30px'>Tanggal</th>
                    <th width='30px'>Yg Mengajukan</th>
                    <th width='30px'>Attach Images</th>
                    <th width='30px'>Nama Barang</th>
                    <th width='50px'>Spesifikasi</th>
                    <th width='50px'>Keterangan</th>
                    <th width='50px'>Jumlah</th>
                    <th width='20px'>Satuan</th>
                    <th width='20px'>Harga</th>
                    <th width='50px'>Departemen</th>
                    <th width='50px'>Status</th>
                    <th width='50px'>User Input</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select distinct idpr from $tmp01 order by idpr DESC";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidpr=$row['idpr'];
                    $pidnoget=encodeString($pidpr);
                    $pbelumlewat=false;
                    
                    $query = "select * from $tmp01 WHERE idpr='$pidpr' order by idpr DESC";
                    $tampil1= mysqli_query($cnmy, $query);
                    while ($row1= mysqli_fetch_array($tampil1)) {
                        $pd_idpr=$row1['idpr_d'];
                        $pd_idpr_d=encodeString($pd_idpr);
                        
                        $ptgl=$row1['tanggal'];
                        $pnmtipe=$row1['nama_tipe'];
                        $pkryid=$row1['karyawanid'];
                        $pkrynm=$row1['nama_karyawan'];
                        $pnmbarang=$row1['namabarang'];
                        $pspesifikasi=$row1['spesifikasi1'];
                        $pketerangan=$row1['keterangan'];
                        $pnotes=$row1['aktivitas'];
                        $nuseridinput=$row1['userid'];
                        $puserinput=$row1['nama_user'];
                        $psatuan=$row1['satuan'];
                        $pnmdept=$row1['nama_dep'];
                        $pdivid=$row1['divisi'];
                        $psudah=$row1['ssudah'];
                        $pgbrbukti = $row1["adabukti"];
                        
                        $pjml=$row1['jml'];
                        $pharga=$row1['rp_pr'];
                        
                        
                        $npengajuan=$row1['pengajuan'];
                        $njbt=$row1['jabatanid'];
                        $nats1=$row1['atasan1'];
                        $ntglats1=$row1['tgl_atasan1'];
                        $nats2=$row1['atasan2'];
                        $ntglats2=$row1['tgl_atasan2'];
                        $nats3=$row1['atasan3'];
                        $ntglats3=$row1['tgl_atasan3'];
                        $nats4=$row1['atasan4'];
                        $ntglats4=$row1['tgl_atasan4'];
                        $nats5=$row1['atasan5'];
                        $ntglats5=$row1['tgl_atasan5'];
                        $puserval1=$row1['validate1'];
                        $ptglval1=$row1['tgl_validate1'];
                        $puserval2=$row1['validate2'];
                        $ptglval2=$row1['tgl_validate2'];

                        if ($ntglats1=="0000-00-00 00:00:00") $ntglats1="";
                        if ($ntglats2=="0000-00-00 00:00:00") $ntglats2="";
                        if ($ntglats3=="0000-00-00 00:00:00") $ntglats3="";
                        if ($ntglats4=="0000-00-00 00:00:00") $ntglats4="";
                        if ($ntglats5=="0000-00-00 00:00:00") $ntglats5="";
                        if ($ptglval1=="0000-00-00 00:00:00") $ptglval1="";
                        if ($ptglval2=="0000-00-00 00:00:00") $ptglval2="";
                
                        
                        $nsudahapprove=false;
                        
                        if ($npengajuan=="HO" OR $npengajuan=="OTC" OR $npengajuan=="CHC") {
                            if ( !empty($ntglats4) ) $nsudahapprove=true;
                        }else{
                            if ( ($njbt=="15" OR $njbt=="38") AND !empty($nats1) AND !empty($ntglats1)) $nsudahapprove=true;
                            if ( ($njbt=="15" OR $njbt=="38" OR $njbt=="10" OR $njbt=="18") AND !empty($nats2) AND !empty($ntglats2)) $nsudahapprove=true;
                            if ( ($njbt=="15" OR $njbt=="38" OR $njbt=="10" OR $njbt=="18" OR $njbt=="08") AND !empty($nats3) AND !empty($ntglats3)) $nsudahapprove=true;
                            if ( ($njbt=="15" OR $njbt=="38" OR $njbt=="10" OR $njbt=="18" OR $njbt=="08" OR $njbt=="20") AND !empty($nats4) AND !empty($ntglats4)) $nsudahapprove=true;
                            if ( ($njbt=="15" OR $njbt=="38" OR $njbt=="10" OR $njbt=="18" OR $njbt=="08" OR $njbt=="05") AND !empty($nats5) AND !empty($ntglats5)) $nsudahapprove=true;
                        }
                        $pstatuspch="";
                        if (!empty($ptglval2)) $pstatuspch="Sudah Purchasing";
                
                        
                        $pnmdivisi=$pdivid;
                        if ($pdivid=="CAN") $pnmdivisi="CANARY/ETHICAL";
                        elseif ($pdivid=="PEACO") $pnmdivisi="PEACOCK";
                        elseif ($pdivid=="PIGEO") $pnmdivisi="PIGEON";
                        elseif ($pdivid=="OTC") $pnmdivisi="CHC";
                        
                        $ptgl= date("d/m/Y", strtotime($ptgl));
                        $pjml=number_format($pjml,0,",",",");
                        $pharga=number_format($pharga,0,",",",");
                        
                        $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidnoget'>Edit</a>";
                        $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$pidpr')\">";
                        
                        $print="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=$pmodule&brid=$pidpr&iprint=print',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Print</a>";
    
                        //$print="<a title='Print / Cetak' href='eksekusi3.php?module=$pmodule&brid=$pidpr&iprint=print' class='btn btn-info btn-xs' data-toggle='modal' target='_blank'>Print</a>";
                        
                        $warna="btn btn-success btn-xs";
                        if (!empty($pgbrbukti)) $warna="btn btn-danger btn-xs";
                        $upload="<a class='$warna' href='?module=$pmodule&act=uploaddok&idmenu=$pidmenu&nmun=$pidmenu&id=$pidnoget&idd=$pd_idpr_d'>Upload</a>";
                    
                        if ($pgroupid=="1" OR $pgroupid=="24") {
                        }else{
                            if ($nuseridinput<>$psescardidid) {
                                $pedit="";
                                $phapus="";
                            }
                        }
                        
                        if ($nsudahapprove==true) {
                            $pedit="";
                            $phapus="";
                        }
                        
                        $ppilihan="$pedit $phapus $print";
                        
                
                        if ($psudah=="Y") {
                            $ppilihan="$print";
                        }
                        
                        
                        echo "<tr>";
                        if ($pbelumlewat==false) {
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap class='divnone'>$pidpr $pnmtipe $pkrynm $puserinput $ptgl </td>";
                            echo "<td nowrap>$ppilihan</td>";
                            echo "<td nowrap>$pnmtipe</td>";
                            echo "<td nowrap>$pidpr</td>";
                            echo "<td nowrap>$ptgl</td>";
                            echo "<td nowrap>$pkrynm</td>";
                        }else{
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap class='divnone'>$pidpr $pnmtipe $pkrynm $puserinput $ptgl </td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                        }
                        echo "<td nowrap>$upload</td>";
                        echo "<td nowrap>$pnmbarang</td>";
                        echo "<td >$pspesifikasi</td>";
                        echo "<td >$pketerangan</td>";
                        echo "<td nowrap align='right'>$pjml</td>";
                        echo "<td nowrap>$psatuan</td>";
                        echo "<td nowrap align='right'>$pharga</td>";
                        echo "<td nowrap>$pnmdept ($pnmdivisi)</td>";
                        
                        echo "<td nowrap>$pstatuspch</td>";
                        
                        if ($pbelumlewat==false) {
                            echo "<td nowrap>$puserinput</td>";
                        }else{
                            echo "<td nowrap>&nbsp;</td>";
                        }
                        echo "</tr>";
                        
                        $pbelumlewat=true;
                    }
                    
                    $no++;
                }
                ?>
            </tbody>
        </table>

    </div>
    
</form>


    
<script>
    $(document).ready(function() {
        var dataTable = $('#datatablepchreq').DataTable( {
            "stateSave": true,
            fixedHeader: true,
            "ordering": false,
            "processing": true,
            //"order": [[ 0, "asc" ], [ 1, "desc" ], [ 2, "desc" ], [ 3, "desc" ]],
            "lengthMenu": [[10, 50, 100, 10000000], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { "orderable": false, "targets": 2 },
                { "orderable": false, "targets": 3 },
                { "orderable": false, "targets": 4 },
                { "orderable": false, "targets": 5 },
                { className: "text-right", "targets": [10,12] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 10,11,12] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
    
    function ProsesData(ket, noid){

        ok_ = 1;
        if (ok_) {
            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
            if (r==true) {

                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/purchasing/pch_pr/aksi_purchasereq.php?module="+module+"&idmenu="+idmenu+"&act="+ket+"&kethapus="+"&ket="+ket+"&id="+noid;
                document.getElementById("d-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }



    }
</script>



<style>
    .divnone {
        display: none;
    }
    #datatablepchreq th {
        font-size: 13px;
    }
    #datatablepchreq td { 
        font-size: 11px;
    }
</style>


<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    mysqli_close($cnmy);
?>