<?php

function saveimagetemp($nmfieldimg, $fupload_name, $size) {
    //ini_set("post_max_size", "50M");
    //ini_set("upload_max_filesize", "50M");
    //ini_set("memory_limit", "70000M"); 
	
    //direktori gambar
    $vdir_upload = "../../images/temporary/";
    $vfile_upload = $vdir_upload . $fupload_name;
    if (empty($size)) $size=200;
    //Simpan gambar dalam ukuran sebenarnya
    move_uploaded_file($_FILES["$nmfieldimg"]["tmp_name"], $vfile_upload);

    //identitas file asli
    $im_src = imagecreatefromjpeg($vfile_upload);
    $src_width = imageSX($im_src);
    $src_height = imageSY($im_src);

    //Simpan dalam versi small 120 pixel
    //Set ukuran gambar hasil perubahan
    $dst_width = (int)$size;
    $dst_height = ($dst_width/$src_width)*$src_height;

    //proses perubahan ukuran
    $im = imagecreatetruecolor($dst_width,$dst_height);
    imagecopyresampled($im, $im_src, 0, 0, 0, 0, $dst_width, $dst_height, $src_width, $src_height);

    //Simpan gambar
    imagejpeg($im,$vdir_upload . "kecil_" . $fupload_name);
    
    $instr = fopen($vdir_upload."kecil_" . $fupload_name,"rb");  //need to move this to a safe directory
    $file = addslashes(fread($instr,filesize($vdir_upload."kecil_" . $fupload_name)));
    
    //Hapus gambar di memori komputer
    imagedestroy($im_src);
    imagedestroy($im);
    
    fclose($instr);
    
    unlink($vdir_upload . "kecil_" . $fupload_name);
    unlink($vdir_upload . $fupload_name);
    
    return $file;
}

?>