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

    <div class="page-title"><div class="title_left"><h3>Expense VS Budget</h3></div></div><div class="clearfix"></div>
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Departemen <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="cb_dept" name="cb_dept">
                                            <?PHP
                                            echo "<option value='' selected>-- All --</option>";
                                            $query = "select iddep, nama_dep from dbmaster.t_department WHERE aktif<>'N' ";
                                            $query .=" ORDER BY nama_dep";
                                            $tampil = mysqli_query($cnmy, $query);
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $niddep=$row['iddep'];
                                                $nnmdep=$row['nama_dep'];
                                                
                                                echo "<option value='$niddep' >$nnmdep</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>COA</b> <input type="checkbox" id="chkbtncoa" value="deselect" onClick="SelAllCheckBox('chkbtncoa', 'chkbox_coa[]')" checked/>
                                        <div class="form-group">
                                            <div id="kotak-multi2" class="jarak">
                                                <?PHP
                                                    //echo "&nbsp; <input type=checkbox value='' name='chkbox_coa[]' checked> empty<br/>";
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

