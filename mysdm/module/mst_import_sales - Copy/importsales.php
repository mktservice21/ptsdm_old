<?PHP
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    if (!empty($_SESSION['MSTIMPPERTPIL'])) $tgl_pertama=$_SESSION['MSTIMPPERTPIL'];
    
    //$_SESSION['MSTIMPFILEPIL']="";
    
    
    $pdistiidpil="";
    $pfilename="";
    
    if (!empty($_SESSION['MSTIMPDISTPIL'])) $pdistiidpil=$_SESSION['MSTIMPDISTPIL'];
    if (!empty($_SESSION['MSTIMPFOLDPIL'])) $pfilename=$_SESSION['MSTIMPFOLDPIL'];
    
    $nact="";
    if (isset($_GET['act'])) $nact=$_GET['act'];
    
    $pjudul="Import Data Sales";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli_ms.php";
        $aksi="module/mst_import_sales/aksi_importsales.php";
        switch($_GET['act']){
            default:
                ?>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            
                            <div class='col-sm-2'>
                                Periode
                                <div class="form-group">
                                    <div class='input-group date' id='cbln01x'>
                                        <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                        <span class="input-group-addon">
                                           <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class='col-sm-2'>
                                Distiributor
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_distid" name="cb_distid" onchange="ShowData()">
                                        <?PHP
                                        $pinsel="('0000000002', '0000000003', '0000000005', '0000000006', '0000000010', "
                                                . "'0000000011', '0000000016', '0000000023', '0000000030', '0000000031', '0000000021', "
                                                . " '0000000028', '0000000015', '0000000018', '0000000025', '0000000033')";
                                        
                                        $pinsel="('0000000002', '0000000003', '0000000005', '0000000010', '0000000011', "
                                                . " '0000000021', '0000000031', '0000000006', '0000000016', '0000000030', "
                                                . " '0000000028', '0000000015', '0000000018', '0000000025', '0000000033')";
                                        cComboDistibutorHanya('', $pdistiidpil, $pinsel);
                                        ?>
                                    </select>
                                    <input type="hidden" id="txtpilfoder" name="txtpilfoder" value="<?PHP echo $pfilename; ?>">
                                </div>
                            </div>
                        
                        
                        
                            <div id='div_distributor'>


                            </div>
                        </form>
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                           
                        </div>
                        
                    </div>
                </div>

        
                <script>

                    $(document).ready(function() {
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        if (eiddist=="") {
                        }else{
                            ShowData();
                            
                            if (epilfolder=="") {
                                return false;
                            }
                            
                            if (eiddist=="0000000002" || eiddist=="2") {
                                
                            } else if (eiddist=="0000000003" || eiddist=="3") {
                                AMSCekImportData();
                                /*
                                if (epilfolder=="") {
                                }else{
                                    AMSCekImportData();
                                }
                                */
                            } else if (eiddist=="0000000021" || eiddist=="21") {
                                AKFCekImportData();
                            } else if (eiddist=="0000000015" || eiddist=="15") {
                                EPCekImportData();
                            } else if (eiddist=="0000000018" || eiddist=="18") {
                                if (epilfolder=="") {
                                }else{
                                    GMPCekImportData();
                                }
                            } else if (eiddist=="0000000033" || eiddist=="33") {
                                BCMCekImportData();
                            } else if (eiddist=="0000000028" || eiddist=="28") {
                                BKSCekImportData();
                            } else if (eiddist=="0000000025" || eiddist=="25") {
                                MPSCekImportData();
                            } else if (eiddist=="0000000005" || eiddist=="5") {
                                PVCekImportData();
                            } else if (eiddist=="0000000030" || eiddist=="30") {
                                CPPCekImportData();
                            } else if (eiddist=="0000000010" || eiddist=="10") {
                                SSTCekImportData();
                            } else if (eiddist=="0000000010" || eiddist=="10") {
                                SSTCekImportData();
                            } else if (eiddist=="0000000011" || eiddist=="11") {
                                CPCekImportData();
                            } else if (eiddist=="0000000031" || eiddist=="31") {
                                SKSCekImportData();
                            } else if (eiddist=="0000000006" || eiddist=="6") {
                                CPMCekImportData();
                            } else if (eiddist=="0000000016" || eiddist=="16") {
                                MASCekImportData();
                            }
                        }
                        
                    } );
                    
                    function UploadDataKeServer() {
                        
                        var eiddist = document.getElementById("cb_distid").value;
                        if (eiddist=="") {
                            alert("Distiributor Harus diisi...!!!"); return false;
                        }
                        
                        pText_="Data akan diupload ke server...?";
                        
                        if (eiddist=="0000000003" || eiddist=="3" || eiddist=="0000000021" 
                                || eiddist=="21" || eiddist=="0000000015" || eiddist=="15" 
                                || eiddist=="0000000018" || eiddist=="18" 
                                || eiddist=="0000000033" || eiddist=="33" 
                                || eiddist=="0000000025" || eiddist=="25" 
                                || eiddist=="0000000030" || eiddist=="30" 
                                || eiddist=="0000000010" || eiddist=="10" 
                                || eiddist=="0000000011" || eiddist=="11" 
                                || eiddist=="0000000031" || eiddist=="31" 
                                || eiddist=="0000000006" || eiddist=="6" 
                                || eiddist=="0000000016" || eiddist=="16" 
                                ) {
                            pText_="Pastikan File yang dipilih sesuai...?";
                        } else if (eiddist=="0000000005" || eiddist=="5") {
                            
                        }
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                //document.write("You pressed OK!")
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                document.getElementById("demo-form2").action = "module/mst_import_sales/aksi_uploaddata.php?module="+module+"&act=upload"+"&idmenu="+idmenu;
                                document.getElementById("demo-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
                    }
                    
                    function UploadDataSPP() {
                        
                        var eiddist = document.getElementById("cb_distid").value;
                        if (eiddist=="") {
                            alert("Distiributor Harus diisi...!!!"); return false;
                        }
                        
                        pText_="Data akan diupload ke server...?";
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                //document.write("You pressed OK!")
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                document.getElementById("demo-form2").action = "module/mst_import_sales/aksi_uploaddata.php?module="+module+"&act=upload"+"&idmenu="+idmenu;
                                document.getElementById("demo-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
                    }
                    
                    function Kosongkan(){
                        $("#c-data").html("");
                    }
                    
                    function ShowData() {
                        $("#div_distributor").html("");
                        var eiddist = document.getElementById("cb_distid").value;
                        
                        if (eiddist=="0000000002" || eiddist=="2") {
                            CariDataDIV('SPP');
                        } else if (eiddist=="0000000003" || eiddist=="3" 
                                || eiddist=="0000000005" || eiddist=="5"
                                || eiddist=="0000000030" || eiddist=="30"
                                ) {
                            CariDataDIV('AMS');
                        } else if (eiddist=="0000000021" || eiddist=="21" || eiddist=="0000000015" || eiddist=="15" 
                                 || eiddist=="0000000018" || eiddist=="18" 
                                 || eiddist=="0000000033" || eiddist=="33"  
                                 ) {
                            CariDataDIV('AKF');
                        } else if (eiddist=="0000000028" || eiddist=="28" 
                                || eiddist=="0000000010" || eiddist=="10" 
                                || eiddist=="0000000006" || eiddist=="6" 
                                || eiddist=="0000000016" || eiddist=="16" 
                                ) {
                            CariDataDIV('BKS');
                        } else if (eiddist=="0000000025" || eiddist=="25" 
                                || eiddist=="0000000011" || eiddist=="11" 
                                || eiddist=="0000000031" || eiddist=="31" 
                                ) {
                            CariDataDIV('MPS');
                        }
                        Kosongkan();
                    }
                    
                    function CariDataDIV(idistpilih) {
                        var epildistnya = document.getElementById("cb_distid").value;
                        var ebln = document.getElementById("tgl1").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;
                        if (idistpilih=="SPP") {
                            var nmodule="sppviewdata";
                            if (epilfolder=="") {
                                //nmodule="sppcaridatafolder";
                            }
                        }else if (idistpilih=="AMS") {
                            var nmodule="amsviewdata";
                        }else if (idistpilih=="AKF") {
                            var nmodule="akfviewdata";
                        }else if (idistpilih=="BKS") {
                            var nmodule="bksviewdata";
                        }else if (idistpilih=="MPS") {
                            var nmodule="mpsviewdata";
                        }else if (idistpilih=="EP") {
                            var nmodule="epviewdata";
                        }else if (idistpilih=="GMP") {
                            var nmodule="gmpviewdata";
                        }else if (idistpilih=="MPS") {
                            var nmodule="mpsviewdata";
                        }else if (idistpilih=="BCM") {
                            var nmodule="bcmviewdata";
                        }else if (idistpilih=="PV") {
                            var nmodule="pvviewdata";
                        }else if (idistpilih=="CPP") {
                            var nmodule="cppviewdata";
                        }else if (idistpilih=="SST") {
                            var nmodule="sstviewdata";
                        }else if (idistpilih=="CP") {
                            var nmodule="cpviewdata";
                        }else if (idistpilih=="CPM") {
                            var nmodule="cpmviewdata";
                        }else if (idistpilih=="MAS") {
                            var nmodule="masviewdata";
                        }else if (idistpilih=="DUM") {
                            var nmodule="dumviewdata";
                        }else if (idistpilih=="SKS") {
                            var nmodule="sksviewdata";
                        }
                        
                        $.ajax({
                            type:"post",
                            url:"module/mst_import_sales/viewdata.php?module="+nmodule,
                            data:"udistpilih="+idistpilih+"&ubln="+ebln+"&upilfolder="+epilfolder+"&upildistnya="+epildistnya,
                            success:function(data){
                                $("#div_distributor").html(data);
                            }
                        });
                    }
                    
                    function CekImportDataSPP() {
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfile = document.getElementById("cb_pilihfile").value;
                        if (eiddist=="") {
                            alert("Distiributor Harus diisi...!!!"); return false;
                        }
                        
                        if (epilfile=="") {
                            alert("Tidak ada File yang dipilih...!!!"); return false;
                        }
                        
                        //document.write("You pressed OK!")
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");

                        document.getElementById("demo-form2").action = "module/mst_import_sales/spp_cekimport.php?module="+module+"&act=upload"+"&idmenu="+idmenu;
                        document.getElementById("demo-form2").submit();
                        return 1;
                            
                    }
                    
                   
                    function SPPCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;
                        var epilfile = document.getElementById("cb_pilihfile").value;
                        
                        if (eiddist=="") {
                            alert("Distiributor Harus diisi...!!!"); return false;
                        }
                        
                        if (epilfolder=="") {
                            alert("Tidak ada Folder yang dipilih...!!!"); return false;
                        }
                        
                        if (epilfile=="") {
                            alert("Tidak ada File yang dipilih...!!!"); return false;
                        }

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        //alert(epilfile); return false;
                        pText_="Yakin akan melakukan import data, AMS...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/spp_cekimport.php?module="+module+"&idmenu="+idmenu+"&act=sppcekimport",
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder+"&upilfile="+epilfile,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
                        
                        
                    }
                    
                    function AMSCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        pText_="Yakin akan melakukan import data, AMS...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/ams_cekimport.php?module="+module+"&idmenu="+idmenu+"&act=amscekimport",
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                    
                   
                    
                    function AKFCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="akfcekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, AKF...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/akf_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                    
                    
                   
                    
                    function EPCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="epcekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, EP...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/ep_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                   
                    
                    function GMPCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="gmpcekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, GMP...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/gmp_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                   
                    
                    function BCMCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="bcmcekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, BCM...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/bcm_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                   
                    
                    function BKSCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="bkscekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, BKS...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/bks_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                   
                    
                    function MPSCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="mpscekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, MPS...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/mps_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                   
                    
                    function PVCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="pvcekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, PV...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/pv_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                   
                    
                    function SSTCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="sstcekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, SST...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/sst_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                   
                    
                    function CPCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="cpcekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, CP...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/cp_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                   
                    
                    function CPPCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="cppcekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, CPP...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/cpp_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                   
                    
                    function SKSCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="cppcekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, SKS...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/sks_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                   
                    
                    function CPMCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="cpmcekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, CPM...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/cpm_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                   
                    
                    function MASCekImportData() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var epilfolder = document.getElementById("txtpilfoder").value;

                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var iactpil="cpmcekimport";
                        
                        //alert(iactpil); return false
                        
                        pText_="Yakin akan melakukan import data, MAS...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/mas_cekimport.php?module="+module+"&idmenu="+idmenu+"&act="+iactpil,
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&upilfolder="+epilfolder,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
        
                    }
                    
                    
                    function ProsesDataUploadToTabelDist() {
                        var ebln = document.getElementById("tgl1").value;
                        var eiddist = document.getElementById("cb_distid").value;
                        var enmfilecab = "";
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        
                        var ket="";
                        var nmfileprostotbl="";
                        
                        if (eiddist=="0000000021") {
                            nmfileprostotbl="prosesdata_akf_uploadtodist.php";
                            ket="AKF";
                        }else if (eiddist=="0000000003") {
                            nmfileprostotbl="prosesdata_ams_uploadtodist.php";
                            ket="AMS";
                        }else if (eiddist=="0000000002") {
                            enmfilecab = document.getElementById("cb_pilihfile").value;
                            if (enmfilecab=="") {
                                alert("Nama cabang belum dipilih...");
                                return false;
                            }
                            nmfileprostotbl="prosesdata_spp_uploadtodist.php";
                            ket="SPP";
                        }else if (eiddist=="0000000005") {
                            nmfileprostotbl="prosesdata_pv_uploadtodist.php";
                            ket="PV";
                        }else if (eiddist=="0000000006") {
                            nmfileprostotbl="prosesdata_cpm_uploadtodist.php";
                            ket="CPM";
                        }else if (eiddist=="0000000010") {
                            nmfileprostotbl="prosesdata_sst_uploadtodist.php";
                            ket="SST";
                        }else if (eiddist=="0000000011") {
                            enmfilecab = document.getElementById("e_tgl_01").value;
                            if (enmfilecab=="") {
                                alert("Tanggal Proses Upload Belum diisi....");
                                return false;
                            }
                            nmfileprostotbl="prosesdata_cp_uploadtodist.php";
                            ket="CP";
                        }else if (eiddist=="0000000030") {
                            nmfileprostotbl="prosesdata_cpp_uploadtodist.php";
                            ket="CPP";
                        }else if (eiddist=="0000000031") {
                            nmfileprostotbl="prosesdata_sks_uploadtodist.php";
                            ket="SKS";
                        }else if (eiddist=="0000000015") {
                            nmfileprostotbl="prosesdata_ep_uploadtodist.php";
                            ket="EP";
                        }else if (eiddist=="0000000033") {
                            nmfileprostotbl="prosesdata_bcm_uploadtodist.php";
                            ket="BCM";
                        }else if (eiddist=="0000000028") {
                            nmfileprostotbl="prosesdata_bks_uploadtodist.php";
                            ket="BKS";
                        }else if (eiddist=="0000000018") {
                            nmfileprostotbl="prosesdata_gmp_uploadtodist.php";
                            ket="BKS";
                        }else if (eiddist=="0000000025") {
                            nmfileprostotbl="prosesdata_mps_uploadtodist.php";
                            ket="MPS";
                        }else if (eiddist=="0000000016") {
                            nmfileprostotbl="prosesdata_mas_uploadtodist.php";
                            ket="MAS";
                        }
                        
                        if (nmfileprostotbl=="") {
                            alert("Belum bisa proses upload...");
                            return false;
                        }
                        
                        pText_="Yakin akan melakukan Upload ke Tabel DISTRIBUTOR "+ket+", bulan "+ebln+" ...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                
                                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                                $.ajax({
                                    type:"post",
                                    url:"module/mst_import_sales/"+nmfileprostotbl+"?module="+module+"&idmenu="+idmenu+"&act=prosesdata",
                                    data:"ubln="+ebln+"&uiddist="+eiddist+"&unmfilecab="+enmfilecab,
                                    success:function(data){
                                        $("#c-data").html(data);
                                        $("#loading").html("");
                                    }
                                });
                            }
                            
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
                        
                    }
                </script>
                
                
                
                
                
                <script>
                    $(function() {
                        $('#tgl1').datepicker({
                            showButtonPanel: true,
                            changeMonth: true,
                            changeYear: true,
                            numberOfMonths: 1,
                            firstDay: 1,
                            dateFormat: 'MM yy',
                            onSelect: function(dateStr) {

                            },
                            onClose: function() {
                                var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                                var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                                Kosongkan();
                            },

                            beforeShow: function() {
                                if ((selDate = $(this).val()).length > 0) 
                                {
                                    iYear = selDate.substring(selDate.length - 4, selDate.length);
                                    iMonth = jQuery.inArray(selDate.substring(0, selDate.length - 5), $(this).datepicker('option', 'monthNames'));
                                    $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                                    $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
                                }
                            }
                        });
                    });
                </script>

                <style>
                    .divnone {
                        display: none;
                    }
                    #datatable th {
                        font-size: 13px;
                    }
                    #datatable td { 
                        font-size: 12px;
                    }
                    .ui-datepicker-calendar {
                        display: none;
                    }
                </style>
                <?PHP

            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

