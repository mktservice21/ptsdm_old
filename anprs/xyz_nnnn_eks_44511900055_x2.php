<?PHP
session_start();
include("../mysdm/config/koneksimysqli.php");
$ppilihrpt="";
$iidgrp=$_GET['xyz'];
$iidcard=$_GET['nomxyz'];


?>
<html>
<head>
    <title>pilih data</title>
    
    <meta http-equiv="Expires" content="Mon, 01 Mei 2050 1:00:00 GMT">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="shortcut icon" href="../mysdm/images/icon.ico" />

    <?php header("Cache-Control: no-cache, must-revalidate"); ?>

	
	<link href="../mysdm/css/konten.css" rel="stylesheet">
	
    <!-- Bootstrap -->
    <link href="../mysdm/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="../mysdm/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../mysdm/vendors/nprogress/nprogress.css" rel="stylesheet">
	
	
    <!-- Datatables -->
    <link href="../mysdm/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

    <!-- Datatables -->
    <link href="../mysdm/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
    <link href="../mysdm/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

    <!-- Datatables -->
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <!-- jQuery -->
    <script src="../mysdm/vendors/jquery/dist/jquery.min.js"></script>
	
        
</head>
<body class="nav-md">

<div class="container body">
    <div class="main_container">
    
        <div class="right_col" role="main">    
            
            <div class="">
            
                <div class="row">
                
                    <div class="page">

                        <div id='n_content'>

                            <table class='tjudul' width='100%'>
                                    <?PHP 
                                            echo "<tr><td width='150px'><b>Approve DATA</b></td></tr>";
                                    ?>
                            </table>
                            <br/>&nbsp;


                            <form method='POST' action='' id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>
                                <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $iidcard; ?>' Readonly>
                                <input type='hidden' class='form-control' id='e_idgroup_lnk' name='e_idgroup_lnk' value='<?PHP echo $iidgrp; ?>' Readonly>
                                <table id='datatable2' class='table table-striped table-bordered' width="100%" border="1px solid black">
                                    <thead>
                                        <tr>
                                            <th align="center" width="5">No</th>
                                            <th align="center" width="5"></th>
                                            <th align="center">No BR/Divisi</th>
                                            <th align="center">Jumlah</th>
                                            <th align="center">Pengajuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?PHP
                                        $no=1;
                                        $query = "select a.idinput, a.nodivisi, FORMAT(a.jumlah,0,'de_DE') as jumlah, a.divisi, a.tgl_dir, a.tgl_dir2, b.stsapvdir from dbmaster.t_suratdana_br a "
                                                        . " JOIN dbmaster.t_suratdana_br_link b on a.idinput=b.idinput WHERE "
                                                        . " b.idgroup='$iidgrp' AND b.userid='$iidcard' AND IFNULL(a.stsnonaktif,'')<>'Y' AND "
                                                        . " IFNULL(b.hapus,'')<>'Y'";
                                        $tampil= mysqli_query($cnmy, $query);
                                        $ketemu= mysqli_num_rows($tampil);
                                        if ($ketemu>0) {
                                            while ($row= mysqli_fetch_array($tampil)) {
                                                $idno=$row['idinput'];
                                                $pnodivisi=$row['nodivisi'];
                                                $pjumlah=$row['jumlah'];
                                                $pstsapvdir=$row['stsapvdir'];
                                                $pdivisi=$row['divisi'];
                                                if (empty($pdivisi)) $pdivisi="ETHICAL";

                                                $ptgldir = $row["tgl_dir"];
                                                $ptgldir2 = $row["tgl_dir2"];

                                                $apvdir="";
                                                $apvdir2="";

                                                if (!empty($ptgldir) AND $ptgldir <> "0000-00-00") $apvdir=date("d F Y, h:i:s", strtotime($ptgldir));
                                                if (!empty($ptgldir2) AND $ptgldir2 <> "0000-00-00") $apvdir2=date("d F Y, h:i:s", strtotime($ptgldir2));

                                                $cekbox = "<input type=checkbox value='$idno' name=chkbox_br[] checked>";

                                                if ($pstsapvdir=="Y") {
                                                        if (!empty($apvdir) AND empty($apvdir2)) {
                                                                $cekbox="<a href='#' class='btn btn-danger btn-xs' data-toggle='modal' "
                                                                        . "onClick=\"ProsesDataUnApproveFIN('unapprove', '$idno')\"> "
                                                                        . "unapprove</a>";
                                                        }
                                                }

                                                echo "<tr>";
                                                echo "<td >$no</td>";
                                                echo "<td nowrap>$cekbox</td>";
                                                echo "<td nowrap>$pnodivisi</td>";
                                                echo "<td nowrap align='right'>$pjumlah</td>";
                                                echo "<td nowrap>$pdivisi</td>";
                                                echo "</tr>";

                                                $no++;
                                            }
                                        }

                                    ?>
                                    </tbody>
                                </table>

                                <?PHP
                                include "xyz_pilih_ntdn.php";
                                ?>

                            </form>

                        </div>

                    </div>

                </div>
                
            </div>
            
        </div>
        
    </div>
</div>
        
<!-- Bootstrap -->
<script src="../mysdm/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Datatables -->
<script src="../mysdm/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../mysdm/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="../mysdm/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="../mysdm/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
<script src="../mysdm/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="../mysdm/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="../mysdm/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="../mysdm/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="../mysdm/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="../mysdm/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="../mysdm/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
<script src="../mysdm/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
<script src="../mysdm/vendors/jszip/dist/jszip.min.js"></script>
<script src="../mysdm/vendors/pdfmake/build/pdfmake.min.js"></script>
<script src="../mysdm/vendors/pdfmake/build/vfs_fonts.js"></script>

        
        
<style>
    .tjudul {
        font-family: Georgia, serif;
        font-size: 25px;
        margin-left:10px;
        margin-right:10px;
    }
    .tjudul td {
        padding: 4px;
    }
    #myBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        font-size: 18px;
        border: none;
        outline: none;
        background-color: red;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 4px;
        opacity: 0.5;
    }

    #myBtn:hover {
        background-color: #555;
    }

    #n_content {
        color:#000;
        font-family: "Arial";
        margin: 20px;
        /*overflow-x:auto;*/
    }

    .divnone {
        display: none;
    }
    #datatable2, #datatable3 {
        color:#000;
        font-family: "Arial";
    }
    #datatable2 th, #datatable3 th {
        font-size: 25px;
    }
    #datatable2 td, #datatable3 td { 
        font-size: 20px;
    }

    /* Extra small devices (phones, 600px and down) */
    @media only screen and (max-width: 600px) {
      .page {

            }
    }

    /* Small devices (portrait tablets and large phones, 600px and up) */
    @media only screen and (min-width: 600px) {

    }

    /* Medium devices (landscape tablets, 768px and up) */
    @media only screen and (min-width: 768px) {

    } 

    /* Large devices (laptops/desktops, 992px and up) */
    @media only screen and (min-width: 992px) {
      .page {

            }
    } 

    /* Extra large devices (large laptops and desktops, 1200px and up) */
    @media only screen and (min-width: 1200px) {
      .page {

            }
    }


</style>


        
	<script>
		function ProsesDataUnApproveFIN(ket, cekbr){
			//alert(ket+", "+cekbr);
			var cmt = confirm('Apakah akan melakukan proses unapprove ...?');
			if (cmt == false) {
				return false;
			}
			var allnobr = "";
			
			if (cekbr=="") {
				alert("Tidak ada data yang diproses...!!!");
				return false;
			}
			
			allnobr="('"+cekbr+"')";
			
			var txt;
			var ekaryawan=document.getElementById('e_idkaryawan').value;
			var eidgrplnk=document.getElementById('e_idgroup_lnk').value;
			
			var myurl = window.location;
			var urlku = new URL(myurl);
			var module = urlku.searchParams.get("module");
			var idmenu = urlku.searchParams.get("idmenu");
			
			$.ajax({
				type:"post",
				url:"aksi_ansxyz.php?module="+module+"&idmenu="+idmenu+"&act="+ket,
				data:"ket=unapprove"+"&unobr="+allnobr+"&ukaryawan="+ekaryawan+"&ketrejpen="+txt+"&uidgrplnk="+eidgrplnk,
				success:function(data){
					location.reload();
					alert(data);
				}
			});
			
			
		}
	</script>
    
    
</body>
</html>

<?PHP
mysqli_close($cnmy);
?>