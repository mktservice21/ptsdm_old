<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<script> window.onload = function() { document.getElementById("karyawanid").focus(); } </script>

<div class="">

    <div class="page-title"><div class="title_left"><h3>LAPORAN KAS KECIL</h3></div></div><div class="clearfix"></div>
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
		echo '<td>Periode Kas Kecil</td>';
		echo '<td>:</td>';
		echo '<td><select name="tanggal1" id="tanggal1">';
		for ($i=1; $i<32; $i++) {
			$i_ = substr('0'.$i,-2);		
			if ($i == $tanggal1) {
				echo "<option selected='selected' value='$i_'>$i_</option>";	
			} else {
				echo "<option value='$i_'>$i_</option>";					
			}
		}		
		echo '</select>';
		echo '</td>';

		echo '<td><select name="bulan1" id="bulan1">';
		for ($i=0; $i<12; $i++) {
			$j = '0'.ltrim(strval($i+1));
			$j = substr($j,-2,2);
			$bln_ = nama_bulan($j);
			if ($j == $bulan) {
				echo "<option selected='selected' value='$j'>$bln_</option>";	
			} else {
				echo "<option value='$j'>$bln_</option>";					
			}
		}		
		echo '</select>';
		echo '</td>';		
		echo '<td><select name="tahun1" id="tahun1">';
		echo "<option value='$tahun_3'>$tahun_3</option>";
		echo "<option value='$tahun_1'>$tahun_1</option>";
		echo "<option selected='selected' value='$tahun'>$tahun</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';		
		echo '</td>';	
		
		echo '<td align=right>&nbsp;s/d : </td>';
		echo '<td><select name="tanggal2" id="tanggal2">';
		for ($i=1; $i<32; $i++) {
			$i_ = substr('0'.$i,-2);		
			if ($i == $tanggal) {
				echo "<option selected='selected' value='$i_'>$i_</option>";	
			} else {
				echo "<option value='$i_'>$i_</option>";					
			}
		}		
		echo '</select>';
		echo '</td>';

		echo '<td><select name="bulan2" id="bulan2">';
		for ($i=0; $i<12; $i++) {
			$j = '0'.ltrim(strval($i+1));
			$j = substr($j,-2,2);
			$bln_ = nama_bulan($j);
			if ($j == $bulan) {
				echo "<option selected='selected' value='$j'>$bln_</option>";	
			} else {
				echo "<option value='$j'>$bln_</option>";					
			}
		}		
		echo '</select>';
		echo '</td>';		
		echo '<td><select name="tahun2" id="tahun2">';
		echo "<option value='$tahun_3'>$tahun_3</option>";
		echo "<option value='$tahun_1'>$tahun_1</option>";
		echo "<option selected='selected' value='$tahun'>$tahun</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';		
		echo '</td>';	
		echo "</tr>";
		
		echo "<tr>";
		echo "<td>Kas Kecil (belum ditarik)</td>";
		echo "<td>:</td>";
		echo '<td><input type=checkbox name=chkFull id=chkFull value=1 checked=checked></td>';
		echo "</tr>";
		
		echo "<tr>";
		echo "<tr>";
		echo '<td>Periode Kas Kecil (belum ditarik)</td>';
		echo '<td>:</td>';
		echo '<td><select name="tanggal3" id="tanggal3">';
		for ($i=1; $i<32; $i++) {
			$i_ = substr('0'.$i,-2);		
			if ($i == $tanggal1) {
				echo "<option selected='selected' value='$i_'>$i_</option>";	
			} else {
				echo "<option value='$i_'>$i_</option>";					
			}
		}		
		echo '</select>';
		echo '</td>';

		echo '<td><select name="bulan3" id="bulan3">';
		for ($i=0; $i<12; $i++) {
			$j = '0'.ltrim(strval($i+1));
			$j = substr($j,-2,2);
			$bln_ = nama_bulan($j);
			if ($j == $bulan) {
				echo "<option selected='selected' value='$j'>$bln_</option>";	
			} else {
				echo "<option value='$j'>$bln_</option>";					
			}
		}		
		echo '</select>';
		echo '</td>';		
		echo '<td><select name="tahun3" id="tahun3">';
		echo "<option value='$tahun_3'>$tahun_3</option>";
		echo "<option value='$tahun_1'>$tahun_1</option>";
		echo "<option selected='selected' value='$tahun'>$tahun</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';		
		echo '</td>';	
		
		echo '<td align=right>&nbsp;s/d : </td>';
		echo '<td><select name="tanggal4" id="tanggal4">';
		for ($i=1; $i<32; $i++) {
			$i_ = substr('0'.$i,-2);		
			if ($i == $tanggal) {
				echo "<option selected='selected' value='$i_'>$i_</option>";	
			} else {
				echo "<option value='$i_'>$i_</option>";					
			}
		}		
		echo '</select>';
		echo '</td>';

		echo '<td><select name="bulan4" id="bulan4">';
		for ($i=0; $i<12; $i++) {
			$j = '0'.ltrim(strval($i+1));
			$j = substr($j,-2,2);
			$bln_ = nama_bulan($j);
			if ($j == $bulan) {
				echo "<option selected='selected' value='$j'>$bln_</option>";	
			} else {
				echo "<option value='$j'>$bln_</option>";					
			}
		}		
		echo '</select>';
		echo '</td>';		
		echo '<td><select name="tahun4" id="tahun4">';
		echo "<option value='$tahun_3'>$tahun_3</option>";
		echo "<option value='$tahun_1'>$tahun_1</option>";
		echo "<option selected='selected' value='$tahun'>$tahun</option>";
		echo "<option value='$tahun_2'>$tahun_2</option>";
		echo '</select>';		
		echo '</td>';	
		echo "</tr>";
		
		
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