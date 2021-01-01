<?PHP
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    include "../../config/koneksimysqli.php";
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpotldpldisc01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmpotldpldisc02_".$puserid."_$now ";
    
    
    //$pidcab=$_POST['ucabid'];
    
    //$_SESSION['DISCDPLCBOTL']=$pidcab;
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupidcard=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    
    
    
    
    $sql = "SELECT a.nourut, a.igroup, a.tglinput, a.divisi, a.tahun, a.semester, a.nodpl, a.iprodid, b.nama as nama_produk, 
        a.beli_min, a.beli_max, a.discount, a.keterangan, a.userid, 
        a.sysnow as sysnow ";
    $sql.=" FROM dbdiscount.t_dpl as a ";
    $sql.=" JOIN MKT.iproduk as b on a.iprodid=b.iprodid ";
    $sql.=" WHERE 1=1 ";
    
    $query = "create TEMPORARY table $tmp01 ($sql)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
?>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=input&idmenu=$pidmenu"; ?>' 
      id='demo_data2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
    <div class='x_content'>
        <table id='datatabledcds' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='10px'>No</th>
                    <th width='10px'></th>
                    <th width='10px'>No. DPL</th>
                    <th width='50px'>ID Produk</th>
                    <th width='50px'>Nama Produk</th>
                    <th width='10px'>Pembelian Minimal</th>
                    <th width='10px'>Pembelian Maksimal</th>
                    <th width='10px'>Discount</th>
                    <th width='30px'>Keterangan</th>
                </tr> 
            </thead>
            <tbody>
                <?PHP
                $no=1;
                $query = "select * from $tmp01 order by nama_produk, igroup, nourut";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $pidurut = $row["nourut"];
                    $pidprod = $row["iprodid"];
                    $pnmprod = $row["nama_produk"];
                    $pketerangan = $row["keterangan"];

                    $pigroup= $row["igroup"];
                    $pnodpl= $row["nodpl"];
                    $pbelimin= $row["beli_min"];
                    $pbelimax= $row["beli_max"];
                    $pdiscount= $row["discount"];
                    
                    $puserid= $row["userid"];

                    $pedit = "<a class='btn btn-success btn-xs' href='?module=$pmodule&act=editdata&idmenu=$pidmenu&nmun=$pidmenu&id=$pidurut'>Edit</a>";
                    
                    
                    if ($fkaryawan<>$puserid AND $fgroupidcard<>"1") {
                        //$pedit="";
                    }
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap>$pedit</td>";
                    echo "<td nowrap>$pnodpl</td>";
                    echo "<td nowrap>$pidprod</td>";
                    echo "<td nowrap>$pnmprod</td>";
                    echo "<td nowrap align='right'>$pbelimin</td>";
                    echo "<td nowrap align='right'>$pbelimax</td>";
                    echo "<td nowrap align='right'>$pdiscount</td>";
                    echo "<td >$pketerangan</td>";
                    echo "</tr>";
                    
                    $no++;
                    
                }
                ?>
            </tbody>
        </table>
    </div>
</form>

<style>
    .divnone {
        display: none;
    }
    #datatabledcds th {
        font-size: 12px;
    }
    #datatabledcds td { 
        font-size: 11px;
    }
</style>

<script>
    $(document).ready(function() {
        var dataTable = $('#datatabledcds').DataTable( {
            "stateSave": true,
            fixedHeader: true,
            "ordering": false,
            "processing": true,
            "order": [[ 0, "asc" ], [ 1, "desc" ], [ 2, "desc" ], [ 3, "desc" ]],
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": 10,
            "columnDefs": [
                { "visible": false },
                { "orderable": false, "targets": 0 },
                { "orderable": false, "targets": 1 },
                { "orderable": true, "targets": 2 },
                { "orderable": true, "targets": 4 },
                { className: "text-right", "targets": [5,6,7] },//right
                { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5, 7] }//nowrap

            ],
            "language": {
                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
            }
        } );
        $('div.dataTables_filter input', dataTable.table().container()).focus();
    } );
    
</script>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    
    mysqli_close($cnmy);
?>