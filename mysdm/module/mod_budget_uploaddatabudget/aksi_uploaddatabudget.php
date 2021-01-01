<?php
    //session_start();
    $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "Anda harus login ulang..."; exit;
    }
    
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","500M");
    ini_set('max_execution_time', 0);
    
    $skey="1";
    if (isset($_GET['skey'])) {
        $skey=$_GET['skey'];
    }
    
    $ptxturl=$_POST['e_txturl'];
    $ptahunpilih=$_POST['e_tahun'];
    $pdivisipilih=$_POST['cb_divpilih'];
    $pkaryawanid=$_POST['cb_karyawan'];
    $pdepartemen=$_POST['cb_dept'];
    $pcabangid=$_POST['cb_cabang'];
    
    $_SESSION['BGTUPDTHN']=$ptahunpilih;
    $_SESSION['BGTUPDDVL']=$pdivisipilih;
    $_SESSION['BGTUPDKRY']=$pkaryawanid;
    $_SESSION['BGTUPDDPT']=$pdepartemen;
    $_SESSION['BGTUPDCAB']=$pcabangid;
    
    $pkaryawaid = $pkaryawanid;
    $icabangid = $pcabangid;
    
    if ($pdivisipilih=="ETH") {
        $pseldivpili1="selected";
        $pseldivpili2="";
    }else{
        $pseldivpili1="";
        $pseldivpili2="selected";
    }
    
    if ($pdepartemen=="SLS") {
        $pseldeppili0="";
        $pseldeppili1="selected";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="FIN") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="selected";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="MS") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="selected";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="IT") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="selected";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="AUDIT") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="selected";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="PCH") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="selected";
        $pseldeppili7="";
        $pseldeppili8="";
    }elseif ($pdepartemen=="BUSDV") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="selected";
        $pseldeppili8="";
    }elseif ($pdepartemen=="MKT") {
        $pseldeppili0="";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="selected";
    }else{
    
        $pseldeppili0="selected";
        $pseldeppili1="";
        $pseldeppili2="";
        $pseldeppili3="";
        $pseldeppili4="";
        $pseldeppili5="";
        $pseldeppili6="";
        $pseldeppili7="";
        $pseldeppili8="";
    
    }
    
    include ("config/koneksimysqli.php");
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPUPSBGTDIV_01".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.TMPUPSBGTDIV_02".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.TMPUPSBGTDIV_03".$_SESSION['USERID']."_$now ";
    
        
    $query = "CREATE TABLE $tmp01 (
                nourut MEDIUMINT NOT NULL AUTO_INCREMENT,
                bulan date,
                div_pilih varchar(5), departemen varchar(10), 
                karyawanid VARCHAR(10) NOT NULL, icabangid VARCHAR(10), icabangid_o VARCHAR(10), 
                kodeid VARCHAR(10), nm_id VARCHAR(150), coa4 VARCHAR(20), nama_coa4 VARCHAR(150), jumlah DECIMAL(20,2), 
                saldoawal DECIMAL(20,2), jumlah_tambahan DECIMAL(20,2), keterangan VARCHAR(250),
                PRIMARY KEY (nourut)
           )";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE TABLE : $erropesan"; goto hapusdata; }
    
                                    
    
    $pbolehupload=false;
    
    if ($skey=="1") {
        $pbolehupload=true;
    }
    
    $pjudul="Upload Data Budget";
    if ($pbolehupload==true) {
        $pjudul="Proses Upload Data Budget";
        
        
        include("PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");

        $pfile = $_FILES['fileToUpload']['name'];
        $_SESSION['BGTUPDFIL']=$pfile;
        
    }
    
    
?>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="">

    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        
        <?php
        $aksi="module/mod_budget_uploaddatabudget/aksi_uploaddatabudget2.php";
        
                ?>
        
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='form_data2' name='form2' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $puserid; ?>' Readonly>
                            <input type='hidden' class='form-control' id='e_tahun' name='e_tahun' value='<?PHP echo $ptahunpilih; ?>' Readonly>
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                <div class='x_panel'>

                                    <div class='x_panel'>
                                        <div class='x_content'>
                                            <div class='col-md-12 col-sm-12 col-xs-12'>


                                                <div class='form-group'>
                                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                                    <div class='col-xs-5'>
                                                        <select class='soflow' name='cb_divpilih' id='cb_divpilih' onchange="">
                                                            <?php
                                                            if ($pdivisipilih=="ETH") {
                                                                echo "<option value='ETH' $pseldivpili1>ETHICAL</option>";
                                                            }else{
                                                                echo "<option value='OTC' $pseldivpili2>CHC</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class='form-group'>
                                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                                    <div class='col-xs-5'>
                                                        <select class='soflow' name='cb_karyawan' id='cb_karyawan' onchange="">
                                                            <?php

                                                            $query = "select karyawanId, nama From hrd.karyawan
                                                                WHERE karyawanid='$pkaryawaid' ";
                                                            $tampil = mysqli_query($cnmy, $query);
                                                            while ($z= mysqli_fetch_array($tampil)) {
                                                                $pkaryid=$z['karyawanId'];
                                                                $pkarynm=$z['nama'];
                                                                $pkryid=(INT)$pkaryid;
                                                                echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class='form-group'>
                                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Departemen <span class='required'></span></label>
                                                    <div class='col-xs-5'>
                                                        <select class='soflow' name='cb_dept' id='cb_dept' onchange="">
                                                            <?php
                                                            echo "<option value='' $pseldeppili0>--Pilihan--</option>";
                                                            echo "<option value='SLS' $pseldeppili1>SALES</option>";
                                                            echo "<option value='FIN' $pseldeppili2>FINANCE</option>";
                                                            echo "<option value='MS' $pseldeppili3>MS</option>";
                                                            echo "<option value='IT' $pseldeppili4>IT</option>";
                                                            echo "<option value='AUDIT' $pseldeppili5>AUDIT</option>";
                                                            echo "<option value='PCH' $pseldeppili6>PURCHASING</option>";
                                                            echo "<option value='BUSDV' $pseldeppili7>BUSSINESS DEVELOPMENT</option>";
                                                            echo "<option value='MKT' $pseldeppili8>MARKETING</option>";
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class='form-group'>
                                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                                    <div class='col-xs-5'>
                                                        <select class='soflow' name='cb_cabang' id='cb_cabang' onchange="">
                                                            <?php
                                                            echo "<option value='' selected>--Pilih--</option>";
                                                            if ($pdivisipilih=="OTC" OR $pdivisipilih=="OT" OR $pdivisipilih=="CHC") {
                                                                $query = "select icabangid_o as icabangid, nama as nama From dbmaster.v_icabang_o 
                                                                    WHERE icabangid_o='$icabangid' ";
                                                            }else{
                                                                $query = "select icabangid as icabangid, nama as nama From MKT.icabang
                                                                    WHERE icabangid='$icabangid' ";
                                                            }
                                                            $tampil = mysqli_query($cnmy, $query);
                                                            while ($z= mysqli_fetch_array($tampil)) {
                                                                $pcabangid=$z['icabangid'];
                                                                $pcabnm=$z['nama'];
                                                                $pcabid=(INT)$pcabangid;
                                                                echo "<option value='$pcabangid' selected>$pcabnm</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class='form-group'>
                                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                                    <div class='col-md-4'>
                                                        <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        
                        </form>
                        
                        <div id='c-data'>
                            <?PHP
                            if ($pbolehupload==true) {
                                // upload file xls
                                $target = basename($_FILES['fileToUpload']['name']) ;
                                move_uploaded_file($_FILES['fileToUpload']['tmp_name'], "fileupload/temp_file/".$target);


                                // beri permisi agar file xls dapat di baca
                                chmod("fileupload/temp_file/".$_FILES['fileToUpload']['name'],0777);


                                $objPHPExcel = PHPExcel_IOFactory::load("fileupload/temp_file/".$_FILES['fileToUpload']['name']);
                                
                                $psudhupload=false;
                                $jmlrec=0;
                                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
                                    $totalrow = $worksheet->getHighestRow();
                                    $jmlrec=0;
                                    
                                    $psimpandata=false;
                                    unset($pinsert_data_detail);//kosongkan array
                                    
                                    if ($psudhupload == true) continue;
                                    
                                    for($row=5; $row<=$totalrow; $row++){
                                        $pfile0 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(0, $row)->getValue());
                                        $pfile1 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(1, $row)->getValue());
                                        $pfile2 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(2, $row)->getValue());
                                        
                                        $pfile15 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(15, $row)->getCalculatedValue());
                                        
                                        
                                        if (empty($pfile0) AND empty($pfile1) AND empty($pfile2)) {
                                            continue;
                                        }
                                        
                                        if (!empty($pfile2)) $pfile2 = str_replace("'", "", $pfile2);
                                        if (!empty($pfile2)) $pfile2 = str_replace("*", "", $pfile2);
                                        
                                        if (!empty($pfile15)) $pfile15 = str_replace("'", "", $pfile15);
                                        if (!empty($pfile15)) $pfile15 = str_replace(" ", "", $pfile15);
                                        if (!empty($pfile15)) $pfile15 = str_replace("*", "", $pfile15);
                                        if (!empty($pfile15)) $pfile15 = str_replace(",","", $pfile15);
                                        
                                        $pmulaifield=3;
                                        for ($ifield=1; $ifield<=12; $ifield++) {
                                            
                                            $ppilihbulan=$ptahunpilih."-0".$ifield."-01";
                                            if (strlen($ifield)>1) $ppilihbulan="2021-".$ifield."-01";
                                            
                                            $pfile_jml = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow($pmulaifield, $row)->getValue());
                                            if (empty($pfile_jml)) $pfile_jml=0;
                                            
                                            if (!empty($pfile_jml)) $pfile_jml = str_replace("'", "", $pfile_jml);
                                            if (!empty($pfile_jml)) $pfile_jml = str_replace(" ", "", $pfile_jml);
                                            if (!empty($pfile_jml)) $pfile_jml = str_replace("*", "", $pfile_jml);
                                            if (!empty($pfile_jml)) $pfile_jml = str_replace(",","", $pfile_jml);
                                            
                                            
                                            //echo "$ifield. - bln : $ppilihbulan, $pfile0, $pfile1, $pfile2, $pfile_jml, TTL : $pfile15 : $pdivisipilih, $pkaryawanid, $pdepartemen, $pcabangid<br/>";
                                            
                                            
                                            $pinsert_data_detail[] = "('$ppilihbulan', '$pdivisipilih', '$pkaryawanid', '$pdepartemen', '$pcabangid', '$pcabangid', '$pfile0', '$pfile1', '$pfile2', '$pfile_jml')";
                                            $psimpandata=true;
                                            $psudhupload=true;
                                            
                                            $pmulaifield++;
                                            $pfile_jml=0;
                                        }
                                        
                                        
                                    }
                                    
                                    
                                }
                                
                                
                                
                                if ($psimpandata==true) {
                                    
                                    $query_detail="INSERT INTO $tmp01 (bulan, div_pilih, karyawanid, departemen, icabangid, icabangid_o, coa4, nama_coa4, nm_id, jumlah) VALUES ".implode(', ', $pinsert_data_detail);
                                    $pinsertdetail = mysqli_query($cnmy, $query_detail);
                                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT TABLE TEMP : $erropesan"; goto hapusdata; }
                                    
                                    
                                    $query = "select bulan from $tmp01 WHERE IFNULL(bulan,'')='' OR IFNULL(bulan,'0000-00-00')='0000-00-00'";
                                    $tampil= mysqli_query($cnmy, $query);
                                    $ketemu= mysqli_num_rows($tampil);
                                    if ((INT)$ketemu>0) {
                                        echo "UPLOAD GAGAL....";
                                        goto hapusdata;
                                    }
                                    
                                    $pketicab="icabangid";
                                    if ($pdivisipilih=="OTC" OR $pdivisipilih=="OT" OR $pdivisipilih=="CHC") $pketicab="icabangid_o";
                                    
                                    $query_detail="DELETE FROM dbmaster.t_budget_divisi WHERE YEAR(bulan)='$ptahunpilih' "
                                            . " AND div_pilih='$pdivisipilih' AND IFNULL(departemen,'')='$pdepartemen' "
                                            . " AND karyawanid='$pkaryawanid' AND IFNULL($pketicab,'')='$pcabangid'";
                                    $pinsertdetail = mysqli_query($cnmy, $query_detail);
                                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE TABLE : $erropesan"; goto hapusdata; }
                                    
                                    mysqli_query($cnmy, "ALTER TABLE dbmaster.t_budget_divisi AUTO_INCREMENT = 1");
                                    
                                    $query = "INSERT INTO dbmaster.t_budget_divisi (bulan, div_pilih, departemen, karyawanid, icabangid, icabangid_o, nm_id, coa4, jumlah, userid)"
                                            . "SELECT bulan, div_pilih, departemen, karyawanid, icabangid, icabangid_o, nm_id, coa4, jumlah, '$puserid' as userid FROM $tmp01 "
                                            . " ORDER BY coa4, bulan";
                                    mysqli_query($cnmy, $query);
                                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error INSERT TABLE : $erropesan"; goto hapusdata; }
                                    
                                }
                                
                                
                            
                            }    
                            ?>
                        
                            
                            
                            <div class='x_content' style="margin-left:-20px; margin-right:-20px;"><!-- -->

                                <!--<table id='dtablepiluptgt' class='table table-striped table-bordered' width='100%'>-->
                                <table id='dtablepiluptgt' class='table table-striped table-bordered' width="100%" border="1px solid black">
                                    <thead>
                                        <tr>
                                            <th width='10px'>No</th>
                                            <th align="center" nowrap>COA</th>
                                            <th align="center" nowrap>Nama Perkiranan</th>
                                            <th align="center" nowrap>Bulan</th>
                                            <th align="center" nowrap>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?PHP
                                        $no=1;
                                        $ptotal=0;
                                        $ptotalcoa=0;
                                        $query = "select DISTINCT coa4, nama_coa4 from $tmp01 order by coa4, nama_coa4";
                                        $tampil1= mysqli_query($cnmy, $query);
                                        $ketemu1= mysqli_num_rows($tampil1);
                                        if ($ketemu1>0) {
                                            while ($row1= mysqli_fetch_array($tampil1)) {
                                                $ncoa4=$row1['coa4'];
                                                $nnamacoa=$row1['nama_coa4'];
                                                
                                                $ptotalcoa=0;
                                                $query = "select * from $tmp01 WHERE coa4='$ncoa4' order by coa4, nama_coa4, bulan";
                                                $tampil= mysqli_query($cnmy, $query);
                                                $ketemu= mysqli_num_rows($tampil);
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $nbulan=$row['bulan'];
                                                    $njml=$row['jumlah'];

                                                    $nbulan = date("F Y", strtotime($nbulan));
                                                    
                                                    $ptotalcoa=(double)$ptotalcoa+(double)$njml;
                                                    $ptotal=(double)$ptotal+(double)$njml;
                                                    $njml=number_format($njml,0,",",",");


                                                    echo "<tr>";
                                                    echo "<td nowrap>$no</td>";
                                                    echo "<td nowrap>$ncoa4</td>";
                                                    echo "<td nowrap>$nnamacoa</td>";
                                                    echo "<td nowrap>$nbulan</td>";
                                                    echo "<td nowrap align='right'>$njml</td>";
                                                    echo "</tr>";

                                                    $no++;
                                                }
                                                
                                                $ptotalcoa=number_format($ptotalcoa,0,",",",");

                                                echo "<tr style='font-weight:bold;'>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap>TOTAL $ncoa4 - $nnamacoa : </td>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap align='right'>$ptotalcoa</td>";
                                                echo "</tr>";
                                                
                                            }
                                            
                                            $ptotal=number_format($ptotal,0,",",",");

                                            echo "<tr style='font-weight:bold;'>";
                                            echo "<td nowrap></td>";
                                            echo "<td nowrap></td>";
                                            echo "<td nowrap>GRAND TOTAL : </td>";
                                            echo "<td nowrap></td>";
                                            echo "<td nowrap align='right'>$ptotal</td>";
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
                                            //"bFilter": true,
                                            "bInfo": false,
                                            "ordering": false,
                                            "order": [[ 0, "desc" ]],
                                            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                                            "displayLength": -1,
                                            "columnDefs": [
                                                { "visible": false },
                                                { "orderable": false, "targets": 0 },
                                                { "orderable": false, "targets": 1 },
                                                { className: "text-right", "targets": [4] },//right
                                                { className: "text-nowrap", "targets": [0, 1,2,3,4] }//nowrap

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
                        
                        
                        
                    </div>
                </div>
        
        
        
    </div>
    
</div>
<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;

<style>
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }
</style>


<script>
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("myBtn").style.display = "block";
      } else {
        document.getElementById("myBtn").style.display = "none";
      }
    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    }
</script>


<!--<script src="module/mod_br_entrydcc/mytransaksi.js"></script>-->
<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
    #kotak-multi {
        resize: both;
        overflow: auto;
    }
    .divnone {
        display: none;
    }
</style>
            
            
<?PHP
hapusdata:
    if ($pbolehupload==true)  unlink("fileupload/temp_file/".$_FILES['fileToUpload']['name']);
    mysqli_query($cnmy, "DROP  TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_close($cnmy);
?>