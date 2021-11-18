<?php
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    session_start();

    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    



    $pidcard=$_SESSION['IDCARD'];
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    
    $berhasil="tidak ada no rekening yang disimpan...";
    
    if ($pmodule=="approvebrquestbymkt" AND $pact=="simpankirimwabls") {
        
        include "../../../config/koneksimysqli.php";
        
        $piddokt=$_POST['uiduser'];
        
        $query = "select dokterid, nama, jekel, spid, bagian, alamat1, alamat2, kota, telp, telp2, hp, nowa, tgllahir from hrd.dokter where dokterid='$piddokt'";
        $tampil= mysqli_query($cnmy, $query);
        $row=mysqli_fetch_array($tampil);

        $pnamadokt=$row['nama'];
        $pjekel=$row['jekel'];
        $pspdokt=$row['spid'];
        $palamat1=$row['alamat1'];
        $palamat2=$row['alamat2'];
        $pkota=$row['kota'];
        $ptelp=$row['telp'];
        $pnohp=$row['hp'];
        $pnowa=$row['nowa'];
        
        mysqli_close($cnmy);
        
        //echo $berhasil; exit;
        
        if (!empty($pnowa)) {
            
            $pmessage="<html>";
                    $pmessage .="<head>";
                            $pmessage .="<title>SEND MESSAGE</title>";
                    $pmessage .="</head>";
                    $pmessage .="<body>";
                            $pmessage .="<br/>";
                            $pmessage .="<br/>";
                    $pmessage .="</body>";
            $pmessage .="</html>";
            
            $pgenre="Bapak/Ibu";
            if ($pjekel=="L") {
                $pgenre="Bapak";
            }elseif ($pjekel=="P") {
                $pgenre="Ibu";
            }
            
            $pmessage="Dear $pgenre $pnamadokt, Silakan klik link berikut untuk melakukan verifikasi : https://whatsform.com/JUt9Mx";

            $curl = curl_init();
            //$token = "1zXCKJnzY7M18iHJg4fkZYN0AQ4W04wohahJ5c8VcymQz6R1LYN0s00U9aAven93";//
            $token = "VVxSsXvBdJHrWwO2mTPNAqrcmoKjpIHy7uaxwTiLTUVoS6An75CCrKDxKB7tmPbZ";//HUSPAN
            $payload = [
                "data" => [
                    [
                        'phone' => $pnowa,
                        'caption' => 'verifikasi', // can be null
                        'image' => 'https://ms.marvis.id/mysdm/img/logo/logo_sdm_wa.png',
                        'message' => $pmessage,
                        'secret' => true, // or true
                        'priority' => false, // or true
                    ],
                    /*[
                        'phone' => '085xxxxxx',
                        'message' => 'try message 2',
                        'secret' => false, // or true
                        'priority' => false, // or true
                    ],*/
                ]
            ];

            curl_setopt($curl, CURLOPT_HTTPHEADER,
                array(
                    "Authorization: $token",
                    "Content-Type: application/json"
                )
            );
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload) );
            //curl_setopt($curl, CURLOPT_URL, "https://tx.wablas.com/api/v2/send-bulk/text");//
            curl_setopt($curl, CURLOPT_URL, "https://console.wablas.com/api/v2/send-bulk/text");//HUSPAN
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($curl);
            curl_close($curl);

            //echo "<pre>";
            //print_r($result);
            
            $berhasil="berhasil...".$result;
        
        }else{
            $berhasil="Nomor whatsapp masih kosong";
        }
        
    }
    
    echo $berhasil;

?>


