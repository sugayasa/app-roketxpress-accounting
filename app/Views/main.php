<!DOCTYPE html>
<html lang="en">
    <head>
		<script async src="https://www.googletagmanager.com/gtag/js?id=G-GGS1C83JS9"></script>
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'G-GGS1C83JS9');
		</script>
        <meta http-equiv="Content-Type" content="text/html;">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?=APP_NAME?></title>

        <link rel="icon" href="<?=BASE_URL_ASSETS_IMG?>logo-single.ico" type="image/x-icon"/>
        <link rel="stylesheet" href="<?=BASE_URL_ASSETS_CSS?>main-loader.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?=BASE_URL_ASSETS_CSS?>bootstrap.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?=BASE_URL_ASSETS_CSS?>style.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?=BASE_URL_ASSETS_CSS?>helper.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?=BASE_URL_ASSETS_CSS?>nprogress.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?=BASE_URL_ASSETS_CSS?>font-awesome.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?=BASE_URL_ASSETS_CSS?>plugins.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?=BASE_URL_ASSETS_CSS?>uploadfile.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?=BASE_URL_ASSETS_CSS?>material-design-iconic-font.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?=BASE_URL_ASSETS_CSS?>toastr.min.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="<?=BASE_URL_ASSETS_CSS?>tagify.css" rel="stylesheet" type="text/css">
	</head>
	<body id="mainbody">
		<div class="main-wrapper">
			<div class="content-body m-0 p-0">
				<div class="login_wrapper" style="margin-top:0">
					<div class="animate form login_form">
					  <section class="login_content" id="center_content">
						  <h3><center><?=APP_NAME?></center></h3>
						  <center>
							<img src="<?=BASE_URL_ASSETS_IMG?>loader.gif"/>
							<p id="loadtext">Checking session...</p>
						  </center>
					  </section>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" id="lastUpdateElemLocalStorageChange" name="lastUpdateElemLocalStorageChange" value="">
	</body>
	<script src="<?=BASE_URL_ASSETS_JS?>ubid-0.1.2.min.js"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>define.js?<?=date('YmdHis')?>"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>modernizr.js"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>jquery.min.js"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>popper.min.js"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>bootstrap.min.js"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>tippy4.min.js"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>moment.min.js"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>nprogress.js"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>perfect-scrollbar.min.js"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>chart.min.js"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>echarts.min.js"></script>
	<script src="<?=BASE_URL_ASSETS_JS?>session-controller.js?<?=date('YmdHis')?>"></script>
</html>