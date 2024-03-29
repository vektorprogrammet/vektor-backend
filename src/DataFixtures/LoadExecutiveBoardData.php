<?php

namespace App\DataFixtures;

use App\Entity\ExecutiveBoard;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LoadExecutiveBoardData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $board = new ExecutiveBoard();
        $board->setName('Hovedstyret');
        $board->setShortDescription('Dette her hovedstyret. Det er vi som bestemmer alt!');
        $board->setDescription('<table border="1" cellpadding="1" cellspacing="1" style="width:500px">
	<tbody>
		<tr>
			<td>Dette er en tabell</td>
			<td>Med mange Rader</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>1234</td>
			<td>151231&oslash;l23mklg</td>
		</tr>
	</tbody>
</table>

<h1>STooor overskrift</h1>

<h3>Liten overskrift</h3>

<ul>
	<li>En liste</li>
	<li>Med mange</li>
	<li>Items</li>
</ul>

<p><a href="https://google.com">https://google.com</a></p>');
        $manager->persist($board);
        $manager->flush();

        $this->addReference('board', $board);
    }

    public function getOrder(): int
    {
        return 3;
    }
}
