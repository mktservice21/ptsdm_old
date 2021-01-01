<?PHP
    session_start();
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REALISASI BIAYA LUAR KOTA VS CASH ADVANCE.xls");
    }
    
    
    $nmodule=$_GET['module'];
    
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    include "config/library.php";
    $cnit=$cnmy;
    $tglnow = date("d/m/Y");
    
    //harus ada diseleksi
        $pilih_koneksi="config/koneksimysqli.php";
        $ptgl_pillih = $_POST['bulan1'];
        $stsreport = $_POST['sts_rpt'];
        $pprosid_sts = $_POST['sts_sudahprosesid'];
        $scaperiode1 = "";
        $scaperiode2 = "";
        $iproses_simpandata=false;
        $u_filterkaryawan="";
    //END harus ada diseleksi
    //seleksi data
    include ("module/mod_br_closing_lkca_baru/seleksi_data_lk_ca.php");
    
    $pjenispilih = "1";
    
    $pigroupid="";
    $ptgl_pil01= date("Y-m-01", strtotime($ptgl_pillih));
    $ptgl_pil02= date('Y-m-01', strtotime('+1 month', strtotime($ptgl_pillih)));
    
    if ($scaperiode2=="2") $ptgl_pil02=$ptgl_pil01;
    
    $ptgl_pil_sbl= date('Y-m-01', strtotime('-1 month', strtotime($ptgl_pillih)));
    
    $m_periode1 = date("Y-m", strtotime($ptgl_pil01));
    $m_periode2 = date("Y-m", strtotime($ptgl_pil02));
    $m_periode_sbl = date("Y-m", strtotime($ptgl_pil_sbl));
    
    $perBlnThn1 = date("F Y", strtotime($ptgl_pil01));
    $perBlnThn2 = date("F Y", strtotime($ptgl_pil02));
    
    
    $pidinputpd=""; $pidinputbank="";
    $pdivnomor="";
    
    $query = "select * from $tmp00";
    $tampil= mysqli_query($cnit, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $nr= mysqli_fetch_array($tampil);
        $pidinputpd = $nr['idinput'];
        $pidinputbank = $nr['idinputbank'];
        if ($pidinputpd=="0") $pidinputpd="";
        if ($pidinputbank=="0") $pidinputbank="";
    }
    
    if (!empty($pidinputpd)) {
        $query = "select nodivisi from dbmaster.t_suratdana_br WHERE idinput='$pidinputpd'";
        $tampil= mysqli_query($cnit, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0){
            $sc= mysqli_fetch_array($tampil);
            $pdivnomor=$sc['nodivisi'];
        }

    }
    
    $pnobukti=""; $ptgltrans="";
    if (!empty($pidinputbank)) {
        $query = "select nobukti, tanggal from dbmaster.t_suratdana_bank WHERE idinputbank='$pidinputbank'";
        $tampil= mysqli_query($cnit, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0){
            $sc= mysqli_fetch_array($tampil);
            $pnobukti=$sc['nobukti'];
            $ptgltrans = date('d F Y', strtotime($sc['tanggal']));
        }

    }
                    
    $stsapv = $_POST['sts_apv'];
    $e_stsapv="Semua Data";
    if ($stsapv == "fin") {
        $e_stsapv="Sudah Proses Finance";
    }elseif ($stsapv == "belumfin") {
        $e_stsapv="Belum Proses Finance";
    }
    
    

    $query = "alter table $tmp01 ADD atasan CHAR(10)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnit, "UPDATE $tmp01 a SET a.atasan ='0000000367' WHERE jabatanid IN ('05', '06', '22')");//FARIDA SOEWANTO
    mysqli_query($cnit, "UPDATE $tmp01 a SET a.atasan =atasan3 WHERE jabatanid IN ('08')");
    mysqli_query($cnit, "UPDATE $tmp01 a SET a.atasan =atasan2 WHERE jabatanid IN ('10', '18')");
    mysqli_query($cnit, "UPDATE $tmp01 a SET a.atasan =atasan3 WHERE jabatanid IN ('10', '18') AND IFNULL(atasan,'')=''");
    mysqli_query($cnit, "UPDATE $tmp01 a SET a.atasan =atasan2 WHERE jabatanid IN ('15')");
    mysqli_query($cnit, "UPDATE $tmp01 a SET a.atasan =atasan3 WHERE jabatanid IN ('15', '10', '18') AND IFNULL(atasan,'')=''");
    mysqli_query($cnit, "UPDATE $tmp01 a SET a.atasan =atasan4 WHERE jabatanid IN ('20')");
    mysqli_query($cnit, "UPDATE $tmp01 a SET a.atasan =karyawanid WHERE IFNULL(atasan,'')=''");
    
    
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
    //RUTIN DETAIL
    $query = "select idrutin, coa, nobrid, qty, rp, rptotal, notes, deskripsi, tgl1, tgl2, CAST('' as CHAR(5)) as divisi 
            from dbmaster.t_brrutin1  
            WHERE idrutin IN (select distinct IFNULL(idrutin,'') FROM $tmp01 WHERE IFNULL(idrutin,'') <>'')";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp04 a JOIN (select distinct idrutin, divisi from $tmp01 WHERE IFNULL(idrutin,'') <>'') as b "
            . " on a.idrutin=b.idrutin "
            . " SET a.divisi=b.divisi"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp04 a JOIN dbmaster.posting_coa_rutin b "
            . " on a.divisi=b.divisi AND a.nobrid=b.nobrid "
            . " SET a.coa=b.COA4"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "select a.*, b.nama nama_brid, c.NAMA4 from $tmp04 a LEFT JOIN dbmaster.t_brid b ON a.nobrid=b.nobrid "
            . " LEFT JOIN dbmaster.coa_level4 c on a.coa=c.COA4";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
    
    //END RUTIN DETAIL
    
    
    $query = "select b.coa, b.NAMA4, a.idca1, a.idrutin, a.karyawanid, a.nama_karyawan, a.divisi, 
        b.nobrid, b.nama_brid, b.deskripsi, b.notes, b.rp, b.qty, b.rptotal, b.tgl1, b.tgl2,
        a.keterangan, a.credit, a.saldo, a.ca1, a.ca2, a.kuranglebihca1, a.selisih, a.jml_adj, a.jmltrans, a.atasan, c.nama nama_atasan 
        from $tmp01 a 
        LEFT JOIN $tmp03 b on a.idrutin=b.idrutin 
        LEFT JOIN hrd.karyawan c on a.atasan=c.karyawanId";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnit, $query);
    $erropesan = mysqli_error($cnit); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>

<html>
<head>
    <title>REALISASI BIAYA LUAR KOTA VS CASH ADVANCE</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">
    
        <!-- Datatables -->
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

        <!-- Datatables -->
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    <?PHP } ?>
    
</head>
<body class="nav-md">
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    

    
    
    <div id='n_content'>
        
        <div id="kotakjudul">
            <div id="isikiri">
                <table class='tjudul' width='100%'>
                    <tr><td width="150px"><b>PT SDM</b></td><td></td></tr>
                    <tr><td width="210px"><b>Realisasi Biaya Luar Kota Per </b></td><td><?PHP echo "$perBlnThn1 "; ?></td></tr>
                    <?PHP if (!empty($pdivnomor)) { ?>
                    <tr><td width="210px"><b>No. <?PHP echo $pdivnomor; ?> </b></td><td></td></tr>
                    <?PHP } ?>
                    <tr><td><b>Status Approve </b></td><td><?PHP echo "$e_stsapv"; ?></td></tr>
                </table>
            </div>
            <div id="isikanan">

            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
        
        
        <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                <th align="center" nowrap>Date Trsfr</th>
                <th align="center" nowrap>Bukti</th>
                <th align="center" nowrap>COA</th>
                <th>PERKIRAAN</th>
                <!--<th align="center" nowrap>DAERAH</th>-->
                <th align="center" nowrap>ID CA</th>
                <th align="center" nowrap>No LK</th>
                <th align="center" nowrap>NAMA</th>
                <th align="center" nowrap>DIVISI</th>
                <th align="center"  nowrap>Description</th>
                <th></th>
                <?PHP
                if ($_GET['ket']=="excel") {
                    echo "<th align='center' nowrap>Jenis</th>";
                    echo "<th align='center' nowrap>Debit</th>";
                }else{
                    echo "<th align='center' nowrap>Keterangan</th>";
                    echo "<th align='center' nowrap>Jenis</th>";
                }
                ?>
                
                <th align="center" nowrap>Credit</th>
                <th align="center" nowrap>Saldo REAL</th>
                <th align="center" nowrap>CA <?PHP echo $perBlnThn1; ?></th>
                <th align="center" nowrap>Selisih</th>
                <th align="center" >SPV/DM/SM/GSM</th>
                <th align="center" >CA  <?PHP echo $perBlnThn2; ?></th>
                <th align="center" >AR / AP<br/>  <?PHP echo $m_periode_sbl; ?></th>
                <th align="center" >JUML TRSF</th>
            </thead>
            
            <tbody>
                <?PHP
                    $no=1;
                    $gtotjumlah=0; $gtotca1=0; $gtotca2=0; $gtotadj=0; $gtotselisih=0; $gtottrans=0; $gtotkurlebihca1=0;
                    $gtotsaldo=0;
                    
                    $query = "select distinct IFNULL(divisi,'HO') as divisi from $tmp02 order by IFNULL(divisi,'HO')";
                    $result0 = mysqli_query($cnit, $query);
                    while ($row0 = mysqli_fetch_array($result0)){
                        $pdivisi=$row0['divisi'];
                        
                        $query = "select distinct karyawanid, nama_karyawan, divisi from $tmp02 WHERE divisi='$pdivisi' order by IFNULL(divisi,'HO'), nama_karyawan, karyawanid";
                        $result1 = mysqli_query($cnit, $query);
                        while ($row1 = mysqli_fetch_array($result1)){
                            $pkaryawanid=$row1['karyawanid'];
                            $pnmkaryawan=$row1['nama_karyawan'];
                            
                            $belum=false;
                            
                            $query = "select * from $tmp02 WHERE divisi='$pdivisi' and karyawanid='$pkaryawanid' order by IFNULL(divisi,'HO'), nama_karyawan, karyawanid, idrutin";
                            $result = mysqli_query($cnit, $query);
                            while ($row = mysqli_fetch_array($result)){
                                
                                $pnolk=$row['idrutin'];
                                $pidca=$row['idca1'];
                                $pketerangan=$row['keterangan'];
                                $pcoa4=$row['coa'];
                                $pnmcoa4=$row['NAMA4'];
                                $pkdcabang="";//$row['icabangid'];
                                $pnmcabang="";//$row['nama_cabang'];
                                $pkdarea="";//$row['areaid'];
                                $pnmarea="";//$row['nama_area'];
                                $pnobrid=$row['nobrid'];
                                $pnmdes=$row['nama_brid'];
                                $pdeskripsi=$row['deskripsi'];
                                $pnotes=$row['notes'];
                                if (empty($pdeskripsi)) $pdeskripsi=$pnotes;
                                elseif (!empty($pdeskripsi)) $pdeskripsi=$pdeskripsi.", ".$pnotes;
                                
                                $pqty=$row['qty'];
                                $prp=$row['rp'];
                                $prptotal=$row['rptotal'];
                                
                                if ((double)$pqty==0) $pqty=1;
                                $pjumlah=$prptotal;
                                
                                $pqty=number_format($pqty,0,",",",");
                                $prp=number_format($prp,0,",",",");
                                if ($pnobrid=="04" or $pnobrid=="25") {
                                    if ($ppilihrpt=="excel")
                                         $pnmdes=$pnmdes." (".$pqty."x".$prp.")";
                                    else
                                        $pnmdes=$pnmdes."<br/>(".$pqty."x".$prp.")";
                                }
                                
                                if ($pnobrid=="21") {
                                    $ptgl1="";
                                    $ptgl2="";
                                    if ($row['tgl1']!="0000-00-00" AND !empty($row['tgl1']))
                                        $ptgl1 = date('d/m/Y', strtotime($row['tgl1']));
                                    if ($row['tgl2']!="0000-00-00" AND !empty($row['tgl2']))
                                        $ptgl2 = date('d/m/Y', strtotime($row['tgl2']));
                                    
                                    if ($ppilihrpt=="excel")
                                         $pnmdes=$pnmdes." (".$ptgl1." s/d. ".$ptgl2.")";
                                    else
                                        $pnmdes=$pnmdes."<br/>(".$ptgl1." s/d. ".$ptgl2.")";
                                }
                                
                                $pjenis = "UC";
                                $papv=$row['nama_atasan'];
                                
                                $gtotjumlah=(double)$gtotjumlah+(double)$prptotal;
                                
                                $pjumlah=number_format($pjumlah,0,",",",");
                        
                                echo "<tr>";
                                echo "<td nowrap>$ptgltrans</td>";
                                echo "<td nowrap>$pnobukti</td>";
                                echo "<td nowrap>$pcoa4</td>";
                                echo "<td nowrap>$pnmcoa4</td>";
                                echo "<td nowrap>$pidca</td>";//IDCA
                                echo "<td nowrap>$pnolk</td>";//LK
                                echo "<td nowrap>$pnmkaryawan</td>";
                                echo "<td>$pdivisi</td>";
                                echo "<td nowrap>$pnmdes</td>";
                                echo "<td>$pdeskripsi</td>";
                                if ($ppilihrpt=="excel") {
                                    echo "<td>$pjenis</td>";
                                    echo "<td nowrap></td>";
                                }else{
                                    echo "<td>$pketerangan</td>";
                                    echo "<td nowrap>$pjenis</td>";
                                }
                                
                                echo "<td nowrap align='right'>$pjumlah</td>";
                                if ($belum==true) {
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";

                                    echo "<td nowrap></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                    echo "<td nowrap align='right'></td>";
                                }else{
                                    
                                    $prprutin=$row['saldo'];
                                    $pca1=$row['ca1'];
                                    $pca2=$row['ca2'];
                                    $pjumlahadj=$row['jml_adj'];
                                    $pselisih=$row['selisih'];
                                    $pjmltrans=$row['jmltrans'];
                                    $pjmlkuranglebih_ca1=$row['kuranglebihca1'];
                                    
                                    if ((double)$pjmltrans < 0) $pjmltrans=0;
                                    
                                    $gtotsaldo=(double)$gtotsaldo+(double)$prprutin;
                                    $gtotca1=(double)$gtotca1+(double)$pca1;
                                    $gtotca2=(double)$gtotca2+(double)$pca2;
                                    $gtotadj=(double)$gtotadj+(double)$pjumlahadj;
                                    $gtotselisih=(double)$gtotselisih+(double)$pselisih;
                                    $gtottrans=(double)$gtottrans+(double)$pjmltrans;
                                    $gtotkurlebihca1=(double)$gtotkurlebihca1+(double)$pjmlkuranglebih_ca1;
                                    
                                    $prprutin=number_format($prprutin,0,",",",");
                                    $pca1=number_format($pca1,0,",",",");
                                    $pca2=number_format($pca2,0,",",",");
                                    $pjumlahadj=number_format($pjumlahadj,0,",",",");
                                    $pselisih=number_format($pselisih,0,",",",");
                                    $pjmltrans=number_format($pjmltrans,0,",",",");
                                    $pjmlkuranglebih_ca1=number_format($pjmlkuranglebih_ca1,0,",",",");
                                    
                                    echo "<td nowrap align='right'>$prprutin</td>";
                                    echo "<td nowrap align='right'>$pca1</td>";
                                    echo "<td nowrap align='right'>$pselisih</td>";

                                    echo "<td>$papv</td>";
                                    echo "<td nowrap align='right'>$pca2</td>";
                                    echo "<td nowrap align='right'>$pjumlahadj</td>";
                                    echo "<td nowrap align='right'>$pjmltrans</td>";
                                }
                                
                                echo "</tr>";
                                $belum=true;

                            }

                            echo "<tr>";
                            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                            echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                            echo "</tr>";
                        }
                        
                        
                    }
                    
                    $gtotjumlah=number_format($gtotjumlah,0,",",",");
                    $gtotsaldo=number_format($gtotsaldo,0,",",",");
                    $gtotca1=number_format($gtotca1,0,",",",");
                    $gtotselisih=number_format($gtotselisih,0,",",",");
                    $gtotca2=number_format($gtotca2,0,",",",");
                    $gtotadj=number_format($gtotadj,0,",",",");
                    if ((double)$gtottrans < 0) $gtottrans=0;
                    $gtottrans=number_format($gtottrans,0,",",",");
                    $gtotkurlebihca1=number_format($gtotkurlebihca1,0,",",",");
                    
                    echo "<tr>";
                    echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>";
                    echo "<td>&nbsp;</td><td>&nbsp;</td>";
                    
                    echo "<td nowrap align='right'>$gtotjumlah</td>";
                    echo "<td nowrap align='right'>$gtotsaldo</td>";
                    echo "<td nowrap align='right'>$gtotca1</td>";
                    echo "<td nowrap align='right'>$gtotselisih</td>";

                    echo "<td nowrap></td>";
                    echo "<td nowrap align='right'>$gtotca2</td>";
                    echo "<td nowrap align='right'>$gtotadj</td>";
                    echo "<td nowrap align='right'>$gtottrans</td>";
                    
                    echo "</tr>";
                    
                ?>
            </tbody>
        </table>
        <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    </div>

    <?PHP if ($ppilihrpt!="excel") { ?>

        
        <!-- Bootstrap -->
        <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    
        <!-- Datatables -->
        <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
        <script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
        <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
        <script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
        <script src="vendors/jszip/dist/jszip.min.js"></script>
        <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
        <script src="vendors/pdfmake/build/vfs_fonts.js"></script>

        
        
        <style>
            #myBtn {
                display: none;
                position: fixed;
                bottom: 20px;
                right: 30px;
                z-index: 99;
                font-size: 18px;
                border: none;
                outline: none;
                background-color: red;
                color: white;
                cursor: pointer;
                padding: 15px;
                border-radius: 4px;
                opacity: 0.5;
            }

            #myBtn:hover {
                background-color: #555;
            }

            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 20px;
                /*overflow-x:auto;*/
            }
        </style>

        <style>
            .divnone {
                display: none;
            }
            #datatable2, #datatable3 {
                color:#000;
                font-family: "Arial";
            }
            #datatable2 th, #datatable3 th {
                font-size: 12px;
            }
            #datatable2 td, #datatable3 td { 
                font-size: 11px;
            }
        </style>
        
    <?PHP }else{ ?>
        <style>
            .tjudul {
                font-family: Georgia, serif;
                font-size: 15px;
                margin-left:10px;
                margin-right:10px;
            }
            .tjudul td {
                padding: 4px;
            }
            #datatable2, #datatable3 {
                font-family: Georgia, serif;
                margin-left:10px;
                margin-right:10px;
            }
            #datatable2 th, #datatable2 td, #datatable3 th, #datatable3 td {
                padding: 4px;
            }
            #datatable2 thead, #datatable3 thead{
                background-color:#cccccc; 
                font-size: 12px;
            }
            #datatable2 tbody, #datatable3 tbody{
                font-size: 11px;
            }
        </style>
    <?PHP } ?>
        
        
    <script>
        // SCROLL
        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};
        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("myBtn").style.display = "block";
            } else {
                document.getElementById("myBtn").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
        // END SCROLL
    
    
        $(document).ready(function() {
            var table = $('#datatable2, #datatable3').DataTable({
                fixedHeader: true,
                "ordering": false,
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                "displayLength": -1,
                "order": [[ 0, "asc" ]],
                "columnDefs": [
                    { "visible": false },
                    { className: "text-right", "targets": [12,13,14,15,17,18,19] },//right
                    { className: "text-nowrap", "targets": [0, 1, 2, 3, 4, 5,6,11,12,13,14,15,17,18,19] }//nowrap

                ],
                bFilter: true, bInfo: true, "bLengthChange": true, "bLengthChange": true,
                "bPaginate": true
            } );

        } );
    </script>
    
</body>
</html>


<?PHP
hapusdata:
    mysqli_query($cnit, "drop TEMPORARY table $tmp00");
    mysqli_query($cnit, "drop TEMPORARY table $tmp01");
    mysqli_query($cnit, "drop TEMPORARY table $tmp02");
    mysqli_query($cnit, "drop TEMPORARY table $tmp03");
    mysqli_query($cnit, "drop TEMPORARY table $tmp04");
    mysqli_query($cnit, "drop TEMPORARY table $tmp05");
?>