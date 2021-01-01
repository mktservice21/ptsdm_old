<?PHP
session_start();
$puserid="";
$piket="";
if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];
if (isset($_GET['iprint'])) $piket=$_GET['iprint'];

if (empty($puserid)) {
    echo "ANDA HARUS LOGIN ULANG....";
    exit;
}



$pibrinput=$_GET['brid'];

if (empty($pibrinput)) {
    echo "ID yang akan diedit kosong....";
    exit;
}

$pidmodule=$_GET['module'];
$pidmenu="161";

include "config/koneksimysqli.php";

$pketerangan="";
$ptitle="";
$pjudul="";
$query_cari="";
if ($piket=="nrutin") {
    $ptitle="RUTIN";
    $pjudul="Rutin - Edit Keterangan";
    $query_cari="select keterangan from dbmaster.t_brrutin0 where idrutin='$pibrinput' and kode='1' LIMIT 1";
}elseif ($piket=="nlk") {
    $ptitle="LUAR KOTA";
    $pjudul="Luar Kota - Edit Keterangan";
    $query_cari="select keterangan from dbmaster.t_brrutin0 where idrutin='$pibrinput' and kode='2' LIMIT 1";
}elseif ($piket=="nca") {
    $ptitle="CA";
    $pjudul="Cash Advance - Edit Keterangan";
    $query_cari="select keterangan from dbmaster.t_ca0 where idca='$pibrinput' LIMIT 1";
}
$ketemu=0;
if (!empty($query_cari)) {
    $tampil=mysqli_query($cnmy, $query_cari);
    $ketemu= mysqli_num_rows($tampil);
}
if ($ketemu==0) {
    echo "Data yang akan diedit tidak ada....";
    exit;
}

$row= mysqli_fetch_array($tampil);
$pketerangan=$row['keterangan'];

$aksi="";
?>
<HTML>
    <HEAD>
        <title>Edit Data <?PHP echo $ptitle; ?> Keterangan</title>
        
        <meta http-equiv="Expires" content="Mon, 01 Sep 2030 1:00:00 GMT">
        <meta http-equiv="Pragma" content="no-cache">
        <?php header("Cache-Control: no-cache, must-revalidate"); ?>
        <link rel="shortcut icon" href="images/icon.ico" />
    </HEAD>
    
    <BODY>
        <script> window.onload = function() { document.getElementById("txt_ket").focus(); } </script>
        
        <center><h3><?PHP echo $pjudul; ?></h3></center>
        <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu&iprint=$piket"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
        
            
            <div class='x_panel'>
                <div class='x_content'>
                    <div class='col-md-12 col-sm-12 col-xs-12'>



                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><b>ID</b> <span class='required'></span></label>
                            <div class='col-md-4'>
                                <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pibrinput; ?>' Readonly>
                            </div>
                        </div>
                        <br/>
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><b>Keterangan</b> <span class='required'></span></label>
                            <div class='col-md-4'>
                                <textarea id="txt_ket" name="txt_ket" rows='6' cols="40" placeholder='Aktivitas'><?PHP echo "$pketerangan"; ?></textarea>
                            </div>
                        </div>

                        <br/>
                        <div class='form-group'>
                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''><b>&nbsp;</b> <span class='required'></span></label>
                            <div class='col-md-4'>
                                <button type='button' class='btn btn-success' onclick="disp_confirm_update()">Update</button>
                            </div>
                        </div>




                    </div>
                </div>
            </div>
            
            
            
        </form>
    </BODY>
    
    <script>
        function disp_confirm_update() {
            //simpan data ke DB
            var cmt = confirm('Apakah akan melakukan edit data...???');
            if (cmt == false) {
                return false;
            }else{
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = "161";
                var iprint = urlku.searchParams.get("iprint");

                //document.getElementById("demo-form2").action = "module/mod_apv_biayarutin/aksi_editkrterangan.php?module="+module+"&act=simpanupdate"+"&idmenu="+idmenu+"&iprint="+iprint;
                document.getElementById("demo-form2").action = "eksekusi3.php?module=simpandataeditlkrutinket&act=simpanupdate"+"&idmenu="+idmenu+"&iprint="+iprint;
                document.getElementById("demo-form2").submit();

            }
        }
    </script>

</HTML>

