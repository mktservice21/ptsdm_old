<?PHP
    session_start();
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    include "../../../config/koneksimysqli.php";
    
    
    
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
                <h4 class='modal-title'>Data Pemakai</h4>
            </div>

            <div class='modal-body'>
                <table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>
                    <thead>
                        <tr>
                            <th width='10px'>No</th>
                            <th width='40px'>Nopol</th>
                            <th width='80px'>Karyawan</th>
                            <th width='80px'>Tgl. Awal</th>
                            <th width='80px'>Tgl. Akhir</th>
                        </tr>
                    </thead>
                    <tbody class='gridview-error'>
                        <?PHP
                        $pdatanopol=$_POST['unopol'];
                        
                        $no=1;
                        $query = "select a.nopol, a.karyawanid, b.nama nama_karyawan, a.tglawal, a.tglakhir, a.icabangid, c.nama nama_cabang 
                                from dbmaster.t_kendaraan_pemakai a 
                                LEFT JOIN hrd.karyawan b on a.karyawanid=b.karyawanid 
                                LEFT JOIN MKT.icabang c on a.icabangid=c.icabangid WHERE a.nopol='$pdatanopol' "
                                . " order by a.tglawal";
                        $tampil= mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            
                            $pnopol=$row['nopol'];
                            $pkaryawanid=$row['karyawanid'];
                            $pnmkaryawan=$row['nama_karyawan'];
                            $ptglawal=$row['tglawal'];
                            $ptglakhir=$row['tglakhir'];
                            $pnmcabang=$row['nama_cabang'];

                            echo "<tr scope='row'>";
                            echo "<td>$no</td>";
                            echo "<td>$pnopol</td>";
                            echo "<td>$pnmkaryawan</td>";
                            echo "<td>$ptglawal</td>";
                            echo "<td>$ptglakhir</td>";
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
hapusdata:
    mysqli_close($cnmy);
?>