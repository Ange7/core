<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

if ($item_driver->item->mitem_page_id) {
    $page = \Nos\Page\Model_Page::find($item_driver->item->mitem_page_id);
    if (!empty($page)) {
        $params['text'] = e($item_driver->title());
        if (\Nos\Nos::main_controller()->getUrl() === $page->url()) {
            $params['class'] .= ' '.\Arr::get($params, 'active_class', 'active');
        }
        echo $page->htmlAnchor($params);
        return;
    }
}
echo html_tag('div', $params, e($item_driver->title()));
