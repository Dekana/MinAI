<?php
require_once("config.php");
require_once("util.php");
require_once("sexPrompts.php");
require_once("customintegrations.php");

// Custom command / third party integrations support
// Done here, as this is mounted early in main.php
ProcessIntegrations();
$enforceLength = "You MUST Respond with no more than two sentences.";

$GLOBALS["PROMPTS"]["radiant"]= [
    "cue"=>[
        "write dialogue for {$GLOBALS["HERIKA_NAME"]}.{$GLOBALS["TEMPLATE_DIALOG"]}  "
    ],
    "player_request"=>[    
        "The Narrator: {$GLOBALS["HERIKA_NAME"]} starts a dialogue with {$GLOBALS["target"]} about a relevant topic",
    ]
];

$GLOBALS["PROMPTS"]["minai_force_rechat"]= [
    "cue"=>[
        "{$GLOBALS["HERIKA_NAME"]} responds to {$GLOBALS["target"]}.{$GLOBALS["TEMPLATE_DIALOG"]}  "
    ],
    "player_request"=>[    
        "The Narrator: {$GLOBALS["HERIKA_NAME"]} responds to {$GLOBALS["target"]} about the ongoing conversation",
    ]
];

$GLOBALS["PROMPTS"]["radiantsearchinghostile"]= [
    "cue"=>[
        "write dialogue for {$GLOBALS["HERIKA_NAME"]} who is responding in a hostile, and concerned manner.{$GLOBALS["TEMPLATE_DIALOG"]}  $enforceLength"
    ], 
    "player_request"=>[    
        "The Narrator: {$GLOBALS["HERIKA_NAME"]} is currently searching the area for hostiles, and asks who is there?",
        "The Narrator: {$GLOBALS["HERIKA_NAME"]} is currently searching the area for hostiles, and starts threatening what he's going to do when he finds them",
    ]
];
$GLOBALS["PROMPTS"]["radiantsearchingfriend"]= [
    "cue"=>[
        "write dialogue for {$GLOBALS["HERIKA_NAME"]} who is responding in a concerned manner.{$GLOBALS["TEMPLATE_DIALOG"]}  $enforceLength"
    ], 
    "player_request"=>[    
        "The Narrator: {$GLOBALS["HERIKA_NAME"]} is currently searching the area for hostiles, and starts a dialogue with their ally {$GLOBALS["target"]} about this topic",
    ]
];
$GLOBALS["PROMPTS"]["radiantcombathostile"]= [
    "cue"=>[
        "write dialogue for {$GLOBALS["HERIKA_NAME"]} who is responding in a hostile and combative manner.{$GLOBALS["TEMPLATE_DIALOG"]}  $enforceLength"
    ], 
    "player_request"=>[    
        "The Narrator: {$GLOBALS["HERIKA_NAME"]} is engaged in deadly combat with {$GLOBALS["target"]} and taunts them",
        "The Narrator: {$GLOBALS["HERIKA_NAME"]} is engaged in deadly combat with {$GLOBALS["target"]} and trash-talks them",
        "The Narrator: {$GLOBALS["HERIKA_NAME"]} is engaged in deadly combat with {$GLOBALS["target"]} and boasts about what they will do after {$GLOBALS["HERIKA_NAME"]} has defeated them ",
    ]
];
$GLOBALS["PROMPTS"]["radiantcombatfriend"]= [
    "cue"=>[
        "write dialogue for {$GLOBALS["HERIKA_NAME"]} who is responding in a tense, serious manner.{$GLOBALS["TEMPLATE_DIALOG"]}  $enforceLength"
    ], 
    "player_request"=>[    
        "The Narrator: {$GLOBALS["HERIKA_NAME"]} is teamed up with {$GLOBALS["target"]} in deadly combat against someone and talks about the battle",
        "The Narrator: {$GLOBALS["HERIKA_NAME"]} is teamed up with {$GLOBALS["target"]} in deadly combat against someone and asks for help",
    ]
];

$GLOBALS["PROMPTS"]["minai_combatendvictory"]= [
    "cue"=>[
        "({$GLOBALS["HERIKA_NAME"]} comments about foes defeated) {$GLOBALS["TEMPLATE_DIALOG"]}",
        "({$GLOBALS["HERIKA_NAME"]} curses the defeated enemies.) {$GLOBALS["TEMPLATE_DIALOG"]}",
        "({$GLOBALS["HERIKA_NAME"]} insults the defeated enemies with anger) {$GLOBALS["TEMPLATE_DIALOG"]}",
        "({$GLOBALS["HERIKA_NAME"]} makes a joke about the defeated enemies) {$GLOBALS["TEMPLATE_DIALOG"]}",
        "({$GLOBALS["HERIKA_NAME"]} makes a comment about the type of enemies that was defeated) {$GLOBALS["TEMPLATE_DIALOG"]}",
        "({$GLOBALS["HERIKA_NAME"]} notes something peculiar about last enemy defeated) {$GLOBALS["TEMPLATE_DIALOG"]}"
    ],
    "extra"=>["force_tokens_max"=>"50","dontuse"=>(time()%10!=0)]   //10% chance
];

$GLOBALS["PROMPTS"]["minai_bleedoutself"]= [
    "cue"=>[
        "{$GLOBALS["HERIKA_NAME"]} calls out for help after being badly wounded! {$GLOBALS["TEMPLATE_DIALOG"]} ",
        "{$GLOBALS["HERIKA_NAME"]} cries out in pain after being badly wounded! {$GLOBALS["TEMPLATE_DIALOG"]} ",
        "{$GLOBALS["HERIKA_NAME"]} expresses their resolve after being badly wounded! {$GLOBALS["TEMPLATE_DIALOG"]} ",
    ],
];


if (IsFollower($GLOBALS["HERIKA_NAME"])) {
    $GLOBALS["PROMPTS"]["minai_combatenddefeat"]= [
        "cue"=>[
            "({$GLOBALS["HERIKA_NAME"]} laments having been defeated in combat {$GLOBALS["TEMPLATE_DIALOG"]}"
        ]
    ];
} else {
    $GLOBALS["PROMPTS"]["minai_combatenddefeat"]= [
        "cue"=>[
            "({$GLOBALS["HERIKA_NAME"]} gloats about defeating {$GLOBALS["target"]} in combat and boasts about what they will do next {$GLOBALS["TEMPLATE_DIALOG"]}"
        ]
    ];
}

function SetInputPrompts($prompt) {
    error_log("minai: Overriding input prompts for combat for {$GLOBALS["HERIKA_NAME"]}");
    $GLOBALS["PROMPTS"]["inputtext"]= $prompt;
    $GLOBALS["PROMPTS"]["inputtext_s"]= $prompt;
    $GLOBALS["PROMPTS"]["ginputtext"]= $prompt;
}
// Override default prompts for combat dialogue
if (isset($GLOBALS["gameRequest"]) && in_array(strtolower($GLOBALS["gameRequest"][0]), ["inputtext","inputtext_s","ginputtext"])) {
    $inCombat = IsEnabled($GLOBALS["HERIKA_NAME"], "inCombat");
    $hostile = IsEnabled($GLOBALS["HERIKA_NAME"], "hostileToPlayer");
    $combatPrompt = [
        "cue"=>[
            "{$GLOBALS["TEMPLATE_ACTION"]} {$GLOBALS["HERIKA_NAME"]} is currently engaged in deadly combat and replies to {$GLOBALS["PLAYER_NAME"]}. {$GLOBALS["TEMPLATE_DIALOG"]} {$GLOBALS["MAXIMUM_WORDS"]}"
        ]
        // Prompt is implicit
    ];
    $hostilePrompt = [
        "cue"=>[
            "{$GLOBALS["TEMPLATE_ACTION"]} {$GLOBALS["HERIKA_NAME"]} is currently hostile to {$GLOBALS["PLAYER_NAME"]} and replies in a hostile manner to {$GLOBALS["PLAYER_NAME"]}. {$GLOBALS["TEMPLATE_DIALOG"]} {$GLOBALS["MAXIMUM_WORDS"]}"
        ]
        // Prompt is implicit
    ];
    $hostileCombatPrompt = [
        "cue"=>[
            "{$GLOBALS["TEMPLATE_ACTION"]} {$GLOBALS["HERIKA_NAME"]} is currently engaged in deadly combat against {$GLOBALS["PLAYER_NAME"]} and replies in a hostile manner to {$GLOBALS["PLAYER_NAME"]}. {$GLOBALS["TEMPLATE_DIALOG"]} {$GLOBALS["MAXIMUM_WORDS"]}"
        ]
        // Prompt is implicit
    ];
    if ($hostile && $inCombat)
        SetInputPrompts($hostileCombatPrompt);
    elseif ($inCombat)
        SetInputPrompts($combatPrompt);
    elseif ($hostile)
        SetInputPrompts($hostilePrompt);
}

if (isset($GLOBALS["self_narrator"]) && $GLOBALS["self_narrator"] && $GLOBALS["HERIKA_NAME"] == "The Narrator") {
    $GLOBALS["PROMPTS"]["diary"] = [ 
        "cue"=>["Please write a summary of {$GLOBALS["PLAYER_NAME"]}'s recent thoughts, feelings, and adventures. Respond as if you are writing this into a private diary, including completely unfiltered thoughts and feelings, emphasizing in detail any intimate encounters that may have occurred. WRITE AS IF YOU ARE {$GLOBALS["PLAYER_NAME"]}."],
        "extra"=>["force_tokens_max"=>0]
    ];
}
?>
