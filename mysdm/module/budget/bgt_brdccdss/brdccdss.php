<?PHP 
    date_default_timezone_set('Asia/Jakarta');
    include "config/cek_akses_modul.php"; 
    $aksi="eksekusi3.php";
    $pact="";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];

?>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="Budget Request DCC/DSS";
                if ($pact=="tambahbaru")
                    echo "Tambah $judul";
                elseif ($pact=="editdata")
                    echo "Edit $judul";
                else
                    echo "Entry $judul";
                ?>
            </h3>
            
        </div>
        
    </div>
    <div class="clearfix"></div>

    <!--row-->
    <div class="row">
        <?php
        include "config/koneksimysqli_ms.php";
        $pidkaryawan=$_SESSION['IDCARD'];
        $pidjabatan=$_SESSION['JABATANID'];
        $pidgroup=$_SESSION['GROUP'];
        
        
        $aksi="eksekusi3.php";
        switch($pact){
            default:
                ?>
                <div class='modal fade' id='myModal' role='dialog'></div>

                <script>
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var etgl1=document.getElementById('e_bln01').value;
                        var etgl2=document.getElementById('e_bln02').value;
                        var etipeid=document.getElementById('cb_tgltipe').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/budget/bgt_brdccdss/viewdatatabledcc.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"utipeid="+etipeid+"&utgl1="+etgl1+"&utgl2="+etgl2,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>

                <script type="text/javascript">
                </script>

                <?PHP
                
                $ptipeid_pl=$_SESSION['FINDDTGLTIPE'];
                $ptgl1_pl01=$_SESSION['FINDDPERENTY1'];
                $ptgl1_pl02=$_SESSION['FINDDPERENTY2'];
        
                $hari_ini = date("Y-m-01");
                
                
                $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
                $tgl_akhir = date('F Y', strtotime($hari_ini));
                
                $sa=""; $sb=""; $sc=""; $sd="selected"; $se="";
                
                if ($ptipeid_pl=="1") $sa="selected";
                elseif ($ptipeid_pl=="2") $sb="selected";
                elseif ($ptipeid_pl=="3") $sc="selected";
                elseif ($ptipeid_pl=="4") $sd="selected";
                elseif ($ptipeid_pl=="5") $se="selected";
                
                if (!empty($ptgl1_pl01)) $tgl_pertama=$ptgl1_pl01;
                if (!empty($ptgl1_pl02)) $tgl_akhir=$ptgl1_pl02;
                
                ?>

                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>


                        <div class='col-sm-3'>
                            Periode By
                            <div class="form-group">
                                <select class='form-control' id="cb_tgltipe" name="cb_tgltipe">
                                    <?PHP
                                        echo "<option value='1' $sa>Last Input / Update</option>";
                                        echo "<option value='2' $sb>Tanggal Transfer</option>";
                                        echo "<option value='3' $sc>Tanggal Terima</option>";
                                        echo "<option value='4' $sd>Tanggal Input</option>";
                                        echo "<option value='5' $se>Tanggal Rpt. SBY</option>";
                                    ?>
                                </select>
                            </div>
                        </div>
                        

                        
                        <div class='col-sm-3'>
                            Bulan
                            <div class="form-group">
                                <div class='input-group date' id='cbln01'>
                                    <input type='text' id='e_bln01' name='e_bln01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class='col-sm-3'>
                            s/d.
                            <div class="form-group">
                                <div class='input-group date' id='cbln01'>
                                    <input type='text' id='e_bln02' name='e_bln02' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class='col-sm-2'>
                            <small>&nbsp;</small>
                            <div class="form-group">
                                <button type='button' class='btn btn-success btn-xs' onclick='KlikDataTabel()'>View Data</button>
                            </div>
                        </div>



                        <div id='loading'></div>
                        <div id='c-data'>
                           
                        </div>



                    </div>
                </div>

                
                <?PHP
            break;

            case "tambahbaru":
                include "tambah_brdcc.php";
            break;

            case "editdata":
                include "tambah_brdcc.php";
            break;

        }
        ?>
    </div>
    <!--end row-->
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