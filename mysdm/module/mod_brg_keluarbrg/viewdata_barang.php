<?PHP
    session_start();
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    
    include "../../config/koneksimysqli.php";
    
    $pdatainp1=$_POST['udata1'];
    $pdatainp2=$_POST['udata2'];
    $pdatainp3=$_POST['udata3'];
    
    $pidinput=$_POST['uidinput'];
    $pdivisiid=$_POST['udivisi'];
    $pidcabang=$_POST['ucabang'];
    $ptgl=$_POST['utgl'];
    
    $ptglasli = date('Y-m-01', strtotime($ptgl));
    $pbulan = date('Ym', strtotime($ptgl));
    $pbulanlalu = date('Ym', strtotime('-1 month', strtotime($ptglasli)));
    
    $userid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPSKB01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPSKB02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPSKB03_".$userid."_$now ";
    $tmp04 =" dbtemp.TMPSKB04_".$userid."_$now ";
    
    $filterdivisi=" AND b.DIVISIID='$pdivisiid' ";
    if ($pdivisiid=="OTC") {
        //$filterdivisi=" AND d.PILIHAN='OT' ";
    }
    
    
    
    $query ="SELECT
	d.PILIHAN,
	b.IDBARANG,
	b.DIVISIID,
	d.DIVISINM,
	b.IDKATEGORI,
	k.NAMA_KATEGORI,
	b.NAMABARANG,
        b.HARGA, 
        b.KDSUPP,
        e.NAMA_SUP, 
	b.STSNONAKTIF,
	k.STSAKTIF,
        CAST(0 as DECIMAL(20,2)) as jumlah,
        CAST(0 as DECIMAL(20,2)) as stock, 
        CAST(0 as DECIMAL(20,2)) as jmlawal,
        CAST(0 as DECIMAL(20,2)) as jmlkeluar,
        CAST(0 as DECIMAL(20,2)) as jmlterima 
        FROM
	dbmaster.t_barang AS b
        LEFT JOIN dbmaster.t_barang_kategori AS k ON b.IDKATEGORI = k.IDKATEGORI
        LEFT JOIN dbmaster.t_divisi_gimick d on b.DIVISIID=d.DIVISIID 
        LEFT JOIN dbmaster.t_supplier e on b.KDSUPP=e.KDSUPP 
        LEFT JOIN dbmaster.t_barang_tipe AS l ON b.IDTIPE = l.IDTIPE 
        WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND IFNULL(k.STSAKTIF,'')='Y' AND IFNULL(l.STS,'') IN ('G') 
        $filterdivisi";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    //JUMLAH YANG DIINPUT
    if (!empty($pidinput)) {
        $query="SELECT a.IDKELUAR, a.IDBARANG, a.JUMLAH FROM dbmaster.t_barang_keluar_d a "
                . " JOIN dbmaster.t_barang_keluar b on a.IDKELUAR=b.IDKELUAR "
                . " WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND a.IDKELUAR='$pidinput'";
        $query = "create TEMPORARY table $tmp02 ($query)"; 
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp01 a JOIN (SELECT IDBARANG, SUM(JUMLAH) JUMLAH FROM $tmp02 GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jumlah=b.JUMLAH";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "DROP TEMPORARY TABLE $tmp02";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
    }
    
    
    //STOCK AWAL ATAU BULAN LALU
    $query="SELECT * FROM dbmaster.t_barang_opname_d WHERE DATE_FORMAT(BULAN,'%Y%m')='$pbulanlalu'";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //STOCK DARI TABEL KELUAR BARANG
    $query="SELECT a.IDKELUAR, a.IDBARANG, a.JUMLAH FROM dbmaster.t_barang_keluar_d a "
            . " JOIN dbmaster.t_barang_keluar b on a.IDKELUAR=b.IDKELUAR "
            . " WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(b.TANGGAL,'%Y%m')='$pbulan' AND a.IDKELUAR<>'$pidinput'";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //STOCK DARI TABEL TERIMA BARANG
    $query="SELECT a.IDTERIMA, a.IDBARANG, a.JUMLAH FROM dbmaster.t_barang_terima_d a "
            . " JOIN dbmaster.t_barang_terima b on a.IDTERIMA=b.IDTERIMA "
            . " WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND DATE_FORMAT(b.TANGGAL,'%Y%m')='$pbulan' AND "
            . " IFNULL(VALIDATEDATE,'')<>'' AND IFNULL(VALIDATEDATE,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00' AND "
            . " IFNULL(VALIDATEDATE,'0000-00-00')<>'0000-00-00'";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $query = "UPDATE $tmp01 a JOIN (SELECT IDBARANG, SUM(JMLOP) JMLOP FROM $tmp02 GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlawal=b.JMLOP";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a JOIN (SELECT IDBARANG, SUM(JUMLAH) JUMLAH FROM $tmp03 GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlkeluar=b.JUMLAH";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 a JOIN (SELECT IDBARANG, SUM(JUMLAH) JUMLAH FROM $tmp04 GROUP BY 1) b ON a.IDBARANG=b.IDBARANG SET a.jmlterima=b.JUMLAH";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "UPDATE $tmp01 SET stock=IFNULL(jmlawal,0)+IFNULL(jmlterima,0)-IFNULL(jmlkeluar,0)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    
    
?>
    <!-- Datatables -->
    <script src="../../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../../vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../../vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
    <script src="../../vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
    <script src="../../vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
    <script src="../../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../../vendors/pdfmake/build/vfs_fonts.js"></script>

    <script type="text/javascript" charset="utf-8">
        $(document).ready(function() {
            var table = dataTable = $('#mytable').dataTable({
                fixedHeader: false,
                "ordering": true,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": 10,
            });
        } );
    </script>


    <div class='modal-dialog modal-lg'>
        <!-- Modal content-->
        <div class='modal-content'>
            <div class='modal-header'>
                <button type='button' class='close' data-dismiss='modal'>&times;</button>
                <h4 class='modal-title'>Pilih Data Barang</h4>
            </div>

            <div class='modal-body'>
                <table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>
                    <thead>
                        <tr>
                            <th width='10px'>No</th>
                            <th width='40px'>Kode</th>
                            <th width='80px'>Nama</th>
                            <th width='80px'>Vendor</th>
                            <th width='80px'>Harga</th>
                            <th width='30px'>Stock</th>
                        </tr>
                    </thead>
                    <tbody class='gridview-error'>
                        <?PHP
                        $no=1;
                        $query = "select * from $tmp01 order by NAMA_KATEGORI, NAMABARANG";
                        $tampil= mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            
                            $pidbarang=$row['IDBARANG'];
                            $pnmbarang=$row['NAMABARANG'];

                            $pstock=$row['stock'];
                            $pjml=$row['jumlah'];
                            $pnmsupp=$row['NAMA_SUP'];
                            $phargarp=$row['HARGA'];
                            
                            $phargarp=number_format($phargarp,2,".",",");
                            
                            
                            if (!empty($pnmbarang)) $pnmbarang=strip_tags_content($pnmbarang);
                            if (!empty($pnmbarang)) $pnmbarang = preg_replace("/[\\n\\r]+/", "", $pnmbarang);
							
							
                            echo "<tr scope='row'><td>$no</td>";
                            echo "<td><a data-dismiss='modal' href='#' "
                            . "onClick=\"getDataModalBarang('$pdatainp1', '$pdatainp2', '$pdatainp3', '$pidbarang', '$pnmbarang', '$pstock')\">
                                $pidbarang</a></td>";
                            echo "<td>$pnmbarang</td>";
                            echo "<td>$pnmsupp</td>";
                            echo "<td align='right'>$phargarp</td>";
                            echo "<td align='right'>$pstock</td>";
                            echo "</tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class='modal-footer'>
                <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
            </div>
        </div>
    </div>
    
    
<?PHP

function strip_tags_content($text) {
    return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
}


hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp04");
    mysqli_close($cnmy);
?>