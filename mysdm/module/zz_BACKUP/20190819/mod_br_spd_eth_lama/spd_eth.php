<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('d F Y', strtotime('-1 month', strtotime($hari_ini)));
    $tgl_pertama = date('d F Y', strtotime($hari_ini));
    $tgl_akhir = date('d F Y', strtotime($hari_ini));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
?>

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
    
<button onclick="topFunction()" id="myBtn" title="Go to top">Top</button>
<div class="">

    <div class="page-title"><div class="title_left">
            <h3>
                <?PHP
                $judul="Permintaan Dana";
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
        //$aksi="module/mod_br_spd/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
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
                        var eaksi = "module/mod_br_spd/aksi_spd.php";
                        var etgl1=document.getElementById('tgl1').value;
                        var etgl2=document.getElementById('tgl2').value;
                        
                        var etgltipe=document.getElementById('cb_tgltipe').value;
                        var edivisi=document.getElementById('cb_divisi').value;
                        var ejenis=document.getElementById('cb_jenis').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_spd_eth/viewdatatabel.php?module=viewdata",
                            data:"ujenis="+ejenis+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&uaksi="+eaksi+"&udivisi="+edivisi+"&utgltipe="+etgltipe+"&idmenu="+idmenu+"&module="+module,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }

                    $(function() {

                        $('#tgl1').datepicker({
                            changeMonth: true,
                            changeYear: true,
                            numberOfMonths: 1,
                            firstDay: 1,
                            dateFormat: 'dd MM yy',
                            onSelect: function(dateStr) {
                                document.getElementById('tgl2').value=document.getElementById('tgl1').value;
                            }
                        });

                        $('#tgl2').datepicker({
                            changeMonth: true,
                            changeYear: true,
                            numberOfMonths: 1,
                            firstDay: 1,
                            dateFormat: 'dd MM yy',
                            onSelect: function(dateStr) {
                                
                            }
                        });

                    });
                </script>

                    
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>

                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
                            
                            <div class='col-sm-2'>
                                Periode By
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_tgltipe" name="cb_tgltipe">
                                        <option value="2" selected>Tanggal Transfer</option>
                                        <option value="4" >Tanggal Input</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='col-sm-2'>
                                &nbsp;
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01x'>
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
                                   <div class='input-group date' id='cbln02x'>
                                       <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                       <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                       </span>
                                   </div>
                               </div>
                           </div>
                            
                            <div class='col-sm-2'>
                                Divisi
                                <div class="form-group">
                                    <?PHP
                                        ComboSelectIsiDivisiProdFilter("", "",
                                                "", "$fstsadmin", "$flvlposisi", "$fdivisi", "$_SESSION[FINDDDIV]");
                                    ?>
                                </div>
                            </div>

                            <div class='col-sm-1'>
                                Lampiran
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_jenis" name="cb_jenis">
                                        <option value="Y" selected>Ya</option>
                                        <option value="N">Tidak</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="RefreshDataTabel()">&nbsp;
                               </div>
                           </div>
                            
                            
                            <div id='loading'></div>
                            <div id='c-data'>

                            </div>
                        
                       </form>

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

<script>
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
</script>