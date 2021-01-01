<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
    
    if ($_GET['module']=="entrybrluarkota" OR $_GET['module']=="entrybrluarkotaotc") $kodepilih="2";
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
    
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    include "config/library.php";
    
    $pnobr=$_GET['brid'];
    
    $query = "SELECT a.*, b.nama nama_cabang, c.nama nama_dokter, d.nama nama_karyawan, e.nama nama_mr, f.nama nama_user, "
            . " g.nama nama_validate, h.nama nama1, i.nama nama2, j.nama nama3, k.nama nama4 "
            . " FROM dbmaster.t_br_cab a LEFT JOIN MKT.icabang b on a.icabangid=b.icabangid "
            . " LEFT JOIN hrd.dokter c on a.dokterid=c.dokterId "
            . " LEFT JOIN hrd.karyawan d on a.karyawanid=d.karyawanId"
            . " LEFT JOIN hrd.karyawan e on a.karyawanid2=e.karyawanId"
            . " LEFT JOIN hrd.karyawan f on a.userid=f.karyawanId "
            . " LEFT JOIN hrd.karyawan g on a.validate=g.karyawanId "
            . " LEFT JOIN hrd.karyawan h on a.atasan1=h.karyawanId "
            . " LEFT JOIN hrd.karyawan i on a.atasan2=i.karyawanId "
            . " LEFT JOIN hrd.karyawan j on a.atasan3=j.karyawanId "
            . " LEFT JOIN hrd.karyawan k on a.atasan4=k.karyawanId "
            . "WHERE a.bridinputcab='$pnobr'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $pkaryawan=$row['karyawanid'];
    $pnamakaryawan=$row['nama_karyawan'];
    $pnamacabang=$row['nama_cabang'];
    $pnamadokter=$row['nama_dokter'];
    $pjumlah=$row['jumlah'];
    
    $pjumlah=number_format($pjumlah,0,",",",");
    
    $paktivitas=$row['aktivitas'];
    
    $pjabatanid=$row['jabatanid'];
    $lvlpengajuan = getfield("select LEVELPOSISI as lcfields from dbmaster.jabatan_level where jabatanId='$pjabatanid'");
    
    
    $puserval=$row['nama_validate'];
    $pnama1=$row['nama1'];
    $pnama2=$row['nama2'];
    $pnama3=$row['nama3'];
    $pnama4=$row['nama4'];
    
    $patasan1=$row['atasan1'];
    $patasan2=$row['atasan2'];
    $patasan3=$row['atasan3'];
    $patasan4=$row['atasan4'];
    
    $ptglatasan1=$row['tgl_atasan1'];
    $ptglatasan2=$row['tgl_atasan2'];
    $ptglatasan3=$row['tgl_atasan3'];
    $ptglatasan4=$row['tgl_atasan4'];
    $ptglvalidate=$row['validate_date'];
    $ptglissued=$row['tglissued'];
    $ptglbooking=$row['tglbooking'];

    if ($ptglvalidate=="0000-00-00") $ptglvalidate="";
    if ($ptglatasan1=="0000-00-00") $ptglatasan1="";
    if ($ptglatasan2=="0000-00-00") $ptglatasan2="";
    if ($ptglatasan3=="0000-00-00") $ptglatasan3="";
    if ($ptglatasan4=="0000-00-00") $ptglatasan4="";
    
    if (!empty($ptglissued)) $ptglissued= date("d/m/Y", strtotime($ptglissued));
    if (!empty($ptglbooking)) $ptglbooking= date("d/m/Y", strtotime($ptglbooking));
                    
    $ptanggal=$row['tgl'];
    $tglajukan=date("d-m-Y", strtotime($ptanggal));
    
    $phari=date("w", strtotime($ptanggal));
    $pdate=date("d", strtotime($ptanggal));
    $pbln=(int)date("m", strtotime($ptanggal));
    $pthn=date("Y", strtotime($ptanggal));

    $tglpengajuan=$seminggu[$phari]." ".$pdate." ".$nama_bln[$pbln]." ".$pthn;

    $gambar_val=$row['validate_gbr'];
    $gambar=$row['gambar'];
    $gbr1=$row['gbr_atasan1'];
    $gbr2=$row['gbr_atasan2'];
    $gbr3=$row['gbr_atasan3'];
    $gbr4=$row['gbr_atasan4'];
    
    
    if ($patasan4==$pkaryawan) $gambar=$gbr4;
    
    
    $milliseconds = round(microtime(true) * 1000);
    $now_fil=date("mdYhis").$milliseconds;

    $namaajkn=$tglajukan;
    $namaspv="";
    $namadm="";
    $namasm="";
    $namagsm="";
    $gmrheight = "80px";
                
    
    if (!empty($gambar)) {
        $data="data:".$gambar;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju="imgbc_".$pnobr."PENGAJU_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
    }

    if (!empty($gbr1)) {
        $data="data:".$gbr1;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namaspv="imgbc_".$pnobr."SVP_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namaspv, $data);
    }

    if (!empty($gbr2)) {
        $data="data:".$gbr2;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namadm="imgbc_".$pnobr."DM_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namadm, $data);
    }
    
    if (!empty($gbr3)) {
        $data="data:".$gbr3;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namasm="imgbc_".$pnobr."SM_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namasm, $data);
    }

    if (!empty($gbr4)) {
        $data="data:".$gbr4;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namagsm="imgbc_".$pnobr."GSM_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namagsm, $data);
    }
    
    /*
    $pnotes_fin="";
    if (empty($ptglissued)) $pnotes_fin="BELUM ISSUED";
    else{
        if (empty($ptglvalidate) OR $ptglvalidate=="0000-00-00") $pnotes_fin="BELUM VALIDATE";
        elseif (empty($ptglatasan4)) $pnotes_fin="BELUM APPROVE";
        elseif (empty($ptglbooking)) $pnotes_fin="BELUM BOOKING";
    }
     * 
     */
    
    $pnotes_fin="";
    if (empty($ptglissued)) $pnotes_fin="BELUM ISSUED";
    if (empty($ptglvalidate) OR $ptglvalidate=="0000-00-00") $pnotes_fin="BELUM VALIDATE";
    if (empty($ptglatasan4)) $pnotes_fin="BELUM APPROVE";
    if (empty($ptglbooking)) $pnotes_fin="BELUM BOOKING";
    
    
    //echo "$tglpengajuan";
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");              
?>

<HTML>
    <HEAD>
        <title>Print Budget Reques DSS/DCC <?PHP echo $printdate." ".$jamnow; ?></title>
        <meta http-equiv="Expires" content="Mon, 01 Sep 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        <link rel="shortcut icon" href="images/icon.ico" />
    </HEAD>
    <BODY>
        
        <?PHP if (!empty($pnotes_fin)) { ?>
            <div class="absolute_txt">
                <div class="text_abs">
                    <?php
                        echo "$pnotes_fin<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "$pnotes_fin<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "$pnotes_fin<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "$pnotes_fin<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "$pnotes_fin<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    ?>
                </div>
            </div>
        <?PHP } ?>
        
        <div id="div1">
            
            <center>
                <h3>
                    <?PHP
                        echo "BUDGET REQUEST";
                    ?>
                </h3>
            </center>
            
            <div id="kotakjudul">
                <div id="isikiri">
                    <table class='tjudul' width='100%'>
                        <?PHP
                        echo "<tr><td nowrap>ID</td><td>:</td><td>$pnobr</td></tr>";
                        echo "<tr><td nowrap>NAMA</td><td>:</td><td>$pnamakaryawan</td></tr>";
                        echo "<tr><td nowrap>TANGGAL</td><td>:</td><td>$tglpengajuan</td></tr>";
                        echo "<tr><td nowrap>CABANG</td><td>:</td><td>$pnamacabang</td></tr>";
                        echo "<tr><td nowrap>DOKTER</td><td>:</td><td>$pnamadokter</td></tr>";
                        echo "<tr><td nowrap>JUMLAH</td><td>:</td><td><b>Rp. $pjumlah</b></td></tr>";
                        echo "<tr><td nowrap valign='top'>NOTES</td><td valign='top'>:</td><td>$paktivitas</td></tr>";
                        ?>
                    </table>
                </div>
                <div id="isikanan">
                    
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            
            
            
            
            <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
            <?PHP
            //echo "Note : $paktivitas";
            ?>
            <br/>&nbsp;<br/>&nbsp;
            
            <br/>&nbsp;<br/>&nbsp;
            <center>
                <?PHP
                if ($lvlpengajuan=="FF1") {
                    echo "<table class='tjudul' width='100%'>";
                        echo "<tr>";
                        
                        if (!empty($namagsm)){
                            echo "<td align='center'>Disetujui :";
                            echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                            echo "<b><u>$pnama4</u></b>";
                            echo "</td>";
                        }else{
                            echo "<td align='center'>Disetujui : <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;(.............................)</td>";
                        }
                        
                        echo "<td align='center'>Diperiksa oleh SM :";
                        if (!empty($namasm)) echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                        else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b><u>$pnama3</u></b>";
                        echo "</td>";
                        
                        if (!empty($pnama2)) {
                            echo "<td align='center'>Diperiksa oleh DM :";
                            if (!empty($namadm)) echo "<br/><img src='images/tanda_tangan_base64/$namadm' height='$gmrheight'><br/>";
                            else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b><u>$pnama2</u></b>";
                            echo "</td>";
                        }
                        
                        if (!empty($pnama1)) {
                            echo "<td align='center'>Diperiksa oleh Atasan :";
                            if (!empty($namasm)) echo "<br/><img src='images/tanda_tangan_base64/$namaspv' height='$gmrheight'><br/>";
                            else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b><u>$pnama1</u></b>";
                            echo "</td>";
                        }
                        
                        echo "<td align='center'>Yang Membuat :";
                        if (!empty($namapengaju)) echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                        else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b><u>$pnamakaryawan</u></b>";
                        echo "</td>";
                        
                        
                        echo "</tr>";
                        
                    echo "</table>";
                }elseif ($lvlpengajuan=="FF2") {
                    if ($patasan2==$patasan3) {
                        echo "<table class='tjudul' width='100%'>";
                            echo "<tr>";

                            if (!empty($namagsm)){
                                echo "<td align='center'>Disetujui :";
                                echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                                echo "<b><u>$pnama4</u></b>";
                                echo "</td>";
                            }else{
                                echo "<td align='center'>Disetujui : <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;(.............................)</td>";
                            }

                            echo "<td align='center'>Diperiksa oleh SM :";
                            if (!empty($namasm)) echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                            else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b><u>$pnama3</u></b>";
                            echo "</td>";

                            echo "<td align='center'>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;</td>";

                            echo "<td align='center'>Yang Membuat :";
                            if (!empty($namapengaju)) echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                            else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b><u>$pnamakaryawan</u></b>";
                            echo "</td>";


                            echo "</tr>";

                        echo "</table>";
                    }else{
                        echo "<table class='tjudul' width='100%'>";
                            echo "<tr>";

                            if (!empty($namagsm)){
                                echo "<td align='center'>Disetujui :";
                                echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                                echo "<b><u>$pnama4</u></b>";
                                echo "</td>";
                            }else{
                                echo "<td align='center'>Disetujui : <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;(.............................)</td>";
                            }

                            echo "<td align='center'>Diperiksa oleh SM :";
                            if (!empty($namasm)) echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                            else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b><u>$pnama3</u></b>";
                            echo "</td>";

                            if (!empty($pnama2)) {
                                echo "<td align='center'>Diperiksa oleh Atasan :";
                                if (!empty($namadm)) echo "<br/><img src='images/tanda_tangan_base64/$namadm' height='$gmrheight'><br/>";
                                else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$pnama2</u></b>";
                                echo "</td>";
                            }

                            echo "<td align='center'>Yang Membuat :";
                            if (!empty($namapengaju)) echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                            else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                            echo "<b><u>$pnamakaryawan</u></b>";
                            echo "</td>";


                            echo "</tr>";

                        echo "</table>";
                    }
                }elseif ($lvlpengajuan=="FF3") {
                    echo "<table class='tjudul' width='100%'>";
                        echo "<tr>";

                        if (!empty($namagsm)){
                            echo "<td align='center'>Disetujui :";
                            echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                            echo "<b><u>$pnama4</u></b>";
                            echo "</td>";
                        }else{
                            echo "<td align='center'>Disetujui : <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;(.............................)</td>";
                        }

                        echo "<td align='center'>Diperiksa oleh Atasan :";
                        if (!empty($namasm)) echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                        else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b><u>$pnama3</u></b>";
                        echo "</td>";

                        echo "<td align='center'>Yang Membuat :";
                        if (!empty($namapengaju)) echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                        else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b><u>$pnamakaryawan</u></b>";
                        echo "</td>";


                        echo "</tr>";

                    echo "</table>";
                }else{
                    
                    echo "<table class='tjudul' width='100%'>";
                        echo "<tr>";

                        if (!empty($namagsm)){
                            echo "<td align='center'>Disetujui :";
                            echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                            echo "<b><u>$pnama4</u></b>";
                            echo "</td>";
                        }else{
                            echo "<td align='center'>Disetujui : <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;(.............................)</td>";
                        }

                        echo "<td align='center'>Yang Membuat :";
                        if (!empty($namapengaju)) echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                        else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b><u>$pnamakaryawan</u></b>";
                        echo "</td>";


                        echo "</tr>";

                    echo "</table>";
                    
                }
                ?>
                
            </center>
            
            
            
        </div>
        
        
        <style>
            .text_abs {
                color:#000;
                padding:50px 0;
                display:block;
                position:relative;
                transform: rotate(-60deg);
                font-size:20px;
                color: rgba(0, 0, 0, 0.5);
            }

            .absolute_txt {
                position:absolute;
                top:6%; left:30%;
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
    </BODY>
</HTML>

<?PHP
    mysqli_close($cnmy);
?>