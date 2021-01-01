<?PHP
    session_start();
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    include "../../config/koneksimysqli_ms.php";
    
    $pdatainp1=$_POST['udata1'];
    $pdatainp2=$_POST['udata2'];
    
    $pidcab=$_POST['uidcab'];
    $pidarea=$_POST['uidarea'];
    
    
    $userid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPSKB01_".$userid."_$now ";
    
    
    
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
                <h4 class='modal-title'>Pilih Data Customer</h4>
            </div>

            <div class='modal-body'>
                <table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>
                    <thead>
                        <tr>
                            <th width='10px'>No</th>
                            <th width='40px'>ID</th>
                            <th width='80px'>Nama</th>
                            <th width='30px'>Alamat 1</th>
                            <th width='30px'>Alamat 2</th>
                            <th width='80px'>Kode Pos</th>
                        </tr>
                    </thead>
                    <tbody class='gridview-error'>
                        <?PHP
                        
                        $no=1;
                        $query = "select icustid as icustid, nama as nama, alamat1 as alamat1, alamat2 as alamat2, "
                                . " kodepos as kodepos from sls.icust where icabangid='$pidcab' and areaid='$pidarea' ";
                         $query .=" Order By nama";
                        $tampil= mysqli_query($cnms, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            
                            $pidcust=$row['icustid'];
                            $pnmcust=$row['nama'];
                            $palamat1=$row['alamat1'];
                            $palamat2=$row['alamat2'];
                            $pkdpos=$row['kodepos'];
                            
                            if (!empty($palamat1)) $palamat1=strip_tags_content($palamat1);
                            if (!empty($palamat1)) $palamat1 = preg_replace("/[\\n\\r]+/", "", $palamat1);;
                            
                            if (!empty($palamat2)) $palamat2=strip_tags_content($palamat2);
                            if (!empty($palamat2)) $palamat2 = preg_replace("/[\\n\\r]+/", "", $palamat2);;
                            
                            
                            echo "<tr scope='row'><td>$no</td>";
                            echo "<td><a data-dismiss='modal' href='#' "
                            . "onClick=\"getDataModalCustomer('$pdatainp1', '$pdatainp2', '$pidcust', '$pnmcust')\">
                                $pidcust</a></td>";
                            echo "<td>$pnmcust</td>";
                            echo "<td>$palamat1</td>";
                            echo "<td>$palamat2</td>";
                            echo "<td>$pkdpos</td>";
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
    mysqli_query($cnms, "drop TEMPORARY table $tmp01");
    mysqli_close($cnms);
?>