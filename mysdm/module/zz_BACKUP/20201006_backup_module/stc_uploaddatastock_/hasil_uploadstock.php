<?php
    $userid="";
    if (isset($_SESSION['IDCARD'])) $userid=$_SESSION['IDCARD'];
    if (empty($userid)) {
        echo "Anda harus login ulang..."; exit;
    }
    
        //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    include("PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
    include ("config/koneksimysqli_ms.php");
    
    
    $arridprod[]="";
    $arrnmprod[]="";
    $arrdivprodid[]="";
    $arrprodaktif[]="";
    $query = "select iprodid, nama, divprodid, aktif from sls.iproduk order by divprodid, nama";
    $tampilk= mysqli_query($cnms, $query);
    while ($zr= mysqli_fetch_array($tampilk)) {
        $zidprod=$zr['iprodid'];
        $znmprod=$zr['nama'];
        $zdivprodid=$zr['divprodid'];
        $zprodaktif=$zr['aktif'];
        
        $arridprod[]=$zidprod;
        $arrnmprod[]=$znmprod;
        $arrdivprodid[]=$zdivprodid;
        $arrprodaktif[]=$zprodaktif;
    }
    
    

    $now=date("mdYhis");
    $tmp01 =" dbtemp.tmpstcuploaddata_01".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.tmpstcuploaddata_02".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.tmpstcuploaddata_03".$_SESSION['USERID']."_$now ";
    
    $query = "CREATE TABLE $tmp01 (PERIODE DATE, DIVPRODID VARCHAR(5), KDPROD VARCHAR(10), NMPROD VARCHAR(200), "
            . " PQTY DECIMAL(20,2), PHNA DECIMAL(20,2), PVALUE DECIMAL(20,2), "
            . " NOBATCH VARCHAR(100), EXPDATE DATE, KDPRODSDM VARCHAR(10), NMPRODSDM VARCHAR(200))";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) {  echo "Error CREATE TABLE : $erropesan"; goto hapusdata; }
    
    
    $ptglpil=$_POST['e_periode01'];
    $tgl_pertama=$_POST['e_periode01'];
    $ptglpilihupload = date("Y-m-d", strtotime($ptglpil));
    $pperiode_ = date("Ym", strtotime($ptglpil));
    $pperiodeinsert = date("Y-m-01", strtotime($ptglpil));
    $pfile = $_FILES['fileToUpload']['name'];
    $pblnini=date("Y-m-d");
    
    $_SESSION['STCUPDPERTPIL']=$ptglpil;
    $_SESSION['STCUPDFOLDPIL']=$pfile;
    
    
    $pjudul="Upload Data Stock";
    
    include "config/koneksimysqli_ms.php";
    $aksi="module/stc_uploaddatastock/aksi_uploadstock.php";
    
?>


<div class="">
    
    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        

        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>

                <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form3' name='form2' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
                    <div  hidden class='col-sm-2'>
                        Periode
                        <div class="form-group">
                            <div class='input-group date' id='cbln01x'>
                                <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class='col-sm-2'>
                        <small>&nbsp;</small>
                        <div class="form-group">
                            <button type='button' class='btn btn-success btn-xs' onclick='self.history.back()'>Back</button>
                        </div>
                    </div>
                    
                    
                    <div id='c-data'>
                    <?PHP
                        unset($insert_stock);
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
                            //unset($insert_stock); $pbolehsave=false;
                            for($row=5; $row<=$totalrow; $row++){
                                
                                $pfile0 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(0, $row)->getValue());
                                $pfile1 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(1, $row)->getValue());
                                $pfile2 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(2, $row)->getValue());
                                $pfile3 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(3, $row)->getValue());
                                $pfile4 = mysqli_real_escape_string($cnms, $worksheet->getCellByColumnAndRow(4, $row)->getValue());
                                    
                                if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) AND empty($pfile3) AND empty($pfile4)) {
                                    continue;
                                }
                                    
                                if (!empty($pfile1)) $pfile1 = str_replace("'", "", $pfile1);
                                
                                
                                if (!empty($pfile2)) $pfile2 = str_replace("'", "", $pfile2);
                                if (!empty($pfile2)) $pfile2 = str_replace(" ", "", $pfile2);
                                if (!empty($pfile2)) $pfile2 = str_replace("*", "", $pfile2);
                                if (!empty($pfile2)) $pfile2 = str_replace(",","", $pfile2);
                                
                                $ptahun="";
                                $pbulan="";
                                $pthnblntgl="0000-00-00";
                                if (!empty($pfile4)) {
                                    $ppisah= explode("-", $pfile4);
                                    if (isset($ppisah[0])) $ptahun=$ppisah[0];
                                    if (isset($ppisah[1])) $pbulan=$ppisah[1];
                                    if (!empty($ptahun) AND !empty($pbulan)) $pthnblntgl="20".$ptahun."-".$pbulan."-01";
                                }
                                
                                //echo "$pfile0, $pfile1, $pfile2, $pfile3, $pthnblntgl<br/>";
                                
                                $insert_stock[] = "('$ptglpilihupload','$pfile0','$pfile1','$pfile2','$pfile3','$pthnblntgl')";
                                $pbolehsave=true;
                                
                            }
                            
                        }
                        
                        if ($pbolehsave==true) {
                            $query_stcok = "INSERT INTO $tmp01 (PERIODE, KDPROD, NMPROD, PQTY, NOBATCH, EXPDATE) VALUES "
                                . " ".implode(', ', $insert_stock);
                            mysqli_query($cnms, $query_stcok);
                            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error SIMPAN DATA : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01");  goto hapusdata; }
                            
                            $query = "CREATE TEMPORARY TABLE $tmp03 (SELECT * FROM sls.imaping_produk)";
                            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error TARIK MAPPING : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01");  goto hapusdata; }
                            
                            $query = "CREATE TEMPORARY TABLE $tmp02 (SELECT * FROM sls.imaping_produk WHERE kdproduk='XXXXX_XXXX_XXXX')";
                            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error TARIK MAPPING : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01");  goto hapusdata; }
                            
                            
                            $query = "INSERT INTO $tmp02 (kdproduk) SELECT DISTINCT KDPROD FROM $tmp01 WHERE KDPROD NOT IN (select distinct IFNULL(kdproduk,'') FROM $tmp03)";
                            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error SELECT MAPPING : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01");  goto hapusdata; }
                            
                            $query = "UPDATE $tmp02 a JOIN $tmp01 b on a.kdproduk=b.KDPROD SET a.nmproduk=b.NMPROD";
                            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error UPDATE MAPPING : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01");  goto hapusdata; }
                            
                            $query = "INSERT INTO sls.imaping_produk (kdproduk, nmproduk) SELECT DISTINCT kdproduk, nmproduk FROM $tmp02";
                            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error insert imaping_produk : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01");  goto hapusdata; }
                            
                            mysqli_query($cnms, "DROP TEMPORARY  TABLE $tmp02");
                            mysqli_query($cnms, "DROP TEMPORARY  TABLE $tmp03");
                            
                            $query = "UPDATE $tmp01 a JOIN sls.imaping_produk b on a.KDPROD=b.kdproduk SET a.KDPRODSDM=b.iprodid";
                            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error UPDATE : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01");  goto hapusdata; }
                            $query = "UPDATE $tmp01 a JOIN sls.iproduk b on IFNULL(a.KDPRODSDM,'')=IFNULL(b.iprodid,'') SET a.DIVPRODID=b.divprodid, a.NMPRODSDM=b.nama";
                            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error UPDATE iprodid : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01");  goto hapusdata; }
                            
                            $query = "DELETE FROM sls.istock";//WHERE DATE_FORMAT(bulan,'%Y%m')='$pperiode_'
                            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error HAPUS istock : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01");  goto hapusdata; }
                            
                            $query = "ALTER TABLE sls.istock AUTO_INCREMENT = 1";
                            mysqli_query($cnms, $query);
                            
                            $query = "INSERT INTO sls.istock (bulan, kdproduk, qty, nobatch, expdate)"
                                    . "SELECT '$pblnini' as bulan, KDPROD, PQTY, NOBATCH, EXPDATE FROM $tmp01 ORDER BY EXPDATE, KDPROD";
                            mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "Error INSERT istock : $erropesan"; mysqli_query($cnms, "DROP TABLE $tmp01");  goto hapusdata; }
                            
                            
                            echo "<b>DATA BERHASIL DIUPLOAD...</b><br/>";
                        }
                    
                    ?>
                        
                        <div class='x_content' style="margin-left:-20px; margin-right:-20px;"><!-- -->
                            
                            <table id='dtablepiluptgt' class='table table-striped table-bordered' width="100%" border="1px solid black">
                                
                                <thead>
                                    <tr>
                                        <th width='10px'>No</th>
                                        <th align="center" nowrap>Kode</th>
                                        <th align="center" nowrap>Nama</th>
                                        <th align="center" nowrap>Qty</th>
                                        <th align="center" nowrap>Batch</th>
                                        <th align="center" nowrap>Expired Date</th>
                                        <th align="center" nowrap>IProduk</th>
                                        <th align="center" nowrap></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?PHP
                                    //yang belum mapping
                                    $totalqty=0;
                                    $no=1;
                                    $query = "select * from $tmp01 WHERE IFNULL(KDPRODSDM,'')='' order by NMPROD, KDPROD";
                                    $tampil1= mysqli_query($cnms, $query);
                                    $ketemu1= mysqli_num_rows($tampil1);
                                    $jmlrec1=$ketemu1;
                                    if ($ketemu1>0) {
                                        while ($row1= mysqli_fetch_array($tampil1)) {
                                            $nfile0=$row1['DIVPRODID'];
                                            $nfile1=$row1['KDPROD'];
                                            $nfile2=$row1['PHNA'];
                                            $nfile3=$row1['NMPROD'];
                                            $nfile4=$row1['PQTY'];
                                            $nfile5=$row1['PVALUE'];
                                            $nfile6=$row1['NOBATCH'];
                                            $nfile7=$row1['EXPDATE'];
                                            $nfile8=$row1['KDPRODSDM'];
                                            $nfile9=$row1['NMPRODSDM'];
                                            
                                            
                                            if ($nfile7=="0000-00-00") $nfile7="";
                                            
                                            if (!empty($nfile7)) $nfile7 = date("F Y", strtotime($nfile7));
                                            
                                            if (empty($nfile4)) $nfile4=0;
                                            
                                            $totalqty=(double)$totalqty+(double)$nfile4;
                                            $nfile4=number_format($nfile4,0,",",",");
                                            
                                            
                                            $pwarna="";
                                            if (empty($nfile8)) $pwarna=" style='color:red;' ";
                                            
                                            echo "<tr $pwarna>";
                                            echo "<td nowrap>$no</td>";
                                            echo "<td nowrap>$nfile1</td>";
                                            echo "<td nowrap>$nfile3</td>";
                                            echo "<td nowrap align='right'>$nfile4</td>";
                                            echo "<td nowrap>$nfile6</td>";
                                            echo "<td nowrap>$nfile7</td>";
                                            echo "<td nowrap>";
                                            echo "<select class='soflow' id='cb_iprodid$no' name='cb_iprodid$no'>";
                                            echo "<option value=''>--Pilih--</option>";
                                            for($ix=1;$ix<count($arridprod);$ix++) {
                                                
                                                $zidprod=$arridprod[$ix];
                                                $znmprod=$arrnmprod[$ix];
                                                $zdivprodid=$arrdivprodid[$ix];
                                                $zdivprodid=$arrprodaktif[$ix];
                                                $zprodaktif=$arrprodaktif[$ix];
                                                $paktifnm="Aktif";
                                                if ($zprodaktif=="N") $paktifnm="Non Aktif";
                                                
                                                echo "<option value='$zidprod'>$znmprod ($paktifnm) - $zdivprodid</option>";
                                                
                                            }
                                            echo "</select>";
                                            
                                            echo "</td>";
                                            echo "<td nowrap>";
                                            echo "<input type='hidden' id='txt_kdprod$no' name='txt_kdprod$no' value='$nfile1'>";
                                            echo "<input type='button' class='btn btn-dark btn-xs' id='btnsave' name='btnsave' onclick=\"SimpanDataMapping('txt_kdprod$no', 'cb_iprodid$no')\" value='Save'>";
                                            echo "</td>";
                                            echo "</tr>";

                                            $no++;
                                                    
                                        }
                                    }
                                    
                                    //yang sudah mapping
                                    $query = "select * from $tmp01 WHERE IFNULL(KDPRODSDM,'')<>'' order by NMPROD, KDPROD";
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
                                            $nfile6=$row['NOBATCH'];
                                            $nfile7=$row['EXPDATE'];
                                            $nfile8=$row['KDPRODSDM'];
                                            $nfile9=$row['NMPRODSDM'];
                                            
                                            
                                            if ($nfile7=="0000-00-00") $nfile7="";
                                            
                                            if (!empty($nfile7)) $nfile7 = date("F Y", strtotime($nfile7));
                                            
                                            if (empty($nfile4)) $nfile4=0;
                                            
                                            $totalqty=(double)$totalqty+(double)$nfile4;
                                            $nfile4=number_format($nfile4,0,",",",");
                                            
                                            $pwarna="";
                                            if (empty($nfile8)) $pwarna=" style='color:red;' ";
                                            
                                            echo "<tr $pwarna>";
                                            echo "<td nowrap>$no</td>";
                                            echo "<td nowrap>$nfile1</td>";
                                            echo "<td nowrap>$nfile3</td>";
                                            echo "<td nowrap align='right'>$nfile4</td>";
                                            echo "<td nowrap>$nfile6</td>";
                                            echo "<td nowrap>$nfile7</td>";
                                            echo "<td nowrap>$nfile9</td>";
                                            echo "<td nowrap></td>";
                                            echo "</tr>";

                                            $no++;
                                                    
                                        }
                                    }
                                    $totalqty=number_format($totalqty,0,",",",");
                                    echo "<tr>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap align='right'>Total Qty : </td>";
                                    echo "<td nowrap align='right'>$totalqty</td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                    echo "</tr>";
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
                                
                                
                                function SimpanDataMapping(ikdprodsby, iprodid) {
                                    var eprodsby =document.getElementById(ikdprodsby).value;
                                    var eprodid =document.getElementById(iprodid).value;
                                    
                                    //alert(eprodsby+", "+eprodid);
                                    ok_ = 1;
                                    if (ok_) {
                                        var r = confirm('Apakah akan simpan data...?');
                                        if (r==true) {

                                            var txt="";

                                            $.ajax({
                                                type:"post",
                                                url:"module/stc_uploaddatastock/simpandatamaping.php?module=simpandatamapingstc&act=input",
                                                data:"uprodsby="+eprodsby+"&uprodid="+eprodid,
                                                success:function(data){
                                                    alert(data);
                                                }
                                            });


                                        }
                                    } else {
                                        //document.write("You pressed Cancel!")
                                        return 0;
                                    }
                                    
                                }
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

    
</div>


<?PHP
hapusdata:
    mysqli_query($cnms, "DROP  TABLE $tmp01");
    mysqli_query($cnms, "DROP TEMPORARY  TABLE $tmp02");
    mysqli_query($cnms, "DROP TEMPORARY  TABLE $tmp03");
    mysqli_close($cnms);
?>