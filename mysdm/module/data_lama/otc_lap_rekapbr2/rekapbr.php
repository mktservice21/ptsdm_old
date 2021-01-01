<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    //include "config/koneksimysqli_it.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('Y-m-01', strtotime($hari_ini));
    $tgl_akhir = date('Y-m-d', strtotime($hari_ini));
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>REKAP DATA BR CABANG</h3></div></div><div class="clearfix"></div>
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
                                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idcabang'>Cabang <span class='required'></span></label>
                                      <div class='col-xs-9'>
                                          <select class='soflow' id='icabangid_o' name='icabangid_o' onchange="showArea('e_idcabang', 'cb_areasdm')">
                                              <option value='*' selected>-- All --</option>
                                              <?PHP
                                                $tampil=mysqli_query($cnmy, "SELECT distinct icabangid_o, nama from dbmaster.v_icabang_o where aktif='Y'");
                                                while($a=mysqli_fetch_array($tampil)){
                                                    echo "<option value='$a[icabangid_o]'>$a[nama]</option>";
                                                }
                                                ?>
                                          </select>
                                      </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_subpost'>Posting <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='posting' name='posting' onchange="showPosting('posting', 'subposting')">
                                            <?PHP
                                            $tampil=mysqli_query($cnmy, "select distinct subpost, nmsubpost from hrd.brkd_otc where ifnull(subpost,'') <> '' order by nmsubpost");
                                            echo "<option value='*' selected>-- All --</option>";
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                echo "<option value='$a[subpost]'>$a[nmsubpost]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_post'>Sub-Posting <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='subposting' name='subposting' onchange="">
                                            <?PHP
                                            $filsub="";
                                            if (!empty($subposting)) $filsub="where subpost='$subposting' AND ifnull(subpost,'') <> ''";
                                            
                                            $tampil=mysqli_query($cnmy, "select distinct kodeid, nama from hrd.brkd_otc $filsub order by nama");
                                            echo "<option value='*' selected>-- All --</option>";
                                            while($a=mysqli_fetch_array($tampil)){ 
                                                echo "<option value='$a[kodeid]'>$a[nama]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal BR <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type="date" class='soflow' id='bulan1' name='bulan1' value="<?PHP echo $tgl_pertama; ?>"> s/d. <input type="date" class='soflow' id='bulan2' name='bulan2' value="<?PHP echo $tgl_akhir; ?>">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tgl Transfer <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type="date" class='soflow' id='bulan3' name='bulan3'> s/d. <input type="date" class='soflow' id='bulan4' name='bulan4'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>No Slip <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='slip' name='slip' onchange="">
                                            <option value="*">All</option>
                                            <option value="Y">Ada</option>
                                            <option value="N">Tidak Ada</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Lampiran <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='lamp' name='lamp' onchange="">
                                            <option value="*">All</option>
                                            <option value="Y">Ada</option>
                                            <option value="N">Tidak Ada</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>CA <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='ca' name='ca' onchange="">
                                            <option value="*">All</option>
                                            <option value="Y">Yes</option>
                                            <option value="N">No</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Via SBY <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='via' name='via' onchange="">
                                            <option value="*">All</option>
                                            <option value="Y">Yes</option>
                                            <option value="N">No</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Order By <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='soflow' id='order' name='order' onchange="">
                                            <option value="C">Kode Cabang</option>
                                            <option value="P">Posting</option>
                                            <option value="A">Alokasi</option>
                                            <option value="B">Tgl. BR</option>
                                            <option value="T">Tgl. Transfer</option>
                                        </select>
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
    
    function showPosting(subpost, epost){
        var esubpost = document.getElementById(subpost).value;
        $.ajax({
            type:"post",
            url:"module/mod_br_entryotc/viewdata.php?module=viewdataposting&data1="+esubpost+"&data2="+epost,
            data:"usubpost="+esubpost+"&upost="+epost,
            success:function(data){
                $("#"+epost).html(data);
                showCOANya(subpost, epost, 'cb_coa');
            }
        });
    }
</script>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
</style>