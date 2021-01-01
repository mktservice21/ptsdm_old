<!--
<script type="text/javascript">     
    function PrintDiv() {    
       var divToPrint = document.getElementById('divToPrint');
       var popupWin = window.open('', '_blank', 'width=800,height=700');
       popupWin.document.open();
       popupWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</html>');
        popupWin.document.close();
            }
</script>
<div>
  <input type="button" value="print" onclick="PrintDiv();" />
</div>
-->

<?PHP
    //echo "<div id='divToPrint'>";
?>
 
<?php
    session_start();
    if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
    
?>

<html>
    <head>
        <title>Print SPD <?PHP echo $printdate." ".$jamnow; ?></title>
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
                border-collapse: collapse; 
                border-spacing: 0;
                font-size: 11px;
                border: 0px solid #000;
            }

            table.example_2 td, table.example_2 th {
                border: 0px solid #000; /* No more visible border */
                height: 28px;
                transition: all 0.3s;  /* Simple transition for hover effect */
                padding: 5px;
            }

            table.example_2 th {
                background: #DFDFDF;  /* Darken header a bit */
                font-weight: bold;
            }

            table.example_2 td {
                
            }

            table {
                font-family: "Times New Roman", Times, serif;
                font-size: 12px;
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
                
                $nospd=$_GET['brid'];
                
                
                $userid=$_SESSION['IDCARD'];
                $now=date("mdYhis");
                $tmp01 =" dbtemp.DSETHZVC01_".$userid."_$now ";
                $tmp02 =" dbtemp.DSETHZVC02_".$userid."_$now ";
                
                $query = "select tglspd, CASE WHEN IFNULL(a.divisi,'')='' THEN 'ETHICAL' ELSE a.divisi END as divisi, 
                    a.nomor, a.kodeid, b.nama, a.subkode, b.subnama, a.nodivisi, tgl, a.jumlah, a.stsnonaktif  
                    from dbmaster.t_suratdana_br a LEFT JOIN dbmaster.t_kode_spd b on a.kodeid=b.kodeid 
                    and a.subkode=b.subkode
                    WHERE a.stsnonaktif<>'Y' AND a.nomor='$nospd'";
                $query = "create TEMPORARY table $tmp01 ($query)"; 
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                $query = "select * from $tmp01";
                $query = "create TEMPORARY table $tmp02 ($query)"; 
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
            
                
                $query = "INSERT INTO $tmp01 (divisi, nomor, kodeid, nama, subkode, subnama)
                        select '' as divisi, '$nospd' as nomor, kodeid, nama, subkode, subnama 
                        from dbmaster.t_kode_spd WHERE CONCAT(kodeid,subkode) NOT IN 
                        (SELECT CONCAT(kodeid,subkode) from $tmp02) AND kodeid NOT IN ('3')";
                //mysqli_query($cnmy, $query);
                //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
                
                
                
                $query = "select tglspd from $tmp01 LIMIT 1";
                $result = mysqli_query($cnmy, $query);
                $ro = mysqli_fetch_array($result);
                $tglspd="";
                if (!empty($ro['tglspd']))
                    $tglspd=date("d F Y", strtotime($ro['tglspd']));
                
            ?>
            <table id='example_2' class='table table-striped table-bordered example_2' width="100%" border="0px">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="center"><?PHP echo "<b>No. $nospd</b>"; ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="center"><?PHP echo "<b>Jakarta, $tglspd</b>"; ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            
            <table>
                <tr><td>Kepada Yth.</td></tr>
                <tr><td><b>Sdr. Vanda / Lina (Accounting)</b></td></tr>
                <tr><td>PT.SDM - Surabaya</td></tr>
            </table>
            
            
            <br/>&nbsp;
            <table id='example_2' class='table table-striped table-bordered example_2' width="100%" border="0px">
                <tbody class='inputdatauc'>
                <?PHP
                    $qtotal=0;
                    $llewat=false;
                    $query = "select distinct kodeid, nama from $tmp01 ORDER BY 1";
                    $result = mysqli_query($cnmy, $query);
                    WHILE ($r = mysqli_fetch_array($result)) {
                        $pkode=$r['kodeid'];
                        $pnama=$r['nama'];
                        
                        if ($pkode=="02") $pnama = "KLAIM - PETTY CASH 1,1 M";
                        
                        $totalkodeid=0;
                        $query = "select sum(jumlah) jumlah from $tmp01 WHERE stsnonaktif<>'Y' AND "
                                . " nomor='$nospd' AND kodeid='$pkode'";
                        $tampil = mysqli_query($cnmy, $query);
                        $t = mysqli_fetch_array($tampil);
                        $totalkodeid=$t['jumlah'];
                        $qtotal=$qtotal+$totalkodeid;
                        $totalkodeid=number_format($totalkodeid,0);
                        
                        if ($llewat==true) {
                            echo "<tr>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "<td nowrap></td>";
                            echo "</tr>";
                        }
                        
                        echo "<tr>";
                        echo "<td><b>$pnama</b></td>";
                        echo "<td>&nbsp;</td>";
                        if ($llewat==false) {
                            $llewat=true;
                            echo "<td><b>No Divisi</b></td>";
                        }else{
                            echo "<td>&nbsp;</td>";
                        }
                        echo "<td><b>Rp</b></td>";
                        echo "<td nowrap align='right'><b>$totalkodeid</b></td>";
                        echo "</tr>";
                        /*
                        $query = "select CASE WHEN IFNULL(a.divisi,'')='' THEN 'ETHICAL' ELSE a.divisi END as divisi, 
                            a.nomor, a.kodeid, b.nama, a.subkode, b.subnama, a.nodivisi, tgl, a.jumlah 
                            from dbmaster.t_suratdana_br a LEFT JOIN dbmaster.t_kode_spd b on a.kodeid=b.kodeid 
                            and a.subkode=b.subkode
                            WHERE a.stsnonaktif<>'Y' AND a.nomor='$nospd' AND a.kodeid='$pkode' 
                            ORDER BY 3, 5, 1, 7";
                        */
                        $query = "select a.divisi, 
                            a.nomor, a.kodeid, a.nama, a.subkode, a.subnama, a.nodivisi, a.tgl, a.jumlah 
                            from $tmp01 a 
                            WHERE a.stsnonaktif<>'Y' AND a.nomor='$nospd' AND a.kodeid='$pkode' 
                            ORDER BY 3, 5, 1, 7";

                        $result2 = mysqli_query($cnmy, $query);
                        while ($row = mysqli_fetch_array($result2)){

                            $psubkode=$row['subkode'];
                            $psubnama=$row['subnama'];
                            $pdivisi=$row['divisi'];
                            $pnodivisi=$row['nodivisi'];
                            $pjumlah=$row['jumlah'];
                            $pjumlah=number_format($pjumlah,0);
                            
                            if ($pkode=="2" AND $psubkode=="29"){
                                //$pnodivisi=date("d F Y", strtotime($row['tgl']));
                            }
                            
                            if ($pkode=="1" AND $psubkode=="04"){
                                $pdivisi="";
                            }
                            
                            if ($pkode=="2" AND ($psubkode=="22" OR $psubkode=="23")){
                                $pdivisi="";
                            }
                            
                            if ((double)$pjumlah==0 AND empty($pnodivisi)) {
                                echo "<tr>";
                                echo "<td nowrap>$psubnama $pdivisi</td>";
                                echo "<td align='center'>:</td>";
                                echo "<td nowrap></td>";
                                echo "<td></td>";
                                echo "<td nowrap align='right'></td>";
                                echo "</tr>";
                            }else{
                                echo "<tr>";
                                echo "<td nowrap>$psubnama $pdivisi</td>";
                                echo "<td align='center'>:</td>";
                                echo "<td nowrap>$pnodivisi</td>";
                                echo "<td>Rp</td>";
                                echo "<td nowrap align='right'>$pjumlah</td>";
                                echo "</tr>";
                            }
                        }
                        
                    }
                    /*
                    echo "<tr>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "<td nowrap></td>";
                    echo "</tr>";
                    */
                    $qtotal=number_format($qtotal,0);
                    echo "<tr>";
                    echo "<td nowrap><b>TOTAL</b></td>";
                    echo "<td align='center'>:</td>";
                    echo "<td nowrap>&nbsp;</td>";
                    echo "<td>Rp</td>";
                    echo "<td nowrap align='right'><b>$qtotal</b></td>";
                    echo "</tr>";
                ?>
                </tbody>
            </table>
            
            <br/>&nbsp;
            <table>
                <tr><td>Rincian Terlampir.</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>Dibuat oleh :</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>&nbsp;</td></tr>
                <tr><td>Marianne</td></tr>
            </table>
            

        </div>
<?PHP
hapusdata:
    mysqli_query($cnmy, "drop temporary table $tmp01");
    mysqli_query($cnmy, "drop temporary table $tmp02");
?>
    </body>
</html>

<?PHP
    //echo "</div>";
?>

