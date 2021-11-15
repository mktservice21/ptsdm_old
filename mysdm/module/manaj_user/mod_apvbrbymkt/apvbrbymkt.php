<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<?PHP

$pmodule="";
$pidmenu="";
$pact="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];
if (isset($_GET['idmenu'])) $pidmenu=$_GET['idmenu'];
if (isset($_GET['act'])) $pact=$_GET['act'];

include "config/cek_akses_modul.php";
$aksi="module/manaj_user/mod_apvbrbymkt/aksi_apvbrbymkt.php";
$hari_ini = date("Y-m-d");
$tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
//$tgl_akhir = date('F Y', strtotime('+1 month', strtotime($hari_ini)));
$tgl_akhir = date('F Y', strtotime($hari_ini));

$pkaryawanid = trim($_SESSION['IDCARD']);
$pnamauser = trim($_SESSION['NAMALENGKAP']);
$pgroupid = trim($_SESSION['GROUP']);

$apvpilih="approve";

if (!empty($_SESSION['MUAPVBRMKTSTS'])) $apvpilih=$_SESSION['MUAPVBRMKTSTS'];
if (!empty($_SESSION['MUAPVBRMKTBLN1'])) $tgl_pertama=$_SESSION['MUAPVBRMKTBLN1'];
if (!empty($_SESSION['MUAPVBRMKTBLN2'])) $tgl_akhir=$_SESSION['MUAPVBRMKTBLN2'];
//if (!empty($_SESSION['MUAPVBRMKTAPVBY'])) $pkaryawanid=$_SESSION['MUAPVBRMKTAPVBY'];

?>

<div class='modal fade' id='myModal' role='dialog' class='no-print'></div>
<div class="">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                switch($pact){
                    default:
                        $judul="Approve Realisasi Budget Request (KI)";
                        $pbtnlist_dsb1="disabled";
                        $pbtnlist_dsb2="";
                        break;
                    case "apvbruserbymk":
                        $judul="Approve Budget Request User";
                        $pbtnlist_dsb1="";
                        $pbtnlist_dsb2="disabled";
                    break;
                }
                echo "$judul";
                ?>
            </h3>
            
        </div>
        
    </div>
    <div class="clearfix"></div>
    
    
    <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                
                <div hidden class='col-md-12 col-sm-12 col-xs-12'>
                    <h2>
                        <?PHP
                        
                        echo "<input class='btn btn-default' type=button value='List Approve Realisasi BR (KI)' "
                            . " onclick=\"window.location.href='?module=$pmodule&idmenu=$pidmenu&act=apvbrrealbymk'\" $pbtnlist_dsb1 > ";
                        echo "<input class='btn btn-info' type=button value='List Approve Budget Request User' "
                            . " onclick=\"window.location.href='?module=$pmodule&idmenu=$pidmenu&act=apvbruserbymk'\" $pbtnlist_dsb2 > ";
                        
                        echo "<small></small>";
                        ?>
                        
                    </h2>
                    <div class='clearfix'></div>
                </div>
                
                
                
                <?PHP

                    switch($pact){
                        default:
                            include "apv_brreal.php";
                            break;

                        case "apvbruserbymk":
                            include "apv_bruser.php";
                        break;


                    }

                ?>
                
                
            </div>
        </div>
        
    </div>
        
</div>


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