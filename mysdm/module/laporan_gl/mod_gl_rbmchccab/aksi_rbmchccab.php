<?php

    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    
    session_start();
    $puserid="";
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    
    $printdate= date("d/m/Y H:i:s");
    
    $fjbtid=$_SESSION['JABATANID'];
    $pidgrouppil=$_SESSION['GROUP'];
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    
    
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REPORT REALISASI BIAYA MARKETING CHC BY CABANG.xls");
    }
    
    include("config/koneksimysqli.php");
    include("config/fungsi_sql.php");
    include "config/fungsi_combo.php";
    include("config/common.php");
    
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmprealchccab00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmprealchccab01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprealchccab02_".$puserid."_$now ";
    $tmp03 =" dbtemp.tmprealchccab03_".$puserid."_$now ";
    $tmp04 =" dbtemp.tmprealchccab04_".$puserid."_$now ";
    $tmp05 =" dbtemp.tmprealchccab05_".$puserid."_$now ";
    $tmp06 =" dbtemp.tmprealchccab06_".$puserid."_$now ";
    $tmp07 =" dbtemp.tmprealchccab07_".$puserid."_$now ";
    $tmp08 =" dbtemp.tmprealchccab08_".$puserid."_$now ";
    
    
    $ppilihdivisi = $_POST['cb_divisip'];
    $ptahun = $_POST['e_tahun'];
    
    $ptgl01 = $ptahun."-01-01";
    $ptgl02 = $ptahun."-12-31";
    
    
    $filtercoa=('');
    $pcoapilih="";
    if (isset($_POST['chkbox_coa'])) $pcoapilih=$_POST['chkbox_coa'];
    
    if (!empty($_POST['chkbox_coa'])){
        $filtercoa=$_POST['chkbox_coa'];
        $filtercoa=PilCekBoxAndEmpty($filtercoa);
    }
    
    $pbreth="";
    $pklaim="";
    $pkas="";
    $pbrotc="";
    $prutin="";
    $pblk="";
    $pca="";
    $pbmsby="";
    $ppilbank="";
    $ppilinsen="";
    $psewakontrak="";
    $pserviceken="";
    $pkaskecilcabang="";
    
    if (isset($_POST['chkbox_rpt4'])) $pbrotc=$_POST['chkbox_rpt4'];
    if (isset($_POST['chkbox_rpt2'])) $pklaim=$_POST['chkbox_rpt2'];
    if (isset($_POST['chkbox_rpt5'])) $prutin=$_POST['chkbox_rpt5'];
    if (isset($_POST['chkbox_rpt6'])) $pblk=$_POST['chkbox_rpt6'];
    if (isset($_POST['chkbox_rpt12'])) $pserviceken=$_POST['chkbox_rpt12'];
    if (isset($_POST['chkbox_rpt15'])) $pkaskecilcabang=$_POST['chkbox_rpt15'];
    
    $pfilterselpil="";
    
    if (!empty($pbrotc)) $pfilterselpil .= "'E',"; //// E BR OTC
    if (!empty($pklaim)) $pfilterselpil .= "'B',"; //// B KLAIM DISC
    if (!empty($prutin)) $pfilterselpil .= "'F',"; //// F RUTIN
    if (!empty($pblk)) $pfilterselpil .= "'G',"; //// G LK
    if (!empty($pserviceken)) $pfilterselpil .= "'V',"; //// V SERVICE KENDARAAN
    if (!empty($pkaskecilcabang)) $pfilterselpil .= "'X',"; //// X KAS KECIL CABANG
    
    if (!empty($pfilterselpil)) {
        $pfilterselpil="(".substr($pfilterselpil, 0, -1).")";
    }else{
        $pfilterselpil="('xaxaXX')";
    }
    
    
    
    $query_data = "select noidauto, kodeinput, hapus_nodiv_kosong, coa_edit as coa, pcm, coa_pcm, "
            . " icabangid, areaid, kredit as jumlah FROM dbmaster.t_proses_data_bm WHERE "
            . " IFNULL(hapus_nodiv_kosong,'') <>'Y' AND "
            . " tgltarikan BETWEEN '$ptgl01' AND '$ptgl02' ";
    $query_data .=" AND divisi='OTC' ";
    $query_data .=" AND IFNULL(ishare,'')<>'Y' ";
    if (!empty($filtercoa)) $query_data .=" AND IFNULL(coa_edit,'') IN $filtercoa ";
    
    $query_sel1 =$query_data." AND kodeinput IN $pfilterselpil ";
    
    $query = "create TEMPORARY table $tmp01 ($query_sel1)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 SET icabangid='ZKLAIMDISC' WHERE kodeinput='B'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    if (!empty($ppilbank)) {
        $query_sel2 =$query_data." AND kodeinput IN ('M') ";
        $query_sel2 .=" AND CONCAT(IFNULL(nkodeid,''),IFNULL(nsubkode,'')) IN (select CONCAT(IFNULL(kodeid,''),IFNULL(subkode,'')) from dbmaster.t_kode_spd where IFNULL(igroup,'')='3' AND IFNULL(ibank,'')<>'N') ";
        $query_sel2 .=" AND IFNULL(nkodeid_nama,'')='K' ";
        
        $query = "INSERT INTO $tmp01 $query_sel2";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    $query = "UPDATE $tmp01 SET coa=coa_pcm WHERE IFNULL(coa_pcm,'') ='105-02'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp01 SET icabangid='ZKLAIMDISC' WHERE kodeinput='B'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select a.icabangid, a.coa, SUM(a.jumlah) as jumlah FROM $tmp01 as a "
            . " GROUP BY 1,2";
    $query = "create TEMPORARY table $tmp02 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp02 SET icabangid='zz' WHERE IFNULL(icabangid,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //$query = "select * from fe_it.otc_etl WHERE YEAR(tgljual)='$ptahun' AND divprodid <>'OTHER' and icabangid <> 22";
    $query = "select * from dbmaster.sales_otc_local WHERE YEAR(tgljual)='$ptahun' AND divprodid <>'OTHER' and icabangid <> 22";
    $query = "create TEMPORARY table $tmp03 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    $query = "select a.icabangid, a.tgljual, a.iprodid, c.GRP_FKIDEN, d.GRP_NAMESS, a.`value` 
            from $tmp03 a 
            left JOIN MKT.iproduk b on a.iprodid=b.iprodid 
            left join MKT.T_OTC_GRPPRD_DETAIL c on b.iprodid=c.GRP_IDPROD
            left join MKT.T_OTC_GRPPRD d on c.GRP_FKIDEN = d.GRP_IDENTS";
    $query = "create TEMPORARY table $tmp04 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    //cari berdasarkan group produk
    $query = "select * from $tmp04 WHERE IFNULL(GRP_FKIDEN,'') IN ('1', '6', '4', '5', '2', '3', '7', '10')";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    //cari all untuk kalik discount
    $query = "select * from $tmp04";
    $query = "create TEMPORARY table $tmp06 ($query)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp06 SET icabangid='ZKLAIMDISC'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    // 6 = MELANOX DECORATIVE 1 = MELANOX PREMIUM
    $query = "UPDATE $tmp05 SET icabangid='PM_MELANOX' WHERE IFNULL(GRP_FKIDEN,'') IN ('1', '6')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //  4 	 PARASOL (FOCUS) 5 	 PARASOL EXIST 
    $query = "UPDATE $tmp05 SET icabangid='PM_PARASOL' WHERE IFNULL(GRP_FKIDEN,'') IN ('4', '5')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    // 2 	 CARMED LOTION
    $query = "UPDATE $tmp05 SET icabangid='PM_CARMED' WHERE IFNULL(GRP_FKIDEN,'') IN ('2')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //  3 	 LANORE MAKE UP 	7 	 LANORE SKIN CARE 
    $query = "UPDATE $tmp05 SET icabangid='PM_LANORE' WHERE IFNULL(GRP_FKIDEN,'') IN ('3', '7')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    //  10 	 ACNEMED
    $query = "UPDATE $tmp05 SET icabangid='PM_ACNEMED' WHERE IFNULL(GRP_FKIDEN,'') IN ('10')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        
        
    $query = "INSERT INTO $tmp04 select * from $tmp05";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "INSERT INTO $tmp04 select * from $tmp06";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    $query = "select icabangid, date_format(tgljual,'%Y-%m') bulan, 'OTC' as divprodid, sum(`value`) as rpsales from $tmp04 GROUP BY 1,2,3";
    $query = "create TEMPORARY table $tmp05 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "SELECT * FROM (select DISTINCT icabangid from $tmp02 UNION select DISTINCT icabangid from $tmp05) as tabel";
    $query = "create TEMPORARY table $tmp06 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "ALTER TABLE $tmp06 ADD COLUMN nama_cabang varchar(200)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
    
    $query = "UPDATE $tmp06 as a "
            . " JOIN mkt.icabang_o as b on a.icabangid=b.icabangid_o SET a.nama_cabang=b.nama";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp06 SET nama_cabang=icabangid WHERE IFNULL(nama_cabang,'')=''";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $arridcab[]="";
    $arrnmcab[]="";
    $query = "select distinct icabangid, nama_cabang from $tmp06 order by nama_cabang, icabangid";
    $tampilk= mysqli_query($cnmy, $query);
    while ($zr= mysqli_fetch_array($tampilk)) {
        $zidcab=$zr['icabangid'];
        $znmcab=$zr['nama_cabang'];
        
        $arridcab[]=$zidcab;
        $arrnmcab[]=$znmcab;
    }
    
    
    
    $query = "select DISTINCT d.DIVISI2 as DIVISI, d.COA1, e.NAMA1, c.COA2, d.NAMA2, b.COA3, c.NAMA3, a.coa as COA4, b.NAMA4 "
            . " from $tmp02 a LEFT JOIN dbmaster.coa_level4 b on a.coa=b.COA4 "
            . " LEFT JOIN dbmaster.coa_level3 as c on b.COA3=c.COA3 "
            . " LEFT JOIN dbmaster.coa_level2 as d on c.COA2=d.COA2 "
            . " LEFT JOIN dbmaster.coa_level1 as e on d.COA1=e.COA1 ";
    $query = "create TEMPORARY table $tmp07 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $addcolumn="";
    for($ix=1;$ix<count($arridcab);$ix++) {
        $zidcab=$arridcab[$ix];
        $znmcab=$arrnmcab[$ix];
        
        $nmfield1="B".$zidcab;
        $nmfield2="S".$zidcab;
        
        $addcolumn .= " ADD COLUMN $nmfield1 DECIMAL(20,2), ADD COLUMN $nmfield2 DECIMAL(20,2),";
        
    }
    $addcolumn .= " ADD COLUMN TOTAL DECIMAL(20,2), ADD COLUMN STOTAL DECIMAL(20,2)";
    
    
    $query = "ALTER TABLE $tmp07 $addcolumn";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
        
    for($ix=1;$ix<count($arridcab);$ix++) {
        $zidcab=$arridcab[$ix];
        $znmcab=$arrnmcab[$ix];
        
        $nmfield1="a.B".$zidcab;
        $nmfield2="a.S".$zidcab;
        
        $filcabid=$zidcab;
        if (empty($zidcab) OR $zidcab=="0000000000") $filcabid="";
        
        $query = "UPDATE $tmp07 a JOIN (select coa, sum(jumlah) as jumlah from $tmp02 WHERE IFNULL(icabangid,'')='$filcabid' GROUP BY 1) b on "
                . " a.COA4=b.coa SET $nmfield1=b.jumlah";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
        
        $query = "UPDATE $tmp07 a JOIN (select divprodid, sum(rpsales) rpsales from $tmp05 WHERE IFNULL(icabangid,'')='$filcabid' GROUP BY 1) b "
                . " SET $nmfield2=b.rpsales";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
    
    
    $query = "ALTER TABLE $tmp07 ADD pcm VARCHAR(1)";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp07 SET pcm='Y' WHERE COA2='105'";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
?>


<HTML>
<HEAD>
    <title>REPORT REALISASI BIAYA MARKETING CHC BY CABANG</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <!--<link href="css/laporanbaru.css" rel="stylesheet">-->
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        
        <!-- Bootstrap -->
        <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    
        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
        <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">


        <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        
		
		
		
		<!-- jQuery -->
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        
        
    <?PHP } ?>
    
</HEAD>
<BODY class="nav-md">
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>
    
    <center><div class='h1judul'>REPORT REALISASI BIAYA MARKETING CHC BY CABANG</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Tahun</td><td>:</td><td><?PHP echo "<b>$ptahun</b>"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
        <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
            <thead>
                <tr style='background-color:#cccccc; font-size: 13px;'>
                    <th align="center" nowrap>Kode</th>
                    <th align="center" nowrap>Nama Perkiraan</th>

                    <?PHP
                    $jmlcolspan=0;
                    for($ix=1;$ix<count($arridcab);$ix++) {
                        $zidcab=$arridcab[$ix];
                        $znmcab=$arrnmcab[$ix];
                        
                        if ($znmcab=="zz") $znmcab="OTHERS";
                        elseif ($znmcab=="ZKLAIMDISC") $znmcab="KLAIM DISCOUNT";
                        
                        echo "<th align='center' nowrap>%</th>";
                        echo "<th align='center' nowrap>$znmcab</th>";
                        
                        $jmlcolspan++; $jmlcolspan++;
                    }
                    $jmlcolspan=(double)$jmlcolspan+3;
                    
                    ?>
                    
                    <th align="center" nowrap>%</th>
                    <th align="center" nowrap>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                
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

        </style>

        <style>
            #n_content {
                color:#000;
                font-family: "Arial";
                margin: 5px 20px 20px 20px;
                /*overflow-x:auto;*/
            }
            
            .h1judul {
              color: blue;
              font-family: verdana;
              font-size: 140%;
              font-weight: bold;
            }
            table.tbljudul {
                font-size : 15px;
            }
            table.tbljudul tr td {
                padding: 1px;
                font-family : "Arial, Verdana, sans-serif";
            }
            .tebal {
                 font-weight: bold;
            }
            .miring {
                 font-style: italic;
            }
            table.tbljudul tr.text2 {
                font-size : 13px;
            }
            
            table {
                text-align: left;
                position: relative;
                border-collapse: collapse;
                background-color:#FFFFFF;
            }

            th {
                background: white;
                position: sticky;
                top: 0;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                z-index:1;
            }

            .th2 {
                background: white;
                position: sticky;
                top: 23;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                border-top: 1px solid #000;
            }
        </style>
    
        <style>
            
            .divnone {
                display: none;
            }
            #mydatatable1, #mydatatable2 {
                color:#000;
                font-family: "Arial";
            }
            #mydatatable1 th, #mydatatable2 th {
                font-size: 12px;
            }
            #mydatatable1 td, #mydatatable2 td { 
                font-size: 11px;
            }
        </style>
        
    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 140%;
              font-weight: bold;
            }
        </style>
    <?PHP } ?>
        
        
</BODY>

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
    </script>
    
    
</HTML>


<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp04");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp05");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp06");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp07");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE IF EXISTS $tmp08");
    mysqli_close($cnmy);
?>