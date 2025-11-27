<?php

declare(strict_types=1);

namespace Ypmn;

/**
 * Класс для хранения UTM меток
 */
class UtmDto implements \JsonSerializable
{
    public ?string $utm_source = null;
    public ?string $utm_medium = null;
    public ?string $utm_campaign = null;
    public ?string $utm_term = null;
    public ?string $utm_content = null;

    public function __construct(
        ?string $utm_source = null,
        ?string $utm_medium = null,
        ?string $utm_campaign = null,
        ?string $utm_term = null,
        ?string $utm_content = null
    ) {
        $this->utm_source = $utm_source;
        $this->utm_medium = $utm_medium;
        $this->utm_campaign = $utm_campaign;
        $this->utm_term = $utm_term;
        $this->utm_content = $utm_content;
    }

    /**
     * Создать DTO из ассоциативного массива
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['utm_source'] ?? null,
            $data['utm_medium'] ?? null,
            $data['utm_campaign'] ?? null,
            $data['utm_term'] ?? null,
            $data['utm_content'] ?? null
        );
    }

    /**
     * Преобразовать DTO в массив
     */
    public function toArray(): array
    {
        return [
            'utm_source'   => $this->utm_source,
            'utm_medium'   => $this->utm_medium,
            'utm_campaign' => $this->utm_campaign,
            'utm_term'     => $this->utm_term,
            'utm_content'  => $this->utm_content,
        ];
    }

    /**
     * Сериализация для json_encode()
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Десериализация из JSON-строки
     */
    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return self::fromArray($data);
    }

    /**
     * Сериализация в JSON-строку
     */
    public function toJson(int $flags = 0): string
    {
        return json_encode($this, $flags | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
