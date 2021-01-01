<?php

//shell_exec('rar x path/to/rar/file/salesfinalsd30november.zip');
//exec("unrar unzip/file_load_import/salesfinalsd30november.zip");

$ppath="file_load_import";
$pnamezip="salesfinalsd30november.zip";

$filename = basename($ppath.'/'.$pnamezip);
$filenameWX = preg_replace("/\.[^.]+$/", "", $filename);

$unzip = new ZipArchive;
$out = $unzip->open($ppath.'/'.$pnamezip);
if ($out === TRUE) {
  $unzip->extractTo(getcwd()."/file_load_import/$filenameWX/");
  $unzip->close();
} else {
  echo 'Error';
}


$pnmdir = "$ppath/$filenameWX/";

// Open a directory, and read its contents
if (is_dir($pnmdir)){
    if ($dh = opendir($pnmdir)){
        $no=1;
        while (($pfilerar = readdir($dh)) !== false){
            if (!empty($pfilerar) && $pfilerar!="." && $pfilerar!="..") {
                echo $no.". ".$pfilerar."<br>";
                $no++;
            }
        }
    closedir($dh);
    }
}

$pfilerar="BDG191101-30.rar";

$filename_rar = basename($ppath.'/'.$pfilerar);
$filenameWX_rar = preg_replace("/\.[^.]+$/", "", $filename_rar);

$archive = RarArchive::open($pnmdir.$pfilerar);
$entries = $archive->getEntries();
foreach ($entries as $entry) {
    $entry->extract($pnmdir.$filenameWX_rar);
}
$archive->close();

exit;

/* buka koneksi database kita */
$server = '192.168.88.189:3303';
$username = 'root';
$password = "Ganteng123456";
$database = "dbtemp";
$cnmy= mysqli_connect($server,$username,$password) or die('Koneksi gagal');
mysqli_select_db($cnmy, $database) or die('Database tidak bisa dibuka');

/* memanggil file DBF untuk kita Buka */
$insert=dbase_open('test.dbf',0);
if ($insert){
$jum_record=dbase_numrecords($insert);
}

// Get column information
$column_info = dbase_get_header_info($insert);

// Display information
//print_r($column_info); exit;

for ($ind=1;$ind<=$jum_record;$ind++){
    $record=dbase_get_record($insert,$ind);
    $file0=$record[0];
    $file1=$record[1];
    $file2=$record[2];
    
    
    echo "$file0, $file1, $file2<br/>";
    
}

?>