<?php

function untukPenyebut($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
                $temp = " ". $huruf[$nilai];
        } else if ($nilai <20) {
                $temp = untukPenyebut($nilai - 10). " belas";
        } else if ($nilai < 100) {
                $temp = untukPenyebut($nilai/10)." puluh". untukPenyebut($nilai % 10);
        } else if ($nilai < 200) {
                $temp = " seratus" . untukPenyebut($nilai - 100);
        } else if ($nilai < 1000) {
                $temp = untukPenyebut($nilai/100) . " ratus" . untukPenyebut($nilai % 100);
        } else if ($nilai < 2000) {
                $temp = " seribu" . untukPenyebut($nilai - 1000);
        } else if ($nilai < 1000000) {
                $temp = untukPenyebut($nilai/1000) . " ribu" . untukPenyebut($nilai % 1000);
        } else if ($nilai < 1000000000) {
                $temp = untukPenyebut($nilai/1000000) . " juta" . untukPenyebut($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
                $temp = untukPenyebut($nilai/1000000000) . " milyar" . untukPenyebut(fmod($nilai,1000000000));
        } else if ($nilai < 1000000000000000) {
                $temp = untukPenyebut($nilai/1000000000000) . " trilyun" . untukPenyebut(fmod($nilai,1000000000000));
        }     
        return $temp;
}

function BuatTerbilang($nilai) {
        if($nilai<0) {
                $hasil = "minus ". trim(untukPenyebut($nilai));
        } else {
                $hasil = trim(untukPenyebut($nilai));
        }
        if (!empty($hasil)) $hasil .=" rupiah";
        return $hasil;
}
        
?>