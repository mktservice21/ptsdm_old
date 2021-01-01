<?php
    $kodepilih="1";
    if ($_GET['module']=="entrybrcash") $kodepilih="3";
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
?>
<html>
    <head>
        <title>Data Cash Advance <?PHP echo $printdate." ".$jamnow; ?></title>
        <meta http-equiv="Expires" content="Mon, 01 Sep 2018 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        <link rel="shortcut icon" href="../../images/icon.ico" />
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
    </head>

    <body>
        <div id="div1">
            <?PHP
                include "config/koneksimysqli.php";
                include "config/fungsi_sql.php";
                include "config/library.php";
                $query = "select * from dbmaster.v_ca0 where idca='$_GET[brid]' order by nama, periode, nama_area";
                $result = mysqli_query($cnmy, $query);
                $row = mysqli_fetch_array($result);
                $idbr=$row['idca'];
                $tglajukan=date("d-m-Y", strtotime($row['tgl']));
                $bulanca=date("F Y", strtotime($row['bulan']));
                //$tgl_idbr=date("Ymd", strtotime($row['tgl']))."-".(int)$idbr;
                $tgl_idbr=$idbr;
                $pkaryawan=$row['karyawanid'];
                $nama=$row['nama'];
                $namaarea=$row['nama_area'];
                $keterangan=$row['keterangan'];
                
                $phari=date("w", strtotime($row['tgl']));
                $pdate=date("d", strtotime($row['tgl']));
                $pbln=(int)date("m", strtotime($row['tgl']));
                $pthn=date("Y", strtotime($row['tgl']));
                
                $tglpengajuan=$seminggu[$phari]." ".$pdate." ".$nama_bln[$pbln]." ".$pthn;
                
                $phari1=date("w", strtotime($row['periode']));
                $pdate1=date("d", strtotime($row['periode']));
                $pbln1=(int)date("m", strtotime($row['periode']));
                $pthn1=date("Y", strtotime($row['periode']));
                
                $pp01=$seminggu[$phari1]." ".$pdate1." ".$nama_bln[$pbln1]." ".$pthn1;
                
                $pdivisi=$row['divisi'];
                $pjabatanid=$row['jabatanid'];
                $lvlpengajuan = getfield("select LEVELPOSISI as lcfields from dbmaster.v_level_jabatan where jabatanId='$pjabatanid'");
                $patasan1=$row['atasan1'];
                $nmatasan1 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan1'");
                $patasan2=$row['atasan2'];
                $nmatasan2 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan2'");
                $patasan3=$row['atasan3'];
                $nmatasan3 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan3'");
                $patasan4=$row['atasan4'];
                $nmatasan4 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan4'");
                
                $gambar=$row['gambar'];
                $gbr1=$row['gbr_atasan1'];
                $gbr2=$row['gbr_atasan2'];
                $gbr3=$row['gbr_atasan3'];
                $gbr4=$row['gbr_atasan4'];
                
                if ($patasan4==$pkaryawan) $gambar=$row['gbr_atasan4'];
                
                $milliseconds = round(microtime(true) * 1000);
                $now_fil=date("mdYhis").$milliseconds;
                
                $namaajkn=$tglajukan;
                $namaspv="";
                $namadm="";
                $namasm="";
                $gmrheight = "80px";
                
                if ($pdivisi=="OTC") {
                    $gambar="";
                    $gbr1="";
                    $gbr2="";
                    $gbr3="";
                    $gbr4="";
                    $lvlpengajuan = "";
                }
                
                if ($lvlpengajuan=="FF6" or $lvlpengajuan=="FF7" or $lvlpengajuan=="FF8" or $lvlpengajuan=="FF9") {
                    $gambar="";
                    $gbr1="";
                    $gbr2="";
                    $gbr3="";
                    $gbr4="";
                    $lvlpengajuan = "";
                }
                
                if (!empty($gambar)) {
                    $data="data:".$gambar;
                    $data=str_replace(' ','+',$data);
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $namapengaju="img_".$idbr."PENGAJUCA_.png";
                    file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
                }
                
                if (!empty($gbr1)) {
                    $data="data:".$gbr1;
                    $data=str_replace(' ','+',$data);
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $namaspv="img_".$idbr."SVPCA_.png";
                    file_put_contents('images/tanda_tangan_base64/'.$namaspv, $data);
                }
                
                if (!empty($gbr2)) {
                    $data="data:".$gbr2;
                    $data=str_replace(' ','+',$data);
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $namadm="img_".$idbr."DMCA_.png";
                    file_put_contents('images/tanda_tangan_base64/'.$namadm, $data);
                }
                
                if (!empty($gbr3)) {
                    $data="data:".$gbr3;
                    $data=str_replace(' ','+',$data);
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $namasm="img_".$idbr."SMCA_.png";
                    file_put_contents('images/tanda_tangan_base64/'.$namasm, $data);
                }
                
                if (!empty($gbr4)) {
                    $data="data:".$gbr4;
                    $data=str_replace(' ','+',$data);
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $namagsm="img_".$idbr."SM_.png";
                    file_put_contents('images/tanda_tangan_base64/'.$namagsm, $data);
                }
            ?>
            
            <center>
                <img src="images/logo_sdm.jpg" height="70px">
                <h2>PT SURYA DERMATO MEDICA LABORATORIES</h2>
            </center>
            <hr/>
            <center>
                <h3>
                    <?PHP
                    if ($kodepilih==1)
                        echo "BIAYA RUTIN";
                    else
                        echo "CASH ADVANCE";
                    ?>
                </h3>
            </center>
            <div id="kotakjudul">
                <div id="isikiri">
                    <table class='tjudul' width='100%'>
                        <tr><td>ID</td><td>:</td><td nowrap><?PHP echo "<b>$tgl_idbr</b>"; ?></td></tr>
                        <tr><td>NAMA</td><td>:</td><td><?PHP echo "$nama"; ?></td></tr>
                        <!--<tr><td>AREA</td><td>:</td><td><?PHP echo "$namaarea"; ?></td></tr>-->
                        <tr><td>PERIODE</td><td>:</td><td><?PHP echo "$bulanca"; ?></td></tr>
                        <?php
                        if ($kodepilih==2){
                            echo "<tr><td>KUNJUNGAN KE KOTA</td><td>:</td><td>$keterangan</td></tr>";
                        }
                        ?>
                    </table>
                </div>
                <div id="isikanan">
                    
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            
            <br/>&nbsp;
            <table id='example_2' class='table table-striped table-bordered example_2' width="100%" border="1px">
                <tbody class='inputdatauc'>
                <?PHP
                $total=0;
                $no=1;
                $tampil = mysqli_query($cnmy, "SELECT nobrid, nama, jumlah, qty FROM dbmaster.t_brid where kode=$kodepilih and aktif='Y' order by nobrid");
                while ($uc=mysqli_fetch_array($tampil)){
                    $nobrid=$uc['nobrid'];
                    $cnobrid="";
                    $ada=0;
                    $tjml=1;
                    if (!empty($uc['jumlah'])) $tjml=$uc['jumlah'];
                    
                    $cari = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_ca1 where idca='$_GET[brid]' and nobrid='$uc[nobrid]'");
                    $ada = mysqli_num_rows($cari);
                    if ($ada>0) {
                        $xx=0;
                        while ($c=mysqli_fetch_array($cari)){
                            $notes=$c['notes'];
                            $rptotal=number_format($c['rptotal'],0);
                            if (!empty($c['rptotal']))
                                $total=$total+$c['rptotal'];
                            
                            $uper1=""; $uper2=""; $ucper="";
                            if (!empty($c['tgl1']) AND $c['tgl1']<>"0000-00-00") {
                                $utgl1=date("w", strtotime($c['tgl1']));
                                $udate1=date("d", strtotime($c['tgl1']));
                                $ubln1=date("m", strtotime($c['tgl1']));
                                $uthn1=date("Y", strtotime($c['tgl1']));
                                $uper1=$udate1."/".$ubln1."/".$uthn1;
                            }
                            
                            if (!empty($c['tgl2']) AND $c['tgl2']<>"0000-00-00") {
                                $utgl2=date("w", strtotime($c['tgl2']));
                                $udate2=date("d", strtotime($c['tgl2']));
                                $ubln2=date("m", strtotime($c['tgl2']));
                                $uthn2=date("Y", strtotime($c['tgl2']));
                                $uper2=$udate2."/".$ubln2."/".$uthn2;
                            }
                            if (!empty($uper1) and !empty($uper2))
                                $ucper="$uper1 S/D. $uper2";
                            elseif (!empty($uper1) and empty($uper2))
                                $ucper="$uper1";
                            
                            if ($nobrid==41) {
                                echo "<tr><td>$no</td><td>$uc[nama]</td><td></td></tr>";    
                                echo "<tr><td></td><td>KOTA : $notes</td><td></td></tr>";    
                                echo "<tr><td></td><td>TANGGAL : $ucper</td><td align='right'>Rp. $rptotal</td></tr>";
                                $no++;
                            }else{
                                if ($nobrid<>$cnobrid){
                                    echo "<tr><td>$no</td><td>$uc[nama] : $notes</b></td><td align='right'>Rp. $rptotal</td></tr>";
                                    $cnobrid=$c['nobrid'];
                                    $no++;
                                } else {
                                    echo "<tr><td></td><td>$notes</b></td><td align='right'>Rp. $rptotal</td></tr>";
                                }
                            }
                            $xx++;
                        }
                        $tjml=(int)$tjml-(int)$xx;
                    }
                    
                    for ($i=1; $i <=$tjml; $i++) {
                        if ($nobrid==41) {
                            echo "<tr><td>$no</td><td>$uc[nama]</td><td></td></tr>";
                            echo "<tr><td></td><td>KOTA : </td><td></td></tr>";
                            echo "<tr><td></td><td>TANGGAL : </td><td></td></tr>";
                            $no++;
                        }else {
                            if ($nobrid<>$cnobrid){
                                echo "<tr><td>$no</td><td>$uc[nama]</td><td></td></tr>";
                                $cnobrid=$nobrid;
                                $no++;
                            } else {
                                echo "<tr><td></td><td></td><td></td></tr>";
                            }
                        }
                    }
                }
                //Total
                $gtotal=number_format($total,0);
                echo "<tr>";
                echo "<td style='border:0px;'></td>";
                echo "<td align='right'>Total  </td>";
                echo "<td align='right'>Rp. $gtotal</td>";
                echo "</tr>";
                
                if ($kodepilih==2) {
                    echo "<tr>";
                    echo "<td style='border:0px;'></td>";
                    echo "<td align='right'>Usulan Uang LK </td>";
                    echo "<td align='right'>Rp. </td>";
                    echo "</tr>";
                    
                    echo "<tr>";
                    echo "<td style='border:0px;'></td>";
                    echo "<td align='right'>Sisa </td>";
                    echo "<td align='right'>Rp. </td>";
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
            <br/>&nbsp;
            <?PHP 
                if ($kodepilih==1)
                    echo "Note : $keterangan";
                else{
                    echo "<div align='right'>$tglpengajuan</div>";
                }
            ?>
            <br/>&nbsp;<br/>&nbsp;
            <center>
                <table class='tjudul' width='100%'>
                    <?PHP
                    if ($lvlpengajuan=="FF1") {
                        echo '
                        <tr>
                            <td align="center">
                                Disetujui :
                                <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
                                (.............................)
                            </td>
                            <td align="center">
                                Diperiksa oleh SM :';
                                if (!empty($namasm))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nmatasan3</u></b>";
                        echo '</td>
                            <td align="center">
                                Diperiksa oleh DM :';
                                if (!empty($namadm))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namadm' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nmatasan2</u></b>";
                        echo '</td>
                            <td align="center">
                                Diperiksa oleh Atasan :';
                                if (!empty($namaspv))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namaspv' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nmatasan1</u></b>";
                        echo '</td>
                            <td align="center">
                                Yang Membuat :';
                                if (!empty($namapengaju))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nama</u></b>";
                        echo '</td>
                        </tr>
                        ';
                    }elseif ($lvlpengajuan=="FF2") {
                        echo '
                        <tr>
                            <td align="center">
                                Disetujui :
                                <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
                                (.............................)
                            </td>
                            <td align="center">
                                Diperiksa oleh SM :';
                                if (!empty($namasm))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nmatasan3</u></b>";
                        echo '</td>
                            <td align="center">
                                Diperiksa oleh Atasan :';
                                if (!empty($namadm))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namadm' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nmatasan2</u></b>";
                        echo '</td>
                            <td align="center">
                                Yang Membuat :';
                                if (!empty($namapengaju))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nama</u></b>";
                        echo '</td>
                        </tr>
                        ';
                    }elseif ($lvlpengajuan=="FF3") {
                        echo '
                        <tr>
                            <td align="center">
                                Disetujui :
                                <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
                                (.............................)
                            </td>
                            <td align="center">
                                Diperiksa oleh Atasan :';
                                if (!empty($namasm))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nmatasan3</u></b>";
                        echo '</td>
                            <td align="center">
                                Yang Membuat :';
                                if (!empty($namapengaju))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nama</u></b>";
                        echo '</td>
                        </tr>
                        ';
                    }elseif ($lvlpengajuan=="FF4") {
                        echo '
                        <tr>
                            <td align="center">
                                Menyetujui :
                                <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
                                (.............................)
                            </td>
                            <td align="center">
                                Diperiksa oleh Atasan :';
                                if (!empty($namagsm))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                if (!empty($nmatasan4))
                                    echo "<b><u>$nmatasan4</u></b>";
                                else
                                    echo "(.............................)";
                        echo '</td>
                            <td align="center">
                                Yang Membuat :';
                                if (!empty($namapengaju))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nama</u></b>";
                        echo '</td>
                        </tr>
                        ';
                    }else{
                        echo '
                        <tr>
                            <td align="center">
                                Menyetujui :
                                <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
                                (.................................)
                            </td>
                            <td align="center">
                                Mengetahui :
                                <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
                                (.................................)
                            </td>
                            <td align="center">
                                Yang Membuat :';
                                if (!empty($namapengaju))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nama</u></b>";
                        echo '</td>
                        </tr>
                        ';
                    }
                    ?>
                </table>
            </center>
        </div>
    </body>
</html>
