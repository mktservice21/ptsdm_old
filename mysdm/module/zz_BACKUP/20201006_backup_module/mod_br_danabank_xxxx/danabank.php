<?PHP
    $hari_ini2 = date("Y-m-d");
    $hari_ini = date("Y-01-01");
    $tgl_pertama = date('F Y', strtotime($hari_ini2));
    $tgl_akhir = date('F Y', strtotime($hari_ini2));
    
    if (!empty($_SESSION['DBKENTRY1'])) $tgl_pertama = $_SESSION['DBKENTRY1'];
    if (!empty($_SESSION['DBKENTRY2'])) $tgl_akhir = $_SESSION['DBKENTRY2'];
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    
?>

<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Bank";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
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
        //$aksi="module/mod_br_entrybrbulan/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
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
                        var ket="";
                        var etgltipe="";
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var edivisi="";
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_danabank/viewdatatabel.php?module="+ket,
                            data:"eket="+ket+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uidc="+eidc+"&ucabang=",
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    function IsiBankSPD(ntxt_ket) {
                        var ket="";
                        var etgltipe="";
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        var edivisi="";
                        var eidc=<?PHP echo $_SESSION['USERID']; ?> ;
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        if (ntxt_ket=="3") {
                            $.ajax({
                                type:"post",
                                url:"module/mod_br_danabank/prosessaldobank.php?module="+ket,
                                data:"eket="+ket+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uidc="+eidc+"&uketinput="+ntxt_ket,
                                success:function(data){
                                    $("#c-data").html(data);
                                    $("#loading").html("");
                                }
                            });
                        }else{
                            $.ajax({
                                type:"post",
                                url:"module/mod_br_danabank/isibankspd.php?module="+ket,
                                data:"eket="+ket+"&utgltipe="+etgltipe+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uidc="+eidc+"&uketinput="+ntxt_ket,
                                success:function(data){
                                    $("#c-data").html(data);
                                    $("#loading").html("");
                                }
                            });
                        }
                    }

                </script>
                
                <script>
                    function disp_confirm_print(pText)  {
                        
                        //KlikDataTabel();
                        
                        if (pText == "excel") {
                            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }else{
                            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }
                        
                        
                    }
                </script>

                    
                
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru (Debit/Kredit)'
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <?PHP if ($pses_grpuser=="1" OR $pses_grpuser=="24" OR $pses_grpuser=="25") { ?>
                                    &nbsp;<input type='button' class='btn btn-primary' id="s-submit" value="Bank Masuk (Debit)" onclick="IsiBankSPD('1')">
                                    &nbsp;<input type='button' class='btn btn-info' id="s-submit" value="Bank Keluar (Kredit)" onclick="IsiBankSPD('2')">
                                    &nbsp;<input type='button' class='btn btn-warning' id="s-submit" value="Proses Saldo Bank" onclick="IsiBankSPD('3')">
                                <?PHP }else{ ?>
                                    &nbsp;<input type='button' class='btn btn-info' id="s-submit" value="Bank Keluar (Kredit)" onclick="IsiBankSPD('2')">
                                <?PHP } ?>
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">

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

                            <div hidden class='col-sm-2'>
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



                            <div class='col-sm-6'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data Bank" onclick="RefreshDataTabel()">&nbsp;
                                   <?PHP if ($pses_grpuser=="1" OR $pses_grpuser=="24" OR $pses_grpuser=="25") { ?>
                                   <input type='button' class='btn btn-default btn-xs' id="s-print" value="Preview Data Bank" onclick="disp_confirm_print('bukan')">
                                   <input type='button' class='btn btn-danger btn-xs' id="s-excel" value="Excel Data Bank" onclick="disp_confirm_print('excel')">
                                   <?PHP } ?>
                               </div>
                            </div>
                            
                       </form>

                        <div id='loading'></div>
                        <div id='c-data'>

                        </div>

                    </div>
                </div>
                
                
                
                <script>

                    $('#cbln01').on('change dp.change', function(e){
                        document.getElementById('tgl2').value=document.getElementById('tgl1').value;
                    });

                    $('#cbln02').on('change dp.change', function(e){
                        document.getElementById('tgl1').value=document.getElementById('tgl2').value;
                    });
                </script>

                <?PHP

            break;

            case "tambahbaru":
                include "tambah.php";
            break;

            case "editdata":
                include "tambah.php";
            break;

            case "lihatdata":
                include "lihatdata.php";
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>
