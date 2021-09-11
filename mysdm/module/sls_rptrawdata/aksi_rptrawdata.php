<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
	
	
	
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Report Raw Data Per Cabang.xls");
    }
    
    $aksi="eksekusi3.php";
    
    include("config/koneksimysqli_ms.php");
    $cnmy=$cnms;
    
    $printdate= date("d/m/Y");
    
    $pmyidcard=$_SESSION['IDCARD'];
    $puser=$_SESSION['USERID'];
    $pmyjabatanid=$_SESSION['JABATANID'];
    $pidsession=$_SESSION['IDSESI'];
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];

    $tgl01=$_POST['bulan1'];
    $tgl02=$_POST['bulan2'];
    $pcab=$_POST['cbcabang'];
    $pidregion=$_POST['cbregion'];
    $piddist=$_POST['cbdistributor'];

    
    $pbln1 = date("Y-m-01", strtotime($tgl01));
    $pbln2 = date("Y-m-t", strtotime($tgl02));
    

    $pperiode1=date("F Y", strtotime($tgl01));
    $pperiode2=date("F Y", strtotime($tgl02));

    $pviewdate=date("d/m/Y H:i:s");
    $ptgltarikan=date("Ymd");

    $query = "select nama from sls.icabang where icabangid='$pcab'";
    $tampil= mysqli_query($cnms, $query);
    $rs= mysqli_fetch_array($tampil);
    $pnamacabang=$rs['nama'];

    $pnmdistirbutor="";
    $query = "select nama from MKT.distrib0 where distid='$piddist'";
    $tampil= mysqli_query($cnms, $query);
    $rd= mysqli_fetch_array($tampil);
    $pnmdistirbutor=$rd['nama'];
    if (empty($piddist)) $pnmdistirbutor="All";


    $milliseconds = round(microtime(true) * 1000);
    $now=date("mdYhis");
    $tmp01 ="dbtemp.tmprptslsrwdt01_".$puser."_$now$milliseconds";
    $tmp02 ="dbtemp.tmprptslsrwdt02_".$puser."_$now$milliseconds";
    $tmp03 ="dbtemp.tmprptslsrwdt03_".$puser."_$now$milliseconds";
   
    
    $query = "select nama from sls.icabang where icabangid='$pcab'";
    $tampil= mysqli_query($cnms, $query);
    $rs= mysqli_fetch_array($tampil);
    $pnamacabang=$rs['nama'];

    $filter_cabang= " ";
    if (!empty($pcab)) {
        $filter_cabang= " AND icabangid='$pcab' ";
    }

    if ($pmyjabatanid=="15") {
        $filter_cabang= " AND CONCAT(icabangid,areaid,divprodid) IN "
                . " (select DISTINCT CONCAT(icabangid,areaid,divprodid) FROM sls.imr0 WHERE karyawanid='$pmyidcard') ";
    }elseif ($pmyjabatanid=="10" OR $pmyjabatanid=="18") {
        $filter_cabang= " AND CONCAT(icabangid,areaid,divprodid) IN "
                . " (select DISTINCT CONCAT(icabangid,areaid,divisiid) FROM sls.ispv0 WHERE karyawanid='$pmyidcard') ";
        if (!empty($pcab)) {
            $filter_cabang .=" AND icabangid='$pcab' ";
        }
    }elseif ($pmyjabatanid=="08") {
        if (empty($pcab)) {
            $filter_cabang= " AND icabangid IN "
                    . " (select DISTINCT icabangid FROM sls.idm0 WHERE karyawanid='$pmyidcard') ";
        }
    }elseif ($pmyjabatanid=="20") {
        if (empty($pcab)) {
            $filter_cabang= " AND icabangid IN "
                    . " (select DISTINCT icabangid FROM sls.ism0 WHERE karyawanid='$pmyidcard') ";
        }
    }else{

    }

    $filter_region="";
    if (!empty($pidregion)) $filter_region=" AND icabangid IN (select distinct icabangid from sls.icabang WHERE region='$pidregion')";


	$pprodoth = "";
	if (isset($_POST['chkboth'])) $pprodoth=$_POST['chkboth'];
    
    $query = "select * from sls.mr_sales2 WHERE tgljual BETWEEN '$pbln1' AND '$pbln2' "
            . " $filter_cabang $filter_region ";
	if ($pprodoth=="Y") {
	}else{
		$query .= " AND iprodid NOT IN (select IFNULL(iprodid,'') iprodid from sls.othproduk WHERE divprodid='PEACO')";
	}
        if (!empty($piddist)) $query .= " AND distid='$piddist' ";
    $query = "CREATE TEMPORARY TABLE $tmp01 ($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    
    $query = "SELECT 
        s.`tgljual`, 
        c.`nama` as namacabang, 
        ar.`nama` as namaarea, 
        icu.`nama` as namacust, 
        s.`divprodid`, 
        ip.`nama` as namaproduk, 
        icu.iSektorId as isektorid, 
        s.`qty`, 
        s.`hna` 
        FROM 
        $tmp01 s 
        LEFT JOIN sls.`icabang` c 
        ON s.`icabangid` = c.`icabangid` 
        LEFT JOIN sls.iarea ar 
        ON s.`icabangid` = ar.`icabangid` 
        AND s.`areaid` = ar.`areaid` 
        LEFT JOIN mkt.`icust` icu 
        ON s.`icustid` = icu.`iCustId` 
        LEFT JOIN sls.`iproduk` ip 
        ON s.`iprodid` = ip.`iprodid`";
    $query = "CREATE TEMPORARY TABLE $tmp03 ($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp03 ADD COLUMN nama_pvt VARCHAR(100)";
    mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "UPDATE $tmp03 as a JOIN MKT.isektor as b on a.isektorid=b.isektorid SET a.nama_pvt=b.nama_pvt";
    mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    
    $query = "SELECT 
        CONCAT('BULAN ',MONTH(`tgljual`),' ',YEAR(`tgljual`)) bulan, 
        namacabang, 
        namaarea, 
        namacust, 
        `divprodid`, 
        namaproduk, 
        nama_pvt, 
        ROUND(SUM(`qty`),0) qty, 
        ROUND(SUM(`qty`*`hna`),0) total 
        FROM 
        $tmp03 
        GROUP BY 1,2,3,4,5,6,7";//ORDER BY 1,2,3,4,5,6
    $query = "CREATE TEMPORARY TABLE $tmp02 ($query)";
    mysqli_query($cnms, $query);
    $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
     
    $query = "DELETE FROM dbmaster.tmp_tarikan_rawdata WHERE DATE_FORMAT(tanggaltarikan,'%Y%m%d')<'$ptgltarikan'";
    mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    
    $query = "DELETE FROM dbmaster.tmp_tarikan_rawdata WHERE userid='$puser' AND idsesi='$pidsession' AND "
            . " DATE_FORMAT(tanggaltarikan,'%Y%m%d')='$ptgltarikan' AND icabangid='$pcab' AND distid='$piddist' AND region='$pidregion' AND "
            . " periode1='$pbln1' AND periode2='$pbln2'";
    mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "ALTER TABLE dbmaster.tmp_tarikan_rawdata AUTO_INCREMENT = 1";
    mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    $query = "INSERT INTO dbmaster.tmp_tarikan_rawdata (bulan, namacabang, namaarea, namacust, divprodid, namaproduk, "
            . " userid, idsesi, periode1, periode2, region, icabangid, "
            . " distid, nama_pvt, "
            . " qty, total) "
            . " SELECT bulan, namacabang, namaarea, namacust, divprodid, namaproduk, "
            . " '$puser' userid, '$pidsession' idsesi, '$pbln1' periode1, '$pbln2' periode2, '$pidregion' region, '$pcab' icabangid, "
            . " '$piddist' as distid, nama_pvt, "
            . " qty, total FROM $tmp02 ORDER BY 1,2,3,4,5,6";
    mysqli_query($cnms, $query); $erropesan = mysqli_error($cnms); if (!empty($erropesan)) { echo "$erropesan"; goto hapusdata; }
    
    
    $pborderpil="";
    if ($ppilihrpt=="excel") {
        $pborderpil=" border='1px' ";
    }
?>


<HTML>
<HEAD>
    <title>Report Raw Data Per Cabang</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        
		
		
		
		<!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    <?PHP } ?>
    <script>
        $(document).ready(function() {
            var etgltarik=document.getElementById('txttgltarik').value;
            var ebln1=document.getElementById('bulan1').value;
            var ebln2=document.getElementById('bulan2').value;
            var eidregi=document.getElementById('cbregion').value;
            var eidcab=document.getElementById('cbcabang').value;
            var eiddist=document.getElementById('cbdistributor').value;
            var euserid=document.getElementById('txtuserid').value;
            var eidsesi=document.getElementById('txtidsesi').value;
            
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");
            var act = urlku.searchParams.get("act");
            var iket = urlku.searchParams.get("ket");
                        
            //alert(eidsesi);
            if (iket=="excel") {
                
            }else{
                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                $.ajax({
                    type:"post",
                    url:"module/sls_rptrawdata/viewdatatable.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                    data:"utgltarik="+etgltarik+"&ubln1="+ebln1+"&ubln2="+ebln2+
                            "&uidregi="+eidregi+"&uidcab="+eidcab+"&uiddist="+eiddist+"&uuserid="+euserid+"&uidsesi="+eidsesi,
                    success:function(data){
                        $("#c-data").html(data);
                        $("#loading").html("");
                    }
                });
            }
        } );
        
        
        function disp_confirm(pText)  {
            //alert(pText); return false;
            var ecabid = document.getElementById("cbcabang").value;
            if (ecabid=="") {
                //alert("cabang harus diisi....");
                //return false;
            }
            if (pText == "excel") {
                document.getElementById("form8").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
                document.getElementById("form8").submit();
                return 1;
            }else{
                document.getElementById("form8").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
                document.getElementById("form8").submit();
                return 1;
            }
        }
        
                
    </script>
</HEAD>
<BODY class="nav-md">
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>

<div id='n_content'>
    
    <?PHP if ($ppilihrpt!="excel") { ?>
        <center><div class='h1judul'>Report Raw Data Per Cabang</div></center>

        <div id="divjudul">
            <table class="tbljudul">
                <tr><td>Cabang</td><td>:</td><td><?PHP echo "$pnamacabang"; ?></td></tr>
                <tr><td>Distributor</td><td>:</td><td><?PHP echo "$pnmdistirbutor"; ?></td></tr>
                <tr><td>Periode</td><td>:</td><td><?PHP echo "$pperiode1 s/d. $pperiode2"; ?></td></tr>
				<?PHP
                    if ($pprodoth=="Y") {
                        echo "<tr><td colspan=3 width='150px'>Include Produk Other Peacock</td></tr>";
                    }else{
                        echo "<tr><td colspan=3 width='150px'>Tanpa Produk Other Peacock</td></tr>";
                    }
				?>
                <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
            </table>
        </div>
        <div class="clearfix"></div>
        <hr/>
    <?PHP } ?>
        
    <form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' id='form8' name='form8' data-parsley-validate class='form-horizontal form-label-left'>
        
        <div class='x_content'>
            
            <?PHP if ($ppilihrpt!="excel") { ?>

                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <h2>
                        <button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>
                    </h2>
                    <div class='clearfix'></div>
                </div>

                <div hidden class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-8' for=''>Input <span class='required'></span></label>
                    <div class='col-md-5'>
                        <div class="form-group">
                            <input type='text' id='txttgltarik' name='txttgltarik' required='required' class='form-control' value='<?PHP echo $ptgltarikan; ?>' Readonly>
                            <input type='text' id='bulan1' name='bulan1' required='required' class='form-control' value='<?PHP echo $pbln1; ?>' Readonly>
                            <input type='text' id='bulan2' name='bulan2' required='required' class='form-control' value='<?PHP echo $pbln2; ?>' Readonly>
                            <input type='text' id='cbregion' name='cbregion' required='required' class='form-control' value='<?PHP echo $pidregion; ?>' Readonly>
                            <input type='text' id='cbcabang' name='cbcabang' required='required' class='form-control' value='<?PHP echo $pcab; ?>' Readonly>
                            <input type='text' id='cbdistributor' name='cbdistributor' required='required' class='form-control' value='<?PHP echo $piddist; ?>' Readonly>
                            <input type='text' id='txtuserid' name='txtuserid' required='required' class='form-control' value='<?PHP echo $puser; ?>' Readonly>
                            <textarea id="txtidsesi" name="txtidsesi" ><?PHP echo $pidsession; ?></textarea>
                        </div>
                    </div>
                </div>
        
            <?PHP } ?>
            
            <div id='loading'></div>
            
            <div id='c-data'>
                <table id='datatablebmsby' class='table table-striped table-bordered' width='100%' <?PHP echo $pborderpil; ?>>
                    <thead>
                        <tr>
                            <th width='30px'>Bulan</th>
                            <th width='100px'>Nama Cabang</th>
                            <th width='10px'>Nama Area</th>
                            <th width='100px'>Nama Cust</th>
                            <th width='100px'>Grp. Sektor</th>
                            <th width='30px'>Divisi</th>
                            <th width='100px'>Nama Produk</th>
                            <th width='50px'>Qty</th>
                            <th width='50px'>Total</th>
                        </tr>
                    </thead>
                    <?PHP
                    if ($ppilihrpt=="excel") {
                        echo "<tbody>";
                        
                        $sql = "SELECT bulan, namacabang, namaarea, namacust, nama_pvt, divprodid, namaproduk, qty, total ";
                        $sql.=" FROM $tmp02 ORDER BY 1,2,3,4,5,6";
                        $tampil= mysqli_query($cnms, $sql);
                        while ($row= mysqli_fetch_array($tampil)) {
                            
                            $pnmbulan=$row['bulan'];
                            $pnmcabang=$row['namacabang'];
                            $pnmarea=$row['namaarea'];
                            $pnmcust=$row['namacust'];
                            $pnmgrppvt=$row['nama_pvt'];
                            $pdivprodid=$row['divprodid'];
                            $pnmproduk=$row['namaproduk'];
                            $pqty=$row['qty'];
                            $ptotal=$row['total'];

                            //$pqty=number_format($pqty,0,",",",");
                            //$ptotal=number_format($ptotal,0,",",",");
                            
                            echo "<tr>";
                            echo "<td nowrap>$pnmbulan</td>";
                            echo "<td nowrap>$pnmcabang</td>";
                            echo "<td nowrap>$pnmarea</td>";
                            echo "<td nowrap>$pnmcust</td>";
                            echo "<td nowrap>$pnmgrppvt</td>";
                            echo "<td nowrap>$pdivprodid</td>";
                            echo "<td nowrap>$pnmproduk</td>";
                            echo "<td nowrap align='right'>$pqty</td>";
                            echo "<td nowrap align='right'>$ptotal</td>";
                            echo "</tr>";
                        }
                        
                        echo "</tbody>";
                    }
                    ?>
                </table>

            </div>

        </div>

    </form>
    
    
    
    <p/>&nbsp;<p/>&nbsp;<p/>&nbsp;
</div>
   
    
    
    
    <?PHP if ($ppilihrpt!="excel") { ?>

        
        <!-- Bootstrap -->
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    
        <!-- Datatables -->
		
        <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
        <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
        <script src="vendors/jszip/dist/jszip.min.js"></script>
        <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
        <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

        
        
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

        <style>
            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 5px 20px 20px 20px;
                /*overflow-x:auto;*/
            }
            
            .h1judul {
              color: blue;
              font-family: verdana;
              font-size: 140%;
              font-weight: bold;
            }
            table.tbljudul {
                font-size : 15px;
            }
            table.tbljudul tr td {
                padding: 1px;
                font-family : "Arial, Verdana, sans-serif";
            }
            .tebal {
                 font-weight: bold;
            }
            .miring {
                 font-style: italic;
            }
            table.tbljudul tr.text2 {
                font-size : 13px;
            }
        </style>
    
        <style>
            
            .divnone {
                display: none;
            }
            #mydatatable1, #mydatatable2 {
                color:#000;
                font-family: "Arial";
            }
            #mydatatable1 th, #mydatatable2 th {
                font-size: 12px;
            }
            #mydatatable1 td, #mydatatable2 td { 
                font-size: 11px;
            }
        </style>
        
        <style>

        table {
            text-align: left;
            position: relative;
            border-collapse: collapse;
            background-color:#FFFFFF;
        }

        th {
            background: white;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }

        .th2 {
            background: white;
            position: sticky;
            top: 35;
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
            border-top: 1px solid #000;
        }
        </style>

    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 140%;
              font-weight: bold;
            }
        </style>
    <?PHP } ?>
        
        
</BODY>



    <script>
        // SCROLL
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
        // END SCROLL
    </script>
    
    
    
</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_close($cnmy);
?>