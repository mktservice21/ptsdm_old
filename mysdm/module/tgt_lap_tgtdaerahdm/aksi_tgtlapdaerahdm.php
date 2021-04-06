<?php
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
        header("Content-Disposition: attachment; filename=Report Target Per Daerah DM.xls");
    }
    
    include("config/koneksimysqli_ms.php");
    $cnmy=$cnms;
    
    $printdate= date("d/m/Y");
    
    $karyawanid=$_SESSION['IDCARD'];
    $ptgl=$_POST['bulan'];
    $pbulan = date("Y-m-01", strtotime($ptgl));
    $date=date_create($pbulan);
    $region=$_POST["cb_region"];
    $cbgytd=$_POST["cb_cabang"];
    
    $namaregion="";
    if ($region=="B"){ $namaregion="Barat";} else if ($region=="T"){$namaregion="Timur";}
    
    
    $query = "SELECT nama FROM ms.cbgytd WHERE idcabang='$cbgytd'";
    $tampil= mysqli_query($cnmy, $query);
    $nr= mysqli_fetch_array($tampil);
    $namacabangytd=$nr['nama'];
    
    if (!empty($namacabangytd)) $namacabangytd=str_replace("DAERAH ","", strtoupper($namacabangytd));
    
    $now=date("mdYhis");
    $puserid=$_SESSION['USERID'];
    $tmp01 =" dbtemp.tmplaptgtdrdm01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmplaptgtdrdm02_".$puserid."_$now ";
    
    $query ="select divprodid, iprodid, hna, sum(qty) qty, sum(value) tvalue from tgt.target1 where "
            . " bulan='$pbulan' and icabangid='$cbgytd' ";
    $query .=" group by 1,2,3";
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query ="select a.*, b.nama nama_produk from $tmp01 a JOIN sls.iproduk b on a.iprodid=b.iprodid";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
?>


<HTML>
<HEAD>
    <title>Report Target Per Daerah DM</title>
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
    
</HEAD>
<BODY class="nav-md">
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>

<div id='n_content'>
    
    <center><div class='h1judul'>Report Target Per Daerah DM</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Daerah</td><td>:</td><td><?PHP echo "$namacabangytd"; ?></td></tr>
            <tr><td>Bulan</td><td>:</td><td><?PHP echo "$ptgl"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
        <tr>
            <th>liniproduk</th>
            <th>kodeproduk</th>
            <th>hna</th>
            <th>namaproduk</th>
            <th>qty</th>
            <th>value</th>
        </tr>
        </thead>
        <tbody>
            <?PHP
                $ptotalqty=0;
                $ptotalval=0;
                $query="select * from $tmp02 order by divprodid, nama_produk, iprodid";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    
                    $pdivpdo=$row['divprodid'];
                    $pidprod=$row['iprodid'];
                    $pnmprod=$row['nama_produk'];
                    
                    $phna=$row['hna'];
                    $pqty=$row['qty'];
                    $pvalue=$row['tvalue'];
                    
                    $ptotalqty=(double)$ptotalqty+(double)$pqty;
                    $ptotalval=(double)$ptotalval+(double)$pvalue;
                    
                    if ($ppilihrpt=="excel") {
                        $phna=number_format($phna,0,"","");
                        $pqty=number_format($pqty,0,"","");
                        $pvalue=number_format($pvalue,0,"","");
                    }else{
                        $phna=number_format($phna,0,",",",");
                        $pqty=number_format($pqty,0,",",",");
                        $pvalue=number_format($pvalue,0,",",",");
                    }
                    

                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$pdivpdo</td>";
                    echo "<td nowrap>$pidprod</td>";
                    echo "<td nowrap align='right'>$phna</td>";
                    echo "<td nowrap>$pnmprod</td>";
                    echo "<td nowrap align='right'>$pqty</td>";
                    echo "<td nowrap align='right'>$pvalue</td>";
                    echo "</tr>";
                    
                }
                
                if ($ppilihrpt=="excel") {
                    $ptotalqty=number_format($ptotalqty,0,"","");
                    $ptotalval=number_format($ptotalval,0,"","");
                }else{
                    $ptotalqty=number_format($ptotalqty,0,",",",");
                    $ptotalval=number_format($ptotalval,0,",",",");
                }
                
                echo "<tr class='tebal'>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap align='right'></td>";
                echo "<td nowrap>Total : </td>";
                echo "<td nowrap align='right'>$ptotalqty</td>";
                echo "<td nowrap align='right'>$ptotalval</td>";
                echo "</tr>";
                    
            ?>
        </tbody>
    </table>
    
    <p/>&nbsp;
    <div class='h1judul'>Summary</div>
    <table id='mydatatable2' class='table table-striped table-bordered' width="50%" border="1px solid black">
        <thead>
        <tr>
            <th>liniproduk</th>
            <th>qty</th>
            <th>value</th>
        </tr>
        </thead>
        <tbody>
            <?PHP
                $ptotalqty=0;
                $ptotalval=0;
                
                $query="select divprodid, sum(qty) qty, sum(tvalue) tvalue from $tmp02 GROUP BY 1 order by divprodid";
                $tampil2= mysqli_query($cnmy, $query);
                while ($row2= mysqli_fetch_array($tampil2)) {
                    
                    $pdivpdo=$row2['divprodid'];
                    $pqty=$row2['qty'];
                    $pvalue=$row2['tvalue'];
                    
                    $ptotalqty=(double)$ptotalqty+(double)$pqty;
                    $ptotalval=(double)$ptotalval+(double)$pvalue;
                    
                    if ($ppilihrpt=="excel") {
                        $pqty=number_format($pqty,0,"","");
                        $pvalue=number_format($pvalue,0,"","");
                    }else{
                        $pqty=number_format($pqty,0,",",",");
                        $pvalue=number_format($pvalue,0,",",",");
                    }
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$pdivpdo</td>";
                    echo "<td nowrap align='right'>$pqty</td>";
                    echo "<td nowrap align='right'>$pvalue</td>";
                    echo "</tr>";
                    
                }
                
                if ($ppilihrpt=="excel") {
                    $ptotalqty=number_format($ptotalqty,0,"","");
                    $ptotalval=number_format($ptotalval,0,"","");
                }else{
                    $ptotalqty=number_format($ptotalqty,0,",",",");
                    $ptotalval=number_format($ptotalval,0,",",",");
                }
                
                echo "<tr class='tebal'>";
                echo "<td nowrap>Total : </td>";
                echo "<td nowrap align='right'>$ptotalqty</td>";
                echo "<td nowrap align='right'>$ptotalval</td>";
                echo "</tr>";
                
                
            ?>
        </tbody>
    </table>
    
    
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
    
    
        $(document).ready(function() {
            
            
            var table1 = $('#mydatatable1').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [2,4,5] },//right
                    { className: "text-nowrap", "targets": [0,1,2,3,4,5] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );
            
            var table = $('#mydatatable2').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [1,2] },//right
                    { className: "text-nowrap", "targets": [0,1,2] }//nowrap

                ],
                bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
                "bPaginate": false
            } );
            

        } );
        
        
        function TambahDataInput(eidbank){
            $.ajax({
                type:"post",
                url:"module/mod_br_danabank/tambah_trans_bank.php?module=viewisibankspdall",
                data:"uidbank="+eidbank,
                success:function(data){
                    $("#myModal").html(data);
                }
            });
        }
    
    
    
    </script>
    
    
    
</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_close($cnmy);
?>