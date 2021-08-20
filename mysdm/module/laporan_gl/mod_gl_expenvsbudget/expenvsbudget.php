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
                                        <select class='form-control' id="cb_dept" name="cb_dept" onchange="ShowDariDepartemen()">
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
                                
                                <div hidden id="n_divsls">
                                    
                                    <div hidden id="n_divpengaju">
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' id="cb_pengajuan" name="cb_pengajuan" onchange="ShowDariPengajuan()">
                                                    <?PHP
                                                    echo "<option value='' selected>-- All --</option>";
                                                    echo "<option value='ETH' >ETHICAL</option>";
                                                    echo "<option value='OTC' >CHC</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <div id="n_divregion">
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Region <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' id="cb_region" name="cb_region" onchange="ShowDariRegion()">
                                                    <?PHP
                                                    echo "<option value='' selected>-- All Ethical & CHC --</option>";
                                                    echo "<option value='B_ETH' >Barat Ethical</option>";
                                                    echo "<option value='T_ETH' >Timur Ethical</option>";
                                                    echo "<option value='B_OTC' >Barat CHC</option>";
                                                    echo "<option value='T_OTC' >Timur CHC</option>";
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <div id="n_divcabang">

                                        <div class='form-group'>
                                            <div class='col-sm-12'>
                                                <b>Cabang</b> <input type="checkbox" id="chkbtncab" value="deselect" onClick="SelAllCheckBox('chkbtncab', 'chkbox_cab[]')" checked/>
                                                <div class="form-group">
                                                    <div id="kotak-multi3" class="jarak">
                                                        <?PHP
                                                            //echo "&nbsp; <input type=checkbox value='' name='chkbox_cab[]' checked> empty<br/>";

                                                            $query_cab = "select iCabangId as icabangid, nama as nama_cabang, 'ETH' as iket, region from mkt.icabang WHERE IFNULL(aktif,'')<>'N' ";
                                                            $query_cab .= " AND LEFT(nama,5) NOT IN ('PEA -', 'OTC -') ";
                                                            $query_cab .= " UNION ";
                                                            $query_cab .= "select icabangid_o as icabangid, nama as nama_cabang, 'OTC' as iket, region from dbmaster.v_icabang_o WHERE IFNULL(aktif,'')<>'N' ";
                                                            $query_cab = "SELECT * FROM ($query_cab) as ntabel";
                                                            $query_cab .= " ORDER BY nama_cabang";

                                                            $tampil = mysqli_query($cnmy, $query_cab);
                                                            while ($row= mysqli_fetch_array($tampil)) {
                                                                $ncabid=$row['icabangid'];
                                                                $ncabnm=$row['nama_cabang'];
                                                                $niket=$row['iket'];

                                                                $nnmket=$niket;
                                                                if ($niket=="OTC") $nnmket="CHC";

                                                                $pnid_kode=$ncabid."|".$niket;

                                                                echo "&nbsp; <input type='checkbox' onClick=\"ShowCoaDariBudget()\" value='$pnid_kode' name='chkbox_cab[]' checked> $ncabnm - $nnmket<br/>";

                                                            }
                                                        ?>
                                                    </div>
                                                </div>
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
                                                    //echo "&nbsp; <input type=checkbox value='' name='chkbox_coa[]' checked> empty<br/>";
                                                    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
                                                        join dbmaster.coa_level3 as b on a.COA3=b.COA3 
                                                        join dbmaster.coa_level2 as c on b.COA2=c.COA2 WHERE 1=1 ";
                                                    
                                                    //$query .=" AND c.DIVISI2 IN ('CHC', 'OTC', '', 'OTHER', 'OTHERS') ";
                                                    
                                                    $query .= " ORDER BY a.COA4";
                                                    
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pcoa4=$z['COA4'];
                                                        $pnmcoa4=$z['NAMA4'];
                                                        echo "&nbsp; <input type='checkbox' value='$pcoa4' name='chkbox_coa[]' checked> $pcoa4 - $pnmcoa4<br/>";
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b><input type="checkbox" value="c_allexp" id="c_allexp" name="c_allexp" checked> All Expense </b>
                                        <div class="form-group">
                                            &nbsp;
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b><input type="checkbox" value="c_sum" id="chk_sum" name="chk_sum"> Summary </b>
                                        <div class="form-group">
                                            &nbsp;
                                        </div>
                                    </div>
                                </div>
                                
                                <!--
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b> &nbsp; </b>
                                        <div class="form-group">
                                            <?PHP
                                            //if ($pmobile!="Y") {
                                                //echo "<button type='button' class='btn btn-dark' onclick=\"proses_data_byuser('')\">Proses Data</button>";
                                            //}
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                -->

                            </div>
                        </div>           
                    </div>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <button type='button' class='btn btn-success' onclick="disp_confirm('')">Preview</button>
                            <?PHP
                            if ($pmobile!="Y") {
                                echo "<button type='button' class='btn btn-danger' onclick=\"disp_confirm('excel')\">Excel</button>";
                            }
                            ?>
                        </h2>
                        <div class='clearfix'></div>
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
        
        if (nmbuton=="chkbtncab") {
            $("#kotak-multi2").html("");
            setTimeout(function () {
                ShowCoaDariBudget();
            }, 500);
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
    
    
    function ShowDariDepartemen() {
        var idep = document.getElementById('cb_dept').value;
        var ipengajuan = document.getElementById('cb_pengajuan').value;
        
        if (idep=="SLS" || idep=="SLS01" || idep=="SLS02" || idep=="MKT") {
            n_divsls.style.display = 'block';
            n_divpengaju.style.display = 'block';
            n_divcabang.style.display = 'block';
            n_divregion.style.display = 'block';
            
            if (idep=="MKT" && ipengajuan=="OTC") n_divregion.style.display = 'none';
            
            if (idep=="SLS02") {
                n_divregion.style.display = 'block';
                n_divcabang.style.display = 'none';
                n_divpengaju.style.display = 'none';
            }
            
        }else{
            n_divsls.style.display = 'none';
        }
        
        $("#kotak-multi3").html("");
        ShowRegion();
        ShowCabang();
        $("#kotak-multi2").html("");
        setTimeout(function () {
            ShowCoaDariBudget();
        }, 500);
    }
    
    function ShowCoaDariBudget() {
        document.getElementById('chkbtncoa').checked = 'FALSE';
        document.getElementById('chkbtncoa').value = 'deselect';
        
        document.getElementById('chkbtncoa').checked = 'FALSE';
        
        document.getElementById('c_allexp').checked = 'FALSE';
        document.getElementById('c_allexp').value = 'deselect';
        
        
        var idep = document.getElementById('cb_dept').value;
        var itahun = document.getElementById('e_tahun').value;
        var ipengajuan = document.getElementById('cb_pengajuan').value;//divisi
        var iregion = document.getElementById('cb_region').value;
        
        var nfiltercabdiv="";
        var chk_arr =  document.getElementsByName('chkbox_cab[]');
        var chklength = chk_arr.length;
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                var icab = chk_arr[k].value;
                
                nfiltercabdiv=nfiltercabdiv+""+icab+",";
            }
        }
        //alert(nfiltercabdiv);
            
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_expenvsbudget/viewdataexpbgt.php?module=viewdatacoadep",
            data:"udep="+idep+"&utahun="+itahun+"&upengajuan="+ipengajuan+"&uregion="+iregion+"&ucabdivisi="+nfiltercabdiv,
            success:function(data){
                $("#kotak-multi2").html(data);
            }
        });
    }
    
    function ShowDariPengajuan() {
        
        var idep = document.getElementById('cb_dept').value;
        var ipengajuan = document.getElementById('cb_pengajuan').value;
        
        if (idep=="MKT" && ipengajuan=="OTC") n_divregion.style.display = 'none';
        else n_divregion.style.display = 'block';
        
        
        $("#cb_region").html("<option value='' selected>-- All --</option>");
        ShowRegion();
        ShowCabang();
        
        $("#kotak-multi2").html("");
        setTimeout(function () {
            ShowCoaDariBudget();
        }, 500);
    }
    
    function ShowDariRegion() {
        ShowCabang();
        
        $("#kotak-multi2").html("");
        setTimeout(function () {
            ShowCoaDariBudget();
        }, 500);
    }
    
    function ShowRegion() {
        var idep = document.getElementById('cb_dept').value;
        var ipengajuan = document.getElementById('cb_pengajuan').value;
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_expenvsbudget/viewdataexpbgt.php?module=viewdataregion",
            data:"upengajuan="+ipengajuan+"&udep="+idep,
            success:function(data){
                $("#cb_region").html(data);
            }
        });
    }
    
    function ShowCabang() {
        var idep = document.getElementById('cb_dept').value;
        var ipengajuan = document.getElementById('cb_pengajuan').value;
        var iregion = document.getElementById('cb_region').value;
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_expenvsbudget/viewdataexpbgt.php?module=viewdatacabang",
            data:"upengajuan="+ipengajuan+"&uregion="+iregion+"&udep="+idep,
            success:function(data){
                $("#kotak-multi3").html(data);
            }
        });
    }
    
</script>


