<?php declare(strict_types=1);

namespace Ypmn;

interface PayoutDestinationInterface extends DestinationInterface
{
    /**
     * Получить Информацию о карте
     * @return CardDetails|null
     */
    public function getCard(): ?CardDetails;

    /**
     * Установить Информацию о карте
     * @param CardDetails|null $card
     * @return PayoutDestination
     */
    public function setCard(?CardDetails $card): self;

    /**
     * Установить номер карты
     * @param string $cardNumber
     * @return PayoutDestination
     */
    public function setCardNumber(string $cardNumber): self;
}
