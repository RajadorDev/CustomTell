<?php

declare (strict_types=1);

namespace CustomTell\command;

use CustomTell\System;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class CustomTellCommand extends Command
{

    protected System $system;

    public function __construct()
    {
        $this->system = System::getInstance();
        parent::__construct('tell', $this->system->getConfigValue('command-description', ''), $this->system->getConfigValue('command-usage', ''), $this->system->getConfigValue('aliases', []));
        $this->setPermission('tell.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($args[1]) && trim($args[1]) != '')
        {
            $playerName = $args[0];
            if ($target = $this->system->fetchPlayer($playerName))
            {
                $message = $args;
                $message = array_slice($message, 1);
                $message = implode(' ', $message);
                $format = $this->system->getFormat($sender, $message);
                $target->sendMessage($format);
                $format = $this->system->getSenderFormat($target, $message);
                $sender->sendMessage($format);
            } else {
                $sender->sendMessage(str_replace('{target}', $playerName, $this->system->getConfigValue('player-notfound',  '')));
            }
        } else {
            $this->showUsage($sender, $commandLabel);
        }
    }

    public function showUsage(CommandSender $sender, string $label) : void 
    {
        $sender->sendMessage(
            str_replace(
                '{command_label}',
                $label,
                $this->getUsage()
            )
        );
    }
}