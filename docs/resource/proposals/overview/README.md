# Match Proposal
A match proposal is an invitation to play a match.

## Properties
| Name          | Description
| ---           | ---
| id            | The proposal identifier, uuid
| from          | The player that proposed the match
| to            | The player that received the proposal
| valid-until   | The point in time when the proposal expires
| accepted      | Whether the proposal has been accepted

# Links
- [proposal:from](../../../relation/proposals/from/README.md) - The player that 
  proposed the match
- [proposal:to](../../../relation/proposals/to/README.md) - The player that 
  received the proposal
- [proposal:accept](../../../relation/proposals/accept/README.md) - Accepts the 
  proposal (available to the recipient while the proposal is open)
- [match:await](../../../relation/match/await/README.md) - The status monitor to 
  wait for the match for this proposal (available until the match has started)
- [match:play](../../../relation/match/play/README.md) - The match that resulted 
  from this proposal (available once the match has started)

## Media types
- `application/prs.stratadox.card-game.proposals.v1.overview.hal+json`
  - Schema: [proposals/overview.schema.json](../../../../schema/proposals/v1/overview.schema.json)
  - Example: [proposals/example/overview.json](../../../../schema/proposals/v1/example/overview.json)
- `application/prs.stratadox.card-game.proposals.v1.overview.hal+xml`
  - Schema: [proposals/overview.xsd](../../../../schema/proposals/v1/overview.xsd)
  - Example: [proposals/example/overview.xml](../../../../schema/proposals/v1/example/overview.xml)
