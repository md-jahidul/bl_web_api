<?php
namespace App\Repositories;


use App\Models\Shortcut;
use App\Models\ShortcutUser;
use App\Models\User;


/**
 * Class ShortcutRepository
 * @package App\Repositories
 */
class ShortcutRepository extends BaseRepository
{

    /**
     * @var Shortcut
     */

    protected $model;


    /**
     * ShortcutRepository constructor.
     * @param Shortcut $model
     */
    public function __construct(Shortcut $model)
    {
        $this->model = $model;
    }


    /**
     * Retrieve shortcut list
     *
     * @return mixed
     */
    public function getAllShortcut()
    {
       return Shortcut::select('tittle', 'icon')->get();

    }

    /**
     * Retrieve shortcut list for specific user
     *
     * @param $request
     * @return mixed
     */
    public function getShortcutWithUser($request)
    {
        $user_id = $request->input('user_id');

        $shortcut_count = ShortcutUser::where('user_id',$user_id)->count();

        if($shortcut_count > 0)
        {
            $shortcuts = Shortcut::select('tittle', 'icon')
                ->join('shortcut_user', 'shortcuts.id', '=', 'shortcut_user.shortcut_id')
                ->where('user_id',$user_id)
                ->orderby('sequence')
                ->get();

        } else {

            $shortcuts = Shortcut::select('tittle', 'icon')->get();
        }

        return $shortcuts;
    }


    /**
     * Added shortcut to user profile
     *
     * @param $request
     * @return string
     */
    public function addShortcutToUserProfile($request)
    {
        $user_id = $request->input('user_id');

        $shortcut_id = $request->input('shortcut_id');

        $limit_status = $this->checkShortcutLimit($user_id);

        if($limit_status){
            return "Can not add any more shortcut";
        }

        $shortcut = $this->checkExistOrNot($user_id, $shortcut_id);

        if($shortcut){
            return "You have already added this";
        }

        $this->attachShortcutToUserProfile($shortcut_id, $user_id);

        return 'Success';

    }


    /**
     * Attach shortcut to user profile
     *
     * @param $shortcut_id
     * @param $user_id
     */
    public function attachShortcutToUserProfile($shortcut_id, $user_id): void
    {
        $shortcut = Shortcut::find($shortcut_id);

        $sequence = ShortcutUser::where('user_id', $user_id)->max('sequence');

        $user = User::find($user_id);

        $shortcut->users()->attach($user, ['sequence' => $sequence + 1]);
    }

    /**
     * Check Shortcut limit
     *
     * @param $user_id
     * @return bool
     */
    public function checkShortcutLimit($user_id)
    {
        $shortcut_limit = env('SHORTCUT_LIMIT');

        $shortcut_count = ShortcutUser::where('user_id',$user_id)->count();

        if($shortcut_count ==  $shortcut_limit) {
            return  true;
        }

        return false;

    }


    /**
     * Remove shortcut from user profile
     *
     * @param $request
     * @return string
     */
    public function removeShortcutFromUserProfile($request)
    {
        $user_id = $request->input('user_id');

        $shortcut_id = $request->input('shortcut_id');

        $shortcut = Shortcut::find($shortcut_id);

        $user = User::find($user_id);

        $shortcut->users()->detach($user);

        return 'Success';
    }


    /**
     * @param $user_id
     * @param $shortcut_id
     * @return mixed
     */
    public function checkExistOrNot($user_id, $shortcut_id)
    {
        $shortcut = ShortcutUser::where('user_id', $user_id)
            ->where('shortcut_id', $shortcut_id)
            ->first();

        return $shortcut;
    }


}