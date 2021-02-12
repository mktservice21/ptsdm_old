<?PHP
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    

    $pidmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pidact="tambahbaru";
    $act="input";
    $aksi="module/ks_lihatks/aksi_editmr.php";
    
    include("config/koneksimysqli.php");
    $ptypeapt=""; $ptypeapt2=""; $pnmtypeapt="";
    $paptidinp="";
    $pbulaninp="";
    foreach ($_POST['chkid'] as $noid) {
        $piddokt=$_POST['txtiddokt'][$noid];
        $pnmdokt=$_POST['txtnmdokt'][$noid];
        $pidsr=$_POST['txtidsr'][$noid];
        $pnmsr=$_POST['txtnmsr'][$noid];
        $pidbln=$_POST['txtidbln'][$noid];
        $papttype=$_POST['txtapttyp'][$noid];
        $pidapt=$_POST['txtaptid'][$noid];
        
        $pbulaninp .=$pidbln.",";
        $paptidinp .=$pidapt.",";
        
        if (strpos($ptypeapt2, $papttype)==false) {
            $ptypeapt2 .="'".$papttype."',";
            $ptypeapt .=$papttype.",";
            
            $pnmty="Reguler";
            if ((INT)$papttype==1) $pnmty="Dispensing";
            $pnmtypeapt .=$pnmty.",";
        }
        
        //echo "$pidsr - $piddokt - $pidapt ($papttype) - $pidbln<br/>";
    }
    
    if (!empty($pbulaninp)) $pbulaninp=substr($pbulaninp, 0, -1);
    if (!empty($paptidinp)) $paptidinp=substr($paptidinp, 0, -1);
    if (!empty($ptypeapt)) $ptypeapt=substr($ptypeapt, 0, -1);
    if (!empty($pnmtypeapt)) $pnmtypeapt=substr($pnmtypeapt, 0, -1);

    
?>
<HTML>
<head>
  <title>Edit Apotik Kosong</title>
    <meta http-equiv="Expires" content="Mon, 01 Jan 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
    <style> .str{ mso-number-format:\@; } </style>
    <link href="vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
    <link href="css/stylenew.css" rel="stylesheet" type="text/css" />
    
    
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    
    
    <!-- bootstrap-datetimepicker -->
    <link href="vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css" rel="stylesheet">

    <script src="js/hanyaangka.js"></script>
    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <!--input mask -->
    <script src="js/inputmask.js"></script>
    
        
</head>


<BODY class="nav-md">
    

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>
<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>


<script> window.onload = function() { document.getElementById("e_bulan").focus(); } </script>

<!-- mulai div -->
<div class="container body">
<div class="main_container">


<div class="">
    
<!-- Judul -->
<div class="page-title">
    <div class="title_left">
        <h3>
            <?PHP
            $judul="Apotik KS Kosong";
            if ($pidact=="tambahbaru")
                echo "Edit $judul";
            elseif ($pidact=="editdata")
                echo "Edit $judul";
            else
                echo "Data $judul";
            ?>
        </h3>
    </div>
</div>
<div class="clearfix"></div>
<!-- END judul -->

<!--row-->
<div class="row">

<!-- isinya -->
<div class="">

    <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            
            <div class='x_panel'>
                
                

                <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                      id='form_data01' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            
                            
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Karyawan <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='hidden' class='form-control' id='cb_karyawan' name='cb_karyawan' value='<?PHP echo $pidsr; ?>' Readonly>
                                        <input type='text' class='form-control' id='nm_karyawan' name='nm_karyawan' value='<?PHP echo "$pnmsr ($pidsr)"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Dokter <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='hidden' class='form-control' id='cb_dokter' name='cb_dokter' value='<?PHP echo $piddokt; ?>' Readonly>
                                        <input type='text' class='form-control' id='nm_dokter' name='nm_dokter' value='<?PHP echo "$pnmdokt ($piddokt)"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Apt. Type <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='text' class='form-control' id='txt_nmapttype' name='txt_nmapttype' value='<?PHP echo "$pnmtypeapt"; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='txt_apttype' name='txt_apttype' value='<?PHP echo "$ptypeapt"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Id Apotik <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='text' class='form-control' id='txt_aptid' name='txt_aptid' value='<?PHP echo "$paptidinp"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Bulan <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <input type='text' class='form-control' id='txt_bulan' name='txt_bulan' value='<?PHP echo "$pbulaninp"; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Apotik <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <select class='form-control input-sm' id='e_apotikid' name='e_apotikid' onchange="">
                                        <?PHP
                                            echo "<option value=''>--Pilihan--</option>";
                                            $query = "select idapotik as idapotik, nama from hrd.mr_apt WHERE srid='$pidsr' order by nama";
                                            $tampil= mysqli_query($cnmy, $query);
                                            while($s= mysqli_fetch_array($tampil)) {
                                                $papotikid=$s['idapotik'];
                                                $pnmapotik=$s['nama'];
                                                echo "<option value='$papotikid'>$pnmapotik ($papotikid)</option>";
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-4'>
                                        <button type='button' class='btn btn-success' onclick="disp_simpan('', 'simpanapt')">Simpan</button>
                                    </div>
                                </div>
                                
                                
                                
                                
                                
                                
                                
                                
                                
                            </div>
                            <!-- -->
                            
                            
                        </div>
                    </div>
                
                        
                
                    
                </form>
                
                
            </div>
            
        </div>
        
    </div>
    
    
</div>
<!-- END class="" -->    
    


<!-- END row -->
</div>
    
    
    
    
<!-- END div class="" -->    
</div>

<!-- END main_container -->
</div>
</div>

<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;<br/>&nbsp;

</BODY>

<!--<script src="vendors/jquery/dist/jquery.min.js"></script>-->
<!-- Bootstrap -->
<script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<script type='text/javascript' src='datetime/js/jquery-ui.min.js'></script>
<!-- jquery.inputmask -->
<script src="vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
                                                
                                                
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
    function disp_simpan(pText_,ket)  {
        var isr = document.getElementById('cb_karyawan').value;
        var idr = document.getElementById('cb_dokter').value;
        var iapt = document.getElementById('e_apotikid').value;
                               
        if (isr=="") {
            alert("MR masih kosong...");
            return false;
        }
        if (idr=="") {
            alert("Dokter masih kosong...");
            return false;
        }
        if (iapt=="") {
            alert("Apotik masih kosong...");
            return false;
        }
        var iconfirm_ = "";
        
        iconfirm_="Apakah akan simpan data...?";
        //alert(iconfirm_); return false;

        ok_ = 1;
        if (ok_) {
            var r=confirm(iconfirm_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("form_data01").action = "module/ks_lihatks/simpaneditapt.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("form_data01").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }

    }
</script>



</HTML>

<?PHP
//mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp00");
//mysqli_query($cnmy, "DROP TEMPORARY TABLE $tmp01");
mysqli_close($cnmy);
?>

