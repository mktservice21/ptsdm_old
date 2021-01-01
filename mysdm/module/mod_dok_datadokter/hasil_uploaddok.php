<?php

    $pidcard="";
    if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
    if (empty($pidcard)) {
        echo "Anda harus login ulang..."; exit;
    }
    $puserid=$_SESSION['USERID'];
    
    $pmodule="";
    $pidmenu="";
    $pnact="";
    if (isset($_GET['act'])) $pnact=$_GET['act'];
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
    if (isset($_GET['idmenu'])) $pidmenu=$_GET['idmenu'];
    
        //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    include("PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
    include ("config/koneksimysqli_ms.php");
    
    $pidcabang=$_POST['cb_cabang'];
    $pfile = $_FILES['fileToUpload']['name'];
    
    $_SESSION['DOKUPIDCAB']=$pidcabang;
    $_SESSION['DOKUPNMFILE']=$pfile;
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpupdtdok01".$puserid."_$now ";
    
    $query = "CREATE TEMPORARY TABLE $tmp01 (icabangid VARCHAR(10), gelar VARCHAR(50), namalengkap VARCHAR(100), "
            . " spesialis VARCHAR(50), nohp VARCHAR(50))";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) {  echo "Error CREATE TABLE : $erropesan"; goto hapusdata; }
    
    
    
    $pjudul="Upload Data Dokter";
    
    $aksi="module/mod_dok_datadokter/aksi_uploaddokt.php";
    
    
    unset($insert_data);
    $pbolehsave=false;
    // upload file xlsx
    $pfstock = basename($_FILES['fileToUpload']['name']) ;
    move_uploaded_file($_FILES['fileToUpload']['tmp_name'], "fileupload/temp_file/".$pfstock);

    // beri permisi agar file xlsx dapat di baca
    chmod("fileupload/temp_file/".$_FILES['fileToUpload']['name'],0777);

    $objPHPExcel = PHPExcel_IOFactory::load("fileupload/temp_file/".$_FILES['fileToUpload']['name']);

    $jmlrec=0;

    $jmlrec=0;
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
        $totalrow = $worksheet->getHighestRow();
        $jmlrec=0;
        //unset($insert_data); $pbolehsave=false;
        for($row=2; $row<=$totalrow; $row++){

            $pfile0 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(0, $row)->getValue());
            $pfile1 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(1, $row)->getValue());
            $pfile2 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(2, $row)->getValue());
            $pfile3 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(3, $row)->getValue());
            $pfile4 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(4, $row)->getValue());

            if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4)) {
                continue;
            }



            if (!empty($pfile0)) $pfile0 = str_replace("'", " ", $pfile0);
            if (!empty($pfile1)) $pfile1 = str_replace("'", " ", $pfile1);
            if (!empty($pfile2)) $pfile2 = str_replace("'", " ", $pfile2);
            if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
            if (!empty($pfile4)) $pfile4 = str_replace("'", " ", $pfile4);
            
            //echo "$pfile0, $pfile1, $pfile2, $pfile3, $pfile4<br/>";

            
            $insert_data[] = "('$pidcabang','$pfile1','$pfile2','$pfile3','$pfile4')";
            $pbolehsave=true;

        }

    }
    
    if ($pbolehsave == true) {
        $query_data = "INSERT INTO $tmp01 (icabangid, gelar, namalengkap, spesialis, nohp) VALUES "
            . " ".implode(', ', $insert_data);
        mysqli_query($cnms, $query_data);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error SIMPAN DATA : $erropesan"; goto hapusdata; }
        
        
        $query = "DELETE FROM dr.masterdokter WHERE icabangid='$pidcabang'";
        mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error HAPUS DATA : $erropesan"; goto hapusdata; }
        
        mysqli_query($cnms, "ALTER TABLE dr.masterdokter AUTO_INCREMENT = 2000000008");
        
        $query = "INSERT INTO dr.masterdokter (icabangid, gelar, namalengkap, spesialis, nohp) "
                . " SELECT icabangid, gelar, namalengkap, spesialis, nohp FROM $tmp01";
        mysqli_query($cnms, $query);
        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error HAPUS DATA : $erropesan"; goto hapusdata; }
        
        echo "<b>DATA BERHASIL DIUPLOAD...</b><br/>";
        
    }
?>

<div class="">

    <div class="page-title">
        <div class="title_left">
            <h3>
                <?PHP echo $pjudul; ?>
            </h3>
        </div>
    </div>
    <div class="clearfix"></div>
    
    <!--row-->
    <div class="row">


                
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>

                <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=import&idmenu=$pidmenu"; ?>' 
                      id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  
                      enctype='multipart/form-data'>

                    <div class='col-sm-2'>
                        Cabang
                        <div class="form-group">
                            <select class='form-control input-sm' id="cb_cabang" name="cb_cabang" onchange="">
                                <?PHP
                                $query = "select iCabangId as icabangid, nama as nama_cabang FROM sls.icabang WHERE iCabangId='$pidcabang' ";
                                $query .=" order by nama";
                                $tampil= mysqli_query($cnms, $query);
                                while ($row= mysqli_fetch_array($tampil)) {
                                    $nidcab=$row['icabangid'];
                                    $nnmcab=$row['nama_cabang'];
                                    echo "<option value='$nidcab' selected>$nnmcab</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class='col-sm-5'>
                        <small>&nbsp;</small>
                       <div class="form-group">
                           <button type='button' class='btn btn-success btn-xs' onclick='self.history.back()'>Back</button>
                       </div>
                   </div>


                    <div id='loading'></div>
                    <div id='c-data'>

                        
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
                        
                        
                    </div>

                </form>





            </div>
        </div>
        


    </div>
    <!--end row-->
</div>


<?PHP
hapusdata:
    mysqli_query($cnms, "DROP TEMPORARY  TABLE $tmp01");
    mysqli_close($cnms);
?>