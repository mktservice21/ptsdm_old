<?PHP
    session_start();
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "<center>Maaf, Anda Harus Login Ulang.<br>"; exit;
    }

    $bulan_array=array(1=> "Januari", "Februari", "Maret", "April", "Mei", 
        "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember");

    $hari_array = array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );


    include "config/koneksimysqli.php";
?>

<HTML>
<HEAD>
    <title>Detail Weekly Plan</title>
    <meta http-equiv="Expires" content="Mon, 01 Sep 2030 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <?php header("Cache-Control: no-cache, must-revalidate"); ?>
    <link rel="shortcut icon" href="images/icon.ico" />
</HEAD>


<BODY>

<?PHP
$pidinput=$_GET['brid'];
$query = "select a.*, b.nama as nama_ket from hrd.dkd_new0 as a LEFT JOIN
    hrd.ket as b on a.ketid=b.ketId WHERE a.idinput='$pidinput'";
$tampil=mysqli_query($cnmy, $query);
$row=mysqli_fetch_array($tampil);
$ntgl=$row['tanggal'];

$ntanggal = date('l d F Y', strtotime($ntgl));

$xhari = $hari_array[(INT)date('w', strtotime($ntgl))];
$xtgl= date('d', strtotime($ntgl));
$xbulan = $bulan_array[(INT)date('m', strtotime($ntgl))];
$xthn= date('Y', strtotime($ntgl));

$ptanggal="$xhari, $xtgl $xbulan $xthn";

$nnamaket=$row['nama_ket'];
$ncompl=$row['compl'];
$naktivitas=$row['aktivitas'];

?>

<div>Weekly Plan</div>
<div><?PHP echo $ptanggal; ?></div>
<hr/>
<b><u>Activity</u></b><br/>
<table>
    <tr>
        <td nowrap>Keperluan</td><td> : </td><td nowrap><?PHP echo $nnamaket; ?></td>
    </tr>
    <tr>
        <td nowrap>Compl.</td><td> : </td><td nowrap><?PHP echo $ncompl; ?></td>
    </tr>
    <tr>
        <td nowrap>Aktivitas</td><td> : </td><td><?PHP echo $naktivitas; ?></td>
    </tr>
</table>

<br/>
<b><u>Visit</u></b><br/>

<table id='dtabel' class='table table-striped table-bordered' width='100%'>
<thead>
    <tr>
        <th width='5px' align='center'>No</th>
        <th width='10px' align='center'>Jenis</th>
        <th width='200px' align='center'>Nama Dokter</th>
        <th width='200px' align='center'>Notes</th>
    </tr>
</thead>
<tbody>
    <?PHP
        $no=1;
        $query = "SELECT a.*, b.namalengkap as nama_dokter, b.gelar, b.spesialis 
            FROM hrd.dkd_new1 as a
            LEFT JOIN dr.masterdokter as b on a.dokterid=b.id 
            WHERE a.idinput='$pidinput'";
        $tampild=mysqli_query($cnmy, $query);
        while ($nrd= mysqli_fetch_array($tampild)) {
            $pjenis=$nrd['jenis'];
            $pdokterid=$nrd['dokterid'];
            $pnmdokt=$nrd['nama_dokter'];
            $pgelardokt=$nrd['gelar'];
            $pspesdokt=$nrd['spesialis'];
            $pnotes=$nrd['notes'];
            $psaran="";
            $pnmjenis='';
            if ($pjenis=="JV") $pnmjenis='Join Visit';
            elseif ($pjenis=="EC") $pnmjenis='Extra Call';

            $pnmdokt_=$pnmdokt." (".$pgelardokt.") ".$pspesdokt;

            echo "<tr>";
            echo "<td nowrap>$no</td>";
            echo "<td nowrap>$pjenis</td>";
            echo "<td nowrap>$pnmdokt_</td>";
            echo "<td >$pnotes</td>";
            echo "</tr>";

            
            $no++;

        }
    ?>
</tbody>
</table>
<br/>

</BODY>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<link href="css/stylenew.css" rel="stylesheet" type="text/css" />

<style>
    body {
        font-family: "Times New Roman", Times, serif;
        font-size: 14px;
        border: 0px solid #000;
    }

    #dtabel {
        color: #000;
        font-family: Helvetica, Arial, sans-serif;
        width: 100%;
        border-collapse:
        collapse; border-spacing: 0;
        font-size: 11px;
        border: 1px solid #000;
    }
    #dtabel th {
        font-size: 13px;
    }
    #dtabel td { 
        font-size: 12px;
        padding:5px;
    }
    #dtabel td, #dtabel th {
            border: 1px solid #000; /* No more visible border */
            height: 28px;
            transition: all 0.3s;  /* Simple transition for hover effect */
            padding: 5px;
        }
</style>
</HTML>

<?PHP
hapusdata:
    mysqli_close($cnmy);
?>