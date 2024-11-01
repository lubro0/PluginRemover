<?php

declare(strict_types=1);

namespace zin\PluginRemover;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Main extends PluginBase{

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "plremove") {
            if (count($args) < 2) {
                return false;
            }

            $pluginName = $args[0];
            $mode = strtolower($args[1]);

            $plugin = $this->getServer()->getPluginManager()->getPlugin($pluginName);
            if ($plugin === null) {
                $sender->sendMessage("§cPlugin not found.");
                return true;
            }

            if (!in_array($mode, ["normal", "total"], true)) {
                $sender->sendMessage("§cMode not found. Available modes: normal, total.");
                return true;
            }

            if ($mode === "normal") {
                $this->getServer()->getPluginManager()->disablePlugin($plugin);
                $sender->sendMessage("§aPlugin {$pluginName} has been removed.");
                $pluginPath = $this->getServer()->getDataPath() . "plugins/" . $pluginName . ".phar";
                if (file_exists($pluginPath)) {
                    unlink($pluginPath);
                }
            } elseif ($mode === "total") {
                $this->getServer()->getPluginManager()->disablePlugin($plugin);
                $sender->sendMessage("§aPlugin {$pluginName} and its data have been removed.");
                $pluginPath = $this->getServer()->getDataPath() . "plugins/" . $pluginName . ".phar";
                if (file_exists($pluginPath)) {
                    unlink($pluginPath);
                }
                $pluginDataPath = $this->getServer()->getDataPath() . "plugin_data/" . $pluginName;
                if (is_dir($pluginDataPath)) {
                    array_map('unlink', glob("$pluginDataPath/*.*"));
                    rmdir($pluginDataPath);
                }
            }
            return true;
        }

        return false;
    }
}
