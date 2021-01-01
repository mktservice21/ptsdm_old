<?PHP
    session_start();
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    include "../../config/koneksimysqli.php";
    
    $pdatainp1=$_POST['udata1'];
    $pdatainp2=$_POST['udata2'];
    $pdatainp3=$_POST['udata3'];
    
    $pidinput=$_POST['uidinput'];
    
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
                <h4 class='modal-title'>Pilih Data Vendor</h4>
            </div>

            <div class='modal-body'>
                <table id='mytable' cellpadding='0' cellspacing='0' border='0' class='table table-striped table-bordered' width='100%'>
                    <thead>
                        <tr>
                            <th width='10px'>No</th>
                            <th width='40px'>Kode</th>
                            <th width='50px'>Nama</th>
                            <th width='50px'>Alamat</th>
                            <th width='30px'>Telp.</th>
                            <th width='30px'>Key Person</th>
                        </tr>
                    </thead>
                    <tbody class='gridview-error'>
                        <?PHP
                        $no=1;
                        $query = "select KDSUPP, NAMA_SUP, ALAMAT, TELP, KEYPERSON from dbmaster.t_supplier WHERE "
                                . " IFNULL(AKTIF,'')<>'N' order by NAMA_SUP, KDSUPP ";
                        $tampil= mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            
                            $pidsup=$row['KDSUPP'];
                            $pnmsup=$row['NAMA_SUP'];
                            $palamat=$row['ALAMAT'];
                            $ptelp=$row['TELP'];
                            $pkeyperson=$row['KEYPERSON'];
                            
                            
                            
                            echo "<tr scope='row'><td>$no</td>";
                            echo "<td><a data-dismiss='modal' href='#' "
                            . "onClick=\"getDataModalVendor('$pdatainp1', '$pdatainp2', '$pdatainp3', '$pidsup', '$pnmsup', '$ptelp')\">
                                $pidsup</a></td>";
                            echo "<td>$pnmsup</td>";
                            echo "<td>$palamat</td>";
                            echo "<td>$ptelp</td>";
                            echo "<td>$pkeyperson</td>";
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
    mysqli_close($cnmy);
?>

    