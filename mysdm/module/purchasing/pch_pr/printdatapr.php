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
    
    $query = "select idpr, pilihpo, pengajuan, idtipe, tglinput, tanggal, karyawanid, jabatanid, divisi, iddep, 
            icabangid, areaid, icabangid_o, areaid_o, aktivitas, aktivitas2, jumlah,
            atasan1, atasan2, atasan3, atasan4, atasan5, 
            tgl_atasan1, tgl_atasan2, tgl_atasan3, tgl_atasan4, tgl_atasan5,
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
    $pjabatanid=$row['jabatanid'];
    $pdivisiid=$row['divisi'];
    $pdepid=$row['iddep'];
    $ppengajuan=$row['pengajuan'];
    
    $pptglatasan1=$row['tgl_atasan1'];
    $pptglatasan2=$row['tgl_atasan2'];
    $pptglatasan3=$row['tgl_atasan3'];
    $pptglatasan4=$row['tgl_atasan4'];
    $pptglatasan5=$row['tgl_atasan5'];
    
    if ($pptglatasan1=="0000-00-00 00:00:00") $pptglatasan1="";
    if ($pptglatasan2=="0000-00-00 00:00:00") $pptglatasan2="";
    if ($pptglatasan3=="0000-00-00 00:00:00") $pptglatasan3="";
    if ($pptglatasan4=="0000-00-00 00:00:00") $pptglatasan4="";
    if ($pptglatasan5=="0000-00-00 00:00:00") $pptglatasan5="";
    
    
    $query = "select karyawanid as karyawanid, nama as nama_karyawan from hrd.karyawan WHERE karyawanid='$pidkryid'";
    $tampilk= mysqli_query($cnmy, $query);
    $krow= mysqli_fetch_array($tampilk);
    $pnamakry=$krow['nama_karyawan'];
    
    $query = "select iddep, nama_dep from dbmaster.t_department WHERE iddep='$pdepid'";
    $tampild= mysqli_query($cnmy, $query);
    $drow= mysqli_fetch_array($tampild);
    $pnamadep=$drow['nama_dep'];
    
    
    $patasan1=$row['atasan1'];
    $nmatasan1 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan1'");
    $patasan2=$row['atasan2'];
    $nmatasan2 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan2'");
    $patasan3=$row['atasan3'];
    $nmatasan3 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan3'");
    $patasan4=$row['atasan4'];
    $nmatasan4 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan4'");
    $patasan5=$row['atasan5'];
    $nmatasan5 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan5'");
    
    
    $gambar=""; $gbr1=""; $gbr2=""; $gbr3=""; $gbr4=""; $gbr5="";
    $query = "select * from dbttd.t_pr_transaksi_ttd where idpr='$pidpr'";
    $tampil1=mysqli_query($cnmy, $query);
    $ketemu1= mysqli_num_rows($tampil1);
    if ((INT)$ketemu1>0) {
        $row1= mysqli_fetch_array($tampil1);

        $gambar=$row1['gambar'];
        $gbr1=$row1['gbr_atasan1'];
        $gbr2=$row1['gbr_atasan2'];
        $gbr3=$row1['gbr_atasan3'];
        $gbr4=$row1['gbr_atasan4'];
        $gbr5=$row1['gbr_atasan5'];
    
    }
    
    if (empty($pptglatasan1) OR empty($nmatasan1)) $gbr1="";
    if (empty($pptglatasan2) OR empty($nmatasan2)) $gbr2="";
    if (empty($pptglatasan3) OR empty($nmatasan3)) $gbr3="";
    if (empty($pptglatasan4) OR empty($nmatasan4)) $gbr4="";
    if (empty($pptglatasan5) OR empty($nmatasan5)) $gbr5="";
    
    
    if (!empty($gambar)) {
        $data="data:".$gambar;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju="img_".$pidpr."FCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
    }
    
    if (!empty($gbr1)) {
        $data="data:".$gbr1;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namaspv="img_".$pidpr."SVPFCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namaspv, $data);
    }
    
    if (!empty($gbr2)) {
        $data="data:".$gbr2;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namadm="img_".$pidpr."DMFCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namadm, $data);
    }
    
    if (!empty($gbr3)) {
        $data="data:".$gbr3;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namasm="img_".$pidpr."SMFCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namasm, $data);
    }

    if (!empty($gbr4)) {
        $data="data:".$gbr4;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namagsm="img_".$pidpr."GSMFCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namagsm, $data);
    }
    
    if (!empty($gbr5)) {
        $data="data:".$gbr5;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namaceo="img_".$pidpr."CEOFCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namaceo, $data);
    }
    
    
    
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
                        echo "<td align='center' nowrap>";
                        echo "Yang Membuat :";
                        if (!empty($namapengaju)) {
                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                        }else{
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        }
                        echo "<b><u>$pnamakry</u></b>";
                        
                        echo "</td>";
                        
                        if ($ppengajuan=="HO" OR $ppengajuan=="OTC" OR $ppengajuan=="CHC") {
                            
                            echo "<td align='center' nowrap>";
                            echo "Menyetujui :";
                            if (!empty($namagsm)) {
                                echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                            }else{
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            }
                            if (!empty($nmatasan4)) echo "<b><u>$nmatasan4</u></b>";
                            else echo "..........................................";


                            echo "</td>";
                            
                        }else{
                        
                            if ($pjabatanid=="15" OR $pjabatanid=="38") {

                                if (!empty($nmatasan1)) {
                                    echo "<td align='center' nowrap>";
                                    /*
                                    if (empty($nmatasan2)) echo "Mengetahui :";
                                    else echo "Atasan :";
                                    */
                                    echo "Menyetujui :";

                                    if (!empty($namaspv)) {
                                        echo "<br/><img src='images/tanda_tangan_base64/$namaspv' height='$gmrheight'><br/>";
                                    }else{
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    }
                                    if (!empty($nmatasan1)) echo "<b><u>$nmatasan1</u></b>";
                                    else echo "..........................................";

                                    echo "</td>";
                                }

                            }

                            if ($pjabatanid=="15" OR $pjabatanid=="38" OR $pjabatanid=="10" OR $pjabatanid=="18") {

                                if (!empty($nmatasan2)) {
                                    echo "<td align='center' nowrap>";
                                    /*
                                    if ( ($pjabatanid=="15" OR $pjabatanid=="38") ) echo "Mengetahui :";
                                    else echo "Atasan :";
                                    */
                                    echo "Menyetujui :";

                                    if (!empty($namadm)) {
                                        echo "<br/><img src='images/tanda_tangan_base64/$namadm' height='$gmrheight'><br/>";
                                    }else{
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    }
                                    if (!empty($nmatasan2)) echo "<b><u>$nmatasan2</u></b>";
                                    else echo "..........................................";

                                    echo "</td>";
                                }

                            }

                            if ($pjabatanid=="15" OR $pjabatanid=="38" OR $pjabatanid=="10" OR $pjabatanid=="18" OR $pjabatanid=="08") {

                                echo "<td align='center' nowrap>";
                                /*
                                if ($pjabatanid=="15" OR $pjabatanid=="38") {
                                    echo "Menyetujui :";
                                }else{
                                    if ($pjabatanid=="10" OR $pjabatanid=="18") echo "Mengetahui :";
                                    elseif ($pjabatanid=="08") echo "Mengetahui :";
                                }
                                */
                                echo "Menyetujui :";

                                if (!empty($namasm)) {
                                    echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                                }else{
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                }
                                if (!empty($nmatasan3)) echo "<b><u>$nmatasan3</u></b>";
                                else echo "..........................................";


                                echo "</td>";

                            }

                            if ($pjabatanid=="10" OR $pjabatanid=="18" OR $pjabatanid=="08" OR $pjabatanid=="20") {

                                echo "<td align='center' nowrap>";
                                echo "Menyetujui :";
                                if (!empty($namagsm)) {
                                    echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                                }else{
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                }
                                if (!empty($nmatasan4)) echo "<b><u>$nmatasan4</u></b>";
                                else echo "..........................................";


                                echo "</td>";

                            }

                            if ($pjabatanid=="05" OR $pjabatanid=="22" OR $pjabatanid=="06") {
                                echo "<td align='center' nowrap>";
                                echo "Menyetujui :";
                                if (!empty($namaceo)) {
                                    echo "<br/><img src='images/tanda_tangan_base64/$namaceo' height='$gmrheight'><br/>";
                                }else{
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                }
                                if (!empty($nmatasan5)) echo "<b><u>$nmatasan5</u></b>";
                                else echo "..........................................";


                                echo "</td>";
                            }

                            if ($pjabatanid=="13") {
                                echo "<td align='center' nowrap>";
                                echo "Mengetahui, ";
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "..........................................";


                                echo "</td>";
                                echo "<td align='center' nowrap>";
                                echo "Menyetujui, ";
                                echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "..........................................";


                                echo "</td>";
                            }
                        
                        }
                        
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