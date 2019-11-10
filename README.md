# CardGame
CardGame is a turn-based deck-building card game.

[![Build Status](https://travis-ci.org/Stratadox/CardGame.svg?branch=master)](https://travis-ci.org/Stratadox/CardGame)
[![Coverage Status](https://coveralls.io/repos/github/Stratadox/CardGame/badge.svg?branch=master)](https://coveralls.io/github/Stratadox/CardGame?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Stratadox/CardGame/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Stratadox/CardGame/?branch=master)

## Turn based
Players take turns in playing their cards.
Turns are time-limited; they can be ended sooner by the player but not extended.

## Deck building
New players start with an initial deck, and collect cards as they progress.
Cards can be added to - or removed from - the existing deck.
New decks can be formed with any of the cards owned by the player.
The cards can be in any number of decks at the same time.

The number of copies per card that are allowed in a deck is limited by the 
rarity of the card, and by the number of copies owned by the player.

## Card game
Each match starts with the players drawing their initial hands from their decks.

During their turns, players play the cards from their decks, in order to beat 
their opponents.

Cards need to be paid for with *mana*, which can be collected in *mana pools*.
Players can play any number of cards they have mana for, but only one *mana pool*
per turn.

## Card types
Cards can either be *mana pools*, *spells*, *units* or *upgrades*.

A *spell* is a one-off card, having a (potentially devastating) effect before 
disappearing into the void.

*Units* form the armies that will crush the enemy. They remain faithfully on the 
battlefield until their deaths - unless they survive the war and get to return 
safely home to their families.

*Upgrades* can be applied to units. They remain with the unit until either 
the unit dies or the upgrade ends, breaks or gets stolen.

*Mana pools* are the primary source of mana-income for the players. They remain 
on the battlefield until the match ends, unless they get destroyed somehow.

## Objective
The match ends when either one of the players has zero lives or less.
Players typically start with 20 lives.

A player loses lives when an attacking enemy unit is not blocked.

## Flow of a turn
Turns begin by collecting the mana from the mana pools.
Players get a basic income of mana each turn, which is complemented by their 
mana pools.

The player draws a card from their deck, unless their hand is already full.
If this was the last card in their deck, they lose the match.

If there are any attacking units from the opponents last turn, the player gets 
to decide which of those to block.
Attackers can be defended against by multiple defenders at a time. 
Each defender can only defend against one attacker.

Some spells can be played during this phase.
If the player has such spells, they can play them now.

Once decided, the combat begins.

During combat, defending units damage the attacking units, and the attacking 
units damage the defenders.
When an attacking unit does not encounter any defenders, it damages the player 
instead, causing them to lose lives. Any surviving attackers regroup at their 
home base.

Assuming they survive combat, or if there was none, the player now gets to play 
cards from their hand.


After playing the cards, they get to decide which of their combat-ready units 
will participate in the attack.

The attack itself will take place in the next players' turn.

In bullet points, that makes:
- Player 1, first turn:
  - Gain mana
  - Play cards
  - Attack (quick units only)
- Player 2, first turn:
  - Gain mana
  - Defend (spells only)
  - Combat
  - Play cards
  - Attack (quick units only)
- Player 1:
  - Gain mana
  - Draw card
  - Defend
  - Combat
  - Play cards
  - Attack
- Player 2:
  - Gain mana
  - Draw card
  - Defend
  - Combat
  - Play cards
  - Attack
- Etc

## Cards

### Defence spell ideas
- "Rally the locals": spawn a 2 strength militia unit to defend against an attacker
- "Revive the militia": same but spawn 5 strength defender
- "Scorched earth": sacrifice a unit, send the attackers back home
- "Blow the dam": deal 3 damage to all combatants
### Offence spell ideas
- "Inspiring war speech": all attackers get a temporary +2 strength bonus
- "Shaun the assassin": deal 4 damage to any unit
- "Spawn kill": destroy all units that were created in the last turn
- "Wildfire": burn all mana pools
- "Mana boost": get bonus mana
### Unit ideas
- "Defence tower": tough but cannot attack
- "Mana mage": expensive and weak but makes mana
- "Melee maniacs": get bonus strength when defended against
- "Base invaders": get bonus strength when not defended against
### Upgrade ideas
- "Better gear": bonus strength
- "Noble steed": become quick

## Technical notes
For information about the architectural choices and considerations, see the 
[notes on architecture](notes/README.md).
