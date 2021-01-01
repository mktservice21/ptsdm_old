<?PHP
    session_start();
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    include "../../config/koneksimysqli.php";
    //include "../../config/koneksimysqli_it.php";
    $cnit=$cnmy;
    $pdatainp1=$_POST['udata1'];
    $pdatainp2=$_POST['udata2'];
    
    $filterkaryawan="";
    $nmkaryawan="";
    $pjbtid="";
    
    $pidkaryawan=$_POST['uidkry'];
    if (!empty($pidkaryawan)) {
        $filterkaryawan="'".$pidkaryawan."',";
    }
    
   

    if (!empty($filterkaryawan)) {
        $filterkaryawan="(".substr($filterkaryawan, 0, -1).")";
    }else{
        $filterkaryawan="('')";
    }


    
    $userid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPDOKURSMTR01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPDOKURSMTR02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPDOKURSMTR03_".$userid."_$now ";
    $tmp04 =" dbtemp.TMPDOKURSMTR04_".$userid."_$now ";
    
    
    
    
    $query ="select distinct a.dokterid as dokterid, a.nama as nama, a.alamat1 as alamat1, a.alamat2 as alamat2 "
            . " from hrd.dokter as a JOIN hrd.mr_dokt as b on a.dokterid=b.dokterid WHERE b.karyawanid IN $filterkaryawan";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
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
                <h4 class='modal-title'>Pilih Data</h4>
            </div>

            <div class='modal-body'>
                <table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>
                    <thead>
                        <tr>
                            <th width='10px'>No</th>
                            <th width='40px'>ID</th>
                            <th width='80px'>Nama</th>
                            <th width='80px'>Alamat</th>
                            <th width='80px'>Alamat</th>
                        </tr>
                    </thead>
                    <tbody class='gridview-error'>
                        <?PHP
                        $no=1;
                        $query = "select * from $tmp01 order by nama, dokterid";
                        $tampil= mysqli_query($cnit, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            
                            $piddokt=$row['dokterid'];
                            $pnmdokt=$row['nama'];
                            $palamat1=$row['alamat1'];
                            $palamat2=$row['alamat2'];

                            $pnma_id=$pnmdokt." (".$piddokt.")";
                            
                            if (!empty($pnma_id)) $pnma_id=strip_tags_content($pnma_id);
                            if (!empty($pnma_id)) $pnma_id = preg_replace("/[\\n\\r]+/", "", $pnma_id);
                            
                            
                            echo "<tr scope='row'><td>$no</td>";
                            echo "<td><a data-dismiss='modal' href='#' "
                            . "onClick=\"getDataModalDokter('$pdatainp1', '$pdatainp2', '$piddokt', '$pnma_id')\">
                                $piddokt</a></td>";
                            echo "<td nowrap>$pnmdokt</td>";
                            echo "<td>$palamat1</td>";
                            echo "<td>$palamat2</td>";
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
    //mysqli_close($cnit);
?>