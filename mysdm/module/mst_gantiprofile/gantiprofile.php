<?PHP
$pactpilih="";
$aksi="";
$pkaryawanid=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 
$act="updateprofile";
if (isset($_GET['act'])) $pactpilih=$_GET['act'];
switch($pactpilih){
    default:      
?>

        <div class="">

            <div class="page-title"><div class="title_left"><h3>Foto Profile</h3></div></div><div class="clearfix"></div>
            <!--row-->
            <div class="row">
                <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
                       id='data_formgp' name='formgp' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>


                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                            <div class='col-md-4'>
                                                <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pkaryawanid; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Nama <span class='required'></span></label>
                                            <div class='col-xs-5'>
                                                <input type='text' id='e_nama' name='e_nama' class='form-control col-md-7 col-xs-12' value='<?PHP echo $nmajukan; ?>' Readonly>
                                            </div>
                                        </div>

                                        <div hidden><textarea id="e_imgconv" name="e_imgconv"></textarea></div>

                                        <div class='form-group'>
                                            <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Foto <span class='required'></span></label>
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
                                            </div>
                                        </div>



                                    </div>




                                </div>
                            </div>


                        </div>
                    </div>
                    
                    
                </form>
                
            </div>
        </div>

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

                        document.getElementById("data_formgp").action = "module/mst_gantiprofile/aksi_gantiprofile.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                        document.getElementById("data_formgp").submit();
                        return 1;
                    }
                } else {
                    //document.write("You pressed Cancel!")
                    return 0;
                }
            }
    
        </script>

<?PHP
    break;
    
}
?>