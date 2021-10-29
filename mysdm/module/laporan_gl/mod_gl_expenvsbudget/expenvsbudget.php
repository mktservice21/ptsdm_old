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
    
    
    $psemuadep=false;
    $pbolehpilihdep=false;
    $ppilihlini_produk="";
    $query = "select * from dbproses.maping_karyawan_dep WHERE karyawanid='$fkaryawan' AND iddep='ALL'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $psemuadep=true;
        $pbolehpilihdep=true;
    }
    
    $query = "select * from dbproses.maping_karyawan_dep WHERE karyawanid='$fkaryawan'";
    $tampil2= mysqli_query($cnmy, $query);
    $ketemu2= mysqli_num_rows($tampil2);
    if ((INT)$ketemu2>0) $pbolehpilihdep=true;
    
    
    $query = "select DISTINCT divisi_pengajuan from dbproses.maping_karyawan_dep WHERE karyawanid='$fkaryawan' AND IFNULL(divisi_pengajuan,'')='ALL'";
    $tampil= mysqli_query($cnmy, $query);
    $ketemu= mysqli_num_rows($tampil);
    if ((INT)$ketemu>0) {
        $ppilihlini_produk="ALL";
    }else{
    
        $query = "select DISTINCT divisi_pengajuan from dbproses.maping_karyawan_dep WHERE karyawanid='$fkaryawan' AND IFNULL(divisi_pengajuan,'') NOT IN ('ALL', '')";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        $row= mysqli_fetch_array($tampil);
        
        $ppilihlini_produk=$row['divisi_pengajuan'];
    
    }
    
    
    $pilihregion="";
    if ($fjbtid=="05") {
       $query = "select region FROm dbmaster.t_karyawan_posisi WHERE karyawanid='$fkaryawan'";
        $tampil= mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        $row= mysqli_fetch_array($tampil);
        $pilihregion=$row['region'];
    }
    
    if ($fjbtid=="08") {
        $psemuadep=false;
        $pbolehpilihdep=true;
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
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Report By <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <?php
                                            //echo "<label><input type='checkbox' class='js-switch' id='chk_posting' name='chk_posting' value='POSTING'> Report By Posting</label>";
                                            echo "<input type='radio' class='' name='opt_rpttipe' id='opt_rpttipe' value='coa' checked /> COA";
                                            echo "&nbsp; &nbsp; ";
                                            echo "<input type='radio' class='' name='opt_rpttipe' id='opt_rpttipe' value='posting' /> Posting";
                                            echo "&nbsp; &nbsp; ";
                                            echo "<input type='radio' class='' name='opt_rpttipe' id='opt_rpttipe' value='transaksi' /> Transaksi BR";
                                        ?>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Departemen <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <select class='form-control' id="cb_dept" name="cb_dept" onchange="ShowDariDepartemen()">
                                            <?PHP
                                            if ($pbolehpilihdep==false) {
                                                echo "<option value='TIDAKADAHA' selected>-- Tidak Hak Akses --</option>";
                                            }else{
                                                
                                                
                                                $query = "select iddep, nama_dep from dbmaster.t_department WHERE aktif<>'N' ";
                                                if ($fjbtid=="08") {
                                                    $query .=" AND iddep='SLS01' ";
                                                }else{
                                                    if ($psemuadep==false) {
                                                        $query .=" AND iddep IN (select IFNULL(iddep,'') FROM dbproses.maping_karyawan_dep WHERE karyawanid='$fkaryawan')";
                                                    }
                                                }
                                                $query .=" ORDER BY nama_dep";
                                                
                                                $tampil = mysqli_query($cnmy, $query);
                                                $ketemu=mysqli_num_rows($tampil);
                                                
                                                if ((INT)$ketemu>1) echo "<option value='' selected>-- All --</option>";
                                                
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $niddep=$row['iddep'];
                                                    $nnmdep=$row['nama_dep'];

                                                    echo "<option value='$niddep' >$nnmdep</option>";
                                                }
                                                
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div hidden id="n_divslssm">

                                    <div class='form-group'>
                                        <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>SM <span class='required'></span></label>
                                        <div class='col-xs-9'>
                                            <select class='form-control' id="cb_karyawansm" name="cb_karyawansm" onchange="ShowDariKaryawanSM()">
                                                <?PHP
                                                
                                                
                                                
                                                $query_kry = "SELECT karyawanId as karyawanid, nama as nama_karyawan FROM hrd.karyawan WHERE 1=1 ";
                                                $query_kry .= " AND jabatanId IN ('20', '36')";
                                                $query_kry .= " AND (IFNULL(tglkeluar,'')='' OR IFNULL(tglkeluar,'0000-00-00')='0000-00-00') AND IFNULL(aktif,'')<>'N' ";
                                                $query_kry .= " AND LEFT(nama,5) NOT IN ('NN - ', 'NN - ')";
                                                
                                                if ($fjbtid=="36" OR $fdivisi=="OTC") {
                                                    $query_kry .= " AND karyawanId='$fkaryawan' ";
                                                }elseif ($fjbtid=="20") {
                                                    $query_kry .= " AND karyawanId='$fkaryawan' ";
                                                }elseif ($fjbtid=="05" AND !empty($pilihregion)) {
                                                    $query_kry .= " AND karyawanId IN (select distinct IFNULL(karyawanid,'') from mkt.ism0 as a "
                                                            . " JOIN mkt.icabang as b on a.icabangid=b.iCabangId WHERE region='$pilihregion') ";
                                                    
                                                    echo "<option value='' selected>-- All --</option>";
                                                }else{
                                                    echo "<option value='' selected>-- All --</option>";
                                                }
                                                
                                                $query_kry .= " ORDER BY nama";

                                                $tampil = mysqli_query($cnmy, $query_kry);
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $nkryid=$row['karyawanid'];
                                                    $nkrynm=$row['nama_karyawan'];

                                                    echo "<option value='$nkryid' >$nkrynm</option>";

                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                
                                <div hidden id="n_divsls">
                                    
                                    <div hidden id="n_divpengaju">
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Divisi <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' id="cb_pengajuan" name="cb_pengajuan" onchange="ShowDariPengajuan()">
                                                    <?PHP
                                                    if ($fjbtid=="05" OR $fjbtid=="20" OR $fjbtid=="08" OR $fjbtid=="10" OR $fjbtid=="18" OR $fjbtid=="15") {
                                                        echo "<option value='ETH' >ETHICAL</option>";
                                                    }elseif ($fjbtid=="36" OR $fdivisi=="OTC") {
                                                        echo "<option value='OTC' >CHC</option>";
                                                    }else{
                                                        echo "<option value='' selected>-- All --</option>";
                                                        echo "<option value='ETH' >ETHICAL</option>";
                                                        echo "<option value='OTC' >CHC</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                    <div hidden id="n_liniproduk">
                                        
                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Lini Produk <span class='required'></span></label>
                                            <div class='col-xs-9'>
                                                <select class='form-control' id="cb_liniproduk" name="cb_liniproduk" onchange="ShowDariLiniProduk()">
                                                    <?PHP
                                                    
                                                    if ($ppilihlini_produk=="OTC") {
                                                        echo "<option value='OTC' >CHC</option>";
                                                    }else{
                                                        if ($fkaryawan=="0000000257") {
                                                            echo "<option value='EAGLE' >EAGLE</option>";
                                                        }elseif ($fkaryawan=="0000000910") {
                                                            echo "<option value='PEACO' >PEACOCK</option>";
                                                        }elseif ($fkaryawan=="0000000157") {
                                                            echo "<option value='PIGEO' >PIGEON</option>";
                                                        }else{
                                                            echo "<option value='' selected>-- All --</option>";
                                                            echo "<option value='EAGLE' >EAGLE</option>";
                                                            echo "<option value='PEACO' >PEACOCK</option>";
                                                            echo "<option value='PIGEO' >PIGEON</option>";
                                                            echo "<option value='OTC' >CHC</option>";
                                                        }
                                                    }
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
                                                    $fjbtid="05"; $pilihregion="B";
                                                    if ($fjbtid=="05" AND !empty($pilihregion)) {
                                                        if ($pilihregion=="B") echo "<option value='B' >Barat</option>";
                                                        elseif ($pilihregion=="T") echo "<option value='T' >Timur</option>";
                                                    }else{
                                                        echo "<option value='' selected>-- All Ethical & CHC --</option>";
                                                        echo "<option value='B_ETH' >Barat Ethical</option>";
                                                        echo "<option value='T_ETH' >Timur Ethical</option>";
                                                        echo "<option value='B_OTC' >Barat CHC</option>";
                                                        echo "<option value='T_OTC' >Timur CHC</option>";
                                                    }
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
                                        <b><input type="checkbox" value="allexpen" id="c_allexp" name="c_allexp" checked> All Expense </b>
                                        <div class="form-group">
                                            &nbsp;
                                        </div>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <div class='col-sm-12'>
                                        <b><input type="checkbox" value="pilihsummary" id="chk_sum" name="chk_sum"> Summary </b>
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
    
    $(document).ready(function() {
        ShowDariDepartemen();
    } );
    
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
    
        document.getElementById('chkbtncab').checked = 'FALSE';
        document.getElementById('chkbtncab').value = 'deselect';
        document.getElementById('chkbtncab').checked = 'FALSE';
        
        var idep = document.getElementById('cb_dept').value;
        var ipengajuan = document.getElementById('cb_pengajuan').value;
        
        n_divslssm.style.display = 'none';
        n_liniproduk.style.display = 'none';
        
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
        
        if (idep=="SLS03") {
            n_divslssm.style.display = 'block';
        }
        
        if (idep=="MKT") {
            n_divpengaju.style.display = 'none';
            n_liniproduk.style.display = 'block';
            n_divregion.style.display = 'block';
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
        document.getElementById('c_allexp').value = 'allexpen';
        
        
        var idep = document.getElementById('cb_dept').value;
        var itahun = document.getElementById('e_tahun').value;
        var ipengajuan = document.getElementById('cb_pengajuan').value;//divisi
        var iregion = document.getElementById('cb_region').value;
        var ikrysm = document.getElementById('cb_karyawansm').value;
        var ilproduk = document.getElementById('cb_liniproduk').value;
        
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
            url:"module/laporan_gl/mod_gl_expenvsbudget/viewdataexpbgt.php?module=viewdatacoadepproses",
            data:"udep="+idep+"&utahun="+itahun+"&upengajuan="+ipengajuan+"&uregion="+iregion+"&ucabdivisi="+nfiltercabdiv+"&ukrysm="+ikrysm+"&ulproduk="+ilproduk,
            success:function(data){
                $("#kotak-multi2").html(data);
            }
        });
    }
    
    function ShowDariKaryawanSM() {
        
        $("#kotak-multi2").html("");
        setTimeout(function () {
            ShowCoaDariBudget();
        }, 500);
        
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
    
    
    function ShowDariLiniProduk() {
        var idep = document.getElementById('cb_dept').value;
        var ilproduk = document.getElementById('cb_liniproduk').value;
        
        if (idep=="MKT" && ilproduk=="OTC") n_divregion.style.display = 'none';
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
        var ilproduk = document.getElementById('cb_liniproduk').value;
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_expenvsbudget/viewdataexpbgt.php?module=viewdataregion",
            data:"upengajuan="+ipengajuan+"&udep="+idep+"&ulproduk="+ilproduk,
            success:function(data){
                $("#cb_region").html(data);
            }
        });
    }
    
    function ShowCabang() {
        var itahun = document.getElementById('e_tahun').value;
        var idep = document.getElementById('cb_dept').value;
        var ipengajuan = document.getElementById('cb_pengajuan').value;
        var iregion = document.getElementById('cb_region').value;
        var ilproduk = document.getElementById('cb_liniproduk').value;
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_expenvsbudget/viewdataexpbgt.php?module=viewdatacabang",
            data:"utahun="+itahun+"&upengajuan="+ipengajuan+"&uregion="+iregion+"&udep="+idep+"&ulproduk="+ilproduk,
            success:function(data){
                $("#kotak-multi3").html(data);
            }
        });
    }
    
</script>


