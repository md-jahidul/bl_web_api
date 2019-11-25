<?php

namespace App\Repositories;

use App\Models\Notification;

/**
 * Class NotificationRepository
 * @package App\Repositories
 */
class NotificationRepository
{

    /**
     * @var Notification
     */
    protected $model;


    /**
     * NotificationRepository constructor.
     * @param Notification $model
     */
    public function __construct()
    {
        $this->model = new Notification();
    }


    /**
     * Retrieve Notification list
     *
     * @return mixed
     */
    public function getAllNotificationsWithPagination()
    {
        return $this->model->paginate(15);
    }
}
