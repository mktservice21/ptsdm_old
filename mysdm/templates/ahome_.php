<?PHP
if ($_SESSION['IDCARD']=="0000000367" OR $_SESSION['IDCARD']=="0000001372") {
?>
<section class="content-header">
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>

<style>
     #divlink { 
         font-size:20px;
     }
     #divlink2 { 
         font-size:19px;
     }
     #divlink a {
         color:white;
     }
     #divlink a:hover {
         opacity: 0.7;
     }
     #divlink2 a {
         color:white;
     }
     #divlink2 a:hover {
         opacity: 0.7;
     }
</style>

<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        
        <!--
        <div class="col-lg-3 col-xs-6">
            
            <div class="small-box bg-aqua">
                <div class="inner">
                    <div id="divlink">
                        <a href="?module=appdirca&idmenu=237&act=236" class="vlink">
                            Approve<br/>Cash Advance
                        </a>
                    </div>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="?module=appdirca&idmenu=237&act=236" class="small-box-footer">view <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        
        <div class="col-lg-3 col-xs-6">
            
            <div class="small-box bg-yellow">
                <div class="inner">
                    <div id="divlink">
                        <a href="?module=appdirblk&idmenu=238&act=236" class="vlink">
                            Approve<br/>Biaya Luar Kota
                        </a>
                    </div>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="?module=appdirblk&idmenu=238&act=236" class="small-box-footer">view <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        
        <div class="col-lg-3 col-xs-6">
            
            <div class="small-box bg-red">
                    <div class="inner">
                    <div id="divlink">
                        <a href="?module=appdirrutin&idmenu=239&act=236" class="vlink">
                            Approve<br/>Biaya Rutin
                        </a>
                    </div>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="?module=appdirrutin&idmenu=239&act=236" class="small-box-footer">view <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        -->
        
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <div id="divlink">
                        <a href="?module=appdirpd&idmenu=240&act=236" class="vlink">
                            Approve<br/>Permintaan Dana
                        </a>
                    </div>

                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="?module=appdirpd&idmenu=240&act=236" class="small-box-footer">view <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        
        
        
        
        <!-- sementara
        <div class="col-lg-3 col-xs-6">
            <!-- small box 
            <div class="small-box bg-aqua">
                <div class="inner">
                    <div id="divlink">
                        <a href="?module=lapbudgetmarketingvsrealisasi&idmenu=243&act=152" class="vlink">
                            Realisasi<br/>Biaya Marketing vs Budget
                        </a>
                    </div>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="?module=lapbudgetmarketingvsrealisasi&idmenu=243&act=152" class="small-box-footer">view <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        -->
		
        <!--
        <div class="col-lg-3 col-xs-6">
            
            <div class="small-box bg-yellow">
                <div class="inner">
                    <div id="divlink">
                        <a href="?module=lapbudgetmarketing&idmenu=219&act=152" class="vlink">
                            Realisasi<br/>Budget Marketing
                        </a>
                    </div>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="?module=lapbudgetmarketing&idmenu=219&act=152" class="small-box-footer">view <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        
        <div class="col-lg-3 col-xs-6">
            
            <div class="small-box bg-red">
                    <div class="inner">
                    <div id="divlink">
                        <a href="?module=glrealbiayamkt&idmenu=234&act=188" class="vlink">
                             Realisasi<br/>Biaya Marketing
                        </a>
                    </div>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="?module=glrealbiayamkt&idmenu=234&act=188" class="small-box-footer">view <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        
        <div class="col-lg-3 col-xs-6">
            
            <div class="small-box bg-green">
                <div class="inner">
                    <div id="divlink2">
                        <a href="?module=glrealbiayamktcab&idmenu=235&act=188" class="vlink">
                            Realisasi<br/>Biaya Marketing Cabang
                        </a>
                    </div>

                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="?module=glrealbiayamktcab&idmenu=235&act=188" class="small-box-footer">view <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        -->
        
        
        
        <!--
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>&nbsp;</h3>

                    <p>Realisasi Budget Marketing</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="?module=lapbudgetmarketing&idmenu=219&act=152" class="small-box-footer">view <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        
        
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>&nbsp;</h3>

                    <p>Realisasi Biaya Marketing</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="?module=glrealbiayamkt&idmenu=234&act=188" class="small-box-footer">view <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        
        
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                    <div class="inner">
                    <h3>&nbsp;</h3>

                    <p>Realisasi Biaya Marketing Cabang</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="?module=glrealbiayamktcab&idmenu=235&act=188" class="small-box-footer">view <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        -->
        
        
    </div>
</section>

<?PHP
}
?>