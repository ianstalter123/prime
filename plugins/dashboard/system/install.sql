DROP TABLE IF EXISTS `plugin_dashboard_theme`;
CREATE TABLE IF NOT EXISTS `plugin_dashboard_theme` (
	`name` VARCHAR(255) NOT NULL,
	`value` TEXT,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

UPDATE `plugin` SET `tags`='ecommerce' WHERE `name` = 'dashboard';

-- build on Friday, 24-May-13 14:01:37 EEST theme "dashboardtheme"
INSERT INTO `plugin_dashboard_theme` (`name`, `value`) VALUES ('themeName', 'dashboardtheme');

INSERT INTO `plugin_dashboard_theme` (`name`, `value`) VALUES ('clients.html', '<!DOCTYPE HTML>
<html>
<head>
<meta charset=\"utf-8\">
<title>{$page:title}</title>
<meta name=\"keywords\" content=\"{$meta:keywords}\" />
<meta name=\"description\" content=\"{$meta:description}\" />

<link rel=\"icon\" type=\"image/vnd.microsoft.icon\" href=\"images/favicon.ico\">
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/grid-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/reset-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/fonts-icon.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/style-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/nav-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/content.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/MQueries-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />

<link href=\'http://fonts.googleapis.com/css?family=Droid+Sans:400,700\' rel=\'stylesheet\' type=\'text/css\'>
<link href=\"http://fonts.googleapis.com/css?family=Open+Sans:400,700\" rel=\"stylesheet\" type=\"text/css\">

<script src=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/scripts.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
	$(document).ready(function(e) {
		$(\'#dashboard-list .dashboard-link-index\').html(\'Dashboard\');
		var domainName = window.location.hostname;
		$(\'.numberProduct\').attr(\'href\', \'http://\'+domainName+\'/dashboard/products/\');
		$(\'.numberProduct\').removeClass(\'tpopup\');
		$(\'.quotesScreen\').attr(\'href\', \'http://\'+domainName+\'/dashboard/quotes/\');
		$(\'.quotesScreen\').removeClass(\'tpopup\');
	});
</script>


<!--[if IE]>
<script src=\"html5.js\" type=\"text/javascript\"></script>
<![endif]-->

</head>


<body class=\"seotoaster-dash\">

<!-- HEADER -->
    <header>
		<div class=\"menu\">
			<nav class=\"main\">
				{dashboardmenu}
					index				
					quotes
					sales
					clients
					orders
					products
				{/dashboardmenu}
			</nav>
      <div class=\"clear\"></div>
		</div>
	</header>
	
<div id=\"main\">
<!-- LEFT -->
	<!--div id=\"left\">
		<div class=\"leftBox\">
			<h3 class=\"blue-gradient white\">{$ header:leftBox1}</h3>
			{$ content:leftBox1}
		</div>
		<div class=\"leftBox\">
			<h3 class=\"blue-gradient white\">{$ header:leftBox2}</h3>
			{$ content:leftBox2}
		</div>
	</div-->

<!-- CONTENT -->
	<div id=\"content\">
  	{$store:clients}
	</div>
</div>

</body>
</html>');

INSERT INTO `plugin_dashboard_theme` (`name`, `value`) VALUES ('index.html', '<!DOCTYPE HTML>
<html>
<head>
<meta charset=\"utf-8\">
<title>{$page:title}</title>
<meta name=\"keywords\" content=\"{$meta:keywords}\" />
<meta name=\"description\" content=\"{$meta:description}\" />

<link rel=\"icon\" type=\"image/vnd.microsoft.icon\" href=\"images/favicon.ico\">
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/grid-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/reset-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/fonts-icon.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/style-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/nav-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/content.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/MQueries-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />

<link href=\'http://fonts.googleapis.com/css?family=Droid+Sans:400,700\' rel=\'stylesheet\' type=\'text/css\'>
<link href=\"http://fonts.googleapis.com/css?family=Open+Sans:400,700\" rel=\"stylesheet\" type=\"text/css\">

<script src=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/scripts.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
	$(document).ready(function(e) {
		$(\'#dashboard-list .dashboard-link-index\').html(\'Dashboard\');
		$(\'.numberProduct\').attr(\'href\',\'{$website:url}\'+\'dashboard/products/\');
		$(\'.numberProduct\').removeClass(\'tpopup\');
		$(\'.quotesScreen\').attr(\'href\', \'{$website:url}\'+\'dashboard/quotes/\');
		$(\'.quotesScreen\').removeClass(\'tpopup\');
	});
</script>


<!--[if IE]>
<script src=\"html5.js\" type=\"text/javascript\"></script>
<![endif]-->

</head>


<body class=\"seotoaster-dash\">

<!-- HEADER -->
    <header>
		<div class=\"menu\">
			<nav class=\"main\">
				{dashboardmenu}
					index				
					quotes
					sales
					clients
					orders
					products
				{/dashboardmenu}
			</nav>
      <div class=\"clear\"></div>
		</div>
	</header>
	
<div id=\"main\">
<!-- LEFT -->
	<div id=\"left\">
		<div class=\"leftBox\">
			<h3 class=\"blue-gradient white\"><span class=\"icomoon-stats-up icon16\"></span>Website stats</h3>
			{$plugin:toasterstats:Sitestatistic}
		</div>
	</div>

<!-- CONTENT -->
	<div id=\"content\">
		<div class=\"grid_5 text-center\">
<!-- Sales TODAY -->
			<div class=\"grid_6\">
				{$plugin:toasterstats:sales:today}
				<h4 class=\"margin0px\">Sales Today</h4>
			</div>
<!-- TODAY\'S TOP SELLERS -->
			<div class=\"grid_6\">
				{$plugin:toasterstats:quotes:today}
				<h4 class=\"margin0px\">Quotes Requests Today</h4>
			</div>
<!-- TODAY\'S TOP SELLERS -->
			<div class=\"grid_12\">
				{$plugin:toasterstats:money:today}
				<h4 class=\"margin0px\">Earned Today</h4>
				{$plugin:toasterstats:orders:today}
			</div>
		</div>
		<div class=\"grid_3\">
<!-- TODAY\'S TOP SELLERS -->
			<h4>
				<span class=\"icomoon-user-3 icon18\"></span>
				<span class=\"icomoon-star-3 icon12\" style=\"margin-left: -34px; top: 5px;text-shadow: 0 -1px 0 #FFFFFF;\"></span>
				<span class=\"icomoon-star-3 icon14\" style=\"margin-left: -14px; top: 7px;text-shadow: 0 -1px 0 #FFFFFF;\"></span>
				<span class=\"icomoon-star-3 icon12\" style=\"margin-left: -14px; top: 5px;text-shadow: 0 -1px 0 #FFFFFF;\"></span>
				Today\'s top sellers
			</h4>
			{$plugin:toasterstats:products:topsellers:today:10}
		</div>
		<div class=\"grid_4\">
<!-- LATEST CUSTOMERS -->
			<h4><span class=\"icomoon-user-2\"></span>Latest customers</h4>
			{$plugin:toasterstats:customer:new:10}
		</div>
		<div class=\"grid_9\">
<!-- SALES AND QUOTES AMOUNT -->
			<div class=\"pattern\">
				<h2><span class=\"icomoon-bars\"></span></span>Sales and quotes count</h2>
				{$plugin:toasterstats:graph:columnchart:count:quotes|sales:days:7:400:400}
			</div>
<!-- SALES AND QUOTES AMOUNT -->
			<div class=\"pattern\">
				<h2><span class=\"icomoon-bars\"></span>Sales and quotes amount</h2>				
				{$plugin:toasterstats:graph:columnchart:amount:sales|quotes:days:7:400:400}
			</div>
		</div>
		<div class=\"grid_3\">
<!-- DATE RANGE -->
			<h2><span class=\"icomoon-calendar icon16\"></span>Date range</h2>
			{$plugin:toasterstats:control}
<!-- PAST DAYS -->			
			<h2><span class=\"icomoon-clock icon16\"></span>{$plugin:toasterstats:label:days:7}</h2>
			{$plugin:toasterstats:money:days:7}
			{$plugin:toasterstats:orders:days:7}
<!-- BESTSELLERS -->
			<h2><span class=\"icomoon-thumbs-up icon16\"></span>Bestsellers</h2>
			{$plugin:toasterstats:products:topsellers:days:7:10}
		</div>
	</div>
</div>

</body>
</html>
');

INSERT INTO `plugin_dashboard_theme` (`name`, `value`) VALUES ('orders.html', '<!DOCTYPE HTML>
<html>
<head>
<meta charset=\"utf-8\">
<title>{$page:title}</title>
<meta name=\"keywords\" content=\"{$meta:keywords}\" />
<meta name=\"description\" content=\"{$meta:description}\" />

<link rel=\"icon\" type=\"image/vnd.microsoft.icon\" href=\"images/favicon.ico\">
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/grid-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/reset-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/fonts-icon.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/style-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/nav-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/content.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/MQueries-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />

<link href=\'http://fonts.googleapis.com/css?family=Droid+Sans:400,700\' rel=\'stylesheet\' type=\'text/css\'>
<link href=\"http://fonts.googleapis.com/css?family=Open+Sans:400,700\" rel=\"stylesheet\" type=\"text/css\">

<script src=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/scripts.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
	$(document).ready(function(e) {
		$(\'#dashboard-list .dashboard-link-index\').html(\'Dashboard\');
		var domainName = window.location.hostname;
		$(\'.numberProduct\').attr(\'href\', \'http://\'+domainName+\'/dashboard/products/\');
		$(\'.numberProduct\').removeClass(\'tpopup\');
		$(\'.quotesScreen\').attr(\'href\', \'http://\'+domainName+\'/dashboard/quotes/\');
		$(\'.quotesScreen\').removeClass(\'tpopup\');
	});
</script>


<!--[if IE]>
<script src=\"html5.js\" type=\"text/javascript\"></script>
<![endif]-->

</head>


<body class=\"seotoaster-dash\">

<!-- HEADER -->
    <header>
		<div class=\"menu\">
			<nav class=\"main\">
				{dashboardmenu}
					index				
					quotes
					sales
					clients
					orders
					products
				{/dashboardmenu}
			</nav>
      <div class=\"clear\"></div>
		</div>
	</header>
	
<div id=\"main\">
<!-- LEFT -->
	<!--div id=\"left\">
		<div class=\"leftBox\">
			<h3 class=\"blue-gradient white\">{$ header:leftBox1}</h3>
			{$ content:leftBox1}
		</div>
		<div class=\"leftBox\">
			<h3 class=\"blue-gradient white\">{$ header:leftBox2}</h3>
			{$ content:leftBox2}
		</div>
	</div-->

<!-- CONTENT -->
	<div id=\"content\">
  	{$store:orders}
	</div>
</div>

</body>
</html>');

INSERT INTO `plugin_dashboard_theme` (`name`, `value`) VALUES ('products.html', '<!DOCTYPE HTML>
<html>
<head>
<meta charset=\"utf-8\">
<title>{$page:title}</title>
<meta name=\"keywords\" content=\"{$meta:keywords}\" />
<meta name=\"description\" content=\"{$meta:description}\" />

<link rel=\"icon\" type=\"image/vnd.microsoft.icon\" href=\"images/favicon.ico\">
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/grid-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/reset-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/fonts-icon.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/style-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/nav-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/content.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/MQueries-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />

<link href=\'http://fonts.googleapis.com/css?family=Droid+Sans:400,700\' rel=\'stylesheet\' type=\'text/css\'>
<link href=\"http://fonts.googleapis.com/css?family=Open+Sans:400,700\" rel=\"stylesheet\" type=\"text/css\">

<script src=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/scripts.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
	$(document).ready(function(e) {
		$(\'#dashboard-list .dashboard-link-index\').html(\'Dashboard\');
		var domainName = window.location.hostname;
		$(\'.numberProduct\').attr(\'href\', \'http://\'+domainName+\'/dashboard/products/\');
		$(\'.numberProduct\').removeClass(\'tpopup\');
		$(\'.quotesScreen\').attr(\'href\', \'http://\'+domainName+\'/dashboard/quotes/\');
		$(\'.quotesScreen\').removeClass(\'tpopup\');
	});
</script>


<!--[if IE]>
<script src=\"html5.js\" type=\"text/javascript\"></script>
<![endif]-->

</head>


<body class=\"seotoaster-dash\">

<!-- HEADER -->
    <header>
		<div class=\"menu\">
			<nav class=\"main\">
				{dashboardmenu}
					index				
					quotes
					sales
					clients
					orders
					products
				{/dashboardmenu}
			</nav>
      <div class=\"clear\"></div>
		</div>
	</header>
	
<div id=\"main\">
<!-- LEFT -->
	<!--div id=\"left\">
		<div class=\"leftBox\">
			<h3 class=\"blue-gradient white\">{$ header:leftBox1}</h3>
			{$ content:leftBox1}
		</div>
		<div class=\"leftBox\">
			<h3 class=\"blue-gradient white\">{$ header:leftBox2}</h3>
			{$ content:leftBox2}
		</div>
	</div-->

<!-- CONTENT -->
	<div id=\"content\">
		<h2>Manage your product catalog</h2>
		{$store:products}
	</div>
</div>

</body>
</html>');

INSERT INTO `plugin_dashboard_theme` (`name`, `value`) VALUES ('quotes.html', '<!DOCTYPE HTML>
<html>
<head>
<meta charset=\"utf-8\">
<title>{$page:title}</title>
<meta name=\"keywords\" content=\"{$meta:keywords}\" />
<meta name=\"description\" content=\"{$meta:description}\" />

<link rel=\"icon\" type=\"image/vnd.microsoft.icon\" href=\"images/favicon.ico\">
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/grid-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/reset-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/fonts-icon.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/style-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/nav-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/content.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/MQueries-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />

<link href=\'http://fonts.googleapis.com/css?family=Droid+Sans:400,700\' rel=\'stylesheet\' type=\'text/css\'>
<link href=\"http://fonts.googleapis.com/css?family=Open+Sans:400,700\" rel=\"stylesheet\" type=\"text/css\">

<script src=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/scripts.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
	$(document).ready(function(e) {
		$(\'#dashboard-list .dashboard-link-index\').html(\'Dashboard\');
		var domainName = window.location.hostname;
		$(\'.numberProduct\').attr(\'href\', \'http://\'+domainName+\'/dashboard/products/\');
		$(\'.numberProduct\').removeClass(\'tpopup\');
		$(\'.quotesScreen\').attr(\'href\', \'http://\'+domainName+\'/dashboard/quotes/\');
		$(\'.quotesScreen\').removeClass(\'tpopup\');
	});
</script>


<!--[if IE]>
<script src=\"html5.js\" type=\"text/javascript\"></script>
<![endif]-->

</head>


<body class=\"seotoaster-dash\">

<!-- HEADER -->
    <header>
		<div class=\"menu\">
			<nav class=\"main\">
				{dashboardmenu}
					index				
					quotes
					sales
					clients
					orders
					products
				{/dashboardmenu}
			</nav>
      <div class=\"clear\"></div>
		</div>
	</header>
	
<div id=\"main\">
<!-- LEFT -->
	<!--div id=\"left\">
		<div class=\"leftBox\">
			<h3 class=\"blue-gradient white\">{$ header:leftBox1}</h3>
			{$ content:leftBox1}
		</div>
		<div class=\"leftBox\">
			<h3 class=\"blue-gradient white\">{$ header:leftBox2}</h3>
			{$ content:leftBox2}
		</div>
	</div-->

<!-- CONTENT -->
	<div id=\"content\">
		{$quote:grid}
	</div>
</div>

</body>
</html>');

INSERT INTO `plugin_dashboard_theme` (`name`, `value`) VALUES ('sales.html', '<!DOCTYPE HTML>
<html>
<head>
<meta charset=\"utf-8\">
<title>{$page:title}</title>
<meta name=\"keywords\" content=\"{$meta:keywords}\" />
<meta name=\"description\" content=\"{$meta:description}\" />

<link rel=\"icon\" type=\"image/vnd.microsoft.icon\" href=\"images/favicon.ico\">
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/grid-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/reset-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/fonts-icon.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/style-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/nav-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/content.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />
<link href=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/MQueries-dashboard.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />

<link href=\'http://fonts.googleapis.com/css?family=Droid+Sans:400,700\' rel=\'stylesheet\' type=\'text/css\'>
<link href=\"http://fonts.googleapis.com/css?family=Open+Sans:400,700\" rel=\"stylesheet\" type=\"text/css\">

<script src=\"{$website:url}plugins/dashboard/web/themes/dashboardtheme/scripts.js\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
	$(document).ready(function(e) {
		$(\'#dashboard-list .dashboard-link-index\').html(\'Dashboard\');
		var domainName = window.location.hostname;
		$(\'.numberProduct\').attr(\'href\', \'http://\'+domainName+\'/dashboard/products/\');
		$(\'.numberProduct\').removeClass(\'tpopup\');
		$(\'.quotesScreen\').attr(\'href\', \'http://\'+domainName+\'/dashboard/quotes/\');
		$(\'.quotesScreen\').removeClass(\'tpopup\');
	});
</script>


<!--[if IE]>
<script src=\"html5.js\" type=\"text/javascript\"></script>
<![endif]-->

</head>


<body class=\"seotoaster-dash\">

<!-- HEADER -->
    <header>
		<div class=\"menu\">
			<nav class=\"main\">
				{dashboardmenu}
					index				
					quotes
					sales
					clients
					orders
					products
				{/dashboardmenu}
			</nav>
      <div class=\"clear\"></div>
		</div>
	</header>
	
<div id=\"main\">
<!-- LEFT -->
	<div id=\"left\">
		<div class=\"leftBox\">
<!-- DATE RANGE -->
			<h3 class=\"blue-gradient white\"><span class=\"icomoon-calendar icon16\"></span>Date range</h3>
			{$plugin:toasterstats:salescontrol}
		</div>
	</div>

<!-- CONTENT -->
	<div id=\"content\">
	
		<div class=\"grid_8 pattern\">
			<h2><span class=\"icomoon-stats-up\"></span>Sales and quotes count</h2>
			{$plugin:toasterstats:Graph:linechart:count:sales|quotes:days:20:450:450}
		</div>
		<div class=\"grid_4 pattern\">
			<h2><span class=\"icomoon-pie\"></span>Sales amount breakdown by Type</h2>
			{$plugin:toasterstats:Graph:piechart:amount:type:days:20:400:450}
		</div>
		<div class=\"grid_8 pattern\">
			<h2><span class=\"icomoon-stats-up\"></span>Sales and quotes average amount</h2>
			{$plugin:toasterstats:Graph:linechart:averageamount:sales|quotes:days:20:450:450}
		</div>
		<div class=\"grid_4 pattern\">
			<h2><span class=\"icomoon-pie\"></span>Sales amount breakdown by Customer</h2>
			{$plugin:toasterstats:Graph:piechart:amount:customer:days:20:400:450}
		</div>
		<div class=\"grid_6 pattern\">
			<h2><span class=\"icomoon-pie\"></span>Sales amount breakdown by Product</h2>
			{$plugin:toasterstats:Graph:piechart:amount:product:days:20:400:450}
		</div>
		<div class=\"grid_6 pattern\">
			<h2><span class=\"icomoon-pie\"></span>Sales amount breakdown by Brand</h2>
			{$plugin:toasterstats:Graph:piechart:amount:brand:days:20:400:450}
		</div>
		<div class=\"grid_12 pattern\">
			<h2><span class=\"icomoon-bars\"></span>Sales count by Tag</h2>
			{$plugin:toasterstats:Graph:columnchart:count:tag:days:20:400:400}
		</div>
		<div class=\"grid_12 pattern\">
			<h2><span class=\"icomoon-compass\"></span>Sales location</h2>
			{$plugin:toasterstats:Graph:geo:map:days:20:400:400}
		</div>
	
	</div>
</div>

</body>
</html>');

INSERT INTO `plugin_dashboard_theme` (`name`, `value`) VALUES ('themeHtml', 'a:6:{i:0;s:12:"clients.html";i:1;s:10:"index.html";i:2;s:11:"orders.html";i:3;s:13:"products.html";i:4;s:11:"quotes.html";i:5;s:10:"sales.html";}');