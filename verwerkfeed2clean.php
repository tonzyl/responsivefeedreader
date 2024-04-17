<?php
require 'vendor/autoload.php'; //nodig voor markdown notes
use League\HTMLToMarkdown\HtmlConverter; //nodig voor markdown notes

if ($_POST) {
$replylabel = "reply";
$favlabel = "fav";
$bmarklabel = "bmark";
$hypolabel ="h";

// we maken een post of note
//haal de velden hiervoor op
$site = $_POST['site'];
$type = $_POST['type'];
$lab = $_POST['rad'];
$aut = $_POST['auteur'];
$url = $_POST['link'];
$naam = $_POST['title'];
$motiv = $_POST['motivatie'];
$cit = $_POST['quote'];
$posttitel =''; /* voorkom NULL in WP */
$notenaam = $naam; // standaard de oorspr titel voor note
//html moet nog meekomen met postform
$html = $_POST['artikelcontent'];
if (isset($_POST['eigentitel'])) $posttitel = $_POST['eigentitel'];
$cats = ['timeline']; /* default cat */
if (isset($_POST['cats'])) $cats = explode(", ", $_POST['cats']);
if (isset($_POST['cats'])) $catsnote = $_POST['cats'];
if ($site !== "obs") {
if ($lab == $hypolabel) {
	$annodoc = $naam;
	if ($posttitel !== '') { $annodoc = $posttitel; }
	$annouri = $url;
	$annotext = $motiv;
	include 'hypothis.php'; //ga direct naar h. posten
} else { // end if hypothesis
	//bouw nu de svg etc op en ga naar postingscript
if ($lab == $replylabel) {
	$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="19" height="19"><path d="M576 240c0 115-129 208-288 208-48.3 0-93.9-8.6-133.9-23.8-40.3 31.2-89.8 50.3-142.4 55.7-5.2.6-10.2-2.8-11.5-7.7-1.3-5 2.7-8.1 6.6-11.8 19.3-18.4 42.7-32.8 51.9-94.6C21.9 330.9 0 287.3 0 240 0 125.1 129 32 288 32s288 93.1 288 208z"/></svg>';
	$descriptor = ' <em>In reply to <a href="'.$url.'" class="p-name u-in-reply-to">'.$naam.'</a> by '.$aut.'</em>';
} // end if reply

if ($lab == $favlabel) {
	$svg ='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="15" height="12"><path d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"/></svg>';
	$descriptor = ' <em>Favorited <a class="u-favorite-of p-name" href="'.$url.'">'.$naam.'</a> by '.$aut.'</em>';
	} // end if fav

if ($lab == $bmarklabel) {
	$svg='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="12" height="17"><path d="M0 512V48C0 21.49 21.49 0 48 0h288c26.51 0 48 21.49 48 48v464L192 400 0 512z"/></svg>';
	$descriptor = ' <em>Bookmarked <a class="u-bookmark-of" href="'.$url.'">'.$naam.'</a> (by '.$aut.')</em>';
} // end if bmark

$respons = $svg.$descriptor."<br/><p>".$motiv."</p>";
if ($cit) {
	$respons = $respons."<br/><blockquote>".$cit."<br/><br/>".$aut."</blockquote>";
} // end if citaat
$contentspul = $respons;

include 'jsonclassic.php';
//alle output van het form is verwerkt en afgebeeld
} // end else if not h.
} // end if not obs

if ($site == "obs") {
// making a note (the code applies my personal note template for clipping articles)
$notepath = "../../relative/path/toyour/obsidianvault/folder"; //relatief pad v localhost naar mijn noteclippings
$tijdstamp = date('YmdHis');
if (strlen($_POST['eigentitel'])>0) $notenaam = $_POST['eigentitel'];// pas titel aan als ik een eigen titel meegeef
$notenaam = $notenaam." ".$tijdstamp; // notenaam nu bekend
$prepend = "#nieuw reden:: ".$motiv."\n\n"."clipped #".date('Y')."/".date('m')."/".date('d'). " from ".$url." by ".$aut."\n\n"."# ".$naam."\n\n";
if (strlen($cit)>0) $prepend = $prepend."> ".$cit."\n\n";
$append = "Ref: clipped from ".$url." by ".$aut;
// content moet nog tussen pre en append, maar die moet eerst naar md
$converter = new HtmlConverter(array('strip_tags' => true));
$markdown = $converter->convert($html);
if (strlen($catsnote)>0) { 
	$notecats = str_replace(', ',' #', $catsnote); //alle commaspatie vervangen door spatiehash
	$notecats = "#".$notecats; //eerste tag nog een hash ervoor
	$markdown = $markdown."\n\n".$notecats;//cats als tags achter de posting
}
$mynote = $prepend.$markdown."\n\n".$append;
// schrijven naar file
file_put_contents($notepath.$notenaam.".md", $mynote);
} // end if obs
} // end if POST
// if not POST do nothing
?>