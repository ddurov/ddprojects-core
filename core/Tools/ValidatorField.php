<?php declare(strict_types=1);

namespace Core\Tools;

class ValidatorField
{
    private int $isHeaderField;
    private string $name;
    private array $optionsToValidate;

    /**
     * @param int $isHeaderField
     * @param string $name
     * @param array $optionsToValidate
     */
    public function __construct(int $isHeaderField, string $name, array $optionsToValidate)
    {
        $this->isHeaderField = $isHeaderField;
        $this->name = $name;
        $this->optionsToValidate = $optionsToValidate;
    }

    /**
     * @return int
     */
    public function getIsHeaderField(): int
    {
        return $this->isHeaderField;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getOptionsToValidate(): array
    {
        return $this->optionsToValidate;
    }


}