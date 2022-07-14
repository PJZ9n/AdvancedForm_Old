# AdvancedForm_old

# MOVED: [AdvancedForm](https://github.com/PJZ9n/AdvancedForm)

## Overview

Multi-functional and easy-to-use form framework

It was created with the goal of a highly convenient API. No more worrying about forms!

## Features

- Full validation of form (security)
- Supports class inheritance style / callback style
- Supports method chain style
- Custom handler buttons/elements for DRY
- Display the message in the form
- Highlight the elements of custom form
- Keep content when resend form
- Form back support (Easily install the back button/toggle)
- Numerical validation of Input
- Strict type support

## How to use

### Class inheritance style

You can create a constructor for every class inheritance style form.

- `__construct`: the initialization process of the form

Arguments to pass to create

- `title`: Form title

#### CustomForm

- `elements`: Form elements (optional)

```php
public function __construct(string $title, array $elements = [])
```

- `handleSubmit`: Called when the form is submitted
- `handleClose`: Called when the form is closed (optional)

```php
use pjz9n\advancedform\custom\CustomForm;
use pjz9n\advancedform\custom\response\CustomFormResponse;
use pocketmine\player\Player;

class ExampleForm extends CustomForm
{
    public function __construct()
    {
        parent::__construct("This is title");
    }

    protected function handleSubmit(Player $player, CustomFormResponse $response): void
    {
        //something
    }

    protected function handleClose(Player $player): void
    {
        //something
    }
}
```

#### MenuForm

- `content`: Form content
- `buttons`: Form buttons (optional)

```php
public function __construct(string $title, string $content, array $buttons = [])
```

- `handleSelect`: Called when the form is button selected
- `handleClose`: Called when the form is closed (optional)

```php
use pjz9n\advancedform\menu\button\MenuButton;
use pjz9n\advancedform\menu\MenuForm;
use pocketmine\player\Player;

class ExampleMenu extends MenuForm
{
    public function __construct()
    {
        parent::__construct("This is title", "This is content");
    }

    protected function handleSelect(Player $player, MenuButton $button): void
    {
        //something
    }

    protected function handleClose(Player $player): void
    {
        //something
    }
}
```

#### ModalForm

- `content`: Form content
- `button1Text`: Button 1 text (optional)
- `button2Text`: Button 2 text (optional)

```php
public function __construct(string $title, string $content, string $button1Text = "gui.yes", string $button2Text = "gui.no")
```

- `handleChoice`: Called when the form is choiced

```php
use pjz9n\advancedform\modal\ModalForm;
use pocketmine\player\Player;

class ExampleForm extends ModalForm
{
    public function __construct()
    {
        parent::__construct("This is title", "This is content", "This is button1", "This is button2");
    }

    protected function handleChoice(Player $player, bool $choice): void
    {
        //something
    }
}
```

### Callback style

Basically the same as the class inheritance style, but accepts closures as arguments.

It's easy to use, so it's useful for one-time forms.

#### CustomForm

Method signature

```php
use pjz9n\advancedform\custom\CallbackCustomForm;

public static function CallbackCustomForm::create(string $title, Closure $handleSubmit, ?Closure $handleClose = null): CallbackCustomForm
```

Example code

```php
use pjz9n\advancedform\custom\CallbackCustomForm;
use pjz9n\advancedform\custom\element\Input;
use pjz9n\advancedform\custom\response\CustomFormResponse;
use pocketmine\player\Player;

$form = CallbackCustomForm::create("This is title", function (Player $player, CustomFormResponse $response): void {
    //something
}, function (Player $player): void {
    //something
});
```

#### MenuForm

Method signature

```php
use pjz9n\advancedform\menu\CallbackMenuForm;

public static function CallbackMenuForm::create(string $title, string $content, Closure $handleSelect, ?Closure $handleClose = null): CallbackMenuForm
```

Example code

```php
use pjz9n\advancedform\menu\button\MenuButton;
use pjz9n\advancedform\menu\CallbackMenuForm;
use pocketmine\player\Player;

$form = CallbackMenuForm::create("This is title", "This is content", function (Player $player, MenuButton $menuButton): void {
    //something
}, function (Player $player): void {
    //something
});
```

#### ModalForm

Method signature

```php
use pjz9n\advancedform\modal\CallbackModalForm;

public static function CallbackModalForm::create(string $title, string $content, Closure $handleChoice, string $button1Text = "gui.yes", string $button2Text = "gui.no"): CallbackModalForm
```

Example code

```php
use pjz9n\advancedform\modal\CallbackModalForm;
use pocketmine\player\Player;

$form = new CallbackModalForm("This is title", "This is content", function (Player $player, bool $choice): void {
    //something
}, "This is button1", "This is button2");
```
