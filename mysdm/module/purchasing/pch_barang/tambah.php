<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
</style>

<script>
    
    function disp_confirm(pText_,ket)  {
        var etipeb =document.getElementById('cb_tipebrg').value;
        var enmbarang =document.getElementById('e_nmbarang').value;
        
        if (etipeb=="") {
            alert("tipe barang harus dipilih...");
            return false;
        }
        
        if (enmbarang=="") {
            alert("nama barang masih kosong...");
            return false;
        }
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/pch_barang/aksi_pchbarang.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    
    }
</script>

<script type="text/javascript">
    var fileReader = new FileReader();
    var filterType = /^(?:image\/bmp|image\/cis\-cod|image\/gif|image\/ief|image\/jpeg|image\/jpeg|image\/jpeg|image\/pipeg|image\/png|image\/svg\+xml|image\/tiff|image\/x\-cmu\-raster|image\/x\-cmx|image\/x\-icon|image\/x\-portable\-anymap|image\/x\-portable\-bitmap|image\/x\-portable\-graymap|image\/x\-portable\-pixmap|image\/x\-rgb|image\/x\-xbitmap|image\/x\-xpixmap|image\/x\-xwindowdump)$/i;

    fileReader.onload = function (event) {
        var image = new Image();

        image.onload=function(){
            //document.getElementById("original-Img").src=image.src;
            var canvas=document.createElement("canvas");
            var context=canvas.getContext("2d");
            canvas.width=image.width/4;
            canvas.height=image.height/4;
            context.drawImage(image,
                0,
                0,
                image.width,
                image.height,
                0,
                0,
                canvas.width,
                canvas.height
            );
            document.getElementById("upload-Preview").src = canvas.toDataURL();
            document.getElementById("e_imgconv").value = canvas.toDataURL();
        }
        image.src=event.target.result;
    };

    var loadImageFile = function () {
        var uploadImage = document.getElementById("image");

        //check and retuns the length of uploded file.
        if (uploadImage.files.length === 0) { 
            return; 
        }

        //Is Used for validate a valid file.
        var uploadFile = document.getElementById("image").files[0];
        if (!filterType.test(uploadFile.type)) {
            alert("Please select a valid image."); 
            return;
        }
        fileReader.readAsDataURL(uploadFile);
    }
</script>

<?PHP

$idbr="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));

$pidtipe="";
$pdivisiid="";
$pbrandprod="";
$pkategoriid="";

$pharga="";

$psatuanid="";
$pnamabrg="";
$pspesifikasi="";
$pketerangan="";
$psupplierid="00001";

$pidnourutgbt="";
$pgambar="";
    
$pgetact=$_GET['act'];
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    $idbr=$_GET['id'];
    
    $query = "SELECT * FROM dbmaster.t_barang WHERE IDBARANG='$idbr'";
    $tampil= mysqli_query($cnmy, $query);
    $row= mysqli_fetch_array($tampil);
    
    $pidtipe=$row['IDTIPE'];
    $pdivisiid=$row['DIVISIID'];
    $pbrandprod=$row['IDBRAND'];
    $pkategoriid=$row['IDKATEGORI'];
    $pharga=$row['HARGA'];
    $psatuanid=$row['IDSATUAN'];
    $pnamabrg=$row['NAMABARANG'];
    
    $pspesifikasi=$row['SPESIFIKASI'];
    $pketerangan=$row['KETERANGAN'];
    $psupplierid=$row['KDSUPP'];

    
}


?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' enctype='multipart/form-data'>
        
        
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tipe Barang <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                            <select class='form-control input-sm' id='cb_tipebrg' name='cb_tipebrg' data-live-search="true">
                                              <?PHP
                                                $query = "select IDTIPE, NAMA_TIPE from dbmaster.t_barang_tipe WHERE IFNULL(AKTIF,'')<>'N' AND IDTIPE NOT IN ('30001') ";
                                                $query .=" ORDER BY NAMA_TIPE";
                                                $tampil= mysqli_query($cnmy, $query);
                                                
                                                $pbelumdiv=false;
                                                while ($row= mysqli_fetch_array($tampil)) {
                                                    $npidtipe=$row['IDTIPE'];
                                                    $npnmtipe=$row['NAMA_TIPE'];

                                                    if ($npidtipe==$pidtipe) {
                                                          echo "<option value='$npidtipe' selected>$npnmtipe</option>";
                                                    }else{
                                                        echo "<option value='$npidtipe'>$npnmtipe</option>";
                                                    }
                                                }
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Kategori <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                          <select class='form-control input-sm' id='cb_kategori' name='cb_kategori' data-live-search="true">
                                              <?PHP 
                                              $query = "select distinct IDKATEGORI, NAMA_KATEGORI from dbmaster.t_barang_kategori WHERE IFNULL(STSAKTIF,'')='Y' ";
                                              $query .=" ORDER BY NAMA_KATEGORI";
                                              $tampil= mysqli_query($cnmy, $query);
                                              while ($row= mysqli_fetch_array($tampil)) {
                                                  $npidkategori=$row['IDKATEGORI'];
                                                  $npnmkategori=$row['NAMA_KATEGORI'];
                                                  
                                                  if ($npidkategori==$pkategoriid)
                                                        echo "<option value='$npidkategori' selected>$npnmkategori</option>";
                                                  else
                                                      echo "<option value='$npidkategori'>$npnmkategori</option>";
                                              }
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Barang <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_nmbarang' name='e_nmbarang' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamabrg; ?>' onkeyup="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Spesifikasi <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_spesif' name='e_spesif' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pspesifikasi; ?>' >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_keterangan' name='e_keterangan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>' >
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>

                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        Harga / PCS
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='text' id='e_harga' name='e_harga' autocomplete='off' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pharga; ?>'>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Satuan <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                          <select class='form-control input-sm' id='cb_satuan' name='cb_satuan' data-live-search="true">
                                              <option value="" selected>--Pilihan--</option>
                                              <?PHP 
                                              $query = "select distinct IDSATUAN, SATUAN from dbmaster.t_barang_satuan ";
                                              $query .=" ORDER BY SATUAN";
                                              $tampil= mysqli_query($cnmy, $query);
                                              while ($row= mysqli_fetch_array($tampil)) {
                                                  $npidsatuan=$row['IDSATUAN'];
                                                  $npnmsatuan=$row['SATUAN'];
                                                  
                                                  if ($npidsatuan==$psatuanid)
                                                        echo "<option value='$npidsatuan' selected>$npnmsatuan</option>";
                                                  else
                                                      echo "<option value='$npidsatuan'>$npnmsatuan</option>";
                                              }
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                

                                <div hidden><textarea id="e_imgconv" name="e_imgconv"></textarea></div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Upload Gambar <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class="checkbox">
                                            <input type='file' name='image1' id='image' onchange="loadImageFile();" accept='image/jpeg,image/JPG,,image/JPEG;capture=camera'/>
                                            <br/><img id="upload-Preview" height="100px"/> <b>Preview</b>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                            <input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    <?PHP
                    $querygbr = "SELECT NOURUT, GAMBAR FROM dbimages.img_barang_gimic WHERE IDBARANG='$idbr' ORDER BY NOURUT";
                    $tampilgbr= mysqli_query($cnmy, $querygbr);
                    while ($rgb= mysqli_fetch_array($tampilgbr)) {
                        $pidnourutgbt=$rgb['NOURUT'];
                        $pgambar=$rgb['GAMBAR'];
                    
                        if (!empty($pgambar)) {
                            $data="data:".$pgambar;
                            $data=str_replace(' ','+',$data);
                            list($type, $data) = explode(';', $data);
                            list(, $data)      = explode(',', $data);
                            $data = base64_decode($data);
                            $namapengaju="img_".$pidnourutgbt."GMCID_".$idbr."_.png";
                            file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
                            
                            echo "<div class='col-sm-2'><div class='form-group'>";
                            
                            echo "<img class='imgzoomx' src='images/tanda_tangan_base64/$namapengaju' height='100' width='100' class='img-thumnail'>";
                            
                            $lihat ="<a title='Lihat Gambar' href='#' class='btn btn-success btn-xs' data-toggle='modal' "
                                . "onClick=\"window.open('eksekusi3.php?module=$_GET[module]&id=$pidnourutgbt&iprint=lihatgambar',"
                                . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                                . "Lihat</a>";
                            
                            echo "<br/><br/>$lihat";
                            echo "<input type='button' class='btn btn-danger btn-xs' name='bhapus' value='Hapus' onclick=\"disp_confirm('Hapus ?', 'hapusgambar&idgam=$pidnourutgbt&id=$idbr')\">";
                            echo "</div></div>";
                        }
                        
                    }
                    ?>
                </div>
            </div>
            
        </form>
        
    </div>
    
    
</div>