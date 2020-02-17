# Match commencement monitor
The match commencement monitor is a [status monitor](../../README.md) for match 
commencement statuses. To complete means that the match has started.

It has the properties, links and media types of a generic status monitor, in 
addition to:

## Links
- [match:play](../../../../../relation/match/play/README.md) - The match that 
  has been started (only available for [`completed` status](../../README.md#properties))

## Media types
- `application/prs.stratadox.card-game.status.v1.monitor.hal+json`
  - Example: [status/example/commencement-pending.json](../../../../../../schema/status/v1/example/commencement-pending.json)
  - Example: [status/example/commencement-completed.json](../../../../../../schema/status/v1/example/commencement-completed.json)
- `application/prs.stratadox.card-game.status.v1.monitor.hal+xml`
  - Example: [status/example/commencement-pending.xml](../../../../../../schema/status/v1/example/commencement-pending.xml)
  - Example: [status/example/commencement-completed.xml](../../../../../../schema/status/v1/example/commencement-completed.xml)
