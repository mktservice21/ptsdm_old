<?PHP
    session_start();
    $spdnodivisi=$_GET['nodivisi'];
    $spddivisi=$_GET['divisi'];
    
    $pgroupidpilih=$_SESSION['GROUP'];
    
    $spdidinput="";
    if (isset($_GET['idinspd'])) $spdidinput=$_GET['idinspd'];
    
    $psudahpost=false;
    if (!empty($spdidinput)) {
        include("config/koneksimysqli.php");
        $query ="select idinput FROM dbmaster.t_suratdana_br_close WHERE idinput='$spdidinput'";
        $tampil=mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            $psudahpost=true;
        }
        mysqli_close($cnmy);
    }
    
    
    $psts_posting=false;
    if ($pgroupidpilih=="1" OR $pgroupidpilih=="34") {
        $psts_posting=true;
    }
    
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
        <script src="vendors/jquery/dist/jquery.min.js"></script>
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <?PHP } ?>
        
    <style type="text/css" media="print">
        /*
        @page 
        {
            size: auto;   
            margin-top: 0.2mm;
            margin-bottom: 0.2mm;
        }
        */
    </style>
    
    <style>
        .btn {
          background-color: #4CAF50; /* Green */
          border: none;
          color: white;
          padding: 10px 25px;
          text-align: center;
          text-decoration: none;
          display: inline-block;
          font-size: 14px;
          margin: 4px 2px;
          cursor: pointer;
          box-shadow: 0 5px 5px 0 rgba(0,0,0,0.2), 0 3px 7px 0 rgba(0,0,0,0.19);
        }
        .btn:hover {
          background-color: #e7e7e7;
          color: #000;
        }
        .button1 {background-color: #4CAF50;} /* Blue */
        .button2 {background-color: #008CBA;} /* Blue */
        .button3 {background-color: #f44336;} /* Red */ 
        .button4 {background-color: #e7e7e7; color: black;} /* Gray */ 
        .button5 {background-color: #555555;} /* Black */
    </style>
    
    <script>
        function ProsesDataPosting(ket, noid, nodivisi){

            ok_ = 1;
            if (ok_) {
                var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
                if (r==true) {

                    var txt;
                    if (ket=="reject" || ket=="hapus" || ket=="pending") {
                        var textket = prompt("Masukan alasan "+ket+" : ", "");
                        if (textket == null || textket == "") {
                            txt = textket;
                        } else {
                            txt = textket;
                        }
                    }

                    var myurl = window.location;
                    var urlku = new URL(myurl);
                    var module = urlku.searchParams.get("module");
                    var idmenu = urlku.searchParams.get("idmenu");
                    
                    $.ajax({
                        type:"post",
                        url:"module/laporan_gl/mod_gl_rptspd/proses_posting.php?module="+module+"&act="+ket+"&idmenu="+idmenu,
                        data:"uidspd="+noid+"&unodivisi="+nodivisi,
                        success:function(data){
                            if (data.length > 1) {
                                alert(data);
                            }
                            location.reload();
                        }
                    });
                }
            } else {
                //document.write("You pressed Cancel!")
                return 0;
            }
        }
    </script>
</head>

<body>
<?PHP
    if ($spddivisi=="LK") {
        include "module/laporan_gl/mod_gl_rptspd/spdrpt_lk.php";
    }elseif ($spddivisi=="RUTIN") {
        include "module/laporan_gl/mod_gl_rptspd/spdrpt_rutin.php";
    }elseif ($spddivisi=="INSENTIF") {
        include "module/laporan_gl/mod_gl_rptspd/spdrpt_insentif.php";
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
        elseif ($nkodid=="2" AND $nsubkodid=="39")
            include "module/laporan_gl/mod_gl_rptspd/spdrpt_kascab.php";
        else
            include "module/laporan_gl/mod_gl_rptspd/spdrpt_otc.php";
        
    }else{
        $query = "select * from dbmaster.t_suratdana_br WHERE nodivisi='$spdnodivisi'";
        $tmpl= mysqli_query($cnmy, $query);
        $rx= mysqli_fetch_array($tmpl);
        $nkodid=$rx['kodeid'];
        $nsubkodid=$rx['subkode'];
        
        if ($nkodid=="2" AND $nsubkodid=="39")
            include "module/laporan_gl/mod_gl_rptspd/spdrpt_kascab.php";
        else
			include "module/laporan_gl/mod_gl_rptspd/spdrpt_br.php";
		
    }
?>
</body>
</html>

