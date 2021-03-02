<?php
    include "config/cek_akses_modul.php";
    $fkaryawan=$_SESSION['IDCARD'];
    $fjbtid=$_SESSION['JABATANID'];
    $fgroupidcard=$_SESSION['GROUP'];
    

    
    $aksi="eksekusi3.php";
    $pact="";
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];

    $pidcabangpl="";
    $pidareapl="";
    if (isset($_SESSION['LSTCUSTNEWICAB'])) $pidcabangpl=$_SESSION['LSTCUSTNEWICAB'];
    if (isset($_SESSION['LSTCUSTNEWIARE'])) $pidareapl=$_SESSION['LSTCUSTNEWIARE'];

    
?>
<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="List Data Customer/Outlet Baru Per Februari 2021";
                if ($pact=="tambahbaru")
                    echo "Input $judul";
                elseif ($pact=="editdata")
                    echo "Edit $judul";
                else
                    echo "$judul";
                ?>
            </h3>
            
        </div>
        
    </div>
    <div class="clearfix"></div>
    
    <div class="row">
        <?php
        switch($pact){
            default:
                ?>
        
                <script>
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        var ecabang=document.getElementById('cb_cabang').value;
                        if (ecabang!="") {
                            KlikDataTabel();
                        }
                    } );

                    function KlikDataTabel() {
                        var ecabang=document.getElementById('cb_cabang').value;
                        var earea=document.getElementById('cb_area').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/master/mst_listcustomernew/viewdatatablelstcust.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"ucabang="+ecabang+"&uarea="+earea,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                    function ShowDataArea() {
                        var ecabang=document.getElementById('cb_cabang').value;
                        
                        $.ajax({
                            type:"post",
                            url:"module/master/viewdatamst.php?module=caridataarea",
                            data:"ucabang="+ecabang,
                            async:false,
                            success:function(data){
                                $("#cb_area").html(data);
                            }
                        });
                    }
                </script>
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' 
                              id='form_data1' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            
                            <div class='col-sm-2'>
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_cabang" name="cb_cabang" onchange="ShowDataArea()">
                                        <?PHP
                                            echo "<option value=''>--Piihan--</option>";
                                            $query_cb = "select icabangid as icabangid, nama as nama from mkt.icabang WHERE 1=1 ";
                                            if ($fgroupidcard=="1" OR $fgroupidcard=="24") {
                                            }else{
                                                $query_cb .=" AND icabangid IN (select distinct IFNULL(icabangid,'') FROM mkt.idm0 WHERE karyawanid='$fkaryawan') ";
                                            }
                                            $query_aktif =$query_cb." AND IFNULL(aktif,'')='Y' ";
                                            $query_aktif .=" order by nama";
                                            $tampil= mysqli_query($cnmy, $query_aktif);
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $pidcab=$row['icabangid'];
                                                $pnmcab=$row['nama'];
                                                
                                                $pinidcab=(INT)$pidcab;

                                                if ($fgroupidcard=="1" OR $fgroupidcard=="24") {
                                                    if ($pidcabangpl==$pidcab)
                                                        echo "<option value='$pidcab' selected>$pnmcab ($pinidcab)</option>";
                                                    else
                                                        echo "<option value='$pidcab'>$pnmcab ($pinidcab)</option>";
                                                }else{
                                                    echo "<option value='$pidcab' selected>$pnmcab ($pinidcab)</option>";
                                                }
                                                
                                                $pcabangidpl=$pidcab;
                                            }
                                            
                                            $query_non =$query_cb." AND IFNULL(aktif,'')<>'Y' ";
                                            $query_non .=" order by nama";
                                            $tampil= mysqli_query($cnmy, $query_non);
                                            $ketemunon= mysqli_num_rows($tampil);
                                            if ($ketemunon>0) {
                                                echo "<option value='NONAKTIF'>-- Non Aktif--</option>";
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $pidcab=$row['icabangid'];
                                                    $pnmcab=$row['nama'];
                                                    
                                                    $pinidcab=(INT)$pidcab;
                                                    if ($pidcabangpl==$pidcab)
                                                        echo "<option value='$pidcab' selected>$pnmcab ($pinidcab)</option>";
                                                    else
                                                        echo "<option value='$pidcab'>$pnmcab ($pinidcab)</option>";
                                                    $pcabangidpl=$pidcab;
                                                }
                                            }
                                        ?>
                                    </select>
                                    
                                </div>
                            </div>
                            
                            <div class='col-sm-2'>
                                Area
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_area" name="cb_area" onchange="">
                                        <?PHP
                                        echo "<option value=''>--All--</option>";
                                        if (!empty($pidcabangpl)) {
                                            $query_area="SELECT areaid as areaid, Nama as nama from MKT.iarea where icabangid='$pidcabangpl' ";
                                            $query_ak =$query_area." AND IFNULL(aktif,'')='Y' ";
                                            $query_ak .=" order by nama";
                                            $tampil= mysqli_query($cnmy, $query_ak);
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $pidarea=$row['areaid'];
                                                $pnmarea=$row['nama'];
                                                
                                                $pintidarea=(INT)$pidarea;
                                                if ($pidarea==$pidareapl)
                                                    echo "<option value='$pidarea' selected>$pnmarea ($pintidarea)</option>";
                                                else
                                                    echo "<option value='$pidarea'>$pnmarea ($pintidarea)</option>";
                                            }
                                            
                                            
                                            $query_non =$query_area." AND IFNULL(aktif,'')<>'Y' ";
                                            $query_non .=" order by nama";
                                            $tampil= mysqli_query($cnmy, $query_non);
                                            $ketemunon= mysqli_num_rows($tampil);
                                            if ($ketemunon>0) {
                                                echo "<option value='NONAKTIF'>-- Non Aktif--</option>";
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $pidarea=$row['areaid'];
                                                    $pnmarea=$row['nama'];
                                                    
                                                    $pintidarea=(INT)$pidarea;
                                                    if ($pidarea==$pidareapl)
                                                        echo "<option value='$pidarea' selected>$pnmarea ($pintidarea)</option>";
                                                    else
                                                        echo "<option value='$pidarea'>$pnmarea ($pintidarea)</option>";
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            


                            <div class='col-sm-2'>
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
        
        }
        ?>
        
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