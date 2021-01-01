<?php
    // Fungsi header dengan mengirimkan raw data excel
    header("Content-type: application/vnd-ms-excel");

    // Mendefinisikan nama file ekspor "hasil-export.xls"
    header("Content-Disposition: attachment; filename=Rekap Permohonan Dana Budged Request.xls");
    
    
    include "module/lap_br_otcpermohonan/proses_query.php";
    
    $tgl01=$_POST['e_periode01'];
    $periode1= date("Y-m-d", strtotime($tgl01));
    $jenis = $_POST['cb_jenis'];
?>
<html>
<head>
    <title>Rekap Permohonan Dana Budged Request</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <script>
        function printContent(el){
            var restorepage = document.body.innerHTML;
            var printcontent = document.getElementById(el).innerHTML;
            document.body.innerHTML = printcontent;
            window.print();
            document.body.innerHTML = restorepage;
        }
    </script>
</head>

<body>
    <script> window.onload = function() { document.getElementById("e_id").focus(); } </script>
    <!--<button onclick="printContent('div1')">Print</button>-->
<div id="div1">
    <center><h2><u>REKAP DATA PERMOHONAN DANA BR</u></h2></center>
    
    <style>
        @page 
        {
            /*size: auto;   /* auto is the current printer page size */
            margin: 0mm;  /* this affects the margin in the printer settings */
            margin-left: 7mm;  /* this affects the margin in the printer settings */
            margin-right: 7mm;  /* this affects the margin in the printer settings */
            margin-top: 5mm;  /* this affects the margin in the printer settings */
            margin-bottom: 10mm;  /* this affects the margin in the printer settings */
            size: landscape;
        }
        
        @media print {
            #header, #footer { display: none !important; }
            .header, .footer { display: none !important; }
            body{ background-color:#FFFFFF; background-image:none; color:#000000 }
            #ad{ display:none;}
            #leftbar{ display:none;}
            #contentarea{ width:100%;}
            size: landscape;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            border: 0px solid #000;
        }
        table {
            width: 99%;
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
        }
        table.tjudul {
            width: 99%;
            font-size: 13px;
        }
        table.tisi {
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
        }
        table.tisi td {
            padding-top: 5px;
            padding-bottom: 5px;
            padding-left: 2px;
        }
        input.e_id, input.e_i {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            background-color: transparent;
            border: 0px solid #000;
            height: 20px;
        }
        input.e_id {
            width: 25px;
            color: #000;
            text-align: right;
        }
        input.e_i {
            width: 40px;
            color: #000;
        }
        
        #kotakjudul {
            border: 0px solid #000;
            width:100%;
            height: 2.3cm;
        }
        #isikiri {
            float   : left;
            width   : 49%;
            border-left: 0px solid #000;
        }
        #isikanan {
            text-align: right;
            float   : right;
            width   : 49%;
            border-left: 0px solid #000;
        }
    </style>
    
    
    <div id="kotakjudul">
        <div id="isikiri">
            <?PHP
            echo "<table class='tjudul' width='100%'>";
            echo "<tr>";
            echo "<td><b>To : Sdri. Lina (Finance)</b></td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td>&nbsp;</td>";
            echo "</tr>";
            
            echo "<tr>";
            echo "<td><b>Rekap Budget Request (BR) Team OTC</b></td>";
            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td nowrap align='right'>$noslipurut</td>";
            echo "</tr>";
            if ($jenis=="Y") {
                echo "<tr>";
                echo "<td><b>Permintaan Uang Muka</b></td>";
                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td nowrap align='right'>$periode</td>";
                echo "</tr>";
            }else{
                echo "<tr>";
                echo "<td><b>Uang Muka</b></td>";
                echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> <td nowrap align='right'>$periode</td>";
                echo "</tr>";
            }
            echo "<tr>";
            echo "<td>&nbsp;</td>";
            echo "</tr>";
            ?>
            
        </div>
        <div id="isikanan">
            &nbsp;
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>
    
    <table border="1" cellspacing="0" cellpadding="1" class='tisi'>
        <tr style='background-color:#cccccc; font-size: 13px;'>
        <th align="center">Daerah</th>
        <th align="center">Keterangan</th>
        <th align="center">Realisasi</th>
        <th align="center">No.Rek</th>
        <th align="center">Kredit</th>
        <th align="center">No</th>
        </tr>
        <?PHP
        $gtotal=0;
        $no=1;
        $sql = "select distinct icabangid_o, real1, norekreal1, bankreal1 from $tmpbudgetreq01 order by nama_cabang, real1";
        $tampil = mysqli_query($cnit, $sql);
        while ($r = mysqli_fetch_array($tampil)) {
            $cabang1=$r['icabangid_o'];
            $real1=$r['real1'];
            $norek1=$r['norekreal1'];
            $bankrek1=$r['bankreal1'];
            
            $sql2 = "select * from $tmpbudgetreq01 where icabangid_o='$cabang1' AND real1='$real1' AND norekreal1='$norek1' AND bankreal1='$bankrek1'";
            $tampil2 = mysqli_query($cnit, $sql2);
            $sudah="FALSE";
            $jumlahsub=0;
            while ($r2 = mysqli_fetch_array($tampil2)) {
                $cabang=$r2['icabangid_o'];
                $nmcabang=$r2['nama_cabang'];
                $keterangan=$r2['keterangan1'];
                $realisasi=$r2['real1'];
                $norek=$r2['norekreal1'];
                $bankrek=$r2['bankreal1'];
                
                $ketbanknya="";
                if (empty($bankrek) AND empty($norek))
                    $ketbanknya="";
                else
                    $ketbanknya=$bankrek." : ".$norek;
                
                
                $jumlah=0;
                if (!empty($r2['jumlah'])) {
                    $jumlah=number_format($r2['jumlah'],0,",",",");
                    $jumlahsub = (double)$jumlahsub+$r2['jumlah'];
                }
                        
                echo "<tr>";
                echo "<td>$nmcabang</td>";
                echo "<td>$keterangan</td>";
                echo "<td>$realisasi</td>";
                if ($sudah=="FALSE")
                    echo "<td><b>$ketbanknya</b></td>";
                else
                    echo "<td></td>";
                
                echo "<td align='right'>$jumlah</td>";
                if ($sudah=="FALSE") {
                    echo "<td align='center'>$no</td>";
                    $no++;
                }else
                    echo "<td></td>";
                echo "</tr>";
                
                $sudah="TRUE";
                
            }
            $gtotal=(double)$gtotal+(double)$jumlahsub;
            //subtotal
            $jumlahnya=number_format($jumlahsub,0,",",",");
            echo "<tr>";
            echo "<td colspan=4></td>";
            echo "<td align='right'><b>$jumlahnya</b></td>";
            echo "<td>&nbsp;</td>";
            echo "</tr>";
            echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        }
        //echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        //grandtotal
        $gtotalnya=number_format($gtotal,0,",",",");
        echo "<tr style='background-color:#ffcc99;'>";
        echo "<td colspan=2></td>";
        echo "<td colspan=2 align='center'><b>GRAND TOTAL</b></td>";
        echo "<td align='right'><b>$gtotalnya</b></td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";
            
	echo "</table>";
        echo "<br/>&nbsp;";
        
        if ($jenis=="Y") {
            if ($noslipurut=="172/BR-OTC/IX/19") {
                
                echo "<table class='tjudul' >";
                echo "<tr align='center'>";
                echo "<td>Dibuat oleh,</td><td>Mengetahui,</td><td>Disetujui,</td>";
                echo "</tr>";

                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                echo "<tr><td align='center'>$ntgl_apv1</td><td align='center'>$ntgl_apv2</td><td align='center'>$ntgl_apv_dir2</td></tr>";
                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

                echo "<tr align='center'>";
                echo "<td><b>DESI RATNA DEWI</b></td><td><b>SAIFUL RAHMAT</b></td><td><b>IRA BUDISUSETYO</b></td>";
                echo "</tr>";
                echo "</table>";
                
            }else{
                echo "<table class='tjudul' >";
                echo "<tr align='center'>";
                echo "<td>Dibuat oleh,</td><td colspan=2>Mengetahui,</td><td>Disetujui,</td>";
                echo "</tr>";

                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                echo "<tr><td align='center'>$ntgl_apv1</td><td align='center'>$ntgl_apv2</td><td align='center'>$ntgl_apv_dir1</td><td align='center'>$ntgl_apv_dir2</td></tr>";
                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

                echo "<tr align='center'>";
                //echo "<td>DESI RATNA DEWI</td><td>SAIFUL RAHMAT</td><td>FARIDA SOEWANTO</td><td>IRA BUDISUSETYO</td>";
                echo "<td><b>DESI RATNA DEWI</b></td><td><b>SAIFUL RAHMAT</b></td><td><b>FARIDA SOEWANTO</b></td><td><b>IRA BUDISUSETYO</b></td>";
                echo "</tr>";
                echo "</table>";
            }
        }else{
            echo "<table class='tjudul' >";
            echo "<tr align='center'>";
            echo "<td>Dibuat oleh,</td><td>Mengetahui,</td><td>Disetujui,</td>";
            echo "</tr>";

            echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
            echo "<tr><td align='center'>$ntgl_apv1</td><td align='center'>$ntgl_apv2</td><td align='center'>$ntgl_apv_dir1</td></tr>";
            echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
            echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

            echo "<tr align='center'>";
            //echo "<td>DESI RATNA DEWI</td><td>SAIFUL RAHMAT</td><td>FARIDA SOEWANTO</td><td>IRA BUDISUSETYO</td>";
            echo "<td><b>DESI RATNA DEWI</b></td><td><b>SAIFUL RAHMAT</b></td><td><b>FARIDA SOEWANTO</b></td>";
            echo "</tr>";
            echo "</table>";
        }
        ?>
    </table>
    
</div>
    
</body>
</html>