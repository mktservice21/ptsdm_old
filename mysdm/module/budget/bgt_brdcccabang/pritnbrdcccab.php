<?php
    session_start();
    
    $pidcard="";
    if (isset($_SESSION['IDCARD'])) $pidcard=$_SESSION['IDCARD'];
    
    if (empty($pidcard)) {
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
    $pidgroup=$_SESSION['GROUP'];
    
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
    
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    include "config/library.php";
    include "config/fungsi_ubahget_id.php";
    
    $pidinput_ec=$_GET['brid'];
    $pidrutin = decodeString($pidinput_ec);
    
    
    $namapengaju="";
    $nmatasan4="";
    
    $namaspv="";
    $namadm="";
    $namasm="";
    $namagsm="";
        
    $gmrheight = "80px";
    
    $pnamakry="";
    $pnamajenis="";
    $pnamauser="";
    $pnamabank="";
    
    $query = "select * from ms2.br where id='$pidrutin'";
    $result = mysqli_query($cnmy, $query);
    $row = mysqli_fetch_array($result);
    
    $ptglinput=$row['tanggal'];
    $pkaryawanid=$row['createdby'];
    $pjenisbr=$row['jenis_br'];
    $piduser=$row['iddokter'];
    $pjumlah=$row['jumlah'];
    $pjumlahreal=$row['jumlah1'];
    $pnmrealisasi=$row['nama_realisasi'];
    $pnomerrek=$row['norek'];
    $pidbank=$row['bank'];
    $pketerangan=$row['keterangan'];
    $pnotes=$row['notes'];
    
    if (!empty($pnotes)) {
        if (!empty($pketerangan)) $pketerangan .=" (".$pnotes.")";
        else $pketerangan=$pnotes;
    }
    
    $pjumlah=number_format($pjumlah,0,",",",");
    $pjumlahreal=number_format($pjumlahreal,0,",",",");
    
    $preject2=$row['rejecteddate_dm'];
    $pidatasan2=$row['approvedby_dm'];
    $ptglatasan2=$row['approveddate_dm'];
    $preject3=$row['rejecteddate_sm'];
    $pidatasan3=$row['approvedby_sm'];
    $ptglatasan3=$row['approveddate_sm'];
    $preject4=$row['rejecteddate_gsm'];
    $pidatasan4=$row['approvedby_gsm'];
    $ptglatasan4=$row['approveddate_gsm'];
    
    if (empty($pidatasan2)) $ptglatasan2="";
    if ($ptglatasan2=="0000-00-00 00:00:00" OR $ptglatasan2=="0000-00-00") $ptglatasan2="";
    if ($ptglatasan3=="0000-00-00 00:00:00" OR $ptglatasan3=="0000-00-00") $ptglatasan3="";
    if ($ptglatasan4=="0000-00-00 00:00:00" OR $ptglatasan4=="0000-00-00") $ptglatasan4="";
    
    if ($preject2=="0000-00-00 00:00:00" OR $preject2=="0000-00-00") $preject2="";
    if ($preject3=="0000-00-00 00:00:00" OR $preject3=="0000-00-00") $preject3="";
    if ($preject4=="0000-00-00 00:00:00" OR $preject4=="0000-00-00") $preject4="";
    
    
    $pstatusreject="";
    if (!empty($preject2)) {
        $pstatusreject="Reject DM";
    }
    
    if (!empty($preject3)) {
        $pstatusreject="Reject SM";
    }
    
    if (!empty($preject4)) {
        $pstatusreject="Reject GSM";
    }
    
    
    $query_ = "select nama as nama_karyawan from hrd.karyawan where karyawanid='$pkaryawanid'";
    $result_ = mysqli_query($cnmy, $query_);
    $row1 = mysqli_fetch_array($result_);
    $pnamakry=$row1['nama_karyawan'];
    
    $query_ = "select namalengkap as nama_user from ms2.masterdokter where id='$piduser'";
    $result_ = mysqli_query($cnmy, $query_);
    $row1 = mysqli_fetch_array($result_);
    $pnamauser=$row1['nama_user'];
    
    $query_ = "select `name` as nama_bank from ms2.bank where `code`='$pidbank'";
    $result_ = mysqli_query($cnmy, $query_);
    $row1 = mysqli_fetch_array($result_);
    $pnamabank=$row1['nama_bank'];
    
    
    $query2 = "select nama as nama_dm from hrd.karyawan where karyawanid='$pidatasan2'";
    $result2 = mysqli_query($cnmy, $query2);
    $row2 = mysqli_fetch_array($result2);
    $nmatasan2=$row2['nama_dm'];
    
    $query3 = "select nama as nama_sm from hrd.karyawan where karyawanid='$pidatasan3'";
    $result3 = mysqli_query($cnmy, $query3);
    $row3 = mysqli_fetch_array($result3);
    $nmatasan3=$row3['nama_sm'];
    
    $query4 = "select nama as nama_gsm from hrd.karyawan where karyawanid='$pidatasan4'";
    $result4 = mysqli_query($cnmy, $query4);
    $row4 = mysqli_fetch_array($result4);
    $nmatasan4=$row4['nama_gsm'];
    
    
    
    $ptglinput=date("d/m/Y", strtotime($ptglinput));
    
    
    if ($pjenisbr=="PCM") $pnamajenis="Cash Advance (PC-M)";
    elseif ($pjenisbr=="ADVANCE") $pnamajenis="Sudah Ada Kuitansi";
    
    
    
    $query1_ = "select `name` as nama_bank from ms2.bank where `code`='$pidbank'";
    $result1_ = mysqli_query($cnmy, $query1_);
    $nrow = mysqli_fetch_array($result1_);
    $pnamabank=$nrow['nama_bank'];
    
    
    
    if (empty($pidatasan2)) {
        $pidatasan2="";
        $nmatasan2="";
        $ptglatasan2="";
    }
    
    if ($pidatasan2==$pidatasan3) {
        $pidatasan2="";
        $nmatasan2="";
        $ptglatasan2="";
    }
    
    if ($pidatasan3==$pidatasan4) {
        $pidatasan3="";
        $nmatasan3="";
        $ptglatasan3="";
    }
    
    if ($pidatasan2==$pkaryawanid) {
        $pidatasan2="";
        $nmatasan2="";
        $ptglatasan2="";
    }
    
    if ($pidatasan3==$pkaryawanid) {
        $pidatasan3="";
        $nmatasan3="";
        $ptglatasan3="";
    }
    
    
    if ($pidatasan4==$pkaryawanid) {
        $pidatasan4="";
        $nmatasan4="";
        $ptglatasan4="";
    }
    
    
    $query_ttd = "SELECT id_br, created, dm, sm, gsm FROM "
            . " ms2.br_sign WHERE id_br='$pidrutin'";
    $tampilttd = mysqli_query($cnmy, $query_ttd);
    $nttd = mysqli_fetch_array($tampilttd);
    
    $gambar=$nttd['created'];
    $gbr2=$nttd['dm'];
    $gbr3=$nttd['sm'];
    $gbr4=$nttd['gsm'];
    
    
    
    if (!empty($gambar)) {
        $data="data:".$gambar;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju="img_".$pidrutin."TTDDCCCB_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
    }
    
    
    if (!empty($gbr2)) {
        $data="data:".$gbr2;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namadm="img_".$pidrutin."TTDDCCCBDM_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namadm, $data);
    }
    
    
    if (!empty($gbr3)) {
        $data="data:".$gbr3;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namasm="img_".$pidrutin."TTDDCCCBSM_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namasm, $data);
    }
    
    if (!empty($gbr4)) {
        $data="data:".$gbr4;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namagsm="img_".$pidrutin."TTDDCCCBGSM_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namagsm, $data);
    }
    
?>


<HTML>
<HEAD>
    <title>Data DCC/DSS Cabang <?PHP echo $printdate." ".$jamnow; ?></title>
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
            font-size: 15px;
        }
        h3 {
            font-size: 20px;
        }
    </style>


</HEAD>
    
<BODY>
    
    <div id="div1">
        
        <center>
            <h3>
                <?PHP
                echo "BUDGET REQUEST";
                ?>
            </h3>
        </center>
        <hr/>
        
        <div id="kotakjudul">
            <div id="isikiri">
                <table class='tjudul' width='100%'>
                    <?PHP
                    if (!empty($pstatusreject)) {
                        echo "<tr><td>STATUS</td><td>:</td> <td nowrap>$pstatusreject</td></tr>";
                    }
                    echo "<tr><td>ID</td><td>:</td> <td nowrap><b>$pidrutin</b></td></tr>";
                    echo "<tr><td>TANGGAL</td><td>:</td> <td nowrap>$ptglinput</td></tr>";
                    echo "<tr><td>NAMA</td><td>:</td> <td nowrap>$pnamakry</td></tr>";
                    echo "<tr><td>JENIS</td><td>:</td> <td nowrap>$pnamajenis</td></tr>";
                    echo "<tr><td>USER</td><td>:</td> <td nowrap>$pnamauser</td></tr>";
                    echo "<tr><td>JUMLAH</td><td>:</td> <td nowrap>$pjumlah</td></tr>";
                    echo "<tr><td>REALISASI</td><td>:</td> <td nowrap>$pnmrealisasi</td></tr>";
                    echo "<tr><td>&nbsp;</td><td>:</td> <td nowrap>$pnamabank ($pnomerrek)</td></tr>";
                    echo "<tr><td>KETERANGAN</td><td>:</td> <td nowrap>$pketerangan</td></tr>";
                    
                    ?>
                </table>
                
            </div>
            <div id="isikanan">

            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <br/>&nbsp; <br/>&nbsp;
        
        <table id='example_2' class='table table-striped table-bordered example_2' width="100%" border="1px">
        </table>
        <br/>&nbsp;<br/>&nbsp;
        <div>
            <center>
                <hr/>
                <table class='tjudul' width='100%'>
                    <?PHP
                        echo "<tr>";

                            if (empty($pidatasan2) AND empty($pidatasan3)) {//atasan langsung GSM
                                echo "<td align='center'>";
                                    echo "Menyetujui :";
                                    if (!empty($namagsm)) {
                                        echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                                    }else{
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    }
                                    echo "<b><u>$nmatasan4</u></b>";
                                echo "</td>";
                            }elseif (empty($pidatasan2) AND !empty($pidatasan3)) {//atasan langsung SM

                                if (!empty($namagsm) AND !empty($pidatasan4)) {
                                    echo "<td align='center'>";
                                        echo "Menyetujui :";
                                        if (!empty($namagsm)) {
                                            echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                                        }else{
                                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                        }
                                        echo "<b><u>$nmatasan4</u></b>";
                                    echo "</td>";
                                }

                                echo "<td align='center'>";
                                    echo "Menyetujui :";
                                    if (!empty($namasm)) {
                                        echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                                    }else{
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    }
                                    echo "<b><u>$nmatasan3</u></b>";
                                echo "</td>";
                            }elseif (!empty($pidatasan2)) {//atasan langsung DM
                                if (!empty($namagsm) AND !empty($pidatasan4)) {
                                    echo "<td align='center'>";
                                        echo "Menyetujui :";
                                        if (!empty($namagsm)) {
                                            echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                                        }else{
                                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                        }
                                        echo "<b><u>$nmatasan4</u></b>";
                                    echo "</td>";
                                }
                                
                                echo "<td align='center'>";
                                    echo "Menyetujui :";
                                    if (!empty($namasm)) {
                                        echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                                    }else{
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    }
                                    echo "<b><u>$nmatasan3</u></b>";
                                echo "</td>";

                                echo "<td align='center'>";
                                    echo "Diperiksa oleh Atasan :";
                                    if (!empty($namadm)) {
                                        echo "<br/><img src='images/tanda_tangan_base64/$namadm' height='$gmrheight'><br/>";
                                    }else{
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    }
                                    echo "<b><u>$nmatasan2</u></b>";
                                echo "</td>";

                            }

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
        </div>
        <br/><br/><br/>
        
        
        
        
    </div>


</BODY>

</HTML>

<?PHP
hapusdata:
    mysqli_close($cnmy);
?>