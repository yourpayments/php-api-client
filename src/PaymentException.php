<?php

declare(strict_types=1);

namespace Ypmn;

class PaymentException extends \Exception
{
    /** @var string Подробности для логирования */
    private string $logText;

    /** @return string Ошибка в формате Bootstrap */
    public function getHtmlMessage(): string
    {
        return '
            <div class="alert alert-danger" role="alert">
              <strong>Ошибка оплаты:</strong>
              <br>
              <br>
              ' . htmlspecialchars($this->getMessage()) . '
            </div>
        ';
    }

    /**
     * @return string
     */
    public function getLogText(): string
    {
        return $this->logText;
    }

    /**
     * @param string $logText
     * @return PaymentException
     */
    public function setLogText(string $logText): self
    {
        $this->logText = $logText;
        return $this;
    }
}
