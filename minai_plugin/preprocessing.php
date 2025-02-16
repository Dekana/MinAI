<?php

require_once("util.php");
// TODO: Add an actual install routine to the HerikaServer proper to not do this every request.
InitiateDBTables();

function interceptRoleplayInput() {
    if (IsEnabled($GLOBALS["PLAYER_NAME"], "isRoleplaying") && (isPlayerInput() || $GLOBALS["gameRequest"][0] == "minai_roleplay")) {
        if ($GLOBALS["gameRequest"][0] == "minai_roleplay") {
            error_log("minai: Intercepting minai_roleplay.");
        }
        else {
            error_log("minai: Intercepting dialogue input for Translation. Original input: " . $GLOBALS["gameRequest"][3]);
        }
        
        // Initialize local variables with global defaults
        $PLAYER_NAME = $GLOBALS["PLAYER_NAME"];
        $PLAYER_BIOS = $GLOBALS["PLAYER_BIOS"];
        $HERIKA_NAME = $GLOBALS["HERIKA_NAME"];
        $HERIKA_PERS = $GLOBALS["HERIKA_PERS"];
        $CONNECTOR = $GLOBALS["CONNECTOR"];
        $HERIKA_DYNAMIC = $GLOBALS["HERIKA_DYNAMIC"];

        // Disable roleplay mode after processing begins
        SetEnabled($PLAYER_NAME, "isRoleplaying", false);

        // Import narrator profile which may override the above variables
        if (file_exists(GetNarratorConfigPath())) {
            error_log("minai: Using Narrator Profile");
            $path = GetNarratorConfigPath();    
            include($path);
        }
        
        SetEnabled($GLOBALS["PLAYER_NAME"], "isRoleplaying", false);
        
        // Get the original input and strip the player name prefix if it exists
        $originalInput = $GLOBALS["gameRequest"][3];
        
        // Extract any context information that's in parentheses
        $contextPrefix = "";
        if (preg_match('/^\((.*?)\)/', $originalInput, $matches)) {
            $contextPrefix = $matches[0];
            $originalInput = trim(substr($originalInput, strlen($contextPrefix)));
        }
        
        // Strip player name prefix if it exists
        $originalInput = preg_replace('/^' . preg_quote($PLAYER_NAME . ':') . '\s*/', '', $originalInput);
        $originalInput = trim($originalInput);

        // Get recent context - limit to last 5 exchanges for more focused context
        $lastNDataForContext = 10;
        $contextDataHistoric = DataLastDataExpandedFor("", $lastNDataForContext * -1);
        
        // Get info about location and NPCs
        $contextDataWorld = DataLastInfoFor("", -2);
        
        // Get lists of valid names and locations
        $nearbyActors = array_filter(array_map('trim', explode('|', DataBeingsInRange())));
        $possibleLocations = DataPosibleLocationsToGo();
        
        // Combine contexts
        $contextDataFull = array_merge($contextDataWorld, $contextDataHistoric);
        
        // Build messages array using config settings
        $messages = [];
        $settings = $GLOBALS['roleplay_settings'];
        
        // Get player pronouns
        $playerPronouns = GetActorPronouns($PLAYER_NAME);
        
        // Replace variables in system prompt and request
        $variableReplacements = [
            '#player_name#' => $PLAYER_NAME,
            '#player_bios#' => $PLAYER_BIOS,
            '#nearby_actors#' => implode(", ", $nearbyActors),
            '#nearby_locations#' => implode(", ", $possibleLocations),
            '#recent_events#' => implode("\n", array_map(function($ctx) { 
                return $ctx['content']; 
            }, array_slice($contextDataFull, -$settings['context_messages']))),
            '#player_subject#' => $playerPronouns['subject'],
            '#player_object#' => $playerPronouns['object'],
            '#player_possessive#' => $playerPronouns['possessive'],
            '#herika_dynamic#' => $HERIKA_DYNAMIC,
            '#original_input#' => $originalInput,
            '#instructions#' => $instructions
        ];

        // Apply replacements to system prompt
        $systemPrompt = str_replace(
            array_keys($variableReplacements),
            array_values($variableReplacements),
            $GLOBALS["gameRequest"][0] == "minai_roleplay" 
                ? $settings['roleplay_system_prompt']
                : $settings['system_prompt']
        );

        // Sort sections by their order if it exists, otherwise maintain config file order
        $sections = $settings['sections'];
        if (isset(reset($sections)['order'])) {
            uasort($sections, function($a, $b) {
                return ($a['order'] ?? 0) - ($b['order'] ?? 0);
            });
        }

        // Build the context message
        $contextMessage = '';
        foreach ($sections as $sectionName => $section) {
            if (!$section['enabled']) continue;

            if ($contextMessage !== '') {
                $contextMessage .= "\n\n";
            }

            $content = $section['header'] . "\n";
            $content .= str_replace(
                array_keys($variableReplacements),
                array_values($variableReplacements),
                $section['content']
            );

            $contextMessage .= $content;
        }

        // Build the messages array with proper spacing
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt . "\n\n"],
            ['role' => 'system', 'content' => $contextMessage],
            ['role' => 'user', 'content' => "\n" . str_replace(
                array_keys($variableReplacements),
                array_values($variableReplacements),
                $GLOBALS["gameRequest"][0] == "minai_roleplay" 
                    ? $settings['roleplay_request']
                    : $settings['translation_request']
            )]
        ];

        // Call LLM with specific parameters for dialogue generation
        $response = callLLM($messages, $CONNECTOR["openrouter"]["model"], [
            'temperature' => $CONNECTOR["openrouter"]["temperature"],
            'max_tokens' => 150
        ]);

        if ($response !== null) {
            // Clean up the response - remove quotes and ensure it's dialogue-ready
            $response = trim($response, "\"' \n");
            
            // Remove any character name prefixes the LLM might have added
            $response = preg_replace('/^' . preg_quote($PLAYER_NAME . ':') . '\s*/', '', $response);
            $response = preg_replace('/^' . preg_quote($PLAYER_NAME) . ':\s*/', '', $response);
            
            error_log("minai: Roleplay input transformed from \"{$originalInput}\" to \"{$response}\"");
            
            if ($GLOBALS["gameRequest"][0] == "minai_roleplay") {
                // rewrite as player input
                $GLOBALS["gameRequest"][0] = "inputtext";
                $GLOBALS["gameRequest"][3] = $response;
            }
            else {
                // Format the response with a single character name prefix
                $GLOBALS["gameRequest"][3] = $contextPrefix . $PLAYER_NAME . ": " . $response;
            }
            error_log("minai: Final gameRequest[3]: " . $GLOBALS["gameRequest"][3]);
        } else {
            error_log("minai: Failed to generate roleplay response, using original input");
        }

    }
}

interceptRoleplayInput();

