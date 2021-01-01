<?PHP
    include "config/koneksimysqli_ms.php";
    
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime($hari_ini));
    
    $pfilename="";
    $pidcabang="";
    if (!empty($_SESSION['DOKUPNMFILE'])) $pfilename=$_SESSION['DOKUPNMFILE'];
    if (!empty($_SESSION['DOKUPIDCAB'])) $pidcabang=$_SESSION['DOKUPIDCAB'];
    
    $pmodule="";
    $pidmenu="";
    $pnact="";
    if (isset($_GET['act'])) $pnact=$_GET['act'];
    if (isset($_GET['module'])) $pmodule=$_GET['module'];
    if (isset($_GET['idmenu'])) $pidmenu=$_GET['idmenu'];
    
    $pjudul="Upload Data Dokter";
    
    $aksi="module/mod_dok_datadokter/aksi_uploaddokt.php";
?>
<div class="">

    <div class="page-title">
        <div class="title_left">
            <h3>
                <?PHP echo $pjudul; ?>
            </h3>
        </div>
    </div>
    <div class="clearfix"></div>
    
    <!--row-->
    <div class="row">

        <?php
        
        switch($pnact){
            default:
                ?>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=import&idmenu=$pidmenu"; ?>' 
                              id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'  
                              enctype='multipart/form-data'>
                            
                            <div class='col-sm-2'>
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_cabang" name="cb_cabang" onchange="Kosongkan()">
                                        <option value="" selected>--Pilih--</option>
                                        <?PHP
                                        $query = "select iCabangId as icabangid, nama as nama_cabang FROM sls.icabang WHERE 1=1 ";
                                        $query .=" AND aktif='Y' ";
                                        $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'ETH -', 'HO -')";
                                        $query .=" order by nama";
                                        $tampil= mysqli_query($cnms, $query);
                                        while ($row= mysqli_fetch_array($tampil)) {
                                            $nidcab=$row['icabangid'];
                                            $nnmcab=$row['nama_cabang'];
                                            if ($pidcabang==$nidcab)
                                                echo "<option value='$nidcab' selected>$nnmcab</option>";
                                            else
                                                echo "<option value='$nidcab'>$nnmcab</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='col-sm-3'>
                                Load File (<b>Format File XLSX | XLS</b>)
                                <div class="form-group">
                                    <input type="file" name="fileToUpload" id="fileToUpload" accept=".xls"><!--.xlsx,.xls-->
                                </div>
                            </div>

                            <div class='col-sm-5'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <button type='button' class='btn btn-dark btn-xs' onclick='UploadDataKeServer()'>Upload</button>
                                   <button type='button' class='btn btn-info btn-xs' onclick='LihatDataSudahUpload()'>Lihat Data</button>
                               </div>
                           </div>
                            
                            
                            
                            
                        </form>
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                           
                        </div>
                        
                        
                    </div>
                </div>
        
        
                <script>
                    function Kosongkan(){
                        $("#c-data").html("");
                    }
                    
                    function UploadDataKeServer() {
                        var enmfile = document.getElementById("fileToUpload").value;
                        var ecab = document.getElementById("cb_cabang").value;
                        
                        
                        if (ecab=="") {
                            alert("Cabang belum dipilih..."); return false;
                        }
                        
                        if (enmfile=="") {
                            alert("File belum diload..."); return false;
                        }
                        
                        pText_="Apakah akan melakukan upload data dokter...?";
                        
                        ok_ = 1;
                        if (ok_) {
                            var r=confirm(pText_)
                            if (r==true) {
                                //document.write("You pressed OK!")
                                var myurl = window.location;
                                var urlku = new URL(myurl);
                                var module = urlku.searchParams.get("module");
                                var idmenu = urlku.searchParams.get("idmenu");

                                document.getElementById("demo-form2").action = "?module=mstdatadokterupload"+"&act=upload"+"&idmenu="+idmenu;
                                document.getElementById("demo-form2").submit();
                                return 1;
                            }
                        } else {
                            //document.write("You pressed Cancel!")
                            return 0;
                        }
                        
                    }
                    
                    
                    function LihatDataSudahUpload() {
                        var ecab = document.getElementById("cb_cabang").value;
                        
                        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                        $.ajax({
                            type:"post",
                            url:"module/mod_dok_datadokter/lihatdatadokter.php?module=viewdata",
                            data:"ucab="+ecab,
                            success:function(data){
                                $("#c-data").html(data);
                                $("#loading").html("");
                            }
                        });
                    }
                    
                </script>
                
                <?PHP
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>

