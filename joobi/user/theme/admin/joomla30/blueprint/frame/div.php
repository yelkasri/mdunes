<?php 
/** @copyright Copyright (c) 2007-2016 Joobi. All rights reserved.

* @license GNU GPLv3 */

defined('JOOBI_SECURE') or die('J....');
class WPane_div extends WPane {
	function miseEnPageTwo(&$params,$value) {
		$this->content = $value;
		return $value;
	}
	public function startPane($params) {
		return $this->content;
	}
	public function endPane() {
		return $this->content . $this->crlf;
	}
	public function startPage($params) {
		return $this->content;
	}
	function add($content) {
		$this->content .= $content;
		return $this->content;
	}
	public function line() {
	}
	public function body() {
		$this->endPane();
	}
	public function endPage() {
		return $this->content . $this->crlf;
	}
}
