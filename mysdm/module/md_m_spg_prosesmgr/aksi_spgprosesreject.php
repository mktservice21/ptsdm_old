<?php
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    $puserid=$_SESSION['IDCARD'];
    $pnamalengkap=$_SESSION['NAMALENGKAP'];
    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...!!!";
        exit;
    }

    include "../../config/koneksimysqli.php";
    include "../../config/fungsi_sql.php";
    $dbname = "dbmaster";

    $module=$_GET['module'];
    $act=$_GET['act'];
    $idmenu=$_GET['idmenu'];

    if ($module!="spgprosesmgr") {
        echo "tidak berhak akses...";
        exit;
    }
    
    
    $f_nobr="";
    foreach ($_POST['chkbox_br'] as $nobrinput) {
        if (!empty($nobrinput)) {
            $f_nobr .="'".$nobrinput."',";
        }
    }
    if (empty($f_nobr)) {
        echo "Tidak ada data yang dipilih...!!!";
        exit;
    }
    $f_nobr="(".substr($f_nobr, 0, -1).")";
    
    
    $now=date("mdYhis");
    $tmp01 =" dbtemp.DGJSPGPOTCA01_".$puserid."_$now ";
    
    $query = "SELECT
        a.nodivisi, 
        a.nomor, 
        a.idbrspg,
        a.id_spg,
        b.nama,
        b.penempatan,
        a.periode tglbr,
        a.tglpengajuan,
        a.icabangid,
        a.alokid,
        a.areaid, a.jabatid, d.nama_jabatan, a.id_zona, f.nama_zona, 
        c.nama nama_cabang, e.nama nama_area, 
        a.jml_harikerja harikerja, a.jml_sakit, a.jml_izin, a.jml_alpa, a.jml_uc, 
        a.total,
        a.realisasi,
        a.keterangan,
        a.sts, a.apvtgl3,
        a.total insentif, a.total insentif_tambahan, a.total gaji, a.keterangan hk, a.total rpmakan, a.total makan, a.total sewa, a.total pulsa, a.total bbm, a.total parkir,
        a.total rinsentif, a.total rinsentif_tambahan, a.total rgaji, a.total rmakan, a.total rsewa, a.total rpulsa, a.total rbbm, a.total rparkir,
        a.total sinsentif, a.total sinsentif_tambahan, a.total sgaji, a.total smakan, a.total ssewa, a.total spulsa, a.total sbbm, a.total sparkir
        FROM
        dbmaster.t_spg_gaji_br0 a
        JOIN MKT.spg b ON a.id_spg = b.id_spg
        LEFT JOIN MKT.icabang_o c on a.icabangid=c.icabangid_o 
        LEFT JOIN dbmaster.t_spg_jabatan d on a.jabatid=d.jabatid 
        LEFT JOIN MKT.iarea_o e on a.areaid=e.areaid_o AND a.icabangid=e.icabangid_o 
        LEFT JOIN dbmaster.t_zona f on a.id_zona=f.id_zona 
        WHERE a.idbrspg IN $f_nobr";

    $query ="CREATE Temporary TABLE $tmp01 ($query)";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "UPDATE $tmp01 SET nama_cabang='JAKARTA MT', icabangid='JKT_MT' WHERE icabangid='0000000007' AND alokid='001'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }

    $query = "UPDATE $tmp01 SET nama_cabang='JAKARTA RETAIL', icabangid='JKT_RETAIL' WHERE icabangid='0000000007' AND alokid='002'";
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; goto hapusdata; }
    
    
    $query = "select distinct icabangid FROM $tmp01";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>1) {
        echo "Hanya bisa reject per satu cabang...";
        goto hapusdata;
        exit;
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
    
    
        //DETAIL
        $message = "
        <html>
        <head>";
        $body = "<title>Data Reject SPG ".$printdate." ".$jamnow."</title>";
        $body .= "<meta http-equiv=Expires' content='Mon, 01 Sep 2030 1:00:00 GMT'>";
        $body .='<meta http-equiv="Pragma" content="no-cache">';
        $body .=header("Cache-Control: no-cache, must-revalidate");
        $body .='<link rel="shortcut icon" href="../../images/icon.ico" />';
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

            $body .="<div class='title_left'><h4 style='font-size : 12px; color: red;'><b>Data Reject</b></h4></div>";
            $body .="<div class='title_left'><h4 style='font-size : 12px; color: red;'><b>Date Reject : $printdate $jamnow</b></h4></div>";

            $body .="<table id='example_2' class='table table-striped table-bordered example_2' width='100%' border='0px'>";
                $body .="<thead>";
                $body .="<tr>";
                    $body .="<th>No</th>";
                    $body .="<th>Cabang</th>";
                    $body .="<th>Nama</th>";
                    $body .="<th>Insentif</th>";
                    $body .="<th>Insentif<br/>Tambahan</th>";
                    $body .="<th>Gaji</th>";
                    $body .="<th>S</th>";
                    $body .="<th>I</th>";
                    $body .="<th>A</th>";
                    $body .="<th></th>";
                    $body .="<th>Uang Makan</th>";
                    $body .="<th>Sewa Kendaraan</th>";
                    $body .="<th>Pulsa</th>";
                    $body .="<th>BBM</th>";
                    $body .="<th>Parkir</th>";
                    $body .="<th>Jumlah</th>";
                    $body .="<th>Total</th>";
                    $body .="<th>Jabatan</th>";
                    $body .="<th>Area</th>";
                    $body .="<th>Zona</th>";
                    $body .="<th>Penempatan</th>";
                $body .="</tr>";
                $body .="</thead>";

                $body .="<tbody>";

                    $gtotaljml=0;
                    $gtotaltot=0;
                    $gtotaltrans=0;

                    $no=1;
                    $query = "select distinct icabangid, nama_cabang from $tmp01 order by nama_cabang";
                    $tampil= mysqli_query($cnmy, $query);
                    while ($row= mysqli_fetch_array($tampil)) {
                        $picabang=$row['icabangid'];
                        $pnmcabang=$row['nama_cabang'];

                        $cari = mysqli_query($cnmy,"select idbrspg from $tmp01 WHERE icabangid='$picabang' order by nama_cabang, nama, id_spg, idbrspg LIMIT 1");
                        $rw= mysqli_fetch_array($cari);
                        $idno=$rw['idbrspg'];

                        $body .="<tr>";
                        $body .="<td nowrap>$no</td>";
                        $body .="<td nowrap>$pnmcabang</td>";

                        $ilewat=false;
                        $query2 = "select * from $tmp01 WHERE icabangid='$picabang' order by nama_cabang, nama, id_spg, idbrspg";
                        $tampil2= mysqli_query($cnmy, $query2);
                        $jmlrow=mysqli_num_rows($tampil2);
                        $recno=1;
                        $ptotal=0;
                        while ($row2= mysqli_fetch_array($tampil2)) {
                            $idno=$row2['idbrspg'];
                            $pidspg=$row2['id_spg'];
                            $pnmspg=$row2['nama'];
                            $phk=$row2['hk'];

                            $ptglpengajuan=date("d-m-Y", strtotime($row2['tglpengajuan']));
                            $ppenempatan=$row2['penempatan'];
                            $pnmarea=$row2['nama_area'];
                            $pnmzona=$row2['nama_zona'];
                            $pnmjabatan=$row2['nama_jabatan'];

                            $pinsentif=number_format($row2['insentif'],0,",",",");
                            $pinsentif_tambahan=number_format($row2['insentif_tambahan'],0,",",",");

                            $pgaji=number_format($row2['gaji'],0,",",",");

                            $pjmlsakit=number_format($row2['jml_sakit'],0,",",",");
                            $pjmlizin=number_format($row2['jml_izin'],0,",",",");
                            $pjmlalpa=number_format($row2['jml_alpa'],0,",",",");

                            $pmakan=number_format($row2['makan'],0,",",",");
                            $psewa=number_format($row2['sewa'],0,",",",");
                            $ppulsa=number_format($row2['pulsa'],0,",",",");
                            $pbbm=number_format($row2['bbm'],0,",",",");
                            $pparkir=number_format($row2['parkir'],0,",",",");
                            $pjumlah=number_format($row2['total'],0,",",",");

                            $ptotal=$ptotal+$row2['total'];

                            $gtotaljml=$gtotaljml+$row2['total'];

                            $psts=$row2['sts'];
                            $pcolor="";
                            if ($psts=="P") $pcolor="style='color:red';";


                            if ($ilewat==true) {
                                $body .="<tr>";
                                $body .="<td nowrap>$no</td>";
                                $body .="<td nowrap></td>";
                            }

                            $body .="<td nowrap $pcolor>$pnmspg</td>";

                            $body .="<td nowrap align='right'>$pinsentif</td>";
                            $body .="<td nowrap align='right'>$pinsentif_tambahan</td>";

                            $body .="<td nowrap align='right'>$pgaji</td>";

                            $body .="<td nowrap align='right'>$pjmlsakit</td>";
                            $body .="<td nowrap align='right'>$pjmlizin</td>";
                            $body .="<td nowrap align='right'>$pjmlalpa</td>";

                            $body .="<td nowrap align='center'>$phk</td>";
                            $body .="<td nowrap align='right'>$pmakan</td>";
                            $body .="<td nowrap align='right'>$psewa</td>";
                            $body .="<td nowrap align='right'>$ppulsa</td>";
                            $body .="<td nowrap align='right'>$pbbm</td>";
                            $body .="<td nowrap align='right'>$pparkir</td>";
                            $body .="<td nowrap align='right'><b>$pjumlah</b></td>";

                            $jmltotal="";
                            $jmltransfer="";

                            if ((double)$jmlrow==(double)$recno) {
                                $gtotaltot=$gtotaltot+$ptotal;
                                $jmltotal=number_format($ptotal,0,",",",");
                            }

                            $body .="<td nowrap align='right'><b>$jmltotal</b></td>";



                            $body .="<td nowrap $pcolor>$pnmjabatan</td>";
                            $body .="<td nowrap $pcolor>$pnmarea</td>";
                            $body .="<td nowrap $pcolor>$pnmzona</td>";
                            $body .="<td nowrap $pcolor>$ppenempatan</td>";

                            $body .="</tr>";
                            $ilewat=true;
                            $recno++;
                            $no++;
                        }
                    }

                    $body .="<tr>";
                    $body .="<td colspan='21'></td>";
                    $body .="</tr>";


                    $gtotaljml=number_format($gtotaljml,0,",",",");
                    $gtotaltot=number_format($gtotaltot,0,",",",");
                    $gtotaltrans=number_format($gtotaltrans,0,",",",");

                    $body .="<tr>";
                    $body .="<td align='center' colspan='15'><b>GRAND TOTAL</b></td>";

                    $body .="<td align='right'><b>$gtotaljml</b></td>";
                    $body .="<td align='right'><b>$gtotaltot</b></td>";

                    $body .="<td></td>";
                    $body .="<td></td>";
                    $body .="<td></td>";
                    $body .="<td></td>";
                    $body .="</tr>";

                $body .="</tbody>";

            $body .="</table>";

        $body .="</div>";

        $body .="</body>";


        $body .="<br/>&nbsp;<br/>&nbsp;<div class='title_left'><h4 style='font-size : 12px; color: black;'><b>$pnamalengkap</b></h4></div>";

        $message .= $body . "
        </body>
        </html>";

        //END DETAIL
        mysqli_query($cnmy, "drop Temporary table $tmp01");


        //echo $message; exit;

        $nm_emailkirim=$_SESSION['NAMALENGKAP'];
        $p_emailkirim="";
        $p_emailsubjek="";

        $query = "select * from dbmaster.t_email WHERE untuk='SPGRJKMNG'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $ra= mysqli_fetch_array($tampil);
            $pid=$ra['id'];
            
            $puser_email=$ra['user_email'];
            $ppass_email=$ra['pass_email'];
            $ppass_email_from=$ra['email_from'];
            $nm_emailkirim=$ra['nama_from'];
            $p_emailsubjek=$ra['tsubject'];
            
            $pcc_email1=$ra['cc1'];
            $pcc_email2=$ra['cc2'];
            $pcc_email3=$ra['cc3'];
            $pcc_email4=$ra['cc4'];
            $pcc_email5=$ra['cc5'];

            
            $query = "select * from dbmaster.t_email_cabang_otc WHERE id='$pid' AND icabangid_o='$picabang'";
            $tampil_e= mysqli_query($cnmy, $query);
            $ketemu_e= mysqli_num_rows($tampil_e);
            if ($ketemu_e>0) {
                $rc= mysqli_fetch_array($tampil_e);
                
                $pkirim1=$rc['ckirim1'];
            

                //echo "$pcc_email1, $pkirim1"; exit;
                
                //@set_magic_quotes_runtime(false);
                //ini_set('magic_quotes_runtime', 0);

                include "../../../email/classes/class.phpmailer.php";
                $mail = new PHPMailer; 
                $mail->IsSMTP();
                $mail->SMTPSecure = 'ssl'; 
                $mail->Host = "sdm-mkt.com"; //host masing2 provider email
                $mail->SMTPDebug = 2;
                $mail->Port = 465;
                $mail->SMTPAuth = true;
                $mail->Username = $puser_email; //user email
                $mail->Password = $ppass_email; //password email 
                $mail->SetFrom($ppass_email_from, $nm_emailkirim); //set email pengirim
                $mail->Subject = $p_emailsubjek; //subyek email
                //$mail->AddAddress("huspan.nasrulloh@sdm-mkt.com", "ayrull79@gmail.com","huspan.nasrulloh@sdm-mkt.com");  //tujuan email
                $mail->AddAddress($pkirim1);  //tujuan email
                //if (!empty($pkirim2)) $mail->AddAddress($pkirim2);  //tujuan email 2
                $mail->addBCC($ppass_email_from, $nm_emailkirim);
                if (!empty($pcc_email1)) $mail->AddCC($pcc_email1);
                if (!empty($pcc_email2)) $mail->AddCC($pcc_email2);
                if (!empty($pcc_email3)) $mail->AddCC($pcc_email3);
                if (!empty($pcc_email4)) $mail->AddCC($pcc_email4);
                if (!empty($pcc_email5)) $mail->AddCC($pcc_email5);
                //$mail->AddReplyTo('ayrull79@gmail.com', $nm_emailkirim);
                
                //$mail->AddEmbeddedImage('images/tanda_tangan_base64/'.$namapengaju_ttd_fin1, 'my_ttd', 'images/tanda_tangan_base64/'.$namapengaju_ttd_fin1);
                $mail->MsgHTML($message);
                if($mail->Send()) { 
                    
                    echo "Message has been sent";
                    $psudah_email="Y";
                    
                    $query = "UPDATE dbmaster.t_spg_gaji_br0 set apv1=NULL, apvtgl1=NULL, "
                            . " apv2=NULL, apvtgl2=NULL, apvgbr2=NULL, "
                            . " apv3=NULL, apvtgl3=NULL, apvgbr3=NULL, "
                            . " apv4=NULL, apvtgl4=NULL, apvgbr4=NULL WHERE "
                            . " idbrspg IN $f_nobr";
                    mysqli_query($cnmy, $query);
                    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { 
                        echo "ERROR REJECT..."; exit; 
                    }
                    mysqli_close($cnmy);
                    header('location:../../media.php?module='.$module.'&idmenu='.$idmenu.'&act=complt');
                    
                }else {
                    echo "Failed to sending message";
                }
                
            }else{
                echo "Tidak ada Email yang dikirim";
            }
            
        }else{
            echo "Tidak ada Email yang dikirim";
        }
    
    
    
    
    mysqli_close($cnmy);
    
    
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


<?PHP
    
    exit;
    hapusdata:
        mysqli_query($cnmy, "drop Temporary table $tmp01");
        mysqli_close($cnmy);
        
?>
