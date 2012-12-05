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

class Renderer_Media extends \Fieldset_Field
{
    /**
     * Standalone build of the media renderer.
     *
     * @param  array  $renderer Renderer definition (attributes + renderer_options)
     * @return string The <input> tag + JavaScript to initialise it
     */
    public static function renderer($renderer = array())
    {
        list($attributes, $renderer_options) = static::parse_options($renderer);
        static::hydrate_options($renderer_options, isset($attributes['value']) ? $attributes['value'] : null);
        $attributes['data-media-options'] = htmlspecialchars(\Format::forge()->to_json($renderer_options));

        return '<input '.array_to_attr($attributes).' />'.static::js_init($attributes['id']);
    }

    protected $options = array();

    public function __construct($name, $label = '', array $renderer = array(), array $rules = array(), \Fuel\Core\Fieldset $fieldset = null)
    {
        list($attributes, $this->options) = static::parse_options($renderer);
        parent::__construct($name, $label, $attributes, $rules, $fieldset);
    }

    /**
     * How to display the field
     * @return type
     */
    public function build()
    {
        parent::build();
        $this->fieldset()->append(static::js_init($this->get_attribute('id')));
        static::hydrate_options($this->options, $this->value);
        $this->set_attribute('data-media-options', htmlspecialchars(\Format::forge()->to_json($this->options)));

        return (string) parent::build();
    }

    /**
     * Parse the renderer array to get attributes and the renderer options
     * @param  array $renderer
     * @return array 0: attributes, 1: renderer options
     */
    protected static function parse_options($renderer = array())
    {
        $renderer['class'] = (isset($renderer['class']) ? $renderer['class'] : '').' media';

        if (empty($renderer['id'])) {
            $renderer['id'] = uniqid('media_');
        }

        // Default options of the renderer
        $renderer_options = array(
            'mode' => 'image',
            'inputFileThumb' => array(
                'title' => __('Image from the media library'),
                'texts' => array(
                    'add'            => __('Add'),
                    'edit'           => __('Edit'),
                    'delete'         => __('Delete'),
                    'wrongExtension' => __('This extension is not allowed'),
                ),
            ),
        );

        if (!empty($renderer['renderer_options'])) {
            $renderer_options = \Arr::merge($renderer_options, $renderer['renderer_options']);
        }
        unset($renderer['renderer_options']);

        return array($renderer, $renderer_options);
    }

    /**
     * Hydrate the options array to fill in the media URL for the specified value
     * @param array $options
     * @param int   $media_id
     */
    protected static function hydrate_options(&$options, $media_id = null)
    {
        if (!empty($media_id)) {
            $media = \Nos\Media\Model_Media::find($media_id);
            if (!empty($media)) {
                $options['inputFileThumb']['file'] = $media->is_image() ? $media->get_public_path_resized(64, 64) : $media->get_public_path();
            }
        }
    }

    /**
     * Generates the JavaScript to initialise the renderer
     *
     * @param   string  HTML ID attribute of the <input> tag
     * @return string JavaScript to execute to initialise the renderer
     */
    protected static function js_init($id)
    {
        return \View::forge('renderer/media', array(
            'id' => $id,
        ), false);
    }
}
