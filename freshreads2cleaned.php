<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="feedstyler.css">
</head>
<body>
<h1><a href="http://localhost:8888/freshreads2.php">Tonz (of) subs</a></h1>
<script>
function reveal(dit) {
const targetDiv = document.getElementById(dit);
if (targetDiv.style.display !== "block") {
    targetDiv.style.display = "block";
  } else {
    targetDiv.style.display = "none";
  }
}

function verstuurreactie(form) {
  let data = new FormData(form);
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "verwerkfeed2.php");
  xhr.onload = function () { console.log(this.response); };
  xhr.send(data);
  alert("verzonden");
  return false;
}

function markread(id) {
  var data = new FormData();
  data.append('id', id);
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "markread.php");
  xhr.onload = function () { console.log(this.response); };
  xhr.send(data);
  alert("Groep als gelezen gemarkeerd");
  return false;
}
</script>
<?php
$api_key = "YOURAPIKEY HERE";/* hash v user en v wachtwoord dus vervang bij wijziging ww */
$url="https://yourFreshRSSinstance/fresh/api/fever.php";
$urlgetgroups = $url."?groups";
$urlgetlastunread = $url."?unread_item_ids";
$urlgetfeed = $url."?feeds";
$urlgetitems = $url."?items";
$urlgetsingle = $url."?items&with_ids=";
$openicon = '<svg viewBox="0 0 100 100" width="25" height="25" xmlns="http://www.w3.org/2000/svg"><g fill="green" stroke="green" stroke-width="2"><circle cx="40" cy="40" r="25" /></g></svg>';
$urlthis ="freshreads.php";

/* vragen om groepen */
$askedgroups = askendpoint($urlgetgroups);
$decodedgroups = json_decode($askedgroups);
$groups = $decodedgroups->groups;
foreach ($groups as $folder){
	$groepfolder[$folder->id] = $folder->title;
	 
}
$foldercount= count($groepfolder);
/* feeds bij groepen onthouden */
$groupfeeds = $decodedgroups->feeds_groups;
$feedtogroup= [];
foreach ($groupfeeds as $groupfeed){
	$feedids = explode(",", $groupfeed->feed_ids);
	foreach($feedids as $feedid) {
		$feedtogroup[$feedid] = $groupfeed->group_id;
	}
}


/* vragen om unread item ids */
$askedids = askendpoint($urlgetlastunread);
$decodedids = json_decode($askedids, true);
$unreadids = explode(",",$decodedids['unread_item_ids']);
$unreadcount = count($unreadids)-1;

echo "<h2>Er zijn ".$unreadcount." ongelezen items</h2>";
echo '<button onclick="markread(0)" id="markread">Alles gelezen</button>'."<br/>\n";
for ($fcounter=1; $fcounter<=$foldercount; $fcounter++) {
echo '<button onclick="markread('.$fcounter.')" id="markread">'.$groepfolder[$fcounter].' gelezen</button>'."\n";
}

/* alle unread items aan een groep toewijzen daarna groepen afbeelden */
$groupeditems = [];

/* ongelezen items ophalen in blokken v 50 */
$batchsize = 50; // max 50 per keer ophalen
for ($counter= 0; $counter<$unreadcount; $counter +=$batchsize) { /* counting forwards so it starts with oldest */
	$batchids = array_slice($unreadids, $counter, $batchsize); /* pak 50 item ids */
	$urlgetitem = $urlgetsingle.'&with_ids='.implode(',', $batchids); /*haal 50 feeditems op */
$unreaditem = askendpoint($urlgetitem);
$unreaditemslist = json_decode($unreaditem, true); /* decode the json van fetched item*/

if (array_key_exists("items", $unreaditemslist)) { /* als er een items element is in fetched item */
	$unreads = $unreaditemslist["items"]; /* haal het item op als array */
	foreach ($unreads as $fetcheditem) { /* er zijn er max 50 en die haal je op in een loop */
		$feedid = $fetcheditem["feed_id"]; /* pak het feedid van het item */
		$groupid = $feedtogroup[$feedid]; /*zoek groep id dat er bij hoort */
		if (!isset($groupeditems[$groupid])) { /* als er voor die groep geen array is, maak die aan */
			$groupeditems[$groupid] = []; /* maak een array voor de nieuwe groep */
		}
		$groupeditems[$groupid][] = $fetcheditem; /* voeg het item toe aan de array van de groep */
	 /* eind ophalen item */
	}
} /* eind als er een items element is in fetched item */
} /* eind ophalen ongelezen items in een for loop */
/* alle items zitten nu in een groep */
/* groepen nu 1 voor 1 afbeelden en ook een reveal button geven zodat je per groep kunt lezen */
foreach ($groupeditems as $groupid => $groupitems) { /* begin loop om groepen te doorlopen */
	echo '<div class="group">';
	echo '<h2><a onclick="reveal('.$groupid.')"><img src="network.png" width="30" height="30"> '.$groepfolder[$groupid].'</a></h2><br/>';
	echo '<div id="'.$groupid.'" style="display:none;">';
	foreach ($groupitems as $fetcheditem) { /* begin loop om items te doorlopen */
		$postid=$fetcheditem["id"]; /* deze unieke id stuurt de reactiebutton */
	if (!array_key_exists("title",$fetcheditem)) $fetcheditem["title"] = "a post";
	$openthis = '<a onclick="reveal('."'p".$postid."'".')">'.$openicon.'</a>';
	echo "<br/><h3>".'<a href="'.$fetcheditem["url"].'">'.$fetcheditem["title"]."</a>"." ".$openthis."</h3>uit ".$fetcheditem["url"]."<br/>\n<div id='p".$postid."' style='display:none;'>";
	if (!array_key_exists("html",$fetcheditem)) {
		$echodit ="geen content \n\n<br/>";
		echo $echodit;
		$formcontent = htmlentities($echodit, ENT_QUOTES);
	}
	if (array_key_exists("html",$fetcheditem)) {
		$fetchedcontent= $fetcheditem["html"];
		echo $fetchedcontent;
		$formcontent = htmlentities($fetchedcontent, ENT_QUOTES);
	}
	
	$authorname ="unknown";
	if (array_key_exists("author", $fetcheditem)) { 
		$authorname = $fetcheditem["author"];
	}
	echo '<br/><button onclick="reveal('."'".$postid."'".')" id="toggleresponse">Reageer op bovenstaande post</button><br/><br/>'."\n";
	echo '<div id="'.$postid.'" style="display:none;">'."\n";
	echo '<form name="input_form'.$postid.'" method="POST" onsubmit="verstuurreactie(this)" action="">';
	echo '<input type="hidden" id="artikelcontent" name="artikelcontent" value="'.$formcontent.'">';
	echo '<input type="radio" id="bmark" name="rad" value="bmark" checked onClick=0><label for="bmark">Bmark</label><input type="radio" name="rad" id="reply" value="reply" onClick=0><label for="reply">Reply</label><input type="radio" id="fav" name="rad" value="fav" onClick=0><label for="fav">Fav</label><input type="radio" id="h" name="rad" value="h" onClick=0><label for="h">H.</label><br/>';
	echo "<input type='text' size='60' name='title' value='". $fetcheditem["title"]."'>";
	echo "<input type='text' name='auteur' value='".$authorname."'>";
	echo "\n<br/>Eventuele eigen titel<br/><input type='text' size='60' name='eigentitel'><br/>\n";
	echo "\n<br/><input type='text' size='85' name='link' value='". $fetcheditem["url"]."'><br/>";
	echo "Mijn motivatie of antwoord:<br/><textarea cols='100' rows='10' name='motivatie'></textarea><br/><br/>";
	echo "Te gebruiken quote uit artikel:<br/><textarea cols='100' rows='10' name='quote'></textarea>";
	echo "\n<br/>Cats:<input type='text' size='60' name='cats'><br/>\n";
	echo '<input type="radio" name="site" id="zyl" value="zyl" checked onClick=0><label for="zyl">zyl</label><input type="radio" name="site" id="obs" value="obs" onClick=0><label for="obs">obs</label><input type="radio" name="site" id="tgl" value="tgl" onClick=0><label for="tgl">TGL</label><input type="radio" name="site" id="iwc" value="iwc" onClick=0><label for="iwc">IWC</label><input type="radio" name="site" id="geh" value="geh" onClick=0><label for="geh">geh</label><br/>';
	echo '<input type="radio" name="type" id="post" value="post" checked onClick=0><label for="post">post</label><input type="radio" name="type" id="page" value="page" onClick=0><label for="page">page</label><br/>';
	echo '<br/> <input type="submit" name="submitButton" value="Verwerk">'."\n</form>";
	echo "</div></div>\n\n"; /* end reactieform */
	} /* eind ophalen items*/
	echo "</div></div>\n\n"; /* end per groep en alle groep */
	} /* eind loop om groepen te doorlopen */	
	
	


function askendpoint($geturl) { /* doet een POST op mijn freshrss fever API */
global $api_key;

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $geturl,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('api_key' => $api_key),
));

$response = curl_exec($curl);

curl_close($curl);
return $response;
}

?>
</body>
</html>

