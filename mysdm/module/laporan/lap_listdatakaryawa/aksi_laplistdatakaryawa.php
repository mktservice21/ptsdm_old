<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Rekap_Data_Karyawan.xls");
    }
    
    include("config/koneksimysqli.php");
    
    $printdate= date("d/m/Y");
?>

<?PHP
    $tgl01 = $_POST['e_periode01'];
    $tgl02 = $_POST['e_periode02'];
    $pdivisiid = $_POST['cb_divisi'];
    $purutkan = $_POST['cb_urutkan'];
    $pjabatanid = $_POST['cb_jabatan'];
    $pstsaktif = $_POST['cb_aktif'];
    
    $pchkmasuk="N";
    $pchkkeluar="N";
    $pchktanpann="N";
    
    if (isset($_POST['chk_masuk'])) $pchkmasuk=$_POST['chk_masuk'];
    if (isset($_POST['chk_keluar'])) $pchkkeluar=$_POST['chk_keluar'];
    if (isset($_POST['chk_nn'])) $pchktanpann=$_POST['chk_nn'];
    
    $pperiode1 = date("Ym", strtotime($tgl01));
    $pperiode2 = date("Ym", strtotime($tgl02));
    
    
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    $now=date("mdYhis");
    $tmp00 =" dbtemp.tmprekapkrydt00_".$puserid."_$now ";
    $tmp01 =" dbtemp.tmprekapkrydt01_".$puserid."_$now ";
    $tmp02 =" dbtemp.tmprekapkrydt02_".$puserid."_$now ";
    
    $query = "CALL dbmaster.query_proses_penempatan_di_imr0('karyawan')";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select karyawanid, nama, jabatanid, tempat, tgllahir, alamat1, alamat2, kota, "
            . " hp, agamaid, eduid, tglmasuk, tglkeluar, jkel, pasangan, pekerjaan, "
            . " tempat2, tgllahir2, skar, icabangid, areaid, divisiid, divisiid2, "
            . " atasanid, atasanid2, aktif from hrd.karyawan WHERE 1=1 ";
    if ($pchkmasuk=="Y") $query .= " AND DATE_FORMAT(tglmasuk,'%Y%m')='$pperiode1' ";
    if ($pchkkeluar=="Y") $query .= " AND DATE_FORMAT(tglkeluar,'%Y%m')='$pperiode2' ";
    if (!empty($pjabatanid)) $query .= " AND jabatanid='$pjabatanid' ";
    if (!empty($pstsaktif)) {
        if ($pstsaktif=="A") {
            $query .= " AND IFNULL(aktif,'')='Y' ";
            $query .=" AND (IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00') ";
        }elseif ($pstsaktif=="B") {
            $query .= " AND IFNULL(aktif,'')<>'Y' ";
            $query .=" AND (IFNULL(tglkeluar,'')<>'' AND IFNULL(tglkeluar,'0000-00-00')<>'0000-00-00') ";
        }
    }
    if ($pchktanpann=="NN") {
        $query .=" AND LEFT(nama,4) NOT IN ('NN -', 'DR -', 'DM -', 'BDG ', 'OTH.', 'TO. ', 'BGD-', 'JKT ', 'MR -', 'MR S', 'BKS-')  "
                . " and LEFT(nama,7) NOT IN ('NN DM - ', 'MR SBY1')  "
                . " and LEFT(nama,3) NOT IN ('TO.', 'TO-', 'DR ', 'DR-', 'JKT', 'NN-', 'TO ') "
                . " AND LEFT(nama,5) NOT IN ('OTH -', 'NN AM', 'NN DR', 'TO - ', 'SBY -', 'RS. P') "
                . " AND LEFT(nama,6) NOT IN ('SBYTO-', 'MR SBY', 'LOGIN ', 'SMGTO-') ";
        $query .= " AND nama NOT IN ('ACCOUNTING')";
        $query .= " AND karyawanid NOT IN ('0000002200', '0000002083')";
    }
    
    $query = "create TEMPORARY table $tmp00 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp00 a JOIN MKT.t_karyawan_divcab_now b on "
            . " a.karyawanid=b.karyawanid AND a.jabatanid=b.jabatanid SET a.icabangid=b.icabangid WHERE a.jabatanid NOT IN ('10', '18')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp00 a JOIN (select distinct karyawanid, icabangid FROM MKT.t_karyawan_divcab_now where jabatanid='10') b on "
            . " a.karyawanid=b.karyawanid SET a.icabangid=b.icabangid WHERE a.jabatanid IN ('10', '18')";
    mysqli_query($cnmy, $query); $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query="SELECT a.*, b.nama nama_jabatan, c.nama nama_agama, d.nama nama_cabang FROM $tmp00 a "
            . " LEFT JOIN hrd.jabatan b on a.jabatanid=b.jabatanid "
            . " LEFT JOIN hrd.agama c on a.agamaid=c.agamaid "
            . " LEFT JOIN MKT.icabang d on a.icabangid=d.icabangid";
    $query = "create TEMPORARY table $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    
    
    
    
?>


<HTML>
<HEAD>
    <title>Rekap Data Karyawan</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Apr 2050 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <?PHP } ?>
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>
<BODY class="nav-md">
    
    <div class='modal fade' id='myModal' role='dialog'></div>
    
<?PHP if ($ppilihrpt!="excel") { ?>
    <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<?PHP } ?>
    
    
<div id='n_content'>

    <center><div class='h1judul'>Rekap Data Karyawan</div></center>
    
    <div id="divjudul">
        <table class="tbljudul">
            <?PHP
            //echo "<tr> <td>Periode $pstsperiode</td> <td>:</td> <td><b>$myperiode1 s/d. $myperiode2</b></td> </tr>";
            echo "<tr class='miring text2'> <td>view date</td> <td>:</td> <td>$printdate</td> </tr>";
            ?>
        </table>
    </div>
    <div class="clearfix"></div>
    <hr/>
    
    
    
    <table id='datatable2' class='table table-striped table-bordered example_2' border="1px solid black">
        <thead>
            <tr style='background-color:#cccccc; font-size: 13px;'>
            <th align="center">No.</th>
            <th align="center">Tgl. Masuk</th>
            <th align="center">ID</th>
            <th align="center">Nama Karyawan</th>
            <th align="center">Jenis</th>
            <th align="center">Jabatan</th>
            <th align="center">Agama</th>
            <th align="center">T. Lahir</th>
            <th align="center">Tgl. Lahir</th>
            <th align="center">Alamat</th>
            <th align="center">Hp</th>
            <th align="center">Cabang</th>
            <th align="center">Tgl. Keluar</th>
            </tr>
        </thead>
        <tbody>
            <?PHP
            $no=1;
            $query = "select * from $tmp01 ";
            if ($purutkan=="A") $query .= " order by tglmasuk DESC, karyawanid DESC";
            elseif ($purutkan=="B") $query .= " order by nama, karyawanid";
            elseif ($purutkan=="C") $query .= " order by karyawanid";
            $tampil= mysqli_query($cnmy, $query);
            while ($row= mysqli_fetch_array($tampil)) {
                $pkaryawanid=$row['karyawanid'];
                $pnmkaryawan=$row['nama'];
                $ptglmasuk=$row['tglmasuk'];
                $ptglkeluar=$row['tglkeluar'];
                $pjekel=$row['jkel'];
                $pidjbt=$row['jabatanid'];
                $pnmjbt=$row['nama_jabatan'];
                $pnmagama=$row['nama_agama'];
                $ptempat=$row['tempat'];
                $ptgllahir=$row['tgllahir'];
                $palamat1=$row['alamat1'];
                $palamat2=$row['alamat2'];
                $pkota=$row['kota'];
                $php=$row['hp'];
                $pdivisi=$row['divisiid'];
                $pnamacabang=$row['nama_cabang'];
                
                
                $pnmdivisi=$pdivisi;
                if ($pdivisi=="PEACO") $pnmdivisi="PEACOK";
                if ($pdivisi=="PIGEO") $pnmdivisi="PIGEON";
                
                //$pnmakun=$row['nama_kode'];
               
                if ($ptglmasuk=="0000-00-00") $ptglmasuk="";
                if ($ptgllahir=="0000-00-00") $ptgllahir="";
                if ($ptglkeluar=="0000-00-00") $ptglkeluar="";
                if (!empty($ptglmasuk)) $ptglmasuk = date("d/m/Y", strtotime($ptglmasuk));
                if (!empty($ptgllahir)) $ptgllahir = date("d/m/Y", strtotime($ptgllahir));
                if (!empty($ptglkeluar)) $ptglkeluar = date("d/m/Y", strtotime($ptglkeluar));
                
                echo "<tr>";
                echo "<td nowrap>$no</td>";
                echo "<td nowrap>$ptglmasuk</td>";
                echo "<td nowrap class='str'>$pkaryawanid</td>";
                echo "<td nowrap>$pnmkaryawan</td>";
                echo "<td nowrap>$pjekel</td>";
                echo "<td nowrap>$pidjbt - $pnmjbt</td>";
                echo "<td nowrap>$pnmagama</td>";                
                echo "<td nowrap>$ptempat</td>";
                echo "<td nowrap>$ptgllahir</td>";
                if ($ppilihrpt!="excel") {
                    echo "<td >$palamat1<br/>$palamat2 $pkota</td>";
                }else{
                    echo "<td nowrap>$palamat1 $palamat2 $pkota</td>";
                }
                echo "<td nowrap>$php</td>";
                echo "<td nowrap>$pnamacabang</td>";
                echo "<td nowrap>$ptglkeluar</td>";
                echo "</tr>";
                
                
                $no++;
            }
            ?>
        </tbody>
    </table>
    <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
</div>
    
    
    <?PHP if ($ppilihrpt!="excel") { ?>

        
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
            th, td {
                padding: 3px;
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
        
        
        <style>
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
            }

            .th2 {
                background: white;
                position: sticky;
                top: 23;
                box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
                border-top: 1px solid #000;
            }
        </style>
    
    
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
    
    
    <?PHP }else{ ?>
        <style>
            .h1judul {
              font-size: 140%;
              font-weight: bold;
            }
        </style>
    <?PHP } ?>
</BODY>

</HTML>

<?PHP
hapusdata:
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
    mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp02");
    mysqli_close($cnmy);
?>