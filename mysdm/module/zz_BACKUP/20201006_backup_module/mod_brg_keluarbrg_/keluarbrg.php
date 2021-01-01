<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_pilih = date('d F Y', strtotime($hari_ini));
    
    $pperiode_ = date('Ym', strtotime($hari_ini));
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    
    $query = "select PILIHAN from dbmaster.t_barang_wewenang WHERE karyawanid='$fkaryawan'";
    $tampil_= mysqli_query($cnmy, $query);
    $pn= mysqli_fetch_array($tampil_);
    $ppilihanwewenang=$pn['PILIHAN'];
    echo "<input type='hidden' id='e_wwnpilihan' name='e_wwnpilihan' value='$ppilihanwewenang' Readonly>";
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

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Data Keluar Barang (Gimmick)";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                elseif ($_GET['act']=="isiresi")
                    echo "Isi No. Resi $judul";
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
                        KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var edivprod=document.getElementById('cb_divprod').value;
                        var ewwnpilihan=document.getElementById('e_wwnpilihan').value;
                        var ebulan=document.getElementById('bulan1').value;
                        
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_brg_keluarbrg/viewdatatabel.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"udivprod="+edivprod+"&uwwnpilihan="+ewwnpilihan+"&ubulan="+ebulan,
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
                                document.getElementById("d-form2").action = "module/mod_brg_keluarbrg/aksi_keluarbrg.php?module="+module+"&idmenu="+idmenu+"&act="+ket+"&kethapus="+"&ket="+ket+"&id="+noid;
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
                            
                            <div class='col-sm-2'>
                                Divisi
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_divprod" name="cb_divprod" onchange="">
                                        <option value="">--All--</option>
                                        <?PHP
                                            $query = "select DIVISIID, DIVISINM from dbmaster.t_divisi_gimick WHERE IFNULL(STS,'')='M' ";
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

            case "tambahbaru":
                include "tambah.php";
            break;
            case "editdata":
                include "tambah.php";
            break;
            case "isiresi":
                include "isinoresi.php";
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