<?php
$api_key = "trnsl.1.1.20150925T090358Z.6b210b35847aba52.19735e4559822075be2a8352386ba6a356905beb";
$text = !empty($_GET["text"])?$_GET["text"]:NULL;
if (empty($text)){
    echo json_encode(["status"=>"error", "massage"=> "You must specify text"]);
    exit();
}

$lang = simple_detect_language($text);
$lang_caple = "ru-uk";
if($lang!='ru'){
$lang_caple = "uk-ru";
}

$translate = file_get_contents("https://translate.yandex.net/api/v1.5/tr.json/translate?key=".$api_key.
        "&text=".  urlencode($text)."&lang=".$lang_caple
        );
$translate = json_decode($translate);
if($translate->code==200){
echo json_encode(["status"=> "ok", "text"=>$translate->text]);
}
if($translate->code==402){
    echo json_encode(["status"=>"error", "massage"=> "Api key blocked"]);
    
}
if($translate->code==403){
    echo json_encode(["status"=>"error", "massage"=> "Day lomit"]);
    
}
if($translate->code==404){
    echo json_encode(["status"=>"error", "massage"=> "Month lomit"]);
}

function simple_detect_language( $text ) {
 
$detectLang = array(
'uk'=>array( 'і', 'ї', 'є', 'її', 'цьк', 'ськ', 'ія', 'ння', 'ій', 'ися', 'ись' ),
'ru'=>array( 'ы', 'э', 'ё', 'ъ', 'ее', 'её', 'цк', 'ск', 'ия', 'сс', 'ую', 'ение' ),
);
 
# Get chars presence index
$langsDetected = array();
foreach( $detectLang as $langId=>$nativeChars ) {
$langsDetected[$langId] = 0;
foreach( $nativeChars as $nativeChr )
if( preg_match( "/$nativeChr/ui", $text ) )
$langsDetected[$langId] += 1 / count( $nativeChars );
}
 
# Get the most preferred language for this text
$langsList = array_keys( $detectLang );
$lang = null;
$langIndexMax = 0;
foreach( $langsDetected as $langId=>$index )
if( $index > $langIndexMax ) {
$langIndexMax = $index;
$lang = $langId;
}
 
return $lang;
 
}
