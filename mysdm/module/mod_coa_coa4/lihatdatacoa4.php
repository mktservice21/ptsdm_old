<script>
$(document).ready(function() {
    var groupColumn = 0;
    var groupColumn1 = 1;
    var groupColumn2 = 2;
    var table = $('#datatable').DataTable({
        fixedHeader: true,
        "ordering": false,
        "lengthMenu": [[500, 1000, 1500, -1], [500, 1000, 1500, "All"]],
        "displayLength": -1,
        dom: 'Bfrtip',
        buttons: [
            'excel'//, 'print'
        ]/*,
        "columnDefs": [
            { "visible": false, "targets": groupColumn },
            { "visible": false, "targets": groupColumn1 },
            { "visible": false, "targets": groupColumn2 }//,
            //{ className: "text-right", "targets": [6] }//,//right
        ]/*,
        "order": [[ groupColumn, 'asc' ]],
        "order": [[ groupColumn1, 'asc' ]],
        "order": [[ groupColumn2, 'asc' ]],
        "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;
 
            api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="7">'+group+'</td></tr>'
                    );
 
                    last = group;
                }
            } );
            api.column(groupColumn1, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="7">'+group+'</td></tr>'
                    );
 
                    last = group;
                }
            } );
            api.column(groupColumn2, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="5">'+group+'</td></tr>'
                    );
 
                    last = group;
                }
            } );
            
            
        }*/
    } );
 
    // Order by the grouping
    /*
    $('#datatable tbody').on( 'click', 'tr.group', function () {
        var currentOrder = table.order()[0];
        if ( currentOrder[0] === groupColumn2 && currentOrder[1] === 'asc' ) {
            table.order( [ groupColumn, 'desc' ] ).draw();
        }
        else {
            table.order( [ groupColumn, 'asc' ] ).draw();
        }  
    } );
    */
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
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    //$cnmy=$cnit;
    echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

        //panel
        echo "<div class='x_panel'>";
            //isi content
            echo "<div class='x_content'>";
                
            
            
                $printdate= date("d/m/Y");
                $tahun= date("Y");
                echo "<table width='90%' align='center' border='0' class='table table-striped table-bordered'>";
                    echo "<tr>";
                        echo "<td valign='top'>";
                            echo "<table border='0' width='30%'>";
                            echo "<tr><td><small style='color:blue;'>$_SESSION[NAMAPT]</small></td></tr>";
                            echo "<tr><td>Employee : <u>$_SESSION[NAMALENGKAP]</u></td></tr>";
                            echo "</table>";
                        echo "</td>";
                        echo "<td valign='top'>";
                            echo "<table align='left' border='0' width='100%'>";
                            echo "<tr><td align='left'><h2>Data COA</h2></td></tr>";
                            echo "<tr><td align='left'><b>Periode : $tahun</b></td></tr>";
                            echo "<tr><td align='right'><small><i>View Date : $printdate</i></small></td></tr>";
                            echo "</table>";
                        echo "</td>";
                    echo "</tr>";
                echo "</table><hr>&nbsp;</hr>";
    
    
                echo "<table id='datatable' class='table table-striped table-bordered'>";
                echo "<thead><tr><th>Level 1</th><th>Level 2</th><th>Level 3</th><th>Kode 4</th><th>Level 4</th>"
                . "<th>Gol</th><th>Aktif</th><th>Posting (kode)</th></tr></thead>";
                echo "<tbody>";
                $no=1;
                $query = "SELECT c1.*, c2.*, c3.*, c4.* FROM dbmaster.coa_level4 as c4 "
                        . "left join dbmaster.coa_level3 as c3 on c4.COA3=c3.COA3 "
                        . "left join dbmaster.coa_level2 as c2 on c3.COA2=c2.COA2 "
                        . "left join dbmaster.coa_level1 as c1 on c2.COA1=c1.COA1 order by c1.COA1, c2.COA2, c3.COA3, c4.COA4";
                
                $filekhusus="";
                if ($_SESSION['ADMINKHUSUS']=="Y") {
                    if (!empty($_SESSION['KHUSUSSEL']))
                        $filekhusus=" and (DIVISI in $_SESSION[KHUSUSSEL] or ifnull(DIVISI, '')='') ";
                }
                $query = "select * from dbmaster.v_coa_all WHERE 1=1 $filekhusus order by COA1, COA2, COA3, COA4";
                $tampil = mysqli_query($cnmy, $query);
                while ($r=mysqli_fetch_array($tampil)){
                    echo "<td><b>$r[COA1] - $r[NAMA1]</b></td>";
                    echo "<td><b>$r[COA2] - $r[NAMA2]</b></td>";
                    echo "<td><b>$r[COA3] - $r[NAMA3]</b></td>";
                    echo "<td>$r[COA4]</td>";
                    echo "<td>$r[NAMA4]</td>";
                    echo "<td>$r[GOL4]</td>";
                    echo "<td>$r[AKTIF4]</td>";
                    
                    
                    
                    if ($r['DIVISI2']=="OTC") {
                        $kode=""; $namakode="";
                        if (!empty($r['subpost']) AND empty($r['nmsubpost'])) {
                            $kode=$r['subpost'];
                            $namakode=  getfield("select nmsubpost as lcfields from hrd.brkd_otc where subpost='$kode'");
                        }elseif (!empty($r['nmsubpost'])) {
                            $kode=$r['subpost'];
                            $namakode=$r['nmsubpost'];
                        }else{
                            $kode=$r['kodeid'];
                            $namakode=$r['nama_kodeotc'];
                        }

                        $namaall="";
                        if (!empty($kode) AND !empty($namakode))
                            $namaall=$kode." - ".$namakode;
                        
                        echo "<td>$namaall</td>";
                    }else{
                        if (!empty($r['kodeid'])) 
                            echo "<td>$r[kodeid] - $r[nama_kode]</td>";
                        else
                            echo "<td></td>";
                    }
                    echo "</tr>";
                    $no++;
                }
                echo "</tbody>";
                echo "</table>";
            echo "</div>";//end x_content

        echo "</div>";//end panel

    echo "</div>";
                
?>