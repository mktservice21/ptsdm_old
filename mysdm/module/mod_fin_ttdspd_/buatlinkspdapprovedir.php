<?php

    session_start();
    include "../../config/koneksimysqli.php";
    
    $module=$_GET['module'];
    $idmenu=$_GET['idmenu'];
    $act=$_GET['act'];
    $cnmy=$cnmy;
    $dbname = "dbmaster";
    
    $pses_grpuser=$_SESSION['GROUP'];
    $pses_divisi=$_SESSION['DIVISI'];
    $pses_idcard=$_SESSION['IDCARD'];
    $pses_idsesi=$_SESSION['IDSESI'];
    
    $noidbr=$_POST['unobr'];
    if ($noidbr=="()") $noidbr = "";
    
    $berhasil = "Tidak ada data yang dipilih...";
    
    if (empty($noidbr)) {
        //echo $berhasil;
        //exit;
    }
    
    
    // Program to display URL of current page. 
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
        $link = "https"; 
    else
        $link = "http"; 
    // Here append the common URL characters. 
    $link .= "://"; 
    // Append the host(domain name, ip) to the URL. 
    $link .= $_SERVER['HTTP_HOST']; 
    // Append the requested resource location to the URL 
    //$link .= $_SERVER['REQUEST_URI']; 
    // Print the link 
    if ($link=="http://ms.marvis.id") {
    }else{
        $link .="/ptsdm";
    }
    
    
    if ($module=="buatlinkappdir" AND !empty($noidbr)) {
        $pigroup=1;
        
        //$query = "DELETE FROM dbmaster.t_suratdana_br_link WHERE idinput IN $noidbr AND IFNULL(stsapvdir,'')<>'Y'";
        //mysqli_query($cnmy, $query);
        //$erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
                
        $sql=  mysqli_query($cnmy, "select MAX(idgroup) idgroup from dbmaster.t_suratdana_br_link");
        $ketemu=  mysqli_num_rows($sql);
        if ($ketemu>0){
            $o=  mysqli_fetch_array($sql);
            
            $pigroup=$o['idgroup'];
            if (empty($pigroup)) $pigroup=0;
            $pigroup++;
        }
        
        $plinkbuka = "module=apvdirpilihlink&act=apv&xyz=$pigroup&nomxyz=$pses_idcard";
		
        $plinkbukaWA = "module=apvdirpilihlink%26act=apv%26xyz=$pigroup%26nomxyz=$pses_idcard";
		
		
        $query = "select idinput from dbmaster.t_suratdana_br WHERE idinput IN $noidbr";
        $tampil = mysqli_query($cnmy, $query);
        $ketemu= mysqli_num_rows($tampil);
        if ($ketemu>0) {
            while ($row= mysqli_fetch_array($tampil)) {
                $pidinputspd=$row['idinput'];
                
                $query = "INSERT INTO dbmaster.t_suratdana_br_link (idgroup, idinput, userid, linkpilih, session_id)VALUES"
                        . "('$pigroup', '$pidinputspd', '$pses_idcard', '$plinkbuka', '$pses_idsesi')";
                mysqli_query($cnmy, $query);
                $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
            }
			
            $plinkbuka = "$link/anprs/xyz_nnnn_eks_44511900055.php?$plinkbuka";
			
			$plinkbukaWA = "$link/anprs/xyz_nnnn_eks_44511900055.php?$plinkbukaWA";
            
            $berhasil="$plinkbuka";
        }
    }else{
        $plinkbuka="";
		$plinkbukaWA="";
        $link="";
    }
    
    

    mysqli_close($cnmy);
    //echo $berhasil;
?>
    
    
    
<div class='modal-dialog modal-lg'>
    <!-- Modal content-->
    <div class='modal-content'>
        <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal'>&times;</button>
            <h4 class='modal-title'>LINK</h4>
        </div>

        
        
        <div class="">

            <!--row-->
            <div class="row">

                

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>

                            <div class='x_panel'>
                                <div class='x_content'>
                                    <div class='col-md-12 col-sm-12 col-xs-12'>

                                        <div class='form-group'>
                                            <div class='col-md-12'>
                                                <textarea id='e_datalink' name='e_datalink' class='form-control' rows="5" cols="100"><?PHP echo "$plinkbuka"; ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <?PHP if (!empty($plinkbuka)) { ?>
                                            <div class='form-group'>
                                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                                <div class='col-xs-9'>
                                                    <div class="checkbox">
                                                        <button title='copy link' class='btn btn-default' onclick="myCopyClip('e_datalink')">Copy Link</button>
														<span>
                                                        <a class='btn btn-success' href="<?PHP echo "https://wa.me/?text=$plinkbukaWA"; ?>" target="_blank">Share WA</a>
														</span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?PHP } ?>
                                    </div>

                                </div>
                            </div>


                        </div>
                    </div>


                
            </div>
            <!--end row-->
        </div>
        
        
        <div class='modal-footer'>
            <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
        </div>
    </div>
</div>

        
<script>
    function myCopyClip(text) {
        /* Get the text field */
        var copyText = document.getElementById(text);
        /* Select the text field */
        copyText.select();

        /* Copy the text inside the text field */
        document.execCommand("copy");

        /* Alert the copied text */
        //alert("Copied the text: " + copyText.value);
    }
</script>