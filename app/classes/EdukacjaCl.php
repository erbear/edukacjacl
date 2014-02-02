<?php
//require('phpQuery.php');

class EdukacjaCl 
{



	private $ch;
	private $HTML;
	private $URL= [
		'default'=> "https://edukacja.pwr.wroc.pl",
		'home' => "https://edukacja.pwr.wroc.pl/EdukacjaWeb/studia.do",
		'logIn' => "https://edukacja.pwr.wroc.pl/EdukacjaWeb/logInUser.do",
		'zapisy' => "https://edukacja.pwr.wroc.pl/EdukacjaWeb/zapisy.do?href=#hrefZapisySzczSlu",
		'przegladanieGrup'=>"https://edukacja.pwr.wroc.pl/EdukacjaWeb/zapisy.do",
		'przegladanieZWektora'=>"https://edukacja.pwr.wroc.pl/EdukacjaWeb/zapisy.do?event=ZapiszFiltr&event=wyborKryterium&href=#hrefKryteriumFiltrowania",
		'kolejnaStrona'=>"https://edukacja.pwr.wroc.pl/EdukacjaWeb/zapisy.do?href="
	];
	protected $UrlEvents = [
		''
	];

	private $dataToSend;
	private $courses;
	private $dates;
	private $kursyDoZapisow;
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
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
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
	protected function clearData(){
		$this->dataToSend = [];
	}
	//buduje URL z danych
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

	//pobiera tokeny
	private function getTokens(){
		$doc = phpQuery::newDocumentHtml($this->HTML);
		$this->dataToSend['clEduWebSESSIONTOKEN'] = pq('input[name="clEduWebSESSIONTOKEN"]')->attr('value');
		$this->dataToSend['cl.edu.web.TOKEN'] = pq('input[name="cl.edu.web.TOKEN"]')->attr('value');
	}
	//pobiera URL z pozycji z menu
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
				preg_match('/(pn|wt|śr|cz|pt)(\/(TP|TN))?\s+(\d{2}):(\d{2})-(\d{2}):(\d{2}),?\s(bud.)?\s([A-Z]+-\d+),?\s(sala)?\s([^\s]+)/i' , $course, $timestapmps);
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
	public function getDataFromWektor(){
		$doc = phpQuery::newDocumentHtml($this->HTML);

		preg_match_all('/>\s*[a-zA-Z]{1}\d{2}-\d{1,2}[a-zA-Z]{1}/i', $this->HTML, $matches);

		foreach ($matches as $m){
			foreach ($m as $key=>$match){
				$pos1 = strrpos($this->HTML, $match);
				$pos2 = (isset($m[$key+1])?$m[$key+1]:0) ? strrpos($this->HTML, isset($m[$key+1])?$m[$key+1]:0) : strlen($this->HTML);
				$course = substr($this->HTML, $pos1, ($pos2-$pos1));
				$course = '<tr><td'. $course;
				$doc = phpQuery::newDocumentHtml($course);
				preg_match('/(pn|wt|śr|cz|pt)(\/(TP|TN))?\s+(\d{2}):(\d{2})-(\d{2}):(\d{2}),?\s(bud.)?\s([A-Z]+-\d+),?\s(sala)?\s([^\s]+)/i', $course, $timestapmps);
				if(count($timestapmps) == 0)
				{
					continue;	
				}
				$wiersz1 = pq('td:contains(' . trim(str_replace('>', ' ', $match)) .')')->parent('tr');
				$miejsca = preg_split('/\//', trim($wiersz1['td:eq(3)']->text()));
				$this->dates[] = ["nazwa" => trim($wiersz1['td:eq(2)']->text()),
									"prowadzacy" => trim($wiersz1->nextAll('tr')->eq(0)->find('td:eq(0)')->text()),
									"rodzaj" => trim($wiersz1->nextAll('tr')->eq(0)->find('td:eq(1)')->text()),		
									"kod" => trim(str_replace('>', ' ', $match)),
									"zajete" => $miejsca[0],
									"wszystkie" => $miejsca[1],
									"dzien" => trim($timestapmps[1]),
									"tydzien" => trim($timestapmps[3]),
									"start" => trim($timestapmps[4]) . ":" . trim($timestapmps[5]),
									"koniec" => trim($timestapmps[6]) . ":" . trim($timestapmps[7]),
									"budynek" => trim($timestapmps[9]),
									"sala" => trim($timestapmps[11]),
									"semestr" => trim($this->semestr)];
			}
		}
		$doc = phpQuery::newDocumentHtml($this->HTML);
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
	public function idzDoPrzegladaniaKursow(){
		$this->logIn();
		$this->goToPathFromMenu('Zapisy');
		$this->wybierzSemestr();

		//pobieram dane zeby dostac sie do przegladania grup

		$this->getTokens();
		$doc = phpQuery::newDocumentHtml($this->HTML);
		$input1 = pq('input[name="event_ZapisyPrzegladanieGrup"]')->parent('td')->find('input:eq(0)');
		$input2 = pq('input[name="event_ZapisyPrzegladanieGrup"]')->parent('td')->find('input:eq(1)');
		$this->dataToSend[$input1->attr('name')] = $input1->attr('value');
		$this->dataToSend[$input2->attr('name')] = $input2->attr('value');
		$this->dataToSend['event'] = 'ZapisyPrzegladanieGrup';
		$this->setPOST(true);
		curl_setopt($this->ch, CURLOPT_URL, $this->URL['przegladanieGrup']);
		$this->loadPage();

		//pobieram dane zeby dostac sie do przegladania grup z wektora

		$this->getTokens();
		$this->dataToSend['KryteriumFiltrowania'] = 'Z_WEKTORA_ZAP';
		$this->setPOST(true);
		curl_setopt($this->ch, CURLOPT_URL, $this->URL['przegladanieZWektora']);
		$this->loadPage();

		//przemieszczam sie pomiedzy kursami

		$this->setPOST(false);
		$this->getTokens();
		$doc = phpQuery::newDocumentHtml($this->HTML);
		$odnosniki = pq("a[title='Wybierz wiersz']");
		$nazwaKursu = [];
		
		//pobieranie adresow do wszystkich kursow

		$pagings = pq(".paging-numeric-span")->eq(0)->find('input');//ilosc odnosnikow do zminy strony
		$odnosnik = [];//adresy stron z danym kursem
		$pagingRange = -10;
		$numerKursu = 0;
		foreach ($pagings as $key=>$paging){//foreach po stronach z grupami kursow
			$hiddenInputs = pq(".paging-panel")->eq(0)->find('input:hidden');//wszystkie inputy ktore sa potrzebne do wyslania zapytania post
			//pobieranie inputow do zmiennej z danymi
			foreach($hiddenInputs as $hiddenInput){
				$this->dataToSend[pq($hiddenInput)->attr('name')] = pq($hiddenInput)->attr('value');
			}
			$this->getTokens();
			$this->dataToSend['pagingRangeStart'] = strval($pagingRange+=10); //ustawienie do ktorej strony ma byc przekierowanie
			//wyslanie zapytania do danej strony
			$this->setPOST(true);
			curl_setopt($this->ch, CURLOPT_URL, $this->URL['kolejnaStrona']);
			$this->loadPage();
			$doc = phpQuery::newDocumentHtml($this->HTML);


			$odnosniki = pq("a[title='Wybierz wiersz']");
			foreach ($odnosniki as $numer=>$o){
				$odnosnik[$numer] = ['kod'=>trim(pq($o)->text()),
								 'href'=> pq($o)->attr('href'),
								 'nazwa'=> trim(pq($o)->parent('td')->parent('tr')->find('td:eq(1)')->text())];
			}
			foreach($odnosnik as $o){
				$this->getTokens();
				$this->setPOST(false);
				curl_setopt($this->ch, CURLOPT_URL, $this->URL['default'] . $o['href']);
				$this->loadPage();
				$numerStrony = 0;
				$this->getDataFromWektor();
				foreach (pq(".paging-numeric-span")->eq(1)->find('input:not(input:first-child))') as $input){
					$hiddenInputs = pq(".paging-panel")->eq(1)->find('input:hidden');
					foreach($hiddenInputs as $hiddenInput){
						$this->dataToSend[pq($hiddenInput)->attr('name')] = pq($hiddenInput)->attr('value');
					}
					$this->getTokens();
					$this->dataToSend['pagingRangeStart'] = strval($numerStrony+=10);
					$this->setPOST(true);
					curl_setopt($this->ch, CURLOPT_URL, $this->URL['kolejnaStrona']);
					$this->loadPage();
					$this->getDataFromWektor();
				}
				$this->kursyDoZapisow[] = ['nazwa' => $o['nazwa'], 'kod'=>$o['kod'], 'dane'=>$this->dates];
				$this->dates = '';
				$numerKursu++;
			}
			$odnosnik = '';
		}


		
		return $this->kursyDoZapisow;
		
	}
	public function getOpisStudiow(){
		$this->logIn();
		$this->goToPathFromMenu('Akademiki');
		$doc = phpQuery::newDocumentHtml($this->HTML);
		return trim(pq('.CENTER_TRESC')->find('table')->find('tr:eq(1)')->find('td:eq(1)')->html());
	}
}
?>