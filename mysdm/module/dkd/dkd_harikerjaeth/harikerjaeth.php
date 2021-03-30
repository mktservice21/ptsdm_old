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
                $judul="Hari Kerja";
                echo "$judul";
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

                <script>
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var etahun=document.getElementById('e_tahun').value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/dkd/dkd_harikerjaeth/viewdatatablehk.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"utahun="+etahun,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                </script>

                <script type="text/javascript">
                    $(function() {

                        $('#e_tanggal').datepicker({
                            changeMonth: true,
                            changeYear: true,
                            numberOfMonths: 1,
                            dateFormat: 'dd MM yy',
                            onSelect: function(dateStr) {
                                
                            },
                            beforeShowDay: function (date) {
                                var day = date.getDay();
                                return [(day == 1)];
                            }
                        });

                    });
                </script>

                <?PHP
                
                $hari_ini = date("Y-m-d");
                $tgl_pertama = date('Y', strtotime($hari_ini));
                ?>

                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div class='col-sm-3'>
                            Tahun 
                            <div class='input-group date' id='thn01'>
                                <input type="text" class="form-control" id='e_tahun' name='e_tahun' autocomplete='off' required='required' placeholder='d F Y' value='<?PHP echo $tgl_pertama; ?>' Readonly>
                                <span class='input-group-addon'>
                                    <span class='glyphicon glyphicon-calendar'></span>
                                </span>
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