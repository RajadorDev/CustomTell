<?php

declare (strict_types=1);

namespace CustomTell;

use CustomTell\command\CustomTellCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

final class System extends PluginBase
{

    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->saveResource('config.yml');
        $defaultCommand = $this->getServer()->getCommandMap()->getCommand('tell');
        $this->getServer()->getCommandMap()->unregister($defaultCommand);
        $this->getServer()->getCommandMap()->register('customtell', new CustomTellCommand);
    }

    public function getConfigValue(string $id, mixed $default = null) : mixed 
    {
        if ($this->getConfig()->exists($id))
        {
            return $this->getConfig()->get($id);
        }
        $this->getLogger()->warning("Config with id $id not found!");
        return $default;
    }

    public function getFormat(CommandSender $sender, string $message) : string 
    {
        return str_replace(['{sender_name}', '{message}'], [$sender->getName(), $message], $this->getConfigValue('format'));
    }

    public function getSenderFormat(Player $target, string $message) : string 
    {
        return str_replace(['{target_name}', '{message}'], [$target->getName(), $message], $this->getConfigValue('sender-format'));
    }

    public function fetchPlayer(string &$input) : ? Player
    {
        $inputSearch = trim(strtolower(TextFormat::clean($input)));
        $autoSearch = null;
        foreach ($this->getServer()->getOnlinePlayers() as $player)
        {
            $playerName = strtolower($player->getName());
            if ($playerName === $inputSearch)
            {
                $input = $player->getName();
                return $player;
            } else if (str_contains($playerName, $inputSearch)) {
                $autoSearch = $player;
            }
        }

        if ($autoSearch instanceof Player)
        {
            $input = $player->getName();
        }
        return $autoSearch;
    }

}