<?php

/*
 * Copyright (c) 2022 PJZ9n.
 *
 * This file is part of AdvancedForm.
 *
 * AdvancedForm is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * AdvancedForm is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with AdvancedForm. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace pjz9n\advancedform\custom;

use InvalidArgumentException;
use pjz9n\advancedform\BaseForm;
use pjz9n\advancedform\custom\element\Element;
use pjz9n\advancedform\custom\element\handler\HandlerElement;
use pjz9n\advancedform\custom\element\Input;
use pjz9n\advancedform\custom\element\Label;
use pjz9n\advancedform\custom\element\Selector;
use pjz9n\advancedform\custom\element\Slider;
use pjz9n\advancedform\custom\element\Toggle;
use pjz9n\advancedform\custom\response\CustomFormResponse;
use pjz9n\advancedform\FormTypes;
use pjz9n\advancedform\MessagesTrait;
use pjz9n\advancedform\util\Utils;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_values;
use function count;
use function gettype;
use function implode;
use function is_array;
use function is_string;
use function uniqid;

/**
 * Form with elements
 * e.g. Input, Dropdown
 */
abstract class CustomForm extends BaseForm
{
    use MessagesTrait;

    /**
     * Called when the form is submitted by the player
     * NOTE: Not called when the form is closed
     * @see CustomForm::handleClose()
     */
    abstract protected function handleSubmit(Player $player, CustomFormResponse $response): void;

    /**
     * @var Element[]
     * @phpstan-var array<string, Element>
     */
    private array $elements = [];

    private ?CustomFormResponse $defaultResponse = null;

    /**
     * @param string $title
     * @param Element[] $elements
     * @phpstan-param array<mixed, Element> $elements
     */
    public function __construct(string $title, array $elements = [])
    {
        parent::__construct($title);
        $this->setElements($elements);
    }

    /**
     * Add an error message
     * An error message is added and the element is highlighted
     */
    public function addError(string|Element $element, string $message, string $messagePrefix = TextFormat::RED, ?string $highlightPrefix = null): self
    {
        if (is_string($element)) {
            if (!array_key_exists($element, $this->getElements())) {
                throw new InvalidArgumentException("Element \"$element\" does not exist");
            }
            $element = $this->getElements()[$element];
        }

        $element->setMessage($messagePrefix . $message);
        $element->setHighlight(true);
        if ($highlightPrefix !== null) $element->setHighlightPrefix($highlightPrefix);

        return $this;
    }

    /**
     * Fill the default value with the response
     * This is useful when resend a form
     */
    public function fillDefaults(CustomFormResponse $response): self
    {
        $this->defaultResponse = $response;
        return $this;
    }

    public function restoreDefaults(): self
    {
        $this->defaultResponse = null;
        return $this;
    }

    /**
     * @return Element[]
     * @phpstan-return array<string, Element>
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @return Element[]
     * @phpstan-return list<Element>
     */
    public function getElementsIndexed(): array
    {
        return array_values($this->getElements());
    }

    public function getElement(string $name): ?Element
    {
        return $this->getElements()[$name] ?? null;
    }

    public function getElementByIndex(int $index): ?Element
    {
        return $this->getElementsIndexed()[$index] ?? null;
    }

    /**
     * @param Element[] $elements
     * @phpstan-param array<mixed, Element> $elements
     */
    public function setElements(array $elements): self
    {
        $this->checkDuplicate($elements);
        $this->elements = $this->fixElementKeys($elements);
        return $this;
    }

    public function addElement(Element $element): self
    {
        $this->checkDuplicate([$element]);
        $this->elements[$element->getName()] = $element;
        return $this;
    }

    /**
     * @param Element[] $elements
     * @phpstan-param array<mixed, Element> $elements
     */
    public function addElements(array $elements): self
    {
        $this->checkDuplicate($elements);
        $this->elements = array_merge($this->elements, $this->fixElementKeys($elements));
        return $this;
    }

    public function removeElement(string $name): self
    {
        if (!array_key_exists($name, $this->elements)) {
            throw new InvalidArgumentException("Element $name does not exist");
        }
        unset($this->elements[$name]);
        return $this;
    }

    /**
     * Called when the form is closed by the player
     */
    protected function handleClose(Player $player): void
    {
        //NOOP
    }

    final public function handleResponse(Player $player, $data): void
    {
        if ($data === null) {
            $this->handleClose($player);
            return;
        }

        if (!is_array($data)) {
            throw new FormValidationException("Excepted array, got " . gettype($data));
        }

        $exceptedCount = count($this->getElements());
        $actualCount = count($data);

        if ($actualCount === $exceptedCount + 1) {//include message(s)?
            if (array_key_exists(0, $data)) {
                try {
                    (new Label("", ""))->validate($data[0]);

                    //remove the message label
                    unset($data[0]);
                    //Safe shift the array
                    $data = array_values($data);//TODO: This breaks if it was originally an associative array
                    //recalculation
                    $actualCount = count($data);
                } catch (FormValidationException) {
                }
            }
        }

        if ($actualCount !== $exceptedCount) {
            throw new FormValidationException("Excepted $exceptedCount elements, got $actualCount elements");
        }

        $responseArray = [];
        foreach ($data as $index => $value) {
            if (!array_key_exists($index, $this->getElementsIndexed())) {
                throw new FormValidationException("Element[$index] does not exist");
            }
            $element = $this->getElementsIndexed()[$index];
            $name = $element->getName();
            try {
                $element->validate($value);
            } catch (FormValidationException $exception) {
                throw new FormValidationException("Element \"$name\" validation failed: " . $exception->getMessage(), previous: $exception);
            }
            $responseArray[$element->getName()] = $value;
        }

        //TODO: Is it correct to do this implicitly?
        foreach ($this->getElements() as $element) {
            $element->setHighlight(false);
            $element->setMessage(null);
        }

        $response = new CustomFormResponse($this, $responseArray);

        foreach ($this->getElements() as $element) {
            if ($element instanceof HandlerElement && $element->handle($this, $player, $response)) {
                return;
            }
        }

        $this->handleSubmit($player, $response);
    }

    final protected function getNetworkType(): string
    {
        return FormTypes::CUSTOM;
    }

    final protected function getNetworkData(): array
    {
        $messageLabels = [];
        if ($this->hasMessages()) {
            $messageLabels[] = new Label(uniqid(), implode(TextFormat::EOL, array_map(fn(string $message): string => $message . TextFormat::RESET, $this->getMessages())));
        }

        if ($this->defaultResponse === null) {
            $elements = $this->getElements();
        } else {
            $elements = array_map(function (Element $element): Element {
                $element = clone $element;//Does not change the element
                switch (true) {
                    case $element instanceof Input:
                        $element->setDefault($this->defaultResponse->getInputValue($element->getName()));
                        break;
                    case $element instanceof Selector:
                        $element->setDefault($this->defaultResponse->getSelectorIndex($element->getName()));
                        break;
                    case $element instanceof Slider:
                        $element->setDefault($this->defaultResponse->getSliderValue($element->getName()));
                        break;
                    case $element instanceof Toggle:
                        $element->setDefault($this->defaultResponse->getToggleValue($element->getName()));
                        break;
                }
                return $element;
            }, $this->getElements());
        }

        return [
            "content" => array_merge($messageLabels, Utils::arrayToList($elements)),
        ];
    }

    /**
     * @param Element[] $elements
     * @phpstan-param list<Element> $elements
     */
    private function checkDuplicate(array $elements): void
    {
        $getElementName = fn(Element $element): string => $element->getName();
        $elementNames = array_map($getElementName, $elements);
        $thisElementNames = array_map($getElementName, $this->elements);
        if (($duplicate = Utils::arrayDuplicate($elementNames, $thisElementNames)) !== null) {
            throw new InvalidArgumentException("Elements cannot have the same name ($duplicate)");
        }
    }

    /**
     * @param Element[] $elements
     * @phpstan-param array<mixed, Element> $elements
     *
     * @return Element[]
     * @phpstan-return array<string, Element>
     */
    private function fixElementKeys(array $elements): array
    {
        $validElements = [];
        foreach ($elements as $element) {
            $validElements[$element->getName()] = $element;
        }
        return $validElements;
    }
}
