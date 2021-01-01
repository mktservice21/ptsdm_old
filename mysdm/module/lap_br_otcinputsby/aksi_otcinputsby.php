<?PHP
    session_start();
    include("config/koneksimysqli_it.php");
    include("config/common.php");
    
    $thnbln=$_POST['e_periode01'];
    $bln= date("m", strtotime($thnbln));
    $thn= date("Y", strtotime($thnbln));
    $periode= date("Ym", strtotime($thnbln));
    $jbln = substr($bln,-2,2);
    $bln_ = nama_bulan($jbln);
    
    echo "<b>INPUT REPORT SBY PERIODE $bln_ $thn</b><br>&nbsp;<br/>&nbsp;";


?>

<html>
    <head>
        <title>INPUT REPORT SBY</title>
        <meta http-equiv="Expires" content="Tue, 01 Sep 2018 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
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
    </head>
    <body>
    <form id="rpbosby1" action="brosby0.php" method=post>
        <table id='datatable2' class='table table-striped table-bordered example_2'>
            <thead>
                <tr>
                    <th width="30px">No</th>
                    <th width="70px">No Slip</th>
                    <th width="150px">Nama</th>
                    <th width="150px">Alokasi Budget</th>
                    <th width="300px">Keterangan Tempat</th>
                    <th width="200px">Keterangan</th>
                    <th width="80px">Jumlah IDR</th>
                    <th width="80px">Realisasi</th>
                    <th width="70px">Tgl. Report SBY</th>
                    <th width="30px">Report SBY</th>
                    <th width="50px">Jenis</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $query = "select * from dbmaster.v_br_otc_all where DATE_FORMAT(tgltrans, '%Y%m')='$periode' and via<>'Y' and lampiran='Y' "
                        . "order by tgltrans, nama_cabang, icabangid_o";
                $result = mysqli_query($cnit, $query);
                $records = mysqli_num_rows($result);
                $row = mysqli_fetch_array($result);
                if ($records) {
                    $reco = 1;
                    $gtotal = $gtotal_ = 0;
                    while ($reco <= $records) {
                        $tgltrans = $row['tgltrans'];
                        $tglt= date("d", strtotime($tgltrans));
                        $blnt= date("m", strtotime($tgltrans));
                        $jblnt = substr($blnt,-2,2);
                        $blnt_ = nama_bulan($jbln);
                        $thnt= date("Y", strtotime($tgltrans));
                        echo "<tr>";
                        echo "<td colspan=9><b>Tanggal Transfer : $tglt $blnt_ $thnt</b></td>";
                        echo "<td align=center><input type='checkbox' name='' value='chk$reco'></td>";
                        echo "<td></td>";
                        echo "</tr>";

                        $total = $total_ = 0;
                        $no=1;
                        while ( ($reco<=$records) and ($tgltrans == $row['tgltrans']) ) {
                            $brid = $row['brOtcId'];		
                            $tgltrans = $row['tgltrans'];
                            $nobr = $row['brOtcId']; //echo"$noslip";
                            $noslip = $row['noslip'];
                            $kodeid = $row['kodeid'];
                            $nmkodeid = $row['nama_kode'];
                            $keterangan1 = $row['keterangan1'];
                            $keterangan2 = $row['keterangan2'];
                            $jumlah = $row['jumlah']; 
                            $realisasi = $row['realisasi']; 
                            $icabangid_o = $row['icabangid_o'];
                            $nmicabangid_o = $row['nama_cabang'];

                            if (empty($row['tglrpsby']))
                                $tglrpsby="0000-00-00";
                            else
                                $tglrpsby = $row['tglrpsby'];

                            $disabled_ = "disabled";
                            $sby = $row['sby'];

                            if ($tglrpsby=="0000-00-00"){
                                $checked_ = "";
                                $disabled_ = "";
                            }else{
                                if ($sby=='Y') {
                                    $checked_ = "checked";
                                } else {
                                    $checked_ = "";
                                    $disabled_ = "";
                                }
                            }

                            $jrec = "0000" . $reco;
                            $jrec = substr($jrec,-4);
                            $var_ = "br" . $jrec;   //nama variable diawali dengan "cust", contoh cust0001,cust0002,dst.
                            $brsby = $nobr;  //ganti $jrec dengan custid dari database


                            $jenis = $row['jenis'];
                            if ($jenis==""){
                                $jns="";
                            } else {
                                if ($jenis=='A') {
                                        $jns = 'Advance';
                                } else {
                                        $jns = 'Klaim';
                                }
                            }

                            $total = $total + $jumlah;
                            $total_ = $total_ + $realisasi; //echo"$total_";
                            $gtotal = $gtotal + $jumlah;
                            $gtotal_ = $gtotal_ + $realisasi;

                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td>$noslip</td>";
                            echo "<td>$nmicabangid_o</td>";
                            echo "<td>$nmkodeid</td>";
                            echo "<td>$keterangan1</td>";
                            echo "<td>$keterangan2</td>";
                            echo "<td align='right'>".number_format($jumlah,0)."</td>";
                            echo "<td align='right'>".number_format($realisasi,0)."</td>";
                            echo "<td>$tglrpsby</td>";
                            echo "<td align='center'><input type='checkbox' name='$var_' value='$brsby' $checked_  $disabled_>";
                            echo "<td>$jns</td>";
                            echo "</tr>";


                            $no++;

                            $row = mysqli_fetch_array($result);
                            $reco++;
                        }
                        // break per tanggal transfer
                        echo "<tr>";
                        //echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "<td colspan=6 align=right><b>Total :</td>";
                        echo "<td align=right><b>".number_format($total,0)."</b></td>";
                        echo "<td align=right><b>".number_format($total_,0)."</b></td>";
                        echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                        echo "</tr>";

                    }
                    echo "<tr>";
                    //echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    echo "<td colspan=6 align=right><b>Grand Total :</td>";
                    echo "<td align=right><b>".number_format($gtotal,0)."</b></td>";
                    echo "<td align=right><b>".number_format($gtotal_,0)."</b></td>";
                    echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>"; // report SBY
                    echo "</tr>";
                    echo "</table>";
                    echo "<br/>&nbsp;";
                }//end records
                ?>
            </tbody>
        </table>
        
        <table id="tabel1" class="tabe">
            <tr>
                <td align="right">Tanggal Report SBY</td><td>:</td>
                <td>
                    
                    <select name='tanggal1' id='tanggal1' >
                        <?PHP
                            $tanggal1 = date('d'); 
                            $bln1 = date('m');
                            $tahun1 = date('Y');
                            $tahun_1 = $tahun1 - 1;
                            $tahun_2 = $tahun1 + 1;
                            
                            
                            for ($i=1; $i<32; $i++) {
                                $i_ = substr('0'.$i,-2);		
                                if ($i == $tanggal1) {
                                    echo "<option selected='selected' value='$i_'>$i_</option>";	
                                } else {
                                    echo "<option value='$i_'>$i_</option>";					
                                }
                            }
                        ?>
                    </select>
                    
                    <select name='bln1' id='bln1'>
                        <?PHP
                        for ($i=0; $i<12; $i++) {
                            $j = '0'.ltrim(strval($i+1));
                            $j = substr($j,-2,2);
                            $bln_ = nama_bulan($j);
                            if ($j == $bln1) {
                                echo "<option selected='selected' value='$j'>$bln_</option>";	
                            } else {
                                echo "<option value='$j'>$bln_</option>";					
                            }
                        }
                        ?>
                    </select>
                    
                    <select name='tahun1' id='tahun1'>
                        <?PHP
                        echo "<option selected='selected' value='$tahun1'>$tahun1</option>";
                        echo "<option value='$tahun_2'>$tahun_2</option>";
                        ?>
                    </select>
                </td>
                
            </tr>
            <tr>
                <td align="right">Jenis</td><td>:</td>
                <td>
                    <select name='jenis' id='jenis'>
                        <option value='A'>Advance</option>
                        <option value='K'>Klaim</option>
                        <option value='S'>Sudah minta uang muka</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="3"></td>
            </tr>
        </table>
    </form>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
        
    </body>

    <style>
        table.example_2 {
            color: #000;
            font-family: Helvetica, Arial, sans-serif;
            width: 98%;
            border-collapse:
            collapse; border-spacing: 0;
            font-size: 11px;
            border: 1px solid #000;
        }

        td, th {
            border: 1px solid #000; /* No more visible border */
            height: 30px;
            transition: all 0.3s;  /* Simple transition for hover effect */
        }

        th {
            background: #DFDFDF;  /* Darken header a bit */
            font-weight: bold;
        }

        td {
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
    </style>
</html>      
            