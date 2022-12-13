<?php

namespace App\Tests\Type;

use App\Entity\Department;
use App\Form\Type\CreateDepartmentType;
use Symfony\Component\Form\Test\TypeTestCase;

class CreateDepartmentTest extends TypeTestCase
{
    /**
     * @dataProvider getValidTestData
     */
    public function testForm($data)
    {
        $type = CreateDepartmentType::class;
        $form = $this->factory->create($type);

        $object = new Department();

        $object->fromArray($data);

        // submit the data to the form directly
        $form->submit($data);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($data) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    public function getValidTestData()
    {
        return [
            [
                'data' => [
                    'name' => 'Universitetet i Østfold',
                    'shortName' => 'UiØ',
                    'email' => 'uiø@mail.com',
                    'address' => 'Ormvegen 12',
                    'active' => false,
                ],
            ],
            [
                'data' => ['active' => true],
            ],
            [
                'data' => [
                    'name' => null,
                    'shortName' => null,
                    'email' => null,
                    'address' => null,
                    'active' => true,
                ],
            ],
        ];
    }
}
