<?php
    function BuatFormatNum($prp, $ppilih) {
        if (empty($prp)) $prp=0;
        
        $numrp=$prp;
        if ($ppilih=="1") $numrp=number_format($prp,0,",",",");
        elseif ($ppilih=="2") $numrp=number_format($prp,0,".",".");
            
        return $numrp;
    }
    
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $pidgrouppil=$_SESSION['GROUP'];
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    
    $ppilihrpt="";
    $ppilformat="1";
    if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        $ppilformat="3";
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Laporan Rincian Biaya Rutin.xls");
    }
    
    if (($picardid=="0000000143" OR $picardid=="0000000329") AND $ppilihrpt=="excel") {
        $ppilformat="2";
    }
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    include "config/fungsi_sql.php";
    include("config/common.php");
    
    $printdate= date("d/m/Y");
    
    $pboleheditcoa=false;
    if ($pidgrouppil=="28" OR $pidgrouppil=="23" OR $pidgrouppil=="40" OR $pidgrouppil=="26" OR $pidgrouppil=="1" OR $pidgrouppil=="24") {
        $pboleheditcoa=true;
    }
    if ($ppilihrpt=="excel") {
        $pboleheditcoa=false;
    }
?>


<?PHP

$puserid=$_SESSION['USERID'];
$now=date("mdYhis");
$tmp01 =" dbtemp.tmprptrtnrinci01_".$puserid."_$now ";
$tmp02 =" dbtemp.tmprptrtnrinci02_".$puserid."_$now ";
$tmp03 =" dbtemp.tmprptrtnrinci03_".$puserid."_$now ";
$tmp04 =" dbtemp.tmprptrtnrinci04_".$puserid."_$now ";


$filterjenis=('');
if (!empty($_POST['chkbox_jnsobat'])){
    $filterjenis=$_POST['chkbox_jnsobat'];
    $filterjenis=PilCekBoxAndEmpty($filterjenis);
}

$ppilihanapv="All Data";
$pfilterapv="";
if (!empty($pstsreport)) {
    if ($pstsreport=="apvfin") {
        $pfilterapv=" AND IFNULL(tgl_fin,'') <>'' ";
        $ppilihanapv="Sudah Proses Finance";
    }
}


$pidkaryawan=$_POST['cb_karyawan'];
$nmkaryawan= getfield("select nama as lcfields from hrd.karyawan where karyawanId='$pidkaryawan'");
$ppilihkry="";
$pfilterkry="";
if (!empty($pidkaryawan)) {
    $pfilterkry=" AND karyawanid='$pidkaryawan' ";
    $ppilihkry="($nmkaryawan)";
}
        
        
$tgl01 = $_POST['e_tgl1'];
$tgl02 = $_POST['e_tgl2'];
$pstsreport = $_POST['cb_status'];
$psortby = $_POST['cb_sortby'];


$pperiode1 = date("Y-m", strtotime($tgl01));
$pperiode2 = date("Y-m", strtotime($tgl02));

$myperiode1 = date("F Y", strtotime($tgl01));
$myperiode2 = date("F Y", strtotime($tgl02));
        
$query = "select divisi, bulan, periode1, periode2, karyawanid, nama_pengaju, idkodeinput as idrutin, kodeid as nobrid, coa4, "
        . " keterangan1, deskripsi, kredit as jumlah "
        . " from dbproses.proses_expenses "
        . " WHERE kodeinput='5' AND IFNULL(divisi,'') <> 'OTC' "
        . " AND DATE_FORMAT(tanggal,'%Y-%m') BETWEEN '$pperiode1' AND '$pperiode2' "
        . " $pfilterkry AND kodeid in $filterjenis";
$query = "create TEMPORARY table $tmp01 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER TABLE $tmp01 ADD COLUMN nama_karyawan varchar(300), ADD COLUMN nama_kode VARCHAR(200), ADD COLUMN nama_coa VARCHAR(300)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN hrd.karyawan as b on a.karyawanid=b.karyawanId SET a.nama_karyawan=b.nama";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 SET nama_karyawan=nama_pengaju WHERE IFNULL(karyawanid,'') IN ('0000002200', '0000002083', '')";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN dbmaster.coa_level4 as b on a.coa4=b.COA4 SET a.nama_coa=b.NAMA4";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "UPDATE $tmp01 as a JOIN dbmaster.t_brid as b on a.nobrid=b.nobrid SET a.nama_kode=b.nama";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }



$query = "select a.karyawanid, a.tglawal, a.nopol, b.merk, b.jenis, c.nama_jenis, b.tahun from dbmaster.t_kendaraan_pemakai a "
        . " LEFT JOIN dbmaster.t_kendaraan b on a.nopol=b.nopol "
        . " LEFT JOIN dbmaster.t_kendaraan_jenis c on b.jenis=c.jenis";
$query = "create TEMPORARY table $tmp02 ($query)";
mysqli_query($cnmy, $query);
$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "ALTER table $tmp02 ADD COLUMN noidauto BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
$query = "CREATE UNIQUE INDEX `unx1` ON $tmp02 (noidauto)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }


$query = "ALTER table $tmp01 ADD COLUMN nopol VARCHAR(50), ADD COLUMN jenis VARCHAR(50), ADD COLUMN nama_merk VARCHAR(100), ADD COLUMN tahun VARCHAR(10)";
mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

$query = "select distinct karyawanid, DATE_FORMAT(tglawal,'%Y%m') bulan, nopol, nama_jenis, merk, tahun FROM $tmp02 order by 1,2";
$tampil=mysqli_query($cnmy, $query);
while ($nr= mysqli_fetch_array($tampil)) {
    $pikryid=$nr['karyawanid'];
    $pibln=$nr['bulan'];
    $pinopol=$nr['nopol'];
    $pidjenis=$nr['nama_jenis'];
    $pnmmerk=$nr['merk'];
    $pntahun=$nr['tahun'];
    if (!empty($pinopol)) {
        $query = "UPDATE $tmp01 SET nopol='$pinopol', jenis='$pidjenis', nama_merk='$pnmmerk', tahun='$pntahun' WHERE DATE_FORMAT(bulan,'%Y%m')>='$pibln' AND karyawanid='$pikryid'";
        mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    }
}
        
            

$arriddiv[]="";
$arridcoa[]="";
$arrnmcoa[]="";
$query = "select c.DIVISI2, a.COA4, a.NAMA4 from dbmaster.coa_level4 as a "
        . " JOIN dbmaster.coa_level3 as b on a.COA3=b.COA3 "
        . " JOIN dbmaster.coa_level2 as c on b.COA2=c.COA2 "
        . " WHERE 1=1 "
        . " ORDER BY c.DIVISI2, a.COA4";
$tampilk= mysqli_query($cnmy, $query);
while ($zr= mysqli_fetch_array($tampilk)) {
    $ziddiv=$zr['DIVISI2'];
    $zidcoa=$zr['COA4'];
    $znmcoa=$zr['NAMA4'];

    $arriddiv[]=$ziddiv;
    $arridcoa[]=$zidcoa;
    $arrnmcoa[]=$znmcoa;
}


//echo "$myperiode1 s/d. $myperiode2<br/>";
//goto hapusdata;

?>
<HTML>
<HEAD>
    <title>Laporan Rincian Biaya Rutin</title>
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
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>


<BODY>
    
<div class='modal fade' id='myModal' role='dialog'></div>
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>

<div id='n_content'>

    <center><div class='h1judul'>Laporan Rincian Biaya Rutin</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <tr><td>Periode</td><td>:</td><td><?PHP echo "<b>$myperiode1 s/d. $myperiode2</b>"; ?></td></tr>
            <tr class='miring text2'><td>view date</td><td>:</td><td><?PHP echo "$printdate"; ?></td></tr>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    <table id='mydatatable1' class='table table-striped table-bordered' width="100%" border="1px solid black">
        <thead>
            <tr>
                <th align="center" nowrap>No</th>
                <?PHP
                if ($psortby=="periode") {
                    echo "<th align='center' nowrap>BULAN</th>";
                    echo "<th align='center' nowrap></th>";
                    echo "<th align='center' nowrap>ID KARYAWAN</th>";
                    echo "<th align='center' nowrap>KARYAWAN</th>";
                }else{
                    echo "<th align='center' nowrap>ID KARYAWAN</th>";
                    echo "<th align='center' nowrap>KARYAWAN</th>";
                    echo "<th align='center' nowrap>BULAN</th>";
                    echo "<th align='center' nowrap></th>";
                }
                ?>
                
                <th align="center" nowrap>ID</th>
                <th align="center" nowrap>NAMA KODE</th>
                <th align="center" nowrap>COA</th>
                <th align="center" nowrap>NAMA PERKIRAAN</th>
                <th align="center" nowrap>JUMLAH</th>
                <th align="center" nowrap>No. Kendaraan</th>
                <th align="center" nowrap>NOTES</th>
                <th align="center" nowrap>KETERANGAN</th>
                
                <?PHP
                if ($pboleheditcoa==true) {
                    echo "<th align='center' nowrap>COA EDIT</th>";
                    echo "<th align='center' nowrap></th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $pnotnopol = array("07", "13", "14", "18", "05", "15", "06", "19", "17", "16", "10", "11");
            
            $no=1;
            $query = "SELECT * FROM $tmp01 ORDER BY nama_karyawan, bulan, periode1, periode2, idrutin, nobrid";
            $tampil=mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pidkry=$row['karyawanid'];
                $pnmkry=$row['nama_karyawan'];
                $pbulan=$row['bulan'];
                $pper1=$row['periode1'];
                $pper2=$row['periode2'];
                $pidrutin=$row['idrutin'];
                $pnmkode=$row['nama_kode'];
                $pidkode=$row['nobrid'];
                $pidcoa=$row['coa4'];
                $pnmcoa=$row['nama_coa'];
                $pjumlah=$row['jumlah'];
                $pketerangan=$row['keterangan1'];
                $pdesnote=$row['deskripsi'];
                $piddivisi=$row['divisi'];
                $pnopolis=$row['nopol'];
                
                
                if (in_array($pidkode, $pnotnopol)) $pnopolis="";
                        
                $pjumlah=BuatFormatNum($pjumlah, $ppilformat);
                
                if ($ppilihrpt=="excel") {
                    $pbulan = date("d F Y", strtotime($pbulan));
                }else{
                    $pbulan = date("F Y", strtotime($pbulan));
                }
                $pper1 = date("d/m/Y", strtotime($pper1));
                $pper2 = date("d/m/Y", strtotime($pper2));
                
                $pidurut=$pidrutin."|".$pidkode;
                
                if ($pboleheditcoa==true) {
                    $pinputidkry="<input type='hidden' id='txt_idkry[$pidurut]' name='txt_idkry[$pidurut]' value='$pidkry' Readonly>";
                    $pinputidrutin="<input type='hidden' id='txt_idrutin[$pidurut]' name='txt_idrutin[$pidurut]' value='$pidrutin' Readonly>";
                    $pinputbrid="<input type='hidden' id='txt_idbrid[$pidurut]' name='txt_idbrid[$pidurut]' value='$pidkode' Readonly>";
                    
                    $pcbselect = "<select class='soflow' id='cb_coa[$pidurut]' name='cb_coa[$pidurut]'>";
                    $pcbselect .="<option value=''>--Pilih--</option>";
                    for($ix=1;$ix<count($arridcoa);$ix++) {
                        $ziddiv=$arriddiv[$ix];

                        if ($ziddiv==$piddivisi) {
                            $zidcoa=$arridcoa[$ix];
                            $znmcoa=$arrnmcoa[$ix];
                            if ($zidcoa==$pidcoa)
                                $pcbselect .="<option value='$zidcoa' selected>$zidcoa $znmcoa</option>";
                            else
                                $pcbselect .="<option value='$zidcoa'>$zidcoa $znmcoa</option>";
                        }

                    }
                    $pcbselect .="</select>";
                    
                    $psimpan="<input type='button' id='btnsave[]' name='btnsave[]' value='Save' "
                            . " onclick=\"SimpanData('$pidurut', '$pidrutin', '$pidkode', '$pidkry', '$pidrutin', '$pidkode', 'cb_coa[$pidurut]', '$pidcoa')\">";
                }else{
                    $pinputidkry="";
                    $pinputidrutin="";
                    $pinputbrid="";
                    $pcbselect="";
                    $psimpan="";
                }
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap class='str'>$pidkry</td>";
                echo "<td nowrap>$pnmkry</td>";
                echo "<td nowrap>$pbulan</td>";
                echo "<td nowrap>$pper1 s/d. $pper2</td>";
                echo "<td nowrap class='str'>$pidrutin</td>";
                echo "<td nowrap>$pnmkode</td>";
                echo "<td nowrap>$pidcoa</td>";
                echo "<td nowrap>$pnmcoa</td>";
                echo "<td nowrap align='right'>$pjumlah</td>";
                echo "<td nowrap>$pnopolis</td>";
                echo "<td >$pdesnote</td>";
                echo "<td >$pketerangan</td>";
                if ($pboleheditcoa==true) {
                    echo "<td nowrap>$pinputidkry $pinputidrutin $pinputbrid $pcbselect</td>";
                    echo "<td nowrap>$psimpan</td>";
                }
                echo "</tr>";
                
                $no++;
            }
            ?>
        </tbody>
    </table>
    

    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
    
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
    
    
    <script>
        function SimpanData(unourut, unoid, unoiddet, ukryid, uidrtn, ubrid, ucoa, ucoalama) {
        
            var icoa = document.getElementById(ucoa).value;

            if (unourut=="") {
                alert("ID kosong..."); return; false;
            }

            if (ukryid=="") {
                alert("KARYAWAN KOSONG..."); return; false;
            }

            if (uidrtn=="") {
                alert("ID RUTIN KOSONG..."); return; false;
            }

            if (ubrid=="") {
                alert("NOBR ID KOSONG..."); return; false;
            }

            if (icoa=="") {
                alert("COA kosong..."); return; false;
            }
            
            //alert(ukryid+" "+uidrtn+" "+ubrid+" "+icoa)
            var r=confirm("Apakah akan menyimpan data...???")
            if (r==true) {
                //document.write("You pressed OK!")
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");

                $.ajax({
                    type:"post",
                    url:"module/laporan/mod_lap_rincianbrrutin/simpandataeditbrrutin.php?module="+module+"&act=input&idmenu="+idmenu,
                    data:"unourut="+unourut+"&uidbrrutin="+unoid+"&uinoid="+unoiddet+"&ukryid="+ukryid+"&uidrtn="+uidrtn+"&ubrid="+ubrid+"&ucoa="+icoa+"&ucoalama="+ucoalama,
                    success:function(data){
                        alert(data);
                    }
                });

                return 1;
            }
            
        
        }
        
    </script>

</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp03");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp04");

    mysqli_close($cnmy);
?>