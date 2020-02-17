# Status monitor
The status monitor (generally accompanied by [HTTP status 202](https://tools.ietf.org/html/rfc7231#section-6.3.3)) 
indicates the current status of an asynchronous action.

## Properties
| Name      | Description
| :---      | :---
| status    | The current status, either `pending`, `rejected` or `completed`
| time      | The time at which the status was retrieved, useful for building [If-Modified-Since headers](https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.25) 
| reasons   | An optional list of rejection reasons

## Links
The status monitor may be extended with additional links when appropriate.
By default, the status monitor only contains a `self` relation, which should be 
accessed with [If-Modified-Since](https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.25) 
headers, whose the value should be taken from the `time` property, in order to 
refresh the status monitor without overtaxing the network.

## Media types
- `application/prs.stratadox.card-game.status.v1.monitor.hal+json`
  - Schema: [status/monitor.schema.json](../../../../schema/status/v1/monitor.schema.json)
  - Example: [status/example/monitor.json](../../../../schema/status/v1/example/monitor.json)
- `application/prs.stratadox.card-game.status.v1.monitor.hal+xml`
  - Schema: [status/monitor.xsd](../../../../schema/status/v1/monitor.xsd)
  - Example: [status/example/monitor.xml](../../../../schema/status/v1/example/monitor.xml)
