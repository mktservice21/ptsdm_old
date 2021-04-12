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
    
    include "config/koneksimysqli.php";
    $pid=$_GET['brid'];
    $query ="select a.*, b.nama as nama_karyawan, c.nama_group, d.nama as nama_jabatan from hrd.t_cuti0 as a "
            . " JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId "
            . " JOIN hrd.jenis_cuti as c on a.id_jenis=c.id_jenis "
            . " LEFT JOIN hrd.jabatan as d on a.jabatanid=d.jabatanId "
            . " where a.idcuti='$pid'";
    $tampil=mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    $pnamakaryawan=$row['nama_karyawan'];
    $pidjenis=$row['id_jenis'];
    $pnamajenis=$row['nama_group'];
    $pnamajbt=$row['nama_jabatan'];
    $pkeperluan=$row['keperluan'];
    $pbln1=$row['bulan1'];
    $pbln2=$row['bulan2'];
    $ptglinput=$row['tglinput'];
    $pnamaarea="";
    
    
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
                    <td nowrap>Nama</td><td> : </td><td nowrap><?PHP echo $pnamakaryawan; ?></td>
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
        
    </div>
    
</BODY>

</HTML>

<?PHP
hapusdata:
    mysqli_close($cnmy);
?>