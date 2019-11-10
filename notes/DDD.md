# Domain Driven
Deck-building card games have the potential to get relatively complex.
In an attempt to tackle this complexity, a Domain Driven Design approach is 
followed.

The domain layer is kept strictly separated from other layers, to prevent 
card-game logic mixing up with infrastructure concerns and the like.
Names of classes are kept as closely to their real-world counterparts as 
possible, maintaining a ubiquitous language within each context.

## Bounded contexts
Some of the core concepts of the game, such as cards and decks, have vastly 
different behaviour in different contexts.

During a match, for instance, a card might be played onto the battlefield. It 
might honourably defend its position against enemy attackers and eventually 
perish, launch strikes against enemy positions or be casted as a spell with 
devastating effects.

When building a deck, however, all of that is completely out-of-scope. Yet, we 
are talking about the same cards as are used during the match.

Bounded contexts are applied to maintain a separation between the concepts.

## Aggregates
Deck-building card games tend to have many moving parts. 
Whether it is to rearrange the cards in a deck, or to apply the special 
abilities of a magical unit during a match, over the course of time many 
elements can change their state.

In order to maintain a consistent gaming experience, it's important to prevent 
multiple players from moving the same cards in different directions at the same 
time. The card might rip apart if that happens, and nobody would like that.

Aggregates are used to draw consistency- and concurrency boundaries. Although 
potentially made up of many smaller parts, they can be considered conceptual 
single things from the outside.

Each aggregate can only be altered by at most one person at a time, allowing 
for locking of aggregates to maintain consistency within the model.
Communication between aggregates happens through domain events.

## Domain events
The [aggregates](#aggregates) in the domain layer [are only used for making 
changes](CQRS.md). 
In order to communicate to the outside world about what changed, the aggregates 
produce domain events.

Based on these events, subsequent action can be taken - such as notifying the 
graphical user interface, updating some statistics or taking the next step in a 
long-running process.

Note: the events in question are domain events, not to be confused with events 
from event sourcing or other contexts.

## Ubiquitous language
When the names of the terms in the software don't align with the names of the 
same things in real-life, it becomes difficult and confusing to understand what 
is what, making development slower and the code more difficult to maintain.

To maintain readability and, with that, maintainability, names are chosen in 
such a way to closely reflect the concepts they represent.

Note that each "ubiquitous language" is only ubiquitous within the bounded 
context it applies to.

In the context of a match, cards can be fierce units, ready to protect their 
homes and fight glorious battles, be casted as terrifying spells, get carried 
into battle as special gear, supply wealth as a mana pool...

Such is the meaning of a card in the match context. When talking about deck 
building, however, all those traits become merely informative: it is still 
important for the player to *know* what the card *would do*, were it in a match, 
so that they can make an informed decision on whether or not to add it to the 
deck.

When it comes to making changes to the deck, *what* the card does in battle is 
not particularly important: when considering whether a card can be added to the 
deck, all that matters is that the deck does not contain too many copies of the 
same card.

In the context of deck building, the cards are of a particular *rarity*, which 
determines how many copies of the card are allowed in the deck.

To minimise the cognitive load when working on a model in a particular context, 
all irrelevant attributes and behaviours are left out of the language and the 
model.

Decks and cards still represent the same conceptual entity in both contexts. 
This parity may or may not apply to other concepts: a player, for instance, 
represents one particular real person in the account context.
This one player in the account context might play multiple matches at the same 
time, in which case the one player account would be represented by multiple 
players in the match context, one for each match.

Other concepts that share a name may be even less connected: when talking about 
the cost of a card in the match context, we talk about Mana costs. When talking 
about the cost of that same card in the shop context, a monetary value is 
implied.

## Aggregate Boundaries
An important and difficult decision within domain driven design is about where 
to draw the lines of the aggregate boundaries. [Aggregates](#aggregates), being 
consistency boundaries, are fundamental building blocks in a domain model.

For the simpler contexts, such as player accounts or page visits, the borders 
between aggregates are relatively simple and natural. In the core domains, the 
distinctions are less obvious.

A rule of thumb in choosing such borders is to keep aggregates small. Aggregates 
are guarantees for immediate consistency, and with large aggregates that means 
locking large resources. Since this makes collaboration difficult, the guideline 
is to keep them small.

When aggregates become too small, however, the overall complexity can increase. 
In order to keep aggregates decoupled and independent, only a single aggregate 
should be modified per transaction.

As might be seen in the commit history, experience has shown that aggregates 
smaller than the use case can overly complicate the overall system.

### Evolution
This project has seen several experiments with aggregate boundaries in the match 
context. Initially, the aggregates where modeled after some of the natural 
mental models that are used in such card games: a Deck, a Hand, a Battlefield...

Given that an aggregate is a transactional consistency boundary, this approach 
quickly proved problematic; whether we're drawing from the Deck into the Hand, 
playing from the Hand onto the Battlefield or blasting enemies into the Void, 
all of these operations require a modification of more than one aggregate: when 
the card is drawn *from* the Deck, it is also added *to* the Hand.

#### Moving parts
Noticing the chain of linked aggregates induced the realisation that these 
mental models are merely that: mental models. These mental models are produced 
by taking a subset of the Cards, based on their current location. The true 
moving part here is the Card itself.

Rather than the card being a value object, belonging to a Deck or a Hand, the 
Card became the central concept, with decks and hands turning into values of the 
Card's location.

It made sense to see the Card as the ultimate entity: we say entities can be 
distinguished from value objects by having an identity and a life cycle: There's 
even a UnitDied event!

#### Use cases
Although free from the shackles of the instinctive models that match the player 
perspective but not that of the match as a whole, the complexity of several 
match-related operations grew out of hand.

With the Card as aggregate root, maintaining the constraint of modifying only a 
single aggregate per transaction became easier in most trivial cases, such as 
drawing it from the deck, but rather involving in cases like drawing an initial 
hand or shuffling the deck. Let alone operations that combine those, such as 
starting a match.

#### Locking
Going back to the motivation behind the rule of thumb of creating small 
aggregates, we notice that the rule comes from resource management and locking. 
To prevent data corruption caused by simultaneous modifications of different 
users, aggregates can be locked. While the aggregate is locked, other people 
cannot make modifications to it.

For most applications, this drives the need to keep aggregates small. As it 
turns out, however, this project has a huge edge over all those other projects: 
there's a locking mechanism built into the domain! The essential domain concept 
of a Turn can easily be interpreted as pessimistic locking mechanism. Whether a 
player is not allowed to play cards in the other player's turn, or a user is not 
allowed to modify an aggregate while another user has a lock on it, is merely a 
matter of perspective.

#### Current result
The initial design with Deck, Hand, Battlefield and Void as aggregate roots led 
to viewing the Cards as essential entities with their own life cycles.

Experimenting with Cards as aggregate roots led to the realisation that the 
consistency boundary lies at a higher level.

The current model considers the Match itself as aggregate root, with Turns as 
locking mechanism and Players and Cards as aggregate components.

Some might (rightfully?) call this a [Large-Cluster Aggregate](https://dddcommunity.org/wp-content/uploads/files/pdf_articles/Vernon_2011_1.pdf), 
which is often rather impractical. In this particular scenario, it seems to fit 
pretty well. There are potential dragons on the road with this approach. Having 
a large aggregate means that a lot of resources are involved in a transaction, 
and that only one person can update the Match aggregate at a time.

Both of these potential downsides are no immediate concerns; in practice, many 
match elements might have an effect when the match is altered in any way.
If any unit may activate some kind of ability when a card is played, drawn or 
destroyed, it's probably preferable to have all units of the match available 
when a played, drawn or destroyed.
After all, even though the *players* are not allowed to perform actions outside 
of their turn, the *cards* in play definitely are.

For the moment, it is not an immediate problem that the aggregate can only be 
modified by one person at a time - in fact, rather than a problem, it's a 
desired behaviour. If at any point this desire changes, the design will need to 
be remodeled accordingly.
