<?php
$localVars = localVars::getInstance();
$localVars->set("cssExt","less");
$localVars->set("cssURL","https://www.libraries.wvu.edu/css/2012");
$localVars->set("jsURL", "https://www.libraries.wvu.edu/javascript/2012");
$localVars->set("imgURL","https://www.libraries.wvu.edu/images/2012");
$localVars->set("styleRel","");
// using javascript less, uncomment the following
//$localVars->set("styleRel","/less");

$localVars->set("docRoot","");

// for open graph tags
$localVars->set("pageURL","http".((isset($_SERVER['HTTPS']))?"s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].((isset($_SERVER['QUERY_STRING']) && !is_empty($_SERVER['QUERY_STRING']))?"?".htmlSanitize($_SERVER['QUERY_STRING']):""));
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>{local var="pageTitle"}</title>

	<meta charset="UTF-8">

	<!-- Facebook Open Graph tags -->
	<meta property="og:title" content="{local var="pageTitle"}" />
	<meta property="og:type" content="university" />
	<meta property="og:url" content="{local var="pageURL"}" />
	<meta property="og:image" content="{local var="pageImg"}" />
	<meta property="og:site_name" content="WVU Libraries" />
	<meta property="fb:admins" content="1021916351" />

	<script type="text/javascript" src="{local var="jsURL"}/jquery/jquery.js"></script>
	<script type="text/javascript" src="{local var="jsURL"}/modernizr.js"></script>
	<!-- // <script type="text/javascript" src="{local var="jsURL"}/html5.js"></script> -->
	<script type="text/javascript" src="{local var="jsURL"}/bootstrap.js"></script>

	<!-- Add some HTML5 element support into IE 6 - 8 -->
	<!--[if lt IE 9]>
		<script type="text/javascript" src="{local var="jsURL"}/html5.js"></script>
	<![endif]-->

	<!-- <script type="text/javascript" src="{local var="jsURL"}/bootstrap/js/bootstrap.min.js"></script> -->
	<link rel="stylesheet" type="text/css" href="{local var="jsURL"}/bootstrap/css/bootstrap.min.css"/> 

	<link rel="stylesheet{local var="styleRel"}" type="text/css" href="{local var="cssURL"}/common.{local var="cssExt"}" />
	<link rel="stylesheet{local var="styleRel"}" type="text/css" href="{local var="cssURL"}/fonts.{local var="cssExt"}" />
	<link rel="stylesheet" type="text/css" href="{local var="cssURL"}/print.css" media="print" />


	<?php
	// include the CSS for the home page
	// $url = $localVars->get("docRoot")."/index.php";
	// $url = str_replace("/","\\/",$url);
	// recurseInsert("templateIncludes/homepageHeaderIncludes.php","php","^".$url,"PHP_SELF");

	// include the 2 column css when the 2column template is loaded
	if (templates::name() == "library2012.2col") {
		recurseInsert("templateIncludes/2colHeaderIncludes.php","php");
	}
	// include the 3 column css when the 2column template is loaded
	if (templates::name() == "library2012.3col") {
		recurseInsert("templateIncludes/3colHeaderIncludes.php","php");
	}
	// include the 3 column css when the 2column template is loaded
	if (templates::name() == "library2012.2col.right") {
		recurseInsert("templateIncludes/2colRightHeaderIncludes.php","php");
	}

	?>

	<?php recurseInsert("includes/engine/headerIncludes.php","php"); ?>

	<!-- Less should be removed once development is done and less should be compiled into JS -->
	<!-- <script type="text/javascript" src="{local var="jsURL"}/less.js"></script> -->

</head>

<body>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<header>
		<div id="wvuMasthead">
			<a href="http://www.wvu.edu"><img src="{local var="imgURL"}/header/wvuLogo.png" alt="wvuLogo"/></a>
			<ul>
				<li>
					<a href="http://www.wvu.edu/SiteIndex/" title="">A-Z Site Index</a>
				</li>
				<li>
					<a href="http://www.wvu.edu/campusmap/" title="">Campus Map</a>
				</li>
				<li>
					<a href="http://directory.wvu.edu/" title="">Directory</a>
				</li>
				<li>
					<a href="/about/contactus/" title="">Contact Us</a>
				</li>
				<li>
					<a href="/hours/" title="">Hours</a>
				</li>
				<li>
					<a href="http://www.wvu.edu/" title="">WVU Home</a>
				</li>
			</ul>
		</div>

		<div id="middleHeaderBar">

			<h1><a href="/">L<span class="font-size-30pt">IBRARIES</span></a></h1>
			<form method="GET" action="http://wvu.summon.serialssolutions.com/search">
				<input type="hidden" name="s.fvf[]" value="ContentType,Newspaper Article,t" />
				<input type="search" id="summonSearch" name="s.q" tabindex="1" placeholder="Search Summon: Books, eBooks, Videos, Articles, &amp; More" />
				<button type="submit">
					<span class="label">Search</span>
				</button>

			</form>
		</div>

		<nav>
			<ul>
				<li>
					<a href="/"><img src="{local var="imgURL"}/header/{local var="homeHeaderImg"}" alt="Home" /></a>
				</li>
				<li>
					<a href="/libraries/" {local var="headerLibraryClass"}>LIBRARIES</a>
				</li>
				<li>
					<a href="/collections/" {local var="headerCollectionsClass"}>COLLECTIONS</a>
				</li>
				<li>
					<a href="/instruction/" {local var="headerInstructionClass"}>INSTRUCTION</a>
				</li>
				<li>
					<a href="/services/" {local var="headerServicesClass"}>SERVICES</a>
				</li>
				<li>
					<a href="/about/" {local var="headerAboutClass"}>ABOUT</a>
				</li>

			</ul>
		</nav>

		<a href="/services/ask/"><img src="{local var="imgURL"}/askALibrarianButton.png" alt="Ask a Librarian" id="askALibrarian_header"/></a>

	</header>

	<div id="alertBar">

	</div>

	<div id="contentContainer">
	<!-- End Header -->