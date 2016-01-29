<?php

namespace BuildBattle;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;

use pocketmine\event\block\BlockPlaceEvent;

use pocketmine\event\Listener;

use pocketmine\event\player\PlayerGameModeChangeEvent;

use pocketmine\event\player\PlayerItemHeldEvent;

use pocketmine\event\player\PlayerJoinEvent;

use pocketmine\event\player\PlayerQuitEvent;

use pocketmine\item\Item;

use pocketmine\level\Position;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;

use pocketmine\scheduler\CallbackTask;

use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener
{
    public $bb = array();
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->bb = array();
        $this->bb[0] = 0; // запущена ли игра
        $this->bb[1] = 0; // то что нужно построить

        $this->bb[2] = 0; // общая оценка 1 постройки
        $this->bb[3] = 0; // общая оценка 2 постройки
        $this->bb[4] = 0; // общая оценка 3 постройки
        $this->bb[5] = 0; // общая оценка 4 постройки
        $this->bb[6] = 0; // общая оценка 5 постройки

        $this->bb[7] = 0; // ник строителя 1 постройки
        $this->bb[8] = 0; // ник строителя 2 постройки
        $this->bb[9] = 0; // ник строителя 3 постройки
        $this->bb[10] = 0; // ник строителя 4 постройки
        $this->bb[11] = 0; // ник строителя 5 постройки

        $this->bb[12] = 0; // на какой арене сейчас игроки
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new CallbackTask(array($this, "Popup")), 10);
        if (!file_exists($this->getDataFolder() . "config.yml")) {
            @mkdir($this->getDataFolder());
            file_put_contents($this->getDataFolder() . "config.yml", $this->getResource("config.yml"));
        }
    }

    public function PlayerJoinEvent(PlayerJoinEvent $event){
        $p = $event->getPlayer();
        if((int)$this->bb[0] != 0){
            $p->close("", TextFormat::RED."Игра уже началась!");
            return false;
        }
        $p->setNameTagVisible(false);
        $p->setGamemode(0);
        $p->teleport(new Position($this->getConfig()->get("Spawn")));
        if(count($this->getServer()->getOnlinePlayers()) >= 5){
            $this->getServer()->broadcastMessage(TextFormat::RED."Начало игры через 10 секунд!");
            $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "Start"]), 10 * 20 );
        } else {
            $p->sendMessage(TextFormat::GOLD.'Вы присоединились к очереди на BuildBattle.');
            $p->sendMessage(TextFormat::GOLD.'Как число игроков достигнет 5-ти, вы начнете игру.');
        }
    }
    public function PlayerQuitEvent(PlayerQuitEvent $event){
        $event->getPlayer()->getInventory()->clearAll();
    }
    public function Start(){
        if(count($this->getServer()->getOnlinePlayers()) >= 5){
            $this->getServer()->broadcastMessage(TextFormat::RED."Начало игры было отменено. Кто то вышел с сервера.");
        } else {
            $this->getServer()->broadcastMessage(TextFormat::RED."Игра началась!");
            $online = $this->getServer()->getOnlinePlayers();
            $online[0]->teleport(new Position($this->getConfig()->get("1")));
            $online[1]->teleport(new Position($this->getConfig()->get("2")));
            $online[2]->teleport(new Position($this->getConfig()->get("3")));
            $online[3]->teleport(new Position($this->getConfig()->get("4")));
            $online[4]->teleport(new Position($this->getConfig()->get("5")));

            $this->bb[7] = $online[0]; // ник строителя 1 постройки
            $this->bb[8] = $online[1]; // ник строителя 2 постройки
            $this->bb[9] = $online[2]; // ник строителя 3 постройки
            $this->bb[10] = $online[3]; // ник строителя 4 постройки
            $this->bb[11] = $online[4]; // ник строителя 5 постройки

            foreach($this->getServer()->getOnlinePlayers() as $p){
                $p->setGamemode(1);
            }
            $r = mt_rand(1,10);
            if($r == 1){
                $this->bb[1] = "Мост";
                $this->getServer()->broadcastMessage(TextFormat::GOLD."Постройте мост, в течении 3-ех минут");
                $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "min1"]), 3 * 60 * 20 );
            } elseif($r == 2){
                $this->bb[1] = "Компьютер";
                $this->getServer()->broadcastMessage(TextFormat::GOLD."Постройте компьютер, в течении 5-ти минут");
                $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "min1"]), 4 * 60 * 20 );
            } elseif($r == 3){
                $this->bb[1] = "Замок";
                $this->getServer()->broadcastMessage(TextFormat::GOLD."Постройте замок, в течении 5-ти минут");
                $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "min1"]), 4 * 60 * 20 );
            } elseif($r == 4){
                $this->bb[1] = "Башню лучников";
                $this->getServer()->broadcastMessage(TextFormat::GOLD."Постройте башню лучников, в течении 5-ти минут");
                $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "min1"]), 4 * 60 * 20 );
            } elseif($r == 5){
                $this->bb[1] = "Светильник";
                $this->getServer()->broadcastMessage(TextFormat::GOLD."Постройте светильник, в течении 2-ух минут");
                $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "min1"]), 1 * 60 * 20 );
            } elseif($r == 6){
                $this->bb[1] = "Наушники";
                $this->getServer()->broadcastMessage(TextFormat::GOLD."Постройте наушники, в течении 3-ех минут");
                $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "min1"]), 2 * 60 * 20 );
            } elseif($r == 7){
                $this->bb[1] = "Клавиатура";
                $this->getServer()->broadcastMessage(TextFormat::GOLD."Постройте клавиатуру, в течении 3-ех минут");
                $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "min1"]), 2 * 60 * 20 );
            } elseif($r == 8){
                $this->bb[1] = "Машина";
                $this->getServer()->broadcastMessage(TextFormat::GOLD."Постройте машину, в течении 5-ти минут");
                $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "min1"]), 4 * 60 * 20 );
            } elseif($r == 9){
                $this->bb[1] = "Вертолёт";
                $this->getServer()->broadcastMessage(TextFormat::GOLD."Постройте вертолёт, в течении 5-ти минут");
                $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "min1"]), 4 * 60 * 20 );
            } elseif($r == 10){
                $this->bb[1] = "Телевизор";
                $this->getServer()->broadcastMessage(TextFormat::GOLD."Постройте телевизор, в течении 3-ех минут");
                $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "min1"]), 2 * 60 * 20 );
            }
            $this->bb[0] = 1;
        }
    }
    public function Popup(){
        if($this->bb[0] = 1){
            $this->getServer()->broadcastPopup(TextFormat::GOLD."Вы должны построить ".$this->bb[1]);
        }
    }
    public function min1(){
        $this->getServer()->broadcastMessage(TextFormat::GOLD."Осталась одна минута! Успейте достроить");
        $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "second30"]), 30 * 20 );
    }
    public function second30(){
        $this->getServer()->broadcastMessage(TextFormat::GOLD."Осталось 30 секунд, стройте быстрее!");
        $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "finish"]), 30 * 20 );
    }
    public function finish(){
        $this->getServer()->broadcastMessage(TextFormat::GOLD."Игра завершена, начинаем ставить оценку игрокам!");
        $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "a1"]), 20 * 20 );
        $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "a2"]), 40 * 20 );
        $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "a3"]), 60 * 20 );
        $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "a4"]), 80 * 20 );
        $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "a5"]), 100 * 20 );
        $this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "stats"]), 120 * 20 );
    }
    public function a1(){
        foreach ($this->getServer()->getOnlinePlayers() as $p) {
            $p->setGamemode(0);
            $p->getInventory()->setItem(1, Item::get(35,5,1)); // хорошо
            $p->getInventory()->setItem(1, Item::get(35,4,1)); // нормально
            $p->getInventory()->setItem(1, Item::get(35,14,1)); // плохо
            $p->teleport(new Position(100,100,100));
            $p->sendMessage(TextFormat::GOLD."Данная постройка от игрока ".$this->bb[7]);
            $p->sendMessage(TextFormat::GOLD."Поставьте столько баллов, сколько его постройка заслуживает.");
            $p->sendMessage(TextFormat::GREEN."- зеленая шерсть: отлично");
            $p->sendMessage(TextFormat::YELLOW."- жёлтая шерсть: нормально");
            $p->sendMessage(TextFormat::RED."- красная шерсть: плохо");
            $this->bb[12] = "1";
        }
    }
    public function a2(){
        foreach ($this->getServer()->getOnlinePlayers() as $p) {
            $p->setGamemode(0);
            $p->getInventory()->setItem(1, Item::get(35,5,1)); // хорошо
            $p->getInventory()->setItem(1, Item::get(35,4,1)); // нормально
            $p->getInventory()->setItem(1, Item::get(35,14,1)); // плохо
            $p->teleport(new Position(100,100,100));
            $p->sendMessage(TextFormat::GOLD."Данная постройка от игрока ".$this->bb[8]);
            $p->sendMessage(TextFormat::GOLD."Поставьте столько баллов, сколько его постройка заслуживает.");
            $p->sendMessage(TextFormat::GREEN."- зеленая шерсть: отлично");
            $p->sendMessage(TextFormat::YELLOW."- жёлтая шерсть: нормально");
            $p->sendMessage(TextFormat::RED."- красная шерсть: плохо");
            $this->bb[12] = "2";
        }
    }
    public function a3(){
        foreach ($this->getServer()->getOnlinePlayers() as $p) {
            $p->setGamemode(0);
            $p->getInventory()->setItem(1, Item::get(35,5,1)); // хорошо
            $p->getInventory()->setItem(1, Item::get(35,4,1)); // нормально
            $p->getInventory()->setItem(1, Item::get(35,14,1)); // плохо
            $p->teleport(new Position(100,100,100));
            $p->sendMessage(TextFormat::GOLD."Данная постройка от игрока ".$this->bb[9]);
            $p->sendMessage(TextFormat::GOLD."Поставьте столько баллов, сколько его постройка заслуживает.");
            $p->sendMessage(TextFormat::GREEN."- зеленая шерсть: отлично");
            $p->sendMessage(TextFormat::YELLOW."- жёлтая шерсть: нормально");
            $p->sendMessage(TextFormat::RED."- красная шерсть: плохо");
            $this->bb[12] = "3";
        }
    }
    public function a4(){
        foreach ($this->getServer()->getOnlinePlayers() as $p) {
            $p->setGamemode(0);
            $p->getInventory()->setItem(1, Item::get(35,5,1)); // хорошо
            $p->getInventory()->setItem(1, Item::get(35,4,1)); // нормально
            $p->getInventory()->setItem(1, Item::get(35,14,1)); // плохо
            $p->teleport(new Position(100,100,100));
            $p->sendMessage(TextFormat::GOLD."Данная постройка от игрока ".$this->bb[10]);
            $p->sendMessage(TextFormat::GOLD."Поставьте столько баллов, сколько его постройка заслуживает.");
            $p->sendMessage(TextFormat::GREEN."- зеленая шерсть: отлично");
            $p->sendMessage(TextFormat::YELLOW."- жёлтая шерсть: нормально");
            $p->sendMessage(TextFormat::RED."- красная шерсть: плохо");
            $this->bb[12] = "4";
        }
    }
    public function a5(){
        foreach ($this->getServer()->getOnlinePlayers() as $p) {
            $p->setGamemode(0);
            $p->getInventory()->setItem(1, Item::get(35,5,1)); // хорошо
            $p->getInventory()->setItem(1, Item::get(35,4,1)); // нормально
            $p->getInventory()->setItem(1, Item::get(35,14,1)); // плохо
            $p->teleport(new Position(100,100,100));
            $p->sendMessage(TextFormat::GOLD."Данная постройка от игрока ".$this->bb[11]);
            $p->sendMessage(TextFormat::GOLD."Поставьте столько баллов, сколько его постройка заслуживает.");
            $p->sendMessage(TextFormat::GREEN."- зеленая шерсть: отлично");
            $p->sendMessage(TextFormat::YELLOW."- жёлтая шерсть: нормально");
            $p->sendMessage(TextFormat::RED."- красная шерсть: плохо");
            $this->bb[12] = "5";
        }
    }
    public function stats(){
        $stats = array((int)$this->bb[2], (int)$this->bb[3], (int)$this->bb[4], (int)$this->bb[5], (int)$this->bb[6]);
        $iterator = new \RecursiveArrayIterator(new \RecursiveArrayIterator($stats));
        $max = max(iterator_to_array($iterator, false));
        if((int)$this->bb[2] == $max){
            foreach($this->getServer()->getOnlinePlayers() as $p){
                $p->teleport(new Position($this->getConfig()->get("Spawn")));
                $p->getInventory()->addItem(Item::get(1,0,1));
                $p->getInventory()->clearAll();
            }
            $this->getServer()->broadcastMessage(TextFormat::RED."В BuildBattle победил игрок ".$this->bb[7]);
        } elseif((int)$this->bb[3] == $max){
            foreach($this->getServer()->getOnlinePlayers() as $p){
                $p->teleport(new Position($this->getConfig()->get("Spawn")));
                $p->getInventory()->addItem(Item::get(1,0,1));
                $p->getInventory()->clearAll();
            }
            $this->getServer()->broadcastMessage(TextFormat::RED."В BuildBattle победил игрок ".$this->bb[8]);
        } elseif((int)$this->bb[4] == $max){
            foreach($this->getServer()->getOnlinePlayers() as $p){
                $p->teleport(new Position($this->getConfig()->get("Spawn")));
                $p->getInventory()->addItem(Item::get(1,0,1));
                $p->getInventory()->clearAll();
            }
            $this->getServer()->broadcastMessage(TextFormat::RED."В BuildBattle победил игрок ".$this->bb[9]);
        } elseif((int)$this->bb[5] == $max){
            foreach($this->getServer()->getOnlinePlayers() as $p){
                $p->teleport(new Position($this->getConfig()->get("Spawn")));
                $p->getInventory()->addItem(Item::get(1,0,1));
                $p->getInventory()->clearAll();
            }
            $this->getServer()->broadcastMessage(TextFormat::RED."В BuildBattle победил игрок ".$this->bb[10]);
        } elseif((int)$this->bb[6] == $max){
            foreach($this->getServer()->getOnlinePlayers() as $p){
                $p->teleport(new Position($this->getConfig()->get("Spawn")));
                $p->getInventory()->addItem(Item::get(1,0,1));
                $p->getInventory()->clearAll();
            }
            $this->getServer()->broadcastMessage(TextFormat::RED."В BuildBattle победил игрок ".$this->bb[11]);
        }

        $this->bb = array();
        $this->bb[0] = 0; // запущена ли игра
        $this->bb[1] = 0; // то что нужно построить

        $this->bb[2] = 0; // общая оценка 1 постройки
        $this->bb[3] = 0; // общая оценка 2 постройки
        $this->bb[4] = 0; // общая оценка 3 постройки
        $this->bb[5] = 0; // общая оценка 4 постройки
        $this->bb[6] = 0; // общая оценка 5 постройки

        $this->bb[7] = 0; // ник строителя 1 постройки
        $this->bb[8] = 0; // ник строителя 2 постройки
        $this->bb[9] = 0; // ник строителя 3 постройки
        $this->bb[10] = 0; // ник строителя 4 постройки
        $this->bb[11] = 0; // ник строителя 5 постройки

        $this->bb[12] = 0; // на какой арене сейчас игроки

    }
    public function PlayerItemHeldEvent(PlayerItemHeldEvent $event){
        $i = $event->getItem();
        $p = $event->getPlayer();
        if($i->getId() == 35 && $i->getDamage() == 5){
            $p->sendMessage(TextFormat::GREEN."Вы успешно оставили голос!");
            $p->sendTip(TextFormat::GREEN."Вы успешно оставили голос!");
            $event->setCancelled(true);
            if($this->bb[12] == "1"){
                $this->bb[2] = (int)$this->bb[2] + 3;
            } elseif($this->bb[12] == "2"){
                $this->bb[3] = (int)$this->bb[2] + 3;
            } elseif($this->bb[12] == "3"){
                $this->bb[4] = (int)$this->bb[2] + 3;
            } elseif($this->bb[12] == "4"){
                $this->bb[5] = (int)$this->bb[2] + 3;
            } elseif($this->bb[12] == "5"){
                $this->bb[6] = (int)$this->bb[2] + 3;
            }
            $event->getPlayer()->getInventory()->addItem(Item::get(1,0,1));
            $p->getInventory()->clearAll();
        } elseif($i->getId() == 35 && $i->getDamage() == 4){
            $p->sendMessage(TextFormat::YELLOW."Вы успешно оставили голос!");
            $p->sendTip(TextFormat::YELLOW."Вы успешно оставили голос!");
            $event->setCancelled(true);
            if($this->bb[12] == "1"){
                $this->bb[2] = (int)$this->bb[2] + 2;
            } elseif($this->bb[12] == "2"){
                $this->bb[3] = (int)$this->bb[2] + 2;
            } elseif($this->bb[12] == "3"){
                $this->bb[4] = (int)$this->bb[2] + 2;
            } elseif($this->bb[12] == "4"){
                $this->bb[5] = (int)$this->bb[2] + 2;
            } elseif($this->bb[12] == "5"){
                $this->bb[6] = (int)$this->bb[2] + 2;
            }
            $event->getPlayer()->getInventory()->addItem(Item::get(1,0,1));
            $p->getInventory()->clearAll();
        } elseif($i->getId() == 35 && $i->getDamage() == 14){
            $p->sendMessage(TextFormat::RED."Вы успешно оставили голос!");
            $p->sendTip(TextFormat::RED."Вы успешно оставили голос!");
            $event->setCancelled(true);
            if($this->bb[12] == "1"){
                $this->bb[2] = (int)$this->bb[2] + 1;
            } elseif($this->bb[12] == "2"){
                $this->bb[3] = (int)$this->bb[2] + 1;
            } elseif($this->bb[12] == "3"){
                $this->bb[4] = (int)$this->bb[2] + 1;
            } elseif($this->bb[12] == "4"){
                $this->bb[5] = (int)$this->bb[2] + 1;
            } elseif($this->bb[12] == "5"){
                $this->bb[6] = (int)$this->bb[2] + 1;
            }
            $event->getPlayer()->getInventory()->addItem(Item::get(1,0,1));
            $p->getInventory()->clearAll();
        }
    }
    public function BlockBreakEvent(BlockBreakEvent $event){
        if($event->getPlayer()->getGamemode() != 1){
            $event->setCancelled(true);
        } elseif($event->getBlock()->getId() == 20 && !$event->getPlayer()->isOp()){
            $event->setCancelled(true);
        }
    }
    public function BlockPlaceEvent(BlockPlaceEvent $event){
        if($event->getPlayer()->getGamemode() != 1){
            $event->setCancelled(true);
        } elseif($event->getBlock()->getId() == 20 && !$event->getPlayer()->isOp()){
            $event->setCancelled(true);
        }
    }
    public function PlayerGameModeChangeEvent(PlayerGameModeChangeEvent $event){
        $event->getPlayer()->getInventory()->addItem(Item::get(1,0,1));
        $event->getPlayer()->getInventory()->clearAll();
    }
}
