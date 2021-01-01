<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_pilih = date('d F Y', strtotime($hari_ini));
    
    $pperiode_ = date('Ym', strtotime($hari_ini));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $pistatus="B";
    if (!empty($_SESSION['SBPJSINPBLN01'])) $tgl_pertama = $_SESSION['SBPJSINPBLN01'];
    if (!empty($_SESSION['SBPJSINPTIPE'])) $pistatus = $_SESSION['SBPJSINPTIPE'];
    
?>




<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Permintaan Dana BPJS";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "$judul";
                ?>
            </h3>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/mod_br_brrutin/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
        
                <script>
                    
                    function RefreshDataTabel() {
                        KlikDataTabel();
                    }

                    $(document).ready(function() {
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var act = urlku.searchParams.get("act");
                        if (act=="sudahsimpan") {
                            KlikDataTabel();
                        }
                    } );

                    function KlikDataTabel() {
                        var etipe=document.getElementById('cb_rtptype').value;
                        var ebulan=document.getElementById('bulan1').value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_br_spdbpjs/viewdatatabel.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"utipe="+etipe+"&ubulan="+ebulan,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                    
                    
                    function ProsesData(ket, noid){

                        ok_ = 1;
                        if (ok_) {
                            var r = confirm('Apakah akan melakukan proses '+ket+' ...?');
                            if (r==true) {

                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                //document.write("You pressed OK!")
                                document.getElementById("d-form2").action = "module/mod_br_spdbpjs/aksi_spdbpjs.php?module="+module+"&idmenu="+idmenu+"&act="+ket+"&kethapus="+"&ket="+ket+"&id="+noid;
                                document.getElementById("d-form2").submit();
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
                                onclick="window.location.href='<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru"; ?>';">
                                <small></small>
                            </h2>
                            <div class='clearfix'></div>
                        </div>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                        
                            <div class='col-sm-3'>
                                Status (sudah minta dana)
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_rtptype" name="cb_rtptype" onchange="">
                                        <?PHP
                                        if ($pistatus=="A") {
                                            echo "<option value=''></option>";
                                            echo "<option value='B'>DARI BLN LALU (belum minta dana)</option>";
                                            echo "<option value='A' selected>SEMUA (sudah minta dana)</option>";
                                        }elseif ($pistatus=="B") {
                                            echo "<option value=''></option>";
                                            echo "<option value='B' selected>DARI BLN LALU (belum minta dana)</option>";
                                            echo "<option value='A'>SEMUA (sudah minta dana)</option>";
                                        }else{
                                            echo "<option value=''></option>";
                                            echo "<option value='B'>DARI BLN LALU (belum minta dana)</option>";
                                            echo "<option value='A' selected>SEMUA (sudah minta dana)</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            

                            <div id="div_bln">
                                <div class='col-sm-2'>
                                    Bulan
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln01'>
                                            <input type='text' id='bulan1' name='bulan1' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
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
                            <table id='datatablebmsby' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='10px'>No</th>
                                        <th width='10px'></th>
                                        <th width='50px'>Karyawan ID</th>
                                        <th width='50px'>Nama</th>
                                        <th width='50px'>Gaji Pokok</th>
                                        <th width='10px'>Kelas</th>
                                        <th width='40px'>Potongan <br/>Perusahaan(4%)</th>
                                        <th width='40px'>Potongan <br/>Karyawan(1%)</th>
                                        <th width='40px'>Total Bayar</th>
                                    </tr>
                                </thead>
                                
                            </table>
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