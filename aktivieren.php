<?php
	session_start ();
	include 'functions.php';
	$dbconn = sqlconnect();
	
	$hash = $_REQUEST['hash'];
	
?>
<!DOCTYPE HTML>
<!--
	Snapshot by TEMPLATED
	templated.co @templatedco
	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
-->
<html>
	<head>
		<title>tipppfehler.de</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="assets/css/maincss2.css" />
	</head>
	<body>
		<div class="page-wrap">	
			<div class="wrsec">
			<!-- Nav -->
				<section id="header">
						<div class="logo"><div style="margin-left: 65px;"><?php echo showtirsetting($dbconn, $tir_index, "tir_name"); ?></div></div>

			<!-- Nav -->

			<!-- Main -->
			</section>
				<section id="main">

					<!-- Banner -->
								<h1>tipppfehler.de</h1>
								<?php
									$array = mysqli_query ($dbconn, "select usr_index from usr_user where usr_hash='".$hash."'");
									if (mysqli_num_rows ( $array ) == 0)
									{
										echo 'Es ist ein Fehler aufgetreten. Bitte wende dich an mich: '.showsetting($dbconn, "email"); 
									}
									while ($row = mysqli_fetch_array($array))
									{
										mysqli_query ($dbconn, "update usr_user set usr_locked=0 where usr_index='".$row["usr_index"]."'");
										echo '<a href="index.php">Benutzerkonto erfolgreich aktiviert.<br> Hier geht es weiter zum login</a>...';
									}
								?>	
						</section>
							</div>
						</section>
						</section></div><center><br>
			<a onclick="togglediv(1)" >Impressum</a><div id="tdiv1" style="display:none;"><?php echo showsetting($dbconn, "impressum"); ?></div>&nbsp;&nbsp;&nbsp;&nbsp;
			<a onclick="togglediv(2)" >Datenschutz</a><div id="tdiv2" style="display:none;">
			<h1>Datenschutzerklärung</h1><br><br>
								
								<strong>
									Die ist eine private Internetseite.<br>
									Die Nutzung ist nur für Leute im Bekanntenkreis vorgesehen ! Andere Personen die sich dennoch registrieren werden gelöscht.
								</strong><br><br>
								
								1.1 Name und Anschrift des für die Verarbeitung Verantwortlichen<br />
								[Name des Betreibers]<br>
								[Strasse Hausnummer]<br>
								[PLZ Ort]<br>
								[Telefon]<br>
								[E-Mail]<br><br>
								1.2 Erfassung von allgemeinen Daten und Informationen<br />
								Diese Internetseite erfasst mit jedem Aufruf der Internetseite durch eine betroffene Person oder ein automatisiertes System eine Reihe von allgemeinen Daten und Informationen. Diese allgemeinen Daten und Informationen werden in den Logfiles des Servers gespeichert. Erfasst werden können die (1) verwendeten Browsertypen und Versionen, (2) das vom zugreifenden System verwendete Betriebssystem, (3) die Internetseite, von welcher ein zugreifendes System auf diese Internetseite gelangt (sogenannte Referrer), (4) die Unterwebseiten, welche über ein zugreifendes System auf dieser Internetseite angesteuert werden, (5) das Datum und die Uhrzeit eines Zugriffs auf die Internetseite, (6) eine Internet-Protokoll-Adresse (IP-Adresse), (7) der Internet-Service-Provider des zugreifenden Systems und (8) sonstige ähnliche Daten und Informationen, die der Gefahrenabwehr im Falle von Angriffen auf das informationstechnologischen Systeme dienen.<br>
								Bei der Nutzung dieser allgemeinen Daten und Informationen können keine Rückschlüsse auf die betroffene Person gezogen werden. Diese Informationen werden vielmehr benötigt, um (1) die Inhalte dieser Internetseite korrekt auszuliefern, (2) die Inhalte dieser Internetseite sowie die Werbung für diese zu optimieren, (3) die dauerhafte Funktionsfähigkeit der informationstechnologischen Systeme und der Technik dieser Internetseite zu gewährleisten sowie (4) um Strafverfolgungsbehörden im Falle eines Cyberangriffes die zur Strafverfolgung notwendigen Informationen bereitzustellen. Diese anonym erhobenen Daten und Informationen werden daher einerseits statistisch und ferner mit dem Ziel ausgewertet, den Datenschutz und die Datensicherheit zu erhöhen, um letztlich ein optimales Schutzniveau für die von uns verarbeiteten personenbezogenen Daten sicherzustellen. Die anonymen Daten der Server-Logfiles werden getrennt von allen durch eine betroffene Person angegebenen personenbezogenen Daten gespeichert.<br>
								1.3 Cookies<br />
								Diese Internetseite verwendet Cookies. Cookies sind Textdateien, welche über einen Internetbrowser auf einem Computersystem abgelegt und gespeichert werden. Zahlreiche Internetseiten und Server verwenden Cookies. Viele Cookies enthalten eine sogenannte Cookie-ID. Eine Cookie-ID ist eine eindeutige Kennung des Cookies. Sie besteht aus einer Zeichenfolge, durch welche Internetseiten und Server dem konkreten Internetbrowser zugeordnet werden können, in dem das Cookie gespeichert wurde. Dies ermöglicht es den besuchten Internetseiten und Servern, den individuellen Browser der betroffenen Person von anderen Internetbrowsern, die andere Cookies enthalten, zu unterscheiden. Ein bestimmter Internetbrowser kann über die eindeutige Cookie-ID wiedererkannt und identifiziert werden.
								Durch den Einsatz von Cookies ist das bereitstellen von nutzerfreundlichere Servicesmöglich, die ohne die Cookie-Setzung nicht möglich wären. Mittels eines Cookies können die Informationen und Angebote auf dieser Internetseite im Sinne des Benutzers optimiert werden. Cookies ermöglichen uns, wie bereits erwähnt, die Benutzer dieser Internetseite wiederzuerkennen. Zweck dieser Wiedererkennung ist es, den Nutzern die Verwendung dieser Internetseite zu erleichtern. Der Benutzer einer Internetseite, die Cookies verwendet, muss beispielsweise nicht bei jedem Besuch der Internetseite erneut seine Zugangsdaten eingeben, weil dies von der Internetseite und dem auf dem Computersystem des Benutzers abgelegten Cookie übernommen wird. Ein weiteres Beispiel ist das Cookie eines Warenkorbes im Online-Shop. Der Online-Shop merkt sich die Artikel, die ein Kunde in den virtuellen Warenkorb gelegt hat, über ein Cookie.<br>
								Die betroffene Person kann die Setzung von Cookies durch diese Internetseite jederzeit mittels einer entsprechenden Einstellung des genutzten Internetbrowsers verhindern und damit der Setzung von Cookies dauerhaft widersprechen. Ferner können bereits gesetzte Cookies jederzeit über einen Internetbrowser oder andere Softwareprogramme gelöscht werden. Dies ist in allen gängigen Internetbrowsern möglich. Deaktiviert die betroffene Person die Setzung von Cookies in dem genutzten Internetbrowser, sind unter Umständen nicht alle Funktionen dieser Internetseite vollumfänglich nutzbar.<br>
								1.4 Personenbezogene Daten<br />
								Der Besucher dieser Webseite hat die Möglichkeit, sich unter Angabe von personenbezogenen Daten zu registrieren.
								Welche personenbezogenen Daten dabei an den für die Verarbeitung Verantwortlichen übermittelt werden, ergibt sich aus der jeweiligen Eingabemaske, die für die Registrierung verwendet wird. 
								Die von der betroffenen Person eingegebenen personenbezogenen Daten werden ausschließlich für die interne Verwendung bei dem für die Verarbeitung Verantwortlichen und für eigene Zwecke erhoben 
								und gespeichert. Die Registrierung der betroffenen Person unter freiwilliger Angabe personenbezogener Daten dient dem für die Verarbeitung Verantwortlichen dazu, der betroffenen Person Inhalte oder Leistungen anzubieten, die aufgrund der Natur der Sache nur registrierten Benutzern angeboten werden können. Registrierten Personen steht die Möglichkeit frei, die bei der Registrierung angegebenen personenbezogenen Daten jederzeit abzuändern oder vollständig aus dem Datenbestand des für die Verarbeitung Verantwortlichen löschen zu lassen.
								Der für die Verarbeitung Verantwortliche erteilt jeder betroffenen Person jederzeit auf Anfrage Auskunft darüber, welche personenbezogenen Daten über die betroffene Person gespeichert sind. Ferner berichtigt oder löscht der für die Verarbeitung Verantwortliche personenbezogene Daten auf Wunsch oder Hinweis der betroffenen Person, soweit dem keine gesetzlichen Aufbewahrungspflichten entgegenstehen.<br>
								1.5 Routinemäßige Löschung und Sperrung von personenbezogenen Daten<br />
								Der für die Verarbeitung Verantwortliche verarbeitet und speichert personenbezogene Daten der betroffenen Person nur für den Zeitraum, der zur Erreichung des Speicherungszwecks erforderlich ist oder sofern dies durch den Europäischen Richtlinien- und Verordnungsgeber oder einen anderen Gesetzgeber in Gesetzen oder Vorschriften, welchen der für die Verarbeitung Verantwortliche unterliegt, vorgesehen wurde.
								Entfällt der Speicherungszweck oder läuft eine vom Europäischen Richtlinien- und Verordnungsgeber oder einem anderen zuständigen Gesetzgeber vorgeschriebene Speicherfrist ab, werden die personenbezogenen Daten routinemäßig und entsprechend den gesetzlichen Vorschriften gesperrt oder gelöscht.<br />
								1.6 Rechte der betroffenen Person<br />
								a) Recht auf Bestätigung<br />
								Jede betroffene Person hat das vom Europäischen Richtlinien- und Verordnungsgeber eingeräumte Recht, von dem für die Verarbeitung Verantwortlichen eine Bestätigung darüber zu verlangen, ob sie betreffende personenbezogene Daten verarbeitet werden. Möchte eine betroffene Person dieses Bestätigungsrecht in Anspruch nehmen, kann sie sich hierzu jederzeit an den für die Verarbeitung Verantworlichen wenden.
								<br />
								b) Recht auf Auskunft<br />
								Jede von der Verarbeitung personenbezogener Daten betroffene Person hat das vom Europäischen Richtlinien- und Verordnungsgeber gewährte Recht, jederzeit von dem für die Verarbeitung Verantwortlichen unentgeltliche Auskunft über die zu seiner Person gespeicherten personenbezogenen Daten und eine Kopie dieser Auskunft zu erhalten. Ferner hat der Europäische Richtlinien- und Verordnungsgeber der betroffenen Person Auskunft über folgende Informationen zugestanden:
								die Verarbeitungszwecke,
								die Kategorien personenbezogener Daten, die verarbeitet werden,
								die Empfänger oder Kategorien von Empfängern, gegenüber denen die personenbezogenen Daten offengelegt worden sind oder noch offengelegt werden, insbesondere bei Empfängern in Drittländern oder bei internationalen Organisationen,
								falls möglich die geplante Dauer, für die die personenbezogenen Daten gespeichert werden, oder, falls dies nicht möglich ist, die Kriterien für die Festlegung dieser Dauer,
								das Bestehen eines Rechts auf Berichtigung oder Löschung der sie betreffenden personenbezogenen Daten oder auf Einschränkung der Verarbeitung durch den Verantwortlichen oder eines Widerspruchsrechts gegen diese Verarbeitung,
								das Bestehen eines Beschwerderechts bei einer Aufsichtsbehörde
								wenn die personenbezogenen Daten nicht bei der betroffenen Person erhoben werden: Alle verfügbaren Informationen über die Herkunft der Daten,
								das Bestehen einer automatisierten Entscheidungsfindung einschließlich Profiling gemäß Artikel 22 Abs.1 und 4 DS-GVO und — zumindest in diesen Fällen — aussagekräftige Informationen über die involvierte Logik sowie die Tragweite und die angestrebten Auswirkungen einer derartigen Verarbeitung für die betroffene Person
								Ferner steht der betroffenen Person ein Auskunftsrecht darüber zu, ob personenbezogene Daten an ein Drittland oder an eine internationale Organisation übermittelt wurden. Sofern dies der Fall ist, so steht der betroffenen Person im Übrigen das Recht zu, Auskunft über die geeigneten Garantien im Zusammenhang mit der Übermittlung zu erhalten.
								Möchte eine betroffene Person dieses Auskunftsrecht in Anspruch nehmen, kann sie sich hierzu jederzeit an den für die Verarbeitung Verantwortlichen wenden.<br />
								c) Recht auf Berichtigung<br />
								Jede von der Verarbeitung personenbezogener Daten betroffene Person hat das vom Europäischen Richtlinien- und Verordnungsgeber gewährte Recht, die unverzügliche Berichtigung sie betreffender unrichtiger personenbezogener Daten zu verlangen. Ferner steht der betroffenen Person das Recht zu, unter Berücksichtigung der Zwecke der Verarbeitung, die Vervollständigung unvollständiger personenbezogener Daten — auch mittels einer ergänzenden Erklärung — zu verlangen.
								Möchte eine betroffene Person dieses Berichtigungsrecht in Anspruch nehmen, kann sie sich hierzu jederzeit an den für die Verarbeitung Verantwortlichen wenden.
								<br />
								d) Recht auf Löschung (Recht auf Vergessen werden)<br />
								Jede von der Verarbeitung personenbezogener Daten betroffene Person hat das vom Europäischen Richtlinien- und Verordnungsgeber gewährte Recht, von dem Verantwortlichen zu verlangen, dass die sie betreffenden personenbezogenen Daten unverzüglich gelöscht werden, sofern einer der folgenden Gründe zutrifft und soweit die Verarbeitung nicht erforderlich ist:<br />
								Die personenbezogenen Daten wurden für solche Zwecke erhoben oder auf sonstige Weise verarbeitet, für welche sie nicht mehr notwendig sind.<br />
								Die betroffene Person widerruft ihre Einwilligung, auf die sich die Verarbeitung gemäß Art. 6 Abs. 1 Buchstabe a DS-GVO oder Art. 9 Abs. 2 Buchstabe a DS-GVO stützte, und es fehlt an einer anderweitigen Rechtsgrundlage für die Verarbeitung.<br />
								Die betroffene Person legt gemäß Art. 21 Abs. 1 DS-GVO Widerspruch gegen die Verarbeitung ein, und es liegen keine vorrangigen berechtigten Gründe für die Verarbeitung vor, oder die betroffene Person legt gemäß Art. 21 Abs. 2 DS-GVO Widerspruch gegen die Verarbeitung ein.<br />
								Die personenbezogenen Daten wurden unrechtmäßig verarbeitet.<br />
								Die Löschung der personenbezogenen Daten ist zur Erfüllung einer rechtlichen Verpflichtung nach dem Unionsrecht oder dem Recht der Mitgliedstaaten erforderlich, dem der Verantwortliche unterliegt.<br />
								Die personenbezogenen Daten wurden in Bezug auf angebotene Dienste der Informationsgesellschaft gemäß Art. 8 Abs. 1 DS-GVO erhoben.<br />
								Sofern einer der oben genannten Gründe zutrifft und eine betroffene Person die Löschung von personenbezogenen Daten veranlassen möchte, kann sie sich hierzu jederzeit an den Datenschutzbeauftragten wenden. Der Datenschutzbeauftragte wird veranlassen, dass dem Löschverlangen unverzüglich nachgekommen wird.<br />
								Wurden die personenbezogenen Daten von die Seite öffentlich gemacht und ist der Verantwortliche gemäß Art. 17 Abs. 1 DS-GVO zur Löschung der personenbezogenen Daten verpflichtet, so trifft der Verantwortliche unter Berücksichtigung der verfügbaren Technologie und der Implementierungskosten angemessene Maßnahmen, auch technischer Art, um andere für die Datenverarbeitung Verantwortliche, welche die veröffentlichten personenbezogenen Daten verarbeiten, darüber in Kenntnis zu setzen, dass die betroffene Person von diesen anderen für die Datenverarbeitung Verantwortlichen die Löschung sämtlicher Links zu diesen personenbezogenen Daten oder von Kopien oder Replikationen dieser personenbezogenen Daten verlangt hat, soweit die Verarbeitung nicht erforderlich ist. Der Datenschutzbeauftragte wird im Einzelfall das Notwendige veranlassen.<br />
								e) Recht auf Einschränkung der Verarbeitung<br />
								Jede von der Verarbeitung personenbezogener Daten betroffene Person hat das vom Europäischen Richtlinien- und Verordnungsgeber gewährte Recht, von dem Verantwortlichen die Einschränkung der Verarbeitung zu verlangen, wenn eine der folgenden Voraussetzungen gegeben ist:
								Die Richtigkeit der personenbezogenen Daten wird von der betroffenen Person bestritten, und zwar für eine Dauer, die es dem Verantwortlichen ermöglicht, die Richtigkeit der personenbezogenen Daten zu überprüfen.
								Die Verarbeitung ist unrechtmäßig, die betroffene Person lehnt die Löschung der personenbezogenen Daten ab und verlangt stattdessen die Einschränkung der Nutzung der personenbezogenen Daten.
								Der Verantwortliche benötigt die personenbezogenen Daten für die Zwecke der Verarbeitung nicht länger, die betroffene Person benötigt sie jedoch zur Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen.
								Die betroffene Person hat Widerspruch gegen die Verarbeitung gem. Art. 21 Abs. 1 DS-GVO eingelegt und es steht noch nicht fest, ob die berechtigten Gründe des Verantwortlichen gegenüber denen der betroffenen Person überwiegen.
								Sofern eine der oben genannten Voraussetzungen gegeben ist und eine betroffene Person die Einschränkung von personenbezogenen Daten verlangen möchte, kann sie sich hierzu jederzeit an den Datenschutzbeauftragten wenden. Der Datenschutzbeauftragte wird die Einschränkung der Verarbeitung veranlassen.<br />
								f) Recht auf Datenübertragbarkeit<br />
								Jede von der Verarbeitung personenbezogener Daten betroffene Person hat das vom Europäischen Richtlinien- und Verordnungsgeber gewährte Recht, die sie betreffenden personenbezogenen Daten, welche durch die betroffene Person einem Verantwortlichen bereitgestellt wurden, in einem strukturierten, gängigen und maschinenlesbaren Format zu erhalten. Sie hat außerdem das Recht, diese Daten einem anderen Verantwortlichen ohne Behinderung durch den Verantwortlichen, dem die personenbezogenen Daten bereitgestellt wurden, zu übermitteln, sofern die Verarbeitung auf der Einwilligung gemäß Art. 6 Abs. 1 Buchstabe a DS-GVO oder Art. 9 Abs. 2 Buchstabe a DS-GVO oder auf einem Vertrag gemäß Art. 6 Abs. 1 Buchstabe b DS-GVO beruht und die Verarbeitung mithilfe automatisierter Verfahren erfolgt, sofern die Verarbeitung nicht für die Wahrnehmung einer Aufgabe erforderlich ist, die im öffentlichen Interesse liegt oder in Ausübung öffentlicher Gewalt erfolgt, welche dem Verantwortlichen übertragen wurde.
								Ferner hat die betroffene Person bei der Ausübung ihres Rechts auf Datenübertragbarkeit gemäß Art. 20 Abs. 1 DS-GVO das Recht, zu erwirken, dass die personenbezogenen Daten direkt von einem Verantwortlichen an einen anderen Verantwortlichen übermittelt werden, soweit dies technisch machbar ist und sofern hiervon nicht die Rechte und Freiheiten anderer Personen beeinträchtigt werden.
								Zur Geltendmachung des Rechts auf Datenübertragbarkeit kann sich die betroffene Person jederzeit an den Datenschutzbeauftragten wenden.<br />
								g) Recht auf Widerspruch<br />
								Jede von der Verarbeitung personenbezogener Daten betroffene Person hat das vom Europäischen Richtlinien- und Verordnungsgeber gewährte Recht, aus Gründen, die sich aus ihrer besonderen Situation ergeben, jederzeit gegen die Verarbeitung sie betreffender personenbezogener Daten, die aufgrund von Art. 6 Abs. 1 Buchstaben e oder f DS-GVO erfolgt, Widerspruch einzulegen. Dies gilt auch für ein auf diese Bestimmungen gestütztes Profiling.<br>
								h) Automatisierte Entscheidungen im Einzelfall einschließlich Profiling<br />
								Jede von der Verarbeitung personenbezogener Daten betroffene Person hat das vom Europäischen Richtlinien- und Verordnungsgeber gewährte Recht, nicht einer ausschließlich auf einer automatisierten Verarbeitung — einschließlich Profiling — beruhenden Entscheidung unterworfen zu werden, die ihr gegenüber rechtliche Wirkung entfaltet oder sie in ähnlicher Weise erheblich beeinträchtigt, sofern die Entscheidung (1) nicht für den Abschluss oder die Erfüllung eines Vertrags zwischen der betroffenen Person und dem Verantwortlichen erforderlich ist, oder (2) aufgrund von Rechtsvorschriften der Union oder der Mitgliedstaaten, denen der Verantwortliche unterliegt, zulässig ist und diese Rechtsvorschriften angemessene Maßnahmen zur Wahrung der Rechte und Freiheiten sowie der berechtigten Interessen der betroffenen Person enthalten oder (3) mit ausdrücklicher Einwilligung der betroffenen Person erfolgt.
								Ist die Entscheidung (1) für den Abschluss oder die Erfüllung eines Vertrags zwischen der betroffenen Person und dem Verantwortlichen erforderlich oder (2) erfolgt sie mit ausdrücklicher Einwilligung der betroffenen Person, werden angemessene Maßnahmen getroffen, um die Rechte und Freiheiten sowie die berechtigten Interessen der betroffenen Person zu wahren, wozu mindestens das Recht auf Erwirkung des Eingreifens einer Person seitens des Verantwortlichen, auf Darlegung des eigenen Standpunkts und auf Anfechtung der Entscheidung gehört.
								Möchte die betroffene Person Rechte mit Bezug auf automatisierte Entscheidungen geltend machen, kann sie sich hierzu jederzeit an den Datenschutzbeauftragten wenden.<br />
								i) Recht auf Widerruf einer datenschutzrechtlichen Einwilligung
								Jede von der Verarbeitung personenbezogener Daten betroffene Person hat das vom Europäischen Richtlinien- und Verordnungsgeber gewährte Recht, eine Einwilligung zur Verarbeitung personenbezogener Daten jederzeit zu widerrufen.
								Möchte die betroffene Person ihr Recht auf Widerruf einer Einwilligung geltend machen, kann sie sich hierzu jederzeit an den für die Verarbeitung Verantwortlichen wenden.<br />
								1.6 Rechtsgrundlage der Verarbeitung<br />
								Art. 6 I lit. a DS-GVO dient als Rechtsgrundlage für Verarbeitungsvorgänge, bei denen wir eine Einwilligung für einen bestimmten Verarbeitungszweck einholen. Ist die Verarbeitung personenbezogener Daten zur Erfüllung eines Vertrags, dessen Vertragspartei die betroffene Person ist, erforderlich, wie dies beispielsweise bei Verarbeitungsvorgängen der Fall ist, die für eine Lieferung von Waren oder die Erbringung einer sonstigen Leistung oder Gegenleistung notwendig sind, so beruht die Verarbeitung auf Art. 6 I lit. b DS-GVO. Gleiches gilt für solche Verarbeitungsvorgänge die zur Durchführung vorvertraglicher Maßnahmen erforderlich sind, etwa in Fällen von Anfragen zu Leistungen. Unterliegt diese Webseite einer rechtlichen Verpflichtung durch welche eine Verarbeitung von personenbezogenen Daten erforderlich wird, wie beispielsweise zur Erfüllung steuerlicher Pflichten, so basiert die Verarbeitung auf Art. 6 I lit. c DS-GVO. In seltenen Fällen könnte die Verarbeitung von personenbezogenen Daten erforderlich werden, um lebenswichtige Interessen der betroffenen Person oder einer anderen natürlichen Person zu schützen. Letztlich könnten Verarbeitungsvorgänge auf Art. 6 I lit. f DS-GVO beruhen. Auf dieser Rechtsgrundlage basieren Verarbeitungsvorgänge, die von keiner der vorgenannten Rechtsgrundlagen erfasst werden, wenn die Verarbeitung zur Wahrung eines berechtigten Interesses erforderlich ist, sofern die Interessen, Grundrechte und Grundfreiheiten des Betroffenen nicht überwiegen. Solche Verarbeitungsvorgänge sind uns insbesondere deshalb gestattet, weil sie durch den Europäischen Gesetzgeber besonders erwähnt wurden. Er vertrat insoweit die Auffassung, dass ein berechtigtes Interesse anzunehmen sein könnte, wenn die betroffene Person ein Kunde des Verantwortlichen ist (Erwägungsgrund 47 Satz 2 DS-GVO).<br />
								1.7 Dauer, für die die personenbezogenen Daten gespeichert werden<br />
								Das Kriterium für die Dauer der Speicherung von personenbezogenen Daten ist die jeweilige gesetzliche Aufbewahrungsfrist. Nach Ablauf der Frist werden die entsprechenden Daten routinemäßig gelöscht, sofern sie nicht mehr zur Vertragserfüllung oder Vertragsanbahnung erforderlich sind.
							
			</div>
</center></div>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.poptrox.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>
			<script src="assets/js/formular_pruefen.js"></script>
			<script>
				function togglediv(index) {
					var a = "tdiv"+index;
					var x = document.getElementById(a);
					if (x.style.display === "none") {
						x.style.display = "block";
					} else {
						x.style.display = "none";
					}
				}
			</script>

	</body>
</html>
