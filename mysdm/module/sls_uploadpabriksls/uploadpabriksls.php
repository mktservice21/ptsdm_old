<?PHP
    
    include "config/cek_akses_modul.php";
    $pidgroup=$_SESSION['GROUP'];
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    $ptahunini = date('Y', strtotime($hari_ini));
    $pawalthn="2020";
    
    $pfilename="";
    
    $nact="";
    if (isset($_GET['act'])) $nact=$_GET['act'];
    
    $phiden="";
    if ($pidgroup=="50") $phiden="hidden";
	
    $pjudul="Upload Sales Pabrik";
?>
<div class="">

    <div class="page-title"><div class="title_left"><h3><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli_ms.php";
        $aksi="module/sls_uploadpabriksls/aksi_uploadpabriksls.php";
        switch($_GET['act']){
            default:
                ?>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                            
                            <div class='col-sm-2'>
                                Jenis
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_untuk" name="cb_untuk" onchange="">
                                        <option value="S" selected>Sales</option>
                                        <option value="R" >Retur</option>
                                    </select>
                                </div>
                            </div>
                            

                            
                            <div class='col-sm-2'>
                                Tahun
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_tahun" name="cb_tahun" onchange="">
                                        <option value="">All</option>

                                        <?PHP
                                        for($nthn=$pawalthn;$nthn<=$ptahunini;$nthn++) {
                                            if ($nthn==$ptahunini)
                                                echo "<option value='$nthn' selected>$nthn</option>";
                                            else
                                                echo "<option value='$nthn'>$nthn</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            
                            <div <?PHP echo $phiden; ?> class='col-sm-3'>
                                Load File (<b>Format File ZIP</b>)
                                <div class="form-group">
                                    <input type="file" id="txtnmfile" name="fileToUpload" id="fileToUpload" accept=".zip">
                                </div>
                            </div>

                            
                            
                            <div class='col-sm-3'
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <?PHP
                                   
                                   if ($pidgroup=="50") {
                                   }else{
                                   ?>
                                        <button type='button' class='btn btn-dark btn-xs' onclick="UploadDataKeServer('1')">Proses Upload</button>
                                   <?PHP
                                   }
                                   ?>
                                   <button type='button' class='btn btn-info btn-xs' onclick="UploadDataKeServer('2')">Lihat Data</button>
                               </div>
                           </div>
                            
                            
                        </form>
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                           
                        </div>
                        
                    </div>
                </div>

                <script>
                    function UploadDataKeServer(skey) {
                        var inmfile=document.getElementById("txtnmfile").value;
                        var ijenis=document.getElementById("cb_untuk").value;
                        var ithn=document.getElementById("cb_tahun").value;
                        var inmjenis="Sales Pabrik";
                        if (ijenis=="R") inmjenis="Sales Retur Pabrik";
                        pText_="Data "+inmjenis+" akan diupload ke server...?";
                        
                        if (skey=="2") {
                            var myurl = window.location;
                            var urlku = new URL(myurl);
                            var module = urlku.searchParams.get("module");
                            var idmenu = urlku.searchParams.get("idmenu");

                            document.getElementById("demo-form2").action = "?module=slsuploadsalespabriklihat"+"&act=upload"+"&idmenu="+idmenu+"&skey="+skey+"&nmodul="+module;
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }else{
                        
                            if (inmfile=="") {
                                alert("FILE BELUM DILOAD....");
                                return false;
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

                                    //document.getElementById("demo-form2").action = "module/sls_uploadpabriksls/aksi_uploadpabriksls.php?module="+module+"&act=upload"+"&idmenu="+idmenu;
                                    document.getElementById("demo-form2").action = "?module=slsuploadsalespabrikpros"+"&act=upload"+"&idmenu="+idmenu+"&skey="+skey+"&nmodul="+module;
                                    document.getElementById("demo-form2").submit();
                                    return 1;
                                }
                            } else {
                                //document.write("You pressed Cancel!")
                                return 0;
                            }
                        
                        }
                        
                    }
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

