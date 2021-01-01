<?php
date_default_timezone_set('Asia/Jakarta');

session_start();

    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG...!!!";
        exit;
    }
    

    $pnamalengkapprint=$_SESSION['NAMALENGKAP'];
    $tgl_print = date("d/m/Y");
    $waktu_print = date("H i s");
    
    
    $pidgrppl="";
    $pidkeluar="";
    if (isset($_GET['inx'])) $pidkeluar=$_GET['inx'];
    if (isset($_GET['igx'])) $pidgrppl=$_GET['igx'];
    
    
    if (empty($pidkeluar) AND empty($pidgrppl)) {
        echo "Tidak ada data yang diprint";
        exit;
    }
    
    include "config/koneksimysqli.php";
    
    
    $pfilteridkeluar="";
    
    if (!empty($pidgrppl)) {
        $query = "select Distinct IDKELUAR from dbmaster.t_barang_keluar_kirim WHERE GRPPRINT='$pidgrppl'";
        $tampil= mysqli_query($cnmy, $query);
        while ($row= mysqli_fetch_array($tampil)) {
            $pidkeluarbrg=$row['IDKELUAR'];
            if (strpos($pfilteridkeluar, $pidkeluarbrg)==false) $pfilteridkeluar .="'".$pidkeluarbrg."',";
        }
    }else{
        $parryid = explode(',', $pidkeluar);
        foreach ($parryid as $pidkeluarbrg) {
            if (!empty($pidkeluarbrg)) {

                if (strpos($pfilteridkeluar, $pidkeluarbrg)==false) $pfilteridkeluar .="'".$pidkeluarbrg."',";

            }
        }
    }

    
    $pfilteridkeluar="(".substr($pfilteridkeluar, 0, -1).")";
    
    
    
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPGMCTPSJB01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPGMCTPSJB02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPGMCTPSJB03_".$userid."_$now ";
    
    
    
    $query = "select DISTINCT 
        b.PILIHAN,a.IDKELUAR,a.TGLINPUT,a.TANGGAL, a.KARYAWANID,e.nama NAMA_KARYAWAN,a.DIVISIID,
        b.DIVISINM,a.ICABANGID,c.nama NAMA_CABANGETH,a.ICABANGID_O,d.nama NAMA_CABANGOTC,
        a.AREAID, g.nama NAMAAREAETH, a.AREAID_O, h.Nama NAMAAREAOTC, 
        a.NOTES,a.USERID,a.STSNONAKTIF,a.SYS_NOW,a.PM_APV,a.PM_TGL,a.APV1,a.APV1_TGL,f.PRINT,
        f.NORESI,f.TGLKIRIM,f.TGLTERIMA, f.NAMA_KARYAWAN NAMA_KARYAWANTERIMA, 
        f.IGROUP, f.IDPENERIMA, f.NAMA_PENERIMA, f.ALAMAT1, f.ALAMAT2, f.KOTA, f.PROVINSI, f.KODEPOS, f.HP 
        from dbmaster.t_barang_keluar a JOIN dbmaster.t_divisi_gimick b on a.DIVISIID=b.DIVISIID LEFT JOIN 
        mkt.icabang c on a.ICABANGID=c.iCabangId
        LEFT JOIN mkt.icabang_o d on a.ICABANGID_O=d.icabangid_o 
        LEFT JOIN hrd.karyawan e on a.KARYAWANID=e.karyawanId 
        LEFT JOIN dbmaster.t_barang_keluar_kirim f on a.IDKELUAR=f.IDKELUAR 
        LEFT JOIN MKT.iarea g on IFNULL(a.AREAID,'')=IFNULL(g.areaId,'') AND a.ICABANGID=g.iCabangId 
        LEFT JOIN MKT.iarea_o h on IFNULL(a.AREAID_O,'')=IFNULL(h.areaid_o,'') AND a.ICABANGID_O=h.icabangid_o 
        WHERE IFNULL(a.STSNONAKTIF,'')<>'Y' 
        AND a.IDKELUAR IN $pfilteridkeluar";
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.IDKELUAR, b.IDKATEGORI, c.NAMA_KATEGORI, a.IDBARANG, b.NAMABARANG, a.STOCK, a.JUMLAH from dbmaster.t_barang_keluar_d a 
        JOIN dbmaster.t_barang b on a.IDBARANG=b.IDBARANG LEFT JOIN dbmaster.t_barang_kategori c on b.IDKATEGORI=c.IDKATEGORI WHERE 
        a.IDKELUAR IN (select IFNULL(IDKELUAR,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select DISTINCT IDPENERIMA, NAMA_PENERIMA, ALAMAT1, ALAMAT2, KOTA, PROVINSI, KODEPOS, HP FROM $tmp01";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $ppnridpenerima=$row['IDPENERIMA'];
    $ppnrnmpenerima=$row['NAMA_PENERIMA'];
    $ppnralamat1=$row['ALAMAT1'];
    $ppnralamat2=$row['ALAMAT2'];
    $ppnrkota=$row['KOTA'];
    $ppnrprovinsi=$row['PROVINSI'];
    $ppnrkdpos=$row['KODEPOS'];
    $ppnrhp=$row['HP'];
    
    $palamat=$ppnralamat1;
    if (!empty($ppnralamat2)) $palamat=$ppnralamat1." ".$ppnralamat2;
    if (!empty($ppnrprovinsi)) $ppnrkota .=", ".$ppnrprovinsi;
    if (!empty($ppnrkdpos)) $ppnrkota .=", ".$ppnrkdpos;
    
?>
<HTML>
<HEAD>
    <TITLE>PRINT SURAT JALAN BARANG GIMMICK</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Sep 2050 1:00:00 GMT">
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
            margin-left: 7mm;  /* this affects the margin in the printer settings */
            margin-right: 7mm;  /* this affects the margin in the printer settings */
            margin-top: 5mm;  /* this affects the margin in the printer settings */
            margin-bottom: 5mm;  /* this affects the margin in the printer settings */
            size: portrait;
        }
        @media all {
            /* .page-break { display: none; } */
        }

        @media print {
            .page-break { display: block; page-break-before: always; }
        }
        </style>
        
        <style>
            body {
                font-family: "Times New Roman", Times, serif;
                font-size: 13px;
                border: 0px solid #000;
            }
            h2 {
                font-size: 15px;
            }
            h3 {
                font-size: 20px;
            }
            

            #tbljudul {
                font-family: "Times New Roman", Times, serif;
                font-size: 20px;
            }
            #tbljudul .tjudul {
                font-size: 13px;
                width: 97%;
            }
            
            #container {
                width:100%;
                text-align:center;
            }

            #left {
                float:left;
                width:40%;
            }
            #right {
                float:right;
                width:40%;
            }
            .clear { clear: both; }
            
            #datatableprntsjb, #datatableprntsjb th, #datatableprntsjb td {
                border : 1px solid black;
                border-collapse: collapse;
                font-family: "Times New Roman", Times, serif;
                padding:5px;               
            }
            #datatableprntsjb th {
                font-size: 16px;
            }
            
            #datatableprntsjb td {
                font-size: 14px;
            }
        </style>
    
    
</HEAD>


<BODY>
    
    <?PHP
    $query = "select DISTINCT IGROUP FROM $tmp01 WHERE IFNULL(IGROUP,'')<>'' ORDER BY IGROUP";
    $tampilhd= mysqli_query($cnmy, $query);
    while ($rhd= mysqli_fetch_array($tampilhd)) {
        $pidgroup=$rhd['IGROUP'];
        
        $query = "select DISTINCT IDPENERIMA, NAMA_PENERIMA, ALAMAT1, ALAMAT2, KOTA, PROVINSI, KODEPOS, HP FROM $tmp01 WHERE IGROUP='$pidgroup'";
        $tampilhd2= mysqli_query($cnmy, $query);
        $rhd2= mysqli_fetch_array($tampilhd2);

        $ppnridpenerima=$rhd2['IDPENERIMA'];
        $ppnrnmpenerima=$rhd2['NAMA_PENERIMA'];
        $ppnralamat1=$rhd2['ALAMAT1'];
        $ppnralamat2=$rhd2['ALAMAT2'];
        $ppnrkota=$rhd2['KOTA'];
        $ppnrprovinsi=$rhd2['PROVINSI'];
        $ppnrkdpos=$rhd2['KODEPOS'];
        $ppnrhp=$rhd2['HP'];

        $palamat=$ppnralamat1;
        if (!empty($ppnralamat2)) $palamat=$ppnralamat1." ".$ppnralamat2;
        if (!empty($ppnrprovinsi)) $ppnrkota .=", ".$ppnrprovinsi;
        if (!empty($ppnrkdpos)) $ppnrkota .=", ".$ppnrkdpos;
        
    ?>
        <div class="page-break">
            
            <div id="container">

                <div id="left">
                    <table id="tbljudul">
                        <tr><td>Kepada :</td></tr>
                        <tr><td><?PHP echo "$ppnrnmpenerima"; ?></td></tr>
                        <tr><td><?PHP echo "$palamat"; ?></td></tr>
                        <tr><td><?PHP echo "$ppnrkota"; ?></td></tr>
                        <tr><td>Hp. <?PHP echo "$ppnrhp"; ?></td></tr>
                    </table>
                </div>

                <div id="right">
                    <table id="tbljudul">
                        <tr><td>Dari :</td></tr>
                        <tr><td>PT. Surya Dermato Medica</td></tr>
                        <tr><td>Jl. Paseban Raya No. 21</td></tr>
                        <tr><td>Senen, Jakarta Pusat, 10440 DKI Jakarta</td></tr>
                        <tr><td>Telp. (021) 3162414</td></tr>
                    </table>
                </div>

            </div>
            <div class="clear"></div>




            <br/>&nbsp;

            <table id='datatableprntsjb' class='table table-striped table-bordered' width='100%'>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>ID</th>
                        <th>Cabang - Area</th>
                        <th>Divisi</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?PHP
                    $no=1;
                    $query = "select * FROM $tmp01 WHERE IGROUP='$pidgroup' ORDER BY DIVISINM, NAMA_CABANGETH, NAMA_CABANGOTC, IDKELUAR";
                    $tampil1= mysqli_query($cnmy, $query);
                    while ($row1= mysqli_fetch_array($tampil1)) {
                        $ppilihanid=$row1['PILIHAN'];
                        $pidkeluar=$row1['IDKELUAR'];
                        $ptgl=$row1['TANGGAL'];
                        $pdivisinm=$row1['DIVISINM'];

                        $pnmcabang=$row1['NAMA_CABANGETH'];
                        $pnmarea=$row1['NAMAAREAETH'];
                        if ($ppilihanid=="OT" OR $ppilihanid=="OTC" OR $ppilihanid=="CHC") {
                            $pnmcabang=$row1['NAMA_CABANGOTC'];
                            $pnmarea=$row1['NAMAAREAOTC'];
                        }
                        if (!empty($pnmarea) AND $pnmarea!=$pnmcabang) {
                            $pnmcabang .=" - $pnmarea";
                        }
                        echo "<tr>";
                        echo "<td nowrap>$no</td>";
                        echo "<td nowrap>$pidkeluar</td>";
                        echo "<td nowrap>$pnmcabang</td>";
                        echo "<td nowrap>$pdivisinm</td>";


                        $ifirst=false;
                        $query = "select * from $tmp02 WHERE IDKELUAR='$pidkeluar' order by NAMA_KATEGORI, NAMABARANG";
                        $tampil2= mysqli_query($cnmy, $query);
                        while ($row2= mysqli_fetch_array($tampil2)) {
                            $pnmkategori=$row2['NAMA_KATEGORI'];
                            $pnmbarang=$row2['NAMABARANG'];
                            $pjml=$row2['JUMLAH'];

                            $pjml=number_format($pjml,0);

                            if ($ifirst==true){
                                echo "<tr>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                                echo "<td nowrap></td>";
                            }
                            //echo "<td nowrap>$pnmkategori</td>";
                            echo "<td nowrap>$pnmbarang</td>";
                            echo "<td nowrap align='right'>$pjml</td>";
                            if ($ifirst==true){
                                echo "</tr>";
                            }



                            $ifirst=true;
                        }
                        $no++;
                        echo "<tr><td colspan='6'>&nbsp;</td></tr>";

                    }
                    ?>
                </tbody>
            </table>
            <br/>&nbsp;
            <div align="right">
                <table>
                    <tr style="font-size: 10px;"><td nowrap><?PHP echo "<i>print by : $pnamalengkapprint $tgl_print $waktu_print</i>"; ?></td></tr>
                </table>
            </div>
            <hr/>
        </div>
    <?PHP
    }
    ?>
        
</BODY>

</HTML>


<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>