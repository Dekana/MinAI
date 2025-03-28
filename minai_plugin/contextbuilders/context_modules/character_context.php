<?php
/**
 * Character Context Builders
 * 
 * This file contains context builders related to character status and attributes
 */

require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../util.php");
require_once(__DIR__ . "/../system_prompt_context.php");
require_once(__DIR__ . "/../../contextbuilders/dirtandblood_context.php");
require_once(__DIR__ . "/../../contextbuilders/exposure_context.php");
require_once(__DIR__ . "/../../contextbuilders/fertilitymode_context.php");

/**
 * Helper function to validate and sanitize parameters for context builders
 * 
 * @param array $params Parameters to validate
 * @param array $required List of required parameter keys
 * @return array Validated and sanitized parameters with fallbacks if needed
 */
function ValidateContextParams($params, $required = ['herika_name']) {
    $validated = [];
    
    // Check for required parameters
    foreach ($required as $key) {
        if (isset($params[$key])) {
            $validated[$key] = $params[$key];
        } else {
            // Try to use globals as fallback
            switch ($key) {
                case 'herika_name':
                    $validated[$key] = isset($GLOBALS["HERIKA_NAME"]) ? $GLOBALS["HERIKA_NAME"] : "";
                    break;
                case 'player_name':
                    $validated[$key] = isset($GLOBALS["PLAYER_NAME"]) ? $GLOBALS["PLAYER_NAME"] : "";
                    break;
                case 'target':
                    $validated[$key] = isset($GLOBALS["HERIKA_TARGET"]) ? 
                                      $GLOBALS["HERIKA_TARGET"] : 
                                      (isset($validated['player_name']) ? $validated['player_name'] : "");
                    break;
                default:
                    $validated[$key] = "";
            }
        }
    }
    
    // Add any other parameters that were in the original params
    foreach ($params as $key => $value) {
        if (!isset($validated[$key])) {
            $validated[$key] = $value;
        }
    }
    
    return $validated;
}

/**
 * Initialize character context builders
 */
function InitializeCharacterContextBuilders() {
    $registry = ContextBuilderRegistry::getInstance();
    
    // Register physical description context builder
    $registry->register('physical_description', [
        'section' => 'status',
        'header' => 'Physical Appearance',
        'description' => 'Physical description of the character',
        'priority' => 10,
        'enabled' => isset($GLOBALS['minai_context']['physical_description']) ? (bool)$GLOBALS['minai_context']['physical_description'] : true,
        'builder_callback' => 'BuildPhysicalDescriptionContext'
    ]);
    
    // Register equipment context builder
    $registry->register('equipment', [
        'section' => 'status',
        'header' => 'Equipment',
        'description' => 'Equipment and worn items',
        'priority' => 20,
        'enabled' => isset($GLOBALS['minai_context']['equipment']) ? (bool)$GLOBALS['minai_context']['equipment'] : true,
        'builder_callback' => 'BuildEquipmentContext'
    ]);
    
    // Register tattoos context builder
    $registry->register('tattoos', [
        'section' => 'status',
        'header' => 'Tattoos',
        'description' => 'Character tattoos',
        'priority' => 30,
        'enabled' => isset($GLOBALS['minai_context']['tattoos']) ? (bool)$GLOBALS['minai_context']['tattoos'] : true,
        'builder_callback' => 'BuildTattooContext'
    ]);
    
    // Register arousal context builder
    $registry->register('arousal', [
        'section' => 'status',
        'header' => 'Arousal Status',
        'description' => 'Character arousal level',
        'priority' => 40,
        'is_nsfw' => true,
        'enabled' => isset($GLOBALS['minai_context']['arousal']) ? (bool)$GLOBALS['minai_context']['arousal'] : true,
        'builder_callback' => 'BuildArousalContext'
    ]);
    
    // Register fertility context builder
    $registry->register('fertility', [
        'section' => 'status',
        'header' => 'Fertility Status',
        'description' => 'Character fertility status',
        'priority' => 50,
        'is_nsfw' => true,
        'enabled' => isset($GLOBALS['minai_context']['fertility']) ? (bool)$GLOBALS['minai_context']['fertility'] : true,
        'builder_callback' => 'BuildFertilityContext'
    ]);
    
    // Register following context builder
    $registry->register('following', [
        'section' => 'status',
        'header' => 'Following Status',
        'description' => 'Character following status',
        'priority' => 60,
        'enabled' => isset($GLOBALS['minai_context']['following']) ? (bool)$GLOBALS['minai_context']['following'] : true,
        'builder_callback' => 'BuildFollowingContext'
    ]);
    
    // Register survival context builder
    $registry->register('survival', [
        'section' => 'status',
        'header' => 'Survival Status',
        'description' => 'Character survival needs',
        'priority' => 70,
        'enabled' => isset($GLOBALS['minai_context']['survival']) ? (bool)$GLOBALS['minai_context']['survival'] : true,
        'builder_callback' => 'BuildSurvivalContext'
    ]);
    
    
    // Register bounty context builder
    $registry->register('bounty', [
        'section' => 'interaction',
        'header' => 'Bounty Status',
        'description' => 'Player bounty status',
        'priority' => 40,
        'enabled' => isset($GLOBALS['minai_context']['bounty']) ? (bool)$GLOBALS['minai_context']['bounty'] : true,
        'builder_callback' => 'BuildBountyContext'
    ]);
    
    // Register mind influence context builder
    $registry->register('mind_influence', [
        'section' => 'status',
        'header' => 'Mental State',
        'description' => 'Character mind influence state',
        'priority' => 80,
        'enabled' => isset($GLOBALS['minai_context']['mind_influence']) ? (bool)$GLOBALS['minai_context']['mind_influence'] : true,
        'builder_callback' => 'BuildMindInfluenceContext'
    ]);
}

/**
 * Build the physical description context
 * 
 * @param array $params Parameters including herika_name, player_name, target
 * @return string Formatted physical description context
 */
function BuildPhysicalDescriptionContext($params) {
    // Determine which character we're building context for
    $character = $params['herika_name'];
    
    $gender = GetActorValue($character, "gender");
    $race = GetActorValue($character, "race");
    $beautyScore = GetActorValue($character, "beautyScore");
    $breastsScore = GetActorValue($character, "breastsScore");
    $buttScore = GetActorValue($character, "buttScore");
    $isexposed = GetActorValue($character, "isexposed");
    
    // Get proper pronouns for the character
    $pronouns = GetActorPronouns($character);
    
    $ret = "";
    $isWerewolf = false;
    
    if ($gender != "" && $race != "") {
        $ret .= "{$character} is a {$gender} {$race}. ";
        if ($race == "werewolf") {
            $isWerewolf = true;
            $ret .= "{$character} is currently transformed into a terrifying werewolf! ";
        }
    }
    
    // Don't add beauty/physical attributes for NPCs unless specified
    $isPlayer = IsPlayer($character);
    $addPhysicalDetails = $isPlayer || isset($params['add_npc_physical_details']);
    
    if (!$addPhysicalDetails) {
        return $ret;
    }
    
    // Beauty description using 0-10 scale
    if (isset($beautyScore) && $beautyScore !== "" && !$isWerewolf) {
        // Convert to 0-10 scale if needed
        $beautyStage = min(10, ceil(intval($beautyScore)/10));
        
        $beautyDesc = "";
        switch ($beautyStage) {
            case 0: $beautyDesc = "Extremely Unattractive"; break;
            case 1: $beautyDesc = "Very Unattractive"; break;
            case 2: $beautyDesc = "Unattractive"; break;
            case 3: $beautyDesc = "Below Average"; break;
            case 4: $beautyDesc = "Plain"; break;
            case 5: $beautyDesc = "Average with Charm"; break;
            case 6: $beautyDesc = "Somewhat Alluring"; break;
            case 7: $beautyDesc = "Naturally Sensual"; break;
            case 8: $beautyDesc = "Captivating"; break;
            case 9: $beautyDesc = "Strikingly Beautiful"; break;
            case 10: $beautyDesc = "Exceptionally Gorgeous"; break;
            default: $beautyDesc = "Unknown";
        }
        
        // Add the numerical rating
        $beautyDesc .= " ({$beautyStage}/10)";
        
        $ret .= ucfirst($pronouns['subject']) . " is {$beautyDesc} (appearance). ";
    }
    
    // Breast and butt descriptions (if applicable)
    if((isset($breastsScore) && $breastsScore !== "") && (isset($buttScore) && $buttScore !== "") && !$isWerewolf) {
        // Convert to 0-10 scale
        $breastsStage = min(10, ceil(intval($breastsScore)/10));
        $buttStage = min(10, ceil(intval($buttScore)/10));
        
        // Breast description
        $breastsDesc = "";
        switch ($breastsStage) {
            case 0: $breastsDesc = "Unappealing Breasts"; break;
            case 1: $breastsDesc = "Very Plain Breasts"; break;
            case 2: $breastsDesc = "Plain Breasts"; break;
            case 3: $breastsDesc = "Unremarkable Breasts"; break;
            case 4: $breastsDesc = "Decent Breasts"; break;
            case 5: $breastsDesc = "Pleasing Breast Curves"; break;
            case 6: $breastsDesc = "Attractive Breasts"; break;
            case 7: $breastsDesc = "Very Attractive Breasts"; break;
            case 8: $breastsDesc = "Striking Breast Curves"; break;
            case 9: $breastsDesc = "Exceptionally Attractive Breasts"; break;
            case 10: $breastsDesc = "Remarkably Beautiful Breasts"; break;
            default: $breastsDesc = "Unknown";
        }
        
        // Add the numerical rating
        $breastsDesc .= " ({$breastsStage}/10)";
        
        // Butt description
        $buttDesc = "";
        switch ($buttStage) {
            case 0: $buttDesc = "Unappealing Buttocks"; break;
            case 1: $buttDesc = "Very Plain Buttocks"; break;
            case 2: $buttDesc = "Plain Buttocks"; break;
            case 3: $buttDesc = "Unremarkable Buttocks"; break;
            case 4: $buttDesc = "Decent Buttocks"; break;
            case 5: $buttDesc = "Nicely Formed Buttocks"; break;
            case 6: $buttDesc = "Attractive Buttocks"; break;
            case 7: $buttDesc = "Well-Curved Buttocks"; break;
            case 8: $buttDesc = "Very Attractive Buttocks"; break;
            case 9: $buttDesc = "Exceptionally Shapely Buttocks"; break;
            case 10: $buttDesc = "Remarkably Well-Formed Buttocks"; break;
            default: $buttDesc = "Unknown";
        }
        
        // Add the numerical rating
        $buttDesc .= " ({$buttStage}/10)";
        
        $ret .= ucfirst($pronouns['subject']) . " has {$breastsDesc} (chest) and {$buttDesc} (posterior). ";
    }
    
    if (IsEnabled($character, "isexposed")) {
        $ret .= GetPenisSize($character);
    }
    
    return $ret;
}

/**
 * Get the description of a character's penis size
 * 
 * @param string $name Character name
 * @return string Formatted penis size description
 */
function GetPenisSize($name) {
    $tngsize = GetActorValue($name, "tngsize");
    $gender = strtolower(GetActorValue($name, "gender"));
    // Get the size stage (0-4 scale)
    $sizeStage = 2; // Default to average
    if (!HasKeyword($name, "TNG_Gentlewoman") && $gender == "female") {
        $sizeStage = -1;
    }
    elseif (HasKeyword($name, "TNG_XL") || ($tngsize == 4)) {
        $sizeStage = 4;
    }
    elseif (HasKeyword($name, "TNG_L") || ($tngsize == 3)) {
        $sizeStage = 3;
    }
    elseif (HasKeyword($name, "TNG_M") || HasKeyword($name, "TNG_DefaultSize") || ($tngsize == 2)) {
        $sizeStage = 2;
    }
    elseif (HasKeyword($name, "TNG_S") || ($tngsize == 1)) {
        $sizeStage = 1;
    }        
    elseif (HasKeyword($name, "TNG_XS") || ($tngsize == 0)) {
        $sizeStage = 0;
    }
    
    // Map stage to description
    $sizeDescription = "";
    switch ($sizeStage) {
        case 0: $sizeDescription = "Embarrassingly Tiny Prick"; break;
        case 1: $sizeDescription = "Small Cock"; break;
        case 2: $sizeDescription = "Average Sized Cock"; break;
        case 3: $sizeDescription = "Large Cock"; break;
        case 4: $sizeDescription = "Impressively Huge Cock, one of the biggest you've ever seen"; break;
        default: $sizeDescription = "";
    }
    
    
    if ($sizeDescription != "") {
        return "{$name} has an {$sizeDescription}. ";
    }
    
    return "";
}

/**
 * Build the equipment context
 * 
 * @param array $params Parameters including herika_name, player_name, target
 * @return string Formatted equipment context
 */
function BuildEquipmentContext($params) {
    // Determine which character we're building context for
    $herika_name = $params['herika_name'];
    $character = $herika_name;
    
    // This function calls the existing GetUnifiedEquipmentContext function
    return GetUnifiedEquipmentContext($character);
}

/**
 * Build the tattoo context
 * 
 * @param array $params Parameters including herika_name, player_name, target
 * @return string Formatted tattoo context
 */
function BuildTattooContext($params) {
    $character = $params['herika_name'];
    $ret = GetTattooContext($character);
    return $ret;
}

/**
 * Build the arousal context
 * 
 * @param array $params Parameters including herika_name, player_name, target
 * @return string Formatted arousal context
 */
function BuildArousalContext($params) {
    // Determine which character we're building context for
    $character = $params['herika_name'];
    if ($character == "The Narrator") {
        $character = $params['player_name'];
    }
    $arousal = GetActorValue($character, "arousal");
    
    $ret = "";
    if (isset($arousal) && $arousal !== "") {
        // Convert percentage (0-99) to stage (0-10)
        $stage = min(10, floor(floatval($arousal) / 10));
        
        $arousalDesc = "";
        switch ($stage) {
            case 0: $arousalDesc = "Completely Satisfied and Content"; break;
            case 1: $arousalDesc = "Fulfilled with No Desire"; break;
            case 2: $arousalDesc = "Not Aroused"; break;
            case 3: $arousalDesc = "Slightly Aroused and Curious"; break;
            case 4: $arousalDesc = "Moderately Aroused and Interested"; break;
            case 5: $arousalDesc = "Noticeably Aroused and Eager"; break;
            case 6: $arousalDesc = "Quite Aroused and Desiring"; break;
            case 7: $arousalDesc = "Very Horny and Wanting"; break;
            case 8: $arousalDesc = "Intensely Horny and Needing"; break;
            case 9: $arousalDesc = "Burning with Passionate Desire"; break;
            case 10: $arousalDesc = "Overwhelmingly Horny and Desperate"; break;
            default: $arousalDesc = "Unknown";
        }
        
        // Add the numerical rating
        $arousalDesc .= " ({$stage}/10)";
        
        $ret .= "{$character} is {$arousalDesc} (arousal).\n";
    }
    
    return $ret;
}

/**
 * Build the fertility context
 * 
 * @param array $params Parameters including herika_name, player_name, target
 * @return string Formatted fertility context
 */
function BuildFertilityContext($params) {
    $character = $params['herika_name'];
    if ($character == "The Narrator") {
        $character = $params['player_name'];
    }
    // This function would call the existing GetFertilityContext function
    return GetFertilityContext($character);
}

/**
 * Build the following context
 * 
 * @param array $params Parameters including herika_name, player_name, target
 * @return string Formatted following context
 */
function BuildFollowingContext($params) {
    $character = $params['herika_name'];
    $player_name = $params['player_name'];
    
    if (IsFollowing($character)) {
        return "{$character} is following, walking, or escorting {$player_name}";
    }
    
    return "";
}

/**
 * Build the survival context
 * 
 * @param array $params Parameters including herika_name, player_name, target
 * @return string Formatted survival context
 */
function BuildSurvivalContext($params) {
    // Determine which character we're building context for
    $herika_name = $params['herika_name'];
    $character = $herika_name;
    $player_name = $params['player_name'];
    if ($character == "The Narrator") {
        $character = $player_name;
    }
    
    // Initialize all stage variables
    $hungerStage = null;
    $thirstStage = null;
    $fatigueStage = null;
    $coldStage = null;

    if (IsModEnabled("Sunhelm")) {
        // Get stage values for Sunhelm (0-5 scale)
        $hungerValue = GetActorValue($character, "hunger");
        $thirstValue = GetActorValue($character, "thirst");
        $fatigueValue = GetActorValue($character, "fatigue");
        $coldValue = GetActorValue($character, "cold");
        
        // Check if the values exist (including zero values)
        if (isset($hungerValue) && $hungerValue !== "") $hungerStage = intval($hungerValue);
        if (isset($thirstValue) && $thirstValue !== "") $thirstStage = intval($thirstValue);
        if (isset($fatigueValue) && $fatigueValue !== "") $fatigueStage = intval($fatigueValue);
        if (isset($coldValue) && $coldValue !== "") $coldStage = intval($coldValue);
    }
    else {
        // For other mods, try to convert percentage values to stages
        $hungerPercent = GetActorValue($character, "hunger");
        $thirstPercent = GetActorValue($character, "thirst");
        $fatiguePercent = GetActorValue($character, "fatigue");
        $coldPercent = GetActorValue($character, "cold");
        
        // Convert percentages to stages (0-5) if values exist (including zero values)
        if (isset($hungerPercent) && $hungerPercent !== "") $hungerStage = min(5, floor(floatval($hungerPercent) / 20));
        if (isset($thirstPercent) && $thirstPercent !== "") $thirstStage = min(5, floor(floatval($thirstPercent) / 20));
        if (isset($fatiguePercent) && $fatiguePercent !== "") $fatigueStage = min(5, floor(floatval($fatiguePercent) / 20));
        if (isset($coldPercent) && $coldPercent !== "") $coldStage = min(5, floor(floatval($coldPercent) / 20));
    }
    
    $ret = "";
    $pronouns = GetActorPronouns($character);
    
    // Hunger description
    if ($hungerStage !== null) {
        $hungerDesc = "";
        switch ($hungerStage) {
            case 0: $hungerDesc = "Well Fed"; break;
            case 1: $hungerDesc = "Satisfied"; break;
            case 2: $hungerDesc = "Peckish"; break;
            case 3: $hungerDesc = "Hungry"; break;
            case 4: $hungerDesc = "Ravenous"; break;
            case 5: $hungerDesc = "Starving"; break;
            default: $hungerDesc = "Unknown";
        }
        $ret .= "{$character} feels {$hungerDesc} (hunger).\n";
    }
    
    // Thirst description
    if ($thirstStage !== null) {
        $thirstDesc = "";
        switch ($thirstStage) {
            case 0: $thirstDesc = "Quenched"; break;
            case 1: $thirstDesc = "Sated"; break;
            case 2: $thirstDesc = "Thirsty"; break;
            case 3: $thirstDesc = "Parched"; break;
            case 4: $thirstDesc = "Dehydrated"; break;
            case 5: $thirstDesc = "Severely Dehydrated"; break;
            default: $thirstDesc = "Unknown";
        }
        $ret .= "{$character} is {$thirstDesc} (thirst).\n";
    }
    
    // Fatigue description
    if ($fatigueStage !== null) {
        $fatigueDesc = "";
        switch ($fatigueStage) {
            case 0: $fatigueDesc = "Well Rested"; break;
            case 1: $fatigueDesc = "Rested"; break;
            case 2: $fatigueDesc = "Slightly Tired"; break;
            case 3: $fatigueDesc = "Tired"; break;
            case 4: $fatigueDesc = "Weary"; break;
            case 5: $fatigueDesc = "Exhausted"; break;
            default: $fatigueDesc = "Unknown";
        }
        $ret .= "{$character} is {$fatigueDesc} (energy).\n";
    }
    
    // Cold description
    if ($coldStage !== null) {
        $coldDesc = "";
        switch ($coldStage) {
            case 0: $coldDesc = "Warm"; break;
            case 1: $coldDesc = "Comfortable"; break;
            case 2: $coldDesc = "Chilly"; break;
            case 3: $coldDesc = "Cold"; break;
            case 4: $coldDesc = "Freezing"; break;
            case 5: $coldDesc = "Frigid"; break;
            default: $coldDesc = "Unknown";
        }
        $ret .= "{$character} feels {$coldDesc} (temperature).\n";
    }
    
    return $ret;
}


/**
 * Build the bounty context
 * 
 * @param array $params Parameters including herika_name, player_name, target
 * @return string Formatted bounty context
 */
function BuildBountyContext($params) {
    $herika_name = $params['herika_name'];
    $player_name = $params['player_name'];
    $target = $params['target'];

    // Check conditions to show bounty:
    // 1. If we are talking to the narrator OR
    // 2. If the player is in the conversation AND the target is a guard
    $showBounty = false;
    
    // Condition 1: Talking to the narrator
    if ($herika_name == "The Narrator") {
        $showBounty = true;
    }
    // Condition 2: Player is in conversation AND target is a guard
    else if ($player_name == $target && (HasKeyword($target, "GuardFaction")) || HasKeyword($target, "Guard Faction")) {
        $showBounty = true;
    }
    
    // Only return bounty context if conditions are met
    if ($showBounty) {
        return GetBountyContext($player_name);
    }
    
    return "";
}

/**
 * Build the mind influence context
 * 
 * @param array $params Parameters including herika_name, player_name, target
 * @return string Formatted mind influence context
 */
function BuildMindInfluenceContext($params) {
    $herika_name = $params['herika_name'];
    $player_name = $params['player_name'];

    if ($herika_name != "The Narrator") {
        return "";
    }
    
    // Only supported for the player at the moment
    $mindState = GetMindInfluenceState($player_name);
    if ($mindState == "normal") {
        return "";
    }
    
    // This function would call the existing GetMindInfluenceContext function
    return GetMindInfluenceContext($mindState);
} 

