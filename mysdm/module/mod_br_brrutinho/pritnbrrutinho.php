<?php
    session_start();
    
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
    
    
    $pfilterkrypilih="";
    if ($pidgroup=="50") {
        $query ="select karyawanid as karyawanid from dbmaster.t_karyawan_mkt_dir";
        $tampiln= mysqli_query($cnmy, $query);
        while ($nrow= mysqli_fetch_array($tampiln)) {
            $pkryplid=$nrow['karyawanid'];

            $pfilterkrypilih="'".$pkryplid."',";
        }
    }
    if (!empty($pfilterkrypilih)) $pfilterkrypilih="(".substr($pfilterkrypilih, 0, -1).")";
    else $pfilterkrypilih="('00XXX00')";
    
    
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
            . " e.nama as nama_atasan4 "
            . " from dbmaster.t_brrutin0 as a "
            . " JOIN hrd.karyawan as b on a.karyawanid=b.karyawanid "
            . " LEFT JOIN MKT.icabang as c on a.icabangid=c.icabangid "
            . " LEFT JOIN MKT.iarea as d on a.icabangid=d.icabangid AND a.areaid=d.areaid "
            . " LEFT JOIN hrd.karyawan as e on a.atasan4=e.karyawanid "
            . " WHERE "
            . " a.idrutin='$pidrutin' ";
    if ( ($pidgroup=="28" AND $pidcard=="0000000143") OR $pidgroup=="50" OR $pidgroup=="46" OR $pidgroup=="1" OR $pidgroup=="24" ) {
        if ($pidgroup == "50") {
            $query .=" AND ( a.karyawanid='$pidcard' OR a.karyawanid IN $pfilterkrypilih ) ";
        }
    }else{
        $query .=" AND (a.karyawanid='$pidcard' OR atasan4='$pidcard') ";
    }
    $result = mysqli_query($cnmy, $query);
    $row = mysqli_fetch_array($result);
    
    $tglajukan=date("d-m-Y", strtotime($row['tgl']));
    $pkaryawanid=$row['karyawanid'];
    $pnamakry=$row['nama_kry'];
    $pidcab=$row['icabangid'];
    $pnmcab=$row['nama_cabang'];
    $pidarea=$row['areaid'];
    $pnmarea=$row['nama_area'];
    $pjbtid=$row['jabatanid'];
    
    
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
    
    $gambar=$row['gambar'];
    $gbr4=$row['gbr_atasan4'];
    
    if ($ptglatasan4=="0000-00-00") $ptglatasan4="";
    
    
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
    }
    
    
    $pketperiksa04="Diperiksa oleh :";
    if ($pidatasan4=="0000002403") $pketperiksa04="Menyetujui :";
    
    if ($pkaryawanid=="0000001479") {
        if (empty($gbr4)) $nmatasan4="";
    }
?>

<HTML>
<HEAD>
    <title>Data Biaya Rutin HO <?PHP echo $printdate." ".$jamnow; ?></title>
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
            font-size: 15px;
        }
        h3 {
            font-size: 20px;
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
                echo "BIAYA RUTIN";
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


        <table id='example_2' class='table table-striped table-bordered example_2' width="100%" border="1px">
            <tbody class='inputdatauc'>
                <?PHP
                $ptotal=0;
                $no=1;
                $tampil = mysqli_query($cnmy, "SELECT nobrid, nama, jumlah, qty FROM dbmaster.t_brid where kode='1' and IFNULL(aktif,'')='Y' order by nobrid");
                while ($uc=mysqli_fetch_array($tampil)){

                    $pada=0;
                    $tjml=1;
                    $pbridno=$uc['nobrid'];
                    $pbridnm=$uc['nama'];
                    if (!empty($uc['jumlah'])) $tjml=$uc['jumlah'];

                    $pcari = mysqli_query($cnmy, "SELECT * FROM dbmaster.t_brrutin1 where idrutin='$pidrutin' and nobrid='$pbridno'");
                    $pada = mysqli_num_rows($pcari);
                    if ($pada>0) {

                        $xx=0;
                        while ($c=mysqli_fetch_array($pcari)){

                            $palasaneditfin=$c['alasanedit_fin'];
                            $pnotesdetail=$c['notes'];

                            $pnotenket="";
                            $alsaneditfin="";
                            if (!empty($palasaneditfin)) {
                            }else{
                                if (!empty($pnotesdetail))
                                    $alsaneditfin=$pnotesdetail;
                            }

                            if (!empty($alsaneditfin))
                                $pnotenket="&nbsp; &nbsp; &nbsp; (".$alsaneditfin.")";

                            $pkmdetail="";
                            $npkmdetail="";
                            if ($pbridno=="01" OR $pbridno=="24")  {
                                $pkmdetail=$c['km'];
                                if (empty($pkmdetail)) $pkmdetail=0;
                                $pkmdetail=number_format($pkmdetail,0);
                                if ($pkmdetail<>"0") $npkmdetail =" (KM : $pkmdetail) ";
                            }
                            
                            $rptotal=$c['rptotal'];
                            $rpnilai=$c['rp'];
                            $jmlhari=$c['qty'];
                            
                            if (empty($rptotal)) $rptotal=0;
                            
                            $ptotal=$ptotal+$rptotal;
                            
                            $rptotal=number_format($rptotal,0);
                            $rpnilai=number_format($rpnilai,0);
                            $jmlhari=number_format($jmlhari,0);

                            $satuan="";
                            if ($pbridno=="04" OR $pbridno=="25") $satuan="($jmlhari x $rpnilai)";
                            
                            $pobatuntuk="";
                            if ($pbridno=="11" OR $pbridno=="19") {
                                $ptkes=$c['obat_untuk'];
                                if ($ptkes=="1") $pobatuntuk=" (Istri) ";
                                elseif ($ptkes=="2") $pobatuntuk=" (Anak) ";
                                else $pobatuntuk="";
                            }
                            
                            $ptgl1=""; $ptgl2="";
                            $nntgl1=""; $nntgl2="";
                            $mtglpilih="";
                            if ($pbridno=="18" OR $pbridno=="19" OR $pbridno=="12")  {
                                $ptgl1=$c['tgl1'];
                                $ptgl2=$c['tgl2'];

                                if ($ptgl1=="0000-00-00") $ptgl1="";
                                if ($ptgl2=="0000-00-00") $ptgl2="";

                                if (!empty($ptgl1)) {
                                    if ($pbridno=="12") {
                                        $nntgl1 = date('F Y', strtotime($ptgl1));
                                    }else{
                                        $nntgl1 = date('d/m/Y', strtotime($ptgl1));
                                    }
                                }

                                if (!empty($ptgl2)) {
                                    if ($pbridno=="12") {
                                        $nntgl2 = date('F Y', strtotime($pbridno));
                                    }else{
                                        $nntgl2 = date('d/m/Y', strtotime($pbridno));
                                    }
                                }

                                if ($pbridno=="12") {
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
                            echo "<td>$pbridnm $satuan $pobatuntuk $pnotenket $npkmdetail $mtglpilih</td>";
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
                        echo "<td>$pbridnm</td>";
                        echo "<td></td>";
                        echo "</tr>";
                        $no++;
                    }
                }

                //Total
                $gtotal=number_format($ptotal,0);
                
                echo "<tr>";
                echo "<td style='border:0px;'></td>";
                echo "<td align='right'>Total  </td>";
                echo "<td align='right'>Rp. $gtotal</td>";
                echo "</tr>";


                ?>
            </tbody>
        </table>
        <br/>
        <?PHP
            echo "Note : $pketerangan";
            
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
                        echo "$pketperiksa04";
                        if (!empty($namagsm)) {
                            echo "<br/><img src='images/tanda_tangan_base64/$namagsm' height='$gmrheight'><br/>";
                        }else{
                            echo "<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                        }
                        echo "<b><u>$nmatasan4</u></b>";

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
    mysqli_close($cnmy);
?>