<?php
namespace App\Services;


use App\Repositories\ShortcutRepository;
use Exception;


/**
 * Class ShortcutService
 * @package App\Services
 */
class ShortcutService{

    /**
     * @var $shortcutRepository
     */
    protected $shortcutRepository;


    /**
     * ShortcutService constructor.
     * @param ShortcutRepository $shortcutRepository
     */
    public function __construct(ShortcutRepository $shortcutRepository)
    {
        $this->shortcutRepository = $shortcutRepository;
    }


    public function getAllShortcut()
    {
        try{
            return $this->shortcutRepository->getAllShortcut();
        }catch (Exception $exception ){
            return $exception->getMessage();
        }
    }

    /**
     * Retrieve Shortcut list
     *
     * @return mixed|string
     */
    public function getShortcutWithUser($request)
    {
        try{
            return $this->shortcutRepository->getShortcutWithUser($request);
        }catch (Exception $exception ){
            return $exception->getMessage();
        }

    }


    /**
     * Add shortcut to user profile
     *
     * @param $request
     * @return string
     */
    public function addShortcutToUserProfile($request)
    {
        try{
            return $this->shortcutRepository->addShortcutToUserProfile($request);
        }catch (Exception $exception ){
            return $exception->getMessage();
        }
    }

    /**
     * Remove shortcut from user profile
     *
     * @param $request
     * @return string
     */
    public function removeShortcutFromUserProfile($request)
    {
        try{
            return $this->shortcutRepository->removeShortcutFromUserProfile($request);
        }catch (Exception $exception ){
            return $exception->getMessage();
        }
    }


}