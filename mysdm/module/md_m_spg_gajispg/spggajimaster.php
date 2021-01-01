<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_tunjangan=$tgl_pertama;
    $tgl_gaji=$tgl_pertama;
    if (!empty($_SESSION['SPGMSTGJTGLCAB'])) $tgl_pertama=$_SESSION['SPGMSTGJTGLCAB'];
    
    $query = "select IFNULL(MAX(bulan), CURRENT_DATE()) bulan from dbmaster.t_spg_gaji_zona_jabatan";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0){
        $rw= mysqli_fetch_array($tampil);
        if (!empty($rw['bulan'])){
            $mtgl=$rw['bulan'];
            $tgl_pertama = date('F Y', strtotime($mtgl));
        }
    }
    
    $query = "select IFNULL(MAX(bulan), CURRENT_DATE()) bulan from dbmaster.t_spg_gaji_jabatan";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0){
        $rw= mysqli_fetch_array($tampil);
        if (!empty($rw['bulan'])){
            $mtgl=$rw['bulan'];
            $tgl_tunjangan = date('F Y', strtotime($mtgl));
        }
    }
    
    $query = "select IFNULL(MAX(bulan), CURRENT_DATE()) bulan from dbmaster.t_spg_gaji_area_zona";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ($ketemu>0){
        $rw= mysqli_fetch_array($tampil);
        if (!empty($rw['bulan'])){
            $mtgl=$rw['bulan'];
            $tgl_gaji = date('F Y', strtotime($mtgl));
        }
    }
    
    
?>

<div class="">

    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left">
            <h2>
                <?PHP
                $judul="Master Gaji dan Tunjangan SPG";
                if ($_GET['act']=="tambahbaru")
                    echo "Input $judul";
                elseif ($_GET['act']=="editdata")
                    echo "Edit $judul";
                else
                    echo "Data $judul";
                ?>
            </h2>
    </div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        //$aksi="module/md_m_spg_gajispg/laporanbrbulan.php";
        $aksi="eksekusi3.php";
        switch($_GET['act']){
            default:
                ?>
                <script>
                    $(document).ready(function() {
                        //ShowUMJabatanZona();
                        //ShowTunjanganJabatan();
                        //ShowGajiAreaZona();
                    } );
                </script>
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    
                    <div class='x_panel'>
                        
                        <script>
                            function ShowUMJabatanZona() {
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");
                                var ket="";
                                var etgl1=document.getElementById('tgl1').value;
                                var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/md_m_spg_gajispg/viewumspg.php?module="+ket,
                                    data:"eket="+ket+"&uidc="+eidc+"&idmenu="+idmenu+"&module="+module+"&utgl="+etgl1,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                        </script>
                        
                        <div class="x_title">
                            <span style="color:blue; font-size:13px;"><u>Uang Makan Per Jabatan dan Zona</u></span>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">    
                            
                            <div class='col-sm-2'>
                                Periode Terakhir
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
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="ShowUMJabatanZona()">&nbsp;
                               </div>
                           </div>
                            
                            <div id='loading'></div>
                            <div id='c-data'>
                                
                                <table id='datatablezonajbt' class='datatable table nowrap table-striped table-bordered' width="100%">
                                    <thead>
                                        <tr>
                                            <th width='10px'>NO</th>
                                            <th width='300px' align="center">JABATAN</th>
                                            <th align="center" nowrap>ZONA</th>
                                            <th align="center" nowrap>U. Makan</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                
                            </div>
                            
                        </div>
                    
                    </div>
                    
                </div>

                    
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    
                    <div class='x_panel'>
                        
                        <script>
                            function ShowTunjanganJabatan() {
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");
                                var ket="";
                                var etgl1=document.getElementById('tgl2').value;
                                var eidc=<?PHP echo $_SESSION['USERID']; ?> ;

                                $("#loading2").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/md_m_spg_gajispg/viewtjspg.php?module="+ket,
                                    data:"eket="+ket+"&uidc="+eidc+"&idmenu="+idmenu+"&module="+module+"&utgl="+etgl1,
                                    success:function(data){
                                        $("#c-data2").html(data);
                                        $("#loading2").html("");
                                    }
                                });
                            }
                        </script>
                        
                        <div class="x_title">
                            <span style="color:blue; font-size:13px;"><u>Tunjangan Per Jabatan</u></span>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">    
                            
                            <div class='col-sm-2'>
                                Periode Terakhir
                                <div class="form-group">
                                    <div class='input-group date' id='cbln02'>
                                        <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_tunjangan; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="ShowTunjanganJabatan()">&nbsp;
                               </div>
                           </div>
                            
                            <div id='loading2'></div>
                            <div id='c-data2'>
                                
                                <table id='datatablejbt' class='datatable table nowrap table-striped table-bordered' width="100%">
                                    <thead>
                                        <tr>
                                            <th width='10px'>NO</th>
                                            <th width='300px' align="center">JABATAN</th>
                                            <th align="center" nowrap>SEWA KENDARAAN</th>
                                            <th align="center" nowrap>PULSA</th>
                                            <th align="center" nowrap>BBM</th>
                                            <th align="center" nowrap>PARKIR DAN TOL</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                
                            </div>
                            
                        </div>
                    
                    </div>
                    
                </div>
                
                
                
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    
                    <div class='x_panel'>
                        
                        <script>
                            function ShowGajiAreaZona() {
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");
                                var ket="";
                                var etgl1=document.getElementById('tgl3').value;
                                var ecabang=document.getElementById('e_cabangid').value;
                                var eidc=<?PHP echo $_SESSION['USERID']; ?> ;
                                
                                if (ecabang=="") {
                                    alert("cabang harus diisi");
                                    return false;
                                }
                                $("#loading3").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/md_m_spg_gajispg/viewgpspg.php?module="+ket,
                                    data:"eket="+ket+"&uidc="+eidc+"&idmenu="+idmenu+"&module="+module+"&utgl="+etgl1+"&ucab="+ecabang,
                                    success:function(data){
                                        $("#c-data3").html(data);
                                        $("#loading3").html("");
                                    }
                                });
                            }
                        </script>
                        
                        
                        <div class="x_title">
                            <span style="color:blue; font-size:13px;"><u>Gaji Pokok Per Cabang Area dan Zona</u></span>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">    
                            
                            
                            <div class='col-sm-2'>
                                Periode Terakhir
                                <div class="form-group">
                                    <div class='input-group date' id='cbln03'>
                                        <input type='text' id='tgl3' name='e_periode03' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_gaji; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id='e_cabangid' name='e_cabangid'>
                                        <?PHP
                                            echo "<option value='' selected>-- Pilihan --</option>";
                                            //$query = "select icabangid_o, nama from MKT.icabang_o WHERE aktif='Y' AND nama NOT IN ('OTHER1', 'OTHER2') ORDER BY nama";
                                            $query = "select icabangid_o, nama from dbmaster.v_icabang_o WHERE aktif='Y' AND i_spg='Y' AND nama NOT IN ('OTHER1', 'OTHER2') ORDER BY nama";
                                            $tampil= mysqli_query($cnmy, $query);
                                            while($s= mysqli_fetch_array($tampil)) {
                                                $pcabangid=$s['icabangid_o'];
                                                $pnmcabang=$s['nama'];
                                                if ($pcabangid==$icabang)
                                                    echo "<option value='$pcabangid' selected>$pnmcabang</option>";
                                                else
                                                    echo "<option value='$pcabangid'>$pnmcabang</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="View Data" onclick="ShowGajiAreaZona()">&nbsp;
                               </div>
                           </div>
                            
                            
                            
                            <div id='loading3'></div>
                            <div id='c-data3'>

                            </div>
                            
                            
                            
                            
                        </div>
                    
                    </div>
                    
                </div>
                
                
                
                
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    
                    <div class='x_panel'>
                        
                        <script>
                            function ShowGajiSettingEmail() {
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");
                                var ket="";
                                $("#loading4").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/md_m_spg_gajispg/viewsettingemail.php?module="+ket,
                                    data:"eket="+ket+"&idmenu="+idmenu+"&module="+module,
                                    success:function(data){
                                        $("#c-data4").html(data);
                                        $("#loading4").html("");
                                    }
                                });
                            }
                        </script>
                        
                        
                        <div class="x_title">
                            <span style="color:blue; font-size:13px;"><u>Setting Email</u></span>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>

                        <div class="x_content">    

                            <div class='col-sm-3'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <input type='button' class='btn btn-success btn-xs' id="s-submit" value="Setting Email" onclick="ShowGajiSettingEmail()">&nbsp;
                               </div>
                           </div>
                            
                            
                            
                            <div id='loading4'></div>
                            <div id='c-data4'>

                            </div>
                            
                            
                            
                            
                        </div>
                    
                    </div>
                    
                </div>

                <?PHP

            break;
        
        }
        ?>

    </div>
    <!--end row-->
</div>
<script>
    $(document).ready(function() {
        
        var table = $('#datatablezonajbt').DataTable({
            fixedHeader: true,
            "ordering": false,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "displayLength": -1,
            "order": [[ 0, "asc" ]],
            bFilter: false, bInfo: false, "bLengthChange": false, "bLengthChange": false,
            "bPaginate": false
        } );
            
    } );
</script>
