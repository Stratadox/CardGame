<?php declare(strict_types=1);

namespace Stratadox\CardGame\Context\Step;

use Stratadox\CardGame\Account\OpenAnAccount;
use Stratadox\CardGame\Command;
use Stratadox\CardGame\CorrelationId;
use Stratadox\CardGame\Visiting\Visit;
use Stratadox\CardGame\Visiting\VisitorId;

trait SigningUp
{
    /**
     * @Given :player has signed up for the game
     */
    public function hasSignedUpForTheGame(string $player)
    {
        $this->setVisitor($player, VisitorId::from('visitor-'.$player));
        $this->handle(Visit::page(
            'home',
            'source',
            $this->visitor($player),
            $this->correlation()
        ));
        $this->handle(OpenAnAccount::forVisitorWith(
            $this->visitor($player),
            $this->correlation()
        ));
    }

    abstract protected function setVisitor(string $player, VisitorId $id): void;
    abstract protected function visitor(string $player): VisitorId;
    abstract protected function correlation(): CorrelationId;
    abstract protected function handle(Command $command): void;
}
