<?PHP
    date_default_timezone_set('Asia/Jakarta');
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime($hari_ini));
    $tgl_akhir = date('d F Y', strtotime($hari_ini));
    
    if (!empty($_SESSION['FDTBRCABTGL1'])) $tgl_pertama = $_SESSION['FDTBRCABTGL1'];
    if (!empty($_SESSION['FDTBRCABTGL2'])) $tgl_akhir = $_SESSION['FDTBRCABTGL2'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
?>

<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Budget Request Tiket dan Hotel";
                if ($_GET['act']=="tambahbaru")
                    echo "Entry $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h3>
        </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        
        $aksi="module/mod_br_entrydcc/aksi_entrybrdcc.php";
        switch($_GET['act']){
            default:
                ?>
                <div class='modal fade' id='myModal' role='dialog'></div>
                
                <script type="text/javascript" language="javascript" >

                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var ket="";
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var nmun = urlku.searchParams.get("nmun");
                        var act = urlku.searchParams.get("act");
                        
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_entrybrdcccab/viewdatatabel.php?module=?module="+module+"&act="+act+"&idmenu="+idmenu+"&nmun="+nmun,
                            data:"eket="+ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                </script>

                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>
                        
                        <div class='col-sm-2'>
                            Periode
                            <div class="form-group">
                                <div class='input-group date' id='tgl01'>
                                    <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                    <span class="input-group-addon">
                                       <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class='col-sm-2'>
                           <small>s/d.</small>
                           <div class="form-group">
                               <div class='input-group date' id='tgl02'>
                                   <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                   <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                   </span>
                               </div>
                           </div>
                       </div>
                       
                        
                        <div class='col-sm-2'>
                            <small>&nbsp;</small>
                           <div class="form-group">
                               <input type='button' class='btn btn-success  btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">
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