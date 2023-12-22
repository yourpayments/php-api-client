<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Это класс для выплат физ. лицам на банковские карты
 **/
class Payout implements PayoutInterface, \JsonSerializable
{
    private ?string $merchantPayoutReference;
    private ?Amount $amount = null;
    private ?string $description;
    private ?DestinationInterface $destination;
    private ?PayoutSource $source;

    public function __construct(string $merchantPayoutReference = null)
    {
        $this->merchantPayoutReference = $merchantPayoutReference;
    }

    /** @inheritdoc */
    public function getMerchantPayoutReference(): ?string
    {
        return $this->merchantPayoutReference;
    }

    /** @inheritdoc */
    public function setMerchantPayoutReference(?string $merchantPayoutReference): self
    {
        $this->merchantPayoutReference = $merchantPayoutReference;
        return $this;
    }

    /** @inheritdoc */
    public function getAmount(): ?Amount
    {
        return $this->amount;
    }

    /** @inheritdoc */
    public function setAmount(?Amount $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /** @inheritdoc */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /** @inheritdoc */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /** @inheritdoc */
    public function getDestination(): ?DestinationInterface
    {
        return $this->destination;
    }

    /** @inheritdoc */
    public function setDestination(?DestinationInterface $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    /** @inheritdoc */
    public function getSource(): ?PayoutSource
    {
        return $this->source;
    }

    /** @inheritdoc */
    public function setSource(?PayoutSource $source): self
    {
        $this->source = $source;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        //TODO: проверка необходимых параметров
        $requestData = [
            'merchantPayoutReference'	=> $this->getMerchantPayoutReference(),
            'amount'	=> $this->getAmount()->arraySerialize(),
            'description'	=> $this->getDescription(),
            'destination'	=> $this->getDestination()->arraySerialize(),
            'source' => $this->getSource()->arraySerialize(),
        ];

        $requestData = Std::removeNullValues($requestData);

        /**
         * В некоторых версиях PHP необходима тонкая настройка округления при сериализации
         * https://stackoverflow.com/questions/42981409/php7-1-json-encode-float-issue
         */
        ini_set('serialize_precision', '14');
        ini_set('precision', '14');

        return json_encode($requestData, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_LINE_TERMINATORS);
    }
}
