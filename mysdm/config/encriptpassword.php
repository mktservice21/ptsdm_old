<?php
function DecriptedPassword($ilpass){
    $lcpass=$ilpass;
    $passARR=  explode("  ", $lcpass);
    $nItem= count($passARR);
    $a = trim(strtoupper($passARR[0]));
    if ($nItem > 1) {$A2=trim($passARR[1]); }
    $X=strlen($a) % 6;// % fungsi MOD
    $xi = (strLen($a) - $X) / 6;
    $xi= strlen($A2);
    $txme = "";
    $zi = 1;
    $az = 0;
    $amin = $a;
    $cari0001 = 0;
    $nn=0;
    
    for ($i=0; $i<=($xi-1); $i++){
        $nn++;
        
        $pret = substr($A2, $i, 1);
        $tx3 = substr($a, $az, $pret);
        $az = $az + $pret;
        
        if ($nn==$xi and empty($pret) and empty($tx3)){
            $pret = substr($A2, $i, 1);
            $tx3 = substr($a, $az, $pret);
            $az = $az + $pret;
            //echo "xi=$xi <br/>A2=$A2, i=8  <br />$pret, $tx3, $az";    echo "<br />";  exit;
        }
        $tx1 = substr($tx3, 0, 2);
        $tx2 = substr($tx3, 2, 2);
        $tx3 = substr($tx3, 4, strLen($tx3) - 4);
        
        if ($txt3>=0 And $txt3<256){
            $tx=(intval($tx3)*intval($tx2))+intval($tx1);
            $tx=chr(intval($tx));
            $txme = $txme.$tx;
            
        }else{
            $tx3 = substr($tx3, strLen($tx3) - 1);
            $tx = (intval($tx3) * intval($tx2)) + intval($tx1);
            $tx = chr(intval($tx));
            $zi = $zi - 1;
            
            echo "$tx3, $tx, $zi";echo "<br />";exit;
            
        }
    }
    return $txme;
}

function encriptpassword($ilpass, $tgl){
    $nama=$ilpass;
    $pj=strlen($nama)-1;
    $bln = substr($tgl,4,2);
    $xi=0;
    for ($x=0; $x<=$pj; $x++){
        $ab=ord(substr($nama, $xi,1)) % $bln;
        $cd=(ord(substr($nama, $xi,1))-$ab) / $bln;

        if (strlen($ab)==1) { $ab='0'.$ab; }
        if (strlen($bln)==1) { $bln='0'.$bln; }
        if (strlen($cd)==1) { $cd='0'.$cd; }

        $pass=$pass .$ab.$bln.$cd;
        
        $ppj=$ab.$bln.$cd;
        $keyNo = $keyNo.strLen($ppj);
        
        $xi++;
    }
    
    If (strLen($ppj) > 0) { $keyNo = $keyNo.strLen($ppj); }
    $pass=$pass."  ".$keyNo;
    
    return $pass;
}

function encriptpasswordSSQL($ilpass, $tgl){
    $nama=$ilpass;
    $pj=strlen($nama)-1;
    $bln = substr($tgl,4,2);
    $xi=0;$pass="";
    for ($x=0; $x<=$pj; $x++){
        $ab=ord(substr($nama, $xi,1)) % $bln;
        $cd=(ord(substr($nama, $xi,1))-$ab) / $bln;

        if (strlen($ab)==1) { $ab='0'.$ab; }
        if (strlen($bln)==1) { $bln='0'.$bln; }
        if (strlen($cd)==1) { $cd='0'.$cd; }
        
        $pass=$pass.$ab.$bln.$cd;

        $xi++;
    }
    return $pass;
}
?>
