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
    
    $pseldivpili1="";
    $pseldivpili2="";
    $pseldivpili3="";
    
    include ("config/koneksimysqli.php");
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPUPSBGTDIV_01".$_SESSION['USERID']."_$now ";
    $tmp02 =" dbtemp.TMPUPSBGTDIV_02".$_SESSION['USERID']."_$now ";
    $tmp03 =" dbtemp.TMPUPSBGTDIV_03".$_SESSION['USERID']."_$now ";
    
        
    $query = "CREATE TABLE $tmp01 (
                nourut MEDIUMINT NOT NULL AUTO_INCREMENT,
                bulan date,
                divisi_pengajuan varchar(5), iddep varchar(10), 
                karyawanid VARCHAR(10) NOT NULL, icabangid VARCHAR(10), icabangid_o VARCHAR(10), 
                transaksi_nama VARCHAR(200), postingid VARCHAR(10), posting_nama varchar(200), kodeid VARCHAR(20), kode_nama VARCHAR(150), coa4 VARCHAR(20), nama_coa4 VARCHAR(150), divisi varchar(5), jumlah DECIMAL(20,2), 
                saldoawal DECIMAL(20,2), jumlah_tambahan DECIMAL(20,2), keterangan VARCHAR(250),
                PRIMARY KEY (nourut)
           )";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error CREATE TABLE : $erropesan"; goto hapusdata; }
    
                                    
    
    $pbolehupload=false;
    
    if ($skey=="1") {
        $pbolehupload=true;
    }
    
    $pname_file="";
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
        $aksi="module/budget/mod_budget_uploaddatabudget/aksi_uploaddatabudget2.php";
        
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
                                                            }elseif ($pdivisipilih=="HO") {
                                                                echo "<option value='HO' $pseldivpili3>HO</option>";
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
                                                            
                                                            $query_dep = "select iddep, nama_dep, aktif, igroup, nama_group from dbmaster.t_department WHERE 1=1 ";
                                                            $query_dep .=" AND IFNULL(aktif,'')<>'N'";
                                                            $query_dep .=" ORDER BY IFNULL(nama_group,''), nama_dep";

                                                            if (!empty($query_dep)) {
                                                                $tampil = mysqli_query($cnmy, $query_dep);
                                                                $ketemu= mysqli_num_rows($tampil);
                                                                if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";

                                                                while ($z= mysqli_fetch_array($tampil)) {
                                                                    $pdepid=$z['iddep'];
                                                                    $pdepnm=$z['nama_dep'];
                                                                    $pdepgrpid=$z['igroup'];
                                                                    $pdepgrpnm=$z['nama_group'];

                                                                    if (!empty($pdepgrpnm) AND $pdepgrpid=="1") {
                                                                        $pdepnm .=" - (".$pdepgrpnm.")";
                                                                    }

                                                                    if ($pdepid==$pdepartemen)
                                                                        echo "<option value='$pdepid' selected>$pdepnm</option>";
                                                                    else
                                                                        echo "<option value='$pdepid'>$pdepnm</option>";

                                                                }
                                                            }else{
                                                                echo "<option value='' selected>-- Pilih --</option>";
                                                            }
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
                            $piddepadayangkosong=false;
                            if ($pbolehupload==true) {
                                // upload file xls
                                $target = basename($_FILES['fileToUpload']['name']) ;
                                $temp_file = explode(".", $target);
                                $pname_file = RTRIM($temp_file[0]);
                                $pname_file = $pname_file."_".round(microtime(true)).".".RTRIM($temp_file[1]);
                                move_uploaded_file($_FILES['fileToUpload']['tmp_name'], "fileupload/temp_file/".$pname_file);

                                $_SESSION['BGTUPDFIL']=$pname_file;
                                // beri permisi agar file xls dapat di baca
                                chmod("fileupload/temp_file/".$pname_file,0777);


                                $objPHPExcel = PHPExcel_IOFactory::load("fileupload/temp_file/".$pname_file);
                                
                                $psudhupload=false;
                                $jmlrec=0;
                                foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){
                                    $totalrow = $worksheet->getHighestRow();
                                    $jmlrec=0;
                                    
                                    $psimpandata=false;
                                    unset($pinsert_data_detail);//kosongkan array
                                    
                                    if ($psudhupload == true) continue;
                                    
                                    for($row=6; $row<=$totalrow; $row++){
                                        $pfile0 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(0, $row)->getValue());
                                        $pfile1 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(1, $row)->getValue());
                                        $pfile2 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(2, $row)->getValue());
                                        $pfile3 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(3, $row)->getValue());
                                        $pfile4 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(4, $row)->getValue());
                                        $pfile5 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(5, $row)->getValue());
                                        $pfile6 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(6, $row)->getValue());
                                        $pfile7 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(7, $row)->getValue());
                                        $pfile8 = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow(8, $row)->getValue());
                                        
                                        
                                        if (empty($pfile0) AND empty($pfile1) AND empty($pfile2) and empty($pfile3) and empty($pfile4) and empty($pfile5) and empty($pfile6)) {
                                            continue;
                                        }
                                        
                                        if (!empty($pfile0)) $pfile0 = str_replace("'", "", $pfile0);
                                        if (!empty($pfile0)) $pfile0 = str_replace("*", "", $pfile0);
                                        
                                        if (!empty($pfile1)) $pfile1 = str_replace("'", "", $pfile1);
                                        if (!empty($pfile1)) $pfile1 = str_replace("*", "", $pfile1);
                                        
                                        if (!empty($pfile2)) $pfile2 = str_replace("'", "", $pfile2);
                                        if (!empty($pfile2)) $pfile2 = str_replace("*", "", $pfile2);
                                        
                                        if (!empty($pfile3)) $pfile3 = str_replace("'", "", $pfile3);
                                        if (!empty($pfile3)) $pfile3 = str_replace("*", "", $pfile3);
                                        
                                        if (!empty($pfile4)) $pfile4 = str_replace("'", "", $pfile4);
                                        if (!empty($pfile4)) $pfile4 = str_replace("*", "", $pfile4);
                                        
                                        if (!empty($pfile5)) $pfile5 = str_replace("'", "", $pfile5);
                                        if (!empty($pfile5)) $pfile5 = str_replace("*", "", $pfile5);
                                        
                                        if (!empty($pfile6)) $pfile6 = str_replace("'", "", $pfile6);
                                        if (!empty($pfile6)) $pfile6 = str_replace("*", "", $pfile6);
                                        
                                        if (!empty($pfile7)) $pfile7 = str_replace("'", "", $pfile7);
                                        if (!empty($pfile7)) $pfile7 = str_replace("*", "", $pfile7);
                                        
                                        if (!empty($pfile8)) $pfile8 = str_replace("'", "", $pfile8);
                                        if (!empty($pfile8)) $pfile8 = str_replace("*", "", $pfile8);
                                        
                                        //if (!empty($pfile15)) $pfile15 = str_replace("'", "", $pfile15);
                                        //if (!empty($pfile15)) $pfile15 = str_replace(" ", "", $pfile15);
                                        //if (!empty($pfile15)) $pfile15 = str_replace("*", "", $pfile15);
                                        //if (!empty($pfile15)) $pfile15 = str_replace(",","", $pfile15);
                                        
                                        //echo "$pfile0, $pfile1, $pfile2, $pfile3, $pfile4, $pfile5, $pfile6, $pfile7, $pfile8<br/>";
                                        
                                        if ($pdepartemen<>$pfile0) {
                                            $piddepadayangkosong=true;
                                            continue;
                                        }
                                        
                                        if (empty($pdepartemen) OR empty($pfile0)) {
                                            $piddepadayangkosong=true;
                                            continue;
                                        }
                                        
                                        $pmulaifield=9;
                                        for ($ifield=1; $ifield<=12; $ifield++) {
                                            
                                            $ppilihbulan=$ptahunpilih."-0".$ifield."-01";
                                            if (strlen($ifield)>1) $ppilihbulan="2022-".$ifield."-01";
                                            
                                            $pfile_jml = mysqli_real_escape_string($cnmy, $worksheet->getCellByColumnAndRow($pmulaifield, $row)->getValue());
                                            if (empty($pfile_jml)) $pfile_jml=0;
                                            
                                            if (!empty($pfile_jml)) $pfile_jml = str_replace("'", "", $pfile_jml);
                                            if (!empty($pfile_jml)) $pfile_jml = str_replace(" ", "", $pfile_jml);
                                            if (!empty($pfile_jml)) $pfile_jml = str_replace("*", "", $pfile_jml);
                                            if (!empty($pfile_jml)) $pfile_jml = str_replace(",","", $pfile_jml);
                                            
                                            
                                            //echo "$ifield. - bln : $ppilihbulan, $pfile0, $pfile1, $pfile2, $pfile_jml, TTL : $pfile15 : $pdivisipilih, $pkaryawanid, $pdepartemen, $pcabangid<br/>";
                                            
                                            $pdepartemen=$pfile0;
                                            $pinsert_data_detail[] = "('$ppilihbulan', '$pdivisipilih', '$pkaryawanid', '$pcabangid', '$pcabangid', '$pdepartemen', "
                                                    . " '$pfile1', '$pfile2', '$pfile3', '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', '$pfile_jml')";
                                            $psimpandata=true;
                                            $psudhupload=true;
                                            
                                            $pmulaifield++;
                                            $pfile_jml=0;
                                        }
                                        
                                        
                                    }
                                    
                                    
                                }
                                
                                if ($piddepadayangkosong == true) {
                                    echo "GAGAL....<br/>Departemen ada yang kosong, atau file dan departemen yang dipilih tidak sesuai...";
                                    $psimpandata=false;
                                }
                                
                                if ($psimpandata==true) {
                                    
                                    $query_detail="INSERT INTO $tmp01 (bulan, divisi_pengajuan, karyawanid, icabangid, icabangid_o, iddep, "
                                            . " transaksi_nama, postingid, posting_nama, kodeid, kode_nama, divisi, coa4, nama_coa4, jumlah) VALUES ".implode(', ', $pinsert_data_detail);
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
                                            . " AND divisi_pengajuan='$pdivisipilih' AND IFNULL(iddep,'')='$pdepartemen' "
                                            . " AND karyawanid='$pkaryawanid' AND IFNULL($pketicab,'')='$pcabangid'";
                                    $pinsertdetail = mysqli_query($cnmy, $query_detail);
                                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { mysqli_close($cnmy); echo "Error DELETE TABLE : $erropesan"; goto hapusdata; }
                                    
                                    mysqli_query($cnmy, "ALTER TABLE dbmaster.t_budget_divisi AUTO_INCREMENT = 1");
                                    
                                    $query = "INSERT INTO dbmaster.t_budget_divisi (bulan, divisi_pengajuan, karyawanid, icabangid, icabangid_o, iddep, "
                                            . " postingid, kodeid, divisi, coa4, jumlah, userid)"
                                            . "SELECT bulan, divisi_pengajuan, karyawanid, icabangid, icabangid_o, iddep, "
                                            . " postingid, kodeid, divisi, coa4, jumlah, '$puserid' as userid FROM $tmp01 "
                                            . " ORDER BY postingid, kodeid, divisi, coa4, bulan";
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
                                            <th align="center" nowrap>Transasksi</th>
                                            <th align="center" nowrap>Posting Id</th>
                                            <th align="center" nowrap>Posting Nama</th>
                                            <th align="center" nowrap>Kode Id</th>
                                            <th align="center" nowrap>Kode Nama</th>
                                            <th align="center" nowrap>Divisi</th>
                                            <th align="center" nowrap>COA</th>
                                            <th align="center" nowrap>Nama COA</th>
                                            <th align="center" nowrap>Bulan</th>
                                            <th align="center" nowrap>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?PHP
                                        $no=1;
                                        $ptotal=0;
                                        $ptotalcoa=0;
                                        $query = "select DISTINCT postingid, posting_nama from $tmp01 order by postingid, posting_nama";
                                        $tampil1= mysqli_query($cnmy, $query);
                                        $ketemu1= mysqli_num_rows($tampil1);
                                        if ($ketemu1>0) {
                                            while ($row1= mysqli_fetch_array($tampil1)) {
                                                $npostingid=$row1['postingid'];
                                                $nnamaposting=$row1['posting_nama'];
                                                
                                                $ptotalcoa=0;
                                                $query = "select * from $tmp01 WHERE postingid='$npostingid' order by transaksi_nama, kodeid, kode_nama, coa4, nama_coa4, bulan";
                                                $tampil= mysqli_query($cnmy, $query);
                                                $ketemu= mysqli_num_rows($tampil);
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $nbulan=$row['bulan'];
                                                    $njml=$row['jumlah'];
                                                    
                                                    $nnmtransasksi=$row['transaksi_nama'];
                                                    $nkodeid=$row['kodeid'];
                                                    $nkodenm=$row['kode_nama'];
                                                    $ndivisi=$row['divisi'];
                                                    $ncoa=$row['coa4'];
                                                    $nnmcoa=$row['nama_coa4'];

                                                    $nbulan = date("F Y", strtotime($nbulan));
                                                    
                                                    $ptotalcoa=(double)$ptotalcoa+(double)$njml;
                                                    $ptotal=(double)$ptotal+(double)$njml;
                                                    $njml=number_format($njml,0,",",",");


                                                    echo "<tr>";
                                                    echo "<td nowrap>$no</td>";
                                                    echo "<td nowrap>$nnmtransasksi</td>";
                                                    echo "<td nowrap>$npostingid</td>";
                                                    echo "<td nowrap>$nnamaposting</td>";
                                                    echo "<td nowrap>$nkodeid</td>";
                                                    echo "<td nowrap>$nkodenm</td>";
                                                    echo "<td nowrap>$ndivisi</td>";
                                                    echo "<td nowrap>$ncoa</td>";
                                                    echo "<td nowrap>$nnmcoa</td>";
                                                    echo "<td nowrap>$nbulan</td>";
                                                    echo "<td nowrap align='right'>$njml</td>";
                                                    echo "</tr>";

                                                    $no++;
                                                }
                                                
                                                $ptotalcoa=number_format($ptotalcoa,0,",",",");

                                                echo "<tr style='font-weight:bold;'>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap>TOTAL $npostingid - $nnamaposting : </td>";
                                                echo "<td nowrap></td>";
                                                echo "<td nowrap align='right'>$ptotalcoa</td>";
                                                echo "</tr>";
                                                
                                            }
                                            
                                            $ptotal=number_format($ptotal,0,",",",");

                                            echo "<tr style='font-weight:bold;'>";
                                            echo "<td nowrap></td>";
                                            echo "<td nowrap></td>";
                                            echo "<td nowrap></td>";
                                            echo "<td nowrap></td>";
                                            echo "<td nowrap></td>";
                                            echo "<td nowrap></td>";
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
    //if ($pbolehupload==true)  unlink("fileupload/temp_file/".$pname_file);
    mysqli_query($cnmy, "DROP  TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_close($cnmy);
?>