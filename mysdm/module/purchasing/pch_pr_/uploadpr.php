<?PHP
    $act=$_GET['act'];
    include "config/fungsi_ubahget_id.php";
    
    $pidbr_ec=$_GET['id'];
    $pidbr = decodeString($pidbr_ec);
    
    $pidbr_ec_d=$_GET['idd'];
    $pidbr_d = decodeString($pidbr_ec_d);
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbpurchasing.t_pr_transaksi WHERE idpr='$pidbr'");
    $r    = mysqli_fetch_array($edit);
    $idnomor=$r['idpr'];
    $pkaryawanid=$r['karyawanid']; 
    $nmajukan = getfield("select nama as lcfields from hrd.karyawan where karyawanid='$pkaryawanid'");
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbpurchasing.t_pr_transaksi_d WHERE idpr='$pidbr' AND idpr_d='$pidbr_d'");
    $row    = mysqli_fetch_array($edit);
    $pnamabarang=$row['namabarang'];
?>
<script>
    function disp_confirm(pText_, ket)  {
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");
            
            document.getElementById("d-formup").action = "module/purchasing/pch_pr/aksi_purchasereq.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
            document.getElementById("d-formup").submit();
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
    
<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class="">

    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=$act&idmenu=$_GET[idmenu]"; ?>' id='d-formup' name='form1' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $idnomor; ?>' Readonly>
                                        <input type='hidden' id='e_idd' name='e_idd' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr_d; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Yang Membuat <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_nama' name='e_nama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nmajukan; ?>' Readonly>
                                    </div>
                                </div>

                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Barang <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                        <input type='text' id='e_brgnama' name='e_brgnama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamabarang; ?>' Readonly>
                                    </div>
                                </div>

                                <div hidden><textarea id="e_imgconv" name="e_imgconv"></textarea></div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Upload <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <div class="checkbox">
                                            <input type='file' name='image1' id='image' onchange="loadImageFile();" accept='image/jpeg,image/JPG,,image/JPEG;capture=camera'/>
                                            <br/><img id="upload-Preview" height="100px"/> <b>Preview</b>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-md-6 col-sm-6 col-xs-12'>
                                        <br/>
                                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                        <a class='btn btn-default' href="<?PHP echo "?module=$_GET[module]&idmenu=$_GET[idmenu]&act=$_GET[idmenu]"; ?>">Back</a>
                                    </div>
                                </div>
                                
                                
                        
                            </div>
                            
                            
                            
                            
                        </div>
                    </div>
                    
                    
                    <?PHP
                    //$query = "select * from dbimages.img_pr where idpr='$pidbr' AND idpr_d='$pidbr_d' AND idpr IN (select distinct IFNULL(idpr,'') from dbpurchasing.t_pr_transaksi where(userid='$_SESSION[IDCARD]' OR karyawanid='$_SESSION[IDCARD]'))";
                    $query = "select * from dbimages.img_pr where idpr='$pidbr' AND idpr_d='$pidbr_d' ";
                    $tampil= mysqli_query($cnmy, $query);
                    $ketemu=mysqli_num_rows($tampil);
                    if ($ketemu>0) {
                        $pnidrtn=$_GET['id'];
                        $no=1;
                        while ($i= mysqli_fetch_array($tampil)) {
                            $idgam=$i['nourut'];
                            $gambar=$i['gambar'];
                            $gambar2=$i['gambar'];
                            
                            if (!empty($gambar2)) {
                                $data="data:".$gambar2;
                                $data=str_replace(' ','+',$data);
                                list($type, $data) = explode(';', $data);
                                list(, $data)      = explode(',', $data);
                                $data = base64_decode($data);
                                //$namapengaju="img_".$no."IDPRTR_.png";
                                $namapengaju="img_".$no."IDPRTR_".$pnidrtn."_.png";
                                file_put_contents('images/tanda_tangan_base64/'.$namapengaju, $data);
                            }
                            
                            $lihat ="<a title='Lihat Gambar' href='#' class='btn btn-success btn-xs' data-toggle='modal' "
                                . "onClick=\"window.open('eksekusi3.php?module=$_GET[module]&id=$idgam&iprint=lihatgambar',"
                                . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                                . "Lihat</a>";
                            echo "<div class='col-sm-2'><div class='form-group'>";
                            if (empty($gambar2))
                                echo '<img class="imgzoomx" src="data:image/jpeg;base64,'.base64_encode( $gambar ).'" height="100" width="100" class="img-thumnail"/>';
                            else
                                echo "<img class='imgzoomx' src='images/tanda_tangan_base64/$namapengaju' height='100' width='100' class='img-thumnail'>";
                            echo "<br/>$lihat";
                            echo "<input type='button' class='btn btn-danger btn-xs' name='bhapus' value='Hapus' onclick=\"disp_confirm('Hapus ?', 'hapusgambar&idgam=$idgam&id=$pidbr&idd=$pidbr_d')\">";
                            echo "</div></div>";
                            
                            $no++;
                        }
                    }
                    ?>
                    
                    
                </div>
            </div>

        </form>
        

        
    </div>
    <!--end row-->
</div>
