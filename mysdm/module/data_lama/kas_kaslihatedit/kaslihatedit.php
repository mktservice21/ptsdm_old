<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<script> window.onload = function() { document.getElementById("karyawanid").focus(); } </script>

<div class="">

    <div class="page-title"><div class="title_left"><h3>LIHAT/EDIT/DELETE KAS KECIL</h3></div></div><div class="clearfix"></div>
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
                
                $karyawanid= $_SESSION['IDCARD'];
                $nama= "";
                $tanggal = date('d');
                $bulan = date('m');
		$tahun = date('Y');
		$tahun_1 = $tahun - 1;
		$tahun_2 = $tahun + 1;
                
		$disabled_="";
		$checked_="";
		$kodeid="";
		$aktiv1="";
		$aktiv2="";
		$jumlah=0;
		$entrymode="";
                $kasid ="";
                if (isset($_GET['entry'])) {
                    $entrymode=$_GET['entry'];
                }
                if (isset($_GET['id'])) {
                    $kasid=trim($_GET['id']);
                }
		if ($entrymode=='D') {
			$disabled_ = "disabled";
		}
                
		$periode1="";
		$periode2="";
		$set_focus="";
		$lampiran="";
                
		echo "<table>";

		
		$query = "select kodeid,nama from hrd.bp_kode where per='' or per='D' order by kodeid"; 
		$result = mysqli_query($cnmy, $query);
		$num_results = mysqli_num_rows($result);
		echo "<td align=right>Kode :</td>";
		echo "<td><select  class='soflow' name=\"kodeid\" id=\"kodeid\" $disabled_>";
                echo '<option value="blank"></option>';
		for ($i=0; $i < $num_results; $i++)
		{
			$row = mysqli_fetch_array($result);
			$str_ = $row['nama'];		
			if ($kodeid == $row['kodeid']) {
				echo '<option selected="selected" value="'.$row['kodeid'].'">'.$row['kodeid'].' - '.$str_.'</option>';		
			} else {
				echo '<option value="'.$row['kodeid'].'">'.$row['kodeid'].' - '.$str_.'</option>';		
			}
		}
		echo '</select></td>';	  
		echo "</tr>";	

		echo '<tr>';
		echo '<td align=right>Tgl Transaksi :</td>';

			echo "<td><select name='tanggal' id='tanggal' $disabled_>";
			for ($i=1; $i<32; $i++) {
				$i_ = substr('0'.$i,-2);		
				if ($i == $tanggal) {
					echo "<option selected='selected' value='$i_'>$i_</option>";	
				} else {
					echo "<option value='$i_'>$i_</option>";					
				}
			}		
			echo '</select>';
		
			echo "&nbsp;&nbsp;<select name='bulan' id='bulan' $disabled_>";
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
	
			echo "&nbsp;&nbsp;<select name='tahun' id='tahun' $disabled_>";
			echo "<option value='$tahun_1'>$tahun_1</option>";
			echo "<option selected='selected' value='$tahun'>$tahun</option>";
			echo "<option value='$tahun_2'>$tahun_2</option>";
			echo '</select>';		
			echo '</td>';
				
		echo '</tr>';
		echo "</table>";
	   
		echo "<input class='soflow' type=hidden id='set_focus' name='set_focus' value=".$set_focus."></td>";	  	  
		echo "<SCRIPT LANGUAGE='javascript'>\n";
		echo "   set_focus('$set_focus');\n";
		echo "</SCRIPT>\n";
	  
	}  // if (empty($_SESSION['srid'])) 
	echo "<br>";

?>
                    
                    <button type='button' class='btn btn-success btn-sm' onclick="disp_confirm('')">Search</button>
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