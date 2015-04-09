<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ReflectionBrowser (<?php echo $this->runtimeName(); ?>)</title>
	<link href="template/css/bootstrap.min.css" rel="stylesheet">
	<link href="template/css/styles.css" rel="stylesheet">
	<link href="template/css/reflectionbrowser.css" rel="stylesheet">
</head>
<body>
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="<?php echo $this->scriptURI(); ?>">Reflection<span>Browser</span></a>
			</div>
		</div>
	</nav>
	<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
		<?php echo $this->navigationItems(); ?>
		<?php echo $this->attributions(); ?>
	</div>
	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
		<div class="row">
			<?php echo $this->breadcrumbItems(); ?>
		</div>

    <?php echo $this->pageContent(); ?>
  </div>
  <script src="template/js/jquery.min.js"></script>
  <script src="template/js/bootstrap.min.js"></script>
</body>
</html>
