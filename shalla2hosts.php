<?php

//set default list, so simply adding an URL-parameter "?download" will suffice
$preSelection=array("ads","hacking","music","porn","sex","spyware","violence","adv","audio-video","tracker","warez","webradio","aggressive","drugs","podcasts","radiotv","updatesites","webtv","movies");
if(!isset($_GET["selectedCategories"]))
	$_GET["selectedCategories"]=$preSelection;

//get update time of last update.
$lastDownloadTime=getLastUpdateTime();

//if local list is older than 1 week
if($lastDownloadTime<strtotime("-1 week"))
{
	@unlink("shallalist.tar.gz");
	@unlink("shallalist.tar");
	downloadShallaList("http://www.shallalist.de/Downloads/shallalist.tar.gz");
	unpackList();
	
	$lastDownloadTime=saveDownloadTimeToFile();
}

//if downloadbutton is hit
if(isset($_GET["download"]))
{
	//generate random file
	$tempFileName="hosts".mt_rand();
	
	//add each selected category to hosts-file
	foreach ($_GET["selectedCategories"] as $category)
	{
		assembleHostsFile($category,$tempFileName);
	}
	
	//deliver it as download
	header('Content-Disposition: attachment; filename="hosts"');
	
	//add some header information.
	echo "#last list update: ".date("c",$lastDownloadTime)."\n";
	echo "#For automated download use: wget \"http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI]."\" -O hosts\n";
	echo "#\n";
	echo "#This file is derived from URL blacklist maintained by http://www.shallalist.de/\n";
	echo "#using an automatic conversion tool: https://github.com/derStephan/shallalistToHosts\n";
	echo "#Note: The author of this tool is in no way connected to shalla.\n";
	echo "#\n";
	readfile($tempFileName);
	
	//delete temp file
	unlink($tempFileName);
}
else
{
	//if downloadbutton is not pressed, show list.
	showAvailableCategorySelection($lastDownloadTime);
}


function showAvailableCategorySelection($lastDownloadTime)
{
	//get main categories from list
	$categoryDirectories=glob("list/BL/*");
	//get already checked categories
	$selectedCategories=$_GET["selectedCategories"];

	?>
	<!DOCTYPE html>
	<head>
		<title>Shalla to hosts</title>
		<script type="text/javascript">
		function toggle(source) 
		{
			checkboxes = document.getElementsByName('selectedCategories[]');
			for(var i=0, n=checkboxes.length;i<n;i++) 
			{
				checkboxes[i].checked = source.checked;
			}
		}
		</script>
	
	</head>
	<body>
		
	last list update: <?=date("c",$lastDownloadTime);?>
	
	<form method="GET">
	<input type="checkbox" value="select all" onClick="toggle(this)" id="selectAll"><label for="selectAll">toggle all</label><br/><br />		
	<input type="submit" value="download hosts" name="download"><br />
	<?php
	foreach ($categoryDirectories as $categoryDirectory)
	{
		if(is_dir($categoryDirectory))
		{
			$category=explode("/",$categoryDirectory);
			$category=$category[2];
			?>
			<input type="checkbox" name="selectedCategories[]" value="<?=$category?>" id="<?=$category?>" <?php if(in_array($category,@$selectedCategories)) echo "checked"; ?>><label for="<?=$category?>"><?=$category?></label><br />

			<?php
		}
	}
	?>
	<input type="submit" value="download hosts" name="download">
	</form>
	</body>
	</html>
	<?php
	
}


function getLastUpdateTime()
{
	if (!file_exists("lastDownload"))
		return 0;
	
	$fp=fopen("lastDownload","r");
	$lastDownloadTime=fgets($fp);
	fclose($fp);
	return $lastDownloadTime;
}



function downloadShallaList($url)
{
	// is cURL installed yet?
	if (!function_exists('curl_init')){
		die('Sorry cURL is not installed!');
	}

	// OK cool - then let's create a new cURL resource handle
	$ch = curl_init();

	// Now set some options (most are optional)
	// Set URL to download
	curl_setopt($ch, CURLOPT_URL, $url);
	// Include header in result? (0 = yes, 1 = no)
	curl_setopt($ch, CURLOPT_HEADER, 0);
	// Should cURL return or print out the data? (true = return, false = print)
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// Timeout in seconds, pretty high!
	curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
	// Download the given URL, and save output
	$contents = curl_exec($ch);

	//write output to file
	$fp=fopen("shallalist.tar.gz","w");
	fwrite($fp, $contents);
	fclose($fp);

	// Close the cURL resource, and free system resources
	curl_close($ch);
	
}

function unpackList()
{
	$p = new PharData('shallalist.tar.gz');
	$p->decompress(); // creates files.tar

	// unarchive from the tar
	$phar = new PharData('shallalist.tar');
	$phar->extractTo('list',null,true); 
}

function saveDownloadTimeToFile()
{
	//save downloadtime to file.
	$fp=fopen("lastDownload","w");
	fwrite($fp, time());
	fclose($fp);
	
	return time();
}



function assembleHostsFile($category,$hostsFilename)
{
	$directory=glob("list/BL/$category");
	
	@$directory=$directory[0];
	
	if($directory[0]=="")
		return false;
	
	if(file_exists ("$directory/domains"))
		writeDomainsToHosts("$directory/domains",$hostsFilename);
	else 
	{
		$subCategories=glob("$directory/*");
		foreach($subCategories as $subCategory)
		{
			writeDomainsToHosts("$subCategory/domains",$hostsFilename);
		}
	}
}

function writeDomainsToHosts($filePath,$hostsFilename)
{
	$domains=fopen($filePath,"r");
	$hosts=fopen($hostsFilename,"a");
	//add heading for upcoming list.
	fwrite($hosts,"#category: $filePath \n");
	while($line = fgets($domains))
	{
		fwrite($hosts,"127.0.0.1 $line");
	}
		
	fclose($hosts);
	fclose($domains);
}

?>
