<?PHP
    date_default_timezone_set('Asia/Jakarta');
    session_start();
    include "../../config/koneksimysqli.php";
    $pidbr=$_POST['ubrid'];
    $pidinput=$_POST['uidinput'];
    $pnmjenis=$_POST['unmjenis'];
    
    
$act="input";
$aksi="";
?>

<!--input mask -->
<script src="js/inputmask.js"></script>

    
<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>

<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>Lampiran</h4>
        </div>
        
        
        
        <div class="">

            <!--row-->
            <div class="row">

                <form method='POST' action='<?PHP echo "$aksi?module=brdanabank&act=input&idmenu=258"; ?>' id='demo-form6' name='form6' data-parsley-validate class='form-horizontal form-label-left'>
                

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>
                            
                            
                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>

                                        
                                    <?PHP
                                    $query = "select * from dbimages.img_br_cab1 where bridinputcab='$pidbr' AND noid='$pidinput'";
                                    $tampil= mysqli_query($cnmy, $query);
                                    $ketemu=mysqli_num_rows($tampil);
                                    if ($ketemu>0) {
                                        $no=1;
                                        while ($i= mysqli_fetch_array($tampil)) {
                                            $idgam=$i['nourut'];
                                            $gambar=$i['gambar'];

                                            if (!empty($gambar)) {
                                                $data="data:".$gambar;
                                                $data=str_replace(' ','+',$data);
                                                list($type, $data) = explode(';', $data);
                                                list(, $data)      = explode(',', $data);
                                                $data = base64_decode($data);
                                                $namapengaju="img_".$no."IDGBCABF_.png";
                                                file_put_contents('../../images/tanda_tangan_base64/'.$namapengaju, $data);


                                                $lihat ="<a title='Lihat Gambar' href='#' class='btn btn-success btn-xs' data-toggle='modal' "
                                                    . "onClick=\"window.open('eksekusi3.php?module=fincekprosesbrcab&id=$idgam&iprint=lihatgambar',"
                                                    . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                                                    . "Lihat</a>";
                                                echo "<div class='col-sm-9'><div class='form-group'>";
                                                echo "<img class='imgzoomx' src='images/tanda_tangan_base64/$namapengaju' height='500' width='500' class='img-thumnail'>";
                                                //echo "<br/>$lihat";
                                                echo "</div></div>";
                                            }
                                            $no++;
                                        }
                                    }
                                    ?>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            
                            
                            
                            
                        </div>
                    </div>
                    
                    
                </form>
                
            </div>
            
        </div>
        
        
    </div>
    
    
</div>


<script>
    function disp_confirm_upload(pText_, ket)  {
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            var myurl = window.location;
            var urlku = new URL(myurl);
            var module = urlku.searchParams.get("module");
            var idmenu = urlku.searchParams.get("idmenu");
            
            document.getElementById("demo-form6").action = "module/mod_fin_cekprosbrcab/simpan_upload.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
            document.getElementById("demo-form6").submit();
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
mysqli_close($cnmy);
?>