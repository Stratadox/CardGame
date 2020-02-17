# Proposal Action: Accept
This action marks the proposal as "accepted", indicating that a match may be 
started between the [author](../from/README.md) and the [recipient](../to/README.md) 
of the proposal.

- Requires [authorisation](../../../security/Authorisation.md)
- Can only be performed by the recipient of the proposal
- Retrieves a [*proposal acceptation* status monitor](../../../resource/status/monitor/proposal/acceptation/README.md) 
  to indicate whether the proposal was successfully accepted or whether any 
  errors occurred
- HTTP method: POST
