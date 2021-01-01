<?php

for ($x=0; $x<=10; $x++){
    $dn_br="chk_br".$x;
    $dn_urut="cb_urut".$x;
    $n_br="";
    $n_urut="";
    if (isset($_POST[$dn_br])) {
        $n_br=$_POST[$dn_br];
        $n_urut=$_POST[$dn_urut];
    }
    echo "$n_br - $n_urut<br/>";
}

exit;

    $dname="chk_jml1";
    $datanya=$_POST[$dname];
    
    $dname2="cb_urut1";
    $datanya2=$_POST[$dname2];
    
    if (!empty($datanya)){
        $tag = implode(',',$datanya);
        $arr_kata = explode(",",$tag);
        $count_kata = count($arr_kata);
        $jumlah_tag = substr_count($tag, ",") + 1;
        $u=0;
        
        $tag_2 = implode(',',$datanya2);
        $arr_kata_2 = explode(",",$tag_2);
        
        for ($x=0; $x<=$jumlah_tag; $x++){
            if (!empty($arr_kata[$u])){
                $nobrinput=trim($arr_kata[$u]);
                $n_nourut=$arr_kata_2[$u-1];
                if (!empty($nobrinput) AND $nobrinput <> "0") {
                    echo "$nobrinput $n_nourut<br/>";
                }
            }
            $u++;
        }
    }
    
    echo "<br/>&nbsp;ada <br/>&nbsp;<br/>&nbsp;";
    if (!empty($datanya2)){
        $tag = implode(',',$datanya2);
        $arr_kata = explode(",",$tag);
        $count_kata = count($arr_kata);
        $jumlah_tag = substr_count($tag, ",") + 1;
        $u=0;
        
        for ($x=0; $x<=$jumlah_tag; $x++){
            if (!empty($arr_kata[$u])){
                $nobrinput=trim($arr_kata[$u]);
                if (!empty($nobrinput) AND $nobrinput <> "0") {
                    echo "$nobrinput<br/>";
                }
            }
            $u++;
        }
    }
?>

