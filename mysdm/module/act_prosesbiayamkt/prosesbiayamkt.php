<?PHP
    $fkaryawan=$_SESSION['IDCARD'];
    $fstsadmin=$_SESSION['STSADMIN'];
    $flvlposisi=$_SESSION['LVLPOSISI'];
    $fdivisi=$_SESSION['DIVISI'];
    
    $aksi="eksekusi3.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('Y', strtotime($hari_ini));
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Proses Data Transaski Budget (Biaya Marketing)</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php

        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=home"; ?>">Home</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>

                    <!--kiri-->
                    <div class='col-md-12 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'><br />
                                
                                
                                <div class='col-sm-3'>
                                    <b>Tahun</b>
                                    <div class="form-group">
                                        <div class='input-group date' id='thn01'>
                                            <input type='text' id='e_tgl1' name='e_tgl1' required='required' class='form-control' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                            <span class="input-group-addon">
                                               <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <div class='col-sm-12'>
                                        <b>Pilih Report Dari</b> <input type="checkbox" id="chkbtnrptd" value="deselect" onClick="SelAllCheckBoxByNM('chkbtnrptd')" checked/>
                                        <div class="form-group">
                                            <div id="kotak-multi3" class="jarak">
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
                                                
                                                &nbsp; <input type=checkbox value='pilslseth' id='chkbox_rpt13' name='chkbox_rpt13' checked> Sales Ethical<br/>
                                                &nbsp; <input type=checkbox value='pilslsotc' id='chkbox_rpt14' name='chkbox_rpt14' checked> Sales OTC<br/>
                                                
                                                
                                                &nbsp; <input type=checkbox value='pilsharebg' id='chkbox_rpt100' name='chkbox_rpt100' > Shared Budget HO<br/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                


                            </div>
                        </div>           
                    </div>
                    
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <button type='button' class='btn btn-dark' onclick="disp_confirm('')">Proses</button>
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
    function disp_confirm(pText)  {
        
        var cmt = confirm('Apakah akan melakukan proses data...???');
        if (cmt == false) {
            return false;
        }else{
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
            document.getElementById('chkbox_rpt13').checked = 'FALSE';
            document.getElementById('chkbox_rpt14').checked = 'FALSE';
            document.getElementById('chkbox_rpt15').checked = 'FALSE';
            
            
            document.getElementById('chkbox_rpt100').checked = 'FALSE';
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
            document.getElementById('chkbox_rpt13').checked = '';
            document.getElementById('chkbox_rpt14').checked = '';
            document.getElementById('chkbox_rpt15').checked = '';
            
            
            document.getElementById('chkbox_rpt100').checked = '';
            button.value = 'select';
        }
    }
    
</script>