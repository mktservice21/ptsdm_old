<?php
    if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
    $kodepilih="1";
    if ($_GET['module']=="entrybrluarkota" OR $_GET['module']=="entrybrluarkotaotc") $kodepilih="2";
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
?>
<html>
    <head>
        <?PHP if ($kodepilih==2) { ?>
            <title>Data Biaya Luar Kota <?PHP echo $printdate." ".$jamnow; ?></title>
        <?PHP }else{ ?>
            <title>Data Biaya Rutin <?PHP echo $printdate." ".$jamnow; ?></title>
        <?PHP } ?>
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
                
                $pidbrrutin=$_GET['brid'];
                
                    $qXuery = "select * from dbmaster.v_brrutin0 where idrutin='$pidbrrutin' order by nama, bulan, nama_area";
                
                
                $query = "select a.idrutin, a.tgl, a.bulan, a.periode1, a.periode2, a.karyawanid, b.nama, "
                        . " a.divisi, a.icabangid, c.nama as nama_cabang, a.areaid, d.nama as nama_area, a.keterangan, "
                        . " a.tgl_atasan1, a.tgl_atasan2, a.tgl_atasan3, a.tgl_atasan4, a.tgl_dir, a.tgl_fin, "
                        . " a.atasan1, a.atasan2, a.atasan3, a.atasan4, a.dir, a.fin, "
                        . " a.nama_karyawan, a.userid, a.jabatanid, a.gbr_atasan4 "
                        . " FROM dbmaster.t_brrutin0 as a LEFT JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId "
                        . " LEFT JOIN mkt.icabang as c on a.icabangid=c.iCabangId "
                        . " LEFT JOIN mkt.iarea as d on a.icabangid=d.iCabangId AND a.areaid=d.areaId "
                        . " WHERE a.idrutin='$pidbrrutin' ";
                
                $result = mysqli_query($cnmy, $query);
                $row = mysqli_fetch_array($result);
                $pbulanpengajuan=$row['bulan'];
                $idbr=$row['idrutin'];
                $tglajukan=date("d-m-Y", strtotime($row['tgl']));
                //$tgl_idbr=date("Ymd", strtotime($row['tgl']))."-".(int)$idbr;
                $tgl_idbr=$idbr;
                
                $pbulanpengajuan=date("Ym", strtotime($pbulanpengajuan));
                
                $pkaryawan=$row['karyawanid'];
                $nama=$row['nama'];
                if ($_SESSION['KRYNONE']==$pkaryawan) $nama=$row["nama_karyawan"];
                
                $namaarea=$row['nama_area'];
                $keterangan=$row['keterangan'];
                
                $puserid=$row['userid'];
                $ptglatasan1=$row['tgl_atasan1'];
                $ptglatasan2=$row['tgl_atasan2'];
                $ptglatasan3=$row['tgl_atasan3'];
                $ptglatasan4=$row['tgl_atasan4'];
                $ptglatasanfin=$row['tgl_fin'];
                $ptglatasandir=$row['tgl_dir'];
                
                if ($ptglatasan1=="0000-00-00" OR $ptglatasan1=="0000-00-00 00:00:00") $ptglatasan1="";
                if ($ptglatasan2=="0000-00-00" OR $ptglatasan2=="0000-00-00 00:00:00") $ptglatasan2="";
                if ($ptglatasan3=="0000-00-00" OR $ptglatasan3=="0000-00-00 00:00:00") $ptglatasan3="";
                if ($ptglatasan4=="0000-00-00" OR $ptglatasan4=="0000-00-00 00:00:00") $ptglatasan4="";
                if ($ptglatasanfin=="0000-00-00" OR $ptglatasanfin=="0000-00-00 00:00:00") $ptglatasanfin="";
                if ($ptglatasandir=="0000-00-00" OR $ptglatasandir=="0000-00-00 00:00:00") $ptglatasandir="";
                
                $phari=date("w", strtotime($row['tgl']));
                $pdate=date("d", strtotime($row['tgl']));
                $pbln=(int)date("m", strtotime($row['tgl']));
                $pthn=date("Y", strtotime($row['tgl']));
                
                $tglpengajuan=$seminggu[$phari]." ".$pdate." ".$nama_bln[$pbln]." ".$pthn;
                
                $phari1=date("w", strtotime($row['periode1']));
                $pdate1=date("d", strtotime($row['periode1']));
                $pbln1=(int)date("m", strtotime($row['periode1']));
                $pthn1=date("Y", strtotime($row['periode1']));
                
                $phari2=date("w", strtotime($row['periode2']));
                $pdate2=date("d", strtotime($row['periode2']));
                $pbln2=(int)date("m", strtotime($row['periode2']));
                $pthn2=date("Y", strtotime($row['periode2']));
                
                //$pp01 =  date("d F Y", strtotime($row['periode1']));
                //$pp02 =  date("d F Y", strtotime($row['periode2']));
                
                $pp01=$pdate1." ".$nama_bln[$pbln1]." ".$pthn1;
                $pp02=$pdate2." ".$nama_bln[$pbln2]." ".$pthn2;
                
                $pdivisi=$row['divisi'];
                
                $pjabatanid=$row['jabatanid'];
                $lvlpengajuan = getfield("select LEVELPOSISI as lcfields from dbmaster.v_level_jabatan where jabatanId='$pjabatanid'");
                
                $query = "SELECT distinct karyawanid, gsm FROM dbmaster.t_karyawan_app_gsm where karyawanid='$pkaryawan'";
                $ketemu= mysqli_num_rows(mysqli_query($cnmy, $query));
                if ($ketemu>0) {
                    $lvlpengajuan="FF4";
                }
                
                $patasan1=$row['atasan1'];
                $nmatasan1 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan1'");
                $patasan2=$row['atasan2'];
                $nmatasan2 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan2'");
                $patasan3=$row['atasan3'];
                $nmatasan3 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan3'");
                $patasan4=$row['atasan4'];
                $nmatasan4 = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasan4'");
                $patasandir=$row['dir'];
                $nmatasandir = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasandir'");
                
                if ($pbulanpengajuan<'202107') {
                    $query_ttd = "SELECT idrutin, gambar, gbr_atasan1, gbr_atasan2, gbr_atasan3, gbr_atasan4, gbr_dir FROM "
                            . " dbttd.t_brrutin_ttd WHERE idrutin='$pidbrrutin'";
                }else{
                    $query_ttd = "SELECT idrutin, gambar, gbr_atasan1, gbr_atasan2, gbr_atasan3, gbr_atasan4, gbr_dir FROM "
                            . " dbmaster.t_brrutin0 WHERE idrutin='$pidbrrutin'";
                }
                $tampilttd = mysqli_query($cnmy, $query_ttd);
                $nttd = mysqli_fetch_array($tampilttd);
                
                $gambar=$nttd['gambar'];
                $gbr1=$nttd['gbr_atasan1'];
                $gbr2=$nttd['gbr_atasan2'];
                $gbr3=$nttd['gbr_atasan3'];
                $gbr4=$nttd['gbr_atasan4'];
                $gbrdir=$nttd['gbr_dir'];
                
                if ($patasan4==$pkaryawan) $gambar=$row['gbr_atasan4'];
                
                $milliseconds = round(microtime(true) * 1000);
                $now_fil=date("mdYhis").$milliseconds;
                
                $namaajkn=$tglajukan;
                $namaspv="";
                $namadm="";
                $namasm="";
                $namagsm="";
                $namadir="";
                $gmrheight = "80px";
                
                if ($pdivisi=="OTC" AND $_GET['module']=="entrybrrutin") {
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
                    $namapengaju="img_".$idbr."PENGAJU_.png";
                    file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
                }
                
                if (!empty($gbr1)) {
                    $data="data:".$gbr1;
                    $data=str_replace(' ','+',$data);
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $namaspv="img_".$idbr."SVP_.png";
                    file_put_contents('images/tanda_tangan_base64/'.$namaspv, $data);
                }
                
                if (!empty($gbr2)) {
                    $data="data:".$gbr2;
                    $data=str_replace(' ','+',$data);
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $namadm="img_".$idbr."DM_.png";
                    file_put_contents('images/tanda_tangan_base64/'.$namadm, $data);
                }
                
                
                
                if ($pdivisi=="OTC" AND ($_GET['module']=="entrybrrutinotc" OR $_GET['module']=="entrybrluarkotaotc") ) {
                    if ($nmatasan1==$nmatasan2) $nmatasan2="";
                    if ($nmatasan1==$nmatasan3) $nmatasan3="";
                    $nmatasan3=$nmatasan4;
                    if (!empty($gbr4)) {
                        $data="data:".$gbr4;
                        $data=str_replace(' ','+',$data);
                        list($type, $data) = explode(';', $data);
                        list(, $data)      = explode(',', $data);
                        $data = base64_decode($data);
                        $namasm="img_".$idbr."SM_.png";
                        file_put_contents('images/tanda_tangan_base64/'.$namasm, $data);
                    }
                }else{
                    
                    if (!empty($gbr3)) {
                        $data="data:".$gbr3;
                        $data=str_replace(' ','+',$data);
                        list($type, $data) = explode(';', $data);
                        list(, $data)      = explode(',', $data);
                        $data = base64_decode($data);
                        $namasm="img_".$idbr."SM_.png";
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
                    
                }
				
                if (!empty($gbrdir)) {
                    $data="data:".$gbrdir;
                    $data=str_replace(' ','+',$data);
                    list($type, $data) = explode(';', $data);
                    list(, $data)      = explode(',', $data);
                    $data = base64_decode($data);
                    $namadir="img_".$idbr."DIR01_.png";
                    file_put_contents('images/tanda_tangan_base64/'.$namadir, $data);
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
                        echo "BIAYA LUAR KOTA";
                    ?>
                </h3>
            </center>
            <div id="kotakjudul">
                <div id="isikiri">
                    <table class='tjudul' width='100%'>
                        <tr><td>ID</td><td>:</td><td nowrap><?PHP echo "<b>$tgl_idbr</b>"; ?></td></tr>
                        <tr><td>NAMA</td><td>:</td><td nowrap><?PHP echo "$nama"; ?></td></tr>
                        <?PHP
                        if ((int)$pjabatanid!=19 AND (int)$pjabatanid!=38 AND (int)$pjabatanid!=10 AND (int)$pjabatanid!=18 AND (int)$pjabatanid!=15 AND (int)$pjabatanid!=20 AND (int)$pjabatanid!=8) {
                        ?>
                        <!--<tr><td>AREA</td><td>:</td><td nowrap><?PHP echo "$namaarea"; ?></td></tr>-->
                        <?PHP
                        }
                        ?>
                        <tr><td>PERIODE</td><td>:</td><td nowrap><?PHP echo "$pp01 - $pp02"; ?></td></tr>
                        <?php
                        if ($kodepilih==2){
                            echo "<tr><td nowrap>KUNJUNGAN KE KOTA</td><td>:</td><td>$keterangan</td></tr>";
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
                    $ada=0;
                    $tjml=1;
                    if (!empty($uc['jumlah'])) $tjml=$uc['jumlah'];
                    
                    if ($_GET["brid"]=="BRT0000036")
                        $cari = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_brrutin1 where idrutin='$_GET[brid]' and nobrid=$uc[nobrid]");
                    else
                        $cari = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_brrutin1 where idrutin='$_GET[brid]' and nobrid='$uc[nobrid]'");
                    
                    $ada = mysqli_num_rows($cari);
                    if ($ada>0) {
                        $xx=0;
                        while ($c=mysqli_fetch_array($cari)){
                            $rptotal=number_format($c['rptotal'],0);
                            $rpnilai=number_format($c['rp'],0);
                            $jmlhari=number_format($c['qty'],0);
                            $satuan="";
                            if ($c['nobrid']=="04" OR $c['nobrid']=="25") $satuan="($jmlhari x $rpnilai)";
                            if (!empty($c['rptotal']))
                                $total=$total+$c['rptotal'];
                            
                            if ($kodepilih==2) {
                                $ptgl1=$c['tgl1'];
                                $ptgl2=$c['tgl2'];
                                if (!empty($ptgl1) AND $ptgl1!="0000-00-00") {
                                    $ptgl1 = date('d/m/Y', strtotime($c['tgl1']));
                                    $ptgl2 = date('d/m/Y', strtotime($c['tgl2']));
                                    $satuan=" TGL. $ptgl1 s/d. $ptgl2";
                                }
                            }
                            
                            //khusus finance note
                            $pnotenket="";
                            //if (($_SESSION['GROUP']=="1" OR $_SESSION['GROUP']=="28")) {//AND $_SESSION['IDCARD']!="0000000329"
                                $alsaneditfin="";
                                if (!empty($c['alasanedit_fin'])) {
                                    $alsaneditfin=$c['alasanedit_fin'];
                                }else{
                                    if (!empty($c['notes']))
                                        $alsaneditfin=$c['notes'];
                                }
                                if (!empty($alsaneditfin))
                                    $pnotenket="&nbsp; &nbsp; &nbsp; (".$alsaneditfin.")";
                            //}
                            
                            $npkmdetail="";
                            if ($c['nobrid']=="01" OR $c['nobrid']=="24")  {
                                $pkmdetail=$c['km'];
                                if (empty($pkmdetail)) $pkmdetail=0;
                                $pkmdetail=number_format($pkmdetail,0);
                                if ($pkmdetail<>"0") $npkmdetail =" (KM : $pkmdetail) ";
                            }
                            
                            $mtglpilih="";
                            if ($c['nobrid']=="18" OR $c['nobrid']=="19" OR $c['nobrid']=="12")  {
                                $ptgl1=$c['tgl1'];
                                $ptgl2=$c['tgl2'];
                                
                                $nntgl1=""; $nntgl2="";
                                if (!empty($ptgl1) AND $ptgl1!="0000-00-00") {
                                    if ($c['nobrid']=="12") {
                                        $nntgl1 = date('F Y', strtotime($c['tgl1']));
                                    }else{
                                        $nntgl1 = date('d/m/Y', strtotime($c['tgl1']));
                                    }
                                }
                                if (!empty($ptgl2) AND $ptgl2!="0000-00-00") {
                                    if ($c['nobrid']=="12") {
                                        $nntgl2 = date('F Y', strtotime($c['tgl2']));
                                    }else{
                                        $nntgl2 = date('d/m/Y', strtotime($c['tgl2']));
                                    }
                                }
                                if ($c['nobrid']=="12") {
                                    if (!empty($nntgl1) AND !empty($nntgl2)) $mtglpilih=" Periode $nntgl1 s/d. $nntgl2";
                                    if (!empty($nntgl1) AND empty($nntgl2)) $mtglpilih=" Periode $nntgl1";
                                }else{
                                    if (!empty($nntgl1) AND !empty($nntgl2)) $mtglpilih=" Tgl. $nntgl1 s/d. $nntgl2";
                                    if (!empty($nntgl1) AND empty($nntgl2)) $mtglpilih=" Tgl. $nntgl1";
                                }
                                
                                if ((double)$rptotal==0) $mtglpilih="";
                            }
                            
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td>$uc[nama] $satuan $pnotenket $npkmdetail $mtglpilih</td>";
                            echo "<td align='right'>Rp. $rptotal</td>";
                            echo "</tr>"; 
                            $no++;
                            $xx++;
                        }
                        $tjml=(int)$tjml-(int)$xx;
                    }
                    
                    for ($i=1; $i <=$tjml; $i++) {
                        echo "<tr>";
                        echo "<td>$no</td>";
                        echo "<td>$uc[nama]</td>";
                        echo "<td></td>";
                        echo "</tr>";
                        $no++;
                    }
                }
                //Total
                $gtotal=number_format($total,0);
                echo "<tr>";
                echo "<td style='border:0px;'></td>";
                echo "<td align='right'>Total  </td>";
                echo "<td align='right'>Rp. $gtotal</td>";
                echo "</tr>";
                
                if ($kodepilih==2222) {
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
            <br/>
            <?PHP 
                if ($kodepilih==1){
                    echo "Note : $keterangan";
                }else{
                    echo "<div align='right'>$tglpengajuan</div>";
                }
                
                if (!empty($ptglatasan1) AND !empty($ptglatasan2) AND !empty($ptglatasan3) AND ($puserid=="0000000143" OR $puserid=="0000000329")) {
                    echo "<br/><br/><b><u>Approve Manual (diinput Finance)</u></b>";
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
                            <td align="center">';
                        if ($pdivisi=="OTC" AND ( $_GET['module']=="entrybrrutinotc" OR $_GET['module']=="entrybrluarkotaotc") ) {
                            echo 'Diperiksa oleh HOS :';
                        }else{
                            echo 'Diperiksa oleh SM :';
                        }
                            
                                if (!empty($namasm))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nmatasan3</u></b>";
                             
                        if (!empty($nmatasan2)) {
                            echo '</td>
                                <td align="center">
                                    Diperiksa oleh DM :';
                                    if (!empty($namadm))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namadm' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b><u>$nmatasan2</u></b>";
                        }
                        echo '</td>';
                        if (!empty($nmatasan1)) {
                            
                                echo '<td align="center">
                                    Diperiksa oleh Atasan :';
                                    if (!empty($namaspv))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namaspv' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b><u>$nmatasan1</u></b>";
                            echo '</td>';
                        }
                        
                        echo '<td align="center">
                                Yang Membuat :';
                                if (!empty($namapengaju))
                                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                                else
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                echo "<b><u>$nama</u></b>";
                        echo '</td>
                        </tr>
                        ';
                    }elseif ($lvlpengajuan=="FF2" OR $lvlpengajuan=="AD1" OR $lvlpengajuan=="OB1") {
						
                        if ($lvlpengajuan=="AD1" AND empty($patasan3)) {
						
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
							
							
							
                        if ($patasan2==$patasan3) {
                            echo '
                            <tr>
                                <td align="center">
                                    Disetujui :
                                    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
                                    (.............................)
                                </td>
                                <td align="center">';
                                if ($pdivisi=="OTC" AND ( $_GET['module']=="entrybrrutinotc" OR $_GET['module']=="entrybrluarkotaotc") ) {
                                    echo 'Diperiksa oleh HOS :';
                                }else{
                                    echo 'Diperiksa oleh SM :';
                                }
                                    if (!empty($namasm))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b><u>$nmatasan3</u></b>";
                            echo '</td>
                                <td align="center">
                                    &nbsp';
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b>&nbsp</b>";
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
                                        Disetujui :
                                        <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
                                        (.............................)
                                </td>
                                <td align="center">';
                                if ($pdivisi=="OTC" AND ( $_GET['module']=="entrybrrutinotc" OR $_GET['module']=="entrybrluarkotaotc") ) {
                                    echo 'Diperiksa oleh HOS :';
                                }else{
                                    echo 'Diperiksa oleh SM :';
                                }
                                    if (!empty($namasm))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namasm' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b><u>$nmatasan3</u></b>";

                            echo '</td>';
                            if (!empty($nmatasan2)) {
                                echo '<td align="center">
                                    Diperiksa oleh Atasan :';
                                    if (!empty($namadm))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namadm' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b><u>$nmatasan2</u></b>";

                                echo '</td>';
                            }
                            echo '<td align="center">
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
						
						
						
                    }
						
						
						
                    }elseif ($lvlpengajuan=="FF3") {
						
                        //khusus
                        if ($pkaryawan=="0000002297x") {
                            
                            echo '
                            <tr>
                                <td align="center">
                                    Diperiksa oleh Atasan :';
                                    if (!empty($namagsm))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b><u>$nmatasan4</u></b>";
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
							
                        }
						
						
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
                        if ($pkaryawan=="0000002083") {
                            
                            echo "<tr>";
                            
                                echo "<td align='center'>";
                                    echo "Menyetujui :";
                                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "(.................................)";
                                echo "</td>";
                            
                            
                                echo "<td align='center'>";
                                    echo "Mengetahui :";
                                    if (!empty($namaspv))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namaspv' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b><u>$nmatasan1</u></b>";
                                echo "</td>";
                            
                                echo "<td align='center'>";
                                    echo "Yang Membuat :";
                                    if (!empty($namapengaju))
                                        echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                                    else
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                    echo "<b><u>$nama</u></b>";
                                echo "</td>";
                                
                            echo "</tr>";
                            
                        }else{
                        
                            if ( ($pjabatanid=="05" OR $pjabatanid=="25") AND !empty($gbrdir)) {
                                
                                echo "<tr>";

                                    echo "<td align='center'>";
                                        echo "Menyetujui :";
                                        if (!empty($namadir))
                                            echo "<br/><img src='images/tanda_tangan_base64/$namadir' height='$gmrheight'><br/>";
                                        else
                                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                        echo "<b><u>$nmatasandir</u></b>";
                                    echo "</td>";

                                    echo "<td align='center'>";
                                        echo "Yang Membuat :";
                                        if (!empty($namapengaju))
                                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                                        else
                                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                        echo "<b><u>$nama</u></b>";
                                    echo "</td>";

                                echo "</tr>";
                                
                            }else{
						
                                echo "<tr>";

                                    echo "<td align='center'>";
                                        echo "Menyetujui :";
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                        echo "(.................................)";
                                    echo "</td>";


                                    echo "<td align='center'>";
                                        echo "Mengetahui :";
                                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                        echo "(.................................)";
                                    echo "</td>";

                                    echo "<td align='center'>";
                                        echo "Yang Membuat :";
                                        if (!empty($namapengaju))
                                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                                        else
                                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                                        echo "<b><u>$nama</u></b>";
                                    echo "</td>";

                                echo "</tr>";

                        }
							
                            
                        }
                    }
                    ?>
                </table>
            </center>
        </div>
    </body>
</html>
