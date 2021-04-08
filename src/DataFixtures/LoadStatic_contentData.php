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
