<?php

session_start();

if (!isset($_SESSION['USERID'])) {
    echo "ANDA HARUS LOGIN ULANG...!!!";
    exit;
}
include "config/koneksimysqli.php";

$pidkeluar=$_GET['nid'];
$query = "select 
    b.PILIHAN,
    a.IDKELUAR,
    a.TGLINPUT,
    a.TANGGAL, 
    a.KARYAWANID,
    d.nama NAMA_KARYAWAN,
    a.DIVISIID,
    b.DIVISINM,
    a.ICABANGID,
    c.nama NAMA_CABANGETH,
    a.ICABANGID_O,
    d.nama NAMA_CABANGOTC,
    a.NOTES,
    a.USERID,
    a.STSNONAKTIF,
    a.SYS_NOW,
    a.PM_APV,
    g.nama PM_NAMA,
    a.PM_TGL,
    a.PM_GBR,
    f.NORESI,
    f.TGLKIRIM,
    f.TGLTERIMA,
    h.nama USERREJECT 
    from dbmaster.t_barang_keluar a JOIN dbmaster.t_divisi_gimick b on a.DIVISIID=b.DIVISIID LEFT JOIN 
    mkt.icabang c on a.ICABANGID=c.iCabangId
    LEFT JOIN mkt.icabang_o d on a.ICABANGID_O=d.icabangid_o 
    LEFT JOIN hrd.karyawan e on a.KARYAWANID=e.karyawanId 
    LEFT JOIN dbmaster.t_barang_keluar_kirim f on a.IDKELUAR=f.IDKELUAR 
    LEFT JOIN hrd.karyawan g on a.PM_APV=g.karyawanid 
    LEFT JOIN hrd.karyawan h on a.USERID=h.karyawanid 
    WHERE a.IDKELUAR='$pidkeluar'";

$tampil= mysqli_query($cnmy, $query);
$row= mysqli_fetch_array($tampil);

$pdivisiid=$row['DIVISIID'];
$ptgl=$row['TANGGAL'];
$ptglminta = date('d F Y', strtotime($ptgl));

$pgrpprod=$row['DIVISINM'];
$ppilihan=$row['PILIHAN'];
$pnamacabang=$row['NAMA_CABANGETH'];
if ($ppilihan=="OT") $pnamacabang=$row['NAMA_CABANGOTC'];

$pnotes=$row['NOTES'];
$pnoresi=$row['NORESI'];
$ptglkirim=$row['TGLKIRIM'];
$ptglterima=$row['TGLTERIMA'];
$ptglpm=$row['PM_TGL'];
$pnamapm=$row['PM_NAMA'];
$pgbrpm=$row['PM_GBR'];
$pnonaktif=$row['STSNONAKTIF'];
$puserreject=$row['USERREJECT'];

if ($ptglkirim=="0000-00-00" OR $ptglkirim=="0000-00-00 00:00:00") $ptglkirim="";
if ($ptglterima=="0000-00-00" OR $ptglterima=="0000-00-00 00:00:00") $ptglterima="";
if ($ptglpm=="0000-00-00" OR $ptglpm=="0000-00-00 00:00:00") $ptglpm="";

if (!empty($ptglkirim)) $ptglkirim = date('d/m/Y', strtotime($ptglkirim));
if (!empty($ptglterima)) $ptglterima = date('d/m/Y', strtotime($ptglterima));

if ($pnonaktif=="Y") $pnotes ="<span style='color:red;'>REJECT ($puserreject) </span> <br/>".$pnotes;

$gmrheight = "80px";
$pgamabrpm="";
if (!empty($pgbrpm)) {
    $data="data:".$pgbrpm;
    $data=str_replace(' ','+',$data);
    list($type, $data) = explode(';', $data);
    list(, $data)      = explode(',', $data);
    $data = base64_decode($data);
    $pgamabrpm="img_".$pidkeluar."PMGMC.png";
    file_put_contents('images/tanda_tangan_base64/'.$pgamabrpm, $data);
}
                
?>

<HTML>
<HEAD>
    <TITLE>PRINT SURAT KELUAR BARANG PER ID</TITLE>
    <meta http-equiv="Expires" content="Mon, 01 Sep 2050 1:00:00 GMT">
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
            h2 {
                font-size: 15px;
            }
            h3 {
                font-size: 20px;
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
        </style>
    
    
</HEAD>
<BODY>
    <center>
        <img src="images/logo_sdm.jpg" height="70px">
        <h2>PT SURYA DERMATO MEDICA LABORATORIES</h2>
    </center>
    <hr/>
    
    <center>
        <h3>
            SURAT KELUAR BARANG (SKB)
        </h3>
    </center>
    
    <table class='tjudul' width='100%'>
        <?PHP
        if (empty($ptglpm)) {
            echo "<tr><td colspan='3'><span style='color:red;'>BELUM APPROVE PM</span></td></tr>";
        }
        ?>
        <tr>
            <td><b>ID</b></td><td>:</td><td><?PHP echo "$pidkeluar"; ?></td>
        </tr>
        <tr>
            <td><b>Tanggal</b></td><td>:</td><td><?PHP echo "$ptglminta"; ?></td>
        </tr>
        <tr>
            <td><b>Cabang</b></td><td>:</td><td><?PHP echo "$pnamacabang"; ?></td>
        </tr>
        <tr>
            <td><b>Grp. Produk</b></td><td>:</td><td><?PHP echo "$pgrpprod"; ?></td>
        </tr>
        <tr>
            <td><b>No. Resi</b></td><td>:</td><td><?PHP echo "$pnoresi"; ?></td>
        </tr>
        <tr>
            <td><b>Tgl. Kirim</b></td><td>:</td><td><?PHP echo "$ptglkirim"; ?></td>
        </tr>
        <tr>
            <td valign="top"><b>Notes</b></td><td valign="top">:</td><td valign="top"><?PHP echo "$pnotes"; ?></td>
        </tr>
    </table>
    
    <br/>&nbsp;
    <table id='example_2' class='table table-striped table-bordered example_2' width="100%" border="1px">
        <tr>
            <th>No</th>
            <th>Kategori</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
        </tr>
        <tbody class='inputdatauc'>
            <?PHP
            $no=1;
            $query = "select a.NOURUT, a.IDKELUAR, b.IDKATEGORI, c.NAMA_KATEGORI, "
                    . " a.IDBARANG, b.NAMABARANG, a.STOCK, a.JUMLAH from "
                    . " dbmaster.t_barang_keluar_d a JOIN "
                    . " dbmaster.t_barang b on a.IDBARANG=b.IDBARANG "
                    . " LEFT JOIN dbmaster.t_barang_kategori c "
                    . " on b.IDKATEGORI=c.IDKATEGORI WHERE a.IDKELUAR='$pidkeluar'";
            $query .=" ORDER BY c.NAMA_KATEGORI, b.NAMABARANG";
            $tampil= mysqli_query($cnmy, $query);
            while ($nrow= mysqli_fetch_array($tampil)) {
                $pnmkategori=$nrow['NAMA_KATEGORI'];
                $pnmbarang=$nrow['NAMABARANG'];
                $pjumlah=$nrow['JUMLAH'];
                
                $pjumlah=number_format($pjumlah,0);
                        
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$pnmkategori</td>";
                echo "<td nowrap>$pnmbarang</td>";
                echo "<td nowrap align='right'>$pjumlah</td>";
                echo "</tr>";
                
                $no++;
            }
            ?>
        </tbody>
    </table>
    
    <br/>&nbsp;<br/>&nbsp;
    <center>
        <table class='' width='100%'>
            <?PHP
            echo "<tr>";
                echo "<td align='center'>";
                echo "Approve By : ";
                if (empty($pgamabrpm)) {
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    echo "(.................................)";
                }else{
                    echo "<br/><img src='images/tanda_tangan_base64/$pgamabrpm' height='$gmrheight'><br/>";
                    echo "<b><u>$pnamapm</u></b>";
                }
                echo "</td>";
            echo "</tr>";
            ?>
        </table>
    </center>
</BODY>
</HTML>
<?PHP
mysqli_close($cnmy);
?>