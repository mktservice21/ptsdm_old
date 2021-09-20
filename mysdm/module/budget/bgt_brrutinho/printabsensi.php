<?php
    session_start();
    
    $bulan_array=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
        "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember");

    $hari_array = array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );
    
    
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

    $query = "select a.*, b.nama as nama_kry, c.nama as nama_cabang, d.nama as nama_area, "
            . " e.nama as nama_atasan4, f.absen_rutin "
            . " from dbmaster.t_brrutin0 as a "
            . " JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang as c on a.icabangid=c.icabangid "
            . " LEFT JOIN MKT.iarea as d on a.icabangid=d.icabangid AND a.areaid=d.areaid "
            . " LEFT JOIN hrd.karyawan as e on a.atasan4=e.karyawanid "
            . " LEFT JOIN dbmaster.t_karyawan_posisi as f on a.karyawanid=f.karyawanId "
            . " WHERE a.kode='1' AND "
            . " a.idrutin='$pidrutin' ";
    $result = mysqli_query($cnmy, $query);
    $row = mysqli_fetch_array($result);
    
    
    $pblnpengajuan=$row['bulan'];
    $pbulanpengajuan=date("Ym", strtotime($pblnpengajuan));
    
    $tglajukan=date("d-m-Y", strtotime($row['tgl']));
    $pkaryawanid=$row['karyawanid'];
    $pnamakry=$row['nama_kry'];
    $pidcab=$row['icabangid'];
    $pnmcab=$row['nama_cabang'];
    $pidarea=$row['areaid'];
    $pnmarea=$row['nama_area'];
    $pjbtid=$row['jabatanid'];
    $phitungabs=$row['absen_rutin'];
    $pkdoeperiode=$row['kodeperiode'];
    
    
    $phari1=date("w", strtotime($row['periode1']));
    $pdate1=date("d", strtotime($row['periode1']));
    $pbln1=(int)date("m", strtotime($row['periode1']));
    $pthn1=date("Y", strtotime($row['periode1']));

    $phari2=date("w", strtotime($row['periode2']));
    $pdate2=date("d", strtotime($row['periode2']));
    $pbln2=(int)date("m", strtotime($row['periode2']));
    $pthn2=date("Y", strtotime($row['periode2']));
                
    $pp01=$pdate1." ".$nama_bln[$pbln1]." ".$pthn1;
    $pp02=$pdate2." ".$nama_bln[$pbln2]." ".$pthn2;
                
                
    $pketerangan=$row['keterangan'];
    
    $pidatasan4=$row['atasan4'];
    $nmatasan4=$row['nama_atasan4'];
    $ptglatasan4=$row['tgl_atasan4'];
    
    
    if ($pbulanpengajuan<'202107') {
        $query_ttd = "SELECT idrutin, gambar, gbr_atasan4 FROM "
                . " dbttd.t_brrutin_ttd WHERE idrutin='$pidrutin'";
    }else{
        $query_ttd = "SELECT idrutin, gambar, gbr_atasan4 FROM "
                . " dbmaster.t_brrutin0 WHERE idrutin='$pidrutin'";
    }
    $tampilttd = mysqli_query($cnmy, $query_ttd);
    $nttd = mysqli_fetch_array($tampilttd);
                
    $gambar=$nttd['gambar'];
    $gbr4=$nttd['gbr_atasan4'];
    
    
    
    if ($ptglatasan4=="0000-00-00") $ptglatasan4="";
    
    $ptglatasandir=$row['tgl_dir'];
    if ($ptglatasandir=="0000-00-00") $ptglatasandir="";
    $patasandir=$row['dir'];
    $nmatasandir = getfield("select nama as lcfields from hrd.karyawan where karyawanId='$patasandir'");
    $gbrdir=$row['gbr_dir'];
                
    
    
    $puserid=$row['userid'];
    
    
    if (!empty($gambar)) {
        $data="data:".$gambar;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju="img_".$pidrutin."PENGAJUA_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
    }
    
    if (!empty($gbr4)) {
        $data="data:".$gbr4;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namagsm="img_".$pidrutin."GSMA_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namagsm, $data);
        
        if ($pbulanpengajuan>='202108' AND $pjbtid=="05") {
            $namapengaju=$namagsm;
        }
        
    }
    
    $namadir="";
    if (!empty($gbrdir)) {
        $data="data:".$gbrdir;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namadir="img_".$pidrutin."DIR01_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namadir, $data);
    }
    
    
    $pketperiksa04="Diperiksa oleh :";
    if ($pidatasan4=="0000002403") $pketperiksa04="Menyetujui :";
    
    if ($pkaryawanid=="0000001479") {
        if (empty($gbr4)) $nmatasan4="";
    }else{
    
        if ($pjbtid=="05") {
            $nmatasan4=$nmatasandir;
            $namagsm=$namadir;
        }
        
    }
    
    
    $tmp_tabel01="";
    if ($phitungabs=="Y" AND $pkdoeperiode=="2") {
        include "cari_absen_karyawan.php";
        $pjumlahabs = CariAbsensiByKaryawan("", $pkaryawanid, $pblnpengajuan, "0");
        $pjmlwfh=$pjumlahabs[0];
        $pjmlwfo=$pjumlahabs[1];
        $pjmlwfo_val=$pjumlahabs[2];
        $pjmlwfo_inv=$pjumlahabs[3];
        $tmp_tabel01=$pjumlahabs[4];
    
    }
    
    
?>

<HTML>
<HEAD>
    <title>Data Absensi Invalid Biaya Rutin HO <?PHP echo $printdate." ".$jamnow; ?></title>
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

        table.example_2 td, table.example_3 th {
            border: 1px solid #000; /* No more visible border */
            height: 28px;
            transition: all 0.3s;  /* Simple transition for hover effect */
            padding: 5px;
        }

        table.example_2 th, table.example_3 th {
            background: #DFDFDF;  /* Darken header a bit */
            font-weight: bold;
        }

        table.example_2 td, table.example_3 td {
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
        
        
        table.example_3 {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            width: 100%;
            border-collapse:
            collapse; border-spacing: 0;
            font-size: 14px;
            border: 1px solid #000;
            padding:5px;
        }
        table.example_3 td {
            padding:5px;
        }
    </style>


</HEAD>
    
<BODY>

    <div id="div1">


        <center>
            <img src="images/logo_sdm.jpg" height="70px">
            <h2>PT SURYA DERMATO MEDICA LABORATORIES</h2>
        </center>
        <hr/>
        <center>
            <h3>
                <?PHP
                echo "Data Absensi Invalid";
                ?>
            </h3>
        </center>

        <div id="kotakjudul">
            <div id="isikiri">
                <table class='tjudul' width='100%'>
                    <?PHP
                    echo "<tr><td>ID</td><td>:</td> <td nowrap><b>$pidrutin</b></td></tr>";
                    echo "<tr><td>NAMA</td><td>:</td> <td nowrap>$pnamakry</td></tr>";
                    echo "<tr><td>PERIODE</td><td>:</td> <td nowrap>$pp01 - $pp02</td></tr>";
                    ?>
                </table>
            </div>
            <div id="isikanan">

            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        <br/>&nbsp;

        <?PHP
            if (!empty($tmp_tabel01) && $phitungabs=="Y") {
                
                $query ="select tanggal, jam_masuk, keterangan, jam_pulang, keterangan_p, j_durasi from $tmp_tabel01 WHERE "
                        . " IFNULL(wfo_valid,'')='N'";
                $tampil = mysqli_query($cnmy, $query);
                $ketemu= mysqli_num_rows($tampil);
                if ((INT)$ketemu>0) {
                    echo "<table id='example_2' class='table-bordered example_3' border='1px'>";
                    echo "<tr>";
                    echo "<th nowrap width='100px'>Tanggal</th>";
                    echo "<th nowrap width='100px'>Jam</th>";
                    echo "<th nowrap width='100px'>Durasi</th>";
                    echo "<th nowrap width='250px'>Keterangan</th>";
                    echo "<th nowrap width='50px'>Paraf</th>";
                    echo "</tr>";
                    while ($row=mysqli_fetch_array($tampil)) {
                        $ntgl=$row['tanggal'];
                        $absjammasuk=$row['jam_masuk'];
                        $absjampulang=$row['jam_pulang'];
                        $absdurasi=$row['j_durasi'];
                        $absket_masuk=$row['keterangan'];
                        $absket_pulang=$row['keterangan_p'];
                        
                        $abstanggal = date('d/m/Y', strtotime($ntgl));
                        
                        $xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
                        $xtgl= date('d', strtotime($ntgl));
                        $xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
                        $xthn= date('Y', strtotime($ntgl));
                        
                        $abs_keterangan="";
                        
                        if (!empty($absket_masuk)) {
                            $abs_keterangan=$absket_masuk;
                            if (!empty($absket_pulang)) {
                                $abs_keterangan=$absket_masuk.", ".$absket_pulang;
                            }
                        }else{
                            if (!empty($absket_pulang)) $abs_keterangan=$absket_pulang;
                        }
                        
                        if (!empty($abs_keterangan)) $abs_keterangan .="<hr/>";
                        
                        echo "<tr valign='top'>";
                        echo "<td nowrap>$xhari, $xtgl $xbulan $xthn</td>";
                        echo "<td nowrap>$absjammasuk s/d. $absjampulang</td>";
                        echo "<td nowrap>$absdurasi</td>";
                        echo "<td >$abs_keterangan<br/>&nbsp;<br/>&nbsp;</td>";
                        echo "<td nowrap>&nbsp;</td>";
                        echo "</tr>";
                    }
                    
                    echo "</table>";
                }
            }
    
            if (!empty($ptglatasan4) AND ($puserid=="0000000143" OR $puserid=="0000000329")) {
                echo "<br/><br/><b><u>Approve Manual (diinput Finance)</u></b>";
            }
            
            if ($pidatasan4==$pkaryawanid AND $pjbtid=="01") {
                $namapengaju="";
                $pnamakry="";
            }
        ?>
        <br/>&nbsp;<br/>&nbsp;

        <center>
            <table class='tjudul' width='100%'>
                <?PHP
                    echo "<tr>";
                    
                        
                        echo "<td align='center'>";
                        echo "&nbsp; &nbsp; &nbsp;";
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>";
                        echo "</td>";
                        
                        echo "<td align='center'>";
                        echo "&nbsp; &nbsp; &nbsp;";
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>";
                        echo "</td>";
                        
                        echo "<td align='center'>";
                        echo "&nbsp; &nbsp; &nbsp;";
                        echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        echo "<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>";
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
        <br/><br/><br/>
        
    </div>

</BODY>
    
</HTML>

<?PHP
hapusdata:
    if (!empty($tmp_tabel01)) {
        mysqli_query($cnmy, "drop table IF EXISTS $tmp_tabel01");
    }
    mysqli_close($cnmy);
?>