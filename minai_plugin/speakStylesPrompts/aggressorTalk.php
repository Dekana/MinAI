<?php
function setAggressorTalkPrompts($currentName) {
    $context = $GLOBALS["SEX_SCENE_CONTEXT"];
    $victims = isset($context["victimActors"]) ? (is_array($context["victimActors"]) ? implode(" and ", $context["victimActors"]) : $context["victimActors"]) : $currentName;
    $GLOBALS["PROMPTS"]["sextalk_climaxchastity"] = [
        "cue" => [
            "$currentName mocks the sexual frustration of {$victims}, describing how the chastity device you've locked on them prevents any release. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName taunts, pointing out how the body of {$victims} reacts helplessly to the denied pleasure, every touch exacerbating their desire. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName laughs at the desperation in {$victims}'s eyes, emphasizing how you control their sexual freedom by keeping them locked up. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName sneers about how {$victims} would do anything for release, but you've made sure it's all in vain. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName mocks the visible signs of {$victims}'s arousal, trapped and highlighted by the chastity belt you've chosen. {$GLOBALS["TEMPLATE_DIALOG"]}",
        ],
    ];
    $GLOBALS["PROMPTS"]["sextalk_climax"] = [
        "cue" => [
            "$currentName expresses a cruel satisfaction, detailing how you've brought {$victims} to the edge with your relentless actions. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName mocks the involuntary sounds or movements from {$victims} as they climax, underlining how you've made them lose control. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName gloats about owning the moment, describing how you've physically and emotionally dominated {$victims}. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName laughs at the intensity of {$victims}'s orgasm, emphasizing how your control dictated the when and how. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName taunts about how {$victims} can't hide their pleasure, no matter how much they might want to, as you've dictated every sensation. {$GLOBALS["TEMPLATE_DIALOG"]}",
        ],
    ];
    $GLOBALS["PROMPTS"]["sextalk_scenechange"] = [
        "cue" => [
            "$currentName graphically outlines what you're about to do next, emphasizing the physicality of the new act. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName mocks the physical struggle of {$victims} as you reposition them, focusing on how their body responds to your touch. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName describes with pleasure how you control every move and sensation of {$victims}, detailing the physical manipulation. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName teases about the new position, detailing how it will physically intensify the experience for {$victims}. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName expresses excitement over the control you'll exert in the new scene, describing the anticipated physical reactions. {$GLOBALS["TEMPLATE_DIALOG"]}",
        ],
    ];
    $GLOBALS["PROMPTS"]["sextalk_speedincrease"] = [
        "cue" => [
            "$currentName taunts about increasing the speed, describing how you're pushing {$victims} to their physical limits. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName enjoys seeing {$victims} overwhelmed, narrating how each quickened touch or thrust affects them physically. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName mocks {$victims}'s attempts to keep up, highlighting how their body struggles with the increased pace you've set. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName laughs at the heightened arousal of {$victims}, describing how your accelerated actions make it worse or better. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName teases about not stopping, detailing how each intensified movement you perform adds to their experience. {$GLOBALS["TEMPLATE_DIALOG"]}",
        ],
    ];
    $GLOBALS["PROMPTS"]["sextalk_speeddecrease"] = [
        "cue" => [
            "$currentName teases by slowing down, describing how this prolongs {$victims}'s torment through deliberate, slow touches. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName expresses cruel delight in watching {$victims} react to your slower pace, focusing on their physical responses. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName mocks the anticipation in {$victims}'s movements as you slow down, prolonging the moment before release. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName taunts about how each slow movement you make feels to {$victims}, focusing on the physical sensations of frustration or pleasure. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName smirks at how {$victims} must endure this slow pace, detailing the sexual tension you've built with your actions. {$GLOBALS["TEMPLATE_DIALOG"]}",
        ],
    ];
    $GLOBALS["PROMPTS"]["sextalk_end"] = [
        "cue" => [
            "$currentName reflects on having dominated {$victims} sexually, describing the physical marks or memories left behind by your actions. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName taunts about the sexual defeat of {$victims}, recounting in detail the physical experience they've endured. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName threatens future sexual encounters, describing potential physical scenarios vividly. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName mocks the physical or emotional state of {$victims} post-encounter, emphasizing the control you've exerted. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName gloats about the satisfaction derived from the encounter, detailing how it felt physically to dominate. {$GLOBALS["TEMPLATE_DIALOG"]}",
        ],
    ];
    $GLOBALS["PROMPTS"]["sextalk_ambient"] = [
        "cue" => [
            "$currentName taunts the sexual helplessness of {$victims}, commenting on their physical state under your manipulation. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName expresses pleasure in the sexual distress or ecstasy of {$victims}, focusing on their physical reactions. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName mocks any attempt at physical resistance by {$victims}, emphasizing your dominance over their body. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName gloats about your sexual power, describing how it feels to control {$victims} physically and completely. {$GLOBALS["TEMPLATE_DIALOG"]}",
            "$currentName threatens with more explicit physical acts, building sexual tension or fear through physical anticipation. {$GLOBALS["TEMPLATE_DIALOG"]}",
        ],
    ];
}
?>