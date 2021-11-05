<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    $fgroupid=$_SESSION['GROUP'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('Y', strtotime($hari_ini));
    
    $pchk1=""; $pchk2=""; $pchk3=""; $pchk4=""; $pchk5="";
    $pchk6=""; $pchk7=""; $pchk8=""; $pchk9=""; $pchk10="";
    $pchk11=""; $pchk12=""; $pchk13=""; $pchk14=""; $pchk15=""; 
    $pchk16="";
    
    $ppilihdivisi="";
    if ($fkaryawan=="0000000148") $ppilihdivisi = "HO";
    elseif ($fkaryawan=="0000001043xx") $ppilihdivisi = "EAGLE";
    else{
        if ($fdivisi=="OTC" OR $fdivisi=="CHC") {
            $ppilihdivisi="OTC";
        }
    }
    
    $pbukasemua=false;
    $pbukdivisiall=false;
    $pnot_otc=false;
    if ($fgroupid=="1" OR $fgroupid=="25" OR $fgroupid=="24" OR $fgroupid=="2" OR $fgroupid=="22" OR $fgroupid=="46" OR $fgroupid=="50") {
        $pbukasemua=true;
        $pbukdivisiall=true;
        
        if ($fgroupid == "25") {
            $pchk1="checked";
        }else{
            $pchk1="checked"; $pchk2="checked"; $pchk3="checked"; $pchk4="checked"; $pchk5="checked";
            $pchk6="checked"; $pchk7="checked"; $pchk8="checked"; $pchk9="checked"; $pchk10="checked";
            $pchk11="checked"; $pchk12="checked"; $pchk13="checked"; $pchk14="checked"; $pchk15="checked";  
            $pchk16="";//checked
        }
        
    }
    
    
    if (empty($ppilihdivisi)) {
        $pbukdivisiall=true;
    }
    
    if ($fgroupid=="28") {
        $pnot_otc=true;
    }
    
    if ($fkaryawan=="0000001043") $pbukdivisiall=true;
    
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Realisasi Biaya Marketing By Finance</h3></div></div><div class="clearfix"></div>
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
                                        <b>Report Type</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_rpttype" name="cb_rpttype" onchange="ShowDataDivisiTipeRpt()">
                                                <?PHP
                                                echo "<option value='COA' selected>COA</option>";
                                                echo "<option value='DIV'>Divisi Akun</option>";
                                                echo "<option value='BMB'>Sub Posting Transaksi</option>";
                                                //echo "<option value='RAW'>Raw Data</option>";
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Divisi</b>
                                        <div class="form-group">
                                            <select class='form-control' id="cb_divisip" name="cb_divisip" onchange="">
                                                <?PHP
                                                $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId<>'OTHER' ";
                                                if ($pbukdivisiall==false) {
                                                    $query .=" AND DivProdId='$ppilihdivisi' ";
                                                }
                                                
                                                if ($pnot_otc == true) {
                                                    $query .=" AND DivProdId NOT IN ('CHC', 'OTC') ";
                                                }
                                                $query .=" order by DivProdId";
                                                $tampil = mysqli_query($cnmy, $query);
                                                if ($pbukdivisiall==true) echo "<option value='' selected>All</option>";
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pgetdivisi=$z['DivProdId'];
                                                    
                                                    $pdivisinm=$pgetdivisi;
                                                    if ($pgetdivisi=="CAN") $pdivisinm="CANARY";
                                                    if ($pgetdivisi=="PIGEO") $pdivisinm="PIGEON";
                                                    if ($pgetdivisi=="PEACO") $pdivisinm="PEACOCK";
                                                    if ($pgetdivisi=="OTC") $pdivisinm="CHC";
                                                    
                                                    if ($pgetdivisi==$ppilihdivisi)
                                                        echo "<option value='$pgetdivisi' selected>$pdivisinm</option>";
                                                    else
                                                        echo "<option value='$pgetdivisi'>$pdivisinm</option>";
                                                }
                                                
                                                if ($fgroupid=="1" OR $fgroupid=="24" OR $fgroupid=="61" OR $fgroupid=="28" OR $fgroupid=="25") {
                                                    if ($fkaryawan=="0000000143" OR $fkaryawan=="0000000329")
                                                        echo "<option value='ETH' selected>ETHICAL</option>";
                                                    else
                                                        echo "<option value='ETH'>ETHICAL</option>";
                                                }
                                                
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>COA</b>
                                        <div class="form-group">
                                            <select class='form-control s2' id="cb_coa" name="cb_coa[]" onchange="" multiple="multiple">
                                                <?PHP
                                                $query = "select COA4, NAMA4 from dbmaster.coa_level4 ";
                                                $query .=" order by COA4";
                                                $tampil = mysqli_query($cnmy, $query);
                                                //echo "<option value='' selected>All</option>";
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pcoaid=$z['COA4'];
                                                    $pcoanm=$z['NAMA4'];
                                                    
                                                    echo "<option value='$pcoaid'>$pcoaid - $pcoanm</option>";
                                                }
                                                
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Pilih Report Dari</b> 
                                        <?PHP
                                        if ($pbukasemua==false) {
                                            echo "<input type='checkbox' id='chkbtnrptd' value='deselect' onClick=\"SelAllCheckBoxByNM('chkbtnrptd')\" checked/>";
                                        }else{
                                            echo "<input type='checkbox' id='chkbtnrptd' value='select' onClick=\"SelAllCheckBoxByNM('chkbtnrptd')\" />";
                                        }
                                        ?>
                                        <div class="form-group">
                                            <div id="kotak-multi3" class="jarak">
                                                <?PHP
                                                if ($pbukasemua==false) {
                                                    if ($fdivisi=="OTC" OR $fdivisi=="CHC") {//ipul dan desi
                                                        echo "&nbsp; <input type=checkbox value='brotc' id='chkbox_rpt4' name='chkbox_rpt4' checked> BR OTC<br/>";
                                                        if ($fgroupid == "26") {
                                                            echo "&nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' checked> Biaya Rutin<br/>";
                                                            echo "&nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' checked> Biaya Luar Kota<br/>";
                                                        }
                                                        echo "&nbsp; <input type=checkbox value='pilkascab' id='chkbox_rpt15' name='chkbox_rpt15' checked> Kas Kecil Cabang<br/>";
                                                    }else{
                                                        if ($fgroupid=="28" OR $fgroupid=="61") {//marsis dan ria
                                                            if ($fkaryawan=="0000000329") {//marsis
                                                                echo "&nbsp; <input type=checkbox value='kaskecil' id='chkbox_rpt3' name='chkbox_rpt3' checked> Kas Kecil<br/>";
                                                                echo "&nbsp; <input type=checkbox value='kaskecil' id='chkbox_rpt16' name='chkbox_rpt16' > Kasbon<br/>";
                                                                echo "&nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' checked> Biaya Luar Kota<br/>";
                                                            }elseif ($fkaryawan=="0000000143") {//ria
                                                                echo "&nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' checked> Biaya Rutin<br/>";
                                                                echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt10' name='chkbox_rpt10' checked> Incentive Ethical<br/>";
                                                                echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt11' name='chkbox_rpt11' checked> Sewa Kontrakan Ruman<br/>";
                                                            }
                                                        }else{
                                                            if ($fkaryawan=="0000000566") {//erni
                                                                echo "&nbsp; <input type=checkbox value='brethical' id='chkbox_rpt1' name='chkbox_rpt1' checked> BR Ethical<br/>";
                                                            }elseif ($fkaryawan=="0000001043") {//prita
                                                                echo "&nbsp; <input type=checkbox value='brethical' id='chkbox_rpt1' name='chkbox_rpt1' checked> BR Ethical<br/>";
                                                                echo "&nbsp; <input type=checkbox value='klaimdisc' id='chkbox_rpt2' name='chkbox_rpt2' checked> Klaim Discount<br/>";
                                                            }elseif ($fkaryawan=="0000000144") {//titik
                                                                echo "&nbsp; <input type=checkbox value='klaimdisc' id='chkbox_rpt2' name='chkbox_rpt2' checked> Klaim Discount<br/>";
                                                                echo "&nbsp; <input type=checkbox value='pilkascab' id='chkbox_rpt15' name='chkbox_rpt15' checked> Kas Kecil Cabang<br/>";
                                                            }elseif ($fkaryawan=="0000000266") {//ahmed ahmad saihu
                                                                echo "&nbsp; <input type=checkbox value='klaimdisc' id='chkbox_rpt2' name='chkbox_rpt2' checked> Klaim Discount<br/>";
                                                            }
                                                        }
                                                    }
                                                }else{// ane, sami dan ...
                                                    echo "&nbsp; <input type=checkbox value='brethical' id='chkbox_rpt1' name='chkbox_rpt1' $pchk1> BR Ethical<br/>";
                                                    echo "&nbsp; <input type=checkbox value='klaimdisc' id='chkbox_rpt2' name='chkbox_rpt2' $pchk2> Klaim Discount<br/>";
                                                    echo "&nbsp; <input type=checkbox value='kaskecil' id='chkbox_rpt3' name='chkbox_rpt3' $pchk3> Kas Kecil<br/>";
                                                    echo "&nbsp; <input type=checkbox value='kaskecil' id='chkbox_rpt16' name='chkbox_rpt16' $pchk16> Kasbon<br/>";
                                                    echo "&nbsp; <input type=checkbox value='brotc' id='chkbox_rpt4' name='chkbox_rpt4' $pchk4> BR OTC<br/>";
                                                    echo "&nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' $pchk5> Biaya Rutin<br/>";
                                                    echo "&nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' $pchk6> Biaya Luar Kota<br/>";
                                                    //echo "&nbsp; <input type=checkbox value='ca' id='chkbox_rpt7' name='chkbox_rpt7' $pchk7> Cash Advance<br/>";
                                                    
                                                    echo "&nbsp; <input type=checkbox value='bmsby' id='chkbox_rpt8' name='chkbox_rpt8' $pchk8> Biaya Marketing SBY<br/>";
                                                    echo "&nbsp; <input type=checkbox value='pilbank' id='chkbox_rpt9' name='chkbox_rpt9' $pchk9> Bank<br/>";
                                                    echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt10' name='chkbox_rpt10' $pchk10> Incentive Ethical<br/>";
                                                    echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt11' name='chkbox_rpt11' $pchk11> Sewa Kontrakan Ruman<br/>";
                                                    echo "&nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt12' name='chkbox_rpt12' $pchk12> Service Kendaraan<br/>";
                                                    echo "&nbsp; <input type=checkbox value='pilkascab' id='chkbox_rpt15' name='chkbox_rpt15' $pchk15> Kas Kecil Cabang<br/>";
                                                    
                                                    
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

    $('#cbln01').on('change dp.change', function(e){
        
        var idate1=document.getElementById('e_tgl1').value;
        var idate2=document.getElementById('e_tgl2').value;
        var ndate1 = new Date(idate1+" 01");
        var ndate2 = new Date(idate2+" 01");
        
        
        var month = new Array();
        month[0] = "January";
        month[1] = "February";
        month[2] = "March";
        month[3] = "April";
        month[4] = "May";
        month[5] = "June";
        month[6] = "July";
        month[7] = "August";
        month[8] = "September";
        month[9] = "October";
        month[10] = "November";
        month[11] = "December";
        
        var nbln1 = ndate1.getMonth();
        var nbulan1 = month[ndate1.getMonth()];
        var ntahun1 = ndate1.getFullYear();
        
        var nbln2 = ndate2.getMonth();
        var nbulan2 = month[ndate2.getMonth()];
        var ntahun2 = ndate2.getFullYear();
        
        if (nbln1=="NaN" || nbln2=="NaN") return false;
        
        if (parseInt(nbln1)>parseInt(nbln2)) {
            document.getElementById('e_tgl2').value=document.getElementById('e_tgl1').value;
        }else{
        
            if (ntahun1==ntahun2){
            }else{
                document.getElementById('e_tgl2').value=nbulan2+" "+ntahun1;
            }
            
        }
        //alert(nbulan1+" "+ntahun1+" - "+nbulan2+" "+ntahun2);
    });
    
    $('#cbln02').on('change dp.change', function(e){
        var idate1=document.getElementById('e_tgl1').value;
        var idate2=document.getElementById('e_tgl2').value;
        var ndate1 = new Date(idate1+" 01");
        var ndate2 = new Date(idate2+" 01");
        
        
        var month = new Array();
        month[0] = "January";
        month[1] = "February";
        month[2] = "March";
        month[3] = "April";
        month[4] = "May";
        month[5] = "June";
        month[6] = "July";
        month[7] = "August";
        month[8] = "September";
        month[9] = "October";
        month[10] = "November";
        month[11] = "December";
        
        var nbln1 = ndate1.getMonth();
        var nbulan1 = month[ndate1.getMonth()];
        var ntahun1 = ndate1.getFullYear();
        
        var nbln2 = ndate2.getMonth();
        var nbulan2 = month[ndate2.getMonth()];
        var ntahun2 = ndate2.getFullYear();
        //alert(nbln1+" "+nbln2);
        if (nbln1=="NaN" || nbln2=="NaN") return false;
        
        if (parseInt(nbln1)>parseInt(nbln2)) {
            document.getElementById('e_tgl1').value=document.getElementById('e_tgl2').value;
        }else{
        
            if (ntahun1==ntahun2){
            }else{
                document.getElementById('e_tgl1').value=nbulan1+" "+ntahun2;
            }
            
        }
        
        
    });
    
    
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
            //document.getElementById('chkbox_rpt16').checked = 'FALSE';
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
            document.getElementById('chkbox_rpt16').checked = '';
            button.value = 'select';
        }
    }
    
    function ShowCOA(udiv, ucoa) {
        var icar = "";
        var idiv = document.getElementById(udiv).value;
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_rbmfin/viewdata.php?module=viewcoadivisichk",
            data:"umr="+icar+"&udivi="+idiv,
            success:function(data){
                $("#"+ucoa).html(data);
            }
        });
    }
    
    function ShowDataDivisiTipeRpt() {
        var icar = "";
        var itipe = document.getElementById('cb_rpttype').value;
        $.ajax({
            type:"post",
            url:"module/laporan_gl/mod_gl_rbmfin/viewdata.php?module=viewdivisibytipe",
            data:"umr="+icar+"&utipe="+itipe,
            success:function(data){
                $("#cb_divisip").html(data);
            }
        });
    }
</script>

<!--<script src="vendors/jquery/dist/jquery.min.js"></script>-->
<link href="module/dkd/select2.min.css" rel="stylesheet" type="text/css" />
<script src="module/dkd/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.s2, .s3').select2();
    });
</script>