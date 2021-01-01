<?PHP
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
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
    
    $pkodeid=$row['kode'];
    $pkaryawan=$row['karyawanid'];
    $pnamakaryawan=$row['nama_karyawan'];
    $pnamacabang=$row['nama_cabang'];
    $pnamadokter=$row['nama_dokter'];
    $pjumlah=$row['jumlah'];
    
    $pjumlah=number_format($pjumlah,0,",",",");
    
    $pketerangan=$row['aktivitas'];
    
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
    
    
    
    $pnotes_fin="";
    if (empty($ptglissued)) $pnotes_fin="BELUM ISSUED";
    if (empty($ptglvalidate) OR $ptglvalidate=="0000-00-00") $pnotes_fin="BELUM VALIDATE";
    if (empty($ptglatasan4)) $pnotes_fin="BELUM APPROVE";
    if (empty($ptglbooking)) $pnotes_fin="BELUM BOOKING";
    
    $ptotalrp=0;
    $paktivitas="<table border='0px' width='100%' class='tbl_aktv'>";
    
    if (!empty($pnamadokter)) {
        $paktivitas .="<tr>";
        $paktivitas .="<td valign='TOP'>&nbsp;</td>";
        $paktivitas .="<td>";
        $paktivitas .="<b>Dokter : $pnamadokter</b>";
        $paktivitas .="</td>";
        $paktivitas .="</tr>";
    }
    
    $query = "SELECT a.*, b.nama_agency "
            . " FROM dbmaster.t_br_cab1 a LEFT JOIN dbmaster.t_agency b on a.id_agency=b.id_agency "
            . " WHERE a.bridinputcab='$pnobr'";
    $tampildet= mysqli_query($cnmy, $query);
    while($rsd= mysqli_fetch_array($tampildet)){
        $pnoid=$rsd['noid'];
        
        if ($pnoid=="01") {
            $pjnspiltiket=$rsd['jenistiket'];
            $ptujuandari=$rsd['kota1'];
            $ptujuanke=$rsd['kota2'];
            $ptgltiket1=$rsd['tgl1'];
            $pjamtiket1=$rsd['jam1'];
            $pketpergi=$rsd['notes'];
            $pnmagency=$rsd['nama_agency'];
            $pjenisbayarhotel=$rsd['stsbayar'];
            $phargarp1=$rsd['rp'];
            
            $ptotalrp=(double)$ptotalrp+(double)$phargarp1;
            
            $pjenistiket="";
            if ($pjnspiltiket=="K") $pjenistiket="KAI";
            elseif ($pjnspiltiket=="P") $pjenistiket="PESAWAT";
            
            $ptgltiket1 = date('d M Y', strtotime($ptgltiket1));
            
            $phargarp1=number_format($phargarp1,0,",",",");
            
            $pnmketnya1 ="Tiket $pjenistiket Pergi ke $ptujuandari - $ptujuanke Tgl. $ptgltiket1 Jam $pjamtiket1";
            $pnmrealisasi1 ="Agency : $pnmagency";
            
            $paktivitas .="<tr>";
            $paktivitas .="<td valign='TOP'><b> - </b></td>";
            $paktivitas .="<td>";
            $paktivitas .="$pnmketnya1<br/>$pnmrealisasi1";
            $paktivitas .="<br/>Harga Rp. : $phargarp1";
            if (!empty($pketpergi)) $paktivitas .="<br/>Note : $pketpergi";
            $paktivitas .="</td>";
            $paktivitas .="</tr>";
            
            
        }
        
        if ($pnoid=="02") {
            $pjnspiltiket=$rsd['jenistiket'];
            $ptujuandari=$rsd['kota1'];
            $ptujuanke=$rsd['kota2'];
            $ptgltiket1=$rsd['tgl1'];
            $pjamtiket1=$rsd['jam1'];
            $pketpergi=$rsd['notes'];
            $pnmagency=$rsd['nama_agency'];
            $pjenisbayarhotel=$rsd['stsbayar'];
            $phargarp2=$rsd['rp'];
            
            $ptotalrp=(double)$ptotalrp+(double)$phargarp2;
            
            $pjenistiket="";
            if ($pjnspiltiket=="K") $pjenistiket="KAI";
            elseif ($pjnspiltiket=="P") $pjenistiket="PESAWAT";
            
            $ptgltiket1 = date('d M Y', strtotime($ptgltiket1));
            
            $phargarp2=number_format($phargarp2,0,",",",");
            
            
            $pnmketnya2 ="Tiket $pjenistiket Pulang ke $ptujuandari - $ptujuanke Tgl. $ptgltiket1 Jam $pjamtiket1";
            $pnmrealisasi2 ="Agency : $pnmagency";
            
            $paktivitas .="<tr>";
            $paktivitas .="<td valign='TOP'><b> - </b></td>";
            $paktivitas .="<td>";
            $paktivitas .="$pnmketnya2<br/>$pnmrealisasi2";
            $paktivitas .="<br/>Harga Rp. : $phargarp2";
            if (!empty($pketpergi)) $paktivitas .="<br/>Note : $pketpergi";
            $paktivitas .="</td>";
            $paktivitas .="</tr>";
            
            
        }
        
        if ($pnoid=="03") {
            $pchkhotel="checked";
            
            $pnginapdi=$rsd['kota1'];
            $ptglhotel1=$rsd['tgl1'];
            $ptglhotel2=$rsd['tgl2'];
            $pkethotel=$rsd['notes'];
            $pnmagency=$rsd['nama_agency'];
            $pjenisbayarhotel=$rsd['stsbayar'];
            $phargarp3=$rsd['rp'];
            
            $ptotalrp=(double)$ptotalrp+(double)$phargarp3;
            
            $ptglhotel1 = date('d M Y', strtotime($ptglhotel1));
            $ptglhotel2 = date('d M Y', strtotime($ptglhotel2));
            
            $phargarp3=number_format($phargarp3,0,",",",");
            
            
            $pnmketnya3 ="Hotel $pnginapdi Tgl. $ptglhotel1 s/d. $ptglhotel2";
            $pnmrealisasi3 ="Agency : $pnmagency";
            
            $paktivitas .="<tr>";
            $paktivitas .="<td valign='TOP'><b> - </b></td>";
            $paktivitas .="<td>";
            $paktivitas .="$pnmketnya3<br/>$pnmrealisasi3";
            $paktivitas .="<br/>Harga Rp. : $phargarp3";
            if (!empty($pkethotel)) $paktivitas .="<br/>Note : $pkethotel";
            $paktivitas .="</td>";
            $paktivitas .="</tr>";
            
            
        }
        
        if ($pnoid=="04") {
            $pchksewa="checked";
            
            $pkotasewa=$rsd['kota1'];
            $ptglsewa1=$rsd['tgl1'];
            $pjamsewa1=$rsd['jam1'];
            $ptglsewa2=$rsd['tgl2'];
            $pjamsewa2=$rsd['jam2'];
            $pketsewa=$rsd['notes'];
            $pnmagency=$rsd['nama_agency'];
            $pjenisbayarsewa=$rsd['stsbayar'];
            $phargarp4=$rsd['rp'];
            
            $ptotalrp=(double)$ptotalrp+(double)$phargarp4;
            
            $ptglsewa1 = date('d F Y', strtotime($ptglsewa1));
            $ptglsewa2 = date('d F Y', strtotime($ptglsewa2));
            
            $phargarp4=number_format($phargarp4,0,",",",");

            
            $pnmketnya4 ="Sewa Kendaraan $pkotasewa Tgl. $ptglsewa1 $pjamsewa1 s/d. $ptglsewa2 $pjamsewa2";
            $pnmrealisasi4 ="Agency : $pnmagency";
            
            $paktivitas .="<tr>";
            $paktivitas .="<td valign='TOP'><b> - </b></td>";
            $paktivitas .="<td>";
            $paktivitas .="$pnmketnya4<br/>$pnmrealisasi4";
            $paktivitas .="<br/>Harga Rp. : $phargarp4";
            if (!empty($pketsewa)) $paktivitas .="<br/>Note : $pketsewa";
            $paktivitas .="</td>";
            $paktivitas .="</tr>";
            
            
        }
        
    }
    
    $paktivitas .="</table>";
    
    $ptotalrp=number_format($ptotalrp,0,",",",");
    
    $pprintdate= date("d/m/Y");
    $pjamnow=date("H:i:s");
    
?>
<HTML>
    <HEAD>
        <title>Print Budget Reques DSS/DCC <?PHP echo $pprintdate." ".$pjamnow; ?></title>
        <meta http-equiv="Expires" content="Mon, 01 Sep 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        <link rel="shortcut icon" href="images/icon.ico" />
    </HEAD>
    <BODY>
        
        <div id="div1">
        
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
            
            
            <center>
                <h3>
                    <?PHP
                        echo "BUDGET REQUEST";
                    ?>
                </h3>
            </center>
            
            <div id="div_title">
                
                <div id="t_kiri">
                    
                    <table>
                        <tr>
                            <td><b>Yang Membuat : </b></td>
                            <td><span style="font-size:13px;"><b><?PHP echo $pnamakaryawan; ?></b></span></td>
                        </tr>
                    </table>
                    
                </div>
                
                <div id="t_kanan">
                    <table>
                        <tr>
                            <td><b>ID. : </b></td>
                            <td><span style="font-size:12px;"><?PHP echo $pnobr; ?> <i>print date</i> <?PHP echo "$pprintdate $pjamnow"; ?></span></td>
                        </tr>
                    </table>
                </div>
                <div class="clsaner"></div>
            </div>
            
            <hr/>
            
            <div id="div_isi">
                
                <div id="i_kirix">
                    
                    <table>
                        
                        <tr>
                            <td valign="top">KODE</td>
                            <td valign="top">:</td>
                            <td valign="top"><b><?PHP echo $pkodeid; ?></b></td>
                        </tr>
                        
                        <tr>
                            <td valign="top">AKTIVITAS</td>
                            <td valign="top">:</td>
                            <td valign="top">
                                <?PHP
                                echo "$paktivitas";
                                ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td valign="top">JUMLAH</td>
                            <td valign="top">:</td>
                            <td valign="top">
                                <?PHP
                                echo "Rp. <b>$ptotalrp</b>";
                                ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td valign="top">KETERANGAN</td>
                            <td valign="top">:</td>
                            <td valign="top">
                                <?PHP
                                echo "$pketerangan";
                                ?>
                            </td>
                        </tr>
                        
                    </table>
                    
                </div>
                
                <!--
                <div id="i_kanan">
                    
                    <div>
                        <table align="center" border="0px">
                            <tr align="center"><td>Yang Membuat</td></tr>
                            <tr align="center">
                                <td>
                                    <?PHP
                                    //if (!empty($namapengaju)) echo "<img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                                    //else echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    //echo "<b><u><span style='font-size:10px;'>$pnamakaryawan</span></u></b>";
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                </div>
                -->
                <div class="clsaner"></div>
            </div>
            
            
            
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
                }elseif ($lvlpengajuan=="FF5") {
                    echo "<table class='tjudul' width='100%'>";
                        echo "<tr>";

                        echo "<td align='center'>Disetujui : <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;(.............................)</td>";

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
        
        
    </BODY>
    
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
    
    #t_kiri {
        float   : left;
        width   : 55%;
        border: 0px solid #000;
    }
    #t_kanan {
        text-align: right;
        float   : right;
        width   : 43%;
        border: 0px solid #000;
    }
    
    #i_kiri {
        float   : left;
        width   : 70%;
        border-right: 0px solid #000;
    }
    #i_kanan {
        text-align: left;
        float   : right;
        width   : 25%;
    }
    
    .clsaner {
        clear: both;
    }
    .tbl_aktv {
        font-size:13px;
    }
</style>
    
    <style type="text/css" media="print">
        @page 
        {
            size: auto;   /* auto is the current printer page size */
            margin-top: 0.2mm;  /* this affects the margin in the printer settings */
            margin-bottom: 0.2mm;  /* this affects the margin in the printer settings */
        }
    </style>
    
</HTML>
<?PHP
    mysqli_close($cnmy);
?>