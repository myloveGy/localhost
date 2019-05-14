<?php

namespace lib\mode;

class Observer implements \SplObserver
{
    public function update(\SplSubject $subject)
    {
        echo __CLASS__ . ' - ' . $subject->getName();
    }
}

class Observer1 implements \SplObserver
{
    public function update(\SplSubject $subject)
    {
        echo __CLASS__ . ' - ' . $subject->getName();
    }
}

class MySubject implements \SplSubject
{
    /**
     * @var \SplObjectStorage 观察者数组
     */
    private $Observers;

    /**
     * @var string 名称
     */
    private $name;

    public function __construct($name)
    {
        $this->observers = new \SplObjectStorage();
        $this->name = $name;
    }

    public function attach(\SplObserver $observer)
    {
        $this->observers->attach($observer);
    }

    public function detach(\SplObserver $observer)
    {
        $this->observers->detach($observer);
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function getName()
    {
        return $this->name;
    }
}
