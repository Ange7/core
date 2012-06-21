<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos;

class Controller_Admin_Noviusos extends Controller
{
    public $template = 'nos::admin/html';

    public function before()
    {
        parent::before();

        if (!\Nos\Auth::check())
        {
            \Response::redirect('/admin/nos/login'.($_SERVER['REDIRECT_URL'] ? '?redirect='.urlencode($_SERVER['REDIRECT_URL']) : ''));
            exit();
        }

        $this->auto_render = false;
    }

    public function after($response)
    {
        foreach (array(
            'title' => 'Administration',
            'base' => \Uri::base(false),
            'require' => 'static/novius-os/admin/vendor/requirejs/require.js',
        ) as $var => $default)
        {
            if (empty($this->template->$var))
            {
                $this->template->$var = $default;
            }
        }
        $ret = parent::after($response);
        $this->template->set(array(
            'css' => \Asset::render('css'),
            'js' => \Asset::render('js'),
        ), false, false);

        return $ret;
    }

    public function action_index()
    {
        $view = \View::forge('admin/noviusos');

        $user = \Session::user();
        $user_configuration = unserialize($user->user_configuration);
        $deep_linking_url = \Input::get('tab', null);

        $osTabs = array(
            'panelId' => 'noviusospanel',
            'url' => 'admin/nos/noviusos/appstab',
            'iconClasses' => 'nos-icon32',
            'iconSize' => 32,
            'label' => 'Novius OS',
        );
        $trayTabs = array(
            array(
                'url' => 'admin/nos/tray/help',
                'iconClasses' => 'nos-icon24 nos-icon24-help',
                'label' => __('Help'),
                'iconSize' => 24,
            ),
            array(
                'url' => 'admin/nos/tray/account',
                'iconClasses' => 'nos-icon24 nos-icon24-account',
                'label' => __('Account'),
                'iconSize' => 24,
            ),
        );
        if ($user->check_permission('nos_tray', 'access'))
        {
            array_unshift($trayTabs, array(
                'url' => 'admin/nos/tray/appmanager',
                'iconClasses' => 'nos-icon24 nos-icon24-noviusstore',
                'label' => __('Applications manager'),
                'iconSize' => 24,
            ));
        }
        $count_trayTabs = count($trayTabs);


        if (!empty($deep_linking_url))
        {
            if (!isset($user_configuration['tabs'])) {
                $user_configuration['tabs']  = array();
            }
            if (!isset($user_configuration['tabs']['tabs'])) {
                $user_configuration['tabs']['tabs']  = array();
            }
            $openRank = null;
            $found = false;

            // Native = OS + tray
            $nativeTabs = $trayTabs;
            array_unshift($nativeTabs, $osTabs);

            // Search native tabs
            foreach ($nativeTabs as $i => $tab)
            {
                if ($tab['url'] == $deep_linking_url)
                {
                    $user_configuration['tabs']['selected'] = $i;
                    $found = true;
                }
            }

            // Search user tabs
            if (!$found)
            {
                foreach ($user_configuration['tabs']['tabs'] as $i => &$tab)
                {
                    if ($tab['url'] == $deep_linking_url)
                    {
                        $openRank = $tab['openRank'];
                        $tab['openRank'] = 0;
                        $user_configuration['tabs']['selected'] = $i + $count_trayTabs + 1;
                        $found = true;
                    }
                }
                unset($tab);
            }

            // Tab was not found found, add it
            if (!$found)
            {
                $user_configuration['tabs']['selected'] = count($user_configuration['tabs']['tabs']) + 1 + $count_trayTabs;
                $openRank = 1;
                $user_configuration['tabs']['tabs'][] = array(
                    'url' => $deep_linking_url,
                    'openRank' => 1,
                );

                // Rank from every tab goes up
                foreach ($user_configuration['tabs']['tabs'] as $i => &$tab)
                {
                    if ($tab['openRank'] < $openRank)
                    {
                        $tab['openRank']++;
                    }
                }
                unset($tab);
            }
        }

        $ostabs = array(
            'initTabs' => array(),
            'trayTabs' => $trayTabs,
            'appsTab' => $osTabs,
            'newTab' => array(
                'panelId' => 'noviusospanel',
                'url' => 'admin/nos/noviusos/appstab',
                'iconClasses' => 'nos-icon16 nos-icon16-add',
                'iconSize' => 16,
            ),
            'user_configuration' => $user_configuration,
        );

        $view->set('ostabs', \Format::forge($ostabs)->to_json(), false);
        $this->template->body = $view;
        return $this->template;
    }

    public function action_appstab()
    {
        \Config::load(APPPATH.'data'.DS.'config'.DS.'launchers.php', 'launchers');
        $launchers = \Config::get('launchers', array());

        \Config::load('nos::admin/launchers_default', true);
        $launchers_default = \Config::get('nos::admin/launchers_default', array());
        $launchers = array_merge($launchers, $launchers_default);
        $launchers = \Config::mergeWithUser('misc.apps', $launchers);

        $apps = array();
        foreach ($launchers as $key => $app)
        {
            if (!empty($app['url']) && !empty($app['icon64']))
            { // do we have to display the application?
                //\Debug::dump($app['application'], Permission::check($app['application'], 'access'));
                if (!isset($app['application']) || Permission::check($app['application'], 'access'))
                { // do we have the rights to access the application?
                    $app['key'] = $key;
                    $apps[] = $app;
                }
            }
        }
        if (count($apps) > 0)
        {
            $apps = \Arr::sort($apps, 'order', 'asc');
        }

        //\Debug::dump($apps);

        $user = \Session::get('logged_user', false);
        $background_id = \Arr::get($user->getConfiguration(), 'misc.display.background');
        $background = $background_id ? Model_Media::find($background_id) : false;

        $view = \View::forge('admin/appstab', array(
                    'apps' => $apps,
                ));
        $view->set('background', $background, false);
        return $view;
    }

    public function action_save_user_configuration()
    {
        $key = \Input::post('key');
        $new_config = \Input::post('configuration');

        if (!$new_config)
        {
            $new_config = array();
        }
        $new_config = $this->convertFromPost($new_config);


        $json = array(
            'success' => true,
        );

        $user = \Session::get('logged_user', false);
        if ($user)
        {
            if (!$user->user_configuration)
            {
                $user_configuration = array();
            }
            else
            {
                $user_configuration = unserialize($user->user_configuration);
            }
            $configuration = &$user_configuration;
            \Arr::set($configuration, $key, $new_config);

            $user->user_configuration = serialize($user_configuration);
            $user->save();
            \Session::set('logged_user', $user);
        }


        \Response::json($json);
    }

    protected function convertFromPost($arr)
    {
        if (is_array($arr))
        {
            foreach ($arr as $key => $value)
            {
                if (is_array($value))
                {
                    $arr[$key] = $this->convertFromPost($arr[$key]);
                }
                else
                {
                    $arr[$key] = $arr[$key] == 'true' ? true : ($arr[$key] == 'false' ? false : $arr[$key]);
                    $arr[$key] = is_numeric($arr[$key]) ? floatval($arr[$key]) : $arr[$key];
                }
            }
        }
        return $arr;
    }

}


