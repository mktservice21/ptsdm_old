<?PHP
    //include "config/cek_akses_modul.php";
    $pmodule=""; $pact=""; $pidmenu="";
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
    if (isset($_GET['idmenu'])) $pidmenu=$_GET['idmenu'];
    
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_pilih = date('d F Y', strtotime($hari_ini));
    
    $pperiode_ = date('Ym', strtotime($hari_ini));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    
    $perror="";
    $pketeksekusi="";
    if (isset($_GET['iderror'])) $perror=$_GET['iderror'];
    if (isset($_GET['keteks'])) $pketeksekusi=$_GET['keteks'];
    
    
?>




<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Data Karyawan (Ubah PIN/Password)";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Ubah PIN/Password";
                else
                    echo "$judul";
                ?>
            </h3>
            anda harus ubah password agar bisa masuk ke menu yang lain.
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/purchasing/pch_barang/laporan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                
                include "config/cek_akses_modul.php";
                
                ?>
        
                <script>
                    
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var ejabatanid=document.getElementById('cb_jabatan').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/tools/mod_tools_resetpass/viewdatatabelrstpass.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"ujabatanid="+ejabatanid,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                    
                </script>
                
                
                <?PHP
                if ($perror=="error" OR $perror=="berhasil") {

                    echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                        echo "<div class='x_panel'>";

                            echo "<div class='x_title'>";
                                if ($perror=="error") {
                                    echo "<h2 style='color:red;'>Gagal ubah password</h2>";
                                    echo "<div class='clearfix'></div>";
                                    echo "<div>($pketeksekusi)</div>";
                                }elseif ($perror=="berhasil") {
                                    echo "<h2 style='color:blue;'>password berhasil diubah, silakan logout dan login ulang...</h2>";
                                }
                                echo "<ul class='nav navbar-right panel_toolbox'><li><a class='close-link'><i class='fa fa-close'></i></a></li></ul>";
                                echo "<div class='clearfix'></div>";

                            echo "</div>";

                        echo "</div>";

                    echo "</div>";

                }
                ?>
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=datarstpasskry&idmenu=$pidmenu"; ?>' id='form_data' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                        
                            <div hidden class='col-sm-2'>
                                Jabatan
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_jabatan" name="cb_jabatan" onchange="">
                                        <option value="">--All--</option>
                                        <?PHP
                                        /*
                                            $query = "select DIVISIID, DIVISINM from dbmaster.t_divisi_gimick WHERE 1=1 ";
                                            if ($ppilihanwewenang=="AL") {
                                            }else{
                                                $query .=" AND PILIHAN='$ppilihanwewenang' ";
                                            }
                                            $query .=" order by DIVISINM";
                                            $tampil= mysqli_query($cnmy, $query);
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $pdivid=$row['DIVISIID'];
                                                $pdivnm=$row['DIVISINM'];
                                                echo "<option value='$pdivid'>$pdivnm</option>";
                                            }
                                        */
                                        ?>
                                    </select>
                                </div>
                            </div>
                            


                            <div hidden class='col-sm-2'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <button type='button' class='btn btn-success btn-xs' onclick='KlikDataTabel()'>View Data</button>
                               </div>
                           </div>
                            
                        </form>
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                           
                        </div>
                        
                    </div>
                </div>

                <?PHP

            break;

            case "tambahbaru":
                include "tambah.php";
            break;
            case "editdata":
                include "tambah.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>