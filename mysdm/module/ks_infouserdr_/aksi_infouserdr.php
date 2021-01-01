<?PHP
    date_default_timezone_set('Asia/Jakarta');
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    session_start();
    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    $ppilihrpt="";
    
    if (isset($_GET['ket'])) $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=Informasi Dokter.xls");
    }
    
    $printdate= date("d/m/Y");
    
    
    $pidgrouppil=$_SESSION['GROUP'];
    $picardid=$_SESSION['IDCARD'];
    $puserid=$_SESSION['USERID'];
    
    
    include("config/koneksimysqli.php");
    include "config/fungsi_combo.php";
    include "config/fungsi_sql.php";
    include("config/common.php");
    
    $cnit=$cnmy;
?>
<HTML>
<HEAD>
    <title>Informasi Dokter</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <style> .str{ mso-number-format:\@; } </style>
</HEAD>

<BODY>

<form id="doktinf1" action="eksekusi3.php?module=ksinfouserpilih" method=post>
<?php

    $srid = $_SESSION['IDCARD'];
    $spid = $_POST['cb_spesialis'];
    $nmdokter = $_POST['e_nmuserdr'];
    $iddokter = $_POST['e_iddr'];
    
    echo "<b><u>$nmdokter</u></b>";
    
    $sr_id = substr('0000000000'.$srid,-10);
  
    $icabangid="";
    $spid_ = '';
    $icabangid_ = '';

    if ($icabangid=="*") {
    } else {
        $icabangid_ = $icabangid ;
    }

    if($nmdokter != '' && $iddokter == ''){
        $where = "dokter.nama like '".$nmdokter."%'";
    }

    if($nmdokter == '' && $iddokter != ''){
        $where = "dokter.dokterid LIKE '%".$iddokter."'";
    }

    if($nmdokter != '' && $iddokter != ''){
        $where = "dokter.dokterid LIKE '%".$iddokter."' AND dokter.nama like '".$nmdokter."%'";
    }
    
    if (empty($where)) $where= " 1=1 ";
    
    if ($spid=="*" OR $spid=="") {
        $query = "
        select dokter.dokterid,dokter.nama,dokter.aktif,
				dokter.bagian,dokter.alamat1,dokter.alamat2,dokter.kota,
				dokter.telp,dokter.hp,
				dokter.spid,spesial.nama as nmspesial
				from hrd.dokter as dokter 
				join hrd.spesial as spesial on dokter.spid = spesial.spid
				where $where
				order by dokter.nama
        ";
    
    } else {

        $query = "
        select dokter.dokterid,dokter.nama,dokter.aktif,
			dokter.bagian,dokter.alamat1,dokter.alamat2,dokter.kota,
			dokter.telp,dokter.hp,
			dokter.spid,spesial.nama as nmspesial
			from hrd.dokter as dokter 
			join hrd.spesial as spesial on dokter.spid = spesial.spid 
			where $where and dokter.spid='$spid'
			order by dokter.nama
        ";

    }
    // echo "$query<br>";

    $result = mysqli_query($cnit, $query);
    $num_results = mysqli_num_rows($result);
    
    echo '&nbsp; Klik Nama Dokter yang ingin dilihat';
    echo '<br>';
    
    
echo '<table border="1" cellspacing="0" cellpadding="1">';
    echo '<tr>';
        $header_ = add_space('Nama Dokter',40);
        echo "<th align=center><small><b>$header_</b></small></th>";
        $header_ = add_space('Spesialis',30);
        echo "<th align=center><small><b>$header_</b></small></th>";
        $header_ = add_space('Alamat',40);
        echo "<th align=center><small><b>$header_</b></small></th>";
        $header_ = add_space('Telp.',30);
        echo "<th align=center><small><b>$header_</b></small></th>";
    echo '</tr>';
  
    for ($i=0; $i < $num_results; $i++){
        $row = mysqli_fetch_array($result);
        echo '<tr>';
        echo "<td><small><a href=eksekusi3.php?module=ksinfouserpilih&iid=$row[dokterid]>".$row['nama']."</a></small></td>";
        echo "<td><small>".$row['nmspesial']."</small></td>";
        echo "<td><small>".$row['alamat1']."</small></td>";
        echo "<td><small>".$row['telp']."&nbsp;</small></td>";
        echo '</tr>';
        if ($row['alamat2'] || $row['hp']) {
            echo '<tr>';
            echo '<td>&nbsp;</td><td>&nbsp;</td>';
            echo "<td><small>".$row['alamat2']."&nbsp;</small></td>";
            echo "<td><small>".$row['hp']."&nbsp;</small></td>";
            echo '</tr>';
        }
        if ($row['kota']) {
            echo '<tr>';
            echo '<td>&nbsp;</td><td>&nbsp;</td>';
            echo "<td><small>".$row['kota']."</small></td>";
            echo "<td><small>&nbsp;</small></td>";
            echo '</tr>';
        }
    }  // end for
echo '</table>';
  

?>

</form>
<br/><br/><br/><br/>
<br/><br/><br/><br/>

    <?PHP if ($ppilihrpt!="excel") { ?>
        <button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
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
    //mysqli_query($cnit, "DROP TEMPORARY TABLE $tmp01");

    mysqli_close($cnit);
?>