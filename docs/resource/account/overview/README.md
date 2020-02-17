# Account Overview
The account overview contains information on the player account, such as its 
id and current status.

## Properties
| Name      | Description
| ---       | ---
| id        | The account identifier, uuid
| type      | The type of account, such as "Guest", "Member", "Mod" or "Admin"

## Links
- [proposals:open](../../../relation/proposals/open/README.md) - Proposals 
  made to this account that have not been accepted nor expired yet. Available 
  only to the owner of the account.
- [proposals:accepted](../../../relation/proposals/accepted/README.md) - 
  Proposals that have been accepted by this account. Available only to the owner 
  of the account.
- [proposals:successful](../../../relation/proposals/successful/README.md) -
  Proposals made by this account that have been accepted by the recipient. 
  Available only to the owner of the account.
- [proposals:propose](../../../relation/proposals/propose/README.md) - 
  Proposes a match to this player account. Available only to authenticated users 
  that are not the owner of this account.

## Media types
- `application/prs.stratadox.card-game.account.v1.overview.hal+json`
  - Schema: [account/overview.schema.json](../../../../schema/account/v1/overview.schema.json)
  - Example: [account/example/overview-mine.json](../../../../schema/account/v1/example/overview-mine.json)
  - Example: [account/example/overview-other.json](../../../../schema/account/v1/example/overview-other.json)
- `application/prs.stratadox.card-game.account.v1.overview.hal+xml`
  - Schema: [account/overview.xsd](../../../../schema/account/v1/overview.xsd)
  - Example: [account/example/overview-mine.xml](../../../../schema/account/v1/example/overview-mine.xml)
  - Example: [account/example/overview-other.xml](../../../../schema/account/v1/example/overview-other.xml)
