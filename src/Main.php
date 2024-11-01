<?php

declare(strict_types=1);

namespace zin\PluginRemover;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class Main extends PluginBase{

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "plremove") {
            if (!$sender->hasPermission("pl.remove")) {
                $sender->sendMessage("§cYou do not have permission to use this command.");
                return true;
            }

            if (count($args) < 2) {
                $sender->sendMessage("§cUsage: /plremove <Plugin Name> <Mode: normal/total>");
                return true;
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

            $pluginPath = $this->getServer()->getDataPath() . "plugins/" . $pluginName;
            if ($mode === "normal") {
                $this->getServer()->getPluginManager()->disablePlugin($plugin);
                $sender->sendMessage("§aPlugin {$pluginName} has been removed.");
                if (is_file($pluginPath . ".phar")) {
                    unlink($pluginPath . ".phar");
                } elseif (is_dir($pluginPath)) {
                    $this->deleteDirectory($pluginPath);
                }
            } elseif ($mode === "total") {
                $this->getServer()->getPluginManager()->disablePlugin($plugin);
                $sender->sendMessage("§aPlugin {$pluginName} and its data have been removed.");
                if (is_file($pluginPath . ".phar")) {
                    unlink($pluginPath . ".phar");
                } elseif (is_dir($pluginPath)) {
                    $this->deleteDirectory($pluginPath);
                }
                $pluginDataPath = $this->getServer()->getDataPath() . "plugin_data/" . $pluginName;
                if (is_dir($pluginDataPath)) {
                    $this->deleteDirectory($pluginDataPath);
                }
            }
            return true;
        }

        return false;
    }

    private function deleteDirectory(string $dirPath): void {
        foreach (scandir($dirPath) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dirPath . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dirPath);
    }
}
