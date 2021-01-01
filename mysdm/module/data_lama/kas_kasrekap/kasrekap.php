<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<script> window.onload = function() { document.getElementById("karyawanid").focus(); } </script>

<div class="">

    <div class="page-title"><div class="title_left"><h3>LAPORAN KAS KECIL PER TAHUN</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $aksi="eksekusi3.php";
        ?>
        <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left' target="_blank">
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>

                    
<?php
    include("config/common.php");
   include "config/koneksimysqli.php";
   
   if (empty($_SESSION['IDCARD'])) {
      echo 'not authorized';
      exit;
   } else {	
		$srid = $_SESSION['USERID'];
		$srnama = $_SESSION['NAMALENGKAP'];
		$sr_id = substr('0000000000'.$srid,-10);
		$userid = $_SESSION['IDCARD'];
                
		$tahun = date('Y');
		$tahun_3 = $tahun - 2;
		$tahun_1 = $tahun - 1;
		$tahun_2 = $tahun + 1;
		$bulan = date('m');
		if ($bulan=="") {
			$bulan = date('m');
		}
                $tanggal = date('t');
		if ($tanggal=="") {
			$tanggal = date('t');
		}
		$tanggal1 = start_of_month('d'); 
		if ($tanggal1=="") {
			$tanggal1 = start_of_month('d'); 
		}
                
		
                
		echo "<table>";
		echo "<tr>";
		echo '<td align=right>Periode : </td>';
		echo '<td><select name="tahun1" id="tahun1">';
		echo "<option value='$tahun_1'>$tahun_1</option>";
		echo "<option selected='selected' value='$tahun'>$tahun</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';
		echo '</td>';
		echo '</tr>';
		
		
		echo "</table>";
                $set_focus="";
		echo "<input class='soflow' type=hidden id='set_focus' name='set_focus' value=".$set_focus."></td>";	  	  
		echo "<SCRIPT LANGUAGE='javascript'>\n";
		echo "   set_focus('$set_focus');\n";
		echo "</SCRIPT>\n";
	  
	}  // if (empty($_SESSION['srid'])) 
	echo "<br>";

?>
                    
                    <button type='button' class='btn btn-success btn-sm' onclick="disp_confirm('')">Search</button>
                    <button type='button' class='btn btn-danger btn-sm' onclick="disp_confirm('excel')">Excel</button>
                </div>
            </div>
        </form>

    </div>
    <!--end row-->
</div>
<style>
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
    table tr, td, th {
        padding : 3px;
    }
</style>
<script>
    function disp_confirm(pText)  {
        if (pText == "excel") {
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=excel"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }else{
            document.getElementById("demo-form2").action = "<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]&ket=bukan"; ?>";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    }
</script>