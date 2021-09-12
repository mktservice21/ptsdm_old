<?PHP
    include "config/cek_akses_modul.php";
    
    date_default_timezone_set('Asia/Jakarta'); 
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    $tgl_akhir = date('F Y', strtotime($hari_ini));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    $pidjabatan=$_SESSION['JABATANID'];
    
    $pidmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pidact=$_GET['act'];
    
    $perror="";
    $pketeksekusi="";
    if (isset($_GET['iderror'])) $perror=$_GET['iderror'];
    if (isset($_GET['keteks'])) $pketeksekusi=$_GET['keteks'];
    
    
?>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Data Lokasi";
                if ($pidact=="tambahbaru")
                    echo "Input $judul";
                elseif ($pidact=="editdata")
                    echo "Edit $judul";
                elseif ($pidact=="editdatalsdm")
                    echo "Edit Radius Lokasi SDM";
                elseif ($pidact=="editdatawfh")
                    echo "Edit Radius Lokasi WFH Karyawan";
                elseif ($pidact=="editdataexpsdmkry")
                    echo "Edit Radius Lokasi SDM Karyawan (exception)";
                elseif ($pidact=="tambahbaruexpkry")
                    echo "Input Radius Lokasi SDM Karyawan (exception)";
                else
                    echo "Data $judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="eksekusi3.php";
        switch($pidact){
            default:
                ?>
        
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var act = urlku.searchParams.get("act");
                        var idmenu = urlku.searchParams.get("idmenu");

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/hrd/hrd_lokasi/viewdatatabel_lokasi.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>
                
                
                <script>
                    function ProsesData(ket, noid){

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

                                //document.write("You pressed OK!")
                                document.getElementById("d-form2").action = "module/hrd/hrd_lokasi/aksi_lokasi.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+txt+"&ket="+ket+"&id="+noid;
                                document.getElementById("d-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }


                    }
                    
                    function ShowIframeMapsPerson(slatitude, slongitude) {

                        $.ajax({
                            url: 'module/hrd/hrd_lokasirumah/peta_lokasiwfh.php?module=showiframemaps',
                            type: 'POST',
                            data: {
                                ulat: slatitude,
                                ulong: slongitude,
                            },
                            success: function (data) {
                                $("#div_map").html(data);
                            }
                        })   
                    }
    
                </script>
                

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        
                        <?PHP
                        
                        if ($perror=="error" OR $perror=="berhasil" OR $perror=="hapusok" OR $perror=="updatettd" OR $perror=="uploaddok" OR $perror=="hapusdok") {

                            echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                                echo "<div class='x_panel'>";

                                    echo "<div class='x_title'>";
                                        if ($perror=="error") {
                                            echo "<h2 style='color:red;'>Gagal Simpan Data...</h2>";
                                            echo "<div class='clearfix'></div>";
                                            echo "<div>($pketeksekusi)</div>";
                                        }elseif ($perror=="berhasil") {
                                            echo "<h2 style='color:blue;'>berhasil simpan</h2>";
                                        }elseif ($perror=="hapusok" OR $perror=="updatettd") {
                                            echo "<h2 style='color:blue;'>$pketeksekusi</h2>";
                                        }
                                        echo "<ul class='nav navbar-right panel_toolbox'><li><a class='close-link'><i class='fa fa-close'></i></a></li></ul>";
                                        echo "<div class='clearfix'></div>";

                                    echo "</div>";

                                echo "</div>";

                            echo "</div>";

                        }
                        ?>
                        
                        <div class='x_titleX'>
                            <h2><input class='btn btn-default' type=button value='Tambah Exception Lokasi SDM'
                                onclick="window.location.href='<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=tambahbaruexpkry"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                              id='d-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                               </div>
                           </div>
                       </form>

                        <div id='loading'></div>
                        <div id='c-data'>

                        </div>

                        <div id='div_map'>

                        </div>
                        
                        
                    </div>
                </div>
                
                
                
                

                <?PHP

            break;

            case "tambahbaruexpkry":
                include "tambahlokasiexp.php";
            break;

            case "editdatalsdm":
                include "editsdmlokasi.php";
            break;

            case "editdatawfh":
                include "editkrylokasi.php";
            break;

            case "editdataexpsdmkry":
                include "editkrylokasi.php";
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


