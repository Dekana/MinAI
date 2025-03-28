<?php
require_once("config.php");

/*
if (IsEnabled($GLOBALS["PLAYER_NAME"], "isSinging")) {
    $moods=explode(",",$GLOBALS["EMOTEMOODS"]);
    shuffle($moods);
    $pronouns = GetActorPronouns($GLOBALS["PLAYER_NAME"]);
    $GLOBALS["responseTemplate"] = [
        "character"=>$GLOBALS["PLAYER_NAME"],
        "listener"=>"{$GLOBALS['PLAYER_NAME']} is singing to those around {$pronouns['object']}",
        "message"=>"lines of dialogue",
        "mood"=>implode("|",$moods),
        "action"=>implode("|",$GLOBALS["FUNC_LIST"]),
        "target"=>"action's target|destination name",
        "lang"=>"en|es",
        "response_tone_happiness"=>"Value from 0-1",
        "response_tone_sadness"=>"Value from 0-1",
        "response_tone_disgust"=>"Value from 0-1",
        "response_tone_fear"=>"Value from 0-1",
        "response_tone_surprise"=>"Value from 0-1",
        "response_tone_anger"=>"Value from 0-1",
        "response_tone_other"=>"Value from 0-1",
        "response_tone_neutral"=>"Value from 0-1"
    ];
}
else*/
if (isset($GLOBALS["self_narrator"]) && $GLOBALS["self_narrator"] && $GLOBALS["HERIKA_NAME"] == "The Narrator") {
    $moods=explode(",",$GLOBALS["EMOTEMOODS"]);
    shuffle($moods);
    $pronouns = GetActorPronouns($GLOBALS["PLAYER_NAME"]);
    $GLOBALS["responseTemplate"] = [
        "character"=>IsExplicitScene() ? $GLOBALS["PLAYER_NAME"] . "'s body" : $GLOBALS["PLAYER_NAME"] . "'s subconscious",
        "listener"=>IsExplicitScene() ? 
            "{$GLOBALS['PLAYER_NAME']} is reacting to physical sensations" : 
            "{$GLOBALS['PLAYER_NAME']} is thinking to {$pronouns['object']}self",
        "message"=>"lines of dialogue",
        "mood"=>implode("|",$moods),
        "action"=>implode("|",$GLOBALS["FUNC_LIST"]),
        "target"=>"action's target|destination name",
        "lang"=>"en|es",
    ];
    
    // Only include response tones if TTSFUNCTION is zonos_gradio
    if (isset($GLOBALS["TTSFUNCTION"]) && $GLOBALS["TTSFUNCTION"] == "zonos_gradio") {
        $GLOBALS["responseTemplate"]["response_tone_happiness"] = "Value from 0-1";
        $GLOBALS["responseTemplate"]["response_tone_sadness"] = "Value from 0-1";
        $GLOBALS["responseTemplate"]["response_tone_disgust"] = "Value from 0-1";
        $GLOBALS["responseTemplate"]["response_tone_fear"] = "Value from 0-1";
        $GLOBALS["responseTemplate"]["response_tone_surprise"] = "Value from 0-1";
        $GLOBALS["responseTemplate"]["response_tone_anger"] = "Value from 0-1";
        $GLOBALS["responseTemplate"]["response_tone_other"] = "Value from 0-1";
        $GLOBALS["responseTemplate"]["response_tone_neutral"] = "Value from 0-1";
    }
}
