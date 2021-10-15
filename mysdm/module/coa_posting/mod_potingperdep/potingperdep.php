<?PHP
    //include "config/cek_akses_modul.php";
    
    date_default_timezone_set('Asia/Jakarta'); 
    //ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('d F Y', strtotime($hari_ini));
    $tgl_akhir = $tgl_pertama;//date('d F Y', strtotime($hari_ini));
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    $fdepartid="";
    $fpengajuan="";
    
    $pidmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pidact=$_GET['act'];
    
    $perror="";
    $pketeksekusi="";
    if (isset($_GET['iderror'])) $perror=$_GET['iderror'];
    if (isset($_GET['keteks'])) $pketeksekusi=$_GET['keteks'];
    
    
    if (!empty($_SESSION['COAPOSDEP1'])) $fdepartid = $_SESSION['COAPOSDEP1'];
    if (!empty($_SESSION['COAPOSDIP2'])) $fpengajuan = $_SESSION['COAPOSDIP2'];
    
?>

<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Posting Budget Per Departemen";
                if ($pidact=="tambahbaru")
                    echo "Input $judul";
                elseif ($pidact=="editdata")
                    echo "Edit $judul";
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
                        //KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var act = urlku.searchParams.get("act");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var edep=document.getElementById('cb_departemen').value;
                        var edivisi=document.getElementById('cb_divisi').value;
                        var epengajuan=document.getElementById('cb_pengajuan').value;
                        
                        if (edep=="") {
                            alert("Departemen harus dipilih");
                            return false;
                        }
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/coa_posting/mod_potingperdep/viewdatatable_postingdep.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"udep="+edep+"&udivisi="+edivisi+"&upengajuan="+epengajuan,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function ShowDataPengajuan() {
                        var idep = document.getElementById('cb_departemen').value;
                        $.ajax({
                            type:"post",
                            url:"module/coa_posting/viewdata_coaposting.php?module=getdatapengajuan",
                            data:"udep="+idep,
                            success:function(data){
                                $("#cb_divisi").html(data);
                            }
                        });
                    }
                    
                    function disp_confirm(pText)  {
                        
                        var edep=document.getElementById('cb_departemen').value;
                        var edivisi=document.getElementById('cb_divisi').value;
                        var epengajuan=document.getElementById('cb_pengajuan').value;
                        
                        if (edep=="") {
                            alert("Departemen harus dipilih");
                            return false;
                        }

                        if (pText == "excel") {
                            document.getElementById("d-form2").action = "<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu&ket=excel"; ?>";
                            document.getElementById("d-form2").submit();
                            return 1;
                        }else{
                            document.getElementById("d-form2").action = "<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu&ket=bukan"; ?>";
                            document.getElementById("d-form2").submit();
                            return 1;
                        }

                    }
                </script>
                
                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        
                        <?PHP
                        
                        if ($perror=="error" OR $perror=="berhasil" OR $perror=="hapusok") {

                            echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                                echo "<div class='x_panel'>";

                                    echo "<div class='x_title'>";
                                        if ($perror=="error") {
                                            echo "<h2 style='color:red;'>Gagal Simpan Data...</h2>";
                                            echo "<div class='clearfix'></div>";
                                            echo "<div>($pketeksekusi)</div>";
                                        }elseif ($perror=="berhasil") {
                                            echo "<h2 style='color:blue;'>berhasil simpan dengan id : $pketeksekusi</h2>";
                                        }
                                        echo "<ul class='nav navbar-right panel_toolbox'><li><a class='close-link'><i class='fa fa-close'></i></a></li></ul>";
                                        echo "<div class='clearfix'></div>";

                                    echo "</div>";

                                echo "</div>";

                            echo "</div>";

                        }
                        ?>
                        
                        <div hidden class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                              id='d-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">


                            <div class='col-sm-3'>
                                Departemen
                                <div class="form-group">
                                    <select class='form-control' id="cb_departemen" name="cb_departemen" onchange="">
                                        <?PHP
                                            echo "<option value=''>--Pilih--</option>";
                                            
                                            $query_dep = "select iddep, nama_dep, aktif from dbmaster.t_department WHERE 1=1 ";
                                            $query_dep .=" AND IFNULL(aktif,'')<>'N'";
                                            $query_dep .=" ORDER BY nama_dep";
                                            
                                            if (!empty($query_dep)) {
                                                $tampil = mysqli_query($cnmy, $query_dep);
                                                $ketemu= mysqli_num_rows($tampil);
                                                if ((INT)$ketemu<=0) echo "<option value='' selected>-- Pilih --</option>";

                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pdepid=$z['iddep'];
                                                    $pdepnm=$z['nama_dep'];

                                                    if ($pdepid==$fdepartid)
                                                        echo "<option value='$pdepid' selected>$pdepnm</option>";
                                                    else
                                                        echo "<option value='$pdepid'>$pdepnm</option>";
                                                    
                                                }
                                            }else{
                                                echo "<option value='' selected>-- Pilih --</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-3'>
                                Divisi
                                <div class="form-group">
                                    <select class='form-control' id="cb_divisi" name="cb_divisi">
                                        <?PHP
                                            $query = "select DivProdId as divisi, nama as namadivisi from mkt.divprod where DivProdId in ('CAN', 'EAGLE', 'HO', 'PEACO', 'PIGEO', 'OTC', 'OTHER') ";
                                            $query .=" ORDER BY nama";
                                            $tampil = mysqli_query($cnmy, $query);
                                            $ketemu= mysqli_num_rows($tampil);
                                            echo "<option value='' selected>-- Pilih --</option>";
                                            
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                $pdivisiid=$z['divisi'];
                                                $pdivisinm=$z['namadivisi'];

                                                if ($pdivisiid==$fpengajuan)
                                                    echo "<option value='$pdivisiid' selected>$pdivisinm</option>";
                                                else
                                                    echo "<option value='$pdivisiid'>$pdivisinm</option>";

                                            }
                                            
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-3'>
                                Pengajuan
                                <div class="form-group">
                                    <select class='form-control' id="cb_pengajuan" name="cb_pengajuan">
                                        <?PHP
                                            echo "<option value='' selected>-- All --</option>";
                                            echo "<option value='ETH'>ETHICAL</option>";
                                            echo "<option value='OTC'>CHC</option>";
                                            
                                        ?>
                                    </select>
                                </div>
                            </div>
                            

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                                    <?PHP
                                    if ($_SESSION['MOBILE']!="Y") {
                                        echo "<button type='button' class='btn btn-danger btn-xs' onclick=\"disp_confirm('excel')\">Excel</button>";
                                    }
                                    ?>
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
                include "tambahposdep.php";
            break;

            case "editdata":
                include "tambahposdep.php";
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


