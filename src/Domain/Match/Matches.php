<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match;

interface Matches
{
    /**
     * Adds a new match.
     *
     * @param Match $match
     */
    public function add(Match $match): void;

    /**
     * Finds the match with the given identifier.
     *
     * @param MatchId $match
     * @return Match
     * @todo @throws NoSuchMatch
     */
    public function withId(MatchId $match): Match;

    /**
     * Retrieves the ongoing matches.
     *
     * @return Match[]
     */
    public function ongoing(): array;
}
