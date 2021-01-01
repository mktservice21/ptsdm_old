<html>
<head>
  <title>Rekap Permohonan Dana Budged Request</title>
  <meta http-equiv="Expires" content="Mon, 01 Jan 2007 1:00:00 GMT">
  <meta http-equiv="Pragma" content="no-cache">
  <?php header("Cache-Control: no-cache, must-revalidate"); ?>
</head>

    <style type="text/css" media="print">
        @page 
        {
            size: auto;   /* auto is the current printer page size */
            /*margin: 0mm;  /* this affects the margin in the printer settings */
            margin-left: 7mm;  /* this affects the margin in the printer settings */
            margin-right: 7mm;  /* this affects the margin in the printer settings */
            margin-top: 5mm;  /* this affects the margin in the printer settings */
            margin-bottom: 5mm;  /* this affects the margin in the printer settings */
        }

        body 
        {
            margin: 0px;  /* the margin on the content before printing */
       }
    </style>
    
<style>
    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 13px;
    }
    table {
        font-family: "Times New Roman", Times, serif;
        font-size: 11px;
    }
    
page {
  background: white;
  display: block;
  margin: 0 auto;
  margin-bottom: 0.5cm;
  /*box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);*/
}

page[size="A4"] {  
  /*width: 29.7cm;
  height: 21cm; */
}
</style>
<body>
<page>
<?php
	session_start();
        include "config/koneksimysqli_it.php";
        include_once("config/common.php");
	$srid = $_SESSION['USERID'];
	$srnama = $_SESSION['NAMALENGKAP'];
	$sr_id = substr('0000000000'.$srid,-10);
	$userid = $_SESSION['USERID'];
	$jenis = $_POST['cb_jenis'];
        
        $tgl01=$_POST['e_periode01'];

        $periode1= date("Y-m-d", strtotime($tgl01));
        $periode= date("d-m-Y", strtotime($tgl01));
        
        $now=date("mdYhis");
        $tmpbudgetreq01 =" dbtemp.DTBUDGETBRREKAPSBYOTC01_$_SESSION[IDCARD]$now ";
        echo "<center><h2><u>REKAP DATA PERMOHONAN DANA BR</u></h2></center>";
	//echo "<b>To : Sdri. Lina (Finance)</b><br><br>";
	//echo "<b>Rekap Budget Request (BR) Team OTC</b><br>";
	//echo "<b>Permintaan Uang Muka</b><br>";
	//echo "<right>$periode1</right><br>";
        
        $bl=date("m");
        $bl=(int)$bl;
        $blromawi="I";
        if ($bl==1) $blromawi="I";
        if ($bl==2) $blromawi="II";
        if ($bl==3) $blromawi="III";
        if ($bl==4) $blromawi="IV";
        if ($bl==5) $blromawi="V";
        if ($bl==6) $blromawi="VI";
        if ($bl==7) $blromawi="VII";
        if ($bl==8) $blromawi="VIII";
        if ($bl==9) $blromawi="IX";
        if ($bl==10) $blromawi="X";
        if ($bl==11) $blromawi="XI";
        if ($bl==12) $blromawi="XII";
        
        $noslipurut="BR-OTC/".$blromawi."/".date("y");
        $noslipurut="BR-OTC";
        
        ?>
    <script> window.onload = function() { document.getElementById("e_id").focus(); } </script>
    <style>
        input.e_id, input.e_i {
            font-family: "Times New Roman", Times, serif;
            font-size: 13px;
            background-color: transparent;
            border: 0px solid #000;
            height: 20px;
        }
        input.e_id {
            width: 25px;
            color: #000;
            text-align: right;
        }
        input.e_i {
            width: 40px;
            color: #000;
        }
    </style>
        <?PHP
        echo "<table border='0px' style='font-size: 13px; width:95%;'>";
        echo "<tr>";
        echo "<td colspan=2><b>To : Sdri. Lina (Finance)</b></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td colspan=2><b>Rekap Budget Request (BR) Team OTC</b></td>";
        echo "<td colspan=2 align='right'><input type='text' placeholder='no' id='e_id' name='e_id' class='e_id'>/$noslipurut/<input type='text' placeholder='bln/thn' id='e_i' name='e_i' class='e_i'></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td><b>Permintaan Uang Muka</b></td>";
        echo "<td align='right'></td>";
        echo "<td align='right'>$periode</td>";
        echo "</tr>";
        echo "</table><br/>";
	

	$sql = "select icabangid_o, nama_cabang, keterangan1, idkontak, bankreal1, real1, norekreal1, sum(jumlah) as jumlah, CAST(null as char(1)) as GRP1 "
                . " from dbmaster.v_br_otc_all"
                . " where tglbr = '$periode1' and case when ifnull(lampiran,'N')='' then 'N' else lampiran end ='$jenis' ";
        $sql .= " group by icabangid_o, nama_cabang, keterangan1, idkontak, real1, norekreal1";
        
        $sql = "create table $tmpbudgetreq01 ($sql)";
        mysqli_query($cnit, $sql);
        
        $sql = "update $tmpbudgetreq01 as b set b.GRP1=(select a.group1 from dbmaster.cabang_otc as a where b.icabangid_o=a.cabangid_ho)";
        mysqli_query($cnit, $sql);
        
        $sql = "update $tmpbudgetreq01 set icabangid_o='group1' where ifnull(GRP1,'') <> ''";
        mysqli_query($cnit, $sql);
        
        //echo"$query";
	$header_ = add_space('Daerah',15);
	$header1_ = add_space('Keterangan',150);
	$header2_ = add_space('Realisasi',25);
	$header3_ = add_space('No.Rek',35);
	$header4_ = add_space('Kredit',20);
	$header5_ = add_space('No',7);
	echo '<table border="1" cellspacing="0" cellpadding="1">';
	echo "<tr style='background-color:#cccccc; font-size: 13px;'>\n";
		
	echo '<th align="center">'."Daerah"."</th>";
	echo '<th align="center">'."Keterangan"."</th>";
	echo '<th align="center">'."Realisasi"."</th>";
	echo '<th align="center">'."No.Rek"."</th>";
	echo '<th align="center">'."Kredit"."</th>";
	echo '<th align="center">'."No"."</th>";
	echo "</tr>";
        $gtotal=0;
        $no=1;
        $sql = "select distinct icabangid_o, real1, norekreal1, bankreal1 from $tmpbudgetreq01 order by nama_cabang, real1";
        $tampil = mysqli_query($cnit, $sql);
        while ($r = mysqli_fetch_array($tampil)) {
            $cabang1=$r['icabangid_o'];
            $real1=$r['real1'];
            $norek1=$r['norekreal1'];
            $bankrek1=$r['bankreal1'];
            
            $sql2 = "select * from $tmpbudgetreq01 where icabangid_o='$cabang1' AND real1='$real1' AND norekreal1='$norek1' AND bankreal1='$bankrek1'";
            $tampil2 = mysqli_query($cnit, $sql2);
            $sudah="FALSE";
            $jumlahsub=0;
            while ($r2 = mysqli_fetch_array($tampil2)) {
                $cabang=$r2['icabangid_o'];
                $nmcabang=$r2['nama_cabang'];
                $keterangan=$r2['keterangan1'];
                $realisasi=$r2['real1'];
                $norek=$r2['norekreal1'];
                $bankrek=$r2['bankreal1'];
                
                $ketbanknya="";
                if (empty($bankrek) AND empty($norek))
                    $ketbanknya="";
                else
                    $ketbanknya=$bankrek." : ".$norek;
                
                
                $jumlah=0;
                if (!empty($r2['jumlah'])) {
                    $jumlah=number_format($r2['jumlah'],0,",",",");
                    $jumlahsub = (double)$jumlahsub+$r2['jumlah'];
                }
                        
                echo "<tr>";
                echo "<td>$nmcabang</td>";
                echo "<td>$keterangan</td>";
                echo "<td>$realisasi</td>";
                if ($sudah=="FALSE")
                    echo "<td><b>$ketbanknya</b></td>";
                else
                    echo "<td></td>";
                
                echo "<td align='right'>$jumlah</td>";
                if ($sudah=="FALSE") {
                    echo "<td align='center'>$no</td>";
                    $no++;
                }else
                    echo "<td></td>";
                echo "</tr>";
                
                $sudah="TRUE";
                
            }
            $gtotal=(double)$gtotal+(double)$jumlahsub;
            //subtotal
            $jumlahnya=number_format($jumlahsub,0,",",",");
            echo "<tr>";
            echo "<td colspan=4></td>";
            echo "<td align='right'><b>$jumlahnya</b></td>";
            echo "<td>&nbsp;</td>";
            echo "</tr>";
            echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        }
        //echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        //grandtotal
        $gtotalnya=number_format($gtotal,0,",",",");
        echo "<tr style='background-color:#ffcc99;'>";
        echo "<td colspan=2></td>";
        echo "<td colspan=2 align='center'><b>GRAND TOTAL</b></td>";
        echo "<td align='right'><b>$gtotalnya</b></td>";
        echo "<td>&nbsp;</td>";
        echo "</tr>";
            
	echo "</table>";
        echo "<br/>&nbsp;";
        
        
        echo "<table width='100%' border='0px' style='font-size: 11px; font-weight: bold;'>";
        echo "<tr align='center'>";
        echo "<td>Dibuat oleh,</td><td colspan=2>Mengetahui,</td><td>Disetujui,</td>";
        echo "</tr>";
        
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        
        echo "<tr align='center'>";
        echo "<td>DESI RATNA DEWI</td><td>SAIFUL RAHMAT</td><td>FARIDA SOEWANTO</td><td>IRA BUDISUSETYO</td>";
        echo "</tr>";
        
        echo "</table><br/>";
        
        
        mysqli_query($cnit, "drop table $tmpbudgetreq01");
?>
</page>
</body>
</html>
