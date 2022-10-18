<?php declare(strict_types=1);

namespace Core\Tools;

use Core\Exceptions\InternalError;
use Core\Exceptions\InvalidParameter;
use Core\Exceptions\InvalidParameterLength;
use Core\Exceptions\InvalidParameterType;
use Core\Exceptions\ParameterNotParsedRegexp;
use Core\Exceptions\ParameterParsedRegexp;

class Validator
{
    private array $fields;
    private array $data;

    /**
     * @param ValidatorField[] $fields
     */
    public function __construct(array $fields, array $data)
    {
        $this->fields = $fields;
        $this->data = $data;
    }

    /**
     * @return void
     * @throws InvalidParameter
     * @throws InternalError
     * @throws ParameterParsedRegexp
     * @throws ParameterNotParsedRegexp
     * @throws InvalidParameterLength
     * @throws InvalidParameterType
     */
    public function validate(): void
    {
        foreach ($this->fields as $field) {
            $fieldData = $this->data[$field->getIsHeaderField() ? "headers" : "data"][$field->getName()];
            if ($fieldData === "" || !isset($fieldData))
                throw new InvalidParameter($field->getName());

            foreach ($field->getOptionsToValidate() as $keyOption => $option) {
                if ($option === null || ($option <= 0 && is_int($option))) throw new InternalError();
                if (is_int($keyOption)) $keyOption = $option;
                switch ($keyOption) {
                    case "needInt":
                        if (!is_numeric($fieldData))
                            throw new InvalidParameterType($field->getName(), "integer");
                        break;
                    case "inRange":
                        if (!(($option[0] <= $fieldData) && ($fieldData <= $option[1])))
                            throw new InvalidParameterLength($field->getName());
                        break;
                    case "regexp":
                        if (!preg_match($option, $fieldData))
                            throw new ParameterNotParsedRegexp($field->getName(), $option);
                        break;
                    case "excludeRegexp":
                        if (preg_match($option, $fieldData))
                            throw new ParameterParsedRegexp($field->getName(), $option);
                        break;
                }
            }
        }
    }
}