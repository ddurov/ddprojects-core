<?php declare(strict_types=1);

namespace Core\Tools;

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
     * @throws selfThrows
     */
    public function validate(): void
    {
        if ($this->fields === [] || $this->data === []) throw new selfThrows(["message" => "fields or data for validator are missing"]);
        foreach ($this->fields as $field) {
            $fieldData = $this->data[$field->getIsHeaderField() ? "headers" : "data"][$field->getName()];
            if ($fieldData === "" || !isset($fieldData))
                throw new selfThrows(["message" => "{$field->getName()} parameter isn't isset or empty"], 400);

            foreach ($field->getOptionsToValidate() as $keyOption => $option) {
                if ($option === null || ($option <= 0 && is_int($option))) throw new selfThrows(["message" => "some option are incorrect (internal error)"]);
                if (is_int($keyOption)) $keyOption = $option;
                switch ($keyOption) {
                    case "needInt":
                        if (!is_numeric($fieldData))
                            throw new selfThrows(["message" => "{$field->getName()} parameter should be integer"], 400);
                        break;
                    case "minLength":
                        if (mb_strlen($fieldData) <= $option)
                            throw new selfThrows(["message" => "{$field->getName()} parameter is too small (minimal {$option} characters)"], 400);
                        break;
                    case "maxLength":
                        if (mb_strlen($fieldData) > $option)
                            throw new selfThrows(["message" => "{$field->getName()} parameter is too big (maximum {$option} characters)"], 400);
                        break;
                    case "regexp":
                        if (!preg_match($option, $fieldData))
                            throw new selfThrows(["message" => "{$field->getName()} parameter should be parsed by regular expression '{$option}'"], 400);
                        break;
                    case "excludeRegexp":
                        if (preg_match($option, $fieldData))
                            throw new selfThrows(["message" => "{$field->getName()} parameter should doesn't parsed by regular expression '{$option}'"], 400);
                        break;
                }
            }
        }
    }
}