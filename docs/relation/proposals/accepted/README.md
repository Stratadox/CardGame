# Proposal Relation: Accepted
Refers to the [accepted proposals](../../../resource/proposals/accepted/README.md) 
that have been accepted by a particular [player account](../../../resource/account/overview/README.md).

- Requires [authorisation](../../../security/Authorisation.md)
- Only accessible by the [recipient](../to/README.md) of the proposals
- Accepts an optional query string parameter `since`, containing a ISO 8601 date 
value. For example: `?since=2020-02-12T15:19:21+00:00`
- Retrieves the collection of [accepted proposals](../../../resource/proposals/accepted/README.md)
- HTTP method: GET
