<?php
    include ("config/koneksimysqli_ms.php");
    
    
    $ptglpil=$_POST['e_periode01'];
    $tgl_pertama=$_POST['e_periode01'];
    $ptglpilihupload = date("Y-m-d", strtotime($ptglpil));
    $pperiode_ = date("Ym", strtotime($ptglpil));
    
    
    $pidcabpil=$_POST['cb_cabang'];
    
    $_SESSION['TGTUPDPERTPILCB']=$ptglpil;
    $_SESSION['TGTUPDCABPILCB']=$pidcabpil;
    
    
    
    $pjudul="Isi Produk Target Cabang (Isi Target Area Kosong/Reset(0))";
    
    $aksi="";
?>

<div class="">

    <div class="page-title"><div class="title_left"><h3 style="font-size: 18px;"><?PHP echo $pjudul; ?></h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                
                <div class='col-md-12 col-sm-12 col-xs-12'>
                    <div class='x_panel'>
                        
                        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=import&idmenu=$_GET[idmenu]"; ?>' id='demo-form3' name='form2' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                        
                        
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
                                Cabang
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_cabang" name="cb_cabang" onchange="ShowDataArea()">
                                        <?PHP
                                        $query = "select iCabangId, nama from MKT.icabang where aktif='Y' and iCabangId='$pidcabpil' order by nama";
                                        $tampil = mysqli_query($cnmy, $query);
                                        while ($rx= mysqli_fetch_array($tampil)) {
                                            $nidcab=$rx['iCabangId'];
                                            $nnmcab=$rx['nama'];
                                            if ($pidcabpil==$nidcab)
                                                echo "<option value='$nidcab' selected>$nnmcab</option>";
                                            else
                                                echo "<option value='$nidcab'>$nnmcab</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            
                            
                            <div class='col-sm-2'>
                                Area
                                <div class="form-group">
                                    <select class='form-control input-sm' id="cb_area" name="cb_area" onchange="Kosongkan()">
                                        <?PHP
                                        echo "<option value=''>-- Pilih --</option>";
                                        $query = "select icabangid, areaid, nama from sls.iarea where aktif='Y' AND icabangid='$pidcabpil' order by nama";
                                        $tampil = mysqli_query($cnms, $query);
                                        while ($rx= mysqli_fetch_array($tampil)) {
                                            $nidarea=$rx['areaid'];
                                            $nnmarea=$rx['nama'];
                                            echo "<option value='$nidarea'>$nnmarea</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class='col-sm-5'>
                                <small>&nbsp;</small>
                               <div class="form-group">
                                   <button type='button' class='btn btn-warning btn-xs' onclick='ProsesSimpanArea()'>Proses Simpan</button>
                                   &nbsp; &nbsp; &nbsp; 
                                   <button type='button' class='btn btn-success btn-xs' onclick='self.history.back()'>Back</button>
                               </div>
                           </div>
                            
                            
                        </form>
                        
                        
                        <div id='loading'></div>
                        <div id='c-data'>
                           
                        </div>
                        
                        
                    </div>
                </div>
                
                
            </div>
        </div>
        
        
    </div>
    
</div>

<script>
    function Kosongkan(){
        $("#c-data").html("");
    }


    function ProsesSimpanArea() {
        var ecabid = document.getElementById("cb_cabang").value;
        var eareaid = document.getElementById("cb_area").value;
        var ebulan = document.getElementById("tgl1").value;

        if (ecabid=="") {
            alert("Cabang Kosong..."); return false;
        }

        if (eareaid=="") {
            alert("Area Tidak Boleh Kosong..."); return false;
        }

        pText_="Jika ada data yang sudah diupload pada Cabang dan Area diBulan "+ebulan+", maka akan dihapus terlebih dahulu.\n\
Apakah yakin akan melanjutkan proses simpan...?";

        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                //document.write("You pressed OK!")
                
                
                $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
                $.ajax({
                    type:"post",
                    url:"module/tgt_upload_tgt_cab/simpanareakosongtarget.php?module=viewdata",
                    data:"uidcabang="+ecabid+"&uareaid="+eareaid+"&uperiode1="+ebulan,
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