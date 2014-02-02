<?php
//require('phpquery/phpQuery/phpQuery.php');

class EdukacjaCl 
{

	private $ch;
	private $HTML;
	private $URL= [
		'home' => "https://edukacja.pwr.wroc.pl/EdukacjaWeb/studia.do",
		'logIn' => "https://edukacja.pwr.wroc.pl/EdukacjaWeb/logInUser.do",
		'zapisy' => "https://edukacja.pwr.wroc.pl/EdukacjaWeb/zapisy.do?href=#hrefZapisySzczSlu"
	];
	private $UrlEvents = [
		''
	];
	private $dataToSend;
	private $courses;
	private $semestr;
	private $dane = [];
	public function EdukacjaCl($user, $password)
	{
		$this->ch = curl_init ();
		$cookie = '';
		//some starting options
		curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookie);
		curl_setopt($this->ch, CURLOPT_VERBOSE, 1);
		curl_setopt($this->ch, CURLOPT_HEADER, 0);  
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, true);


		$this->setUser($user, $password);
	}
	function __destruct(){
		curl_close($this->ch);
	}
	private function loadPage()
	{
		$this->HTML = curl_exec($this->ch);
	}
	public function showPage()
	{
		echo $this->HTML;
	}
	public function setPOST($option)
	{
		//ustawic dane!
		if(is_bool($option)){
			if($option == true){
				curl_setopt ($this->ch, CURLOPT_POST, true);
				curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $this->dataToUrl());
			} else {
				curl_setopt ($this->ch, CURLOPT_POST, false);
				curl_setopt ($this->ch, CURLOPT_POSTFIELDS, false);
			}
		}
	}
	public function setUser($login, $password){
		$this->dataToSend["login"] = $login;
		$this->dataToSend["password"] = $password;
	}
	private function clearData(){
		$this->dataToSend = [];
	}
	private function dataToUrl(){
		$dataProcessed='';
		foreach ($this->dataToSend as $key =>$value){
			$dataProcessed .= $key . '=' . urlencode($value) . '&';
		}
		$this->clearData();
		return $dataProcessed;
	}
	public function logIn(){
		curl_setopt($this->ch, CURLOPT_URL, $this->URL['home']);
		$this->loadPage();
		$this->getTokens();
		$this->setPOST(true);
		curl_setopt($this->ch, CURLOPT_URL, $this->URL['logIn']);
		$this->loadPage();
		$doc = phpQuery::newDocumentHtml($this->HTML);
		$this->dane['uzytkownik'] = pq('td[class="ZALOGOWANY_UZYT"]')->text();
	}
	public function getDane(){
		return $this->dane;
	}
	private function getTokens(){
		$doc = phpQuery::newDocumentHtml($this->HTML);
		$this->dataToSend['clEduWebSESSIONTOKEN'] = pq('input[name="clEduWebSESSIONTOKEN"]')->attr('value');
		$this->dataToSend['cl.edu.web.TOKEN'] = pq('input[name="cl.edu.web.TOKEN"]')->attr('value');
	}
	private function getUrl($title){
		$doc = phpQuery::newDocumentHtml($this->HTML);
		$urlPath = "https://edukacja.pwr.wroc.pl" . pq('.GUZIK[title="'.$title.'"]')->attr('href');
		return $urlPath;
	}
	public function goToPathFromMenu($menuOption){
		curl_setopt($this->ch, CURLOPT_URL, $this->getUrl($menuOption));
		$this->setPOST(false);
		$this->loadPage();
	}
	public function wybierzSemestr(){
		$index = 0;
		if ($this->sprawdzPrawa($index)!="Zapisany na kursy"){
			$index++;
		}
		$doc = phpQuery::newDocumentHtml($this->HTML);
		$urlPath = "https://edukacja.pwr.wroc.pl" . pq('a[title="Wybierz wiersz"]:eq('.$index.')')->attr('href');
		$this->semestr = pq('a[title="Wybierz wiersz"]:eq('.$index.')')->parent('td')->nextAll('td')->eq(2)->text();
		curl_setopt($this->ch, CURLOPT_URL, $urlPath);
		$this->setPOST(false);
		$this->loadPage();
	}
	public function idzDoPlanu(){
		curl_setopt($this->ch, CURLOPT_URL, $this->URL['zapisy']);
		$this->getTokens();
		$this->dataToSend['event'] = 'WyborZapisowWidok';
		$this->setPost(true);
		$this->loadPage();
	}
	public function sprawdzPrawa($index){
		$doc = phpQuery::newDocumentHtml($this->HTML);
		$urlPath = pq('a[title="Wybierz wiersz"]:eq('.$index.')')->attr('href');
		$statusZapisow = pq('a[title="Wybierz wiersz"]:eq('.$index.')')->parent('td')->parent('tr')->nextAll('tr')->eq(0)->text();
		return trim($statusZapisow);
	}
	public function getDataFromSchedule(){
		$doc = phpQuery::newDocumentHtml($this->HTML);

		preg_match_all('/[a-zA-Z]{1}\d{2}-\d{1,2}[a-zA-Z]{1}/i', $this->HTML, $matches);

		$courses = [];
		foreach ($matches as $m){
			foreach ($m as $key=>$match){
				$pos1 = strrpos($this->HTML, $match);
				$pos2 = (isset($m[$key+1])?$m[$key+1]:0) ? strrpos($this->HTML, isset($m[$key+1])?$m[$key+1]:0) : strlen($this->HTML);
				$course = substr($this->HTML, $pos1, ($pos2 - $pos1));
				$doc = phpQuery::newDocumentHtml($course);
				preg_match('/(pn|wt|śr|cz|pt)(\/(TP|TN))?\s+(\d{2}):(\d{2})-(\d{2}):(\d{2}),?\s(bud.)?\s([A-Z]+-\d+),?\s(sala)?\s([^\s]+)/i', $course, $timestapmps);
				if(count($timestapmps) == 0)
				{
					continue;	
				}
				$this->courses[$key]["nazwa"] = trim(pq('td:eq(1)')->text());
				$this->courses[$key]["prowadzacy"] = trim(pq('tr:eq(2)')->find('td:eq(0)')->text());
				$this->courses[$key]["rodzaj"] = trim(pq('tr:eq(2)')->find('td:eq(1)')->text());				
				if(count($timestapmps) == 0)
				{
					continue;	
				}
				$this->courses[$key]["dzien"] = trim($timestapmps[1]);
				$this->courses[$key]["tydzien"] = trim($timestapmps[3]);
				$this->courses[$key]["start"] = trim($timestapmps[4]) . ":" . trim($timestapmps[5]);
				$this->courses[$key]["koniec"] = trim($timestapmps[6]) . ":" . trim($timestapmps[7]);
				$this->courses[$key]["budynek"] = trim($timestapmps[9]);
				$this->courses[$key]["sala"] = trim($timestapmps[11]);
				$this->courses[$key]["semestr"] = trim($this->semestr);
				
			}
		}
		
	}
	public function getCourses(){
		return $this->courses;
	}
	public function getPlan(){
		$this->logIn();
		$this->goToPathFromMenu('Zapisy');
		$this->wybierzSemestr();
		$this->idzDoPlanu();
		$this->getDataFromSchedule();
		return $this->getCourses();
	}
}

?>