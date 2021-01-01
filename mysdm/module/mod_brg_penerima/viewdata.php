<?PHP
    session_start();
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    include "../../config/koneksimysqli.php";
    
    
    $pdatainp1=$_POST['udata1'];
    $pdatainp2=$_POST['udata2'];
    
    $puntuk=$_POST['uuntuk'];
    
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
                <h4 class='modal-title'>Pilih Data Cabang</h4>
            </div>

            <div class='modal-body'>
                <table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>
                    <thead>
                        <tr>
                            <th width='10px'>No</th>
                            <th width='40px'>ID</th>
                            <th width='80px'>Nama</th>
                        </tr>
                    </thead>
                    <tbody class='gridview-error'>
                        <?PHP
                        $no=1;
                        if ($puntuk=="OTC" OR $puntuk=="CHC" OR $puntuk=="OT")
                            $query = "select icabangid_o as icabangid, nama as nama from dbmaster.v_icabang_o WHERE aktif='Y' order by nama";
                        else
                            $query = "select iCabangId as icabangid, nama as nama from MKT.icabang WHERE aktif='Y' order by nama";
                        
                        $tampil= mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            
                            $pidcabang=$row['icabangid'];
                            $pnmcabang=$row['nama'];

                            
                            echo "<tr scope='row'><td>$no</td>";
                            echo "<td><a data-dismiss='modal' href='#' "
                            . "onClick=\"getDataModalCabang('$pdatainp1', '$pdatainp2', '$pidcabang', '$pnmcabang')\">
                                $pidcabang</a></td>";
                            echo "<td>$pnmcabang</td>";
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
    
    
    mysqli_close($cnmy);
?>