<?php
/*
 * Laconica - a distributed open-source microblogging tool
 * Copyright (C) 2008, Controlez-Vous, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
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
 */

if (!defined('LACONICA')) { exit(1); }

define('DEFAULT_RSS_LIMIT', 48);

class Rss10Action extends Action {

	function handle($args) {
		parent::handle($args);
		$limit = (int) $this->trimmed('limit');
		if ($limit == 0) {
			$limit = DEFAULT_RSS_LIMIT;
		}
		$this->show_rss($limit);
	}

	function init() {
		return true;
	}

	function get_notices() {
		return array();
	}

	function get_channel() {
		return array('url' => '',
					 'title' => '',
					 'link' => '',
					 'description' => '');
	}

	function get_image() {
		return NULL;
	}

	function show_rss($limit=0) {

		if (!$this->init()) {
			return;
		}

		$notices = $this->get_notices($limit);

		$this->init_rss();
		$this->show_channel($notices);
		$this->show_image();

		foreach ($notices as $n) {
			$this->show_item($n);
		}

		$this->end_rss();
	}

	function show_channel($notices) {
		global $config;

		$channel = $this->get_channel();
		$image = $this->get_image();

		common_element_start('channel', array('rdf:about' => $channel['url']));
		common_element('title', NULL, $channel['title']);
		common_element('link', NULL, $channel['link']);
		common_element('description', NULL, $channel['description']);
		common_element('cc:licence', array('rdf:resource' => $config['license']['url']));

		if ($image) {
			common_element('image', array('rdf:resource' => $image));
		}

		common_element_start('items');
		common_element_start('rdf:Seq');

		foreach ($notices as $notice) {
			common_element('rdf:li', array('rdf:resource' => $notice->uri));
		}

		common_element_end('rdf:Seq');
		common_element_end('items');

		common_element_end('channel');
	}

	function show_image() {
		$image = $this->get_image();
		if ($image) {
			$channel = $this->get_channel();
			common_element_start('image', array('rdf:about' => $image));
			common_element('title', NULL, $channel['title']);
			common_element('link', NULL, $channel['link']);
			common_element('url', NULL, $image);
			common_element_end('image');
		}
	}

	function show_item($notice) {
		global $config;
		$profile = Profile::staticGet($notice->profile_id);
		$nurl = common_local_url('shownotice', array('notice' => $notice->id));
		common_element_start('item', array('rdf:about' => $notice->uri));
		common_element('title', NULL, $notice->content);
		common_element('link', NULL, $nurl);
		common_element('description', NULL, $profile->nickname."'s status on ".common_exact_date($notice->created));
		common_element('dc:date', NULL, common_date_w3dtf($notice->created));
		common_element('dc:creator', NULL, ($profile->fullname) ? $profile->fullname : $profile->nickname);
		common_element('cc:licence', array('rdf:resource' => $config['license']['url']));
		common_element_end('item');
	}

	function init_rss() {
		header('Content-Type: application/rdf+xml');

		common_start_xml();
		common_element_start('rdf:RDF', array('xmlns:rdf' =>
											  'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
											  'xmlns:dc' =>
											  'http://purl.org/dc/elements/1.1/',
											  'xmlns:cc' =>
											  'http://web.resource.org/cc/',
											  'xmlns' => 'http://purl.org/rss/1.0/'));
	}

	function end_rss() {
		common_element_end('rdf:RDF');
	}
}
