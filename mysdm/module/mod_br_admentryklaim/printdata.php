<?php
    session_start();

    if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    include "config/library.php";
    
    $pidkodeinput=$_GET['brid'];
    $prppettycash=0;
    
    
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
    
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpinptkscbdt00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpinptkscbdt01_".$puserid."_$now ";
    
    
    $query = "SELECT a.region, a.pengajuan, a.klaimid as klaimid, a.distid as distid, d.nama AS nama_distributor, "
            . " a.jumlah, a.pembulatan, a.jmlkuranglebih, a.dpp, a.ppn, a.pph, a.ppn_rp, a.pph_rp, "
            . " a.realisasi1, a.tgl, a.bulan, a.periode1, a.periode2, "
            . " a.aktivitas1 as aktivitas1, a.aktivitas2 as aktivitas2, a.ketkuranglebih, "
            . " a.karyawanid, c.nama nama_karyawan, b.gambar, "
            . " b.atasan4, e.nama nama_atasan4, b.tgl_atasan4, b.gbr_atasan4, "
            . " b.atasan5, f.nama nama_atasan5, b.tgl_atasan5, b.gbr_atasan5, a.jenisklaim, a.batal, a.alasan_batal "
            . " from hrd.klaim as a "
            . " LEFT JOIN dbttd.klaim_ttd b on a.klaimid=b.klaimid "
            . " LEFT JOIN hrd.karyawan c on a.karyawanid=c.karyawanid "
            . " LEFT JOIN MKT.distrib0 d ON a.distid = d.Distid "
            . " LEFT JOIN hrd.karyawan e on b.atasan4=e.karyawanid "
            . " LEFT JOIN hrd.karyawan f on b.atasan5=f.karyawanid "
            . " WHERE a.klaimid='$pidkodeinput'";
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp00 SET nama_karyawan='Ervianty' WHERE karyawanid='0000000144'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.klaimid as klaimid, b.distid as distid, a.idcab, c.nama_cabang, a.nm_cab, a.jumlah1, a.jumlah2, a.jumlah3, a.notes, "
            . " a.jumlahsusulan, a.periodesusulan, a.notes_susulan "
            . " from hrd.klaim_d a "
            . " JOIN $tmp00 b on a.klaimid=b.klaimid "
            . " LEFT JOIN dbmaster.t_klaim_cab_dist c on a.idcab=c.idcab AND b.distid=c.distid"
            . " WHERE a.klaimid='$pidkodeinput'";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 SET nama_cabang=nm_cab WHERE IFNULL(nama_cabang,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    $pkodeinput="700-02-07";
    $ptotrpdpp=0;
    $pppnrpklaim=0;
    $pppnrpreal=0;
    $pppnrptolak=0;
    $ptotppnrptolak=0;
    
    $query = "select * FROM $tmp00";
    $tampilk=mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampilk);
    $pidpengaju=$row['karyawanid'];
    $ptglajukan=$row['tgl'];
    $pbln=$row['bulan'];
    
    $pjumlahminta=$row['jumlah'];
    $ptotrpdpp=$row['dpp'];
    $pjmlrpppn=$row['ppn_rp'];
    $pjmlrppph=$row['pph_rp'];
    
    $pjmlkuranglebih=$row['jmlkuranglebih'];
    $pbulatreal=$row['pembulatan'];
    $pjmlppn=$row['ppn'];
    $pjmlpph=$row['pph'];
    
    $pregion=$row['region'];
    $pdivpengajuan=$row['pengajuan'];


    $pjenisklaim=$row['jenisklaim'];
    $pnamajenisklm="";
    if ($pjenisklaim=="S") $pnamajenisklm="SDM ONLINE";
    elseif ($pjenisklaim=="D") $pnamajenisklm="SKS ONLINE";
    
    
    $pnamapengaju=$row['nama_karyawan'];
    $pketerangan=$row['aktivitas1'];
    $pketerangan2=$row['aktivitas2'];
    $pketerangankl=$row['ketkuranglebih'];
    
    $pbatalbr= $row["batal"];
    $pbatalalasan= $row["alasan_batal"];
    
    $pnamarealisasi="";
    $pnamadistributor=$row['nama_distributor'];
    $pnmreal=$row['realisasi1'];
    
    if (TRIM($pnmreal)!=TRIM($pnamadistributor)) $pnamarealisasi=$pnmreal;
    
    
    $pptglatasan4=$row['tgl_atasan4'];
    $pptglatasan5=$row['tgl_atasan5'];
    
    $ptglajukan = date("d F Y", strtotime($ptglajukan));
    $pbulan = date("F Y", strtotime($pbln));
    
    if ($pptglatasan4=="0000-00-00 00:00:00") $pptglatasan4="";
    if ($pptglatasan5=="0000-00-00 00:00:00") $pptglatasan5="";
            
    $patasan4=$row['atasan4'];
    $nmatasan4 = $row['nama_atasan4'];
    $patasan5=$row['atasan5'];
    $nmatasan5 = $row['nama_atasan5'];
    
    $gambar=$row['gambar'];
    $gbr4=$row['gbr_atasan4'];
    $gbr5=$row['gbr_atasan5'];
         
    if (empty($patasan5)) {
        $patasan5="0000002403";
        $nmatasan5="EVI KOSINA SANTOSO";
    }
        
    if (empty($patasan4)) {
        if ($pregion=="B") {
            $patasan4="0000000158";
            $nmatasan4="ANAK NIAS LASE";
        }elseif ($pregion=="T") {
            $patasan4="0000000159";
            $nmatasan4="SOESILO";
        }else{
            if ($pdivpengajuan=="OTC" OR $pdivpengajuan=="CHC") {
                $patasan4="0000001851";
                $nmatasan4="MUHAMMAD ASYKUR ROHMAT S.AG";
            }
        }
    }
    
    $milliseconds = round(microtime(true) * 1000);
    $now_fil=date("mdYhis").$milliseconds;

    $namapengaju="";
    $namagsm="";
    $namadir="";
    $gmrheight = "80px";
    
    if (empty($pptglatasan4) OR empty($nmatasan4)) $gbr4="";
    if (empty($pptglatasan5) OR empty($nmatasan5)) $gbr5="";
    
    if (!empty($gambar)) {
        $data="data:".$gambar;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju="img_".$pidkodeinput."BRKLPI_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
    }
    
    if (!empty($gbr4)) {
        $data="data:".$gbr4;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namagsm="img_".$pidkodeinput."BRKLPG_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namagsm, $data);
    }
    
    
    if (!empty($gbr5)) {
        $data="data:".$gbr5;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namadir="img_".$pidkodeinput."BRKLPD_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namadir, $data);
    }
    
    if (empty($pptglatasan4)) $namagsm="";
    if (empty($pptglatasan5)) $namadir="";
    
    
    
    
?>

<HTML>
<HEAD>
    <title>Budget Request Klaim Discount <?PHP echo $printdate." ".$jamnow; ?></title>
    <meta http-equiv="Expires" content="Mon, 01 Sep 2018 1:00:00 GMT">
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
            font-size: 12px;
            width: 97%;
        }


        #kotakjudul {
            border: 0px solid #000;
            width:100%;
            height: 1.1cm;
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
            font-size: 13px;
        }
        h3 {
            font-size: 18px;
        }
    </style>
</HEAD>
<BODY>
    
    <center>
        <h3>
            <?PHP
                echo "BUDGET REQUEST";
            ?>
        </h3>
        <?PHP
            if ($pbatalbr=="Y") {
                echo "<br/><span style='color:red;'>BATAL ($pbatalalasan)</span>";
            }
        ?>
    </center>
    <hr/>
    <div id="kotakjudul">
        <div id="isikiri">
            <table class='tjudul' width='100%'>
                <tr><td>Kode</td><td>:</td><td nowrap><?PHP echo "<b>$pkodeinput</b>"; ?></td></tr>
                <tr><td>ID</td><td>:</td><td nowrap><?PHP echo "<b>$pidkodeinput</b>"; ?></td></tr>
                <tr><td>Aktivitas</td><td>:</td><td nowrap><?PHP echo "$pketerangan"; ?></td></tr>
                <tr><td>Distributor</td><td>:</td><td nowrap><?PHP echo "$pnamadistributor"; ?></td></tr>
                <?PHP if (!empty($pnamarealisasi)) { ?>
                    <tr><td>Realisasi</td><td>:</td><td nowrap><?PHP echo "$pnamarealisasi"; ?></td></tr>
                <?PHP } ?>
                <tr><td>Periode</td><td>:</td><td nowrap><?PHP echo "$pbulan"; ?></td></tr>
                <?PHP
                if (!empty($pnamajenisklm)) {
                    echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td nowrap>$pnamajenisklm</td></tr>";
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
        <thead>
            <tr>
                <th width='30%' >CABANG</th>
                <th width='10%' align="center">NILAI KLAIM DIST.</th>
                <th width='10%' align="center">NILAI REALISASI</th>
                <th width='10%' align="center">TOLAKAN</th>
                <th width='10%' align="center">REKLAIM</th>
                <th width='10%' align="center">NOTES</th>
            </tr>
        </thead>
        <tbody class='inputdatauc'>
            <?PHP
            $ptotrpklaim=0;
            $ptotrpreal=0;
            $ptotrptolak=0;
            $ptotrpsusulan=0;
            $no=1;
            $query = "select * from $tmp01 order by nama_cabang";
            $tampil=mysqli_query($cnmy, $query);
            while ($nrow= mysqli_fetch_array($tampil)){
                $pnmcabang=$nrow['nama_cabang'];
                $pjmlklaim=$nrow['jumlah1'];
                $pjmlreal=$nrow['jumlah2'];
                $pjmltolak=$nrow['jumlah3'];
                $pnotespldt=$nrow['notes'];
                
                $prpsusulan=$nrow['jumlahsusulan'];
                $pnotessusulan=$nrow['notes_susulan'];
                $pblnsusulan=$nrow['periodesusulan'];
                
                $ptotrpklaim=(DOUBLE)$ptotrpklaim+(DOUBLE)$pjmlklaim;
                $ptotrpreal=(DOUBLE)$ptotrpreal+(DOUBLE)$pjmlreal;
                $ptotrptolak=(DOUBLE)$ptotrptolak+(DOUBLE)$pjmltolak;
                
                if ($pblnsusulan=="0000-00-00") $pblnsusulan="";
                if (!empty($pblnsusulan)) $pblnsusulan =date("F Y", strtotime($pblnsusulan));
                
                if ((DOUBLE)$prpsusulan<>0) {
                    if (!empty($pnotessusulan)) {
                        if (!empty($pnotespldt)) {
                            $pnotespldt=$pnotespldt.", Reklaim : ".$pnotessusulan." Periode ".$pblnsusulan;
                        }else{
                            $pnotespldt="Reklaim : ".$pnotessusulan." Periode ".$pblnsusulan;
                        }
                    }else{
                        if (!empty($pnotespldt)) {
                            $pnotespldt=$pnotespldt.", Reklaim : Periode ".$pblnsusulan;
                        }else{
                            $pnotespldt="Reklaim : Periode ".$pblnsusulan;
                        }
                    }
                }
                
                $ptotrpsusulan=(DOUBLE)$ptotrpsusulan+(DOUBLE)$prpsusulan;
                
                $pjmlklaim=number_format($pjmlklaim,0,",",",");
                $pjmlreal=number_format($pjmlreal,0,",",",");
                $pjmltolak=number_format($pjmltolak,0,",",",");
                
                $prpsusulan=number_format($prpsusulan,0,",",",");
                
                
                echo "<tr>";
                echo "<td >$pnmcabang</td>";
                echo "<td nowrap align='right'>$pjmlklaim</td>";
                echo "<td nowrap align='right'>$pjmlreal</td>";
                echo "<td nowrap align='right'>$pjmltolak</td>";
                echo "<td nowrap align='right'>$prpsusulan</td>";
                echo "<td >$pnotespldt</td>";
                echo "</tr>";
                
                $no++;
            }
            
            /*
            if (!empty($pjmlkuranglebih) AND $pjmlkuranglebih!="0") {
                $pjmlkuranglebih_=number_format($pjmlkuranglebih,0,",",",");
                echo "<tr>";
                echo "<td nowrap>$pketerangankl</td>";
                echo "<td nowrap align='right'></td>";
                echo "<td nowrap align='right'>$pjmlkuranglebih_</td>";
                echo "<td nowrap align='right'></td>";
                
                echo "<td nowrap align='right'></td>";
                
                echo "<td nowrap></td>";
                echo "</tr>";
            }
            */
            
            $ptotrpreal=$ptotrpdpp;//disamakan dengan yang diinput
            //$ptotrpreal=(DOUBLE)$ptotrpreal+(DOUBLE)$pjmlkuranglebih;
                
            $pppnrpklaim=(DOUBLE)$ptotrpklaim*(DOUBLE)$pjmlppn/100;
            $pppnrpreal=(DOUBLE)$ptotrpreal*(DOUBLE)$pjmlppn/100;
            
            $ptotppnrpklaim=(DOUBLE)$ptotrpklaim+(DOUBLE)$pppnrpklaim;
            $ptotppnrpreal=(DOUBLE)$ptotrpreal+(DOUBLE)$pppnrpreal;
            
            $ppphrpreal=(DOUBLE)$ptotrpreal*(DOUBLE)$pjmlpph/100;
            
            $pgrdrpreal=(DOUBLE)$ptotppnrpreal-(DOUBLE)$ppphrpreal+(DOUBLE)$pbulatreal;
            
            $ptotrpklaim=number_format($ptotrpklaim,0,",",",");
            $ptotrpreal=number_format($ptotrpreal,0,",",",");
            $ptotrptolak=number_format($ptotrptolak,0,",",",");
            
            //$pppnrpklaim=number_format($pppnrpklaim,0,",",",");
            //$pppnrpreal=number_format($pppnrpreal,0,",",",");
            //$pppnrptolak=number_format($pppnrptolak,0,",",",");
            
            $pppnrpklaim=number_format($pppnrpklaim,2,".",",");
            
            $pppnrpreal=$pjmlrpppn;//disamakan dengan yang diinput
            $pppnrpreal=number_format($pppnrpreal,2,".",",");
            
            $pppnrptolak=number_format($pppnrptolak,2,".",",");
            
            
            //$ptotppnrpklaim=number_format($ptotppnrpklaim,0,",",",");
            //$ptotppnrpreal=number_format($ptotppnrpreal,0,",",",");
            //$ptotppnrptolak=number_format($ptotppnrptolak,0,",",",");
            
            $ptotppnrpklaim=number_format($ptotppnrpklaim,2,".",",");
            $ptotppnrpreal=number_format($ptotppnrpreal,2,".",",");
            $ptotppnrptolak=number_format($ptotppnrptolak,2,".",",");
            
            
            
            $ppphrpreal=$pjmlrppph;//disamakan dengan yang diinput
            //$ppphrpreal=number_format($ppphrpreal,0,",",",");
            $ppphrpreal=number_format($ppphrpreal,2,".",",");
            
            
            $pjumlahminta=$pgrdrpreal;//disamakan dengan yang diinput
            $pgrdrpreal=number_format($pgrdrpreal,0,",",",");
            
            $ptotrpsusulan=number_format($ptotrpsusulan,0,",",",");
            
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap>&nbsp;</td>";
            echo "<td nowrap align='right'>&nbsp;</td>";
            echo "<td nowrap align='right'>&nbsp;</td>";
            echo "<td nowrap align='right'>&nbsp;</td>";
            
            echo "<td nowrap align='right'>&nbsp;</td>";
            
            echo "<td nowrap>&nbsp;</td>";
            echo "</tr>";
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap>Total</td>";
            echo "<td nowrap align='right'>$ptotrpklaim</td>";
            echo "<td nowrap align='right'>$ptotrpreal</td>";
            echo "<td nowrap align='right'>$ptotrptolak</td>";
            
            echo "<td nowrap align='right'>$ptotrpsusulan</td>";
            
            echo "<td nowrap></td>";
            echo "</tr>";
            
            

            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap>PPN 10%</td>";
            echo "<td nowrap align='right'>$pppnrpklaim</td>";
            echo "<td nowrap align='right'>$pppnrpreal</td>";
            echo "<td nowrap align='right'></td>";
            
            echo "<td nowrap align='right'></td>";
            
            echo "<td nowrap></td>";
            echo "</tr>";
            
            
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap>Total</td>";
            echo "<td nowrap align='right'>$ptotppnrpklaim</td>";
            echo "<td nowrap align='right'>$ptotppnrpreal</td>";
            echo "<td nowrap align='right'></td>";
            
            echo "<td nowrap align='right'></td>";
            
            echo "<td nowrap></td>";
            echo "</tr>";
            
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap>PPH 2%</td>";
            echo "<td nowrap align='right'></td>";
            echo "<td nowrap align='right'>$ppphrpreal</td>";
            echo "<td nowrap align='right'></td>";
            
            echo "<td nowrap align='right'></td>";
            
            echo "<td nowrap></td>";
            echo "</tr>";
            
            echo "<tr style='font-weight:bold;'>";
            echo "<td nowrap>Grand Total Discount</td>";
            echo "<td nowrap align='right'></td>";
            echo "<td nowrap align='right'>$pgrdrpreal</td>";
            echo "<td nowrap align='right'></td>";
            
            echo "<td nowrap align='right'></td>";
            
            echo "<td nowrap></td>";
            echo "</tr>";
            
            
            ?>
        </tbody>
    </table>
    <br/>&nbsp;
    <?PHP
        //echo "Note : $pketerangan<br/>&nbsp;";
        //echo "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; $pketerangan2<br/>&nbsp;";
        
        
    ?>
      
    
    <center>
        <table class='tjudul' width='100%'>
            <?PHP
            $plewatatasan=false;
            echo "<tr>";
                
    
            
            
                echo "<td align='center'>Yang Membuat :";
                if (!empty($namapengaju)) {
                    echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                }else{
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                }
                echo "<b><u>$pnamapengaju</u></b>";
            
                echo "</td>";
                
                echo "<td align='center'>Menyetujui :";
                if (!empty($namagsm)) {
                    echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                }else{
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                }
                echo "<b><u>$nmatasan4</u></b>";

                echo "</td>";
                
                
                echo "<td align='center'>Disetujui :";
                if (!empty($namadir)) {
                    echo "<br/><img src='images/tanda_tangan_base64/$namadir' height='$gmrheight'><br/>";
                }else{
                    echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                }
                echo "<b><u>$nmatasan5</u></b>";

                echo "</td>";
                
                
            echo "</tr>";
            ?>
        </table>
    </center>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
</BODY>
</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_close($cnmy);
?>