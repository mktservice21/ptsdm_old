<?PHP
    session_start();
    include "../../config/koneksimysqli_it.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lihat Data Cek Selisih</title>
    <link rel="shortcut icon" href="../../images/icon.ico" />
    <style>
    body {font-family: Arial;}

    /* Style the tab */
    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    /* Style the buttons inside the tab */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 8px 14px;
        transition: 0.3s;
        font-size: 17px;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
    }
    </style>
</head>
<body>

    <div class="tab">
        <button class="tablinks" onclick="openCity(event, 'tab1')" id="defaultOpen">Sales SDM</button>
        <button class="tablinks" onclick="openCity(event, 'tab2')">Sales Distributor</button>
        <!--<button class="tablinks" onclick="openCity(event, 'Tokyo')">Tokyo</button>-->
    </div>

    <div id="tab1" class="tabcontent">
        <h4>Sales SDM</h4>
        <?PHP
        $idprod=$_GET['idprod'];
        $faktur=$_GET['fakturid'];
        $query = "SELECT  s.*, c.nama nama_cabang, a.nama nama_area, e.nama nama_ecust, i.nama nama_icust, p.nama nama_produk 
            FROM  MKT.mr_sales2 s 
            LEFT JOIN MKT.icabang c on s.icabangid=c.icabangid
            LEFT JOIN MKT.iarea a on s.icabangid=a.icabangid and s.areaid=a.areaid
            LEFT JOIN MKT.ecust as e on s.ecustid=e.ecustid and s.initialecabang=e.cabangid
            LEFT JOIN MKT.icust i on i.iCustId=s.icustid and s.icabangid=i.icabangid
            LEFT JOIN MKT.iproduk p on s.iprodid=p.iprodid
            WHERE s.fakturid='$faktur' AND s.iprodid='$idprod'";
        $tampil = mysqli_query($cnit, $query);
        $r = mysqli_fetch_array($tampil);
        
        $dist = $r['distId'];
        $cabang = $r['icabangid'];
        $area = $r['areaid'];
        $icustid = $r['icustid'];
        $ecustid = $r['ecustid'];
        $initial = $r['initial'];
        $faktur = $r['fakturid'];
        $tgljual = $r['tgljual'];
        $harga = $r['hna'];
        $qty = $r['qty'];
        
        function ComboDistributor($sel) {
            include "../../config/koneksimysqli_it.php";
            $cquery = "select Distid, nama from MKT.distrib0 order by nama";
            $ctampil = mysqli_query($cnit, $cquery);
            echo "<option value=''>--Pilih--</option>";
            while ($cr = mysqli_fetch_array($ctampil)) {
                if ($cr['Distid']==$sel)
                    echo "<option value='$cr[Distid]' selected>$cr[nama]</option>";
                else
                    echo "<option value='$cr[Distid]'>$cr[nama]</option>";
            }
        }
        
        function ComboCabang($sel) {
            include "../../config/koneksimysqli_it.php";
            $cquery = "select iCabangId, nama from MKT.icabang order by nama";
            $ctampil = mysqli_query($cnit, $cquery);
            echo "<option value=''>--Pilih--</option>";
            while ($cr = mysqli_fetch_array($ctampil)) {
                if ($cr['iCabangId']==$sel)
                    echo "<option value='$cr[iCabangId]' selected>$cr[nama]</option>";
                else
                    echo "<option value='$cr[iCabangId]'>$cr[nama]</option>";
            }
        }
        
        function ComboArea($sel, $cabang) {
            if (!empty($cabang)) $cabang = " Where iCabangId='$cabang'";
            include "../../config/koneksimysqli_it.php";
            $cquery = "select areaId, nama from MKT.iarea $cabang order by nama";
            $ctampil = mysqli_query($cnit, $cquery);
            echo "<option value=''>--Pilih--</option>";
            while ($cr = mysqli_fetch_array($ctampil)) {
                if ($cr['areaId']==$sel)
                    echo "<option value='$cr[areaId]' selected>$cr[nama]</option>";
                else
                    echo "<option value='$cr[areaId]'>$cr[nama]</option>";
            }
        }
        ?>
        <table>
            <tr>
                <td>Cabang</td><td>:</td>
                <td>
                    <select name="icabang" id="icabang">
                    <?PHP ComboCabang($cabang) ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Area</td><td>:</td>
                <td>
                    <select name="iarea" id="iarea">
                    <?PHP ComboArea($area, $cabang) ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Distributor</td><td>:</td>
                <td>
                    <select name="idist" id="idist">
                    <?PHP ComboDistributor($dist) ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Initial</td><td>:</td>
                <td>
                    <input type="text" nama="iinitial" id="iinitial" value="<?PHP echo $initial; ?>">
                </td>
            </tr>
            <tr>
                <td>ICust</td><td>:</td>
                <td>
                    
                </td>
            </tr>
            <tr>
                <td>ECust</td><td>:</td>
                <td>
                    
                </td>
            </tr>
            <tr>
                <td>ECabang Id</td><td>:</td>
                <td>
                    
                </td>
            </tr>
            <tr>
                <td>Initial ECabang</td><td>:</td>
                <td>
                    
                </td>
            </tr>
            <tr>
                <td>Divisi</td><td>:</td>
                <td>
                    
                </td>
            </tr>
            <tr>
                <td>Produk</td><td>:</td>
                <td>
                    
                </td>
            </tr>
            <tr>
                <td>FakturId</td><td>:</td>
                <td>
                    <input type="text" nama="ifaktur" id="ifaktur" value="<?PHP echo $faktur; ?>">
                </td>
            </tr>
            <tr>
                <td>Tgl. Jual</td><td>:</td>
                <td>
                    <input type="text" nama="itgljual" id="itgljual" value="<?PHP echo $tgljual; ?>">
                </td>
            </tr>
            <tr>
                <td>Harga</td><td>:</td>
                <td>
                    <input type="text" nama="iharga" id="iharga" value="<?PHP echo $harga; ?>">
                </td>
            </tr>
            <tr>
                <td>QTY</td><td>:</td>
                <td>
                    <input type="text" nama="iqty" id="iqty" value="<?PHP echo $qty; ?>">
                </td>
            </tr>
            
            <tr>
                <td colspan="3"></td>
            </tr>
        </table>
    </div>

    
    
    
    <div id="tab2" class="tabcontent">
        <h3>Sales Distributor</h3>

    </div>

    <div id="Tokyo" class="tabcontent">

    </div>
    
    
    

    <script>
        function openCity(evt, cityName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }

        // Get the element with id="defaultOpen" and click on it
        document.getElementById("defaultOpen").click();
    </script>
     
</body>
</html> 
