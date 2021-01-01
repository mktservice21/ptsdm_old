<?php
    session_start();
    if (empty($_SESSION['IDCARD']) AND empty($_SESSION['NAMALENGKAP'])){
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }
    
    // Program to display URL of current page. 
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        $link = "https"; 
    else
        $link = "http"; 
    // Here append the common URL characters. 
    $link .= "://"; 
    // Append the host(domain name, ip) to the URL. 
    $link .= $_SERVER['HTTP_HOST']; 
    // Append the requested resource location to the URL 
    //$link .= $_SERVER['REQUEST_URI']; 
    // Print the link 
    if ($link=="http://ms.sdm-mkt.com") {
    }else{
        $link .="/ptsdm";
    }
    //echo $link; 


    $printdate= date("d/m/Y");
    $jamnow=date("H:i:s");
    
    
    include "config/koneksimysqli.php";
    include "config/fungsi_sql.php";
    include "config/library.php";
    
    $nospd=$_GET['brid'];
    
    $gmrheight = "100px";
    $ngbr_idinput=date("mdYhis");
    $gbrttd_fin1="";
    
    $psudah_email="N";
    $nospd_sudahttd="";
    $query = "select * from dbmaster.t_suratdana_br_ttd WHERE nomor='$nospd'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0) {
        $ra= mysqli_fetch_array($tampil);
        if (!empty($ra['nomor'])) {
            $nospd_sudahttd=$ra['nomor'];
        }
        $gbrttd_fin1=$ra['gbr_ttd1'];
        $psudah_email=$ra['sudah_email'];
        
    }
    
    if (!empty($gbrttd_fin1)) {
        $data="data:".$gbrttd_fin1;
        $data=str_replace(' ','+',$data);
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        $namapengaju_ttd_fin1="imgfin1_".$ngbr_idinput."TTDSPD_.png";
        file_put_contents('images/tanda_tangan_base64/'.$namapengaju_ttd_fin1, $data);
    }
    
    
    $userid=$_SESSION['IDCARD'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DSETHZVC01_".$userid."_$now ";
    $tmp02 =" dbtemp.DSETHZVC02_".$userid."_$now ";
    $tmp03 =" dbtemp.DSETHZVC03_".$userid."_$now ";
	$tmp04 =" dbtemp.DSETHZVC04_".$userid."_$now ";

    $query = "select *, kodeid as inketadj from dbmaster.t_suratdana_br WHERE stsnonaktif<>'Y' AND nomor='$nospd'";
    $query = "create TEMPORARY table $tmp03 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    $query = "UPDATE $tmp03 SET kodeid=kodeid2, subkode=subkode2, divisi=divisi2, nodivisi=nodivisi2 WHERE kodeid=3"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select tglspd, CASE WHEN IFNULL(a.divisi,'')='' THEN 'ETHICAL' ELSE a.divisi END as divisi, 
        a.nomor, a.kodeid, b.nama, a.subkode, b.subnama, a.nodivisi, tgl, a.jumlah, a.keterangan, a.inketadj, a.stsnonaktif  
        from $tmp03 a LEFT JOIN dbmaster.t_kode_spd b on a.kodeid=b.kodeid 
        and a.subkode=b.subkode
        ";//WHERE a.stsnonaktif<>'Y' AND a.nomor='$nospd'
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

	
	
    $query = "select tglspd, '' as divisi, 
        nomor, kodeid, nama, subkode, 'BIAYA TRANSFER' as subnama, nodivisi, tgl, sum(jumlah) jumlah, '' as keterangan, kodeid as inketadj, stsnonaktif  
        from $tmp01 WHERE subkode='29'";
    $query = "create TEMPORARY table $tmp04 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    mysqli_query($cnmy, "DELETE FROM $tmp01 WHERE subkode='29'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    mysqli_query($cnmy, "INSERT INTO $tmp01 select * from $tmp04 WHERE subkode='29'");
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
	
	
	
	
    $query = "select * from $tmp01";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    
    //insert yang tidak ada transaksi
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
                
    $message = "
    <html>
    <head>";
    $body = "<title>Print SPD ".$printdate." ".$jamnow."</title>";
    $body .= "<meta http-equiv=Expires' content='Mon, 01 Sep 2030 1:00:00 GMT'>";
    $body .='<meta http-equiv="Pragma" content="no-cache">';
    $body .=header("Cache-Control: no-cache, must-revalidate");
    $body .='<link rel="shortcut icon" href="images/icon.ico" />';
    $body .="<script>
                function printContent(el){
                    var restorepage = document.body.innerHTML;
                    var printcontent = document.getElementById(el).innerHTML;
                    document.body.innerHTML = printcontent;
                    window.print();
                    document.body.innerHTML = restorepage;
                }
            </script>";
    $body .="<script>
            var EventUtil = new Object;
            EventUtil.formatEvent = function (oEvent) {
                    return oEvent;
            }
            function goto2(pForm_,pPage_) {
               document.getElementById(pForm_).action = pPage_;
               document.getElementById(pForm_).submit();

            }
        </script>";
    
    $body .= "</head>";
    
    
    $body .="<body>";
    
    $body .="<div id='div1'>";

        $body .="<table id='example_2' class='table table-striped table-bordered example_2' width='100%' border='0px'>";

            $body .="<tr>";
                $body .="<td>&nbsp;</td>";
                $body .="<td>&nbsp;</td>";
                $body .="<td align='center'><b>No. ".$nospd."</b></td>";
                $body .="<td>&nbsp;</td>";
                $body .="<td>&nbsp;</td>";
            $body .="</tr>";

            $body .="<tr>";
                $body .="<td>&nbsp;</td>";
                $body .="<td>&nbsp;</td>";
                $body .="<td align='center'><b>Jakarta, ".$tglspd."</b></td>";
                $body .="<td>&nbsp;</td>";
                $body .="<td>&nbsp;</td>";
            $body .="</tr>";

        $body .="</table>";

        $body .="<table>
                    <tr><td>Kepada Yth.</td></tr>
                    <tr><td><b>Sdr. Vanda / Lina (Accounting)</b></td></tr>
                    <tr><td>PT.SDM - Surabaya</td></tr>
                </table>";

        $body .="<br/>&nbsp;";

        $body .="<table id='example_2' class='table table-striped table-bordered example_2' width='100%' border='0px'>";

            $body .="<tbody class='inputdatauc'>";
                
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
                            $body .="<tr>";
                            $body .="<td nowrap></td>";
                            $body .="<td nowrap></td>";
                            $body .="<td nowrap></td>";
                            $body .="<td nowrap></td>";
                            $body .="<td nowrap></td>";
                            $body .="</tr>";
                        }
                        
                        $body .="<tr>";
                        $body .="<td><b>$pnama</b></td>";
                        $body .="<td>&nbsp;</td>";
                        if ($llewat==false) {
                            $llewat=true;
                            $body .="<td><b>No Divisi</b></td>";
                        }else{
                            $body .="<td>&nbsp;</td>";
                        }
                        $body .="<td><b>Rp</b></td>";
                        $body .="<td nowrap align='right'><b>$totalkodeid</b></td>";
                        $body .="</tr>";
                        $query = "select a.divisi, 
                            a.nomor, a.kodeid, a.nama, a.subkode, a.subnama, a.nodivisi, a.tgl, a.keterangan, a.inketadj, a.jumlah 
                            from $tmp01 a 
                            WHERE a.stsnonaktif<>'Y' AND a.nomor='$nospd' AND a.kodeid='$pkode' 
                            ORDER BY 3, 5, 1, 7";

                        $result2 = mysqli_query($cnmy, $query);
                        while ($row = mysqli_fetch_array($result2)){

                            $pidket="";
                            if (isset($row['inketadj'])) $pidket=$row['inketadj'];
                            $pketeranganadj="";
                            if ($pidket=="3") {
                                $pketeranganadj="";
                                if (isset($row['keterangan'])) $pketeranganadj=$row['keterangan'];
                                if (!empty($pketeranganadj)) $pketeranganadj=" (".$pketeranganadj.")";
                            }
							
							
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
                            
                            if ($pkode=="2" AND ($psubkode=="22" OR $psubkode=="23" OR $psubkode=="39")){
                                $pdivisi="";
                            }
                            
                            if ((double)$pjumlah==0 AND empty($pnodivisi)) {
                                $body .="<tr>";
                                $body .="<td nowrap>$psubnama $pdivisi $pketeranganadj</td>";
                                $body .="<td align='center'>:</td>";
                                $body .="<td nowrap></td>";
                                $body .="<td></td>";
                                $body .="<td nowrap align='right'></td>";
                                $body .="</tr>";
                            }else{
                                $body .="<tr>";
                                $body .="<td nowrap>$psubnama $pdivisi $pketeranganadj</td>";
                                $body .="<td align='center'>:</td>";
                                $body .="<td nowrap>$pnodivisi</td>";
                                $body .="<td>Rp</td>";
                                $body .="<td nowrap align='right'>$pjumlah</td>";
                                $body .="</tr>";
                            }
                        }
                        
                    }
                    
                    
                    $qtotal=number_format($qtotal,0);
                    $body .="<tr>";
                    $body .="<td nowrap><b>TOTAL</b></td>";
                    $body .="<td align='center'>:</td>";
                    $body .="<td nowrap>&nbsp;</td>";
                    $body .="<td>Rp</td>";
                    $body .="<td nowrap align='right'><b>$qtotal</b></td>";
                    $body .="</tr>";
                    
                
            $body .="</tbody>";//end tbody

        $body .="</table>";// end table

        $body .="<br/>&nbsp;";
        
        $body .="<table>
                <tr><td>Rincian Terlampir.</td></tr>
                <tr><td>&nbsp;</td></tr>
            </table>";
        
        
        $nospd_sudahttd="";//jika email digunakan maka script ini dihapus
        if (empty($nospd_sudahttd)) {
            //jika email digunakan maka script ini dihapus
            $body .="<table class='tjudul' width='100%'>";
                $body .="<tr>";
                    $body .="<td align='left'>";
                    $body .="Dibuat oleh :";
                        $body .="<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    $body .="Marianne</td>";
                $body .="</tr>";

            $body .="</table>";
            //end sampai sini jika email digunakan maka script ini dihapus
        }else{
            
            $body .="<table class='tjudul' width='100%'>";
                $body .="<tr>";
                    $body .="<td align='left'>";
                    $body .="Dibuat oleh :";
                    if (!empty($namapengaju_ttd_fin1)){
                        //$body .="<br/><img src='images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                        //$body .="<br/><img src='http://ms.sdm-mkt.com/mysdm/images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                        $body .="<br/><img src='$link/mysdm/images/tanda_tangan_base64/$namapengaju_ttd_fin1' height='$gmrheight'><br/>";
                        //$body .="<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    }else
                        $body .="<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;";
                    $body .="<b>Marianne</b></td>";
                $body .="</tr>";

            $body .="</table>";
            
        }
        
        
    $body .="</div>";
    $body .="</body>";
    
    
    
    
    $message .= $body . "
    </body>
    </html>";


    echo $message;
    
    //cek diatas juga ada yang harus dihapus jika email digunakan
    goto hapusdata; //jika email digunakan maka script goto dihapus
    
    
    
    if (empty($nospd_sudahttd)) {
        echo "<input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' autocomplete='off' "
            . " value='$_SESSION[IDCARD]' Readonly>";
        echo "<input type='hidden' class='form-control' id='e_nospd' name='e_nospd' autocomplete='off' "
            . " value='$nospd' Readonly>";
        include "ttd_ttdsuratpd.php";
    }else{
        
    }
    
    if ($psudah_email=="Y" OR empty($nospd_sudahttd)) {
        goto hapusdata;
    }else{
    
        $nm_emailkirim=$_SESSION['NAMALENGKAP'];
        $p_emailkirim="";
        
        $query = "select * from dbmaster.t_email WHERE karyawanid='$_SESSION[IDCARD]'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            
            $puser_email=$ra['user_email'];
            $ppass_email=$ra['pass_email'];
            $pkirim1=$ra['kirim1'];
            $pkirim2=$ra['kirim2'];
            $pkirim3=$ra['kirim3'];
            $pkirim4=$ra['kirim4'];
            $pkirim5=$ra['kirim5'];
            
            if (!empty($pkirim2)) $p_emailkirim .=",$pkirim2";
            if (!empty($pkirim3)) $p_emailkirim .=",$pkirim3";
            if (!empty($pkirim4)) $p_emailkirim .=",$pkirim4";
            if (!empty($pkirim5)) $p_emailkirim .=",$pkirim5";
        
            
            
            //@set_magic_quotes_runtime(false);
            //ini_set('magic_quotes_runtime', 0);

            include "../email/classes/class.phpmailer.php";
            $mail = new PHPMailer; 
            $mail->IsSMTP();
            $mail->SMTPSecure = 'ssl'; 
            $mail->Host = "sdm-mkt.com"; //host masing2 provider email
            $mail->SMTPDebug = 2;
            $mail->Port = 465;
            $mail->SMTPAuth = true;
            $mail->Username = $puser_email; //user email
            $mail->Password = $ppass_email; //password email 
            $mail->SetFrom($puser_email); //set email pengirim
            $mail->Subject = "Surat Permintaan Dana No. $nospd"; //subyek email
            //$mail->AddAddress("huspan.nasrulloh@sdm-mkt.com", "ayrull79@gmail.com","huspan.nasrulloh@sdm-mkt.com");  //tujuan email
            $mail->AddAddress($p_emailkirim);  //tujuan email
            $mail->addBCC($puser_email, $nm_emailkirim);
            //$mail->AddEmbeddedImage('images/tanda_tangan_base64/'.$namapengaju_ttd_fin1, 'my_ttd', 'images/tanda_tangan_base64/'.$namapengaju_ttd_fin1);
            $mail->MsgHTML($message);
            if($mail->Send()) { 
                echo "Message has been sent";
                $psudah_email="Y";
                mysqli_query($cnmy, "UPDATE dbmaster.t_suratdana_br_ttd SET sudah_email='Y' WHERE nomor='$nospd'");
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; }
            }else {
                echo "Failed to sending message";
            }
            
        }else{
            echo "Tidak ada Email yang dikirim";
        }
    }

    
    
hapusdata:
    mysqli_query($cnmy, "drop temporary table $tmp01");
    mysqli_query($cnmy, "drop temporary table $tmp02");
    mysqli_query($cnmy, "drop temporary table $tmp03");
	mysqli_query($cnmy, "drop temporary table $tmp04");
    
?>

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