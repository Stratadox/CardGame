<?php declare(strict_types=1);

namespace Stratadox\CardGame\Match\Player;

use Stratadox\CardGame\Account\AccountId;

final class StartPlaying
{
    private $player;
    private $account;

    private function __construct(PlayerId $player, AccountId $account)
    {
        $this->player = $player;
        $this->account = $account;
    }

    public static function as(PlayerId $player, AccountId $account): self
    {
        return new self($player, $account);
    }

    public function player(): PlayerId
    {
        return $this->player;
    }

    public function account(): AccountId
    {
        return $this->account;
    }
}
