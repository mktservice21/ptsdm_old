<?php
function format_rupiah($angka){
  $rupiah=number_format($angka,0,',','.');
  return $rupiah;
}

function format_rupiah2($angka){
  $rupiah=number_format($angka,0,',',',');
  return $rupiah;
}
?> 
