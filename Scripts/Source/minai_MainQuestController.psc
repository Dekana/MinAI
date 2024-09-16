ScriptName minai_MainQuestController extends Quest

GlobalVariable minai_WhichAI
actor playerRef

int property logLevel Auto

; AI
minai_Mantella minMantella
minai_AIFF minAIFF

; Modules
minai_Sex sex
minai_Survival survival
minai_Arousal arousal
minai_DeviousStuff devious

bool bHasMantella = False;
bool bHasAIFF = False;

Event OnInit()
  Maintenance()
EndEvent

Int Function GetVersion()
  return 14
EndFunction


Function Maintenance()
  playerRef = game.GetPlayer()
  if logLevel == 0
    logLevel = 3
  EndIf
  Info("Maintenance() - minai v" +GetVersion() + " initializing.")
  ; Register for Mod Events
  Info("Checking for installed mods...")

  minai_WhichAI = Game.GetFormFromFile(0x0907, "MinAI.esp") as GlobalVariable
  minMantella = (Self as Quest) as minai_Mantella
  minAIFF = (Self as Quest) as minai_AIFF
  sex = (Self as Quest) as minai_Sex
  survival = (Self as Quest) as minai_Survival
  arousal = (Self as Quest) as minai_Arousal
  devious = (Self as Quest) as minai_DeviousStuff
  
  sex.Maintenance(Self)
  survival.Maintenance(Self)
  arousal.Maintenance(Self)
  devious.Maintenance(Self)

  bHasMantella = (Game.GetModByName("Mantella.esp") != 255)
  bHasAIFF = (Game.GetModByName("AIAgent.esp") != 255)
  
  if bHasMantella
    minMantella.Maintenance(Self)
  EndIf
  if bHasAIFF
    minAIFF.Maintenance(Self)
    if (!minAIFF.IsInitialized())
      Debug.Notification("MinAI - First time setup complete. Save/reload to enable mod functionality")
      minAIFF.SetInitialized()
    EndIf
  EndIf
  Info("Initialization complete.")
EndFunction




Function RegisterAction(String eventLine)
  if bHasMantella
    minMantella.RegisterAction(eventLine)
  EndIf
EndFunction

Function RegisterEvent(String eventLine, string eventType = "")
  if bHasMantella
    minMantella.RegisterEvent(eventLine)
  EndIf
  if bHasAIFF
    if eventType == ""
      eventType = "info_sexscene"
    EndIf
    minAIFF.RegisterEvent(eventLine, eventType)
  EndIf
  
EndFunction

string Function GetActorName(actor akActor)
  return akActor.GetActorBase().GetName()
EndFunction


string Function GetYouYour(actor akCaster)
  if akCaster != playerRef
    return GetActorName(akCaster) + "'s"
  endif
  return "your"
EndFunction

int function CountMatch(string sayLine, string lineToMatch)
  int count = 0
  int index = 0
  while index != -1
    index = StringUtil.Find(sayLine, lineToMatch, index+1)
    count += 1
  endWhile
  return count
EndFunction


Function Log(String str, string lvl)
  Debug.Trace("[minai (" + lvl + ")]: " + str)
EndFunction

Function Error(String str)
  if logLevel >= 1
    Log(str, "ERROR")
  EndIf
EndFunction


Function Warn(String str)
  if logLevel >= 2
    Log(str, "WARN")
  EndIf
EndFunction


Function Info(String str)
  if logLevel >= 3
    Log(str, "INFO")
  EndIf
EndFunction

Function Debug(String str)
  if LogLevel >= 4
    Log(str, "DEBUG")
  EndIf
EndFunction