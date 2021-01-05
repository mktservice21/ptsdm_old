<?php
    $userid=$_SESSION['IDCARD'];
    if (empty($userid)) {
        echo "Anda harus login ulang..."; exit;
    }
    
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    include("PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
    include ("config/koneksimysqli_ms.php");
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DTUPTGTCABTUSR_01".$_SESSION['USERID']."_$now ";
    
    $query = "CREATE TABLE $tmp01 (PERIODE DATE, DIVPRODID VARCHAR(5), KDPROD VARCHAR(10), NMPROD VARCHAR(200), PQTY DECIMAL(20,2), PHNA DECIMAL(20,2), PVALUE DECIMAL(20,2))";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error CREATE TABLE : $erropesan"; exit; }
    
    
    $ptglpil=$_POST['e_periode01'];
    $tgl_pertama=$_POST['e_periode01'];
    $ptglpilihupload = date("Y-m-d", strtotime($ptglpil));
    $pperiode_ = date("Y", strtotime($ptglpil));
    
    
    $pidcabpil=$_POST['cb_cabang'];
    $pidregion=$_POST['cb_region'];
    
    $pfile = $_FILES['fileToUpload']['name'];
    
    
    
    $_SESSION['TGTUPDPERTPILCBT']=$ptglpil;
    $_SESSION['TGTUPDCABPILCBT']=$pidcabpil;
    $_SESSION['TGTUPDREGPILCBT']=$pidregion;
    $_SESSION['TGTUPDFOLDPILCBT']=$pfile;
    
    //echo "$ptglpil : $pidcabpil";
    
    
    $pnmarea="";
    
    $pjudul="Upload Target Cabang Per Tahun";
?>

<div class="">

    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli_ms.php";
        $aksi="module/tgt_upload_tgt_cab/aksi_uploadtglcab.php";
        switch($_GET['act']){
            default:
                ?>
        
        
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form3' name='form2' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            
                            <div class='col-sm-2'>
                                Periode
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01x'>
                                        <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_cabang" name="cb_cabang" onchange="ShowDataArea()">
                                        <?PHP
                                        $query = "select idcabang, nama from ms.cbgytd where aktif='Y' and idcabang='$pidcabpil' order by nama";
                                        $tampil = mysqli_query($cnms, $query);
                                        while ($rx= mysqli_fetch_array($tampil)) {
                                            $nidcab=$rx['idcabang'];
                                            $nnmcab=$rx['nama'];
                                            if ($pidcabpil==$nidcab)
                                                echo "<option value='$nidcab' selected>$nnmcab</option>";
                                            else
                                                echo "<option value='$nidcab'>$nnmcab</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <button type='button' class='btn btn-success btn-xs' onclick='self.history.back()'>Back</button>
                               </div>
                           </div>
                            
                        </form>
                        
                        
                        <div id='c-data'>
                            <?PHP
                            
                            // upload file xls
                            $target = basename($_FILES['fileToUpload']['name']) ;
                            move_uploaded_file($_FILES['fileToUpload']['tmp_name'], "fileupload/temp_file/".$target);
                            
                            
                            // beri permisi agar file xls dapat di baca
                            chmod("fileupload/temp_file/".$_FILES['fileToUpload']['name'],0777);
                             
                            
                            $objPHPExcel = PHPExcel_IOFactory::load("fileupload/temp_file/".$_FILES['fileToUpload']['name']);
                            
                            $jmlrec=0;
                            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
                                $totalrow = $worksheet->getHighestRow();
                                $jmlrec=0;
                                
                                for($row=2; $row<=$totalrow; $row++){
                                    $pfile0 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(0, $row)->getValue());
                                    $pfile1 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(1, $row)->getValue());
                                    $pfile2 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(2, $row)->getValue());
                                    $pfile3 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(3, $row)->getValue());
                                    $pfile4 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(4, $row)->getValue());
                                    
                                    if (empty($pfile0) AND empty($pfile1) AND empty($pfile2)) {
                                        continue;
                                    }
            
                                    $jml_pj_kode=  strlen($pfile1);
                                    $awalkodeprod=10-(double)$jml_pj_kode;
                                    $pfile1=str_repeat("0", $awalkodeprod).$pfile1;
                                    
                                    
                                    if (!empty($pfile2)) $pfile2 = str_replace("'", "", $pfile2);
                                    if (!empty($pfile2)) $pfile2 = str_replace(" ", "", $pfile2);
                                    if (!empty($pfile2)) $pfile2 = str_replace("*", "", $pfile2);
                                    if (!empty($pfile2)) $pfile2 = str_replace(",","", $pfile2);
            
                                    if (!empty($pfile4)) $pfile4 = str_replace("'", "", $pfile4);
                                    if (!empty($pfile4)) $pfile4 = str_replace(" ", "", $pfile4);
                                    if (!empty($pfile4)) $pfile4 = str_replace("*", "", $pfile4);
                                    if (!empty($pfile4)) $pfile4 = str_replace(",","", $pfile4);
                                    
                                    
                                    if (!empty($pfile3)) $pfile3 = str_replace("'", " ", $pfile3);
                                    
                                    
                                    if (empty($pfile2)) $pfile2=0;
                                    if (empty($pfile4)) $pfile4=0;
                                    
                                    $pfile5=0;
                                    $pfile5=(DOUBLE)$pfile2*(DOUBLE)$pfile4;
                                    
                                    //echo "$pfile0, $pfile1, $pfile2, $pfile3, $pfile4, $pfile5<br/>";
                                    
                                    //ganti produk ---- info tgl : 07-FEB-2020 jam 16:00 BPK YAKUB
                                    if ($pfile1=="0000000272") $pfile1="0000000351";
									
                                    $query = "INSERT INTO $tmp01 (PERIODE, DIVPRODID, KDPROD, PHNA, NMPROD, PQTY, PVALUE)VALUES"
                                            . "('$ptglpilihupload', '$pfile0', '$pfile1', '$pfile2', '$pfile3', '$pfile4', $pfile5)";
                                    mysqli_query($cnms, $query);
                                    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error SIMPAN DATA : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01"); mysqli_close($cnms); exit; }
    
                                }
                                
                                
                            }
							
							
                            $query = "UPDATE $tmp01 a JOIN sls.iproduk b on a.KDPROD=b.iprodid SET a.NMPROD=b.nama, a.DIVPRODID=b.divprodid WHERE a.KDPROD='0000000351'";
                            mysqli_query($cnms, $query);
							
                                
                            $query = "SELECT * FROM $tmp01";
                            $tamp_= mysqli_query($cnms, $query);
                            $ketemu_= mysqli_num_rows($tamp_);
                            if ($ketemu_>0) {
                                // eksekusi delete data pada cabang aera dibulan tsb.
                                
                                if ((DOUBLE)$pperiode_ > 2019) {
                                    if ($pidcabpil=="N" OR $pidcabpil=="B" OR $pidcabpil=="T") {
                                        
                                        $query ="DELETE FROM tgt.targettahun_nasional WHERE DATE_FORMAT(bulan,'%Y')='$pperiode_' AND icabangid='$pidcabpil' AND YEAR(bulan)>'2019'";
                                        mysqli_query($cnms, $query);
                                        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error DELETE DATA : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01"); mysqli_close($cnms); exit; }


                                        $query ="INSERT INTO tgt.targettahun_nasional (bulan, divprodid, iprodid, hna, qty, value, icabangid)"
                                                . " SELECT PERIODE, DIVPRODID, KDPROD, PHNA, PQTY, PVALUE, '$pidcabpil' FROM $tmp01 ";
                                        mysqli_query($cnms, $query);
                                        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error DELETE DATA : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01"); mysqli_close($cnms); exit; }
                                        
                                    }else{
                                        $query ="DELETE FROM tgt.targettahun WHERE DATE_FORMAT(bulan,'%Y')='$pperiode_' AND icabangid='$pidcabpil' AND YEAR(bulan)>'2019'";
                                        mysqli_query($cnms, $query);
                                        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error DELETE DATA : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01"); mysqli_close($cnms); exit; }


                                        $query ="INSERT INTO tgt.targettahun (bulan, divprodid, iprodid, hna, qty, value, icabangid)"
                                                . " SELECT PERIODE, DIVPRODID, KDPROD, PHNA, PQTY, PVALUE, '$pidcabpil' FROM $tmp01 ";
                                        mysqli_query($cnms, $query);
                                        $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error DELETE DATA : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01"); mysqli_close($cnms); exit; }
                                    }
                                    
                                }
                                
                                
                                echo "<b>DATA BERHASIL DIUPLOAD...</b><br/>";
                            }
                            ?>
                            
                            
                            <div class='x_content' style="margin-left:-20px; margin-right:-20px;"><!-- -->

                                <!--<table id='dtablepiluptgt' class='table table-striped table-bordered' width='100%'>-->
                                <table id='dtablepiluptgt' class='table table-striped table-bordered' width="100%" border="1px solid black">
                                    <thead>
                                        <tr>
                                            <th width='10px'>No</th>
                                            <th align="center" nowrap>DIVISI</th>
                                            <th align="center" nowrap>KD PRODUK</th>
                                            <th align="center" nowrap>HNA</th>
                                            <th align="center" nowrap>NAMA PRODUK</th>
                                            <th align="center" nowrap>QTY</th>
                                            <th align="center" nowrap>VALUE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?PHP
                                        
                                            $pgrdtotal=0;
                                            $no=1;
                                            $query = "select * from $tmp01";
                                            $tampil= mysqli_query($cnms, $query);
                                            $ketemu= mysqli_num_rows($tampil);
                                            $jmlrec=$ketemu;
                                            if ($ketemu>0) {
                                                while ($row= mysqli_fetch_array($tampil)) {

                                                    $nfile0=$row['DIVPRODID'];
                                                    $nfile1=$row['KDPROD'];
                                                    $nfile2=$row['PHNA'];
                                                    $nfile3=$row['NMPROD'];
                                                    $nfile4=$row['PQTY'];
                                                    $nfile5=$row['PVALUE'];

                                                    $pgrdtotal=(double)$pgrdtotal+(double)$nfile5;
                                                    
                                                    $nfile2=number_format($nfile2,0,",",",");
                                                    $nfile4=number_format($nfile4,0,",",",");
                                                    $nfile5=number_format($nfile5,0,",",",");

                                                    echo "<tr>";
                                                    echo "<td nowrap>$no</td>";
                                                    echo "<td nowrap>$nfile0</td>";
                                                    echo "<td nowrap>$nfile1</td>";
                                                    echo "<td nowrap align='right'>$nfile2</td>";
                                                    echo "<td nowrap>$nfile3</td>";
                                                    echo "<td nowrap align='right'>$nfile4</td>";
                                                    echo "<td nowrap align='right'>$nfile5</td>";
                                                    echo "</tr>";

                                                    $no++;
                                                }
                                                
                                                echo "<tr>";
                                                echo "<td nowrap colspan='7' align='center'>&nbsp;</td>";
                                                echo "<td class='divnone'></td>";
                                                echo "<td class='divnone'></td>";
                                                echo "<td class='divnone'></td>";
                                                echo "<td class='divnone'></td>";
                                                echo "<td class='divnone'></td>";
                                                echo "<td class='divnone'></td>";
                                                echo "</tr>";
                                                
                                                $pgrdtotal=number_format($pgrdtotal,0,",",",");
                                                
                                                
                                                $query = "select DIVPRODID, SUM(PVALUE) as GVALUE from $tmp01 GROUP BY 1 ORDER BY 1";
                                                $tampilg= mysqli_query($cnms, $query);
                                                $ketemug= mysqli_num_rows($tampilg);
                                                if ($ketemug>0) {
                                                    while ($rg= mysqli_fetch_array($tampilg)) {
                                                        $pndivi=$rg['DIVPRODID'];
                                                        $ntotperdiv=$rg['GVALUE'];
                                                        
                                                        $ntotperdiv=number_format($ntotperdiv,0,",",",");
                                                        
                                                        echo "<tr>";
                                                        echo "<td nowrap colspan='6' align='right'><b>Total $pndivi : </b></td>";
                                                        echo "<td class='divnone'></td>";
                                                        echo "<td class='divnone'></td>";
                                                        echo "<td class='divnone'></td>";
                                                        echo "<td class='divnone'></td>";
                                                        echo "<td class='divnone'></td>";
                                                        echo "<td nowrap align='right'><b>$ntotperdiv</b></td>";
                                                        echo "</tr>";
                                                         
                                                    }
                                                    
                                                    echo "<tr>";
                                                    echo "<td nowrap colspan='7' align='center'>&nbsp;</td>";
                                                    echo "<td class='divnone'></td>";
                                                    echo "<td class='divnone'></td>";
                                                    echo "<td class='divnone'></td>";
                                                    echo "<td class='divnone'></td>";
                                                    echo "<td class='divnone'></td>";
                                                    echo "<td class='divnone'></td>";
                                                    echo "</tr>";
                                                     
                                                }
                                                
                                                echo "<tr>";
                                                echo "<td nowrap colspan='6' align='right'><b>Grand Total $pnmarea : </b></td>";
                                                echo "<td class='divnone'></td>";
                                                echo "<td class='divnone'></td>";
                                                echo "<td class='divnone'></td>";
                                                echo "<td class='divnone'></td>";
                                                echo "<td class='divnone'></td>";
                                                echo "<td nowrap align='right'><b>$pgrdtotal</b></td>";
                                                echo "</tr>";
                                                 

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
                                                { className: "text-right", "targets": [3] },//right
                                                { className: "text-nowrap", "targets": [0, 1, 2] }//nowrap

                                            ],
                                            "language": {
                                                "zeroRecords": "Lihat Page di bawah!!! Jika ada Page, Pilih Page 1...!!! Jika tidak ada Page, maka data KOSONG..."
                                            },
                                            "scrollY": 460,
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
                        
                    </div>
                </div>
                <?PHP

                
                
                hapusdata:
                    mysqli_query($cnms, "DROP  TABLE $tmp01");
                
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>