<?PHP include "module/lap_br_otcpermohonan/proses_query.php"; ?>
<html>
    <head>
        <title>Rekap Permohonan Dana Budged Request</title>
        <meta http-equiv="Expires" content="Tue, 01 Sep 2018 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        <link rel="shortcut icon" href="images/icon.ico" />
        <script>
            var EventUtil = new Object;
            EventUtil.formatEvent = function (oEvent) {
                    return oEvent;
            }


            function goto2(pForm_,pPage_) {
               document.getElementById(pForm_).action = pPage_;
               document.getElementById(pForm_).submit();

            }
        </script>
        <style>
        @page 
        {
            /*size: auto;   /* auto is the current printer page size */
            /*margin: 0mm;  /* this affects the margin in the printer settings */
            /*margin-left: 15mm;  /* this affects the margin in the printer settings */
            /*margin-right: 7mm;  /* this affects the margin in the printer settings */
            /*margin-top: 10mm;  /* this affects the margin in the printer settings */
            /*margin-bottom: 10mm;  /* this affects the margin in the printer settings */
            /*size: landscape; /*   */
        }
        </style>
    </head>
    <body>
        <?PHP
        $tgl01=$_POST['e_periode01'];
        $periode1= date("Y-m-d", strtotime($tgl01));
        $jenis = $_POST['cb_jenis'];
        
        
        $iapprovedirut=true;
        
        $snamarpt_judul="Cash Advance";
        if ($pjenis_rpt=="S") {
            $snamarpt_judul="Kasbon Surabaya";
        }elseif ($pjenis_rpt=="B") {
            $snamarpt_judul="Petty Cash Marketing";
            $iapprovedirut=false;
        }elseif ($pjenis_rpt=="K") {
            $snamarpt_judul="Klaim PC-M";
        }else{
            $snamarpt_judul="Cash Advance";
        }
        
        if (!isset($nnama_ss_mktdir)) {
            $nnama_ss_mktdir="FARIDA SOEWANTO";
        }
		
        ?>
        <center><h2><u>REKAP DATA PERMOHONAN DANA BR</u></h2></center>
        
        <script> window.onload = function() { document.getElementById("e_id").focus(); } </script>
        <div id="kotakjudul">
            <div id="isikiri">
                <table class='tjudul' width='100%'>
                    <tr><td><b>To : Sdri. Lina (Finance)</b></td></tr>
                    <tr><td><b>Rekap Budget Request (BR) Team OTC</b></td></tr>
                    
                    <tr><td><b><?PHP echo "$snamarpt_judul"; ?></b></td></tr>
                    <!--
                    <?PHP if ($jenis=="Y") { ?>
                    <tr><td><b>Permintaan Uang Muka</b></td></tr>
                    <?PHP }else{ ?>
                    <tr><td><b>Petty Cash Marketing</b></td></tr>
                    <?PHP } ?>
                    -->
                </table>
            </div>
            <div id="isikanan">
                <table class='tjudul' width='100%'>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td align='right'>
                            <?PHP
                            //echo "<input type='text' placeholder='no' id='e_id' name='e_id' class='e_id'>/$noslipurut/"
                            //        . "<input type='hidden' placeholder='bln/thn' id='e_i' name='e_i' class='e_i'>";
                            echo "$noslipurut";
                            ?>
                        </td>
                    </tr>
                    <tr><td align='right'><?PHP echo $periode; ?></td></tr>
                </table>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    
        <table id='datatable2' class='table table-striped table-bordered example_2'>
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center">Daerah</th>
                <th align="center">Keterangan</th>
                <th align="center">Realisasi</th>
                <th align="center">No.Rek</th>
                <th align="center">Kredit</th>
                <th align="center">No</th>
            </thead>
            <tbody>
                <?PHP
                $adadata=false;
                $gtotal=0;
                $no=1;
                $sql = "select distinct icabangid_o, real1, norekreal1, bankreal1 from $tmpbudgetreq01 order by nama_cabang, real1";
                $tampil = mysqli_query($cnit, $sql);
                while ($r = mysqli_fetch_array($tampil)) {
                    $cabang1=$r['icabangid_o'];
                    $real1=$r['real1'];
                    $norek1=$r['norekreal1'];
                    $bankrek1=$r['bankreal1'];

                    $sql2 = "select * from $tmpbudgetreq01 where icabangid_o='$cabang1' AND real1='$real1' AND norekreal1='$norek1' AND bankreal1='$bankrek1' order by keterangan1";
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
                        echo "<td style='padding-left:5px;'>$nmcabang</td>";
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
                        
                        $adadata=true;
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
                
                if ($noslipurut=="024/BROTC-GAJI/IX/19") {
                    echo "Tanda Tangan Pada Lampiran Manual, karena disistem belum ada ttd pak Asykur. "
                    . "<a href='eksekusi3.php?module=bukafilenya&act=files&id=BR 024 - Gaji SPG Sep 2019.pdf'>Klik di sini</a><br/>";
                }elseif ($noslipurut=="026/BROTC-GAJI/XI/19") {
                    echo "Gaji SPG tambahan periode Oktober, Approve manual. "
                    . "<a href='eksekusi3.php?module=bukafilenya&act=files&id=BR 026 - tambahan gaji okt.pdf'>Klik di sini</a><br/>";
                }
                
                echo "<br/>&nbsp;";
                //if ($jenis=="Y") {
                if ($iapprovedirut==true) {

                    echo "<table class='tjudul' width='100%'>";
                        echo "<tr>";
                        
                            echo "<td align='center'>";
                            echo "Dibuat oleh,";
                            if (!empty($namapengaju_ttd_fin1))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>DESI RATNA DEWI</b></td>";
                             
                            if ($noslipurut!="172/BR-OTC/IX/19") {
                                
                                if ($pjenis_rpt=="G") {//$noslipurut=="026/BROTC-GAJI/XI/19"
                                    
                                    echo "<td align='center'>";
                                    echo "";
                                    if (!empty($namapengaju_ttd_fin2))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b>SAIFUL RAHMAT</b></td>";

                                    echo "<td align='center'>";
                                    echo "Mengetahui,";
                                    if (!empty($namapengaju_ttd_fin3))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin3' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b>M. ASYKUR</b></td>";


                                    echo "<td align='center'>";
                                    echo "";
                                    if (!empty($namapengaju_ttd1))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b>$nnama_ss_mktdir</b></td>";
                                    
                                }else{
                                
                                    echo "<td align='right'>";
                                    echo "";
                                    if (!empty($namapengaju_ttd_fin2))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b>SAIFUL RAHMAT</b></td>";


                                    echo "<td align='center'>";
                                    echo "Mengetahui,";
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "</td>";


                                    echo "<td align='left'>";
                                    echo "";
                                    if (!empty($namapengaju_ttd1))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b>$nnama_ss_mktdir</b></td>";
                                
                                }
                                
                                
                            }else{
                                
                                echo "<td align='center'>";
                                echo "Mengetahui,";
                                if (!empty($namapengaju_ttd_fin2))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b>SAIFUL RAHMAT</b></td>";
                            
                                
                            }
                        
                            echo "<td align='center'>";
                            echo "Disetujui,";
                            if (!empty($namapengaju_ttd2))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd2' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>IRA BUDISUSETYO</b></td>";
                            
                        echo "</tr>";
                        
                    echo "</table>";
                    
                    echo "<br/>&nbsp;<br/>&nbsp;";
                    
                }else{
					
					
					
                    echo "<table class='tjudul' width='100%'>";
                        echo "<tr>";
                        
                            echo "<td align='center'>";
                            echo "Dibuat oleh,";
                            if (!empty($namapengaju_ttd_fin1))
                                echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                            else
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b>DESI RATNA DEWI</b></td>";
                             
                            
                                echo "<td align='center'>";
                                echo "Mengetahui,";
                                if (!empty($namapengaju_ttd_fin2))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin2' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b>SAIFUL RAHMAT</b></td>";

                                echo "<td align='center'>";
                                echo "";
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "</td>";
                            
                            
                                echo "<td align='center'>";
                                echo "Disetujui,";
                                if (!empty($namapengaju_ttd1))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd1' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b>$nnama_ss_mktdir</b></td>";
								
                        echo "</tr>";
                        
                    echo "</table>";
                    
                    echo "<br/>&nbsp;<br/>&nbsp;";
					
					
					
					/*
                    if ($jenis=="Y") {
                        echo "<table width='100%' border='0px' style='border : 0px solid #fff; font-size: 11px; font-weight: bold;'>";
                        echo "<tr align='center'>";
                        echo "<td>Dibuat oleh,</td><td colspan=2>Mengetahui,</td><td>Disetujui,</td>";
                        echo "</tr>";

                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

                        echo "<tr align='center'>";
                        echo "<td>DESI RATNA DEWI</td><td>SAIFUL RAHMAT</td><td>$nnama_ss_mktdir</td><td>IRA BUDISUSETYO</td>";
                        echo "</tr>";
                    }else{
                        echo "<table width='100%' border='0px' style='border : 0px solid #fff; font-size: 11px; font-weight: bold;'>";
                        echo "<tr align='center'>";
                        echo "<td>Dibuat oleh,</td><td>Mengetahui,</td><td>Disetujui,</td>";
                        echo "</tr>";

                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

                        echo "<tr align='center'>";
                        echo "<td>DESI RATNA DEWI</td><td>SAIFUL RAHMAT</td><td>$nnama_ss_mktdir</td>";
                        echo "</tr>";
                    }
					*/
					
					
                }
                ?>
            </tbody>
        </table>
        
        <?PHP
        /*
        if ($adadata==true) {
            $query = "select tglbr from dbmaster.t_otc_norekapdanabr_b WHERE tglbr='$periode1'";
            $tampil= mysqli_query($cnit, $query);
            $ketemu= mysqli_num_rows($tampil);
            if ($ketemu==0) {
                $query = "INSERT INTO dbmaster.t_otc_norekapdanabr_b(tglbr, tno)values('$periode1', '$tpenomoran')";
                mysqli_query($cnit, $query);
            }
            if (!empty($tpenomoran)) {
                mysqli_query($cnit, "UPDATE dbmaster.t_otc_norekapdanabr SET tno='$tpenomoran'");
            }
        }
         * 
         */
        ?>
    
        
    </body>

    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            border: 0px solid #000;
        }
        table.example_2 {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            width: 98%;
            border-collapse:
            collapse; border-spacing: 0;
            font-size: 11px;
            border: 1px solid #000;
        }

        table.example_2 td, table.example_2 th {
            border: 1px solid #000; /* No more visible border */
            height: 25px;
            transition: all 0.3s;  /* Simple transition for hover effect */
        }

        table.example_2 th {
            background: #DFDFDF;  /* Darken header a bit */
            font-weight: bold;
        }

        table.example_2 td {
            background: #FAFAFA;
        }

        /* Cells in even rows (2,4,6...) are one color */
        /*tr:nth-child(even) td { background: #F1F1F1; }*/

        /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
        tr:nth-child(odd) td { background: #FEFEFE; }

        tr td:hover.biasa { background: #666; color: #FFF; }
        tr td:hover.left { background: #ccccff; color: #000; }

        tr td.center1, td.center2 { text-align: center; }

        tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
        tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
        /* Hover cell effect! */
        
        table {
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
        }
        table.tjudul {
            font-size: 13px;
            width: 97%;
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
        }
    </style>
</html>      
<?PHP
    mysqli_query($cnit, "DROP TABLE IF EXISTS $tmpbudgetreq01");
    mysqli_close($cnit);
?>