<?php
    function BuatFormatNum($prp, $ppilih) {
        if (empty($prp)) $prp=0;
        
        $numrp=$prp;
        if ($ppilih=="1") $numrp=number_format($prp,0,",",",");
        elseif ($ppilih=="2") $numrp=number_format($prp,0,".",".");
            
        return $numrp;
    }
    
    
    session_start();

    if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    include "config/library.php";
    
    $ppilformat="1";
    $pviewdate=date("d/m/Y H:i");
    
    $pidpo=$_GET['brid'];
    
    $gmrheight = "80px";
    
    
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
    
    $pidgroup=$_SESSION['GROUP'];
    $puserid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmpinptpodt00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmpinptpodt01_".$puserid."_$now ";
    
    $query = "select idpo, karyawanid, tanggal, kdsupp, notes, idbayar, tglkirim, note_kirim, status_bayar, "
            . " apv_mgr, tgl_mgr, dir1, dir2, tgl_dir1, tgl_dir2, "
            . " ppn as ppn_h, ppnrp as ppnrp_h, disc, discrp, pembulatan, totalrp, dpp, pph, pph_rp, pph_jns, id_alamat "
            . " from dbpurchasing.t_po_transaksi WHERE "
            . " IFNULL(stsnonaktif,'')<>'Y' AND idpo='$pidpo'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $ptanggal=$row['tanggal'];
    $ptanggal = date("d F Y", strtotime($ptanggal));
    
    $pstsbayar=$row['status_bayar'];
    $premarks=$row['notes'];
    $pkdvendor=$row['kdsupp'];
    $pidbayar=$row['idbayar'];
    $pidkryid=$row['karyawanid'];
    $pidmanager=$row['apv_mgr'];
    $piddir1=$row['dir1'];
    $piddir2=$row['dir2'];
    
    $pdpprp=$row['dpp'];
    $pdisc_h=$row['disc'];
    $pdiscrp_h=$row['discrp'];
    
    $ppphjns_h=$row['pph_jns'];
    $ppph_h=$row['pph'];
    $ppphrp_h=$row['pph_rp'];
    
    $pppn_h=$row['ppn_h'];
    $pppnrp_h=$row['ppnrp_h'];
    
    
    $pidalamatkirim=$row['id_alamat'];
    $query = "select id_alamat, nama, alamat1, alamat2, kel_kec, kota, provinsi, kodepos, telp, hp, kontakperson from dbpurchasing.t_alamat_kirim WHERE id_alamat='$pidalamatkirim'";
    $tampilak= mysqli_query($cnmy, $query);
    $akr= mysqli_fetch_array($tampilak);
    $pnamaalmt=$akr['nama'];
    $pnalamat1=$akr['alamat1'];
    $pnalamat2=$akr['alamat2'];
    $pkelkec=$akr['kel_kec'];
    $pkota=$akr['kota'];
    $pprovinsi=$akr['provinsi'];
    $pkodepos=$akr['kodepos'];
    $ptelp=$akr['telp'];
    $phpalmt=$akr['hp'];
    $pkontakperson=$akr['kontakperson'];
    
    $palamatkirim01=$pnalamat1;
    $palamatkirim02=$pkelkec.", ".$pkota.", ".$pkodepos." ".$pprovinsi;
    
    if (empty($palamatkirim01)) {
        $palamatkirim01="Jl. Paseban Raya No. 21";
        $palamatkirim02="Senen, Jakarta Pusat, 10440 DKI Jakarta";
        $ptelp="(021) 3162414";
    }
    
    
    $query = "select karyawanid as karyawanid, nama as nama_karyawan from hrd.karyawan WHERE karyawanid='$pidkryid'";
    $tampilk= mysqli_query($cnmy, $query);
    $krow= mysqli_fetch_array($tampilk);
    $pnamakry=$krow['nama_karyawan'];
    
    $query = "select karyawanid as karyawanid, nama as nama_mgr from hrd.karyawan WHERE karyawanid='$pidmanager'";
    $tampilmg= mysqli_query($cnmy, $query);
    $mg= mysqli_fetch_array($tampilmg);
    $nmatasanmgr=$mg['nama_mgr'];
    
    $query = "select karyawanid as karyawanid, nama as nama_dir1 from hrd.karyawan WHERE karyawanid='$piddir1'";
    $tampilk= mysqli_query($cnmy, $query);
    $krow= mysqli_fetch_array($tampilk);
    $nmatasandir1=$krow['nama_dir1'];
    
    $query = "select karyawanid as karyawanid, nama as nama_dir2 from hrd.karyawan WHERE karyawanid='$piddir2'";
    $tampilk= mysqli_query($cnmy, $query);
    $krow= mysqli_fetch_array($tampilk);
    $nmatasandir2=$krow['nama_dir2'];
    
    
    
    $query = "select idbayar, nama_bayar from dbpurchasing.t_jenis_bayar WHERE idbayar='$pidbayar'";
    $tampilb= mysqli_query($cnmy, $query);
    $brow= mysqli_fetch_array($tampilb);
    $ptipebayar=$brow['nama_bayar'];
    
    $query = "select KDSUPP, NAMA_SUP, ALAMAT, TELP, KEYPERSON, AKTIF from dbmaster.t_supplier WHERE KDSUPP='$pkdvendor'";
    $tampilv= mysqli_query($cnmy, $query);
    $vrow= mysqli_fetch_array($tampilv);
    
    $pnamavendor=$vrow['NAMA_SUP'];
    $palamatvendor=$vrow['ALAMAT'];
    $ptelpendor=$vrow['TELP'];
    $pkontakvendor=$vrow['KEYPERSON'];
    $paktifvendor=$vrow['AKTIF'];
    
    
    $query = "select idpo, gambar, gbr_mgr, gbr_dir1, gbr_dir2 from dbttd.t_po_transaksi_ttd WHERE idpo='$pidpo'";
    $tampilg= mysqli_query($cnmy, $query);
    $grow= mysqli_fetch_array($tampilg);
    
    
    $namapengaju="";
    $gambar=$grow['gambar'];
    
    $namamgr="";
    $gambarmgr=$grow['gbr_mgr'];
    $namadir1="";
    $gambardir1=$grow['gbr_dir1'];
    $namadir2="";
    $gambardir2=$grow['gbr_dir2'];
    
    if (!empty($gambar)) {
        $data="data:".$gambar;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju="img_".$pidpo."PENGAJUPO_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
    }
    
    if (!empty($gambarmgr)) {
        $data="data:".$gambarmgr;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namamgr="img_".$pidpo."PENGAJUMGRPO_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namamgr, $data);
    }
    
    if (!empty($gambardir1)) {
        $data="data:".$gambardir1;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namadir1="img_".$pidpo."PENGAJUDIR1PO_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namadir1, $data);
    }
    
    if (!empty($gambardir2)) {
        $data="data:".$gambardir2;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namadir2="img_".$pidpo."PENGAJUDIR2PO_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namadir2, $data);
    }
    
    
    $namadirmkt=""; $nmatasandirmkt=""; 
    
?>


<HTML>
<HEAD>
    <title>Purchase Order <?PHP echo $printdate." ".$jamnow; ?></title>
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
            font-size: 13px;
        }
        h3 {
            font-size: 13px;
        }
            

        #tbljudul {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
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
    </style>
</HEAD>


<BODY>
    
    <div id="div1">


        <center>
            <img src="images/logo_sdm.jpg" height="50px">
            <h2>PT SURYA DERMATO MEDICA LABORATORIES</h2>
            Jl. Paseban Raya No. 21 RT. 2 / RW. 2 
            Telp : 021-2305570 Fax : 021-3162417<br/>
            NPWP : 01.122.214.8-631.000
        </center>
        <hr/>
        <center>
            <h3>
                <?PHP
                echo "PURCHASE ORDER";
                ?>
            </h3>
        </center>

        <div id="kotakjudul">
            <div id="isikiri">
                <table class='tjudul' width='100%'>
                    <?PHP
                    echo "<tr><td>PO No</td><td>:</td> <td nowrap><b>$pidpo</b></td></tr>";
                    echo "<tr><td>Tanggal</td><td>:</td> <td nowrap>$ptanggal</td></tr>";
                    ?>
                </table>
            </div>
            <div id="isikanan">

            </div>
            <div class="clearfix"></div>
        </div>
        <br/><div class="clearfix"></div>
        <div id="container">

            <div id="left">
                <table id="tbljudul">
                    <tr><td><u>Vendor</u></td></tr>
                    <tr><td><?PHP echo "$pnamavendor"; ?></td></tr>
                    <tr><td>Alamat : <?PHP echo "$palamatvendor"; ?></td></tr>
                    <tr><td>Telp. : <?PHP echo "$ptelpendor"; ?></td></tr>
                    <tr><td>Kontak Person : <?PHP echo "$pkontakvendor"; ?></td></tr>
                </table>
            </div>

            <div id="right">
                <table id="tbljudul">
                    <tr><td><u>Place of Delivery</u></td></tr>
                    <tr><td><?PHP echo $palamatkirim01; ?></td></tr>
                    <tr><td><?PHP echo $palamatkirim02; ?></td></tr>
                    <tr><td><?PHP echo "Telp. $ptelp"; ?></td></tr>
                </table>
            </div>

        </div>
        <div class="clear"></div>
        <br/>

        Please supply the following items
        <table id='example_2' class='table table-striped table-bordered example_2' width="100%" border="1px">
            <thead>
                <tr>
                    <th width='5%px'>No</th>
                    <th width='30%px'>Description of Goods</th>
                    <th width='5%px'>UoM</th>
                    <th width='5%px'>Qty</th>
                    <th width='5%px'>Unit Price</th>
                    <th width='5%px'>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $ptotalbayar=0;
                $no=1;
                $query = "select a.idpo_d, a.idpo, b.idbarang, b.namabarang, 
                    b.idbarang_d, b.spesifikasi1, b.spesifikasi2, 
                    b.ccyid, b.jumlah, b.satuan, b.harga, b.ppn, b.ppnrp, 
                    b.disc, b.discrp, b.pembulatan, b.totalrp, b.aktif
                    from dbpurchasing.t_po_transaksi_d as a 
                    JOIN dbpurchasing.t_pr_transaksi_po as b on a.idpr_po=b.idpr_po
                    where IFNULL(aktif,'')<>'N' AND idpo='$pidpo'";
                $tampil1=mysqli_query($cnmy, $query);
                while ($row1= mysqli_fetch_array($tampil1)){
                    
                    $pnamabrg=$row1['namabarang'];
                    $pspesifikasibrg1=$row1['spesifikasi1'];
                    $psatuan=$row1['satuan'];
                    $pqty=$row1['jumlah'];
                    $pharga=$row1['harga'];
                    $ptotalrp=$row1['totalrp'];
                    
                    $ptotalbayar=(DOUBLE)$ptotalbayar+(DOUBLE)$ptotalrp;
                    
                    $pqty=BuatFormatNum($pqty, $ppilformat);
                    $pharga=BuatFormatNum($pharga, $ppilformat);
                    $ptotalrp=BuatFormatNum($ptotalrp, $ppilformat);
                    
                    
                    echo "<tr>";
                    echo "<td nowrap>$no</td>";
                    echo "<td nowrap><u>$pnamabrg</u><br/>$pspesifikasibrg1</td>";
                    echo "<td nowrap>$psatuan</td>";
                    echo "<td nowrap align='right'>$pqty</td>";
                    echo "<td nowrap align='right'>$pharga</td>";
                    echo "<td nowrap align='right'>$ptotalrp</td>";
                    echo "</tr>";
                    $no++;
                }
                
                if ((DOUBLE)$pdpprp==(DOUBLE)$ptotalbayar) {
                    $pdpprp=0;
                }
                
                if ((DOUBLE)$pdpprp<>0) {
                    $pdpprp=BuatFormatNum($pdpprp, $ppilformat);
                    
                    echo "<tr style='font-weight:bold;'>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap>DPP :</td>";
                    echo "<td nowrap align='right'>$pdpprp</td>";
                    echo "</tr>";
                }
                
                if ((DOUBLE)$pppnrp_h<>0) {
                    $ptotalbayar=(DOUBLE)$ptotalbayar+(DOUBLE)$pppnrp_h;
                    
                    $pppn_h=str_replace(".00", "", $pppn_h);
                    $pppnrp_h=BuatFormatNum($pppnrp_h, $ppilformat);
                    echo "<tr style='font-weight:bold;'>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap>Include PPN $pppn_h% :</td>";
                    echo "<td nowrap align='right'>$pppnrp_h</td>";
                    echo "</tr>";
                }
                
                
                $ptotalbayar=BuatFormatNum($ptotalbayar, $ppilformat);
                
                if ((DOUBLE)$pdiscrp_h<>0) {
                    $pdisc_h=BuatFormatNum($pdisc_h, $ppilformat);
                    $pdiscrp_h=BuatFormatNum($pdiscrp_h, $ppilformat);
                    
                    echo "<tr style='font-weight:bold;'>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap>Discount ($pdisc_h %) :</td>";
                    echo "<td nowrap align='right'>$pdiscrp_h</td>";
                    echo "</tr>";
                }
                
                if ((DOUBLE)$ppphrp_h<>0) {
                    $ppph_h=BuatFormatNum($ppph_h, $ppilformat);
                    $ppphrp_h=BuatFormatNum($ppphrp_h, $ppilformat);
                
                    echo "<tr style='font-weight:bold;'>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap>PPH ($ppph_h %) :</td>";
                    echo "<td nowrap align='right'>$ppphrp_h</td>";
                    echo "</tr>";
                }
                
                echo "<tr style='font-weight:bold;'>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap></td>";
                echo "<td nowrap>TOTAL :</td>";
                echo "<td nowrap align='right'>$ptotalbayar</td>";
                echo "</tr>";
                    
                ?>
            </tbody>
        </table>
        
        <br/>
        <?PHP
            echo "Term of Payment : $ptipebayar<br/>";
            echo "Remarks : $premarks";
        ?>
        <br/><br/>
        
        <center>
            <table class='tjudul' width='100%'>
                <?PHP
                    echo "<tr>";
                    
                        echo "<td align='center'>";
                        echo "Purchasing :";
                        if (!empty($namapengaju)) {
                            echo "<br/><img src='images/tanda_tangan_base64/$namapengaju' height='$gmrheight'><br/>";
                        }else{
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        }
                        echo "<b><u>$pnamakry</u></b>";

                        echo "</td>";
                    
                        echo "<td align='center'>";
                        echo "Approved :";
                        if (!empty($namamgr)) {
                            echo "<br/><img src='images/tanda_tangan_base64/$namamgr' height='$gmrheight'><br/>";
                        }else{
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        }
                        echo "<b><u>$nmatasanmgr</u></b>";

                        echo "</td>";
                        
                        
                        echo "<td align='center'>";
                        echo "Approved :";
                        if (!empty($namadir1)) {
                            echo "<br/><img src='images/tanda_tangan_base64/$namadir1' height='$gmrheight'><br/>";
                        }else{
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        }
                        echo "<b><u>$nmatasandir1</u></b>";

                        echo "</td>";
                        
                        
                    echo "</tr>";
                ?>
            </table>
        </center>
        <br/>
        
        
        <div id="kotakjudul">
            <div id="isikiri">
                <table class='tjudul' width='100%'>
                    <?PHP
                    echo "<tr><td><i>Print Date</i></td><td>:</td> <td nowrap><i>$pviewdate</i></td></tr>";
                    ?>
                </table>
            </div>
            <div id="isikanan">

            </div>
            <div class="clearfix"></div>
        </div>
        
    </div>

    
</BODY>


</HTML>
    
<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_close($cnmy);
?>