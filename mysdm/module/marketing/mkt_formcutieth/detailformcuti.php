<?php
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
    
    
    session_start();
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
    $namapengaju="";
    $nmatasan4="";
    
    $namaspv="";
    $namadm="";
    $namasm="";
    $namagsm="";
    $namaceo="";
    $gmrheight = "80px";
    
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    
    
    $pid=$_GET['brid'];
    $query ="select a.*, b.nama as nama_karyawan, c.nama_group, d.nama as nama_jabatan from hrd.t_cuti0 as a "
            . " JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId "
            . " JOIN hrd.jenis_cuti as c on a.id_jenis=c.id_jenis "
            . " LEFT JOIN hrd.jabatan as d on a.jabatanid=d.jabatanId "
            . " where a.idcuti='$pid'";
    $tampil=mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pnamakry=$row['nama_karyawan'];
    $pidjenis=$row['id_jenis'];
    $pnamajenis=$row['nama_group'];
    $pnamajbt=$row['nama_jabatan'];
    $pkeperluan=$row['keperluan'];
    $pbln1=$row['bulan1'];
    $pbln2=$row['bulan2'];
    $ptglinput=$row['tglinput'];
    $pnamaarea="";
    
    
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
    $query = "select * from dbttd.t_cuti_ttd where idcuti='$pid'";
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
        $namapengaju="img_".$pid."FCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
    }
    
    if (!empty($gbr1)) {
        $data="data:".$gbr1;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namaspv="img_".$pid."SVPFCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namaspv, $data);
    }
    
    if (!empty($gbr2)) {
        $data="data:".$gbr2;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namadm="img_".$pid."DMFCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namadm, $data);
    }
    
    if (!empty($gbr3)) {
        $data="data:".$gbr3;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namasm="img_".$pid."SMFCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namasm, $data);
    }

    if (!empty($gbr4)) {
        $data="data:".$gbr4;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namagsm="img_".$pid."GSMFCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namagsm, $data);
    }
    
    if (!empty($gbr5)) {
        $data="data:".$gbr5;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namaceo="img_".$pid."CEOFCUT_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namaceo, $data);
    }
    
    
    
    $xtgl= date('d', strtotime($ptglinput));
    $xbulan = $bulan_array[(INT)date('m', strtotime($ptglinput))];
    $xthn= date('Y', strtotime($ptglinput));
    $ptanggalinput=$xtgl." ".$xbulan." ".$xthn;
    
    
    $ptglpilih="";
    $pjmlhari=0;
    $query = "select distinct tanggal from hrd.t_cuti1 where idcuti='$pid' order by tanggal";
    $tampil2=mysqli_query($cnmy, $query);
    while ($row2= mysqli_fetch_array($tampil2)) {
        $n_tanggal=$row2['tanggal'];
        $x_tgl= date('d/m/Y', strtotime($n_tanggal));
        
        $ptglpilih .=$x_tgl.", ";
        
        
        $pjmlhari++;
    }
    if (!empty($ptglpilih)) $ptglpilih=substr($ptglpilih, 0, -2).".";
    
    $pthn1 = date('Y', strtotime($pbln1));
    $pbulan1 = $bulan_array[(INT)date('m', strtotime($pbln1))];
    $pthn2 = date('Y', strtotime($pbln2));
    $pbulan2 = $bulan_array[(INT)date('m', strtotime($pbln2))];
    
    $pketterhitung="&nbsp;selama $pjmlhari <b>hari kerja</b>";
    $pterhitung="tanggal $ptglpilih";
    if ($pidjenis=="02") {
        $pketterhitung="";
        $pterhitung="bulan $pbulan1 $pthn1 s/d. $pbulan2 $pthn2";
    }
?>

<HTML>
<HEAD>
    <title>Form Cuti</title>
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
                echo "SURAT PENGAJUAN IZIN/CUTI";
                ?>
            </h3>
        </center>
        
        <div>
            Yang bertanda tangan di bawah ini : <br/><br/>
            <table style="margin-left:15px;" >
                <tr>
                    <td nowrap>Nama</td><td> : </td><td nowrap><?PHP echo $pnamakry; ?></td>
                </tr>
                <tr>
                    <td nowrap>Jabatan</td><td> : </td><td nowrap><?PHP echo $pnamajbt; ?></td>
                </tr>
                <tr>
                    <td nowrap>Area</td><td> : </td><td nowrap><?PHP echo $pnamaarea; ?></td>
                </tr>
                <?PHP if ($pidjenis!="02") { ?>
                <tr>
                    <td>Keperluan</td><td> : </td><td ><?PHP echo $pkeperluan; ?></td>
                </tr>
                <?PHP } ?>
            </table><br/>
            Dengan ini mengajukan permohonan <b><?PHP echo $pnamajenis; ?></b><?PHP echo $pketterhitung; ?>,<br/>
            terhitung <?PHP echo $pterhitung; ?> <br/><br/>
            Demikian surat pengajuan ini kami buat sebenar-benarnya untuk dipergunakan seperlunya.<br/>
            Terima kasih.<br/>
        </div>
        
        <br/><br/>
        <div>
            <?PHP echo "................................., ".$ptanggalinput; ?>
        </div>
        
        
        <br/>&nbsp;<br/>&nbsp;

        <center>
            <table class='tjudul' width='100%'>
                <?PHP
                    echo "<tr>";
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
    mysqli_close($cnmy);
?>