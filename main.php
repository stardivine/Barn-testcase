<?php
interface Animal {
}
interface CollectMilk {
    public function getMilk(): int;
}
interface CollectEggs {
    public function getEggs(): int;
}
class Cow implements Animal, CollectMilk {
    public $id;
    public function __construct()
    {
        $this->id = substr(md5(rand()), 0, 8); //определеяем уникальный номер (id) для коровы
    }
    public function getMilk(): int
    {
        return rand(8, 12); //выдает 8-12 литров молока
    }
}
class Chicken implements Animal, CollectEggs {
    public $id;
    public function __construct()
    {
        $this->id = substr(md5(rand()), 0, 8); //определеяем уникальный номер (id) для курицы
    }
    public function getEggs(): int
    {
        return rand(0, 1); //выдает 0-1 яйцо
    }
}
interface Storage { //хранилище
    public function addMilk(int $liters);
    public function addEggs(int $eggsCount);
    public function getFreeSpaceForMilk(): int;
    public function getFreeSpaceForEggs(): int;
    public function howMuchMilk(): int;
    public function howMuchEggs(): int;
}
class Barn implements Storage { //амбар
    private $milkLiters = 0;
    private $eggsCount = 0;
    private $milkLimit = 0;
    private $eggsLimit = 0;
    public function __construct(int $milkLimit, int $eggsLimit)
    {
        $this->milkLimit = $milkLimit; //указываем максимальную вместимость по молоку
        $this->eggsLimit = $eggsLimit; //указываем максимальную вместимость по яйцам
    }
    public function addMilk(int $liters)
    {
        $freeSpace = $this->getFreeSpaceForMilk();
        if ($freeSpace === 0) { //абмар заполнен, места нет
            return;
        }
        if ($freeSpace < $liters) { //дозаполняем амбар, насколько хватает места
            $this->milkLiters = $this->milkLimit;
            return;
        }
        $this->milkLiters += $liters; //льем все молоко, что надоили
    }
    public function addEggs(int $eggsCount) //для яиц аналогичные действия
    {
        $freeSpace = $this->getFreeSpaceForEggs();
        if ($freeSpace === 0) {
            return;
        }
        if ($freeSpace < $eggsCount) {
            $this->eggsCount = $this->eggsLimit;
            return;
        }
        $this->eggsCount += $eggsCount;
    }
    public function getFreeSpaceForMilk(): int //считаем свободное место молоко
    {
        return $this->milkLimit - $this->milkLiters;
    }
    public function getFreeSpaceForEggs(): int //считаем свободное место яйца
    {
        return $this->eggsLimit - $this->eggsCount;
    }
    public function howMuchMilk(): int
    {
        return $this->milkLiters;
    }
    public function howMuchEggs(): int
    {
        return $this->eggsCount;
    }
}
class Farm { //класс фермы
    private $name;
    private $storage;
    private $animals = [];
    public function __construct(string $name, Storage $storage)
    {
        $this->name = $name;
        $this->storage = $storage;
    }
    public function returnMilk()
    {
        return $this->storage->howMuchMilk();
    }
    public function returnEggs()
    {
        return $this->storage->howMuchEggs();
    }
    public function addAnimal(Animal $animal)
    {
        $this->animals[] = $animal; //добавляем животное в массив
    }
    public function collectProducts() //сбор продукции
    {
        foreach ($this->animals as $animal)
        {
            if ($animal instanceOf CollectMilk) { //если относится к молокодающим, то сбор молока
                $milkLiters = $animal->getMilk();
                $this->storage->addMilk($milkLiters);
            }
            if ($animal instanceOf CollectEggs) { //с яйценесущих яйца
                $eggsCount = $animal->getEggs();
                $this->storage->addEggs($eggsCount);
            }
        }
    }
}
$barn = new Barn($milkLimit = 300, $eggsLimit = 500); //создаем амбар вместимостью 300 литров молока и 500 яичек
$myFarm = new Farm('MyFirstFarm', $barn);
for ($i = 1; $i < 20; $i++) {
    $myFarm->addAnimal(new Chicken()); //сажаем в ферму курочек
}

for ($i = 1; $i < 10; $i++) {
    $myFarm->addAnimal(new Cow()); //и коров
}

$myFarm->collectProducts(); //собираем продукты

echo 'Мы собрали: '.$myFarm->returnMilk().' литров молока'."\r\n"; //выводим результат сбора
echo 'Мы собрали: '.$myFarm->returnEggs().' яиц'."\r\n";