# Authorisation
Authorisation refers to the practice of checking whether the [authenticated](Authentication.md) 
user has permission to perform a particular action or see a certain resource.

## HATEOAS
Any [relation](../relation) that cannot be followed due to missing permissions, is 
excluded from the [resource](../resource) representations.

HATEOAS alone, however, is not nearly enough to secure the authorisation flow.
Since the resource representation layer sits at the very edge of the system, 
they are bound to be less thoroughly tested than [deeper layers](../../notes/DDD.md). 
(although they will still have their expected output checked against the actual 
results, [of course](../../notes/TDD.md)) Additionally, [Url Hacking](#url-hacking) 
could occur. The HATEOAS layer is thus more of an indicator to the client than 
a reliable access violation prevention.

## Url Hacking
In some cases, it might be possible to look at the URIs yielded by the relations 
available to the resources the user has access to, and change one or more 
parameters in the hope of gaining access to restricted actions or material.

To prevent players with malicious intent (like *cheaters* and *hackers*) from 
exceeding their access levels, all parameters that serve to identify the player 
are taken from the [authentication claims](Authentication.md#claims) rather than 
the URI parameters. As such, the role of many URI parameters is reduced to 
labeling for caching purposes, as well as potentially logging discrepancies to 
detect attempts at cheating or hacking.

## Domain model
The source of truth of all authorisation logic is the [domain model](../../notes/DDD.md). 
This layer determines whether the intended action is valid under the particular 
circumstances and - if it is - performs the action and communicates its 
consequences.

In order to [exclude relations from resources](#hateoas), the domain model 
communicates the possibilities to the [read models](../../notes/CQRS.md#queries) 
through [domain events](../../notes/DDD.md#domain-events).
Those read models are, in turn, used to produce the resource representations 
that get sent to the client.

## Read access
The domain layer protects write-access - access to protected read models is 
handled by the UI layer, although based on messages from the domain model.
