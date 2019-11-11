# Architecture Notes
This section describes the architecture of the software, and attempts to 
clarify some of the reasoning behind the architectural choices in the project 
and the process that has been used to come to it.

## Contents
- [Domain Driven Design](DDD.md) is followed, in order to tackle the complexity 
    of a deck-building card-game.
- [CQRS](CQRS.md) is used in order to alleviate potential performance 
    bottlenecks and to simplify the overall structure of the code.
- [REST](REST.md) is supported, so that client- and server can evolve 
    independently.
- [TDD](TDD.md) is applied to work faster and more deliberate.
