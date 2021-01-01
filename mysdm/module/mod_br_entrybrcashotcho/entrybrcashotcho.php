<?PHP
    date_default_timezone_set('Asia/Jakarta'); 
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    $tgl_akhir = date('F Y', strtotime($hari_ini));
    
    if (!empty($_SESSION['FINCAAPERENTYOTC1'])) $tgl_pertama = $_SESSION['FINCAAPERENTYOTC1'];
    if (!empty($_SESSION['FINCAAPERENTYOTC2'])) $tgl_akhir = $_SESSION['FINCAAPERENTYOTC2'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    
    $pidmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pidact=$_GET['act'];
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Cash Advance OTC By HO";
                if ($pidact=="tambahbaru")
                    echo "Input $judul";
                elseif ($pidact=="editdata")
                    echo "Edit $judul";
                elseif ($pidact=="uploaddok")
                    echo "Upload Bukti $judul";
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
                        
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_entrybrcashotcho/viewdatatabel.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"uperiode1="+etgl1+"&uperiode2="+etgl2,
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
                                document.getElementById("demo-form2").action = "module/mod_br_entrybrcashotcho/aksi_entrybrluarkotaotcho.php?module="+module+"&idmenu="+idmenu+"&act=hapus&kethapus="+txt+"&ket="+ket+"&id="+noid;
                                document.getElementById("demo-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }



                    }
                </script>
                

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                                onclick="window.location.href='<?PHP echo "?module=$pidmodule&idmenu=$pidmenu&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>

                        <form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">


                            <div class='col-sm-2'>
                                Periode Input
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
                include "tambah.php";
            break;

            case "editdata":
                include "tambah.php";
            break;

            case "uploaddok":
                include "uploaddok.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>

