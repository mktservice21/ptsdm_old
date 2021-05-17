<?php
    //$_SESSION['BNKDANATIPE']="";
    //$_SESSION['BNKDANATGL01']="";
?>


<?PHP
    include "config/cek_akses_modul.php";
    $hari_ini = date("Y-m-d");
    $hari_ini2 = date("Y-01-d");
    //$tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_akhir = date('F Y', strtotime($hari_ini));
    
    $ptipepilih="";
    $ptglpilih01="";
    $ptglpilih02="";
    if (isset($_SESSION['BNKDANATIPE'])) $ptipepilih=$_SESSION['BNKDANATIPE'];
    if (isset($_SESSION['BNKDANATGL01'])) $ptglpilih01=$_SESSION['BNKDANATGL01'];
    
    if (!empty($ptglpilih01)) $tgl_pertama = $ptglpilih01;
    if (!empty($ptglpilih02)) $tgl_akhir = $ptglpilih02;
    if (empty($ptipepilih)) $ptipepilih = "viewdatabank";
    
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
                $judul="Bank";
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
                        var etipe=document.getElementById('txt_tipe').value;
                        if (etipe=="viewdatabankkeluar") {
                            ViewDataBankKeluar('2');
                        }else{
                            KlikDataTabel();
                        }
                    } );

                    function KlikDataTabel() {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var eaksi = "module/budget/bgt_danabank/aksi_danabank.php";
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl1').value;
                        var etipe=document.getElementById('txt_tipe').value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/budget/bgt_danabank/viewdatatabeldbank.php?module="+module+"&idmenu="+idmenu,
                            data:"uperiode1="+etgl1+"&uperiode2="+etgl2+"&utipe="+etipe+"&uaksi="+eaksi,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function ProsesDataHapus(ket, noid){
                        
                        
                        ok_ = 1;
                        if (ok_) {
                            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');

                            if (r==true) {

                                var txt;
                                var textket = prompt("Masukan alasan "+ket+" : ", "");
                                if (textket == null || textket == "") {
                                    txt = textket;
                                } else {
                                    txt = textket;
                                }


                                if (txt=="") {
                                    alert("alasan harus diisi...");
                                    return false;
                                }else if (txt==null) {
                                    return false;
                                }

                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                //document.write("You pressed OK!")
                                document.getElementById("d-form2").action = "module/budget/bgt_danabank/aksi_danabank.php?module="+module+"&act=hapus&idmenu="+idmenu+"&kethapus="+txt+"&ket="+ket+"&id="+noid;
                                document.getElementById("d-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }



                    }
                    
                    
                    function ViewDataBankKeluar(ntxt_ket) {
                        var ket="";
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl1').value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/budget/bgt_danabank/viewdatatabeldbankkeluar.php?module="+module+"&idmenu="+idmenu,
                            data:"uketinput="+ntxt_ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2,
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
                            <h2>
                                <input class='btn btn-default' type=button value='Tambah Baru (Debit/Kredit)'
                                    onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                
                                &nbsp;<input type='button' class='btn btn-info' id="s-submit" value="Bank Keluar (Kredit)" onclick="ViewDataBankKeluar('2')">
                                
                                
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='d-form1' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
                            <input type="hidden" id="txt_tipe" name="txt_tipe" value="<?PHP echo $ptipepilih; ?>" Readonly>
                            <div class='col-sm-2'>
                                Periode 
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01'>
                                        <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data Bank" onclick="RefreshDataTabel()">&nbsp;
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
                include "tambahdbank.php";
            break;

            case "editdata":
                include "tambahdbank.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>