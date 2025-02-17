<?php
require_once("config.php");
require_once("util.php");
$GLOBALS["speaker"] = $GLOBALS["HERIKA_NAME"];
$GLOBALS["minai_processing_input"] = false;

Function SetRadiance($rechat_h, $rechat_p) {
    // minai_log("info", "Setting Rechat Parameters (h={$rechat_h}, p={$rechat_p})");
    $GLOBALS["RECHAT_H"] = $rechat_h;
    $GLOBALS["RECHAT_P"] = $rechat_p;
}

Function SetNarratorProfile() {
    if ($GLOBALS["HERIKA_NAME"] == "The Narrator" && $GLOBALS["use_narrator_profile"]) {
        if (!file_exists(GetNarratorConfigPath())) {
            minai_log("info", "Initializing Narrator Profile");
            createProfile("Narrator", [
                "HERIKA_NAME" => "The Narrator",
                "HERIKA_PERS" => "You are The Narrator in a Skyrim adventure. You will only talk to #PLAYER_NAME#. You refer to yourself as 'The Narrator'. Only #PLAYER_NAME# can hear you. Your goal is to comment on #PLAYER_NAME#'s playthrough, and occasionally, give some hints. NO SPOILERS. Talk about quests and last events."
            ], true);
        }
        $path = GetNarratorConfigPath();
        // minai_log("info", "Overwriting profile with narrator profile ($path).");
        // Ignore narrator name
        global $HERIKA_NAME;
        $HERIKA_NAME = "The Narrator";
        global $PROMPT_HEAD;
        global $HERIKA_PERS;
        global $DYNAMIC_PROFILE;
        global $RECHAT_H;
        global $RECHAT_P;
        global $BORED_EVENT;
        global $CONTEXT_HISTORY;
        global $HTTP_TIMEOUT;
        global $CORE_LANG;
        global $MAX_WORDS_LIMIT;
        global $BOOK_EVENT_FULL;
        global $LANG_LLM_XTTS;
        global $HERIKA_ANIMATIONS;
        global $EMOTEMOODS;
        global $CONNECTORS;
        global $CONNECTORS_DIARY;
        global $CONNECTOR;
        global $TTSFUNCTION;
        global $TTS;
        global $STT;
        global $ITT;
        global $FEATURES;
        require_once($path);
        $_GET["profile"] = md5("Narrator");
    }
}

if (IsRadiant()) {
    SetRadiance(0, 0); // Disable rechat during radiant conversations, as this is handled by MinAI's controller in-game
}

SetNarratorProfile();

// If talking to the narrator, force it to respond.
if (IsEnabled($GLOBALS["PLAYER_NAME"], "isTalkingToNarrator") && isPlayerInput() ) {
    minai_log("info", "Forcing herika_name to the narrator: Is talking to narrator");
    SetEnabled($GLOBALS["PLAYER_NAME"], "isTalkingToNarrator", false);
    $GLOBALS["HERIKA_NAME"] = "The Narrator";
    $GLOBALS["minai_processing_input"] = true;
    SetNarratorProfile();
}

if (IsEnabled($GLOBALS["PLAYER_NAME"], "isSinging")) {
    minai_log("info", "Forcing herika_name to the narrator: Is singing");
    // $GLOBALS["HERIKA_NAME"] = "The Narrator";
    // SetNarratorProfile();
}

require_once("deviousnarrator.php");
if (ShouldUseDeviousNarrator()) {
    SetDeviousNarrator();
}


Function GetConfigPath($npcName) {
    // If use symlink, php code is actually in repo folder but included in wsl php server
    // with just dirname((__FILE__)) it was getting directory of repo not php server 
    $path = getcwd().DIRECTORY_SEPARATOR;
    $newConfFile=md5($npcName);
    return $path . "conf".DIRECTORY_SEPARATOR."conf_$newConfFile.php";
}

if (isset($GLOBALS["realnames_support"]) && $GLOBALS["realnames_support"]) {
    $matches = [];
    if (preg_match('/^(.+?) \[(.+)\]$/', $GLOBALS["HERIKA_NAME"], $matches)) {
        $fullPath = GetConfigPath($matches[0]);
        $badPers = "Roleplay as {$matches[0]}";
        if (!file_exists($fullPath) || $GLOBALS["HERIKA_PERS"] == $badPers) {
            $npcName = $matches[2];
            $codename=addslashes(strtr(strtolower(trim($npcName)),[" "=>"_","'"=>"+"]));
            minai_log("info", "Detected generic NPC, seeding profile. Original: {$matches[0]}, new: {$matches[2]}, codename: $codename");
            $npcTemlate=$GLOBALS["db"]->fetchAll("SELECT npc_pers FROM npc_templates where npc_name='$codename'");
            $personality = 'Roleplay as '.addslashes(trim($matches[1])) . ", who is a " . addslashes(trim($matches[2]));;
            
            // Check if we got results and they have the expected structure
            if (!empty($npcTemlate) && isset($npcTemlate[0]['npc_pers'])) {
                $personality = addslashes(trim($npcTemlate[0]["npc_pers"]));
            } else {
                $npcTemlate=$GLOBALS["db"]->fetchAll("SELECT npc_pers FROM npc_templates_custom where npc_name='$codename'");
                if (!empty($npcTemlate) && isset($npcTemlate[0]['npc_pers'])) {
                    $personality = addslashes(trim($npcTemlate[0]["npc_pers"]));
                }
            }

            // Swap out the generic name for the new name
            $personality = str_replace("Roleplay as {$matches[2]}", "Roleplay as {$matches[0]}", $personality);
            minai_log("info", "Initializing generic NPC {$matches[0]} with personality: $personality");
            createProfile($matches[0],
                          ["HERIKA_PERS" => $personality],
                          true
            );
            global $HERIKA_PERS;
            include($fullPath);
                
        }
    }
}

$GLOBALS["LLM_RETRY_FNCT"] = function() {
    if (isset($GLOBALS['use_llm_fallback']) && !$GLOBALS['use_llm_fallback']) {
        minai_log("info", "LLM fallback is disabled - skipping retry");
        return false;
    }
    
    minai_log("info", "Retrying LLM...");
    SetLLMFallbackProfile();
    $outputWasValid = call_llm();   
    if (!$outputWasValid) {
        minai_log("info", "Warning: LLM returned invalid output after retry.");
    }
    return $outputWasValid;
};

// Only create the fallback config if the feature is enabled
if (isset($GLOBALS['use_llm_fallback']) && $GLOBALS['use_llm_fallback']) {
    CreateFallbackConfig();
}
