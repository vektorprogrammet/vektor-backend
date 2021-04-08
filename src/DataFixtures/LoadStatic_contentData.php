<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\StaticContent;

class LoadStatic_contentData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $elements = array(
            'home-tagline' => '<p>- sender studenter til ungdomsskoler for &aring; hjelpe til som l&aelig;rerens assistent i matematikkundervisningen</p>',


            'teams-header' => '<h1>Styre og team</h1><p>Vektorprogrammet er en stor organisasjon med assistenter i 4 norske byer. Vi trenger derfor mange frivillige bak kulissene som kan f&aring; hjulene til &aring; g&aring; rundt. Uten vektorprogrammets 13 team hadde dette aldri g&aring;tt an!</p><p>Kunne du tenkt deg et team-verv hos oss?&nbsp;<br /><strong><strong>Les mer om de ulike teamene nedenfor!</strong></strong></p>',

            'teacher-header' => '<h1>Vektorassistenter i skolen</h1><p>Vektorprogrammet er en frivillig organisasjon som tilbyr ungdomsskoler i Norge hjelpel&aelig;rere i matematikktimene. Her kan du enkelt s&oslash;ke om &aring; f&aring; studenter til &aring; hjelpe og motivere elevene i dine timer.</p>',
            'teacher-assistants-in-class' => '<h2>Vektorassistenter i matteundervisning</h2><p>Vektorprogrammet er Norges st&oslash;rste organisasjon som arbeider for &aring; &oslash;ke interessen for matematikk og realfag blant elever i grunnskolen. Vi er en studentorganisasjon som sender ut dyktige og motiverte studenter til ungdomsskoler i norge.</p><p>Studentene fungerer som l&aelig;rerens assistenter og kan dermed bidra til at elevene raskere f&aring;r hjelp i timen, og at undervisningen blir mer tilpasset de ulike elevgruppene. Vi har erfart at l&aelig;rerne ofte har mye &aring; gj&oslash;re i timene, og ofte ikke rekker &aring; hjelpe alle elevene som st&aring;r fast. Derfor er vi overbevist om at to assistenter kan forhindre mye hodebry for b&aring;de l&aelig;rere og elever.&nbsp;<br />Hvert &aring;r gjennomf&oslash;rer vi evalueringsunders&oslash;kelser, og i gjennomsnitt sier over 95% av l&aelig;rerne at de er forn&oslash;yde med prosjektet og &oslash;nsker &aring; delta videre.</p><p>Alle assistentene har v&aelig;rt gjennom en intervjuprossess som gj&oslash;r oss sikre p&aring; at de vil passe som assistentl&aelig;rere og kan v&aelig;re gode forbilder for elevene. Dette er en unik mulighet til &aring; f&aring; inn rollemodeller i klasserommet som kan v&aelig;re med &aring; gi elevene mer motivasjon.</p>',
            'teacher-how-to-use' => '<h2>Enkelt &aring; bruke assistenter i undervisningen</h2><p>Assistentene kan brukes som hjelp i undervisningen. Her er noen forslag vi har gode erfaringer med:</p><ul><li>Hjelpe til med oppgavel&oslash;sing i klasserom</li><li>Utfordre de sterkeste elevene med vanskeligere oppgaver</li><li>Engasjere elever med matteleker, g&aring;ter og spill</li><li>Arbeid med elever p&aring; grupperom</li></ul>',

            'vektor_i_media' => '<p>Vektorprogrammet i media:</p><p>VG: Studenter hjelper elever med matte</p><p>NRK: Studenter hjelper elever med matte</p><p>DAGBLADET: Studenter hjelper elever med matte</p><p>AFTENPOSTEN: Studenter hjelper elever med matte</p>',


        );
        foreach ($elements as $html_id => $content) {
            $staticElement = new StaticContent();
            $staticElement->setHtmlId($html_id);
            $staticElement->setHtml($content);

            $manager->persist($staticElement);
        }
        $manager->flush();
    }
}
