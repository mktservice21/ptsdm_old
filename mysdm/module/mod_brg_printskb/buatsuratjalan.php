<?php
session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    
    $pnamalengkapprint=$_SESSION['NAMALENGKAP'];
    $tgl_print = date("d/m/Y");
    $waktu_print = date("H i s");
    
$module=$_GET['module'];
$idmenu=$_GET['idmenu'];
$act=$_GET['act'];
  
    
include "../../config/koneksimysqli.php";
    

$pnidkeluarpilih="";
$pfilteridkeluar="";
foreach ($_POST['chkbox_br'] as $pidkeluarbrg) {
    if (!empty($pidkeluarbrg)) {

        if (strpos($pfilteridkeluar, $pidkeluarbrg)==false) $pfilteridkeluar .="'".$pidkeluarbrg."',";
        if (strpos($pnidkeluarpilih, $pidkeluarbrg)==false) $pnidkeluarpilih .=$pidkeluarbrg.",";

    }
}
    
if (!empty($pfilteridkeluar)) {
    $pfilteridkeluar="(".substr($pfilteridkeluar, 0, -1).")";
    $pnidkeluarpilih=substr($pnidkeluarpilih, 0, -1);
}else{
    $pfilteridkeluar="('')";
}
    
$userid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.TMPGMCTPSKB01_".$userid."_$now ";
    
    

$query = "select d.PILIHAN, b.DIVISIID, a.IDKELUAR, b.ICABANGID, b.AREAID, b.ICABANGID_O, b.AREAID_O,
    a.IDBARANG, e.NAMABARANG, e.IDBRAND, f.NAMA_BRAND,
    c.IDPENERIMA, c.NAMA_PENERIMA, c.ALAMAT1, c.ALAMAT2, c.KOTA, c.PROVINSI, c.HP, c.KODEPOS,
    a.STOCK, a.JUMLAH, b.NOTES from dbmaster.t_barang_keluar_d as a 
    JOIN dbmaster.t_barang_keluar as b on a.IDKELUAR=b.IDKELUAR
    LEFT JOIN dbmaster.t_barang_keluar_kirim as c on a.IDKELUAR=c.IDKELUAR
    JOIN dbmaster.t_divisi_gimick as d on b.DIVISIID=d.DIVISIID
    LEFT JOIN dbmaster.t_barang as e on a.IDBARANG=e.IDBARANG
    LEFT JOIN dbmaster.t_barang_brand as f on e.IDBRAND=f.IDBRAND
    WHERE IFNULL(b.STSNONAKTIF,'')<>'Y' AND a.IDKELUAR IN $pfilteridkeluar";
$query = "create TEMPORARY table $tmp01 ($query)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


$query = "ALTER TABLE $tmp01 ADD COLUMN NAMACABANG VARCHAR(100), ADD COLUMN NAMAAREA VARCHAR(100)"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

$query = "UPDATE $tmp01 as a JOIN MKT.icabang as b on a.ICABANGID=b.icabangid SET a.NAMACABANG=b.nama WHERE a.PILIHAN NOT IN ('OTC', 'OT', 'CHC')"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

$query = "UPDATE $tmp01 as a JOIN MKT.iarea as b on a.ICABANGID=b.icabangid AND a.areaid=b.areaid SET a.NAMAAREA=b.nama WHERE a.PILIHAN NOT IN ('OTC', 'OT', 'CHC')"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }


$query = "UPDATE $tmp01 as a JOIN MKT.icabang_o as b on a.ICABANGID_O=b.icabangid_o SET a.NAMACABANG=b.nama WHERE a.PILIHAN IN ('OTC', 'OT', 'CHC')"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

$query = "UPDATE $tmp01 as a JOIN MKT.iarea_o as b on a.ICABANGID_O=b.icabangid_o AND a.areaid_o=b.areaid_o SET a.NAMACABANG=b.nama WHERE a.PILIHAN IN ('OTC', 'OT', 'CHC')"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

$query = "UPDATE $tmp01 SET ICABANGID=ICABANGID_O, AREAID=AREAID_O WHERE PILIHAN IN ('OTC', 'OT', 'CHC')"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

$query = "UPDATE $tmp01 SET AREAID='', NAMAAREA='' WHERE IFNULL(AREAID,'')=''"; 
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }

    


?>


<HTML>
<HEAD>
    <TITLE>PRINT SURAT TUGAS</TITLE>
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
        @media all {
            /* .page-break { display: none; } */
        }

        @media print {
            .page-break { display: block; page-break-before: always; }
        }
        </style>
        
        <style>
            body {
                font-family: "Times New Roman", Times, serif;
                font-size: 13px;
                border: 0px solid #000;
            }
            h2 {
                font-size: 17px;
            }
            h3 {
                font-size: 14px;
            }
            

            #tbljudul {
                font-family: "Times New Roman", Times, serif;
                font-size: 15px;
            }
            #tbljudul .tjudul {
                font-size: 13px;
                width: 97%;
            }
            
            #container {
                width:100%;
                text-align:center;
            }

            #left {
                float:left;
                width:40%;
            }
            #right {
                float:right;
                width:40%;
            }
            .clear { clear: both; }
            
            #datatableprntsjb, #datatableprntsjb th, #datatableprntsjb td {
                border : 1px solid black;
                border-collapse: collapse;
                font-family: "Times New Roman", Times, serif;
                padding:5px;               
            }
            #datatableprntsjb th {
                font-size: 14px;
            }
            
            #datatableprntsjb td {
                font-size: 13px;
            }
        </style>
    
    
</HEAD>


<BODY>
    

            
        <div id="container">
            <h2>SURAT TUGAS ALOKASI</h2>
            <h3>PROMO MATERIAL</h3>
            <div id="left">
                <table id="tbljudul">
                    <tr><td>Kepada Yth. :</td><td><?PHP //echo $tgl_print; ?></td></tr>
                    <tr><td>Tanggal :</td><td><?PHP echo $tgl_print; ?></td></tr>
                </table>
            </div>

            

        </div>
        <div class="clear"></div>


        <br/>&nbsp;

        <table id='datatableprntsjb' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>NAMA PROMO MATERIAL</th>
                    <th>KODE PROMO MATERIAL</th>
                    <th>BRAND</th>
                    <th>JUMLAH</th>
                    <th>KETERANGAN</th>
                    <th>TUJUAN ALOKASI</th>
                    <th>NAMA PENERIMA</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $query = "select distinct PILIHAN from $tmp01 order by PILIHAN";
                $tampil= mysqli_query($cnmy, $query);
                while ($row= mysqli_fetch_array($tampil)) {
                    $ppilihan=$row['PILIHAN'];
                    
                    $query = "select distinct PILIHAN, ICABANGID, NAMACABANG, AREAID, NAMAAREA from $tmp01 WHERE PILIHAN='$ppilihan' order by NAMACABANG, NAMAAREA";
                    $tampil2= mysqli_query($cnmy, $query);
                    while ($row2=mysqli_fetch_array($tampil2)) {
                        $pidcab=$row2['ICABANGID'];
                        $pidarea=$row2['AREAID'];
                        
                        $query = "select distinct IDKELUAR from $tmp01 WHERE PILIHAN='$ppilihan' AND ICABANGID='$pidcab' AND AREAID='$pidarea' order by IDKELUAR";
                        $tampil3= mysqli_query($cnmy, $query);
                        while ($row3=mysqli_fetch_array($tampil3)) {
                            $pidkeluar=$row3['IDKELUAR'];
                            
                            echo "<tr>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap colspan='7'><b>$pidkeluar</b></td>";
                            echo "</tr>";
                                
                            $no=1;
                            $psudahlewat=false;
                            $query = "select * from $tmp01 WHERE PILIHAN='$ppilihan' AND ICABANGID='$pidcab' AND AREAID='$pidarea' AND IDKELUAR='$pidkeluar' order by NAMACABANG, NAMAAREA, NAMA_PENERIMA, IDPENERIMA, NAMA_BRAND, NAMABARANG";
                            $tampil4= mysqli_query($cnmy, $query);
                            while ($row4=mysqli_fetch_array($tampil4)) {
                                $pidbarang=$row4['IDBARANG'];
                                $pnmbarang=$row4['NAMABARANG'];
                                $pnmbrand=$row4['NAMA_BRAND'];
                                $pnotes=$row4['NOTES'];
                                $pnmcabang=$row4['NAMACABANG'];
                                $pnmarea=$row4['NAMAAREA'];
                                $pnmpenerima=$row4['NAMA_PENERIMA'];
                                $palamat1=$row4['ALAMAT1'];
                                $palamat2=$row4['ALAMAT2'];
                                $pkota=$row4['KOTA'];
                                $pkdpos=$row4['KODEPOS'];
                                
                                
                                
                                $ptujuan=$pnmcabang;
                                if (!empty($pnmarea)) $ptujuan=$pnmcabang." - ".$pnmarea;
                                
                                $palamat=$palamat1;
                                if (!empty($palamat2)) $palamat=$palamat1." ".$palamat2;
                                
                                if (!empty($pkota)) $palamat.=" ".$pkota;
                                if (!empty($pkdpos)) $palamat.=" ".$pkdpos;
                                
                                if (!empty($palamat)) {
                                    $ptujuan .="<br/>".$palamat;
                                }
                                
                                
                                $pjml=$row4['JUMLAH'];

                                $pjml=number_format($pjml,0);

                                echo "<tr>";
                                echo "<td nowrap>$no</td>";
                                echo "<td nowrap>$pnmbarang</td>";
                                echo "<td nowrap>$pidbarang</td>";
                                echo "<td nowrap>$pnmbrand</td>";
                                echo "<td nowrap align='right'>$pjml</td>";
                                echo "<td >$pnotes</td>";
                                
                                if ($psudahlewat==false) {
                                    echo "<td >$ptujuan</td>";
                                    echo "<td nowrap>$pnmpenerima</td>";
                                    $psudahlewat=true;
                                }else{
                                    echo "<td nowrap></td>";
                                    echo "<td nowrap></td>";
                                }
                                
                                
                                echo "</tr>";
                                
                                $no++;
                                
                            }
                        
                        }
                        
                    }
                    
                }
                
                
                ?>
            </tbody>
        </table>
        <br/>&nbsp;
        <div align="left">
            <table>
                <tr><td>Surat tugas dikeluarkan oleh :</td></tr>
                <tr><td><?PHP echo $pnamalengkapprint; ?></td></tr>
                <tr><td>Marketing Support</td></tr>
            </table>
        </div>
        <hr/>

        
</BODY>

</HTML>



<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    
    mysqli_close($cnmy);
?>