<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<script> window.onload = function() { document.getElementById("karyawanid").focus(); } </script>

        
<div class="">

    <div class="page-title"><div class="title_left"><h3>ISI KAS KECIL</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        $trg= "";
        if (isset($_GET['entry'])) {
            $entrymode=$_GET['entry'];
            if ($entrymode=="D") $trg= " target='_blank' ";
        }
        ?>
        <form id="kas00" action="kas01" method='post' <?PHP echo $trg; ?>>
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
		$ppilcoa="";
                
                if ($entrymode=="E" or $entrymode=="D") {
			$query = "select * from hrd.kas where kasid='$kasid'"; 
			$result = mysqli_query($cnmy, $query);
			$num_results = mysqli_num_rows($result);
			if ($num_results) {
				$row = mysqli_fetch_array($result);
				$kasid = $row['kasId'];					
				$nama = $row['nama'];
				$karyawanid = $row['karyawanid'];
				$periode1 = $row['periode1'];	
				$kodeid = $row['kode']; 
                                $ppilcoa=$row['coa4'];
				$aktiv1 = $row['aktivitas1'];
				$aktiv2 = $row['aktivitas2']; 
				$jumlah = $row['jumlah']; 
				$nobukti = $row['nobukti']; 
				$periode2 = $row['periode2']; 
				$user1  = $row['user1']; 	  						
				
				if ($lampiran=='Y') {
					$checked_ = "checked";
				}
			}else{
                            $entrymode = "";
                            $kasid ="";
                        }
                }
                
		echo "<table>";
		echo "<tr>";
		$query = "select karyawanId, nama from hrd.karyawan where icabangid='0000000002' or icabangid='0000000025' or icabangid='0000000001' order by nama";
		$result = mysqli_query($cnmy, $query);
		$num_results = mysqli_num_rows($result);
		echo "<td align=right>Nama Pembuat:</td>";
		echo "<td><select  class='soflow' name=\"karyawanid\" id=\"karyawanid\" $disabled_>";
		for ($i=0; $i < $num_results; $i++)
		{
			$row = mysqli_fetch_array($result);
			$str_ = $row['nama'];		
			if ($karyawanid == $row['karyawanId']) {
				echo '<option selected="selected" value="'.$row['karyawanId'].'">'.$str_.'</option>';		
			} else {
				echo '<option value="'.$row['karyawanId'].'">'.$str_.'</option>';		
			}
		}
		echo '</select></td>';	
		echo '</tr>';
		
		echo '<tr>';	  	  	  
		echo '<td align=right>Nama :</td>';	  
		echo "<td><input type=text  class='soflow' id='nama' name='nama' maxlength=35 size=37 value='$nama' $disabled_></td>";	  	  
		echo '</tr>';
                
		echo '<tr>';
                $pkodeidpil="";
		$query = "select kodeid,nama from hrd.bp_kode where per='' or per='D' order by kodeid"; 
		$result = mysqli_query($cnmy, $query);
		$num_results = mysqli_num_rows($result);
		echo "<td align=right>Kode :</td>";
		echo "<td><select  class='soflow' name=\"kodeid\" id=\"kodeid\" onchange=\"ShowCOAKode('HO', 'kodeid', 'cbcoaid');\" $disabled_>";
		for ($i=0; $i < $num_results; $i++)
		{
			$row = mysqli_fetch_array($result);
			$str_ = $row['nama'];		
			if ($kodeid == $row['kodeid']) {
                            $pkodeidpil=$row['kodeid'];
				echo '<option selected="selected" value="'.$row['kodeid'].'">'.$str_.'</option>';		
			} else {
				echo '<option value="'.$row['kodeid'].'">'.$str_.'</option>';		
			}
		}
		echo '</select></td>';	  
		echo "</tr>";
                
                
                
		echo '<tr>';
                if ($entrymode=='E' OR $ppilcoa=='') {
                }else{
                    
                    $query = "select COA4 FROM dbmaster.posting_coa_kas WHERE kodeid='$pkodeidpil'";
                    $tampils= mysqli_query($cnmy, $query);
                    $nr= mysqli_fetch_array($tampils);
                    $ppilcoa=$nr['COA4'];
                }
                
                $fil = " AND ( c.DIVISI2 = 'HO' OR RTRIM(IFNULL(c.DIVISI2,'')) in ('OTHER', '') )";
                $query = "select a.COA4, a.NAMA4 from dbmaster.coa_level4 a 
                    LEFT JOIN dbmaster.coa_level3 b on a.COA3=b.COA3
                    LEFT JOIN dbmaster.coa_level2 c on b.COA2=c.COA2
                    WHERE 1=1 $fil OR a.COA4='$ppilcoa' ";
                $query .= " ORDER BY a.COA4";
		$result = mysqli_query($cnmy, $query);
		$num_results = mysqli_num_rows($result);
		echo "<td align=right>COA :</td>";
		echo "<td><select  class='soflow' name=\"cbcoaid\" id=\"cbcoaid\" $disabled_>";
		for ($i=0; $i < $num_results; $i++)
		{
			$row = mysqli_fetch_array($result);
			$pcoanm4_ = $row['NAMA4'];		
			if ($ppilcoa == $row['COA4']) {
				echo '<option selected="selected" value="'.$row['COA4'].'">'.$row['COA4'].' - '.$pcoanm4_.'</option>';		
			} else {
				echo '<option value="'.$row['COA4'].'">'.$row['COA4'].' - '.$pcoanm4_.'</option>';		
			}
		}
		echo '</select></td>';	  
		echo "</tr>";
                
                
                
                
		
		echo '<tr>';
		echo '<td align=right>Tanggal :</td>';
		if ($entrymode=='E') {
			echo "<td><input  class='soflow' type=text id='periode1' name='periode1' maxlength=10 size=12 value='$periode1' $disabled_></td>";
		} else {
			echo "<td><select name='tanggal1' id='tanggal1' $disabled_>";
			for ($i=1; $i<32; $i++) {
				$i_ = substr('0'.$i,-2);		
				if ($i == $tanggal) {
					echo "<option selected='selected' value='$i_'>$i_</option>";	
				} else {
					echo "<option value='$i_'>$i_</option>";					
				}
			}		
			echo '</select>';
		
			echo "&nbsp;&nbsp;<select name='bulan1' id='bulan1' $disabled_>";
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
	
			echo "&nbsp;&nbsp;<select name='tahun1' id='tahun1' $disabled_>";
			echo "<option value='$tahun_1'>$tahun_1</option>";
			echo "<option selected='selected' value='$tahun'>$tahun</option>";
			echo "<option value='$tahun_2'>$tahun_2</option>";
			echo '</select>';		
			echo '</td>';	
		}
		echo '</tr>';
		
		echo '<tr>';	  	  	  
		echo '<td align=right>Uraian :</td>';	  
		echo "<td><input  class='soflow' type=text id='aktiv1' name='aktiv1' maxlength=35 size=37 value='$aktiv1' $disabled_></td>";	  	  
		echo '</tr>';

		echo '<tr>';	  	  	  
		echo '<td></td>';	  
		echo "<td><input  class='soflow' type=text id='aktiv2' name='aktiv2' maxlength=35 size=37 value='$aktiv2' $disabled_></td>";	  	  
		echo '</tr>';
	    
		echo '<tr>';
		echo "<td align=right>Jumlah :</td>";
		echo "<td><input type=text id=\"jumlah0\" name=\"jumlah0\" onBlur=\"this.value=say_it(this.value,'jumlah')\" 
		                onfocus = \"this.select()\"
		                validchars=\"0123456789.\" onkeypress=\"return allowChars(this,event)\"
						maxlength=15 size=17 value=\"".number_format($jumlah,0)."\" $disabled_>";
		echo "<input type=text align=\"right\" id=\"jumlah\" name=\"jumlah\" value=\"$jumlah\" size=17 disabled></td>";	  	  
		echo '</tr>';
		$tahun = date('Y');	

		echo '<tr>';
		echo '<td align=right>Tgl Transaksi :</td>';
		if ($entrymode=='E') {
			echo "<td><input class='soflow' type=text id='periode2' name='periode2' maxlength=10 size=12 value='$periode2' $disabled_></td>";	 
		} else {
			echo "<td><select name='tanggal2' id='tanggal2' $disabled_>";
			for ($i=1; $i<32; $i++) {
				$i_ = substr('0'.$i,-2);		
				if ($i == $tanggal) {
					echo "<option selected='selected' value='$i_'>$i_</option>";	
				} else {
					echo "<option value='$i_'>$i_</option>";					
				}
			}		
			echo '</select>';
		
			echo "&nbsp;&nbsp;<select name='bulan2' id='bulan2' $disabled_>";
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
	
			echo "&nbsp;&nbsp;<select name='tahun2' id='tahun2' $disabled_>";
			echo "<option value='$tahun_1'>$tahun_1</option>";
			echo "<option selected='selected' value='$tahun'>$tahun</option>";
			echo "<option value='$tahun_2'>$tahun_2</option>";
			echo '</select>';		
			echo '</td>';
		}			
		echo '</tr>';
		echo "</table>";
	   
		echo "<input class='soflow' type=hidden id='set_focus' name='set_focus' value=".$set_focus."></td>";	  	  
		echo "<SCRIPT LANGUAGE='javascript'>\n";
		echo "   set_focus('$set_focus');\n";
		echo "</SCRIPT>\n";
	  
	}  // if (empty($_SESSION['srid'])) 
	echo "<br>";
	
	if ($entrymode=='D') {
   	    echo "<input class='btn btn-danger btn-sm' type=button id=cmdDel name=cmdDel value='Delete' onclick='disp_hapus(\"Hapus ?\")'>";
	    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='btn btn-default btn-sm' type=hidden id=cmdCancel name=cmdCancel value='Cancel' onclick='hapus_cancel()'>";
		echo "<input type=hidden id='kodeid' name='kodeid' value='$kodeid'>";
		echo "<input type=hidden id='periode1' name='periode1' value='$periode1'>";
	} else {
            if ($entrymode=='E')
		echo "<input class='btn btn-success btn-sm' type=button id=cmdSave name=cmdSave value='Update' onclick='disp_confirm(\"Simpan ?\")'>";
            else
		echo "<input class='btn btn-success btn-sm' type=button id=cmdSave name=cmdSave value='Save' onclick='disp_confirm(\"Simpan ?\")'>";
            
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input class='btn btn-default btn-sm' type=button id=cmdReset name=cmdReset value='Reset' onclick='click_reset()'>";
	}	  
	echo "<input type=hidden id='entrymode' name='entrymode' value='$entrymode'>";
	echo "<input type=hidden id='kasid' name='kasid' value='$kasid'>";
	echo "<input type=hidden id='jumlah_' name='jumlah_' value='$jumlah'>";
	
	
?>
                    

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
function disp_hapus(pText_)  {
    ok_ = 1;
	if (ok_) {
		var r=confirm(pText_)
		if (r==true) {
                    var myurl = window.location;
                    var urlku = new URL(myurl);
                    var module = urlku.searchParams.get("module");
                    var idmenu = urlku.searchParams.get("idmenu");
                    var act = urlku.searchParams.get("act");
			//document.write("You pressed OK!")
			document.getElementById("kas00").action = "module/data_lama/kas_isikas/kas12.php?module="+module+"&idmenu="+idmenu+"&act="+act;
			document.getElementById("kas00").submit();
			return 1;
		}
	} else {
		//document.write("You pressed Cancel!")
		return 0;
	}
}
function disp_confirm(pText_)  {
    ok_ = 1;
    
    var ukode = document.getElementById('kodeid').value;
    var ucoap = document.getElementById('cbcoaid').value;
    if (ukode=="") {
        alert("kode masih kosong...");
        return false;
    }
    if (ucoap=="") {
        alert("COA harus dipilih...");
        return false;
    }
    
    
	if (ok_) {
		var r=confirm(pText_)
		if (r==true) {
                    var myurl = window.location;
                    var urlku = new URL(myurl);
                    var module = urlku.searchParams.get("module");
                    var idmenu = urlku.searchParams.get("idmenu");
                    var act = urlku.searchParams.get("act");
			//document.write("You pressed OK!")
			document.getElementById("kas00").action = "module/data_lama/kas_isikas/kas01.php?module="+module+"&idmenu="+idmenu+"&act="+act;
			document.getElementById("kas00").submit();
			return 1;
		}
	} else {
		//document.write("You pressed Cancel!")
		return 0;
	}
}

function click_reset() {
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
	document.getElementById("kas00").action = "?module="+module+"&idmenu="+idmenu+"&act="+act;
	document.getElementById("kas00").submit();
}

function say_it(num,pDestination) {
	num = num.toString().replace(/\$|\,/g,'');
	if(isNaN(num))
		num = "0";
	document.getElementById(pDestination).value = num;
	document.getElementById("jumlah_").value = num;
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
	if(cents<10)
		cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+','+
		num.substring(num.length-(4*i+3));
	ret_ =  (((sign)?'':'-') + num + '.' + cents);
	return ret_;
}
function allowChars(oTextbox, oEvent) {
    return true;
	if(window.event) { // IE
		keynum = oEvent.keyCode;
	} else if(oEvent.which) { // Netscape/Firefox/Opera
		keynum = oEvent.which;
	}
    //alert(keynum); 
	keynum = oEvent.keyCode;
	oEvent = EventUtil.formatEvent(oEvent);
	var sValidChars = oTextbox.getAttribute("validchars");
	var sChar = String.fromCharCode(oEvent.charCode); 
	var bIsValidChar = sValidChars.indexOf(sChar) > -1;
    var mystr = oTextbox.value;
    var len_ = mystr.length;
	if (keynum==8 || keynum==37) {  //8=backspace 37=left arrow
	   mystr = mystr.substr(0,len_-1);
	   oTextbox.value = mystr;
	}
	if (keynum==36) {  //36=home
		oTextbox.value = "";
	}
	return bIsValidChar || oEvent.ctrlKey || keynum==9  || keynum==40;   //9=tab, 40=downarrow
	
	
}



function ShowCOAKode(udiv, ukode, ucoa) {
    var icar = "";
    var ikode = document.getElementById(ukode).value;
    var idiv = udiv;
    $.ajax({
        type:"post",
        url:"module/mod_br_isikasbon/viewdata.php?module=caricoaperkode",
        data:"umr="+icar+"&ukode="+ikode+"&udivi="+idiv,
        success:function(data){
            $("#"+ucoa).html(data);
        }
    });
}

</script>