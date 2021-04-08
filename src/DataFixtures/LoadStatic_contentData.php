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

            'assistants-header' => '<h1>Assistenter</h1><p>Vektorassistent er et frivillig verv der du reiser til en ungdomsskole &eacute;n dag i uka for &aring; hjelpe til som&nbsp;<strong>l&aelig;rerassistent i matematikk</strong>. En stilling som vektorassistent varer i 4 eller 8 uker, og du kan selv velge hvilken ukedag som passer best for deg.</p>',
            'assistants-role-model' => '<h4>V&aelig;r et forbilde</h4><p>Som vektorassistent er du med p&aring; &aring; gj&oslash;re matte g&oslash;y. Ditt engasjement kan bidra til &oslash;kt motivasjon og l&aelig;relyst. Bli med og gj&oslash;r en forskjell!</p>',
            'assistants-social' => '<h4>Sosiale arrangementer</h4><p>Alle assistenter blir invitert til arrangementer som fester, popul&aelig;rforedrag, bowling, grilling i parken, gokart og paintball.</p>',
            'assistants-cv' => '<h4>Fint &aring; ha p&aring; CVen</h4><p>Erfaring som arbeidsgivere setter pris p&aring;. Alle assistenter f&aring;r en attest.</p>',
            'assistants-teacher' => '<h2>L&aelig;rerassistent i matematikk</h2><p>Vektorprogrammet er en studentorganisasjon som sender realfagssterke studenter til grunnskolen for &aring; hjelpe elevene med matematikk i skoletiden. Vi ser etter deg som lengter etter en mulighet til &aring; l&aelig;re bort kunnskapene du sitter med og &oslash;nsker &aring; ta del i et sterkt sosialt fellesskap. Etter &aring; ha v&aelig;rt vektorassistent kommer du til &aring; sitte igjen med mange gode erfaringer og nye venner p&aring; tvers av trinn og linje.</p>',
            'assistants-teacher-2' => '<p>I tillegg vil du f&aring; muligheten til &aring; delta p&aring; mange sosiale arrangementer, alt fra fest og grilling til spillkveld. Samtidig arrangerer vi popul&aelig;rforedrag som er til for &aring; &oslash;ke motivasjonen din for videre studier. Vi har hatt bes&oslash;k av blant annet Andreas Wahl, Jo R&oslash;islien, Knut J&oslash;rgen R&oslash;ed &Oslash;degaard og James Grime.</p>',
            'assistants-tasks' => '<h2>Arbeidsoppgaver</h2><p>Som vektorassistent er du ute &eacute;n dag i uka, i 4 eller 8 uker, p&aring; en ungdomsskole i n&aelig;romr&aring;det. Vi tilpasser timeplanen slik at du selv kan bestemme hvilken dag som passer best. Vektorassistenter blir sendt ut i par, slik at du alltid kan ha noen &aring; st&oslash;tte deg p&aring;. Oppgavene dine vil variere fra &aring; g&aring; rundt i klasserommet og hjelpe elever med oppgaver, til &aring; gjennomg&aring; utvalgte temaer i mindre grupper. Det er l&aelig;reren som bestemmer hva som skal bli gjennomg&aring;tt. Dette arbeidet blir satt stor pris p&aring; av b&aring;de barn og l&aelig;rere!</p>',
            'assistants-admission-requirements' => '<h5>Opptakskrav</h5><ul><li>Du studerer p&aring; h&oslash;gskole/universitet</li><li>Du har hatt R1/S2 p&aring; videreg&aring;ende</li><li>Du har tid til &aring; dra til en ungdomsskole&nbsp;<br />&eacute;n dag i uka (kl. 8-14)</li></ul>',
            'assistants-admission-process' => '<h5>Opptaksprosessen</h5><ol><li>Vektorprogrammet tar opp nye assistenter i starten av hvert semester</li><li>Send inn s&oslash;knad fra skjemaet lengre ned p&aring; denne siden</li><li>M&oslash;t opp p&aring; intervju slik at vi kan bli bedre kjent med deg</li><li>Dra p&aring; et gratis pedagogikkurs arrangert av Vektorprogrammet</li><li>F&aring; tildelt en ungdomsskole som du og din vektorpartner skal dra til</li></ol>',

            'teams-header' => '<h1>Styre og team</h1><p>Vektorprogrammet er en stor organisasjon med assistenter i 4 norske byer. Vi trenger derfor mange frivillige bak kulissene som kan f&aring; hjulene til &aring; g&aring; rundt. Uten vektorprogrammets 13 team hadde dette aldri g&aring;tt an!</p><p>Kunne du tenkt deg et team-verv hos oss?&nbsp;<br /><strong><strong>Les mer om de ulike teamene nedenfor!</strong></strong></p>',

            'teacher-header' => '<h1>Vektorassistenter i skolen</h1><p>Vektorprogrammet er en frivillig organisasjon som tilbyr ungdomsskoler i Norge hjelpel&aelig;rere i matematikktimene. Her kan du enkelt s&oslash;ke om &aring; f&aring; studenter til &aring; hjelpe og motivere elevene i dine timer.</p>',
            'teacher-assistants-in-class' => '<h2>Vektorassistenter i matteundervisning</h2><p>Vektorprogrammet er Norges st&oslash;rste organisasjon som arbeider for &aring; &oslash;ke interessen for matematikk og realfag blant elever i grunnskolen. Vi er en studentorganisasjon som sender ut dyktige og motiverte studenter til ungdomsskoler i norge.</p><p>Studentene fungerer som l&aelig;rerens assistenter og kan dermed bidra til at elevene raskere f&aring;r hjelp i timen, og at undervisningen blir mer tilpasset de ulike elevgruppene. Vi har erfart at l&aelig;rerne ofte har mye &aring; gj&oslash;re i timene, og ofte ikke rekker &aring; hjelpe alle elevene som st&aring;r fast. Derfor er vi overbevist om at to assistenter kan forhindre mye hodebry for b&aring;de l&aelig;rere og elever.&nbsp;<br />Hvert &aring;r gjennomf&oslash;rer vi evalueringsunders&oslash;kelser, og i gjennomsnitt sier over 95% av l&aelig;rerne at de er forn&oslash;yde med prosjektet og &oslash;nsker &aring; delta videre.</p><p>Alle assistentene har v&aelig;rt gjennom en intervjuprossess som gj&oslash;r oss sikre p&aring; at de vil passe som assistentl&aelig;rere og kan v&aelig;re gode forbilder for elevene. Dette er en unik mulighet til &aring; f&aring; inn rollemodeller i klasserommet som kan v&aelig;re med &aring; gi elevene mer motivasjon.</p>',
            'teacher-how-to-use' => '<h2>Enkelt &aring; bruke assistenter i undervisningen</h2><p>Assistentene kan brukes som hjelp i undervisningen. Her er noen forslag vi har gode erfaringer med:</p><ul><li>Hjelpe til med oppgavel&oslash;sing i klasserom</li><li>Utfordre de sterkeste elevene med vanskeligere oppgaver</li><li>Engasjere elever med matteleker, g&aring;ter og spill</li><li>Arbeid med elever p&aring; grupperom</li></ul>',

            'vektor_i_media' => '<p>Vektorprogrammet i media:</p><p>VG: Studenter hjelper elever med matte</p><p>NRK: Studenter hjelper elever med matte</p><p>DAGBLADET: Studenter hjelper elever med matte</p><p>AFTENPOSTEN: Studenter hjelper elever med matte</p>',
            'contact-header' => '<h1>Organisasjonen</h1><p>Vektorprogrammet er en stor organisasjon med assistenter i 4 norske byer. Under kan du kontakte vektorprogrammet i n&aelig;rmeste by eller hovedstyret for generelle henvendelser.&nbsp;</p>',


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
