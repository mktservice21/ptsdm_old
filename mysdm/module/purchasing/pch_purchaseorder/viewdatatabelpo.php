<?PHP
    date_default_timezone_set('Asia/Jakarta'); 
    session_start(); 
    
    //ini_set('display_errors', '0');
    ini_set("memory_limit","10G");
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
    
    $_SESSION['PCHSESITGLPO01']=$_POST['uperiode1'];
    $_SESSION['PCHSESITGLPO02']=$_POST['uperiode2'];
    
    
    $psescardidid=$_SESSION['IDCARD'];
    $pgroupid=$_SESSION['GROUP'];
    
    
    include "../../../config/koneksimysqli.php";
    
    
    $date1=$_POST['uperiode1'];
    $date2=$_POST['uperiode2'];
    $tgl1= date("Y-m-01", strtotime($date1));
    $tgl2= date("Y-m-t", strtotime($date2));
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPPCHPRTMPL01_".$puserid."_$now ";
    
    $query = "select d.idtipe, g.nama_tipe, d.pilihpo, 
        a.idpo, a.tglinput, a.tanggal, a.kdsupp, a.notes, a.userid, f.nama as nama_user, 
        a.idbayar, a.tglkirim, a.note_kirim, a.ppn as ppn_h, a.ppnrp as ppnrp_h, 
        a.disc as disc_h, a.discrp as discrp_h, a.pembulatan as pembulatan, a.totalrp as jumlahrp,
        c.idpr, b.idpo_d, b.idpr_po,
        d.karyawanid, e.nama as nama_karyawan,
        c.idbarang, c.namabarang, c.idbarang_d, c.spesifikasi1, 
        c.spesifikasi2, c.uraian, c.keterangan,
        c.jumlah, c.satuan, c.harga, c.ppn, c.ppnrp, c.disc, c.discrp, c.pembulatan as pembulatanpr, c.totalrp
        from dbpurchasing.t_po_transaksi as a 
        join dbpurchasing.t_po_transaksi_d as b on a.idpo=b.idpo 
        JOIN dbpurchasing.t_pr_transaksi_po as c on b.idpr_po=c.idpr_po 
        join dbpurchasing.t_pr_transaksi as d on c.idpr=d.idpr
        left join hrd.karyawan as e on d.karyawanid=e.karyawanId
        left join hrd.karyawan as f on a.userid=f.karyawanId 
        LEFT JOIN dbpurchasing.t_pr_tipe as g on d.idtipe=g.idtipe WHERE 1=1 ";//AND IFNULL(d.pilihpo,'') IN ('Y') 
    $query .=" AND IFNULL(a.stsnonaktif,'')<>'Y' AND a.tanggal BETWEEN '$tgl1' AND '$tgl2' ";
    $query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }

?>


<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    
    <div class='x_content'>
        <table id='datatablepchord' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='5px'>No</th>
                    <th width='50px' class='divnone'></th>
                    <th width='50px'></th>
                    <th width='20px'>Tipe</th>
                    <th width='20px'>ID</th>
                    <th width='30px'>Tanggal</th>
                    <th width='30px'>Yg Mengajukan</th>
                    <th width='30px'>Nama Barang</th>
                    <th width='50px'>Spesifikasi</th>
                    <th width='50px'>Keterangan</th>
                    <th width='50px'>Jumlah</th>
                    <th width='50px'>Satuan</th>
                    <th width='50px'>Harga</th>
                    <th width='50px'>PPN</th>
                    <th width='50px'>Disc.</th>
                    <th width='50px'>Total</th>
                    <th width='50px'>User Input</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select distinct idpo from $tmp01 order by idpo DESC";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidpo=$row['idpo'];
                    $pbelumlewat=false;
                    
                    $query = "select * from $tmp01 WHERE idpo='$pidpo' order by idpo DESC";
                    $tampil1= mysqli_query($cnmy, $query);
                    while ($row1= mysqli_fetch_array($tampil1)) {
                        $ptgl=$row1['tanggal'];
                        $pnmtipe=$row1['nama_tipe'];
                        $pkryid=$row1['karyawanid'];
                        $pkrynm=$row1['nama_karyawan'];
                        $pnmbarang=$row1['namabarang'];
                        $pspesifikasi=$row1['spesifikasi1'];
                        $pketerangan=$row1['keterangan'];
                        $pnotes=$row1['notes'];
                        $puserinput=$row1['nama_user'];
                        $psatuan=$row1['satuan'];
                        
                        $pjml=$row1['jumlah'];
                        $pharga=$row1['harga'];
                        $pppn=$row1['ppn'];
                        $pdisc=$row1['disc'];
                        $ptotalrp=$row1['totalrp'];
                        
                        $ptgl= date("d/m/Y", strtotime($ptgl));
                        $pjml=number_format($pjml,0,",",",");
                        $pharga=number_format($pharga,0,",",",");
                        $ptotalrp=number_format($ptotalrp,0,",",",");
                        
                        $pppn=ROUND($pppn,2);
                        $pdisc=ROUND($pdisc,2);
                        
                        $pedit="<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidpo'>Edit</a>";
                        $phapus="<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesData('hapus', '$pidpo')\">";
                        
                        $print="<a title='Print / Cetak' href='#' class='btn btn-info btn-xs' data-toggle='modal' "
                            . "onClick=\"window.open('eksekusi3.php?module=$pmodule&brid=$pidpo&iprint=print',"
                            . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                            . "Print</a>";
                        
                        //$print="<a title='Print / Cetak' href='eksekusi3.php?module=$pmodule&brid=$pidpo&iprint=print' class='btn btn-info btn-xs' data-toggle='modal' target='_blank'>Print</a>";
    
    
                        $ppilihan="$pedit $phapus $print";
                        
                        echo "<tr>";
                        if ($pbelumlewat==false) {
                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap class='divnone'>$pidpo $pnmtipe $pkrynm $puserinput $ptgl </td>";
                            echo "<td nowrap>$ppilihan</td>";
                            echo "<td nowrap>$pnmtipe</td>";
                            echo "<td nowrap>$pidpo</td>";
                            echo "<td nowrap>$ptgl</td>";
                            echo "<td nowrap>$pkrynm</td>";
                        }else{
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap class='divnone'>$pidpo $pnmtipe $pkrynm $puserinput $ptgl </td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                            echo "<td nowrap>&nbsp;</td>";
                        }
                        echo "<td nowrap>$pnmbarang</td>";
                        echo "<td >$pspesifikasi</td>";
                        echo "<td >$pketerangan</td>";
                        echo "<td nowrap align='right'>$pjml</td>";
                        echo "<td nowrap>$psatuan</td>";
                        echo "<td nowrap align='right'>$pharga</td>";
                        echo "<td nowrap align='right'>$pppn</td>";
                        echo "<td nowrap align='right'>$pdisc</td>";
                        echo "<td nowrap align='right'>$ptotalrp</td>";
                        
                        
                        if ($pbelumlewat==false) {
                            echo "<td >$puserinput</td>";
                        }else{
                            echo "<td >&nbsp;</td>";
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
        var dataTable = $('#datatablepchord').DataTable( {
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
                { className: "text-right", "targets": [10,12,13,14,15] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 10,11,12,13] }//nowrap

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
                document.getElementById("d-form2").action = "module/purchasing/pch_purchaseorder/aksi_purchaseorder.php?module="+module+"&idmenu="+idmenu+"&act="+ket+"&kethapus="+"&ket="+ket+"&id="+noid;
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
    #datatablepchord th {
        font-size: 13px;
    }
    #datatablepchord td { 
        font-size: 11px;
    }
</style>


<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    
    mysqli_close($cnmy);
?>