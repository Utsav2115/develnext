<?php
namespace ide\action\types;

use ide\action\AbstractSimpleActionType;
use ide\action\Action;
use ide\action\ActionScript;
use php\lib\Str;

class AppendVarActionType extends AbstractSimpleActionType
{
    function attributes()
    {
        return [
            'name' => 'name',
            'value' => 'mixed',
            'asString' => 'flag',
        ];
    }

    function attributeLabels()
    {
        return [
            'name' => 'Имя переменной',
            'value' => 'Значение',
            'asString' => 'Как к строке (а не к числу)'
        ];
    }

    function getGroup()
    {
        return self::GROUP_SCRIPT;
    }

    function getTagName()
    {
        return "appendVar";
    }

    function getTitle(Action $action = null)
    {
        return "Добавить к переменной";
    }

    function getDescription(Action $action = null)
    {
        if (!$action) {
            return "Добавить к значению переменной";
        }

        $name = $action->get('name');

        if ($name[0] != '$') {
            $name = "\${$name}";
        }

        if ($action->asString) {
            return Str::format("Добавить к значению переменной %s строку %s", $name, $action->get('value'));
        } else {
            return Str::format("Добавить к значению переменной %s + %s", $name, $action->get('value'));
        }
    }

    function getIcon(Action $action = null)
    {
        return "icons/pointGo16.png";
    }

    /**
     * @param Action $action
     * @param ActionScript $actionScript
     * @return string
     */
    function convertToCode(Action $action, ActionScript $actionScript)
    {
        $name = $action->get('name');

        $actionScript->addLocalVariable($name);

        if ($name[0] != '$') {
            $name = "\${$name}";
        }

        if ($action->asString) {
            return "$name .= {$action->get('value')}";
        } else {
            return "$name += {$action->get('value')}";
        }
    }
}