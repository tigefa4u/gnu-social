<?php
/**
 * StatusNet, the distributed open-source microblogging tool
 *
 * Base class for API actions that require "bare auth". Bare auth means
 * authentication is required only if the action is called without an argument
 * or query param specifying user id.
 *
 * PHP version 5
 *
 * LICENCE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  API
 * @package   StatusNet
 * @author    Zach Copley <zach@status.net>
 * @copyright 2009 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License version 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET')) {
    exit(1);
}

require_once INSTALLDIR.'/lib/apiauth.php';

class ApiBareAuthAction extends ApiAuthAction
{
    /**
     * Does this API resource require authentication?
     *
     * @return boolean true or false
     */

    function requiresAuth()
    {
        // If the site is "private", all API methods except statusnet/config
        // need authentication

        if (common_config('site', 'private')) {
            return true;
        }

        // check whether a user has been specified somehow

        $id           = $this->arg('id');
        $user_id      = $this->arg('user_id');
        $screen_name  = $this->arg('screen_name');

        if (empty($id) && empty($user_id) && empty($screen_name)) {
            return true;
        }

        return false;
    }

}