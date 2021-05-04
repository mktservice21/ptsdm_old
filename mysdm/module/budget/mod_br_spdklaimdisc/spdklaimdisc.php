<?PHP
    include "config/cek_akses_modul.php";
    $hari_ini = date("Y-m-d");
    $hari_ini2 = date("Y-01-d");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    //$tgl_pertama = date('F Y', strtotime($hari_ini2));
    $tgl_akhir = date('F Y', strtotime($hari_ini));
    
    
    $ptglpilih01="";
    $ptglpilih02="";
    if (isset($_SESSION['SPDKDTGL01'])) $ptglpilih01=$_SESSION['SPDKDTGL01'];
    if (isset($_SESSION['SPDKDTGL02'])) $ptglpilih02=$_SESSION['SPDKDTGL02'];
    
    if (!empty($ptglpilih01)) $tgl_pertama = $ptglpilih01;
    if (!empty($ptglpilih02)) $tgl_akhir = $ptglpilih02;
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $pmodule="";
    $pidmenu="";
    $pact="";
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
    if (isset($_GET['idmenu'])) $pidmenu=$_GET['idmenu'];
    if (isset($_GET['act'])) $pact=$_GET['act'];
?>


<div class="">

    <div class="page-title">
        <div class="title_left">
            <h3>
                <?PHP
                $judul="Permintaan Dana Klaim Discount";
                if ($pact=="tambahbaru")
                    echo "Input $judul";
                elseif ($pact=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h3>
        </div>
    </div>
    <div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        switch($pact){
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
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var eaksi = "module/budget/mod_br_spdklaimdisc/aksi_spdklaimdisc.php";
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/budget/mod_br_spdklaimdisc/viewdatatabelpdkd.php?module="+module+"&idmenu="+idmenu,
                            data:"uperiode1="+etgl1+"&uperiode2="+etgl2+"&uaksi="+eaksi,
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

                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='d-form1' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">

                            <div class='col-sm-2'>
                                Periode Permintaan
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01'>
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
                                   <div class='input-group date' id='cbln02'>
                                       <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                       <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                       </span>
                                   </div>
                               </div>
                           </div>

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

                    </div>
                </div>
                
                
                

                <?PHP

            break;

            case "tambahbaru":
                include "tambahpdkd.php";
            break;

            case "editdata":
                include "tambahpdkd.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>