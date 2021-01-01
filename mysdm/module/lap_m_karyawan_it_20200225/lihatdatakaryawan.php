<?PHP
session_start();
    $printdate= date("d_m_Y");
    $jamnow=date("H_i_s");
    
    if ($_GET['ket']=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Data Karyawan $printdate $jamnow.xls");
    }
?>
<script>

$(document).ready(function() {

                var groupColumn = 1;
                var groupColumn2 = 2;
                var table = $('#datatable').DataTable({
                    fixedHeader: true,
                    "ordering": false,
                    "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
                    "displayLength": -1,
                    "columnDefs": [
                        { "contentPadding": "1" },
                        { "visible": false },
                        { className: "text-right", "targets": [0] },//right
                        { className: "text-nowrap", "targets": [0, 1, 2, 3,4,5,6,7,8] }//nowrap

                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'excel', 'print'
                    ],
                    bFilter: true, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                    "bPaginate": false
                } );

                $('#enable').on( 'click', function () {
                    table.fixedHeader.enable();
                } );

                $('#disable').on( 'click', function () {
                    table.fixedHeader.disable();
                } );
 
                // Order by the grouping
                $('#datatable tbody').on( 'click', 'tr.group', function () {
                    var currentOrder = table.order()[0];
                    if ( currentOrder[0] === groupColumn && currentOrder[1] === 'asc' ) {
                        table.order( [ groupColumn, 'desc' ] ).draw();
                    }
                    else {
                        table.order( [ groupColumn, 'asc' ] ).draw();
                    }
                } );

} );

</script>

<style>
    .divnone {
        display: none;
    }
    #datatable th {
        font-size: 12px;
    }
    #datatable td { 
        font-size: 11px;
    }
</style>


<?PHP
    include "config/koneksimysqli_it.php";
    echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

        //panel
        echo "<div class='x_panel'>";
            //isi content
            echo "<div class='x_content'>";
                
            
            
    $printdate= date("d/m/Y");
    echo "<table width='100%' align='center' border='0' class='table table-striped table-bordered'>";
        echo "<tr>";
            echo "<td valign='top'>";
                echo "<table border='0' width='100%'>";
                echo "<tr><td><small style='color:blue;'>$_SESSION[NAMAPT]</small></td></tr>";
                echo "</table>";
            echo "</td>";
            echo "<td valign='top'>";
                echo "<table align='center' border='0' width='70%'>";
                echo "<tr><td align='left'><h1>Data Karyawan</h1></td></tr>";
                echo "<tr><td align='left'><b><br/></td></tr>";
                echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                echo "</table>";
            echo "</td>";
        echo "</tr>";
    echo "</table>";
    
    
                echo "<table id='datatable' class='display  table table-striped table-bordered' style='width:100%' border='1px'>";
                echo "<thead><tr><th width='10px'>No</th><th>ID</th><th>Karyawan</th>"
                        . "<th width='100px'>Tempat</th><th width='100px'>Tgl. Lahir</th>"
                        . "<th>Jabatan</th><th width='100px'>Atasan</th>"
                        . "<th>Tgl Masuk</th><th>Tgl Keluar</th>"
                        . "</tr></thead>";
                echo "<tbody>";
                $no=1;
                $query = "SELECT karyawanId, pin, nama, jabatanId, nama_jabatan, atasanId, nama_atasan, tempat, DATE_FORMAT(tgllahir,'%d %M %Y') as tgllahir "
                        . ", LEVELPOSISI, AKTIF, DATE_FORMAT(tglmasuk,'%d %M %Y') as tglmasuk, DATE_FORMAT(tglkeluar,'%d %M %Y') as tglkeluar ";
                $query.=" FROM dbmaster.v_karyawan_posisi order by nama";
                $tampil = mysqli_query($cnit, $query);
                while ($r=mysqli_fetch_array($tampil)){
                    echo "<td>$no</td>";
                    echo "<td>$r[karyawanId]</td>";
                    echo "<td>$r[nama]</td>";
                    echo "<td>$r[tempat]</td>";
                    echo "<td>$r[tgllahir]</td>";
                    echo "<td>$r[nama_jabatan]</td>";
                    echo "<td>$r[nama_atasan]</td>";
                    echo "<td>$r[tglmasuk]</td>";
                    echo "<td>$r[tglkeluar]</td>";
                    echo "</tr>";
                    $no++;
                }
                echo "</tbody>";
                echo "</table>";
            echo "</div>";//end x_content

        echo "</div>";//end panel

    echo "</div>";
                
?>