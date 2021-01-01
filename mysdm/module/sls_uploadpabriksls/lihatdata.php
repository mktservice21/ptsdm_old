<?php
    $puserid=$_SESSION['IDCARD'];
    if (empty($puserid)) {
        echo "Anda harus login ulang..."; exit;
    }
    
    //ini_set('memory_limit', '-1');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $skey="1";
    if (isset($_GET['skey'])) {
        $skey=$_GET['skey'];
    }
    
    $pnamatext_file="";
    $pjenis=$_POST['cb_untuk'];
    
    $pnmupload="faktur";
    if ($pjenis=="R") $pnmupload="retur";
    
    include ("config/koneksimysqli_ms.php");
    
    $pjudul="Data Penjualan Pabrik";
    if ($pjenis=="R") {
        $pjudul="Data Retur Pabrik";
    }
    $aksi="module/sls_uploadpabriksls/aksi_uploadpabriksls.php";
?>


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

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="">
    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
        <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                
                <div class='x_content' style="margin-left:-20px; margin-right:-20px;"><!-- -->
                    
                <?PHP
                    if ($pjenis=="R") {
                ?>
                    
                    <script type="text/javascript" language="javascript" >

                        function RefreshDataTabel() {
                            KlikDataTabel();
                        }

                        $(document).ready(function() {
                            KlikDataTabel();
                        } );

                        function KlikDataTabel() {
                            var ket="";

                            $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                            $.ajax({
                                type:"post",
                                url:"module/sls_uploadpabriksls/viewdatatableretur.php?module="+ket,
                                data:"eket="+ket,
                                success:function(data){
                                    $("#c-data").html(data);
                                    $("#loading").html("");
                                }
                            });
                        }

                    </script>

                    <div id='loading'></div>
                    <div id='c-data'>

                    </div>
                <?PHP
                    }else{
                ?>
                    
                    <script type="text/javascript" language="javascript" >

                        function RefreshDataTabel() {
                            KlikDataTabel();
                        }

                        $(document).ready(function() {
                            KlikDataTabel();
                        } );

                        function KlikDataTabel() {
                            var ket="";

                            $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                            $.ajax({
                                type:"post",
                                url:"module/sls_uploadpabriksls/viewdatatablesales.php?module="+ket,
                                data:"eket="+ket,
                                success:function(data){
                                    $("#c-data").html(data);
                                    $("#loading").html("");
                                }
                            });
                        }

                    </script>

                    <div id='loading'></div>
                    <div id='c-data'>

                    </div>
                <?PHP
                    }
                ?>
            
        </div>
                
            </div>
        </div>
        
    </div>
</div>


<?PHP
mysqli_close($cnms);
?>