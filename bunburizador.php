<?php

interface Ibunburizador {
	public function getUrl();
}

abstract class bunburizador_abstract {
	const NUM_PAGES = 3;
	
	protected $language;
	
	protected $phrases = array();
	
	protected function __construct($language) {
		$this->language = $language;
	}
	
	static public function factory($language = "es") {
		$bunburizador = null;
		switch($language) {
			case 'es':
				$bunburizador = new bunburizador($language);
				break;
			case 'en':
				$bunburizador = new nirvanizer($language);
				break;
			default:
				throw new Exception("Language not supported");
		}
		return $bunburizador;
	}
	
	protected function getText($page) {
		$myPage = new DOMDocument();
		$myPage->loadHTML($page);
		$myPageElements = $myPage->getElementById("bodyContent");
		
		$nodeList = $myPageElements->getElementsByTagName("p");
		$phrases = array();
		foreach($nodeList as $node) {
			$phrases[] = $node->nodeValue;
		}
		return $phrases;
	}
	
	protected function getParsedPage() {
		$page = file_get_contents($this->getUrl());
		$phrases = $this->getText($page);
		$songPhrases = array();
		foreach($phrases as $phrase) {
			$dotPhrases = explode(".", $phrase);
			foreach($dotPhrases as $dotPhrase) {
				$commaPhrases = explode(",", $dotPhrase);
				$songPhrases = array_merge($songPhrases, $commaPhrases);
			}
		}
		return $songPhrases;
	}
	
	protected function purgePhrases() {
		$phrases = array();
		foreach($this->phrases as $phrase) {
			if(preg_match("/\[.\]/", $phrase)) {
			}
			else {
				$phrases[] = $phrase;
			}
		}
		$this->phrases = $phrases;
		return $this;
	}

	protected function getPages() {
		$this->phrases = array();
		for($i = 0; $i < self::NUM_PAGES; $i++) {
			$this->phrases = array_merge($this->phrases, $this->getParsedPage());
		}
		return $this;
	}
	
	public function getSong() {
		$this->getPages()->purgePhrases();
		$randomPhrases = rand(6, 30);
		$song = '';
		for($i = 0; $i < $randomPhrases; $i++) {
			$randomPhrase = rand(0, count($this->phrases));
			$song .= $this->phrases[$randomPhrase]."\n";
		}
		return $song;
	}
}

class bunburizador extends bunburizador_abstract implements Ibunburizador {
	public function getUrl() {
		return "http://es.wikipedia.org/wiki/Especial:Aleatoria";
	}
}

class nirvanizer extends bunburizador_abstract implements Ibunburizador {
	public function getUrl() {
		return "http://en.wikipedia.org/wiki/Special:Random";
	}
}