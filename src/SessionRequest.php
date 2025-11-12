<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Это класс для запроса ID сессии
 **/
class SessionRequest implements \JsonSerializable
{
    private int $lifetimeMinutes;

    /**
     * @param int $lifetimeMinutes
     * @throws PaymentException
     */
    public function __construct(int $lifetimeMinutes)
    {
        if ($lifetimeMinutes< 1) {
            throw new PaymentException('Время жизни сессии слишком маленькое');
        }

        $this->lifetimeMinutes = $lifetimeMinutes;
    }


    #[\ReturnTypeWillChange]
    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return json_encode(
            [
                'lifetimeMinutes' => $this->lifetimeMinutes,
            ]
        );
    }
}
