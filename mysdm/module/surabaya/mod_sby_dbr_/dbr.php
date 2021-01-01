<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $tgl_terakhir = date('F Y', strtotime($hari_ini));
    
    
    $x_tgl1="";
    $x_tgl2="";
    $x_divisi="";
    $x_via="";
    $x_pajak="";
    
    $x_selvia1="";
    $x_selvia2="selected";
    $x_selvia3="";
    
    $x_selpajak1="selected";
    $x_selpajak2="";
    $x_selpajak3="";
    
    if ($_GET['act']=="complt") {
        if (isset($_GET['xtgl1'])) {
            $x_tgl1=$_GET['xtgl1'];
            $x_tgl2=$_GET['xtgl2'];
            $x_divisi=$_GET['xdivisi'];
            $x_via=$_GET['xvia'];
            $x_pajak=$_GET['xpajak'];
            
            if (!empty($x_tgl1)) $tgl_pertama = $x_tgl1;//date('F Y', strtotime($x_tgl1));
            if (!empty($x_tgl2)) $tgl_terakhir = $x_tgl2;//date('F Y', strtotime($x_tgl2));
            
            
            if (empty($x_via)){
                $x_selvia1="selected";
                $x_selvia2="";
                $x_selvia3="";
            }elseif ($x_via=="T"){
                $x_selvia1="";
                $x_selvia2="";
                $x_selvia3="selected";
            }
            
            
            if ($x_pajak=="Y"){
                $x_selpajak1="";
                $x_selpajak2="selected";
                $x_selpajak3="";
            }elseif ($x_pajak=="T"){
                $x_selpajak1="";
                $x_selpajak2="";
                $x_selpajak3="selected";
            }
            
            
        }
    }
    
    $aksi="eksekusi3.php";
    //$aksi="mrpt0000001.php";
?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Data Budget Request By Surabaya</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                
                                <div class='col-sm-6'>
                                    <b>Periode</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln01'>
                                            <input type='text' id='e_tgl1' name='e_tgl1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='col-sm-6'>
                                    <b>s/d.</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='cbln02'>
                                            <input type='text' id='e_tgl2' name='e_tgl2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_terakhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Divisi</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_divisi" name="cb_divisi" onchange="">
                                                <?PHP
                                                if ($x_divisi=="OTC") {
                                                    echo "<option value='ETHICAL'>ETHICAL</option>";
                                                    echo "<option value='OTC' selected>OTC</option>";
                                                }else{
                                                    echo "<option value='ETHICAL' selected>ETHICAL</option>";
                                                    echo "<option value='OTC'>OTC</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Via Surabaya</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_via" name="cb_via" onchange="">
                                                <?PHP
                                                echo "<option value='' $x_selvia1>-- Pilih --</option>";
                                                echo "<option value='Y' $x_selvia2>Y</option>";
                                                echo "<option value='T' $x_selvia3>T</option>";
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Pajak</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_pajak" name="cb_pajak" onchange="">
                                                <?PHP
                                                echo "<option value='' $x_selpajak1>-- Pilih --</option>";
                                                echo "<option value='Y' $x_selpajak2>Y</option>";
                                                echo "<option value='T' $x_selpajak3>T</option>";
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                

                                <div class='col-sm-6'>
                                    <b></b>
                                    <div class="form-group">
                                        <input onclick="pilihData('')" class='btn btn-primary btn-sm' type='button' name='buttonview1' value='Tampilkan Data'>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>           
                    </div>

                </div>
            </div>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <div class='x_panel'>
                    
                    <div id='loading'></div>
                    <div id='c-data'>
                        <div class='x_content'>
                            
                            <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='10px'>No</th>
                                        <th width='100px'>No ID</th>
                                        <th width='50px'>COA</th>
                                        <th width='30px'>PERKIRAAN</th>
                                        <th width='50px'>Tgl Pengajuan</th>
                                        <th width='50px'>Tgl Transfer</th>
                                        <th width='30px'>Yang Membuat</th>
                                        <th width='250px'>Dokter/Customer/Supplier</th>
                                        <th width='30px'>No Slip</th>
                                        <th width='30px'>Jumlah</th>
                                        <th width='30px'>Jml. Realisasi</th>
                                        <th width='30px'>Selisih</th>
                                        <th width='30px'>Realisasi</th>
                                        <th width='30px'>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                    
                </div>
                
            </div>
            
            
        </form>

    </div>
    <!--end row-->
</div>

<script>
    
    function pilihData(ket){
        var etgl1=document.getElementById('e_tgl1').value;
        var etgl2=document.getElementById('e_tgl2').value;
        var edivisi=document.getElementById('cb_divisi').value;
        var eviasby=document.getElementById('cb_via').value;
        var epajak=document.getElementById('cb_pajak').value;
        
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/surabaya/mod_sby_dbr/viewdatatable.php?module="+ket,
            data:"eket="+ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&udivisi="+edivisi+"&uviasby="+eviasby+"&upajak="+epajak,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
        
    }
    
    
    $(document).ready(function() {
        
        <?PHP
            if ($_GET['act']=="complt") {
                if (isset($_GET['xtgl1'])) {
                    ?>
                    pilihData('');
                    <?PHP
                }
            }
        ?>
            
    } );

</script>