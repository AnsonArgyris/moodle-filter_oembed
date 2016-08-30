<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package filter_oembed
 * @author Mike Churchward <mike.churchward@poetgroup.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2016 The POET Group
 */

namespace filter_oembed\provider;

/**
 * Base class for oembed providers.
 */
class provider {
    /**
     * @var string
     */
    protected $provider_name = '';

    /**
     * @var string
     */
    protected $provider_url = '';

    /**
     * @var endpoints
     */
    protected $endpoints = [];

    /**
     * Constructor.
     * @param $data JSON decoded array or a data object containing all provider data.
     */
    public function __construct($data = null) {
        if (is_object($data)) {
            $data = (array)$data;
        }
        $this->provider_name = $data['provider_name'];
        $this->provider_url = $data['provider_url'];

        // If the endpoint data is a string, assume its a json encoded string.
        if (is_string($data['endpoints'])) {
            $data['endpoints'] = json_decode($data['endpoints'], true);
        }
        if (is_array($data['endpoints'])) {
            foreach ($data['endpoints'] as $endpoint) {
                $this->endpoints[] = new endpoint($endpoint);
            }
        } else {
            throw new \coding_exception('"endpoint" data must be an array for '.get_class($this));
        }
    }

    /**
     * Magic method for getting properties.
     * @param string $name
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($name) {
        $allowed = ['provider_name', 'provider_url', 'endpoints'];
        if (in_array($name, $allowed)) {
            return $this->$name;
        } else {
            throw new \coding_exception($name.' is not a publicly accessible property of '.get_class($this));
        }
    }

    /**
     * Function to turn an endpoint into JSON, since json_encode doesn't work on objects.
     * @return JSON encoded array.
     */
    public function endpoints_to_json() {
        $endpointsarr = [];
        foreach ($this->endpoints as $endpoint) {
            $endpointsarr[] = [
                'schemes' => $endpoint->schemes,
                'url' => $endpoint->url,
                'discovery' => $endpoint->discovery,
                'formats' => $endpoint->formats,
            ];
        }
        return json_encode($endpointsarr);
    }
}