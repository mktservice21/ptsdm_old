<?php
    session_start();
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    include ("../../config/koneksimysqli_ms.php");
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpupdtdok01".$puserid."_$now ";
    
    $pmodule="";
    $pidmenu="";
    $pnact="";
    if (isset($_GET['act'])) $pnact=$_GET['act'];
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
    if (isset($_GET['idmenu'])) $pidmenu=$_GET['idmenu'];
    
    
    $pidcabang=$_POST['ucab'];
    
    $_SESSION['DOKUPIDCAB']=$pidcabang;
    
    $query = "CREATE TEMPORARY TABLE $tmp01 (SELECT * FROM dr.masterdokter WHERE icabangid='$pidcabang')";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) {  echo "Error CREATE TABLE : $erropesan"; goto hapusdata; }
    
    
    $aksi="module/mod_dok_datadokter/aksi_uploaddokt.php";
    
?>

                <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=import&idmenu=$pidmenu"; ?>' 
                      id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  
                      enctype='multipart/form-data'>

   
                        <div class='x_content' style="margin-left:-20px; margin-right:-20px;"><!-- -->
                            
                            <table id='dtablepiluptgt' class='table table-striped table-bordered' width="100%" border="1px solid black">
                                
                                <thead>
                                    <tr>
                                        <th width='10px'>No</th>
                                        <th align="center" nowrap>Gelar</th>
                                        <th align="center" nowrap>Nama Lengkap</th>
                                        <th align="center" nowrap>Spesialis</th>
                                        <th align="center" nowrap>No. Hp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?PHP
                                    $no=1;
                                    $query = "select * from $tmp01 ORDER BY namalengkap";
                                    $tampil1= mysqli_query($cnms, $query);
                                    $ketemu1= mysqli_num_rows($tampil1);
                                    $jmlrec1=$ketemu1;
                                    if ($ketemu1>0) {
                                        while ($row1= mysqli_fetch_array($tampil1)) {
                                            $nfile0=$row1['icabangid'];
                                            $nfile1=$row1['gelar'];
                                            $nfile2=$row1['namalengkap'];
                                            $nfile3=$row1['spesialis'];
                                            $nfile4=$row1['nohp'];
                                            
                                            echo "<tr>";
                                            echo "<td nowrap>$no</td>";
                                            echo "<td nowrap>$nfile1</td>";
                                            echo "<td nowrap>$nfile2</td>";
                                            echo "<td nowrap>$nfile3</td>";
                                            echo "<td nowrap>$nfile4</td>";
                                            echo "</tr>";

                                            $no++;
                                                    
                                        }
                                    }
                                    
                                    ?>
                                </tbody>
                            </table>
                            
                            
                            <script>

                                $(document).ready(function() {
                                    var dataTable = $('#dtablepiluptgt').DataTable( {
                                        "bPaginate": false,
                                        "bLengthChange": false,
                                        "bFilter": true,
                                        "bInfo": false,
                                        "ordering": false,
                                        "order": [[ 0, "desc" ]],
                                        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                                        "displayLength": -1,
                                        "columnDefs": [
                                            { "visible": false },
                                            { "orderable": false, "targets": 0 },
                                            { "orderable": false, "targets": 1 },
                                            //{ className: "text-right", "targets": [3, 6] },//right
                                            { className: "text-nowrap", "targets": [0, 1, 2, 3, 4] }//nowrap

                                        ],
                                        "language": {
                                            "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
                                        },
                                        //"scrollY": 460,
                                        "scrollX": true
                                    } );
                                    $('div.dataTables_filter input', dataTable.table().container()).focus();
                                } );
                                
                            </script>


                            <style>
                                .divnone {
                                    display: none;
                                }
                                #dtablepiluptgt th {
                                    font-size: 13px;
                                }
                                #dtablepiluptgt td { 
                                    font-size: 11px;
                                }
                                .imgzoom:hover {
                                    -ms-transform: scale(3.5); /* IE 9 */
                                    -webkit-transform: scale(3.5); /* Safari 3-8 */
                                    transform: scale(3.5);

                                }
                            </style>
                            
                        </div>
                        
                </form>


<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY TABLE $tmp01");
    mysqli_close($cnms);
?>