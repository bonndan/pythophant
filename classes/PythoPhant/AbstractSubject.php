<?php /** generated by PythoPhant on 2012/03/31 17:29:14 from AbstractSubject. #0c924fbd28716356c2e791fd72df0d96 */
/**
 * base class of subjects
 * 
 * 
 */
abstract class PythoPhant_AbstractSubject
{
    /**
     * observers
     * @var SplObjectStorage 
     */
    private $observers;

    /**
     * the constructor initialises the observer object storage 
     */
    public function __construct()
    {
        $this->observers = new SplObjectStorage();
    }

    /**
     * attach an observer
     * 
     * @param PythoPhant_Observer $observer
     * 
     * @return \PythoPhant_DirectoryWatcher 
     */
    public function attach(PythoPhant_Observer $observer)
    {
        $this->observers->attach($observer);
        return $this;
    }

    /**
     *
     * @param PythoPhant_Observer $observer
     * @return \PythoPhant_DirectoryWatcher 
     */
    public function detach(PythoPhant_Observer $observer)
    {
        $this->observers->detach($observer);
        return $this;
    }

    /**
     * get the observer storage
     * @return SplObjectStorage 
     */
    protected function getObservers()
    {
        return $this->observers;
    }
    
    /**
     * notify all observers
     * 
     * @param PythoPhant_Event $event 
     */
    protected function notify(PythoPhant_Event $event)
    {
        foreach ($this->getObservers() as $observer) {
            $observer->update($event);
        }

    }

}

