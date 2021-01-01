<?php
    function BuatFormatNum($prp, $ppilih) {
        if (empty($prp)) $prp=0;
        
        $numrp=$prp;
        if ($ppilih=="1") $numrp=number_format($prp,0,",",",");
        elseif ($ppilih=="2") $numrp=number_format($prp,0,".",".");
            
        return $numrp;
    }
    
    
    session_start();

    if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    include "config/library.php";
    
    $ppilformat="1";
    
    $pidpr=$_GET['brid'];
    
    $gmrheight = "80px";
    
    
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
    
    $pidgroup=$_SESSION['GROUP'];
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpinptpodt00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpinptpodt01_".$puserid."_$now ";
    
    $query = "select idpr, pilihpo, pengajuan, idtipe, tglinput, tanggal, karyawanid, jabatanid, divisi, 
            icabangid, areaid, icabangid_o, areaid_o, aktivitas, aktivitas2, jumlah,
            atasan1, atasan2, atasan3, atasan4,
            tgl_atasan1, tgl_atasan2, tgl_atasan4,
            validate1, validate2, validate3, 
            tgl_validate1, tgl_validate2, tgl_validate3
            from dbpurchasing.t_pr_transaksi WHERE "
            . " IFNULL(stsnonaktif,'')<>'Y' AND idpr='$pidpr'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $ptanggal=$row['tanggal'];
    $ptanggal = date("d F Y", strtotime($ptanggal));
    
    $pket1=$row['aktivitas'];
    $pket2=$row['aktivitas2'];
    $pidkryid=$row['karyawanid'];
    
    $query = "select karyawanid as karyawanid, nama as nama_karyawan from hrd.karyawan WHERE karyawanid='$pidkryid'";
    $tampilk= mysqli_query($cnmy, $query);
    $krow= mysqli_fetch_array($tampilk);
    $pnamakry=$krow['nama_karyawan'];
    
    
    
    
    $query = "select idpr, gambar, gbr_atasan1, gbr_atasan2, gbr_atasan3, gbr_atasan4, gbr_atasan5 from dbttd.t_pr_transaksi_ttd WHERE idpr='$pidpr'";
    $tampilg= mysqli_query($cnmy, $query);
    $grow= mysqli_fetch_array($tampilg);
    
    $namapengaju="";
    $gambar=$grow['gambar'];
    
    if (!empty($gambar)) {
        $data="data:".$gambar;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju="img_".$pidpr."PENGAJUPR_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
    }
    
    
    $namaatasan=""; $nmatasan=""; 
    
?>


<HTML>
<HEAD>
    <title>Purchase Request <?PHP echo $printdate." ".$jamnow; ?></title>
    <meta http-equiv="Expires" content="Mon, 01 Sep 2030 1:00:00 GMT">
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
    </style>

    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            border: 0px solid #000;
        }
        table.example_2 {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            width: 100%;
            border-collapse:
            collapse; border-spacing: 0;
            font-size: 11px;
            border: 1px solid #000;
        }

        table.example_2 td, table.example_2 th {
            border: 1px solid #000; /* No more visible border */
            height: 28px;
            transition: all 0.3s;  /* Simple transition for hover effect */
            padding: 5px;
        }

        table.example_2 th {
            background: #DFDFDF;  /* Darken header a bit */
            font-weight: bold;
        }

        table.example_2 td {
            background: #FAFAFA;
        }

        /* Cells in even rows (2,4,6...) are one color */
        tr:nth-child(even) td { background: #F1F1F1; }

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


        #kotakjudul {
            border: 0px solid #000;
            width:100%;
            height: 1.3cm;
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
        h2 {
            font-size: 13px;
        }
        h3 {
            font-size: 13px;
        }
            

        #tbljudul {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
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
    </style>
</HEAD>


<BODY>
    
    <div id="div1">


        <center>
            <img src="images/logo_sdm.jpg" height="50px">
            <h2>PT SURYA DERMATO MEDICA LABORATORIES</h2>
        </center>
        <hr/>
        <center>
            <h3>
                <?PHP
                echo "PURCHASE REQUEST";
                ?>
            </h3>
        </center>

        <div id="kotakjudul">
            <div id="isikiri">
                <table class='tjudul' width='100%'>
                    <?PHP
                    echo "<tr><td>PR No</td><td>:</td> <td nowrap><b>$pidpr</b></td></tr>";
                    echo "<tr><td>Tanggal</td><td>:</td> <td nowrap>$ptanggal</td></tr>";
                    ?>
                </table>
            </div>
            <div id="isikanan">

            </div>
            <div class="clearfix"></div>
        </div>
        
        <br/>

        
        <table id='example_2' class='table table-striped table-bordered example_2' width="100%" border="1px">
            <thead>
                <tr>
                    <th width='5%px'>No</th>
                    <th width='30%px'>Description of Goods</th>
                    <th width='5%px'>UoM</th>
                    <th width='5%px'>Qty</th>
                    <th width='5%px'>Unit Price</th>
                    <th width='5%px'>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $ptotalbayar=0;
                $no=1;
                $query = "select a.idpr_d, a.idpr, a.idbarang, a.namabarang, 
                    a.idbarang_d, a.spesifikasi1, a.spesifikasi2, 
                    a.jumlah, a.satuan, a.harga 
                    from dbpurchasing.t_pr_transaksi_d as a 
                    where idpr='$pidpr'";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)){
                    
                    $pnamabrg=$row1['namabarang'];
                    $pspesifikasibrg1=$row1['spesifikasi1'];
                    $psatuan=$row1['satuan'];
                    $pqty=$row1['jumlah'];
                    $pharga=$row1['harga'];
                    
                    $ptotalrp=(DOUBLE)$pqty+(DOUBLE)$pharga;
                    $ptotalbayar=(DOUBLE)$ptotalbayar+(DOUBLE)$ptotalrp;
                    
                    $pqty=BuatFormatNum($pqty, $ppilformat);
                    $pharga=BuatFormatNum($pharga, $ppilformat);
                    $ptotalrp=BuatFormatNum($ptotalrp, $ppilformat);
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap><u>$pnamabrg</u><br/>$pspesifikasibrg1</td>";
                    echo "<td nowrap>$psatuan</td>";
                    echo "<td nowrap align='right'>$pqty</td>";
                    echo "<td nowrap align='right'>$pharga</td>";
                    echo "<td nowrap align='right'>$ptotalrp</td>";
                    echo "</tr>";
                    $no++;
                }
                $ptotalbayar=BuatFormatNum($ptotalbayar, $ppilformat);
                
                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap>TOTAL :</td>";
                echo "<td nowrap align='right'>$ptotalbayar</td>";
                echo "</tr>";
                    
                ?>
            </tbody>
        </table>
        
        <br/>
        <?PHP
            echo "Notes : $pket1";
        ?>
        <br/><br/>
        
        <center>
            <table class='tjudul' width='100%'>
                <?PHP
                    echo "<tr>";
                    
                        echo "<td align='center'>";
                        echo "Atasan :";
                        if (!empty($namaatasan)) {
                            echo "<br/><img src='images/tanda_tangan_base64/$namaatasan' height='$gmrheight'><br/>";
                        }else{
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        }
                        echo "<b><u>$nmatasan</u></b>";

                        echo "</td>";
                    
                        echo "<td align='center'>";
                        echo "Yang Membuat :";
                        if (!empty($namapengaju)) {
                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                        }else{
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        }
                        echo "<b><u>$pnamakry</u></b>";

                        echo "</td>";
                        
                    echo "</tr>";
                ?>
            </table>
        </center>
        <br/>
        
    </div>

    
</BODY>


</HTML>
    
<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_close($cnmy);
?>