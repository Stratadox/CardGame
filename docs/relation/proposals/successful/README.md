# Proposal Relation: Successful
Refers to the [successful proposals](../../../resource/proposals/successful/README.md) 
that have been proposed by a particular [player account](../../../resource/account/overview/README.md), 
and [accepted](../accepted/README.md) by the [recipient](../to/README.md). 

- Requires [authorisation](../../../security/Authorisation.md)
- Only accessible by the [author](../from/README.md) of the proposals
- Accepts an optional query string parameter `since`, containing a ISO 8601 date 
value. For example: `?since=2020-02-12T15:19:21+00:00`
- Retrieves the collection of [successful proposals](../../../resource/proposals/successful/README.md)
- HTTP method: GET
