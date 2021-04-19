# Unity / C# Dev Notes

---

> Akira Fudo - Valheim Modding Discord

Save Game Location: `%HOMEDRIVE%%HOMEPATH%\AppData\LocalLow\IronGate\Valheim`
Unity Version: 2019.4.16.

---

> 1010101110#4526 - Valheim Modding Discord

## == `FejdStartup` class is the main menu and entry point for server ==

`FejdStartup::Awake()`
- entry point for server
- gets command line args and creates the world and networking code

`FejdStartup::Start()`
- entry point for client
- initialize gui, input, objects, starts main menu
- loads all character files from disk GetAllPlayerProfiles()
- default AppData\LocalLow\IronGate\Valheim\characters 

`FejdStartup::OnStartGame()`
- client clicks main menu start button
- starts character selection

`FejdStartup::OnCharacterStart()` when player hits start on character selection
- gets selected player from loaded characters array
- sets PlayerPrefs["profile"] selected player filename
- sets Game.SetProfile selected player filename
- opens world selection / join menu

`FejdStartup::OnWorldStart()` when player hits start on world selection screen
- passes ZNet the world filename
- transitions to Game 

`FejdStartup::OnJoinStart() > FejdStartup::JoinServer()` when player joins a community server
- ZNet sets server to null
- ZNet sets serverhost to steamhostid from server list
- transitions to Game
== ZNet class is the networking code ==
`ZNet::Awake()`
- if server
-- reads adminlist.txt, bannedlist.txt, permittedlist.txt
-- if open (visible to community) open socket and register to matchmaking list
-- WorldGenerator::Initialize(world file name)
-- ZNet::LoadWorld()

- if client
-- `ZNet::Connect(steam server id)`

`ZNet::Connect()`
- creates ZNetPeer
- `ZNet::OnNewConnection(peer)`

`ZNet::OnNewConnection()`
- sets up rpc connecitons to server
- invokes ZNet::RPC_ServerHandshake()

`ZNet::RPC_ServerHandshake()`
`ZNet::RPC_ClientHandshake()`
`ZNet::SendPeerInfo()`
- server password dialog
- send password and character name

`ZNet::RPC_PeerInfo()` finish server/client connection here
- if server 
-- checks peer is valid (version, password, ban, whitelist, #players, already connected)

- if client
-- setup local world
-- `WorldGenerator::Initialize()`

-- finished connection!
`ZNet.m_connectionStatus = ZNet.ConnectionStatus.Connected`;

- `ZDOMan::AddPeer()`
-- Add peer to object manager so we keep all world objects in sync
- `ZRoutedRpc::AddPeer()`
-- Add to rpc peers so we can call rpcs on it

--- 
