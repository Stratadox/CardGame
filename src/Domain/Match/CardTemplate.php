<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

interface CardTemplate
{
    /**
     * Retrieves the events that happen when the card with this template is
     * played onto the battlefield.
     *
     * @param MatchId $match  The match in which this happens.
     * @param int     $player The player that plays the card.
     * @param int     $offset The offset of the card.
     * @return MatchEvent[]   The events that happen when the card is played.
     */
    public function playingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array;

    /**
     * Retrieves the events that happen when the card with this template is
     * drawn into the hand.
     *
     * @param MatchId $match  The match in which this happens.
     * @param int     $player The player that plays the card.
     * @param int     $offset The offset of the card.
     * @return MatchEvent[]   The events that happen when the card gets drawn.
     */
    public function drawingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array;

    /**
     * Retrieves the events that happen when the card with this template is
     * sent forward to attack.
     *
     * @param MatchId $match  The match in which this happens.
     * @param int     $player The player that plays the card.
     * @param int     $offset The offset of the card.
     * @return MatchEvent[]   The events that happen when the card attacks.
     */
    public function attackingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array;

    /** @return MatchEvent[] */
    public function defendingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array;

    /** @return MatchEvent[] */
    public function regroupingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array;

    /** @return MatchEvent[] */
    public function dyingEvents(
        MatchId $match,
        int $player,
        int $offset
    ): array;

    public function playingMove(int $position): Location;

    public function attackingMove(int $position): Location;

    public function defendingMove(int $position): Location;

    public function regroupingMove(int $position): Location;

    public function cost(): Mana;
}
