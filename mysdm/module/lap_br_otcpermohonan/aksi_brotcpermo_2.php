<?PHP
    session_start();
    include "config/koneksimysqli_it.php";
    include_once("config/common.php");
    $srid = $_SESSION['USERID'];
    $srnama = $_SESSION['NAMALENGKAP'];
    $sr_id = substr('0000000000'.$srid,-10);
    $userid = $_SESSION['USERID'];
    $jenis = $_POST['cb_jenis'];

    $tgl01=$_POST['e_periode01'];

    $periode1= date("Y-m-d", strtotime($tgl01));
    $periode= date("d-m-Y", strtotime($tgl01));

    $now=date("mdYhis");
    $tmpbudgetreq01 =" dbtemp.DTBUDGETBRREKAPSBYOTC01_$_SESSION[IDCARD]$now ";
    echo "<center><h2><u>REKAP DATA PERMOHONAN DANA BR</u></h2></center>";

    $bl=date("m");
    $bl=(int)$bl;
    $blromawi="I";
    if ($bl==1) $blromawi="I";
    if ($bl==2) $blromawi="II";
    if ($bl==3) $blromawi="III";
    if ($bl==4) $blromawi="IV";
    if ($bl==5) $blromawi="V";
    if ($bl==6) $blromawi="VI";
    if ($bl==7) $blromawi="VII";
    if ($bl==8) $blromawi="VIII";
    if ($bl==9) $blromawi="IX";
    if ($bl==10) $blromawi="X";
    if ($bl==11) $blromawi="XI";
    if ($bl==12) $blromawi="XII";

    $noslipurut="BR-OTC/".$blromawi."/".date("y");
    $noslipurut="BR-OTC";


    $sql = "select icabangid_o, nama_cabang, keterangan1, idkontak, bankreal1, real1, norekreal1, sum(jumlah) as jumlah, CAST(null as char(1)) as GRP1 "
            . " from dbmaster.v_br_otc_all"
            . " where tglbr = '$periode1' and case when ifnull(lampiran,'N')='' then 'N' else lampiran end ='$jenis' ";
    $sql .= " group by icabangid_o, nama_cabang, keterangan1, idkontak, real1, norekreal1";

    $sql = "create table $tmpbudgetreq01 ($sql)";
    mysqli_query($cnit, $sql);

    $sql = "update $tmpbudgetreq01 as b set b.nama_cabang=(select a.initial from dbmaster.cabang_otc as a where b.icabangid_o=a.cabangid_ho)"
            . "where b.icabangid_o in (select distinct c.cabangid_ho from dbmaster.cabang_otc as c)";
    mysqli_query($cnit, $sql);

    $sql = "update $tmpbudgetreq01 as b set b.GRP1=(select a.group1 from dbmaster.cabang_otc as a where b.icabangid_o=a.cabangid_ho)";
    mysqli_query($cnit, $sql);

    $sql = "update $tmpbudgetreq01 set icabangid_o='group1' where ifnull(GRP1,'') <> ''";
    mysqli_query($cnit, $sql);
        
        
?>
<html>
    <head>
        <!-- CSS bootstrap untuk menampilkan halaman secara cantik -->
        <link href="../assets/css/bootstrap.css" rel="stylesheet">
        <style type="text/css">  
        /* CSS untuk memformat halaman */  
        body {
            padding-top: 20px;
            padding-bottom: 40px;
            font-size: 0.7em;
        }
        table {
            width: 99%;
            font-family: "Times New Roman", Times, serif;
            font-size: 11px;
        }
        table.tjudul {
            width: 99%;
            font-size: 13px;
        }
        table.tisi {
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
        }
        table.tisi td {
            padding-top: 5px;
            padding-bottom: 5px;
            padding-left: 2px;
        }
    </style>
    </head>
    <body>
        
    <div class='span8  offset2'>
        <h2 style='text-align: center'> UMR 2013</h2>
        <hr>
        
        <table  class="table  table-condensed table-hover">
            <thead>
                <th align="center">Daerah</th>
                <th align="center">Keterangan</th>
                <th align="center">Realisasi</th>
                <th align="center">No.Rek</th>
                <th align="center">Kredit</th>
                <th align="center">No</th>
            </thead>
            <tbody>
                <?PHP
                $gtotal=0;
                $no=1;
                $sql = "select distinct icabangid_o, real1, norekreal1, bankreal1 from $tmpbudgetreq01 order by nama_cabang, real1";
                $tampil = mysqli_query($cnit, $sql);
                while ($r = mysqli_fetch_array($tampil)) {
                    $cabang1=$r['icabangid_o'];
                    $real1=$r['real1'];
                    $norek1=$r['norekreal1'];
                    $bankrek1=$r['bankreal1'];

                    $sql2 = "select * from $tmpbudgetreq01 where icabangid_o='$cabang1' AND real1='$real1' AND norekreal1='$norek1' AND bankreal1='$bankrek1'";
                    $tampil2 = mysqli_query($cnit, $sql2);
                    $sudah="FALSE";
                    $jumlahsub=0;
                    while ($r2 = mysqli_fetch_array($tampil2)) {
                        $cabang=$r2['icabangid_o'];
                        $nmcabang=$r2['nama_cabang'];
                        $keterangan=$r2['keterangan1'];
                        $realisasi=$r2['real1'];
                        $norek=$r2['norekreal1'];
                        $bankrek=$r2['bankreal1'];

                        $ketbanknya="";
                        if (empty($bankrek) AND empty($norek))
                            $ketbanknya="";
                        else
                            $ketbanknya=$bankrek." : ".$norek;


                        $jumlah=0;
                        if (!empty($r2['jumlah'])) {
                            $jumlah=number_format($r2['jumlah'],0,",",",");
                            $jumlahsub = (double)$jumlahsub+$r2['jumlah'];
                        }

                        echo "<tr>";
                        echo "<td>$nmcabang</td>";
                        echo "<td>$keterangan</td>";
                        echo "<td>$realisasi</td>";
                        if ($sudah=="FALSE")
                            echo "<td><b>$ketbanknya</b></td>";
                        else
                            echo "<td></td>";

                        echo "<td align='right'>$jumlah</td>";
                        if ($sudah=="FALSE") {
                            echo "<td align='center'>$no</td>";
                            $no++;
                        }else
                            echo "<td></td>";
                        echo "</tr>";

                        $sudah="TRUE";

                    }
                    $gtotal=(double)$gtotal+(double)$jumlahsub;
                    //subtotal
                    $jumlahnya=number_format($jumlahsub,0,",",",");
                    echo "<tr>";
                    echo "<td colspan=4></td>";
                    echo "<td align='right'><b>$jumlahnya</b></td>";
                    echo "<td>&nbsp;</td>";
                    echo "</tr>";
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                }
                //echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                //grandtotal
                $gtotalnya=number_format($gtotal,0,",",",");
                echo "<tr style='background-color:#ffcc99;'>";
                echo "<td colspan=2></td>";
                echo "<td colspan=2 align='center'><b>GRAND TOTAL</b></td>";
                echo "<td align='right'><b>$gtotalnya</b></td>";
                echo "<td>&nbsp;</td>";
                echo "</tr>";

                echo "</table>";
                echo "<br/>&nbsp;";


                echo "<table width='100%' border='0px' style='font-size: 11px; font-weight: bold;'>";
                echo "<tr align='center'>";
                echo "<td>Dibuat oleh,</td><td colspan=2>Mengetahui,</td><td>Disetujui,</td>";
                echo "</tr>";

                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
                echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";

                echo "<tr align='center'>";
                echo "<td>DESI RATNA DEWI</td><td>SAIFUL RAHMAT</td><td>FARIDA SOEWANTO</td><td>IRA BUDISUSETYO</td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
        
        <p align='center'>
            <!-- kode untuk menampilkan tombol print dan saat di klik 
            akan membuka printer dialog -->
            <a href="umr2013_cetak.php" cls='btn' onClick="window.print();return false">
                <i class='icon-print'></i>Cetak </a>
        </p>
    </div>
        
    </body>
</html>

