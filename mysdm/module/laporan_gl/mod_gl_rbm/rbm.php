<?PHP

    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    $fjbtid=$_SESSION['JABATANID'];
    
    
    $jabatan_="";
    $fildiv="";
    $tampilbawahan = "N";
    $filkaryawncabang = "";
    $hanyasatukaryawan = "";
    $fildiv = "('OTC')";
    if (!empty($_SESSION['AKSES_JABATAN'])) {
        $jabatan_ = $_SESSION['AKSES_JABATAN'];
    }

    if (!empty($_SESSION['AKSES_CABANG'])) {
        $filkaryawncabang = $_SESSION['AKSES_CABANG'];
    }
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('Y', strtotime($hari_ini));
    

    
    $pnot_otc=false;
    if ($fgroupid=="28") {
        $pnot_otc=true;
    }
    
    $ppilihpm="";
    if ($fjbtid=="06" OR $fjbtid=="22") {
        $ppilihpm=getfield("select divprodid as lcfields from ms.penempatan_pm WHERE karyawanid='$fkaryawan'");
    }
    
    $aksi="eksekusi3.php";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Realisasi Biaya Marketing</h3></div></div><div class="clearfix"></div>
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
                            <?PHP
                            if ($_SESSION['MOBILE']!="Y") {
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
                                            if ($fgroupid=="22" OR $fgroupid=="2" OR $fgroupid=="46") {
                                                echo "<option value='' selected>--All--</option>";
                                                echo "<option value='ETHICAL' >ETHICAL</option>";
                                                echo "<option value='OTC'>CHC</option>";
                                            }elseif ($fgroupid=="38" OR $fgroupid=="48") {
                                                echo "<option value='OTC' selected>CHC</option>";
                                                $ppilihcoaawal="OTC";
                                            }elseif ($fgroupid=="8") {
                                                echo "<option value='ETHICAL' selected>ETHICAL</option>";
                                                $ppilihcoaawal="ETH";
                                            }else{
                                                
                                                if (!empty($ppilihpm)) {
                                                    echo "<option value='ETHICAL' selected>ETHICAL</option>";
                                                    $ppilihcoaawal="ETH";
                                                }else{
                                                
                                                    if ($_SESSION['DIVISI']=="OTC") {
                                                        echo "<option value='OTC' selected>CHC</option>";
                                                    }else{
                                                        echo "<option value='' selected>--All--</option>";
                                                        echo "<option value='ETHICAL' >ALL ETHICAL</option>";
                                                        echo "<option value='OTC'>CHC</option>";
                                                        echo "<option value='CAN'>CANARY</option>";
                                                        echo "<option value='EAGLE'>EAGLE</option>";
                                                        echo "<option value='HO'>HO</option>";
                                                        echo "<option value='PEACO'>PEACOCK</option>";
                                                        echo "<option value='PIGEO'>PIGEON</option>";
                                                    }
                                                    
                                                }
                                                
                                            }
                                                
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Periode <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <div class="form-group">
                                            <div class='input-group date' id='thn01'>
                                                <input type='text' id='tglfrom' name='bulan1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
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
                                                    //$query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a ";
                                                    /*
                                                    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
                                                        join dbmaster.coa_level3 as b on a.COA3=b.COA3 
                                                        join dbmaster.coa_level2 as c on b.COA2=c.COA2 WHERE 1=1 ";
                                                    
                                                    if ($fgroupid=="28") {
                                                        $query .=" AND c.DIVISI2 NOT IN ('CHC', 'OTC') ";
                                                    }
                                                    $query .= " ORDER BY a.COA4";
                                                    */
                                                    
                                                    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
                                                        join dbmaster.coa_level3 as b on a.COA3=b.COA3 
                                                        join dbmaster.coa_level2 as c on b.COA2=c.COA2 WHERE 1=1 ";
                                                    
                                                    
                                                    if ($fgroupid=="28" OR $fgroupid=="8") {
                                                        $query .=" AND c.DIVISI2 NOT IN ('CHC', 'OTC') ";
                                                    }elseif ($fgroupid=="38" OR $fgroupid=="48") {
                                                        $query .=" AND c.DIVISI2 IN ('CHC', 'OTC', '', 'OTHER', 'OTHERS') ";
                                                    }else{
                                                        if (!empty($ppilihpm)) {
                                                            $query .=" AND c.DIVISI2 IN ('$ppilihpm', '', 'OTHER', 'OTHERS') ";
                                                        }
                                                    }
                                                    
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
                                        <b>Pilih Report Dari</b> <input type="checkbox" id="chkbtnrptd" value="deselect" onClick="SelAllCheckBoxByNM('chkbtnrptd')" checked/>
                                        <div class="form-group">
                                            
                                            <div id="kotak-multi3" class="jarak">
                                                
                                                <?PHP
                                                if ($_SESSION['IDCARD']=="0000000148") {
                                                    ?>
                                                    &nbsp; <input type=checkbox value='brethical' id='chkbox_rpt1' name='chkbox_rpt1' checked> BR Ethical<br/>
                                                    &nbsp; <input type=checkbox value='klaimdisc' id='chkbox_rpt2' name='chkbox_rpt2' > Klaim Discount<br/>
                                                    &nbsp; <input type=checkbox value='kaskecil' id='chkbox_rpt3' name='chkbox_rpt3' > Kas Kecil<br/>
                                                    &nbsp; <input type=checkbox value='pilkasbno' id='chkbox_rpt16' name='chkbox_rpt16' > Kasbon<br/>
                                                    &nbsp; <input type=checkbox value='brotc' id='chkbox_rpt4' name='chkbox_rpt4' > BR OTC<br/>
                                                    &nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' > Biaya Rutin<br/>
                                                    &nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' > Biaya Luar Kota<br/>
                                                    <!--&nbsp; <input type=checkbox value='ca' id='chkbox_rpt7' name='chkbox_rpt7' > Cash Advance<br/>-->
                                                    &nbsp; <input type=checkbox value='bmsby' id='chkbox_rpt8' name='chkbox_rpt8' > Biaya Marketing SBY<br/>
                                                    &nbsp; <input type=checkbox value='pilbank' id='chkbox_rpt9' name='chkbox_rpt9' > Bank<br/>
                                                    &nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt10' name='chkbox_rpt10' > Incentive Ethical<br/>
                                                    &nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt11' name='chkbox_rpt11' > Sewa Kontrakan Ruman<br/>
                                                    &nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt12' name='chkbox_rpt12' > Service Kendaraan<br/>
                                                    
                                                    &nbsp; <input type=checkbox value='pilkascab' id='chkbox_rpt15' name='chkbox_rpt15' > Kas Kecil Cabang<br/>
                                                    
                                                    
                                                    <?PHP
                                                }elseif ($_SESSION['IDCARD']=="0000000566") {
                                                    echo "&nbsp; <input type=checkbox value='brethical' name='chkbox_rpt1' checked> BR Ethical<br/>";
                                                }elseif ($_SESSION['IDCARD']=="0000001043") {
                                                    echo "&nbsp; <input type=checkbox value='brethical' name='chkbox_rpt1' checked> BR Ethical<br/>";
                                                    echo "&nbsp; <input type=checkbox value='klaimdisc' name='chkbox_rpt2' checked> Klaim Discount<br/>";
                                                }elseif ($_SESSION['IDCARD']=="0000000143") {
                                                    echo "&nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' checked> Biaya Rutin<br/>";
                                                    echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt10' name='chkbox_rpt10' checked> Incentive Ethical<br/>";
                                                    echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt11' name='chkbox_rpt11' checked> Sewa Kontrakan Ruman<br/>";
                                                }elseif ($_SESSION['IDCARD']=="0000000329") {
                                                    echo "&nbsp; <input type=checkbox value='kaskecil' id='chkbox_rpt3' name='chkbox_rpt3' checked> Kas Kecil<br/>";
                                                    echo "&nbsp; <input type=checkbox value='pilkasbno' id='chkbox_rpt16' name='chkbox_rpt16' checked> Kasbon<br/>";
                                                    echo "&nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' checked> Biaya Luar Kota<br/>";
                                                }elseif ($_SESSION['IDCARD']=="0000000144") {
                                                    echo "&nbsp; <input type=checkbox value='pilkascab' id='chkbox_rpt15' name='chkbox_rpt15' checked> Kas Kecil Cabang<br/>";
                                                }else{
                                                    
                                                    if ($fgroupid=="38") {
                                                        echo "&nbsp; <input type=checkbox value='brotc' name='chkbox_rpt4' checked> BR OTC<br/>";
                                                        echo "&nbsp; <input type=checkbox value='klaimdisc' id='chkbox_rpt2' name='chkbox_rpt2' checked> Klaim Discount<br/>";
                                                        echo "&nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' checked> Biaya Rutin<br/>";
                                                        echo "&nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' checked> Biaya Luar Kota<br/>";
                                                        echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt12' name='chkbox_rpt12' checked> Service Kendaraan<br/>";
                                                        echo "&nbsp; <input type=checkbox value='pilkascab' id='chkbox_rpt15' name='chkbox_rpt15' checked> Kas Kecil Cabang<br/>";
                                                    }elseif ($fgroupid=="48") {
                                                        echo "&nbsp; <input type=checkbox value='brotc' name='chkbox_rpt4' checked> BR OTC<br/>";
                                                    }elseif ($_SESSION['DIVISI']=="OTC") {
                                                        if ($_SESSION['IDCARD']=="0000001272") {
                                                            echo "&nbsp; <input type=checkbox value='brotc' name='chkbox_rpt4' checked> BR OTC<br/>";
                                                        }else{
                                                            echo "&nbsp; <input type=checkbox value='brotc' name='chkbox_rpt4' checked> BR OTC<br/>";
                                                            echo "&nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' checked> Biaya Rutin<br/>";
                                                            echo "&nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' checked> Biaya Luar Kota<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt12' name='chkbox_rpt12' checked> Service Kendaraan<br/>";
                                                        }
                                                        echo "&nbsp; <input type=checkbox value='pilkascab' id='chkbox_rpt15' name='chkbox_rpt15' checked> Kas Kecil Cabang<br/>";
                                                    }else{
                                                        if (!empty($ppilihpm) AND $fgroupid=="30") {
                                                            echo "&nbsp; <input type=checkbox value='brethical' id='chkbox_rpt1' name='chkbox_rpt1' checked> BR Ethical<br/>";
                                                            echo "&nbsp; <input type=checkbox value='klaimdisc' id='chkbox_rpt2' name='chkbox_rpt2' checked> Klaim Discount<br/>";
                                                            echo "&nbsp; <input type=checkbox value='kaskecil' id='chkbox_rpt3' name='chkbox_rpt3' > Kas Kecil<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilkasbno' id='chkbox_rpt16' name='chkbox_rpt16' > Kasbon<br/>";
                                                            echo "&nbsp; <input type=checkbox value='brotc' id='chkbox_rpt4' name='chkbox_rpt4' > BR OTC<br/>";
                                                            echo "&nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' > Biaya Rutin<br/>";
                                                            echo "&nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' > Biaya Luar Kota<br/>";
                                                            //echo "&nbsp; <input type=checkbox value='ca' id='chkbox_rpt7' name='chkbox_rpt7' > Cash Advance<br/>";
                                                            echo "&nbsp; <input type=checkbox value='bmsby' id='chkbox_rpt8' name='chkbox_rpt8' > Biaya Marketing SBY<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilbank' id='chkbox_rpt9' name='chkbox_rpt9' > Bank<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt10' name='chkbox_rpt10' > Incentive Ethical<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt11' name='chkbox_rpt11' > Sewa Kontrakan Ruman<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt12' name='chkbox_rpt12' > Service Kendaraan<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilkascab' id='chkbox_rpt15' name='chkbox_rpt15' > Kas Kecil Cabang<br/>";
                                                        }elseif ($fgroupid=="8") {
                                                            echo "&nbsp; <input type=checkbox value='brethical' id='chkbox_rpt1' name='chkbox_rpt1' checked> BR Ethical<br/>";
                                                            echo "&nbsp; <input type=checkbox value='klaimdisc' id='chkbox_rpt2' name='chkbox_rpt2' checked> Klaim Discount<br/>";
                                                            echo "&nbsp; <input type=checkbox value='kaskecil' id='chkbox_rpt3' name='chkbox_rpt3' checked> Kas Kecil<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilkasbno' id='chkbox_rpt16' name='chkbox_rpt16' checked> Kasbon<br/>";
                                                            //echo "&nbsp; <input type=checkbox value='brotc' id='chkbox_rpt4' name='chkbox_rpt4' checked> BR OTC<br/>";
                                                            echo "&nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' checked> Biaya Rutin<br/>";
                                                            echo "&nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' checked> Biaya Luar Kota<br/>";
                                                            //echo "&nbsp; <input type=checkbox value='ca' id='chkbox_rpt7' name='chkbox_rpt7' checked> Cash Advance<br/>";
                                                            echo "&nbsp; <input type=checkbox value='bmsby' id='chkbox_rpt8' name='chkbox_rpt8' checked> Biaya Marketing SBY<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilbank' id='chkbox_rpt9' name='chkbox_rpt9' checked> Bank<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt10' name='chkbox_rpt10' checked> Incentive Ethical<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt11' name='chkbox_rpt11' checked> Sewa Kontrakan Ruman<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt12' name='chkbox_rpt12' checked> Service Kendaraan<br/>";
                                                            echo "&nbsp; <input type=checkbox value='pilkascab' id='chkbox_rpt15' name='chkbox_rpt15' checked> Kas Kecil Cabang<br/>";
                                                        }else{
                                                        
                                                ?>
                                                                &nbsp; <input type=checkbox value='brethical' id='chkbox_rpt1' name='chkbox_rpt1' checked> BR Ethical<br/>
                                                                &nbsp; <input type=checkbox value='klaimdisc' id='chkbox_rpt2' name='chkbox_rpt2' checked> Klaim Discount<br/>
                                                                &nbsp; <input type=checkbox value='kaskecil' id='chkbox_rpt3' name='chkbox_rpt3' checked> Kas Kecil & Kasbon<br/>
                                                                &nbsp; <input type=checkbox value='brotc' id='chkbox_rpt4' name='chkbox_rpt4' checked> BR OTC<br/>
                                                                &nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' checked> Biaya Rutin<br/>
                                                                &nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' checked> Biaya Luar Kota<br/>
                                                                <!--&nbsp; <input type=checkbox value='ca' id='chkbox_rpt7' name='chkbox_rpt7' checked> Cash Advance<br/>-->
                                                                &nbsp; <input type=checkbox value='bmsby' id='chkbox_rpt8' name='chkbox_rpt8' checked> Biaya Marketing SBY<br/>
                                                                &nbsp; <input type=checkbox value='pilbank' id='chkbox_rpt9' name='chkbox_rpt9' checked> Bank<br/>
                                                                &nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt10' name='chkbox_rpt10' checked> Incentive Ethical<br/>
                                                                &nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt11' name='chkbox_rpt11' checked> Sewa Kontrakan Ruman<br/>
                                                                &nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt12' name='chkbox_rpt12' checked> Service Kendaraan<br/>

                                                                &nbsp; <input type=checkbox value='pilkascab' id='chkbox_rpt15' name='chkbox_rpt15' checked> Kas Kecil Cabang<br/>
                                                
                                                <?PHP
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Report Type <span class='required'></span></label>
                                    <div class='col-md-6'>
                                        <input type="radio" id="radio1" name="radio1" value="S"> Sesuai SPD
                                        <input type="radio" id="radio1" name="radio1" value="A" checked> All
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
    
    
    function SelAllCheckBoxByNM(nmbuton){
        var button = document.getElementById(nmbuton);
        
        if(button.value == 'select'){
            document.getElementById('chkbox_rpt1').checked = 'FALSE';
            document.getElementById('chkbox_rpt2').checked = 'FALSE';
            document.getElementById('chkbox_rpt3').checked = 'FALSE';
            document.getElementById('chkbox_rpt4').checked = 'FALSE';
            document.getElementById('chkbox_rpt5').checked = 'FALSE';
            document.getElementById('chkbox_rpt6').checked = 'FALSE';
            //document.getElementById('chkbox_rpt7').checked = 'FALSE'; //CA
            document.getElementById('chkbox_rpt8').checked = 'FALSE';
            document.getElementById('chkbox_rpt9').checked = 'FALSE';
            document.getElementById('chkbox_rpt10').checked = 'FALSE';
            document.getElementById('chkbox_rpt11').checked = 'FALSE';
            document.getElementById('chkbox_rpt12').checked = 'FALSE';
            document.getElementById('chkbox_rpt15').checked = 'FALSE';
            button.value = 'deselect'
        }else{
            document.getElementById('chkbox_rpt1').checked = '';
            document.getElementById('chkbox_rpt2').checked = '';
            document.getElementById('chkbox_rpt3').checked = '';
            document.getElementById('chkbox_rpt4').checked = '';
            document.getElementById('chkbox_rpt5').checked = '';
            document.getElementById('chkbox_rpt6').checked = '';
            //document.getElementById('chkbox_rpt7').checked = ''; //CA
            document.getElementById('chkbox_rpt8').checked = '';
            document.getElementById('chkbox_rpt9').checked = '';
            document.getElementById('chkbox_rpt10').checked = '';
            document.getElementById('chkbox_rpt11').checked = '';
            document.getElementById('chkbox_rpt12').checked = '';
            document.getElementById('chkbox_rpt15').checked = '';
            button.value = 'select';
        }
    }
    
    
    function ShowCOA(udiv, ucoa) {
        var icar = "";
        var idiv = document.getElementById(udiv).value;
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_generalledger/viewdata.php?module=viewcoadivisichk",
            data:"umr="+icar+"&udivi="+idiv,
            success:function(data){
                $("#"+ucoa).html(data);
            }
        });
    }
</script>