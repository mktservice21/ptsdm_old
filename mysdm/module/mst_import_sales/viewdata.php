<?php

session_start();
$pmodule="";
if (isset($_GET['module'])) $pmodule=$_GET['module'];


if ($pmodule=="sppviewdata" OR $pmodule=="sppcaridatafolder") {
    $ppilfolder=$_POST['upilfolder'];
    $pbln=$_POST['ubln'];
    $pperiode= date("Ym", strtotime($pbln));
    
    $target_dir = "../../fileupload/";
    $target_dir .=$pperiode."/SPP";
    
    $pnmdir = "$target_dir/$ppilfolder/";
    
    $pnmfle=$_SESSION['MSTIMPFILEPIL'];
    
    $ppildistnya=$_POST['upildistnya'];
    $prosesuploaddata_asli="";
    if ($ppildistnya=="0000000002") {
        $prosesuploaddata_asli="ProsesDataUploadToTabelDist()";
    }
    
    
    ?>
        <div class='col-sm-2'>
            Load File (<b>File ZIP</b>)
            <div class="form-group">
                <input type="file" id="txtnmfile" name="fileToUpload" id="fileToUpload" accept=".zip">
            </div>
        </div>

        <div class='col-sm-2'>
            <small>&nbsp;</small>
           <div class="form-group">
               <button type='button' class='btn btn-success btn-xs' onclick='UploadDataSPP()'>Simpan Ke Server</button>
           </div>
       </div>

       
        <div class='col-sm-1'>
            Pilih File
            <div class="form-group">
                <select class='form-control input-sm' id="cb_pilihfile" name="cb_pilihfile">
                    <?PHP
                    echo "<option value=''>-- Pilih --</option>";
                    // Open a directory, and read its contents
                    if (!empty($ppilfolder)) {
                        if (is_dir($pnmdir)){
                            if ($dh = opendir($pnmdir)){
                                while (($pfilerar = readdir($dh)) !== false){
                                    if (!empty($pfilerar) && $pfilerar!="." && $pfilerar!="..") {
                                        
                                        $path = pathinfo($ppilfolder.'/'.$pfilerar);
                                        $ext = $path['extension'];
                                        
                                        if (!empty($ext)) {
                                            $filename_rar = basename($ppilfolder.'/'.$pfilerar);
                                            $filenameWX_rar = preg_replace("/\.[^.]+$/", "", $filename_rar);

                                            if ($pnmfle==$pfilerar)
                                                echo "<option value='$pfilerar' selected>$pfilerar</option>";
                                            else
                                                echo "<option value='$pfilerar'>$pfilerar</option>";
                                        
                                        }
                                    }
                                }
                                closedir($dh);
                            }
                        }
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class='col-sm-3'>
            <small>&nbsp;</small>
           <div class="form-group">
               <button type='button' class='btn btn-dark btn-xs' onclick='SPPCekImportData()'>Import</button>
               
                <?PHP
                if (!empty($prosesuploaddata_asli)) {
                    echo "&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-warning btn-xs' onclick='$prosesuploaddata_asli'>Proses Upload</button>";
                }
                ?>
               
           </div>
       </div>


    <?PHP
} elseif ($pmodule=="amsviewdata") {
    
    $ppilfolder=$_POST['upilfolder'];
    $pbln=$_POST['ubln'];
    $pperiode= date("Ym", strtotime($pbln));
    
    $target_dir = "../../fileupload/";
    $target_dir .=$pperiode."/SPP";
    
    $pnmdir = "$target_dir/$ppilfolder/";
    
    $pnmfle=$_SESSION['MSTIMPFILEPIL'];
    
    $ppildistnya=$_POST['upildistnya'];
    $prosesuploaddata_asli="";
    if ($ppildistnya=="0000000003" OR $ppildistnya=="0000000005" OR $ppildistnya=="0000000030") {
        $prosesuploaddata_asli="ProsesDataUploadToTabelDist()";
    }
    ?>
        <div class='col-sm-3'>
            Load File (<b>Format File ZIP</b>)
            <div class="form-group">
                <input type="file" id="txtnmfile" name="fileToUpload" id="fileToUpload" accept=".zip">
            </div>
        </div>

        <div class='col-sm-3'>
            <small>&nbsp;</small>
           <div class="form-group">
               <button type='button' class='btn btn-dark btn-xs' onclick='UploadDataKeServer()'>Import</button>
               <?PHP
               if (!empty($prosesuploaddata_asli)) {
                   echo "&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-warning btn-xs' onclick='$prosesuploaddata_asli'>Proses Upload</button>";
               }
               ?>
               
           </div>
       </div>



    <?PHP
} elseif ($pmodule=="akfviewdata") {
    $ppilfolder=$_POST['upilfolder'];
    $pbln=$_POST['ubln'];
    $pperiode= date("Ym", strtotime($pbln));
    
    $target_dir = "../../fileupload/";
    $target_dir .=$pperiode."/SPP";
    
    $pnmdir = "$target_dir/$ppilfolder/";
    
    $pnmfle=$_SESSION['MSTIMPFILEPIL'];
    
    $ppildistnya=$_POST['upildistnya'];
    $prosesuploaddata_asli="";
    if ($ppildistnya=="0000000021" OR $ppildistnya=="0000000015" OR $ppildistnya=="0000000033" OR $ppildistnya=="0000000018") {
        $prosesuploaddata_asli="ProsesDataUploadToTabelDist()";
    }
    
    ?>
        <div class='col-sm-3'>
            Load File (<b>Format File RAR</b>)
            <div class="form-group">
                <input type="file" id="txtnmfile" name="fileToUpload" id="fileToUpload" accept=".rar">
            </div>
        </div>

        <div class='col-sm-3'>
            <small>&nbsp;</small>
           <div class="form-group">
               <button type='button' class='btn btn-dark btn-xs' onclick='UploadDataKeServer()'>Import</button>
               
               <?PHP
               if (!empty($prosesuploaddata_asli)) {
                   echo "&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-warning btn-xs' onclick='$prosesuploaddata_asli'>Proses Upload</button>";
               }
               ?>
               
           </div>
       </div>



    <?PHP
} elseif ($pmodule=="bksviewdata") {
    
    $ppilfolder=$_POST['upilfolder'];
    $pbln=$_POST['ubln'];
    $pperiode= date("Ym", strtotime($pbln));
    
    $target_dir = "../../fileupload/";
    $target_dir .=$pperiode."/SPP";
    
    $pnmdir = "$target_dir/$ppilfolder/";
    
    $pnmfle=$_SESSION['MSTIMPFILEPIL'];
    $pkoenkdb="";
    if (isset($_SESSION['MSTIMPKONEPIL'])) $pkoenkdb=$_SESSION['MSTIMPKONEPIL'];
    
    $ppildistnya=$_POST['upildistnya'];
    $prosesuploaddata_asli="";
    if ($ppildistnya=="0000000006" OR $ppildistnya=="0000000010" OR $ppildistnya=="0000000028" OR $ppildistnya=="0000000016" OR $ppildistnya=="0000000023") {
        $prosesuploaddata_asli="ProsesDataUploadToTabelDist()";
    }
    
    
    ?>
        <div class='col-sm-3'>
            Load File (<b>Format File XLSX</b>)
            <div class="form-group">
                <input type="file" id="txtnmfile" name="fileToUpload" id="fileToUpload" accept=".xlsx"><!--.xlsx,.xls-->
            </div>
        </div>

        <?PHP
        if ($ppildistnya=="0000000010") { ?>
            <div class='col-sm-2'>
            Pilih DB
                <div class="form-group">
                    <select class='form-control input-sm' id="cbpilih" name="cb_pildb" onchange="">
                    <?PHP
                    if ($pkoenkdb=="M") {
                        echo "<option value='A'>--All (MS & IT)--</option>";
                        echo "<option value='M' selected>MS</option>";
                        echo "<option value='I'>IT</option>";
                    }elseif ($pkoenkdb=="I") {
                        echo "<option value='A'>--All (MS & IT)--</option>";
                        echo "<option value='M'>MS</option>";
                        echo "<option value='I' selected>IT</option>";
                    }else{
                        echo "<option value='A' selected>--All (MS & IT)--</option>";
                        echo "<option value='M'>MS</option>";
                        echo "<option value='I'>IT</option>";
                    }
                    ?>
                    </select>
                </div>
            </div>

        <?PHP }
        ?>

        
        <div class='col-sm-3'>
            <small>&nbsp;</small>
           <div class="form-group">
               <button type='button' class='btn btn-dark btn-xs' onclick='UploadDataKeServer()'>Import</button>
               
               <?PHP
               if (!empty($prosesuploaddata_asli)) {
                   echo "&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-warning btn-xs' onclick='$prosesuploaddata_asli'>Proses Upload</button>";
               }
               ?>
           </div>
       </div>



    <?PHP
} elseif ($pmodule=="mpsviewdata") {
    $ppilfolder=$_POST['upilfolder'];
    $pbln=$_POST['ubln'];
    $pperiode= date("Ym", strtotime($pbln));
    
    $target_dir = "../../fileupload/";
    $target_dir .=$pperiode."/SPP";
    
    $pnmdir = "$target_dir/$ppilfolder/";
    
    $pnmfle=$_SESSION['MSTIMPFILEPIL'];
    
    $ppildistnya=$_POST['upildistnya'];
    $prosesuploaddata_asli="";
    if ($ppildistnya=="0000000006" OR $ppildistnya=="0000000010" OR $ppildistnya=="0000000011" 
            OR $ppildistnya=="0000000031" OR $ppildistnya=="0000000025" OR $ppildistnya=="0000000023") {
        $prosesuploaddata_asli="ProsesDataUploadToTabelDist()";
    }
    
    $width_p=" col-sm-3 ";
    if ($ppildistnya=="0000000011" AND !empty($prosesuploaddata_asli)) {
        $width_p=" col-sm-1 ";
    }
    ?>
        <div class='col-sm-3'>
            Load File (<b>Format File XLS</b>)
            <div class="form-group">
                <input type="file" id="txtnmfile" name="fileToUpload" id="fileToUpload" accept=".xls"><!--.xlsx,.xls-->
            </div>
        </div>
        
        <div class='<?PHP echo $width_p;?>'>
            <small>&nbsp;</small>
           <div class="form-group">
               <button type='button' class='btn btn-dark btn-xs' onclick='UploadDataKeServer()'>Import</button>
               
               <?PHP
               if (!empty($prosesuploaddata_asli) AND $ppildistnya!="0000000011") {
                   echo "&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-warning btn-xs' onclick='$prosesuploaddata_asli'>Proses Upload</button>";
               }
               ?>
           </div>
       </div>
       
       <?PHP
       if ($ppildistnya=="0000000011" AND !empty($prosesuploaddata_asli)) {
           $hari_ini = date("Y-m-d");
       ?>
        <div class='col-sm-2'>
            <small>Tgl. Proses (bln/tgl/thn)<input type='date' id='e_tgl_01' name='e_tgl_01' required='required' value='<?PHP echo $hari_ini; ?>' size="5px"></small>
           <div class="form-group">
               <?PHP echo "<button type='button' class='btn btn-warning btn-xs' onclick='$prosesuploaddata_asli'>Proses Upload</button>"; ?>
           </div>
       </div>
       <?PHP
       }
       ?>


    <?PHP
} elseif ($pmodule=="xxxxxx") {
} elseif ($pmodule=="xxxxxx") {
    
}

