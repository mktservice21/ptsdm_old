<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
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
    
    
    $pilihanselectdivisi="";
    $ppildivisiada="";
    
    if (!empty($_SESSION['BRGOPNHOTGL1'])) $tgl_pertama=$_SESSION['BRGOPNHOTGL1'];
    if (!empty($_SESSION['BRGOPNHODIVP'])) $ppildivisiada=$_SESSION['BRGOPNHODIVP'];
    
    $pilihanselectdivisi=$ppildivisiada;
    if (empty($ppildivisiada)) {
        $pilihanselectdivisi=$ppilihanwewenang;
        if ($ppilihanwewenang=="AL") $pilihanselectdivisi="ET";
    }
    
    
    
?>



<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h3>
                <?PHP
                $judul="Stock Opname Gimmick";
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
                        //KlikDataTabel();
                    } );

                    function KlikDataTabel() {
                        var edivprod=document.getElementById('cb_divprod').value;
                        var edivpilih=document.getElementById('cb_udiv').value;
                        var ebulan=document.getElementById('bulan1').value;
                        var ewwnpilihan=document.getElementById('e_wwnpilihan').value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var act = urlku.searchParams.get("act");
            
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_brg_stockopn/viewdatatabel.php?module="+module+"&idmenu="+idmenu+"&act="+act,
                            data:"udivprod="+edivprod+"&udivpilih="+edivpilih+"&ubulan="+ebulan+"&uwwnpilihan="+ewwnpilihan,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                    
                </script>

                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
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
                                Untuk Divisi
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_udiv" name="cb_udiv" onchange="">
                                        <?PHP
                                            if ($ppilihanwewenang=="AL") {
                                                if ($ppildivisiada=="OT") {
                                                    echo "<option value='ET'>ETHICAL</option>";
                                                    echo "<option value='OT' selected>CHC</option>";
                                                }else{
                                                    echo "<option value='ET' selected>ETHICAL</option>";
                                                    echo "<option value='OT'>CHC</option>";
                                                }
                                            }elseif ($ppilihanwewenang=="ET") {
                                                echo "<option value='ET' selected>ETHICAL</option>";
                                            }elseif ($ppilihanwewenang=="OT") {
                                                echo "<option value='OT' selected>CHC</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            
                            <div hidden class='col-sm-2'>
                                Group Produk
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_divprod" name="cb_divprod" onchange="">
                                        <option value="">--All--</option>
                                        <?PHP
                                        
                                            $query = "select DIVISIID, DIVISINM from dbmaster.t_divisi_gimick WHERE 1=1 ";
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
                
            break;
            case "editdata":
                
            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>