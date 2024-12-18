<?php declare(strict_types=1);

namespace Ypmn;

interface PaymentDetailsInterface
{
    /**
     * Получить расширенные данные по транзакции
     * @return Details|null Расширенные данные по транзакции
     */
    public function getDetails(): ?Details;

    /**
     * Установить расширенные данные по транзакции
     * @param Details|null $details Расширенные данные по транзакции
     * @return $this
     */
    public function setDetails(?Details $details): self;
}
