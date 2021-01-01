<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>General Ledger</h3></div></div><div class="clearfix"></div>
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
                                            <input type='text' id='e_tgl2' name='e_tgl2' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
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
                                            <select class='form-control' id="divprodid" name="divprodid" onchange="ShowCOA('divprodid', 'kotak-multi2');">
                                                <?PHP
                                                if ($_SESSION['IDCARD']=="0000000148") $divisi = "HO";
                                                elseif ($_SESSION['IDCARD']=="0000001043") $divisi = "EAGLE";
                                                else{
                                                    if ($_SESSION['DIVISI']=="OTC") $divisi = "OTC";
                                                }
                                                
                                                $query = "select DivProdId from MKT.divprod WHERE br='Y' AND DivProdId<>'OTHER' ";
                                                $query .=" order by DivProdId";
                                                $tampil = mysqli_query($cnmy, $query);
                                                echo "<option value='' selected>All</option>";
                                                while ($z= mysqli_fetch_array($tampil)) {
                                                    $pdivisi=$z['DivProdId'];
                                                    if ($pdivisi=="CAN") $pdivisi="CANARY";
                                                    if ($z['DivProdId']==$divisi)
                                                        echo "<option value='$z[DivProdId]' selected>$pdivisi</option>";
                                                    else
                                                        echo "<option value='$z[DivProdId]'>$pdivisi</option>";
                                                }
                                                ?>
                                            </select>
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
                                                    $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a ";
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
                                                    &nbsp; <input type=checkbox value='kaskecil' id='chkbox_rpt3' name='chkbox_rpt3' > Kas Kecil & Kasbon<br/>
                                                    &nbsp; <input type=checkbox value='brotc' id='chkbox_rpt4' name='chkbox_rpt4' > BR OTC<br/>
                                                    &nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' > Biaya Rutin<br/>
                                                    &nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' > Biaya Luar Kota<br/>
                                                    <!--&nbsp; <input type=checkbox value='ca' id='chkbox_rpt7' name='chkbox_rpt7' > Cash Advance<br/>-->
                                                    &nbsp; <input type=checkbox value='bmsby' id='chkbox_rpt8' name='chkbox_rpt8' > Biaya Marketing SBY<br/>
                                                    &nbsp; <input type=checkbox value='pilbank' id='chkbox_rpt9' name='chkbox_rpt9' > Bank<br/>
                                                    &nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt10' name='chkbox_rpt10' > Incentive Ethical<br/>
                                                    &nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt11' name='chkbox_rpt11' checked> Sewa Kontrakan Ruman<br/>
                                                    &nbsp; <input type=checkbox value='pilinc' id='chkbox_rpt12' name='chkbox_rpt12' checked> Service Kendaraan<br/>
                                                    <?PHP
                                                }elseif ($_SESSION['IDCARD']=="0000000566") {
                                                    echo "&nbsp; <input type=checkbox value='brethical' name='chkbox_rpt1' checked> BR Ethical<br/>";
                                                }elseif ($_SESSION['IDCARD']=="0000001043") {
                                                    echo "&nbsp; <input type=checkbox value='brethical' name='chkbox_rpt1' checked> BR Ethical<br/>";
                                                    echo "&nbsp; <input type=checkbox value='klaimdisc' name='chkbox_rpt2' checked> Klaim Discount<br/>";
                                                }elseif ($_SESSION['IDCARD']=="0000000143") {
                                                    echo "&nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' checked> Biaya Rutin<br/>";
                                                }elseif ($_SESSION['IDCARD']=="0000000329") {
                                                    echo "&nbsp; <input type=checkbox value='kaskecil' id='chkbox_rpt3' name='chkbox_rpt3' checked> Kas Kecil & Kasbon<br/>";
                                                    echo "&nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' checked> Biaya Luar Kota<br/>";
                                                }else{
                                                    if ($_SESSION['DIVISI']=="OTC") {
                                                        if ($_SESSION['IDCARD']=="0000001272") {
                                                            echo "&nbsp; <input type=checkbox value='brotc' name='chkbox_rpt4' checked> BR OTC<br/>";
                                                        }else{
                                                            echo "&nbsp; <input type=checkbox value='brotc' name='chkbox_rpt4' checked> BR OTC<br/>";
                                                            echo "&nbsp; <input type=checkbox value='rutin' id='chkbox_rpt5' name='chkbox_rpt5' checked> Biaya Rutin<br/>";
                                                            echo "&nbsp; <input type=checkbox value='blk' id='chkbox_rpt6' name='chkbox_rpt6' checked> Biaya Luar Kota<br/>";
                                                        }
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
                                                
                                                <?PHP
                                                    }
                                                }
                                                ?>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <u><b>Report Type</b></u>
                                        <div class="form-group">
                                            <input type="radio" id="radio1" name="radio1" value="D" checked> Detail &nbsp; 
                                            <input type="radio" id="radio1" name="radio1" value="S"> Summary
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