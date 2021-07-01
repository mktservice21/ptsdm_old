<?php

    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    $pmobile=$_SESSION['MOBILE'];
    
    $pmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    $pact=$_GET['act'];
    $aksi="eksekusi3.php";
    
    
    $hari_ini = date("Y-m-d");
    $ptahun = date('Y', strtotime($hari_ini));
    
    $pspanhiden4=""; $pspanhiden2=""; $pspanhiden5=""; $pspanhiden6=""; $pspanhiden12=""; $pspanhiden15="";
    
    if ($fgroupid=="48") {
        $pspanhiden4=""; $pspanhiden2="hidden"; $pspanhiden5="hidden"; $pspanhiden6="hidden"; $pspanhiden12="hidden"; $pspanhiden15="hidden";
    }else{
        if ($fkaryawan=="0000001272") {
            $pspanhiden4=""; $pspanhiden2="hidden"; $pspanhiden5="hidden"; $pspanhiden6="hidden"; $pspanhiden12="hidden";
        }
    }
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Realisasi Biaya Marketing CHC By Cabang</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' id='d-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                            <?PHP
                            if ($pmobile!="Y") {
                                echo "<button type='button' class='btn btn-danger' onclick=\"disp_confirm('excel')\">Excel</button>";
                            }
                            ?>
                            <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>

                    <!--kiri-->
                    <div class='col-md-6 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="cb_divisip" name="cb_divisip">
                                            <?PHP
                                            echo "<option value='OTC' selected>CHC</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div class='input-group date' id='thn01'>
                                                <input type='text' id='e_tahun' name='e_tahun' required='required' class='form-control' placeholder='tahun' value='<?PHP echo $ptahun; ?>' Readonly>
                                                <span class="input-group-addon">
                                                   <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>COA</b> <input type="checkbox" id="chkbtncoa" value="deselect" onClick="SelAllCheckBox('chkbtncoa', 'chkbox_coa[]')" checked/>
                                        <div class="form-group">
                                            <div id="kotak-multi2" class="jarak">
                                                <?PHP
                                                    echo "&nbsp; <input type=checkbox value='' name='chkbox_coa[]' checked> empty<br/>";
                                                    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
                                                        join dbmaster.coa_level3 as b on a.COA3=b.COA3 
                                                        join dbmaster.coa_level2 as c on b.COA2=c.COA2 WHERE 1=1 ";
                                                    
                                                    $query .=" AND c.DIVISI2 IN ('CHC', 'OTC', '', 'OTHER', 'OTHERS') ";
                                                    
                                                    $query .= " ORDER BY a.COA4";
                                                    
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pcoa4=$z['COA4'];
                                                        $pnmcoa4=$z['NAMA4'];
                                                        echo "&nbsp; <input type=checkbox value='$pcoa4' name='chkbox_coa[]' checked> $pcoa4 - $pnmcoa4<br/>";
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Pilih Report Dari</b> 
                                        <?PHP
                                        echo "<input type='checkbox' id='chkbtnrptd' value='deselect' onClick=\"SelAllCheckBoxByNM('chkbtnrptd')\" checked/>";
                                        ?>
                                        
                                        <div class="form-group">
                                            <div id="kotak-multi3" class="jarak">
                                                
                                                <?PHP
                                                    echo "<span $pspanhiden4 class='chkspan4'>&nbsp; <input type=checkbox value='brotc' name='chkbox_rpt4' checked> BR OTC</span><br/>";
                                                    echo "<span $pspanhiden2 class='chkspan2'>&nbsp; <input type=checkbox value='klaimdisc' id='chkbox_rpt2' name='chkbox_rpt2' checked> Klaim Discount</span><br/>";
                                                    echo "<span $pspanhiden5 class='chkspan5'>&nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' checked> Biaya Rutin</span><br/>";
                                                    echo "<span $pspanhiden6 class='chkspan6'>&nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' checked> Biaya Luar Kota</span><br/>";
                                                    echo "<span $pspanhiden12 class='chkspan12'>&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt12' name='chkbox_rpt12' checked> Service Kendaraan</span><br/>";
                                                    echo "<span $pspanhiden15 class='chkspan15'>&nbsp; <input type=checkbox value='pilkascab' id='chkbox_rpt15' name='chkbox_rpt15' checked> Kas Kecil Cabang</span><br/>";
                                                ?>
                                                
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
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);

        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
    }
    
    
    
    function disp_confirm(pText)  {
        if (pText == "excel") {
            document.getElementById("d-form2").action = "<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu&ket=excel"; ?>";
            document.getElementById("d-form2").submit();
            return 1;
        }else{
            document.getElementById("d-form2").action = "<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu&ket=bukan"; ?>";
            document.getElementById("d-form2").submit();
            return 1;
        }
    }
    
    
</script>

