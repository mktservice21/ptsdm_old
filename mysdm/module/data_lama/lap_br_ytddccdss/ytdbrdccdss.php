<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    include "config/koneksimysqli_it.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('Y', strtotime($hari_ini));
?>

<script>
function showCOANya(divisi, coa){
    var ediv = document.getElementById(divisi).value;
    $.ajax({
        type:"post",
        url:"module/data_lama/lap_br_ytddccdss/viewdata.php?module=viewdatacombocoa&data1="+ediv+"&data2="+coa,
        data:"udiv="+ediv+"&ucoa="+coa,
        success:function(data){
            $("#"+coa).html(data);
        }
    });
}
</script>

<div class="">

    <div class="page-title"><div class="title_left"><h3>Laporan YTD DCC dan DSS</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                            <button type='button' class='btn btn-danger' onclick="disp_confirm('excel')">Excel</button>
                        </h2>
                        <div class='clearfix'></div>
                    </div>

                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />

                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Regional <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="wilayah" name="wilayah">
                                            <option value="A" selected>PAN</option>
                                            <option value="B">REG 1</option>
                                            <option value="C">REG 2</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="divprodid" name="divprodid" onchange="showCOANya('divprodid', 'kodeid')">
                                            <?PHP
                                            $query = "select DivProdId from MKT.divprod WHERE br='Y' ";
                                            if ($_SESSION['ADMINKHUSUS']=="Y") {
                                                if (!empty($_SESSION['KHUSUSSEL'])) $query .=" AND DivProdId in $_SESSION[KHUSUSSEL]";
                                            }
                                            $query .=" order by DivProdId";
                                            $tampil = mysqli_query($cnit, $query);
                                            while ($z= mysqli_fetch_array($tampil)) {
                                                if ($z['DivProdId']==$divisi)
                                                    echo "<option value='$z[DivProdId]' selected>$z[DivProdId]</option>";
                                                else
                                                    echo "<option value='$z[DivProdId]'>$z[DivProdId]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kode <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="kodeid" name="kodeid">
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tahun <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div class='input-group date' id='thn01'>
                                                <input type='text' id='tahun1' name='tahun1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                            </div>
                        </div>           
                    </div>

                </div>
            </div>
        </form>

    </div>
    <!--end row-->
</div>

<script>
    $(document).ready(function() {
        showCOANya('divprodid', 'kodeid');
    });
</script>

<script>
    function disp_confirm(pText)  {
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