# Representational state transfer
At the time of writing, the only client for the application is the test suite.

Eventually (preferably sooner rather than later) a player-facing client should 
be developed. It will be interacting with the card game back end through a REST 
api.

In these times we're living, the most oft encountered retort is: "Why not 
GraphQL? They say it's more efficient!".

## REST vs GraphQL
When people compare GraphQL with REST, they often accidentally compare GraphQL 
with "regular" web API's. After all, REST API's are much rarer than "rest" API's.
In other words, the term REST has been used so often to describe non-REST API's 
that many, including most people who make said comparison, accidentally compare 
GraphQL with not-REST.

If the above sounds foreign to the reader, [please take the time to view this 
wonderful talk](https://www.youtube.com/watch?v=pspy1H6A3FM).

### GraphQL
Although GraphQL can be very powerful in situations that benefit from GraphQL, 
it comes with great cost. Not directly monetary costs, it's an open protocol, 
but it involves a major cost towards encapsulation. In order to provide clients 
with a consistent GraphQL experience, the data model has to be locked in place, 
and is difficult to change without breaking clients.

The power of GraphQL comes from its ability to give the client control over what 
kind of data the client receives. In a scenario where one data-rich provider 
supplies any number of third-party clients with the data they need, GraphQL can 
be a perfect fit.

### Semi-Rest
The "regular" web API's that are often called Rest API's, but that are not 
[restful](https://www.ics.uci.edu/~fielding/pubs/dissertation/rest_arch_style.htm), 
generally suffer from similar data model lock-ins as GraphQL. Most "Rest" API's 
resort to an explicit versioning system in the URI's from the start, for example 
"`example.com/api/v1/something`".

Such API's are often accompanied by an external documentation of the endpoints 
and the data they provide, such as OpenAPI specifications or extensive manuals.

Their main advantage over GraphQL is in the reduced implementation cost. While 
GraphQL requires a back-end that supports GraphQL queries and a frontend that 
defines all the queries, a "rest" API only needs generic endpoints and a basic, 
often framework-driven CRUD back-end and a client that handles predictably 
structured data.

### REST
A true [REST API](https://restfulapi.net/) is largely self-describing, and is 
able to evolve without explicit version numbers in the URI's nor suddenly 
breaking clients.

A true REST architecture informs the client about which "next steps" are 
available to the client.

API's that follow this pattern are largely self-describing and easy to change.
Due to the REST trait called Hypermedia As The Engine Of Application State, or 
HATEOAS, the response of a REST API will contain links to other potentially 
interesting URI's. Combined with HTTP features such as content negotiation and 
redirection status codes, evolving the API can easily be done in an on-going 
way, generally without breaking existing clients.

Document versioning is done through content negotiation, allowing clients a 
grace period on a per resource level.

## REST vs GraphQL vs "rest" in this project
While each of the considered designs have great benefits, a choice ought not be 
made based on the benefits by themselves, but by how well the benefits (and 
potential downsides) align with the situation at hand.

### GraphQL for this project
The advantage of GraphQL over semi-rest is that it allows the client to 
determine what information it wants to receive. By letting clients completely 
ignore new fields, clients can remain stable and adopt new functionality when 
they are ready for it.

By having the server expose a schema for all obtainable data, it gives clients 
a lot of control over the data. GraphQL's evolutionary model requires the server 
to maintain all existing objects and exposed fields indefinitely, incrementally 
adding new types and fields over time.

Although these features are incredibly useful for a data warehouse application 
that allow third party clients to use the available data in building tools and 
plugins, they are absolutely detrimental to this project.

Clients with control over the data is exactly what we *don't* want, especially 
in the match context: clients usually may not, for example, look into the other 
player's hand or at the order of the cards in their deck.

Additionally, being a digital game, the API is presumed to be subject of heavy 
change over time. Experimental features may be added - possibly later removed. 
Fundamental concepts, seemingly natural at the time of writing, may change in 
significant ways.

Such potential volatility in terms of the data model, as well as the heavy 
restrictions on what information the client is allowed to access, make GraphQL 
particularly unsuitable for this project.

### Semi-REST for this project
While presumably better suited for this project than GraphQL, semi-REST has 
several shortcomings when compared to real REST. With a semi-REST API, much of 
the game logic would have to be duplicated in the client, causing difficulties 
in maintainability and extensibility, as well as significantly adding to the 
overall development cost.

### REST for this project
Keeping session state with the client increases scalability and reliability of 
the system. Luckily, the Match context consists of very little session state: 
most of the relevant data is connected to the ongoing match itself, rather than 
the players' interactions.
The trick there, is to make sure that the client represents the right player 
number, instead of cheating. Existing identification/authentication libraries 
and/or encryption tools should be used to prevent this at the infrastructure 
level.

With players being allowed a specific, context-dependant set of actions at any 
point in a match, using hypermedia to guide the clients through the process 
seems a natural choice, at least in the Match context.

#### Resources
REST implies a layered system. Clients ought not be able to look further than 
the layer they are interacting with. Such abstraction is a must for this project, 
because players (or their clients) are not supposed to be seeing or interacting 
with all the internals of the match.

The resources that are accessible by the client through the REST interface, do 
not map one-to-one to the [domain aggregates](DDD.md#aggregates) or entities.

Aggregates in the domain layer are concerned with authorising and effectuating 
changes to the state of the game; REST resources are outward-facing and mainly 
concerned with user interaction.

While in the domain aggregates, modeling the entities after mental models like 
Decks, Hands and a Battlefield [proved impractical](DDD.md#evolution), they are 
excellent candidates for becoming REST resources. 
(Amongst many other REST resources..)

#### Example flow
In this example, the client wants to see the cards in their hand, through a 
previously discovered URI. 

They request `GET /match/xyz/hand/1` (with some authentication token to validate 
their access) and receive, for example, something in the following spirit:
```json
{
  "base": "https://example.com/foo",
  "self": "/match/xyz/hand/1",
  "match": "/match/xyz",
  "cards": {
    "1": "/cards/card-id-1",
    "2": "/cards/card-id-1002",
    "3": "/cards/card-id-26e828-41fsq981"
  },
  "actions": {
      "play": {
        "1": "/match/xyz/hand/1/card/1/play",
        "3": "/match/xyz/hand/1/card/3/play"
      },
      "mulligan": {
        "1": "/match/xyz/hand/1/card/1/mulligan",
        "2": "/match/xyz/hand/1/card/2/mulligan",
        "3": "/match/xyz/hand/1/card/3/mulligan"
      }
  }
}
```
Or:
```json
{
  "base": "https://example.com/foo",
  "self": "/match/xyz/hand/1",
  "match": "/match/xyz",
  "cards": [
    {
      "details": "/cards/card-id-1",
      "actions": {
        "play": "/match/xyz/hand/1/card/1/play",
        "mulligan": "/match/xyz/hand/1/card/1/mulligan"
      }
    },
    {
      "details": "/cards/card-id-1002",
      "actions": {
        "mulligan": "/match/xyz/hand/1/card/2/mulligan"
      }
    },
    {
      "details": "/cards/card-id-26e828-41fsq981",
      "actions": {
        "play": "/match/xyz/hand/1/card/3/play",
        "mulligan": "/match/xyz/hand/1/card/3/mulligan"
      }
    }
  ]
}
```

#### HATEOAS
A key takeaway in both versions is the presence of the uri's. 
Not hard-coding the uri's in the client, but having them discovered at runtime, 
provides several benefits.

Discoverable uri's decouple the client from the uri structure of the server. 
All uri's but the initial entry point could change, without affecting the 
experience of the client. Such design aids in letting clients and server evolve 
independently.

Another benefit, especially useful to this project, is that the client receives 
instructions about what it can do, and about *how* to do it. This alleviates a 
lot of responsibility of the client: it only needs to be able to interpret the 
response media types, not duplicate the game logic.

The reader might have noticed that in both examples, the "play" action for card 
2 was missing. A likely explanation would be that the card costs more mana that 
the player has available - but maybe the card can only be played after the *n*th 
turn, or because some other in-game condition isn't met.
The information at `/cards/card-id-1002` is presumably enough to indicate to the 
player why the card cannot be played at this time. The client itself does not 
even have to necessarily track the player's mana at all, save for display 
purposes.

This practice decouples the client from much of the internal game logic, freeing 
clients to focus on user experience and presentational logic.

#### Versioning
Syntax of the response is presumed to be subject to heavy change, and may go 
through several changes. In many cases, such as the example above, both versions 
of the response can be created from the same underlying data; those cases allow 
for graceful versioning, where clients can request their preferred version 
through content negotiation.

There are other scenario's, in which graceful versioning is either very 
difficult or outright impossible. Both representations of the Hand resource 
imply that a Card can be played or not be played, and that's that. While this 
may ring true for most Cards, such as Units, Mana Pools and most Spells, the 
assumption does not hold for Equipment or some Spells that have a specific 
target.

Newer clients may be able to interpret, for instance:
```json
// (more cards here)
{
  "details": "/cards/card-id-26e828-41fsq981",
  "actions": {
    "play": {
      "2": {
        "1": "/match/xyz/hand/1/card/3/play?player=2&unit=1",
        "2": "/match/xyz/hand/1/card/3/play?player=2&unit=2"
      }
    },
    "mulligan": "/match/xyz/hand/1/card/3/mulligan"
  }
}
```
...and know that they can target units 1 or 2 of player 2. Older clients will 
probably be unaware of this change, and would potentially throw a TypeError by 
trying to make a request to an object, rather than a uri.

If the goal is to not break existing clients, how to deliver this new feature?

The first step is to apply content negotiation to prevent older clients from 
receiving a response they cannot interpret.
As a result, the older client still requests `/match/xyz/hand/1/card/3/play`, 
without informing the server which unit of which player should be targeted.
Alternatively, the uri given in the older representation can be changed to, for 
example, `/match/xyz/hand/1/card/3/play/unknown-target`.

The server, having released the newer version with backwards-compatibility in 
mind, is aware this might happen. 

Instead of processing the request as-is, it responds with a 400 (bad request) or 
a 422 (unprocessable entity) status, with a response body that includes a 
redirection uri, something in the spirit of:
```json
{
  "get-instead": "/match/xyz/hand/1/card/3/play/where",
  "need-also": "card-target"
}
```

If the older client has a targeting system, it might use content negotiation to 
request the response of the `get-instead` request in a json or xml format, which 
it can interpret to smoothly handle the process. Something like:
```json
{
  "options": {
    "2": {
      "1": "/match/xyz/hand/1/card/3/play?player=2&unit=1",
      "2": "/match/xyz/hand/1/card/3/play?player=2&unit=2"
    }
  }
}
```
As "last resort", the `get-instead` uri might respond with a form, containing 
one or more multiple choice fields in html:
```html
<html>
<body>
<form target="/match/xyz/hand/1/card/3/" method="post">
    Select target: 
    <input type="hidden" name="player" value="2" />
    <select name="unit">
        <option value="1">Name of unit 1</option>
        <option value="2">Name of unit 2</option>
    </select>
    <input type="submit" value="Ok">
</form>
</body>
</html>
```

Notice that, in all cases, the clients - even those so called "older" clients, 
which in practice haven't been written yet - are supposed to be built for 
failure.

#### Build for failure
All clients should be built with the realisation in mind that not everything is 
possible. Unreliable networks, slow connections, protocol updates... plenty of 
things from the cold realms of reality can ruin the utopian world of hopes and 
dreams.

When instead of naively hoping for the best, the client anticipates failure and 
acts to mitigate any problems whenever possible, many issues can be prevented 
from seriously harming the user experience.

Impact of timed-out network requests can be reduced for idempotent operations by 
simply retrying the request at a slightly later time. When the repeated request 
succeeds, the user might notice slowness, but is not otherwise blocked from 
continuing their intended course - even though the initial request failed.

In the same spirit, automatically looking for a fallback mechanism when a 
feature "does not work anymore" (such as the 400 response in our spell casting
example) might somewhat reduce the user experience (because a html drop-down is 
less intuitive than clicking on one of the cards) but does not prevent the 
continuation of the gameplay.

Clients that do not respond to alternatives provided by 400-range responses, 
like clients that do not retry timed-out operations, will not be able to handle 
potential future additions.

#### Downsides of REST
Due to the abstractness of [the original source on REST](https://www.ics.uci.edu/~fielding/pubs/dissertation/rest_arch_style.htm) 
and a widespread misuse of the name, finding information about REST can often be 
difficult and misleading.

The architecture is more restrictive, and thus more difficult to apply, than 
[semi-REST](#semi-rest). 
For the simplest of "CRUD-API's", that serve as back-ends for clients who GET 
and PUT data of a stable format without many context-sensitive restrictions, a 
fully fledged [level 3 REST API](https://martinfowler.com/articles/richardsonMaturityModel.html) 
might be complete overkill.

## Conclusion
Given all the above, it would seem [REST](#rest) is most aligned with this 
project when compared to the reviewed alternatives, at least when it comes to 
the match playing context.

Even so, it might not be unimaginable to one day implement a GraphQL interface 
specifically in the context of deck building and/or card collection, or a 
semi-REST component for managing user profile information.
