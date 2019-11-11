# CQRS
When games gain popularity, they gain popularity fast.

If that happens, and the game is not built in a scalable manner, it becomes 
unplayable and quickly gets defeated by its own success.

Although the potential for this project to linger in obscurity is currently 
high, on the off-chance that sudden interest causes a viral spike in player 
numbers, the architecture should be capable of supporting the traffic without 
breaking down, and allow for easy scaling to multiple machines.

In an attempt to achieve that goal, Command/Query Responsibility Separation 
(CQRS) is applied.

## Commands
Commands are simple Data Transfer Objects (DTO's) that are passed to Command 
Handlers. A command is a request to change something, be it to open an account 
or to play a card.

### Command handlers
Command handlers process the commands by asking the [domain model](DDD.md) to 
perform the appropriate action(s), collecting the [events](DDD.md#domain-events) 
that get generated in the process.

The command handlers do not return any values, nor will they throw exceptions.
This limitation allows for delaying the execution of commands: if the server is 
busy, the command can be put on a queue. A load balancer can be put in between, 
and sharding strategies can be injected at will. This solution might somewhat 
stretch up response times, but its horizontal scaling capacities make up for 
that by providing a throughput only limited by the number of available machines.

While this solution vastly increases scalability, it does introduce some 
challenges in designing an API.

### Identifier assignment
A common approach to identifier management in web applications is to have the 
database handle it all. Simply mark the column as auto-increment and return the 
id to the client once the entity gets saved to the database.

When commands can be queued, their execution may be delayed. We don't want to 
keep the client without feedback for so long, nor do we want the http requests 
to keep on lingering on the server until their command finished the queue.

In order to quickly provide feedback on whether the command was successfully 
received or failed due to some infrastructure concern (eg. network/server down), 
we cannot have the transaction hang until the database assigned an id.

Within CQRS, it is common to generate a uuid on the client side, so that the 
identifier is already known before the request is made. The same approach is 
used in this project as well, albeit only for the visitor (and 
[correlation](#refused-commands)) ids.

The rest of the identifiers are retrievable from the read model. If a visitor 
opened an account, the account can be accessed on the read side by using the 
visitor id.

### Timing
Since the game is somewhat time-sensitive, in the sense that turn phases have a 
certain maximum duration, queueing the commands for a significant amount of time 
(like a second or more) could impact the gameplay itself.

Depending on which point in time is considered when determining the validity of 
a move - the moment the request was sent, the moment the request reached the 
server, or the moment the command is actually handled - the move can be either 
valid or invalid.
At the time of writing, the moment of handling the command is used. This initial 
choice might need reconsideration. 

### Refused commands
Commands will not throw exceptions or return values, yet not every command is 
followed through.

Since command handlers don't produce *return values*, they are free to perform 
side-effects.
Correlation ids are assigned to each command. If the command is refused, or 
fails for any other reason, details of the problem are published to the client 
through a refusal event, linked to the correlation id.

By querying the refusals for the (client-side generated) correlation id, clients 
can find out the status of their requests.

## Events
As opposed to commands, events cannot be refused: they indicate the things that 
have already happened.

Domain events are the messages that go from the write model to the read model.

### Event handlers
When events are published, they are picked up by the event handlers that are 
interested in the event. These handlers use the events to update the read models 
according to the latest changes in the domain.

## Queries
Queries - your proverbial GET requests, not database queries: *query* from 
the sense of inquiring information - are handled by the various read models.

Read models are updated by the event handlers, based on what happened in the 
card game. 
Their state is essentially redundant if the events are properly saved, making it 
possible to solely persist read models in a cache or completely denormalised 
database tables. 

Since the single purpose of these models is to provide information, they can be 
persisted in such a way as to facilitate fast reads, while the infrastructure 
that supports the domain model can be independently optimized for efficient 
writing.
