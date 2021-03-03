<?php
    $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "Anda harus login ulang..."; exit;
    }
    
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $skey="1";
    if (isset($_GET['skey'])) {
        $skey=$_GET['skey'];
    }
    
    
    
    $pnamatext_file="";
    $pjenis=$_POST['cb_untuk'];
    $ptahun=$_POST['cb_tahun'];
    $pfile = $_FILES['fileToUpload']['name'];
    
    echo "<input type='hidden' name='txt_tahun' id='txt_tahun' value='$ptahun'>";
    
    $pnmupload="faktur";
    if ($pjenis=="R") $pnmupload="retur";
    
    $target_dir = "fileupload/slspabrik/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $path = pathinfo($pfile);
    $filename = $path['filename'];
    $ext = $path['extension'];
    $temp_name = $_FILES['fileToUpload']['tmp_name'];
    $path_filename_ext = $target_dir.$filename.".".$ext;
    
    move_uploaded_file($temp_name,$path_filename_ext);
    
    
    $ppath=$target_dir;
    $pnamezip=$filename.".".$ext;

    $filename = basename($ppath.'/'.$pnamezip);
    $filenameWX = preg_replace("/\.[^.]+$/", "", $filename);
    
    
    if ($ext=="xls" OR $ext=="XLS" OR $ext=="xlsx" OR $ext=="XLSX" OR $ext=="csv" OR $ext=="CSV") {
        $filenameWX=$pnamezip;
    }else{
        if ($ext=="rar" OR $ext=="RAR") {
            $archive = RarArchive::open($ppath.$pnamezip);
            $entries = $archive->getEntries();
            foreach ($entries as $entry) {
                $entry->extract($ppath.$filenameWX);
            }
            $archive->close();
        }else{
            $unzip = new ZipArchive;
            $out = $unzip->open($ppath.$pnamezip);
            if ($out === TRUE) {
              $unzip->extractTo(getcwd()."/$ppath/$filenameWX/");
              $unzip->close();
            } else {
              echo 'Error';exit;
            }
        }
    }
    
    //end upload dan extract
    
    unlink("fileupload/slspabrik/$pnmupload.zip");
    
    //require('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
    require('spreadsheet-reader-master/SpreadsheetReader.php');
    
    $Reader = new SpreadsheetReader($target_dir."$pnmupload/$pnmupload.csv");
    
    unset($pinsert_data);//kosongkan array
    $pbolehsave=false;
    $jmlrec=0;
    
	include ("config/koneksimysqli_ms.php");
	
	if ($pjenis=="R") {
	}else{
		//dipindah karean ada error, kebanyakan data 
            if (empty($ptahun)) {
                mysqli_query($cnms, "DELETE FROM sls.pabrik_sales");
            }else{
                mysqli_query($cnms, "DELETE FROM sls.pabrik_sales WHERE YEAR(tglfaktur)='$ptahun'");
            }
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error HAPUS DATA : $erropesan"; exit; }

            mysqli_query($cnms, "ALTER TABLE sls.pabrik_sales AUTO_INCREMENT = 1;");
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error HAPUS DATA : $erropesan"; exit; }
	}
    
    
    foreach ($Reader as $Key => $Row) {
        
        if ($pjenis=="R") {
            $pfile0=trim($Row[0]);
            $pfile1=trim($Row[1]);
            $pfile2=trim($Row[2]);
            $pfile3=trim($Row[3]);
            $pfile4=trim($Row[4]);
            $pfile5=trim($Row[5]);
            $pfile6=trim($Row[6]);
            $pfile7=trim($Row[7]);
            $pfile8=trim($Row[8]);
            $pfile9=trim($Row[9]);
            $pfile10=trim($Row[10]);
            $pfile11=trim($Row[11]);
            $pfile12=trim($Row[12]);
            $pfile13=trim($Row[13]);
            
            if (!empty($pfile10)) $pfile10 = str_replace("'", " ", $pfile10);
            
            $pinsert_data[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', "
                    . " '$pfile9', '$pfile10', '$pfile11', '$pfile12', '$pfile13')";
            
            
            $pbolehsave=true;
        }else{
        
            $pfile0=trim($Row[0]);
            $pfile1=trim($Row[1]);
            $pfile2=trim($Row[2]);
            $pfile3=trim($Row[3]);
            $pfile4=trim($Row[4]);
            $pfile5=trim($Row[5]);
            $pfile6=trim($Row[6]);
            $pfile7=trim($Row[7]);
            $pfile8=trim($Row[8]);
            
            $pfile9=trim($Row[9]);//kuantitas
            $pfile10=trim($Row[10]);//kuantitas_bonus
            $pfile11=trim($Row[11]);//bonus
            $pfile12=trim($Row[12]);//harga
            $pfile13=trim($Row[13]);//disc
            $pfile14=trim($Row[14]);//disc rp
            $pfile15=trim($Row[15]);//Jumlah Rp (Kuantitas * Harga - Bonus Rp - Disc Rp)
            $pfile16=trim($Row[16]);//Disc tambahan dalam % (kalau memang ditentukan % nya kebanyakan langsung Rp)
            $pfile17=trim($Row[17]);//Disc tambahan dalam Rp.
            $pfile18=trim($Row[18]);//Jumlah Netto dalam Rp (Jumlah Rp - Disc tambahan Rp)


            //echo "$pfile0 | $pfile1 | $pfile2 | $pfile3 | $pfile4 | $pfile5 | $pfile6 | $pfile7 | $pfile8 | <br/>";
            //echo "$pfile9 | $pfile10 | $pfile11 | $pfile12 | $pfile13 | $pfile14 | $pfile15 | $pfile16 | $pfile17 | $pfile18 | <br/>";

            $pinsert_data[] = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', "
                    . " '$pfile9', '$pfile10', '$pfile11', '$pfile12', '$pfile13', '$pfile14',"
                    . " '$pfile15', '$pfile16', '$pfile17', '$pfile18')";
			
			
            $pinsert_data_pl = "('$pfile0', '$pfile1', '$pfile2', '$pfile3', "
                    . " '$pfile4', '$pfile5', '$pfile6', '$pfile7', '$pfile8', "
                    . " '$pfile9', '$pfile10', '$pfile11', '$pfile12', '$pfile13', '$pfile14',"
                    . " '$pfile15', '$pfile16', '$pfile17', '$pfile18')";
			
			
			//dipindah karean ada error, kebanyakan data 
            $query_ins_pil = "INSERT INTO sls.pabrik_sales (nofaktur, tglfaktur, kdcustomer, nama_customer, "
                    . " alamat_customer, kota, kdbarang, nama_barang, nobatch, "
                    . " kuantitas, kuantitas_b, bonus, harga, disc_p, disc_rp, "
                    . " jumlahrp, disc_t, disc_tr, jumlah_net) values "
                    . " ".$pinsert_data_pl;
            mysqli_query($cnms, $query_ins_pil);
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error INSERT DATA SALES : $erropesan"; exit; }

            //$pbolehsave=true;
            
        }
        
        
    }
    
    
    
    
    if ($pbolehsave==true) {
        
        if ($pjenis=="R") {
            
            if (empty($ptahun)) {
                mysqli_query($cnms, "DELETE FROM sls.pabrik_retur");
            }else{
                mysqli_query($cnms, "DELETE FROM sls.pabrik_retur WHERE YEAR(tgl_retur)='$ptahun'");
            }
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error HAPUS DATA : $erropesan"; exit; }

            mysqli_query($cnms, "ALTER TABLE sls.pabrik_retur AUTO_INCREMENT = 1;");
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error HAPUS DATA : $erropesan"; exit; }
            
            $query_ins_pil = "INSERT INTO sls.pabrik_retur (bukti_retur, tgl_retur, kdcustomer, nama_customer, "
                    . " alamat_customer, kota, kdbarang, nama_barang, nobatch, "
                    . " kuantitas_r, keterangan, sts, catatan, kirim) values "
                    . " ".implode(', ', $pinsert_data);
            mysqli_query($cnms, $query_ins_pil);
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error INSERT DATA RETUR : $erropesan"; exit; }
            
            
        }else{
            /*
            mysqli_query($cnms, "DELETE FROM sls.pabrik_sales");
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error HAPUS DATA : $erropesan"; exit; }

            mysqli_query($cnms, "ALTER TABLE sls.pabrik_sales AUTO_INCREMENT = 1;");
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error HAPUS DATA : $erropesan"; exit; }

            $query_ins_pil = "INSERT INTO sls.pabrik_sales (nofaktur, tglfaktur, kdcustomer, nama_customer, "
                    . " alamat_customer, kota, kdbarang, nama_barang, nobatch, "
                    . " kuantitas, kuantitas_b, bonus, harga, disc_p, disc_rp, "
                    . " jumlahrp, disc_t, disc_tr, jumlah_net) values "
                    . " ".implode(', ', $pinsert_data);
            mysqli_query($cnms, $query_ins_pil);
            $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { mysqli_close($cnms); echo "Error INSERT DATA SALES : $erropesan"; exit; }
            
			*/
        }
        
    
    }
    
    error_reporting(0);
    unlink($ppath.$filenameWX."/faktur".".csv");
    unlink($ppath.$filenameWX."/retur".".csv");
    error_reporting(-1);
    
    $pjudul="Data Penjualan Pabrik";
    if ($pjenis=="R") {
        $pjudul="Data Retur Pabrik";
    }
    $aksi="module/sls_uploadpabriksls/aksi_uploadpabriksls.php";
?>

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

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="">
    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
        <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                
                <div class='x_content' style="margin-left:-20px; margin-right:-20px;"><!-- -->
                    
                <?PHP
                    if ($pjenis=="R") {
                ?>
                    
                    <script type="text/javascript" language="javascript" >

                        function RefreshDataTabel() {
                            KlikDataTabel();
                        }

                        $(document).ready(function() {
                            KlikDataTabel();
                        } );

                        function KlikDataTabel() {
                            var ket="";
                            var etahun=document.getElementById('txt_tahun').value;

                            $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                            $.ajax({
                                type:"post",
                                url:"module/sls_uploadpabriksls/viewdatatableretur.php?module="+ket+"&utahun="+etahun,
                                data:"eket="+ket+"&utahun="+etahun,
                                success:function(data){
                                    $("#c-data").html(data);
                                    $("#loading").html("");
                                }
                            });
                        }

                    </script>

                    <div id='loading'></div>
                    <div id='c-data'>

                    </div>
                <?PHP
                    }else{
                ?>
                    
                    <script type="text/javascript" language="javascript" >

                        function RefreshDataTabel() {
                            KlikDataTabel();
                        }

                        $(document).ready(function() {
                            KlikDataTabel();
                        } );

                        function KlikDataTabel() {
                            var ket="";
                            var etahun=document.getElementById('txt_tahun').value;

                            $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                            $.ajax({
                                type:"post",
                                url:"module/sls_uploadpabriksls/viewdatatablesales.php?module="+ket+"&utahun="+etahun,
                                data:"eket="+ket+"&utahun="+etahun,
                                success:function(data){
                                    $("#c-data").html(data);
                                    $("#loading").html("");
                                }
                            });
                        }

                    </script>

                    <div id='loading'></div>
                    <div id='c-data'>

                    </div>
                <?PHP
                    }
                ?>
            
        </div>
                
            </div>
        </div>
        
    </div>
</div>


<?PHP
mysqli_close($cnms);
?>


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