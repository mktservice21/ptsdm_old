<?PHP
    session_start();
    $spdnodivisi=$_GET['nodivisi'];
    $spddivisi=$_GET['divisi'];
    
    $ppilihrpt=$_GET['ket'];
    if ($ppilihrpt=="excel") {
        // Fungsi header dengan mengirimkan raw data excel
        header("Content-type: application/vnd-ms-excel");
        // Mendefinisikan nama file ekspor "hasil-export.xls"
        header("Content-Disposition: attachment; filename=REKAP_BUDGET_REQUEST_NODIVISI_$spdnodivisi.xls");
    }
    
    include("config/koneksimysqli.php");
    $cnit=$cnmy;
    

?>


<html>
<head>
    <title>Rekap Budget Request</title>
    <?PHP if ($ppilihrpt!="excel") { ?>
        <meta http-equiv="Expires" content="Mon, 01 Apr 2019 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <link rel="shortcut icon" href="images/icon.ico" />
        <link href="css/laporanbaru.css" rel="stylesheet">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <?PHP } ?>
</head>

<body>
<?PHP
    if ($spddivisi=="LK") {
        include "module/laporan_gl/mod_gl_rptspd/spdrpt_lk.php";
    }elseif ($spddivisi=="RUTIN") {
        include "module/laporan_gl/mod_gl_rptspd/spdrpt_rutin.php";
    }elseif ($spddivisi=="KAS" OR $spddivisi=="KASCOR") {
        include "module/laporan_gl/mod_gl_rptspd/spdrpt_kas.php";
    }elseif ($spddivisi=="OTC") {
        $query = "select * from dbmaster.t_suratdana_br WHERE nodivisi='$spdnodivisi'";
        $tmpl= mysqli_query($cnmy, $query);
        $rx= mysqli_fetch_array($tmpl);
        $nkodid=$rx['kodeid'];
        $nsubkodid=$rx['subkode'];
        //echo "$spddivisi, $nkodid, $nsubkodid"; exit;
        if ($nkodid=="1" AND $nsubkodid=="03")
            include "module/laporan_gl/mod_gl_rptspd/spdrpt_rutin.php";
        elseif ($nkodid=="2" AND $nsubkodid=="21")
            include "module/laporan_gl/mod_gl_rptspd/spdrpt_lk.php";
        else
            include "module/laporan_gl/mod_gl_rptspd/spdrpt_otc.php";
        
    }else{
        include "module/laporan_gl/mod_gl_rptspd/spdrpt_br.php";
    }
?>
</body>
</html>

